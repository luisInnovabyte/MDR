# 📋 Plan de Comprobaciones - Sistema de Peso

> **Proyecto:** MDR ERP Manager  
> **Funcionalidad:** Sistema de cálculo y visualización de peso  
> **Fecha:** 15 de febrero de 2026  
> **Estado:** ✅ Elementos OK | ⏳ Pendientes de verificar

---

## 🎯 Objetivo

Verificar que el sistema de peso funciona correctamente en todos los niveles:
- ✅ **Elementos**: Guardar y mostrar peso individual
- ⏳ **Artículos**: Cálculo de peso medio por artículo
- ⏳ **Kits**: Suma de peso de componentes
- ⏳ **Presupuestos**: Cálculo total por línea y global
- ⏳ **PDF**: Visualización correcta en documentos

---

## 📦 NIVEL 1: Base de Datos

### 1.1. Verificar estructura de tabla elemento
**Objetivo:** Confirmar que el campo peso_elemento existe con las características correctas

```sql
-- Verificar campo peso_elemento
SHOW COLUMNS FROM elemento LIKE 'peso_elemento';

-- Resultado esperado:
-- Field: peso_elemento
-- Type: decimal(10,3)
-- Null: YES
-- Default: NULL
```

**✅ Checklist:**
- [ ] Campo existe en tabla elemento
- [ ] Tipo de dato: DECIMAL(10,3)
- [ ] Permite NULL
- [ ] Default es NULL
- [ ] Comentario descriptivo presente

---

### 1.2. Verificar índices
**Objetivo:** Confirmar que los índices para optimización están creados

```sql
-- Verificar índices de peso
SHOW INDEX FROM elemento WHERE Key_name IN ('idx_peso_elemento', 'idx_articulo_peso');
SHOW INDEX FROM linea_presupuesto WHERE Key_name = 'idx_version_articulo_peso';
SHOW INDEX FROM kit WHERE Key_name = 'idx_maestro_activo_peso';
SHOW INDEX FROM articulo WHERE Key_name = 'idx_es_kit_activo_peso';

-- Resultado esperado: 5 índices
```

**✅ Checklist:**
- [ ] idx_peso_elemento en elemento (peso_elemento)
- [ ] idx_articulo_peso en elemento (id_articulo_elemento, activo_elemento, peso_elemento)
- [ ] idx_version_articulo_peso en linea_presupuesto
- [ ] idx_maestro_activo_peso en kit
- [ ] idx_es_kit_activo_peso en articulo

---

### 1.3. Verificar vistas SQL
**Objetivo:** Confirmar que las 5 vistas de cálculo de peso existen

```sql
-- Listar vistas de peso
SHOW TABLES LIKE '%peso%';

-- Resultado esperado: 5 vistas
-- vista_articulo_peso
-- vista_linea_peso
-- vista_presupuesto_peso
-- vista_version_presupuesto_peso
-- vista_componentes_kit_peso
```

**✅ Checklist:**
- [ ] Vista vista_articulo_peso existe
- [ ] Vista vista_linea_peso existe
- [ ] Vista vista_presupuesto_peso existe
- [ ] Vista vista_version_presupuesto_peso existe
- [ ] Vista vista_componentes_kit_peso existe

---

### 1.4. Verificar vista_elementos_completa
**Objetivo:** Confirmar que incluye el campo peso_elemento

```sql
-- Verificar que peso_elemento está en la vista
SELECT COLUMN_NAME 
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = 'toldos_db'
  AND TABLE_NAME = 'vista_elementos_completa'
  AND COLUMN_NAME = 'peso_elemento';

-- Debe devolver 1 fila
```

**✅ Checklist:**
- [ ] Campo peso_elemento incluido en vista_elementos_completa

---

### 1.5. Test de cálculo vista_articulo_peso
**Objetivo:** Verificar que calcula correctamente el peso medio por artículo

