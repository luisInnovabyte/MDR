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
                <a class="breadcrumb-item" href="index.php">Mantenimiento Furgonetas</a>
                <span class="breadcrumb-item active" id="breadcrumb-title">Nueva Furgoneta</span>
            </nav>
        </div><!-- br-pageheader -->
        
        <div class="br-pagetitle">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <h4 class="mb-0 me-2" id="page-title">Nueva Furgoneta</h4>
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
                
                <!-- Formulario de Furgoneta -->
                <form id="formFurgoneta">
                    <!-- Campo oculto para ID de la furgoneta -->
                    <input type="hidden" name="id_furgoneta" id="id_furgoneta">

                    <!-- SECCIÓN 1: Identificación -->
                    <div class="card mb-4 border-primary">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0 tx-bold">
                                <i class="fas fa-id-card me-2"></i>Identificación del Vehículo
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-12 col-md-4">
                                    <label for="matricula_furgoneta" class="form-label">Matrícula: <span class="tx-danger">*</span></label>
                                    <input type="text" class="form-control text-uppercase" name="matricula_furgoneta" id="matricula_furgoneta" maxlength="20" placeholder="Ej: 1234ABC" required>
                                    <div class="invalid-feedback small-invalid-feedback">Ingrese una matrícula válida (máximo 20 caracteres, única)</div>
                                    <small class="form-text text-muted">Matrícula única identificativa</small>
                                </div>
                                <div class="col-12 col-md-4">
                                    <label for="marca_furgoneta" class="form-label">Marca: <span class="tx-danger">*</span></label>
                                    <input type="text" class="form-control" name="marca_furgoneta" id="marca_furgoneta" maxlength="100" placeholder="Ej: Mercedes-Benz, Ford, etc..." required>
                                    <div class="invalid-feedback small-invalid-feedback">Ingrese una marca válida</div>
                                    <small class="form-text text-muted">Marca del fabricante</small>
                                </div>
                                <div class="col-12 col-md-4">
                                    <label for="modelo_furgoneta" class="form-label">Modelo: <span class="tx-danger">*</span></label>
                                    <input type="text" class="form-control" name="modelo_furgoneta" id="modelo_furgoneta" maxlength="100" placeholder="Ej: Sprinter 314, Transit 350, etc..." required>
                                    <div class="invalid-feedback small-invalid-feedback">Ingrese un modelo válido</div>
                                    <small class="form-text text-muted">Modelo del vehículo</small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12 col-md-6">
                                    <label for="anio_furgoneta" class="form-label">Año de fabricación:</label>
                                    <input type="number" class="form-control" name="anio_furgoneta" id="anio_furgoneta" min="1900" max="2099" placeholder="2020">
                                    <small class="form-text text-muted">Año de fabricación del vehículo</small>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="numero_bastidor_furgoneta" class="form-label">Número de Bastidor (VIN):</label>
                                    <input type="text" class="form-control text-uppercase" name="numero_bastidor_furgoneta" id="numero_bastidor_furgoneta" maxlength="50" placeholder="Ej: WVWZZZ1KZ8W123456">
                                    <small class="form-text text-muted">Número de identificación del vehículo (VIN)</small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12 col-md-4">
                                    <label class="form-label">Estado del vehículo:</label>
                                    <div class="form-check form-switch mt-2">
                                        <input class="form-check-input" type="checkbox" name="activo_furgoneta" id="activo_furgoneta" checked disabled>
                                        <label class="form-check-label" for="activo_furgoneta">
                                            <span id="estado-text">Furgoneta Activa</span>
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">El estado se establece automáticamente</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECCIÓN 2: ITV y Seguros -->
                    <div class="card mb-4 border-success">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0 tx-bold">
                                <i class="fas fa-shield-alt me-2"></i>ITV y Seguros
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-12 col-md-6">
                                    <label for="fecha_proxima_itv_furgoneta" class="form-label">Fecha próxima ITV:</label>
                                    <input type="date" class="form-control" name="fecha_proxima_itv_furgoneta" id="fecha_proxima_itv_furgoneta">
                                    <small class="form-text text-muted">Fecha de la próxima inspección técnica</small>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="fecha_vencimiento_seguro_furgoneta" class="form-label">Fecha vencimiento seguro:</label>
                                    <input type="date" class="form-control" name="fecha_vencimiento_seguro_furgoneta" id="fecha_vencimiento_seguro_furgoneta">
                                    <small class="form-text text-muted">Fecha de vencimiento del seguro</small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12 col-md-6">
                                    <label for="compania_seguro_furgoneta" class="form-label">Compañía de seguro:</label>
                                    <input type="text" class="form-control" name="compania_seguro_furgoneta" id="compania_seguro_furgoneta" maxlength="100" placeholder="Ej: AXA, Mapfre, etc...">
                                    <small class="form-text text-muted">Nombre de la compañía aseguradora</small>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="numero_poliza_seguro_furgoneta" class="form-label">Número de póliza:</label>
                                    <input type="text" class="form-control" name="numero_poliza_seguro_furgoneta" id="numero_poliza_seguro_furgoneta" maxlength="50" placeholder="Ej: POL-123456789">
                                    <small class="form-text text-muted">Número de póliza del seguro</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECCIÓN 3: Capacidad y Combustible -->
                    <div class="card mb-4 border-info">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0 tx-bold">
                                <i class="fas fa-weight-hanging me-2"></i>Capacidad y Combustible
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-12 col-md-4">
                                    <label for="capacidad_carga_kg_furgoneta" class="form-label">Capacidad de carga (kg):</label>
                                    <input type="number" class="form-control" name="capacidad_carga_kg_furgoneta" id="capacidad_carga_kg_furgoneta" step="0.01" min="0" placeholder="1000">
                                    <small class="form-text text-muted">Capacidad máxima de carga en kilogramos</small>
                                </div>
                                <div class="col-12 col-md-4">
                                    <label for="capacidad_carga_m3_furgoneta" class="form-label">Capacidad de carga (m³):</label>
                                    <input type="number" class="form-control" name="capacidad_carga_m3_furgoneta" id="capacidad_carga_m3_furgoneta" step="0.01" min="0" placeholder="10">
                                    <small class="form-text text-muted">Capacidad máxima de carga en metros cúbicos</small>
                                </div>
                                <div class="col-12 col-md-4">
                                    <label for="tipo_combustible_furgoneta" class="form-label">Tipo de combustible:</label>
                                    <select class="form-control" name="tipo_combustible_furgoneta" id="tipo_combustible_furgoneta">
                                        <option value="">Seleccionar...</option>
                                        <option value="Diésel">Diésel</option>
                                        <option value="Gasolina">Gasolina</option>
                                        <option value="Eléctrico">Eléctrico</option>
                                        <option value="Híbrido">Híbrido</option>
                                        <option value="GLP">GLP</option>
                                        <option value="GNC">GNC</option>
                                    </select>
                                    <small class="form-text text-muted">Tipo de combustible del vehículo</small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12 col-md-6">
                                    <label for="consumo_medio_furgoneta" class="form-label">Consumo medio (L/100km):</label>
                                    <input type="number" class="form-control" name="consumo_medio_furgoneta" id="consumo_medio_furgoneta" step="0.1" min="0" placeholder="8.5">
                                    <small class="form-text text-muted">Consumo promedio en litros por cada 100 km</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECCIÓN 4: Mantenimiento -->
                    <div class="card mb-4 border-warning">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="mb-0 tx-bold">
                                <i class="fas fa-wrench me-2"></i>Mantenimiento
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-12 col-md-6">
                                    <label for="taller_habitual_furgoneta" class="form-label">Taller habitual:</label>
                                    <input type="text" class="form-control" name="taller_habitual_furgoneta" id="taller_habitual_furgoneta" maxlength="100" placeholder="Ej: Taller García">
                                    <small class="form-text text-muted">Nombre del taller habitual para mantenimiento</small>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="telefono_taller_furgoneta" class="form-label">Teléfono del taller:</label>
                                    <input type="text" class="form-control" name="telefono_taller_furgoneta" id="telefono_taller_furgoneta" maxlength="20" placeholder="Ej: 912345678">
                                    <small class="form-text text-muted">Teléfono de contacto del taller</small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12 col-md-6">
                                    <label for="kilometros_entre_revisiones_furgoneta" class="form-label">Kilómetros entre revisiones:</label>
                                    <input type="number" class="form-control" name="kilometros_entre_revisiones_furgoneta" id="kilometros_entre_revisiones_furgoneta" min="0" placeholder="10000">
                                    <small class="form-text text-muted">Kilómetros entre cada revisión programada</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECCIÓN 5: Estado y Observaciones -->
                    <div class="card mb-4 border-secondary">
                        <div class="card-header bg-secondary text-white">
                            <h5 class="mb-0 tx-bold">
                                <i class="fas fa-info-circle me-2"></i>Estado Operativo y Observaciones
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-12 col-md-6">
                                    <label for="estado_furgoneta" class="form-label">Estado operativo: <span class="tx-danger">*</span></label>
                                    <select class="form-control" name="estado_furgoneta" id="estado_furgoneta" required>
                                        <option value="">Seleccionar estado...</option>
                                        <option value="operativa" selected>Operativa</option>
                                        <option value="taller">En Taller</option>
                                        <option value="averiada">Averiada</option>
                                        <option value="baja">Baja</option>
                                    </select>
                                    <div class="invalid-feedback small-invalid-feedback">Seleccione un estado válido</div>
                                    <small class="form-text text-muted">Estado operativo actual del vehículo</small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12">
                                    <label for="observaciones_furgoneta" class="form-label">Observaciones:</label>
                                    <textarea class="form-control" name="observaciones_furgoneta" id="observaciones_furgoneta" rows="4" maxlength="1000" placeholder="Observaciones o notas sobre la furgoneta..."></textarea>
                                    <small class="form-text text-muted">Observaciones generales sobre la furgoneta (máximo 1000 caracteres)</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botones de acción -->
                    <div class="card">
                        <div class="card-body text-center">
                            <button type="button" name="action" id="btnSalvarFurgoneta" class="btn btn-primary btn-lg me-3">
                                <i class="fas fa-save me-2"></i>Guardar Furgoneta
                            </button>
                            <a href="index.php" class="btn btn-secondary btn-lg">
                                <i class="fas fa-times me-2"></i>Cancelar
                            </a>
                        </div>
                    </div>

                </form>
            </div><!-- br-section-wrapper -->
        </div><!-- br-pagebody -->

        <!-- ---------------------- -->
        <!--      MainFooter.php      -->
        <!-- ---------------------- -->
        <?php include_once('../../config/template/mainFooter.php') ?>
        <!-- ---------------------- -->
        <!--   END MainFooter.php     -->
        <!-- ---------------------- -->

    </div><!-- br-mainpanel -->
    <!-- ########## END: MAIN PANEL ########## -->

    <!-- Modal de ayuda del formulario -->
    <div class="modal fade" id="modalAyudaFormulario" tabindex="-1" aria-labelledby="modalAyudaFormularioLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalAyudaFormularioLabel">
                        <i class="fa fa-question-circle me-2"></i>Ayuda - Formulario de Furgonetas
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h6 class="text-primary"><i class="fa fa-info-circle me-2"></i>Campos Obligatorios</h6>
                    <ul>
                        <li><strong>Matrícula:</strong> Identificador único del vehículo. No puede repetirse.</li>
                        <li><strong>Marca:</strong> Fabricante del vehículo (ej: Mercedes-Benz, Ford, Renault).</li>
                        <li><strong>Modelo:</strong> Modelo específico del vehículo (ej: Sprinter 314, Transit 350).</li>
                        <li><strong>Estado operativo:</strong> Condición actual del vehículo (operativa, taller, averiada, baja).</li>
                    </ul>

                    <hr>

                    <h6 class="text-primary"><i class="fa fa-clipboard-list me-2"></i>Secciones del Formulario</h6>
                    <p><strong>1. Identificación:</strong> Datos básicos del vehículo (matrícula, marca, modelo, año, bastidor).</p>
                    <p><strong>2. ITV y Seguros:</strong> Control de documentación legal (fechas ITV, seguro, póliza, compañía).</p>
                    <p><strong>3. Capacidad y Combustible:</strong> Especificaciones técnicas (capacidad de carga, tipo de combustible, consumo).</p>
                    <p><strong>4. Mantenimiento:</strong> Información del taller habitual y frecuencia de revisiones.</p>
                    <p><strong>5. Estado y Observaciones:</strong> Estado operativo actual y notas adicionales.</p>

                    <hr>

                    <h6 class="text-primary"><i class="fa fa-lightbulb me-2"></i>Consejos</h6>
                    <ul>
                        <li>La matrícula debe ser única en el sistema.</li>
                        <li>Configure el intervalo de km entre revisiones para recibir alertas automáticas.</li>
                        <li>Mantenga actualizadas las fechas de ITV y seguro para evitar sanciones.</li>
                        <li>Use el campo observaciones para notas importantes sobre el vehículo.</li>
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ---------------------- -->
    <!--       mainJs.php        -->
    <!-- ---------------------- -->
    <?php include_once('../../config/template/mainJs.php') ?>
    <!-- ---------------------- -->
    <!--     END mainJs.php        -->
    <!-- ---------------------- -->

    <!-- Script específico del formulario -->
    <script src="formularioFurgoneta.js?v=<?php echo time(); ?>"></script>

</body>
</html>
