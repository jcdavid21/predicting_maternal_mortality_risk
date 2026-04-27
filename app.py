import os
import uuid
import json
import math
import threading
import traceback
from datetime import datetime
from pathlib import Path

import numpy as np
import pandas as pd
import joblib
from flask import Flask, request, jsonify, render_template, g
from flask_cors import CORS

from sklearn.model_selection import train_test_split
from sklearn.linear_model import LogisticRegression
from sklearn.ensemble import RandomForestClassifier, StackingClassifier
from sklearn.metrics import (
    accuracy_score, precision_score, recall_score, f1_score
)
from xgboost import XGBClassifier

import db  # our thin DB wrapper — see db.py

# ── App Setup ──────────────────────────────────────────────────
app = Flask(__name__, template_folder='templates', static_folder='static')

CORS(app, resources={r"/*": {"origins": "http://localhost:3000"}},
     supports_credentials=True)

MODELS_DIR   = Path('models')
MODELS_DIR.mkdir(exist_ok=True)
BASE_MODEL   = MODELS_DIR / 'model.pkl'


FEATURE_COLS = ['Age', 'SystolicBP', 'DiastolicBP', 'Blood sugar', 'BodyTemp', 'HeartRate']
RISK_MAPPING = {0: 'low risk', 1: 'mid risk', 2: 'high risk'}
RISK_REVERSE = {v: k for k, v in RISK_MAPPING.items()}
MORTALITY_PROXY_MAPPING = {
    'low risk': 'low',
    'mid risk': 'moderate',
    'high risk': 'high',
}

_jobs: dict[str, dict] = {}
_jobs_lock = threading.Lock()


_HEAT_WEIGHTS = {'low risk': 0.4, 'mid risk': 0.7, 'high risk': 1.0}
 
# Safe whitelist for query-param values (avoids SQL injection via ENUM bypass).
_ALLOWED_RISK   = {'all', 'low risk', 'mid risk', 'high risk'}
_ALLOWED_PERIOD = {'all', 'week', 'month'}
_ALLOWED_SOURCE = {'patients', 'predictions'}
 

def _cursor(conn):
    """Return a fresh DictCursor from the given connection."""
    return conn.cursor()


def _parse_community_text(value):
    """Parse 'Barangay, Municipality' into (barangay, municipality)."""
    if not value:
        return None, None
    parts = [p.strip() for p in str(value).split(',') if p and str(p).strip()]
    if len(parts) >= 2:
        return parts[0], ', '.join(parts[1:])
    if len(parts) == 1:
        return parts[0], None
    return None, None


def _sync_patient_snapshot(cur, patient_id, payload):
    """Update patient profile with latest known location/vitals snapshot."""
    if not patient_id:
        return

    municipality = (payload.get('municipality') or '').strip() or None
    barangay = (payload.get('barangay') or '').strip() or None
    community = f'{barangay}, {municipality}' if barangay and municipality else None

    cur.execute("SHOW COLUMNS FROM patients")
    existing_cols = {r['Field'] for r in cur.fetchall()}

    candidate_values = {
        'age': payload.get('age'),
        'municipality': municipality,
        'barangay': barangay,
        'community': community,
        'distance_to_facility_km': payload.get('distance_to_facility_km'),
        'socioeconomic_index': payload.get('socioeconomic_index'),
        'low_resource_area': payload.get('low_resource_area'),
        'latest_risk_level': payload.get('latest_risk_level'),
        'latest_probability_score': payload.get('latest_probability_score'),
        'last_prediction_at': payload.get('last_prediction_at'),
        'prenatal_visits': payload.get('prenatal_visits'),
        'gravida': payload.get('gravida'),
        'para': payload.get('para'),
        'referral_delay_hours': payload.get('referral_delay_hours'),
        'has_prior_complication': payload.get('has_prior_complication'),
        'prior_complications': payload.get('prior_complications'),
        'has_comorbidity': payload.get('has_comorbidity'),
        'comorbidities': payload.get('comorbidities'),
    }

    set_clauses = []
    params = []
    for col, val in candidate_values.items():
        if col in existing_cols:
            set_clauses.append(f"{col} = COALESCE(%s, {col})")
            params.append(val)

    if not set_clauses:
        return

    sql = f"UPDATE patients SET {', '.join(set_clauses)} WHERE id = %s"
    params.append(patient_id)
    cur.execute(sql, params)


# ════════════════════════════════════════════════════════════════
#  PAGE
# ════════════════════════════════════════════════════════════════
@app.route('/')
def index():
    return render_template('prediction.html')


@app.route('/patients/search')
def search_patients():
    query = request.args.get('q', '').strip()
    if len(query) < 2:
        return jsonify({'patients': []})

    conn = db.get_conn()
    # FIX #1: ? → %s  |  FIX #2: use cursor
    with _cursor(conn) as cur:
        cur.execute(
            """SELECT id, patient_code, name, age
               FROM patients
               WHERE name LIKE %s OR patient_code LIKE %s
               LIMIT 20""",
            (f'%{query}%', f'%{query}%')
        )
        rows = cur.fetchall()

    # FIX #5: DictCursor already returns dicts — no need for dict(r)
    return jsonify({'patients': rows})


@app.route('/patients/list')
def list_patients():
    conn = db.get_conn()
    with _cursor(conn) as cur:
        cur.execute(
            """SELECT id, patient_code, name, age, community
               FROM patients
               ORDER BY name ASC"""
        )
        rows = cur.fetchall()

    # Keep API backwards-compatible while exposing parsed location fields.
    for r in rows:
        barangay, municipality = _parse_community_text(r.get('community'))
        r['barangay'] = barangay
        r['municipality'] = municipality

    return jsonify({'patients': rows})


@app.route('/patients/<int:patient_id>/info')
def patient_info(patient_id):
    """Return patient profile plus derived location/community context."""
    conn = db.get_conn()

    with _cursor(conn) as cur:
        cur.execute(
            """SELECT id, patient_code, name, age, community,
                      has_prior_complication, prior_complications,
                      has_comorbidity, comorbidities
               FROM patients
               WHERE id = %s
               LIMIT 1""",
            (patient_id,)
        )
        patient = cur.fetchone()

    if not patient:
        return jsonify({'error': 'Patient not found'}), 404

    barangay, municipality = _parse_community_text(patient.get('community'))

    if not (barangay and municipality):
        with _cursor(conn) as cur:
            cur.execute(
                """SELECT municipality, barangay
                   FROM predictions
                   WHERE patient_id = %s
                     AND municipality IS NOT NULL AND municipality <> ''
                     AND barangay IS NOT NULL AND barangay <> ''
                   ORDER BY created_at DESC
                   LIMIT 1""",
                (patient_id,)
            )
            latest_loc = cur.fetchone()
        if latest_loc:
            municipality = municipality or latest_loc.get('municipality')
            barangay = barangay or latest_loc.get('barangay')

    comm_row = None
    if barangay and municipality:
        with _cursor(conn) as cur:
            cur.execute(
                """SELECT municipality, barangay, latitude, longitude,
                          socioeconomic_index, low_resource_area, population_approx
                   FROM communities
                   WHERE municipality = %s AND barangay = %s
                   LIMIT 1""",
                (municipality, barangay)
            )
            comm_row = cur.fetchone()

    distance_to_facility_km = None
    if comm_row and comm_row.get('latitude') and comm_row.get('longitude'):
        c_lat = float(comm_row['latitude'])
        c_lon = float(comm_row['longitude'])
        with _cursor(conn) as cur:
            cur.execute(
                """SELECT latitude, longitude
                   FROM health_facilities
                   WHERE is_active = 1
                     AND latitude IS NOT NULL
                     AND longitude IS NOT NULL"""
            )
            fac_rows = cur.fetchall()
        if fac_rows:
            distances = [
                _haversine_km(c_lat, c_lon, float(f['latitude']), float(f['longitude']))
                for f in fac_rows
            ]
            if distances:
                distance_to_facility_km = round(min(distances), 2)

    patient_out = {
        'id': patient['id'],
        'patient_code': patient.get('patient_code'),
        'name': patient.get('name'),
        'age': patient.get('age'),
        'community': patient.get('community'),
        'has_prior_complication': patient.get('has_prior_complication'),
        'prior_complications': patient.get('prior_complications'),
        'has_comorbidity': patient.get('has_comorbidity'),
        'comorbidities': patient.get('comorbidities'),
        'municipality': municipality,
        'barangay': barangay,
        'distance_to_facility_km': distance_to_facility_km,
        'socioeconomic_index': comm_row.get('socioeconomic_index') if comm_row else None,
        'low_resource_area': comm_row.get('low_resource_area') if comm_row else None,
        'population_approx': comm_row.get('population_approx') if comm_row else None,
    }

    return jsonify({'patient': patient_out})