```sql
-- Ver peso medio de artículos normales (no kit)
SELECT 
    id_articulo,
    nombre_articulo,
    total_elementos,
    elementos_con_peso,
    peso_medio_kg,
    peso_total_kg
FROM vista_articulo_peso
WHERE total_elementos > 0
ORDER BY nombre_articulo
LIMIT 10;
```

**✅ Checklist:**
- [ ] Vista devuelve resultados
- [ ] total_elementos coincide con COUNT real de elementos
- [ ] elementos_con_peso solo cuenta los que tienen peso != NULL
- [ ] peso_medio_kg es correcto (peso_total/elementos_con_peso)
- [ ] Solo incluye artículos con es_kit_articulo = 0

**🧪 Test manual:**
1. Crear 3 elementos del mismo artículo con pesos: 10.500, 12.300, 11.200
2. Verificar que peso_medio_kg = 11.333
3. Verificar que peso_total_kg = 34.000

---

### 1.6. Test de cálculo vista_componentes_kit_peso
**Objetivo:** Verificar cálculo recursivo de peso de kits

```sql
-- Ver peso calculado de kits
SELECT 
    id_articulo_maestro,
    nombre_articulo_maestro,
    nivel_profundidad,
    tipo_componente,
    peso_total_componentes_kg
FROM vista_componentes_kit_peso
WHERE nivel_profundidad <= 2
ORDER BY nombre_articulo_maestro, nivel_profundidad;
```

**✅ Checklist:**
- [ ] Vista devuelve resultados para kits
- [ ] nivel_profundidad correcto para kits anidados
- [ ] tipo_componente identifica 'ARTICULO_NORMAL' y 'KIT_ANIDADO'
- [ ] peso_total_componentes_kg suma correctamente

**🧪 Test manual:**
1. Crear Kit A con:
   - Componente 1: Artículo peso medio 5 kg × cantidad 2 = 10 kg
   - Componente 2: Artículo peso medio 3 kg × cantidad 3 = 9 kg
2. Verificar peso_total_componentes_kg = 19.000 kg

---

## 🎨 NIVEL 2: Frontend - Formularios

### 2.1. Formulario de elemento - Crear nuevo
**Ubicación:** view/MntElementos/index.php (modal)

**✅ Checklist:**
- [ ] Campo "Peso (kg)" visible en formulario
- [ ] Input tipo number con step="0.001"
- [ ] Placeholder "0.000"
- [ ] Min="0", Max="99999.999"
- [ ] Sufijo visual " kg" presente
- [ ] Campo NO es obligatorio (requerido)
- [ ] Icono bi-box-seam presente

**🧪 Test manual:**
1. Abrir modal "Nuevo Elemento"
2. Intentar ingresar peso negativo → Debería rechazar
3. Ingresar peso "25.567" → Debería aceptar
4. Dejar campo vacío → Debería aceptar (NULL)
5. Ingresar "100000" → Debería rechazar (excede max)

---

### 2.2. Formulario de elemento - Editar existente
**Ubicación:** view/MntElementos/index.php (modal)

**✅ Checklist:**
- [ ] Al abrir elemento con peso, muestra el valor correcto
- [ ] Al abrir elemento sin peso (NULL), muestra campo vacío
- [ ] Formato con 3 decimales (25.500 no se convierte a 25.5)
- [ ] Modificar peso y guardar funciona correctamente
- [ ] Borrar peso y guardar guarda NULL

**🧪 Test manual:**
1. Editar elemento con peso 10.500 kg
2. Verificar que input muestra "10.500"
3. Cambiar a 0 y guardar → Debería guardar 0 (no NULL)
4. Cambiar a vacío y guardar → Debería guardar NULL

---

### 2.3. Listado de elementos - Subtabla desplegable
**Ubicación:** view/MntElementos/mntelementos.js (function format)

**✅ Checklist:**
- [ ] Fila de "Peso" visible en subtabla
- [ ] Icono bi-box-seam presente
- [ ] Formato: "X.XXX kg" con 3 decimales
- [ ] Si peso es NULL, muestra "--"
- [ ] Si peso es 0, muestra "0.000 kg"
- [ ] Ubicado en columna izquierda después de "Altura/Nivel"

