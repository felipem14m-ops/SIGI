<?php
/**
 * ============================================================================
 * CONTROLADOR: InventarioController
 * Sistema: SIGI — Gestión de Inventario
 * ============================================================================
 * Responsabilidad: API JSON para registrar movimientos de inventario.
 * Accesible para cualquier usuario con sesión activa.
 * ============================================================================
 */

session_start();
header('Content-Type: application/json; charset=utf-8');

// Verificación de sesión activa
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    echo json_encode(['ok' => false, 'error' => 'No autorizado.']);
    exit;
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Inventario.php';

$db     = (new Database())->conectar();
$model  = new Inventario($db);
$accion = $_GET['accion'] ?? '';

try {
    switch ($accion) {

        case 'registrar_movimiento':
            $idProducto = filter_input(INPUT_POST, 'id_producto', FILTER_VALIDATE_INT);
            $idTipo     = filter_input(INPUT_POST, 'id_tipo',     FILTER_VALIDATE_INT);
            $cantidad   = (int) ($_POST['cantidad'] ?? 0);
            $motivo     = trim($_POST['motivo'] ?? '');
            $idUsuario  = (int) ($_SESSION['usuario']['id_usuario'] ?? 0);

            if (!$idProducto || !$idTipo || $cantidad === 0) {
                throw new Exception('Datos incompletos: se requiere producto, tipo y cantidad.');
            }

            if (!$idUsuario) {
                throw new Exception('No se pudo identificar al usuario de la sesión.');
            }

            $resultado = $model->registrarMovimiento($idProducto, $idUsuario, $idTipo, $cantidad, $motivo);

            if ($resultado === true) {
                echo json_encode(['ok' => true, 'mensaje' => 'Movimiento registrado correctamente.']);
            } else {
                throw new Exception(is_string($resultado) ? $resultado : 'Error al registrar el movimiento.');
            }
            break;

        default:
            throw new Exception('Acción no permitida.');
    }

} catch (Exception $e) {
    echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
}
