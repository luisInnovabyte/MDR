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
                <a class="breadcrumb-item" href="index.html">Dashboard</a>
                <a class="breadcrumb-item" href="index.php">Mantenimiento Observaciones</a>
                <span class="breadcrumb-item active" id="breadcrumb-title">Nueva Observación</span>
            </nav>
        </div><!-- br-pageheader -->
        
        <div class="br-pagetitle">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <h4 class="mb-0 me-2" id="page-title">Nueva Observación</h4>
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
                
                <!-- Formulario de Observación -->
                <form id="formObservacion">
                    <!-- Campo oculto para ID de la observación -->
                    <input type="hidden" name="id_obs_general" id="id_obs_general">

                    <!-- SECCIÓN: Información Básica -->
                    <div class="card mb-4 border-primary">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0 tx-bold">
                                <i class="fas fa-info-circle me-2"></i>Información Básica de la Observación
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-12 col-md-6">
                                    <label for="codigo_obs_general" class="form-label">Código: <span class="tx-danger">*</span></label>
                                    <input type="text" class="form-control" name="codigo_obs_general" id="codigo_obs_general" maxlength="20" placeholder="Ej: OBS-001" required>
                                    <div class="invalid-feedback small-invalid-feedback">Ingrese un código válido (máximo 20 caracteres, único)</div>
                                    <small class="form-text text-muted">Código único identificativo de la observación (máximo 20 caracteres)</small>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label">Estado:</label>
                                    <div class="form-check form-switch mt-2">
                                        <input class="form-check-input" type="checkbox" name="activo_obs_general" id="activo_obs_general" checked disabled>
                                        <label class="form-check-label" for="activo_obs_general">
                                            <span id="estado-text">Observación Activa</span>
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">El estado se establece automáticamente</small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12 col-md-6">
                                    <label for="titulo_obs_general" class="form-label">Título (Español): <span class="tx-danger">*</span></label>
                                    <input type="text" class="form-control" name="titulo_obs_general" id="titulo_obs_general" maxlength="100" placeholder="Título de la observación" required>
                                    <div class="invalid-feedback small-invalid-feedback">Ingrese un título válido (máximo 100 caracteres)</div>
                                    <small class="form-text text-muted">Título descriptivo de la observación (máximo 100 caracteres)</small>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="title_obs_general" class="form-label">Title (English):</label>
                                    <input type="text" class="form-control" name="title_obs_general" id="title_obs_general" maxlength="100" placeholder="Observation title">
                                    <div class="invalid-feedback small-invalid-feedback">Ingrese un título válido (máximo 100 caracteres)</div>
                                    <small class="form-text text-muted">Título en inglés (opcional, máximo 100 caracteres)</small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12 col-md-6">
                                    <label for="texto_obs_general" class="form-label">Texto (Español): <span class="tx-danger">*</span></label>
                                    <textarea class="form-control" name="texto_obs_general" id="texto_obs_general" rows="5" placeholder="Texto de la observación..." required></textarea>
                                    <div class="invalid-feedback small-invalid-feedback">El texto es obligatorio</div>
                                    <small class="form-text text-muted">Texto completo de la observación</small>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="text_obs_general" class="form-label">Text (English):</label>
                                    <textarea class="form-control" name="text_obs_general" id="text_obs_general" rows="5" placeholder="Observation text..."></textarea>
                                    <div class="invalid-feedback small-invalid-feedback">Texto opcional</div>
                                    <small class="form-text text-muted">Texto en inglés (opcional)</small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12 col-md-4">
                                    <label for="orden_obs_general" class="form-label">Orden:</label>
                                    <input type="number" class="form-control" name="orden_obs_general" id="orden_obs_general" min="0" value="0" placeholder="0">
                                    <small class="form-text text-muted">Orden de visualización (0 por defecto)</small>
                                </div>
                                <div class="col-12 col-md-4">
                                    <label for="tipo_obs_general" class="form-label">Tipo:</label>
                                    <select class="form-control" name="tipo_obs_general" id="tipo_obs_general">
                                        <option value="condiciones">Condiciones</option>
                                        <option value="tecnicas">Técnicas</option>
                                        <option value="legales">Legales</option>
                                        <option value="comerciales">Comerciales</option>
                                        <option value="otras" selected>Otras</option>
                                    </select>
                                    <small class="form-text text-muted">Tipo de observación</small>
                                </div>
                                <div class="col-12 col-md-4">
                                    <label for="obligatoria_obs_general" class="form-label">Obligatoria:</label>
                                    <div class="form-check form-switch mt-2">
                                        <input class="form-check-input" type="checkbox" name="obligatoria_obs_general" id="obligatoria_obs_general" checked>
                                        <label class="form-check-label" for="obligatoria_obs_general">
                                            <span id="obligatoria-text">Sí</span>
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">Aparece siempre en presupuestos</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botones de acción -->
                    <div class="card">
                        <div class="card-body text-center">
                            <button type="button" name="action" id="btnSalvarObservacion" class="btn btn-primary btn-lg me-3">
                                <i class="fas fa-save me-2"></i>Guardar Observación
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
                        <i class="fas fa-question-circle me-2"></i>Ayuda - Formulario de Observaciones
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <h6 class="text-primary"><i class="fas fa-tag me-2"></i>Código de Observación</h6>
                            <p><strong>Campo obligatorio.</strong> Identificador único de la observación.</p>
                            <ul class="list-unstyled ms-3">
                                <li><i class="fas fa-check text-success me-2"></i>Máximo 20 caracteres</li>
                                <li><i class="fas fa-check text-success me-2"></i>Debe ser único en el sistema</li>
                                <li><i class="fas fa-info text-info me-2"></i>Ejemplos: OBS-001, COND-001, TEC-001</li>
                            </ul>
                            <hr>
                        </div>
                        
                        <div class="col-12">
                            <h6 class="text-primary"><i class="fas fa-heading me-2"></i>Título (Español / English)</h6>
                            <p><strong>Campo obligatorio (español).</strong> Título descriptivo de la observación en ambos idiomas.</p>
                            <ul class="list-unstyled ms-3">
                                <li><i class="fas fa-check text-success me-2"></i>Título español: obligatorio, máximo 100 caracteres</li>
                                <li><i class="fas fa-info text-info me-2"></i>Title inglés: opcional, máximo 100 caracteres</li>
                                <li><i class="fas fa-info text-info me-2"></i>Debe ser claro y descriptivo en ambos idiomas</li>
                            </ul>
                            <hr>
                        </div>
                        
                        <div class="col-12">
                            <h6 class="text-primary"><i class="fas fa-file-alt me-2"></i>Texto (Español / English)</h6>
                            <p><strong>Campo obligatorio (español).</strong> Contenido completo de la observación en ambos idiomas.</p>
                            <ul class="list-unstyled ms-3">
                                <li><i class="fas fa-check text-success me-2"></i>Texto español: obligatorio, contenido completo</li>
                                <li><i class="fas fa-info text-info me-2"></i>Text inglés: opcional, traducción del contenido</li>
                                <li><i class="fas fa-info text-info me-2"></i>Útil para documentos internacionales</li>
                            </ul>
                            <hr>
                        </div>
                        
                        <div class="col-12">
                            <h6 class="text-primary"><i class="fas fa-cog me-2"></i>Configuración Adicional</h6>
                            <p><strong>Información complementaria.</strong> Otros campos de la observación.</p>
                            <ul class="list-unstyled ms-3">
                                <li><i class="fas fa-info text-info me-2"></i>Orden: número para ordenar visualización (0 por defecto)</li>
                                <li><i class="fas fa-info text-info me-2"></i>Tipo: categoría de la observación</li>
                                <li><i class="fas fa-info text-info me-2"></i>Obligatoria: si aparece siempre en presupuestos</li>
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
    <script type="text/javascript" src="formularioObservaciones.js"></script>

