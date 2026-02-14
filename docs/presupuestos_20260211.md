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

### 16. Fechas de Montaje y Desmontaje - Optimización de Espacio ✅ **COMPLETADA**

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

**Decisión del cliente**: Se implementó **Opción A con criterio del 30%**

**Implementación realizada** (13 feb 2026):
- ✅ Análisis automático de fechas predominantes por grupo de fecha_inicio
- ✅ Criterio: Si >= 30% de líneas tienen las mismas fechas → ocultar columnas
- ✅ Cabecera de fecha modificada: "Fecha inicio: DD/MM/YYYY | Mtje: DD/MM/YYYY | Dsmtje: DD/MM/YYYY"
- ✅ Columnas Mtje/Dsmtje eliminadas dinámicamente del cuerpo cuando aplica criterio
- ✅ Ancho de columna Descripción ajustado automáticamente (+30mm cuando se ocultan columnas)
- ✅ Auto-generación de observaciones para líneas excepcionales: "Mtje: DD/MM/YYYY - Dsmtje: DD/MM/YYYY"
- ✅ Integración con observaciones manuales del usuario (separadas con " | ")
- ✅ Componentes de KIT ajustados para respetar columnas ocultas
- ✅ Lógica aplicada a todos los grupos de fecha de forma independiente
- ✅ Corrección del cálculo de altura de filas considerando ancho dinámico de descripción
- ✅ Eliminación de espacios en blanco extras entre líneas

**Formato de observaciones auto-generadas**:
- Solo para líneas con fechas diferentes a las predominantes
- Formato: `Mtje: DD/MM/YYYY - Dsmtje: DD/MM/YYYY`
- Si ya hay observaciones del usuario: `[Obs usuario] | Mtje: DD/MM/YYYY - Dsmtje: DD/MM/YYYY`

**Correcciones aplicadas**:
- Fix: Cálculo de altura de fila ahora usa el ancho real de la columna descripción (49mm o 79mm según contexto)
- Fix: Posicionamiento correcto de observaciones sin añadir líneas extra
- Resultado: PDF sin espacios en blanco innecesarios entre líneas

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
- [x] 17. Mostrar observaciones de líneas en PDF
- [ ] 18. Ocultar sección observaciones si está vacía
- [ ] 19. Mostrar número de cuenta con forma de pago TRANSFERENCIA

---

### 17. Clientes Exentos de IVA - Operaciones Intracomunitarias ✅ **COMPLETADO**

**Fecha inicio**: 11 de febrero de 2026  
**Fecha finalización**: 14 de febrero de 2026  
**Prioridad**: Alta  
**Tipo**: Nueva funcionalidad

#### 📋 Situación Actual

Actualmente, el sistema calcula el IVA según el porcentaje configurado en cada artículo/línea del presupuesto (21%, 10%, 4%, etc.). No existe la posibilidad de marcar clientes como exentos de IVA para operaciones intracomunitarias o empresas con normativa especial.

#### 🎯 Cambios Requeridos

1. **En la tabla `cliente`:**
   - Añadir campo `exento_iva` (BOOLEAN, DEFAULT FALSE)
   - Añadir campo `justificacion_exencion_iva` (TEXT, DEFAULT 'Operación exenta de IVA según artículo 25 Ley 37/1992')

2. **En la pantalla de gestión de clientes:**
   - Checkbox para marcar cliente como exento de IVA
   - Campo de texto/textarea para editar la justificación
   - Al activar el checkbox, mostrar el campo de justificación
   - Valor por defecto: "Operación exenta de IVA según artículo 25 Ley 37/1992"

3. **En el cálculo de presupuestos:**
   - Si `cliente.exento_iva = TRUE`, forzar el cálculo de IVA al 0% para TODAS las líneas
   - Ignorar el porcentaje de IVA configurado en cada artículo
   - Mostrar IVA 0,00 € en el desglose de totales

4. **En el PDF del presupuesto:**
   - Mostrar el texto de justificación en el área de totales o después de los totales
   - Formato sugerido: Texto en cursiva o con fondo gris claro
   - Ubicación: Entre los totales y las observaciones de líneas

#### 💻 Implementación Técnica Requerida

##### 1. Migración de Base de Datos

```sql
-- Añadir campos a la tabla cliente
ALTER TABLE cliente 
ADD COLUMN exento_iva BOOLEAN DEFAULT FALSE COMMENT 'Cliente exento de IVA',
ADD COLUMN justificacion_exencion_iva TEXT 
    DEFAULT 'Operación exenta de IVA según artículo 25 Ley 37/1992' 
    COMMENT 'Texto legal de justificación de exención';

-- Índice para búsquedas
CREATE INDEX idx_exento_iva ON cliente(exento_iva);
```

