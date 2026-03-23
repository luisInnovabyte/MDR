# Patrón — Modal de Ayuda con icono «?»

> Documentación de referencia para replicar el componente de ayuda contextual
> que aparece junto al título de la vista, siguiendo el ejemplo del
> **Informe de Rotación de Inventario** (`view/Informe_rotacion/`).

---

## 1. Concepto general

Cada vista que lo requiera puede incluir un botón `?` junto al título de la
sección. Al pulsarlo se abre un **modal de ayuda contextual** que explica al
usuario qué es la pantalla, cómo funciona y qué significan sus datos.
El modal se implementa en un fichero PHP separado (p. ej. `ayuda<Vista>.php`)
que se incluye con `include_once` al final del `<body>`, antes del `</html>`.

---

## 2. Icono trigger («?»)

### Posición
Incrustado **dentro del `<h4>` del título**, a la derecha del texto y del icono
del módulo, usando `d-flex align-items-center gap-2` en el `<h4>`.

### Código exacto
```html
<h4 class="mb-0 d-flex align-items-center gap-2">
    <i class="fas fa-<icono-del-modulo> me-1 text-primary"></i><Título del módulo>
    <button type="button"
            class="btn btn-link p-0 ms-1"
            data-bs-toggle="modal"
            data-bs-target="#modalAyuda<NombreVista>"
            title="Ayuda sobre este informe">
        <i class="bi bi-question-circle text-primary" style="font-size: 1.3rem;"></i>
    </button>
</h4>
```

### Reglas
| Propiedad | Valor |
|---|---|
| Elemento | `<button>` — **no** `<a>` |
| Clases del botón | `btn btn-link p-0 ms-1` |
| Icono Bootstrap Icons | `bi bi-question-circle` (hueco, no relleno) |
| Color | `text-primary` |
| Tamaño | `style="font-size: 1.3rem;"` |
| `data-bs-target` | `#modalAyuda<NombreVista>` |
| `title` (tooltip nativo) | `"Ayuda sobre este informe"` o descripción análoga |

---

## 3. Estructura del modal

### Fichero
Crear `view/<Modulo>/ayuda<Vista>.php` (ej. `ayudaRotacion.php`).
Incluirlo en `index.php` **después de todos los scripts** de la vista:

```html
<!-- Scripts de la vista -->
<script src="js/<vista>.js"></script>

<!--  MODAL AYUDA  -->
<?php include_once('ayuda<Vista>.php') ?>
```

### Plantilla completa del modal

```html
<!-- Modal de Ayuda - <Título de la vista> -->
<div class="modal fade" id="modalAyuda<NombreVista>"
     tabindex="-1"
     aria-labelledby="modalAyuda<NombreVista>Label"
     aria-hidden="true">

    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">

            <!-- Encabezado -->
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title d-flex align-items-center"
                    id="modalAyuda<NombreVista>Label">
                    <i class="bi bi-question-circle-fill me-2 fs-4"></i>
                    Ayuda — <Título de la vista>
                </h5>
                <button type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal"
                        aria-label="Cerrar"></button>
            </div>

            <!-- Cuerpo -->
            <div class="modal-body">
                <!-- Secciones de contenido — ver §4 -->
            </div>

        </div>
    </div>
</div>
```

### Reglas del modal
| Propiedad | Valor |
|---|---|
| Tamaño | `modal-lg` |
| Scroll interno | `modal-dialog-scrollable` (obligatorio si el contenido es largo) |
| Header | `bg-primary text-white` |
| Icono del título | `bi bi-question-circle-fill me-2 fs-4` (relleno, blanco por herencia) |
| Botón cerrar | `btn-close btn-close-white` |
| IDs | Únicos por vista: `modalAyuda<NombreVista>` y `modalAyuda<NombreVista>Label` |

---

## 4. Secciones de contenido del modal body

Cada bloque de contenido sigue este patrón:

```html
<div class="help-section mb-4">
    <h6 class="text-primary d-flex align-items-center">
        <i class="bi bi-<icono> me-2"></i>
        <Título de la sección>
    </h6>
    <!-- contenido: <p>, tablas, alertas, acordeones… -->
</div>
```

