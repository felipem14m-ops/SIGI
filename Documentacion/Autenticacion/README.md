# Módulo de Autenticación

Este módulo gestiona el acceso seguro al sistema SIGI, controlando el inicio y cierre de sesión de usuarios.

## Funcionalidades Documentadas

1. **[Login](logica_login.md)** - Proceso de autenticación de usuarios con validación de credenciales, control de intentos fallidos y redirección según rol.

2. **[Logout](logica_logout.md)** - Cierre seguro de sesión con limpieza completa de variables de sesión.

## Archivos Relacionados

- **Vistas**: 
  - `views/Usuario/login.php` (Formulario de inicio de sesión)
- **Controlador**: `controllers/AuthController.php`
- **Modelo**: `models/Usuario.php`
- **Tabla Principal**: `usuarios`
- **Tablas Relacionadas**: `roles`

## Características Principales

- Autenticación segura con password_verify
- Validación de formato de email
- Control de cuentas activas/inactivas
- Prevención de ataques de fuerza bruta
- Bloqueo temporal por intentos fallidos
- Regeneración de ID de sesión (prevención de Session Fixation)
- Redirección basada en roles (RBAC)
- Registro de último acceso
- Cierre de sesión seguro
