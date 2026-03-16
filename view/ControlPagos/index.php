<!-- ============================================================ -->
<!-- Control de Pagos - MDR ERP Manager                         -->
<!-- Vista global del estado de pagos de presupuestos aprobados -->
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
        .kpi-card {
            border-left: 4px solid;
            border-radius: 6px;
        }
        .kpi-card.kpi-total     { border-left-color: #6c757d; }
        .kpi-card.kpi-cobrado   { border-left-color: #198754; }
        .kpi-card.kpi-pendiente { border-left-color: #dc3545; }
        .kpi-card.kpi-porcentaje{ border-left-color: #0d6efd; }
        .kpi-card.kpi-conciliado{ border-left-color: #0dcaf0; }

        .kpi-valor {
            font-size: 1.6rem;
            font-weight: 700;
            line-height: 1.2;
        }
        .kpi-label {
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: .05em;
            color: #6c757d;
        }
        .kpi-sub {
            font-size: 0.82rem;
            color: #6c757d;
        }

        /* ---- DataTable ---- */
        .dataTables_wrapper { overflow-x: auto; }
        div.dt-container { width: 100% !important; }

        #tblControlPagos th, #tblControlPagos td { vertical-align: middle; }

        /* progreso mini */
        .progress { min-width: 60px; }

        /* filtros barra */
        #barraFiltros { background: #f8f9fa; border-radius: 8px; padding: 12px 16px; }
    </style>
</head>

<body>

    <!-- ########## LEFT PANEL ########## -->
    <?php require_once('../../config/template/mainLogo.php') ?>

    <div class="br-sideleft sideleft-scrollbar">
        <?php require_once('../../config/template/mainSidebar.php') ?>
        <?php require_once('../../config/template/mainSidebarDown.php') ?>
        <br>
    </div>

    <!-- ########## HEAD PANEL ########## -->
    <div class="br-header">
        <?php include_once('../../config/template/mainHeader.php') ?>
    </div>

    <!-- ########## RIGHT PANEL ########## -->
    <div class="br-sideright">
        <?php include_once('../../config/template/mainRightPanel.php') ?>
    </div>

    <!-- ########## MAIN PANEL ########## -->
    <div class="br-mainpanel">
        <div class="br-pageheader pd-y-15 pd-l-20">
            <nav class="breadcrumb pd-0 mg-0 tx-12">
                <a class="breadcrumb-item" href="../Home/index.php">Inicio</a>
                <span class="breadcrumb-item active">Control de Pagos</span>
            </nav>
        </div>

        <div class="br-pagebody">
            <div class="br-section-wrapper">

                <!-- ========================================== -->
                <!-- CABECERA + TÍTULO -->
                <!-- ========================================== -->
                <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                    <div>
                        <div class="d-flex align-items-center gap-2">
                            <h4 class="mb-0">
                                <i class="fas fa-money-check-alt me-2 text-primary"></i>Control de Pagos
                            </h4>
                            <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal"
                                    data-bs-target="#modalAyudaControlPagos" title="Ayuda">
                                <i class="fas fa-question-circle"></i>
                            </button>
                        </div>
                        <p class="text-muted mb-0 small">Estado financiero de presupuestos aprobados</p>
                    </div>
                    <button class="btn btn-sm btn-outline-secondary" onclick="recargarTabla()">
                        <i class="fas fa-sync-alt me-1"></i>Actualizar
                    </button>
                </div>

                <!-- ========================================== -->
                <!-- KPI CARDS -->
                <!-- ========================================== -->
                <div class="row row-cols-2 row-cols-md-3 row-cols-xl-5 g-3 mb-4" id="seccionKPIs">
                    <div class="col">
                        <div class="card kpi-card kpi-total h-100 shadow-sm">
                            <div class="card-body py-3">
                                <div class="kpi-label">Total Aprobado</div>
                                <div class="kpi-valor text-secondary" id="kpi-total">—</div>
                                <div class="kpi-sub" id="kpi-total-sub">— presupuestos</div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card kpi-card kpi-cobrado h-100 shadow-sm">
                            <div class="card-body py-3">
                                <div class="kpi-label">Total Facturado</div>
                                <div class="kpi-valor text-success" id="kpi-cobrado">—</div>
                                <div class="kpi-sub">Pagos no anulados</div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card kpi-card kpi-conciliado h-100 shadow-sm">
                            <div class="card-body py-3">
                                <div class="kpi-label">Total Pagado</div>
                                <div class="kpi-valor text-info" id="kpi-conciliado">—</div>
                                <div class="kpi-sub">Pagos conciliados</div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card kpi-card kpi-pendiente h-100 shadow-sm">
                            <div class="card-body py-3">
                                <div class="kpi-label">Total Pdte. Facturar</div>
                                <div class="kpi-valor text-danger" id="kpi-pendiente">—</div>
                                <div class="kpi-sub" id="kpi-pendiente-sub">Aprobado − Facturado</div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card kpi-card kpi-porcentaje h-100 shadow-sm">
                            <div class="card-body py-3">
                                <div class="kpi-label">% Global Cobrado</div>
                                <div class="kpi-valor text-primary" id="kpi-porcentaje">—</div>
                                <div class="kpi-sub">conciliado / facturado</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ========================================== -->
                <!-- BARRA DE FILTROS -->
                <!-- ========================================== -->
                <div id="barraFiltros" class="mb-3 d-flex flex-wrap align-items-end gap-3">
                    <div class="form-check form-switch align-self-center mb-0">
                        <input class="form-check-input" type="checkbox" id="chkSoloPdteFacturar">
                        <label class="form-check-label" for="chkSoloPdteFacturar">Solo pdtes. de Facturar</label>
                    </div>
                    <div class="form-check form-switch align-self-center mb-0">
                        <input class="form-check-input" type="checkbox" id="chkSoloPdteCobrar">
                        <label class="form-check-label" for="chkSoloPdteCobrar">Solo pdtes. de Cobrar</label>
                    </div>
                    <div>
                        <label class="form-label mb-1 small">Evento desde</label>
                        <input type="date" class="form-control form-control-sm" id="filtroFechaDesde" style="max-width:150px;">
                    </div>
                    <div>
                        <label class="form-label mb-1 small">Evento hasta</label>
                        <input type="date" class="form-control form-control-sm" id="filtroFechaHasta" style="max-width:150px;">
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

                <!-- ========================================== -->
                <!-- TABLA PRINCIPAL -->
                <!-- ========================================== -->
                <div class="table-wrapper">
                    <table id="tblControlPagos" class="table display responsive nowrap">
                        <thead>
                            <tr>
                                <th>Nº Presupuesto</th>
                                <th>Cliente</th>
                                <th>Evento</th>
                                <th class="text-end">Aprobado</th>
                                <th class="text-end">Facturado</th>
                                <th class="text-end">Pagado</th>
                                <th class="text-end">Pdte. Facturar</th>
                                <th>Documentos</th>
                                <th>Última Factura</th>
                                <th class="text-center" style="width:80px;">Opciones</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>

            </div><!-- br-section-wrapper -->
        </div><!-- br-pagebody -->

        <footer class="br-footer">
            <?php include_once('../../config/template/mainFooter.php') ?>
        </footer>
    </div><!-- br-mainpanel -->

    <!-- ========================================================= -->
    <!-- MODAL DETALLE DE PAGOS DE UN PRESUPUESTO                  -->
    <!-- ========================================================= -->
    <div class="modal fade" id="modalDetallePagos" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered" style="max-width: 60vw;">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-list-ul me-2"></i>
                        Desglose de pagos — <span id="modalDetalleTitulo"></span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Resumen financiero del presupuesto seleccionado -->
                    <div id="detalleResumen" class="row g-2 mb-3">
                        <div class="col-4">
                            <div class="border rounded p-2 text-center">
                                <div class="small text-muted">Total presupuesto</div>
                                <div class="fw-bold" id="detalleTotalPptoVal">—</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="border rounded p-2 text-center">
                                <div class="small text-muted">Total facturado</div>
                                <div class="fw-bold text-success" id="detalleTotalPagadoVal">—</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="border rounded p-2 text-center">
                                <div class="small text-muted">Pdte. facturar</div>
                                <div class="fw-bold text-danger" id="detalleSaldoPendienteVal">—</div>
                            </div>
                        </div>
                    </div>

                    <div id="detalleCargando" class="text-center py-4">
                        <div class="spinner-border text-primary"></div>
                        <p class="mt-2 text-muted">Cargando pagos...</p>
                    </div>

                    <div id="detalleContenido" style="display:none;">
                        <table id="tblDetallePagos"
                               class="table table-sm table-bordered w-100">
                            <thead class="table-secondary">
                                <tr>
                                    <th>Fecha</th>
                                    <th>Tipo</th>
                                    <th class="text-end">Importe</th>
                                    <th>Método</th>
                                    <th>Documento</th>
                                    <th>Estado</th>
                                    <th>Referencia</th>
                                </tr>
                            </thead>
                            <tbody id="tblDetallePagosTbody"></tbody>
                        </table>
                    </div>

                    <div id="detalleSinPagos" class="text-center py-3 text-muted" style="display:none;">
                        <i class="fas fa-inbox fa-2x mb-2"></i>
                        <p>No hay pagos registrados para este presupuesto.</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <a id="detalleLinkPresupuesto" href="#" class="btn btn-outline-primary btn-sm" target="_self">
                        <i class="fas fa-external-link-alt me-1"></i>Ir al presupuesto
                    </a>
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    <!-- FIN MODAL DETALLE -->

    <!-- Modal Ayuda -->
    <?php include_once('ayudaControlPagos.php') ?>

    <!-- MainJs.php (jQuery, Bootstrap, DataTables, SweetAlert2...) -->
    <?php include_once('../../config/template/mainJs.php') ?>
    <script src="controlpagos.js"></script>

    <script>
        // Colapsar sidebar al cargar
        $(document).ready(function () {
            $('body').addClass('collapsed-menu');
            $('.br-sideleft').addClass('collapsed');
        });
    </script>

</body>
</html>
