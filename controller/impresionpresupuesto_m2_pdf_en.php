<?php
// Iniciar sesión para acceder a datos del usuario (si no está ya iniciada)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Iniciar buffer de salida para capturar cualquier output no deseado
while (ob_get_level()) {
    ob_end_clean();
}
ob_start();

// Configurar errores para logging pero NO mostrarlos
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

require_once __DIR__ . "/../config/conexion.php";
require_once __DIR__ . "/../config/funciones.php";
require_once __DIR__ . "/../models/ImpresionPresupuesto.php";
require_once __DIR__ . "/../models/Kit.php";
require_once __DIR__ . "/../models/Comerciales.php";
require_once __DIR__ . "/../vendor/tcpdf/tcpdf.php";

// =====================================================
// CLASE PERSONALIZADA TCPDF CON CABECERA/PIE
// =====================================================
class MYPDF extends TCPDF {
    private $datos_empresa;
    private $mostrar_logo;
    private $path_logo;
    private $datos_presupuesto;
    private $fecha_presupuesto;
    private $fecha_validez;
    private $fecha_evento;
    private $observaciones;
    private $observaciones_cabecera;
    private $texto_pie_empresa;
    
    public function setDatosHeader($empresa, $presupuesto, $logo, $path_logo, $fecha_ppto, $fecha_val, $fecha_ev, $obs, $obs_cabecera, $pie_empresa) {
        $this->datos_empresa = $empresa;
        $this->datos_presupuesto = $presupuesto;
        $this->mostrar_logo = $logo;
        $this->path_logo = $path_logo;
        $this->fecha_presupuesto = $fecha_ppto;
        $this->fecha_validez = $fecha_val;
        $this->fecha_evento = $fecha_ev;
        $this->observaciones = $obs;
        $this->observaciones_cabecera = $obs_cabecera;
        $this->texto_pie_empresa = $pie_empresa;
    }
    
