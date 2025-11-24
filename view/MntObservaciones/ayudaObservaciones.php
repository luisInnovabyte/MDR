<!-- Modal de Ayuda para Observaciones -->
<div class="modal fade" id="modalAyudaObservaciones" tabindex="-1" aria-labelledby="modalAyudaObservacionesLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <!-- Encabezado del Modal -->
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title d-flex align-items-center" id="modalAyudaObservacionesLabel">
                    <i class="bi bi-question-circle-fill me-2 fs-4"></i>
                    Ayuda - Gestión de Observaciones
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <!-- Cuerpo del Modal -->
            <div class="modal-body">
                
                <!-- Sección: ¿Qué son las Observaciones? -->
                <div class="help-section mb-4">
                    <h6 class="text-primary d-flex align-items-center">
                        <i class="bi bi-chat-left-text me-2"></i>
                        ¿Qué son las Observaciones en el Sistema?
                    </h6>
                    <p class="text-muted">
                        Las observaciones son textos predefinidos que se pueden incluir en presupuestos y documentos. 
                        Permiten estandarizar información sobre condiciones, términos técnicos, legales o comerciales 
                        que se repiten frecuentemente en los documentos del negocio.
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
                                    <i class="bi bi-upc-scan me-2"></i>
                                    Código *
                                </button>
                            </h2>
                            <div id="collapseCodigo" class="accordion-collapse collapse" aria-labelledby="headingCodigo" data-bs-parent="#accordionCampos">
                                <div class="accordion-body">
                                    <strong>Campo obligatorio.</strong> Ingrese el código identificativo de la observación.
                                    <br><strong>Ejemplos:</strong> OBS-001, COND-001, TEC-001, LEG-001
                                    <br><strong>Validaciones:</strong> Máximo 20 caracteres, debe ser único
                                </div>
                            </div>
                        </div>

                        <!-- Campo Título -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingTitulo">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTitulo" aria-expanded="false" aria-controls="collapseTitulo">
                                    <i class="bi bi-card-heading me-2"></i>
                                    Título *
                                </button>
                            </h2>
                            <div id="collapseTitulo" class="accordion-collapse collapse" aria-labelledby="headingTitulo" data-bs-parent="#accordionCampos">
                                <div class="accordion-body">
                                    <strong>Campo obligatorio.</strong> Ingrese el título descriptivo de la observación.
                                    <br><strong>Ejemplos:</strong> Condiciones de pago, Garantía del producto, Términos de instalación
                                    <br><strong>Validaciones:</strong> Máximo 100 caracteres
                                </div>
                            </div>
                        </div>

                        <!-- Campo Texto -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingTexto">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTexto" aria-expanded="false" aria-controls="collapseTexto">
                                    <i class="bi bi-file-text me-2"></i>
                                    Texto *
                                </button>
                            </h2>
                            <div id="collapseTexto" class="accordion-collapse collapse" aria-labelledby="headingTexto" data-bs-parent="#accordionCampos">
                                <div class="accordion-body">
                                    <strong>Campo obligatorio.</strong> Contenido completo de la observación.
                                    <br><strong>Uso:</strong> Este es el texto que aparecerá en los documentos
                                    <br><strong>Validaciones:</strong> Campo de texto libre, obligatorio
                                </div>
                            </div>
                        </div>

                        <!-- Campo Orden -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingOrden">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOrden" aria-expanded="false" aria-controls="collapseOrden">
                                    <i class="bi bi-sort-numeric-down me-2"></i>
                                    Orden
                                </button>
                            </h2>
                            <div id="collapseOrden" class="accordion-collapse collapse" aria-labelledby="headingOrden" data-bs-parent="#accordionCampos">
                                <div class="accordion-body">
                                    <strong>Campo opcional.</strong> Número para ordenar la visualización.
                                    <br><strong>Uso:</strong> Las observaciones se mostrarán ordenadas por este número
                                    <br><strong>Validaciones:</strong> Número entero, 0 por defecto
                                    <br><strong>Ejemplos:</strong> 0, 1, 2, 10, 100
                                </div>
                            </div>
                        </div>

                        <!-- Campo Tipo -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingTipo">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTipo" aria-expanded="false" aria-controls="collapseTipo">
                                    <i class="bi bi-tag me-2"></i>
                                    Tipo
                                </button>
                            </h2>
                            <div id="collapseTipo" class="accordion-collapse collapse" aria-labelledby="headingTipo" data-bs-parent="#accordionCampos">
                                <div class="accordion-body">
                                    <strong>Campo opcional.</strong> Categoría de la observación.
                                    <br><strong>Opciones:</strong>
                                    <ul class="mt-2">
                                        <li><strong>Condiciones:</strong> Condiciones comerciales, de pago, etc.</li>
                                        <li><strong>Técnicas:</strong> Especificaciones técnicas, instalación</li>
                                        <li><strong>Legales:</strong> Términos legales, cláusulas</li>
                                        <li><strong>Comerciales:</strong> Información comercial, promociones</li>
                                        <li><strong>Otras:</strong> Otras observaciones generales</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Campo Obligatoria -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingObligatoria">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseObligatoria" aria-expanded="false" aria-controls="collapseObligatoria">
                                    <i class="bi bi-exclamation-circle me-2"></i>
                                    Obligatoria
                                </button>
                            </h2>
                            <div id="collapseObligatoria" class="accordion-collapse collapse" aria-labelledby="headingObligatoria" data-bs-parent="#accordionCampos">
                                <div class="accordion-body">
                                    <strong>Campo opcional.</strong> Indica si la observación aparece siempre.
                                    <br><strong>Sí:</strong> La observación aparecerá automáticamente en todos los presupuestos
                                    <br><strong>No:</strong> La observación debe ser seleccionada manualmente
                                    <br><strong>Por defecto:</strong> Sí (marcada como obligatoria)
                                </div>
                            </div>
                        </div>

                        <!-- Campo Estado -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingEstado">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseEstado" aria-expanded="false" aria-controls="collapseEstado">
                                    <i class="bi bi-toggle-on me-2"></i>
                                    Estado
                                </button>
                            </h2>
                            <div id="collapseEstado" class="accordion-collapse collapse" aria-labelledby="headingEstado" data-bs-parent="#accordionCampos">
                                <div class="accordion-body">
                                    <strong>Campo automático.</strong> Determina si la observación está disponible.
                                    <br><strong>Activa:</strong> La observación está disponible para usar
                                    <br><strong>Inactiva:</strong> La observación no está disponible
                                    <br><strong>Nota:</strong> Las nuevas observaciones se crean activas automáticamente
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sección: Ejemplos de Observaciones -->
                <div class="help-section mb-4">
                    <h6 class="text-primary d-flex align-items-center">
                        <i class="bi bi-lightbulb-fill me-2"></i>
                        Ejemplos de Observaciones Comunes
                    </h6>
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-secondary">Condiciones:</h6>
                            <ul class="list-unstyled text-muted">
                                <li>• Condiciones de pago</li>
                                <li>• Plazos de entrega</li>
                                <li>• Validez del presupuesto</li>
                                <li>• Forma de pago aceptadas</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-secondary">Técnicas:</h6>
                            <ul class="list-unstyled text-muted">
                                <li>• Especificaciones técnicas</li>
                                <li>• Requisitos de instalación</li>
                                <li>• Mantenimiento necesario</li>
                                <li>• Garantía técnica</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-secondary">Legales:</h6>
                            <ul class="list-unstyled text-muted">
                                <li>• Cláusulas de responsabilidad</li>
                                <li>• Términos y condiciones</li>
                                <li>• Normativa aplicable</li>
                                <li>• Protección de datos</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-secondary">Comerciales:</h6>
                            <ul class="list-unstyled text-muted">
                                <li>• Promociones vigentes</li>
                                <li>• Descuentos aplicables</li>
                                <li>• Política de devoluciones</li>
                                <li>• Servicio postventa</li>
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
                            <li>Use códigos claros y sistemáticos (ej: OBS-001, COND-001)</li>
                            <li>Escriba textos concisos y fáciles de entender</li>
                            <li>Mantenga actualizado el contenido de las observaciones</li>
                            <li>Use el campo "Tipo" para organizar las observaciones</li>
                            <li>Configure como obligatorias solo las observaciones esenciales</li>
                            <li>Use el campo "Orden" para controlar la secuencia de aparición</li>
                            <li>Revise periódicamente las observaciones activas</li>
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
                                Use el campo de búsqueda superior para encontrar observaciones por cualquier dato.
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
                                    <td>Observación Activa / Obligatoria</td>
                                    <td>La observación está disponible para usar</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <i class="bi bi-x-circle text-danger fa-2x"></i>
                                    </td>
                                    <td>Observación Inactiva / No Obligatoria</td>
                                    <td>La observación no está disponible o no es obligatoria</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <button class="btn btn-info btn-sm">
                                            <i class="fa-solid fa-edit"></i>
                                        </button>
                                    </td>
                                    <td>Botón Editar</td>
                                    <td>Permite modificar los datos de la observación</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <button class="btn btn-danger btn-sm">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </td>
                                    <td>Botón Desactivar</td>
                                    <td>Desactiva la observación (solo si está activa)</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <button class="btn btn-success btn-sm">
                                            <i class="bi bi-hand-thumbs-up-fill"></i>
                                        </button>
                                    </td>
                                    <td>Botón Activar</td>
                                    <td>Activa la observación (solo si está inactiva)</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <span class="badge rounded-circle bg-success text-white" style="font-size:0.75rem;">
                                            <i class="fas fa-plus"></i>
                                        </span>
                                    </td>
                                    <td>Mostrar Detalles</td>
                                    <td>Expande la fila para ver información adicional</td>
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
                         Versión del sistema: MDR v1.1 - Última actualización: 17-11-2025
                    </small>
                </div>
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
                    <i class="bi bi-check-lg me-2"></i>Entendido
                </button>
            </div>
        </div>
    </div>
</div>
