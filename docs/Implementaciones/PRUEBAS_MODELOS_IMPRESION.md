# ✅ Checklist de Pruebas - Sistema Multi-Modelo de Impresión

**Fecha**: 10 de febrero de 2026  
**Branch**: impr_presupuesto  
**Objetivo**: Verificar funcionamiento del sistema de modelos de impresión dinámicos

---

## 📋 PRE-REQUISITOS

### ✅ Verificar Migración SQL Ejecutada

```sql
-- Verificar que el campo existe
USE toldos_db;
DESCRIBE empresa;
-- Debe aparecer: modelo_impresion_empresa | varchar(50) | DEFAULT 'impresionpresupuesto_m1_es.php'

-- Ver modelos configurados
SELECT id_empresa, nombre_empresa, modelo_impresion_empresa 
FROM empresa 
WHERE activo_empresa = 1;
```

**Resultado esperado**: 
- Campo `modelo_impresion_empresa` existe en tabla `empresa`
- Todas las empresas tienen valor `impresionpresupuesto_m1_es.php`

---

## 🧪 PRUEBA 1: Verificar Datos en Botón

### Pasos:
1. Abrir navegador: `http://localhost/MDR/view/Presupuesto/`
2. Abrir DevTools (F12) → Pestaña "Console"
3. Inspeccionar cualquier botón de impresión (click derecho → Inspeccionar)

### Verificar:
```html
<button ... class="imprimirPresupuesto" 
        data-id_presupuesto="11"
        data-id_empresa="1">
```

✅ **RESULTADO ESPERADO**: El botón tiene ambos atributos `data-id_presupuesto` y `data-id_empresa`

❌ **SI FALLA**: Refrescar página con Ctrl+F5 (limpiar caché)

---

## 🧪 PRUEBA 2: Modal de Impresión

### Pasos:
1. En la consola del navegador, escribir:
   ```javascript
   $('#impresion_id_empresa').length
   ```
2. Presionar Enter

### Verificar:
✅ **RESULTADO ESPERADO**: Retorna `1` (el campo existe)

❌ **SI FALLA**: El campo oculto no se añadió al HTML, revisar `view/Presupuesto/index.php`

---

## 🧪 PRUEBA 3: Click en Imprimir - Console Logs

### Pasos:
1. Mantener DevTools abierto en pestaña "Console"
2. Click en botón "Imprimir" de cualquier presupuesto
3. Observar mensajes en consola

### Verificar:
```javascript
Abriendo modal de impresión para presupuesto: 11 Empresa: 1
```

✅ **RESULTADO ESPERADO**: Se muestran ambos IDs (presupuesto y empresa)

---

## 🧪 PRUEBA 4: AJAX Request - Network Tab

### Pasos:
1. DevTools → Pestaña "Network"
2. Marcar checkbox "Preserve log"
3. En modal, seleccionar "Cliente Final" y "Español"
4. Click en botón "Imprimir" del modal
5. Buscar request a `presupuesto.php?op=obtener_modelo_impresion`

### Verificar Request:
- **URL**: `presupuesto.php?op=obtener_modelo_impresion`
- **Method**: POST
- **Form Data**: `id_empresa: 1`

### Verificar Response:
```json
{
    "success": true,
    "modelo": "impresionpresupuesto_m1_es.php"
}
```

✅ **RESULTADO ESPERADO**: Response con success=true y modelo correcto

---

## 🧪 PRUEBA 5: Impresión Exitosa con Modelo 1

### Pasos:
1. Observar en consola después del AJAX:
   ```javascript
   Usando modelo de impresión: impresionpresupuesto_m1_es.php
   ```
2. Se abre nueva pestaña/ventana con el presupuesto
3. Verificar que el presupuesto se muestra correctamente

### Verificar en nueva ventana:
- ✅ Logo de empresa
- ✅ Datos del cliente
- ✅ Líneas del presupuesto
- ✅ Totales calculados

---

## 🧪 PRUEBA 6: Logs del Servidor (Modelo 1)

