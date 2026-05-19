# Módulo de Inventario

Este módulo gestiona los movimientos de inventario del sistema SIGI, permitiendo el control y trazabilidad de entradas y salidas de productos.

## Funcionalidades Documentadas

1. **[Registrar Movimiento](logica_registrar_movimiento.md)** - Registro manual de movimientos de inventario (entradas, salidas, ajustes) con actualización automática de stock.

2. **[Consultar Kardex](logica_consultar_kardex.md)** - Visualización del historial completo de movimientos de inventario con filtros avanzados.

3. **[Alertas de Stock](logica_alertas_stock.md)** - Sistema de alertas automáticas para productos con stock bajo o crítico.

## Archivos Relacionados

- **Vistas**: 
  - `views/Dashboard/Admininventario.php` (Gestión de movimientos)
  - `views/Dashboard/Adminalertas.php` (Alertas de stock)
- **Controlador**: `controllers/InventarioController.php`
- **Modelo**: `models/Inventario.php`
- **Tabla Principal**: `movimiento_inventario`
- **Tablas Relacionadas**: `productos`, `usuarios`, `tipo_movimiento`

## Características Principales

- Registro de movimientos de entrada y salida
- Ajustes de inventario con justificación
- Kardex completo con stock anterior y resultante
- Alertas automáticas de stock bajo
- Trazabilidad completa de movimientos
- Origen automático o manual
- Filtros por producto, tipo y fecha
- Auditoría de responsables
