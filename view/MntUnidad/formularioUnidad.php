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
                <a class="breadcrumb-item" href="index.php">Mantenimiento Unidades</a>
                <span class="breadcrumb-item active" id="breadcrumb-title">Nueva Unidad</span>
            </nav>
        </div><!-- br-pageheader -->
        
        <div class="br-pagetitle">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <h4 class="mb-0 me-2" id="page-title">Nueva Unidad</h4>
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
                
                <!-- Formulario de Unidad -->
                <form id="formUnidad">
                    <!-- Campo oculto para ID de la unidad -->
                    <input type="hidden" name="id_unidad" id="id_unidad">

                    <!-- SECCIÓN: Información Básica -->
                    <div class="card mb-4 border-primary">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0 tx-bold">
                                <i class="fas fa-ruler me-2"></i>Información Básica de la Unidad
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-12 col-md-6">
                                    <label for="nombre_unidad" class="form-label">Nombre de la unidad (Español): <span class="tx-danger">*</span></label>
                                    <input type="text" class="form-control" name="nombre_unidad" id="nombre_unidad" maxlength="50" placeholder="Ej: Metro, Kilogramo, Litro..." required>
                                    <div class="invalid-feedback small-invalid-feedback">Ingrese un nombre válido (máximo 50 caracteres, único)</div>
                                    <small class="form-text text-muted">Nombre único de la unidad en español (máximo 50 caracteres)</small>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="name_unidad" class="form-label">Nombre de la unidad (Inglés): <span class="tx-danger">*</span></label>
                                    <input type="text" class="form-control" name="name_unidad" id="name_unidad" maxlength="50" placeholder="Ej: Meter, Kilogram, Liter..." required>
                                    <div class="invalid-feedback small-invalid-feedback">Ingrese un nombre válido en inglés (máximo 50 caracteres, único)</div>
                                    <small class="form-text text-muted">Nombre único de la unidad en inglés (máximo 50 caracteres)</small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12 col-md-6">
                                    <label for="simbolo_unidad" class="form-label">Símbolo de la unidad:</label>
                                    <input type="text" class="form-control" name="simbolo_unidad" id="simbolo_unidad" maxlength="10" placeholder="Ej: m, kg, L, pz...">
                                    <div class="invalid-feedback small-invalid-feedback">Ingrese un símbolo válido (máximo 10 caracteres)</div>
                                    <small class="form-text text-muted">Símbolo o abreviatura de la unidad (opcional, máximo 10 caracteres)</small>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label">Estado:</label>
                                    <div class="form-check form-switch mt-2">
                                        <input class="form-check-input" type="checkbox" name="activo_unidad" id="activo_unidad" checked disabled>
                                        <label class="form-check-label" for="activo_unidad">
                                            <span id="estado-text">Unidad Activa</span>
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">El estado se establece automáticamente</small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12">
                                    <label for="descr_unidad" class="form-label">Descripción:</label>
                                    <textarea class="form-control" name="descr_unidad" id="descr_unidad" maxlength="255" rows="3" placeholder="Descripción detallada de la unidad de medida..."></textarea>
                                    <div class="invalid-feedback small-invalid-feedback">La descripción no puede exceder los 255 caracteres</div>
                                    <small class="form-text text-muted">
                                        Descripción opcional de la unidad (máximo 255 caracteres)
                                        <span class="float-end">
                                            <span id="char-count">0</span>/255 caracteres
                                        </span>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botones de acción -->
                    <div class="card">
                        <div class="card-body text-center">
                            <button type="button" name="action" id="btnSalvarUnidad" class="btn btn-primary btn-lg me-3">
                                <i class="fas fa-save me-2"></i>Guardar Unidad
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
                        <i class="fas fa-question-circle me-2"></i>Ayuda - Formulario de Unidades
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <h6 class="text-primary"><i class="fas fa-tag me-2"></i>Nombre de la Unidad (Español)</h6>
                            <p><strong>Campo obligatorio.</strong> Nombre único de la unidad en español.</p>
                            <ul class="list-unstyled ms-3">
                                <li><i class="fas fa-check text-success me-2"></i>Máximo 50 caracteres</li>
                                <li><i class="fas fa-check text-success me-2"></i>Se convierte automáticamente con formato apropiado</li>
                                <li><i class="fas fa-check text-success me-2"></i>Debe ser único en el sistema</li>
                                <li><i class="fas fa-info text-info me-2"></i>Ejemplos: Metro, Kilogramo, Litro, Pieza</li>
                            </ul>
                            <hr>
                        </div>
                        
                        <div class="col-12">
                            <h6 class="text-primary"><i class="fas fa-language me-2"></i>Nombre de la Unidad (Inglés)</h6>
                            <p><strong>Campo obligatorio.</strong> Nombre único de la unidad en inglés.</p>
                            <ul class="list-unstyled ms-3">
                                <li><i class="fas fa-check text-success me-2"></i>Máximo 50 caracteres</li>
                                <li><i class="fas fa-check text-success me-2"></i>Se convierte automáticamente con formato apropiado</li>
                                <li><i class="fas fa-check text-success me-2"></i>Debe ser único en el sistema</li>
                                <li><i class="fas fa-info text-info me-2"></i>Ejemplos: Meter, Kilogram, Liter, Piece</li>
                            </ul>
                            <hr>
                        </div>
                        
                        <div class="col-12">
                            <h6 class="text-primary"><i class="fas fa-badge me-2"></i>Símbolo y Estado</h6>
                            <p><strong>Información complementaria.</strong> Campos adicionales de la unidad.</p>
                            <ul class="list-unstyled ms-3">
                                <li><i class="fas fa-info text-info me-2"></i>Símbolo: campo opcional, máximo 10 caracteres (Ej: m, kg, L)</li>
                                <li><i class="fas fa-info text-info me-2"></i>Descripción: campo opcional, máximo 255 caracteres</li>
                                <li><i class="fas fa-lock text-warning me-2"></i>Estado: se establece automáticamente (activo para nuevas unidades)</li>
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
    <script type="text/javascript" src="formularioUnidad.js"></script>

