<!-- ---------------------- -->
<!--   Comprobar permisos     -->
<!-- ---------------------- -->
<?php $moduloActual = 'usuarios'; ?>
<?php require_once('../../config/template/verificarPermiso.php'); ?>

<!DOCTYPE html>
<html lang="es">

<head>
    <?php include_once('../../config/template/mainHead.php') ?>
</head>

<body>

    <?php require_once('../../config/template/mainLogo.php') ?>

    <div class="br-sideleft sideleft-scrollbar">
        <?php require_once('../../config/template/mainSidebar.php') ?>
        <?php require_once('../../config/template/mainSidebarDown.php') ?>
        <br>
    </div><!-- br-sideleft -->

    <div class="br-header">
        <?php include_once('../../config/template/mainHeader.php') ?>
    </div><!-- br-header -->

    <div class="br-sideright">
        <?php include_once('../../config/template/mainRightPanel.php') ?>
    </div><!-- br-sideright -->

    <!-- ########## START: MAIN PANEL ########## -->
    <div class="br-mainpanel">
        <div class="br-pageheader">
            <nav class="breadcrumb pd-0 mg-0 tx-12">
                <a class="breadcrumb-item" href="../Dashboard/index.php">Dashboard</a>
                <a class="breadcrumb-item" href="index.php">Gestor Documental</a>
                <span class="breadcrumb-item active" id="breadcrumb-title">Nuevo Documento</span>
            </nav>
        </div><!-- br-pageheader -->
        
        <div class="br-pagetitle">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <h4 class="mb-0 me-2" id="page-title">Nuevo Documento</h4>
                    <button type="button" class="btn btn-link p-0 ms-1" data-bs-toggle="modal" data-bs-target="#modalAyudaFormulario" title="Ayuda sobre el formulario">
                        <i class="bi bi-question-circle text-primary" style="font-size: 1.3rem;"></i>
                    </button>
                </div>
                
                <a href="index.php" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Volver al listado
                </a>
            </div>
            <br>
        </div><!-- br-pagetitle -->

        <div class="br-pagebody">
            <div class="br-section-wrapper">
                
                <!-- Formulario de Documento -->
                <form id="formDocumento" enctype="multipart/form-data">
                    <input type="hidden" name="id_documento" id="id_documento">

                    <!-- SECCIÓN: Información Básica -->
                    <div class="card mb-4 border-primary">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0 tx-bold">
                                <i class="fas fa-info-circle me-2"></i>Información del Documento
                            </h5>
                        </div>
                        <div class="card-body">
                            
                            <div class="row mb-3">
                                <div class="col-12 col-md-8">
                                    <label for="titulo_documento" class="form-label">Título del documento: <span class="tx-danger">*</span></label>
                                    <input type="text" class="form-control" name="titulo_documento" id="titulo_documento" maxlength="255" placeholder="Título descriptivo del documento..." required>
                                    <div class="invalid-feedback small-invalid-feedback">Ingrese un título válido (mínimo 3 y máximo 255 caracteres)</div>
                                    <small class="form-text text-muted">Título descriptivo del documento técnico</small>
                                </div>
                                <div class="col-12 col-md-4">
                                    <label for="id_tipo_documento_documento" class="form-label">Tipo de documento: <span class="tx-danger">*</span></label>
                                    <select class="form-control" name="id_tipo_documento_documento" id="id_tipo_documento_documento" required>
                                        <option value="">Seleccionar tipo...</option>
                                        <!-- Las opciones se cargarán dinámicamente desde la tabla tipo_documento -->
                                    </select>
                                    <div class="invalid-feedback small-invalid-feedback">Debe seleccionar un tipo de documento</div>
                                    <small class="form-text text-muted">Categoría del documento</small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12">
                                    <label for="descripcion_documento" class="form-label">Descripción:</label>
                                    <textarea class="form-control" name="descripcion_documento" id="descripcion_documento" rows="3" placeholder="Descripción breve del documento..."></textarea>
                                    <small class="form-text text-muted">Descripción del contenido del documento</small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12 col-md-6">
                                    <label for="ruta_documento" class="form-label">Archivo del documento: <span class="tx-danger">*</span></label>
                                    <input type="file" class="form-control" name="ruta_documento" id="ruta_documento" accept=".pdf,.doc,.docx" required>
                                    <input type="hidden" name="ruta_actual" id="ruta_actual">
                                    <div class="invalid-feedback small-invalid-feedback">Seleccione un archivo válido (PDF, DOC, DOCX)</div>
                                    <small class="form-text text-muted">Archivo del documento (máximo 10MB, formatos: PDF, DOC, DOCX)</small>
                                    <div class="alert alert-warning mt-2 py-2" role="alert">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        <small><strong>Advertencia:</strong> El archivo tardará unos segundos en procesarse, no salga de la pantalla.</small>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label">Archivo actual:</label>
                                    <div class="file-preview-container" style="border: 2px dashed #ddd; border-radius: 8px; padding: 10px; text-align: center; min-height: 120px; display: flex; align-items: center; justify-content: center;">
                                        <div id="file-preview" style="max-width: 100%;">
                                            <i class="fas fa-file text-muted" style="font-size: 3rem;"></i>
                                            <p class="text-muted mt-2 mb-0">Sin archivo</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12 col-md-6">
                                    <label for="fecha_publicacion_documento" class="form-label">Fecha de publicación:</label>
                                    <input type="date" class="form-control" name="fecha_publicacion_documento" id="fecha_publicacion_documento" value="<?php echo date('Y-m-d'); ?>">
                                    <small class="form-text text-muted">Fecha en la que se publicó o recibió el documento</small>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label">Estado:</label>
                                    <div class="form-check form-switch mt-2">
                                        <input class="form-check-input" type="checkbox" name="activo_documento" id="activo_documento" checked disabled>
                                        <label class="form-check-label" for="activo_documento">
                                            <span id="estado-text">Documento Activo</span>
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">El estado se establece automáticamente</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botones de acción -->
                    <div class="card">
                        <div class="card-body text-center">
                            <button type="button" name="action" id="btnSalvarDocumento" class="btn btn-primary btn-lg me-3">
                                <i class="fas fa-save me-2"></i>Guardar Documento
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

    <!-- Modal de Ayuda del Formulario -->
    <div class="modal fade" id="modalAyudaFormulario" tabindex="-1" aria-labelledby="modalAyudaFormularioLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalAyudaFormularioLabel">
                        <i class="fas fa-question-circle me-2"></i>Ayuda - Formulario de Documentos
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Este formulario permite gestionar documentos técnicos del departamento. Complete los campos obligatorios marcados con (*) para guardar el documento.</p>
                    <p><strong>Título:</strong> Nombre descriptivo del documento (ej: "Manual de seguridad 2025")</p>
                    <p><strong>Tipo:</strong> Categoría del documento según su naturaleza</p>
                    <p><strong>Archivo:</strong> El documento en formato PDF, DOC o DOCX (máximo 10MB)</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <?php include_once('../../config/template/mainJs.php') ?>

    <script src="../../public/js/tooltip-colored.js"></script>
    <script src="../../public/js/popover-colored.js"></script>
    <script type="text/javascript" src="formularioDocumento.js"></script>

</body>

</html>
