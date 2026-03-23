# 🧪 Tests Prioritarios - Sistema de Peso
> Lista ejecutable de tests en orden de importancia

---

## 🔴 CRÍTICOS (Ejecutar primero)

### 1. Verificar estructura BD completa
```sql
-- Ejecutar en HeidiSQL/phpMyAdmin
SHOW COLUMNS FROM elemento LIKE 'peso_elemento';
SHOW TABLES LIKE '%peso%';
SHOW INDEX FROM elemento WHERE Key_name LIKE '%peso%';
```
**Resultado esperado:** Campo existe, 5 vistas, 2 índices

---

### 2. Test INSERT elemento con peso
**Pasos:**
1. Ir a un artículo cualquiera
2. Crear nuevo elemento
3. Ingresar peso: `12.500`
4. Guardar
5. Verificar en BD:
   ```sql
   SELECT codigo_elemento, peso_elemento 
   FROM elemento 
   ORDER BY id_elemento DESC 
   LIMIT 1;
   ```
**Resultado esperado:** `12.500` (no 12.5, no NULL)

---

### 3. Test UPDATE elemento con peso
**Pasos:**
1. Editar elemento del test anterior
2. Cambiar peso a: `25.750`
3. Guardar
4. Verificar en BD que cambió
5. Editar de nuevo, dejar campo vacío
6. Guardar
7. Verificar en BD: debe ser NULL

**Resultado esperado:** Cambios se guardan correctamente

---

### 4. Test visualización en subtabla
**Pasos:**
1. En listado de elementos, hacer clic en `[+]` del elemento con peso
2. Buscar fila "Peso" en la subtabla desplegada

**Resultado esperado:** 
- Muestra "25.750 kg" con icono caja
- Si peso es NULL, muestra "--"

---

### 5. Test cálculo peso medio artículo
```sql
-- Ejecutar después de tener 2-3 elementos con peso del mismo artículo
SELECT 
    id_articulo,
    nombre_articulo,
    total_elementos,
    elementos_con_peso,
    peso_medio_kg,
    peso_total_kg
FROM vista_articulo_peso
WHERE id_articulo = [TU_ID_ARTICULO]
LIMIT 1;
```
**Cálculo manual:** Si tienes elementos con peso 10, 12, 14 → peso_medio = 12.000

---

## 🟡 IMPORTANTES (Ejecutar después)

### 6. Test presupuesto con peso
**Pasos:**
1. Crear presupuesto nuevo
2. Añadir línea con artículo que tenga peso calculado (de test 5)
3. Cantidad: 3 unidades
4. Verificar cálculo:
   ```sql
   SELECT 
       nombre_articulo,
       cantidad_linea,
       peso_unitario_kg,
       peso_total_linea_kg
   FROM vista_linea_peso
   WHERE id_presupuesto = [TU_ID_PRESUPUESTO];
   ```

**Resultado esperado:** peso_total_linea = peso_unitario × cantidad

---

### 7. Test peso total versión
```sql
SELECT 
    numero_presupuesto,
    total_lineas,
    lineas_con_peso,
    peso_total_version_kg
FROM vista_version_presupuesto_peso
WHERE id_presupuesto = [TU_ID_PRESUPUESTO];
```

**Resultado esperado:** Suma de todas las líneas con peso

---

### 8. Test PDF presupuesto CON peso
**Pasos:**
1. Abrir presupuesto del test 6
2. Generar PDF
3. Buscar sección "PESO TOTAL DEL EVENTO" después de TOTAL €

**Resultado esperado:** Sección visible con peso formateado "XXX.XXX kg"

---

### 9. Test PDF presupuesto SIN peso
**Pasos:**
1. Crear presupuesto con artículos SIN peso
2. Generar PDF
3. Buscar sección de peso

**Resultado esperado:** Sección NO aparece

---

## 🟢 OPCIONALES (Si hay tiempo)

### 10. Test valor cero vs NULL
**Pasos:**
1. Crear elemento con peso `0`
2. Verificar muestra "0.000 kg" (no "--")
3. Verificar en BD es 0 (no NULL)
4. Verificar que SÍ cuenta en peso_medio

---

### 11. Test precisión decimal
**Pasos:**
1. Crear elemento con peso `10.999`
2. Crear línea presupuesto con cantidad 3
3. Verificar cálculo: 10.999 × 3 = 32.997
4. Verificar en PDF muestra "32.997 kg"

---

### 12. Test kit simple (si hay kits)
**Requisito:** Tener un kit con 2-3 componentes que tengan peso

```sql
SELECT 
    nombre_articulo_maestro,
    peso_total_componentes_kg
FROM vista_componentes_kit_peso
WHERE id_articulo_maestro = [ID_KIT];
```

**Cálculo manual:** Sumar (peso_componente × cantidad) de todos

---

### 13. Test validación frontend
**Pasos:**
1. Intentar ingresar peso `-5` → Debe rechazar
2. Intentar ingresar `100000` → Debe rechazar
3. Intentar ingresar `10.12345` → Debe truncar/redondear

---

### 14. Test performance
```sql
-- Medir tiempo de ejecución
SELECT SQL_NO_CACHE
    COUNT(*) as total_lineas,
    SUM(peso_total_linea_kg) as peso_total
FROM vista_linea_peso
WHERE activo_linea_ppto = 1;
```

**Resultado esperado:** < 2 segundos con cientos de líneas

---

## ✅ Checklist Rápido

Marca según vayas completando:

- [ ] 1. Estructura BD completa
- [ ] 2. INSERT elemento con peso
- [ ] 3. UPDATE elemento con peso
- [ ] 4. Visualización subtabla
- [ ] 5. Peso medio artículo
- [ ] 6. Presupuesto con peso
- [ ] 7. Peso total versión
- [ ] 8. PDF CON peso
- [ ] 9. PDF SIN peso
- [ ] 10. Valor 0 vs NULL
- [ ] 11. Precisión decimal
- [ ] 12. Kit simple
- [ ] 13. Validación frontend
- [ ] 14. Performance

---

## 🐛 Si encuentras errores

### Peso no se guarda
1. Verificar campo existe: `SHOW COLUMNS FROM elemento LIKE 'peso_elemento';`
2. Verificar controller envía peso: Ver Network en DevTools
3. Verificar model recibe peso: Revisar logs

### Peso no se muestra en subtabla
1. Verificar vista incluye peso: Ver `PLAN_COMPROBACIONES_PESO.md` sección 1.4
2. Verificar controller devuelve peso en JSON
3. Verificar JS accede a `d.peso_elemento`

### Peso no aparece en PDF
1. Verificar vista_version_presupuesto_peso devuelve datos
2. Verificar query en impresionpresupuesto_m2_pdf_es.php
3. Verificar condicional `if ($peso_total_kg > 0)`

### Cálculos incorrectos
1. Verificar elementos tienen peso: `SELECT COUNT(*) FROM elemento WHERE peso_elemento IS NOT NULL;`
2. Verificar vistas filtran activo=1
3. Recalcular manualmente y comparar

---

**Tiempo estimado total:** 30-45 minutos  
**Prioridad:** Ejecutar tests 1-9 mínimo
