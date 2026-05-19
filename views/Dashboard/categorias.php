<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) { header("Location: Usuario/login.php"); exit; }

$titulo = "Gestión de Categorías";

// =========================================================================
// CONEXIÓN A MODELOS (Defensivo: sidebar siempre se renderiza)
// =========================================================================
$categorias = [];
$_error_bd = '';

try {
    require_once __DIR__ . '/../../config/database.php';
    require_once __DIR__ . '/../../models/Categoria.php';
    $db = (new Database())->conectar();
    $categorias = (new Categoria($db))->listarTodas();
} catch (Throwable $e) {
    $_error_bd = 'No se pudieron cargar los datos. Verifique la base de datos.';
    error_log("[SIGI] categorias.php Error: " . $e->getMessage());
}

require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidebar.php';
?>

<style>
    /* =====================================================
       DESIGN TOKENS
       ===================================================== */
    .module-card { background: #fff; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); }
    .bg-soft-success { background: #dcfce7; color: #16a34a; }
    .bg-soft-danger { background: #fee2e2; color: #dc2626; }

    /* =====================================================
       CATEGORY CARDS
       ===================================================== */
    .category-card {
        background: #fff; border-radius: 16px; border: 1px solid #e2e8f0;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.04);
        padding: 1.5rem; text-align: center; position: relative;
        height: 100%; display: flex; flex-direction: column;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .category-card:hover { transform: translateY(-5px); box-shadow: 0 12px 30px rgba(0,0,0,0.07); }

    .category-icon { width: 64px; height: 64px; border-radius: 18px; display: flex; align-items: center; justify-content: center; font-size: 1.75rem; margin: 0 auto 1.25rem; }
    .category-title { font-weight: 700; color: #1e293b; font-size: 1.15rem; margin-bottom: 0.4rem; }
    .category-desc { font-size: 0.84rem; color: #64748b; flex-grow: 1; margin-bottom: 1.25rem; }
    .category-stat-pill { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 0.4rem 0.9rem; font-size: 0.82rem; font-weight: 600; color: #475569; display: inline-flex; align-items: center; gap: 0.4rem; margin-bottom: 1.25rem; }
    .category-status-badge { position: absolute; top: 1rem; right: 1rem; padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.73rem; font-weight: 700; }

    .btn-card-action { flex: 1; display: flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 0.55rem; border-radius: 10px; font-size: 0.83rem; font-weight: 600; border: none; transition: all 0.2s; text-decoration: none; cursor: pointer; }
    .btn-card-edit { background: #eef2ff; color: #6366f1; }
    .btn-card-edit:hover { background: #e0e7ff; color: #4f46e5; }
    .btn-card-danger { background: #fef2f2; color: #ef4444; max-width: 44px; }
    .btn-card-danger:hover { background: #fee2e2; }

    .empty-state { text-align: center; padding: 4rem 2rem; }
    .empty-state i { font-size: 3.5rem; color: #cbd5e1; margin-bottom: 1rem; }

    /* Form focus */
    .form-control:focus, .form-select:focus { border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37,99,235,0.1); background: #fff !important; }
</style>

<div class="container-fluid py-2">

    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h2 class="h4 mb-1 fw-bold text-dark">Catálogo de Categorías</h2>
            <p class="text-muted mb-0" style="font-size: 0.9rem;">
                Organiza las clasificaciones de tus productos.
                <span class="badge bg-primary rounded-pill ms-2"><?= count($categorias) ?> categorías</span>
            </p>
        </div>
        <button class="btn btn-primary rounded-3 shadow-sm px-3" style="background: #2563eb; border: none; font-weight: 500;" data-bs-toggle="modal" data-bs-target="#modalCategoria">
            <i class="fas fa-plus me-2"></i> Nueva Categoría
        </button>
    </div>

    <!-- Buscador rápido -->
    <div class="module-card p-2 mb-4">
        <div class="input-group">
            <span class="input-group-text bg-white border-0"><i class="fas fa-search text-muted"></i></span>
            <input type="text" id="buscadorCategorias" class="form-control border-0" style="box-shadow:none;" placeholder="Buscar categoría...">
        </div>
    </div>

    <!-- Grid de Categorías -->
    <div class="row g-4" id="gridCategorias">
        <?php if (!empty($categorias)): ?>
            <?php
            // Paletas de colores para los iconos de categorías
            $paleta = [
                ['bg' => '#fef3c7', 'color' => '#d97706', 'icon' => 'fa-shopping-basket'],
                ['bg' => '#e0f2fe', 'color' => '#0284c7', 'icon' => 'fa-cheese'],
                ['bg' => '#fee2e2', 'color' => '#ef4444', 'icon' => 'fa-wine-bottle'],
                ['bg' => '#dcfce7', 'color' => '#16a34a', 'icon' => 'fa-leaf'],
                ['bg' => '#f5f3ff', 'color' => '#7c3aed', 'icon' => 'fa-box'],
                ['bg' => '#ecfeff', 'color' => '#0e7490', 'icon' => 'fa-snowflake'],
                ['bg' => '#fff7ed', 'color' => '#ea580c', 'icon' => 'fa-pepper-hot'],
                ['bg' => '#f0fdf4', 'color' => '#15803d', 'icon' => 'fa-carrot'],
            ];
            foreach ($categorias as $i => $cat):
                $p = $paleta[$i % count($paleta)];
                // El modelo puede devolver icono y color propios de la BD
                $bg_icon = $cat['color'] ?? $p['bg'];
                $icon = $cat['icono'] ?? $p['icon'];
            ?>
            <div class="col-12 col-md-6 col-xl-4 categoria-item" data-nombre="<?= strtolower(htmlspecialchars($cat['nombre'])) ?>">
                <div class="category-card">
                    <!-- Badge estado -->
                    <span class="category-status-badge <?= ($cat['activo'] ?? 1) ? 'bg-soft-success' : 'bg-soft-danger' ?>">
                        <?= ($cat['activo'] ?? 1) ? 'Activa' : 'Inactiva' ?>
                    </span>

                    <!-- Ícono -->
                    <div class="category-icon" style="background: <?= $p['bg'] ?>; color: <?= $p['color'] ?>;">
                        <i class="fas <?= htmlspecialchars($icon) ?>"></i>
                    </div>

                    <h3 class="category-title"><?= htmlspecialchars($cat['nombre']) ?></h3>
                    <p class="category-desc"><?= htmlspecialchars($cat['descripcion'] ?? 'Sin descripción.') ?></p>

                    <!-- Conteo de productos (viene del JOIN en el modelo) -->
                    <div>
                        <span class="category-stat-pill">
                            <i class="fas fa-box text-primary"></i>
                            <?= $cat['total_productos'] ?? 0 ?> productos
                        </span>
                    </div>

                    <!-- Acciones -->
                    <div class="d-flex gap-2 mt-auto">
                        <button class="btn-card-action btn-card-edit"
                            onclick="editarCategoria(
                                <?= $cat['id_categoria'] ?>,
                                '<?= htmlspecialchars($cat['nombre'], ENT_QUOTES) ?>',
                                '<?= htmlspecialchars($cat['descripcion'] ?? '', ENT_QUOTES) ?>',
                                '<?= htmlspecialchars($cat['icono'] ?? $p['icon'], ENT_QUOTES) ?>'
                            )">
                            <i class="fas fa-pen"></i> Editar
                        </button>
                        <button class="btn-card-action btn-card-danger" title="Desactivar"
                            onclick="confirmarAccion(
                                '<?= $base_url ?>controllers/CategoriaController.php?accion=desactivar&id=<?= $cat['id_categoria'] ?>',
                                '<?= htmlspecialchars($cat['nombre'], ENT_QUOTES) ?>',
                                'desactivar'
                            )">
                            <i class="fas fa-ban"></i>
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="empty-state">
                    <i class="fas fa-tags"></i>
                    <h5 class="text-muted fw-bold">Sin categorías registradas</h5>
                    <p class="text-muted">Crea tu primera categoría para organizar los productos.</p>
                    <button class="btn btn-primary rounded-3 mt-2" data-bs-toggle="modal" data-bs-target="#modalCategoria">
                        <i class="fas fa-plus me-2"></i> Crear Categoría
                    </button>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- =========================================================================
   MODAL: CREAR / EDITAR CATEGORÍA
   ========================================================================= -->
<div class="modal fade" id="modalCategoria" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-0 px-4 pt-4 pb-0">
                <h5 class="modal-title fw-bold" id="modalCategoriaTitulo">
                    <i class="fas fa-tag me-2 text-primary"></i> Nueva Categoría
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formCategoria" method="POST" action="<?= $base_url ?>controllers/CategoriaController.php?accion=crear">
                <div class="modal-body px-4 py-3">
                    <input type="hidden" name="id_categoria" id="cat_id">

                    <div class="mb-3">
                        <label class="form-label fw-bold" style="font-size: 0.85rem; color: #475569;">Nombre</label>
                        <input type="text" name="nombre" id="cat_nombre" class="form-control bg-light rounded-3" placeholder="Ej: Abarrotes" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold" style="font-size: 0.85rem; color: #475569;">Descripción</label>
                        <textarea name="descripcion" id="cat_descripcion" class="form-control bg-light rounded-3" rows="2" placeholder="Describe esta categoría..."></textarea>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-bold" style="font-size: 0.85rem; color: #475569;">Clase Ícono (Font Awesome)</label>
                        <input type="text" name="icono" id="cat_icono" class="form-control bg-light rounded-3" placeholder="Ej: fa-shopping-basket" value="fa-tag">
                        <small class="text-muted">Usa clases de <a href="https://fontawesome.com" target="_blank">Font Awesome</a></small>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-0">
                    <button type="button" class="btn btn-light rounded-3 fw-bold px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary rounded-3 fw-bold px-4" style="background: #2563eb; border: none;">
                        <i class="fas fa-save me-1"></i> Guardar
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
// Buscador en tiempo real
document.getElementById('buscadorCategorias').addEventListener('input', function() {
    const t = this.value.toLowerCase();
    document.querySelectorAll('.categoria-item').forEach(el => {
        el.style.display = (el.dataset.nombre || '').includes(t) ? '' : 'none';
    });
});

// Rellenar modal para edición
function editarCategoria(id, nombre, descripcion, icono) {
    document.getElementById('modalCategoriaTitulo').innerHTML = '<i class="fas fa-edit me-2 text-primary"></i> Editar Categoría';
    document.getElementById('cat_id').value = id;
    document.getElementById('cat_nombre').value = nombre;
    document.getElementById('cat_descripcion').value = descripcion;
    document.getElementById('cat_icono').value = icono;
    document.getElementById('formCategoria').action = '<?= $base_url ?>controllers/CategoriaController.php?accion=editar';
    new bootstrap.Modal(document.getElementById('modalCategoria')).show();
}

// Resetear modal al cerrar
document.getElementById('modalCategoria').addEventListener('hidden.bs.modal', function() {
    document.getElementById('modalCategoriaTitulo').innerHTML = '<i class="fas fa-tag me-2 text-primary"></i> Nueva Categoría';
    document.getElementById('formCategoria').reset();
    document.getElementById('cat_id').value = '';
    document.getElementById('cat_icono').value = 'fa-tag';
    document.getElementById('formCategoria').action = '<?= $base_url ?>controllers/CategoriaController.php?accion=crear';
});
</script>
