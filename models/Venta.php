<?php
/**
 * ============================================================================
 * MODELO: Venta
 * Sistema: SIGI — Gestión de Inventario
 * ============================================================================
 */

class Venta
{
    private PDO $conn;

    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }

    /**
     * Procesa una venta completa de forma atómica (Transaccional).
     */
    public function procesarVenta(array $datosVenta, array $productos): bool|string
    {
        try {
            $this->conn->beginTransaction();

            // 1. Registrar la Venta Principal (Tabla: venta)
            $sqlVenta = "INSERT INTO venta (id_usuario, id_metodo, total, monto_recibido, cambio_devuelto, estado) 
                         VALUES (:user, :metodo, :total, :monto_recibido, :cambio_devuelto, 'completada')";
            $stmtVenta = $this->conn->prepare($sqlVenta);
            $stmtVenta->execute([
                ':user'            => $datosVenta['id_usuario'],
                ':metodo'          => $datosVenta['id_metodo'],
                ':total'           => $datosVenta['total'],
                ':monto_recibido'  => $datosVenta['monto_recibido'] ?? null,
                ':cambio_devuelto' => $datosVenta['cambio_devuelto'] ?? null
            ]);
            
            $idVenta = $this->conn->lastInsertId();

            // 2. Procesar cada producto (Tabla: detalleventa)
            foreach ($productos as $item) {
                $idProd   = $item['id_producto'];
                $cantidad = $item['cantidad'];
                $precio   = $item['precio'];

                // Bloqueo de stock
                $stmtStock = $this->conn->prepare("SELECT stock_actual, nombre FROM productos WHERE id_producto = ? FOR UPDATE");
                $stmtStock->execute([$idProd]);
                $productoData = $stmtStock->fetch(PDO::FETCH_ASSOC);

                if (!$productoData || $productoData['stock_actual'] < $cantidad) {
                    throw new Exception("Stock insuficiente para: " . ($productoData['nombre'] ?? "ID $idProd"));
                }

                // Registrar detalle (Tabla: detalleventa)
                $sqlDetalle = "INSERT INTO detalleventa (id_venta, id_producto, cantidad, precioUnitario, subtotal) 
                               VALUES (?, ?, ?, ?, ?)";
                $this->conn->prepare($sqlDetalle)->execute([
                    $idVenta, $idProd, $cantidad, $precio, ($cantidad * $precio)
                ]);

                // Actualizar stock
                $nuevoStock = $productoData['stock_actual'] - $cantidad;
                $this->conn->prepare("UPDATE productos SET stock_actual = ? WHERE id_producto = ?")->execute([$nuevoStock, $idProd]);

                // Registrar movimiento de inventario
                $sqlMov = "INSERT INTO movimiento_inventario 
                           (id_producto, id_usuario, id_tipo_movimiento, cantidad, stock_anterior, stock_resultante, origen, motivo) 
                           VALUES (?, ?, 2, ?, ?, ?, 'automatico', ?)";
                $this->conn->prepare($sqlMov)->execute([
                    $idProd, $datosVenta['id_usuario'], $cantidad, $productoData['stock_actual'], $nuevoStock, "Venta registrada #$idVenta"
                ]);
            }

            $this->conn->commit();
            return $idVenta;

        } catch (Exception $e) {
            if ($this->conn->inTransaction()) $this->conn->rollBack();
            error_log("[SIGI] Venta Error: " . $e->getMessage());
            return $e->getMessage();
        }
    }
    /**
     * Listar ventas con filtros opcionales.
     * 
     * @param string $filtroFecha   Fecha en formato YYYY-MM-DD
     * @param string $filtroUsuario Nombre parcial del cajero
     * @param int    $id_usuario    ID específico del usuario (0 para todos)
     * @return array
     */
    public function listarVentas(string $filtroFecha = '', string $filtroUsuario = '', int $id_usuario = 0): array
    {
        try {
            $sql = "SELECT v.*, u.nombre as nombre_usuario, mp.nombre as metodo_pago,
                    (SELECT SUM(cantidad) FROM detalleventa WHERE id_venta = v.id_venta) as total_items
                    FROM venta v
                    JOIN usuarios u ON v.id_usuario = u.id_usuario
                    JOIN metodos_pago mp ON v.id_metodo = mp.id_metodo";
            
            $conditions = [];
            $params = [];
            
            if ($filtroFecha) {
                $conditions[] = "DATE(v.fecha_venta) = :fecha";
                $params[':fecha'] = $filtroFecha;
            }
            
            if ($filtroUsuario) {
                $conditions[] = "u.nombre LIKE :usuario";
                $params[':usuario'] = "%{$filtroUsuario}%";
            }

            if ($id_usuario > 0) {
                $conditions[] = "v.id_usuario = :id_usuario";
                $params[':id_usuario'] = $id_usuario;
            }
            
            if (!empty($conditions)) {
                $sql .= " WHERE " . implode(" AND ", $conditions);
            }
            
            $sql .= " ORDER BY v.fecha_venta DESC";
            
            $stmt = $this->conn->prepare($sql);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Obtiene la cabecera de una venta con datos del cajero y método de pago.
     *
     * @param  int        $idVenta ID de la venta
     * @return array|false
     */
    public function obtenerCabeceraVenta(int $idVenta): array|false
    {
        try {
            $sql = "SELECT
                        v.*,
                        u.nombre  AS cajero,
                        mp.nombre AS metodo
                    FROM venta v
                    JOIN usuarios     u  ON v.id_usuario = u.id_usuario
                    JOIN metodos_pago mp ON v.id_metodo  = mp.id_metodo
                    WHERE v.id_venta = :id
                    LIMIT 1";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':id', $idVenta, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: false;

        } catch (PDOException $e) {
            error_log('[SIGI][Venta] obtenerCabeceraVenta: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtiene el detalle de los productos de una venta.
     *
     * @param  int   $idVenta
     * @return array
     */
    public function obtenerDetalleVenta(int $idVenta): array
    {
        try {
            $sql = "SELECT
                        dv.*,
                        p.nombre     AS nombre_producto,
                        p.codigoUnico,
                        dv.precioUnitario AS precio_unitario
                    FROM detalleventa dv
                    JOIN productos p ON dv.id_producto = p.id_producto
                    WHERE dv.id_venta = :id";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':id', $idVenta, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log('[SIGI][Venta] obtenerDetalleVenta: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Resumen de ventas del día para el dashboard.
     *
     * @return array ['total_ventas', 'total_ingresos']
     */
    public function resumenDiario(int $id_usuario = 0): array
    {
        try {
            $sql = "SELECT COUNT(*) AS total_ventas, IFNULL(SUM(total), 0) AS total_ingresos
                    FROM venta
                    WHERE DATE(fecha_venta) = CURDATE()";
            
            if ($id_usuario > 0) {
                $sql .= " AND id_usuario = :id_user";
            }
            
            $stmt = $this->conn->prepare($sql);
            if ($id_usuario > 0) {
                $stmt->bindValue(':id_user', $id_usuario, PDO::PARAM_INT);
            }
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: ['total_ventas' => 0, 'total_ingresos' => 0];
        } catch (PDOException $e) {
            error_log('[SIGI][Venta] resumenDiario: ' . $e->getMessage());
            return ['total_ventas' => 0, 'total_ingresos' => 0];
        }
    }

    /**
     * Lista las ventas registradas hoy.
     *
     * @return array
     */
    public function listarVentasHoy(int $id_usuario = 0): array
    {
        return $this->listarVentas(date('Y-m-d'), '', $id_usuario);
    }
}
