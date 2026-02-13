# Conclusiones Reunión - Formato PDF Presupuestos
**Fecha**: 11 de febrero de 2026  
**Tema**: Mejoras y ajustes en la generación del PDF de presupuestos

---

## 📋 Índice de Cambios

### 1. Observaciones de Líneas de Presupuesto ✅ **COMPLETADA**
**Situación actual**: Las observaciones de las líneas se pierden o no se muestran correctamente.

**Cambios requeridos**:
- ✅ Las observaciones de cada línea de presupuesto deben aparecer en la parte inferior del PDF
- ✅ Si no hay observaciones, el sistema **NO debe reservar espacio** para esta sección
- ✅ Optimización de espacio dinámico

**Implementación realizada** (13 feb 2026):
- ✅ Campo `observaciones_linea_ppto` agregado al SELECT del modelo (`ImpresionPresupuesto.php`)
- ✅ Renderizado en PDF después de cada línea y **antes** de los componentes del KIT
- ✅ Formato: Helvetica 6.5pt, color gris (80,80,80), indentación 4 espacios
- ✅ Solo se muestra si hay observaciones (condicional)
- ✅ Soporte MultiCell para texto multilínea
- ✅ Orden de renderizado: Línea → Observaciones → Componentes KIT

---

### 2. Formato de Líneas de Artículos ✅ **COMPLETADA**

**Cambios requeridos**:
- ❌ **Eliminar negritas** de la primera línea de cada artículo
- ❌ **Quitar líneas de espacios redundantes** (las que reservan dos líneas añaden una más en blanco)
- ✅ Formato limpio y consistente

---

### 3. Cabecera - Nº Presupuesto de Cliente ✅ **COMPLETADA**

**Situación actual**: Se muestra la cabecera incluso cuando no hay número de presupuesto del cliente.

**Cambio requerido**:
- ✅ Si el campo `Nº Presupuesto de cliente` está vacío o no existe:
  - **NO mostrar la cabecera del campo**
  - **NO mostrar el campo vacío**
  - Eliminación completa de la sección

---

### 4. Ubicación del Evento ✅ **COMPLETADA**

**Situación actual**: Se muestra la cabecera del campo incluso cuando está vacío.

**Cambio requerido**:
- ✅ Si el campo `Ubicación del evento` (lateral derecho) está vacío:
  - **NO mostrar la cabecera**
  - **NO mostrar el campo**
  - Eliminación completa de la sección

---

### 5. Título Principal "PRESUPUESTO" ✅ **COMPLETADA**

**Cambio requerido**:
- ✅ Añadir la palabra **"PRESUPUESTO"** en letras grandes en la parte superior del documento
- ✅ Diseño destacado y profesional

---

### 6. CIF de la Empresa ✅ **COMPLETADA**

**Situación actual**: Se muestra el CIF incluso cuando termina en 0000 (empresas ficticias).

**Cambio requerido**:
- ✅ Si los últimos 4 dígitos del CIF son `0000`:
  - **NO mostrar el CIF**
  - **NO mostrar el titular "CIF:"**
  - Ejemplo: `B12340000` → NO se muestra

---

### 7. Observación Cabecera - Montaje y Alquiler ✅ **COMPLETADA**

**Situación actual**: Se incluyen fechas en la observación.

**Cambio requerido**:
- ✅ Texto **fijo**: "Montaje ______ alquiler"
- ❌ **NO incluir fechas** (ya aparecen en la cabecera del presupuesto)
- ✅ Formato simplificado

---

### 8. Subtotales por Fecha ✅ **COMPLETADA**

**Cambio requerido**:
- ❌ **Eliminar completamente** los subtotales por fecha del PDF
- ✅ Solo mostrar el total general al final

---

### 9. Totales Finales - Descuento ✅ **COMPLETADA**

**Situación actual**: No se muestra el importe total del descuento aplicado.

**Cambio requerido**:
- ✅ Añadir línea con el **importe total del descuento**
- ✅ Estructura propuesta:
  ```
  Subtotal:           XXX,XX €
  Descuento:          -YY,YY €
  Base Imponible:     XXX,XX €
  IVA (21%):          XX,XX €
  ─────────────────────────────
  TOTAL:              XXX,XX €
  ```

