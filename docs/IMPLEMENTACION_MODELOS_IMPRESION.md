# Implementación de Modelos de Impresión de Presupuestos

**Fecha**: 2026-02-10  
**Objetivo**: Permitir que cada empresa pueda usar un modelo de impresión personalizado para sus presupuestos

---

## ✅ FASE 1: Estructura de Archivos - COMPLETADO

### Archivos Creados:

1. **controller/impresionpresupuesto_m1_es.php** (Modelo 1 - Español)
   - Copia del controller original
   - Logging actualizado con prefijo `[MODELO 1]`
   - Diseño: Grid estándar con información compacta

2. **controller/impresionpresupuesto_m2_es.php** (Modelo 2 - Español)
   - Copia del controller original
   - Logging actualizado con prefijo `[MODELO 2]`
   - Diseño: Actualmente idéntico a M1 (pendiente de personalización)

3. **BD/migrations/alter_empresa_modelo_impresion.sql**
   - ALTER TABLE para agregar campo `modelo_impresion_empresa VARCHAR(50)`
   - Valor por defecto: `'impresionpresupuesto_m1_es.php'`
   - Índice en el campo para mejorar performance
   - Script de UPDATE para registros existentes

---

## ✅ FASE 2: Lógica de Backend - COMPLETADO

### Cambios en Modelos:

**models/ImpresionPresupuesto.php**:
- ✅ Añadido método `get_modelo_impresion($id_empresa)`
  - Retorna el nombre del controller configurado para la empresa
  - Valor por defecto: `'impresionpresupuesto_m1_es.php'`
  - Manejo robusto de errores con fallback automático
  - Logging completo de todas las operaciones

### Cambios en Controllers:

**controller/presupuesto.php**:
- ✅ Añadido case `"obtener_modelo_impresion"`
  - Endpoint POST: `presupuesto.php?op=obtener_modelo_impresion`
  - Parámetro: `id_empresa` (POST)
  - Respuesta JSON:
    ```json
    {
        "success": true,
        "modelo": "impresionpresupuesto_m1_es.php"
    }
    ```

---

## ⚠️ FASE 3: Migración de Base de Datos - PENDIENTE

### Ejecutar SQL:

```bash
mysql -u administrator -p toldos_db < BD/migrations/alter_empresa_modelo_impresion.sql
```

O ejecutar manualmente en cliente MySQL:

```sql
USE toldos_db;

-- Añadir campo modelo_impresion_empresa
ALTER TABLE empresa 
ADD COLUMN modelo_impresion_empresa VARCHAR(50) 
DEFAULT 'impresionpresupuesto_m1_es.php'
COMMENT 'Nombre del archivo controller usado para imprimir presupuestos';

-- Crear índice
CREATE INDEX idx_modelo_impresion ON empresa(modelo_impresion_empresa);

-- Actualizar registros existentes
UPDATE empresa 
SET modelo_impresion_empresa = 'impresionpresupuesto_m1_es.php' 
WHERE modelo_impresion_empresa IS NULL;

-- Verificar
SELECT id_empresa, nombre_empresa, modelo_impresion_empresa 
FROM empresa 
WHERE activo_empresa = 1;
```

---

## ⚠️ FASE 4: Frontend JavaScript - PENDIENTE

### Modificaciones Necesarias en `view/Presupuesto/mntpresupuesto.js`:

#### Ubicación de Cambios:
Función `function mostrarModalImpresion(id_presupuesto, row)` - Línea aproximada 560

#### Cambio 1: Guardar id_empresa al abrir modal

**BUSCAR:**
```javascript
function mostrarModalImpresion(id_presupuesto, row) {
    // Guardar el ID en el modal para usarlo después
    $('#modalImpresionPresupuesto').data('id_presupuesto', id_presupuesto);
```

**REEMPLAZAR CON:**
```javascript
function mostrarModalImpresion(id_presupuesto, row) {
    // Guardar el ID del presupuesto y empresa en el modal para usarlo después
    $('#modalImpresionPresupuesto').data('id_presupuesto', id_presupuesto);
    $('#modalImpresionPresupuesto').data('id_empresa', row.id_empresa);
```

#### Cambio 2: Actualizar función de impresión

**BUSCAR (línea ~598):**
```javascript
$('#btnImprimirPresupuesto').on('click', function() {
    var id_presupuesto = $('#modalImpresionPresupuesto').data('id_presupuesto');
    var tipo = $('input[name="tipo_presupuesto"]:checked').val();
    var idioma = $('input[name="idioma"]:checked').val();
```

