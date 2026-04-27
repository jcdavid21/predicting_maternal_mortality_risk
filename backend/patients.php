<?php
/**
 * backend/patients.php — Patients CRUD API (revised)
 *
 * Changes from original:
 *  - patient_code is auto-generated (PAT-XXXX format) on create — never user-supplied
 *  - municipality / barangay / community are resolved from the communities table (select-driven)
 *  - socioeconomic_index and low_resource_area are auto-filled from the selected community
 *  - prior_complications and comorbidities use fixed enum sets (select-driven)
 *  - has_prior_complication / has_comorbidity derived from those select values automatically
 *  - New action: 'form-options'  → returns all data needed to populate form <select> elements
 *  - New action: 'generate-code' → returns a preview of the next patient code (read-only, for display)
 *
 * All responses: JSON { success, data?, error? }
 */

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

require_once __DIR__ . '/db.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

// ── Allowed enum options (mirrors DB constraints + domain knowledge) ─────────

const PRIOR_COMPLICATIONS = [
    'preterm_birth'       => 'Preterm Birth',
    'hemorrhage'          => 'Hemorrhage',
    'prior_abortion'      => 'Prior Abortion',
    'postpartum_hemorrhage' => 'Postpartum Hemorrhage',
    'eclampsia'           => 'Eclampsia',
    'preeclampsia'        => 'Pre-Eclampsia',
    'placenta_previa'     => 'Placenta Previa',
    'gestational_diabetes' => 'Gestational Diabetes',
    'miscarriage'         => 'Miscarriage / Abortion',
    'stillbirth'          => 'Stillbirth',
    'cesarean_section'    => 'Cesarean Section',
    'anemia'              => 'Severe Anemia',
    'none'                => 'None',
];

const COMORBIDITIES = [
    'hypertension'        => 'Hypertension',
    'diabetes'            => 'Diabetes Mellitus',
    'heart_disease'       => 'Heart Disease',
    'tuberculosis'        => 'Tuberculosis',
    'anemia'              => 'Anemia',
    'asthma'              => 'Asthma',
    'hiv'                 => 'HIV / AIDS',
    'hepatitis'           => 'Hepatitis B',
    'thyroid_disorder'    => 'Thyroid Disorder',
    'kidney_disease'      => 'Chronic Kidney Disease',
    'none'                => 'None',
];

const RISK_LEVELS = ['low risk', 'mid risk', 'high risk'];

// ── Socioeconomic index labels (from communities table comments) ──────────────
const SOCIOECONOMIC_LABELS = [
    0 => 'Moderate',
    1 => 'Low',
    2 => 'Very Low',
];

