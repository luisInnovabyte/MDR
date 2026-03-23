# Sistema de Preparación de Material (Picking Móvil)

> Módulo: Salida de Almacén para Técnicos  
> Fecha de diseño: 10 de marzo de 2026  
> Estado: Pendiente de implementación

---

## Descripción general

Flujo móvil-first de "picking de almacén" que parte del escaneo QR del número de presupuesto aprobado, carga sus artículos necesarios, y registra los elementos físicos escaneados vía NFC (`codigo_elemento`).

**No existe ninguna infraestructura previa** de asignación presupuesto → elemento, por lo que todo es nuevo.

---

## Contexto técnico clave

- El presupuesto referencia `articulo` (tipo), **nunca** `elemento` (unidad física). La relación presupuesto→elemento no existe y hay que crearla.
- `codigo_elemento` (ej: `CAMARA-001`) generado por trigger = lo que llevará la etiqueta NFC. No se añade campo extra.
- La versión activa del presupuesto se obtiene por `presupuesto.version_actual_presupuesto` → `presupuesto_version`.
- Los KITs en el presupuesto tienen `tipo_linea_ppto = 'kit'` con hijos `componente_kit`. El técnico escanea el KIT como 1 sola unidad.
- Estado `ALQU` ya existe en `estado_elemento`. Se añadirá un estado intermedio `PREP` ("En preparación").

---

## Decisiones de diseño

| Decisión | Resolución |
|---|---|
| Campo NFC en elemento | Se usa `codigo_elemento` existente (ej: `CAMARA-001`). No se añade campo nuevo. |
| KITs compuestos | Se escanean como **1 sola unidad** (el KIT completo). Los `componente_kit` no generan escaneos independientes. |
| Material backup | Mismo flujo que el material normal, con `es_backup_linea_salida = 1`. |
| Devolución al almacén | Solo salida en esta fase. La devolución se deja para una fase posterior. |
| Identificación del técnico | Por `id_usuario` (tabla `usuarios` existente). Si no hay sesión activa se selecciona el nombre en la Fase 1. |
| Estado intermedio | `PREP` para elementos escaneados pero aún no despachados. Permite cancelar sin dejar elementos "fantasma" en `ALQU`. |

---

## Plan de implementación

### Paso 1 — Migración BD

**Archivo**: `BD/migrations/20260310_01_salida_almacen.sql`

#### 1.1 Nuevo estado de elemento: `PREP`

```sql
INSERT INTO estado_elemento (codigo_estado_elemento, descripcion_estado_elemento, color_estado_elemento, permite_alquiler_estado_elemento)
VALUES ('PREP', 'En preparación', '#795548', 0);
```

#### 1.2 Tabla `salida_almacen` (cabecera de cada operación de picking)

| Campo | Tipo | Notas |
|---|---|---|
| `id_salida_almacen` | INT UNSIGNED PK | |
| `id_presupuesto` | INT UNSIGNED NOT NULL | FK `presupuesto` |
| `id_version_presupuesto` | INT UNSIGNED NOT NULL | FK `presupuesto_version` |
| `id_usuario_salida` | INT NOT NULL | FK `usuarios` |
| `numero_presupuesto_salida` | VARCHAR(50) NOT NULL | Desnormalizado para histórico |
| `estado_salida` | ENUM('en_proceso','completada','cancelada') | DEFAULT 'en_proceso' |
| `fecha_inicio_salida` | DATETIME | |
| `fecha_fin_salida` | DATETIME | NULL hasta completar |
| `observaciones_salida` | TEXT | |
| `activo_salida_almacen` | BOOLEAN DEFAULT TRUE | Soft delete |
| `created_at_salida_almacen` | TIMESTAMP | |
| `updated_at_salida_almacen` | TIMESTAMP | |

Índices: `id_presupuesto`, `id_usuario_salida`, `estado_salida`.

#### 1.3 Tabla `linea_salida_almacen` (cada elemento físico escaneado)

