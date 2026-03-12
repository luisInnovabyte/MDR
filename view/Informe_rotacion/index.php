<!-- ============================================================ -->
<!-- Informe de Rotación de Inventario - MDR ERP Manager       -->
<!-- Identifica equipos con alta/baja rotación                 -->
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
        .kpi-card.kpi-total   { border-left-color: #6c757d; }
        .kpi-card.kpi-activos { border-left-color: #198754; }
        .kpi-card.kpi-pct     { border-left-color: #0d6efd; }
        .kpi-card.kpi-inactivos { border-left-color: #dc3545; }

        .kpi-valor { font-size: 1.6rem; font-weight: 700; line-height: 1.2; }
        .kpi-label { font-size: 0.78rem; text-transform: uppercase; letter-spacing: .05em; color: #6c757d; }
        .kpi-sub   { font-size: 0.82rem; color: #6c757d; }

        /* ---- Filtros ---- */
        #barraFiltros { background: #f8f9fa; border-radius: 8px; padding: 12px 16px; }

        /* ---- Leyenda semáforo ---- */
        .leyenda-item { display: flex; align-items: center; gap: 6px; font-size: 0.82rem; }

        /* ---- Gráfico ---- */
        #chartTopArticulos { max-height: 320px; }

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
                <span class="breadcrumb-item active">Rotación de Inventario</span>
            </nav>
        </div>

        <div class="br-pagebody">
            <div class="br-section-wrapper">

                <!-- CABECERA -->
                <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                    <div>
                        <h4 class="mb-0">
                            <i class="fas fa-boxes me-2 text-primary"></i>Rotación de Inventario
                        </h4>
                        <p class="text-muted mb-0 small">
                            Análisis de uso de artículos — identifica equipos activos, moderados e inactivos
                        </p>
                    </div>
                    <button class="btn btn-sm btn-outline-secondary" onclick="recargarTodo()">
                        <i class="fas fa-sync-alt me-1"></i>Actualizar
                    </button>
                </div>

                <!-- BARRA DE FILTROS -->
                <div id="barraFiltros" class="mb-4 d-flex flex-wrap align-items-end gap-3">
                    <div>
                        <label class="form-label mb-1 small">Período</label>
                        <select class="form-select form-select-sm" id="filtroPeriodo" style="min-width:170px;">
                            <option value="90"  selected>Últimos 90 días</option>
                            <option value="180">Últimos 180 días</option>
                            <option value="365">Último año</option>
                            <option value="0">Histórico completo</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label mb-1 small">Familia</label>
                        <select class="form-select form-select-sm" id="filtroFamilia" style="min-width:160px;">
                            <option value="">Todas las familias</option>
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
                    <!-- Leyenda semáforo -->
                    <div class="ms-auto d-flex gap-3 flex-wrap">
                        <div class="leyenda-item">
                            <span class="badge bg-success">Activo</span>
                            <span>usado ≤ 30 días</span>
                        </div>
                        <div class="leyenda-item">
                            <span class="badge bg-warning text-dark">Moderado</span>
                            <span>≤ 90 días</span>
                        </div>
                        <div class="leyenda-item">
                            <span class="badge bg-danger">Inactivo</span>
                            <span>&gt; 90 días</span>
                        </div>
                        <div class="leyenda-item">
                            <span class="badge bg-secondary">Nunca usado</span>
                        </div>
                    </div>
                </div>

                <!-- KPI CARDS -->
                <div class="row row-cols-2 row-cols-md-4 g-3 mb-4">
                    <div class="col">
                        <div class="card kpi-card kpi-total h-100 shadow-sm">
                            <div class="card-body py-3">
                                <div class="kpi-label">Artículos Activos</div>
                                <div class="kpi-valor text-secondary" id="kpi-total">—</div>
                                <div class="kpi-sub">en catálogo</div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card kpi-card kpi-activos h-100 shadow-sm">
                            <div class="card-body py-3">
                                <div class="kpi-label">Usados en período</div>
                                <div class="kpi-valor text-success" id="kpi-usados">—</div>
                                <div class="kpi-sub" id="kpi-periodo-sub">en los últimos 90 días</div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card kpi-card kpi-pct h-100 shadow-sm">
                            <div class="card-body py-3">
                                <div class="kpi-label">% Uso</div>
                                <div class="kpi-valor text-primary" id="kpi-pct">—</div>
                                <div class="kpi-sub">del total del catálogo</div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card kpi-card kpi-inactivos h-100 shadow-sm">
                            <div class="card-body py-3">
                                <div class="kpi-label">Sin uso en período</div>
                                <div class="kpi-valor text-danger" id="kpi-sin-uso">—</div>
                                <div class="kpi-sub">posibles candidatos a revisar</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- GRÁFICO TOP 10 -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header fw-semibold">
                        <i class="fas fa-trophy me-2"></i>Top 10 Artículos más Alquilados
                    </div>
                    <div class="card-body">
                        <canvas id="chartTopArticulos"></canvas>
                    </div>
                </div>

                <!-- TABLA COMPLETA DE ROTACIÓN -->
                <div class="card shadow-sm">
                    <div class="card-header fw-semibold">
                        <i class="fas fa-table me-2"></i>Detalle de Rotación por Artículo
                    </div>
                    <div class="card-body p-0">
                        <table id="tblRotacion" class="table display responsive nowrap" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Artículo</th>
                                    <th>Familia</th>
                                    <th class="text-center">Nº Usos</th>
                                    <th class="text-center">Uds. totales</th>
                                    <th>Último uso</th>
                                    <th class="text-center">Días sin uso</th>
                                    <th class="text-center">Estado</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div><!-- card tabla rotación -->

            </div><!-- br-section-wrapper -->
        </div><!-- br-pagebody -->

        <footer class="br-footer">
            <?php include_once('../../config/template/mainFooter.php') ?>
        </footer>
    </div><!-- br-mainpanel -->

    <?php include_once('../../config/template/mainJs.php') ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script src="js/informerotacion.js"></script>

    <script>
        $(document).ready(function () {
            $('body').addClass('collapsed-menu');
            $('.br-sideleft').addClass('collapsed');
        });
    </script>
</body>
</html>
                                    <th class="text-center">Estado</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>

            </div><!-- br-section-wrapper -->
        </div><!-- br-pagebody -->

        <footer class="br-footer">
            <?php include_once('../../config/template/mainFooter.php') ?>
        </footer>
    </div><!-- br-mainpanel -->

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <script src="js/informerotacion.js"></script>
</body>
</html>