@app.route('/patients/<int:patient_id>/latest-vitals')
def latest_vitals(patient_id):
    conn = db.get_conn()
    # FIX #1 & #2
    with _cursor(conn) as cur:
        cur.execute(
            """SELECT age, systolic_bp, diastolic_bp,
                      blood_sugar, body_temp, heart_rate,
                      municipality, barangay,
                      distance_to_facility_km, socioeconomic_index, low_resource_area,
                      prenatal_visits, gravida, para, referral_delay_hours,
                      has_prior_complication, prior_complications,
                      has_comorbidity, comorbidities
               FROM health_records
               WHERE patient_id = %s
               ORDER BY recorded_at DESC
               LIMIT 1""",
            (patient_id,)
        )
        row = cur.fetchone()

    if row:
        return jsonify({'vitals': row})
    return jsonify({'vitals': None})


@app.route('/health-records')
def get_health_records():
    """Return all health records joined with patient info."""
    conn = db.get_conn()
    with _cursor(conn) as cur:
        cur.execute(
            """SELECT hr.id, hr.patient_id, p.name AS patient_name,
                      p.patient_code, hr.age, hr.systolic_bp,
                      hr.diastolic_bp, hr.blood_sugar, hr.body_temp,
                 hr.heart_rate,
                 hr.prenatal_visits, hr.gravida, hr.para,
                 hr.referral_delay_hours,
                 hr.has_prior_complication, hr.prior_complications,
                 hr.has_comorbidity, hr.comorbidities,
                 hr.municipality, hr.barangay,
                 hr.distance_to_facility_km, hr.socioeconomic_index,
                 hr.recorded_at
               FROM health_records hr
               LEFT JOIN patients p ON p.id = hr.patient_id
               ORDER BY hr.recorded_at DESC"""
        )
        records = cur.fetchall()
    return jsonify({'records': records})


@app.route('/save-prediction', methods=['POST'])
def save_prediction():
    """Link prediction to patient and persist the current vitals in health_records."""
    body          = request.get_json(force=True)
    prediction_id = body.get('prediction_id')
    patient_id    = body.get('patient_id')
    vitals        = body.get('vitals') or {}

    if not prediction_id:
        return jsonify({'error': 'prediction_id is required'}), 400
    if not patient_id:
        return jsonify({'error': 'Please select a patient before saving results.'}), 400

    required_vitals = ['age', 'systolic_bp', 'diastolic_bp', 'blood_sugar', 'body_temp', 'heart_rate']
    missing_vitals = [k for k in required_vitals if vitals.get(k) is None]
    if missing_vitals:
        return jsonify({'error': f'Missing vitals: {", ".join(missing_vitals)}'}), 400

    try:
        age = float(vitals['age'])
        systolic_bp = float(vitals['systolic_bp'])
        diastolic_bp = float(vitals['diastolic_bp'])
        blood_sugar = float(vitals['blood_sugar'])
        body_temp = float(vitals['body_temp'])
        heart_rate = float(vitals['heart_rate'])
    except (TypeError, ValueError):
        return jsonify({'error': 'Vitals must be valid numeric values.'}), 400

    conn = db.get_conn()
    with _cursor(conn) as cur:
        cur.execute(
            """SELECT id, risk_level, probability_score, created_at,
                      municipality, barangay
               FROM predictions WHERE id = %s""",
            (prediction_id,)
        )
        row = cur.fetchone()
        if not row:
            return jsonify({'error': 'Prediction not found'}), 404

        cur.execute(
            'UPDATE predictions SET patient_id = %s WHERE id = %s',
            (patient_id, prediction_id)
        )

        # Keep prediction location in sync when location was provided on save.
        pred_muni = row.get('municipality')
        pred_brgy = row.get('barangay')
        save_muni = (vitals.get('municipality') or '').strip() or None
        save_brgy = (vitals.get('barangay') or '').strip() or None
        if save_muni and save_brgy and (not pred_muni or not pred_brgy):
            cur.execute(
                """UPDATE predictions
                   SET municipality = %s,
                       barangay = %s,
                       community = %s
                   WHERE id = %s""",
                (save_muni, save_brgy, f'{save_brgy}, {save_muni}', prediction_id)
            )

        # Extended optional fields
        municipality            = (vitals.get('municipality') or '').strip() or None
        barangay                = (vitals.get('barangay') or '').strip() or None
        community_str           = f'{barangay}, {municipality}' if barangay and municipality else None
        distance_to_facility_km = vitals.get('distance_to_facility_km')
        socioeconomic_index     = vitals.get('socioeconomic_index')
        low_resource_area       = vitals.get('low_resource_area')
        prenatal_visits         = vitals.get('prenatal_visits')
        gravida                 = vitals.get('gravida')
        para                    = vitals.get('para')
        referral_delay_hours    = vitals.get('referral_delay_hours')
        has_prior_complication  = vitals.get('has_prior_complication')
        prior_complications     = (vitals.get('prior_complications') or 'none').strip()
        has_comorbidity         = vitals.get('has_comorbidity')
        comorbidities           = (vitals.get('comorbidities') or 'none').strip()

        cur.execute(
            """INSERT INTO health_records
               (patient_id, age, systolic_bp, diastolic_bp, blood_sugar, body_temp, heart_rate,
                community, municipality, barangay,
                distance_to_facility_km, socioeconomic_index, low_resource_area,
                prenatal_visits, gravida, para, referral_delay_hours,
                has_prior_complication, prior_complications,
                has_comorbidity, comorbidities)
               VALUES (%s, %s, %s, %s, %s, %s, %s,
                       %s, %s, %s,
                       %s, %s, %s,
                       %s, %s, %s, %s,
                       %s, %s,
                       %s, %s)""",
            (patient_id, age, systolic_bp, diastolic_bp, blood_sugar, body_temp, heart_rate,
             community_str, municipality, barangay,
             distance_to_facility_km, socioeconomic_index, low_resource_area,
             prenatal_visits, gravida, para, referral_delay_hours,
             has_prior_complication, prior_complications,
             has_comorbidity, comorbidities)
        )

        # Persist latest patient snapshot (patient table becomes canonical for profile state).
        patient_snapshot = {
            'age': age,
            'municipality': municipality,
            'barangay': barangay,
            'distance_to_facility_km': distance_to_facility_km,
            'socioeconomic_index': socioeconomic_index,
            'low_resource_area': low_resource_area,
            'latest_risk_level': row.get('risk_level'),
            'latest_probability_score': row.get('probability_score'),
            'last_prediction_at': row.get('created_at'),
            'prenatal_visits': prenatal_visits,
            'gravida': gravida,
            'para': para,
            'referral_delay_hours': referral_delay_hours,
            'has_prior_complication': has_prior_complication,
            'prior_complications': prior_complications,
            'has_comorbidity': has_comorbidity,
            'comorbidities': comorbidities,
        }
        _sync_patient_snapshot(cur, patient_id, patient_snapshot)

        conn.commit()

    return jsonify({'saved': True, 'prediction_id': prediction_id, 'health_record_saved': True})


