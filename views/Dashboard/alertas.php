<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) { header("Location: Usuario/login.php"); exit; }

$titulo = "Alertas de Stock";

// =========================================================================
// CONEXIÓN A MODELOS (Defensivo: sidebar siempre se renderiza)
// =========================================================================
$productos_alerta = [];
$_error_bd = '';

try {
    require_once __DIR__ . '/../../config/database.php';
    require_once __DIR__ . '/../../models/Producto.php';
    $db = (new Database())->conectar();
    $productos_alerta = (new Producto($db))->obtenerBajoStock();
} catch (Throwable $e) {
    $_error_bd = 'No se pudieron cargar los datos. Verifique la base de datos.';
    error_log("[SIGI] alertas.php Error: " . $e->getMessage());
}

// Contadores para tarjetas resumen
$agotados = 0;
$bajo_stock = 0;
foreach ($productos_alerta as $p) {
    if ($p['stock_actual'] <= 0) { $agotados++; }
    else { $bajo_stock++; }
}

require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidebar.php';
?>

<style>
    .module-card { background: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); }
    .table-custom th { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b; font-weight: 700; border-bottom: 2px solid #e2e8f0; background: #f8fafc; padding: 1rem; }
    .table-custom td { padding: 0.85rem 1rem; vertical-align: middle; font-size: 0.9rem; color: #334155; border-bottom: 1px solid #f1f5f9; }

    .badge-alert { padding: 0.4rem 0.8rem; border-radius: 50px; font-size: 0.75rem; font-weight: 600; display: inline-flex; align-items: center; gap: 0.25rem; }
    .bg-soft-danger { background: #fee2e2; color: #dc2626; }
    .bg-soft-warning { background: #fffbeb; color: #b45309; }
    .bg-soft-info { background: #eff6ff; color: #2563eb; }

    .btn-action { padding: 0.5rem 1rem; border-radius: 8px; border: none; font-weight: 600; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 0.5rem; transition: all 0.2s; }
    .btn-reabastecer { background: #eff6ff; color: #2563eb; }
    .btn-reabastecer:hover { background: #dbeafe; }

    .alert-summary-card { background: #ffffff; padding: 1.5rem; border-radius: 16px; border: 1px solid #e2e8f0; display: flex; align-items: center; gap: 1.25rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); height: 100%; transition: transform 0.2s, box-shadow 0.2s; }
    .alert-summary-card:hover { transform: translateY(-5px); box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
    .alert-summary-icon { width: 56px; height: 56px; border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; }
    .bg-icon-danger { background: #fee2e2; color: #dc2626; }
    .bg-icon-warning { background: #fffbeb; color: #d97706; }
    .bg-icon-info { background: #eff6ff; color: #2563eb; }

    /* DataTables */
    .dataTables_wrapper .dataTables_filter input { border-radius: 10px; border: 1px solid #e2e8f0; padding: 0.5rem 1rem; background: #f8fafc; }
    .dataTables_wrapper .dataTables_filter input:focus { border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37,99,235,0.1); outline: none; }
    .dataTables_wrapper .dataTables_length select { border-radius: 8px; border: 1px solid #e2e8f0; }
    .dataTables_wrapper .dataTables_info { font-size: 0.85rem; color: #64748b; }
    .dataTables_wrapper .dataTables_paginate .paginate_button { border-radius: 8px !important; border: none !important; margin: 0 2px; }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current { background: #2563eb !important; color: white !important; border: none !important; }
    .dataTables_wrapper .dataTables_paginate .paginate_button:hover { background: #eff6ff !important; color: #2563eb !important; border: none !important; }
</style>

<div class="container-fluid py-2">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h2 class="h4 mb-1 fw-bold text-dark">Alertas de Stock</h2>
            <p class="text-muted mb-0" style="font-size: 0.9rem;">Monitorea los productos que requieren tu atención inmediata</p>
        </div>
        <button class="btn btn-primary rounded-3 shadow-sm px-3" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); border: none; font-weight: 500;">
            <i class="fas fa-truck-loading me-2"></i> Orden de Compra Múltiple
        </button>
    </div>

    <!-- Tarjetas Resumen -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-4">
            <div class="alert-summary-card">
                <div class="alert-summary-icon bg-icon-danger"><i class="fas fa-exclamation-circle"></i></div>
                <div>
                    <h3 class="fw-bold mb-0 text-dark"><?= $agotados ?></h3>
                    <p class="text-muted mb-0 small fw-semibold text-uppercase" style="letter-spacing: 0.5px;">Agotados</p>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="alert-summary-card">
                <div class="alert-summary-icon bg-icon-warning"><i class="fas fa-exclamation-triangle"></i></div>
                <div>
                    <h3 class="fw-bold mb-0 text-dark"><?= $bajo_stock ?></h3>
                    <p class="text-muted mb-0 small fw-semibold text-uppercase" style="letter-spacing: 0.5px;">Bajo Stock</p>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="alert-summary-card">
                <div class="alert-summary-icon bg-icon-info"><i class="fas fa-clipboard-list"></i></div>
                <div>
                    <h3 class="fw-bold mb-0 text-dark"><?= count($productos_alerta) ?></h3>
                    <p class="text-muted mb-0 small fw-semibold text-uppercase" style="letter-spacing: 0.5px;">Total Alertas</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Alertas -->
    <div class="module-card overflow-hidden">
        <div class="table-responsive p-3">
            <table id="tablaAlertas" class="table table-hover table-custom mb-0 w-100">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Categoría</th>
                        <th>Proveedor</th>
                        <th>Nivel de Alerta</th>
                        <th class="text-center">Stock Mín.</th>
                        <th class="text-center">Stock Actual</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($productos_alerta)): ?>
                        <?php foreach ($productos_alerta as $prod): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <?php if ($prod['stock_actual'] <= 0): ?>
                                        <div style="width: 40px; height: 40px; background: #fee2e2; border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #dc2626;">
                                            <i class="fas fa-times-circle"></i>
                                        </div>
                                    <?php else: ?>
                                        <div style="width: 40px; height: 40px; background: #fffbeb; border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #d97706;">
                                            <i class="fas fa-arrow-down"></i>
                                        </div>
                                    <?php endif; ?>
                                    <div>
                                        <div class="fw-bold text-dark"><?= htmlspecialchars($prod['nombre']) ?></div>
                                        <div class="text-muted" style="font-size: 0.8rem;">#PROD-<?= str_pad($prod['id_producto'], 3, '0', STR_PAD_LEFT) ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-muted"><?= htmlspecialchars($prod['nombre_categoria'] ?? '-') ?></td>
                            <td class="text-muted"><?= htmlspecialchars($prod['nombre_proveedor'] ?? '-') ?></td>
                            <td>
                                <?php if ($prod['stock_actual'] <= 0): ?>
                                    <span class="badge-alert bg-soft-danger"><i class="fas fa-fire"></i> Crítico - Agotado</span>
                                <?php else: ?>
                                    <span class="badge-alert bg-soft-warning"><i class="fas fa-exclamation-triangle"></i> Bajo Mínimo</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center text-muted"><?= $prod['stock_minimo'] ?> und</td>
                            <td class="text-center">
                                <?php if ($prod['stock_actual'] <= 0): ?>
                                    <span class="badge bg-soft-danger px-3 py-2 rounded-pill" style="font-size: 0.85rem; font-weight: 700;"><?= $prod['stock_actual'] ?> und</span>
                                <?php else: ?>
                                    <span class="badge bg-soft-warning px-3 py-2 rounded-pill" style="font-size: 0.85rem; font-weight: 700;"><?= $prod['stock_actual'] ?> und</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <button class="btn-action btn-reabastecer" title="Crear orden de compra"
                                    onclick="Swal.fire({title:'Reabastecer', html:'<p>Crear orden de compra para <strong><?= htmlspecialchars($prod['nombre'], ENT_QUOTES) ?></strong></p>', icon:'info', showCancelButton:true, confirmButtonText:'Crear Orden', cancelButtonText:'Cancelar', confirmButtonColor:'#2563eb', customClass:{popup:'rounded-4'}})">
                                    <i class="fas fa-plus-circle"></i> Reabastecer
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
echo '</section></main></div>';
require_once __DIR__ . '/../layouts/footer.php';
?>

<script>
$(document).ready(function() {
    $('#tablaAlertas').DataTable({
        order: [[5, 'asc']],
        columnDefs: [
            { orderable: false, targets: [6] }
        ]
    });
});
</script>
