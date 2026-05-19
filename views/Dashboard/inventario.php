<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) { header("Location: Usuario/login.php"); exit; }

$titulo = "Gestión de Inventario";

// =========================================================================
// CONEXIÓN A MODELOS (Defensivo: sidebar siempre se renderiza)
// =========================================================================
$movimientos = [];
$productos_select = [];
$tipos_movimiento = [];
$_error_bd = '';

try {
    require_once __DIR__ . '/../../config/database.php';
    require_once __DIR__ . '/../../models/Inventario.php';
    require_once __DIR__ . '/../../models/Producto.php';
    $db = (new Database())->conectar();
    $inventarioModel = new Inventario($db);
    $movimientos = $inventarioModel->listarMovimientos(100);
    $tipos_movimiento = $inventarioModel->obtenerTiposMovimiento();
    $productos_select = (new Producto($db))->listarTodos();
} catch (Throwable $e) {
    $_error_bd = 'No se pudieron cargar los datos. Verifique la base de datos.';
    error_log("[SIGI] inventario.php Error: " . $e->getMessage());
}

require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidebar.php';
?>

<style>
    .module-card { background: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); }
    .table-custom th { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b; font-weight: 700; border-bottom: 2px solid #e2e8f0; background: #f8fafc; padding: 1rem; }
    .table-custom td { padding: 0.85rem 1rem; vertical-align: middle; font-size: 0.9rem; color: #334155; border-bottom: 1px solid #f1f5f9; }
    
    .badge-mov { padding: 0.35rem 0.75rem; border-radius: 50px; font-size: 0.75rem; font-weight: 600; display: inline-flex; align-items: center; gap: 0.3rem; }
    .mov-entrada { background: #dcfce7; color: #16a34a; }
    .mov-salida { background: #fee2e2; color: #dc2626; }
    .mov-ajuste { background: #ede9fe; color: #7c3aed; }

    /* DataTables custom */
    .dataTables_wrapper .dataTables_filter input { border-radius: 10px; border: 1px solid #e2e8f0; padding: 0.5rem 1rem; font-size: 0.9rem; background: #f8fafc; }
    .dataTables_wrapper .dataTables_filter input:focus { border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37,99,235,0.1); outline: none; }
    .dataTables_wrapper .dataTables_length select { border-radius: 8px; border: 1px solid #e2e8f0; padding: 0.4rem 2rem 0.4rem 0.6rem; }
    .dataTables_wrapper .dataTables_info { font-size: 0.85rem; color: #64748b; }
    .dataTables_wrapper .dataTables_paginate .paginate_button { border-radius: 8px !important; border: none !important; margin: 0 2px; }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current { background: #2563eb !important; color: white !important; border: none !important; }
    .dataTables_wrapper .dataTables_paginate .paginate_button:hover { background: #eff6ff !important; color: #2563eb !important; border: none !important; }
</style>

<div class="container-fluid py-2">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h2 class="h4 mb-1 fw-bold text-dark">Movimientos de Inventario</h2>
            <p class="text-muted mb-0" style="font-size: 0.9rem;">
                Historial de entradas, salidas y ajustes de stock.
                <span class="badge bg-primary rounded-pill ms-2"><?= count($movimientos) ?> registros</span>
            </p>
        </div>
        <button class="btn btn-primary rounded-3 shadow-sm" style="background-color: #2563eb; border: none; font-weight: 500;" data-bs-toggle="modal" data-bs-target="#modalMovimiento">
            <i class="fas fa-exchange-alt me-2"></i> Registrar Movimiento
        </button>
    </div>

    <div class="module-card overflow-hidden">
        <div class="table-responsive p-3">
            <table id="tablaInventario" class="table table-hover table-custom mb-0 w-100">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Tipo</th>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Responsable</th>
                        <th>Motivo</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($movimientos)): ?>
                        <?php foreach ($movimientos as $mov): ?>
                        <tr>
                            <td>
                                <div class="fw-bold"><?= date('d/m/Y', strtotime($mov['fecha_movimiento'])) ?></div>
                                <small class="text-muted"><?= date('H:i', strtotime($mov['fecha_movimiento'])) ?></small>
                            </td>
                            <td>
                                <?php
                                $tipo = strtolower($mov['tipo_movimiento']);
                                $clases = ['entrada' => 'mov-entrada', 'salida' => 'mov-salida', 'ajuste' => 'mov-ajuste'];
                                $iconos = ['entrada' => 'fa-arrow-down', 'salida' => 'fa-arrow-up', 'ajuste' => 'fa-sync'];
                                ?>
                                <span class="badge-mov <?= $clases[$tipo] ?? 'mov-ajuste' ?>">
                                    <i class="fas <?= $iconos[$tipo] ?? 'fa-sync' ?> me-1"></i> <?= ucfirst($tipo) ?>
                                </span>
                            </td>
                            <td>
                                <div class="fw-bold"><?= htmlspecialchars($mov['nombre_producto'] ?? 'Producto #' . $mov['id_producto']) ?></div>
                            </td>
                            <td>
                                <?php if ($tipo === 'entrada'): ?>
                                    <span class="fw-bold text-success">+ <?= abs($mov['cantidad']) ?> und</span>
                                <?php elseif ($tipo === 'salida'): ?>
                                    <span class="fw-bold text-danger">- <?= abs($mov['cantidad']) ?> und</span>
                                <?php else: ?>
                                    <span class="fw-bold" style="color: #7c3aed;"><?= $mov['cantidad'] > 0 ? '+' : '' ?><?= $mov['cantidad'] ?> und</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-muted"><?= htmlspecialchars($mov['nombre_usuario'] ?? 'Sistema') ?></td>
                            <td class="text-muted" style="font-size: 0.85rem;"><?= htmlspecialchars($mov['motivo'] ?? '-') ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- =========================================================================
   MODAL: REGISTRAR MOVIMIENTO DE INVENTARIO
   ========================================================================= -->
<div class="modal fade" id="modalMovimiento" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-0 px-4 pt-4 pb-0">
                <h5 class="modal-title fw-bold">
                    <i class="fas fa-exchange-alt me-2 text-primary"></i> Registrar Movimiento
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="<?= $base_url ?>controllers/InventarioController.php?accion=registrar">
                <div class="modal-body px-4 py-3">
                    <div class="mb-3">
                        <label class="form-label fw-bold" style="font-size: 0.85rem; color: #475569;">Producto</label>
                        <select name="id_producto" class="form-select bg-light rounded-3" required>
                            <option value="">Seleccionar producto...</option>
                            <?php foreach ($productos_select as $prod): ?>
                                <option value="<?= $prod['id_producto'] ?>">
                                    <?= htmlspecialchars($prod['nombre']) ?> 
                                    (Stock: <?= $prod['stock_actual'] ?> und)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-7">
                            <label class="form-label fw-bold" style="font-size: 0.85rem; color: #475569;">Tipo de Movimiento</label>
                            <select name="tipo_movimiento" id="tipoMovimiento" class="form-select bg-light rounded-3" required>
                                <option value="">Seleccionar...</option>
                                <option value="entrada">📦 Entrada (Compra/Recepción)</option>
                                <option value="salida">📤 Salida (Despacho)</option>
                                <option value="ajuste">🔧 Ajuste Manual</option>
                            </select>
                        </div>
                        <div class="col-5">
                            <label class="form-label fw-bold" style="font-size: 0.85rem; color: #475569;">Cantidad</label>
                            <input type="number" name="cantidad" class="form-control bg-light rounded-3" min="1" placeholder="0" required>
                        </div>
                    </div>
                    <!-- Solo para ajustes: dirección negativa -->
                    <div class="mb-3 d-none" id="ajusteNegativo">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="ajuste_negativo" id="chkAjusteNeg">
                            <label class="form-check-label text-muted" for="chkAjusteNeg" style="font-size: 0.85rem;">
                                Ajuste negativo (reducir stock por merma o vencimiento)
                            </label>
                        </div>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-bold" style="font-size: 0.85rem; color: #475569;">Motivo / Observación</label>
                        <input type="text" name="motivo" class="form-control bg-light rounded-3" placeholder="Ej: Compra a proveedor Alpina, Ajuste de inventario...">
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-0">
                    <button type="button" class="btn btn-light rounded-3 fw-bold px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary rounded-3 fw-bold px-4" style="background: #2563eb; border: none;">
                        <i class="fas fa-save me-1"></i> Registrar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php 
echo '</section></main></div>';
require_once __DIR__ . '/../layouts/footer.php'; 
?>

<script>
$(document).ready(function() {
    $('#tablaInventario').DataTable({
        order: [[0, 'desc']]
    });
});

// Mostrar opción de ajuste negativo solo cuando tipo = ajuste
document.getElementById('tipoMovimiento').addEventListener('change', function() {
    const bloque = document.getElementById('ajusteNegativo');
    bloque.classList.toggle('d-none', this.value !== 'ajuste');
    if (this.value !== 'ajuste') {
        document.getElementById('chkAjusteNeg').checked = false;
    }
});
</script>
