# Implementación: Botón de Líneas de Presupuesto con Versiones

**Fecha:** 20 de enero de 2026  
**Objetivo:** Añadir botón en DataTable de presupuestos que redirija a las líneas de la versión ACTUAL del presupuesto

---

## 📋 RESUMEN DE CAMBIOS

Se ha implementado un sistema completo para gestionar el acceso a las líneas de presupuesto basándose en el sistema de versiones existente.

---

## 🗄️ 1. CAMBIOS EN BASE DE DATOS

### Archivo Modificado
- `w:\MDR\BD\presupuesto\vista_presupuesto`

### Cambios Realizados

#### A. Campo añadido en SELECT principal
```sql
p.version_actual_presupuesto,  -- ← VERSIÓN ACTUAL DEL PRESUPUESTO
```

#### B. Nuevos campos de versión calculados
```sql
-- DATOS DE LA VERSIÓN ACTUAL DEL PRESUPUESTO
pv.id_version_presupuesto AS id_version_actual,
pv.numero_version_presupuesto AS numero_version_actual,
pv.estado_version_presupuesto AS estado_version_actual,
pv.fecha_creacion_version AS fecha_creacion_version_actual,
```

#### C. Nuevo JOIN con presupuesto_version
```sql
LEFT JOIN presupuesto_version pv
    ON p.id_presupuesto = pv.id_presupuesto
    AND pv.numero_version_presupuesto = p.version_actual_presupuesto;
```

### 🔧 SCRIPT DE ACTUALIZACIÓN

**Ubicación:** `w:\MDR\BD\presupuesto\actualizar_vista_version_actual.sql`

**Ejecutar en phpMyAdmin o cliente MySQL:**

```bash
# Conectar a la base de datos
mysql -h 217.154.117.83 -P 3308 -u administrator -p toldos_db

# Ejecutar el script
source w:/MDR/BD/presupuesto/actualizar_vista_version_actual.sql
```

O desde phpMyAdmin:
1. Seleccionar base de datos `toldos_db`
2. Ir a pestaña SQL
3. Copiar contenido de `actualizar_vista_version_actual.sql`
4. Ejecutar

---

## 💻 2. CAMBIOS EN BACKEND (Controller)

### Archivo Modificado
- `w:\MDR\controller\presupuesto.php`

### Cambios en case "listar"

#### Campo añadido en array de respuesta
```php
"version_actual_presupuesto" => $row["version_actual_presupuesto"] ?? 1,
```

#### Nuevos campos de versión al final del array
```php
// Datos de la versión actual
"id_version_actual" => $row["id_version_actual"] ?? null,
"numero_version_actual" => $row["numero_version_actual"] ?? null,
"estado_version_actual" => $row["estado_version_actual"] ?? null,
"fecha_creacion_version_actual" => $row["fecha_creacion_version_actual"] ?? null
```

---

## 🎨 3. CAMBIOS EN FRONTEND (JavaScript)

### Archivo Modificado
- `w:\MDR\view\Presupuesto\mntpresupuesto.js`

### A. Definición del Botón en DataTable (Línea ~218-228)

```javascript
// Columna 15: BOTON PARA GESTIONAR LÍNEAS DEL PRESUPUESTO
{
    targets: "lineas:name", 
    width: '5%', 
    searchable: false, 
    orderable: false, 
    class: "text-center",
    render: function (data, type, row) {
        // Verificar si existe versión actual
        if (!row.id_version_actual) {
            return `<button type="button" class="btn btn-secondary btn-sm" disabled 
                     title="Sin versión actual"> 
                     <i class="fas fa-list"></i>
                   </button>`;
        }
        return `<button type="button" class="btn btn-info btn-sm gestionarLineas" 
                 data-toggle="tooltip-primary" 
                 data-placement="top" 
                 title="Ver líneas de presupuesto (versión actual)"  
                 data-id_version_presupuesto="${row.id_version_actual}"
                 data-numero_version="${row.numero_version_actual || 1}"> 
                 <i class="fas fa-list"></i>
               </button>`;
    }
}
```

### B. Manejador del Evento Click (Línea ~542-552)

```javascript
// Gestionar líneas del presupuesto (versión actual)
$(document).on('click', '.gestionarLineas', function () {
    var id_version_presupuesto = $(this).data('id_version_presupuesto');
    var numero_version = $(this).data('numero_version') || 1;
    
    console.log('Redirigiendo a líneas de presupuesto:', {
        id_version: id_version_presupuesto,
        numero_version: numero_version
    });
    
    window.location.href = '../lineasPresupuesto/index.php?id_version_presupuesto=' + id_version_presupuesto;
});
```

---

## ✅ 4. CARACTERÍSTICAS IMPLEMENTADAS

### 🎯 Botón "Ver Líneas de Presupuesto"

| Característica | Detalle |
|---------------|---------|
| **Estilo** | `btn-info btn-sm` (azul claro, pequeño) |
| **Icono** | `fas fa-list` (Font Awesome) |
| **Tooltip** | "Ver líneas de presupuesto (versión actual)" |
| **Estado deshabilitado** | Si no existe versión actual, botón gris y deshabilitado |
| **Parámetro GET** | `id_version_presupuesto` (ID de la versión actual) |
| **URL destino** | `../lineasPresupuesto/index.php?id_version_presupuesto={id}` |

### 🔐 Validaciones

1. **Verificación de versión actual**: Si no existe `id_version_actual`, el botón se muestra deshabilitado
2. **Data attributes**: Se pasa tanto el ID de versión como el número de versión para referencia
3. **Console log**: Se registra en consola la información de redirección para debugging

