<!-- Modal con animación fade -->
<div class="modal fade" id="modalMarca" tabindex="-1" aria-labelledby="modalMarcaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header pd-y-20 pd-x-25">
                <h6 class="tx-14 mg-b-0 tx-uppercase tx-inverse tx-bold">Gestión de Marcas</h6>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body pd-25">
                <h4 class="lh-3 mg-b-20" id="mdltitulo">
                    <a href="#" class="tx-inverse hover-primary">Formulario de Marca</a>
                </h4>

                <!-- Formulario de Marca -->
                <form id="formMarca">
                    <!-- Campo oculto para ID de la familia -->
                    <input type="hidden" name="id_marca" id="id_marca">

                    <!-- SECCIÓN 1: Código de la Familia -->
                    <div class="card mb-4 border-primary">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0 tx-bold">
                                <i class="fas fa-barcode me-2"></i>Código de la Marca
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row no-gutters">
                                <div class="col-12 col-lg-3">
                                    <label for="codigo_marca" class="form-label">Código marca: <span class="tx-danger">*</span></label>
                                </div>
                                <div class="col-7 col-sm-9">
                                    <input type="text" class="form-control" name="codigo_marca" id="codigo_marca" maxlength="20" placeholder="Ej: MAR001, TOLDOS, etc..." required>
                                    <div class="invalid-feedback small-invalid-feedback">Ingrese un código válido (máximo 20 caracteres, único)</div>
                                    <small class="form-text text-muted">Código único identificativo de la marca (máximo 20 caracteres)</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECCIÓN 2: Nombre de la Marca -->
                    <div class="card mb-4 border-success">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0 tx-bold">
                                <i class="fas fa-tag me-2"></i>Nombre de la Marca
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row no-gutters">
                                <div class="col-12 col-lg-3">
                                    <label for="nombre_marca" class="form-label">Nombre marca: <span class="tx-danger">*</span></label>
                                </div>
                                <div class="col-7 col-sm-9">
                                    <input type="text" class="form-control" name="nombre_marca" id="nombre_marca" maxlength="100" placeholder="Ej: Toldos exteriores, Parasoles, etc..." required>
                                    <div class="invalid-feedback small-invalid-feedback">Ingrese un nombre válido (mínimo 3 y máximo 100 caracteres)</div>
                                    <small class="form-text text-muted">Nombre descriptivo de la marca de productos (máximo 100 caracteres)</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECCIÓN 3: Nombre en Inglés -->
                    <div class="card mb-4 border-info">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0 tx-bold">
                                <i class="fas fa-globe me-2"></i>Nombre en Inglés
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row no-gutters">
                                <div class="col-12 col-lg-3">
                                    <label for="name_marca" class="form-label">English name: <span class="tx-danger">*</span></label>
                                </div>
                                <div class="col-7 col-sm-9">
                                    <input type="text" class="form-control" name="name_marca" id="name_marca" maxlength="100" placeholder="Ej: Outdoor awnings, Parasols, etc..." required>
                                    <div class="invalid-feedback small-invalid-feedback">Ingrese un nombre válido en inglés (mínimo 3 y máximo 100 caracteres)</div>
                                    <small class="form-text text-muted">Nombre de la marca de productos en inglés (máximo 100 caracteres)</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECCIÓN 4: Descripción de la Marca -->
                    <div class="card mb-4 border-secondary">
                        <div class="card-header bg-secondary text-white">
                            <h5 class="mb-0 tx-bold">
                                <i class="fas fa-file-alt me-2"></i>Descripción de la Marca
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row no-gutters">
                                <div class="col-12 col-lg-3">
                                    <label for="descr_marca" class="form-label">Descripción:</label>
                                </div>
                                <div class="col-7 col-sm-9">
                                    <textarea class="form-control" name="descr_marca" id="descr_marca" maxlength="255" rows="4" placeholder="Descripción detallada de la marca de productos..."></textarea>
                                    <div class="invalid-feedback small-invalid-feedback">La descripción no puede exceder los 255 caracteres</div>
                                    <small class="form-text text-muted">
                                        Descripción opcional de la marca (máximo 255 caracteres)
                                        <span class="float-end">
                                            <span id="char-count">0</span>/255 caracteres
                                        </span>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECCIÓN 5: Estado de la Familia -->
                    <!-- <div class="card mb-4 border-warning">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="mb-0 tx-bold">
                                <i class="fas fa-toggle-on me-2"></i>Estado de la Familia
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row no-gutters">
                                <div class="col-12 col-lg-3">
                                    <label class="form-label">Estado:</label>
                                </div>
                                <div class="col-7 col-sm-9">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="activo_familia" id="activo_familia" checked>
                                        <label class="form-check-label" for="activo_familia">
                                            <span id="estado-text">Familia Activa</span>
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">Las familias activas aparecerán disponibles en el sistema</small>
                                </div>
                            </div>
                        </div>
                    </div> -->

                </form>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer">
                <button type="button" name="action" id="btnSalvarMarca" class="btn btn-primary tx-11 tx-uppercase pd-y-12 pd-x-25 tx-mont tx-medium">
                    <i class="fas fa-save me-2"></i>Guardar
                </button>
                <button type="button" class="btn btn-secondary tx-11 tx-uppercase pd-y-12 pd-x-25 tx-mont tx-medium" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Cerrar
                </button>
            </div>
        </div>
    </div>
</div>



<script>
document.addEventListener('DOMContentLoaded', function() {
    // Contador de caracteres para la descripción
    const descrTextarea = document.getElementById('descr_marca');
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
    
    // Cambiar texto del estado según el switch
    const activoSwitch = document.getElementById('activo_marca');
    const estadoText = document.getElementById('estado-text');
    
    if (activoSwitch && estadoText) {
        activoSwitch.addEventListener('change', function() {
            if (this.checked) {
                estadoText.textContent = 'Marca Activa';
                estadoText.className = 'text-success fw-bold';
            } else {
                estadoText.textContent = 'Marca Inactiva';
                estadoText.className = 'text-danger fw-bold';
            }
        });
    }
    
    // Validación en tiempo real para código familia
    const codigoInput = document.getElementById('codigo_marca');
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
    
    // Validación en tiempo real para nombre familia
    const nombreInput = document.getElementById('nombre_marca');
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
    
    // Validación en tiempo real para nombre en inglés
    const nameInput = document.getElementById('name_marca');
    if (nameInput) {
        nameInput.addEventListener('input', function() {
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
</script>