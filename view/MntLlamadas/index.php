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
        <?php
        session_start();

        $idComercial = isset($_SESSION['id_comercial']) ? $_SESSION['id_comercial'] : null;
        ?>
        <script>
            const idComercial = <?php echo json_encode($idComercial); ?>;
        </script>
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
                    <a class="breadcrumb-item" href="index.html">Dashboard</a>
                    <span class="breadcrumb-item active">Mantenimiento Llamadas</span>
                </nav>
            </div><!-- br-pageheader -->

            <div class="br-pagetitle">
                <i class="icon icon ion-ios-bookmarks-outline"></i>
                <div>
                    <div class="d-flex align-items-center">
                        <h4>Mantenimiento de Llamadas</h4>
                            <button type="button" class="btn btn-link p-0 ms-1" data-toggle="modal" data-target="#modalAyudaLlamadas" title="Ayuda sobre el módulo">
                <i class="bi bi-question-circle text-primary" style="font-size: 1.3rem;"></i>
            </button>
                    </div>
                    
                    <p class="mg-b-0">Tabla básica para el mantenimiento de las llamadas</p>
                    
                </div>
            </div><!-- d-flex -->

            <div class="br-pagebody">
                <div class="br-section-wrapper">
                    <!-- Fila superior con alerta y botón -->
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

                        <!-- Botón Nueva llamada -->
                        <button class="btn btn-oblong btn-outline-primary flex-shrink-0 mt-2 mt-sm-0" id="btnnuevo">
                            <i class="fas fa-plus-circle me-2"></i>Nueva Llamada
                        </button>
                    </div>

                    <!-- Acordeón de filtros -->
                    <div id="accordion" class="accordion mb-4">
                        <div class="card">
                            <div class="card-header p-0">
                                <h6 class="mg-b-0">
                                    <a id="accordion-toggle"
                                        class="d-block p-3 bg-primary text-white collapsed"
                                        data-toggle="collapse"
                                        href="#collapseOne"
                                        style="text-decoration: none;">
                                        <i class="fas fa-filter me-2"></i>Filtros de Llamadas
                                    </a>
                                </h6>
                            </div>

                            <div id="collapseOne" class="collapse" data-parent="#accordion">
                                <div class="card-body pd-20 pt-3">
                                    <div class="row g-3">
                                        <!-- Bloque Estado -->
                                        <div class="col-md-3">
                                            <div class="card shadow-sm h-100">
                                                <div class="card-header bg-white border-bottom py-2">
                                                    <h6 class="mb-0 text-primary">
                                                        <i class="fas fa-toggle-on me-2"></i>Estado
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

                                        <!-- Bloque Estado Eventos -->
                                        <div class="col-md-3">
                                            <div class="card shadow-sm h-100">
                                                <div class="card-header bg-white border-bottom py-2">
                                                    <h6 class="mb-0 text-primary">
                                                        <i class="fas fa-toggle-on me-2"></i>Estado Eventos
                                                    </h6>
                                                </div>
                                                <div class="card-body p-2">
                                                    <div class="status-selector">
                                                        <div class="status-option estado-todos">
                                                            <input type="radio" name="filterDates" id="filterAllDates" value="all" class="status-radio" checked>
                                                            <label for="filterAllDates" class="status-label">
                                                                <span class="status-icon"><i class="fas fa-layer-group"></i></span>
                                                                <span class="status-text fs-5">Todos</span>
                                                            </label>
                                                        </div>
                                                        <div class="status-option estado-pasada">
                                                            <input type="radio" name="filterDates" id="filterPast" value="past" class="status-radio">
                                                            <label for="filterPast" class="status-label">
                                                                <span class="status-icon"><i class="fas fa-history"></i></span>
                                                                <span class="status-text fs-5">Pasadas</span>
                                                            </label>
                                                        </div>
                                                        <div class="status-option estado-futura">
                                                            <input type="radio" name="filterDates" id="filterFuture" value="future" class="status-radio">
                                                            <label for="filterFuture" class="status-label">
                                                                <span class="status-icon"><i class="fas fa-calendar-check"></i></span>
                                                                <span class="status-text fs-5">Futuras</span>
                                                            </label>
                                                        </div>
                                                        <div class="status-option estado-hoy">
                                                            <input type="radio" name="filterDates" id="filterCurrent" value="current" class="status-radio">
                                                            <label for="filterCurrent" class="status-label">
                                                                <span class="status-icon"><i class="fas fa-calendar-day"></i></span>
                                                                <span class="status-text fs-5">Hoy</span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Bloque Fechas -->
                                        <div class="col-md-6">
                                            <div class="card shadow-sm h-100">
                                                <div class="card-header bg-white border-bottom">
                                                    <h6 class="mb-0 text-primary">
                                                        <i class="fas fa-calendar-day me-2"></i>Rango de Fechas
                                                    </h6>
                                                </div>
                                                <div class="card-body d-flex align-items-center">
                                                    <div class="row g-3 w-100">
                                                        <!-- Fecha Hora Preferida -->
                                                        <div class="col-md-6 d-flex flex-column justify-content-center">
                                                            <label class="form-label small text-muted mb-1">Fecha Hora Preferida</label>
                                                            <div class="input-group">
                                                                <span class="input-group-text bg-light">
                                                                    <i class="far fa-calendar-alt"></i>
                                                                </span>
                                                                <input id="filtroFechaHoraPreferida" type="text"
                                                                    class="form-control datepicker"
                                                                    placeholder="dd-mm-aaaa" readonly>
                                                            </div>
                                                            <span class="text-info small mt-1 cursor-pointer" id="borrarFechaHoraPreferidaFiltro">
                                                                <i class="fas fa-times-circle"></i> Borrar fecha hora preferida
                                                            </span>
                                                        </div>

                                                        <!-- Fecha Recepción -->
                                                        <div class="col-md-6 d-flex flex-column justify-content-center">
                                                            <label class="form-label small text-muted mb-1">Fecha Recepción</label>
                                                            <div class="input-group">
                                                                <span class="input-group-text bg-light">
                                                                    <i class="far fa-calendar-alt"></i>
                                                                </span>
                                                                <input id="filtroFechaRecepcion" type="text"
                                                                    class="form-control datepicker"
                                                                    placeholder="dd-mm-aaaa" readonly>
                                                            </div>
                                                            <span class="text-info small mt-1 cursor-pointer" id="borrarFechaRecepcionFiltro">
                                                                <i class="fas fa-times-circle"></i> Borrar fecha de recepción
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div> <!-- row g-3 -->
                                </div> <!-- card-body -->
                            </div> <!-- collapseOne -->
                        </div> <!-- card -->
                    </div> <!-- accordion -->


                    <!-- Tabla de llamadas -->
                    <div class="table-wrapper table order-column hover row-border stripe responsive">
                        <table id="llamadas_data" class="table display responsive nowrap">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Id Llamada</th>
                                    <th>Método</th>
                                    <th>Nombre Comunicante</th>
                                    <th>Domicilio Instalacion</th>
                                    <th>Teléfono Fijo</th>
                                    <th>Teléfono Móvil</th>
                                    <th>Email de Contacto.</th>
                                    <th>Fecha hora Preferida</th>
                                    <th>Observaciones</th>
                                    <th>Id Comercial Asignado</th>
                                    <th>Estado</th>
                                    <th>Fecha Recepcion</th>
                                    <th>Semáforo</th>
                                    <th>Activo?</th>
                                    <th>Act./Desac.</th>
                                    <th>Contactos</th>
                                    <th>Adjuntar</th>
                                    <th>Edit.</th>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Aquí se cargan los datos de la tabla -->
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th class="wp-5p"></th>
                                    <th></th>
                                    <!-- 21-07-2025 -->
                                    <!-- <th class="wp-15p"><input type="text" placeholder="Buscar Método" class="form-control form-control-sm" /></th> -->

                                    <th class="wp-8p">
                                        <select id="filtroMetodoPie" class="form-control tx-10">
                                            <option value="" style="height:20px;">Todos</option>

                                            <option value="whatsapp" style="height:20px;">
                                                WhatsApp
                                            </option>

                                            <option value="email" style="height:20px;">Email</option>

                                            <option value="llamada" style="height:20px;">Llamada</option>

                                            <option value="presencia" style="height:20px;">Presencia</option>
                                        </select>
                                    </th>

                                    <th class="wp-15p"><input type="text" placeholder="Buscar Comunicante" class="form-control form-control-sm" /></th>
                                    <th class="d-none"><input type="text" class="form-control form-control-sm" disabled /></th>
                                    <th class="d-none"><input type="text" class="form-control form-control-sm" disabled /></th>
                                    <th class="d-none"><input type="text" class="form-control form-control-sm" disabled /></th>
                                    <th class="d-none"><input type="text" class="form-control form-control-sm" disabled /></th>
                                    <th class="d-none"><input type="text" class="form-control form-control-sm" disabled /></th>
                                    <th class="d-none"><input type="text" class="form-control form-control-sm" disabled /></th>
                                    <th class="wp-15p"><input type="text" placeholder="Buscar Comercial" class="form-control form-control-sm" /></th>
                                    <th class="wp-10p"><input type="text" placeholder="Buscar Estado" class="form-control form-control-sm" /></th>
                                    <th class="wp-10p"><input type="text" id="fechaRecepcionFiltroPie" placeholder="dd-mm-yyyy hh:mm:ss" class="form-control form-control-sm" /></th>
                                    <th class="wp-5p"><input type="text" placeholder="NO BUSCAR" class="d-none" /></th>
                                    <th class="wp-5p">

                                        <select id="filtroActivoPie" class="form-control tx-10">
                                            <option value="" style="height:20px;">Todos</option>
                                            <option value="1" style="height:20px;">
                                                Activos
                                            </option>
                                            <option value="0" style="height:20px;">Inactivos</option>
                                        </select>

                                    </th>
                                    <th></th>
                                    <th class="wp-5p"></th>
                                    <th class="wp-5p"></th>
                                    <th class="wp-5p"></th>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div><!-- table-wrapper -->
                </div><!-- br-section-wrapper -->
            </div><!-- br-pagebody -->

            <!-- <footer class="br-footer">
        <?php ##include_once('../../config/template/mainFooter.php') 
        ?>
    </footer> -->
        </div><!-- br-mainpanel -->

        <!-- ----------------------- -->
        <!--     mainFooter.php      -->
        <!-- ----------------------- -->
        <footer class="br-footer">
            <?php include_once('../../config/template/mainFooter.php') ?>
        </footer>
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

        <?php //include_once('detalleProductoBra.php') 
        ?>

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
        <!-- Modal de Ayuda Llamadas -->
        <?php include_once('ayudaLlamadas.php') ?>
        <!-- Fin Modal de Ayuda Llamadas -->
        <?php include_once('mantenimientoLlamadas.php') ?>
        <?php include_once('mantenimientoAdjuntos.php') ?>

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
        <script type="text/javascript" src="mntllamadas.js"></script>

    <script>
        // Colapsar el sidebar al cargar la página
        $(document).ready(function() {
            $('body').addClass('collapsed-menu');
            $('.br-sideleft').addClass('collapsed');
        });
    </script>

    </body>

    </html>