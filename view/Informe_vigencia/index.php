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
                <span class="breadcrumb-item active">Calendario de Vigencias</span>
            </nav>
        </div><!-- br-pageheader -->
        
        <div class="br-pagetitle">
            <div class="d-flex align-items-center">
                <i class="fas fa-calendar-check me-3 text-primary" style="font-size: 2rem;"></i>
                <h4 class="mb-0 me-2">Calendario de Vigencias de Garantías</h4>
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
                <div class="calendar-legend mt-3">
                    <div class="card">
                        <div class="card-body p-2">
                            <strong class="me-3">Leyenda:</strong>
                            <span class="legend-item me-3">
                                <span class="legend-color bg-info"></span>
                                Día actual
                            </span>
                            <span class="legend-item me-3">
                                <span class="legend-color bg-success"></span>
                                Vigente
                            </span>
                            <span class="legend-item me-3">
                                <span class="legend-color bg-warning"></span>
                                Por vencer
                            </span>
                            <span class="legend-item">
                                <span class="legend-color bg-danger"></span>
                                Vencida
                            </span>
                        </div>
                    </div>
                </div>

            </div><!-- br-section-wrapper -->
        </div><!-- br-pagebody -->

        <footer class="br-footer">
            <?php include_once('../../config/template/mainFooter.php') ?>
        </footer>
    </div><!-- br-mainpanel -->
    <!-- ########## END: MAIN PANEL ########## -->

    <!-- *********************************** -->
    <!-- MODAL DETALLE DE ELEMENTO          -->
    <!-- *********************************** -->
    <div class="modal fade" id="modalDetalleElemento" tabindex="-1" role="dialog" aria-labelledby="modalDetalleElementoLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalDetalleElementoLabel">
                        <i class="fas fa-info-circle me-2"></i>
                        Detalle del Elemento
                    </h5>
                    <button type="button" class="close text-white" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <!-- Información del Elemento -->
                        <div class="row mb-3">
                            <div class="col-12">
                                <h6 class="text-primary border-bottom pb-2">
                                    <i class="fas fa-box me-2"></i>Información del Elemento
                                </h6>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="font-weight-bold">Código:</label>
                                <p id="modalCodigoElemento" class="mb-2"></p>
                            </div>
                            <div class="col-md-6">
                                <label class="font-weight-bold">Descripción:</label>
                                <p id="modalDescripcionElemento" class="mb-2"></p>
                            </div>
                        </div>

                        <!-- Información del Artículo -->
                        <div class="row mb-3">
                            <div class="col-12">
                                <h6 class="text-primary border-bottom pb-2">
                                    <i class="fas fa-cube me-2"></i>Artículo y Familia
                                </h6>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="font-weight-bold">Artículo:</label>
                                <p id="modalNombreArticulo" class="mb-2"></p>
                            </div>
                            <div class="col-md-6">
                                <label class="font-weight-bold">Familia:</label>
                                <p id="modalNombreFamilia" class="mb-2"></p>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="font-weight-bold">Marca:</label>
                                <p id="modalNombreMarca" class="mb-2"></p>
                            </div>
                            <div class="col-md-6">
                                <label class="font-weight-bold">Grupo:</label>
                                <p id="modalNombreGrupo" class="mb-2"></p>
                            </div>
                        </div>

                        <!-- Información de Garantía -->
                        <div class="row mb-3">
                            <div class="col-12">
                                <h6 class="text-primary border-bottom pb-2">
                                    <i class="fas fa-shield-alt me-2"></i>Garantía
                                </h6>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="font-weight-bold">Fecha Fin de Garantía:</label>
                                <p id="modalFechaGarantia" class="mb-2"></p>
                            </div>
                            <div class="col-md-6">
                                <label class="font-weight-bold">Estado de Garantía:</label>
                                <p id="modalEstadoGarantia" class="mb-2"></p>
                            </div>
                        </div>

                        <!-- Ubicación -->
                        <div class="row mb-3">
                            <div class="col-12">
                                <h6 class="text-primary border-bottom pb-2">
                                    <i class="fas fa-map-marker-alt me-2"></i>Ubicación
                                </h6>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4" id="modalNaveContainer">
                                <label class="font-weight-bold">Nave:</label>
                                <p id="modalNave" class="mb-2"></p>
                            </div>
                            <div class="col-md-4" id="modalPasilloContainer">
                                <label class="font-weight-bold">Pasillo/Columna:</label>
                                <p id="modalPasillo" class="mb-2"></p>
                            </div>
                            <div class="col-md-4" id="modalAlturaContainer">
                                <label class="font-weight-bold">Altura:</label>
                                <p id="modalAltura" class="mb-2"></p>
                            </div>
                        </div>

                        <!-- Información Adicional -->
                        <div class="row mb-3" id="modalInfoAdicional">
                            <div class="col-12">
                                <h6 class="text-primary border-bottom pb-2">
                                    <i class="fas fa-info me-2"></i>Información Adicional
                                </h6>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6" id="modalModeloContainer">
                                <label class="font-weight-bold">Modelo:</label>
                                <p id="modalModelo" class="mb-2"></p>
                            </div>
                            <div class="col-md-6" id="modalSerieContainer">
                                <label class="font-weight-bold">Número de Serie:</label>
                                <p id="modalSerie" class="mb-2"></p>
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
    <!-- *********************************** -->
    <!-- FIN MODAL DETALLE DE ELEMENTO      -->
    <!-- *********************************** -->

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
