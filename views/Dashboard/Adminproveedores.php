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

$titulo = "Directorio de Proveedores";
$base_url = "http://" . $_SERVER['HTTP_HOST'] . "/SIGI/";

// =========================================================================
// CONEXIÓN A MODELOS
// =========================================================================
$proveedores = [];
$_error_bd = '';

try {
    require_once __DIR__ . '/../../config/database.php';
    require_once __DIR__ . '/../../models/Proveedor.php';
    $db = (new Database())->conectar();
    
    // Capturar filtros
    $filtros = [
        'estado'  => $_GET['estado'] ?? 'todos',
        'empresa' => $_GET['empresa'] ?? ''
    ];
    
    $proveedores = (new Proveedor($db))->listarTodos($filtros);
} catch (Throwable $e) {
    $_error_bd = 'No se pudieron cargar los datos. Verifique la base de datos.';
    error_log("[SIGI] proveedores.php Error: " . $e->getMessage());
}

require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidebar.php';
?>

<style>
    /* ==========================================================================
       VARIABLES Y DISEÑO BASE
       ========================================================================== */
    :root {
        --c-primary: #2563eb;
        --c-surface: #ffffff;
        --c-bg: #f8fafc;
        --c-border: #e2e8f0;
        --c-text: #0f172a;
        --c-muted: #64748b;
    }

    /* BANNER PREMIUM (Acorde a Usuarios/Categorías) */
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

    .module-banner::after {
        content: '';
        position: absolute;
        bottom: -80px;
        right: 100px;
        width: 180px;
        height: 180px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 50%;
    }

    .module-banner .inner {
        position: relative;
        z-index: 1;
    }

    .module-banner h2 {
        font-size: 1.8rem;
        font-weight: 800;
        margin-bottom: 0.3rem;
        font-family: 'Poppins', sans-serif;
    }

    .module-banner p {
        font-size: 0.95rem;
        opacity: 0.9;
        margin: 0;
    }

    .stat-pill {
        background: rgba(255, 255, 255, 0.15);
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 50px;
        padding: 0.5rem 1.2rem;
        font-size: 0.85rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.6rem;
        backdrop-filter: blur(8px);
    }

    /* CARD CONTENEDORA Y TABLA */
    .table-card {
        background: var(--c-surface);
        border-radius: 20px;
        border: 1px solid var(--c-border);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        padding: 1.5rem;
    }

    /* DATATABLES PREMIUM */
    .dataTables_wrapper .dataTables_length select,
    .dataTables_wrapper .dataTables_filter input {
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        padding: 0.5rem 1rem;
        font-size: 0.9rem;
        transition: all 0.3s;
    }

    .dataTables_wrapper .dataTables_filter input:focus {
        border-color: var(--c-primary);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        outline: none;
    }

    table.dataTable {
        border-collapse: separate !important;
        border-spacing: 0;
        width: 100% !important;
        margin-top: 1rem !important;
    }

    table.dataTable thead th {
        background: #f1f5f9;
        color: #64748b;
        font-weight: 700;
        font-size: 0.72rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 1.2rem 1rem;
        border-bottom: 2px solid #e2e8f0;
        border-top: none;
        white-space: nowrap;
    }

    table.dataTable tbody td {
        padding: 1rem;
        color: #334155;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }

    table.dataTable tbody tr:hover td {
        background: #eff6ff !important;
    }

    /* BOTONES DE ACCIÓN */
    .btn-accion {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        border: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
    }

    .btn-accion-editar {
        background: #eff6ff;
        color: #2563eb;
    }

    .btn-accion-editar:hover {
        background: #2563eb;
        color: #fff;
        transform: translateY(-2px);
    }

    .btn-accion-desactivar {
        background: #fef2f2;
        color: #ef4444;
    }

    .btn-accion-desactivar:hover {
        background: #ef4444;
        color: #fff;
        transform: translateY(-2px);
    }

    .btn-accion-activar {
        background: #ecfdf5;
        color: #10b981;
    }

    .btn-accion-activar:hover {
        background: #10b981;
        color: #fff;
        transform: translateY(-2px);
    }

    .badge-estado {
        padding: 0.4rem 0.8rem;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .estado-activo {
        background: #dcfce7;
        color: #16a34a;
        border: 1px solid #bbf7d0;
    }

    .estado-inactivo {
        background: #f1f5f9;
        color: #64748b;
        border: 1px solid #e2e8f0;
    }

    /* MODAL ANIMATION */
    @keyframes modalFadeIn {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .modal.show .modal-dialog {
        animation: modalFadeIn 0.3s ease-out forwards;
    }

    /* MODAL PREMIUM FIX (Blindaje total) */
    body.modal-open {
        overflow: hidden !important;
        padding-right: 0 !important;
    }

    .modal {
        background: rgba(0, 0, 0, 0.5);
    }

    .modal-dialog {
        margin: 0 auto !important;
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 100vh;
        max-width: 650px;
        z-index: 1070;
    }

    .modal-content {
        border-radius: 24px;
        border: none;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        width: 100%;
        position: relative;
    }

    /* MODAL HEADER LABELS */
    #modalProveedor .form-label-pro {
        font-size: 0.75rem;
        font-weight: 800;
        text-transform: uppercase;
        color: #94a3b8;
        letter-spacing: 0.8px;
        margin-bottom: 0.5rem;
        display: block;
    }

    #modalProveedor .form-control,
    #modalProveedor .form-select {
        border-radius: 12px;
        border: 2px solid #f1f5f9;
        padding: 0.75rem 1rem;
        font-size: 0.95rem;
        background: #f8fafc;
        transition: all 0.2s;
    }

    #modalProveedor .form-control:focus,
    #modalProveedor .form-select:focus {
        border-color: #2563eb;
        background: #fff;
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
        outline: none;
    }
