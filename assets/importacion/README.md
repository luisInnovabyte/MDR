# Importación de datos desde Access — MDR ERP Manager

> Documentación de los scripts de migración de datos desde la base de datos Access legada a MySQL/MariaDB (toldos_db).  
> Ubicación: `assets/importacion/`  
> Última actualización: 12 de marzo de 2026

---

## Estructura de carpetas

```
assets/importacion/
├── README.md                  ← Este archivo
├── clientes/
│   ├── CLIENTES.csv           ← Exportación de Access (ISO-8859-1, sep ;)
│   ├── importar_clientes.php  ← Script de importación
│   └── tablas.txt             ← Lista de todas las tablas de Access
├── familias/
│   ├── FAMILIAS.TXT           ← Exportación de Access (ISO-8859-1, sep ;)
│   ├── importar_familias.php  ← Script de importación + mapeo
│   └── mapeo_familias.json    ← Generado al ejecutar (id_access → id_mysql)
└── articulos/
    ├── ARTICULOS.TXT          ← Exportación de Access (ISO-8859-1, sep ;)
    ├── mapeo_familias.json    ← Copia de familias/mapeo_familias.json (prerrequisito)
    ├── importar_articulos.php ← Script de importación + mapeo
    └── mapeo_articulos.json   ← Generado al ejecutar (cod_access → id_mysql)
```

---

## Patrón base de los scripts (replicable)

Todos los importadores siguen el mismo patrón. Para crear uno nuevo, replicar esta estructura:

### 1. Cabecera y configuración

```php
define('TXT_PATH', __DIR__ . '/ARCHIVO.TXT');
define('TXT_SEP',  ';');
define('TXT_ENC',  '"');

require_once __DIR__ . '/../../../config/conexion.php';
require_once __DIR__ . '/../../../config/funciones.php';
```

### 2. Helpers obligatorios

| Función                                      | Propósito                              |
| -------------------------------------------- | -------------------------------------- |
| `csv_str(?string $valor)`                    | UTF-8 + trim + NULL si vacío           |
| `csv_str_max(?string $valor, int $max, ...)` | Igual + truncar a $max chars con aviso |
| `csv_decimal(?string $valor)`                | Coma → punto + cast float              |
| `out(string $msg)`                           | Salida compatible HTML/CLI             |
| `echo_ok / echo_warn / echo_err`             | Salida con color por tipo              |

### 3. Salida dual CLI / Navegador

```php
$isCli = (php_sapi_name() === 'cli');
if (!$isCli) { /* HTML head + body */ }
```

### 4. Búsqueda del fichero (case-insensitive para Linux)

```php
if (!file_exists($txtPath)) {
    foreach (glob(dirname($txtPath) . '/*') as $f) {
        if (strcasecmp(basename($f), basename($txtPath)) === 0) {
            $txtPath = $f; break;
        }
    }
}
```

### 5. Conexión PDO

```php
$pdo = (new Conexion())->getConexion();
$pdo->exec("SET time_zone = 'Europe/Madrid'");
$registro = new RegistroActividad();
```

### 6. Bucle de lectura

```php
while (($fila = fgetcsv($fh, 0, TXT_SEP, TXT_ENC)) !== false) {
    $linea++;
    if ($linea === 1) { /* saltar cabecera */ continue; }
    /* mapeo de columnas → validar → verificar duplicado → INSERT */
}
```

### 7. Resumen y log

```php
$registro->registrarActividad(
    'importacion',
    'importar_X.php',
    'importacion_completa',
    "Insertados: $cInsertados | Omitidos: $cOmitidos | Vacíos: $cVacios | Errores: $cErrores",
    $cErrores > 0 ? 'warning' : 'info'
);
```

### 8. Ejecución

```
# Navegador (servidor Linux):
http://servidor/MDR/assets/importacion/<entidad>/importar_<entidad>.php

# CLI (si PHP disponible en PATH):
php importar_<entidad>.php
```

---

## Importador: Clientes

| Propiedad           | Valor                                           |
| ------------------- | ----------------------------------------------- |
| **Script**          | `clientes/importar_clientes.php`                |
| **Fichero origen**  | `clientes/CLIENTES.csv`                         |
| **Tabla destino**   | `cliente` (toldos_db)                           |
| **Encoding origen** | ISO-8859-1                                      |
| **Separador**       | `;`                                             |
| **id_cliente**      | AUTO_INCREMENT (no se preserva el id de Access) |
| **Idempotente**     | Sí — omite `codigo_cliente` si ya existe en BD  |

