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
                <button type="button" class="btn btn-link p-0 ms-1" data-bs-toggle="modal" data-bs-target="#modalAyudaPpto" title="Ayuda" style="font-size:1.3rem; color:#6c757d; line-height:1;">
                    <i class="fas fa-question-circle"></i>
                </button>
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
<!-- MODAL AYUDA -->
<div class="modal fade" id="modalAyudaPpto" tabindex="-1" aria-labelledby="modalAyudaPptoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalAyudaPptoLabel">
                    <i class="fas fa-question-circle me-2"></i>¿Cómo funciona el Calendario de Presupuestos?
                </h5>
                <button type="button" class="close text-white" data-bs-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <p class="text-muted">
                    Este calendario muestra <strong>la duración completa de los eventos</strong> de los presupuestos.
                    Cada presupuesto ocupa todos los días comprendidos entre su fecha de inicio y su fecha de fin de evento,
                    coloreado según el estado en que se encuentra, <strong>independientemente del estado</strong>.
                </p>

                <h6 class="text-primary border-bottom pb-2 mt-4">
                    <i class="fas fa-palette me-2"></i>¿Qué significan los colores?
                </h6>
                <div class="row mt-3">
                    <div class="col-md-4 col-6 mb-3 text-center">
                        <span class="badge d-block py-2 mb-1" style="font-size:.9rem; background:#28a745;">Aprobado</span>
                        <small class="text-muted">Presupuesto aceptado por el cliente</small>
                    </div>
                    <div class="col-md-4 col-6 mb-3 text-center">
                        <span class="badge d-block py-2 mb-1" style="font-size:.9rem; background:#17a2b8;">En proceso</span>
                        <small class="text-muted">En elaboración o negociación</small>
                    </div>
                    <div class="col-md-4 col-6 mb-3 text-center">
                        <span class="badge d-block py-2 mb-1" style="font-size:.9rem; background:#ff9b29;">Esperando respuesta</span>
                        <small class="text-muted">Enviado, pendiente de confirmación</small>
                    </div>
                    <div class="col-md-4 col-6 mb-3 text-center">
                        <span class="badge d-block py-2 mb-1" style="font-size:.9rem; background:#dc3545;">Rechazado</span>
                        <small class="text-muted">El cliente no lo ha aceptado</small>
                    </div>
                    <div class="col-md-4 col-6 mb-3 text-center">
                        <span class="badge d-block py-2 mb-1" style="font-size:.9rem; background:#6c757d;">Cancelado</span>
                        <small class="text-muted">Servicio cancelado</small>
                    </div>
                    <div class="col-md-4 col-6 mb-3 text-center">
                        <span class="badge d-block py-2 mb-1" style="font-size:.9rem; background:#0000ff;">Pendiente revisión</span>
                        <small class="text-muted">Pendiente de revisar antes de enviar</small>
                    </div>
                </div>

                <h6 class="text-primary border-bottom pb-2 mt-4">
                    <i class="fas fa-mouse-pointer me-2"></i>¿Cómo navego por el calendario?
                </h6>
                <ul class="mt-2" style="line-height:2;">
                    <li>Usa las flechas <strong>&#8249; &#8250;</strong> para moverte entre meses.</li>
                    <li>El botón <strong>Hoy</strong> te devuelve al mes actual.</li>
                    <li>Haz clic sobre un día con presupuestos para ver su <strong>detalle completo</strong>: cliente, evento, importe, condiciones de pago y observaciones.</li>
                    <li>El botón <strong>Exportar PDF</strong> genera un documento con el mes visible.</li>
                </ul>

                <h6 class="text-primary border-bottom pb-2 mt-4">
                    <i class="fas fa-info-circle me-2"></i>¿De dónde vienen los datos?
                </h6>
                <p class="text-muted">
                    Cada presupuesto tiene asignada una <strong>fecha de inicio</strong> y una <strong>fecha de fin de evento</strong>.
                    El calendario toma automáticamente ese rango y muestra el presupuesto en <strong>todos los días que dura el evento</strong>,
                    coloreado con el estado actual. Los presupuestos aparecen en el calendario sea cual sea su estado.
                </p>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cerrar
                </button>
            </div>
        </div>
    </div>
</div>
<!-- FIN MODAL AYUDA -->

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
