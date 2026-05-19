# Solución al Error de Integridad en Módulo de Ventas

## Problema Identificado

**Error:** `SQLSTATE[23000]: Integrity constraint violation: 1452`

**Mensaje completo:**
```
Cannot add or update a child row: a foreign key constraint fails 
(`sigi`.`movimiento_inventario`, CONSTRAINT `fk_movimiento_tipo` 
FOREIGN KEY (`id_tipo_movimiento`) REFERENCES `tipos_movimiento` (`id_tipo`))
```

## Causa Raíz

El sistema intenta registrar un movimiento de inventario cuando se realiza una venta, pero la tabla `tipos_movimiento` está vacía. La foreign key `fk_movimiento_tipo` requiere que el `id_tipo_movimiento` exista en la tabla `tipos_movimiento`.

## Solución Implementada

### 1. Script SQL con Datos Iniciales

Se creó el archivo `SQL/datos_iniciales_tipos_movimiento.sql` con los tipos de movimiento necesarios:

| ID | Código | Nombre | Signo |
|----|--------|--------|-------|
| 1 | ENTRADA_COMPRA | Entrada por compra | + |
| 2 | ENTRADA_DEVOLUCION | Entrada por devolución | + |
| 3 | ENTRADA_AJUSTE | Entrada por ajuste de inventario | + |
| 4 | SALIDA_VENTA | Salida por venta | - |
| 5 | SALIDA_MERMA | Salida por merma o pérdida | - |
| 6 | SALIDA_AJUSTE | Salida por ajuste de inventario | - |
| 7 | SALIDA_DEVOLUCION | Salida por devolución a proveedor | - |

### 2. Modificación del Modelo Venta

Se actualizó el método `registrar()` en `models/Venta.php` para:

- Obtener el stock actual antes de cada movimiento
- Validar que hay stock suficiente antes de procesar
- Registrar automáticamente el movimiento de inventario con:
  - `id_tipo_movimiento = 4` (SALIDA_VENTA)
  - `origen = 'automatico'`
  - `motivo = "Venta #[id_venta]"`
  - `stock_anterior` y `stock_resultante` correctamente calculados

### 3. Transaccionalidad Mejorada

El proceso ahora es completamente atómico:
1. Inserta la venta
2. Para cada producto:
   - Obtiene stock actual (con bloqueo FOR UPDATE)
   - Valida stock suficiente
   - Inserta detalle de venta
   - Actualiza stock del producto
   - Registra movimiento de inventario
3. Si cualquier paso falla, hace rollback completo

## Pasos para Aplicar la Solución

### Paso 1: Ejecutar el Script SQL

Conectarse a la base de datos y ejecutar:

```bash
mysql -u [usuario] -p [nombre_base_datos] < SQL/datos_iniciales_tipos_movimiento.sql
```

O desde phpMyAdmin/MySQL Workbench:
1. Abrir el archivo `SQL/datos_iniciales_tipos_movimiento.sql`
2. Ejecutar el script completo

### Paso 2: Verificar los Datos

```sql
SELECT * FROM tipos_movimiento ORDER BY id_tipo;
```

Debe mostrar 7 registros.

### Paso 3: Probar el Módulo de Ventas

1. Acceder al módulo de ventas
2. Agregar productos al carrito
3. Procesar una venta
4. Verificar que:
   - La venta se registra correctamente
   - El stock se descuenta
   - Se crea el movimiento de inventario

### Paso 4: Verificar Movimientos de Inventario

```sql
SELECT m.*, t.nombre as tipo_movimiento, p.nombre as producto
FROM movimiento_inventario m
LEFT JOIN tipos_movimiento t ON m.id_tipo_movimiento = t.id_tipo
LEFT JOIN productos p ON m.id_producto = p.id_producto
WHERE m.origen = 'automatico'
ORDER BY m.fecha DESC
LIMIT 10;
```

## Beneficios de la Solución

1. **Trazabilidad completa**: Cada venta genera automáticamente su movimiento de inventario
2. **Integridad de datos**: Stock y movimientos siempre están sincronizados
3. **Auditoría**: Historial completo de todos los movimientos de stock
4. **Prevención de errores**: Validación de stock antes de procesar la venta
5. **Transaccionalidad**: Si algo falla, todo se revierte (rollback)

## Notas Adicionales

- El `id_tipo_movimiento = 4` está hardcodeado en el modelo Venta para "SALIDA_VENTA"
- Si se necesita cambiar este ID, modificar la línea en `models/Venta.php`:
  ```php
  $sqlMovimiento = "INSERT INTO movimiento_inventario
                    (id_producto, id_usuario, id_tipo_movimiento, ...
                    VALUES (:id_producto, :id_usuario, 4, ...
  ```
- Los movimientos automáticos tienen `origen = 'automatico'` para diferenciarlos de los manuales

## Prevención de Problemas Futuros

1. **Siempre ejecutar scripts de datos iniciales** al configurar una nueva base de datos
2. **Documentar las dependencias** entre tablas y sus datos requeridos
3. **Validar foreign keys** antes de insertar datos
4. **Usar transacciones** para operaciones que afectan múltiples tablas

## Contacto y Soporte

Si el problema persiste después de aplicar esta solución:
1. Verificar que el script SQL se ejecutó correctamente
2. Revisar los logs de error de PHP (`error_log`)
3. Verificar permisos de usuario en la base de datos
4. Comprobar que la versión de MySQL/MariaDB es compatible

---
**Fecha de creación:** 2026-05-10  
**Versión:** 1.0.0  
**Sistema:** SIGI - Sistema Integral de Gestión de Inventario
