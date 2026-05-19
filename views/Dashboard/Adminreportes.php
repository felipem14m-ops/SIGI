<?php

/**
 * ============================================================================
 * VISTA: Adminreportes.php
 * Sistema: SIGI — Gestión de Inventario
 * ============================================================================
 */
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Categoria.php';

$db = (new Database())->conectar();
$categorias = (new Categoria($db))->listarTodas();
$titulo = "Reportes del Sistema";

// Consultar el historial de reportes generados
$historial_reportes = [];
try {
    $query = "SELECT * FROM reportes_generados ORDER BY fecha_generacion DESC";
    $historial_reportes = $db->query($query)->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // La tabla podría estar vacía o en desarrollo
    $historial_reportes = [];
}

include_once __DIR__ . '/../layouts/header.php';
include_once __DIR__ . '/../layouts/sidebar.php';
?>

<style>
    :root {
        --primary-blue: #1e3a8a;
        --accent-gold: #fbbf24;
    }

    .report-card {
        background: #fff;
        border-radius: 28px;
        padding: 3.5rem 3rem;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.07);
        border: 1px solid rgba(0, 0, 0, 0.05);
        height: 100%;
        min-height: 380px;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .report-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 35px -10px rgba(0, 0, 0, 0.1);
    }

    .report-icon {
        width: 100px;
        height: 100px;
        border-radius: 26px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        margin-bottom: 2rem;
    }

    .icon-blue {
        background: #eff6ff;
        color: #2563eb;
    }

    .icon-green {
        background: #ecfdf5;
        color: #10b981;
    }

    .icon-amber {
        background: #fffbeb;
        color: #f59e0b;
    }

    .btn-generate {
        background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
        color: #fff;
        border: none;
        border-radius: 16px;
        padding: 1.1rem 1.5rem;
        font-weight: 700;
        font-size: 1.05rem;
        transition: all 0.2s;
        box-shadow: 0 6px 18px rgba(37, 99, 235, 0.25);
        letter-spacing: 0.5px;
        margin-top: auto;
    }

    .btn-generate:hover {
        transform: scale(1.02);
        box-shadow: 0 8px 20px rgba(37, 99, 235, 0.35);
        color: #fff;
    }

    /* Título de la tarjeta */
    .report-card h4 {
        font-family: 'Poppins', sans-serif;
        font-size: 1.5rem;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 1rem;
        letter-spacing: -0.5px;
    }

    /* Párrafo de descripción */
    .report-card p.card-desc {
        font-size: 1rem;
        line-height: 1.7;
        color: #475569;
        margin-bottom: 1.5rem;
    }

    .module-banner {
        background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
        border-radius: 24px;
        padding: 2.5rem;
        margin-bottom: 2rem;
        box-shadow: 0 10px 30px -10px rgba(30, 58, 138, 0.3);
    }

    .form-control-custom {
        border-radius: 12px;
        border: 1.5px solid #e2e8f0;
        padding: 0.75rem 1rem;
        transition: all 0.2s;
    }

    .form-control-custom:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
    }

    .form-label-custom {
        font-weight: 600;
        color: #475569;
        margin-bottom: 0.5rem;
        font-size: 0.9rem;
    }

    /* Estilos DataTables Premium */
    .table-custom th {
        background: #f1f5f9;
        color: #64748b;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #e2e8f0;
        padding: 1rem;
        font-weight: 700;
    }

    .table-custom td {
        padding: 1rem;
        vertical-align: middle;
        font-size: 0.9rem;
        border-bottom: 1px solid #f1f5f9;
    }

    .dataTables_wrapper .dataTables_paginate {
        margin-top: 1.5rem !important;
        padding-bottom: 1.5rem !important;
        display: flex;
        justify-content: flex-end;
        gap: 5px;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button {
        border-radius: 10px !important;
        border: 1px solid #e2e8f0 !important;
        background: white !important;
        padding: 0.5em 1em !important;
        color: #64748b !important;
        font-weight: 600;
        cursor: pointer;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: var(--primary-blue) !important;
        color: white !important;
        border-color: var(--primary-blue) !important;
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
        text-decoration: none;
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

    .btn-accion-descargar {
        background: #ecfdf5;
        color: #10b981;
    }

    .btn-accion-descargar:hover {
        background: #10b981;
        color: #fff;
        transform: translateY(-2px);
    }

    /* Centrar modal */
    #modalFiltros .modal-dialog {
        margin: 1.75rem auto;
        display: flex;
        align-items: center;
        min-height: calc(100% - 3.5rem);
    }
</style>

<!-- jsPDF & AutoTable - Generación de PDF en cliente -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>
<script src="../../JS/PDFGenerator.js"></script>

<div class="container-fluid py-3">
    <!-- Banner Principal -->
    <div class="module-banner">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="fw-bold m-0 text-white" style="font-family: 'Poppins', sans-serif; letter-spacing: -1px;">
                    <i class="fas fa-file-invoice me-3"></i>Centro de Reportes
                </h1>
                <p class="m-0 text-white opacity-90 fw-light">Generación de reportes globales del sistema</p>
            </div>
            <div class="bg-white bg-opacity-20 p-3 rounded-circle">
                <i class="fas fa-shield-alt fa-2x text-white"></i>
            </div>
        </div>
    </div>

    <!-- Descripción destacada -->
    <div class="text-center mb-4 py-3">
        <h3 class="fw-bold mb-2" style="font-family: 'Poppins', sans-serif; background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; font-size: 1.6rem; letter-spacing: -0.5px;">
            Selecciona el tipo de reporte que deseas generar
        </h3>
        <p class="text-muted mb-0" style="font-size: 1rem;">Descárgalo instantáneamente en formato PDF con diseño institucional Golden Standard.</p>
    </div>

    <!-- Panel de Generación - Tarjetas grandes, sin restricción de ancho -->
    <div class="row g-4">
        <!-- Inventario Actual -->
        <div class="col-md-4">
            <div class="report-card">
                <div>
                    <div class="report-icon icon-blue">
                        <i class="fas fa-boxes"></i>
                    </div>
                    <h4>Inventario Actual</h4>
                    <p class="card-desc">
                        Genera un PDF completo con todos los productos activos del sistema: códigos, categorías, precios y niveles de stock en tiempo real.
                    </p>
                </div>
                <button class="btn btn-generate w-100" onclick="generarInventarioActual()">
                    <i class="fas fa-file-pdf me-2"></i>GENERAR REPORTE PDF
                </button>
            </div>
        </div>

        <!-- Reporte Personalizado -->
        <div class="col-md-4">
            <div class="report-card">
                <div>
                    <div class="report-icon icon-green">
                        <i class="fas fa-filter"></i>
                    </div>
                    <h4>Filtros Avanzados</h4>
                    <p class="card-desc">
                        Personaliza tu reporte aplicando filtros por categoría, estado del producto o rango de fechas. Solo ves exactamente lo que necesitas.
                    </p>
                </div>
                <button class="btn btn-generate w-100" onclick="abrirModalFiltros()">
                    <i class="fas fa-sliders-h me-2"></i>CONFIGURAR FILTROS
                </button>
            </div>
        </div>

        <!-- Alertas de Stock -->
        <div class="col-md-4">
            <div class="report-card">
                <div>
                    <div class="report-icon icon-amber">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <h4>Alertas de Stock</h4>
                    <p class="card-desc">
                        Detecta y exporta todos los productos que han alcanzado su nivel mínimo de inventario. Reporte crítico para tomar acción inmediata.
                    </p>
                </div>
                <button class="btn btn-generate w-100" onclick="generarStockBajo()">
                    <i class="fas fa-file-pdf me-2"></i>REPORTE DE ALERTA
                </button>
            </div>
        </div>
    </div>
</div>


<script>
    const CATEGORIAS_REP = <?= json_encode($categorias) ?>;

    function _buildModalFiltros() {
        const prev = document.getElementById('modalRepDyn');
        if (prev) prev.remove();
        const prevOv = document.getElementById('modalRepOverlay');
        if (prevOv) prevOv.remove();

        const overlay = document.createElement('div');
        overlay.id = 'modalRepOverlay';
        overlay.style.cssText = 'position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:9998;backdrop-filter:blur(2px);';
        overlay.onclick = cerrarModalRep;
        document.body.appendChild(overlay);

        const wrap = document.createElement('div');
        wrap.id = 'modalRepDyn';
        wrap.style.cssText = 'position:fixed;top:0;left:0;width:100%;height:100%;z-index:9999;display:flex;align-items:center;justify-content:center;padding:1rem;';

        wrap.innerHTML = `
        <div class="modal-content shadow-lg" style="background:#fff; border-radius: 24px; border: none; overflow: hidden; width:100%; max-width:700px; animation: modalIn 0.3s ease;">
            <div style="background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%); padding: 1.5rem 2rem; display: flex; justify-content: space-between; align-items: center; color: #fff;">
                <div>
                    <h5 class="modal-title fw-bold text-white m-0"><i class="fas fa-sliders-h me-2"></i>Criterios de Selección</h5>
                    <p class="small mb-0 text-white opacity-75">Define los parámetros para tu reporte</p>
                </div>
                <button onclick="cerrarModalRep()" style="background:rgba(255,255,255,0.2); border:none; color:#fff; width:36px; height:36px; border-radius:10px; cursor:pointer;">✕</button>
            </div>
            <div class="modal-body p-4 p-md-5">
                <form id="formFiltrosDyn">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label-custom">Categoría</label>
                            <select name="id_categoria" class="form-select form-control-custom">
                                <option value="">Todas las categorías</option>
                                ${CATEGORIAS_REP.map(c => `<option value="${c.id_categoria}">${c.nombre}</option>`).join('')}
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-custom">Estado</label>
                            <select name="estado" class="form-select form-control-custom">
                                <option value="">Todos los estados</option>
                                <option value="activo">Activo</option>
                                <option value="inactivo">Inactivo</option>
                                <option value="agotado">Agotado</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-custom">Desde</label>
                            <input type="date" name="fecha_desde" class="form-control form-control-custom">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-custom">Hasta</label>
                            <input type="date" name="fecha_hasta" class="form-control form-control-custom">
                        </div>
                        <div class="col-12">
                            <div class="form-check p-3 bg-light rounded-3 border">
                                <input class="form-check-input" type="checkbox" name="stock_bajo" id="stockBajoCheck">
                                <label class="form-check-label fw-bold text-dark" for="stockBajoCheck">
                                    Filtrar únicamente productos con stock crítico (actual ≤ mínimo)
                                </label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0 p-4 pt-0">
                <button type="button" onclick="cerrarModalRep()" class="btn btn-light px-4 rounded-pill">Cancelar</button>
                <button type="button" class="btn btn-generate px-5 rounded-pill" onclick="generarInventarioFiltrado()">
                    <i class="fas fa-file-pdf me-2"></i>GENERAR REPORTE PDF
                </button>
            </div>
        </div>
        `;
        document.body.appendChild(wrap);
    }

    function cerrarModalRep() {
        document.getElementById('modalRepOverlay')?.remove();
        document.getElementById('modalRepDyn')?.remove();
    }

    function abrirModalFiltros() {
        _buildModalFiltros();
    }

    // Estilos de animación
    const styleRep = document.createElement('style');
    styleRep.innerHTML = `
        @keyframes modalIn {
            from { opacity: 0; transform: scale(0.95) translateY(20px); }
            to { opacity: 1; transform: scale(1) translateY(0); }
        }
    `;
    document.head.appendChild(styleRep);

    /**
     * Funciones de Generación de Reportes (Senior Refactor)
     */
    async function fetchReportData(filters = null) {
        const url = '../../controllers/ReporteController.php?accion=obtener_datos_inventario';
        const options = filters ? {
            method: 'POST',
            body: filters
        } : {
            method: 'GET'
        };
        const resp = await fetch(url, options);
        const result = await resp.json();
        if (!result.ok) throw new Error(result.error);
        return result.data;
    }

    function processTableData(data) {
        return data.map(p => [
            p.codigoUnico || '---',
            p.nombre || 'Sin nombre',
            p.categoria || 'N/A',
            '$' + (p.precio_costo ? parseInt(p.precio_costo).toLocaleString() : '0'),
            '$' + (p.precio_venta ? parseInt(p.precio_venta).toLocaleString() : '0'),
            p.stock_actual || 0,
            p.stock_minimo || 0,
            (p.estado || 'activo').toUpperCase()
        ]);
    }

    async function generarInventarioActual() {
        Swal.fire({
            title: 'Procesando...',
            text: 'Generando reporte institucional',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });
        try {
            const data = await fetchReportData();
            if (!data.length) return Swal.fire('Inventario Vacío', 'No hay productos registrados.', 'info');

            const gen = new SIGIPDFGenerator('Inventario Actual');
            const headers = ['Código', 'Producto', 'Categoría', 'P. Costo', 'P. Venta', 'Stock', 'Mín', 'Estado'];
            gen.generateTable(headers, processTableData(data));
            Swal.fire('¡Éxito!', 'Reporte descargado correctamente.', 'success');
        } catch (e) {
            Swal.fire('Error', e.message, 'error');
        }
    }

    async function generarInventarioFiltrado() {
        const formData = new FormData(document.getElementById('formFiltrosDyn'));
        Swal.fire({
            title: 'Procesando...',
            text: 'Aplicando filtros personalizados',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });
        try {
            const data = await fetchReportData(formData);
            if (!data.length) return Swal.fire('Sin Resultados', 'No se encontraron productos con esos filtros.', 'info');

            const gen = new SIGIPDFGenerator('Reporte Personalizado');
            const headers = ['Código', 'Producto', 'Categoría', 'P. Costo', 'P. Venta', 'Stock', 'Mín', 'Estado'];
            gen.generateTable(headers, processTableData(data));
            cerrarModalRep();
            Swal.fire('¡Éxito!', 'Reporte personalizado descargado.', 'success');
        } catch (e) {
            Swal.fire('Error', e.message, 'error');
        }
    }

    async function generarStockBajo() {
        Swal.fire({
            title: 'Analizando...',
            text: 'Buscando alertas de stock',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });
        try {
            const data = await fetchReportData(new URLSearchParams({
                stock_bajo: '1'
            }));
            if (!data.length) return Swal.fire('Stock Saludable', 'No hay productos con stock bajo.', 'success');

            const gen = new SIGIPDFGenerator('Alerta de Stock Bajo');
            const headers = ['Código', 'Producto', 'Categoría', 'Stock', 'Mínimo', 'Estado'];
            const tableData = data.map(p => [p.codigoUnico || '---', p.nombre || 'Sin nombre', p.categoria || 'N/A', p.stock_actual, p.stock_minimo, 'CRÍTICO']);

            gen.generateTable(headers, tableData, {
                didParseCell: (d) => {
                    if (d.section === 'body') d.cell.styles.textColor = [185, 28, 28];
                }
            });
            Swal.fire('¡Alerta Generada!', 'Reporte de stock bajo descargado.', 'warning');
        } catch (e) {
            Swal.fire('Error', e.message, 'error');
        }
    }

</script>

<?php
echo '</section></main></div>';
include_once __DIR__ . '/../layouts/footer.php';
?>