</body>

</html>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Obtener parámetros URL para determinar si es edición
    const urlParams = new URLSearchParams(window.location.search);
    const idObsGeneral = urlParams.get('id');
    const modo = urlParams.get('modo') || 'nuevo';
    
    // Configurar página según el modo
    if (modo === 'editar' && idObsGeneral) {
        document.getElementById('page-title').textContent = 'Editar Observación';
        document.getElementById('breadcrumb-title').textContent = 'Editar Observación';
        document.getElementById('btnSalvarObservacion').innerHTML = '<i class="fas fa-save me-2"></i>Actualizar Observación';
        document.getElementById('id_obs_general').value = idObsGeneral;
        
        // Cargar datos de la observación para edición
        cargarDatosObservacion(idObsGeneral);
    }
    
    // Validación en tiempo real para código observación
    const codigoInput = document.getElementById('codigo_obs_general');
    if (codigoInput) {
        codigoInput.addEventListener('input', function() {
            // Convertir a mayúsculas
            this.value = this.value.toUpperCase();
            
            // Validar longitud
            if (this.value.length > 20) {
                this.classList.add('is-invalid');
            } else if (this.value.length >= 2) {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            } else {
                this.classList.remove('is-valid', 'is-invalid');
            }
        });
    }
    
    // Switch de obligatoria
    const obligatoriaSwitch = document.getElementById('obligatoria_obs_general');
    const obligatoriaText = document.getElementById('obligatoria-text');
    if (obligatoriaSwitch && obligatoriaText) {
        obligatoriaSwitch.addEventListener('change', function() {
            obligatoriaText.textContent = this.checked ? 'Sí' : 'No';
        });
    }
});

// Función para cargar datos de observación en modo edición
function cargarDatosObservacion(idObsGeneral) {
    // Esta función se implementará en el archivo formularioObservaciones.js
    console.log('Cargando datos de observación ID:', idObsGeneral);
}
</script>
