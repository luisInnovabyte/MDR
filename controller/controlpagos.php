<?php

require_once "../config/conexion.php";
require_once "../config/funciones.php";
require_once "../models/ControlPagos.php";

$registro     = new RegistroActividad();
$controlPagos = new ControlPagos();

switch ($_GET["op"] ?? '') {

    // ------------------------------------------------------------------
    // LISTAR: DataTable principal con todos los presupuestos aprobados
    // ------------------------------------------------------------------
    case "listar":
        $datos = $controlPagos->get_control_pagos();
        $data  = [];

        foreach ($datos as $row) {
            // Badge % pagado
            $pct   = (float)$row['porcentaje_pagado'];
            $color = 'success';
            if ($pct < 20)       $color = 'danger';
            elseif ($pct < 80)   $color = 'warning';

            // Barra de progreso del pendiente
            $pct_pendiente = max(0, min(100, 100 - $pct));
            $color_barra   = $pct_pendiente > 80 ? 'danger' : ($pct_pendiente > 20 ? 'warning' : 'success');

            // Pdte. Facturar = Aprobado − Facturado (de la vista)
            $saldo_pdte      = (float)$row['saldo_pendiente'];
            $pct_pdte_con    = (float)$row['total_presupuesto'] > 0 ? max(0, min(100, $saldo_pdte / (float)$row['total_presupuesto'] * 100)) : 0;
            $color_barra_con = $pct_pdte_con > 80 ? 'danger' : ($pct_pdte_con > 20 ? 'warning' : 'success');

            // Fechas del evento
            $f_inicio = $row['fecha_inicio_evento_presupuesto'] ? date('d/m/Y', strtotime($row['fecha_inicio_evento_presupuesto'])) : '—';
            $f_fin    = $row['fecha_fin_evento_presupuesto']     ? date('d/m/Y', strtotime($row['fecha_fin_evento_presupuesto']))     : '';
            $fechas_evento = $f_inicio . ($f_fin ? ' → ' . $f_fin : '');

            // Nombre del evento (tooltip)
            $nombre_evento = htmlspecialchars($row['nombre_evento_presupuesto'] ?? '', ENT_QUOTES, 'UTF-8');

            // Badges de documentos emitidos
            $badges_docs = '';
            if (!empty($row['tipos_documentos'])) {
                $etiquetas = [
                    'presupuesto'            => ['Presupuesto',   'secondary'],
                    'parte_trabajo'          => ['Parte',         'info'],
                    'factura_proforma'       => ['Proforma',      'primary'],
                    'factura_anticipo'       => ['Anticipo',      'warning'],
                    'factura_final'          => ['Final',         'success'],
                    'factura_rectificativa'  => ['Abono',         'danger'],
                    'factura_agrupada'       => ['Agrupada',      'dark'],
                    'factura_agrupada_abono' => ['Abono Agr.',    'danger'],
                ];
                foreach (explode(',', $row['tipos_documentos']) as $tipo) {
                    $tipo = trim($tipo);
                    if (isset($etiquetas[$tipo])) {
                        [$label, $col] = $etiquetas[$tipo];
                        $badges_docs .= '<span class="badge bg-' . $col . ' me-1">' . $label . '</span>';
                    }
                }
            }

            // Última factura generada
            $ultima_factura = '—';
            if (!empty($row['fecha_ultima_factura'])) {
                $ultima_factura = date('d/m/Y', strtotime($row['fecha_ultima_factura']));
            }

            // Botones opciones
            $opciones = '<div class="d-flex gap-1 justify-content-center">
                <button class="btn btn-sm btn-outline-info" 
                    onclick="verDetallePagos(' . $row['id_presupuesto'] . ', \'' . htmlspecialchars($row['numero_presupuesto'], ENT_QUOTES, 'UTF-8') . '\')"
                    title="Ver desglose de pagos">
                    <i class="fa fa-list-ul"></i>
                </button>
                <a href="../Presupuesto/formularioPresupuesto.php?id=' . $row['id_presupuesto'] . '&tab=pagos"
                    class="btn btn-sm btn-outline-primary" title="Ir al presupuesto">
                    <i class="fa fa-external-link-alt"></i>
                </a>
            </div>';

            $data[] = [
                'id_presupuesto'               => $row['id_presupuesto'],
                'numero_presupuesto'           => '<a href="../Presupuesto/formularioPresupuesto.php?id=' . $row['id_presupuesto'] . '&tab=pagos" class="fw-semibold text-decoration-none">' . htmlspecialchars($row['numero_presupuesto'], ENT_QUOTES, 'UTF-8') . '</a>',
                'nombre_completo_cliente'      => htmlspecialchars($row['nombre_completo_cliente'], ENT_QUOTES, 'UTF-8'),
                'evento'                       => '<span title="' . $nombre_evento . '">' . $fechas_evento . ($nombre_evento ? '<br><small class="text-muted">' . $nombre_evento . '</small>' : '') . '</span>',
                'total_presupuesto'            => number_format((float)$row['total_presupuesto'], 2, ',', '.') . ' €',
                'total_pagado'                 => '<span class="fw-semibold">' . number_format((float)$row['total_pagado'], 2, ',', '.') . ' €</span>',
                'total_conciliado'             => '<span class="fw-semibold text-info">' . number_format((float)$row['total_conciliado'], 2, ',', '.') . ' €</span><br><span class="badge bg-' . $color . '">' . $pct . '%</span>',
                'saldo_pendiente'              => number_format($saldo_pdte, 2, ',', '.') . ' €<br>
                    <div class="progress mt-1" style="height:6px;" title="' . round($pct_pdte_con) . '% pendiente">
                        <div class="progress-bar bg-' . $color_barra_con . '" style="width:' . $pct_pdte_con . '%"></div>
                    </div>',
                'tipos_documentos'             => $badges_docs ?: '<span class="text-muted">—</span>',
                'ultima_factura'               => $ultima_factura,
                'opciones'                     => $opciones,
                // Valores numéricos planos para exportación/ordenación
                '_total_presupuesto_num'       => (float)$row['total_presupuesto'],
                '_total_pagado_num'            => (float)$row['total_pagado'],
                '_total_conciliado_num'        => (float)$row['total_conciliado'],
                '_saldo_pendiente_num'         => $saldo_pdte,
                '_porcentaje_pagado'           => $pct,
            ];
        }

        $results = [
            'draw'            => intval($_POST['draw'] ?? 1),
            'recordsTotal'    => count($data),
            'recordsFiltered' => count($data),
            'data'            => $data,
        ];

        header('Content-Type: application/json');
        echo json_encode($results, JSON_UNESCAPED_UNICODE);
        break;

    // ------------------------------------------------------------------
    // LISTAR_FILTRADO: mismo listado pero con filtros POST
    // ------------------------------------------------------------------
    case "listar_filtrado":
        $filtros = [
            'solo_pdte_facturar' => !empty($_POST['solo_pdte_facturar']) && $_POST['solo_pdte_facturar'] === '1',
            'solo_pdte_cobrar'   => !empty($_POST['solo_pdte_cobrar'])   && $_POST['solo_pdte_cobrar']   === '1',
            'fecha_evento_desde' => !empty($_POST['fecha_evento_desde']) ? $_POST['fecha_evento_desde'] : null,
            'fecha_evento_hasta' => !empty($_POST['fecha_evento_hasta']) ? $_POST['fecha_evento_hasta'] : null,
        ];

        $datos = $controlPagos->get_control_pagos($filtros);
        $data  = [];

        foreach ($datos as $row) {
            $pct   = (float)$row['porcentaje_pagado'];
            $color = 'success';
            if ($pct < 20)       $color = 'danger';
            elseif ($pct < 80)   $color = 'warning';

            $pct_pendiente = max(0, min(100, 100 - $pct));
            $color_barra   = $pct_pendiente > 80 ? 'danger' : ($pct_pendiente > 20 ? 'warning' : 'success');

            // Pdte. Facturar = Aprobado − Facturado (de la vista)
            $saldo_pdte      = (float)$row['saldo_pendiente'];
            $pct_pdte_con    = (float)$row['total_presupuesto'] > 0 ? max(0, min(100, $saldo_pdte / (float)$row['total_presupuesto'] * 100)) : 0;
            $color_barra_con = $pct_pdte_con > 80 ? 'danger' : ($pct_pdte_con > 20 ? 'warning' : 'success');

            $f_inicio      = $row['fecha_inicio_evento_presupuesto'] ? date('d/m/Y', strtotime($row['fecha_inicio_evento_presupuesto'])) : '—';
            $f_fin         = $row['fecha_fin_evento_presupuesto']     ? date('d/m/Y', strtotime($row['fecha_fin_evento_presupuesto']))     : '';
            $fechas_evento = $f_inicio . ($f_fin ? ' → ' . $f_fin : '');
            $nombre_evento = htmlspecialchars($row['nombre_evento_presupuesto'] ?? '', ENT_QUOTES, 'UTF-8');

            $badges_docs = '';
            if (!empty($row['tipos_documentos'])) {
                $etiquetas = [
                    'presupuesto'            => ['Presupuesto',   'secondary'],
                    'parte_trabajo'          => ['Parte',         'info'],
                    'factura_proforma'       => ['Proforma',      'primary'],
                    'factura_anticipo'       => ['Anticipo',      'warning'],
                    'factura_final'          => ['Final',         'success'],
                    'factura_rectificativa'  => ['Abono',         'danger'],
                    'factura_agrupada'       => ['Agrupada',      'dark'],
                    'factura_agrupada_abono' => ['Abono Agr.',    'danger'],
                ];
                foreach (explode(',', $row['tipos_documentos']) as $tipo) {
                    $tipo = trim($tipo);
                    if (isset($etiquetas[$tipo])) {
                        [$label, $col] = $etiquetas[$tipo];
                        $badges_docs .= '<span class="badge bg-' . $col . ' me-1">' . $label . '</span>';
                    }
                }
            }

            $ultima_factura = '—';
            if (!empty($row['fecha_ultima_factura'])) {
                $ultima_factura = date('d/m/Y', strtotime($row['fecha_ultima_factura']));
            }

            $opciones = '<div class="d-flex gap-1 justify-content-center">
                <button class="btn btn-sm btn-outline-info"
                    onclick="verDetallePagos(' . $row['id_presupuesto'] . ', \'' . htmlspecialchars($row['numero_presupuesto'], ENT_QUOTES, 'UTF-8') . '\')"
                    title="Ver desglose de pagos">
                    <i class="fa fa-list-ul"></i>
                </button>
                <a href="../Presupuesto/formularioPresupuesto.php?id=' . $row['id_presupuesto'] . '&tab=pagos"
                    class="btn btn-sm btn-outline-primary" title="Ir al presupuesto">
                    <i class="fa fa-external-link-alt"></i>
                </a>
            </div>';

            $data[] = [
                'id_presupuesto'               => $row['id_presupuesto'],
                'numero_presupuesto'           => '<a href="../Presupuesto/formularioPresupuesto.php?id=' . $row['id_presupuesto'] . '&tab=pagos" class="fw-semibold text-decoration-none">' . htmlspecialchars($row['numero_presupuesto'], ENT_QUOTES, 'UTF-8') . '</a>',
                'nombre_completo_cliente'      => htmlspecialchars($row['nombre_completo_cliente'], ENT_QUOTES, 'UTF-8'),
                'evento'                       => '<span title="' . $nombre_evento . '">' . $fechas_evento . ($nombre_evento ? '<br><small class="text-muted">' . $nombre_evento . '</small>' : '') . '</span>',
                'total_presupuesto'            => number_format((float)$row['total_presupuesto'], 2, ',', '.') . ' €',
                'total_pagado'                 => '<span class="fw-semibold">' . number_format((float)$row['total_pagado'], 2, ',', '.') . ' €</span>',
                'total_conciliado'             => '<span class="fw-semibold text-info">' . number_format((float)$row['total_conciliado'], 2, ',', '.') . ' €</span><br><span class="badge bg-' . $color . '">' . $pct . '%</span>',
                'saldo_pendiente'              => number_format($saldo_pdte, 2, ',', '.') . ' €<br>
                    <div class="progress mt-1" style="height:6px;" title="' . round($pct_pdte_con) . '% pendiente">
                        <div class="progress-bar bg-' . $color_barra_con . '" style="width:' . $pct_pdte_con . '%"></div>
                    </div>',
                'tipos_documentos'             => $badges_docs ?: '<span class="text-muted">—</span>',
                'ultima_factura'               => $ultima_factura,
                'opciones'                     => $opciones,
            ];
        }

        $results = [
            'draw'            => intval($_POST['draw'] ?? 1),
            'recordsTotal'    => count($data),
            'recordsFiltered' => count($data),
            'data'            => $data,
        ];

        header('Content-Type: application/json');
        echo json_encode($results, JSON_UNESCAPED_UNICODE);
        break;

    // ------------------------------------------------------------------
    // RESUMEN_GLOBAL: KPIs para las tarjetas superiores
    // ------------------------------------------------------------------
    case "resumen_global":
        $filtros_kpi = [
            'solo_pdte_facturar' => !empty($_POST['solo_pdte_facturar']) && $_POST['solo_pdte_facturar'] === '1',
            'solo_pdte_cobrar'   => !empty($_POST['solo_pdte_cobrar'])   && $_POST['solo_pdte_cobrar']   === '1',
            'fecha_evento_desde' => !empty($_POST['fecha_evento_desde']) ? $_POST['fecha_evento_desde'] : null,
            'fecha_evento_hasta' => !empty($_POST['fecha_evento_hasta']) ? $_POST['fecha_evento_hasta'] : null,
        ];
        $resumen = $controlPagos->get_resumen_global($filtros_kpi);

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'data'    => $resumen,
        ], JSON_UNESCAPED_UNICODE);
        break;

    // ------------------------------------------------------------------
    // DETALLE_PAGOS: desglose individual de pagos de un presupuesto
    // ------------------------------------------------------------------
    case "detalle_pagos":
        $id_presupuesto = intval($_POST['id_presupuesto'] ?? 0);

        if ($id_presupuesto <= 0) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'ID de presupuesto inválido'], JSON_UNESCAPED_UNICODE);
            break;
        }

        $pagos = $controlPagos->get_detalle_pagos_presupuesto($id_presupuesto);
        $data  = [];

        $etiquetas_tipo = [
            'anticipo'    => ['Anticipo',    'warning'],
            'total'       => ['Total',       'success'],
            'resto'       => ['Resto',       'primary'],
            'devolucion'  => ['Devolución',  'danger'],
        ];

        $etiquetas_estado = [
            'pendiente'   => ['Pendiente',   'secondary'],
            'recibido'    => ['Recibido',    'success'],
            'conciliado'  => ['Conciliado',  'info'],
            'anulado'     => ['Anulado',     'danger'],
        ];

        foreach ($pagos as $p) {
            [$lbl_tipo,   $col_tipo]   = $etiquetas_tipo[$p['tipo_pago_ppto']] ?? [$p['tipo_pago_ppto'], 'secondary'];
            [$lbl_estado, $col_estado] = $etiquetas_estado[$p['estado_pago_ppto']] ?? [$p['estado_pago_ppto'], 'secondary'];

            $importe = ($p['tipo_pago_ppto'] === 'devolucion' ? '−' : '') . number_format((float)$p['importe_pago_ppto'], 2, ',', '.') . ' €';

            $data[] = [
                'fecha_pago'      => $p['fecha_pago_ppto'] ? date('d/m/Y', strtotime($p['fecha_pago_ppto'])) : '—',
                'tipo'            => '<span class="badge bg-' . $col_tipo . '">' . $lbl_tipo . '</span>',
                'importe'         => '<span class="' . ($p['tipo_pago_ppto'] === 'devolucion' ? 'text-danger' : 'fw-semibold') . '">' . $importe . '</span>',
                'metodo'          => htmlspecialchars($p['nombre_metodo_pago'] ?? '—', ENT_QUOTES, 'UTF-8'),
                'documento'       => $p['numero_documento_ppto']
                    ? '<small>' . htmlspecialchars($p['tipo_documento_ppto'], ENT_QUOTES, 'UTF-8') . '</small><br><strong>' . htmlspecialchars($p['numero_documento_ppto'], ENT_QUOTES, 'UTF-8') . '</strong>'
                    : '—',
                'estado'          => '<span class="badge bg-' . $col_estado . '">' . $lbl_estado . '</span>',
                'referencia'      => htmlspecialchars($p['referencia_pago_ppto'] ?? '—', ENT_QUOTES, 'UTF-8'),
            ];
        }

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'data'    => $data,
            'total'   => count($data),
        ], JSON_UNESCAPED_UNICODE);
        break;

    default:
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Operación no reconocida'], JSON_UNESCAPED_UNICODE);
        break;
}
?>
