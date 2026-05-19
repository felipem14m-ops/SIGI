<?php
/**
 * Script de Actualización de Base de Datos - SIGI
 * Propósito: Añadir columnas de descripción e imagen a la tabla de categorías.
 */

require_once __DIR__ . '/../config/database.php';

try {
    $db = (new Database())->conectar();
    
    echo "Iniciando actualización de la tabla 'categorias'...\n";
    
    // 1. Verificar si las columnas ya existen
    $check = $db->query("SHOW COLUMNS FROM categorias LIKE 'descripcion'");
    if ($check->rowCount() == 0) {
        echo "Añadiendo columna 'descripcion'...\n";
        $db->exec("ALTER TABLE categorias ADD COLUMN descripcion TEXT AFTER nombre");
    }
    
    $check = $db->query("SHOW COLUMNS FROM categorias LIKE 'imagen'");
    if ($check->rowCount() == 0) {
        echo "Añadiendo columna 'imagen'...\n";
        $db->exec("ALTER TABLE categorias ADD COLUMN imagen VARCHAR(255) AFTER descripcion");
    }
    
    echo "¡ACTUALIZACIÓN EXITOSA! Ahora el sistema soporta imágenes en categorías.\n";
    
} catch (Exception $e) {
    echo "ERROR DURANTE LA ACTUALIZACIÓN: " . $e->getMessage() . "\n";
}
