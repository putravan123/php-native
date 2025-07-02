<?php
$env = require_once 'env.php';

function getConnection($type = 'sqlite', $env = []) {
    if ($type === 'mysql') {
        $host = $env['DB_HOST'];
        $db   = $env['DB_NAME'];
        $user = $env['DB_USER'];
        $pass = $env['DB_PASS'];
        $char = 'utf8mb4';
        $dsn  = "mysql:host=$host;dbname=$db;charset=$char";

        try {
            $pdo = new PDO($dsn, $user, $pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        } catch (PDOException $e) {
            die("MySQL connection failed: " . $e->getMessage());
        }

    } elseif ($type === 'sqlite') {
        $path = $env['DB_PATH'] ?? 'config/db/database/db.sqlite';

        try {
            if (!file_exists(dirname($path))) {
                mkdir(dirname($path), 0755, true);
            }

            $pdo = new PDO("sqlite:" . $path);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;

        } catch (PDOException $e) {
            die("SQLite connection failed: " . $e->getMessage());
        }
    }

    throw new Exception("Unsupported DB type: $type");
}
