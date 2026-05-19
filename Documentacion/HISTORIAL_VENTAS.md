# 📊 Módulo de Historial de Ventas

## Descripción

Módulo completo para consultar y filtrar el historial de ventas del sistema, mostrando información detallada incluyendo el **rol del usuario** y el **tipo de movimiento de inventario** asociado a cada venta.

## Características Implementadas

### ✅ Vista de Historial (`views/Dashboard/historial_ventas.php`)

**Información mostrada en la tabla:**
- **Folio**: Número de venta con formato #00001
- **Fecha y Hora**: Fecha y hora de la transacción
- **Usuario/Cajero**: Nombre del vendedor con avatar
- **Rol**: Badge con el rol del usuario (Admin, Empleado, Cajero)
- **Método de Pago**: Efectivo, Tarjeta, etc.
- **Tipo de Movimiento**: Tipo de movimiento de inventario registrado
- **Items**: Cantidad de productos vendidos
- **Total**: Monto total de la venta
- **Acciones**: Botón para ver detalle

### 🔍 Filtros Disponibles

1. **Fecha Inicio**: Filtrar desde una fecha específica
2. **Fecha Fin**: Filtrar hasta una fecha específica
3. **Rol**: Filtrar por rol del usuario (Admin, Empleado, Cajero, etc.)
4. **Método de Pago**: Filtrar por método de pago utilizado
5. **Estado**: Filtrar por estado (Completada, Anulada)

### 📈 Estadísticas en Tiempo Real

- **Total Ventas**: Cantidad de ventas en el período filtrado
- **Ingresos Totales**: Suma total de ingresos
- **Productos Vendidos**: Total de items vendidos

### 🎨 Diseño Visual

- **Badges de Rol con colores distintivos:**
  - Admin: Azul
  - Empleado: Verde
  - Cajero: Amarillo

- **Interfaz moderna y responsive**
- **Tabla con hover effects**
- **Iconos intuitivos**
- **Botón de impresión**

## Modelo Actualizado

### Nuevos Métodos en `models/Venta.php`

#### 1. `listarVentasConFiltros(array $filtros)`

Obtiene ventas con filtros opcionales:

```php
$filtros = [
    'fecha_inicio' => '2026-05-01',
    'fecha_fin'    => '2026-05-10',
    'id_rol'       => 1,           // Opcional
    'id_metodo'    => 2,           // Opcional
    'estado'       => 'completada', // Opcional
    'limite'       => 100          // Opcional
];

$ventas = $ventaModel->listarVentasConFiltros($filtros);
```

**Retorna:**
- `id_venta`: ID de la venta
- `nombre_vendedor`: Nombre del usuario que realizó la venta
- `rol_vendedor`: Nombre del rol del usuario
- `id_rol`: ID del rol
- `metodo_pago`: Nombre del método de pago
- `tipo_movimiento`: Tipo de movimiento de inventario asociado
- `total_items`: Cantidad de productos en la venta
- `total`: Monto total
- `fecha_venta`: Fecha y hora de la venta
- `estado`: Estado de la venta

#### 2. `obtenerRoles()`

Obtiene todos los roles disponibles para los filtros:

```php
$roles = $ventaModel->obtenerRoles();
```

#### 3. `listarVentasHoy()` - Actualizado

Ahora incluye el rol del vendedor:

```php
$ventasHoy = $ventaModel->listarVentasHoy();
```

## Cómo Usar

### 1. Acceder al Módulo

Agregar al menú de navegación (sidebar):

```php
<a href="historial_ventas.php" class="nav-link">
    <i class="fas fa-history"></i>
    <span>Historial de Ventas</span>
</a>
```

### 2. Consultar Ventas

**Sin filtros (mes actual):**
```
http://localhost/tu-proyecto/views/Dashboard/historial_ventas.php
```

**Con filtros:**
```
http://localhost/tu-proyecto/views/Dashboard/historial_ventas.php?fecha_inicio=2026-05-01&fecha_fin=2026-05-10&id_rol=1
```

### 3. Filtrar por Rol

1. Seleccionar el rol en el dropdown
2. Clic en "Aplicar Filtros"
3. La tabla mostrará solo las ventas de ese rol

### 4. Exportar/Imprimir

Clic en el botón "Imprimir" para generar una versión imprimible del reporte.

## Consultas SQL Utilizadas

### Obtener Ventas con Rol y Tipo de Movimiento

```sql
SELECT v.*, 
       u.nombre as nombre_vendedor, 
       r.nombre as rol_vendedor,
       r.id_rol,
       mp.nombre as metodo_pago,
       (SELECT COUNT(*) FROM detalleventa d WHERE d.id_venta = v.id_venta) as total_items,
       (SELECT tm.nombre 
        FROM movimiento_inventario mi 
        LEFT JOIN tipos_movimiento tm ON mi.id_tipo_movimiento = tm.id_tipo
        WHERE mi.motivo LIKE CONCAT('Venta #', v.id_venta)
        LIMIT 1) as tipo_movimiento
FROM venta v
LEFT JOIN usuarios u      ON v.id_usuario = u.id_usuario
LEFT JOIN roles r         ON u.id_rol     = r.id_rol
LEFT JOIN metodos_pago mp ON v.id_metodo  = mp.id_metodo
WHERE DATE(v.fecha_venta) BETWEEN '2026-05-01' AND '2026-05-10'
  AND r.id_rol = 1
ORDER BY v.fecha_venta DESC;
```

## Ejemplos de Uso

### Ejemplo 1: Ventas de Administradores del Mes

```php
$filtros = [
    'fecha_inicio' => date('Y-m-01'),
    'fecha_fin'    => date('Y-m-d'),
    'id_rol'       => 1 // ID del rol Admin
];
$ventas = $ventaModel->listarVentasConFiltros($filtros);
```

### Ejemplo 2: Ventas en Efectivo de la Semana

```php
$filtros = [
    'fecha_inicio' => date('Y-m-d', strtotime('-7 days')),
    'fecha_fin'    => date('Y-m-d'),
    'id_metodo'    => 1 // ID del método Efectivo
];
$ventas = $ventaModel->listarVentasConFiltros($filtros);
```

### Ejemplo 3: Últimas 50 Ventas Completadas

```php
$filtros = [
    'estado' => 'completada',
    'limite' => 50
];
$ventas = $ventaModel->listarVentasConFiltros($filtros);
```

## Beneficios

✅ **Trazabilidad completa**: Saber quién realizó cada venta y su rol
✅ **Auditoría**: Verificar movimientos de inventario asociados
✅ **Análisis por rol**: Comparar desempeño entre roles
✅ **Filtros flexibles**: Múltiples criterios de búsqueda
✅ **Interfaz intuitiva**: Fácil de usar y visualmente atractiva
✅ **Responsive**: Funciona en desktop, tablet y móvil

## Próximas Mejoras (Opcional)

- [ ] Exportar a Excel/CSV
- [ ] Gráficos de ventas por rol
- [ ] Comparativa entre períodos
- [ ] Detalle de venta en modal
- [ ] Paginación de resultados
- [ ] Búsqueda por nombre de usuario
- [ ] Filtro por rango de montos

## Notas Técnicas

- El tipo de movimiento se obtiene mediante una subconsulta que busca en `movimiento_inventario` el registro con motivo "Venta #[id_venta]"
- Los filtros son opcionales y se pueden combinar
- Por defecto muestra las ventas del mes actual
- La consulta está optimizada con índices en las tablas relacionadas

---
**Fecha de creación:** 2026-05-10  
**Versión:** 1.0.0  
**Sistema:** SIGI - Sistema Integral de Gestión de Inventario