**🧪 Test manual:**
1. Desplegar elemento con peso 12.500 → Debe mostrar "12.500 kg"
2. Desplegar elemento sin peso → Debe mostrar "--"
3. Desplegar elemento con peso 0 → Debe mostrar "0.000 kg"

---

## ⚙️ NIVEL 3: Backend - Controllers y Models

### 3.1. Controller elemento.php - Caso "listar"
**Archivo:** controller/elemento.php

**✅ Checklist:**
- [ ] JSON de respuesta incluye "peso_elemento"
- [ ] Si peso es NULL en BD, devuelve null en JSON
- [ ] Si peso es 0, devuelve 0 (no null)
- [ ] Si peso tiene decimales, los mantiene (no redondea)

**🧪 Test manual:**
1. Abrir DevTools → Network
2. Filtrar llamada a `elemento.php?op=listar`
3. Verificar JSON de respuesta incluye "peso_elemento": 12.500

---

### 3.2. Controller elemento.php - Caso "guardaryeditar"
**Archivo:** controller/elemento.php

**✅ Checklist:**
- [ ] POST recibe peso_elemento correctamente
- [ ] Si campo vacío, convierte a NULL
- [ ] Si campo es 0, mantiene 0 (no convierte a NULL)
- [ ] Llamada a insert_elemento incluye $peso_elemento en posición 11
- [ ] Llamada a update_elemento incluye $peso_elemento en posición 12

**🧪 Test manual:**
1. Crear elemento con peso 15.750 kg
2. Verificar en BD: `SELECT peso_elemento FROM elemento WHERE id_elemento = X;`
3. Resultado esperado: 15.750

---

### 3.3. Model Elemento.php - Método insert_elemento
**Archivo:** models/Elemento.php

**✅ Checklist:**
- [ ] Parámetro $peso_elemento en posición 11 con default null
- [ ] SQL INSERT incluye campo peso_elemento
- [ ] bindValue(11) con lógica condicional para NULL
- [ ] Si peso es NULL o '', guarda NULL
- [ ] Si peso es 0, guarda 0

**🧪 Test SQL directo:**
```sql
-- Después de INSERT via modelo
SELECT peso_elemento FROM elemento ORDER BY id_elemento DESC LIMIT 1;
-- Verificar que coincide con valor enviado
```

---

### 3.4. Model Elemento.php - Método update_elemento
**Archivo:** models/Elemento.php

**✅ Checklist:**
- [ ] Parámetro $peso_elemento en posición 12 con default null
- [ ] SQL UPDATE incluye `peso_elemento = ?`
- [ ] bindValue(11) con lógica condicional para NULL
- [ ] Actualiza correctamente peso existente
- [ ] Puede cambiar peso a NULL (borrar)
- [ ] Puede cambiar NULL a valor numérico

**🧪 Test manual:**
1. UPDATE elemento peso 10 → 20: `UPDATE elemento SET peso_elemento = 20 WHERE id_elemento = X`
2. UPDATE elemento peso 20 → NULL: Editar y dejar vacío
3. UPDATE elemento peso NULL → 15: Editar y poner 15

---

### 3.5. Model Elemento.php - Método get_elementoxid
**Archivo:** models/Elemento.php

**✅ Checklist:**
- [ ] Usa vista_elementos_completa (que incluye peso_elemento)
- [ ] Devuelve campo peso_elemento en array
- [ ] Si peso es NULL, devuelve null (no string vacío)

---

## 📊 NIVEL 4: Cálculos en Presupuestos

### 4.1. Vista vista_linea_peso - Peso por línea
**Objetivo:** Verificar cálculo de peso total por línea de presupuesto

```sql
-- Ver peso de líneas de un presupuesto
SELECT 
    id_linea_ppto,
    id_presupuesto,
    numero_presupuesto,
    id_articulo,
    nombre_articulo,
    cantidad_linea,
    peso_unitario_kg,
    peso_total_linea_kg
FROM vista_linea_peso
WHERE id_presupuesto = 1
ORDER BY id_linea_ppto;
```

