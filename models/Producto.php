<?php
/**
 * ============================================================================
 * MODELO: Producto
 * Sistema: SIGI — Gestión de Inventario
 * ============================================================================
 * Responsabilidad única: encapsular TODA la lógica de acceso a datos
 * de la tabla `productos`. Ningún otro archivo debe escribir SQL
 * relacionado con productos.
 *
 * Convenciones:
 *  - Métodos que devuelven colecciones → array (nunca false)
 *  - Métodos que devuelven un registro  → array|false
 *  - Métodos de escritura              → true|string (true=éxito, string=error)
 *  - Toda consulta usa PDO preparado   → sin concatenación de variables en SQL
 * ============================================================================
 */
class Producto
{
    private PDO $conn;

    // Nombre de la tabla principal
    private const TABLE = 'productos';

    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }

    // =========================================================================
    // LECTURA
    // =========================================================================

    /**
     * Listar todos los productos con nombre de categoría y proveedor.
     * Incluye activos, inactivos y agotados.
     */
    public function listarTodos(): array
    {
        try {
            $sql = "SELECT
                        p.id_producto,
                        p.codigoUnico,
                        p.nombre,
                        p.descripcion,
                        p.id_categoria,
                        p.id_proveedor,
                        p.precio_venta,
                        p.precio_costo   AS precio_compra,
                        p.stock_minimo,
                        p.stock_actual,
                        p.fechaCreacion,
                        p.fechaVencimiento,
                        p.estado,
                        p.imagen,
                        c.nombre         AS nombre_categoria,
                        pr.nombre        AS nombre_proveedor
                    FROM " . self::TABLE . " p
                    LEFT JOIN categorias c   ON p.id_categoria = c.id_categoria
                    LEFT JOIN proveedores pr ON p.id_proveedor = pr.id_proveedor
                    ORDER BY p.nombre ASC";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("[SIGI][Producto] listarTodos: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Listar solo productos con estado 'activo' (para POS y consultas de empleado).
     */
    public function listarActivos(): array
    {
        try {
            $sql = "SELECT
                        p.id_producto,
                        p.codigoUnico,
                        p.nombre,
                        p.descripcion,
                        p.id_categoria,
                        p.id_proveedor,
                        p.precio_venta,
                        p.precio_costo   AS precio_compra,
                        p.stock_minimo,
                        p.stock_actual,
                        p.estado,
                        p.imagen,
                        c.nombre         AS nombre_categoria,
                        pr.nombre        AS nombre_proveedor
                    FROM " . self::TABLE . " p
                    LEFT JOIN categorias c   ON p.id_categoria = c.id_categoria
                    LEFT JOIN proveedores pr ON p.id_proveedor = pr.id_proveedor
                    WHERE p.estado = 'activo'
                    ORDER BY p.nombre ASC";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("[SIGI][Producto] listarActivos: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener un producto por su ID.
     * @return array|false
     */
    public function obtenerPorId(int $id)
    {
        try {
            $sql = "SELECT
                        p.*,
                        p.precio_costo AS precio_compra,
                        c.nombre       AS nombre_categoria,
                        pr.nombre      AS nombre_proveedor
                    FROM " . self::TABLE . " p
                    LEFT JOIN categorias c   ON p.id_categoria = c.id_categoria
                    LEFT JOIN proveedores pr ON p.id_proveedor = pr.id_proveedor
                    WHERE p.id_producto = :id
                    LIMIT 1";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: false;

        } catch (PDOException $e) {
            error_log("[SIGI][Producto] obtenerPorId: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Verificar si un código único ya está en uso (para validación antes de insertar).
     *
     * @param string   $codigo     Código a verificar
     * @param int|null $excluirId  ID a excluir (útil al editar)
     */
    public function existeCodigo(string $codigo, ?int $excluirId = null): bool
    {
        try {
            $sql    = "SELECT COUNT(*) FROM " . self::TABLE . " WHERE codigoUnico = :codigo";
            $params = [':codigo' => trim($codigo)];

            if ($excluirId !== null) {
                $sql .= " AND id_producto != :excluir";
                $params[':excluir'] = $excluirId;
            }

            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return (int) $stmt->fetchColumn() > 0;

        } catch (PDOException $e) {
            error_log("[SIGI][Producto] existeCodigo: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtener productos con stock por debajo del mínimo (alertas).
     */
    public function obtenerBajoStock(): array
    {
        try {
            $sql = "SELECT
                        p.id_producto,
                        p.nombre,
                        p.codigoUnico,
                        p.stock_actual,
                        p.stock_minimo,
                        c.nombre AS nombre_categoria
                    FROM " . self::TABLE . " p
                    LEFT JOIN categorias c ON p.id_categoria = c.id_categoria
                    WHERE p.stock_actual <= p.stock_minimo
                      AND p.estado != 'inactivo'
                    ORDER BY p.stock_actual ASC";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("[SIGI][Producto] obtenerBajoStock: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Estadísticas generales para el dashboard.
     */
    public function obtenerEstadisticas(): array
    {
        try {
            $sql = "SELECT
                        COUNT(*)                                                       AS total,
                        SUM(CASE WHEN estado = 'activo'   THEN 1 ELSE 0 END)          AS activos,
                        SUM(CASE WHEN estado = 'inactivo' THEN 1 ELSE 0 END)          AS inactivos,
                        SUM(CASE WHEN estado = 'agotado'  THEN 1 ELSE 0 END)          AS agotados,
                        SUM(CASE WHEN stock_actual <= stock_minimo THEN 1 ELSE 0 END) AS bajo_stock
                    FROM " . self::TABLE;

            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: [
                'total' => 0, 'activos' => 0, 'inactivos' => 0,
                'agotados' => 0, 'bajo_stock' => 0
            ];

        } catch (PDOException $e) {
            error_log("[SIGI][Producto] obtenerEstadisticas: " . $e->getMessage());
            return ['total' => 0, 'activos' => 0, 'inactivos' => 0, 'agotados' => 0, 'bajo_stock' => 0];
        }
    }

    // =========================================================================
    // ESCRITURA
    // =========================================================================

    /**
     * Crear un nuevo producto.
     * @param  array $datos Campos del formulario ya saneados por el controlador
     * @return true|string  true si éxito, mensaje de error si falla
     */
    public function crear(array $datos)
    {
        try {
            // Validar código único
            if ($this->existeCodigo($datos['codigoUnico'])) {
                return 'El código "' . htmlspecialchars($datos['codigoUnico']) . '" ya está registrado.';
            }

            $sql = "INSERT INTO " . self::TABLE . "
                        (codigoUnico, nombre, descripcion, id_categoria, id_proveedor,
                         precio_venta, precio_costo, stock_minimo, stock_actual,
                         fechaVencimiento, estado, imagen)
                    VALUES
                        (:codigo, :nombre, :descripcion, :id_categoria, :id_proveedor,
                         :precio_venta, :precio_costo, :stock_minimo, :stock_actual,
                         :fechaVencimiento, :estado, :imagen)";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':codigo',          trim($datos['codigoUnico']),                PDO::PARAM_STR);
            $stmt->bindValue(':nombre',          trim($datos['nombre']),                     PDO::PARAM_STR);
            $stmt->bindValue(':descripcion',     $datos['descripcion'] ?? null,              PDO::PARAM_STR);
            $stmt->bindValue(':id_categoria',    (int) $datos['id_categoria'],               PDO::PARAM_INT);
            $stmt->bindValue(':id_proveedor',    $datos['id_proveedor'] ?: null,             PDO::PARAM_INT);
            $stmt->bindValue(':precio_venta',    (float) $datos['precio_venta'],             PDO::PARAM_STR);
            $stmt->bindValue(':precio_costo',    (float) $datos['precio_costo'],             PDO::PARAM_STR);
            $stmt->bindValue(':stock_minimo',    (int) ($datos['stock_minimo'] ?? 0),        PDO::PARAM_INT);
            $stmt->bindValue(':stock_actual',    (int) ($datos['stock_actual'] ?? 0),        PDO::PARAM_INT);
            $stmt->bindValue(':fechaVencimiento',$datos['fechaVencimiento'] ?: null,         PDO::PARAM_STR);
            $stmt->bindValue(':estado',          $datos['estado'] ?? 'activo',               PDO::PARAM_STR);
            $stmt->bindValue(':imagen',          $datos['imagen'] ?? null,                   PDO::PARAM_STR);

            $stmt->execute();
            return true;

        } catch (PDOException $e) {
            error_log("[SIGI][Producto] crear: " . $e->getMessage());
            return 'Error al guardar el producto. Intente nuevamente.';
        }
    }

    /**
     * Actualizar un producto existente.
     * El stock_actual NO se modifica aquí — se gestiona desde el módulo de Inventario.
     * @return true|string
     */
    public function actualizar(int $id, array $datos)
    {
        try {
            // Validar código único excluyendo el propio registro
            if ($this->existeCodigo($datos['codigoUnico'], $id)) {
                return 'El código "' . htmlspecialchars($datos['codigoUnico']) . '" ya está en uso por otro producto.';
            }

            $sql = "UPDATE " . self::TABLE . " SET
                        codigoUnico      = :codigo,
                        nombre           = :nombre,
                        descripcion      = :descripcion,
                        id_categoria     = :id_categoria,
                        id_proveedor     = :id_proveedor,
                        precio_venta     = :precio_venta,
                        precio_costo     = :precio_costo,
                        stock_minimo     = :stock_minimo,
                        fechaVencimiento = :fechaVencimiento,
                        estado           = :estado,
                        imagen           = :imagen
                    WHERE id_producto = :id";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':codigo',          trim($datos['codigoUnico']),                PDO::PARAM_STR);
            $stmt->bindValue(':nombre',          trim($datos['nombre']),                     PDO::PARAM_STR);
            $stmt->bindValue(':descripcion',     $datos['descripcion'] ?? null,              PDO::PARAM_STR);
            $stmt->bindValue(':id_categoria',    (int) $datos['id_categoria'],               PDO::PARAM_INT);
            $stmt->bindValue(':id_proveedor',    $datos['id_proveedor'] ?: null,             PDO::PARAM_INT);
            $stmt->bindValue(':precio_venta',    (float) $datos['precio_venta'],             PDO::PARAM_STR);
            $stmt->bindValue(':precio_costo',    (float) $datos['precio_costo'],             PDO::PARAM_STR);
            $stmt->bindValue(':stock_minimo',    (int) ($datos['stock_minimo'] ?? 0),        PDO::PARAM_INT);
            $stmt->bindValue(':fechaVencimiento',$datos['fechaVencimiento'] ?: null,         PDO::PARAM_STR);
            $stmt->bindValue(':estado',          $datos['estado'] ?? 'activo',               PDO::PARAM_STR);
            $stmt->bindValue(':imagen',          $datos['imagen'] ?? null,                   PDO::PARAM_STR);
            $stmt->bindValue(':id',              $id,                                        PDO::PARAM_INT);

            $stmt->execute();
            return true;

        } catch (PDOException $e) {
            error_log("[SIGI][Producto] actualizar: " . $e->getMessage());
            return 'Error al actualizar el producto. Intente nuevamente.';
        }
    }

    /**
     * Cambiar el estado de un producto (activo / inactivo / agotado).
     * @return true|string
     */
    public function cambiarEstado(int $id, string $estado)
    {
        $estadosValidos = ['activo', 'inactivo', 'agotado'];
        if (!in_array($estado, $estadosValidos, true)) {
            return 'Estado no válido.';
        }

        try {
            $sql  = "UPDATE " . self::TABLE . " SET estado = :estado WHERE id_producto = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':estado', $estado, PDO::PARAM_STR);
            $stmt->bindValue(':id',     $id,     PDO::PARAM_INT);
            $stmt->execute();
            return true;

        } catch (PDOException $e) {
            error_log("[SIGI][Producto] cambiarEstado: " . $e->getMessage());
            return 'Error al cambiar el estado del producto.';
        }
    }
}
