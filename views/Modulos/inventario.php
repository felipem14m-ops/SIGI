<?php 
session_start();
$pagina_activa = 'inventario';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventario - La Esquina</title>
    <link rel="stylesheet" href="../../CSS/global.css?v=<?php echo time() . '_' . rand(10000, 99999); ?>">
    <link rel="stylesheet" href="../../CSS/components.css?v=<?php echo time() . '_' . rand(10000, 99999); ?>">
    </head>
<body>
<div class="pagina">
    <?php include 'menu.php'; ?>
    <div class="contenido">
        <header class="cabecera">
            <div class="cabecera-titulo">
                <h1>Inventario</h1>
                <p>Registro de entradas y salidas</p>
            </div>
                <!-- Zona de usuario: notificaciones + avatar -->
            <div class="cabecera-usuario">
                <!-- Botón de notificaciones con punto rojo -->
                <button class="boton-notif" title="Notificaciones">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                        <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
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

            <!-- Resumen del día -->
            <div class="fila-resumen">
                <div class="resumen-caja">
                    <div class="resumen-icono fondo-verde">↑</div>
                    <div class="resumen-texto"><h3>Entradas Hoy</h3><p>12</p><span>+250 unidades</span></div>
                </div>
                <div class="resumen-caja">
                    <div class="resumen-icono fondo-rojo">↓</div>
                    <div class="resumen-texto"><h3>Salidas Hoy</h3><p>45</p><span>-180 unidades</span></div>
                </div>
                <div class="resumen-caja">
                    <div class="resumen-icono fondo-naranja">⚠️</div>
                    <div class="resumen-texto"><h3>Pérdidas del Mes</h3><p>8</p><span>$125.000</span></div>
                </div>
            </div>

            <!-- Registrar movimiento -->
            <div class="caja-formulario">
                <div class="caja-titulo"><h2>Registrar Movimiento</h2></div>
                <form method="POST" action="">
                <div class="formulario-dos-columnas">
                    <div class="campo">
                        <label>Producto</label>
                        <select name="producto">
                            <option>Seleccionar producto...</option>
                            <option>Arroz Premium 1kg</option>
                            <option>Leche Entera 1L</option>
                        </select>
                    </div>
                    <div class="campo">
                        <label>Tipo de Movimiento</label>
                        <select name="tipo">
                            <option>Entrada</option>
                            <option>Salida por Venta</option>
                            <option>Pérdida / Merma</option>
                            <option>Devolución</option>
                        </select>
                    </div>
                    <div class="campo">
                        <label>Cantidad</label>
                        <input type="number" name="cantidad" placeholder="0">
                    </div>
                    <div class="campo">
                        <label>Fecha de Vencimiento (opcional)</label>
                        <input type="date" name="fecha_vencimiento">
                    </div>
                    <div class="campo campo-grande">
                        <label>Motivo</label>
                        <textarea name="motivo" rows="3" placeholder="Describe el motivo..."></textarea>
                    </div>
                    <div class="campo campo-grande botones-derecha">
                        <button type="button" class="boton-accion boton-secundario">Cancelar</button>
                        <button type="submit" class="boton-accion boton-primario">Guardar</button>
                    </div>
                </div>
                </form>
            </div>

            <!-- Historial de Movimientos -->
            <div class="tarjeta">
                <div class="tarjeta-titulo">
                    <h2>
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right:8px">
                            <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Historial de Movimientos
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
                                <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            Filtrar
                        </button>
                    </div>
                </div>
                
                <!-- Filtros rápidos -->
                <div class="filtros-historial">
                    <div class="filtro-grupo">
                        <label>Tipo de movimiento:</label>
                        <select class="filtro-select">
                            <option value="">Todos</option>
                            <option value="entrada">Entradas</option>
                            <option value="salida">Salidas</option>
                            <option value="merma">Pérdidas</option>
                            <option value="devolucion">Devoluciones</option>
                        </select>
                    </div>
                    <div class="filtro-grupo">
                        <label>Fecha:</label>
                        <input type="date" class="filtro-input">
                    </div>
                    <div class="filtro-grupo">
                        <label>Producto:</label>
                        <input type="text" class="filtro-input" placeholder="Buscar producto...">
                    </div>
                </div>

                <div class="tabla-contenedor tabla-moderna">
                    <table class="tabla-historial">
                        <thead>
                            <tr>
                                <th>Fecha y Hora</th>
                                <th>Producto</th>
                                <th>Tipo</th>
                                <th>Cantidad</th>
                                <th>Stock</th>
                                <th>Responsable</th>
                                <th>Referencia</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="movimiento-entrada">
                                <td>
                                    <div class="fecha-celda">
                                        <span class="fecha-dia">15/03/2025</span>
                                        <span class="fecha-hora">14:30</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="producto-celda">
                                        <div class="producto-icono">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                                                <polyline points="3.27,6.96 12,12.01 20.73,6.96"/>
                                                <line x1="12" y1="22.08" x2="12" y2="12"/>
                                            </svg>
                                        </div>
                                        <div class="producto-info">
                                            <span class="producto-nombre">Arroz Premium 1kg</span>
                                            <span class="producto-codigo">SKU-001</span>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="etiqueta etiqueta-entrada">Entrada</span></td>
                                <td><span class="cantidad-positiva">+50</span></td>
                                <td>
                                    <div class="stock-cambio">
                                        <span class="stock-anterior">100</span>
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="9,18 15,12 9,6"/>
                                        </svg>
                                        <span class="stock-actual">150</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="responsable-celda">
                                        <div class="responsable-avatar">A</div>
                                        <span>Admin</span>
                                    </div>
                                </td>
                                <td><span class="referencia">Reabastecimiento</span></td>
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
                                    </div>
                                </td>
                            </tr>
                            <tr class="movimiento-salida">
                                <td>
                                    <div class="fecha-celda">
                                        <span class="fecha-dia">15/03/2025</span>
                                        <span class="fecha-hora">13:15</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="producto-celda">
                                        <div class="producto-icono">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <circle cx="12" cy="12" r="10"/>
                                                <path d="M12 6v6l4 2"/>
                                            </svg>
                                        </div>
                                        <div class="producto-info">
                                            <span class="producto-nombre">Leche Entera 1L</span>
                                            <span class="producto-codigo">SKU-002</span>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="etiqueta etiqueta-salida">Salida</span></td>
                                <td><span class="cantidad-negativa">-5</span></td>
                                <td>
                                    <div class="stock-cambio">
                                        <span class="stock-anterior">90</span>
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="15,18 9,12 15,6"/>
                                        </svg>
                                        <span class="stock-actual">85</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="responsable-celda">
                                        <div class="responsable-avatar">JP</div>
                                        <span>Juan Pérez</span>
                                    </div>
                                </td>
                                <td><span class="referencia">Venta #V-2025003</span></td>
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
                                    </div>
                                </td>
                            </tr>
                            <tr class="movimiento-merma">
                                <td>
                                    <div class="fecha-celda">
                                        <span class="fecha-dia">15/03/2025</span>
                                        <span class="fecha-hora">10:00</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="producto-celda">
                                        <div class="producto-icono">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                                                <line x1="12" y1="9" x2="12" y2="13"/>
                                                <line x1="12" y1="17" x2="12.01" y2="17"/>
                                            </svg>
                                        </div>
                                        <div class="producto-info">
                                            <span class="producto-nombre">Galletas Chocolate</span>
                                            <span class="producto-codigo">SKU-003</span>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="etiqueta etiqueta-merma">Pérdida</span></td>
                                <td><span class="cantidad-advertencia">-10</span></td>
                                <td>
                                    <div class="stock-cambio">
                                        <span class="stock-anterior">25</span>
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="15,18 9,12 15,6"/>
                                        </svg>
                                        <span class="stock-actual">15</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="responsable-celda">
                                        <div class="responsable-avatar">A</div>
                                        <span>Admin</span>
                                    </div>
                                </td>
                                <td><span class="referencia">Producto vencido</span></td>
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
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <!-- Paginación -->
                <div class="paginacion-historial">
                    <div class="paginacion-info">
                        <span>Mostrando 1-3 de 45 movimientos</span>
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
                        <button class="paginacion-boton">15</button>
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
