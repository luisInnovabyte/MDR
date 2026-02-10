<?php

require_once __DIR__ . "/../config/conexion.php";
require_once __DIR__ . "/../config/funciones.php";
require_once __DIR__ . "/../models/ImpresionPresupuesto.php";
require_once __DIR__ . "/../models/Kit.php";

// Inicializar clases
$registro = new RegistroActividad();
$impresion = new ImpresionPresupuesto();
$kitModel = new Kit();

// Switch principal basado en operación
switch ($_GET["op"]) {
    
    /**
     * Generar presupuesto para cliente final en español - MODELO 2
     * 
     * MODELO 2: Diseño alternativo (pendiente de personalización)
     * Actualmente es copia del MODELO 1
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
                    'impresionpresupuesto_m2_es.php',
                    'cli_esp',
                    "[MODELO 2] Ruta logo desde BD: " . $datos_empresa['logotipo_empresa'],
                    'info'
                );
                
                if ($impresion->validar_logo($datos_empresa['logotipo_empresa'])) {
                    $mostrar_logo = true;
                    $url_logo = $impresion->get_url_logo($datos_empresa['logotipo_empresa']);
                    
                    $registro->registrarActividad(
                        'admin',
                        'impresionpresupuesto_m2_es.php',
                        'cli_esp',
                        "[MODELO 2] Logo validado. URL generada: $url_logo",
                        'info'
                    );
                } else {
                    $registro->registrarActividad(
                        'admin',
                        'impresionpresupuesto_m2_es.php',
                        'cli_esp',
                        "[MODELO 2] Logo NO validado para ruta: " . $datos_empresa['logotipo_empresa'],
                        'warning'
                    );
                }
            } else {
                $registro->registrarActividad(
                    'admin',
                    'impresionpresupuesto_m2_es.php',
                    'cli_esp',
                    "[MODELO 2] No hay logotipo_empresa en la BD",
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
            
            // 6. Obtener líneas del presupuesto
            $lineas = $impresion->get_lineas_impresion($id_presupuesto);
            
            if ($lineas === false) {
                throw new Exception("Error al obtener las líneas del presupuesto");
            }
            
            // 7. Agrupar líneas por fecha de inicio y luego por ubicación
            $lineas_agrupadas = [];
            $totales_generales = [
                'subtotal' => 0,
                'total_iva' => 0,
                'total' => 0
            ];
            
            // Desglose de IVA por porcentaje
            $desglose_iva = [];
            
            foreach ($lineas as $linea) {
                $fecha_inicio = $linea['fecha_inicio_linea_ppto'];
                $id_ubicacion = $linea['id_ubicacion'] ?? 0;
                
                // Inicializar fecha si no existe
                if (!isset($lineas_agrupadas[$fecha_inicio])) {
                    $lineas_agrupadas[$fecha_inicio] = [
                        'ubicaciones' => [],
                        'subtotal_fecha' => 0,
                        'total_iva_fecha' => 0,
                        'total_fecha' => 0
                    ];
                }
                
                // Inicializar ubicación si no existe
                if (!isset($lineas_agrupadas[$fecha_inicio]['ubicaciones'][$id_ubicacion])) {
                    $lineas_agrupadas[$fecha_inicio]['ubicaciones'][$id_ubicacion] = [
                        'nombre_ubicacion' => $linea['nombre_ubicacion'] ?? 'Sin ubicación',
                        'ubicacion_completa' => $linea['ubicacion_completa_agrupacion'] ?? '',
                        'lineas' => [],
                        'subtotal_ubicacion' => 0,
                        'total_iva_ubicacion' => 0,
                        'total_ubicacion' => 0
                    ];
                }
                
                // Añadir línea al grupo
                $lineas_agrupadas[$fecha_inicio]['ubicaciones'][$id_ubicacion]['lineas'][] = $linea;
                
                // Acumular subtotales de ubicación
                $base = floatval($linea['base_imponible'] ?? 0);
                $iva = floatval($linea['importe_iva'] ?? 0);
                $total = floatval($linea['total_linea'] ?? 0);
                
                $lineas_agrupadas[$fecha_inicio]['ubicaciones'][$id_ubicacion]['subtotal_ubicacion'] += $base;
                $lineas_agrupadas[$fecha_inicio]['ubicaciones'][$id_ubicacion]['total_iva_ubicacion'] += $iva;
                $lineas_agrupadas[$fecha_inicio]['ubicaciones'][$id_ubicacion]['total_ubicacion'] += $total;
                
                // Acumular subtotales de fecha
                $lineas_agrupadas[$fecha_inicio]['subtotal_fecha'] += $base;
                $lineas_agrupadas[$fecha_inicio]['total_iva_fecha'] += $iva;
                $lineas_agrupadas[$fecha_inicio]['total_fecha'] += $total;
                
                // Acumular totales generales
                $totales_generales['subtotal'] += $base;
                $totales_generales['total_iva'] += $iva;
                $totales_generales['total'] += $total;
                
                // Agrupar por porcentaje de IVA para desglose
                $porcentaje_iva = floatval($linea['porcentaje_iva_linea_ppto'] ?? 0);
                if (!isset($desglose_iva[$porcentaje_iva])) {
                    $desglose_iva[$porcentaje_iva] = [
                        'base_imponible' => 0,
                        'importe_iva' => 0
                    ];
                }
                $desglose_iva[$porcentaje_iva]['base_imponible'] += $base;
                $desglose_iva[$porcentaje_iva]['importe_iva'] += $iva;
            }
            
            // 8. Obtener observaciones de familias/artículos
            $observaciones = $impresion->get_observaciones_presupuesto($id_presupuesto, 'es');
            
            // 9. Registrar actividad
            $registro->registrarActividad(
                'admin',
                'impresionpresupuesto_m2_es.php',
                'cli_esp',
                "[MODELO 2] Generando impresión para presupuesto: " . $datos_presupuesto['numero_presupuesto'] . 
                " (Versión: " . $datos_presupuesto['numero_version_presupuesto'] . ") - " . count($lineas) . " líneas, " . count($observaciones) . " observaciones",
                'info'
            );
            
            // 9. Configurar headers para HTML
            header('Content-Type: text/html; charset=utf-8');
            
            // 10. Generar HTML
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
            margin: 0;
        }
        
        @media print {
            .no-print { display: none !important; }
            
            body {
                margin: 0;
                padding: 0;
                font-size: 9pt;
                line-height: 1.3;
                counter-reset: page;
            }
            
            .header-section { page-break-after: avoid; }
            
            /* Footers al final del contenido (última página) - con espaciado visual mejorado */
            .footer-observaciones {
                margin-top: 30px;
                padding: 12px 15px;
                border-top: 3px solid #2c3e50;
                border-bottom: 1px solid #dfe6e9;
                background: #f8f9fa;
                font-size: 8pt;
                line-height: 1.4;
                color: #636e72;
                text-align: center;
                page-break-inside: avoid;
            }
            
            .footer-page {
                padding: 12px 15px;
                border-top: 3px solid #2c3e50;
                background: #f8f9fa;
                font-size: 8pt;
                line-height: 1.4;
                color: #636e72;
                text-align: center;
                margin-bottom: 15mm;
                page-break-inside: avoid;
            }
            
            /* Número de página al final del documento (última página) */
            .page-number {
                text-align: right;
                font-size: 9pt;
                font-weight: 600;
                color: #2c3e50;
                background: white;
                padding: 3px 8px;
                margin-top: 5px;
                border-radius: 3px;
                /* JavaScript maneja el contenido */
            }
        }
        
        /* Contador de páginas para vista en pantalla */
        .page-number {
            text-align: right;
            font-size: 9pt;
            font-weight: 600;
            color: #2c3e50;
            background: white;
            padding: 5px 10px;
            margin-top: 5px;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            /* JavaScript maneja el contenido */
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
            padding-bottom: 10px;
            margin-bottom: 10px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            align-items: start;
        }
        
        /* Columna Izquierda - Empresa */
        .columna-izquierda {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        
        /* Logo */
        .logo-box {
            text-align: left;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            padding: 5px;
            margin-bottom: 5px;
        }
        
        .logo-box img {
            max-width: 200px;
            max-height: 90px;
            width: auto;
            height: auto;
            display: block;
            object-fit: contain;
        }
        
        .logo-box .empresa-nombre {
            font-size: 14pt;
            font-weight: 700;
            color: #2c3e50;
            line-height: 1.2;
            display: block;
        }
        
        /* Datos Empresa */
        .empresa-datos {
            font-size: 8.5pt;
            line-height: 1.4;
            padding: 8px;
            background: #f8f9fa;
            border-radius: 4px;
            border: 1px solid #dfe6e9;
        }
        
        .empresa-datos .nombre-comercial {
            font-size: 11pt;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 4px;
        }
        
        .empresa-datos .nif {
            font-weight: 600;
            color: #e74c3c;
            margin-bottom: 3px;
        }
        
        .empresa-datos p {
            margin: 2px 0;
            color: #34495e;
        }
        
        /* Info Presupuesto (debajo del logo) - EN LÍNEA HORIZONTAL */
        .presupuesto-info {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 8px 10px;
            border-radius: 6px;
            font-size: 7.5pt;
            line-height: 1.3;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            align-items: center;
        }
        
        .presupuesto-info .info-linea {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            white-space: nowrap;
            padding: 0 6px;
            border-right: 1px solid rgba(255,255,255,0.3);
        }
        
        .presupuesto-info .info-linea:last-child {
            border-right: none;
        }
        
        .presupuesto-info .label {
            font-weight: 600;
            opacity: 0.9;
        }
        
        .presupuesto-info .valor {
            font-weight: 700;
            font-size: 8pt;
        }
        
        /* Columna Derecha - Cliente */
        .columna-derecha {
            display: flex;
            flex-direction: column;
        }
        
        .cliente-box {
            background: #f8f9fa;
            border: 2px solid #27ae60;
            border-radius: 6px;
            padding: 10px;
            height: 100%;
        }
        
        .cliente-box .titulo-seccion {
            font-size: 9pt;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 6px;
            padding-bottom: 4px;
            border-bottom: 2px solid #27ae60;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        
        .cliente-box .datos-cliente {
            margin-bottom: 6px;
        }
        
        .cliente-box .info-row {
            display: flex;
            margin: 2px 0;
            font-size: 8pt;
            line-height: 1.3;
        }
        
        .cliente-box .info-row .label {
            font-weight: 600;
            color: #7f8c8d;
            min-width: 75px;
            flex-shrink: 0;
        }
        
        .cliente-box .info-row .value {
            color: #2c3e50;
            font-weight: 500;
            word-break: break-word;
        }
        
        .cliente-box .atencion-de {
            padding-top: 6px;
            border-top: 1px solid #ddd;
        }
        
        .cliente-box .atencion-de .titulo-atencion {
            font-size: 8pt;
            font-weight: 700;
            color: #9b59b6;
            margin-bottom: 4px;
        }
        
        /* Observación de cabecera */
        .observacion-cabecera {
            margin: 15px 0;
            padding: 12px;
            background: #fff3cd;
            border-left: 4px solid #f39c12;
            border-radius: 4px;
        }
        
        .observacion-cabecera .titulo-obs {
            font-size: 9pt;
            font-weight: 600;
            color: #856404;
            margin-bottom: 5px;
        }
        
        .observacion-cabecera .texto-obs {
            font-size: 8.5pt;
            color: #856404;
            line-height: 1.5;
            white-space: pre-wrap;
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
        
        /* ============================================ */
        /* TABLA DE LÍNEAS DEL PRESUPUESTO */
        /* ============================================ */
        .lineas-section {
            margin-top: 15px;
            /* Permitir que las líneas fluyan naturalmente entre páginas */
        }
        
        .fecha-header {
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            color: white;
            padding: 6px 10px;
            margin: 12px 0 6px 0;
            border-radius: 4px;
            font-size: 10pt;
            font-weight: 700;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .ubicacion-header {
            background: #e8f4f8;
            border-left: 4px solid #3498db;
            padding: 5px 10px;
            margin: 8px 0 4px 0;
            font-size: 9pt;
            font-weight: 600;
            color: #2c3e50;
        }
        
        .ubicacion-header .codigo {
            color: #7f8c8d;
            font-size: 8pt;
            margin-left: 8px;
        }
        
        .lineas-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
            font-size: 8pt;
        }
        
        .lineas-table thead {
            background: #34495e;
            color: white;
        }
        
        .lineas-table th {
            padding: 5px 4px;
            text-align: left;
            font-weight: 600;
            font-size: 7.5pt;
            border: 1px solid #2c3e50;
        }
        
        .lineas-table th.text-center {
            text-align: center;
        }
        
        .lineas-table th.text-right {
            text-align: right;
        }
        
        .lineas-table tbody tr {
            border-bottom: 1px solid #dfe6e9;
        }
        
        .lineas-table tbody tr:hover {
            background: #f8f9fa;
        }
        
        .lineas-table td {
            padding: 4px;
            font-size: 7.5pt;
            border: 1px solid #dfe6e9;
        }
        
        .lineas-table td.text-center {
            text-align: center;
        }
        
        .lineas-table td.text-right {
            text-align: right;
        }
        
        .subtotal-row {
            background: #ecf0f1;
            font-weight: 600;
        }
        
        .subtotal-row td {
            padding: 5px 4px;
            border-top: 2px solid #95a5a6;
        }
        
        .total-fecha-row {
            background: #d5dbdb;
            font-weight: 700;
        }
        
        .total-fecha-row td {
            padding: 6px 4px;
            border-top: 2px solid #7f8c8d;
        }
        
        /* Tabla de desglose de IVA */
        .desglose-iva-section {
            margin-top: 15px;
            margin-bottom: 10px;
        }
        
        .desglose-iva-table {
            width: 100%;
            max-width: 500px;
            margin-left: auto;
            border-collapse: collapse;
            font-size: 8.5pt;
        }
        
        .desglose-iva-table thead th {
            background: #34495e;
            color: white;
            padding: 6px 8px;
            text-align: center;
            font-weight: 600;
            border: 1px solid #2c3e50;
        }
        
        .desglose-iva-table tbody td {
            padding: 5px 8px;
            border: 1px solid #bdc3c7;
            text-align: right;
        }
        
        .desglose-iva-table tbody td:first-child {
            text-align: center;
        }
        
        .desglose-iva-table tbody tr:nth-child(odd) {
            background: #ecf0f1;
        }
        
        .desglose-iva-table tfoot td {
            background: #95a5a6;
            color: white;
            font-weight: 700;
            padding: 6px 8px;
            border: 1px solid #7f8c8d;
            text-align: right;
        }
        
        .desglose-iva-table tfoot td:first-child {
            text-align: center;
        }
        
        .total-general-section {
            margin-top: 15px;
            border-top: 3px solid #2c3e50;
            padding-top: 10px;
        }
        
        /* Forma de pago en formato texto */
        .forma-pago-texto {
            margin-top: 20px;
            padding: 12px 15px;
            background: #f8f9fa;
            border-left: 4px solid #3498db;
            border-radius: 4px;
            font-size: 9pt;
            line-height: 1.6;
            color: #2c3e50;
        }
        
        .forma-pago-texto .titulo-forma-pago {
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 5px;
            font-size: 9.5pt;
        }
        
        .forma-pago-texto .texto-forma-pago {
            font-weight: 500;
        }
        
        /* Forma de pago en formato texto */
        .forma-pago-texto {
            margin-top: 20px;
            padding: 12px 15px;
            background: #f8f9fa;
            border-left: 4px solid #3498db;
            border-radius: 4px;
            font-size: 9pt;
            line-height: 1.6;
            color: #2c3e50;
        }
        
        .forma-pago-texto .titulo-forma-pago {
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 5px;
            font-size: 9.5pt;
        }
        
        .forma-pago-texto .texto-forma-pago {
            font-weight: 500;
        }
        
        .total-general-table {
            width: 100%;
            max-width: 400px;
            margin-left: auto;
            border-collapse: collapse;
            font-size: 9pt;
        }
        
        .total-general-table td {
            padding: 5px 10px;
            border: 1px solid #dfe6e9;
        }
        
        .total-general-table td.label {
            font-weight: 600;
            text-align: right;
            background: #ecf0f1;
            width: 60%;
        }
        
        .total-general-table td.valor {
            text-align: right;
            font-weight: 700;
        }
        
        .total-general-table tr.total-final td {
            background: linear-gradient(135deg, #27ae60 0%, #229954 100%);
            color: white;
            font-size: 11pt;
            padding: 8px 10px;
        }
        
        /* Observaciones de familias y artículos */
        .observaciones-familias-articulos-section {
            margin-top: 20px;
            margin-bottom: 20px;
            padding: 15px;
            background: #fafafa;
            border-left: 3px solid #e0e0e0;
            page-break-inside: avoid;
        }
        
        .titulo-observaciones {
            font-size: 9.5pt;
            font-weight: 600;
            color: #424242;
            margin: 0 0 12px 0;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        
        .observacion-item {
            margin-bottom: 10px;
            padding: 10px 12px;
            background: white;
            border-radius: 2px;
            border-left: 2px solid #e8e8e8;
        }
        
        .observacion-item:last-child {
            margin-bottom: 0;
        }
        
        .observacion-tipo {
            font-weight: 600;
            font-size: 8pt;
            color: #757575;
            margin-bottom: 4px;
            text-transform: capitalize;
        }
        
        .observacion-texto {
            font-size: 8pt;
            color: #616161;
            line-height: 1.5;
        }
        
        @media print {
            /* Evitar que encabezados queden huérfanos al final de página */
            .fecha-header {
                page-break-after: avoid;
                page-break-inside: avoid;
            }
            
            .ubicacion-header {
                page-break-after: avoid;
                page-break-inside: avoid;
            }
            
            /* Evitar que una fila de la tabla quede partida entre páginas */
            .lineas-table tr {
                page-break-inside: avoid;
            }
            
            /* Mantener subtotales con su tabla */
            .subtotal-row,
            .total-fecha-row {
                page-break-before: avoid;
            }
            
            /* Mantener tabla de desglose de IVA unida */
            .desglose-iva-section {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>

    <!-- Botón de impresión -->
    <button class="print-button no-print" onclick="window.print()">
        🖨️ Imprimir / PDF
    </button>

    <!-- Contenedor principal que crece para llenar espacio -->
    <div class="main-content">

    <!-- ============================================ -->
    <!-- HEADER PRINCIPAL -->
    <!-- ============================================ -->
    <div class="header-section">
        
        <!-- COLUMNA IZQUIERDA: Empresa + Info Presupuesto -->
        <div class="columna-izquierda">
            
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
            
            <!-- Info presupuesto -->
            <div class="presupuesto-info">
                <div class="info-linea">
                    <span class="label">Nº Presupuesto:</span>
                    <span class="valor">' . htmlspecialchars($datos_presupuesto['numero_presupuesto']) . '</span>
                </div>
                <div class="info-linea">
                    <span class="label">Fecha:</span>
                    <span class="valor">' . htmlspecialchars($fecha_presupuesto) . '</span>
                </div>
                <div class="info-linea">
                    <span class="label">Válido hasta:</span>
                    <span class="valor">' . htmlspecialchars($fecha_validez) . '</span>
                </div>
                <div class="info-linea">
                    <span class="label">Versión:</span>
                    <span class="valor">' . htmlspecialchars($datos_presupuesto['numero_version_presupuesto'] ?? '1') . '</span>
                </div>';
            
            // Mostrar número de pedido del cliente si existe
            if (!empty($datos_presupuesto['numero_pedido_cliente_presupuesto'])) {
                $html .= '
                <div class="info-linea">
                    <span class="label">Ref. Cliente:</span>
                    <span class="valor">' . htmlspecialchars($datos_presupuesto['numero_pedido_cliente_presupuesto']) . '</span>
                </div>';
            }
            
            $html .= '
            </div>
            
        </div>
        
        <!-- COLUMNA DERECHA: Cliente + A la atención de -->
        <div class="columna-derecha">
            <div class="cliente-box">
                <div class="titulo-seccion">👤 Cliente</div>
                
                <!-- Datos del cliente -->
                <div class="datos-cliente">
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
            
            if (!empty($datos_presupuesto['direccion_cliente'])) {
                $direccion_completa = htmlspecialchars($datos_presupuesto['direccion_cliente']);
                
                // Añadir CP, población y provincia si existen
                $partes_direccion = [];
                if (!empty($datos_presupuesto['cp_cliente'])) {
                    $partes_direccion[] = htmlspecialchars($datos_presupuesto['cp_cliente']);
                }
                if (!empty($datos_presupuesto['poblacion_cliente'])) {
                    $partes_direccion[] = htmlspecialchars($datos_presupuesto['poblacion_cliente']);
                }
                if (!empty($datos_presupuesto['provincia_cliente'])) {
                    $partes_direccion[] = htmlspecialchars($datos_presupuesto['provincia_cliente']);
                }
                
                if (!empty($partes_direccion)) {
                    $direccion_completa .= ', ' . implode(' - ', $partes_direccion);
                }
                
                $html .= '
                    <div class="info-row">
                        <span class="label">Dirección:</span>
                        <span class="value">' . $direccion_completa . '</span>
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
                </div>';
        
        // A la atención de (persona de contacto)
        if (!empty($datos_presupuesto['nombre_contacto_cliente'])) {
            $html .= '
                
                <!-- A la atención de -->
                <div class="atencion-de">
                    <div class="titulo-atencion">A la atención de:</div>
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
            </div>
        </div>
        
    </div>
    
    <!-- ============================================ -->
    <!-- OBSERVACIÓN DE CABECERA -->
    <!-- ============================================ -->';
    
    if (!empty($datos_presupuesto['observaciones_cliente_presupuesto'])) {
        $html .= '
    <div class="observacion-cabecera">
        <div class="titulo-obs">📝 Observaciones:</div>
        <div class="texto-obs">' . nl2br(htmlspecialchars($datos_presupuesto['observaciones_cliente_presupuesto'])) . '</div>
    </div>';
    }
    
    $html .= '
    
    <!-- ============================================ -->
    <!-- INFORMACIÓN PRIMERA PÁGINA ÚNICAMENTE -->
    <!-- ============================================ -->
    <div class="first-page-only">
    
    <!-- ============================================ -->
    <!-- GRID DE INFORMACIÓN -->
    <!-- ============================================ -->
    
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
    
    // ============================================
    // SECCIÓN DE LÍNEAS DEL PRESUPUESTO
    // ============================================
    if (!empty($lineas_agrupadas)) {
        $html .= '
    
    <!-- ============================================ -->
    <!-- DETALLE DE LÍNEAS DEL PRESUPUESTO -->
    <!-- ============================================ -->
    <div class="lineas-section">';
        
        // Recorrer por fecha de inicio
        foreach ($lineas_agrupadas as $fecha => $datos_fecha) {
            $fecha_formateada = !empty($fecha) ? date('d/m/Y', strtotime($fecha)) : 'Sin fecha';
            
            $html .= '
        
        <!-- Encabezado de fecha -->
        <div class="fecha-header">
            <span>📅 Fecha de inicio: ' . htmlspecialchars($fecha_formateada) . '</span>
        </div>';
            
            // Recorrer por ubicación dentro de cada fecha
            foreach ($datos_fecha['ubicaciones'] as $id_ubicacion => $datos_ubicacion) {
                $nombre_ubicacion = $datos_ubicacion['nombre_ubicacion'];
                $ubicacion_completa = $datos_ubicacion['ubicacion_completa'];
                
                $html .= '
        
        <!-- Encabezado de ubicación -->
        <div class="ubicacion-header">
            📍 ' . htmlspecialchars($nombre_ubicacion);
                
                if (!empty($id_ubicacion) && $id_ubicacion > 0) {
                    $html .= ' <span class="codigo">(ID: ' . htmlspecialchars($id_ubicacion) . ')</span>';
                }
                
                $html .= '
        </div>
        
        <!-- Tabla de líneas -->
        <table class="lineas-table">
            <thead>
                <tr>
                    <th style="width: 7%;">Fecha Inicio</th>
                    <th style="width: 7%;">Fecha Fin</th>
                    <th style="width: 7%;">Montaje</th>
                    <th style="width: 7%;">Desmontaje</th>
                    <th class="text-center" style="width: 5%;">Días</th>
                    <th class="text-center" style="width: 5%;">Coef.</th>
                    <th style="width: 25%;">Descripción</th>
                    <th class="text-center" style="width: 5%;">Cant.</th>
                    <th class="text-right" style="width: 8%;">P. Unit.</th>
                    <th class="text-center" style="width: 5%;">%Dto</th>
                    <th class="text-right" style="width: 19%;">Importe(€)</th>
                </tr>
            </thead>
            <tbody>';
                
                // Recorrer líneas de la ubicación
                foreach ($datos_ubicacion['lineas'] as $linea) {
                    $fecha_inicio_linea = !empty($linea['fecha_inicio_linea_ppto']) 
                        ? date('d/m/Y', strtotime($linea['fecha_inicio_linea_ppto'])) 
                        : '-';
                    
                    $fecha_fin_linea = !empty($linea['fecha_fin_linea_ppto']) 
                        ? date('d/m/Y', strtotime($linea['fecha_fin_linea_ppto'])) 
                        : '-';
                    
                    $fecha_montaje_linea = !empty($linea['fecha_montaje_linea_ppto']) 
                        ? date('d/m/Y', strtotime($linea['fecha_montaje_linea_ppto'])) 
                        : '-';
                    
                    $fecha_desmontaje_linea = !empty($linea['fecha_desmontaje_linea_ppto']) 
                        ? date('d/m/Y', strtotime($linea['fecha_desmontaje_linea_ppto'])) 
                        : '-';
                    
                    $jornadas = !empty($linea['dias_linea']) ? intval($linea['dias_linea']) : 0;
                    $coeficiente = !empty($linea['valor_coeficiente_linea_ppto']) 
                        ? number_format(floatval($linea['valor_coeficiente_linea_ppto']), 2, ',', '.') 
                        : '1,00';
                    
                    $descripcion = !empty($linea['nombre_articulo']) 
                        ? $linea['nombre_articulo'] 
                        : $linea['descripcion_linea_ppto'] ?? '';
                    
                    $cantidad = !empty($linea['cantidad_linea_ppto']) 
                        ? number_format(floatval($linea['cantidad_linea_ppto']), 0, ',', '.') 
                        : '0';
                    
                    $precio_unitario = !empty($linea['precio_unitario_linea_ppto']) 
                        ? number_format(floatval($linea['precio_unitario_linea_ppto']), 2, ',', '.') . ' €' 
                        : '0,00 €';
                    
                    $descuento = !empty($linea['descuento_linea_ppto']) 
                        ? number_format(floatval($linea['descuento_linea_ppto']), 2, ',', '.') 
                        : '0,00';
                    
                    $iva = !empty($linea['porcentaje_iva_linea_ppto']) 
                        ? number_format(floatval($linea['porcentaje_iva_linea_ppto']), 2, ',', '.') 
                        : '0,00';
                    
                    $base_imponible = !empty($linea['base_imponible']) 
                        ? number_format(floatval($linea['base_imponible']), 2, ',', '.') . ' €' 
                        : '0,00 €';
                    
                    $total_linea = !empty($linea['total_linea']) 
                        ? number_format(floatval($linea['total_linea']), 2, ',', '.') . ' €' 
                        : '0,00 €';
                    
                    $html .= '
                <tr>
                    <td>' . htmlspecialchars($fecha_inicio_linea) . '</td>
                    <td>' . htmlspecialchars($fecha_fin_linea) . '</td>
                    <td>' . htmlspecialchars($fecha_montaje_linea) . '</td>
                    <td>' . htmlspecialchars($fecha_desmontaje_linea) . '</td>
                    <td class="text-center">' . htmlspecialchars($jornadas) . '</td>
                    <td class="text-center">' . htmlspecialchars($coeficiente) . '</td>
                    <td>' . htmlspecialchars($descripcion) . '</td>
                    <td class="text-center">' . htmlspecialchars($cantidad) . '</td>
                    <td class="text-right">' . $precio_unitario . '</td>
                    <td class="text-center">' . htmlspecialchars($descuento) . '</td>
                    <td class="text-right">' . $base_imponible . '</td>
                </tr>';
                    
                    // ========== COMPONENTES DEL KIT ==========
                    // Si es un KIT y no está oculto el detalle, mostrar componentes
                    if (!empty($linea['es_kit_articulo']) && $linea['es_kit_articulo'] == 1 && 
                        isset($linea['ocultar_detalle_kit_linea_ppto']) && $linea['ocultar_detalle_kit_linea_ppto'] == 0) {
                        
                        // Obtener componentes del KIT
                        $componentes = $kitModel->get_kits_by_articulo_maestro($linea['id_articulo']);
                        
                        if (!empty($componentes)) {
                            // Filtrar solo componentes activos
                            $componentesActivos = array_filter($componentes, function($comp) {
                                return isset($comp['activo_articulo_componente']) && $comp['activo_articulo_componente'] != 0;
                            });
                            
                            if (!empty($componentesActivos)) {
                                $html .= '
                <tr>
                    <td colspan="11" style="padding: 8px 4px 8px 30px; background: #f8f9fa; border-left: 3px solid #28a745;">
                        <div style="font-size: 7.5pt; color: #495057;">
                            <strong style="color: #28a745;">✓ Componentes del KIT:</strong>
                            <ul style="margin: 4px 0 0 15px; padding: 0; list-style: none;">';
                                
                                foreach ($componentesActivos as $comp) {
                                    $cantidad_comp = $comp['cantidad_kit'] ?? $comp['total_componente_kit'] ?? 1;
                                    $nombre_comp = $comp['nombre_articulo_componente'] ?? 'Sin nombre';
                                    
                                    $html .= '
                                <li style="margin: 2px 0; color: #6c757d;">
                                    <span style="color: #28a745;">•</span> 
                                    <strong>' . htmlspecialchars($cantidad_comp) . 'x</strong> ' . 
                                    htmlspecialchars($nombre_comp) . '
                                </li>';
                                }
                                
                                $html .= '
                            </ul>
                        </div>
                    </td>
                </tr>';
                            }
                        }
                    }
                    // ========== FIN COMPONENTES DEL KIT ==========
                }
                
                // Subtotal de ubicación
                $html .= '
                <tr class="subtotal-row">
                    <td colspan="10" style="text-align: right; font-weight: 700;">
                        Subtotal ' . htmlspecialchars($nombre_ubicacion) . ':
                    </td>
                    <td class="text-right">' . 
                        number_format($datos_ubicacion['subtotal_ubicacion'], 2, ',', '.') . ' €' . 
                    '</td>
                </tr>';
                
                $html .= '
            </tbody>
        </table>';
            }
            
            // Total por fecha - OCULTADO
            // $html .= '
        // <table class="lineas-table">
        //     <tbody>
        //         <tr class="total-fecha-row">
        //             <td colspan="9" style="text-align: right; font-weight: 700; font-size: 9pt;">
        //                 TOTAL FECHA ' . htmlspecialchars($fecha_formateada) . ':
        //             </td>
        //             <td class="text-right" style="font-size: 9pt;">' . 
        //                 number_format($datos_fecha['subtotal_fecha'], 2, ',', '.') . ' €' . 
        //             '</td>
        //             <td class="text-right" style="font-size: 9pt;">' . 
        //                 number_format($datos_fecha['total_fecha'], 2, ',', '.') . ' €' . 
        //             '</td>
        //         </tr>
        //     </tbody>
        // </table>';
        }
        
        // Tabla de desglose de IVA - Solo mostrar si hay MÁS de un tipo de IVA
        if (!empty($desglose_iva) && count($desglose_iva) > 1) {
            // Ordenar por porcentaje de IVA
            ksort($desglose_iva);
            
            $html .= '
        
        <!-- Desglose de IVA -->
        <div class="desglose-iva-section">
            <h4 style="font-size: 10pt; margin-bottom: 8px; color: #2c3e50;">Desglose de IVA</h4>
            <table class="desglose-iva-table">
                <thead>
                    <tr>
                        <th style="width: 20%;">% IVA</th>
                        <th style="width: 40%;">Base Imponible</th>
                        <th style="width: 40%;">Importe IVA</th>
                    </tr>
                </thead>
                <tbody>';
            
            $total_iva_desglose = 0;
            
            foreach ($desglose_iva as $porcentaje => $datos) {
                $total_iva_desglose += $datos['importe_iva'];
                
                $html .= '
                    <tr>
                        <td>' . number_format($porcentaje, 2, ',', '.') . ' %</td>
                        <td>' . number_format($datos['base_imponible'], 2, ',', '.') . ' €</td>
                        <td>' . number_format($datos['importe_iva'], 2, ',', '.') . ' €</td>
                    </tr>';
            }
            
            $html .= '
                </tbody>
                <tfoot>
                    <tr>
                        <td>TOTAL</td>
                        <td></td>
                        <td>' . number_format($total_iva_desglose, 2, ',', '.') . ' €</td>
                    </tr>
                </tfoot>
            </table>
        </div>';
        }
        
        // Totales generales
        $html .= '
        
        <!-- Totales generales -->
        <div class="total-general-section">
            <table class="total-general-table">
                <tbody>
                    <tr>
                        <td class="label">Subtotal (Base Imponible):</td>
                        <td class="valor">' . 
                            number_format($totales_generales['subtotal'], 2, ',', '.') . ' €' . 
                        '</td>
                    </tr>
                    <tr>
                        <td class="label">Total IVA';
        
        // Si solo hay un tipo de IVA, mostrar el porcentaje
        if (!empty($desglose_iva) && count($desglose_iva) == 1) {
            $porcentaje_unico = array_key_first($desglose_iva);
            $html .= ' (' . number_format($porcentaje_unico, 2, ',', '.') . '%)';
        }
        
        $html .= ':</td>
                        <td class="valor">' . 
                            number_format($totales_generales['total_iva'], 2, ',', '.') . ' €' . 
                        '</td>
                    </tr>
                    <tr class="total-final">
                        <td class="label">TOTAL PRESUPUESTO:</td>
                        <td class="valor">' . 
                            number_format($totales_generales['total'], 2, ',', '.') . ' €' . 
                        '</td>
                    </tr>
                </tbody>
            </table>
        </div>';
        
        // FORMA DE PAGO en formato texto
        if (!empty($datos_presupuesto['nombre_pago'])) {
            $html .= '
        
        <!-- Forma de Pago -->
        <div class="forma-pago-texto">
            <span class="titulo-forma-pago">FORMA DE PAGO:</span> ';
            
            // Construir frase
            $frase_pago = [];
            
            // Método
            if (!empty($datos_presupuesto['nombre_metodo_pago'])) {
                $frase_pago[] = htmlspecialchars($datos_presupuesto['nombre_metodo_pago']);
            }
            
            // Modalidad
            $frase_pago[] = htmlspecialchars($datos_presupuesto['nombre_pago']);
            
            // Anticipo
            if (!empty($datos_presupuesto['porcentaje_anticipo_pago'])) {
                $texto_anticipo = 'Anticipo del ' . htmlspecialchars($datos_presupuesto['porcentaje_anticipo_pago']) . '%';
                
                if (isset($datos_presupuesto['dias_anticipo_pago'])) {
                    $dias_anticipo = intval($datos_presupuesto['dias_anticipo_pago']);
                    
                    if ($dias_anticipo < 0) {
                        $texto_anticipo .= ' (' . abs($dias_anticipo) . ' días antes del inicio)';
                    } elseif ($dias_anticipo > 0) {
                        $texto_anticipo .= ' (' . $dias_anticipo . ' días después del inicio)';
                    } else {
                        $texto_anticipo .= ' (el día de inicio)';
                    }
                }
                
                $frase_pago[] = $texto_anticipo;
            }
            
            // Pago final
            if (!empty($datos_presupuesto['porcentaje_final_pago']) && 
                $datos_presupuesto['porcentaje_anticipo_pago'] < 100) {
                $texto_final = 'Pago final del ' . htmlspecialchars($datos_presupuesto['porcentaje_final_pago']) . '%';
                
                if (isset($datos_presupuesto['dias_final_pago'])) {
                    $dias_final = intval($datos_presupuesto['dias_final_pago']);
                    
                    if ($dias_final < 0) {
                        $texto_final .= ' (' . abs($dias_final) . ' días antes del fin)';
                    } elseif ($dias_final > 0) {
                        $texto_final .= ' (' . $dias_final . ' días después del fin)';
                    } else {
                        $texto_final .= ' (el día de fin)';
                    }
                }
                
                $frase_pago[] = $texto_final;
            }
            
            // Descuento
            if (!empty($datos_presupuesto['descuento_pago']) && $datos_presupuesto['descuento_pago'] > 0) {
                $frase_pago[] = 'Descuento aplicado: ' . htmlspecialchars($datos_presupuesto['descuento_pago']) . '%';
            }
            
            // Unir todas las partes con punto y coma
            $html .= '<span class="texto-forma-pago">' . implode('; ', $frase_pago) . '.</span>
        </div>';
        }
        
        // Observaciones de familias y artículos
        if (!empty($observaciones)) {
            $html .= '
        
        <!-- Observaciones de familias y artículos -->
        <div class="observaciones-familias-articulos-section">
            <h4 class="titulo-observaciones">Observaciones del Presupuesto</h4>';
            
            foreach ($observaciones as $obs) {
                // Obtener nombre según tipo
                $nombre = '';
                if ($obs['tipo_observacion'] == 'familia' && !empty($obs['nombre_familia'])) {
                    $nombre = $obs['nombre_familia'];
                } elseif ($obs['tipo_observacion'] == 'articulo' && !empty($obs['nombre_articulo'])) {
                    $nombre = $obs['nombre_articulo'];
                }
                
                // Obtener texto de observación
                $texto = $obs['observacion_es'] ?? '';
                
                // Solo mostrar si hay nombre y texto
                if (!empty($nombre) && !empty(trim($texto))) {
                    $tipo_label = ucfirst(strtolower($obs['tipo_observacion']));
                    $html .= '
            <div class="observacion-item">
                <div class="observacion-tipo">' . $tipo_label . ': ' . htmlspecialchars($nombre) . '</div>
                <div class="observacion-texto">' . nl2br(htmlspecialchars($texto)) . '</div>
            </div>';
                }
            }
            
            $html .= '
        </div>';
        }
        
        $html .= '
        
    </div><!-- fin lineas-section -->';
    }
        
        $html .= '

    </div><!-- fin main-content -->
    
    <!-- Contenedor de footers al final de la última página -->
    <div class="footer-container">

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
    
    $html .= '
    
    </div><!-- fin footer-container -->
    
    <!-- Numeración de páginas -->
    <div class="page-number"></div>
    
    <!-- Script para numeración de páginas -->
    <script>
        // Calcular y mostrar número de páginas
        function actualizarNumeroPagina() {
            var pageNumberEl = document.querySelector(\'.page-number\');
            if (pageNumberEl) {
                // Calcular páginas aproximadas (A4 = ~1056px en altura útil con márgenes)
                var altoPagina = 1056;
                var altoContenido = document.body.scrollHeight;
                var totalPaginas = Math.ceil(altoContenido / altoPagina);
                
                // Mostrar en pantalla
                pageNumberEl.textContent = \'Página 1 de \' + totalPaginas;
            }
        }
        
        // Actualizar al cargar
        window.addEventListener(\'load\', function() {
            setTimeout(actualizarNumeroPagina, 100);
        });
        
        // Al imprimir, el CSS counter se encarga automáticamente
        window.addEventListener(\'beforeprint\', function() {
            try {
                var pageNumbers = document.querySelectorAll(\'.page-number\');
                pageNumbers.forEach(function(el) {
                    el.style.display = \'block\';
                });
            } catch(e) {
                console.log(\'Error en numeración:\', e);
            }
        });
        
        // Forzar impresión con Ctrl+P
        document.addEventListener(\'keydown\', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === \'p\') {
                e.preventDefault();
                window.print();
            }
        });
    </script>

</body>
</html>';
            
            // 9. Enviar HTML al navegador
            echo $html;
            
        } catch (Exception $e) {
            // Registrar error
            $registro->registrarActividad(
                'admin',
                'impresionpresupuesto_m2_es.php',
                'cli_esp',
                "[MODELO 2] Error al generar presupuesto: " . $e->getMessage(),
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
