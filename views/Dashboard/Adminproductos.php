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

$titulo = "Gestión de Productos";

// =========================================================================
// CONEXIÓN A MODELOS
// =========================================================================
$productos = $categorias = $proveedores = [];
$total_prod = 0;
$_error_bd = '';

try {
    require_once __DIR__ . '/../../config/database.php';
    require_once __DIR__ . '/../../models/Producto.php';
    require_once __DIR__ . '/../../models/Categoria.php';
    require_once __DIR__ . '/../../models/Proveedor.php';
    $db = (new Database())->conectar();
    $productos   = (new Producto($db))->listarTodos();
    $categorias  = (new Categoria($db))->listarTodas();
    $proveedores = (new Proveedor($db))->listarTodos();
    $total_prod  = count($productos);
} catch (Throwable $e) {
    $_error_bd = 'No se pudieron cargar los datos. Verifique la base de datos.';
    error_log("[SIGI] productos.php Error: " . $e->getMessage());
}

require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidebar.php';
?>

<style>
    /* =====================================================
       TOKENS DE DISEÑO
       ===================================================== */
    .module-card {
        background: #fff;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }

    /* GRID CARDS */
    .product-grid-card {
        background: #fff;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.04);
        overflow: hidden;
        display: flex;
        flex-direction: column;
        height: 100%;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .product-grid-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08);
    }

    .product-img-container {
        background: #f8fafc;
        height: 160px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1rem;
        border-bottom: 1px solid #f1f5f9;
        position: relative;
    }

    .product-img-container img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }

    .product-info {
        padding: 1.25rem;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }

    .product-title {
        font-weight: 700;
        color: #1e293b;
        font-size: 0.95rem;
        margin-bottom: 0.5rem;
    }

    .tag-badge {
        padding: 0.25rem 0.6rem;
        border-radius: 6px;
        font-size: 0.72rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
    }

    .tag-cat {
        background: #eff6ff;
        color: #3b82f6;
    }

    .tag-activo {
        background: #dcfce7;
        color: #16a34a;
    }

    .tag-inactivo {
        background: #f1f5f9;
        color: #64748b;
    }

    .tag-stock-ok {
        background: #dcfce7;
        color: #16a34a;
    }

    .tag-stock-low {
        background: #eef2ff;
        color: #4338ca;
    }

    .tag-stock-out {
        background: #fee2e2;
        color: #dc2626;
    }

    .product-price {
        font-size: 1.15rem;
        font-weight: 800;
        color: #0f172a;
        margin-top: auto;
    }

    .product-price-buy {
        font-size: 0.75rem;
        color: #94a3b8;
        font-weight: 500;
    }

    .btn-card-action {
        flex: 1;
        padding: 0.5rem;
        border-radius: 10px;
        font-size: 0.8rem;
        font-weight: 600;
        border: none;
        transition: all 0.2s;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
    }

    .btn-card-edit {
        background: #eef2ff;
        color: #6366f1;
    }

    .btn-card-delete {
        background: #fef2f2;
        color: #ef4444;
        max-width: 42px;
    }

    /* VISTA TABLA */
    #vistaTablaCont {
        display: none;
    }

    .table-custom thead th {
        background: #f1f5f9;
        color: #64748b;
        font-size: 0.72rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #e2e8f0;
        padding: 1rem;
        font-weight: 700;
    }

    .table-custom tbody td {
        padding: 1rem;
        vertical-align: middle;
        font-size: 0.88rem;
        border-bottom: 1px solid #f1f5f9;
        color: #334155;
    }

    .table-custom tbody tr:hover td {
        background: #f8fafc;
        transition: background 0.2s ease;
    }

    /* BOTONES DE ACCIÓN PREMIUM */
    .btn-accion {
        width: 38px;
        height: 38px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: none;
        transition: all 0.2s;
        font-size: 0.95rem;
        cursor: pointer;
    }

    .btn-accion-editar {
        background: #eff6ff;
        color: #2563eb;
    }

    .btn-accion-editar:hover {
        background: #2563eb;
        color: #fff;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
    }

    .btn-accion-desactivar {
        background: #fef2f2;
        color: #ef4444;
    }

    .btn-accion-desactivar:hover {
        background: #ef4444;
        color: #fff;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
    }

    .btn-accion-activar {
        background: #ecfdf5;
        color: #10b981;
    }

    .btn-accion-activar:hover {
        background: #10b981;
        color: #fff;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
    }

    .btn-vista {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        border: 1px solid rgba(255, 255, 255, .3);
        background: rgba(255, 255, 255, .15);
        color: #fff;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .btn-vista.activo {
        background: #fff !important;
        color: #2563eb !important;
        border-color: #fff !important;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
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
        margin: 0 2px !important;
    }
    .dataTables_paginate .paginate_button:hover { 
        background: #e2e8f0 !important;
        color: #1e3a8a !important;
    }
    .dataTables_paginate .paginate_button.current {
        background: #2563eb !important;
        color: #fff !important;
        box-shadow: 0 4px 10px rgba(37, 99, 235, 0.2);
    }
    .dataTables_paginate .paginate_button.disabled {
        opacity: 0.5 !important;
        cursor: not-allowed !important;
    }

    /* DATATABLES CONTROLES */
    .dataTables_wrapper .dataTables_length {
        margin-bottom: 1rem;
    }
    
    .dataTables_wrapper .dataTables_length select {
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 0.4rem 2rem 0.4rem 0.8rem;
        font-size: 0.85rem;
        font-weight: 600;
        color: #475569;
        background: #fff;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .dataTables_wrapper .dataTables_length select:focus {
        outline: none;
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }
    
    .dataTables_wrapper .dataTables_filter {
        margin-bottom: 1rem;
    }
    
    .dataTables_wrapper .dataTables_filter input {
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 0.5rem 1rem;
        font-size: 0.85rem;
        background: #f8fafc;
        transition: all 0.2s;
        width: 250px;
    }
    
    .dataTables_wrapper .dataTables_filter input:focus {
        outline: none;
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        background: #fff;
    }
    
    .dataTables_wrapper .dataTables_info {
        font-size: 0.82rem;
        color: #64748b;
        font-weight: 600;
        padding: 1rem 0;
    }
    
    .dataTables_wrapper .dataTables_length label,
    .dataTables_wrapper .dataTables_filter label {
        font-size: 0.85rem;
        font-weight: 600;
        color: #475569;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
</style>

<div class="container-fluid py-4">
    <!-- Banner de módulo Premium -->
    <div class="module-banner">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-4 position-relative" style="z-index: 2;">
            <div>
                <span class="banner-label" style="color: #fff;">Gestión Comercial</span>
                <h1 class="fw-bold m-0 text-white" style="font-family: 'Poppins', sans-serif; letter-spacing: -1px;">Catálogo de Productos</h1>
                <p class="m-0 text-white opacity-90 fw-light">Administra el inventario y precios de tu tienda con precisión.</p>
            </div>
            <div class="d-flex align-items-center gap-3 flex-wrap">
                <!-- Píldora de Total Sincronizada -->
                <div style="background: rgba(255, 255, 255, 0.15); border: 1px solid rgba(255, 255, 255, 0.3); border-radius: 50px; padding: 0.5rem 1.2rem; display: flex; align-items: center; gap: 0.6rem; color: #fff; backdrop-filter: blur(8px); box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                    <i class="fas fa-boxes-stacked" style="font-size: 0.9rem; opacity: 0.9;"></i>
                    <div class="d-flex flex-column" style="line-height: 1;">
                        <span style="font-size: 1rem; font-weight: 800; color: #fff;"><?= $total_prod ?></span>
                        <span style="font-size: 0.65rem; font-weight: 700; text-transform: uppercase; opacity: 0.8; letter-spacing: 0.3px; color: #fff;">Productos</span>
                    </div>
                </div>
                <div class="d-flex gap-2 mx-1">
                    <button id="btnVistaGrid" class="btn-vista activo" title="Vista tarjetas"><i class="fas fa-th-large"></i></button>
                    <button id="btnVistaTabla" class="btn-vista" title="Vista tabla"><i class="fas fa-list"></i></button>
                </div>
                <button class="btn btn-white fw-bold px-4 py-2 rounded-3" style="background:#fff; color:#2563eb; border:none; box-shadow: 0 4px 15px rgba(0,0,0,0.1);" onclick="abrirModalProducto()">
                    <i class="fas fa-plus me-2"></i> Nuevo Producto
                </button>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="module-card p-4 mb-4">
        <div class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label small fw-bold text-muted text-uppercase mb-2" style="letter-spacing: 0.5px;">Buscar Producto</label>
                <div class="input-group shadow-sm" style="border-radius: 12px; overflow: hidden;">
                    <span class="input-group-text bg-light border-0" style="padding: 0.6rem 1rem;"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" id="filtroNombre" class="form-control bg-light border-0" placeholder="Nombre o código de producto..." style="padding: 0.6rem 1rem;">
                </div>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-bold text-muted text-uppercase mb-2" style="letter-spacing: 0.5px;">Categoría</label>
                <select id="filtroCategoria" class="form-select bg-light border-0 shadow-sm" style="border-radius: 12px; padding: 0.6rem 1rem;">
                    <option value="">Todas las categorías</option>
                    <?php foreach ($categorias as $cat): ?>
                        <option value="<?= $cat['id_categoria'] ?>"><?= htmlspecialchars($cat['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-bold text-muted text-uppercase mb-2" style="letter-spacing: 0.5px;">Stock</label>
                <select id="filtroEstado" class="form-select bg-light border-0 shadow-sm" style="border-radius: 12px; padding: 0.6rem 1rem;">
                    <option value="">Todos los estados</option>
                    <option value="activo">Activo</option>
                    <option value="inactivo">Inactivo</option>
                    <option value="bajo">Bajo Stock</option>
                </select>
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="button" onclick="aplicarFiltros()" class="btn fw-bold px-4 rounded-pill shadow-sm text-white w-100" style="background: linear-gradient(135deg, #1e3a8a, #2563eb); border: none; padding: 0.6rem 1rem;">
                    <i class="fas fa-filter me-2"></i> Aplicar Filtros
                </button>
                <button id="btnLimpiarFiltros" class="btn btn-light px-4 fw-bold rounded-pill border text-muted w-100" style="padding: 0.6rem 1rem;">
                    <i class="fas fa-undo me-2"></i> Limpiar
                </button>
            </div>
        </div>
        <div id="txtResultados" class="text-muted mt-3 small fw-bold opacity-75"><i class="fas fa-info-circle me-1"></i> Mostrando todos los productos</div>
    </div>

    <!-- Grid View -->
    <div id="vistaGridCont">
        <div class="row g-4" id="gridProductos">
            <?php foreach ($productos as $p): ?>
                <?php
                $st = strtolower($p['estado'] ?? 'activo');
                $isLow = ($p['stock_actual'] <= $p['stock_minimo'] && $p['stock_actual'] > 0);
                $isOut = ($p['stock_actual'] <= 0);
                $st_tag = $isOut ? 'tag-stock-out' : ($isLow ? 'tag-stock-low' : 'tag-stock-ok');
                ?>
                <div class="col-12 col-sm-6 col-xl-3 producto-item"
                    data-nombre="<?= strtolower(htmlspecialchars($p['nombre'] . ' ' . ($p['codigoUnico'] ?? ''))) ?>"
                    data-categoria="<?= $p['id_categoria'] ?>"
                    data-estado="<?= $isOut ? 'agotado' : ($isLow ? 'bajo' : $st) ?>">
                    <div class="product-grid-card" style="position:relative;">
                        <span class="tag-badge <?= $st === 'activo' ? 'tag-activo' : 'tag-inactivo' ?> shadow-sm" style="position:absolute; top:1rem; right:1rem; z-index: 10;">
                            <?= ucfirst($st) ?>
                        </span>
                        
                        <div class="product-img-container">
                            <?php if (!empty($p['imagen'])): ?>
                                <img src="<?= $base_url ?>IMG/productos/<?= htmlspecialchars($p['imagen']) ?>">
                            <?php else: ?>
                                <i class="fas fa-box-open opacity-20 fa-4x text-muted"></i>
                            <?php endif; ?>
                        </div>
                        <div class="product-info text-center">
                            <h3 class="product-title"><?= htmlspecialchars($p['nombre']) ?></h3>
                            <div class="d-flex flex-wrap gap-1 mb-2 justify-content-center">
                                <span class="tag-badge tag-cat"><?= htmlspecialchars($p['nombre_categoria'] ?? 'Sin cat.') ?></span>
                                <span class="tag-badge <?= $st_tag ?>"><?= $p['stock_actual'] ?> und</span>
                            </div>
                            <div class="product-price">
                                $<?= number_format($p['precio_venta'], 0, ',', '.') ?>
                                <div class="product-price-buy">Costo: $<?= number_format($p['precio_compra'] ?? 0, 0, ',', '.') ?></div>
                            </div>
                            <div class="d-flex gap-2 mt-3 justify-content-center">
                                <button class="btn-card-action btn-card-edit w-100" onclick="editarProducto(<?= $p['id_producto'] ?>, '<?= htmlspecialchars($p['nombre'], ENT_QUOTES) ?>', <?= $p['id_categoria'] ?>, <?= (int)($p['id_proveedor'] ?? 0) ?>, <?= $p['precio_compra'] ?>, <?= $p['precio_venta'] ?>, <?= $p['stock_minimo'] ?>, '<?= htmlspecialchars($p['descripcion'] ?? '', ENT_QUOTES) ?>', '<?= htmlspecialchars($p['imagen'] ?? '', ENT_QUOTES) ?>', '<?= htmlspecialchars($p['codigoUnico'] ?? '', ENT_QUOTES) ?>')">
                                    <i class="fas fa-edit"></i> Editar
                                </button>
                                <button class="btn-card-action btn-card-delete" onclick="confirmarAccion('<?= $base_url ?>controllers/ProductoController.php?accion=<?= $st === 'activo' ? 'desactivar' : 'activar' ?>&id=<?= $p['id_producto'] ?>', '<?= htmlspecialchars($p['nombre'], ENT_QUOTES) ?>', '<?= $st === 'activo' ? 'desactivar' : 'activar' ?>')">
                                    <i class="fas fa-<?= $st === 'activo' ? 'ban' : 'check' ?>"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Table View -->
    <div id="vistaTablaCont">
        <div class="module-card overflow-hidden">
            <div class="table-responsive p-4">
                <table id="tablaProductos" class="table table-hover table-custom w-100">
                    <thead>
                        <tr>
                            <th style="width: 12%;">Código</th>
                            <th style="width: 20%;">Nombre</th>
                            <th style="width: 14%;">Categoría</th>
                            <th style="width: 14%;">Proveedor</th>
                            <th class="text-center" style="width: 10%;">Estado</th>
                            <th class="text-end" style="width: 12%;">Precio</th>
                            <th class="text-center" style="width: 10%;">Stock</th>
                            <th class="text-center" style="width: 8%;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($productos as $p): ?>
                            <?php
                            $st = strtolower($p['estado'] ?? 'activo');
                            $isLow = ($p['stock_actual'] <= $p['stock_minimo'] && $p['stock_actual'] > 0);
                            $isOut = ($p['stock_actual'] <= 0);
                            ?>
                            <tr class="producto-item-tr"
                                data-nombre="<?= strtolower(htmlspecialchars($p['nombre'] . ' ' . ($p['codigoUnico'] ?? ''))) ?>"
                                data-categoria="<?= $p['id_categoria'] ?>"
                                data-estado="<?= $isOut ? 'agotado' : ($isLow ? 'bajo' : $st) ?>">
                                <td>
                                    <code class="fw-bold text-primary" style="font-size: 0.8rem; background: #eff6ff; padding: 0.25rem 0.6rem; border-radius: 6px;">
                                        <?= htmlspecialchars($p['codigoUnico'] ?? 'S/C') ?>
                                    </code>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <?php if (!empty($p['imagen'])): ?>
                                            <img src="<?= $base_url ?>IMG/productos/<?= htmlspecialchars($p['imagen']) ?>" style="width:32px; height:32px; border-radius:6px; object-fit:cover; border:1px solid #e2e8f0;">
                                        <?php endif; ?>
                                        <div class="fw-bold text-dark"><?= htmlspecialchars($p['nombre']) ?></div>
                                    </div>
                                </td>
                                <td>
                                    <span class="tag-badge tag-cat">
                                        <i class="fas fa-folder me-1" style="font-size: 0.65rem;"></i>
                                        <?= htmlspecialchars($p['nombre_categoria'] ?? '-') ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="text-muted small fw-bold">
                                        <i class="fas fa-truck me-1 opacity-50"></i>
                                        <?= htmlspecialchars($p['nombre_proveedor'] ?? 'Sin prov.') ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="tag-badge <?= strtolower($p['estado'] ?? '') === 'activo' ? 'tag-activo' : 'tag-inactivo' ?>">
                                        <?= ucfirst($p['estado'] ?? 'Activo') ?>
                                    </span>
                                </td>
                                <td class="text-end fw-bold text-dark">
                                    $<?= number_format($p['precio_venta'], 0, ',', '.') ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($isOut): ?>
                                        <span class="tag-badge tag-stock-out">Agotado</span>
                                    <?php elseif ($isLow): ?>
                                        <span class="tag-badge tag-stock-low"><?= $p['stock_actual'] ?> und</span>
                                    <?php else: ?>
                                        <span class="tag-badge tag-stock-ok"><?= $p['stock_actual'] ?> und</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        <button class="btn-accion btn-accion-editar" style="width:32px; height:32px;"
                                                title="Editar"
                                                onclick="editarProducto(<?= $p['id_producto'] ?>, '<?= htmlspecialchars($p['nombre'], ENT_QUOTES) ?>', <?= $p['id_categoria'] ?>, <?= (int)($p['id_proveedor'] ?? 0) ?>, <?= $p['precio_compra'] ?>, <?= $p['precio_venta'] ?>, <?= $p['stock_minimo'] ?>, '<?= htmlspecialchars($p['descripcion'] ?? '', ENT_QUOTES) ?>', '<?= htmlspecialchars($p['imagen'] ?? '', ENT_QUOTES) ?>', '<?= htmlspecialchars($p['codigoUnico'] ?? '', ENT_QUOTES) ?>')">
                                            <i class="fas fa-edit" style="font-size: 0.8rem;"></i>
                                        </button>
                                        <button class="btn-accion <?= strtolower($p['estado'] ?? '') === 'activo' ? 'btn-accion-desactivar' : 'btn-accion-activar' ?>" style="width:32px; height:32px;"
                                                title="<?= strtolower($p['estado'] ?? '') === 'activo' ? 'Desactivar' : 'Activar' ?>"
                                                onclick="confirmarAccion('<?= $base_url ?>controllers/ProductoController.php?accion=<?= strtolower($p['estado'] ?? '') === 'activo' ? 'desactivar' : 'activar' ?>&id=<?= $p['id_producto'] ?>', '<?= htmlspecialchars($p['nombre'], ENT_QUOTES) ?>', '<?= strtolower($p['estado'] ?? '') === 'activo' ? 'desactivar' : 'activar' ?>')">
                                            <i class="fas fa-<?= strtolower($p['estado'] ?? '') === 'activo' ? 'ban' : 'check' ?>" style="font-size: 0.8rem;"></i>
                                        </button>
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
    const BASE_URL_PROD = '<?= $base_url ?>';
    const CATEGORIAS_PROD = <?= json_encode(array_map(fn($c) => ['id' => $c['id_categoria'], 'nombre' => $c['nombre']], $categorias)) ?>;
    const PROVEEDORES_PROD = <?= json_encode(array_map(fn($p) => ['id' => $p['id_proveedor'], 'nombre' => $p['nombre']], $proveedores)) ?>;

    function _buildModalProducto() {
        const prev = document.getElementById('modalProductoDyn');
        const prevOv = document.getElementById('modalProductoOvDyn');
        if (prev) prev.remove();
        if (prevOv) prevOv.remove();

        const overlay = document.createElement('div');
        overlay.id = 'modalProductoOvDyn';
        overlay.style.cssText = 'position:fixed;top:0;right:0;bottom:0;left:0;background:rgba(15,23,42,0.6);backdrop-filter:blur(4px);z-index:9998;animation:overlayFadeIn 0.3s ease;';
        overlay.onclick = cerrarModalProducto;
        document.body.appendChild(overlay);

        const optCats = CATEGORIAS_PROD.map(c => `<option value="${c.id}">${c.nombre}</option>`).join('');
        const optProvs = PROVEEDORES_PROD.map(p => `<option value="${p.id}">${p.nombre}</option>`).join('');

        const wrap = document.createElement('div');
        wrap.id = 'modalProductoDyn';
        wrap.style.cssText = 'position:fixed;top:0;right:0;bottom:0;left:0;z-index:9999;display:flex;align-items:center;justify-content:center;padding:1rem;overflow-y:auto;pointer-events:none;';

        const modalDiv = document.createElement('div');
        modalDiv.style.cssText = 'width:100%; max-width:850px; pointer-events:auto;';
        wrap.appendChild(modalDiv);

        modalDiv.innerHTML = `
        <div style="background:#fff;border-radius:24px;overflow:hidden;width:100%;max-width:850px;box-shadow:0 30px 70px rgba(0,0,0,.3);margin:auto;animation: modalFadeIn 0.3s ease-out;">
            <div style="background:linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);padding:1.5rem 2rem;display:flex;justify-content:space-between;align-items:center;color:#fff;">
                <div class="d-flex align-items-center gap-2">
                    <div style="background:rgba(255,255,255,0.2);width:38px;height:38px;border-radius:10px;display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-box-open text-white"></i>
                    </div>
                    <h5 id="modalProdTitulo" class="m-0 fw-bold text-white" style="letter-spacing:-0.5px; font-size:1.4rem;">Gestión de Producto</h5>
                </div>
                <button type="button" onclick="cerrarModalProducto()" class="btn-close btn-close-white" style="opacity:0.8; transition:0.2s;"></button>
            </div>
            
            <form id="formProductoDyn" method="POST" enctype="multipart/form-data" class="p-4 px-md-5 needs-validation" novalidate>
                <input type="hidden" id="p_id" name="id_producto">
                <input type="hidden" id="p_imagen_actual" name="imagen_actual">
                
                <div class="row g-4">
                    <!-- Sección Principal -->
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-muted text-uppercase mb-1"><i class="fas fa-barcode me-1"></i> Código Único *</label>
                        <input type="text" id="p_codigo" name="codigoUnico" class="form-control rounded-3" placeholder="Ej: PROD-001" required style="padding: 0.6rem 1rem;">
                        <div class="invalid-feedback">El código es obligatorio.</div>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label small fw-bold text-muted text-uppercase mb-1"><i class="fas fa-tag me-1"></i> Nombre del Producto *</label>
                        <input type="text" id="p_nombre" name="nombre" class="form-control rounded-3" placeholder="Ej: Zapatos Deportivos Nike" required style="padding: 0.6rem 1rem;">
                        <div class="invalid-feedback">El nombre es obligatorio.</div>
                    </div>

                    <!-- Clasificación -->
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-muted text-uppercase mb-1"><i class="fas fa-folder me-1"></i> Categoría *</label>
                        <select id="p_categoria" name="id_categoria" class="form-select rounded-3" required style="padding: 0.6rem 1rem;">
                            <option value="" disabled selected>Seleccionar categoría...</option>
                            ${optCats}
                        </select>
                        <div class="invalid-feedback">Seleccione una categoría.</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-muted text-uppercase mb-1"><i class="fas fa-truck me-1"></i> Proveedor</label>
                        <select id="p_proveedor" name="id_proveedor" class="form-select rounded-3" style="padding: 0.6rem 1rem;">
                            <option value="">Sin proveedor asignado</option>
                            ${optProvs}
                        </select>
                    </div>

                    <!-- Precios y Stock -->
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-muted text-uppercase mb-1"><i class="fas fa-dollar-sign me-1"></i> Precio Costo</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">$</span>
                            <input type="number" id="p_precio_costo" name="precio_costo" class="form-control rounded-end-3" required step="0.01" placeholder="0.00" style="padding: 0.6rem 1rem;">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-muted text-uppercase mb-1"><i class="fas fa-hand-holding-dollar me-1"></i> Precio Venta</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">$</span>
                            <input type="number" id="p_precio_venta" name="precio_venta" class="form-control rounded-end-3" required step="0.01" placeholder="0.00" style="padding: 0.6rem 1rem;">
                        </div>
                    </div>
                    <div class="col-md-3" id="col_stock_dyn">
                        <label class="form-label small fw-bold text-muted text-uppercase mb-1"><i class="fas fa-cubes me-1"></i> Stock Inicial</label>
                        <input type="number" id="p_stock" name="stock_actual" class="form-control rounded-3" required value="0" style="padding: 0.6rem 1rem;">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-muted text-uppercase mb-1"><i class="fas fa-triangle-exclamation me-1"></i> Alerta Mín.</label>
                        <input type="number" id="p_stock_min" name="stock_minimo" class="form-control rounded-3" required value="5" style="padding: 0.6rem 1rem;">
                    </div>

                    <!-- Descripción e Imagen -->
                    <div class="col-md-7">
                        <label class="form-label small fw-bold text-muted text-uppercase mb-1"><i class="fas fa-align-left me-1"></i> Descripción del Producto</label>
                        <textarea id="p_descripcion" name="descripcion" class="form-control rounded-3" rows="4" placeholder="Detalles, material, tallas..." style="padding: 0.6rem 1rem; resize:none;"></textarea>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label small fw-bold text-muted text-uppercase mb-1"><i class="fas fa-image me-1"></i> Imagen</label>
                        <div class="border rounded-3 p-2 d-flex flex-column align-items-center justify-content-center" style="min-height: 115px; background: #fcfdfe; border-style: dashed !important; border-width: 2px !important;">
                            <input type="file" id="p_imagen" name="imagen" class="form-control form-control-sm border-0 bg-transparent mb-2" accept="image/*">
                            <div id="p_preview_wrap" style="display:none;" class="mt-1">
                                <img id="p_preview_img" src="" style="width:80px; height:80px; object-fit:cover; border-radius:12px; box-shadow:0 4px 10px rgba(0,0,0,0.1);">
                            </div>
                            <div id="p_no_image_text" class="text-muted small py-3"><i class="fas fa-cloud-arrow-up me-1"></i> Subir imagen</div>
                        </div>
                    </div>
                </div>

                <div class="text-end mt-5 pb-2">
                    <button type="button" onclick="cerrarModalProducto()" class="btn btn-light border-0 px-4 py-2 fw-bold me-2 rounded-pill" style="color: #64748b;">Cancelar</button>
                    <button type="submit" class="btn btn-primary px-5 py-2 fw-bold rounded-pill shadow-sm" style="background:linear-gradient(135deg,#2563eb,#1e3a8a); border:none;">
                        <i class="fas fa-save me-2"></i>Guardar Cambios
                    </button>
                </div>
            </form>
        </div>`;

        document.body.appendChild(wrap);

        // Preview de imagen
        document.getElementById('p_imagen').addEventListener('change', function() {
            const file = this.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = e => {
                document.getElementById('p_preview_img').src = e.target.result;
                document.getElementById('p_preview_wrap').style.display = 'block';
                document.getElementById('p_no_image_text').style.display = 'none';
            };
            reader.readAsDataURL(file);
        });

        // Validación de Bootstrap
        document.getElementById('formProductoDyn').addEventListener('submit', function(event) {
            if (!this.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            this.classList.add('was-validated');
        }, false);
    }

    function abrirModalProducto() {
        _buildModalProducto();
        document.getElementById('modalProdTitulo').innerText = 'Nuevo Producto';
        document.getElementById('formProductoDyn').action = BASE_URL_PROD + 'controllers/ProductoController.php?accion=crear';
    }

    function editarProducto(id, n, c, p, pc, pv, sm, d, i, co) {
        _buildModalProducto();
        document.getElementById('modalProdTitulo').innerText = 'Editar Producto';
        document.getElementById('p_id').value = id;
        document.getElementById('p_nombre').value = n;
        document.getElementById('p_categoria').value = c;
        document.getElementById('p_proveedor').value = p;
        document.getElementById('p_precio_costo').value = pc;
        document.getElementById('p_precio_venta').value = pv;
        document.getElementById('p_stock_min').value = sm;
        document.getElementById('p_descripcion').value = d;
        document.getElementById('p_imagen_actual').value = i;
        document.getElementById('p_codigo').value = co;
        document.getElementById('col_stock_dyn').style.display = 'none';
        document.getElementById('formProductoDyn').action = BASE_URL_PROD + 'controllers/ProductoController.php?accion=editar';
    }

    function cerrarModalProducto() {
        document.getElementById('modalProductoDyn')?.remove();
        document.getElementById('modalProductoOvDyn')?.remove();
    }

    // Toggle Vista
    const btnGrid = document.getElementById('btnVistaGrid');
    const btnTabla = document.getElementById('btnVistaTabla');
    const contGrid = document.getElementById('vistaGridCont');
    const contTabla = document.getElementById('vistaTablaCont');
    let tablaInit = false;

    btnGrid?.addEventListener('click', () => {
        contGrid.style.display = 'block';
        contTabla.style.display = 'none';
        btnGrid.classList.add('activo');
        btnTabla.classList.remove('activo');
    });

    btnTabla?.addEventListener('click', () => {
        contGrid.style.display = 'none';
        contTabla.style.display = 'block';
        btnGrid.classList.remove('activo');
        btnTabla.classList.add('activo');
        
        // Inicializar DataTables con diseño premium
        if (!tablaInit && typeof $.fn.DataTable !== 'undefined') {
            $('#tablaProductos').DataTable({
                searching: true,
                order: [[0, 'asc']],
                pageLength: 20,
                dom: 'rt<"px-4 py-4 d-flex justify-content-between align-items-center"ip>',
                language: {
                    lengthMenu: "Mostrar _MENU_",
                    zeroRecords: "No se encontraron productos",
                    info: "_TOTAL_ productos",
                    infoEmpty: "0 productos",
                    infoFiltered: "(filtrado)",
                    paginate: {
                        first: "Prim.",
                        last: "Últ.",
                        next: "Sig.",
                        previous: "Ant."
                    }
                }
            });
            tablaInit = true;
        }
    });

    // Filtros
    // Filtrado Premium en Tiempo Real con DataTables API para la Vista de Tabla
    $.fn.dataTable.ext.search.push(
        function(settings, data, dataIndex, rowData, counter) {
            if (settings.nTable.id !== 'tablaProductos') return true;
            
            const txt = document.getElementById('filtroNombre').value.toLowerCase().trim();
            const cat = document.getElementById('filtroCategoria').value;
            const est = document.getElementById('filtroEstado').value;
            
            const rowEl = $(settings.aoData[dataIndex].nTr);
            const dataNom = (rowEl.data('nombre') || '').toString().toLowerCase();
            const dataCat = (rowEl.data('categoria') || '').toString();
            const dataEst = (rowEl.data('estado') || '').toString();

            const ok = (!txt || dataNom.includes(txt)) && (!cat || dataCat === cat) && (!est || dataEst === est);
            return ok;
        }
    );

    function aplicarFiltros() {
        const txt = document.getElementById('filtroNombre').value.toLowerCase().trim();
        const cat = document.getElementById('filtroCategoria').value;
        const est = document.getElementById('filtroEstado').value;
        
        // 1. Filtrar Vista Grid (Tarjetas)
        const gridItems = document.querySelectorAll('.producto-item');
        let visGrid = 0;
        gridItems.forEach(el => {
            const dataNom = (el.dataset.nombre || '').toLowerCase();
            const dataCat = (el.dataset.categoria || '');
            const dataEst = (el.dataset.estado || '');
            const ok = (!txt || dataNom.includes(txt)) && (!cat || dataCat === cat) && (!est || dataEst === est);
            el.style.display = ok ? 'block' : 'none';
            if (ok) visGrid++;
        });

        // 2. Filtrar Vista Tabla (DataTables)
        let visTabla = visGrid;
        if (typeof $.fn.DataTable !== 'undefined' && $.fn.DataTable.isDataTable('#tablaProductos')) {
            const table = $('#tablaProductos').DataTable();
            table.draw();
            visTabla = table.page.info().recordsDisplay;
        } else {
            // Fallback si la tabla no se ha inicializado todavía
            const tableRows = document.querySelectorAll('.producto-item-tr');
            tableRows.forEach(el => {
                const dataNom = (el.dataset.nombre || '').toLowerCase();
                const dataCat = (el.dataset.categoria || '');
                const dataEst = (el.dataset.estado || '');
                const ok = (!txt || dataNom.includes(txt)) && (!cat || dataCat === cat) && (!est || dataEst === est);
                el.style.display = ok ? '' : 'none';
            });
        }
        
        // Actualizar contador
        const isTablaActive = document.getElementById('btnVistaTabla').classList.contains('activo');
        const count = isTablaActive ? visTabla : visGrid;
        document.getElementById('txtResultados').innerText = `Encontrados ${count} productos`;
    }

    ['filtroNombre', 'filtroCategoria', 'filtroEstado'].forEach(id => {
        document.getElementById(id)?.addEventListener('input', aplicarFiltros);
        document.getElementById(id)?.addEventListener('change', aplicarFiltros);
    });

    document.getElementById('btnLimpiarFiltros')?.addEventListener('click', () => {
        ['filtroNombre', 'filtroCategoria', 'filtroEstado'].forEach(id => document.getElementById(id).value = '');
        aplicarFiltros();
    });

    function confirmarAccion(url, nom, acc) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: `¿${acc.charAt(0).toUpperCase() + acc.slice(1)} producto?`,
                text: nom,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: acc === 'desactivar' ? '#dc2626' : '#16a34a'
            }).then(r => {
                if (r.isConfirmed) window.location.href = url;
            });
        } else if (confirm(`¿${acc} ${nom}?`)) window.location.href = url;
    }

    aplicarFiltros();
</script>