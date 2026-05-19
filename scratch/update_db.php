<?php
require_once __DIR__ . '/../config/database.php';

try {
    $db = (new Database())->conectar();
    
    $sql = "ALTER TABLE categorias 
            ADD COLUMN descripcion TEXT DEFAULT NULL AFTER nombre,
            ADD COLUMN imagen VARCHAR(255) DEFAULT NULL AFTER descripcion";
            
    $db->exec($sql);
    echo "Base de datos actualizada correctamente.\n";
    
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
        echo "Las columnas ya existen en la base de datos.\n";
    } else {
        echo "Error al actualizar la base de datos: " . $e->getMessage() . "\n";
    }
}
