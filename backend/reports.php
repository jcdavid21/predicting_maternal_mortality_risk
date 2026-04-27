<?php
/* ═══════════════════════════════════════════════════════════════════
   reports_api.php  —  MaternaHealth
   Proxy/adapter layer: PHP receives requests from reports.js,
   queries MariaDB directly, and returns JSON.

   Mirrors the structure of high_risk_cases_api.php.

   Endpoints (called via Flask API_BASE on localhost:8800, but you
   can also host these directly if you replace the Flask proxy):

     GET  /reports/summary
     GET  /reports/municipalities
     GET  /reports/predictions
     GET  /reports/risk-distribution
     GET  /reports/trend
     GET  /reports/municipality-breakdown
     GET  /reports/municipality-avg-score

   All endpoints accept optional query params:
     date_from    YYYY-MM-DD
     date_to      YYYY-MM-DD
     risk         high risk | mid risk | low risk
     municipality string
═══════════════════════════════════════════════════════════════════ */

if (session_status() === PHP_SESSION_NONE) session_start();

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }

/* ── DB connection ─────────────────────────────────────────── */
require_once __DIR__ . '/db.php';
$pdo = getDbConnection();

/* ── Router ─────────────────────────────────────────────────── */
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
// Strip any prefix (e.g. /api) so we match the tail
$action = trim(substr($uri, strrpos($uri, '/reports') + 8), '/');
// $action is now: summary | municipalities | predictions |
//                 risk-distribution | trend |
//                 municipality-breakdown | municipality-avg-score

/* ── Shared: sanitise & build WHERE clause ─────────────────── */
function getFilters(): array
{
    $allowed_risk = ['high risk', 'mid risk', 'low risk'];
    return [
        'date_from'    => $_GET['date_from']    ?? '',
        'date_to'      => $_GET['date_to']      ?? '',
        'risk'         => in_array(strtolower($_GET['risk'] ?? ''), $allowed_risk, true)
                            ? strtolower($_GET['risk']) : '',
        'municipality' => trim($_GET['municipality'] ?? ''),
    ];
}

/**
 * Returns [whereClause, params] for the predictions table.
 * All filters operate on predictions.created_at, risk_level, municipality.
 */
function buildWhere(array $f, string $tableAlias = 'pred'): array
{
    $clauses = [];
    $params  = [];

    if ($f['date_from'] !== '') {
        $clauses[] = "{$tableAlias}.created_at >= :date_from";
        $params[':date_from'] = $f['date_from'] . ' 00:00:00';
    }
    if ($f['date_to'] !== '') {
        $clauses[] = "{$tableAlias}.created_at <= :date_to";
        $params[':date_to'] = $f['date_to'] . ' 23:59:59';
    }
    if ($f['risk'] !== '') {
        $clauses[] = "{$tableAlias}.risk_level = :risk";
        $params[':risk'] = $f['risk'];
    }
    if ($f['municipality'] !== '') {
        $clauses[] = "{$tableAlias}.municipality = :municipality";
        $params[':municipality'] = $f['municipality'];
    }

    $where = $clauses ? 'WHERE ' . implode(' AND ', $clauses) : '';
    return [$where, $params];
}