| Campo | Tipo | Notas |
|---|---|---|
| `id_linea_salida` | INT UNSIGNED PK | |
| `id_salida_almacen` | INT UNSIGNED NOT NULL | FK |
| `id_elemento` | INT UNSIGNED NOT NULL | FK `elemento` |
| `id_articulo` | INT UNSIGNED NOT NULL | FK `articulo` (para validación y navegación) |
| `id_linea_ppto` | INT UNSIGNED | FK `linea_presupuesto` que está cubriendo |
| `es_backup_linea_salida` | TINYINT(1) DEFAULT 0 | 1 = material de backup |
| `orden_escaneo` | INT UNSIGNED | Orden cronológico de escaneo |
| `fecha_escaneo_linea_salida` | TIMESTAMP | |
| `observaciones_linea_salida` | TEXT | |
| `activo_linea_salida` | BOOLEAN DEFAULT TRUE | Soft delete |
| `created_at_linea_salida` | TIMESTAMP | |
| `updated_at_linea_salida` | TIMESTAMP | |

Índices: `id_salida_almacen`, `id_elemento`.

#### 1.4 Vista SQL `vista_progreso_salida`

JOIN entre `linea_presupuesto` + `linea_salida_almacen` que devuelve por `id_salida_almacen` y `id_articulo`:
- Cantidad necesaria según presupuesto
- Cuántos elementos escaneados (no backup)
- Cuántos backup
- Booleano `esta_completo`

---

### Paso 2 — Modelo

**Archivo**: `models/SalidaAlmacen.php`

| Método | Descripción |
|---|---|
| `__construct()` | PDO + RegistroActividad + timezone |
| `buscar_presupuesto_por_numero($numero)` | Busca por `numero_presupuesto`, devuelve presupuesto + versión activa + estado general |
| `get_necesidades_presupuesto($id_version)` | Líneas de tipo `articulo` y `kit` (excluye `componente_kit`, `seccion`, `texto`) con cantidad requerida |
| `get_salida_activa($id_presupuesto)` | Busca salida en estado `en_proceso` para ese presupuesto (evita duplicados) |
| `iniciar_salida($id_presupuesto, $id_version, $id_usuario, $numero)` | INSERT en `salida_almacen`, retorna `id_salida_almacen` |
| `escanear_elemento($id_salida, $codigo_elemento, $es_backup)` | **Método central** — ver lógica de validación abajo |
| `get_progreso_salida($id_salida)` | Usa `vista_progreso_salida`, devuelve necesidades y cobertura por artículo |
| `get_elementos_escaneados($id_salida)` | Lista de elementos ya escaneados con sus datos |
| `completar_salida($id_salida)` | Valida cobertura completa, cambia estados a `ALQU`, registra `fecha_fin_salida` |
| `cancelar_salida($id_salida)` | Revierte estados de elementos a `DISP`, elimina líneas, marca `cancelada` |
| `get_salidas_por_presupuesto($id_presupuesto)` | Histórico de salidas |

#### Lógica de validación de `escanear_elemento()`

1. Buscar el elemento por `codigo_elemento`
2. Verificar que el elemento existe y está en estado `DISP` o `TERC`
3. Verificar que el elemento NO está ya escaneado en esta salida
4. Verificar que `id_articulo_elemento` corresponde a alguna línea del presupuesto
5. Si `es_backup = false`: comprobar que aún quedan unidades sin cubrir para ese artículo
6. INSERT en `linea_salida_almacen`
7. Cambiar estado del elemento a `PREP`
8. Retornar resultado + progreso actualizado

---

### Paso 3 — Controller

**Archivo**: `controller/salida_almacen.php`

| Case (`$_GET["op"]`) | Método HTTP | Descripción |
|---|---|---|
| `buscar_presupuesto` | POST `numero_presupuesto` | Busca presupuesto, valida `estado_general = 'aprobado'`, devuelve datos + necesidades + salida activa si existe |
| `iniciar_salida` | POST `id_presupuesto, id_version, id_usuario` | Crea o retoma salida existente |
| `escanear` | POST `id_salida, codigo_elemento, es_backup` | Devuelve `{success, tipo, elemento, progreso}` |
| `progreso` | GET `id_salida` | Devuelve progreso actual |
| `elementos_escaneados` | GET `id_salida` | Lista de lo ya escaneado |
| `completar` | POST `id_salida` | Valida y completa la salida |
| `cancelar` | POST `id_salida` | Cancela y revierte estados |
| `listar` | GET | Para vista de administración (DataTables) |

#### Tipos de respuesta del case `escanear`

