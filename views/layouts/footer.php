<footer class="footer-dashboard">
    <div class="container-fluid">
        <div class="footer-content">
            <!-- Branding & About -->
            <div class="footer-brand-section">
                <div class="footer-logo">
                    <i class="fas fa-store"></i>
                    <span>SIGI</span>
                </div>
                <p class="footer-description">
                    Sistema de gestión de inventario diseñado para optimizar tu negocio con tecnología de vanguardia y control total en tiempo real.
                </p>
                <div class="footer-social-links">
                    <a href="#" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" title="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="#" title="WhatsApp"><i class="fab fa-whatsapp"></i></a>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="footer-links-section">
                <h5 class="footer-title">Enlaces Rápidos</h5>
                <ul class="footer-list">
                    <li><a href="<?= $base_url ?>views/Dashboard/Admin.php"><i class="fas fa-chevron-right"></i> Panel Principal</a></li>
                    <li><a href="<?= $base_url ?>views/Dashboard/productos.php"><i class="fas fa-chevron-right"></i> Catálogo de Productos</a></li>
                    <li><a href="<?= $base_url ?>views/Dashboard/ventas.php"><i class="fas fa-chevron-right"></i> Punto de Venta</a></li>
                    <li><a href="<?= $base_url ?>views/Dashboard/reportes.php"><i class="fas fa-chevron-right"></i> Reportes</a></li>
                </ul>
            </div>

            <!-- Support & Contact -->
            <div class="footer-contact-section">
                <h5 class="footer-title">Soporte Técnico</h5>
                <ul class="footer-contact-list">
                    <li>
                        <i class="fas fa-envelope"></i>
                        <span>soporte@sigi.com</span>
                    </li>
                    <li>
                        <i class="fas fa-phone-alt"></i>
                        <span>+57 1 234 5678</span>
                    </li>
                    <li>
                        <i class="fas fa-clock"></i>
                        <span>Lun - Vie: 8:00 AM - 6:00 PM</span>
                    </li>
                </ul>
            </div>
        </div>
        
        <div class="footer-bottom-bar">
            <div class="copyright">
                &copy; <?= date('Y') ?> <strong>SIGI</strong> - Sistema de Gestión de Inventario. Todos los derechos reservados.
            </div>
            <div class="footer-legal">
                <a href="#">Privacidad</a>
                <a href="#">Términos</a>
                <a href="#">Ayuda</a>
            </div>
        </div>
    </div>
</footer>

<!-- Bootstrap JS (primero - no depende de jQuery) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- jQuery (después de Bootstrap para evitar conflictos) -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!-- DataTables JS (requiere jQuery) -->
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// =========================================================================
// CONFIGURACIÓN GLOBAL DE DATATABLES (Español)
// =========================================================================
if (typeof $.fn.dataTable !== 'undefined') {
    $.extend(true, $.fn.dataTable.defaults, {
        language: {
            processing: "Procesando...",
            search: "",
            searchPlaceholder: "Buscar...",
            lengthMenu: "Mostrar _MENU_ registros",
            info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
            infoEmpty: "Sin registros disponibles",
            infoFiltered: "(filtrado de _MAX_ registros totales)",
            loadingRecords: "Cargando...",
            zeroRecords: "No se encontraron resultados",
            emptyTable: "No hay datos disponibles en la tabla",
            paginate: {
                first: '<i class="fas fa-angle-double-left"></i>',
                previous: '<i class="fas fa-chevron-left"></i>',
                next: '<i class="fas fa-chevron-right"></i>',
                last: '<i class="fas fa-angle-double-right"></i>'
            }
        },
        pageLength: 10,
        lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "Todos"]],
        responsive: true,
        dom: '<"row align-items-center mb-3"<"col-md-6"l><"col-md-6"f>>rt<"row align-items-center mt-3"<"col-md-6"i><"col-md-6"p>>'
    });
}

// =========================================================================
// FUNCIONES GLOBALES DE SWEETALERT PARA ACCIONES CRUD
// =========================================================================

/**
 * Confirmación genérica para eliminar/desactivar registros.
 * @param {string} url - URL de la acción a ejecutar
 * @param {string} nombre - Nombre del elemento a eliminar (para el mensaje)
 * @param {string} accion - Texto de la acción: 'eliminar', 'desactivar', 'activar'
 */
function confirmarAccion(url, nombre, accion = 'eliminar') {
    const colores = {
        'eliminar': { color: '#dc2626', icon: 'warning', btnText: 'Sí, eliminar' },
        'desactivar': { color: '#f59e0b', icon: 'warning', btnText: 'Sí, desactivar' },
        'activar': { color: '#10b981', icon: 'question', btnText: 'Sí, activar' }
    };
    const config = colores[accion] || colores['eliminar'];
    
    Swal.fire({
        title: '¿Estás seguro?',
        html: `Vas a <strong>${accion}</strong> <strong>${nombre}</strong>.<br>Esta acción puede afectar registros relacionados.`,
        icon: config.icon,
        showCancelButton: true,
        confirmButtonColor: config.color,
        cancelButtonColor: '#64748b',
        confirmButtonText: config.btnText,
        cancelButtonText: 'Cancelar',
        reverseButtons: true,
        customClass: {
            popup: 'rounded-4',
            confirmButton: 'rounded-3 px-4',
            cancelButton: 'rounded-3 px-4'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = url;
        }
    });
}

/**
 * Notificación tipo Toast para acciones exitosas.
 * @param {string} mensaje - Texto del mensaje
 * @param {string} tipo - 'success', 'error', 'warning', 'info'
 */
function mostrarToast(mensaje, tipo = 'success') {
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        customClass: { popup: 'rounded-3' }
    });
    Toast.fire({ icon: tipo, title: mensaje });
}
</script>

<?php
// Mostrar alertas de sesión pendientes (generadas por controladores)
if (isset($_SESSION['alert'])): ?>
<script>
    Swal.fire({
        icon:  '<?= htmlspecialchars($_SESSION['alert']['icon'],  ENT_QUOTES, 'UTF-8') ?>',
        title: '<?= htmlspecialchars($_SESSION['alert']['title'], ENT_QUOTES, 'UTF-8') ?>',
        text:  '<?= htmlspecialchars($_SESSION['alert']['text'],  ENT_QUOTES, 'UTF-8') ?>',
        confirmButtonColor: '#2563eb',
        customClass: { popup: 'rounded-4', confirmButton: 'rounded-3 px-4' }
    });
</script>
<?php unset($_SESSION['alert']); endif; ?>

</body>
</html>