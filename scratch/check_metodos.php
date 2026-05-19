<?php
require_once 'config/database.php';
try {
    $db = (new Database())->conectar();
    $stmt = $db->query("SELECT * FROM metodos_pago");
    $metodos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($metodos, JSON_PRETTY_PRINT);
} catch (Exception $e) {
    echo $e->getMessage();
}