</body>

</html>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Obtener parámetros URL para determinar si es edición
    const urlParams = new URLSearchParams(window.location.search);
    const idUnidad = urlParams.get('id');
    const modo = urlParams.get('modo') || 'nuevo';
    
    // Configurar página según el modo
    if (modo === 'editar' && idUnidad) {
        document.getElementById('page-title').textContent = 'Editar Unidad';
        document.getElementById('breadcrumb-title').textContent = 'Editar Unidad';
        document.getElementById('btnSalvarUnidad').innerHTML = '<i class="fas fa-save me-2"></i>Actualizar Unidad';
        document.getElementById('id_unidad').value = idUnidad;
        
        // Cargar datos de la unidad para edición
        cargarDatosUnidad(idUnidad);
    }
    
    // Contador de caracteres para la descripción
    const descrTextarea = document.getElementById('descr_unidad');
    const charCount = document.getElementById('char-count');
    
    if (descrTextarea && charCount) {
        descrTextarea.addEventListener('input', function() {
            const currentLength = this.value.length;
            charCount.textContent = currentLength;
            
            // Cambiar color según la proximidad al límite
            if (currentLength > 200) {
                charCount.style.color = '#dc3545'; // Rojo
            } else if (currentLength > 150) {
                charCount.style.color = '#ffc107'; // Amarillo
            } else {
                charCount.style.color = '#6c757d'; // Gris normal
            }
        });
    }
    
    // Validación en tiempo real para nombre unidad español
    const nombreInput = document.getElementById('nombre_unidad');
    if (nombreInput) {
        nombreInput.addEventListener('input', function() {
            // Validar longitud
            if (this.value.length > 50) {
                this.classList.add('is-invalid');
            } else if (this.value.length >= 2) {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            } else {
                this.classList.remove('is-valid', 'is-invalid');
            }
        });
    }
    
    // Validación en tiempo real para nombre unidad inglés
    const nameInput = document.getElementById('name_unidad');
    if (nameInput) {
        nameInput.addEventListener('input', function() {
            // Validar longitud
            if (this.value.length > 50) {
                this.classList.add('is-invalid');
            } else if (this.value.length >= 2) {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            } else {
                this.classList.remove('is-valid', 'is-invalid');
            }
        });
    }
    
    // Validación en tiempo real para símbolo unidad
    const simboloInput = document.getElementById('simbolo_unidad');
    if (simboloInput) {
        simboloInput.addEventListener('input', function() {
            // Validar longitud
            if (this.value.length > 10) {
                this.classList.add('is-invalid');
            } else if (this.value.length > 0) {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            } else {
                this.classList.remove('is-valid', 'is-invalid');
            }
        });
    }
});

// Función para cargar datos de unidad en modo edición
function cargarDatosUnidad(idUnidad) {
    // Esta función se implementará en el archivo formularioUnidad.js
    console.log('Cargando datos de unidad ID:', idUnidad);
}
</script>