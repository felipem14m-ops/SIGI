/**
 * ============================================================================
 * LIBRERÍA: SIGIPDFGenerator (Golden Standard)
 * Sistema: SIGI — Gestión de Inventario
 * ============================================================================
 * Responsabilidad: Generación centralizada de documentos PDF profesionales
 * utilizando jsPDF y jsPDF-AutoTable.
 * ============================================================================
 */

class SIGIPDFGenerator {
    /**
     * @param {string} title - Título del reporte/documento
     * @param {string} orientation - 'p' (portrait) o 'l' (landscape)
     */
    constructor(title, orientation = 'l') {
        if (!window.jspdf) {
            console.error('jsPDF no está cargado. Asegúrese de incluir la librería vía CDN.');
            return;
        }
        
        const { jsPDF } = window.jspdf;
        this.pdf = new jsPDF({ orientation: orientation, unit: 'mm', format: 'a4' });
        this.pageTitle = title;
        this.accentColor = [255, 215, 0];  // Dorado Golden Standard
        this.primaryColor = [30, 58, 138]; // Azul Corporativo
        this.secondaryColor = [100, 100, 100];
    }

    /**
     * Añade un encabezado institucional unificado
     */
    addHeader() {
        const pageWidth = this.pdf.internal.pageSize.getWidth();
        
        // Logo / Sigla Sistema
        this.pdf.setFont("helvetica", "bold");
        this.pdf.setFontSize(32);
        this.pdf.setTextColor(this.accentColor[0], this.accentColor[1], this.accentColor[2]);
        this.pdf.text("SIGI", pageWidth / 2, 20, { align: 'center' });

        // Eslogan / Descripción
        this.pdf.setFontSize(10);
        this.pdf.setTextColor(this.secondaryColor[0], this.secondaryColor[1], this.secondaryColor[2]);
        this.pdf.setFont("helvetica", "normal");
        this.pdf.text("SISTEMA INTEGRAL DE GESTIÓN DE INVENTARIOS", pageWidth / 2, 26, { align: 'center' });

        // Título del Documento
        this.pdf.setFontSize(18);
        this.pdf.setTextColor(0, 0, 0);
        this.pdf.setFont("helvetica", "bold");
        this.pdf.text(this.pageTitle.toUpperCase(), pageWidth / 2, 38, { align: 'center' });

        // Línea Dorada Decorativa
        this.pdf.setDrawColor(this.accentColor[0], this.accentColor[1], this.accentColor[2]);
        this.pdf.setLineWidth(0.8);
        this.pdf.line(20, 42, pageWidth - 20, 42);
    }

    /**
     * Añade un pie de página institucional
     */
    addFooter() {
        const totalPages = this.pdf.internal.getNumberOfPages();
        const pageWidth = this.pdf.internal.pageSize.getWidth();
        const pageHeight = this.pdf.internal.pageSize.getHeight();

        for (let i = 1; i <= totalPages; i++) {
            this.pdf.setPage(i);
            this.pdf.setFontSize(9);
            this.pdf.setTextColor(150, 150, 150);
            this.pdf.setFont("helvetica", "italic");
            
            this.pdf.line(20, pageHeight - 15, pageWidth - 20, pageHeight - 15);
            this.pdf.text(`SIGI © 2026 - Gestión de Alta Eficiencia | Página ${i} de ${totalPages}`, pageWidth / 2, pageHeight - 10, { align: 'center' });
        }
    }

    /**
     * Genera una tabla de datos profesional
     * @param {Array} headers - Encabezados de la tabla
     * @param {Array} data - Filas de datos
     * @param {Object} options - Opciones adicionales de AutoTable
     */
    generateTable(headers, data, options = {}) {
        this.addHeader();
        
        this.pdf.autoTable({
            startY: 48,
            head: [headers],
            body: data,
            theme: 'grid',
            headStyles: { 
                fillColor: this.primaryColor, 
                textColor: 255, 
                fontStyle: 'bold', 
                halign: 'center',
                fontSize: 10
            },
            styles: { fontSize: 9, cellPadding: 3 },
            alternateRowStyles: { fillColor: [248, 250, 252] },
            margin: { left: 20, right: 20 },
            ...options
        });

        this.addFooter();
        const fileName = `${this.pageTitle.replace(/\s+/g, '_')}_${new Date().toISOString().slice(0, 10)}.pdf`;
        this.pdf.save(fileName);

        // --- NUEVO: Subir automáticamente el archivo a la BD para el historial ---
        try {
            const blob = this.pdf.output('blob');
            const formData = new FormData();
            formData.append('archivo_pdf', blob, fileName);
            formData.append('tipo_reporte', this.pageTitle);
            formData.append('parametros', JSON.stringify(options));
            
            fetch('../../controllers/ReporteController.php?accion=guardar_reporte', {
                method: 'POST',
                body: formData
            }).then(resp => resp.json()).then(res => {
                if(res.ok && window.location.href.includes('Adminreportes.php')) {
                    // Refrescar para que el DataTables lea el nuevo historial
                    setTimeout(() => window.location.reload(), 1500);
                }
            }).catch(err => console.error("Error silencioso al guardar historial:", err));
        } catch (error) {
            console.error("Error al procesar el Blob del PDF:", error);
        }
    }

