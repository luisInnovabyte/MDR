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
                <a class="breadcrumb-item" href="index.html">Dashboard</a>
                <a class="breadcrumb-item" href="index.php">Mantenimiento Formas de Pago</a>
                <span class="breadcrumb-item active" id="breadcrumb-title">Nueva Forma de Pago</span>
            </nav>
        </div><!-- br-pageheader -->
        
        <div class="br-pagetitle">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <h4 class="mb-0 me-2" id="page-title">Nueva Forma de Pago</h4>
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
                
                <!-- Formulario de Forma de Pago -->
                <form id="formFormaPago">
                    <!-- Campo oculto para ID de la forma de pago -->
                    <input type="hidden" name="id_pago" id="id_pago">

                    <!-- SECCIÓN: Información Básica -->
                    <div class="card mb-4 border-primary">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0 tx-bold">
                                <i class="fas fa-info-circle me-2"></i>Información Básica
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-12 col-md-6">
                                    <label for="codigo_pago" class="form-label">Código: <span class="tx-danger">*</span></label>
                                    <input type="text" class="form-control" name="codigo_pago" id="codigo_pago" maxlength="20" placeholder="Ej: CONT_TRANS" required>
                                    <div class="invalid-feedback small-invalid-feedback">Ingrese un código válido (máximo 20 caracteres, único)</div>
                                    <small class="form-text text-muted">Código único identificativo de la forma de pago (máximo 20 caracteres)</small>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="nombre_pago" class="form-label">Nombre: <span class="tx-danger">*</span></label>
                                    <input type="text" class="form-control" name="nombre_pago" id="nombre_pago" maxlength="100" placeholder="Ej: Contado transferencia" required>
                                    <div class="invalid-feedback small-invalid-feedback">Ingrese un nombre válido (máximo 100 caracteres)</div>
                                    <small class="form-text text-muted">Nombre descriptivo de la forma de pago</small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12 col-md-6">
                                    <label for="id_metodo_pago" class="form-label">Método de Pago: <span class="tx-danger">*</span></label>
                                    <select class="form-control" name="id_metodo_pago" id="id_metodo_pago" required>
                                        <option value="">Seleccione un método...</option>
                                    </select>
                                    <div class="invalid-feedback small-invalid-feedback">Debe seleccionar un método de pago</div>
                                    <small class="form-text text-muted">Método de pago asociado (transferencia, efectivo, etc.)</small>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="descuento_pago" class="form-label">Descuento (%):</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" name="descuento_pago" id="descuento_pago" step="0.01" min="0" max="100" value="0.00" placeholder="0.00">
                                        <span class="input-group-text">%</span>
                                    </div>
                                    <div class="invalid-feedback small-invalid-feedback">Ingrese un descuento válido (0-100%)</div>
                                    <small class="form-text text-muted">Descuento aplicable solo en pagos únicos (0-100%)</small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12">
                                    <label class="form-label">Estado:</label>
                                    <div class="form-check form-switch mt-2">
                                        <input class="form-check-input" type="checkbox" name="activo_pago" id="activo_pago" checked disabled>
                                        <label class="form-check-label" for="activo_pago">
                                            <span id="estado-text">Forma de Pago Activa</span>
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">El estado se establece automáticamente</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECCIÓN: Configuración de Pagos -->
                    <div class="card mb-4 border-info">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0 tx-bold">
                                <i class="fas fa-calculator me-2"></i>Configuración de Pagos
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Importante:</strong> La suma de los porcentajes de anticipo y pago final debe ser igual a 100%.
                            </div>

                            <div class="row mb-3">
                                <div class="col-12 col-md-6">
                                    <label for="porcentaje_anticipo_pago" class="form-label">Porcentaje Anticipo (%): <span class="tx-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" name="porcentaje_anticipo_pago" id="porcentaje_anticipo_pago" step="0.01" min="0" max="100" value="100.00" required>
                                        <span class="input-group-text">%</span>
                                    </div>
                                    <div class="invalid-feedback small-invalid-feedback">Ingrese un porcentaje válido (0-100%)</div>
                                    <small class="form-text text-muted">Porcentaje del pago como anticipo (por defecto 100%)</small>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="dias_anticipo_pago" class="form-label">Días para Anticipo: <span class="tx-danger">*</span></label>
                                    <input type="number" class="form-control" name="dias_anticipo_pago" id="dias_anticipo_pago" min="0" value="0" required>
                                    <div class="invalid-feedback small-invalid-feedback">Ingrese un número de días válido</div>
                                    <small class="form-text text-muted">Días desde la fecha del presupuesto para el anticipo</small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12 col-md-6">
                                    <label for="porcentaje_final_pago" class="form-label">Porcentaje Pago Final (%): <span class="tx-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" name="porcentaje_final_pago" id="porcentaje_final_pago" step="0.01" min="0" max="100" value="0.00" required>
                                        <span class="input-group-text">%</span>
                                    </div>
                                    <div class="invalid-feedback small-invalid-feedback">Ingrese un porcentaje válido (0-100%)</div>
                                    <small class="form-text text-muted">Porcentaje del pago final (por defecto 0%)</small>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="dias_final_pago" class="form-label">Días para Pago Final: <span class="tx-danger">*</span></label>
                                    <input type="number" class="form-control" name="dias_final_pago" id="dias_final_pago" value="0" required>
                                    <div class="invalid-feedback small-invalid-feedback">Ingrese un número de días válido</div>
                                    <small class="form-text text-muted">Días desde el evento (positivo) o antes del evento (negativo)</small>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <div class="alert alert-warning" id="alert-porcentaje" style="display: none;">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        <strong>Atención:</strong> <span id="mensaje-porcentaje"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECCIÓN: Observaciones -->
                    <div class="card mb-4 border-secondary">
                        <div class="card-header bg-secondary text-white">
                            <h5 class="mb-0 tx-bold">
                                <i class="fas fa-comment-alt me-2"></i>Observaciones
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <label for="observaciones_pago" class="form-label">Observaciones:</label>
                                    <textarea class="form-control" name="observaciones_pago" id="observaciones_pago" rows="4" placeholder="Observaciones o notas adicionales sobre esta forma de pago..."></textarea>
                                    <small class="form-text text-muted">
                                        Información adicional relevante sobre esta forma de pago
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
                            <button type="button" name="action" id="btnSalvarFormaPago" class="btn btn-primary btn-lg me-3">
                                <i class="fas fa-save me-2"></i>Guardar Forma de Pago
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
                        <i class="fas fa-question-circle me-2"></i>Ayuda - Formulario de Formas de Pago
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <h6 class="text-primary"><i class="fas fa-barcode me-2"></i>Código de Forma de Pago</h6>
                            <p><strong>Campo obligatorio.</strong> Identificador único de la forma de pago.</p>
                            <ul class="list-unstyled ms-3">
                                <li><i class="fas fa-check text-success me-2"></i>Máximo 20 caracteres</li>
                                <li><i class="fas fa-check text-success me-2"></i>Debe ser único en el sistema</li>
                                <li><i class="fas fa-info text-info me-2"></i>Ejemplos: CONT_TRANS, FRAC40_60, FRAC50_50, PAGO_30D</li>
                            </ul>
                            <hr>
                        </div>
                        
                        <div class="col-12">
                            <h6 class="text-primary"><i class="fas fa-tag me-2"></i>Nombre de la Forma de Pago</h6>
                            <p><strong>Campo obligatorio.</strong> Nombre descriptivo de la forma de pago.</p>
                            <ul class="list-unstyled ms-3">
                                <li><i class="fas fa-check text-success me-2"></i>Máximo 100 caracteres</li>
                                <li><i class="fas fa-check text-success me-2"></i>Debe ser único en el sistema</li>
                                <li><i class="fas fa-info text-info me-2"></i>Ejemplos: Contado transferencia, Fraccionado 40-60, Pago a 30 días</li>
                            </ul>
                            <hr>
                        </div>
                        
                        <div class="col-12">
                            <h6 class="text-primary"><i class="fas fa-wallet me-2"></i>Método de Pago</h6>
                            <p><strong>Campo obligatorio.</strong> Método mediante el cual se realizará el pago.</p>
                            <ul class="list-unstyled ms-3">
                                <li><i class="fas fa-check text-success me-2"></i>Seleccione de la lista desplegable</li>
                                <li><i class="fas fa-info text-info me-2"></i>Solo aparecen métodos activos (Transferencia, Efectivo, Tarjeta, etc.)</li>
                                <li><i class="fas fa-link text-info me-2"></i>Vincula esta forma de pago con un método específico</li>
                            </ul>
                            <hr>
                        </div>
                        
                        <div class="col-12">
                            <h6 class="text-primary"><i class="fas fa-percentage me-2"></i>Configuración de Porcentajes</h6>
                            <p><strong>Regla fundamental:</strong> Anticipo + Pago Final = 100%</p>
                            <ul class="list-unstyled ms-3">
                                <li><i class="fas fa-arrow-right text-success me-2"></i><strong>Porcentaje Anticipo:</strong> Parte inicial del pago (0-100%)</li>
                                <li><i class="fas fa-arrow-right text-success me-2"></i><strong>Porcentaje Final:</strong> Parte restante del pago (0-100%)</li>
                                <li><i class="fas fa-exclamation-triangle text-warning me-2"></i>El sistema valida que la suma sea exactamente 100%</li>
                            </ul>
                            <p class="mt-2"><strong>Ejemplos:</strong></p>
                            <ul class="list-unstyled ms-3">
                                <li>• Pago único: Anticipo 100% + Final 0%</li>
                                <li>• Fraccionado 40-60: Anticipo 40% + Final 60%</li>
                                <li>• Fraccionado 50-50: Anticipo 50% + Final 50%</li>
                            </ul>
                            <hr>
                        </div>
                        
                        <div class="col-12">
                            <h6 class="text-primary"><i class="fas fa-calendar-alt me-2"></i>Días para Pagos</h6>
                            <p><strong>Plazos de pago desde fechas de referencia.</strong></p>
                            <ul class="list-unstyled ms-3">
                                <li><i class="fas fa-calendar-check text-info me-2"></i><strong>Días para Anticipo:</strong> Días desde la fecha del presupuesto
                                    <ul class="mt-1">
                                        <li>• 0 = Inmediato al aceptar presupuesto</li>
                                        <li>• 7 = A los 7 días del presupuesto</li>
                                    </ul>
                                </li>
                                <li class="mt-2"><i class="fas fa-calendar-day text-info me-2"></i><strong>Días para Pago Final:</strong> Días respecto al evento
                                    <ul class="mt-1">
                                        <li>• Negativo (-7) = 7 días ANTES del evento</li>
                                        <li>• Cero (0) = El mismo día del evento</li>
                                        <li>• Positivo (7) = 7 días DESPUÉS del evento</li>
                                    </ul>
                                </li>
                            </ul>
                            <hr>
                        </div>
                        
                        <div class="col-12">
                            <h6 class="text-primary"><i class="fas fa-tag me-2"></i>Descuento (%)</h6>
                            <p><strong>Campo opcional.</strong> Descuento aplicable sobre el total.</p>
                            <ul class="list-unstyled ms-3">
                                <li><i class="fas fa-check text-success me-2"></i>Rango: 0% a 100% (con 2 decimales)</li>
                                <li><i class="fas fa-exclamation-triangle text-warning me-2"></i><strong>Solo se aplica cuando Anticipo = 100%</strong></li>
                                <li><i class="fas fa-times text-danger me-2"></i>No se aplica en pagos fraccionados</li>
                                <li><i class="fas fa-info text-info me-2"></i>Ejemplo: 5% descuento por pago al contado</li>
                            </ul>
                            <hr>
                        </div>
                        
                        <div class="col-12">
                            <h6 class="text-primary"><i class="fas fa-file-alt me-2"></i>Observaciones y Estado</h6>
                            <p><strong>Información adicional.</strong> Campos complementarios de la forma de pago.</p>
                            <ul class="list-unstyled ms-3">
                                <li><i class="fas fa-info text-info me-2"></i>Observaciones: campo opcional, para condiciones especiales o aclaraciones</li>
                                <li><i class="fas fa-lock text-warning me-2"></i>Estado: se establece automáticamente (activo para nuevas formas de pago)</li>
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
    <script type="text/javascript" src="formularioFormaPago.js"></script>

