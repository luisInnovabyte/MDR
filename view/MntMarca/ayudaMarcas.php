<!-- Modal de Ayuda para Marcas -->
<div class="modal fade" id="modalAyudaMarcas" tabindex="-1" aria-labelledby="modalAyudaMarcasLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <!-- Encabezado del Modal -->
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title d-flex align-items-center" id="modalAyudaMarcasLabel">
                    <i class="bi bi-question-circle-fill me-2 fs-4"></i>
                    Ayuda - Gestión de Marcas de Productos
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <!-- Cuerpo del Modal -->
            <div class="modal-body">
                
                <!-- Sección: ¿Qué son las Marcas? -->
                <div class="help-section mb-4">
                    <h6 class="text-primary d-flex align-items-center">
                        <i class="bi bi-award-fill me-2"></i>
                        ¿Qué son las Marcas de Productos?
                    </h6>
                    <p class="text-muted">
                        Las marcas son identificadores que permiten clasificar y organizar los productos según su fabricante 
                        o proveedor. Facilitan la búsqueda, inventario y gestión comercial de los productos en el sistema.
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
                                    <i class="bi bi-upc me-2"></i>
                                    Código de Marca *
                                </button>
                            </h2>
                            <div id="collapseCodigo" class="accordion-collapse collapse" aria-labelledby="headingCodigo" data-bs-parent="#accordionCampos">
                                <div class="accordion-body">
                                    <strong>Campo obligatorio.</strong> Código único identificativo de la marca.
                                    <br><strong>Ejemplos:</strong> SAMSUNG, APPLE, SONY, LG, CANON
                                    <br><strong>Validaciones:</strong> Mínimo 2 caracteres, máximo 20 caracteres, debe ser único
                                    <br><strong>Formato recomendado:</strong> Letras mayúsculas, sin espacios
                                </div>
                            </div>
                        </div>

                        <!-- Campo Nombre -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingNombre">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseNombre" aria-expanded="false" aria-controls="collapseNombre">
                                    <i class="bi bi-tags me-2"></i>
                                    Nombre de la Marca *
                                </button>
                            </h2>
                            <div id="collapseNombre" class="accordion-collapse collapse" aria-labelledby="headingNombre" data-bs-parent="#accordionCampos">
                                <div class="accordion-body">
                                    <strong>Campo obligatorio.</strong> Nombre completo y oficial de la marca.
                                    <br><strong>Ejemplos:</strong> Samsung Electronics, Apple Inc., Sony Corporation, LG Electronics
                                    <br><strong>Validaciones:</strong> Mínimo 2 caracteres, máximo 100 caracteres, debe ser único
                                </div>
                            </div>
                        </div>

                        <!-- Campo Estado -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingEstado">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseEstado" aria-expanded="false" aria-controls="collapseEstado">
                                    <i class="bi bi-toggle-on me-2"></i>
                                    Estado de la Marca
                                </button>
                            </h2>
                            <div id="collapseEstado" class="accordion-collapse collapse" aria-labelledby="headingEstado" data-bs-parent="#accordionCampos">
                                <div class="accordion-body">
                                    <strong>Campo obligatorio.</strong> Determina si la marca está disponible para usar.
                                    <br><strong>Activa:</strong> La marca aparece en formularios y puede ser asignada a productos
                                    <br><strong>Inactiva:</strong> La marca no está disponible para nuevos productos
                                </div>
                            </div>
                        </div>

                        <!-- Campo Descripción -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingDescripcion">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDescripcion" aria-expanded="false" aria-controls="collapseDescripcion">
                                    <i class="bi bi-chat-left-text me-2"></i>
                                    Descripción de la Marca
                                </button>
                            </h2>
                            <div id="collapseDescripcion" class="accordion-collapse collapse" aria-labelledby="headingDescripcion" data-bs-parent="#accordionCampos">
                                <div class="accordion-body">
                                    <strong>Campo opcional.</strong> Información adicional sobre la marca.
                                    <br><strong>Uso:</strong> País de origen, especialidad, gama de productos, características destacadas
                                    <br><strong>Validaciones:</strong> Máximo 500 caracteres
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sección: Ejemplos de Marcas -->
                <div class="help-section mb-4">
                    <h6 class="text-primary d-flex align-items-center">
                        <i class="bi bi-lightbulb-fill me-2"></i>
                        Ejemplos de Marcas por Categoría
                    </h6>
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-secondary">Tecnología Audiovisual:</h6>
                            <ul class="list-unstyled text-muted">
                                <li>• Samsung Electronics</li>
                                <li>• Sony Professional</li>
                                <li>• Panasonic</li>
                                <li>• Canon</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-secondary">Audio Profesional:</h6>
                            <ul class="list-unstyled text-muted">
                                <li>• Yamaha</li>
                                <li>• Behringer</li>
                                <li>• Shure</li>
                                <li>• JBL Professional</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-secondary">Iluminación:</h6>
                            <ul class="list-unstyled text-muted">
                                <li>• Martin Professional</li>
                                <li>• Chauvet</li>
                                <li>• Ayrton</li>
                                <li>• Clay Paky</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-secondary">Accesorios:</h6>
                            <ul class="list-unstyled text-muted">
                                <li>• Manfrotto</li>
                                <li>• Gitzo</li>
                                <li>• K&M Stands</li>
                                <li>• Adam Hall</li>
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
                            <li>Use códigos cortos y fáciles de recordar basados en el nombre de la marca</li>
                            <li>Mantenga el nombre oficial completo para identificación formal</li>
                            <li>Use la descripción para anotar características importantes de la marca</li>
                            <li>Revise periódicamente las marcas para mantener el catálogo actualizado</li>
                            <li>Agrupe marcas por categorías similares para mejor organización</li>
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
                                Use el campo de búsqueda superior para encontrar marcas por cualquier dato.
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
                                    <td>Marca Activa</td>
                                    <td>La marca está disponible para asignar a productos</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <i class="bi bi-x-circle text-danger fa-2x"></i>
                                    </td>
                                    <td>Marca Inactiva</td>
                                    <td>La marca no está disponible para nuevos productos</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <button class="btn btn-info btn-sm">
                                            <i class="fa-solid fa-edit"></i>
                                        </button>
                                    </td>
                                    <td>Botón Editar</td>
                                    <td>Permite modificar los datos de la marca</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <button class="btn btn-danger btn-sm">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </td>
                                    <td>Botón Desactivar</td>
                                    <td>Desactiva la marca (solo si está activa)</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <button class="btn btn-success btn-sm">
                                            <i class="bi bi-hand-thumbs-up-fill"></i>
                                        </button>
                                    </td>
                                    <td>Botón Activar</td>
                                    <td>Activa la marca (solo si está inactiva)</td>
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