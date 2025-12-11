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
    <style>

    /* Permite que el dropdown pueda mostrarse fuera del modal-dialog */
#modalMantenimiento .modal-dialog,
#modalMantenimiento .modal-content {
  overflow: visible !important;
}

/* También el modal mismo */
#modalMantenimiento {
  overflow: visible !important;
}


    </style>
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
            <span class="breadcrumb-item active">Mantenimiento Responsables atención</span>
        </nav>
    </div><!-- br-pageheader -->

    <div class="br-pagetitle">
        <i class="icon icon ion-ios-bookmarks-outline"></i>
        <div>
            <div class="d-flex align-items-center">
            <h4>Mantenimiento de Responsables de atención</h4>
            <button type="button" class="btn btn-link p-0 ms-1" data-toggle="modal" data-target="#modalAyudaComerciales" title="Ayuda sobre el módulo">
                <i class="bi bi-question-circle text-primary" style="font-size: 1.3rem;"></i>
            </button>
            </div>
            <p class="mg-b-0">Tabla básica para el mantenimiento de los responsables de atención</p>
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

                <!-- Botón Nuevo Comercial -->
                <button class="btn btn-oblong btn-outline-primary flex-shrink-0 mt-2 mt-sm-0" id="btnnuevo">
                    <i class="fas fa-plus-circle me-2"></i>Nuevo Responsable
                </button>
            </div>


            <!-- Tabla de Comerciales -->
            <div class="row">
                <div class="col-md-12">
                    <div class="table-wrapper">
                        <table id="comerciales_data" class="table display responsive nowrap" style="width:100%">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Id Responsable</th>
                                    <th>Nombre</th>
                                    <th>Apellidos</th>
                                    <th>Movil</th>
                                    <th>Telefono</th>
                                    <th>Email</th>
                                    <th>Activo</th>
                                    <th>Act./Desac.</th>
                                    <th>Edit.</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Aquí se cargarán los datos dinámicamente -->
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th><input type="text" placeholder="Buscar Nombre" class="form-control form-control-sm" /></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th><input type="text" placeholder="Activo (1/0)" class="form-control form-control-sm" /></th>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

        </div><!-- br-section-wrapper -->
    </div><!-- br-pagebody -->

    <footer class="br-footer">
        <?php include_once('../../config/template/mainFooter.php') ?>
    </footer>
</div><!-- br-mainpanel -->

        <!-- ------------------------- -->
        <!--   END mainFooter.php      -->
        <!-- ------------------------- -->

    </div><!-- br-mainpanel -->
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

    <?php include_once('ayudaComerciales.php') ?>
    <!-- *********************************** -->
    <!-- MODAL QUE SE DISPARA DESDE EL BOTON -->
    <!--             NUEVO                   -->
    <!-- *********************************** -->

    <?php include_once('mantenimientoComercial.php') ?>


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
    <script type="text/javascript" src="mntcomercial.js"></script>

</body>

</html>