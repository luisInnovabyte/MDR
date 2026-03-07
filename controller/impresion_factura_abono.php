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
// CLASE PDF — Factura de Abono (rojo #C0392B)
// ══════════════════════════════════════════════════════════════════
class MYPDF_ABONO extends TCPDF
{
    public $datos_empresa     = [];
    public $datos_presupuesto = [];
    public $numero_documento  = '';
    public $fecha_documento   = '';
    public $numero_doc_origen = '';
    public $texto_pie_empresa = '';
    public $mostrar_logo      = false;
    public $path_logo         = '';

    // RGB rojo
    private $cr = 192;
    private $cg = 57;
    private $cb = 43;

    public function setDatosHeader(
        array  $datos_empresa,
        array  $datos_presupuesto,
        string $numero,
        string $fecha,
        string $origen,
        string $pie,
        bool   $logo,
        string $path
    ): void {
        $this->datos_empresa     = $datos_empresa;
        $this->datos_presupuesto = $datos_presupuesto;
        $this->numero_documento  = $numero;
        $this->fecha_documento   = $fecha;
        $this->numero_doc_origen = $origen;
        $this->texto_pie_empresa = $pie;
        $this->mostrar_logo      = $logo;
        $this->path_logo         = $path;
    }

    public function Header(): void
    {
        $cr = $this->cr; $cg = $this->cg; $cb = $this->cb;
        $e  = $this->datos_empresa;
        $p  = $this->datos_presupuesto;

        // ── Título doc (arriba derecha) ───────────────────────────────
        $y_start = 10;
        $this->SetY($y_start);
        $this->SetFont('helvetica', 'B', 16);
        $this->SetTextColor($cr, $cg, $cb);
        $this->Cell(194, 7, 'FACTURA DE ABONO', 0, 1, 'R');
        $y_start = $this->GetY(); // ~20

        // ── Logo ──────────────────────────────────────────────────────
        if ($this->mostrar_logo && file_exists($this->path_logo)) {
            $this->Image($this->path_logo, 8, $y_start, 35, 0, '', '', '', false, 300);
        }

        // ── Empresa (columna izqda) ───────────────────────────────────
        $this->SetXY(46, $y_start);
        $this->SetFont('helvetica', 'B', 9);
        $this->SetTextColor(44, 62, 80);
        $nombre_com = $e['nombre_comercial_empresa'] ?? ($e['nombre_empresa'] ?? '');
        $this->Cell(57, 4, $nombre_com, 0, 0, 'L');

        $dir_lines = [];
        if (!empty($e['nif_empresa']))              $dir_lines[] = 'NIF: ' . $e['nif_empresa'];
        if (!empty($e['direccion_fiscal_empresa'])) $dir_lines[] = $e['direccion_fiscal_empresa'];
        $cp_pob = trim(($e['cp_fiscal_empresa'] ?? '') . ' ' . ($e['poblacion_fiscal_empresa'] ?? ''));
        if ($cp_pob)                                 $dir_lines[] = $cp_pob;
        if (!empty($e['telefono_empresa']))          $dir_lines[] = 'Tel: ' . $e['telefono_empresa'];
        if (!empty($e['email_empresa']))             $dir_lines[] = $e['email_empresa'];

        $y_emp = $y_start + 5;
        $this->SetFont('helvetica', '', 7.5);
        $this->SetTextColor(70, 70, 70);
        foreach ($dir_lines as $dl) {
            $this->SetXY(46, $y_emp);
            $this->Cell(57, 3.5, $dl, 0, 0, 'L');
            $y_emp += 3.5;
        }

        // ── Caja info (rojo fill · 3 líneas · alto 14) ───────────────
        $y_info = $y_start;
        $this->SetXY(108, $y_info);
        $this->SetFillColor($cr, $cg, $cb);
        $this->SetTextColor(255, 255, 255);
        $this->Cell(95, 14, '', 0, 0, 'L', true);

        // Línea 1: Nº | Fecha
        $num_fecha = 'Nº: ' . $this->numero_documento . '  |  F: ' . $this->fecha_documento;
        $this->SetFont('helvetica', 'B', 8);
        $this->SetXY(110, $y_info + 1);
        $this->Cell(91, 4, $num_fecha, 0, 0, 'L');

        // Línea 2: ABONA LA FACTURA
        $this->SetXY(110, $y_info + 6);
        $this->SetFont('helvetica', '', 7.5);
        $this->Cell(91, 4, 'ABONA LA FACTURA: ' . $this->numero_doc_origen, 0, 0, 'L');

        // ── Caja cliente (borde rojo) ─────────────────────────────────
        $y_cli    = $y_start + 16;
        $alt_cli  = !empty($p['nif_cliente']) ? 36 : 26;
        $this->SetDrawColor($cr, $cg, $cb);
        $this->SetLineWidth(0.5);
        $this->Rect(8, $y_cli, 94, $alt_cli, 'D');
        $this->SetLineWidth(0.2);

        $this->SetXY(10, $y_cli + 1.5);
        $this->SetFont('helvetica', 'B', 7.5);
        $this->SetTextColor(100, 100, 100);
        $this->Cell(90, 3.5, 'CLIENTE', 0, 0, 'L');

        $this->SetXY(10, $y_cli + 6);
        $this->SetFont('helvetica', 'B', 8.5);
        $this->SetTextColor(44, 62, 80);
        $nombre_cli = trim(($p['nombre_cliente'] ?? '') . ' ' . ($p['apellido_cliente'] ?? ''));
        $this->Cell(90, 4, $nombre_cli, 0, 0, 'L');

        $this->SetFont('helvetica', '', 7.5);
        $this->SetTextColor(70, 70, 70);
        $y_off = $y_cli + 11;
        if (!empty($p['nif_cliente'])) {
            $this->SetXY(10, $y_off);
            $this->Cell(90, 3.5, 'NIF/CIF: ' . $p['nif_cliente'], 0, 0, 'L');
            $y_off += 3.5;
        }
        $dir_c = trim(($p['direccion_cliente'] ?? '') . ', ' . ($p['cp_cliente'] ?? '') . ' ' . ($p['poblacion_cliente'] ?? ''));
        if ($dir_c !== ', ') {
            $this->SetXY(10, $y_off);
            $this->Cell(90, 3.5, $dir_c, 0, 0, 'L');
            $y_off += 3.5;
        }
        if (!empty($p['telefono_cliente'])) {
            $this->SetXY(10, $y_off);
            $this->Cell(90, 3.5, 'Tel: ' . $p['telefono_cliente'], 0, 0, 'L');
        }

        // ── Reset ─────────────────────────────────────────────────────
        $this->SetTextColor(0, 0, 0);
        $this->SetDrawColor(170, 170, 170);
    }

