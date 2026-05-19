<?php
/**
 * ============================================================================
 * CONTROLADOR: ProveedorController
 * Sistema: SIGI — Gestión de Inventario
 * ============================================================================
 * Responsabilidad: Gestión CRUD de proveedores con validación y autorización.
 * Patrón: Front Controller básico con switch de acciones.
 * ============================================================================
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Proveedor.php';

class ProveedorController
{
    private PDO    $db;
    private Proveedor $proveedorModel;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Verificación de sesión activa y rol administrador
        // Usa la estructura correcta: $_SESSION['usuario']['rol']
        if (
            !isset($_SESSION['logged_in'])
            || $_SESSION['logged_in'] !== true
            || strtolower($_SESSION['usuario']['rol'] ?? '') !== 'administrador'
        ) {
            header('Location: ../views/Usuario/login.php');
            exit;
        }

        $this->db             = (new Database())->conectar();
        $this->proveedorModel = new Proveedor($this->db);
    }

    // =========================================================================
    // HELPERS PRIVADOS
    // =========================================================================

    /**
     * Almacena una alerta en sesión para que el footer la muestre con SweetAlert.
     * Usa la clave 'alert' (consistente con el resto del sistema).
     */
    private function setAlert(string $icon, string $title, string $text): void
    {
        $_SESSION['alert'] = [
            'icon'  => $icon,
            'title' => $title,
            'text'  => $text,
        ];
    }

    /**
     * Redirige y termina la ejecución.
     */
    private function redirigir(string $url): never
    {
        header('Location: ' . $url);
        exit;
    }

    // =========================================================================
    // CREAR PROVEEDOR
    // =========================================================================

    public function crear(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir('../views/Dashboard/Adminproveedores.php');
        }

        $datos = [
            'nombre'   => trim($_POST['nombre']   ?? ''),
            'empresa'  => trim($_POST['empresa']  ?? ''),
            'telefono' => trim($_POST['telefono'] ?? ''),
            'email'    => trim($_POST['email']    ?? ''),
        ];

        if (empty($datos['nombre'])) {
            $this->setAlert('warning', 'Campos incompletos', 'El nombre del proveedor es obligatorio.');
            $this->redirigir('../views/Dashboard/Adminproveedores.php');
        }

        $resultado = $this->proveedorModel->crear($datos);

        if ($resultado === true) {
            $this->setAlert('success', 'Éxito', 'Proveedor registrado correctamente.');
        } else {
            $this->setAlert('error', 'Error', is_string($resultado) ? $resultado : 'No se pudo crear el proveedor.');
        }

        $this->redirigir('../views/Dashboard/Adminproveedores.php');
    }

    // =========================================================================
    // EDITAR PROVEEDOR
    // =========================================================================

    public function editar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir('../views/Dashboard/Adminproveedores.php');
        }

        $idProveedor = filter_input(INPUT_POST, 'id_proveedor', FILTER_VALIDATE_INT);

        if (!$idProveedor) {
            $this->setAlert('error', 'Error', 'ID de proveedor inválido.');
            $this->redirigir('../views/Dashboard/Adminproveedores.php');
        }

        $datos = [
            'nombre'   => trim($_POST['nombre']   ?? ''),
            'empresa'  => trim($_POST['empresa']  ?? ''),
            'telefono' => trim($_POST['telefono'] ?? ''),
            'email'    => trim($_POST['email']    ?? ''),
        ];

        if (empty($datos['nombre'])) {
            $this->setAlert('warning', 'Campos incompletos', 'El nombre del proveedor es obligatorio.');
            $this->redirigir('../views/Dashboard/Adminproveedores.php');
        }

        $resultado = $this->proveedorModel->actualizar($idProveedor, $datos);

        if ($resultado === true) {
            $this->setAlert('success', 'Actualizado', 'Información del proveedor actualizada correctamente.');
        } else {
            $this->setAlert('error', 'Error', is_string($resultado) ? $resultado : 'No se pudo actualizar el proveedor.');
        }

        $this->redirigir('../views/Dashboard/Adminproveedores.php');
    }

    // =========================================================================
    // CAMBIAR ESTADO (Activar / Desactivar)
    // =========================================================================

    public function toggleEstado(): void
    {
        $id           = filter_input(INPUT_GET, 'id',     FILTER_VALIDATE_INT);
        $estadoActual = filter_input(INPUT_GET, 'estado', FILTER_VALIDATE_INT);

        if ($id === false || $id === null || $estadoActual === false || $estadoActual === null) {
            $this->setAlert('error', 'Error', 'Parámetros inválidos.');
            $this->redirigir('../views/Dashboard/Adminproveedores.php');
        }

        $nuevoEstado = ($estadoActual == 1) ? 0 : 1;
        $resultado   = $this->proveedorModel->cambiarEstado($id, $nuevoEstado);

        if ($resultado === true) {
            $accionMsg = ($nuevoEstado === 1) ? 'activado' : 'desactivado';
            $this->setAlert('success', 'Estado actualizado', "El proveedor ha sido {$accionMsg}.");
        } else {
            $this->setAlert('error', 'Error', is_string($resultado) ? $resultado : 'No se pudo cambiar el estado.');
        }

        $this->redirigir('../views/Dashboard/Adminproveedores.php');
    }

    // =========================================================================
    // OBTENER PRODUCTOS (AJAX)
    // =========================================================================

    public function obtenerProductos(): void
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            header('Content-Type: application/json');
            echo json_encode(['ok' => false, 'error' => 'ID inválido']);
            exit;
        }

        $productos = $this->proveedorModel->obtenerProductosActivos($id);

        header('Content-Type: application/json');
        echo json_encode(['ok' => true, 'productos' => $productos]);
        exit;
    }
}

// =============================================================================
// ENRUTADOR (Front Controller básico)
// =============================================================================

$accion = $_GET['accion'] ?? '';

if ($accion !== '') {
    $controller = new ProveedorController();

    switch ($accion) {
        case 'crear':
            $controller->crear();
            break;

        case 'editar':
            $controller->editar();
            break;

        case 'toggleEstado':
            $controller->toggleEstado();
            break;

        case 'obtenerProductos':
        case 'listarProductos':
            $controller->obtenerProductos();
            break;

        default:
            header('Location: ../views/Dashboard/Adminproveedores.php');
            exit;
    }
}
