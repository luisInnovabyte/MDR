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
                <a class="breadcrumb-item" href="index.html">Dashboard</a>
                <a class="breadcrumb-item" href="index.php">Mantenimiento Coeficientes Reductores</a>
                <span class="breadcrumb-item active" id="breadcrumb-title">Nuevo Coeficiente Reductor</span>
            </nav>
        </div><!-- br-pageheader -->
        
        <div class="br-pagetitle">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <h4 class="mb-0 me-2" id="page-title">Nuevo Coeficiente Reductor</h4>
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
                
                <!-- Formulario de Coeficiente -->
                <form id="formCoeficiente">
                    <!-- Campo oculto para ID del coeficiente -->
                    <input type="hidden" name="id_coeficiente" id="id_coeficiente">

                    <!-- SECCIÓN: Información Básica -->
                    <div class="card mb-4 border-primary">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0 tx-bold">
                                <i class="fas fa-calculator me-2"></i>Información Básica del Coeficiente Reductor
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-12 col-md-6">
                                    <label for="jornadas_coeficiente" class="form-label">Jornadas alquiladas: <span class="tx-danger">*</span></label>
                                    <input type="number" class="form-control" name="jornadas_coeficiente" id="jornadas_coeficiente" min="1" max="9999" placeholder="Ej: 1, 5, 10, 20..." required>
                                    <div class="invalid-feedback small-invalid-feedback">Ingrese un número de jornadas válido (1-9999, único)</div>
                                    <small class="form-text text-muted">Número de días que se alquila el equipo (único en el sistema)</small>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label">Estado:</label>
                                    <div class="form-check form-switch mt-2">
                                        <input class="form-check-input" type="checkbox" name="activo_coeficiente" id="activo_coeficiente" checked disabled>
                                        <label class="form-check-label" for="activo_coeficiente">
                                            <span id="estado-text">Coeficiente Activo</span>
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">El estado se establece automáticamente</small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12 col-md-6">
                                    <label for="valor_coeficiente" class="form-label">Días a facturar: <span class="tx-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" name="valor_coeficiente" id="valor_coeficiente" min="0" max="9999.99" step="0.01" placeholder="Ej: 8.50, 4.75, 9.25..." required>
                                        <span class="input-group-text">
                                            <i class="fas fa-calendar-day"></i>
                                        </span>
                                    </div>
                                    <div class="invalid-feedback small-invalid-feedback">Ingrese un valor válido (0-9999.99, hasta 2 decimales)</div>
                                    <small class="form-text text-muted">Días que se facturarán realmente (puede incluir decimales)</small>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label">Vista previa del coeficiente:</label>
                                    <div class="form-control-plaintext border rounded p-3 bg-light">
                                        <div id="preview-coeficiente" class="text-center">
                                            <span class="badge bg-primary fs-6 me-2">-</span>
                                            <span class="badge bg-success fs-6">-</span>
                                            <br><small class="text-muted">Configurar jornadas y valor</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12">
                                    <label for="observaciones_coeficiente" class="form-label">Observaciones:</label>
                                    <textarea class="form-control" name="observaciones_coeficiente" id="observaciones_coeficiente" maxlength="500" rows="4" placeholder="Observaciones adicionales sobre este coeficiente reductor..."></textarea>
                                    <div class="invalid-feedback small-invalid-feedback">Las observaciones no pueden exceder los 500 caracteres</div>
                                    <small class="form-text text-muted">
                                        Observaciones adicionales (máximo 500 caracteres)
                                        <span class="float-end">
                                            <span id="char-count">0</span>/500 caracteres
                                        </span>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

        

                    <!-- Botones de acción -->
                    <div class="card">
                        <div class="card-body text-center">
                            <button type="button" name="action" id="btnSalvarCoeficiente" class="btn btn-primary btn-lg me-3">
                                <i class="fas fa-save me-2"></i>Guardar Coeficiente
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
                        <i class="fas fa-question-circle me-2"></i>Ayuda - Formulario de Coeficientes Reductores
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <h6 class="text-primary"><i class="fas fa-calendar-week me-2"></i>Número de Jornadas</h6>
                            <p><strong>Campo obligatorio.</strong> Número de jornadas para las que se aplicará este coeficiente.</p>
                            <ul class="list-unstyled ms-3">
                                <li><i class="fas fa-check text-success me-2"></i>Valor entre 1 y 9999</li>
                                <li><i class="fas fa-check text-success me-2"></i>Debe ser único en el sistema</li>
                                <li><i class="fas fa-check text-success me-2"></i>Solo números enteros</li>
                                <li><i class="fas fa-lightbulb text-warning me-2"></i>Ejemplos: 1, 5, 10, 20, 30 jornadas</li>
                                <li><i class="fas fa-info-circle text-info me-2"></i>Representa el número de días trabajados</li>
                            </ul>
                            <hr>
                        </div>
                        
                        <div class="col-12">
                            <h6 class="text-primary"><i class="fas fa-percent me-2"></i>Días a Facturar</h6>
                            <p><strong>Campo obligatorio.</strong> Número de días que se facturarán realmente.</p>
                            <ul class="list-unstyled ms-3">
                                <li><i class="fas fa-check text-success me-2"></i>Valor entre 0 y las jornadas alquiladas</li>
                                <li><i class="fas fa-check text-success me-2"></i>Hasta 2 decimales de precisión</li>
                                <li><i class="fas fa-lightbulb text-warning me-2"></i>Ejemplos: 10 jornadas → 8.50 días facturados (descuento 1.5 días)</li>
                                <li><i class="fas fa-calculator text-info me-2"></i>Descuento automático: jornadas alquiladas - días facturados</li>
                                <li><i class="fas fa-info-circle text-info me-2"></i>Valores menores = mayor descuento por volumen</li>
                            </ul>
                            <hr>
                        </div>
                        
                        <div class="col-12">
                            <h6 class="text-primary"><i class="fas fa-chart-line me-2"></i>Funcionamiento del Sistema</h6>
                            <p><strong>Explicación de uso.</strong> Cómo funcionan los coeficientes de días facturados.</p>
                            <ul class="list-unstyled ms-3">
                                <li><i class="fas fa-arrow-right text-info me-2"></i>Se utilizan para aplicar descuentos por volumen de días alquilados</li>
                                <li><i class="fas fa-arrow-right text-info me-2"></i>Fórmula: Días Facturados × Precio Diario = Total a Cobrar</li>
                                <li><i class="fas fa-example text-warning me-2"></i>Ejemplo: 10 días alquilados, 8.50 días facturados = Descuento de 1.5 días</li>
                                <li><i class="fas fa-example text-warning me-2"></i>Ejemplo: 5 días alquilados, 4.75 días facturados = Descuento de 0.25 días</li>
                                <li><i class="fas fa-cog text-secondary me-2"></i>Cada número de jornadas puede tener sus propios días facturados</li>
                            </ul>
                            <hr>
                        </div>
                        
                        <div class="col-12">
                            <h6 class="text-primary"><i class="fas fa-file-alt me-2"></i>Observaciones y Estado</h6>
                            <p><strong>Información adicional.</strong> Campos complementarios del coeficiente.</p>
                            <ul class="list-unstyled ms-3">
                                <li><i class="fas fa-note-sticky text-info me-2"></i>Observaciones: campo opcional, máximo 500 caracteres</li>
                                <li><i class="fas fa-info text-info me-2"></i>Use las observaciones para explicar cuándo aplicar este coeficiente</li>
                                <li><i class="fas fa-toggle-on text-success me-2"></i>Estado: se establece automáticamente como activo para nuevos coeficientes</li>
                                <li><i class="fas fa-eye text-secondary me-2"></i>El resumen de configuración se actualiza automáticamente</li>
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
    <script type="text/javascript" src="formularioCoeficiente.js"></script>

</body>

</html>