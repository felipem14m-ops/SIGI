<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: ../Usuario/login.php");
    exit;
}
$_rol_actual = strtolower($_SESSION['usuario']['rol'] ?? $_SESSION['rol'] ?? '');
if ($_rol_actual === 'empleado') {
    header("Location: Empleado.php");
    exit;
}

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Inventario.php';
require_once __DIR__ . '/../../models/Producto.php';

$db = (new Database())->conectar();
$invModel = new Inventario($db);
$prodModel = new Producto($db);

$resumen = $invModel->obtenerResumenStock();
$movimientos = $invModel->listarMovimientos(200);
$tipos = $invModel->obtenerTiposMovimiento();
$productos = $prodModel->listarTodos();

$titulo = "Control de Inventario";
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidebar.php';
?>

<style>
    .module-card {
        background: #fff;
        border-radius: 24px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }

    .table-pro th {
        background: #f1f5f9;
        text-transform: uppercase;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.5px;
        color: #64748b;
        padding: 1.2rem 1rem;
        border-bottom: 2px solid #e2e8f0;
    }

    .table-pro td {
        padding: 1.2rem 1rem;
        vertical-align: middle;
        font-size: 0.88rem;
        border-bottom: 1px solid #f1f5f9;
        color: #334155;
    }

    .badge-tipo {
        padding: 0.5rem 1rem;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .t-entrada {
        background: #dcfce7;
        color: #16a34a;
    }

    .t-salida {
        background: #fee2e2;
        color: #dc2626;
    }

    .t-ajuste {
        background: #f1f5f9;
        color: #475569;
    }

    /* Estilo Premium para el Modal */
    .modal-premium {
        border-radius: 30px;
        border: none;
        overflow: hidden;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
    }

    .modal-premium .modal-header {
        background: #fff;
        border-bottom: 1px solid #f1f5f9;
        padding: 2rem 2rem 1rem;
    }

    .modal-premium .modal-body {
        padding: 2rem;
    }

    .form-pro-label {
        font-size: 0.75rem;
        font-weight: 800;
        text-transform: uppercase;
        color: #94a3b8;
        margin-bottom: 0.6rem;
        display: block;
        letter-spacing: 1px;
    }

    .form-pro-input {
        border-radius: 14px;
        border: 2px solid #f1f5f9;
        padding: 0.8rem 1rem;
        font-size: 0.95rem;
        transition: all 0.2s;
        background: #f8fafc;
    }

    .form-pro-input:focus {
        border-color: #10b981;
        background: #fff;
        box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1);
        outline: none;
    }

    .btn-save-pro {
        background: #10b981;
        color: #fff;
        border: none;
        padding: 1rem;
        border-radius: 16px;
        font-weight: 700;
        font-size: 1rem;
        transition: all 0.3s;
        box-shadow: 0 10px 15px -3px rgba(16, 185, 129, 0.3);
    }

    .btn-save-pro:hover {
        background: #059669;
        transform: translateY(-2px);
        box-shadow: 0 20px 25px -5px rgba(16, 185, 129, 0.4);
    }

    /* Estilo del Nuevo Banner Premium */
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

    /* DATATABLES PREMIUM STYLE */
    .dataTables_wrapper .dataTables_length {
        margin-bottom: 1.5rem !important;
        padding-left: 0.5rem;
    }

    .dataTables_wrapper .dataTables_length select {
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        padding: 0.4rem 1rem;
        outline: none;
        margin: 0 0.5rem;
        background: #f8fafc;
        font-weight: 700;
        color: #1e3a8a;
    }

    .dataTables_wrapper .dataTables_info {
        font-size: 0.85rem;
        font-weight: 600;
        color: #64748b;
        margin-top: 1rem;
    }

    .dataTables_paginate {
        margin-top: 1rem !important;
        display: flex;
        justify-content: flex-end;
        gap: 5px;
    }

    .dataTables_paginate .paginate_button {
        background: #f1f5f9 !important;
        border: none !important;
        border-radius: 10px !important;
        padding: 0.5rem 1rem !important;
        font-weight: 700 !important;
        color: #475569 !important;
        cursor: pointer;
        transition: 0.2s;
    }

    .dataTables_paginate .paginate_button:hover {
        background: #e2e8f0 !important;
    }

    .dataTables_paginate .paginate_button.current {
        background: #2563eb !important;
        color: #fff !important;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
    }

    /* CENTRADO PERFECTO DEL MODAL */
    #modalInventarioPro .modal-dialog {
        margin: auto !important;
    }
</style>

