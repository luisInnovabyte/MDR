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
                <a class="breadcrumb-item" href="../MntClientes/index.php">Clientes</a>
                <a class="breadcrumb-item" href="index.php">Contactos del Cliente</a>
                <span class="breadcrumb-item active" id="breadcrumb-title">Nuevo Contacto</span>
            </nav>
        </div><!-- br-pageheader -->
        
        <div class="br-pagetitle">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <h4 class="mb-0 me-2" id="page-title">Nuevo Contacto del Cliente</h4>
                    <button type="button" class="btn btn-link p-0 ms-1" data-bs-toggle="modal" data-bs-target="#modalAyudaFormulario" title="Ayuda sobre el formulario">
                        <i class="bi bi-question-circle text-primary" style="font-size: 1.3rem;"></i>
                    </button>
                </div>
                
                <!-- Botón de regreso -->
                <a href="index.php?id_cliente=<?php echo $_GET['id_cliente'] ?? ''; ?>" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Volver al listado
                </a>
            </div>

            <!-- Info del cliente -->
            <div class="mt-3" id="info-cliente">
                <div class="alert alert-info d-flex align-items-center" role="alert">
                    <i class="bi bi-info-circle me-2"></i>
                    <div>
                        <strong>Cliente:</strong> <span id="nombre-cliente">Cargando...</span>
                        <small class="ms-3 text-muted">ID: <span id="id-cliente"><?php echo $_GET['id_cliente'] ?? ''; ?></span></small>
                    </div>
                </div>
            </div>
            <br>
        </div><!-- br-pagetitle -->

        <div class="br-pagebody">
            <div class="br-section-wrapper">
                
                <!-- Formulario de Contacto -->
                <form id="formContacto">
                    <!-- Campos ocultos -->
                    <input type="hidden" name="id_contacto_cliente" id="id_contacto_cliente">
                    <input type="hidden" name="id_cliente" id="id_cliente" value="<?php echo $_GET['id_cliente'] ?? ''; ?>">

                    <!-- SECCIÓN: Información Personal -->
                    <div class="card mb-4 border-primary">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0 tx-bold">
                                <i class="fas fa-user me-2"></i>Información Personal
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-12 col-md-6">
                                    <label for="nombre_contacto_cliente" class="form-label">Nombre: <span class="tx-danger">*</span></label>
                                    <input type="text" class="form-control" name="nombre_contacto_cliente" id="nombre_contacto_cliente" maxlength="100" placeholder="Ej: Juan, María, Carlos..." required>
                                    <div class="invalid-feedback small-invalid-feedback">Ingrese un nombre válido (mínimo 2 y máximo 100 caracteres)</div>
                                    <small class="form-text text-muted">Nombre del contacto</small>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="apellidos_contacto_cliente" class="form-label">Apellidos:</label>
                                    <input type="text" class="form-control" name="apellidos_contacto_cliente" id="apellidos_contacto_cliente" maxlength="150" placeholder="Ej: García López, Pérez Martín...">
                                    <div class="invalid-feedback small-invalid-feedback">Máximo 150 caracteres</div>
                                    <small class="form-text text-muted">Apellidos del contacto</small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12 col-md-6">
                                    <label for="cargo_contacto_cliente" class="form-label">Cargo:</label>
                                    <input type="text" class="form-control" name="cargo_contacto_cliente" id="cargo_contacto_cliente" maxlength="100" placeholder="Ej: Director Comercial, Técnico de Ventas...">
                                    <div class="invalid-feedback small-invalid-feedback">Máximo 100 caracteres</div>
                                    <small class="form-text text-muted">Posición en la empresa</small>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="departamento_contacto_cliente" class="form-label">Departamento:</label>
                                    <input type="text" class="form-control" name="departamento_contacto_cliente" id="departamento_contacto_cliente" maxlength="100" placeholder="Ej: Ventas, Administración, Técnico...">
                                    <div class="invalid-feedback small-invalid-feedback">Máximo 100 caracteres</div>
                                    <small class="form-text text-muted">Departamento al que pertenece</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECCIÓN: Información de Contacto -->
                    <div class="card mb-4 border-info">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0 tx-bold">
                                <i class="fas fa-phone me-2"></i>Información de Contacto
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-12 col-md-6">
                                    <label for="telefono_contacto_cliente" class="form-label">Teléfono:</label>
                                    <input type="text" class="form-control" name="telefono_contacto_cliente" id="telefono_contacto_cliente" maxlength="50" placeholder="Ej: 912345678, +34 91 234 56 78">
                                    <div class="invalid-feedback small-invalid-feedback">Ingrese un teléfono válido</div>
                                    <small class="form-text text-muted">Teléfono fijo o directo</small>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="movil_contacto_cliente" class="form-label">Móvil:</label>
                                    <input type="text" class="form-control" name="movil_contacto_cliente" id="movil_contacto_cliente" maxlength="50" placeholder="Ej: 612345678, +34 612 34 56 78">
                                    <div class="invalid-feedback small-invalid-feedback">Ingrese un móvil válido</div>
                                    <small class="form-text text-muted">Teléfono móvil</small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12 col-md-6">
                                    <label for="email_contacto_cliente" class="form-label">Email:</label>
                                    <input type="email" class="form-control" name="email_contacto_cliente" id="email_contacto_cliente" maxlength="255" placeholder="Ej: contacto@empresa.com">
                                    <div class="invalid-feedback small-invalid-feedback">Ingrese un email válido</div>
                                    <small class="form-text text-muted">Dirección de correo electrónico</small>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="extension_contacto_cliente" class="form-label">Extensión:</label>
                                    <input type="text" class="form-control" name="extension_contacto_cliente" id="extension_contacto_cliente" maxlength="10" placeholder="Ej: 123, 1001">
                                    <div class="invalid-feedback small-invalid-feedback">Máximo 10 caracteres</div>
                                    <small class="form-text text-muted">Extensión telefónica</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECCIÓN: Configuración y Observaciones -->
                    <div class="card mb-4 border-success">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0 tx-bold">
                                <i class="fas fa-cogs me-2"></i>Configuración y Observaciones
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-12">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="principal_contacto_cliente" id="principal_contacto_cliente">
                                        <label class="form-check-label" for="principal_contacto_cliente">
                                            <strong>Contacto Principal</strong>
                                        </label>
                                        <small class="form-text text-muted d-block">Marque si es el contacto principal de este cliente</small>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12">
                                    <label for="observaciones_contacto_cliente" class="form-label">Observaciones:</label>
                                    <textarea class="form-control" name="observaciones_contacto_cliente" id="observaciones_contacto_cliente" rows="4" placeholder="Notas adicionales sobre el contacto..."></textarea>
                                    <small class="form-text text-muted">Información adicional del contacto</small>
                                </div>
                            </div>

                            <!-- Estado del contacto (solo en edición) -->
                            <div class="row" id="estado_section" style="display: none;">
                                <div class="col-12">
                                    <label class="form-label">Estado del contacto:</label>
                                    <!-- Indicador visual (solo lectura) -->
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="activo_contacto_cliente_display" checked disabled>
                                        <label class="form-check-label" for="activo_contacto_cliente_display">
                                            <strong id="estado_texto">Contacto Activo</strong>
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        <span id="estado_descripcion">Los contactos nuevos siempre se crean activos. El estado se puede cambiar desde la lista.</span>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botones de acción -->
                    <div class="card">
                        <div class="card-body text-center">
                            <button type="button" name="action" id="btnSalvarContacto" class="btn btn-primary btn-lg me-3">
                                <i class="fas fa-save me-2"></i>Guardar Contacto
                            </button>
                            <a href="index.php?id_cliente=<?php echo $_GET['id_cliente'] ?? ''; ?>" class="btn btn-secondary btn-lg">
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
                        <i class="fas fa-question-circle me-2"></i>Ayuda - Formulario de Contactos del Cliente
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <h6 class="text-primary"><i class="fas fa-user me-2"></i>Información Personal</h6>
                            <p><strong>Datos básicos del contacto.</strong></p>
                            <ul class="list-unstyled ms-3">
                                <li><i class="fas fa-check text-success me-2"></i><strong>Nombre:</strong> Campo obligatorio, mínimo 2 y máximo 100 caracteres</li>
                                <li><i class="fas fa-info text-info me-2"></i><strong>Apellidos:</strong> Campo opcional, máximo 150 caracteres</li>
                                <li><i class="fas fa-info text-info me-2"></i><strong>Cargo:</strong> Posición en la empresa, máximo 100 caracteres</li>
                                <li><i class="fas fa-info text-info me-2"></i><strong>Departamento:</strong> Departamento al que pertenece, máximo 100 caracteres</li>
                            </ul>
                            <hr>
                        </div>
                        
                        <div class="col-12">
                            <h6 class="text-primary"><i class="fas fa-phone me-2"></i>Información de Contacto</h6>
                            <p><strong>Medios para comunicarse con el contacto.</strong></p>
                            <ul class="list-unstyled ms-3">
                                <li><i class="fas fa-info text-info me-2"></i><strong>Teléfono:</strong> Número fijo o directo</li>
                                <li><i class="fas fa-info text-info me-2"></i><strong>Móvil:</strong> Teléfono móvil personal</li>
                                <li><i class="fas fa-envelope text-info me-2"></i><strong>Email:</strong> Se valida formato correcto</li>
                                <li><i class="fas fa-info text-info me-2"></i><strong>Extensión:</strong> Extensión telefónica interna</li>
                            </ul>
                            <hr>
                        </div>
                        
                        <div class="col-12">
                            <h6 class="text-primary"><i class="fas fa-cogs me-2"></i>Configuración</h6>
                            <p><strong>Opciones especiales del contacto.</strong></p>
                            <ul class="list-unstyled ms-3">
                                <li><i class="fas fa-star text-warning me-2"></i><strong>Contacto Principal:</strong> Solo puede haber uno por cliente</li>
                                <li><i class="fas fa-clipboard text-secondary me-2"></i><strong>Observaciones:</strong> Notas adicionales sobre el contacto</li>
                                <li><i class="fas fa-toggle-on text-success me-2"></i><strong>Estado:</strong> Los contactos nuevos se crean siempre activos</li>
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
    <script type="text/javascript" src="formularioContacto.js"></script>

</body>

</html>
