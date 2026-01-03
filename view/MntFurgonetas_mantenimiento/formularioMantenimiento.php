<!-- ---------------------- -->
<!--   Comprobar permisos     -->
<!-- ---------------------- -->
<?php $moduloActual = 'usuarios'; ?>
<?php require_once('../../config/template/verificarPermiso.php'); ?>

<!DOCTYPE html>
<html lang="es">

<!-- ---------------------- -->
<!--      MainHead.php      -->
<!-- ---------------------- -->

<head>
    <?php include_once('../../config/template/mainHead.php') ?>
</head>

<!-- ---------------------- -->
<!--  END MainHead.php      -->
<!-- ---------------------- -->

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
                <a class="breadcrumb-item" href="index.php">Historial de Mantenimiento</a>
                <span class="breadcrumb-item active" id="breadcrumb-title">Nuevo Mantenimiento</span>
            </nav>
        </div><!-- br-pageheader -->
        
        <div class="br-pagetitle">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <h4 class="mb-0 me-2" id="page-title">Nuevo Mantenimiento</h4>
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
            </div>
            <br>
        </div><!-- br-pagetitle -->

        <div class="br-pagebody">
            <div class="br-section-wrapper">
                
                <!-- Formulario de Mantenimiento -->
                <form id="formMantenimiento">
                    <!-- Campos ocultos -->
                    <input type="hidden" name="id_mantenimiento" id="id_mantenimiento">
                    <input type="hidden" name="id_furgoneta" id="id_furgoneta" value="<?php echo $_GET['id_furgoneta'] ?? ''; ?>">

                    <!-- SECCIÓN: Datos del Mantenimiento -->
                    <div class="card mb-4 border-primary">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0 tx-bold">
                                <i class="fas fa-wrench me-2"></i>Datos del Mantenimiento
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-12 col-md-6">
                                    <label for="fecha_mantenimiento" class="form-label">Fecha del Servicio: <span class="tx-danger">*</span></label>
                                    <input type="date" class="form-control" name="fecha_mantenimiento" id="fecha_mantenimiento" required>
                                    <div class="invalid-feedback small-invalid-feedback">Ingrese la fecha del servicio</div>
                                    <small class="form-text text-muted">Fecha en que se realizó el trabajo</small>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="tipo_mantenimiento" class="form-label">Tipo de Mantenimiento: <span class="tx-danger">*</span></label>
                                    <select class="form-control" name="tipo_mantenimiento" id="tipo_mantenimiento" required>
                                        <option value="">Seleccione...</option>
                                        <option value="revision">Revisión</option>
                                        <option value="reparacion">Reparación</option>
                                        <option value="itv">ITV</option>
                                        <option value="neumaticos">Neumáticos</option>
                                        <option value="otros">Otros</option>
                                    </select>
                                    <div class="invalid-feedback small-invalid-feedback">Seleccione el tipo de servicio</div>
                                    <small class="form-text text-muted">Categoría del trabajo realizado</small>
                                    <div class="alert alert-warning mt-2 mb-0 py-2" role="alert">
                                        <i class="fas fa-info-circle me-2"></i><strong>Nota importante:</strong> Seleccionar el tipo de servicio aquí <strong>NO afectará al estado del vehículo</strong> en la ficha general. Si el vehículo queda inoperativo, debe modificarse manualmente en la ficha del vehículo.
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12">
                                    <label for="descripcion_mantenimiento" class="form-label">Descripción del Trabajo: <span class="tx-danger">*</span></label>
                                    <textarea class="form-control" name="descripcion_mantenimiento" id="descripcion_mantenimiento" rows="4" placeholder="Detalle el trabajo realizado..." required></textarea>
                                    <div class="invalid-feedback small-invalid-feedback">Describa el trabajo realizado</div>
                                    <small class="form-text text-muted">Descripción detallada del servicio</small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12 col-md-6">
                                    <label for="kilometraje_mantenimiento" class="form-label">Kilometraje:</label>
                                    <input type="number" class="form-control" name="kilometraje_mantenimiento" id="kilometraje_mantenimiento" min="0" placeholder="Ej: 50000">
                                    <div class="invalid-feedback small-invalid-feedback">Ingrese un kilometraje válido</div>
                                    <small class="form-text text-muted">Kilómetros en el momento del servicio</small>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="costo_mantenimiento" class="form-label">Costo (€):</label>
                                    <input type="number" class="form-control" name="costo_mantenimiento" id="costo_mantenimiento" min="0" step="0.01" placeholder="Ej: 150.50">
                                    <div class="invalid-feedback small-invalid-feedback">Ingrese un importe válido</div>
                                    <small class="form-text text-muted">Importe total del servicio</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECCIÓN: Información del Taller -->
                    <div class="card mb-4 border-info">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0 tx-bold">
                                <i class="fas fa-store me-2"></i>Información del Taller
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-12 col-md-6">
                                    <label for="numero_factura_mantenimiento" class="form-label">Número de Factura:</label>
                                    <input type="text" class="form-control" name="numero_factura_mantenimiento" id="numero_factura_mantenimiento" maxlength="100" placeholder="Ej: FAC-2024-001">
                                    <div class="invalid-feedback small-invalid-feedback">Máximo 100 caracteres</div>
                                    <small class="form-text text-muted">Número de factura del taller</small>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="taller_mantenimiento" class="form-label">Nombre del Taller:</label>
                                    <input type="text" class="form-control" name="taller_mantenimiento" id="taller_mantenimiento" maxlength="255" placeholder="Ej: Taller Mecánico García">
                                    <div class="invalid-feedback small-invalid-feedback">Máximo 255 caracteres</div>
                                    <small class="form-text text-muted">Nombre del establecimiento</small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12 col-md-6">
                                    <label for="telefono_taller_mantenimiento" class="form-label">Teléfono del Taller:</label>
                                    <input type="text" class="form-control" name="telefono_taller_mantenimiento" id="telefono_taller_mantenimiento" maxlength="50" placeholder="Ej: 912345678">
                                    <div class="invalid-feedback small-invalid-feedback">Ingrese un teléfono válido</div>
                                    <small class="form-text text-muted">Contacto del taller</small>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="direccion_taller_mantenimiento" class="form-label">Dirección del Taller:</label>
                                    <input type="text" class="form-control" name="direccion_taller_mantenimiento" id="direccion_taller_mantenimiento" maxlength="255" placeholder="Ej: Calle Principal, 123">
                                    <div class="invalid-feedback small-invalid-feedback">Máximo 255 caracteres</div>
                                    <small class="form-text text-muted">Ubicación del taller</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECCIÓN: Información ITV (solo visible si tipo=itv) -->
                    <div class="card mb-4 border-warning" id="seccion_itv" style="display: none;">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="mb-0 tx-bold">
                                <i class="fas fa-clipboard-check me-2"></i>Información de la ITV
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-12 col-md-6">
                                    <label for="resultado_itv" class="form-label">Resultado de la ITV:</label>
                                    <select class="form-control" name="resultado_itv" id="resultado_itv">
                                        <option value="">Seleccione...</option>
                                        <option value="favorable">Favorable</option>
                                        <option value="desfavorable">Desfavorable</option>
                                        <option value="negativa">Negativa</option>
                                    </select>
                                    <small class="form-text text-muted">Resultado obtenido en la inspección</small>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="fecha_proxima_itv" class="form-label">Fecha Próxima ITV:</label>
                                    <input type="date" class="form-control" name="fecha_proxima_itv" id="fecha_proxima_itv">
                                    <small class="form-text text-muted">Cuándo toca la siguiente inspección</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECCIÓN: Garantía y Observaciones -->
                    <div class="card mb-4 border-success">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0 tx-bold">
                                <i class="fas fa-shield-alt me-2"></i>Garantía y Observaciones
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-12 col-md-6">
                                    <label for="garantia_hasta_mantenimiento" class="form-label">Garantía Hasta:</label>
                                    <input type="date" class="form-control" name="garantia_hasta_mantenimiento" id="garantia_hasta_mantenimiento">
                                    <small class="form-text text-muted">Fecha límite de la garantía del trabajo</small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12">
                                    <label for="observaciones_mantenimiento" class="form-label">Observaciones:</label>
                                    <textarea class="form-control" name="observaciones_mantenimiento" id="observaciones_mantenimiento" rows="4" placeholder="Notas adicionales sobre el servicio..."></textarea>
                                    <small class="form-text text-muted">Información adicional del mantenimiento</small>
                                </div>
                            </div>

                            <!-- Estado del mantenimiento (solo en edición) -->
                            <div class="row" id="estado_section" style="display: none;">
                                <div class="col-12">
                                    <label class="form-label">Estado del registro:</label>
                                    <!-- Indicador visual (solo lectura) -->
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="activo_mantenimiento_display" checked disabled>
                                        <label class="form-check-label" for="activo_mantenimiento_display">
                                            <strong id="estado_texto">Registro Activo</strong>
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        <span id="estado_descripcion">Los registros nuevos siempre se crean activos. El estado se puede cambiar desde la lista.</span>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botones de acción -->
                    <div class="card">
                        <div class="card-body text-center">
                            <button type="button" name="action" id="btnGuardarMantenimiento" class="btn btn-primary btn-lg me-3">
                                <i class="fas fa-save me-2"></i>Guardar Mantenimiento
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
                        <i class="fas fa-question-circle me-2"></i>Ayuda - Formulario de Mantenimiento
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <h6 class="text-primary"><i class="fas fa-wrench me-2"></i>Datos del Mantenimiento</h6>
                            <p><strong>Información básica del servicio realizado.</strong></p>
                            <ul class="list-unstyled ms-3">
                                <li><i class="fas fa-check text-success me-2"></i><strong>Fecha:</strong> Campo obligatorio, fecha del servicio</li>
                                <li><i class="fas fa-check text-success me-2"></i><strong>Tipo:</strong> Campo obligatorio, categoría del mantenimiento</li>
                                <li><i class="fas fa-check text-success me-2"></i><strong>Descripción:</strong> Campo obligatorio, detalle del trabajo</li>
                                <li><i class="fas fa-info text-warning me-2"></i><strong>Kilometraje:</strong> Recomendado para llevar control del vehículo</li>
                                <li><i class="fas fa-info text-info me-2"></i><strong>Costo:</strong> Campo opcional, importe del servicio</li>
                            </ul>
                            <div class="alert alert-warning" role="alert">
                                <h6 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i>Importante: Estado del Vehículo</h6>
                                <p class="mb-0"><strong>Registrar un mantenimiento NO modifica automáticamente el estado del vehículo.</strong> Si a consecuencia de este mantenimiento el vehículo quedara inoperativo o en taller, deberá reflejarlo manualmente en la <strong>ficha general del vehículo</strong> (sección de Furgonetas).</p>
                            </div>
                            <hr>
                        </div>
                        
                        <div class="col-12">
                            <h6 class="text-primary"><i class="fas fa-store me-2"></i>Información del Taller</h6>
                            <p><strong>Datos del establecimiento que realizó el trabajo.</strong></p>
                            <ul class="list-unstyled ms-3">
                                <li><i class="fas fa-info text-info me-2"></i><strong>Número de Factura:</strong> Para referencia contable</li>
                                <li><i class="fas fa-info text-info me-2"></i><strong>Nombre del Taller:</strong> Identificación del establecimiento</li>
                                <li><i class="fas fa-phone text-info me-2"></i><strong>Teléfono:</strong> Contacto del taller</li>
                                <li><i class="fas fa-map-marker text-info me-2"></i><strong>Dirección:</strong> Ubicación del taller</li>
                            </ul>
                            <hr>
                        </div>
                        
                        <div class="col-12">
                            <h6 class="text-primary"><i class="fas fa-clipboard-check me-2"></i>Información ITV</h6>
                            <p><strong>Campos específicos para inspecciones técnicas.</strong></p>
                            <ul class="list-unstyled ms-3">
                                <li><i class="fas fa-check-circle text-success me-2"></i><strong>Resultado:</strong> Favorable, Desfavorable o Negativa</li>
                                <li><i class="fas fa-calendar text-warning me-2"></i><strong>Próxima ITV:</strong> Fecha de la siguiente inspección</li>
                            </ul>
                            <hr>
                        </div>
                        
                        <div class="col-12">
                            <h6 class="text-primary"><i class="fas fa-shield-alt me-2"></i>Garantía</h6>
                            <p><strong>Control de garantías de los trabajos.</strong></p>
                            <ul class="list-unstyled ms-3">
                                <li><i class="fas fa-calendar-check text-success me-2"></i><strong>Garantía Hasta:</strong> Fecha límite de cobertura</li>
                                <li><i class="fas fa-clipboard text-secondary me-2"></i><strong>Observaciones:</strong> Notas adicionales</li>
                            </ul>
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
    <script type="text/javascript" src="formularioMantenimiento.js"></script>

</body>

</html>
