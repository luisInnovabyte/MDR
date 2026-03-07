<?php
/**
 * impresion_factura_anticipo.php
 *
 * Genera FACTURA oficial de anticipo (una sola línea).
 * Tipo DB: factura_anticipo
 * SP tipo: factura
 * Título PDF: "FACTURA"
 * Línea: "A cuenta del presupuesto [NUMERO_PRESUPUESTO]"
 * Total = importe anticipo con IVA desglosado
 *
 * op=generar (POST)
 *   - id_presupuesto    (requerido)
 *   - id_empresa        (requerido — empresa real emisora)
 *   - id_pago_ppto      (requerido — pago origen)
 *   - importe_total     (requerido — importe factura con IVA)
 *   - porcentaje_iva    (opcional — default 21)
 *   - numero_version    (opcional)
 *
 * op=descargar (GET/POST)
 *   - id_documento_ppto (requerido)
 *
 * Devuelve JSON: { success, id_documento_ppto, numero_documento, url_pdf }
 * El PDF se guarda en public/documentos/anticipos/
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
// CLASE PDF — mismo estilo visual que impresionpresupuesto_m2_pdf_es.php
// Color verde (39,174,96) · Título: FACTURA
// ══════════════════════════════════════════════════════════════════

class MYPDF_ANTICIPO extends TCPDF
{
    public $datos_empresa      = [];
    public $datos_presupuesto  = [];
    public $numero_documento   = '';
    public $fecha_documento    = '';
    public $texto_pie_empresa  = '';
    public $mostrar_logo       = false;
    public $path_logo          = '';
    public $idioma             = 'es';
    public $y_header_bottom    = 72;

    // Verde anticipo
    private $cr = 39;
    private $cg = 174;
    private $cb = 96;

    public function Header()
    {
        $y_start = 10;

        // TÍTULO
        $this->SetY($y_start);
        $this->SetFont('helvetica', 'B', 16);
        $this->SetTextColor($this->cr, $this->cg, $this->cb);
        $this->Cell(0, 8, $this->idioma === 'en' ? 'INVOICE' : 'FACTURA', 0, 1, 'R');
        $this->Ln(2);
        $y_start = $this->GetY();

        // LOGO
        if ($this->mostrar_logo && !empty($this->path_logo) && file_exists($this->path_logo)) {
            $this->Image($this->path_logo, 8, $y_start, 35, 0, '', '', '', false, 300, '', false, false, 0);
            $logo_height = 18;
        } else {
            $logo_height = 0;
        }

        $y_empresa = $y_start + $logo_height + 1;
        $this->SetY($y_empresa);
        $this->SetX(8);

        // Nombre comercial
        $this->SetFont('helvetica', 'B', 9);
        $this->SetTextColor(44, 62, 80);
        $this->Cell(95, 3.5, $this->datos_empresa['nombre_comercial_empresa'] ?? ($this->datos_empresa['nombre_empresa'] ?? ''), 0, 1, 'L');

        // CIF en rojo
        $nif_emp = $this->datos_empresa['nif_empresa'] ?? '';
        if ($nif_emp && substr($nif_emp, -4) !== '0000') {
            $this->SetX(8);
            $this->SetFont('helvetica', 'B', 8);
            $this->SetTextColor(231, 76, 60);
            $this->Cell(95, 2.5, 'CIF: ' . $nif_emp, 0, 1, 'L');
        }

        $this->SetFont('helvetica', '', 7.5);
        $this->SetTextColor(52, 73, 94);

        if (!empty($this->datos_empresa['direccion_fiscal_empresa'])) {
            $this->SetX(8);
            $this->Cell(95, 3, $this->datos_empresa['direccion_fiscal_empresa'], 0, 1, 'L');
        }
        $cp_pob_prov = trim(
            ($this->datos_empresa['cp_fiscal_empresa'] ?? '') . ' ' .
            ($this->datos_empresa['poblacion_fiscal_empresa'] ?? '') .
            (!empty($this->datos_empresa['provincia_fiscal_empresa'])
                ? ' (' . $this->datos_empresa['provincia_fiscal_empresa'] . ')' : '')
        );
        if ($cp_pob_prov) {
            $this->SetX(8);
            $this->Cell(95, 3, $cp_pob_prov, 0, 1, 'L');
        }
        $tel_str = '';
        if (!empty($this->datos_empresa['telefono_empresa'])) {
            $tel_str = 'Tel: ' . $this->datos_empresa['telefono_empresa'];
        }
        if (!empty($this->datos_empresa['movil_empresa'])) {
            $tel_str .= ($tel_str ? ' | ' : '') . $this->datos_empresa['movil_empresa'];
        }
        if ($tel_str) {
            $this->SetX(8);
            $this->Cell(95, 2.5, $tel_str, 0, 1, 'L');
        }
        if (!empty($this->datos_empresa['email_empresa'])) {
            $this->SetX(8);
            $this->Cell(95, 2.5, $this->datos_empresa['email_empresa'], 0, 1, 'L');
        }
        if (!empty($this->datos_empresa['web_empresa'])) {
            $this->SetX(8);
            $this->Cell(95, 2.5, $this->datos_empresa['web_empresa'], 0, 1, 'L');
        }

        // CAJA INFO (verde)
        $y_info = $this->GetY() + 1;
        $this->SetFillColor($this->cr, $this->cg, $this->cb);
        $this->SetTextColor(255, 255, 255);
        $this->SetFont('helvetica', 'B', 8.5);
        $this->SetXY(8, $y_info);
        $this->Cell(95, 10, '', 0, 0, 'L', true);
        $this->SetXY(9, $y_info + 1);
        $this->Cell(93, 3, 'Nº: ' . $this->numero_documento . '  |  F: ' . $this->fecha_documento, 0, 1, 'L');
        $num_ppto = $this->datos_presupuesto['numero_presupuesto'] ?? '';
        if ($num_ppto) {
            $this->SetXY(9, $y_info + 5);
            $this->SetFont('helvetica', '', 7.5);
            $this->Cell(93, 3, ($this->idioma === 'en' ? 'Ref. Quotation: ' : 'Ref. Presupuesto: ') . $num_ppto, 0, 1, 'L');
        }
        $this->SetTextColor(0, 0, 0);

        // BOX CLIENTE (borde verde)
        $col2_x = 108;
        $col2_w = 94;
        $box_y  = $y_start;
        $cli_h  = 26;
        if (!empty($this->datos_presupuesto['nombre_contacto_cliente'])) {
            $cli_h += 16;
        }

        $this->SetFillColor(248, 249, 250);
        $this->SetDrawColor($this->cr, $this->cg, $this->cb);
        $this->SetLineWidth(0.5);
        $this->Rect($col2_x, $box_y, $col2_w, $cli_h, 'DF');
        $this->SetLineWidth(0.2);

        $this->SetXY($col2_x + 2, $box_y + 1.5);
        $this->SetFont('helvetica', 'B', 9);
        $this->SetTextColor(44, 62, 80);
        $this->Cell($col2_w - 4, 3.5, $this->idioma === 'en' ? 'CUSTOMER' : 'CLIENTE', 0, 1, 'L');
        $this->SetDrawColor($this->cr, $this->cg, $this->cb);
        $this->Line($col2_x + 2, $box_y + 5.5, $col2_x + $col2_w - 2, $box_y + 5.5);

        $nombre_cli = trim(
            ($this->datos_presupuesto['nombre_cliente'] ?? '') . ' ' .
            ($this->datos_presupuesto['apellido_cliente'] ?? '')
        );
        $this->SetXY($col2_x + 2, $box_y + 6.5);
        $this->SetFont('helvetica', 'B', 8.5);
        $this->SetTextColor(44, 62, 80);
        $this->Cell($col2_w - 4, 3.5, $nombre_cli, 0, 1, 'L');

        $y_cd = $box_y + 11;
        $this->SetFont('helvetica', '', 7.5);
        $this->SetTextColor(70, 70, 70);

        $nif_cli = $this->datos_presupuesto['nif_cliente'] ?? '';
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
            ($this->datos_presupuesto['direccion_cliente'] ?? '') . ' ' .
            ($this->datos_presupuesto['cp_cliente'] ?? '') . ' ' .
            ($this->datos_presupuesto['poblacion_cliente'] ?? '')
        );
        if ($dir_cli) {
            $this->SetXY($col2_x + 2, $y_cd);
            $this->Cell($col2_w - 4, 3.5, $dir_cli, 0, 1, 'L');
            $y_cd += 4;
        }
        if (!empty($this->datos_presupuesto['email_cliente'])) {
            $this->SetXY($col2_x + 2, $y_cd);
            $this->Cell($col2_w - 4, 3.5, $this->datos_presupuesto['email_cliente'], 0, 1, 'L');
            $y_cd += 4;
        }
        if (!empty($this->datos_presupuesto['telefono_cliente'])) {
            $this->SetXY($col2_x + 2, $y_cd);
            $this->Cell($col2_w - 4, 3.5, $this->datos_presupuesto['telefono_cliente'], 0, 1, 'L');
        }
        if (!empty($this->datos_presupuesto['nombre_contacto_cliente'])) {
            $y_cd += 5;
            $this->SetXY($col2_x + 2, $y_cd);
            $this->SetFont('helvetica', 'B', 8);
            $this->SetTextColor($this->cr, $this->cg, $this->cb);
            $this->Cell($col2_w - 4, 3, 'A la atención de:', 0, 1, 'L');
            $y_cd += 3.5;
            $nombre_cont = trim(
                ($this->datos_presupuesto['nombre_contacto_cliente'] ?? '') . ' ' .
                ($this->datos_presupuesto['apellidos_contacto_cliente'] ?? '')
            );
            $this->SetFont('helvetica', '', 7.5);
            $this->SetTextColor(52, 73, 94);
            $this->SetXY($col2_x + 2, $y_cd);
            $this->Cell(15, 2.5, 'Nombre:', 0, 0, 'L');
            $this->SetFont('helvetica', 'B', 7.5);
            $this->Cell($col2_w - 19, 2.5, $nombre_cont, 0, 1, 'L');
            $y_cd += 3;
            if (!empty($this->datos_presupuesto['telefono_contacto_cliente'])) {
                $this->SetFont('helvetica', '', 7.5);
                $this->SetXY($col2_x + 2, $y_cd);
                $this->Cell(15, 2.5, 'Telefono:', 0, 0, 'L');
                $this->Cell($col2_w - 19, 2.5, $this->datos_presupuesto['telefono_contacto_cliente'], 0, 1, 'L');
                $y_cd += 3;
            }
            if (!empty($this->datos_presupuesto['email_contacto_cliente'])) {
                $this->SetFont('helvetica', '', 7.5);
                $this->SetXY($col2_x + 2, $y_cd);
                $this->Cell(15, 2.5, 'Email:', 0, 0, 'L');
                $this->Cell($col2_w - 19, 2.5, $this->datos_presupuesto['email_contacto_cliente'], 0, 1, 'L');
            }
        }

        $this->y_header_bottom = max($y_info + 12, $box_y + $cli_h + 5);
        $this->SetDrawColor(200, 200, 200);
        $this->SetTextColor(0, 0, 0);
        $this->SetLineWidth(0.2);
    }

    public function Footer()
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
        $id_presupuesto = (int)($_POST['id_presupuesto'] ?? 0);
        $id_empresa     = (int)($_POST['id_empresa']     ?? 0);
        $id_pago_ppto   = (int)($_POST['id_pago_ppto']   ?? 0);
        $importe_total  = (float)($_POST['importe_total'] ?? 0);
        $pct_iva        = (float)($_POST['porcentaje_iva'] ?? 21);
        $numero_version = !empty($_POST['numero_version']) ? (int)$_POST['numero_version'] : null;
        $tipo_cliente   = in_array($_POST['tipo_cliente'] ?? '', ['cliente_final', 'agencia_descuento'])
                            ? $_POST['tipo_cliente'] : 'cliente_final';
        $idioma         = ($_POST['idioma'] ?? 'es') === 'en' ? 'en' : 'es';

        // Si importe_total no llega del POST (flujo normal desde modal), leerlo del pago registrado
        if ($importe_total <= 0 && $id_pago_ppto) {
            $pagoTmp = (new PagoPresupuesto())->get_pagoxid($id_pago_ppto);
            if ($pagoTmp) {
                $importe_total = (float)($pagoTmp['importe_pago_ppto'] ?? 0);
            }
        }

        // Asegurar datos del pago para comprobaciones posteriores (método de pago real)
        if (empty($pagoTmp) && $id_pago_ppto) {
            $pagoTmp = (new PagoPresupuesto())->get_pagoxid($id_pago_ppto);
        }

        if (!$id_presupuesto || !$id_empresa || !$id_pago_ppto || $importe_total <= 0) {
            echo json_encode(['success' => false, 'message' => 'Faltan campos obligatorios (id_presupuesto, id_empresa, id_pago_ppto, importe_total)'], JSON_UNESCAPED_UNICODE);
            break;
        }

        try {
            if (!$docModel->verificar_empresa_real($id_empresa)) {
                echo json_encode(['success' => false, 'message' => 'La empresa seleccionada no es una empresa real válida'], JSON_UNESCAPED_UNICODE);
                break;
            }

            $datos_ppto = $impresion->get_datos_cabecera($id_presupuesto, $numero_version);
            if (!$datos_ppto) {
                echo json_encode(['success' => false, 'message' => "No se encontraron datos del presupuesto ID: $id_presupuesto"], JSON_UNESCAPED_UNICODE);
                break;
            }

            $datos_empresa = $empModel->get_empresaxid($id_empresa);
            if (!$datos_empresa) {
                echo json_encode(['success' => false, 'message' => "No se encontraron datos de la empresa ID: $id_empresa"], JSON_UNESCAPED_UNICODE);
                break;
            }

            // Inyectar método de pago real en datos_ppto para la comprobación de transferencia en PDF
            if (!empty($pagoTmp['nombre_metodo_pago'])) {
                $datos_ppto['nombre_metodo_pago_pago'] = $pagoTmp['nombre_metodo_pago'];
            }

            // Calcular desglose IVA
            $base_imp     = round($importe_total / (1 + $pct_iva / 100), 2);
            $cuota_iva    = $importe_total - $base_imp;
            $desglose_iva = [$pct_iva => ['base' => $base_imp, 'cuota' => $cuota_iva]];

            // Crear registro documento
            $datos_insert = [
                'id_presupuesto'              => $id_presupuesto,
                'tipo_documento_ppto'         => 'factura_anticipo',
                'id_empresa'                  => $id_empresa,
                'id_pago_ppto'                => $id_pago_ppto,
                'numero_version'              => $numero_version,
                'importe_documento_ppto'      => $importe_total,
            ];

            $id_doc = $docModel->insert_documento($datos_insert);
            if (!$id_doc) {
                echo json_encode(['success' => false, 'message' => 'Error al crear el registro de documento'], JSON_UNESCAPED_UNICODE);
                break;
            }

            // Vincular documento al pago (actualizar pago_presupuesto.id_documento_ppto)
            if ($id_pago_ppto) {
                $pagoModel = new PagoPresupuesto();
                $pagoModel->update_pago($id_pago_ppto, ['id_documento_ppto' => $id_doc]);
            }

            $doc             = $docModel->get_documentoxid($id_doc);
            $numero_documento = $doc['numero_documento_ppto'] ?? "F-{$id_doc}";

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

            // Actualizar importes
            $docModel->actualizar_importes($id_doc, $base_imp, $cuota_iva, $importe_total);

            // Generar PDF
            $pdf = _generar_pdf_anticipo(
                $datos_ppto, $datos_empresa, $numero_documento,
                $base_imp, $cuota_iva, $importe_total, $desglose_iva,
                $mostrar_logo, $path_logo,
                $idioma, $tipo_cliente
            );

            // Guardar a disco
            $dir_guardado = __DIR__ . '/../public/documentos/anticipos/';
            if (!is_dir($dir_guardado)) {
                if (!mkdir($dir_guardado, 0755, true)) {
                    throw new Exception("No se pudo crear el directorio: $dir_guardado");
                }
            }
            $nombre_archivo = preg_replace('/[^A-Za-z0-9_\-]/', '_', $numero_documento) . '.pdf';
            $ruta_absoluta  = $dir_guardado . $nombre_archivo;
            $ruta_relativa  = 'public/documentos/anticipos/' . $nombre_archivo;

            $pdf_string = $pdf->Output($nombre_archivo, 'S');
            if (file_put_contents($ruta_absoluta, $pdf_string) === false) {
                throw new Exception("No se pudo escribir el PDF en: $ruta_absoluta");
            }
            $tamano = filesize($ruta_absoluta);

            $docModel->actualizar_ruta_pdf($id_doc, $ruta_relativa, $tamano);

            $registro->registrarActividad(
                'admin', 'impresion_factura_anticipo.php', 'generar',
                "Factura anticipo $numero_documento generada. Doc ID: $id_doc", 'info'
            );

            ob_end_clean();
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'success'           => true,
                'id_documento_ppto' => $id_doc,
                'numero_documento'  => $numero_documento,
                'url_pdf'           => $ruta_relativa,
            ], JSON_UNESCAPED_UNICODE);

        } catch (Exception $e) {
            $registro->registrarActividad(
                'admin', 'impresion_factura_anticipo.php', 'generar',
                "Error: " . $e->getMessage(), 'error'
            );
            ob_end_clean();
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false, 'message' => 'Error al generar la factura: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
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
// FUNCIÓN: Generar PDF Factura Anticipo
// Usa MYPDF_ANTICIPO con Header/Footer al estilo presupuesto
// ══════════════════════════════════════════════════════════════════
function _generar_pdf_anticipo(
    array $datos_ppto, array $datos_empresa, string $numero_documento,
    float $base_imp, float $cuota_iva, float $importe_total, array $desglose_iva,
    bool $mostrar_logo, string $path_logo,
    string $idioma = 'es', string $tipo_cliente = 'cliente_final'
): MYPDF_ANTICIPO {

    $fecha_hoy = date('d/m/Y');

    // Textos bilingüe
    $t = ($idioma === 'en') ? [
        'desc'        => 'Description',
        'cant'        => 'Qty.',
        'punit'       => 'Unit Price €',
        'importe'     => 'Amount €',
        'a_cuenta'    => 'On account of confirmation of quotation ',
        'base_imp'    => 'Tax Base:',
        'iva_label'   => 'VAT ',
        'total'       => 'TOTAL:',
        'forma_pago'  => 'Payment method: ',
        'banco'       => 'Bank:',
        'visto_bueno' => 'CUSTOMER APPROVAL',
        'fecha'       => 'Date: ',
    ] : [
        'desc'        => 'Descripción',
        'cant'        => 'Cant.',
        'punit'       => 'P.Unit. €',
        'importe'     => 'Importe €',
        'a_cuenta'    => 'Entrega a cuenta confirmación presupuesto ',
        'base_imp'    => 'Base imponible:',
        'iva_label'   => 'IVA ',
        'total'       => 'TOTAL:',
        'forma_pago'  => 'Forma de pago: ',
        'banco'       => 'Banco:',
        'visto_bueno' => 'VISTO BUENO DEL CLIENTE',
        'fecha'       => 'Fecha: ',
    ];

    // ─── Inicializar PDF ──────────────────────────────────────────
    $pdf = new MYPDF_ANTICIPO('P', 'mm', 'A4', true, 'UTF-8', false);
    $pdf->SetCreator('MDR ERP');
    $pdf->SetAuthor($datos_empresa['nombre_comercial_empresa'] ?? ($datos_empresa['nombre_empresa'] ?? 'MDR'));
    $pdf->SetTitle('Factura ' . $numero_documento);

    $pdf->datos_empresa     = $datos_empresa;
    $pdf->datos_presupuesto = $datos_ppto;
    $pdf->numero_documento  = $numero_documento;
    $pdf->fecha_documento   = $fecha_hoy;
    $pdf->texto_pie_empresa = $datos_empresa['texto_pie_factura_empresa'] ?? '';
    $pdf->mostrar_logo      = $mostrar_logo;
    $pdf->path_logo         = $path_logo;
    $pdf->idioma            = $idioma;

    $pdf->setPrintHeader(true);
    $pdf->setPrintFooter(true);
    $pdf->SetMargins(8, 72, 8);
    $pdf->SetHeaderMargin(5);
    $pdf->SetFooterMargin(15);
    $pdf->SetAutoPageBreak(true, 25);
    $pdf->AddPage();
    $pdf->SetY($pdf->y_header_bottom);

    // ─── OBSERVACIONES DE CABECERA ────────────────────────────────
    $obs_cab = ($idioma === 'en')
        ? trim($datos_ppto['observaciones_cabecera_ingles_presupuesto'] ?? '')
        : trim($datos_ppto['observaciones_cabecera_presupuesto'] ?? '');
    if (!empty($obs_cab)) {
        $pdf->SetFont('helvetica', '', 8);
        $pdf->SetTextColor(44, 62, 80);
        $pdf->MultiCell(194, 4, $obs_cab, 0, 'L');
        $pdf->Ln(3);
    }

    // ─── TABLA (una sola línea) ───────────────────────────────────
    // Anchos: Desc=128, Cant=12, PUnit=18, IVA%=12, Importe=24 → total=194mm
    $w_desc  = 140;
    $w_cant  = 12;
    $w_punit = 18;
    $w_imp   = 24;
    $col_h   = 6;

    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->SetFillColor(240, 240, 240);
    $pdf->SetDrawColor(200, 200, 200);
    $pdf->SetTextColor(44, 62, 80);
    $pdf->Cell($w_desc,  $col_h, $t['desc'],    1, 0, 'L', true);
    $pdf->Cell($w_cant,  $col_h, $t['cant'],    1, 0, 'C', true);
    $pdf->Cell($w_punit, $col_h, $t['punit'],   1, 0, 'C', true);
    $pdf->Cell($w_imp,   $col_h, $t['importe'], 1, 1, 'C', true);

    $pdf->SetFont('helvetica', '', 8);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFillColor(255, 255, 255);
    $desc_linea = $t['a_cuenta'] . ($datos_ppto['numero_presupuesto'] ?? '');
    $pdf->Cell($w_desc,  $col_h, $desc_linea,                                   1, 0, 'L');
    $pdf->Cell($w_cant,  $col_h, '1',                                           1, 0, 'C');
    $pdf->Cell($w_punit, $col_h, number_format($base_imp, 2, ',', '.'),         1, 0, 'R');
    $pdf->Cell($w_imp,   $col_h, number_format($base_imp, 2, ',', '.'),         1, 1, 'R');

    // ─── TOTALES ──────────────────────────────────────────────────
    $w_spacer = 144;
    $w_label  = 30;
    $w_value  = 20;

    $pdf->Ln(3);
    $pdf->SetFont('helvetica', '', 8.5);
    $pdf->SetFillColor(248, 249, 250);
    $pdf->SetDrawColor(220, 220, 220);
    $pdf->SetTextColor(44, 62, 80);

    $pdf->Cell($w_spacer, 6, '', 0, 0);
    $pdf->Cell($w_label,  6, $t['base_imp'], 'LTB', 0, 'R', true);
    $pdf->SetFont('helvetica', 'B', 8.5);
    $pdf->Cell($w_value,  6, number_format($base_imp, 2, ',', '.') . ' €', 'RTB', 1, 'R', true);

    foreach ($desglose_iva as $pct => $v) {
        $pdf->SetFont('helvetica', '', 8);
        $pdf->SetTextColor(52, 73, 94);
        $pdf->Cell($w_spacer, 5, '', 0, 0);
        $pdf->Cell($w_label,  5, $t['iva_label'] . $pct . '%:', 0, 0, 'R');
        $pdf->Cell($w_value,  5, number_format($v['cuota'], 2, ',', '.') . ' €', 0, 1, 'R');
    }

    // TOTAL verde
    $pdf->SetFont('helvetica', 'B', 9);
    $pdf->SetFillColor(39, 174, 96);
    $pdf->SetDrawColor(30, 140, 70);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->Cell($w_spacer, 10, '', 0, 0);
    $pdf->Cell($w_label,  10, $t['total'], 1, 0, 'R', true);
    $pdf->Cell($w_value,  10, number_format($importe_total, 2, ',', '.') . ' €', 1, 1, 'R', true);

    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetDrawColor(0, 0, 0);
    $pdf->SetLineWidth(0.2);
    $pdf->Ln(4);

    // ─── DATOS BANCARIOS ──────────────────────────────────────────
    // Preferir el método real del pago (ej. "Transferencia bancaria") sobre la forma de pago del presupuesto
    $nombre_fp        = strtoupper($datos_ppto['nombre_metodo_pago_pago'] ?? $datos_ppto['nombre_pago'] ?? '');
    $es_transferencia = (strpos($nombre_fp, 'TRANSFERENCIA') !== false || strpos($nombre_fp, 'TRANSFER') !== false);
    $tiene_datos_ban  = !empty($datos_empresa['iban_empresa']) || !empty($datos_empresa['banco_empresa']);
    $mostrar_cuenta   = !empty($datos_empresa['mostrar_cuenta_bancaria_pdf_presupuesto_empresa']);

    if ($es_transferencia && $tiene_datos_ban && $mostrar_cuenta) {
        $texto_fp     = $t['forma_pago'] . ($datos_ppto['nombre_pago'] ?? '');
        $lineas_banco = [];
        $altura_banco = 8;
        if (!empty($datos_empresa['banco_empresa'])) {
            $lineas_banco[] = ['label' => $t['banco'],  'value' => $datos_empresa['banco_empresa']];
            $altura_banco += 6;
        }
        if (!empty($datos_empresa['iban_empresa'])) {
            $lineas_banco[] = ['label' => 'IBAN:',      'value' => $datos_empresa['iban_empresa']];
            $altura_banco += 6;
        }
        if (!empty($datos_empresa['swift_empresa'])) {
            $lineas_banco[] = ['label' => 'SWIFT/BIC:', 'value' => $datos_empresa['swift_empresa']];
            $altura_banco += 6;
        }
        $pdf->Ln(8);
        $y_banco = $pdf->GetY();
        $pdf->SetFillColor(245, 245, 245);
        $pdf->SetDrawColor(180, 180, 180);
        $pdf->Rect(8, $y_banco, 100, $altura_banco, 'DF');
        $pdf->SetXY(10, $y_banco + 1.5);
        $pdf->SetFont('helvetica', 'B', 7);
        $pdf->SetTextColor(44, 62, 80);
        $pdf->Cell(93, 3.5, $texto_fp, 0, 1, 'L');
        foreach ($lineas_banco as $lb) {
            $pdf->SetX(10);
            $pdf->SetFont('helvetica', '', 6);
            $pdf->SetTextColor(80, 80, 80);
            $pdf->Cell(25, 5.5, $lb['label'], 0, 0, 'R');
            $pdf->SetFont('helvetica', 'B', 7);
            $pdf->SetTextColor(44, 62, 80);
            $pdf->Cell(66, 5.5, $lb['value'], 0, 1, 'L');
        }
        $pdf->Ln(5);
    }

    $pdf->SetAutoPageBreak(true, 25);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetLineWidth(0.2);

    return $pdf;
}

