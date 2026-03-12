<?php

/**
 * Importador de artículos desde TXT (exportado de Access)
 * Proyecto: MDR ERP Manager
 *
 * Origen  : ARTICULOS.TXT  (separador ; | codificación ISO-8859-1 | comillas ")
 * Destino : tabla `articulo` (toldos_db)
 *
 * PRERREQUISITO:
 *  · mapeo_familias.json en la misma carpeta (generado por importar_familias.php)
 *    Formato: { "id_access": id_mysql, ... }  ej: { "10": 171, "11": 172 }
 *
 * Comportamiento:
 *  - Omite cabecera (primera fila)
 *  - Convierte encoding ISO-8859-1 → UTF-8
 *  - Salta artículos cuyo nombre_articulo ya existe en BD (idempotente)
 *  - Salta artículos cuya familia de Access no está en mapeo_familias.json
 *  - Genera codigo_articulo: primeras 3 letras de palabra1 + palabra2 + secuencial
 *    Ejemplos: "PROYECTOR DATOS..." → PRO-DAT-001 | "MICRÓFONO SOBREMESA" → MIC-SOB-001
 *  - Normaliza nombre: primera letra mayúscula, resto minúsculas
 *  - name_articulo = '(pending translation)'
 *  - Al finalizar muestra en pantalla Y guarda mapeo_articulos.json: { "cod_access": id_mysql }
 *    (necesario para el futuro importar_elementos.php)
 *
 * Uso:
 *  · Navegador : http://servidor/MDR/assets/importacion/articulos/importar_articulos.php
 *  · CLI        : php importar_articulos.php
 *
 * Reutilizable: puede ejecutarse varias veces; los ya importados se omiten.
 */

// ─── Configuración ────────────────────────────────────────────────────────────
define('TXT_PATH',              __DIR__ . '/ARTICULOS.TXT');
define('TXT_SEP',               ';');
define('TXT_ENC',               '"');
define('MAPEO_FAMILIAS_PATH',   __DIR__ . '/mapeo_familias.json');
define('MAPEO_ARTICULOS_PATH',  __DIR__ . '/mapeo_articulos.json');

// ─── Configuración de ejecución ───────────────────────────────────────────────
set_time_limit(0);                   // Sin límite de tiempo (fichero grande)
ini_set('output_buffering', 'off');
ob_implicit_flush(true);
if (ob_get_level()) ob_end_flush();  // Vaciar buffer existente para salida en tiempo real

// Ruta relativa desde assets/importacion/articulos/ hasta config/
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
 * Convierte un valor decimal con coma ('120,00') a float.
 * Devuelve 0.00 si está vacío o no es numérico.
 */
function csv_decimal(?string $valor): float
{
    if ($valor === null || trim($valor) === '') return 0.00;
    $v = str_replace(',', '.', trim($valor));
    return is_numeric($v) ? (float)$v : 0.00;
}

/**
 * Genera el prefijo del código de artículo a partir de la descripción.
 * Quita acentos, toma 3 chars de palabra[0] y 3 chars de palabra[1].
 *
 * Ejemplos:
 *   "Proyector datos/video 2000 lúmenes" → "PRO-DAT"
 *   "Micrófono sobremesa"                → "MIC-SOB"
 *   "Atril"                              → "ATR"
 */
function generarCodigoBase(string $descripcion): string
{
    $limpio = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $descripcion);
    $limpio = strtoupper(preg_replace('/[^A-Za-z0-9 ]/', ' ', $limpio));
    $partes = array_values(array_filter(preg_split('/\s+/', trim($limpio))));

    $p1 = strtoupper(mb_substr($partes[0] ?? 'ART', 0, 3));
    $p2 = isset($partes[1]) ? strtoupper(mb_substr($partes[1], 0, 3)) : null;

    return $p2 !== null ? $p1 . '-' . $p2 : $p1;
}

/**
 * Devuelve el siguiente código único para un prefijo dado.
 * Usa un cache en memoria para evitar una query por artículo (problema N+1).
 * La primera vez que se usa un prefijo consulta la BD; las siguientes solo incrementa.
 */
