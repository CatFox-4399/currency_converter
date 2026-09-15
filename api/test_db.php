<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
header('Content-Type: text/plain; charset=utf-8');

echo "=== CurrencyX DB Diagnostic ===\n";
require_once __DIR__ . '/../includes/config.php';

echo "PHP Version: " . PHP_VERSION . "\n";
echo "DB_HOST: " . (defined('DB_HOST') ? DB_HOST : 'undefined') . "\n";
echo "DB_NAME: " . (defined('DB_NAME') ? DB_NAME : 'undefined') . "\n";
echo "DB_USER: " . (defined('DB_USER') ? DB_USER : 'undefined') . "\n";

try {
    $dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s', DB_HOST, DB_NAME, DB_CHARSET);
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    echo "STATUS: Database Connection SUCCESSFUL!\n";

    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo "Tables found (" . count($tables) . "): " . implode(', ', $tables) . "\n";

    if (in_array('currencies', $tables, true)) {
        $count = $pdo->query("SELECT COUNT(*) FROM currencies")->fetchColumn();
        echo "Currencies table count: " . $count . " rows\n";
    } else {
        echo "WARNING: 'currencies' table does NOT exist yet!\n";
    }
} catch (Throwable $e) {
    echo "STATUS: Connection FAILED!\n";
    echo "Error Message: " . $e->getMessage() . "\n";
}
