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
                <a class="breadcrumb-item" href="index.php">Mantenimiento Métodos de Pago</a>
                <span class="breadcrumb-item active" id="breadcrumb-title">Nuevo Método de Pago</span>
            </nav>
        </div><!-- br-pageheader -->
        
        <div class="br-pagetitle">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <h4 class="mb-0 me-2" id="page-title">Nuevo Método de Pago</h4>
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
                
                <!-- Formulario de Método de Pago -->
                <form id="formMetodoPago">
                    <!-- Campo oculto para ID del método de pago -->
                    <input type="hidden" name="id_metodo_pago" id="id_metodo_pago">

                    <!-- SECCIÓN: Información Básica -->
                    <div class="card mb-4 border-primary">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0 tx-bold">
                                <i class="fas fa-credit-card me-2"></i>Información Básica del Método de Pago
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-12 col-md-6">
                                    <label for="codigo_metodo_pago" class="form-label">Código del método: <span class="tx-danger">*</span></label>
                                    <input type="text" class="form-control" name="codigo_metodo_pago" id="codigo_metodo_pago" maxlength="20" placeholder="Ej: TRANS, TARJ, EFEC..." required>
                                    <div class="invalid-feedback small-invalid-feedback">Ingrese un código válido (máximo 20 caracteres, único)</div>
                                    <small class="form-text text-muted">Código único identificativo del método de pago (máximo 20 caracteres)</small>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label">Estado:</label>
                                    <div class="form-check form-switch mt-2">
                                        <input class="form-check-input" type="checkbox" name="activo_metodo_pago" id="activo_metodo_pago" checked disabled>
                                        <label class="form-check-label" for="activo_metodo_pago">
                                            <span id="estado-text">Método Activo</span>
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">El estado se establece automáticamente</small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12">
                                    <label for="nombre_metodo_pago" class="form-label">Nombre del método: <span class="tx-danger">*</span></label>
                                    <input type="text" class="form-control" name="nombre_metodo_pago" id="nombre_metodo_pago" maxlength="100" placeholder="Ej: Transferencia bancaria, Tarjeta de crédito..." required>
                                    <div class="invalid-feedback small-invalid-feedback">Ingrese un nombre válido (máximo 100 caracteres)</div>
                                    <small class="form-text text-muted">Nombre descriptivo del método de pago (máximo 100 caracteres)</small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12">
                                    <label for="observaciones_metodo_pago" class="form-label">Observaciones:</label>
                                    <textarea class="form-control" name="observaciones_metodo_pago" id="observaciones_metodo_pago" rows="3" placeholder="Observaciones adicionales sobre el método de pago..."></textarea>
                                    <small class="form-text text-muted">
                                        Observaciones opcionales sobre el método de pago
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botones de acción -->
                    <div class="card">
                        <div class="card-body text-center">
                            <button type="button" name="action" id="btnSalvarMetodoPago" class="btn btn-primary btn-lg me-3">
                                <i class="fas fa-save me-2"></i>Guardar Método de Pago
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
                        <i class="fas fa-question-circle me-2"></i>Ayuda - Formulario de Métodos de Pago
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <h6 class="text-primary"><i class="fas fa-barcode me-2"></i>Código del Método</h6>
                            <p><strong>Campo obligatorio.</strong> Identificador único del método de pago.</p>
                            <ul class="list-unstyled ms-3">
                                <li><i class="fas fa-check text-success me-2"></i>Máximo 20 caracteres</li>
                                <li><i class="fas fa-check text-success me-2"></i>Se convierte automáticamente a mayúsculas</li>
                                <li><i class="fas fa-check text-success me-2"></i>No puede contener espacios</li>
                                <li><i class="fas fa-check text-success me-2"></i>Debe ser único en el sistema</li>
                                <li><i class="fas fa-info text-info me-2"></i>Ejemplos: TRANS, TARJ, EFEC, CHEQ, BIZUM</li>
                            </ul>
                            <hr>
                        </div>
                        
                        <div class="col-12">
                            <h6 class="text-primary"><i class="fas fa-tag me-2"></i>Nombre del Método</h6>
                            <p><strong>Campo obligatorio.</strong> Nombre descriptivo del método de pago.</p>
                            <ul class="list-unstyled ms-3">
                                <li><i class="fas fa-check text-success me-2"></i>Máximo 100 caracteres</li>
                                <li><i class="fas fa-check text-success me-2"></i>Debe ser único en el sistema</li>
                                <li><i class="fas fa-info text-info me-2"></i>Ejemplos: Transferencia bancaria, Tarjeta de crédito/débito, Efectivo</li>
                            </ul>
                            <hr>
                        </div>
                        
                        <div class="col-12">
                            <h6 class="text-primary"><i class="fas fa-file-alt me-2"></i>Observaciones y Estado</h6>
                            <p><strong>Información adicional.</strong> Campos complementarios del método de pago.</p>
                            <ul class="list-unstyled ms-3">
                                <li><i class="fas fa-info text-info me-2"></i>Observaciones: campo opcional, para notas adicionales</li>
                                <li><i class="fas fa-lock text-warning me-2"></i>Estado: se establece automáticamente (activo para nuevos métodos)</li>
                                <li><i class="fas fa-warning text-warning me-2"></i>Un código y nombre solo pueden existir una vez</li>
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
    <script type="text/javascript" src="formularioMetodoPago.js"></script>

</body>

</html>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Obtener parámetros URL para determinar si es edición
    const urlParams = new URLSearchParams(window.location.search);
    const idMetodoPago = urlParams.get('id');
    const modo = urlParams.get('modo') || 'nuevo';
    
    // Configurar página según el modo
    if (modo === 'editar' && idMetodoPago) {
        document.getElementById('page-title').textContent = 'Editar Método de Pago';
        document.getElementById('breadcrumb-title').textContent = 'Editar Método de Pago';
        document.getElementById('btnSalvarMetodoPago').innerHTML = '<i class="fas fa-save me-2"></i>Actualizar Método de Pago';
        document.getElementById('id_metodo_pago').value = idMetodoPago;
        
        // Cargar datos del método de pago para edición
        cargarDatosMetodoPago(idMetodoPago);
    }
    
    // Validación en tiempo real para código método pago
    const codigoInput = document.getElementById('codigo_metodo_pago');
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
    
    // Validación en tiempo real para nombre método pago
    const nombreInput = document.getElementById('nombre_metodo_pago');
    if (nombreInput) {
        nombreInput.addEventListener('input', function() {
            if (this.value.length > 100) {
                this.classList.add('is-invalid');
            } else if (this.value.length >= 3) {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            } else {
                this.classList.remove('is-valid', 'is-invalid');
            }
        });
    }
});

// Función para cargar datos de método de pago en modo edición
function cargarDatosMetodoPago(idMetodoPago) {
    // Esta función se implementará en el archivo formularioMetodoPago.js
    console.log('Cargando datos de método de pago ID:', idMetodoPago);
}
</script>