### Secciones habituales (replicar las que apliquen)

| # | Sección | Icono BI | Contenido |
|---|---|---|---|
| 1 | ¿Qué es esta pantalla? | `bi-info-circle-fill` | `<p>` descriptivo + `alert alert-info` con el "para qué sirve" |
| 2 | Estados / Semáforo | `bi-traffic-light` | Tabla `table-sm table-bordered` con: estado · criterio · interpretación · acción |
| 3 | KPIs / indicadores | `bi-card-checklist` | `row g-3` con tarjetas `border rounded p-3 h-100` — una por KPI |
| 4 | Gráficos | `bi-bar-chart-line-fill` | Acordeón `accordion` — un `accordion-item` por gráfico, todos `collapsed` por defecto |
| 5 | Columnas de tabla | `bi-table` | Tabla `table-sm table-bordered table-hover thead-primary` — columna · descripción · cómo interpretarlo |
| 6 | Filtros | `bi-funnel-fill` | `row g-3` con tarjetas `border rounded p-3 h-100` — una por filtro |
| 7 | FAQ | `bi-chat-left-text-fill` | Acordeón `accordion` — un `accordion-item` por pregunta, todos `collapsed` |

### Alertas informativas dentro de secciones
```html
<!-- Informativa -->
<div class="alert alert-info py-2 mb-0">
    <small>
        <i class="bi bi-lightbulb me-1"></i>
        <strong>Para qué sirve:</strong> texto breve...
    </small>
</div>

<!-- Advertencia -->
<div class="alert alert-warning mt-2 py-2 mb-0">
    <small>
        <i class="bi bi-exclamation-triangle me-1"></i>
        <strong>Importante:</strong> texto breve...
    </small>
</div>
```

---

## 5. Cabecera de la vista (bloque completo de referencia)

El icono `?` vive dentro del bloque cabecera estándar de `br-section-wrapper`:

```html
<div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
    <div class="d-flex align-items-center gap-2">
        <div>
            <h4 class="mb-0 d-flex align-items-center gap-2">
                <i class="fas fa-<icono> me-1 text-primary"></i><Título>
                <button type="button"
                        class="btn btn-link p-0 ms-1"
                        data-bs-toggle="modal"
                        data-bs-target="#modalAyuda<NombreVista>"
                        title="Ayuda sobre este informe">
                    <i class="bi bi-question-circle text-primary" style="font-size: 1.3rem;"></i>
                </button>
            </h4>
            <p class="text-muted mb-0 small"><Subtítulo descriptivo></p>
        </div>
    </div>
    <!-- botones de acción opcionales: Actualizar, Exportar… -->
</div>
```

---

## 6. Dependencias

El componente requiere que la vista ya cargue:

| Librería | Cómo se carga |
|---|---|
| Bootstrap 5 (modal JS) | `mainJs.php` (ya incluido en todas las vistas) |
| Bootstrap Icons | `mainHead.php` (ya incluido); clase `bi bi-*` |
| Font Awesome | `mainHead.php` (ya incluido); clase `fas fa-*` |

No se necesita ningún JavaScript adicional: el modal funciona exclusivamente
con atributos `data-bs-toggle` / `data-bs-target` de Bootstrap 5.

---

## 7. Convención de nombres

| Elemento | Patrón | Ejemplo |
|---|---|---|
| Fichero PHP del modal | `ayuda<NombreVista>.php` | `ayudaRotacion.php` |
| ID del modal | `modalAyuda<NombreVista>` | `modalAyudaRotacion` |
| `aria-labelledby` | `modalAyuda<NombreVista>Label` | `modalAyudaRotacionLabel` |
| IDs internos del acordeón | `accordion<Seccion>` | `accordionGraficos`, `accordionFAQ` |
| IDs de paneles del acordeón | `collapse<Item>` / `faq<N>` | `collapseTop10`, `faq1` |

---

## 8. Vista de referencia

[view/Informe_rotacion/index.php](../../../view/Informe_rotacion/index.php) — icono trigger (línea ~75)  
[view/Informe_rotacion/ayudaRotacion.php](../../../view/Informe_rotacion/ayudaRotacion.php) — modal completo
