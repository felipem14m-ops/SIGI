# QA Test Cases - Modelos SIGI
## Sistema de Gestión de Inventario - Tienda de Barrio

---

## 1. Database (`config/database.php`)

| ID | Método | Descripción | Resultado Esperado | Prioridad |
|----|--------|-------------|-------------------|-----------|
| TC-DB-001 | `conectar()` | Conexión exitosa | Retorna PDO sin excepción | Crítica |
| TC-DB-002 | `conectar()` | Credenciales inválidas | PDOException sin exponer credenciales | Crítica |
| TC-DB-003 | `conectar()` | Singleton | Misma instancia en 2 llamadas | Media |

### Checklist de Seguridad:
- ERRMODE_EXCEPTION activo
- EMULATE_PREPARES en false
- Charset utf8mb4
- Mensaje de error NO expone host/usuario/contraseña

---

## 2. Usuario (`models/Usuario.php`)

### CRUD

| ID | Método | Descripción | Resultado Esperado | Prioridad |
|----|--------|-------------|-------------------|-----------|
| TC-USR-010 | `registrar()` | Datos válidos | true, usuario en BD | Crítica |
| TC-USR-011 | `registrar()` | Email duplicado | PDOException | Crítica |
| TC-USR-013 | `registrar()` | Contraseña hasheada | Inicia con $2y$ | Crítica |
| TC-USR-020 | `obtenerPorCorreo()` | Email existente | Array con nombre_rol | Crítica |
| TC-USR-021 | `obtenerPorCorreo()` | Email inexistente | false | Crítica |
| TC-USR-030 | `existeCorreo()` | Email existente | true | Media |
| TC-USR-031 | `existeCorreo()` | Email nuevo | false | Media |
| TC-USR-050 | `obtenerPorId()` | ID válido | Array con datos | Media |
| TC-USR-060 | `listarTodos()` | Lista activos | Excluye activo=0 | Media |
| TC-USR-070 | `actualizar()` | Datos válidos | true | Media |
| TC-USR-080 | `cambiarContrasena()` | Nueva contraseña | Hash verificable | Crítica |
| TC-USR-090 | `cambiarEstado()` | Desactivar | activo=0 | Media |

### Anti-Fuerza Bruta

| ID | Método | Descripción | Resultado Esperado | Prioridad |
|----|--------|-------------|-------------------|-----------|
| TC-USR-110 | `registrarIntentoFallido()` | Incremento | intentos +1 | Crítica |
| TC-USR-111 | `registrarIntentoFallido()` | Bloqueo al 5to | bloqueado_hasta = NOW()+15min | Crítica |
| TC-USR-120 | `resetearIntentosFallidos()` | Limpieza | intentos=0, bloqueado=NULL | Crítica |
| TC-USR-130 | `verificarCredenciales()` | Login exitoso | Array usuario | Crítica |
| TC-USR-131 | `verificarCredenciales()` | Email falso | Exception genérica | Crítica |
| TC-USR-132 | `verificarCredenciales()` | Cuenta inactiva | Exception "desactivada" | Crítica |
| TC-USR-133 | `verificarCredenciales()` | Cuenta bloqueada | Exception con minutos | Crítica |
| TC-USR-135 | `verificarCredenciales()` | 5 intentos | Bloqueo 15 min | Crítica |

---

## 3. Producto (`models/Producto.php`)

| ID | Método | Descripción | Resultado Esperado | Prioridad |
|----|--------|-------------|-------------------|-----------|
| TC-PROD-010 | `crear()` | Datos completos | true | Crítica |
| TC-PROD-020 | `obtenerPorId()` | ID existente | Array con categoría y proveedor | Media |
| TC-PROD-030 | `listarTodos()` | Sin filtro | Todos los activos | Media |
| TC-PROD-031 | `listarTodos()` | Con filtro categoría | Solo esa categoría | Media |
| TC-PROD-050 | `desactivar()` | Soft delete | activo=0 | Media |
| TC-PROD-060 | `actualizarStock()` | Entrada +10 | stock += 10 | Crítica |
| TC-PROD-061 | `actualizarStock()` | Salida -5 | stock -= 5 | Crítica |
| TC-PROD-070 | `obtenerBajoStock()` | Alertas | stock <= stock_minimo | Crítica |
| TC-PROD-080 | `buscar()` | Parcial "arroz" | Productos con arroz | Media |

---

## 4. Categoría (`models/Categoria.php`)

| ID | Método | Descripción | Resultado Esperado | Prioridad |
|----|--------|-------------|-------------------|-----------|
| TC-CAT-010 | `crear()` | Datos válidos | true | Media |
| TC-CAT-020 | `obtenerPorId()` | ID válido | Array | Media |
| TC-CAT-030 | `listarTodas()` | Con conteo | total_productos incluido | Media |
| TC-CAT-040 | `actualizar()` | Datos válidos | true | Media |
| TC-CAT-050 | `desactivar()` | Soft delete | activo=0 | Media |

---

## 5. Proveedor (`models/Proveedor.php`)

| ID | Método | Descripción | Resultado Esperado | Prioridad |
|----|--------|-------------|-------------------|-----------|
| TC-PROV-010 | `crear()` | Datos válidos | true | Media |
| TC-PROV-020 | `obtenerPorId()` | ID válido | Array | Media |
| TC-PROV-030 | `listarTodos()` | Con conteo | total_productos incluido | Media |
| TC-PROV-040 | `actualizar()` | Datos válidos | true | Media |
| TC-PROV-050 | `desactivar()` | Soft delete | activo=0 | Media |