```json
{
  "success": true|false,
  "tipo": "correcto|backup|articulo_no_pertenece|ya_escaneado|no_disponible|elemento_no_encontrado",
  "elemento": { "codigo_elemento": "...", "nombre_articulo": "...", "estado": "..." },
  "progreso": { "por_articulo": [...], "total_requerido": 10, "total_escaneado": 7 }
}
```

---

### Paso 4 — Vista móvil

**Archivos**: `view/Picking/index.php` + `view/Picking/picking.js`

Página única (SPA ligera). Todo AJAX sin recargas. Bootstrap 5. Librería `html5-qrcode` para lectura de QR.

#### Fase 1 — Identificar presupuesto

- Campo de texto + botón "Activar cámara QR"
- Usa `html5-qrcode` para leer el QR con la cámara del móvil
- El QR contiene `numero_presupuesto`
- Fallback: introducción manual del número
- Al leer: AJAX a `buscar_presupuesto` y muestra resumen del evento (nombre, cliente, fechas)

#### Fase 2 — Lista de material

- Tarjetas de artículos con barra de progreso: "Cámara Sony: **2/3** escaneadas"
- Artículos completos → tarjeta verde
- Artículos pendientes → tarjeta naranja/roja
- Botón "Escanear elemento" fijo en la parte inferior de la pantalla

#### Fase 3 — Escanear elemento

- Activar lectura NFC vía **Web NFC API** (`NDEFReader`, solo Android Chrome)
- Fallback: campo de texto donde el lector NFC/RFID actúa como teclado
- El `codigo_elemento` leído → AJAX a `escanear`
- Respuesta `correcto` → flash verde, vibración, actualiza la lista
- Respuesta `articulo_no_pertenece` → flash rojo, alerta SweetAlert
- Artículo ya completo + técnico sigue escaneando → SweetAlert "¿Añadir como material de backup?"

#### Fase 4 — Confirmación

- Cuando todos los artículos están cubiertos: se activa botón "Completar preparación" (verde)
- Si hay artículos sin cubrir: el botón muestra advertencia pero permite forzar si el técnico lo decide
- SweetAlert de confirmación final
- Al completar: llama a `completar`, los elementos pasan de `PREP` a `ALQU`

#### UX específico para móvil

- Layout `fixed-bottom` para el botón de escaneo
- Fuente grande legible en condiciones de almacén (luz variable)
- Feedback visual inmediato (animación flash verde/rojo al escanear)
- Vibración del dispositivo: `navigator.vibrate([100])` en escaneo correcto, `navigator.vibrate([200, 100, 200])` en error

---

### Paso 5 — Acceso desde el presupuesto (baja prioridad)

En la vista del presupuesto, cuando `estado_general = 'aprobado'`, añadir botón "Preparar material" que:
- Genera/muestra el QR del `numero_presupuesto` en pantalla grande
- El técnico lo escanea directamente desde el monitor de oficina

---

## Plan de verificación

| Test | Resultado esperado |
|---|---|
| Escaneo QR en móvil (Android Chrome) con presupuesto `aprobado` | Carga datos del presupuesto y lista de material |
| Escanear `codigo_elemento` de artículo **que sí está** en el presupuesto | Actualiza el contador, flash verde |
| Escanear `codigo_elemento` de artículo **que NO está** en el presupuesto | Mensaje de error claro, no se registra |
| Escanear más unidades de las necesarias | SweetAlert preguntando si es backup |
| Escanear un elemento en estado `REPA` | Rechazado con mensaje, no se registra |
| Completar la salida | Todos los elementos de `PREP` → `ALQU` |
| Cancelar la salida | Todos los elementos vuelven a `DISP` |
| Acceso desde iOS Safari (sin Web NFC) | Fallback campo de texto funciona correctamente |

---

## Estructura de ficheros a crear

```
BD/
└── migrations/
    └── 20260310_01_salida_almacen.sql   ← Migración BD completa

models/
└── SalidaAlmacen.php                   ← Modelo

controller/
└── salida_almacen.php                  ← Controller

view/
└── Picking/
    ├── index.php                       ← Vista móvil
    └── picking.js                      ← Lógica JavaScript
```

---

*Última actualización: 10 de marzo de 2026 — Proyecto MDR ERP Manager*