**✅ Checklist:**
- [ ] peso_unitario_kg muestra peso del artículo (normal o kit)
- [ ] peso_total_linea_kg = cantidad_linea × peso_unitario_kg
- [ ] Si artículo no tiene peso, peso_unitario_kg es NULL
- [ ] Si artículo no tiene peso, peso_total_linea_kg es NULL

**🧪 Test manual:**
1. Crear línea presupuesto: Artículo peso medio 10 kg × cantidad 5
2. Verificar peso_total_linea_kg = 50.000

---

### 4.2. Vista vista_version_presupuesto_peso - Peso por versión
**Objetivo:** Verificar suma de peso total por versión de presupuesto

```sql
-- Ver peso total de cada versión
SELECT 
    id_version_presupuesto,
    id_presupuesto,
    numero_presupuesto,
    total_lineas,
    lineas_con_peso,
    peso_total_version_kg
FROM vista_version_presupuesto_peso
WHERE id_presupuesto = 1;
```

**✅ Checklist:**
- [ ] total_lineas cuenta todas las líneas activas
- [ ] lineas_con_peso cuenta solo líneas con peso != NULL
- [ ] peso_total_version_kg es SUM de peso_total_linea_kg

**🧪 Test manual:**
1. Crear presupuesto con 3 líneas:
   - Línea 1: 10 kg × 2 = 20 kg
   - Línea 2: 5 kg × 3 = 15 kg
   - Línea 3: Sin peso
2. Verificar peso_total_version_kg = 35.000

---

### 4.3. Vista vista_presupuesto_peso - Peso total del presupuesto
**Objetivo:** Verificar peso total considerando todas las versiones

```sql
-- Ver peso total del presupuesto (todas las versiones)
SELECT 
    id_presupuesto,
    numero_presupuesto,
    nombre_evento_presupuesto,
    total_versiones,
    versiones_con_peso,
    peso_total_presupuesto_kg
FROM vista_presupuesto_peso
WHERE id_presupuesto = 1;
```

**✅ Checklist:**
- [ ] Suma peso de TODAS las versiones activas
- [ ] Si hay versión sin peso, no afecta el cálculo
- [ ] peso_total_presupuesto_kg >= peso de versión individual

---

## 📄 NIVEL 5: Generación de PDF

### 5.1. PDF Presupuesto - Sección de peso
**Archivo:** controller/impresionpresupuesto_m2_pdf_es.php

**✅ Checklist:**
- [ ] Sección "PESO TOTAL DEL EVENTO" se muestra después de TOTAL €
- [ ] Solo aparece SI peso_total_kg > 0
- [ ] Formato: "X.XXX kg" con 3 decimales
- [ ] Estilo consistente con resto del PDF
- [ ] Icono/símbolo presente (📦 o similar)
- [ ] NO se muestra si peso es NULL o 0

**🧪 Test manual:**
1. Crear presupuesto CON peso (ej: 150 kg)
2. Generar PDF
3. Verificar sección "PESO TOTAL DEL EVENTO: 150.000 kg"
4. Crear presupuesto SIN peso
5. Generar PDF
6. Verificar que NO aparece sección de peso

---

### 5.2. PDF Presupuesto - Consulta de peso
**Archivo:** controller/impresionpresupuesto_m2_pdf_es.php

```php
// Verificar query de peso
$sql_peso = "SELECT peso_total_version_kg 
             FROM vista_version_presupuesto_peso 
             WHERE id_version_presupuesto = ?";
```

**✅ Checklist:**
- [ ] Query usa vista_version_presupuesto_peso
- [ ] Filtra por id_version_presupuesto correcto
- [ ] Maneja NULL correctamente
- [ ] Variable $peso_total_kg se inicializa

---

### 5.3. PDF Presupuesto - Renderizado condicional

