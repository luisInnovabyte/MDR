<!-- Modal de Ayuda para Unidades de Medida -->
<div class="modal fade" id="modalAyudaUnidades" tabindex="-1" aria-labelledby="modalAyudaUnidadesLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <!-- Encabezado del Modal -->
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title d-flex align-items-center" id="modalAyudaUnidadesLabel">
                    <i class="bi bi-question-circle-fill me-2 fs-4"></i>
                    Ayuda - Gestión de Unidades de Medida
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <!-- Cuerpo del Modal -->
            <div class="modal-body">
                
                <!-- Sección: ¿Qué son las Unidades de Medida? -->
                <div class="help-section mb-4">
                    <h6 class="text-primary d-flex align-items-center">
                        <i class="bi bi-rulers me-2"></i>
                        ¿Qué son las Unidades de Medida?
                    </h6>
                    <p class="text-muted">
                        Las unidades de medida son estándares utilizados para cuantificar productos, materiales o servicios en su empresa. 
                        Pueden incluir medidas de longitud, peso, volumen, área, tiempo, etc.
                    </p>
                </div>

            
                <!-- Sección: Campos del Formulario -->
                <div class="help-section mb-4">
                    <h6 class="text-primary d-flex align-items-center">
                        <i class="bi bi-clipboard-data me-2"></i>
                        Campos del Formulario
                    </h6>
                    
                    <div class="accordion" id="accordionCampos">
                        <!-- Campo Nombre Español -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingNombre">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseNombre" aria-expanded="false" aria-controls="collapseNombre">
                                    <i class="bi bi-tags me-2"></i>
                                    Nombre de la Unidad (Español) *
                                </button>
                            </h2>
                            <div id="collapseNombre" class="accordion-collapse collapse" aria-labelledby="headingNombre" data-bs-parent="#accordionCampos">
                                <div class="accordion-body">
                                    <strong>Campo obligatorio.</strong> Ingrese el nombre de la unidad en español.
                                    <br><strong>Ejemplos:</strong> Metro, Kilogramo, Litro, Pieza, Metro cuadrado
                                    <br><strong>Validaciones:</strong> Mínimo 2 caracteres, máximo 100 caracteres, debe ser único
                                </div>
                            </div>
                        </div>

                        <!-- Campo Nombre Inglés -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingNameEn">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseNameEn" aria-expanded="false" aria-controls="collapseNameEn">
                                    <i class="bi bi-translate me-2"></i>
                                    Nombre de la Unidad (Inglés) *
                                </button>
                            </h2>
                            <div id="collapseNameEn" class="accordion-collapse collapse" aria-labelledby="headingNameEn" data-bs-parent="#accordionCampos">
                                <div class="accordion-body">
                                    <strong>Campo obligatorio.</strong> Ingrese el nombre de la unidad en inglés.
                                    <br><strong>Ejemplos:</strong> Meter, Kilogram, Liter, Piece, Square meter
                                    <br><strong>Validaciones:</strong> Mínimo 2 caracteres, máximo 100 caracteres, debe ser único
                                </div>
                            </div>
                        </div>

                        <!-- Campo Símbolo -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingSimbolo">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSimbolo" aria-expanded="false" aria-controls="collapseSimbolo">
                                    <i class="bi bi-badge-ad me-2"></i>
                                    Símbolo de la Unidad
                                </button>
                            </h2>
                            <div id="collapseSimbolo" class="accordion-collapse collapse" aria-labelledby="headingSimbolo" data-bs-parent="#accordionCampos">
                                <div class="accordion-body">
                                    <strong>Campo opcional.</strong> Abreviatura o símbolo de la unidad.
                                    <br><strong>Ejemplos:</strong> m, kg, L, pz, m², cm
                                    <br><strong>Validaciones:</strong> Máximo 10 caracteres
                                </div>
                            </div>
                        </div>

                        <!-- Campo Estado -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingEstado">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseEstado" aria-expanded="false" aria-controls="collapseEstado">
                                    <i class="bi bi-toggle-on me-2"></i>
                                    Estado de la Unidad
                                </button>
                            </h2>
                            <div id="collapseEstado" class="accordion-collapse collapse" aria-labelledby="headingEstado" data-bs-parent="#accordionCampos">
                                <div class="accordion-body">
                                    <strong>Campo obligatorio.</strong> Determina si la unidad está disponible para usar.
                                    <br><strong>Activa:</strong> La unidad aparecerá en formularios y listados
                                    <br><strong>Inactiva:</strong> La unidad no estará disponible para nuevos registros
                                </div>
                            </div>
                        </div>

                        <!-- Campo Descripción -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingDescripcion">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDescripcion" aria-expanded="false" aria-controls="collapseDescripcion">
                                    <i class="bi bi-chat-left-text me-2"></i>
                                    Descripción de la Unidad
                                </button>
                            </h2>
                            <div id="collapseDescripcion" class="accordion-collapse collapse" aria-labelledby="headingDescripcion" data-bs-parent="#accordionCampos">
                                <div class="accordion-body">
                                    <strong>Campo opcional.</strong> Descripción detallada de la unidad.
                                    <br><strong>Uso:</strong> Para aclaraciones adicionales sobre el uso de la unidad
                                    <br><strong>Validaciones:</strong> Máximo 500 caracteres
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sección: Ejemplos de Unidades -->
                <div class="help-section mb-4">
                    <h6 class="text-primary d-flex align-items-center">
                        <i class="bi bi-lightbulb-fill me-2"></i>
                        Ejemplos de Unidades de Medida
                    </h6>
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-secondary">Medidas de Longitud:</h6>
                            <ul class="list-unstyled text-muted">
                                <li>• Metro (m)</li>
                                <li>• Centímetro (cm)</li>
                                <li>• Kilómetro (km)</li>
                                <li>• Pulgada (in)</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-secondary">Medidas de Peso:</h6>
                            <ul class="list-unstyled text-muted">
                                <li>• Kilogramo (kg)</li>
                                <li>• Gramo (g)</li>
                                <li>• Tonelada (t)</li>
                                <li>• Libra (lb)</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-secondary">Medidas de Volumen:</h6>
                            <ul class="list-unstyled text-muted">
                                <li>• Litro (L)</li>
                                <li>• Mililitro (ml)</li>
                                <li>• Metro cúbico (m³)</li>
                                <li>• Galón (gal)</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-secondary">Medidas de Cantidad:</h6>
                            <ul class="list-unstyled text-muted">
                                <li>• Pieza (pz)</li>
                                <li>• Docena (doc)</li>
                                <li>• Ciento (cto)</li>
                                <li>• Millar (mil)</li>
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
                            <li>Use nombres descriptivos y claros para las unidades</li>
                            <li>Mantenga consistencia en los símbolos y abreviaturas</li>
                            <li>No elimine unidades que ya estén en uso, mejor desactívelas</li>
                            <li>Revise regularmente las unidades para mantener el catálogo actualizado</li>
                            <li>Use el campo descripción para aclarar casos especiales de uso</li>
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
                                Use el campo de búsqueda superior para encontrar unidades por cualquier dato.
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
                                Use los botones superiores para filtrar por estado: Todas, Activas, Inactivas.
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
                                    <td>Unidad Activa</td>
                                    <td>La unidad está disponible para usar</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <i class="bi bi-x-circle text-danger fa-2x"></i>
                                    </td>
                                    <td>Unidad Inactiva</td>
                                    <td>La unidad no está disponible para usar</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <button class="btn btn-info btn-sm">
                                            <i class="fa-solid fa-edit"></i>
                                        </button>
                                    </td>
                                    <td>Botón Editar</td>
                                    <td>Permite modificar los datos de la unidad</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <button class="btn btn-danger btn-sm">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </td>
                                    <td>Botón Desactivar</td>
                                    <td>Desactiva la unidad (solo si está activa)</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <button class="btn btn-success btn-sm">
                                            <i class="bi bi-hand-thumbs-up-fill"></i>
                                        </button>
                                    </td>
                                    <td>Botón Activar</td>
                                    <td>Activa la unidad (solo si está inactiva)</td>
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
                         Versión del sistema: MDR v1.1 - Última actualización: 16-11-2025
                    </small>
                </div>
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
                    <i class="bi bi-check-lg me-2"></i>Entendido
                </button>
            </div>
        </div>
    </div>
</div>