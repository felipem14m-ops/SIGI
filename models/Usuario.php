<?php
/**
 * ============================================================================
 * MODELO USUARIO - SIGI
 * ============================================================================
 * Gestiona todas las operaciones relacionadas con usuarios del sistema:
 * autenticación, registro, edición, activación/desactivación y consultas.
 *
 * @package SIGI\Models
 * @version 2.0.0
 * ============================================================================
 */

class Usuario
{
    /** @var PDO Conexión a la base de datos */
    private $conn;

    /** @var string Tabla principal */
    private $table = 'usuarios';

    /**
     * Constructor
     *
     * @param PDO $db Conexión PDO inyectada
     */
    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }

    // =========================================================================
    // AUTENTICACIÓN
    // =========================================================================

    /**
     * Verificar credenciales de login.
     * Usado por login.php directamente.
     *
     * @param string $email
     * @param string $contrasena Contraseña en texto plano
     * @return array|false Datos del usuario o false si falla
     */
    public function verificarCredenciales(string $email, string $contrasena)
    {
        try {
            $sql = "SELECT u.*, r.nombre AS nombre_rol
                    FROM {$this->table} u
                    INNER JOIN roles r ON u.id_rol = r.id_rol
                    WHERE u.email = :email
                      AND u.activo = 1
                    LIMIT 1";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':email', trim($email), PDO::PARAM_STR);
            $stmt->execute();

            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$usuario) {
                return false;
            }

            if (!password_verify($contrasena, $usuario['contrasena'])) {
                return false;
            }

            // No exponer la contraseña
            unset($usuario['contrasena']);

            return $usuario;

        } catch (PDOException $e) {
            error_log("[SIGI][Usuario] verificarCredenciales error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtener usuario por correo (incluye inactivos, para el AuthController).
     *
     * @param string $email
     * @return array|false
     */
    public function obtenerPorCorreo(string $email)
    {
        try {
            $sql = "SELECT u.*, r.nombre AS nombre_rol
                    FROM {$this->table} u
                    INNER JOIN roles r ON u.id_rol = r.id_rol
                    WHERE u.email = :email
                    LIMIT 1";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':email', trim($email), PDO::PARAM_STR);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC) ?: false;

        } catch (PDOException $e) {
            error_log("[SIGI][Usuario] obtenerPorCorreo error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Actualiza la fecha de último acceso del usuario.
     *
     * @param  int  $idUsuario
     * @return bool
     *
     * NOTA: Desactivado temporalmente. La columna 'ultimo_acceso' no existe en
     * el esquema actual. Para habilitarlo, agregar la columna en la migración SQL
     * y descomentar el cuerpo del método.
     */
    public function actualizarUltimoAcceso(int $idUsuario): bool
    {
        // TODO: Implementar cuando se agregue la columna 'ultimo_acceso' a la tabla usuarios.
        return true;
    }

    // =========================================================================
    // VALIDACIONES PREVIAS
    // =========================================================================

    /**
     * Verificar si un correo ya está registrado.
     *
     * @param string $email
     * @return bool
     */
    public function existeCorreo(string $email): bool
    {
        try {
            $sql  = "SELECT COUNT(*) FROM {$this->table} WHERE email = :email";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':email', trim($email), PDO::PARAM_STR);
            $stmt->execute();
            return (int) $stmt->fetchColumn() > 0;

        } catch (PDOException $e) {
            error_log("[SIGI][Usuario] existeCorreo error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Verificar si un número de identificación ya está registrado.
     *
     * @param string $numeroIdentificacion
     * @return bool
     */
    public function existeIdentificacion(string $numeroIdentificacion): bool
    {
        try {
            $sql  = "SELECT COUNT(*) FROM {$this->table} WHERE numeroIdentificacion = :num";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':num', trim($numeroIdentificacion), PDO::PARAM_STR);
            $stmt->execute();
            return (int) $stmt->fetchColumn() > 0;

        } catch (PDOException $e) {
            error_log("[SIGI][Usuario] existeIdentificacion error: " . $e->getMessage());
            return false;
        }
    }

    // =========================================================================
    // CRUD
    // =========================================================================

    /**
     * Registrar un nuevo usuario.
     * Usado por UsuarioController (registro público) y AdmiUsuarioController.
     *
     * @param array $datos ['nombres'|'nombre', 'email', 'contrasena', 'numeroIdentificacion', 'rol']
     * @return bool|string true si éxito, string con mensaje de error si falla
     */
    public function registrar(array $datos)
    {
        try {
            // Aceptar 'nombres' o 'nombre' indistintamente
            $nombre = trim($datos['nombres'] ?? $datos['nombre'] ?? '');
            $email  = trim($datos['email'] ?? '');
            $pass   = $datos['contrasena'] ?? '';
            $numId  = trim($datos['numeroIdentificacion'] ?? '');
            $rol    = strtolower(trim($datos['rol'] ?? ''));

            if (empty($nombre) || empty($email) || empty($pass)) {
                return 'Nombre, email y contraseña son obligatorios';
            }

            // Resolver id_rol (si se pasa el nombre del rol) o usar id_rol directamente
            $idRol = $datos['id_rol'] ?? null;
            if (!$idRol && !empty($rol)) {
                $idRol = $this->resolverIdRol($rol);
            }

            if (!$idRol) {
                return "No se especificó un rol válido para el usuario.";
            }

            $sql = "INSERT INTO {$this->table}
                        (nombre, email, contrasena, numeroIdentificacion, id_rol, activo)
                    VALUES
                        (:nombre, :email, :contrasena, :num_id, :id_rol, 1)";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':nombre',     htmlspecialchars(strip_tags($nombre), ENT_QUOTES, 'UTF-8'), PDO::PARAM_STR);
            $stmt->bindValue(':email',      $email,  PDO::PARAM_STR);
            $stmt->bindValue(':contrasena', $pass,   PDO::PARAM_STR); // ya viene hasheada desde el controller
            $stmt->bindValue(':num_id',     $numId,  PDO::PARAM_STR);
            $stmt->bindValue(':id_rol',     $idRol,  PDO::PARAM_INT);

            return $stmt->execute() ? true : 'No se pudo registrar el usuario';

        } catch (PDOException $e) {
            error_log("[SIGI][Usuario] registrar error: " . $e->getMessage());
            return 'Error de base de datos: ' . $e->getMessage();
        }
    }

    /**
     * Actualizar datos de un usuario existente.
     *
     * @param int   $idUsuario
     * @param array $datos ['nombres'|'nombre', 'numeroIdentificacion', 'rol', 'contrasena'(opcional)]
     * @return bool|string true si éxito, string con mensaje de error si falla
     */
    public function actualizar(int $idUsuario, array $datos)
    {
        try {
            $campos = [];
            $params = [':id' => $idUsuario];

            // Nombre
            $nombre = trim($datos['nombres'] ?? $datos['nombre'] ?? '');
            if (!empty($nombre)) {
                $campos[] = "nombre = :nombre";
                $params[':nombre'] = htmlspecialchars(strip_tags($nombre), ENT_QUOTES, 'UTF-8');
            }

            // Identificación
            if (isset($datos['numeroIdentificacion'])) {
                $campos[] = "numeroIdentificacion = :num_id";
                $params[':num_id'] = trim($datos['numeroIdentificacion']);
            }

            // Rol
            $rol = strtolower(trim($datos['rol'] ?? ''));
            if (!empty($rol)) {
                $idRol = $this->resolverIdRol($rol);
                if ($idRol) {
                    $campos[] = "id_rol = :id_rol";
                    $params[':id_rol'] = $idRol;
                }
            }

            // Contraseña (opcional)
            if (!empty($datos['contrasena'])) {
                $campos[] = "contrasena = :contrasena";
                $params[':contrasena'] = $datos['contrasena']; // ya viene hasheada desde el controller
            }

            if (empty($campos)) {
                return 'No hay datos para actualizar';
            }

            $sql  = "UPDATE {$this->table} SET " . implode(', ', $campos) . " WHERE id_usuario = :id";
            $stmt = $this->conn->prepare($sql);

            foreach ($params as $key => $value) {
                $type = is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
                $stmt->bindValue($key, $value, $type);
            }

            return $stmt->execute() ? true : 'No se pudo actualizar el usuario';

        } catch (PDOException $e) {
            error_log("[SIGI][Usuario] actualizar error: " . $e->getMessage());
            return 'Error de base de datos: ' . $e->getMessage();
        }
    }

    /**
     * Activar o desactivar un usuario (soft delete).
     *
     * @param int $idUsuario
     * @param int $nuevoEstado 1 = activo, 0 = inactivo
     * @return bool|string
     */
    public function cambiarEstado(int $idUsuario, int $nuevoEstado)
    {
        try {
            $sql  = "UPDATE {$this->table} SET activo = :activo WHERE id_usuario = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':activo', $nuevoEstado, PDO::PARAM_INT);
            $stmt->bindValue(':id',     $idUsuario,   PDO::PARAM_INT);

            return $stmt->execute() ? true : 'No se pudo cambiar el estado';

        } catch (PDOException $e) {
            error_log("[SIGI][Usuario] cambiarEstado error: " . $e->getMessage());
            return 'Error de base de datos: ' . $e->getMessage();
        }
    }

    // =========================================================================
    // CONSULTAS
    // =========================================================================

    /**
     * Listar todos los usuarios con su rol.
     *
     * @return array
     */
    public function listarTodos(): array
    {
        try {
            $sql = "SELECT u.id_usuario, u.nombre, u.email, u.numeroIdentificacion,
                           u.activo, u.fecha_creacion, r.nombre AS nombre_rol
                    FROM {$this->table} u
                    INNER JOIN roles r ON u.id_rol = r.id_rol
                    ORDER BY u.nombre ASC";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("[SIGI][Usuario] listarTodos error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Listar solo usuarios activos.
     *
     * @return array
     */
    public function listarActivos(): array
    {
        try {
            $sql = "SELECT u.id_usuario, u.nombre, u.email, u.numeroIdentificacion,
                           u.activo, u.fecha_creacion, r.nombre AS nombre_rol
                    FROM {$this->table} u
                    INNER JOIN roles r ON u.id_rol = r.id_rol
                    WHERE u.activo = 1
                    ORDER BY u.nombre ASC";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("[SIGI][Usuario] listarActivos error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener estadísticas de usuarios.
     *
     * @return array
     */
    public function obtenerEstadisticas(): array
    {
        try {
            $sql = "SELECT
                        COUNT(*) AS total,
                        SUM(CASE WHEN activo = 1 THEN 1 ELSE 0 END) AS activos,
                        SUM(CASE WHEN activo = 0 THEN 1 ELSE 0 END) AS inactivos
                    FROM {$this->table}";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: ['total' => 0, 'activos' => 0, 'inactivos' => 0];

        } catch (PDOException $e) {
            error_log("[SIGI][Usuario] obtenerEstadisticas error: " . $e->getMessage());
            return ['total' => 0, 'activos' => 0, 'inactivos' => 0];
        }
    }

    // =========================================================================
    // HELPER PRIVADO
    // =========================================================================

    /**
     * Resolver el id_rol a partir del nombre del rol.
     *
     * @param string $nombreRol  'administrador' | 'empleado'
     * @return int|false
     */
    private function resolverIdRol(string $nombreRol)
    {
        try {
            $sql  = "SELECT id_rol FROM roles WHERE LOWER(nombre) = :nombre LIMIT 1";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':nombre', strtolower(trim($nombreRol)), PDO::PARAM_STR);
            $stmt->execute();
            $row  = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ? (int) $row['id_rol'] : false;

        } catch (PDOException $e) {
            error_log("[SIGI][Usuario] resolverIdRol error: " . $e->getMessage());
            return false;
        }
    }
}
