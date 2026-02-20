<?php
/**
 * Importador de clientes desde CSV
 * Proyecto: MDR ERP Manager
 *
 * Origen : CLIENTES.CSV  (separador ; | codificación ISO-8859-1 | comillas ")
 * Destino: tabla `cliente` (toldos_db)
 *
 * Comportamiento:
 *  - Omite cabecera (primera fila)
 *  - Convierte encoding ISO-8859-1 → UTF-8
 *  - Salta registros cuyo codigo_cliente ya existe en BD (no duplica)
 *  - id_forma_pago_habitual = NULL (los códigos del CSV no coinciden con la BD)
 *  - exento_iva_cliente     = 0
 *  - activo_cliente         = 1
 *
 * Uso:
 *  · Navegador : http://servidor/MDR/assets/importacion/clientes/importar_clientes.php
 *  · CLI        : php importar_clientes.php
 *
 * Reutilizable: puede ejecutarse varias veces; los ya importados se omiten.
 */

// ─── Configuración ────────────────────────────────────────────────────────────
define('CSV_PATH',  __DIR__ . '/CLIENTES.CSV');
define('CSV_SEP',   ';');
define('CSV_ENC',   '"');

// Ruta relativa desde assets/importacion/clientes/ hasta config/
require_once __DIR__ . '/../../../config/conexion.php';
require_once __DIR__ . '/../../../config/funciones.php';

// ─── Helpers ──────────────────────────────────────────────────────────────────

/**
 * Convierte un valor del CSV a UTF-8, elimina espacios y devuelve NULL si vacío.
 */
function csv_str(?string $valor): ?string
{
    if ($valor === null) return null;
    $v = mb_convert_encoding(trim($valor), 'UTF-8', 'ISO-8859-1');
    return ($v === '') ? null : $v;
}

/**
 * Trunca una cadena a $max caracteres (con aviso) y devuelve NULL si vacía.
 */
function csv_str_max(?string $valor, int $max, int $linea, string $campo): ?string
{
    $v = csv_str($valor);
    if ($v === null) return null;
    if (mb_strlen($v) > $max) {
        echo_warn("Línea $linea — campo $campo truncado a $max chars: \"$v\"");
        $v = mb_substr($v, 0, $max);
    }
    return $v;
}

/**
 * Convierte un valor decimal con coma ('60,00') a float.
 * Devuelve 0.00 si está vacío o no es numérico.
 */
function csv_decimal(?string $valor): float
{
    if ($valor === null || trim($valor) === '') return 0.00;
    $v = str_replace(',', '.', trim($valor));
    return is_numeric($v) ? (float)$v : 0.00;
}

// ─── Salida HTML/CLI ──────────────────────────────────────────────────────────
$isCli = (php_sapi_name() === 'cli');

function out(string $msg): void
{
    global $isCli;
    echo $isCli ? $msg . PHP_EOL : htmlspecialchars($msg) . "<br>\n";
}

function echo_warn(string $msg): void
{
    global $isCli;
    echo $isCli
        ? "  ⚠  $msg" . PHP_EOL
        : '<span style="color:#e67e22">⚠ ' . htmlspecialchars($msg) . "</span><br>\n";
}

function echo_err(string $msg): void
{
    global $isCli;
    echo $isCli
        ? "  ✗  $msg" . PHP_EOL
        : '<span style="color:#e74c3c">✗ ' . htmlspecialchars($msg) . "</span><br>\n";
}

function echo_ok(string $msg): void
{
    global $isCli;
    echo $isCli
        ? "  ✔  $msg" . PHP_EOL
        : '<span style="color:#27ae60">✔ ' . htmlspecialchars($msg) . "</span><br>\n";
}

