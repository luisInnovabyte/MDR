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
                <a class="breadcrumb-item" href="index.php">Fotos de Elementos</a>
                <span class="breadcrumb-item active" id="breadcrumb-title">Nueva Foto</span>
            </nav>
        </div><!-- br-pageheader -->
        
        <div class="br-pagetitle">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <h4 class="mb-0 me-2" id="page-title">Nueva Foto</h4>
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
                
                <!-- Formulario de Foto -->
                <form id="formFoto" enctype="multipart/form-data">
                    <!-- Campo oculto para ID de la foto -->
                    <input type="hidden" name="id_foto_elemento" id="id_foto_elemento">

                    <!-- SECCIÓN: Información Básica -->
                    <div class="card mb-4 border-primary">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0 tx-bold">
                                <i class="fas fa-info-circle me-2"></i>Información Básica de la Foto
                            </h5>
                        </div>
                        <div class="card-body">
                            <!-- Campo oculto para ID del elemento -->
                            <input type="hidden" name="id_elemento" id="id_elemento" required>
                            
                            <!-- Banner informativo del elemento -->
                            <div class="row mb-3">
                                <div class="col-12">
                                    <div class="alert alert-info d-flex align-items-center" role="alert" id="elemento-banner">
                                        <div class="me-3">
                                            <i class="bi bi-box-seam" style="font-size: 2.5rem;"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="alert-heading mb-1">
                                                <i class="fas fa-image me-2"></i>Añadiendo foto al elemento:
                                            </h6>
                                            <p class="mb-0">
                                                <strong id="elemento-nombre">Cargando...</strong>
                                                <span class="badge bg-primary ms-2" id="elemento-codigo">--</span>
                                            </p>
                                            <small class="text-muted" id="elemento-id">ID: --</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12">
                                    <label for="descripcion_foto_elemento" class="form-label">Descripción: <span class="tx-danger">*</span></label>
                                    <input type="text" class="form-control" name="descripcion_foto_elemento" id="descripcion_foto_elemento" maxlength="200" placeholder="Descripción de la foto..." required>
                                    <div class="invalid-feedback small-invalid-feedback">Ingrese una descripción válida (mínimo 3 y máximo 200 caracteres)</div>
                                    <small class="form-text text-muted">Descripción breve de la foto</small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12 col-md-6">
                                    <label for="archivo_foto" class="form-label">Archivo de imagen: <span class="tx-danger">*</span></label>
                                    <input type="file" class="form-control" name="archivo_foto" id="archivo_foto" accept="image/jpeg,image/jpg,image/png,image/gif,image/webp" required>
                                    <input type="hidden" name="archivo_actual" id="archivo_actual">
                                    <div class="invalid-feedback small-invalid-feedback">Seleccione una imagen válida (JPG, JPEG, PNG, GIF, WEBP)</div>
                                    <small class="form-text text-muted">Archivo de imagen (máximo 5MB, formatos: JPG, JPEG, PNG, GIF, WEBP)</small>
                                    <!-- Mensaje de advertencia fijo -->
                                    <div class="alert alert-warning mt-2 py-2" role="alert">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        <small><strong>Advertencia:</strong> La imagen tardará unos segundos en procesarse, no salga de la pantalla.</small>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label">Vista previa:</label>
                                    <div class="file-preview-container" style="border: 2px dashed #ddd; border-radius: 8px; padding: 10px; text-align: center; min-height: 200px; display: flex; align-items: center; justify-content: center; background-color: #f8f9fa;">
                                        <div id="file-preview" style="max-width: 100%; max-height: 180px;">
                                            <i class="fas fa-image text-muted" style="font-size: 3rem;"></i>
                                            <p class="text-muted mt-2 mb-0">Sin imagen</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12 col-md-6">
                                    <label class="form-label">Estado:</label>
                                    <div class="form-check form-switch mt-2">
                                        <input class="form-check-input" type="checkbox" name="activo_foto_elemento" id="activo_foto_elemento" checked disabled>
                                        <label class="form-check-label" for="activo_foto_elemento">
                                            <span id="estado-text">Foto Activa</span>
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">El estado se establece automáticamente</small>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label">Privacidad de la foto:</label>
                                    <div class="form-check form-switch mt-2">
                                        <input class="form-check-input" type="checkbox" name="privado_foto" id="privado_foto">
                                        <label class="form-check-label" for="privado_foto">
                                            <span id="privado-text">Foto Pública</span>
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">
                                        Si la foto es privada, solo usuarios autorizados podrán verla
                                        <i class="fas fa-question-circle text-info ms-1" 
                                           data-bs-toggle="tooltip" 
                                           title="Este control determina si la foto es visible para todos o solo para usuarios con permisos especiales."></i>
                                    </small>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <label for="observaciones_foto" class="form-label">Observaciones:</label>
                                    <textarea class="form-control" name="observaciones_foto" id="observaciones_foto" rows="4" placeholder="Observaciones adicionales sobre la foto..."></textarea>
                                    <small class="form-text text-muted">
                                        Notas adicionales o comentarios sobre la foto
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botones de acción -->
                    <div class="card">
                        <div class="card-body text-center">
                            <button type="button" name="action" id="btnSalvarFoto" class="btn btn-primary btn-lg me-3">
                                <i class="fas fa-save me-2"></i>Guardar Foto
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
                        <i class="fas fa-question-circle me-2"></i>Ayuda - Formulario de Fotos
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <h6 class="text-primary"><i class="fas fa-box-seam me-2"></i>Elemento Asociado</h6>
                            <p><strong>Campo automático.</strong> La foto se añadirá al elemento seleccionado.</p>
                            <ul class="list-unstyled ms-3">
                                <li><i class="fas fa-info text-info me-2"></i>El elemento está preseleccionado automáticamente</li>
                                <li><i class="fas fa-info text-info me-2"></i>Se muestra en el banner informativo azul</li>
                            </ul>
                            <hr>
                        </div>
                        
                        <div class="col-12">
                            <h6 class="text-primary"><i class="fas fa-file-alt me-2"></i>Descripción</h6>
                            <p><strong>Campo obligatorio.</strong> Información descriptiva de la foto.</p>
                            <ul class="list-unstyled ms-3">
                                <li><i class="fas fa-info text-info me-2"></i>Descripción: entre 3 y 200 caracteres</li>
                                <li><i class="fas fa-info text-info me-2"></i>Ejemplos: "Vista frontal del equipo", "Detalle de conexiones", "Placa de características"</li>
                            </ul>
                            <hr>
                        </div>
                        
                        <div class="col-12">
                            <h6 class="text-primary"><i class="fas fa-image me-2"></i>Archivo de Imagen</h6>
                            <p><strong>Campo obligatorio.</strong> Archivo de imagen.</p>
                            <ul class="list-unstyled ms-3">
                                <li><i class="fas fa-check text-success me-2"></i>Formatos soportados: JPG, JPEG, PNG, GIF, WEBP</li>
                                <li><i class="fas fa-check text-success me-2"></i>Tamaño máximo: 5MB</li>
                                <li><i class="fas fa-exclamation-triangle text-warning me-2"></i>La imagen tardará unos segundos en subirse</li>
                            </ul>
                            <hr>
                        </div>
                        
                        <div class="col-12">
                            <h6 class="text-primary"><i class="fas fa-shield-lock me-2"></i>Privacidad de la Foto</h6>
                            <p><strong>Campo opcional.</strong> Control de acceso a la foto.</p>
                            <ul class="list-unstyled ms-3">
                                <li><i class="fas fa-unlock text-success me-2"></i>Pública: visible para todos los usuarios</li>
                                <li><i class="fas fa-lock text-danger me-2"></i>Privada: solo usuarios autorizados pueden verla</li>
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
    <script type="text/javascript" src="formularioFoto.js"></script>

