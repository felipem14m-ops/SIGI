<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) { header("Location: ../Usuario/login.php"); exit; }

$titulo = "Punto de Venta";

$productos      = [];
$categorias     = [];
$metodos_pago   = [];
$resumen_hoy    = ['total_ingresos' => 0];
$_error_bd      = '';

try {
    require_once __DIR__ . '/../../config/database.php';
    require_once __DIR__ . '/../../models/Producto.php';
    require_once __DIR__ . '/../../models/Categoria.php';
    require_once __DIR__ . '/../../models/Venta.php';
    $db           = (new Database())->conectar();
    $productos    = (new Producto($db))->listarActivos();
    $categorias   = (new Categoria($db))->listarActivas();
    $id_usuario   = $_SESSION['usuario']['id_usuario'] ?? 0;
    $resumen_hoy  = (new Venta($db))->resumenDiario($id_usuario);

    // Métodos de pago directo desde BD
    $stmtMP = $db->query("SELECT id_metodo, nombre FROM metodos_pago WHERE activo = 1 ORDER BY nombre");
    $metodos_pago = $stmtMP ? $stmtMP->fetchAll(PDO::FETCH_ASSOC) : [];
} catch (Throwable $e) {
    $_error_bd = 'No se pudieron cargar los datos.';
    error_log("[SIGI] ventas.php (empleado) Error: " . $e->getMessage());
}

$usuario_sesion = $_SESSION['usuario'] ?? [];

require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidebar.php';
?>

