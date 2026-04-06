<?php
session_start();
require_once '../../Api/database.php';
require_once '../../models/Usuario.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email      = trim($_POST['email'] ?? '');
    $contrasena = $_POST['contrasena'] ?? '';

    if (empty($email) || empty($contrasena)) {
        $error = 'Correo y contraseña son obligatorios';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'El correo electrónico no es válido';
    } else {
        try {
            $database = new Database();
            $conn = $database->conectar();
            $usuario = new Usuario($conn);

            // Verificar credenciales
            $user = $usuario->verificarCredenciales($email, $contrasena);

            if ($user) {
                // Crear sesión
                $_SESSION['id_usuario'] = $user['id_usuario'];
                $_SESSION['nombre']     = $user['nombre'];
                $_SESSION['email']      = $user['email'];
                $_SESSION['rol']        = $user['nombre_rol'];
                $_SESSION['logged_in']  = true;

                // Actualizar último acceso
                $usuario->actualizarUltimoAcceso($user['id_usuario']);

                header("Location: dashboard.php");
                exit;
            } else {
                $error = 'Correo o contraseña incorrectos, o cuenta desactivada';
            }
        } catch (PDOException $e) {
            $error = 'Error en el sistema. Intente más tarde: ' . $e->getMessage();
        }
    }
}

// Mostrar mensaje de éxito si viene del registro
$exito = '';
if (isset($_SESSION['exito'])) {
    $exito = $_SESSION['exito'];
    unset($_SESSION['exito']);
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - La Esquina</title>
    <link rel="stylesheet" href="../../CSS/global.css?v=<?php echo time() . '_' . rand(1000, 9999); ?>">
    <link rel="stylesheet" href="../../CSS/components.css?v=<?php echo time() . '_' . rand(1000, 9999); ?>">
</head>

<body class="pagina-acceso">

    <div class="caja-acceso">
        <div class="tarjeta-acceso">

            <!-- ==========================================
             PANEL IZQUIERDO: icono y descripción
        =========================================== -->
            <div class="panel-izquierdo">

                <!-- Icono SVG profesional de inventario -->
                <div class="circulo-icono">
                    <svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <!-- Caja principal -->
                        <rect x="12" y="24" width="40" height="28" rx="3" fill="white" opacity="0.95"/>
                        <!-- Tapa de la caja -->
                        <path d="M10 24 L32 16 L54 24" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" fill="none" opacity="0.9"/>
                        <!-- Línea central de la tapa -->
                        <line x1="32" y1="16" x2="32" y2="24" stroke="white" stroke-width="2" opacity="0.7"/>
                        <!-- Franja de identificación -->
                        <rect x="12" y="32" width="40" height="4" fill="#2563eb" opacity="0.6"/>
                        <!-- Código de barras simplificado -->
                        <rect x="18" y="38" width="2" height="8" fill="#2563eb" opacity="0.8"/>
                        <rect x="22" y="38" width="1" height="8" fill="#2563eb" opacity="0.8"/>
                        <rect x="25" y="38" width="2" height="8" fill="#2563eb" opacity="0.8"/>
                        <rect x="29" y="38" width="1" height="8" fill="#2563eb" opacity="0.8"/>
                        <rect x="32" y="38" width="2" height="8" fill="#2563eb" opacity="0.8"/>
                        <rect x="36" y="38" width="1" height="8" fill="#2563eb" opacity="0.8"/>
                        <rect x="39" y="38" width="2" height="8" fill="#2563eb" opacity="0.8"/>
                        <rect x="43" y="38" width="1" height="8" fill="#2563eb" opacity="0.8"/>
                        <!-- Puntos de inventario -->
                        <circle cx="20" cy="44" r="1.5" fill="#2563eb" opacity="0.9"/>
                        <circle cx="32" cy="44" r="1.5" fill="#2563eb" opacity="0.9"/>
                        <circle cx="44" cy="44" r="1.5" fill="#2563eb" opacity="0.9"/>
                        <!-- Indicador de stock -->
                        <path d="M48 20 L52 20 M50 18 L50 22" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>

                <!-- Título que describe el sistema, no el negocio -->
                <h2>Control de Inventario</h2>
                <p>Gestiona productos, ventas y stock desde un solo lugar.</p>

                <!-- Nota de seguridad con icono SVG -->
                <div class="nota-seguridad">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="11" width="18" height="11" rx="2" />
                        <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                    </svg>
                    Acceso seguro y protegido
                </div>
            </div>

            <!-- ==========================================
             PANEL DERECHO: formulario de inicio de sesión
        =========================================== -->
            <div class="panel-formulario">

                <h1>Inicia sesión</h1>
                <span class="texto-bienvenida">Bienvenido de nuevo al sistema</span>

                <!-- Mensaje de éxito cuando viene del registro -->
                <?php if (!empty($exito)): ?>
                    <div class="aviso exito">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20,6 9,17 4,12"/>
                        </svg>
                        <?php echo htmlspecialchars($exito); ?>
                    </div>
                <?php endif; ?>

                <!-- Mensaje de error si las credenciales son incorrectas -->
                <?php if (!empty($error)): ?>
                    <div class="aviso error">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="15" y1="9" x2="9" y2="15"/>
                            <line x1="9" y1="9" x2="15" y2="15"/>
                        </svg>
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <form action="" method="POST">

                    <!-- Campo: correo electrónico -->
                    <div class="grupo-campo">
                        <label for="email">Correo electrónico</label>
                        <div class="caja-input">
                            <!-- Icono sobre (correo) -->
                            <svg class="icono-campo" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="2" y="4" width="20" height="16" rx="2" />
                                <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7" />
                            </svg>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                placeholder="ejemplo@correo.com"
                                required>
                        </div>
                    </div>

                    <!-- Campo: contraseña -->
                    <div class="grupo-campo">
                        <label for="contrasena">Contraseña</label>
                        <div class="caja-input">
                            <!-- Icono candado (contraseña) -->
                            <svg class="icono-campo" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                                <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                            </svg>
                            <input
                                type="password"
                                id="contrasena"
                                name="contrasena"
                                placeholder="••••••••"
                                required>
                        </div>
                    </div>

                    <!-- Opciones: recordar equipo y link de ayuda -->
                    <div class="fila-opciones">
                        <div class="opcion-recordar">
                            <input type="checkbox" id="recordar" name="recordar">
                            <label for="recordar">Recordar equipo</label>
                        </div>
                        <a href="#" class="link-ayuda">¿Problemas de acceso?</a>
                    </div>

                    <!-- Botón de envío -->
                    <button type="submit" class="boton-entrar">
                        Ingresar →
                    </button>

                </form>

                <!-- Divisor visual -->
                <div class="linea-divisora"><span>o</span></div>

                <!-- Link para ir al registro -->
                <footer class="pie-formulario">
                    ¿Nuevo en el sistema? <a href="registro.php">Crea una cuenta aquí</a>
                </footer>

            </div><!-- fin panel-formulario -->

        </div><!-- fin tarjeta-acceso -->
    </div><!-- fin caja-acceso -->

</body>

</html>