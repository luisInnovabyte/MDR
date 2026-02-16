# 🧭 Guía Paso a Paso - Comprobación Sistema de Peso

## ✅ YA COMPLETADO
- [x] Elementos guardan peso correctamente
- [x] Elementos muestran peso en subtabla
- [x] Vista elementos incluye peso

---

## 📍 PASO 1: Verificar Peso Medio de Artículo

### 🎯 Objetivo
Comprobar que cuando varios elementos del mismo artículo tienen peso, se calcula el promedio correctamente.

### ✏️ Acciones:
1. **Ir a Mantenimiento → Artículos**
2. **Seleccionar un artículo** que tenga elementos (ejemplo: "Toldo 3x3")
3. **Crear o editar 3 elementos** de ese artículo con estos pesos:
   - Elemento 1: `10.000` kg
   - Elemento 2: `12.000` kg
   - Elemento 3: `14.000` kg

### 🔍 Verificación en BD:
Abre HeidiSQL/phpMyAdmin y ejecuta:

```sql
-- Paso 1.1: Ver los elementos con peso que acabas de crear
SELECT 
    codigo_elemento,
    descripcion_elemento,
    peso_elemento
FROM elemento
WHERE id_articulo_elemento = [REEMPLAZA_CON_ID_ARTICULO]
  AND activo_elemento = 1
ORDER BY codigo_elemento;
```

**✅ Resultado esperado:**
```
codigo_elemento | descripcion_elemento | peso_elemento
TOLDO-001      | Elemento 1          | 10.000
TOLDO-002      | Elemento 2          | 12.000
TOLDO-003      | Elemento 3          | 14.000
```

### 🧮 Verificación de Cálculo:
```sql
-- Paso 1.2: Ver el peso medio calculado por la vista
SELECT 
    id_articulo,
    nombre_articulo,
    total_elementos,
    elementos_con_peso,
    peso_medio_kg,
    peso_total_kg
FROM vista_articulo_peso
WHERE id_articulo = [REEMPLAZA_CON_ID_ARTICULO];
```

**✅ Resultado esperado:**
```
total_elementos: 3
elementos_con_peso: 3
peso_medio_kg: 12.000  (porque (10+12+14)/3 = 12)
peso_total_kg: 36.000
```

### ⚠️ Si no coincide:
- Verificar que los 3 elementos tienen `activo_elemento = 1`
- Verificar que ninguno tiene `peso_elemento IS NULL`
- Ejecutar: `SELECT * FROM vista_articulo_peso WHERE id_articulo = [ID];` para ver qué está devolviendo

---

## 📍 PASO 2: Crear Presupuesto con Peso

### 🎯 Objetivo
Crear un presupuesto con líneas que tengan peso y verificar que se calcula correctamente.

### ✏️ Acciones:
1. **Ir a Presupuestos → Nuevo Presupuesto**
2. **Rellenar datos básicos:**
   - Cliente: cualquiera
   - Fecha evento: cualquiera
   - Estado: "En elaboración"
3. **Añadir línea:**
   - Artículo: El que usaste en PASO 1 (con peso medio 12 kg)
   - Cantidad: `3` unidades
   - Días: `1`
4. **Guardar presupuesto**
5. **Anotar el ID del presupuesto** (aparece en la URL o en el número de presupuesto)

### 🔍 Verificación en BD:
```sql
-- Paso 2.1: Ver el presupuesto que acabas de crear
SELECT 
    id_presupuesto,
    numero_presupuesto,
    nombre_evento_presupuesto,
    fecha_presupuesto
FROM presupuesto
ORDER BY id_presupuesto DESC
LIMIT 1;
```

Anota el `id_presupuesto` (ejemplo: 125)

```sql
-- Paso 2.2: Ver las líneas de ese presupuesto
SELECT 
    id_linea_ppto,
    nombre_articulo,
    cantidad_linea,
    precio_unidad_linea
FROM linea_presupuesto
WHERE id_presupuesto = [TU_ID_PRESUPUESTO]
  AND activo_linea_ppto = 1;
```