### Mapeo de columnas (CSV → MySQL)

| Índice | Columna Access | Campo MySQL                     | Notas                |
| ------ | -------------- | ------------------------------- | -------------------- |
| 0      | CLIENTE        | `codigo_cliente`                | Obligatorio          |
| 1      | IC             | —                               | Ignorado             |
| 2      | NOMBRE         | `nombre_cliente`                | Obligatorio          |
| 3      | DIRECCION      | `direccion_cliente`             | Opcional             |
| 4      | CODPOSTAL      | `cp_cliente`                    | Max 10 chars         |
| 5      | POBLACION      | `poblacion_cliente`             | Opcional             |
| 6      | PROVINCIA      | `provincia_cliente`             | Opcional             |
| 7      | CIF            | `nif_cliente`                   | Max 20 chars         |
| 8      | TELEFONO       | `telefono_cliente`              | Opcional             |
| 9      | FAX            | `fax_cliente`                   | Opcional             |
| 10     | WEB            | `web_cliente`                   | Opcional             |
| 11     | EMAIL          | `email_cliente`                 | Lowercase automático |
| 12     | NOMBREFAC      | `nombre_facturacion_cliente`    | Opcional             |
| 13     | DIRECCIONFAC   | `direccion_facturacion_cliente` | Opcional             |
| 14     | CODPOSTALFAC   | `cp_facturacion_cliente`        | Max 10 chars         |
| 15     | POBLACIONFAC   | `poblacion_facturacion_cliente` | Opcional             |
| 16     | PROVINCIAFAC   | `provincia_facturacion_cliente` | Opcional             |
| 17     | DTO1           | `porcentaje_descuento_cliente`  | Coma → punto         |

### Valores fijos insertados

| Campo                    | Valor  | Motivo                              |
| ------------------------ | ------ | ----------------------------------- |
| `id_forma_pago_habitual` | `NULL` | Códigos Access incompatibles con BD |
| `observaciones_cliente`  | `NULL` | No mapeado                          |
| `exento_iva_cliente`     | `0`    | Valor por defecto                   |
| `activo_cliente`         | `1`    | Activo al importar                  |

---

## Importador: Familias

| Propiedad           | Valor                                                                |
| ------------------- | -------------------------------------------------------------------- |
| **Script**          | `familias/importar_familias.php`                                     |
| **Fichero origen**  | `familias/FAMILIAS.TXT`                                              |
| **Tabla destino**   | `familia` (toldos_db)                                                |
| **Encoding origen** | ISO-8859-1                                                           |
| **Separador**       | `;`                                                                  |
| **id_familia**      | AUTO_INCREMENT (no se preserva el id de Access)                      |
| **Idempotente**     | Sí — omite el registro si su `codigo_familia` ya existe en BD        |
| **Mapeo**           | Se muestra en pantalla al finalizar (`{ id_access: id_mysql, ... }`) |

### Mapeo de columnas (TXT → MySQL)

| Índice | Columna Access | Campo MySQL      | Notas                                      |
| ------ | -------------- | ---------------- | ------------------------------------------ |
| 0      | FAMILIA        | —                | id de Access, solo para el mapeo de salida |
| 1      | IC             | —                | Ignorado                                   |
| 2      | DESCRIPCION    | `nombre_familia` | Obligatorio. Normalizado a Ucfirst         |

### Generación automática de `codigo_familia`

```
DESCRIPCION → quitar acentos (iconv TRANSLIT) → mayúsculas → primera palabra → 3 chars → "FAM-XXX"

Ejemplos:
  "ILUMINACIÓN"       → FAM-ILU
  "IMAGEN"            → FAM-IMA
  "SONIDO"            → FAM-SON
  "COMPLEMENTO"       → FAM-COM
  "VENTA"             → FAM-VEN
  "PRODUCCIÓN PROPIA" → FAM-PRO
  "MANO DE OBRA"      → FAM-MAN
  "VARIOS"            → FAM-VAR
```

> Si el código generado ya existe en BD, el registro se **omite** (no se genera FAM-ILU2 ni variantes).

### Normalización del nombre

```php
// Primera letra mayúscula, resto minúsculas (respeta UTF-8 / acentos)
$descripcion = mb_strtolower($descripcion, 'UTF-8');
$descripcion = mb_strtoupper(mb_substr($descripcion, 0, 1, 'UTF-8'), 'UTF-8')
             . mb_substr($descripcion, 1, null, 'UTF-8');
// "ILUMINACIÓN" → "Iluminación"
```

### Valores fijos insertados

