# 🔧 Solución Rápida - Error en Módulo de Ventas

## ❌ Problema
Error al registrar ventas: `SQLSTATE[23000]: Integrity constraint violation: 1452`

## ✅ Solución en 3 Pasos

### Paso 1: Ejecutar Script SQL
Abrir phpMyAdmin o MySQL Workbench y ejecutar el archivo:
```
SQL/datos_iniciales_tipos_movimiento.sql
```

O desde terminal:
```bash
mysql -u tu_usuario -p tu_base_datos < SQL/datos_iniciales_tipos_movimiento.sql
```

### Paso 2: Verificar Configuración
Ejecutar desde el navegador:
```
http://localhost/tu-proyecto/scratch/diagnostico_sistema.php
```

Debe mostrar todos los checks en verde ✓

### Paso 3: Probar Ventas
1. Ir al módulo de ventas
2. Agregar productos al carrito
3. Procesar una venta
4. ¡Debe funcionar correctamente!

## 📋 ¿Qué se Solucionó?

1. ✅ Se agregaron los tipos de movimiento necesarios a la base de datos
2. ✅ Se mejoró el modelo de Venta para registrar movimientos de inventario automáticamente
3. ✅ Se agregó validación de stock antes de procesar ventas
4. ✅ Se implementó transaccionalidad completa (rollback si algo falla)

## 📚 Documentación Completa

Para más detalles, ver: `Documentacion/SOLUCION_ERROR_VENTAS.md`

## 🆘 Si el Problema Persiste

1. Verificar que el script SQL se ejecutó correctamente
2. Ejecutar el diagnóstico: `scratch/diagnostico_sistema.php`
3. Revisar los logs de error de PHP
4. Verificar permisos de usuario en la base de datos

---
**Sistema:** SIGI v2.0.0  
**Fecha:** 2026-05-10
