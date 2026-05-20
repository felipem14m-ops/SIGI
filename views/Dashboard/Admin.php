<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
date_default_timezone_set('America/Bogota');

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    $_SESSION['alert'] = ['icon' => 'warning', 'title' => 'Acceso denegado', 'text' => 'Debe iniciar sesión'];
    header("Location: ../Usuario/login.php");
    exit;
}

$_rol_actual = strtolower($_SESSION['usuario']['rol'] ?? $_SESSION['rol'] ?? '');
if ($_rol_actual === 'empleado') {
    header("Location: Empleado.php");
    exit;
}

// =========================================================================
// DATOS DE SESIÓN
// =========================================================================
$usuario        = $_SESSION['usuario'] ?? [];
$nombre_admin   = $usuario['nombre'] ?? ($_SESSION['nombre'] ?? 'Administrador');

// Saludo dinámico
$hora = (int) date('H');
if ($hora >= 5 && $hora < 12) {
    $saludo = 'Buenos días';
    $emoji = '☀️';
} elseif ($hora >= 12 && $hora < 18) {
    $saludo = 'Buenas tardes';
    $emoji = '🌤️';
} else {
    $saludo = 'Buenas noches';
    $emoji = '🌙';
}

$hora_actual  = date('H:i');
$dias_es      = ['domingo', 'lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado'];
$meses_es     = [
    '',
    'enero',
    'febrero',
    'marzo',
    'abril',
    'mayo',
    'junio',
    'julio',
    'agosto',
    'septiembre',
    'octubre',
    'noviembre',
    'diciembre'
];
$fecha_actual = $dias_es[date('w')] . ', ' . date('d') . ' de ' . $meses_es[(int)date('n')] . ' de ' . date('Y');

// =========================================================================
// CARGA DE MODELOS
// =========================================================================
$stats         = [
    'total_productos' => 0,
    'ventas_hoy' => 0,
    'total_ingresos_hoy' => 0,
    'alertas' => 0,
    'total_usuarios' => 0,
    'usuarios_activos' => 0
];
$ventas_recientes = [];
$alertas_stock    = [];
$_error_bd        = '';

try {
    require_once __DIR__ . '/../../config/database.php';
    require_once __DIR__ . '/../../models/Usuario.php';
    require_once __DIR__ . '/../../models/Producto.php';
    require_once __DIR__ . '/../../models/Venta.php';

    $db           = (new Database())->conectar();
    $usuarioModel = new Usuario($db);
    $productoModel = new Producto($db);
    $ventaModel   = new Venta($db);

    $statsUsuarios  = $usuarioModel->obtenerEstadisticas();
    $statsProductos = $productoModel->obtenerEstadisticas();
    $resumenHoy     = $ventaModel->resumenDiario();
    $alertas_stock  = $productoModel->obtenerBajoStock();
    $ventas_recientes = $ventaModel->listarVentasHoy();

    $stats = [
        'total_productos'    => $statsProductos['total']   ?? 0,
        'ventas_hoy'         => $resumenHoy['total_ventas'] ?? 0,
        'total_ingresos_hoy' => $resumenHoy['total_ingresos'] ?? 0,
        'alertas'            => count($alertas_stock),
        'total_usuarios'     => $statsUsuarios['total']    ?? 0,
        'usuarios_activos'   => $statsUsuarios['activos']  ?? 0,
    ];

    $usuarioModel->actualizarUltimoAcceso($usuario['id_usuario'] ?? 0);
} catch (Throwable $e) {
    $_error_bd = 'No se pudieron cargar algunos datos del dashboard.';
    error_log("[SIGI] Admin.php Error: " . $e->getMessage());
}

$titulo = "Dashboard Administrador";
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidebar.php';
?>

