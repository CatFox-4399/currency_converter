<?php
/**
 * API: All Rates for a Base Currency
 * GET /api/rates.php?base=USD
 * Returns: { base, rates: [{code, rate, currency_en, currency_zh, country_en, country_zh, symbol, fetched_at}], source, cached }
 */

define('APP_ROOT', dirname(__DIR__));
require_once APP_ROOT . '/includes/config.php';
require_once APP_ROOT . '/includes/helpers.php';
require_once APP_ROOT . '/includes/rates.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    jsonResponse(['error' => true, 'message' => 'Method not allowed.'], 405);
}

enforceRateLimit('rates');

$baseRaw = $_GET['base'] ?? DEFAULT_FROM;
$base    = normaliseCode((string) $baseRaw);

if ($base === null) {
    jsonResponse(['error' => true, 'message' => 'Invalid base currency code.'], 400);
}

$rows = getAllRatesForBase($base);

if ($rows === null) {
    jsonResponse([
        'error'   => true,
        'message' => 'Exchange rates are temporarily unavailable. Please try again later.',
    ], 503);
}

// Build response
$rates      = [];
$source     = '';
$fetchedAt  = '';

foreach ($rows as $row) {
    $rates[] = [
        'code'        => e($row['code']),
        'rate'        => (float) $row['rate'],
        'currency_en' => e($row['currency_en']),
        'currency_zh' => e($row['currency_zh']),
        'country_en'  => e($row['country_en']),
        'country_zh'  => e($row['country_zh']),
        'symbol'      => e($row['symbol']),
        'fetched_at'  => $row['fetched_at'],
    ];
    if (!$source) $source = $row['source'];
    if (!$fetchedAt) $fetchedAt = $row['fetched_at'];
}

jsonResponse([
    'error'      => false,
    'base'       => $base,
    'source'     => $source,
    'fetched_at' => $fetchedAt,
    'count'      => count($rates),
    'rates'      => $rates,
]);
