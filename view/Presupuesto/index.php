<!-- ---------------------- -->
<!--   Comprobar permisos     -->
<!-- ---------------------- -->
<?php 
// $moduloActual = 'presupuestos'; 
// require_once('../../config/template/verificarPermiso.php'); 
?>

<!DOCTYPE html>
<html lang="es">

<!-- ---------------------- -->
<!--      MainHead.php      -->
<!-- ---------------------- -->

<head>
    <?php include_once('../../config/template/mainHead.php') ?>
</head>

<!-- ---------------------- -->
<!--  END MainHead.php      -->
<!-- ---------------------- -->

<body>

    <!-- ########## START: LEFT PANEL ########## -->

    <!-- ---------------------- -->
    <!--      MainLogo.php      -->
    <!-- ---------------------- -->

    <?php require_once('../../config/template/mainLogo.php') ?>

    <!-- ---------------------- -->
    <!--   END MainLogo.php     -->
    <!-- ---------------------- -->

    <div class="br-sideleft sideleft-scrollbar">
        <!-- ---------------------- -->
        <!--   MainSideBar.php      -->
        <!-- ---------------------- -->
        <?php require_once('../../config/template/mainSidebar.php') ?>

        <?php require_once('../../config/template/mainSidebarDown.php') ?>
        <!-- ---------------------- -->
        <!-- END MainSideBar.php    -->
        <!-- ---------------------- -->
        <br>
    </div><!-- br-sideleft -->
    <!-- ########## END: LEFT PANEL ########## -->


    <!-- ########## START: HEAD PANEL ########## -->
    <div class="br-header">
        <?php include_once('../../config/template/mainHeader.php') ?>
    </div><!-- br-header -->
    <!-- ########## END: HEAD PANEL ########## -->


    <!-- ########## START: RIGHT PANEL ########## -->
    <div class="br-sideright">
        <!-- ---------------------- -->
        <!--   mainRightPanel.php      -->
        <!-- ---------------------- -->
        <?php include_once('../../config/template/mainRightPanel.php') ?>
        <!-- ------------------------- -->
        <!-- END MainRightPanel.php    -->
        <!-- ------------------------- -->

    </div><!-- br-sideright -->
    <!-- ########## END: RIGHT PANEL ########## -->
    
    <style>
        #modalMantenimiento {
            z-index: 1050 !important;
        }

        .ui-datepicker {
            z-index: 1060 !important;
        }

        /* Estilos para scroll horizontal */
        .dataTables_wrapper {
            overflow-x: auto;
        }
        
        /* Contenedor DataTables */
        div.dt-container {
            width: 96% !important;
        }
        
        /* Botón de detalles */
        button.details-control {
            min-width: 30px;
        }
    </style>
    
    <!-- ########## START: MAIN PANEL ########## -->
