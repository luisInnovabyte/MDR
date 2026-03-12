<?php
// ----------------------
//   Comprobar permisos
// ----------------------
$moduloActual = 'usuarios';
require_once('../../config/template/verificarPermiso.php');
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <?php include_once('../../config/template/mainHead.php') ?>
</head>

<body>

    <!-- ########## START: LEFT PANEL ########## -->
    <?php require_once('../../config/template/mainLogo.php') ?>

    <div class="br-sideleft sideleft-scrollbar">
        <?php require_once('../../config/template/mainSidebar.php') ?>
        <?php require_once('../../config/template/mainSidebarDown.php') ?>
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
        <?php include_once('../../config/template/mainRightPanel.php') ?>
    </div><!-- br-sideright -->
    <!-- ########## END: RIGHT PANEL ########## -->

    <!-- ########## START: MAIN PANEL ########## -->
    <div class="br-mainpanel">
        <div class="br-pageheader">
            <nav class="breadcrumb pd-0 mg-0 tx-12">
                <a class="breadcrumb-item" href="../Dashboard/index.php">Dashboard</a>
                <span class="breadcrumb-item active">Albaranes de Carga</span>
            </nav>
        </div><!-- br-pageheader -->

        <div class="br-pagetitle">
            <div class="d-flex align-items-center">
                <i class="fas fa-clipboard-list me-2 text-primary" style="font-size:1.6rem;"></i>
                <h4 class="mb-0 me-2">Albaranes de Carga</h4>
                <button type="button" class="btn btn-link p-0 ms-1" data-bs-toggle="modal" data-bs-target="#modalAyudaAlbaranes" title="Ayuda" style="font-size:1.3rem; color:#6c757d; line-height:1;">
                    <i class="fas fa-question-circle"></i>
                </button>
            </div>
            <p class="mg-b-0 tx-13 text-muted mt-1">Presupuestos aprobados con evento próximo. Genera el albarán completo o resumido para la carga de material.</p>
        </div><!-- br-pagetitle -->

        <div class="br-pagebody">
            <div class="br-section-wrapper">

                <div class="table-wrapper">
                    <table id="tblAlbaranesCarga" class="table display responsive nowrap" style="width:100%">
                        <thead>
                            <tr>
                                <th>Nº Presupuesto</th>
                                <th>Evento</th>
                                <th>Cliente</th>
                                <th>Fecha evento</th>
                                <th>Fecha montaje</th>
                                <th>Fecha desmontaje</th>
                                <th class="text-center">Albaranes</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Se carga vía AJAX -->
                        </tbody>
                    </table>
                </div>

            </div><!-- br-section-wrapper -->
        </div><!-- br-pagebody -->

    </div><!-- br-mainpanel -->
    <!-- ########## END: MAIN PANEL ########## -->

    <!-- ################################ -->
    <!-- MODAL AYUDA                       -->
    <!-- ################################ -->
    <div class="modal fade" id="modalAyudaAlbaranes" tabindex="-1" aria-labelledby="modalAyudaAlbaranesLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalAyudaAlbaranesLabel">
                        <i class="fas fa-question-circle me-2"></i>¿Cómo funcionan los Albaranes de Carga?
                    </h5>
                    <button type="button" class="close text-white" data-bs-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <p class="text-muted">
                        Esta pantalla muestra todos los <strong>presupuestos aprobados</strong> cuyo evento está pendiente de producirse.
                        Desde aquí puedes generar el albarán de carga para preparar el material antes del servicio.
                    </p>

                    <h6 class="text-primary border-bottom pb-2 mt-4">
                        <i class="fas fa-table me-2"></i>¿Qué datos aparecen?
                    </h6>
                    <ul class="mt-2" style="line-height:2.2;">
                        <li><strong>Nº Presupuesto</strong> — identificador del presupuesto aprobado.</li>
                        <li><strong>Evento</strong> — nombre del servicio o evento.</li>
                        <li><strong>Cliente</strong> — empresa o persona contratante.</li>
                        <li><strong>Fecha evento</strong> — rango de fechas del evento (inicio → fin).</li>
                        <li><strong>Fecha montaje</strong> — fecha más temprana de montaje entre todas las líneas del presupuesto.</li>
                        <li><strong>Fecha desmontaje</strong> — fecha más tardía de desmontaje entre todas las líneas.</li>
                    </ul>

                    <h6 class="text-primary border-bottom pb-2 mt-4">
                        <i class="fas fa-print me-2"></i>¿Qué albaranes puedo generar?
                    </h6>
                    <div class="row mt-3">
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-start gap-2">
                                <span class="badge bg-warning text-dark px-3 py-2 flex-shrink-0"><i class="fas fa-clipboard-list"></i></span>
                                <div>
                                    <strong>Albarán de carga completo</strong><br>
                                    <small class="text-muted">Lista detallada de todos los elementos individuales a cargar.</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-start gap-2">
                                <span class="badge bg-info text-white px-3 py-2 flex-shrink-0"><i class="fas fa-boxes"></i></span>
                                <div>
                                    <strong>Albarán resumido</strong><br>
                                    <small class="text-muted">Agrupado por artículo, sin desglose de unidades individuales.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <p class="text-muted small mt-1">Ambos albaranes se abren en una nueva pestaña en formato PDF.</p>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- FIN MODAL AYUDA -->

    <!-- ----------------------- -->
    <!--       mainJs.php        -->
    <!-- ----------------------- -->
    <?php include_once('../../config/template/mainJs.php') ?>
    <!-- ------------------------- -->
    <!--     END mainJs.php        -->
    <!-- ------------------------- -->

    <script src="albaranesCarga.js"></script>

</body>

</html>
