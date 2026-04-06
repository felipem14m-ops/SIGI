<?php 
session_start();
$pagina_activa = 'usuarios';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios - La Esquina</title>
    <link rel="stylesheet" href="../../CSS/global.css?v=<?php echo time() . '_' . rand(1000, 9999); ?>">
    <link rel="stylesheet" href="../../CSS/components.css?v=<?php echo time() . '_' . rand(1000, 9999); ?>">
</head>

<body>
    <div class="pagina">
        <?php include 'menu.php'; ?>
        <div class="contenido">
            <header class="cabecera">
                <div class="cabecera-titulo">
                    <h1>Usuarios</h1>
                    <p>Control de acceso y permisos</p>
                </div>
                <!-- Zona de usuario: notificaciones + avatar -->
                <div class="cabecera-usuario">
                    <!-- Botón de notificaciones con punto rojo -->
                    <button class="boton-notif" title="Notificaciones">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9" />
                            <path d="M13.73 21a2 2 0 0 1-3.46 0" />
                        </svg>
                        <span class="punto-notif"></span>
                    </button>
                    <!-- Tarjeta del usuario con inicial y rol -->
                    <div class="tarjeta-usuario">
                        <div class="avatar-usuario">
                            <?php echo strtoupper(substr($_SESSION['nombre'] ?? 'A', 0, 1)); ?>
                        </div>
                        <div class="datos-usuario">
                            <strong><?php echo htmlspecialchars($_SESSION['nombre'] ?? 'Administrador'); ?></strong>
                            <span><?php echo htmlspecialchars($_SESSION['rol'] ?? 'Administrador'); ?></span>
                        </div>
                    </div>
                </div>
            </header>

            <div class="zona-principal">

                <!-- Estadísticas de usuarios -->
                <div class="fila-numeros">
                    <div class="tarjeta-numero">
                        <div class="tarjeta-numero-icono fondo-azul">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                <circle cx="9" cy="7" r="4"/>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                            </svg>
                        </div>
                        <div class="tarjeta-numero-texto">
                            <h3>Total Usuarios</h3>
                            <p>8</p>
                            <span>6 activos</span>
                        </div>
                    </div>
                    <div class="tarjeta-numero">
                        <div class="tarjeta-numero-icono fondo-verde">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                <polyline points="22,4 12,14.01 9,11.01"/>
                            </svg>
                        </div>
                        <div class="tarjeta-numero-texto">
                            <h3>Conectados Hoy</h3>
                            <p>5</p>
                            <span>62.5% activos</span>
                        </div>
                    </div>
                    <div class="tarjeta-numero">
                        <div class="tarjeta-numero-icono fondo-amarillo">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"/>
                                <polyline points="12,6 12,12 16,14"/>
                            </svg>
                        </div>
                        <div class="tarjeta-numero-texto">
                            <h3>Último Acceso</h3>
                            <p>Hace 5 min</p>
                            <span>Juan Pérez</span>
                        </div>
                    </div>
                </div>

                <!-- Actividad reciente -->
                <div class="tarjeta">
                    <div class="tarjeta-titulo">
                        <h2>
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right:8px">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                <polyline points="22,4 12,14.01 9,11.01"/>
                            </svg>
                            Actividad Reciente
                        </h2>
                        <div class="tarjeta-acciones">
                            <button class="boton-accion boton-primario">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                    <polyline points="7,10 12,15 17,10"/>
                                    <line x1="12" y1="15" x2="12" y2="3"/>
                                </svg>
                                Ver Todo
                            </button>
                        </div>
                    </div>
                    
                    <div class="actividad-lista">
                        <div class="actividad-item">
                            <div class="actividad-avatar">JP</div>
                            <div class="actividad-contenido">
                                <div class="actividad-texto">
                                    <strong>Juan Pérez</strong> inició sesión
                                </div>
                                <div class="actividad-tiempo">Hace 5 minutos</div>
                            </div>
                            <div class="actividad-icono">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                    <polyline points="22,4 12,14.01 9,11.01"/>
                                </svg>
                            </div>
                        </div>
                        <div class="actividad-item">
                            <div class="actividad-avatar">MG</div>
                            <div class="actividad-contenido">
                                <div class="actividad-texto">
                                    <strong>María Gómez</strong> actualizó su perfil
                                </div>
                                <div class="actividad-tiempo">Hace 1 hora</div>
                            </div>
                            <div class="actividad-icono">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="actividad-item">
                            <div class="actividad-avatar">A</div>
                            <div class="actividad-contenido">
                                <div class="actividad-texto">
                                    <strong>Admin</strong> creó nuevo usuario
                                </div>
                                <div class="actividad-tiempo">Hace 3 horas</div>
                            </div>
                            <div class="actividad-icono">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#3b82f6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                    <circle cx="8.5" cy="7" r="4"/>
                                    <line x1="20" y1="8" x2="20" y2="14"/>
                                    <line x1="23" y1="11" x2="17" y2="11"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Lista de usuarios -->
                <div class="tarjeta">
                    <div class="tarjeta-titulo">
                        <h2>
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right:8px">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                <circle cx="9" cy="7" r="4"/>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                            </svg>
                            Lista de Usuarios
                        </h2>
                        <div class="tarjeta-acciones">
                            <button class="boton-accion boton-primario">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                    <polyline points="7,10 12,15 17,10"/>
                                    <line x1="12" y1="15" x2="12" y2="3"/>
                                </svg>
                                Exportar
                            </button>
                            <button class="boton-accion boton-secundario">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="12" y1="5" x2="12" y2="19"/>
                                    <line x1="5" y1="12" x2="19" y2="12"/>
                                </svg>
                                Nuevo Usuario
                            </button>
                        </div>
                    </div>

                    <!-- Filtros rápidos -->
                    <div class="filtros-historial">
                        <div class="filtro-grupo">
                            <label>Buscar usuario:</label>
                            <input type="text" class="filtro-input" placeholder="Nombre o correo...">
                        </div>
                        <div class="filtro-grupo">
                            <label>Rol:</label>
                            <select class="filtro-select">
                                <option value="">Todos</option>
                                <option value="admin">Administradores</option>
                                <option value="empleado">Empleados</option>
                                <option value="gerente">Gerentes</option>
                            </select>
                        </div>
                        <div class="filtro-grupo">
                            <label>Estado:</label>
                            <select class="filtro-select">
                                <option value="">Todos</option>
                                <option value="activo">Activos</option>
                                <option value="inactivo">Inactivos</option>
                                <option value="suspendido">Suspendidos</option>
                            </select>
                        </div>
                    </div>

                    <div class="tabla-contenedor tabla-moderna">
                        <table class="tabla-historial">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Usuario</th>
                                    <th>Contacto</th>
                                    <th>Rol</th>
                                    <th>Estado</th>
                                    <th>Último Acceso</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="usuario-activo">
                                    <td><span class="proveedor-id">#001</span></td>
                                    <td>
                                        <div class="proveedor-celda">
                                            <div class="proveedor-icono">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                                    <circle cx="12" cy="7" r="4"/>
                                                </svg>
                                            </div>
                                            <div class="proveedor-info">
                                                <span class="proveedor-nombre">Admin Usuario</span>
                                                <span class="proveedor-empresa">admin@laesquina.com</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="contacto-celda">
                                            <div class="contacto-item">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                                                </svg>
                                                <span>300 123 4567</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="etiqueta etiqueta-admin">Administrador</span></td>
                                    <td><span class="etiqueta etiqueta-activo">Activo</span></td>
                                    <td>
                                        <div class="comunicacion-celda">
                                            <span class="comunicacion-fecha">Hoy</span>
                                            <span class="comunicacion-hora">14:30</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="acciones-celda">
                                            <button class="boton-accion-tabla boton-ver" title="Ver detalles">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                                    <circle cx="12" cy="12" r="3"/>
                                                </svg>
                                            </button>
                                            <button class="boton-accion-tabla boton-editar" title="Editar">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                                </svg>
                                            </button>
                                            <button class="boton-accion-tabla boton-eliminar" title="Eliminar">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <polyline points="3,6 5,6 21,6"/>
                                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="usuario-activo">
                                    <td><span class="proveedor-id">#002</span></td>
                                    <td>
                                        <div class="proveedor-celda">
                                            <div class="proveedor-icono">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                                    <circle cx="12" cy="7" r="4"/>
                                                </svg>
                                            </div>
                                            <div class="proveedor-info">
                                                <span class="proveedor-nombre">Juan Pérez</span>
                                                <span class="proveedor-empresa">juan@laesquina.com</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="contacto-celda">
                                            <div class="contacto-item">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                                                </svg>
                                                <span>310 987 6543</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="etiqueta etiqueta-empleado">Empleado</span></td>
                                    <td><span class="etiqueta etiqueta-activo">Activo</span></td>
                                    <td>
                                        <div class="comunicacion-celda">
                                            <span class="comunicacion-fecha">Hoy</span>
                                            <span class="comunicacion-hora">09:15</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="acciones-celda">
                                            <button class="boton-accion-tabla boton-ver" title="Ver detalles">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                                    <circle cx="12" cy="12" r="3"/>
                                                </svg>
                                            </button>
                                            <button class="boton-accion-tabla boton-editar" title="Editar">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                                </svg>
                                            </button>
                                            <button class="boton-accion-tabla boton-eliminar" title="Eliminar">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <polyline points="3,6 5,6 21,6"/>
                                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="usuario-inactivo">
                                    <td><span class="proveedor-id">#003</span></td>
                                    <td>
                                        <div class="proveedor-celda">
                                            <div class="proveedor-icono">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                                    <circle cx="12" cy="7" r="4"/>
                                                </svg>
                                            </div>
                                            <div class="proveedor-info">
                                                <span class="proveedor-nombre">María Gómez</span>
                                                <span class="proveedor-empresa">maria@laesquina.com</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="contacto-celda">
                                            <div class="contacto-item">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                                                </svg>
                                                <span>314 222 5555</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="etiqueta etiqueta-empleado">Empleado</span></td>
                                    <td><span class="etiqueta etiqueta-inactivo">Inactivo</span></td>
                                    <td>
                                        <div class="comunicacion-celda">
                                            <span class="comunicacion-fecha">Hace 3 días</span>
                                            <span class="comunicacion-hora">09:00</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="acciones-celda">
                                            <button class="boton-accion-tabla boton-ver" title="Ver detalles">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                                    <circle cx="12" cy="12" r="3"/>
                                                </svg>
                                            </button>
                                            <button class="boton-accion-tabla boton-editar" title="Editar">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                                </svg>
                                            </button>
                                            <button class="boton-accion-tabla boton-eliminar" title="Eliminar">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <polyline points="3,6 5,6 21,6"/>
                                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="usuario-activo">
                                    <td><span class="proveedor-id">#004</span></td>
                                    <td>
                                        <div class="proveedor-celda">
                                            <div class="proveedor-icono">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                                    <circle cx="12" cy="7" r="4"/>
                                                </svg>
                                            </div>
                                            <div class="proveedor-info">
                                                <span class="proveedor-nombre">Carlos Rodríguez</span>
                                                <span class="proveedor-empresa">carlos@laesquina.com</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="contacto-celda">
                                            <div class="contacto-item">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                                                </svg>
                                                <span>315 777 8888</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="etiqueta etiqueta-gerente">Gerente</span></td>
                                    <td><span class="etiqueta etiqueta-activo">Activo</span></td>
                                    <td>
                                        <div class="comunicacion-celda">
                                            <span class="comunicacion-fecha">Ayer</span>
                                            <span class="comunicacion-hora">16:45</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="acciones-celda">
                                            <button class="boton-accion-tabla boton-ver" title="Ver detalles">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                                    <circle cx="12" cy="12" r="3"/>
                                                </svg>
                                            </button>
                                            <button class="boton-accion-tabla boton-editar" title="Editar">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                                </svg>
                                            </button>
                                            <button class="boton-accion-tabla boton-eliminar" title="Eliminar">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <polyline points="3,6 5,6 21,6"/>
                                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="usuario-activo">
                                    <td><span class="proveedor-id">#005</span></td>
                                    <td>
                                        <div class="proveedor-celda">
                                            <div class="proveedor-icono">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                                    <circle cx="12" cy="7" r="4"/>
                                                </svg>
                                            </div>
                                            <div class="proveedor-info">
                                                <span class="proveedor-nombre">Ana Martínez</span>
                                                <span class="proveedor-empresa">ana@laesquina.com</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="contacto-celda">
                                            <div class="contacto-item">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                                                </svg>
                                                <span>320 555 9999</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="etiqueta etiqueta-empleado">Empleado</span></td>
                                    <td><span class="etiqueta etiqueta-activo">Activo</span></td>
                                    <td>
                                        <div class="comunicacion-celda">
                                            <span class="comunicacion-fecha">Hace 2 días</span>
                                            <span class="comunicacion-hora">11:30</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="acciones-celda">
                                            <button class="boton-accion-tabla boton-ver" title="Ver detalles">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                                    <circle cx="12" cy="12" r="3"/>
                                                </svg>
                                            </button>
                                            <button class="boton-accion-tabla boton-editar" title="Editar">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                                </svg>
                                            </button>
                                            <button class="boton-accion-tabla boton-eliminar" title="Eliminar">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <polyline points="3,6 5,6 21,6"/>
                                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Paginación -->
                    <div class="paginacion-historial">
                        <div class="paginacion-info">
                            <span>Mostrando 1-5 de 8 usuarios</span>
                        </div>
                        <div class="paginacion-controles">
                            <button class="paginacion-boton" disabled>
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="15,18 9,12 15,6"/>
                                </svg>
                            </button>
                            <button class="paginacion-boton activo">1</button>
                            <button class="paginacion-boton">2</button>
                            <button class="paginacion-boton">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="9,18 15,12 9,6"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</body>

</html>