/* ── Dispatch ────────────────────────────────────────────────── */
try {
    switch ($action) {

        /* ────────────────────────────────────────────────────────
           GET /reports/summary
           Returns aggregate counts for the 4 stat cards.
        ──────────────────────────────────────────────────────── */
        case 'summary':
        case '': {
            $f = getFilters();
            [$where, $params] = buildWhere($f);

            // Total patients (unfiltered or filtered by municipality)
            if ($f['municipality'] !== '') {
                $patSql = "SELECT COUNT(DISTINCT id) FROM patients WHERE municipality = :muni";
                $patStmt = $pdo->prepare($patSql);
                $patStmt->execute([':muni' => $f['municipality']]);
            } else {
                $patStmt = $pdo->query("SELECT COUNT(*) FROM patients");
            }
            $total_patients = (int) $patStmt->fetchColumn();

            // Predictions in range
            $predSql  = "SELECT COUNT(*) FROM predictions pred $where";
            $predStmt = $pdo->prepare($predSql);
            $predStmt->execute($params);
            $total_predictions = (int) $predStmt->fetchColumn();

            // High-risk count
            $hrParams   = $params;
            $hrClauses  = $where ? "$where AND pred.risk_level = 'high risk'"
                                  : "WHERE pred.risk_level = 'high risk'";
            $hrStmt     = $pdo->prepare("SELECT COUNT(*) FROM predictions pred $hrClauses");
            $hrStmt->execute($hrParams);
            $high_risk_count = (int) $hrStmt->fetchColumn();

            // Distinct municipalities with predictions
            $muniSql  = "SELECT COUNT(DISTINCT pred.municipality) FROM predictions pred $where WHERE pred.municipality IS NOT NULL";
            // Rebuild properly to avoid duplicate WHERE
            $muniClauses = $where ?: 'WHERE 1=1';
            $muniClauses .= ' AND pred.municipality IS NOT NULL';
            $muniStmt    = $pdo->prepare("SELECT COUNT(DISTINCT pred.municipality) FROM predictions pred " .
                ($where ? $where . " AND pred.municipality IS NOT NULL" : "WHERE pred.municipality IS NOT NULL"));
            $muniStmt->execute($params);
            $municipalities_count = (int) $muniStmt->fetchColumn();

            $avgStmt = $pdo->prepare("SELECT AVG(pred.probability_score) FROM predictions pred $where");
            $avgStmt->execute($params);
            $avg_risk_score = $avgStmt->fetchColumn();
            $avg_risk_score = $avg_risk_score !== null ? (float) $avg_risk_score : null;

            $resolvedAlerts = (int) $pdo->query("SELECT COUNT(*) FROM alerts WHERE is_resolved = 1")->fetchColumn();

            echo json_encode([
                'total_patients'      => $total_patients,
                'total_predictions'   => $total_predictions,
                'high_risk_count'     => $high_risk_count,
                'municipalities_count' => $municipalities_count,
                'resolved_alerts'     => $resolvedAlerts,
                'avg_risk_score'      => $avg_risk_score,
            ]);
            break;
        }

        /* ────────────────────────────────────────────────────────
           GET /reports/municipalities
           Returns distinct municipalities that have predictions.
        ──────────────────────────────────────────────────────── */
        case 'municipalities': {
            $stmt = $pdo->query(
                "SELECT DISTINCT municipality
                 FROM predictions
                 WHERE municipality IS NOT NULL AND municipality != ''
                 ORDER BY municipality ASC"
            );
            $municipalities = $stmt->fetchAll(PDO::FETCH_COLUMN);
            echo json_encode(['municipalities' => $municipalities]);
            break;
        }

        /* ────────────────────────────────────────────────────────
           GET /reports/predictions
           Returns prediction rows joined with patient info for
           the report table. Supports all 4 filters.
        ──────────────────────────────────────────────────────── */
        case 'predictions': {
            $f = getFilters();
            [$where, $params] = buildWhere($f);

            $sql = "
                SELECT
                    pred.id,
                    pat.name          AS patient_name,
                    pat.patient_code,
                    pat.age,
                    pred.municipality,
                    pred.barangay,
                    pred.risk_level,
                    pred.probability_score,
                    pred.created_at   AS predicted_at
                FROM predictions pred
                LEFT JOIN patients pat ON pat.id = pred.patient_id
                $where
                ORDER BY pred.created_at DESC
                LIMIT 5000
            ";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode(['predictions' => $rows]);
            break;
        }

        /* ────────────────────────────────────────────────────────
           GET /reports/risk-distribution
           Returns count per risk_level within filters.
        ──────────────────────────────────────────────────────── */
        case 'risk-distribution': {
            $f = getFilters();
            [$where, $params] = buildWhere($f);

            $sql  = "
                SELECT risk_level, COUNT(*) AS cnt
                FROM predictions pred
                $where
                GROUP BY risk_level
            ";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $dist = [];
            foreach ($rows as $r) {
                $dist[strtolower($r['risk_level'])] = (int) $r['cnt'];
            }

            echo json_encode(['distribution' => $dist]);
            break;
        }

        /* ────────────────────────────────────────────────────────
           GET /reports/trend?group_by=month|week
           Returns time-series counts per risk level.
        ──────────────────────────────────────────────────────── */
        case 'trend': {
            $f = getFilters();
            [$where, $params] = buildWhere($f);

            $groupBy = $_GET['group_by'] ?? 'month';
            $fmt     = $groupBy === 'week'
                ? "CONCAT(YEAR(pred.created_at), '-W', LPAD(WEEK(pred.created_at, 3), 2, '0'))"
                : "DATE_FORMAT(pred.created_at, '%Y-%m')";

            $sql = "
                SELECT
                    {$fmt} AS period,
                    SUM(CASE WHEN pred.risk_level = 'high risk' THEN 1 ELSE 0 END) AS high_risk,
                    SUM(CASE WHEN pred.risk_level = 'mid risk'  THEN 1 ELSE 0 END) AS mid_risk,
                    SUM(CASE WHEN pred.risk_level = 'low risk'  THEN 1 ELSE 0 END) AS low_risk
                FROM predictions pred
                $where
                GROUP BY period
                ORDER BY period ASC
            ";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode(['trend' => $rows]);
            break;
        }

        /* ────────────────────────────────────────────────────────
           GET /reports/municipality-breakdown
           Returns all risk counts per municipality.
        ──────────────────────────────────────────────────────── */
        case 'municipality-breakdown': {
            $f = getFilters();
            // For this chart we apply date and municipality filters,
            // but always show all risk levels.
            $f['risk'] = '';
            [$where, $params] = buildWhere($f);

            $where = $where ? "$where AND pred.municipality IS NOT NULL" : "WHERE pred.municipality IS NOT NULL";

            $sql = "
                SELECT
                    pred.municipality,
                    SUM(CASE WHEN pred.risk_level = 'high risk' THEN 1 ELSE 0 END) AS high_risk_count,
                    SUM(CASE WHEN pred.risk_level = 'mid risk'  THEN 1 ELSE 0 END) AS mid_risk_count,
                    SUM(CASE WHEN pred.risk_level = 'low risk'  THEN 1 ELSE 0 END) AS low_risk_count
                FROM predictions pred
                $where
                GROUP BY pred.municipality
                ORDER BY high_risk_count DESC
            ";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode(['breakdown' => $rows]);
            break;
        }

        /* ────────────────────────────────────────────────────────
           GET /reports/municipality-avg-score
           Returns avg probability_score per municipality.
        ──────────────────────────────────────────────────────── */
        case 'municipality-avg-score': {
            $f = getFilters();
            [$where, $params] = buildWhere($f);

            $nullClause = $where
                ? "$where AND pred.municipality IS NOT NULL"
                : "WHERE pred.municipality IS NOT NULL";

            $sql = "
                SELECT
                    pred.municipality,
                    AVG(pred.probability_score) AS avg_score
                FROM predictions pred
                $nullClause
                GROUP BY pred.municipality
                ORDER BY avg_score DESC
            ";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Cast avg_score to float
            $rows = array_map(function($r) {
                $r['avg_score'] = (float) $r['avg_score'];
                return $r;
            }, $rows);

            echo json_encode(['scores' => $rows]);
            break;
        }

        /* ────────────────────────────────────────────────────────
           404 fallback
        ──────────────────────────────────────────────────────── */
        default: {
            http_response_code(404);
            echo json_encode(['error' => 'Unknown reports endpoint: ' . htmlspecialchars($action)]);
            break;
        }
    }
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error', 'detail' => $e->getMessage()]);
}