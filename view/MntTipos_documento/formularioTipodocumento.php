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
                <a class="breadcrumb-item" href="index.php">Mantenimiento Tipos de Documento</a>
                <span class="breadcrumb-item active" id="breadcrumb-title">Nuevo Tipo de Documento</span>
            </nav>
        </div><!-- br-pageheader -->
        
        <div class="br-pagetitle">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <h4 class="mb-0 me-2" id="page-title">Nuevo Tipo de Documento</h4>
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
                
                <!-- Formulario de Tipo de Documento -->
                <form id="formTipodocumento">
                    <!-- Campo oculto para ID del tipo de documento -->
                    <input type="hidden" name="id_tipo_documento" id="id_tipo_documento">

                    <!-- SECCIÓN: Información Básica -->
                    <div class="card mb-4 border-primary">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0 tx-bold">
                                <i class="fas fa-file-alt me-2"></i>Información Básica del Tipo de Documento
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-12 col-md-6">
                                    <label for="codigo_tipo_documento" class="form-label">Código: <span class="tx-danger">*</span></label>
                                    <input type="text" class="form-control" name="codigo_tipo_documento" id="codigo_tipo_documento" maxlength="20" placeholder="Ej: SEG, MAN, PROC..." required>
                                    <div class="invalid-feedback small-invalid-feedback">Ingrese un código válido (máximo 20 caracteres, único)</div>
                                    <small class="form-text text-muted">Código único identificativo del tipo de documento (máximo 20 caracteres)</small>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label">Estado:</label>
                                    <div class="form-check form-switch mt-2">
                                        <input class="form-check-input" type="checkbox" name="activo_tipo_documento" id="activo_tipo_documento" checked disabled>
                                        <label class="form-check-label" for="activo_tipo_documento">
                                            <span id="estado-text">Tipo de Documento Activo</span>
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">El estado se establece automáticamente</small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12">
                                    <label for="nombre_tipo_documento" class="form-label">Nombre: <span class="tx-danger">*</span></label>
                                    <input type="text" class="form-control" name="nombre_tipo_documento" id="nombre_tipo_documento" maxlength="100" placeholder="Ej: Seguro, Manual, Procedimiento..." required>
                                    <div class="invalid-feedback small-invalid-feedback">Ingrese un nombre válido (máximo 100 caracteres)</div>
                                    <small class="form-text text-muted">Nombre descriptivo del tipo de documento (máximo 100 caracteres)</small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12">
                                    <label for="descripcion_tipo_documento" class="form-label">Descripción:</label>
                                    <textarea class="form-control" name="descripcion_tipo_documento" id="descripcion_tipo_documento" rows="3" placeholder="Descripción detallada del tipo de documento..."></textarea>
                                    <div class="invalid-feedback small-invalid-feedback">La descripción es opcional</div>
                                    <small class="form-text text-muted">
                                        Descripción opcional del tipo de documento
                                        <span class="float-end">
                                            <span id="char-count">0</span> caracteres
                                        </span>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botones de acción -->
                    <div class="card">
                        <div class="card-body text-center">
                            <button type="button" name="action" id="btnSalvarTipodocumento" class="btn btn-primary btn-lg me-3">
                                <i class="fas fa-save me-2"></i>Guardar Tipo de Documento
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
                        <i class="fas fa-question-circle me-2"></i>Ayuda - Formulario de Tipos de Documento
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <h6 class="text-primary"><i class="fas fa-tag me-2"></i>Código del Tipo de Documento</h6>
                            <p><strong>Campo obligatorio.</strong> Identificador único del tipo de documento.</p>
                            <ul class="list-unstyled ms-3">
                                <li><i class="fas fa-check text-success me-2"></i>Máximo 20 caracteres</li>
                                <li><i class="fas fa-check text-success me-2"></i>Debe ser único en el sistema</li>
                                <li><i class="fas fa-info text-info me-2"></i>Ejemplos: SEG, MAN, PROC, CERT</li>
                            </ul>
                            <hr>
                        </div>
                        
                        <div class="col-12">
                            <h6 class="text-primary"><i class="fas fa-heading me-2"></i>Nombre del Tipo de Documento</h6>
                            <p><strong>Campo obligatorio.</strong> Nombre descriptivo del tipo de documento.</p>
                            <ul class="list-unstyled ms-3">
                                <li><i class="fas fa-check text-success me-2"></i>Máximo 100 caracteres</li>
                                <li><i class="fas fa-info text-info me-2"></i>Ejemplos: Seguro, Manual, Procedimiento, Certificado</li>
                            </ul>
                            <hr>
                        </div>
                        
                        <div class="col-12">
                            <h6 class="text-primary"><i class="fas fa-file-alt me-2"></i>Descripción y Estado</h6>
                            <p><strong>Información adicional.</strong> Campos complementarios del tipo de documento.</p>
                            <ul class="list-unstyled ms-3">
                                <li><i class="fas fa-info text-info me-2"></i>Descripción: campo opcional para detalles adicionales</li>
                                <li><i class="fas fa-lock text-warning me-2"></i>Estado: se establece automáticamente (activo para nuevos tipos)</li>
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
    <script type="text/javascript" src="formularioTipodocumento.js"></script>

</body>

</html>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Obtener parámetros URL para determinar si es edición
    const urlParams = new URLSearchParams(window.location.search);
    const idTipodocumento = urlParams.get('id');
    const modo = urlParams.get('modo') || 'nuevo';
    
    // Configurar página según el modo
    if (modo === 'editar' && idTipodocumento) {
        document.getElementById('page-title').textContent = 'Editar Tipo de Documento';
        document.getElementById('breadcrumb-title').textContent = 'Editar Tipo de Documento';
        document.getElementById('btnSalvarTipodocumento').innerHTML = '<i class="fas fa-save me-2"></i>Actualizar Tipo de Documento';
        document.getElementById('id_tipo_documento').value = idTipodocumento;
        
        // Cargar datos del tipo de documento para edición
        cargarDatosTipodocumento(idTipodocumento);
    }
    
    // Contador de caracteres para la descripción
    const descrTextarea = document.getElementById('descripcion_tipo_documento');
    const charCount = document.getElementById('char-count');
    
    if (descrTextarea && charCount) {
        descrTextarea.addEventListener('input', function() {
            const currentLength = this.value.length;
            charCount.textContent = currentLength;
        });
    }
    
    // Validación en tiempo real para código
    const codigoInput = document.getElementById('codigo_tipo_documento');
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
    
    // Validación en tiempo real para nombre
    const nombreInput = document.getElementById('nombre_tipo_documento');
    if (nombreInput) {
        nombreInput.addEventListener('input', function() {
            if (this.value.length > 100) {
                this.classList.add('is-invalid');
            } else if (this.value.length >= 2) {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            } else {
                this.classList.remove('is-valid', 'is-invalid');
            }
        });
    }
});

// Función para cargar datos de tipo de documento en modo edición
function cargarDatosTipodocumento(idTipodocumento) {
    // Esta función se implementará en el archivo formularioTipodocumento.js
    console.log('Cargando datos de tipo de documento ID:', idTipodocumento);
}
</script>