# ════════════════════════════════════════════════════════════════
#  PREDICT
# ════════════════════════════════════════════════════════════════
@app.route('/predict', methods=['POST'])
def predict():
    body = request.get_json(force=True)

    # ── Validate input ────────────────────────────────────────
    required = ['age', 'systolic_bp', 'diastolic_bp', 'blood_sugar', 'body_temp', 'heart_rate']
    missing  = [f for f in required if f not in body or body[f] is None]
    if missing:
        return jsonify({'error': f'Missing fields: {", ".join(missing)}'}), 400

    try:
        features = [
            float(body['age']),
            float(body['systolic_bp']),
            float(body['diastolic_bp']),
            float(body['blood_sugar']),
            float(body['body_temp']),
            float(body['heart_rate']),
        ]
    except (ValueError, TypeError):
        return jsonify({'error': 'All fields must be numeric values'}), 400

    # ── Load model ────────────────────────────────────────────
    model_row, model_obj = _load_active_model()
    if model_obj is None:
        return jsonify({'error': 'No trained model found. Please train a model first.'}), 503

    # ── Run prediction ────────────────────────────────────────
    # Use the model's own trained feature names when available so
    # older saved models and newer retrained models both work.
    model_cols = list(getattr(model_obj, 'feature_names_in_', FEATURE_COLS))
    X      = pd.DataFrame([features], columns=model_cols)
    pred   = int(model_obj.predict(X)[0])
    probs  = model_obj.predict_proba(X)[0].tolist()

    risk_level = RISK_MAPPING[pred]
    prob_score = probs[pred]
    all_probs  = {RISK_MAPPING[i]: round(p, 4) for i, p in enumerate(probs)}

    # ── Persist to DB ─────────────────────────────────────────
    conn             = db.get_conn()
    patient_id       = body.get('patient_id')
    municipality     = (body.get('municipality') or '').strip() or None
    barangay         = (body.get('barangay') or '').strip() or None
    community        = f'{barangay}, {municipality}' if barangay and municipality else None
    model_version_id = model_row['id'] if model_row and model_row.get('id') else None
    created_at       = datetime.utcnow().strftime('%Y-%m-%d %H:%M:%S')

    # FIX #1 & #2
    with _cursor(conn) as cur:
        cur.execute(
            """INSERT INTO predictions
                    (patient_id, municipality, barangay, community,
                     risk_level, probability_score, all_probabilities,
                     model_version_id, created_at)
                    VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s)""",
                (patient_id, municipality, barangay, community,
                 risk_level, round(prob_score, 4), json.dumps(all_probs),
                 model_version_id, created_at)
        )
        prediction_id = cur.lastrowid

        # ── Auto-alert for high risk ──────────────────────────
        if risk_level == 'high risk' and patient_id:
            cur.execute(
                """INSERT INTO alerts
                   (patient_id, prediction_id, alert_type, message, is_resolved, created_at)
                   VALUES (%s, %s, %s, %s, %s, %s)""",
                (patient_id, prediction_id, 'HIGH_RISK',
                 f'High risk detected for patient {patient_id} — immediate evaluation required.',
                 0, created_at)
            )

        if patient_id:
            _sync_patient_snapshot(cur, patient_id, {
                'age': body.get('age'),
                'municipality': municipality,
                'barangay': barangay,
                'distance_to_facility_km': body.get('distance_to_facility_km'),
                'socioeconomic_index': body.get('socioeconomic_index'),
                'low_resource_area': body.get('low_resource_area'),
                'latest_risk_level': risk_level,
                'latest_probability_score': round(prob_score, 4),
                'last_prediction_at': created_at,
                'prenatal_visits': body.get('prenatal_visits'),
                'gravida': body.get('gravida'),
                'para': body.get('para'),
                'referral_delay_hours': body.get('referral_delay_hours'),
                'has_prior_complication': body.get('has_prior_complication'),
                'prior_complications': body.get('prior_complications'),
                'has_comorbidity': body.get('has_comorbidity'),
                'comorbidities': body.get('comorbidities'),
            })

    conn.commit()

    return jsonify({
        'risk_level':        risk_level,
        'probability_score': round(prob_score, 4),
        'all_probabilities': all_probs,
        'mortality_risk_label': MORTALITY_PROXY_MAPPING.get(risk_level, 'unknown'),
        'prediction_id':     prediction_id,
        'model_version':     model_row['version_name'] if model_row else 'model.pkl',
        'created_at':        created_at,
    })


# ════════════════════════════════════════════════════════════════
#  RETRAIN
# ════════════════════════════════════════════════════════════════
@app.route('/retrain', methods=['POST'])
def retrain():
    if 'file' not in request.files:
        return jsonify({'error': 'No file uploaded'}), 400

    file = request.files['file']
    if not file.filename.endswith('.csv'):
        return jsonify({'error': 'Only CSV files are accepted'}), 400

    try:
        df = pd.read_csv(file)
    except Exception as e:
        return jsonify({'error': f'Could not read CSV: {e}'}), 400

    # FIX #4: required_cols now uses the corrected FEATURE_COLS (BloodSugar)
    required_cols = FEATURE_COLS + ['RiskLevel']
    missing_cols  = [c for c in required_cols if c not in df.columns]
    if missing_cols:
        return jsonify({'error': f'Missing columns: {", ".join(missing_cols)}'}), 400

    valid_levels = set(RISK_REVERSE.keys())
    invalid      = set(df['RiskLevel'].unique()) - valid_levels
    if invalid:
        return jsonify({
            'error': (
                f'Unknown RiskLevel values: {", ".join(map(str, invalid))}. '
                'Use: low risk, mid risk, high risk'
            )
        }), 400

    if len(df) < 50:
        return jsonify({'error': 'Dataset too small — need at least 50 rows'}), 400

    job_id = str(uuid.uuid4())
    with _jobs_lock:
        _jobs[job_id] = {
            'status':       'running',
            'percent':      0,
            'message':      'Starting…',
            'current_step': 'preprocessing',
        }

    thread = threading.Thread(target=_retrain_worker, args=(job_id, df), daemon=True)
    thread.start()

    return jsonify({'job_id': job_id})


def _retrain_worker(job_id: str, df: pd.DataFrame):
    """Background retraining — updates _jobs[job_id] as it progresses."""
    def update(step, pct, msg):
        with _jobs_lock:
            _jobs[job_id].update({'current_step': step, 'percent': pct, 'message': msg})

    try:
        # ── Step 1: Preprocess ────────────────────────────────
        update('preprocessing', 5, 'Preprocessing data…')

        df = df[FEATURE_COLS + ['RiskLevel']].copy()
        df.dropna(inplace=True)
        df[FEATURE_COLS] = df[FEATURE_COLS].apply(pd.to_numeric, errors='coerce')
        df.dropna(inplace=True)

        X = df[FEATURE_COLS]
        y = df['RiskLevel'].map(RISK_REVERSE)

        X_train, X_test, y_train, y_test = train_test_split(
            X, y, test_size=0.2, random_state=42, stratify=y
        )
        update('preprocessing', 15, 'Data split complete')

        # ── Step 2: Train ─────────────────────────────────────
        update('training', 20, 'Training Logistic Regression…')

        base_estimators = [
            ('lr',  LogisticRegression(max_iter=1000, random_state=42)),
            ('rf',  RandomForestClassifier(n_estimators=200, random_state=42, n_jobs=-1)),
            ('xgb', XGBClassifier(
                n_estimators=200, learning_rate=0.1, max_depth=5,
                eval_metric='mlogloss',
                random_state=42, verbosity=0
            )),
        ]
        meta_learner = LogisticRegression(max_iter=1000, random_state=42)

        update('training', 35, 'Training Random Forest…')

        model = StackingClassifier(
            estimators=base_estimators,
            final_estimator=meta_learner,
            cv=5,
            passthrough=False,
            n_jobs=-1,
        )

        update('training', 45, 'Training XGBoost + Stacking Ensemble…')
        model.fit(X_train, y_train)
        update('training', 55, 'Training complete')

        # ── Step 3: Evaluate ──────────────────────────────────
        update('evaluating', 65, 'Evaluating performance…')

        y_pred = model.predict(X_test)
        metrics = {
            'accuracy':  round(accuracy_score(y_test, y_pred), 4),
            'precision': round(precision_score(y_test, y_pred, average='weighted', zero_division=0), 4),
            'recall':    round(recall_score(y_test, y_pred, average='weighted', zero_division=0), 4),
            'f1':        round(f1_score(y_test, y_pred, average='weighted', zero_division=0), 4),
        }
        update('evaluating', 80, f"Accuracy: {metrics['accuracy'] * 100:.1f}%")

        # ── Step 4: Save ──────────────────────────────────────
        update('saving', 85, 'Saving model…')

        conn = db.get_conn()
        # FIX #1, #2 & #3: use cursor + %s + access dict key for COUNT
        with _cursor(conn) as cur:
            cur.execute('SELECT COUNT(*) AS cnt FROM model_versions')
            row = cur.fetchone()
            version_num  = row['cnt'] + 1           # FIX #3: was fetchone()[0]
            version_name = f'model_v{version_num}.pkl'
            model_path   = MODELS_DIR / version_name
            joblib.dump(model, model_path)

            created_at = datetime.utcnow().strftime('%Y-%m-%d %H:%M:%S')
            cur.execute(
                """INSERT INTO model_versions
                   (version_name, file_path, accuracy, precision_score,
                    recall_score, f1_score, is_active, created_at)
                   VALUES (%s, %s, %s, %s, %s, %s, %s, %s)""",
                (version_name, str(model_path),
                 metrics['accuracy'], metrics['precision'],
                 metrics['recall'],   metrics['f1'],
                 0, created_at)
            )
            new_id = cur.lastrowid

        conn.commit()
        update('saving', 95, 'Model saved to database')

        with _jobs_lock:
            _jobs[job_id].update({
                'status':       'done',
                'percent':      100,
                'message':      'Complete!',
                'current_step': 'done',
                'metrics':      metrics,
                'version_name': version_name,
                'model_id':     new_id,
            })

    except Exception:
        err = traceback.format_exc()
        with _jobs_lock:
            _jobs[job_id].update({
                'status':  'error',
                'message': f'Training failed: {err[:300]}',
                'percent': 0,
            })

@app.route('/progress/<job_id>')
def progress(job_id):
    with _jobs_lock:
        job = _jobs.get(job_id)

    if job is None:
        return jsonify({'error': 'Job not found'}), 404

    return jsonify(job)


