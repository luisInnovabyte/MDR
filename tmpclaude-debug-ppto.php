<?php
$pdo = new PDO('mysql:host=217.154.117.83;port=3308;dbname=toldos_db;charset=utf8mb4', 'administrator', '27979699');
$pdo->exec("SET time_zone = 'Europe/Madrid'");

$id_ppto = 16; // P-00007/2026
$descuento_global_pct = 20.00;

// Versión activa
$stmt = $pdo->query("SELECT id_version_presupuesto FROM presupuesto_version WHERE id_presupuesto = $id_ppto AND activo_version = 1 ORDER BY numero_version_presupuesto DESC LIMIT 1");
$ver = $stmt->fetch(PDO::FETCH_ASSOC);
echo "Versión activa: "; print_r($ver);
$id_ver = $ver['id_version_presupuesto'];

// Líneas
$stmt2 = $pdo->query("
    SELECT 
        lp.id_linea_ppto,
        lp.descripcion_linea_ppto,
        lp.cantidad_linea_ppto,
        lp.precio_unitario_linea_ppto,
        lp.descuento_linea_ppto,
        lp.valor_coeficiente_linea_ppto,
        lp.aplicar_coeficiente_linea_ppto,
        v.dias_linea,
        v.base_imponible
    FROM linea_presupuesto lp
    LEFT JOIN v_linea_presupuesto_calculada v ON v.id_linea_ppto = lp.id_linea_ppto
    WHERE lp.id_version_presupuesto = $id_ver
    AND lp.activo_linea_ppto = 1
    ORDER BY lp.orden_linea_ppto
");
$lineas = $stmt2->fetchAll(PDO::FETCH_ASSOC);

$total_sin_desc   = 0;
$total_dto_linea  = 0;
$total_base       = 0;

foreach ($lineas as $l) {
    echo "\n---\n";
    echo "Desc: " . $l['descripcion_linea_ppto'] . "\n";
    echo "Cant: " . $l['cantidad_linea_ppto'] . " | PrecioUnit: " . $l['precio_unitario_linea_ppto'] . " | Dto%linea: " . $l['descuento_linea_ppto'] . "\n";
    echo "Coef: " . $l['valor_coeficiente_linea_ppto'] . " | AplicaCoef: " . $l['aplicar_coeficiente_linea_ppto'] . "\n";
    echo "Dias(vista): " . $l['dias_linea'] . " | BaseImponible(vista): " . $l['base_imponible'] . "\n";

    $cantidad    = floatval($l['cantidad_linea_ppto']);
    $precio      = floatval($l['precio_unitario_linea_ppto']);
    $desc_pct    = floatval($l['descuento_linea_ppto']);
    $coef_val    = $l['valor_coeficiente_linea_ppto'];
    $coef        = floatval($coef_val);
    $aplica_coef = ($coef_val !== null && $coef > 0);
    $dias        = floatval($l['dias_linea'] ?? 1);

    if ($aplica_coef) {
        $sin_desc = $cantidad * $precio * $coef;
    } else {
        $sin_desc = $dias * $cantidad * $precio;
    }
    $dto_linea = $sin_desc * ($desc_pct / 100);

    echo "PHP -> SinDesc: " . number_format($sin_desc, 2) . " | DtoLinea€: " . number_format($dto_linea, 2) . "\n";

    $total_sin_desc  += $sin_desc;
    $total_dto_linea += $dto_linea;
    $total_base      += floatval($l['base_imponible']);
}

// Descuento global aplicado sobre la base (como hace el controlador del hotel)
$total_dto_global = $total_base * ($descuento_global_pct / 100);

echo "\n=== TOTALES ===\n";
echo "Subtotal sin dto (Python calc): " . number_format($total_sin_desc, 2) . " €\n";
echo "Dto línea acumulado:             " . number_format($total_dto_linea, 2) . " €\n";
echo "Base (suma base_imponible vista):" . number_format($total_base, 2) . " €\n";
echo "Dto global $descuento_global_pct% sobre base: " . number_format($total_dto_global, 2) . " €\n";
echo "\n>>> variable \$total_descuentos en el controlador PHP: " . number_format($total_dto_linea, 2) . " €\n";
$ppto = $stmt->fetch(PDO::FETCH_ASSOC);
echo "Presupuesto: "; print_r($ppto);

if ($ppto) {
    $id_ver = $ppto['id_version_presupuesto'];

    $stmt2 = $pdo->query("
        SELECT 
            lp.id_linea_ppto,
            lp.descripcion_linea_ppto,
            lp.cantidad_linea_ppto,
            lp.precio_unitario_linea_ppto,
            lp.descuento_linea_ppto,
            lp.valor_coeficiente_linea_ppto,
            lp.aplicar_coeficiente_linea_ppto,
            lp.mostrar_en_presupuesto,
            v.dias_linea,
            v.base_imponible
        FROM linea_presupuesto lp
        LEFT JOIN v_linea_presupuesto_calculada v ON v.id_linea_ppto = lp.id_linea_ppto
        WHERE lp.id_version_presupuesto = $id_ver
        AND lp.activo_linea_ppto = 1
        ORDER BY lp.orden_linea_ppto
    ");
    $lineas = $stmt2->fetchAll(PDO::FETCH_ASSOC);

    $total_sin_desc   = 0;
    $total_descuentos = 0;

    foreach ($lineas as $l) {
        echo "---\n";
        echo "Desc: " . $l['descripcion_linea_ppto'] . "\n";
        echo "Cantidad: " . $l['cantidad_linea_ppto'] . " | PrecioUnit: " . $l['precio_unitario_linea_ppto'] . " | Descuento%: " . $l['descuento_linea_ppto'] . "\n";
        echo "Coef: " . $l['valor_coeficiente_linea_ppto'] . " | AplicaCoef(BD): " . $l['aplicar_coeficiente_linea_ppto'] . " | MostrarEnPpto: " . $l['mostrar_en_presupuesto'] . "\n";
        echo "Dias: " . $l['dias_linea'] . " | BaseImponible(vista SQL): " . $l['base_imponible'] . "\n";

        $cantidad    = floatval($l['cantidad_linea_ppto']);
        $precio      = floatval($l['precio_unitario_linea_ppto']);
        $desc_pct    = floatval($l['descuento_linea_ppto']);
        $coef_val    = $l['valor_coeficiente_linea_ppto'];
        $coef        = floatval($coef_val);
        $aplica_coef = ($coef_val !== null && $coef > 0);
        $dias        = floatval($l['dias_linea'] ?? 1);

        if ($aplica_coef) {
            $sin_desc = $cantidad * $precio * $coef;
        } else {
            $sin_desc = $dias * $cantidad * $precio;
        }
        $dto_linea = $sin_desc * ($desc_pct / 100);

        echo "Calc PHP -> SinDesc: " . number_format($sin_desc, 2) . " | DtoLinea€: " . number_format($dto_linea, 2) . "\n";

        $total_sin_desc   += $sin_desc;
        $total_descuentos += $dto_linea;
    }

    echo "\n=== TOTALES CALCULADOS POR PHP ===\n";
    echo "Total sin descuento : " . number_format($total_sin_desc, 2) . " €\n";
    echo "Total descuentos    : " . number_format($total_descuentos, 2) . " €\n";
    echo "Diferencia (base)   : " . number_format($total_sin_desc - $total_descuentos, 2) . " €\n";
}
