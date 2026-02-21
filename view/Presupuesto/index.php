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
        
        /* Estilos para encabezados agrupados */
        #presupuestos_data thead tr:first-child th[colspan] {
            font-weight: bold;
            font-size: 0.95rem;
            padding: 12px 8px;
            vertical-align: middle;
        }
        
        #presupuestos_data thead tr:first-child th[colspan="8"] {
            background-color: #d1ecf1 !important; /* Azul claro suave */
            color: #0c5460;
            border-color: #bee5eb !important;
        }
        
        #presupuestos_data thead tr:first-child th[colspan="3"] {
            background-color: #d4edda !important; /* Verde claro suave */
            color: #155724;
            border-color: #c3e6cb !important;
        }
        
        /* Estilos para las columnas de la segunda fila del encabezado - Grupo EVENTO */
        #presupuestos_data thead tr:nth-child(2) th:nth-child(1),
        #presupuestos_data thead tr:nth-child(2) th:nth-child(2),
        #presupuestos_data thead tr:nth-child(2) th:nth-child(3),
        #presupuestos_data thead tr:nth-child(2) th:nth-child(4),
        #presupuestos_data thead tr:nth-child(2) th:nth-child(5),
        #presupuestos_data thead tr:nth-child(2) th:nth-child(6),
        #presupuestos_data thead tr:nth-child(2) th:nth-child(7),
        #presupuestos_data thead tr:nth-child(2) th:nth-child(8) {
            background-color: #d1ecf1 !important; /* Azul claro suave - EVENTO */
            color: #0c5460 !important;
            border-color: #bee5eb !important;
        }
        
        /* Estilos para las columnas de la segunda fila del encabezado - Grupo PRESUPUESTO */
        #presupuestos_data thead tr:nth-child(2) th:nth-child(9),
        #presupuestos_data thead tr:nth-child(2) th:nth-child(10),
        #presupuestos_data thead tr:nth-child(2) th:nth-child(11) {
            background-color: #d4edda !important; /* Verde claro suave - PRESUPUESTO */
            color: #155724 !important;
            border-color: #c3e6cb !important;
        }
        
        /* Asegurar que DataTables no sobrescriba los colores */
        #presupuestos_data.dataTable thead th {
            background-color: inherit !important;
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
                            <th class="d-none" rowspan="2">Id</th>
                            <th colspan="8" class="text-center">EVENTO</th>
                            <th colspan="3" class="text-center">PRESUPUESTO</th>
                            <th rowspan="2">Activo</th>
                            <th rowspan="2">Acciones</th>
                        </tr>
                        <tr>
                            <th>Número</th>
                            <th>Cliente</th>
                            <th>Evento</th>
                            <th>F. Inicio</th>
                            <th>F. Fin</th>
                            <th>Duración</th>
                            <th>Días Inicio</th>
                            <th>Estado Evento</th>
                            <th>Días Val.</th>
                            <th>Estado</th>
                            <th>Importe (€)</th>
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
                            <th><input type="text" placeholder="Días val." class="form-control form-control-sm" /></th>
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

