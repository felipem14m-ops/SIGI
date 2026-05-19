# Documentación Técnica - Sistema SIGI

**Sistema Integral de Gestión de Inventario**

Esta documentación describe la lógica y funcionamiento de todas las funcionalidades del sistema SIGI. Cada archivo explica de forma narrativa cómo funciona cada proceso, permitiendo recuperar o reconstruir el código si es necesario.

## Estructura de la Documentación

La documentación está organizada por módulos funcionales. Cada módulo contiene:
- Un archivo `README.md` con el índice de funcionalidades
- Archivos individuales `logica_*.md` con la descripción detallada de cada funcionalidad

---

## 📦 Módulos del Sistema

### 1. [Autenticación](Autenticacion/)
Control de acceso y seguridad del sistema.

**Funcionalidades:**
- [Login](Autenticacion/logica_login.md) - Inicio de sesión con validación de credenciales
- [Logout](Autenticacion/logica_logout.md) - Cierre seguro de sesión

---

### 2. [Productos](Productos/)
Gestión del catálogo de productos.

**Funcionalidades:**
- [Registrar Producto](Productos/logica_registrar_producto.md) - Creación de nuevos productos
- [Editar Producto](Productos/logica_editar_producto.md) - Modificación de productos existentes
- [Desactivar Producto](Productos/logica_desactivar_producto.md) - Desactivación lógica de productos
- [Consultar Productos](Productos/logica_consultar_productos.md) - Listado y búsqueda de productos

---

### 3. [Categorías](Categorias/)
Organización de productos en categorías.

**Funcionalidades:**
- [Registrar Categoría](Categorias/logica_registrar_categoria.md) - Creación de nuevas categorías
- [Editar Categoría](Categorias/logica_editar_categoria.md) - Modificación de categorías existentes
- [Desactivar Categoría](Categorias/logica_desactivar_categoria.md) - Desactivación lógica de categorías
- [Consultar Categorías](Categorias/logica_consultar_categorias.md) - Listado de categorías

---

### 4. [Proveedores](Proveedores/)
Gestión de proveedores y suministradores.

**Funcionalidades:**
- [Registrar Proveedor](Proveedores/logica_registrar_proveedor.md) - Creación de nuevos proveedores
- [Editar Proveedor](Proveedores/logica_editar_proveedor.md) - Modificación de proveedores existentes
- [Desactivar Proveedor](Proveedores/logica_desactivar_proveedor.md) - Desactivación lógica de proveedores
- [Consultar Proveedores](Proveedores/logica_consultar_proveedores.md) - Listado de proveedores

---

### 5. [Inventario](Inventario/)
Control de movimientos y stock de productos.

**Funcionalidades:**
- [Registrar Movimiento](Inventario/logica_registrar_movimiento.md) - Registro de entradas, salidas y ajustes
- [Consultar Kardex](Inventario/logica_consultar_kardex.md) - Historial de movimientos de inventario
- [Alertas de Stock](Inventario/logica_alertas_stock.md) - Sistema de alertas de stock bajo

---

### 6. [Ventas](Ventas/)
Procesamiento de transacciones comerciales.

**Funcionalidades:**
- [Procesar Venta](Ventas/logica_procesar_venta.md) - Registro de nuevas ventas
- [Consultar Ventas](Ventas/logica_consultar_ventas.md) - Visualización de ventas del día
- [Historial de Ventas](Ventas/logica_historial_ventas.md) - Consulta completa del historial

---

### 7. [Usuarios](Usuarios/)
Administración de usuarios del sistema.

**Funcionalidades:**
- [Registrar Usuario](Usuarios/logica_registrar_usuario.md) - Creación de nuevos usuarios
- [Editar Usuario](Usuarios/logica_editar_usuario.md) - Modificación de usuarios existentes
- [Desactivar Usuario](Usuarios/logica_desactivar_usuario.md) - Desactivación de cuentas
- [Consultar Usuarios](Usuarios/logica_consultar_usuarios.md) - Listado de usuarios

---

### 8. [Reportes](Reportes/)
Generación de reportes e informes.

**Funcionalidades:**
- [Generar Reporte de Inventario](Reportes/logica_generar_reporte_inventario.md) - Reportes PDF de inventario

---

## 🗂️ Estructura del Proyecto

