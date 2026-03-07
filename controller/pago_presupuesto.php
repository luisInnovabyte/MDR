<?php

require_once "../config/conexion.php";
require_once "../config/funciones.php";
require_once "../models/PagoPresupuesto.php";
require_once "../models/DocumentoPresupuesto.php";
require_once "../models/MetodosPago.php";
require_once "../models/Empresas.php";

header('Content-Type: application/json; charset=utf-8');

$registro   = new RegistroActividad();
$pago       = new PagoPresupuesto();
$documento  = new DocumentoPresupuesto();
$metodos    = new MetodosPago();
$empresas   = new Empresas();

$op = $_GET['op'] ?? '';

switch ($op) {

    // ══════════════════════════════════════════════════════════
    // LISTAR — pagos activos de un presupuesto
    // POST: id_presupuesto
    // ══════════════════════════════════════════════════════════
    case "listar":
        $id_presupuesto = (int)($_POST['id_presupuesto'] ?? 0);

        if (!$id_presupuesto) {
            echo json_encode(['success' => false, 'message' => 'Falta id_presupuesto'], JSON_UNESCAPED_UNICODE);
            break;
        }

        $datos = $pago->get_pagos_presupuesto($id_presupuesto);
        $data  = [];

        foreach ($datos as $row) {
            $data[] = [
                'id_pago_ppto'            => $row['id_pago_ppto'],
                'id_presupuesto'          => $row['id_presupuesto'],
                'id_documento_ppto'       => $row['id_documento_ppto'],
                'numero_documento_ppto'   => $row['numero_documento_vinculado'] ?? null,
                'ruta_pdf_vinculado'      => $row['ruta_pdf_vinculado'] ?? null,
                'tipo_pago_ppto'          => $row['tipo_pago_ppto'],
                'importe_pago_ppto'       => $row['importe_pago_ppto'],
                'porcentaje_pago_ppto'    => $row['porcentaje_pago_ppto'],
                'id_metodo_pago'          => $row['id_metodo_pago'],
                'nombre_metodo_pago'      => $row['nombre_metodo_pago'] ?? '',
                'referencia_pago_ppto'    => $row['referencia_pago_ppto'],
                'fecha_pago_ppto'         => $row['fecha_pago_ppto'],
                'fecha_valor_pago_ppto'   => $row['fecha_valor_pago_ppto'],
                'estado_pago_ppto'        => $row['estado_pago_ppto'],
                'observaciones_pago_ppto' => $row['observaciones_pago_ppto'],
                'activo_pago_ppto'        => $row['activo_pago_ppto'],
                'opciones'                => _opciones_pago($row),
            ];
        }

        echo json_encode([
            'draw'            => intval($_POST['draw'] ?? 1),
            'recordsTotal'    => count($data),
            'recordsFiltered' => count($data),
            'data'            => $data,
        ], JSON_UNESCAPED_UNICODE);
        break;

    // ══════════════════════════════════════════════════════════
    // MOSTRAR — obtener pago por ID
    // POST: id_pago_ppto
    // ══════════════════════════════════════════════════════════
    // ══════════════════════════════════════════════════════════
    // GUARDARYEDITAR — registrar nuevo pago o actualizar existente
    //
    // POST comunes:
    //   id_presupuesto, tipo_pago_ppto, importe_pago_ppto,
    //   fecha_pago_ppto, id_metodo_pago
    // POST opcionales:
    //   id_pago_ppto (si edición), id_documento_ppto,
    //   referencia_pago_ppto, fecha_valor_pago_ppto,
    //   estado_pago_ppto, observaciones_pago_ppto
    // ══════════════════════════════════════════════════════════
    case "guardaryeditar":
        $id_pago_ppto   = !empty($_POST['id_pago_ppto']) ? (int)$_POST['id_pago_ppto'] : null;
        $id_presupuesto = (int)($_POST['id_presupuesto'] ?? 0);
        $tipo           = htmlspecialchars(trim($_POST['tipo_pago_ppto']    ?? ''), ENT_QUOTES, 'UTF-8');
        $importe        = (float)($_POST['importe_pago_ppto']          ?? 0);
        $fecha          = trim($_POST['fecha_pago_ppto']               ?? '');
        $id_metodo      = (int)($_POST['id_metodo_pago']               ?? 0);

        // Validaciones básicas
        if (!$id_presupuesto || !$tipo || $importe <= 0 || !$fecha || !$id_metodo) {
            echo json_encode(['success' => false, 'message' => 'Faltan campos obligatorios o el importe debe ser mayor que cero'], JSON_UNESCAPED_UNICODE);
            break;
        }

        // Validar que el tipo es válido
        $tipos_validos = ['anticipo', 'total', 'resto'];
        if (!in_array($tipo, $tipos_validos)) {
            echo json_encode(['success' => false, 'message' => 'Tipo de pago no válido'], JSON_UNESCAPED_UNICODE);
            break;
        }

        // Validar fecha
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
            echo json_encode(['success' => false, 'message' => 'Formato de fecha incorrecto (YYYY-MM-DD)'], JSON_UNESCAPED_UNICODE);
            break;
        }

        // Solo se pueden registrar pagos NUEVOS en presupuestos aprobados
        if (empty($id_pago_ppto)) {
            try {
                $pdo        = (new Conexion())->getConexion();
                $stmtEstado = $pdo->prepare(
                    "SELECT ep.codigo_estado_ppto, ep.nombre_estado_ppto
                     FROM presupuesto p
                     JOIN estado_presupuesto ep ON p.id_estado_ppto = ep.id_estado_ppto
                     WHERE p.id_presupuesto = ?"
                );
                $stmtEstado->execute([$id_presupuesto]);
                $rowEstado = $stmtEstado->fetch(PDO::FETCH_ASSOC);
                if (!$rowEstado || $rowEstado['codigo_estado_ppto'] !== 'APROB') {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Solo se pueden registrar pagos en presupuestos aprobados.',
                        'estado'  => $rowEstado['nombre_estado_ppto'] ?? null,
                    ], JSON_UNESCAPED_UNICODE);
                    break;
                }
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Error al verificar el estado del presupuesto'], JSON_UNESCAPED_UNICODE);
                break;
            }

            // ── Validaciones de negocio (solo inserts nuevos) ────────────
            try {
                $pdoVal = (new Conexion())->getConexion();

                // V1: Ningún cobro puede registrarse si el saldo pendiente es 0,
                //     ni superar el saldo pendiente (aplica a anticipo, total y resto)
                $saldoActual = $pago->get_saldo_pendiente($id_presupuesto);
                if ($saldoActual <= 0) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'El presupuesto ya está completamente pagado (saldo pendiente: 0,00 €). No se pueden registrar más cobros. Para rectificar un cobro, emita un abono desde el tab Documentos.',
                    ], JSON_UNESCAPED_UNICODE);
                    break;
                }
                if ($importe > round($saldoActual + 0.01, 2)) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'El importe (' . number_format($importe, 2, ',', '.') . ' €) supera el saldo pendiente (' . number_format($saldoActual, 2, ',', '.') . ' €).',
                    ], JSON_UNESCAPED_UNICODE);
                    break;
                }

                // V2: Un Resto requiere anticipos previos activos
                if ($tipo === 'resto') {
                    $stmtAntic = $pdoVal->prepare(
                        "SELECT COUNT(*) FROM pago_presupuesto
                         WHERE id_presupuesto   = ?
                           AND tipo_pago_ppto   = 'anticipo'
                           AND estado_pago_ppto != 'anulado'
                           AND activo_pago_ppto = 1"
                    );
                    $stmtAntic->execute([$id_presupuesto]);
                    if ((int)$stmtAntic->fetchColumn() === 0) {
                        echo json_encode([
                            'success' => false,
                            'message' => 'No se puede registrar un Resto sin anticipos previos. Usa "Pago Total" si es el primer cobro.',
                        ], JSON_UNESCAPED_UNICODE);
                        break;
                    }
                }

            } catch (Exception $eVal) {
                $registro->registrarActividad('admin', 'pago_presupuesto.php', 'guardaryeditar',
                    "Error en validaciones de negocio: " . $eVal->getMessage(), 'warning');
                // No bloqueamos la operación por un error en las validaciones extra
            }
        }

        // Campos opcionales
        $id_documento   = !empty($_POST['id_documento_ppto'])       ? (int)$_POST['id_documento_ppto']   : null;
        $id_empresa_pago = !empty($_POST['id_empresa'])             ? (int)$_POST['id_empresa']           : null;
        $referencia     = !empty($_POST['referencia_pago_ppto'])     ? htmlspecialchars(trim($_POST['referencia_pago_ppto']), ENT_QUOTES, 'UTF-8')     : null;
        $fecha_valor    = !empty($_POST['fecha_valor_pago_ppto'])    ? trim($_POST['fecha_valor_pago_ppto'])    : null;
        $estado         = !empty($_POST['estado_pago_ppto'])         ? trim($_POST['estado_pago_ppto'])         : 'pendiente';
        $observaciones  = !empty($_POST['observaciones_pago_ppto'])  ? htmlspecialchars(trim($_POST['observaciones_pago_ppto']), ENT_QUOTES, 'UTF-8')  : null;

        // Validar estado
        $estados_validos = ['pendiente', 'recibido', 'conciliado', 'anulado'];
        if (!in_array($estado, $estados_validos)) {
            $estado = 'recibido';
        }

        $datos = [
            'id_presupuesto'          => $id_presupuesto,
            'tipo_pago_ppto'          => $tipo,
            'importe_pago_ppto'       => $importe,
            'fecha_pago_ppto'         => $fecha,
            'id_metodo_pago'          => $id_metodo,
            'id_documento_ppto'       => $id_documento,
            'id_empresa_pago'         => $id_empresa_pago,
            'referencia_pago_ppto'    => $referencia,
            'fecha_valor_pago_ppto'   => $fecha_valor,
            'estado_pago_ppto'        => $estado,
            'observaciones_pago_ppto' => $observaciones,
        ];

        try {
            if (empty($id_pago_ppto)) {
                // INSERT
                $id_nuevo = $pago->insert_pago($datos);

                if ($id_nuevo) {
                    echo json_encode([
                        'success'       => true,
                        'message'       => 'Pago registrado correctamente',
                        'id_pago_ppto'  => $id_nuevo,
                    ], JSON_UNESCAPED_UNICODE);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Error al registrar el pago'], JSON_UNESCAPED_UNICODE);
                }
            } else {
                // UPDATE
                $ok = $pago->update_pago($id_pago_ppto, $datos);

                echo json_encode([
                    'success' => (bool)$ok,
                    'message' => $ok ? 'Pago actualizado correctamente' : 'Error al actualizar el pago',
                ], JSON_UNESCAPED_UNICODE);
            }
        } catch (Exception $e) {
            $registro->registrarActividad('admin', 'pago_presupuesto.php', 'guardaryeditar',
                "Error: " . $e->getMessage(), 'error');
            echo json_encode(['success' => false, 'message' => 'Error al procesar la solicitud'], JSON_UNESCAPED_UNICODE);
        }
        break;

    // ══════════════════════════════════════════════════════════
    // DESACTIVAR — soft delete de pago (mantiene histórico)
    // POST: id_pago_ppto
    // ══════════════════════════════════════════════════════════
    case "desactivar":
        $id_pago_ppto = (int)($_POST['id_pago_ppto'] ?? 0);

        if (!$id_pago_ppto) {
            echo json_encode(['success' => false, 'message' => 'Falta id_pago_ppto'], JSON_UNESCAPED_UNICODE);
            break;
        }

        $ok = $pago->delete_pagoxid($id_pago_ppto);

        echo json_encode([
            'success' => $ok,
            'message' => $ok ? 'Pago desactivado correctamente' : 'Error al desactivar el pago',
        ], JSON_UNESCAPED_UNICODE);
        break;

    // ══════════════════════════════════════════════════════════
    // ANULAR — cambia estado a 'anulado' (permanece en histórico)
    // POST: id_pago_ppto
    // ══════════════════════════════════════════════════════════
    case "anular":
        $id_pago_ppto = (int)($_POST['id_pago_ppto'] ?? 0);

        if (!$id_pago_ppto) {
            echo json_encode(['success' => false, 'message' => 'Falta id_pago_ppto'], JSON_UNESCAPED_UNICODE);
            break;
        }

        $ok = $pago->anular_pago_por_abono($id_pago_ppto);

        echo json_encode([
            'success' => $ok,
            'message' => $ok ? 'Pago anulado correctamente' : 'El pago ya está anulado o no existe',
        ], JSON_UNESCAPED_UNICODE);
        break;

    // ══════════════════════════════════════════════════════════
    // CONCILIAR — marca pago como conciliado con extracto
    // POST: id_pago_ppto
    // ══════════════════════════════════════════════════════════
    case "conciliar":
        $id_pago_ppto = (int)($_POST['id_pago_ppto'] ?? 0);

        if (!$id_pago_ppto) {
            echo json_encode(['success' => false, 'message' => 'Falta id_pago_ppto'], JSON_UNESCAPED_UNICODE);
            break;
        }

        $ok = $pago->conciliar_pago($id_pago_ppto);

        echo json_encode([
            'success' => $ok,
            'message' => $ok ? 'Pago conciliado correctamente' : 'Error al conciliar el pago',
        ], JSON_UNESCAPED_UNICODE);
        break;

    // ══════════════════════════════════════════════════════════
    // CALCULAR_SALDO — saldo pendiente de cobro
    // POST: id_presupuesto
    // ══════════════════════════════════════════════════════════
    case "calcular_saldo":
        $id_presupuesto = (int)($_POST['id_presupuesto'] ?? 0);

        if (!$id_presupuesto) {
            echo json_encode(['success' => false, 'message' => 'Falta id_presupuesto'], JSON_UNESCAPED_UNICODE);
            break;
        }

        $saldo = $pago->get_saldo_pendiente($id_presupuesto);

        echo json_encode([
            'success'         => true,
            'saldo_pendiente' => $saldo,
        ], JSON_UNESCAPED_UNICODE);
        break;

    // ══════════════════════════════════════════════════════════
    // VERIFICAR_ESTADO_PAGABLE — ¿puede el presupuesto recibir pagos?
    // POST: id_presupuesto
    // ══════════════════════════════════════════════════════════
    case "verificar_estado_pagable":
        $id_presupuesto = (int)($_POST['id_presupuesto'] ?? 0);
        if (!$id_presupuesto) {
            echo json_encode(['pagable' => false, 'estado' => null], JSON_UNESCAPED_UNICODE);
            break;
        }
        $pdo = (new Conexion())->getConexion();
        $stmtPagable = $pdo->prepare(
            "SELECT ep.codigo_estado_ppto, ep.nombre_estado_ppto
             FROM presupuesto p
             JOIN estado_presupuesto ep ON p.id_estado_ppto = ep.id_estado_ppto
             WHERE p.id_presupuesto = ?"
        );
        $stmtPagable->execute([$id_presupuesto]);
        $rowPagable = $stmtPagable->fetch(PDO::FETCH_ASSOC);
        echo json_encode([
            'pagable' => ($rowPagable && $rowPagable['codigo_estado_ppto'] === 'APROB'),
            'estado'  => $rowPagable['nombre_estado_ppto'] ?? null,
        ], JSON_UNESCAPED_UNICODE);
        break;

    // ══════════════════════════════════════════════════════════
    // VERIFICAR_PAGO_COMPLETO — ¿está el presupuesto completamente pagado?
    // POST: id_presupuesto
    // ══════════════════════════════════════════════════════════
    case "verificar_pago_completo":
        $id_presupuesto = (int)($_POST['id_presupuesto'] ?? 0);

        if (!$id_presupuesto) {
            echo json_encode(['success' => false, 'message' => 'Falta id_presupuesto'], JSON_UNESCAPED_UNICODE);
            break;
        }

        $resumen = $pago->get_resumen_financiero($id_presupuesto);
        $completo = $resumen['saldo_pendiente'] <= 0 && $resumen['total_presupuesto'] > 0;

        echo json_encode([
            'success'            => true,
            'completo'           => $completo,
            'total_presupuesto'  => $resumen['total_presupuesto'],
            'total_pagado'       => $resumen['total_pagado'],
            'saldo_pendiente'    => $resumen['saldo_pendiente'],
            'porcentaje_pagado'  => $resumen['porcentaje_pagado'],
        ], JSON_UNESCAPED_UNICODE);
        break;

    // ══════════════════════════════════════════════════════════
    // RESUMEN_FINANCIERO — totales de un presupuesto
    // POST: id_presupuesto
    // ══════════════════════════════════════════════════════════
    case "resumen_financiero":
        $id_presupuesto = (int)($_POST['id_presupuesto'] ?? 0);

        if (!$id_presupuesto) {
            echo json_encode(['success' => false, 'message' => 'Falta id_presupuesto'], JSON_UNESCAPED_UNICODE);
            break;
        }

        $resumen = $pago->get_resumen_financiero($id_presupuesto);

        echo json_encode(array_merge(['success' => true], $resumen), JSON_UNESCAPED_UNICODE);
        break;

    // ══════════════════════════════════════════════════════════
    // LISTAR_EMPRESAS_FACTURACION — empresas reales disponibles
    //   para emitir facturas (anticipo/final/rectificativa)
    // POST: id_presupuesto (opcional — si se pasa, informa si hay empresa bloqueada)
    // ══════════════════════════════════════════════════════════
    case "listar_empresas_facturacion":
        $id_presupuesto = (int)($_POST['id_presupuesto'] ?? 0);

        $lista = $empresas->get_empresas_reales_activas();

        // Si se pasa id_presupuesto, indicar cuál está bloqueada
        $empresa_bloqueada_id = null;
        if ($id_presupuesto) {
            $bloqueada = $pago->obtener_empresa_bloqueada_por_pagos($id_presupuesto);
            if ($bloqueada) {
                $empresa_bloqueada_id = (int)$bloqueada['id_empresa'];
            }
        }

        $data = [];
        foreach ($lista as $row) {
            $data[] = [
                'id_empresa'                            => $row['id_empresa'],
                'codigo_empresa'                        => $row['codigo_empresa'],
                'nombre_empresa'                        => $row['nombre_empresa'],
                'nif_empresa'                           => $row['nif_empresa'],
                'serie_factura_empresa'                 => $row['serie_factura_empresa'],
                'numero_actual_factura_empresa'         => $row['numero_actual_factura_empresa'],
                'serie_factura_proforma_empresa'        => $row['serie_factura_proforma_empresa'],
                'numero_actual_factura_proforma_empresa'=> $row['numero_actual_factura_proforma_empresa'],
                'serie_abono_empresa'                   => $row['serie_abono_empresa'],
                'numero_actual_abono_empresa'           => $row['numero_actual_abono_empresa'],
                'bloqueada'                             => ($empresa_bloqueada_id && (int)$row['id_empresa'] === $empresa_bloqueada_id),
            ];
        }

        echo json_encode([
            'success'              => true,
            'data'                 => $data,
            'empresa_bloqueada_id' => $empresa_bloqueada_id,
        ], JSON_UNESCAPED_UNICODE);
        break;

    // ══════════════════════════════════════════════════════════
    // LISTAR_METODOS_PAGO — métodos de pago disponibles
    //   (helper para selectores en el formulario)
    // ══════════════════════════════════════════════════════════
    case "listar_metodos_pago":
        $lista = $metodos->get_metodo_pago_disponible();
        $data  = [];
        foreach ($lista as $row) {
            $data[] = [
                'id_metodo_pago'     => $row['id_metodo_pago'],
                'codigo_metodo_pago' => $row['codigo_metodo_pago'],
                'nombre_metodo_pago' => $row['nombre_metodo_pago'],
            ];
        }
        echo json_encode(['success' => true, 'data' => $data], JSON_UNESCAPED_UNICODE);
        break;

    // ══════════════════════════════════════════════════════════
    // DEFAULT
    // ══════════════════════════════════════════════════════════
    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => "Operación '$op' no reconocida"], JSON_UNESCAPED_UNICODE);
        break;
}