// ─── Cabecera HTML (solo navegador) ──────────────────────────────────────────
if (!$isCli) {
    echo '<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8">
    <title>Importación Clientes - MDR</title>
    <style>
        body { font-family: monospace; font-size: 13px; padding: 20px; background:#1e1e1e; color:#d4d4d4; }
        h1   { color:#569cd6; }
        h2   { color:#9cdcfe; margin-top:20px; }
        hr   { border-color:#444; }
        .box { background:#252526; border:1px solid #3c3c3c; padding:15px; border-radius:4px; }
    </style></head><body>
    <h1>Importación de Clientes desde CSV</h1>
    <div class="box">' . "\n";
}

// ─── Verificar archivo CSV (búsqueda case-insensitive para Linux) ─────────────
$csvPath = CSV_PATH;
if (!file_exists($csvPath)) {
    // Intentar encontrar el fichero con cualquier combinación de mayúsculas/minúsculas
    $dir     = dirname($csvPath);
    $name    = basename($csvPath);
    $matches = glob($dir . '/*');
    $found   = false;
    if ($matches) {
        foreach ($matches as $f) {
            if (strcasecmp(basename($f), $name) === 0) {
                $csvPath = $f;
                $found   = true;
                out("Fichero encontrado como: " . basename($csvPath));
                break;
            }
        }
    }
    if (!$found) {
        echo_err('No se encontró el fichero: ' . $csvPath);
        echo_err('Directorio buscado: ' . $dir);
        $listar = glob($dir . '/*.{csv,CSV,Csv}', GLOB_BRACE);
        if ($listar) {
            echo_err('Ficheros CSV en el directorio:');
            foreach ($listar as $f) { echo_err('  · ' . basename($f)); }
        } else {
            echo_err('No hay ficheros CSV en: ' . $dir);
        }
        if (!$isCli) echo '</div></body></html>';
        exit(1);
    }
}

// ─── Conexión PDO ─────────────────────────────────────────────────────────────
try {
    $pdo = (new Conexion())->getConexion();
    $pdo->exec("SET time_zone = 'Europe/Madrid'");
    $registro = new RegistroActividad();
} catch (Exception $e) {
    echo_err('Error de conexión: ' . $e->getMessage());
    if (!$isCli) echo '</div></body></html>';
    exit(1);
}

out('Conexión a BD establecida.');
out('Fichero CSV: ' . $csvPath);
out('');

// ─── Prepared statements ──────────────────────────────────────────────────────

// Verificar si un codigo_cliente ya existe
$stmtCheck = $pdo->prepare("
    SELECT COUNT(*) AS total
    FROM cliente
    WHERE LOWER(codigo_cliente) = LOWER(?)
");

// INSERT principal (sin id_cliente → AUTO_INCREMENT)
$stmtInsert = $pdo->prepare("
    INSERT INTO cliente (
        codigo_cliente,
        nombre_cliente,
        direccion_cliente,
        cp_cliente,
        poblacion_cliente,
        provincia_cliente,
        nif_cliente,
        telefono_cliente,
        fax_cliente,
        web_cliente,
        email_cliente,
        nombre_facturacion_cliente,
        direccion_facturacion_cliente,
        cp_facturacion_cliente,
        poblacion_facturacion_cliente,
        provincia_facturacion_cliente,
        id_forma_pago_habitual,
        porcentaje_descuento_cliente,
        observaciones_cliente,
        exento_iva_cliente,
        activo_cliente
    ) VALUES (
        ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
        ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
    )
");

// ─── Lectura y procesado del CSV ──────────────────────────────────────────────
$fh = fopen($csvPath, 'r');
if ($fh === false) {
    echo_err('No se pudo abrir el fichero CSV.');
    if (!$isCli) echo '</div></body></html>';
    exit(1);
}

$cInsertados = 0;
$cOmitidos   = 0;
$cVacios     = 0;
$cErrores    = 0;
$linea       = 0;

while (($fila = fgetcsv($fh, 0, CSV_SEP, CSV_ENC)) !== false) {
    $linea++;

    // Saltar cabecera
    if ($linea === 1) {
        out('Cabecera detectada: ' . implode(' | ', $fila));
        out(str_repeat('─', 60));
        continue;
    }

    // Asegurar que la fila tiene al menos el código y nombre
    if (count($fila) < 3) {
        echo_warn("Línea $linea — fila con menos de 3 columnas, se omite.");
        $cVacios++;
        continue;
    }

    // ── Mapeo de columnas ────────────────────────────────────────────────────
    // Índices según cabecera:
    // 0:CLIENTE 1:IC 2:NOMBRE 3:DIRECCION 4:CODPOSTAL 5:POBLACION 6:PROVINCIA
    // 7:CIF 8:TELEFONO 9:FAX 10:WEB 11:EMAIL 12:NOMBREFAC 13:DIRECCIONFAC
    // 14:CODPOSTALFAC 15:POBLACIONFAC 16:PROVINCIAFAC 17:DTO1 ...

    $codigo    = csv_str($fila[0] ?? null);
    $nombre    = csv_str($fila[2] ?? null);

    // Código y nombre son obligatorios
    if (empty($codigo)) {
        echo_warn("Línea $linea — CLIENTE vacío, se omite.");
        $cVacios++;
        continue;
    }
    if (empty($nombre)) {
        echo_warn("Línea $linea — NOMBRE vacío para código '$codigo', se omite.");
        $cVacios++;
        continue;
    }

    // ── Verificar duplicado ──────────────────────────────────────────────────
    try {
        $stmtCheck->execute([$codigo]);
        $existe = (int)$stmtCheck->fetch(PDO::FETCH_ASSOC)['total'];
    } catch (PDOException $e) {
        echo_err("Línea $linea — Error al verificar duplicado '$codigo': " . $e->getMessage());
        $cErrores++;
        continue;
    }

    if ($existe > 0) {
        out("  ↷  Línea $linea — '$codigo' ya existe, omitido.");
        $cOmitidos++;
        continue;
    }

    // ── Resto de campos ──────────────────────────────────────────────────────
    $direccion         = csv_str($fila[3]  ?? null);
    $cp                = csv_str_max($fila[4]  ?? null, 10, $linea, 'CODPOSTAL');
    $poblacion         = csv_str($fila[5]  ?? null);
    $provincia         = csv_str($fila[6]  ?? null);
    $nif               = csv_str_max($fila[7]  ?? null, 20, $linea, 'CIF');
    $telefono          = csv_str($fila[8]  ?? null);
    $fax               = csv_str($fila[9]  ?? null);
    $web               = csv_str($fila[10] ?? null);
    $email             = ($e = csv_str($fila[11] ?? null)) !== null ? strtolower($e) : null;
    $nombreFac         = csv_str($fila[12] ?? null);
    $direccionFac      = csv_str($fila[13] ?? null);
    $cpFac             = csv_str_max($fila[14] ?? null, 10, $linea, 'CODPOSTALFAC');
    $poblacionFac      = csv_str($fila[15] ?? null);
    $provinciaFac      = csv_str($fila[16] ?? null);
    $descuento         = csv_decimal($fila[17] ?? null);

    // ── INSERT ───────────────────────────────────────────────────────────────
    try {
        $stmtInsert->bindValue(1,  $codigo,       PDO::PARAM_STR);
        $stmtInsert->bindValue(2,  $nombre,       PDO::PARAM_STR);
        $stmtInsert->bindValue(3,  $direccion,    $direccion    !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
        $stmtInsert->bindValue(4,  $cp,           $cp           !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
        $stmtInsert->bindValue(5,  $poblacion,    $poblacion    !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
        $stmtInsert->bindValue(6,  $provincia,    $provincia    !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
        $stmtInsert->bindValue(7,  $nif,          $nif          !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
        $stmtInsert->bindValue(8,  $telefono,     $telefono     !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
        $stmtInsert->bindValue(9,  $fax,          $fax          !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
        $stmtInsert->bindValue(10, $web,          $web          !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
        $stmtInsert->bindValue(11, $email,        $email        !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
        $stmtInsert->bindValue(12, $nombreFac,    $nombreFac    !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
        $stmtInsert->bindValue(13, $direccionFac, $direccionFac !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
        $stmtInsert->bindValue(14, $cpFac,        $cpFac        !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
        $stmtInsert->bindValue(15, $poblacionFac, $poblacionFac !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
        $stmtInsert->bindValue(16, $provinciaFac, $provinciaFac !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
        $stmtInsert->bindValue(17, null,          PDO::PARAM_NULL);  // id_forma_pago_habitual → NULL
        $stmtInsert->bindValue(18, $descuento,    PDO::PARAM_STR);
        $stmtInsert->bindValue(19, null,          PDO::PARAM_NULL);  // observaciones_cliente → NULL
        $stmtInsert->bindValue(20, 0,             PDO::PARAM_INT);   // exento_iva_cliente
        $stmtInsert->bindValue(21, 1,             PDO::PARAM_INT);   // activo_cliente

        $stmtInsert->execute();
        $cInsertados++;
        echo_ok("Línea $linea — '$codigo' · $nombre — INSERTADO");

    } catch (PDOException $e) {
        echo_err("Línea $linea — Error al insertar '$codigo': " . $e->getMessage());
        $registro->registrarActividad(
            'importacion',
            'importar_clientes.php',
            'insert_cliente',
            "Error línea $linea - código $codigo: " . $e->getMessage(),
            'error'
        );
        $cErrores++;
    }
}

fclose($fh);

// ─── Resumen final ────────────────────────────────────────────────────────────
$totalProcesadas = $linea - 1; // sin cabecera

out('');
out(str_repeat('═', 60));
if (!$isCli) echo '<h2>Resumen de importación</h2>';
out('Total filas procesadas : ' . $totalProcesadas);
echo_ok('Insertados           : ' . $cInsertados);
out("  ↷  Omitidos (ya existían): $cOmitidos");
if ($cVacios   > 0) echo_warn("Omitidos (vacíos)    : $cVacios");
if ($cErrores  > 0) echo_err("Errores              : $cErrores");
out(str_repeat('═', 60));

$registro->registrarActividad(
    'importacion',
    'importar_clientes.php',
    'importacion_completa',
    "Importación finalizada — Insertados: $cInsertados | Omitidos: $cOmitidos | Vacíos: $cVacios | Errores: $cErrores",
    $cErrores > 0 ? 'warning' : 'info'
);

if (!$isCli) {
    echo '</div></body></html>';
}
