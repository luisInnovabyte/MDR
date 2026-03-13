<!DOCTYPE html>
<html lang="es">
<head>
    <?php include_once('../../config/template/mainHead.php') ?>
    <title>TMP - Datos de Prueba | MDR</title>
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

    <div class="br-mainpanel">
        <?php include_once('../../config/template/pageHeader.php') ?>

        <div class="br-pagebody">
            <div class="br-section-wrapper">

                <!-- Cabecera -->
                <div class="row mg-b-20">
                    <div class="col-12">
                        <div class="d-flex align-items-center">
                            <span class="badge badge-warning mg-r-10" style="font-size:14px; padding:6px 12px;">TMP</span>
                            <h4 class="mb-0 tx-gray-800">Datos de Prueba - Presupuestos</h4>
                        </div>
                        <p class="tx-gray-500 mg-t-5 mg-b-0">
                            Herramientas para generar o limpiar presupuestos ficticios de prueba en la base de datos.
                            Todos los presupuestos de prueba están marcados internamente y pueden eliminarse en cualquier momento.
                        </p>
                        <hr>
                    </div>
                </div>

                <!-- Panel de estado -->
                <div class="row mg-b-20">
                    <div class="col-12">
                        <div id="panel-estado" class="alert alert-info" style="display:none;"></div>
                    </div>
                </div>

                <!-- Tarjetas de acciones -->
                <div class="row">

                    <!-- CREAR DATOS -->
                    <div class="col-md-6 mg-b-20">
                        <div class="card card-body" style="border-left: 4px solid #28a745;">
                            <div class="d-flex align-items-start">
                                <div class="mg-r-15">
                                    <i class="icon ion-ios-add-circle tx-success" style="font-size:48px;"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="tx-gray-800 mg-b-5">Crear datos de prueba</h5>
                                    <p class="tx-gray-500 tx-13 mg-b-15">
                                        Genera <strong>4 presupuestos ficticios</strong> con <strong>30 artículos cada uno</strong>,
                                        con fechas futuras (Mayo–Agosto 2026) y distintos estados
                                        (Borrador, En Proceso, Aprobado, Esperando respuesta).
                                        Si ya existen datos de prueba anteriores, se eliminan y recrean automáticamente.
                                    </p>
                                    <ul class="tx-13 tx-gray-500 mg-b-15">
                                        <li>P-XXXXX/2026 — Congreso Tecnología Audiovisual</li>
                                        <li>P-XXXXX/2026 — Gala de Premios Empresariales</li>
                                        <li>P-XXXXX/2026 — Convención Anual Comercial <strong>(Aprobado)</strong></li>
                                        <li>P-XXXXX/2026 — Festival de Verano Costa Blanca</li>
                                    </ul>
                                    <button id="btn-crear" class="btn btn-success btn-block" onclick="ejecutarSeeder('crear')">
                                        <i class="icon ion-ios-add-circle mg-r-5"></i> Crear datos de prueba
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- LIMPIAR DATOS -->
                    <div class="col-md-6 mg-b-20">
                        <div class="card card-body" style="border-left: 4px solid #dc3545;">
                            <div class="d-flex align-items-start">
                                <div class="mg-r-15">
                                    <i class="icon ion-ios-trash tx-danger" style="font-size:48px;"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="tx-gray-800 mg-b-5">Limpiar datos de prueba</h5>
                                    <p class="tx-gray-500 tx-13 mg-b-15">
                                        Elimina de forma permanente todos los presupuestos de prueba creados con este seeder
                                        (líneas, versiones y cabecera). No afecta a los presupuestos reales del sistema.
                                    </p>
                                    <div class="alert alert-warning tx-13 pd-10 mg-b-15">
                                        <i class="icon ion-android-warning mg-r-5"></i>
                                        Esta acción es <strong>irreversible</strong>. Solo elimina los registros marcados con la etiqueta de test.
                                    </div>
                                    <button id="btn-limpiar" class="btn btn-danger btn-block" onclick="confirmarLimpiar()">
                                        <i class="icon ion-ios-trash mg-r-5"></i> Limpiar datos de prueba
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div><!-- row -->

                <!-- LOG de salida -->
                <div class="row mg-t-10" id="row-log" style="display:none;">
                    <div class="col-12">
                        <h6 class="tx-gray-700 mg-b-5"><i class="icon ion-ios-list mg-r-5"></i>Salida del proceso</h6>
                        <pre id="log-output" style="
                            background:#1e2328;
                            color:#a8ff78;
                            padding:15px;
                            border-radius:6px;
                            font-size:13px;
                            max-height:400px;
                            overflow-y:auto;
                            white-space:pre-wrap;
                            word-break:break-all;
                        "></pre>
                    </div>
                </div>

            </div><!-- br-section-wrapper -->
        </div><!-- br-pagebody -->

        <?php include_once('../../config/template/mainFooter.php') ?>
    </div><!-- br-mainpanel -->

    <?php include_once('../../config/template/mainJs.php') ?>

    <script>
    function ejecutarSeeder(accion) {
        const btnCrear   = document.getElementById('btn-crear');
        const btnLimpiar = document.getElementById('btn-limpiar');
        const panelEstado = document.getElementById('panel-estado');
        const rowLog     = document.getElementById('row-log');
        const logOutput  = document.getElementById('log-output');

        // Bloquear botones
        btnCrear.disabled   = true;
        btnLimpiar.disabled = true;

        // Mostrar estado
        panelEstado.style.display = 'block';
        panelEstado.className = 'alert alert-info';
        panelEstado.innerHTML = '<i class="icon ion-load-c mg-r-5"></i> Ejecutando... por favor espera.';

        rowLog.style.display = 'block';
        logOutput.textContent = '';

        $.ajax({
            url:  '../../controller/seeder_tmp.php',
            type: 'POST',
            data: { accion: accion },
            dataType: 'json',
            timeout: 60000,
            success: function(resp) {
                if (resp.success) {
                    panelEstado.className = 'alert alert-success';
                    panelEstado.innerHTML = '<i class="icon ion-checkmark-circled mg-r-5"></i><strong>Completado.</strong> ' + resp.message;
                } else {
                    panelEstado.className = 'alert alert-danger';
                    panelEstado.innerHTML = '<i class="icon ion-close-circled mg-r-5"></i><strong>Error.</strong> ' + resp.message;
                }
                logOutput.textContent = resp.log || '';
                logOutput.scrollTop   = logOutput.scrollHeight;
            },
            error: function(xhr) {
                panelEstado.className = 'alert alert-danger';
                panelEstado.innerHTML = '<i class="icon ion-close-circled mg-r-5"></i>Error de comunicación con el servidor.';
                logOutput.textContent = xhr.responseText || 'Sin respuesta del servidor.';
            },
            complete: function() {
                btnCrear.disabled   = false;
                btnLimpiar.disabled = false;
            }
        });
    }

    function confirmarLimpiar() {
        Swal.fire({
            title: '¿Limpiar datos de prueba?',
            text: 'Se eliminarán todos los presupuestos creados con el seeder. Esta acción no puede deshacerse.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor:  '#6c757d',
            confirmButtonText:  'Sí, limpiar',
            cancelButtonText:   'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                ejecutarSeeder('limpiar');
            }
        });
    }

    // Si se accede con ?accion=limpiar desde el menú, disparar confirm automáticamente
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('accion') === 'limpiar') {
        confirmarLimpiar();
    }
    </script>

</body>
</html>