    /**
     * Genera una factura detallada de venta
     * @param {number} idVenta - ID de la venta a consultar
     */
    async generateFactura(idVenta) {
        try {
            const resp = await fetch(`../../controllers/VentaController.php?accion=detalle&id=${idVenta}`);
            const result = await resp.json();
            if (!result.ok) throw new Error(result.error);

            const { venta, productos } = result;
            const pageWidth = this.pdf.internal.pageSize.getWidth();
            
            this.addHeader();

            // Bloque de información de factura
            this.pdf.setFontSize(11);
            this.pdf.setFont("helvetica", "bold");
            this.pdf.text(`NÚMERO DE FACTURA: #000${venta.id_venta}`, 20, 52);
            
            this.pdf.setFont("helvetica", "normal");
            this.pdf.setFontSize(10);
            this.pdf.text(`Fecha y Hora: ${new Date(venta.fecha_venta).toLocaleString()}`, 20, 58);
            this.pdf.text(`Atendido por: ${venta.cajero}`, 20, 63);
            this.pdf.text(`Método de Pago: ${venta.metodo}`, 20, 68);

            // Tabla de productos
            const headers = ['Descripción del Producto', 'Cant.', 'V. Unitario', 'Subtotal'];
            const tableData = productos.map(p => [
                p.nombre_producto,
                p.cantidad,
                '$' + parseInt(p.precio_unitario).toLocaleString(),
                '$' + parseInt(p.subtotal).toLocaleString()
            ]);

            this.pdf.autoTable({
                startY: 75,
                head: [headers],
                body: tableData,
                theme: 'striped',
                headStyles: { fillColor: this.primaryColor },
                styles: { fontSize: 10, cellPadding: 4 },
                columnStyles: {
                    0: { cellWidth: 'auto' },
                    1: { halign: 'center', cellWidth: 20 },
                    2: { halign: 'right', cellWidth: 35 },
                    3: { halign: 'right', cellWidth: 35 }
                }
            });

            const finalY = this.pdf.lastAutoTable.finalY + 15;
            
            // Resumen de Totales
            this.pdf.setDrawColor(200, 200, 200);
            this.pdf.line(pageWidth - 90, finalY - 5, pageWidth - 20, finalY - 5);
            
            this.pdf.setFontSize(14);
            this.pdf.setFont("helvetica", "bold");
            this.pdf.setTextColor(0, 0, 0);
            this.pdf.text(`TOTAL: $${parseInt(venta.total).toLocaleString()}`, pageWidth - 20, finalY, { align: 'right' });

            if (venta.monto_recibido) {
                this.pdf.setFontSize(10);
                this.pdf.setFont("helvetica", "normal");
                this.pdf.setTextColor(100, 100, 100);
                this.pdf.text(`RECIBIDO: $${parseInt(venta.monto_recibido).toLocaleString()}`, pageWidth - 20, finalY + 7, { align: 'right' });
                
                this.pdf.setFontSize(11);
                this.pdf.setFont("helvetica", "bold");
                this.pdf.setTextColor(16, 163, 74); // Verde éxito
                this.pdf.text(`CAMBIO: $${parseInt(venta.cambio_devuelto || 0).toLocaleString()}`, pageWidth - 20, finalY + 14, { align: 'right' });
            }

            this.addFooter();
            this.pdf.save(`Factura_SIGI_${venta.id_venta}.pdf`);
            return true;
        } catch (e) {
            console.error('Error generando factura:', e);
            return false;
        }
    }
}
