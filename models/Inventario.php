<?php
/**
 * ============================================================================
 * MODELO: Inventario (Kardex de movimientos)
 * Sistema: SIGI — Gestión de Inventario
 * ============================================================================
 * Responsabilidad: Registrar y consultar movimientos de stock.
 * Las tablas de soporte se crean desde la migración SQL, no desde aquí.
 * ============================================================================
 */

class Inventario
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    // =========================================================================
    // LECTURA
    // =========================================================================

    /**
     * Lista los últimos N movimientos de inventario con sus relaciones.
     *
     * @param  int   $limite Máximo de registros a devolver (default: 500)
     * @return array
     */
    public function listarMovimientos(int $limite = 500): array
    {
        try {
                $sql = "SELECT
                        m.*,
                        p.nombre  AS nombre_producto,
                        u.nombre  AS nombre_usuario,
                        CASE WHEN tm.nombre = 'Ajuste' THEN 'Merma' ELSE tm.nombre END AS tipo_movimiento
                    FROM movimiento_inventario m
                    JOIN productos       p  ON m.id_producto       = p.id_producto
                    JOIN usuarios        u  ON m.id_usuario        = u.id_usuario
                    JOIN tipos_movimiento tm ON m.id_tipo_movimiento = tm.id_tipo
                    ORDER BY m.fecha DESC
                    LIMIT :limite";

            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log('[SIGI][Inventario] listarMovimientos: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Devuelve los tipos de movimiento disponibles (Entrada, Salida, Ajuste).
     *
     * @return array
     */
    public function obtenerTiposMovimiento(): array
    {
        try {
            $stmt = $this->db->query("SELECT id_tipo AS id_tipo_movimiento, CASE WHEN nombre = 'Ajuste' THEN 'Merma' ELSE nombre END AS nombre FROM tipos_movimiento ORDER BY id_tipo ASC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('[SIGI][Inventario] obtenerTiposMovimiento: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Resumen de stock para el dashboard de inventario.
     *
     * @return array ['total_items', 'bajo_stock', 'agotados']
     */
    public function obtenerResumenStock(): array
    {
        try {
            $sql = "SELECT
                        COUNT(*) AS total_items,
                        SUM(CASE WHEN stock_actual <= 5 AND stock_actual > 0 THEN 1 ELSE 0 END) AS bajo_stock,
                        SUM(CASE WHEN stock_actual <= 0                       THEN 1 ELSE 0 END) AS agotados
                    FROM productos";

            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: ['total_items' => 0, 'bajo_stock' => 0, 'agotados' => 0];

        } catch (PDOException $e) {
            error_log('[SIGI][Inventario] obtenerResumenStock: ' . $e->getMessage());
            return ['total_items' => 0, 'bajo_stock' => 0, 'agotados' => 0];
        }
    }

    // =========================================================================
    // ESCRITURA
    // =========================================================================

    /**
     * Registra un movimiento de inventario y actualiza el stock del producto.
     * Opera en una transacción atómica para garantizar consistencia.
     *
     * Lógica de tipo:
     *   1 = Entrada  → suma cantidad al stock
     *   2 = Salida   → resta cantidad (valida stock suficiente)
     *   3 = Ajuste   → aplica cantidad directamente (puede ser positiva o negativa)
     *
     * @param  int    $idProducto ID del producto
     * @param  int    $idUsuario  ID del usuario que registra
     * @param  int    $idTipo     1=Entrada, 2=Salida, 3=Ajuste
     * @param  int    $cantidad   Unidades del movimiento
     * @param  string $motivo     Descripción del motivo
     * @return true|string        true si éxito, mensaje de error si falla
     */
    public function registrarMovimiento(
        int    $idProducto,
        int    $idUsuario,
        int    $idTipo,
        int    $cantidad,
        string $motivo
    ): bool|string 
    {
        try {
            $this->db->beginTransaction();

            // Bloquear la fila del producto para evitar condiciones de carrera
            $stmt = $this->db->prepare('SELECT stock_actual FROM productos WHERE id_producto = ? FOR UPDATE');
            $stmt->execute([$idProducto]);
            $producto = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$producto) {
                throw new \Exception('Producto no encontrado.');
            }

            $stockAnterior = (int) $producto['stock_actual'];

            // Calcular nuevo stock según tipo de movimiento
            if ($idTipo === 1) {                     // Entrada
                $stockResultante = $stockAnterior + abs($cantidad);
                $cantidadReal    = abs($cantidad);
            } elseif ($idTipo === 2) {               // Salida
                if ($stockAnterior < abs($cantidad)) {
                    throw new \Exception('Stock insuficiente para registrar la salida.');
                }
                $stockResultante = $stockAnterior - abs($cantidad);
                $cantidadReal    = -abs($cantidad);
            } else {                                  // Ajuste (tipo 3)
                $stockResultante = $stockAnterior + $cantidad;
                $cantidadReal    = $cantidad;
            }

            // Actualizar stock en tabla productos
            $this->db
                ->prepare('UPDATE productos SET stock_actual = ? WHERE id_producto = ?')
                ->execute([$stockResultante, $idProducto]);

            // Registrar entrada en el kardex
            $this->db
                ->prepare("INSERT INTO movimiento_inventario
                                (id_producto, id_usuario, id_tipo_movimiento, cantidad,
                                 stock_anterior, stock_resultante, origen, motivo)
                            VALUES (?, ?, ?, ?, ?, ?, 'manual', ?)")
                ->execute([
                    $idProducto, $idUsuario, $idTipo,
                    $cantidadReal, $stockAnterior, $stockResultante,
                    $motivo,
                ]);

            $this->db->commit();
            return true;

        } catch (\Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log('[SIGI][Inventario] registrarMovimiento: ' . $e->getMessage());
            return $e->getMessage();
        }
    }
}
