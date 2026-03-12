# Informe de Ventas por Período

> **Módulo**: Informes → Ventas por Período  
> **URL**: `http://192.168.31.19/MDR/view/Informe_ventas/index.php`  
> **Rama Git activa**: `elementos`  
> **Fecha de referencia**: 12 de marzo de 2026  
> **Autor del módulo**: Equipo MDR / InnovaByte

---

## 1. Objetivo de la pantalla

Esta pantalla es un **dashboard analítico de ventas** dirigido a dirección y comercial.  
Consolida, en una sola vista, toda la información económica de los presupuestos en estado **APROB** (aprobados/aceptados), permitiendo filtrar por **año** y **mes** para responder preguntas como:

- ¿Cuánto hemos facturado este año?
- ¿Cuál ha sido nuestro mejor mes?
- ¿Qué clientes generan más negocio?
- ¿Qué familia de artículos mueve más dinero?

> **Importante**: "Facturado" en este contexto significa el valor económico de las líneas de artículos de presupuestos aprobados, no necesariamente facturas emitidas. El módulo de facturación es independiente.

---

## 2. Arquitectura y flujo de datos

```
Navegador (Bootstrap 5 + Chart.js + DataTables)
    │  AJAX POST con { anyo, mes }
    ▼
controller/informeventas.php   ← único punto de entrada
    │  ?op=kpis | grafico_mensual | top_clientes | por_familia | anyos_disponibles
    ▼
models/InformeVentas.php       ← acceso a datos (PDO)
    │
    ▼
MySQL VIEW: vista_ventas_periodo  ← calculada, no editable
    │
    ▼  (JOINs internos de la vista)
  presupuesto → presupuesto_version → linea_presupuesto → articulo → familia
  presupuesto → cliente
  presupuesto → estado_presupuesto  (solo APROB)
```

---

## 3. Mapa de archivos

| Archivo | Tipo | Responsabilidad |
|---|---|---|
| `view/Informe_ventas/index.php` | Vista (HTML+PHP) | Estructura de la página, filtros, cards, canvas, tablas |
| `view/Informe_ventas/js/informeventas.js` | JS | Toda la lógica AJAX, Chart.js, DataTables, filtros |
| `controller/informeventas.php` | Controller (PHP) | Recibe peticiones AJAX, valida POST, llama al modelo |
| `models/InformeVentas.php` | Modelo (PHP) | Consultas SQL contra `vista_ventas_periodo` |
| `BD/migrations/20260317_01_create_vista_ventas_periodo.sql` | SQL | Definición de la vista (documentación) |
| `config/template/mainHead.php` | Template | CSS y meta del proyecto |
| `config/template/mainJs.php` | Template | jQuery, Bootstrap, DataTables, bracket.js (sidebar) |
| `config/template/mainSidebar.php` | Template | Menú lateral — contiene la entrada "Informes" |

### Dependencias JS cargadas en la vista (orden de carga)

```html
<!-- 1. Templates del proyecto (incluidos vía mainJs.php) -->
jQuery 3.7.1  →  Bootstrap 5.3  →  DataTables 2.2  →  SweetAlert2  →  bracket.js

<!-- 2. Cargados directamente en la vista -->
Chart.js 4.4.0  (CDN)
js/informeventas.js
```

> **Regla crítica**: `informeventas.js` debe cargarse **después** de Chart.js y **después** de `mainJs.php`.  
> Si se rompe ese orden → gráfico no renderiza / sidebar no responde.

---

## 4. Vista SQL: `vista_ventas_periodo`

Esta vista es la **única fuente de datos** del informe. Se crea con el script de migración y **no se modifica manualmente**.

### Columnas disponibles

