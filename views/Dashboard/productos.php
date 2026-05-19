<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: ../Usuario/login.php");
    exit;
}

$titulo = "Consultar Productos";

$productos  = [];
$categorias = [];
$_error_bd  = '';

try {
    require_once __DIR__ . '/../../config/database.php';
    require_once __DIR__ . '/../../models/Producto.php';
    require_once __DIR__ . '/../../models/Categoria.php';
    $db         = (new Database())->conectar();
    $productos  = (new Producto($db))->listarActivos();
    $categorias = (new Categoria($db))->listarActivas();
} catch (Throwable $e) {
    $_error_bd = 'No se pudieron cargar los productos.';
    error_log("[SIGI] productos.php (empleado) Error: " . $e->getMessage());
}

require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidebar.php';
?>

<style>
    :root {
        --emp-primary: #2563eb;
        --emp-border: #e2e8f0;
        --emp-surface: #ffffff;
        --emp-muted: #64748b;
        --emp-text: #0f172a;
    }

    /* BANNER PREMIUM */
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

    .module-banner::before {
        content: '';
        position: absolute;
        top: -50px;
        right: -50px;
        width: 250px;
        height: 250px;
        background: rgba(255, 255, 255, 0.08);
        border-radius: 50%;
    }

    /* ── Buscador ── */
    .search-bar {
        background: var(--emp-surface);
        border-radius: 14px;
        border: 1px solid var(--emp-border);
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.04);
        padding: 0.5rem 1rem;
    }

    .search-bar input {
        border: none;
        background: transparent;
        font-size: 0.95rem;
        width: 100%;
    }

    .search-bar input:focus {
        outline: none;
    }

    /* ── Filtros de categoría ── */
    .cat-pill {
        border: 1.5px solid var(--emp-border);
        border-radius: 50px;
        padding: 0.35rem 1.2rem;
        font-size: 0.82rem;
        font-weight: 700;
        color: var(--emp-muted);
        background: var(--emp-surface);
        cursor: pointer;
        transition: all 0.2s;
        white-space: nowrap;
    }

    .cat-pill:hover,
    .cat-pill.activo {
        background: var(--emp-primary);
        color: #fff;
        border-color: var(--emp-primary);
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
    }

    /* ── Cards de producto ── */
    .prod-card {
        background: var(--emp-surface);
        border-radius: 20px;
        border: 1px solid var(--emp-border);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.04);
        overflow: hidden;
        display: flex;
        flex-direction: column;
        height: 100%;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .prod-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }

    .prod-img {
        background: #f8fafc;
        height: 180px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-bottom: 1px solid #f1f5f9;
        padding: 1rem;
    }

    .prod-img img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
        border-radius: 12px;
        transition: transform 0.3s;
    }

    .prod-card:hover .prod-img img {
        transform: scale(1.05);
    }

    .prod-img .no-img {
        font-size: 3.5rem;
        color: #cbd5e1;
    }

    .prod-body {
        padding: 1.25rem;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }

    .prod-name {
        font-weight: 700;
        color: var(--emp-text);
        font-size: 1rem;
        margin-bottom: 0.5rem;
        line-height: 1.4;
    }

    .tag {
        padding: 0.3rem 0.75rem;
        border-radius: 50px;
        font-size: 0.7rem;
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .tag-cat {
        background: #eff6ff;
        color: #2563eb;
    }

    .tag-ok {
        background: #dcfce7;
        color: #16a34a;
    }

    .tag-low {
        background: #fef3c7;
        color: #d97706;
    }

    .tag-out {
        background: #fee2e2;
        color: #dc2626;
    }

    .prod-price {
        font-size: 1.4rem;
        font-weight: 900;
        color: var(--emp-text);
        margin-top: auto;
        padding-top: 1rem;
    }

    .prod-code {
        font-size: 0.75rem;
        color: var(--emp-muted);
        margin-bottom: 0.75rem;
        font-family: monospace;
        letter-spacing: 0.5px;
    }

    /* ── Sin resultados ── */
    .empty-state {
        text-align: center;
        padding: 4rem 1rem;
        color: var(--emp-muted);
    }

    .empty-state i {
        font-size: 3.5rem;
        opacity: 0.2;
        margin-bottom: 1rem;
        display: block;
    }

    /* ── Paginación Premium ── */
    .page-btn {
        border: 1px solid var(--emp-border);
        background: var(--emp-surface);
        color: var(--emp-muted);
        border-radius: 10px;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        transition: all 0.2s;
    }

    .page-btn:hover:not(.disabled) {
        background: #f1f5f9;
        color: var(--emp-text);
    }

    .page-btn.active {
        background: var(--emp-primary);
        color: #fff;
        border-color: var(--emp-primary);
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
    }

    .page-btn.disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
</style>

<div class="container-fluid py-2">

    <!-- Banner de módulo Premium -->
    <div class="module-banner">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-4 position-relative" style="z-index: 2;">
            <div>
                <span class="banner-label">Catálogo Digital</span>
                <h2 class="fw-bold m-0 text-white" style="font-family: 'Poppins', sans-serif; letter-spacing: -1px;">Consultar Productos</h2>
                <p class="m-0 text-white opacity-90 fw-light">Explora el inventario disponible y verifica existencias en tiempo real.</p>
            </div>
            <div class="d-flex align-items-center gap-3 flex-wrap">
                <div class="stat-pill text-center text-white" style="background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.3); border-radius: 50px; padding: 0.4rem 1.2rem; backdrop-filter: blur(8px);">
                    <div class="fw-bold fs-5 lh-1"><?= count($productos) ?></div>
                    <div class="small fw-bold text-uppercase opacity-80" style="font-size: 0.6rem;">Items</div>
                </div>
                <a href="ventas.php" class="btn btn-white fw-bold px-4 py-2 rounded-3" style="background:#fff; color:#2563eb; border:none; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                    <i class="fas fa-cash-register me-2"></i> Ir a POS
                </a>
            </div>
        </div>
    </div>

    <?php if ($_error_bd): ?>
        <div class="alert alert-warning rounded-3 mb-4"><i class="fas fa-exclamation-triangle me-2"></i><?= htmlspecialchars($_error_bd) ?></div>
    <?php endif; ?>

    <!-- Buscador -->
    <div class="search-bar d-flex align-items-center px-3 py-2 mb-3">
        <i class="fas fa-search text-muted me-2"></i>
        <input type="text" id="buscador" class="form-control" placeholder="Buscar por nombre o código...">
    </div>

    <!-- Filtros de categoría -->
    <div class="d-flex gap-2 mb-4 flex-wrap">
        <button class="cat-pill activo" data-cat="">Todos</button>
        <?php foreach ($categorias as $cat): ?>
            <button class="cat-pill" data-cat="<?= $cat['id_categoria'] ?>">
                <?= htmlspecialchars($cat['nombre']) ?>
            </button>
        <?php endforeach; ?>
    </div>

    <!-- Grid de productos -->
    <div class="row g-4" id="gridProductos">
        <?php if (!empty($productos)): ?>
            <?php foreach ($productos as $p): ?>
                <?php
                if ($p['stock_actual'] <= 0) {
                    $tag_cls = 'tag-out';
                    $tag_txt = 'Agotado';
                } elseif ($p['stock_actual'] <= $p['stock_minimo']) {
                    $tag_cls = 'tag-low';
                    $tag_txt = $p['stock_actual'] . ' und · Bajo';
                } else {
                    $tag_cls = 'tag-ok';
                    $tag_txt = $p['stock_actual'] . ' und';
                }
                ?>
                <div class="col-6 col-md-4 col-xl-3 prod-item"
                    data-nombre="<?= strtolower(htmlspecialchars($p['nombre'])) ?>"
                    data-codigo="<?= strtolower(htmlspecialchars($p['codigoUnico'])) ?>"
                    data-cat="<?= $p['id_categoria'] ?>">
                    <div class="prod-card">
                        <div class="prod-img">
                            <?php if (!empty($p['imagen'])): ?>
                                <img src="<?= $base_url ?>IMG/productos/<?= htmlspecialchars($p['imagen']) ?>" alt="<?= htmlspecialchars($p['nombre']) ?>">
                            <?php else: ?>
                                <i class="fas fa-box-open no-img"></i>
                            <?php endif; ?>
                        </div>
                        <div class="prod-body">
                            <div class="prod-code"><?= htmlspecialchars($p['codigoUnico']) ?></div>
                            <div class="prod-name"><?= htmlspecialchars($p['nombre']) ?></div>
                            <div class="d-flex flex-wrap gap-1 mb-2">
                                <span class="tag tag-cat"><i class="fas fa-tag"></i><?= htmlspecialchars($p['nombre_categoria'] ?? 'Sin cat.') ?></span>
                                <span class="tag <?= $tag_cls ?>"><i class="fas fa-circle" style="font-size:.4rem;"></i><?= $tag_txt ?></span>
                            </div>
                            <div class="prod-price">$<?= number_format($p['precio_venta'], 0, ',', '.') ?></div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="empty-state">
                    <i class="fas fa-box-open"></i>
                    <h5 class="fw-bold">Sin productos disponibles</h5>
                    <p>No hay productos activos en el catálogo.</p>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Contenedor de Paginación -->
    <div id="pagination-container" class="d-flex justify-content-center gap-2 mt-5 mb-3" style="display: none !important;"></div>

    <!-- Sin resultados de búsqueda -->
    <div id="sinResultados" class="empty-state" style="display:none;">
        <i class="fas fa-search"></i>
        <h5 class="fw-bold">Sin resultados</h5>
        <p>No se encontraron productos con ese criterio.</p>
    </div>

</div>

<?php
echo '</section></main></div>';
require_once __DIR__ . '/../layouts/footer.php';
?>

<script>
    // =====================================================
    // BUSCADOR + FILTRO POR CATEGORÍA
    // =====================================================
    const items = document.querySelectorAll('.prod-item');
    const sinRes = document.getElementById('sinResultados');
    const buscador = document.getElementById('buscador');
    const pagContainer = document.getElementById('pagination-container');
    let catActiva = '';

    const itemsPerPage = 12;
    let currentPage = 1;
    let filteredItems = [];

    function filtrar() {
        const q = buscador.value.toLowerCase().trim();
        filteredItems = [];

        items.forEach(el => {
            const nombre = el.dataset.nombre || '';
            const codigo = el.dataset.codigo || '';
            const cat = el.dataset.cat || '';

            const matchQ = !q || nombre.includes(q) || codigo.includes(q);
            const matchCat = !catActiva || cat === catActiva;

            if (matchQ && matchCat) {
                filteredItems.push(el);
            } else {
                el.style.display = 'none';
            }
        });

        currentPage = 1;
        renderPage();
    }

    function renderPage() {
        items.forEach(el => el.style.display = 'none');

        if (filteredItems.length === 0) {
            sinRes.style.display = '';
            pagContainer.style.setProperty('display', 'none', 'important');
            return;
        }

        sinRes.style.display = 'none';

        const startIndex = (currentPage - 1) * itemsPerPage;
        const endIndex = startIndex + itemsPerPage;

        filteredItems.slice(startIndex, endIndex).forEach(el => {
            el.style.display = '';
        });

        renderPagination();
    }

    function renderPagination() {
        const totalPages = Math.ceil(filteredItems.length / itemsPerPage);

        if (totalPages <= 1) {
            pagContainer.style.setProperty('display', 'none', 'important');
            return;
        }

        pagContainer.style.setProperty('display', 'flex', 'important');
        let html = '';

        html += `<button class="page-btn ${currentPage === 1 ? 'disabled' : ''}" onclick="changePage(${currentPage - 1})"><i class="fas fa-chevron-left"></i></button>`;

        // Lógica para mostrar páginas sin que se desborde (máximo 5 botones)
        let startPage = Math.max(1, currentPage - 2);
        let endPage = Math.min(totalPages, startPage + 4);

        if (endPage - startPage < 4) {
            startPage = Math.max(1, endPage - 4);
        }

        if (startPage > 1) {
            html += `<button class="page-btn" onclick="changePage(1)">1</button>`;
            if (startPage > 2) html += `<span class="d-flex align-items-center text-muted px-1">...</span>`;
        }

        for (let i = startPage; i <= endPage; i++) {
            html += `<button class="page-btn ${currentPage === i ? 'active' : ''}" onclick="changePage(${i})">${i}</button>`;
        }

        if (endPage < totalPages) {
            if (endPage < totalPages - 1) html += `<span class="d-flex align-items-center text-muted px-1">...</span>`;
            html += `<button class="page-btn" onclick="changePage(${totalPages})">${totalPages}</button>`;
        }

        html += `<button class="page-btn ${currentPage === totalPages ? 'disabled' : ''}" onclick="changePage(${currentPage + 1})"><i class="fas fa-chevron-right"></i></button>`;

        pagContainer.innerHTML = html;
    }

    window.changePage = function(page) {
        const totalPages = Math.ceil(filteredItems.length / itemsPerPage);
        if (page >= 1 && page <= totalPages && page !== currentPage) {
            currentPage = page;
            renderPage();
            window.scrollTo({
                top: document.getElementById('gridProductos').offsetTop - 50,
                behavior: 'smooth'
            });
        }
    };

    buscador.addEventListener('input', filtrar);

    document.querySelectorAll('.cat-pill').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.cat-pill').forEach(b => b.classList.remove('activo'));
            this.classList.add('activo');
            catActiva = this.dataset.cat;
            filtrar();
        });
    });

    // Inicializar la primera vez
    filtrar();
</script>