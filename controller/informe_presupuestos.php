<?php

require_once "../config/conexion.php";
require_once "../config/funciones.php";
require_once "../models/Presupuesto.php";

// Inicializar clases
$registro = new RegistroActividad();
$presupuesto = new Presupuesto();

// Switch principal basado en operación
switch ($_GET["op"]) {
    
    case "generar_pdf":
        try {
            // Obtener mes y año
            $month = isset($_POST['month']) ? intval($_POST['month']) : date('n');
            $year = isset($_POST['year']) ? intval($_POST['year']) : date('Y');
            
            // Obtener datos de presupuestos
            $datos = $presupuesto->get_presupuestos_por_mes($month, $year);
            
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
    <title>Informe de Presupuestos - ' . $nombreMes . ' ' . $year . '</title>
    <style>
        @page {
            size: A4 landscape;
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
            font-size: 11px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #6f42c1;
            padding-bottom: 15px;
        }
        .header h1 {
            color: #6f42c1;
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
            background-color: #6f42c1;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            z-index: 1000;
        }
        .print-button:hover {
            background-color: #5a32a3;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th {
            background-color: #6f42c1;
            color: white;
            padding: 12px 8px;
            text-align: left;
            font-weight: bold;
            font-size: 11px;
        }
        td {
            padding: 10px 8px;
            border-bottom: 1px solid #ddd;
            font-size: 10px;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f0f0f0;
        }
        .estado-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 9px;
            color: white;
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
            border-left: 4px solid #6f42c1;
        }
        .summary h3 {
            margin: 0 0 10px 0;
            color: #6f42c1;
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
        .total-column {
            text-align: right;
            font-weight: bold;
        }
    </style>
    <script>
        function imprimirPDF() {
            window.print();
        }
    </script>
</head>
<body>
    <button class="print-button no-print" onclick="imprimirPDF()">
        <i class="fas fa-print"></i> Imprimir / Guardar como PDF
    </button>
    
    <div class="header">
        <h1>📋 Informe de Presupuestos</h1>
        <p class="subtitle">' . $nombreMes . ' ' . $year . '</p>
        <p class="date">Generado el ' . date('d/m/Y H:i') . '</p>
    </div>';
            
            if (count($datos) > 0) {
                // Calcular estadísticas
                $totalImporte = 0;
                $estadisticas = [];
                
                foreach ($datos as $row) {
                    $totalImporte += floatval($row['total_presupuesto']);
                    $estado = $row['nombre_estado_ppto'];
                    if (!isset($estadisticas[$estado])) {
                        $estadisticas[$estado] = 0;
                    }
                    $estadisticas[$estado]++;
                }
                
                // Resumen estadístico
                $html .= '
    <div class="summary no-print">
        <h3>📊 Resumen Estadístico</h3>
        <div class="summary-item">
            <strong>Total de presupuestos:</strong> ' . count($datos) . '
        </div>
        <div class="summary-item">
            <strong>Importe total:</strong> ' . number_format($totalImporte, 2, ',', '.') . ' €
        </div>';
        
                foreach ($estadisticas as $estado => $cantidad) {
                    $html .= '
        <div class="summary-item">
            <strong>' . htmlspecialchars($estado) . ':</strong> ' . $cantidad . '
        </div>';
                }
                
                $html .= '
    </div>';
                
                $html .= '
    <table>
        <thead>
            <tr>
                <th style="width: 10%;">Número</th>
                <th style="width: 12%;">Fecha Inicio</th>
                <th style="width: 12%;">Fecha Fin</th>
                <th style="width: 26%;">Evento</th>
                <th style="width: 20%;">Cliente</th>
                <th style="width: 10%;">Total</th>
                <th style="width: 10%;">Estado</th>
            </tr>
        </thead>
        <tbody>';
                
                foreach ($datos as $row) {
                    // Formatear fechas
                    $fechaInicio = date('d/m/Y', strtotime($row['fecha_inicio_evento_presupuesto']));
                    $fechaFin = date('d/m/Y', strtotime($row['fecha_fin_evento_presupuesto']));
                    
                    // Color del estado
                    $colorEstado = $row['color_estado_ppto'] ?? '#6c757d';
                    
                    // Formatear total
                    $total = number_format(floatval($row['total_presupuesto']), 2, ',', '.');
                    
                    $html .= '
            <tr>
                <td><strong>' . htmlspecialchars($row['numero_presupuesto']) . '</strong></td>
                <td>' . $fechaInicio . '</td>
                <td>' . $fechaFin . '</td>
                <td>' . htmlspecialchars($row['nombre_evento_presupuesto']) . '</td>
                <td>' . htmlspecialchars($row['nombre_completo_cliente']) . '</td>
                <td class="total-column">' . $total . ' €</td>
                <td><span class="estado-badge" style="background-color: ' . $colorEstado . ';">' . 
                    htmlspecialchars($row['nombre_estado_ppto']) . '</span></td>
            </tr>';
                }
                
                // Fila de totales
                $html .= '
            <tr style="background-color: #f0f0f0; font-weight: bold;">
                <td colspan="5" style="text-align: right; padding-right: 10px;">TOTAL GENERAL:</td>
                <td class="total-column">' . number_format($totalImporte, 2, ',', '.') . ' €</td>
                <td></td>
            </tr>';
                
                $html .= '
        </tbody>
    </table>';
            } else {
                $html .= '
    <div class="no-data">
        <p>⚠️ No hay presupuestos registrados para ' . $nombreMes . ' de ' . $year . '</p>
    </div>';
            }
            
            $html .= '
    <div class="footer">
        <p><strong>MDR ERP Manager</strong> - Sistema de Gestión de Equipos Audiovisuales</p>
        <p>Total de registros: ' . count($datos) . ' | Importe total: ' . number_format($totalImporte, 2, ',', '.') . ' € | Página generada: ' . date('d/m/Y H:i:s') . '</p>
    </div>
</body>
</html>';
            
            echo $html;
            
            $registro->registrarActividad(
                'admin',
                'informe_presupuestos.php',
                'generar_pdf',
                "PDF de presupuestos generado para $nombreMes $year - Total registros: " . count($datos) . ", Importe: " . number_format($totalImporte, 2) . "€",
                'info'
            );
            
        } catch (Exception $e) {
            $registro->registrarActividad(
                'admin',
                'informe_presupuestos.php',
                'generar_pdf',
                "Error al generar PDF: " . $e->getMessage(),
                'error'
            );
            
            echo '<h1>Error al generar el informe</h1><p>' . htmlspecialchars($e->getMessage()) . '</p>';
        }
        break;
    
    case "listar_presupuestos":
        try {
            // Obtener mes y año
            $month = isset($_POST['month']) ? intval($_POST['month']) : date('n');
            $year = isset($_POST['year']) ? intval($_POST['year']) : date('Y');
            
            // Obtener datos de presupuestos
            $datos = $presupuesto->get_presupuestos_por_mes($month, $year);
            
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
