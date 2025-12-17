<!-- Modal de Ayuda para Tipos de Documento -->
<div class="modal fade" id="modalAyudaTiposdocumento" tabindex="-1" aria-labelledby="modalAyudaTiposdocumentoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <!-- Encabezado del Modal -->
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title d-flex align-items-center" id="modalAyudaTiposdocumentoLabel">
                    <i class="bi bi-question-circle-fill me-2 fs-4"></i>
                    Ayuda - Gestión de Tipos de Documento
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <!-- Cuerpo del Modal -->
            <div class="modal-body">
                
                <!-- Sección: ¿Qué son los Tipos de Documento? -->
                <div class="help-section mb-4">
                    <h6 class="text-primary d-flex align-items-center">
                        <i class="bi bi-file-earmark-text me-2"></i>
                        ¿Qué son los Tipos de Documento en el Sistema?
                    </h6>
                    <p class="text-muted">
                        Los tipos de documento son categorías que permiten clasificar y organizar los diferentes documentos 
                        del gestor documental de técnicos. Cada tipo representa una clasificación específica como manuales, 
                        procedimientos, certificados, seguros, etc., facilitando la búsqueda y gestión de la documentación.
                    </p>
                </div>

            
                <!-- Sección: Campos del Formulario -->
                <div class="help-section mb-4">
                    <h6 class="text-primary d-flex align-items-center">
                        <i class="bi bi-clipboard-data me-2"></i>
                        Campos del Formulario
                    </h6>
                    
                    <div class="accordion" id="accordionCampos">
                        <!-- Campo Código -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingCodigo">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCodigo" aria-expanded="false" aria-controls="collapseCodigo">
                                    <i class="bi bi-tag me-2"></i>
                                    Código del Tipo de Documento *
                                </button>
                            </h2>
                            <div id="collapseCodigo" class="accordion-collapse collapse" aria-labelledby="headingCodigo" data-bs-parent="#accordionCampos">
                                <div class="accordion-body">
                                    <strong>Campo obligatorio.</strong> Ingrese el código identificativo del tipo de documento.
                                    <br><strong>Ejemplos:</strong> SEG (Seguro), MAN (Manual), PROC (Procedimiento), CERT (Certificado)
                                    <br><strong>Validaciones:</strong> Máximo 20 caracteres, debe ser único en el sistema
                                </div>
                            </div>
                        </div>

                        <!-- Campo Nombre -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingNombre">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseNombre" aria-expanded="false" aria-controls="collapseNombre">
                                    <i class="bi bi-card-heading me-2"></i>
                                    Nombre del Tipo de Documento *
                                </button>
                            </h2>
                            <div id="collapseNombre" class="accordion-collapse collapse" aria-labelledby="headingNombre" data-bs-parent="#accordionCampos">
                                <div class="accordion-body">
                                    <strong>Campo obligatorio.</strong> Ingrese el nombre descriptivo del tipo de documento.
                                    <br><strong>Ejemplos:</strong> Seguro, Manual de Usuario, Procedimiento de Operación, Certificado de Calidad
                                    <br><strong>Validaciones:</strong> Máximo 100 caracteres
                                </div>
                            </div>
                        </div>

                        <!-- Campo Estado -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingEstado">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseEstado" aria-expanded="false" aria-controls="collapseEstado">
                                    <i class="bi bi-toggle-on me-2"></i>
                                    Estado del Tipo de Documento
                                </button>
                            </h2>
                            <div id="collapseEstado" class="accordion-collapse collapse" aria-labelledby="headingEstado" data-bs-parent="#accordionCampos">
                                <div class="accordion-body">
                                    <strong>Campo obligatorio.</strong> Determina si el tipo de documento está disponible para usar.
                                    <br><strong>Activo:</strong> El tipo de documento aparecerá en formularios y listas de selección
                                    <br><strong>Inactivo:</strong> El tipo de documento no estará disponible para nuevos documentos
                                    <br><strong>Nota:</strong> Los tipos inactivos conservan su historial pero no se pueden asignar a nuevos documentos
                                </div>
                            </div>
                        </div>

                        <!-- Campo Descripción -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingDescripcion">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDescripcion" aria-expanded="false" aria-controls="collapseDescripcion">
                                    <i class="bi bi-chat-left-text me-2"></i>
                                    Descripción del Tipo de Documento
                                </button>
                            </h2>
                            <div id="collapseDescripcion" class="accordion-collapse collapse" aria-labelledby="headingDescripcion" data-bs-parent="#accordionCampos">
                                <div class="accordion-body">
                                    <strong>Campo opcional.</strong> Descripción detallada del tipo de documento.
                                    <br><strong>Uso:</strong> Para aclarar el propósito, contenido esperado o características del tipo de documento
                                    <br><strong>Validaciones:</strong> Sin límite de caracteres
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sección: Ejemplos de Tipos de Documento -->
                <div class="help-section mb-4">
                    <h6 class="text-primary d-flex align-items-center">
                        <i class="bi bi-lightbulb-fill me-2"></i>
                        Ejemplos de Tipos de Documento Comunes
                    </h6>
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-secondary">Documentación Técnica:</h6>
                            <ul class="list-unstyled text-muted">
                                <li>• MAN - Manual de Usuario</li>
                                <li>• PROC - Procedimiento</li>
                                <li>• INST - Instrucciones de Trabajo</li>
                                <li>• DIAG - Diagrama Técnico</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-secondary">Documentación Legal:</h6>
                            <ul class="list-unstyled text-muted">
                                <li>• SEG - Seguro</li>
                                <li>• CERT - Certificado</li>
                                <li>• LIC - Licencia</li>
                                <li>• CONT - Contrato</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Sección: Consejos de Uso -->
                <div class="help-section mb-4">
                    <h6 class="text-primary d-flex align-items-center">
                        <i class="bi bi-star-fill me-2"></i>
                        Consejos de Uso
                    </h6>
                    <div class="alert alert-info">
                        <h6 class="alert-heading">
                            <i class="bi bi-info-circle me-2"></i>
                            Mejores Prácticas
                        </h6>
                        <ul class="mb-0">
                            <li>Use códigos cortos y descriptivos que sean fáciles de recordar</li>
                            <li>Mantenga los nombres claros y concisos</li>
                            <li>Utilice el campo descripción para aclarar el uso del tipo de documento</li>
                            <li>No elimine tipos que ya estén en uso, mejor desactívelos</li>
                            <li>Establezca una nomenclatura consistente para los códigos</li>
                            <li>Revise periódicamente los tipos activos para mantener el catálogo actualizado</li>
                        </ul>
                    </div>
                </div>

                <!-- Sección: Filtros y Búsqueda -->
                <div class="help-section mb-4">
                    <h6 class="text-primary d-flex align-items-center">
                        <i class="bi bi-funnel-fill me-2"></i>
                        Cómo usar Filtros y Búsqueda
                    </h6>
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-secondary">Búsqueda General:</h6>
                            <p class="text-muted small">
                                Use el campo de búsqueda superior para encontrar tipos de documento por cualquier dato.
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-secondary">Filtros por Columna:</h6>
                            <p class="text-muted small">
                                Use los campos del pie de tabla para filtrar por columnas específicas.
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-secondary">Filtro de Estado:</h6>
                            <p class="text-muted small">
                                Use los botones superiores para filtrar por estado: Todos, Activos, Inactivos.
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-secondary">Limpiar Filtros:</h6>
                            <p class="text-muted small">
                                Use el botón "Limpiar Filtros" para restablecer todos los filtros aplicados.
                            </p>
                        </div>
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
                                    <td>Tipo de Documento Activo</td>
                                    <td>El tipo de documento está disponible para asignar a documentos</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <i class="bi bi-x-circle text-danger fa-2x"></i>
                                    </td>
                                    <td>Tipo de Documento Inactivo</td>
                                    <td>El tipo de documento no está disponible para nuevos documentos</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <button class="btn btn-info btn-sm">
                                            <i class="fa-solid fa-edit"></i>
                                        </button>
                                    </td>
                                    <td>Botón Editar</td>
                                    <td>Permite modificar los datos del tipo de documento</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <button class="btn btn-danger btn-sm">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </td>
                                    <td>Botón Desactivar</td>
                                    <td>Desactiva el tipo de documento (solo si está activo)</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <button class="btn btn-success btn-sm">
                                            <i class="bi bi-hand-thumbs-up-fill"></i>
                                        </button>
                                    </td>
                                    <td>Botón Activar</td>
                                    <td>Activa el tipo de documento (solo si está inactivo)</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <span class="badge rounded-circle bg-success text-white" style="font-size:0.75rem;">
                                            <i class="fas fa-plus"></i>
                                        </span>
                                    </td>
                                    <td>Mostrar Detalles</td>
                                    <td>Expande la fila para ver información adicional del tipo de documento</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

            <!-- Pie del Modal -->
            <div class="modal-footer bg-light">
                <div class="text-left flex-grow-1">
                    <small class="text-muted">
                        <i class="bi bi-clock mr-1"></i>
                        Versión del sistema: MDR ERP Manager v1.0 - Última actualización: 17-12-2024
                    </small>
                </div>
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
                    <i class="bi bi-check-lg mr-2"></i>Entendido
                </button>
            </div>
        </div>
    </div>
</div>
