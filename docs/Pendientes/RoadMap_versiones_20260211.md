# 🎯 Sistema de Versiones - Guía del Usuario

**Fecha**: 16 de febrero de 2026  
**Audiencia**: Usuarios finales (comerciales, administradores)  
**Objetivo**: Entender cómo funciona el sistema de versiones sin tecnicismos

---

## ¿Qué es el sistema de versiones?

Es como tener un **historial de cambios** de cada presupuesto. Cada vez que necesitas hacer modificaciones, creas una nueva versión (como "Presupuesto_v1", "Presupuesto_v2", etc.) sin perder las anteriores.

**En resumen**: Es el "Control Z" profesional para presupuestos.

---

## 📖 Historia de un Presupuesto (Ejemplo Real)

### **Día 1: Cliente solicita presupuesto** 🆕

María (comercial) crea un presupuesto para una boda:
- 100 sillas Napoleón
- 10 mesas redondas
- 1 carpa 10x20m
- **Total: 3.500€**

👉 El sistema **automáticamente** crea la **Versión 1** (en estado "borrador")

---

### **Día 2: María envía el presupuesto** 📧

María revisa, está conforme, y presiona el botón **"Enviar al cliente"**.

**Lo que pasa:**
- ✅ Se genera el PDF automáticamente con "Versión 1"
- 🔒 La Versión 1 se **bloquea** (ya no se puede editar)
- 📧 PDF listo para enviar al cliente

**Estado actual**: Versión 1 → Enviada

---

### **Día 3: El cliente pide cambios** 🔄

Cliente llama: *"Me gustan 150 sillas en lugar de 100, y añade 5 calefactores"*

María NO puede editar la Versión 1 (está bloqueada). Entonces:

1. Abre el presupuesto
2. Presiona **"Nueva Versión"**
3. Escribe el motivo: *"Cliente solicita 150 sillas y 5 calefactores"*
4. ✨ El sistema crea **Versión 2** copiando automáticamente todo de la v1

Ahora María puede:
- Cambiar 100 sillas → 150 sillas
- Añadir 5 calefactores
- Nuevo total: **4.200€**

**Estado actual**:
- Versión 1 → Enviada (guardada, histórico)
- Versión 2 → Borrador (editable)

---

### **Día 4: Envía la nueva versión** 📧

María presiona **"Enviar al cliente"** sobre la Versión 2.

**Estado actual**:
- ✅ **Versión 1** → Enviada (histórico conservado)
- ✅ **Versión 2** → Enviada (versión actual)

---

### **Día 5: Cliente aprueba** ✅

Cliente llama: *"Perfecto, adelante con la Versión 2"*

María presiona **"Aprobar"** sobre la Versión 2.

**Lo que pasa:**
- ✅ Versión 2 queda **APROBADA** (cerrada definitivamente)
- 🎉 El presupuesto está confirmado
- 📋 Se puede generar el contrato/albarán
- 🔒 Nadie puede modificar nada más

---

## 🎬 Flujo Visual Completo

