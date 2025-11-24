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
                <a class="breadcrumb-item" href="index.php">Mantenimiento Clientes</a>
                <span class="breadcrumb-item active" id="breadcrumb-title">Nuevo Cliente</span>
            </nav>
        </div><!-- br-pageheader -->
        
        <div class="br-pagetitle">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <h4 class="mb-0 me-2" id="page-title">Nuevo Cliente</h4>
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
                
                <!-- Formulario de Cliente -->
                <form id="formCliente">
                    <!-- Campo oculto para ID del cliente -->
                    <input type="hidden" name="id_cliente" id="id_cliente">

                    <!-- SECCIÓN: Información Básica -->
                    <div class="card mb-4 border-primary">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0 tx-bold">
                                <i class="fas fa-info-circle me-2"></i>Información Básica del Cliente
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-12 col-md-6">
                                    <label for="codigo_cliente" class="form-label">Código cliente: <span class="tx-danger">*</span></label>
                                    <input type="text" class="form-control" name="codigo_cliente" id="codigo_cliente" maxlength="20" placeholder="Ej: CLI001, EMP123, etc..." required>
                                    <div class="invalid-feedback small-invalid-feedback">Ingrese un código válido (máximo 20 caracteres, único)</div>
                                    <small class="form-text text-muted">Código único identificativo (máximo 20 caracteres)</small>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="nombre_cliente" class="form-label">Nombre cliente: <span class="tx-danger">*</span></label>
                                    <input type="text" class="form-control" name="nombre_cliente" id="nombre_cliente" maxlength="255" placeholder="Ej: Empresa López S.L., Juan García, etc..." required>
                                    <div class="invalid-feedback small-invalid-feedback">Ingrese un nombre válido (mínimo 3 y máximo 255 caracteres)</div>
                                    <small class="form-text text-muted">Nombre completo del cliente</small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12 col-md-6">
                                    <label for="nif_cliente" class="form-label">NIF/CIF: <span class="tx-danger">*</span></label>
                                    <input type="text" class="form-control" name="nif_cliente" id="nif_cliente" maxlength="20" placeholder="Ej: 12345678A, B12345678, etc..." required>
                                    <div class="invalid-feedback small-invalid-feedback">Ingrese un NIF/CIF válido</div>
                                    <small class="form-text text-muted">Número de identificación fiscal</small>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="telefono_cliente" class="form-label">Teléfono:</label>
                                    <input type="text" class="form-control" name="telefono_cliente" id="telefono_cliente" maxlength="255" placeholder="Ej: 912345678, 600123456">
                                    <small class="form-text text-muted">Teléfono principal de contacto</small>
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
                                    <label for="direccion_cliente" class="form-label">Dirección:</label>
                                    <input type="text" class="form-control" name="direccion_cliente" id="direccion_cliente" maxlength="255" placeholder="Ej: Calle Mayor, 123, 2º A">
                                    <small class="form-text text-muted">Dirección completa del cliente</small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12 col-md-4">
                                    <label for="cp_cliente" class="form-label">Código Postal:</label>
                                    <input type="text" class="form-control" name="cp_cliente" id="cp_cliente" maxlength="10" placeholder="Ej: 28001, 08080">
                                </div>
                                <div class="col-12 col-md-4">
                                    <label for="poblacion_cliente" class="form-label">Población:</label>
                                    <input type="text" class="form-control" name="poblacion_cliente" id="poblacion_cliente" maxlength="100" placeholder="Ej: Madrid, Barcelona">
                                </div>
                                <div class="col-12 col-md-4">
                                    <label for="provincia_cliente" class="form-label">Provincia:</label>
                                    <input type="text" class="form-control" name="provincia_cliente" id="provincia_cliente" maxlength="100" placeholder="Ej: Madrid, Barcelona">
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
                                    <label for="email_cliente" class="form-label">Email:</label>
                                    <input type="email" class="form-control" name="email_cliente" id="email_cliente" maxlength="255" placeholder="cliente@ejemplo.com">
                                    <div class="invalid-feedback small-invalid-feedback">Ingrese un email válido</div>
                                    <small class="form-text text-muted">Email principal de contacto</small>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="web_cliente" class="form-label">Página Web:</label>
                                    <input type="url" class="form-control" name="web_cliente" id="web_cliente" maxlength="255" placeholder="https://www.cliente.com">
                                    <div class="invalid-feedback small-invalid-feedback">Ingrese una URL válida</div>
                                    <small class="form-text text-muted">Sitio web del cliente</small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12 col-md-6">
                                    <label for="fax_cliente" class="form-label">Fax:</label>
                                    <input type="text" class="form-control" name="fax_cliente" id="fax_cliente" maxlength="50" placeholder="Ej: 912345679">
                                    <small class="form-text text-muted">Número de fax (opcional)</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECCIÓN: Dirección de Facturación -->
                    <div class="card mb-4 border-warning">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="mb-0 tx-bold">
                                <i class="fas fa-receipt me-2"></i>Dirección de Facturación
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Información:</strong> Complete solo si la dirección de facturación es diferente a la dirección principal.
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-12">
                                    <label for="nombre_facturacion_cliente" class="form-label">Nombre para facturación:</label>
                                    <input type="text" class="form-control" name="nombre_facturacion_cliente" id="nombre_facturacion_cliente" maxlength="255" placeholder="Nombre que aparecerá en facturas...">
                                    <small class="form-text text-muted">Si es diferente al nombre principal</small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12">
                                    <label for="direccion_facturacion_cliente" class="form-label">Dirección de facturación:</label>
                                    <input type="text" class="form-control" name="direccion_facturacion_cliente" id="direccion_facturacion_cliente" maxlength="255" placeholder="Dirección específica para facturación...">
                                    <small class="form-text text-muted">Dirección donde enviar las facturas</small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12 col-md-4">
                                    <label for="cp_facturacion_cliente" class="form-label">CP Facturación:</label>
                                    <input type="text" class="form-control" name="cp_facturacion_cliente" id="cp_facturacion_cliente" maxlength="10" placeholder="CP para facturación...">
                                </div>
                                <div class="col-12 col-md-4">
                                    <label for="poblacion_facturacion_cliente" class="form-label">Población Facturación:</label>
                                    <input type="text" class="form-control" name="poblacion_facturacion_cliente" id="poblacion_facturacion_cliente" maxlength="100" placeholder="Población para facturación...">
                                </div>
                                <div class="col-12 col-md-4">
                                    <label for="provincia_facturacion_cliente" class="form-label">Provincia Facturación:</label>
                                    <input type="text" class="form-control" name="provincia_facturacion_cliente" id="provincia_facturacion_cliente" maxlength="100" placeholder="Provincia para facturación...">
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
                                    <label for="observaciones_cliente" class="form-label">Observaciones:</label>
                                    <textarea class="form-control" name="observaciones_cliente" id="observaciones_cliente" rows="4" placeholder="Observaciones sobre el cliente, condiciones especiales, notas importantes, etc..."></textarea>
                                    <small class="form-text text-muted">
                                        Información adicional sobre el cliente
                                        <span class="float-end">
                                            <span id="obs-char-count">0</span>/65535 caracteres
                                        </span>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECCIÓN: Estado del Cliente -->
                    <div class="card shadow-sm">
                        <div class="card-header bg-info text-white">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-toggle-on me-2"></i>Estado del Cliente
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <!-- Campo oculto para enviar el valor real -->
                                    <input type="hidden" name="activo_cliente" id="activo_cliente_hidden" value="1">
                                    
                                    <!-- Indicador visual (solo lectura) -->
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="activo_cliente_display" checked disabled>
                                        <label class="form-check-label" for="activo_cliente_display">
                                            <strong id="estado_texto">Cliente Activo</strong>
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        <span id="estado_descripcion">Los clientes nuevos siempre se crean activos. El estado se puede cambiar desde la lista.</span>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botones de acción -->
                    <div class="card">
                        <div class="card-body text-center">
                            <button type="button" name="action" id="btnSalvarCliente" class="btn btn-primary btn-lg me-3">
                                <i class="fas fa-save me-2"></i>Guardar Cliente
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
                        <i class="fas fa-question-circle me-2"></i>Ayuda - Formulario de Clientes
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <h6 class="text-primary"><i class="fas fa-barcode me-2"></i>Código de Cliente</h6>
                            <p><strong>Campo obligatorio.</strong> Identificador único del cliente.</p>
                            <ul class="list-unstyled ms-3">
                                <li><i class="fas fa-check text-success me-2"></i>Máximo 20 caracteres</li>
                                <li><i class="fas fa-check text-success me-2"></i>Se convierte automáticamente a mayúsculas</li>
                                <li><i class="fas fa-check text-success me-2"></i>No puede contener espacios</li>
                                <li><i class="fas fa-check text-success me-2"></i>Debe ser único en el sistema</li>
                            </ul>
                            <hr>
                        </div>
                        
                        <div class="col-12">
                            <h6 class="text-primary"><i class="fas fa-user me-2"></i>Información del Cliente</h6>
                            <p><strong>Datos principales del cliente.</strong></p>
                            <ul class="list-unstyled ms-3">
                                <li><i class="fas fa-check text-success me-2"></i>Nombre: Mínimo 3, máximo 255 caracteres</li>
                                <li><i class="fas fa-check text-success me-2"></i>NIF/CIF: Campo obligatorio para identificación fiscal</li>
                                <li><i class="fas fa-info text-info me-2"></i>Teléfono: Campo opcional para contacto</li>
                            </ul>
                            <hr>
                        </div>
                        
                        <div class="col-12">
                            <h6 class="text-primary"><i class="fas fa-map-marker-alt me-2"></i>Direcciones</h6>
                            <p><strong>Información de ubicación.</strong></p>
                            <ul class="list-unstyled ms-3">
                                <li><i class="fas fa-home text-info me-2"></i>Dirección principal: Domicilio del cliente</li>
                                <li><i class="fas fa-receipt text-warning me-2"></i>Dirección facturación: Solo si es diferente a la principal</li>
                                <li><i class="fas fa-envelope text-info me-2"></i>Todos los campos son opcionales</li>
                            </ul>
                            <hr>
                        </div>
                        
                        <div class="col-12">
                            <h6 class="text-primary"><i class="fas fa-phone me-2"></i>Información de Contacto y Observaciones</h6>
                            <p><strong>Medios de comunicación y notas adicionales.</strong></p>
                            <ul class="list-unstyled ms-3">
                                <li><i class="fas fa-envelope text-info me-2"></i>Email: Se valida formato correcto</li>
                                <li><i class="fas fa-globe text-info me-2"></i>Web: Se valida formato URL válida</li>
                                <li><i class="fas fa-printer text-info me-2"></i>Fax: Número de fax opcional</li>
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
    <script type="text/javascript" src="formularioCliente.js"></script>

</body>

</html>