@app.route('/models')
def list_models():
    conn = db.get_conn()
    with _cursor(conn) as cur:
        cur.execute(
            """SELECT id, version_name, accuracy, precision_score,
                      recall_score, f1_score, is_active, created_at
               FROM model_versions
               ORDER BY created_at DESC"""
        )
        versions = cur.fetchall()

    for v in versions:
        v['is_active'] = bool(v['is_active'])

    base_entry = {
        'id': 0, 'version_name': 'model.pkl',
        'accuracy': None, 'precision_score': None,
        'recall_score': None, 'f1_score': None,
        'is_active': True, 'created_at': None,
    }
    active = next((v for v in versions if v['is_active']), None)

    if active is None and BASE_MODEL.exists():
        active = base_entry

    if not versions and BASE_MODEL.exists():
        versions = [base_entry]

    return jsonify({
        'versions': versions,
        'active':   active,
    })


@app.route('/set-active-model', methods=['POST'])
def set_active_model():
    body     = request.get_json(force=True)
    model_id = body.get('model_id')

    if not model_id:
        return jsonify({'error': 'model_id is required'}), 400

    conn = db.get_conn()
    # FIX #1 & #2
    with _cursor(conn) as cur:
        cur.execute('UPDATE model_versions SET is_active = 0')
        cur.execute('UPDATE model_versions SET is_active = 1 WHERE id = %s', (model_id,))
        cur.execute('SELECT version_name FROM model_versions WHERE id = %s', (model_id,))
        row = cur.fetchone()

    if not row:
        return jsonify({'error': 'Model version not found'}), 404

    conn.commit()
    return jsonify({'version_name': row['version_name']})


@app.route('/dashboard/stats')
def dashboard_stats():
    conn = db.get_conn()
    with _cursor(conn) as cur:

        cur.execute('SELECT COUNT(*) AS cnt FROM patients')
        total_patients = cur.fetchone()['cnt']

        cur.execute(
            """
            SELECT
                COUNT(*)                       AS total,
                SUM(risk_level = 'low risk')  AS low_risk,
                SUM(risk_level = 'mid risk')  AS mid_risk,
                SUM(risk_level = 'high risk') AS high_risk
            FROM predictions
            """
        )
        row = cur.fetchone()
        total_pred = row['total'] or 0
        low_risk = row['low_risk'] or 0
        mid_risk = row['mid_risk'] or 0
        high_risk = row['high_risk'] or 0

        cur.execute(
            """
            SELECT
                SUM(risk_level = 'low risk')  AS low,
                SUM(risk_level = 'mid risk')  AS mid,
                SUM(risk_level = 'high risk') AS high
            FROM predictions
            WHERE created_at >= DATE(DATE_SUB(NOW(), INTERVAL WEEKDAY(NOW()) DAY))
            """
        )
        wk = cur.fetchone()

        cur.execute(
            """
            SELECT
                SUM(risk_level = 'low risk')  AS low,
                SUM(risk_level = 'mid risk')  AS mid,
                SUM(risk_level = 'high risk') AS high
            FROM predictions
            WHERE YEAR(created_at) = YEAR(NOW())
              AND MONTH(created_at) = MONTH(NOW())
            """
        )
        mo = cur.fetchone()

        cur.execute('SELECT COUNT(*) AS cnt FROM alerts WHERE is_resolved = 0')
        active_alerts = cur.fetchone()['cnt']

        cur.execute('SELECT COUNT(*) AS cnt FROM alerts WHERE is_resolved = 1')
        resolved_alerts = cur.fetchone()['cnt']

    return jsonify({
        'total_patients': total_patients,
        'total_predictions': total_pred,
        'low_risk_count': int(low_risk),
        'mid_risk_count': int(mid_risk),
        'high_risk_count': int(high_risk),
        'active_alerts': active_alerts,
        'resolved_alerts': resolved_alerts,
        'week_low': int(wk['low'] or 0),
        'week_mid': int(wk['mid'] or 0),
        'week_high': int(wk['high'] or 0),
        'month_low': int(mo['low'] or 0),
        'month_mid': int(mo['mid'] or 0),
        'month_high': int(mo['high'] or 0),
    })


@app.route('/dashboard/health-records-distribution')
def dashboard_hr_distribution():
    conn = db.get_conn()
    with _cursor(conn) as cur:
        cur.execute(
            """
            SELECT
                SUM(latest_pred.risk_level = 'low risk')  AS low,
                SUM(latest_pred.risk_level = 'mid risk')  AS mid,
                SUM(latest_pred.risk_level = 'high risk') AS high
            FROM (
                SELECT DISTINCT hr.patient_id
                FROM health_records hr
                WHERE hr.patient_id IS NOT NULL
            ) AS hr_patients
            JOIN predictions latest_pred ON latest_pred.id = (
                SELECT id
                FROM predictions
                WHERE patient_id = hr_patients.patient_id
                ORDER BY created_at DESC
                LIMIT 1
            )
            """
        )
        all_row = cur.fetchone()

        cur.execute(
            """
            SELECT
                SUM(latest_pred.risk_level = 'low risk')  AS low,
                SUM(latest_pred.risk_level = 'mid risk')  AS mid,
                SUM(latest_pred.risk_level = 'high risk') AS high
            FROM (
                SELECT DISTINCT hr.patient_id
                FROM health_records hr
                WHERE hr.patient_id IS NOT NULL
                  AND hr.recorded_at >= DATE(DATE_SUB(NOW(), INTERVAL WEEKDAY(NOW()) DAY))
            ) AS hr_patients
            JOIN predictions latest_pred ON latest_pred.id = (
                SELECT id
                FROM predictions
                WHERE patient_id = hr_patients.patient_id
                ORDER BY created_at DESC
                LIMIT 1
            )
            """
        )
        week_row = cur.fetchone()

        cur.execute(
            """
            SELECT
                SUM(latest_pred.risk_level = 'low risk')  AS low,
                SUM(latest_pred.risk_level = 'mid risk')  AS mid,
                SUM(latest_pred.risk_level = 'high risk') AS high
            FROM (
                SELECT DISTINCT hr.patient_id
                FROM health_records hr
                WHERE hr.patient_id IS NOT NULL
                  AND YEAR(hr.recorded_at) = YEAR(NOW())
                  AND MONTH(hr.recorded_at) = MONTH(NOW())
            ) AS hr_patients
            JOIN predictions latest_pred ON latest_pred.id = (
                SELECT id
                FROM predictions
                WHERE patient_id = hr_patients.patient_id
                ORDER BY created_at DESC
                LIMIT 1
            )
            """
        )
        month_row = cur.fetchone()

    def to_ints(row):
        row = row or {}
        return {
            'low': int(row.get('low') or 0),
            'mid': int(row.get('mid') or 0),
            'high': int(row.get('high') or 0),
        }

    return jsonify({
        'all': to_ints(all_row),
        'week': to_ints(week_row),
        'month': to_ints(month_row),
    })


@app.route('/dashboard/recent-predictions')
def dashboard_recent_predictions():
    conn = db.get_conn()
    with _cursor(conn) as cur:
        cur.execute(
            """
            SELECT
                pred.id,
                COALESCE(pat.name, 'Anonymous') AS patient_name,
                pred.risk_level,
                pred.probability_score,
                pred.patient_id,
                pred.created_at
                ,CASE
                    WHEN pred.patient_id IS NOT NULL
                     AND EXISTS (
                         SELECT 1
                         FROM health_records hr
                         WHERE hr.patient_id = pred.patient_id
                     )
                    THEN 1 ELSE 0
                END AS is_saved
            FROM predictions pred
            LEFT JOIN patients pat ON pat.id = pred.patient_id
            ORDER BY pred.created_at DESC
            LIMIT 10
            """
        )
        rows = cur.fetchall()

    for r in rows:
        if r.get('created_at'):
            r['created_at'] = r['created_at'].strftime('%Y-%m-%dT%H:%M:%S')
        r['is_saved'] = bool(r['is_saved'])

    return jsonify({'predictions': rows})


@app.route('/dashboard/patient-health-record/<int:patient_id>')
def dashboard_patient_health_record(patient_id):
    conn = db.get_conn()
    with _cursor(conn) as cur:
        cur.execute(
            """
            SELECT
                id,
                patient_id,
                age,
                systolic_bp,
                diastolic_bp,
                blood_sugar,
                body_temp,
                heart_rate,
                recorded_by,
                recorded_at
            FROM health_records
            WHERE patient_id = %s
            ORDER BY recorded_at DESC
            LIMIT 1
            """,
            (patient_id,)
        )
        row = cur.fetchone()

    if row and row.get('recorded_at'):
        row['recorded_at'] = row['recorded_at'].strftime('%Y-%m-%dT%H:%M:%S')

    return jsonify({'record': row})


