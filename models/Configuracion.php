<?php
/**
 * ============================================================================
 * MODELO: Configuracion
 * Sistema: SIGI — Gestión de Inventario
 * ============================================================================
 * Responsabilidad: Gestión de métodos de pago del sistema.
 * ============================================================================
 */

class Configuracion
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    // =========================================================================
    // MÉTODOS DE PAGO
    // =========================================================================

    /**
     * Lista todos los métodos de pago, opcionalmente solo los activos.
     *
     * @param  bool  $soloActivos Si true, filtra solo activos (activo = 1)
     * @return array
     */
    public function listarMetodos(bool $soloActivos = false): array
    {
        try {
            $sql = 'SELECT * FROM metodos_pago';
            if ($soloActivos) {
                $sql .= ' WHERE activo = 1';
            }
            $sql .= ' ORDER BY id_metodo ASC';

            return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log('[SIGI][Configuracion] listarMetodos: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Crea un nuevo método de pago o actualiza uno existente.
     *
     * @param  string   $nombre Nombre del método de pago
     * @param  int|null $id     Si se pasa, actualiza el registro; si no, crea uno nuevo
     * @return bool
     */
    public function guardarMetodo(string $nombre, ?int $id = null): bool
    {
        try {
            if ($id !== null) {
                $stmt = $this->db->prepare('UPDATE metodos_pago SET nombre = ? WHERE id_metodo = ?');
                return $stmt->execute([$nombre, $id]);
            }

            $stmt = $this->db->prepare('INSERT INTO metodos_pago (nombre, activo) VALUES (?, 1)');
            return $stmt->execute([$nombre]);

        } catch (PDOException $e) {
            error_log('[SIGI][Configuracion] guardarMetodo: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Cambia el estado activo/inactivo de un método de pago.
     *
     * @param  int  $id     ID del método de pago
     * @param  int  $estado 1 = activo, 0 = inactivo
     * @return bool
     */
    public function toggleMetodo(int $id, int $estado): bool
    {
        try {
            $stmt = $this->db->prepare('UPDATE metodos_pago SET activo = ? WHERE id_metodo = ?');
            return $stmt->execute([$estado, $id]);

        } catch (PDOException $e) {
            error_log('[SIGI][Configuracion] toggleMetodo: ' . $e->getMessage());
            return false;
        }
    }
}