<style>
    :root {
        --pos-blue: #1e3a8a;
        --pos-accent: #2563eb;
        --pos-bg: #f8fafc;
        --pos-border: #e2e8f0;
    }

    body { background-color: var(--pos-bg); overflow: hidden; font-family: 'Inter', sans-serif; }

    .pos-main-layout {
        display: grid;
        grid-template-columns: 1fr 400px;
        gap: 1.5rem;
        height: calc(100vh - 280px);
        align-items: start;
    }

    @media (max-width: 1200px) {
        .pos-main-layout { grid-template-columns: 1fr; height: auto; }
    }

    /* BANNER PREMIUM (Golden Standard) */
    .module-banner {
        background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
        border-radius: 20px;
        padding: 2rem;
        color: #fff;
        position: relative;
        overflow: hidden;
        margin-bottom: 1.5rem;
        box-shadow: 0 10px 25px rgba(37, 99, 235, 0.15);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .module-banner::before { content: ''; position: absolute; top: -30px; right: -30px; width: 150px; height: 150px; background: rgba(255,255,255,0.08); border-radius: 50%; }
    .banner-label { background: rgba(255,255,255,0.15); padding: 0.3rem 0.8rem; border-radius: 50px; font-size: 0.65rem; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; display: inline-block; margin-bottom: 0.5rem; backdrop-filter: blur(4px); }

    .cart-pos-header {
        background: linear-gradient(135deg, var(--pos-blue) 0%, var(--pos-accent) 100%);
        border-radius: 16px;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.75rem 1.5rem;
        box-shadow: 0 8px 20px rgba(30, 58, 138, 0.12);
    }

    /* CONTENEDORES */
    .catalog-container {
        background: #fff;
        border-radius: 20px;
        border: 1px solid var(--pos-border);
        padding: 1.5rem;
        overflow-y: auto;
    }

    .cart-container {
        background: #fff;
        border-radius: 20px;
        border: 1px solid var(--pos-border);
        display: flex;
        flex-direction: column;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0,0,0,0.04);
    }

    /* TABLA ESPACIOSA */
    .table-pos img { width: 50px; height: 50px; object-fit: contain; border-radius: 10px; background: #f8fafc; }
    .table-pos th { background: #f8fafc; color: #64748b; font-size: 0.8rem; text-transform: uppercase; font-weight: 800; padding: 1.25rem 1rem; border-bottom: 2px solid #f1f5f9; }
    .table-pos td { vertical-align: middle; padding: 1.1rem 1rem; border-bottom: 1px solid #f1f5f9; }
    
    .prod-name { font-size: 1.05rem; font-weight: 800; color: #0f172a; display: block; margin-bottom: 2px; }
    .prod-cat { font-size: 0.8rem; color: #64748b; font-weight: 600; }

    .badge-stock { background: #eff6ff; color: #1e40af; font-weight: 700; font-size: 0.75rem; padding: 0.4rem 0.8rem; border-radius: 8px; }

    /* BOTON AÑADIR PRO */
    .btn-add-pro {
        background: var(--pos-accent); color: #fff; border: none; padding: 0.5rem 0.75rem;
        border-radius: 8px; font-weight: 700; transition: 0.2s;
    }
    .btn-add-pro:hover { background: var(--pos-blue); transform: scale(1.05); }

    /* CARRITO PREMIUM */
    .cart-list { flex: 1; overflow-y: auto; padding: 1.25rem; }
    .cart-item {
        display: flex; align-items: center; gap: 1rem;
        padding: 1rem; background: #fff; border-radius: 16px; margin-bottom: 0.75rem;
        border: 1px solid var(--pos-border); transition: 0.3s;
        box-shadow: 0 2px 6px rgba(0,0,0,0.02);
    }
    .cart-item:hover { transform: translateY(-2px); box-shadow: 0 8px 15px rgba(0,0,0,0.05); border-color: var(--pos-accent); }
    
    .cart-item-img { width: 45px; height: 45px; object-fit: contain; border-radius: 10px; background: #f8fafc; border: 1px solid #f1f5f9; }
    
    .qty-controls {
        display: flex; align-items: center; background: #f1f5f9; border-radius: 10px; padding: 2px;
    }
    .btn-qty {
        width: 28px; height: 28px; border: none; background: #fff; color: var(--pos-blue);
        border-radius: 8px; font-weight: 800; display: flex; align-items: center; justify-content: center;
        transition: 0.2s; box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    .btn-qty:hover { background: var(--pos-accent); color: #fff; }
    .qty-val { width: 30px; text-align: center; font-weight: 800; font-size: 0.9rem; color: var(--pos-blue); }

    .cart-footer { padding: 1.5rem; background: #fff; border-top: 1px solid var(--pos-border); border-radius: 0 0 20px 20px; }
    .total-display { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; }
    .total-display h3 { font-weight: 900; margin: 0; color: var(--pos-accent); font-size: 1.85rem; letter-spacing: -1px; }

    .btn-checkout {
        width: 100%; padding: 1rem; border-radius: 12px; border: none;
        background: var(--pos-accent); color: #fff; font-weight: 800; font-size: 1.05rem;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2); transition: 0.3s;
    }
    .btn-checkout:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(37, 99, 235, 0.3); }

    /* DATATABLES TOOLBAR & PAGINATION */
    .catalog-header-actions { display: flex; gap: 0.75rem; margin-bottom: 1rem; }
    .custom-search-input { border-radius: 10px; border: 1px solid var(--pos-border); padding: 0.5rem 1rem; width: 250px; font-size: 0.9rem; }
    .custom-cat-filter { border-radius: 10px; border: 1px solid var(--pos-border); padding: 0.5rem 1rem; min-width: 180px; font-size: 0.9rem; background-color: #fff; font-weight: 600; color: #475569; }

    .dataTables_paginate {
        margin-top: 1rem !important;
        display: flex;
        justify-content: flex-end;
        gap: 5px;
    }
    .dataTables_paginate .paginate_button {
        background: #f1f5f9 !important;
        border: none !important;
        border-radius: 8px !important;
        padding: 0.4rem 0.8rem !important;
        font-weight: 700 !important;
        color: #475569 !important;
        cursor: pointer;
        transition: 0.2s;
    }
    .dataTables_paginate .paginate_button.current {
        background: #2563eb !important;
        color: #fff !important;
    }
</style>

<!-- jsPDF & AutoTable - Generación de PDF en cliente -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>
<script src="../../JS/PDFGenerator.js"></script>

<div class="container-fluid py-3">

    <!-- Banner de módulo Premium -->
    <div class="module-banner">
        <div class="d-flex align-items-center gap-3 position-relative" style="z-index: 2;">
            <div style="background:rgba(255,255,255,0.15); width:60px; height:60px; border-radius:16px; display:flex; align-items:center; justify-content:center; font-size:1.8rem; backdrop-filter: blur(8px); border: 1px solid rgba(255,255,255,0.2);">
                <i class="fas fa-cash-register text-white"></i>
            </div>
            <div>
                <span class="banner-label" style="margin-bottom: 0.2rem;">Operación Comercial</span>
                <h2 class="fw-bold m-0 text-white" style="font-family: 'Poppins', sans-serif; letter-spacing: -1px; font-size: 1.8rem;">Punto de Venta</h2>
                <p class="m-0 text-white opacity-90 fw-light small">Atención al cliente y registro de ventas.</p>
            </div>
        </div>
        <div class="text-end position-relative" style="z-index: 2;">
            <div class="small fw-bold text-uppercase opacity-75" style="font-size: 0.65rem; letter-spacing: 1px; color: rgba(255,255,255,0.8);">Mis Ventas Hoy</div>
            <div class="fw-bold fs-2 text-white" style="font-family: 'Poppins', sans-serif; letter-spacing: -1.5px;">$<?= number_format($resumen_hoy['total_ingresos'], 0, ',', '.') ?></div>
        </div>
    </div>

    <!-- Layout POS -->
    <div class="pos-main-layout">

    <!-- FILA 2: CONTENIDO -->
    <div class="catalog-container">
        <!-- Barra de Filtros Personalizada -->
        <div class="catalog-header-actions">
            <input type="text" id="posSearch" class="custom-search-input" placeholder="Buscar por nombre o código...">
            <select id="posCatFilter" class="custom-cat-filter">
                <option value="">Todas las Categorías</option>
                <?php foreach($categorias as $cat): ?>
                    <option value="<?= htmlspecialchars($cat['nombre']) ?>"><?= htmlspecialchars($cat['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <table id="tablaPOS" class="table table-pos w-100">
            <thead>
                <tr>
                    <th class="text-center">IMG</th>
                    <th>Producto</th>
                    <th>Stock</th>
                    <th>Precio</th>
                    <th class="text-center">Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($productos as $p): ?>
                    <?php if($p['stock_actual'] > 0): ?>
                    <tr>
                        <td class="text-center"><img src="<?= $p['imagen'] ? "../../IMG/productos/".$p['imagen'] : "../../IMG/no-image.png" ?>"></td>
                        <td>
                            <span style="display:none;"><?= htmlspecialchars($p['codigoUnico'] ?? '') ?></span>
                            <span class="prod-name"><?= htmlspecialchars($p['nombre']) ?></span>
                            <span class="prod-cat"><?= htmlspecialchars($p['nombre_categoria']) ?></span>
                        </td>
                        <td><span class="badge-stock"><?= $p['stock_actual'] ?> unidades</span></td>
                        <td class="fw-bold text-dark">$<?= number_format($p['precio_venta'], 0, ',', '.') ?></td>
                        <td class="text-center">
                            <button class="btn-add-pro" onclick='agregarAlCarrito(<?= json_encode($p) ?>)'>
                                <i class="fas fa-plus me-1"></i> Añadir
                            </button>
                        </td>
                    </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="cart-container">
        <div class="cart-pos-header" style="border-radius: 20px 20px 0 0; margin-bottom: 0;">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-shopping-cart text-white"></i>
                <h5 class="fw-bold m-0 text-white" style="font-family: 'Poppins', sans-serif; font-size: 1rem;">Carrito</h5>
            </div>
            <button class="btn btn-link text-white p-0 text-decoration-none small" onclick="vaciarCarrito()">
                <i class="fas fa-trash-alt me-1"></i> Limpiar
            </button>
        </div>
        <div class="cart-list" id="itemsCarrito">
            <div class="text-center py-5 opacity-25">
                <i class="fas fa-shopping-basket fa-3x mb-2"></i>
                <p class="small fw-bold">Selecciona productos</p>
            </div>
        </div>

        <div class="cart-footer">
            <div class="mb-3">
                <label class="small fw-bold text-muted text-uppercase mb-1 d-block" style="font-size: 0.65rem;">Método de Pago</label>
                <select id="id_metodo" class="form-select form-select-sm rounded-3 fw-bold border-2" style="font-size: 0.85rem;">
                    <?php foreach($metodos_pago as $mp): ?>
                        <option value="<?= $mp['id_metodo'] ?>"><?= htmlspecialchars($mp['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="total-display">
                <span class="fw-bold text-muted small">TOTAL A COBRAR:</span>
                <h3 id="cartTotal">$0.00</h3>
            </div>
            <button class="btn-checkout" onclick="procesarVenta()">
                <i class="fas fa-check-circle me-1"></i> FINALIZAR VENTA (F10)
            </button>
        </div>
    </div>
</div>

<?php 
echo '</section></main></div>';
require_once __DIR__ . '/../layouts/footer.php'; 
?>

<script>
let carrito = [];

$(document).ready(function() {
    const table = $('#tablaPOS').DataTable({
        pageLength: 8,
        language: { paginate: { previous: "«", next: "»" } },
        dom: 'rt<"d-flex justify-content-end mt-2"p>', // Ocultamos el search original de DataTables
    });

    // Lógica de búsqueda personalizada
    $('#posSearch').on('keyup', function() {
        table.search(this.value).draw();
    });

    // Lógica de filtro por categoría
    $('#posCatFilter').on('change', function() {
        table.column(1).search(this.value).draw();
    });
});

function agregarAlCarrito(prod) {
    if (prod.stock_actual <= 0) {
        Swal.fire({ 
            icon: 'error', 
            title: '¡Producto Agotado!', 
            text: 'Este producto no tiene stock disponible para la venta.',
            confirmButtonColor: '#ef4444'
        });
        return;
    }

    const existe = carrito.find(x => x.id_producto === prod.id_producto);
    if(existe) {
        if(existe.cantidad + 1 > prod.stock_actual) {
            Swal.fire({ 
                icon: 'warning', 
                title: 'Límite de Stock', 
                text: 'No hay más unidades en inventario.',
                timer: 2000, 
                showConfirmButton: false 
            });
            return;
        }
        existe.cantidad++;
    } else {
        carrito.push({...prod, cantidad: 1});
    }
    renderCarrito();
}

function renderCarrito() {
    const cont = $('#itemsCarrito');
    cont.empty();
    let total = 0;

    if(carrito.length === 0) {
        cont.html('<div class="text-center py-5 opacity-25"><i class="fas fa-shopping-basket fa-3x mb-2"></i><p class="small fw-bold">Selecciona productos</p></div>');
    }

    carrito.forEach((item, idx) => {
        const sub = item.cantidad * item.precio_venta;
        total += sub;
        const imgPath = item.imagen ? `../../IMG/productos/${item.imagen}` : `../../IMG/no-image.png`;
        cont.append(`
            <div class="cart-item animate__animated animate__fadeInRight animate__faster">
                <img src="${imgPath}" class="cart-item-img">
                <div style="flex:1; min-width:0;">
                    <div class="fw-bold text-truncate" style="font-size:0.9rem; color:#0f172a;" title="${item.nombre}">${item.nombre}</div>
                    <div class="fw-bold" style="font-size:0.85rem; color:var(--pos-accent);">$${parseFloat(item.precio_venta).toLocaleString('es-CO')}</div>
                </div>
                <div class="qty-controls">
                    <button class="btn-qty" onclick="cambiarCantidad(${idx}, -1)">-</button>
                    <div class="qty-val">${item.cantidad}</div>
                    <button class="btn-qty" onclick="cambiarCantidad(${idx}, 1)">+</button>
                </div>
                <button class="btn btn-link text-danger p-1 border-0" onclick="eliminarDelCarrito(${idx})" title="Quitar">
                    <div style="background: #fee2e2; width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-trash-alt" style="font-size: 0.85rem;"></i>
                    </div>
                </button>
            </div>
        `);
    });

    $('#cartTotal').text('$' + total.toLocaleString('es-CO', {minimumFractionDigits: 0}));
}

function cambiarCantidad(idx, delta) {
    const item = carrito[idx];
    const nuevaCant = item.cantidad + delta;
    if(nuevaCant > 0) {
        if(delta > 0 && nuevaCant > item.stock_actual) return;
        item.cantidad = nuevaCant;
    } else {
        carrito.splice(idx, 1);
    }
    renderCarrito();
}

function eliminarDelCarrito(idx) {
    carrito.splice(idx, 1);
    renderCarrito();
}

function vaciarCarrito() {
    if(carrito.length === 0) return;
    carrito = [];
    renderCarrito();
}

async function procesarVenta() {
    if(carrito.length === 0) return;
    const id_metodo = $('#id_metodo').val();
    
    let totalVenta = 0;
    const productosParaBackend = carrito.map(item => {
        const precio = parseFloat(item.precio_venta);
        totalVenta += item.cantidad * precio;
        return {
            id_producto: item.id_producto,
            cantidad: item.cantidad,
            precio: precio
        };
    });

    // Determinar si es pago en efectivo para pedir monto recibido
    const metodoTexto = $('#id_metodo option:selected').text().toLowerCase();
    const esEfectivo  = metodoTexto.includes('efectivo');
    
    let montoRecibido = totalVenta;

    if (esEfectivo) {
        // Paso 1: Solicitar monto recibido (Solo para Efectivo)
        const { value: monto } = await Swal.fire({
            title: 'Monto Recibido',
            html: `
                <div style="text-align: left; margin-bottom: 1.5rem;">
                    <div style="background: #f8fafc; padding: 1rem; border-radius: 12px; margin-bottom: 1rem;">
                        <div style="font-size: 0.85rem; color: #64748b; font-weight: 600; margin-bottom: 0.5rem;">TOTAL A COBRAR</div>
                        <div style="font-size: 2rem; font-weight: 800; color: #2563eb;">$${totalVenta.toLocaleString('es-CO')}</div>
                    </div>
                    <label style="font-size: 0.9rem; font-weight: 600; color: #475569; display: block; margin-bottom: 0.5rem;">
                        <i class="fas fa-money-bill-wave me-2"></i>Ingrese el monto recibido:
                    </label>
                    <input type="number" id="montoRecibidoInput" class="swal2-input" 
                           placeholder="0.00" step="1" min="${totalVenta}" 
                           style="font-size: 1.5rem; font-weight: 700; text-align: center; margin: 0; width: 100%;">
                </div>
            `,
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#2563eb',
            cancelButtonColor: '#64748b',
            confirmButtonText: '<i class="fas fa-calculator me-2"></i>Calcular Cambio',
            cancelButtonText: 'Cancelar',
            focusConfirm: false,
            didOpen: () => {
                const input = document.getElementById('montoRecibidoInput');
                input.focus();
                input.addEventListener('keypress', (e) => {
                    if (e.key === 'Enter') {
                        Swal.clickConfirm();
                    }
                });
            },
            preConfirm: () => {
                const montoVal = parseFloat(document.getElementById('montoRecibidoInput').value);
                if (!montoVal || isNaN(montoVal)) {
                    Swal.showValidationMessage('Por favor ingrese un monto válido');
                    return false;
                }
                if (montoVal < totalVenta) {
                    Swal.showValidationMessage(`El monto debe ser mayor o igual a $${totalVenta.toLocaleString('es-CO')}`);
                    return false;
                }
                return montoVal;
            }
        });

        if (!monto) return;
        montoRecibido = monto;
    }

    // Calcular cambio
    const cambio = montoRecibido - totalVenta;

    // Paso 2: Mostrar cambio y confirmar
    const res = await Swal.fire({
        title: '¿Confirmar Venta?',
        html: `
            <div style="text-align: left;">
                <div style="background: #f8fafc; padding: 1.25rem; border-radius: 12px; margin-bottom: 1rem;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem; padding-bottom: 0.75rem; border-bottom: 2px dashed #e2e8f0;">
                        <span style="font-weight: 600; color: #64748b;">Total:</span>
                        <span style="font-weight: 800; color: #0f172a; font-size: 1.1rem;">$${totalVenta.toLocaleString('es-CO')}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem; padding-bottom: 0.75rem; border-bottom: 2px dashed #e2e8f0;">
                        <span style="font-weight: 600; color: #64748b;">Recibido:</span>
                        <span style="font-weight: 800; color: #0f172a; font-size: 1.1rem;">$${montoRecibido.toLocaleString('es-CO')}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-weight: 700; color: #2563eb; font-size: 1rem;">CAMBIO:</span>
                        <span style="font-weight: 900; color: #10b981; font-size: 1.8rem;">$${cambio.toLocaleString('es-CO')}</span>
                    </div>
                </div>
                ${cambio === 0 ? '<div style="text-align: center; color: #10b981; font-weight: 600; font-size: 0.9rem;"><i class="fas fa-check-circle me-1"></i>Monto exacto</div>' : ''}
            </div>
        `,
        icon: 'success',
        showCancelButton: true,
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#64748b',
        confirmButtonText: '<i class="fas fa-check-circle me-2"></i>Finalizar Venta',
        cancelButtonText: 'Cancelar'
    });
    
    if(!res.isConfirmed) return;
    try {
        const resp = await fetch('../../controllers/VentaController.php?accion=crear', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ 
                productos: productosParaBackend, 
                id_metodo: id_metodo,
                total: totalVenta,
                monto_recibido: montoRecibido,
                cambio_devuelto: cambio
            })
        });
        const data = await resp.json();
        if(data.ok) {
            const result = await Swal.fire({
                icon: 'success',
                title: '¡Venta Realizada!',
                text: '¿Desea descargar la factura en PDF?',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#64748b',
                confirmButtonText: '<i class="fas fa-file-pdf me-2"></i>Descargar Factura',
                cancelButtonText: 'No, finalizar'
            });

            if (result.isConfirmed) {
                const gen = new SIGIPDFGenerator('Factura de Venta');
                await gen.generateFactura(data.id_venta);
            }
            location.reload();
        } else {
            Swal.fire('Error', data.error || 'No se pudo completar la venta', 'error');
        }
    } catch(err) {
        Swal.fire('Error', 'Error de red al completar la venta', 'error');
    }
}

$(document).on('keydown', function(e) { if(e.key === 'F10') { e.preventDefault(); procesarVenta(); } });
</script>  