**✅ Deberías ver tu línea con cantidad = 3**

---

## 📍 PASO 3: Verificar Cálculo de Peso en Línea

### 🎯 Objetivo
Comprobar que el peso de la línea = peso_unitario × cantidad

### 🔍 Verificación:
```sql
-- Paso 3.1: Ver peso calculado de la línea
SELECT 
    id_linea_ppto,
    numero_presupuesto,
    nombre_articulo,
    cantidad_linea,
    peso_unitario_kg,
    peso_total_linea_kg
FROM vista_linea_peso
WHERE id_presupuesto = [TU_ID_PRESUPUESTO]
ORDER BY id_linea_ppto;
```

**✅ Resultado esperado:**
```
cantidad_linea: 3
peso_unitario_kg: 12.000  (del artículo)
peso_total_linea_kg: 36.000  (porque 12 × 3 = 36)
```

### ⚠️ Si peso_unitario_kg es NULL:
- El artículo no tiene elementos con peso
- Volver al PASO 1 y asegurarse que los elementos tienen peso

### ⚠️ Si peso_total_linea_kg no coincide:
- Verificar la multiplicación: peso_unitario × cantidad
- Revisar que `activo_linea_ppto = 1`

---

## 📍 PASO 4: Verificar Peso Total de Versión

### 🎯 Objetivo
Comprobar que el peso total de la versión suma todas las líneas

### ✏️ Acciones (opcional):
Si quieres probar con múltiples líneas, añade otra línea al mismo presupuesto con otro artículo que tenga peso.

### 🔍 Verificación:
```sql
-- Paso 4.1: Ver la versión del presupuesto
SELECT 
    id_version_presupuesto,
    numero_version
FROM version_presupuesto
WHERE id_presupuesto = [TU_ID_PRESUPUESTO]
  AND activo_version_presupuesto = 1
ORDER BY numero_version DESC
LIMIT 1;
```

Anota el `id_version_presupuesto` (ejemplo: 200)

```sql
-- Paso 4.2: Ver peso total de esa versión
SELECT 
    id_version_presupuesto,
    numero_presupuesto,
    total_lineas,
    lineas_con_peso,
    peso_total_version_kg
FROM vista_version_presupuesto_peso
WHERE id_version_presupuesto = [TU_ID_VERSION];
```

**✅ Resultado esperado:**
```
total_lineas: 1 (o más si añadiste líneas)
lineas_con_peso: 1
peso_total_version_kg: 36.000 (o suma de todas las líneas)
```

### 🧮 Cálculo manual:
- Si solo tienes 1 línea: peso_total = 36.000
- Si añadiste otra línea (ej: 10 kg × 2 = 20 kg): peso_total = 36 + 20 = 56.000

---

## 📍 PASO 5: Verificar Peso Total del Presupuesto

### 🎯 Objetivo
Comprobar que suma todas las versiones (aunque normalmente solo hay 1 versión activa)

### 🔍 Verificación:
```sql
-- Paso 5.1: Ver peso total del presupuesto completo
SELECT 
    id_presupuesto,
    numero_presupuesto,
    nombre_evento_presupuesto,
    total_versiones,
    versiones_con_peso,
    peso_total_presupuesto_kg
FROM vista_presupuesto_peso
WHERE id_presupuesto = [TU_ID_PRESUPUESTO];
```

**✅ Resultado esperado:**
```
total_versiones: 1
versiones_con_peso: 1
peso_total_presupuesto_kg: 36.000 (mismo que versión si solo hay 1)
```

---

## 📍 PASO 6: Generar PDF CON Peso

### 🎯 Objetivo
Verificar que el PDF muestra la sección de peso cuando hay peso calculado

### ✏️ Acciones:
1. **Ir al presupuesto** que creaste en PASO 2
2. **Hacer clic en botón "Imprimir/PDF"** o similar
3. **Generar PDF**
4. **Abrir el PDF generado**

### 🔍 Verificación visual:
Busca al **final del documento, después de "TOTAL €"**:

**✅ Deberías ver una sección así:**