<!-- ============================================ -->
<!-- MODAL DE IMPRESIÓN DE PRESUPUESTO -->
<!-- ============================================ -->
<div class="modal fade" id="modalImpresionPresupuesto" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" style="max-width:480px;" role="document">
        <div class="modal-content shadow-lg border-0">
            <!-- Cabecera -->
            <div class="modal-header border-0 text-white" style="background: linear-gradient(135deg, #1a73e8 0%, #0d47a1 100%);">
                <h5 class="modal-title fw-semibold">
                    <i class="fas fa-file-pdf me-2"></i>Generar documento
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-0">
                <form id="formImpresionPresupuesto">
                    <input type="hidden" id="impresion_id_presupuesto" name="id_presupuesto">
                    <input type="hidden" id="impresion_id_empresa" name="id_empresa">
                    <input type="hidden" id="impresion_codigo_estado" name="codigo_estado">

                    <!-- Banda informativa: presupuesto seleccionado -->
                    <div class="d-flex align-items-center gap-2 px-4 py-3 border-bottom bg-light">
                        <span class="text-primary"><i class="fas fa-file-invoice fs-5"></i></span>
                        <span id="impresion_info_presupuesto" class="small text-muted fw-semibold">—</span>
                    </div>

                    <div class="px-4 pt-3 pb-2">

                        <!-- Destinatario -->
                        <p class="text-uppercase text-muted fw-bold mb-2" style="font-size:.7rem; letter-spacing:.06em;">
                            <i class="fas fa-user me-1"></i>Destinatario
                        </p>
                        <div class="form-check mb-1">
                            <input class="form-check-input" type="radio" name="tipo_presupuesto" id="tipo_cliente" value="cliente" checked>
                            <label class="form-check-label" for="tipo_cliente">
                                <strong>Cliente final</strong>
                                <small class="d-block text-muted">Presupuesto detallado con precios y condiciones</small>
                            </label>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="radio" name="tipo_presupuesto" id="tipo_intermediario" value="intermediario">
                            <label class="form-check-label" for="tipo_intermediario">
                                <strong>Intermediario (Hotel)</strong>
                                <small class="d-block text-muted">Presupuesto con descuento de intermediario aplicado</small>
                            </label>
                        </div>

                        <hr class="my-3">

                        <!-- Idioma -->
                        <p class="text-uppercase text-muted fw-bold mb-2" style="font-size:.7rem; letter-spacing:.06em;">
                            <i class="fas fa-language me-1"></i>Idioma
                        </p>
                        <div class="form-check mb-1">
                            <input class="form-check-input" type="radio" name="idioma" id="idioma_espanol" value="espanol" checked>
                            <label class="form-check-label" for="idioma_espanol">
                                <strong>Español</strong> <span class="ms-1">🇪🇸</span>
                            </label>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="radio" name="idioma" id="idioma_ingles" value="ingles" disabled>
                            <label class="form-check-label text-muted" for="idioma_ingles">
                                <strong>English</strong> <span class="ms-1">🇬🇧</span>
                                <span class="badge bg-secondary ms-1" style="font-size:.62rem;">Próximamente</span>
                            </label>
                        </div>

                        <!-- Selector de versión (aparece solo si hay más de una) -->
                        <div class="mb-3" id="divSelectorVersion" style="display:none;">
                            <hr class="my-3">
                            <p class="text-uppercase text-muted fw-bold mb-2" style="font-size:.7rem; letter-spacing:.06em;">
                                <i class="fas fa-code-branch me-1"></i>Versión a imprimir
                            </p>
                            <select class="form-select form-select-sm" id="impresion_numero_version" name="numero_version">
                            </select>
                            <div class="form-text text-muted mt-1">
                                <i class="fas fa-info-circle me-1"></i>Selecciona la versión que quieres imprimir.
                            </div>
                        </div>

                        <!-- Alerta versión actual -->
                        <div class="alert alert-info py-2 mb-1 d-flex align-items-center gap-2" id="alertaVersionActual">
                            <i class="fas fa-info-circle flex-shrink-0"></i>
                            <span class="small">Se imprimirá la <strong>versión actual</strong> del presupuesto.</span>
                        </div>

                    </div><!-- /px-4 -->
                </form>
            </div><!-- /modal-body -->

            <div class="modal-footer border-top bg-light d-flex justify-content-between">
                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cancelar
                </button>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-warning btn-sm" id="btnAlbaranCarga"
                            title="Solo disponible para presupuestos con versión aprobada">
                        <i class="fas fa-truck me-1"></i>Albarán de carga
                    </button>
                    <button type="button" class="btn btn-primary btn-sm" id="btnImprimirPresupuesto">
                        <i class="fas fa-file-pdf me-1"></i>Generar PDF
                    </button>
                </div>
            </div>

        </div><!-- /modal-content -->
    </div>
</div>
<!-- ============================================ -->
<!-- FIN MODAL DE IMPRESIÓN -->
<!-- ============================================ -->

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