```
┌─────────────┐
│   CREAR     │  Usuario crea presupuesto
│ Presupuesto │  → Sistema auto-crea Versión 1
└──────┬──────┘
       │
       ▼
┌─────────────┐
│  Versión 1  │  Estado: Borrador (editable)
│  Borrador   │  • Añade líneas
└──────┬──────┘  • Modifica precios
       │         • Calcula totales
       │
       ▼
┌─────────────┐
│   ENVIAR    │  Usuario presiona "Enviar al cliente"
│ al Cliente  │  → Genera PDF versión 1
└──────┬──────┘  → Bloquea edición
       │
       ▼
┌─────────────┐
│  Versión 1  │  Estado: Enviada (bloqueada)
│   Enviada   │  Esperando respuesta del cliente...
└──────┬──────┘
       │
       ├──────────────────┐
       │                  │
       ▼                  ▼
┌──────────┐      ┌──────────────┐
│ APROBAR  │      │  RECHAZAR    │
│          │      │  o           │
└────┬─────┘      │  MODIFICAR   │
     │            └──────┬───────┘
     │                   │
     │                   ▼
     │            ┌──────────────┐
     │            │ NUEVA        │
     │            │ VERSIÓN      │  Usuario crea Versión 2
     │            └──────┬───────┘  → Copia todo de v1
     │                   │          → Permite editar
     │                   │
     │                   ▼
     │            ┌──────────────┐
     │            │  Versión 2   │  Modifica líneas
     │            │  Borrador    │  Cambia precios
     │            └──────┬───────┘
     │                   │
     │                   ▼
     │            ┌──────────────┐
     │            │   ENVIAR     │
     │            │  Versión 2   │
     │            └──────┬───────┘
     │                   │
     │                   ▼
     │            ┌──────────────┐
     │            │  Versión 2   │
     │            │   Enviada    │
     │            └──────┬───────┘
     │                   │
     └───────────────────┘
                         │
                         ▼
                  ┌──────────────┐
                  │   APROBAR    │
                  │  Versión 2   │
                  └──────┬───────┘
                         │
                         ▼
                  🎉 CERRADO
                  Presupuesto finalizado
```

---

## 🔍 Pantallas que verá el usuario

### **1. Listado de Presupuestos**

```
┌──────────────────────────────────────────────────────────────┐
│ Nº Presupuesto  │ Cliente      │ Versión │ Estado     │     │
├──────────────────────────────────────────────────────────────┤
│ P-00025/2026    │ Hotel Melia  │  v2     │ ⚫ Enviado   │ 👁️ │
│ P-00024/2026    │ Bodas López  │  v1     │ 🟢 Aprobado │ 👁️ │
│ P-00023/2026    │ Ayuntamiento │  v3     │ 🔵 Borrador │ 👁️ │
└──────────────────────────────────────────────────────────────┘
```

👉 **Badge de versión** visible en cada línea con color según estado

---

### **2. Detalle del Presupuesto**

```
┌─────────────────────────────────────────────────────┐
│ 📋 Presupuesto P-00025/2026                        │
│ 👤 Cliente: Hotel Melia                            │
│ 🎯 Evento: Cena de gala anual                      │
│                                                     │
│ [🕐 Ver Historial de Versiones]                    │
│ [📄 Nueva Versión]                                 │
└─────────────────────────────────────────────────────┘
```

**Botón "Ver Historial"** abre ventana emergente:

```
┌──────────────────────────────────────────────────────────────┐
│              📚 HISTORIAL DE VERSIONES                       │
├──────────────────────────────────────────────────────────────┤
│ Ver. │ Estado    │ Fecha      │ Motivo                  │ Acciones│
├──────┼───────────┼────────────┼─────────────────────────┼─────────┤
│  v3  │🟢Borrador │ 15/02/2026 │ Añadir 5 calefactores  │ 👁️📧✏️  │
│  v2  │🔵Enviada  │ 14/02/2026 │ Cambiar 150 sillas     │ 👁️✅❌📄 │
│  v1  │🔴Rechazada│ 13/02/2026 │ Versión inicial        │ 👁️📄   │
├──────┴───────────┴────────────┴─────────────────────────┴─────────┤
│                      [+ Nueva Versión]                            │
└──────────────────────────────────────────────────────────────────┘
```

**Iconos de acciones:**
- 👁️ **Ver** → Abre las líneas de esa versión
- 📧 **Enviar** → Cambia a estado "Enviada" (solo si es borrador)
- ✅ **Aprobar** → Cierra definitivamente (solo si está enviada)
- ❌ **Rechazar** → Marca como rechazada (solo si está enviada)
- 📄 **PDF** → Descarga el PDF de esa versión
- ✏️ **Editar** → Permite modificar líneas (solo borrador)

