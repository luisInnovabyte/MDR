<?php
// Test básico de TCPDF
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Test TCPDF Básico</h2>";

try {
    require_once __DIR__ . "/../vendor/tcpdf/tcpdf.php";
    echo "✓ TCPDF cargado correctamente<br>";
    
    // Crear PDF simple
    $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
    echo "✓ Instancia de TCPDF creada<br>";
    
    $pdf->SetCreator('Test');
    $pdf->SetAuthor('Test');
    $pdf->SetTitle('Test PDF');
    echo "✓ Metadatos configurados<br>";
    
    $pdf->SetMargins(15, 15, 15);
    $pdf->SetAutoPageBreak(TRUE, 15);
    echo "✓ Márgenes configurados<br>";
    
    $pdf->AddPage();
    echo "✓ Página añadida<br>";
    
    $pdf->SetFont('helvetica', 'B', 16);
    $pdf->Cell(0, 10, 'Test PDF con TCPDF', 0, 1, 'C');
    echo "✓ Contenido añadido<br>";
    
    $pdf->Output('test.pdf', 'I');
    echo "✓ PDF generado correctamente<br>";
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "<br>";
    echo "Trace: " . $e->getTraceAsString();
}
?>
