<?php
// ============================================================================
// Dashboard: Empleado
// Sistema: SIGI - Gestión de Inventario
// 
// Panel principal para el empleado de caja de la tienda de barrio.
// Muestra métricas de su turno actual, accesos rápidos al POS,
// resumen de ventas del día y alertas de stock relevantes.
// ============================================================================
session_start();
date_default_timezone_set('America/Bogota');

// Verificación de autenticación
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    $_SESSION['alert'] = ['icon' => 'warning', 'title' => 'Acceso denegado', 'text' => 'Debe iniciar sesión'];
    header("Location: ../Usuario/login.php");
    exit;
}

// Verificación de rol empleado
$rol = strtolower($_SESSION['usuario']['rol'] ?? $_SESSION['rol'] ?? '');
if (!in_array($rol, ['empleado', 'administrador'])) {
    header("Location: ../Usuario/login.php");
    exit;
}

// Conexión a modelos (Defensivo: sidebar siempre se renderiza)
$resumen_hoy = ['total_ventas' => 0, 'total_ingresos' => 0];
$ventas_hoy = [];
$alertas_stock = [];
$_error_bd = '';

try {
    require_once __DIR__ . '/../../config/database.php';
    require_once __DIR__ . '/../../models/Venta.php';
    require_once __DIR__ . '/../../models/Producto.php';
    $db = (new Database())->conectar();
    $id_usuario    = $_SESSION['usuario']['id_usuario'] ?? 0;
    $resumen_hoy   = (new Venta($db))->resumenDiario($id_usuario);
    $ventas_hoy    = (new Venta($db))->listarVentasHoy($id_usuario);
    $alertas_stock = (new Producto($db))->obtenerBajoStock();
} catch (Throwable $e) {
    $_error_bd = 'No se pudieron cargar los datos. Verifique la base de datos.';
    error_log("[SIGI] Empleado.php Error: " . $e->getMessage());
}

// Datos del empleado logueado
$usuario = $_SESSION['usuario'] ?? [];
$nombre_empleado = $usuario['nombre'] ?? 'Empleado';
$hora_actual = date('H:i');
// Fecha en español sin depender de strftime (compatible PHP 8.x)
$dias_es   = ['domingo','lunes','martes','miércoles','jueves','viernes','sábado'];
$meses_es  = ['','enero','febrero','marzo','abril','mayo','junio',
               'julio','agosto','septiembre','octubre','noviembre','diciembre'];
$fecha_actual = $dias_es[date('w')] . ', ' . date('d') . ' de ' . $meses_es[(int)date('n')] . ' de ' . date('Y');

// Saludo dinámico según la hora
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

$titulo = "Mi Panel - " . $nombre_empleado;

require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidebar.php';
?>