---

## 🧪 5. PRUEBAS RECOMENDADAS

### Verificar Vista SQL
```sql
-- Comprobar que los campos de versión existen
SELECT 
    id_presupuesto,
    numero_presupuesto,
    version_actual_presupuesto,
    id_version_actual,
    numero_version_actual,
    estado_version_actual
FROM vista_presupuesto_completa
LIMIT 5;
```

### Verificar Response del Controller
1. Abrir DevTools (F12) → Network
2. Cargar página de presupuestos
3. Buscar petición a `presupuesto.php?op=listar`
4. Verificar que incluye:
   - `version_actual_presupuesto`
   - `id_version_actual`
   - `numero_version_actual`
   - `estado_version_actual`

### Verificar Funcionamiento del Botón
1. Cargar página de presupuestos
2. Verificar que aparece botón azul con icono de lista
3. Hover sobre botón → Debe mostrar tooltip
4. Clic en botón → Debe redirigir a:
   ```
   /view/lineasPresupuesto/index.php?id_version_presupuesto={id}
   ```
5. Verificar console.log para confirmar datos correctos

---

## 📝 6. NOTAS IMPORTANTES

### ⚠️ Prerequisitos
- La tabla `presupuesto` debe tener el campo `version_actual_presupuesto`
- Debe existir la tabla `presupuesto_version`
- Cada presupuesto debe tener al menos una versión creada
- El directorio `w:\MDR\view\lineasPresupuesto\` debe existir con `index.php`

### 🔄 Orden de Implementación
1. ✅ **PRIMERO**: Ejecutar script SQL para actualizar la vista
2. ✅ **SEGUNDO**: Los cambios en PHP/JS ya están aplicados en el código
3. ✅ **TERCERO**: Reiniciar/refrescar la aplicación web
4. ✅ **CUARTO**: Realizar pruebas

### 🐛 Troubleshooting

#### Botón aparece deshabilitado
- Verificar que existen registros en `presupuesto_version`
- Comprobar que `version_actual_presupuesto` tiene valor válido
- Revisar que el JOIN con `presupuesto_version` encuentra coincidencias

#### Error al hacer clic
- Verificar que existe el directorio `/view/lineasPresupuesto/`
- Comprobar que `index.php` existe en ese directorio
- Revisar console de navegador para ver URL generada

#### Datos no se cargan
- Verificar que la vista SQL se actualizó correctamente
- Comprobar response en Network tab de DevTools
- Revisar logs de PHP en `public/logs/`

---

## 📊 7. FLUJO DE DATOS

```
┌─────────────────────────────────────────────────────────────────┐
│                     TABLA: presupuesto                          │
│  - id_presupuesto                                               │
│  - version_actual_presupuesto (INT: 1, 2, 3...)               │
└────────────────┬────────────────────────────────────────────────┘
                 │
                 │ JOIN ON:
                 │ pv.id_presupuesto = p.id_presupuesto
                 │ AND pv.numero_version = p.version_actual
                 │
                 ▼
┌─────────────────────────────────────────────────────────────────┐
│              TABLA: presupuesto_version                         │
│  - id_version_presupuesto (PK)                                 │
│  - id_presupuesto (FK)                                         │
│  - numero_version_presupuesto (1, 2, 3...)                    │
│  - estado_version_presupuesto                                  │
└────────────────┬────────────────────────────────────────────────┘
                 │
                 ▼
┌─────────────────────────────────────────────────────────────────┐
│            VISTA: vista_presupuesto_completa                    │
│  Devuelve:                                                      │
│  - id_version_actual (= id_version_presupuesto)                │
│  - numero_version_actual                                        │
│  - estado_version_actual                                        │
└────────────────┬────────────────────────────────────────────────┘
                 │
                 ▼
┌─────────────────────────────────────────────────────────────────┐
│         CONTROLLER: presupuesto.php?op=listar                   │
│  Formatea y envía JSON al frontend                             │
└────────────────┬────────────────────────────────────────────────┘
                 │
                 ▼
┌─────────────────────────────────────────────────────────────────┐
│            FRONTEND: mntpresupuesto.js                          │
│  - DataTable recibe datos                                       │
│  - Renderiza botón con id_version_actual                       │
│  - Click → Redirige con id_version_presupuesto                 │
└────────────────┬────────────────────────────────────────────────┘
                 │
                 ▼
┌─────────────────────────────────────────────────────────────────┐
│   DESTINO: lineasPresupuesto/index.php                         │
│   Parámetro: ?id_version_presupuesto={id}                     │
└─────────────────────────────────────────────────────────────────┘
```

---

## 🎯 8. RESULTADO FINAL

### Antes
- Botón redirigía con `id_presupuesto`
- No consideraba sistema de versiones
- Ruta: `Presupuesto_pies/index.php`

### Después
- ✅ Botón redirige con `id_version_presupuesto` de versión ACTUAL
- ✅ Integrado con sistema de versiones
- ✅ Validación si no existe versión (botón deshabilitado)
- ✅ Tooltip descriptivo
- ✅ Data attributes adicionales para debugging
- ✅ Ruta correcta: `lineasPresupuesto/index.php`
- ✅ Estilo Bootstrap 5 consistente

---

## 📞 SOPORTE

Si hay problemas:
1. Revisar console del navegador (F12)
2. Revisar logs en `public/logs/`
3. Verificar que la vista SQL se actualizó
4. Comprobar respuesta JSON del controller

---

**Implementado por:** GitHub Copilot  
**Revisado:** Pendiente  
**Estado:** Listo para pruebas
