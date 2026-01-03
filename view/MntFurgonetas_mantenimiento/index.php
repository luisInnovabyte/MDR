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
            <a class="breadcrumb-item" href="../MntFurgonetas/index.php">Furgonetas</a>
            <span class="breadcrumb-item active">Historial de Mantenimiento</span>
        </nav>
    </div><!-- br-pageheader -->
    
    <div class="br-pagetitle">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div class="d-flex align-items-center">
                <h4 class="mb-0 me-2">Historial de Mantenimiento de Furgoneta</h4>
                <button type="button" class="btn btn-link p-0 ms-1" data-bs-toggle="modal" data-bs-target="#modalAyudaMantenimiento" title="Ayuda sobre el módulo">
                    <i class="bi bi-question-circle text-primary" style="font-size: 1.3rem;"></i>
                </button>
            </div>
        </div>
        
        <!-- Info de la furgoneta -->
        <div class="mt-2 mb-3" id="info-furgoneta">
            <div class="card border-0 shadow-sm" style="background-color: #f8f9fa;">
                <div class="card-body py-4 px-5">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center flex-grow-1">
                            <div class="me-4" style="color: #6c757d;">
                                <i class="bi bi-truck" style="font-size: 2.2rem;"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="text-muted mb-2" style="font-size: 0.95rem;">Furgoneta actual</div>
                                <h5 class="mb-2 fw-bold" style="color: #495057;">
                                    <span id="matricula-furgoneta">Cargando...</span>
                                </h5>
                                <p class="mb-0 text-muted">
                                    <span id="marca-furgoneta">--</span> 
                                    <span id="modelo-furgoneta">--</span>
                                    | ID: <span id="id-furgoneta" class="badge bg-secondary ms-1">--</span>
                                </p>
                            </div>
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
                <div class="d-flex gap-2">
                    <a href="formularioMantenimiento.php?modo=nuevo&id_furgoneta=<?php echo $_GET['id_furgoneta'] ?? ''; ?>" class="btn btn-oblong btn-outline-primary flex-shrink-0">
                        <i class="fas fa-plus-circle me-2"></i>Nuevo Mantenimiento
                    </a>
                    <a href="../MntFurgonetas/index.php" class="btn btn-oblong btn-outline-secondary flex-shrink-0">
                        <i class="fas fa-arrow-left me-2"></i>Volver a Furgonetas
                    </a>
                </div>
            </div>

            <!-- Tabla de mantenimientos -->
            <div class="table-wrapper">
                <table id="mantenimientos_data" class="table display responsive nowrap">
                    <thead>
                        <tr>
                            <th></th>
                            <th>ID</th>
                            <th>Fecha</th>
                            <th>Tipo</th>
                            <th>Descripción</th>
                            <th>Kilometraje</th>
                            <th>Costo</th>
                            <th>Taller</th>
                            <th>Estado</th>
                            <th>Act./Desac.</th>
                            <th>Editar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Datos se cargarán aquí -->
                    </tbody>
                    <tfoot>
                        <tr>
                            <th></th>
                            <th class="d-none"><input type="text" placeholder="Buscar ID" class="form-control form-control-sm" /></th>
                            <th><input type="text" placeholder="Buscar fecha" class="form-control form-control-sm" /></th>
                            <th>
                                <select class="form-control form-control-sm" title="Filtrar por tipo">
                                    <option value="">Todos</option>
                                    <option value="revision">Revisión</option>
                                    <option value="reparacion">Reparación</option>
                                    <option value="itv">ITV</option>
                                    <option value="neumaticos">Neumáticos</option>
                                    <option value="otros">Otros</option>
                                </select>
                            </th>
                            <th><input type="text" placeholder="Buscar descripción" class="form-control form-control-sm" /></th>
                            <th><input type="text" placeholder="Buscar km" class="form-control form-control-sm" /></th>
                            <th><input type="text" placeholder="Buscar costo" class="form-control form-control-sm" /></th>
                            <th><input type="text" placeholder="Buscar taller" class="form-control form-control-sm" /></th>
                            <th>
                                <select class="form-control form-control-sm" title="Filtrar por estado">
                                    <option value="">Todos los estados</option>
                                    <option value="1">Activo</option>
                                    <option value="0">Inactivo</option>
                                </select>
                            </th>
                            <th class="d-none"><input type="text" placeholder="NO Buscar" class="form-control form-control-sm" /></th>
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

    <!-- #################################### -->
    <!-- MODAL DE AYUDA                       -->
    <!-- #################################### -->

    <?php include_once('ayudaMantenimiento.php') ?>

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
    <script type="text/javascript" src="mntfurgonetas_mantenimiento.js"></script>

</body>

</html>
