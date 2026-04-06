<?php 
session_start();
$pagina_activa = 'proveedores';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proveedores - La Esquina</title>
    <link rel="stylesheet" href="../../CSS/global.css?v=<?php echo time() . '_' . rand(1000, 9999); ?>">
    <link rel="stylesheet" href="../../CSS/components.css?v=<?php echo time() . '_' . rand(1000, 9999); ?>">
</head>

<body>
    <div class="pagina">
        <?php include 'menu.php'; ?>
        <div class="contenido">
            <header class="cabecera">
                <div class="cabecera-titulo">
                    <h1>Proveedores</h1>
                    <p>Contactos y relaciones comerciales</p>
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

                <!-- Números resumen -->
                <div class="fila-numeros">
                    <div class="tarjeta-numero">
                        <div class="tarjeta-numero-icono fondo-azul">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="1" y="3" width="15" height="13" rx="1"/>
                                <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/>
                                <circle cx="5.5" cy="18.5" r="2.5"/>
                                <circle cx="18.5" cy="18.5" r="2.5"/>
                            </svg>
                        </div>
                        <div class="tarjeta-numero-texto">
                            <h3>Total Proveedores</h3>
                            <p>23</p>
                            <span>12 activos</span>
                        </div>
                    </div>
                    <div class="tarjeta-numero">
                        <div class="tarjeta-numero-icono fondo-verde">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                            </svg>
                        </div>
                        <div class="tarjeta-numero-texto">
                            <h3>Último Contacto</h3>
                            <p>Hoy</p>
                            <span>2 comunicaciones</span>
                        </div>
                    </div>
                </div>

                <!-- Tabla de proveedores -->
                <div class="tarjeta">
                    <div class="tarjeta-titulo">
                        <h2>
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right:8px">
                                <rect x="1" y="3" width="15" height="13" rx="1"/>
                                <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/>
                                <circle cx="5.5" cy="18.5" r="2.5"/>
                                <circle cx="18.5" cy="18.5" r="2.5"/>
                            </svg>
                            Lista de Proveedores
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
                                Nuevo Proveedor
                            </button>
                        </div>
                    </div>

                    <!-- Filtros rápidos -->
                    <div class="filtros-historial">
                        <div class="filtro-grupo">
                            <label>Buscar proveedor:</label>
                            <input type="text" class="filtro-input" placeholder="Nombre o empresa...">
                        </div>
                        <div class="filtro-grupo">
                            <label>Estado:</label>
                            <select class="filtro-select">
                                <option value="">Todos</option>
                                <option value="activo">Activos</option>
                                <option value="inactivo">Inactivos</option>
                            </select>
                        </div>
                        <div class="filtro-grupo">
                            <label>Tipo de producto:</label>
                            <select class="filtro-select">
                                <option value="">Todos</option>
                                <option value="alimentos">Alimentos</option>
                                <option value="bebidas">Bebidas</option>
                                <option value="papeleria">Papelería</option>
                            </select>
                        </div>
                    </div>

                    <div class="tabla-contenedor tabla-moderna">
                        <table class="tabla-historial">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Proveedor</th>
                                    <th>Contacto</th>
                                    <th>Productos</th>
                                    <th>Estado</th>
                                    <th>Última Comunicación</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="proveedor-activo">
                                    <td><span class="proveedor-id">#001</span></td>
                                    <td>
                                        <div class="proveedor-celda">
                                            <div class="proveedor-icono">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <rect x="1" y="3" width="15" height="13" rx="1"/>
                                                    <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/>
                                                    <circle cx="5.5" cy="18.5" r="2.5"/>
                                                    <circle cx="18.5" cy="18.5" r="2.5"/>
                                                </svg>
                                            </div>
                                            <div class="proveedor-info">
                                                <span class="proveedor-nombre">Distribuidora XYZ</span>
                                                <span class="proveedor-empresa">XYZ S.A.S</span>
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
                                            <div class="contacto-item">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                                                    <polyline points="22,6 12,13 2,6"/>
                                                </svg>
                                                <span>contacto@xyz.com</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="productos-cantidad">45</span></td>
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
                                <tr class="proveedor-activo">
                                    <td><span class="proveedor-id">#002</span></td>
                                    <td>
                                        <div class="proveedor-celda">
                                            <div class="proveedor-icono">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                                                    <polyline points="3.27,6.96 12,12.01 20.73,6.96"/>
                                                    <line x1="12" y1="22.08" x2="12" y2="12"/>
                                                </svg>
                                            </div>
                                            <div class="proveedor-info">
                                                <span class="proveedor-nombre">Lácteos del Valle</span>
                                                <span class="proveedor-empresa">Lácteos Valle Ltda</span>
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
                                            <div class="contacto-item">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                                                    <polyline points="22,6 12,13 2,6"/>
                                                </svg>
                                                <span>ventas@lacteos.com</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="productos-cantidad">23</span></td>
                                    <td><span class="etiqueta etiqueta-activo">Activo</span></td>
                                    <td>
                                        <div class="comunicacion-celda">
                                            <span class="comunicacion-fecha">Ayer</span>
                                            <span class="comunicacion-hora">10:15</span>
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
                                <tr class="proveedor-inactivo">
                                    <td><span class="proveedor-id">#003</span></td>
                                    <td>
                                        <div class="proveedor-celda">
                                            <div class="proveedor-icono">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                                    <polyline points="14,2 14,8 20,8"/>
                                                    <line x1="16" y1="13" x2="8" y2="13"/>
                                                    <line x1="16" y1="17" x2="8" y2="17"/>
                                                    <polyline points="10,9 9,9 8,9"/>
                                                </svg>
                                            </div>
                                            <div class="proveedor-info">
                                                <span class="proveedor-nombre">Papelería Central</span>
                                                <span class="proveedor-empresa">Papel y Útiles S.A.S</span>
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
                                            <div class="contacto-item">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                                                    <polyline points="22,6 12,13 2,6"/>
                                                </svg>
                                                <span>pedidos@papeleria.com</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="productos-cantidad">8</span></td>
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
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Paginación -->
                    <div class="paginacion-historial">
                        <div class="paginacion-info">
                            <span>Mostrando 1-3 de 23 proveedores</span>
                        </div>
                        <div class="paginacion-controles">
                            <button class="paginacion-boton" disabled>
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="15,18 9,12 15,6"/>
                                </svg>
                            </button>
                            <button class="paginacion-boton activo">1</button>
                            <button class="paginacion-boton">2</button>
                            <button class="paginacion-boton">3</button>
                            <span class="paginacion-puntos">...</span>
                            <button class="paginacion-boton">8</button>
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