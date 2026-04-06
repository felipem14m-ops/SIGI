admin.php:
<?php
session_start();

if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'administrador') {
    header("Location: ../usuarios/login.php");
    exit;
}

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Usuario.php';

$database = new Database();
$db = $database->conectar();
$usuarioModel = new Usuario($db);
$usuarios = $usuarioModel->obtenerTodos();

$titulo = "Dashboard Administrador";
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidebar.php';
?>

<div class="space-y-8">
    <div class="bg-white rounded-2xl shadow p-6 border border-slate-200">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-4xl font-light text-slate-800">Gestión de <span class="font-bold">Usuarios</span></h2>
            <button onclick="openModal('modalCrear')" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-3 rounded-xl shadow inline-block">
                Agregar Usuario
            </button>
        </div>

        <?php if (isset($_SESSION['alert'])): ?>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: '<?= htmlspecialchars($_SESSION['alert']['icon']) ?>',
                        title: '<?= htmlspecialchars($_SESSION['alert']['title']) ?>',
                        text: '<?= htmlspecialchars($_SESSION['alert']['text']) ?>',
                        confirmButtonText: 'Aceptar'
                    });
                });
            </script>
            <?php unset($_SESSION['alert']); ?>
        <?php endif; ?>

        <div class="overflow-x-auto">
            <table class="w-full text-left border border-slate-200 rounded-xl overflow-hidden">
                <thead class="bg-slate-100 text-slate-700">
                    <tr>
                        <th class="p-4">Nombre Completo</th>
                        <th class="p-4">Email</th>
                        <th class="p-4">Rol</th>
                        <th class="p-4">Estado</th>
                        <th class="p-4 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    <?php foreach ($usuarios as $ruben): ?>
                    <tr class="<?= $ruben['activo'] == 0 ? 'bg-red-50 opacity-75' : '' ?>">
                        <td class="p-4"><?= htmlspecialchars($ruben['nombres'] . ' ' . $ruben['apellidos']) ?></td>
                        <td class="p-4"><?= htmlspecialchars($ruben['email']) ?></td>
                        <td class="p-4 capitalize"><?= htmlspecialchars($ruben['rol']) ?></td>
                        <td class="p-4">
                            <?php if ($ruben['activo'] == 1): ?>
                                <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded border border-green-400">Activo</span>
                            <?php else: ?>
                                <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded border border-red-400">Inactivo</span>
                            <?php endif; ?>
                        </td>
                        <td class="p-4 text-center space-x-2">
                            <button type="button" onclick="openEditModal(<?= htmlspecialchars(json_encode($ruben)) ?>)" class="px-3 py-1 rounded border text-blue-600 border-blue-300 hover:bg-blue-50" title="Editar">
                                <i class="fas fa-pen"></i>
                            </button>
                            <?php if ($ruben['activo'] == 1): ?>
                                <a href="../../controllers/AdminUsuarioController.php?accion=toggleEstado&id=<?= $ruben['id_usuario'] ?>&estado=1" class="px-3 py-1 rounded border text-red-600 border-red-300 hover:bg-red-50" title="Desactivar" onclick="return confirm('¿Seguro que desea desactivar este usuario?');">
                                    <i class="fas fa-ban"></i>
                                </a>
                            <?php else: ?>
                                <a href="../../controllers/AdminUsuarioController.php?accion=toggleEstado&id=<?= $ruben['id_usuario'] ?>&estado=0" class="px-3 py-1 rounded border text-green-600 border-green-300 hover:bg-green-50" title="Activar" onclick="return confirm('¿Seguro que desea activar este usuario?');">
                                    <i class="fas fa-check"></i>
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    
                    <?php if (empty($usuarios)): ?>
                    <tr>
                        <td colspan="5" class="p-4 text-center text-slate-500">No hay usuarios registrados.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow p-6 border border-slate-200">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-3xl font-light text-slate-800">Últimos Reportes</h3>
            <button class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-5 py-3 rounded-xl border">
                Consultar Reporte
            </button>
        </div>

        <div class="space-y-4">
            <div class="flex items-center justify-between border rounded-xl p-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-lg bg-slate-100 flex items-center justify-center text-blue-700">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <div>
                        <p class="font-semibold text-slate-800">Análisis General de Usuarios</p>
                        <p class="text-sm text-slate-500">Reporte administrativo del sistema</p>
                    </div>
                </div>
                <button class="bg-slate-100 hover:bg-slate-200 px-4 py-2 rounded-lg border">Ver</button>
            </div>

            <div class="flex items-center justify-between border rounded-xl p-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-lg bg-slate-100 flex items-center justify-center text-blue-700">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <div>
                        <p class="font-semibold text-slate-800">Reporte de Rendimiento</p>
                        <p class="text-sm text-slate-500">Resumen académico institucional</p>
                    </div>
                </div>
                <button class="bg-slate-100 hover:bg-slate-200 px-4 py-2 rounded-lg border">Abrir</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Crear Usuario -->
