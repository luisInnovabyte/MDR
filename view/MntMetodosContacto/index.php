    <!-- ---------------------- -->
    <!--   Comprobar permisos     -->
    <!-- ---------------------- -->
<?php $moduloActual = 'mantenimientos'; ?>
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
            <a class="breadcrumb-item" href="../Dashboard/index.php">Dashboard</a>
            <span class="breadcrumb-item active">Mantenimiento Metodos</span>
        </nav>
    </div><!-- br-pageheader -->
    
    <div class="br-pagetitle">
        <i class="icon icon ion-ios-bookmarks-outline"></i>
        <div>
            <div class="d-flex align-items-center">
            <h4>Mantenimiento de Metodos</h4>
            <button type="button" class="btn btn-link p-0 ms-1" data-toggle="modal" data-target="#modalAyudaMetodos" title="Ayuda sobre el módulo">
                    <i class="bi bi-question-circle text-primary" style="font-size: 1.3rem;"></i>
                </button>
            </div>
            <p class="mg-b-0">Tabla básica para el mantenimiento de los metodos</p>
        </div>
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

            <!-- Botón Nuevo método (se mantiene a la derecha) -->
            <button class="btn btn-oblong btn-outline-primary flex-shrink-0 mt-2 mt-sm-0" id="btnnuevo">
                <i class="fas fa-plus-circle me-2"></i>Nuevo método
            </button>
        </div>

              <!-- Acordeón -->
        <div id="accordion" class="accordion mb-3">
            <div class="card">
                <div class="card-header p-0">
                    <h6 class="mg-b-0">
                        <a id="accordion-toggle" 
                           class="d-block p-3 bg-primary text-white collapsed" 
                           data-toggle="collapse" 
                           href="#collapseOne"
                           style="text-decoration: none;">
                            <i class="fas fa-filter me-2"></i>Filtros de Métodos
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
                                                <i class="fas fa-address-book me-2"></i>Métodos de Contacto
                                            </h6>
                                        </div>
                                        <div class="card-body p-2">
                                            <div class="status-selector">
                                                <div class="status-option">
                                                    <input type="radio" name="filterStatus" id="filterAll" value="all" class="status-radio" checked>
                                                    <label for="filterAll" class="status-label">
                                                        <span class="status-icon">
                                                            <i class="fas fa-layer-group"></i>
                                                        </span>
                                                        <span class="status-text">Todos</span>
                                                        <span class="status-badge"></span>
                                                    </label>
                                                </div>
                                                
                                                <div class="status-option">
                                                    <input type="radio" name="filterStatus" id="filterActive" value="1" class="status-radio">
                                                    <label for="filterActive" class="status-label">
                                                        <span class="status-icon">
                                                            <i class="fas fa-check-circle"></i>
                                                        </span>
                                                        <span class="status-text">Activado</span>
                                                        <span class="status-badge"></span>
                                                    </label>
                                                </div>
                                                
                                                <div class="status-option">
                                                    <input type="radio" name="filterStatus" id="filterInactive" value="0" class="status-radio">
                                                    <label for="filterInactive" class="status-label">
                                                        <span class="status-icon">
                                                            <i class="fas fa-times-circle"></i>
                                                        </span>
                                                        <span class="status-text">Desactivado</span>
                                                        <span class="status-badge"></span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Espacio para otros filtros -->
                                <div class="col-md-8">
                                    <!-- Espacio para filtros adicionales -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div><!-- card -->
            </div><!-- accordion -->

            <!-- Tabla -->
            <div class="row">
                <div class="col-md-12">
                    <div class="table-wrapper">
                        <table id="metodos_data" class="table display responsive nowrap" style="width:100%">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Id Método</th>
                                    <th>Nombre</th>
                                    <th>Estado</th>
                                    <th>Imagen</th>
                                    <th>Activar/Desactivar</th>
                                    <th>Editar</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Datos se cargarán automáticamente -->
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th></th>
                                    <th><input type="text" placeholder="Buscar ID" class="form-control form-control-sm" /></th>
                                    <th><input type="text" placeholder="Buscar Nombre" class="form-control form-control-sm" /></th>
                                    <th><input type="text" placeholder="1 Act. 0 Des." class="form-control form-control-sm" /></th>
                                    <th></th>
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

    <!--Ayuda Metodos-->
    <?php include_once('ayudaMetodos.php')  ?>
    <!------------------->

    <?php include_once('mantenimientoMetodos.php') ?>


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
    <script type="text/javascript" src="mntmetodos.js"></script>

</body>

</html>