@app.route('/dashboard/high-risk')
def dashboard_high_risk():
    conn = db.get_conn()
    with _cursor(conn) as cur:
        cur.execute(
            """
            SELECT
                pat.id,
                pat.patient_code,
                pat.name,
                pat.age,
                latest.risk_level,
                latest.probability_score,
                latest.created_at AS last_prediction_at
            FROM patients pat
            JOIN predictions latest ON latest.id = (
                SELECT id
                FROM predictions
                WHERE patient_id = pat.id
                  AND risk_level = 'high risk'
                ORDER BY created_at DESC
                LIMIT 1
            )
            ORDER BY latest.created_at DESC
            """
        )
        rows = cur.fetchall()

    for r in rows:
        if r.get('last_prediction_at'):
            r['last_prediction_at'] = r['last_prediction_at'].strftime('%Y-%m-%dT%H:%M:%S')

    return jsonify({'patients': rows})


@app.route('/dashboard/alerts')
def dashboard_alerts():
    conn = db.get_conn()
    with _cursor(conn) as cur:
        cur.execute(
            """
            SELECT
                a.id,
                a.alert_type,
                a.message,
                a.is_resolved,
                a.created_at,
                COALESCE(pat.name, 'Unknown Patient') AS patient_name,
                a.patient_id
            FROM alerts a
            LEFT JOIN patients pat ON pat.id = a.patient_id
            ORDER BY a.is_resolved ASC, a.created_at DESC
            LIMIT 20
            """
        )
        rows = cur.fetchall()

    for r in rows:
        r['is_resolved'] = bool(r['is_resolved'])
        if r.get('created_at'):
            r['created_at'] = r['created_at'].strftime('%Y-%m-%dT%H:%M:%S')

    return jsonify({'alerts': rows})


@app.route('/alerts/<int:alert_id>/resolve', methods=['POST'])
def resolve_alert(alert_id):
    conn = db.get_conn()
    with _cursor(conn) as cur:
        cur.execute(
            """
            UPDATE alerts
            SET is_resolved = 1
            WHERE id = %s
            """,
            (alert_id,)
        )
        updated = cur.rowcount

    conn.commit()

    if not updated:
        return jsonify({'error': 'Alert not found'}), 404

    return jsonify({'success': True, 'alert_id': alert_id, 'is_resolved': True})


@app.route('/dashboard/weekly')
def dashboard_weekly():
    conn = db.get_conn()
    with _cursor(conn) as cur:
        cur.execute(
            """
            SELECT
                DATE(created_at)              AS day,
                SUM(risk_level = 'low risk')  AS low,
                SUM(risk_level = 'mid risk')  AS mid,
                SUM(risk_level = 'high risk') AS high
            FROM predictions
            WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
            GROUP BY day
            ORDER BY day ASC
            """
        )
        rows = cur.fetchall()

    from datetime import date, timedelta

    day_map = {r['day']: r for r in rows}
    result = []
    for i in range(6, -1, -1):
        d = date.today() - timedelta(days=i)
        row = day_map.get(d, {})
        result.append({
            'day': d.isoformat(),
            'day_label': d.strftime('%a'),
            'low': int(row.get('low', 0) or 0),
            'mid': int(row.get('mid', 0) or 0),
            'high': int(row.get('high', 0) or 0),
        })

    return jsonify({'days': result})


def _haversine_km(lat1, lon1, lat2, lon2):
    R = 6371.0
    phi1, phi2 = math.radians(lat1), math.radians(lat2)
    dphi = math.radians(lat2 - lat1)
    dlam = math.radians(lon2 - lon1)
    a = math.sin(dphi / 2) ** 2 + math.cos(phi1) * math.cos(phi2) * math.sin(dlam / 2) ** 2
    return R * 2 * math.atan2(math.sqrt(a), math.sqrt(1 - a))


@app.route('/community/municipalities')
def community_municipalities():
    region = request.args.get('region', '').strip() or None
    conn   = db.get_conn()
    with _cursor(conn) as cur:
        if region:
            cur.execute("SELECT DISTINCT municipality FROM communities WHERE region=%s ORDER BY municipality", (region,))
        else:
            cur.execute("SELECT DISTINCT municipality FROM communities ORDER BY municipality")
        rows = cur.fetchall()
    return jsonify({'municipalities': [r['municipality'] for r in rows]})


@app.route('/community/barangays')
def community_barangays():
    municipality = request.args.get('municipality', '').strip()
    if not municipality:
        return jsonify({'barangays': []})
    conn = db.get_conn()
    with _cursor(conn) as cur:
        cur.execute(
            "SELECT id,barangay,community,latitude,longitude,socioeconomic_index,low_resource_area,population_approx FROM communities WHERE municipality=%s ORDER BY barangay",
            (municipality,)
        )
        rows = cur.fetchall()
    return jsonify({'barangays': rows})


@app.route('/community/locations')
def community_locations():
    region = request.args.get('region', '').strip() or None
    conn   = db.get_conn()
    with _cursor(conn) as cur:
        if region:
            cur.execute("SELECT id,region,municipality,barangay,community,latitude,longitude,socioeconomic_index,low_resource_area FROM communities WHERE region=%s ORDER BY municipality,barangay", (region,))
        else:
            cur.execute("SELECT id,region,municipality,barangay,community,latitude,longitude,socioeconomic_index,low_resource_area FROM communities ORDER BY municipality,barangay")
        rows = cur.fetchall()
    return jsonify({'locations': rows})


@app.route('/community/search')
def community_search():
    q    = request.args.get('q', '').strip()
    kind = request.args.get('type', 'all')
    try:
        user_lat  = float(request.args.get('lat', 0) or 0)
        user_lon  = float(request.args.get('lon', 0) or 0)
        has_coords = user_lat != 0 and user_lon != 0
    except (ValueError, TypeError):
        user_lat = user_lon = 0
        has_coords = False

    if len(q) < 2:
        return jsonify({'communities': [], 'facilities': [], 'query': q})

    like = f'%{q}%'
    conn = db.get_conn()
    communities_out = []
    facilities_out  = []

    if kind in ('community', 'all'):
        with _cursor(conn) as cur:
            cur.execute(
                "SELECT id,region,municipality,barangay,community,latitude,longitude,socioeconomic_index,low_resource_area,population_approx FROM communities WHERE municipality LIKE %s OR barangay LIKE %s OR community LIKE %s ORDER BY barangay LIMIT 20",
                (like, like, like)
            )
            rows = cur.fetchall()
        for r in rows:
            r['distance_km'] = round(_haversine_km(user_lat, user_lon, float(r['latitude']), float(r['longitude'])), 2) if has_coords and r['latitude'] and r['longitude'] else None
        communities_out = rows

    if kind in ('facility', 'all'):
        with _cursor(conn) as cur:
            cur.execute(
                "SELECT id,name,facility_type,municipality,barangay,community,address,latitude,longitude,contact_number,operating_hours,has_ob_service,has_prenatal,has_delivery FROM health_facilities WHERE is_active=1 AND (name LIKE %s OR municipality LIKE %s OR barangay LIKE %s OR community LIKE %s) ORDER BY name LIMIT 20",
                (like, like, like, like)
            )
            rows = cur.fetchall()
        for r in rows:
            r['distance_km']  = round(_haversine_km(user_lat, user_lon, float(r['latitude']), float(r['longitude'])), 2) if has_coords and r['latitude'] and r['longitude'] else None
            r['has_ob_service'] = bool(r['has_ob_service'])
            r['has_prenatal']   = bool(r['has_prenatal'])
            r['has_delivery']   = bool(r['has_delivery'])
        facilities_out = rows

    return jsonify({'communities': communities_out, 'facilities': facilities_out, 'query': q})


