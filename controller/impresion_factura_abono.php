

<?php
/**
 * impresion_factura_abono.php
 *
 * Genera FACTURA DE ABONO (factura rectificativa).
 * Tipo DB: factura_rectificativa
 * SP tipo: abono
 * Título PDF: "FACTURA DE ABONO" (en rojo)
 * Referencia: "ABONA LA FACTURA: Nº del documento origen"
 * Importes: NEGATIVOS y en rojo
 * Total label: "TOTAL A DEVOLVER"
 *
 * op=generar (POST)
 *   - id_presupuesto        (requerido)
 *   - id_empresa            (requerido — empresa real emisora)
 *   - id_documento_origen   (requerido — documento que se abona)
 *   - motivo_abono          (requerido)
 *   - numero_version        (opcional)
 *
 * op=descargar (GET/POST)
 *   - id_documento_ppto     (requerido)
 *
 * Devuelve JSON: { success, id_documento_ppto, numero_documento, url_pdf }
 * El PDF se guarda en public/documentos/abonos/
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

while (ob_get_level()) {
    ob_end_clean();
}
ob_start();

error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

require_once __DIR__ . "/../config/conexion.php";
require_once __DIR__ . "/../config/funciones.php";
require_once __DIR__ . "/../models/DocumentoPresupuesto.php";
require_once __DIR__ . "/../models/ImpresionPresupuesto.php";
require_once __DIR__ . "/../models/Empresas.php";
require_once __DIR__ . "/../models/PagoPresupuesto.php";
require_once __DIR__ . "/../vendor/tcpdf/tcpdf.php";

// ══════════════════════════════════════════════════════════════════
// CLASE PDF — Factura de Abono — mismo estilo visual que anticipo
// Color rojo (192,57,43) · Título: FACTURA DE ABONO
// ══════════════════════════════════════════════════════════════════
class MYPDF_ABONO extends TCPDF
{
    public $datos_empresa     = [];
    public $datos_presupuesto = [];
    public $numero_documento  = '';
    public $fecha_documento   = '';
    public $numero_doc_origen   = '';
    public $fecha_doc_origen    = '';
    public $texto_pie_empresa   = '';
    public $mostrar_logo      = false;
    public $path_logo         = '';
    public $y_header_bottom   = 78;

    // RGB rojo
    private $cr = 192;
    private $cg = 57;
    private $cb = 43;

    public function Header(): void
    {
        $cr = $this->cr; $cg = $this->cg; $cb = $this->cb;
        $e  = $this->datos_empresa;
        $p  = $this->datos_presupuesto;

        $y_start = 10;

        // ── TÍTULO (arriba derecha, rojo) ─────────────────────────────
        $this->SetY($y_start);
        $this->SetFont('helvetica', 'B', 16);
        $this->SetTextColor($cr, $cg, $cb);
        $this->Cell(0, 8, 'FACTURA DE ABONO', 0, 1, 'R');
        $this->Ln(2);
        $y_start = $this->GetY();

        // ── LOGO ──────────────────────────────────────────────────────
        if ($this->mostrar_logo && !empty($this->path_logo) && file_exists($this->path_logo)) {
            $this->Image($this->path_logo, 8, $y_start, 35, 0, '', '', '', false, 300, '', false, false, 0);
            $logo_height = 18;
        } else {
            $logo_height = 0;
        }

        $y_empresa = $y_start + $logo_height + 1;
        $this->SetY($y_empresa);
        $this->SetX(8);

        // ── EMPRESA (columna izqda) ───────────────────────────────────
        $this->SetFont('helvetica', 'B', 9);
        $this->SetTextColor(44, 62, 80);
        $this->Cell(95, 3.5, $e['nombre_comercial_empresa'] ?? ($e['nombre_empresa'] ?? ''), 0, 1, 'L');

        // CIF en rojo
        $nif_emp = $e['nif_empresa'] ?? '';
        if ($nif_emp && substr($nif_emp, -4) !== '0000') {
            $this->SetX(8);
            $this->SetFont('helvetica', 'B', 8);
            $this->SetTextColor(231, 76, 60);
            $this->Cell(95, 2.5, 'CIF: ' . $nif_emp, 0, 1, 'L');
        }

        $this->SetFont('helvetica', '', 7.5);
        $this->SetTextColor(52, 73, 94);
        if (!empty($e['direccion_fiscal_empresa'])) {
            $this->SetX(8);
            $this->Cell(95, 3, $e['direccion_fiscal_empresa'], 0, 1, 'L');
        }
        $cp_pob_prov = trim(
            ($e['cp_fiscal_empresa'] ?? '') . ' ' .
            ($e['poblacion_fiscal_empresa'] ?? '') .
            (!empty($e['provincia_fiscal_empresa']) ? ' (' . $e['provincia_fiscal_empresa'] . ')' : '')
        );
        if ($cp_pob_prov) {
            $this->SetX(8);
            $this->Cell(95, 3, $cp_pob_prov, 0, 1, 'L');
        }
        $tel_str = '';
        if (!empty($e['telefono_empresa'])) {
            $tel_str = 'Tel: ' . $e['telefono_empresa'];
        }
        if (!empty($e['movil_empresa'])) {
            $tel_str .= ($tel_str ? ' | ' : '') . $e['movil_empresa'];
        }
        if ($tel_str) {
            $this->SetX(8);
            $this->Cell(95, 2.5, $tel_str, 0, 1, 'L');
        }
        if (!empty($e['email_empresa'])) {
            $this->SetX(8);
            $this->Cell(95, 2.5, $e['email_empresa'], 0, 1, 'L');
        }
        if (!empty($e['web_empresa'])) {
            $this->SetX(8);
            $this->Cell(95, 2.5, $e['web_empresa'], 0, 1, 'L');
        }

        // ── CAJA INFO izquierda (rojo fill, 3 líneas) ─────────────────
        $y_info = $this->GetY() + 1;
        $this->SetFillColor($cr, $cg, $cb);
        $this->SetTextColor(255, 255, 255);
        $this->SetFont('helvetica', 'B', 8.5);
        $this->SetXY(8, $y_info);
        $this->Cell(95, 20, '', 0, 0, 'L', true);
        // Línea 1: Nº | Fecha
        $this->SetXY(9, $y_info + 1);
        $this->Cell(93, 3, 'Nº: ' . $this->numero_documento . '  |  F: ' . $this->fecha_documento, 0, 1, 'L');
        // Línea 2: Ref. presupuesto
        $num_ppto = $p['numero_presupuesto'] ?? '';
        if ($num_ppto) {
            $this->SetXY(9, $y_info + 5);
            $this->SetFont('helvetica', '', 7.5);
            $this->Cell(93, 3, 'Ref. Presupuesto: ' . $num_ppto, 0, 1, 'L');
        }
        // Línea 3: ABONA LA FACTURA
        $this->SetXY(9, $y_info + 10);
        $this->SetFont('helvetica', '', 7.5);
        $this->Cell(93, 3, 'ABONA LA FACTURA: ' . $this->numero_doc_origen, 0, 1, 'L');
        // Línea 4: Fecha factura original
        if (!empty($this->fecha_doc_origen)) {
            $this->SetXY(9, $y_info + 14);
            $this->SetFont('helvetica', '', 7.5);
            $this->Cell(93, 3, 'Fecha fac. original: ' . $this->fecha_doc_origen, 0, 1, 'L');
        }
        $this->SetTextColor(0, 0, 0);

        // ── CAJA CLIENTE derecha (borde rojo, fondo gris claro) ───────
        $col2_x = 108;
        $col2_w = 94;
        $box_y  = $y_start;
        $cli_h  = 26;
        if (!empty($p['nombre_contacto_cliente'])) {
            $cli_h += 16;
        }

        $this->SetFillColor(248, 249, 250);
        $this->SetDrawColor($cr, $cg, $cb);
        $this->SetLineWidth(0.5);
        $this->Rect($col2_x, $box_y, $col2_w, $cli_h, 'DF');
        $this->SetLineWidth(0.2);

        $this->SetXY($col2_x + 2, $box_y + 1.5);
        $this->SetFont('helvetica', 'B', 9);
        $this->SetTextColor(44, 62, 80);
        $this->Cell($col2_w - 4, 3.5, 'CLIENTE', 0, 1, 'L');
        $this->SetDrawColor($cr, $cg, $cb);
        $this->Line($col2_x + 2, $box_y + 5.5, $col2_x + $col2_w - 2, $box_y + 5.5);

        $nombre_cli = trim(($p['nombre_cliente'] ?? '') . ' ' . ($p['apellido_cliente'] ?? ''));
        $this->SetXY($col2_x + 2, $box_y + 6.5);
        $this->SetFont('helvetica', 'B', 8.5);
        $this->SetTextColor(44, 62, 80);
        $this->Cell($col2_w - 4, 3.5, $nombre_cli, 0, 1, 'L');

        $y_cd = $box_y + 11;
        $this->SetFont('helvetica', '', 7.5);
        $this->SetTextColor(70, 70, 70);
        $nif_cli = $p['nif_cliente'] ?? '';
        if ($nif_cli) {
            $this->SetXY($col2_x + 2, $y_cd);
            $this->SetFont('helvetica', '', 7.5);
            $this->Cell(14, 3.5, 'NIF/CIF:', 0, 0, 'L');
            $this->SetFont('helvetica', 'B', 7.5);
            $this->Cell($col2_w - 18, 3.5, $nif_cli, 0, 1, 'L');
            $y_cd += 4;
        }
        $this->SetFont('helvetica', '', 7.5);
        $dir_cli = trim(
            ($p['direccion_cliente'] ?? '') . ' ' .
            ($p['cp_cliente'] ?? '') . ' ' .
            ($p['poblacion_cliente'] ?? '')
        );
        if ($dir_cli) {
            $this->SetXY($col2_x + 2, $y_cd);
            $this->Cell($col2_w - 4, 3.5, $dir_cli, 0, 1, 'L');
            $y_cd += 4;
        }
        if (!empty($p['email_cliente'])) {
            $this->SetXY($col2_x + 2, $y_cd);
            $this->Cell($col2_w - 4, 3.5, $p['email_cliente'], 0, 1, 'L');
            $y_cd += 4;
        }
        if (!empty($p['telefono_cliente'])) {
            $this->SetXY($col2_x + 2, $y_cd);
            $this->Cell($col2_w - 4, 3.5, $p['telefono_cliente'], 0, 1, 'L');
        }
        if (!empty($p['nombre_contacto_cliente'])) {
            $y_cd += 5;
            $this->SetXY($col2_x + 2, $y_cd);
            $this->SetFont('helvetica', 'B', 8);
            $this->SetTextColor($cr, $cg, $cb);
            $this->Cell($col2_w - 4, 3, 'A la atención de:', 0, 1, 'L');
            $y_cd += 3.5;
            $nombre_cont = trim(
                ($p['nombre_contacto_cliente'] ?? '') . ' ' .
                ($p['apellidos_contacto_cliente'] ?? '')
            );
            $this->SetFont('helvetica', '', 7.5);
            $this->SetTextColor(52, 73, 94);
            $this->SetXY($col2_x + 2, $y_cd);
            $this->Cell(15, 2.5, 'Nombre:', 0, 0, 'L');
            $this->SetFont('helvetica', 'B', 7.5);
            $this->Cell($col2_w - 19, 2.5, $nombre_cont, 0, 1, 'L');
            $y_cd += 3;
            if (!empty($p['telefono_contacto_cliente'])) {
                $this->SetFont('helvetica', '', 7.5);
                $this->SetXY($col2_x + 2, $y_cd);
                $this->Cell(15, 2.5, 'Telefono:', 0, 0, 'L');
                $this->Cell($col2_w - 19, 2.5, $p['telefono_contacto_cliente'], 0, 1, 'L');
                $y_cd += 3;
            }
            if (!empty($p['email_contacto_cliente'])) {
                $this->SetFont('helvetica', '', 7.5);
                $this->SetXY($col2_x + 2, $y_cd);
                $this->Cell(15, 2.5, 'Email:', 0, 0, 'L');
                $this->Cell($col2_w - 19, 2.5, $p['email_contacto_cliente'], 0, 1, 'L');
            }
        }

        $this->y_header_bottom = max($y_info + 17, $box_y + $cli_h + 5);
        $this->SetDrawColor(200, 200, 200);
        $this->SetTextColor(0, 0, 0);
        $this->SetLineWidth(0.2);
    }

    public function Footer(): void
    {
        $this->SetY(-20);
        $this->SetDrawColor(44, 62, 80);
        $this->SetLineWidth(0.3);
        $this->Line(8, $this->GetY(), 202, $this->GetY());
        if (!empty($this->texto_pie_empresa)) {
            $this->SetFont('helvetica', '', 7);
            $this->SetTextColor(100, 100, 100);
            $this->MultiCell(0, 4, $this->texto_pie_empresa, 0, 'C');
        }
        $this->SetY(-10);
        $this->SetFont('helvetica', 'I', 8);
        $this->SetTextColor(100, 100, 100);
        $this->Cell(0, 5, 'Página ' . $this->getAliasNumPage() . ' de ' . $this->getAliasNbPages(), 0, 0, 'C');
        $this->SetTextColor(0, 0, 0);
    }
}

// ════════════════════════════════════════════════════════════════
header('Content-Type: application/json; charset=utf-8');

$registro  = new RegistroActividad();
$docModel  = new DocumentoPresupuesto();
$impresion = new ImpresionPresupuesto();
$empModel  = new Empresas();

$op = $_GET['op'] ?? '';

switch ($op) {

    // ══════════════════════════════════════════════════════════════
    // GENERAR
    // ══════════════════════════════════════════════════════════════
    case "generar":
        $id_presupuesto      = (int)($_POST['id_presupuesto']      ?? 0);
        $id_empresa          = (int)($_POST['id_empresa']           ?? 0);
        $id_documento_origen = (int)($_POST['id_documento_origen']  ?? 0);
        $motivo_abono        = htmlspecialchars(trim($_POST['motivo_abono'] ?? ''), ENT_QUOTES, 'UTF-8');
        $numero_version      = !empty($_POST['numero_version']) ? (int)$_POST['numero_version'] : null;

        if (!$id_presupuesto || !$id_empresa || !$id_documento_origen) {
            echo json_encode(['success' => false, 'message' => 'Faltan campos obligatorios (id_presupuesto, id_empresa, id_documento_origen)'], JSON_UNESCAPED_UNICODE);
            break;
        }

        if (empty($motivo_abono)) {
            echo json_encode(['success' => false, 'message' => 'El motivo del abono es obligatorio'], JSON_UNESCAPED_UNICODE);
            break;
        }

        try {
            // Validar empresa real
            if (!$docModel->verificar_empresa_real($id_empresa)) {
                echo json_encode(['success' => false, 'message' => 'La empresa seleccionada no es una empresa real válida'], JSON_UNESCAPED_UNICODE);
                break;
            }

            // Obtener documento origen (que se abona)
            $doc_origen = $docModel->get_documentoxid($id_documento_origen);
            if (!$doc_origen) {
                echo json_encode(['success' => false, 'message' => "Documento origen ID $id_documento_origen no encontrado"], JSON_UNESCAPED_UNICODE);
                break;
            }

            $numero_doc_origen = $doc_origen['numero_documento_ppto'] ?? "DOC-{$id_documento_origen}";

            // Verificar si ya existe abono para este documento
            $resultado_verif = $docModel->verificar_puede_abonar($id_documento_origen);
            if (!$resultado_verif['puede']) {
                echo json_encode(['success' => false, 'message' => $resultado_verif['motivo'] ?? 'Este documento ya ha sido abonado'], JSON_UNESCAPED_UNICODE);
                break;
            }

            // Obtener datos presupuesto
            $datos_ppto = $impresion->get_datos_cabecera($id_presupuesto, $numero_version);
            if (!$datos_ppto) {
                echo json_encode(['success' => false, 'message' => "No se encontraron datos del presupuesto ID: $id_presupuesto"], JSON_UNESCAPED_UNICODE);
                break;
            }

            // Obtener datos empresa emisora
            $datos_empresa = $empModel->get_empresaxid($id_empresa);
            if (!$datos_empresa) {
                echo json_encode(['success' => false, 'message' => "No se encontraron datos de la empresa ID: $id_empresa"], JSON_UNESCAPED_UNICODE);
                break;
            }

            // Usar EXACTAMENTE los importes del documento origen (no recalcular desde líneas)
            $subtotal_base = (float)($doc_origen['subtotal_documento_ppto'] ?? 0);
            $total_iva     = (float)($doc_origen['total_iva_documento_ppto'] ?? 0);
            $total_con_iva = (float)($doc_origen['total_documento_ppto']     ?? 0);
            $importe_abono = -abs($total_con_iva);

            // Inferir desglose IVA a partir de los totales del documento origen
            $desglose_iva = [];
            if ($subtotal_base > 0) {
                $pct_inferido = (int)round(($total_iva / $subtotal_base) * 100);
            } else {
                $pct_inferido = 21; // fallback
            }
            $desglose_iva[$pct_inferido] = [
                'base'  => $subtotal_base,
                'cuota' => $total_iva,
            ];

            // Línea para el PDF — misma descripción que la factura original
            $lineas = [[
                'descripcion_linea_ppto'     => 'Entrega a cuenta confirmación presupuesto ' . ($datos_ppto['numero_presupuesto'] ?? ''),
                'cantidad_linea_ppto'        => -1,
                'precio_unitario_linea_ppto' => $subtotal_base,
                'descuento_linea_ppto'       => 0,
                'base_imponible'             => $subtotal_base,
                'porcentaje_iva_linea_ppto'  => $pct_inferido,
            ]];

            // Crear registro documento
            $datos_insert = [
                'id_presupuesto'               => $id_presupuesto,
                'tipo_documento_ppto'          => 'factura_rectificativa',
                'id_empresa'                   => $id_empresa,
                'id_documento_origen'          => $id_documento_origen,
                'numero_version'               => $numero_version,
                'motivo_abono_documento_ppto'  => $motivo_abono,
                'observaciones_documento_ppto' => $motivo_abono,
                'importe_documento_ppto'       => $importe_abono,
            ];

            $id_doc = $docModel->insert_documento($datos_insert);
            if (!$id_doc) {
                echo json_encode(['success' => false, 'message' => 'Error al crear el registro de documento abono'], JSON_UNESCAPED_UNICODE);
                break;
            }

            $doc             = $docModel->get_documentoxid($id_doc);
            $numero_documento = $doc['numero_documento_ppto'] ?? "R-{$id_doc}";

            // Logo empresa
            $mostrar_logo = false;
            $path_logo    = '';
            if (!empty($datos_empresa['logotipo_empresa'])) {
                $logo_name = ltrim($datos_empresa['logotipo_empresa'], '/');
                if (strpos($logo_name, 'public/') === 0) {
                    $logo_name = substr($logo_name, 7);
                }
                $path_logo_abs = __DIR__ . '/../public/' . $logo_name;
                if (file_exists($path_logo_abs)) {
                    $mostrar_logo = true;
                    $path_logo    = realpath($path_logo_abs);
                }
            }

            // Actualizar importes en BD (negativos)
            $docModel->actualizar_importes($id_doc, -abs($subtotal_base), -abs($total_iva), $importe_abono);

            // Formatear fecha del documento origen
            $fecha_doc_origen_raw = $doc_origen['fecha_emision_documento_ppto'] ?? '';
            $fecha_doc_origen = '';
            if ($fecha_doc_origen_raw) {
                $parts = explode('-', $fecha_doc_origen_raw);
                $fecha_doc_origen = count($parts) === 3
                    ? $parts[2] . '/' . $parts[1] . '/' . $parts[0]
                    : $fecha_doc_origen_raw;
            }

            // Generar PDF
            $pdf = _generar_pdf_abono(
                $datos_ppto, $datos_empresa, $numero_documento,
                $numero_doc_origen, $motivo_abono, $fecha_doc_origen,
                $lineas, $desglose_iva, $subtotal_base, $total_iva, $total_con_iva,
                $mostrar_logo, $path_logo
            );

            // Guardar a disco
            $dir_guardado = __DIR__ . '/../public/documentos/abonos/';
            if (!is_dir($dir_guardado)) {
                mkdir($dir_guardado, 0755, true);
            }
            $nombre_archivo = preg_replace('/[^A-Za-z0-9_\-]/', '_', $numero_documento) . '.pdf';
            $ruta_absoluta  = $dir_guardado . $nombre_archivo;
            $ruta_relativa  = 'public/documentos/abonos/' . $nombre_archivo;

            $pdf_string = $pdf->Output($nombre_archivo, 'S');
            file_put_contents($ruta_absoluta, $pdf_string);
            $tamano = filesize($ruta_absoluta);

            $docModel->actualizar_ruta_pdf($id_doc, $ruta_relativa, $tamano);

            // ── Auto-anular el pago vinculado al documento origen ────────
            $pago_anulado    = false;
            $id_pago_anulado = null;
            $pagoModel = new PagoPresupuesto();
            $pago_vinculado  = $pagoModel->get_pago_vinculado_documento($id_documento_origen);
            if ($pago_vinculado && $pago_vinculado['estado_pago_ppto'] !== 'anulado') {
                $pago_anulado    = $pagoModel->anular_pago_por_abono((int)$pago_vinculado['id_pago_ppto']);
                $id_pago_anulado = (int)$pago_vinculado['id_pago_ppto'];
                $registro->registrarActividad(
                    'admin', 'impresion_factura_abono.php', 'generar',
                    "Pago ID $id_pago_anulado anulado automáticamente al generar abono $numero_documento",
                    $pago_anulado ? 'info' : 'warning'
                );
            }

            $registro->registrarActividad(
                'admin', 'impresion_factura_abono.php', 'generar',
                "Abono $numero_documento generado. Abona: $numero_doc_origen. Doc ID: $id_doc", 'info'
            );

            ob_end_clean();
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'success'           => true,
                'id_documento_ppto' => $id_doc,
                'numero_documento'  => $numero_documento,
                'url_pdf'           => $ruta_relativa,
                'pago_anulado'      => $pago_anulado,
                'id_pago_anulado'   => $id_pago_anulado,
            ], JSON_UNESCAPED_UNICODE);

        } catch (Exception $e) {
            $registro->registrarActividad(
                'admin', 'impresion_factura_abono.php', 'generar',
                "Error: " . $e->getMessage(), 'error'
            );
            ob_end_clean();
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false, 'message' => 'Error al generar la factura de abono: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
        }
        break;

    // ══════════════════════════════════════════════════════════════
    // DESCARGAR
    // ══════════════════════════════════════════════════════════════
    case "descargar":
        $id_doc = (int)($_POST['id_documento_ppto'] ?? $_GET['id_documento_ppto'] ?? 0);
        if (!$id_doc) {
            echo json_encode(['success' => false, 'message' => 'Falta id_documento_ppto'], JSON_UNESCAPED_UNICODE);
            break;
        }

        $doc = $docModel->get_documentoxid($id_doc);
        if (!$doc || empty($doc['ruta_pdf_documento_ppto'])) {
            echo json_encode(['success' => false, 'message' => 'Documento no encontrado o sin PDF'], JSON_UNESCAPED_UNICODE);
            break;
        }

        $ruta_abs = __DIR__ . '/../' . $doc['ruta_pdf_documento_ppto'];
        if (!file_exists($ruta_abs)) {
            echo json_encode(['success' => false, 'message' => 'Archivo PDF no encontrado en disco'], JSON_UNESCAPED_UNICODE);
            break;
        }

        ob_end_clean();
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="' . basename($ruta_abs) . '"');
        header('Content-Length: ' . filesize($ruta_abs));
        readfile($ruta_abs);
        exit;

    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => "Operación '$op' no reconocida"], JSON_UNESCAPED_UNICODE);
        break;
}

// ══════════════════════════════════════════════════════════════════
// FUNCIÓN: Generar PDF abono
// ══════════════════════════════════════════════════════════════════
function _generar_pdf_abono(
    array $datos_ppto, array $datos_empresa, string $numero_documento,
    string $numero_doc_origen, string $motivo_abono, string $fecha_doc_origen,
    array $lineas, array $desglose_iva, float $subtotal_base, float $total_iva, float $total_con_iva,
    bool $mostrar_logo, string $path_logo
): MYPDF_ABONO {

    $fecha_hoy = date('d/m/Y');

    // ── Instanciar MYPDF_ABONO ────────────────────────────────────────
    $pdf = new MYPDF_ABONO('P', 'mm', 'A4', true, 'UTF-8', false);
    $pdf->SetCreator('MDR ERP');
    $pdf->SetAuthor($datos_empresa['nombre_comercial_empresa'] ?? ($datos_empresa['nombre_empresa'] ?? 'MDR'));
    $pdf->SetTitle('Factura de Abono ' . $numero_documento);

    $pdf->datos_empresa     = $datos_empresa;
    $pdf->datos_presupuesto = $datos_ppto;
    $pdf->numero_documento  = $numero_documento;
    $pdf->fecha_documento   = $fecha_hoy;
    $pdf->numero_doc_origen = $numero_doc_origen;
    $pdf->fecha_doc_origen   = $fecha_doc_origen;
    $pdf->texto_pie_empresa  = $datos_empresa['texto_pie_factura_empresa'] ?? '';
    $pdf->mostrar_logo      = $mostrar_logo;
    $pdf->path_logo         = $path_logo;

    $pdf->setPrintHeader(true);
    $pdf->setPrintFooter(true);
    $pdf->SetMargins(8, 78, 8);
    $pdf->SetHeaderMargin(5);
    $pdf->SetFooterMargin(15);
    $pdf->SetAutoPageBreak(true, 25);
    $pdf->AddPage();
    $pdf->SetY($pdf->y_header_bottom);

    // ── TABLA (una sola línea) ────────────────────────────────────────
    // Anchos: Desc=140, Cant=12, PUnit=18, Importe=24 → total=194mm
    $w_desc  = 140;
    $w_cant  = 12;
    $w_punit = 18;
    $w_imp   = 24;
    $col_h   = 6;

    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->SetFillColor(240, 240, 240);
    $pdf->SetDrawColor(200, 200, 200);
    $pdf->SetTextColor(44, 62, 80);
    $pdf->Cell($w_desc,  $col_h, 'Descripción', 1, 0, 'L', true);
    $pdf->Cell($w_cant,  $col_h, 'Cant.',        1, 0, 'C', true);
    $pdf->Cell($w_punit, $col_h, 'P.Unit. €',    1, 0, 'C', true);
    $pdf->Cell($w_imp,   $col_h, 'Importe €',    1, 1, 'C', true);

    $pdf->SetFont('helvetica', '', 8);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFillColor(255, 255, 255);

    foreach ($lineas as $l) {
        $desc   = $l['descripcion_linea_ppto']     ?? '';
        $cant   = floatval($l['cantidad_linea_ppto']        ?? 0);
        $precio = floatval($l['precio_unitario_linea_ppto'] ?? 0);
        $base   = -abs(floatval($l['base_imponible']         ?? 0));

        $pdf->Cell($w_desc,  $col_h, $desc,                                        1, 0, 'L');
        $pdf->SetTextColor(192, 57, 43);
        $pdf->Cell($w_cant,  $col_h, number_format($cant, 0),                      1, 0, 'C');
        $pdf->Cell($w_punit, $col_h, number_format($precio, 2, ',', '.'),          1, 0, 'R');
        $pdf->Cell($w_imp,   $col_h, number_format($base, 2, ',', '.') . ' €',    1, 1, 'R');
        $pdf->SetTextColor(0, 0, 0);
    }

    // ── TOTALES ───────────────────────────────────────────────────────
    $w_spacer = 144;
    $w_label  = 30;
    $w_value  = 20;

    $pdf->Ln(3);
    $pdf->SetFont('helvetica', '', 8.5);
    $pdf->SetFillColor(248, 249, 250);
    $pdf->SetDrawColor(220, 220, 220);
    $pdf->SetTextColor(44, 62, 80);

    // Base imponible
    $pdf->Cell($w_spacer, 6, '', 0, 0);
    $pdf->Cell($w_label,  6, 'Base imponible:', 'LTB', 0, 'R', true);
    $pdf->SetFont('helvetica', 'B', 8.5);
    $pdf->SetTextColor(192, 57, 43);
    $pdf->Cell($w_value,  6, '-' . number_format($subtotal_base, 2, ',', '.') . ' €', 'RTB', 1, 'R', true);

    // Por tramo de IVA
    foreach ($desglose_iva as $pct => $v) {
        $pdf->SetFont('helvetica', '', 8);
        $pdf->SetTextColor(52, 73, 94);
        $pdf->Cell($w_spacer, 5, '', 0, 0);
        $pdf->Cell($w_label,  5, "IVA {$pct}%:", 0, 0, 'R');
        $pdf->SetTextColor(192, 57, 43);
        $pdf->Cell($w_value,  5, '-' . number_format($v['cuota'], 2, ',', '.') . ' €', 0, 1, 'R');
    }

    // TOTAL (fondo rojo)
    $pdf->SetFont('helvetica', 'B', 9);
    $pdf->SetFillColor(192, 57, 43);
    $pdf->SetDrawColor(160, 40, 30);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->Cell($w_spacer, 10, '', 0, 0);
    $pdf->Cell($w_label,  10, 'TOTAL:', 1, 0, 'R', true);
    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Cell($w_value,  10, '-' . number_format($total_con_iva, 2, ',', '.') . ' €', 1, 1, 'R', true);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetDrawColor(200, 200, 200);
    $pdf->SetLineWidth(0.2);
    $pdf->Ln(4);

    $pdf->SetAutoPageBreak(true, 25);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetLineWidth(0.2);

    // ── MOTIVO DEL ABONO ─────────────────────────────────────────────
    if (!empty($motivo_abono)) {
        $pdf->Ln(2);
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->SetTextColor(44, 62, 80);
        $pdf->Cell(0, 5, 'Motivo del abono:', 0, 1, 'L');
        $pdf->Ln(1);
        $pdf->SetFont('helvetica', '', 8);
        $pdf->SetFillColor(255, 245, 245);
        $pdf->SetDrawColor(192, 57, 43);
        $pdf->MultiCell(0, 5, $motivo_abono, 1, 'L', true);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetDrawColor(200, 200, 200);
    }

    return $pdf;
}
