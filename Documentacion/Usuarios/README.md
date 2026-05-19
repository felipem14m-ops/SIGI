# Módulo de Usuarios

Este módulo gestiona los usuarios del sistema SIGI, permitiendo el control de acceso y permisos según roles.

## Funcionalidades Documentadas

1. **[Registrar Usuario](logica_registrar_usuario.md)** - Proceso de creación de nuevos usuarios con asignación de roles y encriptación de contraseñas.

2. **[Editar Usuario](logica_editar_usuario.md)** - Modificación de información de usuarios existentes con validaciones de seguridad.

3. **[Desactivar Usuario](logica_desactivar_usuario.md)** - Desactivación de cuentas de usuario sin eliminar registros históricos.

4. **[Consultar Usuarios](logica_consultar_usuarios.md)** - Visualización del listado completo de usuarios con información de roles y estado.

## Archivos Relacionados

- **Vista**: `views/Dashboard/Adminusuarios.php`
- **Controlador**: `controllers/UsuarioController.php`
- **Modelo**: `models/Usuario.php`
- **Tabla Principal**: `usuarios`
- **Tablas Relacionadas**: `roles`

## Características Principales

- Gestión de usuarios con roles (Administrador, Empleado)
- Encriptación de contraseñas con password_hash
- Validación de emails únicos
- Control de acceso basado en roles (RBAC)
- Desactivación sin eliminación física
- Registro de último acceso
- Estados: activo, inactivo
