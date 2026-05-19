-- ============================================================================
-- Script: Verificación de Configuración del Sistema
-- Sistema: SIGI - Gestión de Inventario
-- Descripción: Verifica que todas las tablas y datos necesarios estén
--              correctamente configurados
-- ============================================================================

-- 1. Verificar que existen tipos de movimiento
SELECT 'Verificando tipos_movimiento...' as paso;
SELECT COUNT(*) as total_tipos,
       SUM(CASE WHEN signo = '+' THEN 1 ELSE 0 END) as entradas,
       SUM(CASE WHEN signo = '-' THEN 1 ELSE 0 END) as salidas
FROM tipos_movimiento;

-- 2. Listar todos los tipos de movimiento
SELECT 'Listado de tipos de movimiento:' as paso;
SELECT id_tipo, codigo, nombre, signo FROM tipos_movimiento ORDER BY id_tipo;

-- 3. Verificar que existe el tipo SALIDA_VENTA (ID 4)
SELECT 'Verificando tipo SALIDA_VENTA (requerido para ventas)...' as paso;
SELECT * FROM tipos_movimiento WHERE id_tipo = 4 OR codigo = 'SALIDA_VENTA';

-- 4. Verificar integridad de foreign keys en movimiento_inventario
SELECT 'Verificando integridad de movimientos de inventario...' as paso;
SELECT 
    COUNT(*) as total_movimientos,
    COUNT(DISTINCT id_tipo_movimiento) as tipos_usados,
    SUM(CASE WHEN origen = 'automatico' THEN 1 ELSE 0 END) as automaticos,
    SUM(CASE WHEN origen = 'manual' THEN 1 ELSE 0 END) as manuales
FROM movimiento_inventario;

-- 5. Verificar que no hay movimientos huérfanos (sin tipo válido)
SELECT 'Buscando movimientos con tipo inválido...' as paso;
SELECT m.id_movimiento, m.id_tipo_movimiento, m.fecha
FROM movimiento_inventario m
LEFT JOIN tipos_movimiento t ON m.id_tipo_movimiento = t.id_tipo
WHERE t.id_tipo IS NULL;

-- 6. Verificar métodos de pago
SELECT 'Verificando métodos de pago...' as paso;
SELECT COUNT(*) as total_metodos, 
       SUM(CASE WHEN activo = 1 THEN 1 ELSE 0 END) as activos
FROM metodos_pago;

-- 7. Resumen de configuración
SELECT 'RESUMEN DE CONFIGURACIÓN' as paso;
SELECT 
    (SELECT COUNT(*) FROM tipos_movimiento) as tipos_movimiento,
    (SELECT COUNT(*) FROM metodos_pago WHERE activo = 1) as metodos_pago_activos,
    (SELECT COUNT(*) FROM categorias WHERE activa = 1) as categorias_activas,
    (SELECT COUNT(*) FROM productos WHERE estado = 'activo') as productos_activos,
    (SELECT COUNT(*) FROM usuarios WHERE activo = 1) as usuarios_activos;

-- 8. Estado del sistema
SELECT 'ESTADO DEL SISTEMA' as paso;
SELECT 
    CASE 
        WHEN (SELECT COUNT(*) FROM tipos_movimiento) >= 4 THEN '✓ OK'
        ELSE '✗ FALTA CONFIGURAR'
    END as tipos_movimiento_status,
    CASE 
        WHEN (SELECT COUNT(*) FROM tipos_movimiento WHERE id_tipo = 4) = 1 THEN '✓ OK'
        ELSE '✗ FALTA TIPO SALIDA_VENTA'
    END as tipo_venta_status,
    CASE 
        WHEN (SELECT COUNT(*) FROM metodos_pago WHERE activo = 1) > 0 THEN '✓ OK'
        ELSE '✗ FALTA CONFIGURAR'
    END as metodos_pago_status;
