<?php
namespace Config;

use PDO;

final class Database {
    private static ?PDO $pdo = null;

    public static function pdo(): PDO {
        if (self::$pdo) return self::$pdo;

        $driver = $_ENV['DB_DRIVER'] ?? 'pgsql';
        $host   = $_ENV['DB_HOST'] ?? '127.0.0.1';
        $port   = $_ENV['DB_PORT'] ?? 5432;
        $db     = $_ENV['DB_NAME'] ?? 'pbl_db';
        $user   = $_ENV['DB_USER'] ?? 'pbl_user';
        $pass   = $_ENV['DB_PASS'] ?? 'pbl_pass';

        if ($driver === 'pgsql') {
            $dsn = "pgsql:host={$host};port={$port};dbname={$db}";
        } else {
            $dsn = "mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4";
        }

        self::$pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

        return self::$pdo;
    }
}
