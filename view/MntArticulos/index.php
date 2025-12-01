<?php 
// ----------------------
//   Comprobar permisos
// ----------------------
$moduloActual = 'usuarios';
require_once('../../config/template/verificarPermiso.php');

// Inicializar variables por defecto
$totalArticulos = 0;
$totalArticulosActivos = 0;

// Cargar estadísticas de artículos
try {
    require_once('../../models/Articulo.php');
    $articuloModel = new Articulo();
    
    $totalArticulos = $articuloModel->total_articulo();
    if ($totalArticulos === false || $totalArticulos === null) {
        $totalArticulos = 0;
    }
    
    $totalArticulosActivos = $articuloModel->total_articulo_activo();
    if ($totalArticulosActivos === false || $totalArticulosActivos === null) {
        $totalArticulosActivos = 0;
    }

    $totalArticulosKits = $articuloModel->total_articulo_activo_kit();
    if ($totalArticulosKits === false || $totalArticulosKits === null) {
        $totalArticulosKits = 0;
    }

     $totalArticulosCoeficientes = $articuloModel->total_articulo_activo_coeficiente();
    if ($totalArticulosCoeficientes === false || $totalArticulosCoeficientes === null) {
        $totalArticulosCoeficientes = 0;
    }


} catch (Throwable $e) {
    // Captura cualquier error (Exception o Error)
    $totalArticulos = 0;
    $totalArticulosActivos = 0;
    error_log("Error al cargar estadísticas de artículos: " . $e->getMessage() . " en " . $e->getFile() . ":" . $e->getLine());
}
?>
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
            <span class="breadcrumb-item active">Mantenimiento Artículos</span>
        </nav>
    </div><!-- br-pageheader -->
    
    <div class="br-pagetitle">
        <div class="d-flex align-items-center">
            <h4 class="mb-0 me-2">Mantenimiento de Artículos</h4>
            <button type="button" class="btn btn-link p-0 ms-1" data-bs-toggle="modal" data-bs-target="#modalAyudaArticulos" title="Ayuda sobre el módulo">
                <i class="bi bi-question-circle text-primary" style="font-size: 1.3rem;"></i>
            </button>
        </div>
        <br>
    </div><!-- d-flex -->

    <div class="br-pagebody">
        <!-- Panel de Estadísticas -->
        <div class="row row-sm mb-4">
            <div class="col-lg-3 col-md-3 col-sm-12">
                <div class="card shadow-sm border-primary">
                    <div class="card-body text-center">
                        <div class="d-flex align-items-center justify-content-center mb-2">
                            <i class="bi bi-box-seam text-primary me-2" style="font-size: 2rem;"></i>
                            <h6 class="mb-0 text-muted">Total Artículos</h6>
                        </div>
                        <h2 class="mb-0 text-primary fw-bold"><?php echo $totalArticulos; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-12">
                <div class="card shadow-sm border-success">
                    <div class="card-body text-center">
                        <div class="d-flex align-items-center justify-content-center mb-2">
                            <i class="bi bi-check-circle text-success me-2" style="font-size: 2rem;"></i>
                            <h6 class="mb-0 text-muted">Activos</h6>
                        </div>
                        <h2 class="mb-0 text-success fw-bold"><?php echo $totalArticulosActivos; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-12">
                <div class="card shadow-sm border-info">
                    <div class="card-body text-center">
                        <div class="d-flex align-items-center justify-content-center mb-2">
                            <i class="bi bi-box-seam-fill text-info me-2" style="font-size: 2rem;"></i>
                            <h6 class="mb-0 text-muted">Kits</h6>
                        </div>
                        <h2 class="mb-0 text-success fw-bold"><?php echo $totalArticulosKits; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-12">
                <div class="card shadow-sm border-warning">
                    <div class="card-body text-center">
                        <div class="d-flex align-items-center justify-content-center mb-2">
                            <i class="bi bi-percent text-warning me-2" style="font-size: 2rem;"></i>
                            <h6 class="mb-0 text-muted">Con Coeficientes</h6>
                        </div>
                        <h2 class="mb-0 text-success fw-bold"><?php echo $totalArticulosCoeficientes; ?></h2>
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

                <!-- Botón Nuevo Artículo -->
                <a href="formularioArticulo.php?modo=nuevo" class="btn btn-oblong btn-outline-primary flex-shrink-0 mt-2 mt-sm-0">
                    <i class="fas fa-plus-circle me-2"></i>Nuevo Artículo 
                </a>
            </div>

                     <!-- Tabla de artículos -->
            <div class="table-wrapper">
                <table id="articulos_data" class="table display responsive nowrap">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Id artículo</th>
                            <th>Código artículo</th>
                            <th>Nombre artículo</th>
                            <th>Familia</th>
                            <th>Precio alquiler</th>
                            <th>Es kit</th>
                            <th>Coeficientes</th>
                            <th>Estado</th>
                            <th>Act./Desac.</th>
                            <th>Edit.</th>
                            <th>Elementos</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Datos se cargarán aquí -->
                    </tbody>
                    <tfoot>
                        <tr>
                            <th></th>
                            <th class="d-none"><input type="text" placeholder="Buscar ID" class="form-control form-control-sm" /></th>
                            <th><input type="text" placeholder="Buscar código" class="form-control form-control-sm" /></th>
                            <th><input type="text" placeholder="Buscar nombre artículo" class="form-control form-control-sm" /></th>
                            <th><input type="text" placeholder="Buscar familia" class="form-control form-control-sm" /></th>
                            <th><input type="text" placeholder="Buscar precio" class="form-control form-control-sm" /></th>
                            <th>
                                <select class="form-control form-control-sm" title="Filtrar por kit">
                                    <option value="">Todos</option>
                                    <option value="1">Es kit</option>
                                    <option value="0">No es kit</option>
                                </select>
                            </th>
                            <th>
                                <select class="form-control form-control-sm" title="Filtrar por coeficientes">
                                    <option value="">Todos</option>
                                    <option value="1">Permite coeficientes</option>
                                    <option value="0">No permite</option>
                                </select>
                            </th>
                            <th>
                                <select class="form-control form-control-sm" title="Filtrar por estado">
                                    <option value="">Todos los estados</option>
                                    <option value="1">Activo</option>
                                    <option value="0">Inactivo</option>
                                </select>
                            </th>
                            <th class="d-none"><input type="text" placeholder="NO Buscar" class="form-control form-control-sm" /></th>
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

    <?php //include_once('detalleProductoBra.php') ?>
    <!-- Se ha sustituido por el formulario independiente -->

    <!-- *********************************** -->
    <!--                FIN                  -->
    <!-- MODAL QUE SE DISPARA DESDE EL BOTON -->
    <!--        DE LA PROPIA TABLA           -->
    <!--             columDef                -->
    <!-- *********************************** -->


    <!-- *********************************** -->
    <!-- MODAL REMOVIDO - AHORA USA FORMULARIO INDEPENDIENTE -->
    <!-- *********************************** -->



<!-- *************************************** -->
    <!-- MODAL QUE SE DISPARA DESDE EL BOTON -->
    <!--             AYUDA                   -->
    <!-- *********************************** -->

    <?php include_once('ayudaArticulos.php') ?>


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
    <script type="text/javascript" src="mntarticulo.js"></script>
    
    <script>
        // Colapsar el sidebar al cargar la página
        $(document).ready(function() {
            $('body').addClass('collapsed-menu');
            $('.br-sideleft').addClass('collapsed');
        });
    </script>

</body>

</html>
