<?php
session_start();
require_once '../../Api/database.php';
require_once '../../models/Usuario.php';

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre               = trim($_POST['nombre'] ?? '');
    $email                = trim($_POST['email'] ?? '');
    $numeroIdentificacion = trim($_POST['numero_identificacion'] ?? '');
    $contrasena           = $_POST['contrasena'] ?? '';
    $confirmar_contrasena = $_POST['confirmar'] ?? '';
    $terminos             = isset($_POST['terminos']) ? true : false;

    // Validaciones
    if (empty($nombre) || empty($email) || empty($numeroIdentificacion) || empty($contrasena) || empty($confirmar_contrasena)) {
        $error = 'Todos los campos son obligatorios';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'El correo electrónico no es válido';
    } elseif (strlen($contrasena) < 8) {
        $error = 'La contraseña debe tener al menos 8 caracteres';
    } elseif ($contrasena !== $confirmar_contrasena) {
        $error = 'Las contraseñas no coinciden';
    } elseif (!$terminos) {
        $error = 'Debes aceptar los términos y condiciones';
    } else {
        try {
            $database = new Database();
            $conn = $database->conectar();
            $usuario = new Usuario($conn);

            // Verificar email duplicado
            if ($usuario->existeCorreo($email)) {
                $error = 'Este correo ya está registrado';
            }

            // Verificar identificación duplicada
            if (empty($error) && $usuario->existeIdentificacion($numeroIdentificacion)) {
                $error = 'Este número de identificación ya está registrado';
            }

            // Registrar usuario
            if (empty($error)) {
                $datos = [
                    'id_rol' => 2,  // 2 = Vendedor/Empleado
                    'nombre' => $nombre,
                    'numeroIdentificacion' => $numeroIdentificacion,
                    'email' => $email,
                    'contrasena' => $contrasena
                ];

                if ($usuario->registrar($datos)) {
                    $_SESSION['exito'] = '¡Registro exitoso! Redirigiendo al login...';
                    header("refresh:2;url=login.php");
                    exit;
                } else {
                    $error = 'Error al registrar el usuario. Intente nuevamente';
                }
            }
        } catch (PDOException $e) {
            $error = 'Error en el sistema. Intente más tarde: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Cuenta - La Esquina</title>
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

                <!-- Título acorde al sistema, no el nombre del negocio -->
                <h2>Control de Inventario</h2>
                <p>Registra tu acceso para gestionar productos, ventas y stock.</p>

                <div class="nota-seguridad">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="11" width="18" height="11" rx="2" />
                        <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                    </svg>
                    Tus datos están protegidos
                </div>
            </div>

            <!-- ==========================================
             PANEL DERECHO: formulario de registro
        =========================================== -->
            <div class="panel-formulario">

                <h1>Crear cuenta</h1>
                <span class="texto-bienvenida">Completa los datos para continuar</span>

                <!-- Mensaje de error de validación -->
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

                <!-- Mensaje de éxito al registrarse -->
                <?php if (!empty($success)): ?>
                    <div class="aviso exito">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20,6 9,17 4,12"/>
                        </svg>
                        <?php echo htmlspecialchars($success); ?>
                    </div>
                <?php endif; ?>

                <form action="" method="POST">

                    <!-- Campo: nombre completo -->
                    <div class="grupo-campo">
                        <label>Nombre completo</label>
                        <div class="caja-input">
                            <!-- Icono persona -->
                            <svg class="icono-campo" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="8" r="4" />
                                <path d="M4 20c0-4 3.6-7 8-7s8 3 8 7" />
                            </svg>
                            <input type="text" name="nombre" placeholder="Tu nombre completo" required>
                        </div>
                    </div>

                    <!-- Campo: correo electrónico -->
                    <div class="grupo-campo">
                        <label>Correo electrónico</label>
                        <div class="caja-input">
                            <!-- Icono sobre (correo) -->
                            <svg class="icono-campo" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="2" y="4" width="20" height="16" rx="2" />
                                <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7" />
                            </svg>
                            <input type="email" name="email" placeholder="correo@ejemplo.com" required>
                        </div>
                    </div>

                    <!-- Campo: número de identificación (cédula) -->
                    <div class="grupo-campo">
                        <label>Número de identificación</label>
                        <div class="caja-input">
                            <!-- Icono tarjeta de identidad -->
                            <svg class="icono-campo" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="2" y="5" width="20" height="14" rx="2" />
                                <circle cx="8" cy="12" r="2" />
                                <path d="M13 12h4M13 15h3" />
                            </svg>
                            <input type="text" name="numero_identificacion" placeholder="Cédula" required>
                        </div>
                    </div>

                    <!-- Campo: contraseña nueva -->
                    <div class="grupo-campo">
                        <label>Contraseña</label>
                        <div class="caja-input">
                            <!-- Icono candado -->
                            <svg class="icono-campo" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                                <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                            </svg>
                            <input type="password" name="contrasena" placeholder="Mínimo 8 caracteres" required>
                        </div>
                    </div>

                    <!-- Campo: confirmar contraseña -->
                    <div class="grupo-campo">
                        <label>Confirmar contraseña</label>
                        <div class="caja-input">
                            <!-- Icono candado con check -->
                            <svg class="icono-campo" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                                <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                                <path d="m9 16 2 2 4-4" />
                            </svg>
                            <input type="password" name="confirmar" placeholder="Repite tu contraseña" required>
                        </div>
                    </div>

                    <!-- Checkbox: aceptar términos -->
                    <div class="grupo-checkbox">
                        <input type="checkbox" name="terminos" id="terminos" required>
                        <label for="terminos">Acepto los términos y condiciones del sistema</label>
                    </div>

                    <!-- Botón de envío -->
                    <button type="submit" class="boton-entrar">
                        Crear mi cuenta →
                    </button>

                </form>

                <!-- Link para ir al login si ya tiene cuenta -->
                <footer class="pie-formulario">
                    ¿Ya tienes cuenta? <a href="login.php">Inicia sesión aquí</a>
                </footer>

            </div><!-- fin panel-formulario -->

        </div><!-- fin tarjeta-acceso -->
    </div><!-- fin caja-acceso -->

</body>

</html>