</style>

<div class="container-fluid py-3">

    <!-- Manejo de Errores -->
    <?php if ($_error_bd): ?>
        <div class="alert alert-danger shadow-sm border-0 rounded-3 mb-4"><i class="fas fa-exclamation-triangle me-2"></i> <?= htmlspecialchars($_error_bd) ?></div>
    <?php endif; ?>

    <!-- Banner Principal -->
    <div class="module-banner">
        <div class="inner d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <span class="banner-label" style="color: #fff; background: rgba(255,255,255,0.15); padding: 0.4rem 1rem; border-radius: 50px; font-size: 0.65rem; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; display: inline-block; margin-bottom: 0.75rem; backdrop-filter: blur(4px);">Gestión de Suministros</span>
                <h2 class="fw-bold m-0 text-white" style="font-family: 'Poppins', sans-serif; letter-spacing: -1px;"><i class="fas fa-truck-loading me-2"></i> Directorio de Proveedores</h2>
                <p class="m-0 text-white opacity-90 fw-light">Control comercial y administración de proveedores estratégicos.</p>
            </div>
            <div class="d-flex align-items-center gap-3 flex-wrap">
                <span class="stat-pill"><i class="fas fa-building"></i> <?= count($proveedores) ?> <?= (count($proveedores) === 1) ? 'Resultado' : 'Resultados' ?></span>
                <button class="btn px-4 py-2 fw-bold" id="btnCrearProveedor"
                    style="background:#ffffff; color:#2563eb; border-radius:12px; box-shadow:0 4px 12px rgba(0,0,0,0.1);"
                    onclick="abrirModalNuevo()">
                    <i class="fas fa-plus me-2"></i> Añadir Proveedor
                </button>
            </div>
        </div>
    </div>

    <!-- Barra de Filtros (UML Sequence - Siempre visible) -->
    <div class="table-card mb-4" id="filtroContainer">
        <form method="GET" action="" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label small fw-bold text-muted text-uppercase mb-2" style="letter-spacing: 0.5px;">Nombre de Empresa</label>
                <div class="input-group shadow-sm" style="border-radius: 12px; overflow: hidden;">
                    <span class="input-group-text bg-light border-0" style="padding: 0.6rem 1rem;"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" name="empresa" class="form-control bg-light border-0" placeholder="Ej. Distribuidora Central" value="<?= htmlspecialchars($_GET['empresa'] ?? '') ?>" style="padding: 0.6rem 1rem;">
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold text-muted text-uppercase mb-2" style="letter-spacing: 0.5px;">Estado del Proveedor</label>
                <select name="estado" class="form-select bg-light border-0 shadow-sm" style="border-radius: 12px; padding: 0.6rem 1rem;">
                    <option value="todos" <?= ($_GET['estado'] ?? '') === 'todos' ? 'selected' : '' ?>>Todos los estados</option>
                    <option value="activo" <?= ($_GET['estado'] ?? '') === 'activo' ? 'selected' : '' ?>>Solo Activos</option>
                    <option value="inactivo" <?= ($_GET['estado'] ?? '') === 'inactivo' ? 'selected' : '' ?>>Solo Inactivos</option>
                </select>
            </div>
            <div class="col-md-5 d-flex gap-2">
                <button type="submit" class="btn fw-bold px-4 rounded-pill shadow-sm text-white w-100" style="background: linear-gradient(135deg, #1e3a8a, #2563eb); border: none; padding: 0.6rem 1rem;">
                    <i class="fas fa-filter me-2"></i> Aplicar Filtros
                </button>
                <a href="Adminproveedores.php" class="btn btn-light px-4 fw-bold rounded-pill border text-muted w-100 d-flex align-items-center justify-content-center" style="padding: 0.6rem 1rem;">
                    <i class="fas fa-undo me-2"></i> Limpiar
                </a>
            </div>
        </form>
    </div>

    <!-- Tabla DataTables -->
    <div class="table-card">
        <div class="table-responsive">
            <table id="tablaProveedores" class="table table-hover w-100">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 60px;">ID</th>
                        <th>Proveedor / Contacto</th>
                        <th>Empresa</th>
                        <th>Contacto</th>
                        <th class="text-center">Productos</th>
                        <th class="text-center">Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($proveedores)): ?>
                        <?php foreach ($proveedores as $prov): ?>
                            <tr>
                                <td class="text-center fw-bold text-muted">#<?= $prov['id_proveedor'] ?></td>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width:40px; height:40px;">
                                            <?= strtoupper(substr($prov['nombre'], 0, 1)) ?>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-bold text-dark"><?= htmlspecialchars($prov['nombre']) ?></h6>
                                            <small class="text-muted"><i class="fas fa-clock me-1"></i> Reg: <?= date('d/m/Y', strtotime($prov['fecha_registro'])) ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="fw-semibold text-dark"><i class="fas fa-building text-muted me-1"></i> <?= htmlspecialchars($prov['empresa'] ?: 'N/A') ?></span>
                                </td>
                                <td>
                                    <div class="d-flex flex-column" style="font-size:0.85rem;">
                                        <span class="text-muted mb-1"><i class="fas fa-phone me-1"></i> <?= htmlspecialchars($prov['telefono'] ?: 'N/A') ?></span>
                                        <span class="text-muted"><i class="fas fa-envelope me-1"></i> <?= htmlspecialchars($prov['email'] ?: 'N/A') ?></span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-primary border border-primary px-3 py-2 rounded-pill">
                                        <?= $prov['total_productos'] ?? 0 ?>Art.
                                    </span>
                                </td>
                                <td class="text-center">
                                    <?php if ($prov['activo']): ?>
                                        <span class="badge-estado estado-activo"><i class="fas fa-check-circle"></i> Activo</span>
                                    <?php else: ?>
                                        <span class="badge-estado estado-inactivo"><i class="fas fa-times-circle"></i> Inactivo</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <button class="btn-accion btn-accion-editar" title="Editar"
                                            onclick="abrirModalEditar(<?= $prov['id_proveedor'] ?>, '<?= htmlspecialchars($prov['nombre'], ENT_QUOTES) ?>', '<?= htmlspecialchars($prov['empresa'] ?? '', ENT_QUOTES) ?>', '<?= htmlspecialchars($prov['telefono'] ?? '', ENT_QUOTES) ?>', '<?= htmlspecialchars($prov['email'] ?? '', ENT_QUOTES) ?>')">
                                            <i class="fas fa-edit"></i>
                                        </button>

                                        <button class="btn-accion" style="background: #f5f3ff; color: #7c3aed;" title="Ver productos"
                                            onclick="verProductos(<?= $prov['id_proveedor'] ?>)">
                                            <i class="fas fa-boxes"></i>
                                        </button>

                                        <?php if ($prov['activo']): ?>
                                            <button class="btn-accion btn-accion-desactivar" title="Desactivar"
                                                onclick="confirmarEstado('<?= $base_url ?>controllers/ProveedorController.php?accion=toggleEstado&id=<?= $prov['id_proveedor'] ?>&estado=1', '<?= htmlspecialchars($prov['nombre'], ENT_QUOTES) ?>', 'desactivar')">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                        <?php else: ?>
                                            <button class="btn-accion btn-accion-activar" title="Activar"
                                                onclick="confirmarEstado('<?= $base_url ?>controllers/ProveedorController.php?accion=toggleEstado&id=<?= $prov['id_proveedor'] ?>&estado=0', '<?= htmlspecialchars($prov['nombre'], ENT_QUOTES) ?>', 'activar')">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        <?php endif; ?>
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
    const BASE_URL = '<?= $base_url ?>';

    $(document).ready(function() {
        $('#tablaProveedores').DataTable({
            searching: false,
            pageLength: 20,
            dom: 'rt<"px-4 py-4 d-flex justify-content-between align-items-center"ip>',
            language: {
                lengthMenu: "Mostrar _MENU_",
                info: "_TOTAL_ proveedores",
                paginate: {
                    previous: "Ant.",
                    next: "Sig."
                },
                zeroRecords: "No se encontraron proveedores"
            }
        });

        <?php if (isset($_SESSION['alert'])): ?>
            Swal.fire({
                icon: '<?= $_SESSION['alert']['icon'] ?>',
                title: '<?= $_SESSION['alert']['title'] ?>',
                text: '<?= $_SESSION['alert']['text'] ?>',
                confirmButtonColor: '#2563eb',
                customClass: {
                    popup: 'rounded-4',
                    confirmButton: 'rounded-3 px-4'
                }
            });
            <?php unset($_SESSION['alert']); ?>
        <?php endif; ?>
    });

    // =========================================================
    // MODALES DINÁMICOS (Soluciona problema de sidebar)
    // =========================================================
    function _buildModalProveedor() {
        const prev = document.getElementById('modalProveedorDyn');
        if (prev) prev.remove();
        const prevOv = document.getElementById('modalProvOverlay');
        if (prevOv) prevOv.remove();

        const overlay = document.createElement('div');
        overlay.id = 'modalProvOverlay';
        overlay.style.cssText = 'position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:9998;backdrop-filter:blur(2px);';
        overlay.onclick = cerrarModal;
        document.body.appendChild(overlay);

        const wrap = document.createElement('div');
        wrap.id = 'modalProveedorDyn';
        wrap.style.cssText = 'position:fixed;top:0;left:0;width:100%;height:100%;z-index:9999;display:flex;align-items:center;justify-content:center;padding:1rem;';
        
        wrap.innerHTML = `
        <div class="modal-content shadow-lg" style="background:#fff; border-radius: 24px; border: none; overflow: hidden; width:100%; max-width:550px; animation: modalIn 0.3s ease;">
            <div style="background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%); padding: 1.5rem 2rem; display: flex; justify-content: space-between; align-items: center; color: #fff;">
                <div class="d-flex align-items-center gap-3">
                    <div style="background: rgba(255,255,255,0.2); width: 42px; height: 42px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-truck-loading text-white"></i>
                    </div>
                    <div>
                        <h5 id="modalTitle" class="m-0 fw-bold text-white" style="font-size: 1.35rem; font-family: 'Poppins', sans-serif;">Nuevo Proveedor</h5>
                        <p class="m-0 text-white opacity-75" style="font-size: 0.72rem; text-transform: uppercase; letter-spacing: 1px; font-weight: 600;">Gestión comercial</p>
                    </div>
                </div>
                <button onclick="cerrarModal()" style="background:rgba(255,255,255,0.2); border:none; color:#fff; width:36px; height:36px; border-radius:10px; cursor:pointer;">✕</button>
            </div>
            <form id="formProvDyn" method="POST" action="${BASE_URL}controllers/ProveedorController.php?accion=crear" class="p-4 px-md-5">
                <input type="hidden" name="id_proveedor" id="p_id">
                <div class="row g-4 mb-3">
                    <div class="col-md-12">
                        <label class="form-label small fw-bold text-muted text-uppercase"><i class="fas fa-user me-1"></i> Nombre del Contacto *</label>
                        <input type="text" class="form-control rounded-3" id="p_nombre" name="nombre" required placeholder="Ej. Juan Pérez" style="padding: 0.7rem 1rem; border:2px solid #f1f5f9;">
                    </div>
                </div>
                <div class="row g-4 mb-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-muted text-uppercase"><i class="fas fa-phone me-1"></i> Teléfono</label>
                        <input type="text" class="form-control rounded-3" id="p_telefono" name="telefono" placeholder="Ej. 555-1234" style="padding: 0.7rem 1rem; border:2px solid #f1f5f9;">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-muted text-uppercase"><i class="fas fa-building me-1"></i> Empresa</label>
                        <input type="text" class="form-control rounded-3" id="p_empresa" name="empresa" placeholder="Ej. Distribuidora XYZ" style="padding: 0.7rem 1rem; border:2px solid #f1f5f9;">
                    </div>
                </div>
                <div class="row g-4 mb-4">
                    <div class="col-md-12">
                        <label class="form-label small fw-bold text-muted text-uppercase"><i class="fas fa-envelope me-1"></i> Correo Electrónico</label>
                        <input type="email" class="form-control rounded-3" id="p_email" name="email" placeholder="correo@empresa.com" style="padding: 0.7rem 1rem; border:2px solid #f1f5f9;">
                    </div>
                </div>
                <div class="d-flex justify-content-end gap-3 pt-4 border-top">
                    <button type="button" onclick="cerrarModal()" class="btn btn-light px-4 py-2 fw-bold text-muted border-0 rounded-pill" style="background: #f1f5f9;">Cancelar</button>
                    <button type="submit" id="btnSubmit" class="btn fw-bold px-5 py-2 rounded-pill shadow-sm text-white" style="background: linear-gradient(135deg, #1e3a8a, #2563eb); border: none;">GUARDAR</button>
                </div>
            </form>
        </div>
        `;
        document.body.appendChild(wrap);
    }

    function _buildModalProductos() {
        const prev = document.getElementById('modalProdDyn');
        if (prev) prev.remove();
        const prevOv = document.getElementById('modalProdOverlay');
        if (prevOv) prevOv.remove();

        const overlay = document.createElement('div');
        overlay.id = 'modalProdOverlay';
        overlay.style.cssText = 'position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:9998;backdrop-filter:blur(2px);';
        overlay.onclick = cerrarModal;
        document.body.appendChild(overlay);

        const wrap = document.createElement('div');
        wrap.id = 'modalProdDyn';
        wrap.style.cssText = 'position:fixed;top:0;left:0;width:100%;height:100%;z-index:9999;display:flex;align-items:center;justify-content:center;padding:1rem;';
        
        wrap.innerHTML = `
        <div class="modal-content shadow-lg" style="background:#fff; border-radius: 20px; border: none; overflow: hidden; width:100%; max-width:850px; animation: modalIn 0.3s ease;">
            <div style="background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%); padding: 1.5rem 2rem; display: flex; justify-content: space-between; align-items: center; color: #fff;">
                <div class="d-flex align-items-center gap-3">
                    <div style="background: rgba(255,255,255,0.2); width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-boxes text-white"></i>
                    </div>
                    <h5 class="modal-title m-0 fw-bold text-white">Productos del Proveedor</h5>
                </div>
                <button onclick="cerrarModal()" style="background:rgba(255,255,255,0.2); border:none; color:#fff; width:36px; height:36px; border-radius:10px; cursor:pointer;">✕</button>
            </div>
            <div class="modal-body p-0">
                <div id="contenedorProductos" class="table-responsive" style="max-height: 450px;">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light sticky-top">
                            <tr style="font-size: 0.75rem; text-transform: uppercase;">
                                <th class="ps-4 py-3">Código</th>
                                <th class="py-3">Producto</th>
                                <th class="py-3">Categoría</th>
                                <th class="text-center py-3">Stock</th>
                                <th class="text-end pe-4 py-3">Precio</th>
                            </tr>
                        </thead>
                        <tbody id="listaProductosBody"></tbody>
                    </table>
                </div>
                <div id="mensajeSinProductos" class="text-center py-5 d-none">
                    <i class="fas fa-box-open text-muted mb-3" style="font-size: 3rem; opacity: 0.3;"></i>
                    <h5 class="text-muted fw-bold">Sin productos registrados</h5>
                </div>
            </div>
            <div class="modal-footer border-0 p-3">
                <button type="button" onclick="cerrarModal()" class="btn btn-light px-4 rounded-pill fw-bold">Cerrar</button>
            </div>
        </div>
        `;
        document.body.appendChild(wrap);
    }

    function cerrarModal() {
        document.getElementById('modalProvOverlay')?.remove();
        document.getElementById('modalProveedorDyn')?.remove();
        document.getElementById('modalProdOverlay')?.remove();
        document.getElementById('modalProdDyn')?.remove();
    }

    // Estilos de animación
    const style = document.createElement('style');
    style.innerHTML = `
        @keyframes modalIn {
            from { opacity: 0; transform: scale(0.95) translateY(20px); }
            to { opacity: 1; transform: scale(1) translateY(0); }
        }
    `;
    document.head.appendChild(style);

    function abrirModalNuevo() {
        _buildModalProveedor();
        document.getElementById('modalTitle').textContent = 'Nuevo Proveedor';
        document.getElementById('formProvDyn').action = BASE_URL + 'controllers/ProveedorController.php?accion=crear';
    }

    function abrirModalEditar(id, nombre, empresa, telefono, email) {
        _buildModalProveedor();
        document.getElementById('modalTitle').textContent = 'Editar Proveedor';
        document.getElementById('formProvDyn').action = BASE_URL + 'controllers/ProveedorController.php?accion=editar';
        
        document.getElementById('p_id').value = id;
        document.getElementById('p_nombre').value = nombre;
        document.getElementById('p_empresa').value = empresa;
        document.getElementById('p_telefono').value = telefono;
        document.getElementById('p_email').value = email;
    }

    function verProductos(id) {
        _buildModalProductos();
        const listaBody = document.getElementById('listaProductosBody');
        const cont = document.getElementById('contenedorProductos');
        const sin = document.getElementById('mensajeSinProductos');

        listaBody.innerHTML = '<tr><td colspan="5" class="text-center py-4"><i class="fas fa-spinner fa-spin me-2"></i> Cargando...</td></tr>';

        fetch(BASE_URL + 'controllers/ProveedorController.php?accion=listarProductos&id=' + id)
            .then(res => res.json())
            .then(data => {
                if (data.ok) {
                    if (data.productos.length === 0) {
                        cont.classList.add('d-none');
                        sin.classList.remove('d-none');
                    } else {
                        cont.classList.remove('d-none');
                        sin.classList.add('d-none');
                        listaBody.innerHTML = data.productos.map(p => `
                            <tr>
                                <td class="ps-4 fw-bold text-muted small">#${p.codigoUnico || 'S/C'}</td>
                                <td class="fw-bold">${p.nombre}</td>
                                <td><span class="badge bg-light text-dark">${p.nombre_categoria}</span></td>
                                <td class="text-center fw-bold ${p.stock_actual <= p.stock_minimo ? 'text-danger' : 'text-success'}">${p.stock_actual}</td>
                                <td class="text-end pe-4 fw-bold">$${Number(p.precio_venta).toLocaleString('es-CO')}</td>
                            </tr>
                        `).join('');
                    }
                } else {
                    throw new Error(data.error);
                }
            })
            .catch(error => {
                Swal.fire({ icon: 'error', title: 'Error', text: error.message });
            });
    }

    function confirmarEstado(url, nombre, accion) {
        const accionTexto = accion === 'activar' ? 'activar' : 'desactivar';
        const colorBtn = accion === 'activar' ? '#10b981' : '#ef4444';
        const icono = accion === 'activar' ? 'success' : 'warning';

        Swal.fire({
            title: `¿${accionTexto.charAt(0).toUpperCase() + accionTexto.slice(1)} proveedor?`,
            html: `Estás a punto de ${accionTexto} a <strong>${nombre}</strong>.<br>¿Deseas continuar?`,
            icon: icono,
            showCancelButton: true,
            confirmButtonColor: colorBtn,
            cancelButtonColor: '#64748b',
            confirmButtonText: `Sí, ${accionTexto}`,
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        });
    }
</script>