</body>

</html>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Obtener parámetros URL para determinar si es edición
    const urlParams = new URLSearchParams(window.location.search);
    const idPago = urlParams.get('id');
    const modo = urlParams.get('modo') || 'nuevo';
    
    // Configurar página según el modo
    if (modo === 'editar' && idPago) {
        document.getElementById('page-title').textContent = 'Editar Forma de Pago';
        document.getElementById('breadcrumb-title').textContent = 'Editar Forma de Pago';
        document.getElementById('btnSalvarFormaPago').innerHTML = '<i class="fas fa-save me-2"></i>Actualizar Forma de Pago';
        document.getElementById('id_pago').value = idPago;
        
        // Cargar datos de la forma de pago para edición
        cargarDatosFormaPago(idPago);
    }
    
    // Contador de caracteres para observaciones
    const obsTextarea = document.getElementById('observaciones_pago');
    const charCount = document.getElementById('char-count');
    
    if (obsTextarea && charCount) {
        obsTextarea.addEventListener('input', function() {
            charCount.textContent = this.value.length;
        });
    }
    
    // Validación de porcentajes en tiempo real
    const porcentajeAnticipo = document.getElementById('porcentaje_anticipo_pago');
    const porcentajeFinal = document.getElementById('porcentaje_final_pago');
    const alertPorcentaje = document.getElementById('alert-porcentaje');
    const mensajePorcentaje = document.getElementById('mensaje-porcentaje');
    
    function validarPorcentajes() {
        const anticipo = parseFloat(porcentajeAnticipo.value) || 0;
        const final = parseFloat(porcentajeFinal.value) || 0;
        const suma = anticipo + final;
        
        if (Math.abs(suma - 100) > 0.01) { // Tolerancia para decimales
            alertPorcentaje.style.display = 'block';
            mensajePorcentaje.textContent = `La suma de porcentajes es ${suma.toFixed(2)}%. Debe ser exactamente 100%.`;
            return false;
        } else {
            alertPorcentaje.style.display = 'none';
            return true;
        }
    }
    
    if (porcentajeAnticipo && porcentajeFinal) {
        porcentajeAnticipo.addEventListener('input', validarPorcentajes);
        porcentajeFinal.addEventListener('input', validarPorcentajes);
    }
});

// Función para cargar datos de forma de pago en modo edición
function cargarDatosFormaPago(idPago) {
    // Esta función se implementará en el archivo formularioFormaPago.js
    console.log('Cargando datos de forma de pago ID:', idPago);
}
</script>
```
