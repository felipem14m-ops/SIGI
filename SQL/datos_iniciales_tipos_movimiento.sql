-- ============================================================================
-- Script: Datos iniciales para tipos_movimiento
-- Sistema: SIGI - Gestión de Inventario
-- Descripción: Inserta los tipos de movimiento básicos necesarios para el
--              funcionamiento del sistema de inventario
-- ============================================================================

-- Limpiar datos existentes (opcional, comentar si ya hay datos)
-- TRUNCATE TABLE tipos_movimiento;

-- Insertar tipos de movimiento básicos
INSERT INTO `tipos_movimiento` (`id_tipo`, `codigo`, `nombre`, `signo`) VALUES
(1, 'ENTRADA_COMPRA', 'Entrada por compra', '+'),
(2, 'ENTRADA_DEVOLUCION', 'Entrada por devolución', '+'),
(3, 'ENTRADA_AJUSTE', 'Entrada por ajuste de inventario', '+'),
(4, 'SALIDA_VENTA', 'Salida por venta', '-'),
(5, 'SALIDA_MERMA', 'Salida por merma o pérdida', '-'),
(6, 'SALIDA_AJUSTE', 'Salida por ajuste de inventario', '-'),
(7, 'SALIDA_DEVOLUCION', 'Salida por devolución a proveedor', '-');

-- Verificar inserción
SELECT * FROM tipos_movimiento ORDER BY id_tipo;
