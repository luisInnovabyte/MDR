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
        
        /* Botón de detalles */
        button.details-control {
            min-width: 30px;
        }
        
        /* ========================================
           ESTILOS PARA AGRUPACIÓN DATATABLES
           ======================================== */
        
        /* Agrupación nivel 1: Fecha */
        tr.group-fecha td {
            background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%) !important;
            color: #1976d2 !important;
            font-weight: 400;
            padding: 6px 12px !important;
            font-size: 0.875rem;
            border: none !important;
            border-left: 3px solid #1976d2 !important;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }
        
        tr.group-fecha td:hover {
            background: linear-gradient(135deg, #bbdefb 0%, #90caf9 100%) !important;
            cursor: pointer;
        }
        
        /* Agrupación nivel 2: Ubicación */
        tr.group-ubicacion td {
            background: linear-gradient(135deg, #f1f8e9 0%, #dcedc8 100%) !important;
            color: #558b2f !important;
            font-weight: 300;
            padding: 5px 12px 5px 28px !important;
            font-size: 0.813rem;
            border: none !important;
            border-left: 2px solid #7cb342 !important;
            box-shadow: 0 1px 1px rgba(0,0,0,0.03);
        }
        
        tr.group-ubicacion td:hover {
            background: linear-gradient(135deg, #dcedc8 0%, #c5e1a5 100%) !important;
            cursor: pointer;
        }
        
        /* Iconos en agrupaciones */
        tr.group-fecha i.bi,
        tr.group-ubicacion i.bi {
            font-size: 0.95em;
            vertical-align: middle;
            opacity: 0.8;
        }
        
        /* Ajustar espaciado entre grupos */
        tr.group-fecha + tr.group-ubicacion td {
            padding-top: 8px !important;
        }
        
        /* Filas de datos dentro de grupos */
        .dtr-group + tr td {
            border-top: 2px solid #e9ecef !important;
        }
        
        /* ========================================
           OCULTAR SPINNERS DE INPUTS NUMBER
           ======================================== */
        
        /* Chrome, Safari, Edge, Opera */
        input[type="number"]::-webkit-outer-spin-button,
        input[type="number"]::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        
        /* Firefox */
        input[type="number"] {
            -moz-appearance: textfield;
        }
        
        /* Badge cliente exento IVA */
        #badge-cliente-exento {
            display: none;
            animation: fadeIn 0.5s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-5px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
    
    <!-- ########## START: MAIN PANEL ########## -->
<div class="br-mainpanel">
    <div class="br-pageheader">
        <nav class="breadcrumb pd-0 mg-0 tx-12">
            <a class="breadcrumb-item" href="../Dashboard/index.php">Dashboard</a>
            <a class="breadcrumb-item" href="../Presupuesto/mntpresupuesto.php">Presupuestos</a>
            <a class="breadcrumb-item" href="#" id="breadcrumb-presupuesto">Presupuesto Actual</a>
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
                                <!-- Badge Cliente Exento de IVA -->
                                <span id="badge-cliente-exento" class="badge bg-warning text-dark" style="font-size: 0.85rem; padding: 0.4rem 0.8rem;">
                                    <i class="bi bi-exclamation-triangle-fill me-1"></i>Cliente EXENTO de IVA (0%)
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
                            <a href="#" id="btn-volver-header" class="btn btn-light btn-sm">
                                <i class="bi bi-arrow-left me-1"></i>Volver al Presupuesto
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
                        <div class="d-flex flex-column gap-1">
                            <div>
                                <small class="text-muted">Normal:</small>
                                <h5 class="mb-0 d-inline ms-1" id="total-base">0,00 €</h5>
                            </div>
                            <div class="border-top pt-1">
                                <small class="text-primary fw-bold">Hotel:</small>
                                <h5 class="mb-0 d-inline ms-1 text-primary" id="total-base-hotel">0,00 €</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-total iva">
                    <div class="card-body">
                        <h6 class="text-muted mb-1">IVA Total</h6>
                        <div class="d-flex flex-column gap-1">
                            <div>
                                <small class="text-muted">Normal:</small>
                                <h5 class="mb-0 d-inline ms-1" id="total-iva">0,00 €</h5>
                            </div>
                            <div class="border-top pt-1">
                                <small class="text-primary fw-bold">Hotel:</small>
                                <h5 class="mb-0 d-inline ms-1 text-primary" id="total-iva-hotel">0,00 €</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-total total">
                    <div class="card-body">
                        <h6 class="text-muted mb-1 fw-bold">TOTAL con IVA</h6>
                        <div class="d-flex flex-column gap-1">
                            <div>
                                <small class="text-muted">Normal:</small>
                                <h4 class="mb-0 d-inline ms-1 fw-bold text-success" id="total-con-iva">0,00 €</h4>
                            </div>
                            <div class="border-top pt-1">
                                <small class="text-primary fw-bold">Hotel:</small>
                                <h4 class="mb-0 d-inline ms-1 fw-bold text-primary" id="total-con-iva-hotel">0,00 €</h4>
                            </div>
                        </div>
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
                    <a href="#" id="btn-volver-footer" class="btn btn-oblong btn-outline-secondary flex-shrink-0">
                        <i class="fas fa-arrow-left me-2"></i>Volver al Presupuesto
                    </a>
                </div>
            </div>

            <!-- Tabla de líneas -->
            <div class="table-wrapper">
                <table id="lineas_data" class="table display responsive nowrap" style="width:100%">
                    <thead>
                        <tr>
                            <th></th><!-- Detalles -->
                            <th>Orden</th><!-- Oculta -->
                            <th>Fecha Inicio</th><!-- Oculta - para agrupación -->
                            <th>Ubicación</th><!-- Oculta - para agrupación -->
                            <th>Localización</th><!-- Oculta -->
                            <th>Código</th>
                            <th>Descripción</th>
                            <th>Fecha Fin</th>
                            <th>Días</th>
                            <th>Cantidad</th>
                            <th>Coef.</th>
                            <th>Total<span class="text-danger fw-bold" style="font-size: 1.1rem;">*</span></th>
                            <th>Total Final (Inter.)<span class="text-danger fw-bold" style="font-size: 1.1rem;">*</span></th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Datos se cargarán aquí -->
                    </tbody>
                    <tfoot>
                        <tr>
                            <th></th><!-- Detalles -->
                            <th></th><!-- Orden (oculto) -->
                            <th></th><!-- Fecha Inicio (oculto) -->
                            <th></th><!-- Ubicación (oculto) -->
                            <th></th><!-- Localización (oculto) -->
                            <th><input type="text" placeholder="Código" class="form-control form-control-sm" /></th>
                            <th><input type="text" placeholder="Descripción" class="form-control form-control-sm" /></th>
                            <th></th><!-- Fecha Fin -->
                            <th></th><!-- Días -->
                            <th></th><!-- Cantidad -->
                            <th></th><!-- Coeficiente -->
                            <th></th><!-- Total -->
                            <th></th><!-- Total Final (Hotel) -->
                            <th>
                                <select class="form-control form-control-sm">
                                    <option value="">Todos</option>
                                    <option value="1">Activo</option>
                                    <option value="0">Inactivo</option>
                                </select>
                            </th>
                            <th></th><!-- Acciones -->
                        </tr>
                    </tfoot>
                </table>
                
                <!-- Nota explicativa del asterisco -->
                <div class="mt-2 px-2">
                    <small class="text-muted">
                        <span class="text-danger fw-bold" style="font-size: 1.1rem;">*</span>
                        <strong>Nota:</strong> El total mostrado incluye el descuento aplicado en la línea de presupuesto del artículo 
                        y el coeficiente (si procede), <strong>SIN IMPUESTOS INCLUIDOS</strong>.
                    </small>
                </div>
                <div class="mt-2 px-2">
                    <small class="text-muted">
                        <span class="text-danger fw-bold" style="font-size: 1.1rem;">**</span>
                        <strong>Nota:</strong> Las observaciones indicadas en esta sección se imprimirán en el presupuesto.
                    </small>
                </div>
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
    
    <!-- *** PUNTO 17: Variables globales para exención de IVA *** -->
    <script>
        // Variable global para indicar si el cliente está exento de IVA
        // Se inicializa aquí y se actualiza cuando se carga la info de la versión
        let clienteExentoIVA = false;
    </script>
    
    <script type="text/javascript" src="lineasPresupuesto.js"></script>
  
  <script>
        // Colapsar el sidebar al cargar la página
        $(document).ready(function() {
            $('body').addClass('collapsed-menu');
            $('.br-sideleft').addClass('collapsed');
        });
    </script>




</body>

</html>
