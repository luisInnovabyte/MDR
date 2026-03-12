<?php

/**
 * Importador de familias desde TXT (exportado de Access)
 * Proyecto: MDR ERP Manager
 *
 * Origen : FAMILIAS.TXT  (separador ; | codificación ISO-8859-1 | comillas ")
 * Destino: tabla `familia` (toldos_db)
 *
 * Comportamiento:
 *  - Omite cabecera (primera fila)
 *  - Convierte encoding ISO-8859-1 → UTF-8
 *  - Salta registros cuyo codigo_familia ya existe en BD (no duplica)
 *  - Genera codigo_familia automáticamente desde DESCRIPCION (ej: FAM-ILU)
 *  - name_familia = '(pending translation)'
 *  - id_grupo, coeficiente, id_unidad, imagen, observaciones → NULL
 *  - activo_familia = 1, permite_descuento_familia = 1
 *  - Al finalizar genera mapeo_familias.json: { "id_access": id_mysql, ... }
 *
 * Uso:
 *  · Navegador : http://servidor/MDR/assets/importacion/familias/importar_familias.php
 *  · CLI        : php importar_familias.php
 *
 * Reutilizable: puede ejecutarse varias veces; los ya importados se omiten.
 * El mapeo_familias.json se usará en el futuro script de artículos para
 * traducir idFamilia de Access → id_familia de MySQL.
 */

// ─── Configuración ────────────────────────────────────────────────────────────
define('TXT_PATH',    __DIR__ . '/FAMILIAS.TXT');
define('TXT_SEP',     ';');
define('TXT_ENC',     '"');

// Ruta relativa desde assets/importacion/familias/ hasta config/
require_once __DIR__ . '/../../../config/conexion.php';
require_once __DIR__ . '/../../../config/funciones.php';

// ─── Helpers ──────────────────────────────────────────────────────────────────

/**
 * Convierte un valor del TXT a UTF-8, elimina espacios y devuelve NULL si vacío.
 */
function csv_str(?string $valor): ?string
{
    if ($valor === null) return null;
    $v = mb_convert_encoding(trim($valor), 'UTF-8', 'ISO-8859-1');
    return ($v === '') ? null : $v;
}

/**
 * Genera un codigo_familia a partir de la descripción.
 * Quita acentos, toma las 3 primeras letras de la primera palabra.
 * Ejemplo: "ILUMINACIÓN" → "FAM-ILU"
 */
function generarCodigo(string $descripcion): string
{
    // Quitar acentos y caracteres especiales
    $limpio = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $descripcion);
    $limpio = strtoupper(preg_replace('/[^A-Za-z0-9 ]/', '', $limpio));

    // Primera palabra
    $partes   = preg_split('/\s+/', trim($limpio));
    $primera  = $partes[0] ?? 'FAM';

    // Tomar máximo 3 caracteres
    $sufijo = strtoupper(mb_substr($primera, 0, 3));

    return 'FAM-' . $sufijo;
}

/**
 * Devuelve el codigo_familia si NO existe en BD, o null si ya está creado.
 */