### Pasos:
```powershell
# Abrir archivo de log de hoy
Get-Content "w:\MDR\public\logs\$(Get-Date -Format 'yyyy-MM-dd').json" -Tail 20 | ConvertFrom-Json | Format-Table -AutoSize
```

### Verificar:
Buscar entradas con:
- **Pantalla**: `impresionpresupuesto_m1_es.php`
- **Mensaje**: Debe contener `[MODELO 1]`

Ejemplo:
```json
{
    "usuario": "admin",
    "pantalla": "impresionpresupuesto_m1_es.php",
    "actividad": "cli_esp",
    "mensaje": "[MODELO 1] Ruta logo desde BD: ../public/img/logos/logo_empresa.png",
    "tipo": "info"
}
```

✅ **RESULTADO ESPERADO**: Logs identifican claramente MODELO 1

---

## 🧪 PRUEBA 7: Cambiar a Modelo 2

### Pasos:
```sql
-- Cambiar empresa ID 1 a modelo 2
UPDATE empresa 
SET modelo_impresion_empresa = 'impresionpresupuesto_m2_es.php' 
WHERE id_empresa = 1;

-- Verificar cambio
SELECT id_empresa, nombre_empresa, modelo_impresion_empresa 
FROM empresa 
WHERE id_empresa = 1;
```

### Verificar:
✅ **RESULTADO ESPERADO**: Campo actualizado a `impresionpresupuesto_m2_es.php`

---

## 🧪 PRUEBA 8: Impresión con Modelo 2

### Pasos:
1. Volver al navegador
2. Refrescar la página de presupuestos (F5)
3. Click en "Imprimir" del mismo presupuesto
4. Observar consola:
   ```javascript
   Usando modelo de impresión: impresionpresupuesto_m2_es.php
   ```

### Verificar:
- ✅ AJAX retorna modelo 2
- ✅ Se abre nueva ventana con impresión
- ✅ Presupuesto se muestra (idéntico a modelo 1 por ahora)

---

## 🧪 PRUEBA 9: Logs del Servidor (Modelo 2)

### Pasos:
```powershell
Get-Content "w:\MDR\public\logs\$(Get-Date -Format 'yyyy-MM-dd').json" -Tail 20 | ConvertFrom-Json | Where-Object { $_.pantalla -like "*m2*" } | Format-Table -AutoSize
```

### Verificar:
```json
{
    "pantalla": "impresionpresupuesto_m2_es.php",
    "mensaje": "[MODELO 2] Ruta logo desde BD: ...",
    "tipo": "info"
}
```

✅ **RESULTADO ESPERADO**: Logs identifican claramente MODELO 2

---

## 🧪 PRUEBA 10: Restaurar Modelo 1

### Pasos:
```sql
-- Volver a modelo 1
UPDATE empresa 
SET modelo_impresion_empresa = 'impresionpresupuesto_m1_es.php' 
WHERE id_empresa = 1;
```

---

## 🧪 PRUEBA 11: Manejo de Errores

### Test A: ID Empresa Vacío

```javascript
// En consola del navegador:
$.post('../../controller/presupuesto.php?op=obtener_modelo_impresion', {}, function(r){ console.log(r); });
```

✅ **ESPERADO**: `{success: false, message: "ID de empresa no proporcionado"}`

### Test B: Empresa Sin Campo Configurado

```sql
-- Poner campo en NULL
UPDATE empresa SET modelo_impresion_empresa = NULL WHERE id_empresa = 1;
```

Imprimir presupuesto → Debe usar modelo 1 por defecto

✅ **ESPERADO**: AJAX retorna `impresionpresupuesto_m1_es.php` (fallback)

```sql
-- Restaurar
UPDATE empresa SET modelo_impresion_empresa = 'impresionpresupuesto_m1_es.php' WHERE id_empresa = 1;
```

---

## 🧪 PRUEBA 12: Múltiples Empresas

### Si tienes más de una empresa:

```sql
-- Configurar diferentes modelos
UPDATE empresa SET modelo_impresion_empresa = 'impresionpresupuesto_m1_es.php' WHERE id_empresa = 1;
UPDATE empresa SET modelo_impresion_empresa = 'impresionpresupuesto_m2_es.php' WHERE id_empresa = 2;

-- Verificar
SELECT id_empresa, nombre_empresa, modelo_impresion_empresa FROM empresa;
```

