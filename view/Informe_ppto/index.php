<!-- ---------------------- -->
    <!--   Comprobar permisos     -->
    <!-- ---------------------- -->
<?php $moduloActual = 'usuarios'; ?>
<?php require_once('../../config/template/verificarPermiso.php'); ?>

<!DOCTYPE html>
<html lang="es">

<!-- ---------------------- -->
<!--      MainHead.php      -->
<!-- ---------------------- -->

<head>
    <?php include_once('../../config/template/mainHead.php') ?>
    <link rel="stylesheet" href="css/calendario.css">
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

    <!-- ########## START: MAIN PANEL ########## -->
    <div class="br-mainpanel">
        <div class="br-pageheader">
            <nav class="breadcrumb pd-0 mg-0 tx-12">
                <a class="breadcrumb-item" href="../Dashboard/index.php">Dashboard</a>
                <span class="breadcrumb-item active">Calendario de Presupuestos</span>
            </nav>
        </div><!-- br-pageheader -->
        
        <div class="br-pagetitle">
            <div class="d-flex align-items-center">
                <i class="fas fa-tools me-3 text-primary" style="font-size: 2rem;"></i>
                <h4 class="mb-0 me-2">Calendario de Presupuestos</h4>
            </div>
            <br>
        </div><!-- br-pagetitle -->

        <div class="br-pagebody">
            <div class="br-section-wrapper">
                <!-- Cabecera del calendario con navegación -->
                <div class="calendar-header mb-3">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <button type="button" class="btn btn-primary btn-sm" id="btnPrevMonth" title="Mes anterior">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                        </div>
                        <div class="col text-center">
                            <h4 class="mb-0 current-month text-primary" id="currentMonth"></h4>
                        </div>
                        <div class="col-auto">
                            <button type="button" class="btn btn-primary btn-sm" id="btnNextMonth" title="Mes siguiente">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>
                        <div class="col-auto">
                            <button type="button" class="btn btn-outline-primary btn-sm" id="btnToday">
                                <i class="fas fa-calendar-day me-1"></i>
                                Hoy
                            </button>
                        </div>
                        <div class="col-auto ms-auto">
                            <button type="button" class="btn btn-info btn-sm" id="btnExportPDF">
                                <i class="fas fa-file-pdf me-1"></i>
                                Exportar PDF
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Días de la semana -->
                <div class="calendar-grid">
                    <div class="calendar-weekdays">
                        <div class="weekday">Lunes</div>
                        <div class="weekday">Martes</div>
                        <div class="weekday">Miércoles</div>
                        <div class="weekday">Jueves</div>
                        <div class="weekday">Viernes</div>
                        <div class="weekday">Sábado</div>
                        <div class="weekday">Domingo</div>
                    </div>

                    <!-- Días del mes (se generarán dinámicamente) -->
                    <div class="calendar-days" id="calendarDays"></div>
                </div>

                <!-- Leyenda -->
                <span class="legend-item">
                <span class="legend-color" style="background:#28a745"></span> Aprobado
                </span>
                <span class="legend-item">
                <span class="legend-color" style="background:#17a2b8"></span> En proceso
                </span>
                <span class="legend-item">
                <span class="legend-color" style="background:#ff9b29"></span> Esperando respuesta
                </span>
                <span class="legend-item">
                <span class="legend-color" style="background:#dc3545"></span> Rechazado
                </span>
                <span class="legend-item">
                <span class="legend-color" style="background:#6c757d"></span> Cancelado
                </span>
                <span class="legend-item">
                <span class="legend-color" style="background:#0000ff"></span> Pendiente Revisión
                </span>

            </div><!-- br-section-wrapper -->
        </div><!-- br-pagebody -->

        <footer class="br-footer">
            <?php include_once('../../config/template/mainFooter.php') ?>
        </footer>
    </div><!-- br-mainpanel -->
    <!-- ########## END: MAIN PANEL ########## -->
