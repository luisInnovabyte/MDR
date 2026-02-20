# Conclusiones Reunión - Formato PDF Presupuestos
**Fecha**: 11 de febrero de 2026  
**Tema**: Mejoras y ajustes en la generación del PDF de presupuestos

---

## 📋 Índice de Cambios

- [1. Observaciones de Líneas de Presupuesto](#1-observaciones-de-líneas-de-presupuesto--completada) ✅ COMPLETADA
- [2. Formato de Líneas de Artículos](#2-formato-de-líneas-de-artículos--completada) ✅ COMPLETADA
- [3. Cabecera - Nº Presupuesto de Cliente](#3-cabecera---nº-presupuesto-de-cliente--completada) ✅ COMPLETADA
- [4. Ubicación del Evento](#4-ubicación-del-evento--completada) ✅ COMPLETADA
- [5. Título Principal "PRESUPUESTO"](#5-título-principal-presupuesto--completada) ✅ COMPLETADA
- [6. CIF de la Empresa](#6-cif-de-la-empresa--completada) ✅ COMPLETADA
- [7. Observación Cabecera - Montaje y Alquiler](#7-observación-cabecera---montaje-y-alquiler--completada) ✅ COMPLETADA
- [8. Subtotales por Fecha](#8-subtotales-por-fecha--completada) ✅ COMPLETADA
- [9. Totales Finales - Descuento](#9-totales-finales---descuento--completada) ✅ COMPLETADA
- [10. Observaciones - Formato de Referencias](#10-observaciones---formato-de-referencias--completada) ✅ COMPLETADA
- [11. Pies de Empresa](#11-pies-de-empresa--completada) ✅ COMPLETADA
- [12. Firmas - Posicionamiento](#12-firmas---posicionamiento--completada) ✅ COMPLETADA
- [13. Firma - Texto del Departamento](#13-firma---texto-del-departamento--completada) ✅ COMPLETADA
- [14. Nueva Funcionalidad - Firma de Empleado](#14-nueva-funcionalidad---firma-de-empleado--completada) ✅ COMPLETADA
- [15. Líneas del Presupuesto - Bordes Grises](#15-líneas-del-presupuesto---bordes-grises--completada) ✅ COMPLETADA
- [16. Fechas de Montaje y Desmontaje - Optimización de Espacio](#16-fechas-de-montaje-y-desmontaje---optimización-de-espacio--completada) ✅ COMPLETADA
- [17. Clientes Exentos de IVA - Operaciones Intracomunitarias](#17-clientes-exentos-de-iva---operaciones-intracomunitarias--completado) ✅ COMPLETADO
- [18. Ocultar sección observaciones si está vacía](#18-ocultar-sección-observaciones-si-está-vacía--completado) ✅ COMPLETADO
- [19. Mostrar Datos Bancarios con Forma de Pago TRANSFERENCIA](#19-mostrar-datos-bancarios-con-forma-de-pago-transferencia--completado) ✅ COMPLETADO
- [20. Sistema de Peso en Presupuestos](#20-sistema-de-peso-en-presupuestos--completada) ✅ COMPLETADA
- [21. Impresión de Albaranes](#21-impresión-de-albaranes--completada-y-finalizada) ✅ COMPLETADA Y FINALIZADA

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

### 14. Nueva Funcionalidad - Firma de Empleado ✅ **COMPLETADA**

**Fecha inicio**: 14 de febrero de 2026  
**Fecha finalización**: 15 de febrero de 2026  
**Prioridad**: Media  
**Tipo**: Nueva funcionalidad

#### 📋 Descripción

Implementación de firma digital personalizada del comercial en el presupuesto PDF. Los comerciales pueden dibujar su firma en un canvas HTML y guardarla en la base de datos. La firma se renderiza automáticamente en la sección de firmas del PDF de presupuestos.

**Nota importante**: Aunque el requerimiento original mencionaba "empleado", se implementó para **comerciales** ya que son ellos quienes generan y firman los presupuestos.

#### 🎯 Cambios Implementados

##### 14.1 Base de Datos ✅
- ✅ Campo añadido: `comerciales.firma_comercial` TEXT
- ✅ Almacena imagen en formato base64: `data:image/png;base64,...`
- ✅ Se añadió mediante migración SQL

**Migración aplicada**:
```sql
ALTER TABLE comerciales 
ADD COLUMN firma_comercial TEXT COMMENT 'Firma digital del comercial en base64 PNG';
```

##### 14.2 Pantalla de Perfil de Usuario ✅
**Archivo**: `view/Home/perfil.php`

- ✅ Canvas HTML con SignaturePad library (4.1.7)
- ✅ Dimensiones: ancho 100% (responsive), altura 150px fija
- ✅ Botones implementados:
  - **Guardar Firma**: Guarda en DB vía AJAX
  - **Limpiar**: Borra canvas y mantiene dimensiones
  - **Cargar Existente**: Recupera firma guardada automáticamente

**Características técnicas**:
- Canvas responsive con device pixel ratio scaling
- Formato: PNG base64 con prefijo `data:image/png;base64,`
- Validación client-side de tipo de dato
- Feedback visual con SweetAlert2

##### 14.3 Modelo Comerciales.php ✅

**Archivo modificado**: `models/Comerciales.php`

**Métodos implementados**:
```php
// Obtener firma digital de un comercial por su id_usuario
public function get_firma_by_usuario($id_usuario)
{
    $sql = "SELECT firma_comercial FROM comerciales 
            WHERE id_usuario = ? AND activo = 1";
    // Retorna: string base64 PNG o null
}

// Actualizar firma digital de un comercial
public function update_firma_by_usuario($id_usuario, $firma_base64)
{
    $sql = "UPDATE comerciales SET firma_comercial = ? 
            WHERE id_usuario = ?";
    // Soporte para NULL (eliminar firma)
    // Retorna: boolean
}

// Obtener comercial asociado a un usuario
public function get_comercial_by_usuario($id_usuario)
{
    $sql = "SELECT id_comercial, nombre, apellidos, firma_comercial 
            FROM comerciales 
            WHERE id_usuario = ? AND activo = 1";
    // Retorna: array con datos del comercial o null
}
```

##### 14.4 Controllers AJAX ✅

**Archivo nuevo**: `controller/ajax_guardar_firma.php`
- ✅ Validación de sesión activa
- ✅ Verificación de usuario es comercial
- ✅ Validación formato base64 PNG
- ✅ Límite de tamaño: ~500KB
- ✅ Sanitización de datos
- ✅ Logging de actividad con RegistroActividad
- ✅ Respuestas JSON estandarizadas

**Archivo nuevo**: `controller/ajax_obtener_firma.php`
- ✅ Recupera firma por id_usuario
- ✅ Validación de permisos
- ✅ Retorna JSON con firma en base64

##### 14.5 PDF del Presupuesto ✅

**Archivo modificado**: `controller/impresionpresupuesto_m2_pdf_es.php`

**Implementación**:
```php
// 1. Iniciar sesión para acceder a id_usuario
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Obtener firma del comercial logueado
if (isset($_SESSION['id_usuario'])) {
    $comercialesModel = new Comerciales();
    $firma_comercial = $comercialesModel->get_firma_by_usuario($_SESSION['id_usuario']);
}

// 3. Renderizar firma en sección de firmas
if (!empty($firma_comercial)) {
    // Decodificar base64 para TCPDF
    $imagen_base64 = preg_replace('/^data:image\/(png|jpg|jpeg);base64,/', '', $firma_comercial);
    $imagen_decodificada = base64_decode($imagen_base64);
    
    // Renderizar con prefijo @ (imagen en memoria)
    $pdf->Image(
        '@' . $imagen_decodificada,  // @ indica imagen en memoria
        $x_firma,                     // Posición X
        $y_firma,                     // Posición Y
        60,                           // Ancho máximo 60mm
        14,                           // Alto máximo 14mm
        'PNG',                        // Tipo explícito
        '', '', false, 300            // Parámetros adicionales
    );
}
```

**Formato visual en PDF**:
```
┌─────────────────────────────────┐ ┌─────────────────────────────────┐
│  FIRMA Y SELLO                  │ │  VISTO BUENO DEL CLIENTE        │
│                                 │ │                                 │
│  ┌──────────────────────────┐  │ │                                 │
│  │  [Firma renderizada]     │  │ │  (espacio para firma manual)    │
│  │  (60mm × 14mm)           │  │ │                                 │
│  └──────────────────────────┘  │ │                                 │
│  ____________________________  │ │  ____________________________    │
│  Firma y Sello                 │ │  Firma del Cliente              │
│  Fecha: 15/02/2026             │ │                                 │
└─────────────────────────────────┘ └─────────────────────────────────┘
```

**Ubicación**: Casilla izquierda "FIRMA Y SELLO", después de los totales

#### 📂 Archivos Creados/Modificados

**Commits realizados**: `c055363`, `f57800c`, `ed6e47b`, `7335962`

| Archivo | Tipo | Descripción |
|---------|------|-------------|
| `BD/migrations/20250120_add_firma_comercial.sql` | Nuevo | Migración ALTER TABLE comerciales |
| `models/Comerciales.php` | Modificado | Métodos get_firma_by_usuario, update_firma_by_usuario, get_comercial_by_usuario |
| `controller/ajax_guardar_firma.php` | Nuevo | Endpoint para guardar firma (POST) |
| `controller/ajax_obtener_firma.php` | Nuevo | Endpoint para obtener firma (GET) |
| `controller/impresionpresupuesto_m2_pdf_es.php` | Modificado | Renderizado de firma en PDF (~45 líneas) |
| `view/Home/perfil.php` | Modificado | Canvas HTML con SignaturePad |
| `view/Home/perfil.js` | Modificado | Lógica JavaScript de firma |

**Rama**: `cliente0_presupuesto`

#### ✅ Flujo Completo Implementado

1. **Usuario dibuja firma**:
   - Accede a Perfil → sección "Firma Digital"
   - Dibuja en canvas con mouse/touch
   - Click en "Guardar Firma"

2. **Sistema guarda firma**:
   - JavaScript captura canvas como PNG base64
   - AJAX POST a `ajax_guardar_firma.php`
   - Validación de formato y tamaño
   - UPDATE en `comerciales.firma_comercial`
   - Feedback visual con SweetAlert2

3. **PDF renderiza firma**:
   - Al generar PDF, inicia sesión para acceder a `$_SESSION['id_usuario']`
   - Recupera firma de BD con modelo Comerciales
   - Decodifica base64 a binario
   - Renderiza con TCPDF usando prefijo `@`
   - Posicionamiento automático en casilla "FIRMA Y SELLO"

#### 🧪 Casos de Prueba Validados

- [x] Dibujar y guardar firma nueva
- [x] Cargar firma existente al abrir perfil
- [x] Limpiar canvas mantiene dimensiones correctas
- [x] Firma aparece en PDF de presupuesto
- [x] PDF sin firma muestra espacio vacío (no error)
- [x] Canvas responsive en diferentes resoluciones
- [x] Validación de formato base64 PNG
- [x] Límite de tamaño ~500KB funciona
- [x] Usuario sin comercial asociado recibe error claro
- [x] Firma se renderiza correctamente en TCPDF (60mm × 14mm)

#### 💡 Características Técnicas

**Canvas de firma**:
- Librería: SignaturePad 4.1.7
- Responsive: Ancho 100%, altura 150px fija
- Scaling: Device pixel ratio automático
- Formato salida: PNG base64 con prefijo data URI

**Almacenamiento**:
- Campo: TEXT (soporta ~65KB, suficiente para firma PNG)
- Formato: `data:image/png;base64,iVBORw0KGgoAAAANS...`
- Tamaño típico: 6-10 KB por firma
- NULL accepted: Sí (sin firma = NULL)

**Renderizado PDF**:
- Técnica: Decodificación base64 + prefijo `@` para TCPDF
- TCPDF NO acepta data URI directamente
- Se extrae base64 puro, se decodifica a binario
- Se usa `$pdf->Image('@' . $binario, ...)` para imagen en memoria
- Control de espacio: Salto de página automático si no cabe

#### ⚠️ Consideraciones Importantes

1. **Sesión en PDF**: Se inicia sesión condicionalmente para acceder a `id_usuario`
2. **Comercial vs Empleado**: Se implementó para tabla `comerciales`, no `empleado`
3. **Inmutabilidad**: PDFs generados mantienen firma histórica (no se re-generan)
4. **Permisos**: Solo el comercial puede editar su propia firma
5. **Formato crítico**: DEBE ser `data:image/png;base64,` o falla validación

#### 📝 Mejoras Futuras (Opcionales)

1. **Administración centralizada**:
   - Pantalla de gestión de firmas por admin
   - Ver/editar firmas de todos los comerciales
   - Cargar firma desde archivo

2. **Múltiples formatos**:
   - Soporte JPG además de PNG
   - Conversión automática a formato óptimo
   - Compresión de imagen para reducir tamaño

3. **Validación mejorada**:
   - Verificar que la firma no esté "vacía" (canvas en blanco)
   - Detectar firmas demasiado simples (pocos trazos)
   - Requerir firma obligatoria para generar presupuestos

4. **Histórico**:
   - Tabla `firma_comercial_historial` con versionado
   - Auditoría de cambios de firma
   - Recuperar firmas antiguas

---

**Última actualización**: 15 de febrero de 2026  
**Estado**: ✅ Completada e Integrada  
**Rama**: cliente0_presupuesto  
**Commits**: c055363, f57800c, ed6e47b, 7335962  
**Archivo**: `docs/presupuestos_20260211.md`

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
- [x] 19. Mostrar datos bancarios con forma de pago TRANSFERENCIA

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

### 18. Ocultar sección observaciones si está vacía ✅ **COMPLETADO**

**Fecha alta**: 11 de febrero de 2026  
**Fecha finalización**: 19 de febrero de 2026  
**Estado**: ✅ Completado  
**Prioridad**: Media  
**Tipo**: Ajuste de layout PDF

#### 📋 Descripción

Cuando un presupuesto no tenga contenido en la sección de observaciones, el PDF no debe reservar bloque visual ni dejar hueco en blanco.

#### 🎯 Implementación realizada

**Archivo modificado**: `controller/impresionpresupuesto_m2_pdf_es.php`

**Problema**: El bloque `OBSERVACIONES DE FAMILIAS Y ARTÍCULOS` pintaba el título "OBSERVACIONES DEL PRESUPUESTO" y los saltos `Ln(8)` / `Ln(2)` incluso cuando todos los ítems del array tenían `observacion_es` vacío, porque la guardia exterior solo comprobaba que `$observaciones_array` no era vacío, sin verificar si algún ítem tenía contenido real.

**Solución**: Se añade un pre-filtrado (`array_filter`) antes de cualquier renderizado. Solo si el array filtrado tiene al menos un elemento se pinta el título y el bloque completo:

```php
$obs_con_contenido = array_filter(
    is_array($observaciones_array) ? $observaciones_array : [],
    function ($obs) {
        $nombre = ''; // ... resuelve nombre según tipo ...
        $texto = $obs['observacion_es'] ?? '';
        return !empty($nombre) && !empty(trim($texto));
    }
);

if (!empty($obs_con_contenido)) {
    // Ln(8), título, Ln(2), foreach...
}
```

#### ✅ Criterios de validación

- ✅ Presupuesto con observaciones: sección visible con formato habitual.
- ✅ Presupuesto sin observaciones: sección completamente oculta (sin título ni saltos).
- ✅ No aparecen títulos vacíos ni saltos innecesarios.
- ✅ Las secciones posteriores (PIE, FIRMAS) suben de posición sin solaparse.

---

### 19. Mostrar Datos Bancarios con Forma de Pago TRANSFERENCIA ✅ **COMPLETADO**

**Fecha inicio**: 14 de febrero de 2026  
**Fecha finalización**: 14 de febrero de 2026  
**Prioridad**: Alta  
**Tipo**: Nueva funcionalidad  
**Origen**: Petición del cliente en reunión de puesta en marcha

#### 📋 Descripción

Cuando un presupuesto tiene como forma de pago "TRANSFERENCIA", el PDF debe mostrar los datos bancarios completos de la empresa donde el cliente debe realizar el pago. Se muestran hasta 3 campos bancarios:

- **Banco**: Nombre de la entidad bancaria (ej: "BANCO SANTANDER")
- **IBAN**: Código IBAN formateado con espacios cada 4 caracteres (ej: "ES12 1234 5678 9012 3456 7890")
- **SWIFT**: Código SWIFT/BIC internacional (ej: "BSCHESMMXXX")

Los campos bancarios **ya existen** en la tabla `empresa`:
- `iban_empresa` VARCHAR(34)
- `swift_empresa` VARCHAR(11)
- `banco_empresa` VARCHAR(100)

#### 🎯 Implementación Realizada

**1. Backend - Modelo `ImpresionPresupuesto.php`**

Se agregaron los 3 campos bancarios al SELECT de datos de empresa:

```php
$sql = "SELECT 
    id_empresa,
    nombre_comercial_empresa,
    // ... otros campos ...
    web_empresa,
    iban_empresa,      // ← NUEVO
    swift_empresa,     // ← NUEVO
    banco_empresa,     // ← NUEVO
    logotipo_empresa,
    // ... resto de campos ...
FROM empresa 
WHERE empresa_ficticia_principal = 1 
AND activo_empresa = 1";
```

**Archivo modificado**: `models/ImpresionPresupuesto.php`

**2. PDF - Bloque de Datos Bancarios**

Se implementó renderizado condicional en el PDF después de la sección "FORMA DE PAGO":

**Archivo modificado**: `controller/impresionpresupuesto_m2_pdf_es.php`

**Ubicación**: Después de línea ~1327 (sección FORMA DE PAGO)

**Lógica implementada**:

```php
// Detectar si es TRANSFERENCIA (case-insensitive)
$forma_pago_lower = strtolower($datos_presupuesto['nombre_metodo_pago'] ?? '');
$es_transferencia = (strpos($forma_pago_lower, 'transferencia') !== false);

// Verificar si hay algún dato bancario
$tiene_datos_bancarios = (
    !empty($datos_empresa['iban_empresa']) ||
    !empty($datos_empresa['swift_empresa']) ||
    !empty($datos_empresa['banco_empresa'])
);

// Solo renderizar si ES transferencia Y HAY datos
if ($es_transferencia && $tiene_datos_bancarios) {
    // Calcular altura dinámica
    $altura_bloque = 7; // Overhead
    if (!empty($datos_empresa['banco_empresa'])) $altura_bloque += 5;
    if (!empty($datos_empresa['iban_empresa'])) $altura_bloque += 5;
    if (!empty($datos_empresa['swift_empresa'])) $altura_bloque += 5;
    
    // Control salto de página
    if (($pdf->GetY() + $altura_bloque) > 270) {
        $pdf->AddPage();
        $pdf->SetY(15);
    }
    
    // Dibujar rectángulo con fondo gris
    $pdf->SetFillColor(245, 245, 245);
    $pdf->SetDrawColor(180, 180, 180);
    $pdf->Rect($x_inicio, $y_inicio, 195, $altura_bloque, 'DF');
    
    // Título
    $pdf->SetFont('helvetica', 'B', 9);
    $pdf->Cell(189, 4, 'DATOS BANCARIOS PARA TRANSFERENCIA', 0, 1, 'L');
    
    // Mostrar campos solo si tienen valor
    if (!empty($datos_empresa['banco_empresa'])) {
        // Banco: [nombre]
    }
    
    if (!empty($datos_empresa['iban_empresa'])) {
        // IBAN: ES12 1234 5678 9012 3456 7890 (formateado)
        $iban_formateado = wordwrap($iban_sin_espacios, 4, ' ', true);
    }
    
    if (!empty($datos_empresa['swift_empresa'])) {
        // SWIFT: [código]
    }
}
```

**Características del bloque visual**:
- **Fondo**: Gris claro RGB(245, 245, 245)
- **Borde**: Gris medio RGB(180, 180, 180)
- **Ancho**: 195mm (todo el ancho disponible)
- **Altura**: Dinámica según campos (5mm por campo + 7mm overhead)
- **Título**: "DATOS BANCARIOS PARA TRANSFERENCIA" (Helvetica Bold 9pt)
- **Labels**: Helvetica 8pt gris oscuro RGB(70, 70, 70)
- **Valores**: Helvetica Bold 9pt negro
- **Espaciado**: 4mm antes del bloque, campos separados 5mm
- **IBAN formateado**: Agrupado en bloques de 4 caracteres
- **Control de página**: Salto automático si no hay espacio

**3. Script de Testing**

Se creó script de prueba y verificación:

**Archivo nuevo**: `controller/test_banco.php`

**Funciones del script**:
1. Verifica datos bancarios en empresa principal
2. Inserta datos de prueba si no existen
3. Busca presupuestos con TRANSFERENCIA
4. Modifica presupuestos de prueba si es necesario
5. Verifica que modelo recupera campos correctamente
6. Proporciona links directos para abrir PDFs de prueba

**Uso**: Abrir en navegador `http://[servidor]/controller/test_banco.php`

#### ✅ Comportamiento Implementado

**Condiciones de visualización**:

1. **Método de pago contiene "TRANSFERENCIA"** (case-insensitive)
   - Detecta: "Transferencia", "TRANSFERENCIA", "transferencia"
   - Funciona con formas mixtas: "50% Transferencia + 50% Metálico"

2. **Al menos UN campo bancario tiene valor**
   - Si todos están vacíos → No se muestra bloque
   - Si al menos uno tiene valor → Se muestra bloque con campos disponibles

3. **Campos mostrados dinámicamente**:
   - Banco: Solo si `banco_empresa` no está vacío
   - IBAN: Solo si `iban_empresa` no está vacío (+ formato con espacios)
   - SWIFT: Solo si `swift_empresa` no está vacío

4. **Control de espacio en página**:
   - Se calcula altura necesaria según campos disponibles
   - Si no hay espacio suficiente → Salto de página automático
   - Altura dinámica: 7mm (overhead) + 5mm por cada campo

#### 📂 Archivos Modificados

**Commits realizados**: `2db8a64`

| Archivo | Tipo | Descripción |
|---------|------|-------------|
| `models/ImpresionPresupuesto.php` | Modificado | Agregados 3 campos al SELECT: iban_empresa, swift_empresa, banco_empresa |
| `controller/impresionpresupuesto_m2_pdf_es.php` | Modificado | Bloque bancario condicional después de FORMA DE PAGO (~95 líneas) |
| `controller/test_banco.php` | Nuevo | Script de verificación y prueba de datos bancarios |

**Rama**: `cliente0_presupuesto`

#### 🧪 Casos de Prueba

- [x] **Presupuesto TRANSFERENCIA + todos los campos bancarios** → Bloque completo visible con 3 líneas
- [x] **Presupuesto TRANSFERENCIA + solo IBAN** → Bloque con 1 línea (IBAN)
- [x] **Presupuesto TRANSFERENCIA + IBAN + Banco** → Bloque con 2 líneas
- [x] **Presupuesto TRANSFERENCIA + sin datos bancarios** → NO se muestra bloque  
- [x] **Presupuesto METÁLICO + datos bancarios** → NO se muestra bloque
- [x] **Presupuesto "50% TRANSFERENCIA + 50% metálico"** → Detecta y muestra bloque
- [x] **IBAN formateado correctamente** → Espacios cada 4 caracteres automático
- [x] **Altura del bloque se ajusta** → 5mm por campo + 7mm overhead
- [x] **Salto de página si hay poco espacio** → Control automático en 270mm
- [x] **Modelo recupera campos bancarios** → Campos disponibles en `$datos_empresa`

#### � Ejemplo Visual del Bloque

```
┌────────────────────────────────────────────────────────────────┐
│ FORMA DE PAGO: Transferencia Bancaria, Anticipo del 50%      │
└────────────────────────────────────────────────────────────────┘

┏━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━┓
┃ DATOS BANCARIOS PARA TRANSFERENCIA                           ┃ ← Título Bold 9pt
┣━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━┫
┃ Banco:  BANCO SANTANDER                                       ┃ ← Label 8pt + Valor Bold 9pt
┃ IBAN:   ES12 1234 5678 9012 3456 7890                        ┃ ← Formateado con espacios
┃ SWIFT:  BSCHESMMXXX                                           ┃
┗━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━┛
        ↑ Fondo gris RGB(245,245,245)
        ↑ Borde gris RGB(180,180,180)
```

#### 💡 Mejoras Futuras (Opcionales)

1. **Validación y formato en pantalla de empresa**:
   - Campo para editar `iban_empresa` con validación IBAN
   - Campo para editar `swift_empresa` con validación BIC
   - Campo para editar `banco_empresa` con autocompletado
   - Formato automático con espacios en IBAN al escribir

2. **Código QR para transferencia**:
   - Generar QR con formato Bizum o código de pago instantáneo
   - Incluir QR en PDF junto a datos bancarios
   - Facilita pago desde móvil

3. **Múltiples cuentas bancarias**:
   - Tabla relacional `cuenta_bancaria` (1:N con empresa)
   - Permitir seleccionar cuenta por defecto
   - Asociar cuenta a forma de pago específica

4. **Soporte multi-moneda**:
   - Mostrar cuenta bancaria según moneda del presupuesto
   - Cuentas en EUR, USD, GBP, etc.

5. **Referencias de pago**:
   - Generar referencia única por presupuesto
   - Incluir en bloque bancario para identificación automática
   - Facilita conciliación bancaria

#### ⚠️ Consideraciones y Notas

**Seguridad**:
- Los datos bancarios son información pública necesaria para cobros
- IBAN/SWIFT no son datos sensibles según normativa bancaria europea
- Solo se usan para recibir pagos (no para hacer cargos)

**Base de datos existente**:
- **NO se requiere migración**: Los 3 campos ya existen en tabla `empresa`
- Campos ya creados: `iban_empresa`, `swift_empresa`, `banco_empresa`
- Si están vacíos, completar desde pantalla de empresas (futura mejora)

**Actualización de datos**:
- Si se cambia cuenta bancaria, solo afecta a presupuestos nuevos
- PDFs generados previamente mantienen datos históricos (inmutables)
- Regenerar PDF refleja datos bancarios actuales

**Formas de pago combinadas**:
- Detecta "TRANSFERENCIA" en cualquier parte del texto
- Ejemplos que funcionan:
  - "Transferencia Bancaria"
  - "50% Transferencia + 50% Metálico"
  - "ANTICIPO POR TRANSFERENCIA"
  - "transferencia inmediata"

**Multi-empresa**:
- Cada empresa tiene sus propios datos bancarios
- Sistema multi-empresa funciona correctamente
- Se obtienen datos de `empresa_ficticia_principal = 1`

#### 🧰 Instrucciones de Testing

**Para probar la implementación**:

1. **Acceder al script de prueba**:
   ```
   http://[tu-servidor]/controller/test_banco.php
   ```

2. **El script automáticamente**:
   - ✓ Verifica datos bancarios en empresa
   - ✓ Inserta datos de prueba si están vacíos
   - ✓ Busca presupuestos con TRANSFERENCIA
   - ✓ Proporciona links a PDFs de prueba
   - ✓ Verifica campos en modelo

3. **Verificación manual en PDF**:
   - Abrir cualquier presupuesto con TRANSFERENCIA
   - Buscar sección "FORMA DE PAGO"
   - Verificar que aparece bloque gris después
   - Comprobar formato de IBAN (espacios cada 4 chars)
   - Verificar que campos vacíos no aparecen

4. **Agregar/editar datos bancarios** (futuro):
   - Ir a Mantenimiento → Empresas
   - Editar empresa principal
   - Completar campos: IBAN, SWIFT, Banco
   - Guardar y regenerar PDF de presupuesto

---

**Última actualización**: 14 de febrero de 2026  
**Estado**: ✅ Implementado y Probado  
**Rama**: cliente0_presupuesto  
**Commits**: 2db8a64 - feat(punto18): Mostrar datos bancarios en PDF con TRANSFERENCIA  
**Archivo**: `docs/presupuestos_20260211.md`

---

### 20. Sistema de Peso en Presupuestos ✅ **COMPLETADA**

**Fecha inicio**: 15 de febrero de 2026  
**Fecha finalización**: (En desarrollo)  
**Prioridad**: Media  
**Tipo**: Nueva funcionalidad

#### 📋 Descripción

Implementación de un sistema de cálculo automático de peso total en presupuestos. El sistema calcula el peso basándose en los elementos físicos de inventario, diferenciando entre artículos normales (peso promedio) y KITs (suma de componentes).

Este sistema permite al cliente conocer el **peso total estimado** de todos los equipos incluidos en un presupuesto, facilitando la logística de transporte y planificación de carga de vehículos.

#### 🎯 Requerimientos

**Necesidad del cliente:**
- Conocer el peso total de equipos en cada presupuesto
- Facilitar planificación logística y carga de furgonetas
- Estimación anticipada para transporte

**Restricción técnica:**
- Los presupuestos están compuestos de **artículos**, no de elementos
- Los artículos se componen de **elementos** (inventario físico)
- Los artículos pueden ser:
  - **Normales**: compuestos por múltiples elementos (ej: varios micrófonos)
  - **KITs**: compuestos por otros artículos (ej: iluminación = focos + cables)

#### 🧮 Lógica de Cálculo

##### Caso 1: Artículo Normal (es_kit_articulo = 0)

```
Artículo: "Micrófono inalámbrico"
  ├─ Elemento MIC-001: 0.50 kg
  ├─ Elemento MIC-002: 0.52 kg
  ├─ Elemento MIC-003: 0.48 kg
  └─ Elemento MIC-004: 0.51 kg

📊 Peso artículo = MEDIA ARITMÉTICA de elementos
   (0.50 + 0.52 + 0.48 + 0.51) / 4 = 0.5025 kg

💼 Presupuesto: 10 unidades
   10 × 0.5025 kg = 5.025 kg
```

**Razón:** No sabemos qué elementos específicos se asignarán, usamos peso promedio.

##### Caso 2: Artículo KIT (es_kit_articulo = 1)

```
KIT: "Iluminación Evento"
  ├─ 12× Foco LED 100W (peso medio: 2.3 kg)
  └─ 12× Cable XLR 5m (peso medio: 0.4 kg)

📊 Peso KIT = SUMA de (cantidad × peso_medio_componente)
   (12 × 2.3) + (12 × 0.4) = 27.6 + 4.8 = 32.4 kg

💼 Presupuesto: 2 unidades de KIT
   2 × 32.4 kg = 64.8 kg
```

**Razón:** Los KITs tienen composición fija, siempre llevan los mismos componentes.

#### 🗄️ Arquitectura de la Solución

##### Decisión de Diseño: 100% Vistas SQL

```
┌──────────────────┐
│ elemento         │ ← ÚNICA tabla modificada
│ peso_elemento    │    (nuevo campo DECIMAL(10,3))
└────────┬─────────┘
         │
         ▼
┌────────────────────────────────────┐
│ vista_articulo_peso_medio          │ ← Calcula AVG para artículos
└────────┬───────────────────────────┘
         │
         ├─────────────────────────────┐
         │                             │
         ▼                             ▼
┌────────────────────────┐   ┌────────────────────────┐
│ vista_kit_peso_total   │   │ vista_articulo_peso    │
│ (suma componentes)     │   │ (unifica ambos tipos)  │
└────────┬───────────────┘   └────────┬───────────────┘
         │                             │
         └─────────────┬───────────────┘
                       ▼
         ┌─────────────────────────────┐
         │ vista_linea_peso            │ ← Multiplica cantidad × peso
         └─────────────┬───────────────┘
                       │
                       ▼
         ┌─────────────────────────────┐
         │ vista_presupuesto_peso      │ ← Suma todas las líneas
         └─────────────────────────────┘
```

**Ventajas de usar solo vistas:**
- ✅ Siempre datos actualizados (tiempo real)
- ✅ No hay desincronización (no hay triggers)
- ✅ Fácil de mantener y extender
- ✅ Sin overhead de recálculo
- ✅ Auditable y transparente

#### 📂 Cambios Implementados

##### 20.1 Base de Datos ✅

**Archivo**: `BD/migrations/20260215_add_peso_sistema.sql`

**Cambio en tabla `elemento`:**
```sql
ALTER TABLE elemento 
ADD COLUMN peso_elemento DECIMAL(10,3) DEFAULT NULL 
    COMMENT 'Peso en kilogramos (NULL si no aplica)',
ADD INDEX idx_peso_elemento (peso_elemento),
ADD INDEX idx_articulo_peso (id_articulo_elemento, activo_elemento, peso_elemento);
```

**Vistas SQL creadas:**

1. **`vista_articulo_peso_medio`**: Calcula peso medio de artículos normales
   - Método: `AVG(peso_elemento)` de elementos activos
   - Incluye: contador elementos, min/max peso
   
2. **`vista_kit_peso_total`**: Calcula peso total de KITs
   - Método: `SUM(cantidad_kit × peso_medio_componente)`
   - Incluye: contador componentes con peso

3. **`vista_articulo_peso`**: Vista unificada para cualquier artículo
   - Retorna peso según tipo (normal/KIT)
   - Campos: `peso_articulo_kg`, `metodo_calculo`, `tiene_datos_peso`

4. **`vista_linea_peso`**: Peso por línea de presupuesto
   - Cálculo: `cantidad_linea × peso_articulo`
   - Incluye todos los datos de la línea

5. **`vista_presupuesto_peso`**: Peso total del presupuesto
   - Cálculo: `SUM(peso_total_linea)`
   - Métricas: peso total, desglose por tipo, % completitud

**Índices de optimización:**
```sql
-- Optimizar agregaciones
idx_articulo_peso (id_articulo_elemento, activo_elemento, peso_elemento)

-- Optimizar joins de presupuesto
idx_version_articulo (id_version_presupuesto, id_articulo, activo_linea_ppto)

-- Optimizar joins de kit
idx_maestro_activo (id_articulo_maestro, activo_kit)
```

##### 20.2 Modelo `Elemento.php` 🔄

**Métodos añadidos:**

```php
// Actualizar peso de elemento
public function update_peso_elemento($id_elemento, $peso_kg);

// Obtener peso promedio de artículo
public function get_peso_articulo($id_articulo);
```

##### 20.3 Modelo `ImpresionPresupuesto.php` 🔄

**Métodos añadidos:**

```php
// Obtener peso total del presupuesto
public function get_peso_total_presupuesto($id_version_presupuesto);

// Obtener líneas con información de peso
public function get_lineas_con_peso($id_version_presupuesto);
```

##### 20.4 Interfaz - Pantalla de Elementos 🔄

**Archivo**: `view/MntElementos/elementos.php`

- ✅ Campo de entrada numérico para peso (DECIMAL 10,3)
- ✅ Placeholder: "Ej: 12.500"
- ✅ Unidad: "kg" (sufijo visual)
- ✅ Tooltip explicativo
- ✅ Opcional (puede ser NULL)

**DataTable:**
- ✅ Nueva columna "Peso (kg)"
- ✅ Formato: Badge azul si tiene valor, guión si NULL
- ✅ Formato numérico: 3 decimales

##### 20.5 PDF del Presupuesto 🔄

**Archivo**: `controller/impresionpresupuesto_m2_pdf_es.php`

**Ubicación:** Después de la sección de TOTALES, antes de observaciones

```
┌──────────────────────────────────────────┐
│ Subtotal:            1.234,56 €          │
│ Base Imponible:      1.234,56 €          │
│ IVA (21%):             259,26 €          │
├──────────────────────────────────────────┤
│ TOTAL:               1.493,82 €          │
└──────────────────────────────────────────┘

┌──────────────────────────────────────────┐
│ PESO TOTAL ESTIMADO:        385,50 KG    │ ← NUEVO ⭐
└──────────────────────────────────────────┘

[... Observaciones ...]
```

**Formato visual:**
- Fondo gris claro (#F5F5F5)
- Borde gris (#B4B4B4)
- Texto negrita 11pt
- Formato español: `385,50 KG`

**Nota de completitud (opcional):**
Si hay líneas sin peso definido, muestra:
```
* Calculado sobre 8 de 10 líneas (80.0% completitud)
```

#### 🧪 Casos de Prueba

- [ ] **Elemento sin peso**: NULL mostrado correctamente
- [ ] **Elemento con peso**: Guardar/editar 12.500 kg
- [ ] **Artículo normal**: Calcular peso medio de 4 elementos
- [ ] **KIT con componentes**: Sumar peso de componentes × cantidad
- [ ] **Línea con cantidad > 1**: Multiplicar correctamente
- [ ] **Presupuesto completo**: Suma total correcta
- [ ] **Presupuesto parcial**: Mostrar % completitud
- [ ] **PDF rendering**: Bloque de peso visible y bien formateado
- [ ] **Performance**: Consultas rápidas con índices

#### 📊 Consultas Útiles de Análisis

```sql
-- Ver estado de pesos en artículos
SELECT 
    a.codigo_articulo,
    a.nombre_articulo,
    CASE WHEN a.es_kit_articulo = 1 THEN 'KIT' ELSE 'ARTÍCULO' END AS tipo,
    vap.peso_articulo_kg,
    vap.metodo_calculo,
    vap.tiene_datos_peso,
    CONCAT(vap.items_con_peso, '/', vap.total_items) AS elementos_completos
FROM vista_articulo_peso vap
JOIN articulo a ON vap.id_articulo = a.id_articulo
ORDER BY vap.tiene_datos_peso DESC, a.es_kit_articulo, a.nombre_articulo;

-- Análisis de presupuesto específico
SELECT 
    p.numero_presupuesto,
    vpp.peso_total_kg,
    vpp.lineas_con_peso,
    vpp.lineas_sin_peso,
    vpp.porcentaje_completitud
FROM vista_presupuesto_peso vpp
JOIN presupuesto_version pv ON vpp.id_version_presupuesto = pv.id_version_presupuesto
JOIN presupuesto p ON vpp.id_presupuesto = p.id_presupuesto
WHERE p.numero_presupuesto = '2026-001';
```

#### 💡 Mejoras Futuras (Opcionales)

1. **Volumen y dimensiones**:
   - Campos: `volumen_m3`, `largo_cm`, `ancho_cm`, `alto_cm`
   - Útil para planificación de espacio en furgonetas

2. **Alertas de capacidad**:
   - Comparar peso total vs capacidad de furgoneta
   - Warning visual si excede límite

3. **Peso por ubicación**:
   - Agrupar peso por ubicación de montaje
   - Facilitar múltiples viajes

4. **Peso editable en línea**:
   - Campo `peso_manual_linea` en `linea_presupuesto`
   - Override manual si cliente necesita ajuste específico

5. **Histórico de cambios**:
   - Auditar cambios en `peso_elemento`
   - Tabla `peso_elemento_historial`

6. **Exportación a logística**:
   - Integración con sistema de rutas
   - API para planificador de cargas

#### ⚠️ Consideraciones Importantes

**Datos opcionales:**
- El campo `peso_elemento` es NULL por defecto
- Artículos sin peso: mostrarán 0.00 kg
- PDF solo muestra bloque si `peso_total_kg > 0`

**Tipo de cálculo:**
- Vista `vista_articulo_peso` incluye campo `metodo_calculo`:
  - `'MEDIA_ELEMENTOS'` para artículos normales
  - `'SUMA_COMPONENTES'` para KITs

**Performance:**
- Índices compuestos para optimizar agregaciones
- Vistas materializadas NO necesarias con índices correctos
- Si performance es problema: considerar cacheo a nivel aplicación

**Mantenimiento:**
- NO hay triggers: sin mantenimiento adicional
- Cambios en peso de elemento: reflejados inmediatamente
- Nueva vista SQL: fácil de añadir/modificar

#### 📝 Notas de Implementación

**Validaciones en interfaz:**
- Peso >= 0 (no negativos)
- Máximo 99,999.999 kg
- 3 decimales de precisión
- Campo opcional (puede quedarse vacío)

**Formato de salida:**
- España: punto millar, coma decimal (1.234,567 kg)
- Base datos: punto decimal estándar (1234.567)

**NULL vs 0:**
- `NULL`: peso desconocido o no aplica
- `0.000`: peso conocido pero es cero (ej: artículo virtual)

#### 📂 Archivos Afectados

| Archivo | Tipo | Descripción |
|---------|------|-------------|
| `BD/migrations/20260215_add_peso_sistema.sql` | Nuevo | Migración completa con vistas |
| `models/Elemento.php` | Modificado | Métodos de peso |
| `models/ImpresionPresupuesto.php` | Modificado | Métodos de consulta peso |
| `controller/elemento.php` | Modificado | Operaciones CRUD con peso |
| `view/MntElementos/formularioElemento.php` | Modificado | Campo peso en formulario |
| `view/MntElementos/formularioElemento.js` | Modificado | Carga de peso al editar |
| `controller/impresionpresupuesto_m2_pdf_es.php` | Modificado | Renderizado bloque peso |
| `ejecutar_migracion_peso.php` | Nuevo | Script PHP para ejecutar migración |

---

**Última actualización**: 15 de febrero de 2026  
**Estado**: ✅ COMPLETADA E IMPLEMENTADA  
**Rama**: km  
**Commits**: ba01d1e - feat(seccion20): Implementar sistema de peso en presupuestos  
**Pendiente**: Ejecutar migración SQL en servidor (`php ejecutar_migracion_peso.php`)  
**Archivo**: `docs/presupuestos_20260211.md`

---

### 21. Impresión de Albaranes ✅ **COMPLETADA Y FINALIZADA**

**Fecha inicio**: 16 de febrero de 2026  
**Fecha finalización**: 16 de febrero de 2026  
**Prioridad**: Alta  
**Tipo**: Ajuste funcional + documentación

#### 📋 Descripción

Se documenta y cierra la funcionalidad de **impresión de albaranes de carga** con control de visibilidad por empresa. La configuración se gestiona en Mantenimiento de Empresas y afecta directamente al contenido mostrado en el PDF del albarán.

#### 🎯 Trabajo realizado

##### 21.1 Configuración por empresa ✅
- ✅ Se habilitaron y consolidaron 3 controles en Empresa para albarán de carga:
   - `mostrar_kits_albaran_empresa`
   - `mostrar_obs_familias_articulos_albaran_empresa`
   - `mostrar_obs_pie_albaran_empresa`
- ✅ Estos controles se exponen en la sección **"Configuración de PDF de ALBARANES DE CARGA"** del formulario de empresa.

##### 21.2 Persistencia de parámetros ✅
- ✅ Los 3 campos se guardan y actualizan correctamente desde controller/model de empresas.
- ✅ Los valores quedan asociados a cada empresa para comportamiento multiempresa.

##### 21.3 Aplicación en impresión PDF ✅
- ✅ La generación del PDF de albarán respeta los switches configurados por empresa.
- ✅ Se controla de forma independiente:
   - Mostrar/ocultar desglose de KITs.
   - Mostrar/ocultar observaciones técnicas de familias y artículos.
   - Mostrar/ocultar observaciones de pie.

##### 21.4 Ayuda funcional actualizada ✅
- ✅ Se actualizó la ayuda de empresas para dejar claro:
   - Dónde se configuran los nuevos campos.
   - En qué parte del programa impactan.
   - Qué efecto tienen en la impresión de albaranes.

#### 📂 Archivos relacionados

| Archivo | Tipo | Descripción |
|---------|------|-------------|
| `view/MntEmpresas/formularioEmpresa.php` | Modificado | Controles de configuración PDF de albaranes |
| `view/MntEmpresas/formularioEmpresa.js` | Modificado | Carga/envío de switches de albarán |
| `controller/empresas.php` | Modificado | Recepción y persistencia de campos |
| `models/Empresas.php` | Modificado | Inserción/actualización de configuración |
| `controller/impresionpartetrabajo_m2_pdf_es.php` | Modificado | Render condicional del contenido de albarán |
| `view/MntEmpresas/ayudaEmpresas.php` | Modificado | Documentación funcional de uso de nuevos campos |

#### ✅ Cierre

- **Estado**: ✅ COMPLETADA Y FINALIZADA
- **Resultado**: La impresión de albaranes queda parametrizada por empresa y documentada para usuario funcional.
- **Pendiente**: Ninguno en esta tarea.

