<!DOCTYPE html>
<html lang="es">

<head>
    <!-- ------------------------------ -->
    <!--        MainHead.php            -->
    <!-- ------------------------------ -->

    <?php include_once('../../config/template/mainHead.php') ?>

    <!-- ------------------------------ -->
    <!--      END MainHead.php          -->
    <!-- ------------------------------ -->

    <title>Registro de Kilometraje | MDR</title>
</head>

<body>

    <!-- ########## START: LEFT PANEL ########## -->

    <!-- ---------------------- -->
    <!--      MainLogo.php      -->
    <!-- ---------------------- -->

    <?php require_once('../../config/template/mainLogo.php') ?>

    <!-- ---------------------- -->
    <!--   END MainLogo.php     -->
    <!-- ---------------------- -->

    <div class="br-sideleft sideleft-scrollbar">
        <!-- ---------------------- -->
        <!--   MainSideBar.php      -->
        <!-- ---------------------- -->
        <?php require_once('../../config/template/mainSidebar.php') ?>

        <?php require_once('../../config/template/mainSidebarDown.php') ?>
        <!-- ---------------------- -->
        <!-- END MainSideBar.php    -->
        <!-- ---------------------- -->
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
        <!-- ---------------------- -->
        <!--   mainRightPanel.php      -->
        <!-- ---------------------- -->
        <?php include_once('../../config/template/mainRightPanel.php') ?>
        <!-- ------------------------- -->
        <!-- END MainRightPanel.php    -->
        <!-- ------------------------- -->

    </div><!-- br-sideright -->
    <!-- ########## END: RIGHT PANEL ########## -->

    <!-- ########## START: MAIN PANEL ########## -->
    <div class="br-mainpanel">
        <div class="br-pageheader">
            <nav class="breadcrumb pd-0 mg-0 tx-12">
                <a class="breadcrumb-item" href="../Dashboard/index.php">Dashboard</a>
                <a class="breadcrumb-item" href="../MntFurgonetas/index.php">Furgonetas</a>
                <a class="breadcrumb-item" href="../MntFurgonetas_mantenimiento/index.php?id_furgoneta=<?php echo $_GET['id_furgoneta'] ?? ''; ?>">Mantenimiento</a>
                <a class="breadcrumb-item" href="index.php?id_furgoneta=<?php echo $_GET['id_furgoneta'] ?? ''; ?>">Registro de Kilometraje</a>
                <span class="breadcrumb-item active">Nuevo Registro</span>
            </nav>
        </div><!-- br-pageheader -->
        
        <div class="br-pagetitle">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <h4 class="mb-0 me-2">Nuevo Registro de Kilometraje</h4>
                    <button type="button" class="btn btn-link p-0 ms-1" data-bs-toggle="modal" data-bs-target="#modalAyudaFormulario" title="Ayuda sobre el formulario">
                        <i class="bi bi-question-circle text-primary" style="font-size: 1.3rem;"></i>
                    </button>
                </div>
                
                <!-- Botón de regreso -->
                <a href="index.php?id_furgoneta=<?php echo $_GET['id_furgoneta'] ?? ''; ?>" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Volver al historial
                </a>
            </div>

            <!-- Info de la furgoneta -->
            <div class="mt-3" id="info-furgoneta">
                <div class="alert alert-info d-flex align-items-center" role="alert">
                    <i class="bi bi-info-circle me-2"></i>
                    <div>
                        <strong>Furgoneta:</strong> <span id="matricula-furgoneta">Cargando...</span>
                        <small class="ms-3 text-muted"><span id="marca-furgoneta"></span> <span id="modelo-furgoneta"></span></small>
                        <small class="ms-3 text-muted">ID: <span id="id-furgoneta"><?php echo $_GET['id_furgoneta'] ?? ''; ?></span></small>
                    </div>
                </div>
                <!-- Kilometraje actual -->
                <div class="alert alert-success d-flex align-items-center" role="alert">
                    <i class="fas fa-tachometer-alt me-2"></i>
                    <div>
                        <strong>Kilometraje actual registrado:</strong> <span id="km-actual">Cargando...</span> km
                        <small class="ms-3 text-muted">Último registro: <span id="fecha-ultimo-registro">-</span></small>
                    </div>
                </div>
            </div>
            <br>
        </div><!-- br-pagetitle -->

        <div class="br-pagebody">
            <div class="br-section-wrapper">
                
                <!-- Formulario de Registro de Kilometraje -->
                <form id="formKilometraje">
                    <!-- Campos ocultos -->
                    <input type="hidden" name="id_furgoneta" id="id_furgoneta" value="<?php echo $_GET['id_furgoneta'] ?? ''; ?>">

                    <!-- SECCIÓN: Datos del Registro -->
                    <div class="card mb-4 border-primary">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0 tx-bold">
                                <i class="fas fa-tachometer-alt me-2"></i>Registro de Kilometraje
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-12 col-md-6">
                                    <label for="fecha_registro_km" class="form-label">Fecha del Registro: <span class="tx-danger">*</span></label>
                                    <input type="date" class="form-control" name="fecha_registro_km" id="fecha_registro_km" required>
                                    <div class="invalid-feedback small-invalid-feedback">Ingrese la fecha del registro</div>
                                    <small class="form-text text-muted">Fecha de la lectura del kilometraje</small>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="kilometraje_registrado_km" class="form-label">Kilometraje: <span class="tx-danger">*</span></label>
                                    <input type="number" class="form-control" name="kilometraje_registrado_km" id="kilometraje_registrado_km" min="0" placeholder="Ej: 50000" required>
                                    <div class="invalid-feedback small-invalid-feedback">Ingrese el kilometraje registrado</div>
                                    <small class="form-text text-muted">Lectura actual del cuentakilómetros</small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12 col-md-6">
                                    <label for="tipo_registro_km" class="form-label">Tipo de Registro: <span class="tx-danger">*</span></label>
                                    <select class="form-control" name="tipo_registro_km" id="tipo_registro_km" required>
                                        <option value="">Seleccione...</option>
                                        <option value="manual" selected>Manual</option>
                                        <option value="revision">Revisión</option>
                                        <option value="itv">ITV</option>
                                        <option value="evento">Evento</option>
                                    </select>
                                    <div class="invalid-feedback small-invalid-feedback">Seleccione el tipo de registro</div>
                                    <small class="form-text text-muted">Origen o motivo del registro</small>
                                    <div class="alert alert-info mt-2 mb-0 py-2" role="alert">
                                        <i class="fas fa-info-circle me-2"></i><strong>Nota:</strong> Este registro solo documenta el kilometraje. No afecta el estado del vehículo ni crea mantenimientos.
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="observaciones_registro_km" class="form-label">Observaciones:</label>
                                    <textarea class="form-control" name="observaciones_registro_km" id="observaciones_registro_km" rows="4" placeholder="Notas adicionales sobre este registro..."></textarea>
                                    <small class="form-text text-muted">Información adicional del registro</small>
                                </div>
                            </div>

                            <!-- Información de validación -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="alert alert-warning" role="alert">
                                        <h6 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i>Validaciones del sistema</h6>
                                        <ul class="mb-0">
                                            <li>El kilometraje debe ser mayor o igual al último registro: <strong id="validacion-km-minimo">0 km</strong></li>
                                            <li>La fecha no puede ser anterior al último registro: <strong id="validacion-fecha-minima">-</strong></li>
                                            <li>La fecha no puede ser futura</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botones de acción -->
                    <div class="card">
                        <div class="card-body text-center">
                            <button type="submit" name="action" id="btnGuardarKilometraje" class="btn btn-primary btn-lg me-3">
                                <i class="fas fa-save me-2"></i>Guardar Registro
                            </button>
                            <a href="index.php?id_furgoneta=<?php echo $_GET['id_furgoneta'] ?? ''; ?>" class="btn btn-secondary btn-lg">
                                <i class="fas fa-times me-2"></i>Cancelar
                            </a>
                        </div>
                    </div>

                </form>

            </div><!-- br-section-wrapper -->
        </div><!-- br-pagebody -->

        <footer class="br-footer">
            <?php include_once('../../config/template/mainFooter.php') ?>
        </footer>
    </div><!-- br-mainpanel -->
    <!-- ########## END: MAIN PANEL ########## -->

    <!-- Modal de Ayuda del Formulario -->
    <div class="modal fade" id="modalAyudaFormulario" tabindex="-1" aria-labelledby="modalAyudaFormularioLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalAyudaFormularioLabel">
                        <i class="fas fa-question-circle me-2"></i>Ayuda - Registro de Kilometraje
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <h6 class="text-primary"><i class="fas fa-tachometer-alt me-2"></i>¿Qué es el Registro de Kilometraje?</h6>
                            <p>Sistema para documentar las lecturas del cuentakilómetros de la furgoneta a lo largo del tiempo.</p>
                            <hr>
                        </div>
                        
                        <div class="col-12">
                            <h6 class="text-primary"><i class="fas fa-clipboard-list me-2"></i>Campos del Formulario</h6>
                            <ul class="list-unstyled ms-3">
                                <li><i class="fas fa-check text-success me-2"></i><strong>Fecha del Registro:</strong> Obligatorio. Fecha en que se realizó la lectura</li>
                                <li><i class="fas fa-check text-success me-2"></i><strong>Kilometraje:</strong> Obligatorio. Lectura actual del cuentakilómetros</li>
                                <li><i class="fas fa-check text-success me-2"></i><strong>Tipo:</strong> Obligatorio. Origen del registro (Manual, Revisión, ITV, Evento)</li>
                                <li><i class="fas fa-info text-info me-2"></i><strong>Observaciones:</strong> Opcional. Notas sobre el registro</li>
                            </ul>
                            <hr>
                        </div>

                        <div class="col-12">
                            <h6 class="text-primary"><i class="fas fa-shield-alt me-2"></i>Validaciones</h6>
                            <ul class="list-unstyled ms-3">
                                <li><i class="fas fa-check-circle text-success me-2"></i>El kilometraje debe ser mayor o igual al último registro</li>
                                <li><i class="fas fa-check-circle text-success me-2"></i>La fecha no puede ser anterior al último registro</li>
                                <li><i class="fas fa-check-circle text-success me-2"></i>La fecha no puede ser futura</li>
                                <li><i class="fas fa-check-circle text-success me-2"></i>Todos los campos obligatorios deben completarse</li>
                            </ul>
                            <hr>
                        </div>

                        <div class="col-12">
                            <h6 class="text-primary"><i class="fas fa-lightbulb me-2"></i>Tipos de Registro</h6>
                            <ul class="list-unstyled ms-3">
                                <li><i class="fas fa-hand-pointer text-primary me-2"></i><strong>Manual:</strong> Registro realizado manualmente por el usuario</li>
                                <li><i class="fas fa-wrench text-warning me-2"></i><strong>Revisión:</strong> Kilometraje registrado durante una revisión</li>
                                <li><i class="fas fa-clipboard-check text-info me-2"></i><strong>ITV:</strong> Kilometraje registrado en la ITV</li>
                                <li><i class="fas fa-flag text-danger me-2"></i><strong>Evento:</strong> Kilometraje registrado por un evento específico</li>
                            </ul>
                            <hr>
                        </div>

                        <div class="col-12">
                            <div class="alert alert-info" role="alert">
                                <h6 class="alert-heading"><i class="fas fa-info-circle me-2"></i>Importante</h6>
                                <ul class="mb-0">
                                    <li>Los registros de kilometraje <strong>NO se pueden modificar</strong> después de crearlos</li>
                                    <li>Los registros sirven para llevar un historial preciso del uso del vehículo</li>
                                    <li>Este registro <strong>NO afecta el estado del vehículo</strong> ni crea automáticamente registros de mantenimiento</li>
                                    <li>Para documentar mantenimientos, usar el módulo correspondiente</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ----------------------- -->
    <!--       mainJs.php        -->
    <!-- ----------------------- -->
    <?php include_once('../../config/template/mainJs.php') ?>

    <script src="../../public/js/tooltip-colored.js"></script>
    <script src="../../public/js/popover-colored.js"></script>
    <!-- ------------------------- -->
    <!--     END mainJs.php        -->
    <!-- ------------------------- -->
    <script type="text/javascript" src="formularioKilometraje.js"></script>

</body>

</html>