---

### **3. Editar Líneas de una Versión**

#### **Si es BORRADOR** (editable):

```
┌─────────────────────────────────────────────────────┐
│ 📝 Versión 3 - BORRADOR (Editable)                 │
│ ✅ Puede realizar cambios libremente                │
├─────────────────────────────────────────────────────┤
│ Línea │ Artículo         │ Cant │ Precio  │ Total │
├───────┼──────────────────┼──────┼─────────┼───────┤
│   1   │ Silla Napoleón   │ 150  │  4.50€ │675.00€│
│   2   │ Mesa redonda     │  10  │ 45.00€ │450.00€│
│   3   │ Calefactor 5kW   │   5  │ 25.00€ │125.00€│
│                                                     │
│ [+ Nueva línea] [✏️ Editar] [🗑️ Borrar]             │
│       ✅ ACTIVOS                                    │
└─────────────────────────────────────────────────────┘
```

✅ **Puede editar libremente**

#### **Si es ENVIADA/APROBADA** (bloqueada):

```
┌─────────────────────────────────────────────────────┐
│ 🔒 Versión 2 - ENVIADA (Bloqueada)                 │
│ ⚠️ No se pueden realizar cambios en esta versión    │
│    [📄 Crear nueva versión para modificar]          │
├─────────────────────────────────────────────────────┤
│ Línea │ Artículo         │ Cant │ Precio  │ Total │
├───────┼──────────────────┼──────┼─────────┼───────┤
│   1   │ Silla Napoleón   │ 100  │  4.50€ │450.00€│
│   2   │ Mesa redonda     │  10  │ 45.00€ │450.00€│
│                                                     │
│ [+ Nueva línea] [✏️ Editar] [🗑️ Borrar]             │
│   ❌ DESHABILITADOS                                 │
└─────────────────────────────────────────────────────┘
```

❌ **No puede editar** (botones deshabilitados)

---

## ✨ Ventajas para el Usuario

### **1. 📊 Trazabilidad Total**
- 📌 Sabes exactamente qué enviaste al cliente en cada momento
- 📝 Guardas histórico de lo que rechazó y por qué
- 👁️ Auditoría completa: quién cambió qué y cuándo
- 📅 Fechas exactas de cada cambio

### **2. 🛡️ Protección contra Errores**
- ❌ No puedes borrar accidentalmente un presupuesto enviado
- 🔒 No puedes modificar algo que el cliente ya vio
- 💾 Siempre hay respaldo de versiones anteriores
- ⚡ Evita pérdida de información

### **3. 🔄 Comparaciones Fáciles**
- 📊 Ver diferencias entre versión 1 y versión 3
- ✅ Saber qué cambió: líneas añadidas, eliminadas, precios modificados
- 📈 Tracking de evolución del presupuesto
- 💰 Comparar totales entre versiones

### **4. 💬 Comunicación Clara con Cliente**
- 📄 PDF siempre muestra "Versión 2" en la cabecera
- 🎯 Cliente ve claramente que es una versión actualizada
- ✉️ Evita confusiones y malentendidos
- 📋 Referencia clara en conversaciones

---

## 🎯 Casos de Uso Típicos

### **Caso 1: Cliente indeciso** 🤔

Cliente pide 3-4 variaciones diferentes del mismo presupuesto:

- **v1**: Opción básica (2.500€)
- **v2**: Opción intermedia (3.500€)
- **v3**: Opción premium (5.000€)
- **v4**: Mezcla de v2 + extras (4.200€)

Envías las 4 versiones. Cliente compara → Elige la v3 → Apruebas la v3 ✅

**Resultado**: Todas guardadas, histórico completo de opciones presentadas.

---

### **Caso 2: Revisión interna** 👔

- **v1**: Borrador inicial del comercial (con errores de precio)
- Jefe revisa → Detecta errores → Pide correcciones
- **v2**: Versión corregida con precios ajustados
- **v2** se envía al cliente (v1 nunca salió de la empresa)