**Implementación**:
- ✅ Añadido cálculo de subtotal sin descuento y descuento total (líneas 650-677)
- ✅ Cálculo correcto: si hay coeficiente (cantidad × precio × coeficiente), si no (días × cantidad × precio)
- ✅ Línea "Subtotal" añadida antes de "Base Imponible" en sección de totales
- ✅ Línea "Descuento" en color rojo con signo negativo (-) añadida
- ✅ Condicional: solo se muestra si total_descuentos > 0
- ✅ Formato español aplicado (1.234,56 €)
- ✅ Verificación matemática: Subtotal - Descuento = Base Imponible

---

### 10. Observaciones - Formato de Referencias ✅ **COMPLETADA**

**Situación actual**: Se muestra texto como "Familia: XXX, Artículo: XXX, etc."

**Cambio requerido**:
- ❌ Eliminar texto descriptivo largo
- ✅ Cambiar por sistema de asteriscos:
  - `*` para referencias de primer nivel
  - `**` para referencias de segundo nivel
- ✅ Formato más limpio y profesional

---

### 11. Pies de Empresa ✅ **COMPLETADA**

**Situación actual**: Los pies de empresa (configurados en la pantalla de empresas) aparecen en posición incorrecta.

**Cambio requerido**:
- ✅ **Bajar los pies de empresa al final del presupuesto**
- ✅ Después de los totales
- ✅ Antes de las firmas

**Estructura final**:
```
[Totales]
[Observaciones]
[Pies de empresa] ← AQUÍ
[Firmas]
```

---

### 12. Firmas - Posicionamiento ✅ **COMPLETADA**

**Situación actual**: Las firmas están en la parte inferior del documento.

**Cambio requerido**:
- ✅ **Subir las firmas** al final de las observaciones del presupuesto
- ✅ Antes de los pies de empresa

**Nueva estructura**:
```
[Totales]
[Observaciones]
[Firmas] ← AQUÍ
[Pies de empresa]
```

---

### 13. Firma - Texto del Departamento ✅ **COMPLETADA**

**Situación actual**: Aparece "MDR" en la firma.

**Cambio requerido**:
- ❌ Eliminar "MDR"
- ✅ Cambiar por **"Departamento Comercial"**

---

### 14. Nueva Funcionalidad - Firma de Empleado

**Requerimiento**: Añadir firma personalizada del empleado comercial en el presupuesto.

**Cambios requeridos**:

#### 14.1 Base de Datos
- ✅ Añadir campo `firma_empleado` en la tabla `empleado`
  - Tipo: `VARCHAR(255)` o `TEXT`
  - Puede almacenar ruta de imagen o texto HTML

#### 14.2 Pantalla de Empleados
- ✅ Añadir campo de entrada para la firma
- ✅ Opciones posibles:
  - Upload de imagen de firma
  - Editor de texto para firma HTML
  - Campo de texto simple

#### 14.3 Modelo `Empleado.php`
- ✅ Actualizar métodos `insert_empleado()` y `update_empleado()`
- ✅ Incluir campo `firma_empleado`

#### 14.4 PDF del Presupuesto
- ✅ Recuperar firma del empleado asociado al presupuesto
- ✅ Mostrar en la sección de firmas
- ✅ Formato:
  ```
  ________________________          ________________________
  Departamento Comercial            [Nombre del Empleado]
                                    [Firma personalizada]
  ```

---

### 15. Líneas del Presupuesto - Bordes Grises ✅ **COMPLETADA**

**Cambio requerido**:
- ✅ Aplicar bordes grises claros a las líneas del cuerpo del presupuesto
- ✅ Mejorar legibilidad y aspecto visual de la tabla de artículos
- ✅ Color de bordes: gris claro (200, 200, 200)

**Implementación**:
- ✅ SetDrawColor(200, 200, 200) aplicado en cabeceras de tabla
- ✅ Bordes grises claros en todas las líneas de datos del cuerpo
- ✅ Bordes grises claros en subtotales por ubicación
- ✅ Bordes grises claros en subtotales por fecha
- ✅ Restauración del color negro después de cada sección
- ✅ Aspecto uniforme y profesional en toda la tabla del presupuesto

---

### 16. Fechas de Montaje y Desmontaje - Optimización de Espacio

**Situación actual**: Las fechas de montaje y desmontaje se muestran como columnas en cada línea del cuerpo del presupuesto.

**Problema identificado por el cliente**:
- Por cada fecha de inicio (grupo de líneas), todas las fechas de montaje y desmontaje de todos los elementos son iguales
- Las columnas ocupan espacio innecesario cuando los valores se repiten
- El cliente solicita eliminar estas columnas del cuerpo y moverlas a la cabecera

