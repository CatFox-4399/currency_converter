<?php
/**
 * PDO Database Singleton
 * Currency Converter — includes/db.php
 */

if (!defined('APP_ROOT')) {
    define('APP_ROOT', dirname(__DIR__));
}
require_once __DIR__ . '/config.php';

class Database
{
    private static ?PDO $instance = null;

    private function __construct() {}
    private function __clone() {}

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            $charset = defined('DB_CHARSET') ? DB_CHARSET : 'utf8mb4';
            $host    = defined('DB_HOST')    ? DB_HOST    : 'localhost';
            $name    = defined('DB_NAME')    ? DB_NAME    : 'currency_converter';
            $user    = defined('DB_USER')    ? DB_USER    : 'root';
            $pass    = defined('DB_PASS')    ? DB_PASS    : '';

            $dsn = sprintf(
                'mysql:host=%s;dbname=%s;charset=%s',
                $host, $name, $charset
            );
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci',
            ];
            try {
                self::$instance = new PDO($dsn, $user, $pass, $options);
                self::ensureSchema(self::$instance);
            } catch (PDOException $e) {
                error_log('[CurrencyX] DB connection failed: ' . $e->getMessage());
                http_response_code(503);
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode([
                    'error'   => true,
                    'message' => 'Service temporarily unavailable. Please try again later.',
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }
        }
        return self::$instance;
    }

    /**
     * Automatically initialize tables from schema.sql if not present.
     */
    private static function ensureSchema(PDO $pdo): void
    {
        try {
            $check = $pdo->query("SHOW TABLES LIKE 'currencies'");
            if ($check && $check->fetch()) {
                return; // Tables already exist
            }
            $schemaFile = __DIR__ . '/schema.sql';
            if (!file_exists($schemaFile)) {
                return;
            }
            $sql = file_get_contents($schemaFile);
            // Remove comments and multi-database commands
            $sql = preg_replace('/CREATE\s+DATABASE[^;]+;/i', '', $sql);
            $sql = preg_replace('/USE\s+[^;]+;/i', '', $sql);
            $pdo->exec($sql);
        } catch (Throwable $t) {
            error_log('[CurrencyX] Schema auto-init warning: ' . $t->getMessage());
        }
    }
}

/** Convenience shortcut */
function db(): PDO
{
    return Database::getInstance();
}
