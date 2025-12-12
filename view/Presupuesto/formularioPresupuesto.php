<!-- ---------------------- -->
<!--   Comprobar permisos     -->
<!-- ---------------------- -->
<?php $moduloActual = 'mantenimientos'; ?>
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
                <a class="breadcrumb-item" href="index.php">Mantenimiento Presupuestos</a>
                <span class="breadcrumb-item active" id="breadcrumb-title">Nuevo Presupuesto</span>
            </nav>
        </div><!-- br-pageheader -->
        
        <div class="br-pagetitle">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <h4 class="mb-0 me-2" id="page-title">Nuevo Presupuesto</h4>
                    <button type="button" class="btn btn-link p-0 ms-1" data-bs-toggle="modal" data-bs-target="#modalAyudaFormulario" title="Ayuda sobre el formulario">
                        <i class="bi bi-question-circle text-primary" style="font-size: 1.3rem;"></i>
                    </button>
                </div>
                
                <!-- Botón de regreso -->
                <a href="index.php" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Volver al listado
                </a>
            </div>
            <br>
        </div><!-- br-pagetitle -->

        <div class="br-pagebody">
            <div class="br-section-wrapper">
                
                <!-- Formulario de Presupuesto -->
                <form id="formPresupuesto">
                    <!-- Campo oculto para ID del presupuesto -->
                    <input type="hidden" name="id_presupuesto" id="id_presupuesto">

                    <!-- SECCIÓN: Información Básica -->
                    <div class="card mb-4 border-primary">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0 tx-bold">
                                <i class="fas fa-file-invoice me-2"></i>Información Básica del Presupuesto
                            </h5>
                        </div>
                        <div class="card-body">
                            
                          <!-- Información de la Empresa -->
                            <div class="row mb-3">
                                <div class="col-12 col-md-8">
                                    <label for="nombre_empresa_info" class="form-label">
                                        Empresa emisora del presupuesto:
                                        <i class="bi bi-info-circle text-info" data-bs-toggle="tooltip" title="Empresa ficticia que emite este presupuesto. Solo informativo."></i>
                                    </label>
                                    <input type="text" class="form-control bg-light" id="nombre_empresa_info" placeholder="Cargando..." readonly>
                                    <small class="form-text text-muted">Empresa configurada para emisión de presupuestos</small>
                                </div>
                                <div class="col-12 col-md-4">
                                    <label for="dias_validez_info" class="form-label">
                                        Días de validez configurados:
                                        <i class="bi bi-info-circle text-info" data-bs-toggle="tooltip" title="Días de validez por defecto configurados en la empresa. Solo informativo."></i>
                                    </label>
                                    <div class="input-group">
                                        <input type="text" class="form-control bg-light text-center" id="dias_validez_info" placeholder="--" readonly>
                                        <span class="input-group-text bg-light">días</span>
                                    </div>
                                    <small class="form-text text-muted">Configurado en la empresa</small>
                                </div>
                            </div>
                        
                            <div class="row mb-3">
                                <div class="col-12 col-md-3">
                                    <label for="numero_presupuesto" class="form-label">Número: <span class="tx-danger">*</span></label>
                                    <input type="text" class="form-control" name="numero_presupuesto" id="numero_presupuesto" placeholder="Se generará automáticamente" readonly>
                                    <small class="form-text text-muted">Se asigna automáticamente al guardar</small>
                                </div>
                                <div class="col-12 col-md-3">
                                    <label for="fecha_presupuesto" class="form-label">Fecha: <span class="tx-danger">*</span></label>
                                    <input type="date" class="form-control" name="fecha_presupuesto" id="fecha_presupuesto" required>
                                    <div class="invalid-feedback small-invalid-feedback">Ingrese una fecha válida</div>
                                </div>
                                <div class="col-12 col-md-3">
                                    <label for="fecha_validez_presupuesto" class="form-label">
                                        Validez:
                                        <i class="bi bi-info-circle text-info" data-bs-toggle="tooltip" title="Se calcula automáticamente según los días de validez configurados en la empresa. Puede modificarse."></i>
                                    </label>
                                    <input type="date" class="form-control" name="fecha_validez_presupuesto" id="fecha_validez_presupuesto">
                                    <div class="invalid-feedback small-invalid-feedback">Debe ser mayor o igual a la fecha del presupuesto</div>
                                    <small class="form-text text-muted">Fecha hasta la que es válido el presupuesto (modificable)</small>
                                </div>
                                <div class="col-12 col-md-3">
                                    <label class="form-label">Estado:</label>
                                    <div class="form-check form-switch mt-2">
                                        <input class="form-check-input" type="checkbox" name="activo_presupuesto" id="activo_presupuesto" checked disabled>
                                        <label class="form-check-label" for="activo_presupuesto">
                                            <span id="estado-text">Activo</span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                          
                            <div class="row mb-3">
                                <div class="col-12 col-md-6">
                                    <label for="id_cliente" class="form-label">Cliente: <span class="tx-danger">*</span></label>
                                    <select class="form-control" name="id_cliente" id="id_cliente" required>
                                        <option value="">Seleccione un cliente</option>
                                    </select>
                                    <div class="invalid-feedback small-invalid-feedback">Debe seleccionar un cliente</div>
                                    <small class="form-text text-muted">Cliente al que se emite el presupuesto</small>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="id_contacto_cliente" class="form-label">Contacto del cliente:</label>
                                    <select class="form-control" name="id_contacto_cliente" id="id_contacto_cliente">
                                        <option value="">Seleccione primero un cliente</option>
                                    </select>
                                    <small class="form-text text-muted">Contacto específico del cliente (opcional)</small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12 col-md-6">
                                    <label for="id_estado_ppto" class="form-label">Estado presupuesto: <span class="tx-danger">*</span></label>
                                    <select class="form-control" name="id_estado_ppto" id="id_estado_ppto" required>
                                        <option value="">Seleccione un estado</option>
                                    </select>
                                    <div class="invalid-feedback small-invalid-feedback">Debe seleccionar un estado</div>
                                    <small class="form-text text-muted">Estado actual del presupuesto</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECCIÓN: Datos del Evento -->
                    <div class="card mb-4 border-info">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0 tx-bold">
                                <i class="fas fa-calendar-event me-2"></i>Datos del Evento
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-12">
                                    <label for="nombre_evento_presupuesto" class="form-label">Nombre del evento:</label>
                                    <input type="text" class="form-control" name="nombre_evento_presupuesto" id="nombre_evento_presupuesto" maxlength="255" placeholder="Ej: Concierto Anual 2025">
                                    <small class="form-text text-muted">Nombre del evento o proyecto</small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12 col-md-6">
                                    <label for="direccion_evento_presupuesto" class="form-label">Dirección del evento:</label>
                                    <input type="text" class="form-control" name="direccion_evento_presupuesto" id="direccion_evento_presupuesto" maxlength="100" placeholder="Ej: Calle Principal 123">
                                    <small class="form-text text-muted">Dirección donde se realizará el evento</small>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="poblacion_evento_presupuesto" class="form-label">Población:</label>
                                    <input type="text" class="form-control" name="poblacion_evento_presupuesto" id="poblacion_evento_presupuesto" maxlength="80" placeholder="Ej: Badajoz">
                                    <small class="form-text text-muted">Ciudad o población del evento</small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12 col-md-6">
                                    <label for="cp_evento_presupuesto" class="form-label">Código postal:</label>
                                    <input type="text" class="form-control" name="cp_evento_presupuesto" id="cp_evento_presupuesto" maxlength="10" placeholder="Ej: 06006">
                                    <small class="form-text text-muted">CP del evento</small>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="provincia_evento_presupuesto" class="form-label">Provincia:</label>
                                    <input type="text" class="form-control" name="provincia_evento_presupuesto" id="provincia_evento_presupuesto" maxlength="80" placeholder="Ej: Badajoz">
                                    <small class="form-text text-muted">Provincia del evento</small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12 col-md-6">
                                    <label for="fecha_inicio_evento_presupuesto" class="form-label">Fecha inicio evento:</label>
                                    <input type="date" class="form-control" name="fecha_inicio_evento_presupuesto" id="fecha_inicio_evento_presupuesto">
                                    <small class="form-text text-muted">Fecha de inicio del evento/servicio</small>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="fecha_fin_evento_presupuesto" class="form-label">Fecha fin evento:</label>
                                    <input type="date" class="form-control" name="fecha_fin_evento_presupuesto" id="fecha_fin_evento_presupuesto">
                                    <div class="invalid-feedback small-invalid-feedback">Debe ser mayor o igual a la fecha de inicio del evento</div>
                                    <small class="form-text text-muted">Fecha de finalización del evento/servicio</small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12">
                                    <label for="numero_pedido_cliente_presupuesto" class="form-label">Número de pedido del cliente:</label>
                                    <input type="text" class="form-control" name="numero_pedido_cliente_presupuesto" id="numero_pedido_cliente_presupuesto" maxlength="80" placeholder="Ej: PED-2025-001">
                                    <small class="form-text text-muted">Número de pedido proporcionado por el cliente (si aplica)</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECCIÓN: Observaciones -->
                    <div class="card mb-4 border-secondary">
                        <div class="card-header bg-secondary text-white">
                            <h5 class="mb-0 tx-bold">
                                <i class="fas fa-comment-alt me-2"></i>Observaciones
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-12">
                                    <label for="observaciones_cabecera_presupuesto" class="form-label">Observaciones de cabecera:</label>
                                    <textarea class="form-control" name="observaciones_cabecera_presupuesto" id="observaciones_cabecera_presupuesto" rows="3" placeholder="Observaciones que aparecerán al inicio del presupuesto..."></textarea>
                                    <small class="form-text text-muted">Observaciones iniciales del presupuesto</small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12">
                                    <label for="observaciones_pie_presupuesto" class="form-label">Observaciones de pie:</label>
                                    <textarea class="form-control" name="observaciones_pie_presupuesto" id="observaciones_pie_presupuesto" rows="3" placeholder="Observaciones que aparecerán al final del presupuesto..."></textarea>
                                    <small class="form-text text-muted">Observaciones específicas adicionales al pie</small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12">
                                    <label for="observaciones_internas_presupuesto" class="form-label">Observaciones internas:</label>
                                    <textarea class="form-control" name="observaciones_internas_presupuesto" id="observaciones_internas_presupuesto" rows="3" placeholder="Notas internas (no se imprimirán en el PDF)..."></textarea>
                                    <small class="form-text text-muted">Notas internas, no se imprimen en el PDF</small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12 col-md-6">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="mostrar_obs_familias_presupuesto" id="mostrar_obs_familias_presupuesto" checked>
                                        <label class="form-check-label" for="mostrar_obs_familias_presupuesto">
                                            Mostrar observaciones de familias
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">Si está activo, muestra observaciones de las familias usadas</small>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="mostrar_obs_articulos_presupuesto" id="mostrar_obs_articulos_presupuesto" checked>
                                        <label class="form-check-label" for="mostrar_obs_articulos_presupuesto">
                                            Mostrar observaciones de artículos
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">Si está activo, muestra observaciones de los artículos usados</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botones de acción -->
                    <div class="card">
                        <div class="card-body text-center">
                            <button type="button" name="action" id="btnSalvarPresupuesto" class="btn btn-primary btn-lg me-3">
                                <i class="fas fa-save me-2"></i>Guardar Presupuesto
                            </button>
                            <a href="index.php" class="btn btn-secondary btn-lg">
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
                        <i class="fas fa-question-circle me-2"></i>Ayuda - Formulario de Presupuestos
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <h6 class="text-primary"><i class="fas fa-file-invoice me-2"></i>Información Básica</h6>
                            <p><strong>Campos obligatorios:</strong> Número de presupuesto, fecha, cliente y estado.</p>
                            <ul class="list-unstyled ms-3">
                                <li><i class="fas fa-check text-success me-2"></i>Número de presupuesto: único en el sistema</li>
                                <li><i class="fas fa-check text-success me-2"></i>Fecha: fecha de emisión del presupuesto</li>
                                <li><i class="fas fa-check text-success me-2"></i>Cliente: obligatorio seleccionar uno</li>
                                <li><i class="fas fa-info text-info me-2"></i>Contacto del cliente: se carga automáticamente al seleccionar el cliente (opcional)</li>
                                <li><i class="fas fa-check text-success me-2"></i>Estado: define el estado actual del presupuesto</li>
                            </ul>
                            <hr>
                        </div>
                        
                        <div class="col-12">
                            <h6 class="text-primary"><i class="fas fa-calendar-event me-2"></i>Datos del Evento</h6>
                            <p><strong>Información del evento.</strong> Campos opcionales para detallar el evento.</p>
                            <ul class="list-unstyled ms-3">
                                <li><i class="fas fa-info text-info me-2"></i>Nombre y lugar del evento son opcionales</li>
                                <li><i class="fas fa-info text-info me-2"></i>Las fechas de inicio y fin definen la duración del evento</li>
                                <li><i class="fas fa-info text-info me-2"></i>Número de pedido del cliente es referencia externa</li>
                            </ul>
                            <hr>
                        </div>
                        
                        <div class="col-12">
                            <h6 class="text-primary"><i class="fas fa-comment-alt me-2"></i>Observaciones</h6>
                            <p><strong>Notas y comentarios.</strong> Información adicional del presupuesto.</p>
                            <ul class="list-unstyled ms-3">
                                <li><i class="fas fa-info text-info me-2"></i>Observaciones de cabecera: aparecen al inicio del PDF</li>
                                <li><i class="fas fa-info text-info me-2"></i>Observaciones de pie: aparecen al final del PDF</li>
                                <li><i class="fas fa-lock text-warning me-2"></i>Observaciones internas: no se imprimen, solo para uso interno</li>
                                <li><i class="fas fa-check text-success me-2"></i>Los switches controlan la visualización de observaciones de familias y artículos</li>
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
    <script type="text/javascript" src="formularioPresupuesto.js"></script>

</body>

</html>
