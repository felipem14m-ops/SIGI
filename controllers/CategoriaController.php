<?php
/**
 * ============================================================================
 * CONTROLADOR: CategoriaController
 * Sistema: SIGI — Gestión de Inventario
 * ============================================================================
 * Responsabilidad: CRUD de categorías con subida de imagen.
 * Patrón: Front Controller básico con switch de acciones.
 * ============================================================================
 */

session_start();

// Verificación de acceso: sesión activa y rol administrador
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: ../views/Usuario/login.php');
    exit;
}

$rol = strtolower($_SESSION['usuario']['rol'] ?? '');
if ($rol !== 'administrador') {
    header('Location: ../views/Dashboard/Empleado.php');
    exit;
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Categoria.php';

$db     = (new Database())->conectar();
$modelo = new Categoria($db);
$accion = $_GET['accion'] ?? '';

// =============================================================================
// HELPERS (funciones anidadas en namespace de archivo, no globales)
// Se declaran como closures para evitar colisiones si el archivo es incluido.
// =============================================================================

$setAlert = static function (string $icon, string $title, string $text): void {
    $_SESSION['alert'] = compact('icon', 'title', 'text');
};

$redirigir = static function (string $url): never {
    header('Location: ' . $url);
    exit;
};

$subirImagen = static function (array $file): array {
    $extensionesPermitidas = ['jpg', 'jpeg', 'png', 'webp'];
    $maxBytes              = 2 * 1024 * 1024; // 2 MB
    $dirDestino            = __DIR__ . '/../IMG/categorias/';

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['ok' => false, 'error' => 'Error al subir la imagen.'];
    }

    if ($file['size'] > $maxBytes) {
        return ['ok' => false, 'error' => 'La imagen no debe superar 2 MB.'];
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $extensionesPermitidas, true)) {
        return ['ok' => false, 'error' => 'Formato no permitido. Use JPG, PNG o WEBP.'];
    }

    if (!is_dir($dirDestino)) {
        mkdir($dirDestino, 0755, true);
    }

    $nombreFinal = 'cat_' . uniqid() . '.' . $ext;
    if (!move_uploaded_file($file['tmp_name'], $dirDestino . $nombreFinal)) {
        return ['ok' => false, 'error' => 'No se pudo guardar la imagen en el servidor.'];
    }

    return ['ok' => true, 'nombre' => $nombreFinal];
};

// =============================================================================
// ENRUTADOR
// =============================================================================

switch ($accion) {

    case 'crear':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $redirigir('../views/Dashboard/Admincategorias.php');
        }

        $datos = [
            'nombre'      => trim($_POST['nombre']      ?? ''),
            'descripcion' => trim($_POST['descripcion'] ?? ''),
            'imagen'      => null,
        ];

        if (empty($datos['nombre'])) {
            $setAlert('warning', 'Campos incompletos', 'El nombre de la categoría es obligatorio.');
            $redirigir('../views/Dashboard/Admincategorias.php');
        }

        if (!empty($_FILES['imagen']['name'])) {
            $resultadoImg = $subirImagen($_FILES['imagen']);
            if ($resultadoImg['ok']) {
                $datos['imagen'] = $resultadoImg['nombre'];
            } else {
                $setAlert('warning', 'Imagen no válida', $resultadoImg['error']);
                $redirigir('../views/Dashboard/Admincategorias.php');
            }
        }

        $resultado = $modelo->crear($datos);
        if ($resultado === true) {
            $setAlert('success', 'Categoría creada', 'La categoría fue registrada correctamente.');
        } else {
            $setAlert('error', 'Error', is_string($resultado) ? $resultado : 'No se pudo crear la categoría.');
        }
        $redirigir('../views/Dashboard/Admincategorias.php');
        break;

    case 'editar':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $redirigir('../views/Dashboard/Admincategorias.php');
        }

        $id = (int) ($_POST['id_categoria'] ?? 0);
        if ($id <= 0) {
            $setAlert('error', 'Error', 'ID de categoría no válido.');
            $redirigir('../views/Dashboard/Admincategorias.php');
        }

        $datos = [
            'nombre'      => trim($_POST['nombre']        ?? ''),
            'descripcion' => trim($_POST['descripcion']   ?? ''),
            'imagen'      => trim($_POST['imagen_actual'] ?? '') ?: null,
        ];

        if (empty($datos['nombre'])) {
            $setAlert('warning', 'Campos incompletos', 'El nombre es obligatorio.');
            $redirigir('../views/Dashboard/Admincategorias.php');
        }

        if (!empty($_FILES['imagen']['name'])) {
            $resultadoImg = $subirImagen($_FILES['imagen']);
            if ($resultadoImg['ok']) {
                // Eliminar imagen anterior si existe
                if ($datos['imagen']) {
                    $rutaAnterior = __DIR__ . '/../IMG/categorias/' . $datos['imagen'];
                    if (file_exists($rutaAnterior)) {
                        @unlink($rutaAnterior);
                    }
                }
                $datos['imagen'] = $resultadoImg['nombre'];
            } else {
                $setAlert('warning', 'Imagen no válida', $resultadoImg['error']);
                $redirigir('../views/Dashboard/Admincategorias.php');
            }
        }

        $resultado = $modelo->actualizar($id, $datos);
        if ($resultado === true) {
            $setAlert('success', 'Categoría actualizada', 'Los cambios fueron guardados correctamente.');
        } else {
            $setAlert('error', 'Error', is_string($resultado) ? $resultado : 'No se pudo actualizar la categoría.');
        }
        $redirigir('../views/Dashboard/Admincategorias.php');
        break;

    case 'desactivar':
        $id = (int) ($_GET['id'] ?? 0);
        if ($id <= 0) {
            $setAlert('error', 'Error', 'ID de categoría no válido.');
            $redirigir('../views/Dashboard/Admincategorias.php');
        }

        if ($modelo->tieneProductos($id)) {
            $setAlert('error', 'Operación no permitida', 'No se puede desactivar una categoría que tiene productos activos asociados.');
            $redirigir('../views/Dashboard/Admincategorias.php');
        }

        $resultado = $modelo->cambiarEstado($id, 0);
        if ($resultado === true) {
            $setAlert('success', 'Categoría desactivada', 'La categoría fue ocultada del catálogo.');
        } else {
            $setAlert('error', 'Error', is_string($resultado) ? $resultado : 'No se pudo desactivar la categoría.');
        }
        $redirigir('../views/Dashboard/Admincategorias.php');
        break;

    case 'activar':
        $id = (int) ($_GET['id'] ?? 0);
        if ($id <= 0) {
            $setAlert('error', 'Error', 'ID de categoría no válido.');
            $redirigir('../views/Dashboard/Admincategorias.php');
        }

        $resultado = $modelo->cambiarEstado($id, 1);
        if ($resultado === true) {
            $setAlert('success', 'Categoría activada', 'La categoría fue reactivada correctamente.');
        } else {
            $setAlert('error', 'Error', is_string($resultado) ? $resultado : 'No se pudo activar la categoría.');
        }
        $redirigir('../views/Dashboard/Admincategorias.php');
        break;

    default:
        $redirigir('../views/Dashboard/Admincategorias.php');
}
