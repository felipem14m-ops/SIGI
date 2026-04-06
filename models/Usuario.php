<?php

class Usuario
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    /* ==========================================
    REGISTRAR USUARIO
       ========================================== */
    public function registrar($datos)
    {
        $sql = "INSERT INTO usuarios
                (id_rol, nombre, numeroIdentificacion, email, contrasena)
                VALUES
                (:id_rol, :nombre, :numeroIdentificacion, :email, :contrasena)";

        $stmt = $this->conn->prepare($sql);

        // Hashear contraseña
        $contrasena_hash = password_hash($datos['contrasena'], PASSWORD_BCRYPT);

        $stmt->bindParam(':id_rol', $datos['id_rol']);
        $stmt->bindParam(':nombre', $datos['nombre']);
        $stmt->bindParam(':numeroIdentificacion', $datos['numeroIdentificacion']);
        $stmt->bindParam(':email', $datos['email']);
        $stmt->bindParam(':contrasena', $contrasena_hash);

        return $stmt->execute();
    }

    /* ==========================================
    OBTENER USUARIO POR CORREO
       ========================================== */
    public function obtenerPorCorreo($email)
    {
        $sql = "SELECT u.id_usuario, u.nombre, u.email, u.contrasena, u.activo, r.nombre as nombre_rol
                FROM usuarios u
                LEFT JOIN roles r ON u.id_rol = r.id_rol
                WHERE u.email = :email LIMIT 1";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /* ==========================================
    VERIFICAR SI EXISTE CORREO
       ========================================== */
    public function existeCorreo($email)
    {
        $sql = "SELECT id_usuario FROM usuarios WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    /* ==========================================
    VERIFICAR SI EXISTE IDENTIFICACIÓN
       ========================================== */
    public function existeIdentificacion($numero_identificacion)
    {
        $sql = "SELECT id_usuario FROM usuarios WHERE numeroIdentificacion = :numeroIdentificacion LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':numeroIdentificacion', $numero_identificacion);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    /* ==========================================
    ACTUALIZAR ÚLTIMO ACCESO
       ========================================== */
    public function actualizarUltimoAcceso($id_usuario)
    {
        $sql = "UPDATE usuarios SET ultima_actualizacion = NOW() WHERE id_usuario = :id_usuario";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id_usuario', $id_usuario);

        return $stmt->execute();
    }

    /* ==========================================
    OBTENER USUARIO POR ID
       ========================================== */
    public function obtenerPorId($id_usuario)
    {
        $sql = "SELECT u.*, r.nombre as nombre_rol
                FROM usuarios u
                LEFT JOIN roles r ON u.id_rol = r.id_rol
                WHERE u.id_usuario = :id_usuario LIMIT 1";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id_usuario', $id_usuario);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /* ==========================================
    LISTAR TODOS LOS USUARIOS
       ========================================== */
    public function listarTodos()
    {
        $sql = "SELECT u.*, r.nombre as nombre_rol
                FROM usuarios u
                LEFT JOIN roles r ON u.id_rol = r.id_rol
                WHERE u.activo = 1
                ORDER BY u.fecha_creacion DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* ==========================================
    ACTUALIZAR USUARIO
       ========================================== */
    public function actualizar($id_usuario, $datos)
    {
        $sql = "UPDATE usuarios SET 
                nombre = :nombre,
                email = :email,
                numeroIdentificacion = :numeroIdentificacion
                WHERE id_usuario = :id_usuario";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindParam(':id_usuario', $id_usuario);
        $stmt->bindParam(':nombre', $datos['nombre']);
        $stmt->bindParam(':email', $datos['email']);
        $stmt->bindParam(':numeroIdentificacion', $datos['numeroIdentificacion']);

        return $stmt->execute();
    }

    /* ==========================================
    CAMBIAR CONTRASEÑA
       ========================================== */
    public function cambiarContrasena($id_usuario, $contrasena_nueva)
    {
        $sql = "UPDATE usuarios SET 
                contrasena = :contrasena
                WHERE id_usuario = :id_usuario";

        $stmt = $this->conn->prepare($sql);

        $contrasena_hash = password_hash($contrasena_nueva, PASSWORD_BCRYPT);
        $stmt->bindParam(':id_usuario', $id_usuario);
        $stmt->bindParam(':contrasena', $contrasena_hash);

        return $stmt->execute();
    }

    /* ==========================================
    ACTIVAR/DESACTIVAR USUARIO
       ========================================== */
    public function cambiarEstado($id_usuario, $activo)
    {
        $sql = "UPDATE usuarios SET activo = :activo WHERE id_usuario = :id_usuario";
        $stmt = $this->conn->prepare($sql);

        $stmt->bindParam(':id_usuario', $id_usuario);
        $stmt->bindParam(':activo', $activo);

        return $stmt->execute();
    }

    /* ==========================================
    VERIFICAR CREDENCIALES
       ========================================== */
    public function verificarCredenciales($email, $contrasena)
    {
        $usuario = $this->obtenerPorCorreo($email);

        if (!$usuario) {
            return false;
        }

        if (!$usuario['activo']) {
            return false;
        }

        if (!password_verify($contrasena, $usuario['contrasena'])) {
            return false;
        }

        return $usuario;
    }
}
?>