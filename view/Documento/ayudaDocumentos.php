<!-- Modal de Ayuda para Gestor Documental -->
<div class="modal fade" id="modalAyudaDocumentos" tabindex="-1" aria-labelledby="modalAyudaDocumentosLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <!-- Encabezado del Modal -->
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title d-flex align-items-center" id="modalAyudaDocumentosLabel">
                    <i class="bi bi-question-circle-fill me-2 fs-4"></i>
                    Ayuda - Gestor Documental del Departamento Técnico
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <!-- Cuerpo del Modal -->
            <div class="modal-body">
                
                <!-- Sección: ¿Qué es el Gestor Documental? -->
                <div class="help-section mb-4">
                    <h6 class="text-primary d-flex align-items-center">
                        <i class="bi bi-file-earmark-text me-2"></i>
                        ¿Qué es el Gestor Documental?
                    </h6>
                    <p class="text-muted">
                        El Gestor Documental es una herramienta centralizada para almacenar y gestionar toda la documentación técnica necesaria para el departamento. 
                        Permite almacenar manuales, certificados, procedimientos, fichas técnicas y cualquier otro documento importante que el equipo técnico necesite tener siempre al alcance.
                    </p>
                    <p class="text-muted">
                        <strong>Objetivo:</strong> Mantener organizada y accesible toda la documentación técnica en formato digital, facilitando su consulta rápida y segura.
                    </p>
                </div>

                <!-- Sección: Campos del Formulario -->
                <div class="help-section mb-4">
                    <h6 class="text-primary d-flex align-items-center">
                        <i class="bi bi-clipboard-data me-2"></i>
                        Campos del Formulario
                    </h6>
                    
                    <div class="accordion" id="accordionCampos">
                        <!-- Campo Título -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingTitulo">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTitulo" aria-expanded="false" aria-controls="collapseTitulo">
                                    <i class="bi bi-file-text me-2"></i>
                                    Título del Documento *
                                </button>
                            </h2>
                            <div id="collapseTitulo" class="accordion-collapse collapse" aria-labelledby="headingTitulo" data-bs-parent="#accordionCampos">
                                <div class="accordion-body">
                                    <strong>Campo obligatorio.</strong> Nombre descriptivo del documento.
                                    <br><strong>Ejemplos:</strong> "Manual de seguridad en altura 2025", "Certificado CE equipos lifting", "Procedimiento mantenimiento preventivo"
                                    <br><strong>Validaciones:</strong> Mínimo 3 caracteres, máximo 255 caracteres
                                    <br><strong>Recomendación:</strong> Use títulos claros que identifiquen fácilmente el contenido
                                </div>
                            </div>
                        </div>

                        <!-- Campo Tipo de Documento -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingTipo">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTipo" aria-expanded="false" aria-controls="collapseTipo">
                                    <i class="bi bi-file-earmark me-2"></i>
                                    Tipo de Documento *
                                </button>
                            </h2>
                            <div id="collapseTipo" class="accordion-collapse collapse" aria-labelledby="headingTipo" data-bs-parent="#accordionCampos">
                                <div class="accordion-body">
                                    <strong>Campo obligatorio.</strong> Categoría del documento.
                                    <br><strong>Función:</strong> Permite clasificar y organizar los documentos por tipo
                                    <br><strong>Nota:</strong> Los tipos disponibles se gestionan en la tabla de tipos de documento
                                    <br><strong>Ejemplos comunes:</strong> Manual, Certificado, Procedimiento, Ficha técnica, Normativa, etc.
                                </div>
                            </div>
                        </div>

                        <!-- Campo Descripción -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingDescripcion">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDescripcion" aria-expanded="false" aria-controls="collapseDescripcion">
                                    <i class="bi bi-tags me-2"></i>
                                    Descripción del Documento
                                </button>
                            </h2>
                            <div id="collapseDescripcion" class="accordion-collapse collapse" aria-labelledby="headingDescripcion" data-bs-parent="#accordionCampos">
                                <div class="accordion-body">
                                    <strong>Campo opcional.</strong> Descripción detallada del contenido.
                                    <br><strong>Uso:</strong> Proporcione información adicional sobre el documento que no esté en el título
                                    <br><strong>Ejemplos:</strong> "Incluye procedimientos de trabajo en altura según normativa vigente", "Certificado válido hasta diciembre 2025"
                                </div>
                            </div>
                        </div>

                        <!-- Campo Archivo -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingArchivo">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseArchivo" aria-expanded="false" aria-controls="collapseArchivo">
                                    <i class="bi bi-file-pdf me-2"></i>
                                    Archivo del Documento *
                                </button>
                            </h2>
                            <div id="collapseArchivo" class="accordion-collapse collapse" aria-labelledby="headingArchivo" data-bs-parent="#accordionCampos">
                                <div class="accordion-body">
                                    <strong>Campo obligatorio.</strong> Archivo digital del documento.
                                    <br><strong>Formatos soportados:</strong> PDF, DOC, DOCX
                                    <br><strong>Tamaño máximo:</strong> 10MB por archivo
                                    <br><strong>Ubicación:</strong> Los archivos se almacenan en <code>public/img/documentos/</code>
                                    <br><strong>Recomendaciones:</strong>
                                    <ul class="mt-2 mb-0">
                                        <li>Preferiblemente use formato PDF para mejor compatibilidad</li>
                                        <li>Asegúrese de que el archivo no esté dañado</li>
                                        <li>El sistema renombrará el archivo automáticamente</li>
                                    </ul>
                                    <div class="alert alert-warning mt-2 mb-0 py-2">
                                        <small><i class="bi bi-exclamation-triangle me-1"></i><strong>Importante:</strong> El archivo tardará unos segundos en procesarse. No cierre la ventana durante la subida.</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Campo Fecha de Publicación -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingFecha">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFecha" aria-expanded="false" aria-controls="collapseFecha">
                                    <i class="bi bi-calendar me-2"></i>
                                    Fecha de Publicación
                                </button>
                            </h2>
                            <div id="collapseFecha" class="accordion-collapse collapse" aria-labelledby="headingFecha" data-bs-parent="#accordionCampos">
                                <div class="accordion-body">
                                    <strong>Campo opcional.</strong> Fecha en la que se publicó o recibió el documento.
                                    <br><strong>Uso:</strong> Útil para mantener un historial y saber la vigencia del documento
                                    <br><strong>Nota:</strong> Puede ser diferente a la fecha de creación del registro en el sistema
                                </div>
                            </div>
                        </div>

                        <!-- Campo Estado -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingEstado">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseEstado" aria-expanded="false" aria-controls="collapseEstado">
                                    <i class="bi bi-toggle-on me-2"></i>
                                    Estado del Documento
                                </button>
                            </h2>
                            <div id="collapseEstado" class="accordion-collapse collapse" aria-labelledby="headingEstado" data-bs-parent="#accordionCampos">
                                <div class="accordion-body">
                                    <strong>Campo de control.</strong> Determina si el documento está disponible.
                                    <br><strong>Activo:</strong> El documento está disponible y visible
                                    <br><strong>Inactivo:</strong> El documento está oculto (documentos obsoletos)
                                    <br><strong>Nota:</strong> Se establece automáticamente como activo para nuevos documentos
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sección: Ejemplos de Uso -->
                <div class="help-section mb-4">
                    <h6 class="text-primary d-flex align-items-center">
                        <i class="bi bi-lightbulb-fill me-2"></i>
                        Ejemplos de Documentos a Gestionar
                    </h6>
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-secondary">Documentación Técnica:</h6>
                            <ul class="list-unstyled text-muted">
                                <li>• Manuales de seguridad</li>
                                <li>• Procedimientos operativos</li>
                                <li>• Fichas técnicas de equipos</li>
                                <li>• Instrucciones de montaje</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-secondary">Certificaciones:</h6>
                            <ul class="list-unstyled text-muted">
                                <li>• Certificados CE</li>
                                <li>• Certificados de calibración</li>
                                <li>• Certificados de formación</li>
                                <li>• Homologaciones</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-secondary">Normativas:</h6>
                            <ul class="list-unstyled text-muted">
                                <li>• Normas de seguridad</li>
                                <li>• Regulaciones técnicas</li>
                                <li>• Estándares de calidad</li>
                                <li>• Protocolos internos</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-secondary">Otros Documentos:</h6>
                            <ul class="list-unstyled text-muted">
                                <li>• Planos técnicos</li>
                                <li>• Informes de inspección</li>
                                <li>• Hojas de datos de seguridad</li>
                                <li>• Guías de buenas prácticas</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Sección: Consejos de Uso -->
                <div class="help-section mb-4">
                    <h6 class="text-primary d-flex align-items-center">
                        <i class="bi bi-star-fill me-2"></i>
                        Mejores Prácticas
                    </h6>
                    <div class="alert alert-info">
                        <h6 class="alert-heading">
                            <i class="bi bi-info-circle me-2"></i>
                            Recomendaciones de Uso
                        </h6>
                        <ul class="mb-0">
                            <li>Digitalice todos los documentos importantes del departamento técnico</li>
                            <li>Use títulos descriptivos y claros</li>
                            <li>Clasifique correctamente los documentos por tipo</li>
                            <li>Mantenga actualizados los documentos con vigencia temporal</li>
                            <li>Agregue descripciones con información relevante sobre el contenido</li>
                            <li>Revise periódicamente los documentos obsoletos y desactívelos</li>
                            <li>Centralice toda la documentación técnica en este gestor</li>
                            <li>Use la búsqueda y filtros para localizar rápidamente documentos</li>
                        </ul>
                    </div>
                </div>

                <!-- Sección: Iconos y Estados -->
                <div class="help-section">
                    <h6 class="text-primary d-flex align-items-center">
                        <i class="bi bi-palette-fill me-2"></i>
                        Iconos y Estados en la Tabla
                    </h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Icono</th>
                                    <th>Descripción</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-center">
                                        <i class="bi bi-check-circle text-success fa-2x"></i>
                                    </td>
                                    <td>Documento Activo</td>
                                    <td>El documento está disponible</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <i class="bi bi-x-circle text-danger fa-2x"></i>
                                    </td>
                                    <td>Documento Inactivo</td>
                                    <td>El documento está oculto</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <button class="btn btn-info btn-sm">
                                            <i class="fa-solid fa-edit"></i>
                                        </button>
                                    </td>
                                    <td>Botón Editar</td>
                                    <td>Modificar datos del documento</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <button class="btn btn-danger btn-sm">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </td>
                                    <td>Botón Desactivar</td>
                                    <td>Desactiva el documento</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <button class="btn btn-success btn-sm">
                                            <i class="bi bi-hand-thumbs-up-fill"></i>
                                        </button>
                                    </td>
                                    <td>Botón Activar</td>
                                    <td>Activa el documento</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <button class="btn btn-outline-primary btn-sm">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </td>
                                    <td>Ver Documento</td>
                                    <td>Abre el documento en nueva ventana</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <button class="btn btn-outline-success btn-sm">
                                            <i class="bi bi-download"></i>
                                        </button>
                                    </td>
                                    <td>Descargar Documento</td>
                                    <td>Descarga el archivo</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

            <!-- Pie del Modal -->
            <div class="modal-footer bg-light">
                <div class="text-start flex-grow-1">
                    <small class="text-muted">
                        <i class="bi bi-clock me-1"></i>
                        Versión del sistema: MDR v1.1 - Última actualización: <?php echo date('d-m-Y'); ?>
                    </small>
                </div>
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
                    <i class="bi bi-check-lg me-2"></i>Entendido
                </button>
            </div>
        </div>
    </div>
</div>
