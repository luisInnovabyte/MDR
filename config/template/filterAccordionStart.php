<?php
/**
 * Componente: Filter Accordion
 * Descripción: Acordeón de filtros avanzados con diseño profesional
 * Uso: include_once('../../config/template/filterAccordionStart.php');
 *      [contenido de los filtros]
 *      include_once('../../config/template/filterAccordionEnd.php');
 * 
 * Variables opcionales:
 * @param string $accordionId - ID del acordeón (default: 'accordion')
 * @param string $collapseId - ID del collapse (default: 'collapseOne')
 * @param string $accordionTitle - Título del acordeón (default: 'Filtros Avanzados')
 */

$accordionId = $accordionId ?? 'accordion';
$collapseId = $collapseId ?? 'collapseOne';
$accordionTitle = $accordionTitle ?? 'Filtros Avanzados';
?>

<!-- Filter Accordion Start Component -->
<div id="<?php echo $accordionId; ?>" class="accordion mg-b-20">
    <div class="card bd-0 shadow-base">
        <div class="card-header bg-gray-100 bd-b pd-20">
            <h6 class="mg-b-0">
                <a id="accordion-toggle" 
                   class="tx-gray-800 tx-semibold d-flex align-items-center collapsed" 
                   data-toggle="collapse" 
                   href="#<?php echo $collapseId; ?>"
                   style="text-decoration: none;">
                    <i class="fa fa-filter mg-r-10 tx-primary"></i>
                    <span><?php echo $accordionTitle; ?></span>
                    <i class="fa fa-angle-down mg-l-auto tx-20"></i>
                </a>
            </h6>
        </div>

        <div id="<?php echo $collapseId; ?>" class="collapse" data-parent="#<?php echo $accordionId; ?>">
            <div class="card-body pd-30 bg-gray-50">
