# ✅ COMPLETADO: Punto 14 - Nueva Funcionalidad - Firma Digital de Empleado

**Fecha:** 20 de enero de 2025  
**Estado:** COMPLETADO  
**Funcionalidad:** Sistema completo de firma digital para comerciales en presupuestos PDF

---

## 📋 Descripción de la Funcionalidad

Sistema que permite a los usuarios comerciales capturar su firma digital mediante un canvas HTML5, almacenarla en base de datos y renderizarla automáticamente en los PDFs de presupuestos generados.

### Características Implementadas

✅ **Captura de Firma:**
- Canvas HTML5 con librería Signature Pad 4.1.7
- Interfaz intuitiva en pantalla de perfil de usuario
- Botones "Limpiar" y "Guardar Firma"
- Vista previa de firma guardada
- Validación de firma no vacía

✅ **Almacenamiento:**
- Formato: Base64 PNG (~20-50KB por firma)
- Campo: `comerciales.firma_comercial` (TEXT)
- Relación: `usuarios.id_usuario → comerciales.id_usuario`
- Validación de formato y tamaño (máx 500KB)

✅ **Renderizado en PDF:**
- Aparece en sección "DEPARTAMENTO COMERCIAL"
- Tamaño: 60mm ancho × 14mm alto (proporcional)
- Posición: Entre título y línea de firma manuscrita
- Fallback: Si no hay firma digital, muestra espacio vacío

✅ **Seguridad:**
- Solo usuarios con perfil comercial pueden firmar
- Validación de sesión en todos los endpoints
- Sanitización de datos base64
- Logging de todas las operaciones

---

## 🗂️ Archivos Modificados/Creados

### 1. Base de Datos
**📄 Archivo:** `BD/migrations/20250120_add_firma_comercial.sql`
```sql
ALTER TABLE comerciales 
ADD COLUMN firma_comercial TEXT 
COMMENT 'Firma digital del comercial en formato base64 PNG';
```

**⚠️ IMPORTANTE:** Este script SQL debe ejecutarse en la base de datos antes de usar la funcionalidad.

### 2. Modelo (Backend)
**📄 Archivo:** `models/Comerciales.php`

**Métodos agregados:**
- `update_firma_by_usuario($id_usuario, $firma_base64)` - Actualiza la firma por ID de usuario
- `get_firma_by_usuario($id_usuario)` - Obtiene la firma por ID de usuario
- `get_comercial_by_usuario($id_usuario)` - Obtiene datos del comercial incluyendo firma

### 3. Controllers (Endpoints AJAX)
**📄 Nuevos archivos:**
- `controller/ajax_guardar_firma.php` - Guardar firma digital
- `controller/ajax_obtener_firma.php` - Obtener firma existente

**Características de los endpoints:**
- Validación de sesión activa
- Verificación de perfil comercial
- Validación de formato base64 PNG
- Límite de tamaño (500KB)
- Logging de actividad con RegistroActividad
- Respuestas JSON estandarizadas

### 4. Vista (Frontend)
**📄 Archivo:** `view/Home/perfil.php`

**Cambios realizados:**
- Inclusión de librería Signature Pad 4.1.7 (CDN)
- Sección de firma digital con canvas 260×150px
- Botones de acción (Limpiar, Guardar)
- Área de vista previa de firma guardada
- Estilos CSS embebidos para diseño responsive
- Sección oculta por defecto (solo visible para comerciales)

### 5. JavaScript
**📄 Archivo:** `view/Home/perfil.js`

**Funcionalidades agregadas:**
- Inicialización de Signature Pad en canvas
- Ajuste responsive del canvas (device pixel ratio)
- Función `verificarYCargarFirma()` - Carga firma existente
- Función `guardarFirma()` - Envía firma por AJAX
- Manejo de eventos para botones Limpiar/Guardar
- Validación de firma vacía
- Notificaciones toastr para feedback

### 6. Generación PDF
**📄 Archivo:** `controller/impresionpresupuesto_m2_pdf_es.php`

**Cambios realizados:**
- Línea 15: Agregado `require_once Comerciales.php`
- Líneas 1530-1595: Sección de firma digital implementada
  - Consulta de firma por `$_SESSION['id_usuario']`
  - Validación de formato base64
  - Renderizado con `$pdf->Image()` usando data URI
  - Manejo de errores con fallback a espacio vacío
  - Dimensiones: 60mm × 14mm con DPI 300

---

## 🔄 Flujo de Funcionamiento