<div class="br-mainpanel">
    <div class="br-pageheader">
        <nav class="breadcrumb pd-0 mg-0 tx-12">
            <a class="breadcrumb-item" href="../Dashboard/index.php">Dashboard</a>
            <span class="breadcrumb-item active">Mantenimiento Presupuestos</span>
        </nav>
    </div><!-- br-pageheader -->
    
    <div class="br-pagetitle">
        <div class="d-flex align-items-center">
            <h4 class="mb-0 me-2">Mantenimiento de Presupuestos</h4>
            <button type="button" class="btn btn-link p-0 ms-1" data-bs-toggle="modal" data-bs-target="#modalAyudaPresupuestos" title="Ayuda sobre el módulo">
                <i class="bi bi-question-circle text-primary" style="font-size: 1.3rem;"></i>
            </button>
            <button type="button" class="btn btn-link p-0 ms-1" data-bs-toggle="modal" data-bs-target="#modalEstadisticasPresupuestos" title="Estadísticas de presupuestos">
                <i class="fas fa-chart-bar text-success" style="font-size: 1.3rem;"></i>
            </button>
        </div>
        <br>
    </div><!-- d-flex -->

    <div class="br-pagebody">
        <div class="br-section-wrapper">
            <!-- Fila contenedora -->
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
                <!-- Contenedor de alerta expandible -->
                <div class="flex-grow-1 me-3" style="min-width: 300px;">
                    <!-- Alerta de filtro activo -->
                    <div class="alert alert-warning alert-dismissible fade show mb-0 w-100" role="alert" id="filter-alert" style="display: none;">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="truncate">
                                <i class="fas fa-filter me-2"></i>
                                <span>Filtros aplicados: </span>
                                <span id="active-filters-text" class="text-truncate"></span>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-warning ms-2 flex-shrink-0" id="clear-filter">
                                Limpiar filtros
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Botón Nuevo Presupuesto -->
                <a href="formularioPresupuesto.php?modo=nuevo" class="btn btn-oblong btn-outline-primary flex-shrink-0 mt-2 mt-sm-0">
                    <i class="fas fa-plus-circle me-2"></i>Nuevo Presupuesto
                </a>
            </div>

            <!-- Tabla de presupuestos -->
            <div class="table-wrapper">
                <table id="presupuestos_data" class="table display nowrap" style="width:100%">
                    <thead>
                        <tr>
                            <th class="d-none">Id</th>
                            <th>Número</th>
                            <th>Cliente</th>
                            <th>Evento</th>
                            <th>F. Inicio</th>
                            <th>F. Fin</th>
                            <th>Días Val.</th>
                            <th>Duración</th>
                            <th>Días Inicio</th>
                            <th>Estado Evento</th>
                            <th>Estado</th>
                            <th>Importe (€)</th>
                            <th>Activo</th>
                            <th>Act./Desac.</th>
                            <th>Edit.</th>
                            <th>Líneas</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Datos se cargarán aquí -->
                    </tbody>
                    <tfoot>
                        <tr>
                            <th class="d-none"></th>
                            <th><input type="text" placeholder="Buscar número" class="form-control form-control-sm" /></th>
                            <th><input type="text" placeholder="Buscar cliente" class="form-control form-control-sm" /></th>
                            <th><input type="text" placeholder="Buscar evento" class="form-control form-control-sm" /></th>
                            <th><input type="text" placeholder="F. Inicio" class="form-control form-control-sm" /></th>
                            <th><input type="text" placeholder="F. Fin" class="form-control form-control-sm" /></th>
                            <th><input type="text" placeholder="Días val." class="form-control form-control-sm" /></th>
                            <th><input type="text" placeholder="Duración" class="form-control form-control-sm" /></th>
                            <th><input type="text" placeholder="Días" class="form-control form-control-sm" /></th>
                            <th>
                                <select class="form-control form-control-sm" title="Filtrar por estado evento">
                                    <option value="">Todos los estados</option>
                                    <option value="finalizado">Evento finalizado</option>
                                    <option value="curso">Evento en curso</option>
                                    <option value="hoy">Evento hoy</option>
                                    <option value="próximo">Evento próximo</option>
                                    <option value="futuro">Evento futuro</option>
                                </select>
                            </th>
                            <th>
                                <select class="form-control form-control-sm" title="Filtrar por estado presupuesto">
                                    <option value="">Todos los estados</option>
                                    <option value="Pendiente">Pendiente</option>
                                    <option value="En Proceso">En Proceso</option>
                                    <option value="Aprobado">Aprobado</option>
                                    <option value="Rechazado">Rechazado</option>
                                    <option value="Cancelado">Cancelado</option>
                                </select>
                            </th>
                            <th></th>
                            <th>
                                <select class="form-control form-control-sm" title="Filtrar por estado">
                                    <option value="">Todos los estados</option>
                                    <option value="1">Activo</option>
                                    <option value="0">Inactivo</option>
                                </select>
                            </th>
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div><!-- table-wrapper -->
        </div><!-- br-section-wrapper -->
    </div><!-- br-pagebody -->

    <footer class="br-footer">
        <?php include_once('../../config/template/mainFooter.php') ?>
    </footer>
</div><!-- br-mainpanel -->
<!-- ########## END: MAIN PANEL ########## -->

<!-- Modal de ayuda -->
<?php include_once('ayudaPresupuestos.php') ?>