</body>

</html>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Obtener parámetros URL para determinar si es edición
    const urlParams = new URLSearchParams(window.location.search);
    const idFoto = urlParams.get('id');
    const modo = urlParams.get('modo') || 'nuevo';
    const idElemento = urlParams.get('id_elemento');
    
    // Debug: Mostrar parámetros recibidos en consola
    console.log('=== Parámetros de URL ===');
    console.log('Modo:', modo);
    console.log('ID Foto:', idFoto);
    console.log('ID Elemento:', idElemento);
    console.log('========================');
    
    // Si no hay id_elemento en modo nuevo, mostrar advertencia
    if (modo === 'nuevo' && !idElemento) {
        console.warn('⚠️ No se proporcionó ID de elemento. El usuario será redirigido.');
    }
    
    // Configurar página según el modo
    if (modo === 'editar' && idFoto) {
        document.getElementById('page-title').textContent = 'Editar Foto';
        document.getElementById('breadcrumb-title').textContent = 'Editar Foto';
        document.getElementById('btnSalvarFoto').innerHTML = '<i class="fas fa-save me-2"></i>Actualizar Foto';
        document.getElementById('id_foto_elemento').value = idFoto;
        
        // Cargar datos de la foto para edición
        cargarDatosFoto(idFoto);
    }
    
    // Manejar cambio en el checkbox de privacidad
    const privadoCheckbox = document.getElementById('privado_foto');
    const privadoText = document.getElementById('privado-text');
    
    if (privadoCheckbox && privadoText) {
        privadoCheckbox.addEventListener('change', function() {
            if (this.checked) {
                privadoText.textContent = 'Foto Privada';
                privadoText.className = 'text-danger';
            } else {
                privadoText.textContent = 'Foto Pública';
                privadoText.className = 'text-success';
            }
        });
    }
    
    // Validación en tiempo real para descripción
    const descripcionInput = document.getElementById('descripcion_foto_elemento');
    if (descripcionInput) {
        descripcionInput.addEventListener('input', function() {
            if (this.value.length >= 3 && this.value.length <= 200) {
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
    
    // Manejo de vista previa de imagen
    const archivoInput = document.getElementById('archivo_foto');
    const filePreview = document.getElementById('file-preview');
    
    if (archivoInput && filePreview) {
        archivoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            
            if (file) {
                // Validar tamaño del archivo (5MB máximo)
                if (file.size > 5 * 1024 * 1024) {
                    toastr.error('La imagen es demasiado grande. Máximo 5MB permitido.');
                    this.value = '';
                    showDefaultFilePreview();
                    return;
                }
                
                // Validar tipo de archivo
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
                if (!allowedTypes.includes(file.type)) {
                    toastr.error('Formato de imagen no válido. Use JPG, JPEG, PNG, GIF o WEBP.');
                    this.value = '';
                    showDefaultFilePreview();
                    return;
                }
                
                // Mostrar vista previa de la imagen
                const reader = new FileReader();
                reader.onload = function(e) {
                    const fileSize = (file.size / 1024).toFixed(2);
                    filePreview.innerHTML = `
                        <img src="${e.target.result}" class="img-fluid rounded" style="max-height: 180px; max-width: 100%;" alt="Vista previa">
                        <p class="text-muted small mt-2 mb-0">${file.name}</p>
                        <p class="text-muted small mb-0">${fileSize} KB</p>
                    `;
                };
                reader.readAsDataURL(file);
                
                // Marcar como válido
                archivoInput.classList.remove('is-invalid');
                archivoInput.classList.add('is-valid');
            } else {
                showDefaultFilePreview();
            }
        });
    }
});

