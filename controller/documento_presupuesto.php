<?php

require_once "../config/conexion.php";
require_once "../config/funciones.php";
require_once "../models/DocumentoPresupuesto.php";
require_once "../models/Empresas.php";

header('Content-Type: application/json; charset=utf-8');

$registro   = new RegistroActividad();
$documento  = new DocumentoPresupuesto();
$empresas   = new Empresas();

$op = $_GET['op'] ?? '';

switch ($op) {

    // ══════════════════════════════════════════════════════════
    // LISTAR — todos los documentos activos de un presupuesto
    // POST: id_presupuesto
    // ══════════════════════════════════════════════════════════
    case "listar":
        $id_presupuesto = (int)($_POST['id_presupuesto'] ?? 0);

        if (!$id_presupuesto) {
            echo json_encode(['success' => false, 'message' => 'Falta id_presupuesto'], JSON_UNESCAPED_UNICODE);
            break;
        }

        $datos = $documento->get_documentos_presupuesto($id_presupuesto);
        $data  = [];

        foreach ($datos as $row) {
            $data[] = [
                'id_documento_ppto'                   => $row['id_documento_ppto'],
                'id_presupuesto'                      => $row['id_presupuesto'],
                'id_version_presupuesto'              => $row['id_version_presupuesto'],
                'id_empresa'                          => $row['id_empresa'],
                'nombre_empresa'                      => $row['nombre_empresa'] ?? '',
                'tipo_documento_ppto'                 => $row['tipo_documento_ppto'],
                'numero_documento_ppto'               => $row['numero_documento_ppto'],
                'subtotal_documento_ppto'             => $row['subtotal_documento_ppto'],
                'total_iva_documento_ppto'            => $row['total_iva_documento_ppto'],
                'total_documento_ppto'                => $row['total_documento_ppto'],
                'fecha_emision_documento_ppto'        => $row['fecha_emision_documento_ppto'],
                'fecha_generacion_documento_ppto'     => $row['fecha_generacion_documento_ppto'],
                'ruta_pdf_documento_ppto'             => $row['ruta_pdf_documento_ppto'],
                'id_documento_origen'                 => $row['id_documento_origen'],
                'numero_documento_origen'             => $row['numero_documento_origen'] ?? null,
                'activo_documento_ppto'               => $row['activo_documento_ppto'],
                'opciones'                            => _opciones_documento($row),
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
    // LISTAR_POR_TIPO — filtrar por tipo de documento
    // POST: id_presupuesto, tipo_documento_ppto
    // ══════════════════════════════════════════════════════════
    case "listar_por_tipo":
        $id_presupuesto = (int)($_POST['id_presupuesto'] ?? 0);
        $tipo           = trim($_POST['tipo_documento_ppto'] ?? '');

        if (!$id_presupuesto || !$tipo) {
            echo json_encode(['success' => false, 'message' => 'Faltan parámetros'], JSON_UNESCAPED_UNICODE);
            break;
        }

        $datos = $documento->get_documentos_por_tipo($id_presupuesto, $tipo);

        echo json_encode([
            'success' => true,
            'data'    => $datos,
        ], JSON_UNESCAPED_UNICODE);
        break;

    // ══════════════════════════════════════════════════════════
    // MOSTRAR — obtener documento por ID
    // POST: id_documento_ppto
    // ══════════════════════════════════════════════════════════
    case "mostrar":
        $id_documento_ppto = (int)($_POST['id_documento_ppto'] ?? 0);

        if (!$id_documento_ppto) {
            echo json_encode(['success' => false, 'message' => 'Falta id_documento_ppto'], JSON_UNESCAPED_UNICODE);
            break;
        }

        $datos = $documento->get_documentoxid($id_documento_ppto);

        if ($datos) {
            echo json_encode($datos, JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(['success' => false, 'message' => 'Documento no encontrado'], JSON_UNESCAPED_UNICODE);
        }
        break;

    // ══════════════════════════════════════════════════════════
    // GUARDARYEDITAR — registrar nuevo documento
    // POST: id_presupuesto, id_version_presupuesto, id_empresa,
    //       tipo_documento_ppto, fecha_emision_documento_ppto,
    //       subtotal_documento_ppto (opcional), total_iva_documento_ppto (opcional),
    //       total_documento_ppto (opcional),
    //       id_documento_origen (solo rectificativas),
    //       motivo_abono_documento_ppto (solo rectificativas),
    //       seleccion_manual_empresa_documento_ppto (0/1),
    //       observaciones_documento_ppto (opcional)
    // ══════════════════════════════════════════════════════════
    case "guardaryeditar":
        $id_presupuesto      = (int)($_POST['id_presupuesto']      ?? 0);
        $id_version          = (int)($_POST['id_version_presupuesto'] ?? 0);
        $id_empresa          = (int)($_POST['id_empresa']           ?? 0);
        $tipo                = htmlspecialchars(trim($_POST['tipo_documento_ppto']  ?? ''), ENT_QUOTES, 'UTF-8');
        $fecha_emision       = trim($_POST['fecha_emision_documento_ppto'] ?? date('Y-m-d'));

        if (!$id_presupuesto || !$id_version || !$id_empresa || !$tipo) {
            echo json_encode(['success' => false, 'message' => 'Faltan campos obligatorios'], JSON_UNESCAPED_UNICODE);
            break;
        }

        // Validar que las facturas usan empresa real
        $tipos_requieren_empresa_real = ['factura_anticipo', 'factura_final', 'factura_rectificativa'];
        if (in_array($tipo, $tipos_requieren_empresa_real)) {
            if (!$documento->verificar_empresa_real($id_empresa)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Las facturas solo pueden emitirse con una empresa real (no ficticia)',
                ], JSON_UNESCAPED_UNICODE);
                break;
            }
        }

        // Validar empresa bloqueada: si ya hay facturas, no se puede cambiar
        $empresa_bloqueada = $documento->obtener_empresa_bloqueada_presupuesto($id_presupuesto);
        if ($empresa_bloqueada && in_array($tipo, $tipos_requieren_empresa_real)) {
            if ((int)$empresa_bloqueada['id_empresa'] !== $id_empresa) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Este presupuesto ya tiene facturas emitidas con la empresa "' .
                                 $empresa_bloqueada['nombre_empresa'] . '". No se puede cambiar de empresa emisora.',
                ], JSON_UNESCAPED_UNICODE);
                break;
            }
        }

        // Validar documento origen en rectificativas
        $id_documento_origen = !empty($_POST['id_documento_origen'])
            ? (int)$_POST['id_documento_origen'] : null;

        if ($tipo === 'factura_rectificativa') {
            if (!$id_documento_origen) {
                echo json_encode(['success' => false, 'message' => 'Una factura rectificativa requiere id_documento_origen'], JSON_UNESCAPED_UNICODE);
                break;
            }
            $check = $documento->verificar_puede_abonar($id_documento_origen);
            if (!$check['puede']) {
                echo json_encode(['success' => false, 'message' => $check['motivo']], JSON_UNESCAPED_UNICODE);
                break;
            }
        }

        // Campos opcionales
        $subtotal    = !empty($_POST['subtotal_documento_ppto'])    ? (float)$_POST['subtotal_documento_ppto']    : null;
        $total_iva   = !empty($_POST['total_iva_documento_ppto'])   ? (float)$_POST['total_iva_documento_ppto']   : null;
        $total       = !empty($_POST['total_documento_ppto'])       ? (float)$_POST['total_documento_ppto']       : null;
        $motivo      = !empty($_POST['motivo_abono_documento_ppto']) ? htmlspecialchars(trim($_POST['motivo_abono_documento_ppto']), ENT_QUOTES, 'UTF-8') : null;
        $obs         = !empty($_POST['observaciones_documento_ppto']) ? htmlspecialchars(trim($_POST['observaciones_documento_ppto']), ENT_QUOTES, 'UTF-8') : null;
        $sel_manual  = (int)($_POST['seleccion_manual_empresa_documento_ppto'] ?? 0);

        $datos = [
            'id_presupuesto'                            => $id_presupuesto,
            'id_version_presupuesto'                    => $id_version,
            'id_empresa'                                => $id_empresa,
            'seleccion_manual_empresa_documento_ppto'   => $sel_manual,
            'tipo_documento_ppto'                       => $tipo,
            'fecha_emision'                             => $fecha_emision,
            'subtotal_documento_ppto'                   => $subtotal,
            'total_iva_documento_ppto'                  => $total_iva,
            'total_documento_ppto'                      => $total,
            'id_documento_origen'                       => $id_documento_origen,
            'motivo_abono_documento_ppto'               => $motivo,
            'observaciones_documento_ppto'              => $obs,
        ];

        try {
            $id_nuevo = $documento->insert_documento($datos);

            if ($id_nuevo) {
                echo json_encode([
                    'success'           => true,
                    'message'           => 'Documento registrado correctamente',
                    'id_documento_ppto' => $id_nuevo,
                ], JSON_UNESCAPED_UNICODE);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al registrar el documento'], JSON_UNESCAPED_UNICODE);
            }
        } catch (Exception $e) {
            $registro->registrarActividad('admin', 'documento_presupuesto.php', 'guardaryeditar',
                "Error: " . $e->getMessage(), 'error');
            echo json_encode(['success' => false, 'message' => 'Error al procesar la solicitud'], JSON_UNESCAPED_UNICODE);
        }
        break;

    // ══════════════════════════════════════════════════════════
    // DESACTIVAR — soft delete
    // POST: id_documento_ppto
    // ══════════════════════════════════════════════════════════
    case "desactivar":
        $id_documento_ppto = (int)($_POST['id_documento_ppto'] ?? 0);

        if (!$id_documento_ppto) {
            echo json_encode(['success' => false, 'message' => 'Falta id_documento_ppto'], JSON_UNESCAPED_UNICODE);
            break;
        }

        $ok = $documento->delete_documentoxid($id_documento_ppto);

        echo json_encode([
            'success' => $ok,
            'message' => $ok ? 'Documento desactivado correctamente' : 'Error al desactivar el documento',
        ], JSON_UNESCAPED_UNICODE);
        break;

    // ══════════════════════════════════════════════════════════
    // ACTIVAR — reactivar soft-deleted
    // POST: id_documento_ppto
    // ══════════════════════════════════════════════════════════
    case "activar":
        $id_documento_ppto = (int)($_POST['id_documento_ppto'] ?? 0);

        if (!$id_documento_ppto) {
            echo json_encode(['success' => false, 'message' => 'Falta id_documento_ppto'], JSON_UNESCAPED_UNICODE);
            break;
        }

        $ok = $documento->activar_documentoxid($id_documento_ppto);

        echo json_encode([
            'success' => $ok,
            'message' => $ok ? 'Documento activado correctamente' : 'Error al activar el documento',
        ], JSON_UNESCAPED_UNICODE);
        break;

    // ══════════════════════════════════════════════════════════
    // VERIFICAR_PUEDE_ABONAR — saber si una factura es abonable
    // POST: id_documento_ppto
    // ══════════════════════════════════════════════════════════
    case "verificar_puede_abonar":
        $id_documento_ppto = (int)($_POST['id_documento_ppto'] ?? 0);

        if (!$id_documento_ppto) {
            echo json_encode(['success' => false, 'puede' => false, 'message' => 'Falta id_documento_ppto'], JSON_UNESCAPED_UNICODE);
            break;
        }

        $resultado = $documento->verificar_puede_abonar($id_documento_ppto);

        echo json_encode([
            'success' => true,
            'puede'   => $resultado['puede'],
            'motivo'  => $resultado['motivo'],
        ], JSON_UNESCAPED_UNICODE);
        break;

    // ══════════════════════════════════════════════════════════
    // VERIFICAR_EMPRESA_DISPONIBLE — informa si la empresa emisora
    // está bloqueada (ya se emitió factura con empresa X)
    // POST: id_presupuesto
    // ══════════════════════════════════════════════════════════
    case "verificar_empresa_disponible":
        $id_presupuesto = (int)($_POST['id_presupuesto'] ?? 0);

        if (!$id_presupuesto) {
            echo json_encode(['success' => false, 'message' => 'Falta id_presupuesto'], JSON_UNESCAPED_UNICODE);
            break;
        }

        $empresa_bloqueada = $documento->obtener_empresa_bloqueada_presupuesto($id_presupuesto);

        if ($empresa_bloqueada) {
            echo json_encode([
                'success'          => true,
                'bloqueada'        => true,
                'id_empresa'       => $empresa_bloqueada['id_empresa'],
                'nombre_empresa'   => $empresa_bloqueada['nombre_empresa'],
                'codigo_empresa'   => $empresa_bloqueada['codigo_empresa'],
                'message'          => 'La empresa emisora está bloqueada para este presupuesto.',
            ], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode([
                'success'   => true,
                'bloqueada' => false,
                'message'   => 'No hay empresa bloqueada. Se puede seleccionar libremente.',
            ], JSON_UNESCAPED_UNICODE);
        }
        break;

    // ══════════════════════════════════════════════════════════
    // REPETIR_PROFORMA — repite una factura proforma de anticipo
    //   (desactiva la anterior y crea una nueva)
    // POST: id_documento_ppto (la proforma a sustituir),
    //       id_presupuesto, id_version_presupuesto, id_empresa,
    //       fecha_emision_documento_ppto,
    //       subtotal_documento_ppto (opcional), total_iva (opcional),
    //       total_documento_ppto (opcional),
    //       observaciones_documento_ppto (opcional)
    // ══════════════════════════════════════════════════════════
    case "repetir_proforma":
        $id_documento_origen = (int)($_POST['id_documento_ppto'] ?? 0);
        $id_presupuesto      = (int)($_POST['id_presupuesto']    ?? 0);
        $id_version          = (int)($_POST['id_version_presupuesto'] ?? 0);
        $id_empresa          = (int)($_POST['id_empresa']        ?? 0);
        $fecha_emision       = trim($_POST['fecha_emision_documento_ppto'] ?? date('Y-m-d'));

        if (!$id_documento_origen || !$id_presupuesto || !$id_version || !$id_empresa) {
            echo json_encode(['success' => false, 'message' => 'Faltan campos obligatorios'], JSON_UNESCAPED_UNICODE);
            break;
        }

        $subtotal  = !empty($_POST['subtotal_documento_ppto'])   ? (float)$_POST['subtotal_documento_ppto']   : null;
        $total_iva = !empty($_POST['total_iva_documento_ppto'])  ? (float)$_POST['total_iva_documento_ppto']  : null;
        $total     = !empty($_POST['total_documento_ppto'])      ? (float)$_POST['total_documento_ppto']      : null;
        $obs       = !empty($_POST['observaciones_documento_ppto']) ? htmlspecialchars(trim($_POST['observaciones_documento_ppto']), ENT_QUOTES, 'UTF-8') : null;

        $datos_nuevo = [
            'id_presupuesto'             => $id_presupuesto,
            'id_version_presupuesto'     => $id_version,
            'id_empresa'                 => $id_empresa,
            'tipo_documento_ppto'        => 'factura_proforma',
            'fecha_emision'              => $fecha_emision,
            'subtotal_documento_ppto'    => $subtotal,
            'total_iva_documento_ppto'   => $total_iva,
            'total_documento_ppto'       => $total,
            'observaciones_documento_ppto' => $obs,
        ];

        try {
            $id_nuevo = $documento->repetir_proforma_anticipo($id_documento_origen, $datos_nuevo);

            if ($id_nuevo) {
                echo json_encode([
                    'success'           => true,
                    'message'           => 'Proforma repetida correctamente',
                    'id_documento_ppto' => $id_nuevo,
                ], JSON_UNESCAPED_UNICODE);
            } else {
                echo json_encode(['success' => false, 'message' => 'No se pudo repetir la proforma'], JSON_UNESCAPED_UNICODE);
            }
        } catch (Exception $e) {
            $registro->registrarActividad('admin', 'documento_presupuesto.php', 'repetir_proforma',
                "Error: " . $e->getMessage(), 'error');
            echo json_encode(['success' => false, 'message' => 'Error al procesar la solicitud'], JSON_UNESCAPED_UNICODE);
        }
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
 * Genera el HTML de botones de acción para el DataTable.
 */
function _opciones_documento(array $row): string
{
    $id   = $row['id_documento_ppto'];
    $tipo = $row['tipo_documento_ppto'];
    $html = '';

    // Ver / descargar PDF (solo si hay ruta)
    if (!empty($row['ruta_pdf_documento_ppto'])) {
        $html .= '<a href="../../' . htmlspecialchars($row['ruta_pdf_documento_ppto']) . '"
                     target="_blank" class="btn btn-info btn-sm me-1" title="Ver PDF">
                    <i class="fa fa-file-pdf"></i>
                  </a>';
    }

    // Botón repetir proforma (solo factura_proforma activa)
    if ($tipo === 'factura_proforma' && $row['activo_documento_ppto']) {
        $html .= '<button class="btn btn-secondary btn-sm me-1" onclick="repetirProforma(' . $id . ')"
                          title="Repetir proforma">
                    <i class="fa fa-redo"></i>
                  </button>';
    }

    // Botón desactivar
    if ($row['activo_documento_ppto']) {
        $html .= '<button class="btn btn-danger btn-sm" onclick="desactivarDocumento(' . $id . ')"
                          title="Desactivar">
                    <i class="fa fa-trash"></i>
                  </button>';
    }

    return $html;
}
