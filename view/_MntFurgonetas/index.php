<?php 
// ----------------------
//   Comprobar permisos
// ----------------------
$moduloActual = 'usuarios';
require_once('../../config/template/verificarPermiso.php');

// Inicializar variables por defecto
$totalFurgonetas = 0;
$totalFurgonetasActivas = 0;
$totalFurgonetasOperativas = 0;
$totalFurgonetasTaller = 0;

// Cargar estadísticas de furgonetas
try {
    require_once('../../models/Furgoneta.php');
    $furgonetaModel = new Furgoneta();
    
    $totalFurgonetas = $furgonetaModel->total_furgonetas();
    if ($totalFurgonetas === false || $totalFurgonetas === null) {
        $totalFurgonetas = 0;
    }
    
    $totalFurgonetasActivas = $furgonetaModel->total_furgonetas_activas();
    if ($totalFurgonetasActivas === false || $totalFurgonetasActivas === null) {
        $totalFurgonetasActivas = 0;
    }

    $totalFurgonetasOperativas = $furgonetaModel->total_furgonetas_operativas();
    if ($totalFurgonetasOperativas === false || $totalFurgonetasOperativas === null) {
        $totalFurgonetasOperativas = 0;
    }

    $totalFurgonetasTaller = $furgonetaModel->total_furgonetas_taller();
    if ($totalFurgonetasTaller === false || $totalFurgonetasTaller === null) {
        $totalFurgonetasTaller = 0;
    }

} catch (Throwable $e) {
    // Captura cualquier error (Exception o Error)
    $totalFurgonetas = 0;
    $totalFurgonetasActivas = 0;
    $totalFurgonetasOperativas = 0;
    $totalFurgonetasTaller = 0;
    error_log("Error al cargar estadísticas de furgonetas: " . $e->getMessage() . " en " . $e->getFile() . ":" . $e->getLine());
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

    <!-- ########## START: MAIN PANEL ########## -->
<div class="br-mainpanel">
    <div class="br-pageheader">
        <nav class="breadcrumb pd-0 mg-0 tx-12">
            <a class="breadcrumb-item" href="../Dashboard/index.php">Dashboard</a>
            <span class="breadcrumb-item active">Mantenimiento Furgonetas</span>
        </nav>
    </div><!-- br-pageheader -->
    
    <div class="br-pagetitle">
        <div class="d-flex align-items-center">
            <h4 class="mb-0 me-2">Mantenimiento de Furgonetas</h4>
            <button type="button" class="btn btn-link p-0 ms-1" data-bs-toggle="modal" data-bs-target="#modalAyudaFurgonetas" title="Ayuda sobre el módulo">
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
                            <i class="fa fa-truck text-primary me-2" style="font-size: 2rem;"></i>
                            <h6 class="mb-0 text-muted">Total Furgonetas</h6>
                        </div>
                        <h2 class="mb-0 text-primary fw-bold"><?php echo $totalFurgonetas; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-12">
                <div class="card shadow-sm border-success">
                    <div class="card-body text-center">
                        <div class="d-flex align-items-center justify-content-center mb-2">
                            <i class="fa fa-check-circle text-success me-2" style="font-size: 2rem;"></i>
                            <h6 class="mb-0 text-muted">Activas</h6>
                        </div>
                        <h2 class="mb-0 text-success fw-bold"><?php echo $totalFurgonetasActivas; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-12">
                <div class="card shadow-sm border-info">
                    <div class="card-body text-center">
                        <div class="d-flex align-items-center justify-content-center mb-2">
                            <i class="fa fa-road text-info me-2" style="font-size: 2rem;"></i>
                            <h6 class="mb-0 text-muted">Operativas</h6>
                        </div>
                        <h2 class="mb-0 text-info fw-bold"><?php echo $totalFurgonetasOperativas; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-12">
                <div class="card shadow-sm border-warning">
                    <div class="card-body text-center">
                        <div class="d-flex align-items-center justify-content-center mb-2">
                            <i class="fa fa-wrench text-warning me-2" style="font-size: 2rem;"></i>
                            <h6 class="mb-0 text-muted">En Taller</h6>
                        </div>
                        <h2 class="mb-0 text-warning fw-bold"><?php echo $totalFurgonetasTaller; ?></h2>
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
                                <i class="fa fa-filter me-2"></i>
                                <span>Filtros aplicados: </span>
                                <span id="active-filters-text" class="text-truncate"></span>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-warning ms-2 flex-shrink-0" id="clear-filter">
                                Limpiar filtros
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Botón Nueva Furgoneta -->
                <a href="formularioFurgoneta.php?modo=nuevo" class="btn btn-oblong btn-outline-primary flex-shrink-0 mt-2 mt-sm-0">
                    <i class="fa fa-plus-circle me-2"></i>Nueva Furgoneta 
                </a>
            </div>

            <!-- Tabla de furgonetas -->
            <div class="table-wrapper">
                <table id="furgonetas_data" class="table display responsive nowrap">
                    <thead>
                        <tr>
                            <th></th>
                            <th>ID</th>
                            <th>Matrícula</th>
                            <th>Marca</th>
                            <th>Modelo</th>
                            <th>Año</th>
                            <th>Estado</th>
                            <th>Activo</th>
                            <th>Act./Desac.</th>
                            <th>Edit.</th>
                            <th>Kilometraje</th>
                            <th>Mantenimiento</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Datos se cargarán aquí -->
                    </tbody>
                    <tfoot>
                        <tr>
                            <th></th>
                            <th class="d-none"><input type="text" placeholder="Buscar ID" class="form-control form-control-sm" /></th>
                            <th><input type="text" placeholder="Buscar matrícula" class="form-control form-control-sm" /></th>
                            <th><input type="text" placeholder="Buscar marca" class="form-control form-control-sm" /></th>
                            <th><input type="text" placeholder="Buscar modelo" class="form-control form-control-sm" /></th>
                            <th><input type="text" placeholder="Buscar año" class="form-control form-control-sm" /></th>
                            <th>
                                <select class="form-control form-control-sm" title="Filtrar por estado">
                                    <option value="">Todos</option>
                                    <option value="operativa">Operativa</option>
                                    <option value="taller">En taller</option>
                                    <option value="averiada">Averiada</option>
                                    <option value="baja">Baja</option>
                                </select>
                            </th>
                            <th>
                                <select class="form-control form-control-sm" title="Filtrar por activo">
                                    <option value="">Todos</option>
                                    <option value="1">Activas</option>
                                    <option value="0">Inactivas</option>
                                </select>
                            </th>
                            <th class="d-none"></th>
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div><!-- br-section-wrapper -->
    </div><!-- br-pagebody -->

    <!-- ---------------------- -->
    <!--      MainFooter.php    -->
    <!-- ---------------------- -->
    <?php include_once('../../config/template/mainFooter.php') ?>
    <!-- ---------------------- -->
    <!--  END MainFooter.php    -->
    <!-- ---------------------- -->

</div><!-- br-mainpanel -->
<!-- ########## END: MAIN PANEL ########## -->

<!-- Modal de ayuda -->
<?php include 'ayudaFurgonetas.php'; ?>

<!-- ---------------------- -->
<!--       mainJs.php        -->
<!-- ---------------------- -->
<?php include_once('../../config/template/mainJs.php') ?>
<!-- ---------------------- -->
<!--     END mainJs.php        -->
<!-- ---------------------- -->

<!-- Scripts específicos de la página -->
<script src="mntfurgoneta.js"></script>

</body>
</html>
