    <!-- ---------------------- -->
    <!--   Comprobar permisos     -->
    <!-- ---------------------- -->
<?php $moduloActual = 'comerciales'; ?>
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
    <style>
        #modalMantenimiento {
            z-index: 1050 !important;
        }

        .ui-datepicker {
            z-index: 1060 !important;
        }
    </style>
  <!-- ########## START: MAIN PANEL ########## -->
<div class="br-mainpanel">
    <div class="br-pageheader">
        <nav class="breadcrumb pd-0 mg-0 tx-12">
            <a class="breadcrumb-item" href="index.html">Dashboard</a>
            <span class="breadcrumb-item active">Mantenimiento Vacaciones</span>
        </nav>
    </div><!-- br-pageheader -->

    <div class="br-pagetitle">
        <i class="icon icon ion-ios-bookmarks-outline"></i>
        <div>
            <div class="d-flex align-items-center">
            <h4>Mantenimiento de Vacaciones</h4>
            <button type="button" class="btn btn-link p-0 ms-1" data-toggle="modal" data-target="#modalAyudaVacaciones" title="Ayuda sobre el módulo">
                <i class="bi bi-question-circle text-primary" style="font-size: 1.3rem;"></i>
            </button>
            </div>
            <p class="mg-b-0">Tabla básica para el mantenimiento de las vacaciones</p>
        </div>
    </div><!-- d-flex -->

    <div class="br-pagebody">
        <div class="br-section-wrapper">

            <!-- Alerta de filtros y botón de nueva vacación -->
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
                <div class="flex-grow-1 me-3" style="min-width: 300px;">
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

                <button class="btn btn-oblong btn-outline-primary flex-shrink-0 mt-2 mt-sm-0" id="btnnuevo">
                    <i class="fas fa-plus-circle me-2"></i>Nueva Vacación
                </button>
            </div>

            <!-- Acordeón de Filtros -->
            <div id="accordion" class="accordion mb-3">
                <div class="card">
                    <div class="card-header p-0">
                        <h6 class="mg-b-0">
                            <a id="accordion-toggle" 
                               class="d-block p-3 bg-primary text-white collapsed" 
                               data-toggle="collapse" 
                               href="#collapseOne"
                               style="text-decoration: none;">
                                <i class="fas fa-filter me-2"></i>Filtros de Vacaciones
                            </a>
                        </h6>
                    </div>

                    <div id="collapseOne" class="collapse" data-parent="#accordion">
                        <div class="card-body pd-20 pt-3"> <!-- Aquí he añadido pt-3 -->
                            <div class="row g-3">
                                <!-- Bloque Estado -->
                                <div class="col-md-4">
                                    <div class="card shadow-sm h-100">
                                        <div class="card-header bg-white border-bottom py-2">
                                            <h6 class="mb-0 text-primary">
                                                <i class="fas fa-address-book me-2"></i>Vacaciones de Comercialesy
                                            </h6>
                                        </div>
                                        <div class="card-body p-2">
                                            <div class="status-selector">
                                                <div class="status-option">
                                                    <input type="radio" name="filterStatus" id="filterAll" value="all" class="status-radio">
                                                    <label for="filterAll" class="status-label">
                                                        <span class="status-icon"><i class="fas fa-layer-group"></i></span>
                                                        <span class="status-text">Todos</span>
                                                        <span class="status-badge"></span>
                                                    </label>
                                                </div>
                                                <div class="status-option">
                                                    <input type="radio" name="filterStatus" id="filterActive" value="1" checked class="status-radio">
                                                    <label for="filterActive" class="status-label">
                                                        <span class="status-icon"><i class="fas fa-check-circle"></i></span>
                                                        <span class="status-text">Activos</span>
                                                        <span class="status-badge"></span>
                                                    </label>
                                                </div>
                                                <div class="status-option">
                                                    <input type="radio" name="filterStatus" id="filterInactive" value="0" class="status-radio">
                                                    <label for="filterInactive" class="status-label">
                                                        <span class="status-icon"><i class="fas fa-times-circle"></i></span>
                                                        <span class="status-text">Inactivos</span>
                                                        <span class="status-badge"></span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Bloque Fechas -->
                                <div class="col-md-4">
                                    <div class="card shadow-sm h-100">
                                        <div class="card-header bg-white border-bottom py-2">
                                            <h6 class="mb-0 text-primary">
                                                <i class="fas fa-calendar-day me-2"></i>Rango de Fechas
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row g-2">
                                                <div class="col-12 col-sm-6">
                                                    <label class="form-label small text-muted">Fecha Inicio</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text bg-light"><i class="far fa-calendar-alt"></i></span>
                                                        <input id="filtroFechaInicio" type="text" class="form-control datepicker" placeholder="dd-mm-aaaa" readonly>
                                                    </div>
                                                    <span class="text-info small mt-1 d-block cursor-pointer" id="borrarFechaInicioFiltro">
                                                        <i class="fas fa-times-circle"></i> Borrar fecha inicio
                                                    </span>
                                                </div>
                                                <div class="col-12 col-sm-6">
                                                    <label class="form-label small text-muted">Fecha Fin</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text bg-light"><i class="far fa-calendar-alt"></i></span>
                                                        <input id="filtroFechaFin" type="text" class="form-control datepicker" placeholder="dd-mm-aaaa" readonly>
                                                    </div>
                                                    <span class="text-info small mt-1 d-block cursor-pointer" id="borrarFechaFinFiltro">
                                                        <i class="fas fa-times-circle"></i> Borrar fecha fin
                                                    </span>
                                                </div>
                                                <div class="col-12 mt-2">
                                                    <div class="d-flex gap-2">
                                                        <button id="btnFiltrarFecha" class="btn btn-primary flex-grow-1">
                                                            <i class="fas fa-filter me-1"></i> Aplicar
                                                        </button>
                                                        <button id="btnQuitarFiltro" class="btn btn-outline-danger" style="width: 42px;" title="Quitar filtro">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Bloque Comercial -->
                                <div class="col-md-4">
                                    <div class="card shadow-sm h-100 border-0">
                                        <div class="card-header bg-transparent border-0 pb-0">
                                            <h6 class="text-primary fw-normal">
                                                <i class="fas fa-user-tie me-2 text-muted"></i>Comercial
                                            </h6>
                                        </div>
                                        <div class="card-body d-flex align-items-center pt-2">
                                            <select id="filtroComercial" class="form-select border-0 border-bottom rounded-0 py-2 px-1 bg-transparent">
                                                <option value="" selected>Todos los comerciales</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div> <!-- row -->
                        </div>
                    </div>
                </div>
            </div><!-- accordion -->

            <!-- Tabla de vacaciones -->
            <div class="table-wrapper table order-column hover row-border stripe responsive">
                <table id="vacaciones_data" class="table display responsive nowrap">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Id Vacacion</th>
                            <th>Comercial</th>
                            <th>Fecha Inicio</th>
                            <th>Fecha Fin</th>
                            <th>Descripción</th>
                            <th>Tiene vacaciones?</th>
                            <th>Act./Desac.</th>
                            <th>Edit.</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Aquí se cargan los datos de la tabla -->
                    </tbody>
                    <tfoot>
                        <tr>
                            <th></th>
                            <th class="d-none"><input type="text" placeholder="ID" class="form-control form-control-sm" disabled /></th>
                            <th><input type="text" placeholder="Buscar Comercial" class="form-control form-control-sm" /></th>
                            <th><input type="text" id="fechaInicioFiltroPies" placeholder="Buscar fecha inicio: dd-mm-yyyy" class="form-control form-control-sm" /></th>
                            <th><input type="text" id="fechaFinFiltroPies" placeholder="Buscar fecha fin: dd-mm-yyyy" class="form-control form-control-sm" /></th>
                            <th class="d-none"><input type="text" placeholder="Descripción" class="form-control form-control-sm" disabled /></th>
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

