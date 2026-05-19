# Módulo de Proveedores

Este módulo gestiona la información de proveedores del sistema SIGI, permitiendo mantener un registro completo de los suministradores de productos.

## Funcionalidades Documentadas

1. **[Registrar Proveedor](logica_registrar_proveedor.md)** - Proceso de registro de nuevos proveedores con datos de contacto y fiscales.

2. **[Editar Proveedor](logica_editar_proveedor.md)** - Modificación de información de proveedores existentes.

3. **[Desactivar Proveedor](logica_desactivar_proveedor.md)** - Desactivación lógica de proveedores sin afectar productos asociados.

4. **[Consultar Proveedores](logica_consultar_proveedores.md)** - Visualización del listado completo de proveedores con información de contacto.

## Archivos Relacionados

- **Vista**: `views/Dashboard/Adminproveedores.php`
- **Controlador**: `controllers/ProveedorController.php`
- **Modelo**: `models/Proveedor.php`
- **Tabla Principal**: `proveedores`
- **Tablas Relacionadas**: `productos`

## Características Principales

- Gestión completa de datos de proveedores
- Información de contacto: teléfono, email, dirección
- Datos fiscales: NIT, razón social
- Contador de productos por proveedor
- Desactivación sin eliminación física
- Validación de datos de contacto
