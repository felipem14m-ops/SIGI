<?php
/* ================================================
   MÓDULO: PRODUCTOS
   Gestión del catálogo de productos del sistema.
   Permite buscar, filtrar, ver, editar y eliminar.
   ================================================ */
session_start();
$pagina_activa = 'productos';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos — La Esquina</title>
    <link rel="stylesheet" href="../../CSS/global.css?v=<?php echo time() . '_' . rand(1000, 9999); ?>">
    <link rel="stylesheet" href="../../CSS/components.css?v=<?php echo time() . '_' . rand(1000, 9999); ?>">
</head>

<body>
    <div class="pagina">

        <!-- Menú lateral compartido -->
        <?php include 'menu.php'; ?>

        <!-- Área de contenido -->
        <div class="contenido">

            <!-- Cabecera de la página -->
            <header class="cabecera">
                <div class="cabecera-titulo">
                    <h1>Productos</h1>
                    <p>Gestión del catálogo de productos</p>
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

            <!-- Contenido principal -->
            <div class="zona-principal">

                <!-- Tarjeta principal con la tabla de productos -->
                <div class="tarjeta">

                    <!-- Encabezado de la tarjeta: título + botones de acción -->
                    <div class="tarjeta-encabezado">
                        <div>
                            <h2>Lista de Productos</h2>
                            <p>Catálogo completo de productos registrados</p>
                        </div>
                        <div class="grupo-botones">
                            <!-- Exportar el listado -->
                            <button class="boton boton-secundario">Exportar</button>
                            <!-- Abrir formulario de nuevo producto -->
                            <button class="boton boton-primario">Nuevo Producto</button>
                        </div>
                    </div>

                    <!-- Barra de filtros: buscar, categoría y estado -->
                    <div class="barra-filtros">
                        <div class="grupo-campo-form">
                            <label>Buscar</label>
                            <input type="text" placeholder="Nombre o código...">
                        </div>
                        <div class="grupo-campo-form">
                            <label>Categoría</label>
                            <select>
                                <option value="">Todas</option>
                                <option value="alimentos">Alimentos</option>
                                <option value="aseo">Aseo Personal</option>
                                <option value="escolares">Útiles Escolares</option>
                            </select>
                        </div>
                        <div class="grupo-campo-form">
                            <label>Estado</label>
                            <select>
                                <option value="">Todos</option>
                                <option value="activo">Activos</option>
                                <option value="inactivo">Inactivos</option>
                                <option value="agotado">Agotados</option>
                            </select>
                        </div>
                    </div>

                    <!-- Tabla de productos -->
                    <div class="tabla-contenedor">
                        <table class="tabla">
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Producto</th>
                                    <th>Categoría</th>
                                    <th>Proveedor</th>
                                    <th>Precio</th>
                                    <th>Stock</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>

                                <!-- Producto 1: activo con stock normal -->
                                <tr>
                                    <td>
                                        <span style="font-size:var(--tam-xs);color:var(--gris-400);font-family:monospace;">PROD-001</span>
                                    </td>
                                    <td>
                                        <div class="celda-principal">Arroz Premium 1kg</div>
                                    </td>
                                    <td>Alimentos</td>
                                    <td>Distribuidora XYZ</td>
                                    <td><strong>$4.500</strong></td>
                                    <td>150</td>
                                    <td><span class="etiqueta etiqueta-activo">Activo</span></td>
                                    <td>
                                        <div class="grupo-botones">
                                            <button class="boton-accion boton-ver">Ver</button>
                                            <button class="boton-accion boton-editar">Editar</button>
                                            <button class="boton-accion boton-eliminar">Eliminar</button>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Producto 2: activo con stock normal -->
                                <tr>
                                    <td>
                                        <span style="font-size:var(--tam-xs);color:var(--gris-400);font-family:monospace;">PROD-002</span>
                                    </td>
                                    <td>
                                        <div class="celda-principal">Leche Entera 1L</div>
                                    </td>
                                    <td>Alimentos</td>
                                    <td>Lácteos del Valle</td>
                                    <td><strong>$5.200</strong></td>
                                    <td>85</td>
                                    <td><span class="etiqueta etiqueta-activo">Activo</span></td>
                                    <td>
                                        <div class="grupo-botones">
                                            <button class="boton-accion boton-ver">Ver</button>
                                            <button class="boton-accion boton-editar">Editar</button>
                                            <button class="boton-accion boton-eliminar">Eliminar</button>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Producto 3: agotado, sin botón eliminar -->
                                <tr>
                                    <td>
                                        <span style="font-size:var(--tam-xs);color:var(--gris-400);font-family:monospace;">PROD-003</span>
                                    </td>
                                    <td>
                                        <div class="celda-principal">Jabón Líquido 500ml</div>
                                    </td>
                                    <td>Aseo Personal</td>
                                    <td>Limpieza Total</td>
                                    <td><strong>$8.900</strong></td>
                                    <!-- Stock en rojo porque está agotado -->
                                    <td style="color:var(--peligro);font-weight:700;">0</td>
                                    <td><span class="etiqueta etiqueta-agotado">Agotado</span></td>
                                    <td>
                                        <div class="grupo-botones">
                                            <button class="boton-accion boton-ver">Ver</button>
                                            <button class="boton-accion boton-editar">Editar</button>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Producto 4: activo con stock bajo -->
                                <tr>
                                    <td>
                                        <span style="font-size:var(--tam-xs);color:var(--gris-400);font-family:monospace;">PROD-004</span>
                                    </td>
                                    <td>
                                        <div class="celda-principal">Cuaderno 100 Hojas</div>
                                    </td>
                                    <td>Útiles Escolares</td>
                                    <td>Papelería Central</td>
                                    <td><strong>$12.000</strong></td>
                                    <!-- Stock en naranja porque está bajo -->
                                    <td style="color:var(--advertencia);font-weight:700;">2</td>
                                    <td><span class="etiqueta etiqueta-agotado">Stock Bajo</span></td>
                                    <td>
                                        <div class="grupo-botones">
                                            <button class="boton-accion boton-ver">Ver</button>
                                            <button class="boton-accion boton-editar">Editar</button>
                                            <button class="boton-accion boton-eliminar">Eliminar</button>
                                        </div>
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                    </div>

                    <!-- Pie de la tabla: paginación y conteo -->
                    <div style="padding:12px 20px;border-top:1px solid var(--gris-100);display:flex;justify-content:space-between;align-items:center;">
                        <span style="font-size:var(--tam-xs);color:var(--gris-500);">Mostrando 4 de 1,247 productos</span>
                        <div class="grupo-botones">
                            <button class="boton boton-secundario" style="padding:5px 12px;font-size:var(--tam-xs);">Anterior</button>
                            <button class="boton boton-secundario" style="padding:5px 12px;font-size:var(--tam-xs);">Siguiente</button>
                        </div>
                    </div>

                </div><!-- fin tarjeta -->

            </div><!-- fin zona-principal -->
        </div><!-- fin contenido -->
    </div><!-- fin pagina -->
</body>

</html>