##### 2. Modificaciones en el Modelo Cliente

Archivo: `models/Clientes.php`

- Actualizar método `insert_cliente()` para incluir los nuevos campos
- Actualizar método `update_cliente()` para incluir los nuevos campos
- Los campos son opcionales, null-safe

##### 3. Modificaciones en el Controller Cliente

Archivo: `controller/cliente.php`

- En `guardaryeditar`:
  ```php
  $exento_iva = isset($_POST["exento_iva"]) ? 1 : 0;
  $justificacion_exencion_iva = htmlspecialchars(
      trim($_POST["justificacion_exencion_iva"] ?? 'Operación exenta de IVA según artículo 25 Ley 37/1992'),
      ENT_QUOTES, 
      'UTF-8'
  );
  ```

##### 4. Modificaciones en la Vista de Clientes

Archivo: `view/MntClientes/`

- Añadir checkbox para `exento_iva`
- Añadir textarea para `justificacion_exencion_iva`
- JavaScript para mostrar/ocultar justificación según checkbox

##### 5. Modificaciones en Cálculo de Presupuestos

Archivos afectados:
- `controller/impresionpresupuesto_m2_pdf_es.php`
- `models/Presupuesto.php`

**Lógica de cálculo:**

```php
// Al obtener datos del cliente
$cliente_exento_iva = (bool)$rspta_datoscliente["exento_iva"];
$justificacion_iva = $rspta_datoscliente["justificacion_exencion_iva"] ?? 
                     'Operación exenta de IVA según artículo 25 Ley 37/1992';

// En el bucle de líneas de presupuesto
foreach ($datoslineas as $reg) {
    // Si el cliente está exento, forzar IVA a 0
    if ($cliente_exento_iva) {
        $impuesto_articulo = 0;
    } else {
        $impuesto_articulo = floatval($reg["impuesto_articulo"]);
    }
    
    // Calcular importes con el IVA correcto
    $importe_iva = $subtotal_linea * ($impuesto_articulo / 100);
    $total_linea = $subtotal_linea + $importe_iva;
}
```

##### 6. Modificaciones en el PDF

Archivo: `controller/impresionpresupuesto_m2_pdf_es.php`

**Ubicación del texto de justificación:**

```php
// Después de la sección de totales, antes de las observaciones
if ($cliente_exento_iva) {
    $pdf->Ln(5);
    $pdf->SetFont('', 'I', 9); // Cursiva, tamaño 9
    $pdf->SetFillColor(240, 240, 240); // Fondo gris claro
    $pdf->MultiCell(
        190, 
        5, 
        $justificacion_iva, 
        0, 
        'L', 
        true, // Con fondo
        1
    );
    $pdf->Ln(2);
}

// Continuar con observaciones de líneas...
```