```
═══════════════════════════════════════════
        PESO TOTAL DEL EVENTO
               36.000 kg
═══════════════════════════════════════════
```

### ⚠️ Si NO aparece la sección:
1. Verificar que `peso_total_version_kg > 0` en la BD (PASO 4)
2. Abrir archivo: `controller/impresionpresupuesto_m2_pdf_es.php`
3. Buscar la línea con query de peso
4. Verificar que la variable `$peso_total_kg` tiene valor
5. Verificar condicional `if ($peso_total_kg > 0)`

### 🐛 Debug del PDF:
Si no funciona, añade esto temporalmente en el archivo PHP (línea ~500):

```php
// DEBUG: Ver valor de peso
error_log("DEBUG PESO: " . print_r($peso_total_kg, true));
```

Luego genera PDF y revisa logs en `public/logs/`

---

## 📍 PASO 7: Generar PDF SIN Peso

### 🎯 Objetivo
Verificar que el PDF NO muestra la sección cuando no hay peso

### ✏️ Acciones:
1. **Crear un NUEVO presupuesto**
2. **Añadir línea con artículo SIN peso** (artículo que no tenga elementos con peso)
3. **Guardar presupuesto**
4. **Generar PDF**

### 🔍 Verificación:
```sql
-- Paso 7.1: Verificar que el artículo NO tiene peso
SELECT 
    id_articulo,
    nombre_articulo,
    peso_medio_kg
FROM vista_articulo_peso
WHERE id_articulo = [ID_ARTICULO_SIN_PESO];
```

**✅ Resultado esperado:**
```
peso_medio_kg: NULL
```

### 🔍 Verificación en PDF:
**✅ La sección "PESO TOTAL DEL EVENTO" NO debe aparecer**

Si aparece con "0.000 kg" o algo similar, hay un bug en el condicional del PDF.

---

## 📍 PASO 8: Test de Valor Cero

### 🎯 Objetivo
Diferenciar entre peso NULL (sin datos) y peso 0 (peso real que es cero)

### ✏️ Acciones:
1. **Editar un elemento**
2. **Poner peso = `0` (cero)**
3. **Guardar**

### 🔍 Verificación:
```sql
-- Ver el elemento con peso 0
SELECT 
    codigo_elemento,
    peso_elemento
FROM elemento
WHERE id_elemento = [ID_ELEMENTO_CON_CERO]
  AND activo_elemento = 1;
```

**✅ Resultado esperado:**
```
peso_elemento: 0.000  (NO NULL)
```

### 🔍 Verificación visual:
1. **Desplegar la subtabla** del elemento
2. **Debe mostrar:** "0.000 kg" (NO "--")

### 🧮 Verificación en cálculo:
```sql
-- El elemento con peso 0 SÍ debe contarse en el promedio
SELECT 
    elementos_con_peso,
    peso_medio_kg
FROM vista_articulo_peso
WHERE id_articulo = [ID_ARTICULO];
```

Si hay 3 elementos con peso 10, 12, 0:
- elementos_con_peso = 3 (incluye el 0)
- peso_medio_kg = 7.333 (porque (10+12+0)/3)

---

## 📍 PASO 9: Test de Precisión Decimal

### 🎯 Objetivo
Verificar que los decimales se mantienen correctamente

### ✏️ Acciones:
1. **Crear elemento con peso: `10.999`**
2. **Crear presupuesto con ese artículo, cantidad: `3`**

### 🔍 Verificación:
```sql
-- Debe multiplicar correctamente
SELECT 
    cantidad_linea,
    peso_unitario_kg,
    peso_total_linea_kg
FROM vista_linea_peso
WHERE id_linea_ppto = [ID_LINEA];
```

**✅ Resultado esperado:**
```
cantidad_linea: 3
peso_unitario_kg: 10.999
peso_total_linea_kg: 32.997  (NO 33.0, NO 32.99)
```

### 🔍 Verificación en PDF:
Debe mostrar: **"32.997 kg"** con 3 decimales

---

## 📍 PASO 10: Test con Kit (Opcional)

### 🎯 Objetivo
Si tienes kits configurados, verificar que calculan peso correctamente

