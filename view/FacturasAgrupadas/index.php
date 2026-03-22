<!-- ============================================================ -->
<!-- Facturas Agrupadas - MDR ERP Manager                       -->
<!-- Agrupa N presupuestos de un mismo cliente en una factura   -->
<!-- ============================================================ -->
<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <?php include_once('../../config/template/mainHead.php') ?>
    <style>
        /* ── Color naranja MDR Agrupada ─────────────────────── */
        :root {
            --fa-naranja: #d35400;
            --fa-naranja-light: #fdebd0;
            --fa-naranja-mid: #e59866;
        }

        /* ── KPI Cards ──────────────────────────────────────── */
        .kpi-card-fa { border-left: 4px solid var(--fa-naranja); border-radius: 6px; }
        .kpi-valor-fa { font-size: 1.5rem; font-weight: 700; color: var(--fa-naranja); line-height: 1.2; }
        .kpi-label-fa { font-size: .78rem; text-transform: uppercase; letter-spacing: .05em; color: #6c757d; }

        /* ── Tabla ───────────────────────────────────────────── */
        #tblFacturasAgrupadas th, #tblFacturasAgrupadas td { vertical-align: middle; }
        .dataTables_wrapper { overflow-x: auto; }

        /* ── Badge naranja ────────────────────────────────────── */
        .badge-agrupada { background-color: var(--fa-naranja); color: #fff; }
        .badge-abono    { background-color: #c0392b; color: #fff; }

        /* ── Paso wizard ─────────────────────────────────────── */
        .wizard-step-indicator { display: flex; gap: 8px; margin-bottom: 20px; }
        .wizard-step {
            flex: 1; padding: 8px 6px; border-radius: 6px;
            border: 2px solid #dee2e6; text-align: center;
            font-size: .8rem; font-weight: 600; color: #adb5bd;
            transition: all .2s;
        }
        .wizard-step.active  { border-color: var(--fa-naranja); color: var(--fa-naranja); background: var(--fa-naranja-light); }
        .wizard-step.done    { border-color: #27ae60; color: #27ae60; background: #eafaf1; }

        /* ── Lista de presupuestos seleccionables ─────────────── */
        .ppto-item {
            border: 1px solid #dee2e6; border-radius: 6px;
            padding: 10px 14px; margin-bottom: 8px;
            cursor: pointer; transition: all .15s;
            display: flex; align-items: center; gap: 10px;
        }
        .ppto-item:hover { border-color: var(--fa-naranja-mid); background: var(--fa-naranja-light); }
        .ppto-item.selected { border-color: var(--fa-naranja); background: var(--fa-naranja-light); }
        .ppto-item .ppto-num { font-weight: 700; font-size: .9rem; color: var(--fa-naranja); min-width: 90px; }
        .ppto-item .ppto-evento { font-size: .82rem; color: #555; flex: 1; }
        .ppto-item .ppto-total  { font-size: .9rem; font-weight: 600; color: #343a40; white-space: nowrap; }
        .ppto-item input[type=checkbox] { accent-color: var(--fa-naranja); width: 18px; height: 18px; }

        /* ── Confirmación ──────────────────────────────────────── */
        .resumen-linea { display: flex; justify-content: space-between; padding: 4px 0; border-bottom: 1px dashed #dee2e6; }
        .resumen-linea:last-child { border-bottom: none; }
        .resumen-total { font-weight: 700; font-size: 1.05rem; color: var(--fa-naranja); }

        /* ── Alerta errores validación ──────────────────────────- */
        #alertaValidacion { display: none; }
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
                <span class="breadcrumb-item active">Facturas Agrupadas</span>
            </nav>
        </div>

        <div class="br-pagebody">
            <div class="br-section-wrapper">

                <!-- ========================================== -->
                <!-- CABECERA -->
                <!-- ========================================== -->
                <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                    <div>
                        <h4 class="mb-0 d-flex align-items-center gap-2">
                            <i class="fas fa-file-invoice me-1" style="color: var(--fa-naranja);"></i>
                            Facturas Agrupadas
                            <button type="button" class="btn btn-link p-0 ms-1"
                                    data-bs-toggle="modal" data-bs-target="#modalAyudaFacturasAgrupadas"
                                    title="Ayuda sobre las facturas agrupadas">
                                <i class="bi bi-question-circle text-primary" style="font-size: 1.3rem;"></i>
                            </button>
                        </h4>
                        <p class="text-muted mb-0 small">Agrupa varios presupuestos del mismo cliente en una sola factura</p>
                    </div>
                    <button class="btn btn-sm" style="background:var(--fa-naranja); color:#fff;" onclick="abrirWizard()">
                        <i class="fas fa-plus me-1"></i>Nueva Factura Agrupada
                    </button>
                </div>

                <!-- ========================================== -->
                <!-- KPI CARDS -->
                <!-- ========================================== -->
                <div class="row g-3 mb-4" id="seccionKPIs">
                    <div class="col-6 col-md-3">
                        <div class="card kpi-card-fa h-100 shadow-sm">
                            <div class="card-body py-3">
                                <div class="kpi-label-fa">Total Facturas</div>
                                <div class="kpi-valor-fa" id="kpi-total">—</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card kpi-card-fa h-100 shadow-sm">
                            <div class="card-body py-3">
                                <div class="kpi-label-fa">Importe Total</div>
                                <div class="kpi-valor-fa text-dark" id="kpi-importe">—</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card kpi-card-fa h-100 shadow-sm">
                            <div class="card-body py-3">
                                <div class="kpi-label-fa">Presupuestos Agrupados</div>
                                <div class="kpi-valor-fa text-dark" id="kpi-presupuestos">—</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card kpi-card-fa h-100 shadow-sm">
                            <div class="card-body py-3">
                                <div class="kpi-label-fa">Facturas Rectificativas</div>
                                <div class="kpi-valor-fa" id="kpi-abonos">—</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ========================================== -->
                <!-- TABLA PRINCIPAL -->
                <!-- ========================================== -->
                <div class="table-responsive">
                    <table id="tblFacturasAgrupadas"
                           class="table table-striped table-bordered dt-responsive nowrap"
                           style="width:100%">
                        <thead>
                            <tr>
                                <th>Número</th>
                                <th>Fecha</th>
                                <th>Cliente</th>
                                <th>Empresa</th>
                                <th>Pres.</th>
                                <th>Total Bruto</th>
                                <th>A Cobrar</th>
                                <th>Tipo</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>

            </div><!-- /br-section-wrapper -->
        </div><!-- /br-pagebody -->
    </div><!-- /br-mainpanel -->


    <!-- ====================================================== -->
    <!-- MODAL WIZARD: Nueva Factura Agrupada                   -->
    <!-- ====================================================== -->
    <div class="modal fade" id="modalWizard" tabindex="-1" aria-labelledby="labelWizard" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">

                <div class="modal-header" style="background: var(--fa-naranja);">
                    <h5 class="modal-title text-white" id="labelWizard">
                        <i class="fas fa-file-invoice me-2"></i>Nueva Factura Agrupada
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <!-- Indicador de pasos -->
                    <div class="wizard-step-indicator">
                        <div class="wizard-step active" id="step-ind-1">1. Cliente</div>
                        <div class="wizard-step" id="step-ind-2">2. Presupuestos</div>
                        <div class="wizard-step" id="step-ind-3">3. Empresa</div>
                        <div class="wizard-step" id="step-ind-4">4. Confirmar</div>
                    </div>

                    <!-- ── PASO 1: Selección de cliente ─────── -->
                    <div id="step-1">
                        <h6 class="mb-3 text-muted"><i class="fas fa-user me-1"></i>Selecciona el cliente</h6>
                        <div class="mb-3">
                            <label class="form-label">Cliente *</label>
                            <select class="form-select" id="sel-cliente">
                                <option value="">— Selecciona un cliente —</option>
                            </select>
                        </div>
                        <div id="info-ppto-cliente" class="text-muted small" style="display:none;">
                            <i class="fas fa-info-circle me-1"></i>
                            <span id="txt-pptos-disponibles"></span>
                        </div>
                    </div>

                    <!-- ── PASO 2: Selección de presupuestos ───────────── -->
                    <div id="step-2" style="display:none;">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h6 class="mb-0 text-muted"><i class="fas fa-list-check me-1"></i>Selecciona los presupuestos</h6>
                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-outline-secondary" onclick="seleccionarTodos()">
                                    <i class="fas fa-check-double me-1"></i>Todos
                                </button>
                                <button class="btn btn-sm btn-outline-secondary" onclick="deseleccionarTodos()">
                                    <i class="fas fa-times me-1"></i>Ninguno
                                </button>
                            </div>
                        </div>
                        <div id="lista-presupuestos">
                            <!-- Se llena via AJAX -->
                        </div>
                        <div id="alertaValidacion" class="alert alert-danger mt-2" style="display:none;">
                            <ul id="listaErroresValidacion" class="mb-0"></ul>
                        </div>
                    </div>

                    <!-- ── PASO 3: Empresa emisora ──────────────────────── -->
                    <div id="step-3" style="display:none;">
                        <h6 class="mb-3 text-muted"><i class="fas fa-building me-1"></i>Empresa emisora de la factura</h6>

                        <!-- Alerta empresa bloqueada -->
                        <div id="alertaEmpresaBloqueada" class="alert alert-info d-none">
                            <i class="fas fa-lock me-2"></i>
                            <strong>Empresa asignada automáticamente:</strong>
                            Uno o más presupuestos seleccionados ya tienen facturas emitidas con la empresa
                            <strong id="txtEmpresaBloqueadaNombre"></strong>.
                            Se usará la misma para mantener la coherencia.
                        </div>

                        <!-- Alerta sin empresas -->
                        <div id="alertaSinEmpresas" class="alert alert-warning d-none">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            No hay empresas reales activas configuradas. Contacta con el administrador.
                        </div>

                        <div class="mb-3" id="divSelEmpresa">
                            <label class="form-label">Empresa emisora *</label>
                            <select class="form-select" id="sel-empresa">
                                <option value="">&#8212; Selecciona una empresa &#8212;</option>
                            </select>
                            <div class="form-text text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Esta empresa aparecerá en la cabecera de la factura.
                            </div>
                        </div>
                    </div>

                    <!-- ── PASO 4: Confirmar y datos adicionales -->
                    <div id="step-4" style="display:none;">
                        <h6 class="mb-3 text-muted"><i class="fas fa-check-circle me-1"></i>Confirmar factura agrupada</h6>

                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label class="form-label">Fecha de factura *</label>
                                <input type="date" class="form-control" id="input-fecha-fa" required
                                       value="<?php echo date('Y-m-d'); ?>">
                            </div>
                            <div class="col-md-8">
                                <label class="form-label">Observaciones (opcional)</label>
                                <input type="text" class="form-control" id="input-obs-fa"
                                       placeholder="Cualquier nota visible en la factura...">
                            </div>
                        </div>

                        <!-- Resumen de presupuestos seleccionados -->
                        <div id="resumen-pptos" class="mb-3"></div>

                        <!-- Totales -->
                        <div class="card" style="border-color: var(--fa-naranja);">
                            <div class="card-header" style="background:var(--fa-naranja); color:#fff;">
                                <strong>Totales estimados</strong>
                            </div>
                            <div class="card-body py-2">
                                <div class="resumen-linea"><span>Base imponible</span><span id="res-base">—</span></div>
                                <div class="resumen-linea"><span>Total IVA</span><span id="res-iva">—</span></div>
                                <div class="resumen-linea"><span>Anticipos descontados</span><span id="res-anticipos">—</span></div>
                                <div class="resumen-linea resumen-total"><span>TOTAL A COBRAR</span><span id="res-total">—</span></div>
                            </div>
                        </div>
                    </div>

                </div><!-- /modal-body -->

                <div class="modal-footer">
                    <button class="btn btn-secondary" id="btn-wiz-anterior" onclick="wizardAnterior()" style="display:none;">
                        <i class="fas fa-arrow-left me-1"></i>Anterior
                    </button>
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button class="btn" id="btn-wiz-siguiente" style="background: var(--fa-naranja); color:#fff;" onclick="wizardSiguiente()">
                        Siguiente <i class="fas fa-arrow-right ms-1"></i>
                    </button>
                    <button class="btn btn-success" id="btn-wiz-guardar" style="display:none;" onclick="guardarFacturaAgrupada()">
                        <i class="fas fa-save me-1"></i>Crear Factura
                    </button>
                </div>

            </div>
        </div>
    </div>

    <!-- ====================================================== -->
    <!-- MODAL DETALLE (ver presupuestos de una FA)             -->
    <!-- ====================================================== -->
    <div class="modal fade" id="modalDetalle" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header" style="background: var(--fa-naranja);">
                    <h5 class="modal-title text-white">
                        <i class="fas fa-eye me-2"></i>Presupuestos de la Factura Agrupada
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="cuerpo-detalle">
                    <!-- Se llena via JS -->
                </div>
                <div class="modal-footer">
                    <div id="footer-pdf-detalle" class="me-auto"></div>
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ====================================================== -->
    <!-- MODAL ABONO -->
    <!-- ====================================================== -->
    <div class="modal fade" id="modalAbono" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="fas fa-undo me-2"></i>Generar Rectificativa</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="abono-id-fa">
                    <div class="mb-3">
                        <label class="form-label">Motivo de rectificación *</label>
                        <textarea class="form-control" id="abono-motivo" rows="3"
                                  placeholder="Describe el motivo de la rectificación..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button class="btn btn-danger" onclick="confirmarAbono()">
                        <i class="fas fa-undo me-1"></i>Generar Rectificativa
                    </button>
                </div>
            </div>
        </div>
    </div>


    <!-- ====================================================== -->
    <!-- SCRIPTS -->
    <!-- ====================================================== -->
    <?php include_once('../../config/template/mainJs.php') ?>
    <script src="../../public/js/factura_agrupada.js"></script>

    <!-- MODAL AYUDA -->
    <?php include_once('ayudaFacturasAgrupadas.php') ?>

</body>
</html>
