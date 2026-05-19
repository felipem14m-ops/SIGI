<?php
require_once __DIR__ . '/../config/database.php';

try {
    $db = (new Database())->conectar();
    
    // 1. Verificar si hay métodos
    $stmt = $db->query("SELECT COUNT(*) FROM metodos_pago");
    $count = $stmt->fetchColumn();
    
    if ($count == 0) {
        echo "Insertando métodos de pago por defecto...<br>";
        $sql = "INSERT INTO metodos_pago (id_metodo, nombre, activo) VALUES 
                (1, 'Efectivo', 1),
                (2, 'Transferencia', 1),
                (3, 'Tarjeta', 1)";
        $db->exec($sql);
        echo "¡Éxito! Métodos básicos creados.<br>";
    } else {
        echo "La tabla ya tiene $count métodos registrados.<br>";
        // Listarlos para saber cuáles son
        $res = $db->query("SELECT * FROM metodos_pago");
        while($row = $res->fetch(PDO::FETCH_ASSOC)) {
            echo "- ID: {$row['id_metodo']} | Nombre: {$row['nombre']}<br>";
        }
    }
    
    echo "<br><b>Ahora intenta realizar la venta de nuevo.</b>";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
