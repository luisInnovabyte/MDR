<!-- Modal de Ayuda para Impuestos -->
<div class="modal fade" id="modalAyudaImpuestos" tabindex="-1" aria-labelledby="modalAyudaImpuestosLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <!-- Encabezado del Modal -->
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title d-flex align-items-center" id="modalAyudaImpuestosLabel">
                    <i class="bi bi-question-circle-fill me-2 fs-4"></i>
                    Ayuda - Gestión de Impuestos
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <!-- Cuerpo del Modal -->
            <div class="modal-body">
                
                <!-- Sección: ¿Qué son los Impuestos? -->
                <div class="help-section mb-4">
                    <h6 class="text-primary d-flex align-items-center">
                        <i class="bi bi-percent me-2"></i>
                        ¿Qué son los Impuestos en el Sistema?
                    </h6>
                    <p class="text-muted">
                        Los impuestos son tributos que se aplican a productos o servicios según las normativas fiscales. 
                        El sistema permite gestionar diferentes tipos de impuestos con sus respectivas tasas para aplicar 
                        automáticamente en las operaciones comerciales.
                    </p>
                </div>

            
                <!-- Sección: Campos del Formulario -->
                <div class="help-section mb-4">
                    <h6 class="text-primary d-flex align-items-center">
                        <i class="bi bi-clipboard-data me-2"></i>
                        Campos del Formulario
                    </h6>
                    
                    <div class="accordion" id="accordionCampos">
                        <!-- Campo Tipo de Impuesto -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingTipo">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTipo" aria-expanded="false" aria-controls="collapseTipo">
                                    <i class="bi bi-tags me-2"></i>
                                    Tipo de Impuesto *
                                </button>
                            </h2>
                            <div id="collapseTipo" class="accordion-collapse collapse" aria-labelledby="headingTipo" data-bs-parent="#accordionCampos">
                                <div class="accordion-body">
                                    <strong>Campo obligatorio.</strong> Ingrese el nombre del tipo de impuesto.
                                    <br><strong>Ejemplos:</strong> IVA, IRPF, Impuesto Municipal, Tasa Especial
                                    <br><strong>Validaciones:</strong> Mínimo 2 caracteres, máximo 100 caracteres, debe ser único
                                </div>
                            </div>
                        </div>

                        <!-- Campo Tasa de Impuesto -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingTasa">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTasa" aria-expanded="false" aria-controls="collapseTasa">
                                    <i class="bi bi-calculator me-2"></i>
                                    Tasa de Impuesto (%) *
                                </button>
                            </h2>
                            <div id="collapseTasa" class="accordion-collapse collapse" aria-labelledby="headingTasa" data-bs-parent="#accordionCampos">
                                <div class="accordion-body">
                                    <strong>Campo obligatorio.</strong> Ingrese el porcentaje del impuesto.
                                    <br><strong>Ejemplos:</strong> 21, 10, 4, 0 (para exento)
                                    <br><strong>Validaciones:</strong> Número decimal entre 0 y 100, máximo 2 decimales
                                    <br><strong>Formato:</strong> Use punto (.) para decimales, ejemplo: 21.5
                                </div>
                            </div>
                        </div>

                        <!-- Campo Estado -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingEstado">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseEstado" aria-expanded="false" aria-controls="collapseEstado">
                                    <i class="bi bi-toggle-on me-2"></i>
                                    Estado del Impuesto
                                </button>
                            </h2>
                            <div id="collapseEstado" class="accordion-collapse collapse" aria-labelledby="headingEstado" data-bs-parent="#accordionCampos">
                                <div class="accordion-body">
                                    <strong>Campo obligatorio.</strong> Determina si el impuesto está disponible para usar.
                                    <br><strong>Activo:</strong> El impuesto aparecerá en formularios y cálculos
                                    <br><strong>Inactivo:</strong> El impuesto no estará disponible para nuevos registros
                                    <br><strong>Nota:</strong> Solo puede haber una tasa activa por tipo de impuesto
                                </div>
                            </div>
                        </div>

                        <!-- Campo Descripción -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingDescripcion">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDescripcion" aria-expanded="false" aria-controls="collapseDescripcion">
                                    <i class="bi bi-chat-left-text me-2"></i>
                                    Descripción del Impuesto
                                </button>
                            </h2>
                            <div id="collapseDescripcion" class="accordion-collapse collapse" aria-labelledby="headingDescripcion" data-bs-parent="#accordionCampos">
                                <div class="accordion-body">
                                    <strong>Campo opcional.</strong> Descripción detallada del impuesto.
                                    <br><strong>Uso:</strong> Para aclarar normativas, ámbito de aplicación, excepciones
                                    <br><strong>Validaciones:</strong> Máximo 500 caracteres
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sección: Ejemplos de Impuestos -->
                <div class="help-section mb-4">
                    <h6 class="text-primary d-flex align-items-center">
                        <i class="bi bi-lightbulb-fill me-2"></i>
                        Ejemplos de Impuestos Comunes
                    </h6>
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-secondary">Impuestos Generales:</h6>
                            <ul class="list-unstyled text-muted">
                                <li>• IVA General (21%)</li>
                                <li>• IVA Reducido (10%)</li>
                                <li>• IVA Súper Reducido (4%)</li>
                                <li>• Exento IVA (0%)</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-secondary">Impuestos Especiales:</h6>
                            <ul class="list-unstyled text-muted">
                                <li>• IRPF (15%, 19%, 24%)</li>
                                <li>• Recargo Equivalencia (5.2%)</li>
                                <li>• Impuesto Municipal (variable)</li>
                                <li>• Tasa Ambiental (variable)</li>
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
                            <li>Use nombres claros y específicos para los tipos de impuesto</li>
                            <li>Verifique las tasas según la normativa fiscal vigente</li>
                            <li>Solo mantenga activo un impuesto por tipo</li>
                            <li>No elimine impuestos que ya estén en uso, mejor desactívelos</li>
                            <li>Use el campo descripción para documentar cambios normativos</li>
                            <li>Revise periódicamente las tasas según cambios legislativos</li>
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
                                Use el campo de búsqueda superior para encontrar impuestos por cualquier dato.
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
                                    <td>Impuesto Activo</td>
                                    <td>El impuesto está disponible para usar en cálculos</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <i class="bi bi-x-circle text-danger fa-2x"></i>
                                    </td>
                                    <td>Impuesto Inactivo</td>
                                    <td>El impuesto no está disponible para nuevos cálculos</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <button class="btn btn-info btn-sm">
                                            <i class="fa-solid fa-edit"></i>
                                        </button>
                                    </td>
                                    <td>Botón Editar</td>
                                    <td>Permite modificar los datos del impuesto</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <button class="btn btn-danger btn-sm">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </td>
                                    <td>Botón Desactivar</td>
                                    <td>Desactiva el impuesto (solo si está activo)</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <button class="btn btn-success btn-sm">
                                            <i class="bi bi-hand-thumbs-up-fill"></i>
                                        </button>
                                    </td>
                                    <td>Botón Activar</td>
                                    <td>Activa el impuesto (solo si está inactivo)</td>
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
                <!-- Pie del Modal -->
            <div class="modal-footer bg-light">
                <div class="text-left flex-grow-1">
                    <small class="text-muted">
                        <i class="bi bi-clock mr-1"></i>
                        Versión del sistema: SMM v1.0 - Última actualización: 24-11-2025
                    </small>
                </div>
                <button type="button" class="btn btn-primary" data-dismiss="modal">
                    <i class="bi bi-check-lg mr-2"></i>Entendido
                </button>
            </div>
        </div>
    </div>
</div>