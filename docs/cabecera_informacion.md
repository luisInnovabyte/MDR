# Cabecera de Información con Gradiente

> Documentación para implementar tarjetas de información con gradiente de colores  
> **Fecha de creación:** 19 de diciembre de 2025  
> **Autor:** Luis - Innovabyte  
> **Implementado en:** MntElementos/index.php

---

## 📋 Descripción

Componente visual tipo "card" con gradiente de colores que muestra información destacada del contexto actual (artículo, cliente, empresa, etc.). Diseñado para proporcionar contexto visual inmediato al usuario sobre el registro con el que está trabajando.

### ✨ Características

- **Diseño moderno** con gradiente de colores personalizable
- **Responsive** adaptado a móviles y escritorio
- **Icono circular** con fondo translúcido
- **Badges** para datos adicionales
- **Botón de acción** opcional (volver, editar, etc.)
- **Sombra suave** para destacar del fondo

---

## 🎯 Estructura HTML Completa

### Plantilla Base

```html
<!-- Info del [entidad] -->
<div class="mt-2 mb-3" id="info-[entidad]">
    <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #COLOR1 0%, #COLOR2 100%);">
        <div class="card-body py-3 px-4">
            <div class="row align-items-center">
                <!-- Icono principal -->
                <div class="col-auto">
                    <div class="rounded-circle d-flex align-items-center justify-content-center" 
                         style="width: 60px; height: 60px; background-color: rgba(255,255,255,0.15);">
                        <i class="bi bi-[icono] text-white" style="font-size: 1.8rem;"></i>
                    </div>
                </div>
                
                <!-- Información principal -->
                <div class="col">
                    <div class="text-white-50 mb-1" style="font-size: 0.85rem; font-weight: 500;">
                        <i class="bi bi-info-circle me-1"></i>[Etiqueta descriptiva]
                    </div>
                    <h5 class="mb-2 fw-bold text-white" id="nombre-[entidad]">
                        [Nombre o título principal]
                    </h5>
                    <div class="d-flex align-items-center gap-3">
                        <span class="text-white-50" style="font-size: 0.9rem;">
                            <i class="bi bi-[icono1] me-1"></i>[Campo 1]:
                            <span id="[campo1]-[entidad]" class="badge bg-white text-dark ms-1 fw-semibold">--</span>
                        </span>
                        <span class="text-white-50" style="font-size: 0.9rem;">
                            <i class="bi bi-[icono2] me-1"></i>[Campo 2]:
                            <span id="[campo2]-[entidad]" class="badge bg-white text-dark ms-1 fw-semibold">--</span>
                        </span>
                    </div>
                </div>
                
                <!-- Botón de acción (opcional) -->
                <div class="col-auto d-none d-md-block">
                    <a href="[url-destino]" class="btn btn-light btn-sm">
                        <i class="bi bi-[icono-accion] me-1"></i>[Texto del botón]
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
```

---

## 🎨 Paleta de Colores Predefinidas

### 1. Azul (Artículos) - Original

```css
background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
```

**Uso recomendado:** Artículos, Productos, Catálogo

### 2. Verde (Clientes)

```css
background: linear-gradient(135deg, #134e5e 0%, #71b280 100%);
```

**Uso recomendado:** Clientes, Contactos, CRM

### 3. Naranja (Empresas)

```css
background: linear-gradient(135deg, #f12711 0%, #f5af19 100%);
```

**Uso recomendado:** Empresas, Organizaciones, Proveedores

### 4. Morado (Presupuestos)

```css
background: linear-gradient(135deg, #5f2c82 0%, #49a09d 100%);
```

**Uso recomendado:** Presupuestos, Facturas, Documentos

### 5. Turquesa (Elementos)

```css
background: linear-gradient(135deg, #2980b9 0%, #6dd5fa 100%);
```

**Uso recomendado:** Elementos, Componentes, Stock

### 6. Rojo (Alertas/Crítico)

```css
background: linear-gradient(135deg, #c31432 0%, #240b36 100%);
```

**Uso recomendado:** Alertas, Estados críticos, Advertencias

### 7. Verde Esmeralda (Éxito)

```css
background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
```