```
SIGI/
├── config/
│   └── database.php          # Configuración de conexión a base de datos
├── controllers/              # Controladores (lógica de negocio)
│   ├── AuthController.php
│   ├── ProductoController.php
│   ├── CategoriaController.php
│   ├── ProveedorController.php
│   ├── InventarioController.php
│   ├── VentaController.php
│   ├── UsuarioController.php
│   └── ReporteController.php
├── models/                   # Modelos (acceso a datos)
│   ├── Usuario.php
│   ├── Producto.php
│   ├── Categoria.php
│   ├── Proveedor.php
│   ├── Inventario.php
│   ├── Venta.php
│   └── Reporte.php
├── views/                    # Vistas (interfaz de usuario)
│   ├── Dashboard/
│   │   ├── Admin.php
│   │   ├── Empleado.php
│   │   ├── Adminproductos.php
│   │   ├── Admincategorias.php
│   │   ├── Adminproveedores.php
│   │   ├── Admininventario.php
│   │   ├── Adminalertas.php
│   │   ├── Adminventas.php
│   │   ├── Adminhistorialventas.php
│   │   ├── Adminusuarios.php
│   │   └── Adminreportes.php
│   ├── Usuario/
│   │   └── login.php
│   └── layouts/
│       ├── header.php
│       ├── sidebar.php
│       └── footer.php
├── CSS/                      # Hojas de estilo
├── JS/                       # Scripts JavaScript
├── IMG/                      # Imágenes del sistema
└── SQL/
    └── SIGI.sql             # Esquema de base de datos
```

---

## 🗄️ Base de Datos

**Sistema Gestor:** MySQL/MariaDB  
**Nombre de la Base de Datos:** `sigi`

### Tablas Principales:
- `usuarios` - Usuarios del sistema
- `roles` - Roles de usuario (Administrador, Empleado)
- `productos` - Catálogo de productos
- `categorias` - Categorías de productos
- `proveedores` - Proveedores de productos
- `movimiento_inventario` - Historial de movimientos de stock
- `tipo_movimiento` - Tipos de movimiento (Entrada, Salida, Ajuste)
- `venta` - Cabecera de ventas
- `detalleventa` - Detalle de productos vendidos
- `metodos_pago` - Métodos de pago disponibles

---

## 🔐 Seguridad

El sistema implementa las siguientes medidas de seguridad:

- **Autenticación:** Validación de credenciales con password_verify
- **Encriptación:** Contraseñas hasheadas con password_hash (bcrypt)
- **Control de Acceso:** Sistema de roles (RBAC) - Administrador y Empleado
- **Prevención de Inyección SQL:** Consultas preparadas con PDO
- **Validación de Sesiones:** Verificación en cada página protegida
- **Regeneración de Sesión:** Prevención de Session Fixation
- **Bloqueo Temporal:** Protección contra ataques de fuerza bruta
- **Validación de Datos:** Sanitización de entradas del usuario

---

## 📋 Convenciones del Código

### Arquitectura:
- **Patrón MVC:** Modelo-Vista-Controlador
- **Separación de Responsabilidades:** Cada capa tiene una función específica

### Nomenclatura:
- **Tablas:** minúsculas, singular o plural según contexto
- **Clases:** PascalCase (ej: `ProductoController`)
- **Métodos:** camelCase (ej: `obtenerPorId`)
- **Variables:** camelCase (ej: `$nombreUsuario`)
- **Constantes:** MAYÚSCULAS con guiones bajos (ej: `MAX_INTENTOS`)

### Base de Datos:
- **Claves Primarias:** `id_[nombre_tabla]` (ej: `id_producto`)
- **Claves Foráneas:** `id_[tabla_referenciada]` (ej: `id_categoria`)
- **Campos de Auditoría:** `fechaCreacion`, `fechaModificacion`
- **Estados:** Campo `activo` (1 = activo, 0 = inactivo)

---

## 🚀 Tecnologías Utilizadas

- **Backend:** PHP 8.x
- **Base de Datos:** MySQL/MariaDB
- **Frontend:** HTML5, CSS3, JavaScript
- **Librerías JavaScript:**
  - jQuery 3.x
  - DataTables (tablas interactivas)
  - SweetAlert2 (alertas y notificaciones)
- **Librerías PHP:**
  - PDO (acceso a base de datos)
  - TCPDF (generación de PDF)
- **Servidor Local:** Laragon (Windows)

---

## 📝 Notas Importantes

1. **Formato de Documentación:** Todos los archivos de documentación están escritos en formato narrativo puro, sin bloques de código, para facilitar la comprensión del flujo lógico.

2. **Recuperación de Código:** Si se elimina código del sistema, esta documentación permite reconstruir la lógica completa de cada funcionalidad.

3. **Actualización:** Esta documentación debe actualizarse cada vez que se modifique o agregue una funcionalidad al sistema.

4. **Transacciones:** Las operaciones críticas como ventas y movimientos de inventario utilizan transacciones de base de datos para garantizar la integridad de los datos.

5. **Desactivación vs Eliminación:** El sistema utiliza desactivación lógica (cambio de estado) en lugar de eliminación física para mantener la integridad referencial y el historial.

---

## 📞 Soporte

Para dudas o consultas sobre el sistema, revisar primero la documentación específica de cada módulo. Cada archivo de funcionalidad explica detalladamente el flujo completo desde la vista hasta la base de datos.

---

**Última Actualización:** Mayo 2026  
**Versión del Sistema:** 1.0  
**Documentación Generada por:** Equipo de Desarrollo SIGI
