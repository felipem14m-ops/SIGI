CREATE TABLE categorias (
    id_categoria INT NOT NULL AUTO_INCREMENT,
    nombre VARCHAR(80) NOT NULL,
    descripcion TEXT,
    imagen VARCHAR(255),
    activa TINYINT(1) NOT NULL DEFAULT 1,
    PRIMARY KEY (id_categoria),
    UNIQUE KEY uq_categorias_nombre (nombre)
);

CREATE TABLE proveedores (
    id_proveedor INT NOT NULL AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    telefono VARCHAR(20),
    email VARCHAR(100),
    empresa VARCHAR(100),
    activo TINYINT(1) NOT NULL DEFAULT 1,
    fecha_registro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_proveedor)
);

CREATE TABLE roles (
    id_rol TINYINT NOT NULL,
    nombre VARCHAR(30) NOT NULL,
    descripcion VARCHAR(150),
    PRIMARY KEY (id_rol),
    UNIQUE KEY uq_roles_nombre (nombre)
);

CREATE TABLE usuarios (
    id_usuario INT NOT NULL AUTO_INCREMENT,
    id_rol TINYINT NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    numeroIdentificacion VARCHAR(20) NOT NULL,
    email VARCHAR(100) NOT NULL,
    contrasena VARCHAR(255) NOT NULL,
    activo TINYINT(1) NOT NULL DEFAULT 1,
    intentos_fallidos TINYINT NOT NULL DEFAULT 0,
    bloqueado_hasta DATETIME,
    fecha_creacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_usuario),
    UNIQUE KEY uq_usuarios_identificacion (numeroIdentificacion),
    UNIQUE KEY uq_usuarios_email (email),
    KEY idx_rol_activo (id_rol, activo),
    CONSTRAINT fk_usuarios_rol
        FOREIGN KEY (id_rol)
        REFERENCES roles (id_rol)
);

CREATE TABLE tipos_movimiento (
    id_tipo INT NOT NULL AUTO_INCREMENT,
    codigo VARCHAR(30) NOT NULL,
    nombre VARCHAR(80) NOT NULL,
    signo ENUM('+', '-') NOT NULL,
    PRIMARY KEY (id_tipo),
    UNIQUE KEY uq_tipos_codigo (codigo)
);

CREATE TABLE productos (
    id_producto INT NOT NULL AUTO_INCREMENT,
    codigoUnico VARCHAR(50) NOT NULL,
    nombre VARCHAR(150) NOT NULL,
    descripcion TEXT,
    id_categoria INT NOT NULL,
    id_proveedor INT,
    precio_venta DECIMAL(10,2) NOT NULL,
    precio_costo DECIMAL(10,2) NOT NULL,
    stock_minimo INT NOT NULL DEFAULT 0,
    stock_actual INT NOT NULL DEFAULT 0,
    fechaCreacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fechaVencimiento DATE,
    estado ENUM('activo','inactivo','agotado') NOT NULL DEFAULT 'activo',
    imagen VARCHAR(255),
    PRIMARY KEY (id_producto),
    UNIQUE KEY uq_productos_codigo (codigoUnico),
    KEY idx_categoria (id_categoria),
    KEY idx_proveedor (id_proveedor),
    KEY idx_estado (estado),
    KEY idx_vencimiento (fechaVencimiento),
    CONSTRAINT fk_productos_categoria
        FOREIGN KEY (id_categoria)
        REFERENCES categorias (id_categoria),
    CONSTRAINT fk_productos_proveedor
        FOREIGN KEY (id_proveedor)
        REFERENCES proveedores (id_proveedor)
);

CREATE TABLE metodos_pago (
    id_metodo INT NOT NULL AUTO_INCREMENT,
    nombre VARCHAR(50) NOT NULL,
    activo TINYINT(1) NOT NULL DEFAULT 1,
    PRIMARY KEY (id_metodo),
    UNIQUE KEY uq_metodos_nombre (nombre)
);

CREATE TABLE venta (
    id_venta INT NOT NULL AUTO_INCREMENT,
    id_usuario INT NOT NULL,
    id_metodo INT NOT NULL,
    fecha_venta DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    total DECIMAL(12,2) NOT NULL,
    estado ENUM('completada','anulada') NOT NULL DEFAULT 'completada',
    monto_recibido DECIMAL(10,2),
    cambio_devuelto DECIMAL(10,2),
    PRIMARY KEY (id_venta),
    KEY idx_venta_fecha (fecha_venta),
    KEY idx_venta_estado (estado),
    KEY idx_venta_usuario (id_usuario),
    KEY fk_venta_metodo (id_metodo),
    CONSTRAINT fk_venta_usuario
        FOREIGN KEY (id_usuario)
        REFERENCES usuarios (id_usuario),
    CONSTRAINT fk_venta_metodo
        FOREIGN KEY (id_metodo)
        REFERENCES metodos_pago (id_metodo)
);

CREATE TABLE detalleventa (
    id_detalle INT NOT NULL AUTO_INCREMENT,
    id_venta INT NOT NULL,
    id_producto INT NOT NULL,
    cantidad INT NOT NULL,
    precioUnitario DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(12,2) NOT NULL,
    PRIMARY KEY (id_detalle),
    KEY idx_detalle_venta (id_venta),
    KEY idx_detalle_producto (id_producto),
    CONSTRAINT fk_detalle_venta
        FOREIGN KEY (id_venta)
        REFERENCES venta (id_venta),
    CONSTRAINT fk_detalle_producto
        FOREIGN KEY (id_producto)
        REFERENCES productos (id_producto)
);

CREATE TABLE movimiento_inventario (
    id_movimiento INT NOT NULL AUTO_INCREMENT,
    id_producto INT NOT NULL,
    id_usuario INT NOT NULL,
    id_tipo_movimiento INT NOT NULL,
    cantidad INT NOT NULL,
    stock_anterior INT NOT NULL,
    stock_resultante INT NOT NULL,
    origen ENUM('manual','automatico') NOT NULL DEFAULT 'manual',
    motivo TEXT,
    fecha DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_movimiento),
    KEY idx_mov_producto (id_producto),
    KEY idx_mov_fecha (fecha),
    KEY idx_mov_usuario (id_usuario),
    KEY fk_movimiento_tipo (id_tipo_movimiento),
    CONSTRAINT fk_movimiento_producto
        FOREIGN KEY (id_producto)
        REFERENCES productos (id_producto),
    CONSTRAINT fk_movimiento_usuario
        FOREIGN KEY (id_usuario)
        REFERENCES usuarios (id_usuario),
    CONSTRAINT fk_movimiento_tipo
        FOREIGN KEY (id_tipo_movimiento)
        REFERENCES tipos_movimiento (id_tipo)
);