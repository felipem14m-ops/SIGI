<?php
/**
 * ============================================================================
 * CONTROLADOR: ConfiguracionController
 * Sistema: SIGI — Gestión de Inventario
 * ============================================================================
 * Responsabilidad: API JSON para gestión de métodos de pago.
 * Solo accesible para administradores con sesión activa.
 * ============================================================================
 */

session_start();
header('Content-Type: application/json; charset=utf-8');

// Verificación de acceso: sesión activa y rol administrador
if (
    !isset($_SESSION['logged_in'])
    || $_SESSION['logged_in'] !== true
    || strtolower($_SESSION['usuario']['rol'] ?? '') !== 'administrador'
) {
    echo json_encode(['ok' => false, 'error' => 'No autorizado.']);
    exit;
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Configuracion.php';

$db     = (new Database())->conectar();
$model  = new Configuracion($db);
$accion = $_GET['accion'] ?? '';

try {
    switch ($accion) {

        case 'guardar_metodo':
            $nombre = trim($_POST['nombre'] ?? '');
            $id     = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT) ?: null;

            if (empty($nombre)) {
                throw new Exception('El nombre del método de pago es obligatorio.');
            }

            if ($model->guardarMetodo($nombre, $id)) {
                echo json_encode(['ok' => true, 'mensaje' => 'Método de pago guardado correctamente.']);
            } else {
                throw new Exception('No se pudo guardar el método de pago.');
            }
            break;

        case 'toggle_metodo':
            $id     = filter_input(INPUT_POST, 'id',     FILTER_VALIDATE_INT);
            $estado = filter_input(INPUT_POST, 'estado', FILTER_VALIDATE_INT);

            if (!$id) {
                throw new Exception('ID de método de pago requerido.');
            }

            if ($model->toggleMetodo($id, (int) $estado)) {
                echo json_encode(['ok' => true, 'mensaje' => 'Estado del método de pago actualizado.']);
            } else {
                throw new Exception('No se pudo actualizar el estado.');
            }
            break;

        case 'inicializar_metodos':
            // Inicializa los métodos de pago predeterminados si la tabla está vacía
            $db->exec("INSERT IGNORE INTO metodos_pago (id_metodo, nombre, activo) VALUES
                       (1, 'Efectivo',       1),
                       (2, 'Transferencia',  1),
                       (3, 'Tarjeta',        1)");
            echo json_encode(['ok' => true, 'mensaje' => 'Métodos de pago predeterminados inicializados.']);
            break;

        default:
            throw new Exception('Acción no reconocida.');
    }

} catch (Exception $e) {
    echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
}