try {
    $pdo = getDbConnection();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    switch ($action) {

        // ── FORM OPTIONS (feed all <select> elements in one call) ─────────────
        case 'form-options':
            // Communities grouped by municipality
            $stmt = $pdo->query(
                "SELECT id, municipality, barangay, community,
                        socioeconomic_index, low_resource_area
                 FROM communities
                 ORDER BY municipality, barangay"
            );
            $communities = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Distinct municipalities for the first-level select
            $municipalities = array_values(array_unique(array_column($communities, 'municipality')));
            sort($municipalities);

            echo json_encode([
                'success' => true,
                'data'    => [
                    'communities'        => $communities,
                    'municipalities'     => $municipalities,
                    'prior_complications'=> PRIOR_COMPLICATIONS,
                    'comorbidities'      => COMORBIDITIES,
                    'risk_levels'        => RISK_LEVELS,
                    'socioeconomic_labels' => SOCIOECONOMIC_LABELS,
                ],
            ]);
            break;

        // ── GENERATE PREVIEW CODE (for display before save) ──────────────────
        case 'generate-code':
            $next = generateNextPatientCode($pdo);
            echo json_encode(['success' => true, 'data' => ['patient_code' => $next]]);
            break;

        // ── LIST ──────────────────────────────────────────────────────────────
        case 'list':
            $q            = trim($_GET['q'] ?? '');
            $code         = trim($_GET['code'] ?? '');
            $sort_code    = strtolower(trim($_GET['sort_code'] ?? ''));
            $risk         = trim($_GET['risk'] ?? '');
            $municipality = trim($_GET['municipality'] ?? '');
            $page         = max(1, (int)($_GET['page'] ?? 1));
            $per_page     = in_array((int)($_GET['per_page'] ?? 10), [5, 10, 25, 50])
                            ? (int)$_GET['per_page'] : 10;
            $offset       = ($page - 1) * $per_page;

            $where  = ['1=1'];
            $params = [];

            if ($q !== '') {
                $where[]  = '(p.name LIKE ? OR p.contact_number LIKE ?)';
                $like     = '%' . $q . '%';
                $params[] = $like;
                $params[] = $like;
            }
            if ($code !== '') {
                $where[]  = 'p.patient_code LIKE ?';
                $params[] = '%' . $code . '%';
            }
            if ($risk !== '') {
                $where[]  = 'p.latest_risk_level = ?';
                $params[] = $risk;
            }
            if ($municipality !== '') {
                $where[]  = 'p.municipality = ?';
                $params[] = $municipality;
            }

            $whereSQL = implode(' AND ', $where);
            $orderSQL = in_array($sort_code, ['asc', 'desc'], true)
                ? 'p.patient_code ' . strtoupper($sort_code) . ', p.created_at DESC'
                : 'p.created_at DESC';

            $stmtCount = $pdo->prepare("SELECT COUNT(*) FROM patients p WHERE $whereSQL");
            $stmtCount->execute($params);
            $total = (int)$stmtCount->fetchColumn();

            $sql = "SELECT p.id, p.patient_code, p.name, p.age, p.contact_number,
                           p.municipality, p.barangay,
                           p.latest_risk_level, p.latest_probability_score,
                           p.last_prediction_at, p.created_at
                    FROM patients p
                    WHERE $whereSQL
                    ORDER BY $orderSQL
                    LIMIT ? OFFSET ?";

            $stmtList = $pdo->prepare($sql);
            $stmtList->execute(array_merge($params, [$per_page, $offset]));
            $items = $stmtList->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode([
                'success' => true,
                'data'    => [
                    'items'    => $items,
                    'total'    => $total,
                    'page'     => $page,
                    'per_page' => $per_page,
                ],
            ]);
            break;

        // ── GET ONE ───────────────────────────────────────────────────────────
        case 'get':
            $id = (int)($_GET['id'] ?? 0);
            if (!$id) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'id required']);
                break;
            }

            $stmt = $pdo->prepare('SELECT * FROM patients WHERE id = ? LIMIT 1');
            $stmt->execute([$id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$row) {
                http_response_code(404);
                echo json_encode(['success' => false, 'error' => 'Patient not found']);
                break;
            }

            echo json_encode(['success' => true, 'data' => $row]);
            break;

        // ── LATEST HEALTH RECORD ──────────────────────────────────────────────
        case 'latest-health-record':
            $patient_id = (int)($_GET['patient_id'] ?? 0);
            if (!$patient_id) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'patient_id required']);
                break;
            }

            $stmt = $pdo->prepare(
                'SELECT * FROM health_records WHERE patient_id = ? ORDER BY recorded_at DESC LIMIT 1'
            );
            $stmt->execute([$patient_id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            echo json_encode(['success' => true, 'data' => $row ?: null]);
            break;

        // ── CREATE ────────────────────────────────────────────────────────────
        case 'create':
            if ($method !== 'POST') {
                http_response_code(405);
                echo json_encode(['success' => false, 'error' => 'POST required']);
                break;
            }

            // Auto-generate a unique patient code — user cannot supply their own
            $patientCode = generateNextPatientCode($pdo);

            $data = sanitizePatientInput($_POST, $pdo);

            if (empty($data['name'])) {
                http_response_code(422);
                echo json_encode(['success' => false, 'error' => 'name is required']);
                break;
            }

            // Inject auto-generated code
            $data['patient_code'] = $patientCode;

            $cols  = array_keys($data);
            $ph    = array_fill(0, count($cols), '?');
            $sql   = 'INSERT INTO patients (' . implode(',', $cols) . ') VALUES (' . implode(',', $ph) . ')';
            $stmt  = $pdo->prepare($sql);
            $stmt->execute(array_values($data));
            $newId = (int)$pdo->lastInsertId();

            echo json_encode([
                'success' => true,
                'data'    => ['id' => $newId, 'patient_code' => $patientCode],
            ]);
            break;

        // ── UPDATE ────────────────────────────────────────────────────────────
        case 'update':
            if ($method !== 'POST') {
                http_response_code(405);
                echo json_encode(['success' => false, 'error' => 'POST required']);
                break;
            }

            $id = (int)($_GET['id'] ?? 0);
            if (!$id) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'id required']);
                break;
            }

            // Verify patient exists and retrieve the immutable patient_code
            $chkExist = $pdo->prepare('SELECT patient_code FROM patients WHERE id = ? LIMIT 1');
            $chkExist->execute([$id]);
            $existing = $chkExist->fetch(PDO::FETCH_ASSOC);
            if (!$existing) {
                http_response_code(404);
                echo json_encode(['success' => false, 'error' => 'Patient not found']);
                break;
            }

            $data = sanitizePatientInput($_POST, $pdo);

            if (empty($data['name'])) {
                http_response_code(422);
                echo json_encode(['success' => false, 'error' => 'name is required']);
                break;
            }

            // patient_code is immutable — always restore the original, never allow changes
            unset($data['patient_code']);

            $sets = array_map(fn($c) => "$c = ?", array_keys($data));
            $sql  = 'UPDATE patients SET ' . implode(', ', $sets) . ' WHERE id = ?';
            $stmt = $pdo->prepare($sql);
            $stmt->execute(array_merge(array_values($data), [$id]));

            echo json_encode(['success' => true, 'data' => ['updated' => $stmt->rowCount()]]);
            break;

        // ── DELETE ────────────────────────────────────────────────────────────
        case 'delete':
            if ($method !== 'POST') {
                http_response_code(405);
                echo json_encode(['success' => false, 'error' => 'POST required']);
                break;
            }

            $id = (int)($_GET['id'] ?? 0);
            if (!$id) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'id required']);
                break;
            }

            // Cascading deletes handled by FK ON DELETE CASCADE in schema (health_records, alerts, predictions)
            $stmt = $pdo->prepare('DELETE FROM patients WHERE id = ?');
            $stmt->execute([$id]);

            echo json_encode(['success' => true, 'data' => ['deleted' => $stmt->rowCount()]]);
            break;

        // ── MUNICIPALITIES (filter options for list page) ─────────────────────
        case 'municipalities':
            $stmt = $pdo->query(
                "SELECT DISTINCT municipality FROM patients
                 WHERE municipality IS NOT NULL AND municipality <> ''
                 ORDER BY municipality"
            );
            $rows = $stmt->fetchAll(PDO::FETCH_COLUMN);
            echo json_encode(['success' => true, 'data' => $rows]);
            break;

        // ── SIDEBAR BADGE COUNT ───────────────────────────────────────────────
        case 'high-risk-count':
            $stmt  = $pdo->query("SELECT COUNT(*) FROM patients WHERE latest_risk_level = 'high risk'");
            $count = (int)$stmt->fetchColumn();
            echo json_encode(['success' => true, 'data' => ['count' => $count]]);
            break;

        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Unknown action']);
    }

} catch (PDOException $e) {
    http_response_code(500);
    error_log('patients.php PDO error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Database error']);
} catch (Throwable $e) {
    http_response_code(500);
    error_log('patients.php error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Server error']);
}


