# Botones Flotantes de Navegación (Arriba/Abajo)

> Documentación para implementar botones flotantes de navegación en formularios largos  
> **Fecha de creación:** 19 de diciembre de 2025  
> **Autor:** Luis - Innovabyte  
> **Implementado en:** formularioCliente.php

---

## 📋 Descripción

Sistema de botones flotantes que permiten navegar rápidamente al inicio o final de formularios largos, mejorando la experiencia de usuario.

### ✨ Características

- **2 botones circulares flotantes** (50x50px)
- **Posición fija** en esquina inferior derecha
- **Aparecen automáticamente** después de 300px de scroll
- **Animación suave** (fadeIn/fadeOut)
- **Scroll animado** (800ms)
- **Diseño consistente** con el sistema (color primario)
- **Sombra** para destacar sobre el contenido

---

## 🎯 Implementación

### 1. HTML - Botones Flotantes

Añadir **justo antes del cierre de `</body>`**, después de los scripts del template:

```html
<!-- Botones flotantes para navegación -->
<!-- Botón para ir al inicio del formulario -->
<button id="scrollToTop" class="btn btn-primary" style="position: fixed; bottom: 140px; right: 30px; z-index: 1000; border-radius: 50%; width: 50px; height: 50px; display: none; box-shadow: 0 4px 8px rgba(0,0,0,0.3);" title="Ir al inicio del formulario">
    <i class="fas fa-arrow-up"></i>
</button>

<!-- Botón para ir al final del formulario -->
<button id="scrollToBottom" class="btn btn-primary" style="position: fixed; bottom: 80px; right: 30px; z-index: 1000; border-radius: 50%; width: 50px; height: 50px; display: none; box-shadow: 0 4px 8px rgba(0,0,0,0.3);" title="Ir al final del formulario">
    <i class="fas fa-arrow-down"></i>
</button>
```

### 2. JavaScript - Funcionalidad

Añadir **inmediatamente después de los botones HTML**:

```html
<!-- Script para botones flotantes de navegación -->
<script>
    $(document).ready(function() {
        // Mostrar/ocultar botones según scroll
        $(window).scroll(function() {
            if ($(this).scrollTop() > 300) {
                $('#scrollToTop').fadeIn();
                $('#scrollToBottom').fadeIn();
            } else {
                $('#scrollToTop').fadeOut();
                $('#scrollToBottom').fadeOut();
            }
        });

        // Hacer scroll al inicio del formulario
        $('#scrollToTop').click(function() {
            $('html, body').animate({
                scrollTop: 0
            }, 800);
            return false;
        });

        // Hacer scroll al final del formulario
        $('#scrollToBottom').click(function() {
            $('html, body').animate({
                scrollTop: $(document).height()
            }, 800);
            return false;
        });
    });
</script>
```

---

## 📐 Estructura de Posicionamiento

```
┌─────────────────────────────────┐
│                                 │
│         Contenido               │
│                                 │
│                          ┌────┐ │ ← 140px desde bottom
│                          │ ↑  │ │   (Botón arriba)
│                          └────┘ │
│                                 │
│                          ┌────┐ │ ← 80px desde bottom
│                          │ ↓  │ │   (Botón abajo)
│                          └────┘ │
└─────────────────────────────────┘
       30px desde right →
```

### Parámetros de posición:

| Elemento | Bottom | Right | Separación vertical |
|----------|--------|-------|---------------------|
| **scrollToTop** | 140px | 30px | 60px con scrollToBottom |
| **scrollToBottom** | 80px | 30px | - |

---

## 🎨 Estilos CSS Inline

```css
position: fixed;           /* Permanece fijo en pantalla */
bottom: 140px / 80px;      /* Altura desde el borde inferior */
right: 30px;               /* Distancia desde el borde derecho */
z-index: 1000;             /* Por encima de otros elementos */
border-radius: 50%;        /* Forma circular */
width: 50px;               /* Ancho del botón */
height: 50px;              /* Alto del botón */
display: none;             /* Oculto por defecto */
box-shadow: 0 4px 8px rgba(0,0,0,0.3); /* Sombra */
```

---

## ⚙️ Configuración

### Ajustar umbral de aparición

Modificar el valor `300` en la línea de scroll:

```javascript
if ($(this).scrollTop() > 300) {  // 300px = umbral
```

**Valores recomendados:**
- Formularios cortos: `200px`
- Formularios medianos: `300px` ✅ (por defecto)
- Formularios largos: `400px - 500px`

### Ajustar velocidad de animación

Modificar el valor `800` en las funciones de scroll:

```javascript
scrollTop: 0
}, 800);  // 800ms = velocidad
```

**Valores recomendados:**
- Rápido: `500ms`
- Normal: `800ms` ✅ (por defecto)
- Suave: `1200ms`

### Cambiar posición vertical

Modificar los valores de `bottom` en el HTML:

```html
<!-- Botón superior -->
bottom: 140px;  <!-- Ajustar aquí -->

<!-- Botón inferior -->
bottom: 80px;   <!-- Ajustar aquí -->
```

**Separación recomendada:** 60px entre botones

---

## 📦 Dependencias