### ⚠️ Requisito previo:
Necesitas tener un artículo configurado como KIT con componentes

### ✏️ Acciones:
1. **Crear/Identificar un KIT** (ejemplo: "Kit Evento Completo")
2. **Verificar que tiene componentes** con campo `es_kit_articulo = 1`

### 🔍 Verificación:
```sql
-- Ver componentes del kit
SELECT 
    a_maestro.nombre_articulo as kit_maestro,
    a_comp.nombre_articulo as componente,
    k.cantidad_kit,
    ap.peso_medio_kg as peso_unitario
FROM kit k
INNER JOIN articulo a_maestro ON k.id_articulo_maestro = a_maestro.id_articulo
INNER JOIN articulo a_comp ON k.id_articulo_componente = a_comp.id_articulo
LEFT JOIN vista_articulo_peso ap ON a_comp.id_articulo = ap.id_articulo
WHERE k.id_articulo_maestro = [ID_KIT]
  AND k.activo_kit = 1;
```

```sql
-- Ver peso total del kit
SELECT 
    nombre_articulo_maestro,
    peso_total_componentes_kg
FROM vista_componentes_kit_peso
WHERE id_articulo_maestro = [ID_KIT];
```

**🧮 Cálculo manual:**
Si el kit tiene:
- Componente A (5 kg) × 2 unidades = 10 kg
- Componente B (3 kg) × 1 unidad = 3 kg
- **Total: 13 kg**

---

## ✅ CHECKLIST FINAL

Marca según completes:

- [ ] ✅ Paso 1: Peso medio de artículo calculado correctamente
- [ ] ✅ Paso 2: Presupuesto creado con líneas
- [ ] ✅ Paso 3: Peso de línea = peso_unitario × cantidad
- [ ] ✅ Paso 4: Peso total de versión suma líneas
- [ ] ✅ Paso 5: Peso total de presupuesto suma versiones
- [ ] ✅ Paso 6: PDF muestra sección peso cuando hay peso
- [ ] ✅ Paso 7: PDF NO muestra sección cuando no hay peso
- [ ] ✅ Paso 8: Valor 0 se distingue de NULL
- [ ] ✅ Paso 9: Decimales se mantienen (3 dígitos)
- [ ] ⏩ Paso 10: Kits calculan peso (opcional)

---

## 🐛 Problemas Comunes

### ❌ "Vista no devuelve resultados"
```sql
-- Verificar que existe
SHOW TABLES LIKE 'vista_articulo_peso';

-- Ver estructura
DESCRIBE vista_articulo_peso;

-- Ver primeros registros
SELECT * FROM vista_articulo_peso LIMIT 5;
```

### ❌ "Peso medio es NULL pero elementos tienen peso"
```sql
-- Verificar filtro activo
SELECT 
    codigo_elemento,
    peso_elemento,
    activo_elemento
FROM elemento
WHERE id_articulo_elemento = [ID_ARTICULO];

-- Deben estar activo_elemento = 1
```

### ❌ "PDF no muestra sección"
1. Verificar query en `impresionpresupuesto_m2_pdf_es.php`
2. Verificar `$peso_total_kg > 0`
3. Agregar debug: `error_log("Peso: " . $peso_total_kg);`

### ❌ "Cálculo incorrecto"
```sql
-- Recalcular manualmente
SELECT 
    SUM(peso_elemento * cantidad_linea) as manual
FROM elemento e
INNER JOIN linea_presupuesto lp ON lp.id_articulo = e.id_articulo_elemento
WHERE lp.id_presupuesto = [ID]
  AND e.activo_elemento = 1
  AND lp.activo_linea_ppto = 1;
```

---

## 📞 ¿Problemas?

Si algo no funciona:
1. Anota el **paso exacto** donde falla
2. Copia el **resultado SQL** que obtienes
3. Copia el **resultado esperado** de esta guía
4. Proporciona el **mensaje de error** (si hay)

---

**Tiempo estimado:** 20-30 minutos  
**Siguiente paso:** Empezar con PASO 1