<!-- ============================================================ -->
<!-- MODAL: Historial de Versiones                                -->
<!-- ============================================================ -->
<div class="modal fade" id="modalHistorialVersiones" tabindex="-1" aria-labelledby="tituloHistorialVersiones" aria-hidden="true">
    <div class="modal-dialog modal-xl" style="max-width:95%;">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="tituloHistorialVersiones">
                    <i class="fas fa-history me-2"></i>Historial de Versiones
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info py-2 mb-3" id="infoPresupuestoVersiones">
                    <strong>Presupuesto:</strong> <span id="hv_numeroPresupuesto"></span> &nbsp;|&nbsp;
                    <strong>Cliente:</strong> <span id="hv_nombreCliente"></span> &nbsp;|&nbsp;
                    <strong>Evento:</strong> <span id="hv_nombreEvento"></span>
                </div>
                <table id="tblHistorialVersiones" class="table table-striped table-bordered table-sm nowrap" style="width:100%">
                    <thead>
                        <tr>
                            <th>Ver.</th>
                            <th>Estado</th>
                            <th>Creación</th>
                            <th>Envío</th>
                            <th>Motivo</th>
                            <th>Líneas</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-outline-info" id="btnAbrirComparador">
                    <i class="fas fa-code-branch me-1"></i>Comparar versiones
                </button>
                <button type="button" class="btn btn-success" id="btnNuevaVersion">
                    <i class="fas fa-plus me-1"></i>Nueva versión
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================ -->
<!-- MODAL: Nueva Versión                                         -->
<!-- ============================================================ -->
<div class="modal fade" id="modalNuevaVersion" tabindex="-1" aria-labelledby="tituloNuevaVersion" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tituloNuevaVersion">
                    <i class="fas fa-plus-circle me-2"></i>Nueva versión del presupuesto
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="nv_idPresupuesto">
                <div class="mb-3">
                    <label for="nv_motivo" class="form-label fw-semibold">
                        Motivo de la nueva versión <span class="text-danger">*</span>
                    </label>
                    <textarea
                        class="form-control"
                        id="nv_motivo"
                        rows="3"
                        placeholder="Ej: Cliente solicita 2 focos adicionales y descuento del 5%"
                    ></textarea>
                    <div class="form-text text-muted">
                        Explica brevemente qué cambia en esta versión. Se guardará como historial.
                    </div>
                </div>
                <div class="alert alert-warning py-2">
                    <i class="fas fa-info-circle me-1"></i>
                    Se copiarán todas las líneas de la versión actual a la nueva versión. La nueva versión quedará en estado <strong>borrador</strong>.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnConfirmarNuevaVersion">
                    <i class="fas fa-check me-1"></i>Crear versión
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================ -->
<!-- MODAL: Comparar Versiones                                    -->
<!-- ============================================================ -->
<div class="modal fade" id="modalCompararVersiones" tabindex="-1" aria-labelledby="tituloCompararVersiones" aria-hidden="true">
    <div class="modal-dialog" style="max-width: 50vw; width: 50vw;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tituloCompararVersiones">
                    <i class="fas fa-code-branch me-2"></i>Comparar versiones
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3 align-items-end">
                    <div class="col-md-5">
                        <label class="form-label fw-semibold">Versión A (base):</label>
                        <select class="form-select" id="cmp_selectVersionA"></select>
                    </div>
                    <div class="col-md-2 text-center">
                        <i class="fas fa-exchange-alt fa-2x text-muted"></i>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label fw-semibold">Versión B (nueva):</label>
                        <select class="form-select" id="cmp_selectVersionB"></select>
                    </div>
                </div>
                <div class="mb-3">
                    <button class="btn btn-primary" id="btnEjecutarComparacion">
                        <i class="fas fa-search me-1"></i>Comparar
                    </button>
                </div>

                <div id="cmp_resultado" style="display:none;">
                    <div class="alert alert-info" id="cmp_resumen"></div>

                    <div id="cmp_seccionAnadidas" style="display:none;">
                        <h6 class="text-success"><i class="fas fa-plus-circle me-1"></i>Líneas añadidas en versión B</h6>
                        <table class="table table-sm table-bordered">
                            <thead class="table-success"><tr><th>Código</th><th>Descripción</th><th>Cantidad</th><th>P.Unit.</th><th>Total</th></tr></thead>
                            <tbody id="cmp_tbodyAnadidas"></tbody>
                        </table>
                    </div>

                    <div id="cmp_seccionEliminadas" style="display:none;">
                        <h6 class="text-danger"><i class="fas fa-minus-circle me-1"></i>Líneas eliminadas en versión B</h6>
                        <table class="table table-sm table-bordered">
                            <thead class="table-danger"><tr><th>Código</th><th>Descripción</th><th>Cantidad</th><th>P.Unit.</th><th>Total</th></tr></thead>
                            <tbody id="cmp_tbodyEliminadas"></tbody>
                        </table>
                    </div>

                    <div id="cmp_seccionModificadas" style="display:none;">
                        <h6 class="text-warning"><i class="fas fa-pencil-alt me-1"></i>Líneas modificadas</h6>
                        <table class="table table-sm table-bordered">
                            <thead class="table-warning"><tr><th>Descripción</th><th>Cant. A</th><th>Cant. B</th><th>P. A</th><th>P. B</th><th>Total A</th><th>Total B</th></tr></thead>
                            <tbody id="cmp_tbodyModificadas"></tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
<!-- FIN MODALES VERSIONES -->


</body>
</html>
