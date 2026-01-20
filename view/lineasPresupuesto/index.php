<!-- ---------------------- -->
<!--   Comprobar permisos     -->
<!-- ---------------------- -->
<?php $moduloActual = 'presupuestos'; ?>
<?php require_once('../../config/template/verificarPermiso.php'); ?>

<!DOCTYPE html>
<html lang="es">

<!-- ---------------------- -->
<!--      MainHead.php      -->
<!-- ---------------------- -->

<head>
    <?php include_once('../../config/template/mainHead.php') ?>
    <title>Líneas de Presupuesto - MDR ERP</title>
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
        #modalFormularioLinea {
            z-index: 1050 !important;
        }

        .ui-datepicker {
            z-index: 1060 !important;
        }
        
        /* Estilos para totales del PIE */
        .card-total {
            border-left: 4px solid;
        }
        .card-total.base { border-color: #6c757d; }
        .card-total.iva { border-color: #17a2b8; }
        .card-total.total { border-color: #28a745; background: #f8f9fa; }
        
        /* Estado de versión */
        .badge-estado-version {
            font-size: 0.9rem;
            padding: 0.5rem 1rem;
        }
    </style>
    
    <!-- ########## START: MAIN PANEL ########## -->
<div class="br-mainpanel">
    <div class="br-pageheader">
        <nav class="breadcrumb pd-0 mg-0 tx-12">
            <a class="breadcrumb-item" href="../Dashboard/index.php">Dashboard</a>
            <a class="breadcrumb-item" href="../Presupuesto/mntpresupuesto.php">Presupuestos</a>
            <span class="breadcrumb-item active">Líneas de Presupuesto</span>
        </nav>
    </div><!-- br-pageheader -->
    
    <div class="br-pagetitle">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div class="d-flex align-items-center">
                <h4 class="mb-0 me-2">Líneas de Presupuesto</h4>
                <button type="button" class="btn btn-link p-0 ms-1" data-bs-toggle="modal" data-bs-target="#modalAyudaLineas" title="Ayuda sobre el módulo">
                    <i class="bi bi-question-circle text-primary" style="font-size: 1.3rem;"></i>
                </button>
            </div>
        </div>
        
        <!-- Info de la versión del presupuesto -->
        <div class="mt-2 mb-3" id="info-version">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body py-3 px-4">
                    <div class="row align-items-center">
                        <!-- Icono principal -->
                        <div class="col-auto">
                            <div class="rounded-circle d-flex align-items-center justify-content-center" 
                                 style="width: 60px; height: 60px; background-color: rgba(255,255,255,0.15);">
                                <i class="bi bi-file-earmark-text text-white" style="font-size: 1.8rem;"></i>
                            </div>
                        </div>
                        
                        <!-- Información de la versión -->
                        <div class="col">
                            <div class="text-white-50 mb-1" style="font-size: 0.85rem; font-weight: 500;">
                                <i class="bi bi-info-circle me-1"></i>Presupuesto y Versión Actual
                            </div>
                            <h5 class="mb-2 fw-bold text-white" id="numero-presupuesto">
                                Cargando...
                            </h5>
                            <div class="d-flex align-items-center gap-3 flex-wrap">
                                <span class="text-white-50" style="font-size: 0.9rem;">
                                    <i class="bi bi-hash me-1"></i>Cliente:
                                    <span id="nombre-cliente" class="text-white fw-semibold ms-1">--</span>
                                </span>
                                <span class="text-white-50" style="font-size: 0.9rem;">
                                    <i class="bi bi-calendar-event me-1"></i>Evento:
                                    <span id="nombre-evento" class="text-white fw-semibold ms-1">--</span>
                                </span>
                                <span class="text-white-50" style="font-size: 0.9rem;">
                                    <i class="bi bi-arrow-repeat me-1"></i>Versión:
                                    <span id="numero-version" class="badge bg-white text-dark ms-1 fw-semibold">v0</span>
                                </span>
                                <span id="estado-version-badge">
                                    <!-- Se llenará dinámicamente -->
                                </span>
                            </div>
                        </div>
                        
                        <!-- Botón de acción -->
                        <div class="col-auto d-none d-md-block">
                            <a href="../Presupuesto/mntpresupuesto.php" class="btn btn-light btn-sm">
                                <i class="bi bi-arrow-left me-1"></i>Volver
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Alerta de versión bloqueada -->
        <div class="alert alert-warning alert-dismissible fade show mb-3" role="alert" id="alert-version-bloqueada" style="display: none;">
            <i class="bi bi-lock me-2"></i>
            <strong>Versión bloqueada:</strong> Esta versión no está en estado "borrador", por lo tanto no se pueden crear, modificar o eliminar líneas. 
            Para hacer cambios, debe crear una nueva versión desde el listado de presupuestos.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div><!-- br-pagetitle -->

    <div class="br-pagebody">
        <!-- TOTALES DEL PIE -->
        <div class="row mb-3" id="totales-pie">
            <div class="col-md-3">
                <div class="card card-total base">
                    <div class="card-body">
                        <h6 class="text-muted mb-1">Base Imponible</h6>
                        <h4 class="mb-0" id="total-base">0,00 €</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-total iva">
                    <div class="card-body">
                        <h6 class="text-muted mb-1">IVA Total</h6>
                        <h4 class="mb-0" id="total-iva">0,00 €</h4>
                        <small class="text-muted" id="desglose-iva">--</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-total total">
                    <div class="card-body">
                        <h6 class="text-muted mb-1 fw-bold">TOTAL con IVA</h6>
                        <h3 class="mb-0 fw-bold text-success" id="total-con-iva">0,00 €</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-total">
                    <div class="card-body">
                        <h6 class="text-muted mb-1">Nº Líneas</h6>
                        <h4 class="mb-0" id="cantidad-lineas">0</h4>
                    </div>
                </div>
            </div>
        </div>
        
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
                    <button class="btn btn-oblong btn-outline-primary flex-shrink-0" id="btn-nueva-linea">
                        <i class="fas fa-plus-circle me-2"></i>Nueva Línea
                    </button>
                    <a href="../Presupuesto/mntpresupuesto.php" class="btn btn-oblong btn-outline-secondary flex-shrink-0">
                        <i class="fas fa-arrow-left me-2"></i>Volver a Presupuestos
                    </a>
                </div>
            </div>

            <!-- Tabla de líneas -->
            <div class="table-wrapper">
                <table id="lineas_data" class="table display responsive nowrap" style="width:100%">
                    <thead>
                        <tr>
                            <th>Orden</th>
                            <th>Código</th>
                            <th>Descripción</th>
                            <th>Tipo</th>
                            <th>Cantidad</th>
                            <th>P. Unitario</th>
                            <th>Dto. %</th>
                            <th>Coef.</th>
                            <th>Base Imp.</th>
                            <th>IVA %</th>
                            <th>Imp. IVA</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Datos se cargarán aquí -->
                    </tbody>
                    <tfoot>
                        <tr>
                            <th></th>
                            <th><input type="text" placeholder="Código" class="form-control form-control-sm" /></th>
                            <th><input type="text" placeholder="Descripción" class="form-control form-control-sm" /></th>
                            <th>
                                <select class="form-control form-control-sm">
                                    <option value="">Todos</option>
                                    <option value="articulo">Artículo</option>
                                    <option value="kit">Kit</option>
                                    <option value="seccion">Sección</option>
                                    <option value="texto">Texto</option>
                                </select>
                            </th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th>
                                <select class="form-control form-control-sm">
                                    <option value="">Todos</option>
                                    <option value="1">Activo</option>
                                    <option value="0">Inactivo</option>
                                </select>
                            </th>
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
    <!-- MODAL DE FORMULARIO                  -->
    <!-- #################################### -->

    <?php include_once('formularioLinea.php') ?>

    <!-- #################################### -->
    <!-- FIN MODAL DE FORMULARIO              -->
    <!-- #################################### -->

    <!-- #################################### -->
    <!-- MODAL DE AYUDA                       -->
    <!-- #################################### -->

    <?php include_once('ayudaLineas.php') ?>

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
    <script type="text/javascript" src="lineasPresupuesto.js"></script>

</body>

</html>
