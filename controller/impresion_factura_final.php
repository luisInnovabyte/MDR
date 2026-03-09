<?php
/**
 * impresion_factura_final.php
 *
 * Genera FACTURA FINAL (pago total o liquidación/resto).
 * Tipo DB: factura_final
 * SP tipo: factura (misma serie F que factura_anticipo)
 * Título PDF: "FACTURA"
 * Color: azul marino (41, 128, 185)
 * Contenido: detalle completo de líneas del presupuesto
 *            + bloque informativo de anticipos previos (si los hay)
 *
 * op=generar (POST)
 *   - id_presupuesto    (requerido)
 *   - id_empresa        (requerido — empresa real emisora)
 *   - id_pago_ppto      (requerido — pago origen)
 *   - tipo_contenido    (requerido — 'total' siempre para esta factura)
 *   - idioma            (opcional — es|en, default es)
 *   - tipo_cliente      (opcional — cliente_final|agencia_descuento)
 *   - numero_version    (opcional)
 *
 * op=descargar (GET/POST)
 *   - id_documento_ppto (requerido)
 *
 * Devuelve JSON: { success, id_documento_ppto, numero_documento, url_pdf }
 * El PDF se guarda en public/documentos/facturas/
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
require_once __DIR__ . "/../models/Kit.php";
require_once __DIR__ . "/../models/Comerciales.php";
require_once __DIR__ . "/../vendor/tcpdf/tcpdf.php";

// ══════════════════════════════════════════════════════════════════
// CLASE PDF — Factura Final
// Diseño idéntico al presupuesto · Azul (102,126,234) · Borde verde cliente
// ══════════════════════════════════════════════════════════════════

class MYPDF_FINAL extends TCPDF
{
    private $datos_empresa;
    private $datos_presupuesto;
    private $mostrar_logo;
    private $path_logo;
    private $fecha_factura;
    private $numero_factura;
    private $observaciones_cabecera;
    private $texto_pie_empresa;

    // Azul elegante (igual que el presupuesto)
    private $cr = 102;
    private $cg = 126;
    private $cb = 234;

    public function setDatosHeader(
        array  $empresa,
        array  $presupuesto,
        bool   $logo,
        string $path_logo,
        string $fecha_factura,
        string $numero_factura,
        string $obs_cabecera,
        string $pie_empresa
    ) {
        $this->datos_empresa          = $empresa;
        $this->datos_presupuesto      = $presupuesto;
        $this->mostrar_logo           = $logo;
        $this->path_logo              = $path_logo;
        $this->fecha_factura          = $fecha_factura;
        $this->numero_factura         = $numero_factura;
        $this->observaciones_cabecera = $obs_cabecera;
        $this->texto_pie_empresa      = $pie_empresa;
    }


    // ─────────────────────────────────────────────────────────────
    // CABECERA (repetida en cada página)
    // ─────────────────────────────────────────────────────────────
    public function Header()
    {
        $y_start = 10;

        // ── TÍTULO "FACTURA" ──────────────────────────────────────
        $this->SetY($y_start);
        $this->SetFont('helvetica', 'B', 16);
        $this->SetTextColor($this->cr, $this->cg, $this->cb);
        $this->Cell(0, 8, 'FACTURA', 0, 1, 'R');
        $this->Ln(2);
        $y_start = $this->GetY();

        // ── LOGO ──────────────────────────────────────────────────
        if ($this->mostrar_logo && !empty($this->path_logo) && file_exists($this->path_logo)) {
            $this->Image($this->path_logo, 8, $y_start, 35, 0, '', '', '', false, 300, '', false, false, 0);
            $logo_height = 18;
        } else {
            $logo_height = 0;
        }

        // ── DATOS EMPRESA (columna izquierda) ─────────────────────
        $y_empresa = $y_start + $logo_height + 1;
        $this->SetY($y_empresa);
        $this->SetX(8);

        $this->SetFont('helvetica', 'B', 9);
        $this->SetTextColor(44, 62, 80);
        $this->Cell(95, 3.5, $this->datos_empresa['nombre_comercial_empresa'] ?? ($this->datos_empresa['nombre_empresa'] ?? ''), 0, 1, 'L');

        // CIF en rojo (solo si no termina en 0000)
        $nif_empresa = $this->datos_empresa['nif_empresa'] ?? '';
        if ($nif_empresa && substr($nif_empresa, -4) !== '0000') {
            $this->SetX(8);
            $this->SetFont('helvetica', 'B', 8);
            $this->SetTextColor(231, 76, 60);
            $this->Cell(95, 2.5, 'CIF: ' . $nif_empresa, 0, 1, 'L');
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

        // ── CAJA INFO FACTURA (fondo azul igual que presupuesto) ──
        $y_info = $this->GetY() + 1;

        $this->SetFillColor($this->cr, $this->cg, $this->cb);
        $this->SetTextColor(255, 255, 255);
        $this->SetFont('helvetica', 'B', 8.5);
        $this->SetXY(8, $y_info);
        $this->Cell(95, 10, '', 0, 0, 'L', true);

        // Línea 1: Nº factura + Fecha
        $this->SetXY(9, $y_info + 1);
        $info_linea1 = 'Nº: ' . $this->numero_factura . '  |  F: ' . $this->fecha_factura;
        $this->Cell(93, 3, $info_linea1, 0, 1, 'L');

        // Línea 2: Referencia presupuesto
        $num_ppto = $this->datos_presupuesto['numero_presupuesto'] ?? '';
        if ($num_ppto) {
            $this->SetXY(9, $y_info + 5);
            $this->SetFont('helvetica', '', 7.5);
            $this->Cell(93, 3, 'Ref. Presupuesto: ' . $num_ppto, 0, 1, 'L');
        }

        $this->SetTextColor(0, 0, 0);
        $y_final_izquierda = $this->GetY();

        // ── BOX CLIENTE (verde — igual que presupuesto) ───────────
        $col2_x     = 108;
        $col2_width = 94;
        $box_y      = $y_start;

        $client_box_height = 26;
        if (!empty($this->datos_presupuesto['nombre_contacto_cliente'])) {
            $client_box_height += 10;
        }

        $this->SetFillColor(248, 249, 250);
        $this->Rect($col2_x, $box_y, $col2_width, $client_box_height, 'F');

        $this->SetDrawColor(39, 174, 96); // Verde
        $this->SetLineWidth(0.5);
        $this->Rect($col2_x, $box_y, $col2_width, $client_box_height);
        $this->SetLineWidth(0.2);

        $this->SetXY($col2_x + 2, $box_y + 1.5);
        $this->SetFont('helvetica', 'B', 9);
        $this->SetTextColor(44, 62, 80);
        $this->Cell($col2_width - 4, 3.5, 'CLIENTE', 0, 1, 'L');

        $this->SetDrawColor(39, 174, 96);
        $this->Line($col2_x + 2, $box_y + 5.5, $col2_x + $col2_width - 2, $box_y + 5.5);

        $this->SetXY($col2_x + 2, $box_y + 6.5);
        $this->SetFont('helvetica', '', 8);
        $this->SetTextColor(52, 73, 94);

        $nombre_completo = trim(
            ($this->datos_presupuesto['nombre_cliente'] ?? '') . ' ' .
            ($this->datos_presupuesto['apellido_cliente'] ?? '')
        );
        if (!empty($nombre_completo)) {
            $this->SetFont('helvetica', 'B', 8.5);
            $this->Cell($col2_width - 4, 3, $nombre_completo, 0, 1, 'L');
            $this->SetX($col2_x + 2);
            $this->SetFont('helvetica', '', 7.5);
        }

        if (!empty($this->datos_presupuesto['nif_cliente'])) {
            $this->SetFont('helvetica', '', 7.5);
            $this->Cell(15, 2.5, 'NIF/CIF:', 0, 0, 'L');
            $this->SetFont('helvetica', 'B', 7.5);
            $this->Cell($col2_width - 19, 2.5, $this->datos_presupuesto['nif_cliente'], 0, 1, 'L');
            $this->SetX($col2_x + 2);
        }

        if (!empty($this->datos_presupuesto['direccion_cliente'])) {
            $this->SetFont('helvetica', '', 7.5);
            $this->Cell(15, 2.5, 'Direccion:', 0, 0, 'L');
            $dir_cli = $this->datos_presupuesto['direccion_cliente'];
            $partes = [];
            if (!empty($this->datos_presupuesto['cp_cliente']))        $partes[] = $this->datos_presupuesto['cp_cliente'];
            if (!empty($this->datos_presupuesto['poblacion_cliente']))  $partes[] = $this->datos_presupuesto['poblacion_cliente'];
            if (!empty($partes)) $dir_cli .= ', ' . implode(' ', $partes);
            $this->MultiCell($col2_width - 19, 2.5, $dir_cli, 0, 'L');
            $this->SetX($col2_x + 2);
        }

        if (!empty($this->datos_presupuesto['email_cliente'])) {
            $this->SetFont('helvetica', '', 7.5);
            $this->Cell(15, 2.5, 'Email:', 0, 0, 'L');
            $this->Cell($col2_width - 19, 2.5, $this->datos_presupuesto['email_cliente'], 0, 1, 'L');
            $this->SetX($col2_x + 2);
        }

        if (!empty($this->datos_presupuesto['telefono_cliente'])) {
            $this->SetFont('helvetica', '', 7.5);
            $this->Cell(15, 2.5, 'Telefono:', 0, 0, 'L');
            $this->Cell($col2_width - 19, 2.5, $this->datos_presupuesto['telefono_cliente'], 0, 1, 'L');
            $this->SetX($col2_x + 2);
        }

        // A la atención de (contacto)
        if (!empty($this->datos_presupuesto['nombre_contacto_cliente'])) {
            $this->Ln(1);
            $this->SetXY($col2_x + 2, $this->GetY());
            $this->SetFont('helvetica', 'B', 8);
            $this->SetTextColor(39, 174, 96);
            $this->Cell($col2_width - 4, 3, 'A la atencion de:', 0, 1, 'L');

            $this->SetX($col2_x + 2);
            $this->SetFont('helvetica', '', 7.5);
            $this->SetTextColor(52, 73, 94);

            $nombre_contacto = trim(
                ($this->datos_presupuesto['nombre_contacto_cliente'] ?? '') . ' ' .
                ($this->datos_presupuesto['apellidos_contacto_cliente'] ?? '')
            );
            if (!empty($nombre_contacto)) {
                $this->Cell(15, 2.5, 'Nombre:', 0, 0, 'L');
                $this->SetFont('helvetica', 'B', 7.5);
                $this->Cell($col2_width - 19, 2.5, $nombre_contacto, 0, 1, 'L');
                $this->SetX($col2_x + 2);
                $this->SetFont('helvetica', '', 7.5);
            }
            if (!empty($this->datos_presupuesto['telefono_contacto_cliente'])) {
                $this->Cell(15, 2.5, 'Telefono:', 0, 0, 'L');
                $this->Cell($col2_width - 19, 2.5, $this->datos_presupuesto['telefono_contacto_cliente'], 0, 1, 'L');
                $this->SetX($col2_x + 2);
            }
            if (!empty($this->datos_presupuesto['email_contacto_cliente'])) {
                $this->Cell(15, 2.5, 'Email:', 0, 0, 'L');
                $this->Cell($col2_width - 19, 2.5, $this->datos_presupuesto['email_contacto_cliente'], 0, 1, 'L');
            }
        }

        // ── DATOS DEL EVENTO (columna derecha, debajo del cliente) ─
        $y_evento    = $box_y + $client_box_height + 3;
        $evento_x    = 108;
        $evento_w    = 94;
        $y_ev_final  = $y_evento;

        $this->SetXY($evento_x, $y_evento);
        $this->SetFont('helvetica', 'B', 8);
        $this->SetTextColor(39, 174, 96);
        $this->Cell($evento_w, 4, 'DATOS DEL EVENTO', 0, 1, 'L');

        $this->SetX($evento_x);
        $this->SetFont('helvetica', '', 8);
        $this->SetTextColor(127, 140, 141);

        $mini_w = $evento_w / 3;
        $this->Cell($mini_w, 3, 'Inicio',   0, 0, 'C');
        $this->Cell($mini_w, 3, 'Fin',      0, 0, 'C');
        $this->Cell($mini_w, 3, 'Duracion', 0, 1, 'C');

        $this->SetX($evento_x);
        $this->SetFont('helvetica', 'B', 8);
        $this->SetTextColor(52, 73, 94);

        $fi_str = '';
        if (!empty($this->datos_presupuesto['fecha_inicio_evento_presupuesto'])) {
            $fi_str = date('d/m/Y', strtotime($this->datos_presupuesto['fecha_inicio_evento_presupuesto']));
        }
        $ff_str = '';
        if (!empty($this->datos_presupuesto['fecha_fin_evento_presupuesto'])) {
            $ff_str = date('d/m/Y', strtotime($this->datos_presupuesto['fecha_fin_evento_presupuesto']));
        }
        $dur_str = '';
        if (!empty($this->datos_presupuesto['duracion_evento_dias'])) {
            $dur_str = $this->datos_presupuesto['duracion_evento_dias'] . ' dias';
        }

        $this->Cell($mini_w, 4, $fi_str,  0, 0, 'C');
        $this->Cell($mini_w, 4, $ff_str,  0, 0, 'C');
        $this->Cell($mini_w, 4, $dur_str, 0, 1, 'C');
        $this->Ln(4);
        $y_ev_final = $this->GetY();

        // Nombre del evento
        if (!empty($this->datos_presupuesto['nombre_evento_presupuesto'])) {
            $this->SetX($evento_x);
            $this->SetFont('helvetica', 'B', 7.5);
            $this->SetFillColor(255, 243, 205);
            $this->SetTextColor(133, 100, 4);
            $this->MultiCell($evento_w, 4, $this->datos_presupuesto['nombre_evento_presupuesto'], 0, 'L', true);
            $this->Ln(1);
            $y_ev_final = $this->GetY();
        }

        // Ubicación del evento
        $ubic = '';
        if (!empty($this->datos_presupuesto['direccion_evento_presupuesto'])) {
            $ubic .= $this->datos_presupuesto['direccion_evento_presupuesto'];
        }
        if (!empty($this->datos_presupuesto['cp_evento_presupuesto']) ||
            !empty($this->datos_presupuesto['poblacion_evento_presupuesto'])) {
            if (!empty($ubic)) $ubic .= ', ';
            if (!empty($this->datos_presupuesto['cp_evento_presupuesto']))
                $ubic .= $this->datos_presupuesto['cp_evento_presupuesto'] . ' ';
            if (!empty($this->datos_presupuesto['poblacion_evento_presupuesto']))
                $ubic .= $this->datos_presupuesto['poblacion_evento_presupuesto'];
        }
        if (!empty($this->datos_presupuesto['provincia_evento_presupuesto'])) {
            if (!empty($ubic)) $ubic .= ', ';
            $ubic .= $this->datos_presupuesto['provincia_evento_presupuesto'];
        }
        if (!empty($ubic)) {
            $this->SetX($evento_x);
            $this->SetFont('helvetica', 'B', 8);
            $this->SetTextColor(243, 156, 18);
            $this->Cell($evento_w, 4, 'UBICACION DEL EVENTO', 0, 1, 'L');
            $this->SetX($evento_x);
            $this->SetFont('helvetica', '', 7);
            $this->SetTextColor(52, 73, 94);
            $this->Cell($evento_w, 3.5, $ubic, 0, 1, 'L');
            $y_ev_final = $this->GetY();
        }

        $this->SetDrawColor(0, 0, 0);
        $this->SetTextColor(0, 0, 0);

        // ── OBSERVACIONES DE CABECERA (ancho total) ───────────────
        $y_obs = max($y_final_izquierda, $y_ev_final) + 6;

        if (!empty($this->observaciones_cabecera)) {
            $this->SetXY(8, $y_obs);
            $this->SetFont('helvetica', '', 8);
            $this->SetTextColor(44, 62, 80);
            $this->MultiCell(194, 4, $this->observaciones_cabecera, 0, 'L');
        }

        $this->SetY($this->GetY());
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


header('Content-Type: application/json; charset=utf-8');

$registro  = new RegistroActividad();
$docModel  = new DocumentoPresupuesto();
$impresion = new ImpresionPresupuesto();
$empModel  = new Empresas();
$kitModel  = new Kit();
$comModel  = new Comerciales();

$op = $_GET['op'] ?? '';

switch ($op) {

    // ══════════════════════════════════════════════════════════════
    // GENERAR
    // ══════════════════════════════════════════════════════════════
    case "generar":
        $id_presupuesto  = (int)($_POST['id_presupuesto']  ?? 0);
        $id_empresa      = (int)($_POST['id_empresa']      ?? 0);
        $id_pago_ppto    = (int)($_POST['id_pago_ppto']    ?? 0);
        $numero_version  = !empty($_POST['numero_version']) ? (int)$_POST['numero_version'] : null;
        $tipo_cliente    = in_array($_POST['tipo_cliente'] ?? '', ['cliente_final', 'agencia_descuento'])
                            ? $_POST['tipo_cliente'] : 'cliente_final';
        $idioma          = ($_POST['idioma'] ?? 'es') === 'en' ? 'en' : 'es';
        $observaciones_internas = !empty($_POST['observaciones']) ? htmlspecialchars(trim($_POST['observaciones']), ENT_QUOTES, 'UTF-8') : null;

        if (!$id_presupuesto || !$id_empresa) {
            echo json_encode(['success' => false, 'message' => 'Faltan campos obligatorios (id_presupuesto, id_empresa)'], JSON_UNESCAPED_UNICODE);
            break;
        }

        try {
            // 1. Validar empresa real
            if (!$docModel->verificar_empresa_real($id_empresa)) {
                echo json_encode(['success' => false, 'message' => 'La empresa seleccionada no es una empresa real válida'], JSON_UNESCAPED_UNICODE);
                break;
            }

            // 2. Obtener datos presupuesto
            $datos_ppto = $impresion->get_datos_cabecera($id_presupuesto, $numero_version);
            if (!$datos_ppto) {
                echo json_encode(['success' => false, 'message' => "No se encontraron datos del presupuesto ID: $id_presupuesto"], JSON_UNESCAPED_UNICODE);
                break;
            }

            // 3. Obtener datos empresa emisora (real)
            $datos_empresa = $empModel->get_empresaxid($id_empresa);
            if (!$datos_empresa) {
                echo json_encode(['success' => false, 'message' => "No se encontraron datos de la empresa ID: $id_empresa"], JSON_UNESCAPED_UNICODE);
                break;
            }

            // 4. Obtener líneas del presupuesto
            $lineas = $impresion->get_lineas_impresion($id_presupuesto, $numero_version);

            // Configuración de empresa
            $mostrar_subtotales_fecha = !isset($datos_empresa['mostrar_subtotales_fecha_presupuesto_empresa'])
                ? true
                : ($datos_empresa['mostrar_subtotales_fecha_presupuesto_empresa'] == 1);
            $permitir_descuentos = !isset($datos_empresa['permitir_descuentos_lineas_empresa'])
                ? true
                : ($datos_empresa['permitir_descuentos_lineas_empresa'] == 1);

            // 4.1 Agrupar líneas por fecha de inicio y ubicación
            $lineas_agrupadas = [];
            foreach ($lineas as $linea) {
                $fecha_inicio = $linea['fecha_inicio_linea_ppto'];
                $id_ubicacion = $linea['id_ubicacion'] ?? 0;
                if (!isset($lineas_agrupadas[$fecha_inicio])) {
                    $lineas_agrupadas[$fecha_inicio] = [
                        'ubicaciones'     => [],
                        'subtotal_fecha'  => 0,
                        'total_iva_fecha' => 0,
                        'total_fecha'     => 0,
                    ];
                }
                if (!isset($lineas_agrupadas[$fecha_inicio]['ubicaciones'][$id_ubicacion])) {
                    $lineas_agrupadas[$fecha_inicio]['ubicaciones'][$id_ubicacion] = [
                        'nombre_ubicacion'    => $linea['nombre_ubicacion'] ?? 'Sin ubicación',
                        'lineas'              => [],
                        'subtotal_ubicacion'  => 0,
                        'total_iva_ubicacion' => 0,
                        'total_ubicacion'     => 0,
                    ];
                }
                $lineas_agrupadas[$fecha_inicio]['ubicaciones'][$id_ubicacion]['lineas'][] = $linea;
                if ($permitir_descuentos) {
                    $base_ag = floatval($linea['base_imponible'] ?? 0);
                } else {
                    $coef_ag   = floatval($linea['valor_coeficiente_linea_ppto'] ?? 0);
                    $cant_ag   = floatval($linea['cantidad_linea_ppto'] ?? 0);
                    $precio_ag = floatval($linea['precio_unitario_linea_ppto'] ?? 0);
                    $dias_ag   = floatval($linea['dias_linea'] ?? 1);
                    $base_ag   = ($coef_ag > 0)
                        ? $cant_ag * $precio_ag * $coef_ag
                        : $dias_ag * $cant_ag * $precio_ag;
                }
                $iva_ag = $base_ag * (floatval($linea['porcentaje_iva_linea_ppto'] ?? 0) / 100);
                $lineas_agrupadas[$fecha_inicio]['ubicaciones'][$id_ubicacion]['subtotal_ubicacion']  += $base_ag;
                $lineas_agrupadas[$fecha_inicio]['ubicaciones'][$id_ubicacion]['total_iva_ubicacion'] += $iva_ag;
                $lineas_agrupadas[$fecha_inicio]['ubicaciones'][$id_ubicacion]['total_ubicacion']     += $base_ag + $iva_ag;
                $lineas_agrupadas[$fecha_inicio]['subtotal_fecha']  += $base_ag;
                $lineas_agrupadas[$fecha_inicio]['total_iva_fecha'] += $iva_ag;
                $lineas_agrupadas[$fecha_inicio]['total_fecha']     += $base_ag + $iva_ag;
            }

            // 5. Calcular totales con desglose IVA y descuentos
            $desglose_iva           = [];
            $subtotal_sin_iva       = 0;
            $subtotal_sin_descuento = 0;
            $total_descuentos       = 0;

            foreach ($lineas as $linea) {
                $cantidad        = floatval($linea['cantidad_linea_ppto'] ?? 0);
                $precio_unitario = floatval($linea['precio_unitario_linea_ppto'] ?? 0);
                $dias            = floatval($linea['dias_linea'] ?? 1);
                $coeficiente     = floatval($linea['valor_coeficiente_linea_ppto'] ?? 0);
                $aplica_coef     = ($coeficiente > 0);
                $descuento_pct   = floatval($linea['descuento_linea_ppto'] ?? 0);
                $porcentaje_iva  = floatval($linea['porcentaje_iva_linea_ppto'] ?? 0);

                if ($aplica_coef) {
                    $subtotal_linea_sin_desc = $cantidad * $precio_unitario * $coeficiente;
                } else {
                    $subtotal_linea_sin_desc = $dias * $cantidad * $precio_unitario;
                }
                $importe_dto = $subtotal_linea_sin_desc * ($descuento_pct / 100);

                if ($permitir_descuentos) {
                    $base_imponible = floatval($linea['base_imponible'] ?? 0);
                    if ($base_imponible >= 0) {
                        $subtotal_sin_descuento += $subtotal_linea_sin_desc;
                        $total_descuentos       += $importe_dto;
                    } else {
                        $total_descuentos += abs($base_imponible);
                    }
                } else {
                    $base_imponible = $aplica_coef
                        ? $cantidad * $precio_unitario * $coeficiente
                        : $dias * $cantidad * $precio_unitario;
                    if ($base_imponible >= 0) {
                        $subtotal_sin_descuento += $base_imponible;
                    } else {
                        $total_descuentos += abs($base_imponible);
                    }
                }

                $subtotal_sin_iva += $base_imponible;

                if (!isset($desglose_iva[$porcentaje_iva])) {
                    $desglose_iva[$porcentaje_iva] = ['base' => 0, 'cuota' => 0];
                }
                $cuota_iva = $base_imponible * ($porcentaje_iva / 100);
                $desglose_iva[$porcentaje_iva]['base']  += $base_imponible;
                $desglose_iva[$porcentaje_iva]['cuota'] += $cuota_iva;
            }
            ksort($desglose_iva);

            $total_iva = 0;
            foreach ($desglose_iva as $iva) {
                $total_iva += $iva['cuota'];
            }
            $total_con_iva = round($subtotal_sin_iva + $total_iva, 2);

            // 6. Obtener anticipos previos activos, separando proforma vs factura real
            //    - anticipos con factura_anticipo (real) → ya tienen validez legal propia;
            //      se deducen del total de ESTA factura.
            //    - anticipos con factura_proforma (o sin doc) → no tienen validez legal;
            //      se muestran como "anticipos previos" en el PDF pero el total de la
            //      factura final los absorbe.
            $pagoModel = new PagoPresupuesto();
            $pagos_previos = $pagoModel->get_pagos_presupuesto($id_presupuesto);
            $anticipos_proforma  = 0.0;  // anticipos sin validez legal → se muestran en PDF
            $anticipos_reales    = 0.0;  // anticipos con factura_anticipo legal → se descuentan del total
            foreach ($pagos_previos as $p) {
                if ($p['tipo_pago_ppto'] === 'anticipo' && $p['estado_pago_ppto'] !== 'anulado') {
                    $tipo_doc_vinculado = $p['tipo_documento_vinculado'] ?? null;
                    if ($tipo_doc_vinculado === 'factura_anticipo') {
                        $anticipos_reales += (float)($p['importe_pago_ppto'] ?? 0);
                    } else {
                        // factura_proforma, sin documento, etc. → no tienen validez legal
                        $anticipos_proforma += (float)($p['importe_pago_ppto'] ?? 0);
                    }
                }
            }
            $anticipos_proforma = round($anticipos_proforma, 2);
            $anticipos_reales   = round($anticipos_reales, 2);

            // Los "anticipos previos" en el bloque informativo del PDF son solo los de proforma
            $total_anticipos = $anticipos_proforma;

            // El total fiscal de ESTA factura = total presupuesto − anticipos ya facturados legalmente
            $total_factura_final = round($total_con_iva - $anticipos_reales, 2);

            // 7. Crear registro documento_presupuesto (SP asigna número de la serie F)
            $datos_insert = [
                'id_presupuesto'               => $id_presupuesto,
                'tipo_documento_ppto'          => 'factura_final',
                'id_empresa'                   => $id_empresa,
                'id_pago_ppto'                 => $id_pago_ppto ?: null,
                'numero_version'               => $numero_version,
                'observaciones_documento_ppto' => $observaciones_internas,
                // El importe almacenado es el total fiscal de ESTA factura (excluye anticipos reales)
                'importe_documento_ppto'       => $total_factura_final,
            ];

            $id_doc = $docModel->insert_documento($datos_insert);
            if (!$id_doc) {
                echo json_encode(['success' => false, 'message' => 'Error al crear el registro de documento factura final'], JSON_UNESCAPED_UNICODE);
                break;
            }

            // Vincular documento al pago
            if ($id_pago_ppto) {
                $pagoModel->update_pago($id_pago_ppto, ['id_documento_ppto' => $id_doc]);
            }

            // 8. Obtener registro creado (número asignado por SP)
            $doc = $docModel->get_documentoxid($id_doc);
            $numero_documento = $doc['numero_documento_ppto'] ?? "F-{$id_doc}";

            // 9. Logo empresa
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

            // 10. Actualizar importes en BD
            // subtotal_sin_iva y total_iva son los de las líneas completas del presupuesto;
            // total_factura_final descuenta los anticipos con factura real ya emitida.
            $docModel->actualizar_importes($id_doc, $subtotal_sin_iva, $total_iva, $total_factura_final);

            // 11. Generar PDF
            $pdf = _generar_pdf_factura_final(
                $datos_ppto, $datos_empresa, $numero_documento,
                $lineas_agrupadas,
                $desglose_iva,
                $subtotal_sin_iva, $subtotal_sin_descuento, $total_descuentos,
                $total_iva, $total_con_iva,
                $total_anticipos,
                $anticipos_reales,
                $mostrar_logo, $path_logo,
                $mostrar_subtotales_fecha, $permitir_descuentos,
                $idioma, $tipo_cliente
            );

            // 12. Guardar a disco
            $dir_guardado = __DIR__ . '/../public/documentos/facturas/';
            if (!is_dir($dir_guardado)) {
                mkdir($dir_guardado, 0755, true);
            }
            $nombre_archivo = preg_replace('/[^A-Za-z0-9_\-]/', '_', $numero_documento) . '.pdf';
            $ruta_absoluta  = $dir_guardado . $nombre_archivo;
            $ruta_relativa  = 'public/documentos/facturas/' . $nombre_archivo;

            $pdf_string = $pdf->Output($nombre_archivo, 'S');
            file_put_contents($ruta_absoluta, $pdf_string);
            $tamano = filesize($ruta_absoluta);

            $docModel->actualizar_ruta_pdf($id_doc, $ruta_relativa, $tamano);

            $registro->registrarActividad(
                'admin', 'impresion_factura_final.php', 'generar',
                "Factura final $numero_documento generada. Presupuesto ID: $id_presupuesto. Doc ID: $id_doc", 'info'
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
                'admin', 'impresion_factura_final.php', 'generar',
                "Error: " . $e->getMessage(), 'error'
            );
            ob_end_clean();
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false, 'message' => 'Error al generar la factura final: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
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
// FUNCIÓN: Generar PDF Factura Final (diseño igual que presupuesto)
// ══════════════════════════════════════════════════════════════════
function _generar_pdf_factura_final(
    array  $datos_ppto,
    array  $datos_empresa,
    string $numero_documento,
    array  $lineas_agrupadas,
    array  $desglose_iva,
    float  $subtotal_sin_iva,
    float  $subtotal_sin_descuento,
    float  $total_descuentos,
    float  $total_iva,
    float  $total_con_iva,
    float  $total_anticipos,
    float  $anticipos_reales,
    bool   $mostrar_logo,
    string $path_logo,
    bool   $mostrar_subtotales_fecha,
    bool   $permitir_descuentos,
    string $idioma       = 'es',
    string $tipo_cliente = 'cliente_final'
): MYPDF_FINAL {

    global $kitModel, $comModel;

    $fecha_hoy = date('d/m/Y');

    // ─── Inicializar PDF ───────────────────────────────────────────
    $pdf = new MYPDF_FINAL('P', 'mm', 'A4', true, 'UTF-8', false);
    $pdf->SetCreator('MDR ERP');
    $pdf->SetAuthor($datos_empresa['nombre_comercial_empresa'] ?? ($datos_empresa['nombre_empresa'] ?? 'MDR'));
    $pdf->SetTitle('Factura ' . $numero_documento);

    $pdf->setPrintHeader(true);
    $pdf->setPrintFooter(true);
    $pdf->SetHeaderMargin(5);
    $pdf->SetFooterMargin(15);
    $pdf->SetAutoPageBreak(true, 25);

    // Pasar datos a la cabecera mediante setDatosHeader (igual que presupuesto)
    $pdf->setDatosHeader(
        $datos_empresa,
        $datos_ppto,
        $mostrar_logo,
        $path_logo,
        $fecha_hoy,
        $numero_documento,
        $datos_ppto['observaciones_cabecera_presupuesto'] ?? '',
        $datos_empresa['texto_pie_factura_empresa'] ?? ''
    );
    $pdf->SetMargins(8, 95, 8);

    $pdf->AddPage();

    // =====================================================================
    // ANÁLISIS DE FECHAS DE MONTAJE Y DESMONTAJE (umbral 30%)
    // =====================================================================
    $analisis_fechas = [];

    foreach ($lineas_agrupadas as $fecha_inicio => $grupo_fecha) {
        $todas_lineas = [];
        foreach ($grupo_fecha['ubicaciones'] as $grupo_ub) {
            $todas_lineas = array_merge($todas_lineas, $grupo_ub['lineas']);
        }
        $total_lineas = count($todas_lineas);

        if ($total_lineas == 0) {
            $analisis_fechas[$fecha_inicio] = [
                'ocultar_columnas' => false,
                'fecha_montaje'    => null,
                'fecha_desmontaje' => null,
                'excepciones'      => [],
            ];
            continue;
        }

        $combinaciones = [];
        foreach ($todas_lineas as $linea) {
            $fm_raw  = $linea['fecha_montaje_linea_ppto']    ?? null;
            $fd_raw  = $linea['fecha_desmontaje_linea_ppto'] ?? null;
            $mtje    = (!empty($fm_raw) && $fm_raw != '0000-00-00') ? date('Y-m-d', strtotime($fm_raw)) : '';
            $dsmtje  = (!empty($fd_raw) && $fd_raw != '0000-00-00') ? date('Y-m-d', strtotime($fd_raw)) : '';
            $clave   = $mtje . '|' . $dsmtje;
            if (!isset($combinaciones[$clave])) {
                $combinaciones[$clave] = ['count' => 0, 'montaje' => $mtje, 'desmontaje' => $dsmtje, 'indices' => []];
            }
            $combinaciones[$clave]['count']++;
            $combinaciones[$clave]['indices'][] = $linea['id_linea_ppto'];
        }

        $max_count = 0;
        $comb_pred = null;
        foreach ($combinaciones as $datos_comb) {
            if ($datos_comb['count'] > $max_count) {
                $max_count = $datos_comb['count'];
                $comb_pred = $datos_comb;
            }
        }

        $porcentaje = ($max_count / $total_lineas) * 100;

        if ($porcentaje >= 30) {
            $excepciones = [];
            foreach ($todas_lineas as $linea) {
                if (!in_array($linea['id_linea_ppto'], $comb_pred['indices'])) {
                    $excepciones[] = $linea['id_linea_ppto'];
                }
            }
            $analisis_fechas[$fecha_inicio] = [
                'ocultar_columnas' => true,
                'fecha_montaje'    => $comb_pred['montaje'],
                'fecha_desmontaje' => $comb_pred['desmontaje'],
                'excepciones'      => $excepciones,
            ];
        } else {
            $analisis_fechas[$fecha_inicio] = [
                'ocultar_columnas' => false,
                'fecha_montaje'    => null,
                'fecha_desmontaje' => null,
                'excepciones'      => [],
            ];
        }
    }

    // =====================================================================
    // TABLA DE LÍNEAS AGRUPADAS POR FECHA Y UBICACIÓN
    // =====================================================================
    $pdf->SetFont('helvetica', 'B', 8);

    foreach ($lineas_agrupadas as $fecha_inicio => $grupo_fecha) {

        // Cabecera de fecha
        $fecha_formateada = date('d/m/Y', strtotime($fecha_inicio));
        $info_fechas      = $analisis_fechas[$fecha_inicio] ?? ['ocultar_columnas' => false];
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
            $ocultar_cols = $info_fechas['ocultar_columnas'];
            $ancho_descripcion = $ocultar_cols ? 79 : 49;
            if (!$permitir_descuentos) {
                $ancho_descripcion += 12;
            }

            $pdf->SetFont('helvetica', 'B', 8);
            $pdf->SetFillColor(240, 240, 240);
            $pdf->SetDrawColor(200, 200, 200);

            $pdf->Cell(17, 6, 'Inicio',      1, 0, 'C', 1);
            $pdf->Cell(17, 6, 'Fin',         1, 0, 'C', 1);
            if (!$ocultar_cols) {
                $pdf->Cell(15, 6, 'Mtje',   1, 0, 'C', 1);
                $pdf->Cell(15, 6, 'Dsmtje', 1, 0, 'C', 1);
            }
            $pdf->Cell(8,  6, 'Días',         1, 0, 'C', 1);
            $pdf->Cell(10, 6, 'Coef.',        1, 0, 'C', 1);
            $pdf->Cell($ancho_descripcion, 6, 'Descripción', 1, 0, 'C', 1);
            $pdf->Cell(12, 6, 'Cant.',        1, 0, 'C', 1);
            $pdf->Cell(15, 6, 'P.Unit.',      1, 0, 'C', 1);
            if ($permitir_descuentos) {
                $pdf->Cell(12, 6, '%Dto',     1, 0, 'C', 1);
            }
            $pdf->Cell(24, 6, 'Importe(€)',   1, 1, 'C', 1);
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
                $pdf->Cell(24, $altura_fila, number_format($base_imponible, 2, ',', '.'), 1, 0, 'R');

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
                    $obs_fechas = 'Mtje: ' . $fecha_montaje . ' - Dsmtje: ' . $fecha_desmontaje;
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
                        $compsActivos = array_filter($componentes, function($c) {
                            return isset($c['activo_articulo_componente']) && $c['activo_articulo_componente'] != 0;
                        });
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
                            $pdf->Cell(51, 4, '', 0, 1, 'R');
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

        // Subtotal por fecha
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

    // =====================================================================
    // TOTALES
    // =====================================================================
    $pdf->Ln(5);
    $pdf->SetDrawColor(200, 200, 200);
    $pdf->Line(145, $pdf->GetY(), 200, $pdf->GetY());
    $pdf->Ln(3);

    // Subtotal sin descuento (solo si hay descuentos)
    if ($total_descuentos > 0) {
        $pdf->SetFont('helvetica', '', 9);
        $pdf->SetFillColor(248, 249, 250);
        $pdf->SetDrawColor(220, 220, 220);
        $pdf->Cell(144, 6, '', 0, 0);
        $pdf->Cell(30, 6, 'Subtotal:', 1, 0, 'R', 1);
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->Cell(20, 6, number_format($subtotal_sin_descuento, 2, ',', '.') . ' €', 1, 1, 'R', 1);
    }

    // Descuento total (en rojo)
    if ($total_descuentos > 0) {
        $pdf->SetFont('helvetica', '', 9);
        $pdf->SetFillColor(248, 249, 250);
        $pdf->SetDrawColor(220, 220, 220);
        $pdf->Cell(144, 6, '', 0, 0);
        $pdf->Cell(30, 6, 'Descuento:', 1, 0, 'R', 1);
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->SetTextColor(231, 76, 60);
        $pdf->Cell(20, 6, '-' . number_format($total_descuentos, 2, ',', '.') . ' €', 1, 1, 'R', 1);
        $pdf->SetTextColor(0, 0, 0);
    }

    // Base imponible
    $pdf->SetFont('helvetica', '', 9);
    $pdf->SetFillColor(248, 249, 250);
    $pdf->SetDrawColor(220, 220, 220);
    $pdf->Cell(144, 6, '', 0, 0);
    $pdf->Cell(30, 6, 'Base Imponible:', 1, 0, 'R', 1);
    $pdf->SetFont('helvetica', 'B', 9);
    $pdf->Cell(20, 6, number_format($subtotal_sin_iva, 2, ',', '.') . ' €', 1, 1, 'R', 1);

    // Desglose IVA (si hay más de un tipo)
    if (count($desglose_iva) > 1) {
        $pdf->SetFont('helvetica', '', 8);
        $pdf->Cell(144, 5, '', 0, 0);
        $pdf->Cell(30, 5, 'Desglose de IVA:', 0, 1, 'R');
        foreach ($desglose_iva as $pct => $vals) {
            $pdf->Cell(144, 4, '', 0, 0);
            $pdf->Cell(30, 4, "Base IVA {$pct}%:", 0, 0, 'R');
            $pdf->Cell(20, 4, number_format($vals['base'], 2, ',', '.') . ' €', 0, 1, 'R');
            $pdf->Cell(144, 4, '', 0, 0);
            $pdf->Cell(30, 4, "IVA {$pct}%:", 0, 0, 'R');
            $pdf->Cell(20, 4, number_format($vals['cuota'], 2, ',', '.') . ' €', 0, 1, 'R');
        }
    }

    // Total IVA
    $pdf->SetFont('helvetica', '', 9);
    $pdf->SetFillColor(248, 249, 250);
    $pdf->SetDrawColor(220, 220, 220);
    $pdf->Cell(144, 6, '', 0, 0);
    if (count($desglose_iva) == 1) {
        $pct_unico = key($desglose_iva);
        $pdf->Cell(30, 6, "Total IVA ({$pct_unico}%):", 1, 0, 'R', 1);
    } else {
        $pdf->Cell(30, 6, 'Total IVA:', 1, 0, 'R', 1);
    }
    $pdf->SetFont('helvetica', 'B', 9);
    $pdf->Cell(20, 6, number_format($total_iva, 2, ',', '.') . ' €', 1, 1, 'R', 1);

    $pdf->Ln(2);

    // TOTAL (fondo azul) — muestra el total bruto del presupuesto en todas las líneas
    $pdf->SetFont('helvetica', 'B', 11);
    $pdf->SetFillColor(102, 126, 234);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->SetDrawColor(80, 100, 200);
    $pdf->SetLineWidth(0.5);
    $pdf->Cell(144, 8, '', 0, 0);
    $pdf->Cell(20, 8, 'TOTAL:', 1, 0, 'R', 1);
    $pdf->Cell(30, 8, number_format($total_con_iva, 2, ',', '.') . ' €', 1, 1, 'R', 1);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetDrawColor(0, 0, 0);
    $pdf->SetLineWidth(0.2);

    // =====================================================================
    // DEDUCCIÓN POR FACTURAS DE ANTICIPO YA EMITIDAS (factura_anticipo legal)
    // =====================================================================
    if ($anticipos_reales > 0) {
        $total_esta_factura = round($total_con_iva - $anticipos_reales, 2);

        $pdf->SetFont('helvetica', '', 8);
        $pdf->SetFillColor(240, 247, 255);
        $pdf->SetDrawColor(190, 215, 240);
        $pdf->SetTextColor(44, 62, 80);
        $pdf->Ln(1);
        $pdf->Cell(144, 5, '', 0, 0);
        $pdf->Cell(30, 5, '(-) Facturas anticipo emitidas:', 'LTB', 0, 'R', true);
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->SetTextColor(192, 57, 43);
        $pdf->Cell(20, 5, '-' . number_format($anticipos_reales, 2, ',', '.') . ' €', 'RTB', 1, 'R', true);

        // TOTAL ESTA FACTURA (azul, pero más pequeño que el TOTAL bruto)
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->SetFillColor(102, 126, 234);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetDrawColor(80, 100, 200);
        $pdf->SetLineWidth(0.4);
        $pdf->Cell(144, 7, '', 0, 0);
        $pdf->Cell(20, 7, 'TOTAL FACTURA:', 1, 0, 'R', 1);
        $pdf->Cell(30, 7, number_format($total_esta_factura, 2, ',', '.') . ' €', 1, 1, 'R', 1);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetDrawColor(0, 0, 0);
        $pdf->SetLineWidth(0.2);
    }

    $pdf->SetLineWidth(0.2);
    $pdf->Ln(4);

    // =====================================================================
    // EXENCIÓN IVA
    // =====================================================================
    if (isset($datos_ppto['exento_iva_cliente']) && $datos_ppto['exento_iva_cliente'] == 1 &&
        !empty($datos_ppto['justificacion_exencion_iva_cliente'])) {

        $pdf->Ln(6);
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->SetFillColor(255, 243, 205);
        $pdf->SetDrawColor(255, 193, 7);
        $pdf->SetTextColor(133, 100, 4);
        $pdf->Cell(0, 6, 'INFORMACIÓN FISCAL - CLIENTE EXENTO DE IVA', 1, 1, 'C', 1);
        $pdf->SetFont('helvetica', 'I', 8);
        $pdf->SetFillColor(255, 252, 240);
        $pdf->SetTextColor(80, 80, 80);
        $pdf->MultiCell(0, 5, $datos_ppto['justificacion_exencion_iva_cliente'], 1, 'L', 1);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetDrawColor(0, 0, 0);
    }

    // =====================================================================
    // FORMA DE PAGO Y DATOS BANCARIOS
    // =====================================================================
    if (!empty($datos_ppto['nombre_pago'])) {
        $pdf->Ln(6);
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->SetTextColor(52, 73, 94);
        $pdf->Cell(40, 5, 'FORMA DE PAGO:', 0, 0, 'L');

        // En la factura final solo mostramos el método de pago; el bloque de anticipo
        // ya no aplica (el anticipo fue cobrado previamente).
        $texto_fp = trim($datos_ppto['nombre_metodo_pago'] ?? $datos_ppto['nombre_pago'] ?? '') . '.';
        $pdf->SetFont('helvetica', '', 8);
        $pdf->SetTextColor(70, 70, 70);
        $pdf->MultiCell(160, 4, $texto_fp, 0, 'L');

        // Datos bancarios para transferencia
        $fp_lower       = strtolower($datos_ppto['nombre_metodo_pago'] ?? '');
        $es_transf      = (strpos($fp_lower, 'transferencia') !== false);
        $tiene_banco    = (!empty($datos_empresa['iban_empresa']) || !empty($datos_empresa['swift_empresa']) || !empty($datos_empresa['banco_empresa']));
        $mostrar_cuenta = ($datos_empresa['mostrar_cuenta_bancaria_pdf_presupuesto_empresa'] ?? 1);

        if ($es_transf && $tiene_banco && $mostrar_cuenta) {
            $altura_bloque = 5;
            if (!empty($datos_empresa['banco_empresa']))  $altura_bloque += 3.5;
            if (!empty($datos_empresa['iban_empresa']))   $altura_bloque += 3.5;
            if (!empty($datos_empresa['swift_empresa']))  $altura_bloque += 3.5;

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

            if (!empty($datos_empresa['banco_empresa'])) {
                $pdf->SetXY($x0 + 2, $ya);
                $pdf->SetFont('helvetica', '', 6);
                $pdf->SetTextColor(70, 70, 70);
                $pdf->Cell(20, 3, 'Banco:', 0, 0, 'L');
                $pdf->SetFont('helvetica', 'B', 7);
                $pdf->Cell(160, 3, $datos_empresa['banco_empresa'], 0, 1, 'L');
                $ya += 3.5;
            }
            if (!empty($datos_empresa['iban_empresa'])) {
                $pdf->SetXY($x0 + 2, $ya);
                $pdf->SetFont('helvetica', '', 6);
                $pdf->SetTextColor(70, 70, 70);
                $pdf->Cell(20, 3, 'IBAN:', 0, 0, 'L');
                $pdf->SetFont('helvetica', 'B', 7);
                $iban_fmt = wordwrap(str_replace(' ', '', $datos_empresa['iban_empresa']), 4, ' ', true);
                $pdf->Cell(160, 3, $iban_fmt, 0, 1, 'L');
                $ya += 3.5;
            }
            if (!empty($datos_empresa['swift_empresa'])) {
                $pdf->SetXY($x0 + 2, $ya);
                $pdf->SetFont('helvetica', '', 6);
                $pdf->SetTextColor(70, 70, 70);
                $pdf->Cell(20, 3, 'SWIFT:', 0, 0, 'L');
                $pdf->SetFont('helvetica', 'B', 7);
                $pdf->Cell(160, 3, $datos_empresa['swift_empresa'], 0, 1, 'L');
            }
            $pdf->SetY($y0 + $altura_bloque + 1.5);
        }
    }

    // =====================================================================
    // ANTICIPOS PREVIOS — SALDO FINAL (debajo de forma de pago)
    // Solo anticipos con factura_proforma o sin documento (informativos)
    // =====================================================================
    if ($total_anticipos > 0) {
        $saldo_final = round($total_con_iva - $anticipos_reales - $total_anticipos, 2);

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
        $pdf->Write(5, number_format(max(0, $saldo_final), 2, ',', '.') . ' €');
        $pdf->SetFont('helvetica', '', 8);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Ln();
    }

    return $pdf;
}
