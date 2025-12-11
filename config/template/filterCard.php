<?php
/**
 * Componente: Filter Card
 * Descripción: Tarjeta de filtro individual con header y body
 * Uso: include_once('../../config/template/filterCard.php');
 * 
 * Variables requeridas:
 * @param string $cardTitle - Título de la tarjeta
 * @param string $cardIcon - Icono Font Awesome (ej: 'fa-toggle-on')
 * @param string $cardContent - Contenido HTML de la tarjeta
 * @param string $colClass - Clases de columna (default: 'col-md-6')
 */

$cardTitle = $cardTitle ?? 'Filtro';
$cardIcon = $cardIcon ?? 'fa-filter';
$cardContent = $cardContent ?? '';
$colClass = $colClass ?? 'col-md-6';
?>

<!-- Filter Card Component -->
<div class="<?php echo $colClass; ?>">
    <div class="card bd bd-gray-300 shadow-sm">
        <div class="card-header bg-white bd-b pd-15">
            <h6 class="mg-b-0 tx-semibold tx-gray-700">
                <i class="fa <?php echo $cardIcon; ?> mg-r-8 tx-primary"></i><?php echo $cardTitle; ?>
            </h6>
        </div>
        <div class="card-body pd-20">
            <?php echo $cardContent; ?>
        </div>
    </div>
</div>
