<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) { header("Location: Usuario/login.php"); exit; }

$titulo = "Configuración del Sistema";
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidebar.php';
?>

<style>
    .config-card { background: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); }
    .config-header { padding: 1.5rem; border-bottom: 1px solid #e2e8f0; background: #f8fafc; border-radius: 16px 16px 0 0; }
    .config-body { padding: 1.5rem; }
    
    .nav-config .nav-link { color: #64748b; font-weight: 600; padding: 1rem 1.5rem; border-radius: 8px; margin-bottom: 0.5rem; transition: all 0.2s; }
    .nav-config .nav-link:hover { background: #f1f5f9; color: #0f172a; }
    .nav-config .nav-link.active { background: #eff6ff; color: #2563eb; }

    /* Estilos tabla */
    .table-custom th {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
        font-weight: 700;
        border-bottom: 2px solid #e2e8f0;
        background: #f8fafc;
        padding: 1rem;
    }
    .table-custom td {
        padding: 1rem;
        vertical-align: middle;
        font-size: 0.9rem;
        color: #334155;
        border-bottom: 1px solid #f1f5f9;
    }
    .badge-status {
        padding: 0.4rem 0.8rem;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    .bg-soft-success { background: #dcfce7; color: #16a34a; }
    .bg-soft-danger { background: #fee2e2; color: #dc2626; }
    
    .btn-action {
        width: 32px;
        height: 32px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        transition: all 0.2s;
        border: none;
        background: #f8fafc;
    }
    .btn-edit { color: #6366f1; }
    .btn-disable { color: #f59e0b; }
</style>

<div class="container-fluid py-2">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h2 class="h4 mb-1 fw-bold text-dark">Ajustes Generales</h2>
            <p class="text-muted mb-0" style="font-size: 0.9rem;">Personaliza la información y el comportamiento del sistema.</p>
        </div>
        <button class="btn btn-primary rounded-3 shadow-sm px-3" style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); border: none; font-weight: 500;">
            <i class="fas fa-plus me-2"></i> Nuevo Método de Pago
        </button>
    </div>

    <div class="row g-4">
        <!-- Menú Lateral Config -->
        <div class="col-12 col-md-4 col-lg-3">
            <div class="nav flex-column nav-config">
                <a class="nav-link" href="configuracion.php"><i class="fas fa-store me-2 w-20px"></i> Info. Empresa</a>
                <a class="nav-link active" href="metodos_pago.php"><i class="fas fa-credit-card me-2 w-20px"></i> Métodos de Pago</a>
            </div>
        </div>

        <!-- Tabla -->
        <div class="col-12 col-md-8 col-lg-9">
            <div class="config-card overflow-hidden">
                <div class="config-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-bold text-dark mb-1">Métodos de Pago</h5>
                        <p class="text-muted small mb-0">Gestiona las opciones de pago que aceptas en tu negocio.</p>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-hover table-custom mb-0">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Tipo</th>
                                <th>Estado</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div style="width: 40px; height: 40px; background: #f1f5f9; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #94a3b8;">
                                            <i class="fas fa-money-bill-wave"></i>
                                        </div>
                                        <div class="fw-bold text-dark">Efectivo</div>
                                    </div>
                                </td>
                                <td class="text-muted">Físico</td>
                                <td><span class="badge-status bg-soft-success">Activo</span></td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-1">
                                        <button class="btn btn-action btn-edit" title="Editar"><i class="fas fa-edit"></i></button>
                                        <button class="btn btn-action btn-disable" title="Desactivar"><i class="fas fa-ban"></i></button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div style="width: 40px; height: 40px; background: #f1f5f9; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #94a3b8;">
                                            <i class="fas fa-credit-card"></i>
                                        </div>
                                        <div class="fw-bold text-dark">Tarjeta de Crédito</div>
                                    </div>
                                </td>
                                <td class="text-muted">Electrónico</td>
                                <td><span class="badge-status bg-soft-success">Activo</span></td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-1">
                                        <button class="btn btn-action btn-edit" title="Editar"><i class="fas fa-edit"></i></button>
                                        <button class="btn btn-action btn-disable" title="Desactivar"><i class="fas fa-ban"></i></button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div style="width: 40px; height: 40px; background: #f1f5f9; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #94a3b8;">
                                            <i class="fas fa-mobile-alt"></i>
                                        </div>
                                        <div class="fw-bold text-dark">Nequi / Daviplata</div>
                                    </div>
                                </td>
                                <td class="text-muted">Transferencia</td>
                                <td><span class="badge-status bg-soft-success">Activo</span></td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-1">
                                        <button class="btn btn-action btn-edit" title="Editar"><i class="fas fa-edit"></i></button>
                                        <button class="btn btn-action btn-disable" title="Desactivar"><i class="fas fa-ban"></i></button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div style="width: 40px; height: 40px; background: #f1f5f9; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #94a3b8;">
                                            <i class="fas fa-university"></i>
                                        </div>
                                        <div class="fw-bold text-dark">Transferencia Bancaria</div>
                                    </div>
                                </td>
                                <td class="text-muted">Transferencia</td>
                                <td><span class="badge-status bg-soft-danger">Inactivo</span></td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-1">
                                        <button class="btn btn-action btn-edit" title="Editar"><i class="fas fa-edit"></i></button>
                                        <button class="btn btn-action btn-disable" title="Activar"><i class="fas fa-check"></i></button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
echo '</section></main></div>';
require_once __DIR__ . '/../layouts/footer.php'; 
?>
