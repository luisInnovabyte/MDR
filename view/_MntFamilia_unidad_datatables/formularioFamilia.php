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
                <a class="breadcrumb-item" href="index.php">Mantenimiento Familias</a>
                <span class="breadcrumb-item active" id="breadcrumb-title">Nueva Familia</span>
            </nav>
        </div><!-- br-pageheader -->
        
        <div class="br-pagetitle">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <h4 class="mb-0 me-2" id="page-title">Nueva Familia</h4>
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
                
                <!-- Formulario de Familia -->
                <form id="formFamilia">
                    <!-- Campo oculto para ID de la familia -->
                    <input type="hidden" name="id_familia" id="id_familia">

                    <!-- SECCIÓN: Información Básica -->
                    <div class="card mb-4 border-primary">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0 tx-bold">
                                <i class="fas fa-info-circle me-2"></i>Información Básica de la Familia
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-12 col-md-6">
                                    <label for="codigo_familia" class="form-label">Código familia: <span class="tx-danger">*</span></label>
                                    <input type="text" class="form-control" name="codigo_familia" id="codigo_familia" maxlength="20" placeholder="Ej: FAM001, TOLDOS, etc..." required>
                                    <div class="invalid-feedback small-invalid-feedback">Ingrese un código válido (máximo 20 caracteres, único)</div>
                                    <small class="form-text text-muted">Código único identificativo (máximo 20 caracteres)</small>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label">Estado:</label>
                                    <div class="form-check form-switch mt-2">
                                        <input class="form-check-input" type="checkbox" name="activo_familia" id="activo_familia" checked disabled>
                                        <label class="form-check-label" for="activo_familia">
                                            <span id="estado-text">Familia Activa</span>
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">El estado se establece automáticamente</small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12 col-md-6">
                                    <label for="nombre_familia" class="form-label">Nombre familia: <span class="tx-danger">*</span></label>
                                    <input type="text" class="form-control" name="nombre_familia" id="nombre_familia" maxlength="100" placeholder="Ej: Toldos exteriores, Parasoles, etc..." required>
                                    <div class="invalid-feedback small-invalid-feedback">Ingrese un nombre válido (mínimo 3 y máximo 100 caracteres)</div>
                                    <small class="form-text text-muted">Nombre descriptivo en español</small>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="name_familia" class="form-label">English name: <span class="tx-danger">*</span></label>
                                    <input type="text" class="form-control" name="name_familia" id="name_familia" maxlength="100" placeholder="Ej: Outdoor awnings, Parasols, etc..." required>
                                    <div class="invalid-feedback small-invalid-feedback">Ingrese un nombre válido en inglés (mínimo 3 y máximo 100 caracteres)</div>
                                    <small class="form-text text-muted">Nombre descriptivo en inglés</small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12 col-md-6">
                                    <label for="descr_familia" class="form-label">Descripción:</label>
                                    <textarea class="form-control" name="descr_familia" id="descr_familia" maxlength="255" rows="3" placeholder="Descripción detallada de la familia de productos..."></textarea>
                                    <div class="invalid-feedback small-invalid-feedback">La descripción no puede exceder los 255 caracteres</div>
                                    <small class="form-text text-muted">
                                        Descripción opcional de la familia (máximo 255 caracteres)
                                        <span class="float-end">
                                            <span id="char-count">0</span>/255 caracteres
                                        </span>
                                    </small>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="id_unidad_familia" class="form-label">Unidad de medida:</label>
                                    <div class="input-group">
                                        <input type="text" 
                                               class="form-control" 
                                               id="unidad_medida_display" 
                                               placeholder="Seleccionar unidad de medida..."
                                               readonly>
                                        <input type="hidden" name="id_unidad_familia" id="id_unidad_familia">
                                        <button type="button" 
                                                class="btn btn-outline-secondary" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#modalBuscarUnidad">
                                            <i class="fas fa-search"></i>
                                        </button>
                                        <button type="button" 
                                                class="btn btn-outline-danger" 
                                                id="limpiarUnidad"
                                                title="Limpiar selección">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                    <div class="invalid-feedback small-invalid-feedback">Seleccione una unidad de medida válida</div>
                                    <small class="form-text text-muted">
                                        Haga clic en el botón de búsqueda para seleccionar una unidad
                                        <div id="unidad-descripcion" class="mt-1 p-2 bg-light border rounded" style="display: none;">
                                            <strong>Descripción:</strong> <span id="unidad-descr-text"></span>
                                        </div>
                                    </small>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <label for="imagen_familia" class="form-label">Imagen de la familia:</label>
                                    <input type="file" class="form-control" name="imagen_familia" id="imagen_familia" accept="image/*">
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

                    <!-- Botones de acción -->
                    <div class="card">
                        <div class="card-body text-center">
                            <button type="button" name="action" id="btnSalvarFamilia" class="btn btn-primary btn-lg me-3">
                                <i class="fas fa-save me-2"></i>Guardar Familia
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

        </div>

    <!-- Modal de búsqueda de unidades -->
    <div class="modal fade" id="modalBuscarUnidad" tabindex="-1" aria-labelledby="modalBuscarUnidadLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalBuscarUnidadLabel">
                        <i class="fas fa-search me-2"></i>Seleccionar Unidad de Medida
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <p class="text-muted">Seleccione una unidad de medida de la tabla o use los filtros para encontrar la unidad específica.</p>
                    </div>
                    <table id="tablaUnidadesBusqueda" class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Símbolo</th>
                                <th>Descripción</th>
                                <th>Estado</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Se carga dinámicamente -->
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Ayuda del Formulario -->
    <div class="modal fade" id="modalAyudaFormulario" tabindex="-1" aria-labelledby="modalAyudaFormularioLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalAyudaFormularioLabel">
                        <i class="fas fa-question-circle me-2"></i>Ayuda - Formulario de Familias
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <h6 class="text-primary"><i class="fas fa-barcode me-2"></i>Código de Familia</h6>
                            <p><strong>Campo obligatorio.</strong> Identificador único de la familia.</p>
                            <ul class="list-unstyled ms-3">
                                <li><i class="fas fa-check text-success me-2"></i>Máximo 20 caracteres</li>
                                <li><i class="fas fa-check text-success me-2"></i>Se convierte automáticamente a mayúsculas</li>
                                <li><i class="fas fa-check text-success me-2"></i>No puede contener espacios</li>
                                <li><i class="fas fa-check text-success me-2"></i>Debe ser único en el sistema</li>
                            </ul>
                            <hr>
                        </div>
                        
                        <div class="col-12">
                            <h6 class="text-primary"><i class="fas fa-tag me-2"></i>Nombres de Familia</h6>
                            <p><strong>Campos obligatorios.</strong> Nombres descriptivos de la familia en español e inglés.</p>
                            <ul class="list-unstyled ms-3">
                                <li><i class="fas fa-check text-success me-2"></i>Entre 3 y 100 caracteres cada uno</li>
                                <li><i class="fas fa-check text-success me-2"></i>Deben ser descriptivos y claros</li>
                                <li><i class="fas fa-check text-success me-2"></i>Nombre en español y traducción al inglés</li>
                            </ul>
                            <hr>
                        </div>
                        
                        <div class="col-12">
                            <h6 class="text-primary"><i class="fas fa-balance-scale me-2"></i>Unidad de Medida</h6>
                            <p><strong>Campo opcional.</strong> Unidad de medida asociada a esta familia de productos.</p>
                            <ul class="list-unstyled ms-3">
                                <li><i class="fas fa-search text-info me-2"></i>Haga clic en el botón de búsqueda para abrir el modal de selección</li>
                                <li><i class="fas fa-table text-info me-2"></i>Use los filtros y búsqueda de la tabla para encontrar la unidad deseada</li>
                                <li><i class="fas fa-check text-success me-2"></i>Haga clic en "Seleccionar" para elegir la unidad de medida</li>
                                <li><i class="fas fa-times text-danger me-2"></i>Use el botón X para limpiar la selección</li>
                                <li><i class="fas fa-info-circle text-info me-2"></i>Se muestra la descripción de la unidad al seleccionarla</li>
                                <li><i class="fas fa-tools text-secondary me-2"></i>Útil para categorizar productos por su forma de medición</li>
                            </ul>
                            <hr>
                        </div>
                        
                        <div class="col-12">
                            <h6 class="text-primary"><i class="fas fa-file-alt me-2"></i>Descripción, Imagen y Estado</h6>
                            <p><strong>Información adicional.</strong> Campos complementarios de la familia.</p>
                            <ul class="list-unstyled ms-3">
                                <li><i class="fas fa-info text-info me-2"></i>Descripción: campo opcional, máximo 255 caracteres</li>
                                <li><i class="fas fa-image text-info me-2"></i>Imagen: opcional, máximo 2MB (JPG, PNG, GIF)</li>
                                <li><i class="fas fa-lock text-warning me-2"></i>Estado: se establece automáticamente (activo para nuevas familias)</li>
                                <li><i class="fas fa-eye text-secondary me-2"></i>Vista previa de imagen en tiempo real</li>
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
    
    <!-- Estilos específicos para el modal de unidades -->
    <style>
        /* Estilos para el modal de búsqueda */
        #modalBuscarUnidad .modal-lg {
            max-width: 900px;
        }
        
        /* Estilos para la tabla dentro del modal */
        #tablaUnidadesBusqueda {
            font-size: 0.9rem;
        }
        
        #tablaUnidadesBusqueda th {
            background-color: #f8f9fa;
            font-weight: 600;
            font-size: 0.85rem;
            border-bottom: 2px solid #dee2e6;
        }
        
        #tablaUnidadesBusqueda td {
            vertical-align: middle;
            padding: 0.5rem;
        }
        
        /* Botón de selección */
        .seleccionar-unidad {
            font-size: 0.8rem;
            padding: 0.25rem 0.5rem;
        }
        
        /* Campo de búsqueda personalizado */
        .input-group .btn-outline-secondary:hover {
            background-color: #6c757d;
            border-color: #6c757d;
            color: white;
        }
        
        .input-group .btn-outline-danger:hover {
            background-color: #dc3545;
            border-color: #dc3545;
            color: white;
        }
        
        /* Responsivo para tabla en modal */
        @media (max-width: 768px) {
            #modalBuscarUnidad .modal-lg {
                max-width: 95%;
                margin: 1rem;
            }
            
            #tablaUnidadesBusqueda {
                font-size: 0.8rem;
            }
            
            .seleccionar-unidad {
                font-size: 0.7rem;
                padding: 0.2rem 0.4rem;
            }
        }
    </style>
    
    <script type="text/javascript" src="formularioFamilia.js"></script>