| Columna | Tipo | Descripción |
|---|---|---|
| `id_presupuesto` | INT | PK del presupuesto |
| `numero_presupuesto` | VARCHAR | Número legible (ej: P-2026-001) |
| `fecha_presupuesto` | DATE | Fecha del presupuesto |
| `anyo_presupuesto` | INT | Año extraído de la fecha (YEAR()) |
| `mes_presupuesto` | INT | Mes extraído de la fecha (MONTH()) — 1 a 12 |
| `nombre_evento_presupuesto` | VARCHAR | Nombre del evento |
| `id_cliente` | INT | FK al cliente |
| `nombre_completo_cliente` | VARCHAR | Nombre del cliente (nombre_cliente) |
| `id_familia` | INT | FK a la familia de artículo |
| `nombre_familia` | VARCHAR | Nombre de la familia |
| `codigo_familia` | VARCHAR | Código interno |
| `id_articulo` | INT | FK al artículo |
| `nombre_articulo` | VARCHAR | Nombre del artículo |
| `codigo_articulo` | VARCHAR | Código del artículo |
| `cantidad_linea_ppto` | INT | Unidades alquiladas en la línea |
| `precio_linea_ppto` | DECIMAL | Precio unitario |
| `descuento_linea_ppto` | DECIMAL | Descuento en % |
| `jornadas_linea_ppto` | INT | Número de jornadas |
| `total_linea_ppto` | DECIMAL | `cantidad × precio × (1 − desc/100) × jornadas` |

### Filtros internos de la vista (no modificables desde el informe)

```sql
WHERE p.activo_presupuesto  = 1
  AND v.activo_version      = 1
  AND v.numero_version_presupuesto = p.version_actual_presupuesto
  AND lp.activo_linea_ppto  = 1
  AND lp.tipo_linea_ppto    = 'articulo'
  AND ep.codigo_estado_ppto = 'APROB'
```

---

## 5. API del Controller

Todos los endpoints reciben `POST` (parámetros en body) y el tipo de operación en `?op=`.

| `?op=` | Parámetros POST | Respuesta | Descripción |
|---|---|---|---|
| `kpis` | `anyo` (int, 0=todos), `mes` (int, 0=todos) | `{success, data:{total_facturado, num_presupuestos, ticket_promedio, mes_top, mes_top_total}}` | KPI cards |
| `grafico_mensual` | `anyo` (int) | `{success, data:[{mes, total}×12]}` | 12 barras del gráfico |
| `top_clientes` | `anyo`, `mes`, `limite` (int, def:10) | DataTables format + `data[]` | Ranking clientes |
| `por_familia` | `anyo`, `mes` | DataTables format + `data[]` | Agrupación por familia |
| `anyos_disponibles` | (ninguno) | `{success, data:[{anyo}]}` | Para poblar el `<select>` año |

> El endpoint `grafico_mensual` **no recibe `mes`** intencionadamente: el gráfico siempre muestra los 12 meses del año para dar contexto. Solo los KPIs y las tablas filtran por mes.

---

## 6. Modelo: `InformeVentas.php`

### Métodos públicos

```php
getKpisVentas(int $anyo = 0, int $mes = 0): array
getVentasPorMes(int $anyo): array          // siempre 12 filas (meses sin datos → 0)
getTopClientes(int $anyo, int $limite = 10, int $mes = 0): array
getVentasPorFamilia(int $anyo, int $mes = 0): array
getAnyosDisponibles(): array
```

### Patrón de filtro dinámico

Todos los métodos que aceptan `$anyo` y `$mes` usan el mismo patrón:

```php
$params = [];
$where  = [];

if ($anyo > 0) { $where[] = 'anyo_presupuesto = ?'; $params[] = $anyo; }
if ($mes  > 0) { $where[] = 'mes_presupuesto  = ?'; $params[] = $mes;  }

if (!empty($where)) {
    $sql .= ' WHERE ' . implode(' AND ', $where);
}
```

---

## 7. Vista (index.php): widgets y sus IDs

