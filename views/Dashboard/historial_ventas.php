<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: ../Usuario/login.php");
    exit;
}

$titulo = "Historial de Ventas";
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Venta.php';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidebar.php';

// Inicializar modelo
$db = (new Database())->conectar();
$ventaModel = new Venta($db);

// Obtener filtros
$filtros = [
    'fecha_inicio' => $_GET['fecha_inicio'] ?? date('Y-m-01'), // Primer día del mes
    'fecha_fin'    => $_GET['fecha_fin']    ?? date('Y-m-d'),  // Hoy
    'id_rol'       => $_GET['id_rol']       ?? '',
    'id_metodo'    => $_GET['id_metodo']    ?? '',
    'estado'       => $_GET['estado']       ?? '',
];

// Obtener ventas con filtros
$ventas = $ventaModel->listarVentasConFiltros($filtros);

// Obtener datos para los filtros
$roles = $ventaModel->obtenerRoles();
$metodosPago = $ventaModel->obtenerMetodosPago();

// Calcular totales
$totalVentas = count($ventas);
$totalIngresos = array_sum(array_column($ventas, 'total'));
$totalItems = array_sum(array_column($ventas, 'total_items'));
?>

<style>
    .stats-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        border: 1px solid #e2e8f0;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    .stats-icon {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
    }
    .filter-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        border: 1px solid #e2e8f0;
        margin-bottom: 1.5rem;
    }
    .table-ventas {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid #e2e8f0;
    }
    .table-ventas thead {
        background: #f8fafc;
    }
    .table-ventas th {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #64748b;
        font-weight: 700;
        padding: 1rem 0.75rem;
        border-bottom: 2px solid #e2e8f0;
    }
    .table-ventas td {
        padding: 1rem 0.75rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
    }
    .badge-rol {
        padding: 0.4rem 0.8rem;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    .badge-admin { background: #dbeafe; color: #1e40af; }
    .badge-empleado { background: #dcfce7; color: #166534; }
    .badge-cajero { background: #fef3c7; color: #92400e; }
    .badge-metodo {
        padding: 0.3rem 0.7rem;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
        background: #f1f5f9;
        color: #475569;
    }
    .badge-items {
        background: #e0e7ff;
        color: #3730a3;
        padding: 0.3rem 0.7rem;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    .folio-link {
        color: #2563eb;
        font-weight: 700;
        text-decoration: none;
        font-size: 0.9rem;
    }
    .folio-link:hover {
        color: #1d4ed8;
        text-decoration: underline;
    }
    .btn-filter {
        background: #14532d;
        color: white;
        border: none;
        padding: 0.6rem 1.5rem;
        border-radius: 8px;
        font-weight: 600;
    }
    .btn-filter:hover {
        background: #166534;
        color: white;
    }
    .btn-reset {
        background: #f1f5f9;
        color: #475569;
        border: none;
        padding: 0.6rem 1.5rem;
        border-radius: 8px;
        font-weight: 600;
    }
    .btn-reset:hover {
        background: #e2e8f0;
    }
    .tipo-movimiento {
        font-size: 0.75rem;
        color: #64748b;
        font-style: italic;
    }
</style>

<div class="container-fluid py-3">
    <!-- Encabezado -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h4 mb-1 fw-bold text-dark">
                <i class="fas fa-history text-primary me-2"></i>
                Historial de Ventas
            </h2>
            <p class="text-muted mb-0" style="font-size: 0.9rem;">
                Consulta y filtra todas las ventas realizadas
            </p>
        </div>
        <button class="btn btn-outline-dark rounded-3" onclick="window.print()">
            <i class="fas fa-print me-2"></i> Imprimir
        </button>
    </div>

    <!-- Estadísticas Rápidas -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="stats-card">
                <div class="d-flex align-items-center">
                    <div class="stats-icon" style="background: #dbeafe; color: #1e40af;">
                        <i class="fas fa-receipt"></i>
                    </div>
                    <div class="ms-3">
                        <div class="text-muted" style="font-size: 0.85rem;">Total Ventas</div>
                        <h4 class="mb-0 fw-bold"><?php echo number_format($totalVentas); ?></h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stats-card">
                <div class="d-flex align-items-center">
                    <div class="stats-icon" style="background: #dcfce7; color: #166534;">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div class="ms-3">
                        <div class="text-muted" style="font-size: 0.85rem;">Ingresos Totales</div>
                        <h4 class="mb-0 fw-bold">$<?php echo number_format($totalIngresos, 0, ',', '.'); ?></h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stats-card">
                <div class="d-flex align-items-center">
                    <div class="stats-icon" style="background: #fef3c7; color: #92400e;">
                        <i class="fas fa-box"></i>
                    </div>
                    <div class="ms-3">
                        <div class="text-muted" style="font-size: 0.85rem;">Productos Vendidos</div>
                        <h4 class="mb-0 fw-bold"><?php echo number_format($totalItems); ?></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="filter-card">
        <form method="GET" action="">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-bold" style="font-size: 0.85rem;">Fecha Inicio</label>
                    <input type="date" name="fecha_inicio" class="form-control" 
                           value="<?php echo htmlspecialchars($filtros['fecha_inicio']); ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold" style="font-size: 0.85rem;">Fecha Fin</label>
                    <input type="date" name="fecha_fin" class="form-control" 
                           value="<?php echo htmlspecialchars($filtros['fecha_fin']); ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold" style="font-size: 0.85rem;">Rol</label>
                    <select name="id_rol" class="form-select">
                        <option value="">Todos los roles</option>
                        <?php foreach ($roles as $rol): ?>
                            <option value="<?php echo $rol['id_rol']; ?>" 
                                    <?php echo $filtros['id_rol'] == $rol['id_rol'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($rol['nombre']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold" style="font-size: 0.85rem;">Método de Pago</label>
                    <select name="id_metodo" class="form-select">
                        <option value="">Todos</option>
                        <?php foreach ($metodosPago as $metodo): ?>
                            <option value="<?php echo $metodo['id_metodo']; ?>"
                                    <?php echo $filtros['id_metodo'] == $metodo['id_metodo'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($metodo['nombre']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold" style="font-size: 0.85rem;">Estado</label>
                    <select name="estado" class="form-select">
                        <option value="">Todos</option>
                        <option value="completada" <?php echo $filtros['estado'] == 'completada' ? 'selected' : ''; ?>>Completada</option>
                        <option value="anulada" <?php echo $filtros['estado'] == 'anulada' ? 'selected' : ''; ?>>Anulada</option>
                    </select>
                </div>
            </div>
            <div class="d-flex gap-2 mt-3">
                <button type="submit" class="btn btn-filter">
                    <i class="fas fa-filter me-2"></i> Aplicar Filtros
                </button>
                <a href="historial_ventas.php" class="btn btn-reset">
                    <i class="fas fa-redo me-2"></i> Limpiar
                </a>
            </div>
        </form>
    </div>

    <!-- Tabla de Ventas -->
    <div class="table-ventas">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>FOLIO</th>
                        <th>FECHA Y HORA</th>
                        <th>USUARIO / CAJERO</th>
                        <th>ROL</th>
                        <th>MÉTODO PAGO</th>
                        <th>TIPO MOVIMIENTO</th>
                        <th>ITEMS</th>
                        <th>TOTAL</th>
                        <th class="text-center">ACCIONES</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($ventas)): ?>
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No se encontraron ventas con los filtros aplicados</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($ventas as $venta): ?>
                            <tr>
                                <td>
                                    <a href="detalle_venta.php?id=<?php echo $venta['id_venta']; ?>" 
                                       class="folio-link">
                                        #<?php echo str_pad($venta['id_venta'], 5, '0', STR_PAD_LEFT); ?>
                                    </a>
                                </td>
                                <td>
                                    <div style="font-size: 0.9rem;">
                                        <?php echo date('d/m/Y', strtotime($venta['fecha_venta'])); ?>
                                    </div>
                                    <div class="text-muted" style="font-size: 0.8rem;">
                                        <?php echo date('h:i A', strtotime($venta['fecha_venta'])); ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div style="width: 32px; height: 32px; background: #e0e7ff; border-radius: 50%; 
                                                    display: flex; align-items: center; justify-content: center; 
                                                    color: #4338ca; font-weight: bold; font-size: 0.85rem;">
                                            <?php echo strtoupper(substr($venta['nombre_vendedor'], 0, 1)); ?>
                                        </div>
                                        <span class="ms-2 fw-semibold" style="font-size: 0.9rem;">
                                            <?php echo htmlspecialchars($venta['nombre_vendedor']); ?>
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <?php
                                    $rolClass = 'badge-empleado';
                                    if (stripos($venta['rol_vendedor'], 'admin') !== false) {
                                        $rolClass = 'badge-admin';
                                    } elseif (stripos($venta['rol_vendedor'], 'cajero') !== false) {
                                        $rolClass = 'badge-cajero';
                                    }
                                    ?>
                                    <span class="badge-rol <?php echo $rolClass; ?>">
                                        <?php echo htmlspecialchars($venta['rol_vendedor']); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge-metodo">
                                        <i class="fas fa-credit-card me-1"></i>
                                        <?php echo htmlspecialchars($venta['metodo_pago']); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="tipo-movimiento">
                                        <?php echo $venta['tipo_movimiento'] ? htmlspecialchars($venta['tipo_movimiento']) : 'N/A'; ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge-items">
                                        <?php echo $venta['total_items']; ?> und
                                    </span>
                                </td>
                                <td>
                                    <span class="fw-bold text-dark" style="font-size: 1rem;">
                                        $<?php echo number_format($venta['total'], 0, ',', '.'); ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="detalle_venta.php?id=<?php echo $venta['id_venta']; ?>" 
                                       class="btn btn-sm btn-outline-primary rounded-circle" 
                                       style="width: 32px; height: 32px; padding: 0; display: inline-flex; 
                                              align-items: center; justify-content: center;"
                                       title="Ver detalle">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Resumen al final -->
    <?php if (!empty($ventas)): ?>
        <div class="mt-3 p-3 bg-light rounded">
            <div class="row text-center">
                <div class="col-md-4">
                    <small class="text-muted d-block">Mostrando</small>
                    <strong><?php echo count($ventas); ?> ventas</strong>
                </div>
                <div class="col-md-4">
                    <small class="text-muted d-block">Período</small>
                    <strong>
                        <?php echo date('d/m/Y', strtotime($filtros['fecha_inicio'])); ?> - 
                        <?php echo date('d/m/Y', strtotime($filtros['fecha_fin'])); ?>
                    </strong>
                </div>
                <div class="col-md-4">
                    <small class="text-muted d-block">Total Ingresos</small>
                    <strong class="text-success">$<?php echo number_format($totalIngresos, 0, ',', '.'); ?></strong>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php
echo '</section></main></div>';
require_once __DIR__ . '/../layouts/footer.php';
?>
