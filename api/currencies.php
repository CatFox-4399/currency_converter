<?php
/**
 * API: Currency List
 * GET /api/currencies.php?q=search_term&lang=en
 * Returns: { currencies: [{code, currency_en, currency_zh, country_en, country_zh, symbol}] }
 */

define('APP_ROOT', dirname(__DIR__));
require_once APP_ROOT . '/includes/config.php';
require_once APP_ROOT . '/includes/helpers.php';
require_once APP_ROOT . '/includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    jsonResponse(['error' => true, 'message' => 'Method not allowed.'], 405);
}

enforceRateLimit('currencies');

$query = trim($_GET['q'] ?? '');
$lang  = strtolower(trim($_GET['lang'] ?? 'en'));
$lang  = in_array($lang, ['en', 'zh'], true) ? $lang : 'en';

$pdo = db();

if ($query === '') {
    $stmt = $pdo->prepare(
        "SELECT code, currency_en, currency_zh, country_en, country_zh, symbol
         FROM currencies
         ORDER BY code ASC"
    );
    $stmt->execute();
} else {
    $like  = '%' . $query . '%';
    $stmt = $pdo->prepare(
        "SELECT code, currency_en, currency_zh, country_en, country_zh, symbol
         FROM currencies
         WHERE code        LIKE ?
            OR currency_en LIKE ?
            OR currency_zh LIKE ?
            OR country_en  LIKE ?
            OR country_zh  LIKE ?
         ORDER BY
           CASE WHEN code = ? THEN 0 ELSE 1 END,
           code ASC"
    );
    $upper = strtoupper($query);
    $stmt->execute([$like, $like, $like, $like, $like, $upper]);
}

$currencies = $stmt->fetchAll();

// Sanitise output
$out = array_map(function (array $c) {
    return [
        'code'        => e($c['code']),
        'currency_en' => e($c['currency_en']),
        'currency_zh' => e($c['currency_zh']),
        'country_en'  => e($c['country_en']),
        'country_zh'  => e($c['country_zh']),
        'symbol'      => e($c['symbol']),
    ];
}, $currencies);

jsonResponse(['error' => false, 'currencies' => $out]);
