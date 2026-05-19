<?php
/**
 * ============================================================================
 * CONTROLADOR: VentaController
 * Sistema: SIGI — Gestión de Inventario
 * ============================================================================
 * Responsabilidad: Punto de entrada (API) para transacciones comerciales.
 * Maneja el ciclo de vida de la venta, desde la creación hasta la consulta
 * de detalles para facturación.
 * ============================================================================
 */

// Desactivar visualización de errores para evitar corromper la salida JSON
error_reporting(0);
ini_set('display_errors', 0);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

// Verificación estricta de sesión
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    echo json_encode(['ok' => false, 'error' => 'Sesión no iniciada o expirada.']);
    exit;
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Venta.php';

try {
    $db         = (new Database())->conectar();
    $ventaModel = new Venta($db);
    $accion     = $_GET['accion'] ?? '';

    // Manejo de compatibilidad para métodos POST sin parámetro 'accion'
    if (!$accion && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $accion = 'crear';
    }

    switch ($accion) {

        /**
         * REGISTRAR UNA NUEVA VENTA
         * Recibe un JSON con productos, método de pago y totales.
         */
        case 'crear':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Método de solicitud no permitido.');
            }

            $input = json_decode(file_get_contents('php://input'), true);

            if (!$input || empty($input['productos']) || empty($input['id_metodo'])) {
                throw new Exception('Datos incompletos para procesar la venta.');
            }

            $idUsuario = (int) ($_SESSION['usuario']['id_usuario'] ?? 0);
            if (!$idUsuario) {
                throw new Exception('Usuario no identificado en la sesión actual.');
            }

            $datosVenta = [
                'id_usuario'      => $idUsuario,
                'id_metodo'       => (int)   $input['id_metodo'],
                'total'           => (float) $input['total'],
                'monto_recibido'  => isset($input['monto_recibido'])  ? (float) $input['monto_recibido']  : null,
                'cambio_devuelto' => isset($input['cambio_devuelto']) ? (float) $input['cambio_devuelto'] : null,
            ];

            // Ejecutar transacción en el modelo
            $resultado = $ventaModel->procesarVenta($datosVenta, $input['productos']);

            if (is_numeric($resultado)) {
                echo json_encode([
                    'ok'       => true, 
                    'mensaje'  => 'Venta registrada con éxito.', 
                    'id_venta' => $resultado
                ]);
            } else {
                // El modelo devuelve el mensaje de error de la excepción
                throw new Exception($resultado);
            }
            break;

        /**
         * OBTENER DETALLE DE UNA VENTA ESPECÍFICA
         * Utilizado principalmente para la generación de facturas PDF en el cliente.
         */
        case 'detalle':
            $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
            if (!$id) {
                throw new Exception('Identificador de venta no válido.');
            }

            $venta     = $ventaModel->obtenerCabeceraVenta($id);
            $productos = $ventaModel->obtenerDetalleVenta($id);

            if (!$venta) {
                throw new Exception('La venta solicitada no existe en nuestros registros.');
            }

            echo json_encode([
                'ok'        => true,
                'venta'     => $venta,
                'productos' => $productos,
            ]);
            break;

        default:
            throw new Exception('La acción solicitada no es válida.');
    }

} catch (Throwable $e) {
    // Captura cualquier error o excepción y lo devuelve de forma controlada
    error_log("[SIGI][VentaController] Error: " . $e->getMessage());
    echo json_encode([
        'ok'    => false, 
        'error' => 'Error en el servidor: ' . $e->getMessage()
    ]);
}