    public function Footer(): void
    {
        $this->SetY(-15);
        $this->SetDrawColor(170, 170, 170);
        $this->Line(8, $this->GetY(), 202, $this->GetY());
        $this->Ln(1);
        if (!empty($this->texto_pie_empresa)) {
            $this->SetFont('helvetica', 'I', 6.5);
            $this->SetTextColor(130, 130, 130);
            $this->MultiCell(194, 3.5, $this->texto_pie_empresa, 0, 'C');
        }
        $this->SetFont('helvetica', 'I', 7);
        $this->SetTextColor(150, 150, 150);
        $this->SetXY(8, -8);
        $this->Cell(194, 4, 'Pág. ' . $this->getAliasNumPage() . ' / ' . $this->getAliasNbPages(), 0, 0, 'R');
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

            // Obtener líneas del presupuesto para calcular importes del abono
            $lineas = $impresion->get_lineas_impresion($id_presupuesto, $numero_version);

            // Calcular totales (los mismos que el documento origen, invertidos)
            $importe_origen = (float)($doc_origen['importe_documento_ppto'] ?? 0);
            $subtotal_base  = 0;
            $total_iva      = 0;
            $desglose_iva   = [];

            foreach ($lineas as $l) {
                $base  = (float)($l['base_imponible'] ?? 0);
                $pct   = (float)($l['porcentaje_iva_linea_ppto'] ?? 0);
                $cuota = $base * ($pct / 100);
                $subtotal_base += $base;
                $total_iva     += $cuota;
                if (!isset($desglose_iva[$pct])) $desglose_iva[$pct] = ['base' => 0, 'cuota' => 0];
                $desglose_iva[$pct]['base']  += $base;
                $desglose_iva[$pct]['cuota'] += $cuota;
            }
            ksort($desglose_iva);

            $total_con_iva = round($subtotal_base + $total_iva, 2);

            // Importe negativo para el abono
            $importe_abono = -abs($importe_origen ?: $total_con_iva);

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

            // Generar PDF
            $pdf = _generar_pdf_abono(
                $datos_ppto, $datos_empresa, $numero_documento,
                $numero_doc_origen, $motivo_abono,
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
    string $numero_doc_origen, string $motivo_abono,
    array $lineas, array $desglose_iva, float $subtotal_base, float $total_iva, float $total_con_iva,
    bool $mostrar_logo, string $path_logo
): MYPDF_ABONO {

    $fecha_hoy = date('d/m/Y');

    // ── Instanciar MYPDF_ABONO ────────────────────────────────────────
    $pdf = new MYPDF_ABONO('P', 'mm', 'A4', true, 'UTF-8', false);
    $pdf->SetCreator('MDR ERP');
    $pdf->SetAuthor($datos_empresa['nombre_comercial_empresa'] ?? ($datos_empresa['nombre_empresa'] ?? 'MDR'));
    $pdf->SetTitle('Factura de Abono ' . $numero_documento);
    $pdf->SetMargins(8, 72, 8);
    $pdf->SetAutoPageBreak(true, 20);

    $pie = $datos_empresa['texto_pie_presupuesto_empresa'] ?? '';
    $pdf->setDatosHeader(
        $datos_empresa,
        $datos_ppto,
        $numero_documento,
        $fecha_hoy,
        $numero_doc_origen,
        $pie,
        $mostrar_logo,
        $path_logo
    );

    $pdf->AddPage();

    // ── Caja motivo abono (amarillo) ──────────────────────────────────
    if ($motivo_abono) {
        $pdf->SetFillColor(255, 243, 205);
        $pdf->SetDrawColor(255, 193, 7);
        $pdf->SetLineWidth(0.5);
        $pdf->SetFont('helvetica', 'B', 7.5);
        $pdf->SetTextColor(44, 62, 80);
        $pdf->Cell(194, 5, 'MOTIVO DEL ABONO:', 'LTR', 1, 'L', true);
        $pdf->SetFont('helvetica', '', 8);
        $pdf->MultiCell(194, 5, $motivo_abono, 'LBR', 'L', true);
        $pdf->Ln(4);
        $pdf->SetDrawColor(200, 200, 200);
        $pdf->SetLineWidth(0.2);
    }

    // ── Tabla de líneas ───────────────────────────────────────────────
    // Cabecera: Desc(128) + Cant(12) + PUnit(18) + %Dto(12) + Importe(24) = 194
    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->SetFillColor(255, 235, 235);
    $pdf->SetDrawColor(200, 200, 200);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell(128, 6, 'Descripción',  1, 0, 'L', true);
    $pdf->Cell( 12, 6, 'Cant.',        1, 0, 'C', true);
    $pdf->Cell( 18, 6, 'P.Unit. €',    1, 0, 'C', true);
    $pdf->Cell( 12, 6, '%Dto',         1, 0, 'C', true);
    $pdf->Cell( 24, 6, 'Importe €',    1, 1, 'C', true);

    $pdf->SetFont('helvetica', '', 8);

    foreach ($lineas as $l) {
        $desc        = $l['descripcion_linea_ppto'] ?? '';
        $cant        = floatval($l['cantidad_linea_ppto']      ?? 0);
        $precio      = floatval($l['precio_unitario_linea_ppto'] ?? 0);
        $dto         = floatval($l['descuento_linea_ppto']     ?? 0);
        $base        = -abs(floatval($l['base_imponible']       ?? 0));

        $altura_desc = max(6, $pdf->getStringHeight(127, $desc));
        $x0 = $pdf->GetX();
        $y0 = $pdf->GetY();

        // Comprueba salto de página manual
        if ($y0 + $altura_desc > $pdf->getPageHeight() - 25) {
            $pdf->AddPage();
            $y0 = $pdf->GetY();
            $x0 = $pdf->GetX();
        }

        // Descripción (MultiCell)
        $pdf->SetXY($x0, $y0);
        $pdf->Rect($x0, $y0, 128, $altura_desc, 'D');
        $pdf->SetXY($x0 + 1, $y0 + 0.5);
        $pdf->MultiCell(126, 5, $desc, 0, 'L');

        // Columnas numéricas en rojo
        $pdf->SetXY($x0 + 128, $y0);
        $pdf->SetTextColor(192, 57, 43);
        $pdf->Cell( 12, $altura_desc, number_format($cant, 0),               1, 0, 'C');
        $pdf->Cell( 18, $altura_desc, number_format($precio, 2, ',', '.'),   1, 0, 'R');
        $pdf->Cell( 12, $altura_desc, ($dto > 0 ? number_format($dto, 0).'%' : '-'), 1, 0, 'C');
        $pdf->Cell( 24, $altura_desc, number_format($base,  2, ',', '.') . ' €', 1, 1, 'R');
        $pdf->SetTextColor(0, 0, 0);
    }

    // ── Totales ───────────────────────────────────────────────────────
    $pdf->Ln(3);
    $pdf->SetFont('helvetica', '', 8.5);
    $pdf->SetFillColor(248, 249, 250);

    // Base imponible
    $pdf->Cell(144, 6, '', 0, 0);
    $pdf->Cell( 30, 6, 'Base imponible:', 1, 0, 'R', true);
    $pdf->SetFont('helvetica', 'B', 8.5);
    $pdf->SetTextColor(192, 57, 43);
    $pdf->Cell( 20, 6, '-' . number_format($subtotal_base, 2, ',', '.') . ' €', 1, 1, 'R', true);
    $pdf->SetTextColor(0, 0, 0);

    // Por tramo de IVA
    foreach ($desglose_iva as $pct => $v) {
        $pdf->SetFont('helvetica', '', 8.5);
        $pdf->Cell(144, 5, '', 0, 0);
        $pdf->Cell( 30, 5, "IVA {$pct}%:",  1, 0, 'R', true);
        $pdf->SetFont('helvetica', 'B', 8.5);
        $pdf->SetTextColor(192, 57, 43);
        $pdf->Cell( 20, 5, '-' . number_format($v['cuota'], 2, ',', '.') . ' €', 1, 1, 'R', true);
        $pdf->SetTextColor(0, 0, 0);
    }

    // TOTAL A DEVOLVER (fondo rojo)
    $pdf->SetFont('helvetica', 'B', 11);
    $pdf->SetFillColor(192, 57, 43);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->Cell(144, 8, '', 0, 0);
    $pdf->Cell( 30, 8, 'TOTAL A DEVOLVER:',  1, 0, 'R', true);
    $pdf->Cell( 20, 8, '-' . number_format($total_con_iva, 2, ',', '.') . ' €', 1, 1, 'R', true);
    $pdf->SetTextColor(0, 0, 0);

    // ── Datos bancarios ───────────────────────────────────────────────
    $mostrar_banco = !empty($datos_empresa['mostrar_cuenta_bancaria_pdf_presupuesto_empresa'])
        && $datos_empresa['mostrar_cuenta_bancaria_pdf_presupuesto_empresa'] == 1;

    if ($mostrar_banco && (
        !empty($datos_empresa['iban_empresa']) ||
        !empty($datos_empresa['swift_empresa']) ||
        !empty($datos_empresa['banco_empresa'])
    )) {
        $pdf->Ln(5);
        $pdf->SetFillColor(248, 249, 250);
        $pdf->SetDrawColor(220, 220, 220);
        $pdf->SetLineWidth(0.3);
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->SetTextColor(44, 62, 80);
        $pdf->Cell(194, 5, 'DATOS BANCARIOS PARA DEVOLUCIÓN', 'LTR', 1, 'C', true);
        $pdf->SetFont('helvetica', '', 8);
        $pdf->SetTextColor(70, 70, 70);
        if (!empty($datos_empresa['banco_empresa'])) {
            $pdf->Cell(194, 4.5, 'Banco: ' . $datos_empresa['banco_empresa'], 'LR', 1, 'L', true);
        }
        if (!empty($datos_empresa['iban_empresa'])) {
            $pdf->Cell(194, 4.5, 'IBAN: ' . $datos_empresa['iban_empresa'], 'LR', 1, 'L', true);
        }
        if (!empty($datos_empresa['swift_empresa'])) {
            $pdf->Cell(194, 4.5, 'SWIFT/BIC: ' . $datos_empresa['swift_empresa'], 'LBR', 1, 'L', true);
        } else {
            $pdf->Cell(194, 0, '', 'B', 1, 'L');
        }
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetDrawColor(170, 170, 170);
    }

    // ── Bloque de firmas ──────────────────────────────────────────────
    $pdf->Ln(10);
    $y_firma = $pdf->GetY();

    $pdf->SetFont('helvetica', '', 8);
    $pdf->SetTextColor(80, 80, 80);
    $cabecera_firma = $datos_empresa['cabecera_firma_presupuesto_empresa'] ?? 'DEPARTAMENTO COMERCIAL';

    // Columna izquierda: empresa
    $pdf->SetXY(8, $y_firma);
    $pdf->Cell(88, 4, $cabecera_firma, 'T', 0, 'C');
    $pdf->SetFont('helvetica', '', 7);
    $pdf->SetXY(8, $y_firma + 5);
    $pdf->Cell(88, 3.5, 'Nombre y firma:', 0, 0, 'L');
    $pdf->SetXY(8, $y_firma + 22);
    $pdf->Cell(88, 3.5, 'Fecha: ___/___/______', 0, 0, 'L');

    // Columna derecha: cliente
    $pdf->SetFont('helvetica', '', 8);
    $pdf->SetTextColor(80, 80, 80);
    $pdf->SetXY(108, $y_firma);
    $pdf->Cell(94, 4, 'VISTO BUENO DEL CLIENTE', 'T', 0, 'C');
    $pdf->SetFont('helvetica', '', 7);
    $pdf->SetXY(108, $y_firma + 5);
    $pdf->Cell(94, 3.5, 'Nombre y firma:', 0, 0, 'L');
    $pdf->SetXY(108, $y_firma + 22);
    $pdf->Cell(94, 3.5, 'Fecha: ___/___/______', 0, 0, 'L');

    $pdf->SetTextColor(0, 0, 0);

    return $pdf;
}
