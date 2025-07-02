<?php

function up($pdo) { 
    $migrations = [
        "CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            email TEXT UNIQUE NOT NULL,
            password TEXT NOT NULL,
            last_name TEXT,
            created_at TEXT DEFAULT CURRENT_TIMESTAMP
        );"
    ];

    foreach ($migrations as $sql) {
        try {
            $pdo->exec($sql);
            echo "✅ Migrasi berhasil: " . strtok($sql, '(') . "\n";
        } catch (PDOException $e) {
            echo "❌ Migrasi gagal: " . $e->getMessage() . "\n";
        }
    }
}

function down($pdo) {
    try {
        $pdo->exec("DROP TABLE IF EXISTS users;");
        echo "✅ Rollback berhasil: DROP TABLE users\n";
    } catch (PDOException $e) {
        echo "❌ Rollback gagal: " . $e->getMessage() . "\n";
    }
}

?>