**Uso recomendado:** Estados completados, Confirmaciones

### 8. Índigo (Administración)

```css
background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
```

**Uso recomendado:** Configuración, Administración, Sistema

---

## 🔧 Componentes del Card

### 1. Contenedor Principal

```html
<div class="mt-2 mb-3" id="info-articulo">
```

- **mt-2:** Margen superior pequeño (8px)
- **mb-3:** Margen inferior medio (16px)
- **id:** Identificador único para manipulación JavaScript

### 2. Card con Gradiente

```html
<div class="card border-0 shadow-sm" style="background: linear-gradient(...);">
```

- **border-0:** Sin borde
- **shadow-sm:** Sombra suave
- **linear-gradient(135deg, ...):** Gradiente diagonal de 135°

### 3. Icono Circular

```html
<div class="rounded-circle d-flex align-items-center justify-content-center" 
     style="width: 60px; height: 60px; background-color: rgba(255,255,255,0.15);">
    <i class="bi bi-box-seam text-white" style="font-size: 1.8rem;"></i>
</div>
```

- **rounded-circle:** Forma circular perfecta
- **rgba(255,255,255,0.15):** Fondo blanco translúcido al 15%
- **60x60px:** Tamaño fijo del círculo
- **font-size: 1.8rem:** Tamaño del icono

### 4. Texto Principal

```html
<h5 class="mb-2 fw-bold text-white" id="nombre-articulo">
    Cargando...
</h5>
```

- **fw-bold:** Negrita
- **text-white:** Color blanco
- **mb-2:** Margen inferior pequeño

### 5. Badges de Información

```html
<span class="badge bg-white text-dark ms-1 fw-semibold">--</span>
```

- **bg-white:** Fondo blanco
- **text-dark:** Texto oscuro
- **fw-semibold:** Semi-negrita

---

## 📖 Ejemplos Completos

### Ejemplo 1: Info de Artículo (Azul)

```html
<!-- Info del artículo -->
<div class="mt-2 mb-3" id="info-articulo">
    <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);">
        <div class="card-body py-3 px-4">
            <div class="row align-items-center">
                <!-- Icono principal -->
                <div class="col-auto">
                    <div class="rounded-circle d-flex align-items-center justify-content-center" 
                         style="width: 60px; height: 60px; background-color: rgba(255,255,255,0.15);">
                        <i class="bi bi-box-seam text-white" style="font-size: 1.8rem;"></i>
                    </div>
                </div>
                
                <!-- Información del artículo -->
                <div class="col">
                    <div class="text-white-50 mb-1" style="font-size: 0.85rem; font-weight: 500;">
                        <i class="bi bi-info-circle me-1"></i>Artículo actual
                    </div>
                    <h5 class="mb-2 fw-bold text-white" id="nombre-articulo">
                        Cámara Sony A7 III
                    </h5>
                    <div class="d-flex align-items-center gap-3">
                        <span class="text-white-50" style="font-size: 0.9rem;">
                            <i class="bi bi-upc-scan me-1"></i>Código:
                            <span id="codigo-articulo" class="badge bg-white text-dark ms-1 fw-semibold">CAM-001</span>
                        </span>
                        <span class="text-white-50" style="font-size: 0.9rem;">
                            <i class="bi bi-hash me-1"></i>ID:
                            <span id="id-articulo" class="badge bg-white text-dark ms-1 fw-semibold">42</span>
                        </span>
                    </div>
                </div>
                
                <!-- Botón de acción -->
                <div class="col-auto d-none d-md-block">
                    <a href="../MntArticulos/index.php" class="btn btn-light btn-sm">
                        <i class="bi bi-arrow-left me-1"></i>Volver
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
```

### Ejemplo 2: Info de Cliente (Verde)