**Consideración técnica importante**:
- El sistema permite definir fechas de montaje y desmontaje diferentes para cada artículo
- No hay restricción a nivel de base de datos que garantice que sean iguales
- Dependemos de que el usuario introduzca fechas consistentes por grupo de fecha de inicio

**Propuesta de solución**:

#### Opción A: Criterio de Mayoría
1. **Análisis por grupo de fecha de inicio**: Dentro de cada grupo de líneas con la misma fecha de inicio, analizar las fechas de montaje y desmontaje
2. **Detectar fecha predominante**: Si la mayoría de las líneas tienen las mismas fechas, mostrarlas en la cabecera del grupo
3. **Excepciones en observaciones**: Si alguna línea tiene fechas diferentes, agregarlas automáticamente al campo de observaciones de esa línea
   - Formato propuesto: `"Mtje: DD/MM/YYYY - Dsmtje: DD/MM/YYYY"`

**Criterios a definir**:
- ¿Qué porcentaje consideramos "mayoría"? (¿50%+1?, ¿80%?, ¿100%?)
- ¿Cómo se muestra en la cabecera? "Fecha inicio: DD/MM - Mtje: DD/MM - Dsmtje: DD/MM"

**Ventajas Opción A**:
- ✅ Flexible y adaptable a diferentes escenarios
- ✅ Optimiza espacio incluso con excepciones
- ✅ Usa el campo de observaciones recién implementado

**Desventajas Opción A**:
- ⚠️ Requiere definir criterio de "mayoría" (puede ser ambiguo)
- ⚠️ Mezcla observaciones del usuario con datos técnicos auto-generados
- ⚠️ Mayor complejidad de implementación y mantenimiento

#### Opción B: Criterio Estricto (Recomendada)
1. **Análisis por grupo de fecha de inicio**: Verificar si TODAS las líneas del grupo tienen las mismas fechas de montaje y desmontaje
2. **Caso de unanimidad**: Si todas coinciden, mostrar en cabecera y eliminar columnas del cuerpo
3. **Caso de diferencias**: Si hay alguna diferencia, mantener las columnas en el cuerpo para todas las líneas del grupo
   - Evita confusión al usuario
   - No mezcla información de cabecera con observaciones

**Ventajas Opción B**:
- ✅ Comportamiento predecible y consistente
- ✅ No requiere tomar decisiones de "mayoría"
- ✅ Más fácil de entender para el usuario final
- ✅ El campo de observaciones mantiene su propósito original
- ✅ Lógica simple = más fácil de testear y mantener
- ✅ Educativo: si el usuario ve las columnas, sabe que hay inconsistencias

**Desventajas Opción B**:
- ⚠️ Menos flexible: no optimiza espacio si hay una sola excepción

#### Opción C: Híbrida
1. **Análisis estricto**: Si todas las líneas coinciden → mostrar en cabecera
2. **Aviso visual**: Si hay diferencias, mostrar en cabecera las fechas más comunes y añadir un asterisco (*) en las líneas excepcionales
3. **Detalle en observaciones**: Las excepciones se detallan automáticamente en observaciones

**Ventajas Opción C**:
- ✅ Balance entre optimización de espacio y claridad
- ✅ Aviso visual claro de excepciones

**Desventajas Opción C**:
- ⚠️ Mayor complejidad que Opción B
- ⚠️ Mezcla observaciones del usuario con datos técnicos

**Implementación técnica requerida**:
- Modificar lógica de renderizado en controlador PDF
- Añadir análisis de fechas por grupo antes del renderizado
- Agregar fechas Mtje/Dsmtje en subtotales por fecha (cabecera de grupo)
- Ajustar ancho de columnas si se eliminan las de montaje/desmontaje
- Auto-generar texto en observaciones para excepciones (solo Opción A o C)

**Campos involucrados**:
- `fecha_montaje_linea_ppto`
- `fecha_desmontaje_linea_ppto`
- `fecha_inicio_linea_ppto` (agrupador)
- `observaciones_linea_ppto` (para excepciones en Opción A/C)

**Recomendación técnica**:
Se recomienda **Opción B (Criterio Estricto)** porque:
1. Mantiene claridad y consistencia
2. Evita lógica compleja de mayorías
3. No contamina el campo de observaciones con datos técnicos
4. Es más fácil de testear y mantener
5. El usuario verá rápidamente si hay inconsistencias en sus datos
6. Comportamiento binario predecible (todo o nada)