// ═══════════════════════════════════════════════════════════════════════════════
// HELPERS
// ═══════════════════════════════════════════════════════════════════════════════

/**
 * Generate the next sequential patient code in PAT-XXXX format.
 * Locks against race conditions by scanning the actual patient_code column.
 *
 * @param  PDO    $pdo
 * @return string e.g. "PAT-0042"
 */
function generateNextPatientCode(PDO $pdo): string
{
    // Only consider real (non-anonymous) codes that match our PAT-NNNN pattern
    $stmt = $pdo->query(
        "SELECT patient_code FROM patients
         WHERE patient_code REGEXP '^PAT-[0-9]+$'
         ORDER BY CAST(SUBSTRING(patient_code, 5) AS UNSIGNED) DESC
         LIMIT 1"
    );
    $last = $stmt->fetchColumn();

    $next = 1;
    if ($last) {
        $next = (int)substr($last, 4) + 1;   // strip "PAT-" prefix
    }

    // Pad to at least 4 digits; grow naturally beyond 9999
    return 'PAT-' . str_pad($next, 4, '0', STR_PAD_LEFT);
}

/**
 * Sanitize and cast all allowed patient input fields.
 *
 * Key changes vs original:
 *  - patient_code removed (auto-generated, not user-supplied)
 *  - community / municipality / barangay / socioeconomic_index / low_resource_area
 *    resolved from the communities table when a community_id is posted
 *  - prior_complications / comorbidities validated against fixed enum sets
 *  - has_prior_complication / has_comorbidity auto-derived
 *
 * Only whitelisted columns are returned — never trust raw $_POST keys.
 *
 * @param  array $raw   Typically $_POST
 * @param  PDO   $pdo   Used to look up community data
 * @return array        Column → value map safe for INSERT / UPDATE
 */
