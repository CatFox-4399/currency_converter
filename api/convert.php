<?php
/**
 * API: Currency Conversion
 * POST /api/convert.php
 * Body: { "from": "USD", "to": "MYR", "amount": 100 }
 * Returns: { from, to, amount, result, rate, last_updated, source, stale }
 */

define('APP_ROOT', dirname(__DIR__));
require_once APP_ROOT . '/includes/config.php';
require_once APP_ROOT . '/includes/helpers.php';
require_once APP_ROOT . '/includes/rates.php';

// Only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['error' => true, 'message' => 'Method not allowed. Use POST.'], 405);
}

// Rate limit
enforceRateLimit('convert');

// Parse JSON body
$body = json_decode(file_get_contents('php://input'), true);
if (!is_array($body)) {
    jsonResponse(['error' => true, 'message' => 'Invalid JSON body.'], 400);
}

// Validate inputs
$fromRaw   = $body['from']   ?? '';
$toRaw     = $body['to']     ?? '';
$amountRaw = $body['amount'] ?? '';

$from = normaliseCode((string) $fromRaw);
$to   = normaliseCode((string) $toRaw);

if ($from === null || $to === null) {
    jsonResponse(['error' => true, 'message' => 'Invalid currency code. Must be 3 letters (e.g. USD).'], 400);
}

if (!isValidAmount($amountRaw)) {
    jsonResponse(['error' => true, 'message' => 'Invalid amount. Must be a positive number up to 1,000,000,000.'], 400);
}

$amount = (float) $amountRaw;

// Same currency shortcut
if ($from === $to) {
    jsonResponse([
        'error'        => false,
        'from'         => $from,
        'to'           => $to,
        'amount'       => $amount,
        'result'       => $amount,
        'rate'         => 1.0,
        'last_updated' => date('Y-m-d H:i:s') . ' UTC',
        'source'       => 'direct',
        'stale'        => false,
    ]);
}

// Fetch rate
$rateData = getRate($from, $to);

if ($rateData === null) {
    jsonResponse([
        'error'   => true,
        'message' => 'Live exchange rate is temporarily unavailable. Please try again later.',
    ], 503);
}

$rate   = $rateData['rate'];
$result = round($amount * $rate, 4);

jsonResponse([
    'error'        => false,
    'from'         => $from,
    'to'           => $to,
    'amount'       => $amount,
    'result'       => $result,
    'rate'         => $rate,
    'last_updated' => $rateData['fetched_at'],
    'source'       => $rateData['source'],
    'stale'        => $rateData['stale'] ?? false,
]);
