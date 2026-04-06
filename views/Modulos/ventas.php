<?php 
session_start();
$pagina_activa = 'ventas';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Venta - La Esquina</title>
    <link rel="stylesheet" href="../../CSS/global.css?v=<?php echo time() . '_' . rand(1000, 9999); ?>">
    <link rel="stylesheet" href="../../CSS/components.css?v=<?php echo time() . '_' . rand(1000, 9999); ?>">
    </head>

<body>
    <div class="pagina">
        <?php include 'menu.php'; ?>
        <div class="contenido">
            <header class="cabecera">
                <div class="cabecera-titulo">
                    <h1>Nueva Venta</h1>
                    <p>Registrar una venta</p>
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
                <div class="pantalla-venta">

                    <!-- Lista de productos -->
                    <div class="panel-productos">
                        <div class="buscador-productos">
                            <input type="text" placeholder="🔍 Buscar producto o código...">
                            <select>
                                <option>Todas las categorías</option>
                                <option>Alimentos</option>
                                <option>Aseo Personal</option>
                                <option>Útiles Escolares</option>
                            </select>
                        </div>
                        <div class="lista-productos">
                            <div class="producto-caja">
                                <div class="producto-icono">🍚</div>
                                <p class="producto-nombre">Arroz Premium 1kg</p>
                                <p class="producto-precio">$4.500</p>
                                <p class="producto-stock">Stock: 150</p>
                            </div>
                            <div class="producto-caja">
                                <div class="producto-icono">🥛</div>
                                <p class="producto-nombre">Leche Entera 1L</p>
                                <p class="producto-precio">$5.200</p>
                                <p class="producto-stock">Stock: 85</p>
                            </div>
                            <div class="producto-caja">
                                <div class="producto-icono">🍞</div>
                                <p class="producto-nombre">Pan Integral</p>
                                <p class="producto-precio">$3.800</p>
                                <p class="producto-stock">Stock: 42</p>
                            </div>
                            <div class="producto-caja">
                                <div class="producto-icono">🧼</div>
                                <p class="producto-nombre">Jabón Líquido</p>
                                <p class="producto-precio">$8.900</p>
                                <p class="producto-stock">Sin stock</p>
                            </div>
                            <div class="producto-caja">
                                <div class="producto-icono">📓</div>
                                <p class="producto-nombre">Cuaderno 100H</p>
                                <p class="producto-precio">$12.000</p>
                                <p class="producto-stock">Stock: 45</p>
                            </div>
                            <div class="producto-caja">
                                <div class="producto-icono">✏️</div>
                                <p class="producto-nombre">Lápiz x12</p>
                                <p class="producto-precio">$6.500</p>
                                <p class="producto-stock">Stock: 78</p>
                            </div>
                        </div>
                    </div>

                    <!-- Carrito -->
                    <div class="panel-carrito">
                        <h3>🛒 Carrito</h3>
                        <div class="lista-carrito">
                            <div class="carrito-fila">
                                <div class="carrito-producto">
                                    <h4>Arroz Premium 1kg</h4>
                                    <p>$4.500 x 2</p>
                                </div>
                                <div class="cantidad-controles">
                                    <button>-</button>
                                    <span>2</span>
                                    <button>+</button>
                                </div>
                                <div style="font-weight:600;">$9.000</div>
                            </div>
                            <div class="carrito-fila">
                                <div class="carrito-producto">
                                    <h4>Leche Entera 1L</h4>
                                    <p>$5.200 x 3</p>
                                </div>
                                <div class="cantidad-controles">
                                    <button>-</button>
                                    <span>3</span>
                                    <button>+</button>
                                </div>
                                <div style="font-weight:600;">$15.600</div>
                            </div>
                        </div>

                        <div class="resumen-pago">
                            <div class="fila-total">
                                <span>Subtotal</span>
                                <span>$24.600</span>
                            </div>
                            <div class="fila-total">
                                <span>Descuento</span>
                                <span style="color:var(--verde);">-$0</span>
                            </div>
                            <div class="fila-total total-final">
                                <span>Total</span>
                                <span>$24.600</span>
                            </div>

                            <div class="campo-pago">
                                <label>Forma de Pago</label>
                                <select>
                                    <option>Efectivo</option>
                                    <option>Tarjeta Débito</option>
                                    <option>Tarjeta Crédito</option>
                                    <option>Nequi</option>
                                    <option>Daviplata</option>
                                </select>
                            </div>

                            <div class="botones-venta">
                                <button class="boton boton-gris">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="10"/>
                                        <line x1="15" y1="9" x2="9" y2="15"/>
                                        <line x1="9" y1="9" x2="15" y2="15"/>
                                    </svg>
                                    Cancelar
                                </button>
                                <button class="boton boton-azul">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="20,6 9,17 4,12"/>
                                    </svg>
                                    Confirmar
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</body>

</html>