function codigoUnicoArticulo(PDO $pdo, string $base, array &$cache): string
{
    if (!isset($cache[$base])) {
        // Primera vez con este prefijo: consultar BD para conocer el máximo actual
        $stmt = $pdo->prepare("
            SELECT codigo_articulo FROM articulo
            WHERE codigo_articulo LIKE ?
        ");
        $stmt->execute([$base . '-%']);
        $filas = $stmt->fetchAll(PDO::FETCH_COLUMN);

        $max = 0;
        foreach ($filas as $codigo) {
            $partes = explode('-', $codigo);
            $sufijo = end($partes);
            if (is_numeric($sufijo)) {
                $max = max($max, (int)$sufijo);
            }
        }
        $cache[$base] = $max;
    }

    $cache[$base]++;
    return $base . '-' . str_pad($cache[$base], 3, '0', STR_PAD_LEFT);
}

// ─── Salida HTML/CLI ──────────────────────────────────────────────────────────
$isCli = (php_sapi_name() === 'cli');

function out(string $msg): void
{
    global $isCli;
    echo $isCli ? $msg . PHP_EOL : htmlspecialchars($msg) . "<br>\n";
    flush();
}

function echo_warn(string $msg): void
{
    global $isCli;
    echo $isCli
        ? "  ⚠  $msg" . PHP_EOL
        : '<span style="color:#e67e22">⚠ ' . htmlspecialchars($msg) . "</span><br>\n";
    flush();
}

function echo_err(string $msg): void
{
    global $isCli;
    echo $isCli
        ? "  ✗  $msg" . PHP_EOL
        : '<span style="color:#e74c3c">✗ ' . htmlspecialchars($msg) . "</span><br>\n";
    flush();
}

function echo_ok(string $msg): void
{
    global $isCli;
    echo $isCli
        ? "  ✔  $msg" . PHP_EOL
        : '<span style="color:#27ae60">✔ ' . htmlspecialchars($msg) . "</span><br>\n";
    flush();
}

// ─── Cabecera HTML (solo navegador) ──────────────────────────────────────────
if (!$isCli) {
    echo '<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8">
    <title>Importación Artículos - MDR</title>
    <style>
        body { font-family: monospace; font-size: 13px; padding: 20px; background:#1e1e1e; color:#d4d4d4; }
        h1   { color:#569cd6; }
        h2   { color:#9cdcfe; margin-top:20px; }
        hr   { border-color:#444; }
        .box { background:#252526; border:1px solid #3c3c3c; padding:15px; border-radius:4px; }
    </style></head><body>
    <h1>Importación de Artículos desde TXT (Access)</h1>
    <div class="box">' . "\n";
}

// ─── Cargar mapeo de familias (PRERREQUISITO) ─────────────────────────────────
if (!file_exists(MAPEO_FAMILIAS_PATH)) {
    echo_err('No se encontró mapeo_familias.json en: ' . MAPEO_FAMILIAS_PATH);
    echo_err('Ejecuta primero importar_familias.php y copia el JSON resultante como');
    echo_err('mapeo_familias.json en esta misma carpeta (articulos/).');
    if (!$isCli) echo '</div></body></html>';
    exit(1);
}

$mapeoFamilias = json_decode(file_get_contents(MAPEO_FAMILIAS_PATH), true);
if (!is_array($mapeoFamilias)) {
    echo_err('mapeo_familias.json no es un JSON válido o está vacío.');
    if (!$isCli) echo '</div></body></html>';
    exit(1);
}

out('Mapeo de familias cargado: ' . count($mapeoFamilias) . ' entradas.');
out('  Familias mapeadas: ' . implode(', ', array_keys($mapeoFamilias)));
out('');

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

// Verificar si un nombre_articulo ya existe en BD (idempotencia)
$stmtCheck = $pdo->prepare("
    SELECT COUNT(*) AS total
    FROM articulo
    WHERE LOWER(nombre_articulo) = LOWER(?)
");

// INSERT en tabla articulo (sin id_articulo → AUTO_INCREMENT)
$stmtInsert = $pdo->prepare("
    INSERT INTO articulo (
        id_familia,
        id_unidad,
        codigo_articulo,
        nombre_articulo,
        name_articulo,
        imagen_articulo,
        precio_alquiler_articulo,
        coeficiente_articulo,
        es_kit_articulo,
        control_total_articulo,
        no_facturar_articulo,
        notas_presupuesto_articulo,
        notes_budget_articulo,
        orden_obs_articulo,
        observaciones_articulo,
        activo_articulo,
        permitir_descuentos_articulo,
        precio_editable_articulo,
        id_impuesto
    ) VALUES (
        ?, NULL, ?, ?, ?, NULL, ?, ?, ?, 0, ?, NULL, NULL, 200, NULL, 1, 1, 0, NULL
    )
");

// ─── Lectura y procesado del TXT ─────────────────────────────────────────────
$fh = fopen($txtPath, 'r');
if ($fh === false) {
    echo_err('No se pudo abrir el fichero TXT.');
    if (!$isCli) echo '</div></body></html>';
    exit(1);
}

$cInsertados     = 0;
$cOmitidos       = 0;
$cVacios         = 0;
$cErrores        = 0;
$cSinMapeo       = 0;
$linea           = 0;
$mapeo           = [];  // [ "cod_access" => id_mysql ]
$codigosUsados   = [];  // cache de prefijos → máximo número ya asignado (evita N+1 queries)

while (($fila = fgetcsv($fh, 0, TXT_SEP, TXT_ENC)) !== false) {
    $linea++;

    // Saltar cabecera
    if ($linea === 1) {
        out('Cabecera detectada: ' . implode(' | ', $fila));
        out(str_repeat('─', 60));
        continue;
    }

    // Asegurar columnas mínimas necesarias
    if (count($fila) < 13) {
        echo_warn("Línea $linea — fila con menos de 13 columnas, se omite.");
        $cVacios++;
        continue;
    }

    // ── Mapeo de columnas ────────────────────────────────────────────────────
    // 0:ARTICULO | 1:IC | 2:DESCRIPCION | 3:GRUPO | 4:FAMILIA
    // 5:STOCKDISPONIBLE | 6:STOCKREPARACION | 7:STOCKCLIENTES | 8:STOCKDEPOSITO
    // 9:APLICACOEF | 10:PRECIOVENTA | 11:PRECIOALQUILER | 12:KIT
    // 13:ACUMFACALQUILER | 14:ACUMFACVENTA | 15:CONSUMIBLE
    // 16:TARIFA | 17:ARTTARIFA | 18:ETIQUETA | 19:NOTA | 20:NO_FACTURAR_DOBLE

    $cod_access  = trim($fila[0] ?? '');
    $descripcion = csv_str($fila[2] ?? null);
    $id_fam_acc  = trim($fila[4] ?? '');
    $aplicacoef  = isset($fila[9])  && trim($fila[9])  !== '' ? (int)trim($fila[9])  : 0;
    $precioAlq   = csv_decimal($fila[11] ?? null);
    $esKit       = isset($fila[12]) && trim($fila[12]) !== '' ? (int)trim($fila[12]) : 0;
    $noFacturar  = isset($fila[20]) && trim($fila[20]) !== '' ? (int)trim($fila[20]) : 0;

    // ── Validaciones obligatorias ─────────────────────────────────────────────
    if ($cod_access === '') {
        echo_warn("Línea $linea — ARTICULO vacío, se omite.");
        $cVacios++;
        continue;
    }

    if (empty($descripcion)) {
        echo_warn("Línea $linea — DESCRIPCION vacía para '$cod_access', se omite.");
        $cVacios++;
        continue;
    }

    if ($id_fam_acc === '' || !is_numeric($id_fam_acc)) {
        echo_warn("Línea $linea — '$cod_access': FAMILIA vacía o no numérica, se omite.");
        $cVacios++;
        continue;
    }

    // ── Traducir familia Access → MySQL ──────────────────────────────────────
    $id_fam_int = (int)$id_fam_acc;
    if (!isset($mapeoFamilias[(string)$id_fam_int])) {
        echo_warn("Línea $linea — '$cod_access': familia Access[$id_fam_int] no está en mapeo_familias.json, se omite.");
        $cSinMapeo++;
        continue;
    }
    $id_familia_mysql = (int)$mapeoFamilias[(string)$id_fam_int];

    // ── Normalizar nombre: primera mayúscula, resto minúsculas ───────────────
    $descripcion = mb_strtolower($descripcion, 'UTF-8');
    $descripcion = mb_strtoupper(mb_substr($descripcion, 0, 1, 'UTF-8'), 'UTF-8')
                 . mb_substr($descripcion, 1, null, 'UTF-8');

    // ── Verificar duplicado por nombre (idempotencia) ────────────────────────
    try {
        $stmtCheck->execute([$descripcion]);
        $existe = (int)$stmtCheck->fetch(PDO::FETCH_ASSOC)['total'];
    } catch (PDOException $e) {
        echo_err("Línea $linea — Error al verificar duplicado '$descripcion': " . $e->getMessage());
        $cErrores++;
        continue;
    }

    if ($existe > 0) {
        out("  ↷  Línea $linea — '$cod_access' · '$descripcion' ya existe en BD, omitido.");
        $cOmitidos++;
        continue;
    }

    // ── Generar código único ──────────────────────────────────────────────────
    $codigoBase = generarCodigoBase($descripcion);
    $codigo     = codigoUnicoArticulo($pdo, $codigoBase, $codigosUsados);

    // ── INSERT ────────────────────────────────────────────────────────────────
    try {
        $stmtInsert->bindValue(1, $id_familia_mysql,          PDO::PARAM_INT);
        $stmtInsert->bindValue(2, $codigo,                    PDO::PARAM_STR);
        $stmtInsert->bindValue(3, $descripcion,               PDO::PARAM_STR);
        $stmtInsert->bindValue(4, '(pending translation)',    PDO::PARAM_STR);
        $stmtInsert->bindValue(5, $precioAlq,                 PDO::PARAM_STR);
        $stmtInsert->bindValue(6, $aplicacoef,                PDO::PARAM_INT);
        $stmtInsert->bindValue(7, $esKit,                     PDO::PARAM_INT);
        $stmtInsert->bindValue(8, $noFacturar,                PDO::PARAM_INT);
        $stmtInsert->execute();

        $id_mysql          = (int)$pdo->lastInsertId();
        $mapeo[$cod_access] = $id_mysql;

        $cInsertados++;
        echo_ok("Línea $linea — Access[$cod_access] → MySQL[$id_mysql] · $codigo · $descripcion — INSERTADO");
    } catch (PDOException $e) {
        echo_err("Línea $linea — Error al insertar '$descripcion': " . $e->getMessage());
        $registro->registrarActividad(
            'importacion',
            'importar_articulos.php',
            'insert_articulo',
            "Error línea $linea - cod_access $cod_access: " . $e->getMessage(),
            'error'
        );
        $cErrores++;
    }
}

fclose($fh);

// ─── Mostrar y guardar mapeo cod_access → id_mysql ────────────────────────────
out('');
out('─── MAPEO cod_access → id_mysql ' . str_repeat('─', 27));
if (!empty($mapeo)) {
    $jsonBonito = json_encode($mapeo, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

    if ($isCli) {
        echo $jsonBonito . PHP_EOL;
    } else {
        echo '<pre style="background:#1a1a2e;color:#00ff88;padding:12px;border-radius:4px;margin-top:8px;">' . htmlspecialchars($jsonBonito) . '</pre>' . "\n";
    }

    $jsonResult = file_put_contents(MAPEO_ARTICULOS_PATH, $jsonBonito);
    if ($jsonResult !== false) {
        echo_ok('mapeo_articulos.json guardado con ' . count($mapeo) . ' entradas → ' . MAPEO_ARTICULOS_PATH);
    } else {
        echo_err('No se pudo escribir mapeo_articulos.json en: ' . MAPEO_ARTICULOS_PATH);
        echo_err('Copia el JSON mostrado arriba y guárdalo manualmente como mapeo_articulos.json.');
    }
} else {
    out('(ningún registro nuevo insertado, no hay mapeo que mostrar)');
}

// ─── Resumen final ────────────────────────────────────────────────────────────
$totalProcesadas = $linea - 1; // sin cabecera

out('');
out(str_repeat('═', 60));
if (!$isCli) echo '<h2>Resumen de importación</h2>';
out('Total filas procesadas         : ' . $totalProcesadas);
echo_ok('Insertados                     : ' . $cInsertados);
out("  ↷  Omitidos (ya existían)     : $cOmitidos");
if ($cSinMapeo > 0) echo_warn("Omitidos (familia sin mapeo)   : $cSinMapeo");
if ($cVacios   > 0) echo_warn("Omitidos (vacíos/inválidos)    : $cVacios");
if ($cErrores  > 0) echo_err("Errores                        : $cErrores");
out(str_repeat('═', 60));

$registro->registrarActividad(
    'importacion',
    'importar_articulos.php',
    'importacion_completa',
    "Importación finalizada — Insertados: $cInsertados | Omitidos: $cOmitidos | Sin mapeo familia: $cSinMapeo | Vacíos: $cVacios | Errores: $cErrores",
    $cErrores > 0 ? 'warning' : 'info'
);

if (!$isCli) {
    echo '</div></body></html>';
}