@app.route('/community/heatmap-data')
def community_heatmap_data():
    """Aggregate patient-level risk distribution per municipality/barangay."""
    threshold = float(request.args.get('threshold', 70) or 70)
    min_total = int(request.args.get('min_total', 1) or 1)
    min_total = max(1, min_total)

    conn = db.get_conn()
    with _cursor(conn) as cur:
        cur.execute(
            """
            SELECT
                loc.municipality,
                loc.barangay,
                COUNT(*) AS total,
                SUM(loc.risk_level='low risk')  AS low_count,
                SUM(loc.risk_level='mid risk')  AS mid_count,
                SUM(loc.risk_level='high risk') AS high_count,
                ROUND(100.0 * SUM(loc.risk_level='low risk')  / NULLIF(COUNT(*), 0), 1) AS low_pct,
                ROUND(100.0 * SUM(loc.risk_level='mid risk')  / NULLIF(COUNT(*), 0), 1) AS mid_pct,
                ROUND(100.0 * SUM(loc.risk_level='high risk') / NULLIF(COUNT(*), 0), 1) AS high_pct,
                MAX(loc.created_at) AS last_prediction_at
            FROM (
                SELECT
                    pr.patient_id,
                    pr.risk_level,
                    pr.created_at,
                    COALESCE(NULLIF(pr.municipality, ''), NULLIF(pa.municipality, '')) AS municipality,
                    COALESCE(NULLIF(pr.barangay, ''), NULLIF(pa.barangay, '')) AS barangay
                FROM predictions pr
                JOIN (
                    SELECT patient_id, MAX(id) AS latest_id
                    FROM predictions
                    WHERE patient_id IS NOT NULL
                    GROUP BY patient_id
                ) latest ON latest.latest_id = pr.id
                LEFT JOIN patients pa ON pa.id = pr.patient_id
            ) loc
            WHERE loc.municipality IS NOT NULL AND loc.municipality <> ''
              AND loc.barangay IS NOT NULL AND loc.barangay <> ''
            GROUP BY loc.municipality, loc.barangay
            HAVING COUNT(*) >= %s
            ORDER BY high_pct DESC, total DESC, loc.municipality ASC, loc.barangay ASC
            LIMIT 200
            """,
            (min_total,)
        )
        rows = cur.fetchall()

    for r in rows:
        r['total'] = int(r.get('total') or 0)
        r['low_count'] = int(r.get('low_count') or 0)
        r['mid_count'] = int(r.get('mid_count') or 0)
        r['high_count'] = int(r.get('high_count') or 0)
        r['low_pct'] = float(r.get('low_pct') or 0)
        r['mid_pct'] = float(r.get('mid_pct') or 0)
        r['high_pct'] = float(r.get('high_pct') or 0)
        r['alert_triggered'] = r['high_pct'] >= threshold
        if r.get('last_prediction_at'):
            r['last_prediction_at'] = r['last_prediction_at'].strftime('%Y-%m-%dT%H:%M:%S')

    return jsonify({
        'threshold_pct': threshold,
        'heatmap': rows,
    })


@app.route('/community/top-locations')
def community_top_locations():
    """Top barangays ranked from unique-patient latest predictions only."""
    n = int(request.args.get('n', 10) or 10)
    n = max(1, min(n, 50))

    conn = db.get_conn()
    with _cursor(conn) as cur:
        cur.execute(
            """
            SELECT
                loc.municipality,
                loc.barangay,
                COUNT(*) AS total_predictions,
                ROUND(100.0 * SUM(loc.risk_level='high risk') / NULLIF(COUNT(*), 0), 1) AS high_risk_pct,
                ROUND(AVG(
                    CASE loc.risk_level
                        WHEN 'low risk'  THEN 0
                        WHEN 'mid risk'  THEN 1.5
                        WHEN 'high risk' THEN 3
                        ELSE NULL
                    END
                ), 2) AS mortality_score_avg,
                MAX(loc.created_at) AS last_prediction_at
            FROM (
                SELECT
                    pr.patient_id,
                    pr.risk_level,
                    pr.created_at,
                    COALESCE(NULLIF(pr.municipality, ''), NULLIF(pa.municipality, '')) AS municipality,
                    COALESCE(NULLIF(pr.barangay, ''), NULLIF(pa.barangay, '')) AS barangay
                FROM predictions pr
                JOIN (
                    SELECT patient_id, MAX(id) AS latest_id
                    FROM predictions
                    WHERE patient_id IS NOT NULL
                    GROUP BY patient_id
                ) latest ON latest.latest_id = pr.id
                LEFT JOIN patients pa ON pa.id = pr.patient_id
            ) loc
            WHERE loc.municipality IS NOT NULL AND loc.municipality <> ''
              AND loc.barangay IS NOT NULL AND loc.barangay <> ''
            GROUP BY loc.municipality, loc.barangay
            ORDER BY mortality_score_avg DESC, high_risk_pct DESC, total_predictions DESC
            LIMIT %s
            """,
            (n,)
        )
        rows = cur.fetchall()

    for r in rows:
        r['total_predictions'] = int(r.get('total_predictions') or 0)
        r['high_risk_pct'] = float(r.get('high_risk_pct') or 0)
        if r.get('mortality_score_avg') is not None:
            r['mortality_score_avg'] = float(r['mortality_score_avg'])
        if r.get('last_prediction_at'):
            r['last_prediction_at'] = r['last_prediction_at'].strftime('%Y-%m-%dT%H:%M:%S')

    return jsonify({'locations': rows, 'score_max': 3})


@app.route('/community/trend')
def community_trend():
    """Weekly trend with unique patients per week (no duplicate counting)."""
    weeks = int(request.args.get('weeks', 12) or 12)
    weeks = max(1, min(weeks, 52))

    conn = db.get_conn()
    with _cursor(conn) as cur:
        cur.execute(
            """
            SELECT
                t.yw,
                MIN(t.week_start) AS week_start,
                SUM(t.risk_level='low risk')  AS low,
                SUM(t.risk_level='mid risk')  AS mid,
                SUM(t.risk_level='high risk') AS high,
                COUNT(*) AS total
            FROM (
                SELECT
                    YEARWEEK(pr.created_at, 1) AS yw,
                    DATE(pr.created_at) AS week_start,
                    pr.patient_id,
                    pr.risk_level
                FROM predictions pr
                JOIN (
                    SELECT patient_id, YEARWEEK(created_at, 1) AS yw, MAX(id) AS latest_id
                    FROM predictions
                    WHERE patient_id IS NOT NULL
                      AND created_at >= DATE_SUB(CURDATE(), INTERVAL %s WEEK)
                    GROUP BY patient_id, YEARWEEK(created_at, 1)
                ) latest ON latest.latest_id = pr.id
                WHERE pr.patient_id IS NOT NULL
            ) t
            GROUP BY t.yw
            ORDER BY t.yw ASC
            """,
            (weeks,)
        )
        rows = cur.fetchall()

    for r in rows:
        if r.get('week_start'):
            r['week_start'] = str(r['week_start'])
        r['low'] = int(r.get('low') or 0)
        r['mid'] = int(r.get('mid') or 0)
        r['high'] = int(r.get('high') or 0)
        r['total'] = int(r.get('total') or 0)

    return jsonify({'trend': rows})


@app.route('/community/alert-areas')
def community_alert_areas():
    """Return high-risk alert areas based on unique-patient latest predictions."""
    threshold = float(request.args.get('threshold', 70) or 70)
    min_total = int(request.args.get('min_total', 3) or 3)
    min_total = max(1, min_total)

    conn = db.get_conn()
    with _cursor(conn) as cur:
        cur.execute(
            """
            SELECT
                loc.municipality,
                loc.barangay,
                COUNT(*) AS total_predictions,
                ROUND(100.0 * SUM(loc.risk_level='high risk') / NULLIF(COUNT(*), 0), 1) AS high_risk_pct,
                MAX(loc.created_at) AS last_prediction_at
            FROM (
                SELECT
                    pr.patient_id,
                    pr.risk_level,
                    pr.created_at,
                    COALESCE(NULLIF(pr.municipality, ''), NULLIF(pa.municipality, '')) AS municipality,
                    COALESCE(NULLIF(pr.barangay, ''), NULLIF(pa.barangay, '')) AS barangay
                FROM predictions pr
                JOIN (
                    SELECT patient_id, MAX(id) AS latest_id
                    FROM predictions
                    WHERE patient_id IS NOT NULL
                    GROUP BY patient_id
                ) latest ON latest.latest_id = pr.id
                LEFT JOIN patients pa ON pa.id = pr.patient_id
            ) loc
            WHERE loc.municipality IS NOT NULL AND loc.municipality <> ''
              AND loc.barangay IS NOT NULL AND loc.barangay <> ''
            GROUP BY loc.municipality, loc.barangay
            HAVING COUNT(*) >= %s AND high_risk_pct >= %s
            ORDER BY high_risk_pct DESC, total_predictions DESC, loc.municipality ASC, loc.barangay ASC
            LIMIT 200
            """,
            (min_total, threshold)
        )
        rows = cur.fetchall()

    for r in rows:
        r['total_predictions'] = int(r.get('total_predictions') or 0)
        r['high_risk_pct'] = float(r.get('high_risk_pct') or 0)
        if r.get('last_prediction_at'):
            r['last_prediction_at'] = r['last_prediction_at'].strftime('%Y-%m-%dT%H:%M:%S')

    return jsonify({
        'threshold_pct': threshold,
        'alert_areas': rows,
    })


