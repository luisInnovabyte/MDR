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
    
    // Cabecera repetida en cada página
    public function Header() {
        $y_start = 10;

        // ============================================
        // TÍTULO "PRESUPUESTO" (JUSTIFICADO A LA DERECHA)
        // ============================================

        $this->SetY($y_start);
        $this->SetFont('helvetica', 'B', 16);
        $this->SetTextColor(102, 126, 234); // Color azul elegante (mismo que caja de info)
        $this->Cell(0, 8, 'PRESUPUESTO', 0, 1, 'R'); // Alineado a la derecha

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

        // Dirección fiscal - LÍNEA 1
        $this->SetX(8);
        $this->SetFont('helvetica', '', 7.5);
        $this->SetTextColor(52, 73, 94); // Color normal
        $direccion_fiscal = $this->datos_empresa['direccion_fiscal_empresa'] ?? '';
        $this->Cell(95, 3, $direccion_fiscal, 0, 1, 'L');
        
        // Dirección fiscal - LÍNEA 2 (CP, Población, Provincia)
        $this->SetX(8);
        $cp_poblacion_provincia = ($this->datos_empresa['cp_fiscal_empresa'] ?? '') . ' ' .
                                  ($this->datos_empresa['poblacion_fiscal_empresa'] ?? '') . ' (' .
                                  ($this->datos_empresa['provincia_fiscal_empresa'] ?? '') . ')';
        $this->Cell(95, 3, $cp_poblacion_provincia, 0, 1, 'L');

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

        // Caja con info del presupuesto (más cerca, sin espacio extra)
        $y_info = $this->GetY() + 1;
        $this->SetY($y_info);

        // Fondo de color para la caja de info
        $this->SetFillColor(102, 126, 234); // Color azul/morado
        $this->SetTextColor(255, 255, 255); // Texto blanco
        $this->SetFont('helvetica', 'B', 8.5);

        $this->SetXY(8, $y_info);
        $this->Cell(95, 10, '', 0, 0, 'L', true); // Fondo de la caja ajustado al nuevo ancho

        // Contenido de la caja en dos líneas
        $this->SetXY(9, $y_info + 1);
        $info_text_linea1 = 'N°: ' . ($this->datos_presupuesto['numero_presupuesto'] ?? '') .
                     '  |  F: ' . $this->fecha_presupuesto .
                     '  |  Val: ' . ($this->fecha_validez ?: 'N/A') .
                     '  |  Ver: ' . ($this->datos_presupuesto['numero_version_presupuesto'] ?? '1');
        
        $this->Cell(93, 3, $info_text_linea1, 0, 1, 'L');
        
        // Segunda línea con referencia del cliente (si existe)
        if (!empty($this->datos_presupuesto['numero_pedido_cliente_presupuesto'])) {
            $this->SetXY(9, $y_info + 5);
            $info_text_linea2 = 'Ref. Cliente: ' . $this->datos_presupuesto['numero_pedido_cliente_presupuesto'];
            $this->Cell(93, 3, $info_text_linea2, 0, 1, 'L');
        }

        // Restaurar color de texto
        $this->SetTextColor(0, 0, 0);

        // ============================================
        // OBSERVACIONES DE CABECERA (COLUMNA IZQUIERDA)
        // ============================================

        if (!empty($this->observaciones)) {
            $y_obs = $this->GetY() + 2; // Agregar 2mm de margen
            $this->SetY($y_obs);
            $this->SetX(8);

            // Sin título, solo el texto de las observaciones
            $this->SetFont('helvetica', '', 6.5);
            $this->SetTextColor(99, 110, 114);
            $this->MultiCell(95, 3, $this->observaciones, 0, 'L');
        }

        // Guardar Y final de columna izquierda
        $y_final_izquierda = $this->GetY();
        
        // ============================================
        // COLUMNA DERECHA: Box Verde con Datos Cliente
        // ============================================

        $col2_x = 108; // Ajustado para margen de 8mm
        $col2_width = 94; // Aumentado por el espacio ganado (210-8-8=194, 194-108=86, pero dejamos 94 para más espacio)
        $box_y_start = $y_start;
        
        // Calcular altura necesaria para el box del cliente
        $client_box_height = 26; // Altura base para cliente (más compacta)
        
        // Si hay contacto, aumentar altura
        if (!empty($this->datos_presupuesto['nombre_contacto_cliente'])) {
            $client_box_height += 10; // Espacio adicional para "A la atención de"
        }
        
        // Fondo verde claro
        $this->SetFillColor(248, 249, 250); // Gris muy claro
        $this->Rect($col2_x, $box_y_start, $col2_width, $client_box_height, 'F');
        
        // Borde verde
        $this->SetDrawColor(39, 174, 96); // Verde
        $this->SetLineWidth(0.5);
        $this->Rect($col2_x, $box_y_start, $col2_width, $client_box_height);
        $this->SetLineWidth(0.2); // Restaurar
        
        // Título "CLIENTE" (sin emoji para evitar caracteres extraños)
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
            // Añadir CP y población si existen
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
        
        // ============================================
        // A LA ATENCIÓN DE (Contacto del cliente)
        // ============================================
        if (!empty($this->datos_presupuesto['nombre_contacto_cliente'])) {
            $this->Ln(1);
            
            // Título "A la atención de:"
            $this->SetXY($col2_x + 2, $this->GetY());
            $this->SetFont('helvetica', 'B', 8);
            $this->SetTextColor(39, 174, 96); // Color verde
            $this->Cell($col2_width - 4, 3, 'A la atencion de:', 0, 1, 'L');

            $this->SetX($col2_x + 2);
            $this->SetFont('helvetica', '', 7.5);
            $this->SetTextColor(52, 73, 94);
            
            // Nombre contacto
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
            
            // Teléfono contacto
            if (!empty($this->datos_presupuesto['telefono_contacto_cliente'])) {
                $this->Cell(15, 2.5, 'Telefono:', 0, 0, 'L');
                $this->Cell($col2_width - 19, 2.5, $this->datos_presupuesto['telefono_contacto_cliente'], 0, 1, 'L');
                $this->SetX($col2_x + 2);
            }
            
            // Email contacto
            if (!empty($this->datos_presupuesto['email_contacto_cliente'])) {
                $this->Cell(15, 2.5, 'Email:', 0, 0, 'L');
                $this->Cell($col2_width - 19, 2.5, $this->datos_presupuesto['email_contacto_cliente'], 0, 1, 'L');
            }
        }
        
        // ============================================
        // DATOS DEL EVENTO Y UBICACIÓN (COLUMNA DERECHA, DEBAJO DEL CLIENTE)
        // ============================================

        $y_evento = $box_y_start + $client_box_height + 3; // Debajo del box del cliente

        // Ancho de la columna derecha - ajustado para márgenes de 8mm
        $evento_x = 108;
        $evento_width = 94;

        // Inicializar variable para tracking de posición en columna derecha
        $y_despues_fechas_evento = $y_evento;

        // ========== FECHAS DEL EVENTO (SIEMPRE SE MUESTRAN) ==========

        $this->SetXY($evento_x, $y_evento);

        // Título de la sección de evento
        $this->SetFont('helvetica', 'B', 8);
        $this->SetTextColor(39, 174, 96); // Color verde
        $this->Cell($evento_width, 4, 'DATOS DEL EVENTO', 0, 1, 'L');

        // Fechas del evento en formato tabla compacta (3 columnas)
        $this->SetX($evento_x);
        $this->SetFont('helvetica', '', 8);
        $this->SetTextColor(127, 140, 141); // Gris para labels

        // Crear mini tabla de 3 columnas: Inicio | Fin | Duración
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

        $this->Ln(4); // Aumentado a 4mm para dar más espacio cuando no hay ubicación

        // Guardar posición Y después de las fechas del evento
        $y_despues_fechas_evento = $this->GetY();

        // ========== NOMBRE Y UBICACIÓN DEL EVENTO (CONDICIONAL) ==========

        // Solo mostrar nombre del evento si existe
        if (!empty($this->datos_presupuesto['nombre_evento_presupuesto'])) {
            $this->SetX($evento_x);
            $this->SetFont('helvetica', 'B', 7.5);
            $this->SetFillColor(255, 243, 205); // Fondo amarillo claro
            $this->SetTextColor(133, 100, 4); // Texto oscuro
            $this->MultiCell($evento_width, 4, $this->datos_presupuesto['nombre_evento_presupuesto'], 0, 'L', true);

            $this->Ln(1);
            $y_despues_fechas_evento = $this->GetY();
        }

        // ========== UBICACIÓN DEL EVENTO ==========
        // Construir ubicación completa PRIMERO
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

        // Solo mostrar si hay ubicación completa
        if (!empty($ubicacion_completa)) {
            $this->SetX($evento_x);
            $this->SetFont('helvetica', 'B', 8);
            $this->SetTextColor(243, 156, 18); // Color naranja
            $this->Cell($evento_width, 4, 'UBICACION DEL EVENTO', 0, 1, 'L');

            $this->SetX($evento_x);
            $this->SetFont('helvetica', '', 7);
            $this->SetTextColor(52, 73, 94);
            $this->Cell($evento_width, 3.5, $ubicacion_completa, 0, 1, 'L');

            // Actualizar posición final si se dibujó la ubicación
            $y_despues_fechas_evento = $this->GetY();
        }

        // Restaurar colores
        $this->SetDrawColor(0, 0, 0);
        $this->SetTextColor(0, 0, 0);

        // ============================================
        // OBSERVACIONES DE CABECERA (TODO EL ANCHO)
        // ============================================

        // Calcular Y máxima entre columna izquierda y derecha
        // Usar la posición real que fue actualizada según el contenido
        $y_observaciones_cabecera = max($y_final_izquierda, $y_despues_fechas_evento) + 6; // +6mm de margen para evitar solapado

        // Mostrar observaciones de cabecera si existen
        if (!empty($this->observaciones_cabecera)) {
            $this->SetXY(8, $y_observaciones_cabecera);

            // Estilo para las observaciones
            $this->SetFont('helvetica', '', 8);
            $this->SetTextColor(44, 62, 80); // Color oscuro

            // Usar todo el ancho disponible (ambas columnas)
            $ancho_total = 194; // 210mm (ancho A4) - 8mm (margen izq) - 8mm (margen der)
            
            $this->MultiCell($ancho_total, 4, $this->observaciones_cabecera, 0, 'L');
        }
        
        // Posicionar cursor para contenido (después de todo)
        $final_y = $this->GetY();
        $this->SetY($final_y);
    }
    
    // Pie de página repetido
    public function Footer() {
        // Pie de empresa (si existe)
        if (!empty($this->texto_pie_empresa)) {
            $this->SetY(-20); // Posición cerca del margen inferior
            
            // Línea superior decorativa
            $this->SetDrawColor(44, 62, 80);
            $this->SetLineWidth(0.5);
            $this->Line(8, $this->GetY(), 202, $this->GetY());
            $this->SetY($this->GetY() + 1);
            
            // Texto del pie de empresa
            $this->SetFont('helvetica', '', 7);
            $this->SetTextColor(99, 110, 114);
            $this->MultiCell(0, 3, $this->texto_pie_empresa, 0, 'C');
        }
        
        // Números de página (siempre al final)
        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 8);
        $this->SetTextColor(0, 0, 0);
        $this->Cell(0, 10, 'Página ' . $this->getAliasNumPage() . ' de ' . $this->getAliasNbPages(), 0, 0, 'C');
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
    
    case "cli_esp":
        try {
            // 1. Validar que se recibió el ID del presupuesto
            if (!isset($_POST['id_presupuesto']) || empty($_POST['id_presupuesto'])) {
                throw new Exception("No se recibió el ID del presupuesto");
            }
            
            $id_presupuesto = intval($_POST['id_presupuesto']);
            $numero_version = !empty($_POST['numero_version']) ? intval($_POST['numero_version']) : null;
            
            // 2. Obtener datos del presupuesto (versión actual o la indicada)
            $datos_presupuesto = $impresion->get_datos_cabecera($id_presupuesto, $numero_version);
            
            if (!$datos_presupuesto) {
                throw new Exception("No se encontraron datos del presupuesto ID: $id_presupuesto");
            }
            
            // Porcentaje de descuento del hotel (desde campo en cabecera del presupuesto)
            $pct_hotel = floatval($datos_presupuesto['descuento_presupuesto'] ?? 0);
            
            // 3. Obtener datos de la empresa
            $datos_empresa = $impresion->get_empresa_datos();
            
            if (!$datos_empresa) {
                throw new Exception("No se encontraron datos de la empresa");
            }

            // Configuración de subtotales por fecha
            // Si no existe el campo en BD, por defecto TRUE (backward compatibility)
            $mostrar_subtotales_fecha = !isset($datos_empresa['mostrar_subtotales_fecha_presupuesto_empresa'])
                ? true  // Default si no existe el campo
                : ($datos_empresa['mostrar_subtotales_fecha_presupuesto_empresa'] == 1);
            
            // 4. Validar logo de empresa
            $mostrar_logo = false;
            $path_logo = '';
            
            if (!empty($datos_empresa['logotipo_empresa'])) {
                if ($impresion->validar_logo($datos_empresa['logotipo_empresa'])) {
                    $mostrar_logo = true;
                    
                    // Limpiar el nombre del logo
                    $logo_name = $datos_empresa['logotipo_empresa'];
                    // Eliminar barra inicial si existe
                    $logo_name = ltrim($logo_name, '/');
                    // Eliminar prefijo 'public/' si existe
                    if (strpos($logo_name, 'public/') === 0) {
                        $logo_name = substr($logo_name, 7); // Quitar 'public/'
                    }
                    
                    // Construir ruta absoluta
                    $path_logo = __DIR__ . '/../public/' . $logo_name;
                    
                    // Verificar que el archivo existe
                    if (!file_exists($path_logo)) {
                        $mostrar_logo = false;
                        $path_logo = null;
                    } else {
                        $path_logo = realpath($path_logo);
                    }
                }
            }
            
            // 5. Formatear fechas
            $fecha_presupuesto = !empty($datos_presupuesto['fecha_presupuesto']) 
                ? date('d/m/Y', strtotime($datos_presupuesto['fecha_presupuesto'])) 
                : '';
            
            $fecha_validez = !empty($datos_presupuesto['fecha_validez_presupuesto']) 
                ? date('d/m/Y', strtotime($datos_presupuesto['fecha_validez_presupuesto'])) 
                : '';
            
            // Formatear fechas del evento
            $fecha_inicio_evento = !empty($datos_presupuesto['fecha_inicio_evento_presupuesto']) 
                ? date('d/m/Y', strtotime($datos_presupuesto['fecha_inicio_evento_presupuesto'])) 
                : '';
            
            $fecha_fin_evento = !empty($datos_presupuesto['fecha_fin_evento_presupuesto']) 
                ? date('d/m/Y', strtotime($datos_presupuesto['fecha_fin_evento_presupuesto'])) 
                : '';
            
            // 6. Obtener líneas del presupuesto
            $lineas = $impresion->get_lineas_impresion_hotel($id_presupuesto, $numero_version);
            
            // 6.1 Agrupar líneas por fecha de inicio y ubicación
            $lineas_agrupadas = [];
            foreach ($lineas as $linea) {
                $fecha_inicio = $linea['fecha_inicio_linea_ppto'];
                $id_ubicacion = $linea['id_ubicacion'] ?? 0;
                
                // Inicializar grupo de fecha si no existe
                if (!isset($lineas_agrupadas[$fecha_inicio])) {
                    $lineas_agrupadas[$fecha_inicio] = [
                        'ubicaciones' => [],
                        'subtotal_fecha' => 0,
                        'total_iva_fecha' => 0,
                        'total_fecha' => 0,
                        'subtotal_hotel_fecha' => 0
                    ];
                }
                
                // Inicializar grupo de ubicación si no existe
                if (!isset($lineas_agrupadas[$fecha_inicio]['ubicaciones'][$id_ubicacion])) {
                    $lineas_agrupadas[$fecha_inicio]['ubicaciones'][$id_ubicacion] = [
                        'nombre_ubicacion' => $linea['nombre_ubicacion'] ?? 'Sin ubicación',
                        'ubicacion_completa' => $linea['ubicacion_completa_agrupacion'] ?? '',
                        'lineas' => [],
                        'subtotal_ubicacion' => 0,
                        'total_iva_ubicacion' => 0,
                        'total_ubicacion' => 0,
                        'subtotal_hotel_ubicacion' => 0
                    ];
                }
                
                // Agregar línea al grupo
                $lineas_agrupadas[$fecha_inicio]['ubicaciones'][$id_ubicacion]['lineas'][] = $linea;
                
                // Acumular subtotales
                $base_imponible = floatval($linea['base_imponible']);
                $total_linea = floatval($linea['total_linea']);
                $total_iva_linea = $total_linea - $base_imponible;
                
                $lineas_agrupadas[$fecha_inicio]['ubicaciones'][$id_ubicacion]['subtotal_ubicacion'] += $base_imponible;
                $lineas_agrupadas[$fecha_inicio]['ubicaciones'][$id_ubicacion]['total_iva_ubicacion'] += $total_iva_linea;
                $lineas_agrupadas[$fecha_inicio]['ubicaciones'][$id_ubicacion]['total_ubicacion'] += $total_linea;
                
                $lineas_agrupadas[$fecha_inicio]['subtotal_fecha'] += $base_imponible;
                $lineas_agrupadas[$fecha_inicio]['total_iva_fecha'] += $total_iva_linea;
                $lineas_agrupadas[$fecha_inicio]['total_fecha'] += $total_linea;
                
                // Acumulado hotel por grupo
                $aplica_dto_h = ($linea['permitir_descuentos_articulo'] != 0) && ($linea['permite_descuento_familia'] != 0);
                $importe_h = ($aplica_dto_h && $pct_hotel > 0) ? ($base_imponible * (1 - $pct_hotel / 100)) : $base_imponible;
                $lineas_agrupadas[$fecha_inicio]['ubicaciones'][$id_ubicacion]['subtotal_hotel_ubicacion'] += $importe_h;
                $lineas_agrupadas[$fecha_inicio]['subtotal_hotel_fecha'] += $importe_h;
            }
            
            // 7. Obtener observaciones (convertir a string si es necesario)
            $observaciones_array = $impresion->get_observaciones_presupuesto($id_presupuesto, 'es', $numero_version);
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
            
            // 8. Calcular desglose de IVA y descuentos
            $desglose_iva = [];
            $subtotal_sin_iva = 0;
            $subtotal_sin_descuento = 0;
            $total_descuentos = 0;
            
            foreach ($lineas as $linea) {
                // Calcular subtotal sin descuento y descuento por línea
                $cantidad = floatval($linea['cantidad_linea_ppto'] ?? 0);
                $precio_unitario = floatval($linea['precio_unitario_linea_ppto'] ?? 0);
                $dias = floatval($linea['dias_linea'] ?? 1);
                $coeficiente = floatval($linea['valor_coeficiente_linea_ppto'] ?? null);
                $aplica_coeficiente = ($coeficiente !== null && $coeficiente > 0);
                $descuento_pct = floatval($linea['descuento_linea_ppto'] ?? 0);
                
                // Calcular subtotal sin descuento según lógica de la vista:
                // Si aplica coeficiente: (cantidad × precio_unitario × coeficiente)
                // Si NO aplica coeficiente: (días × cantidad × precio_unitario)
                if ($aplica_coeficiente) {
                    $subtotal_linea_sin_desc = $cantidad * $precio_unitario * $coeficiente;
                } else {
                    $subtotal_linea_sin_desc = $dias * $cantidad * $precio_unitario;
                }
                
                $importe_descuento_linea = $subtotal_linea_sin_desc * ($descuento_pct / 100);
                
                $subtotal_sin_descuento += $subtotal_linea_sin_desc;
                $total_descuentos += $importe_descuento_linea;
                
                // Calcular IVA y base imponible
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
            // TOTALES HOTEL
            // =====================================================
            $desglose_iva_hotel = [];
            $total_base_hotel   = 0;
            
            foreach ($lineas as $linea) {
                $base_h    = floatval($linea['base_imponible'] ?? 0);
                $pct_iva_h = floatval($linea['porcentaje_iva_linea_ppto'] ?? 0);
                $aplica_h  = ($linea['permitir_descuentos_articulo'] != 0) && ($linea['permite_descuento_familia'] != 0);
                $importe_h = ($aplica_h && $pct_hotel > 0) ? ($base_h * (1 - $pct_hotel / 100)) : $base_h;
                
                $total_base_hotel += $importe_h;
                
                if (!isset($desglose_iva_hotel[$pct_iva_h])) {
                    $desglose_iva_hotel[$pct_iva_h] = ['base' => 0, 'cuota' => 0];
                }
                $desglose_iva_hotel[$pct_iva_h]['base']  += $importe_h;
                $desglose_iva_hotel[$pct_iva_h]['cuota'] += $importe_h * ($pct_iva_h / 100);
            }
            
            ksort($desglose_iva_hotel);
            $total_iva_hotel         = 0;
            foreach ($desglose_iva_hotel as $iva_h) { $total_iva_hotel += $iva_h['cuota']; }
            $total_descuento_hotel   = ($pct_hotel > 0) ? ($subtotal_sin_iva - $total_base_hotel) : 0;
            $total_presupuesto_hotel = $total_base_hotel + $total_iva_hotel;
            
            // =====================================================
            // CREAR PDF CON TCPDF
            // =====================================================
            
            // Crear instancia de PDF
            // Parámetros: orientación, unidad, formato, unicode, encoding, diskcache
            $pdf = new MYPDF('P', 'mm', 'A4', true, 'UTF-8', false);
            
            // Configurar PDF
            $pdf->SetCreator('MDR ERP');
            $pdf->SetAuthor($datos_empresa['nombre_empresa'] ?? 'MDR');
            $pdf->SetTitle('Presupuesto ' . ($datos_presupuesto['numero_presupuesto'] ?? ''));
            $pdf->SetSubject('Presupuesto para ' . ($datos_presupuesto['nombre_evento_presupuesto'] ?? ''));
            
            // Establecer márgenes
            $pdf->SetMargins(8, 95, 8); // Margen superior ajustado a 95mm por el título "PRESUPUESTO"
            $pdf->SetHeaderMargin(5);
            $pdf->SetFooterMargin(10);
            $pdf->SetAutoPageBreak(TRUE, 25); // Aumentado a 25mm para dar espacio al pie de empresa
            
            // Pasar datos a la cabecera
            $pdf->setDatosHeader(
                $datos_empresa,
                $datos_presupuesto,
                $mostrar_logo,
                $path_logo,
                $fecha_presupuesto,
                $fecha_validez,
                $fecha_inicio_evento,
                $observaciones,
                $datos_presupuesto['observaciones_cabecera_presupuesto'] ?? '',
                $datos_empresa['texto_pie_presupuesto_empresa'] ?? ''
            );
            
            // Añadir página
            $pdf->AddPage();
            
            // =====================================================
            // ANÁLISIS DE FECHAS DE MONTAJE Y DESMONTAJE
            // =====================================================
            // Determinar por cada grupo de fecha_inicio si >= 80% de líneas
            // tienen las mismas fechas de montaje y desmontaje
            
            $analisis_fechas = [];
            
            foreach ($lineas_agrupadas as $fecha_inicio => $grupo_fecha) {
                // Contar todas las líneas del grupo (todas las ubicaciones)
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
                
                // Contar ocurrencias de cada combinación de fechas
                $combinaciones = [];
                foreach ($todas_lineas as $idx => $linea) {
                    // Normalizar fechas a solo componente de fecha (sin hora) para comparación
                    // Asegurar que siempre tengamos un string válido para comparar
                    $fecha_mtje_raw = $linea['fecha_montaje_linea_ppto'] ?? null;
                    $fecha_dsmtje_raw = $linea['fecha_desmontaje_linea_ppto'] ?? null;
                    
                    // Normalizar solo si la fecha no está vacía
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
                
                // Encontrar la combinación más frecuente
                $max_count = 0;
                $combinacion_predominante = null;
                
                foreach ($combinaciones as $clave => $datos) {
                    if ($datos['count'] > $max_count) {
                        $max_count = $datos['count'];
                        $combinacion_predominante = $datos;
                    }
                }
                
                // Calcular porcentaje
                $porcentaje = ($max_count / $total_lineas) * 100;
                
                // Si >= 30%, ocultar columnas y marcar excepciones
                if ($porcentaje >= 30) {
                    // Identificar líneas excepcionales (las que NO tienen las fechas predominantes)
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
                    // No se cumple el 30%, mantener columnas
                    $analisis_fechas[$fecha_inicio] = [
                        'ocultar_columnas' => false,
                        'fecha_montaje' => null,
                        'fecha_desmontaje' => null,
                        'excepciones' => []
                    ];
                }
            }
            
            // =====================================================
            // TABLA DE LÍNEAS AGRUPADAS POR FECHA Y UBICACIÓN
            // =====================================================
            
            $pdf->SetFont('helvetica', 'B', 8);
            
            // Iterar por cada fecha de inicio
            foreach ($lineas_agrupadas as $fecha_inicio => $grupo_fecha) {
                // =====================================================
                // CABECERA DE FECHA DE INICIO
                // =====================================================
                
                $fecha_formateada = date('d/m/Y', strtotime($fecha_inicio));
                
                // Construir texto de cabecera con fechas de montaje/desmontaje si aplica
                $texto_cabecera = 'Fecha de inicio: ' . $fecha_formateada;
                
                $info_fechas = $analisis_fechas[$fecha_inicio] ?? ['ocultar_columnas' => false];
                
                if ($info_fechas['ocultar_columnas']) {
                    $mtje_formateada = !empty($info_fechas['fecha_montaje']) ? date('d/m/Y', strtotime($info_fechas['fecha_montaje'])) : '-';
                    $dsmtje_formateada = !empty($info_fechas['fecha_desmontaje']) ? date('d/m/Y', strtotime($info_fechas['fecha_desmontaje'])) : '-';
                    $texto_cabecera .= ' | Montaje: ' . $mtje_formateada . ' | Desmontaje: ' . $dsmtje_formateada;
                }
                
                // Fecha de inicio con fondo azul
                $pdf->SetFillColor(52, 152, 219); // Azul
                $pdf->SetTextColor(255, 255, 255); // Texto blanco
                $pdf->SetFont('helvetica', 'B', 9);
                $pdf->Cell(194, 6, $texto_cabecera, 0, 1, 'L', true);
                
                // Restaurar colores
                $pdf->SetFillColor(255, 255, 255);
                $pdf->SetTextColor(0, 0, 0);
                
                // Iterar por cada ubicación dentro de la fecha
                foreach ($grupo_fecha['ubicaciones'] as $id_ubicacion => $grupo_ubicacion) {
                    // Mostrar nombre de ubicación
                    $pdf->SetFont('helvetica', 'B', 8);
                    $ubicacion_text = $grupo_ubicacion['nombre_ubicacion'];
                    if ($id_ubicacion > 0) {
                       // $ubicacion_text .= ' (ID: ' . $id_ubicacion . ')';
                        
                    }
                    $pdf->Cell(194, 5, $ubicacion_text, 0, 1, 'L');
                    
                    $pdf->Ln(1);
                    
                    // Cabecera de tabla con bordes grises (ajustada según si se ocultan columnas Mtje/Dsmtje)
                    $pdf->SetFont('helvetica', 'B', 8);
                    $pdf->SetFillColor(240, 240, 240);
                    $pdf->SetDrawColor(200, 200, 200); // Bordes grises claros
                    
                    $ocultar_cols = $info_fechas['ocultar_columnas'];
                    
                    $pdf->Cell(15, 6, 'Inicio', 1, 0, 'C', 1);
                    $pdf->Cell(15, 6, 'Fin', 1, 0, 'C', 1);
                    
                    if (!$ocultar_cols) {
                        $pdf->Cell(13, 6, 'Mtje', 1, 0, 'C', 1);
                        $pdf->Cell(13, 6, 'Dsmtje', 1, 0, 'C', 1);
                    }
                    
                    $pdf->Cell(7, 6, 'Días', 1, 0, 'C', 1);
                    $pdf->Cell(9, 6, 'Coef.', 1, 0, 'C', 1);
                    
                    // Ajustar ancho de Descripción según columnas visibles (ocultar: +26mm = 13+13)
                    $ancho_descripcion = $ocultar_cols ? 63 : 37;
                    $pdf->Cell($ancho_descripcion, 6, 'Descripción', 1, 0, 'C', 1);
                    
                    $pdf->Cell(12, 6, 'Cant.', 1, 0, 'C', 1);
                    $pdf->Cell(15, 6, 'P.Unit.', 1, 0, 'C', 1);
                    $pdf->Cell(10, 6, '%Dto', 1, 0, 'C', 1);
                    $pdf->Cell(20, 6, 'Importe(€)', 1, 0, 'C', 1);
                    $pdf->Cell(8,  6, '%Htl', 1, 0, 'C', 1);
                    $pdf->Cell(20, 6, 'Imp. Hotel(€)', 1, 1, 'C', 1);
                    $pdf->SetDrawColor(0, 0, 0); // Restaurar color negro
                    
                    // Datos de líneas de esta ubicación
                    // Asegurar que cada grupo inicia con fuente normal
                    $pdf->SetFont('helvetica', '', 7);
                    
                    foreach ($grupo_ubicacion['lineas'] as $linea) {
                        // Reset de fuente al inicio de cada línea para evitar contaminación
                        $pdf->SetFont('helvetica', '', 7);
                        // Formatear fechas
                        $fecha_inicio_linea = !empty($linea['fecha_inicio_linea_ppto']) ? date('d/m/Y', strtotime($linea['fecha_inicio_linea_ppto'])) : '-';
                        $fecha_fin = !empty($linea['fecha_fin_linea_ppto']) ? date('d/m/Y', strtotime($linea['fecha_fin_linea_ppto'])) : '-';
                        $fecha_montaje = !empty($linea['fecha_montaje_linea_ppto']) ? date('d/m/Y', strtotime($linea['fecha_montaje_linea_ppto'])) : '-';
                        $fecha_desmontaje = !empty($linea['fecha_desmontaje_linea_ppto']) ? date('d/m/Y', strtotime($linea['fecha_desmontaje_linea_ppto'])) : '-';
                        
                        // Verificar si esta línea es excepción (fechas diferentes a las predominantes)
                        $es_excepcion = in_array($linea['id_linea_ppto'], $info_fechas['excepciones'] ?? []);
                        
                        // Línea normal o KIT
                        $es_kit = (isset($linea['es_kit_articulo']) && $linea['es_kit_articulo'] == 1);
                        
                        // No aplicar negrita a KITs - mantener formato consistente
                        
                        $descripcion = $linea['descripcion_linea_ppto'] ?? '';
                        $cantidad = floatval($linea['cantidad_linea_ppto']);
                        $precio_unitario = floatval($linea['precio_unitario_linea_ppto']);
                        $descuento = floatval($linea['descuento_linea_ppto']);
                        $base_imponible = floatval($linea['base_imponible']);
                        
                        // Calcular altura dinámica basada en el contenido real
                        // Usar el ancho correcto según si las columnas están ocultas o no
                        $altura_texto_desc = $pdf->getStringHeight($ancho_descripcion - 1, $descripcion);
                        
                        // Si la descripción necesita más de una línea, usar altura calculada + margen
                        if ($altura_texto_desc > 5) {
                            $altura_fila = ceil($altura_texto_desc) + 1; // +1mm de margen interno
                        } else {
                            $altura_fila = 5; // Altura mínima para una línea
                        }
                        
                        $necesita_dos_lineas = ($altura_fila > 5);
                        
                        // VERIFICAR SI HAY ESPACIO SUFICIENTE ANTES DE EMPEZAR LA FILA
                        $espacio_necesario = $altura_fila + 5; // Altura de fila + margen de seguridad
                        $espacio_disponible = $pdf->getPageHeight() - $pdf->GetY() - $pdf->getBreakMargin();
                        
                        if ($espacio_disponible < $espacio_necesario) {
                            // No hay espacio suficiente, forzar salto de página AHORA
                            $pdf->AddPage();
                        }
                        
                        // Guardar posición inicial
                        $x_inicial = $pdf->GetX();
                        $y_inicial = $pdf->GetY();
                        
                        // Aplicar bordes grises claros a las líneas del presupuesto
                        $pdf->SetDrawColor(200, 200, 200); // Gris claro para bordes
                        
                        $pdf->Cell(15, $altura_fila, $fecha_inicio_linea, 1, 0, 'C');
                        $pdf->Cell(15, $altura_fila, $fecha_fin, 1, 0, 'C');
                        
                        // Mostrar columnas Mtje/Dsmtje solo si NO se ocultan
                        if (!$ocultar_cols) {
                            $pdf->Cell(13, $altura_fila, $fecha_montaje, 1, 0, 'C');
                            $pdf->Cell(13, $altura_fila, $fecha_desmontaje, 1, 0, 'C');
                        }
                        
                        $pdf->Cell(7, $altura_fila, $linea['dias_linea'] ?? '0', 1, 0, 'C');
                        $pdf->Cell(9, $altura_fila, number_format(floatval($linea['valor_coeficiente_linea_ppto'] ?? 1.00), 2), 1, 0, 'C');
                        
                        // Descripción: ancho ajustado según si se ocultan columnas
                        $x_desc = $pdf->GetX();
                        if ($necesita_dos_lineas) {
                            // Dibujar rectángulo con borde gris
                            $pdf->Rect($x_desc, $y_inicial, $ancho_descripcion, $altura_fila, 'D');
                            // MultiCell sin borde interno, con altura que llena exactamente el rectángulo
                            $margen_interno = 1;
                            $altura_linea_mc = ($altura_fila - $margen_interno) / max(1, floor($altura_texto_desc / 5));
                            $pdf->SetXY($x_desc + 0.5, $y_inicial + 0.5);
                            $pdf->MultiCell($ancho_descripcion - 1, $altura_linea_mc, $descripcion, 0, 'L');
                        } else {
                            // Una sola línea - usar descripción completa
                            $pdf->Cell($ancho_descripcion, $altura_fila, $descripcion, 1, 0, 'L');
                        }
                        
                        // Volver a la misma línea para las siguientes celdas
                        $pdf->SetXY($x_desc + $ancho_descripcion, $y_inicial);
                        
                        $pdf->Cell(12, $altura_fila, number_format($cantidad, 0), 1, 0, 'C');
                        $pdf->Cell(15, $altura_fila, number_format($precio_unitario, 2, ',', '.'), 1, 0, 'R');
                        $pdf->Cell(10, $altura_fila, number_format($descuento, 0), 1, 0, 'C');
                        $pdf->Cell(20, $altura_fila, number_format($base_imponible, 2, ',', '.'), 1, 0, 'R');
                        
                        // Celdas hotel: %Htl e Imp. Hotel
                        $aplica_dto_hotel = ($linea['permitir_descuentos_articulo'] != 0)
                                         && ($linea['permite_descuento_familia']    != 0);
                        if ($aplica_dto_hotel && $pct_hotel > 0) {
                            $importe_hotel_linea = $base_imponible * (1 - $pct_hotel / 100);
                            $label_pct_hotel     = ($pct_hotel == intval($pct_hotel))
                                ? intval($pct_hotel) . '%'
                                : number_format($pct_hotel, 2, ',', '.') . '%';
                        } else {
                            $importe_hotel_linea = $base_imponible;
                            $label_pct_hotel     = '-';
                        }
                        $pdf->Cell(8,  $altura_fila, $label_pct_hotel, 1, 0, 'C');
                        $pdf->Cell(20, $altura_fila, number_format($importe_hotel_linea, 2, ',', '.'), 1, 0, 'R');
                        
                        // Restaurar color de borde por defecto
                        $pdf->SetDrawColor(0, 0, 0);
                        
                        // Mover cursor manualmente a la siguiente fila
                        $pdf->SetXY($x_inicial, $y_inicial + $altura_fila);
                        
                        // No es necesario restaurar fuente ya que se resetea al inicio de cada línea
                        
                        // OBSERVACIONES DE LÍNEA
                        // Mostrar observaciones si existen y no están vacías
                        // O auto-generar si es línea excepcional (fechas diferentes a predominantes)
                        $observaciones_a_mostrar = '';
                        
                        // Verificar si hay observaciones manuales del usuario
                        if (!empty($linea['observaciones_linea_ppto']) && trim($linea['observaciones_linea_ppto']) != '') {
                            $observaciones_a_mostrar = trim($linea['observaciones_linea_ppto']);
                        }
                        
                        // Si es excepción, agregar fechas particulares a las observaciones
                        if ($es_excepcion && $ocultar_cols) {
                            $obs_fechas = 'Mtje: ' . $fecha_montaje . ' - Dsmtje: ' . $fecha_desmontaje;
                            if (!empty($observaciones_a_mostrar)) {
                                // Si ya hay observaciones, agregar al final con separador
                                $observaciones_a_mostrar .= ' | ' . $obs_fechas;
                            } else {
                                // Si no hay observaciones, crear nuevas solo con las fechas
                                $observaciones_a_mostrar = $obs_fechas;
                            }
                        }
                        
                        // Renderizar observaciones si existen
                        if (!empty($observaciones_a_mostrar)) {
                            // Guardar posición Y actual (después de la línea principal)
                            $y_antes_obs = $pdf->GetY();
                            
                            // Configurar formato para observaciones
                            $pdf->SetFont('helvetica', '', 6.5);
                            $pdf->SetTextColor(80, 80, 80);
                            
                            // Determinar alineación según configuración de empresa
                            $obs_alineadas = !empty($datos_empresa['obs_linea_alineadas_descripcion_empresa']);
                            
                            if ($obs_alineadas) {
                                // Alineado bajo columna Descripción: sin indentación manual,
                                // el propio SetX posiciona el texto correctamente
                                $texto_observaciones = $observaciones_a_mostrar;
                                $ancho_obs = 170 - ($x_desc - $x_inicial);
                                $pdf->MultiCell($ancho_obs, 4, $texto_observaciones, 0, 'L', false, 1, $x_desc, $y_antes_obs);
                            } else {
                                // Desde margen izquierdo con indentación de 4 espacios (comportamiento original)
                                $texto_observaciones = '    ' . $observaciones_a_mostrar;
                                $pdf->MultiCell(170, 4, $texto_observaciones, 0, 'L', false, 1, '', $y_antes_obs);
                            }
                            
                            // Resetear colores y fuente para siguientes elementos
                            $pdf->SetTextColor(0, 0, 0);
                            $pdf->SetFont('helvetica', '', 7);
                        }
                        
                        // Componentes del KIT
                        // Mostrar solo si es KIT Y el detalle NO está oculto
                        if ($es_kit && !empty($linea['id_articulo']) && 
                            isset($linea['ocultar_detalle_kit_linea_ppto']) && 
                            $linea['ocultar_detalle_kit_linea_ppto'] == 0) {
                            
                            $componentes = $kitModel->get_kits_by_articulo_maestro($linea['id_articulo']);
                            
                            // Filtrar solo componentes activos
                            if (!empty($componentes)) {
                                $componentesActivos = array_filter($componentes, function($comp) {
                                    return isset($comp['activo_articulo_componente']) && $comp['activo_articulo_componente'] != 0;
                                });
                                
                                foreach ($componentesActivos as $comp) {
                                    $pdf->SetFont('helvetica', 'I', 6);
                                    $pdf->SetTextColor(100, 100, 100);
                                    $pdf->Cell(15, 4, '', 0, 0, 'C');
                                    $pdf->Cell(15, 4, '', 0, 0, 'C');
                                    
                                    // Omitir celdas de Mtje/Dsmtje si están ocultas
                                    if (!$ocultar_cols) {
                                        $pdf->Cell(13, 4, '', 0, 0, 'C');
                                        $pdf->Cell(13, 4, '', 0, 0, 'C');
                                    }
                                    
                                    $pdf->Cell(7, 4, '', 0, 0, 'C');
                                    $pdf->Cell(9, 4, '', 0, 0, 'C');
                                    
                                    $cantidad_comp = $comp['cantidad_kit'] ?? $comp['total_componente_kit'] ?? 1;
                                    $nombre_comp = $comp['nombre_articulo_componente'] ?? $comp['nombre_articulo'] ?? 'Sin nombre';
                                    
                                    // Ajustar ancho según si se ocultan columnas - usar nombre completo
                                    $pdf->Cell($ancho_descripcion, 4, '    • ' . $cantidad_comp . 'x ' . $nombre_comp, 0, 0, 'L');
                                    $pdf->Cell(12, 4, '', 0, 0, 'C');
                                    $pdf->Cell(73, 4, '', 0, 1, 'R');
                                    
                                    $pdf->SetFont('helvetica', '', 7);
                                    $pdf->SetTextColor(0, 0, 0);
                                }
                            }
                        }
                    } // Fin foreach líneas
                    
                    // Subtotal por ubicación
                    $pdf->SetFont('helvetica', 'B', 7);
                    $pdf->SetFillColor(245, 245, 245);
                    $pdf->SetDrawColor(200, 200, 200); // Bordes grises claros
                    $pdf->Cell(146, 5, 'Subtotal ' . $grupo_ubicacion['nombre_ubicacion'], 1, 0, 'R', 1);
                    $pdf->Cell(20, 5, number_format($grupo_ubicacion['subtotal_ubicacion'], 2, ',', '.'), 1, 0, 'R', 1);
                    $pdf->Cell(8,  5, '-', 1, 0, 'C', 1);
                    $pdf->Cell(20, 5, number_format($grupo_ubicacion['subtotal_hotel_ubicacion'], 2, ',', '.'), 1, 1, 'R', 1);
                    $pdf->SetDrawColor(0, 0, 0); // Restaurar color negro
                    $pdf->Ln(2);
                    
                } // Fin foreach ubicaciones
                
                // Subtotal por fecha - SOLO SI ESTÁ HABILITADO en configuración de empresa
                if ($mostrar_subtotales_fecha) {
                    $pdf->SetFont('helvetica', 'B', 8);
                    $pdf->SetFillColor(220, 220, 220);
                    $pdf->SetDrawColor(200, 200, 200); // Bordes grises claros
                    $pdf->Cell(146, 6, 'Subtotal Fecha ' . $fecha_formateada, 1, 0, 'R', 1);
                    $pdf->Cell(20, 6, number_format($grupo_fecha['subtotal_fecha'], 2, ',', '.'), 1, 0, 'R', 1);
                    $pdf->Cell(8,  6, '-', 1, 0, 'C', 1);
                    $pdf->Cell(20, 6, number_format($grupo_fecha['subtotal_hotel_fecha'], 2, ',', '.'), 1, 1, 'R', 1);
                    $pdf->SetDrawColor(0, 0, 0); // Restaurar color negro
                    $pdf->Ln(3);
                } else {
                    // Sin subtotal, solo un pequeño espacio visual
                    $pdf->Ln(2);
                }
                
            } // Fin foreach fechas
            
            // =====================================================
            // TOTALES
            // =====================================================
            
            $pdf->Ln(5);
            
            // Línea separadora sutil antes de los totales
            $pdf->SetDrawColor(200, 200, 200);
            $pdf->Line(145, $pdf->GetY(), 200, $pdf->GetY());
            $pdf->Ln(3);
            
            // Subtotal bruto (antes de cualquier descuento)
            $pdf->SetFont('helvetica', '', 9);
            $pdf->SetFillColor(248, 249, 250);
            $pdf->SetDrawColor(220, 220, 220);
            $pdf->Cell(144, 6, '', 0, 0);
            $pdf->Cell(30, 6, 'Subtotal:', 1, 0, 'R', 1);
            $pdf->SetFont('helvetica', 'B', 9);
            $pdf->Cell(20, 6, number_format($subtotal_sin_descuento, 2, ',', '.') . ' €', 1, 1, 'R', 1);
            
            // Descuento de línea (rojo, solo si hay)
            if ($total_descuentos > 0) {
                $pdf->SetFont('helvetica', '', 9);
                $pdf->SetFillColor(248, 249, 250);
                $pdf->SetDrawColor(220, 220, 220);
                $pdf->Cell(144, 6, '', 0, 0);
                $pdf->Cell(30, 6, 'Dto. línea:', 1, 0, 'R', 1);
                $pdf->SetFont('helvetica', 'B', 9);
                $pdf->SetTextColor(231, 76, 60);
                $pdf->Cell(20, 6, '-' . number_format($total_descuentos, 2, ',', '.') . ' €', 1, 1, 'R', 1);
                $pdf->SetTextColor(0, 0, 0);
            }
            
            // Descuento Hotel (naranja, solo si pct_hotel > 0)
            if ($pct_hotel > 0 && $total_descuento_hotel > 0) {
                $pdf->SetFont('helvetica', '', 9);
                $pdf->SetFillColor(248, 249, 250);
                $pdf->SetDrawColor(220, 220, 220);
                $pdf->Cell(144, 6, '', 0, 0);
                $label_dto_hotel = ($pct_hotel == intval($pct_hotel))
                    ? 'Dto. Hotel (' . intval($pct_hotel) . '%):'
                    : 'Dto. Hotel (' . number_format($pct_hotel, 2, ',', '.') . '%):';
                $pdf->Cell(30, 6, $label_dto_hotel, 1, 0, 'R', 1);
                $pdf->SetFont('helvetica', 'B', 9);
                $pdf->SetTextColor(230, 126, 34); // Naranja
                $pdf->Cell(20, 6, '-' . number_format($total_descuento_hotel, 2, ',', '.') . ' €', 1, 1, 'R', 1);
                $pdf->SetTextColor(0, 0, 0);
            }
            
            // Base Imponible Hotel
            $pdf->SetFont('helvetica', '', 9);
            $pdf->SetFillColor(248, 249, 250);
            $pdf->SetDrawColor(220, 220, 220);
            $pdf->Cell(144, 6, '', 0, 0);
            $pdf->Cell(30, 6, 'Base Imponible:', 1, 0, 'R', 1);
            $pdf->SetFont('helvetica', 'B', 9);
            $pdf->Cell(20, 6, number_format($total_base_hotel, 2, ',', '.') . ' €', 1, 1, 'R', 1);
            
            // Desglose de IVA Hotel (solo si hay más de un tipo)
            if (count($desglose_iva_hotel) > 1) {
                $pdf->SetFont('helvetica', '', 8);
                $pdf->Cell(144, 5, '', 0, 0);
                $pdf->Cell(30, 5, 'Desglose de IVA:', 0, 1, 'R');
                
                foreach ($desglose_iva_hotel as $porcentaje => $valores) {
                    $pdf->Cell(144, 4, '', 0, 0);
                    $pdf->Cell(30, 4, "Base IVA {$porcentaje}%:", 0, 0, 'R');
                    $pdf->Cell(20, 4, number_format($valores['base'], 2, ',', '.') . ' €', 0, 1, 'R');
                    
                    $pdf->Cell(144, 4, '', 0, 0);
                    $pdf->Cell(30, 4, "IVA {$porcentaje}%:", 0, 0, 'R');
                    $pdf->Cell(20, 4, number_format($valores['cuota'], 2, ',', '.') . ' €', 0, 1, 'R');
                }
            }
            
            // Total IVA Hotel
            $pdf->SetFont('helvetica', '', 9);
            $pdf->SetFillColor(248, 249, 250);
            $pdf->SetDrawColor(220, 220, 220);
            $pdf->Cell(144, 6, '', 0, 0);
            
            if (count($desglose_iva_hotel) == 1) {
                $porcentaje_unico = key($desglose_iva_hotel);
                $pdf->Cell(30, 6, "Total IVA ({$porcentaje_unico}%):", 1, 0, 'R', 1);
            } else {
                $pdf->Cell(30, 6, 'Total IVA:', 1, 0, 'R', 1);
            }
            
            $pdf->Cell(20, 6, number_format($total_iva_hotel, 2, ',', '.') . ' €', 1, 1, 'R', 1);
            
            $pdf->Ln(2);
            
            // TOTAL Hotel
            $pdf->SetFont('helvetica', 'B', 11);
            $pdf->SetFillColor(102, 126, 234);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->SetDrawColor(80, 100, 200);
            $pdf->SetLineWidth(0.5);
            $pdf->Cell(144, 8, '', 0, 0);
            $pdf->Cell(20, 8, 'TOTAL:', 1, 0, 'R', 1);
            $pdf->Cell(30, 8, number_format($total_presupuesto_hotel, 2, ',', '.') . ' €', 1, 1, 'R', 1);
            
            // Restaurar colores y grosor de línea
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetDrawColor(0, 0, 0);
            $pdf->SetLineWidth(0.2);
            
            
            // =====================================================
            // *** PUNTO 17: JUSTIFICACIÓN EXENCIÓN IVA ***
            // =====================================================
            
            if (isset($datos_presupuesto['exento_iva_cliente']) && 
                $datos_presupuesto['exento_iva_cliente'] == 1 && 
                !empty($datos_presupuesto['justificacion_exencion_iva_cliente'])) {
                
                $pdf->Ln(6);
                
                // Título
                $pdf->SetFont('helvetica', 'B', 9);
                $pdf->SetFillColor(255, 243, 205); // Fondo amarillo claro
                $pdf->SetDrawColor(255, 193, 7); // Borde amarillo
                $pdf->SetTextColor(133, 100, 4); // Texto marrón/dorado oscuro
                $pdf->Cell(0, 6, 'INFORMACIÓN FISCAL - CLIENTE EXENTO DE IVA', 1, 1, 'C', 1);
                
                // Justificación
                $pdf->SetFont('helvetica', 'I', 8);
                $pdf->SetFillColor(255, 252, 240); // Fondo amarillo muy claro
                $pdf->SetTextColor(80, 80, 80); // Texto gris oscuro
                $pdf->MultiCell(0, 5, $datos_presupuesto['justificacion_exencion_iva_cliente'], 1, 'L', 1);
                
                // Restaurar colores
                $pdf->SetTextColor(0, 0, 0);
                $pdf->SetDrawColor(0, 0, 0);
            }
            
            // =====================================================
            // FORMA DE PAGO
            // =====================================================
            
            if (!empty($datos_presupuesto['nombre_pago'])) {
                $pdf->Ln(6);
                
                // Título "FORMA DE PAGO:"
                $pdf->SetFont('helvetica', 'B', 9);
                $pdf->SetTextColor(52, 73, 94); // Color oscuro
                $pdf->Cell(40, 5, 'FORMA DE PAGO:', 0, 0, 'L');
                
                // Construir frase de forma de pago
                $frase_pago = [];
                
                // Método de pago
                if (!empty($datos_presupuesto['nombre_metodo_pago'])) {
                    $frase_pago[] = $datos_presupuesto['nombre_metodo_pago'];
                }
                
                // Anticipo
                if (!empty($datos_presupuesto['porcentaje_anticipo_pago'])) {
                    $texto_anticipo = 'Anticipo del ' . $datos_presupuesto['porcentaje_anticipo_pago'] . '%';
                    
                    if (isset($datos_presupuesto['dias_anticipo_pago'])) {
                        $dias_anticipo = intval($datos_presupuesto['dias_anticipo_pago']);
                        
                        if ($dias_anticipo < 0) {
                            $texto_anticipo .= ' (' . abs($dias_anticipo) . ' dias antes del inicio)';
                        } elseif ($dias_anticipo > 0) {
                            $texto_anticipo .= ' (' . $dias_anticipo . ' dias despues del inicio)';
                        } else {
                            $texto_anticipo .= ' (el dia de inicio)';
                        }
                    }
                    
                    $frase_pago[] = $texto_anticipo;
                }
                
                // Pago final
                if (!empty($datos_presupuesto['porcentaje_final_pago']) && 
                    $datos_presupuesto['porcentaje_anticipo_pago'] < 100) {
                    $texto_final = 'Pago final del ' . $datos_presupuesto['porcentaje_final_pago'] . '%';
                    
                    if (isset($datos_presupuesto['dias_final_pago'])) {
                        $dias_final = intval($datos_presupuesto['dias_final_pago']);
                        
                        if ($dias_final < 0) {
                            $texto_final .= ' (' . abs($dias_final) . ' dias antes del fin)';
                        } elseif ($dias_final > 0) {
                            $texto_final .= ' (' . $dias_final . ' dias despues del fin)';
                        } else {
                            $texto_final .= ' (el dia de fin)';
                        }
                    }
                    
                    $frase_pago[] = $texto_final;
                }
                
                // Descuento
                if (!empty($datos_presupuesto['descuento_pago']) && $datos_presupuesto['descuento_pago'] > 0) {
                    $frase_pago[] = 'Descuento aplicado: ' . $datos_presupuesto['descuento_pago'] . '%';
                }
                
                // Unir todas las partes con punto y coma y mostrar
                $texto_forma_pago = implode('; ', $frase_pago) . '.';
                
                $pdf->SetFont('helvetica', '', 8);
                $pdf->SetTextColor(70, 70, 70); // Gris oscuro
                $pdf->MultiCell(160, 4, $texto_forma_pago, 0, 'L');
                
                // =====================================================
                // DATOS BANCARIOS PARA TRANSFERENCIA
                // =====================================================
                
                // Detectar si la forma de pago incluye TRANSFERENCIA
                $forma_pago_lower = strtolower($datos_presupuesto['nombre_metodo_pago'] ?? '');
                $es_transferencia = (strpos($forma_pago_lower, 'transferencia') !== false);
                
                // Verificar si hay datos bancarios disponibles
                $tiene_datos_bancarios = (
                    !empty($datos_empresa['iban_empresa']) ||
                    !empty($datos_empresa['swift_empresa']) ||
                    !empty($datos_empresa['banco_empresa'])
                );
                
                // Verificar si el switch de mostrar cuenta bancaria está activado (default: 1)
                $mostrar_cuenta_bancaria_activado = ($datos_empresa['mostrar_cuenta_bancaria_pdf_presupuesto_empresa'] ?? 1);
                
                // Solo mostrar si es transferencia Y hay datos bancarios Y el switch está activado
                if ($es_transferencia && $tiene_datos_bancarios && $mostrar_cuenta_bancaria_activado) {
                    
                    // Calcular altura dinámica según campos disponibles
                    $altura_bloque = 5; // 5mm overhead (título + márgenes) - REDUCIDO
                    if (!empty($datos_empresa['banco_empresa'])) $altura_bloque += 3.5;
                    if (!empty($datos_empresa['iban_empresa'])) $altura_bloque += 3.5;
                    if (!empty($datos_empresa['swift_empresa'])) $altura_bloque += 3.5;
                    
                    // Verificar si hay espacio suficiente
                    if (($pdf->GetY() + $altura_bloque) > 270) {
                        $pdf->AddPage();
                        $pdf->SetY(15);
                    }
                    
                    $pdf->Ln(2); // Espacio antes del bloque - REDUCIDO
                    
                    // Guardar posición inicial
                    $x_inicio = $pdf->GetX();
                    $y_inicio = $pdf->GetY();
                    
                    // Dibujar rectángulo de fondo gris claro
                    $pdf->SetFillColor(245, 245, 245);
                    $pdf->SetDrawColor(180, 180, 180);
                    $pdf->Rect($x_inicio, $y_inicio, 195, $altura_bloque, 'DF');
                    
                    // Posicionar para escribir el título
                    $pdf->SetXY($x_inicio + 2, $y_inicio + 1.5); // Padding reducido
                    
                    // Título del bloque - FUENTE MÁS PEQUEÑA
                    $pdf->SetFont('helvetica', 'B', 7);
                    $pdf->SetTextColor(52, 73, 94);
                    $pdf->Cell(189, 3, 'DATOS BANCARIOS PARA TRANSFERENCIA', 0, 1, 'L', false);
                    
                    $y_actual = $pdf->GetY() + 0.5; // Espacio reducido
                    
                    // Mostrar Banco si existe
                    if (!empty($datos_empresa['banco_empresa'])) {
                        $pdf->SetXY($x_inicio + 2, $y_actual); // Padding reducido
                        $pdf->SetFont('helvetica', '', 6); // Fuente label más pequeña
                        $pdf->SetTextColor(70, 70, 70);
                        $pdf->Cell(20, 3, 'Banco:', 0, 0, 'L'); // Altura reducida
                        $pdf->SetFont('helvetica', 'B', 7); // Fuente valor más pequeña
                        $pdf->Cell(160, 3, $datos_empresa['banco_empresa'], 0, 1, 'L');
                        $y_actual += 3.5; // Separación reducida
                    }
                    
                    // Mostrar IBAN si existe (formateado con espacios cada 4 caracteres)
                    if (!empty($datos_empresa['iban_empresa'])) {
                        $pdf->SetXY($x_inicio + 2, $y_actual);
                        $pdf->SetFont('helvetica', '', 6);
                        $pdf->SetTextColor(70, 70, 70);
                        $pdf->Cell(20, 3, 'IBAN:', 0, 0, 'L');
                        $pdf->SetFont('helvetica', 'B', 7);
                        
                        // Formatear IBAN con espacios cada 4 caracteres
                        $iban_sin_espacios = str_replace(' ', '', $datos_empresa['iban_empresa']);
                        $iban_formateado = wordwrap($iban_sin_espacios, 4, ' ', true);
                        
                        $pdf->Cell(160, 3, $iban_formateado, 0, 1, 'L');
                        $y_actual += 3.5;
                    }
                    
                    // Mostrar SWIFT si existe
                    if (!empty($datos_empresa['swift_empresa'])) {
                        $pdf->SetXY($x_inicio + 2, $y_actual);
                        $pdf->SetFont('helvetica', '', 6);
                        $pdf->SetTextColor(70, 70, 70);
                        $pdf->Cell(20, 3, 'SWIFT:', 0, 0, 'L');
                        $pdf->SetFont('helvetica', 'B', 7);
                        $pdf->Cell(160, 3, $datos_empresa['swift_empresa'], 0, 1, 'L');
                        $y_actual += 3.5;
                    }
                    
                    // Posicionar después del bloque
                    $pdf->SetY($y_inicio + $altura_bloque + 1.5); // Espacio después reducido
                }
            }
            
            // =====================================================
            // OBSERVACIONES DE FAMILIAS Y ARTÍCULOS
            // =====================================================
            
            // Pre-filtrar: solo ítems con nombre Y texto real para evitar
            // que se pinte el título aunque no haya contenido visible (ítem 18)
            $obs_con_contenido = array_filter(
                is_array($observaciones_array) ? $observaciones_array : [],
                function ($obs) {
                    $nombre = '';
                    if ($obs['tipo_observacion'] == 'familia' && !empty($obs['nombre_familia'])) {
                        $nombre = $obs['nombre_familia'];
                    } elseif ($obs['tipo_observacion'] == 'articulo' && !empty($obs['nombre_articulo'])) {
                        $nombre = $obs['nombre_articulo'];
                    }
                    $texto = $obs['observacion_es'] ?? '';
                    return !empty($nombre) && !empty(trim($texto));
                }
            );
            
            if (!empty($obs_con_contenido)) {
                $pdf->Ln(8);
                
                // Título de la sección
                $pdf->SetFont('helvetica', 'B', 9.5);
                $pdf->SetTextColor(66, 66, 66);
                $pdf->Cell(0, 6, 'OBSERVACIONES', 0, 1, 'L');
                
                $pdf->Ln(2);
                
                // Recorrer cada observación (ya filtrada: nombre y texto garantizados)
                foreach ($obs_con_contenido as $obs) {
                    // Determinar símbolo según tipo: * para familia, ** para artículo
                    $simbolo = ($obs['tipo_observacion'] == 'familia') ? '*' : '**';
                    
                    // Fondo gris claro para cada observación
                    $pdf->SetFillColor(250, 250, 250);
                    
                    // Símbolo y texto en la misma línea
                    $pdf->SetFont('helvetica', '', 8);
                    $pdf->SetTextColor(97, 97, 97);
                    $pdf->MultiCell(0, 4, $simbolo . ' ' . ($obs['observacion_es'] ?? ''), 0, 'L', 1);
                    
                    $pdf->Ln(3);
                }
            }
            
            // =====================================================
            // OBSERVACIONES DE PIE INTEGRADAS (cuando NO se destacan)
            // =====================================================
            
            // Si destacar_observaciones_pie_presupuesto es 0 (FALSE), mostrar aquí integradas
            if (!empty($datos_presupuesto['observaciones_pie_presupuesto']) && 
                isset($datos_presupuesto['destacar_observaciones_pie_presupuesto']) && 
                $datos_presupuesto['destacar_observaciones_pie_presupuesto'] == 0) {
                
                // Si no había observaciones anteriores, agregar el título de sección
                if (empty($obs_con_contenido)) {
                    $pdf->Ln(8);
                    $pdf->SetFont('helvetica', 'B', 9.5);
                    $pdf->SetTextColor(66, 66, 66);
                    $pdf->Cell(0, 6, 'OBSERVACIONES', 0, 1, 'L');
                    $pdf->Ln(2);
                }
                
                // Símbolo *** para observaciones de pie
                $pdf->SetFillColor(250, 250, 250);
                $pdf->SetFont('helvetica', '', 8);
                $pdf->SetTextColor(97, 97, 97);
                $pdf->MultiCell(0, 4, $datos_presupuesto['observaciones_pie_presupuesto'], 0, 'L', 1);
                $pdf->Ln(3);
            }
            
            // =====================================================
            // OBSERVACIONES DE PIE DEL PRESUPUESTO (DESTACADAS)
            // =====================================================
            
            // Solo mostrar destacadas si el campo es 1 o no está definido (compatibilidad con registros antiguos)
            if (!empty($datos_presupuesto['observaciones_pie_presupuesto']) && 
                (!isset($datos_presupuesto['destacar_observaciones_pie_presupuesto']) || 
                 $datos_presupuesto['destacar_observaciones_pie_presupuesto'] == 1)) {
                $pdf->Ln(10);
                
                // Línea superior decorativa
                $pdf->SetDrawColor(44, 62, 80); // Color oscuro
                $pdf->SetLineWidth(0.8);
                $pdf->Line(8, $pdf->GetY(), 202, $pdf->GetY());
                $pdf->Ln(3);
                
                // Fondo gris claro
                $y_inicio = $pdf->GetY();
                $texto_altura = $pdf->getStringHeight(0, $datos_presupuesto['observaciones_pie_presupuesto']);
                $pdf->SetFillColor(248, 249, 250);
                $pdf->Rect(8, $y_inicio, 194, $texto_altura + 6, 'F');
                
                // Texto centrado
                $pdf->SetFont('helvetica', '', 8);
                $pdf->SetTextColor(99, 110, 114);
                $pdf->SetXY(8, $y_inicio + 3);
                $pdf->MultiCell(194, 4, $datos_presupuesto['observaciones_pie_presupuesto'], 0, 'C');
                
                // Línea inferior
                $pdf->SetDrawColor(223, 230, 233); // Color gris claro
                $pdf->SetLineWidth(0.3);
                $pdf->Line(8, $pdf->GetY() + 3, 202, $pdf->GetY() + 3);
            }
            
            // =====================================================
            // CASILLAS DE FIRMA (DESPUÉS DE OBSERVACIONES DE PIE)
            // =====================================================
            
            $pdf->Ln(10);
            
            // Restaurar colores y configuración
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetDrawColor(0, 0, 0);
            $pdf->SetLineWidth(0.2);
            
            // CONTROL DE SALTO DE PÁGINA: Verificar si hay espacio suficiente para las firmas
            $altura_necesaria_firmas = 45; // Altura aproximada de toda la sección de firmas (título + espacio + línea + textos + fechas)
            $espacio_disponible = $pdf->getPageHeight() - $pdf->GetY() - $pdf->getBreakMargin();
            
            // Si no hay suficiente espacio, saltar a nueva página
            if ($espacio_disponible < $altura_necesaria_firmas) {
                $pdf->AddPage();
            }
            
            // IMPORTANTE: Desactivar saltos automáticos durante las firmas para mantener todo junto
            $pdf->SetAutoPageBreak(false);
            
            // Ancho de cada casilla de firma
            $ancho_casilla = 90;
            $separacion = 7;
            $x_inicio_izq = 8;
            $x_inicio_der = $x_inicio_izq + $ancho_casilla + $separacion;
            
            // Guardar posición Y inicial para alinear ambas casillas
            $y_inicio_firmas = $pdf->GetY();
            
            // ======== CASILLA IZQUIERDA: FIRMA MDR (EMPRESA) ========
            
            $pdf->SetXY($x_inicio_izq, $y_inicio_firmas);
            
            // Título - Usar cabecera personalizada de empresa o valor por defecto
            $cabecera_firma = !empty($datos_empresa['cabecera_firma_presupuesto_empresa']) 
                ? strtoupper($datos_empresa['cabecera_firma_presupuesto_empresa']) 
                : 'DEPARTAMENTO COMERCIAL';
            
            $pdf->SetFont('helvetica', 'B', 9);
            $pdf->Cell($ancho_casilla, 5, $cabecera_firma, 0, 1, 'C');
            
            $pdf->SetX($x_inicio_izq);
            
            // ========================================
            // FIRMA DIGITAL DEL COMERCIAL
            // Punto 14: Nueva Funcionalidad - Firma de Empleado
            // ========================================
            
            // Obtener firma digital y datos del comercial si el usuario está en sesión
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
                    // Log del error pero continuar sin firma
                    error_log("Error al obtener datos del comercial: " . $e->getMessage());
                }
            }
            
            // Si existe firma digital, renderizarla
            if (!empty($firma_comercial)) {
                // Verificar que sea un base64 válido
                if (preg_match('/^data:image\/(png|jpg|jpeg);base64,/', $firma_comercial)) {
                    $pdf->Ln(2);
                    
                    // Renderizar imagen de firma centrada
                    // Tamaño: ancho máximo 60mm, alto máximo 14mm
                    $x_firma = $x_inicio_izq + 15; // Centrado en la casilla
                    $y_firma = $pdf->GetY();
                    
                    try {
                        // TCPDF requiere decodificar el base64 y usar prefijo @
                        // Extraer solo el base64 sin el prefijo data:image/png;base64,
                        $imagen_base64 = preg_replace('/^data:image\/(png|jpg|jpeg);base64,/', '', $firma_comercial);
                        $imagen_decodificada = base64_decode($imagen_base64);
                        
                        // Usar Image con prefijo @ para imágenes en memoria
                        $pdf->Image(
                            '@' . $imagen_decodificada, // @ indica imagen en memoria
                            $x_firma,                    // X position
                            $y_firma,                    // Y position
                            60,                          // Ancho máximo 60mm
                            14,                          // Alto máximo 14mm (ajustado proporcionalmente)
                            'PNG',                       // Tipo específico
                            '',                          // Link
                            '',                          // Align
                            false,                       // No resize
                            300,                         // DPI
                            '',                          // Palign
                            false,                       // Ismask
                            false,                       // Imgmask
                            0,                           // Border
                            false,                       // Fitbox
                            false,                       // Hidden
                            true                         // Fitonpage
                        );
                        
                        // Ajustar posición Y después de la imagen
                        $pdf->SetY($y_firma + 15);
                        
                    } catch (Exception $e) {
                        // Si hay error al renderizar la imagen, dejar espacio vacío
                        error_log("Error al renderizar firma en PDF: " . $e->getMessage());
                        $pdf->Ln(18);
                    }
                } else {
                    // Formato de firma inválido, dejar espacio vacío
                    $pdf->Ln(18);
                }
            } else {
                // No hay firma digital, dejar espacio vacío para firma manuscrita
                $pdf->Ln(18);
            }
            
            // Línea para firmar
            $y_linea_izq = $pdf->GetY();
            $pdf->Line($x_inicio_izq + 10, $y_linea_izq, $x_inicio_izq + $ancho_casilla - 10, $y_linea_izq);
            
            $pdf->SetXY($x_inicio_izq, $y_linea_izq + 2);
            $pdf->SetFont('helvetica', '', 8);
            //$pdf->Cell($ancho_casilla, 4, 'Firma y Sello', 0, 1, 'C');
            
            // Nombre del firmante (si se obtuvo del comercial asociado al usuario)
            if (!empty($nombre_firmante)) {
                $pdf->SetXY($x_inicio_izq, $pdf->GetY());
                $pdf->SetFont('helvetica', 'I', 7);
                $pdf->Cell($ancho_casilla, 4, $nombre_firmante, 0, 1, 'C');
            } else {
                $pdf->Ln(2);
            }
            
            // Fecha (fecha actual de impresión)
            $pdf->SetXY($x_inicio_izq, $pdf->GetY());
            $pdf->SetFont('helvetica', '', 7);
            $fecha_impresion = date('d/m/Y');
            $pdf->Cell($ancho_casilla, 4, 'Fecha: ' . $fecha_impresion, 0, 1, 'C');
            
            // ======== CASILLA DERECHA: FIRMA CLIENTE (VISTO BUENO) ========
            
            $pdf->SetXY($x_inicio_der, $y_inicio_firmas);
            
            // Título
            $pdf->SetFont('helvetica', 'B', 9);
            $pdf->Cell($ancho_casilla, 5, 'VISTO BUENO DEL CLIENTE', 0, 1, 'C');
            
            $pdf->SetX($x_inicio_der);
            $pdf->Ln(18);
            
            // Línea para firmar
            $y_linea_der = $pdf->GetY();
            $pdf->Line($x_inicio_der + 10, $y_linea_der, $x_inicio_der + $ancho_casilla - 10, $y_linea_der);
            
            $pdf->SetXY($x_inicio_der, $y_linea_der + 2);
            $pdf->SetFont('helvetica', '', 8);
            
            // Siempre mostrar texto genérico "Firma del Cliente"
            $pdf->Cell($ancho_casilla, 4, 'Firma del Cliente', 0, 1, 'C');
            
            $pdf->SetX($x_inicio_der);
            $pdf->Ln(2);
            
            // Fecha
            $pdf->SetXY($x_inicio_der, $pdf->GetY());
            $pdf->SetFont('helvetica', '', 7);
            $pdf->Cell($ancho_casilla, 4, 'Fecha: ___/___/______', 0, 1, 'C');
            
            // Restaurar saltos automáticos de página
            $pdf->SetAutoPageBreak(TRUE, 25);
            
            // =====================================================
            // SALIDA DEL PDF
            // =====================================================
            
            // Limpiar cualquier output previo
            ob_end_clean();
            
            $nombre_archivo = 'Presupuesto_' . ($datos_presupuesto['numero_presupuesto'] ?? 'SN') . '_Hotel.pdf';
            
            $pdf->Output($nombre_archivo, 'I'); // 'I' = inline en navegador
            
            $registro->registrarActividad(
                'admin',
                'impresionpresupuestohotel_m2_pdf_es.php',
                'cli_esp',
                "PDF HOTEL generado para presupuesto ID: $id_presupuesto",
                'info'
            );
            
            exit; // Importante: terminar la ejecución después de enviar el PDF
            
        } catch (Exception $e) {
            ob_end_clean(); // Limpiar buffer en caso de error
            
            $registro->registrarActividad(
                'admin',
                'impresionpresupuestohotel_m2_pdf_es.php',
                'cli_esp',
                "Error PDF Hotel: " . $e->getMessage(),
                'error'
            );
            
            // Mostrar error al usuario
            header('Content-Type: text/html; charset=utf-8');
            echo '<!DOCTYPE html>';
            echo '<html><head><meta charset="UTF-8"><title>Error</title></head><body>';
            echo '<h2>Error al generar el PDF</h2>';
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
