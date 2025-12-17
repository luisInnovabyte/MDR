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

            </div><!-- br-section-wrapper -->
        </div><!-- br-pagebody -->

        <footer class="br-footer">
            <?php include_once('../../config/template/mainFooter.php') ?>
        </footer>
    </div><!-- br-mainpanel -->
    <!-- ########## END: MAIN PANEL ########## -->

   <!-- MODAL DETALLE DE PRESUPUESTO -->
<div class="modal fade" id="modalDetalleElemento" tabindex="-1" role="dialog" aria-labelledby="modalDetalleElementoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalDetalleElementoLabel">
                    <i class="fas fa-info-circle me-2"></i>
                    Detalle del Presupuesto
                </h5>
                <button type="button" class="close text-white" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">

                    <!-- Información del presupuesto -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="font-weight-bold">Número:</label>
                            <p id="modalNumeroPresupuesto" class="mb-2"></p>
                        </div>
                        <div class="col-md-6">
                            <label class="font-weight-bold">Cliente:</label>
                            <p id="modalClientePresupuesto" class="mb-2"></p>
                        </div>
                    </div>

                    <!-- Evento -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="font-weight-bold">Nombre Evento:</label>
                            <p id="modalNombreEvento" class="mb-2"></p>
                        </div>
                        <div class="col-md-6">
                            <label class="font-weight-bold">Estado:</label>
                            <p id="modalEstadoPresupuesto" class="mb-2"></p>
                        </div>
                    </div>

                    <!-- Importe -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="font-weight-bold">Importe:</label>
                            <p id="modalImportePresupuesto" class="mb-2"></p>
                        </div>
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cerrar
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
