<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <?php include_once('../../config/template/mainHead.php') ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <style>
        /* ===== Layout dos paneles ===== */
        #panel-lista {
            height: calc(100vh - 155px);
            overflow-y: auto;
            border-right: 1px solid #dee2e6;
            padding-right: 0;
        }
        #panel-ficha {
            height: calc(100vh - 155px);
            overflow-y: auto;
            padding-left: 0;
        }

        /* ===== Lista de elementos ===== */
        #buscador-elemento {
            border-radius: 0;
            border-top: none;
            border-left: none;
            border-right: none;
            border-bottom: 1px solid #dee2e6;
        }
        .el-item {
            padding: 8px 14px;
            cursor: pointer;
            border-bottom: 1px solid #f0f0f0;
            transition: background .15s;
        }
        .el-item:hover { background: #f1f3f5; }
        .el-item.active { background: #e7f0ff; border-left: 3px solid #0d6efd; }
        .el-item .el-codigo { font-size: .75rem; color: #6c757d; }
        .el-item .el-nombre { font-size: .88rem; font-weight: 500; }
        .el-estado-dot {
            width: 9px; height: 9px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 5px;
        }

        /* ===== Panel ficha ===== */
        #ficha-placeholder {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            color: #adb5bd;
        }
        #ficha-detalle { display: none; }

        /* ===== Info cards ===== */
        .info-card { background: #f8f9fa; border-radius: 6px; padding: 14px 16px; margin-bottom: 12px; }
        .info-card .info-title { font-size: .7rem; text-transform: uppercase; letter-spacing: .05em; color: #6c757d; margin-bottom: 4px; }
        .info-card .info-value { font-size: .92rem; font-weight: 500; }

        /* ===== Estado badge ===== */
        .badge-estado { font-size: .8rem; padding: 4px 10px; border-radius: 20px; color: #fff; }

        /* ===== Chart ===== */
        #chartSalidas { max-height: 320px; }

        /* ===== Tabla presupuestos ===== */
        #tblPresupuestos td, #tblPresupuestos th { font-size: .82rem; vertical-align: middle; }

        /* ===== Spinner lista ===== */
        #spinner-lista { padding: 20px; text-align: center; color: #6c757d; }
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
        <div class="br-pageheader">
            <nav class="breadcrumb pd-0 mg-0 tx-12">
                <a class="breadcrumb-item" href="../../index.php">MDR</a>
                <span class="breadcrumb-item">Presupuestos</span>
                <span class="breadcrumb-item active">Ficha de Elementos</span>
            </nav>
        </div><!-- br-pageheader -->

        <div class="br-pagetitle">
            <div class="d-flex align-items-center">
                <h4 class="mb-0 me-2">Ficha de Elementos</h4>
            </div>
            <br>
        </div><!-- br-pagetitle -->

        <div class="br-pagebody pd-x-0 pd-t-0">
            <div class="br-section-wrapper pd-0">
                <div class="row g-0" style="min-height: calc(100vh - 200px);">

                    <!-- ===== PANEL IZQUIERDO: lista ===== -->
                    <div class="col-md-3" id="panel-lista">
                        <input type="text" id="buscador-elemento" class="form-control"
                               placeholder="&#x1F50D;  Buscar elemento..." autocomplete="off">

                        <div id="spinner-lista">
                            <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                            Cargando elementos...
                        </div>
                        <div id="lista-elementos"></div>
                    </div>

                    <!-- ===== PANEL DERECHO: ficha ===== -->
                    <div class="col-md-9" id="panel-ficha">

                        <!-- Placeholder inicial -->
                        <div id="ficha-placeholder">
                            <i class="bi bi-boxes" style="font-size:64px; margin-bottom:12px;"></i>
                            <p class="tx-16">Selecciona un elemento de la lista</p>
                        </div>

                        <!-- Contenido ficha -->
                        <div id="ficha-detalle" class="pd-20">

                            <!-- Cabecera del elemento -->
                            <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                                <div>
                                    <h5 class="mb-1" id="fh-nombre">—</h5>
                                    <span class="text-muted small" id="fh-codigo">—</span>
                                </div>
                                <span class="badge-estado" id="fh-estado-badge">—</span>
                            </div>

                            <!-- Tabs -->
                            <ul class="nav nav-tabs mb-3" id="fichaTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="tab-info-btn" data-bs-toggle="tab"
                                            data-bs-target="#tab-info" type="button">
                                        <i class="bi bi-info-circle me-1"></i> Información
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="tab-presupuestos-btn" data-bs-toggle="tab"
                                            data-bs-target="#tab-presupuestos" type="button">
                                        <i class="bi bi-file-earmark-text me-1"></i> Presupuestos
                                        <span class="badge bg-secondary ms-1" id="cnt-presupuestos">0</span>
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="tab-grafico-btn" data-bs-toggle="tab"
                                            data-bs-target="#tab-grafico" type="button">
                                        <i class="bi bi-bar-chart-line me-1"></i> Salidas por mes
                                    </button>
                                </li>
                            </ul>

                            <div class="tab-content" id="fichaTabContent">

                                <!-- ===== TAB INFORMACIÓN ===== -->
                                <div class="tab-pane fade show active" id="tab-info" role="tabpanel">
                                    <div class="row">
                                        <!-- Columna izquierda -->
                                        <div class="col-md-6">
                                            <div class="info-card">
                                                <div class="info-title">Descripción</div>
                                                <div class="info-value" id="fi-descripcion">—</div>
                                            </div>
                                            <div class="info-card">
                                                <div class="info-title">Artículo / Familia / Grupo</div>
                                                <div class="info-value" id="fi-jerarquia">—</div>
                                            </div>
                                            <div class="info-card">
                                                <div class="info-title">Marca / Modelo</div>
                                                <div class="info-value" id="fi-marca-modelo">—</div>
                                            </div>
                                            <div class="info-card">
                                                <div class="info-title">Número de serie</div>
                                                <div class="info-value" id="fi-serie">—</div>
                                            </div>
                                            <div class="info-card">
                                                <div class="info-title">Código de barras</div>
                                                <div class="info-value" id="fi-codbarras">—</div>
                                            </div>
                                            <div class="info-card">
                                                <div class="info-title">Tipo de propiedad</div>
                                                <div class="info-value" id="fi-propiedad">—</div>
                                            </div>
                                        </div>
                                        <!-- Columna derecha -->
                                        <div class="col-md-6">
                                            <div class="info-card">
                                                <div class="info-title">Ubicación (Nave / Pasillo / Altura)</div>
                                                <div class="info-value" id="fi-ubicacion">—</div>
                                            </div>
                                            <div class="info-card">
                                                <div class="info-title">Fecha de compra</div>
                                                <div class="info-value" id="fi-fecha-compra">—</div>
                                            </div>
                                            <div class="info-card">
                                                <div class="info-title">Precio de compra</div>
                                                <div class="info-value" id="fi-precio-compra">—</div>
                                            </div>
                                            <div class="info-card">
                                                <div class="info-title">Fin de garantía</div>
                                                <div class="info-value" id="fi-garantia">—</div>
                                            </div>
                                            <div class="info-card">
                                                <div class="info-title">Próximo mantenimiento</div>
                                                <div class="info-value" id="fi-mantenimiento">—</div>
                                            </div>
                                            <div class="info-card">
                                                <div class="info-title">Días en servicio</div>
                                                <div class="info-value" id="fi-dias">—</div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Proveedor -->
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info-card">
                                                <div class="info-title">Proveedor compra</div>
                                                <div class="info-value" id="fi-prov-compra">—</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-card">
                                                <div class="info-title">Proveedor alquiler</div>
                                                <div class="info-value" id="fi-prov-alquiler">—</div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Observaciones -->
                                    <div class="info-card">
                                        <div class="info-title">Observaciones</div>
                                        <div class="info-value" id="fi-observaciones" style="white-space: pre-wrap;">—</div>
                                    </div>
                                </div>

                                <!-- ===== TAB PRESUPUESTOS ===== -->
                                <div class="tab-pane fade" id="tab-presupuestos" role="tabpanel">
                                    <div id="spinner-ppto" class="text-center pd-20" style="display:none;">
                                        <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                                        Cargando presupuestos...
                                    </div>
                                    <div class="table-responsive">
                                        <table id="tblPresupuestos" class="table table-striped table-bordered nowrap" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th>Nº</th>
                                                    <th>Evento</th>
                                                    <th>Cliente</th>
                                                    <th>Fecha inicio</th>
                                                    <th>Estado</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- ===== TAB GRÁFICO ===== -->
                                <div class="tab-pane fade" id="tab-grafico" role="tabpanel">
                                    <div id="spinner-chart" class="text-center pd-20" style="display:none;">
                                        <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                                        Cargando datos...
                                    </div>
                                    <div class="pd-10">
                                        <p class="tx-gray-500 tx-12 mg-b-10">Número de presupuestos distintos en los que ha salido el elemento — últimos 24 meses</p>
                                        <canvas id="chartSalidas"></canvas>
                                        <p id="chart-empty" class="text-center tx-gray-400 pd-20" style="display:none;">
                                            Sin datos de salidas en los últimos 24 meses
                                        </p>
                                    </div>
                                </div>

                            </div><!-- /tab-content -->
                        </div><!-- /ficha-detalle -->
                    </div><!-- /panel-ficha -->

                </div><!-- /row -->
            </div><!-- /br-section-wrapper -->
        </div><!-- /br-pagebody -->
    </div><!-- /br-mainpanel -->

    <?php include_once('../../config/template/mainJs.php') ?>
    <script src="fichaElemento.js"></script>
</body>
</html>
