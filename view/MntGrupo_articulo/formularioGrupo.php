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
                <a class="breadcrumb-item" href="index.php">Mantenimiento Grupos de Artículos</a>
                <span class="breadcrumb-item active" id="breadcrumb-title">Nuevo Grupo de Artículo</span>
            </nav>
        </div><!-- br-pageheader -->
        
        <div class="br-pagetitle">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <h4 class="mb-0 me-2" id="page-title">Nuevo Grupo de Artículo</h4>
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
                
                <!-- Formulario de Grupo de Artículo -->
                <form id="formGrupo">
                    <!-- Campo oculto para ID del grupo -->
                    <input type="hidden" name="id_grupo" id="id_grupo">

                    <!-- SECCIÓN: Información Básica -->
                    <div class="card mb-4 border-primary">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0 tx-bold">
                                <i class="fas fa-info-circle me-2"></i>Información Básica del Grupo de Artículo
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-12 col-md-4">
                                    <label for="codigo_grupo" class="form-label">Código grupo: <span class="tx-danger">*</span></label>
                                    <input type="text" class="form-control" name="codigo_grupo" id="codigo_grupo" maxlength="20" placeholder="Ej: AUD, ILU, VID..." required>
                                    <div class="invalid-feedback small-invalid-feedback">Ingrese un código válido (máximo 20 caracteres, único)</div>
                                    <small class="form-text text-muted">Código único identificativo (máximo 20 caracteres)</small>
                                </div>
                                <div class="col-12 col-md-4">
                                    <label class="form-label">Estado:</label>
                                    <div class="form-check form-switch mt-2">
                                        <input class="form-check-input" type="checkbox" name="activo_grupo" id="activo_grupo" checked disabled>
                                        <label class="form-check-label" for="activo_grupo">
                                            <span id="estado-text">Grupo Activo</span>
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">El estado se establece automáticamente</small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12 col-md-6">
                                    <label for="nombre_grupo" class="form-label">Nombre grupo: <span class="tx-danger">*</span></label>
                                    <input type="text" class="form-control" name="nombre_grupo" id="nombre_grupo" maxlength="100" placeholder="Ej: Audio, Iluminación, Vídeo..." required>
                                    <div class="invalid-feedback small-invalid-feedback">Ingrese un nombre válido (mínimo 3 y máximo 100 caracteres)</div>
                                    <small class="form-text text-muted">Nombre descriptivo del grupo</small>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="descripcion_grupo" class="form-label">Descripción:</label>
                                    <input type="text" class="form-control" name="descripcion_grupo" id="descripcion_grupo" maxlength="255" placeholder="Descripción breve del grupo...">
                                    <div class="invalid-feedback small-invalid-feedback">La descripción no puede exceder los 255 caracteres</div>
                                    <small class="form-text text-muted">
                                        Descripción opcional del grupo (máximo 255 caracteres)
                                        <span class="float-end">
                                            <span id="char-count-desc">0</span>/255 caracteres
                                        </span>
                                    </small>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <label for="observaciones_grupo" class="form-label">Observaciones:</label>
                                    <textarea class="form-control" name="observaciones_grupo" id="observaciones_grupo" rows="4" placeholder="Observaciones adicionales del grupo..."></textarea>
                                    <div class="invalid-feedback small-invalid-feedback">Las observaciones son demasiado largas</div>
                                    <small class="form-text text-muted">
                                        Observaciones opcionales (notas internas, consideraciones especiales, etc.)
                                        <span class="float-end">
                                            <span id="char-count-obs">0</span> caracteres
                                        </span>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botones de acción -->
                    <div class="card">
                        <div class="card-body text-center">
                            <button type="button" name="action" id="btnSalvarGrupo" class="btn btn-primary btn-lg me-3">
                                <i class="fas fa-save me-2"></i>Guardar Grupo
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
                        <i class="fas fa-question-circle me-2"></i>Ayuda - Formulario de Grupos de Artículos
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <h6 class="text-primary"><i class="fas fa-barcode me-2"></i>Código de Grupo</h6>
                            <p><strong>Campo obligatorio.</strong> Identificador único del grupo.</p>
                            <ul class="list-unstyled ms-3">
                                <li><i class="fas fa-check text-success me-2"></i>Máximo 20 caracteres</li>
                                <li><i class="fas fa-check text-success me-2"></i>Se convierte automáticamente a mayúsculas</li>
                                <li><i class="fas fa-check text-success me-2"></i>No puede contener espacios</li>
                                <li><i class="fas fa-check text-success me-2"></i>Debe ser único en el sistema</li>
                            </ul>
                            <hr>
                        </div>
                        
                        <div class="col-12">
                            <h6 class="text-primary"><i class="fas fa-tag me-2"></i>Nombre de Grupo</h6>
                            <p><strong>Campo obligatorio.</strong> Nombre descriptivo del grupo.</p>
                            <ul class="list-unstyled ms-3">
                                <li><i class="fas fa-check text-success me-2"></i>Entre 3 y 100 caracteres</li>
                                <li><i class="fas fa-check text-success me-2"></i>Debe ser descriptivo y claro</li>
                                <li><i class="fas fa-check text-success me-2"></i>Ejemplos: Audio, Iluminación, Vídeo, Estructuras</li>
                            </ul>
                            <hr>
                        </div>
                        
                        <div class="col-12">
                            <h6 class="text-primary"><i class="fas fa-file-alt me-2"></i>Descripción y Observaciones</h6>
                            <p><strong>Campos opcionales.</strong> Información complementaria del grupo.</p>
                            <ul class="list-unstyled ms-3">
                                <li><i class="fas fa-info text-info me-2"></i>Descripción: campo breve, máximo 255 caracteres</li>
                                <li><i class="fas fa-info text-info me-2"></i>Observaciones: campo de texto libre para notas internas</li>
                                <li><i class="fas fa-lock text-warning me-2"></i>Estado: se establece automáticamente (activo para nuevos grupos)</li>
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
    <script type="text/javascript" src="formularioGrupo.js"></script>

