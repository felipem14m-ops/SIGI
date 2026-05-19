<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
date_default_timezone_set('America/Bogota');

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: ../Usuario/login.php"); exit;
}

$_rol_actual = strtolower($_SESSION['usuario']['rol'] ?? $_SESSION['rol'] ?? '');
if ($_rol_actual === 'empleado') { header("Location: Empleado.php"); exit; }

$titulo = "Historial de Ventas";
$ventas = [];
$_error_bd = '';

try {
    require_once __DIR__ . '/../../config/database.php';
    require_once __DIR__ . '/../../models/Venta.php';
    
    $db = (new Database())->conectar();
    $ventaModel = new Venta($db);
    
    $filtro_fecha = $_GET['fecha'] ?? '';
    $filtro_usuario = $_GET['usuario'] ?? '';
    $ventas = $ventaModel->listarVentas($filtro_fecha, $filtro_usuario);
} catch (Throwable $e) {
    $_error_bd = 'No se pudo cargar el historial.';
}

require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidebar.php';
?>

<style>
    :root {
        --hst-primary: #2563eb;
        --hst-surface: #ffffff;
        --hst-bg: #f8fafc;
        --hst-border: #e2e8f0;
        --hst-text: #0f172a;
        --hst-muted: #64748b;
    }

    .history-card {
        background: var(--hst-surface);
        border-radius: 20px;
        border: 1px solid var(--hst-border);
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
        overflow: hidden;
    }

    .panel-head {
        padding: 1.5rem;
        border-bottom: 1px solid var(--hst-border);
        background: #f8fafc;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .table-hst th {
        background: #f1f5f9;
        color: #64748b;
        font-size: 0.72rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #e2e8f0;
        padding: 1.2rem 1rem;
        font-weight: 700;
    }

    .table-hst td {
        padding: 1rem;
        vertical-align: middle;
        font-size: 0.9rem;
        border-bottom: 1px solid #f1f5f9;
    }

    .badge-metodo {
        padding: 0.3rem 0.8rem;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 700;
    }

    .btn-detail {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        background: white;
        color: var(--hst-primary);
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
    }

    .btn-detail:hover {
        background: var(--hst-primary);
        color: white;
        border-color: var(--hst-primary);
        transform: scale(1.1);
    }

    /* Estilo del Banner Premium */
    .module-banner {
        background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
        border-radius: 24px;
        padding: 2.5rem;
        color: #fff;
        position: relative;
        overflow: hidden;
        margin-bottom: 2rem;
        box-shadow: 0 10px 25px -5px rgba(30, 58, 138, 0.3);
    }
    .banner-label {
        background: rgba(255, 255, 255, 0.15);
        padding: 0.4rem 1rem;
        border-radius: 50px;
        font-size: 0.65rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1px;
        display: inline-block;
        margin-bottom: 0.75rem;
        backdrop-filter: blur(4px);
    }
    .stat-pill-premium {
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 20px;
        padding: 0.6rem 1.2rem;
        display: flex;
        align-items: center;
        gap: 0.8rem;
        backdrop-filter: blur(8px);
    }

    /* PAGINACIÓN ESTILO PREMIUM (DataTables) */
    .dataTables_wrapper .dataTables_paginate {
        margin-top: 1.5rem !important;
        padding-bottom: 1.5rem !important;
        display: flex;
        justify-content: flex-end;
        gap: 5px;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        background: #f1f5f9 !important;
        border: none !important;
        border-radius: 8px !important;
        padding: 0.5rem 1rem !important;
        font-weight: 700 !important;
        color: #475569 !important;
        cursor: pointer;
        transition: all 0.2s;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: #e2e8f0 !important;
        color: #1e3a8a !important;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: #2563eb !important;
        color: #fff !important;
        box-shadow: 0 4px 10px rgba(37, 99, 235, 0.2);
    }
    
    .dataTables_wrapper .dataTables_info {
        font-size: 0.85rem;
        color: var(--hst-muted);
        font-weight: 600;
        padding: 1rem 0;
    }
    
    .dataTables_wrapper .dataTables_length select {
        border-radius: 8px;
        border: 1px solid var(--hst-border);
        padding: 0.3rem 0.5rem;
    }
    
    .dataTables_wrapper .dataTables_filter input {
        border-radius: 10px;
        border: 1px solid var(--hst-border);
        padding: 0.5rem 1rem;
        margin-left: 0.5rem;
        background: #f8fafc;
        outline: none;
    }
    
    /* Botones de paginación específicos de la imagen */
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        background: #f1f5f9 !important;
        border: none !important;
        border-radius: 6px !important;
        padding: 0.5rem 1.2rem !important;
        font-weight: 600 !important;
        margin: 0 2px !important;
        color: #475569 !important;
    }
    
    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: #007bff !important;
        color: #fff !important;
    }
    
    .dataTables_wrapper .dataTables_info {
        font-weight: 500;
        color: #64748b;
    }
</style>