**REEMPLAZAR CON:**
```javascript
$('#btnImprimirPresupuesto').on('click', function() {
    var id_presupuesto = $('#modalImpresionPresupuesto').data('id_presupuesto');
    var id_empresa = $('#modalImpresionPresupuesto').data('id_empresa');
    var tipo = $('input[name="tipo_presupuesto"]:checked').val();
    var idioma = $('input[name="idioma"]:checked').val();
```

#### Cambio 3: Cambiar POST directo por AJAX

**BUSCAR (línea ~632):**
```javascript
// Crear formulario temporal para enviar por POST y abrir en nueva ventana
var form = $('<form>', {
    'method': 'POST',
    'action': '../../controller/impresionpresupuesto.php?op=' + operacion,
    'target': '_blank'
});

// Añadir campo oculto con el ID del presupuesto
form.append($('<input>', {
    'type': 'hidden',
    'name': 'id_presupuesto',
    'value': id_presupuesto
}));

// Añadir el formulario al body, enviarlo y eliminarlo
$('body').append(form);
form.submit();
form.remove();

// Cerrar el modal
$('#modalImpresionPresupuesto').modal('hide');

// Notificar al usuario
Swal.fire({
    icon: 'success',
    title: 'Generando impresión',
    text: 'Se abrirá el presupuesto en una nueva ventana',
    timer: 2000,
    showConfirmButton: false
});
```

**REEMPLAZAR CON:**
```javascript
// Primero obtener el modelo de impresión configurado para la empresa
$.ajax({
    url: '../../controller/presupuesto.php?op=obtener_modelo_impresion',
    type: 'POST',
    data: {
        id_empresa: id_empresa
    },
    dataType: 'json',
    success: function(response) {
        if (response.success) {
            var modeloController = response.modelo;
            console.log('Usando modelo de impresión:', modeloController);
            
            // Crear formulario temporal para enviar por POST y abrir en nueva ventana
            var form = $('<form>', {
                'method': 'POST',
                'action': '../../controller/' + modeloController + '?op=' + operacion,
                'target': '_blank'
            });
            
            // Añadir campo oculto con el ID del presupuesto
            form.append($('<input>', {
                'type': 'hidden',
                'name': 'id_presupuesto',
                'value': id_presupuesto
            }));
            
            // Añadir el formulario al body, enviarlo y eliminarlo
            $('body').append(form);
            form.submit();
            form.remove();
            
            // Cerrar el modal
            $('#modalImpresionPresupuesto').modal('hide');
            
            // Notificar al usuario
            Swal.fire({
                icon: 'success',
                title: 'Generando impresión',
                text: 'Se abrirá el presupuesto en una nueva ventana (Modelo: ' + modeloController + ')',
                timer: 2000,
                showConfirmButton: false
            });
        } else {
            Swal.fire('Error', response.message || 'No se pudo obtener el modelo de impresión', 'error');
        }
    },
    error: function() {
        Swal.fire('Error', 'Error de comunicación al obtener el modelo de impresión', 'error');
    }
});
```

---

## 📋 FASE 5: Pruebas y Validación - PENDIENTE

### Test Plan:

1. **Ejecutar Migración SQL**
   ```bash
   mysql -u administrator -p toldos_db < BD/migrations/alter_empresa_modelo_impresion.sql
   ```

2. **Verificar Campo en BD**
   ```sql
   DESCRIBE empresa;
   SELECT id_empresa, nombre_empresa, modelo_impresion_empresa FROM empresa;
   ```

3. **Aplicar Cambios JavaScript**
   - Editar `view/Presupuesto/mntpresupuesto.js` con los 3 cambios documentados
   - Verificar sintaxis (no errores de consola)

4. **Prueba Funcional**
   - Abrir un presupuesto
   - Click en botón "Imprimir"
   - Verificar en console.log: `Usando modelo de impresión: impresionpresupuesto_m1_es.php`
   - Verificar que se abre la impresión correctamente

5. **Prueba de Cambio de Modelo**
   ```sql
   -- Cambiar empresa a modelo 2
   UPDATE empresa 
   SET modelo_impresion_empresa = 'impresionpresupuesto_m2_es.php' 
   WHERE id_empresa = 1;
   
   -- Imprimir presupuesto de esa empresa
   -- Verificar en logs que dice [MODELO 2]
   ```