<div class="container-fluid py-4">
    <!-- Banner de módulo Premium -->
    <div class="module-banner">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-4 position-relative" style="z-index: 2;">
            <div>
                <span class="banner-label" style="color: #fff;">Control de Almacén</span>
                <h1 class="fw-bold m-0 text-white" style="font-family: 'Poppins', sans-serif; letter-spacing: -1px;">Gestión de Inventario</h1>
                <p class="m-0 text-white opacity-90 fw-light">Auditoría completa de movimientos.</p>
            </div>
            <div class="d-flex align-items-center gap-3 flex-wrap">
                <div class="stat-pill-premium text-center text-white">
                    <div class="fw-900 fs-5 lh-1 text-white"><?= $resumen['total_items'] ?></div>
                    <div class="small fw-bold text-uppercase opacity-80" style="font-size: 0.6rem; color: #fff;">Items</div>
                </div>
                <button class="btn btn-white fw-bold px-4 py-2 rounded-3" style="background:#fff; color:#2563eb; border:none; box-shadow: 0 4px 15px rgba(0,0,0,0.1);" onclick="abrirModalInventario()">
                    <i class="fas fa-exchange-alt me-2"></i> Nuevo Movimiento
                </button>
            </div>
        </div>
    </div>
    <!-- Sección de Filtros Premium -->
    <div class="module-card mb-4">
        <div class="p-4">
            <div class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label class="form-label small fw-bold text-muted text-uppercase mb-2" style="letter-spacing: 0.5px;">Buscar Producto</label>
                    <div class="input-group shadow-sm" style="border-radius: 12px; overflow: hidden;">
                        <span class="input-group-text bg-light border-0" style="padding: 0.6rem 1rem;"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" id="filtroProducto" class="form-control bg-light border-0" placeholder="Nombre del producto o ID..." style="padding: 0.6rem 1rem;">
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted text-uppercase mb-2" style="letter-spacing: 0.5px;">Tipo de Movimiento</label>
                    <select id="filtroTipo" class="form-select bg-light border-0 shadow-sm" style="border-radius: 12px; padding: 0.6rem 1rem;">
                        <option value="">Todos los tipos</option>
                        <?php foreach ($tipos as $t): ?>
                            <option value="<?= $t['id_tipo_movimiento'] ?>"><?= htmlspecialchars($t['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <button type="button" onclick="aplicarFiltros()" class="btn fw-bold px-4 rounded-pill shadow-sm text-white w-100" style="background: linear-gradient(135deg, #1e3a8a, #2563eb); border: none; padding: 0.6rem 1rem;">
                        <i class="fas fa-filter me-2"></i> Aplicar Filtros
                    </button>
                    <button type="button" id="btnLimpiarFiltros" class="btn btn-light px-4 fw-bold rounded-pill border text-muted w-100" style="padding: 0.6rem 1rem;">
                        <i class="fas fa-undo me-2"></i> Limpiar
                    </button>
                </div>
            </div>
            <div id="txtResultados" class="text-muted small fw-bold mt-3 opacity-75">Mostrando todos los movimientos</div>
        </div>
    </div>
    <div class="module-card">
        <div class="p-0">
            <table id="tablaInv" class="table table-pro w-100 mb-0">
                <thead>
                    <tr>
                        <th class="text-center">ID</th>
                        <th>Fecha y Hora</th>
                        <th>Tipo y Origen</th>
                        <th>Producto</th>
                        <th class="text-center">Stock Anterior</th>
                        <th class="text-center">Cantidad</th>
                        <th class="text-center">Stock Final</th>
                        <th>Responsable</th>
                        <th>Motivo</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($movimientos as $m):
                        $clase = 't-ajuste';
                        if ($m['id_tipo_movimiento'] == 1) $clase = 't-entrada';
                        if ($m['id_tipo_movimiento'] == 2) $clase = 't-salida';
                    ?>
                        <tr class="movimiento-row" data-producto="<?= strtolower(htmlspecialchars($m['nombre_producto'])) ?> #<?= $m['id_producto'] ?>" data-tipo="<?= $m['id_tipo_movimiento'] ?>">
                            <td class="text-center fw-bold text-muted small">#<?= $m['id_movimiento'] ?></td>
                            <td>
                                <div class="fw-bold"><?= date('d/m/Y', strtotime($m['fecha'])) ?></div>
                                <div class="text-muted small"><?= date('H:i:s', strtotime($m['fecha'])) ?></div>
                            </td>
                            <td>
                                <div class="mb-1"><span class="badge-tipo <?= $clase ?>"><?= htmlspecialchars($m['tipo_movimiento']) ?></span></div>
                                <div class="text-muted small text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.5px;"><i class="fas <?= $m['origen'] === 'automatico' ? 'fa-robot' : 'fa-hand-paper' ?> me-1"></i> <?= htmlspecialchars($m['origen']) ?></div>
                            </td>
                            <td>
                                <div class="fw-bold"><?= htmlspecialchars($m['nombre_producto']) ?></div>
                                <div class="text-muted small">ID: #<?= $m['id_producto'] ?></div>
                            </td>
                            <td class="fw-bold text-center text-muted">
                                <?= $m['stock_anterior'] ?>
                            </td>
                            <td class="fw-bold text-center <?= $m['cantidad'] > 0 ? 'text-success' : 'text-danger' ?>">
                                <?= $m['cantidad'] > 0 ? '+' : '' ?><?= $m['cantidad'] ?>
                            </td>
                            <td class="fw-bold text-center">
                                <span class="bg-light px-3 py-1 rounded-pill"><?= $m['stock_resultante'] ?></span>
                            </td>
                            <td><i class="fas fa-user-circle me-1 opacity-50"></i> <?= htmlspecialchars($m['nombre_usuario']) ?></td>
                            <td class="text-muted small" style="max-width: 150px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="<?= htmlspecialchars($m['motivo'] ?: '-') ?>"><?= htmlspecialchars($m['motivo'] ?: '-') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php echo '</section></main></div>'; ?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

<script>
    const TIPOS_MOV = <?= json_encode($tipos) ?>;
    const PRODUCTOS_INV = <?= json_encode(array_map(fn($p) => [
                                'id' => $p['id_producto'],
                                'nombre' => $p['nombre'],
                                'stock' => $p['stock_actual']
                            ], $productos)) ?>;

    function _buildModalInventario() {
        const prev = document.getElementById('modalInvDyn');
        if (prev) prev.remove();
        const prevOv = document.getElementById('modalInvOverlay');
        if (prevOv) prevOv.remove();

        const overlay = document.createElement('div');
        overlay.id = 'modalInvOverlay';
        overlay.style.cssText = 'position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:9998;backdrop-filter:blur(2px);';
        overlay.onclick = cerrarModalInv;
        document.body.appendChild(overlay);

        const wrap = document.createElement('div');
        wrap.id = 'modalInvDyn';
        wrap.style.cssText = 'position:fixed;top:0;left:0;width:100%;height:100%;z-index:9999;display:flex;align-items:center;justify-content:center;padding:1rem;';

        wrap.innerHTML = `
        <div class="modal-content shadow-lg" style="background:#fff; border-radius: 24px; border: none; overflow: hidden; width:100%; max-width:550px; animation: modalIn 0.3s ease;">
            <div style="background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%); padding: 1.5rem 2rem; display: flex; justify-content: space-between; align-items: center; color: #fff;">
                <div>
                    <h4 class="fw-bold mb-1 text-white" style="font-size:1.4rem;"><i class="fas fa-exchange-alt me-2"></i>Registrar Movimiento</h4>
                    <p class="text-white opacity-75 small mb-0">Afectar stock de forma manual</p>
                </div>
                <button onclick="cerrarModalInv()" style="background:rgba(255,255,255,0.2); border:none; color:#fff; width:36px; height:36px; border-radius:10px; cursor:pointer;">✕</button>
            </div>
            <div class="modal-body p-4 p-md-5">
                <form id="formMovimientoDyn">
                    <div class="mb-4">
                        <label class="form-pro-label">Producto a modificar</label>
                        <select name="id_producto" class="form-select form-pro-input" required style="border: 2px solid #f1f5f9; border-radius: 12px; padding: 0.8rem;">
                            <option value="">Seleccione un producto...</option>
                            ${PRODUCTOS_INV.map(p => `<option value="${p.id}">${p.nombre} (Actual: ${p.stock})</option>`).join('')}
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-pro-label">Tipo de Acción</label>
                            <select name="id_tipo" class="form-select form-pro-input" required style="border: 2px solid #f1f5f9; border-radius: 12px; padding: 0.8rem;">
                                ${TIPOS_MOV.map(t => `<option value="${t.id_tipo_movimiento}">${t.nombre}</option>`).join('')}
                            </select>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-pro-label">Cantidad</label>
                            <input type="number" name="cantidad" class="form-control form-pro-input" placeholder="0" required style="border: 2px solid #f1f5f9; border-radius: 12px; padding: 0.8rem;">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-pro-label">Justificación / Motivo</label>
                        <textarea name="motivo" class="form-control form-pro-input" rows="3" placeholder="Escriba el motivo del ajuste..." style="border: 2px solid #f1f5f9; border-radius: 12px; padding: 0.8rem;"></textarea>
                    </div>

                    <button type="submit" class="btn w-100 fw-bold py-3 rounded-3 shadow-sm text-white" style="background:linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%); border:none; border-radius: 16px;">
                        <i class="fas fa-check-circle me-2"></i> PROCESAR MOVIMIENTO
                    </button>
                </form>
            </div>
        </div>
        `;
        document.body.appendChild(wrap);

        // Adjuntar evento de envío al nuevo formulario
        document.getElementById('formMovimientoDyn').onsubmit = enviarMovimiento;
    }

    function cerrarModalInv() {
        document.getElementById('modalInvOverlay')?.remove();
        document.getElementById('modalInvDyn')?.remove();
    }

    function abrirModalInventario() {
        _buildModalInventario();
    }

    // Estilos de animación
    const styleInv = document.createElement('style');
    styleInv.innerHTML = `
        @keyframes modalIn {
            from { opacity: 0; transform: scale(0.95) translateY(20px); }
            to { opacity: 1; transform: scale(1) translateY(0); }
        }
    `;
    document.head.appendChild(styleInv);

    async function enviarMovimiento(e) {
        e.preventDefault();
        const btn = e.target.querySelector('button[type="submit"]');
        const originalText = btn.innerHTML;

        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Procesando...';

        try {
            const formData = new FormData(e.target);
            const resp = await fetch('../../controllers/InventarioController.php?accion=registrar_movimiento', {
                method: 'POST',
                body: formData
            });
            const data = await resp.json();

            if (data.ok) {
                Swal.fire({
                    title: '¡Ajuste Realizado!',
                    text: data.mensaje,
                    icon: 'success',
                    confirmButtonColor: '#10b981'
                }).then(() => location.reload());
            } else {
                throw new Error(data.error);
            }
        } catch (err) {
            Swal.fire({
                title: 'Error en Proceso',
                text: err.message,
                icon: 'error',
                confirmButtonColor: '#dc2626'
            });
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    }

    $(document).ready(function() {
        // Reinicialización segura de DataTable
        if ($.fn.DataTable.isDataTable('#tablaInv')) {
            $('#tablaInv').DataTable().destroy();
        }
        $('#tablaInv').DataTable({
            searching: true,
            pageLength: 20,
            dom: 'rt<"px-4 py-4 d-flex justify-content-between align-items-center"ip>',
            language: {
                lengthMenu: "Mostrar _MENU_",
                info: "_TOTAL_ movimientos",
                infoEmpty: "0 movimientos",
                infoFiltered: "(filtrado)",
                paginate: {
                    previous: "Ant.",
                    next: "Sig.",
                    first: "Prim.",
                    last: "Últ."
                },
                zeroRecords: "No se encontraron movimientos"
            }
        });

        // Filtrado Premium en Tiempo Real con DataTables API
        $.fn.dataTable.ext.search.push(
            function(settings, data, dataIndex, rowData, counter) {
                if (settings.nTable.id !== 'tablaInv') return true;

                const busqueda = $('#filtroProducto').val().toLowerCase().trim();
                const tipo = $('#filtroTipo').val();

                const rowEl = $(settings.aoData[dataIndex].nTr);
                const dataProd = (rowEl.data('producto') || '').toString().toLowerCase();
                const dataTipo = (rowEl.data('tipo') || '').toString();

                const okProd = !busqueda || dataProd.includes(busqueda);
                const okTipo = !tipo || dataTipo === tipo;

                return okProd && okTipo;
            }
        );

        function aplicarFiltros() {
            const busqueda = $('#filtroProducto').val().toLowerCase().trim();
            const tipo = $('#filtroTipo').val();

            // Actualizar tabla
            const table = $('#tablaInv').DataTable();
            table.draw();

            const info = table.page.info();
            $('#txtResultados').text(busqueda || tipo ? `Se encontraron ${info.recordsDisplay} movimientos con los filtros aplicados` : `Mostrando todos los movimientos (${info.recordsTotal})`);
        }

        $('#filtroProducto, #filtroTipo').on('input change', aplicarFiltros);
        $('#btnLimpiarFiltros').on('click', function() {
            $('#filtroProducto').val('');
            $('#filtroTipo').val('');
            aplicarFiltros();
        });
    });
</script>