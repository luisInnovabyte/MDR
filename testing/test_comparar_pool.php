<?php
/**
 * Test manual para SalidaAlmacen::comparar_pool() y confirmar_pool()
 *
 * Uso:
 *   php /var/www/html/testing/test_comparar_pool.php
 *
 * NO modificar el estado de la BD (comparar_pool no escribe nada).
 * confirmar_pool se ejecuta solo si pasas ?confirmar=1 — requiere rollback manual si lo usas en dev.
 */

// Los modelos usan require_once '../config/...' (relativo desde controller/)
// Movemos CWD a controller/ para que los paths internos resuelvan igual que en producción
chdir(dirname(__DIR__) . '/controller');
require_once '../config/conexion.php';
require_once '../config/funciones.php';
require_once '../models/SalidaAlmacen.php';

// ── Configuración del test ────────────────────────────────────────────────
// Salida real en BD (estado 'en_proceso') para el presupuesto P-00015/2026
$ID_SALIDA = 20;

// Escenarios de pool a probar:
// - EQUIP-MEGA-001-001  → artículo correcto (está en el presupuesto)
// - MIC-INAL-001-001    → artículo NO en el presupuesto (no_relacionado)
// - EQUIP-MEGA-001-001  → duplicado (debe deduplicarse)
// - CODIGO-INEXISTENTE  → elemento no existe en BD
$POOL = [
    'EQUIP-MEGA-001-001',
    'MIC-INAL-001-001',
    'EQUIP-MEGA-001-001',  // duplicado
    'CODIGO-INEXISTENTE',
];
// ─────────────────────────────────────────────────────────────────────────

$model = new SalidaAlmacen();

echo "════════════════════════════════════════\n";
echo " TEST comparar_pool()\n";
echo " id_salida = $ID_SALIDA\n";
echo " pool      = " . implode(', ', $POOL) . "\n";
echo "════════════════════════════════════════\n\n";

$resultado = $model->comparar_pool($ID_SALIDA, $POOL);

if (isset($resultado['error'])) {
    echo "❌ ERROR: " . $resultado['error'] . "\n";
    exit(1);
}

// ── Mostrar resultado por cubos ───────────────────────────────────────────
foreach (['correctos', 'sobran', 'no_relacionados', 'faltan'] as $cubo) {
    $items = $resultado[$cubo];
    echo strtoupper($cubo) . " (" . count($items) . ")\n";
    echo str_repeat('─', 40) . "\n";
    if (empty($items)) {
        echo "  (vacío)\n";
    } else {
        foreach ($items as $item) {
            if ($cubo === 'faltan') {
                echo "  • {$item['nombre_articulo']} | id_linea_ppto={$item['id_linea_ppto']}"
                    . " | familia=" . ($item['nombre_familia'] ?? 'null') . "\n";
            } else {
                echo "  • {$item['codigo_elemento']} | {$item['nombre_articulo']}"
                    . " | familia=" . ($item['nombre_familia'] ?? 'null')
                    . " | estado=" . ($item['codigo_estado_elemento'] ?? 'null')
                    . ($item['estado_invalido'] ? " ⚠ INVÁLIDO" : "")
                    . (isset($item['no_encontrado']) ? " ❓ NO ENCONTRADO" : "")
                    . "\n";
            }
        }
    }
    echo "\n";
}

// ── Validaciones automáticas ──────────────────────────────────────────────
echo "── VALIDACIONES ────────────────────────\n";

$ok = true;

// El duplicado debe haberse eliminado → EQUIP-MEGA-001-001 solo debe aparecer 1 vez en total
$todas_apariciones = array_merge(
    array_column($resultado['correctos'],       'codigo_elemento'),
    array_column($resultado['sobran'],          'codigo_elemento'),
    array_column($resultado['no_relacionados'], 'codigo_elemento')
);
$conteo = array_count_values($todas_apariciones);
if (($conteo['EQUIP-MEGA-001-001'] ?? 0) === 1) {
    echo "  ✅ Duplicado deduplicado correctamente\n";
} else {
    echo "  ❌ El duplicado NO se deduplicó\n";
    $ok = false;
}

// EQUIP-MEGA-001-001 debe estar en correctos (está en el presupuesto y hay unidades libres)
$codigos_correctos = array_column($resultado['correctos'], 'codigo_elemento');
if (in_array('EQUIP-MEGA-001-001', $codigos_correctos)) {
    echo "  ✅ EQUIP-MEGA-001-001 clasificado como correcto\n";
} else {
    echo "  ❌ EQUIP-MEGA-001-001 no está en correctos\n";
    $ok = false;
}

// MIC-INAL-001-001 debe estar en no_relacionados
$codigos_norel = array_column($resultado['no_relacionados'], 'codigo_elemento');
if (in_array('MIC-INAL-001-001', $codigos_norel)) {
    echo "  ✅ MIC-INAL-001-001 clasificado como no_relacionado\n";
} else {
    echo "  ❌ MIC-INAL-001-001 no está en no_relacionados\n";
    $ok = false;
}

// CODIGO-INEXISTENTE debe estar en no_relacionados con no_encontrado=true
$encontrado_norel = false;
foreach ($resultado['no_relacionados'] as $item) {
    if ($item['codigo_elemento'] === 'CODIGO-INEXISTENTE' && !empty($item['no_encontrado'])) {
        $encontrado_norel = true;
        break;
    }
}
if ($encontrado_norel) {
    echo "  ✅ CODIGO-INEXISTENTE marcado como no_encontrado en no_relacionados\n";
} else {
    echo "  ❌ CODIGO-INEXISTENTE no está en no_relacionados con no_encontrado=true\n";
    $ok = false;
}

// La PANTALLA PLASMA 65" no se escaneó → debe estar en faltan
if (!empty($resultado['faltan'])) {
    echo "  ✅ Hay faltantes (PANTALLA PLASMA no escaneada)\n";
} else {
    echo "  ❌ No hay faltantes — se esperaba al menos 1\n";
    $ok = false;
}

// Todos los items de correctos deben tener id_familia (si el artículo tiene familia en BD)
foreach ($resultado['correctos'] as $item) {
    if (array_key_exists('id_familia', $item) && array_key_exists('nombre_familia', $item)) {
        continue;
    }
    echo "  ❌ Item correcto sin campos id_familia/nombre_familia: {$item['codigo_elemento']}\n";
    $ok = false;
}

echo "\n";
echo $ok
    ? "════════════════════════════════════════\n ✅ TODOS LOS TESTS PASARON\n════════════════════════════════════════\n"
    : "════════════════════════════════════════\n ❌ ALGÚN TEST FALLÓ\n════════════════════════════════════════\n";
