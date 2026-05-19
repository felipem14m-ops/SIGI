<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) { header("Location: Usuario/login.php"); exit; }

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
    .module-card { background: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); }
    .table-custom th { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b; font-weight: 700; border-bottom: 2px solid #e2e8f0; background: #f8fafc; padding: 1rem; }
    .table-custom td { padding: 0.85rem 1rem; vertical-align: middle; font-size: 0.9rem; color: #334155; border-bottom: 1px solid #f1f5f9; }
    
    .avatar-sm { width: 38px; height: 38px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-weight: 700; color: white; font-size: 0.8rem; }
    .badge-role { padding: 0.35rem 0.75rem; border-radius: 50px; font-size: 0.75rem; font-weight: 600; }
    .role-admin { background: #dbeafe; color: #1d4ed8; }
    .role-emp { background: #f1f5f9; color: #475569; }
    .badge-estado { padding: 0.35rem 0.75rem; border-radius: 50px; font-size: 0.75rem; font-weight: 600; display: inline-flex; align-items: center; gap: 0.3rem; }
    .estado-activo { background: #dcfce7; color: #16a34a; }
    .estado-inactivo { background: #fee2e2; color: #dc2626; }

    .btn-accion { width: 34px; height: 34px; padding: 0; display: inline-flex; align-items: center; justify-content: center; border-radius: 10px; border: none; font-size: 0.85rem; transition: all 0.2s; }
    .btn-accion-editar { background: #eff6ff; color: #3b82f6; }
    .btn-accion-editar:hover { background: #dbeafe; color: #2563eb; }
    .btn-accion-toggle { background: #fef3c7; color: #d97706; }
    .btn-accion-toggle:hover { background: #fde68a; color: #b45309; }
    .btn-accion-activar { background: #dcfce7; color: #16a34a; }
    .btn-accion-activar:hover { background: #bbf7d0; color: #15803d; }

    /* DataTables custom styling */
    .dataTables_wrapper .dataTables_filter input { border-radius: 10px; border: 1px solid #e2e8f0; padding: 0.5rem 1rem; font-size: 0.9rem; background: #f8fafc; }
    .dataTables_wrapper .dataTables_filter input:focus { border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37,99,235,0.1); outline: none; }
    .dataTables_wrapper .dataTables_length select { border-radius: 8px; border: 1px solid #e2e8f0; padding: 0.4rem 2rem 0.4rem 0.6rem; font-size: 0.85rem; }
    .dataTables_wrapper .dataTables_info { font-size: 0.85rem; color: #64748b; }
    .dataTables_wrapper .dataTables_paginate .paginate_button { border-radius: 8px !important; border: none !important; margin: 0 2px; font-size: 0.85rem; }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current { background: #2563eb !important; color: white !important; border: none !important; }
    .dataTables_wrapper .dataTables_paginate .paginate_button:hover { background: #eff6ff !important; color: #2563eb !important; border: none !important; }
</style>

<div class="container-fluid py-2">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h2 class="h4 mb-1 fw-bold text-dark">Cuentas de Empleados</h2>
            <p class="text-muted mb-0" style="font-size: 0.9rem;">
                Gestiona accesos y permisos al sistema.
                <span class="badge bg-primary rounded-pill ms-2"><?= count($usuarios) ?> usuarios</span>
            </p>
        </div>
        <button class="btn btn-primary rounded-3 shadow-sm" style="background-color: #2563eb; border: none; font-weight: 500;" data-bs-toggle="modal" data-bs-target="#modalUsuario">
            <i class="fas fa-user-plus me-2"></i> Nuevo Usuario
        </button>
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
                        <tr>
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
                                <?= isset($usr['fecha_actualizacion']) ? date('d/m/Y H:i', strtotime($usr['fecha_actualizacion'])) : 'Sin registro' ?>
                            </td>
                            <td>
                                <?php if ($usr['activo']): ?>
                                    <span class="badge-estado estado-activo"><i class="fas fa-circle" style="font-size: 0.45rem;"></i> Activo</span>
                                <?php else: ?>
                                    <span class="badge-estado estado-inactivo"><i class="fas fa-circle" style="font-size: 0.45rem;"></i> Inactivo</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-1">
                                    <button class="btn-accion btn-accion-editar btn-editar-usuario" title="Editar usuario"
                                        data-id="<?= $usr['id_usuario'] ?>"
                                        data-nombre="<?= htmlspecialchars($usr['nombre'], ENT_QUOTES) ?>"
                                        data-email="<?= htmlspecialchars($usr['email'], ENT_QUOTES) ?>"
                                        data-identificacion="<?= htmlspecialchars($usr['numeroIdentificacion'] ?? '', ENT_QUOTES) ?>">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <?php if ($usr['activo']): ?>
                                        <button class="btn-accion btn-accion-toggle btn-toggle-estado" title="Desactivar usuario"
                                            data-url="<?= $base_url ?>controllers/AdmiUsuarioController.php?accion=toggleEstado&id=<?= $usr['id_usuario'] ?>&estado=<?= $usr['activo'] ?>"
                                            data-nombre="<?= htmlspecialchars($usr['nombre'], ENT_QUOTES) ?>"
                                            data-accion="desactivar">
                                            <i class="fas fa-ban"></i>
                                        </button>
                                    <?php else: ?>
                                        <button class="btn-accion btn-accion-activar btn-toggle-estado" title="Activar usuario"
                                            data-url="<?= $base_url ?>controllers/AdmiUsuarioController.php?accion=toggleEstado&id=<?= $usr['id_usuario'] ?>&estado=<?= $usr['activo'] ?>"
                                            data-nombre="<?= htmlspecialchars($usr['nombre'], ENT_QUOTES) ?>"
                                            data-accion="activar">
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

<!-- =========================================================================
   MODAL: CREAR/EDITAR USUARIO
   ========================================================================= -->
<div class="modal fade" id="modalUsuario" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-0 pb-0 px-4 pt-4">
                <h5 class="modal-title fw-bold" id="modalUsuarioTitulo">
                    <i class="fas fa-user-plus me-2 text-primary"></i> Nuevo Usuario
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formUsuario" method="POST" action="<?= $base_url ?>controllers/AdmiUsuarioController.php?accion=crear">
                <div class="modal-body px-4 py-3">
                    <input type="hidden" name="id_usuario" id="input_id_usuario">
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold" style="font-size: 0.85rem; color: #475569;">Nombre Completo</label>
                        <input type="text" name="nombres" id="input_nombre" class="form-control bg-light rounded-3" placeholder="Ej: Carlos Pérez" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold" style="font-size: 0.85rem; color: #475569;">Correo Electrónico</label>
                        <input type="email" name="email" id="input_email" class="form-control bg-light rounded-3" placeholder="correo@ejemplo.com" required>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-bold" style="font-size: 0.85rem; color: #475569;">N° Identificación</label>
                            <input type="text" name="numeroIdentificacion" id="input_identificacion" class="form-control bg-light rounded-3" placeholder="1234567890">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold" style="font-size: 0.85rem; color: #475569;">Rol</label>
                            <select name="rol" id="input_rol" class="form-select bg-light rounded-3" required>
                                <option value="">Seleccionar...</option>
                                <option value="1">Administrador</option>
                                <option value="2">Empleado</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-bold" style="font-size: 0.85rem; color: #475569;">Contraseña</label>
                        <input type="password" name="contrasena" id="input_contrasena" class="form-control bg-light rounded-3" placeholder="Mínimo 6 caracteres" minlength="6">
                        <small class="text-muted" id="passHint">Obligatoria para nuevos usuarios.</small>
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
// =========================================================================
// INICIALIZACIÓN DE DATATABLE PARA USUARIOS
// =========================================================================
$(document).ready(function() {
    // Inicializar DataTable
    const table = $('#tablaUsuarios').DataTable({
        order: [[4, 'desc']],
        columnDefs: [
            { orderable: false, targets: [6] }
        ]
    });
    
    // =========================================================================
    // EVENT DELEGATION: Botón Editar Usuario
    // =========================================================================
    $('#tablaUsuarios').on('click', '.btn-editar-usuario', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const id = $(this).data('id');
        const nombre = $(this).data('nombre');
        const email = $(this).data('email');
        const identificacion = $(this).data('identificacion');
        
        editarUsuario(id, nombre, email, identificacion);
        return false;
    });
    
    // =========================================================================
    // EVENT DELEGATION: Botón Toggle Estado (Activar/Desactivar)
    // =========================================================================
    $('#tablaUsuarios').on('click', '.btn-toggle-estado', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const url = $(this).data('url');
        const nombre = $(this).data('nombre');
        const accion = $(this).data('accion');
        
        confirmarAccion(url, nombre, accion);
        return false;
    });
});

// =========================================================================
// FUNCIÓN: EDITAR USUARIO (Rellena el modal con datos existentes)
// =========================================================================
function editarUsuario(id, nombre, email, identificacion) {
    document.getElementById('modalUsuarioTitulo').innerHTML = '<i class="fas fa-edit me-2 text-primary"></i> Editar Usuario';
    document.getElementById('input_id_usuario').value = id;
    document.getElementById('input_nombre').value = nombre;
    document.getElementById('input_email').value = email;
    document.getElementById('input_identificacion').value = identificacion;
    document.getElementById('input_contrasena').removeAttribute('required');
    document.getElementById('passHint').textContent = 'Dejar en blanco para no cambiar la contraseña.';
    document.getElementById('formUsuario').action = '<?= $base_url ?>controllers/AdmiUsuarioController.php?accion=editar';
    
    new bootstrap.Modal(document.getElementById('modalUsuario')).show();
}

// Resetear modal al cerrar
document.getElementById('modalUsuario').addEventListener('hidden.bs.modal', function() {
    document.getElementById('modalUsuarioTitulo').innerHTML = '<i class="fas fa-user-plus me-2 text-primary"></i> Nuevo Usuario';
    document.getElementById('formUsuario').reset();
    document.getElementById('input_id_usuario').value = '';
    document.getElementById('input_contrasena').setAttribute('required', 'required');
    document.getElementById('passHint').textContent = 'Obligatoria para nuevos usuarios.';
    document.getElementById('formUsuario').action = '<?= $base_url ?>controllers/AdmiUsuarioController.php?accion=crear';
});
</script>
