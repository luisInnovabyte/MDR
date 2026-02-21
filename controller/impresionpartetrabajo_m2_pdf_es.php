<?php
// =====================================================
// ALBARÁN DE CARGA - PARTE DE TRABAJO PARA TÉCNICOS
// Sin precios, descuentos, totales ni información comercial
// =====================================================

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
class MYPDF_ALBARAN extends TCPDF {
    private $datos_empresa;
    private $mostrar_logo;
    private $path_logo;
    private $datos_presupuesto;
    private $fecha_evento;
    private $observaciones;
    
    public function setDatosHeader($empresa, $presupuesto, $logo, $path_logo, $fecha_ev, $obs) {
        $this->datos_empresa = $empresa;
        $this->datos_presupuesto = $presupuesto;
        $this->mostrar_logo = $logo;
        $this->path_logo = $path_logo;
        $this->fecha_evento = $fecha_ev;
        $this->observaciones = $obs;
    }
    
    // Cabecera repetida en cada página
    public function Header() {
        $y_start = 10;

        // ============================================
        // TÍTULO "ALBARÁN DE CARGA" (JUSTIFICADO A LA DERECHA) - VERDE
        // ============================================

        $this->SetY($y_start);
        $this->SetFont('helvetica', 'B', 16);
        $this->SetTextColor(46, 204, 113); // Color verde
        $this->Cell(0, 8, 'ALBARAN DE CARGA', 0, 1, 'R'); // Alineado a la derecha

        $this->Ln(2); // Pequeño margen después del título

        // Ajustar y_start para el resto del contenido
        $y_start = $this->GetY();

        // ============================================
        // COLUMNA IZQUIERDA: Logo + Datos Empresa
        // ============================================

        // Logo en la parte superior izquierda
        if ($this->mostrar_logo && !empty($this->path_logo) && file_exists($this->path_logo)) {
            // Logo compacto
            $this->Image($this->path_logo, 8, $y_start, 35, 0, '', '', '', false, 300, '', false, false, 0);
            $logo_height = 18; // Altura reducida del logo
        } else {
            $logo_height = 0;
        }

        // Datos de la empresa (debajo del logo, más cerca)
        $y_empresa = $y_start + $logo_height + 1;
        $this->SetY($y_empresa);
        $this->SetX(8);
        
        // Nombre comercial empresa (negrita, tamaño grande)
        $this->SetFont('helvetica', 'B', 9);
        $this->SetTextColor(44, 62, 80); // Color oscuro
        $this->Cell(95, 3.5, $this->datos_empresa['nombre_comercial_empresa'] ?? '', 0, 1, 'L');

        // CIF en rojo (solo si NO termina en 0000)
        $nif_empresa = $this->datos_empresa['nif_empresa'] ?? '';
        if (substr($nif_empresa, -4) !== '0000') {
            $this->SetX(8);
            $this->SetFont('helvetica', 'B', 8);
            $this->SetTextColor(231, 76, 60); // Rojo
            $this->Cell(95, 2.5, 'CIF: ' . $nif_empresa, 0, 1, 'L');
        }

        // Dirección fiscal
        $this->SetX(8);
        $this->SetFont('helvetica', '', 7.5);
        $this->SetTextColor(52, 73, 94); // Color normal
        $linea1_dir = ($this->datos_empresa['direccion_fiscal_empresa'] ?? '');
        $prov = trim($this->datos_empresa['provincia_fiscal_empresa'] ?? '');
        $linea2_dir = trim(($this->datos_empresa['cp_fiscal_empresa'] ?? '') . ' ' .
                      ($this->datos_empresa['poblacion_fiscal_empresa'] ?? ''));
        if (!empty($prov)) {
            $linea2_dir .= ' (' . $prov . ')';
        }
        $this->MultiCell(95, 3, trim($linea1_dir), 0, 'L');
        if (!empty(trim($linea2_dir))) {
            $this->SetX(8);
            $this->MultiCell(95, 3, $linea2_dir, 0, 'L');
        }

        // Teléfono, móvil y email
        $this->SetX(8);
        $contacto = 'Tel: ' . ($this->datos_empresa['telefono_empresa'] ?? '');
        if (!empty($this->datos_empresa['movil_empresa'])) {
            $contacto .= ' | ' . $this->datos_empresa['movil_empresa'];
        }
        $this->Cell(95, 2.5, $contacto, 0, 1, 'L');

        $this->SetX(8);
        $this->Cell(95, 2.5, ($this->datos_empresa['email_empresa'] ?? ''), 0, 1, 'L');

        // Web (si existe)
        if (!empty($this->datos_empresa['web_empresa'])) {
            $this->SetX(8);
            $this->Cell(95, 2.5, $this->datos_empresa['web_empresa'], 0, 1, 'L');
        }

        // Caja con info del presupuesto (SOLO NÚMERO Y VERSIÓN)
        $y_info = $this->GetY() + 1;
        $this->SetY($y_info);

        // Fondo de color para la caja de info (verde para distinguir)
        $this->SetFillColor(46, 204, 113); // Color verde
        $this->SetTextColor(255, 255, 255); // Texto blanco
        $this->SetFont('helvetica', 'B', 8.5);

        $this->SetXY(8, $y_info);
        $this->Cell(95, 6, '', 0, 0, 'L', true); // Fondo de la caja ajustado

        // Contenido de la caja: SOLO Número y Versión
        $this->SetXY(9, $y_info + 1);
        $info_text = 'N°: ' . ($this->datos_presupuesto['numero_presupuesto'] ?? '') .
                     '  |  Ver: ' . ($this->datos_presupuesto['numero_version_presupuesto'] ?? '1');
        
        $this->Cell(93, 3, $info_text, 0, 1, 'L');

        // Restaurar color de texto
        $this->SetTextColor(0, 0, 0);

        // Guardar Y final de columna izquierda
        $y_final_izquierda = $this->GetY();
        
        // ============================================
        // COLUMNA DERECHA: Box Verde con Datos Cliente
        // ============================================

        $col2_x = 108;
        $col2_width = 94;
        $box_y_start = $y_start;
        
        // Calcular altura necesaria para el box del cliente
        $client_box_height = 26; // Altura base para cliente
        
        // Si hay contacto, aumentar altura
        if (!empty($this->datos_presupuesto['nombre_contacto_cliente'])) {
            $client_box_height += 10;
        }
        
        // Fondo verde claro
        $this->SetFillColor(248, 249, 250);
        $this->Rect($col2_x, $box_y_start, $col2_width, $client_box_height, 'F');
        
        // Borde verde
        $this->SetDrawColor(39, 174, 96);
        $this->SetLineWidth(0.5);
        $this->Rect($col2_x, $box_y_start, $col2_width, $client_box_height);
        $this->SetLineWidth(0.2);
        
        // Título "CLIENTE"
        $this->SetXY($col2_x + 2, $box_y_start + 1.5);
        $this->SetFont('helvetica', 'B', 9);
        $this->SetTextColor(44, 62, 80);
        $this->Cell($col2_width - 4, 3.5, 'CLIENTE', 0, 1, 'L');
        
        // Línea bajo el título
        $this->SetDrawColor(39, 174, 96);
        $this->Line($col2_x + 2, $box_y_start + 5.5, $col2_x + $col2_width - 2, $box_y_start + 5.5);
        
        // Datos del cliente
        $this->SetXY($col2_x + 2, $box_y_start + 6.5);
        $this->SetFont('helvetica', '', 8);
        $this->SetTextColor(52, 73, 94);
        
        $nombre_completo = trim(
            ($this->datos_presupuesto['nombre_cliente'] ?? '') . ' ' . 
            ($this->datos_presupuesto['apellido_cliente'] ?? '')
        );
        
        // Nombre completo
        if (!empty($nombre_completo)) {
            $this->SetFont('helvetica', 'B', 8.5);
            $this->Cell($col2_width - 4, 3, $nombre_completo, 0, 1, 'L');
            $this->SetX($col2_x + 2);
            $this->SetFont('helvetica', '', 7.5);
        }
        
        // NIF/CIF si existe
        if (!empty($this->datos_presupuesto['nif_cliente'])) {
            $this->SetFont('helvetica', '', 7.5);
            $this->Cell(15, 2.5, 'NIF/CIF:', 0, 0, 'L');
            $this->SetFont('helvetica', 'B', 7.5);
            $this->Cell($col2_width - 19, 2.5, $this->datos_presupuesto['nif_cliente'], 0, 1, 'L');
            $this->SetX($col2_x + 2);
        }
        
        // Dirección
        if (!empty($this->datos_presupuesto['direccion_cliente'])) {
            $this->SetFont('helvetica', '', 7.5);
            $this->Cell(15, 2.5, 'Direccion:', 0, 0, 'L');
            
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
        
        // Teléfono
        if (!empty($this->datos_presupuesto['telefono_cliente'])) {
            $this->SetFont('helvetica', '', 7.5);
            $this->Cell(15, 2.5, 'Telefono:', 0, 0, 'L');
            $this->Cell($col2_width - 19, 2.5, $this->datos_presupuesto['telefono_cliente'], 0, 1, 'L');
            $this->SetX($col2_x + 2);
        }
        
        // A LA ATENCIÓN DE (Contacto del cliente)
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
        
        // ============================================
        // DATOS DEL EVENTO Y UBICACIÓN (COLUMNA DERECHA, DEBAJO DEL CLIENTE)
        // ============================================

        $y_evento = $box_y_start + $client_box_height + 3;
        $evento_x = 108;
        $evento_width = 94;
        $y_despues_fechas_evento = $y_evento;

        // FECHAS DEL EVENTO
        $this->SetXY($evento_x, $y_evento);
        $this->SetFont('helvetica', 'B', 8);
        $this->SetTextColor(39, 174, 96);
        $this->Cell($evento_width, 4, 'DATOS DEL EVENTO', 0, 1, 'L');

        // Fechas del evento en formato tabla compacta (3 columnas)
        $this->SetX($evento_x);
        $this->SetFont('helvetica', '', 8);
        $this->SetTextColor(127, 140, 141);

        $mini_col_width = $evento_width / 3;

        // Labels
        $this->Cell($mini_col_width, 3, 'Inicio', 0, 0, 'C');
        $this->Cell($mini_col_width, 3, 'Fin', 0, 0, 'C');
        $this->Cell($mini_col_width, 3, 'Duracion', 0, 1, 'C');

        // Valores
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
            $duracion_str = $this->datos_presupuesto['duracion_evento_dias'] . ' dias';
        }

        $this->Cell($mini_col_width, 4, $fecha_inicio_str, 0, 0, 'C');
        $this->Cell($mini_col_width, 4, $fecha_fin_str, 0, 0, 'C');
        $this->Cell($mini_col_width, 4, $duracion_str, 0, 1, 'C');

        $this->Ln(4);
        $y_despues_fechas_evento = $this->GetY();

        // NOMBRE DEL EVENTO
        if (!empty($this->datos_presupuesto['nombre_evento_presupuesto'])) {
            $this->SetX($evento_x);
            $this->SetFont('helvetica', 'B', 7.5);
            $this->SetFillColor(255, 243, 205);
            $this->SetTextColor(133, 100, 4);
            $this->MultiCell($evento_width, 4, $this->datos_presupuesto['nombre_evento_presupuesto'], 0, 'L', true);

            $this->Ln(1);
            $y_despues_fechas_evento = $this->GetY();
        }

        // UBICACIÓN DEL EVENTO
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
            $this->SetTextColor(39, 174, 96);
            $this->Cell($evento_width, 4, 'UBICACION DEL EVENTO', 0, 1, 'L');

            $this->SetX($evento_x);
            $this->SetFont('helvetica', '', 7);
            $this->SetTextColor(52, 73, 94);
            $this->Cell($evento_width, 3.5, $ubicacion_completa, 0, 1, 'L');

            $y_despues_fechas_evento = $this->GetY();
        }