<div id="modalCrear" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl overflow-hidden">
        <div class="flex justify-between items-center p-6 border-b border-slate-200">
            <h3 class="text-2xl font-bold text-slate-800">Agregar Usuario</h3>
            <button onclick="closeModal('modalCrear')" class="text-slate-400 hover:text-slate-600 transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form action="../../controllers/AdminUsuarioController.php?accion=crear" method="POST" class="p-6 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Nombres *</label>
                    <input type="text" name="nombres" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-blue-600 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Apellidos *</label>
                    <input type="text" name="apellidos" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-blue-600 outline-none">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Correo Electrónico *</label>
                <input type="email" name="email" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-blue-600 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Contraseña *</label>
                <input type="password" name="password" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-blue-600 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Rol *</label>
                <select name="rol" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-blue-600 outline-none bg-white">
                    <option value="">Seleccione un rol...</option>
                    <option value="administrador">Administrador</option>
                    <option value="docente">Docente</option>
                    <option value="estudiante">Estudiante</option>
                    <option value="acudiente">Acudiente</option>
                </select>
            </div>
            <div class="flex justify-end gap-3 pt-4 border-t border-slate-200 mt-6">
                <button type="button" onclick="closeModal('modalCrear')" class="px-5 py-2 text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors">Cancelar</button>
                <button type="submit" class="px-5 py-2 text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow transition-colors">Guardar Usuario</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Editar Usuario -->
<div id="modalEditar" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl overflow-hidden">
        <div class="flex justify-between items-center p-6 border-b border-slate-200">
            <h3 class="text-2xl font-bold text-slate-800">Editar Usuario</h3>
            <button onclick="closeModal('modalEditar')" class="text-slate-400 hover:text-slate-600 transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form action="../../controllers/AdminUsuarioController.php?accion=editar" method="POST" class="p-6 space-y-4">
            <input type="hidden" name="id_usuario" id="edit_id_usuario">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Nombres *</label>
                    <input type="text" name="nombres" id="edit_nombres" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-blue-600 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Apellidos *</label>
                    <input type="text" name="apellidos" id="edit_apellidos" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-blue-600 outline-none">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Correo Electrónico</label>
                <input type="email" id="edit_email" readonly class="w-full px-4 py-2 border border-slate-200 bg-slate-50 text-slate-500 rounded-lg outline-none cursor-not-allowed">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Nueva Contraseña <span class="font-normal text-slate-400">(opcional)</span></label>
                <input type="password" name="password" placeholder="••••••••" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-blue-600 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Rol *</label>
                <select name="rol" id="edit_rol" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-blue-600 outline-none bg-white">
                    <option value="administrador">Administrador</option>
                    <option value="docente">Docente</option>
                    <option value="estudiante">Estudiante</option>
                    <option value="acudiente">Acudiente</option>
                </select>
            </div>
            <div class="flex justify-end gap-3 pt-4 border-t border-slate-200 mt-6">
                <button type="button" onclick="closeModal('modalEditar')" class="px-5 py-2 text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors">Cancelar</button>
                <button type="submit" class="px-5 py-2 text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow transition-colors">Actualizar Usuario</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
    }

    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
    }

    function openEditModal(usuario) {
        document.getElementById('edit_id_usuario').value = usuario.id_usuario;
        document.getElementById('edit_nombres').value = usuario.nombres;
        document.getElementById('edit_apellidos').value = usuario.apellidos;
        document.getElementById('edit_email').value = usuario.email;
        document.getElementById('edit_rol').value = usuario.rol;
        openModal('modalEditar');
    }
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>