<div class="container-fluid py-4">
    
    <!-- Banner de módulo Premium -->
    <div class="module-banner">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-4 position-relative" style="z-index: 2;">
            <div>
                <span class="banner-label" style="color: #fff;">Auditoría de Ventas</span>
                <h1 class="fw-bold m-0 text-white" style="font-family: 'Poppins', sans-serif; letter-spacing: -1px;">Historial de Ventas</h1>
                <p class="m-0 text-white opacity-90 fw-light">Consulta y seguimiento detallado de todas tus transacciones.</p>
            </div>
            <div class="d-flex align-items-center gap-3 flex-wrap">
                <div class="stat-pill-premium text-center text-white">
                    <div class="fw-900 fs-5 lh-1 text-white"><?= count($ventas) ?></div>
                    <div class="small fw-bold text-uppercase opacity-80" style="font-size: 0.6rem; color: #fff;">Registros</div>
                </div>
                <a href="Adminventas.php" class="btn btn-white fw-bold px-4 py-2 rounded-3" style="background:#fff; color:#2563eb; border:none; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                    <i class="fas fa-plus me-2"></i> Nueva Venta
                </a>
            </div>
        </div>
    </div>

    <div class="history-card">
        <div class="panel-head p-4">
            <form class="row g-3 align-items-end w-100" method="GET">
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted text-uppercase mb-2" style="letter-spacing: 0.5px;">Fecha de Venta</label>
                    <input type="date" name="fecha" value="<?= htmlspecialchars($filtro_fecha) ?>" class="form-control bg-light border-0 shadow-sm" style="border-radius: 12px; padding: 0.6rem 1rem;">
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-bold text-muted text-uppercase mb-2" style="letter-spacing: 0.5px;">Usuario / Cajero</label>
                    <div class="input-group shadow-sm" style="border-radius: 12px; overflow: hidden;">
                        <span class="input-group-text bg-light border-0" style="padding: 0.6rem 1rem;"><i class="fas fa-user text-muted"></i></span>
                        <input type="text" name="usuario" value="<?= htmlspecialchars($filtro_usuario) ?>" class="form-control bg-light border-0" placeholder="Nombre de cajero..." style="padding: 0.6rem 1rem;">
                    </div>
                </div>
                <div class="col-md-5 d-flex gap-2">
                    <button type="submit" class="btn fw-bold px-4 rounded-pill shadow-sm text-white w-100" style="background: linear-gradient(135deg, #1e3a8a, #2563eb); border: none; padding: 0.6rem 1rem;">
                        <i class="fas fa-filter me-2"></i> Aplicar Filtros
                    </button>
                    <a href="Adminhistorialventas.php" class="btn btn-light px-4 fw-bold rounded-pill border text-muted w-100 d-flex align-items-center justify-content-center" style="padding: 0.6rem 1rem;">
                        <i class="fas fa-undo me-2"></i> Limpiar
                    </a>
                </div>
            </form>
        </div>


        <div class="table-responsive">
            <table id="tablaHistorialVentas" class="table table-hst mb-0">
                <thead>
                    <tr>
                        <th>Folio</th>
                        <th>Fecha y Hora</th>
                        <th>Usuario / Cajero</th>
                        <th>Método Pago</th>
                        <th class="text-center">Ítems</th>
                        <th class="text-end">Total</th>
                        <th class="text-end">Cambio</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($ventas)): ?>
                        <tr><td colspan="8" class="text-center py-5 text-muted">No se encontraron ventas registrados</td></tr>
                    <?php else: ?>
                        <?php foreach($ventas as $v): ?>
                        <tr>
                            <td class="fw-bold text-primary">#<?= str_pad($v['id_venta'], 5, '0', STR_PAD_LEFT) ?></td>
                            <td><?= date('d/m/Y h:i A', strtotime($v['fecha_venta'])) ?></td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div style="width:30px;height:30px;border-radius:50%;background:#e2e8f0;display:flex;align-items:center;justify-content:center;font-size:.7rem;">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <?= htmlspecialchars($v['nombre_usuario']) ?>
                                </div>
                            </td>
                            <td>
                                <span class="badge-metodo bg-light text-dark border">
                                    <i class="fas fa-credit-card me-1 text-primary opacity-50"></i>
                                    <?= htmlspecialchars($v['metodo_pago']) ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge rounded-pill bg-primary" style="font-size:.7rem;"><?= $v['total_items'] ?> und</span>
                            </td>
                            <td class="text-end fw-bold text-dark">$<?= number_format($v['total'], 0, ',', '.') ?></td>
                            <td class="text-end">
                                <span class="fw-bold <?= ($v['cambio_devuelto'] > 0) ? 'text-success' : 'text-muted' ?>">
                                    $<?= number_format($v['cambio_devuelto'] ?? 0, 0, ',', '.') ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <button class="btn-detail ms-auto" onclick="verDetalle(<?= $v['id_venta'] ?>)" title="Ver Detalle">
                                    <i class="fas fa-eye"></i>
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

    // Estilos de animación
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
                        <small class="text-muted">NIT: 123.456.789-0</small><br>
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

    $(document).ready(function() {
        $('#tablaHistorialVentas').DataTable({
            "order": [[1, "desc"]], 
            "pageLength": 20,
            "dom": 'rt<"px-4 py-4 d-flex justify-content-between align-items-center"ip>',
            "language": {
                "lengthMenu": "Mostrar _MENU_",
                "zeroRecords": "No se encontraron ventas",
                "info": "_TOTAL_ ventas",
                "infoEmpty": "0 ventas",
                "infoFiltered": "(filtrado)",
                "search": "Buscar:",
                "paginate": {
                    "first": "Prim.",
                    "last": "Últ.",
                    "next": "Sig.",
                    "previous": "Ant."
                }
            },
            "columnDefs": [
                { "orderable": false, "targets": [7] }
            ]
        });
    });
</script>
<?php 
echo '</section></main></div>';
require_once __DIR__ . '/../layouts/footer.php'; 
?>
