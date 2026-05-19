# Módulo de Productos

Este módulo gestiona el catálogo completo de productos del sistema SIGI, permitiendo el registro, edición, consulta y desactivación de productos.

## Funcionalidades Documentadas

1. **[Registrar Producto](logica_registrar_producto.md)** - Proceso completo de registro de nuevos productos con validaciones, imágenes y datos de inventario inicial.

2. **[Editar Producto](logica_editar_producto.md)** - Modificación de información de productos existentes manteniendo la integridad de datos históricos.

3. **[Desactivar Producto](logica_desactivar_producto.md)** - Desactivación lógica de productos sin eliminar registros de la base de datos.

4. **[Consultar Productos](logica_consultar_productos.md)** - Visualización y búsqueda del catálogo completo con filtros avanzados y alertas de stock.

## Archivos Relacionados

- **Vista**: `views/Dashboard/Adminproductos.php`
- **Controlador**: `controllers/ProductoController.php`
- **Modelo**: `models/Producto.php`
- **Tabla Principal**: `productos`
- **Tablas Relacionadas**: `categorias`, `proveedores`

## Características Principales

- Gestión completa del ciclo de vida del producto
- Validación de códigos únicos
- Carga y gestión de imágenes de productos
- Control de stock mínimo y alertas
- Integración con categorías y proveedores
- Fechas de vencimiento opcionales
- Estados: activo, inactivo, agotado