<!-- ------------------------- -->
<!--   END mainFooter.php      -->
<!-- ------------------------- -->
<!-- ------------------------- -->
<!--   END mainFooter.php      -->
<!-- ------------------------- -->
    <!-- ########## END: MAIN PANEL ########## -->

    <!-- *********************************** -->
    <!-- MODAL QUE SE DISPARA DESDE EL BOTON -->
    <!--        DE LA PROPIA TABLA           -->
    <!--        columDef                     -->
    <!-- *********************************** -->

    <?php //include_once('detalleProductoBra.php') ?>

    <!-- *********************************** -->
    <!--                FIN                  -->
    <!-- MODAL QUE SE DISPARA DESDE EL BOTON -->
    <!--        DE LA PROPIA TABLA           -->
    <!--             columDef                -->
    <!-- *********************************** -->


    <!-- Modal ayuda -->
    <?php include_once('ayudaVacaciones.php') ?>
    <!-- Fin modal ayuda -->

    <!-- *********************************** -->
    <!-- MODAL QUE SE DISPARA DESDE EL BOTON -->
    <!--             NUEVO                   -->
    <!-- *********************************** -->

    <?php include_once('mantenimientoVacaciones.php') ?>


    <!-- *********************************** -->
    <!--                FIN                  -->
    <!-- MODAL QUE SE DISPARA DESDE EL BOTON -->
    <!--               NUEVO                 -->
    <!-- *********************************** -->

    <!-- ----------------------- -->
    <!--       mainJs.php        -->
    <!-- ----------------------- -->
    <?php include_once('../../config/template/mainJs.php') ?>

    <script src="../../public/js/tooltip-colored.js"></script>
    <script src="../../public/js/popover-colored.js"></script>
    <!-- ------------------------- -->
    <!--     END mainJs.php        -->
    <!-- ------------------------- -->
    <script type="text/javascript" src="mntvacaciones.js"></script>

</body>

</html>