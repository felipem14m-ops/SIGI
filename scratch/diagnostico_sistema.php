<?php
/**
 * ============================================================================
 * Script de Diagnóstico del Sistema
 * Sistema: SIGI - Gestión de Inventario
 * 
 * Verifica la configuración de la base de datos y detecta problemas comunes
 * 
 * INSTRUCCIONES:
 * 1. Acceder desde el navegador: http://localhost/tu-proyecto/scratch/diagnostico_sistema.php
 * 2. Revisar los resultados
 * 3. Aplicar las soluciones sugeridas si hay errores
 * 
 * @version 1.0.0
 * @since   2026-05-10
 */

require_once __DIR__ . '/../config/database.php';

// Configuración de salida HTML
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diagnóstico del Sistema - SIGI</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
            min-height: 100vh;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 { font-size: 2em; margin-bottom: 10px; }
        .header p { opacity: 0.9; }
        .content { padding: 30px; }
        .test-section {
            margin-bottom: 30px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            overflow: hidden;
        }
        .test-header {
            background: #f5f5f5;
            padding: 15px 20px;
            font-weight: bold;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .test-body { padding: 20px; }
        .status {
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.9em;
            font-weight: bold;
        }
        .status.ok { background: #4caf50; color: white; }
        .status.warning { background: #ff9800; color: white; }
        .status.error { background: #f44336; color: white; }
        .status.info { background: #2196f3; color: white; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
        }
        th {
            background: #f5f5f5;
            font-weight: bold;
        }
        .solution {
            background: #fff3cd;
            border-left: 4px solid #ff9800;
            padding: 15px;
            margin-top: 15px;
            border-radius: 4px;
        }
        .solution h4 {
            color: #856404;
            margin-bottom: 10px;
        }
        .code {
            background: #f5f5f5;
            padding: 10px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            font-size: 0.9em;
            margin-top: 10px;
            overflow-x: auto;
        }
        .footer {
            text-align: center;
            padding: 20px;
            color: #666;
            border-top: 1px solid #e0e0e0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔍 Diagnóstico del Sistema</h1>
            <p>SIGI - Sistema Integral de Gestión de Inventario</p>
        </div>
        
        <div class="content">
            <?php
            try {
                $db = (new Database())->conectar();
                
                // TEST 1: Conexión a la base de datos
                echo '<div class="test-section">';
                echo '<div class="test-header">';
                echo '<span>1. Conexión a Base de Datos</span>';
                echo '<span class="status ok">✓ OK</span>';
                echo '</div>';
                echo '<div class="test-body">';
                echo '<p>Conexión establecida correctamente.</p>';
                echo '</div>';
                echo '</div>';
                
                // TEST 2: Tipos de Movimiento
                echo '<div class="test-section">';
                echo '<div class="test-header">';
                echo '<span>2. Tipos de Movimiento</span>';
                
                $stmt = $db->query("SELECT COUNT(*) as total FROM tipos_movimiento");
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $totalTipos = $result['total'];
                
                if ($totalTipos >= 4) {
                    echo '<span class="status ok">✓ OK</span>';
                } else {
                    echo '<span class="status error">✗ ERROR</span>';
                }
                echo '</div>';
                echo '<div class="test-body">';
                
                if ($totalTipos >= 4) {
                    echo "<p>Se encontraron <strong>{$totalTipos}</strong> tipos de movimiento configurados.</p>";
                    
                    $stmt = $db->query("SELECT * FROM tipos_movimiento ORDER BY id_tipo");
                    $tipos = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    echo '<table>';
                    echo '<tr><th>ID</th><th>Código</th><th>Nombre</th><th>Signo</th></tr>';
                    foreach ($tipos as $tipo) {
                        echo "<tr>";
                        echo "<td>{$tipo['id_tipo']}</td>";
                        echo "<td>{$tipo['codigo']}</td>";
                        echo "<td>{$tipo['nombre']}</td>";
                        echo "<td>{$tipo['signo']}</td>";
                        echo "</tr>";
                    }
                    echo '</table>';
                } else {
                    echo "<p>⚠️ Solo se encontraron <strong>{$totalTipos}</strong> tipos de movimiento.</p>";
                    echo '<div class="solution">';
                    echo '<h4>Solución:</h4>';
                    echo '<p>Ejecutar el script de datos iniciales:</p>';
                    echo '<div class="code">mysql -u usuario -p nombre_bd &lt; SQL/datos_iniciales_tipos_movimiento.sql</div>';
                    echo '</div>';
                }
                
                echo '</div>';
                echo '</div>';
                
                // TEST 3: Tipo SALIDA_VENTA
                echo '<div class="test-section">';
                echo '<div class="test-header">';
                echo '<span>3. Tipo SALIDA_VENTA (Requerido para Ventas)</span>';
                
                $stmt = $db->query("SELECT * FROM tipos_movimiento WHERE id_tipo = 4 OR codigo = 'SALIDA_VENTA'");
                $tipoVenta = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($tipoVenta) {
                    echo '<span class="status ok">✓ OK</span>';
                } else {
                    echo '<span class="status error">✗ ERROR</span>';
                }
                echo '</div>';
                echo '<div class="test-body">';
                
                if ($tipoVenta) {
                    echo '<p>El tipo de movimiento para ventas está configurado correctamente:</p>';
                    echo '<table>';
                    echo '<tr><th>ID</th><th>Código</th><th>Nombre</th><th>Signo</th></tr>';
                    echo "<tr>";
                    echo "<td>{$tipoVenta['id_tipo']}</td>";
                    echo "<td>{$tipoVenta['codigo']}</td>";
                    echo "<td>{$tipoVenta['nombre']}</td>";
                    echo "<td>{$tipoVenta['signo']}</td>";
                    echo "</tr>";
                    echo '</table>';
                } else {
                    echo '<p>⚠️ No se encontró el tipo de movimiento SALIDA_VENTA.</p>';
                    echo '<p><strong>Este es el problema que causa el error en las ventas.</strong></p>';
                    echo '<div class="solution">';
                    echo '<h4>Solución:</h4>';
                    echo '<p>Ejecutar el script de datos iniciales:</p>';
                    echo '<div class="code">mysql -u usuario -p nombre_bd &lt; SQL/datos_iniciales_tipos_movimiento.sql</div>';
                    echo '</div>';
                }
                
                echo '</div>';
                echo '</div>';
                
                // TEST 4: Métodos de Pago
                echo '<div class="test-section">';
                echo '<div class="test-header">';
                echo '<span>4. Métodos de Pago</span>';
                
                $stmt = $db->query("SELECT COUNT(*) as total FROM metodos_pago WHERE activo = 1");
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $totalMetodos = $result['total'];
                
                if ($totalMetodos > 0) {
                    echo '<span class="status ok">✓ OK</span>';
                } else {
                    echo '<span class="status warning">⚠ ADVERTENCIA</span>';
                }
                echo '</div>';
                echo '<div class="test-body">';
                
                if ($totalMetodos > 0) {
                    echo "<p>Se encontraron <strong>{$totalMetodos}</strong> métodos de pago activos.</p>";
                    
                    $stmt = $db->query("SELECT * FROM metodos_pago WHERE activo = 1 ORDER BY nombre");
                    $metodos = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    echo '<table>';
                    echo '<tr><th>ID</th><th>Nombre</th><th>Estado</th></tr>';
                    foreach ($metodos as $metodo) {
                        echo "<tr>";
                        echo "<td>{$metodo['id_metodo']}</td>";
                        echo "<td>{$metodo['nombre']}</td>";
                        echo "<td>Activo</td>";
                        echo "</tr>";
                    }
                    echo '</table>';
                } else {
                    echo '<p>⚠️ No hay métodos de pago configurados.</p>';
                    echo '<div class="solution">';
                    echo '<h4>Recomendación:</h4>';
                    echo '<p>Agregar al menos un método de pago:</p>';
                    echo '<div class="code">INSERT INTO metodos_pago (nombre, activo) VALUES (\'Efectivo\', 1);</div>';
                    echo '</div>';
                }
                
                echo '</div>';
                echo '</div>';
                
                // TEST 5: Movimientos de Inventario
                echo '<div class="test-section">';
                echo '<div class="test-header">';
                echo '<span>5. Movimientos de Inventario</span>';
                
                $stmt = $db->query("SELECT COUNT(*) as total FROM movimiento_inventario");
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $totalMovimientos = $result['total'];
                
                echo '<span class="status info">ℹ INFO</span>';
                echo '</div>';
                echo '<div class="test-body">';
                
                echo "<p>Total de movimientos registrados: <strong>{$totalMovimientos}</strong></p>";
                
                if ($totalMovimientos > 0) {
                    $stmt = $db->query("
                        SELECT 
                            COUNT(*) as total,
                            SUM(CASE WHEN origen = 'automatico' THEN 1 ELSE 0 END) as automaticos,
                            SUM(CASE WHEN origen = 'manual' THEN 1 ELSE 0 END) as manuales
                        FROM movimiento_inventario
                    ");
                    $stats = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    echo '<table>';
                    echo '<tr><th>Tipo</th><th>Cantidad</th></tr>';
                    echo "<tr><td>Automáticos (ventas)</td><td>{$stats['automaticos']}</td></tr>";
                    echo "<tr><td>Manuales</td><td>{$stats['manuales']}</td></tr>";
                    echo "<tr><td><strong>Total</strong></td><td><strong>{$stats['total']}</strong></td></tr>";
                    echo '</table>';
                }
                
                echo '</div>';
                echo '</div>';
                
                // TEST 6: Resumen General
                echo '<div class="test-section">';
                echo '<div class="test-header">';
                echo '<span>6. Resumen General del Sistema</span>';
                echo '<span class="status info">ℹ INFO</span>';
                echo '</div>';
                echo '<div class="test-body">';
                
                $stmt = $db->query("
                    SELECT 
                        (SELECT COUNT(*) FROM categorias WHERE activa = 1) as categorias,
                        (SELECT COUNT(*) FROM productos WHERE estado = 'activo') as productos,
                        (SELECT COUNT(*) FROM usuarios WHERE activo = 1) as usuarios,
                        (SELECT COUNT(*) FROM venta) as ventas
                ");
                $resumen = $stmt->fetch(PDO::FETCH_ASSOC);
                
                echo '<table>';
                echo '<tr><th>Elemento</th><th>Cantidad</th></tr>';
                echo "<tr><td>Categorías activas</td><td>{$resumen['categorias']}</td></tr>";
                echo "<tr><td>Productos activos</td><td>{$resumen['productos']}</td></tr>";
                echo "<tr><td>Usuarios activos</td><td>{$resumen['usuarios']}</td></tr>";
                echo "<tr><td>Ventas registradas</td><td>{$resumen['ventas']}</td></tr>";
                echo '</table>';
                
                echo '</div>';
                echo '</div>';
                
            } catch (Exception $e) {
                echo '<div class="test-section">';
                echo '<div class="test-header">';
                echo '<span>Error de Conexión</span>';
                echo '<span class="status error">✗ ERROR</span>';
                echo '</div>';
                echo '<div class="test-body">';
                echo '<p>No se pudo conectar a la base de datos:</p>';
                echo '<div class="code">' . htmlspecialchars($e->getMessage()) . '</div>';
                echo '</div>';
                echo '</div>';
            }
            ?>
        </div>
        
        <div class="footer">
            <p>Diagnóstico generado el <?php echo date('d/m/Y H:i:s'); ?></p>
            <p>SIGI v2.0.0 - Sistema Integral de Gestión de Inventario</p>
        </div>
    </div>
</body>
</html>
