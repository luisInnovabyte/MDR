# Informe de Rotación de Inventario — Guía para Becario Programador

**Proyecto**: MDR ERP Manager  
**Pantalla**: `http://[servidor]/MDR/view/Informe_rotacion/index.php`  
**Fecha documento**: 12 de marzo de 2026  
**Autor**: GitHub Copilot — generado para incorporación de becario

---

## Índice

1. [Objetivo y contexto de negocio](#1-objetivo-y-contexto-de-negocio)
2. [Arquitectura general](#2-arquitectura-general)
3. [Mapa de archivos](#3-mapa-de-archivos)
4. [Base de datos — Vista SQL](#4-base-de-datos--vista-sql)
5. [API — Endpoints del controller](#5-api--endpoints-del-controller)
6. [Modelo — Métodos PHP](#6-modelo--métodos-php)
7. [Vista HTML — Widgets y IDs clave](#7-vista-html--widgets-y-ids-clave)
8. [JavaScript — Funciones y flujo](#8-javascript--funciones-y-flujo)
9. [Sistema semáforo de actividad](#9-sistema-semáforo-de-actividad)
10. [Permisos y acceso](#10-permisos-y-acceso)
11. [Checklist de certificación](#11-checklist-de-certificación)

---

## 1. Objetivo y contexto de negocio

Esta pantalla responde a una necesidad operativa del departamento de logística: **saber qué equipos de sonido/luz se alquilan con frecuencia y cuáles llevan meses en el almacén sin moverse**.

### Preguntas que responde

| Pregunta | Widget que la responde |
|---|---|
| ¿Cuántos artículos tiene el catálogo activo? | KPI "Artículos Activos" |
| ¿Cuántos se han alquilado en los últimos N días? | KPI "Usados en período" |
| ¿Qué porcentaje del catálogo está en circulación? | KPI "% Uso" |
| ¿Qué artículos llevan meses sin tocarlos? | KPI "Sin uso" + tabla detalle |
| ¿Cuáles son los 10 más demandados? | Gráfico de barras horizontal |
| ¿En qué estado está cada artículo concreto? | Tabla con semáforo por fila |

### Semáforo de actividad (regla de negocio central)

El informe clasifica cada artículo en 4 estados según los días transcurridos desde su último uso en un presupuesto aprobado:

| Badge | Color | Criterio |
|---|---|---|
| **Activo** | Verde | `dias_desde_ultimo_uso <= 30` |
| **Moderado** | Amarillo | `dias_desde_ultimo_uso <= 90` |
| **Inactivo** | Rojo | `dias_desde_ultimo_uso > 90` |
| **Nunca usado** | Gris | `total_usos == 0` |

> **Importante**: el semáforo se calcula en el **controller PHP** (no en JS). El frontend recibe el HTML del badge ya formado.

---

## 2. Arquitectura general

```
NAVEGADOR
    │
    ├── view/Informe_rotacion/index.php      ← HTML + includes de plantilla
    │       └── js/informerotacion.js        ← Lógica cliente (AJAX, Chart.js, DataTables)
    │
    │   (AJAX POST a ?op=...)
    │
    ├── controller/informerotacion.php       ← Switch de operaciones, sanitiza, genera badges
    │
    └── models/InformeRotacion.php           ← PDO, consultas contra la vista SQL
            │
            └── vista_rotacion_inventario    ← Vista MySQL (2.789 filas aprox.)
                    │
                    ├── articulo             ← catálogo
                    ├── familia              ← categoría del artículo
                    ├── linea_presupuesto    ← apariciones en presupuestos
                    ├── presupuesto_version  ← versión activa del presupuesto
                    ├── presupuesto          ← cabecera del presupuesto
                    └── estado_presupuesto   ← solo APROB cuenta como uso real
```

### Stack tecnológico

| Capa | Tecnología | Versión |
|---|---|---|
| Backend | PHP + PDO | 8.x |
| Base de datos | MySQL/MariaDB | — |
| CSS framework | Bootstrap | 5.0.2 |
| Tablas interactivas | DataTables | 2.2 |
| Gráficos | Chart.js | 4.4.0 (CDN) |
| jQuery | jQuery | 3.7.1 |
| Alertas | SweetAlert2 | 11.7.32 |

---

## 3. Mapa de archivos

| Ruta | Tipo | Responsabilidad |
|---|---|---|
| `view/Informe_rotacion/index.php` | Vista HTML | Estructura de página, includes de plantilla, HTML de widgets |
| `view/Informe_rotacion/js/informerotacion.js` | JavaScript | AJAX, Chart.js, DataTables, filtros |
| `controller/informerotacion.php` | Controller PHP | Switch por `?op=`, sanitización, cálculo de semáforo, JSON |
| `models/InformeRotacion.php` | Modelo PHP | Consultas PDO contra `vista_rotacion_inventario` |
| `BD/migrations/20260317_02_create_vista_rotacion_inventario.sql` | SQL | DDL de la vista, documentado con los JOINs |
| `config/template/mainHead.php` | Plantilla | `<head>` con CSS y meta |
| `config/template/mainLogo.php` | Plantilla | Logo y barra superior |
| `config/template/mainSidebar.php` | Plantilla | Menú lateral (requiere `bracket.js`) |
| `config/template/mainSidebarDown.php` | Plantilla | Parte inferior del sidebar |
| `config/template/mainHeader.php` | Plantilla | Cabecera de página |
| `config/template/mainRightPanel.php` | Plantilla | Panel derecho deslizable |
| `config/template/mainFooter.php` | Plantilla | Pie de página |
| `config/template/mainJs.php` | Plantilla | jQuery + Bootstrap + DataTables + **bracket.js** |

### Orden de carga de scripts (crítico)

```html
<!-- Al final del body, en este orden exacto: -->
<?php include_once('../../config/template/mainJs.php') ?>            <!-- 1º: jQuery, Bootstrap, bracket.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/..."></script>  <!-- 2º: Chart.js -->
<script src="js/informerotacion.js"></script>                             <!-- 3º: Lógica propia -->
```

> Si `mainJs.php` no se carga, el sidebar queda inerte (bracket.js es el motor de interactividad del sidebar).

---

## 4. Base de datos — Vista SQL

### Nombre: `vista_rotacion_inventario`

**Archivo de migración**: `BD/migrations/20260317_02_create_vista_rotacion_inventario.sql`  
**Filas aproximadas**: 2.789 (un registro por artículo activo del catálogo)

### Cadena de JOINs

```sql
articulo a
  LEFT JOIN familia f                  ON a.id_familia = f.id_familia
  LEFT JOIN linea_presupuesto lp       ON a.id_articulo = lp.id_articulo
                                          AND lp.activo_linea_ppto = 1
                                          AND lp.tipo_linea_ppto = 'articulo'
  LEFT JOIN presupuesto_version pv     ON lp.id_version_presupuesto = pv.id_version_presupuesto
  LEFT JOIN presupuesto p              ON pv.id_presupuesto = p.id_presupuesto
                                          AND p.activo_presupuesto = 1
                                          AND pv.numero_version_presupuesto = p.version_actual_presupuesto
  LEFT JOIN estado_presupuesto ep      ON p.id_estado_ppto = ep.id_estado_ppto
                                          AND ep.codigo_estado_ppto = 'APROB'
WHERE a.activo_articulo = 1
GROUP BY a.id_articulo, ...
```

> Todos los JOINs son `LEFT JOIN`. Esto garantiza que los artículos que **nunca han sido alquilados** también aparecen en la vista con `total_usos = 0` y `ultimo_uso = NULL`.

> Solo cuenta como **uso real** un presupuesto con estado `APROB` (aprobado). Los presupuestos en borrador, pendientes o cancelados no incrementan `total_usos`.

### Columnas de la vista

| Columna | Tipo | Descripción |
|---|---|---|
| `id_articulo` | INT | PK del artículo |
| `codigo_articulo` | VARCHAR | Código alfanumérico del artículo |
| `nombre_articulo` | VARCHAR | Nombre del artículo |
| `precio_alquiler_articulo` | DECIMAL | Precio de alquiler diario |
| `activo_articulo` | BOOL | Siempre 1 (filtrado en WHERE) |
| `id_familia` | INT | FK de familia (0 si no tiene) |
| `nombre_familia` | VARCHAR | Nombre de familia ('Sin familia' si no tiene) |
| `codigo_familia` | VARCHAR | Código de familia ('--' si no tiene) |
| `total_usos` | INT | Nº de presupuestos APROB donde ha aparecido |
| `ultimo_uso` | DATE | Fecha del último presupuesto APROB con este artículo |
| `dias_desde_ultimo_uso` | INT | `DATEDIFF(CURDATE(), MAX(fecha_presupuesto))` — NULL si total_usos=0 |
| `total_unidades_alquiladas` | INT | Suma de `cantidad_linea_ppto` en todos los presupuestos APROB |

### Consultas de verificación rápida

```sql
-- Top 10 más usados
SELECT nombre_articulo, total_usos, ultimo_uso
FROM vista_rotacion_inventario
ORDER BY total_usos DESC LIMIT 10;

-- Artículos nunca alquilados
SELECT nombre_articulo, nombre_familia
FROM vista_rotacion_inventario
WHERE total_usos = 0
ORDER BY nombre_familia, nombre_articulo;

-- Artículos inactivos > 90 días
SELECT nombre_articulo, dias_desde_ultimo_uso
FROM vista_rotacion_inventario
WHERE total_usos > 0 AND dias_desde_ultimo_uso > 90
ORDER BY dias_desde_ultimo_uso DESC;

-- Conteo por estado semáforo
SELECT
  SUM(total_usos = 0)                                          AS nunca_usado,
  SUM(total_usos > 0 AND dias_desde_ultimo_uso <= 30)          AS activo,
  SUM(total_usos > 0 AND dias_desde_ultimo_uso BETWEEN 31 AND 90) AS moderado,
  SUM(total_usos > 0 AND dias_desde_ultimo_uso > 90)           AS inactivo
FROM vista_rotacion_inventario;
```

---

## 5. API — Endpoints del controller

**Archivo**: `controller/informerotacion.php`  
**Método HTTP**: POST con `?op=<operacion>` en la URL

### `?op=kpis`

**Propósito**: Carga los 4 KPI cards superiores.

| Parámetro POST | Tipo | Default | Descripción |
|---|---|---|---|
| `dias_periodo` | int | 90 | Ventana de análisis en días (90, 180, 365 o 0=histórico) |

**Respuesta JSON**:
```json
{
  "success": true,
  "data": {
    "total_articulos": 145,
    "articulos_usados": 89,
    "articulos_sin_uso": 56,
    "porcentaje_uso": 61.4
  }
}
```

---

### `?op=top_articulos`

**Propósito**: Datos para el gráfico de barras horizontal (Top N artículos).

| Parámetro POST | Tipo | Default | Descripción |
|---|---|---|---|
| `dias_periodo` | int | 0 | 0 = sin límite temporal |
| `limite` | int | 10 | Nº de artículos a devolver |

**Respuesta JSON**:
```json
{
  "success": true,
  "data": [
    {
      "id_articulo": 12,
      "nombre_articulo": "Foco LED PAR 64",
      "nombre_familia": "Iluminación",
      "total_usos": 47,
      "total_unidades_alquiladas": 312
    },
    ...
  ]
}
```

> **Nota**: el controller devuelve un array directo (no anidado en `data`) cuando la respuesta es `resp` en JS. La función `cargarGrafico` usa `if (!Array.isArray(resp))` para comprobarlo.

---

### `?op=tabla_rotacion`

**Propósito**: Carga la tabla DataTables con todos los artículos filtrados.

| Parámetro POST | Tipo | Default | Descripción |
|---|---|---|---|
| `id_familia` | int | null | Filtro opcional por familia |
| `dias_periodo` | int | 0 | 0 = incluye todos; >0 = filtra por período |
| `draw` | int | 1 | Parámetro estándar de DataTables |

**Respuesta JSON** (formato DataTables):
```json
{
  "draw": 1,
  "recordsTotal": 280,
  "recordsFiltered": 280,
  "data": [
    {
      "codigo_articulo": "FOC-001",
      "nombre_articulo": "Foco LED PAR 64",
      "nombre_familia": "Iluminación",
      "total_usos": 47,
      "total_unidades_alquiladas": 312,
      "ultimo_uso": "2026-02-15",
      "dias_desde_ultimo_uso": 25,
      "estado_rotacion": "<span class=\"badge bg-success\">Activo</span>"
    },
    ...
  ]
}
```

> El campo `estado_rotacion` viene como **HTML ya generado** por PHP. El semáforo se calcula en el controller, no en el cliente.

---

### `?op=familias`

**Propósito**: Poblar el selector desplegable de familias.

No requiere parámetros POST.

**Respuesta JSON**:
```json
{
  "success": true,
  "data": [
    { "id_familia": 3, "nombre_familia": "Iluminación" },
    { "id_familia": 5, "nombre_familia": "Sonido" },
    ...
  ]
}
```

---

## 6. Modelo — Métodos PHP

**Archivo**: `models/InformeRotacion.php`  
**Clase**: `InformeRotacion`

### Constructor

```php
public function __construct()
```

- Instancia la conexión PDO desde `Conexion()`
- Instancia `RegistroActividad()`
- Configura la zona horaria a `Europe/Madrid`

---

### `getKpisRotacion(int $diasPeriodo = 90): array`

Devuelve los 4 KPIs. Usa **dos consultas**:
1. `SELECT COUNT(*) FROM articulo WHERE activo_articulo = 1` → total del catálogo
2. Si `$diasPeriodo > 0`: cuenta con `SUM(CASE WHEN dias_desde_ultimo_uso <= ?)` sobre la vista
3. Si `$diasPeriodo == 0`: cuenta directamente `total_usos > 0 / = 0` en la vista

**Retorna**:
```php
[
  'total_articulos'   => int,
  'articulos_usados'  => int,
  'articulos_sin_uso' => int,
  'porcentaje_uso'    => float,
]
```

---

### `getTopArticulos(int $diasPeriodo = 0, int $limite = 10): array`

Consulta sobre `vista_rotacion_inventario` con `WHERE total_usos > 0`.  
Si `$diasPeriodo > 0`, añade `AND dias_desde_ultimo_uso <= $diasPeriodo`.  
Ordena por `total_usos DESC LIMIT $limite`.

**Retorna**: array de filas con `{id_articulo, nombre_articulo, nombre_familia, total_usos, total_unidades_alquiladas}`

---

### `getTablaRotacion(array $filtros = []): array`

Consulta dinámica con filtros opcionales:
- `$filtros['id_familia']` → `AND id_familia = ?`
- `$filtros['dias_periodo']` → `AND (total_usos = 0 OR dias_desde_ultimo_uso <= ?)` — los artículos nunca usados siempre se incluyen

Ordena por `total_usos DESC, nombre_articulo ASC`.

**Retorna**: array completo de todas las columnas de la vista.

---

### `getFamilias(): array`

```sql
SELECT DISTINCT id_familia, nombre_familia
FROM vista_rotacion_inventario
WHERE id_familia > 0
ORDER BY nombre_familia ASC
```

**Retorna**: array `[{id_familia, nombre_familia}]` — solo familias con artículos activos.

---

## 7. Vista HTML — Widgets y IDs clave

**Archivo**: `view/Informe_rotacion/index.php`

### Filtros

| ID | Tipo | Valores posibles | Descripción |
|---|---|---|---|
| `#filtroPeriodo` | `<select>` | 90, 180, 365, 0 | Ventana temporal de análisis |
| `#filtroFamilia` | `<select>` | "" (todas) + ids dinámicos | Filtro por familia de artículo |
| `.btn btn-primary` | Botón | — | Llama `aplicarFiltros()` |
| `.btn btn-outline-secondary` (Limpiar) | Botón | — | Llama `limpiarFiltros()` |
| `.btn btn-sm btn-outline-secondary` (Actualizar) | Botón | — | Llama `recargarTodo()` |

### KPI Cards

| ID | Color del borde | Valor mostrado | Unidad |
|---|---|---|---|
| `#kpi-total` | Gris | Total artículos activos | número entero |
| `#kpi-usados` | Verde | Artículos usados en período | número entero |
| `#kpi-pct` | Azul | Porcentaje de uso | `xx.x %` |
| `#kpi-sin-uso` | Rojo | Artículos sin uso | número entero |
| `#kpi-periodo-sub` | — | Texto del período activo | texto dinámico |

### Gráfico

| ID | Tipo | Librería | Configuración |
|---|---|---|---|
| `#chartTopArticulos` | Canvas | Chart.js 4.4.0 | `indexAxis: 'y'` (barras horizontales), max-height 320px |

### Tabla

| ID | Tipo | Columnas | Orden por defecto |
|---|---|---|---|
| `#tblRotacion` | DataTable | 8 columnas (ver abajo) | `total_usos DESC` |

**Columnas de `#tblRotacion`**:

| Col. | Campo | Ancho | Ordenable |
|---|---|---|---|
| 0 | `codigo_articulo` | 10% | Sí |
| 1 | `nombre_articulo` | auto | Sí |
| 2 | `nombre_familia` | 14% | Sí |
| 3 | `total_usos` | 8% | Sí (numérico) |
| 4 | `total_unidades_alquiladas` | 9% | Sí (numérico) |
| 5 | `ultimo_uso` | 11% | Sí |
| 6 | `dias_desde_ultimo_uso` | 9% | Sí (numérico) |
| 7 | `estado_rotacion` | 10% | **No** (es HTML) |

---

## 8. JavaScript — Funciones y flujo

**Archivo**: `view/Informe_rotacion/js/informerotacion.js`

### Variables globales

```javascript
const CTRL     = '../../controller/informerotacion.php';
let tablaRotacion = null;   // instancia DataTables
let graficoTop    = null;   // instancia Chart.js
```

> Estas variables globales permiten actualizar el gráfico sin destruirlo y destruir la tabla antes de reinicializarla.

### Flujo de inicialización

```
$(document).ready()
    ├── cargarFamilias()   → ?op=familias → pobla #filtroFamilia
    └── recargarTodo()
            ├── actualizarSubPeriodo(dias)  → actualiza texto #kpi-periodo-sub
            ├── cargarKPIs(dias)            → ?op=kpis → actualiza 4 KPIs
            ├── cargarGrafico(dias)         → ?op=top_articulos → Chart.js
            └── inicializarTabla(dias, familia) → ?op=tabla_rotacion → DataTables
```

### Referencia de funciones

| Función | Parámetros | Descripción |
|---|---|---|
| `aplicarFiltros()` | — | Solo llama a `recargarTodo()` |
| `limpiarFiltros()` | — | Resetea `#filtroPeriodo`=90, `#filtroFamilia`='', llama `recargarTodo()` |
| `recargarTodo()` | — | Lee filtros actuales y lanza las 4 cargas en paralelo |
| `actualizarSubPeriodo(dias)` | `dias: int` | Actualiza el texto bajo el KPI "Usados en período" |
| `cargarFamilias()` | — | POST `?op=familias`, popula `<select id="filtroFamilia">` |
| `cargarKPIs(dias)` | `dias: int` | POST `?op=kpis`, actualiza los 4 `#kpi-*` |
| `cargarGrafico(dias)` | `dias: int` | POST `?op=top_articulos`, actualiza o crea la instancia `graficoTop` |
| `inicializarTabla(dias, familia)` | `dias: int, familia: string` | Destruye y reinicializa `tablaRotacion` |

### Patrón Chart.js (update vs create)

```javascript
if (graficoTop) {
    graficoTop.data.labels           = labels;
    graficoTop.data.datasets[0].data = datos;
    graficoTop.update();
    return;            // ← no crea una nueva instancia
}
// Si no existe, la crea:
graficoTop = new Chart(ctx, { ... });
```

> Este patrón es importante: si se destruyera y recreara el canvas en cada actualización, habría parpadeos y posibles fugas de memoria.

### Patrón DataTables (destroy + recreate)

A diferencia del gráfico, la tabla **sí se destruye y recrea** con `inicializarTabla()`:

```javascript
if (tablaRotacion) {
    tablaRotacion.destroy();
    tablaRotacion = null;
    $('#tblRotacion tbody').empty();
}
tablaRotacion = $('#tblRotacion').DataTable({ ... });
```

> Esto es necesario porque los parámetros POST cambian con cada filtro.

### Idioma DataTables

```javascript
language: {
    url: '../../public/lib/DataTables/es-ES.json'
}
```

---

## 9. Sistema semáforo de actividad

La clasificación de actividad de un artículo es la **regla de negocio más importante** de esta pantalla. Se aplica en el **controller PHP** durante el case `tabla_rotacion`:

```php
if ($row['total_usos'] == 0) {
    $badge = '<span class="badge bg-secondary">Nunca usado</span>';
} elseif ($diasDesdeUso !== null && $diasDesdeUso <= 30) {
    $badge = '<span class="badge bg-success">Activo</span>';
} elseif ($diasDesdeUso !== null && $diasDesdeUso <= 90) {
    $badge = '<span class="badge bg-warning text-dark">Moderado</span>';
} else {
    $badge = '<span class="badge bg-danger">Inactivo</span>';
}
```

**Nota de diseño**: esta lógica podría haberse puesto en JS, pero se colocó deliberadamente en PHP para:
- Centralizar la regla de negocio
- Facilitar su cambio sin tocar el frontend
- Permitir que otros consumidores del controller (ej. exportación a PDF) usen la misma lógica

La leyenda visual se muestra en la barra de filtros (`#barraFiltros`) con los mismos colores.

---

## 10. Permisos y acceso

El menú de la pantalla se configura en `config/template/mainSidebar.php`.

```php
'informes' => [2, 3, 4],   // Roles que ven el menú Informes
```

La entrada de navegación en el sidebar apunta a:
```
../Informe_rotacion/index.php
```

Con el texto **"Rotación de Inventario"** bajo el grupo **"Informes"**.

---

## 11. Checklist de certificación

El becario debe marcar cada ítem para certificar que la pantalla funciona correctamente. Los bloques A-C son funcionales, D-E técnicos, F-G de robustez.

---

### Bloque A — Carga inicial de la página

- [ ] **A1** La página carga sin errores de consola del navegador (F12 → Console)
- [ ] **A2** Los 4 KPIs muestran valores numéricos (no `—` ni `Error`) tras la carga
- [ ] **A3** El gráfico de barras horizontal se renderiza con al menos 1 barra
- [ ] **A4** La tabla se puebla con filas de artículos
- [ ] **A5** El selector `#filtroFamilia` contiene opciones además de "Todas las familias"
- [ ] **A6** El sidebar es interactivo (puede expandirse/colapsarse)

---

### Bloque B — Filtros

- [ ] **B1** Cambiar el período a "Últimos 180 días" y pulsar **Filtrar** actualiza los KPIs
- [ ] **B2** El subtítulo del KPI "Usados en período" cambia al texto correcto según el período seleccionado
- [ ] **B3** Seleccionar una familia específica y filtrar: la tabla muestra solo artículos de esa familia
- [ ] **B4** Seleccionar período **"Histórico completo"** (valor 0) devuelve datos sin restricción temporal
- [ ] **B5** El botón **Limpiar** restablece `#filtroPeriodo` a 90 y `#filtroFamilia` a "Todas las familias"
- [ ] **B6** El botón **Actualizar** (arriba a la derecha) refresca los datos con los filtros actuales

---

### Bloque C — Semáforo y lógica de negocio

- [ ] **C1** Existen filas con badge **"Nunca usado"** (gris) — artículos con 0 usos
- [ ] **C2** Existen filas con badge **"Activo"** (verde) — usados hace ≤ 30 días
- [ ] **C3** Existen filas con badge **"Inactivo"** (rojo) — usados hace > 90 días
- [ ] **C4** Un artículo con `total_usos = 0` tiene `ultimo_uso = —` y `dias_desde_ultimo_uso = —` en la tabla
- [ ] **C5** La leyenda del semáforo visible en `#barraFiltros` coincide con los colores de la tabla
- [ ] **C6** El total de artículos del KPI "Artículos Activos" es consistente con `SELECT COUNT(*) FROM articulo WHERE activo_articulo = 1`

---

### Bloque D — Gráfico

- [ ] **D1** El gráfico muestra barras **horizontales** (los artículos en eje Y, usos en eje X)
- [ ] **D2** El gráfico muestra exactamente 10 artículos (o menos si hay menos con usos)
- [ ] **D3** Los nombres de artículos largos se truncan con `…` en el eje Y
- [ ] **D4** Al cambiar el período y filtrar, el gráfico se **actualiza** (no se duplica ni aparece en blanco)
- [ ] **D5** El tooltip del gráfico muestra el nº de usos (ej. `47 usos`)

---

### Bloque E — Tabla DataTables

- [ ] **E1** La tabla muestra las 8 columnas: Código, Artículo, Familia, Nº Usos, Uds. totales, Último uso, Días sin uso, Estado
- [ ] **E2** La tabla está ordenada por "Nº Usos" descendente al cargar
- [ ] **E3** Se puede hacer clic en las cabeceras de columna para reordenar (excepto "Estado")
- [ ] **E4** La tabla muestra 25 filas por página por defecto
- [ ] **E5** El buscador de DataTables filtra correctamente por nombre de artículo o familia
- [ ] **E6** Los textos de paginación, búsqueda y contador están en **español**
- [ ] **E7** Al cambiar filtros y pulsar **Filtrar**, la tabla se destruye y recarga (no se duplican filas)

---

### Bloque F — Red y API

- [ ] **F1** En la pestaña Network (F12), las llamadas AJAX devuelven status 200
- [ ] **F2** La llamada `?op=kpis` devuelve JSON con `success: true` y los 4 campos de datos
- [ ] **F3** La llamada `?op=top_articulos` devuelve un array con `total_usos > 0` en todos los elementos
- [ ] **F4** La llamada `?op=tabla_rotacion` devuelve JSON con las claves `draw`, `recordsTotal`, `recordsFiltered`, `data`
- [ ] **F5** La llamada `?op=familias` devuelve `success: true` y un array con al menos una familia
- [ ] **F6** No aparecen errores PHP (warnings, notices) en los encabezados de respuesta

---

### Bloque G — Consistencia de datos

- [ ] **G1** El número de artículos en el KPI "Artículos Activos" es igual o mayor que la suma de "Usados" + "Sin uso" (pueden solaparse por el criterio de período)
- [ ] **G2** El nº de filas totales de la tabla sin filtros coincide aproximadamente con el catálogo activo
- [ ] **G3** Cambiar el filtro de familia reduce el número de filas de la tabla (si hay varias familias)
- [ ] **G4** El campo "Días sin uso" de una fila con `ultimo_uso` reciente es un número pequeño (coherente con la fecha)
- [ ] **G5** Los artículos del Top 10 del gráfico coinciden con las primeras filas de la tabla ordenada por Nº Usos

---

*Fin del documento — MDR ERP Manager — Informe de Rotación de Inventario*