function sanitizePatientInput(array $raw, PDO $pdo): array
{
    $out = [];

    // ── Basic string fields ────────────────────────────────────────────────────
    $strings = ['name', 'contact_number', 'address'];
    foreach ($strings as $col) {
        if (isset($raw[$col])) {
            $val        = trim($raw[$col]);
            $out[$col]  = $val === '' ? null : $val;
        }
    }

    // ── Date of birth ──────────────────────────────────────────────────────────
    if (isset($raw['date_of_birth'])) {
        $d = trim($raw['date_of_birth']);
        $out['date_of_birth'] = ($d !== '' && strtotime($d) !== false) ? $d : null;
    }

    // ── Age (integer; can be derived from DOB on the front-end, but stored separately) ──
    if (isset($raw['age']) && $raw['age'] !== '') {
        $out['age'] = (int)$raw['age'];
    } elseif (isset($raw['age'])) {
        $out['age'] = null;
    }

    // ── Community resolution via community_id (select-driven) ─────────────────
    // The form posts a community_id from the communities table.
    // We resolve municipality, barangay, community label, socioeconomic_index,
    // and low_resource_area from the DB so they are always consistent.
    if (!empty($raw['community_id'])) {
        $communityId = (int)$raw['community_id'];
        $stmt = $pdo->prepare(
            'SELECT municipality, barangay, community, socioeconomic_index, low_resource_area
             FROM communities WHERE id = ? LIMIT 1'
        );
        $stmt->execute([$communityId]);
        $comm = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($comm) {
            $out['municipality']           = $comm['municipality'];
            $out['barangay']               = $comm['barangay'];
            $out['community']              = $comm['community'];
            $out['socioeconomic_index']    = (int)$comm['socioeconomic_index'];
            $out['low_resource_area']      = (int)$comm['low_resource_area'];
        }
    }

    // Allow manual distance override (e.g. exact GPS reading from field worker)
    if (isset($raw['distance_to_facility_km']) && $raw['distance_to_facility_km'] !== '') {
        $out['distance_to_facility_km'] = (float)$raw['distance_to_facility_km'];
    } elseif (isset($raw['distance_to_facility_km']) && !isset($out['distance_to_facility_km'])) {
        $out['distance_to_facility_km'] = null;
    }

    // ── Obstetric integer fields ───────────────────────────────────────────────
    $ints = ['prenatal_visits', 'gravida', 'para', 'referral_delay_hours'];
    foreach ($ints as $col) {
        if (isset($raw[$col]) && $raw[$col] !== '') {
            $out[$col] = (int)$raw[$col];
        } elseif (isset($raw[$col])) {
            $out[$col] = null;
        }
    }

    // ── Prior complications (select; comma-separated multi-value allowed) ──────
    if (isset($raw['prior_complications'])) {
        $allowed = array_keys(PRIOR_COMPLICATIONS);
        // Accept a JSON array string or comma-separated string from multi-select
        $raw_val = $raw['prior_complications'];
        if (is_array($raw_val)) {
            $selected = $raw_val;
        } elseif (str_starts_with(trim($raw_val), '[')) {
            $selected = json_decode($raw_val, true) ?? [];
        } else {
            $selected = array_filter(array_map('trim', explode(',', $raw_val)));
        }

        // Sanitize — only keep known values, remove 'none' if other values are present
        $selected = array_values(array_intersect($selected, $allowed));
        if (count($selected) > 1) {
            $selected = array_diff($selected, ['none']);
        }

        if (empty($selected) || $selected === ['none']) {
            $out['prior_complications']   = null;
            $out['has_prior_complication'] = 0;
        } else {
            $out['prior_complications']   = implode(',', $selected);
            $out['has_prior_complication'] = 1;
        }
    }

    // ── Comorbidities (select; comma-separated multi-value allowed) ────────────
    if (isset($raw['comorbidities'])) {
        $allowed  = array_keys(COMORBIDITIES);
        $raw_val  = $raw['comorbidities'];
        if (is_array($raw_val)) {
            $selected = $raw_val;
        } elseif (str_starts_with(trim($raw_val), '[')) {
            $selected = json_decode($raw_val, true) ?? [];
        } else {
            $selected = array_filter(array_map('trim', explode(',', $raw_val)));
        }

        $selected = array_values(array_intersect($selected, $allowed));
        if (count($selected) > 1) {
            $selected = array_diff($selected, ['none']);
        }

        if (empty($selected) || $selected === ['none']) {
            $out['comorbidities']   = null;
            $out['has_comorbidity'] = 0;
        } else {
            $out['comorbidities']   = implode(',', $selected);
            $out['has_comorbidity'] = 1;
        }
    }

    // ── Risk level (enum, select-driven) ──────────────────────────────────────
    if (isset($raw['latest_risk_level'])) {
        $val = trim($raw['latest_risk_level']);
        $out['latest_risk_level'] = in_array($val, RISK_LEVELS, true) ? $val : null;
    }

    // ── Float fields ──────────────────────────────────────────────────────────
    if (isset($raw['latest_probability_score']) && $raw['latest_probability_score'] !== '') {
        $out['latest_probability_score'] = (float)$raw['latest_probability_score'];
    } elseif (isset($raw['latest_probability_score'])) {
        $out['latest_probability_score'] = null;
    }

    return $out;
}