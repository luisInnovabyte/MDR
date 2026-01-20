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
        #modalKilometraje {
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
            <span class="breadcrumb-item active">Registro de Kilometraje</span>
        </nav>
    </div><!-- br-pageheader -->
    
    <div class="br-pagetitle">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div class="d-flex align-items-center">
                <h4 class="mb-0 me-2">Registro de Kilometraje de Furgoneta</h4>
                <button type="button" class="btn btn-link p-0 ms-1" data-bs-toggle="modal" data-bs-target="#modalAyudaKilometraje" title="Ayuda sobre el módulo">
                    <i class="bi bi-question-circle text-primary" style="font-size: 1.3rem;"></i>
                </button>
            </div>
        </div>
        
        <!-- Info de la furgoneta -->
        <div class="mt-2 mb-3" id="info-furgoneta">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #2980b9 0%, #6dd5fa 100%);">
                <div class="card-body py-3 px-4">
                    <div class="row align-items-center">
                        <!-- Icono principal -->
                        <div class="col-auto">
                            <div class="rounded-circle d-flex align-items-center justify-content-center" 
                                 style="width: 60px; height: 60px; background-color: rgba(255,255,255,0.15);">
                                <i class="bi bi-truck text-white" style="font-size: 1.8rem;"></i>
                            </div>
                        </div>
                        
                        <!-- Información de la furgoneta -->
                        <div class="col">
                            <div class="text-white-50 mb-1" style="font-size: 0.85rem; font-weight: 500;">
                                <i class="bi bi-info-circle me-1"></i>Furgoneta actual
                            </div>
                            <h5 class="mb-2 fw-bold text-white" id="nombre-furgoneta">
                                Cargando...
                            </h5>
                            <div class="d-flex align-items-center gap-3">
                                <span class="text-white-50" style="font-size: 0.9rem;">
                                    <i class="bi bi-hash me-1"></i>ID:
                                    <span id="id-furgoneta" class="badge bg-white text-dark ms-1 fw-semibold">--</span>
                                </span>
                            </div>
                        </div>
                        
                        <!-- Botón de acción (opcional) -->
                        <div class="col-auto d-none d-md-block">
                            <a href="../MntFurgonetas/index.php" class="btn btn-light btn-sm">
                                <i class="bi bi-arrow-left me-1"></i>Volver
                            </a>
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
                    <a href="formularioKilometraje.php?modo=nuevo&id_furgoneta=<?php echo $_GET['id_furgoneta'] ?? ''; ?>" class="btn btn-oblong btn-outline-primary flex-shrink-0 mt-2 mt-sm-0">
                        <i class="fas fa-plus-circle me-2"></i>Nuevo Registro
                    </a>
                    <a href="../MntFurgonetas/index.php" class="btn btn-oblong btn-outline-secondary flex-shrink-0 mt-2 mt-sm-0">
                        <i class="fas fa-arrow-left me-2"></i>Volver a Furgonetas
                    </a>
                </div>
            </div>

            <!-- Acordeón de Filtros -->
            <div id="accordion" class="accordion mb-3">
                <div class="card">
                    <!-- <div class="card-header p-0">
                        <h6 class="mg-b-0">
                            <a id="accordion-toggle" 
                               class="d-block p-3 bg-primary text-white collapsed" 
                               data-toggle="collapse" 
                               href="#collapseOne"
                               style="text-decoration: none;">
                                <i class="fas fa-filter me-2"></i>Filtros de Kilometraje
                            </a>
                        </h6>
                    </div> -->

                    <div id="collapseOne" class="collapse" data-parent="#accordion">
                        <div class="card-body pd-20 pt-3">
                            <div class="row g-3">
                                <!-- Bloque Tipo de Registro -->
                                <div class="col-md-6">
                                    <div class="card shadow-sm h-100">
                                        <div class="card-header bg-white border-bottom py-2">
                                            <h6 class="mb-0 text-primary">
                                                <i class="fas fa-tag me-2"></i>Tipo de Registro
                                            </h6>
                                        </div>
                                        <div class="card-body p-2">
                                            <div class="status-selector">
                                                <div class="status-option">
                                                    <input type="radio" name="filterTipo" id="filterAllTipo" value="all" class="status-radio" checked>
                                                    <label for="filterAllTipo" class="status-label">
                                                        <span class="status-icon">
                                                            <i class="fas fa-layer-group"></i>
                                                        </span>
                                                        <span class="status-text">Todos</span>
                                                    </label>
                                                </div>
                                                <div class="status-option">
                                                    <input type="radio" name="filterTipo" id="filterManual" value="manual" class="status-radio">
                                                    <label for="filterManual" class="status-label">
                                                        <span class="status-icon">
                                                            <i class="fas fa-hand-pointer"></i>
                                                        </span>
                                                        <span class="status-text">Manual</span>
                                                    </label>
                                                </div>
                                                <div class="status-option">
                                                    <input type="radio" name="filterTipo" id="filterRevision" value="revision" class="status-radio">
                                                    <label for="filterRevision" class="status-label">
                                                        <span class="status-icon">
                                                            <i class="fas fa-wrench"></i>
                                                        </span>
                                                        <span class="status-text">Revisión</span>
                                                    </label>
                                                </div>
                                                <div class="status-option">
                                                    <input type="radio" name="filterTipo" id="filterItv" value="itv" class="status-radio">
                                                    <label for="filterItv" class="status-label">
                                                        <span class="status-icon">
                                                            <i class="fas fa-clipboard-check"></i>
                                                        </span>
                                                        <span class="status-text">ITV</span>
                                                    </label>
                                                </div>
                                                <div class="status-option">
                                                    <input type="radio" name="filterTipo" id="filterEvento" value="evento" class="status-radio">
                                                    <label for="filterEvento" class="status-label">
                                                        <span class="status-icon">
                                                            <i class="fas fa-calendar-alt"></i>
                                                        </span>
                                                        <span class="status-text">Evento</span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Espacio para otros filtros -->
                                <div class="col-md-6">
                                    <!-- Puedes agregar más filtros aquí si es necesario -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div><!-- card -->
            </div><!-- accordion -->

            <!-- Tabla de registros de kilometraje -->
            <div class="table-wrapper">
                <table id="kilometrajes_data" class="table display responsive nowrap">
                    <thead>
                        <tr>
                            <th></th>
                            <th>ID</th>
                            <th>Fecha</th>
                            <th>Kilometraje</th>
                            <th>KM Recorridos</th>
                            <th>Días</th>
                            <th>KM/Día</th>
                            <th>Tipo</th>
                            <th>Observaciones</th>
                            <th>Fecha Registro</th>
                            <th>Edit.</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Datos se cargarán aquí -->
                    </tbody>
                    <tfoot>
                        <tr>
                            <th></th> <!-- Columna 0: Control (expandir) -->
                            <th><input type="text" placeholder="Buscar ID" class="form-control form-control-sm" /></th> <!-- Columna 1: ID -->
                            <th><input type="text" placeholder="Buscar fecha" class="form-control form-control-sm" /></th> <!-- Columna 2: Fecha -->
                            <th><input type="text" placeholder="Buscar km" class="form-control form-control-sm" /></th> <!-- Columna 3: Kilometraje -->
                            <th></th> <!-- Columna 4: KM Recorridos (calculado, no buscar) -->
                            <th></th> <!-- Columna 5: Días (calculado, no buscar) -->
                            <th></th> <!-- Columna 6: KM/Día (calculado, no buscar) -->
                            <th> <!-- Columna 7: Tipo -->
                                <select class="form-control form-control-sm" title="Filtrar por tipo">
                                    <option value="">Todos</option>
                                    <option value="manual">Manual</option>
                                    <option value="revision">Revisión</option>
                                    <option value="itv">ITV</option>
                                    <option value="evento">Evento</option>
                                </select>
                            </th>
                            <th><input type="text" placeholder="Buscar observaciones" class="form-control form-control-sm" /></th> <!-- Columna 8: Observaciones -->
                            <th></th> <!-- Columna 9: Fecha Registro (no buscar por ahora) -->
                            <th></th> <!-- Columna 10: Botón Editar -->
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

    <!-- *********************************** -->
    <!--                FIN                  -->
    <!-- MODAL QUE SE DISPARA DESDE EL BOTON -->
    <!--        DE LA PROPIA TABLA           -->
    <!--             columDef                -->
    <!-- *********************************** -->

<!-- *************************************** -->
    <!-- MODAL QUE SE DISPARA DESDE EL BOTON -->
    <!--             AYUDA                   -->
    <!-- *********************************** -->

    <?php include_once('ayudaKilometraje.php') ?>

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
    <script type="text/javascript" src="mntfurgonetas_registro_kilometraje.js"></script>

</body>

</html>