**Decisión pendiente del cliente**.

---

## 📊 Orden Final del PDF

```
┌─────────────────────────────────────────┐
│ PRESUPUESTO (título grande)             │
├─────────────────────────────────────────┤
│ Cabecera (datos empresa, cliente)       │
│ - Nº Presupuesto cliente (si existe)    │
│ - Ubicación evento (si existe)          │
│ - CIF (si no termina en 0000)           │
├─────────────────────────────────────────┤
│ Observación fija: Montaje __ alquiler   │
├─────────────────────────────────────────┤
│ Líneas de presupuesto (sin negritas)    │
│ - Sin subtotales por fecha              │
├─────────────────────────────────────────┤
│ Totales con descuento detallado         │
├─────────────────────────────────────────┤
│ Observaciones de líneas (* y **)        │
├─────────────────────────────────────────┤
│ Firmas (Dpto. Comercial + Empleado)     │
├─────────────────────────────────────────┤
│ Pies de empresa                          │
└─────────────────────────────────────────┘
```

---

## 🎯 Prioridad de Implementación

### Alta Prioridad
1. ✅ Observaciones de líneas en PDF
2. ✅ Eliminación de espacios redundantes
3. ✅ Campos condicionales (Nº Ppto Cliente, Ubicación, CIF)
4. ✅ Título "PRESUPUESTO"

### Media Prioridad
5. ✅ Reordenamiento (Firmas + Pies de empresa)
6. ✅ Formato de observaciones (* y **)
7. ✅ Importe total de descuento
8. ✅ Eliminar subtotales por fecha

### Baja Prioridad (Nueva Funcionalidad)
9. ✅ Campo firma en ficha de empleados
10. ✅ Integración firma empleado en PDF

---

## 📝 Archivos Afectados

### Controllers
- `controller/impresionpresupuesto_m2_pdf_es.php` (principal)
- `controller/impresionpresupuesto_m2_es.php` (respaldo)
- `controller/impresionpresupuesto.php` (original)

### Models
- `models/Empleado.php` (añadir campo firma)
- `models/Presupuesto.php` (si es necesario)

### Views
- `view/MntEmpleados/` (pantalla de empleados)

### Base de Datos
- `migrations/` (nueva migración para campo firma_empleado)

---

## 💡 Notas Técnicas

### Librería PDF
- El sistema utiliza **TCPDF** para la generación de PDFs
- Ubicación: `public/lib/tcpdf/`

### Consideraciones
- Mantener compatibilidad con versiones anteriores
- Crear backup antes de modificaciones
- Probar con presupuestos reales de diferentes clientes
- Validar todos los casos edge (campos vacíos, NULL, etc.)

### Testing
- [ ] Presupuesto con todas las observaciones
- [ ] Presupuesto sin observaciones
- [ ] Presupuesto sin Nº Cliente
- [ ] Presupuesto sin Ubicación
- [ ] Presupuesto con CIF terminado en 0000
- [ ] Presupuesto con descuentos
- [ ] Presupuesto sin descuentos
- [ ] Presupuesto con firma de empleado
- [ ] Presupuesto sin firma de empleado

---

## ✅ Checklist de Implementación

- [ ] 1. Añadir título "PRESUPUESTO" en parte superior
- [ ] 2. Eliminar negritas de primera línea
- [ ] 3. Quitar líneas de espacios redundantes
- [ ] 4. Condicional: Nº Presupuesto Cliente
- [ ] 5. Condicional: Ubicación del Evento
- [ ] 6. Condicional: CIF terminado en 0000
- [ ] 7. Fijar texto: "Montaje ______ alquiler"
- [ ] 8. Eliminar subtotales por fecha
- [ ] 9. Añadir línea de descuento en totales
- [ ] 10. Cambiar formato observaciones a * y **
- [ ] 11. Mover pies de empresa al final
- [ ] 12. Mover firmas después de observaciones
- [ ] 13. Cambiar "MDR" por "Departamento Comercial"
- [ ] 14. Crear campo firma en BD (empleado)
- [ ] 15. Añadir firma en pantalla empleados
- [ ] 16. Integrar firma empleado en PDF
- [ ] 17. Mostrar observaciones de líneas en PDF
- [ ] 18. Ocultar sección observaciones si está vacía

---

**Última actualización**: 11 de febrero de 2026  
**Estado**: Pendiente de implementación  
**Archivo**: `docs/presupuestos_20260211.md`
