<?php
/**
 * Componente: Filter Alert
 * Descripción: Alerta que se muestra cuando hay filtros activos
 * Uso: include_once('../../config/template/filterAlert.php');
 * 
 * Variables requeridas:
 * @param string $alertId - ID del elemento alert (default: 'filter-alert')
 * @param string $filtersTextId - ID del elemento para mostrar los filtros activos (default: 'active-filters-text')
 * @param string $clearButtonId - ID del botón limpiar filtros (default: 'clear-filter')
 */

$alertId = $alertId ?? 'filter-alert';
$filtersTextId = $filtersTextId ?? 'active-filters-text';
$clearButtonId = $clearButtonId ?? 'clear-filter';
?>

<!-- Filter Alert Component -->
<div class="alert alert-info alert-dismissible fade show d-flex align-items-center" 
     role="alert" 
     id="<?php echo $alertId; ?>" 
     style="display: none !important;">
    <i class="fa fa-filter mg-r-10 tx-18"></i>
    <div class="flex-grow-1">
        <strong>Filtros aplicados:</strong>
        <span id="<?php echo $filtersTextId; ?>" class="mg-l-5"></span>
    </div>
    <button type="button" 
            class="btn btn-sm btn-white bd bd-gray-300 mg-l-10" 
            id="<?php echo $clearButtonId; ?>">
        <i class="fa fa-times mg-r-5"></i>Limpiar
    </button>
</div>
