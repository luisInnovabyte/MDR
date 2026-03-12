# Lógica de columnas del Kanban de Servicios

**Ubicación del Kanban:** `view/CalendarioTecnicos/index.php`  
**Controller:** `controller/calendarioTecnicos.php` · operación `?op=listar`

---

## 1. Qué presupuestos se muestran

Solo se cargan presupuestos que cumplan **todas** estas condiciones:

| Condición | Detalle |
|-----------|---------|
| Estado | `codigo_estado_ppto = 'APROB'` — solo presupuestos aprobados |
| Activo | `activo_presupuesto = 1` — no eliminados (soft delete) |
| Rango temporal | Ver sección 2 |

### 1.1 Rango temporal

Se calcula desde SQL evaluando `fecha_inicio_evento_presupuesto` y `fecha_fin_evento_presupuesto`:

```
¿Tiene fecha de inicio?
│
├── NO  → Se incluye siempre (sin fecha asignada, pendiente de planificar)
│
└── SÍ  → Se incluye si:
           fecha_inicio <= HOY + 14 días
           AND
           IFNULL(fecha_fin, fecha_inicio) >= HOY - 3 días
```

**Efecto práctico:** La pantalla muestra servicios que empiezan en los próximos 14 días y también servicios que terminaron hace 3 días como máximo (para capturar desmontajes recientes aún activos en el taller).

---

## 2. Fechas de referencia usadas para clasificar

### 2.1 Fechas por línea de artículo (montaje/desmontaje)

Cada línea de artículo del presupuesto tiene sus propias fechas de trabajo:

| Campo en `linea_ppto` | Descripción |
|-----------------------|-------------|
| `fecha_montaje_linea_ppto` | Fecha en que se prevé montar **ese artículo concreto** |
| `fecha_desmontaje_linea_ppto` | Fecha en que se prevé desmontar **ese artículo concreto** |

Como un presupuesto puede tener **N artículos**, cada uno con fechas distintas, el controller las agrega para obtener el rango global del servicio:

```sql
MIN(vlp.fecha_montaje_linea_ppto)    AS fecha_montaje_min    -- día en que empieza el primer montaje
MAX(vlp.fecha_desmontaje_linea_ppto) AS fecha_desmontaje_max -- día en que termina el último desmontaje
```

**Ejemplo:** Un presupuesto con 3 artículos:

| Artículo | fecha_montaje | fecha_desmontaje |
|----------|--------------|-----------------|
| Carpa 10x20 | 14/03/2026 | 20/03/2026 |
| Iluminación | 15/03/2026 | 19/03/2026 |
| Sonido | 15/03/2026 | 20/03/2026 |

→ `fecha_montaje_min = 14/03/2026` · `fecha_desmontaje_max = 20/03/2026`

El Kanban trata el servicio como un **bloque completo**: empieza cuando llega el primer camión y termina cuando sale el último.

### 2.2 Fechas del evento (fallback)

Si los artículos no tienen fechas de montaje/desmontaje asignadas, se usan las fechas generales del presupuesto:

| Campo en `presupuesto` | Descripción |
|------------------------|-------------|
| `fecha_inicio_evento_presupuesto` | Fecha de inicio del evento o celebración |
| `fecha_fin_evento_presupuesto` | Fecha de fin del evento o celebración |

### 2.3 Prioridad

```
inicio_ref = fecha_montaje_min    ?? fecha_inicio_evento
fin_ref    = fecha_desmontaje_max ?? fecha_fin_evento ?? inicio_ref
```

Se usan **primero** las fechas técnicas de los artículos. Solo si no existen se recurre a las fechas del evento.

```
inicio_ref = fecha_montaje_min   ?? fecha_inicio_evento
fin_ref    = fecha_desmontaje_max ?? fecha_fin_evento ?? inicio_ref
```

---

## 3. Reglas de clasificación por columna

Una vez determinadas `inicio_ref` y `fin_ref`, se aplica la siguiente lógica (evaluada en PHP, comparando contra `$hoy = new DateTime('today')`):

```
¿inicio_ref existe?
│
├── NO  → columna = MONTAJE
│         (sin fechas → pendiente de planificar)
│
└── SÍ  → ¿inicio_ref > hoy?
           │
           ├── SÍ → columna = MONTAJE
           │        (el montaje aún no ha comenzado)
           │
           └── NO → ¿fin_ref >= hoy?
                    │
                    ├── SÍ → columna = EN CURSO
                    │        (el montaje ya empezó y el desmontaje no ha concluido)
                    │
                    └── NO → columna = DESMONTAJE
                             (el evento ya terminó, desmontaje reciente)
```

### Resumen visual

| Columna | Color | Condición |
|---------|-------|-----------|
| **Montaje** | Amarillo `warning` | Sin fechas, O `inicio_ref` es futuro (> hoy) |
| **En Curso** | Verde `success` | `inicio_ref` ≤ hoy Y `fin_ref` ≥ hoy |
| **Desmontaje** | Azul `info` | `inicio_ref` ≤ hoy Y `fin_ref` < hoy (terminó recientemente) |

---

## 4. Ejemplos concretos

Suponiendo que **hoy es 12/03/2026**:

| Evento | inicio_ref | fin_ref | Columna |
|--------|-----------|---------|---------|
| Sin fechas asignadas | — | — | **Montaje** |
| Evento el 20/03/2026 | 18/03/2026 | 21/03/2026 | **Montaje** (inicio es futuro) |
| Evento el 10/03/2026 | 08/03/2026 | 15/03/2026 | **En Curso** (empezó, no ha terminado) |
| Evento el 08/03/2026 | 06/03/2026 | 09/03/2026 | **Desmontaje** (terminó el 09, dentro del margen de 3 días) |
| Evento del 01/03/2026 | 28/02/2026 | 02/03/2026 | ❌ No aparece (terminó hace >3 días, excluido por el filtro SQL) |

---

## 5. Orden de las tarjetas dentro de cada columna

El SQL ordena los resultados antes de la clasificación:

```sql
ORDER BY
    (fecha_inicio_evento_presupuesto IS NULL) ASC,  -- primero los que tienen fecha
    fecha_inicio_evento_presupuesto ASC             -- luego por fecha más próxima
```

Los presupuestos **sin fecha** aparecen al final dentro de cada columna.

---

## 6. Refresco automático

El frontend (`calendario.js`) recarga los datos automáticamente cada **5 minutos** mediante `setInterval`. Un contador visual de cuenta atrás (`MM:SS`) en la barra superior indica cuánto tiempo falta para el próximo refresco.

---

## 7. Ficheros implicados

| Fichero | Responsabilidad |
|---------|----------------|
| `controller/calendarioTecnicos.php` | SQL de filtrado, cálculo de `inicio_ref`/`fin_ref` y asignación de columna |
| `view/CalendarioTecnicos/index.php` | Estructura HTML de las 3 columnas Kanban |
| `view/CalendarioTecnicos/calendario.js` | AJAX, renderizado de tarjetas, countdown y reloj |
