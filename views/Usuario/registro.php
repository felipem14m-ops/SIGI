<?php
session_start();
require_once '../../config/database.php';
require_once '../../models/Usuario.php';

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_rol               = 2; // Default to Empleado
    $nombre               = trim($_POST['nombre'] ?? '');
    $email                = trim($_POST['email'] ?? '');
    $numeroIdentificacion = trim($_POST['numeroIdentificacion'] ?? '');
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
                    'id_rol' => $id_rol,
                    'nombre' => $nombre,
                    'numeroIdentificacion' => $numeroIdentificacion,
                    'email' => $email,
                    'contrasena' => password_hash($contrasena, PASSWORD_DEFAULT)
                ];

                if ($usuario->registrar($datos) === true) {
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
    <title>Crear Cuenta - SIGI</title>
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
            /* Registro: Formulario a la izquierda, imagen a la derecha */
            flex-direction: row-reverse;
            width: 100%;
            max-width: 1100px;
            min-height: 650px;
            background: #ffffff;
            border-radius: 24px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .auth-cover {
            flex: 1;
            /* Misma imagen de almacén que en el login */
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
            right: 3rem;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 1.5rem;
            font-weight: 700;
            justify-content: flex-end;
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
            max-width: 450px;
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

        .form-row {
            display: flex;
            gap: 1rem;
        }

        .form-row .form-group {
            flex: 1;
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
            padding: 0.8rem 1rem 0.8rem 2.75rem;
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
            margin-top: 1rem;
        }

        .btn-submit:hover {
            background: var(--primary-hover);
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -5px rgba(79, 70, 229, 0.4);
        }

        .auth-footer {
            margin-top: 1.5rem;
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
                flex-direction: column;
                height: auto;
                min-height: 90vh;
            }

            .auth-form-wrapper {
                padding: 2rem;
            }

            .form-row {
                flex-direction: column;
                gap: 0;
            }
        }
    </style>
</head>

<body>
    <div class="auth-container">
        <!-- Panel Derecho: Branding -->
        <div class="auth-cover">
            <div class="brand-header">
                SIGI
                <div class="brand-logo"><i class="fas fa-store"></i></div>
            </div>
            <div class="cover-content">
                <h1 class="cover-title">Únete a nosotros</h1>
                <p class="cover-subtitle">Accede a las herramientas de gestión de inventario, colabora con tu equipo y lleva el control comercial al siguiente nivel.</p>
            </div>
            <div class="cover-footer" style="font-size: 0.85rem; color: #94a3b8;">
                &copy; 2026 SIGI. Todos los derechos reservados.
            </div>
        </div>

        <!-- Panel Izquierdo: Formulario -->
        <div class="auth-form-wrapper">
            <div class="form-content">
                <a href="../../public/index.php" class="back-link-inline">
                    <i class="fas fa-arrow-left"></i> Volver al inicio
                </a>

                <a href="../../public/index.php" class="mobile-logo">
                    <div class="logo-icon"><i class="fas fa-store"></i></div> SIGI
                </a>

                <div class="auth-header">
                    <h1>Crear Cuenta</h1>
                    <p>Registra tus datos para unirte a la plataforma</p>
                </div>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle" style="margin-top: 2px;"></i>
                        <div><?= htmlspecialchars($error) ?></div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($success) || !empty($_SESSION['exito'])): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle" style="margin-top: 2px;"></i>
                        <div><?= htmlspecialchars($success ?? $_SESSION['exito']) ?></div>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="form-group">
                        <label class="form-label" for="nombre">Nombre Completo</label>
                        <div class="input-icon-wrapper">
                            <i class="fas fa-user input-icon"></i>
                            <input type="text" id="nombre" name="nombre" class="form-input" placeholder="Ej. Juan Carlos Pérez" required value="<?= isset($_POST['nombre']) ? htmlspecialchars($_POST['nombre']) : '' ?>">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="numeroIdentificacion">Número de Identificación</label>
                            <div class="input-icon-wrapper">
                                <i class="fas fa-id-card input-icon"></i>
                                <input type="text" id="numeroIdentificacion" name="numeroIdentificacion" class="form-input" placeholder="Cédula o NIT" required value="<?= isset($_POST['numeroIdentificacion']) ? htmlspecialchars($_POST['numeroIdentificacion']) : '' ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="email">Correo Electrónico</label>
                            <div class="input-icon-wrapper">
                                <i class="fas fa-envelope input-icon"></i>
                                <input type="email" id="email" name="email" class="form-input" placeholder="ejemplo@sigi.com" required value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="contrasena">Contraseña</label>
                            <div class="input-icon-wrapper">
                                <i class="fas fa-lock input-icon"></i>
                                <input type="password" id="contrasena" name="contrasena" class="form-input" placeholder="Mínimo 8 caracteres" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="confirmar">Confirmar Contraseña</label>
                            <div class="input-icon-wrapper">
                                <i class="fas fa-check-circle input-icon"></i>
                                <input type="password" id="confirmar" name="confirmar" class="form-input" placeholder="Mínimo 8 caracteres" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom: 0.5rem;">
                        <label style="display: flex; align-items: flex-start; gap: 0.5rem; color: var(--text-gray); font-size: 0.85rem; cursor: pointer;">
                            <input type="checkbox" name="terminos" required style="accent-color: var(--primary-color); margin-top: 3px;">
                            <span>He leído y acepto los <a href="#" class="auth-link">Términos de Servicio</a> y la <a href="#" class="auth-link">Política de Privacidad</a> de SIGI.</span>
                        </label>
                    </div>

                    <button type="submit" class="btn-submit">
                        Crear Cuenta <i class="fas fa-user-plus"></i>
                    </button>
                </form>

                <div class="auth-footer">
                    ¿Ya tienes una cuenta institucional? <a href="login.php" class="auth-link">Inicia sesión</a>
                </div>
            </div>
        </div>
    </div>
</body>

</html>