Imprimir presupuestos de cada empresa y verificar que usa su modelo correspondiente.

---

## 📊 RESUMEN DE RESULTADOS

| # | Prueba | Estado | Notas |
|---|--------|--------|-------|
| 1 | Datos en botón | ⬜ | data-id_presupuesto y data-id_empresa |
| 2 | Campo oculto modal | ⬜ | #impresion_id_empresa existe |
| 3 | Console log modal | ⬜ | Muestra ambos IDs |
| 4 | AJAX request | ⬜ | Llama correctamente al endpoint |
| 5 | Impresión M1 | ⬜ | Presupuesto se genera correctamente |
| 6 | Logs M1 | ⬜ | Identifican [MODELO 1] |
| 7 | Cambio a M2 | ⬜ | UPDATE exitoso |
| 8 | Impresión M2 | ⬜ | Presupuesto se genera correctamente |
| 9 | Logs M2 | ⬜ | Identifican [MODELO 2] |
| 10 | Restaurar M1 | ⬜ | UPDATE exitoso |
| 11 | Errores | ⬜ | Manejo correcto de casos edge |
| 12 | Multi-empresa | ⬜ | Cada empresa usa su modelo |

Leyenda: ⬜ Pendiente | ✅ Éxito | ❌ Fallo

---

## 🐛 TROUBLESHOOTING

### Problema: "No se encontraron datos del presupuesto"
**Solución**: Verificar que el presupuesto existe y tiene id_empresa asignado:
```sql
SELECT id_presupuesto, numero_presupuesto, id_empresa 
FROM presupuesto 
WHERE id_presupuesto = 11;
```

### Problema: AJAX no retorna nada
**Solución**: Verificar endpoint en DevTools → Network → Response
```powershell
# Ver errores PHP
Get-Content "w:\MDR\public\logs\$(Get-Date -Format 'yyyy-MM-dd').json" -Tail 50 | ConvertFrom-Json | Where-Object { $_.tipo -eq 'error' }
```

### Problema: "modelo_impresion_empresa doesn't exist"
**Solución**: Ejecutar migración SQL:
```bash
mysql -u administrator -p toldos_db < BD/migrations/alter_empresa_modelo_impresion.sql
```

### Problema: Caché del navegador
**Solución**: Hard refresh
- Chrome/Edge: `Ctrl + Shift + R` o `Ctrl + F5`
- Firefox: `Ctrl + Shift + R` o `Ctrl + F5`

### Problema: JavaScript no se actualiza
**Solución**: 
1. Abrir DevTools → Sources
2. Localizar `mntpresupuesto.js`
3. Verificar que contiene los cambios (buscar "id_empresa")
4. Si no, hacer Ctrl+F5 para forzar recarga

---

## ✅ CRITERIO DE ÉXITO

**Sistema funcional si**:
- ✅ Botón de impresión tiene data-id_empresa
- ✅ Modal guarda id_empresa en campo oculto
- ✅ AJAX obtiene modelo de empresa correctamente
- ✅ Impresión se abre con modelo correcto
- ✅ Logs identifican qué modelo se usó
- ✅ Cambio de modelo en BD se refleja inmediatamente
- ✅ Fallback a modelo 1 funciona ante errores

---

## 📝 REPORTE DE ERRORES

Si encuentras errores, documenta:
1. **Qué prueba**: Número y nombre
2. **Qué esperabas**: Comportamiento esperado
3. **Qué obtuviste**: Comportamiento real
4. **Logs**: Captura del log de servidor o consola
5. **Screenshot**: Si es visual, captura de pantalla

Ejemplo:
```
PRUEBA 4 - AJAX Request
- Esperado: {success: true, modelo: "impresionpresupuesto_m1_es.php"}
- Obtenido: {success: false, message: "ID de empresa no proporcionado"}
- Logs: [Ver archivo adjunto]
```

---

**Última actualización**: 10 de febrero de 2026  
**Autor**: Luis - Innovabyte  
**Branch**: impr_presupuesto