---

## 6. Venta (`models/Venta.php`)

| ID | Método | Descripción | Resultado Esperado | Prioridad |
|----|--------|-------------|-------------------|-----------|
| TC-VTA-010 | `registrar()` | 1 producto | ID venta > 0, stock descontado | Crítica |
| TC-VTA-011 | `registrar()` | N productos | Todos stocks descontados | Crítica |
| TC-VTA-012 | `registrar()` | Rollback | false, stock intacto | Crítica |
| TC-VTA-020 | `obtenerPorId()` | ID válido | Array con detalles | Media |
| TC-VTA-030 | `listarVentasHoy()` | Hoy | Solo DATE = CURDATE | Media |
| TC-VTA-040 | `resumenDiario()` | Totales | total_ventas, total_ingresos | Media |

---

## 7. Inventario (`models/Inventario.php`)

| ID | Método | Descripción | Resultado Esperado | Prioridad |
|----|--------|-------------|-------------------|-----------|
| TC-INV-010 | `registrarMovimiento()` | Entrada | stock += cantidad | Crítica |
| TC-INV-011 | `registrarMovimiento()` | Salida | stock -= cantidad | Crítica |
| TC-INV-012 | `registrarMovimiento()` | Ajuste | stock += cant (puede ser negativo) | Media |
| TC-INV-013 | `registrarMovimiento()` | Stock insuficiente | false, rollback | Crítica |
| TC-INV-020 | `listarMovimientos()` | General | Max N registros, orden DESC | Media |
| TC-INV-030 | `historialProducto()` | 1 producto | Solo ese producto | Media |
| TC-INV-040 | `resumenDiario()` | Hoy | Conteo por tipo | Baja |

---

## 8. Controladores - Casos de Prueba

### ProductoController

| ID | Acción | Descripción | Resultado Esperado | Prioridad |
|----|--------|-------------|-------------------|-----------|
| TC-CTRL-PROD-001 | Guard | Sin sesión admin | Redirige a login | Crítica |
| TC-CTRL-PROD-010 | `crear` | Datos válidos POST | Alert success + redirect | Crítica |
| TC-CTRL-PROD-011 | `crear` | Nombre vacío | Alert warning | Media |
| TC-CTRL-PROD-012 | `crear` | Método GET | Redirige sin acción | Media |
| TC-CTRL-PROD-020 | `editar` | Datos válidos | Alert success | Crítica |
| TC-CTRL-PROD-021 | `editar` | Sin id_producto | Alert error | Media |
| TC-CTRL-PROD-030 | `desactivar` | ID válido | Alert success, activo=0 | Crítica |

### CategoriaController

| ID | Acción | Descripción | Resultado Esperado | Prioridad |
|----|--------|-------------|-------------------|-----------|
| TC-CTRL-CAT-001 | Guard | Sin sesión admin | Redirige a login | Crítica |
| TC-CTRL-CAT-010 | `crear` | Nombre válido | Alert success | Media |
| TC-CTRL-CAT-011 | `crear` | Nombre vacío | Alert warning | Media |
| TC-CTRL-CAT-020 | `editar` | Datos válidos | Alert success | Media |
| TC-CTRL-CAT-030 | `desactivar` | ID válido | Alert success | Media |

### ProveedorController

| ID | Acción | Descripción | Resultado Esperado | Prioridad |
|----|--------|-------------|-------------------|-----------|
| TC-CTRL-PROV-001 | Guard | Sin sesión admin | Redirige a login | Crítica |
| TC-CTRL-PROV-010 | `crear` | Datos completos | Alert success | Media |
| TC-CTRL-PROV-020 | `editar` | Datos válidos | Alert success | Media |
| TC-CTRL-PROV-030 | `desactivar` | ID válido | Alert success | Media |

### InventarioController

| ID | Acción | Descripción | Resultado Esperado | Prioridad |
|----|--------|-------------|-------------------|-----------|
| TC-CTRL-INV-001 | Guard | Sin sesión admin | Redirige a login | Crítica |
| TC-CTRL-INV-010 | `registrar` | Entrada válida | Alert success, stock+= | Crítica |
| TC-CTRL-INV-011 | `registrar` | Salida válida | Alert success, stock-= | Crítica |
| TC-CTRL-INV-012 | `registrar` | Tipo inválido | Alert error | Media |
| TC-CTRL-INV-013 | `registrar` | Stock insuficiente | Alert error, rollback | Crítica |
| TC-CTRL-INV-014 | `registrar` | Campos vacíos | Alert warning | Media |

### VentaController

| ID | Acción | Descripción | Resultado Esperado | Prioridad |
|----|--------|-------------|-------------------|-----------|
| TC-CTRL-VTA-001 | Guard | Sin sesión | Redirige a login | Crítica |
| TC-CTRL-VTA-010 | `registrar` | Carrito válido | Alert success con ticket# | Crítica |
| TC-CTRL-VTA-011 | `registrar` | Carrito vacío | Alert warning | Media |
| TC-CTRL-VTA-012 | `registrar` | Stock insuficiente | Alert error, rollback | Crítica |
| TC-CTRL-VTA-013 | `registrar` | Total calculado | subtotal - descuento = total | Crítica |

---

## Resumen Total: 90 Casos de Prueba

| Componente | Casos | Críticos |
|------------|-------|----------|
| Modelos | 62 | 28 |
| Controladores | 28 | 14 |
| **Total** | **90** | **42** |

**Orden de ejecución:** Database → Modelos → Controladores → Vistas
