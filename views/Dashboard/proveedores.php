<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) { header("Location: Usuario/login.php"); exit; }

$titulo = "Directorio de Proveedores";

// =========================================================================
// CONEXIÓN A MODELOS (Defensivo: sidebar siempre se renderiza)
// =========================================================================
$proveedores = [];
$_error_bd = '';

try {
    require_once __DIR__ . '/../../config/database.php';
    require_once __DIR__ . '/../../models/Proveedor.php';
    $db = (new Database())->conectar();
    $proveedores = (new Proveedor($db))->listarTodos();
} catch (Throwable $e) {
    $_error_bd = 'No se pudieron cargar los datos. Verifique la base de datos.';
    error_log("[SIGI] proveedores.php Error: " . $e->getMessage());
}

require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidebar.php';
?>

<style>
    .provider-card { background: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; padding: 1.5rem; transition: transform 0.2s, box-shadow 0.2s; height: 100%; display: flex; flex-direction: column; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); }
    .provider-card:hover { transform: translateY(-5px); box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
    .provider-icon { width: 56px; height: 56px; background: #f8fafc; border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; margin-bottom: 1rem; }
    .provider-status { padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.75rem; font-weight: 600; }
    .status-activo { background: #dcfce7; color: #16a34a; }
    .status-inactivo { background: #fee2e2; color: #dc2626; }
    .btn-card-action { flex: 1; display: flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 0.5rem; border-radius: 8px; font-size: 0.85rem; font-weight: 600; border: none; transition: all 0.2s; text-decoration: none; cursor: pointer; }
    .btn-card-detail { background: #eef2ff; color: #4f46e5; }
    .btn-card-detail:hover { background: #e0e7ff; }
    .btn-card-more { background: #f8fafc; color: #64748b; max-width: 45px; }
    .btn-card-more:hover { background: #f1f5f9; color: #475569; }
    .btn-card-danger { background: #fef2f2; color: #ef4444; max-width: 45px; }
    .btn-card-danger:hover { background: #fee2e2; }

    .empty-state { text-align: center; padding: 4rem 2rem; }
    .empty-state i { font-size: 3rem; color: #cbd5e1; margin-bottom: 1rem; }
</style>

<div class="container-fluid py-2">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h2 class="h4 mb-1 fw-bold text-dark">Mis Proveedores</h2>
            <p class="text-muted mb-0" style="font-size: 0.9rem;">
                Gestiona la información de contacto y compras.
                <span class="badge bg-primary rounded-pill ms-2"><?= count($proveedores) ?> proveedores</span>
            </p>
        </div>
        <button class="btn btn-primary rounded-3 shadow-sm" style="background-color: #2563eb; border: none; font-weight: 500;" data-bs-toggle="modal" data-bs-target="#modalProveedor">
            <i class="fas fa-plus me-2"></i> Añadir Proveedor
        </button>
    </div>

    <!-- Buscador -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 p-2" style="background: white;">
        <div class="input-group">
            <span class="input-group-text bg-white border-0"><i class="fas fa-search text-muted"></i></span>
            <input type="text" id="buscadorProveedores" class="form-control border-0" style="box-shadow: none;" placeholder="Buscar por nombre, empresa o NIT...">
        </div>
    </div>

    <!-- Grid Proveedores -->
    <div class="row g-4" id="gridProveedores">
        <?php if (!empty($proveedores)): ?>
            <?php 
            $colores = ['#3b82f6', '#ea580c', '#ef4444', '#10b981', '#8b5cf6', '#06b6d4'];
            $fondos = ['#eff6ff', '#fff7ed', '#fee2e2', '#ecfdf5', '#f5f3ff', '#ecfeff'];
            $iconos_prov = ['fa-truck', 'fa-truck-moving', 'fa-box-open', 'fa-dolly', 'fa-warehouse', 'fa-shipping-fast'];
            foreach ($proveedores as $i => $prov): 
                $idx = $i % count($colores);
            ?>
            <div class="col-12 col-md-6 col-xl-4 proveedor-item" data-nombre="<?= strtolower($prov['nombre'] ?? '') ?>" data-nit="<?= strtolower($prov['nit'] ?? '') ?>">
                <div class="provider-card">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="provider-icon" style="color: <?= $colores[$idx] ?>; background: <?= $fondos[$idx] ?>;">
                            <i class="fas <?= $iconos_prov[$idx] ?>"></i>
                        </div>
                        <span class="provider-status <?= ($prov['activo'] ?? 1) ? 'status-activo' : 'status-inactivo' ?>">
                            <?= ($prov['activo'] ?? 1) ? 'Activo' : 'Inactivo' ?>
                        </span>
                    </div>
                    <h5 class="fw-bold text-dark mb-1"><?= htmlspecialchars($prov['nombre']) ?></h5>
                    <p class="text-muted small mb-3">NIT: <?= htmlspecialchars($prov['nit'] ?? '-') ?></p>
                    
                    <div class="d-flex flex-column gap-2 mb-4 flex-grow-1">
                        <div class="d-flex align-items-center gap-2 text-muted" style="font-size: 0.85rem;">
                            <i class="fas fa-user" style="width: 15px; color: <?= $colores[$idx] ?>;"></i> 
                            Contacto: <?= htmlspecialchars($prov['contacto'] ?? '-') ?>
                        </div>
                        <div class="d-flex align-items-center gap-2 text-muted" style="font-size: 0.85rem;">
                            <i class="fas fa-phone" style="width: 15px; color: <?= $colores[$idx] ?>;"></i> 
                            <?= htmlspecialchars($prov['telefono'] ?? '-') ?>
                        </div>
                        <div class="d-flex align-items-center gap-2 text-muted" style="font-size: 0.85rem;">
                            <i class="fas fa-envelope" style="width: 15px; color: <?= $colores[$idx] ?>;"></i> 
                            <?= htmlspecialchars($prov['email'] ?? '-') ?>
                        </div>
                        <?php if (isset($prov['total_productos'])): ?>
                        <div class="mt-2">
                            <span class="badge bg-light text-dark border"><?= $prov['total_productos'] ?> productos</span>
                        </div>
                        <?php endif; ?>
                    </div>

                    <div class="d-flex gap-2 mt-auto">
                        <button class="btn-card-action btn-card-detail"
                            onclick="Swal.fire({title:'<?= htmlspecialchars($prov['nombre'], ENT_QUOTES) ?>', html:'<p><strong>NIT:</strong> <?= htmlspecialchars($prov['nit'] ?? '-', ENT_QUOTES) ?></p><p><strong>Contacto:</strong> <?= htmlspecialchars($prov['contacto'] ?? '-', ENT_QUOTES) ?></p><p><strong>Teléfono:</strong> <?= htmlspecialchars($prov['telefono'] ?? '-', ENT_QUOTES) ?></p><p><strong>Email:</strong> <?= htmlspecialchars($prov['email'] ?? '-', ENT_QUOTES) ?></p><p><strong>Dirección:</strong> <?= htmlspecialchars($prov['direccion'] ?? '-', ENT_QUOTES) ?></p>', icon:'info', confirmButtonColor:'#2563eb', customClass:{popup:'rounded-4'}})">
                            Ver Detalles
                        </button>
                        <button class="btn-card-action btn-card-danger" title="Desactivar"
                            onclick="confirmarAccion('', '<?= htmlspecialchars($prov['nombre'], ENT_QUOTES) ?>', 'desactivar')">
                            <i class="fas fa-ban"></i>
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="empty-state">
                    <i class="fas fa-truck"></i>
                    <h5 class="text-muted fw-bold">Sin proveedores registrados</h5>
                    <p class="text-muted">Añade tu primer proveedor para comenzar.</p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php 
echo '</section></main></div>';
require_once __DIR__ . '/../layouts/footer.php'; 
?>

<script>
// Buscador en tiempo real para tarjetas de proveedores
document.getElementById('buscadorProveedores').addEventListener('input', function() {
    const termino = this.value.toLowerCase();
    document.querySelectorAll('.proveedor-item').forEach(function(item) {
        const nombre = item.dataset.nombre || '';
        const nit = item.dataset.nit || '';
        item.style.display = (nombre.includes(termino) || nit.includes(termino)) ? '' : 'none';
    });
});
</script>
