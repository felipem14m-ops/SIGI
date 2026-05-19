<?php
$usuario_arr = $_SESSION['usuario'] ?? [];
$rol = $_SESSION['rol'] ?? $usuario_arr['rol'] ?? 'Invitado';
$nombre = $_SESSION['nombre'] ?? $usuario_arr['nombre'] ?? 'Usuario';

// Obtener número dinámico de alertas de stock para la campanita
$num_alertas_sidebar = 0;
try {
    if (!class_exists('Database')) {
        require_once __DIR__ . '/../../config/database.php';
    }
    $db_sidebar = (new Database())->conectar();
    $stmt_alertas = $db_sidebar->query("SELECT COUNT(*) FROM productos WHERE stock_actual <= stock_minimo AND estado = 'activo'");
    $num_alertas_sidebar = (int) $stmt_alertas->fetchColumn();
} catch (Throwable $e) {
    // Silencioso en caso de error
}
?>

<?php /* Styles moved to CSS/sidebar.css */ ?>

<aside class="barra-lateral">
    <!-- Header del Sidebar (Logo) -->
    <div class="cabecera-barra">
        <div class="logo-icon-container">
            <i class="fas fa-store"></i>
        </div>
        <div class="logo-text-container">
            <span class="logo-title">SIGI</span>
            <span class="logo-subtitle">Inventario</span>
        </div>
    </div>

    <!-- Navegación -->
    <nav class="menu-navegacion">
        <?php
        $current_page = basename($_SERVER['PHP_SELF']);
        // Determinar la ruta base relativa desde Dashboard
        $dashboard_base = '';
        ?>
        <?php if (strtolower($rol) === 'administrador'): ?>
            <!-- SIDEBAR PARA ADMINISTRADOR - ACCESO COMPLETO -->

            <div class="seccion-menu">
                <div class="titulo-seccion">Dashboard</div>
            </div>
            <a href="Admin.php" class="enlace-menu <?= ($current_page == 'Admin.php') ? 'activo' : '' ?>">
                <i class="fas fa-gauge-high"></i> Panel Principal
            </a>

            <div class="seccion-menu">
                <div class="titulo-seccion">Gestión Comercial</div>
            </div>
            <a href="Adminproductos.php" class="enlace-menu <?= ($current_page == 'Adminproductos.php') ? 'activo' : '' ?>">
                <i class="fas fa-box"></i> Productos
            </a>
            <a href="Admincategorias.php" class="enlace-menu <?= ($current_page == 'Admincategorias.php') ? 'activo' : '' ?>">
                <i class="fas fa-tags"></i> Categorías
            </a>
            <a href="Adminventas.php" class="enlace-menu <?= ($current_page == 'Adminventas.php') ? 'activo' : '' ?>">
                <i class="fas fa-shopping-cart"></i> Punto de Venta
            </a>
            <a href="Adminhistorialventas.php" class="enlace-menu <?= ($current_page == 'Adminhistorialventas.php') ? 'activo' : '' ?>">
                <i class="fas fa-receipt"></i> Historial de Ventas
            </a>
            <a href="Admininventario.php" class="enlace-menu <?= ($current_page == 'Admininventario.php') ? 'activo' : '' ?>">
                <i class="fas fa-warehouse"></i> Inventario
            </a>
            <a href="Adminalertas.php" class="enlace-menu <?= ($current_page == 'Adminalertas.php') ? 'activo' : '' ?>">
                <i class="fas fa-exclamation-triangle"></i> Alertas de Stock
                <span class="insignia-menu bg-danger">!</span>
            </a>
            <a href="Adminproveedores.php" class="enlace-menu <?= ($current_page == 'Adminproveedores.php') ? 'activo' : '' ?>">
                <i class="fas fa-truck"></i> Proveedores
            </a>

            <div class="seccion-menu">
                <div class="titulo-seccion">Administración</div>
            </div>
            <a href="Adminusuarios.php" class="enlace-menu <?= ($current_page == 'Adminusuarios.php') ? 'activo' : '' ?>">
                <i class="fas fa-users"></i> Usuarios
            </a>
            <a href="Adminreportes.php" class="enlace-menu <?= ($current_page == 'Adminreportes.php') ? 'activo' : '' ?>">
                <i class="fas fa-chart-bar"></i> Reportes
            </a>
            <a href="Adminconfiguracion.php" class="enlace-menu <?= ($current_page == 'Adminconfiguracion.php') ? 'activo' : '' ?>">
                <i class="fas fa-cog"></i> Configuración
            </a>

        <?php else: ?>
            <!-- SIDEBAR PARA EMPLEADO - ACCESO LIMITADO -->

            <div class="seccion-menu">
                <div class="titulo-seccion">Dashboard</div>
            </div>
            <a href="Empleado.php" class="enlace-menu <?= ($current_page == 'Empleado.php') ? 'activo' : '' ?>">
                <i class="fas fa-gauge-high"></i> Panel Principal
            </a>

            <div class="seccion-menu">
                <div class="titulo-seccion">Mis Operaciones</div>
            </div>
            <a href="ventas.php" class="enlace-menu <?= ($current_page == 'ventas.php') ? 'activo' : '' ?>">
                <i class="fas fa-shopping-cart"></i> Nueva Venta
            </a>
            <a href="productos.php" class="enlace-menu <?= ($current_page == 'productos.php') ? 'activo' : '' ?>">
                <i class="fas fa-search"></i> Consultar Productos
            </a>
            <a href="misventas.php" class="enlace-menu <?= ($current_page == 'misventas.php') ? 'activo' : '' ?>">
                <i class="fas fa-receipt"></i> Mis Ventas
            </a>


        <?php endif; ?>

        <div class="seccion-menu">
            <div class="titulo-seccion">Sesión</div>
        </div>
        <a href="<?= $base_url ?>controllers/AuthController.php?accion=logout" class="enlace-menu enlace-logout">
            <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
        </a>
    </nav>

    <div class="sidebar-footer">
        <i class="fas fa-shield-alt text-success me-1"></i> Sistema Seguro v1.0
    </div>
