    <!-- ---------------------- -->
    <!--   Comprobar permisos     -->
    <!-- ---------------------- -->
<?php $moduloActual = 'logs'; ?>
<?php require_once('../../config/template/verificarPermiso.php'); ?>


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

    <!-- ########## START: MAIN PANEL ########## -->
    <div class="br-mainpanel">
        <div class="br-pageheader">
            <nav class="breadcrumb pd-0 mg-0 tx-12">
                <a class="breadcrumb-item" href="../Dashboard/index.php">Dashboard</a>
                <span class="breadcrumb-item active">Logs</span>
            </nav>
        </div><!-- br-pageheader -->
        <div class="br-pagetitle">
            <i class="icon icon ion-ios-bookmarks-outline"></i>
            <div>
                <h4>Consulta de logs</h4>
                <p class="mg-b-0">Consulta del sistema automático de control de LOGS</p>
            </div>
        </div><!-- d-flex -->

        <div class="br-pagebody">
            <div class="br-section-wrapper">
                <!-- <h6 class="br-section-label">Basic Responsive DataTable</h6>
                <p class="br-section-text">Searching, ordering and paging goodness will be immediately added to the
                    table, as
                    shown in this example.</p> -->

                <!-- Botones de radio para filtrar -->
                <div class="d-flex justify-content-start align-items-start mb-3">
                    <div class="radio-container" style="margin-left: 20px; border: 1px solid #ccc; padding: 5px; margin-left:70px;">
                        <div class="form-check form-check-inline">
                            <input class=" form-check-input" type="radio" name="filterStatus" id="filterInfo" value="info" checked>
                            <label class="form-check-label" for="filterInfo">Info <i class="fa-solid fa-circle-info text-info tx-24"></i></label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="filterStatus" id="filterError" value="error">
                            <label class="form-check-label" for="filterError">Error <i class="fa-solid fa-circle-exclamation text-danger tx-24"></i></label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="filterStatus" id="filterWarning" value="warning">
                            <label class="form-check-label" for="filterWarning">Warning <i class="fa-solid fa-triangle-exclamation text-warning tx-24"></i></label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="filterStatus" id="filterAll" value="all">
                            <label class="form-check-label" for="filterAll">Todos</label>
                        </div>
                    </div>

                    <div class="col-7 col-lg-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <i class="fa fa-calendar tx-16 lh-0 op-6"></i>
                                </div>
                            </div>
                            <input id="dateCreateFilter" type="text" class="form-control fc-datepicker" placeholder="dd-mm-aaaa" readonly>
                        </div><!-- input-group -->
                        <div class="d-flex justify-content-between">
                            <div class="tx-10 tx-info" id="borrarFechaFiltro">Borrar fecha</div>
                            <div class="tx-10 tx-info"> Introduzca la fecha </div>
                        </div>
                    </div><!-- col-4 -->

                </div>

                <!-- Para mostrar en el caso de un filtro activo -->
                <div class="alert alert-warning" role="alert" id="filter-alert">
                    <i class="bi bi-funnel"></i> Hay un filtro activo.
                    <button type="button" class="btn btn-primary" id="clear-filter">Limpiar</button>
                </div>

                <div class="table-wrapper" class="table, order-colum, hover, row-border, stripe responsive">
                    <table id="logs_data" class="table display responsive nowrap">
                        <thead>
                            <tr>
                                <th>Usuario</th>
                                <th>Pantalla</th>
                                <th>Actividad</th>
                                <th>Mensaje</th>
                                <th class="text-center">Tipo</th>
                                <th>Fecha/Hora</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                        <tfoot>
                            <tr>
                                <!-- USUARIO -->
                                <th><input type="text" placeholder="Usuario " /></th>
                                <th><input type="text" placeholder="Pantalla" /></th>
                                <th><input type="text" placeholder="Actividad" /></th>
                                <th><input type="text" placeholder="Mensaje" /></th>
                                <th><input type="text" placeholder="info, error, warning" /></th>
                                <th><input type="text" placeholder="Fecha-Hora" /></th>
                            </tr>
                        </tfoot>
                    </table>
                </div><!-- table-wrapper -->
            </div><!-- br-section-wrapper -->
        </div><!-- br-pagebody -->

        <!-- ----------------------- -->
        <!--     mainFooter.php      -->
        <!-- ----------------------- -->
        <footer class="br-footer">
            <?php include_once('../../config/template/mainFooter.php') ?>
        </footer>
        <!-- ------------------------- -->
        <!--   END mainFooter.php      -->
        <!-- ------------------------- -->

    </div><!-- br-mainpanel -->
    <!-- ########## END: MAIN PANEL ########## -->



    <!-- ----------------------- -->
    <!--       mainJs.php        -->
    <!-- ----------------------- -->
    <?php include_once('../../config/template/mainJs.php') ?>

    <!-- ------------------------- -->
    <!--     END mainJs.php        -->
    <!-- ------------------------- -->
    <script type="text/javascript" src="logs.js"></script>

</body>

</html>