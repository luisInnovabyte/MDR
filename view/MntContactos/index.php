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
    <?php
    session_start();

    $idComercial = isset($_SESSION['id_comercial']) ? $_SESSION['id_comercial'] : null;
    $idLlamada = isset($_GET['id_llamada']) ? $_GET['id_llamada'] : null;
    ?>
    <script>
        const idComercial = <?php echo json_encode($idComercial); ?>;
        const idLlamada = <?php echo json_encode($idLlamada); ?>;
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
                <span class="breadcrumb-item active">Mantenimiento Contactos</span>
            </nav>
        </div><!-- br-pageheader -->

        <div class="br-pagetitle">
            <i class="icon icon ion-ios-bookmarks-outline"></i>
            <div>
                <div class="d-flex align-items-center">
                <h4>Mantenimiento de Contactos</h4>
                <button type="button" class="btn btn-link p-0 ms-1" data-toggle="modal" data-target="#modalAyudaContactos" title="Ayuda sobre el módulo">
                    <i class="bi bi-question-circle text-primary" style="font-size: 1.3rem;"></i>
                </button>
                </div>
                <p class="mg-b-0">Tabla básica para el mantenimiento de los contactos</p>
                
            </div>
        </div><!-- d-flex -->

        <div class="br-pagebody">
            <div class="br-section-wrapper">

                <!-- Alerta de filtros y botón de nuevo contacto -->
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
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

                    <div class="d-flex flex-shrink-0 mt-2 mt-sm-0 gap-2">
                        <button class="btn btn-oblong btn-outline-primary" id="btnnuevo">
                            <i class="fas fa-plus-circle me-2"></i>Nuevo contacto
                        </button>


                        <a href="../MntLlamadas/index.php" class="btn btn-oblong btn-outline-info">
                            <i class="fas fa-phone me-2"></i>Ir a llamadas
                        </a>
                    </div>
                </div>

                <!-- Acordeón de Filtros -->
                <div id="accordion" class="accordion mb-3">
                    <div class="card">
                        <div class="card-header p-0">
                            <h6 class="mg-b-0">
                                <a id="accordion-toggle"
                                    class="d-block p-3 bg-primary text-white collapsed"
                                    data-toggle="collapse"
                                    href="#collapseOne"
                                    style="text-decoration: none;">
                                    <i class="fas fa-filter me-2"></i>Filtros de Contactos
                                </a>
                            </h6>
                        </div>

                        <div id="collapseOne" class="collapse" data-parent="#accordion">
                            <div class="card-body pd-20 pt-3">
                                <div class="row g-3">
                                    <!-- Bloque Estado -->
                                    <div class="col-md-4">
                                        <div class="card shadow-sm h-100">
                                            <div class="card-header bg-white border-bottom py-2">
                                                <h6 class="mb-0 text-primary">
                                                    <i class="fas fa-toggle-on me-2"></i>Estado Contactos
                                                </h6>
                                            </div>
                                            <div class="card-body p-2">
                                                <div class="status-selector">
                                                    <div class="status-option">
                                                        <input type="radio" name="filterStatus" id="filterAll" value="all" class="status-radio" checked>
                                                        <label for="filterAll" class="status-label">
                                                            <span class="status-icon"><i class="fas fa-layer-group"></i></span>
                                                            <span class="status-text">Todos</span>
                                                            <span class="status-badge"></span>
                                                        </label>
                                                    </div>
                                                    <div class="status-option">
                                                        <input type="radio" name="filterStatus" id="filterActive" value="1" class="status-radio">
                                                        <label for="filterActive" class="status-label">
                                                            <span class="status-icon"><i class="fas fa-check-circle"></i></span>
                                                            <span class="status-text">Activado</span>
                                                            <span class="status-badge"></span>
                                                        </label>
                                                    </div>
                                                    <div class="status-option">
                                                        <input type="radio" name="filterStatus" id="filterInactive" value="0" class="status-radio">
                                                        <label for="filterInactive" class="status-label">
                                                            <span class="status-icon"><i class="fas fa-times-circle"></i></span>
                                                            <span class="status-text">Desactivado</span>
                                                            <span class="status-badge"></span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Bloque Fechas -->
                                    <div class="col-md-8">
                                        <div class="card shadow-sm h-100">
                                            <div class="card-header bg-white border-bottom py-2">
                                                <h6 class="mb-0 text-primary">
                                                    <i class="fas fa-calendar-day me-2"></i>Fechas de Contacto
                                                </h6>
                                            </div>
                                            <div class="card-body d-flex justify-content-center align-items-center">
                                                <div class="row g-3 w-100 justify-content-center align-items-center">
                                                    <!-- Fecha Hora Contacto -->
                                                    <div class="col-md-6 d-flex flex-column justify-content-center align-items-center">
                                                        <label class="form-label small text-muted mb-1">Fecha Contacto</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text bg-light">
                                                                <i class="far fa-calendar-alt"></i>
                                                            </span>
                                                            <input id="filtroFechaHoraContacto" type="text"
                                                                class="form-control datepicker text-start"
                                                                placeholder="dd-mm-aaaa" readonly>
                                                        </div>
                                                        <span class="text-info small mt-1 cursor-pointer" id="borrarFechaHoraContactoFiltro">
                                                            <i class="fas fa-times-circle"></i> Borrar fecha contacto
                                                        </span>
                                                    </div>

                                                    <!-- Fecha Visita Cerrada -->
                                                    <div class="col-md-6 d-flex flex-column justify-content-center align-items-center">
                                                        <label class="form-label small text-muted mb-1">Fecha Visita</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text bg-light">
                                                                <i class="far fa-calendar-alt"></i>
                                                            </span>
                                                            <input id="filtroFechaVisitaCerrada" type="text"
                                                                class="form-control datepicker text-start"
                                                                placeholder="dd-mm-aaaa" readonly>
                                                        </div>
                                                        <span class="text-info small mt-1 cursor-pointer" id="borrarFechaVisitaCerradaFiltro">
                                                            <i class="fas fa-times-circle"></i> Borrar fecha visita
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div><!-- row -->
                            </div><!-- card-body -->
                        </div><!-- collapseOne -->
                    </div><!-- card -->
                </div><!-- accordion -->

                <!-- Tabla -->
                <div class="table-wrapper table order-column hover row-border stripe responsive">
                    <table id="contactos_data" class="table display responsive nowrap">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Id Contacto</th>
                                <th>Nombre Comunicante</th>
                                <th>Fecha Hora Contacto</th>
                                <th>Método</th>
                                <th>Observaciones</th>
                                <th>Fecha Visita Cerrada</th>
                                <th>Estado</th>
                                <th>Act/Des Estado</th>
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
                                <th><!-- Columna 0: Botón "Más" --></th>
                                <th class="d-none"><input type="text" placeholder="ID Contacto" class="form-control form-control-sm" disabled /></th>
                                <th><input type="text" placeholder="Buscar Comunicante" class="form-control form-control-sm" /></th>
                                <th><input type="text" id="fechaHoraContactoFiltroPies" placeholder="Buscar fecha hora contacto: dd-mm-yyyy hh:mm" class="form-control form-control-sm" /></th>
                                <th></th>
                                <th class="d-none"><input type="text" placeholder="Observaciones" class="form-control form-control-sm" disabled /></th>
                                <th class="d-none"><input type="text" placeholder="Fecha Visita" class="form-control form-control-sm" disabled /></th>
                                <th><input type="text" placeholder="1=Activo,0=Inactivo" class="form-control form-control-sm" /></th>
                                <th><!-- Columna 8: Botón Act/Des --></th>
                                <th><!-- Columna 9: Botón Editar --></th>
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

    <!-- *********************************** -->
    <!-- MODAL QUE SE DISPARA DESDE EL BOTON -->
    <!--        DE LA PROPIA TABLA           -->
    <!--        columDef                     -->
    <!-- *********************************** -->

    <!--Ayuda Contactos-->
    <?php include_once('ayudaContactos.php')  ?>
    <!------------------->




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

    <?php include_once('mantenimientoContactos.php') ?>


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
    <script type="text/javascript" src="mntcontactos.js"></script>

</body>

</html>