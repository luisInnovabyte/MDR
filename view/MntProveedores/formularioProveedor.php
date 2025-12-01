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
                <a class="breadcrumb-item" href="index.php">Mantenimiento Proveedores</a>
                <span class="breadcrumb-item active" id="breadcrumb-title">Nuevo Proveedor</span>
            </nav>
        </div><!-- br-pageheader -->
        
        <div class="br-pagetitle">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <h4 class="mb-0 me-2" id="page-title">Nuevo Proveedor</h4>
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
                
                <!-- Formulario de Proveedor -->
                <form id="formProveedor">
                    <!-- Campo oculto para ID del proveedor -->
                    <input type="hidden" name="id_proveedor" id="id_proveedor">

                    <!-- SECCIÓN: Información Básica -->
                    <div class="card mb-4 border-primary">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0 tx-bold">
                                <i class="fas fa-info-circle me-2"></i>Información Básica del Proveedor
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-12 col-md-6">
                                    <label for="codigo_proveedor" class="form-label">Código proveedor: <span class="tx-danger">*</span></label>
                                    <input type="text" class="form-control" name="codigo_proveedor" id="codigo_proveedor" maxlength="20" placeholder="Ej: PROV001, FERR123, etc..." required>
                                    <div class="invalid-feedback small-invalid-feedback">Ingrese un código válido (máximo 20 caracteres, único)</div>
                                    <small class="form-text text-muted">Código único identificativo (máximo 20 caracteres)</small>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="nombre_proveedor" class="form-label">Nombre proveedor: <span class="tx-danger">*</span></label>
                                    <input type="text" class="form-control" name="nombre_proveedor" id="nombre_proveedor" maxlength="255" placeholder="Ej: Ferretería González, Materiales Construcción, etc..." required>
                                    <div class="invalid-feedback small-invalid-feedback">Ingrese un nombre válido (mínimo 3 y máximo 255 caracteres)</div>
                                    <small class="form-text text-muted">Nombre completo del proveedor</small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12 col-md-6">
                                    <label for="nif_proveedor" class="form-label">NIF/CIF:</label>
                                    <input type="text" class="form-control" name="nif_proveedor" id="nif_proveedor" maxlength="20" placeholder="Ej: 12345678A, B12345678, etc...">
                                    <div class="invalid-feedback small-invalid-feedback">Ingrese un NIF/CIF válido</div>
                                    <small class="form-text text-muted">Número de identificación fiscal</small>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="persona_contacto_proveedor" class="form-label">Persona de contacto:</label>
                                    <input type="text" class="form-control" name="persona_contacto_proveedor" id="persona_contacto_proveedor" maxlength="255" placeholder="Ej: Juan García, María López, etc...">
                                    <small class="form-text text-muted">Persona principal para contactar</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECCIÓN: Dirección Principal -->
                    <div class="card mb-4 border-info">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0 tx-bold">
                                <i class="fas fa-map-marker-alt me-2"></i>Dirección Principal
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-12">
                                    <label for="direccion_proveedor" class="form-label">Dirección:</label>
                                    <input type="text" class="form-control" name="direccion_proveedor" id="direccion_proveedor" maxlength="255" placeholder="Ej: Calle Mayor, 123, 2º A">
                                    <small class="form-text text-muted">Dirección completa del proveedor</small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12 col-md-4">
                                    <label for="cp_proveedor" class="form-label">Código Postal:</label>
                                    <input type="text" class="form-control" name="cp_proveedor" id="cp_proveedor" maxlength="10" placeholder="Ej: 28001, 08080">
                                </div>
                                <div class="col-12 col-md-4">
                                    <label for="poblacion_proveedor" class="form-label">Población:</label>
                                    <input type="text" class="form-control" name="poblacion_proveedor" id="poblacion_proveedor" maxlength="100" placeholder="Ej: Madrid, Barcelona">
                                </div>
                                <div class="col-12 col-md-4">
                                    <label for="provincia_proveedor" class="form-label">Provincia:</label>
                                    <input type="text" class="form-control" name="provincia_proveedor" id="provincia_proveedor" maxlength="100" placeholder="Ej: Madrid, Barcelona">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECCIÓN: Información de Contacto -->
                    <div class="card mb-4 border-success">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0 tx-bold">
                                <i class="fas fa-phone me-2"></i>Información de Contacto
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-12 col-md-6">
                                    <label for="telefono_proveedor" class="form-label">Teléfono:</label>
                                    <input type="text" class="form-control" name="telefono_proveedor" id="telefono_proveedor" maxlength="255" placeholder="Ej: 912345678, 600123456">
                                    <small class="form-text text-muted">Teléfono principal de contacto</small>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="fax_proveedor" class="form-label">Fax:</label>
                                    <input type="text" class="form-control" name="fax_proveedor" id="fax_proveedor" maxlength="50" placeholder="Ej: 912345679">
                                    <small class="form-text text-muted">Número de fax (opcional)</small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12 col-md-6">
                                    <label for="email_proveedor" class="form-label">Email:</label>
                                    <input type="email" class="form-control" name="email_proveedor" id="email_proveedor" maxlength="255" placeholder="proveedor@ejemplo.com">
                                    <div class="invalid-feedback small-invalid-feedback">Ingrese un email válido</div>
                                    <small class="form-text text-muted">Email principal de contacto</small>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="web_proveedor" class="form-label">Página Web:</label>
                                    <input type="url" class="form-control" name="web_proveedor" id="web_proveedor" maxlength="255" placeholder="https://www.proveedor.com">
                                    <div class="invalid-feedback small-invalid-feedback">Ingrese una URL válida</div>
                                    <small class="form-text text-muted">Sitio web del proveedor</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECCIÓN: Dirección SAT (Servicio Técnico) -->
                    <div class="card mb-4 border-warning">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="mb-0 tx-bold">
                                <i class="fas fa-tools me-2"></i>Dirección SAT (Servicio de Asistencia Técnica)
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-12">
                                    <label for="direccion_sat_proveedor" class="form-label">Dirección SAT:</label>
                                    <input type="text" class="form-control" name="direccion_sat_proveedor" id="direccion_sat_proveedor" maxlength="255" placeholder="Dirección del servicio técnico...">
                                    <small class="form-text text-muted">Dirección del servicio de asistencia técnica</small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12 col-md-4">
                                    <label for="cp_sat_proveedor" class="form-label">CP SAT:</label>
                                    <input type="text" class="form-control" name="cp_sat_proveedor" id="cp_sat_proveedor" maxlength="10" placeholder="CP del SAT...">
                                </div>
                                <div class="col-12 col-md-4">
                                    <label for="poblacion_sat_proveedor" class="form-label">Población SAT:</label>
                                    <input type="text" class="form-control" name="poblacion_sat_proveedor" id="poblacion_sat_proveedor" maxlength="100" placeholder="Población del SAT...">
                                </div>
                                <div class="col-12 col-md-4">
                                    <label for="provincia_sat_proveedor" class="form-label">Provincia SAT:</label>
                                    <input type="text" class="form-control" name="provincia_sat_proveedor" id="provincia_sat_proveedor" maxlength="100" placeholder="Provincia del SAT...">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12 col-md-4">
                                    <label for="telefono_sat_proveedor" class="form-label">Teléfono SAT:</label>
                                    <input type="text" class="form-control" name="telefono_sat_proveedor" id="telefono_sat_proveedor" maxlength="255" placeholder="Teléfono del SAT...">
                                </div>
                                <div class="col-12 col-md-4">
                                    <label for="fax_sat_proveedor" class="form-label">Fax SAT:</label>
                                    <input type="text" class="form-control" name="fax_sat_proveedor" id="fax_sat_proveedor" maxlength="50" placeholder="Fax del SAT...">
                                </div>
                                <div class="col-12 col-md-4">
                                    <label for="email_sat_proveedor" class="form-label">Email SAT:</label>
                                    <input type="email" class="form-control" name="email_sat_proveedor" id="email_sat_proveedor" maxlength="255" placeholder="sat@proveedor.com">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECCIÓN: Observaciones -->
                    <div class="card mb-4 border-secondary">
                        <div class="card-header bg-secondary text-white">
                            <h5 class="mb-0 tx-bold">
                                <i class="fas fa-clipboard me-2"></i>Observaciones
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <label for="observaciones_proveedor" class="form-label">Observaciones:</label>
                                    <textarea class="form-control" name="observaciones_proveedor" id="observaciones_proveedor" rows="4" placeholder="Observaciones sobre el proveedor, condiciones especiales, notas importantes, etc..."></textarea>
                                    <small class="form-text text-muted">
                                        Información adicional sobre el proveedor
                                        <span class="float-end">
                                            <span id="obs-char-count">0</span>/65535 caracteres
                                        </span>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECCIÓN: Estado del Proveedor -->
                    <div class="card shadow-sm">
                        <div class="card-header bg-info text-white">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-toggle-on me-2"></i>Estado del Proveedor
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <!-- Campo oculto para enviar el valor real -->
                                    <input type="hidden" name="activo_proveedor" id="activo_proveedor_hidden" value="1">
                                    
                                    <!-- Indicador visual (solo lectura) -->
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="activo_proveedor_display" checked disabled>
                                        <label class="form-check-label" for="activo_proveedor_display">
                                            <strong id="estado_texto">Proveedor Activo</strong>
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        <span id="estado_descripcion">Los proveedores nuevos siempre se crean activos. El estado se puede cambiar desde la lista.</span>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botones de acción -->
                    <div class="card">
                        <div class="card-body text-center">
                            <button type="button" name="action" id="btnSalvarProveedor" class="btn btn-primary btn-lg me-3">
                                <i class="fas fa-save me-2"></i>Guardar Proveedor
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
                        <i class="fas fa-question-circle me-2"></i>Ayuda - Formulario de Proveedores
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <h6 class="text-primary"><i class="fas fa-barcode me-2"></i>Código de Proveedor</h6>
                            <p><strong>Campo obligatorio.</strong> Identificador único del proveedor.</p>
                            <ul class="list-unstyled ms-3">
                                <li><i class="fas fa-check text-success me-2"></i>Máximo 20 caracteres</li>
                                <li><i class="fas fa-check text-success me-2"></i>Se convierte automáticamente a mayúsculas</li>
                                <li><i class="fas fa-check text-success me-2"></i>No puede contener espacios</li>
                                <li><i class="fas fa-check text-success me-2"></i>Debe ser único en el sistema</li>
                            </ul>
                            <hr>
                        </div>
                        
                        <div class="col-12">
                            <h6 class="text-primary"><i class="fas fa-building me-2"></i>Información del Proveedor</h6>
                            <p><strong>Datos principales del proveedor.</strong></p>
                            <ul class="list-unstyled ms-3">
                                <li><i class="fas fa-check text-success me-2"></i>Nombre: Mínimo 3, máximo 255 caracteres</li>
                                <li><i class="fas fa-info text-info me-2"></i>NIF/CIF: Campo opcional para identificación fiscal</li>
                                <li><i class="fas fa-user text-info me-2"></i>Persona de contacto: Responsable principal</li>
                            </ul>
                            <hr>
                        </div>
                        
                        <div class="col-12">
                            <h6 class="text-primary"><i class="fas fa-map-marker-alt me-2"></i>Direcciones</h6>
                            <p><strong>Información de ubicación.</strong></p>
                            <ul class="list-unstyled ms-3">
                                <li><i class="fas fa-home text-info me-2"></i>Dirección principal: Sede o domicilio fiscal</li>
                                <li><i class="fas fa-tools text-warning me-2"></i>Dirección SAT: Servicio de Asistencia Técnica</li>
                                <li><i class="fas fa-envelope text-info me-2"></i>Todos los campos son opcionales</li>
                            </ul>
                            <hr>
                        </div>
                        
                        <div class="col-12">
                            <h6 class="text-primary"><i class="fas fa-phone me-2"></i>Información de Contacto y Observaciones</h6>
                            <p><strong>Medios de comunicación y notas adicionales.</strong></p>
                            <ul class="list-unstyled ms-3">
                                <li><i class="fas fa-phone text-info me-2"></i>Teléfono: Número principal de contacto</li>
                                <li><i class="fas fa-envelope text-info me-2"></i>Email: Se valida formato correcto</li>
                                <li><i class="fas fa-globe text-info me-2"></i>Web: Se valida formato URL válida</li>
                                <li><i class="fas fa-clipboard text-secondary me-2"></i>Observaciones: Información adicional relevante</li>
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
    <script type="text/javascript" src="formularioProveedor.js"></script>

