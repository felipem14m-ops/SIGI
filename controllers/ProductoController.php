<?php
/**
 * ============================================================================
 * CONTROLADOR: ProductoController
 * Sistema: SIGI — Gestión de Inventario
 * ============================================================================
 * Patrón Front Controller básico.
 * Responsabilidad: recibir la petición HTTP, validar autorización,
 * sanear entradas, delegar al modelo y redirigir con feedback.
 *
 * Acciones disponibles (parámetro GET ?accion=):
 *   crear      → POST  — registrar nuevo producto
 *   editar     → POST  — actualizar producto existente
 *   desactivar → GET   — cambiar estado a 'inactivo'
 *   activar    → GET   — cambiar estado a 'activo'
 * ============================================================================
 */

session_start();

// ── Autorización ─────────────────────────────────────────────────────────────
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: ../views/Usuario/login.php');
    exit;
}

$rol = strtolower($_SESSION['usuario']['rol'] ?? $_SESSION['rol'] ?? '');
if ($rol !== 'administrador') {
    header('Location: ../views/Dashboard/Empleado.php');
    exit;
}

// ── Dependencias ─────────────────────────────────────────────────────────────
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Producto.php';

$db      = (new Database())->conectar();
$modelo  = new Producto($db);
$accion  = $_GET['accion'] ?? '';

// ── Helpers ───────────────────────────────────────────────────────────────────
function setAlert(string $icon, string $title, string $text): void
{
    $_SESSION['alert'] = compact('icon', 'title', 'text');
}

function redirigir(string $url)
{
    header('Location: ' . $url);
    exit;
}

