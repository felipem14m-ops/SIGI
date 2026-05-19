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

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Configuracion.php';

$db = (new Database())->conectar();
$configModel = new Configuracion($db);

$metodos = $configModel->listarMetodos();

$titulo = "Configuración";
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidebar.php';
?>
<style>
    :root {
        --cfg-primary: #2563eb;
        --cfg-secondary: #1e3a8a;
        --cfg-surface: #ffffff;
        --cfg-border: #e2e8f0;
        --cfg-bg: #f8fafc;
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

    .config-layout {
        display: grid;
        grid-template-columns: 280px 1fr;
        gap: 2rem;
        align-items: start;
    }

    @media (max-width: 992px) {
        .config-layout {
            grid-template-columns: 1fr;
        }
    }

    /* Navegación Lateral */
    .cfg-nav {
        background: var(--cfg-surface);
        border-radius: 16px;
        border: 1px solid var(--cfg-border);
        padding: 1rem;
        position: sticky;
        top: 2rem;
    }

    .cfg-link {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.85rem 1.25rem;
        border-radius: 12px;
        color: #64748b;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s;
        margin-bottom: 0.5rem;
        border: 1px solid transparent;
    }

    .cfg-link:hover {
        background: #f1f5f9;
        color: #0f172a;
    }

    .cfg-link.active {
        background: #eff6ff;
        color: var(--cfg-primary);
        border-color: #dbeafe;
    }

    /* Paneles de Contenido */
    .cfg-panel {
        background: var(--cfg-surface);
        border-radius: 20px;
        border: 1px solid var(--cfg-border);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        display: none;
    }

    .cfg-panel.active {
        display: block;
        animation: fadeIn 0.3s ease;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .panel-header {
        padding: 1.5rem 2rem;
        border-bottom: 1px solid var(--cfg-border);
    }

    .panel-body {
        padding: 2rem;
    }

    /* Switch Estilo iOS */
    .form-check-input:checked {
        background-color: var(--cfg-primary);
        border-color: var(--cfg-primary);
    }

    .metodo-item {
        padding: 1rem;
        border: 1px solid var(--cfg-border);
        border-radius: 14px;
        margin-bottom: 0.75rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: all 0.2s;
    }

    .metodo-item:hover {
        border-color: var(--cfg-primary);
        background: #f8fafc;
    }
</style>

<div class="container-fluid py-4">
    <!-- Banner de módulo -->
    <div class="module-banner">
        <div class="position-relative" style="z-index: 2;">
            <span class="banner-label" style="color:#fff;">Ajustes del Sistema</span>
            <h1 class="fw-bold m-0 text-white" style="font-family: 'Poppins', sans-serif; letter-spacing: -1px;">Configuración General</h1>
            <p class="m-0 text-white opacity-90 fw-light">Personaliza el comportamiento y los métodos de pago de SIGI.</p>
        </div>
    </div>

    <div class="config-layout">

        <!-- SIDEBAR DE CONFIGURACIÓN -->
        <div class="cfg-nav">
            <h6 class="text-uppercase text-muted fw-bold mb-3 px-3" style="font-size: .7rem; letter-spacing: 1px;">Ajustes de Sistema</h6>
            <a href="javascript:void(0)" class="cfg-link active" onclick="switchPanel('pagos', this)">
                <i class="fas fa-wallet w-20px"></i> Métodos de Pago
            </a>
            <a href="javascript:void(0)" class="cfg-link" onclick="switchPanel('seguridad', this)">
                <i class="fas fa-shield-alt w-20px"></i> Seguridad
            </a>
        </div>

        <!-- CONTENIDO PRINCIPAL -->
        <div class="cfg-content">

            <!-- PANEL: MÉTODOS DE PAGO -->
            <div id="panel-pagos" class="cfg-panel active">
                <div class="panel-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-bold mb-1">Métodos de Pago</h5>
                        <p class="text-muted small mb-0">Gestiona las formas en que tus clientes pueden pagar.</p>
                    </div>
                    <button class="btn btn-sm fw-bold px-4 py-2 rounded-3" 
                            style="background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%); color: #fff; border: none; box-shadow: 0 4px 12px rgba(37,99,235,0.25);"
                            onclick="nuevoMetodo()">
                        <i class="fas fa-plus me-1"></i> Agregar
                    </button>
                </div>
                <div class="panel-body" id="listaMetodos">
                    <?php foreach ($metodos as $m): ?>
                        <div class="metodo-item">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-light p-2 rounded-3 text-primary">
                                    <i class="fas fa-money-bill-wave"></i>
                                </div>
                                <span class="fw-bold text-dark"><?= htmlspecialchars($m['nombre']) ?></span>
                            </div>
                            <div class="d-flex align-items-center gap-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch"
                                        <?= $m['activo'] ? 'checked' : '' ?>
                                        onclick="toggleMetodo(<?= $m['id_metodo'] ?>, this.checked)">
                                </div>
                                <button class="btn btn-sm btn-outline-primary border-0" onclick="editarMetodo(<?= $m['id_metodo'] ?>, '<?= htmlspecialchars($m['nombre']) ?>')">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- PANEL: SEGURIDAD -->
            <div id="panel-seguridad" class="cfg-panel">
                <div class="panel-header">
                    <h5 class="fw-bold mb-1">Seguridad de la Cuenta</h5>
                    <p class="text-muted small mb-0">Actualiza tus credenciales de acceso al sistema.</p>
                </div>
                <div class="panel-body">
                    <div class="alert alert-info border-0 rounded-4">
                        <i class="fas fa-info-circle me-2"></i> Para cambiar la contraseña general de administrador, ve al módulo de <b>Usuarios</b> y edita tu perfil.
                    </div>
                    <a href="Adminusuarios.php" class="btn btn-outline-primary fw-bold rounded-3">Ir a Gestión de Usuarios</a>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- MODAL: Nuevo Método de Pago -->
<div id="overlayNuevoMetodo" onclick="cerrarModalMetodo()" 
     style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:9998; backdrop-filter:blur(3px);"></div>

<div id="modalNuevoMetodo" 
     style="display:none; position:fixed; top:50%; left:50%; transform:translate(-50%,-55%); z-index:9999; width:100%; max-width:480px; animation: modalSlideIn 0.3s ease forwards;">
    <div style="background:#fff; border-radius:24px; overflow:hidden; box-shadow:0 25px 60px rgba(0,0,0,0.2);">
        <!-- Header -->
        <div style="background:linear-gradient(135deg,#1e3a8a 0%,#2563eb 100%); padding:1.5rem 2rem; display:flex; justify-content:space-between; align-items:center;">
            <div>
                <h5 class="fw-bold text-white m-0"><i class="fas fa-wallet me-2"></i>Nuevo Método de Pago</h5>
                <p class="text-white opacity-75 small m-0 mt-1">Agrega una nueva forma de pago al sistema</p>
            </div>
            <button onclick="cerrarModalMetodo()" 
                    style="background:rgba(255,255,255,0.2); border:none; color:#fff; width:36px; height:36px; border-radius:10px; cursor:pointer; font-size:1rem;">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <!-- Body -->
        <div style="padding:2rem;">
            <label class="fw-semibold text-dark mb-2 d-block" style="font-size:0.9rem;">
                <i class="fas fa-tag me-1 text-primary"></i> Nombre del Método
            </label>
            <input type="text" id="inputNuevoMetodo" 
                   class="form-control" 
                   placeholder="Ej: Nequi, Daviplata, Efectivo..."
                   style="border-radius:12px; border:1.5px solid #e2e8f0; padding:0.8rem 1rem; font-size:0.95rem;"
                   onkeydown="if(event.key==='Enter') guardarNuevoMetodo()">
            <p class="text-muted small mt-2 mb-0"><i class="fas fa-info-circle me-1"></i>El método quedará activo por defecto.</p>
        </div>
        <!-- Footer -->
        <div style="padding:0 2rem 2rem; display:flex; gap:0.75rem; justify-content:flex-end;">
            <button onclick="cerrarModalMetodo()" 
                    class="btn btn-light fw-semibold px-4 rounded-3">
                Cancelar
            </button>
            <button onclick="guardarNuevoMetodo()" 
                    class="btn fw-bold px-5 rounded-3"
                    style="background:linear-gradient(135deg,#1e3a8a 0%,#2563eb 100%); color:#fff; border:none; box-shadow:0 4px 12px rgba(37,99,235,0.3);">
                <i class="fas fa-save me-1"></i> Guardar
            </button>
        </div>
    </div>
</div>

<!-- MODAL: Editar Método de Pago -->
<div id="overlayEditarMetodo" onclick="cerrarModalEditarMetodo()" 
     style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:9998; backdrop-filter:blur(3px);"></div>

<div id="modalEditarMetodo" 
     style="display:none; position:fixed; top:50%; left:50%; transform:translate(-50%,-55%); z-index:9999; width:100%; max-width:480px; animation: modalSlideIn 0.3s ease forwards;">
    <div style="background:#fff; border-radius:24px; overflow:hidden; box-shadow:0 25px 60px rgba(0,0,0,0.2);">
        <!-- Header -->
        <div style="background:linear-gradient(135deg,#1e3a8a 0%,#2563eb 100%); padding:1.5rem 2rem; display:flex; justify-content:space-between; align-items:center;">
            <div>
                <h5 class="fw-bold text-white m-0"><i class="fas fa-pen me-2"></i>Editar Método de Pago</h5>
                <p class="text-white opacity-75 small m-0 mt-1">Modifica el nombre del método seleccionado</p>
            </div>
            <button onclick="cerrarModalEditarMetodo()" 
                    style="background:rgba(255,255,255,0.2); border:none; color:#fff; width:36px; height:36px; border-radius:10px; cursor:pointer; font-size:1rem;">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <!-- Body -->
        <div style="padding:2rem;">
            <input type="hidden" id="inputIdEditarMetodo">
            <input type="hidden" id="inputNombreOriginalMetodo">
            <label class="fw-semibold text-dark mb-2 d-block" style="font-size:0.9rem;">
                <i class="fas fa-tag me-1 text-primary"></i> Nombre del Método
            </label>
            <input type="text" id="inputEditarMetodo" 
                   class="form-control" 
                   style="border-radius:12px; border:1.5px solid #e2e8f0; padding:0.8rem 1rem; font-size:0.95rem;"
                   onkeydown="if(event.key==='Enter') guardarEdicionMetodo()">
        </div>
        <!-- Footer -->
        <div style="padding:0 2rem 2rem; display:flex; gap:0.75rem; justify-content:flex-end;">
            <button onclick="cerrarModalEditarMetodo()" 
                    class="btn btn-light fw-semibold px-4 rounded-3">
                Cancelar
            </button>
            <button onclick="guardarEdicionMetodo()" 
                    class="btn fw-bold px-5 rounded-3"
                    style="background:linear-gradient(135deg,#1e3a8a 0%,#2563eb 100%); color:#fff; border:none; box-shadow:0 4px 12px rgba(37,99,235,0.3);">
                <i class="fas fa-save me-1"></i> Guardar Cambios
            </button>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function switchPanel(panelId, link) {
        document.querySelectorAll('.cfg-panel').forEach(p => p.classList.remove('active'));
        document.querySelectorAll('.cfg-link').forEach(l => l.classList.remove('active'));

        document.getElementById('panel-' + panelId).classList.add('active');
        link.classList.add('active');
    }


    async function toggleMetodo(id, estado) {
        const formData = new FormData();
        formData.append('id', id);
        formData.append('estado', estado ? 1 : 0);

        try {
            await fetch('../../controllers/ConfiguracionController.php?accion=toggle_metodo', {
                method: 'POST',
                body: formData
            });
        } catch (err) {
            Swal.fire('Error', 'No se pudo cambiar el estado.', 'error');
        }
    }

    function nuevoMetodo() {
        document.getElementById('inputNuevoMetodo').value = '';
        document.getElementById('overlayNuevoMetodo').style.display = 'block';
        document.getElementById('modalNuevoMetodo').style.display = 'block';
        setTimeout(() => document.getElementById('inputNuevoMetodo').focus(), 100);
    }

    function cerrarModalMetodo() {
        document.getElementById('overlayNuevoMetodo').style.display = 'none';
        document.getElementById('modalNuevoMetodo').style.display = 'none';
    }

    async function guardarNuevoMetodo() {
        const nombre = document.getElementById('inputNuevoMetodo').value.trim();
        if (!nombre) {
            document.getElementById('inputNuevoMetodo').style.borderColor = '#ef4444';
            document.getElementById('inputNuevoMetodo').focus();
            return;
        }
        document.getElementById('inputNuevoMetodo').style.borderColor = '#e2e8f0';

        const formData = new FormData();
        formData.append('nombre', nombre);
        try {
            const resp = await fetch('../../controllers/ConfiguracionController.php?accion=guardar_metodo', {
                method: 'POST',
                body: formData
            });
            const data = await resp.json();
            if (data.ok) {
                cerrarModalMetodo();
                Swal.fire({
                    icon: 'success',
                    title: '¡Método guardado!',
                    text: `"${nombre}" fue agregado correctamente.`,
                    confirmButtonColor: '#2563eb',
                    customClass: { popup: 'rounded-4', confirmButton: 'rounded-3 px-4' }
                }).then(() => location.reload());
            } else {
                Swal.fire('Error', data.error || 'No se pudo guardar el método.', 'error');
            }
        } catch (err) {
            Swal.fire('Error', 'Fallo de conexión con el servidor.', 'error');
        }
    }

    function editarMetodo(id, nombreActual) {
        document.getElementById('inputIdEditarMetodo').value = id;
        document.getElementById('inputNombreOriginalMetodo').value = nombreActual;
        document.getElementById('inputEditarMetodo').value = nombreActual;
        
        document.getElementById('overlayEditarMetodo').style.display = 'block';
        document.getElementById('modalEditarMetodo').style.display = 'block';
        setTimeout(() => document.getElementById('inputEditarMetodo').focus(), 100);
    }

    function cerrarModalEditarMetodo() {
        document.getElementById('overlayEditarMetodo').style.display = 'none';
        document.getElementById('modalEditarMetodo').style.display = 'none';
    }

    async function guardarEdicionMetodo() {
        const id = document.getElementById('inputIdEditarMetodo').value;
        const nombreOriginal = document.getElementById('inputNombreOriginalMetodo').value;
        const nombre = document.getElementById('inputEditarMetodo').value.trim();
        
        if (!nombre) {
            document.getElementById('inputEditarMetodo').style.borderColor = '#ef4444';
            document.getElementById('inputEditarMetodo').focus();
            return;
        }
        
        document.getElementById('inputEditarMetodo').style.borderColor = '#e2e8f0';

        if (nombre === nombreOriginal) {
            cerrarModalEditarMetodo();
            return; // Sin cambios, solo cerramos
        }

        const formData = new FormData();
        formData.append('id', id);
        formData.append('nombre', nombre);
        
        try {
            const resp = await fetch('../../controllers/ConfiguracionController.php?accion=guardar_metodo', {
                method: 'POST',
                body: formData
            });
            const data = await resp.json();
            
            if (data.ok) {
                cerrarModalEditarMetodo();
                Swal.fire({
                    icon: 'success',
                    title: '¡Método actualizado!',
                    text: `El método se ha cambiado a "${nombre}".`,
                    confirmButtonColor: '#2563eb',
                    customClass: { popup: 'rounded-4', confirmButton: 'rounded-3 px-4' }
                }).then(() => location.reload());
            } else {
                Swal.fire('Error', data.error || 'No se pudo editar el método.', 'error');
            }
        } catch (err) {
            Swal.fire('Error', 'Fallo de conexión con el servidor.', 'error');
        }
    }
</script>

<?php
echo '</section></main></div>';
require_once __DIR__ . '/../layouts/footer.php';
?>