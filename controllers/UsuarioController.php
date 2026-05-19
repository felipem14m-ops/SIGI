<?php
// Inicia o reanuda la sesión para poder utilizar variables de sesión (como alertas y mensajes de éxito).
session_start();

// Importamos la conexión a la base de datos y la lógica del modelo Usuario.
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Usuario.php';

class UsuarioController {

    // Método principal para registrar nuevos usuarios desde la página de registro público.
    public function registrar() {

        // Validación de método HTTP:
        // Evita que un atacante o usuario intente acceder al proceso de guardado simplemente pegando la URL en el navegador (GET).
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: ../views/Usuario/registro.php");
            exit;
        }

        // Recolección y saneamiento de datos:
        // 'trim' se utiliza para limpiar espacios invisibles (al inicio y final) que el usuario pudo haber tipeado por error.
        $nombres = trim($_POST['nombres'] ?? '');      
        $email = trim($_POST['email'] ?? '');
        $numeroIdentificacion = trim($_POST['numeroIdentificacion'] ?? '');
        $contrasena = trim($_POST['contrasena'] ?? '');
        $confirmar_password = trim($_POST['confirmar_password'] ?? '');
        $rol = trim($_POST['rol'] ?? '');

        // Validación de campos vacíos:
        // Seguridad a nivel de Backend. Previene registros "fantasma" en caso de que alguien haya burlado la validación HTML (Frontend).
        if (empty($nombres) || empty($numeroIdentificacion) || empty($email) || empty($contrasena) || empty($confirmar_password)) {
            $_SESSION['alert'] = [
                'icon' => 'warning',
                'title' => 'Campos incompletos',
                'text' => 'Debe completar todos los campos'
            ];
            header("Location: ../views/Usuario/registro.php");
            exit;
        }

        // Validación de formato de correo:
        // Protege la base de datos contra correos malformados (ej: "usuario@com") usando filtros nativos de PHP.
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['alert'] = [
                'icon' => 'error',
                'title' => 'Correo inválido',
                'text' => 'Ingrese un correo válido'
            ];
            header("Location: ../views/Usuario/registro.php");
            exit;
        }

        // Confirmación de contraseña:
        // Previene que el usuario se registre con un error tipográfico en su contraseña y luego no pueda acceder.
        if ($contrasena !== $confirmar_password) {
            $_SESSION['alert'] = [
                'icon' => 'error',
                'title' => 'Error',
                'text' => 'Las contraseñas no coinciden'
            ];
            header("Location: ../views/Usuario/registro.php");
            exit;
        }

        // Política mínima de seguridad para contraseñas:
        // Exige al menos 6 caracteres para mitigar vulnerabilidades por contraseñas extremadamente débiles.
        if (strlen($contrasena) < 6) {
            $_SESSION['alert'] = [
                'icon' => 'warning',
                'title' => 'Contraseña inválida',
                'text' => 'La contraseña debe tener al menos 6 caracteres'
            ];
            header("Location: ../views/Usuario/registro.php");
            exit;
        }


        // Instancia de base de datos y modelo de Usuario
        $database = new Database();
        $db = $database->conectar();
        $usuario = new Usuario($db);

        // Prevención de duplicados:
        // Consulta en la base de datos si el correo ya existe. Evita conflictos de llaves únicas y cuentas dobles.
        if ($usuario->existeCorreo($email)) {
            $_SESSION['alert'] = [
                'icon' => 'error',
                'title' => 'Correo existente',
                'text' => 'Este correo ya está registrado'
            ];
            header("Location: ../views/Usuario/registro.php");
            exit;
        }

        // Preparación del arreglo de datos que será inyectado al modelo
        $datos = [
            'nombres' => $nombres,
            'email' => $email,
            'contrasena' => password_hash($contrasena, PASSWORD_DEFAULT),
            'numeroIdentificacion'=> $numeroIdentificacion,
            'rol' => $rol,
        ];

        // Ejecuta el método registrar en la base de datos.
        $resultado = $usuario->registrar($datos);

        // Control de flujo según el resultado de la operación en BD:
        if ($resultado === true) {
            // Si el registro fue exitoso, creamos una alerta positiva y redireccionamos para que inicie sesión.
            $_SESSION['alert'] = [
                'icon' => 'success',
                'title' => 'Registro exitoso',
                'text' => 'Tu cuenta fue creada correctamente',
                'redirect' => 'login.php' // Redirección condicional para el SweetAlert en frontend
            ];

            header("Location: ../views/Usuario/registro.php");
            exit;

        } else {
            // Si hubo un fallo (ej: error en query SQL), lanzamos un error en la alerta.
            $_SESSION['alert'] = [
                'icon' => 'error',
                'title' => 'Error',
                'text' => $resultado
            ];

            header("Location: ../views/Usuario/registro.php");            
            exit;
        }    
    }
}

// Instanciamos el controlador
$controller = new UsuarioController();
// Ejecutamos inmediatamente la función principal de registro.
$controller->registrar();
?>