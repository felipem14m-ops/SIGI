<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) { header("Location: ../Usuario/login.php"); exit; }

$titulo = "Mis Ventas";

$ventas_hoy  = [];
$resumen_hoy = ['total_ventas' => 0, 'total_ingresos' => 0];
$_error_bd   = '';
$id_usuario  = $_SESSION['usuario']['id_usuario'] ?? 0;

try {
    require_once __DIR__ . '/../../config/database.php';
    require_once __DIR__ . '/../../models/Venta.php';
    $db          = (new Database())->conectar();
    $ventaModel  = new Venta($db);
    $resumen_hoy = $ventaModel->resumenDiario($id_usuario);
    $ventas_hoy  = $ventaModel->listarVentasHoy($id_usuario);
} catch (Throwable $e) {
    $_error_bd = 'No se pudieron cargar las ventas.';
    error_log("[SIGI] misventas.php Error: " . $e->getMessage());
}

require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidebar.php';
?>

<style>
    :root {
        --mv-primary: #2563eb;
        --mv-border:  #e2e8f0;
        --mv-surface: #ffffff;
        --mv-muted:   #64748b;
        --mv-text:    #0f172a;
        --mv-bg:      #f8fafc;
    }

    /* BANNER PREMIUM */
    .module-banner {
        background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
        border-radius: 20px;
        padding: 2rem;
        color: #fff;
        position: relative;
        overflow: hidden;
        margin-bottom: 2rem;
        box-shadow: 0 10px 25px rgba(37, 99, 235, 0.15);
    }
    .banner-label {
        background: rgba(255, 255, 255, 0.15);
        padding: 0.3rem 0.8rem;
        border-radius: 50px;
        font-size: 0.65rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1px;
        display: inline-block;
        margin-bottom: 0.5rem;
        backdrop-filter: blur(4px);
    }
    .module-banner::before { content: ''; position: absolute; top: -50px; right: -50px; width: 250px; height: 250px; background: rgba(255, 255, 255, 0.08); border-radius: 50%; }

    /* Métricas */
    .metric-card { background: var(--mv-surface); border-radius: 16px; border: 1px solid var(--mv-border); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.04); padding: 1.4rem; display: flex; align-items: center; gap: 1rem; transition: transform .2s, box-shadow .2s; }
    .metric-card:hover { transform: translateY(-3px); box-shadow: 0 10px 24px rgba(0,0,0,0.07); }
    .metric-icon { width: 50px; height: 50px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; flex-shrink: 0; }
    .metric-val  { font-size: 1.8rem; font-weight: 800; color: var(--mv-text); line-height: 1; }
    .metric-lbl  { font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; color: var(--mv-muted); }

    /* Tabla */
    .tbl-mv th { font-size: 0.72rem; text-transform: uppercase; letter-spacing: .5px; color: var(--mv-muted); font-weight: 700; border-bottom: 2px solid var(--mv-border); background: var(--mv-bg); padding: .9rem 1rem; }
    .tbl-mv td { padding: .85rem 1rem; vertical-align: middle; font-size: .88rem; color: #334155; border-bottom: 1px solid #f1f5f9; }

    /* DataTables Premium Style */
    .dataTables_wrapper .dataTables_filter input { border-radius: 10px; border: 1px solid var(--mv-border); padding: .45rem .9rem; background: var(--mv-bg); font-size: .85rem; outline: none; }
    .dataTables_wrapper .dataTables_paginate .paginate_button { border-radius: 10px !important; border: none !important; margin: 0 2px; font-weight: 700 !important; }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current { background: var(--mv-primary) !important; color: white !important; box-shadow: 0 4px 10px rgba(37, 99, 235, 0.2); }
</style>

<div class="container-fluid py-2">

    <!-- Banner de módulo Premium -->
    <div class="module-banner">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 position-relative" style="z-index: 2;">
            <div>
                <span class="banner-label">Auditoría de Turno</span>
                <h2 class="fw-bold m-0 text-white" style="font-family: 'Poppins', sans-serif; letter-spacing: -1px;">Mis Ventas</h2>
                <p class="m-0 text-white opacity-90 fw-light small">Historial detallado de todas tus transacciones realizadas hoy.</p>
            </div>
            <div class="d-flex align-items-center gap-3">
                <a href="ventas.php" class="btn btn-white btn-sm fw-bold px-4 py-2 rounded-3" style="background:#fff; color:#2563eb; border:none; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                    <i class="fas fa-plus me-2"></i> Nueva Venta
                </a>
            </div>
        </div>
    </div>

    <?php if ($_error_bd): ?>
        <div class="alert alert-warning rounded-3 mb-4"><i class="fas fa-exclamation-triangle me-2"></i><?= htmlspecialchars($_error_bd) ?></div>
    <?php endif; ?>

    <!-- Métricas -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="metric-card">
                <div class="metric-icon" style="background:#eff6ff; color:#2563eb;"><i class="fas fa-shopping-bag"></i></div>
                <div>
                    <div class="metric-val"><?= $resumen_hoy['total_ventas'] ?></div>
                    <div class="metric-lbl">Ventas Hoy</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="metric-card">
                <div class="metric-icon" style="background:#ecfdf5; color:#10b981;"><i class="fas fa-dollar-sign"></i></div>
                <div>
                    <div class="metric-val" style="font-size:1.4rem;">$<?= number_format($resumen_hoy['total_ingresos'], 0, ',', '.') ?></div>
                    <div class="metric-lbl">Ingresos Hoy</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="metric-card">
                <div class="metric-icon" style="background:#fef3c7; color:#d97706;"><i class="fas fa-receipt"></i></div>
                <div>
                    <div class="metric-val"><?= count($ventas_hoy) ?></div>
                    <div class="metric-lbl">Tickets</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="metric-card">
                <div class="metric-icon" style="background:#f0fdf4; color:#16a34a;"><i class="fas fa-chart-line"></i></div>
                <div>
                    <div class="metric-val" style="font-size:1.4rem;">
                        $<?= $resumen_hoy['total_ventas'] > 0
                            ? number_format($resumen_hoy['total_ingresos'] / $resumen_hoy['total_ventas'], 0, ',', '.')
                            : '0' ?>
                    </div>
                    <div class="metric-lbl">Ticket Promedio</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de ventas -->
    <div class="module-card overflow-hidden">
        <div class="table-responsive p-3">
            <table id="tablaVentas" class="table table-hover tbl-mv mb-0 w-100">
                <thead>
                    <tr>
                        <th>Ticket</th>
                        <th>Hora</th>
                        <th class="text-center">Ítems</th>
                        <th>Método de Pago</th>
                        <th class="text-end">Total</th>
                        <th class="text-center">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($ventas_hoy)): ?>
                        <?php foreach ($ventas_hoy as $v): ?>
                        <tr>
                            <td>
                                <span class="ticket-num">#<?= str_pad($v['id_venta'], 4, '0', STR_PAD_LEFT) ?></span>
                            </td>
                            <td class="text-muted"><?= date('h:i A', strtotime($v['fecha_venta'])) ?></td>
                            <td class="text-center">
                                <span class="badge rounded-pill bg-light text-dark border"><?= $v['total_items'] ?? '-' ?> und</span>
                            </td>
                            <td>
                                <?php
                                    $mp  = strtolower($v['metodo_pago'] ?? 'otro');
                                    $cls = match($mp) {
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
                            <td class="text-center">
                                <button class="btn btn-sm btn-light text-primary border-0 rounded-circle" style="width:32px; height:32px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);" onclick="verDetalle(<?= $v['id_venta'] ?>)" title="Ver Detalle">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fas fa-receipt fs-2 mb-2 d-block opacity-25"></i>
                                <span class="fw-semibold">Aún no hay ventas registradas hoy</span><br>
                                <a href="ventas.php" class="btn btn-sm btn-primary mt-3 rounded-3">
                                    <i class="fas fa-plus me-1"></i> Registrar primera venta
                                </a>
                            </td>
                        </tr>
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
    function _buildModalDetalle() {
        const prev = document.getElementById('modalHstDyn');
        if (prev) prev.remove();
        const prevOv = document.getElementById('modalHstOverlay');
        if (prevOv) prevOv.remove();

        const overlay = document.createElement('div');
        overlay.id = 'modalHstOverlay';
        overlay.style.cssText = 'position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:9998;backdrop-filter:blur(2px);';
        overlay.onclick = cerrarModalHst;
        document.body.appendChild(overlay);

        const wrap = document.createElement('div');
        wrap.id = 'modalHstDyn';
        wrap.style.cssText = 'position:fixed;top:0;left:0;width:100%;height:100%;z-index:9999;display:flex;align-items:center;justify-content:center;padding:1rem;';
        
        wrap.innerHTML = `
        <div class="modal-content shadow-lg" style="background:#fff; border-radius: 20px; border: none; overflow: hidden; width:100%; max-width:450px; animation: modalIn 0.3s ease;">
            <div style="background: #f8fafc; padding: 1.2rem 1.5rem; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #f1f5f9;">
                <h5 class="fw-bold m-0" style="color:#1e3a8a;">Detalle de Venta</h5>
                <button onclick="cerrarModalHst()" style="background:none; border:none; color:#64748b; font-size:1.2rem; cursor:pointer;">✕</button>
            </div>
            <div class="modal-body p-4" id="detalleContenidoDyn">
                <div class="text-center py-4"><div class="spinner-border text-primary"></div></div>
            </div>
            <div class="modal-footer border-0 p-3 pt-0 d-flex justify-content-end gap-2">
                <button type="button" onclick="cerrarModalHst()" class="btn btn-light fw-bold rounded-pill px-4">Cerrar</button>
                <button type="button" class="btn btn-primary fw-bold rounded-pill px-4" onclick="window.print()"><i class="fas fa-print me-2"></i>Imprimir</button>
            </div>
        </div>
        `;
        document.body.appendChild(wrap);
    }

    function cerrarModalHst() {
        document.getElementById('modalHstOverlay')?.remove();
        document.getElementById('modalHstDyn')?.remove();
    }

    const styleHst = document.createElement('style');
    styleHst.innerHTML = `
        @keyframes modalIn {
            from { opacity: 0; transform: scale(0.95) translateY(20px); }
            to { opacity: 1; transform: scale(1) translateY(0); }
        }
    `;
    document.head.appendChild(styleHst);

    async function verDetalle(id) {
        if (!id) return;
        _buildModalDetalle();
        const cont = document.getElementById('detalleContenidoDyn');
        
        try {
            const resp = await fetch(`../../controllers/VentaController.php?accion=detalle&id=${id}`);
            const data = await resp.json();
            
            if (data.ok) {
                let itemsHtml = '';
                data.productos.forEach(p => {
                    itemsHtml += `
                    <div class="d-flex justify-content-between mb-2">
                        <div style="font-size:.85rem;">
                            <div class="fw-bold">${p.nombre_producto}</div>
                            <small class="text-muted">${p.cantidad} x $${fmt(p.precio_unitario)}</small>
                        </div>
                        <div class="fw-bold">$${fmt(p.subtotal)}</div>
                    </div>`;
                });
                
                cont.innerHTML = `
                    <div class="text-center mb-4">
                        <div class="fw-bold h5 mb-0">TIENDA SIGI</div>
                        <small class="text-muted">Ticket de Venta</small><br>
                        <small class="text-muted">Venta #${String(id).padStart(5, '0')}</small>
                    </div>
                    <div class="border-top border-bottom py-3 mb-3">
                        ${itemsHtml}
                    </div>
                    <div class="d-flex justify-content-between h5 fw-bold mb-1">
                        <span>TOTAL</span>
                        <span>$${fmt(data.venta.total)}</span>
                    </div>
                    ${data.venta.monto_recibido ? `
                    <div class="d-flex justify-content-between text-muted small mt-2">
                        <span>Efectivo Recibido:</span>
                        <span>$${fmt(data.venta.monto_recibido)}</span>
                    </div>
                    <div class="d-flex justify-content-between text-success fw-bold">
                        <span>Cambio:</span>
                        <span>$${fmt(data.venta.cambio_devuelto || 0)}</span>
                    </div>
                    ` : ''}
                `;
            } else {
                cont.innerHTML = `<div class="alert alert-danger">${data.error}</div>`;
            }
        } catch (err) {
            cont.innerHTML = `<div class="alert alert-danger">Error al cargar el detalle</div>`;
        }
    }
    
    function fmt(n) { return Number(n).toLocaleString('es-CO', { maximumFractionDigits: 0 }); }

$(document).ready(function () {
    <?php if (!empty($ventas_hoy)): ?>
    $('#tablaVentas').DataTable({
        order: [[1, 'desc']],
        pageLength: 20,
        dom: 'rt<"px-4 py-3 d-flex justify-content-between align-items-center"ip>',
        language: {
            search:     "Buscar:",
            lengthMenu: "Mostrar _MENU_",
            info:       "_TOTAL_ ventas",
            paginate:   { previous: "Ant.", next: "Sig." },
            zeroRecords: "No se encontraron ventas"
        }
    });
    <?php endif; ?>
});
</script>