**✅ Checklist:**
- [ ] Condicional `if ($peso_total_kg > 0)` funciona
- [ ] Celda con ancho correcto (full width)
- [ ] Altura de celda adecuada (se ve completa)
- [ ] Padding/margins consistentes
- [ ] Color de fondo distinguible
- [ ] Texto centrado
- [ ] Font size legible

---

## 🔄 NIVEL 6: Casos Edge y Validaciones

### 6.1. Valores NULL
**Objetivo:** Verificar comportamiento con valores NULL

**✅ Checklist:**
- [ ] Elemento sin peso (NULL) → Formulario muestra campo vacío
- [ ] Elemento sin peso (NULL) → Subtabla muestra "--"
- [ ] Elemento sin peso (NULL) → No afecta cálculo peso medio
- [ ] Artículo sin elementos con peso → peso_medio_kg es NULL
- [ ] Línea con artículo sin peso → peso_total_linea_kg es NULL
- [ ] Presupuesto sin líneas con peso → NO muestra sección en PDF

---

### 6.2. Valor 0 (cero)
**Objetivo:** Diferenciar entre NULL y 0

**✅ Checklist:**
- [ ] Elemento con peso 0 → Formulario muestra "0"
- [ ] Elemento con peso 0 → Subtabla muestra "0.000 kg"
- [ ] Elemento con peso 0 → SÍ cuenta para peso_medio
- [ ] Artículo con todos elementos peso 0 → peso_medio_kg = 0
- [ ] Línea con peso 0 → peso_total_linea_kg = 0
- [ ] Presupuesto con peso total 0 → NO muestra en PDF (solo si > 0)

---

### 6.3. Decimales y precisión
**Objetivo:** Verificar manejo correcto de decimales

**✅ Checklist:**
- [ ] Input acepta hasta 3 decimales (0.001)
- [ ] BD almacena DECIMAL(10,3) sin redondeo
- [ ] Cálculos mantienen precisión (no usan FLOAT)
- [ ] PDF muestra 3 decimales siempre (ej: 10 → 10.000)
- [ ] JSON devuelve número, no string

**🧪 Test manual:**
1. Guardar peso 10.999 kg
2. Verificar en BD: DECIMAL exacto, no 11.000
3. Calcular peso línea: 10.999 × 3 = 32.997
4. Verificar en PDF: "32.997 kg" (no "32.99" ni "33.0")

---

### 6.4. Valores extremos

**✅ Checklist:**
- [ ] Peso mínimo: 0.001 kg → Acepta y muestra correctamente
- [ ] Peso máximo: 99999.999 kg → Acepta
- [ ] Peso > máximo: 100000 kg → Frontend rechaza
- [ ] Peso negativo: -5 kg → Frontend rechaza
- [ ] Peso con más de 3 decimales: 10.12345 → Frontend trunca/redondea

---

## 🧩 NIVEL 7: Integración con Kits

### 7.1. Kit simple (1 nivel)
**Objetivo:** Verificar peso de kit con componentes directos

**🧪 Test manual:**
1. Crear Kit "KIT-A" con:
   - Artículo A (peso medio: 5 kg) × 2 unidades
   - Artículo B (peso medio: 3 kg) × 1 unidad
2. Peso total KIT-A = (5×2) + (3×1) = 13 kg
3. Verificar en vista_componentes_kit_peso:
   ```sql
   SELECT peso_total_componentes_kg 
   FROM vista_componentes_kit_peso 
   WHERE id_articulo_maestro = [ID_KIT_A];
   ```
4. Resultado esperado: 13.000

**✅ Checklist:**
- [ ] Vista calcula peso correcto
- [ ] Nivel_profundidad = 1
- [ ] tipo_componente = 'ARTICULO_NORMAL'

---

### 7.2. Kit anidado (2 niveles)
**Objetivo:** Verificar cálculo recursivo de peso

**🧪 Test manual:**
1. Crear Kit "KIT-A":
   - Artículo A (5 kg) × 2 = 10 kg
