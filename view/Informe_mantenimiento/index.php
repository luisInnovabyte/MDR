<!-- ---------------------- -->
    <!--   Comprobar permisos     -->
    <!-- ---------------------- -->
<?php $moduloActual = 'area_tecnica'; ?>
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
                <span class="breadcrumb-item active">Calendario de Mantenimientos</span>
            </nav>
        </div><!-- br-pageheader -->
        
        <div class="br-pagetitle">
            <div class="d-flex align-items-center">
                <i class="fas fa-tools me-3 text-primary" style="font-size: 2rem;"></i>
                <h4 class="mb-0 me-2">Calendario de Próximos Mantenimientos</h4>
                <button type="button" class="btn btn-link p-0 ms-1" data-bs-toggle="modal" data-bs-target="#modalAyudaMantenimiento" title="Ayuda" style="font-size:1.3rem; color:#6c757d; line-height:1;">
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
                            <button type="button" class="btn btn-success btn-sm" id="btnExportPDF">
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
                                Al día
                            </span>
                            <span class="legend-item me-3">
                                <span class="legend-color bg-warning"></span>
                                Próximo
                            </span>
                            <span class="legend-item">
                                <span class="legend-color bg-danger"></span>
                                Atrasado
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
    <!-- MODAL AYUDA                        -->
    <!-- *********************************** -->
    <div class="modal fade" id="modalAyudaMantenimiento" tabindex="-1" aria-labelledby="modalAyudaMantenimientoLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalAyudaMantenimientoLabel">
                        <i class="fas fa-question-circle me-2"></i>¿Cómo funciona el Calendario de Mantenimientos?
                    </h5>
                    <button type="button" class="close text-white" data-bs-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <p class="text-muted">
                        Este calendario muestra cuándo toca revisar o mantener cada equipo del almacén.
                        De un vistazo puedes saber qué equipos están al día, cuáles tienen el mantenimiento próximo
                        y cuáles llevan un retraso en su revisión.
                    </p>

                    <h6 class="text-primary border-bottom pb-2 mt-4">
                        <i class="fas fa-palette me-2"></i>¿Qué significan los colores?
                    </h6>
                    <div class="row mt-3">
                        <div class="col-md-3 col-6 mb-3 text-center">
                            <span class="badge bg-info d-block py-2 mb-1" style="font-size:.95rem;">Día actual</span>
                            <small class="text-muted">Hoy en el calendario</small>
                        </div>
                        <div class="col-md-3 col-6 mb-3 text-center">
                            <span class="badge bg-success d-block py-2 mb-1" style="font-size:.95rem;">Al día</span>
                            <small class="text-muted">El mantenimiento está en vigor</small>
                        </div>
                        <div class="col-md-3 col-6 mb-3 text-center">
                            <span class="badge bg-warning text-dark d-block py-2 mb-1" style="font-size:.95rem;">Próximo</span>
                            <small class="text-muted">La revisión se acerca</small>
                        </div>
                        <div class="col-md-3 col-6 mb-3 text-center">
                            <span class="badge bg-danger d-block py-2 mb-1" style="font-size:.95rem;">Atrasado</span>
                            <small class="text-muted">La revisión está pendiente</small>
                        </div>
                    </div>

                    <h6 class="text-primary border-bottom pb-2 mt-4">
                        <i class="fas fa-mouse-pointer me-2"></i>¿Cómo navego por el calendario?
                    </h6>
                    <ul class="mt-2" style="line-height:2;">
                        <li>Usa las flechas <strong>&#8249; &#8250;</strong> de la cabecera para ir al mes anterior o siguiente.</li>
                        <li>El botón <strong>Hoy</strong> te lleva directamente al mes actual.</li>
                        <li>Haz clic sobre cualquier día marcado para ver el <strong>detalle</strong> de los equipos con mantenimiento ese día.</li>
                        <li>El botón <strong>Exportar PDF</strong> genera un documento con la vista del mes que estás viendo.</li>
                    </ul>

                    <h6 class="text-primary border-bottom pb-2 mt-4">
                        <i class="fas fa-info-circle me-2"></i>¿De dónde vienen los datos?
                    </h6>
                    <p class="text-muted">
                        Cada equipo del almacén tiene registrada su <strong>fecha de próxima revisión</strong>.
                        El calendario la consulta automáticamente y la coloca en el día correspondiente,
                        coloréándola según si aún está al día, se acerca o ya está atrasada.
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
    <!-- *********************************** -->
    <!-- FIN MODAL AYUDA                    -->
    <!-- *********************************** -->

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

                        <!-- Información de Mantenimiento -->
                        <div class="row mb-3">
                            <div class="col-12">
                                <h6 class="text-primary border-bottom pb-2">
                                    <i class="fas fa-tools me-2"></i>Mantenimiento
                                </h6>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="font-weight-bold">Próximo Mantenimiento:</label>
                                <p id="modalFechaMantenimiento" class="mb-2"></p>
                            </div>
                            <div class="col-md-6">
                                <label class="font-weight-bold">Estado de Mantenimiento:</label>
                                <p id="modalEstadoMantenimiento" class="mb-2"></p>
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