**Resultado**: Control de calidad interno antes de enviar al cliente.

---

### **Caso 3: Negociación larga** 💼

- **v1**: Presupuesto inicial → ❌ Rechazado (precio alto)
- **v2**: Ajuste de precios -10% → ❌ Rechazado (faltan detalles)
- **v3**: Precios v2 + más detalle → ❌ Rechazado (quiere más equipos)
- **v4**: v3 + equipos extras → ✅ **APROBADO**

**Resultado**: Todo el histórico de negociación guardado, trazabilidad completa.

---

### **Caso 4: Error detectado después de enviar** 😱

- **v1**: Enviada al cliente (pero tiene error en cantidad)
- Te das cuenta del error → No puedes editar v1 (bloqueada)
- **Solución**: Creas v2 corregida → Envías v2 → Cliente aprueba v2

**Resultado**: Error corregido sin perder el histórico, sin caos.

---

## 🚦 Reglas Simples del Sistema

### **Estados y Transiciones**

| Estado | ¿Puedo editar? | ¿Qué puedo hacer? |
|--------|----------------|-------------------|
| 🟢 **Borrador** | ✅ SÍ | Editar todo, enviar al cliente |
| 🔵 **Enviado** | ❌ NO | Aprobar, rechazar, ver PDF |
| ⚫ **Aprobado** | ❌ NO | Solo consultar (cerrado) |
| 🔴 **Rechazado** | ❌ NO | Solo consultar, crear nueva versión |
| ⚪ **Cancelado** | ❌ NO | Solo consultar (anulado) |

### **Regla de Oro** ✨

> **Para cambiar algo enviado → Crear nueva versión**

No se puede editar una versión enviada. Siempre hay que crear una nueva versión para hacer cambios.

---

## 🎨 Código de Colores (Estados Visuales)

```
🟢 Verde Claro    → Borrador     (en proceso, editable)
🔵 Azul           → Enviado      (esperando respuesta)
⚫ Negro/Oscuro   → Aprobado     (confirmado, cerrado)
🔴 Rojo           → Rechazado    (descartado)
⚪ Gris           → Cancelado    (anulado)
```

Estos colores aparecen en:
- Badge de versión en listado
- Estado en historial
- Banner en edición de líneas

---

## 📋 Acciones Disponibles por Estado

### **En estado BORRADOR** 🟢

Acciones disponibles:
- ✏️ **Editar líneas** (añadir, modificar, eliminar)
- 📧 **Enviar al cliente** (cambia a "Enviada")
- 🗑️ **Eliminar** (solo si no tiene líneas)
- 📄 **Generar PDF** (borrador, no oficial)
- 🔄 **Cambiar a otra versión borrador** (si existe)

---

### **En estado ENVIADA** 🔵

Acciones disponibles:
- 👁️ **Ver líneas** (solo lectura)
- ✅ **Aprobar** (cierra definitivamente)
- ❌ **Rechazar** (con motivo obligatorio)
- 📄 **Descargar PDF** (versión oficial)
- 📄 **Crear nueva versión** (si necesitas cambios)

Acciones NO disponibles:
- ❌ Editar líneas
- ❌ Eliminar

---

### **En estado APROBADA** ⚫

Acciones disponibles:
- 👁️ **Ver líneas** (solo lectura)
- 📄 **Descargar PDF**
- 📊 **Ver en reportes**
- 📋 **Generar documentos** (albarán, contrato)

Acciones NO disponibles:
- ❌ Editar líneas
- ❌ Cambiar estado
- ❌ Eliminar
- ❌ Crear nueva versión (presupuesto cerrado)

---

### **En estado RECHAZADA** 🔴

Acciones disponibles:
- 👁️ **Ver líneas** (solo lectura)
- 📄 **Descargar PDF**
- 📄 **Crear nueva versión** (para reintentar)
- 📝 **Ver motivo de rechazo**