@app.route('/community/nearby-facilities')
def nearby_facilities():
    try:
        lat = float(request.args.get('lat', 0))
        lon = float(request.args.get('lon', 0))
    except (ValueError, TypeError):
        return jsonify({'error': 'lat and lon are required numeric values'}), 400

    if lat == 0 and lon == 0:
        return jsonify({'error': 'lat and lon are required'}), 400

    radius_km = float(request.args.get('radius_km', 10))
    ob_only   = request.args.get('ob_only', '0') == '1'
    conn      = db.get_conn()

    lat_d = radius_km / 111.0
    lon_d = radius_km / (111.0 * math.cos(math.radians(lat)))

    sql = "SELECT id,name,facility_type,municipality,barangay,community,address,latitude,longitude,contact_number,operating_hours,has_ob_service,has_prenatal,has_delivery FROM health_facilities WHERE is_active=1 AND latitude BETWEEN %s AND %s AND longitude BETWEEN %s AND %s"
    params = [lat - lat_d, lat + lat_d, lon - lon_d, lon + lon_d]
    if ob_only:
        sql += ' AND (has_ob_service=1 OR has_prenatal=1 OR has_delivery=1)'

    with _cursor(conn) as cur:
        cur.execute(sql, params)
        rows = cur.fetchall()

    results = []
    for r in rows:
        if not r['latitude'] or not r['longitude']:
            continue
        dist = _haversine_km(lat, lon, float(r['latitude']), float(r['longitude']))
        if dist <= radius_km:
            r['distance_km']    = round(dist, 2)
            r['has_ob_service'] = bool(r['has_ob_service'])
            r['has_prenatal']   = bool(r['has_prenatal'])
            r['has_delivery']   = bool(r['has_delivery'])
            results.append(r)

    results.sort(key=lambda x: x['distance_km'])
    return jsonify({'facilities': results[:20], 'user_lat': lat, 'user_lon': lon})


@app.route('/community/info')
def community_info():
    municipality = request.args.get('municipality', '').strip()
    barangay     = request.args.get('barangay', '').strip()
    if not municipality or not barangay:
        return jsonify({'error': 'municipality and barangay are required'}), 400

    try:
        user_lat  = float(request.args.get('lat', 0) or 0)
        user_lon  = float(request.args.get('lon', 0) or 0)
        has_coords = user_lat != 0 and user_lon != 0
    except (ValueError, TypeError):
        user_lat = user_lon = 0
        has_coords = False

    conn = db.get_conn()

    with _cursor(conn) as cur:
        cur.execute("SELECT * FROM communities WHERE municipality=%s AND barangay=%s LIMIT 1", (municipality, barangay))
        comm = cur.fetchone()

    with _cursor(conn) as cur:
        cur.execute(
            """SELECT COUNT(*) AS total_predictions,
                      SUM(t.risk_level='low risk') AS low_count,
                      SUM(t.risk_level='mid risk') AS mid_count,
                      SUM(t.risk_level='high risk') AS high_count,
                      ROUND(100.0*SUM(t.risk_level='high risk')/NULLIF(COUNT(*),0),1) AS high_risk_pct,
                      ROUND(AVG(CASE t.risk_level WHEN 'low risk' THEN 0 WHEN 'mid risk' THEN 1.5 WHEN 'high risk' THEN 3 ELSE NULL END),2) AS mortality_score_avg,
                      MAX(t.created_at) AS last_prediction_at
               FROM (
                   SELECT pr.patient_id, pr.risk_level, pr.created_at,
                          COALESCE(NULLIF(pr.municipality, ''), NULLIF(pa.municipality, '')) AS municipality,
                          COALESCE(NULLIF(pr.barangay, ''), NULLIF(pa.barangay, '')) AS barangay
                   FROM predictions pr
                   JOIN (
                       SELECT patient_id, MAX(id) AS latest_id
                       FROM predictions
                       WHERE patient_id IS NOT NULL
                       GROUP BY patient_id
                   ) latest ON latest.latest_id = pr.id
                   LEFT JOIN patients pa ON pa.id = pr.patient_id
               ) t
               WHERE t.municipality=%s AND t.barangay=%s""",
            (municipality, barangay)
        )
        risk_summary = cur.fetchone()

    ref_lat = float(comm['latitude'])  if comm and comm.get('latitude')  else user_lat
    ref_lon = float(comm['longitude']) if comm and comm.get('longitude') else user_lon

    facilities = []
    if ref_lat and ref_lon:
        lat_d = 15 / 111.0
        lon_d = 15 / (111.0 * math.cos(math.radians(ref_lat)))
        with _cursor(conn) as cur:
            cur.execute(
                "SELECT id,name,facility_type,municipality,barangay,latitude,longitude,contact_number,operating_hours,has_ob_service,has_prenatal,has_delivery FROM health_facilities WHERE is_active=1 AND latitude BETWEEN %s AND %s AND longitude BETWEEN %s AND %s LIMIT 50",
                (ref_lat - lat_d, ref_lat + lat_d, ref_lon - lon_d, ref_lon + lon_d)
            )
            fac_rows = cur.fetchall()
        for f in fac_rows:
            if f['latitude'] and f['longitude']:
                dist = _haversine_km(ref_lat, ref_lon, float(f['latitude']), float(f['longitude']))
                f['distance_km']    = round(dist, 2)
                f['has_ob_service'] = bool(f['has_ob_service'])
                f['has_prenatal']   = bool(f['has_prenatal'])
                f['has_delivery']   = bool(f['has_delivery'])
                facilities.append(f)
        facilities.sort(key=lambda x: x['distance_km'])
        facilities = facilities[:5]

    with _cursor(conn) as cur:
        cur.execute(
            """SELECT YEARWEEK(created_at,1) AS yw, MIN(DATE(created_at)) AS week_start,
                      SUM(risk_level='low risk') AS low, SUM(risk_level='mid risk') AS mid,
                      SUM(risk_level='high risk') AS high, COUNT(*) AS total
               FROM (
                   SELECT
                       YEARWEEK(pr.created_at,1) AS yw,
                       DATE(pr.created_at) AS created_at,
                       pr.patient_id,
                       pr.risk_level,
                       COALESCE(NULLIF(pr.municipality, ''), NULLIF(pa.municipality, '')) AS municipality,
                       COALESCE(NULLIF(pr.barangay, ''), NULLIF(pa.barangay, '')) AS barangay
                   FROM predictions pr
                   JOIN (
                       SELECT patient_id, YEARWEEK(created_at,1) AS yw, MAX(id) AS latest_id
                       FROM predictions
                       WHERE patient_id IS NOT NULL
                         AND created_at >= DATE_SUB(CURDATE(), INTERVAL 8 WEEK)
                       GROUP BY patient_id, YEARWEEK(created_at,1)
                   ) latest ON latest.latest_id = pr.id
                   LEFT JOIN patients pa ON pa.id = pr.patient_id
               ) t
               WHERE t.municipality=%s AND t.barangay=%s
               GROUP BY yw ORDER BY yw ASC""",
            (municipality, barangay)
        )
        trend = cur.fetchall()

    for t in trend:
        if t.get('week_start'):
            t['week_start'] = str(t['week_start'])
        for k in ('low', 'mid', 'high', 'total'):
            t[k] = int(t.get(k) or 0)

    if risk_summary and risk_summary.get('last_prediction_at'):
        risk_summary['last_prediction_at'] = risk_summary['last_prediction_at'].strftime('%Y-%m-%dT%H:%M:%S')
    if risk_summary:
        for k in ('low_count', 'mid_count', 'high_count', 'total_predictions'):
            risk_summary[k] = int(risk_summary.get(k) or 0)

    return jsonify({'community': comm, 'risk_summary': risk_summary, 'facilities': facilities, 'trend': trend})


# ════════════════════════════════════════════════════════════════
#  HELPERS
# ════════════════════════════════════════════════════════════════
def _load_active_model():
    """Returns (model_row_dict, model_object). Falls back to base model.pkl."""
    conn = db.get_conn()
    # FIX #1 & #2
    with _cursor(conn) as cur:
        cur.execute(
            'SELECT * FROM model_versions WHERE is_active = 1 ORDER BY created_at DESC LIMIT 1'
        )
        row = cur.fetchone()

    if row:
        path = Path(row['file_path'])
        if path.exists():
            return row, joblib.load(path)   # FIX #5: row is already a dict

    if BASE_MODEL.exists():
        return {'id': 0, 'version_name': 'model.pkl'}, joblib.load(BASE_MODEL)

    return None, None


