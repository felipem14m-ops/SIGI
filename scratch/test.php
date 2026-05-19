<?php
require_once 'C:\laragon\www\SIGI\config\database.php';
try {
    $db = (new Database())->conectar();
    $stmt = $db->query('SELECT * FROM tipos_movimiento');
    $tipos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    print_r($tipos);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
