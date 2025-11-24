    <!-- ---------------------- -->
    <!--   Comprobar permisos     -->
    <!-- ---------------------- -->
<?php $moduloActual = 'llamadas'; ?>
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
 <div class="br-mainpanel">
    <div class="br-pageheader">
        <nav class="breadcrumb pd-0 mg-0 tx-12">
            <a class="breadcrumb-item" href="index.html">Dashboard</a>
            <span class="breadcrumb-item active">Mantenimiento Adjunto Llamadas</span>
        </nav>
    </div><!-- br-pageheader -->

    <div class="br-pagetitle">
        <i class="icon icon ion-ios-bookmarks-outline"></i>
        <div>
            <h4>Mantenimiento de Adjuntos llamadas</h4>
            <p class="mg-b-0">Tabla básica para el mantenimiento de los adjuntos llamadas</p>
        </div>
    </div><!-- d-flex -->

    <div class="br-pagebody">
        <div class="br-section-wrapper">
            <!-- Fila contenedora -->
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
                <!-- Contenedor de alerta expandible -->
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

                <!-- Botón Nuevo Adjunto 
                <button class="btn btn-oblong btn-outline-primary flex-shrink-0 mt-2 mt-sm-0" id="btnnuevo">
                    <i class="fas fa-plus-circle me-2"></i>Nuevo Adjunto
                </button>
                -->
            </div>

            <!-- Filtros con desplegable -->
            <div id="accordion" class="accordion mb-3">
                <div class="card">
                    <div class="card-header p-0">
                        <h6 class="mg-b-0">
                            <a id="accordion-toggle" 
                               class="d-block p-3 bg-primary text-white collapsed" 
                               data-toggle="collapse" 
                               href="#collapseOne"
                               style="text-decoration: none;">
                                <i class="fas fa-filter me-2"></i>Filtros de Adjuntos Llamadas
                            </a>
                        </h6>
                    </div>

                    <div id="collapseOne" class="collapse" data-parent="#accordion">
                        <div class="card-body pd-20 pt-3"> <!-- Aquí he añadido pt-3 -->
                            <div class="row g-3">
                                <!-- Filtro de Estado -->
                                <div class="col-md-4">
                                    <div class="card shadow-sm h-100">
                                        <div class="card-header bg-white border-bottom py-2">
                                            <h6 class="mb-0 text-primary">
                                                <i class="fas fa-toggle-on me-2"></i>Estado Adjuntos
                                            </h6>
                                        </div>
                                        <div class="card-body p-2">
                                            <div class="status-selector">
                                                <div class="status-option">
                                                    <input type="radio" name="filterStatus" id="filterAll" value="all" class="status-radio" checked>
                                                    <label for="filterAll" class="status-label">
                                                        <span class="status-icon"><i class="fas fa-layer-group"></i></span>
                                                        <span class="status-text">Todos</span>
                                                    </label>
                                                </div>
                                                <div class="status-option">
                                                    <input type="radio" name="filterStatus" id="filterActive" value="1" class="status-radio">
                                                    <label for="filterActive" class="status-label">
                                                        <span class="status-icon"><i class="fas fa-check-circle"></i></span>
                                                        <span class="status-text">Activado</span>
                                                    </label>
                                                </div>
                                                <div class="status-option">
                                                    <input type="radio" name="filterStatus" id="filterInactive" value="0" class="status-radio">
                                                    <label for="filterInactive" class="status-label">
                                                        <span class="status-icon"><i class="fas fa-times-circle"></i></span>
                                                        <span class="status-text">Desactivado</span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                               <!-- Filtro Desplegable con Icono -->
                                <div class="col-md-4">
                                    <div class="card shadow-sm h-100">
                                        <div class="card-header bg-white border-bottom py-2">
                                            <h6 class="mb-0 text-primary">
                                                <i class="fas fa-filter me-2"></i>Filtro Tipo Archivo
                                            </h6>
                                        </div>
                                        <div class="card-body p-2 d-flex justify-content-center align-items-center" style="height: 100%;"> <!-- Centrado completo (horizontal y vertical) -->
                                            <!-- Contenedor del select con icono -->
                                            <div class="input-group" style="width: 80%;"> <!-- Ajustamos el ancho del select -->
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">
                                                        <i class="fas fa-file-alt"></i> <!-- Icono de archivo -->
                                                    </span>
                                                </div>
                                                <select class="form-control" id="filtroTipoArchivo">
                                                    <option value="">Seleccione un Tipo</option>
                                                    <option value="1">PDF</option>
                                                    <option value="2">Imagen</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <!-- Filtro Fecha Subida -->
                                <div class="col-md-4">
                                    <div class="card shadow-sm h-100">
                                        <div class="card-header bg-white border-bottom py-2">
                                            <h6 class="mb-0 text-primary">
                                                <i class="fas fa-calendar-alt me-2"></i>Fecha Subida
                                            </h6>
                                        </div>
                                        <div class="card-body p-2 d-flex flex-column justify-content-center">
                                            <div class="input-group mx-auto" style="width: 95%">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">
                                                        <i class="fa fa-calendar tx-16 lh-0 op-6"></i>
                                                    </div>
                                                </div>
                                                <input id="filtroFechaSubida" type="text" class="form-control fc-datepicker" placeholder="dd-mm-aaaa" readonly>
                                            </div>
                                            <div class="tx-8 tx-info mt-2 text-center" id="borrarFechaSubidaFiltro">
                                                <i class="fas fa-trash-alt me-1"></i> Borrar fecha
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div><!-- card -->
            </div><!-- accordion -->

            <!-- Tabla de Adjuntos Llamadas -->
            <div class="row">
                <div class="col-md-12">
                    <div class="table-wrapper">
                        <table id="adjuntos_data" class="table display responsive nowrap" style="width:100%">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Id Adjunto</th>
                                    <th>Nombre Comunicante</th>
                                    <th>Nombre Archivo</th>
                                    <th>Tipo</th>
                                    <th>Fecha Subida</th>
                                    <th>Estado</th>
                                    <th>Ver</th>
                                    <th>Act/Des</th>
                                    <!-- <th>Edit.</th> -->
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Aquí se cargarán los datos dinámicamente -->
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th><input type="text" placeholder="Buscar ID" class="d-none" /></th>
                                    <th><input type="text" placeholder="Buscar ID" class="d-none" /></th>
                                    <th><input type="text" placeholder="Buscar Nombre Comunicante" /></th>
                                    <th><input type="text" placeholder="Buscar Nombre Archivo" /></th>
                                    <th><input type="text" placeholder="Buscar Tipo Archivo" /></th>
                                    <th><input type="text" id="fechaSubidaFiltroPie" placeholder="dd-mm-yyyy hh:mm:ss" /></th>
                                    <th><input type="text" placeholder="NO Buscar" class="d-none" /></th>
                                    <th><input type="text" placeholder="NO Buscar" class="d-none" /></th>
                                    <th><input type="text" placeholder="NO Buscar" class="d-none" /></th>
                                    <!-- <th><input type="text" placeholder="NO Buscar" class="d-none" /></th> -->
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div><!-- row -->
        </div><!-- br-section-wrapper -->
    </div><!-- br-pagebody -->

    <footer class="br-footer">
        <?php include_once('../../config/template/mainFooter.php') ?>
    </footer>
</div><!-- br-mainpanel -->

        <!-- ----------------------- -->
        <!--     mainFooter.php      -->
        <!-- ----------------------- -->
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


    <!-- *********************************** -->
    <!-- MODAL QUE SE DISPARA DESDE EL BOTON -->
    <!--             NUEVO                   -->
    <!-- *********************************** -->

    <?php include_once('mantenimientoAdjuntos.php') ?>
    <?php include_once('mantenimientoVerAdjunto.php') ?>

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
    <script type="text/javascript" src="mntadjuntos.js"></script>

</body>

</html>