<?php

/**
 * ============================================================================
 * CONTROLADOR: AdmiUsuarioController
 * Sistema: SIGI — Gestión de Inventario
 * ============================================================================
 * Responsabilidad: CRUD de usuarios por parte del Administrador.
 * Patrón: Front Controller básico con switch de acciones.
 * ============================================================================
 */

session_start();

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Usuario.php';

// Verificación de acceso: sesión activa y rol administrador
if (
    !isset($_SESSION['logged_in'])
    || $_SESSION['logged_in'] !== true
    || strtolower($_SESSION['usuario']['rol'] ?? '') !== 'administrador'
) {
    header('Location: ../views/Usuario/login.php');
    exit;
}

class AdminUsuarioController
{
    private Usuario $usuarioModel;

    public function __construct()
    {
        $this->usuarioModel = new Usuario((new Database())->conectar());
    }

    // =========================================================================
    // HELPERS PRIVADOS
    // =========================================================================

    /**
     * Almacena una alerta en sesión para SweetAlert (clave 'alert').
     */
    private function setAlert(string $icon, string $title, string $text): void
    {
        $_SESSION['alert'] = compact('icon', 'title', 'text');
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
    // CREAR USUARIO
    // =========================================================================

    public function crear(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir('../views/Dashboard/Adminusuarios.php');
        }

        $nombres              = trim($_POST['nombres']              ?? '');
        $numeroIdentificacion = trim($_POST['numeroIdentificacion'] ?? '');
        $email                = trim($_POST['email']                ?? '');
        $contrasena           = trim($_POST['contrasena']           ?? '');
        $rol                  = trim($_POST['rol']                  ?? '');

        if (empty($nombres) || empty($email) || empty($contrasena) || empty($rol)) {
            $this->setAlert('warning', 'Campos incompletos', 'Debe completar todos los campos obligatorios.');
            $this->redirigir('../views/Dashboard/Adminusuarios.php');
        }

        if ($this->usuarioModel->existeCorreo($email)) {
            $this->setAlert('error', 'Correo existente', 'Este correo ya está registrado en el sistema.');
            $this->redirigir('../views/Dashboard/Adminusuarios.php');
        }

        $datos = [
            'nombres'              => $nombres,
            'numeroIdentificacion' => $numeroIdentificacion,
            'email'                => $email,
            'contrasena'           => password_hash($contrasena, PASSWORD_DEFAULT),
            'rol'                  => $rol,
        ];

        $resultado = $this->usuarioModel->registrar($datos);

        if ($resultado === true) {
            $this->setAlert('success', 'Éxito', 'Usuario creado correctamente.');
        } else {
            $this->setAlert('error', 'Error', is_string($resultado) ? $resultado : 'No se pudo crear el usuario.');
        }

        $this->redirigir('../views/Dashboard/Adminusuarios.php');
    }

    // =========================================================================
    // EDITAR USUARIO
    // =========================================================================

    public function editar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir('../views/Dashboard/Adminusuarios.php');
        }

        $idUsuario = filter_input(INPUT_POST, 'id_usuario', FILTER_VALIDATE_INT);

        if (!$idUsuario) {
            $this->setAlert('error', 'Error', 'ID de usuario no proporcionado o inválido.');
            $this->redirigir('../views/Dashboard/Adminusuarios.php');
        }

        $datos = [
            'nombres'              => trim($_POST['nombres']              ?? ''),
            'numeroIdentificacion' => trim($_POST['numeroIdentificacion'] ?? ''),
            'rol'                  => trim($_POST['rol']                  ?? ''),
        ];

        // Contraseña opcional: solo se actualiza si el admin escribió una nueva
        if (!empty($_POST['contrasena'])) {
            $datos['contrasena'] = password_hash($_POST['contrasena'], PASSWORD_DEFAULT);
        }

        $resultado = $this->usuarioModel->actualizar($idUsuario, $datos);

        if ($resultado === true) {
            $this->setAlert('success', 'Éxito', 'Usuario actualizado correctamente.');
        } else {
            $this->setAlert('error', 'Error', is_string($resultado) ? $resultado : 'No se pudo actualizar el usuario.');
        }

        $this->redirigir('../views/Dashboard/Adminusuarios.php');
    }

    // =========================================================================
    // DESACTIVAR USUARIO
    // =========================================================================

    public function desactivar(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        if ($id <= 0) {
            $this->setAlert('error', 'Error', 'ID no válido.');
            $this->redirigir('../views/Dashboard/Adminusuarios.php');
        }

        $resultado = $this->usuarioModel->cambiarEstado($id, 0);
        if ($resultado === true) {
            $this->setAlert('success', 'Usuario desactivado', 'El usuario fue desactivado correctamente.');
        } else {
            $this->setAlert('error', 'Error', $resultado);
        }
        $this->redirigir('../views/Dashboard/Adminusuarios.php');
    }

    // =========================================================================
    // ACTIVAR USUARIO
    // =========================================================================

    public function activar(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        if ($id <= 0) {
            $this->setAlert('error', 'Error', 'ID no válido.');
            $this->redirigir('../views/Dashboard/Adminusuarios.php');
        }

        $resultado = $this->usuarioModel->cambiarEstado($id, 1);
        if ($resultado === true) {
            $this->setAlert('success', 'Usuario activado', 'El usuario fue reactivado correctamente.');
        } else {
            $this->setAlert('error', 'Error', $resultado);
        }
        $this->redirigir('../views/Dashboard/Adminusuarios.php');
    }
}

// =============================================================================
// ENRUTADOR
// =============================================================================

$controller = new AdminUsuarioController();
$accion     = $_GET['accion'] ?? '';

switch ($accion) {
    case 'crear':
        $controller->crear();
        break;

    case 'editar':
        $controller->editar();
        break;

    case 'desactivar':
        $controller->desactivar();
        break;

    case 'activar':
        $controller->activar();
        break;

    default:
        header('Location: ../views/Dashboard/Adminusuarios.php');
        exit;
}
