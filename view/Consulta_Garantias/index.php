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
                <span class="breadcrumb-item active">Consulta de Garantias</span>
            </nav>
        </div><!-- br-pageheader -->
        
        <div class="br-pagetitle">
            <div class="d-flex align-items-center">
                <h4 class="mb-0 me-2">Consulta de Garantias</h4>
                <button type="button" class="btn btn-link p-0 ms-1" data-bs-toggle="modal" data-bs-target="#modalAyudaGarantias" title="Ayuda sobre el módulo">
                    <i class="bi bi-question-circle text-primary" style="font-size: 1.3rem;"></i>
                </button>
            </div>
            <p class="text-muted mt-2 mb-0">
                <i class="bi bi-info-circle me-1"></i>
                Vista de solo consulta para técnicos. No se pueden editar ni crear elementos desde aquí.
            </p>
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
                </div>

                <!-- Tabla de elementos -->
                <div class="table-wrapper">
                    <table id="elementos_data" class="table display responsive nowrap">
                        <thead>
                            <tr>
                                <th></th>
                                <th>ID</th>
                                <th>Artículo</th>
                                <th>Código</th>
                                <th>Descripción</th>
                                <th>Marca</th>
                                <th>Modelo</th>
                                <th>N° Serie</th>
                                <th>Ubicación</th>
                                <th>Estado Garantía</th>
                                <th>Fin Garantía</th>
                                <th>Activo</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Datos se cargarán aquí -->
                        </tbody>
                        <tfoot>
                            <tr>
                                <th></th>
                                <th class="d-none"><input type="text" placeholder="Buscar ID" class="form-control form-control-sm" /></th>
                                <th><input type="text" placeholder="Buscar artículo" class="form-control form-control-sm" /></th>
                                <th><input type="text" placeholder="Buscar código" class="form-control form-control-sm" /></th>
                                <th><input type="text" placeholder="Buscar descripción" class="form-control form-control-sm" /></th>
                                <th><input type="text" placeholder="Buscar marca" class="form-control form-control-sm" /></th>
                                <th><input type="text" placeholder="Buscar modelo" class="form-control form-control-sm" /></th>
                                <th><input type="text" placeholder="Buscar n° serie" class="form-control form-control-sm" /></th>
                                <th><input type="text" placeholder="Buscar ubicación" class="form-control form-control-sm" /></th>
                                <th>
                                    <select class="form-control form-control-sm" title="Filtrar por estado garantía">
                                        <option value="">Todos</option>
                                        <option value="Vigente">Vigente</option>
                                        <option value="Próxima a vencer">Próxima a vencer</option>
                                        <option value="Vencida">Vencida</option>
                                    </select>
                                </th>

                                                        
                                <th><input type="text" placeholder="Buscar fecha garantía" class="form-control form-control-sm" /></th>
                                <th>
                                    <select class="form-control form-control-sm" title="Filtrar por activo">
                                        <option value="">Todos los estados</option>
                                        <option value="1">Activo</option>
                                        <option value="0">Inactivo</option>
                                    </select>
                                </th>
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

    <!-- #################################### -->
    <!-- MODAL DE AYUDA                       -->
    <!-- #################################### -->

    <?php include_once('ayudaConsultaGarantias.php') ?>

    <!-- #################################### -->
    <!-- FIN MODAL DE AYUDA                   -->
    <!-- #################################### -->


    <!-- ----------------------- -->
    <!--       mainJs.php        -->
    <!-- ----------------------- -->
    <?php include_once('../../config/template/mainJs.php') ?>

    <script src="../../public/js/tooltip-colored.js"></script>
    <script src="../../public/js/popover-colored.js"></script>
    <!-- ------------------------- -->
    <!--     END mainJs.php        -->
    <!-- ------------------------- -->
    <script type="text/javascript" src="consulta_garantia.js"></script>

  <script>
        // Colapsar el sidebar al cargar la página
        $(document).ready(function() {
            $('body').addClass('collapsed-menu');
            $('.br-sideleft').addClass('collapsed');
        });
    </script>


</body>

</html>
