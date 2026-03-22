<?php
/**
 * impresion_factura_agrupada.php
 *
 * Genera el PDF de una FACTURA AGRUPADA (varios presupuestos en una factura).
 * Los datos proceden directamente de factura_agrupada y factura_agrupada_presupuesto.
 * No utiliza DocumentoPresupuesto ni SP de numeración (el número ya está asignado).
 *
 * Color identificativo: naranja/ámbar  (211, 84, 0)
 *
 * op=generar (POST)
 *   - id_factura_agrupada  (requerido)
 *
 * op=descargar (GET)
 *   - id  (requerido)  — id_factura_agrupada
 *
 * op=regenerar (POST)
 *   - id_factura_agrupada  (requerido) — regenera el PDF aunque ya exista
 *
 * Devuelve JSON { success, id_factura_agrupada, numero, url_pdf }
 * El PDF se guarda en public/documentos/facturas_agrupadas/
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
require_once __DIR__ . "/../models/FacturaAgrupada.php";
require_once __DIR__ . "/../models/Kit.php";
require_once __DIR__ . "/../vendor/tcpdf/tcpdf.php";

// ══════════════════════════════════════════════════════════════════
// CLASE PDF — Factura Agrupada
// Color identificativo: naranja (211, 84, 0)
// ══════════════════════════════════════════════════════════════════

class MYPDF_AGRUPADA extends TCPDF
{
    private $datos_empresa;
    private $datos_factura;
    private $mostrar_logo;
    private $path_logo;
    private $texto_pie_empresa;

    // Azul elegante (igual que factura real y presupuesto)
    private $cr = 102;
    private $cg = 126;
    private $cb = 234;

    public function setDatosHeader(
        array  $empresa,
        array  $factura,
        bool   $logo,
        string $path_logo,
        string $pie_empresa
    ) {
        $this->datos_empresa     = $empresa;
        $this->datos_factura     = $factura;
        $this->mostrar_logo      = $logo;
        $this->path_logo         = $path_logo;
        $this->texto_pie_empresa = $pie_empresa;
    }

    // ─────────────────────────────────────────────────────────────
    // CABECERA
    // ─────────────────────────────────────────────────────────────
    public function Header()
    {
        $y_start = 10;

        // ── TÍTULO ───────────────────────────────────────────────
        $titulo = $this->datos_factura['is_abono_agrupada']
            ? 'FACTURA RECTIFICATIVA'
            : 'FACTURA';

        $this->SetY($y_start);
        $this->SetFont('helvetica', 'B', 16);
        $this->SetTextColor($this->cr, $this->cg, $this->cb);
        $this->Cell(0, 8, $titulo, 0, 1, 'R');
        $this->Ln(2);
        $y_start = $this->GetY();

        // ── LOGO ─────────────────────────────────────────────────
        if ($this->mostrar_logo && !empty($this->path_logo) && file_exists($this->path_logo)) {
            $this->Image($this->path_logo, 8, $y_start, 35, 0, '', '', '', false, 300);
            $logo_height = 18;
        } else {
            $logo_height = 0;
        }

        // ── DATOS EMPRESA ────────────────────────────────────────
        $y_empresa = $y_start + $logo_height + 1;
        $this->SetY($y_empresa);
        $this->SetX(8);

        $this->SetFont('helvetica', 'B', 9);
        $this->SetTextColor(44, 62, 80);
        $nombre_com = $this->datos_empresa['nombre_comercial_empresa'] ?? ($this->datos_empresa['nombre_empresa'] ?? '');
        $this->Cell(95, 3.5, $nombre_com, 0, 1, 'L');

        $nif_empresa = $this->datos_empresa['nif_empresa'] ?? '';
        if ($nif_empresa && substr($nif_empresa, -4) !== '0000') {
            $this->SetX(8);
            $this->SetFont('helvetica', 'B', 8);
            $this->SetTextColor(44, 62, 80);
            $this->Cell(95, 2.5, 'CIF: ' . $nif_empresa, 0, 1, 'L');
        }

        $this->SetFont('helvetica', '', 7.5);
        $this->SetTextColor(52, 73, 94);

        if (!empty($this->datos_empresa['direccion_fiscal_empresa'])) {
            $this->SetX(8);
            $this->Cell(95, 3, $this->datos_empresa['direccion_fiscal_empresa'], 0, 1, 'L');
        }
        $cp_pob = trim(
            ($this->datos_empresa['cp_fiscal_empresa'] ?? '') . ' ' .
            ($this->datos_empresa['poblacion_fiscal_empresa'] ?? '') .
            (!empty($this->datos_empresa['provincia_fiscal_empresa'])
                ? ' (' . $this->datos_empresa['provincia_fiscal_empresa'] . ')' : '')
        );
        if ($cp_pob) {
            $this->SetX(8);
            $this->Cell(95, 3, $cp_pob, 0, 1, 'L');
        }
        $tel_str = '';
        if (!empty($this->datos_empresa['telefono_empresa'])) {
            $tel_str = 'Tel: ' . $this->datos_empresa['telefono_empresa'];
        }
        if ($tel_str) {
            $this->SetX(8);
            $this->Cell(95, 2.5, $tel_str, 0, 1, 'L');
        }
        if (!empty($this->datos_empresa['email_empresa'])) {
            $this->SetX(8);
            $this->Cell(95, 2.5, $this->datos_empresa['email_empresa'], 0, 1, 'L');
        }

        // ── CAJA INFO FACTURA (fondo naranja) ─────────────────────
        $y_info = $this->GetY() + 1;
        $this->SetFillColor($this->cr, $this->cg, $this->cb);
        $this->SetTextColor(255, 255, 255);
        $this->SetFont('helvetica', 'B', 8.5);
        $this->SetXY(8, $y_info);
        $this->Cell(95, 10, '', 0, 0, 'L', true);

        $fecha_fmt = date('d/m/Y', strtotime($this->datos_factura['fecha_factura_agrupada']));

        // Línea 1: Nº factura + Fecha (igual que factura real)
        $this->SetXY(9, $y_info + 1);
        $info_linea1 = 'Nº: ' . $this->datos_factura['numero_factura_agrupada'] . '  |  F: ' . $fecha_fmt;
        $this->Cell(93, 3, $info_linea1, 0, 1, 'L');

        $this->SetTextColor(0, 0, 0);

        // ── BOX CLIENTE (estilo factura real) ─────────────────────
        $col2_x     = 108;
        $col2_width = 94;
        $box_y      = $y_start;

        $client_box_height = 26;
        if (!empty($this->datos_factura['nombre_contacto_cliente'])) {
            $client_box_height += 10;
        }

        // Fondo + borde
        $this->SetFillColor(248, 249, 250);
        $this->Rect($col2_x, $box_y, $col2_width, $client_box_height, 'F');
        $this->SetDrawColor(39, 174, 96);
        $this->SetLineWidth(0.5);
        $this->Rect($col2_x, $box_y, $col2_width, $client_box_height);
        $this->SetLineWidth(0.2);

        // Encabezado "CLIENTE"
        $this->SetXY($col2_x + 2, $box_y + 1.5);
        $this->SetFont('helvetica', 'B', 9);
        $this->SetTextColor(44, 62, 80);
        $this->Cell($col2_width - 4, 3.5, 'CLIENTE', 0, 1, 'L');

        // Línea separadora verde
        $this->SetDrawColor(39, 174, 96);
        $this->Line($col2_x + 2, $box_y + 5.5, $col2_x + $col2_width - 2, $box_y + 5.5);

        $this->SetXY($col2_x + 2, $box_y + 6.5);
        $this->SetFont('helvetica', '', 8);
        $this->SetTextColor(52, 73, 94);

        // Nombre cliente
        $nombre_completo = trim($this->datos_factura['nombre_cliente'] ?? '');
        if (!empty($nombre_completo)) {
            $this->SetFont('helvetica', 'B', 8.5);
            $this->Cell($col2_width - 4, 3, $nombre_completo, 0, 1, 'L');
            $this->SetX($col2_x + 2);
            $this->SetFont('helvetica', '', 7.5);
        }

        // NIF/CIF
        if (!empty($this->datos_factura['nif_cliente'])) {
            $this->SetFont('helvetica', '', 7.5);
            $this->Cell(15, 2.5, 'NIF/CIF:', 0, 0, 'L');
            $this->SetFont('helvetica', 'B', 7.5);
            $this->Cell($col2_width - 19, 2.5, $this->datos_factura['nif_cliente'], 0, 1, 'L');
            $this->SetX($col2_x + 2);
        }

        // Dirección
        if (!empty($this->datos_factura['direccion_cliente'])) {
            $this->SetFont('helvetica', '', 7.5);
            $this->Cell(15, 2.5, 'Direccion:', 0, 0, 'L');
            $dir_cli = $this->datos_factura['direccion_cliente'];
            $partes  = [];
            if (!empty($this->datos_factura['cp_cliente']))       $partes[] = $this->datos_factura['cp_cliente'];
            if (!empty($this->datos_factura['poblacion_cliente'])) $partes[] = $this->datos_factura['poblacion_cliente'];
            if (!empty($partes)) $dir_cli .= ', ' . implode(' ', $partes);
            $this->MultiCell($col2_width - 19, 2.5, $dir_cli, 0, 'L');
            $this->SetX($col2_x + 2);
        }

        // Email
        if (!empty($this->datos_factura['email_cliente'])) {
            $this->SetFont('helvetica', '', 7.5);
            $this->Cell(15, 2.5, 'Email:', 0, 0, 'L');
            $this->Cell($col2_width - 19, 2.5, $this->datos_factura['email_cliente'], 0, 1, 'L');
            $this->SetX($col2_x + 2);
        }

        // Teléfono
        if (!empty($this->datos_factura['telefono_cliente'])) {
            $this->SetFont('helvetica', '', 7.5);
            $this->Cell(15, 2.5, 'Telefono:', 0, 0, 'L');
            $this->Cell($col2_width - 19, 2.5, $this->datos_factura['telefono_cliente'], 0, 1, 'L');
            $this->SetX($col2_x + 2);
        }

        // A la atención de (contacto del primer presupuesto)
        if (!empty($this->datos_factura['nombre_contacto_cliente'])) {
            $this->Ln(1);
            $this->SetXY($col2_x + 2, $this->GetY());
            $this->SetFont('helvetica', 'B', 8);
            $this->SetTextColor(39, 174, 96);
            $this->Cell($col2_width - 4, 3, 'A la atencion de:', 0, 1, 'L');

            $this->SetX($col2_x + 2);
            $this->SetFont('helvetica', '', 7.5);
            $this->SetTextColor(52, 73, 94);

            $nombre_contacto = trim(
                ($this->datos_factura['nombre_contacto_cliente'] ?? '') . ' ' .
                ($this->datos_factura['apellidos_contacto_cliente'] ?? '')
            );
            if (!empty($nombre_contacto)) {
                $this->Cell(15, 2.5, 'Nombre:', 0, 0, 'L');
                $this->SetFont('helvetica', 'B', 7.5);
                $this->Cell($col2_width - 19, 2.5, $nombre_contacto, 0, 1, 'L');
                $this->SetX($col2_x + 2);
                $this->SetFont('helvetica', '', 7.5);
            }
            if (!empty($this->datos_factura['telefono_contacto_cliente'])) {
                $this->Cell(15, 2.5, 'Telefono:', 0, 0, 'L');
                $this->Cell($col2_width - 19, 2.5, $this->datos_factura['telefono_contacto_cliente'], 0, 1, 'L');
                $this->SetX($col2_x + 2);
            }
            if (!empty($this->datos_factura['email_contacto_cliente'])) {
                $this->Cell(15, 2.5, 'Email:', 0, 0, 'L');
                $this->Cell($col2_width - 19, 2.5, $this->datos_factura['email_contacto_cliente'], 0, 1, 'L');
            }
        }

        $this->SetTextColor(0, 0, 0);
    }

    // ─────────────────────────────────────────────────────────────
    // PIE (repetido en cada página)
    // ─────────────────────────────────────────────────────────────
    public function Footer()
    {
        if (!empty($this->texto_pie_empresa)) {
            $this->SetY(-20);
            $this->SetDrawColor(44, 62, 80);
            $this->SetLineWidth(0.5);
            $this->Line(8, $this->GetY(), 202, $this->GetY());
            $this->SetY($this->GetY() + 1);
            $this->SetFont('helvetica', '', 7);
            $this->SetTextColor(99, 110, 114);
            $this->MultiCell(0, 3, $this->texto_pie_empresa, 0, 'C');
        }
        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 8);
        $this->SetTextColor(0, 0, 0);
        $this->Cell(0, 10, 'Página ' . $this->getAliasNumPage() . ' de ' . $this->getAliasNbPages(), 0, 0, 'C');
    }
}

// ══════════════════════════════════════════════════════════════════
// INSTANCIAS
// ══════════════════════════════════════════════════════════════════
$registro        = new RegistroActividad();
$facturaAgrupada = new FacturaAgrupada();
$kitModel        = new Kit();

$op = $_GET['op'] ?? '';

switch ($op) {

    // ══════════════════════════════════════════════════════════════
    // GENERAR / REGENERAR
    // ══════════════════════════════════════════════════════════════
    case "generar":
    case "regenerar":
        $id = (int)($_POST['id_factura_agrupada'] ?? 0);
        if (!$id) {
            ob_end_clean();
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Falta id_factura_agrupada.'], JSON_UNESCAPED_UNICODE);
            break;
        }

        try {
            // 1. Cabecera
            $cabecera = $facturaAgrupada->get_datos_cabecera_agrupada($id);
            if (!$cabecera) {
                ob_end_clean();
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Factura agrupada no encontrada.'], JSON_UNESCAPED_UNICODE);
                break;
            }

            // 2. Líneas agrupadas por presupuesto
            $grupos = $facturaAgrupada->get_lineas_pdf_agrupada($id);

            // 3. Logo
            $mostrar_logo = false;
            $path_logo    = '';
            if (!empty($cabecera['logotipo_empresa'])) {
                $logo_name = ltrim($cabecera['logotipo_empresa'], '/');
                if (strpos($logo_name, 'public/') === 0) {
                    $logo_name = substr($logo_name, 7);
                }
                $path_logo_abs = __DIR__ . '/../public/' . $logo_name;
                if (file_exists($path_logo_abs)) {
                    $mostrar_logo = true;
                    $path_logo    = realpath($path_logo_abs);
                }
            }

            // 4. Construir PDF
            if (function_exists('opcache_invalidate')) {
                opcache_invalidate(__FILE__, true);
            }
            $pdf = _generar_pdf_agrupada($cabecera, $grupos, $mostrar_logo, $path_logo);

            // 5. Guardar a disco
            $dir_guardado = __DIR__ . '/../public/documentos/facturas_agrupadas/';
            if (!is_dir($dir_guardado)) {
                mkdir($dir_guardado, 0755, true);
            }
            $nombre_archivo = preg_replace('/[^A-Za-z0-9_\-]/', '_', $cabecera['numero_factura_agrupada']) . '.pdf';
            $ruta_absoluta  = $dir_guardado . $nombre_archivo;
            $ruta_relativa  = 'public/documentos/facturas_agrupadas/' . $nombre_archivo;

            $pdf_string = $pdf->Output($nombre_archivo, 'S');
            file_put_contents($ruta_absoluta, $pdf_string);

            // 6. Actualizar ruta en BD
            $facturaAgrupada->update_pdf_path($id, $ruta_relativa);

            $registro->registrarActividad(
                'admin', 'impresion_factura_agrupada.php', $op,
                "PDF factura agrupada {$cabecera['numero_factura_agrupada']} generado. ID: $id", 'info'
            );

            ob_end_clean();
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'success'             => true,
                'id_factura_agrupada' => $id,
                'numero'              => $cabecera['numero_factura_agrupada'],
                'url_pdf'             => $ruta_relativa,
            ], JSON_UNESCAPED_UNICODE);

        } catch (\Throwable $e) {
            $registro->registrarActividad(
                'admin', 'impresion_factura_agrupada.php', $op,
                "Error: " . $e->getMessage(), 'error'
            );
            ob_end_clean();
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false, 'message' => 'Error al generar el PDF: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
        }
        break;

    // ══════════════════════════════════════════════════════════════
    // DESCARGAR
    // ══════════════════════════════════════════════════════════════
    case "descargar":
        $id = (int)($_GET['id'] ?? $_POST['id_factura_agrupada'] ?? 0);
        if (!$id) {
            ob_end_clean();
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Falta id_factura_agrupada.'], JSON_UNESCAPED_UNICODE);
            break;
        }

        $fa = $facturaAgrupada->get_factura_agrupadaxid($id);
        if (!$fa) {
            ob_end_clean();
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Factura agrupada no encontrada.'], JSON_UNESCAPED_UNICODE);
            break;
        }

        // Si no tiene PDF, generarlo al vuelo
        if (empty($fa['pdf_path_agrupada'])) {
            // Redirigir a generar via POST — aquí hacemos inline
            $_POST['id_factura_agrupada'] = $id;
            $_GET['op'] = 'generar';
            // Cambiar op y reentrar no es posible en PHP; generamos directamente
            try {
                $cabecera = $facturaAgrupada->get_datos_cabecera_agrupada($id);
                $grupos   = $facturaAgrupada->get_lineas_pdf_agrupada($id);
                $mostrar_logo = false;
                $path_logo    = '';
                if (!empty($cabecera['logotipo_empresa'])) {
                    $logo_name = ltrim($cabecera['logotipo_empresa'], '/');
                    if (strpos($logo_name, 'public/') === 0) { $logo_name = substr($logo_name, 7); }
                    $abs = __DIR__ . '/../public/' . $logo_name;
                    if (file_exists($abs)) { $mostrar_logo = true; $path_logo = realpath($abs); }
                }

                $pdf  = _generar_pdf_agrupada($cabecera, $grupos, $mostrar_logo, $path_logo);
                $dir  = __DIR__ . '/../public/documentos/facturas_agrupadas/';
                if (!is_dir($dir)) { mkdir($dir, 0755, true); }
                $nombre_archivo = preg_replace('/[^A-Za-z0-9_\-]/', '_', $cabecera['numero_factura_agrupada']) . '.pdf';
                $ruta_abs = $dir . $nombre_archivo;
                $ruta_rel = 'public/documentos/facturas_agrupadas/' . $nombre_archivo;
                file_put_contents($ruta_abs, $pdf->Output($nombre_archivo, 'S'));
                $facturaAgrupada->update_pdf_path($id, $ruta_rel);
                $fa['pdf_path_agrupada'] = $ruta_rel;

            } catch (\Throwable $e) {
                ob_end_clean();
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Error al generar PDF: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
                break;
            }
        }

        $ruta_absoluta = __DIR__ . '/../' . $fa['pdf_path_agrupada'];
        if (!file_exists($ruta_absoluta)) {
            ob_end_clean();
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Archivo PDF no encontrado en disco.'], JSON_UNESCAPED_UNICODE);
            break;
        }

        ob_end_clean();
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="' . basename($ruta_absoluta) . '"');
        header('Content-Length: ' . filesize($ruta_absoluta));
        readfile($ruta_absoluta);
        exit;

    default:
        ob_end_clean();
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => "Operación '$op' no reconocida."], JSON_UNESCAPED_UNICODE);
        break;
}

// ══════════════════════════════════════════════════════════════════
// FUNCIÓN: _generar_pdf_agrupada
// ══════════════════════════════════════════════════════════════════
function _generar_pdf_agrupada(
    array $cabecera,
    array $grupos,
    bool  $mostrar_logo,
    string $path_logo
): MYPDF_AGRUPADA {

    $es_abono = (bool)$cabecera['is_abono_agrupada'];
    $fecha_hoy = date('d/m/Y');

    // ── Referencia al modelo Kit (instanciado globalmente) ────────
    global $kitModel;
    global $registro;
    $registro->registrarActividad(
        'admin', 'impresion_factura_agrupada.php', '_generar_pdf_agrupada',
        'NUEVO_CODIGO_v2 ejecutando — ts:' . time(), 'info'
    );

    // ── Config empresa ────────────────────────────────────────────
    $mostrar_subtotales_fecha = !isset($cabecera['mostrar_subtotales_fecha_presupuesto_empresa'])
        ? true
        : ($cabecera['mostrar_subtotales_fecha_presupuesto_empresa'] == 1);
    $permitir_descuentos = !isset($cabecera['permitir_descuentos_lineas_empresa'])
        ? true
        : ($cabecera['permitir_descuentos_lineas_empresa'] == 1);

    $pdf = new MYPDF_AGRUPADA('P', 'mm', 'A4', true, 'UTF-8', false);
    $pdf->SetCreator('MDR ERP');
    $pdf->SetAuthor($cabecera['nombre_empresa'] ?? 'MDR');
    $pdf->SetTitle(($es_abono ? 'Abono ' : 'Factura Agrupada ') . $cabecera['numero_factura_agrupada'] . ' [v2]');

    $pdf->setPrintHeader(true);
    $pdf->setPrintFooter(true);
    $pdf->SetHeaderMargin(5);
    $pdf->SetFooterMargin(15);
    $pdf->SetAutoPageBreak(true, 25);
    $pdf->SetMargins(8, 95, 8);

    // Pasar datos a cabecera
    $pdf->setDatosHeader(
        $cabecera,
        $cabecera,
        $mostrar_logo,
        $path_logo,
        $cabecera['texto_pie_factura_empresa'] ?? ''
    );

    $pdf->AddPage();

    // ── Colores base ─────────────────────────────────────────────
    $cr = 39; $cg = 174; $cb = 96;  // verde (igual que box DATOS DEL CLIENTE)

    // ══════════════════════════════════════════════════════════════
    // BLOQUE: MOTIVO ABONO (solo si es abono)
    // ══════════════════════════════════════════════════════════════
    if ($es_abono && !empty($cabecera['motivo_abono_agrupada'])) {
        $pdf->SetFont('helvetica', 'B', 8.5);
        $pdf->SetFillColor(255, 243, 230);
        $pdf->SetTextColor($cr, $cg, $cb);
        $pdf->Cell(0, 5, 'NOTA DE ABONO / RECTIFICACIÓN', 1, 1, 'C', true);
        $pdf->SetFont('helvetica', '', 8);
        $pdf->SetTextColor(44, 62, 80);
        $pdf->SetFillColor(255, 250, 245);
        $pdf->Cell(0, 6, 'Motivo: ' . $cabecera['motivo_abono_agrupada'], 'LRB', 1, 'L', true);
        $pdf->Ln(4);
    }

    // ══════════════════════════════════════════════════════════════
    // OBSERVACIONES (si las hay)
    // ══════════════════════════════════════════════════════════════
    if (!empty($cabecera['observaciones_agrupada'])) {
        $pdf->SetFont('helvetica', 'I', 8);
        $pdf->SetTextColor(100, 100, 100);
        $pdf->MultiCell(0, 4, $cabecera['observaciones_agrupada'], 0, 'L');
        $pdf->Ln(3);
    }

    // ══════════════════════════════════════════════════════════════
    // SECCIONES POR PRESUPUESTO
    // ══════════════════════════════════════════════════════════════
    $desglose_iva = [];

    foreach ($grupos as $grupo) {
        $ppto   = $grupo['presupuesto'];
        $lineas = $grupo['lineas'];

        // ── Cabecera de presupuesto (naranja) ───────────────────────
        $num_ppto    = $ppto['numero_presupuesto'] ?? '';
        $evento_ppto = $ppto['nombre_evento_presupuesto'] ?? '';
        $fecha_ppto  = !empty($ppto['fecha_presupuesto'])
            ? date('d/m/Y', strtotime($ppto['fecha_presupuesto']))
            : '';

        $cabecera_txt = 'Presupuesto ' . $num_ppto;
        if ($evento_ppto) $cabecera_txt .= ' — ' . $evento_ppto;
        if ($fecha_ppto)  $cabecera_txt .= '   (Fecha: ' . $fecha_ppto . ')';

        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->SetFillColor($cr, $cg, $cb);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(0, 6, $cabecera_txt, 0, 1, 'L', true);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Ln(1);

        if (empty($lineas)) {
            $pdf->SetFont('helvetica', 'I', 8);
            $pdf->SetTextColor(150, 150, 150);
            $pdf->Cell(0, 5, 'Sin líneas de detalle disponibles.', 0, 1, 'C');
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Ln(2);
            continue;
        }

        // ── Agrupar líneas por fecha_inicio → ubicación ─────────────
        $lineas_agrupadas_ppto = [];
        foreach ($lineas as $linea) {
            $fecha_inicio = $linea['fecha_inicio_linea_ppto'];
            $id_ubicacion = $linea['id_ubicacion'] ?? 0;

            if (!isset($lineas_agrupadas_ppto[$fecha_inicio])) {
                $lineas_agrupadas_ppto[$fecha_inicio] = [
                    'ubicaciones'    => [],
                    'subtotal_fecha' => 0.0,
                ];
            }
            if (!isset($lineas_agrupadas_ppto[$fecha_inicio]['ubicaciones'][$id_ubicacion])) {
                $lineas_agrupadas_ppto[$fecha_inicio]['ubicaciones'][$id_ubicacion] = [
                    'nombre_ubicacion'   => $linea['nombre_ubicacion'] ?? 'Sin ubicación',
                    'lineas'             => [],
                    'subtotal_ubicacion' => 0.0,
                ];
            }

            $base = floatval($linea['base_imponible'] ?? 0);
            if ($base == 0) {
                $coef = floatval($linea['valor_coeficiente_linea_ppto'] ?? 0);
                $dias = floatval($linea['dias_linea'] ?? 1);
                $cant = floatval($linea['cantidad_linea_ppto'] ?? 0);
                $pu   = floatval($linea['precio_unitario_linea_ppto'] ?? 0);
                $dto  = floatval($linea['descuento_linea_ppto'] ?? 0) / 100;
                $base = ($coef > 0) ? $cant * $pu * $coef : $dias * $cant * $pu * (1 - $dto);
            }

            $lineas_agrupadas_ppto[$fecha_inicio]['ubicaciones'][$id_ubicacion]['lineas'][]         = $linea;
            $lineas_agrupadas_ppto[$fecha_inicio]['ubicaciones'][$id_ubicacion]['subtotal_ubicacion'] += $base;
            $lineas_agrupadas_ppto[$fecha_inicio]['subtotal_fecha']                                  += $base;

            // Acumular desglose de IVA consolidado (todos los presupuestos)
            $pct_iva   = floatval($linea['porcentaje_iva_linea_ppto'] ?? 0);
            $iva_linea = floatval($linea['importe_iva'] ?? ($base * $pct_iva / 100));
            if (!isset($desglose_iva[$pct_iva])) {
                $desglose_iva[$pct_iva] = ['base' => 0, 'cuota' => 0];
            }
            $desglose_iva[$pct_iva]['base']  += $base;
            $desglose_iva[$pct_iva]['cuota'] += $iva_linea;
        }

        // ── Análisis montaje/desmontaje por fecha ───────────────────
        $analisis_fechas_ppto = [];
        foreach ($lineas_agrupadas_ppto as $fecha_inicio => $grupo_fecha) {
            $todas_lineas = [];
            foreach ($grupo_fecha['ubicaciones'] as $grupo_ub) {
                $todas_lineas = array_merge($todas_lineas, $grupo_ub['lineas']);
            }
            $total_lineas = count($todas_lineas);

            if ($total_lineas == 0) {
                $analisis_fechas_ppto[$fecha_inicio] = [
                    'ocultar_columnas' => false, 'fecha_montaje' => null,
                    'fecha_desmontaje' => null, 'excepciones' => [],
                ];
                continue;
            }

            $combinaciones = [];
            foreach ($todas_lineas as $l) {
                $fm_raw = $l['fecha_montaje_linea_ppto']    ?? null;
                $fd_raw = $l['fecha_desmontaje_linea_ppto'] ?? null;
                $mtje   = (!empty($fm_raw) && $fm_raw != '0000-00-00') ? date('Y-m-d', strtotime($fm_raw)) : '';
                $dsmtje = (!empty($fd_raw) && $fd_raw != '0000-00-00') ? date('Y-m-d', strtotime($fd_raw)) : '';
                $clave  = $mtje . '|' . $dsmtje;
                if (!isset($combinaciones[$clave])) {
                    $combinaciones[$clave] = ['count' => 0, 'montaje' => $mtje, 'desmontaje' => $dsmtje, 'indices' => []];
                }
                $combinaciones[$clave]['count']++;
                $combinaciones[$clave]['indices'][] = $l['id_linea_ppto'];
            }

            $max_count = 0; $comb_pred = null;
            foreach ($combinaciones as $datos_comb) {
                if ($datos_comb['count'] > $max_count) {
                    $max_count = $datos_comb['count']; $comb_pred = $datos_comb;
                }
            }

            if (($max_count / $total_lineas) * 100 >= 30) {
                $excepciones = [];
                foreach ($todas_lineas as $l) {
                    if (!in_array($l['id_linea_ppto'], $comb_pred['indices'])) {
                        $excepciones[] = $l['id_linea_ppto'];
                    }
                }
                $analisis_fechas_ppto[$fecha_inicio] = [
                    'ocultar_columnas' => true,
                    'fecha_montaje'    => $comb_pred['montaje'],
                    'fecha_desmontaje' => $comb_pred['desmontaje'],
                    'excepciones'      => $excepciones,
                ];
            } else {
                $analisis_fechas_ppto[$fecha_inicio] = [
                    'ocultar_columnas' => false, 'fecha_montaje' => null,
                    'fecha_desmontaje' => null, 'excepciones' => [],
                ];
            }
        }

        // ── Renderizar tablas agrupadas por fecha + ubicación ────────
        $pdf->SetFont('helvetica', 'B', 8);

        foreach ($lineas_agrupadas_ppto as $fecha_inicio => $grupo_fecha) {

            // Cabecera de fecha (azul igual que factura_final)
            $fecha_formateada = date('d/m/Y', strtotime($fecha_inicio));
            $info_fechas      = $analisis_fechas_ppto[$fecha_inicio] ?? ['ocultar_columnas' => false];
            $texto_cabecera   = 'Fecha de inicio: ' . $fecha_formateada;

            if ($info_fechas['ocultar_columnas']) {
                $mtje_f   = !empty($info_fechas['fecha_montaje'])    ? date('d/m/Y', strtotime($info_fechas['fecha_montaje']))    : '-';
                $dsmtje_f = !empty($info_fechas['fecha_desmontaje']) ? date('d/m/Y', strtotime($info_fechas['fecha_desmontaje'])) : '-';
                $texto_cabecera .= ' | Montaje: ' . $mtje_f . ' | Desmontaje: ' . $dsmtje_f;
            }

            $pdf->SetFillColor(52, 152, 219);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->SetFont('helvetica', 'B', 9);
            $pdf->Cell(194, 6, $texto_cabecera, 0, 1, 'L', true);
            $pdf->SetFillColor(255, 255, 255);
            $pdf->SetTextColor(0, 0, 0);

            foreach ($grupo_fecha['ubicaciones'] as $id_ubicacion => $grupo_ubicacion) {

                $pdf->SetFont('helvetica', 'B', 8);
                $pdf->Cell(194, 5, $grupo_ubicacion['nombre_ubicacion'], 0, 1, 'L');
                $pdf->Ln(1);

                // Cabecera de columnas
                $ocultar_cols      = $info_fechas['ocultar_columnas'];
                $ancho_descripcion = $ocultar_cols ? 79 : 49;
                if (!$permitir_descuentos) {
                    $ancho_descripcion += 12;
                }

                $pdf->SetFont('helvetica', 'B', 8);
                $pdf->SetFillColor(240, 240, 240);
                $pdf->SetDrawColor(200, 200, 200);

                $pdf->Cell(17, 6, 'Inicio',  1, 0, 'C', 1);
                $pdf->Cell(17, 6, 'Fin',     1, 0, 'C', 1);
                if (!$ocultar_cols) {
                    $pdf->Cell(15, 6, 'Mtje',   1, 0, 'C', 1);
                    $pdf->Cell(15, 6, 'Dsmtje', 1, 0, 'C', 1);
                }
                $pdf->Cell(8,  6, 'Días',         1, 0, 'C', 1);
                $pdf->Cell(10, 6, 'Coef.',         1, 0, 'C', 1);
                $pdf->Cell($ancho_descripcion, 6, 'Descripción', 1, 0, 'C', 1);
                $pdf->Cell(12, 6, 'Cant.',         1, 0, 'C', 1);
                $pdf->Cell(15, 6, 'P.Unit.',       1, 0, 'C', 1);
                if ($permitir_descuentos) {
                    $pdf->Cell(12, 6, '%Dto',       1, 0, 'C', 1);
                }
                $pdf->Cell(24, 6, 'Importe(€)',    1, 1, 'C', 1);
                $pdf->SetDrawColor(0, 0, 0);

                $pdf->SetFont('helvetica', '', 7);

                foreach ($grupo_ubicacion['lineas'] as $linea) {
                    $pdf->SetFont('helvetica', '', 7);

                    $fecha_inicio_linea = !empty($linea['fecha_inicio_linea_ppto'])    ? date('d/m/Y', strtotime($linea['fecha_inicio_linea_ppto']))    : '-';
                    $fecha_fin_linea    = !empty($linea['fecha_fin_linea_ppto'])        ? date('d/m/Y', strtotime($linea['fecha_fin_linea_ppto']))        : '-';
                    $fecha_montaje      = !empty($linea['fecha_montaje_linea_ppto'])    ? date('d/m/Y', strtotime($linea['fecha_montaje_linea_ppto']))    : '-';
                    $fecha_desmontaje   = !empty($linea['fecha_desmontaje_linea_ppto']) ? date('d/m/Y', strtotime($linea['fecha_desmontaje_linea_ppto'])) : '-';

                    $es_excepcion = in_array($linea['id_linea_ppto'], $info_fechas['excepciones'] ?? []);
                    $es_kit       = (isset($linea['es_kit_articulo']) && $linea['es_kit_articulo'] == 1);

                    $descripcion     = $linea['descripcion_linea_ppto'] ?? '';
                    $cantidad        = floatval($linea['cantidad_linea_ppto'] ?? 0);
                    $precio_unitario = floatval($linea['precio_unitario_linea_ppto'] ?? 0);
                    $descuento       = floatval($linea['descuento_linea_ppto'] ?? 0);
                    $base_imponible  = floatval($linea['base_imponible'] ?? 0);

                    if (!$permitir_descuentos) {
                        $descuento = 0;
                        $coef_r = floatval($linea['valor_coeficiente_linea_ppto'] ?? 0);
                        $dias_r  = floatval($linea['dias_linea'] ?? 1);
                        $base_imponible = ($coef_r > 0)
                            ? $cantidad * $precio_unitario * $coef_r
                            : $dias_r * $cantidad * $precio_unitario;
                    }

                    $altura_texto = $pdf->getStringHeight($ancho_descripcion - 1, $descripcion);
                    $altura_fila  = ($altura_texto > 5) ? ceil($altura_texto) + 1 : 5;
                    $necesita_dos = ($altura_fila > 5);

                    $espacio_disponible = $pdf->getPageHeight() - $pdf->GetY() - $pdf->getBreakMargin();
                    if ($espacio_disponible < $altura_fila + 5) {
                        $pdf->AddPage();
                    }

                    $x_inicial = $pdf->GetX();
                    $y_inicial = $pdf->GetY();

                    $pdf->SetDrawColor(200, 200, 200);

                    $linea_negativa = ($cantidad < 0 || $base_imponible < 0);
                    if ($linea_negativa) {
                        $pdf->SetTextColor(0, 102, 204);
                    }

                    $pdf->Cell(17, $altura_fila, $fecha_inicio_linea, 1, 0, 'C');
                    $pdf->Cell(17, $altura_fila, $fecha_fin_linea,    1, 0, 'C');
                    if (!$ocultar_cols) {
                        $pdf->Cell(15, $altura_fila, $fecha_montaje,    1, 0, 'C');
                        $pdf->Cell(15, $altura_fila, $fecha_desmontaje, 1, 0, 'C');
                    }
                    $pdf->Cell(8,  $altura_fila, $linea['dias_linea'] ?? '0', 1, 0, 'C');
                    $pdf->Cell(10, $altura_fila, number_format(floatval($linea['valor_coeficiente_linea_ppto'] ?? 1), 2), 1, 0, 'C');

                    $x_desc = $pdf->GetX();
                    if ($necesita_dos) {
                        $pdf->Rect($x_desc, $y_inicial, $ancho_descripcion, $altura_fila, 'D');
                        $pdf->SetXY($x_desc + 0.5, $y_inicial + 0.5);
                        $pdf->MultiCell($ancho_descripcion - 1, 4.5, $descripcion, 0, 'L');
                    } else {
                        $pdf->Cell($ancho_descripcion, $altura_fila, $descripcion, 1, 0, 'L');
                    }
                    $pdf->SetXY($x_desc + $ancho_descripcion, $y_inicial);

                    $pdf->Cell(12, $altura_fila, number_format($cantidad, 0), 1, 0, 'C');
                    $pdf->Cell(15, $altura_fila, number_format($precio_unitario, 2, ',', '.'), 1, 0, 'R');
                    if ($permitir_descuentos) {
                        $pdf->Cell(12, $altura_fila, number_format($descuento, 0), 1, 0, 'C');
                    }
                    $pdf->Cell(24, $altura_fila, number_format($base_imponible, 2, ',', '.'), 1, 1, 'R');

                    if ($linea_negativa) {
                        $pdf->SetTextColor(0, 0, 0);
                    }
                    $pdf->SetDrawColor(0, 0, 0);
                    $pdf->SetXY($x_inicial, $y_inicial + $altura_fila);

                    // Observaciones de línea
                    $obs_mostrar = '';
                    if (!empty($linea['observaciones_linea_ppto']) && trim($linea['observaciones_linea_ppto']) != '') {
                        $obs_mostrar = trim($linea['observaciones_linea_ppto']);
                    }
                    if ($es_excepcion && $ocultar_cols) {
                        $obs_fechas  = 'Mtje: ' . $fecha_montaje . ' - Dsmtje: ' . $fecha_desmontaje;
                        $obs_mostrar = !empty($obs_mostrar) ? $obs_mostrar . ' | ' . $obs_fechas : $obs_fechas;
                    }
                    if (!empty($obs_mostrar)) {
                        $y_obs = $pdf->GetY();
                        $pdf->SetFont('helvetica', '', 6.5);
                        $pdf->SetTextColor(80, 80, 80);
                        $pdf->MultiCell(170, 4, '    ' . $obs_mostrar, 0, 'L', false, 1, '', $y_obs);
                        $pdf->SetTextColor(0, 0, 0);
                        $pdf->SetFont('helvetica', '', 7);
                    }

                    // Componentes del KIT
                    if ($es_kit && !empty($linea['id_articulo']) &&
                        isset($linea['ocultar_detalle_kit_linea_ppto']) &&
                        $linea['ocultar_detalle_kit_linea_ppto'] == 0) {

                        $componentes = $kitModel->get_kits_by_articulo_maestro($linea['id_articulo']);
                        if (!empty($componentes)) {
                            $compsActivos = array_filter($componentes, function ($c) {
                                return isset($c['activo_articulo_componente']) && $c['activo_articulo_componente'] != 0;
                            });
                            $ancho_fin_kit = 15 + ($permitir_descuentos ? 12 : 0) + 24;
                            foreach ($compsActivos as $comp) {
                                $pdf->SetFont('helvetica', 'I', 6);
                                $pdf->SetTextColor(100, 100, 100);
                                $pdf->Cell(17, 4, '', 0, 0, 'C');
                                $pdf->Cell(17, 4, '', 0, 0, 'C');
                                if (!$ocultar_cols) {
                                    $pdf->Cell(15, 4, '', 0, 0, 'C');
                                    $pdf->Cell(15, 4, '', 0, 0, 'C');
                                }
                                $pdf->Cell(8,  4, '', 0, 0, 'C');
                                $pdf->Cell(10, 4, '', 0, 0, 'C');
                                $cant_comp   = $comp['cantidad_kit'] ?? $comp['total_componente_kit'] ?? 1;
                                $nombre_comp = $comp['nombre_articulo_componente'] ?? $comp['nombre_articulo'] ?? 'Sin nombre';
                                $pdf->Cell($ancho_descripcion, 4, '    • ' . $cant_comp . 'x ' . $nombre_comp, 0, 0, 'L');
                                $pdf->Cell(12, 4, '', 0, 0, 'C');
                                $pdf->Cell($ancho_fin_kit, 4, '', 0, 1, 'R');
                                $pdf->SetFont('helvetica', '', 7);
                                $pdf->SetTextColor(0, 0, 0);
                            }
                        }
                    }

                } // fin foreach lineas

                // Subtotal por ubicación
                $pdf->SetFont('helvetica', 'B', 7);
                $pdf->SetFillColor(245, 245, 245);
                $pdf->SetDrawColor(200, 200, 200);
                $pdf->Cell(170, 5, 'Subtotal ' . $grupo_ubicacion['nombre_ubicacion'], 1, 0, 'R', 1);
                $pdf->Cell(24,  5, number_format($grupo_ubicacion['subtotal_ubicacion'], 2, ',', '.'), 1, 1, 'R', 1);
                $pdf->SetDrawColor(0, 0, 0);
                $pdf->Ln(2);

            } // fin foreach ubicaciones

            // Subtotal por fecha (condicional)
            if ($mostrar_subtotales_fecha) {
                $pdf->SetFont('helvetica', 'B', 8);
                $pdf->SetFillColor(220, 220, 220);
                $pdf->SetDrawColor(200, 200, 200);
                $pdf->Cell(170, 6, 'Subtotal Fecha ' . $fecha_formateada, 1, 0, 'R', 1);
                $pdf->Cell(24,  6, number_format($grupo_fecha['subtotal_fecha'], 2, ',', '.'), 1, 1, 'R', 1);
                $pdf->SetDrawColor(0, 0, 0);
                $pdf->Ln(3);
            } else {
                $pdf->Ln(2);
            }

        } // fin foreach fechas

        // ── Subtotal por presupuesto (naranja) ──────────────────────
        $subtotal_ppto = 0.0;
        foreach ($lineas_agrupadas_ppto as $gf) {
            $subtotal_ppto += $gf['subtotal_fecha'];
        }

        $pdf->SetFont('helvetica', 'B', 7.5);
        $pdf->SetFillColor(232, 249, 240); // verde claro
        $pdf->SetTextColor($cr, $cg, $cb);
        $pdf->SetDrawColor(200, 200, 200);
        $pdf->Cell(170, 5, 'Subtotal Presupuesto ' . $num_ppto, 1, 0, 'R', 1);
        $pdf->Cell(24,  5, number_format($subtotal_ppto, 2, ',', '.'), 1, 1, 'R', 1);
        $pdf->SetDrawColor(0, 0, 0);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Ln(4);

    } // fin foreach grupos
    ksort($desglose_iva);

    // ══════════════════════════════════════════════════════════════
    // BLOQUE TOTALES CONSOLIDADOS
    // ══════════════════════════════════════════════════════════════
    $total_base      = (float)$cabecera['total_base_agrupada'];
    $total_iva       = (float)$cabecera['total_iva_agrupada'];
    $total_bruto     = (float)$cabecera['total_bruto_agrupada'];
    $total_anticipos = (float)$cabecera['total_anticipos_agrupada'];
    $total_a_cobrar  = (float)$cabecera['total_a_cobrar_agrupada'];

    $pdf->Ln(5);
    $pdf->SetDrawColor(200, 200, 200);
    $pdf->Line(131, $pdf->GetY(), 202, $pdf->GetY());
    $pdf->Ln(3);

    // Base Imponible
    $pdf->SetFont('helvetica', '', 9);
    $pdf->SetFillColor(248, 249, 250);
    $pdf->SetDrawColor(220, 220, 220);
    $pdf->Cell(130, 6, '', 0, 0);
    $pdf->Cell(44, 6, 'Base Imponible:', 1, 0, 'R', 1);
    $pdf->SetFont('helvetica', 'B', 9);
    $pdf->Cell(20, 6, number_format($total_base, 2, ',', '.') . ' €', 1, 1, 'R', 1);

    // Desglose IVA (si hay más de un tipo)
    if (count($desglose_iva) > 1) {
        $pdf->SetFont('helvetica', '', 8);
        $pdf->Cell(130, 5, '', 0, 0);
        $pdf->Cell(44, 5, 'Desglose de IVA:', 0, 1, 'R');
        foreach ($desglose_iva as $pct => $vals) {
            $pdf->Cell(130, 4, '', 0, 0);
            $pdf->Cell(44, 4, "Base IVA {$pct}%:", 0, 0, 'R');
            $pdf->Cell(20, 4, number_format($vals['base'], 2, ',', '.') . ' €', 0, 1, 'R');
            $pdf->Cell(130, 4, '', 0, 0);
            $pdf->Cell(44, 4, "IVA {$pct}%:", 0, 0, 'R');
            $pdf->Cell(20, 4, number_format($vals['cuota'], 2, ',', '.') . ' €', 0, 1, 'R');
        }
    }

    // Total IVA
    $pdf->SetFont('helvetica', '', 9);
    $pdf->SetFillColor(248, 249, 250);
    $pdf->SetDrawColor(220, 220, 220);
    $pdf->Cell(130, 6, '', 0, 0);
    if (count($desglose_iva) == 1) {
        $pct_unico = key($desglose_iva);
        $pdf->Cell(44, 6, "Total IVA ({$pct_unico}%):", 1, 0, 'R', 1);
    } else {
        $pdf->Cell(44, 6, 'Total IVA:', 1, 0, 'R', 1);
    }
    $pdf->SetFont('helvetica', 'B', 9);
    $pdf->Cell(20, 6, number_format($total_iva, 2, ',', '.') . ' €', 1, 1, 'R', 1);

    $pdf->Ln(2);

    // TOTAL BRUTO destacado
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->SetFillColor($cr, $cg, $cb);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->SetDrawColor($cr, $cg, $cb);
    $pdf->SetLineWidth(0.5);
    $pdf->Cell(130, 8, '', 0, 0);
    $pdf->Cell(34, 8, 'TOTAL BRUTO:', 1, 0, 'R', 1);
    $pdf->Cell(30, 8, number_format($total_bruto, 2, ',', '.') . ' €', 1, 1, 'R', 1);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetDrawColor(0, 0, 0);
    $pdf->SetLineWidth(0.2);

    // Anticipos y saldo a pagar
    if ($total_anticipos > 0) {
        $pdf->Ln(5);
        $pdf->SetFont('helvetica', '', 9);
        $pdf->SetTextColor(44, 62, 80);
        $pdf->Write(5, 'Anticipos previos: ');
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->SetTextColor(192, 57, 43);
        $pdf->Write(5, '-' . number_format($total_anticipos, 2, ',', '.') . ' €');
        $pdf->SetFont('helvetica', '', 9);
        $pdf->SetTextColor(44, 62, 80);
        $pdf->Write(5, ', saldo a pagar: ');
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->SetTextColor(39, 174, 96);
        $pdf->Write(5, number_format(max(0, $total_a_cobrar), 2, ',', '.') . ' €');
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Ln();
    }

    // =====================================================================
    // FORMA DE PAGO Y DATOS BANCARIOS
    // =====================================================================
    if (!empty($cabecera['nombre_pago'])) {
        $pdf->Ln(6);
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->SetTextColor(52, 73, 94);
        $pdf->Cell(40, 5, 'FORMA DE PAGO:', 0, 0, 'L');

        $texto_fp = trim($cabecera['nombre_metodo_pago'] ?? $cabecera['nombre_pago'] ?? '') . '.';
        $pdf->SetFont('helvetica', '', 8);
        $pdf->SetTextColor(70, 70, 70);
        $pdf->MultiCell(160, 4, $texto_fp, 0, 'L');
    }

    // Datos bancarios
    $fp_lower       = strtolower($cabecera['nombre_metodo_pago'] ?? '');
    $es_transf      = (strpos($fp_lower, 'transferencia') !== false);
    $tiene_banco    = (!empty($cabecera['iban_empresa']) || !empty($cabecera['swift_empresa']) || !empty($cabecera['banco_empresa']));
    $mostrar_cuenta = ($cabecera['mostrar_cuenta_bancaria_pdf_presupuesto_empresa'] ?? 1);

    if ($es_transf && $tiene_banco && $mostrar_cuenta) {
        $altura_bloque = 5;
        if (!empty($cabecera['banco_empresa']))  $altura_bloque += 3.5;
        if (!empty($cabecera['iban_empresa']))   $altura_bloque += 3.5;
        if (!empty($cabecera['swift_empresa']))  $altura_bloque += 3.5;

        if (($pdf->GetY() + $altura_bloque) > 270) {
            $pdf->AddPage();
            $pdf->SetY(15);
        }
        $pdf->Ln(2);
        $x0 = $pdf->GetX();
        $y0 = $pdf->GetY();
        $pdf->SetFillColor(245, 245, 245);
        $pdf->SetDrawColor(180, 180, 180);
        $pdf->Rect($x0, $y0, 195, $altura_bloque, 'DF');
        $pdf->SetXY($x0 + 2, $y0 + 1.5);
        $pdf->SetFont('helvetica', 'B', 7);
        $pdf->SetTextColor(52, 73, 94);
        $pdf->Cell(189, 3, 'DATOS BANCARIOS PARA TRANSFERENCIA', 0, 1, 'L', false);
        $ya = $pdf->GetY() + 0.5;

        if (!empty($cabecera['banco_empresa'])) {
            $pdf->SetXY($x0 + 2, $ya);
            $pdf->SetFont('helvetica', '', 6);
            $pdf->SetTextColor(70, 70, 70);
            $pdf->Cell(20, 3, 'Banco:', 0, 0, 'L');
            $pdf->SetFont('helvetica', 'B', 7);
            $pdf->Cell(160, 3, $cabecera['banco_empresa'], 0, 1, 'L');
            $ya += 3.5;
        }
        if (!empty($cabecera['iban_empresa'])) {
            $pdf->SetXY($x0 + 2, $ya);
            $pdf->SetFont('helvetica', '', 6);
            $pdf->SetTextColor(70, 70, 70);
            $pdf->Cell(20, 3, 'IBAN:', 0, 0, 'L');
            $pdf->SetFont('helvetica', 'B', 7);
            $iban_fmt = wordwrap(str_replace(' ', '', $cabecera['iban_empresa']), 4, ' ', true);
            $pdf->Cell(160, 3, $iban_fmt, 0, 1, 'L');
            $ya += 3.5;
        }
        if (!empty($cabecera['swift_empresa'])) {
            $pdf->SetXY($x0 + 2, $ya);
            $pdf->SetFont('helvetica', '', 6);
            $pdf->SetTextColor(70, 70, 70);
            $pdf->Cell(20, 3, 'SWIFT:', 0, 0, 'L');
            $pdf->SetFont('helvetica', 'B', 7);
            $pdf->Cell(160, 3, $cabecera['swift_empresa'], 0, 1, 'L');
        }
        $pdf->SetY($y0 + $altura_bloque + 1.5);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetDrawColor(0, 0, 0);
    }

    // ── Texto legal / condiciones ─────────────────────────────────
    if (!empty($cabecera['texto_legal_factura_empresa'] ?? '')) {
        $pdf->Ln(6);
        $pdf->SetFont('helvetica', 'I', 7);
        $pdf->SetTextColor(130, 130, 130);
        $pdf->MultiCell(0, 3.5, $cabecera['texto_legal_factura_empresa'], 0, 'L');
    }

    return $pdf;
}
?>