@app.route('/dashboard/risk-heatmap')
def dashboard_risk_heatmap():
    """
    GET /dashboard/risk-heatmap
    Query params:
      period  = all | week | month          (default: all)
      risk    = all | low risk | mid risk | high risk  (default: all)
      source  = patients | predictions      (default: patients)
 
    Returns JSON:
      { "points": [ { lat, lng, weight, risk_level, municipality, barangay, count }, ... ] }
 
    Location strategy (in priority order):
      1. communities table centroid (most accurate)
      2. Deterministic placeholder derived from municipality name hash
         (only when no communities row exists — see TODO note below)
    """
    # ── Validate & whitelist params ──────────────────────────────
    raw_risk   = request.args.get('risk',   'all').strip().lower()
    raw_period = request.args.get('period', 'all').strip().lower()
    raw_source = request.args.get('source', 'patients').strip().lower()
 
    if raw_risk   not in _ALLOWED_RISK:    raw_risk   = 'all'
    if raw_period not in _ALLOWED_PERIOD:  raw_period = 'all'
    if raw_source not in _ALLOWED_SOURCE:  raw_source = 'patients'
 
    conn = db.get_conn()
 
    # ── Build period WHERE fragment ──────────────────────────────
    if raw_period == 'week':
        period_clause = "AND DATE(ts) >= DATE(DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) DAY))"
    elif raw_period == 'month':
        period_clause = "AND YEAR(ts) = YEAR(CURDATE()) AND MONTH(ts) = MONTH(CURDATE())"
    else:
        period_clause = ""
 
    # ── Build risk WHERE fragment ────────────────────────────────
    risk_params = []
    if raw_risk != 'all':
        risk_clause = "AND risk_level = %s"
        risk_params = [raw_risk]
    else:
        risk_clause = ""
 
    # ── Fetch grouped rows by source ────────────────────────────
    if raw_source == 'patients':
        # One row per patient — use patient location, fall back to their
        # latest prediction's location if patient row has no municipality/barangay.
        sql = f"""
            SELECT
                COALESCE(NULLIF(p.municipality,''), NULLIF(pr.municipality,'')) AS municipality,
                COALESCE(NULLIF(p.barangay,''),     NULLIF(pr.barangay,''))     AS barangay,
                p.latest_risk_level           AS risk_level,
                c.latitude,
                c.longitude,
                COUNT(*)                       AS cnt
            FROM patients p
            LEFT JOIN predictions pr ON pr.id = (
                SELECT id FROM predictions
                WHERE patient_id = p.id
                ORDER BY created_at DESC LIMIT 1
            )
            LEFT JOIN communities c
                   ON c.municipality = COALESCE(NULLIF(p.municipality,''), NULLIF(pr.municipality,''))
                  AND c.barangay     = COALESCE(NULLIF(p.barangay,''),     NULLIF(pr.barangay,''))
            WHERE p.latest_risk_level IS NOT NULL
              {'AND DATE(p.last_prediction_at) >= DATE(DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) DAY))' if raw_period == 'week' else ''}
              {'AND YEAR(p.last_prediction_at) = YEAR(CURDATE()) AND MONTH(p.last_prediction_at) = MONTH(CURDATE())' if raw_period == 'month' else ''}
              {f'AND p.latest_risk_level = %s' if raw_risk != 'all' else ''}
            GROUP BY municipality, barangay, p.latest_risk_level, c.latitude, c.longitude
                HAVING municipality IS NOT NULL AND municipality <> ''
                    AND barangay     IS NOT NULL AND barangay     <> ''
            LIMIT 500
        """
    else:
        # All prediction records (one row per prediction).
        sql = f"""
            SELECT
                COALESCE(NULLIF(pr.municipality,''), NULLIF(pa.municipality,'')) AS municipality,
                COALESCE(NULLIF(pr.barangay,''),     NULLIF(pa.barangay,''))     AS barangay,
                pr.risk_level,
                c.latitude,
                c.longitude,
                COUNT(*)                                                          AS cnt
            FROM predictions pr
            LEFT JOIN patients pa ON pa.id = pr.patient_id
            LEFT JOIN communities c
                   ON c.municipality = COALESCE(NULLIF(pr.municipality,''), NULLIF(pa.municipality,''))
                  AND c.barangay     = COALESCE(NULLIF(pr.barangay,''),     NULLIF(pa.barangay,''))
            WHERE pr.risk_level IS NOT NULL
              {f'AND DATE(pr.created_at) >= DATE(DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) DAY))' if raw_period == 'week' else ''}
              {f'AND YEAR(pr.created_at) = YEAR(CURDATE()) AND MONTH(pr.created_at) = MONTH(CURDATE())' if raw_period == 'month' else ''}
              {f'AND pr.risk_level = %s' if raw_risk != 'all' else ''}
            GROUP BY municipality, barangay, pr.risk_level, c.latitude, c.longitude
            HAVING municipality IS NOT NULL AND municipality <> ''
               AND barangay     IS NOT NULL AND barangay     <> ''
            LIMIT 500
        """
 
    with _cursor(conn) as cur:
        cur.execute(sql, risk_params)
        rows = cur.fetchall()
 
    # ── Build output points list ─────────────────────────────────
    # Group by (municipality, barangay) to merge rows with same location
    # but potentially different risk levels — keep dominant risk.
    grouped: dict[tuple, dict] = {}
 
    for r in rows:
        muni  = (r.get('municipality') or '').strip()
        brgy  = (r.get('barangay') or '').strip()
        rlvl  = (r.get('risk_level') or '').strip().lower()
        cnt   = int(r.get('cnt') or 1)
        lat   = r.get('latitude')
        lng   = r.get('longitude')
 
        if not muni or not brgy or not rlvl:
            continue
 
        # Fallback: deterministic placeholder centroid when communities has no row.
        # TODO: Replace this with a proper geocoding table or real coordinates
        #       for municipalities not yet seeded in the communities table.
        if lat is None or lng is None:
            lat, lng = _placeholder_centroid(muni)
 
        lat = float(lat)
        lng = float(lng)
 
        key = (muni, brgy)
        if key not in grouped:
            grouped[key] = {
                'lat': lat, 'lng': lng,
                'municipality': muni, 'barangay': brgy,
                'risk_level': rlvl, 'count': cnt,
                'weight_sum': _HEAT_WEIGHTS.get(rlvl, 0.4) * cnt,
                'total': cnt,
            }
        else:
            g = grouped[key]
            g['count'] += cnt
            g['total'] += cnt
            g['weight_sum'] += _HEAT_WEIGHTS.get(rlvl, 0.4) * cnt
            # Dominant risk = highest-weight label seen so far
            if _HEAT_WEIGHTS.get(rlvl, 0) > _HEAT_WEIGHTS.get(g['risk_level'], 0):
                g['risk_level'] = rlvl
 
    # Build final point list with normalised weight
    max_total = max((v['total'] for v in grouped.values()), default=1)
    points = []
    for v in grouped.values():
        # Weighted intensity: blend count-scaled weight (0–1)
        intensity = min(1.0, (v['weight_sum'] / v['total']) * (v['total'] / max_total + 0.3))
        points.append({
            'lat':          v['lat'],
            'lng':          v['lng'],
            'weight':       round(intensity, 3),
            'risk_level':   v['risk_level'],
            'municipality': v['municipality'],
            'barangay':     v['barangay'],
            'count':        v['count'],
        })
 
    return jsonify({'points': points})
 
 
def _placeholder_centroid(municipality: str) -> tuple[float, float]:
    """
    Return a rough deterministic centroid for a municipality that has
    no entry in the communities table.
 
    Strategy: use a small lookup of known NCR municipalities; for unknowns
    derive a tiny offset from the string hash so sibling barangays don't
    all stack on the exact same pixel.
 
    TODO: Seed the communities table with real coordinates so this function
          is never called in production.
    """
    _NCR_CENTROIDS = {
        'quezon city':      (14.676, 121.043),
        'caloocan city':    (14.657, 120.967),
        'marikina city':    (14.650, 121.103),
        'pasig city':       (14.576, 121.069),
        'taguig city':      (14.521, 121.053),
        'mandaluyong':      (14.579, 121.021),
        'las piñas city':   (14.449, 120.994),
        'muntinlupa city':  (14.408, 121.040),
        'parañaque city':   (14.479, 121.016),
        'valenzuela city':  (14.700, 120.984),
        'malabon city':     (14.662, 120.957),
        'navotas':          (14.666, 120.944),
        'san juan':         (14.600, 121.030),
        'manila':           (14.599, 120.984),
        'makati':           (14.556, 121.023),
        'pasay':            (14.538, 121.000),
        'pateros':          (14.543, 121.068),
    }
    key  = municipality.lower().strip()
    base = _NCR_CENTROIDS.get(key, (14.600, 121.000))  # fallback: Metro Manila centroid
    # Tiny hash-based jitter so multiple unknowns don't overlap
    h    = hash(municipality) & 0xFFFF
    jitter_lat = ((h & 0xFF)   / 255.0 - 0.5) * 0.02
    jitter_lng = ((h >> 8)     / 255.0 - 0.5) * 0.02
    return (base[0] + jitter_lat, base[1] + jitter_lng)


# ════════════════════════════════════════════════════════════════
#  ERROR HANDLERS
# ════════════════════════════════════════════════════════════════
@app.errorhandler(404)
def not_found(e):
    return jsonify({'error': 'Not found'}), 404

@app.errorhandler(500)
def server_error(e):
    return jsonify({'error': 'Internal server error'}), 500


if __name__ == '__main__':
    app.run(debug=True, host='0.0.0.0', port=8800)