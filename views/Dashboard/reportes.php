<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) { header("Location: Usuario/login.php"); exit; }

$titulo = "Reportes y Estadísticas";
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidebar.php';
?>

<style>
    .report-card { background: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; padding: 1.5rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); }
    .report-icon { width: 45px; height: 45px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; margin-bottom: 1rem; }
    .fake-chart { height: 200px; width: 100%; display: flex; align-items: flex-end; justify-content: space-around; padding-top: 2rem; border-bottom: 1px solid #f1f5f9; }
    .bar { width: 12%; background: #2563eb; border-radius: 4px 4px 0 0; opacity: 0.8; transition: all 0.3s; }
    .bar:hover { opacity: 1; background: #1d4ed8; }
</style>

<div class="container-fluid py-2">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h2 class="h4 mb-1 fw-bold text-dark">Rendimiento Comercial</h2>
            <p class="text-muted mb-0" style="font-size: 0.9rem;">Visualiza los ingresos, gastos y el estado general.</p>
        </div>
        <button class="btn btn-outline-dark rounded-3 fw-bold">
            <i class="fas fa-download me-2"></i> Descargar PDF
        </button>
    </div>

    <!-- Filtros Rápido -->
    <div class="d-flex gap-2 mb-4">
        <button class="btn btn-dark rounded-pill px-3 py-1" style="font-size: 0.85rem;">Esta Semana</button>
        <button class="btn btn-outline-secondary bg-white rounded-pill px-3 py-1 border-0 shadow-sm" style="font-size: 0.85rem;">Este Mes</button>
        <button class="btn btn-outline-secondary bg-white rounded-pill px-3 py-1 border-0 shadow-sm" style="font-size: 0.85rem;">Este Año</button>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-12 col-lg-8">
            <div class="report-card h-100">
                <h6 class="fw-bold text-dark mb-4">Ingresos vs Gastos (Últimos 7 Días)</h6>
                <div class="fake-chart">
                    <div class="bar" style="height: 40%;"></div>
                    <div class="bar" style="height: 70%; background: #10b981;"></div>
                    <div class="bar" style="height: 55%;"></div>
                    <div class="bar" style="height: 90%; background: #10b981;"></div>
                    <div class="bar" style="height: 30%;"></div>
                    <div class="bar" style="height: 60%; background: #10b981;"></div>
                    <div class="bar" style="height: 85%;"></div>
                </div>
                <div class="d-flex justify-content-around mt-2 text-muted" style="font-size: 0.75rem;">
                    <span>Lun</span><span>Mar</span><span>Mié</span><span>Jue</span><span>Vie</span><span>Sáb</span><span>Dom</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-4">
            <div class="report-card mb-4" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); border: none;">
                <div class="report-icon" style="background: rgba(255,255,255,0.1); color: white;"><i class="fas fa-coins"></i></div>
                <h6 class="text-white opacity-75 mb-1">Total Ventas (Mes)</h6>
                <h3 class="fw-bold text-white mb-0">$12.450.000</h3>
                <div class="text-success mt-2" style="font-size: 0.8rem;"><i class="fas fa-arrow-up"></i> 14% vs mes anterior</div>
            </div>
            
            <div class="report-card">
                <h6 class="fw-bold text-dark mb-3">Productos más vendidos</h6>
                <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                    <span class="text-muted" style="font-size: 0.85rem;">1. Arroz Premium 500g</span>
                    <span class="fw-bold text-dark" style="font-size: 0.85rem;">450 und</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                    <span class="text-muted" style="font-size: 0.85rem;">2. Agua Mineral 500ml</span>
                    <span class="fw-bold text-dark" style="font-size: 0.85rem;">320 und</span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted" style="font-size: 0.85rem;">3. Aceite Vegetal 1L</span>
                    <span class="fw-bold text-dark" style="font-size: 0.85rem;">180 und</span>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
echo '</section></main></div>';
require_once __DIR__ . '/../layouts/footer.php'; 
?>