<style>
    /* =====================================================
       DESIGN TOKENS - Empleado Dashboard
       ===================================================== */
    :root {
        --emp-primary: #2563eb;
        --emp-success: #10b981;
        --emp-warning: #f59e0b;
        --emp-danger: #ef4444;
        --emp-surface: #ffffff;
        --emp-border: #e2e8f0;
        --emp-text: #0f172a;
        --emp-muted: #64748b;
        --emp-bg: #f8fafc;
    }

    /* =====================================================
       WELCOME BANNER
       ===================================================== */
    .welcome-banner {
        background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 60%, #0ea5e9 100%);
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
        top: -40px; right: -40px;
        width: 200px; height: 200px;
        background: rgba(255,255,255,0.07);
        border-radius: 50%;
    }
    .welcome-banner::after {
        content: '';
        position: absolute;
        bottom: -60px; right: 80px;
        width: 150px; height: 150px;
        background: rgba(255,255,255,0.05);
        border-radius: 50%;
    }
    .welcome-banner .banner-content { position: relative; z-index: 1; }
    .welcome-badge { background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.2); backdrop-filter: blur(10px); border-radius: 50px; padding: 0.3rem 1rem; font-size: 0.8rem; font-weight: 600; display: inline-flex; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem; }
    .welcome-time { font-size: 2rem; font-weight: 800; margin-bottom: 0.25rem; }
    .welcome-date { font-size: 0.9rem; opacity: 0.75; }

    .pos-btn {
        background: white; color: #2563eb; font-weight: 700; font-size: 1rem;
        padding: 0.85rem 2rem; border-radius: 14px; border: none;
        display: inline-flex; align-items: center; gap: 0.75rem;
        box-shadow: 0 4px 14px rgba(0,0,0,0.15);
        transition: all 0.2s; text-decoration: none;
    }
    .pos-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0,0,0,0.2); color: #1d4ed8; }

    /* =====================================================
       METRIC CARDS (Turno)
       ===================================================== */
    .metric-card-emp {
        background: var(--emp-surface);
        border-radius: 16px;
        border: 1px solid var(--emp-border);
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.04);
        padding: 1.5rem;
        height: 100%;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .metric-card-emp:hover { transform: translateY(-4px); box-shadow: 0 10px 25px rgba(0,0,0,0.07); }

    .metric-icon-emp {
        width: 52px; height: 52px; border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.4rem; margin-bottom: 1rem;
    }
    .icon-emp-blue { background: #eff6ff; color: #2563eb; }
    .icon-emp-green { background: #ecfdf5; color: #10b981; }
    .icon-emp-amber { background: #fffbeb; color: #f59e0b; }
    .icon-emp-red { background: #fef2f2; color: #ef4444; }

    .metric-val-emp {
        font-size: 1.9rem; font-weight: 800; color: var(--emp-text); line-height: 1;
        margin-bottom: 0.25rem;
    }
    .metric-lbl-emp { font-size: 0.8rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: var(--emp-muted); }

    /* =====================================================
       ACCESOS RÁPIDOS (Bento Grid)
       ===================================================== */
    .quick-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; }

    .quick-tile {
        background: var(--emp-surface); border-radius: 16px;
        border: 1px solid var(--emp-border);
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.04);
        padding: 1.25rem; text-align: center;
        text-decoration: none; color: inherit;
        display: flex; flex-direction: column; align-items: center; gap: 0.75rem;
        transition: all 0.2s;
    }
    .quick-tile:hover { transform: translateY(-4px); box-shadow: 0 10px 25px rgba(0,0,0,0.07); color: inherit; }
    .quick-tile-icon { width: 54px; height: 54px; border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; }
    .quick-tile span { font-size: 0.85rem; font-weight: 700; color: var(--emp-text); }

    /* =====================================================
       TABLA DE VENTAS DEL TURNO
       ===================================================== */
    .module-card { background: var(--emp-surface); border-radius: 16px; border: 1px solid var(--emp-border); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.04); }
    .panel-header { padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--emp-border); display: flex; justify-content: space-between; align-items: center; }
    .panel-title-emp { font-weight: 700; font-size: 0.95rem; color: var(--emp-text); }
    
    .table-emp th { font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.5px; color: var(--emp-muted); font-weight: 700; border-bottom: 2px solid var(--emp-border); background: #f8fafc; padding: 0.9rem 1rem; }
    .table-emp td { padding: 0.85rem 1rem; vertical-align: middle; font-size: 0.875rem; color: #334155; border-bottom: 1px solid #f1f5f9; }

    .badge-pago { padding: 0.3rem 0.7rem; border-radius: 50px; font-size: 0.72rem; font-weight: 700; }
    .pago-efectivo { background: #dcfce7; color: #15803d; }
    .pago-transferencia { background: #dbeafe; color: #1d4ed8; }
    .pago-tarjeta { background: #f5f3ff; color: #6d28d9; }
    .pago-otro { background: #f1f5f9; color: #475569; }

    /* =====================================================
       ALERT STRIP
       ===================================================== */
    .alert-strip { background: #fef3c7; border: 1px solid #fde68a; border-radius: 12px; padding: 0.85rem 1.25rem; display: flex; align-items: center; gap: 0.75rem; font-size: 0.875rem; font-weight: 600; color: #92400e; }

    /* =====================================================
       DATATABLE OVERRIDES
       ===================================================== */
    .dataTables_wrapper .dataTables_filter input { border-radius: 10px; border: 1px solid var(--emp-border); padding: 0.45rem 0.9rem; font-size: 0.85rem; background: #f8fafc; }
    .dataTables_wrapper .dataTables_filter input:focus { border-color: var(--emp-primary); box-shadow: 0 0 0 3px rgba(37,99,235,0.1); outline: none; }
    .dataTables_wrapper .dataTables_length select { border-radius: 8px; border: 1px solid var(--emp-border); }
    .dataTables_wrapper .dataTables_info { font-size: 0.82rem; color: var(--emp-muted); }
    .dataTables_wrapper .dataTables_paginate .paginate_button { border-radius: 8px !important; border: none !important; margin: 0 2px; font-size: 0.82rem; }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current { background: var(--emp-primary) !important; color: white !important; border: none !important; }
    .dataTables_wrapper .dataTables_paginate .paginate_button:hover { background: #eff6ff !important; color: var(--emp-primary) !important; border: none !important; }
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
                        <i class="fas fa-store"></i> Tienda SIGI
                    </span>
                    <div class="welcome-time"><?= $emoji ?> <?= $saludo ?>, <?= htmlspecialchars(explode(' ', $nombre_empleado)[0]) ?>!</div>
                    <div class="welcome-date"><?= ucfirst($fecha_actual) ?> &bull; <?= $hora_actual ?></div>
                </div>
                <a href="ventas.php" class="pos-btn">
                    <i class="fas fa-cash-register"></i>
                    Abrir Caja / POS
                </a>
            </div>
        </div>
    </div>

    <!-- No alertas banner -->

    <div class="row g-4">

        <!-- =====================================================
             COLUMNA PRINCIPAL: Métricas, Accesos y Tabla
             ===================================================== -->
        <div class="col-12">

            <!-- Métricas del Turno -->
            <div class="row g-3 mb-4">
                <div class="col-6 col-md-3">
                    <div class="metric-card-emp">
                        <div class="metric-icon-emp icon-emp-green"><i class="fas fa-shopping-bag"></i></div>
                        <div class="metric-val-emp"><?= $resumen_hoy['total_ventas'] ?? 0 ?></div>
                        <div class="metric-lbl-emp">Ventas Hoy</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="metric-card-emp">
                        <div class="metric-icon-emp icon-emp-blue"><i class="fas fa-dollar-sign"></i></div>
                        <div class="metric-val-emp" style="font-size: 1.5rem;">
                            $<?= number_format($resumen_hoy['total_ingresos'] ?? 0, 0, ',', '.') ?>
                        </div>
                        <div class="metric-lbl-emp">Ingresos Hoy</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="metric-card-emp">
                        <div class="metric-icon-emp icon-emp-amber"><i class="fas fa-chart-line"></i></div>
                        <div class="metric-val-emp" style="font-size: 1.4rem;">
                            $<?= $resumen_hoy['total_ventas'] > 0 ? number_format($resumen_hoy['total_ingresos'] / $resumen_hoy['total_ventas'], 0, ',', '.') : '0' ?>
                        </div>
                        <div class="metric-lbl-emp">Ticket Promedio</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="metric-card-emp">
                        <div class="metric-icon-emp icon-emp-red"><i class="fas fa-clock"></i></div>
                        <div class="metric-val-emp" style="font-size: 1.4rem;" id="reloj-turno"><?= $hora_actual ?></div>
                        <div class="metric-lbl-emp">Hora Actual</div>
                    </div>
                </div>
            </div>

            <!-- Accesos Rápidos - Bento Grid -->
            <div class="row g-3 mb-4">
                <div class="col-12 col-md-6">
                    <a href="ventas.php" class="quick-tile" style="border-color: #bfdbfe; background: #fff; text-decoration: none;">
                        <div class="quick-tile-icon" style="background: #eff6ff; color: #2563eb;">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <span style="font-size: 0.9rem;">Registrar Nueva Venta</span>
                    </a>
                </div>
                <div class="col-12 col-md-6">
                    <a href="productos.php" class="quick-tile" style="background: #fff; text-decoration: none;">
                        <div class="quick-tile-icon" style="background: #f5f3ff; color: #7c3aed;">
                            <i class="fas fa-search"></i>
                        </div>
                        <span style="font-size: 0.9rem;">Consultar Catálogo de Productos</span>
                    </a>
                </div>
            </div>

            <!-- Tabla de Ventas del Día -->
            <div class="module-card">
                <div class="panel-header">
                    <span class="panel-title-emp"><i class="fas fa-list-alt me-2 text-primary"></i> Mis Ventas de Hoy</span>
                    <a href="misventas.php" class="btn btn-sm rounded-3 fw-bold" style="background: #eff6ff; color: #2563eb; border: none; font-size: 0.82rem;">
                        <i class="fas fa-history me-1"></i> Ver Historial Completo
                    </a>
                </div>
                <div class="p-3">
                    <table id="tablaVentasEmpleado" class="table table-hover table-emp mb-0 w-100">
                        <thead>
                            <tr>
                                <th>Ticket</th>
                                <th>Hora</th>
                                <th>Ítems</th>
                                <th>Pago</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($ventas_hoy)): ?>
                                <?php foreach ($ventas_hoy as $venta): ?>
                                <tr>
                                    <td>
                                        <div class="fw-bold text-dark">#<?= str_pad($venta['id_venta'], 4, '0', STR_PAD_LEFT) ?></div>
                                    </td>
                                    <td class="text-muted"><?= date('h:i A', strtotime($venta['fecha_venta'])) ?></td>
                                    <td>
                                        <span class="badge rounded-pill bg-light text-dark border">
                                            <?= $venta['total_items'] ?? '-' ?> und
                                        </span>
                                    </td>
                                    <td>
                                        <?php
                                        $metodo = strtolower($venta['metodo_pago'] ?? 'otro');
                                        $clases_pago = [
                                            'efectivo' => 'pago-efectivo',
                                            'transferencia' => 'pago-transferencia',
                                            'tarjeta' => 'pago-tarjeta'
                                        ];
                                        $clase_pago = $clases_pago[$metodo] ?? 'pago-otro';
                                        ?>
                                        <span class="badge-pago <?= $clase_pago ?>">
                                            <?= ucfirst($metodo) ?>
                                        </span>
                                    </td>
                                    <td class="text-end fw-bold text-success">
                                        $<?= number_format($venta['total'], 0, ',', '.') ?>
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

        </div><!-- /col-12 -->
    </div><!-- /row -->
</div>

<?php
echo '</section></main></div>';
require_once __DIR__ . '/../layouts/footer.php';
?>

<script>
// =====================================================
// RELOJ EN TIEMPO REAL
// =====================================================
function actualizarReloj() {
    const ahora = new Date();
    const horas = String(ahora.getHours()).padStart(2, '0');
    const minutos = String(ahora.getMinutes()).padStart(2, '0');
    const reloj = document.getElementById('reloj-turno');
    if (reloj) reloj.textContent = horas + ':' + minutos;
}
setInterval(actualizarReloj, 1000);

// =====================================================
// DATATABLE VENTAS DEL TURNO
// =====================================================
$(document).ready(function() {
    <?php if (!empty($ventas_hoy)): ?>
    $('#tablaVentasEmpleado').DataTable({
        order: [[1, 'desc']],
        pageLength: 20,
        dom: 'rt<"px-4 py-3 d-flex justify-content-between align-items-center"ip>',
        language: {
            info: "_TOTAL_ ventas registradas",
            paginate: { previous: "Ant.", next: "Sig." },
            zeroRecords: "No hay ventas registradas hoy"
        }
    });
    <?php endif; ?>
});
</script>