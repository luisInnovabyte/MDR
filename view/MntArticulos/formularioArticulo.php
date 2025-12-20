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
                <a class="breadcrumb-item" href="index.php">Mantenimiento Artículos</a>
                <span class="breadcrumb-item active" id="breadcrumb-title">Nuevo Artículo</span>
            </nav>
        </div><!-- br-pageheader -->
        
        <div class="br-pagetitle">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <h4 class="mb-0 me-2" id="page-title">Nuevo Artículo</h4>
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
                
                <!-- Formulario de Artículo -->
                <form id="formArticulo">
                    <!-- Campo oculto para ID del artículo -->
                    <input type="hidden" name="id_articulo" id="id_articulo">

                    <!-- SECCIÓN: Información Básica -->
                    <div class="card mb-4 border-primary">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0 tx-bold">
                                <i class="fas fa-info-circle me-2"></i>Información Básica del Artículo
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-12 col-md-4">
                                    <label for="codigo_articulo" class="form-label">Código artículo: <span class="tx-danger">*</span></label>
                                    <input type="text" class="form-control" name="codigo_articulo" id="codigo_articulo" maxlength="50" placeholder="Ej: ART001, MIC-SM58, etc..." required>
                                    <div class="invalid-feedback small-invalid-feedback">Ingrese un código válido (máximo 50 caracteres, único)</div>
                                    <small class="form-text text-muted">Código único identificativo (máximo 50 caracteres)</small>
                                </div>
                                <div class="col-12 col-md-4">
                                    <label class="form-label">Estado:</label>
                                    <div class="form-check form-switch mt-2">
                                        <input class="form-check-input" type="checkbox" name="activo_articulo" id="activo_articulo" checked disabled>
                                        <label class="form-check-label" for="activo_articulo">
                                            <span id="estado-text">Artículo Activo</span>
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">El estado se establece automáticamente</small>
                                </div>
                                <div class="col-12 col-md-4">
                                    <label for="precio_alquiler_articulo" class="form-label">Precio de alquiler (€):</label>
                                    <input type="number" class="form-control" name="precio_alquiler_articulo" id="precio_alquiler_articulo" step="0.01" min="0" placeholder="0.00">
                                    <small class="form-text text-muted">Precio base de alquiler del artículo</small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12 col-md-6">
                                    <label for="nombre_articulo" class="form-label">Nombre artículo: <span class="tx-danger">*</span></label>
                                    <input type="text" class="form-control" name="nombre_articulo" id="nombre_articulo" maxlength="255" placeholder="Ej: Micrófono inalámbrico SM58, etc..." required>
                                    <div class="invalid-feedback small-invalid-feedback">Ingrese un nombre válido (mínimo 3 y máximo 255 caracteres)</div>
                                    <small class="form-text text-muted">Nombre descriptivo en español</small>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="name_articulo" class="form-label">English name: <span class="tx-danger">*</span></label>
                                    <input type="text" class="form-control" name="name_articulo" id="name_articulo" maxlength="255" placeholder="Ej: Wireless microphone SM58, etc..." required>
                                    <div class="invalid-feedback small-invalid-feedback">Ingrese un nombre válido en inglés (mínimo 3 y máximo 255 caracteres)</div>
                                    <small class="form-text text-muted">Nombre descriptivo en inglés</small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12 col-md-6">
                                    <label for="id_familia" class="form-label">Familia: <span class="tx-danger">*</span></label>
                                    <select class="form-control" name="id_familia" id="id_familia" required>
                                        <option value="">Seleccionar familia...</option>
                                        <!-- Las opciones se cargarán dinámicamente -->
                                    </select>
                                    <div class="invalid-feedback small-invalid-feedback">Seleccione una familia válida</div>
                                    <small class="form-text text-muted">
                                        Familia a la que pertenece el artículo
                                        <div id="familia-descripcion" class="mt-1 p-2 bg-light border rounded" style="display: none;">
                                            <strong>Descripción:</strong> <span id="familia-descr-text"></span>
                                        </div>
                                    </small>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="id_unidad" class="form-label">Unidad de medida:</label>
                                    <select class="form-control" name="id_unidad" id="id_unidad">
                                        <option value="">Seleccionar unidad de medida...</option>
                                        <!-- Las opciones se cargarán dinámicamente -->
                                    </select>
                                    <div class="invalid-feedback small-invalid-feedback">Seleccione una unidad de medida válida</div>
                                    <small class="form-text text-muted">
                                        Unidad de medida del artículo (opcional, hereda de familia)
                                        <div id="unidad-descripcion" class="mt-1 p-2 bg-light border rounded" style="display: none;">
                                            <strong>Descripción:</strong> <span id="unidad-descr-text"></span>
                                        </div>
                                    </small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12 col-md-6">
                                    <label for="imagen_articulo" class="form-label">Imagen del artículo:</label>
                                    <input type="file" class="form-control" name="imagen_articulo" id="imagen_articulo" accept="image/*">
                                    <input type="hidden" name="imagen_actual" id="imagen_actual">
                                    <div class="invalid-feedback small-invalid-feedback">Seleccione una imagen válida (JPG, PNG, GIF)</div>
                                    <small class="form-text text-muted">Imagen opcional (máximo 2MB, formatos: JPG, PNG, GIF)</small>
                                    <!-- Mensaje de advertencia fijo -->
                                    <div class="alert alert-warning mt-2 py-2" role="alert">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        <small><strong>Advertencia:</strong> La imagen tardará unos segundos en procesarse, no salga de la pantalla.</small>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label">Vista previa:</label>
                                    <div class="image-preview-container" style="border: 2px dashed #ddd; border-radius: 8px; padding: 10px; text-align: center; min-height: 120px; display: flex; align-items: center; justify-content: center;">
                                        <div id="image-preview" style="max-width: 100%; max-height: 100px;">
                                            <i class="fas fa-image text-muted" style="font-size: 3rem;"></i>
                                            <p class="text-muted mt-2 mb-0">Sin imagen</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- SECCIÓN: Configuración Avanzada -->
                    <div class="card mb-4 border-info">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0 tx-bold">
                                <i class="fas fa-cog me-2"></i>Configuración Avanzada
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-12 col-md-4">
                                    <label class="form-label">Coeficientes de descuento:</label>
                                    <div class="mt-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="coeficiente_articulo" id="coeficiente_heredado" value="" checked>
                                            <label class="form-check-label" for="coeficiente_heredado">
                                                Heredar de familia
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="coeficiente_articulo" id="coeficiente_si" value="1">
                                            <label class="form-check-label" for="coeficiente_si">
                                                Sí, permitir coeficientes
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="coeficiente_articulo" id="coeficiente_no" value="0">
                                            <label class="form-check-label" for="coeficiente_no">
                                                No, sin coeficientes
                                            </label>
                                        </div>
                                    </div>
                                    <small class="form-text text-muted">
                                        Si permite aplicar coeficientes de descuento
                                        <i class="fas fa-question-circle text-info ms-1" 
                                           data-bs-toggle="tooltip" 
                                           title="Controla si este artículo puede tener coeficientes de descuento. NULL=hereda de familia, 1=permite, 0=no permite."></i>
                                    </small>
                                </div>
                                
                                <div class="col-12 col-md-4">
                                    <label class="form-label">Características especiales:</label>
                                    <div class="form-check form-switch mt-2">
                                        <input class="form-check-input" type="checkbox" name="es_kit_articulo" id="es_kit_articulo">
                                        <label class="form-check-label" for="es_kit_articulo">
                                            <span id="kit-text">Es un kit (conjunto de artículos)</span>
                                        </label>
                                    </div>
                                    <div class="form-check form-switch mt-2">
                                        <input class="form-check-input" type="checkbox" name="control_total_articulo" id="control_total_articulo">
                                        <label class="form-check-label" for="control_total_articulo">
                                            <span id="control-text">Control total</span>
                                        </label>
                                    </div>
                                    <div class="form-check form-switch mt-2">
                                        <input class="form-check-input" type="checkbox" name="no_facturar_articulo" id="no_facturar_articulo">
                                        <label class="form-check-label" for="no_facturar_articulo">
                                            <span id="facturar-text">No facturar</span>
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">Flags especiales para el artículo</small>
                                </div>
                                
                                <div class="col-12 col-md-4">
                                    <label for="orden_obs_articulo" class="form-label">Orden de observaciones:</label>
                                    <input type="number" class="form-control" name="orden_obs_articulo" id="orden_obs_articulo" value="200" min="1" max="999" placeholder="200">
                                    <small class="form-text text-muted">
                                        Orden de aparición en presupuestos (1-999)
                                        <i class="fas fa-question-circle text-info ms-1" 
                                           data-bs-toggle="tooltip" 
                                           title="Número que define el orden en que aparecerán las observaciones en los presupuestos. Menor número = aparece primero."></i>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECCIÓN: Observaciones -->
                    <div class="card mb-4 border-secondary">
                        <div class="card-header bg-secondary text-white">
                            <h5 class="mb-0 tx-bold">
                                <i class="fas fa-file-alt me-2"></i>Observaciones y Notas
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-12 col-md-6">
                                    <label for="notas_presupuesto_articulo" class="form-label">Notas para presupuestos (Español):</label>
                                    <textarea class="form-control" name="notas_presupuesto_articulo" id="notas_presupuesto_articulo" rows="4" placeholder="Notas que aparecerán en los presupuestos para este artículo..."></textarea>
                                    <small class="form-text text-muted">
                                        Notas en español que se mostrarán en los presupuestos
                                        <span class="float-end">
                                            <span id="char-count-notas">0</span> caracteres
                                        </span>
                                    </small>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="notes_budget_articulo" class="form-label">Budget notes (English):</label>
                                    <textarea class="form-control" name="notes_budget_articulo" id="notes_budget_articulo" rows="4" placeholder="Notes that will appear in budgets for this article..."></textarea>
                                    <small class="form-text text-muted">
                                        Notes in English that will be displayed in budgets
                                        <span class="float-end">
                                            <span id="char-count-notes">0</span> caracteres
                                        </span>
                                    </small>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <label for="observaciones_articulo" class="form-label">Observaciones internas:</label>
                                    <textarea class="form-control" name="observaciones_articulo" id="observaciones_articulo" rows="3" placeholder="Observaciones internas, notas técnicas, comentarios de uso..."></textarea>
                                    <small class="form-text text-muted">
                                        Observaciones internas que NO aparecerán en presupuestos
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
                            <button type="button" name="action" id="btnSalvarArticulo" class="btn btn-primary btn-lg me-3">
                                <i class="fas fa-save me-2"></i>Guardar Artículo
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
                        <i class="fas fa-question-circle me-2"></i>Ayuda - Formulario de Artículos
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <h6 class="text-primary"><i class="fas fa-barcode me-2"></i>Código de Artículo</h6>
                            <p><strong>Campo obligatorio.</strong> Identificador único del artículo.</p>
                            <ul class="list-unstyled ms-3">
                                <li><i class="fas fa-check text-success me-2"></i>Máximo 50 caracteres</li>
                                <li><i class="fas fa-check text-success me-2"></i>Se convierte automáticamente a mayúsculas</li>
                                <li><i class="fas fa-check text-success me-2"></i>Debe ser único en el sistema</li>
                            </ul>
                            <hr>
                        </div>
                        
                        <div class="col-12">
                            <h6 class="text-primary"><i class="fas fa-tag me-2"></i>Nombres de Artículo</h6>
                            <p><strong>Campos obligatorios.</strong> Nombres descriptivos del artículo en español e inglés.</p>
                            <ul class="list-unstyled ms-3">
                                <li><i class="fas fa-check text-success me-2"></i>Entre 3 y 255 caracteres cada uno</li>
                                <li><i class="fas fa-check text-success me-2"></i>Deben ser descriptivos y claros</li>
                            </ul>
                            <hr>
                        </div>
                        
                        <div class="col-12">
                            <h6 class="text-primary"><i class="fas fa-folder me-2"></i>Familia del Artículo</h6>
                            <p><strong>Campo obligatorio.</strong> Familia a la que pertenece el artículo.</p>
                            <ul class="list-unstyled ms-3">
                                <li><i class="fas fa-list text-info me-2"></i>Seleccione de la lista de familias disponibles</li>
                                <li><i class="fas fa-info-circle text-info me-2"></i>Determina la categorización del artículo</li>
                            </ul>
                            <hr>
                        </div>

                        <div class="col-12">
                            <h6 class="text-primary"><i class="fas fa-percent me-2"></i>Coeficientes de Descuento</h6>
                            <p><strong>Campo de configuración.</strong> Determina si el artículo permite coeficientes.</p>
                            <ul class="list-unstyled ms-3">
                                <li><i class="fas fa-arrow-down text-info me-2"></i><strong>Heredar:</strong> Usa la configuración de la familia</li>
                                <li><i class="fas fa-check text-success me-2"></i><strong>Sí:</strong> Permite coeficientes independiente de familia</li>
                                <li><i class="fas fa-times text-danger me-2"></i><strong>No:</strong> No permite coeficientes</li>
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
    <script type="text/javascript" src="formularioArticulo.js"></script>

    <!-- Botones flotantes para navegación -->
    <!-- Botón para ir al inicio del formulario -->
    <button id="scrollToTop" class="btn btn-primary" style="position: fixed; bottom: 140px; right: 30px; z-index: 1000; border-radius: 50%; width: 50px; height: 50px; display: none; box-shadow: 0 4px 8px rgba(0,0,0,0.3);" title="Ir al inicio del formulario">
        <i class="fas fa-arrow-up"></i>
    </button>
    
    <!-- Botón para ir al final del formulario -->
    <button id="scrollToBottom" class="btn btn-primary" style="position: fixed; bottom: 80px; right: 30px; z-index: 1000; border-radius: 50%; width: 50px; height: 50px; display: none; box-shadow: 0 4px 8px rgba(0,0,0,0.3);" title="Ir al final del formulario">
        <i class="fas fa-arrow-down"></i>
    </button>

    <!-- Script para botones flotantes de navegación -->
    <script>
        $(document).ready(function() {
            // Mostrar/ocultar botones según scroll
            $(window).scroll(function() {
                if ($(this).scrollTop() > 300) {
                    $('#scrollToTop').fadeIn();
                    $('#scrollToBottom').fadeIn();
                } else {
                    $('#scrollToTop').fadeOut();
                    $('#scrollToBottom').fadeOut();
                }
            });

            // Hacer scroll al inicio del formulario
            $('#scrollToTop').click(function() {
                $('html, body').animate({
                    scrollTop: 0
                }, 800);
                return false;
            });

            // Hacer scroll al final del formulario
            $('#scrollToBottom').click(function() {
                $('html, body').animate({
                    scrollTop: $(document).height()
                }, 800);
                return false;
            });
        });
    </script>

