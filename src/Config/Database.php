<?php
namespace App\Config;

use PDO;
use PDOException;
use Dotenv\Dotenv;

class Database {
    private static $instance = null;
    private $pdo;
    private static $driver = 'mysql';

    private function __construct() {
        $dotenv = Dotenv::createImmutable(dirname(__DIR__, 2));
        $dotenv->load();

        self::$driver = $_ENV['DB_CONNECTION'] ?? 'mysql';

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            if (self::$driver === 'sqlite') {
                $dbFile = dirname(__DIR__, 2) . '/database.sqlite';
                $isNew = !file_exists($dbFile);
                $dsn = "sqlite:" . $dbFile;
                $this->pdo = new PDO($dsn, null, null, $options);
                
                // Enable foreign keys for SQLite
                $this->pdo->exec('PRAGMA foreign_keys = ON;');
                
                if ($isNew) {
                    require_once __DIR__ . '/Migration.php';
                    Migration::run($this->pdo);
                }
            } else {
                $host = $_ENV['DB_HOST'];
                $db   = $_ENV['DB_NAME'];
                $user = $_ENV['DB_USER'];
                $pass = $_ENV['DB_PASS'];
                $charset = 'utf8mb4';

                $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
                $this->pdo = new PDO($dsn, $user, $pass, $options);
                
                // Sync MySQL timezone with PHP timezone using offset
                $now = new \DateTime();
                $mins = $now->getOffset() / 60;
                $sgn = ($mins < 0 ? -1 : 1);
                $mins = abs($mins);
                $hrs = floor($mins / 60);
                $mins -= $hrs * 60;
                $offset = sprintf('%+d:%02d', $hrs * $sgn, $mins);
                $this->pdo->exec("SET time_zone = '{$offset}'");
            }
        } catch (\PDOException $e) {
            throw new \PDOException($e->getMessage(), (int)$e->getCode());
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance->pdo;
    }

    public static function getDriver() {
        return self::$driver;
    }

    public static function year($column) {
        return self::$driver === 'sqlite' ? "cast(strftime('%Y', $column) as integer)" : "YEAR($column)";
    }

    public static function month($column) {
        return self::$driver === 'sqlite' ? "cast(strftime('%m', $column) as integer)" : "MONTH($column)";
    }

    public function getConnection() {
        return $this->pdo;
    }
}
