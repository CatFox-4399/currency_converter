<?php
/**
 * API: Historical Exchange Rates
 * GET /api/history.php?from=USD&to=MYR&period=30d
 * Periods: 7d | 30d | 90d | 1y | 5y | max
 * Returns: { from, to, period, start, end, data: [{date, rate}] }
 */

define('APP_ROOT', dirname(__DIR__));
require_once APP_ROOT . '/includes/config.php';
require_once APP_ROOT . '/includes/helpers.php';
require_once APP_ROOT . '/includes/rates.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    jsonResponse(['error' => true, 'message' => 'Method not allowed.'], 405);
}

enforceRateLimit('history');

$fromRaw   = $_GET['from']   ?? DEFAULT_FROM;
$toRaw     = $_GET['to']     ?? DEFAULT_TO;
$periodRaw = $_GET['period'] ?? '30d';

$from = normaliseCode((string) $fromRaw);
$to   = normaliseCode((string) $toRaw);

if ($from === null || $to === null) {
    jsonResponse(['error' => true, 'message' => 'Invalid currency code.'], 400);
}

$validPeriods = ['7d', '30d', '90d', '1y', '5y', 'max'];
$period = strtolower(trim($periodRaw));
if (!in_array($period, $validPeriods, true)) {
    $period = '30d';
}

// Same currency edge case
if ($from === $to) {
    ['start' => $start, 'end' => $end] = periodToDates($period);
    $days  = (int) ((strtotime($end) - strtotime($start)) / 86400);
    $data  = [];
    for ($i = 0; $i <= $days; $i += max(1, (int) ($days / 60))) {
        $data[] = ['date' => date('Y-m-d', strtotime($start) + $i * 86400), 'rate' => 1.0];
    }
    jsonResponse([
        'error'  => false,
        'from'   => $from,
        'to'     => $to,
        'period' => $period,
        'start'  => $start,
        'end'    => $end,
        'data'   => $data,
    ]);
}

['start' => $start, 'end' => $end] = periodToDates($period);

$data = getHistoricalRates($from, $to, $period);

if ($data === null || empty($data)) {
    jsonResponse([
        'error'   => true,
        'message' => 'Historical data is not available for this currency pair. Please try a different pair.',
    ], 404);
}

jsonResponse([
    'error'  => false,
    'from'   => $from,
    'to'     => $to,
    'period' => $period,
    'start'  => $start,
    'end'    => $end,
    'count'  => count($data),
    'data'   => $data,
]);
