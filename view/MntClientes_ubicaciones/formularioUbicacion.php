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
                <a class="breadcrumb-item" href="../MntClientes/">Clientes</a>
                <span class="breadcrumb-item active" id="breadcrumb-title">Ubicación</span>
            </nav>
        </div><!-- br-pageheader -->
        
        <div class="br-pagetitle">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="d-flex align-items-center">
                    <h4 class="mb-0 me-2" id="page-title">Formulario de Ubicación</h4>
                    <button type="button" class="btn btn-link p-0 ms-1" data-bs-toggle="modal" data-bs-target="#modalAyudaFormulario" title="Ayuda sobre el formulario">
                        <i class="bi bi-question-circle text-primary" style="font-size: 1.3rem;"></i>
                    </button>
                </div>
            </div>
            
            <!-- Info del cliente -->
                    <div class="mt-2 mb-3" id="info-cliente">
                        <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #134e5e 0%, #71b280 100%);">
                            <div class="card-body py-3 px-4">
                                <div class="row align-items-center">
                                    <!-- Icono principal -->
                                    <div class="col-auto">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center" 
                                             style="width: 60px; height: 60px; background-color: rgba(255,255,255,0.15);">
                                            <i class="bi bi-person-circle text-white" style="font-size: 1.8rem;"></i>
                                        </div>
                                    </div>
                                    
                                    <!-- Información del cliente -->
                                    <div class="col">
                                        <div class="text-white-50 mb-1" style="font-size: 0.85rem; font-weight: 500;">
                                            <i class="bi bi-info-circle me-1"></i>Cliente actual
                                        </div>
                                        <h5 class="mb-2 fw-bold text-white" id="nombre-cliente">
                                            Cargando...
                                        </h5>
                                        <div class="d-flex align-items-center gap-3 flex-wrap">
                                            <span class="text-white-50" style="font-size: 0.9rem;">
                                                <i class="bi bi-hash me-1"></i>ID:
                                                <span id="id-cliente" class="badge bg-white text-dark ms-1 fw-semibold">--</span>
                                            </span>
                                            <span class="text-white-50" style="font-size: 0.9rem;">
                                                <i class="bi bi-envelope me-1"></i>Email:
                                                <span id="email-cliente" class="badge bg-white text-dark ms-1 fw-semibold">--</span>
                                            </span>
                                            <span class="text-white-50" style="font-size: 0.9rem;">
                                                <i class="bi bi-telephone me-1"></i>Teléfono:
                                                <span id="telefono-cliente" class="badge bg-white text-dark ms-1 fw-semibold">--</span>
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <!-- Botón de acción -->
                                    <div class="col-auto d-none d-md-block">
                                        <a href="../MntClientes/index.php" class="btn btn-light btn-sm">
                                            <i class="bi bi-arrow-left me-1"></i>Volver
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
        </div><!-- br-pagetitle -->

        <div class="br-pagebody">
            <div class="br-section-wrapper">

                    <!-- Formulario de Ubicación -->
                    <div class="card bd-0 mg-t-20">
                        <div class="card-header bg-primary tx-white">
                            <h6 class="card-title mb-0"><i class="fas fa-map-marker-alt me-2"></i>Datos de la Ubicación</h6>
                        </div>

                        <div class="card-body bd bd-t-0 pd-20">
                        <form id="frmUbicacion">
                            <input type="hidden" name="id_ubicacion" id="id_ubicacion">
                            <input type="hidden" name="id_cliente" id="id_cliente">

                                <!-- Sección: Información Básica -->
                                <div class="row">
                                    <div class="col-12">
                                        <h5 class="border-bottom pb-2 mb-3"><i class="fas fa-info-circle me-2"></i>Información de la Ubicación</h5>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="nombre_ubicacion" class="form-label">
                                                Nombre de la Ubicación <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" class="form-control" id="nombre_ubicacion" name="nombre_ubicacion" 
                                                   placeholder="Ej: Oficina Central, Almacén Norte" maxlength="100" required>
                                            <div class="invalid-feedback"></div>
                                            <small class="form-text text-muted">Nombre identificativo de la ubicación (2-100 caracteres)</small>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="pais_ubicacion" class="form-label">
                                                País
                                            </label>
                                            <input type="text" class="form-control" id="pais_ubicacion" name="pais_ubicacion" 
                                                   value="España" maxlength="100">
                                            <small class="form-text text-muted">País de la ubicación</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Sección: Dirección Completa -->
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <h5 class="border-bottom pb-2 mb-3"><i class="fas fa-map-marked-alt me-2"></i>Dirección Completa</h5>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group mb-3">
                                            <label for="direccion_ubicacion" class="form-label">
                                                Dirección
                                            </label>
                                            <input type="text" class="form-control" id="direccion_ubicacion" name="direccion_ubicacion" 
                                                   placeholder="Calle, número, piso, puerta" maxlength="255">
                                            <small class="form-text text-muted">Dirección completa de la ubicación</small>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label for="codigo_postal_ubicacion" class="form-label">
                                                Código Postal
                                            </label>
                                            <input type="text" class="form-control" id="codigo_postal_ubicacion" name="codigo_postal_ubicacion" 
                                                   placeholder="28001" maxlength="10">
                                            <small class="form-text text-muted">Código postal</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="poblacion_ubicacion" class="form-label">
                                                Población
                                            </label>
                                            <input type="text" class="form-control" id="poblacion_ubicacion" name="poblacion_ubicacion" 
                                                   placeholder="Madrid" maxlength="100">
                                            <small class="form-text text-muted">Ciudad o población</small>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="provincia_ubicacion" class="form-label">
                                                Provincia
                                            </label>
                                            <input type="text" class="form-control" id="provincia_ubicacion" name="provincia_ubicacion" 
                                                   placeholder="Madrid" maxlength="100">
                                            <small class="form-text text-muted">Provincia o región</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Sección: Información de Contacto -->
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <h5 class="border-bottom pb-2 mb-3"><i class="fas fa-address-card me-2"></i>Información de Contacto</h5>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="persona_contacto_ubicacion" class="form-label">
                                                Persona de Contacto
                                            </label>
                                            <input type="text" class="form-control" id="persona_contacto_ubicacion" name="persona_contacto_ubicacion" 
                                                   placeholder="Nombre del responsable" maxlength="100">
                                            <small class="form-text text-muted">Persona responsable en esta ubicación</small>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="telefono_contacto_ubicacion" class="form-label">
                                                Teléfono de Contacto
                                            </label>
                                            <input type="text" class="form-control" id="telefono_contacto_ubicacion" name="telefono_contacto_ubicacion" 
                                                   placeholder="+34 912 345 678" maxlength="20">
                                            <div class="invalid-feedback"></div>
                                            <small class="form-text text-muted">Teléfono de la ubicación</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group mb-3">
                                            <label for="email_contacto_ubicacion" class="form-label">
                                                Email de Contacto
                                            </label>
                                            <input type="email" class="form-control" id="email_contacto_ubicacion" name="email_contacto_ubicacion" 
                                                   placeholder="ubicacion@empresa.com" maxlength="100">
                                            <div class="invalid-feedback"></div>
                                            <small class="form-text text-muted">Email de contacto en esta ubicación</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Sección: Configuración -->
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <h5 class="border-bottom pb-2 mb-3"><i class="fas fa-cog me-2"></i>Configuración</h5>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="es_principal_ubicacion" name="es_principal_ubicacion">
                                                <label class="form-check-label" for="es_principal_ubicacion">
                                                    <strong>Ubicación Principal</strong>
                                                </label>
                                                <small class="form-text text-muted d-block">
                                                    <i class="fas fa-info-circle text-info"></i> 
                                                    Marque si esta es la ubicación principal del cliente. Solo puede haber una ubicación principal por cliente.
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group mb-3">
                                            <label for="observaciones_ubicacion" class="form-label">
                                                Observaciones
                                            </label>
                                            <textarea class="form-control" id="observaciones_ubicacion" name="observaciones_ubicacion" 
                                                      rows="3" placeholder="Notas adicionales sobre la ubicación"></textarea>
                                            <small class="form-text text-muted">Información adicional o comentarios sobre la ubicación</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Sección: Estado (solo visible en modo edición) -->
                                <div class="row mt-3" id="estado_section" style="display: none;">
                                    <div class="col-12">
                                        <h5 class="border-bottom pb-2 mb-3"><i class="fas fa-toggle-on me-2"></i>Estado</h5>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="card bg-light">
                                            <div class="card-body">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="activo_ubicacion_display" disabled>
                                                    <label class="form-check-label" for="activo_ubicacion_display">
                                                        <strong id="estado_texto">Estado de la ubicación</strong>
                                                    </label>
                                                </div>
                                                <small class="form-text text-muted" id="estado_descripcion">
                                                    El estado actual de la ubicación
                                                </small>
                                                <div class="mt-2">
                                                    <small class="text-info">
                                                        <i class="fas fa-info-circle"></i> 
                                                        Para cambiar el estado, utilice el botón Activar/Desactivar en el listado.
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            <!-- Botones de acción -->
                            <div class="row mg-t-30">
                                <div class="col-md-6">
                                    <button type="button" class="btn btn-secondary btn-block" onclick="window.history.back();">
                                        <i class="fas fa-arrow-left me-2"></i>Volver
                                    </button>
                                </div>
                                <div class="col-md-6">
                                    <button type="button" class="btn btn-primary btn-block" id="btnSalvarUbicacion">
                                        <i class="fas fa-save me-2"></i>Guardar Ubicación
                                    </button>
                                </div>
                            </div>

                        </form>
                        </div><!-- card-body -->
                    </div><!-- card -->

                </div><!-- br-section-wrapper -->
            </div><!-- br-pagebody -->

            <footer class="br-footer">
                <?php include_once('../../config/template/mainFooter.php') ?>
            </footer>
        </div><!-- br-mainpanel -->
        <!-- ########## END: MAIN PANEL ########## -->

    </div><!-- wrapper -->

    <!-- Modal de Ayuda del Formulario -->
    <div class="modal fade" id="modalAyudaFormulario" tabindex="-1" aria-labelledby="modalAyudaFormularioLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalAyudaFormularioLabel">
                        <i class="fas fa-question-circle me-2"></i>Ayuda - Formulario de Ubicaciones del Cliente
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <h6 class="text-primary"><i class="fas fa-map-marker-alt me-2"></i>Información de la Ubicación</h6>
                            <p><strong>Datos básicos de la ubicación.</strong></p>
                            <ul class="list-unstyled ms-3">
                                <li><i class="fas fa-check text-success me-2"></i><strong>Nombre:</strong> Campo obligatorio, mínimo 2 y máximo 100 caracteres. Identificativo único de la ubicación.</li>
                                <li><i class="fas fa-info text-info me-2"></i><strong>País:</strong> País donde se encuentra la ubicación, por defecto "España"</li>
                            </ul>
                            <hr>
                        </div>
                        
                        <div class="col-12">
                            <h6 class="text-primary"><i class="fas fa-map-marked-alt me-2"></i>Dirección Completa</h6>
                            <p><strong>Información detallada de la ubicación física.</strong></p>
                            <ul class="list-unstyled ms-3">
                                <li><i class="fas fa-info text-info me-2"></i><strong>Dirección:</strong> Calle, número, piso, puerta (máximo 255 caracteres)</li>
                                <li><i class="fas fa-info text-info me-2"></i><strong>Código Postal:</strong> CP de la ubicación (máximo 10 caracteres)</li>
                                <li><i class="fas fa-info text-info me-2"></i><strong>Población:</strong> Ciudad o municipio (máximo 100 caracteres)</li>
                                <li><i class="fas fa-info text-info me-2"></i><strong>Provincia:</strong> Provincia o región (máximo 100 caracteres)</li>
                            </ul>
                            <hr>
                        </div>
                        
                        <div class="col-12">
                            <h6 class="text-primary"><i class="fas fa-address-card me-2"></i>Información de Contacto</h6>
                            <p><strong>Datos de contacto en esta ubicación.</strong></p>
                            <ul class="list-unstyled ms-3">
                                <li><i class="fas fa-info text-info me-2"></i><strong>Persona de Contacto:</strong> Nombre del responsable en la ubicación</li>
                                <li><i class="fas fa-phone text-info me-2"></i><strong>Teléfono:</strong> Número de teléfono de la ubicación</li>
                                <li><i class="fas fa-envelope text-info me-2"></i><strong>Email:</strong> Correo electrónico, se valida formato correcto</li>
                            </ul>
                            <hr>
                        </div>
                        
                        <div class="col-12">
                            <h6 class="text-primary"><i class="fas fa-cogs me-2"></i>Configuración</h6>
                            <p><strong>Opciones especiales de la ubicación.</strong></p>
                            <ul class="list-unstyled ms-3">
                                <li><i class="fas fa-star text-warning me-2"></i><strong>Ubicación Principal:</strong> Solo puede haber una ubicación principal por cliente. Si marca una nueva como principal, la anterior perderá esta condición.</li>
                                <li><i class="fas fa-clipboard text-secondary me-2"></i><strong>Observaciones:</strong> Notas adicionales sobre la ubicación (horarios, instrucciones de entrega, etc.)</li>
                                <li><i class="fas fa-toggle-on text-success me-2"></i><strong>Estado:</strong> Las ubicaciones nuevas se crean siempre activas. Para desactivarlas, use el listado principal.</li>
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
    <script type="text/javascript" src="formularioUbicacion.js"></script>

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
                if ($(this).scrollTop() > 100) {
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
                }, 500);
                return false;
            });

            // Hacer scroll al final del formulario
            $('#scrollToBottom').click(function() {
                $('html, body').animate({
                    scrollTop: $(document).height()
                }, 500);
                return false;
            });
        });
    </script>

</body>

</html>
