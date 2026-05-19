<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) { header("Location: ../Usuario/login.php"); exit; }
$_rol_actual = strtolower($_SESSION['usuario']['rol'] ?? $_SESSION['rol'] ?? '');
if ($_rol_actual === 'empleado') { header("Location: Empleado.php"); exit; }

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
    :root { --c-primary:#2563eb; --c-border:#e2e8f0; --c-surface:#fff; --c-muted:#64748b; --c-text:#0f172a; --c-bg:#f8fafc; }

    .module-banner { background:linear-gradient(135deg,#1e3a8a 0%,#2563eb 100%); border-radius:18px; padding:1.75rem 2rem; color:#fff; position:relative; overflow:hidden; margin-bottom:1.5rem; box-shadow: 0 10px 25px -5px rgba(30, 58, 138, 0.3); }
    .module-banner::before { content:''; position:absolute; top:-40px; right:-40px; width:180px; height:180px; background:rgba(255,255,255,.08); border-radius:50%; }
    .module-banner::after  { content:''; position:absolute; bottom:-50px; right:120px; width:130px; height:130px; background:rgba(255,255,255,.05); border-radius:50%; }
    .module-banner .inner  { position:relative; z-index:1; }
    .module-banner h2      { font-size:1.5rem; font-weight:800; margin-bottom:.2rem; }
    .module-banner p       { font-size:.88rem; opacity:.85; margin:0; }
    .module-banner .stat-pill { background:rgba(255,255,255,.15); border:1px solid rgba(255,255,255,.3); border-radius:50px; padding:.4rem 1.1rem; font-size:.8rem; font-weight:700; display:inline-flex; align-items:center; gap:.5rem; color:#fff; backdrop-filter:blur(8px); box-shadow: 0 4px 12px rgba(0,0,0,0.1); }

    .module-card { background:var(--c-surface); border-radius:16px; border:1px solid var(--c-border); box-shadow:0 4px 6px -1px rgba(0,0,0,.05); }
    .table-custom th { font-size:.72rem; text-transform:uppercase; letter-spacing:.5px; color:var(--c-muted); font-weight:700; border-bottom:2px solid var(--c-border); background:var(--c-bg); padding:.9rem 1rem; }
    .table-custom td { padding:.85rem 1rem; vertical-align:middle; font-size:.88rem; color:#334155; border-bottom:1px solid #f1f5f9; }
    .table-custom tbody tr:hover td { background:#f8fafc; }

    .badge-alert { padding:.35rem .8rem; border-radius:50px; font-size:.72rem; font-weight:700; display:inline-flex; align-items:center; gap:.3rem; }
    .bg-soft-danger  { background:#fee2e2; color:#dc2626; }
    .bg-soft-warning { background:#fffbeb; color:#b45309; }
    .bg-soft-info    { background:#eff6ff; color:#2563eb; }

    .btn-action { padding:.45rem .9rem; border-radius:8px; border:none; font-weight:600; font-size:.82rem; display:inline-flex; align-items:center; gap:.4rem; transition:all .2s; cursor:pointer; }
    .btn-reabastecer { background:#eff6ff; color:#2563eb; } .btn-reabastecer:hover { background:#dbeafe; }

    .alert-summary-card { background:var(--c-surface); padding:1.5rem; border-radius:16px; border:1px solid var(--c-border); display:flex; align-items:center; gap:1.25rem; box-shadow:0 4px 6px -1px rgba(0,0,0,.05); height:100%; transition:transform .2s, box-shadow .2s; }
    .alert-summary-card:hover { transform:translateY(-4px); box-shadow:0 10px 24px rgba(0,0,0,.07); }
    .alert-summary-icon { width:56px; height:56px; border-radius:16px; display:flex; align-items:center; justify-content:center; font-size:1.5rem; flex-shrink:0; }
    .bg-icon-danger  { background:#fee2e2; color:#dc2626; }
    .bg-icon-warning { background:#fffbeb; color:#d97706; }
    .bg-icon-info    { background:#eff6ff; color:#2563eb; }

    .dataTables_wrapper .dataTables_filter input { border-radius:10px; border:1px solid var(--c-border); padding:.45rem .9rem; background:var(--c-bg); font-size:.85rem; }
    .dataTables_wrapper .dataTables_filter input:focus { border-color:var(--c-primary); box-shadow:0 0 0 3px rgba(37,99,235,.1); outline:none; }
    .dataTables_wrapper .dataTables_length select { border-radius:8px; border:1px solid var(--c-border); }
    .dataTables_wrapper .dataTables_info { font-size:.82rem; color:var(--c-muted); }
    .dataTables_wrapper .dataTables_paginate .paginate_button { border-radius:8px !important; border:none !important; margin:0 2px; }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current { background:var(--c-primary) !important; color:#fff !important; }
    .dataTables_wrapper .dataTables_paginate .paginate_button:hover  { background:#eff6ff !important; color:var(--c-primary) !important; }
</style>

<div class="container-fluid py-2">

    <!-- Banner de módulo -->
    <div class="module-banner">
        <div class="inner d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <span class="banner-label" style="color: #fff; background: rgba(255,255,255,0.15); padding: 0.4rem 1rem; border-radius: 50px; font-size: 0.65rem; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; display: inline-block; margin-bottom: 0.75rem; backdrop-filter: blur(4px);">Monitoreo de Inventario</span>
                <h2 class="fw-bold m-0 text-white" style="font-family: 'Poppins', sans-serif; letter-spacing: -1px;"><i class="fas fa-exclamation-triangle me-2"></i> Alertas de Stock</h2>
                <p class="m-0 text-white opacity-90 fw-light">Productos que requieren atención inmediata para evitar desabastecimiento.</p>
            </div>
            <div class="d-flex align-items-center gap-3 flex-wrap">
                <span class="stat-pill"><i class="fas fa-fire"></i> <?= $agotados ?> agotados</span>
                <span class="stat-pill"><i class="fas fa-exclamation"></i> <?= $bajo_stock ?> bajo mínimo</span>
            </div>
        </div>
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

    <!-- Panel de filtros -->
    <div class="module-card p-4 mb-4">
        <div class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-bold text-muted text-uppercase mb-2" style="letter-spacing: 0.5px;">Nombre del Producto</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0" style="border-radius: 12px 0 0 12px;"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" id="filtroProducto" class="form-control bg-light border-start-0" placeholder="Ej. Zapatos Nike..." style="border-radius: 0 12px 12px 0;">
                </div>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-bold text-muted text-uppercase mb-2" style="letter-spacing: 0.5px;">Tipo de Alerta</label>
                <select id="filtroTipoAlerta" class="form-select bg-light" style="border-radius: 12px;">
                    <option value="">Todos los tipos</option>
                    <option value="critico">Crítico — Agotado</option>
                    <option value="bajo">Bajo mínimo</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-bold text-muted text-uppercase mb-2" style="letter-spacing: 0.5px;">Estado</label>
                <select id="filtroEstadoAlerta" class="form-select bg-light" style="border-radius: 12px;">
                    <option value="">Todos los estados</option>
                    <option value="pendiente">Pendiente</option>
                    <option value="atendida">Atendida</option>
                </select>
            </div>
            <div class="col-md-5 d-flex gap-2">
                <button type="button" onclick="aplicarFiltros()" class="btn fw-bold px-4 rounded-pill shadow-sm text-white w-100" style="background: linear-gradient(135deg, #1e3a8a, #2563eb); border: none;">
                    <i class="fas fa-filter me-2"></i> Aplicar Filtros
                </button>
                <button id="btnLimpiarAlertas" class="btn btn-light px-4 fw-bold rounded-pill border text-muted w-100">
                    <i class="fas fa-undo me-2"></i> Limpiar Filtros
                </button>
            </div>
        </div>
        <div id="txtResultadosAlertas" class="text-muted mt-3 small fw-bold"><i class="fas fa-filter text-danger me-1"></i> Mostrando todas las alertas</div>
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
                        <?php $tipo_alerta = $prod['stock_actual'] <= 0 ? 'critico' : 'bajo'; ?>
                        <tr class="alerta-row"
                            data-producto="<?= strtolower(htmlspecialchars($prod['nombre'])) ?>"
                            data-tipo="<?= $tipo_alerta ?>"
                            data-estado="pendiente">
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
                                <div class="d-flex justify-content-end gap-2">
                                    <button class="btn-action btn-reabastecer" title="Reabastecer"
                                        onclick="Swal.fire({title:'Reabastecer', html:'<p>Crear orden de compra para <strong><?= htmlspecialchars($prod['nombre'], ENT_QUOTES) ?></strong></p>', icon:'info', showCancelButton:true, confirmButtonText:'Crear Orden', cancelButtonText:'Cancelar', confirmButtonColor:'#2563eb', customClass:{popup:'rounded-4'}})">
                                        <i class="fas fa-plus-circle"></i> Reabastecer
                                    </button>
                                    <button class="btn-action btn-atender" title="Marcar como atendida"
                                            style="background:#dcfce7;color:#16a34a;"
                                            onclick="marcarAtendida(this)">
                                        <i class="fas fa-check"></i> Atender
                                    </button>
                                </div>
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
    if ($.fn.DataTable.isDataTable('#tablaAlertas')) {
        $('#tablaAlertas').DataTable().destroy();
    }
    $('#tablaAlertas').DataTable({
        searching: false,
        order: [[5, 'asc']],
        pageLength: 20,
        dom: 'rt<"px-4 py-4 d-flex justify-content-between align-items-center"ip>',
        columnDefs: [{ orderable: false, targets: [6] }],
        language: {
            lengthMenu: "Mostrar _MENU_",
            info: "_TOTAL_ alertas",
            paginate: { previous: "Ant.", next: "Sig." },
            zeroRecords: "No se encontraron alertas"
        }
    });
});

// =====================================================
// FILTROS EN TIEMPO REAL
// =====================================================
const alertaRows = document.querySelectorAll('.alerta-row');

function aplicarFiltros() {
    const producto = document.getElementById('filtroProducto').value.toLowerCase().trim();
    const tipo     = document.getElementById('filtroTipoAlerta').value;
    const estado   = document.getElementById('filtroEstadoAlerta').value;

    let visibles = 0;

    alertaRows.forEach(row => {
        const okProducto = !producto || (row.dataset.producto || '').includes(producto);
        const okTipo     = !tipo     || row.dataset.tipo    === tipo;
        const okEstado   = !estado   || row.dataset.estado  === estado;

        const visible = okProducto && okTipo && okEstado;
        row.style.display = visible ? '' : 'none';
        if (visible) visibles++;
    });

    const total = alertaRows.length;
    const txt   = document.getElementById('txtResultadosAlertas');
    const hayFiltro = producto || tipo || estado;
    if (hayFiltro) {
        txt.innerHTML = `<strong>${visibles}</strong> de <strong>${total}</strong> alertas coinciden con los filtros`;
    } else {
        txt.textContent = `Mostrando todas las alertas (${total})`;
    }
}

['filtroProducto','filtroTipoAlerta','filtroEstadoAlerta'].forEach(id => {
    const el = document.getElementById(id);
    if (el) { el.addEventListener('input', filtrarAlertas); el.addEventListener('change', filtrarAlertas); }
});

document.getElementById('btnLimpiarAlertas').addEventListener('click', function() {
    document.getElementById('filtroProducto').value     = '';
    document.getElementById('filtroTipoAlerta').value   = '';
    document.getElementById('filtroEstadoAlerta').value = '';
    aplicarFiltros();
});

aplicarFiltros();

// =====================================================
// MARCAR COMO ATENDIDA
// =====================================================
function marcarAtendida(btn) {
    const row = btn.closest('tr');
    row.dataset.estado = 'atendida';

    // Feedback visual
    row.style.opacity = '.5';
    btn.innerHTML = '<i class="fas fa-check-double"></i> Atendida';
    btn.style.background = '#e0f2fe';
    btn.style.color = '#0284c7';
    btn.disabled = true;

    // Añadir badge en la fila
    const tdProducto = row.querySelector('td:first-child');
    if (tdProducto && !tdProducto.querySelector('.badge-atendida')) {
        const badge = document.createElement('span');
        badge.className = 'badge-atendida ms-2';
        badge.style.cssText = 'background:#dcfce7;color:#16a34a;font-size:.7rem;font-weight:700;padding:.2rem .55rem;border-radius:50px;';
        badge.textContent = 'Atendida';
        tdProducto.querySelector('.fw-bold')?.appendChild(badge);
    }

    aplicarFiltros();
}
</script>
