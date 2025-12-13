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
            <button type="button" class="btn btn-success btn-sm ms-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalEstadisticasPresupuestos" title="Ver estadísticas de presupuestos">
                <i class="fas fa-chart-bar me-1"></i>Estadísticas
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
                    
                    if (response.success) {
                        const data = response.data;
                        
                        // ==========================================
                        // ESTADÍSTICAS GENERALES
                        // ==========================================
                        $('#modal-stat-total').text(data.total_activos || 0);
                        $('#modal-stat-aprobados').text(data.aprobados || 0);
                        
                        // Pendientes = En Proceso + Pendiente Revisión + Esperando Respuesta
                        const totalPendientes = (parseInt(data.en_proceso) || 0) + 
                                               (parseInt(data.pendiente_revision) || 0) + 
                                               (parseInt(data.esperando_respuesta) || 0);
                        $('#modal-stat-pendientes').text(totalPendientes);
                        
                        // Tasa de conversión
                        const conversion = data.tasa_conversion || 0;
                        $('#modal-stat-conversion').html(conversion + '<small>%</small>');
                        
                        // ==========================================
                        // DISTRIBUCIÓN POR ESTADOS
                        // ==========================================
                        $('#modal-dist-proceso').text(data.en_proceso || 0);
                        $('#modal-dist-pendiente').text(data.pendiente_revision || 0);
                        $('#modal-dist-esperando').text(data.esperando_respuesta || 0);
                        $('#modal-dist-aprobados').text(data.aprobados || 0);
                        $('#modal-dist-rechazados').text(data.rechazados || 0);
                        $('#modal-dist-cancelados').text(data.cancelados || 0);
                        $('#modal-dist-vigentes').text(data.vigentes || 0);
                        $('#modal-dist-por-caducar').text(data.por_caducar_7dias || 0);
                        
                        // ==========================================
                        // ESTADÍSTICAS DEL MES ACTUAL
                        // ==========================================
                        $('#modal-mes-total').text(data.mes_total || 0);
                        $('#modal-mes-aceptados').text(data.mes_aprobados || 0);
                        $('#modal-mes-pendientes').text(data.mes_pendientes || 0);
                        $('#modal-mes-rechazados').text(data.mes_rechazados || 0);
                        
                        // ==========================================
                        // ALERTAS Y EVENTOS
                        // ==========================================
                        $('#modal-alert-caduca-hoy').text(data.caduca_hoy || 0);
                        $('#modal-alert-caducados').text(data.caducados || 0);
                        $('#modal-alert-eventos-proximos').text(data.eventos_proximos_7dias || 0);
                        
                        console.log('✅ Estadísticas del modal actualizadas correctamente');
                        console.log('📊 Datos procesados:', {
                            total_activos: data.total_activos,
                            aprobados: data.aprobados,
                            rechazados: data.rechazados,
                            tasa_conversion: conversion + '%'
                        });
                    } else {
                        console.error('❌ Error al cargar estadísticas:', response.mensaje);
                        // Mostrar error en el modal
                        $('.modal-body h3, .modal-body h4').html('<small class="text-danger">Error</small>');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('❌ Error AJAX estadísticas modal:', error);
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