// ── Enrutador ─────────────────────────────────────────────────────────────────
switch ($accion) {

    // ── CREAR ─────────────────────────────────────────────────────────────────
    case 'crear':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirigir('../views/Dashboard/Adminproductos.php');
        }

        $datos = [
            'codigoUnico'      => trim($_POST['codigoUnico']      ?? ''),
            'nombre'           => trim($_POST['nombre']           ?? ''),
            'descripcion'      => trim($_POST['descripcion']      ?? ''),
            'id_categoria'     => (int) ($_POST['id_categoria']   ?? 0),
            'id_proveedor'     => (int) ($_POST['id_proveedor']   ?? 0) ?: null,
            'precio_venta'     => (float) ($_POST['precio_venta'] ?? 0),
            'precio_costo'     => (float) ($_POST['precio_costo'] ?? 0),
            'stock_minimo'     => (int) ($_POST['stock_minimo']   ?? 0),
            'stock_actual'     => (int) ($_POST['stock_actual']   ?? 0),
            'fechaVencimiento' => trim($_POST['fechaVencimiento'] ?? '') ?: null,
            'estado'           => 'activo',
            'imagen'           => null,
        ];

        // Validaciones básicas
        if (empty($datos['codigoUnico']) || empty($datos['nombre']) || $datos['id_categoria'] === 0) {
            setAlert('warning', 'Campos incompletos', 'Código, nombre y categoría son obligatorios.');
            redirigir('../views/Dashboard/Adminproductos.php');
        }

        if ($datos['precio_venta'] <= 0 || $datos['precio_costo'] <= 0) {
            setAlert('warning', 'Precios inválidos', 'Los precios deben ser mayores a cero.');
            redirigir('../views/Dashboard/Adminproductos.php');
        }

        // Subida de imagen (opcional)
        if (!empty($_FILES['imagen']['name'])) {
            $resultado_img = subirImagen($_FILES['imagen']);
            if ($resultado_img['ok']) {
                $datos['imagen'] = $resultado_img['nombre'];
            } else {
                setAlert('warning', 'Imagen no válida', $resultado_img['error']);
                redirigir('../views/Dashboard/Adminproductos.php');
            }
        }

        $resultado = $modelo->crear($datos);

        if ($resultado === true) {
            setAlert('success', 'Producto creado', 'El producto fue registrado correctamente.');
        } else {
            setAlert('error', 'Error', $resultado);
        }
        redirigir('../views/Dashboard/Adminproductos.php');

    // ── EDITAR ────────────────────────────────────────────────────────────────
    case 'editar':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirigir('../views/Dashboard/Adminproductos.php');
        }

        $id = (int) ($_POST['id_producto'] ?? 0);
        if ($id <= 0) {
            setAlert('error', 'Error', 'ID de producto no válido.');
            redirigir('../views/Dashboard/Adminproductos.php');
        }

        $datos = [
            'codigoUnico'      => trim($_POST['codigoUnico']      ?? ''),
            'nombre'           => trim($_POST['nombre']           ?? ''),
            'descripcion'      => trim($_POST['descripcion']      ?? ''),
            'id_categoria'     => (int) ($_POST['id_categoria']   ?? 0),
            'id_proveedor'     => (int) ($_POST['id_proveedor']   ?? 0) ?: null,
            'precio_venta'     => (float) ($_POST['precio_venta'] ?? 0),
            'precio_costo'     => (float) ($_POST['precio_costo'] ?? 0),
            'stock_minimo'     => (int) ($_POST['stock_minimo']   ?? 0),
            'fechaVencimiento' => trim($_POST['fechaVencimiento'] ?? '') ?: null,
            'estado'           => in_array($_POST['estado'] ?? '', ['activo','inactivo','agotado'])
                                    ? $_POST['estado'] : 'activo',
            'imagen'           => trim($_POST['imagen_actual']    ?? '') ?: null,
        ];

        if (empty($datos['codigoUnico']) || empty($datos['nombre']) || $datos['id_categoria'] === 0) {
            setAlert('warning', 'Campos incompletos', 'Código, nombre y categoría son obligatorios.');
            redirigir('../views/Dashboard/Adminproductos.php');
        }

        // Subida de imagen nueva (opcional — reemplaza la anterior)
        if (!empty($_FILES['imagen']['name'])) {
            $resultado_img = subirImagen($_FILES['imagen']);
            if ($resultado_img['ok']) {
                // Eliminar imagen anterior si existe
                if ($datos['imagen']) {
                    $ruta_anterior = __DIR__ . '/../IMG/productos/' . $datos['imagen'];
                    if (file_exists($ruta_anterior)) @unlink($ruta_anterior);
                }
                $datos['imagen'] = $resultado_img['nombre'];
            } else {
                setAlert('warning', 'Imagen no válida', $resultado_img['error']);
                redirigir('../views/Dashboard/Adminproductos.php');
            }
        }

        $resultado = $modelo->actualizar($id, $datos);

        if ($resultado === true) {
            setAlert('success', 'Producto actualizado', 'Los cambios fueron guardados correctamente.');
        } else {
            setAlert('error', 'Error', $resultado);
        }
        redirigir('../views/Dashboard/Adminproductos.php');

    // ── DESACTIVAR ────────────────────────────────────────────────────────────
    case 'desactivar':
        $id = (int) ($_GET['id'] ?? 0);
        if ($id <= 0) {
            setAlert('error', 'Error', 'ID no válido.');
            redirigir('../views/Dashboard/Adminproductos.php');
        }

        $resultado = $modelo->cambiarEstado($id, 'inactivo');
        if ($resultado === true) {
            setAlert('success', 'Producto desactivado', 'El producto fue desactivado del catálogo.');
        } else {
            setAlert('error', 'Error', $resultado);
        }
        redirigir('../views/Dashboard/Adminproductos.php');

    // ── ACTIVAR ───────────────────────────────────────────────────────────────
    case 'activar':
        $id = (int) ($_GET['id'] ?? 0);
        if ($id <= 0) {
            setAlert('error', 'Error', 'ID no válido.');
            redirigir('../views/Dashboard/Adminproductos.php');
        }

        $resultado = $modelo->cambiarEstado($id, 'activo');
        if ($resultado === true) {
            setAlert('success', 'Producto activado', 'El producto fue reactivado en el catálogo.');
        } else {
            setAlert('error', 'Error', $resultado);
        }
        redirigir('../views/Dashboard/Adminproductos.php');

    // ── DEFAULT ───────────────────────────────────────────────────────────────
    default:
        redirigir('../views/Dashboard/Adminproductos.php');
}

// ── Función auxiliar: subida de imagen ────────────────────────────────────────
function subirImagen(array $file): array
{
    $extensionesPermitidas = ['jpg', 'jpeg', 'png', 'webp'];
    $maxBytes              = 2 * 1024 * 1024; // 2 MB
    $dirDestino            = __DIR__ . '/../IMG/productos/';

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['ok' => false, 'error' => 'Error al subir la imagen (código ' . $file['error'] . ').'];
    }

    if ($file['size'] > $maxBytes) {
        return ['ok' => false, 'error' => 'La imagen no debe superar 2 MB.'];
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $extensionesPermitidas, true)) {
        return ['ok' => false, 'error' => 'Formato no permitido. Use JPG, PNG o WEBP.'];
    }

    // Verificar que sea realmente una imagen (no un archivo disfrazado)
    $info = @getimagesize($file['tmp_name']);
    if ($info === false) {
        return ['ok' => false, 'error' => 'El archivo no es una imagen válida.'];
    }

    if (!is_dir($dirDestino)) {
        mkdir($dirDestino, 0755, true);
    }

    $nombreFinal = 'prod_' . uniqid() . '.' . $ext;
    if (!move_uploaded_file($file['tmp_name'], $dirDestino . $nombreFinal)) {
        return ['ok' => false, 'error' => 'No se pudo guardar la imagen en el servidor.'];
    }

    return ['ok' => true, 'nombre' => $nombreFinal];
}
