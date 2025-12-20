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
            <span class="breadcrumb-item active">Fotos de Elementos</span>
        </nav>
    </div><!-- br-pageheader -->
    
    <div class="br-pagetitle">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div class="d-flex align-items-center">
                <h4 class="mb-0 me-2">Gestión de Fotos de Elementos</h4>
                <button type="button" class="btn btn-link p-0 ms-1" data-bs-toggle="modal" data-bs-target="#modalAyudaFotos" title="Ayuda sobre el módulo">
                    <i class="bi bi-question-circle text-primary" style="font-size: 1.3rem;"></i>
                </button>
            </div>
        </div>
        
        <!-- Info del elemento -->
        <div class="mt-2 mb-3" id="info-elemento">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #134e4a 0%, #0f766e 100%);">
                <div class="card-body py-3 px-4">
                    <div class="row align-items-center">
                        <!-- Icono principal -->
                        <div class="col-auto">
                            <div class="rounded-circle d-flex align-items-center justify-content-center" 
                                 style="width: 60px; height: 60px; background-color: rgba(255,255,255,0.15);">
                                <i class="bi bi-camera text-white" style="font-size: 1.8rem;"></i>
                            </div>
                        </div>
                        
                        <!-- Información del elemento -->
                        <div class="col">
                            <div class="text-white-50 mb-1" style="font-size: 0.85rem; font-weight: 500;">
                                <i class="bi bi-info-circle me-1"></i>Filtrando por elemento
                            </div>
                            <h5 class="mb-2 fw-bold text-white" id="nombre-elemento">
                                <span id="descripcion-elemento">Cargando...</span>
                            </h5>
                            <div class="d-flex align-items-center gap-3 flex-wrap">
                                <span class="text-white-50" style="font-size: 0.9rem;">
                                    <i class="bi bi-upc-scan me-1"></i>Código:
                                    <span id="codigo-elemento" class="badge bg-white text-dark ms-1 fw-semibold">--</span>
                                </span>
                                <span class="text-white-50" style="font-size: 0.9rem;">
                                    <i class="bi bi-hash me-1"></i>ID Elemento:
                                    <span id="id-elemento" class="badge bg-white text-dark ms-1 fw-semibold">--</span>
                                </span>
                            </div>
                        </div>
                        
                        <!-- Botón de acción -->
                        <div class="col-auto d-none d-md-block">
                            <button id="btnVolverElementosFiltrados" class="btn btn-light btn-sm">
                                <i class="bi bi-arrow-left me-1"></i>Volver
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div><!-- br-pagetitle -->

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

                <!-- Botones de acción -->
                <div class="d-flex gap-2 flex-shrink-0 mt-2 mt-sm-0">
                    <a href="formularioFoto.php?modo=nuevo" class="btn btn-oblong btn-outline-primary" id="btnNuevaFoto">
                        <i class="fas fa-plus-circle me-2"></i>Nueva Foto
                    </a>
                    <a href="javascript:void(0)" class="btn btn-oblong btn-outline-secondary" id="btnVolverElementos">
                        <i class="fas fa-arrow-left me-2"></i>Volver a Elementos
                    </a>
                </div>
            </div>

                     <!-- Tabla de fotos -->
            <div class="table-wrapper">
                <table id="fotos_data" class="table display responsive nowrap">
                    <thead>
                        <tr>
                            <th></th>
                            <th class="d-none">Id foto</th>
                            <th>Descripción</th>
                            <th>Privado</th>
                            <th>Vista previa</th>
                            <th>Estado</th>
                            <th>Act./Desac.</th>
                            <th>Edit.</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Datos se cargarán aquí -->
                    </tbody>
                    <tfoot>
                        <tr>
                            <th></th>
                            <th class="d-none"><input type="text" placeholder="Buscar ID" class="form-control form-control-sm" /></th>
                            <th><input type="text" placeholder="Buscar descripción" class="form-control form-control-sm" /></th>
                            <th>
                                <select class="form-control form-control-sm" title="Filtrar por privacidad">
                                    <option value="">Todos</option>
                                    <option value="1">Privadas</option>
                                    <option value="0">Públicas</option>
                                </select>
                            </th>
                            <th></th>
                            <th>
                                <select class="form-control form-control-sm" title="Filtrar por estado">
                                    <option value="">Todos los estados</option>
                                    <option value="1">Activo</option>
                                    <option value="0">Inactivo</option>
                                </select>
                            </th>
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

    </div><!-- br-mainpanel -->
    <!-- ########## END: MAIN PANEL ########## -->


<!-- *************************************** -->
    <!-- MODAL QUE SE DISPARA DESDE EL BOTON -->
    <!--             AYUDA                   -->
    <!-- *********************************** -->

    <?php include_once('ayudaFotos.php') ?>


    <!-- *********************************** -->
    <!--                FIN                  -->
    <!-- MODAL QUE SE DISPARA DESDE EL BOTON -->
    <!--               AYUDA                 -->
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
    <script type="text/javascript" src="mntFoto.js"></script>

</body>

</html>