```html
<!-- Info del cliente -->
<div class="mt-2 mb-3" id="info-cliente">
    <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #134e5e 0%, #71b280 100%);">
        <div class="card-body py-3 px-4">
            <div class="row align-items-center">
                <!-- Icono principal -->
                <div class="col-auto">
                    <div class="rounded-circle d-flex align-items-center justify-content-center" 
                         style="width: 60px; height: 60px; background-color: rgba(255,255,255,0.15);">
                        <i class="bi bi-person-circle text-white" style="font-size: 1.8rem;"></i>
                    </div>
                </div>
                
                <!-- Información del cliente -->
                <div class="col">
                    <div class="text-white-50 mb-1" style="font-size: 0.85rem; font-weight: 500;">
                        <i class="bi bi-info-circle me-1"></i>Cliente actual
                    </div>
                    <h5 class="mb-2 fw-bold text-white" id="nombre-cliente">
                        Juan Pérez García
                    </h5>
                    <div class="d-flex align-items-center gap-3">
                        <span class="text-white-50" style="font-size: 0.9rem;">
                            <i class="bi bi-envelope me-1"></i>Email:
                            <span id="email-cliente" class="badge bg-white text-dark ms-1 fw-semibold">juan@ejemplo.com</span>
                        </span>
                        <span class="text-white-50" style="font-size: 0.9rem;">
                            <i class="bi bi-telephone me-1"></i>Teléfono:
                            <span id="telefono-cliente" class="badge bg-white text-dark ms-1 fw-semibold">666 123 456</span>
                        </span>
                    </div>
                </div>
                
                <!-- Botón de acción -->
                <div class="col-auto d-none d-md-block">
                    <a href="../MntClientes/index.php" class="btn btn-light btn-sm">
                        <i class="bi bi-arrow-left me-1"></i>Volver
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
```

### Ejemplo 3: Info de Empresa (Naranja)

```html
<!-- Info de la empresa -->
<div class="mt-2 mb-3" id="info-empresa">
    <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #f12711 0%, #f5af19 100%);">
        <div class="card-body py-3 px-4">
            <div class="row align-items-center">
                <!-- Icono principal -->
                <div class="col-auto">
                    <div class="rounded-circle d-flex align-items-center justify-content-center" 
                         style="width: 60px; height: 60px; background-color: rgba(255,255,255,0.15);">
                        <i class="bi bi-building text-white" style="font-size: 1.8rem;"></i>
                    </div>
                </div>
                
                <!-- Información de la empresa -->
                <div class="col">
                    <div class="text-white-50 mb-1" style="font-size: 0.85rem; font-weight: 500;">
                        <i class="bi bi-info-circle me-1"></i>Empresa actual
                    </div>
                    <h5 class="mb-2 fw-bold text-white" id="nombre-empresa">
                        MDR Audiovisuales S.L.
                    </h5>
                    <div class="d-flex align-items-center gap-3">
                        <span class="text-white-50" style="font-size: 0.9rem;">
                            <i class="bi bi-credit-card me-1"></i>CIF:
                            <span id="cif-empresa" class="badge bg-white text-dark ms-1 fw-semibold">B12345678</span>
                        </span>
                        <span class="text-white-50" style="font-size: 0.9rem;">
                            <i class="bi bi-hash me-1"></i>ID:
                            <span id="id-empresa" class="badge bg-white text-dark ms-1 fw-semibold">1</span>
                        </span>
                    </div>
                </div>
                
                <!-- Botón de acción -->
                <div class="col-auto d-none d-md-block">
                    <a href="../MntEmpresas/index.php" class="btn btn-light btn-sm">
                        <i class="bi bi-arrow-left me-1"></i>Volver
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
```

### Ejemplo 4: Info de Presupuesto (Morado)