2. Crear Kit "KIT-B":
   - KIT-A (13 kg) × 1 = 13 kg
   - Artículo C (2 kg) × 3 = 6 kg
3. Peso total KIT-B = 13 + 6 = 19 kg
4. Verificar recursión en vista

**✅ Checklist:**
- [ ] Kit de nivel 1 calcula correctamente
- [ ] Kit de nivel 2 suma kit anidado + componentes propios
- [ ] tipo_componente identifica 'KIT_ANIDADO'
- [ ] nivel_profundidad = 2

---

### 7.3. Kit en presupuesto
**Objetivo:** Verificar que líneas con kits calculan peso correctamente

**🧪 Test manual:**
1. Crear presupuesto con línea:
   - KIT-B (peso total: 19 kg) × cantidad 2
2. Peso línea = 19 × 2 = 38 kg
3. Verificar en vista_linea_peso
4. Verificar en PDF

**✅ Checklist:**
- [ ] peso_unitario_kg del kit es correcto
- [ ] peso_total_linea_kg multiplica por cantidad
- [ ] PDF muestra peso total sumando kits

---

## 📱 NIVEL 8: Usabilidad y UX

### 8.1. Mensajes de validación

**✅ Checklist:**
- [ ] Error al intentar peso negativo: Mensaje claro
- [ ] Error al exceder máximo: Mensaje descriptivo
- [ ] No hay mensaje error si campo vacío (es opcional)

---

### 8.2. Formato visual

**✅ Checklist:**
- [ ] Sufijo " kg" se muestra en todos los lugares
- [ ] Iconos consistentes (bi-box-seam, peso, escala)
- [ ] Alineación correcta en tablas (derecha para números)
- [ ] Color distintivo en PDF para sección peso

---

### 8.3. Performance

**✅ Checklist:**
- [ ] Listado de elementos carga rápido con peso
- [ ] Cálculo de peso en presupuesto no ralentiza carga
- [ ] Vistas SQL optimizadas (con índices)
- [ ] PDF genera en tiempo razonable con peso

---

## ✅ Resumen de Estado

### Completado ✅
- [x] Estructura de BD (tabla, índices)
- [x] Vistas SQL creadas
- [x] Campo en formulario de elemento
- [x] Guardar peso en elemento (INSERT/UPDATE)
- [x] Mostrar peso en listado de elementos
- [x] PDF con sección de peso

### Pendiente de verificar ⏳
- [ ] Cálculos de vista_articulo_peso con datos reales
- [ ] Cálculos de vista_linea_peso
- [ ] Cálculos de vista_version_presupuesto_peso
- [ ] Cálculos de vista_presupuesto_peso
- [ ] Peso recursivo de kits (vista_componentes_kit_peso)
- [ ] PDF con presupuesto real con peso
- [ ] Casos edge (NULL, 0, decimales)
- [ ] Performance con volumen de datos

---

## 🚀 Próximos Pasos Sugeridos

1. **Crear datos de prueba:**
   - 5 artículos normales con elementos con peso
   - 2 kits simples
   - 1 kit anidado
   - 2 presupuestos con diferentes configuraciones

2. **Ejecutar queries de verificación:**
   - Todas las SELECT de verificación de este documento
   - Comparar resultados esperados vs reales

3. **Probar generación de PDF:**
   - Presupuesto CON peso → Debe mostrar sección
   - Presupuesto SIN peso → NO debe mostrar sección

4. **Test de estrés:**
   - Presupuesto con 50+ líneas con peso
   - Verificar tiempo de cálculo y generación PDF

---

## 📝 Notas Adicionales

- **Zona Horaria:** Europe/Madrid configurada en todos los modelos
- **Charset:** UTF8MB4 (utf8mb4_spanish_ci)
- **Soft Delete:** Todos los cálculos filtran activo = 1
- **Logging:** Operaciones críticas registradas en RegistroActividad

---

**Documento generado:** 15/02/2026  
**Versión:** 1.0  
**Autor:** Claude AI + Luis (Innovabyte)
