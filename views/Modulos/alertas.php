<?php 
session_start();
$pagina_activa = 'alertas';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alertas - La Esquina</title>
    <link rel="stylesheet" href="../../CSS/global.css?v=<?php echo time() . '_' . rand(1000, 9999); ?>">
    <link rel="stylesheet" href="../../CSS/components.css?v=<?php echo time() . '_' . rand(1000, 9999); ?>">
    </head>
<body>
<div class="pagina">
    <?php include 'menu.php'; ?>
    <div class="contenido">
        <header class="cabecera">
            <div class="cabecera-titulo">
                <h1>Alertas</h1>
                <p>Eventos importantes del sistema</p>
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

            <!-- Resumen de alertas -->
            <div class="fila-resumen-alertas">
                <div class="caja-alerta-resumen roja">
                    <div style="font-size:32px;margin-bottom:10px;">⚠️</div>
                    <div class="numero-grande rojo">5</div>
                    <div class="tipo-alerta">Poco Stock</div>
                </div>
                <div class="caja-alerta-resumen naranja">
                    <div style="font-size:32px;margin-bottom:10px;">📅</div>
                    <div class="numero-grande naranja">3</div>
                    <div class="tipo-alerta">Próximos a Vencer</div>
                </div>
                <div class="caja-alerta-resumen azul">
                    <div style="font-size:32px;margin-bottom:10px;">🔧</div>
                    <div class="numero-grande azul">2</div>
                    <div class="tipo-alerta">Mantenimiento</div>
                </div>
            </div>

            <!-- Alertas de stock bajo -->
            <div class="seccion-alertas">
                <div class="seccion-alertas-titulo">
                    <h2>⚠️ Poco Stock (5 alertas)</h2>
                    <button class="boton-resolver">✓ Marcar todas como atendidas</button>
                </div>
                <div>
                    <div class="alerta-item">
                        <div class="punto-alerta punto-rojo"></div>
                        <div class="alerta-info">
                            <div class="alerta-nombre">Arroz Premium 1kg — Stock muy bajo</div>
                            <div class="alerta-detalles">
                                <div class="detalle"><span class="detalle-nombre">Código:</span><span>PROD-001</span></div>
                                <div class="detalle"><span class="detalle-nombre">Stock actual:</span><span class="detalle-valor-critico">5 unidades</span></div>
                                <div class="detalle"><span class="detalle-nombre">Stock mínimo:</span><span>10 unidades</span></div>
                                <div class="detalle"><span class="detalle-nombre">Categoría:</span><span>Alimentos</span></div>
                            </div>
                        </div>
                        <div class="alerta-acciones">
                            <button class="boton-atender">Reabastecer</button>
                            <button class="boton-ignorar">Ignorar</button>
                        </div>
                    </div>
                    <div class="alerta-item">
                        <div class="punto-alerta punto-rojo"></div>
                        <div class="alerta-info">
                            <div class="alerta-nombre">Jabón Líquido 500ml — Stock bajo</div>
                            <div class="alerta-detalles">
                                <div class="detalle"><span class="detalle-nombre">Código:</span><span>PROD-045</span></div>
                                <div class="detalle"><span class="detalle-nombre">Stock actual:</span><span class="detalle-valor-critico">3 unidades</span></div>
                                <div class="detalle"><span class="detalle-nombre">Stock mínimo:</span><span>8 unidades</span></div>
                                <div class="detalle"><span class="detalle-nombre">Categoría:</span><span>Aseo Personal</span></div>
                            </div>
                        </div>
                        <div class="alerta-acciones">
                            <button class="boton-atender">Reabastecer</button>
                            <button class="boton-ignorar">Ignorar</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Alertas de vencimiento -->
            <div class="seccion-alertas">
                <div class="seccion-alertas-titulo">
                    <h2>📅 Próximos a Vencer (3 alertas)</h2>
                    <button class="boton-resolver">✓ Marcar todas como atendidas</button>
                </div>
                <div>
                    <div class="alerta-item">
                        <div class="punto-alerta punto-naranja"></div>
                        <div class="alerta-info">
                            <div class="alerta-nombre">Leche Entera 1L — Vence pronto</div>
                            <div class="alerta-detalles">
                                <div class="detalle"><span class="detalle-nombre">Código:</span><span>PROD-002</span></div>
                                <div class="detalle"><span class="detalle-nombre">Vence el:</span><span class="detalle-valor-critico">20/03/2025 (1 día)</span></div>
                                <div class="detalle"><span class="detalle-nombre">Stock:</span><span>85 unidades</span></div>
                                <div class="detalle"><span class="detalle-nombre">Categoría:</span><span>Alimentos</span></div>
                            </div>
                        </div>
                        <div class="alerta-acciones">
                            <button class="boton-atender">Crear Oferta</button>
                            <button class="boton-ignorar">Ignorar</button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
</body>
</html>
