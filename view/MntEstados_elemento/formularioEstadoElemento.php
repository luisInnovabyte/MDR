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
                <a class="breadcrumb-item" href="index.php">Mantenimiento Estados de Elemento</a>
                <span class="breadcrumb-item active" id="breadcrumb-title">Nuevo Estado de Elemento</span>
            </nav>
        </div><!-- br-pageheader -->
        
        <div class="br-pagetitle">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <h4 class="mb-0 me-2" id="page-title">Nuevo Estado de Elemento</h4>
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
                
                <!-- Formulario de Estado de Elemento -->
                <form id="formEstadoElemento">
                    <!-- Campo oculto para ID del estado de elemento -->
                    <input type="hidden" name="id_estado_elemento" id="id_estado_elemento">

                    <!-- SECCIÓN: Información Básica -->
                    <div class="card mb-4 border-primary">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0 tx-bold">
                                <i class="fas fa-flag me-2"></i>Información Básica del Estado de Elemento
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-12 col-md-6">
                                    <label for="codigo_estado_elemento" class="form-label">Código estado: <span class="tx-danger">*</span></label>
                                    <input type="text" class="form-control" name="codigo_estado_elemento" id="codigo_estado_elemento" maxlength="20" placeholder="Ej: DISP, ALQU, REPA, etc..." required>
                                    <div class="invalid-feedback small-invalid-feedback">Ingrese un código válido (máximo 20 caracteres, único)</div>
                                    <small class="form-text text-muted">Código único identificativo (máximo 20 caracteres)</small>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label">Estado:</label>
                                    <div class="form-check form-switch mt-2">
                                        <input class="form-check-input" type="checkbox" name="activo_estado_elemento" id="activo_estado_elemento" checked disabled>
                                        <label class="form-check-label" for="activo_estado_elemento">
                                            <span id="estado-text">Estado de Elemento Activo</span>
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">El estado se establece automáticamente</small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12">
                                    <label for="descripcion_estado_elemento" class="form-label">Descripción del estado: <span class="tx-danger">*</span></label>
                                    <input type="text" class="form-control" name="descripcion_estado_elemento" id="descripcion_estado_elemento" maxlength="50" placeholder="Ej: Disponible, Alquilado, En reparación, etc..." required>
                                    <div class="invalid-feedback small-invalid-feedback">Ingrese una descripción válida (mínimo 3 y máximo 50 caracteres)</div>
                                    <small class="form-text text-muted">Descripción clara del estado de elemento</small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12 col-md-6">
                                    <label for="color_estado_elemento" class="form-label">Color del estado:</label>
                                    <div class="input-group">
                                        <input type="color" class="form-control form-control-color" name="color_estado_elemento" id="color_estado_elemento" value="#4CAF50" title="Seleccione el color para este estado">
                                        <input type="text" class="form-control" id="color_estado_elemento_text" placeholder="#4CAF50" readonly>
                                    </div>
                                    <div class="invalid-feedback small-invalid-feedback">Seleccione un color válido para el estado</div>
                                    <small class="form-text text-muted">Color que representará este estado en la interfaz</small>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="permite_alquiler_estado_elemento" class="form-label">Permite alquiler:</label>
                                    <div class="form-check form-switch mt-2">
                                        <input class="form-check-input" type="checkbox" name="permite_alquiler_estado_elemento" id="permite_alquiler_estado_elemento" checked>
                                        <label class="form-check-label" for="permite_alquiler_estado_elemento">
                                            <span id="permite-alquiler-text">Los elementos en este estado SÍ pueden ser alquilados</span>
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">Controla si el elemento está disponible para alquiler</small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12">
                                    <label for="observaciones_estado_elemento" class="form-label">Observaciones:</label>
                                    <textarea class="form-control" name="observaciones_estado_elemento" id="observaciones_estado_elemento" maxlength="500" rows="4" placeholder="Observaciones adicionales sobre este estado de elemento..."></textarea>
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
                            <button type="button" name="action" id="btnSalvarEstadoElemento" class="btn btn-primary btn-lg me-3">
                                <i class="fas fa-save me-2"></i>Guardar Estado de Elemento
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
                        <i class="fas fa-question-circle me-2"></i>Ayuda - Formulario de Estados de Elemento
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <h6 class="text-primary"><i class="fas fa-barcode me-2"></i>Código del Estado</h6>
                            <p><strong>Campo obligatorio.</strong> Identificador único del estado de elemento.</p>
                            <ul class="list-unstyled ms-3">
                                <li><i class="fas fa-check text-success me-2"></i>Máximo 20 caracteres</li>
                                <li><i class="fas fa-check text-success me-2"></i>Se convierte automáticamente a mayúsculas</li>
                                <li><i class="fas fa-check text-success me-2"></i>No puede contener espacios</li>
                                <li><i class="fas fa-check text-success me-2"></i>Debe ser único en el sistema</li>
                                <li><i class="fas fa-lightbulb text-warning me-2"></i>Ejemplos: DISP, ALQU, REPA, BAJA, TERC, DEPO, MANT, TRAN</li>
                            </ul>
                            <hr>
                        </div>
                        
                        <div class="col-12">
                            <h6 class="text-primary"><i class="fas fa-flag me-2"></i>Descripción del Estado</h6>
                            <p><strong>Campo obligatorio.</strong> Descripción clara del estado de elemento.</p>
                            <ul class="list-unstyled ms-3">
                                <li><i class="fas fa-check text-success me-2"></i>Entre 3 y 50 caracteres</li>
                                <li><i class="fas fa-check text-success me-2"></i>Debe ser descriptivo y claro</li>
                                <li><i class="fas fa-lightbulb text-warning me-2"></i>Ejemplos: "Disponible", "Alquilado", "En reparación", "Dado de baja"</li>
                            </ul>
                            <hr>
                        </div>
                        
                        <div class="col-12">
                            <h6 class="text-primary"><i class="fas fa-palette me-2"></i>Color del Estado</h6>
                            <p><strong>Campo opcional.</strong> Color que representará este estado en la interfaz.</p>
                            <ul class="list-unstyled ms-3">
                                <li><i class="fas fa-check text-success me-2"></i>Se puede seleccionar usando el selector de color</li>
                                <li><i class="fas fa-eye text-info me-2"></i>Mejora la visualización y reconocimiento rápido</li>
                                <li><i class="fas fa-lightbulb text-warning me-2"></i>Sugerencias: Verde (#4CAF50) para disponible, rojo (#F44336) para baja, naranja (#FF9800) para reparación</li>
                                <li><i class="fas fa-info-circle text-info me-2"></i>El color se muestra en formato hexadecimal (ej: #4CAF50)</li>
                            </ul>
                            <hr>
                        </div>
                        
                        <div class="col-12">
                            <h6 class="text-primary"><i class="fas fa-check-circle me-2"></i>Permite Alquiler</h6>
                            <p><strong>Campo importante.</strong> Controla si los elementos en este estado pueden ser alquilados.</p>
                            <ul class="list-unstyled ms-3">
                                <li><i class="fas fa-toggle-on text-success me-2"></i>Activado: El elemento puede incluirse en presupuestos y alquileres</li>
                                <li><i class="fas fa-toggle-off text-danger me-2"></i>Desactivado: El elemento NO está disponible para alquiler</li>
                                <li><i class="fas fa-lightbulb text-warning me-2"></i>Ejemplos Sí: Disponible, De terceros</li>
                                <li><i class="fas fa-lightbulb text-warning me-2"></i>Ejemplos No: Alquilado, En reparación, Dado de baja</li>
                            </ul>
                            <hr>
                        </div>
                        
                        <div class="col-12">
                            <h6 class="text-primary"><i class="fas fa-file-alt me-2"></i>Observaciones y Estado</h6>
                            <p><strong>Información adicional.</strong> Campos complementarios del estado de elemento.</p>
                            <ul class="list-unstyled ms-3">
                                <li><i class="fas fa-note-sticky text-info me-2"></i>Observaciones: campo opcional, máximo 500 caracteres</li>
                                <li><i class="fas fa-info text-info me-2"></i>Use las observaciones para detalles sobre cuándo usar este estado</li>
                                <li><i class="fas fa-toggle-on text-success me-2"></i>Estado: se establece automáticamente como activo para nuevos estados</li>
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
    <script type="text/javascript" src="formularioEstadoElemento.js"></script>

</body>

</html>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Obtener parámetros URL para determinar si es edición
    const urlParams = new URLSearchParams(window.location.search);
    const idEstadoElemento = urlParams.get('id');
    const modo = urlParams.get('modo') || 'nuevo';
    
    // Configurar página según el modo
    if (modo === 'editar' && idEstadoElemento) {
        document.getElementById('page-title').textContent = 'Editar Estado de Elemento';
        document.getElementById('breadcrumb-title').textContent = 'Editar Estado de Elemento';
        document.getElementById('btnSalvarEstadoElemento').innerHTML = '<i class="fas fa-save me-2"></i>Actualizar Estado de Elemento';
        document.getElementById('id_estado_elemento').value = idEstadoElemento;
        
        // Cargar datos del estado de elemento para edición
        cargarDatosEstadoElemento(idEstadoElemento);
    }
    
    // Contador de caracteres para las observaciones
    const obsTextarea = document.getElementById('observaciones_estado_elemento');
    const charCount = document.getElementById('char-count');
    
    if (obsTextarea && charCount) {
        obsTextarea.addEventListener('input', function() {
            const currentLength = this.value.length;
            charCount.textContent = currentLength;
            
            // Cambiar color según la proximidad al límite
            if (currentLength > 400) {
                charCount.style.color = '#dc3545'; // Rojo
            } else if (currentLength > 300) {
                charCount.style.color = '#ffc107'; // Amarillo
            } else {
                charCount.style.color = '#6c757d'; // Gris normal
            }
        });
    }
    
    // Validación en tiempo real para código del estado
    const codigoInput = document.getElementById('codigo_estado_elemento');
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
    
    // Validación en tiempo real para descripción del estado
    const descripcionInput = document.getElementById('descripcion_estado_elemento');
    if (descripcionInput) {
        descripcionInput.addEventListener('input', function() {
            if (this.value.length >= 3 && this.value.length <= 50) {
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
    
    // Sincronización del selector de color y campo de texto
    const colorInput = document.getElementById('color_estado_elemento');
    const colorTextInput = document.getElementById('color_estado_elemento_text');
    
    if (colorInput && colorTextInput) {
        // Actualizar campo de texto cuando cambia el color
        colorInput.addEventListener('input', function() {
            colorTextInput.value = this.value;
        });
        
        // Inicializar el valor del campo de texto
        colorTextInput.value = colorInput.value;
    }
    
    // Actualizar texto del switch de permite alquiler
    const permiteAlquilerSwitch = document.getElementById('permite_alquiler_estado_elemento');
    const permiteAlquilerText = document.getElementById('permite-alquiler-text');
    
    if (permiteAlquilerSwitch && permiteAlquilerText) {
        permiteAlquilerSwitch.addEventListener('change', function() {
            if (this.checked) {
                permiteAlquilerText.textContent = 'Los elementos en este estado SÍ pueden ser alquilados';
                permiteAlquilerText.parentElement.classList.remove('text-danger');
                permiteAlquilerText.parentElement.classList.add('text-success');
            } else {
                permiteAlquilerText.textContent = 'Los elementos en este estado NO pueden ser alquilados';
                permiteAlquilerText.parentElement.classList.remove('text-success');
                permiteAlquilerText.parentElement.classList.add('text-danger');
            }
        });
    }
});

// Función para cargar datos de estado de elemento en modo edición
function cargarDatosEstadoElemento(idEstadoElemento) {
    // Esta función se implementará en el archivo formularioEstadoElemento.js
    console.log('Cargando datos de estado de elemento ID:', idEstadoElemento);
}
</script>
