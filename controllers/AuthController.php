<?php
/**
 * ============================================================================
 * CONTROLADOR: AuthController
 * Sistema: SIGI — Gestión de Inventario
 * ============================================================================
 * Responsabilidad: Autenticación de usuarios (login / logout).
 * ============================================================================
 */

session_start();

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Usuario.php';

class AuthController
{
    /**
     * Procesa el inicio de sesión.
     * Solo acepta método POST; cualquier otro acceso redirige al login.
     */
    public function login(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ../views/Usuario/login.php');
            exit;
        }

        $email     = trim($_POST['email']     ?? '');
        $contrasena = trim($_POST['contrasena'] ?? '');

        // Validación: campos vacíos
        if (empty($email) || empty($contrasena)) {
            $_SESSION['alert'] = [
                'icon'  => 'warning',
                'title' => 'Campos incompletos',
                'text'  => 'Debe ingresar correo y contraseña.',
            ];
            header('Location: ../views/Usuario/login.php');
            exit;
        }

        // Validación: formato de correo
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['alert'] = [
                'icon'  => 'error',
                'title' => 'Correo inválido',
                'text'  => 'Ingrese un correo electrónico válido.',
            ];
            header('Location: ../views/Usuario/login.php');
            exit;
        }

        $db          = (new Database())->conectar();
        $usuarioModel = new Usuario($db);
        $usuario      = $usuarioModel->obtenerPorCorreo($email);

        // Verificación: usuario existe
        if (!$usuario) {
            $_SESSION['alert'] = [
                'icon'  => 'error',
                'title' => 'Usuario no encontrado',
                'text'  => 'El correo no está registrado o la cuenta está inactiva.',
            ];
            header('Location: ../views/Usuario/login.php');
            exit;
        }

        // Verificación: contraseña correcta
        if (!password_verify($contrasena, $usuario['contrasena'])) {
            $_SESSION['alert'] = [
                'icon'  => 'error',
                'title' => 'Contraseña incorrecta',
                'text'  => 'Verifique sus credenciales e intente nuevamente.',
            ];
            header('Location: ../views/Usuario/login.php');
            exit;
        }

        // Prevención de Session Fixation: regenerar ID antes de escribir datos
        session_regenerate_id(true);

        $_SESSION['logged_in'] = true;
        $_SESSION['usuario']   = [
            'id_usuario' => $usuario['id_usuario'],
            'nombre'     => $usuario['nombre'],
            'email'      => $usuario['email'],
            'rol'        => strtolower($usuario['nombre_rol']),
        ];

        $usuarioModel->actualizarUltimoAcceso($usuario['id_usuario']);

        // Redirección basada en rol (RBAC)
        switch (strtolower($usuario['nombre_rol'])) {
            case 'administrador':
                header('Location: ../views/Dashboard/Admin.php');
                exit;

            case 'empleado':
                header('Location: ../views/Dashboard/Empleado.php');
                exit;

            default:
                // Rol desconocido: denegar acceso
                session_unset();
                session_destroy();
                $_SESSION['alert'] = [
                    'icon'  => 'error',
                    'title' => 'Rol no válido',
                    'text'  => 'No se pudo determinar el nivel de acceso del usuario.',
                ];
                header('Location: ../views/Usuario/login.php');
                exit;
        }
    }

    /**
     * Cierra la sesión de forma segura y redirige al login.
     */
    public function logout(): void
    {
        session_unset();
        session_destroy();
        header('Location: ../views/Usuario/login.php');
        exit;
    }
}

// =============================================================================
// ENRUTADOR
// =============================================================================

$controller = new AuthController();
$accion     = $_GET['accion'] ?? 'login';

if ($accion === 'logout') {
    $controller->logout();
} else {
    $controller->login();
}