<!-- MODAL DETALLE DE PRESUPUESTO COMPLETO -->
<div class="modal fade" id="modalDetalleElemento" tabindex="-1" role="dialog" aria-labelledby="modalDetalleElementoLabel" aria-hidden="true">
    <div class="modal-dialog modal-custom" role="document">
        <div class="modal-content shadow-lg border-0">

            <!-- Header con gradiente -->
            <div class="modal-header bg-gradient-primary text-white border-0">
                <h5 class="modal-title font-weight-bold" id="modalDetalleElementoLabel">
                    <i class="fas fa-file-invoice-dollar mr-2"></i>
                    Detalle del Presupuesto
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"   onclick="$('#modalDetalleElemento').modal('hide')">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body p-4" style="max-height: 70vh; overflow-y: auto;">
                <div class="container-fluid">

                    <!-- SECCIÓN PRESUPUESTO -->
                    <div class="card mb-3 border-0 shadow-sm">
                        <div class="card-header bg-light border-bottom">
                            <h6 class="mb-0 text-primary font-weight-bold">
                                <i class="fas fa-calculator mr-2"></i>Información del Presupuesto
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3 mb-3" style="display: none;">
                                    <label class="text-muted small mb-1">ID:</label>
                                    <div class="font-weight-semibold" id="modalIdPresupuesto"></div>
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label class="text-muted small mb-1">Número:</label>
                                    <div class="font-weight-semibold" id="modalNumeroPresupuesto"></div>
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label class="text-muted small mb-1">Fecha:</label>
                                    <div class="font-weight-semibold" id="modalFechaPresupuesto"></div>
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label class="text-muted small mb-1">Validez:</label>
                                    <div class="font-weight-semibold" id="modalFechaValidezPresupuesto"></div>
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label class="text-muted small mb-1">Estado validez:</label>
                                    <div class="font-weight-semibold" id="modalEstadoValidezPresupuesto"></div>
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label class="text-muted small mb-1">Prioridad:</label>
                                    <div class="font-weight-semibold" id="modalPrioridadPresupuesto"></div>
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label class="text-muted small mb-1">Total:</label>
                                    <div class="font-weight-bold text-success h5 mb-0" id="modalImportePresupuesto"></div>
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label class="text-muted small mb-1">Código estado:</label>
                                    <div class="font-weight-semibold" id="modalCodigoEstado"></div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="text-muted small mb-1">Nombre estado:</label>
                                    <div class="font-weight-semibold" id="modalNombreEstado"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECCIÓN EVENTO -->
                    <div class="card mb-3 border-0 shadow-sm">
                        <div class="card-header bg-light border-bottom">
                            <h6 class="mb-0 text-primary font-weight-bold">
                                <i class="fas fa-calendar-alt mr-2"></i>Información del Evento
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="text-muted small mb-1">Nombre:</label>
                                    <div class="font-weight-semibold" id="modalNombreEvento"></div>
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label class="text-muted small mb-1">Inicio:</label>
                                    <div class="font-weight-semibold" id="modalFechaInicioEvento"></div>
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label class="text-muted small mb-1">Fin:</label>
                                    <div class="font-weight-semibold" id="modalFechaFinEvento"></div>
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label class="text-muted small mb-1">Duración (días):</label>
                                    <div class="font-weight-semibold" id="modalDuracionEvento"></div>
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label class="text-muted small mb-1">Estado evento:</label>
                                    <div class="font-weight-semibold" id="modalEstadoEvento"></div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="text-muted small mb-1">Dirección:</label>
                                    <div class="font-weight-semibold" id="modalDireccionEvento"></div>
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label class="text-muted small mb-1">CP:</label>
                                    <div class="font-weight-semibold" id="modalCpEvento"></div>
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label class="text-muted small mb-1">Población:</label>
                                    <div class="font-weight-semibold" id="modalPoblacionEvento"></div>
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label class="text-muted small mb-1">Provincia:</label>
                                    <div class="font-weight-semibold" id="modalProvinciaEvento"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECCIÓN CLIENTE -->
                    <div class="card mb-3 border-0 shadow-sm">
                        <div class="card-header bg-light border-bottom">
                            <h6 class="mb-0 text-primary font-weight-bold">
                                <i class="fas fa-building mr-2"></i>Información del Cliente
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3 mb-3" style="display: none;">
                                    <label class="text-muted small mb-1">ID Cliente:</label>
                                    <div class="font-weight-semibold" id="modalIdCliente"></div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="text-muted small mb-1">Código:</label>
                                    <div class="font-weight-semibold" id="modalCodigoCliente"></div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="text-muted small mb-1">Nombre:</label>
                                    <div class="font-weight-semibold" id="modalNombreCliente"></div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="text-muted small mb-1">NIF:</label>
                                    <div class="font-weight-semibold" id="modalNifCliente"></div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="text-muted small mb-1">Teléfono:</label>
                                    <div class="font-weight-semibold" id="modalTelefonoCliente"></div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="text-muted small mb-1">Email:</label>
                                    <div class="font-weight-semibold" id="modalEmailCliente"></div>
                                </div>
                                <div class="col-md-7 mb-3">
                                    <label class="text-muted small mb-1">Dirección:</label>
                                    <div class="font-weight-semibold" id="modalDireccionCliente"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECCIÓN CONTACTO -->
                    <div class="card mb-3 border-0 shadow-sm">
                        <div class="card-header bg-light border-bottom">
                            <h6 class="mb-0 text-primary font-weight-bold">
                                <i class="fas fa-user mr-2"></i>Persona de Contacto
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label class="text-muted small mb-1">Nombre:</label>
                                    <div class="font-weight-semibold" id="modalNombreContacto"></div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="text-muted small mb-1">Teléfono:</label>
                                    <div class="font-weight-semibold" id="modalTelefonoContacto"></div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="text-muted small mb-1">Cargo:</label>
                                    <div class="font-weight-semibold" id="modalCargoContacto"></div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="text-muted small mb-1">Email:</label>
                                    <div class="font-weight-semibold" id="modalEmailContacto"></div>
                                </div>
                                
                            </div>
                        </div>
                    </div>

                    <!-- SECCIÓN PAGO -->
                    <div class="card mb-3 border-0 shadow-sm">
                        <div class="card-header bg-light border-bottom">
                            <h6 class="mb-0 text-primary font-weight-bold">
                                <i class="fas fa-credit-card mr-2"></i>Condiciones de Pago
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label class="text-muted small mb-1">Tipo pago:</label>
                                    <div class="font-weight-semibold" id="modalTipoPago"></div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="text-muted small mb-1">Forma pago:</label>
                                    <div class="font-weight-semibold" id="modalFormaPago"></div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="text-muted small mb-1">Vencimiento anticipo:</label>
                                    <div class="font-weight-semibold" id="modalFechaVencimientoAnticipo"></div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="text-muted small mb-1">Vencimiento final:</label>
                                    <div class="font-weight-semibold" id="modalFechaVencimientoFinal"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECCIÓN OBSERVACIONES -->
                    <div class="card mb-3 border-0 shadow-sm">
                        <div class="card-header bg-light border-bottom">
                            <h6 class="mb-0 text-primary font-weight-bold">
                                <i class="fas fa-sticky-note mr-2"></i>Observaciones
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="text-muted small mb-1">Cabecera:</label>
                                    <div class="p-2 bg-light rounded" id="modalObsCabecera"></div>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="text-muted small mb-1">Pie:</label>
                                    <div class="p-2 bg-light rounded" id="modalObsPie"></div>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="text-muted small mb-1">Internas:</label>
                                    <div class="p-2 bg-light rounded" id="modalObsInternas"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="modal-footer border-top bg-light">
                <button type="button" class="btn bg-primary btn-secondary px-4" onclick="$('#modalDetalleElemento').modal('hide')">
                    <i class="fas fa-times mr-2"></i>Cerrar
                </button>
            </div>

        </div>
    </div>
</div>


    <!-- ----------------------- -->
    <!--       mainJs.php        -->
    <!-- ----------------------- -->
    <?php include_once('../../config/template/mainJs.php') ?>
    <!-- ------------------------- -->
    <!--     END mainJs.php        -->
    <!-- ------------------------- -->

    <script src="js/calendario.js"></script>
</body>
</html>
