<?php
/* ================================================
   MENÚ LATERAL COMPARTIDO
   Incluir en cada módulo con: include 'menu.php';
   Definir $pagina_activa antes del include para
   marcar el enlace activo. Ejemplo:
       $pagina_activa = 'productos';
   ================================================ */
$activa = $pagina_activa ?? '';
?>
<aside class="menu-lateral">

    <!-- Encabezado: logo cuadrado + nombre del sistema -->
    <div class="menu-encabezado">
        <div class="menu-logo-caja">
            <!-- Ícono profesional de inventario -->
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <!-- Caja principal -->
                <rect x="4" y="9" width="16" height="11" rx="1" fill="white" opacity="0.95"/>
                <!-- Tapa de la caja -->
                <path d="M3 9 L12 5 L21 9" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" fill="none" opacity="0.9"/>
                <!-- Línea central de la tapa -->
                <line x1="12" y1="5" x2="12" y2="9" stroke="white" stroke-width="1.5" opacity="0.7"/>
                <!-- Franja de identificación -->
                <rect x="4" y="12" width="16" height="1.5" fill="#2563eb" opacity="0.6"/>
                <!-- Código de barras simplificado -->
                <rect x="6" y="14" width="1" height="3" fill="#2563eb" opacity="0.8"/>
                <rect x="8" y="14" width="0.5" height="3" fill="#2563eb" opacity="0.8"/>
                <rect x="9.5" y="14" width="1" height="3" fill="#2563eb" opacity="0.8"/>
                <rect x="11" y="14" width="0.5" height="3" fill="#2563eb" opacity="0.8"/>
                <rect x="12.5" y="14" width="1" height="3" fill="#2563eb" opacity="0.8"/>
                <rect x="14" y="14" width="0.5" height="3" fill="#2563eb" opacity="0.8"/>
                <rect x="15.5" y="14" width="1" height="3" fill="#2563eb" opacity="0.8"/>
                <rect x="17" y="14" width="0.5" height="3" fill="#2563eb" opacity="0.8"/>
                <!-- Puntos de inventario -->
                <circle cx="7" cy="17.5" r="0.8" fill="#2563eb" opacity="0.9"/>
                <circle cx="12" cy="17.5" r="0.8" fill="#2563eb" opacity="0.9"/>
                <circle cx="17" cy="17.5" r="0.8" fill="#2563eb" opacity="0.9"/>
                <!-- Indicador de stock -->
                <path d="M19 7 L20 7 M19.5 6 L19.5 8" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>
        <div>
            <div class="menu-nombre">Tienda</div>
            <div class="menu-subtitulo">Sistema de Inventario</div>
        </div>
    </div>

    <!-- Navegación principal -->
    <ul class="menu-lista">

        <!-- Inicio -->
        <li>
            <a href="dashboard.php" class="<?= $activa === 'dashboard' ? 'activo' : '' ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="7" height="7" rx="1"/>
                    <rect x="14" y="3" width="7" height="7" rx="1"/>
                    <rect x="14" y="14" width="7" height="7" rx="1"/>
                    <rect x="3" y="14" width="7" height="7" rx="1"/>
                </svg>
                Inicio
            </a>
        </li>

        <!-- Separador de sección: Gestión -->
        <li class="menu-seccion">Gestión</li>

        <!-- Productos -->
        <li>
            <a href="productos.php" class="<?= $activa === 'productos' ? 'activo' : '' ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                    <polyline points="3.27 6.96 12 12.01 20.73 6.96"/>
                    <line x1="12" y1="22.08" x2="12" y2="12"/>
                </svg>
                Productos
            </a>
        </li>

        <!-- Ventas -->
        <li>
            <a href="ventas.php" class="<?= $activa === 'ventas' ? 'activo' : '' ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="9" cy="21" r="1"/>
                    <circle cx="20" cy="21" r="1"/>
                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                </svg>
                Ventas
            </a>
        </li>

        <!-- Inventario -->
        <li>
            <a href="inventario.php" class="<?= $activa === 'inventario' ? 'activo' : '' ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="8" y1="6" x2="21" y2="6"/>
                    <line x1="8" y1="12" x2="21" y2="12"/>
                    <line x1="8" y1="18" x2="21" y2="18"/>
                    <line x1="3" y1="6" x2="3.01" y2="6"/>
                    <line x1="3" y1="12" x2="3.01" y2="12"/>
                    <line x1="3" y1="18" x2="3.01" y2="18"/>
                </svg>
                Inventario
            </a>
        </li>

        <!-- Proveedores -->
        <li>
            <a href="proveedores.php" class="<?= $activa === 'proveedores' ? 'activo' : '' ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="1" y="3" width="15" height="13" rx="1"/>
                    <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/>
                    <circle cx="5.5" cy="18.5" r="2.5"/>
                    <circle cx="18.5" cy="18.5" r="2.5"/>
                </svg>
                Proveedores
            </a>
        </li>

        <!-- Separador de sección: Sistema -->
        <li class="menu-seccion">Sistema</li>

        <!-- Usuarios -->
        <li>
            <a href="usuarios.php" class="<?= $activa === 'usuarios' ? 'activo' : '' ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
                Usuarios
            </a>
        </li>

        <!-- Alertas con badge de cantidad -->
        <li>
            <a href="alertas.php" class="<?= $activa === 'alertas' ? 'activo' : '' ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                    <line x1="12" y1="9" x2="12" y2="13"/>
                    <line x1="12" y1="17" x2="12.01" y2="17"/>
                </svg>
                Alertas
                <!-- Badge con número de alertas pendientes -->
                <span style="margin-left:auto;background:#dc2626;color:#fff;font-size:0.62rem;font-weight:700;padding:1px 6px;border-radius:99px;line-height:1.7;">10</span>
            </a>
        </li>

        <!-- Reportes -->
        <li>
            <a href="reportes.php" class="<?= $activa === 'reportes' ? 'activo' : '' ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="20" x2="18" y2="10"/>
                    <line x1="12" y1="20" x2="12" y2="4"/>
                    <line x1="6" y1="20" x2="6" y2="14"/>
                </svg>
                Reportes
            </a>
        </li>

        <!-- Configuración -->
        <li>
            <a href="configuracion.php" class="<?= $activa === 'configuracion' ? 'activo' : '' ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="3"/>
                    <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/>
                </svg>
                Configuración
            </a>
        </li>

    </ul>

    <!-- Pie del menú: botón de cerrar sesión -->
    <div class="menu-pie">
        <a href="login.php">
            <button class="boton-salir">
                <!-- Ícono de salida -->
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                    <polyline points="16 17 21 12 16 7"/>
                    <line x1="21" y1="12" x2="9" y2="12"/>
                </svg>
                Cerrar Sesión
            </button>
        </a>
    </div>

</aside>