<!-- Modal de estadísticas -->
<?php include_once('estadisticas.php') ?>

<!-- MainJs.php -->
<?php include_once('../../config/template/mainJs.php') ?>
<script src="mntpresupuesto.js"></script>


<script>
        // Colapsar el sidebar al cargar la página
        $(document).ready(function() {
            $('body').addClass('collapsed-menu');
            $('.br-sideleft').addClass('collapsed');
        });

        // ========================================
        // FUNCIÓN PARA CARGAR ESTADÍSTICAS EN MODAL
        // ========================================
        window.cargarEstadisticasModal = function() {
            console.log('Cargando estadísticas para el modal...');
            
            $.ajax({
                url: "../../controller/presupuesto.php?op=estadisticas",
                type: "GET",
                dataType: "json",
                success: function(response) {
                    console.log('Respuesta estadísticas modal:', response);
                    
                    // Guardar respuesta para debug
                    window.lastStatsResponse = response;
                    
                    // MOSTRAR INFORMACIÓN DE DEBUG
                    if (response.debug) {
                        console.log('=== DEBUG: PRESUPUESTOS ACTIVOS ===');
                        console.table(response.debug.presupuestos_activos);
                        console.log('=== DEBUG: ESTADOS DISPONIBLES ===');
                        console.table(response.debug.estados_disponibles);
                        
                        // Mostrar sección de debug en el modal
                        $('#modal-debug-section').show();
                    }
                    
                    if (response.success) {
                        const general = response.data.general;
                        const mes = response.data.mes;
                        
                        // Estadísticas Generales
                        $('#modal-stat-total').text(general.presupuestos_activos || 0);
                        $('#modal-stat-aceptados').text(general.total_aceptado || 0);
                        
                        const pendientes = (parseInt(general.total_pendiente) || 0) + 
                                          (parseInt(general.total_enviado) || 0);
                        $('#modal-stat-pendientes').text(pendientes);
                        
                        const conversion = mes.tasa_conversion || 0;
                        $('#modal-stat-conversion').html(conversion + '<small>%</small>');
                        
                        // Distribución por Estados
                        $('#modal-dist-borrador').text(general.total_borrador || 0);
                        $('#modal-dist-pendiente').text(general.total_pendiente || 0);
                        $('#modal-dist-enviado').text(general.total_enviado || 0);
                        $('#modal-dist-aceptado').text(general.total_aceptado || 0);
                        $('#modal-dist-rechazado').text(general.total_rechazado || 0);
                        $('#modal-dist-caducado').text(general.total_caducado || 0);
                        $('#modal-dist-vigentes').text(general.vigentes || 0);
                        $('#modal-dist-por-caducar').text(general.por_caducar || 0);
                        
                        // Estadísticas del Mes
                        $('#modal-mes-total').text(mes.total_mes || 0);
                        $('#modal-mes-aceptados').text(mes.aceptados_mes || 0);
                        $('#modal-mes-pendientes').text(mes.pendientes_mes || 0);
                        $('#modal-mes-rechazados').text(mes.rechazados_mes || 0);
                        
                        // Alertas y Eventos
                        $('#modal-alert-caduca-hoy').text(general.caduca_hoy || 0);
                        $('#modal-alert-caducados').text(general.caducados || 0);
                        $('#modal-alert-eventos-proximos').text(general.eventos_proximos || 0);
                        
                        console.log('Estadísticas del modal actualizadas correctamente');
                    } else {
                        console.error('Error al cargar estadísticas:', response.message);
                        // Mostrar error en el modal
                        $('.modal-body h3, .modal-body h4').html('<small class="text-danger">Error</small>');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error AJAX estadísticas modal:', error);
                    console.error('Response:', xhr.responseText);
                    $('.modal-body h3, .modal-body h4').html('<small class="text-danger">Error</small>');
                }
            });
        };

        // Cargar estadísticas cuando se abre el modal
        $('#modalEstadisticasPresupuestos').on('shown.bs.modal', function () {
            window.cargarEstadisticasModal();
        });
</script>


</body>
</html>
