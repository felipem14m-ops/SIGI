<?php 
session_start();
$pagina_activa = 'configuracion';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuración - La Esquina</title>
    <link rel="stylesheet" href="../../CSS/global.css?v=<?php echo time() . '_' . rand(10000, 99999); ?>">
    <link rel="stylesheet" href="../../CSS/components.css?v=<?php echo time() . '_' . rand(10000, 99999); ?>">
    </head>
<body>
<div class="pagina">
    <?php include 'menu.php'; ?>
    <div class="contenido">
        <header class="cabecera">
            <div class="cabecera-titulo">
                <h1>Configuración</h1>
                <p>Opciones y parámetros del sistema</p>
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
            <div class="fila-configuracion">

                <!-- Formas de pago -->
                <div class="caja-config">
                    <div class="caja-config-titulo"><span style="font-size:24px">💳</span><h2>Formas de Pago</h2></div>
                    <div class="caja-config-cuerpo">
                        <div class="fila-opcion">
                            <div class="opcion-nombre"><h4>Efectivo</h4><p>Forma de pago por defecto</p></div>
                            <span class="etiqueta-encendido">Activo</span>
                        </div>
                        <div class="fila-opcion">
                            <div class="opcion-nombre"><h4>Tarjeta Débito</h4><p>Débito / Crédito</p></div>
                            <div style="display:flex;gap:8px;"><button class="boton-accion boton-editar">Editar</button></div>
                        </div>
                        <div class="fila-opcion">
                            <div class="opcion-nombre"><h4>Nequi</h4><p>Billetera Digital</p></div>
                            <div style="display:flex;gap:8px;"><button class="boton-accion boton-editar">Editar</button></div>
                        </div>
                        <div class="fila-opcion">
                            <div class="opcion-nombre"><h4>Daviplata</h4><p>Billetera Digital</p></div>
                            <div style="display:flex;gap:8px;"><button class="boton-accion boton-editar">Editar</button></div>
                        </div>
                        <div class="boton-agregar-container">
                            <button class="boton-accion boton-primario boton-agregar-pago">
                                
                                Agregar
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Límites de stock -->
                <div class="caja-config">
                    <div class="caja-config-titulo">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="18" y1="20" x2="18" y2="10"/>
                            <line x1="12" y1="20" x2="12" y2="4"/>
                            <line x1="6" y1="20" x2="6" y2="14"/>
                        </svg>
                        <h2>Límites de Stock</h2>
                    </div>
                    <div class="caja-config-cuerpo">
                        <form method="POST" action="">
                        <div class="formulario-config">
                            <div class="campo-config">
                                <label>Cantidad mínima por defecto</label>
                                <input type="number" name="stock_minimo" value="5">
                                <small style="color:var(--texto-suave);">Se genera una alerta cuando el stock baja de este número</small>
                            </div>
                            <div class="campo-config">
                                <label>Días de aviso antes de vencer</label>
                                <input type="number" name="dias_aviso" value="30">
                                <small style="color:var(--texto-suave);">Se genera una alerta X días antes del vencimiento</small>
                            </div>
                            <div class="botones-guardar">
                                <button type="button" class="boton-cancelar">Cancelar</button>
                                <button type="submit" class="boton-guardar">✓ Guardar</button>
                            </div>
                        </div>
                        </form>
                    </div>
                </div>

                <!-- Avisos automáticos -->
                <div class="caja-config">
                    <div class="caja-config-titulo">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                            <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                        </svg>
                        <h2>Avisos Automáticos</h2>
                    </div>
                    <div class="caja-config-cuerpo">
                        <div class="fila-opcion">
                            <div class="opcion-nombre">
                                <h4>Aviso de poco stock</h4>
                                <p>Notificar cuando un producto tiene poco stock</p>
                            </div>
                            <div class="interruptor encendido"></div>
                        </div>
                        <div class="fila-opcion">
                            <div class="opcion-nombre">
                                <h4>Aviso de vencimiento</h4>
                                <p>Notificar cuando un producto está próximo a vencer</p>
                            </div>
                            <div class="interruptor encendido"></div>
                        </div>
                        <div class="fila-opcion">
                            <div class="opcion-nombre">
                                <h4>Aviso de mantenimiento</h4>
                                <p>Recordar el mantenimiento de equipos</p>
                            </div>
                            <div class="interruptor encendido"></div>
                        </div>
                    </div>
                </div>

                <!-- Datos del negocio -->
                <div class="caja-config">
                    <div class="caja-config-titulo"><span style="font-size:24px">🏪</span><h2>Datos del Negocio</h2></div>
                    <div class="caja-config-cuerpo">
                        <form method="POST" action="">
                        <div class="formulario-config">
                            <div class="campo-config">
                                <label>Nombre del Negocio</label>
                                <input type="text" name="nombre" value="La Esquina">
                            </div>
                            <div class="campo-config">
                                <label>Teléfono</label>
                                <input type="text" name="telefono" value="+57 315 123 4567">
                            </div>
                            <div class="campo-config">
                                <label>Correo</label>
                                <input type="email" name="correo" value="info@laesquina.com">
                            </div>
                            <div class="campo-config">
                                <label>Dirección</label>
                                <input type="text" name="direccion" value="Cra 5 # 12-45, Neiva">
                            </div>
                            <div class="botones-guardar">
                                <button type="button" class="boton-cancelar">Cancelar</button>
                                <button type="submit" class="boton-guardar">✓ Guardar</button>
                            </div>
                        </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
</body>
</html>