```html
<!-- Info del presupuesto -->
<div class="mt-2 mb-3" id="info-presupuesto">
    <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #5f2c82 0%, #49a09d 100%);">
        <div class="card-body py-3 px-4">
            <div class="row align-items-center">
                <!-- Icono principal -->
                <div class="col-auto">
                    <div class="rounded-circle d-flex align-items-center justify-content-center" 
                         style="width: 60px; height: 60px; background-color: rgba(255,255,255,0.15);">
                        <i class="bi bi-file-earmark-text text-white" style="font-size: 1.8rem;"></i>
                    </div>
                </div>
                
                <!-- Información del presupuesto -->
                <div class="col">
                    <div class="text-white-50 mb-1" style="font-size: 0.85rem; font-weight: 500;">
                        <i class="bi bi-info-circle me-1"></i>Presupuesto actual
                    </div>
                    <h5 class="mb-2 fw-bold text-white" id="nombre-presupuesto">
                        Evento Corporativo 2025
                    </h5>
                    <div class="d-flex align-items-center gap-3">
                        <span class="text-white-50" style="font-size: 0.9rem;">
                            <i class="bi bi-calendar me-1"></i>Número:
                            <span id="numero-presupuesto" class="badge bg-white text-dark ms-1 fw-semibold">PPT-2025-001</span>
                        </span>
                        <span class="text-white-50" style="font-size: 0.9rem;">
                            <i class="bi bi-currency-euro me-1"></i>Total:
                            <span id="total-presupuesto" class="badge bg-white text-dark ms-1 fw-semibold">2.450,00 €</span>
                        </span>
                    </div>
                </div>
                
                <!-- Botón de acción -->
                <div class="col-auto d-none d-md-block">
                    <a href="../Presupuesto/index.php" class="btn btn-light btn-sm">
                        <i class="bi bi-arrow-left me-1"></i>Volver
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
```

---

## 🔍 Iconos Bootstrap Icons Recomendados

### Por Contexto

| Contexto | Icono | Clase |
|----------|-------|-------|
| **Artículos** | 📦 | `bi-box-seam` |
| **Clientes** | 👤 | `bi-person-circle` |
| **Empresas** | 🏢 | `bi-building` |
| **Presupuestos** | 📄 | `bi-file-earmark-text` |
| **Elementos** | 🔧 | `bi-gear-fill` |
| **Proveedores** | 🤝 | `bi-people-fill` |
| **Facturas** | 💶 | `bi-receipt` |
| **Documentos** | 📋 | `bi-file-earmark-pdf` |
| **Ubicaciones** | 📍 | `bi-geo-alt-fill` |
| **Contactos** | 📞 | `bi-telephone-fill` |

### Para Badges

| Dato | Icono | Clase |
|------|-------|-------|
| **Código** | 🏷️ | `bi-upc-scan` |
| **ID** | # | `bi-hash` |
| **Email** | ✉️ | `bi-envelope` |
| **Teléfono** | 📞 | `bi-telephone` |
| **CIF/NIF** | 💳 | `bi-credit-card` |
| **Fecha** | 📅 | `bi-calendar` |
| **Dinero** | 💶 | `bi-currency-euro` |
| **Usuario** | 👤 | `bi-person` |

---

## 💻 JavaScript para Cargar Datos

### Ejemplo de carga de datos dinámicos

```javascript
// Función para cargar info del artículo
function cargarInfoArticulo(id_articulo) {
    $.ajax({
        url: '../../controller/articulo.php?op=mostrar',
        type: 'POST',
        data: { id_articulo: id_articulo },
        dataType: 'json',
        success: function(data) {
            $('#nombre-articulo').text(data.nombre_articulo);
            $('#codigo-articulo').text(data.codigo_articulo);
            $('#id-articulo').text(data.id_articulo);
        },
        error: function() {
            $('#nombre-articulo').text('Error al cargar datos');
            $('#codigo-articulo').text('--');
            $('#id-articulo').text('--');
        }
    });
}

// Llamar al cargar la página
$(document).ready(function() {
    const urlParams = new URLSearchParams(window.location.search);
    const id_articulo = urlParams.get('id_articulo');
    
    if (id_articulo) {
        cargarInfoArticulo(id_articulo);
    }
});
```

---

## 📐 Responsive Design

### Comportamiento según pantalla

```html
<!-- Ocultar botón en móviles -->
<div class="col-auto d-none d-md-block">
    <a href="..." class="btn btn-light btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Volver
    </a>
</div>
```

- **d-none:** Oculto por defecto (móviles)
- **d-md-block:** Visible en tablets y escritorio (≥768px)

### Espaciado adaptativo

```html
<div class="d-flex align-items-center gap-3">
```

- **gap-3:** Espacio de 16px entre elementos
- **flex-wrap:** Automáticamente envuelve en móviles

---

## 🎯 Checklist de Implementación

Al implementar en una nueva pantalla:

