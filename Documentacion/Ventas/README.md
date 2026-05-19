# Módulo de Ventas

Este módulo gestiona el proceso completo de ventas del sistema SIGI, desde el registro de transacciones hasta la consulta del historial.

## Funcionalidades Documentadas

1. **[Procesar Venta](logica_procesar_venta.md)** - Registro de nuevas ventas con múltiples productos, cálculo de totales y actualización automática de inventario.

2. **[Consultar Ventas](logica_consultar_ventas.md)** - Visualización de ventas del día actual con detalles y opciones de impresión.

3. **[Historial de Ventas](logica_historial_ventas.md)** - Consulta completa del historial de ventas con filtros por fecha y usuario.

## Archivos Relacionados

- **Vistas**: 
  - `views/Dashboard/Adminventas.php` (Punto de venta)
  - `views/Dashboard/ventas.php` (Ventas empleado)
  - `views/Dashboard/Adminhistorialventas.php` (Historial)
- **Controlador**: `controllers/VentaController.php`
- **Modelo**: `models/Venta.php`
- **Tablas Principales**: `venta`, `detalleventa`
- **Tablas Relacionadas**: `productos`, `usuarios`, `metodos_pago`, `movimiento_inventario`

## Características Principales

- Procesamiento transaccional de ventas
- Actualización automática de inventario
- Múltiples métodos de pago
- Cálculo automático de cambio
- Generación de tickets de venta
- Historial completo con filtros
- Auditoría de transacciones
- Registro automático de movimientos de inventario
