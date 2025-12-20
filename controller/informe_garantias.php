<?php

require_once "../config/conexion.php";
require_once "../config/funciones.php";
require_once "../models/Elemento.php";

// Inicializar clases
$registro = new RegistroActividad();
$elemento = new Elemento();

// Switch principal basado en operación
switch ($_GET["op"]) {
    
    case "generar_pdf":
        try {
            // Obtener mes y año
            $month = isset($_POST['month']) ? intval($_POST['month']) : date('n');
            $year = isset($_POST['year']) ? intval($_POST['year']) : date('Y');
            
            // Obtener datos de garantías
            $datos = $elemento->getWarrantyEvents($month, $year);
            
            // Nombre del mes en español
            $meses = [
                1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
                5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
                9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
            ];
            
            $nombreMes = $meses[$month];
            
            // Configurar headers para PDF
            header('Content-Type: text/html; charset=utf-8');
            
            // Generar HTML para PDF
            $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Informe de Garantías - ' . $nombreMes . ' ' . $year . '</title>
    <style>
        @page {
            size: A4;
            margin: 15mm;
        }
        
        @media print {
            body {
                margin: 0;
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
        }
        
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #0066cc;
            padding-bottom: 15px;
        }
        .header h1 {
            color: #0066cc;
            margin: 0 0 10px 0;
            font-size: 28px;
        }
        .header .subtitle {
            color: #333;
            margin: 5px 0;
            font-size: 18px;
            font-weight: bold;
        }
        .header .date {
            color: #666;
            margin: 5px 0;
            font-size: 11px;
        }
        
        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 12px 24px;
            background-color: #dc3545;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            z-index: 1000;
        }
        .print-button:hover {
            background-color: #c82333;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th {
            background-color: #0066cc;
            color: white;
            padding: 12px 8px;
            text-align: left;
            font-weight: bold;
            font-size: 12px;
        }
        td {
            padding: 10px 8px;
            border-bottom: 1px solid #ddd;
            font-size: 11px;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f0f0f0;
        }
        .vigente {
            color: #28a745;
            font-weight: bold;
        }
        .por-vencer {
            color: #ffc107;
            font-weight: bold;
        }
        .vencida {
            color: #dc3545;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 11px;
            color: #666;
            border-top: 2px solid #ddd;
            padding-top: 15px;
        }
        .no-data {
            text-align: center;
            padding: 60px 20px;
            color: #666;
            font-style: italic;
            font-size: 16px;
        }
        .summary {
            margin-top: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-left: 4px solid #0066cc;
        }
        .summary h3 {
            margin: 0 0 10px 0;
            color: #0066cc;
            font-size: 14px;
        }
        .summary-item {
            display: inline-block;
            margin-right: 30px;
            margin-bottom: 5px;
        }
        .summary-item strong {
            color: #333;
        }
    </style>
    <script>
        function imprimirPDF() {
            window.print();
        }
        
        // Imprimir automáticamente al cargar (opcional)
        // window.onload = function() {
        //     setTimeout(imprimirPDF, 500);
        // };
    </script>
</head>
<body>
    <button class="print-button no-print" onclick="imprimirPDF()">
        <i class="fas fa-print"></i> Imprimir / Guardar como PDF
    </button>
    
    <div class="header">
        <h1>📋 Informe de Garantías</h1>
        <p class="subtitle">' . $nombreMes . ' ' . $year . '</p>
        <p class="date">Generado el ' . date('d/m/Y H:i') . '</p>
    </div>';
            
            if (count($datos) > 0) {
                // Calcular estadísticas
                $totalVigentes = 0;
                $totalPorVencer = 0;
                $totalVencidas = 0;
                
                foreach ($datos as $row) {
                    switch ($row['estado_garantia_elemento']) {
                        case 'Vigente':
                            $totalVigentes++;
                            break;
                        case 'Por vencer':
                            $totalPorVencer++;
                            break;
                        case 'Vencida':
                            $totalVencidas++;
                            break;
                    }
                }
                
                // Resumen estadístico
                $html .= '
    <div class="summary no-print">
        <h3>📊 Resumen Estadístico</h3>
        <div class="summary-item">
            <strong>Total de registros:</strong> ' . count($datos) . '
        </div>
        <div class="summary-item vigente">
            <strong>Vigentes:</strong> ' . $totalVigentes . '
        </div>
        <div class="summary-item por-vencer">
            <strong>Por vencer:</strong> ' . $totalPorVencer . '
        </div>
        <div class="summary-item vencida">
            <strong>Vencidas:</strong> ' . $totalVencidas . '
        </div>
    </div>';
                
                $html .= '
    <table>
        <thead>
            <tr>
                <th style="width: 12%;">Fecha Vencimiento</th>
                <th style="width: 15%;">Código</th>
                <th style="width: 33%;">Descripción</th>
                <th style="width: 25%;">Artículo</th>
                <th style="width: 15%;">Estado</th>
            </tr>
        </thead>
        <tbody>';
                
                foreach ($datos as $row) {
                    // Formatear fecha
                    $fecha = date('d/m/Y', strtotime($row['fecha_fin_garantia_elemento']));
                    
                    // Determinar clase de estado
                    $estadoClase = '';
                    switch ($row['estado_garantia_elemento']) {
                        case 'Vigente':
                            $estadoClase = 'vigente';
                            break;
                        case 'Por vencer':
                            $estadoClase = 'por-vencer';
                            break;
                        case 'Vencida':
                            $estadoClase = 'vencida';
                            break;
                    }
                    
                    $html .= '
            <tr>
                <td>' . htmlspecialchars($fecha) . '</td>
                <td><strong>' . htmlspecialchars($row['codigo_elemento']) . '</strong></td>
                <td>' . htmlspecialchars($row['descripcion_elemento']) . '</td>
                <td>' . htmlspecialchars($row['nombre_articulo'] ?? 'N/A') . '</td>
                <td class="' . $estadoClase . '">' . htmlspecialchars($row['estado_garantia_elemento']) . '</td>
            </tr>';
                }
                
                $html .= '
        </tbody>
    </table>';
            } else {
                $html .= '
    <div class="no-data">
        <p>⚠️ No hay garantías que venzan en ' . $nombreMes . ' de ' . $year . '</p>
    </div>';
            }
            
            $html .= '
    <div class="footer">
        <p><strong>MDR ERP Manager</strong> - Sistema de Gestión de Equipos Audiovisuales</p>
        <p>Total de registros: ' . count($datos) . ' | Página generada: ' . date('d/m/Y H:i:s') . '</p>
    </div>
</body>
</html>';
            
            echo $html;
            
            $registro->registrarActividad(
                'admin',
                'informe_garantias.php',
                'generar_pdf',
                "PDF de garantías generado para $nombreMes $year - Total registros: " . count($datos),
                'info'
            );
            
        } catch (Exception $e) {
            $registro->registrarActividad(
                'admin',
                'informe_garantias.php',
                'generar_pdf',
                "Error al generar PDF: " . $e->getMessage(),
                'error'
            );
            
            echo json_encode([
                'success' => false,
                'message' => 'Error al generar el PDF: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
        break;
    
    case "listar_garantias":
        try {
            // Obtener mes y año
            $month = isset($_POST['month']) ? intval($_POST['month']) : date('n');
            $year = isset($_POST['year']) ? intval($_POST['year']) : date('Y');
            
            // Obtener datos de garantías
            $datos = $elemento->getWarrantyEvents($month, $year);
            
            echo json_encode([
                'success' => true,
                'data' => $datos
            ], JSON_UNESCAPED_UNICODE);
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener datos: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
        break;
}
?>
