<?php 
session_start();
$pagina_activa = 'reportes';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes - La Esquina</title>
    <link rel="stylesheet" href="../../CSS/global.css?v=<?php echo time() . '_' . rand(10000, 99999); ?>">
    <link rel="stylesheet" href="../../CSS/components.css?v=<?php echo time() . '_' . rand(10000, 99999); ?>">
    </head>

<body>
    <div class="pagina">
        <?php include 'menu.php'; ?>
        <div class="contenido">
            <header class="cabecera">
                <div class="cabecera-titulo">
                    <h1>Reportes</h1>
                    <p>Análisis y descarga de datos</p>
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

                <!-- Números del mes -->
                <div class="fila-numeros">
                    <div class="tarjeta-numero">
                        <div class="tarjeta-numero-icono fondo-azul">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="12" y1="1" x2="12" y2="23"/>
                                <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                            </svg>
                        </div>
                        <div class="tarjeta-numero-texto">
                            <h3>Ingresos del Mes</h3>
                            <p>$45.280.000</p>
                            <span>↑ 15% vs mes anterior</span>
                        </div>
                    </div>
                    <div class="tarjeta-numero">
                        <div class="tarjeta-numero-icono fondo-verde">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="23,6 13.5,15.5 8.5,10.5 1,18"/>
                                <polyline points="17,6 23,6 23,12"/>
                            </svg>
                        </div>
                        <div class="tarjeta-numero-texto">
                            <h3>Ganancia Neta</h3>
                            <p>$12.450.000</p>
                            <span>Margen: 27.5%</span>
                        </div>
                    </div>
                    <div class="tarjeta-numero">
                        <div class="tarjeta-numero-icono fondo-naranja">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                                <polyline points="3.27,6.96 12,12.01 20.73,6.96"/>
                                <line x1="12" y1="22.08" x2="12" y2="12"/>
                            </svg>
                        </div>
                        <div class="tarjeta-numero-texto">
                            <h3>Productos Vendidos</h3>
                            <p>3,245</p>
                            <span>↑ 8% vs mes anterior</span>
                        </div>
                    </div>
                    <div class="tarjeta-numero">
                        <div class="tarjeta-numero-icono fondo-rojo">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                            <line x1="12" y1="9" x2="12" y2="13"/>
                            <line x1="12" y1="17" x2="12.01" y2="17"/>
                        </svg>
                    </div>
                        <div class="tarjeta-numero-texto">
                            <h3>Pérdidas</h3>
                            <p>$125.000</p>
                            <span>↓ 5% vs mes anterior</span>
                        </div>
                    </div>
                </div>

                <!-- Gráficos -->
                <div class="fila-graficos">
                    <div class="caja-grafico">
                        <h3>
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right:8px">
                                <line x1="18" y1="20" x2="18" y2="10"/>
                                <line x1="12" y1="20" x2="12" y2="4"/>
                                <line x1="6" y1="20" x2="6" y2="14"/>
                            </svg>
                            Ventas por Día
                        </h3>
                        <div class="barras">
                            <div class="barra" style="height:60%"><span class="barra-nombre">Lun</span></div>
                            <div class="barra" style="height:80%"><span class="barra-nombre">Mar</span></div>
                            <div class="barra" style="height:45%"><span class="barra-nombre">Mié</span></div>
                            <div class="barra" style="height:90%"><span class="barra-nombre">Jue</span></div>
                            <div class="barra" style="height:100%"><span class="barra-nombre">Vie</span></div>
                            <div class="barra" style="height:70%"><span class="barra-nombre">Sáb</span></div>
                            <div class="barra" style="height:50%"><span class="barra-nombre">Dom</span></div>
                        </div>
                    </div>
                    <div class="caja-grafico">
                        <h3>
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right:8px">
                                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                                <polyline points="3.27,6.96 12,12.01 20.73,6.96"/>
                                <line x1="12" y1="22.08" x2="12" y2="12"/>
                            </svg>
                            Productos Más Vendidos
                        </h3>
                        <table>
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Cantidad</th>
                                    <th>Ingresos</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Arroz Premium 1kg</td>
                                    <td>450</td>
                                    <td>$2.025.000</td>
                                </tr>
                                <tr>
                                    <td>Leche Entera 1L</td>
                                    <td>380</td>
                                    <td>$1.976.000</td>
                                </tr>
                                <tr>
                                    <td>Pan Integral</td>
                                    <td>320</td>
                                    <td>$1.216.000</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Descargar reporte -->
                <div class="caja-descarga">
                    <div class="caja-descarga-titulo">
                        <h2>Descargar Reporte</h2>
                    </div>
                    <form method="GET" action="">
                        <div class="formulario-descarga">
                            <div class="campo">
                                <label>Tipo de Reporte</label>
                                <select name="tipo">
                                    <option>Inventario Actual</option>
                                    <option>Ventas por Período</option>
                                    <option>Ganancias y Pérdidas</option>
                                    <option>Productos Próximos a Vencer</option>
                                </select>
                            </div>
                            <div class="campo">
                                <label>Formato</label>
                                <select name="formato">
                                    <option>Excel (.xlsx)</option>
                                    <option>CSV (.csv)</option>
                                    <option>PDF (.pdf)</option>
                                </select>
                            </div>
                            <div class="campo">
                                <label>Fecha de Inicio</label>
                                <input type="date" name="fecha_inicio">
                            </div>
                            <div class="campo">
                                <label>Fecha de Fin</label>
                                <input type="date" name="fecha_fin">
                            </div>
                            <div class="campo campo-ancho botones-derecha">
                                <button type="button" class="boton-accion boton-secundario">Vista Previa</button>
                                <button type="submit" class="boton-accion boton-primario">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                        <polyline points="7,10 12,15 17,10"/>
                                        <line x1="12" y1="15" x2="12" y2="3"/>
                                    </svg>
                                    Descargar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</body>

</html>