- ✅ **jQuery 3.7.1+** (para animaciones y eventos)
- ✅ **Font Awesome 6.4.2+** (para iconos fa-arrow-up y fa-arrow-down)
- ✅ **Bootstrap 5** (para clase btn-primary)

---

## 🔧 Ejemplo de Implementación Completa

```html
<!DOCTYPE html>
<html lang="es">
<head>
    <!-- Resto del head -->
</head>
<body>
    
    <!-- Contenido del formulario -->
    <form>
        <!-- ... -->
    </form>
    
    <!-- Scripts del template -->
    <?php include_once('../../config/template/mainJs.php') ?>
    <script src="../../public/js/tooltip-colored.js"></script>
    <script src="../../public/js/popover-colored.js"></script>
    <script type="text/javascript" src="tuFormulario.js"></script>
    
    <!-- ============================================ -->
    <!-- AÑADIR AQUÍ: Botones flotantes + JavaScript -->
    <!-- ============================================ -->
    
    <!-- Botones flotantes para navegación -->
    <button id="scrollToTop" class="btn btn-primary" style="position: fixed; bottom: 140px; right: 30px; z-index: 1000; border-radius: 50%; width: 50px; height: 50px; display: none; box-shadow: 0 4px 8px rgba(0,0,0,0.3);" title="Ir al inicio del formulario">
        <i class="fas fa-arrow-up"></i>
    </button>
    
    <button id="scrollToBottom" class="btn btn-primary" style="position: fixed; bottom: 80px; right: 30px; z-index: 1000; border-radius: 50%; width: 50px; height: 50px; display: none; box-shadow: 0 4px 8px rgba(0,0,0,0.3);" title="Ir al final del formulario">
        <i class="fas fa-arrow-down"></i>
    </button>

    <!-- Script para botones flotantes de navegación -->
    <script>
        $(document).ready(function() {
            // Mostrar/ocultar botones según scroll
            $(window).scroll(function() {
                if ($(this).scrollTop() > 300) {
                    $('#scrollToTop').fadeIn();
                    $('#scrollToBottom').fadeIn();
                } else {
                    $('#scrollToTop').fadeOut();
                    $('#scrollToBottom').fadeOut();
                }
            });

            // Hacer scroll al inicio del formulario
            $('#scrollToTop').click(function() {
                $('html, body').animate({
                    scrollTop: 0
                }, 800);
                return false;
            });

            // Hacer scroll al final del formulario
            $('#scrollToBottom').click(function() {
                $('html, body').animate({
                    scrollTop: $(document).height()
                }, 800);
                return false;
            });
        });
    </script>

</body>
</html>
```

---

## ✅ Checklist de Implementación

Al replicar en otro formulario, verificar:

- [ ] jQuery está cargado antes del script
- [ ] Font Awesome está disponible (iconos fa-arrow-up y fa-arrow-down)
- [ ] Bootstrap CSS está cargado (clase btn-primary)
- [ ] Los IDs `scrollToTop` y `scrollToBottom` son únicos en la página
- [ ] Los botones están **después** de los scripts del template
- [ ] Los botones están **antes** del cierre `</body>`
- [ ] El z-index (1000) no interfiere con modales u otros elementos

---

## 🎨 Variantes de Color

Para cambiar el color de los botones según el contexto:

```html
<!-- Azul (por defecto) -->
<button class="btn btn-primary">

<!-- Verde (éxito) -->
<button class="btn btn-success">

<!-- Rojo (peligro) -->
<button class="btn btn-danger">

<!-- Naranja (advertencia) -->
<button class="btn btn-warning">

<!-- Gris (secundario) -->
<button class="btn btn-secondary">

<!-- Info (celeste) -->
<button class="btn btn-info">
```

---

## 🔍 Solución de Problemas

### Los botones no aparecen

1. **Verificar jQuery:** Asegurarse de que jQuery está cargado
2. **Verificar Font Awesome:** Los iconos deben estar disponibles
3. **Verificar scroll:** Hacer scroll >300px para que aparezcan
4. **Consola del navegador:** Buscar errores JavaScript

### Los botones interfieren con otros elementos

1. **Ajustar z-index:** Reducir a 900 o 800 si interfiere con modales
2. **Cambiar posición:** Ajustar valores de `bottom` o `right`

### El scroll no funciona suavemente

1. **Verificar jQuery:** Debe estar versión 1.7+
2. **Verificar animaciones:** No deben estar deshabilitadas en el navegador

---

## 📍 Archivos Implementados

- ✅ `w:\MDR\view\MntClientes\formularioCliente.php` - Primera implementación

---

## 📝 Notas Finales

- **Responsive:** Los botones se adaptan automáticamente a dispositivos móviles
- **Accesibilidad:** Los atributos `title` proporcionan información al hover
- **Performance:** Las animaciones son nativas de jQuery (optimizadas)
- **Compatibilidad:** Funciona en todos los navegadores modernos
- **Mantenimiento:** Código simple y fácil de mantener

---

**Última actualización:** 19 de diciembre de 2025  
**Versión:** 1.0  
**Proyecto:** MDR ERP Manager  
**Autor:** Luis - Innovabyte
