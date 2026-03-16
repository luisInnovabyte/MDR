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
        .kpi-card.kpi-mes       { border-left-color: #198754; }
        .kpi-card.kpi-ppto      { border-left-color: #0d6efd; }
        .kpi-card.kpi-ticket    { border-left-color: #fd7e14; }

        .kpi-valor { font-size: 1.6rem; font-weight: 700; line-height: 1.2; }
        .kpi-label { font-size: 0.78rem; text-transform: uppercase; letter-spacing: .05em; color: #6c757d; }
        .kpi-sub   { font-size: 0.82rem; color: #6c757d; min-height: 20px; line-height: 1.4; }

        /* ---- Filtros ---- */
        #barraFiltros { background: #f8f9fa; border-radius: 8px; padding: 12px 16px; }

        /* ---- Gráfico ---- */
        #chartVentasMensuales { max-height: 280px; }
        .grafico-container { min-height: 300px; }
        .grafico-container canvas { max-height: 350px; }
        
        /* ---- Navegación Gráficos ---- */
        .btn-nav-grafico { width: 36px; height: 36px; padding: 0; }

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
                <div id="barraFiltros" class="mb-4">
                    <!-- Selectores de período -->
                    <div class="d-flex flex-wrap align-items-end gap-3 mb-3">
                        <!-- Período Actual -->
                        <div>
                            <label class="form-label mb-1 small fw-semibold text-primary">Año Actual</label>
                            <select class="form-select form-select-sm" id="filtroAnyoActual" style="min-width:100px;">
                            </select>
                        </div>
                        <div>
                            <label class="form-label mb-1 small fw-semibold text-primary">Mes Actual</label>
                            <select class="form-select form-select-sm" id="filtroMesActual" style="min-width:130px;">
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
                        
                        <!-- Separador visual con más margen -->
                        <div class="border-start px-3 mx-2" style="height:38px;"></div>
                        
                        <!-- Período a Comparar -->
                        <div>
                            <label class="form-label mb-1 small fw-semibold text-info">Año a Comparar</label>
                            <select class="form-select form-select-sm" id="filtroAnyoComparar" style="min-width:120px;">
                                <option value="0">No comparar</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Botones -->
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
                                <div class="kpi-sub" id="kpi-total-sub">Año completo</div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card kpi-card kpi-mes h-100 shadow-sm">
                            <div class="card-body py-3">
                                <div class="kpi-label">Ingresos del Mes</div>
                                <div class="kpi-valor text-success" id="kpi-mes">—</div>
                                <div class="kpi-sub" id="kpi-mes-sub">Mes actual</div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card kpi-card kpi-ppto h-100 shadow-sm">
                            <div class="card-body py-3">
                                <div class="kpi-label">Presupuestos</div>
                                <div class="kpi-valor text-primary" id="kpi-pptos">—</div>
                                <div class="kpi-sub" id="kpi-pptos-sub">Año completo</div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card kpi-card kpi-ticket h-100 shadow-sm">
                            <div class="card-body py-3">
                                <div class="kpi-label">Ticket Promedio</div>
                                <div class="kpi-valor text-warning" id="kpi-ticket">—</div>
                                <div class="kpi-sub" id="kpi-ticket-sub">Año completo</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- GRÁFICO PRINCIPAL CON NAVEGACIÓN -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <button class="btn btn-sm btn-outline-secondary btn-nav-grafico" id="btnGraficoPrev" title="Gráfico anterior">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <span class="fw-semibold" id="lblTituloGrafico">
                            <i class="fas fa-bar-chart me-2"></i>Ingresos por Mes
                        </span>
                        <button class="btn btn-sm btn-outline-secondary btn-nav-grafico" id="btnGraficoNext" title="Gráfico siguiente">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                    <div class="card-body">
                        <!-- Gráfico 1: Barras Comparativas -->
                        <div id="containerGraficoBarras" class="grafico-container">
                            <canvas id="chartVentasMensuales"></canvas>
                        </div>
                        
                        <!-- Gráfico 2: Líneas por Familia -->
                        <div id="containerGraficoFamilias" class="grafico-container d-none">
                            <div class="row" id="rowGraficosFamilia">
                                <div class="col-12" id="colGraficoActual">
                                    <h6 class="text-center text-muted mb-3" id="lblTituloFamiliaActual"></h6>
                                    <canvas id="chartFamiliasActual"></canvas>
                                </div>
                                <div class="col-12 d-none" id="colGraficoComparar">
                                    <h6 class="text-center text-muted mb-3" id="lblTituloFamiliaComparar"></h6>
                                    <canvas id="chartFamiliasComparar"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- FILA: Top Clientes + Por Familia -->
                <div class="row g-4">

                    <!-- TOP CLIENTES -->
                    <div class="col-12 col-xl-6">
                        <div class="card shadow-sm h-100">
                            <div class="card-header fw-semibold">
                                <i class="fas fa-users me-2"></i>Top Clientes
                            </div>
                            <div class="card-body p-0">
                                <table id="tblTopClientes" class="table display nowrap mb-0" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th style="width:36px"></th>
                                            <th>Cliente</th>
                                            <th class="text-end pe-3" style="width:50px">#</th>
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
                            <div class="card-header fw-semibold d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-layer-group me-2"></i>Ventas por Familia de Artículo</span>
                                <button id="btnDesgloseFamilia" class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-table me-1"></i>Desglose
                                </button>
                            </div>

                            <!-- Vista: Gráfico Donut (por defecto) -->
                            <div id="containerGraficoDonut" class="card-body">
                                <div class="row g-0 align-items-center">
                                    <div class="col-7">
                                        <canvas id="chartFamiliaDonut" style="max-height:280px"></canvas>
                                    </div>
                                    <div class="col-5 ps-3">
                                        <div id="leyendaFamiliaDonut" class="small"></div>
                                    </div>
                                </div>
                                <!-- Panel de detalle al pulsar un segmento -->
                                <div id="detalleFamiliaDonut" class="mt-3 d-none">
                                    <hr class="my-2">
                                    <div id="detalleFamiliaContenido"></div>
                                </div>
                            </div>

                            <!-- Vista: DataTable Desglose (oculta por defecto) -->
                            <div id="containerTableFamilia" class="card-body p-0 d-none">
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