// ══════════════════════════════════════════════════════════
//  HELPERS PRIVADOS
// ══════════════════════════════════════════════════════════

/**
 * Genera el HTML de botones de acción para el DataTable de pagos.
 */
function _opciones_pago(array $row): string
{
    $id     = $row['id_pago_ppto'];
    $estado = $row['estado_pago_ppto'];
    $activo = $row['activo_pago_ppto'];
    $html   = '';

    // Ver factura asociada (si el pago tiene documento vinculado con PDF)
    if (!empty($row['ruta_pdf_vinculado'])) {
        $ruta = htmlspecialchars($row['ruta_pdf_vinculado']);
        $html .= '<a href="../../' . $ruta . '" target="_blank"
                     class="btn btn-info btn-sm me-1" title="Ver factura">
                    <i class="fa fa-file-pdf"></i>
                  </a>';
    }

    // Conciliar (si está pendiente o recibido — no anulado ni ya conciliado)
    if (in_array($estado, ['pendiente', 'recibido']) && $activo) {
        $html .= '<button class="btn btn-success btn-sm me-1" onclick="conciliarPago(' . $id . ')"
                          title="Marcar como conciliado">
                    <i class="fa fa-check-double"></i>
                  </button>';
    }

    // Anular (solo si activo y no anulado)
    if ($activo && $estado !== 'anulado') {
        $html .= '<button class="btn btn-danger btn-sm" onclick="anularPago(' . $id . ')"
                          title="Anular pago">
                    <i class="fa fa-ban"></i>
                  </button>';
    }

    return $html;
}
