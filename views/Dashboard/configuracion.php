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
</style>

<div class="container-fluid py-2">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h2 class="h4 mb-1 fw-bold text-dark">Ajustes Generales</h2>
            <p class="text-muted mb-0" style="font-size: 0.9rem;">Personaliza la información y el comportamiento del sistema.</p>
        </div>
        <button class="btn btn-primary rounded-3 shadow-sm" style="background-color: #2563eb; border: none; font-weight: 500;">
            <i class="fas fa-save me-2"></i> Guardar Cambios
        </button>
    </div>

    <div class="row g-4">
        <!-- Menú Lateral Config -->
        <div class="col-12 col-md-4 col-lg-3">
            <div class="nav flex-column nav-config">
                <a class="nav-link active" href="configuracion.php"><i class="fas fa-store me-2 w-20px"></i> Info. Empresa</a>
                <a class="nav-link" href="metodos_pago.php"><i class="fas fa-credit-card me-2 w-20px"></i> Métodos de Pago</a>
            </div>
        </div>

        <!-- Formulario -->
        <div class="col-12 col-md-8 col-lg-9">
            <div class="config-card">
                <div class="config-header">
                    <h5 class="fw-bold text-dark mb-1">Información de la Empresa</h5>
                    <p class="text-muted small mb-0">Estos datos aparecerán en los recibos y reportes generados.</p>
                </div>
                <div class="config-body">
                    <div class="row g-4">
                        <div class="col-12">
                            <label class="form-label fw-bold" style="font-size: 0.85rem; color: #475569;">Nombre del Negocio</label>
                            <input type="text" class="form-control bg-light" value="SIGI">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-bold" style="font-size: 0.85rem; color: #475569;">NIT / Documento</label>
                            <input type="text" class="form-control bg-light" value="900.123.456-7">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-bold" style="font-size: 0.85rem; color: #475569;">Teléfono</label>
                            <input type="text" class="form-control bg-light" value="+57 300 000 0000">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold" style="font-size: 0.85rem; color: #475569;">Dirección Principal</label>
                            <input type="text" class="form-control bg-light" value="Calle Falsa 123, Centro">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold" style="font-size: 0.85rem; color: #475569;">Moneda del Sistema</label>
                            <select class="form-select bg-light">
                                <option>Peso Colombiano (COP)</option>
                                <option>Dólar Estadounidense (USD)</option>
                                <option>Euro (EUR)</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
echo '</section></main></div>';
require_once __DIR__ . '/../layouts/footer.php'; 
?>

