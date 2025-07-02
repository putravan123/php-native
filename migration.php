<?php

require_once 'config/db/connection.php';

$config = require_once '.env';
$dbType = $config['DB_TYPE']; 
$pdo = getConnection($dbType);

$logPath = 'storage/migrations_log.json';
if (!file_exists('storage')) mkdir('storage');

$ranMigrations = file_exists($logPath)
    ? json_decode(file_get_contents($logPath), true)
    : [];

$files = glob('config/db/migrations/*.php');
sort($files);

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