<style>
    /* =====================================================
       DESIGN TOKENS
       ===================================================== */
    :root {
        --adm-primary: #2563eb;
        --adm-success: #10b981;
        --adm-warning: #f59e0b;
        --adm-danger: #ef4444;
        --adm-surface: #ffffff;
        --adm-border: #e2e8f0;
        --adm-text: #0f172a;
        --adm-muted: #64748b;
        --adm-bg: #f8fafc;
    }

    /* =====================================================
       WELCOME BANNER (igual que Empleado)
       ===================================================== */
    .welcome-banner {
        background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 55%, #2563eb 100%);
        border-radius: 20px;
        padding: 2rem 2.5rem;
        color: white;
        position: relative;
        overflow: hidden;
        margin-bottom: 1.5rem;
    }

    .welcome-banner::before {
        content: '';
        position: absolute;
        top: -50px;
        right: -50px;
        width: 220px;
        height: 220px;
        background: rgba(255, 255, 255, 0.06);
        border-radius: 50%;
    }

    .welcome-banner::after {
        content: '';
        position: absolute;
        bottom: -70px;
        right: 100px;
        width: 160px;
        height: 160px;
        background: rgba(255, 255, 255, 0.04);
        border-radius: 50%;
    }

    .welcome-banner .banner-content {
        position: relative;
        z-index: 1;
    }

    .welcome-badge {
        background: rgba(255, 255, 255, 0.15);
        border: 1px solid rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
        border-radius: 50px;
        padding: 0.3rem 1rem;
        font-size: 0.8rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.75rem;
    }

    .welcome-time {
        font-size: 2rem;
        font-weight: 800;
        margin-bottom: 0.25rem;
    }

    .welcome-date {
        font-size: 0.9rem;
        opacity: 0.75;
    }

    /* =====================================================
       METRIC CARDS
       ===================================================== */
    .metric-card {
        background: var(--adm-surface);
        border-radius: 16px;
        border: 1px solid var(--adm-border);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.04);
        padding: 1.5rem;
        height: 100%;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .metric-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 28px rgba(0, 0, 0, 0.08);
    }

    .metric-icon {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        margin-bottom: 1rem;
    }

    .icon-blue {
        background: #eff6ff;
        color: #2563eb;
    }

    .icon-green {
        background: #ecfdf5;
        color: #10b981;
    }

    .icon-amber {
        background: #fffbeb;
        color: #f59e0b;
    }

    .icon-red {
        background: #fef2f2;
        color: #ef4444;
    }

    .icon-purple {
        background: #f5f3ff;
        color: #7c3aed;
    }

    .metric-val {
        font-size: 1.9rem;
        font-weight: 800;
        color: var(--adm-text);
        line-height: 1;
        margin-bottom: 0.25rem;
    }

    .metric-lbl {
        font-size: 0.78rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--adm-muted);
    }

    .metric-sub {
        font-size: 0.8rem;
        color: var(--adm-muted);
        margin-top: 0.5rem;
    }

    /* =====================================================
       PANELS
       ===================================================== */
    .panel-card {
        background: var(--adm-surface);
        border-radius: 16px;
        border: 1px solid var(--adm-border);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.04);
    }

    .panel-head {
        padding: 1.1rem 1.5rem;
        border-bottom: 1px solid var(--adm-border);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .panel-title {
        font-weight: 700;
        font-size: 0.95rem;
        color: var(--adm-text);
    }

    /* =====================================================
       TABLA VENTAS RECIENTES
       ===================================================== */
    .tbl-adm th {
        font-size: 0.72rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--adm-muted);
        font-weight: 700;
        border-bottom: 2px solid var(--adm-border);
        background: var(--adm-bg);
        padding: 0.85rem 1rem;
    }

    .tbl-adm td {
        padding: 0.8rem 1rem;
        vertical-align: middle;
        font-size: 0.875rem;
        color: #334155;
        border-bottom: 1px solid #f1f5f9;
    }

    .badge-pago {
        padding: 0.3rem 0.7rem;
        border-radius: 50px;
        font-size: 0.72rem;
        font-weight: 700;
    }

    .pago-efectivo {
        background: #dcfce7;
        color: #15803d;
    }

    .pago-transferencia {
        background: #dbeafe;
        color: #1d4ed8;
    }

    .pago-tarjeta {
        background: #f5f3ff;
        color: #6d28d9;
    }

    .pago-otro {
        background: #f1f5f9;
        color: #475569;
    }

    /* =====================================================
       ACCESOS RÁPIDOS (Bento)
       ===================================================== */
    .quick-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 0.85rem;
    }

    .quick-tile {
        background: var(--adm-surface);
        border-radius: 14px;
        border: 1px solid var(--adm-border);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.04);
        padding: 1.1rem;
        text-align: center;
        text-decoration: none;
        color: inherit;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.6rem;
        transition: all 0.2s;
    }

    .quick-tile:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
        color: inherit;
    }

    .quick-tile-icon {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
    }

    .quick-tile span {
        font-size: 0.8rem;
        font-weight: 700;
        color: var(--adm-text);
    }

    /* =====================================================
       ALERTAS STOCK
       ===================================================== */
    .alert-strip {
        background: #fef3c7;
        border: 1px solid #fde68a;
        border-radius: 12px;
        padding: 0.85rem 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-size: 0.875rem;
        font-weight: 600;
        color: #92400e;
    }

    /* =====================================================
       STOCK CRÍTICO LIST
       ===================================================== */
    .stock-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.7rem 1.25rem;
        border-bottom: 1px solid #f1f5f9;
    }

    .stock-item:last-child {
        border-bottom: none;
    }
