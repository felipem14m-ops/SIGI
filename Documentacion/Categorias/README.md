# Módulo de Categorías

Este módulo gestiona las categorías de productos del sistema SIGI, permitiendo organizar el catálogo de productos en grupos lógicos.

## Funcionalidades Documentadas

1. **[Registrar Categoría](logica_registrar_categoria.md)** - Proceso de creación de nuevas categorías con nombre, descripción e imagen.

2. **[Editar Categoría](logica_editar_categoria.md)** - Modificación de información de categorías existentes.

3. **[Desactivar Categoría](logica_desactivar_categoria.md)** - Desactivación lógica de categorías sin afectar productos asociados.

4. **[Consultar Categorías](logica_consultar_categorias.md)** - Visualización del listado completo de categorías con contador de productos.

## Archivos Relacionados

- **Vista**: `views/Dashboard/Admincategorias.php`
- **Controlador**: `controllers/CategoriaController.php`
- **Modelo**: `models/Categoria.php`
- **Tabla Principal**: `categorias`
- **Tablas Relacionadas**: `productos`

## Características Principales

- Gestión de categorías con imágenes
- Validación de nombres únicos
- Contador de productos por categoría
- Desactivación sin eliminación física
- Soporte para imágenes JPG, PNG y WEBP