```
┌─────────────────────────────────────────────────────────────┐
│ 1. INICIO DE SESIÓN                                         │
│    - Usuario inicia sesión                                  │
│    - $_SESSION['id_usuario'] se establece                   │
└────────────────────┬────────────────────────────────────────┘
                     │
┌────────────────────▼────────────────────────────────────────┐
│ 2. ACCESO AL PERFIL (view/Home/perfil.php)                 │
│    - Página carga con sección de firma oculta              │
│    - JavaScript ejecuta verificarYCargarFirma()            │
└────────────────────┬────────────────────────────────────────┘
                     │
┌────────────────────▼────────────────────────────────────────┐
│ 3. VERIFICACIÓN DE COMERCIAL                                │
│    - AJAX a ajax_obtener_firma.php                         │
│    - Consulta: comerciales WHERE id_usuario = session      │
│    - Si es comercial: muestra sección + carga firma        │
│    - Si NO es comercial: oculta sección                    │
└────────────────────┬────────────────────────────────────────┘
                     │
┌────────────────────▼────────────────────────────────────────┐
│ 4. CAPTURA DE FIRMA (si es comercial)                      │
│    - Usuario dibuja en canvas con Signature Pad            │
│    - Click en "Limpiar": signaturePad.clear()              │
│    - Click en "Guardar": ejecuta guardarFirma()            │
└────────────────────┬────────────────────────────────────────┘
                     │
┌────────────────────▼────────────────────────────────────────┐
│ 5. GUARDADO DE FIRMA                                        │
│    - JavaScript convierte canvas a base64 PNG              │
│    - AJAX POST a ajax_guardar_firma.php                    │
│    - Validaciones: sesión, comercial, formato, tamaño      │
│    - UPDATE comerciales SET firma_comercial = ?            │
│    - Respuesta JSON + actualización de vista previa        │
└────────────────────┬────────────────────────────────────────┘
                     │
┌────────────────────▼────────────────────────────────────────┐
│ 6. GENERACIÓN DE PRESUPUESTO PDF                           │
│    - Usuario genera PDF desde sistema                      │
│    - impresionpresupuesto_m2_pdf_es.php se ejecuta        │
└────────────────────┬────────────────────────────────────────┘
                     │
┌────────────────────▼────────────────────────────────────────┐
│ 7. RENDERIZADO DE FIRMA EN PDF                             │
│    - Obtiene id_usuario de $_SESSION                       │
│    - Consulta: Comerciales->get_firma_by_usuario()         │
│    - Si existe firma: $pdf->Image(data_uri, x, y, w, h)   │
│    - Si NO existe: espacio vacío para firma manuscrita     │
│    - Continúa con generación normal del PDF                │
└─────────────────────────────────────────────────────────────┘
```

---

## 🧪 Instrucciones de Prueba

### Pre-requisitos
1. ✅ Ejecutar migración SQL: `BD/migrations/20250120_add_firma_comercial.sql`
2. ✅ Tener al menos un usuario con registro en tabla `comerciales`
3. ✅ Navegador moderno con soporte para Canvas API

### Caso de Prueba 1: Usuario Comercial - Captura de Firma

**Pasos:**
1. Iniciar sesión con usuario que tiene `id_usuario` en tabla `comerciales`
2. Navegar a: `view/Home/perfil.php`
3. Verificar que aparezca la sección "Firma Digital"
4. Dibujar firma en el canvas
5. Click en "Limpiar" → Canvas debe limpiarse
6. Dibujar firma nuevamente
7. Click en "Guardar Firma"

**Resultado Esperado:**
- ✅ Mensaje toastr: "Firma guardada"
- ✅ Aparece vista previa bajo el canvas
- ✅ Mensaje de estado: "Firma guardada correctamente" (verde con check)

### Caso de Prueba 2: Usuario Comercial - Firma Existente

**Pasos:**
1. Con firma ya guardada, salir y volver a iniciar sesión
2. Navegar a: `view/Home/perfil.php`
3. Observar sección de firma

**Resultado Esperado:**
- ✅ Sección de firma visible
- ✅ Vista previa muestra firma guardada
- ✅ Mensaje: "Tiene firma guardada" (verde con check)

### Caso de Prueba 3: Usuario NO Comercial

**Pasos:**
1. Iniciar sesión con usuario SIN registro en tabla `comerciales`
2. Navegar a: `view/Home/perfil.php`