- [ ] Cambiar el `id` del contenedor (ej: `info-cliente`, `info-empresa`)
- [ ] Personalizar el gradiente de colores según el contexto
- [ ] Cambiar el icono principal (Bootstrap Icons)
- [ ] Actualizar la etiqueta descriptiva
- [ ] Configurar los IDs de los campos dinámicos
- [ ] Cambiar los iconos de los badges
- [ ] Actualizar el texto y URL del botón de acción
- [ ] Implementar el JavaScript para cargar datos
- [ ] Verificar responsive en móviles

---

## 🔧 Personalización Avanzada

### Cambiar la dirección del gradiente

```css
/* Diagonal izquierda-derecha (por defecto) */
background: linear-gradient(135deg, #color1 0%, #color2 100%);

/* Horizontal */
background: linear-gradient(90deg, #color1 0%, #color2 100%);

/* Vertical */
background: linear-gradient(180deg, #color1 0%, #color2 100%);

/* Radial desde el centro */
background: radial-gradient(circle, #color1 0%, #color2 100%);
```

### Ajustar opacidad del fondo del icono

```css
/* Más transparente */
background-color: rgba(255,255,255,0.10);

/* Por defecto */
background-color: rgba(255,255,255,0.15);

/* Más sólido */
background-color: rgba(255,255,255,0.25);
```

### Cambiar el tamaño del icono circular

```html
<!-- Pequeño (50x50) -->
<div class="rounded-circle ..." style="width: 50px; height: 50px; ...">
    <i class="bi bi-... text-white" style="font-size: 1.5rem;"></i>
</div>

<!-- Mediano (60x60) - Por defecto -->
<div class="rounded-circle ..." style="width: 60px; height: 60px; ...">
    <i class="bi bi-... text-white" style="font-size: 1.8rem;"></i>
</div>

<!-- Grande (70x70) -->
<div class="rounded-circle ..." style="width: 70px; height: 70px; ...">
    <i class="bi bi-... text-white" style="font-size: 2.2rem;"></i>
</div>
```

---

## 🌈 Generador de Gradientes

Herramientas online recomendadas:

1. **CSS Gradient**: https://cssgradient.io/
2. **UI Gradients**: https://uigradients.com/
3. **Gradient Hunt**: https://gradienthunt.com/

---

## 📍 Archivos Implementados

- ✅ `w:\MDR\view\MntElementos\index.php` - Implementación original

---

## 📝 Notas Finales

- **Bootstrap 5:** Requiere Bootstrap 5 y Bootstrap Icons
- **Responsive:** Totalmente adaptado a móviles
- **Accesibilidad:** Usa etiquetas semánticas y contraste adecuado
- **Performance:** No impacta en el rendimiento de la página
- **Mantenimiento:** Fácil de actualizar colores y contenido

---

## 🎨 Paleta Completa de Gradientes

```css
/* AZULES */
#1e3c72 → #2a5298  /* Azul océano */
#2980b9 → #6dd5fa  /* Azul cielo */
#0f2027 → #2c5364  /* Azul oscuro */

/* VERDES */
#134e5e → #71b280  /* Verde bosque */
#11998e → #38ef7d  /* Verde esmeralda */
#56ab2f → #a8e063  /* Verde lima */

/* NARANJAS/ROJOS */
#f12711 → #f5af19  /* Naranja fuego */
#c31432 → #240b36  /* Rojo oscuro */
#eb3349 → #f45c43  /* Coral */

/* MORADOS */
#5f2c82 → #49a09d  /* Morado turquesa */
#667eea → #764ba2  /* Índigo */
#da22ff → #9733ee  /* Morado neón */

/* ROSAS */
#ff6e7f → #bfe9ff  /* Rosa pastel */
#ee0979 → #ff6a00  /* Rosa intenso */

/* GRISES/NEUTROS */
#757f9a → #d7dde8  /* Gris suave */
#2c3e50 → #bdc3c7  /* Gris azulado */
```

---

**Última actualización:** 19 de diciembre de 2025  
**Versión:** 1.0  
**Proyecto:** MDR ERP Manager  
**Autor:** Luis - Innovabyte
