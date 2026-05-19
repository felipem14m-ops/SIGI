<?php

class Categoria
{
    private PDO $conn;
    private const TABLE = 'categorias';

    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }

    /** Listar todas las categorías con conteo de productos. */
    public function listarTodas(): array
    {
        try {
            $sql = "SELECT
                        c.id_categoria,
                        c.nombre,
                        c.descripcion,
                        c.imagen,
                        c.activa,
                        COUNT(p.id_producto) AS total_productos
                    FROM " . self::TABLE . " c
                    LEFT JOIN productos p ON c.id_categoria = p.id_categoria
                    AND p.estado != 'inactivo'
                    GROUP BY c.id_categoria, c.nombre, c.descripcion, c.imagen, c.activa
                    ORDER BY c.nombre ASC";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("[SIGI][Categoria] listarTodas Error: " . $e->getMessage());
            return [];
        }
    }

    /** Listar solo categorías activas (para selects/formularios). */
    public function listarActivas(): array
    {
        try {
            $sql  = "SELECT id_categoria, nombre, descripcion, imagen FROM " . self::TABLE . " WHERE activa = 1 ORDER BY nombre ASC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("[SIGI][Categoria] listarActivas: " . $e->getMessage());
            return [];
        }
    }

    public function crear(array $datos)
    {
        try {
            $nombre = trim($datos['nombre'] ?? '');
            $desc   = trim($datos['descripcion'] ?? '');
            $img    = $datos['imagen'] ?? null;

            if (empty($nombre)) return 'El nombre es obligatorio.';

            $sql  = "INSERT INTO " . self::TABLE . " (nombre, descripcion, imagen, activa) 
                    VALUES (:nombre, :desc, :img, 1)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':nombre', ucfirst($nombre), PDO::PARAM_STR);
            $stmt->bindValue(':desc',   $desc,            PDO::PARAM_STR);
            $stmt->bindValue(':img',    $img,             PDO::PARAM_STR);
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') return 'Ya existe una categoría con ese nombre.';
            error_log("[SIGI][Categoria] crear Error: " . $e->getMessage());
            return 'Error técnico: ' . $e->getMessage();
        }
    }

    public function actualizar(int $id, array $datos)
    {
        try {
            $nombre = trim($datos['nombre'] ?? '');
            $desc   = trim($datos['descripcion'] ?? '');
            $img    = $datos['imagen'] ?? null;

            $sql  = "UPDATE " . self::TABLE . " 
                    SET nombre = :nombre, descripcion = :desc, imagen = :img 
                    WHERE id_categoria = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':nombre', $nombre, PDO::PARAM_STR);
            $stmt->bindValue(':desc',   $desc,   PDO::PARAM_STR);
            $stmt->bindValue(':img',    $img,    PDO::PARAM_STR);
            $stmt->bindValue(':id',     $id,     PDO::PARAM_INT);
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            error_log("[SIGI][Categoria] actualizar: " . $e->getMessage());
            return 'Error al actualizar la categoría.';
        }
    }

    public function cambiarEstado(int $id, int $activa)
    {
        try {
            $sql  = "UPDATE " . self::TABLE . " SET activa = :activa WHERE id_categoria = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':activa', $activa, PDO::PARAM_INT);
            $stmt->bindValue(':id',     $id,     PDO::PARAM_INT);
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            error_log("[SIGI][Categoria] cambiarEstado: " . $e->getMessage());
            return 'Error al cambiar el estado.';
        }
    }

    public function tieneProductos(int $id): bool
    {
        try {
            $sql = "SELECT COUNT(*) FROM productos WHERE id_categoria = :id AND estado != 'inactivo'";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return (int)$stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("[SIGI][Categoria] tieneProductos: " . $e->getMessage());
            return true;
        }
    }
}
