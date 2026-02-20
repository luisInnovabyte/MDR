# Implementación: Switch Destacar Observaciones de Pie

**Fecha:** 20 de febrero de 2026  
**Rama:** modelo_presupuesto  
**Estado:** ✅ COMPLETADO

---

## 📋 Resumen

Se ha implementado un switch en el formulario de presupuestos para controlar cómo se visualizan las observaciones de pie en el PDF generado.

### Funcionalidad

- **Switch ACTIVADO (TRUE - por defecto):**
  - Las observaciones de pie aparecen destacadas al final del documento
  - Con líneas decorativas superior e inferior
  - Fondo gris claro
  - Texto centrado
  - Separadas de las demás observaciones

- **Switch DESACTIVADO (FALSE):**
  - Las observaciones de pie se integran con las observaciones de familias/artículos
  - Sin decoración ni líneas
  - Texto alineado a la izquierda
  - Símbolo: `***` (triple asterisco)
  - Mismo estilo que las demás observaciones

---

## ✅ Archivos Modificados

### 1. Base de Datos
- ✅ **BD/migrations/add_destacar_observaciones_pie.sql**
  - Nuevo archivo de migración SQL
  - Agrega campo `destacar_observaciones_pie_presupuesto BOOLEAN DEFAULT TRUE`
  - **PENDIENTE: Ejecutar manualmente en la base de datos**

### 2. Modelos
- ✅ **models/Presupuesto.php**
  - Firma de `insert_presupuesto()` - agregado parámetro #23
  - SQL INSERT - agregado campo
  - bindValue INSERT - agregado bindValue(23) y renumerado 23→24, 24→25, 25→26
  - Firma de `update_presupuesto()` - agregado parámetro #23
  - SQL UPDATE - agregado campo
  - bindValue UPDATE - agregado bindValue(23) y renumerado 23→24, 24→25, 25→26, 26→27

- ✅ **models/ImpresionPresupuesto.php**
  - Agregado campo `destacar_observaciones_pie_presupuesto` a SELECT en línea 87

### 3. Controladores
- ✅ **controller/presupuesto.php**
  - Operación INSERT - agregado parámetro con valor por defecto 1
  - Operación UPDATE - agregado parámetro con valor por defecto 1

- ✅ **controller/impresionpresupuesto_m2_pdf_es.php**
  - Nuevo bloque para observaciones de pie integradas (líneas 1488-1509)
  - Modificada condición para observaciones destacadas (líneas 1514-1516)
  - Lógica condicional según valor del campo

### 4. Vistas HTML
- ✅ **view/Presupuesto/formularioPresupuesto.php**
  - Nuevo checkbox después de observaciones_pie_ingles (líneas 465-476)
  - Con icono de estrella y texto explicativo
  - Marcado por defecto (checked)

### 5. Vistas JavaScript
- ✅ **view/Presupuesto/formularioPresupuesto.js**
  - Carga del checkbox (línea 620)
  - Captura del valor (línea 747)
  - Parámetro en llamada a `verificarPresupuestoExistente()` (línea 799)
  - Parámetro en definición de `verificarPresupuestoExistente()` (línea 834)
  - Parámetro en llamada a `guardarPresupuesto()` (línea 872)
  - Parámetro en definición de `guardarPresupuesto()` (línea 922)
  - Campo en objeto `formData` (línea 958)

---

## 🚀 Pasos para Completar la Implementación

### 1️⃣ Ejecutar la Migración SQL (OBLIGATORIO)

```bash
# Conectarse a la base de datos
mysql -h 217.154.117.83 -P 3308 -u administrator -p toldos_db

# Ejecutar el script de migración
source W:/MDR/BD/migrations/add_destacar_observaciones_pie.sql

# O copiar y pegar directamente:
ALTER TABLE presupuesto 
ADD COLUMN destacar_observaciones_pie_presupuesto BOOLEAN DEFAULT TRUE 
COMMENT 'Controla visualización de observaciones de pie: TRUE=destacadas con líneas y centrado, FALSE=integradas sin decoración y alineadas a izquierda'
AFTER observaciones_pie_ingles_presupuesto;

# Verificar que se creó correctamente:
SELECT COLUMN_NAME, COLUMN_TYPE, COLUMN_DEFAULT, COLUMN_COMMENT 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = 'toldos_db' 
AND TABLE_NAME = 'presupuesto' 
AND COLUMN_NAME = 'destacar_observaciones_pie_presupuesto';
```