    // Header repeated on each page
    public function Header() {
        $y_start = 10;

        // ============================================
        // TITLE "QUOTATION" (RIGHT-ALIGNED)
        // ============================================

        $this->SetY($y_start);
        $this->SetFont('helvetica', 'B', 16);
        $this->SetTextColor(102, 126, 234); // Elegant blue (same as info box)
        $this->Cell(0, 8, 'QUOTATION', 0, 1, 'R'); // Right-aligned

        $this->Ln(2); // Small margin after title

        // Adjust y_start for the rest of the content
        $y_start = $this->GetY();

        // ============================================
        // LEFT COLUMN: Logo + Company Data
        // ============================================

        // Logo top-left
        if ($this->mostrar_logo && !empty($this->path_logo) && file_exists($this->path_logo)) {
            // Compact logo
            $this->Image($this->path_logo, 8, $y_start, 35, 0, '', '', '', false, 300, '', false, false, 0);
            $logo_height = 18; // Reduced logo height
        } else {
            $logo_height = 0;
        }

        // Company data (below logo, close together)
        $y_empresa = $y_start + $logo_height + 1;
        $this->SetY($y_empresa);
        $this->SetX(8);
        
        // Company trade name (bold, large size)
        $this->SetFont('helvetica', 'B', 9);
        $this->SetTextColor(44, 62, 80); // Dark colour
        $this->Cell(95, 3.5, $this->datos_empresa['nombre_comercial_empresa'] ?? '', 0, 1, 'L');

        // VAT No in red (only if does NOT end in 0000)
        $nif_empresa = $this->datos_empresa['nif_empresa'] ?? '';
        if (substr($nif_empresa, -4) !== '0000') {
            $this->SetX(8);
            $this->SetFont('helvetica', 'B', 8);
            $this->SetTextColor(231, 76, 60); // Red
            $this->Cell(95, 2.5, 'VAT No: ' . $nif_empresa, 0, 1, 'L');
        }

        // Registered address – LINE 1
        $this->SetX(8);
        $this->SetFont('helvetica', '', 7.5);
        $this->SetTextColor(52, 73, 94); // Normal colour
        $direccion_fiscal = $this->datos_empresa['direccion_fiscal_empresa'] ?? '';
        $this->Cell(95, 3, $direccion_fiscal, 0, 1, 'L');
        
        // Registered address – LINE 2 (Postcode, Town, Province)
        $this->SetX(8);
        $cp_poblacion_provincia = ($this->datos_empresa['cp_fiscal_empresa'] ?? '') . ' ' .
                                  ($this->datos_empresa['poblacion_fiscal_empresa'] ?? '') . ' (' .
                                  ($this->datos_empresa['provincia_fiscal_empresa'] ?? '') . ')';
        $this->Cell(95, 3, $cp_poblacion_provincia, 0, 1, 'L');

        // Telephone, mobile and email
        $this->SetX(8);
        $contacto = 'Tel: ' . ($this->datos_empresa['telefono_empresa'] ?? '');
        if (!empty($this->datos_empresa['movil_empresa'])) {
            $contacto .= ' | ' . $this->datos_empresa['movil_empresa'];
        }
        $this->Cell(95, 2.5, $contacto, 0, 1, 'L');

        $this->SetX(8);
        $this->Cell(95, 2.5, ($this->datos_empresa['email_empresa'] ?? ''), 0, 1, 'L');

        // Website (if available)
        if (!empty($this->datos_empresa['web_empresa'])) {
            $this->SetX(8);
            $this->Cell(95, 2.5, $this->datos_empresa['web_empresa'], 0, 1, 'L');
        }

        // Info box (quotation details, close below)
        $y_info = $this->GetY() + 1;
        $this->SetY($y_info);

        // Coloured background for info box
        $this->SetFillColor(102, 126, 234); // Blue/purple
        $this->SetTextColor(255, 255, 255); // White text
        $this->SetFont('helvetica', 'B', 8.5);

        $this->SetXY(8, $y_info);
        $this->Cell(95, 10, '', 0, 0, 'L', true); // Box background

        // Box content in two lines
        $this->SetXY(9, $y_info + 1);
        $info_text_linea1 = 'No: ' . ($this->datos_presupuesto['numero_presupuesto'] ?? '') .
                     '  |  D: ' . $this->fecha_presupuesto .
                     '  |  Val: ' . ($this->fecha_validez ?: 'N/A') .
                     '  |  Ver: ' . ($this->datos_presupuesto['numero_version_presupuesto'] ?? '1');
        
        $this->Cell(93, 3, $info_text_linea1, 0, 1, 'L');
        
        // Second line with client reference (if available)
        if (!empty($this->datos_presupuesto['numero_pedido_cliente_presupuesto'])) {
            $this->SetXY(9, $y_info + 5);
            $info_text_linea2 = 'Client Ref: ' . $this->datos_presupuesto['numero_pedido_cliente_presupuesto'];
            $this->Cell(93, 3, $info_text_linea2, 0, 1, 'L');
        }

        // Restore text colour
        $this->SetTextColor(0, 0, 0);

        // ============================================
        // HEADER NOTES (LEFT COLUMN)
        // ============================================

        if (!empty($this->observaciones)) {
            $y_obs = $this->GetY() + 2;
            $this->SetY($y_obs);
            $this->SetX(8);

            $this->SetFont('helvetica', '', 6.5);
            $this->SetTextColor(99, 110, 114);
            $this->MultiCell(95, 3, $this->observaciones, 0, 'L');
        }

        // Save final Y of left column
        $y_final_izquierda = $this->GetY();
        
        // ============================================
        // RIGHT COLUMN: Client Data Box
        // ============================================

        $col2_x = 108;
        $col2_width = 94;
        $box_y_start = $y_start;
        
        // Calculate required height for client box
        $client_box_height = 26;
        
        if (!empty($this->datos_presupuesto['nombre_contacto_cliente'])) {
            $client_box_height += 10;
        }
        
        // Light grey background
        $this->SetFillColor(248, 249, 250);
        $this->Rect($col2_x, $box_y_start, $col2_width, $client_box_height, 'F');
        
        // Green border
        $this->SetDrawColor(39, 174, 96);
        $this->SetLineWidth(0.5);
        $this->Rect($col2_x, $box_y_start, $col2_width, $client_box_height);
        $this->SetLineWidth(0.2);
        
        // Title "CLIENT"
        $this->SetXY($col2_x + 2, $box_y_start + 1.5);
        $this->SetFont('helvetica', 'B', 9);
        $this->SetTextColor(44, 62, 80);
        $this->Cell($col2_width - 4, 3.5, 'CLIENT', 0, 1, 'L');
        
        // Line under title
        $this->SetDrawColor(39, 174, 96);
        $this->Line($col2_x + 2, $box_y_start + 5.5, $col2_x + $col2_width - 2, $box_y_start + 5.5);
        
        // Client data
        $this->SetXY($col2_x + 2, $box_y_start + 6.5);
        $this->SetFont('helvetica', '', 8);
        $this->SetTextColor(52, 73, 94);
        
        $nombre_completo = trim(
            ($this->datos_presupuesto['nombre_cliente'] ?? '') . ' ' . 
            ($this->datos_presupuesto['apellido_cliente'] ?? '')
        );
        
        // Full name
        if (!empty($nombre_completo)) {
            $this->SetFont('helvetica', 'B', 8.5);
            $this->Cell($col2_width - 4, 3, $nombre_completo, 0, 1, 'L');
            $this->SetX($col2_x + 2);
            $this->SetFont('helvetica', '', 7.5);
        }
        
        // VAT No if available
        if (!empty($this->datos_presupuesto['nif_cliente'])) {
            $this->SetFont('helvetica', '', 7.5);
            $this->Cell(15, 2.5, 'VAT No:', 0, 0, 'L');
            $this->SetFont('helvetica', 'B', 7.5);
            $this->Cell($col2_width - 19, 2.5, $this->datos_presupuesto['nif_cliente'], 0, 1, 'L');
            $this->SetX($col2_x + 2);
        }
        
        // Address
        if (!empty($this->datos_presupuesto['direccion_cliente'])) {
            $this->SetFont('helvetica', '', 7.5);
            $this->Cell(15, 2.5, 'Address:', 0, 0, 'L');
            
            $direccion_cliente = $this->datos_presupuesto['direccion_cliente'];
            $partes = [];
            if (!empty($this->datos_presupuesto['cp_cliente'])) {
                $partes[] = $this->datos_presupuesto['cp_cliente'];
            }
            if (!empty($this->datos_presupuesto['poblacion_cliente'])) {
                $partes[] = $this->datos_presupuesto['poblacion_cliente'];
            }
            if (!empty($partes)) {
                $direccion_cliente .= ', ' . implode(' ', $partes);
            }
            
            $this->MultiCell($col2_width - 19, 2.5, $direccion_cliente, 0, 'L');
            $this->SetX($col2_x + 2);
        }
        
        // Email
        if (!empty($this->datos_presupuesto['email_cliente'])) {
            $this->SetFont('helvetica', '', 7.5);
            $this->Cell(15, 2.5, 'Email:', 0, 0, 'L');
            $this->Cell($col2_width - 19, 2.5, $this->datos_presupuesto['email_cliente'], 0, 1, 'L');
            $this->SetX($col2_x + 2);
        }
        
        // Telephone
        if (!empty($this->datos_presupuesto['telefono_cliente'])) {
            $this->SetFont('helvetica', '', 7.5);
            $this->Cell(15, 2.5, 'Telephone:', 0, 0, 'L');
            $this->Cell($col2_width - 19, 2.5, $this->datos_presupuesto['telefono_cliente'], 0, 1, 'L');
            $this->SetX($col2_x + 2);
        }
        
        // ============================================
        // ATTENTION OF (Client contact)
        // ============================================
        if (!empty($this->datos_presupuesto['nombre_contacto_cliente'])) {
            $this->Ln(1);
            
            // Label "Attention of:"
            $this->SetXY($col2_x + 2, $this->GetY());
            $this->SetFont('helvetica', 'B', 8);
            $this->SetTextColor(39, 174, 96); // Green colour
            $this->Cell($col2_width - 4, 3, 'Attention of:', 0, 1, 'L');

            $this->SetX($col2_x + 2);
            $this->SetFont('helvetica', '', 7.5);
            $this->SetTextColor(52, 73, 94);
            
            // Contact name
            $nombre_contacto = trim(
                ($this->datos_presupuesto['nombre_contacto_cliente'] ?? '') . ' ' .
                ($this->datos_presupuesto['apellidos_contacto_cliente'] ?? '')
            );
            
            if (!empty($nombre_contacto)) {
                $this->Cell(15, 2.5, 'Name:', 0, 0, 'L');
                $this->SetFont('helvetica', 'B', 7.5);
                $this->Cell($col2_width - 19, 2.5, $nombre_contacto, 0, 1, 'L');
                $this->SetX($col2_x + 2);
                $this->SetFont('helvetica', '', 7.5);
            }
            
            // Contact telephone
            if (!empty($this->datos_presupuesto['telefono_contacto_cliente'])) {
                $this->Cell(15, 2.5, 'Telephone:', 0, 0, 'L');
                $this->Cell($col2_width - 19, 2.5, $this->datos_presupuesto['telefono_contacto_cliente'], 0, 1, 'L');
                $this->SetX($col2_x + 2);
            }
            
            // Contact email
            if (!empty($this->datos_presupuesto['email_contacto_cliente'])) {
                $this->Cell(15, 2.5, 'Email:', 0, 0, 'L');
                $this->Cell($col2_width - 19, 2.5, $this->datos_presupuesto['email_contacto_cliente'], 0, 1, 'L');
            }
        }
        
        // ============================================
        // EVENT DETAILS AND LOCATION (RIGHT COLUMN, BELOW CLIENT)
        // ============================================

        $y_evento = $box_y_start + $client_box_height + 3;

        $evento_x = 108;
        $evento_width = 94;

        $y_despues_fechas_evento = $y_evento;

        // ========== EVENT DATES (ALWAYS SHOWN) ==========

        $this->SetXY($evento_x, $y_evento);

        // Event section title
        $this->SetFont('helvetica', 'B', 8);
        $this->SetTextColor(39, 174, 96); // Green colour
        $this->Cell($evento_width, 4, 'EVENT DETAILS', 0, 1, 'L');

        // Event dates in compact table format (3 columns)
        $this->SetX($evento_x);
        $this->SetFont('helvetica', '', 8);
        $this->SetTextColor(127, 140, 141); // Grey for labels

        // 3-column mini table: Start | End | Duration
        $mini_col_width = $evento_width / 3;

        // Labels
        $this->Cell($mini_col_width, 3, 'Start', 0, 0, 'C');
        $this->Cell($mini_col_width, 3, 'End', 0, 0, 'C');
        $this->Cell($mini_col_width, 3, 'Duration', 0, 1, 'C');

        // Values
        $this->SetX($evento_x);
        $this->SetFont('helvetica', 'B', 8);
        $this->SetTextColor(52, 73, 94);

        $fecha_inicio_str = '';
        if (!empty($this->fecha_evento)) {
            $fecha_inicio_str = $this->fecha_evento;
        }

        $fecha_fin_str = '';
        if (!empty($this->datos_presupuesto['fecha_fin_evento_presupuesto'])) {
            $fecha_fin_str = date('d/m/Y', strtotime($this->datos_presupuesto['fecha_fin_evento_presupuesto']));
        }

        $duracion_str = '';
        if (!empty($this->datos_presupuesto['duracion_evento_dias'])) {
            $duracion_str = $this->datos_presupuesto['duracion_evento_dias'] . ' days';
        }

        $this->Cell($mini_col_width, 4, $fecha_inicio_str, 0, 0, 'C');
        $this->Cell($mini_col_width, 4, $fecha_fin_str, 0, 0, 'C');
        $this->Cell($mini_col_width, 4, $duracion_str, 0, 1, 'C');

        $this->Ln(4);

        $y_despues_fechas_evento = $this->GetY();

        // ========== EVENT NAME AND LOCATION (CONDITIONAL) ==========

        if (!empty($this->datos_presupuesto['nombre_evento_presupuesto'])) {
            $this->SetX($evento_x);
            $this->SetFont('helvetica', 'B', 7.5);
            $this->SetFillColor(255, 243, 205); // Light yellow background
            $this->SetTextColor(133, 100, 4); // Dark text
            $this->MultiCell($evento_width, 4, $this->datos_presupuesto['nombre_evento_presupuesto'], 0, 'L', true);

            $this->Ln(1);
            $y_despues_fechas_evento = $this->GetY();
        }

        // ========== EVENT LOCATION ==========
        $ubicacion_completa = '';

        if (!empty($this->datos_presupuesto['direccion_evento_presupuesto'])) {
            $ubicacion_completa .= $this->datos_presupuesto['direccion_evento_presupuesto'];
        }

        if (!empty($this->datos_presupuesto['cp_evento_presupuesto']) ||
            !empty($this->datos_presupuesto['poblacion_evento_presupuesto'])) {

            if (!empty($ubicacion_completa)) {
                $ubicacion_completa .= ', ';
            }

            if (!empty($this->datos_presupuesto['cp_evento_presupuesto'])) {
                $ubicacion_completa .= $this->datos_presupuesto['cp_evento_presupuesto'] . ' ';
            }

            if (!empty($this->datos_presupuesto['poblacion_evento_presupuesto'])) {
                $ubicacion_completa .= $this->datos_presupuesto['poblacion_evento_presupuesto'];
            }
        }

        if (!empty($this->datos_presupuesto['provincia_evento_presupuesto'])) {
            if (!empty($ubicacion_completa)) {
                $ubicacion_completa .= ', ';
            }
            $ubicacion_completa .= $this->datos_presupuesto['provincia_evento_presupuesto'];
        }

        if (!empty($ubicacion_completa)) {
            $this->SetX($evento_x);
            $this->SetFont('helvetica', 'B', 8);
            $this->SetTextColor(243, 156, 18); // Orange colour
            $this->Cell($evento_width, 4, 'EVENT LOCATION', 0, 1, 'L');

            $this->SetX($evento_x);
            $this->SetFont('helvetica', '', 7);
            $this->SetTextColor(52, 73, 94);
            $this->Cell($evento_width, 3.5, $ubicacion_completa, 0, 1, 'L');

            $y_despues_fechas_evento = $this->GetY();
        }

        // Restore colours
        $this->SetDrawColor(0, 0, 0);
        $this->SetTextColor(0, 0, 0);

        // ============================================
        // HEADER NOTES (FULL WIDTH)
        // ============================================

        $y_observaciones_cabecera = max($y_final_izquierda, $y_despues_fechas_evento) + 6;

        if (!empty($this->observaciones_cabecera)) {
            $this->SetXY(8, $y_observaciones_cabecera);

            $this->SetFont('helvetica', '', 8);
            $this->SetTextColor(44, 62, 80);

            $ancho_total = 194;
            
            $this->MultiCell($ancho_total, 4, $this->observaciones_cabecera, 0, 'L');
        }
        
        // Position cursor for body content
        $final_y = $this->GetY();
        $this->SetY($final_y);
    }
    
