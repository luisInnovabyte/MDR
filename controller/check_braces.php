<?php
$file = file_get_contents('lineapresupuesto.php');
$lines = explode("\n", $file);

$bracesCount = 0;
$caseStart = null;
$currentCase = null;

foreach ($lines as $lineNum => $line) {
    $actualLineNum = $lineNum + 1;
    
    // Detectar inicio de case
    if (preg_match('/^\s*case\s+"([^"]+)":/', $line, $matches)) {
        if ($caseStart !== null) {
            echo "Case '$currentCase' (líneas $caseStart-$actualLineNum): Balance de llaves = $bracesCount\n";
        }
        $currentCase = $matches[1];
        $caseStart = $actualLineNum;
        $bracesCount = 0;
    }
    
    // Detectar break o default
    if (preg_match('/^\s*(break|default):/', $line)) {
        if ($caseStart !== null) {
            echo "Case '$currentCase' (líneas $caseStart-$actualLineNum): Balance de llaves = $bracesCount\n";
            $caseStart = null;
        }
    }
    
    // Contar llaves
    $open = substr_count($line, '{');
    $close = substr_count($line, '}');
    $bracesCount += ($open - $close);
    
    // Mostrar líneas con desbalance extremo
    if (abs($bracesCount) > 10) {
        echo "⚠️  ALERTA en línea $actualLineNum: Balance = $bracesCount\n";
        echo "    $line\n";
    }
}

echo "\n=== ANÁLISIS GLOBAL ===\n";
$totalOpen = substr_count($file, '{');
$totalClose = substr_count($file, '}');
echo "Total '{' = $totalOpen\n";
echo "Total '}' = $totalClose\n";
echo "Balance = " . ($totalOpen - $totalClose) . "\n";
?>