</body>

</html>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Obtener parámetros URL para determinar si es edición
    const urlParams = new URLSearchParams(window.location.search);
    const idGrupo = urlParams.get('id');
    const modo = urlParams.get('modo') || 'nuevo';
    
    // Configurar página según el modo
    if (modo === 'editar' && idGrupo) {
        document.getElementById('page-title').textContent = 'Editar Grupo de Artículo';
        document.getElementById('breadcrumb-title').textContent = 'Editar Grupo de Artículo';
        document.getElementById('btnSalvarGrupo').innerHTML = '<i class="fas fa-save me-2"></i>Actualizar Grupo';
        document.getElementById('id_grupo').value = idGrupo;
        
        // Cargar datos del grupo para edición
        cargarDatosGrupo(idGrupo);
    }
    
    // Contador de caracteres para la descripción
    const descrInput = document.getElementById('descripcion_grupo');
    const charCountDesc = document.getElementById('char-count-desc');
    
    if (descrInput && charCountDesc) {
        descrInput.addEventListener('input', function() {
            const currentLength = this.value.length;
            charCountDesc.textContent = currentLength;
            
            // Cambiar color según la proximidad al límite
            if (currentLength > 200) {
                charCountDesc.style.color = '#dc3545'; // Rojo
            } else if (currentLength > 150) {
                charCountDesc.style.color = '#ffc107'; // Amarillo
            } else {
                charCountDesc.style.color = '#6c757d'; // Gris normal
            }
        });
    }
    
    // Contador de caracteres para las observaciones
    const obsTextarea = document.getElementById('observaciones_grupo');
    const charCountObs = document.getElementById('char-count-obs');
    
    if (obsTextarea && charCountObs) {
        obsTextarea.addEventListener('input', function() {
            const currentLength = this.value.length;
            charCountObs.textContent = currentLength;
        });
    }
    
    // Validación en tiempo real para código grupo
    const codigoInput = document.getElementById('codigo_grupo');
    if (codigoInput) {
        codigoInput.addEventListener('input', function() {
            // Convertir a mayúsculas y eliminar espacios
            this.value = this.value.toUpperCase().replace(/\s+/g, '');
            
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
    
    // Validación en tiempo real para nombre grupo
    const nombreInput = document.getElementById('nombre_grupo');
    if (nombreInput) {
        nombreInput.addEventListener('input', function() {
            if (this.value.length >= 3 && this.value.length <= 100) {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            } else {
                this.classList.remove('is-valid');
                if (this.value.length > 0) {
                    this.classList.add('is-invalid');
                }
            }
        });
    }
});

// Función para cargar datos de grupo en modo edición
function cargarDatosGrupo(idGrupo) {
    // Esta función se implementará en el archivo formularioGrupo.js
    console.log('Cargando datos de grupo ID:', idGrupo);
}
</script>