Acciones NO disponibles:
- ❌ Editar líneas
- ❌ Aprobar (ya fue rechazada)

---

## 🔔 Mensajes que verá el usuario

### **Al intentar editar versión bloqueada:**

```
⚠️ ADVERTENCIA

Esta versión está en estado "Enviada" y no puede modificarse.

Para realizar cambios:
1. Pulse el botón "Nueva Versión"
2. Indique el motivo de los cambios
3. Se copiará todo automáticamente
4. Podrá editar la nueva versión libremente

[📄 Crear Nueva Versión]  [❌ Cancelar]
```

---

### **Al crear nueva versión:**

```
📄 CREAR NUEVA VERSIÓN

Presupuesto: P-00025/2026
Cliente: Hotel Melia
Versión actual: v2

Motivo de la nueva versión: *
┌────────────────────────────────────┐
│ Cliente solicita 5 calefactores    │
│ adicionales y cambio de sillas     │
└────────────────────────────────────┘

Esta nueva versión copiará todas las líneas
de la versión actual para que pueda editarlas.

[✅ Crear Versión v3]  [❌ Cancelar]
```

---

### **Al enviar versión al cliente:**

```
📧 ¿ENVIAR AL CLIENTE?

¿Está seguro de enviar la Versión 2 al cliente?

⚠️ Al enviar:
• Se generará el PDF automáticamente
• La versión quedará BLOQUEADA (no editable)
• Solo podrá aprobar o rechazar

[📧 Sí, Enviar]  [❌ Cancelar]
```

---

### **Al aprobar versión:**

```
✅ ¿APROBAR VERSIÓN?

¿Está seguro de aprobar la Versión 2?

⚠️ Esta acción:
• Es DEFINITIVA (no se puede deshacer)
• Cierra el presupuesto completamente
• Genera documentos oficiales

[✅ Sí, Aprobar]  [❌ Cancelar]
```

---

### **Al rechazar versión:**

```
❌ RECHAZAR VERSIÓN

Motivo del rechazo: *
┌────────────────────────────────────┐
│ Cliente indica que el precio es    │
│ muy elevado                         │
└────────────────────────────────────┘

¿Desea crear una nueva versión inmediatamente?

[📄 Rechazar y Crear Nueva]  [❌ Solo Rechazar]
```

---

## 🎓 Tips para Usuarios

### **✅ Buenas Prácticas**

1. **Motivos claros**: Siempre escribe un motivo descriptivo al crear versión
   - ✅ Bueno: "Cliente solicita 50 sillas más y quita mesa imperial"
   - ❌ Malo: "Cambios"

2. **Revisar antes de enviar**: Una vez enviado, no hay vuelta atrás fácil
   - Verifica precios
   - Verifica cantidades
   - Revisa totales

3. **Usar comparador**: Antes de enviar nueva versión, compara con la anterior
   - Te aseguras de no olvidar cambios
   - Verificas que todo está correcto

4. **Histórico como documentación**: El historial es tu mejor defensa
   - Si hay conflicto con cliente: "Le enviamos la v2 el día X"
   - Trazabilidad completa de negociación

---

### **⚠️ Errores Comunes a Evitar**

1. ❌ **Crear versiones innecesarias**
   - Si aún estás en borrador, NO crees nueva versión
   - Solo crea cuando necesites partir de una versión enviada

2. ❌ **Motivos vacíos o genéricos**
   - El motivo es importante para auditoría
   - Será visible en reportes y histórico

3. ❌ **Aprobar sin revisar**
   - Una vez aprobado, NO hay vuelta atrás
   - Verifica dos veces antes de aprobar

4. ❌ **Confundir versión activa**
   - Siempre verifica qué versión estás editando
   - Mira el badge en la cabecera

---

## 🆘 Preguntas Frecuentes (FAQ)

