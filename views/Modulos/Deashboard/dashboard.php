
<div class="pagina">

    <!-- Menú lateral compartido -->
    <?php include 'menu.php'; ?>

    <!-- Área de contenido -->
    <div class="contenido">

        <!-- Cabecera de la página -->
        <header class="cabecera">
            <div class="cabecera-titulo">
                <h1>Panel Principal</h1>
                <p>Bienvenido, <?php echo htmlspecialchars($_SESSION['nombre'] ?? 'Administrador'); ?></p>
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

        <!-- Contenido principal del dashboard -->
        <div class="zona-principal">

            <!-- Aviso de productos con poco stock -->
            <div class="aviso-sistema aviso-advertencia">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                    <line x1="12" y1="9" x2="12" y2="13"/>
                    <line x1="12" y1="17" x2="12.01" y2="17"/>
                </svg>
                <div>
                    <strong>3 productos con poco stock</strong>
                    Revisa el inventario para evitar quedarte sin productos.
                    <a href="inventario.php" style="color:var(--advertencia);font-weight:600;margin-left:6px;">Ver inventario &rarr;</a>
                </div>
            </div>

            <!-- Cuadrícula de métricas principales -->
            <div class="cuadricula-metricas">

                <!-- Métrica: Total de productos -->
                <div class="tarjeta-metrica">
                    <div class="metrica-fila-superior">
                        <span class="metrica-etiqueta">Total Productos</span>
                        <div class="metrica-icono fondo-azul">
                            <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                                <polyline points="3.27 6.96 12 12.01 20.73 6.96"/>
                                <line x1="12" y1="22.08" x2="12" y2="12"/>
                            </svg>
                        </div>
                    </div>
                    <div class="metrica-valor">1,247</div>
                    <div class="metrica-tendencia positiva">&#8593; 12% este mes</div>
                </div>

                <!-- Métrica: Ventas del día -->
                <div class="tarjeta-metrica">
                    <div class="metrica-fila-superior">
                        <span class="metrica-etiqueta">Ventas Hoy</span>
                        <div class="metrica-icono fondo-verde">
                            <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="12" y1="1" x2="12" y2="23"/>
                                <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                            </svg>
                        </div>
                    </div>
                    <div class="metrica-valor">$2,450</div>
                    <div class="metrica-tendencia positiva">&#8593; 5% vs ayer</div>
                </div>

                <!-- Métrica: Ventas del mes con barra de progreso -->
                <div class="tarjeta-metrica">
                    <div class="metrica-fila-superior">
                        <span class="metrica-etiqueta">Ventas del Mes</span>
                        <div class="metrica-icono fondo-morado">
                            <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
                            </svg>
                        </div>
                    </div>
                    <div class="metrica-valor">$45,280</div>
                    <!-- Barra de progreso hacia el objetivo mensual -->
                    <div class="barra-progreso-contenedor">
                        <div class="barra-progreso-fila">
                            <span>Objetivo mensual</span>
                            <strong style="color:var(--primario);">85%</strong>
                        </div>
                        <div class="barra-progreso">
                            <div class="barra-progreso-relleno" style="width:85%;background:linear-gradient(90deg,#7c3aed,#a78bfa);"></div>
                        </div>
                    </div>
                </div>

                <!-- Métrica: Alertas activas -->
                <div class="tarjeta-metrica">
                    <div class="metrica-fila-superior">
                        <span class="metrica-etiqueta">Alertas Activas</span>
                        <div class="metrica-icono fondo-naranja">
                            <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                                <line x1="12" y1="9" x2="12" y2="13"/>
                                <line x1="12" y1="17" x2="12.01" y2="17"/>
                            </svg>
                        </div>
                    </div>
                    <div class="metrica-valor">10</div>
                    <div class="metrica-tendencia negativa">3 stock bajo &bull; 7 vencimiento</div>
                </div>

            </div><!-- fin cuadricula-metricas -->

            <!-- Fila inferior: tabla de stock + accesos rápidos -->
            <div style="display:grid;grid-template-columns:1fr 260px;gap:16px;margin-bottom:20px;">

                <!-- Tabla de productos con poco stock -->
                <div class="tarjeta" style="margin-bottom:0;">
                    <div class="tarjeta-encabezado">
                        <div>
                            <h2>Productos con Poco Stock</h2>
                            <p>Requieren reabastecimiento pronto</p>
                        </div>
                        <a href="inventario.php" class="boton boton-secundario">Ver todos</a>
                    </div>
                    <div class="tabla-contenedor">
                        <table class="tabla">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Categoría</th>
                                    <th>Stock</th>
                                    <th>Estado</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="celda-principal">Arroz Premium 1kg</div>
                                        <div class="celda-secundaria">PROD-001</div>
                                    </td>
                                    <td>Alimentos</td>
                                    <td>
                                        <span style="font-weight:700;color:var(--peligro);">5</span>
                                        <span style="color:var(--gris-400);font-size:0.75rem;"> / 10</span>
                                    </td>
                                    <td><span class="etiqueta etiqueta-inactivo">Crítico</span></td>
                                    <td><button class="boton-accion boton-editar">Reabastecer</button></td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="celda-principal">Jabón Líquido 500ml</div>
                                        <div class="celda-secundaria">PROD-045</div>
                                    </td>
                                    <td>Aseo Personal</td>
                                    <td>
                                        <span style="font-weight:700;color:var(--peligro);">3</span>
                                        <span style="color:var(--gris-400);font-size:0.75rem;"> / 8</span>
                                    </td>
                                    <td><span class="etiqueta etiqueta-inactivo">Crítico</span></td>
                                    <td><button class="boton-accion boton-editar">Reabastecer</button></td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="celda-principal">Cuaderno 100 Hojas</div>
                                        <div class="celda-secundaria">PROD-089</div>
                                    </td>
                                    <td>Útiles Escolares</td>
                                    <td>
                                        <span style="font-weight:700;color:var(--advertencia);">2</span>
                                        <span style="color:var(--gris-400);font-size:0.75rem;"> / 5</span>
                                    </td>
                                    <td><span class="etiqueta etiqueta-agotado">Bajo</span></td>
                                    <td><button class="boton-accion boton-editar">Reabastecer</button></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Panel de accesos rápidos -->
                <div class="panel-accesos">
                    <h3>Accesos Rápidos</h3>
                    <div class="lista-accesos">
                        <a href="ventas.php" class="acceso-rapido acceso-azul">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
                                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                            </svg>
                            Nueva Venta
                        </a>
                        <a href="productos.php" class="acceso-rapido acceso-verde">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="12" y1="5" x2="12" y2="19"/>
                                <line x1="5" y1="12" x2="19" y2="12"/>
                            </svg>
                            Nuevo Producto
                        </a>
                        <a href="inventario.php" class="acceso-rapido acceso-naranja">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="8" y1="6" x2="21" y2="6"/>
                                <line x1="8" y1="12" x2="21" y2="12"/>
                                <line x1="8" y1="18" x2="21" y2="18"/>
                                <line x1="3" y1="6" x2="3.01" y2="6"/>
                                <line x1="3" y1="12" x2="3.01" y2="12"/>
                                <line x1="3" y1="18" x2="3.01" y2="18"/>
                            </svg>
                            Ver Inventario
                        </a>
                        <a href="reportes.php" class="acceso-rapido acceso-morado">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="18" y1="20" x2="18" y2="10"/>
                                <line x1="12" y1="20" x2="12" y2="4"/>
                                <line x1="6" y1="20" x2="6" y2="14"/>
                            </svg>
                            Ver Reportes
                        </a>
                    </div>
                </div>

            </div><!-- fin fila inferior -->

            <!-- Tabla de últimas ventas -->
            <div class="tarjeta">
                <div class="tarjeta-encabezado">
                    <div>
                        <h2>Últimas Ventas</h2>
                        <p>Transacciones recientes del día</p>
                    </div>
                    <div class="grupo-botones">
                        <button class="boton boton-secundario">Ver Todo</button>
                        <a href="ventas.php" class="boton boton-primario">Nueva Venta</a>
                    </div>
                </div>
                <div class="tabla-contenedor">
                    <table class="tabla">
                        <thead>
                            <tr>
                                <th>N° Venta</th>
                                <th>Fecha</th>
                                <th>Productos</th>
                                <th>Total</th>
                                <th>Forma de Pago</th>
                                <th>Estado</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span class="celda-principal">V-2025003</span></td>
                                <td>15/03/2025 14:30</td>
                                <td>5 productos</td>
                                <td><strong>$28.400</strong></td>
                                <td>Efectivo</td>
                                <td><span class="etiqueta etiqueta-completado">Completado</span></td>
                                <td><button class="boton-accion boton-ver">Ver</button></td>
                            </tr>
                            <tr>
                                <td><span class="celda-principal">V-2025002</span></td>
                                <td>15/03/2025 13:15</td>
                                <td>3 productos</td>
                                <td><strong>$15.600</strong></td>
                                <td>Nequi</td>
                                <td><span class="etiqueta etiqueta-completado">Completado</span></td>
                                <td><button class="boton-accion boton-ver">Ver</button></td>
                            </tr>
                            <tr>
                                <td><span class="celda-principal">V-2025001</span></td>
                                <td>15/03/2025 11:00</td>
                                <td>8 productos</td>
                                <td><strong>$42.100</strong></td>
                                <td>Tarjeta Débito</td>
                                <td><span class="etiqueta etiqueta-completado">Completado</span></td>
                                <td><button class="boton-accion boton-ver">Ver</button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div><!-- fin tabla ventas -->

        </div><!-- fin zona-principal -->
    </div><!-- fin contenido -->
</div><!-- fin pagina -->
</body>
</html>
