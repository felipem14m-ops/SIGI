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

$titulo = "Administración de Usuarios";

// =========================================================================
// CONEXIÓN A MODELOS (Defensivo: sidebar siempre se renderiza)
// =========================================================================
$usuarios = [];
$_error_bd = '';

try {
    require_once __DIR__ . '/../../config/database.php';
    require_once __DIR__ . '/../../models/Usuario.php';
    $db = (new Database())->conectar();
    $usuarios = (new Usuario($db))->listarTodos();
} catch (Throwable $e) {
    $_error_bd = 'No se pudieron cargar los datos. Verifique la base de datos.';
    error_log("[SIGI] usuarios.php Error: " . $e->getMessage());
}

require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidebar.php';
?>

<style>
    /* ── Tokens ── */
    :root {
        --c-primary: #2563eb;
        --c-border: #e2e8f0;
        --c-surface: #fff;
        --c-muted: #64748b;
        --c-text: #0f172a;
        --c-bg: #f8fafc;
    }

    /* ── Banner de módulo ── */
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
        background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0) 70%);
        border-radius: 50%;
    }

    .module-banner .inner {
        position: relative;
        z-index: 2;
    }

    .module-banner h2 {
        font-size: 1.85rem;
        font-weight: 800;
        margin-bottom: .2rem;
        color: #fff;
        letter-spacing: -1px;
    }

    .module-banner p {
        font-size: .95rem;
        opacity: .9;
        margin: 0;
        color: #fff;
    }

    .stat-pill {
        background: rgba(255, 255, 255, 0.15);
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 50px;
        padding: 0.5rem 1.2rem;
        font-size: .8rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: .6rem;
        color: #fff;
        backdrop-filter: blur(8px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
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
    }

    .dataTables_paginate .paginate_button:hover {
        background: #e2e8f0 !important;
    }

    .dataTables_paginate .paginate_button.current {
        background: #2563eb !important;
        color: #fff !important;
        box-shadow: 0 4px 10px rgba(37, 99, 235, 0.2);
    }

    /* ── Tabla ── */
    .module-card {
        background: var(--c-surface);
        border-radius: 16px;
        border: 1px solid var(--c-border);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, .05);
    }

    .table-custom th {
        font-size: .72rem;
        text-transform: uppercase;
        letter-spacing: .5px;
        color: var(--c-muted);
        font-weight: 700;
        border-bottom: 2px solid var(--c-border);
        background: var(--c-bg);
        padding: .9rem 1rem;
    }

    .table-custom td {
        padding: .85rem 1rem;
        vertical-align: middle;
        font-size: .88rem;
        color: #334155;
        border-bottom: 1px solid #f1f5f9;
    }

    .table-custom tbody tr:hover td {
        background: #f8fafc;
    }

    /* ── Avatares y badges ── */
    .avatar-sm {
        width: 38px;
        height: 38px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        color: #fff;
        font-size: .8rem;
    }

    .badge-role {
        padding: .3rem .75rem;
        border-radius: 50px;
        font-size: .72rem;
        font-weight: 700;
    }

    .role-admin {
        background: #dbeafe;
        color: #1d4ed8;
    }

    .role-emp {
        background: #f1f5f9;
        color: #475569;
    }

    .badge-estado {
        padding: .3rem .75rem;
        border-radius: 50px;
        font-size: .72rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: .3rem;
    }

    .estado-activo {
        background: #dcfce7;
        color: #16a34a;
    }

    .estado-inactivo {
        background: #fee2e2;
        color: #dc2626;
    }

    /* ── Botones acción ── */
    .btn-accion {
        width: 34px;
        height: 34px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        border: none;
        font-size: .82rem;
        transition: all .2s;
        cursor: pointer;
    }

    .btn-accion-editar {
        background: #eff6ff;
        color: #3b82f6;
    }

    .btn-accion-editar:hover {
        background: #dbeafe;
        color: #2563eb;
    }

    .btn-accion-toggle {
        background: #fef3c7;
        color: #d97706;
    }

    .btn-accion-toggle:hover {
        background: #fde68a;
        color: #b45309;
    }

    .btn-accion-activar {
        background: #dcfce7;
        color: #16a34a;
    }

    .btn-accion-activar:hover {
        background: #bbf7d0;
        color: #15803d;
    }

    /* ── DataTables ── */
    .dataTables_wrapper .dataTables_filter input {
        border-radius: 10px;
        border: 1px solid var(--c-border);
        padding: .45rem .9rem;
        font-size: .85rem;
        background: var(--c-bg);
    }

    .dataTables_wrapper .dataTables_filter input:focus {
        border-color: var(--c-primary);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, .1);
        outline: none;
    }

    .dataTables_wrapper .dataTables_length select {
        border-radius: 8px;
        border: 1px solid var(--c-border);
    }

    .dataTables_wrapper .dataTables_info {
        font-size: .82rem;
        color: var(--c-muted);
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button {
        border-radius: 8px !important;
        border: none !important;
        margin: 0 2px;
        font-size: .82rem;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: var(--c-primary) !important;
        color: #fff !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: #eff6ff !important;
        color: var(--c-primary) !important;
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
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
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
</style>

<div class="container-fluid py-2">

    <!-- Banner de módulo -->
    <div class="module-banner">
        <div class="inner d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <div class="d-flex align-items-center gap-2 mb-2 text-white">
                    <div style="width:40px;height:40px;border-radius:12px;background:rgba(255,255,255,.2);display:flex;align-items:center;justify-content:center;font-size:1.2rem;">
                        <i class="fas fa-users text-white"></i>
                    </div>
                    <span style="font-size:.8rem;font-weight:700;opacity:.9;text-transform:uppercase;letter-spacing:.5px;color:#fff;">Administración</span>
                </div>
                <h2 class="text-white" style="font-family: 'Poppins', sans-serif; letter-spacing: -1px;">Gestión de Usuarios</h2>
                <p class="text-white opacity-90">Controla los accesos y permisos del sistema</p>
            </div>
            <div class="d-flex align-items-center gap-3 flex-wrap">
                <span class="stat-pill text-white"><i class="fas fa-user-check"></i> <?= count($usuarios) ?> registrados</span>
                <button class="btn fw-bold px-4 py-2 rounded-3 shadow-sm"
                    style="background:#fff;color:#2563eb;font-size:.88rem;border:none;"
                    onclick="abrirModalNuevo()">
                    <i class="fas fa-user-plus me-2"></i> Nuevo Usuario
                </button>
            </div>
        </div>
    </div>

    <!-- Barra de Filtros -->
    <div class="module-card p-4 mb-4">
        <div class="row g-3 align-items-end">
            <div class="col-md-5">
                <label class="form-label small fw-bold text-muted text-uppercase mb-2" style="letter-spacing: 0.5px;">Usuario / Correo</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0" style="border-radius: 12px 0 0 12px;"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" id="filtroUsuario" class="form-control bg-light border-start-0 shadow-none" placeholder="Ej. Juan Pérez..." style="border-radius: 0 12px 12px 0;">
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold text-muted text-uppercase mb-2" style="letter-spacing: 0.5px;">Rol de Sistema</label>
                <select id="filtroRol" class="form-select bg-light shadow-none" style="border-radius: 12px;">
                    <option value="">Todos los roles</option>
                    <option value="administrador">Administrador</option>
                    <option value="empleado">Empleado</option>
                </select>
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="button" onclick="aplicarFiltros()" class="btn fw-bold px-4 rounded-pill shadow-sm text-white w-100" style="background: linear-gradient(135deg, #1e3a8a, #2563eb); border: none;">
                    <i class="fas fa-filter me-2"></i> Aplicar Filtros
                </button>
                <button id="btnLimpiarFiltros" class="btn btn-light px-4 fw-bold rounded-pill border text-muted w-100">
                    <i class="fas fa-undo me-2"></i> Limpiar
                </button>
            </div>
        </div>
    </div>

    <div class="module-card overflow-hidden">
        <div class="table-responsive p-3">
            <table id="tablaUsuarios" class="table table-hover table-custom mb-0 w-100">
                <thead>
                    <tr>
                        <th>Usuario</th>
                        <th>Correo Electrónico</th>
                        <th>Identificación</th>
                        <th>Rol</th>
                        <th>Último Acceso</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($usuarios)): ?>
                        <?php foreach ($usuarios as $usr): ?>
                            <tr class="usuario-row" 
                                data-nombre="<?= strtolower(htmlspecialchars($usr['nombre'] . ' ' . $usr['email'])) ?>"
                                data-rol="<?= strtolower($usr['nombre_rol'] ?? '') ?>"
                                data-estado="<?= $usr['activo'] ? '1' : '0' ?>">
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar-sm" style="background: <?= strtolower($usr['nombre_rol'] ?? '') === 'administrador' ? '#2563eb' : '#0f172a' ?>;">
                                            <?= strtoupper(substr($usr['nombre'] ?? 'U', 0, 1)) ?>
                                        </div>
                                        <div class="fw-bold text-dark"><?= htmlspecialchars($usr['nombre']) ?></div>
                                    </div>
                                </td>
                                <td class="text-muted"><?= htmlspecialchars($usr['email']) ?></td>
                                <td class="text-muted"><?= htmlspecialchars($usr['numeroIdentificacion'] ?? '-') ?></td>
                                <td>
                                    <?php if (strtolower($usr['nombre_rol'] ?? '') === 'administrador'): ?>
                                        <span class="badge-role role-admin"><i class="fas fa-shield-alt me-1"></i> Administrador</span>
                                    <?php else: ?>
                                        <span class="badge-role role-emp"><i class="fas fa-user me-1"></i> Empleado</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-muted" style="font-size: 0.85rem;">
                                    <?= isset($usr['fecha_creacion']) ? date('d/m/Y H:i', strtotime($usr['fecha_creacion'])) : 'Sin registro' ?>
                                </td>
                                <td>
                                    <?php if ($usr['activo']): ?>
                                        <span class="badge-estado estado-activo"><i class="fas fa-circle" style="font-size: 0.45rem;"></i> Activo</span>
                                    <?php else: ?>
                                        <span class="badge-estado estado-inactivo"><i class="fas fa-circle" style="font-size: 0.45rem;"></i> Inactivo</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-2">
                                        <button class="btn-accion btn-accion-editar btn-editar-usuario" title="Editar Información"
                                            data-id="<?= $usr['id_usuario'] ?>"
                                            data-nombre="<?= htmlspecialchars($usr['nombre'], ENT_QUOTES) ?>"
                                            data-email="<?= htmlspecialchars($usr['email'], ENT_QUOTES) ?>"
                                            data-identificacion="<?= htmlspecialchars($usr['numeroIdentificacion'] ?? '', ENT_QUOTES) ?>"
                                            data-rol="<?= strtolower($usr['nombre_rol'] ?? '') ?>">
                                            <i class="fas fa-edit"></i>
                                        </button>

                                        <?php if ($usr['activo']): ?>
                                            <button class="btn-accion btn-accion-desactivar btn-toggle-estado" title="Desactivar Cuenta"
                                                data-url="<?= $base_url ?>controllers/AdmiUsuarioController.php?accion=desactivar&id=<?= $usr['id_usuario'] ?>"
                                                data-nombre="<?= htmlspecialchars($usr['nombre'], ENT_QUOTES) ?>"
                                                data-accion="desactivar">
                                                <i class="fas fa-user-slash"></i>
                                            </button>
                                        <?php else: ?>
                                            <button class="btn-accion btn-accion-activar btn-toggle-estado" title="Activar Cuenta"
                                                data-url="<?= $base_url ?>controllers/AdmiUsuarioController.php?accion=activar&id=<?= $usr['id_usuario'] ?>"
                                                data-nombre="<?= htmlspecialchars($usr['nombre'], ENT_QUOTES) ?>"
                                                data-accion="activar">
                                                <i class="fas fa-user-check"></i>
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

<?php echo '</section></main></div>';
require_once __DIR__ . '/../layouts/footer.php'; ?>

<script>
    const BASE_URL = '<?= $base_url ?>';

    // =====================================================================
    // DATATABLE
    // =====================================================================
    $(document).ready(function() {
        // Destruir instancia previa si existe (evita "Cannot reinitialise DataTable")
        if ($.fn.DataTable.isDataTable('#tablaUsuarios')) {
            $('#tablaUsuarios').DataTable().destroy();
        }

        $('#tablaUsuarios').DataTable({
            searching: false,
            order: [
                [4, 'desc']
            ],
            pageLength: 20,
            dom: 'rt<"px-4 py-4 d-flex justify-content-between align-items-center"ip>',
            columnDefs: [{
                orderable: false,
                targets: [6]
            }],
            language: {
                lengthMenu: "Mostrar _MENU_",
                info: "_TOTAL_ usuarios",
                paginate: {
                    previous: "Ant.",
                    next: "Sig."
                },
                zeroRecords: "No se encontraron usuarios"
            }
        });

        // Event delegation — toggle estado
        $('#tablaUsuarios').on('click', '.btn-toggle-estado', function(e) {
            e.preventDefault();
            confirmarAccion($(this).data('url'), $(this).data('nombre'), $(this).data('accion'));
        });

        // Event delegation — editar usuario
        $('#tablaUsuarios').on('click', '.btn-editar-usuario', function(e) {
            e.preventDefault();
            abrirModalEditar(
                $(this).data('id'),
                $(this).data('nombre'),
                $(this).data('email'),
                $(this).data('identificacion'),
                $(this).data('rol')
            );
        });
    });

    // =====================================================================
    // FILTROS PREMIUM (Diseño como proveedores)
    // =====================================================================
    function aplicarFiltros() {
        const busqueda = document.getElementById('filtroUsuario').value.toLowerCase().trim();
        const rol = document.getElementById('filtroRol').value.toLowerCase();
        const rows = document.querySelectorAll('.usuario-row');
        
        let visibles = 0;
        rows.forEach(row => {
            const okNombre = !busqueda || row.dataset.nombre.includes(busqueda);
            const okRol = !rol || row.dataset.rol === rol;
            
            const visible = okNombre && okRol;
            row.style.display = visible ? '' : 'none';
            if (visible) visibles++;
        });
    }

    document.getElementById('btnLimpiarFiltros')?.addEventListener('click', function() {
        document.getElementById('filtroUsuario').value = '';
        document.getElementById('filtroRol').value = '';
        aplicarFiltros();
    });

    // Vincular inputs al filtrado en tiempo real (opcional, pero mejora UX)
    ['filtroUsuario', 'filtroRol'].forEach(id => {
        document.getElementById(id)?.addEventListener('input', aplicarFiltros);
        document.getElementById(id)?.addEventListener('change', aplicarFiltros);
    });

    // =====================================================================
    // CONSTRUIR MODAL DINÁMICAMENTE
    // =====================================================================
    function _buildModal() {
        // Eliminar instancias previas si existen
        const prev = document.getElementById('modalUsuarioDyn');
        const prevOv = document.getElementById('modalOverlayDyn');
        if (prev) prev.remove();
        if (prevOv) prevOv.remove();

        // Overlay
        const overlay = document.createElement('div');
        overlay.id = 'modalOverlayDyn';
        overlay.style.cssText = 'position:fixed;top:0;right:0;bottom:0;left:0;background:rgba(0,0,0,.5);z-index:9998;backdrop-filter:blur(2px);animation:fadeIn 0.3s ease;';
        overlay.onclick = cerrarModal;
        document.body.appendChild(overlay);

        // Modal wrapper
        const wrap = document.createElement('div');
        wrap.id = 'modalUsuarioDyn';
        wrap.style.cssText = 'position:fixed;top:0;right:0;bottom:0;left:0;z-index:9999;display:flex;align-items:center;justify-content:center;padding:1rem;overflow-y:auto;';

        const modalContent = document.createElement('div');
        modalContent.style.cssText = 'background:#fff;border-radius:20px;overflow:hidden;width:100%;max-width:650px;box-shadow:0 25px 70px rgba(0,0,0,.3);animation:fadeInUp .3s ease;';
        
        modalContent.innerHTML = `
        <!-- Cabecera Premium -->
        <div style="background:linear-gradient(135deg,#1e3a8a,#2563eb);padding:1.5rem 2rem;
                    display:flex;align-items:center;justify-content:space-between;color:#fff;">
            <div>
                <h5 id="modalTitulo" style="color:#ffffff !important;font-weight:800;margin:0;font-size:1.4rem;font-family:'Poppins',sans-serif;">
                    <i class="fas fa-user-plus me-2 text-white"></i> Nuevo Usuario
                </h5>
                <p style="color:rgba(255,255,255,0.9);margin:0;font-size:0.75rem;text-transform:uppercase;letter-spacing:1px;font-weight:600;">Configuración de acceso</p>
            </div>
            <button onclick="cerrarModal()" type="button"
                    style="background:rgba(255,255,255,0.2);border:none;color:#fff;
                        width:36px;height:36px;border-radius:10px;cursor:pointer;font-size:1.1rem;transition:0.2s;">
                ✕
            </button>
        </div>

        <!-- Formulario -->
        <form id="formUsuarioDyn" method="POST"
              action="${BASE_URL}controllers/AdmiUsuarioController.php?accion=crear"
              novalidate style="padding:2rem;">
            <input type="hidden" id="u_id" name="id_usuario" value="">

            <div class="row g-4 mb-4">
                <div class="col-md-7">
                    <label class="form-label fw-bold text-dark mb-2" style="font-size:.85rem;">
                        <i class="fas fa-user me-1 text-primary"></i> Nombre Completo <span class="text-danger">*</span>
                    </label>
                    <input type="text" id="u_nombre" name="nombres" class="form-control form-control-lg border-2 shadow-sm"
                           placeholder="Juan Pérez García" required maxlength="100"
                           style="border-radius:12px;font-size:.95rem;">
                </div>
                <div class="col-md-5">
                    <label class="form-label fw-bold text-dark mb-2" style="font-size:.85rem;">
                        <i class="fas fa-id-card me-1 text-primary"></i> N° Identificación
                    </label>
                    <input type="text" id="u_identificacion" name="numeroIdentificacion"
                           class="form-control form-control-lg border-2 shadow-sm" placeholder="123456789" maxlength="20"
                           style="border-radius:12px;font-size:.95rem;">
                </div>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-md-7">
                    <label class="form-label fw-bold text-dark mb-2" style="font-size:.85rem;">
                        <i class="fas fa-envelope me-1 text-primary"></i> Correo Institucional <span class="text-danger">*</span>
                    </label>
                    <input type="email" id="u_email" name="email" class="form-control form-control-lg border-2 shadow-sm"
                           placeholder="correo@empresa.com" required maxlength="100"
                           style="border-radius:12px;font-size:.95rem;">
                </div>
                <div class="col-md-5">
                    <label class="form-label fw-bold text-dark mb-2" style="font-size:.85rem;">
                        <i class="fas fa-shield-alt me-1 text-primary"></i> Rol de Sistema <span class="text-danger">*</span>
                    </label>
                    <select id="u_rol" name="rol" class="form-select form-select-lg border-2 shadow-sm" required
                            style="border-radius:12px;font-size:.95rem;cursor:pointer;">
                        <option value="" disabled selected>Seleccionar...</option>
                        <option value="administrador">Administrador</option>
                        <option value="empleado">Empleado</option>
                    </select>
                </div>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-12">
                    <label class="form-label fw-bold text-dark mb-2" style="font-size:.85rem;">
                        <i class="fas fa-lock me-1 text-primary"></i>
                        Contraseña de Acceso <span class="text-danger" id="passRequired">*</span>
                    </label>
                    <div class="input-group">
                        <input type="password" id="u_contrasena" name="contrasena"
                               class="form-control form-control-lg border-2 border-end-0 shadow-sm"
                               placeholder="Mínimo 6 caracteres" required minlength="6"
                               style="border-radius:12px 0 0 12px;font-size:.95rem;">
                        <button type="button" onclick="togglePass()" class="btn btn-lg border-2 border-start-0 bg-white shadow-sm"
                                style="border-radius:0 12px 12px 0;border-color:#dee2e6;color:#64748b;">
                            <i class="fas fa-eye" id="iconoPass"></i>
                        </button>
                    </div>
                    <div id="passHint" class="form-text text-muted mt-2" style="font-size:0.75rem;">
                        <i class="fas fa-info-circle me-1"></i> Obligatoria para nuevos usuarios.
                    </div>
                </div>
            </div>

            <!-- Footer con botones de diseño Premium -->
            <div class="d-flex justify-content-end gap-3 pt-4 border-top">
                <button type="button" onclick="cerrarModal()"
                        class="btn btn-light px-4 py-2 fw-bold text-muted border-0"
                        style="border-radius:12px;font-size:.9rem;background:#f1f5f9;">
                    CANCELAR
                </button>
                <button type="submit" class="btn btn-primary px-5 py-2 fw-bold"
                        style="border-radius:12px;font-size:.9rem;background:linear-gradient(135deg,#1e3a8a,#2563eb);border:none;box-shadow:0 10px 20px rgba(37,99,235,0.2);">
                    GUARDAR CAMBIOS
                </button>
            </div>
        </form>`;

        wrap.appendChild(modalContent);
        document.body.appendChild(wrap);

        // Validación mejorada al enviar
        document.getElementById('formUsuarioDyn').addEventListener('submit', function(e) {
            if (!this.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
                Swal.fire({
                    icon: 'warning',
                    title: 'Formulario incompleto',
                    text: 'Por favor, rellena todos los campos obligatorios (*)',
                    confirmButtonColor: '#2563eb'
                });
            }
            this.classList.add('was-validated');
        });
    }

    // =====================================================================
    // ABRIR — NUEVO
    // =====================================================================
    function abrirModalNuevo() {
        _buildModal();
        document.getElementById('formUsuarioDyn').action =
            BASE_URL + 'controllers/AdmiUsuarioController.php?accion=crear';
    }

    // =====================================================================
    // ABRIR — EDITAR
    // =====================================================================
    function abrirModalEditar(id, nombre, email, identificacion, rol) {
        _buildModal();

        // Cambiar título y estilo del encabezado para edición
        const titulo = document.getElementById('modalTitulo');
        titulo.innerHTML = '<i class="fas fa-user-edit me-2 text-white"></i> Editar Perfil de Usuario';
        titulo.parentElement.parentElement.style.background = 'linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%)';

        // Cargar datos en los campos
        document.getElementById('u_id').value = id;
        document.getElementById('u_nombre').value = nombre;
        document.getElementById('u_email').value = email;
        document.getElementById('u_identificacion').value = identificacion;
        document.getElementById('u_rol').value = rol;

        // El correo suele ser el identificador único, lo dejamos solo lectura para integridad
        const emailInput = document.getElementById('u_email');
        emailInput.readOnly = true;
        emailInput.style.background = '#f8fafc';
        emailInput.title = 'El correo electrónico no puede ser modificado por seguridad.';

        // Configuración de contraseña opcional
        const pass = document.getElementById('u_contrasena');
        pass.removeAttribute('required');
        pass.removeAttribute('minlength');
        pass.placeholder = '•••••••• (Dejar en blanco para no cambiar)';

        document.getElementById('passRequired').style.display = 'none';
        document.getElementById('passHint').innerHTML =
            '<i class="fas fa-info-circle me-1 text-primary"></i> Solo escribe una nueva contraseña si deseas cambiar la actual.';

        // Cambiar acción del formulario y texto del botón
        const form = document.getElementById('formUsuarioDyn');
        form.action = BASE_URL + 'controllers/AdmiUsuarioController.php?accion=editar';

        const btnSubmit = form.querySelector('button[type="submit"]');
        btnSubmit.innerHTML = '<i class="fas fa-sync-alt me-2"></i> ACTUALIZAR USUARIO';
        btnSubmit.style.background = 'linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%)';
    }

    // =====================================================================
    // CERRAR
    // =====================================================================
    function cerrarModal() {
        const m = document.getElementById('modalUsuarioDyn');
        const o = document.getElementById('modalOverlayDyn');
        if (m) m.remove();
        if (o) o.remove();
    }

    // =====================================================================
    // TOGGLE CONTRASEÑA
    // =====================================================================
    function togglePass() {
        const input = document.getElementById('u_contrasena');
        const icon = document.getElementById('iconoPass');
        if (!input) return;
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    }

    // =====================================================================
    // CONFIRMAR ACTIVAR / DESACTIVAR
    // =====================================================================
    function confirmarAccion(url, nombre, accion) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: `¿${accion.charAt(0).toUpperCase() + accion.slice(1)} usuario?`,
                text: `Se va a ${accion} a "${nombre}".`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: accion === 'desactivar' ? '#dc2626' : '#16a34a',
                cancelButtonColor: '#64748b',
                confirmButtonText: `Sí, ${accion}`,
                cancelButtonText: 'Cancelar'
            }).then(r => {
                if (r.isConfirmed) window.location.href = url;
            });
        } else {
            if (confirm(`¿Desea ${accion} al usuario "${nombre}"?`)) window.location.href = url;
        }
    }
</script>

<style>
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }

    /* Estilos adicionales para centrado perfecto del modal */
    #modalUsuarioDyn {
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        margin-left: 0 !important; /* Ignorar el margin del contenedor principal */
        left: 0 !important; /* Forzar posición desde el borde izquierdo de la ventana */
        right: 0 !important;
        top: 0 !important;
        bottom: 0 !important;
    }

    #modalOverlayDyn {
        margin-left: 0 !important; /* Ignorar el margin del contenedor principal */
        left: 0 !important; /* Forzar posición desde el borde izquierdo de la ventana */
        right: 0 !important;
        top: 0 !important;
        bottom: 0 !important;
    }

    /* Asegurar que el modal sea responsive */
    @media (max-width: 768px) {
        #modalUsuarioDyn {
            padding: 0.5rem !important;
        }
        #modalUsuarioDyn > div {
            max-width: 100% !important;
            border-radius: 12px !important;
        }
    }
</style>