### **P: ¿Puedo eliminar una versión?**
R: Solo si es borrador Y no tiene líneas. El resto se archiva, no se elimina.

### **P: ¿Qué pasa con el PDF de versiones antiguas?**
R: Se conservan todos. Cada versión tiene su propio PDF con marca de agua "Versión X".

### **P: ¿Puedo volver a activar una versión rechazada?**
R: No directamente. Debes crear una nueva versión desde ella.

### **P: ¿Cuántas versiones puedo crear?**
R: No hay límite técnico, pero se recomienda máximo 10-15 por presupuesto.

### **P: ¿El cliente ve todas las versiones?**
R: NO. El cliente solo ve la versión que tú le envíes. El histórico es interno.

### **P: ¿Puedo comparar versión 1 con versión 4?**
R: Sí, el comparador permite elegir cualquier par de versiones.

### **P: Si apruebo por error, ¿puedo deshacer?**
R: NO. La aprobación es definitiva. Por eso muestra advertencia antes.

### **P: ¿Las líneas se copian automáticamente?**
R: SÍ. Al crear nueva versión, TODO se copia (líneas, precios, cantidades, fechas).

---

## 📊 Ejemplo Completo con Timeline

```
📅 LÍNEA DE TIEMPO COMPLETA

┌─ 10/02/2026 ──────────────────────────────────────
│  👤 María crea presupuesto P-00025/2026
│  🆕 Sistema crea v1 automáticamente (Borrador)
│  
├─ 11/02/2026 ──────────────────────────────────────
│  ✏️ María añade 10 líneas de artículos
│  💰 Total: 3.500€
│  
├─ 12/02/2026 ──────────────────────────────────────
│  👔 Supervisor revisa y da OK
│  📧 María envía v1 al cliente (Estado: Enviada)
│  📄 PDF generado automáticamente
│  
├─ 13/02/2026 ──────────────────────────────────────
│  📞 Cliente llama: "Quiero 50 sillas más"
│  ❌ María intenta editar v1 → Bloqueada
│  📄 María crea v2 con motivo
│  ✏️ Modifica cantidades
│  💰 Nuevo total: 3.975€
│  
├─ 14/02/2026 ──────────────────────────────────────
│  📧 María envía v2 al cliente
│  
├─ 15/02/2026 ──────────────────────────────────────
│  📞 Cliente llama: "Perfecto, pero añade catering"
│  📄 María crea v3
│  ✏️ Añade 5 líneas de catering
│  💰 Nuevo total: 5.200€
│  
├─ 16/02/2026 ──────────────────────────────────────
│  📧 María envía v3 al cliente
│  📞 Cliente llama: "¡Aprobado!"
│  ✅ María aprueba v3
│  🎉 Presupuesto cerrado
│  
└─ RESULTADO ──────────────────────────────────────
   • 3 versiones creadas
   • 2 iteraciones con cliente
   • Histórico completo conservado
   • Presupuesto aprobado por 5.200€
```

---

## 🎯 Resumen Final

El sistema de versiones te permite:

1. ✅ **Trabajar con seguridad**: Nunca pierdes información
2. 🔄 **Iterar con el cliente**: Fácil hacer cambios sin caos
3. 📊 **Tener trazabilidad**: Sabes quién hizo qué y cuándo
4. 🛡️ **Proteger el trabajo**: Una vez enviado, nadie puede romper nada
5. 💼 **Ser profesional**: PDFs con versiones claras

**Es como Git, pero para presupuestos.**

---

**📌 Nota importante**: Este documento describe el funcionamiento desde el punto de vista del usuario. Para implementación técnica, consultar el documento `versiones_20260211.md`.

---

**Fecha de creación**: 16 de febrero de 2026  
**Estado**: Pendiente de implementación  
**Prioridad**: Alta  
**Tiempo estimado implementación**: 5 días laborables
