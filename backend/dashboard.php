<?php


header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/db.php';

try {
    $pdo = getDbConnection();
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'DB connection failed: ' . $e->getMessage()]);
    exit;
}

// ── Router ───────────────────────────────────────────────────
$action = $_GET['action'] ?? 'stats';

switch ($action) {
    case 'stats':            echo json_encode(getStats($pdo));                  break;
    case 'recent':           echo json_encode(getRecentPredictions($pdo));      break;
    case 'high-risk':        echo json_encode(getHighRisk($pdo));               break;
    case 'alerts':           echo json_encode(getAlerts($pdo));                 break;
    case 'weekly':           echo json_encode(getWeekly($pdo));                 break;
    case 'model':            echo json_encode(getActiveModel($pdo));            break;
    case 'hr-distribution':  echo json_encode(getHrDistribution($pdo));         break;
    default:
        http_response_code(404);
        echo json_encode(['error' => 'Unknown action']);
}

// ════════════════════════════════════════════════════════════════
//  STATS  — summary card data
//
//  SQL: aggregate counts from patients, predictions, alerts
// ════════════════════════════════════════════════════════════════
function getStats(PDO $pdo): array
{
    // Total patients
    $total_patients = (int) $pdo->query("SELECT COUNT(*) FROM patients")->fetchColumn();

    // Total predictions + risk breakdown (all-time)
    $row = $pdo->query("
        SELECT
            COUNT(*)                                         AS total,
            SUM(risk_level = 'low risk')                     AS low_risk,
            SUM(risk_level = 'mid risk')                     AS mid_risk,
            SUM(risk_level = 'high risk')                    AS high_risk
        FROM predictions
    ")->fetch();

    // This week
    $wk = $pdo->query("
        SELECT
            SUM(risk_level = 'low risk')  AS low,
            SUM(risk_level = 'mid risk')  AS mid,
            SUM(risk_level = 'high risk') AS high
        FROM predictions
        WHERE created_at >= DATE(DATE_SUB(NOW(), INTERVAL WEEKDAY(NOW()) DAY))
    ")->fetch();

    // This month
    $mo = $pdo->query("
        SELECT
            SUM(risk_level = 'low risk')  AS low,
            SUM(risk_level = 'mid risk')  AS mid,
            SUM(risk_level = 'high risk') AS high
        FROM predictions
        WHERE YEAR(created_at) = YEAR(NOW())
          AND MONTH(created_at) = MONTH(NOW())
    ")->fetch();

    // Unresolved alerts
    $active_alerts = (int) $pdo->query(
        "SELECT COUNT(*) FROM alerts WHERE is_resolved = 0"
    )->fetchColumn();

    return [
        'total_patients'    => $total_patients,
        'total_predictions' => (int) ($row['total']     ?? 0),
        'low_risk_count'    => (int) ($row['low_risk']  ?? 0),
        'mid_risk_count'    => (int) ($row['mid_risk']  ?? 0),
        'high_risk_count'   => (int) ($row['high_risk'] ?? 0),
        'active_alerts'     => $active_alerts,
        'week_low'          => (int) ($wk['low']  ?? 0),
        'week_mid'          => (int) ($wk['mid']  ?? 0),
        'week_high'         => (int) ($wk['high'] ?? 0),
        'month_low'         => (int) ($mo['low']  ?? 0),
        'month_mid'         => (int) ($mo['mid']  ?? 0),
        'month_high'        => (int) ($mo['high'] ?? 0),
    ];
}

// ════════════════════════════════════════════════════════════════
//  RECENT PREDICTIONS  (updated)
//
//  is_saved = 1 when prediction has a patient_id AND a health
//  record exists for that patient.
//
//  SQL: LEFT JOIN patients; EXISTS subquery on health_records
// ════════════════════════════════════════════════════════════════
function getRecentPredictions(PDO $pdo): array
{
    $stmt = $pdo->query("
        SELECT
            pred.id,
            COALESCE(pat.name, 'Anonymous') AS patient_name,
            pred.risk_level,
            pred.probability_score,
            pred.patient_id,
            pred.created_at,
            CASE
                WHEN pred.patient_id IS NOT NULL
                 AND EXISTS (
                     SELECT 1 FROM health_records hr
                     WHERE hr.patient_id = pred.patient_id
                 )
                THEN 1 ELSE 0
            END AS is_saved
        FROM predictions pred
        LEFT JOIN patients pat ON pat.id = pred.patient_id
        ORDER BY pred.created_at DESC
        LIMIT 10
    ");
    $rows = $stmt->fetchAll();
    foreach ($rows as &$r) {
        $r['is_saved'] = (bool) $r['is_saved'];
    }
    return ['predictions' => $rows];
}

// ════════════════════════════════════════════════════════════════
//  HEALTH RECORDS DISTRIBUTION
//
//  Risk distribution based on patients who have saved health
//  records, categorised by their most recent prediction.
//  Split into all-time / week / month buckets.
//
//  SQL: DISTINCT patient_ids from health_records JOIN latest pred
// ════════════════════════════════════════════════════════════════
function getHrDistribution(PDO $pdo): array
{
    // Helper: run the distribution query for a given recorded_at WHERE clause
    $run = function(string $where) use ($pdo): array {
        $stmt = $pdo->query("
            SELECT
                SUM(latest_pred.risk_level = 'low risk')  AS low,
                SUM(latest_pred.risk_level = 'mid risk')  AS mid,
                SUM(latest_pred.risk_level = 'high risk') AS high
            FROM (
                SELECT DISTINCT hr.patient_id
                FROM health_records hr
                WHERE hr.patient_id IS NOT NULL {$where}
            ) AS hr_patients
            JOIN predictions latest_pred ON latest_pred.id = (
                SELECT id FROM predictions
                WHERE patient_id = hr_patients.patient_id
                ORDER BY created_at DESC LIMIT 1
            )
        ");
        $r = $stmt->fetch();
        return [
            'low'  => (int) ($r['low']  ?? 0),
            'mid'  => (int) ($r['mid']  ?? 0),
            'high' => (int) ($r['high'] ?? 0),
        ];
    };

    return [
        'all'   => $run(''),
        'week'  => $run("AND hr.recorded_at >= DATE(DATE_SUB(NOW(), INTERVAL WEEKDAY(NOW()) DAY))"),
        'month' => $run("AND YEAR(hr.recorded_at) = YEAR(NOW()) AND MONTH(hr.recorded_at) = MONTH(NOW())"),
    ];
}

// ════════════════════════════════════════════════════════════════
//  HIGH-RISK PATIENTS
//
//  SQL: patients whose latest prediction is 'high risk'
// ════════════════════════════════════════════════════════════════
function getHighRisk(PDO $pdo): array
{
    $stmt = $pdo->query("
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
            SELECT id FROM predictions
            WHERE patient_id = pat.id
              AND risk_level = 'high risk'
            ORDER BY created_at DESC
            LIMIT 1
        )
        ORDER BY latest.created_at DESC
    ");
    return ['patients' => $stmt->fetchAll()];
}

// ════════════════════════════════════════════════════════════════
//  ALERTS
//
//  SQL: SELECT 20 most recent alerts with patient name,
//       unresolved first
// ════════════════════════════════════════════════════════════════
function getAlerts(PDO $pdo): array
{
    $stmt = $pdo->query("
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
    ");
    $rows = $stmt->fetchAll();
    foreach ($rows as &$r) {
        $r['is_resolved'] = (bool) $r['is_resolved'];
    }
    return ['alerts' => $rows];
}

// ════════════════════════════════════════════════════════════════
//  WEEKLY CHART DATA
//
//  SQL: per-day prediction counts for last 7 days, by risk level
// ════════════════════════════════════════════════════════════════
function getWeekly(PDO $pdo): array
{
    $stmt = $pdo->query("
        SELECT
            DATE(created_at)                       AS day,
            SUM(risk_level = 'low risk')           AS low,
            SUM(risk_level = 'mid risk')           AS mid,
            SUM(risk_level = 'high risk')          AS high
        FROM predictions
        WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
        GROUP BY day
        ORDER BY day ASC
    ");
    $dbRows = $stmt->fetchAll();
    $map    = [];
    foreach ($dbRows as $r) $map[$r['day']] = $r;

    $result = [];
    for ($i = 6; $i >= 0; $i--) {
        $d   = date('Y-m-d', strtotime("-{$i} days"));
        $row = $map[$d] ?? [];
        $result[] = [
            'day'       => $d,
            'day_label' => date('D', strtotime($d)),  // e.g. "Mon"
            'low'       => (int) ($row['low']  ?? 0),
            'mid'       => (int) ($row['mid']  ?? 0),
            'high'      => (int) ($row['high'] ?? 0),
        ];
    }
    return ['days' => $result];
}

// ════════════════════════════════════════════════════════════════
//  ACTIVE MODEL INFO
//
//  SQL: SELECT active model from model_versions WHERE is_active=1
// ════════════════════════════════════════════════════════════════
function getActiveModel(PDO $pdo): array
{
    $stmt = $pdo->query("
        SELECT id, version_name, accuracy, precision_score,
               recall_score, f1_score, is_active, created_at
        FROM model_versions
        ORDER BY created_at DESC
    ");
    $versions = $stmt->fetchAll();
    foreach ($versions as &$v) $v['is_active'] = (bool) $v['is_active'];

    $active = null;
    foreach ($versions as $v) {
        if ($v['is_active']) { $active = $v; break; }
    }

    return ['versions' => $versions, 'active' => $active];
}