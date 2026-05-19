<?php
/**
 * ============================================================================
 * CONTROLADOR: ReporteControllerSimple
 * Sistema: SIGI — Gestión de Inventario
 * ============================================================================
 * Responsabilidad: Manejar la lógica de obtención de datos para reportes y
 * gestión del historial de archivos generados.
 * ============================================================================
 */

// Desactivar visualización de errores para no corromper las respuestas JSON o descargas
error_reporting(0);
ini_set('display_errors', 0);

// Iniciar buffering para capturar cualquier salida accidental
ob_start();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Validar sesión
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    ob_end_clean();
    header('Content-Type: application/json');
    echo json_encode(['ok' => false, 'error' => 'Sesión no válida']);
    exit;
}


require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Reporte.php';

// Cargar dependencias adicionales si existen
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}

$db = (new Database())->conectar();
$reporteModel = new Reporte($db);

$accion = $_GET['accion'] ?? '';

switch ($accion) {
    // Obtener historial de reportes (AJAX para la tabla)
    case 'obtener_historial':
        obtenerHistorial($reporteModel);
        break;
        
    // Descargar un archivo PDF generado previamente del servidor
    case 'descargar_reporte':
        descargarReporte($reporteModel);
        break;
        
    // Eliminar un registro del historial y su archivo físico
    case 'eliminar_reporte':
        eliminarReporte($reporteModel);
        break;

    // Guardar un reporte generado en el cliente hacia el servidor y BD
    case 'guardar_reporte':
        guardarReporteServidor($reporteModel);
        break;

    // Endpoint principal: Obtener datos brutos para el generador PDF Golden Standard (JS)
    case 'obtener_datos_inventario':
        obtenerDatosInventario($reporteModel);
        break;
        
    default:
        ob_end_clean();
        header('Content-Type: application/json');
        echo json_encode(['ok' => false, 'error' => 'Acción no válida']);
        break;
}

/**
 * Obtener datos brutos para generación de PDF en cliente (Golden Standard)
 */
function obtenerDatosInventario($reporte) {
    if (ob_get_level()) ob_end_clean();
    header('Content-Type: application/json; charset=utf-8');
    
    try {
        $filtros = [
            'id_categoria' => !empty($_POST['id_categoria']) ? (int)$_POST['id_categoria'] : null,
            'estado'       => !empty($_POST['estado'])       ? $_POST['estado']            : null,
            'fecha_desde'  => !empty($_POST['fecha_desde'])  ? $_POST['fecha_desde']       : null,
            'fecha_hasta'  => !empty($_POST['fecha_hasta'])  ? $_POST['fecha_hasta']       : null,
            'stock_bajo'   => (isset($_POST['stock_bajo']) || isset($_GET['stock_bajo'])) ? true : false
        ];

        $productos = $reporte->obtenerInventarioFiltrado($filtros);
        
        echo json_encode([
            'ok'      => true,
            'data'    => $productos,
            'filtros' => $filtros,
            'fecha'   => date('d/m/Y H:i:s'),
            'usuario' => $_SESSION['usuario']['nombre'] ?? 'Administrador'
        ], JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR);
        
    } catch (Exception $e) {
        echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
    }
    exit;
}

/**
 * Listar el historial de reportes generados
 */
function obtenerHistorial($reporte) {
    if (ob_get_level()) ob_end_clean();
    
    $page   = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit  = 10;
    $offset = ($page - 1) * $limit;
    $idUsuario = (int) ($_SESSION['usuario']['id_usuario'] ?? 0);

    $reportes   = $reporte->obtenerHistorialReportes($idUsuario, $limit, $offset);
    $total      = $reporte->contarReportes($idUsuario);
    $totalPages = (int) ceil($total / $limit);

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'ok'         => true,
        'reportes'   => $reportes,
        'pagination' => [
            'current_page' => $page,
            'total_pages'  => $totalPages,
            'total_items'  => $total,
            'per_page'     => $limit
        ]
    ]);
    exit;
}

/**
 * Forzar la descarga de un archivo desde el servidor
 */
function descargarReporte($reporte) {
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if (!$id) die('ID inválido');

    $idUsuario = (int) ($_SESSION['usuario']['id_usuario'] ?? 0);
    $reportes = $reporte->obtenerHistorialReportes($idUsuario, 1000, 0);
    $encontrado = null;

    foreach ($reportes as $r) {
        if ((int)$r['id_reporte'] === $id) {
            $encontrado = $r;
            break;
        }
    }

    if (!$encontrado || !file_exists($encontrado['ruta_archivo'])) {
        die('Archivo no encontrado');
    }

    if (ob_get_level()) ob_end_clean();

    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . $encontrado['nombre_archivo'] . '"');
    header('Content-Length: ' . filesize($encontrado['ruta_archivo']));
    readfile($encontrado['ruta_archivo']);
    exit;
}

/**
 * Eliminar un reporte del historial
 */
function eliminarReporte($reporte) {
    if (ob_get_level()) ob_end_clean();
    header('Content-Type: application/json');
    
    $id = $_POST['id_reporte'] ?? null;
    if (!$id) {
        echo json_encode(['ok' => false, 'error' => 'ID no proporcionado']);
        exit;
    }
    
    if ($reporte->eliminarReporte($id)) {
        echo json_encode(['ok' => true, 'mensaje' => 'Registro eliminado']);
    } else {
        echo json_encode(['ok' => false, 'error' => 'Error al eliminar el registro']);
    }
    exit;
}

/**
 * Guarda un reporte generado en el cliente en la BD
 */
function guardarReporteServidor($reporte) {
    if (ob_get_level()) ob_end_clean();
    header('Content-Type: application/json');

    $rol_usuario = $_SESSION['usuario']['rol'] ?? 'Administrador';
    $tipo_reporte = $_POST['tipo_reporte'] ?? 'Reporte General';
    
    if (!isset($_FILES['archivo_pdf']) || $_FILES['archivo_pdf']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['ok' => false, 'error' => 'No se recibió el archivo PDF correctamente']);
        exit;
    }

    $file = $_FILES['archivo_pdf'];
    $nombreOriginal = preg_replace('/[^a-zA-Z0-9_\-\.]/', '', basename($file['name']));
    $nombreUnico = time() . '_' . $nombreOriginal;

    // Guardar el archivo físico para poder previsualizarlo y descargarlo después
    $uploadDir = __DIR__ . '/../../public/uploads/reportes/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    $rutaFisica = $uploadDir . $nombreUnico;

    if (move_uploaded_file($file['tmp_name'], $rutaFisica)) {
        // Registrar en BD con el esquema nuevo
        if ($reporte->guardarReporteGenerado($tipo_reporte, $nombreUnico, $rol_usuario)) {
            echo json_encode(['ok' => true, 'mensaje' => 'Reporte guardado en el historial']);
        } else {
            unlink($rutaFisica);
            echo json_encode(['ok' => false, 'error' => 'Error al guardar el registro en la base de datos']);
        }
    } else {
        echo json_encode(['ok' => false, 'error' => 'Error al mover el archivo subido al directorio']);
    }
    exit;
}
