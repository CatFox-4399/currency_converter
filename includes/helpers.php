<?php
/**
 * Helper Utilities
 * Currency Converter — includes/helpers.php
 */

if (!defined('APP_ROOT')) {
    define('APP_ROOT', dirname(__DIR__));
}
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';

// ── Output ────────────────────────────────────────────────────

/**
 * Send a JSON response and exit.
 */
function jsonResponse(array $data, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    if ($status === 200) {
        header('Cache-Control: no-store, no-cache, must-revalidate');
    }
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

/**
 * Safely escape a value for HTML output.
 */
function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

// ── Validation ────────────────────────────────────────────────

/**
 * Validate a 3-letter uppercase currency code.
 */
function isValidCode(string $code): bool
{
    return (bool) preg_match('/^[A-Z]{3}$/', $code);
}

/**
 * Normalise and validate a currency code from user input.
 * Returns the uppercased code on success, or null on failure.
 */
function normaliseCode(string $raw): ?string
{
    $code = strtoupper(trim($raw));
    return isValidCode($code) ? $code : null;
}

/**
 * Validate a numeric amount (positive, finite).
 */
function isValidAmount(mixed $value): bool
{
    if (!is_numeric($value)) return false;
    $f = (float) $value;
    return $f > 0 && is_finite($f) && $f <= 1_000_000_000;
}

// ── Networking ────────────────────────────────────────────────

/**
 * Perform an HTTP GET request using file_get_contents.
 * Returns the response body or null on failure.
 */
function httpGet(string $url, int $timeout = 8): ?string
{
    $opts = [
        'http' => [
            'method'         => 'GET',
            'timeout'        => $timeout,
            'user_agent'     => 'CurrencyX/1.0 (+https://github.com/currencyx)',
            'ignore_errors'  => false,
        ],
        'ssl'  => [
            'verify_peer'      => true,
            'verify_peer_name' => true,
        ],
    ];
    $ctx    = stream_context_create($opts);
    $result = @file_get_contents($url, false, $ctx);
    if ($result === false) {
        error_log('[CurrencyX] httpGet failed for: ' . $url);
        return null;
    }
    return $result;
}

// ── Client IP ─────────────────────────────────────────────────

function clientIp(): string
{
    foreach (['HTTP_X_FORWARDED_FOR', 'HTTP_CLIENT_IP', 'REMOTE_ADDR'] as $key) {
        if (!empty($_SERVER[$key])) {
            $ip = trim(explode(',', $_SERVER[$key])[0]);
            if (filter_var($ip, FILTER_VALIDATE_IP)) {
                return $ip;
            }
        }
    }
    return '0.0.0.0';
}

// ── Rate Limiting ─────────────────────────────────────────────

/**
 * Check and update API rate limit for the current client.
 * Returns true if within limit, false if exceeded (sends 429 automatically).
 */
function enforceRateLimit(string $endpoint): bool
{
    $ip = clientIp();

    // Never rate-limit localhost development / testing
    if (in_array($ip, ['127.0.0.1', '::1', 'localhost', '0.0.0.0'], true)) {
        return true;
    }

    $pdo = db();

    try {
        // Remove stale windows using MySQL's internal clock
        $pdo->prepare("DELETE FROM api_rate_limit WHERE window_start < DATE_SUB(NOW(), INTERVAL ? SECOND)")
            ->execute([RATE_LIMIT_WINDOW]);

        // Upsert: reset count if outside window, increment otherwise
        $pdo->prepare(
            "INSERT INTO api_rate_limit (ip_address, endpoint, request_count, window_start)
             VALUES (?, ?, 1, NOW())
             ON DUPLICATE KEY UPDATE
               request_count = IF(window_start < DATE_SUB(NOW(), INTERVAL ? SECOND), 1, request_count + 1),
               window_start  = IF(window_start < DATE_SUB(NOW(), INTERVAL ? SECOND), NOW(), window_start)"
        )->execute([$ip, $endpoint, RATE_LIMIT_WINDOW, RATE_LIMIT_WINDOW]);

        $stmt = $pdo->prepare(
            "SELECT request_count FROM api_rate_limit WHERE ip_address = ? AND endpoint = ?"
        );
        $stmt->execute([$ip, $endpoint]);
        $row = $stmt->fetch();

        if ($row && $row['request_count'] > RATE_LIMIT_MAX) {
            jsonResponse([
                'error'   => true,
                'message' => 'Too many requests. Please slow down and try again in a moment.',
            ], 429);
        }
        return true;
    } catch (PDOException $e) {
        error_log('[CurrencyX] Rate limit error: ' . $e->getMessage());
        return true; // Fail open — don't block legitimate users on DB error
    }
}

// ── Period Helpers ────────────────────────────────────────────

/**
 * Map a period string to start/end dates and a sample interval.
 * Returns ['start' => 'YYYY-MM-DD', 'end' => 'YYYY-MM-DD', 'step' => int (days)]
 */
function periodToDates(string $period): array
{
    $today = date('Y-m-d');
    $map   = [
        '7d'  => [7,    1],
        '30d' => [30,   1],
        '90d' => [90,   1],
        '1y'  => [365,  1],
        '5y'  => [1825, 7],
        'max' => [9999, 30],
    ];

    $p     = strtolower(trim($period));
    [$days, $step] = $map[$p] ?? $map['30d'];

    $startTs = ($days >= 9000)
        ? mktime(0, 0, 0, 1, 4, 1999)   // Frankfurter earliest data
        : strtotime("-{$days} days");

    return [
        'start' => date('Y-m-d', $startTs),
        'end'   => $today,
        'step'  => $step,
    ];
}

// ── Routing & URL Helpers ─────────────────────────────────────

/**
 * Automatically determine the base URL path of the application.
 * Returns '' when deployed at web root (e.g. localhost:8080),
 * or '/currency_converter' when deployed under a subfolder.
 */
function getBaseUrl(): string
{
    // 1. If BASE_URL constant is defined and non-empty, use it
    if (defined('BASE_URL') && BASE_URL !== '') {
        return rtrim(BASE_URL, '/');
    }

    // 2. Detect from SCRIPT_NAME (e.g. /currency_converter/index.php)
    if (!empty($_SERVER['SCRIPT_NAME'])) {
        $script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME']);
        $dir = rtrim(dirname($script), '/\\');
        if ($dir !== '' && $dir !== '.' && $dir !== '/') {
            // Strip any subdirectories if called from api/ or includes/
            $dir = preg_replace('#/(?:api|assets|includes)$#i', '', $dir);
            if ($dir !== '' && $dir !== '/') {
                return $dir;
            }
        }
    }

    // 3. Fallback check from REQUEST_URI
    if (!empty($_SERVER['REQUEST_URI'])) {
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '';
        if (preg_match('#^(/[^/]+)#', $path, $m)) {
            if (strtolower($m[1]) === '/currency_converter') {
                return $m[1];
            }
        }
    }

    // 4. Document root vs App root comparison
    if (!empty($_SERVER['DOCUMENT_ROOT']) && defined('APP_ROOT')) {
        $doc = rtrim(str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT']) ?: $_SERVER['DOCUMENT_ROOT']), '/');
        $app = rtrim(str_replace('\\', '/', realpath(APP_ROOT) ?: APP_ROOT), '/');
        if (str_starts_with($app, $doc)) {
            $sub = substr($app, strlen($doc));
            if ($sub !== '' && $sub !== false) {
                return '/' . trim(str_replace('\\', '/', $sub), '/');
            }
        }
    }

    return '';
}