// Función para cargar datos de foto en modo edición
function cargarDatosFoto(idFoto) {
    console.log('Cargando datos de foto ID:', idFoto);
}

// Función para mostrar vista previa por defecto
function showDefaultFilePreview() {
    const filePreview = document.getElementById('file-preview');
    if (filePreview) {
        filePreview.innerHTML = `
            <i class="fas fa-image text-muted" style="font-size: 3rem;"></i>
            <p class="text-muted mt-2 mb-0">Sin imagen</p>
        `;
    }
}

// Función para mostrar imagen existente
function showExistingFile(filePath) {
    const filePreview = document.getElementById('file-preview');
    if (filePreview && filePath) {
        const fileName = filePath.split('/').pop();
        filePreview.innerHTML = `
            <img src="../../public/img/fotos_elementos/${filePath}" class="img-fluid rounded" style="max-height: 180px; max-width: 100%;" alt="Foto actual">
            <p class="text-muted small mt-2 mb-0">Imagen actual:</p>
            <p class="text-muted small mb-0">${fileName}</p>
            <a href="../../public/img/fotos_elementos/${filePath}" target="_blank" class="btn btn-sm btn-outline-primary mt-2">
                <i class="bi bi-eye me-1"></i>Ver en tamaño completo
            </a>
        `;
    }
}
</script>
