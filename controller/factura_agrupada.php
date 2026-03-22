<?php

require_once "../config/conexion.php";
require_once "../config/funciones.php";
require_once "../models/FacturaAgrupada.php";

$registro        = new RegistroActividad();
$facturaAgrupada = new FacturaAgrupada();

switch ($_GET["op"] ?? '') {

    // ------------------------------------------------------------------
    // LISTAR — DataTables
    // ------------------------------------------------------------------
    case "listar":
        $datos = $facturaAgrupada->get_facturas_agrupadas();
        $data  = [];

        foreach ($datos as $row) {
            $es_abono   = (bool)($row['is_abono_agrupada'] ?? false);
            $esta_abonada = !$es_abono
                            && !(bool)($row['activo_factura_agrupada'] ?? true)
                            && !empty($row['id_abono_asociado']);

            $num     = htmlspecialchars($row['numero_factura_agrupada'], ENT_QUOTES, 'UTF-8');
            $cliente = htmlspecialchars($row['nombre_cliente'],          ENT_QUOTES, 'UTF-8');
            $empresa = htmlspecialchars($row['nombre_empresa'],          ENT_QUOTES, 'UTF-8');
            $fecha   = $row['fecha_factura_agrupada'];
            $id      = (int)$row['id_factura_agrupada'];

            if ($es_abono) {
                $badge = '<span class="badge bg-warning text-dark">ABONO</span>';
            } elseif ($esta_abonada) {
                $badge = '<span class="badge bg-danger">ABONADA</span>';
            } else {
                $badge = '<span class="badge bg-success">FACTURA</span>';
            }

            // Botón PDF de la propia factura/abono
            $btn_pdf = '<button class="btn btn-sm btn-outline-primary me-1" onclick="descargarPDF(' . $id . ')" title="Descargar PDF">
                            <i class="fa fa-file-pdf"></i>
                        </button>';

            // Si está ABONADA: botón para ir al PDF de la rectificativa asociada
            $btn_pdf_abono = '';
            if ($esta_abonada && !empty($row['id_abono_asociado'])) {
                $id_abono     = (int)$row['id_abono_asociado'];
                $num_abono    = htmlspecialchars($row['numero_abono_asociado'] ?? '', ENT_QUOTES, 'UTF-8');
                $btn_pdf_abono = '<button class="btn btn-sm btn-outline-warning me-1" onclick="descargarPDF(' . $id_abono . ')" title="PDF Rectificativa: ' . $num_abono . '">
                                      <i class="fa fa-file-pdf"></i> <small>RECT.</small>
                                  </button>';
            }

            // Botón generar abono: solo facturas normales activas
            $btn_abono = (!$es_abono && !$esta_abonada)
                ? '<button class="btn btn-sm btn-outline-warning me-1" onclick="generarAbono(' . $id . ')" title="Generar rectificativa">
                       <i class="fa fa-undo"></i>
                   </button>'
                : '';

            // Botón desactivar: solo registros activos (no ABONADAS que ya están inactivas)
            $btn_del = !$esta_abonada
                ? '<button class="btn btn-sm btn-outline-danger" onclick="desactivar(' . $id . ')" title="Desactivar">
                       <i class="fa fa-trash"></i>
                   </button>'
                : '';

            $data[] = [
                'id_factura_agrupada'     => $id,
                'numero_factura_agrupada' => $num . ' ' . $badge,
                'nombre_cliente'          => $cliente,
                'nombre_empresa'          => $empresa,
                'fecha_factura_agrupada'  => $fecha,
                'total_bruto_agrupada'    => (float)$row['total_bruto_agrupada'],
                'total_a_cobrar_agrupada' => (float)$row['total_a_cobrar_agrupada'],
                'total_presupuestos'      => $row['numeros_presupuestos_agrupada'] ?? '',
                'is_abono_agrupada'       => (int)$es_abono,
                'esta_abonada'            => (int)$esta_abonada,
                'id_abono_asociado'       => !empty($row['id_abono_asociado']) ? (int)$row['id_abono_asociado'] : null,
                'numero_abono_asociado'   => $row['numero_abono_asociado'] ?? null,
                'pdf_path_agrupada'       => $row['pdf_path_agrupada'] ?? null,
                'opciones'                => $btn_pdf . $btn_pdf_abono . $btn_abono . $btn_del,
            ];
        }

        header('Content-Type: application/json');
        echo json_encode([
            'draw'            => intval($_POST['draw'] ?? 1),
            'recordsTotal'    => count($data),
            'recordsFiltered' => count($data),
            'data'            => $data,
        ], JSON_UNESCAPED_UNICODE);
        break;

    // ------------------------------------------------------------------
    // LISTAR POR CLIENTE — para el panel de cliente
    // ------------------------------------------------------------------
    case "listar_por_cliente":
        $id_cliente = (int)($_POST['id_cliente'] ?? 0);
        if (!$id_cliente) {
            header('Content-Type: application/json');
            echo json_encode(['draw' => 1, 'recordsTotal' => 0, 'recordsFiltered' => 0, 'data' => []], JSON_UNESCAPED_UNICODE);
            break;
        }

        $datos = $facturaAgrupada->get_facturas_agrupadas_by_cliente($id_cliente);
        $data  = [];

        foreach ($datos as $row) {
            $id = (int)$row['id_factura_agrupada'];
            $data[] = [
                'id_factura_agrupada'     => $id,
                'numero_factura_agrupada' => htmlspecialchars($row['numero_factura_agrupada'], ENT_QUOTES, 'UTF-8'),
                'fecha_factura_agrupada'  => $row['fecha_factura_agrupada'],
                'total_bruto'             => number_format((float)$row['total_bruto_agrupada'], 2, ',', '.') . ' €',
                'total_a_cobrar'          => number_format((float)$row['total_a_cobrar_agrupada'], 2, ',', '.') . ' €',
                'is_abono'                => (bool)$row['is_abono_agrupada'],
                'n_presupuestos'          => (int)($row['n_presupuestos'] ?? 0),
                'opciones'                => '<button class="btn btn-sm btn-outline-primary" onclick="descargarPDF(' . $id . ')">
                                                 <i class="fa fa-file-pdf"></i>
                                              </button>',
            ];
        }

        header('Content-Type: application/json');
        echo json_encode([
            'draw'            => intval($_POST['draw'] ?? 1),
            'recordsTotal'    => count($data),
            'recordsFiltered' => count($data),
            'data'            => $data,
        ], JSON_UNESCAPED_UNICODE);
        break;

    // ------------------------------------------------------------------
    // PRESUPUESTOS DISPONIBLES — para el wizard de selección
    // ------------------------------------------------------------------
    case "presupuestos_disponibles":
        $id_cliente = (int)($_POST['id_cliente'] ?? 0);
        if (!$id_cliente) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'ID de cliente no proporcionado.'], JSON_UNESCAPED_UNICODE);
            break;
        }

        $disponibles = $facturaAgrupada->get_presupuestos_disponibles($id_cliente);

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'data'    => $disponibles,
        ], JSON_UNESCAPED_UNICODE);
        break;

    // ------------------------------------------------------------------
    // DETECTAR EMPRESA POR PRESUPUESTOS SELECCIONADOS — para el paso 3 del wizard
    // ------------------------------------------------------------------
    case "detectar_empresa_para_seleccion":
        $ids_raw = $_POST['ids_presupuesto'] ?? [];
        if (!is_array($ids_raw)) {
            $ids_raw = json_decode($ids_raw, true) ?: [];
        }
        $ids = array_map('intval', array_filter($ids_raw));

        $empresa_bloqueada = $facturaAgrupada->detectar_empresa_real_presupuestos($ids);
        $empresas_reales   = $facturaAgrupada->get_empresas_reales_activas();

        header('Content-Type: application/json');
        echo json_encode([
            'success'           => true,
            'empresa_bloqueada' => $empresa_bloqueada,
            'empresas_reales'   => $empresas_reales,
        ], JSON_UNESCAPED_UNICODE);
        break;

    // ------------------------------------------------------------------
    // LISTAR EMPRESAS DE FACTURACIÓN — para el paso 2 del wizard (conservado)
    // ------------------------------------------------------------------
    case "listar_empresas_facturacion":
        $id_cliente = (int)($_POST['id_cliente'] ?? 0);
        if (!$id_cliente) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'ID de cliente no proporcionado.'], JSON_UNESCAPED_UNICODE);
            break;
        }

        $empresa_bloqueada  = $facturaAgrupada->detectar_empresa_real_cliente($id_cliente);
        $empresas_reales    = $facturaAgrupada->get_empresas_reales_activas();

        header('Content-Type: application/json');
        echo json_encode([
            'success'           => true,
            'empresa_bloqueada' => $empresa_bloqueada,
            'empresas_reales'   => $empresas_reales,
        ], JSON_UNESCAPED_UNICODE);
        break;

    // ------------------------------------------------------------------
    // VALIDAR SELECCIÓN — antes de crear
    // ------------------------------------------------------------------
    case "validar_seleccion":
        $ids_raw = $_POST['ids_presupuesto'] ?? [];
        if (!is_array($ids_raw)) {
            $ids_raw = json_decode($ids_raw, true) ?: [];
        }
        $ids             = array_map('intval', $ids_raw);
        $id_empresa_real = (int)($_POST['id_empresa_real'] ?? 0);

        $resultado = $facturaAgrupada->validar_presupuestos($ids, $id_empresa_real);

        // Normalizar 'valido' → 'success' para el JS
        $resultado['success'] = $resultado['valido'];

        header('Content-Type: application/json');
        echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
        break;

    // ------------------------------------------------------------------
    // CREAR FACTURA AGRUPADA
    // ------------------------------------------------------------------
    case "guardar":
        try {
            $ids_raw = $_POST['ids_presupuesto'] ?? [];
            if (!is_array($ids_raw)) {
                $ids_raw = json_decode($ids_raw, true) ?: [];
            }
            $ids_presupuesto = array_map('intval', $ids_raw);

            $id_empresa_real = (int)($_POST['id_empresa_real'] ?? 0);
            $id_cliente      = (int)($_POST['id_cliente']      ?? 0);
            $observaciones   = htmlspecialchars(trim($_POST['observaciones'] ?? ''), ENT_QUOTES, 'UTF-8');
            $fecha           = $_POST['fecha'] ?? date('Y-m-d');

            // Validar fecha
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
                $fecha = date('Y-m-d');
            }

            if (!$id_empresa_real || !$id_cliente || empty($ids_presupuesto)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Datos incompletos: empresa emisora, cliente y presupuestos son obligatorios.',
                ], JSON_UNESCAPED_UNICODE);
                break;
            }

            // Verificar que la empresa es real (no ficticia) — seguridad extra
            $codigo_empresa = $facturaAgrupada->get_codigo_empresa($id_empresa_real);
            if (!$codigo_empresa) {
                echo json_encode([
                    'success' => false,
                    'message' => 'La empresa seleccionada no es válida.',
                ], JSON_UNESCAPED_UNICODE);
                break;
            }

            // Validar antes de insertar
            $validacion = $facturaAgrupada->validar_presupuestos($ids_presupuesto, $id_empresa_real);
            if (!$validacion['valido']) {
                echo json_encode([
                    'success' => false,
                    'message' => implode(' | ', $validacion['errores']),
                ], JSON_UNESCAPED_UNICODE);
                break;
            }

            // codigo_empresa ya fue obtenido en la validación de empresa real

            $resultado = $facturaAgrupada->insert_factura_agrupada_transaccion([
                'ids_presupuesto' => $ids_presupuesto,
                'id_empresa'      => $id_empresa_real,
                'id_cliente'      => $id_cliente,
                'fecha'           => $fecha,
                'observaciones'   => $observaciones,
                'codigo_empresa'  => $codigo_empresa,
            ]);

            header('Content-Type: application/json');
            echo json_encode($resultado, JSON_UNESCAPED_UNICODE);

        } catch (Exception $e) {
            $registro->registrarActividad(
                'admin', 'factura_agrupada.php', 'guardar',
                "Error: " . $e->getMessage(), 'error'
            );
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Error al procesar la solicitud.',
            ], JSON_UNESCAPED_UNICODE);
        }
        break;

    // ------------------------------------------------------------------
    // MOSTRAR — datos de una factura agrupada (cabecera + presupuestos)
    // ------------------------------------------------------------------
    case "mostrar":
        $id = (int)($_POST['id_factura_agrupada'] ?? 0);
        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'ID no proporcionado.'], JSON_UNESCAPED_UNICODE);
            break;
        }

        $cabecera     = $facturaAgrupada->get_factura_agrupadaxid($id);
        $presupuestos = $facturaAgrupada->get_presupuestos_factura_agrupada($id);

        if ($cabecera) {
            header('Content-Type: application/json');
            echo json_encode([
                'success'      => true,
                'cabecera'     => $cabecera,
                'presupuestos' => $presupuestos,
            ], JSON_UNESCAPED_UNICODE);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Factura agrupada no encontrada.'], JSON_UNESCAPED_UNICODE);
        }
        break;

    // ------------------------------------------------------------------
    // DESACTIVAR — soft delete
    // ------------------------------------------------------------------
    case "desactivar":
        $id = (int)($_POST['id_factura_agrupada'] ?? 0);
        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'ID no proporcionado.'], JSON_UNESCAPED_UNICODE);
            break;
        }

        $resultado = $facturaAgrupada->delete_factura_agrupadaxid($id);

        header('Content-Type: application/json');
        echo json_encode([
            'success' => $resultado,
            'message' => $resultado ? 'Factura agrupada desactivada correctamente.' : 'Error al desactivar.',
        ], JSON_UNESCAPED_UNICODE);
        break;

    // ------------------------------------------------------------------
    // GENERAR ABONO
    // ------------------------------------------------------------------
    case "generar_abono":
        try {
            $id     = (int)($_POST['id_factura_agrupada'] ?? 0);
            $motivo = htmlspecialchars(trim($_POST['motivo'] ?? ''), ENT_QUOTES, 'UTF-8');

            if (!$id) {
                echo json_encode(['success' => false, 'message' => 'ID no proporcionado.'], JSON_UNESCAPED_UNICODE);
                break;
            }
            if (empty($motivo)) {
                echo json_encode(['success' => false, 'message' => 'El motivo del abono es obligatorio.'], JSON_UNESCAPED_UNICODE);
                break;
            }

            // Obtener empresa para el SP
            $cabecera = $facturaAgrupada->get_factura_agrupadaxid($id);
            if (!$cabecera) {
                echo json_encode(['success' => false, 'message' => 'Factura agrupada no encontrada.'], JSON_UNESCAPED_UNICODE);
                break;
            }

            $codigo_empresa = $facturaAgrupada->get_codigo_empresa((int)$cabecera['id_empresa']);
            if (!$codigo_empresa) {
                echo json_encode(['success' => false, 'message' => 'No se pudo obtener el código de empresa.'], JSON_UNESCAPED_UNICODE);
                break;
            }

            $resultado = $facturaAgrupada->insert_abono_agrupada($id, $motivo, $codigo_empresa);

            header('Content-Type: application/json');
            echo json_encode($resultado, JSON_UNESCAPED_UNICODE);

        } catch (Exception $e) {
            $registro->registrarActividad(
                'admin', 'factura_agrupada.php', 'generar_abono',
                "Error: " . $e->getMessage(), 'error'
            );
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Error al generar el abono.',
            ], JSON_UNESCAPED_UNICODE);
        }
        break;

    // ------------------------------------------------------------------
    // DESCARGAR PDF — redirige al controlador de impresión
    // ------------------------------------------------------------------
    // LISTAR POR PRESUPUESTO — sección Documentos del formulario de presupuesto
    // ------------------------------------------------------------------
    case "listar_por_presupuesto":
        $id_presupuesto = (int)($_POST['id_presupuesto'] ?? 0);
        if (!$id_presupuesto) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'id_presupuesto requerido.'], JSON_UNESCAPED_UNICODE);
            break;
        }

        $datos = $facturaAgrupada->get_facturas_agrupadas_por_presupuesto($id_presupuesto);
        $data  = [];

        foreach ($datos as $row) {
            $es_abono     = (bool)($row['is_abono_agrupada'] ?? false);
            $esta_abonada = !$es_abono
                            && !(bool)($row['activo_factura_agrupada'] ?? true)
                            && !empty($row['id_abono_asociado']);

            $num     = htmlspecialchars($row['numero_factura_agrupada'], ENT_QUOTES, 'UTF-8');
            $empresa = htmlspecialchars($row['nombre_empresa'],          ENT_QUOTES, 'UTF-8');
            $id      = (int)$row['id_factura_agrupada'];
            $fecha   = $row['fecha_factura_agrupada']
                        ? date('d/m/Y', strtotime($row['fecha_factura_agrupada']))
                        : '—';

            // Badge de estado
            if ($es_abono) {
                $badge = '<span class="badge bg-warning text-dark">ABONO</span>';
            } elseif ($esta_abonada) {
                $badge = '<span class="badge bg-danger">ABONADA</span>';
            } else {
                $badge = '<span class="badge bg-success">FACTURA</span>';
            }

            // Botón PDF
            $btn_pdf = '<a href="../../controller/impresion_factura_agrupada.php?op=generar&id=' . $id . '"
                           target="_blank"
                           class="btn btn-info btn-sm"
                           title="Ver PDF"><i class="fa fa-file-pdf"></i></a>';

            $data[] = [
                'id_factura_agrupada'     => $id,
                'numero_factura_agrupada' => $num,
                'fecha'                   => $fecha,
                'empresa'                 => $empresa,
                'badge_estado'            => $badge,
                'btn_pdf'                 => $btn_pdf,
                'numero_abono_asociado'   => $row['numero_abono_asociado'] ?? null,
            ];
        }

        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'data' => $data], JSON_UNESCAPED_UNICODE);
        break;

    // ------------------------------------------------------------------
    case "descargar_pdf":
        $id = (int)($_GET['id'] ?? $_POST['id_factura_agrupada'] ?? 0);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'url'     => '../../controller/impresion_factura_agrupada.php?op=descargar&id=' . $id,
        ], JSON_UNESCAPED_UNICODE);
        break;

    default:
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Operación no reconocida.'], JSON_UNESCAPED_UNICODE);
        break;
}
?>