### 2️⃣ Limpiar Caché del Navegador

Para asegurarse de que JavaScript se cargue correctamente:

- **Chrome/Edge:** Ctrl + Shift + R
- **Firefox:** Ctrl + F5
- O agregar cache-busting al script tag si es necesario

### 3️⃣ Pruebas Recomendadas

1. **Crear nuevo presupuesto con switch activado:**
   - Agregar observaciones de pie
   - Guardar
   - Generar PDF → Verificar que aparecen destacadas

2. **Crear nuevo presupuesto con switch desactivado:**
   - Agregar observaciones de pie
   - Desmarcar el checkbox
   - Guardar
   - Generar PDF → Verificar que aparecen integradas con '***'

3. **Editar presupuesto existente:**
   - Abrir presupuesto antiguo (sin el campo)
   - Verificar que checkbox aparece marcado (compatibilidad hacia atrás)
   - Guardar sin modificar
   - Generar PDF → Verificar que mantiene comportamiento destacado

4. **Alternar el switch:**
   - Crear presupuesto con switch activado
   - Guardar
   - Editar y desactivar switch
   - Guardar
   - Generar PDF → Verificar cambio de formato

5. **Casos especiales:**
   - Presupuesto sin observaciones de pie → No debe mostrar nada
   - Presupuesto con observaciones pero sin familias/artículos → Debe crear sección "OBSERVACIONES"
   - Presupuesto con todas las observaciones → Debe integrarlas correctamente

---

## 🔧 Detalles Técnicos

### Compatibilidad con Registros Antiguos

Los presupuestos creados antes de esta implementación NO tienen el campo `destacar_observaciones_pie_presupuesto`. Para mantener compatibilidad:

- **En PHP (PDF):** El código verifica `!isset()` y asume TRUE si no existe
- **En JavaScript:** Al cargar, si el campo no existe, el checkbox se marca por defecto
- **En Base de Datos:** El campo tiene `DEFAULT TRUE`

Esto garantiza que todos los presupuestos existentes mantengan el comportamiento actual (observaciones destacadas).

### Estructura del Código

**Parámetros:**
- Antes: 25 parámetros en modelo, 27 en JS
- Ahora: 26 parámetros en modelo, 28 en JS

**Posición del nuevo parámetro:**
- Posición #23 en la lista de parámetros
- Entre `observaciones_pie_ingles_presupuesto` y `mostrar_obs_familias_presupuesto`

**Símbolos en PDF:**
- `*` - Observaciones de familia
- `**` - Observaciones de artículo
- `***` - Observaciones de pie (cuando no están destacadas)

---

## 📝 Notas Adicionales

- El campo es de tipo BOOLEAN: `1` = destacar (TRUE), `0` = integrar (FALSE)
- Por defecto: `TRUE` (comportamiento actual)
- El switch usa clase Bootstrap: `form-check form-switch`
- Las observaciones integradas mantienen el mismo estilo que familias/artículos
- La lógica PDF es condicional: solo se renderiza en un lugar dependiendo del valor

---

## ✨ Beneficios de la Implementación

1. **Flexibilidad:** Cliente puede elegir cómo presentar las observaciones de pie
2. **Consistencia:** Integración natural con observaciones existentes
3. **Compatibilidad:** Registros antiguos siguen funcionando sin cambios
4. **Usabilidad:** Switch intuitivo con texto explicativo
5. **Mantenibilidad:** Código bien estructurado y siguiendo patrones existentes

---

**Implementado por:** Claude (GitHub Copilot)  
**Validado por:** Pendiente de pruebas del usuario  
**Documentación:** Completa
