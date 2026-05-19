<?php

/**
 * ============================================================================
 * Clase: Database
 * Módulo: Configuración Central de Conexión a Base de Datos
 * Sistema: SIGI - Sistema de Gestión de Inventario
 * ============================================================================
 * 
 * PROPÓSITO:
 *   Establece y gestiona la conexión PDO a la base de datos MySQL.
 *   Implementa el patrón Singleton para garantizar una única instancia
 *   de conexión activa durante todo el ciclo de vida de la petición HTTP.
 * 
 * PATRÓN DE DISEÑO:
 *   - Singleton: Previene múltiples conexiones simultáneas que desperdicien
 *     recursos del servidor de base de datos.
 *   - Encapsulamiento: Las credenciales son propiedades privadas, inaccesibles
 *     desde fuera de la clase.
 * 
 * SEGURIDAD:
 *   - Las credenciales están centralizadas en un único punto.
 *   - Se configura PDO en modo EXCEPTION para capturar errores de SQL.
 *   - Se deshabilita la emulación de prepared statements para mayor seguridad.
 *   - Se establece charset UTF-8 para prevenir inyecciones de caracteres.
 * 
 * QA NOTES:
 *   - Verificar que las credenciales coincidan con el entorno (dev/staging/prod).
 *   - Confirmar que el puerto MySQL esté accesible desde el servidor web.
 *   - Validar que el charset utf8mb4 esté soportado por la versión de MySQL.
 *   - En producción, considerar mover credenciales a variables de entorno (.env).
 * 
 * @version  2.0.0
 * @author   SIGI Development Team
 * @since    2026-05-04
 */
class Database
{
    // =========================================================================
    // PROPIEDADES DE CONEXIÓN (Privadas por seguridad)
    // =========================================================================

    /** @var string Dirección del servidor MySQL */
    private $host = "127.0.0.1";

    /** @var string Puerto de conexión MySQL */
    private $port = "3306";

    /** @var string Nombre de la base de datos objetivo */
    private $db_name = "sigi";

    /** @var string Usuario de base de datos */
    private $username = "root";

    /** @var string Contraseña de base de datos */
    private $password = "";

    /** @var PDO|null Instancia de conexión PDO (Singleton) */
    private $conn = null;

    // =========================================================================
    // MÉTODO PRINCIPAL DE CONEXIÓN
    // =========================================================================

    /**
     * Establece y retorna la conexión PDO a la base de datos.
     * 
     * Si ya existe una conexión activa, la reutiliza (Singleton).
     * Configura los atributos de PDO para máxima seguridad y rendimiento.
     * 
     * CONFIGURACIONES DE SEGURIDAD APLICADAS:
     *   1. ERRMODE_EXCEPTION: Lanza excepciones en errores SQL (no warnings silenciosos).
     *   2. EMULATE_PREPARES = false: Usa prepared statements nativos del driver MySQL.
     *   3. DEFAULT_FETCH_MODE = FETCH_ASSOC: Retorna arrays asociativos limpios.
     *   4. charset=utf8mb4: Soporte completo de Unicode incluyendo emojis.
     * 
     * @return PDO Instancia de conexión activa
     * @throws PDOException Si la conexión falla (credenciales, red, permisos)
     * 
     * QA CASE TC-DB-001: Conexión exitosa con credenciales válidas
     * QA CASE TC-DB-002: Excepción controlada con credenciales inválidas
     * QA CASE TC-DB-003: Singleton retorna la misma instancia en múltiples llamadas
     */
    public function conectar(): PDO
    {
        // Patrón Singleton: Si ya hay conexión, la reutiliza
        if ($this->conn !== null) {
            return $this->conn;
        }

        try {
            // Construcción del DSN (Data Source Name) con charset seguro
            $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->db_name};charset=utf8mb4";

            // Opciones de configuración de PDO
            $opciones = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            $this->conn = new PDO($dsn, $this->username, $this->password, $opciones);
        } catch (PDOException $e) {
            // En producción: registrar en log sin exponer detalles al usuario.
            error_log("SIGI Database Error: " . $e->getMessage());
            throw new PDOException("Error de conexión a la base de datos. Contacte al administrador.");
        }

        return $this->conn;
    }
}