**Formato visual sugerido:**
- Fuente: Helvetica, cursiva, 9pt
- Color de fondo: Gris claro (#F0F0F0)
- Ancho: 190mm (ancho completo)
- Alineación: Izquierda
- Espaciado: 5mm antes, 2mm después

#### ✅ Validaciones Requeridas

1. **Base de datos:**
   - ✓ Campo `exento_iva` no puede ser NULL (DEFAULT FALSE)
   - ✓ Campo `justificacion_exencion_iva` tiene valor por defecto

2. **Interfaz de usuario:**
   - ✓ Checkbox visible en el formulario de cliente
   - ✓ Textarea visible solo cuando checkbox activado
   - ✓ Texto por defecto se carga automáticamente

3. **Cálculos:**
   - ✓ Si `exento_iva = TRUE`, IVA siempre 0%, sin excepciones
   - ✓ Si `exento_iva = FALSE`, IVA según configuración de artículo
   - ✓ Subtotales se calculan correctamente en ambos casos

4. **PDF:**
   - ✓ Justificación solo aparece si `exento_iva = TRUE`
   - ✓ Totales muestran IVA 0,00 € correctamente
   - ✓ Texto de justificación legible y bien posicionado

#### 📂 Archivos a Modificar

1. **Base de datos:**
   - `BD/migrations/alter_cliente_exento_iva.sql` (crear)

2. **Modelos:**
   - `models/Clientes.php`

3. **Controllers:**
   - `controller/cliente.php`
   - `controller/impresionpresupuesto_m2_pdf_es.php`

4. **Vistas:**
   - `view/MntClientes/clientes.php` (formulario)
   - `view/MntClientes/clientes.js` (JavaScript)

5. **Documentación:**
   - `docs/presupuestos_20260211.md` (este archivo)

#### 🧪 Casos de Prueba

- [x] Cliente normal (exento_iva = FALSE): IVA se calcula según artículo
- [x] Cliente exento (exento_iva = TRUE): IVA siempre 0%
- [x] PDF con cliente exento muestra justificación
- [x] PDF con cliente normal NO muestra justificación
- [x] Texto de justificación personalizado se muestra correctamente
- [x] Texto vacío o NULL usa el valor por defecto
- [x] Editar cliente: cambiar de exento a normal y viceversa
- [x] Totales se recalculan correctamente al cambiar estado

#### 📝 Notas Legales

- **Artículo 25 Ley 37/1992**: Operaciones intracomunitarias
- El texto por defecto es orientativo, puede personalizarse según:
  - Operaciones intracomunitarias (Art. 25)
  - Exportaciones (Art. 21)
  - Entregas exentas (Art. 20)
  - Organismos internacionales (Art. 22)

#### ⚠️ Consideraciones Importantes

1. **Responsabilidad fiscal**: El cliente es responsable de indicar correctamente su situación fiscal
2. **Auditoría**: Registrar en logs cuando se marca/desmarca exención de IVA
3. **Histórico**: Los presupuestos/facturas ya generados mantienen el IVA que tenían en su momento
4. **Validación**: Considerar validar el CIF del cliente para operaciones intracomunitarias (debe empezar por letra de país UE)

---

**Última actualización**: 14 de febrero de 2026  
**Estado**: ✅ Implementado y Probado  
**Rama**: cliente0_presupuesto  
**Commits**: fix(punto17), style(punto17), style(pdf)  
**Archivo**: `docs/presupuestos_20260211.md`

---

### 18. Mostrar Número de Cuenta Bancaria con Forma de Pago TRANSFERENCIA 🔧 **PENDIENTE**

**Fecha**: 14 de febrero de 2026  
**Prioridad**: Alta  
**Tipo**: Nueva funcionalidad  
**Origen**: Petición del cliente en reunión de puesta en marcha

#### 📋 Situación Actual

Cuando un presupuesto tiene como forma de pago "TRANSFERENCIA", el PDF no muestra el número de cuenta bancaria de la empresa donde el cliente debe realizar el pago. Esto obliga a enviar esta información por separado o manualmente.

#### 🎯 Cambios Requeridos

1. **En la tabla `empresa`:**
   - Verificar si existe campo `cuenta_bancaria_empresa` o `iban_empresa`
   - Si no existe, crear campo para almacenar número de cuenta bancaria

2. **En la pantalla de gestión de empresas:**
   - Campo de texto para ingresar número de cuenta bancaria (IBAN)
   - Validación de formato IBAN español (ES + 22 dígitos)
   - El campo es opcional pero recomendado

3. **En el PDF del presupuesto:**
   - **Condición**: Solo si `forma_pago = 'TRANSFERENCIA'` o `nombre_forma_pago LIKE '%TRANSFERENCIA%'`
   - **Ubicación**: En la sección de "FORMA DE PAGO", después de la descripción de pago
   - **Formato**: 
     ```
     FORMA DE PAGO: Transferencia Bancaria, Anticipo del 50%
     
     Número de cuenta: ES12 1234 5678 9012 3456 7890
     ```
   - **Estilo sugerido**: 
     - Fuente: Helvetica, negrita, tamaño 9pt
     - Color de fondo: Gris muy claro (#F8F9FA)
     - Con borde sutil

4. **Comportamiento:**
   - Si NO es TRANSFERENCIA → No mostrar número de cuenta
   - Si ES TRANSFERENCIA pero no hay cuenta en BD → Mostrar aviso o no mostrar nada
   - Si ES TRANSFERENCIA y hay cuenta → Mostrar cuenta formateada

#### 💻 Implementación Técnica Requerida

##### 1. Verificación / Migración de Base de Datos

```sql
-- Verificar si existe el campo
SELECT COLUMN_NAME 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_NAME = 'empresa' 
AND COLUMN_NAME IN ('cuenta_bancaria_empresa', 'iban_empresa', 'numero_cuenta_empresa');

-- Si no existe, crear el campo
ALTER TABLE empresa 
ADD COLUMN cuenta_bancaria_empresa VARCHAR(34) 
DEFAULT NULL 
COMMENT 'IBAN de la cuenta bancaria de la empresa para transferencias';

-- Índice opcional para búsquedas
CREATE INDEX idx_cuenta_bancaria ON empresa(cuenta_bancaria_empresa);
```

##### 2. Modificaciones en el Modelo Empresa

Archivo: `models/Empresas.php`

- Actualizar método `insert_empresa()` para incluir `cuenta_bancaria_empresa`
- Actualizar método `update_empresa()` para incluir `cuenta_bancaria_empresa`
- El campo es opcional, null-safe

##### 3. Modificaciones en el Controller Empresa

Archivo: `controller/empresas.php`

- En `guardaryeditar`:
  ```php
  $cuenta_bancaria = !empty($_POST["cuenta_bancaria_empresa"]) 
      ? strtoupper(str_replace(' ', '', trim($_POST["cuenta_bancaria_empresa"]))) 
      : null;
  
  // Validación básica IBAN español (opcional)
  if (!empty($cuenta_bancaria)) {
      if (!preg_match('/^ES\d{22}$/', $cuenta_bancaria)) {
          echo json_encode([
              'success' => false,
              'message' => 'El formato del IBAN debe ser: ES + 22 dígitos'
          ]);
          exit;
      }
  }
  ```

##### 4. Modificaciones en la Vista de Empresas

Archivo: `view/MntEmpresas/`

- Añadir campo de texto para `cuenta_bancaria_empresa`
- Placeholder: "ES12 1234 5678 9012 3456 7890"
- Opcional: Máscara de input para formato IBAN
- Tooltip explicativo: "IBAN de la cuenta para pagos por transferencia"

##### 5. Modificaciones en el PDF

Archivo: `controller/impresionpresupuesto_m2_pdf_es.php`

**Ubicación**: En la sección de Forma de Pago (alrededor de línea ~1260-1300)

```php
// FORMA DE PAGO
if (!empty($datos_presupuesto['nombre_pago'])) {
    $pdf->Ln(6);
    
    // Título "FORMA DE PAGO:"
    $pdf->SetFont('helvetica', 'B', 9);
    $pdf->SetTextColor(52, 73, 94);
    $pdf->Cell(40, 5, 'FORMA DE PAGO:', 0, 0, 'L');
    
    // Descripción de forma de pago
    $pdf->SetFont('helvetica', '', 9);
    $pdf->SetTextColor(0, 0, 0);
    $frase_pago = /* ... construcción de texto existente ... */;
    $pdf->MultiCell(0, 5, $frase_pago, 0, 'L');
    
    // *** NUEVO: Mostrar número de cuenta si es TRANSFERENCIA ***
    $forma_pago_lower = strtolower($datos_presupuesto['nombre_forma_pago'] ?? '');
    $es_transferencia = (strpos($forma_pago_lower, 'transferencia') !== false);
    
    if ($es_transferencia && !empty($datos_empresa['cuenta_bancaria_empresa'])) {
        $pdf->Ln(3);
        
        // Formatear IBAN: ES12 1234 5678 9012 3456 7890
        $iban = $datos_empresa['cuenta_bancaria_empresa'];
        $iban_formateado = wordwrap($iban, 4, ' ', true);
        
        // Caja con fondo gris claro
        $pdf->SetFillColor(248, 249, 250); // Gris muy claro
        $pdf->SetDrawColor(220, 220, 220); // Borde gris suave
        
        // Contenedor
        $y_inicio = $pdf->GetY();
        $pdf->Rect(8, $y_inicio, 194, 9, 'FD'); // Fondo + Borde
        
        // Texto dentro del contenedor
        $pdf->SetXY(8, $y_inicio + 2);
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->SetTextColor(52, 73, 94);
        $pdf->Cell(35, 5, 'Número de cuenta:', 0, 0, 'L');
        
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(0, 5, $iban_formateado, 0, 1, 'L');
        
        $pdf->SetY($y_inicio + 9);
        
        // Restaurar colores
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetDrawColor(0, 0, 0);
    }
}
```

**Formato visual:**
- Fondo: Gris claro (#F8F9FA)
- Borde: Gris suave (#DCDCDC)
- Altura: 9mm
- Ancho: Todo el ancho disponible (194mm)
- Espaciado: 3mm antes del contenedor
- Label "Número de cuenta:": Helvetica, Bold, 8pt, color oscuro
- IBAN: Helvetica, Bold, 9pt, color negro
- Formato IBAN: Grupos de 4 dígitos separados por espacios

##### 6. Modificaciones en la consulta SQL del PDF

Archivo: `models/ImpresionPresupuesto.php` o donde se obtengan datos de empresa

Asegurar que el SELECT incluya:
```php
$sql = "SELECT 
    e.id_empresa,
    e.nombre_comercial_empresa,
    e.cuenta_bancaria_empresa,  -- *** NUEVO CAMPO ***
    /* ... otros campos ... */
FROM empresa e
WHERE e.id_empresa = ?";
```

#### ✅ Validaciones Requeridas

1. **Base de datos:**
   - ✓ Campo `cuenta_bancaria_empresa` puede ser NULL
   - ✓ Longitud máxima 34 caracteres (IBAN estándar internacional)

2. **Interfaz de usuario:**
   - ✓ Campo opcional en formulario de empresa
   - ✓ Validación formato IBAN al guardar (opcional pero recomendada)
   - ✓ Conversión automática a mayúsculas
   - ✓ Eliminación de espacios al guardar

3. **PDF:**
   - ✓ Solo mostrar si `forma_pago` contiene "TRANSFERENCIA"
   - ✓ Solo mostrar si `cuenta_bancaria_empresa` NO está vacío
   - ✓ IBAN formateado con espacios cada 4 caracteres
   - ✓ Estilo consistente con resto del documento

#### 📂 Archivos a Modificar

1. **Base de datos:**
   - `BD/migrations/alter_empresa_cuenta_bancaria.sql` (crear)

2. **Modelos:**
   - `models/Empresas.php`
   - `models/ImpresionPresupuesto.php` (verificar SELECT)

3. **Controllers:**
   - `controller/empresas.php`
   - `controller/impresionpresupuesto_m2_pdf_es.php`

4. **Vistas:**
   - `view/MntEmpresas/empresas.php` (formulario)
   - `view/MntEmpresas/empresas.js` (JavaScript, si aplica)

5. **Documentación:**
   - `docs/presupuestos_20260211.md` (este archivo)

#### 🧪 Casos de Prueba

- [ ] Presupuesto con forma de pago TRANSFERENCIA + cuenta bancaria en BD
- [ ] Presupuesto con forma de pago TRANSFERENCIA + SIN cuenta bancaria
- [ ] Presupuesto con forma de pago METÁLICO (no debe mostrar cuenta)
- [ ] Presupuesto con forma de pago TARJETA (no debe mostrar cuenta)
- [ ] IBAN se muestra formateado correctamente (espacios cada 4 dígitos)
- [ ] Editar empresa: agregar/modificar/eliminar cuenta bancaria
- [ ] Validación de formato IBAN al guardar empresa

#### 💡 Mejoras Opcionales (Futuro)

1. **Múltiples cuentas bancarias**:
   - Algunas empresas tienen varias cuentas (diferentes bancos)
   - Permitir seleccionar cuenta principal o por defecto

2. **Códigos QR**:
   - Generar código QR para pago por Bizum o transferencia rápida
   - Incluir QR en el PDF junto al número de cuenta

3. **Validación IBAN avanzada**:
   - Validar dígito de control del IBAN
   - Identificar banco según código (opcional)

4. **Diferentes formas de pago**:
   - "TRANSFERENCIA 50% + TRANSFERENCIA 50%" → Mostrar cuenta
   - "TRANSFERENCIA + METÁLICO" → Mostrar cuenta
   - Detectar palabra clave "TRANSFERENCIA" en cualquier parte

#### ⚠️ Consideraciones Importantes

1. **Seguridad**: El IBAN es información sensible pero necesaria para cobros
2. **Privacidad**: Solo mostrar en PDFs de cliente, no en listados internos
3. **Multi-empresa**: Si el sistema gestiona varias empresas, cada una tendrá su IBAN
4. **Histórico**: Los PDFs generados mantienen la cuenta que tenían en ese momento
5. **Actualización**: Si se cambia la cuenta bancaria, solo afecta a nuevos presupuestos

#### 📝 Notas de Implementación

- El campo debe almacenarse **sin espacios** en BD (ej: `ES1212341234123412341234`)
- Al mostrar en PDF, formatear **con espacios** (ej: `ES12 1234 1234 1234 1234 1234`)
- Detectar "TRANSFERENCIA" de forma case-insensitive
- Si hay varias formas de pago combinadas, mostrar si alguna es transferencia

---

**Última actualización**: 14 de febrero de 2026  
**Estado**: 🔧 Pendiente de implementación  
**Prioridad**: Alta  
**Origen**: Reunión de puesta en marcha con cliente  
**Archivo**: `docs/presupuestos_20260211.md`