6. **Revisar Logs**
   ```bash
   # Linux/Mac
   tail -f public/logs/$(date +%Y-%m-%d).json
   
   # Windows PowerShell
   Get-Content "public\logs\$(Get-Date -Format 'yyyy-MM-dd').json" -Tail 20
   ```

---

## 📊 ESTADO ACTUAL DEL PROYECTO

### Completado ✅:
- [x] Crear archivos de controllers (m1_es, m2_es)
- [x] Actualizar logging en ambos modelos
- [x] Crear script SQL de migración
- [x] Añadir método `get_modelo_impresion()` en modelo
- [x] Añadir case `obtener_modelo_impresion` en controller

### En Progreso ⚠️:
- [ ] Ejecutar migración SQL en base de datos
- [ ] Aplicar cambios en JavaScript (3 modificaciones)

### Pendiente ❌:
- [ ] Pruebas funcionales completas
- [ ] Customizar diseño de Modelo 2 (actualmente idéntico a M1)
- [ ] Crear versiones en inglés (_m1_en.php, _m2_en.php)
- [ ] Documentar diferencias visuales entre M1 y M2

---

## 🎯 PRÓXIMOS PASOS

### Inmediato (Fase 3 y 4):
1. Ejecutar `BD/migrations/alter_empresa_modelo_impresion.sql` en la base de datos
2. Aplicar las 3 modificaciones JavaScript documentadas arriba
3. Hacer commit de cambios: `git commit -m "feat: sistema multi-modelo para impresión de presupuestos"`

### Corto Plazo (Fase 5):
4. Realizar pruebas funcionales
5. Revisar logs para confirmar que cada modelo se identifica correctamente
6. Probar cambio manual de modelo en BD y verificar que se usa el correcto

### Medio Plazo:
7. Personalizar diseño de Modelo 2 (colores, layout, formato diferente)
8. Crear versiones en inglés (copiar y traducir labels)
9. Documentar con screenshots las diferencias entre modelos

---

## 🔧 COMANDOS ÚTILES

### Git:
```bash
# Ver cambios
git status

# Añadir archivos
git add controller/impresionpresupuesto_m1_es.php
git add controller/impresionpresupuesto_m2_es.php
git add models/ImpresionPresupuesto.php
git add controller/presupuesto.php
git add BD/migrations/alter_empresa_modelo_impresion.sql
git add view/Presupuesto/mntpresupuesto.js

# Commit
git commit -m "feat: sistema multi-modelo para impresión de presupuestos"

# Push
git push origin impr_presupuesto
```

### MySQL:
```bash
# Ejecutar migración
mysql -u administrator -p toldos_db < BD/migrations/alter_empresa_modelo_impresion.sql

# Verificar
mysql -u administrator -p toldos_db -e "DESCRIBE empresa"

# Ver modelos configurados
mysql -u administrator -p toldos_db -e "SELECT id_empresa, nombre_empresa, modelo_impresion_empresa FROM empresa"
```

### Logs:
```powershell
# Ver últimas 20 líneas del log de hoy
Get-Content "w:\MDR\public\logs\$(Get-Date -Format 'yyyy-MM-dd').json" -Tail 20 | ConvertFrom-Json | Format-Table -AutoSize
```

---

## 📝 NOTAS TÉCNICAS

### Arquitectura:
- **Modelo**: 2 archivos PHP independientes (m1_es, m2_es)
- **Selección**: Campo en BD `empresa.modelo_impresion_empresa`
- **Fallback**: Si no configurado o error → `impresionpresupuesto_m1_es.php`
- **Logging**: Cada modelo se identifica con prefijo en logs

### Ventajas del Diseño:
- ✅ Sin plantillas complejas - código directo
- ✅ Fácil de mantener - archivos independientes
- ✅ Flexible - cada modelo totalmente personalizable
- ✅ Escalable - fácil añadir más modelos (m3, m4...)
- ✅ Trazabilidad - logs identifican qué modelo se usó

### Consideraciones:
- ⚠️ Duplicación de código entre modelos (trade-off por simplicidad)
- ⚠️ Cambios globales requieren actualizar ambos archivos
- ⚠️ Verificar nombres de archivo sin typos en BD

---

**Última actualización**: 2026-02-10  
**Autor**: Luis - Innovabyte  
**Branch**: impr_presupuesto
