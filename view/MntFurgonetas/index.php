<?php
// ----------------------
//   Comprobar permisos
// ----------------------
$moduloActual = 'mantenimientos';
require_once('../../config/template/verificarPermiso.php');

// Prueba de documento
// Inicializar variables por defecto
$totalFurgonetas = 0;
$totalOperativas = 0;
$totalTaller = 0;
$totalBaja = 0;

// Inicializar variables de alertas
$itvProximasVencer = 0;
$segurosProximosVencer = 0;
$sinDatosSeguro = 0;
$sinFechaItv = 0;

// Cargar estadísticas de furgonetas
try {
    require_once('../../models/Furgoneta.php');
    $furgonetaModel = new Furgoneta();
    
    // Obtener estadísticas
    $estadisticas = $furgonetaModel->obtenerEstadisticas();
    
    if ($estadisticas && is_array($estadisticas)) {
        $totalFurgonetas = $estadisticas['total'] ?? 0;
        $totalOperativas = $estadisticas['operativas'] ?? 0;
        $totalTaller = $estadisticas['taller'] ?? 0;
        $totalBaja = $estadisticas['baja'] ?? 0;
    }

    // Obtener alertas
    $alertas = $furgonetaModel->obtenerAlertas();
    
    if ($alertas && is_array($alertas)) {
        $itvProximasVencer = $alertas['itv_proximas_vencer'] ?? 0;
        $segurosProximosVencer = $alertas['seguros_proximos_vencer'] ?? 0;
        $sinDatosSeguro = $alertas['sin_datos_seguro'] ?? 0;
        $sinFechaItv = $alertas['sin_fecha_itv'] ?? 0;
    }

} catch (Throwable $e) {
    // Valores por defecto en caso de error
    $totalFurgonetas = 0;
    $totalOperativas = 0;
    $totalTaller = 0;
    $totalBaja = 0;
    $itvProximasVencer = 0;
    $segurosProximosVencer = 0;
    $sinDatosSeguro = 0;
    $sinFechaItv = 0;
    error_log("Error al cargar estadísticas de furgonetas: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <?php include_once('../../config/template/mainHead.php') ?>
    <title>Gestión de Furgonetas - MDR</title>
</head>

<body>
    <!-- LEFT PANEL -->
    <?php require_once('../../config/template/mainLogo.php') ?>

    <div class="br-sideleft sideleft-scrollbar">
        <?php require_once('../../config/template/mainSidebar.php') ?>
        <?php require_once('../../config/template/mainSidebarDown.php') ?>
    </div><!-- br-sideleft -->

    <!-- HEAD PANEL -->
    <div class="br-header">
        <?php include_once('../../config/template/mainHeader.php') ?>
    </div><!-- br-header -->

    <!-- RIGHT PANEL -->
    <div class="br-sideright">
        <?php include_once('../../config/template/mainRightPanel.php') ?>
    </div><!-- br-sideright -->

    <!-- MAIN PANEL -->
    <div class="br-mainpanel">
        <!-- Breadcrumb -->
        <div class="br-pageheader">
            <nav class="breadcrumb pd-0 mg-0 tx-12">
                 <a class="breadcrumb-item" href="../Dashboard/index.php">Dashboard</a>
                <span class="breadcrumb-item active">Furgonetas</span>
            </nav>
        </div><!-- br-pageheader -->

        <!-- Título de página con ayuda -->
        <div class="br-pagetitle d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <i class="bi bi-truck tx-50 lh-0"></i>
                <div class="d-inline-block align-middle">
                    <h4 class="mg-b-0">Gestión de Furgonetas</h4>
                    <p class="mg-b-0 tx-gray-600">Administración de vehículos de la empresa</p>
                </div>
                <button type="button" class="btn btn-link p-0 ms-2" data-bs-toggle="modal" data-bs-target="#modalAyudaFurgonetas" title="Ayuda sobre el módulo">
                    <i class="bi bi-question-circle text-primary" style="font-size: 1.3rem;"></i>
                </button>
            </div>
        </div><!-- br-pagetitle -->

        <div class="br-pagebody">
            
            <!-- Panel de Estadísticas -->
            <div class="row row-sm mb-4">
                <!-- Card 1: Total Furgonetas -->
                <div class="col-lg-3 col-md-3 col-sm-12">
                    <div class="card shadow-sm border-primary">
                        <div class="card-body text-center">
                            <div class="d-flex align-items-center justify-content-center mb-2">
                                <i class="bi bi-truck text-primary me-2" style="font-size: 2rem;"></i>
                                <h6 class="mb-0 text-muted">Total Furgonetas</h6>
                            </div>
                            <h2 class="mb-0 text-primary fw-bold">
                                <?php echo $totalFurgonetas; ?>
                            </h2>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Operativas -->
                <div class="col-lg-3 col-md-3 col-sm-12">
                    <div class="card shadow-sm border-success">
                        <div class="card-body text-center">
                            <div class="d-flex align-items-center justify-content-center mb-2">
                                <i class="bi bi-check-circle text-success me-2" style="font-size: 2rem;"></i>
                                <h6 class="mb-0 text-muted">Operativas</h6>
                            </div>
                            <h2 class="mb-0 text-success fw-bold">
                                <?php echo $totalOperativas; ?>
                            </h2>
                        </div>
                    </div>
                </div>

                <!-- Card 3: En Taller -->
                <div class="col-lg-3 col-md-3 col-sm-12">
                    <div class="card shadow-sm border-warning">
                        <div class="card-body text-center">
                            <div class="d-flex align-items-center justify-content-center mb-2">
                                <i class="bi bi-tools text-warning me-2" style="font-size: 2rem;"></i>
                                <h6 class="mb-0 text-muted">En Taller</h6>
                            </div>
                            <h2 class="mb-0 text-warning fw-bold">
                                <?php echo $totalTaller; ?>
                            </h2>
                        </div>
                    </div>
                </div>

                <!-- Card 4: Dadas de Baja -->
                <div class="col-lg-3 col-md-3 col-sm-12">
                    <div class="card shadow-sm border-danger">
                        <div class="card-body text-center">
                            <div class="d-flex align-items-center justify-content-center mb-2">
                                <i class="bi bi-x-circle text-danger me-2" style="font-size: 2rem;"></i>
                                <h6 class="mb-0 text-muted">Dadas de Baja</h6>
                            </div>
                            <h2 class="mb-0 text-danger fw-bold">
                                <?php echo $totalBaja; ?>
                            </h2>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Fin Panel de Estadísticas -->

            <!-- Panel de Alertas -->
            <div class="row row-sm mb-4">
                <!-- Card 1: ITV Próximas a Vencer -->
                <div class="col-lg-3 col-md-3 col-sm-12">
                    <div class="card shadow-sm <?php echo $itvProximasVencer > 0 ? 'border-danger' : 'border-secondary'; ?>">
                        <div class="card-body text-center">
                            <div class="d-flex align-items-center justify-content-center mb-2">
                                <i class="bi bi-exclamation-triangle <?php echo $itvProximasVencer > 0 ? 'text-danger' : 'text-secondary'; ?> me-2" style="font-size: 2rem;"></i>
                                <h6 class="mb-0 text-muted small">ITV Próximas a Vencer</h6>
                            </div>
                            <h2 class="mb-0 <?php echo $itvProximasVencer > 0 ? 'text-danger' : 'text-secondary'; ?> fw-bold">
                                <?php echo $itvProximasVencer; ?>
                            </h2>
                            <small class="text-muted">(Próximos 30 días)</small>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Seguros Próximos a Vencer -->
                <div class="col-lg-3 col-md-3 col-sm-12">
                    <div class="card shadow-sm <?php echo $segurosProximosVencer > 0 ? 'border-danger' : 'border-secondary'; ?>">
                        <div class="card-body text-center">
                            <div class="d-flex align-items-center justify-content-center mb-2">
                                <i class="bi bi-shield-exclamation <?php echo $segurosProximosVencer > 0 ? 'text-danger' : 'text-secondary'; ?> me-2" style="font-size: 2rem;"></i>
                                <h6 class="mb-0 text-muted small">Seguros a Vencer</h6>
                            </div>
                            <h2 class="mb-0 <?php echo $segurosProximosVencer > 0 ? 'text-danger' : 'text-secondary'; ?> fw-bold">
                                <?php echo $segurosProximosVencer; ?>
                            </h2>
                            <small class="text-muted">(Próximos 30 días)</small>
                        </div>
                    </div>
                </div>

                <!-- Card 3: Sin Datos de Seguro -->
                <div class="col-lg-3 col-md-3 col-sm-12">
                    <div class="card shadow-sm <?php echo $sinDatosSeguro > 0 ? 'border-warning' : 'border-secondary'; ?>">
                        <div class="card-body text-center">
                            <div class="d-flex align-items-center justify-content-center mb-2">
                                <i class="bi bi-shield-x <?php echo $sinDatosSeguro > 0 ? 'text-warning' : 'text-secondary'; ?> me-2" style="font-size: 2rem;"></i>
                                <h6 class="mb-0 text-muted small">Sin Datos de Seguro</h6>
                            </div>
                            <h2 class="mb-0 <?php echo $sinDatosSeguro > 0 ? 'text-warning' : 'text-secondary'; ?> fw-bold">
                                <?php echo $sinDatosSeguro; ?>
                            </h2>
                            <small class="text-muted">(Revisar datos)</small>
                        </div>
                    </div>
                </div>

                <!-- Card 4: Sin Fecha de ITV -->
                <div class="col-lg-3 col-md-3 col-sm-12">
                    <div class="card shadow-sm <?php echo $sinFechaItv > 0 ? 'border-warning' : 'border-secondary'; ?>">
                        <div class="card-body text-center">
                            <div class="d-flex align-items-center justify-content-center mb-2">
                                <i class="bi bi-calendar-x <?php echo $sinFechaItv > 0 ? 'text-warning' : 'text-secondary'; ?> me-2" style="font-size: 2rem;"></i>
                                <h6 class="mb-0 text-muted small">Sin Fecha de ITV</h6>
                            </div>
                            <h2 class="mb-0 <?php echo $sinFechaItv > 0 ? 'text-warning' : 'text-secondary'; ?> fw-bold">
                                <?php echo $sinFechaItv; ?>
                            </h2>
                            <small class="text-muted">(Configurar ITV)</small>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Fin Panel de Alertas -->

            <!-- Tabla de furgonetas -->
            <div class="br-section-wrapper">
                <!-- Fila contenedora -->
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
                    <!-- Contenedor de alerta expandible -->
                    <div class="flex-grow-1 me-3" style="min-width: 300px;">
                        <!-- Alerta de filtro activo -->
                        <div class="alert alert-warning alert-dismissible fade show mb-0 w-100" 
                             role="alert" 
                             id="filter-alert" 
                             style="display: none;">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="truncate">
                                    <i class="fas fa-filter me-2"></i>
                                    <span>Filtros aplicados: </span>
                                    <span id="active-filters-text" class="text-truncate"></span>
                                </div>
                                <button type="button" 
                                        class="btn btn-sm btn-outline-warning ms-2 flex-shrink-0" 
                                        id="clear-filter">
                                    Limpiar filtros
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Botón Nueva Furgoneta -->
                    <a href="formularioFurgoneta.php?modo=nuevo" 
                       class="btn btn-oblong btn-outline-primary flex-shrink-0 mt-2 mt-sm-0">
                        <i class="fas fa-plus-circle me-2"></i>Nueva Furgoneta
                    </a>
                </div>

                <!-- Tabla de furgonetas -->
                <div class="table-wrapper">
                    <table id="furgonetas_data" class="table display responsive nowrap">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Id</th>
                                <th>Matrícula</th>
                                <th>Modelo</th>
                                <th>Año</th>
                                <th>Próxima ITV</th>
                                <th>Seguro Vence</th>
                                <th>Estado</th>
                                <th>Activo</th>
                                <th>Act./Desac.</th>
                                <th>Editar</th>
                                <th>Mantenimiento</th>
                                <th>Kilometraje</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Datos se cargarán aquí -->
                        </tbody>
                        <tfoot>
                            <tr>
                                <th></th>
                                <th class="d-none">
                                    <input type="text" placeholder="Buscar ID" 
                                           class="form-control form-control-sm" />
                                </th>
                                <th>
                                    <input type="text" placeholder="Buscar matrícula" 
                                           class="form-control form-control-sm" />
                                </th>
                                <th>
                                    <input type="text" placeholder="Buscar modelo" 
                                           class="form-control form-control-sm" />
                                </th>
                                <th>
                                    <input type="text" placeholder="Buscar año" 
                                           class="form-control form-control-sm" />
                                </th>
                                <th></th>
                                <th></th>
                                <th>
                                    <select class="form-control form-control-sm" 
                                            title="Filtrar por estado">
                                        <option value="">Todos</option>
                                        <option value="operativa">Operativa</option>
                                        <option value="taller">Taller</option>
                                        <option value="baja">Baja</option>
                                    </select>
                                </th>
                                <th>
                                    <select class="form-control form-control-sm" 
                                            title="Filtrar por activo">
                                        <option value="">Todos</option>
                                        <option value="1">Activo</option>
                                        <option value="0">Inactivo</option>
                                    </select>
                                </th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div><!-- table-wrapper -->
            </div><!-- br-section-wrapper -->
        </div><!-- br-pagebody -->

        <?php include_once('../../config/template/mainFooter.php') ?>
    </div><!-- br-mainpanel -->

    <!-- Scripts de plantilla -->
    <?php include_once('../../config/template/mainJs.php') ?>

    <!-- Scripts de componentes -->
    <script src="../../public/js/tooltip-colored.js"></script>
    <script src="../../public/js/popover-colored.js"></script>

    <!-- Script específico del módulo -->
    <script type="text/javascript" src="mntfurgoneta.js"></script>

    <!-- Modal de Ayuda -->
    <?php include_once('ayudaFurgonetas.php') ?>

    <!-- Script adicional: Colapsar sidebar -->
    <script>
        $(document).ready(function() {
            $('body').addClass('collapsed-menu');
            $('.br-sideleft').addClass('collapsed');
        });
    </script>
</body>

</html>