</style>

<div class="container-fluid py-2">

    <!-- =====================================================
         WELCOME BANNER
         ===================================================== -->
    <div class="welcome-banner mb-4">
        <div class="banner-content">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                    <span class="welcome-badge">
                        <i class="fas fa-shield-alt"></i> Administrador · SIGI
                    </span>
                    <div class="welcome-time"><?= $emoji ?> <?= $saludo ?>, <?= htmlspecialchars(explode(' ', $nombre_admin)[0]) ?>!</div>
                    <div class="welcome-date"><?= ucfirst($fecha_actual) ?> &bull; <span id="reloj-adm"><?= $hora_actual ?></span></div>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="Adminventas.php" class="btn fw-bold px-4 py-2 rounded-3"
                        style="background:white; color:#2563eb; font-size:.9rem; box-shadow:0 4px 14px rgba(0,0,0,.15);">
                        <i class="fas fa-shopping-cart me-2"></i> Ver Ventas
                    </a>
                    <a href="Adminusuarios.php" class="btn fw-bold px-4 py-2 rounded-3"
                        style="background:rgba(255,255,255,.15); color:white; border:1px solid rgba(255,255,255,.3); font-size:.9rem;">
                        <i class="fas fa-users me-2"></i> Usuarios
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Alerta stock bajo -->
    <?php if ($stats['alertas'] > 0): ?>
        <div class="alert-strip mb-4">
            <i class="fas fa-exclamation-triangle fs-5"></i>
            <span>Hay <strong><?= $stats['alertas'] ?></strong> producto(s) con stock bajo.
                <a href="Adminalertas.php" class="text-warning-emphasis fw-bold ms-1">Ver alertas →</a>
            </span>
        </div>
    <?php endif; ?>

    <?php if ($_error_bd): ?>
        <div class="alert alert-warning rounded-3 mb-4"><i class="fas fa-exclamation-triangle me-2"></i><?= htmlspecialchars($_error_bd) ?></div>
    <?php endif; ?>

    <!-- =====================================================
         MÉTRICAS PRINCIPALES
         ===================================================== -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-4 col-xl-2">
            <div class="metric-card">
                <div class="metric-icon icon-blue"><i class="fas fa-box"></i></div>
                <div class="metric-val"><?= number_format($stats['total_productos']) ?></div>
                <div class="metric-lbl">Productos</div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="metric-card">
                <div class="metric-icon icon-green"><i class="fas fa-shopping-bag"></i></div>
                <div class="metric-val"><?= $stats['ventas_hoy'] ?></div>
                <div class="metric-lbl">Ventas Hoy</div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="metric-card">
                <div class="metric-icon icon-green"><i class="fas fa-dollar-sign"></i></div>
                <div class="metric-val" style="font-size:1.4rem;">$<?= number_format($stats['total_ingresos_hoy'], 0, ',', '.') ?></div>
                <div class="metric-lbl">Ingresos Hoy</div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="metric-card">
                <div class="metric-icon icon-amber"><i class="fas fa-bell"></i></div>
                <div class="metric-val"><?= $stats['alertas'] ?></div>
                <div class="metric-lbl">Alertas Stock</div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="metric-card">
                <div class="metric-icon icon-purple"><i class="fas fa-users"></i></div>
                <div class="metric-val"><?= $stats['usuarios_activos'] ?><span style="font-size:1rem;color:#94a3b8;"> /<?= $stats['total_usuarios'] ?></span></div>
                <div class="metric-lbl">Usuarios</div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="metric-card">
                <div class="metric-icon icon-red"><i class="fas fa-clock"></i></div>
                <div class="metric-val" style="font-size:1.5rem;" id="reloj-metric"><?= $hora_actual ?></div>
                <div class="metric-lbl">Hora Actual</div>
            </div>
        </div>
    </div>

    <!-- =====================================================
         FILA PRINCIPAL: Ventas recientes + Accesos + Stock
         ===================================================== -->
    <div class="row g-4">

        <!-- Ventas recientes -->
        <div class="col-12 col-lg-7">
            <div class="panel-card">
                <div class="panel-head">
                    <span class="panel-title"><i class="fas fa-list-alt me-2 text-primary"></i>Ventas de Hoy</span>
                    <a href="Adminventas.php" class="btn btn-sm rounded-3 fw-bold"
                        style="background:#eff6ff; color:#2563eb; border:none; font-size:.82rem;">
                        Ver todas →
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover tbl-adm mb-0 w-100">
                        <thead>
                            <tr>
                                <th>Ticket</th>
                                <th>Hora</th>
                                <th class="text-center">Ítems</th>
                                <th>Pago</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($ventas_recientes)): ?>
                                <?php foreach (array_slice($ventas_recientes, 0, 8) as $v): ?>
                                    <tr>
                                        <td class="fw-bold text-primary">#<?= str_pad($v['id_venta'], 4, '0', STR_PAD_LEFT) ?></td>
                                        <td class="text-muted"><?= date('h:i A', strtotime($v['fecha_venta'])) ?></td>
                                        <td class="text-center">
                                            <span class="badge rounded-pill bg-light text-dark border"><?= $v['total_items'] ?? '-' ?> und</span>
                                        </td>
                                        <td>
                                            <?php
                                            $mp  = strtolower($v['metodo_pago'] ?? 'otro');
                                            $cls = match ($mp) {
                                                'efectivo'      => 'pago-efectivo',
                                                'transferencia' => 'pago-transferencia',
                                                'tarjeta'       => 'pago-tarjeta',
                                                default         => 'pago-otro'
                                            };
                                            ?>
                                            <span class="badge-pago <?= $cls ?>"><?= ucfirst($mp) ?></span>
                                        </td>
                                        <td class="text-end fw-bold text-success">
                                            $<?= number_format($v['total'], 0, ',', '.') ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        <i class="fas fa-receipt fs-2 mb-2 d-block opacity-25"></i>
                                        Aún no hay ventas registradas hoy
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Columna derecha -->
        <div class="col-12 col-lg-5">

            <!-- Accesos rápidos -->
            <div class="panel-card mb-4">
                <div class="panel-head">
                    <span class="panel-title"><i class="fas fa-bolt me-2 text-warning"></i>Acciones Rápidas</span>
                </div>
                <div class="p-3">
                    <div class="quick-grid">
                        <a href="Adminproductos.php" class="quick-tile">
                            <div class="quick-tile-icon" style="background:#eff6ff; color:#2563eb;"><i class="fas fa-box"></i></div>
                            <span>Productos</span>
                        </a>
                        <a href="Adminventas.php" class="quick-tile">
                            <div class="quick-tile-icon" style="background:#ecfdf5; color:#10b981;"><i class="fas fa-shopping-cart"></i></div>
                            <span>Ventas</span>
                        </a>
                        <a href="Admininventario.php" class="quick-tile">
                            <div class="quick-tile-icon" style="background:#f5f3ff; color:#7c3aed;"><i class="fas fa-warehouse"></i></div>
                            <span>Inventario</span>
                        </a>
                        <a href="Adminusuarios.php" class="quick-tile">
                            <div class="quick-tile-icon" style="background:#fef3c7; color:#d97706;"><i class="fas fa-users"></i></div>
                            <span>Usuarios</span>
                        </a>
                        <a href="Adminproveedores.php" class="quick-tile">
                            <div class="quick-tile-icon" style="background:#fef2f2; color:#ef4444;"><i class="fas fa-truck"></i></div>
                            <span>Proveedores</span>
                        </a>
                        <a href="Adminreportes.php" class="quick-tile">
                            <div class="quick-tile-icon" style="background:#f0fdf4; color:#16a34a;"><i class="fas fa-chart-bar"></i></div>
                            <span>Reportes</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Stock crítico -->
            <div class="panel-card">
                <div class="panel-head">
                    <span class="panel-title"><i class="fas fa-exclamation-circle me-2 text-danger"></i>Stock Crítico</span>
                    <a href="Adminalertas.php" class="text-primary" style="font-size:.82rem; font-weight:700;">Ver todas →</a>
                </div>
                <?php if (!empty($alertas_stock)): ?>
                    <?php foreach (array_slice($alertas_stock, 0, 5) as $prod): ?>
                        <div class="stock-item">
                            <div class="d-flex align-items-center gap-2">
                                <div style="width:36px;height:36px;border-radius:10px;background:<?= $prod['stock_actual'] <= 0 ? '#fee2e2' : '#fffbeb' ?>;display:flex;align-items:center;justify-content:center;color:<?= $prod['stock_actual'] <= 0 ? '#dc2626' : '#d97706' ?>;font-size:.9rem;flex-shrink:0;">
                                    <i class="fas <?= $prod['stock_actual'] <= 0 ? 'fa-times-circle' : 'fa-exclamation' ?>"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark" style="font-size:.85rem;"><?= htmlspecialchars($prod['nombre']) ?></div>
                                    <div class="text-muted" style="font-size:.75rem;"><?= htmlspecialchars($prod['nombre_categoria'] ?? 'Sin cat.') ?></div>
                                </div>
                            </div>
                            <span style="font-size:.75rem;font-weight:700;padding:.25rem .6rem;border-radius:50px;background:<?= $prod['stock_actual'] <= 0 ? '#fee2e2' : '#fffbeb' ?>;color:<?= $prod['stock_actual'] <= 0 ? '#dc2626' : '#b45309' ?>;">
                                <?= $prod['stock_actual'] ?> und
                            </span>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center py-4 text-muted">
                        <i class="fas fa-check-circle fs-2 mb-2 d-block text-success opacity-50"></i>
                        <small class="fw-bold">¡Todo en orden!</small><br>
                        <small>No hay alertas de stock</small>
                    </div>
                <?php endif; ?>
            </div>

        </div>
    </div>
</div>

<?php
echo '</section></main></div>';
require_once __DIR__ . '/../layouts/footer.php';
?>

<script>
    // Reloj en tiempo real
    function actualizarReloj() {
        const ahora = new Date();
        const hh = String(ahora.getHours()).padStart(2, '0');
        const mm = String(ahora.getMinutes()).padStart(2, '0');
        const tiempo = hh + ':' + mm;
        const r1 = document.getElementById('reloj-adm');
        const r2 = document.getElementById('reloj-metric');
        if (r1) r1.textContent = tiempo;
        if (r2) r2.textContent = tiempo;
    }
    setInterval(actualizarReloj, 1000);
</script>