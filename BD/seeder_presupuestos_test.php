<?php

/**
 * ============================================================
 *  SEEDER DE PRESUPUESTOS DE PRUEBA - MDR ERP Manager
 * ============================================================
 *  Crea 4 presupuestos ficticios con ~30 artículos cada uno.
 *  Marca todos los registros con '__SEEDER_TEST__' en
 *  observaciones_internas_presupuesto para poder limpiarlos.
 *
 *  USO:
 *    php BD/seeder_presupuestos_test.php         → crea los datos
 *    php BD/seeder_presupuestos_test.php --clean  → solo limpia
 *
 *  IDEMPOTENTE: limpia los datos de prueba anteriores antes de
 *  volver a crearlos, así puede ejecutarse cuantas veces se quiera.
 * ============================================================
 */

define('SEEDER_MARK', '__SEEDER_TEST__');

// ──────────────────────────────────────────────────────────────
//  CONEXIÓN
// ──────────────────────────────────────────────────────────────
$configFile = __DIR__ . '/../config/conexion.json';
if (!file_exists($configFile)) {
    die("[ERROR] No se encontró config/conexion.json\n");
}
$config = json_decode(file_get_contents($configFile), true);

try {
    $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']};charset={$config['charset']}";
    $pdo = new PDO($dsn, $config['user'], $config['password'], [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
    $pdo->exec("SET time_zone = 'Europe/Madrid'");
    echo "[OK] Conectado a {$config['database']}@{$config['host']}:{$config['port']}\n";
} catch (PDOException $e) {
    die("[ERROR] Conexión: " . $e->getMessage() . "\n");
}

// ──────────────────────────────────────────────────────────────
//  PASO 1: LIMPIEZA DE DATOS DE PRUEBA ANTERIORES
// ──────────────────────────────────────────────────────────────
echo "\n[LIMPIEZA] Buscando presupuestos de prueba anteriores...\n";

$stmtIds = $pdo->query("
    SELECT id_presupuesto
    FROM presupuesto
    WHERE observaciones_internas_presupuesto = '" . SEEDER_MARK . "'
");
$idsTest  = $stmtIds->fetchAll(PDO::FETCH_COLUMN);

if (!empty($idsTest)) {
    $placeholders = implode(',', $idsTest);

    // 1. Líneas de presupuesto (a través de las versiones)
    $pdo->exec("
        DELETE lp FROM linea_presupuesto lp
        INNER JOIN presupuesto_version pv ON lp.id_version_presupuesto = pv.id_version_presupuesto
        WHERE pv.id_presupuesto IN ($placeholders)
    ");
    echo "    · Líneas eliminadas\n";

    // 2. Versiones
    $pdo->exec("DELETE FROM presupuesto_version WHERE id_presupuesto IN ($placeholders)");
    echo "    · Versiones eliminadas\n";

    // 3. Presupuesto principal
    $pdo->exec("DELETE FROM presupuesto WHERE id_presupuesto IN ($placeholders)");
    echo "    · " . count($idsTest) . " presupuesto(s) eliminado(s): [" . $placeholders . "]\n";
} else {
    echo "    · No se encontraron datos de prueba anteriores.\n";
}

// Si solo se pidió limpiar, terminar aquí
if (in_array('--clean', $argv ?? [])) {
    echo "\n[OK] Limpieza completada. Fin del script.\n";
    exit(0);
}

// ──────────────────────────────────────────────────────────────
//  PASO 2: CARGAR DATOS DE REFERENCIA DESDE LA BD
// ──────────────────────────────────────────────────────────────
echo "\n[CARGA] Leyendo artículos activos...\n";

// Artículos activos con su impuesto
$articulos = $pdo->query("
    SELECT a.id_articulo,
           a.codigo_articulo,
           a.nombre_articulo,
           a.precio_alquiler_articulo,
           a.id_impuesto,
           COALESCE(i.tasa_impuesto, 21.00) AS tasa_iva
    FROM articulo a
    LEFT JOIN impuesto i ON a.id_impuesto = i.id_impuesto
    WHERE a.activo_articulo = 1
      AND a.precio_alquiler_articulo > 0
    ORDER BY a.id_articulo
")->fetchAll();

if (count($articulos) < 30) {
    die("[ERROR] Hay menos de 30 artículos activos con precio. Imposible crear las líneas.\n");
}
echo "    · " . count($articulos) . " artículos disponibles.\n";

// Estados  [codigo => id]
$estados = $pdo->query("SELECT codigo_estado_ppto, id_estado_ppto FROM estado_presupuesto WHERE activo_estado_ppto = 1")->fetchAll(PDO::FETCH_KEY_PAIR);
// $estados['BORRADOR'] = 1, etc.

// Empresa ficticia principal
$empresa = $pdo->query("SELECT id_empresa FROM empresa WHERE empresa_ficticia_principal = TRUE AND activo_empresa = TRUE LIMIT 1")->fetch();
$idEmpresa = $empresa['id_empresa'];

echo "    · Empresa principal: id=$idEmpresa\n";

// ──────────────────────────────────────────────────────────────
//  DEFINICIÓN DE LOS 4 PRESUPUESTOS DE PRUEBA
// ──────────────────────────────────────────────────────────────
$presupuestosTest = [
    [
        'id_cliente'               => 1,
        'id_contacto_cliente'      => null,
        'estado_codigo'            => 'BORRADOR',
        'id_forma_pago'            => 5,    // Transferencia 30 días
        'fecha_presupuesto'        => '2026-03-13',
        'fecha_validez'            => '2026-04-13',
        'fecha_inicio_evento'      => '2026-05-15',
        'fecha_fin_evento'         => '2026-05-17',
        'nombre_evento'            => '[TEST] Congreso de Tecnología Audiovisual 2026',
        'direccion_evento'         => 'Av. de la Constitución, 20',
        'poblacion_evento'         => 'Alicante',
        'provincia_evento'         => 'Alicante',
        'cp_evento'                => '03001',
        'descuento'                => 0.00,
        'obs_cabecera'             => 'Presupuesto para congreso de dos días con montaje el día anterior.',
        'obs_pie'                  => 'Precios sujetos a IVA vigente. Válido 30 días.',
        'estado_general'           => 'borrador',
    ],
    [
        'id_cliente'               => 4,
        'id_contacto_cliente'      => null,
        'estado_codigo'            => 'PROC',
        'id_forma_pago'            => 8,    // 40% anticipo + 60% al finalizar
        'fecha_presupuesto'        => '2026-03-10',
        'fecha_validez'            => '2026-04-10',
        'fecha_inicio_evento'      => '2026-06-10',
        'fecha_fin_evento'         => '2026-06-12',
        'nombre_evento'            => '[TEST] Gala de Premios Empresariales Costa Blanca',
        'direccion_evento'         => 'Partida Fonts del Algar, s/n',
        'poblacion_evento'         => 'Benidorm',
        'provincia_evento'         => 'Alicante',
        'cp_evento'                => '03501',
        'descuento'                => 5.00,
        'obs_cabecera'             => 'Evento de gala para 200 personas. Incluye equipo de iluminación profesional y sonido.',
        'obs_pie'                  => 'Transporte incluido. Técnicos propios. IVA no incluido.',
        'estado_general'           => 'borrador',
    ],
    [
        'id_cliente'               => 7,
        'id_contacto_cliente'      => null,
        'estado_codigo'            => 'APROB',
        'id_forma_pago'            => 9,    // 50% + 50%
        'fecha_presupuesto'        => '2026-03-01',
        'fecha_validez'            => '2026-04-01',
        'fecha_inicio_evento'      => '2026-07-05',
        'fecha_fin_evento'         => '2026-07-07',
        'nombre_evento'            => '[TEST] Convención Anual Comercial Mediterráneo 2026',
        'direccion_evento'         => 'C/ San Fernando, 40',
        'poblacion_evento'         => 'Alicante',
        'provincia_evento'         => 'Alicante',
        'cp_evento'                => '03002',
        'descuento'                => 10.00,
        'obs_cabecera'             => 'Convención para 350 asistentes. Streaming en directo. Plató virtual.',
        'obs_pie'                  => 'Montaje dos días antes. Personal técnico especializado incluido.',
        'estado_general'           => 'aprobado',
    ],
    [
        'id_cliente'               => 9,
        'id_contacto_cliente'      => null,
        'estado_codigo'            => 'ESPE-RESP',
        'id_forma_pago'            => 1,    // Contado transferencia
        'fecha_presupuesto'        => '2026-03-12',
        'fecha_validez'            => '2026-04-12',
        'fecha_inicio_evento'      => '2026-08-20',
        'fecha_fin_evento'         => '2026-08-22',
        'nombre_evento'            => '[TEST] Festival de Verano Costa Blanca 2026',
        'direccion_evento'         => 'Playa de San Juan, s/n',
        'poblacion_evento'         => 'San Juan de Alicante',
        'provincia_evento'         => 'Alicante',
        'cp_evento'                => '03550',
        'descuento'                => 0.00,
        'obs_cabecera'             => 'Festival al aire libre con escenario principal y escenario secundario.',
        'obs_pie'                  => 'Seguro incluido. Generador eléctrico por cuenta del cliente.',
        'estado_general'           => 'borrador',
    ],
];

// ──────────────────────────────────────────────────────────────
//  PASO 3: INSERTAR PRESUPUESTOS Y LÍNEAS
// ──────────────────────────────────────────────────────────────
echo "\n[INSERCIÓN] Creando presupuestos...\n";

$sqlInsertPpto = "
    INSERT INTO presupuesto (
        id_empresa,
        id_cliente,
        id_contacto_cliente,
        id_estado_ppto,
        id_forma_pago,
        estado_general_presupuesto,
        fecha_presupuesto,
        fecha_validez_presupuesto,
        fecha_inicio_evento_presupuesto,
        fecha_fin_evento_presupuesto,
        nombre_evento_presupuesto,
        direccion_evento_presupuesto,
        poblacion_evento_presupuesto,
        cp_evento_presupuesto,
        provincia_evento_presupuesto,
        descuento_presupuesto,
        aplicar_coeficientes_presupuesto,
        observaciones_cabecera_presupuesto,
        observaciones_pie_presupuesto,
        observaciones_internas_presupuesto,
        mostrar_obs_familias_presupuesto,
        mostrar_obs_articulos_presupuesto,
        activo_presupuesto
    ) VALUES (
        ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
        ?, ?, ?, ?, ?, ?, 1, ?, ?, ?, 1, 1, 1
    )
";

$sqlInsertLinea = "
    INSERT INTO linea_presupuesto (
        id_version_presupuesto,
        id_articulo,
        tipo_linea_ppto,
        numero_linea_ppto,
        orden_linea_ppto,
        codigo_linea_ppto,
        descripcion_linea_ppto,
        cantidad_linea_ppto,
        precio_unitario_linea_ppto,
        descuento_linea_ppto,
        id_impuesto,
        porcentaje_iva_linea_ppto,
        fecha_montaje_linea_ppto,
        fecha_desmontaje_linea_ppto,
        fecha_inicio_linea_ppto,
        fecha_fin_linea_ppto,
        mostrar_en_presupuesto,
        mostrar_obs_articulo_linea_ppto,
        es_opcional,
        activo_linea_ppto
    ) VALUES (
        ?, ?, 'articulo', ?, ?, ?, ?, ?, ?, ?,
        ?, ?, ?, ?, ?, ?, 1, 1, 0, 1
    )
";

$stmtPpto  = $pdo->prepare($sqlInsertPpto);
$stmtLinea = $pdo->prepare($sqlInsertLinea);

// Cantidades posibles para las líneas (variadas pero realistas)
$cantidades = [1, 1, 1, 2, 2, 2, 3, 4, 5, 6, 8, 10];

foreach ($presupuestosTest as $idx => $ppto) {
    $numPpto = $idx + 1;
    echo "\n  → Presupuesto $numPpto: {$ppto['nombre_evento']}\n";

    // Mezclar artículos para esta iteración (orden pseudo-aleatorio reproducible)
    $articulosMezclados = $articulos;
    // Usamos un seed basado en el índice para que sea reproducible
    $seed = 42 + $idx * 17;
    usort($articulosMezclados, function ($a, $b) use ($seed) {
        // Hash determinista por artículo + seed
        $ha = crc32($a['id_articulo'] . $seed);
        $hb = crc32($b['id_articulo'] . $seed);
        return $ha <=> $hb;
    });
    // Tomar los primeros 30
    $articulosLineas = array_slice($articulosMezclados, 0, 30);

    $idEstado = null;
    foreach ($estados as $codigo => $id) {
        if ($codigo === $ppto['estado_codigo']) {
            $idEstado = $id;
            break;
        }
    }
    if (!$idEstado) {
        echo "    [WARN] Estado '{$ppto['estado_codigo']}' no encontrado, usando BORRADOR.\n";
        $idEstado = array_values($estados)[0];
    }

    // INSERT presupuesto (el trigger genera numero_presupuesto automáticamente)
    $stmtPpto->execute([
        $idEmpresa,
        $ppto['id_cliente'],
        $ppto['id_contacto_cliente'],
        $idEstado,
        $ppto['id_forma_pago'],
        $ppto['estado_general'],
        $ppto['fecha_presupuesto'],
        $ppto['fecha_validez'],
        $ppto['fecha_inicio_evento'],
        $ppto['fecha_fin_evento'],
        $ppto['nombre_evento'],
        $ppto['direccion_evento'],
        $ppto['poblacion_evento'],
        $ppto['cp_evento'],
        $ppto['provincia_evento'],
        $ppto['descuento'],
        $ppto['obs_cabecera'],
        $ppto['obs_pie'],
        SEEDER_MARK,    // ← MARCA DE TEST (para poder limpiar)
    ]);
    $idPresupuesto = $pdo->lastInsertId();

    // Obtener el número generado por el trigger
    $numGenerado = $pdo->query("SELECT numero_presupuesto FROM presupuesto WHERE id_presupuesto = $idPresupuesto")->fetchColumn();
    echo "    · Presupuesto creado: ID=$idPresupuesto, Número=$numGenerado\n";

    // Obtener la versión creada automáticamente por el trigger after_insert
    $idVersion = $pdo->query("
        SELECT id_version_presupuesto
        FROM presupuesto_version
        WHERE id_presupuesto = $idPresupuesto
        ORDER BY numero_version_presupuesto ASC
        LIMIT 1
    ")->fetchColumn();

    if (!$idVersion) {
        echo "    [ERROR] No se encontró la versión del presupuesto $idPresupuesto. Saltando.\n";
        continue;
    }
    echo "    · Versión 1 creada: id_version=$idVersion\n";

    // Calcular fecha de montaje/desmontaje (día antes/después del evento)
    $fechaMontaje    = date('Y-m-d', strtotime($ppto['fecha_inicio_evento'] . ' -1 day'));
    $fechaDesmontaje = date('Y-m-d', strtotime($ppto['fecha_fin_evento']  . ' +1 day'));

    // Insertar 30 líneas
    $numLinea = 1;
    $cantIdx  = 0;
    foreach ($articulosLineas as $art) {
        $cantidad = $cantidades[$cantIdx % count($cantidades)];
        $cantIdx++;

        $idImpuesto = $art['id_impuesto'] ?: 1; // IVA normal por defecto
        $pctIva     = (float) $art['tasa_iva'];
        $precio     = (float) $art['precio_alquiler_articulo'];

        $stmtLinea->execute([
            $idVersion,
            $art['id_articulo'],
            $numLinea,
            $numLinea * 10,                  // orden_linea_ppto
            $art['codigo_articulo'],
            $art['nombre_articulo'],
            $cantidad,
            $precio,
            $ppto['descuento'],              // descuento heredado del presupuesto
            $idImpuesto,
            $pctIva,
            $fechaMontaje,
            $fechaDesmontaje,
            $ppto['fecha_inicio_evento'],
            $ppto['fecha_fin_evento'],
        ]);
        $numLinea++;
    }

    echo "    · " . ($numLinea - 1) . " líneas insertadas.\n";

    // Avanzar estado de la versión si es necesario (el trigger exige workflow borrador → enviado → aprobado)
    if ($ppto['estado_general'] === 'enviado' || $ppto['estado_general'] === 'aprobado') {
        $pdo->exec("
            UPDATE presupuesto_version
            SET estado_version_presupuesto = 'enviado',
                fecha_envio_version = NOW()
            WHERE id_version_presupuesto = $idVersion
        ");
        echo "    · Versión marcada como enviada.\n";
    }
    if ($ppto['estado_general'] === 'aprobado') {
        $pdo->exec("
            UPDATE presupuesto_version
            SET estado_version_presupuesto = 'aprobado',
                fecha_aprobacion_version = NOW()
            WHERE id_version_presupuesto = $idVersion
        ");
        // Actualizar también el estado general del presupuesto
        $pdo->exec("
            UPDATE presupuesto
            SET estado_general_presupuesto = 'aprobado'
            WHERE id_presupuesto = $idPresupuesto
        ");
        echo "    · Versión marcada como aprobada.\n";
    }
}

// ──────────────────────────────────────────────────────────────
//  RESUMEN FINAL
// ──────────────────────────────────────────────────────────────
echo "\n";
echo "═══════════════════════════════════════════════════════\n";
echo "  SEEDER COMPLETADO CON ÉXITO\n";
echo "═══════════════════════════════════════════════════════\n";

$resumen = $pdo->query("
    SELECT p.id_presupuesto,
           p.numero_presupuesto,
           p.nombre_evento_presupuesto,
           c.nombre_cliente,
           e.nombre_estado_ppto,
           COUNT(lp.id_linea_ppto) AS num_lineas
    FROM presupuesto p
    JOIN cliente c ON p.id_cliente = c.id_cliente
    JOIN estado_presupuesto e ON p.id_estado_ppto = e.id_estado_ppto
    JOIN presupuesto_version pv ON pv.id_presupuesto = p.id_presupuesto
    LEFT JOIN linea_presupuesto lp ON lp.id_version_presupuesto = pv.id_version_presupuesto
    WHERE p.observaciones_internas_presupuesto = '" . SEEDER_MARK . "'
    GROUP BY p.id_presupuesto
    ORDER BY p.id_presupuesto
")->fetchAll();

printf("  %-6s %-20s %-42s %-25s %-18s %s\n",
    'ID', 'Número', 'Evento', 'Cliente', 'Estado', 'Líneas');
echo "  " . str_repeat('-', 125) . "\n";
foreach ($resumen as $r) {
    $evento  = substr($r['nombre_evento_presupuesto'], 0, 42);
    $cliente = substr($r['nombre_cliente'], 0, 25);
    printf("  %-6s %-20s %-42s %-25s %-18s %s\n",
        $r['id_presupuesto'],
        $r['numero_presupuesto'],
        $evento,
        $cliente,
        $r['nombre_estado_ppto'],
        $r['num_lineas']
    );
}
echo "\n";
echo "  Para limpiar estos datos: php BD/seeder_presupuestos_test.php --clean\n";
echo "═══════════════════════════════════════════════════════\n\n";