function codigoUnico(PDO $pdo, string $base): ?string
{
    $stmt = $pdo->prepare("SELECT COUNT(*) AS total FROM familia WHERE LOWER(codigo_familia) = LOWER(?)");
    $stmt->execute([$base]);
    $total = (int)$stmt->fetch(PDO::FETCH_ASSOC)['total'];
    return $total === 0 ? $base : null;
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
    <title>Importación Familias - MDR</title>
    <style>
        body { font-family: monospace; font-size: 13px; padding: 20px; background:#1e1e1e; color:#d4d4d4; }
        h1   { color:#569cd6; }
        h2   { color:#9cdcfe; margin-top:20px; }
        hr   { border-color:#444; }
        .box { background:#252526; border:1px solid #3c3c3c; padding:15px; border-radius:4px; }
    </style></head><body>
    <h1>Importación de Familias desde TXT (Access)</h1>
    <div class="box">' . "\n";
}

// ─── Verificar archivo TXT (búsqueda case-insensitive) ───────────────────────
$txtPath = TXT_PATH;
if (!file_exists($txtPath)) {
    $dir     = dirname($txtPath);
    $name    = basename($txtPath);
    $matches = glob($dir . '/*');
    $found   = false;
    if ($matches) {
        foreach ($matches as $f) {
            if (strcasecmp(basename($f), $name) === 0) {
                $txtPath = $f;
                $found   = true;
                out("Fichero encontrado como: " . basename($txtPath));
                break;
            }
        }
    }
    if (!$found) {
        echo_err('No se encontró el fichero: ' . $txtPath);
        echo_err('Directorio buscado: ' . $dir);
        $listar = glob($dir . '/*.{txt,TXT,Txt}', GLOB_BRACE);
        if ($listar) {
            echo_err('Ficheros TXT en el directorio:');
            foreach ($listar as $f) {
                echo_err('  · ' . basename($f));
            }
        } else {
            echo_err('No hay ficheros TXT en: ' . $dir);
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
out('Fichero TXT: ' . $txtPath);
out('');

// ─── Prepared statements ──────────────────────────────────────────────────────

// INSERT en tabla familia (sin id_familia → AUTO_INCREMENT)
$stmtInsert = $pdo->prepare("
    INSERT INTO familia (
        id_grupo,
        codigo_familia,
        nombre_familia,
        name_familia,
        descr_familia,
        activo_familia,
        permite_descuento_familia,
        coeficiente_familia,
        id_unidad_familia,
        imagen_familia,
        observaciones_presupuesto_familia,
        observations_budget_familia,
        orden_obs_familia
    ) VALUES (
        NULL, ?, ?, ?, NULL, 1, 1, NULL, NULL, NULL, NULL, NULL, 100
    )
");

// ─── Lectura y procesado del TXT ─────────────────────────────────────────────
$fh = fopen($txtPath, 'r');
if ($fh === false) {
    echo_err('No se pudo abrir el fichero TXT.');
    if (!$isCli) echo '</div></body></html>';
    exit(1);
}

$cInsertados = 0;
$cOmitidos   = 0;
$cVacios     = 0;
$cErrores    = 0;
$linea       = 0;
$mapeo       = [];  // [ id_access => id_mysql ]

while (($fila = fgetcsv($fh, 0, TXT_SEP, TXT_ENC)) !== false) {
    $linea++;

    // Saltar cabecera
    if ($linea === 1) {
        out('Cabecera detectada: ' . implode(' | ', $fila));
        out(str_repeat('─', 60));
        continue;
    }

    // Asegurar que la fila tiene al menos 3 columnas
    if (count($fila) < 3) {
        echo_warn("Línea $linea — fila con menos de 3 columnas, se omite.");
        $cVacios++;
        continue;
    }

    // ── Mapeo de columnas ────────────────────────────────────────────────────
    // 0:FAMILIA (id Access)  |  1:IC (ignorar)  |  2:DESCRIPCION

    $id_access   = trim($fila[0] ?? '');
    // $fila[1] = IC → se ignora
    $descripcion = csv_str($fila[2] ?? null);

    // Validaciones obligatorias
    if ($id_access === '' || !is_numeric($id_access)) {
        echo_warn("Línea $linea — FAMILIA vacío o no numérico, se omite.");
        $cVacios++;
        continue;
    }
    if (empty($descripcion)) {
        echo_warn("Línea $linea — DESCRIPCION vacía para ID '$id_access', se omite.");
        $cVacios++;
        continue;
    }

    $id_access = (int)$id_access;

    // Normalizar nombre: primera letra mayúscula, resto minúsculas
    $descripcion = mb_strtolower($descripcion, 'UTF-8');
    $descripcion = mb_strtoupper(mb_substr($descripcion, 0, 1, 'UTF-8'), 'UTF-8') . mb_substr($descripcion, 1, null, 'UTF-8');

    // ── Generar codigo_familia y verificar si ya existe ─────────────────────
    $codigoBase = generarCodigo($descripcion);
    $codigo     = codigoUnico($pdo, $codigoBase);

    if ($codigo === null) {
        out("  ↷  Línea $linea — '$codigoBase' ya existe en BD, omitido.");
        $cOmitidos++;
        continue;
    }

    // ── INSERT ───────────────────────────────────────────────────────────────
    try {
        $stmtInsert->bindValue(1, $codigo,      PDO::PARAM_STR);
        $stmtInsert->bindValue(2, $descripcion, PDO::PARAM_STR);
        $stmtInsert->bindValue(3, '(pending translation)', PDO::PARAM_STR);
        $stmtInsert->execute();

        $id_mysql = (int)$pdo->lastInsertId();
        $mapeo[$id_access] = $id_mysql;

        $cInsertados++;
        echo_ok("Línea $linea — Access[$id_access] → MySQL[$id_mysql] · $codigo · $descripcion — INSERTADO");
    } catch (PDOException $e) {
        echo_err("Línea $linea — Error al insertar '$descripcion': " . $e->getMessage());
        $registro->registrarActividad(
            'importacion',
            'importar_familias.php',
            'insert_familia',
            "Error línea $linea - id_access $id_access: " . $e->getMessage(),
            'error'
        );
        $cErrores++;
    }
}

fclose($fh);

// ─── Mostrar mapeo id_access → id_mysql ─────────────────────────────────────
out('');
out('─── MAPEO id_access → id_mysql ' . str_repeat('─', 28));
if (!empty($mapeo)) {
    $jsonBonito = json_encode($mapeo, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    if ($isCli) {
        echo $jsonBonito . PHP_EOL;
    } else {
        echo '<pre style="background:#1a1a2e;color:#00ff88;padding:12px;border-radius:4px;margin-top:8px;">' . htmlspecialchars($jsonBonito) . '</pre>' . "\n";
    }
} else {
    out('(ningún registro nuevo insertado, no hay mapeo que mostrar)');
}

// ─── Resumen final ────────────────────────────────────────────────────────────
$totalProcesadas = $linea - 1; // sin cabecera

out('');
out(str_repeat('═', 60));
if (!$isCli) echo '<h2>Resumen de importación</h2>';
out('Total filas procesadas : ' . $totalProcesadas);
echo_ok('Insertados             : ' . $cInsertados);
out("  ↷  Omitidos (ya existían): $cOmitidos");
if ($cVacios  > 0) echo_warn("Omitidos (vacíos)      : $cVacios");
if ($cErrores > 0) echo_err("Errores                : $cErrores");
out(str_repeat('═', 60));

$registro->registrarActividad(
    'importacion',
    'importar_familias.php',
    'importacion_completa',
    "Importación finalizada — Insertados: $cInsertados | Omitidos: $cOmitidos | Vacíos: $cVacios | Errores: $cErrores",
    $cErrores > 0 ? 'warning' : 'info'
);

if (!$isCli) {
    echo '</div></body></html>';
}
