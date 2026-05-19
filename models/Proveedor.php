<?php
/**
 * ============================================================================
 * MODELO: Proveedor
 * Sistema: SIGI — Gestión de Inventario
 * ============================================================================
 */
class Proveedor
{
    private PDO $conn;
    private const TABLE = 'proveedores';

    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }

    /** Listar proveedores con filtros opcionales (Estado, Empresa). */
    public function listarTodos(array $filtros = []): array
    {
        try {
            $where = [];
            $params = [];

            if (isset($filtros['estado']) && $filtros['estado'] !== 'todos') {
                $where[] = "pr.activo = :estado";
                $params[':estado'] = ($filtros['estado'] === 'activo') ? 1 : 0;
            }

            if (!empty($filtros['empresa'])) {
                $where[] = "pr.empresa LIKE :empresa";
                $params[':empresa'] = "%" . $filtros['empresa'] . "%";
            }

            $whereSql = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

            $sql = "SELECT
                        pr.id_proveedor,
                        pr.nombre,
                        pr.telefono,
                        pr.email,
                        pr.empresa,
                        pr.activo,
                        pr.fecha_registro,
                        COUNT(p.id_producto) AS total_productos
                    FROM " . self::TABLE . " pr
                    LEFT JOIN productos p ON pr.id_proveedor = p.id_proveedor
                    $whereSql
                    GROUP BY pr.id_proveedor, pr.nombre, pr.telefono,
                             pr.email, pr.empresa, pr.activo, pr.fecha_registro
                    ORDER BY pr.nombre ASC";

            $stmt = $this->conn->prepare($sql);
            foreach ($params as $key => $val) {
                $stmt->bindValue($key, $val);
            }
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("[SIGI][Proveedor] listarTodos: " . $e->getMessage());
            return [];
        }
    }

    /** Listar solo proveedores activos (para selects). */
    public function listarActivos(): array
    {
        try {
            $sql  = "SELECT id_proveedor, nombre, empresa FROM " . self::TABLE . " WHERE activo = 1 ORDER BY nombre ASC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("[SIGI][Proveedor] listarActivos: " . $e->getMessage());
            return [];
        }
    }

    public function crear(array $datos)
    {
        try {
            $sql = "INSERT INTO " . self::TABLE . " (nombre, telefono, email, empresa, activo)
                    VALUES (:nombre, :telefono, :email, :empresa, 1)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':nombre',   trim($datos['nombre']   ?? ''), PDO::PARAM_STR);
            $stmt->bindValue(':telefono', trim($datos['telefono'] ?? ''), PDO::PARAM_STR);
            $stmt->bindValue(':email',    trim($datos['email']    ?? ''), PDO::PARAM_STR);
            $stmt->bindValue(':empresa',  trim($datos['empresa']  ?? ''), PDO::PARAM_STR);
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            error_log("[SIGI][Proveedor] crear: " . $e->getMessage());
            return 'Error al crear el proveedor.';
        }
    }

    public function actualizar(int $id, array $datos)
    {
        try {
            $sql = "UPDATE " . self::TABLE . "
                    SET nombre = :nombre, telefono = :telefono, email = :email, empresa = :empresa
                    WHERE id_proveedor = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':nombre',   trim($datos['nombre']   ?? ''), PDO::PARAM_STR);
            $stmt->bindValue(':telefono', trim($datos['telefono'] ?? ''), PDO::PARAM_STR);
            $stmt->bindValue(':email',    trim($datos['email']    ?? ''), PDO::PARAM_STR);
            $stmt->bindValue(':empresa',  trim($datos['empresa']  ?? ''), PDO::PARAM_STR);
            $stmt->bindValue(':id',       $id,                            PDO::PARAM_INT);
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            error_log("[SIGI][Proveedor] actualizar: " . $e->getMessage());
            return 'Error al actualizar el proveedor.';
        }
    }

    public function cambiarEstado(int $id, int $activo)
    {
        try {
            $sql  = "UPDATE " . self::TABLE . " SET activo = :activo WHERE id_proveedor = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':activo', $activo, PDO::PARAM_INT);
            $stmt->bindValue(':id',     $id,     PDO::PARAM_INT);
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            error_log("[SIGI][Proveedor] cambiarEstado: " . $e->getMessage());
            return 'Error al cambiar el estado.';
        }
    }

    /** Obtener productos activos asociados a un proveedor específico. */
    public function obtenerProductosActivos(int $idProveedor): array
    {
        try {
            $sql = "SELECT
                        p.codigoUnico,
                        p.nombre,
                        c.nombre AS nombre_categoria,
                        p.stock_actual,
                        p.stock_minimo,
                        p.precio_venta
                    FROM productos p
                    LEFT JOIN categorias c ON p.id_categoria = c.id_categoria
                    WHERE p.id_proveedor = :id AND p.estado = 'Activo'
                    ORDER BY p.nombre ASC";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':id', $idProveedor, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("[SIGI][Proveedor] obtenerProductosActivos: " . $e->getMessage());
            return [];
        }
    }
}