    // Footer repeated on each page
    public function Footer() {
        // Company footer text (if available)
        if (!empty($this->texto_pie_empresa)) {
            $this->SetY(-20);
            
            // Decorative top line
            $this->SetDrawColor(44, 62, 80);
            $this->SetLineWidth(0.5);
            $this->Line(8, $this->GetY(), 202, $this->GetY());
            $this->SetY($this->GetY() + 1);
            
            // Company footer text
            $this->SetFont('helvetica', '', 7);
            $this->SetTextColor(99, 110, 114);
            $this->MultiCell(0, 3, $this->texto_pie_empresa, 0, 'C');
        }
        
        // Page numbers (always at the bottom)
        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 8);
        $this->SetTextColor(0, 0, 0);
        $this->Cell(0, 10, 'Page ' . $this->getAliasNumPage() . ' of ' . $this->getAliasNbPages(), 0, 0, 'C');
    }
}

// =====================================================
// INITIALISE CLASSES
// =====================================================
$registro = new RegistroActividad();
$impresion = new ImpresionPresupuesto();
$kitModel = new Kit();

// Main switch based on operation
switch ($_GET["op"]) {
    
    case "cli_eng":
        try {
            // 1. Validate that the quotation ID was received
            if (!isset($_POST['id_presupuesto']) || empty($_POST['id_presupuesto'])) {
                throw new Exception("No se recibió el ID del presupuesto");
            }
            
            $id_presupuesto = intval($_POST['id_presupuesto']);
            $numero_version = !empty($_POST['numero_version']) ? intval($_POST['numero_version']) : null;
            
            // 2. Get quotation data (current version or the one specified)
            $datos_presupuesto = $impresion->get_datos_cabecera($id_presupuesto, $numero_version);
            
            if (!$datos_presupuesto) {
                throw new Exception("No se encontraron datos del presupuesto ID: $id_presupuesto");
            }
            
            // 3. Get company data
            $datos_empresa = $impresion->get_empresa_datos();
            
            if (!$datos_empresa) {
                throw new Exception("No se encontraron datos de la empresa");
            }

            // Subtotals per date configuration
            $mostrar_subtotales_fecha = !isset($datos_empresa['mostrar_subtotales_fecha_presupuesto_empresa'])
                ? true
                : ($datos_empresa['mostrar_subtotales_fecha_presupuesto_empresa'] == 1);
            
            // 4. Validate company logo
            $mostrar_logo = false;
            $path_logo = '';
            
            if (!empty($datos_empresa['logotipo_empresa'])) {
                if ($impresion->validar_logo($datos_empresa['logotipo_empresa'])) {
                    $mostrar_logo = true;
                    
                    $logo_name = $datos_empresa['logotipo_empresa'];
                    $logo_name = ltrim($logo_name, '/');
                    if (strpos($logo_name, 'public/') === 0) {
                        $logo_name = substr($logo_name, 7);
                    }
                    
                    $path_logo = __DIR__ . '/../public/' . $logo_name;
                    
                    if (!file_exists($path_logo)) {
                        $mostrar_logo = false;
                        $path_logo = null;
                    } else {
                        $path_logo = realpath($path_logo);
                    }
                }
            }
            
            // 5. Format dates (DD/MM/YYYY)
            $fecha_presupuesto = !empty($datos_presupuesto['fecha_presupuesto']) 
                ? date('d/m/Y', strtotime($datos_presupuesto['fecha_presupuesto'])) 
                : '';
            
            $fecha_validez = !empty($datos_presupuesto['fecha_validez_presupuesto']) 
                ? date('d/m/Y', strtotime($datos_presupuesto['fecha_validez_presupuesto'])) 
                : '';
            
            $fecha_inicio_evento = !empty($datos_presupuesto['fecha_inicio_evento_presupuesto']) 
                ? date('d/m/Y', strtotime($datos_presupuesto['fecha_inicio_evento_presupuesto'])) 
                : '';
            
            $fecha_fin_evento = !empty($datos_presupuesto['fecha_fin_evento_presupuesto']) 
                ? date('d/m/Y', strtotime($datos_presupuesto['fecha_fin_evento_presupuesto'])) 
                : '';
            
            // 6. Get quotation lines (English version: includes name_articulo and observaciones_linea_ppto_en)
            $lineas = $impresion->get_lineas_impresion_en($id_presupuesto, $numero_version);
            
            // 6.1 Group lines by start date and location
            $lineas_agrupadas = [];
            foreach ($lineas as $linea) {
                $fecha_inicio = $linea['fecha_inicio_linea_ppto'];
                $id_ubicacion = $linea['id_ubicacion'] ?? 0;
                
                if (!isset($lineas_agrupadas[$fecha_inicio])) {
                    $lineas_agrupadas[$fecha_inicio] = [
                        'ubicaciones' => [],
                        'subtotal_fecha' => 0,
                        'total_iva_fecha' => 0,
                        'total_fecha' => 0
                    ];
                }
                
                if (!isset($lineas_agrupadas[$fecha_inicio]['ubicaciones'][$id_ubicacion])) {
                    $lineas_agrupadas[$fecha_inicio]['ubicaciones'][$id_ubicacion] = [
                        'nombre_ubicacion' => $linea['nombre_ubicacion'] ?? 'No location',
                        'ubicacion_completa' => $linea['ubicacion_completa_agrupacion'] ?? '',
                        'lineas' => [],
                        'subtotal_ubicacion' => 0,
                        'total_iva_ubicacion' => 0,
                        'total_ubicacion' => 0
                    ];
                }
                
                $lineas_agrupadas[$fecha_inicio]['ubicaciones'][$id_ubicacion]['lineas'][] = $linea;
                
                $base_imponible = floatval($linea['base_imponible']);
                $total_linea = floatval($linea['total_linea']);
                $total_iva_linea = $total_linea - $base_imponible;
                
                $lineas_agrupadas[$fecha_inicio]['ubicaciones'][$id_ubicacion]['subtotal_ubicacion'] += $base_imponible;
                $lineas_agrupadas[$fecha_inicio]['ubicaciones'][$id_ubicacion]['total_iva_ubicacion'] += $total_iva_linea;
                $lineas_agrupadas[$fecha_inicio]['ubicaciones'][$id_ubicacion]['total_ubicacion'] += $total_linea;
                
                $lineas_agrupadas[$fecha_inicio]['subtotal_fecha'] += $base_imponible;
                $lineas_agrupadas[$fecha_inicio]['total_iva_fecha'] += $total_iva_linea;
                $lineas_agrupadas[$fecha_inicio]['total_fecha'] += $total_linea;
            }
            
            // 7. Get observations (English version with fallback to Spanish)
            $observaciones_array = $impresion->get_observaciones_presupuesto_en($id_presupuesto, $numero_version);
            $observaciones = '';
            if (!empty($observaciones_array) && is_array($observaciones_array)) {
                $textos = [];
                foreach ($observaciones_array as $obs) {
                    if (!empty($obs['texto_observacion'])) {
                        $textos[] = $obs['texto_observacion'];
                    }
                }
                $observaciones = implode("\n\n", $textos);
            } elseif (is_string($observaciones_array)) {
                $observaciones = $observaciones_array;
            }
            
            // 8. Calculate VAT breakdown and discounts
            $desglose_iva = [];
            $subtotal_sin_iva = 0;
            $subtotal_sin_descuento = 0;
            $total_descuentos = 0;
            
            foreach ($lineas as $linea) {
                $cantidad = floatval($linea['cantidad_linea_ppto'] ?? 0);
                $precio_unitario = floatval($linea['precio_unitario_linea_ppto'] ?? 0);
                $dias = floatval($linea['dias_linea'] ?? 1);
                $coeficiente = floatval($linea['valor_coeficiente_linea_ppto'] ?? null);
                $aplica_coeficiente = ($coeficiente !== null && $coeficiente > 0);
                $descuento_pct = floatval($linea['descuento_linea_ppto'] ?? 0);
                
                if ($aplica_coeficiente) {
                    $subtotal_linea_sin_desc = $cantidad * $precio_unitario * $coeficiente;
                } else {
                    $subtotal_linea_sin_desc = $dias * $cantidad * $precio_unitario;
                }
                
                $importe_descuento_linea = $subtotal_linea_sin_desc * ($descuento_pct / 100);
                
                $subtotal_sin_descuento += $subtotal_linea_sin_desc;
                $total_descuentos += $importe_descuento_linea;
                
                $base_imponible = floatval($linea['base_imponible'] ?? 0);
                $porcentaje_iva = floatval($linea['porcentaje_iva_linea_ppto'] ?? 0);
                
                $subtotal_sin_iva += $base_imponible;
                
                if (!isset($desglose_iva[$porcentaje_iva])) {
                    $desglose_iva[$porcentaje_iva] = [
                        'base' => 0,
                        'cuota' => 0
                    ];
                }
                
                $cuota_iva = $base_imponible * ($porcentaje_iva / 100);
                $desglose_iva[$porcentaje_iva]['base'] += $base_imponible;
                $desglose_iva[$porcentaje_iva]['cuota'] += $cuota_iva;
            }
            
            ksort($desglose_iva);
            
            $total_iva = 0;
            foreach ($desglose_iva as $iva) {
                $total_iva += $iva['cuota'];
            }
            
            $total_presupuesto = $subtotal_sin_iva + $total_iva;
            
            // =====================================================
            // FOOTER NOTES (English with fallback to Spanish)
            // =====================================================
            $obs_pie_final = !empty($datos_presupuesto['observaciones_pie_ingles_presupuesto'])
                ? $datos_presupuesto['observaciones_pie_ingles_presupuesto']
                : ($datos_presupuesto['observaciones_pie_presupuesto'] ?? '');

            // =====================================================
            // CREATE PDF WITH TCPDF
            // =====================================================
            
            $pdf = new MYPDF('P', 'mm', 'A4', true, 'UTF-8', false);
            
            $pdf->SetCreator('MDR ERP');
            $pdf->SetAuthor($datos_empresa['nombre_empresa'] ?? 'MDR');
            $pdf->SetTitle('Quotation ' . ($datos_presupuesto['numero_presupuesto'] ?? ''));
            $pdf->SetSubject('Quotation for ' . ($datos_presupuesto['nombre_evento_presupuesto'] ?? ''));
            
            $pdf->SetMargins(8, 95, 8);
            $pdf->SetHeaderMargin(5);
            $pdf->SetFooterMargin(10);
            $pdf->SetAutoPageBreak(TRUE, 25);
            
            $pdf->setDatosHeader(
                $datos_empresa,
                $datos_presupuesto,
                $mostrar_logo,
                $path_logo,
                $fecha_presupuesto,
                $fecha_validez,
                $fecha_inicio_evento,
                $observaciones,
                // EN header notes with fallback to Spanish
                (!empty($datos_presupuesto['observaciones_cabecera_ingles_presupuesto'])
                    ? $datos_presupuesto['observaciones_cabecera_ingles_presupuesto']
                    : ($datos_presupuesto['observaciones_cabecera_presupuesto'] ?? '')),
                $datos_empresa['texto_pie_presupuesto_empresa'] ?? ''
            );
            
            $pdf->AddPage();
            
            // =====================================================
            // ANALYSIS OF SET-UP AND BREAKDOWN DATES
            // =====================================================
            
            $analisis_fechas = [];
            
            foreach ($lineas_agrupadas as $fecha_inicio => $grupo_fecha) {
                $todas_lineas = [];
                foreach ($grupo_fecha['ubicaciones'] as $grupo_ubicacion) {
                    $todas_lineas = array_merge($todas_lineas, $grupo_ubicacion['lineas']);
                }
                
                $total_lineas = count($todas_lineas);
                
                if ($total_lineas == 0) {
                    $analisis_fechas[$fecha_inicio] = [
                        'ocultar_columnas' => false,
                        'fecha_montaje' => null,
                        'fecha_desmontaje' => null,
                        'excepciones' => []
                    ];
                    continue;
                }
                
                $combinaciones = [];
                foreach ($todas_lineas as $idx => $linea) {
                    $fecha_mtje_raw = $linea['fecha_montaje_linea_ppto'] ?? null;
                    $fecha_dsmtje_raw = $linea['fecha_desmontaje_linea_ppto'] ?? null;
                    
                    if (!empty($fecha_mtje_raw) && $fecha_mtje_raw != '0000-00-00' && $fecha_mtje_raw != '0000-00-00 00:00:00') {
                        $mtje = date('Y-m-d', strtotime($fecha_mtje_raw));
                    } else {
                        $mtje = '';
                    }
                    
                    if (!empty($fecha_dsmtje_raw) && $fecha_dsmtje_raw != '0000-00-00' && $fecha_dsmtje_raw != '0000-00-00 00:00:00') {
                        $dsmtje = date('Y-m-d', strtotime($fecha_dsmtje_raw));
                    } else {
                        $dsmtje = '';
                    }
                    
                    $clave = $mtje . '|' . $dsmtje;
                    
                    if (!isset($combinaciones[$clave])) {
                        $combinaciones[$clave] = [
                            'count' => 0,
                            'montaje' => $mtje,
                            'desmontaje' => $dsmtje,
                            'indices' => []
                        ];
                    }
                    
                    $combinaciones[$clave]['count']++;
                    $combinaciones[$clave]['indices'][] = $linea['id_linea_ppto'];
                }
                
                $max_count = 0;
                $combinacion_predominante = null;
                
                foreach ($combinaciones as $clave => $datos) {
                    if ($datos['count'] > $max_count) {
                        $max_count = $datos['count'];
                        $combinacion_predominante = $datos;
                    }
                }
                
                $porcentaje = ($max_count / $total_lineas) * 100;
                
                if ($porcentaje >= 30) {
                    $excepciones = [];
                    foreach ($todas_lineas as $linea) {
                        if (!in_array($linea['id_linea_ppto'], $combinacion_predominante['indices'])) {
                            $excepciones[] = $linea['id_linea_ppto'];
                        }
                    }
                    
                    $analisis_fechas[$fecha_inicio] = [
                        'ocultar_columnas' => true,
                        'fecha_montaje' => $combinacion_predominante['montaje'],
                        'fecha_desmontaje' => $combinacion_predominante['desmontaje'],
                        'excepciones' => $excepciones
                    ];
                } else {
                    $analisis_fechas[$fecha_inicio] = [
                        'ocultar_columnas' => false,
                        'fecha_montaje' => null,
                        'fecha_desmontaje' => null,
                        'excepciones' => []
                    ];
                }
            }
            
            // =====================================================
            // TABLE OF LINES GROUPED BY DATE AND LOCATION
            // =====================================================
            
            $pdf->SetFont('helvetica', 'B', 8);
            
            foreach ($lineas_agrupadas as $fecha_inicio => $grupo_fecha) {
                // =====================================================
                // START DATE HEADER
                // =====================================================
                
                $fecha_formateada = date('d/m/Y', strtotime($fecha_inicio));
                
                $texto_cabecera = 'Start date: ' . $fecha_formateada;
                
                $info_fechas = $analisis_fechas[$fecha_inicio] ?? ['ocultar_columnas' => false];
                
                if ($info_fechas['ocultar_columnas']) {
                    $mtje_formateada = !empty($info_fechas['fecha_montaje']) ? date('d/m/Y', strtotime($info_fechas['fecha_montaje'])) : '-';
                    $dsmtje_formateada = !empty($info_fechas['fecha_desmontaje']) ? date('d/m/Y', strtotime($info_fechas['fecha_desmontaje'])) : '-';
                    $texto_cabecera .= ' | Set-up: ' . $mtje_formateada . ' | Breakdown: ' . $dsmtje_formateada;
                }
                
                // Start date row with blue background
                $pdf->SetFillColor(52, 152, 219); // Blue
                $pdf->SetTextColor(255, 255, 255); // White text
                $pdf->SetFont('helvetica', 'B', 9);
                $pdf->Cell(194, 6, $texto_cabecera, 0, 1, 'L', true);
                
                // Restore colours
                $pdf->SetFillColor(255, 255, 255);
                $pdf->SetTextColor(0, 0, 0);
                
                foreach ($grupo_fecha['ubicaciones'] as $id_ubicacion => $grupo_ubicacion) {
                    // Show location name
                    $pdf->SetFont('helvetica', 'B', 8);
                    $ubicacion_text = $grupo_ubicacion['nombre_ubicacion'];
                    $pdf->Cell(194, 5, $ubicacion_text, 0, 1, 'L');
                    
                    $pdf->Ln(1);
                    
                    // Table header with grey borders
                    $pdf->SetFont('helvetica', 'B', 8);
                    $pdf->SetFillColor(240, 240, 240);
                    $pdf->SetDrawColor(200, 200, 200);
                    
                    $ocultar_cols = $info_fechas['ocultar_columnas'];
                    
                    $pdf->Cell(17, 6, 'Start', 1, 0, 'C', 1);
                    $pdf->Cell(17, 6, 'End', 1, 0, 'C', 1);
                    
                    if (!$ocultar_cols) {
                        $pdf->Cell(15, 6, 'Set-up', 1, 0, 'C', 1);
                        $pdf->Cell(15, 6, 'Brkdwn', 1, 0, 'C', 1);
                    }
                    
                    $pdf->Cell(8, 6, 'Days', 1, 0, 'C', 1);
                    $pdf->Cell(10, 6, 'Coef.', 1, 0, 'C', 1);
                    
                    $ancho_descripcion = $ocultar_cols ? 79 : 49;
                    $pdf->Cell($ancho_descripcion, 6, 'Description', 1, 0, 'C', 1);
                    
                    $pdf->Cell(12, 6, 'Qty', 1, 0, 'C', 1);
                    $pdf->Cell(15, 6, 'Unit P.', 1, 0, 'C', 1);
                    $pdf->Cell(12, 6, '%Disc', 1, 0, 'C', 1);
                    $pdf->Cell(24, 6, 'Amount(€)', 1, 1, 'C', 1);
                    $pdf->SetDrawColor(0, 0, 0);
                    
                    $pdf->SetFont('helvetica', '', 7);
                    
                    foreach ($grupo_ubicacion['lineas'] as $linea) {
                        $pdf->SetFont('helvetica', '', 7);

                        // Dates
                        $fecha_inicio_linea = !empty($linea['fecha_inicio_linea_ppto']) ? date('d/m/Y', strtotime($linea['fecha_inicio_linea_ppto'])) : '-';
                        $fecha_fin = !empty($linea['fecha_fin_linea_ppto']) ? date('d/m/Y', strtotime($linea['fecha_fin_linea_ppto'])) : '-';
                        $fecha_montaje = !empty($linea['fecha_montaje_linea_ppto']) ? date('d/m/Y', strtotime($linea['fecha_montaje_linea_ppto'])) : '-';
                        $fecha_desmontaje = !empty($linea['fecha_desmontaje_linea_ppto']) ? date('d/m/Y', strtotime($linea['fecha_desmontaje_linea_ppto'])) : '-';
                        
                        $es_excepcion = in_array($linea['id_linea_ppto'], $info_fechas['excepciones'] ?? []);
                        $es_kit = (isset($linea['es_kit_articulo']) && $linea['es_kit_articulo'] == 1);
                        
                        // EN name (COALESCE resuelto en SQL): name_articulo EN → nombre_articulo ES
                        // Líneas libres (sin artículo vinculado) usan descripcion_linea_ppto
                        $descripcion = !empty($linea['name_articulo_display'])
                            ? $linea['name_articulo_display']
                            : ($linea['descripcion_linea_ppto'] ?? '');
                        $cantidad = floatval($linea['cantidad_linea_ppto']);
                        $precio_unitario = floatval($linea['precio_unitario_linea_ppto']);
                        $descuento = floatval($linea['descuento_linea_ppto']);
                        $base_imponible = floatval($linea['base_imponible']);
                        
                        $altura_texto_desc = $pdf->getStringHeight($ancho_descripcion - 1, $descripcion);
                        
                        if ($altura_texto_desc > 5) {
                            $altura_fila = ceil($altura_texto_desc) + 1;
                        } else {
                            $altura_fila = 5;
                        }
                        
                        $necesita_dos_lineas = ($altura_fila > 5);
                        
                        $espacio_necesario = $altura_fila + 5;
                        $espacio_disponible = $pdf->getPageHeight() - $pdf->GetY() - $pdf->getBreakMargin();
                        
                        if ($espacio_disponible < $espacio_necesario) {
                            $pdf->AddPage();
                        }
                        
                        $x_inicial = $pdf->GetX();
                        $y_inicial = $pdf->GetY();
                        
                        $pdf->SetDrawColor(200, 200, 200);
                        
                        $pdf->Cell(17, $altura_fila, $fecha_inicio_linea, 1, 0, 'C');
                        $pdf->Cell(17, $altura_fila, $fecha_fin, 1, 0, 'C');
                        
                        if (!$ocultar_cols) {
                            $pdf->Cell(15, $altura_fila, $fecha_montaje, 1, 0, 'C');
                            $pdf->Cell(15, $altura_fila, $fecha_desmontaje, 1, 0, 'C');
                        }
                        
                        $pdf->Cell(8, $altura_fila, $linea['dias_linea'] ?? '0', 1, 0, 'C');
                        $pdf->Cell(10, $altura_fila, number_format(floatval($linea['valor_coeficiente_linea_ppto'] ?? 1.00), 2), 1, 0, 'C');
                        
                        $x_desc = $pdf->GetX();
                        if ($necesita_dos_lineas) {
                            $pdf->Rect($x_desc, $y_inicial, $ancho_descripcion, $altura_fila, 'D');
                            $margen_interno = 1;
                            $altura_linea_mc = ($altura_fila - $margen_interno) / max(1, floor($altura_texto_desc / 5));
                            $pdf->SetXY($x_desc + 0.5, $y_inicial + 0.5);
                            $pdf->MultiCell($ancho_descripcion - 1, $altura_linea_mc, $descripcion, 0, 'L');
                        } else {
                            $pdf->Cell($ancho_descripcion, $altura_fila, $descripcion, 1, 0, 'L');
                        }
                        
                        $pdf->SetXY($x_desc + $ancho_descripcion, $y_inicial);
                        
                        $pdf->Cell(12, $altura_fila, number_format($cantidad, 0), 1, 0, 'C');
                        $pdf->Cell(15, $altura_fila, number_format($precio_unitario, 2, ',', '.'), 1, 0, 'R');
                        $pdf->Cell(12, $altura_fila, number_format($descuento, 0), 1, 0, 'C');
                        $pdf->Cell(24, $altura_fila, number_format($base_imponible, 2, ',', '.'), 1, 0, 'R');
                        
                        $pdf->SetDrawColor(0, 0, 0);
                        
                        $pdf->SetXY($x_inicial, $y_inicial + $altura_fila);
                        
                        // LINE OBSERVATIONS (English with fallback to Spanish)
                        $observaciones_a_mostrar = '';
                        
                        // English observation field: fallback to Spanish if empty
                        $obs_linea_en = !empty($linea['observaciones_linea_ppto_en']) 
                            ? trim($linea['observaciones_linea_ppto_en']) 
                            : ((!empty($linea['observaciones_linea_ppto'])) ? trim($linea['observaciones_linea_ppto']) : '');
                        
                        if (!empty($obs_linea_en)) {
                            $observaciones_a_mostrar = $obs_linea_en;
                        }
                        
                        // If exception line, append individual dates
                        if ($es_excepcion && $ocultar_cols) {
                            $obs_fechas = 'Set-up: ' . $fecha_montaje . ' - Brkdwn: ' . $fecha_desmontaje;
                            if (!empty($observaciones_a_mostrar)) {
                                $observaciones_a_mostrar .= ' | ' . $obs_fechas;
                            } else {
                                $observaciones_a_mostrar = $obs_fechas;
                            }
                        }
                        
                        if (!empty($observaciones_a_mostrar)) {
                            $y_antes_obs = $pdf->GetY();
                            
                            $pdf->SetFont('helvetica', '', 6.5);
                            $pdf->SetTextColor(80, 80, 80);
                            
                            $obs_alineadas = !empty($datos_empresa['obs_linea_alineadas_descripcion_empresa']);
                            
                            if ($obs_alineadas) {
                                $texto_observaciones = $observaciones_a_mostrar;
                                $ancho_obs = 170 - ($x_desc - $x_inicial);
                                $pdf->MultiCell($ancho_obs, 4, $texto_observaciones, 0, 'L', false, 1, $x_desc, $y_antes_obs);
                            } else {
                                $texto_observaciones = '    ' . $observaciones_a_mostrar;
                                $pdf->MultiCell(170, 4, $texto_observaciones, 0, 'L', false, 1, '', $y_antes_obs);
                            }
                            
                            $pdf->SetTextColor(0, 0, 0);
                            $pdf->SetFont('helvetica', '', 7);
                        }
                        
                        // KIT components
                        if ($es_kit && !empty($linea['id_articulo']) && 
                            isset($linea['ocultar_detalle_kit_linea_ppto']) && 
                            $linea['ocultar_detalle_kit_linea_ppto'] == 0) {
                            
                            $componentes = $kitModel->get_kits_by_articulo_maestro($linea['id_articulo']);
                            
                            if (!empty($componentes)) {
                                $componentesActivos = array_filter($componentes, function($comp) {
                                    return isset($comp['activo_articulo_componente']) && $comp['activo_articulo_componente'] != 0;
                                });
                                
                                foreach ($componentesActivos as $comp) {
                                    $pdf->SetFont('helvetica', 'I', 6);
                                    $pdf->SetTextColor(100, 100, 100);
                                    $pdf->Cell(17, 4, '', 0, 0, 'C');
                                    $pdf->Cell(17, 4, '', 0, 0, 'C');
                                    
                                    if (!$ocultar_cols) {
                                        $pdf->Cell(15, 4, '', 0, 0, 'C');
                                        $pdf->Cell(15, 4, '', 0, 0, 'C');
                                    }
                                    
                                    $pdf->Cell(8, 4, '', 0, 0, 'C');
                                    $pdf->Cell(10, 4, '', 0, 0, 'C');
                                    
                                    $cantidad_comp = $comp['cantidad_kit'] ?? $comp['total_componente_kit'] ?? 1;
                                    $nombre_comp = $comp['nombre_articulo_componente'] ?? $comp['nombre_articulo'] ?? 'No name';
                                    
                                    $pdf->Cell($ancho_descripcion, 4, '    • ' . $cantidad_comp . 'x ' . $nombre_comp, 0, 0, 'L');
                                    $pdf->Cell(12, 4, '', 0, 0, 'C');
                                    $pdf->Cell(51, 4, '', 0, 1, 'R');
                                    
                                    $pdf->SetFont('helvetica', '', 7);
                                    $pdf->SetTextColor(0, 0, 0);
                                }
                            }
                        }
                    } // End foreach lines
                    
                    // Subtotal per location
                    $pdf->SetFont('helvetica', 'B', 7);
                    $pdf->SetFillColor(245, 245, 245);
                    $pdf->SetDrawColor(200, 200, 200);
                    $pdf->Cell(170, 5, 'Subtotal ' . $grupo_ubicacion['nombre_ubicacion'], 1, 0, 'R', 1);
                    $pdf->Cell(24, 5, number_format($grupo_ubicacion['subtotal_ubicacion'], 2, ',', '.'), 1, 1, 'R', 1);
                    $pdf->SetDrawColor(0, 0, 0);
                    $pdf->Ln(2);
                    
                } // End foreach locations
                
                // Subtotal per date
                if ($mostrar_subtotales_fecha) {
                    $pdf->SetFont('helvetica', 'B', 8);
                    $pdf->SetFillColor(220, 220, 220);
                    $pdf->SetDrawColor(200, 200, 200);
                    $pdf->Cell(170, 6, 'Subtotal Date ' . $fecha_formateada, 1, 0, 'R', 1);
                    $pdf->Cell(24, 6, number_format($grupo_fecha['subtotal_fecha'], 2, ',', '.'), 1, 1, 'R', 1);
                    $pdf->SetDrawColor(0, 0, 0);
                    $pdf->Ln(3);
                } else {
                    $pdf->Ln(2);
                }
                
            } // End foreach dates
            
            // =====================================================
            // TOTALS
            // =====================================================
            
            $pdf->Ln(5);
            
            $pdf->SetDrawColor(200, 200, 200);
            $pdf->Line(145, $pdf->GetY(), 200, $pdf->GetY());
            $pdf->Ln(3);
            
            // Subtotal (before discounts)
            $pdf->SetFont('helvetica', '', 9);
            $pdf->SetFillColor(248, 249, 250);
            $pdf->SetDrawColor(220, 220, 220);
            $pdf->Cell(144, 6, '', 0, 0);
            $pdf->Cell(30, 6, 'Subtotal:', 1, 0, 'R', 1);
            $pdf->SetFont('helvetica', 'B', 9);
            $pdf->Cell(20, 6, number_format($subtotal_sin_descuento, 2, ',', '.') . ' €', 1, 1, 'R', 1);
            
            // Discount (only if there is one)
            if ($total_descuentos > 0) {
                $pdf->SetFont('helvetica', '', 9);
                $pdf->SetFillColor(248, 249, 250);
                $pdf->SetDrawColor(220, 220, 220);
                $pdf->Cell(144, 6, '', 0, 0);
                $pdf->Cell(30, 6, 'Discount:', 1, 0, 'R', 1);
                $pdf->SetFont('helvetica', 'B', 9);
                $pdf->SetTextColor(231, 76, 60); // Red for discount
                $pdf->Cell(20, 6, '-' . number_format($total_descuentos, 2, ',', '.') . ' €', 1, 1, 'R', 1);
                $pdf->SetTextColor(0, 0, 0);
            }
            
            // Taxable Base
            $pdf->SetFont('helvetica', '', 9);
            $pdf->SetFillColor(248, 249, 250);
            $pdf->SetDrawColor(220, 220, 220);
            $pdf->Cell(144, 6, '', 0, 0);
            $pdf->Cell(30, 6, 'Taxable Base:', 1, 0, 'R', 1);
            $pdf->SetFont('helvetica', 'B', 9);
            $pdf->Cell(20, 6, number_format($subtotal_sin_iva, 2, ',', '.') . ' €', 1, 1, 'R', 1);
            
            // VAT Breakdown (only if more than one rate)
            if (count($desglose_iva) > 1) {
                $pdf->SetFont('helvetica', '', 8);
                $pdf->Cell(144, 5, '', 0, 0);
                $pdf->Cell(30, 5, 'VAT Breakdown:', 0, 1, 'R');
                
                foreach ($desglose_iva as $porcentaje => $valores) {
                    $pdf->Cell(144, 4, '', 0, 0);
                    $pdf->Cell(30, 4, "VAT Base {$porcentaje}%:", 0, 0, 'R');
                    $pdf->Cell(20, 4, number_format($valores['base'], 2, ',', '.') . ' €', 0, 1, 'R');
                    
                    $pdf->Cell(144, 4, '', 0, 0);
                    $pdf->Cell(30, 4, "VAT {$porcentaje}%:", 0, 0, 'R');
                    $pdf->Cell(20, 4, number_format($valores['cuota'], 2, ',', '.') . ' €', 0, 1, 'R');
                }
            }
            
            // Total VAT
            $pdf->SetFont('helvetica', '', 9);
            $pdf->SetFillColor(248, 249, 250);
            $pdf->SetDrawColor(220, 220, 220);
            $pdf->Cell(144, 6, '', 0, 0);
            
            if (count($desglose_iva) == 1) {
                $porcentaje_unico = key($desglose_iva);
                $pdf->Cell(30, 6, "Total VAT ({$porcentaje_unico}%):", 1, 0, 'R', 1);
            } else {
                $pdf->Cell(30, 6, 'Total VAT:', 1, 0, 'R', 1);
            }
            
            $pdf->Cell(20, 6, number_format($total_iva, 2, ',', '.') . ' €', 1, 1, 'R', 1);
            
            $pdf->Ln(2);
            
            // Grand total – highlighted
            $pdf->SetFont('helvetica', 'B', 11);
            $pdf->SetFillColor(102, 126, 234);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->SetDrawColor(80, 100, 200);
            $pdf->SetLineWidth(0.5);
            $pdf->Cell(144, 8, '', 0, 0);
            $pdf->Cell(20, 8, 'TOTAL:', 1, 0, 'R', 1);
            $pdf->Cell(30, 8, number_format($total_presupuesto, 2, ',', '.') . ' €', 1, 1, 'R', 1);
            
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetDrawColor(0, 0, 0);
            $pdf->SetLineWidth(0.2);
            
            // =====================================================
            // VAT EXEMPTION INFORMATION
            // =====================================================
            
            if (isset($datos_presupuesto['exento_iva_cliente']) && 
                $datos_presupuesto['exento_iva_cliente'] == 1 && 
                !empty($datos_presupuesto['justificacion_exencion_iva_cliente'])) {
                
                $pdf->Ln(6);
                
                $pdf->SetFont('helvetica', 'B', 9);
                $pdf->SetFillColor(255, 243, 205);
                $pdf->SetDrawColor(255, 193, 7);
                $pdf->SetTextColor(133, 100, 4);
                $pdf->Cell(0, 6, 'TAX INFORMATION - VAT EXEMPT CLIENT', 1, 1, 'C', 1);
                
                $pdf->SetFont('helvetica', 'I', 8);
                $pdf->SetFillColor(255, 252, 240);
                $pdf->SetTextColor(80, 80, 80);
                $pdf->MultiCell(0, 5, $datos_presupuesto['justificacion_exencion_iva_cliente'], 1, 'L', 1);
                
                $pdf->SetTextColor(0, 0, 0);
                $pdf->SetDrawColor(0, 0, 0);
            }
            
            // =====================================================
            // PAYMENT TERMS
            // =====================================================
            
            if (!empty($datos_presupuesto['nombre_pago'])) {
                $pdf->Ln(6);
                
                $pdf->SetFont('helvetica', 'B', 9);
                $pdf->SetTextColor(52, 73, 94);
                $pdf->Cell(40, 5, 'PAYMENT TERMS:', 0, 0, 'L');
                
                $frase_pago = [];
                
                if (!empty($datos_presupuesto['nombre_metodo_pago'])) {
                    $frase_pago[] = $datos_presupuesto['nombre_metodo_pago'];
                }
                
                // Deposit
                if (!empty($datos_presupuesto['porcentaje_anticipo_pago'])) {
                    $texto_anticipo = 'Deposit of ' . $datos_presupuesto['porcentaje_anticipo_pago'] . '%';
                    
                    if (isset($datos_presupuesto['dias_anticipo_pago'])) {
                        $dias_anticipo = intval($datos_presupuesto['dias_anticipo_pago']);
                        
                        if ($dias_anticipo < 0) {
                            $texto_anticipo .= ' (' . abs($dias_anticipo) . ' days before start)';
                        } elseif ($dias_anticipo > 0) {
                            $texto_anticipo .= ' (' . $dias_anticipo . ' days after start)';
                        } else {
                            $texto_anticipo .= ' (on the start date)';
                        }
                    }
                    
                    $frase_pago[] = $texto_anticipo;
                }
                
                // Final payment
                if (!empty($datos_presupuesto['porcentaje_final_pago']) && 
                    $datos_presupuesto['porcentaje_anticipo_pago'] < 100) {
                    $texto_final = 'Final payment of ' . $datos_presupuesto['porcentaje_final_pago'] . '%';
                    
                    if (isset($datos_presupuesto['dias_final_pago'])) {
                        $dias_final = intval($datos_presupuesto['dias_final_pago']);
                        
                        if ($dias_final < 0) {
                            $texto_final .= ' (' . abs($dias_final) . ' days before end)';
                        } elseif ($dias_final > 0) {
                            $texto_final .= ' (' . $dias_final . ' days after end)';
                        } else {
                            $texto_final .= ' (on the end date)';
                        }
                    }
                    
                    $frase_pago[] = $texto_final;
                }
                
                // Discount
                if (!empty($datos_presupuesto['descuento_pago']) && $datos_presupuesto['descuento_pago'] > 0) {
                    $frase_pago[] = 'Discount applied: ' . $datos_presupuesto['descuento_pago'] . '%';
                }
                
                $texto_forma_pago = implode('; ', $frase_pago) . '.';
                
                $pdf->SetFont('helvetica', '', 8);
                $pdf->SetTextColor(70, 70, 70);
                $pdf->MultiCell(160, 4, $texto_forma_pago, 0, 'L');
                
                // =====================================================
                // BANK DETAILS FOR BANK TRANSFER
                // =====================================================
                
                $forma_pago_lower = strtolower($datos_presupuesto['nombre_metodo_pago'] ?? '');
                $es_transferencia = (strpos($forma_pago_lower, 'transferencia') !== false 
                                  || strpos($forma_pago_lower, 'transfer') !== false);
                
                $tiene_datos_bancarios = (
                    !empty($datos_empresa['iban_empresa']) ||
                    !empty($datos_empresa['swift_empresa']) ||
                    !empty($datos_empresa['banco_empresa'])
                );
                
                $mostrar_cuenta_bancaria_activado = ($datos_empresa['mostrar_cuenta_bancaria_pdf_presupuesto_empresa'] ?? 1);
                
                if ($es_transferencia && $tiene_datos_bancarios && $mostrar_cuenta_bancaria_activado) {
                    
                    $altura_bloque = 5;
                    if (!empty($datos_empresa['banco_empresa'])) $altura_bloque += 3.5;
                    if (!empty($datos_empresa['iban_empresa'])) $altura_bloque += 3.5;
                    if (!empty($datos_empresa['swift_empresa'])) $altura_bloque += 3.5;
                    
                    if (($pdf->GetY() + $altura_bloque) > 270) {
                        $pdf->AddPage();
                        $pdf->SetY(15);
                    }
                    
                    $pdf->Ln(2);
                    
                    $x_inicio = $pdf->GetX();
                    $y_inicio = $pdf->GetY();
                    
                    $pdf->SetFillColor(245, 245, 245);
                    $pdf->SetDrawColor(180, 180, 180);
                    $pdf->Rect($x_inicio, $y_inicio, 195, $altura_bloque, 'DF');
                    
                    $pdf->SetXY($x_inicio + 2, $y_inicio + 1.5);
                    
                    $pdf->SetFont('helvetica', 'B', 7);
                    $pdf->SetTextColor(52, 73, 94);
                    $pdf->Cell(189, 3, 'BANK DETAILS FOR TRANSFER', 0, 1, 'L', false);
                    
                    $y_actual = $pdf->GetY() + 0.5;
                    
                    if (!empty($datos_empresa['banco_empresa'])) {
                        $pdf->SetXY($x_inicio + 2, $y_actual);
                        $pdf->SetFont('helvetica', '', 6);
                        $pdf->SetTextColor(70, 70, 70);
                        $pdf->Cell(20, 3, 'Bank:', 0, 0, 'L');
                        $pdf->SetFont('helvetica', 'B', 7);
                        $pdf->Cell(160, 3, $datos_empresa['banco_empresa'], 0, 1, 'L');
                        $y_actual += 3.5;
                    }
                    
                    if (!empty($datos_empresa['iban_empresa'])) {
                        $pdf->SetXY($x_inicio + 2, $y_actual);
                        $pdf->SetFont('helvetica', '', 6);
                        $pdf->SetTextColor(70, 70, 70);
                        $pdf->Cell(20, 3, 'IBAN:', 0, 0, 'L');
                        $pdf->SetFont('helvetica', 'B', 7);
                        
                        $iban_sin_espacios = str_replace(' ', '', $datos_empresa['iban_empresa']);
                        $iban_formateado = wordwrap($iban_sin_espacios, 4, ' ', true);
                        
                        $pdf->Cell(160, 3, $iban_formateado, 0, 1, 'L');
                        $y_actual += 3.5;
                    }
                    
                    if (!empty($datos_empresa['swift_empresa'])) {
                        $pdf->SetXY($x_inicio + 2, $y_actual);
                        $pdf->SetFont('helvetica', '', 6);
                        $pdf->SetTextColor(70, 70, 70);
                        $pdf->Cell(20, 3, 'SWIFT:', 0, 0, 'L');
                        $pdf->SetFont('helvetica', 'B', 7);
                        $pdf->Cell(160, 3, $datos_empresa['swift_empresa'], 0, 1, 'L');
                        $y_actual += 3.5;
                    }
                    
                    $pdf->SetY($y_inicio + $altura_bloque + 1.5);
                }
            }
            
            // =====================================================
            // FAMILY AND ARTICLE NOTES
            // =====================================================
            
            // Filter: only items with a name AND real text (EN with fallback to ES)
            $obs_con_contenido = array_filter(
                is_array($observaciones_array) ? $observaciones_array : [],
                function ($obs) {
                    $nombre = '';
                    if ($obs['tipo_observacion'] == 'familia' && !empty($obs['nombre_familia'])) {
                        $nombre = $obs['nombre_familia'];
                    } elseif ($obs['tipo_observacion'] == 'articulo' && !empty($obs['nombre_articulo'])) {
                        $nombre = $obs['nombre_articulo'];
                    }
                    $texto = $obs['observacion_en'] ?? '';
                    return !empty($nombre) && !empty(trim($texto));
                }
            );
            
            if (!empty($obs_con_contenido)) {
                $pdf->Ln(8);
                
                $pdf->SetFont('helvetica', 'B', 9.5);
                $pdf->SetTextColor(66, 66, 66);
                $pdf->Cell(0, 6, 'NOTES', 0, 1, 'L');
                
                $pdf->Ln(2);
                
                foreach ($obs_con_contenido as $obs) {
                    $simbolo = ($obs['tipo_observacion'] == 'familia') ? '*' : '**';
                    
                    $pdf->SetFillColor(250, 250, 250);
                    
                    $pdf->SetFont('helvetica', '', 8);
                    $pdf->SetTextColor(97, 97, 97);
                    $pdf->MultiCell(0, 4, $simbolo . ' ' . ($obs['observacion_en'] ?? ''), 0, 'L', 1);
                    
                    $pdf->Ln(3);
                }
            }
            
            // =====================================================
            // FOOTER NOTES – INLINE (when NOT highlighted)
            // =====================================================
            
            if (!empty($obs_pie_final) && 
                isset($datos_presupuesto['destacar_observaciones_pie_presupuesto']) && 
                $datos_presupuesto['destacar_observaciones_pie_presupuesto'] == 0) {
                
                if (empty($obs_con_contenido)) {
                    $pdf->Ln(8);
                    $pdf->SetFont('helvetica', 'B', 9.5);
                    $pdf->SetTextColor(66, 66, 66);
                    $pdf->Cell(0, 6, 'NOTES', 0, 1, 'L');
                    $pdf->Ln(2);
                }
                
                $pdf->SetFillColor(250, 250, 250);
                $pdf->SetFont('helvetica', '', 8);
                $pdf->SetTextColor(97, 97, 97);
                $pdf->MultiCell(0, 4, $obs_pie_final, 0, 'L', 1);
                $pdf->Ln(3);
            }
            
            // =====================================================
            // FOOTER NOTES – HIGHLIGHTED
            // =====================================================
            
            if (!empty($obs_pie_final) && 
                (!isset($datos_presupuesto['destacar_observaciones_pie_presupuesto']) || 
                 $datos_presupuesto['destacar_observaciones_pie_presupuesto'] == 1)) {
                $pdf->Ln(10);
                
                $pdf->SetDrawColor(44, 62, 80);
                $pdf->SetLineWidth(0.8);
                $pdf->Line(8, $pdf->GetY(), 202, $pdf->GetY());
                $pdf->Ln(3);
                
                $y_inicio = $pdf->GetY();
                $texto_altura = $pdf->getStringHeight(0, $obs_pie_final);
                $pdf->SetFillColor(248, 249, 250);
                $pdf->Rect(8, $y_inicio, 194, $texto_altura + 6, 'F');
                
                $pdf->SetFont('helvetica', '', 8);
                $pdf->SetTextColor(99, 110, 114);
                $pdf->SetXY(8, $y_inicio + 3);
                $pdf->MultiCell(194, 4, $obs_pie_final, 0, 'C');
                
                $pdf->SetDrawColor(223, 230, 233);
                $pdf->SetLineWidth(0.3);
                $pdf->Line(8, $pdf->GetY() + 3, 202, $pdf->GetY() + 3);
            }
            
            // =====================================================
            // SIGNATURE BOXES (AFTER FOOTER NOTES)
            // =====================================================
            
            $pdf->Ln(10);
            
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetDrawColor(0, 0, 0);
            $pdf->SetLineWidth(0.2);
            
            // Page-break check: ensure enough space for signatures
            $altura_necesaria_firmas = 45;
            $espacio_disponible = $pdf->getPageHeight() - $pdf->GetY() - $pdf->getBreakMargin();
            
            if ($espacio_disponible < $altura_necesaria_firmas) {
                $pdf->AddPage();
            }
            
            // Disable auto page-break during signatures to keep them together
            $pdf->SetAutoPageBreak(false);
            
            $ancho_casilla = 90;
            $separacion = 7;
            $x_inicio_izq = 8;
            $x_inicio_der = $x_inicio_izq + $ancho_casilla + $separacion;
            
            $y_inicio_firmas = $pdf->GetY();
            
            // ======== LEFT BOX: COMPANY SIGNATURE ========
            
            $pdf->SetXY($x_inicio_izq, $y_inicio_firmas);
            
            // Title – use company custom header or default
            $cabecera_firma = !empty($datos_empresa['cabecera_firma_presupuesto_empresa']) 
                ? strtoupper($datos_empresa['cabecera_firma_presupuesto_empresa']) 
                : 'COMMERCIAL DEPARTMENT';
            
            $pdf->SetFont('helvetica', 'B', 9);
            $pdf->Cell($ancho_casilla, 5, $cabecera_firma, 0, 1, 'C');
            
            $pdf->SetX($x_inicio_izq);
            
            // ========================================
            // DIGITAL SIGNATURE OF THE SALES PERSON
            // ========================================
            
            $firma_comercial = null;
            $nombre_firmante = null;
            
            if (isset($_SESSION['id_usuario']) && !empty($_SESSION['id_usuario'])) {
                try {
                    $comercialesModel = new Comerciales();
                    $datos_comercial = $comercialesModel->get_comercial_by_usuario($_SESSION['id_usuario']);
                    if ($datos_comercial) {
                        $firma_comercial = $datos_comercial['firma_comercial'] ?? null;
                        $nombre_raw = trim(($datos_comercial['nombre'] ?? '') . ' ' . ($datos_comercial['apellidos'] ?? ''));
                        $nombre_firmante = !empty($nombre_raw) ? $nombre_raw : null;
                    }
                } catch (Exception $e) {
                    error_log("Error al obtener datos del comercial: " . $e->getMessage());
                }
            }
            
            if (!empty($firma_comercial)) {
                if (preg_match('/^data:image\/(png|jpg|jpeg);base64,/', $firma_comercial)) {
                    $pdf->Ln(2);
                    
                    $x_firma = $x_inicio_izq + 15;
                    $y_firma = $pdf->GetY();
                    
                    try {
                        $imagen_base64 = preg_replace('/^data:image\/(png|jpg|jpeg);base64,/', '', $firma_comercial);
                        $imagen_decodificada = base64_decode($imagen_base64);
                        
                        $pdf->Image(
                            '@' . $imagen_decodificada,
                            $x_firma,
                            $y_firma,
                            60,
                            14,
                            'PNG',
                            '',
                            '',
                            false,
                            300,
                            '',
                            false,
                            false,
                            0,
                            false,
                            false,
                            true
                        );
                        
                        $pdf->SetY($y_firma + 15);
                        
                    } catch (Exception $e) {
                        error_log("Error al renderizar firma en PDF: " . $e->getMessage());
                        $pdf->Ln(18);
                    }
                } else {
                    $pdf->Ln(18);
                }
            } else {
                $pdf->Ln(18);
            }
            
            // Signature line
            $y_linea_izq = $pdf->GetY();
            $pdf->Line($x_inicio_izq + 10, $y_linea_izq, $x_inicio_izq + $ancho_casilla - 10, $y_linea_izq);
            
            $pdf->SetXY($x_inicio_izq, $y_linea_izq + 2);
            $pdf->SetFont('helvetica', '', 8);
            
            // Sales person name
            if (!empty($nombre_firmante)) {
                $pdf->SetXY($x_inicio_izq, $pdf->GetY());
                $pdf->SetFont('helvetica', 'I', 7);
                $pdf->Cell($ancho_casilla, 4, $nombre_firmante, 0, 1, 'C');
            } else {
                $pdf->Ln(2);
            }
            
            // Date (print date)
            $pdf->SetXY($x_inicio_izq, $pdf->GetY());
            $pdf->SetFont('helvetica', '', 7);
            $fecha_impresion = date('d/m/Y');
            $pdf->Cell($ancho_casilla, 4, 'Date: ' . $fecha_impresion, 0, 1, 'C');
            
            // ======== RIGHT BOX: CLIENT APPROVAL ========
            
            $pdf->SetXY($x_inicio_der, $y_inicio_firmas);
            
            $pdf->SetFont('helvetica', 'B', 9);
            $pdf->Cell($ancho_casilla, 5, 'CLIENT APPROVAL', 0, 1, 'C');
            
            $pdf->SetX($x_inicio_der);
            $pdf->Ln(18);
            
            // Signature line
            $y_linea_der = $pdf->GetY();
            $pdf->Line($x_inicio_der + 10, $y_linea_der, $x_inicio_der + $ancho_casilla - 10, $y_linea_der);
            
            $pdf->SetXY($x_inicio_der, $y_linea_der + 2);
            $pdf->SetFont('helvetica', '', 8);
            $pdf->Cell($ancho_casilla, 4, 'Client Signature', 0, 1, 'C');
            
            $pdf->SetX($x_inicio_der);
            $pdf->Ln(2);
            
            $pdf->SetXY($x_inicio_der, $pdf->GetY());
            $pdf->SetFont('helvetica', '', 7);
            $pdf->Cell($ancho_casilla, 4, 'Date: ___/___/______', 0, 1, 'C');
            
            // Restore auto page breaks
            $pdf->SetAutoPageBreak(TRUE, 25);
            
            // =====================================================
            // PDF OUTPUT
            // =====================================================
            
            ob_end_clean();
            
            $nombre_archivo = 'Quotation_' . ($datos_presupuesto['numero_presupuesto'] ?? 'SN') . '.pdf';
            
            $pdf->Output($nombre_archivo, 'I'); // 'I' = inline in browser
            
            $registro->registrarActividad(
                'admin',
                'impresionpresupuesto_m2_pdf_en.php',
                'cli_eng',
                "PDF generado para presupuesto ID: $id_presupuesto",
                'info'
            );
            
            exit;
            
        } catch (Exception $e) {
            ob_end_clean();
            
            $registro->registrarActividad(
                'admin',
                'impresionpresupuesto_m2_pdf_en.php',
                'cli_eng',
                "Error: " . $e->getMessage(),
                'error'
            );
            
            header('Content-Type: text/html; charset=utf-8');
            echo '<!DOCTYPE html>';
            echo '<html><head><meta charset="UTF-8"><title>Error</title></head><body>';
            echo '<h2>Error generating PDF</h2>';
            echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
            echo '<p><a href="javascript:history.back()">Go back</a></p>';
            echo '</body></html>';
            exit;
        }
        break;
        
    default:
        ob_end_clean();
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Invalid operation'
        ]);
        exit;
}
?>