| Widget | ID HTML | Alimentado por |
|---|---|---|
| KPI Total Facturado | `#kpi-total` | `?op=kpis` |
| KPI Presupuestos | `#kpi-pptos` | `?op=kpis` |
| KPI Ticket Promedio | `#kpi-ticket` | `?op=kpis` |
| KPI Mejor Mes | `#kpi-mes-top` | `?op=kpis` |
| Selector de año | `#filtroAnyo` | `?op=anyos_disponibles` |
| Selector de mes | `#filtroMes` | Estático (opciones 1–12 hardcoded) |
| Canvas gráfico mensual | `#chartVentasMensuales` | `?op=grafico_mensual` |
| Etiqueta año gráfico | `#lblGraficoAnyo` | JS local |
| Tabla Top Clientes | `#tblTopClientes` | `?op=top_clientes` |
| Tabla Por Familia | `#tblPorFamilia` | `?op=por_familia` |

---

## 8. JS: `informeventas.js` — funciones principales

```javascript
cargarAnyos()                              // Llena #filtroAnyo y arranca todo
recargarTodo()                             // Coordina los 4 bloques
cargarKPIs(anyo, mes)                      // AJAX → #kpi-*
cargarGrafico(anyo)                        // Chart.js — sin filtro de mes
inicializarTablaClientes(anyo, mes)        // DataTable destroy+init
inicializarTablaFamilia(anyo, mes)         // DataTable destroy+init
aplicarFiltros()                           // → recargarTodo()
limpiarFiltros()                           // Resetea #filtroAnyo=#filtroMes=0 → recargarTodo()
formatEuro(valor)                          // Formatea número → "1.234,56 €"
anyoActual()                               // Lee #filtroAnyo como entero
mesActual()                                // Lee #filtroMes como entero
```

### Gestión del gráfico (reutilización de instancia)

```javascript
if (graficoBars) {
    // Ya existe → solo actualizar datos (más eficiente)
    graficoBars.data.labels = labels;
    graficoBars.data.datasets[0].data = totales;
    graficoBars.update();
} else {
    // Primera vez → crear instancia
    graficoBars = new Chart(ctx, { ... });
}
```

---

## 9. Permisos de acceso (Sidebar)

En `config/template/mainSidebar.php`:

```php
// Tabla de permisos por rol:
'informes' => [2, 3, 4]   // IDs de rol que pueden ver el menú Informes
```

Los roles con ID 2, 3 o 4 ven el bloque "Informes" en la barra lateral.  
La función `puedeVerMenu($idRolUsuario, 'informes')` evalúa el acceso.

---

## 10. Añadir el filtro de mes: cambios realizados

Se han modificado **4 archivos** para implementar el filtro de mes:

### `view/Informe_ventas/index.php`
Añadido `<select id="filtroMes">` con las 12 opciones en la barra de filtros, entre el selector de año y los botones.

### `view/Informe_ventas/js/informeventas.js`
- Nueva función `mesActual()` que lee `#filtroMes`
- `recargarTodo()` pasa `mes` a todos los bloques excepto al gráfico
- `cargarKPIs`, `inicializarTablaClientes`, `inicializarTablaFamilia` actualizados para enviar `mes` en el POST
- `limpiarFiltros()` resetea también `#filtroMes`

### `controller/informeventas.php`
En los casos `kpis`, `top_clientes` y `por_familia` se lee `$mes = (int) $_POST['mes'] ?? 0` y se pasa al modelo.

### `models/InformeVentas.php`
`getKpisVentas`, `getTopClientes` y `getVentasPorFamilia` aceptan ahora el parámetro `$mes = 0` con filtrado dinámico. `getVentasPorMes` no cambia (el gráfico siempre muestra el año completo).

---

## ✅ Checklist de certificación (becario)

Abre la URL `http://192.168.31.19/MDR/view/Informe_ventas/index.php` y verifica cada punto:

### A — Carga inicial

- [ ] La página carga sin errores en consola del navegador (F12 → Console)
- [ ] El sidebar se muestra y responde a clics (colapsar/desplegar)
- [ ] El menú "Informes" aparece en el sidebar y está marcado como activo
- [ ] Los 4 KPI cards se rellenan con valores numéricos (no muestran "—" de forma permanente)
- [ ] El gráfico de barras mensuales muestra las 12 barras (o las columnas con datos)
- [ ] La tabla "Top 10 Clientes" carga filas con datos
- [ ] La tabla "Ventas por Familia" carga filas con datos
- [ ] El selector "Año" contiene los años disponibles en la base de datos

