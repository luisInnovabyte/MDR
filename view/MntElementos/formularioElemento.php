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
    <!-- jQuery UI CSS para datepicker -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
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
                <a class="breadcrumb-item" href="../MntArticulos/index.php">Artículos</a>
                <a class="breadcrumb-item" href="index.php">Elementos del Artículo</a>
                <span class="breadcrumb-item active" id="breadcrumb-title">Nuevo Elemento</span>
            </nav>
        </div><!-- br-pageheader -->
        
        <div class="br-pagetitle">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <h4 class="mb-0 me-2" id="page-title">Nuevo Elemento del Artículo</h4>
                    <button type="button" class="btn btn-link p-0 ms-1" data-bs-toggle="modal" data-bs-target="#modalAyudaFormulario" title="Ayuda sobre el formulario">
                        <i class="bi bi-question-circle text-primary" style="font-size: 1.3rem;"></i>
                    </button>
                </div>
                
                <!-- Botón de regreso -->
                <a href="index.php?id_articulo=<?php echo $_GET['id_articulo'] ?? ''; ?>" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Volver al listado
                </a>
            </div>

            <!-- Info del artículo -->
            <div class="mt-3" id="info-articulo">
                <div class="alert alert-info d-flex align-items-center" role="alert">
                    <i class="bi bi-box-seam me-2"></i>
                    <div>
                        <strong>Artículo:</strong> <span id="nombre-articulo">Cargando...</span>
                        <small class="ms-3 text-muted">Código: <span id="codigo-articulo">--</span> | ID: <span id="id-articulo"><?php echo $_GET['id_articulo'] ?? ''; ?></span></small>
                    </div>
                </div>
            </div>
            <br>
        </div><!-- br-pagetitle -->

        <div class="br-pagebody">
            <div class="br-section-wrapper">
                
                <!-- Formulario de Elemento -->
                <form id="formElemento">
                    <!-- Campos ocultos -->
                    <input type="hidden" name="id_elemento" id="id_elemento">
                    <input type="hidden" name="id_articulo_elemento" id="id_articulo_elemento" value="<?php echo $_GET['id_articulo'] ?? ''; ?>">

                    <!-- SECCIÓN: Información Básica -->
                    <div class="card mb-4 border-primary">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0 tx-bold">
                                <i class="fas fa-info-circle me-2"></i>Información Básica
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-12">
                                    <label for="descripcion_elemento" class="form-label">Descripción: <span class="tx-danger">*</span></label>
                                    <input type="text" class="form-control" name="descripcion_elemento" id="descripcion_elemento" maxlength="255" placeholder="Ej: Cámara Sony A7III unidad 1..." required>
                                    <div class="invalid-feedback small-invalid-feedback">Ingrese una descripción válida (mínimo 3 y máximo 255 caracteres)</div>
                                    <small class="form-text text-muted">Descripción específica de este elemento</small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12 col-md-4" id="codigo_elemento_container" style="display: none;">
                                    <label for="codigo_elemento_display" class="form-label">Código del Elemento:</label>
                                    <input type="text" class="form-control bg-light" id="codigo_elemento_display" disabled>
                                    <small class="form-text text-muted"><i class="bi bi-info-circle me-1"></i>Generado automáticamente</small>
                                </div>
                                <div class="col-12 col-md-4">
                                    <label for="codigo_barras_elemento" class="form-label">Código de Barras:</label>
                                    <input type="text" class="form-control" name="codigo_barras_elemento" id="codigo_barras_elemento" maxlength="100" placeholder="Ej: 8435047655295">
                                    <div class="invalid-feedback small-invalid-feedback">Código de barras único</div>
                                    <small class="form-text text-muted">Código de barras del elemento (único)</small>
                                </div>
                                <div class="col-12 col-md-4">
                                    <label for="numero_serie_elemento" class="form-label">Número de Serie:</label>
                                    <input type="text" class="form-control" name="numero_serie_elemento" id="numero_serie_elemento" maxlength="100" placeholder="Ej: SN12345678">
                                    <div class="invalid-feedback small-invalid-feedback">Número de serie único</div>
                                    <small class="form-text text-muted">Número de serie del fabricante (único)</small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12 col-md-4">
                                    <label for="id_marca_elemento" class="form-label">Marca:</label>
                                    <select class="form-control" name="id_marca_elemento" id="id_marca_elemento">
                                        <option value="">Seleccione una marca</option>
                                    </select>
                                    <small class="form-text text-muted">Marca del elemento</small>
                                </div>
                                <div class="col-12 col-md-4">
                                    <label for="modelo_elemento" class="form-label">Modelo:</label>
                                    <input type="text" class="form-control" name="modelo_elemento" id="modelo_elemento" maxlength="100" placeholder="Ej: A7 III, FX6, etc.">
                                    <small class="form-text text-muted">Modelo específico del elemento</small>
                                </div>
                                <div class="col-12 col-md-4">
                                    <label for="id_estado_elemento" class="form-label">Estado:</label>
                                    <select class="form-control" name="id_estado_elemento" id="id_estado_elemento">
                                        <option value="1">Disponible</option>
                                    </select>
                                    <small class="form-text text-muted">Estado actual del elemento</small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12 col-md-4">
                                    <label for="nave_elemento" class="form-label">Nave/Almacén:</label>
                                    <input type="text" class="form-control" name="nave_elemento" id="nave_elemento" maxlength="50" placeholder="Ej: Nave 1, Nave Principal">
                                    <small class="form-text text-muted">Edificio o almacén donde se encuentra</small>
                                </div>
                                <div class="col-12 col-md-4">
                                    <label for="pasillo_columna_elemento" class="form-label">Pasillo/Columna:</label>
                                    <input type="text" class="form-control" name="pasillo_columna_elemento" id="pasillo_columna_elemento" maxlength="50" placeholder="Ej: A-5, B-12">
                                    <small class="form-text text-muted">Referencia de pasillo y columna</small>
                                </div>
                                <div class="col-12 col-md-4">
                                    <label for="altura_elemento" class="form-label">Altura/Nivel:</label>
                                    <input type="text" class="form-control" name="altura_elemento" id="altura_elemento" maxlength="50" placeholder="Ej: Planta baja, Nivel 2">
                                    <small class="form-text text-muted">Altura o nivel de ubicación</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECCIÓN: Tipo de Propiedad -->
                    <div class="card mb-4 border-dark">
                        <div class="card-header bg-dark text-white">
                            <h5 class="mb-0 tx-bold">
                                <i class="fas fa-building me-2"></i>Tipo de Propiedad del Equipo
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-12">
                                    <label class="form-label">¿El equipo es propio de la empresa? <span class="tx-danger">*</span></label>
                                    <div class="d-flex gap-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="es_propio_elemento" id="es_propio_si" value="1" checked>
                                            <label class="form-check-label" for="es_propio_si">
                                                <i class="fas fa-check-circle text-success me-1"></i>
                                                <strong>SÍ - Equipo Propio</strong>
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="es_propio_elemento" id="es_propio_no" value="0">
                                            <label class="form-check-label" for="es_propio_no">
                                                <i class="fas fa-handshake text-primary me-1"></i>
                                                <strong>NO - Equipo Alquilado a Proveedor</strong>
                                            </label>
                                        </div>
                                    </div>
                                    <small class="form-text text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Seleccione "SÍ" si el equipo pertenece a la empresa. Seleccione "NO" si el equipo es alquilado a un proveedor.
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECCIÓN: Datos de Adquisición (SOLO EQUIPOS PROPIOS) (SOLO EQUIPOS PROPIOS) -->
                    <div class="card mb-4 border-info" id="seccion_equipo_propio">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0 tx-bold">
                                <i class="fas fa-shopping-cart me-2"></i>Datos de Adquisición (Equipo Propio)
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-12 col-md-4">
                                    <label for="fecha_compra_elemento" class="form-label">Fecha de Compra:</label>
                                    <input type="text" class="form-control datepicker" name="fecha_compra_elemento" id="fecha_compra_elemento" placeholder="dd/mm/yyyy" autocomplete="off">
                                    <small class="form-text text-muted">Fecha de adquisición del elemento</small>
                                </div>
                                <div class="col-12 col-md-4">
                                    <label for="precio_compra_elemento" class="form-label">Precio de Compra (€):</label>
                                    <input type="number" class="form-control" name="precio_compra_elemento" id="precio_compra_elemento" min="0" step="0.01" placeholder="0.00">
                                    <small class="form-text text-muted">Precio pagado por el elemento</small>
                                </div>
                                <div class="col-12 col-md-4">
                                    <label for="fecha_alta_elemento" class="form-label">Fecha de Alta:</label>
                                    <input type="text" class="form-control datepicker" name="fecha_alta_elemento" id="fecha_alta_elemento" placeholder="dd/mm/yyyy" autocomplete="off">
                                    <small class="form-text text-muted">Fecha de puesta en servicio</small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12">
                                    <label for="id_proveedor_compra_elemento" class="form-label">Proveedor de Compra:</label>
                                    <select class="form-control" name="id_proveedor_compra_elemento" id="id_proveedor_compra_elemento">
                                        <option value="">Seleccione un proveedor</option>
                                    </select>
                                    <small class="form-text text-muted">Proveedor que vendió este elemento</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECCIÓN: Datos de Alquiler (SOLO EQUIPOS ALQUILADOS) -->
                    <div class="card mb-4 border-primary" id="seccion_equipo_alquilado" style="display: none;">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0 tx-bold">
                                <i class="fas fa-handshake me-2"></i>Datos de Alquiler (Equipo Alquilado a Proveedor)
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-12 col-md-6">
                                    <label for="id_proveedor_alquiler_elemento" class="form-label">Proveedor de Alquiler: <span class="tx-danger">*</span></label>
                                    <select class="form-control" name="id_proveedor_alquiler_elemento" id="id_proveedor_alquiler_elemento">
                                        <option value="">Seleccione un proveedor</option>
                                    </select>
                                    <small class="form-text text-muted">Proveedor al que alquilamos este equipo</small>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="precio_dia_alquiler_elemento" class="form-label">Precio por Día (€): <span class="tx-danger">*</span></label>
                                    <input type="number" class="form-control" name="precio_dia_alquiler_elemento" id="precio_dia_alquiler_elemento" min="0" step="0.01" placeholder="0.00">
                                    <small class="form-text text-muted">Precio diario que pagamos al proveedor</small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12 col-md-6">
                                    <label for="id_forma_pago_alquiler_elemento" class="form-label">Forma de Pago:</label>
                                    <select class="form-control" name="id_forma_pago_alquiler_elemento" id="id_forma_pago_alquiler_elemento">
                                        <option value="">Seleccione una forma de pago</option>
                                    </select>
                                    <small class="form-text text-muted">Condiciones de pago acordadas con el proveedor</small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12">
                                    <label for="observaciones_alquiler_elemento" class="form-label">Condiciones de Alquiler:</label>
                                    <textarea class="form-control" name="observaciones_alquiler_elemento" id="observaciones_alquiler_elemento" rows="3" placeholder="Ej: Mínimo 7 días, incluye seguro, contacto: Juan Pérez, etc."></textarea>
                                    <small class="form-text text-muted">Condiciones especiales: mínimo de días, restricciones, contacto del proveedor, etc.</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECCIÓN: Garantía y Mantenimiento (SOLO EQUIPOS PROPIOS) (SOLO EQUIPOS PROPIOS) -->
                    <div class="card mb-4 border-warning" id="seccion_garantia_mantenimiento">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="mb-0 tx-bold">
                                <i class="fas fa-tools me-2"></i>Garantía y Mantenimiento (Equipo Propio)
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-12 col-md-6">
                                    <label for="fecha_fin_garantia_elemento" class="form-label">Fecha Fin de Garantía:</label>
                                    <input type="text" class="form-control datepicker" name="fecha_fin_garantia_elemento" id="fecha_fin_garantia_elemento" placeholder="dd/mm/yyyy" autocomplete="off">
                                    <small class="form-text text-muted">Fecha en que finaliza la garantía</small>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="proximo_mantenimiento_elemento" class="form-label">Próximo Mantenimiento:</label>
                                    <input type="text" class="form-control datepicker" name="proximo_mantenimiento_elemento" id="proximo_mantenimiento_elemento" placeholder="dd/mm/yyyy" autocomplete="off">
                                    <small class="form-text text-muted">Fecha del próximo mantenimiento programado</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECCIÓN: Observaciones -->
                    <div class="card mb-4 border-success">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0 tx-bold">
                                <i class="fas fa-clipboard me-2"></i>Observaciones
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-12">
                                    <label for="observaciones_elemento" class="form-label">Observaciones:</label>
                                    <textarea class="form-control" name="observaciones_elemento" id="observaciones_elemento" rows="4" placeholder="Notas adicionales sobre el elemento, incidencias, características especiales..."></textarea>
                                    <small class="form-text text-muted">Información adicional del elemento</small>
                                </div>
                            </div>

                            <!-- Estado del elemento (solo en edición) -->
                            <div class="row" id="estado_section" style="display: none;">
                                <div class="col-12">
                                    <label class="form-label">Estado del elemento:</label>
                                    <!-- Indicador visual (solo lectura) -->
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="activo_elemento_display" checked disabled>
                                        <label class="form-check-label" for="activo_elemento_display">
                                            <strong id="estado_texto">Elemento Activo</strong>
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        <span id="estado_descripcion">Los elementos nuevos siempre se crean activos. El estado se puede cambiar desde la lista.</span>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botones de acción -->
                    <div class="card">
                        <div class="card-body text-center">
                            <button type="button" name="action" id="btnSalvarElemento" class="btn btn-primary btn-lg me-3">
                                <i class="fas fa-save me-2"></i>Guardar Elemento
                            </button>
                            <a href="index.php?id_articulo=<?php echo $_GET['id_articulo'] ?? ''; ?>" class="btn btn-secondary btn-lg">
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
                        <i class="fas fa-question-circle me-2"></i>Ayuda - Formulario de Elementos
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <h6 class="text-primary"><i class="fas fa-info-circle me-2"></i>Información Básica</h6>
                            <p><strong>Datos principales del elemento.</strong></p>
                            <ul class="list-unstyled ms-3">
                                <li><i class="fas fa-check text-success me-2"></i><strong>Descripción:</strong> Campo obligatorio, mínimo 3 y máximo 255 caracteres</li>
                                <li><i class="fas fa-info text-info me-2"></i><strong>Código:</strong> Se genera automáticamente al guardar</li>
                                <li><i class="fas fa-barcode text-info me-2"></i><strong>Código de Barras:</strong> Debe ser único si se introduce</li>
                                <li><i class="fas fa-hashtag text-info me-2"></i><strong>Número de Serie:</strong> Debe ser único si se introduce</li>
                            </ul>
                            <hr>
                        </div>
                        
                        <div class="col-12">
                            <h6 class="text-primary"><i class="fas fa-shopping-cart me-2"></i>Datos de Adquisición</h6>
                            <p><strong>Información de compra del elemento.</strong></p>
                            <ul class="list-unstyled ms-3">
                                <li><i class="fas fa-calendar text-info me-2"></i><strong>Fecha Compra:</strong> Fecha de adquisición</li>
                                <li><i class="fas fa-euro-sign text-info me-2"></i><strong>Precio Compra:</strong> Importe pagado</li>
                                <li><i class="fas fa-store text-info me-2"></i><strong>Proveedor:</strong> Quién vendió el elemento</li>
                            </ul>
                            <hr>
                        </div>
                        
                        <div class="col-12">
                            <h6 class="text-primary"><i class="fas fa-tools me-2"></i>Mantenimiento</h6>
                            <p><strong>Gestión de garantías y mantenimientos.</strong></p>
                            <ul class="list-unstyled ms-3">
                                <li><i class="fas fa-shield-alt text-warning me-2"></i><strong>Fin Garantía:</strong> Para control de garantías</li>
                                <li><i class="fas fa-wrench text-warning me-2"></i><strong>Próximo Mantenimiento:</strong> Programación de revisiones</li>
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
    <!-- jQuery UI JS para datepicker -->
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <!-- jQuery UI i18n para español -->
    <script src="https://code.jquery.com/ui/1.13.2/i18n/datepicker-es.min.js"></script>
    <!-- ------------------------- -->
    <!--     END mainJs.php        -->
    <!-- ------------------------- -->
    <script type="text/javascript" src="formularioElemento.js"></script>

    <!-- Botones flotantes para navegación -->
    <!-- Botón para ir al inicio del formulario -->
    <button id="scrollToTop" class="btn btn-primary" style="position: fixed; bottom: 140px; right: 30px; z-index: 1000; border-radius: 50%; 
    width: 50px; height: 50px; display: none; box-shadow: 0 4px 8px rgba(0,0,0,0.3);" title="Ir al inicio del formulario">
        <i class="fas fa-arrow-up"></i>
    </button>
    
    <!-- Botón para ir al final del formulario -->
    <button id="scrollToBottom" class="btn btn-primary" style="position: fixed; bottom: 80px; right: 30px; z-index: 1000; border-radius: 50%; 
    width: 50px; height: 50px; display: none; box-shadow: 0 4px 8px rgba(0,0,0,0.3);" title="Ir al final del formulario">
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
