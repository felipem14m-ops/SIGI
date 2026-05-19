<?php
require_once __DIR__ . '/../config/database.php';

function testConnection($db_name) {
    echo "Testing connection with database name: '$db_name'...\n";
    try {
        $dsn = "mysql:host=127.0.0.1;port=3306;dbname=$db_name;charset=utf8mb4";
        $pdo = new PDO($dsn, "root", "", [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
        echo "SUCCESS: Connected to '$db_name'!\n";
        return true;
    } catch (PDOException $e) {
        echo "FAILED: " . $e->getMessage() . "\n";
        return false;
    }
}

testConnection("prototipo_clases.");
testConnection("prototipo_clases");
