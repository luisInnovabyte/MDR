# Conclusiones Reunión - Formato PDF Presupuestos
**Fecha**: 11 de febrero de 2026  
**Tema**: Mejoras y ajustes en la generación del PDF de presupuestos

---

## 📋 Índice de Cambios

### 1. Observaciones de Líneas de Presupuesto
**Situación actual**: Las observaciones de las líneas se pierden o no se muestran correctamente.

**Cambios requeridos**:
- ✅ Las observaciones de cada línea de presupuesto deben aparecer en la parte inferior del PDF
- ✅ Si no hay observaciones, el sistema **NO debe reservar espacio** para esta sección
- ✅ Optimización de espacio dinámico

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

### 9. Totales Finales - Descuento

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
