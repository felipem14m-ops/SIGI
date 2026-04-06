<?php

/* ======================================================
INICIO DE SESIÓN PHP
   ====================================================== */
session_start();


/* ======================================================
CARGA DE RUTAS Y ARCHIVOS NECESARIOS
   ====================================================== */
$rootPath = dirname(__DIR__);
require_once $rootPath . '/config/database.php';
require_once $rootPath . '/models/Usuario.php';


/* ======================================================
CONEXIÓN A BASE DE DATOS Y MODELO USUARIO
   ====================================================== */
$database = new Database();
$db = $database->conectar();
$usuario = new Usuario($db);


/* ======================================================
CAPTURA DE ACCIÓN DESDE FORMULARIO (POST / GET)
   ====================================================== */
$accion = $_POST['accion'] ?? $_GET['accion'] ?? '';


/* ======================================================
CONTROLADOR PRINCIPAL DE ACCIONES
   ====================================================== */
switch ($accion) {

    case 'login':
        login($usuario);
        break;

    case 'registro':
        registro($usuario);
        break;

    case 'logout':
        logout();
        break;

    default:
        redirigirConError('../views/usuarios/login.php', 'Acción no válida.');
        break;
}

/* ======================================================
FUNCIÓN LOGIN (INICIO DE SESIÓN)
   ====================================================== */
function login($usuario)
{
    /* Captura de datos */
    $email = trim($_POST['email'] ?? '');
    $contrasena = trim($_POST['contrasena'] ?? '');

    /* Validaciones básicas */
    if (empty($email) || empty($contrasena)) {
        redirigirConError('../views/usuarios/login.php', 'Completa todos los campos.');
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        redirigirConError('../views/usuarios/login.php', 'El correo no es válido.');
    }
    /* Verificar credenciales */
    $user = $usuario->verificarCredenciales($email, $contrasena);
    if (!$user) {
        redirigirConError('../views/usuarios/login.php', 'Correo o contraseña incorrectos.');
    }

    /* Creación de variables de sesión */
    $_SESSION['id_usuario'] = $user['id_usuario'];
    $_SESSION['nombre'] = $user['nombre'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['rol'] = $user['nombre_rol'];
    $_SESSION['logged_in'] = true;
    /* Actualización último acceso */
    $usuario->actualizarUltimoAcceso($user['id_usuario']);

    /* Redirección al dashboard */
    header('Location: ../views/dashboard.php');
    exit;
}

/* ======================================================
FUNCIÓN REGISTRO DE USUARIO
   ====================================================== */
function registro($usuario)
{
    /* Captura de datos */
    $nombre = trim($_POST['nombre'] ?? '');
    $numeroIdentificacion = trim($_POST['numeroIdentificacion'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $contrasena = trim($_POST['contrasena'] ?? '');
    $confirmar = trim($_POST['confirmar'] ?? '');
    $terminos = isset($_POST['terminos']) ? true : false;

    /* Validaciones del formulario */
    if (
        empty($nombre) ||
        empty($numeroIdentificacion) ||
        empty($email) ||
        empty($contrasena) ||
        empty($confirmar)
    ) {
        redirigirConError('../views/usuarios/registro.php', 'Completa todos los campos.');
    }

    if (!$terminos) {
        redirigirConError('../views/usuarios/registro.php', 'Debes aceptar los términos y condiciones.');
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        redirigirConError('../views/usuarios/registro.php', 'El correo no es válido.');
    }

    if ($contrasena !== $confirmar) {
        redirigirConError('../views/usuarios/registro.php', 'Las contraseñas no coinciden.');
    }

    if (strlen($contrasena) < 8) {
        redirigirConError('../views/usuarios/registro.php', 'La contraseña debe tener al menos 8 caracteres.');
    }

    /* Validación de correo existente */
    if ($usuario->existeCorreo($email)) {
        redirigirConError('../views/usuarios/registro.php', 'Ya existe una cuenta con ese correo.');
    }

    /* Validación de identificación existente */
    if ($usuario->existeIdentificacion($numeroIdentificacion)) {
        redirigirConError('../views/usuarios/registro.php', 'Ya existe una cuenta con ese número de identificación.');
    }

    /* Preparación de datos */
    $datos = [
        'id_rol' => 2,  // 2 = Vendedor/Empleado (por defecto)
        'nombre' => $nombre,
        'numeroIdentificacion' => $numeroIdentificacion,
        'email' => $email,
        'contrasena' => $contrasena
    ];

    /* Registro en base de datos */
    $resultado = $usuario->registrar($datos);

    /* Resultado del registro */
    if ($resultado === true) {
        $_SESSION['exito'] = '¡Cuenta creada exitosamente! Ya puedes iniciar sesión.';
        header('Location: ../views/usuarios/login.php');
        exit;
    }
    redirigirConError('../views/usuarios/registro.php', 'Error al guardar: Intente nuevamente.');
}

/* ======================================================
FUNCIÓN CIERRE DE SESIÓN
   ====================================================== */
function logout()
{
    session_unset();
    session_destroy();
    header('Location: ../views/usuarios/login.php');
    exit;
}

/* ======================================================
FUNCIÓN DE REDIRECCIÓN CON MENSAJE DE ERROR
   ====================================================== */
function redirigirConError($url, $mensaje)
{
    $_SESSION['error'] = $mensaje;
    header('Location: ' . $url);
    exit;
}

?>