</body>

</html>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Obtener parámetros URL para determinar si es edición
    const urlParams = new URLSearchParams(window.location.search);
    const idFamilia = urlParams.get('id');
    const modo = urlParams.get('modo') || 'nuevo';
    
    // Configurar página según el modo
    if (modo === 'editar' && idFamilia) {
        document.getElementById('page-title').textContent = 'Editar Familia';
        document.getElementById('breadcrumb-title').textContent = 'Editar Familia';
        document.getElementById('btnSalvarFamilia').innerHTML = '<i class="fas fa-save me-2"></i>Actualizar Familia';
        document.getElementById('id_familia').value = idFamilia;
        
        // Cargar datos de la familia para edición
        cargarDatosFamilia(idFamilia);
    }
    
    // Contador de caracteres para la descripción
    const descrTextarea = document.getElementById('descr_familia');
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
    
    // El switch de estado está deshabilitado, no necesita event listener
    
    // Validación en tiempo real para código familia
    const codigoInput = document.getElementById('codigo_familia');
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
    const nombreInput = document.getElementById('nombre_familia');
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
    const nameInput = document.getElementById('name_familia');
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
    
    // Manejo de vista previa de imagen
    const imagenInput = document.getElementById('imagen_familia');
    const imagePreview = document.getElementById('image-preview');
    
    if (imagenInput && imagePreview) {
        imagenInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            
            if (file) {
                // Validar tamaño del archivo (2MB máximo)
                if (file.size > 2 * 1024 * 1024) {
                    toastr.error('La imagen es demasiado grande. Máximo 2MB permitido.');
                    this.value = '';
                    showDefaultImagePreview();
                    return;
                }
                
                // Validar tipo de archivo
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
                if (!allowedTypes.includes(file.type)) {
                    toastr.error('Formato de imagen no válido. Use JPG, PNG o GIF.');
                    this.value = '';
                    showDefaultImagePreview();
                    return;
                }
                
                // Mostrar vista previa
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.innerHTML = `<img src="${e.target.result}" alt="Vista previa" style="max-width: 100%; max-height: 100px; border-radius: 4px;">`;
                };
                reader.readAsDataURL(file);
                
                // Marcar como válido
                imagenInput.classList.remove('is-invalid');
                imagenInput.classList.add('is-valid');
            } else {
                showDefaultImagePreview();
            }
        });
    }
    
    // Las funciones showDefaultImagePreview y showExistingImage están definidas en formularioFamilia.js
});

// Función para cargar datos de familia en modo edición
function cargarDatosFamilia(idFamilia) {
    // Esta función se implementará en el archivo formularioFamilia.js
    console.log('Cargando datos de familia ID:', idFamilia);
}
</script>