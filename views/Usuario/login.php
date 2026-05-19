<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Si ya está logueado, redirigir al dashboard según su rol
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    $rol_sesion = strtolower($_SESSION['usuario']['rol'] ?? $_SESSION['rol'] ?? '');
    if ($rol_sesion === 'empleado') {
        header("Location: ../Dashboard/Empleado.php");
    } else {
        header("Location: ../Dashboard/Admin.php");
    }
    exit;
}

require_once '../../config/database.php';
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
            $usuarioModel = new Usuario($conn);

            $user = $usuarioModel->verificarCredenciales($email, $contrasena);

            if ($user) {
                session_regenerate_id(true);

                // Guardar sesión de forma consistente
                $_SESSION['logged_in']  = true;
                $_SESSION['rol']        = strtolower($user['nombre_rol']);
                $_SESSION['nombre']     = $user['nombre'];
                $_SESSION['email']      = $user['email'];
                $_SESSION['usuario']    = [
                    'id_usuario' => $user['id_usuario'],
                    'nombre'     => $user['nombre'],
                    'email'      => $user['email'],
                    'rol'        => strtolower($user['nombre_rol'])
                ];

                $usuarioModel->actualizarUltimoAcceso($user['id_usuario']);

                // Redirigir según el rol
                if (strtolower($user['nombre_rol']) === 'empleado') {
                    header("Location: ../Dashboard/Empleado.php");
                } else {
                    header("Location: ../Dashboard/Admin.php");
                }
                exit;
            } else {
                $error = 'Correo o contraseña incorrectos, o cuenta desactivada';
            }
        } catch (PDOException $e) {
            $error = 'Error en el sistema. Intente más tarde.';
            error_log("[SIGI] Login error: " . $e->getMessage());
        } catch (Exception $e) {
            $error = $e->getMessage();
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
    <title>Iniciar Sesión - SIGI</title>
    <link rel="shortcut icon" type="image/png" href="../../IMG/LOGO SIGI.png">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        :root {
            --primary-color: #4F46E5;
            --primary-hover: #4338ca;
            --bg-body: #f3f4f6;
            --text-dark: #111827;
            --text-gray: #6b7280;
            --border-color: #e5e7eb;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-body);
            color: var(--text-dark);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .auth-container {
            display: flex;
            width: 100%;
            max-width: 1000px;
            min-height: 600px;
            background: #ffffff;
            border-radius: 24px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .auth-cover {
            flex: 1;
            /* Nueva imagen de inventario/logística */
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.8) 0%, rgba(30, 58, 138, 0.9) 100%), url('https://images.unsplash.com/photo-1586528116311-ad8ed3891bb3?auto=format&fit=crop&w=800&q=80') center/cover;
            position: relative;
            display: none;
            flex-direction: column;
            justify-content: center;
            /* Centrado vertical */
            align-items: center;
            /* Centrado horizontal */
            padding: 3rem;
            color: white;
            text-align: center;
            /* Texto centrado */
        }

        @media (min-width: 900px) {
            .auth-cover {
                display: flex;
            }
        }

        .brand-header {
            position: absolute;
            top: 3rem;
            left: 3rem;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 1.5rem;
            font-weight: 700;
        }

        .brand-logo {
            width: 40px;
            height: 40px;
            background: var(--primary-color);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .cover-content {
            max-width: 400px;
            margin: 0 auto;
        }

        .cover-title {
            font-size: 2.5rem;
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: 1rem;
        }

        .cover-subtitle {
            font-size: 1.05rem;
            color: #cbd5e1;
            line-height: 1.6;
        }

        .cover-footer {
            position: absolute;
            bottom: 3rem;
            left: 0;
            width: 100%;
            text-align: center;
        }

        .auth-form-wrapper {
            flex: 1;
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
        }

        .form-content {
            width: 100%;
            max-width: 360px;
            margin: 0 auto;
        }

        .auth-header {
            margin-bottom: 2rem;
        }

        .auth-header h1 {
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--text-dark);
            margin-bottom: 0.5rem;
        }

        .auth-header p {
            color: var(--text-gray);
            font-size: 0.95rem;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-label {
            display: block;
            font-weight: 600;
            font-size: 0.85rem;
            color: #374151;
            margin-bottom: 0.5rem;
        }

        .input-icon-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
        }

        .form-input {
            width: 100%;
            padding: 0.875rem 1rem 0.875rem 2.75rem;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            font-size: 0.95rem;
            font-family: inherit;
            transition: all 0.2s;
            background: #f9fafb;
            box-sizing: border-box;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--primary-color);
            background: white;
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
        }

        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            font-size: 0.85rem;
        }

        .btn-submit {
            width: 100%;
            padding: 1rem;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-submit:hover {
            background: var(--primary-hover);
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -5px rgba(79, 70, 229, 0.4);
        }

        .auth-footer {
            margin-top: 2rem;
            text-align: center;
            font-size: 0.9rem;
            color: var(--text-gray);
        }

        .auth-link {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
        }

        .alert {
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
        }

        .alert-danger {
            background-color: #fef2f2;
            color: #b91c1c;
            border: 1px solid #fecaca;
        }

        .alert-success {
            background-color: #f0fdf4;
            color: #15803d;
            border: 1px solid #bbf7d0;
        }

        .back-link-inline {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--text-gray);
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 2rem;
            background: #f1f5f9;
            padding: 0.4rem 0.8rem;
            border-radius: 50px;
            transition: all 0.2s;
        }

        .back-link-inline:hover {
            color: var(--primary-color);
            background: #eef2ff;
        }

        .mobile-logo {
            display: none;
        }

        @media (max-width: 900px) {
            .mobile-logo {
                display: inline-flex;
                align-items: center;
                gap: 10px;
                font-weight: 800;
                font-size: 1.5rem;
                color: var(--text-dark);
                margin-bottom: 1.5rem;
                text-decoration: none;
            }

            .mobile-logo .logo-icon {
                width: 36px;
                height: 36px;
                background: var(--primary-color);
                border-radius: 8px;
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
            }

            .auth-container {
                height: auto;
                min-height: 90vh;
            }

            .auth-form-wrapper {
                padding: 2rem;
            }
        }
    </style>
