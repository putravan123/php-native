<?php

require_once 'config/db/connection.php';
require_once 'env.php';

$config = require 'env.php';
$dbType = $config['DB_TYPE']; 
$pdo = getConnection($config['DB_TYPE'], $config);

// $logPath = 'storage/migrations_log.json';
// if (!file_exists('storage')) mkdir('storage');

$ranMigrations = file_exists($logPath)
    ? json_decode(file_get_contents($logPath), true)
    : [];

$files = glob('config/db/migrations/*.php');
sort($files);

// === MODE REFRESH ===
if (isset($argv[1]) && $argv[1] === 'refresh') {
    echo "🔄 Refreshing database...\n";

    // Drop tables here (edit sesuai tabelmu)
    $tables = ['users']; // tambahkan sesuai migrasi kamu
    foreach ($tables as $table) {
        $pdo->exec("DROP TABLE IF EXISTS $table;");
        echo "🗑️ Dropped table: $table\n";
    }

    // Clear migration log
    file_put_contents($logPath, json_encode([], JSON_PRETTY_PRINT));
    echo "✅ Semua tabel & riwayat migrasi dihapus.\n\n";
    $ranMigrations = []; // Reset migrasi yang dijalankan
}

// === MODE ROLLBACK ===
if (isset($argv[1]) && $argv[1] === 'rollback') {
    echo "↩️ Rollback terakhir...\n";

    if (empty($ranMigrations)) {
        echo "⚠️ Tidak ada migrasi yang dijalankan.\n";
        exit;
    }

    $lastMigration = array_pop($ranMigrations);
    require_once "config/db/migrations/$lastMigration";

    if (function_exists('down')) {
        try {
            down($pdo);
            echo "✅ Rollback sukses: $lastMigration\n";
            file_put_contents($logPath, json_encode($ranMigrations, JSON_PRETTY_PRINT));
        } catch (Exception $e) {
            echo "❌ Rollback gagal: $e->getMessage()\n";
        }
    } else {
        echo "⚠️ Tidak ada fungsi down() di $lastMigration\n";
    }
    exit;
}

// === MODE NORMAL ===
foreach ($files as $file) {
    $filename = basename($file);

    if (in_array($filename, $ranMigrations)) {
        echo "🔁 Skip: $filename (sudah dijalankan)\n";
        continue;
    }

    require_once $file;

    if (function_exists('up')) {
        try {
            up($pdo);
            echo "✅ Migrasi sukses: $filename\n";
            $ranMigrations[] = $filename;
        } catch (Exception $e) {
            echo "❌ Gagal: $filename\n";
            echo $e->getMessage() . "\n";
        }
    } else {
        echo "⚠️ Tidak ada fungsi up() di $filename\n";
    }
}

file_put_contents($logPath, json_encode($ranMigrations, JSON_PRETTY_PRINT));
