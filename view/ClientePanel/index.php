<?php
$id_cliente = isset($_GET['id_cliente']) ? (int) $_GET['id_cliente'] : 0;
if ($id_cliente <= 0) {
    header('Location: ../MntClientes/index.php');
    exit;
}
$moduloActual = 'usuarios';
require_once('../../config/template/verificarPermiso.php');
?>
<!DOCTYPE html>
<html lang="es">

<!-- ########## HEAD ########## -->
<head>
    <?php include_once('../../config/template/mainHead.php') ?>
</head>

<body>

    <!-- ########## LEFT PANEL ########## -->
    <?php require_once('../../config/template/mainLogo.php') ?>

    <div class="br-sideleft sideleft-scrollbar">
        <?php require_once('../../config/template/mainSidebar.php') ?>
        <?php require_once('../../config/template/mainSidebarDown.php') ?>
        <br>
    </div><!-- br-sideleft -->

    <!-- ########## HEAD PANEL ########## -->
    <div class="br-header">
        <?php include_once('../../config/template/mainHeader.php') ?>
    </div><!-- br-header -->

    <!-- ########## RIGHT PANEL ########## -->
    <div class="br-sideright">
        <?php include_once('../../config/template/mainRightPanel.php') ?>
    </div><!-- br-sideright -->

    <!-- ########## MAIN PANEL ########## -->
    <div class="br-mainpanel">

        <div class="br-pageheader pd-y-15 pd-l-20">
            <nav class="breadcrumb pd-0 mg-0 tx-12">
                <a class="breadcrumb-item" href="../MntClientes/index.php">Clientes</a>
                <span class="breadcrumb-item active">Panel 360°</span>
            </nav>
        </div><!-- br-pageheader -->

        <div class="br-pagebody">
            <div class="br-section-wrapper">

                <!-- ── CABECERA DEL CLIENTE ──────────────────────────────── -->
                <div class="d-flex align-items-start justify-content-between flex-wrap gap-2 mb-3">
                    <div>
                        <a href="../MntClientes/index.php" class="btn btn-sm btn-outline-secondary me-2">
                            <i class="bi bi-arrow-left me-1"></i>Volver a clientes
                        </a>
                        <span id="lblNombreCliente" class="fs-5 fw-bold"></span>
                        <span id="lblCodigoCliente" class="text-muted ms-2 small"></span>
                        <span id="badgeEstadoCliente" class="badge ms-2"></span>
                    </div>
                    <div class="d-flex gap-3 flex-wrap text-muted small align-items-center">
                        <span><i class="bi bi-card-text me-1"></i><span id="lblNifCliente">—</span></span>
                        <span><i class="bi bi-telephone me-1"></i><span id="lblTelefonoCliente">—</span></span>
                        <span><i class="bi bi-envelope me-1"></i><span id="lblEmailCliente">—</span></span>
                    </div>
                </div>

                <!-- ── KPI CARDS ─────────────────────────────────────────── -->
                <div class="row g-3 mb-4">
                    <div class="col-6 col-md-3">
                        <div class="card shadow-sm h-100">
                            <div class="card-body">
                                <div class="text-muted mb-1">Total presupuestado</div>
                                <div class="fs-5 fw-bold" id="kpiTotalPresupuestado">
                                    <span class="placeholder col-8"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card shadow-sm h-100">
                            <div class="card-body">
                                <div class="text-muted mb-1">Facturas emitidas</div>
                                <div class="fs-5 fw-bold" id="kpiFacturasEmitidas">
                                    <span class="placeholder col-8"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card shadow-sm h-100">
                            <div class="card-body">
                                <div class="text-muted mb-1">Total cobrado</div>
                                <div class="fs-5 fw-bold" id="kpiTotalCobrado">
                                    <span class="placeholder col-8"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card shadow-sm h-100">
                            <div class="card-body">
                                <div class="text-muted mb-1">Pendiente cobro</div>
                                <div class="fs-5 fw-bold" id="kpiPendienteCobro">
                                    <span class="placeholder col-8"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ── BARRA DE ACCIONES ─────────────────────────────────── -->
                <div class="d-flex gap-2 mb-4 flex-wrap">
                    <a href="../Presupuesto/formularioPresupuesto.php?modo=nuevo&id_cliente=<?= $id_cliente ?>"
                       class="btn btn-primary btn-sm">
                        <i class="bi bi-plus-lg me-1"></i>Nuevo Presupuesto
                    </a>
                    <button class="btn btn-outline-secondary btn-sm" disabled
                            title="Próximamente — agrupa varios presupuestos en una sola factura">
                        <i class="bi bi-files me-1"></i>Factura Agrupada
                    </button>
                </div>

                <!-- ── TABS ──────────────────────────────────────────────── -->
                <style>
                    #tabsPanel .nav-link {
                        font-weight: 600;
                        font-size: 1rem;
                        padding: 0.65rem 1.2rem;
                        color: #6c757d;
                        border-bottom: 3px solid transparent;
                        transition: color .15s, border-color .15s;
                    }
                    #tabsPanel .nav-link:hover {
                        color: #5a6acf;
                        border-bottom-color: #c5c9f0;
                    }
                    #tabsPanel .nav-link.active {
                        color: #5a6acf;
                        border-bottom: 3px solid #5a6acf;
                        background: transparent;
                    }
                    #tabsPanel .tab-badge {
                        display: inline-flex;
                        align-items: center;
                        justify-content: center;
                        width: 20px;
                        height: 20px;
                        font-size: 0.72rem;
                        border-radius: 50%;
                        background: #e9ecef;
                        color: #6c757d;
                        margin-left: 6px;
                        font-weight: 700;
                        transition: background .15s;
                    }
                    #tabsPanel .nav-link.active .tab-badge {
                        background: #5a6acf;
                        color: #fff;
                    }
                    #tabsPanelContent {
                        background: #fff;
                        font-size: 1rem;
                    }
                    td.details-control {
                        cursor: pointer;
                    }
                    .fila-proforma td {
                        background-color: #fff8e1 !important;
                        color: #6d4c41 !important;
                    }
                    .fila-proforma:hover td {
                        background-color: #ffecb3 !important;
                    }
                </style>
                <ul class="nav nav-tabs mb-0 border-bottom" id="tabsPanel" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="tab-resumen-btn"
                                data-bs-toggle="tab" data-bs-target="#paneResumen"
                                type="button" role="tab">
                            <i class="bi bi-person-vcard me-1"></i>Resumen
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-presupuestos-btn"
                                data-bs-toggle="tab" data-bs-target="#panePresupuestos"
                                type="button" role="tab">
                            <i class="bi bi-file-text me-1"></i>Presupuestos
                            <span class="tab-badge" id="badge-presupuestos">0</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-facturas-btn"
                                data-bs-toggle="tab" data-bs-target="#paneFacturas"
                                type="button" role="tab">
                            <i class="bi bi-receipt me-1"></i>Facturas
                            <span class="tab-badge" id="badge-facturas">0</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-pagos-btn"
                                data-bs-toggle="tab" data-bs-target="#panePagos"
                                type="button" role="tab">
                            <i class="bi bi-cash-coin me-1"></i>Pagos
                            <span class="tab-badge" id="badge-pagos">0</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-contactos-btn"
                                data-bs-toggle="tab" data-bs-target="#paneContactos"
                                type="button" role="tab">
                            <i class="bi bi-people me-1"></i>Contactos
                            <span class="tab-badge" id="badge-contactos">0</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-ubicaciones-btn"
                                data-bs-toggle="tab" data-bs-target="#paneUbicaciones"
                                type="button" role="tab">
                            <i class="bi bi-geo-alt me-1"></i>Ubicaciones
                            <span class="tab-badge" id="badge-ubicaciones">0</span>
                        </button>
                    </li>
                </ul>

                <div class="tab-content border border-top-0" id="tabsPanelContent">

                    <!-- Tab: Resumen -->
                    <div class="tab-pane fade show active" id="paneResumen" role="tabpanel">
                        <div id="fichaResumenCliente" class="row g-3 mt-1">
                            <div class="col-12 col-md-6">
                                <div class="card h-100">
                                    <div class="card-header fw-semibold"><i class="bi bi-person me-2"></i>Datos del cliente</div>
                                    <div class="card-body">
                                        <dl class="row mb-0 small">
                                            <dt class="col-sm-5">C&oacute;digo</dt>       <dd class="col-sm-7" id="resumen_codigo">&mdash;</dd>
                                            <dt class="col-sm-5">Nombre</dt>       <dd class="col-sm-7" id="resumen_nombre">&mdash;</dd>
                                            <dt class="col-sm-5">NIF / CIF</dt>    <dd class="col-sm-7" id="resumen_nif">&mdash;</dd>
                                            <dt class="col-sm-5">Tipo</dt>          <dd class="col-sm-7" id="resumen_tipo">&mdash;</dd>
                                            <dt class="col-sm-5">Tel&eacute;fono</dt>     <dd class="col-sm-7" id="resumen_telefono">&mdash;</dd>
                                            <dt class="col-sm-5">M&oacute;vil</dt>        <dd class="col-sm-7" id="resumen_movil">&mdash;</dd>
                                            <dt class="col-sm-5">Email</dt>        <dd class="col-sm-7" id="resumen_email">&mdash;</dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="card h-100">
                                    <div class="card-header fw-semibold"><i class="bi bi-geo-alt me-2"></i>Direcci&oacute;n</div>
                                    <div class="card-body">
                                        <dl class="row mb-0 small">
                                            <dt class="col-sm-5">Direcci&oacute;n</dt>   <dd class="col-sm-7" id="resumen_direccion">&mdash;</dd>
                                            <dt class="col-sm-5">Poblaci&oacute;n</dt>   <dd class="col-sm-7" id="resumen_poblacion">&mdash;</dd>
                                            <dt class="col-sm-5">CP</dt>           <dd class="col-sm-7" id="resumen_cp">&mdash;</dd>
                                            <dt class="col-sm-5">Provincia</dt>   <dd class="col-sm-7" id="resumen_provincia">&mdash;</dd>
                                            <dt class="col-sm-5">Pa&iacute;s</dt>        <dd class="col-sm-7" id="resumen_pais">&mdash;</dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="card h-100">
                                    <div class="card-header fw-semibold"><i class="bi bi-briefcase me-2"></i>Datos comerciales</div>
                                    <div class="card-body">
                                        <dl class="row mb-0 small">
                                            <dt class="col-sm-5">Forma de pago</dt>  <dd class="col-sm-7" id="resumen_forma_pago">&mdash;</dd>
                                            <dt class="col-sm-5">Descuento</dt>       <dd class="col-sm-7" id="resumen_descuento">&mdash;</dd>
                                            <dt class="col-sm-5">Observaciones</dt>  <dd class="col-sm-7" id="resumen_observaciones">&mdash;</dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="card h-100">
                                    <div class="card-header fw-semibold"><i class="bi bi-calendar me-2"></i>Registro</div>
                                    <div class="card-body">
                                        <dl class="row mb-0 small">
                                            <dt class="col-sm-5">Alta</dt>          <dd class="col-sm-7" id="resumen_created_at">&mdash;</dd>
                                            <dt class="col-sm-5">Actualizaci&oacute;n</dt> <dd class="col-sm-7" id="resumen_updated_at">&mdash;</dd>
                                            <dt class="col-sm-5">Contactos</dt>     <dd class="col-sm-7" id="resumen_num_contactos">&mdash;</dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab: Presupuestos -->
                    <div class="tab-pane fade" id="panePresupuestos" role="tabpanel">
                        <table id="tblPresupuestosCliente"
                               class="table table-striped table-bordered dt-responsive nowrap w-100">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Número</th>
                                    <th>Nombre evento</th>
                                    <th>F. inicio</th>
                                    <th>F. fin</th>
                                    <th>Estado</th>
                                    <th>Total</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>

                    <!-- Tab: Facturas -->
                    <div class="tab-pane fade" id="paneFacturas" role="tabpanel">
                        <table id="tblFacturasCliente"
                               class="table table-striped table-bordered dt-responsive nowrap w-100">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Tipo</th>
                                    <th>Número</th>
                                    <th>Presupuesto</th>
                                    <th>Fecha emisión</th>
                                    <th>Importe</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>

                    <!-- Tab: Pagos -->
                    <div class="tab-pane fade" id="panePagos" role="tabpanel">
                        <table id="tblPagosCliente"
                               class="table table-striped table-bordered dt-responsive nowrap w-100">
                            <thead>
                                <tr>
                                    <th>Presupuesto</th>
                                    <th>Tipo</th>
                                    <th>Importe</th>
                                    <th>Fecha pago</th>
                                    <th>Método</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>

                    <!-- Tab: Contactos -->
                    <div class="tab-pane fade" id="paneContactos" role="tabpanel">
                        <table id="tblContactosCliente"
                               class="table table-striped table-bordered dt-responsive nowrap w-100">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Nombre</th>
                                    <th>Apellidos</th>
                                    <th>Cargo</th>
                                    <th>Departamento</th>
                                    <th>Tel&eacute;fono</th>
                                    <th>M&oacute;vil</th>
                                    <th>Email</th>
                                    <th>Principal</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>

                    <!-- Tab: Ubicaciones -->
                    <div class="tab-pane fade" id="paneUbicaciones" role="tabpanel">
                        <table id="tblUbicacionesCliente"
                               class="table table-striped table-bordered dt-responsive nowrap w-100">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Nombre</th>
                                    <th>Direcci&oacute;n</th>
                                    <th>CP</th>
                                    <th>Poblaci&oacute;n</th>
                                    <th>Provincia</th>
                                    <th>Persona contacto</th>
                                    <th>Tel&eacute;fono</th>
                                    <th>Principal</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>

                </div><!-- /tab-content -->

            </div><!-- /br-section-wrapper -->
        </div><!-- /br-pagebody -->

        <footer class="br-footer">
            <?php include_once('../../config/template/mainFooter.php') ?>
        </footer>

    </div><!-- /br-mainpanel -->
    <!-- ########## END: MAIN PANEL ########## -->

    <?php include_once('../../config/template/mainJs.php') ?>

    <script>
        const ID_CLIENTE = <?= $id_cliente ?>;
    </script>
    <script src="cliente_panel.js"></script>

</body>
</html>
