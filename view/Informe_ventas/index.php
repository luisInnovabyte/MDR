<!-- ============================================================ -->
<!-- Informe de Ventas por Período - MDR ERP Manager           -->
<!-- Análisis de ingresos por mes, cliente y familia           -->
<!-- ============================================================ -->
<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <?php include_once('../../config/template/mainHead.php') ?>
    <style>
        /* ---- KPI Cards ---- */
        .kpi-card { border-left: 4px solid; border-radius: 6px; }
        .kpi-card.kpi-total     { border-left-color: #6c757d; }
        .kpi-card.kpi-ppto      { border-left-color: #0d6efd; }
        .kpi-card.kpi-ticket    { border-left-color: #198754; }
        .kpi-card.kpi-mes       { border-left-color: #fd7e14; }

        .kpi-valor { font-size: 1.6rem; font-weight: 700; line-height: 1.2; }
        .kpi-label { font-size: 0.78rem; text-transform: uppercase; letter-spacing: .05em; color: #6c757d; }
        .kpi-sub   { font-size: 0.82rem; color: #6c757d; }

        /* ---- Filtros ---- */
        #barraFiltros { background: #f8f9fa; border-radius: 8px; padding: 12px 16px; }

        /* ---- Gráfico ---- */
        #chartVentasMensuales { max-height: 280px; }

        /* ---- DataTable ---- */
        .dataTables_wrapper { overflow-x: auto; }
    </style>
</head>

<body>

    <?php require_once('../../config/template/mainLogo.php') ?>

    <div class="br-sideleft sideleft-scrollbar">
        <?php require_once('../../config/template/mainSidebar.php') ?>
        <?php require_once('../../config/template/mainSidebarDown.php') ?>
        <br>
    </div>

    <div class="br-header">
        <?php include_once('../../config/template/mainHeader.php') ?>
    </div>

    <div class="br-sideright">
        <?php include_once('../../config/template/mainRightPanel.php') ?>
    </div>

    <!-- ########## MAIN PANEL ########## -->
    <div class="br-mainpanel">
        <div class="br-pageheader pd-y-15 pd-l-20">
            <nav class="breadcrumb pd-0 mg-0 tx-12">
                <a class="breadcrumb-item" href="../Home/index.php">Inicio</a>
                <span class="breadcrumb-item active">Informe de Ventas</span>
            </nav>
        </div>

        <div class="br-pagebody">
            <div class="br-section-wrapper">

                <!-- CABECERA -->
                <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                    <div>
                        <h4 class="mb-0">
                            <i class="fas fa-chart-line me-2 text-primary"></i>Informe de Ventas por Período
                        </h4>
                        <p class="text-muted mb-0 small">
                            Análisis de ingresos en presupuestos aceptados y facturados
                        </p>
                    </div>
                    <button class="btn btn-sm btn-outline-secondary" onclick="recargarTodo()">
                        <i class="fas fa-sync-alt me-1"></i>Actualizar
                    </button>
                </div>

                <!-- BARRA DE FILTROS -->
                <div id="barraFiltros" class="mb-4 d-flex flex-wrap align-items-end gap-3">
                    <div>
                        <label class="form-label mb-1 small">Año</label>
                        <select class="form-select form-select-sm" id="filtroAnyo" style="min-width:100px;">
                            <option value="0">Todos</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label mb-1 small">Mes</label>
                        <select class="form-select form-select-sm" id="filtroMes" style="min-width:130px;">
                            <option value="0">Todos</option>
                            <option value="1">Enero</option>
                            <option value="2">Febrero</option>
                            <option value="3">Marzo</option>
                            <option value="4">Abril</option>
                            <option value="5">Mayo</option>
                            <option value="6">Junio</option>
                            <option value="7">Julio</option>
                            <option value="8">Agosto</option>
                            <option value="9">Septiembre</option>
                            <option value="10">Octubre</option>
                            <option value="11">Noviembre</option>
                            <option value="12">Diciembre</option>
                        </select>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-primary" onclick="aplicarFiltros()">
                            <i class="fas fa-filter me-1"></i>Filtrar
                        </button>
                        <button class="btn btn-sm btn-outline-secondary" onclick="limpiarFiltros()">
                            <i class="fas fa-times me-1"></i>Limpiar
                        </button>
                    </div>
                </div>

                <!-- KPI CARDS -->
                <div class="row row-cols-2 row-cols-md-4 g-3 mb-4" id="seccionKPIs">
                    <div class="col">
                        <div class="card kpi-card kpi-total h-100 shadow-sm">
                            <div class="card-body py-3">
                                <div class="kpi-label">Total Facturado</div>
                                <div class="kpi-valor text-secondary" id="kpi-total">—</div>
                                <div class="kpi-sub" id="kpi-total-sub">—</div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card kpi-card kpi-ppto h-100 shadow-sm">
                            <div class="card-body py-3">
                                <div class="kpi-label">Presupuestos</div>
                                <div class="kpi-valor text-primary" id="kpi-pptos">—</div>
                                <div class="kpi-sub">Aprobados / Facturados</div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card kpi-card kpi-ticket h-100 shadow-sm">
                            <div class="card-body py-3">
                                <div class="kpi-label">Ticket Promedio</div>
                                <div class="kpi-valor text-success" id="kpi-ticket">—</div>
                                <div class="kpi-sub">Por presupuesto</div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card kpi-card kpi-mes h-100 shadow-sm">
                            <div class="card-body py-3">
                                <div class="kpi-label">Mejor Mes</div>
                                <div class="kpi-valor text-warning" id="kpi-mes-top">—</div>
                                <div class="kpi-sub" id="kpi-mes-top-sub">—</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- GRÁFICO DE BARRAS MENSUAL -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <span class="fw-semibold">
                            <i class="fas fa-bar-chart me-2"></i>Ingresos por Mes
                        </span>
                        <span class="small text-muted" id="lblGraficoAnyo"></span>
                    </div>
                    <div class="card-body">
                        <canvas id="chartVentasMensuales"></canvas>
                    </div>
                </div>

                <!-- FILA: Top Clientes + Por Familia -->
                <div class="row g-4">

                    <!-- TOP CLIENTES -->
                    <div class="col-12 col-xl-6">
                        <div class="card shadow-sm h-100">
                            <div class="card-header fw-semibold">
                                <i class="fas fa-users me-2"></i>Top 10 Clientes
                            </div>
                            <div class="card-body p-0">
                                <table id="tblTopClientes" class="table display nowrap mb-0" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>Cliente</th>
                                            <th class="text-center">Presupuestos</th>
                                            <th class="text-end">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- POR FAMILIA -->
                    <div class="col-12 col-xl-6">
                        <div class="card shadow-sm h-100">
                            <div class="card-header fw-semibold">
                                <i class="fas fa-layer-group me-2"></i>Ventas por Familia de Artículo
                            </div>
                            <div class="card-body p-0">
                                <table id="tblPorFamilia" class="table display nowrap mb-0" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>Familia</th>
                                            <th class="text-center">Pptos.</th>
                                            <th class="text-center">Uds.</th>
                                            <th class="text-end">Total</th>
                                            <th class="text-end">%</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div><!-- row -->

            </div><!-- br-section-wrapper -->
        </div><!-- br-pagebody -->

        <footer class="br-footer">
            <?php include_once('../../config/template/mainFooter.php') ?>
        </footer>
    </div><!-- br-mainpanel -->

    <?php include_once('../../config/template/mainJs.php') ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script src="js/informeventas.js"></script>

    <script>
        $(document).ready(function () {
            $('body').addClass('collapsed-menu');
            $('.br-sideleft').addClass('collapsed');
        });
    </script>
</body>
</html>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div><!-- /row -->

            </div><!-- br-section-wrapper -->
        </div><!-- br-pagebody -->

        <footer class="br-footer">
            <?php include_once('../../config/template/mainFooter.php') ?>
        </footer>
    </div><!-- br-mainpanel -->

    <!-- Chart.js (CDN compatible con el resto del proyecto) -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <script src="js/informeventas.js"></script>
</body>
</html>