</body>

</html>

<!-- Script inline temporalmente comentado para debug -->
<!--
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Obtener parámetros URL para determinar si es edición
    const urlParams = new URLSearchParams(window.location.search);
    const idProveedor = urlParams.get('id');
    const modo = urlParams.get('modo') || 'nuevo';
    
    // Configurar página según el modo
    if (modo === 'editar' && idProveedor) {
        document.getElementById('page-title').textContent = 'Editar Proveedor';
        document.getElementById('breadcrumb-title').textContent = 'Editar Proveedor';
        document.getElementById('btnSalvarProveedor').innerHTML = '<i class="fas fa-save me-2"></i>Actualizar Proveedor';
        document.getElementById('id_proveedor').value = idProveedor;
        
        // Cargar datos del proveedor para edición
        cargarDatosProveedor(idProveedor);
    }
    
    // Contador de caracteres para las observaciones
    const obsTextarea = document.getElementById('observaciones_proveedor');
    const obsCharCount = document.getElementById('obs-char-count');
    
    if (obsTextarea && obsCharCount) {
        obsTextarea.addEventListener('input', function() {
            const currentLength = this.value.length;
            obsCharCount.textContent = currentLength;
            
            // Cambiar color según la longitud
            if (currentLength > 60000) {
                obsCharCount.style.color = '#dc3545'; // Rojo
            } else if (currentLength > 50000) {
                obsCharCount.style.color = '#ffc107'; // Amarillo
            } else {
                obsCharCount.style.color = '#6c757d'; // Gris normal
            }
        });
    }
    
    // Validación en tiempo real para código proveedor
    const codigoInput = document.getElementById('codigo_proveedor');
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
    
    // Validación en tiempo real para nombre proveedor
    const nombreInput = document.getElementById('nombre_proveedor');
    if (nombreInput) {
        nombreInput.addEventListener('input', function() {
            if (this.value.length >= 3 && this.value.length <= 255) {
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
    
    // Validación de email
    const emailInput = document.getElementById('email_proveedor');
    if (emailInput) {
        emailInput.addEventListener('blur', function() {
            if (this.value && this.value.length > 0) {
                const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (emailPattern.test(this.value)) {
                    this.classList.remove('is-invalid');
                    this.classList.add('is-valid');
                } else {
                    this.classList.remove('is-valid');
                    this.classList.add('is-invalid');
                }
            } else {
                this.classList.remove('is-valid', 'is-invalid');
            }
        });
    }
    
    // Validación de URL
    const webInput = document.getElementById('web_proveedor');
    if (webInput) {
        webInput.addEventListener('blur', function() {
            if (this.value && this.value.length > 0) {
                try {
                    new URL(this.value);
                    this.classList.remove('is-invalid');
                    this.classList.add('is-valid');
                } catch {
                    this.classList.remove('is-valid');
                    this.classList.add('is-invalid');
                }
            } else {
                this.classList.remove('is-valid', 'is-invalid');
            }
        });
    }
});

// Función para cargar datos de proveedor en modo edición
function cargarDatosProveedor(idProveedor) {
    // Esta función se implementará en el archivo formularioProveedor.js
    console.log('Cargando datos de proveedor ID:', idProveedor);
}
</script>
-->