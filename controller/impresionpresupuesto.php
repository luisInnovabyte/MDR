<?php

require_once "../config/conexion.php";
require_once "../config/funciones.php";
require_once "../models/ImpresionPresupuesto.php";

// Inicializar clases
$registro = new RegistroActividad();
$impresion = new ImpresionPresupuesto();

// Switch principal basado en operación
switch ($_GET["op"]) {
    
    /**
     * Generar presupuesto para cliente final en español
     * 
     * Genera un documento HTML imprimible con:
     * - Cabecera con logo de empresa (si existe)
     * - Datos de la empresa (fiscales y contacto)
     * - Datos del cliente
     * - Información del presupuesto (número, fechas, evento)
     * 
     * El documento se abre en nueva ventana y permite:
     * - Vista previa
     * - Impresión directa (Ctrl+P)
     * - Guardar como PDF (opción del navegador)
     */
    case "cli_esp":
        try {
            // 1. Validar que se recibió el ID del presupuesto
            if (!isset($_POST['id_presupuesto']) || empty($_POST['id_presupuesto'])) {
                throw new Exception("No se recibió el ID del presupuesto");
            }
            
            $id_presupuesto = intval($_POST['id_presupuesto']);
            
            // 2. Obtener datos del presupuesto (versión actual)
            $datos_presupuesto = $impresion->get_datos_cabecera($id_presupuesto);
            
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
            $url_logo = '';
            
            if (!empty($datos_empresa['logotipo_empresa'])) {
                $registro->registrarActividad(
                    'admin',
                    'impresionpresupuesto.php',
                    'cli_esp',
                    "Ruta logo desde BD: " . $datos_empresa['logotipo_empresa'],
                    'info'
                );
                
                if ($impresion->validar_logo($datos_empresa['logotipo_empresa'])) {
                    $mostrar_logo = true;
                    $url_logo = $impresion->get_url_logo($datos_empresa['logotipo_empresa']);
                    
                    $registro->registrarActividad(
                        'admin',
                        'impresionpresupuesto.php',
                        'cli_esp',
                        "Logo validado. URL generada: $url_logo",
                        'info'
                    );
                } else {
                    $registro->registrarActividad(
                        'admin',
                        'impresionpresupuesto.php',
                        'cli_esp',
                        "Logo NO validado para ruta: " . $datos_empresa['logotipo_empresa'],
                        'warning'
                    );
                }
            } else {
                $registro->registrarActividad(
                    'admin',
                    'impresionpresupuesto.php',
                    'cli_esp',
                    "No hay logotipo_empresa en la BD",
                    'info'
                );
            }
            
            // 5. Formatear fechas en formato europeo (d/m/Y)
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
            
            // 6. Registrar actividad
            $registro->registrarActividad(
                'admin',
                'impresionpresupuesto.php',
                'cli_esp',
                "Generando impresión para presupuesto: " . $datos_presupuesto['numero_presupuesto'] . 
                " (Versión: " . $datos_presupuesto['numero_version_presupuesto'] . ")",
                'info'
            );
            
            // 7. Configurar headers para HTML
            header('Content-Type: text/html; charset=utf-8');
            
            // 8. Generar HTML
            $html = '<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Presupuesto ' . htmlspecialchars($datos_presupuesto['numero_presupuesto']) . '</title>
    <style>
        /* ============================================ */
        /* CONFIGURACIÓN DE PÁGINA */
        /* ============================================ */
        @page {
            size: A4;
            margin: 12mm 15mm 20mm 15mm;
        }
        
        @media print {
            .no-print { display: none !important; }
            body { 
                margin: 0; 
                padding: 0;
                font-size: 9pt;
                line-height: 1.3;
                padding-bottom: 30mm;
            }
            .header-section { page-break-after: avoid; }
            
            /* Pie de página fijo en todas las páginas */
            .footer-observaciones {
                position: fixed;
                bottom: 20mm;
                left: 0;
                right: 0;
                padding: 8px 15mm;
                border-top: 2px solid #dfe6e9;
                background: #f8f9fa;
                font-size: 8pt;
                line-height: 1.4;
                color: #636e72;
                text-align: center;
            }
            
            .footer-page {
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                height: 20mm;
                padding: 8px 15mm;
                border-top: 2px solid #dfe6e9;
                background: #f8f9fa;
                font-size: 7.5pt;
                line-height: 1.4;
                color: #636e72;
                text-align: center;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
            }
            
            /* Numeración de páginas */
            .page-number {
                position: fixed;
                bottom: 3mm;
                right: 15mm;
                font-size: 7pt;
                font-weight: 600;
                color: #2c3e50;
            }
            
            .page-number::after {
                counter-increment: page;
                content: "Página " counter(page);
            }
        }
        
        /* ============================================ */
        /* ESTILOS GENERALES */
        /* ============================================ */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            font-size: 9.5pt;
            color: #2c3e50;
            line-height: 1.35;
            padding: 5px 10px;
            background: #fff;
        }
        
        /* ============================================ */
        /* BOTÓN DE IMPRESIÓN */
        /* ============================================ */
        .print-button {
            position: fixed;
            top: 15px;
            right: 15px;
            padding: 10px 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
            z-index: 9999;
            transition: all 0.3s ease;
        }
        
        .print-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
        }
        
        /* ============================================ */
        /* HEADER PRINCIPAL */
        /* ============================================ */
        .header-section {
            border-bottom: 3px solid #2c3e50;
            padding-bottom: 8px;
            margin-bottom: 10px;
            display: grid;
            grid-template-columns: 200px 1fr auto;
            gap: 15px;
            align-items: start;
        }
        
        /* Logo */
        .logo-box {
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 5px;
        }
        
        .logo-box img {
            max-width: 190px;
            max-height: 80px;
            width: 100%;
            height: auto;
            display: block;
            object-fit: contain;
        }
        
        .logo-box .empresa-nombre {
            font-size: 13pt;
            font-weight: 700;
            color: #2c3e50;
            margin-top: 5px;
            line-height: 1.2;
            display: block;
        }
        
        /* Datos Empresa */
        .empresa-datos {
            font-size: 8.5pt;
            line-height: 1.4;
        }
        
        .empresa-datos .nombre-comercial {
            font-size: 14pt;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 3px;
        }
        
        .empresa-datos .nif {
            font-weight: 600;
            color: #e74c3c;
            margin-bottom: 2px;
        }
        
        .empresa-datos p {
            margin: 1px 0;
            color: #34495e;
        }
        
        /* Info Presupuesto Destacada */
        .presupuesto-box {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 8px 12px;
            border-radius: 6px;
            text-align: right;
            min-width: 200px;
        }
        
        .presupuesto-box .titulo {
            font-size: 10pt;
            font-weight: 600;
            margin-bottom: 4px;
            opacity: 0.9;
        }
        
        .presupuesto-box .numero {
            font-size: 18pt;
            font-weight: 700;
            margin-bottom: 2px;
            letter-spacing: 0.5px;
        }
        
        .presupuesto-box .version {
            font-size: 8pt;
            opacity: 0.85;
            margin-bottom: 4px;
        }
        
        .presupuesto-box .pedido-cliente {
            font-size: 8.5pt;
            background: rgba(255,255,255,0.15);
            padding: 3px 6px;
            border-radius: 3px;
            margin-bottom: 4px;
        }
        
        .presupuesto-box .fecha {
            font-size: 8.5pt;
            border-top: 1px solid rgba(255,255,255,0.3);
            padding-top: 4px;
            margin-top: 4px;
        }
        
        /* ============================================ */
        /* GRID DE INFORMACIÓN COMPACTA */
        /* ============================================ */
        .info-grid-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
            margin-bottom: 12px;
        }
        
        .info-box {
            border: 1px solid #dfe6e9;
            border-radius: 4px;
            padding: 6px 10px;
            background: #f8f9fa;
        }
        
        .info-box .titulo-box {
            font-size: 8.5pt;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 4px;
            padding-bottom: 3px;
            border-bottom: 2px solid #3498db;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        
        .info-box.cliente .titulo-box {
            border-bottom-color: #27ae60;
        }
        
        .info-box.contacto .titulo-box {
            border-bottom-color: #9b59b6;
        }
        
        .info-box.pago .titulo-box {
            border-bottom-color: #27ae60;
        }
        
        .info-box.evento .titulo-box {
            border-bottom-color: #f39c12;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            margin: 2px 0;
            font-size: 8.5pt;
            line-height: 1.3;
        }
        
        .info-row .label {
            font-weight: 600;
            color: #7f8c8d;
            flex: 0 0 auto;
            margin-right: 8px;
        }
        
        .info-row .value {
            color: #2c3e50;
            font-weight: 500;
            text-align: right;
            flex: 1;
        }
        
        .evento-nombre {
            background: #fff3cd;
            border-left: 4px solid #f39c12;
            padding: 5px 8px;
            margin: 4px 0;
            font-weight: 600;
            color: #856404;
            font-size: 9pt;
        }
        
        /* ============================================ */
        /* UTILIDADES */
        /* ============================================ */
        .text-muted {
            color: #95a5a6;
            font-style: italic;
        }
        
        strong {
            font-weight: 600;
        }
        
        /* ============================================ */
        /* CONTENEDOR PRIMERA PÁGINA */
        /* ============================================ */
        .first-page-only {
            /* Sin salto de página forzado - fluye naturalmente */
        }
        
        /* ============================================ */
        /* PIE DE PÁGINA */
        /* ============================================ */
        .footer-page {
            margin-top: 20px;
            padding: 10px 15px;
            border-top: 2px solid #dfe6e9;
            background: #f8f9fa;
            font-size: 8pt;
            line-height: 1.4;
            color: #636e72;
            text-align: center;
        }
    </style>
</head>
<body>

    <!-- Botón de impresión -->
    <button class="print-button no-print" onclick="window.print()">
        🖨️ Imprimir / PDF
    </button>

    <!-- ============================================ -->
    <!-- HEADER PRINCIPAL -->
    <!-- ============================================ -->
    <div class="header-section">
        <!-- Logo o nombre -->
        <div class="logo-box">';
            
            if ($mostrar_logo) {
                $html .= '
            <img src="' . htmlspecialchars($url_logo) . '" 
                 alt="' . htmlspecialchars($datos_empresa['nombre_comercial_empresa']) . '" 
                 title="' . htmlspecialchars($datos_empresa['nombre_comercial_empresa']) . '">';
            } else {
                $html .= '
            <div class="empresa-nombre">' . htmlspecialchars($datos_empresa['nombre_comercial_empresa']) . '</div>';
            }
            
            $html .= '
        </div>
        
        <!-- Datos empresa -->
        <div class="empresa-datos">
            <div class="nombre-comercial">' . htmlspecialchars($datos_empresa['nombre_comercial_empresa']) . '</div>
            <p class="nif">NIF: ' . htmlspecialchars($datos_empresa['nif_empresa']) . '</p>
            <p>' . htmlspecialchars($datos_empresa['direccion_fiscal_empresa']) . ', ' . 
                  htmlspecialchars($datos_empresa['cp_fiscal_empresa']) . ' ' . 
                  htmlspecialchars($datos_empresa['poblacion_fiscal_empresa']) . ' (' . 
                  htmlspecialchars($datos_empresa['provincia_fiscal_empresa']) . ')</p>
            <p>Tel: ' . htmlspecialchars($datos_empresa['telefono_empresa']);
            
            if (!empty($datos_empresa['movil_empresa'])) {
                $html .= ' | ' . htmlspecialchars($datos_empresa['movil_empresa']);
            }
            
            $html .= ' | ' . htmlspecialchars($datos_empresa['email_empresa']) . '</p>';
            
            if (!empty($datos_empresa['web_empresa'])) {
                $html .= '
            <p>' . htmlspecialchars($datos_empresa['web_empresa']) . '</p>';
            }
            
            $html .= '
        </div>
        
        <!-- Info presupuesto destacada -->
        <div class="presupuesto-box">
            <div class="titulo">PRESUPUESTO</div>
            <div class="numero">' . htmlspecialchars($datos_presupuesto['numero_presupuesto']) . '</div>
            <div class="version">Versión: ' . htmlspecialchars($datos_presupuesto['numero_version_presupuesto'] ?? '1') . '</div>';
            
            // Mostrar número de pedido del cliente si existe
            if (!empty($datos_presupuesto['numero_pedido_cliente_presupuesto'])) {
                $html .= '
            <div class="pedido-cliente">
                <strong>Ref. Cliente:</strong> ' . htmlspecialchars($datos_presupuesto['numero_pedido_cliente_presupuesto']) . '
            </div>';
            }
            
            $html .= '
            <div class="fecha">
                <strong>Emisión:</strong> ' . htmlspecialchars($fecha_presupuesto) . '<br>
                <strong>Válido:</strong> ' . htmlspecialchars($fecha_validez) . '
            </div>
        </div>
    </div>
    
    <!-- ============================================ -->
    <!-- INFORMACIÓN PRIMERA PÁGINA ÚNICAMENTE -->
    <!-- ============================================ -->
    <div class="first-page-only">
    
    <!-- ============================================ -->
    <!-- GRID DE INFORMACIÓN -->
    <!-- ============================================ -->
    <div class="info-grid-container">
        
        <!-- CLIENTE -->
        <div class="info-box cliente">
            <div class="titulo-box">👤 Cliente</div>
            <div class="info-row">
                <span class="label">Nombre:</span>
                <span class="value">' . htmlspecialchars($datos_presupuesto['nombre_cliente']) . '</span>
            </div>';
            
            if (!empty($datos_presupuesto['nif_cliente'])) {
                $html .= '
            <div class="info-row">
                <span class="label">NIF/CIF:</span>
                <span class="value">' . htmlspecialchars($datos_presupuesto['nif_cliente']) . '</span>
            </div>';
            }
            
            if (!empty($datos_presupuesto['email_cliente'])) {
                $html .= '
            <div class="info-row">
                <span class="label">Email:</span>
                <span class="value">' . htmlspecialchars($datos_presupuesto['email_cliente']) . '</span>
            </div>';
            }
            
            if (!empty($datos_presupuesto['telefono_cliente'])) {
                $html .= '
            <div class="info-row">
                <span class="label">Teléfono:</span>
                <span class="value">' . htmlspecialchars($datos_presupuesto['telefono_cliente']) . '</span>
            </div>';
            }
            
            $html .= '
        </div>
        
        <!-- CONTACTO DEL CLIENTE (si existe) -->';
        
        if (!empty($datos_presupuesto['nombre_contacto_cliente'])) {
            $html .= '
        <div class="info-box contacto">
            <div class="titulo-box">📞 Persona de Contacto</div>
            <div class="info-row">
                <span class="label">Nombre:</span>
                <span class="value">' . 
                    htmlspecialchars($datos_presupuesto['nombre_contacto_cliente']) . ' ' . 
                    htmlspecialchars($datos_presupuesto['apellidos_contacto_cliente'] ?? '') . 
                '</span>
            </div>';
            
            if (!empty($datos_presupuesto['telefono_contacto_cliente'])) {
                $html .= '
            <div class="info-row">
                <span class="label">Teléfono:</span>
                <span class="value">' . htmlspecialchars($datos_presupuesto['telefono_contacto_cliente']) . '</span>
            </div>';
            }
            
            if (!empty($datos_presupuesto['email_contacto_cliente'])) {
                $html .= '
            <div class="info-row">
                <span class="label">Email:</span>
                <span class="value">' . htmlspecialchars($datos_presupuesto['email_contacto_cliente']) . '</span>
            </div>';
            }
            
            $html .= '
        </div>';
        }
        
        $html .= '
        
        <!-- FORMA DE PAGO (si existe) -->';
        
        if (!empty($datos_presupuesto['nombre_pago'])) {
            $html .= '
        <div class="info-box pago">
            <div class="titulo-box">💳 Forma de Pago</div>
            <div class="info-row">
                <span class="label">Método:</span>
                <span class="value">' . htmlspecialchars($datos_presupuesto['nombre_metodo_pago'] ?? 'N/A') . '</span>
            </div>
            <div class="info-row">
                <span class="label">Modalidad:</span>
                <span class="value">' . htmlspecialchars($datos_presupuesto['nombre_pago']) . '</span>
            </div>';
            
            // Mostrar detalles de anticipo
            if (!empty($datos_presupuesto['porcentaje_anticipo_pago'])) {
                $html .= '
            <div class="info-row">
                <span class="label">Anticipo:</span>
                <span class="value">' . 
                    htmlspecialchars($datos_presupuesto['porcentaje_anticipo_pago']) . '%';
                
                if (isset($datos_presupuesto['dias_anticipo_pago'])) {
                    $dias_anticipo = intval($datos_presupuesto['dias_anticipo_pago']);
                    
                    if ($dias_anticipo < 0) {
                        $html .= ' (' . abs($dias_anticipo) . ' días antes del inicio)';
                    } elseif ($dias_anticipo > 0) {
                        $html .= ' (' . $dias_anticipo . ' días después del inicio)';
                    } else {
                        $html .= ' (el día de inicio)';
                    }
                }
                
                $html .= '</span>
            </div>';
            }
            
            // Mostrar detalles de pago final si es fraccionado
            if (!empty($datos_presupuesto['porcentaje_final_pago']) && 
                $datos_presupuesto['porcentaje_anticipo_pago'] < 100) {
                $html .= '
            <div class="info-row">
                <span class="label">Pago final:</span>
                <span class="value">' . 
                    htmlspecialchars($datos_presupuesto['porcentaje_final_pago']) . '%';
                
                if (isset($datos_presupuesto['dias_final_pago'])) {
                    $dias_final = intval($datos_presupuesto['dias_final_pago']);
                    
                    if ($dias_final < 0) {
                        $html .= ' (' . abs($dias_final) . ' días antes del fin)';
                    } elseif ($dias_final > 0) {
                        $html .= ' (' . $dias_final . ' días después del fin)';
                    } else {
                        $html .= ' (el día de fin)';
                    }
                }
                
                $html .= '</span>
            </div>';
            }
            
            // Mostrar descuento si existe
            if (!empty($datos_presupuesto['descuento_pago']) && $datos_presupuesto['descuento_pago'] > 0) {
                $html .= '
            <div class="info-row">
                <span class="label">Descuento:</span>
                <span class="value">' . htmlspecialchars($datos_presupuesto['descuento_pago']) . '%</span>
            </div>';
            }
            
            $html .= '
        </div>';
        }
        
        $html .= '
        
        <!-- DIRECCIÓN CLIENTE (si existe) -->';
        
        if (!empty($datos_presupuesto['direccion_cliente'])) {
            $html .= '
        <div class="info-box cliente">
            <div class="titulo-box">📍 Dirección</div>
            <div class="info-row">
                <span class="value">' . htmlspecialchars($datos_presupuesto['direccion_cliente']) . '</span>
            </div>';
            
            if (!empty($datos_presupuesto['cp_cliente']) || !empty($datos_presupuesto['poblacion_cliente'])) {
                $html .= '
            <div class="info-row">
                <span class="value">' . 
                    htmlspecialchars($datos_presupuesto['cp_cliente'] ?? '') . ' ' . 
                    htmlspecialchars($datos_presupuesto['poblacion_cliente'] ?? '') . 
                '</span>
            </div>';
            }
            
            if (!empty($datos_presupuesto['provincia_cliente'])) {
                $html .= '
            <div class="info-row">
                <span class="value">' . htmlspecialchars($datos_presupuesto['provincia_cliente']) . '</span>
            </div>';
            }
            
            $html .= '
        </div>';
        }
        
        $html .= '
    </div>
    
    <!-- EVENTO -->';
        
        if (!empty($datos_presupuesto['nombre_evento_presupuesto'])) {
            $html .= '
    <div class="info-grid-container">
        <div class="info-box evento">
            <div class="titulo-box">🎯 Datos del Evento</div>
            <div class="evento-nombre">' . htmlspecialchars($datos_presupuesto['nombre_evento_presupuesto']) . '</div>
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 8px; margin-top: 8px;">
                <div class="info-row" style="flex-direction: column; text-align: right;">
                    <span class="label">Inicio</span>
                    <span class="value"><strong>' . htmlspecialchars($fecha_inicio_evento) . '</strong></span>
                </div>
                <div class="info-row" style="flex-direction: column; text-align: right;">
                    <span class="label">Fin</span>
                    <span class="value"><strong>' . htmlspecialchars($fecha_fin_evento) . '</strong></span>
                </div>';
            
            if (!empty($datos_presupuesto['duracion_evento_dias'])) {
                $html .= '
                <div class="info-row" style="flex-direction: column; text-align: right;">
                    <span class="label">Duración</span>
                    <span class="value"><strong>' . htmlspecialchars($datos_presupuesto['duracion_evento_dias']) . ' días</strong></span>
                </div>';
            }
            
            $html .= '
            </div>
        </div>';
            
            // Dirección del evento si existe
            if (!empty($datos_presupuesto['direccion_evento_presupuesto']) || 
                !empty($datos_presupuesto['poblacion_evento_presupuesto'])) {
                $html .= '
        <div class="info-box evento">
            <div class="titulo-box">📍 Ubicación del Evento</div>';
                
                if (!empty($datos_presupuesto['direccion_evento_presupuesto'])) {
                    $html .= '
            <div class="info-row">
                <span class="value">' . htmlspecialchars($datos_presupuesto['direccion_evento_presupuesto']) . '</span>
            </div>';
                }
                
                if (!empty($datos_presupuesto['cp_evento_presupuesto']) || 
                    !empty($datos_presupuesto['poblacion_evento_presupuesto'])) {
                    $html .= '
            <div class="info-row">
                <span class="value">' . 
                        htmlspecialchars($datos_presupuesto['cp_evento_presupuesto'] ?? '') . ' ' . 
                        htmlspecialchars($datos_presupuesto['poblacion_evento_presupuesto'] ?? '') . 
                '</span>
            </div>';
                }
                
                if (!empty($datos_presupuesto['provincia_evento_presupuesto'])) {
                    $html .= '
            <div class="info-row">
                <span class="value">' . htmlspecialchars($datos_presupuesto['provincia_evento_presupuesto']) . '</span>
            </div>';
                }
                
                $html .= '
        </div>';
            }
            
            $html .= '
    </div>';
        }
        
        $html .= '
    
    </div><!-- fin first-page-only -->
    ';
    
    // OBSERVACIONES DE CABECERA (en todas las páginas)
    if (!empty($datos_presupuesto['observaciones_cabecera_presupuesto'])) {
        $html .= '
    <div style="margin: 12px 0; font-size: 9pt; line-height: 1.5; color: #2c3e50;">' . 
        nl2br(htmlspecialchars($datos_presupuesto['observaciones_cabecera_presupuesto'])) . 
    '</div>';
    }
        
        $html .= '

    <!-- ============================================ -->
    <!-- PIE DE PÁGINA -->
    <!-- ============================================ -->';
    
    // Añadir observaciones de pie del presupuesto (fijo en todas las páginas)
    if (!empty($datos_presupuesto['observaciones_pie_presupuesto'])) {
        $html .= '
    <div class="footer-observaciones">' . 
            nl2br(htmlspecialchars($datos_presupuesto['observaciones_pie_presupuesto'])) . 
    '</div>';
    }
    
    // Añadir pie de página si existe texto_pie_presupuesto_empresa
    if (!empty($datos_empresa['texto_pie_presupuesto_empresa'])) {
        $html .= '
    <div class="footer-page">' . 
            nl2br(htmlspecialchars($datos_empresa['texto_pie_presupuesto_empresa'])) . 
    '</div>';
    }
    
    // Añadir numeración de páginas
    $html .= '
    <div class="page-number"></div>';
    
    $html .= '

</body>
</html>';
            
            // 9. Enviar HTML al navegador
            echo $html;
            
        } catch (Exception $e) {
            // Registrar error
            $registro->registrarActividad(
                'admin',
                'impresionpresupuesto.php',
                'cli_esp',
                "Error al generar presupuesto: " . $e->getMessage(),
                'error'
            );
            
            // Mostrar error al usuario
            echo '<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Error</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f8f9fa;
        }
        .error-container {
            background-color: white;
            border: 2px solid #dc3545;
            border-radius: 8px;
            padding: 30px;
            max-width: 500px;
            text-align: center;
        }
        .error-container h1 {
            color: #dc3545;
            margin-bottom: 20px;
        }
        .error-container p {
            color: #333;
            line-height: 1.6;
        }
        .error-container button {
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #dc3545;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .error-container button:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <h1>❌ Error al generar presupuesto</h1>
        <p>' . htmlspecialchars($e->getMessage()) . '</p>
        <button onclick="window.close()">Cerrar ventana</button>
    </div>
</body>
</html>';
        }
        break;
    
    default:
        // Operación no válida
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Operación no válida'
        ], JSON_UNESCAPED_UNICODE);
        break;
}
?>
