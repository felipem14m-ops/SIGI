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

$titulo = "Gestión de Categorías";

// =========================================================================
// CONEXIÓN A MODELOS
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
    :root {
        --c-primary: #2563eb;
        --c-border: #e2e8f0;
        --c-surface: #fff;
        --c-muted: #64748b;
        --c-text: #0f172a;
        --c-bg: #f8fafc;
    }

    /* Animaciones */
    @keyframes modalFadeIn {
        from { opacity: 0; transform: translateY(-20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes overlayFadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
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
    .module-banner::after {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 400px;
        height: 400px;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 70%);
        border-radius: 50%;
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
        background: rgba(255, 255, 255, 0.15);
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 50px;
        padding: 0.5rem 1.2rem;
        display: flex;
        align-items: center;
        gap: 0.6rem;
        backdrop-filter: blur(8px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .btn-action-banner {
        background: #fff;
        color: #2563eb;
        border: none;
        padding: 0.8rem 1.5rem;
        border-radius: 14px;
        font-weight: 700;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    .btn-action-banner:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.2);
        background: #f8fafc;
    }

    /* GRID CARDS */
    .category-card {
        background: var(--c-surface);
        border-radius: 16px;
        border: 1px solid var(--c-border);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, .04);
        padding: 1.5rem;
        text-align: center;
        position: relative;
        height: 100%;
        display: flex;
        flex-direction: column;
        transition: transform .2s, box-shadow .2s;
    }

    .category-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 30px rgba(0, 0, 0, .07);
    }

    .category-icon {
        width: 64px;
        height: 64px;
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        margin: 0 auto 1.25rem;
    }

    .category-title {
        font-weight: 700;
        color: #1e293b;
        font-size: 1.1rem;
        margin-bottom: .4rem;
    }

    .tag-badge { padding: 0.25rem 0.6rem; border-radius: 6px; font-size: 0.72rem; font-weight: 700; display: inline-flex; align-items: center; gap: 0.25rem; }
    .tag-cat { background: #eff6ff; color: #3b82f6; }
    .tag-activo { background: #dcfce7; color: #16a34a; }
    .tag-inactivo { background: #f1f5f9; color: #64748b; }

    .btn-card-action { flex: 1; padding: 0.55rem; border-radius: 10px; font-size: 0.83rem; font-weight: 600; border: none; transition: all 0.2s; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 5px; }
    .btn-card-edit { background: #eef2ff; color: #6366f1; }
    .btn-card-danger { background: #fef2f2; color: #ef4444; max-width: 44px; }

    /* TOGGLE VISTA */
    #vistaTablaCont { display: none; }
    .btn-vista {
        width: 38px; height: 38px; border-radius: 10px; border: 1px solid rgba(255, 255, 255, .3);
        background: rgba(255, 255, 255, .15); color: #fff; cursor: pointer; transition: all 0.2s;
        display: flex; align-items: center; justify-content: center;
    }
    .btn-vista.activo { background: #fff !important; color: #2563eb !important; border-color: #fff !important; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15); }

    .table-custom thead th { background: #f8fafc; color: #64748b; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid #e2e8f0; padding: 1rem; }
    .table-custom tbody td { padding: 1rem; vertical-align: middle; font-size: 0.88rem; border-bottom: 1px solid #f1f5f9; }

    /* DATATABLES PAGINATION PREMIUM */
    .dataTables_paginate {
        margin-top: 1.5rem !important;
        padding: 1rem !important;
        display: flex;
        justify-content: flex-end;
        gap: 5px;
    }
    .dataTables_paginate .paginate_button {
        background: #f1f5f9 !important;
        border: none !important;
        border-radius: 8px !important;
        padding: 0.5rem 1rem !important;
        font-weight: 700 !important;
        color: #475569 !important;
        cursor: pointer;
        transition: 0.2s;
    }
    .dataTables_paginate .paginate_button:hover { background: #e2e8f0 !important; }
    .dataTables_paginate .paginate_button.current {
        background: #2563eb !important;
        color: #fff !important;
        box-shadow: 0 4px 10px rgba(37, 99, 235, 0.2);
    }
</style>

    <?php if (!empty($_error_bd)): ?>
        <div class="alert alert-danger rounded-4 shadow-sm mb-4">
            <i class="fas fa-exclamation-triangle me-2"></i> <?= $_error_bd ?>
        </div>
    <?php endif; ?>

    <!-- Banner de módulo -->
    <div class="module-banner">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-4 position-relative" style="z-index: 2;">
            <div>
                <span class="banner-label" style="color: #fff;">Gestión Comercial</span>
                <h1 class="fw-bold m-0 text-white" style="font-family: 'Poppins', sans-serif; letter-spacing: -1px;">Catálogo de Categorías</h1>
                <p class="m-0 text-white opacity-90 fw-light">Organiza las clasificaciones de tus productos de forma profesional.</p>
            </div>
            <div class="d-flex align-items-center gap-3 flex-wrap">
                <div class="stat-pill-premium text-white">
                    <i class="fas fa-layer-group" style="font-size: 0.9rem; opacity: 0.9;"></i>
                    <div class="d-flex flex-column" style="line-height: 1;">
                        <span style="font-size: 1rem; font-weight: 800; color: #fff;"><?= count($categorias) ?></span>
                        <span style="font-size: 0.65rem; font-weight: 700; text-transform: uppercase; opacity: 0.8; letter-spacing: 0.3px; color: #fff;">Categorías</span>
                    </div>
                </div>
                <div class="d-flex gap-2 mx-1">
                    <button id="btnVistaGrid" class="btn-vista activo" title="Vista tarjetas"><i class="fas fa-th-large"></i></button>
                    <button id="btnVistaTabla" class="btn-vista" title="Vista tabla"><i class="fas fa-list"></i></button>
                </div>
                <button class="btn fw-bold px-4 py-2 rounded-3" style="background:#fff;color:#2563eb;border:none;box-shadow:0 4px 14px rgba(0,0,0,.15);" onclick="abrirModalCategoria()">
                    <i class="fas fa-plus me-2"></i> Nueva Categoría
                </button>
            </div>
        </div>
    </div>

    <!-- Filtros y Buscador -->
    <div class="module-card p-4 mb-4">
        <div class="row g-3 align-items-end">
            <div class="col-md-5">
                <label class="form-label small fw-bold text-muted text-uppercase mb-2" style="letter-spacing: 0.5px;">Nombre de Categoría</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0" style="border-radius: 12px 0 0 12px;"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" id="filtroNombre" class="form-control bg-light border-start-0 shadow-none" placeholder="Ej. Calzado Deportivo..." style="border-radius: 0 12px 12px 0;">
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold text-muted text-uppercase mb-2" style="letter-spacing: 0.5px;">Estado</label>
                <select id="filtroEstado" class="form-select bg-light shadow-none" style="border-radius: 12px;">
                    <option value="">Todos los estados</option>
                    <option value="1">Solo Activas</option>
                    <option value="0">Solo Inactivas</option>
                </select>
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="button" onclick="aplicarFiltros()" class="btn fw-bold px-4 rounded-pill shadow-sm text-white w-100" style="background: linear-gradient(135deg, #1e3a8a, #2563eb); border: none;">
                    <i class="fas fa-filter me-2"></i> Aplicar Filtros
                </button>
                <button id="btnLimpiar" class="btn btn-light px-4 fw-bold rounded-pill border text-muted w-100">
                    <i class="fas fa-undo me-2"></i> Limpiar
                </button>
            </div>
        </div>
        <div id="txtResultados" class="text-muted mt-3 small fw-bold"><i class="fas fa-info-circle me-1"></i> Mostrando todas las categorías</div>
    </div>

    <!-- Grid de Categorías -->
    <div id="vistaGridCont">
        <div class="row g-4" id="gridCategorias">
            <?php if (!empty($categorias)): ?>
                <?php
                $paleta = [
                    ['bg' => '#fef3c7', 'color' => '#d97706', 'icon' => 'fa-shopping-basket'],
                    ['bg' => '#e0f2fe', 'color' => '#0284c7', 'icon' => 'fa-cheese'],
                    ['bg' => '#fee2e2', 'color' => '#ef4444', 'icon' => 'fa-wine-bottle'],
                    ['bg' => '#dcfce7', 'color' => '#16a34a', 'icon' => 'fa-leaf'],
                    ['bg' => '#f5f3ff', 'color' => '#7c3aed', 'icon' => 'fa-box'],
                ];
                foreach ($categorias as $i => $cat):
                    $p = $paleta[$i % count($paleta)];
                    $icon = $cat['icono'] ?? $p['icon'];
                    $activa = (int)($cat['activa'] ?? 1);
                ?>
                    <div class="col-12 col-md-6 col-xl-4 categoria-item" 
                         data-nombre="<?= strtolower(htmlspecialchars($cat['nombre'])) ?>"
                         data-estado="<?= $activa ?>">
                        <div class="category-card">
                            <span class="tag-badge <?= $activa ? 'tag-activo' : 'tag-inactivo' ?> shadow-sm" style="position:absolute; top:1rem; right:1rem; z-index: 10;">
                                <?= $activa ? 'Activa' : 'Inactiva' ?>
                            </span>

                            <!-- Imagen de Categoría -->
                            <div class="category-visual-container mb-3" style="height: 140px; border-radius: 12px; overflow: hidden; background: <?= $p['bg'] ?>; display: flex; align-items: center; justify-content: center; position: relative; border: 1px solid #f1f5f9;">
                                <?php if (!empty($cat['imagen'])): ?>
                                    <img src="<?= $base_url ?>IMG/categorias/<?= htmlspecialchars($cat['imagen']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                                <?php else: ?>
                                    <div class="text-center opacity-40">
                                        <i class="fas fa-image fa-3x" style="color: <?= $p['color'] ?>;"></i>
                                        <div class="small fw-bold mt-1" style="color: <?= $p['color'] ?>;">SIN IMAGEN</div>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <h3 class="category-title"><?= htmlspecialchars($cat['nombre']) ?></h3>
                            <p class="category-desc"><?= htmlspecialchars($cat['descripcion'] ?? 'Sin descripción.') ?></p>

                            <div class="mb-3">
                                <span class="tag-badge tag-cat">
                                    <i class="fas fa-box me-1"></i>
                                    <?= $cat['total_productos'] ?? 0 ?> productos
                                </span>
                            </div>

                            <div class="d-flex gap-2 mt-auto">
                                <button class="btn-card-action btn-card-edit" onclick="editarCategoria(<?= $cat['id_categoria'] ?>, '<?= htmlspecialchars($cat['nombre'], ENT_QUOTES) ?>', '<?= htmlspecialchars($cat['descripcion'] ?? '', ENT_QUOTES) ?>', '<?= htmlspecialchars($cat['imagen'] ?? '', ENT_QUOTES) ?>')">
                                    <i class="fas fa-edit"></i> Editar
                                </button>
                                <button class="btn-card-action btn-card-danger" onclick="confirmarAccion('<?= $base_url ?>controllers/CategoriaController.php?accion=<?= $activa ? 'desactivar' : 'activar' ?>&id=<?= $cat['id_categoria'] ?>', '<?= htmlspecialchars($cat['nombre'], ENT_QUOTES) ?>', '<?= $activa ? 'desactivar' : 'activar' ?>', <?= $cat['total_productos'] ?? 0 ?>)">
                                    <i class="fas fa-<?= $activa ? 'ban' : 'check' ?>"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <i class="fas fa-tags opacity-20 fa-4x mb-3"></i>
                    <h5 class="fw-bold">No hay categorías</h5>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Tabla de Categorías -->
    <div id="vistaTablaCont">
        <div class="module-card overflow-hidden p-3">
            <div class="table-responsive">
                <table id="tablaCategorias" class="table table-hover table-custom w-100">
                    <thead>
                        <tr>
                            <th>Categoría</th>
                            <th>Descripción</th>
                            <th class="text-center">Productos</th>
                            <th class="text-center">Estado</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categorias as $cat): 
                            $activa = (int)($cat['activa'] ?? 1);
                        ?>
                            <tr class="categoria-item-tr" data-nombre="<?= strtolower(htmlspecialchars($cat['nombre'])) ?>" data-estado="<?= $activa ?>">
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div style="width:40px; height:40px; border-radius:8px; background:#f8fafc; display:flex; align-items:center; justify-content:center; color:#64748b; overflow: hidden; border: 1px solid #eee;">
                                            <?php if (!empty($cat['imagen'])): ?>
                                                <img src="<?= $base_url ?>IMG/categorias/<?= htmlspecialchars($cat['imagen']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                                            <?php else: ?>
                                                <i class="fas fa-image opacity-50"></i>
                                            <?php endif; ?>
                                        </div>
                                        <span class="fw-bold"><?= htmlspecialchars($cat['nombre']) ?></span>
                                    </div>
                                </td>
                                <td class="small text-muted"><?= htmlspecialchars($cat['descripcion'] ?? '-') ?></td>
                                <td class="text-center"><span class="badge bg-light text-dark rounded-pill px-3"><?= $cat['total_productos'] ?? 0 ?></span></td>
                                <td class="text-center">
                                    <span class="tag-badge <?= $activa ? 'tag-activo' : 'tag-inactivo' ?>">
                                        <?= $activa ? 'Activa' : 'Inactiva' ?>
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-1">
                                        <button class="btn btn-sm btn-light border rounded-3" onclick="editarCategoria(<?= $cat['id_categoria'] ?>, '<?= htmlspecialchars($cat['nombre'], ENT_QUOTES) ?>', '<?= htmlspecialchars($cat['descripcion'] ?? '', ENT_QUOTES) ?>', '<?= htmlspecialchars($cat['imagen'] ?? '', ENT_QUOTES) ?>')"><i class="fas fa-edit"></i></button>
                                        <button class="btn btn-sm btn-light border rounded-3 text-<?= $activa ? 'danger' : 'success' ?>" onclick="confirmarAccion('<?= $base_url ?>controllers/CategoriaController.php?accion=<?= $activa ? 'desactivar' : 'activar' ?>&id=<?= $cat['id_categoria'] ?>', '<?= htmlspecialchars($cat['nombre'], ENT_QUOTES) ?>', '<?= $activa ? 'desactivar' : 'activar' ?>', <?= $cat['total_productos'] ?? 0 ?>)"><i class="fas fa-<?= $activa ? 'ban' : 'check' ?>"></i></button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
echo '</section></main></div>';
require_once __DIR__ . '/../layouts/footer.php';
?>

<script>
    const BASE_URL_CAT = '<?= $base_url ?>';

    function _buildModalCategoria() {
        const prev = document.getElementById('modalCatDyn');
        const prevOv = document.getElementById('modalCatOvDyn');
        if (prev) prev.remove();
        if (prevOv) prevOv.remove();

        const overlay = document.createElement('div');
        overlay.id = 'modalCatOvDyn';
        overlay.style.cssText = 'position:fixed;top:0;right:0;bottom:0;left:0;background:rgba(15,23,42,0.6);backdrop-filter:blur(4px);z-index:9998;animation:overlayFadeIn 0.3s ease;';
        overlay.onclick = cerrarModalCategoria;
        document.body.appendChild(overlay);

        const wrap = document.createElement('div');
        wrap.id = 'modalCatDyn';
        wrap.style.cssText = 'position:fixed;top:0;right:0;bottom:0;left:0;z-index:9999;display:flex;align-items:center;justify-content:center;padding:1rem;overflow-y:auto;pointer-events:none;';

        const content = document.createElement('div');
        content.style.cssText = 'width:100%; max-width:850px; pointer-events:auto;';
        content.innerHTML = `
        <div style="background:#fff;border-radius:24px;overflow:hidden;width:100%;max-width:850px;box-shadow:0 30px 70px rgba(0,0,0,.3);margin:auto;animation: modalFadeIn 0.3s ease-out;">
            <div style="background:linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);padding:1.5rem 2rem;display:flex;justify-content:space-between;align-items:center;color:#fff;">
                <div class="d-flex align-items-center gap-2">
                    <div style="background:rgba(255,255,255,0.2);width:38px;height:38px;border-radius:10px;display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-layer-group text-white"></i>
                    </div>
                    <h5 id="modalCatTitulo" class="m-0 fw-bold text-white" style="letter-spacing:-0.5px; font-size:1.4rem;">Gestión de Categoría</h5>
                </div>
                <button type="button" onclick="cerrarModalCategoria()" class="btn-close btn-close-white" style="opacity:0.8; transition:0.2s;"></button>
            </div>
            
            <form id="formCategoriaDyn" method="POST" enctype="multipart/form-data" class="p-4 px-md-5 needs-validation" novalidate>
                <input type="hidden" id="c_id" name="id_categoria">
                <input type="hidden" id="c_imagen_actual" name="imagen_actual">
                
                <div class="row g-4">
                    <div class="col-12">
                        <label class="form-label small fw-bold text-muted text-uppercase mb-1"><i class="fas fa-tag me-1"></i> Nombre de la Categoría *</label>
                        <input type="text" id="c_nombre" name="nombre" class="form-control rounded-3" placeholder="Ej: Electrónica, Hogar..." required style="padding: 0.6rem 1rem;">
                        <div class="invalid-feedback">El nombre es obligatorio.</div>
                    </div>

                    <div class="col-md-7">
                        <label class="form-label small fw-bold text-muted text-uppercase mb-1"><i class="fas fa-align-left me-1"></i> Descripción</label>
                        <textarea id="c_descripcion" name="descripcion" class="form-control rounded-3" rows="5" placeholder="Describe los productos de esta categoría..." style="padding: 0.6rem 1rem; resize:none;"></textarea>
                    </div>
                    
                    <div class="col-md-5">
                        <label class="form-label small fw-bold text-muted text-uppercase mb-1"><i class="fas fa-image me-1"></i> Imagen</label>
                        <div class="border rounded-3 p-2 d-flex flex-column align-items-center justify-content-center" style="min-height: 115px; background: #fcfdfe; border-style: dashed !important; border-width: 2px !important;">
                            <input type="file" id="c_imagen" name="imagen" class="form-control form-control-sm border-0 bg-transparent mb-2" accept="image/*">
                            <div id="c_preview_wrap" style="display:none;" class="mt-1">
                                <img id="c_preview_img" src="" style="width:80px; height:80px; object-fit:cover; border-radius:12px; box-shadow:0 4px 10px rgba(0,0,0,0.1);">
                            </div>
                            <div id="c_no_image_text" class="text-muted small py-3"><i class="fas fa-cloud-arrow-up me-1"></i> Subir imagen</div>
                        </div>
                    </div>
                </div>
                
                <div class="text-end mt-5 pb-2">
                    <button type="button" onclick="cerrarModalCategoria()" class="btn btn-light border-0 px-4 py-2 fw-bold me-2 rounded-pill" style="color: #64748b;">Cancelar</button>
                    <button type="submit" class="btn btn-primary px-5 py-2 fw-bold rounded-pill shadow-sm" style="background:linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%); border:none;">
                        <i class="fas fa-save me-2"></i>Guardar Cambios
                    </button>
                </div>
            </form>
        </div>`;
        wrap.appendChild(content);
        document.body.appendChild(wrap);

        // Preview imagen
        document.getElementById('c_imagen').addEventListener('change', function() {
            const file = this.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = e => {
                document.getElementById('c_preview_img').src = e.target.result;
                document.getElementById('c_preview_wrap').style.display = 'block';
                document.getElementById('c_no_image_text').style.display = 'none';
            };
            reader.readAsDataURL(file);
        });

        document.getElementById('formCategoriaDyn').addEventListener('submit', function(e) {
            if (!this.checkValidity()) { e.preventDefault(); e.stopPropagation(); }
            this.classList.add('was-validated');
        });
    }

    function abrirModalCategoria() {
        _buildModalCategoria();
        document.getElementById('modalCatTitulo').innerText = 'Nueva Categoría';
        document.getElementById('formCategoriaDyn').action = BASE_URL_CAT + 'controllers/CategoriaController.php?accion=crear';
    }

    function editarCategoria(id, nom, desc, img) {
        _buildModalCategoria();
        document.getElementById('modalCatTitulo').innerText = 'Editar Categoría';
        document.getElementById('c_id').value = id;
        document.getElementById('c_nombre').value = nom;
        document.getElementById('c_descripcion').value = desc;
        document.getElementById('c_imagen_actual').value = img;
        if(img) {
            document.getElementById('c_preview_img').src = BASE_URL_CAT + 'IMG/categorias/' + img;
            document.getElementById('c_preview_wrap').style.display = 'block';
            document.getElementById('c_no_image_text').style.display = 'none';
        }
        document.getElementById('formCategoriaDyn').action = BASE_URL_CAT + 'controllers/CategoriaController.php?accion=editar';
    }

    function cerrarModalCategoria() {
        document.getElementById('modalCatDyn')?.remove();
        document.getElementById('modalCatOvDyn')?.remove();
    }

    // Toggle Vista
    const btnGrid = document.getElementById('btnVistaGrid');
    const btnTabla = document.getElementById('btnVistaTabla');
    const contGrid = document.getElementById('vistaGridCont');
    const contTabla = document.getElementById('vistaTablaCont');
    let tablaInit = false;

    btnGrid?.addEventListener('click', () => {
        contGrid.style.display = 'block'; contTabla.style.display = 'none';
        btnGrid.classList.add('activo'); btnTabla.classList.remove('activo');
    });

    btnTabla?.addEventListener('click', () => {
        contGrid.style.display = 'none'; contTabla.style.display = 'block';
        btnGrid.classList.remove('activo'); btnTabla.classList.add('activo');
        if (!tablaInit && typeof $.fn.DataTable !== 'undefined') {
            $('#tablaCategorias').DataTable({ 
                searching: true,
                order: [[0, 'asc']], 
                dom: 'rt<"px-4 py-4 d-flex justify-content-between align-items-center"ip>',
                language: { 
                    lengthMenu: "Mostrar _MENU_", 
                    info: "_TOTAL_ categorías", 
                    paginate: { previous: "Ant.", next: "Sig." } 
                } 
            });
            tablaInit = true;
        }
    });

    // Filtros
    // Filtrado Premium en Tiempo Real con DataTables API para la Vista de Tabla
    $.fn.dataTable.ext.search.push(
        function(settings, data, dataIndex, rowData, counter) {
            if (settings.nTable.id !== 'tablaCategorias') return true;
            
            const txt = document.getElementById('filtroNombre').value.toLowerCase().trim();
            const est = document.getElementById('filtroEstado').value;
            
            const rowEl = $(settings.aoData[dataIndex].nTr);
            const dataNom = (rowEl.data('nombre') || '').toString().toLowerCase();
            const dataEst = (rowEl.data('estado') || '').toString();

            const ok = (!txt || dataNom.includes(txt)) && (!est || dataEst === est);
            return ok;
        }
    );

    function aplicarFiltros() {
        const txt = document.getElementById('filtroNombre').value.toLowerCase().trim();
        const est = document.getElementById('filtroEstado').value;
        
        // 1. Filtrar Vista Grid (Tarjetas)
        const gridItems = document.querySelectorAll('.categoria-item');
        let visGrid = 0;
        gridItems.forEach(el => {
            const dataNom = (el.dataset.nombre || '').toLowerCase();
            const dataEst = (el.dataset.estado || '');
            const ok = (!txt || dataNom.includes(txt)) && (!est || dataEst === est);
            el.style.display = ok ? 'block' : 'none';
            if (ok) visGrid++;
        });

        // 2. Filtrar Vista Tabla (DataTables)
        let visTabla = visGrid;
        if (typeof $.fn.DataTable !== 'undefined' && $.fn.DataTable.isDataTable('#tablaCategorias')) {
            const table = $('#tablaCategorias').DataTable();
            table.draw();
            visTabla = table.page.info().recordsDisplay;
        } else {
            // Fallback si la tabla no se ha inicializado todavía
            const tableRows = document.querySelectorAll('.categoria-item-tr');
            tableRows.forEach(el => {
                const dataNom = (el.dataset.nombre || '').toLowerCase();
                const dataEst = (el.dataset.estado || '');
                const ok = (!txt || dataNom.includes(txt)) && (!est || dataEst === est);
                el.style.display = ok ? '' : 'none';
            });
        }
        
        // Actualizar contador
        const isTablaActive = document.getElementById('btnVistaTabla').classList.contains('activo');
        const count = isTablaActive ? visTabla : visGrid;
        document.getElementById('txtResultados').innerText = `Encontradas ${count} categorías`;
    }
    ['filtroNombre', 'filtroEstado'].forEach(id => {
        document.getElementById(id)?.addEventListener('input', aplicarFiltros);
        document.getElementById(id)?.addEventListener('change', aplicarFiltros);
    });
    document.getElementById('btnLimpiar')?.addEventListener('click', () => {
        document.getElementById('filtroNombre').value = '';
        document.getElementById('filtroEstado').value = '';
        aplicarFiltros();
    });

    function confirmarAccion(url, nom, acc, totalProductos = 0) {
        if (acc === 'desactivar' && totalProductos > 0) {
            if (typeof Swal !== 'undefined') {
                Swal.fire('No permitido', 'No se puede desactivar la categoría porque tiene productos asociados.', 'error');
            } else {
                alert('No se puede desactivar la categoría porque tiene productos asociados.');
            }
            return;
        }

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: `¿${acc.charAt(0).toUpperCase() + acc.slice(1)} categoría?`,
                text: nom,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: acc === 'desactivar' ? '#dc2626' : '#16a34a',
                confirmButtonText: 'Sí, continuar',
                cancelButtonText: 'Cancelar'
            }).then(r => { if (r.isConfirmed) window.location.href = url; });
        } else if (confirm(`¿${acc} ${nom}?`)) window.location.href = url;
    }

    aplicarFiltros();
</script>