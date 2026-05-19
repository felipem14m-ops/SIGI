<?php
/**
 * ============================================================================
 * MODELO: Reporte
 * Sistema: SIGI — Gestión de Inventario
 * ============================================================================
 * Responsabilidad: Proveer los datos estructurados para la generación de 
 * reportes institucionales.
 * ============================================================================
 */

class Reporte
{
    private PDO $conn;

    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }

    /**
     * Obtener inventario actual completo
     * Devuelve solo productos activos por defecto para reportes estándar.
     */
    public function obtenerInventarioActual(): array
    {
        $query = "SELECT 
                    p.id_producto,
                    p.codigoUnico,
                    p.nombre,
                    c.nombre as categoria,
                    pr.nombre as proveedor,
                    p.precio_costo,
                    p.precio_venta,
                    p.stock_actual,
                    p.stock_minimo,
                    p.estado,
                    p.fechaCreacion
                FROM productos p
                LEFT JOIN categorias c ON p.id_categoria = c.id_categoria
                LEFT JOIN proveedores pr ON p.id_proveedor = pr.id_proveedor
                WHERE p.estado = 'activo'
                ORDER BY p.nombre ASC";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("[SIGI][Reporte] obtenerInventarioActual: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener inventario con filtros personalizados
     * @param array $filtros Mapeo de criterios (id_categoria, estado, fechas, stock_bajo)
     */
    public function obtenerInventarioFiltrado(array $filtros): array
    {
        $query = "SELECT 
                    p.id_producto,
                    p.codigoUnico,
                    p.nombre,
                    c.nombre as categoria,
                    pr.nombre as proveedor,
                    p.precio_costo,
                    p.precio_venta,
                    p.stock_actual,
                    p.stock_minimo,
                    p.estado,
                    p.fechaCreacion
                FROM productos p
                LEFT JOIN categorias c ON p.id_categoria = c.id_categoria
                LEFT JOIN proveedores pr ON p.id_proveedor = pr.id_proveedor
                WHERE 1=1";
        
        $params = [];
        
        // Filtro por categoría
        if (!empty($filtros['id_categoria'])) {
            $query .= " AND p.id_categoria = :id_categoria";
            $params[':id_categoria'] = $filtros['id_categoria'];
        }
        
        // Filtro por estado
        if (!empty($filtros['estado'])) {
            $query .= " AND p.estado = :estado";
            $params[':estado'] = $filtros['estado'];
        }
        
        // Filtro por rango de fechas de creación
        if (!empty($filtros['fecha_desde'])) {
            $query .= " AND p.fechaCreacion >= :fecha_desde";
            $params[':fecha_desde'] = $filtros['fecha_desde'];
        }
        
        if (!empty($filtros['fecha_hasta'])) {
            $query .= " AND p.fechaCreacion <= :fecha_hasta";
            $params[':fecha_hasta'] = $filtros['fecha_hasta'] . ' 23:59:59';
        }
        
        // Filtro de alerta por stock bajo
        if (!empty($filtros['stock_bajo'])) {
            $query .= " AND p.stock_actual <= p.stock_minimo";
        }
        
        $query .= " ORDER BY p.nombre ASC";
        
        try {
            $stmt = $this->conn->prepare($query);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("[SIGI][Reporte] obtenerInventarioFiltrado: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Guarda un registro de reporte generado en la base de datos
     */
    public function guardarReporteGenerado(string $tipo_reporte, string $nombre_archivo, string $rol): bool
    {
        $query = "INSERT INTO reportes_generados (tipo_reporte, nombre_archivo, rol)
                  VALUES (:tipo_reporte, :nombre_archivo, :rol)";
        try {
            $stmt = $this->conn->prepare($query);
            return $stmt->execute([
                ':tipo_reporte' => $tipo_reporte,
                ':nombre_archivo' => $nombre_archivo,
                ':rol' => $rol
            ]);
        } catch (PDOException $e) {
            error_log("[SIGI][Reporte] guardarReporteGenerado: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtiene el historial de reportes
     */
    public function obtenerHistorialReportes(int $limit = 1000, int $offset = 0): array
    {
        $query = "SELECT * FROM reportes_generados 
                  ORDER BY fecha_generacion DESC 
                  LIMIT :limit OFFSET :offset";
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("[SIGI][Reporte] obtenerHistorialReportes: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Elimina un reporte del historial
     */
    public function eliminarReporte(int $id_reporte): bool
    {
        $query = "DELETE FROM reportes_generados WHERE id_reporte = :id";
        try {
            $stmt = $this->conn->prepare($query);
            return $stmt->execute([':id' => $id_reporte]);
        } catch (PDOException $e) {
            error_log("[SIGI][Reporte] eliminarReporte: " . $e->getMessage());
            return false;
        }
    }
}