| Campo                               | Valor                     |
| ----------------------------------- | ------------------------- |
| `id_grupo`                          | `NULL`                    |
| `name_familia`                      | `'(pending translation)'` |
| `descr_familia`                     | `NULL`                    |
| `activo_familia`                    | `1`                       |
| `permite_descuento_familia`         | `1`                       |
| `coeficiente_familia`               | `NULL`                    |
| `id_unidad_familia`                 | `NULL`                    |
| `imagen_familia`                    | `NULL`                    |
| `observaciones_presupuesto_familia` | `NULL`                    |
| `observations_budget_familia`       | `NULL`                    |
| `orden_obs_familia`                 | `100`                     |

### Salida del mapeo

Al finalizar, si se han insertado registros nuevos, el script muestra en pantalla el JSON de traducción `id_access → id_mysql`. Este mapeo es **necesario** para el futuro script de artículos, que deberá convertir el `idFamilia` de Access al `id_familia` de MySQL.

```json
{
  "10": 171,
  "11": 172,
  "12": 173,
  "13": 174
}
```

Copiar este JSON y guardarlo manualmente como `familias/mapeo_familias.json` para usarlo en `importar_articulos.php`.

---

## Tablas de Access pendientes de importar

Extraído de `clientes/tablas.txt`. Las marcadas con ✅ ya están importadas.

| Estado | Tabla Access       | Script previsto                   | Dependencias                        |
| ------ | ------------------ | --------------------------------- | ----------------------------------- |
| ✅     | CLIENTES           | `importar_clientes.php`           | —                                   |
| ✅     | FAMILIAS           | `importar_familias.php`           | —                                   |
| ✅     | ARTICULOS          | `importar_articulos.php`          | mapeo_familias.json                 |
| ⬜     | GRUPOS             | `importar_grupos.php`             | —                                   |
| ⬜     | ELEMENTOS          | `importar_elementos.php`          | mapeo artículos                     |
| ⬜     | EMPLEADOS          | `importar_empleados.php`          | —                                   |
| ⬜     | EMPRESAS           | `importar_empresas.php`           | —                                   |
| ⬜     | FORMAPAGOS         | `importar_formas_pago.php`        | —                                   |
| ⬜     | PROVEEDORES        | pendiente                         | —                                   |
| ⬜     | ESTADOS            | pendiente                         | —                                   |
| ⬜     | TIPOIVAS           | pendiente                         | —                                   |
| ⬜     | PRESUPUESTOS       | `importar_presupuestos.php`       | clientes, empleados, formas de pago |
| ⬜     | LINEASPRESUPUESTOS | `importar_lineas_presupuesto.php` | presupuestos, artículos             |

> Consultar `clientes/tablas.txt` para la lista completa de tablas Access.

---

## Orden de ejecución (dependencias)

```
GRUPOS
FAMILIAS  ──────────────────────────┐
                                    ↓
           ARTICULOS (mapeo familias → mapeo_familias.json)
                                    ↓
                        ELEMENTOS (mapeo artículos)

CLIENTES
EMPLEADOS
FORMAPAGOS
                                    ↓
           PRESUPUESTOS (clientes + empleados + formas de pago)
                                    ↓
                        LINEASPRESUPUESTOS (presupuestos + artículos)
```

---

## Checklist para crear un nuevo importador

```
[ ] 1.  Crear carpeta:  assets/importacion/<entidad>/
[ ] 2.  Copiar el fichero TXT/CSV de Access a esa carpeta
[ ] 3.  Copiar importar_familias.php como plantilla base
[ ] 4.  Actualizar define('TXT_PATH', ...) con el nombre del fichero
[ ] 5.  Ajustar el mapeo de columnas según la cabecera del TXT
[ ] 6.  Definir campos obligatorios y opcionales
[ ] 7.  Añadir verificación de duplicados por el campo único de negocio
[ ] 8.  Añadir normalizaciones necesarias (ucfirst, strtolower email, etc.)
[ ] 9.  Si depende de otra entidad, cargar el mapeo JSON de entrada
[ ] 10. Si genera mapeo para otras entidades, mostrarlo en pantalla al finalizar
[ ] 11. Probar en navegador: http://servidor/MDR/assets/importacion/<entidad>/importar_<entidad>.php
[ ] 12. Verificar resumen: Insertados / Omitidos / Vacíos / Errores
[ ] 13. Revisar logs en public/logs/<fecha>.json
[ ] 14. Actualizar este README: marcar tabla como ✅ en la sección de pendientes
```