**Resultado Esperado:**
- ✅ Sección de firma NO visible (oculta)
- ✅ Solo muestra: Email, Fecha de creación, Botón cerrar sesión

### Caso de Prueba 4: Renderizado en PDF con Firma

**Pasos:**
1. Con usuario comercial que tiene firma guardada
2. Crear o editar un presupuesto
3. Generar PDF del presupuesto
4. Abrir PDF y navegar a última página (sección de firmas)

**Resultado Esperado:**
- ✅ En casilla "DEPARTAMENTO COMERCIAL" aparece firma digital
- ✅ Firma centrada, tamaño proporcional (~60×14mm)
- ✅ Debajo: línea de "Firma y Sello"
- ✅ Fecha actual de impresión

### Caso de Prueba 5: Renderizado en PDF SIN Firma

**Pasos:**
1. Con usuario comercial que NO tiene firma guardada
2. Generar PDF de presupuesto

**Resultado Esperado:**
- ✅ Casilla "DEPARTAMENTO COMERCIAL" con espacio vacío
- ✅ Línea de "Firma y Sello" en su lugar habitual
- ✅ No hay errores en PDF

### Caso de Prueba 6: Validación de Firma Vacía

**Pasos:**
1. En perfil, NO dibujar nada en canvas
2. Click directo en "Guardar Firma"

**Resultado Esperado:**
- ✅ Mensaje toastr warning: "Por favor, dibuje su firma antes de guardar"
- ✅ NO se envía petición AJAX

### Caso de Prueba 7: Actualización de Firma

**Pasos:**
1. Con firma ya guardada
2. Dibujar nueva firma en canvas
3. Click en "Guardar Firma"

**Resultado Esperado:**
- ✅ Firma se actualiza en BD
- ✅ Vista previa se actualiza con nueva firma
- ✅ PDFs posteriores muestran nueva firma

---

## 📊 Estructura de Base de Datos

```sql
-- Tabla comerciales (modificada)
CREATE TABLE comerciales (
    id_comercial INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    movil VARCHAR(20),
    telefono VARCHAR(20),
    id_usuario INT UNSIGNED,
    firma_comercial TEXT,  -- ⬅️ NUEVO CAMPO
    activo TINYINT(1) DEFAULT 1,
    created_at_comercial TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at_comercial TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_id_usuario (id_usuario),
    INDEX idx_activo (activo),
    
    CONSTRAINT fk_comercial_usuario 
        FOREIGN KEY (id_usuario) 
        REFERENCES usuarios(id_usuario)
        ON DELETE SET NULL 
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;
```

---

## 🔒 Consideraciones de Seguridad

1. **Validación de Sesión:**
   - Todos los endpoints verifican `$_SESSION['sesion_iniciada']`
   - Requieren `$_SESSION['id_usuario']` válido

2. **Validación de Comercial:**
   - Solo usuarios con registro en `comerciales` pueden firmar
   - Consulta con `WHERE id_usuario = ? AND activo = 1`

3. **Validación de Formato:**
   - Regex: `/^data:image\/png;base64,/`
   - Límite de tamaño: 500KB en base64 (~700,000 caracteres)
   - Rechazo de formatos no PNG

4. **Logging:**
   - Todas las operaciones se registran con `RegistroActividad`
   - Logs incluyen: usuario, acción, resultado, timestamp
   - Ubicación: `public/logs/YYYY-MM-DD.json`

5. **Manejo de Errores:**
   - Try-catch en todos los métodos críticos
   - Mensajes genéricos al usuario (no exponen detalles internos)
   - Errores detallados en logs del servidor

---

## 🎨 Especificaciones Técnicas

### Canvas de Firma
- **Dimensiones:** 260×150 píxeles (display)
- **Resoluci��n:** Ajustada por devicePixelRatio (retina-ready)
- **Color de fondo:** Blanco RGB(255, 255, 255)
- **Color de trazo:** Negro RGB(0, 0, 0)
- **Formato guardado:** PNG con fondo blanco

### Almacenamiento
- **Formato:** `data:image/png;base64,[datos]`
- **Tamaño promedio:** 20-50KB por firma (depende de complejidad)
- **Tamaño máximo:** 500KB (validado en backend)
- **Campo BD:** TEXT (máx. 65,535 caracteres) - suficiente para ~48KB

### Renderizado PDF
- **Método TCPDF:** `Image($data_uri, $x, $y, $w, $h)`
- **Ancho máximo:** 60mm
- **Alto máximo:** 14mm
- **DPI:** 300
- **Posición X:** Centrado en casilla de 90mm
- **Posición Y:** Entre título y línea de firma

