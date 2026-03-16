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
                        <h4 class="mb-0 d-flex align-items-center gap-2">
                            <i class="fas fa-chart-line me-2 text-primary"></i>Informe de Ventas por Período
                            <button type="button" class="btn btn-link p-0" data-bs-toggle="modal" data-bs-target="#modalAyudaInformeVentas" title="Ayuda sobre este informe">
                                <i class="bi bi-question-circle text-primary" style="font-size:1.3rem;"></i>
                            </button>
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

    <!-- ── Modal de Ayuda – Informe de Ventas ──────────────────────────────── -->
    <div class="modal fade" id="modalAyudaInformeVentas" tabindex="-1" aria-labelledby="modalAyudaInformeVentasLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable" style="max-width:75%;width:75%;">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title d-flex align-items-center" id="modalAyudaInformeVentasLabel">
                        <i class="bi bi-question-circle-fill me-2 fs-4"></i>
                        Ayuda – Informe de Ventas por Período
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">

                    <!-- ¿Qué es este informe? -->
                    <div class="mb-4">
                        <h6 class="text-primary d-flex align-items-center">
                            <i class="bi bi-info-circle me-2"></i>¿Qué es este informe?
                        </h6>
                        <p class="text-muted">
                            El <strong>Informe de Ventas por Período</strong> muestra el rendimiento comercial basándose en los presupuestos <strong>aceptados y facturados</strong>.
                            Permite analizar ingresos totales, tendencias mensuales, clientes más relevantes y distribución de ventas por familia de artículo.
                            Todos los datos se actualizan al pulsar <strong>Filtrar</strong> o el botón <strong>Actualizar</strong>.
                        </p>
                    </div>

                    <!-- Filtros -->
                    <div class="mb-4">
                        <h6 class="text-primary d-flex align-items-center">
                            <i class="bi bi-funnel me-2"></i>Filtros de Período
                        </h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead class="table-light">
                                    <tr><th style="width:22%">Campo</th><th>Descripción</th></tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><strong>Año Actual</strong></td>
                                        <td>Año base sobre el que se analizan todos los datos. Se carga automáticamente con el año en curso y muestra únicamente los años con datos registrados.</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Mes Actual</strong></td>
                                        <td>Filtra los datos al mes seleccionado dentro del año base. Si se elige <em>Todos</em>, se muestran los 12 meses acumulados del año.</td>
                                    </tr>
                                    <tr class="table-info">
                                        <td><strong>Año a Comparar</strong></td>
                                        <td>
                                            <strong>Activa el modo comparativo.</strong> Al seleccionar un año diferente a <em>"No comparar"</em>, toda la página cambia de comportamiento:
                                            <ul class="mt-2 mb-1">
                                                <li>Los <strong>KPIs</strong> muestran los valores de ambos años con una flecha indicando si ha subido <span class="text-success fw-bold">↑</span> o bajado <span class="text-danger fw-bold">↓</span>.</li>
                                                <li>El <strong>gráfico de barras</strong> muestra dos series en paralelo: <span class="badge bg-primary">año base</span> y <span class="badge bg-secondary">año comparado</span>.</li>
                                                <li>El <strong>gráfico de líneas por familia</strong> se divide en dos columnas, una por año, con los mismos colores para facilitar la comparación.</li>
                                                <li>La tabla <strong>Top Clientes</strong> acumula los ingresos de ambos años.</li>
                                            </ul>
                                            <div class="alert alert-info mb-0 mt-2 small">
                                                <i class="bi bi-exclamation-circle me-1"></i>
                                                El <strong>gráfico circular de familias</strong> refleja siempre únicamente el año base, independientemente de la comparativa.
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- KPIs -->
                    <div class="mb-4">
                        <h6 class="text-primary d-flex align-items-center">
                            <i class="bi bi-bar-chart-line me-2"></i>Tarjetas KPI
                        </h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead class="table-light">
                                    <tr><th style="width:22%">KPI</th><th>Sin año comparar</th><th>Con año a comparar</th></tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><strong>Total Facturado</strong> <span class="badge bg-secondary">Gris</span></td>
                                        <td>Suma total del año (o del mes si hay filtro de mes aplicado).</td>
                                        <td>Valor del año base + el del año comparado debajo con flecha de variación.</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Ingresos del Mes</strong> <span class="badge bg-success">Verde</span></td>
                                        <td>Total del mes seleccionado. Si no hay mes, muestra el último mes con datos.</td>
                                        <td>Comparativa del mismo mes en ambos años con diferencia.</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Presupuestos</strong> <span class="badge bg-primary">Azul</span></td>
                                        <td>Número de presupuestos aceptados/facturados en el período.</td>
                                        <td>Recuento de ambos años con diferencia numérica.</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Ticket Promedio</strong> <span class="badge bg-warning text-dark">Naranja</span></td>
                                        <td>Importe medio por presupuesto (Total ÷ Nº presupuestos).</td>
                                        <td>Ticket medio de cada año con indicador de variación porcentual.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Gráfico principal -->
                    <div class="mb-4">
                        <h6 class="text-primary d-flex align-items-center">
                            <i class="bi bi-graph-up me-2"></i>Gráfico Principal (navegar con ‹ ›)
                        </h6>
                        <p class="text-muted small">Usa las flechas del encabezado de la tarjeta para cambiar entre los dos gráficos disponibles:</p>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead class="table-light">
                                    <tr><th style="width:28%">Gráfico</th><th>Sin año comparar</th><th>Con año a comparar</th></tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><strong><i class="fas fa-chart-bar me-1"></i>Ingresos por Mes</strong><br><span class="text-muted small">(Gráfico 1)</span></td>
                                        <td>Barras de un color mostrando los ingresos mes a mes del año seleccionado.</td>
                                        <td>Dos barras por mes en paralelo: <span class="badge bg-primary">año base</span> y <span class="badge bg-secondary">año comparado</span>.</td>
                                    </tr>
                                    <tr>
                                        <td><strong><i class="fas fa-layer-group me-1"></i>Ingresos por Familia</strong><br><span class="text-muted small">(Gráfico 2)</span></td>
                                        <td>Un gráfico de líneas con una serie por cada familia, mostrando su evolución mensual.</td>
                                        <td>Dos gráficos de líneas en columnas separadas (uno por año), con los mismos colores por familia para comparar fácilmente.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Top Clientes -->
                    <div class="mb-4">
                        <h6 class="text-primary d-flex align-items-center">
                            <i class="bi bi-people me-2"></i>Tabla Top Clientes
                        </h6>
                        <p class="text-muted small">Lista de clientes ordenados por importe total facturado en el período.</p>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead class="table-light">
                                    <tr><th style="width:25%">Elemento</th><th>Descripción</th></tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><strong>Medalla</strong></td>
                                        <td>Los tres primeros clientes llevan medalla: <span class="badge" style="background:#FFD700;color:#000">🥇 Oro</span> <span class="badge" style="background:#C0C0C0;color:#000">🥈 Plata</span> <span class="badge" style="background:#CD7F32">🥉 Bronce</span></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Botón <i class="bi bi-plus-circle"></i></strong></td>
                                        <td>Expande una fila de detalle con el desglose de presupuestos, unidades y total facturado del cliente.</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Columna #</strong></td>
                                        <td>Importe total facturado por ese cliente. La tabla se ordena por este campo de mayor a menor.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="alert alert-warning small mt-2 mb-0">
                            <i class="bi bi-info-circle me-1"></i>
                            Con <strong>año a comparar</strong> activo, la columna <strong>#</strong> muestra la suma de ingresos de ambos años para ese cliente.
                        </div>
                    </div>

                    <!-- Gráfico Donut Familias -->
                    <div class="mb-2">
                        <h6 class="text-primary d-flex align-items-center">
                            <i class="bi bi-pie-chart me-2"></i>Ventas por Familia (Gráfico Circular)
                        </h6>
                        <p class="text-muted small">Distribución porcentual de los ingresos entre las familias de artículos del año base.</p>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead class="table-light">
                                    <tr><th style="width:28%">Elemento</th><th>Descripción</th></tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><strong>Segmentos</strong></td>
                                        <td>Cada segmento representa una familia. El tamaño es proporcional a su peso porcentual sobre el total facturado.</td>
                                    </tr>
                                    <tr class="table-info">
                                        <td><strong>Hover (pasar el ratón)</strong></td>
                                        <td>Muestra un tooltip con el <strong>importe total en €</strong> y el <strong>porcentaje</strong> de esa familia.</td>
                                    </tr>
                                    <tr class="table-success">
                                        <td><strong>Click en un segmento</strong></td>
                                        <td>Despliega un panel bajo el gráfico con: total facturado, número de presupuestos, unidades y % del total. <strong>Al cargar, el panel muestra automáticamente la familia con mayor volumen.</strong></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Leyenda lateral</strong></td>
                                        <td>A la derecha del gráfico se listan todas las familias con su color y porcentaje de referencia.</td>
                                    </tr>
                                    <tr class="table-warning">
                                        <td><strong>Botón "Desglose"</strong></td>
                                        <td>Oculta el gráfico y muestra la tabla detallada con datos numéricos por familia. El botón cambia a <strong>"Gráfico"</strong> para volver a la vista circular.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div><!-- modal-body -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    <!-- ── /Modal de Ayuda ────────────────────────────────────────────────────── -->

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
