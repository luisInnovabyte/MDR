# ⚠️ ACCIÓN REQUERIDA: Migración de Base de Datos

## 🎯 Punto 14 - Firma Digital de Empleado

### Paso 1: Ejecutar Migración SQL (OBLIGATORIO)

Antes de usar la nueva funcionalidad de firma digital, debe ejecutar este script SQL:

```bash
# Opción 1: Desde línea de comandos MySQL
mysql -u administrator -p -h 217.154.117.83 -P 3308 toldos_db < BD/migrations/20250120_add_firma_comercial.sql

# Opción 2: Copiar y pegar en phpMyAdmin o similar
```

**Contenido del script:**
```sql
USE toldos_db;

ALTER TABLE comerciales 
ADD COLUMN firma_comercial TEXT 
COMMENT 'Firma digital del comercial en formato base64 PNG';
```

### Paso 2: Verificar Instalación

Ejecute esta consulta para confirmar que el campo se agregó:

```sql
SHOW COLUMNS FROM comerciales LIKE 'firma_comercial';
```

**Resultado esperado:**
```
+------------------+------+------+-----+---------+-------+
| Field            | Type | Null | Key | Default | Extra |
+------------------+------+------+-----+---------+-------+
| firma_comercial  | text | YES  |     | NULL    |       |
+------------------+------+------+-----+---------+-------+
```

### Paso 3: Probar Funcionalidad

1. Iniciar sesión con usuario que tenga perfil de comercial
2. Ir a: `view/Home/perfil.php`
3. Dibujar firma en el canvas
4. Click en "Guardar Firma"
5. Generar un presupuesto PDF
6. Verificar que la firma aparece en la sección "DEPARTAMENTO COMERCIAL"

---

## 📋 Archivos Modificados/Creados

### Base de Datos
- ✅ `BD/migrations/20250120_add_firma_comercial.sql` - Script de migración

### Backend (PHP)
- ✅ `models/Comerciales.php` - Agregados 3 métodos para manejo de firma
- ✅ `controller/ajax_guardar_firma.php` - Endpoint para guardar firma
- ✅ `controller/ajax_obtener_firma.php` - Endpoint para obtener firma
- ✅ `controller/impresionpresupuesto_m2_pdf_es.php` - Renderizado de firma en PDF

### Frontend
- ✅ `view/Home/perfil.php` - Canvas de firma con Signature Pad
- ✅ `view/Home/perfil.js` - Lógica de captura y guardado

### Documentación
- ✅ `docs/COMPLETADO_punto14_firma_empleado.md` - Documentación completa

---

## 🔍 Casos de Uso

### ✅ Usuario ES Comercial (tiene id_usuario en tabla comerciales)
- Verá sección de firma en su perfil
- Podrá dibujar y guardar su firma
- Su firma aparecerá en PDFs que genere

### ❌ Usuario NO ES Comercial
- No verá sección de firma en su perfil
- No afecta el funcionamiento normal del sistema
- PDFs mostrarán espacio vacío para firma manuscrita

---

## 🐛 Troubleshooting Rápido

**Problema:** "Sección de firma no aparece"
```
Solución: Verificar que el usuario tiene registro en tabla comerciales con su id_usuario
```

**Problema:** "Error al guardar firma"
```
Solución: Ejecutar migración SQL (Paso 1 arriba)
```

**Problema:** "Firma no aparece en PDF"
```
Solución: 
1. Verificar que la firma se guardó: SELECT firma_comercial FROM comerciales WHERE id_usuario = X
2. Verificar que existe $_SESSION['id_usuario'] al generar PDF
```

---

## 📧 Soporte

Para más detalles, consultar: `docs/COMPLETADO_punto14_firma_empleado.md`

**Desarrollador:** Luis - Innovabyte  
**Fecha:** 20 de enero de 2025
