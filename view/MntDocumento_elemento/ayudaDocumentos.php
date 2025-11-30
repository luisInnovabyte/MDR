<!-- Modal de Ayuda para Documentos de Elementos -->
<div class="modal fade" id="modalAyudaDocumentos" tabindex="-1" aria-labelledby="modalAyudaDocumentosLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <!-- Encabezado del Modal -->
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title d-flex align-items-center" id="modalAyudaDocumentosLabel">
                    <i class="bi bi-question-circle-fill me-2 fs-4"></i>
                    Ayuda - Gestión de Documentos de Elementos
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <!-- Cuerpo del Modal -->
            <div class="modal-body">
                
                <!-- Sección: ¿Qué son los Documentos de Elementos? -->
                <div class="help-section mb-4">
                    <h6 class="text-primary d-flex align-items-center">
                        <i class="bi bi-file-earmark-text me-2"></i>
                        ¿Qué son los Documentos de Elementos?
                    </h6>
                    <p class="text-muted">
                        Los documentos de elementos son archivos digitales (PDF, DOC, DOCX) asociados a elementos específicos del sistema. 
                        Permiten almacenar y gestionar manuales, certificados, fichas técnicas, garantías, contratos y otros documentos 
                        importantes relacionados con cada elemento. Esta funcionalidad facilita el acceso rápido a la documentación técnica 
                        y legal de cada elemento del inventario.
                    </p>
                </div>

            
                <!-- Sección: Campos del Formulario -->
                <div class="help-section mb-4">
                    <h6 class="text-primary d-flex align-items-center">
                        <i class="bi bi-clipboard-data me-2"></i>
                        Campos del Formulario
                    </h6>
                    
                    <div class="accordion" id="accordionCampos">
                        <!-- Campo Elemento Asociado -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingElemento">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseElemento" aria-expanded="false" aria-controls="collapseElemento">
                                    <i class="bi bi-box-seam me-2"></i>
                                    Elemento Asociado *
                                </button>
                            </h2>
                            <div id="collapseElemento" class="accordion-collapse collapse" aria-labelledby="headingElemento" data-bs-parent="#accordionCampos">
                                <div class="accordion-body">
                                    <strong>Campo obligatorio.</strong> Elemento al que pertenece este documento.
                                    <br><strong>Función:</strong> Selector desplegable que conecta con la tabla de elementos
                                    <br><strong>Validaciones:</strong> Debe seleccionar un elemento válido
                                    <br><strong>Nota:</strong> Solo aparecen elementos activos en la lista
                                </div>
                            </div>
                        </div>

                        <!-- Campo Código -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingCodigo">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCodigo" aria-expanded="false" aria-controls="collapseCodigo">
                                    <i class="bi bi-upc me-2"></i>
                                    Código de Documento *
                                </button>
                            </h2>
                            <div id="collapseCodigo" class="accordion-collapse collapse" aria-labelledby="headingCodigo" data-bs-parent="#accordionCampos">
                                <div class="accordion-body">
                                    <strong>Campo obligatorio.</strong> Código único identificativo del documento.
                                    <br><strong>Ejemplos:</strong> DOC001, CERT-2024-001, MAN-ELEM-01, FT-2024-05
                                    <br><strong>Validaciones:</strong> Mínimo 2 caracteres, máximo 50 caracteres, debe ser único
                                    <br><strong>Formato recomendado:</strong> Incluir prefijo del tipo de documento y/o año
                                </div>
                            </div>
                        </div>

                        <!-- Campo Descripción -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingDescripcion">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDescripcion" aria-expanded="false" aria-controls="collapseDescripcion">
                                    <i class="bi bi-tags me-2"></i>
                                    Descripción del Documento *
                                </button>
                            </h2>
                            <div id="collapseDescripcion" class="accordion-collapse collapse" aria-labelledby="headingDescripcion" data-bs-parent="#accordionCampos">
                                <div class="accordion-body">
                                    <strong>Campo obligatorio.</strong> Descripción breve del contenido del documento.
                                    <br><strong>Ejemplos:</strong> Manual de usuario del equipo, Certificado de conformidad CE, Ficha técnica del fabricante
                                    <br><strong>Validaciones:</strong> Mínimo 3 caracteres, máximo 200 caracteres
                                    <br><strong>Recomendación:</strong> Sea claro y conciso sobre el contenido del documento
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
                                    <br><strong>Opciones disponibles:</strong>
                                    <ul class="mt-2 mb-0">
                                        <li><strong>Manual:</strong> Manuales de usuario, instalación o mantenimiento</li>
                                        <li><strong>Certificado:</strong> Certificados de conformidad, calidad, calibración</li>
                                        <li><strong>Ficha técnica:</strong> Especificaciones técnicas del fabricante</li>
                                        <li><strong>Garantía:</strong> Documentos de garantía del equipo</li>
                                        <li><strong>Factura:</strong> Facturas de compra o reparación</li>
                                        <li><strong>Contrato:</strong> Contratos de mantenimiento, alquiler, etc.</li>
                                        <li><strong>Otro:</strong> Cualquier otro tipo de documento</li>
                                    </ul>
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
                                    <br><strong>Ubicación:</strong> Los archivos se almacenan en <code>public/img/docs_elementos/</code>
                                    <br><strong>Recomendaciones:</strong>
                                    <ul class="mt-2 mb-0">
                                        <li>Preferiblemente use formato PDF para mejor compatibilidad</li>
                                        <li>Asegúrese de que el archivo no esté dañado o corrupto</li>
                                        <li>Use nombres de archivo descriptivos</li>
                                        <li>El sistema renombrará el archivo automáticamente para evitar conflictos</li>
                                    </ul>
                                    <div class="alert alert-warning mt-2 mb-0 py-2">
                                        <small><i class="bi bi-exclamation-triangle me-1"></i><strong>Importante:</strong> El archivo tardará unos segundos en procesarse. No cierre la ventana durante la subida.</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Campo Privacidad -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingPrivacidad">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePrivacidad" aria-expanded="false" aria-controls="collapsePrivacidad">
                                    <i class="bi bi-shield-lock me-2"></i>
                                    Privacidad del Documento
                                </button>
                            </h2>
                            <div id="collapsePrivacidad" class="accordion-collapse collapse" aria-labelledby="headingPrivacidad" data-bs-parent="#accordionCampos">
                                <div class="accordion-body">
                                    <strong>Campo de configuración.</strong> Control de acceso al documento.
                                    <br><strong>Opciones:</strong>
                                    <ul class="mt-2">
                                        <li><strong>Documento Público:</strong> Visible para todos los usuarios del sistema</li>
                                        <li><strong>Documento Privado:</strong> Solo visible para usuarios con permisos especiales</li>
                                    </ul>
                                    <strong>Uso recomendado:</strong>
                                    <ul class="mb-0">
                                        <li><strong>Públicos:</strong> Manuales de usuario, fichas técnicas generales</li>
                                        <li><strong>Privados:</strong> Facturas, contratos, documentos con información sensible</li>
                                    </ul>
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
                                    <strong>Campo obligatorio.</strong> Determina si el documento está disponible.
                                    <br><strong>Activo:</strong> El documento está disponible y visible en el sistema
                                    <br><strong>Inactivo:</strong> El documento está oculto (por ejemplo, documentos obsoletos)
                                    <br><strong>Nota:</strong> El estado se establece automáticamente como activo para nuevos documentos
                                </div>
                            </div>
                        </div>

                        <!-- Campo Observaciones -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingObservaciones">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseObservaciones" aria-expanded="false" aria-controls="collapseObservaciones">
                                    <i class="bi bi-chat-left-text me-2"></i>
                                    Observaciones
                                </button>
                            </h2>
                            <div id="collapseObservaciones" class="accordion-collapse collapse" aria-labelledby="headingObservaciones" data-bs-parent="#accordionCampos">
                                <div class="accordion-body">
                                    <strong>Campo opcional.</strong> Notas adicionales o comentarios sobre el documento.
                                    <br><strong>Uso:</strong> Información complementaria, historial de revisiones, notas importantes
                                    <br><strong>Ejemplos:</strong>
                                    <ul class="mt-2 mb-0">
                                        <li>"Documento actualizado en enero 2024"</li>
                                        <li>"Certificado válido hasta diciembre 2025"</li>
                                        <li>"Versión 2.0 del manual"</li>
                                        <li>"Requiere renovación anual"</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sección: Ejemplos de Uso -->
                <div class="help-section mb-4">
                    <h6 class="text-primary d-flex align-items-center">
                        <i class="bi bi-lightbulb-fill me-2"></i>
                        Ejemplos de Documentos
                    </h6>
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-secondary">Documentación Técnica:</h6>
                            <ul class="list-unstyled text-muted">
                                <li>• Manual de usuario del equipo</li>
                                <li>• Ficha técnica del fabricante</li>
                                <li>• Diagrama de conexiones</li>
                                <li>• Instrucciones de mantenimiento</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-secondary">Documentación Legal:</h6>
                            <ul class="list-unstyled text-muted">
                                <li>• Certificado CE de conformidad</li>
                                <li>• Certificado de calibración</li>
                                <li>• Garantía del fabricante</li>
                                <li>• Contrato de mantenimiento</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-secondary">Documentación Comercial:</h6>
                            <ul class="list-unstyled text-muted">
                                <li>• Factura de compra</li>
                                <li>• Albarán de entrega</li>
                                <li>• Presupuesto de reparación</li>
                                <li>• Historial de mantenimientos</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-secondary">Otros Documentos:</h6>
                            <ul class="list-unstyled text-muted">
                                <li>• Protocolo de pruebas</li>
                                <li>• Informe de inspección</li>
                                <li>• Certificado de formación</li>
                                <li>• Declaración de conformidad</li>
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
                            <li>Digitalice todos los documentos importantes de cada elemento</li>
                            <li>Use códigos descriptivos que incluyan el tipo de documento y año</li>
                            <li>Mantenga actualizados los certificados y documentos con vencimiento</li>
                            <li>Use la opción "privado" para documentos con información sensible</li>
                            <li>Agregue observaciones con fechas de caducidad o renovación</li>
                            <li>Revise periódicamente los documentos obsoletos y desactívelos</li>
                            <li>Centralice toda la documentación técnica de cada elemento</li>
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
                                Use el campo de búsqueda superior para encontrar documentos por cualquier dato.
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-secondary">Filtros por Columna:</h6>
                            <p class="text-muted small">
                                Use los campos del pie de tabla para filtrar por columnas específicas.
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-secondary">Filtro de Privacidad:</h6>
                            <p class="text-muted small">
                                Filtre por documentos públicos o privados usando el selector correspondiente.
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
                                    <td>Documento Activo</td>
                                    <td>El documento está disponible en el sistema</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <i class="bi bi-x-circle text-danger fa-2x"></i>
                                    </td>
                                    <td>Documento Inactivo</td>
                                    <td>El documento está oculto u obsoleto</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <i class="bi bi-lock-fill text-danger fa-2x"></i>
                                    </td>
                                    <td>Documento Privado</td>
                                    <td>Solo usuarios autorizados pueden verlo</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <i class="bi bi-unlock-fill text-success fa-2x"></i>
                                    </td>
                                    <td>Documento Público</td>
                                    <td>Visible para todos los usuarios</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <button class="btn btn-info btn-sm">
                                            <i class="fa-solid fa-edit"></i>
                                        </button>
                                    </td>
                                    <td>Botón Editar</td>
                                    <td>Permite modificar los datos del documento</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <button class="btn btn-danger btn-sm">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </td>
                                    <td>Botón Desactivar</td>
                                    <td>Desactiva el documento (solo si está activo)</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <button class="btn btn-success btn-sm">
                                            <i class="bi bi-hand-thumbs-up-fill"></i>
                                        </button>
                                    </td>
                                    <td>Botón Activar</td>
                                    <td>Activa el documento (solo si está inactivo)</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <button class="btn btn-outline-primary btn-sm">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </td>
                                    <td>Ver Documento</td>
                                    <td>Abre el documento en una nueva ventana</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <button class="btn btn-outline-success btn-sm">
                                            <i class="bi bi-download"></i>
                                        </button>
                                    </td>
                                    <td>Descargar Documento</td>
                                    <td>Descarga el archivo del documento</td>
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
