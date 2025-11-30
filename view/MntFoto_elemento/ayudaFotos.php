<!-- Modal de Ayuda para Fotos de Elementos -->
<div class="modal fade" id="modalAyudaFotos" tabindex="-1" aria-labelledby="modalAyudaFotosLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <!-- Encabezado del Modal -->
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title d-flex align-items-center" id="modalAyudaFotosLabel">
                    <i class="bi bi-question-circle-fill me-2 fs-4"></i>
                    Ayuda - Gestión de Fotos de Elementos
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <!-- Cuerpo del Modal -->
            <div class="modal-body">
                
                <!-- Sección: ¿Qué son las Fotos de Elementos? -->
                <div class="help-section mb-4">
                    <h6 class="text-primary d-flex align-items-center">
                        <i class="bi bi-image me-2"></i>
                        ¿Qué son las Fotos de Elementos?
                    </h6>
                    <p class="text-muted">
                        Las fotos de elementos son imágenes digitales (JPG, PNG, GIF, WEBP) asociadas a elementos específicos del sistema. 
                        Permiten almacenar y gestionar fotografías de equipos, componentes, instalaciones y otros elementos del inventario. 
                        Esta funcionalidad facilita la identificación visual rápida, documentación fotográfica de estado, 
                        registro de incidencias y mantenimiento de un archivo visual completo de cada elemento.
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
                                    <strong>Campo obligatorio.</strong> Elemento al que pertenece esta foto.
                                    <br><strong>Función:</strong> Selector desplegable que conecta con la tabla de elementos
                                    <br><strong>Validaciones:</strong> Debe seleccionar un elemento válido
                                    <br><strong>Nota:</strong> Solo aparecen elementos activos en la lista
                                </div>
                            </div>
                        </div>

                        <!-- Campo Descripción -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingDescripcion">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDescripcion" aria-expanded="false" aria-controls="collapseDescripcion">
                                    <i class="bi bi-tags me-2"></i>
                                    Descripción de la Foto *
                                </button>
                            </h2>
                            <div id="collapseDescripcion" class="accordion-collapse collapse" aria-labelledby="headingDescripcion" data-bs-parent="#accordionCampos">
                                <div class="accordion-body">
                                    <strong>Campo obligatorio.</strong> Descripción breve del contenido de la foto.
                                    <br><strong>Ejemplos:</strong> Vista frontal del equipo, Detalle de conexiones, Placa de características, Daño en carcasa
                                    <br><strong>Validaciones:</strong> Mínimo 3 caracteres, máximo 200 caracteres
                                    <br><strong>Recomendación:</strong> Sea claro y descriptivo sobre qué muestra la foto
                                </div>
                            </div>
                        </div>

                        <!-- Campo Archivo -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingArchivo">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseArchivo" aria-expanded="false" aria-controls="collapseArchivo">
                                    <i class="bi bi-card-image me-2"></i>
                                    Archivo de Imagen *
                                </button>
                            </h2>
                            <div id="collapseArchivo" class="accordion-collapse collapse" aria-labelledby="headingArchivo" data-bs-parent="#accordionCampos">
                                <div class="accordion-body">
                                    <strong>Campo obligatorio.</strong> Archivo de imagen digital.
                                    <br><strong>Formatos soportados:</strong> JPG, JPEG, PNG, GIF, WEBP
                                    <br><strong>Tamaño máximo:</strong> 5MB por imagen
                                    <br><strong>Ubicación:</strong> Las imágenes se almacenan en <code>public/img/fotos_elementos/</code>
                                    <br><strong>Recomendaciones:</strong>
                                    <ul class="mt-2 mb-0">
                                        <li>Use imágenes con buena iluminación y enfoque</li>
                                        <li>Preferiblemente formato JPG para fotos normales</li>
                                        <li>Use PNG si necesita transparencias</li>
                                        <li>Mantenga resolución adecuada pero no excesiva</li>
                                        <li>El sistema redimensionará automáticamente si es necesario</li>
                                    </ul>
                                    <div class="alert alert-warning mt-2 mb-0 py-2">
                                        <small><i class="bi bi-exclamation-triangle me-1"></i><strong>Importante:</strong> La imagen tardará unos segundos en procesarse. No cierre la ventana durante la subida.</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Campo Privacidad -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingPrivacidad">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePrivacidad" aria-expanded="false" aria-controls="collapsePrivacidad">
                                    <i class="bi bi-shield-lock me-2"></i>
                                    Privacidad de la Foto
                                </button>
                            </h2>
                            <div id="collapsePrivacidad" class="accordion-collapse collapse" aria-labelledby="headingPrivacidad" data-bs-parent="#accordionCampos">
                                <div class="accordion-body">
                                    <strong>Campo de configuración.</strong> Control de acceso a la foto.
                                    <br><strong>Opciones:</strong>
                                    <ul class="mt-2">
                                        <li><strong>Foto Pública:</strong> Visible para todos los usuarios del sistema</li>
                                        <li><strong>Foto Privada:</strong> Solo visible para usuarios con permisos especiales</li>
                                    </ul>
                                    <strong>Uso recomendado:</strong>
                                    <ul class="mb-0">
                                        <li><strong>Públicas:</strong> Fotos de equipos, instalaciones generales, placas de características</li>
                                        <li><strong>Privadas:</strong> Fotos de daños, incidencias, áreas restringidas, información sensible</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Campo Estado -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingEstado">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseEstado" aria-expanded="false" aria-controls="collapseEstado">
                                    <i class="bi bi-toggle-on me-2"></i>
                                    Estado de la Foto
                                </button>
                            </h2>
                            <div id="collapseEstado" class="accordion-collapse collapse" aria-labelledby="headingEstado" data-bs-parent="#accordionCampos">
                                <div class="accordion-body">
                                    <strong>Campo obligatorio.</strong> Determina si la foto está disponible.
                                    <br><strong>Activo:</strong> La foto está disponible y visible en el sistema
                                    <br><strong>Inactivo:</strong> La foto está oculta (por ejemplo, fotos obsoletas o reemplazadas)
                                    <br><strong>Nota:</strong> El estado se establece automáticamente como activo para nuevas fotos
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
                                    <strong>Campo opcional.</strong> Notas adicionales o comentarios sobre la foto.
                                    <br><strong>Uso:</strong> Información complementaria, contexto de la foto, fecha de captura, condiciones
                                    <br><strong>Ejemplos:</strong>
                                    <ul class="mt-2 mb-0">
                                        <li>"Foto tomada durante instalación inicial - Enero 2024"</li>
                                        <li>"Estado del equipo antes de la reparación"</li>
                                        <li>"Detalle de la avería en el conector"</li>
                                        <li>"Vista del elemento en su ubicación actual"</li>
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
                        Ejemplos de Fotos
                    </h6>
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-secondary">Fotografías Generales:</h6>
                            <ul class="list-unstyled text-muted">
                                <li>• Vista frontal del equipo</li>
                                <li>• Vista posterior con conexiones</li>
                                <li>• Placa de características</li>
                                <li>• Ubicación del elemento instalado</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-secondary">Fotografías Técnicas:</h6>
                            <ul class="list-unstyled text-muted">
                                <li>• Detalle de conexiones eléctricas</li>
                                <li>• Esquema de cableado visible</li>
                                <li>• Etiquetas de identificación</li>
                                <li>• Panel de control o display</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-secondary">Fotografías de Mantenimiento:</h6>
                            <ul class="list-unstyled text-muted">
                                <li>• Estado antes de mantenimiento</li>
                                <li>• Componentes reemplazados</li>
                                <li>• Limpieza o ajustes realizados</li>
                                <li>• Estado después del servicio</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-secondary">Fotografías de Incidencias:</h6>
                            <ul class="list-unstyled text-muted">
                                <li>• Daños o averías detectadas</li>
                                <li>• Desgaste de componentes</li>
                                <li>• Problemas de funcionamiento</li>
                                <li>• Evidencias para garantías</li>
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
                            <li>Capture fotos con buena iluminación y enfoque adecuado</li>
                            <li>Incluya al menos una foto frontal de cada elemento</li>
                            <li>Fotografíe la placa de características para referencia</li>
                            <li>Documente visualmente cualquier incidencia o daño</li>
                            <li>Use descripciones claras que identifiquen qué muestra la foto</li>
                            <li>Actualice las fotos cuando el estado del elemento cambie</li>
                            <li>Marque como privadas las fotos de daños o información sensible</li>
                            <li>Agregue observaciones con contexto temporal (fecha, situación)</li>
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
                                Use el campo de búsqueda superior para encontrar fotos por cualquier dato.
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
                                Filtre por fotos públicas o privadas usando el selector correspondiente.
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
                                    <td>Foto Activa</td>
                                    <td>La foto está disponible en el sistema</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <i class="bi bi-x-circle text-danger fa-2x"></i>
                                    </td>
                                    <td>Foto Inactiva</td>
                                    <td>La foto está oculta u obsoleta</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <i class="bi bi-lock-fill text-danger fa-2x"></i>
                                    </td>
                                    <td>Foto Privada</td>
                                    <td>Solo usuarios autorizados pueden verla</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <i class="bi bi-unlock-fill text-success fa-2x"></i>
                                    </td>
                                    <td>Foto Pública</td>
                                    <td>Visible para todos los usuarios</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <button class="btn btn-info btn-sm">
                                            <i class="fa-solid fa-edit"></i>
                                        </button>
                                    </td>
                                    <td>Botón Editar</td>
                                    <td>Permite modificar los datos de la foto</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <button class="btn btn-danger btn-sm">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </td>
                                    <td>Botón Desactivar</td>
                                    <td>Desactiva la foto (solo si está activa)</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <button class="btn btn-success btn-sm">
                                            <i class="bi bi-hand-thumbs-up-fill"></i>
                                        </button>
                                    </td>
                                    <td>Botón Activar</td>
                                    <td>Activa la foto (solo si está inactiva)</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <img src="#" class="img-thumbnail" style="max-height: 40px; max-width: 60px;" alt="Vista previa">
                                    </td>
                                    <td>Miniatura</td>
                                    <td>Click para ver la imagen en tamaño completo</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <button class="btn btn-outline-success btn-sm">
                                            <i class="bi bi-download"></i>
                                        </button>
                                    </td>
                                    <td>Descargar Foto</td>
                                    <td>Descarga el archivo de imagen</td>
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