</aside>

<main class="contenido-principal">
    <!-- Barra Superior Blanca con Sombra -->
    <header class="cabecera p-3 bg-white border-bottom mb-4" style="box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
        <div class="d-flex justify-content-between align-items-center px-2">
            <div>
                <h1 class="titulo-pagina mb-1" style="font-size: 1.25rem; font-weight: 700; color: #0f172a;"><?= htmlspecialchars($titulo ?? 'Dashboard') ?></h1>
                <p class="mb-0 text-muted" style="font-size: 0.85rem;">Sistema de Gestión SIGI</p>
            </div>

            <div class="menu-usuario d-flex align-items-center gap-4">
                <!-- Notificaciones (Solo Admin) -->
                <?php if (strtolower($rol) === 'administrador'): ?>
                <a href="Adminalertas.php" class="position-relative cursor-pointer text-decoration-none" style="cursor: pointer; display: block;">
                    <div style="width: 36px; height: 36px; border-radius: 50%; background: #f1f5f9; display: flex; align-items: center; justify-content: center; color: #64748b; transition: all 0.2s;">
                        <i class="fas fa-bell"></i>
                    </div>
                    <?php if ($num_alertas_sidebar > 0): ?>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-white" style="font-size: 0.6rem; padding: 0.25rem 0.4rem;">
                        <?= $num_alertas_sidebar ?>
                        <span class="visually-hidden">Notificaciones no leídas</span>
                    </span>
                    <?php endif; ?>
                </a>

                <!-- Separador -->
                <div style="width: 1px; height: 30px; background-color: #e2e8f0;"></div>
                <?php endif; ?>

                <!-- Perfil -->
                <div class="d-flex align-items-center gap-3" style="cursor: pointer;">
                    <div class="datos-usuario text-end d-none d-sm-block">
                        <div class="nombre-usuario fw-bold" style="font-size: 0.9rem; color: #1e293b;"><?= htmlspecialchars($nombre ?? 'Usuario') ?></div>
                        <div class="rol-usuario text-muted" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px;"><?= htmlspecialchars($rol ?? 'Invitado') ?></div>
                    </div>
                    <div class="avatar-usuario shadow-sm" style="width: 40px; height: 40px; background: linear-gradient(135deg, #2563eb 0%, #1e3a8a 100%); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 1.1rem; border: 2px solid white;">
                        <?= strtoupper(substr($nombre ?? 'U', 0, 1)) ?>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Inicio del contenedor del contenido de la página -->
    <section class="px-4 pb-4">