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
    
    <!-- Estilos responsivos adicionales -->
    <style>
        /* ================================ */
        /* ESTILOS RESPONSIVE GENERALES     */
        /* ================================ */
        
        /* Mejoras para dispositivos móviles */
        @media screen and (max-width: 768px) {
            .br-pagetitle h4 {
                font-size: 1.25rem;
            }
            
            .br-pagetitle p {
                font-size: 0.85rem;
            }
            
            /* Alerta de filtros más compacta */
            #filter-alert {
                font-size: 0.85rem;
                padding: 0.5rem;
            }
            
            #filter-alert .btn {
                font-size: 0.75rem;
                padding: 0.25rem 0.5rem;
            }
            
            /* Controles de DataTables responsivos */
            .dataTables_wrapper .dataTables_length,
            .dataTables_wrapper .dataTables_filter {
                margin-bottom: 1rem;
            }
            
            .dataTables_wrapper .dataTables_length select {
                width: auto;
                min-width: 60px;
            }
            
            .dataTables_wrapper .dataTables_filter input {
                width: 100%;
                max-width: 200px;
            }
            
            /* Paginación más compacta */
            .dataTables_wrapper .dataTables_paginate {
                font-size: 0.85rem;
            }
            
            .dataTables_wrapper .dataTables_paginate .paginate_button {
                padding: 0.25rem 0.5rem;
            }
            
            /* Info de registros más pequeña */
            .dataTables_wrapper .dataTables_info {
                font-size: 0.85rem;
                padding-top: 0.5rem;
            }
        }
        
        /* Tabla responsive mejorada */
        @media screen and (max-width: 992px) {
            .table-wrapper {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }
            
            /* Filas de grupo más compactas */
            .group-row td {
                font-size: 0.9rem;
                padding: 8px 4px !important;
            }
            
            .group-row .badge {
                font-size: 0.75rem;
            }
        }
        
        /* ================================ */
        /* ESTILOS PARA DETALLES EXPANDIDOS */
        /* ================================ */
        
        /* Card de detalles responsive */
        .details-card {
            margin: 0 !important;
        }
        
        @media screen and (max-width: 768px) {
            .details-card .card-header h5 {
                font-size: 1rem;
            }
            
            .details-card .card-header i {
                font-size: 1.25rem !important;
            }
            
            /* Tablas de detalles a una columna en móvil */
            .details-card .row > div {
                width: 100%;
                max-width: 100%;
                flex: 0 0 100%;
            }
            
            .details-card table {
                font-size: 0.85rem;
            }
            
            .details-card th {
                padding-left: 0.75rem !important;
                padding-right: 0.25rem !important;
                width: 35% !important;
            }
            
            .details-card td {
                padding-left: 0.25rem !important;
                padding-right: 0.75rem !important;
            }
            
            /* Badges más pequeños */
            .details-card .badge {
                font-size: 0.75rem;
                padding: 0.25rem 0.5rem;
            }
            
            /* Iconos más pequeños */
            .details-card i {
                font-size: 0.85rem;
            }
            
            /* Observaciones más compactas */
            .details-card .alert {
                margin-left: 0.5rem !important;
                margin-right: 0.5rem !important;
                font-size: 0.85rem;
                padding: 0.75rem;
            }
            
            .details-card .card-footer {
                font-size: 0.75rem;
                padding: 0.5rem;
            }
        }
        
        /* Tablet */
        @media screen and (min-width: 769px) and (max-width: 991px) {
            .details-card table {
                font-size: 0.9rem;
            }
        }
        
        /* ================================ */
        /* ESTILOS FILTROS PIE DE TABLA     */
        /* ================================ */
        
        @media screen and (max-width: 768px) {
            #elementos_data tfoot input,
            #elementos_data tfoot select {
                font-size: 0.75rem;
                padding: 0.25rem 0.5rem;
                min-width: 80px;
            }
            
            #elementos_data tfoot th {
                padding: 0.25rem;
            }
        }
        
        /* ================================ */
        /* MEJORAS VISUALES GENERALES       */
        /* ================================ */
        
        /* Scroll suave en tablas */
        .table-wrapper {
            position: relative;
        }
        
        /* Sombra para indicar scroll horizontal */
        @media screen and (max-width: 992px) {
            .table-wrapper::after {
                content: '';
                position: absolute;
                top: 0;
                right: 0;
                bottom: 0;
                width: 30px;
                background: linear-gradient(to right, transparent, rgba(0,0,0,0.1));
                pointer-events: none;
                opacity: 0;
                transition: opacity 0.3s;
            }
            
            .table-wrapper.has-scroll::after {
                opacity: 1;
            }
        }
        
        /* Mejorar visibilidad del botón de expandir */
        .details-control {
            cursor: pointer;
            position: relative;
        }
        
        .details-control::before {
            content: '▶';
            font-size: 0.8rem;
            color: #007bff;
            transition: transform 0.3s;
            display: inline-block;
        }
        
        tr.shown .details-control::before {
            transform: rotate(90deg);
        }
        
        /* Responsive para breadcrumb */
        @media screen and (max-width: 576px) {
            .breadcrumb {
                font-size: 0.75rem;
                flex-wrap: wrap;
            }
            
            .breadcrumb-item + .breadcrumb-item::before {
                padding: 0 0.25rem;
            }
        }
        
        /* Ajuste del contenedor principal en móviles */
        @media screen and (max-width: 768px) {
            .br-pagebody {
                padding: 0.5rem;
            }
            
            .br-section-wrapper {
                padding: 0.75rem;
            }
            
            .br-pageheader {
                padding: 0.5rem 0.75rem;
            }
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
                <span class="breadcrumb-item active">Consulta de Elementos</span>
            </nav>
        </div><!-- br-pageheader -->
        
        <div class="br-pagetitle">
            <div class="d-flex align-items-center flex-wrap">
                <h4 class="mb-0 me-2">Consulta de Elementos</h4>
                <button type="button" class="btn btn-link p-0 ms-1" data-bs-toggle="modal" data-bs-target="#modalAyudaElementos" title="Ayuda sobre el módulo">
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
                    <div class="flex-grow-1 me-md-3" style="min-width: 250px;">
                        <!-- Alerta de filtro activo -->
                        <div class="alert alert-warning alert-dismissible fade show mb-0 w-100" role="alert" id="filter-alert" style="display: none;">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <div class="truncate flex-grow-1">
                                    <i class="fas fa-filter me-2"></i>
                                    <span>Filtros aplicados: </span>
                                    <span id="active-filters-text" class="text-truncate"></span>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-warning flex-shrink-0" id="clear-filter">
                                    Limpiar filtros
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabla de elementos -->
                <div class="table-wrapper">
                    <table id="elementos_data" class="table display responsive nowrap" style="width:100%">
                        <thead>
                            <tr>
                                <th class="text-center"></th>
                                <th class="text-center">ID</th>
                                <th class="text-center">Artículooooo</th>
                                <th class="text-center">Código</th>
                                <th class="text-center">Descripción</th>
                                <th class="text-center">Marca</th>
                                <th class="text-center">Modelo</th>
                                <th class="text-center">N° Serie</th>
                                <th class="text-center">Estado</th>
                                <th class="text-center">Ubicación</th>
                                <th class="text-center">Activo</th>
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
                                <th>
                                    <select class="form-control form-control-sm" title="Filtrar por estado">
                                        <option value="">Todos</option>
                                    </select>
                                </th>
                                <th><input type="text" placeholder="Buscar ubicación" class="form-control form-control-sm" /></th>
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

    <?php include_once('ayudaElementos.php') ?>

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
    <script type="text/javascript" src="movil.js"></script>

</body>

</html>