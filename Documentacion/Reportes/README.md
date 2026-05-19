# Módulo de Reportes

Este módulo gestiona la generación de reportes del sistema SIGI, proporcionando información consolidada para la toma de decisiones.

## Funcionalidades Documentadas

1. **[Generar Reporte de Inventario](logica_generar_reporte_inventario.md)** - Generación de reportes PDF con el estado actual del inventario, incluyendo valorización y alertas de stock.

## Archivos Relacionados

- **Vista**: `views/Dashboard/Adminreportes.php`
- **Controlador**: `controllers/ReporteController.php`
- **Modelo**: `models/Reporte.php`
- **Librería**: TCPDF para generación de PDF
- **Tablas Consultadas**: `productos`, `categorias`, `proveedores`

## Características Principales

- Generación de PDF profesionales
- Reportes de inventario con valorización
- Filtros por categoría y estado
- Alertas de stock bajo
- Información de productos con vencimiento próximo
- Estadísticas consolidadas
- Formato imprimible y descargable