### B — Filtro por año

- [ ] Al cambiar el año y pulsar "Filtrar", todos los widgets se actualizan
- [ ] Al pulsar "Limpiar", el selector de año vuelve a "Todos" (valor 0) y los datos cambian
- [ ] Con año "Todos", los KPIs muestran el acumulado total histórico

### C — Filtro por mes (nuevo)

- [ ] El selector "Mes" aparece en la barra de filtros, entre "Año" y los botones
- [ ] Al seleccionar un mes y pulsar "Filtrar":
  - [ ] Los KPIs muestran valores solo de ese mes
  - [ ] La tabla Top Clientes muestra solo clientes con presupuestos en ese mes
  - [ ] La tabla Por Familia muestra solo ventas de ese mes
  - [ ] El gráfico **NO** cambia (sigue mostrando los 12 meses del año para contexto)
- [ ] Al pulsar "Limpiar", el selector de mes vuelve a "Todos" (valor 0)
- [ ] Con año + mes combinados, el filtrado funciona correctamente (intersección)

### D — Comportamiento del gráfico

- [ ] El gráfico tiene barras azules con bordes redondeados
- [ ] Al pasar el ratón sobre una barra el tooltip muestra el importe en formato "1.234,56 €"
- [ ] El eje Y muestra los valores con separador de miles en español
- [ ] La etiqueta "Año XXXX" / "Todos los años" se actualiza correctamente
- [ ] Al cambiar de año el gráfico se actualiza sin duplicar instancias (un solo `<canvas>`)

### E — Tablas DataTables

- [ ] Ambas tablas son responsivas en pantalla estrecha
- [ ] La columna "Total" en Top Clientes muestra formato "1.234,56 €"
- [ ] La columna "%" en Familia muestra porcentaje con un decimal
- [ ] Las tablas muestran el mensaje "Sin datos para el período seleccionado" cuando no hay resultados

### F — Seguridad básica

- [ ] Acceder a `controller/informeventas.php` directamente (sin `?op=`) devuelve `{"success":false,"message":"Operación no reconocida"}` — no muestra errores PHP
- [ ] Acceder a la vista sin sesión iniciada redirige al login (o bloquea el acceso)
- [ ] En DevTools → Network, las peticiones AJAX son `POST` (no `GET`) y devuelven `Content-Type: application/json`
- [ ] No hay valores SQL visibles en los mensajes de error mostrados al usuario

### G — Compatibilidad y rendimiento

- [ ] La página carga correctamente en Chrome y Firefox
- [ ] No hay errores de JavaScript en consola al filtrar varias veces seguidas
- [ ] El gráfico no acumula múltiples instancias de Chart.js al filtrar repetidamente
- [ ] Las tablas DataTables no se inicializan dos veces (no aparece el error "DataTables warning: table id=...")

---

## 11. Errores comunes y cómo diagnosticarlos

| Síntoma | Causa probable | Solución |
|---|---|---|
| Sidebar no responde | `mainJs.php` no se incluye o está antes de `Chart.js` | Verificar orden de `include` en el `</body>` |
| Gráfico no aparece | `Chart.js` cargado antes de que exista el `<canvas>` | El canvas debe estar en el DOM antes del `<script>` |
| KPIs muestran "—" siempre | Error AJAX — ver consola | Revisar si el controller devuelve JSON válido |
| DataTables: "Requested unknown parameter" | Nombre de columna en JS no coincide con clave JSON del controller | Alinear `data:` en columns[] con las claves del array PHP |
| Página en blanco | Error PHP → `session_start()` duplicado o require_once fallido | Activar `display_errors` en local para depurar |
| Vista no existe | La migración SQL no se ejecutó en la BD | Re-ejecutar `BD/migrations/20260317_01_create_vista_ventas_periodo.sql` |