</body>

</html>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Obtener parámetros URL para determinar si es edición
    const urlParams = new URLSearchParams(window.location.search);
    const idArticulo = urlParams.get('id');
    const modo = urlParams.get('modo') || 'nuevo';
    
    // Configurar página según el modo
    if (modo === 'editar' && idArticulo) {
        document.getElementById('page-title').textContent = 'Editar Artículo';
        document.getElementById('breadcrumb-title').textContent = 'Editar Artículo';
        document.getElementById('btnSalvarArticulo').innerHTML = '<i class="fas fa-save me-2"></i>Actualizar Artículo';
        document.getElementById('id_articulo').value = idArticulo;
        
        // Cargar datos del artículo para edición
        cargarDatosArticulo(idArticulo);
    }
    
    // Contadores de caracteres
    const notasTextarea = document.getElementById('notas_presupuesto_articulo');
    const notesTextarea = document.getElementById('notes_budget_articulo');
    const obsTextarea = document.getElementById('observaciones_articulo');
    
    if (notasTextarea) {
        notasTextarea.addEventListener('input', function() {
            document.getElementById('char-count-notas').textContent = this.value.length;
        });
    }
    
    if (notesTextarea) {
        notesTextarea.addEventListener('input', function() {
            document.getElementById('char-count-notes').textContent = this.value.length;
        });
    }
    
    if (obsTextarea) {
        obsTextarea.addEventListener('input', function() {
            document.getElementById('char-count-obs').textContent = this.value.length;
        });
    }
    
    // Validación en tiempo real para código artículo
    const codigoInput = document.getElementById('codigo_articulo');
    if (codigoInput) {
        codigoInput.addEventListener('input', function() {
            this.value = this.value.toUpperCase();
            
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
    
    // Manejo de vista previa de imagen
    const imagenInput = document.getElementById('imagen_articulo');
    const imagePreview = document.getElementById('image-preview');
    
    if (imagenInput && imagePreview) {
        imagenInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            
            if (file) {
                if (file.size > 2 * 1024 * 1024) {
                    toastr.error('La imagen es demasiado grande. Máximo 2MB permitido.');
                    this.value = '';
                    showDefaultImagePreview();
                    return;
                }
                
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
                if (!allowedTypes.includes(file.type)) {
                    toastr.error('Formato de imagen no válido. Use JPG, PNG o GIF.');
                    this.value = '';
                    showDefaultImagePreview();
                    return;
                }
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.innerHTML = `<img src="${e.target.result}" alt="Vista previa" style="max-width: 100%; max-height: 100px; border-radius: 4px;">`;
                };
                reader.readAsDataURL(file);
                
                imagenInput.classList.remove('is-invalid');
                imagenInput.classList.add('is-valid');
            } else {
                showDefaultImagePreview();
            }
        });
    }
});

function cargarDatosArticulo(idArticulo) {
    console.log('Cargando datos de artículo ID:', idArticulo);
}
</script>