---

## 📝 Logging de Actividad

### Eventos Registrados

```json
{
  "usuario": "admin@example.com",
  "pantalla": "ajax_guardar_firma",
  "actividad": "guardar_firma",
  "mensaje": "Firma guardada exitosamente para comercial: Juan Pérez (ID: 5)",
  "tipo": "info",
  "fecha_hora": "2025-01-20 14:32:15"
}
```

### Tipos de Log
- **info:** Operación exitosa (firma guardada, firma obtenida)
- **warning:** Intentos no autorizados, formato inválido
- **error:** Excepciones, errores de BD, renderizado fallido

---

## 🐛 Troubleshooting

### Problema: Sección de firma no aparece
**Causa:** Usuario no tiene registro en tabla comerciales  
**Solución:** Verificar que id_usuario del usuario existe en comerciales.id_usuario

### Problema: Error al guardar firma
**Causa:** Campo firma_comercial no existe en BD  
**Solución:** Ejecutar migración SQL `20250120_add_firma_comercial.sql`

### Problema: Firma no aparece en PDF
**Causas posibles:**
1. Sesión no tiene id_usuario
2. Firma no guardada en BD (campo NULL)
3. Formato de firma inválido (no es data:image/png;base64,)

**Soluciones:**
1. Verificar que $_SESSION['id_usuario'] existe al generar PDF
2. Verificar en BD: `SELECT firma_comercial FROM comerciales WHERE id_usuario = X`
3. Validar formato en BD, debe empezar con "data:image/png;base64,"

### Problema: Canvas no dibuja en dispositivos móviles
**Causa:** Touch events no manejados  
**Solución verificada:** Signature Pad 4.1.7 tiene soporte touch nativo + CSS touch-action:none

### Problema: Firma muy grande, error 413 (Request Entity Too Large)
**Causa:** Servidor rechaza POST >500KB  
**Solución:** Validación frontend (ya implementada) + ajustar php.ini:
```ini
post_max_size = 2M
upload_max_filesize = 2M
```

---

## 📦 Dependencias Externas

### Signature Pad 4.1.7
- **CDN:** https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js
- **Licencia:** MIT
- **Documentación:** https://github.com/szimek/signature_pad
- **Peso:** ~10KB minificado
- **Compatibilidad:** IE11+, Chrome, Firefox, Safari, Edge, iOS, Android

---

## ✅ Checklist de Implementación

- [x] Migración SQL creada (`20250120_add_firma_comercial.sql`)
- [x] Modelo Comerciales.php actualizado con métodos de firma
- [x] Endpoint ajax_guardar_firma.php creado
- [x] Endpoint ajax_obtener_firma.php creado
- [x] Vista perfil.php actualizada con canvas
- [x] JavaScript perfil.js con integración Signature Pad
- [x] PDF controller actualizado para renderizar firma
- [x] Validaciones de seguridad implementadas
- [x] Logging de actividad configurado
- [x] Manejo de errores robusto
- [x] Documentación completa
- [ ] ⚠️ **PENDIENTE: Ejecutar migración SQL en servidor**
- [ ] ⚠️ **PENDIENTE: Pruebas en entorno de producción**

---

## 🔄 Próximos Pasos

1. **EJECUTAR MIGRACIÓN SQL** (CRÍTICO):
   ```bash
   mysql -u usuario -p toldos_db < BD/migrations/20250120_add_firma_comercial.sql
   ```

2. **Pruebas de Usuario:**
   - Probar con al menos 3 usuarios comerciales diferentes
   - Generar PDFs con y sin firma
   - Verificar firma en diferentes resoluciones de pantalla

3. **Validación de Producción:**
   - Revisar tamaño de firmas almacenadas (estadística)
   - Monitorear logs de errores primeros días
   - Verificar rendimiento de renderizado PDF

4. **Capacitación:**
   - Instruir a usuarios comerciales sobre captura de firma
   - Documentar proceso en manual de usuario
   - Crear video tutorial corto (opcional)

---

## 📞 Soporte Técnico

**Desarrollador:** Luis - Innovabyte  
**Fecha de Implementación:** 20 de enero de 2025  
**Versión del Sistema:** MDR ERP Manager 1.0  
**Branch:** cliente0_presupuesto  

---

**Última actualización:** 20 de enero de 2025  
**Estado:** ✅ COMPLETADO - Listo para pruebas