        // Restaurar colores
        $this->SetDrawColor(0, 0, 0);
        $this->SetTextColor(0, 0, 0);

        // NO MOSTRAR OBSERVACIONES DE CABECERA (eliminado completamente)
        
        // Posicionar cursor para contenido
        $final_y = max($y_final_izquierda, $y_despues_fechas_evento) + 6;
        $this->SetY($final_y);
    }
    
    // Pie de página repetido - SOLO NÚMERO DE PÁGINA
    public function Footer() {
        // Números de página (sin texto pie de empresa)
        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 8);
        $this->SetTextColor(0, 0, 0);
        $this->Cell(0, 10, 'Pagina ' . $this->getAliasNumPage() . ' de ' . $this->getAliasNbPages(), 0, 0, 'C');
    }
}

// =====================================================
// INICIALIZAR CLASES
// =====================================================
$registro = new RegistroActividad();
$impresion = new ImpresionPresupuesto();
$kitModel = new Kit();

// Switch principal basado en operación
switch ($_GET["op"]) {
    
    case "albaran_carga":
        try {
            // 1. Validar que se recibió el ID del presupuesto
            if (!isset($_POST['id_presupuesto']) || empty($_POST['id_presupuesto'])) {
                throw new Exception("No se recibió el ID del presupuesto");
            }
            
            $id_presupuesto = intval($_POST['id_presupuesto']);
            
            // 1b. Buscar la versión aprobada del presupuesto
            $numero_version_aprobada = $impresion->get_numero_version_aprobada($id_presupuesto);
            
            if (is_null($numero_version_aprobada)) {
                // Sin versión aprobada: salir con página de error clara
                ob_end_clean();
                header('Content-Type: text/html; charset=utf-8');
                echo '<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8">
                <title>Albarán de carga — Sin versión aprobada</title>
                <style>body{font-family:sans-serif;display:flex;align-items:center;justify-content:center;min-height:100vh;margin:0;background:#f8f9fa}
                .card{max-width:420px;padding:2.5rem 3rem;background:#fff;border-radius:12px;box-shadow:0 4px 24px rgba(0,0,0,.1);text-align:center}
                .icon{font-size:3rem;margin-bottom:1rem}
                h2{color:#d97706;margin:0 0 .75rem}
                p{color:#6b7280;line-height:1.6;margin:0}</style></head>
                <body><div class="card">
                <div class="icon">⚠️</div>
                <h2>Sin versión aprobada</h2>
                <p>El albarán de carga solo puede generarse a partir de una <strong>versión aprobada</strong> del presupuesto.<br><br>
                Aprueba una versión desde el historial de versiones y vuelve a intentarlo.</p>
                </div></body></html>';
                exit;
            }
            
            // 2. Obtener datos del presupuesto (usando la versión aprobada)
            $datos_presupuesto = $impresion->get_datos_cabecera($id_presupuesto, $numero_version_aprobada);
            
            if (!$datos_presupuesto) {
                throw new Exception("No se encontraron datos del presupuesto ID: $id_presupuesto");
            }
            
            // 3. Obtener datos de la empresa
            $datos_empresa = $impresion->get_empresa_datos();
            
            if (!$datos_empresa) {
                throw new Exception("No se encontraron datos de la empresa");
            }
            
            // 4. Validar logo de empresa
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
            
            // 5. Formatear fechas del evento
            $fecha_inicio_evento = !empty($datos_presupuesto['fecha_inicio_evento_presupuesto']) 
                ? date('d/m/Y', strtotime($datos_presupuesto['fecha_inicio_evento_presupuesto'])) 
                : '';
            
            // 6. Obtener líneas del presupuesto (versión aprobada)
            $lineas = $impresion->get_lineas_impresion($id_presupuesto, $numero_version_aprobada);
            
            // 6.1 Agrupar líneas por fecha de inicio y ubicación
            $lineas_agrupadas = [];
            foreach ($lineas as $linea) {
                $fecha_inicio = $linea['fecha_inicio_linea_ppto'];
                $id_ubicacion = $linea['id_ubicacion'] ?? 0;
                
                if (!isset($lineas_agrupadas[$fecha_inicio])) {
                    $lineas_agrupadas[$fecha_inicio] = [
                        'ubicaciones' => []
                    ];
                }
                
                if (!isset($lineas_agrupadas[$fecha_inicio]['ubicaciones'][$id_ubicacion])) {
                    $lineas_agrupadas[$fecha_inicio]['ubicaciones'][$id_ubicacion] = [
                        'nombre_ubicacion' => $linea['nombre_ubicacion'] ?? 'Sin ubicación',
                        'ubicacion_completa' => $linea['ubicacion_completa_agrupacion'] ?? '',
                        'lineas' => []
                    ];
                }
                
                $lineas_agrupadas[$fecha_inicio]['ubicaciones'][$id_ubicacion]['lineas'][] = $linea;
            }
            
            // 7. Obtener observaciones de familias y artículos (versión aprobada)
            $observaciones_array = $impresion->get_observaciones_presupuesto($id_presupuesto, 'es', $numero_version_aprobada);
            $observaciones = '';
            if (!empty($observaciones_array) && is_array($observaciones_array)) {
                $textos = [];
                foreach ($observaciones_array as $obs) {
                    // El campo correcto es 'observacion_es', no 'texto_observacion'
                    if (!empty($obs['observacion_es'])) {
                        // Añadir prefijo según tipo (familia o artículo)
                        $prefijo = '';
                        if ($obs['tipo_observacion'] === 'familia' && !empty($obs['nombre_familia'])) {
                            $prefijo = "** {$obs['nombre_familia']}:\n";
                        } elseif ($obs['tipo_observacion'] === 'articulo' && !empty($obs['nombre_articulo'])) {
                            $prefijo = "* {$obs['nombre_articulo']}:\n";
                        }
                        
                        $textos[] = $prefijo . $obs['observacion_es'];
                    }
                }
                $observaciones = implode("\n\n", $textos);
            } elseif (is_string($observaciones_array)) {
                $observaciones = $observaciones_array;
            }
            
            // =====================================================
            // ANÁLISIS DE FECHAS DE MONTAJE Y DESMONTAJE
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
                            'fecha_montaje' => $mtje,
                            'fecha_desmontaje' => $dsmtje,
                            'indices' => []
                        ];
                    }
                    
                    $combinaciones[$clave]['count']++;
                    $combinaciones[$clave]['indices'][] = $linea['id_linea_ppto'];
                }
                
                // Encontrar la combinación más frecuente
                $max_count = 0;
                $combinacion_predominante = null;

                foreach ($combinaciones as $clave => $datos) {
                    if ($datos['count'] > $max_count) {
                        $max_count = $datos['count'];
                        $combinacion_predominante = $datos;
                    }
                }

                // Calcular porcentaje - si >= 30% ocultar columnas (igual que presupuesto)
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
                        'fecha_montaje' => $combinacion_predominante['fecha_montaje'],
                        'fecha_desmontaje' => $combinacion_predominante['fecha_desmontaje'],
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
            // CREAR PDF CON TCPDF
            // =====================================================
            
            $pdf = new MYPDF_ALBARAN('P', 'mm', 'A4', true, 'UTF-8', false);
            
            $pdf->SetCreator('MDR ERP');
            $pdf->SetAuthor($datos_empresa['nombre_empresa'] ?? 'MDR');
            $pdf->SetTitle('Albarán de Carga ' . ($datos_presupuesto['numero_presupuesto'] ?? ''));
            $pdf->SetSubject('Albarán de Carga para ' . ($datos_presupuesto['nombre_evento_presupuesto'] ?? ''));
            
            // Establecer márgenes (margen superior reducido a 75mm sin observaciones cabecera)
            $pdf->SetMargins(8, 75, 8);
            $pdf->SetHeaderMargin(5);
            $pdf->SetFooterMargin(10);
            $pdf->SetAutoPageBreak(TRUE, 20);
            
            // Pasar datos a la cabecera
            $pdf->setDatosHeader(
                $datos_empresa,
                $datos_presupuesto,
                $mostrar_logo,
                $path_logo,
                $fecha_inicio_evento,
                $observaciones
            );
            
            $pdf->AddPage();
            
            // =====================================================
            // TABLA DE LÍNEAS AGRUPADAS POR FECHA Y UBICACIÓN
            // SIN COLUMNAS DE PRECIOS
            // =====================================================
            
            $pdf->SetFont('helvetica', 'B', 8);
            
            foreach ($lineas_agrupadas as $fecha_inicio => $grupo_fecha) {
                // CABECERA DE FECHA DE INICIO
                $fecha_formateada = date('d/m/Y', strtotime($fecha_inicio));
                $texto_cabecera = 'Fecha de inicio: ' . $fecha_formateada;
                
                $info_fechas = $analisis_fechas[$fecha_inicio] ?? ['ocultar_columnas' => false];
                
                if ($info_fechas['ocultar_columnas']) {
                    $mtje_formateada = !empty($info_fechas['fecha_montaje']) ? date('d/m/Y', strtotime($info_fechas['fecha_montaje'])) : '-';
                    $dsmtje_formateada = !empty($info_fechas['fecha_desmontaje']) ? date('d/m/Y', strtotime($info_fechas['fecha_desmontaje'])) : '-';
                    $texto_cabecera .= ' | Montaje: ' . $mtje_formateada . ' | Desmontaje: ' . $dsmtje_formateada;
                }
                
                $pdf->SetFillColor(46, 204, 113); // Verde
                $pdf->SetTextColor(255, 255, 255);
                $pdf->SetFont('helvetica', 'B', 9);
                $pdf->Cell(194, 6, $texto_cabecera, 0, 1, 'L', true);
                
                $pdf->SetFillColor(255, 255, 255);
                $pdf->SetTextColor(0, 0, 0);
                
                foreach ($grupo_fecha['ubicaciones'] as $id_ubicacion => $grupo_ubicacion) {
                    // Mostrar nombre de ubicación
                    $pdf->SetFont('helvetica', 'B', 8);
                    $ubicacion_text = $grupo_ubicacion['nombre_ubicacion'];
                    $pdf->Cell(194, 5, $ubicacion_text, 0, 1, 'L');
                    
                    $pdf->Ln(1);
                    
                    // CABECERA DE TABLA SIN COLUMNAS DE PRECIO/DESCUENTO/IMPORTE
                    $pdf->SetFont('helvetica', 'B', 8);
                    $pdf->SetFillColor(240, 240, 240);
                    $pdf->SetDrawColor(200, 200, 200);
                    
                    $ocultar_cols = $info_fechas['ocultar_columnas'];
                    
                    $pdf->Cell(17, 6, 'Inicio', 1, 0, 'C', 1);
                    $pdf->Cell(17, 6, 'Fin', 1, 0, 'C', 1);
                    
                    if (!$ocultar_cols) {
                        $pdf->Cell(15, 6, 'Mtje', 1, 0, 'C', 1);
                        $pdf->Cell(15, 6, 'DesMtje', 1, 0, 'C', 1);
                    }
                    
                    $pdf->Cell(8, 6, 'Días', 1, 0, 'C', 1);
                    
                    // Descripción ampliada (eliminamos Coef, P.Unit, %Dto, Importe)
                    // Ganamos: 10+15+12+24 = 61mm
                    // Ancho original descripción: 49mm (si no ocultar) o 79mm (si ocultar)
                    // Nuevo ancho: 49+61=110mm (si no ocultar) o 79+61=140mm (si ocultar)
                    $ancho_descripcion = $ocultar_cols ? 140 : 110;
                    $pdf->Cell($ancho_descripcion, 6, 'Descripción', 1, 0, 'C', 1);
                    
                    $pdf->Cell(12, 6, 'Cant.', 1, 1, 'C', 1);
                    $pdf->SetDrawColor(0, 0, 0);
                    
                    // DATOS DE LÍNEAS SIN PRECIOS
                    $pdf->SetFont('helvetica', '', 7);
                    
                    foreach ($grupo_ubicacion['lineas'] as $linea) {
                        $pdf->SetFont('helvetica', '', 7);
                        
                        $fecha_inicio_linea = !empty($linea['fecha_inicio_linea_ppto']) ? date('d/m/Y', strtotime($linea['fecha_inicio_linea_ppto'])) : '-';
                        $fecha_fin = !empty($linea['fecha_fin_linea_ppto']) ? date('d/m/Y', strtotime($linea['fecha_fin_linea_ppto'])) : '-';
                        $fecha_montaje = !empty($linea['fecha_montaje_linea_ppto']) ? date('d/m/Y', strtotime($linea['fecha_montaje_linea_ppto'])) : '-';
                        $fecha_desmontaje = !empty($linea['fecha_desmontaje_linea_ppto']) ? date('d/m/Y', strtotime($linea['fecha_desmontaje_linea_ppto'])) : '-';
                        
                        $es_excepcion = in_array($linea['id_linea_ppto'], $info_fechas['excepciones'] ?? []);
                        $es_kit = (isset($linea['es_kit_articulo']) && $linea['es_kit_articulo'] == 1);
                        
                        $descripcion = $linea['descripcion_linea_ppto'] ?? '';
                        $cantidad = floatval($linea['cantidad_linea_ppto']);
                        
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
                        
                        // Descripción
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
                        
                        $pdf->SetDrawColor(0, 0, 0);
                        $pdf->SetXY($x_inicial, $y_inicial + $altura_fila);
                        
                        // OBSERVACIONES DE LÍNEA
                        $observaciones_a_mostrar = '';
                        
                        if (!empty($linea['observaciones_linea_ppto']) && trim($linea['observaciones_linea_ppto']) != '') {
                            $observaciones_a_mostrar = trim($linea['observaciones_linea_ppto']);
                        }
                        
                        if ($es_excepcion && $ocultar_cols) {
                            $obs_fechas = 'Montaje: ' . $fecha_montaje . ' - Desmontaje: ' . $fecha_desmontaje;
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
                                $ancho_obs = 170 - ($x_desc - $x_inicial);
                                $pdf->MultiCell($ancho_obs, 4, $observaciones_a_mostrar, 0, 'L', false, 1, $x_desc, $y_antes_obs);
                            } else {
                                $texto_observaciones = '    ' . $observaciones_a_mostrar;
                                $pdf->MultiCell(170, 4, $texto_observaciones, 0, 'L', false, 1, '', $y_antes_obs);
                            }
                            $pdf->SetTextColor(0, 0, 0);
                            $pdf->SetFont('helvetica', '', 7);
                        }
                        
                        // Componentes del KIT (CONDICIONADO POR SWITCH)
                        if ($es_kit && !empty($linea['id_articulo']) && 
                            isset($linea['ocultar_detalle_kit_linea_ppto']) && 
                            $linea['ocultar_detalle_kit_linea_ppto'] == 0 &&
                            isset($datos_empresa['mostrar_kits_albaran_empresa']) && 
                            $datos_empresa['mostrar_kits_albaran_empresa'] == 1) {
                            
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
                                    
                                    $nombre_componente = '   - ' . ($comp['nombre_articulo_componente'] ?? 'Componente');
                                    $cantidad_comp = floatval($comp['cantidad_articulo_kit'] ?? 1);
                                    
                                    $pdf->Cell($ancho_descripcion, 4, $nombre_componente, 0, 0, 'L');
                                    $pdf->Cell(12, 4, number_format($cantidad_comp, 0), 0, 1, 'C');
                                    
                                    $pdf->SetFont('helvetica', '', 7);
                                    $pdf->SetTextColor(0, 0, 0);
                                }
                            }
                        }
                    }
                    
                    // NO MOSTRAR SUBTOTALES POR UBICACIÓN (eliminado)
                    $pdf->Ln(2);
                }
                
                // NO MOSTRAR SUBTOTALES POR FECHA (eliminado)
                $pdf->Ln(3);
            }
            
            // =====================================================
            // NO MOSTRAR TOTALES FINALES (eliminado completamente)
            // NO MOSTRAR JUSTIFICACIÓN EXENCIÓN IVA (eliminado)
            // NO MOSTRAR FORMA DE PAGO (eliminado)
            // =====================================================

            // =====================================================
            // SECCIÓN: PESO TOTAL ESTIMADO
            // SE MANTIENE en albarán de carga (útil para logística)
            // =====================================================
            $datos_peso = null;
            if (!empty($datos_presupuesto['id_version_presupuesto'])) {
                $datos_peso = $impresion->get_peso_total_presupuesto($datos_presupuesto['id_version_presupuesto']);
            }

            if ($datos_peso &&
                isset($datos_peso['peso_total_kg']) &&
                floatval($datos_peso['peso_total_kg']) > 0) {

                $pdf->Ln(6);

                $pdf->SetFont('helvetica', 'B', 9);
                $pdf->SetFillColor(233, 236, 239);
                $pdf->SetDrawColor(173, 181, 189);
                $pdf->SetTextColor(52, 58, 64);
                $pdf->Cell(0, 6, 'PESO TOTAL ESTIMADO', 1, 1, 'C', 1);

                $pdf->SetFont('helvetica', 'B', 10);
                $pdf->SetFillColor(248, 249, 250);
                $pdf->SetTextColor(28, 126, 214);

                $peso_formateado = number_format($datos_peso['peso_total_kg'], 2, ',', '.');
                $texto_peso = $peso_formateado . ' KG';

                $porcentaje_completitud = floatval($datos_peso['porcentaje_completitud'] ?? 0);
                if ($porcentaje_completitud < 100 && $porcentaje_completitud > 0) {
                    $texto_peso .= ' *';
                    $mostrar_nota_completitud = true;
                } else {
                    $mostrar_nota_completitud = false;
                }

                $pdf->Cell(0, 7, $texto_peso, 1, 1, 'C', 1);

                if ($mostrar_nota_completitud) {
                    $pdf->SetFont('helvetica', 'I', 7);
                    $pdf->SetTextColor(108, 117, 125);
                    $nota = '* Peso estimado basado en ' . $datos_peso['lineas_con_peso'] . ' de ' . $datos_peso['total_lineas'] . ' lineas ';
                    $nota .= '(' . number_format($porcentaje_completitud, 0) . '% de datos disponibles)';
                    $pdf->Cell(0, 4, $nota, 0, 1, 'C');
                }

                // Restaurar colores
                $pdf->SetTextColor(0, 0, 0);
                $pdf->SetDrawColor(0, 0, 0);
            }
            
            // =====================================================
            // SECCIÓN: OBSERVACIONES DE FAMILIAS Y ARTÍCULOS
            // SE MANTIENE (información técnica útil)
            // CONDICIONADO POR SWITCH DE EMPRESA
            // =====================================================
            
            if (!empty($observaciones) && 
                isset($datos_empresa['mostrar_obs_familias_articulos_albaran_empresa']) && 
                $datos_empresa['mostrar_obs_familias_articulos_albaran_empresa'] == 1) {
                $pdf->Ln(5);
                
                // Título de la sección
                $pdf->SetFont('helvetica', 'B', 9);
                $pdf->SetTextColor(44, 62, 80);
                $pdf->Cell(0, 5, 'OBSERVACIONES TÉCNICAS', 0, 1, 'L');
                
                // Línea decorativa
                $pdf->SetDrawColor(243, 156, 18);
                $pdf->SetLineWidth(0.5);
                $pdf->Line(8, $pdf->GetY(), 202, $pdf->GetY());
                $pdf->SetLineWidth(0.2);
                $pdf->Ln(2);
                
                // Contenido de observaciones con fondo gris claro
                $pdf->SetFont('helvetica', '', 7.5);
                $pdf->SetTextColor(52, 73, 94);
                $pdf->SetFillColor(248, 249, 250);
                
                $observaciones_formateadas = str_replace(['**', '*'], ['• ', '  - '], $observaciones);
                $pdf->MultiCell(0, 4, $observaciones_formateadas, 0, 'L', true);
                
                // Restaurar colores
                $pdf->SetDrawColor(0, 0, 0);
                $pdf->SetTextColor(0, 0, 0);
            }
            
            // =====================================================
            // SECCIÓN: OBSERVACIONES DE PIE DEL PRESUPUESTO
            // SE MANTIENE (información relevante para técnicos)
            // CONDICIONADO POR SWITCH DE EMPRESA
            // =====================================================
            
            if (!empty($datos_presupuesto['observaciones_pie_presupuesto']) &&
                isset($datos_empresa['mostrar_obs_pie_albaran_empresa']) && 
                $datos_empresa['mostrar_obs_pie_albaran_empresa'] == 1) {
                $pdf->Ln(8);
                
                // Calcular altura del texto para el fondo
                $pdf->SetFont('helvetica', '', 8);
                $texto_altura = $pdf->getStringHeight(194, $datos_presupuesto['observaciones_pie_presupuesto']);
                
                // Guardar posición Y inicial
                $y_inicio = $pdf->GetY();
                
                // Fondo gris claro
                $pdf->SetFillColor(245, 245, 245);
                $pdf->Rect(8, $y_inicio, 194, $texto_altura + 6, 'F');
                
                // Texto centrado
                $pdf->SetFont('helvetica', '', 8);
                $pdf->SetTextColor(99, 110, 114);
                $pdf->SetXY(8, $y_inicio + 3);
                $pdf->MultiCell(194, 4, $datos_presupuesto['observaciones_pie_presupuesto'], 0, 'C');
                
                // Línea inferior decorativa
                $pdf->SetDrawColor(223, 230, 233);
                $pdf->SetLineWidth(0.3);
                $pdf->Line(8, $pdf->GetY() + 3, 202, $pdf->GetY() + 3);
                
                // Restaurar colores
                $pdf->SetTextColor(0, 0, 0);
                $pdf->SetDrawColor(0, 0, 0);
                $pdf->SetLineWidth(0.2);
            }
            
            // NO MOSTRAR CASILLAS DE FIRMA (eliminado)
            
            // =====================================================
            // SALIDA DEL PDF
            // =====================================================
            
            ob_end_clean();
            
            $nombre_archivo = 'Albaran_Carga_' . ($datos_presupuesto['numero_presupuesto'] ?? 'SN') . '.pdf';
            
            $pdf->Output($nombre_archivo, 'I'); // 'I' = inline en navegador
            
            $registro->registrarActividad(
                'admin',
                'impresionpartetrabajo_m2_pdf_es.php',
                'albaran_carga',
                "Albarán de carga generado para presupuesto ID: $id_presupuesto",
                'info'
            );
            
            exit;
            
        } catch (Exception $e) {
            ob_end_clean();
            
            $registro->registrarActividad(
                'admin',
                'impresionpartetrabajo_m2_pdf_es.php',
                'albaran_carga',
                "Error: " . $e->getMessage(),
                'error'
            );
            
            header('Content-Type: text/html; charset=utf-8');
            echo '<!DOCTYPE html>';
            echo '<html><head><meta charset="UTF-8"><title>Error</title></head><body>';
            echo '<h2>Error al generar el Albarán de Carga</h2>';
            echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
            echo '<p><a href="javascript:history.back()">Volver</a></p>';
            echo '</body></html>';
            exit;
        }
        break;
        
    default:
        ob_end_clean();
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Operación no válida'
        ]);
        exit;
}
?>