</head>

<body>
    <div class="auth-container">
        <!-- Panel Izquierdo: Branding -->
        <div class="auth-cover">
            <div class="brand-header">
                <div class="brand-logo"><i class="fas fa-store"></i></div>
                SIGI
            </div>
            <div class="cover-content">
                <h1 class="cover-title">Optimiza tu logística</h1>
                <p class="cover-subtitle">Controla el stock de tus almacenes de forma precisa, registra movimientos en tiempo real y aumenta tu productividad.</p>
            </div>
            <div class="cover-footer" style="font-size: 0.85rem; color: #94a3b8;">
                &copy; 2026 SIGI. Todos los derechos reservados.
            </div>
        </div>

        <!-- Panel Derecho: Formulario -->
        <div class="auth-form-wrapper">
            <div class="form-content">
                <a href="../../public/index.php" class="back-link-inline">
                    <i class="fas fa-arrow-left"></i> Volver al inicio
                </a>

                <a href="../../public/index.php" class="mobile-logo">
                    <div class="logo-icon"><i class="fas fa-store"></i></div> SIGI
                </a>

                <div class="auth-header">
                    <h1>Iniciar Sesión</h1>
                    <p>Ingresa tus credenciales para continuar</p>
                </div>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle" style="margin-top: 2px;"></i>
                        <div><?= htmlspecialchars($error) ?></div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($exito)): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle" style="margin-top: 2px;"></i>
                        <div><?= htmlspecialchars($exito) ?></div>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="form-group">
                        <label class="form-label" for="email">Correo Electrónico</label>
                        <div class="input-icon-wrapper">
                            <i class="fas fa-envelope input-icon"></i>
                            <input type="email" id="email" name="email" class="form-input" placeholder="ejemplo@sigi.com" required value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="contrasena">Contraseña</label>
                        <div class="input-icon-wrapper">
                            <i class="fas fa-lock input-icon"></i>
                            <input type="password" id="contrasena" name="contrasena" class="form-input" placeholder="••••••••" required>
                        </div>
                    </div>

                    <div class="form-options">
                        <label style="display: flex; align-items: center; gap: 0.5rem; color: var(--text-gray); cursor: pointer;">
                            <input type="checkbox" name="remember" style="accent-color: var(--primary-color);">
                            Recordar sesión
                        </label>
                        <a href="#" class="auth-link">¿Olvidaste tu contraseña?</a>
                    </div>

                    <button type="submit" class="btn-submit">
                        Entrar al Sistema <i class="fas fa-arrow-right"></i>
                    </button>
                </form>

                <div class="auth-footer">
                    ¿Aún no tienes una cuenta? <br>
                    <a href="registro.php" class="auth-link" style="display: inline-block; margin-top: 5px;">Regístrate como empleado</a>
                </div>
            </div>
        </div>
    </div>
</body>

</html>