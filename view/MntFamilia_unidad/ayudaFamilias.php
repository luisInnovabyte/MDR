<!-- Modal de Ayuda para Familias -->
<div class="modal fade" id="modalAyudaFamilias" tabindex="-1" aria-labelledby="modalAyudaFamiliasLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <!-- Encabezado del Modal -->
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title d-flex align-items-center" id="modalAyudaFamiliasLabel">
                    <i class="bi bi-question-circle-fill me-2 fs-4"></i>
                    Ayuda - Gestión de Familias de Productos
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <!-- Cuerpo del Modal -->
            <div class="modal-body">
                
                <!-- Sección: ¿Qué son las Familias de Productos? -->
                <div class="help-section mb-4">
                    <h6 class="text-primary d-flex align-items-center">
                        <i class="bi bi-collection me-2"></i>
                        ¿Qué son las Familias de Equipos?
                    </h6>
                    <p class="text-muted">
                        Las familias de equipos son categorías que agrupan equipos audiovisuales similares o relacionados, 
                        facilitando su organización, búsqueda y gestión en el sistema de alquiler. Cada familia puede tener 
                        una imagen representativa y está asociada a una unidad de medida principal para la facturación de sus equipos.
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
                                    Código de Familia *
                                </button>
                            </h2>
                            <div id="collapseCodigo" class="accordion-collapse collapse" aria-labelledby="headingCodigo" data-bs-parent="#accordionCampos">
                                <div class="accordion-body">
                                    <strong>Campo obligatorio.</strong> Código único identificativo de la familia.
                                    <br><strong>Ejemplos:</strong> AUDIO, VIDEO, ILUMI, ACCES, CABLE
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
                                    Nombre de la Familia *
                                </button>
                            </h2>
                            <div id="collapseNombre" class="accordion-collapse collapse" aria-labelledby="headingNombre" data-bs-parent="#accordionCampos">
                                <div class="accordion-body">
                                    <strong>Campo obligatorio.</strong> Nombre descriptivo de la familia de productos.
                                    <br><strong>Ejemplos:</strong> Equipos de Audio, Equipos de Video, Iluminación Profesional, Accesorios y Cableado
                                    <br><strong>Validaciones:</strong> Mínimo 3 caracteres, máximo 100 caracteres, debe ser único
                                </div>
                            </div>
                        </div>

                        <!-- Campo Unidad de Medida -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingUnidad">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseUnidad" aria-expanded="false" aria-controls="collapseUnidad">
                                    <i class="bi bi-rulers me-2"></i>
                                    Unidad de Medida Principal *
                                </button>
                            </h2>
                            <div id="collapseUnidad" class="accordion-collapse collapse" aria-labelledby="headingUnidad" data-bs-parent="#accordionCampos">
                                <div class="accordion-body">
                                    <strong>Campo obligatorio.</strong> Unidad de medida principal para productos de esta familia.
                                    <br><strong>Función:</strong> Selector desplegable que conecta con la tabla de unidades de medida
                                    <br><strong>Ejemplos:</strong> Pieza, Hora, Metro, Día de alquiler
                                    <br><strong>Uso:</strong> Define la unidad por defecto para nuevos productos de esta familia
                                </div>
                            </div>
                        </div>

                        <!-- Campo Imagen -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingImagen">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseImagen" aria-expanded="false" aria-controls="collapseImagen">
                                    <i class="bi bi-image me-2"></i>
                                    Imagen de la Familia
                                </button>
                            </h2>
                            <div id="collapseImagen" class="accordion-collapse collapse" aria-labelledby="headingImagen" data-bs-parent="#accordionCampos">
                                <div class="accordion-body">
                                    <strong>Campo opcional.</strong> Imagen representativa de la familia de productos.
                                    <br><strong>Formatos soportados:</strong> JPG, PNG, GIF
                                    <br><strong>Tamaño recomendado:</strong> Máximo 2MB, resolución 800x600 píxeles
                                    <br><strong>Uso:</strong> Facilita la identificación visual en catálogos y presupuestos
                                </div>
                            </div>
                        </div>

                        <!-- Campo Estado -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingEstado">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseEstado" aria-expanded="false" aria-controls="collapseEstado">
                                    <i class="bi bi-toggle-on me-2"></i>
                                    Estado de la Familia
                                </button>
                            </h2>
                            <div id="collapseEstado" class="accordion-collapse collapse" aria-labelledby="headingEstado" data-bs-parent="#accordionCampos">
                                <div class="accordion-body">
                                    <strong>Campo obligatorio.</strong> Determina si la familia está disponible para usar.
                                    <br><strong>Activa:</strong> La familia aparece en formularios y puede ser asignada a productos
                                    <br><strong>Inactiva:</strong> La familia no está disponible para nuevos productos
                                </div>
                            </div>
                        </div>

                        <!-- Campo Descripción -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingDescripcion">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDescripcion" aria-expanded="false" aria-controls="collapseDescripcion">
                                    <i class="bi bi-chat-left-text me-2"></i>
                                    Descripción de la Familia
                                </button>
                            </h2>
                            <div id="collapseDescripcion" class="accordion-collapse collapse" aria-labelledby="headingDescripcion" data-bs-parent="#accordionCampos">
                                <div class="accordion-body">
                                    <strong>Campo opcional.</strong> Descripción detallada de la familia y sus características.
                                    <br><strong>Uso:</strong> Criterios de clasificación, tipos de productos incluidos, especificaciones
                                    <br><strong>Validaciones:</strong> Máximo 500 caracteres
                                </div>
                            </div>
                        </div>

                        <!-- Campo Coeficientes -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingCoeficientes">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCoeficientes" aria-expanded="false" aria-controls="collapseCoeficientes">
                                    <i class="bi bi-percent me-2"></i>
                                    Aplicar Coeficientes Reductores
                                </button>
                            </h2>
                            <div id="collapseCoeficientes" class="accordion-collapse collapse" aria-labelledby="headingCoeficientes" data-bs-parent="#accordionCampos">
                                <div class="accordion-body">
                                    <strong>Campo de configuración.</strong> Determina si esta familia permite aplicar coeficientes reductores.
                                    <br><strong>Valores:</strong> Sí / No
                                    <br><strong>Función:</strong> Conecta con el sistema de coeficientes para aplicar descuentos
                                    <br><strong>Jerarquía de prevalencia:</strong>
                                    <ol class="mt-2 mb-0">
                                        <li><strong>Presupuesto:</strong> Configuración específica del presupuesto (máxima prioridad)</li>
                                        <li><strong>Artículo:</strong> Configuración individual del producto</li>
                                        <li><strong>Familia:</strong> Configuración por defecto de la familia (mínima prioridad)</li>
                                    </ol>
                                </div>
                            </div>
                        </div>

                        <!-- Campo Grupo de Artículo -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingGrupo">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseGrupo" aria-expanded="false" aria-controls="collapseGrupo">
                                    <i class="bi bi-folder-fill me-2"></i>
                                    Grupo de Artículo
                                </button>
                            </h2>
                            <div id="collapseGrupo" class="accordion-collapse collapse" aria-labelledby="headingGrupo" data-bs-parent="#accordionCampos">
                                <div class="accordion-body">
                                    <strong>Campo opcional.</strong> Categoría de primer nivel que agrupa familias relacionadas.
                                    <br><strong>Propósito:</strong> Organizar familias en macro categorías para mejor gestión
                                    <br><strong>Jerarquía del sistema:</strong>
                                    <div class="alert alert-info mt-2 mb-2 py-2">
                                        <small>
                                            <strong>GRUPO</strong> (nivel 1 - macro categoría)
                                            <br>└── <strong>FAMILIA</strong> (nivel 2 - categoría específica)
                                            <br>&nbsp;&nbsp;&nbsp;&nbsp;└── <strong>ARTÍCULO</strong> (nivel 3 - producto)
                                            <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;└── <strong>ELEMENTO</strong> (nivel 4 - unidad física)
                                        </small>
                                    </div>
                                    <strong>Ejemplos de grupos:</strong>
                                    <ul class="mt-2 mb-0">
                                        <li><strong>AUDIO:</strong> Incluye familias como Micrófonos, Altavoces, Consolas de mezcla</li>
                                        <li><strong>ILUMINACIÓN:</strong> Incluye familias como Iluminación móvil, Iluminación estática</li>
                                        <li><strong>VÍDEO:</strong> Incluye familias como Proyección, Pantallas LED, Cámaras</li>
                                        <li><strong>ESTRUCTURAS:</strong> Incluye familias como Truss, Torres de elevación, Escenarios</li>
                                    </ul>
                                    <div class="alert alert-success mt-2 mb-0 py-2">
                                        <small><i class="bi bi-check-circle me-1"></i><strong>Ventajas:</strong> Filtrado rápido, reportes por categorías, organización visual, estadísticas por grupo</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Campo Observaciones Presupuesto -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingObservacionesPresupuesto">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseObservacionesPresupuesto" aria-expanded="false" aria-controls="collapseObservacionesPresupuesto">
                                    <i class="bi bi-file-text me-2"></i>
                                    Observaciones para Presupuestos
                                </button>
                            </h2>
                            <div id="collapseObservacionesPresupuesto" class="accordion-collapse collapse" aria-labelledby="headingObservacionesPresupuesto" data-bs-parent="#accordionCampos">
                                <div class="accordion-body">
                                    <strong>Campo opcional.</strong> Texto que aparecerá automáticamente en los presupuestos cuando se incluyan productos de esta familia.
                                    <br><strong>Propósito:</strong> Información técnica, legal o comercial que debe acompañar a esta categoría de productos
                                    <br><strong>Jerarquía de observaciones en presupuestos:</strong>
                                    <ol class="mt-2">
                                        <li><strong>Observaciones generales del sistema</strong> (configuración global)</li>
                                        <li><strong>Observaciones de FAMILIA</strong> (este campo) ← Se aplican según orden</li>
                                        <li><strong>Observaciones de ARTÍCULO</strong> (específicas del producto)</li>
                                        <li><strong>Observaciones del PRESUPUESTO</strong> (comentarios manuales del usuario)</li>
                                    </ol>
                                    <strong>Ejemplos de uso:</strong>
                                    <ul class="mt-2 mb-0">
                                        <li><strong>Estructuras metálicas:</strong> "IMPORTANTE: Requiere certificado de montaje por técnico autorizado. Incluye seguro de responsabilidad civil."</li>
                                        <li><strong>Iluminación móvil:</strong> "El equipo requiere operador técnico especializado durante el evento. No incluido en el precio."</li>
                                        <li><strong>Equipos eléctricos:</strong> "Requiere conexión trifásica 380V. Cliente debe proporcionar punto de suministro adecuado."</li>
                                        <li><strong>Mobiliario:</strong> "Transporte incluido en radio de 50km desde nuestro almacén."</li>
                                    </ul>
                                    <div class="alert alert-warning mt-2 mb-0 py-2">
                                        <small><i class="bi bi-exclamation-triangle me-1"></i><strong>Importante:</strong> Use estas observaciones para información que se repite en todos los presupuestos con productos de esta familia (advertencias legales, requisitos técnicos, condiciones de uso)</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Campo Orden Observaciones -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingOrdenObservaciones">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOrdenObservaciones" aria-expanded="false" aria-controls="collapseOrdenObservaciones">
                                    <i class="bi bi-sort-numeric-down me-2"></i>
                                    Orden de Observaciones
                                </button>
                            </h2>
                            <div id="collapseOrdenObservaciones" class="accordion-collapse collapse" aria-labelledby="headingOrdenObservaciones" data-bs-parent="#accordionCampos">
                                <div class="accordion-body">
                                    <strong>Campo numérico.</strong> Define el orden de aparición de las observaciones en los presupuestos.
                                    <br><strong>Valor por defecto:</strong> 100
                                    <br><strong>Rango permitido:</strong> 1 a 999
                                    <br><strong>Regla:</strong> Menor número = aparece primero en el presupuesto
                                    <br><strong>Estrategia de ordenamiento recomendada:</strong>
                                    <table class="table table-sm table-bordered mt-2">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Rango</th>
                                                <th>Tipo de Observación</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><span class="badge bg-danger">1-50</span></td>
                                                <td>Observaciones críticas/legales (aparecen primero)</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge bg-warning">51-100</span></td>
                                                <td>Observaciones importantes</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge bg-info">101-200</span></td>
                                                <td>Observaciones informativas</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge bg-secondary">201-999</span></td>
                                                <td>Observaciones secundarias</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <strong>Ejemplo práctico:</strong>
                                    <div class="alert alert-light mt-2 mb-0 py-2">
                                        <small>
                                            <strong>[10]</strong> Estructuras: "⚠️ OBLIGATORIO: Certificado de montaje..."<br>
                                            <strong>[15]</strong> Eléctrico: "⚡ Requiere conexión trifásica 380V..."<br>
                                            <strong>[50]</strong> Iluminación: "💡 Operador técnico no incluido..."<br>
                                            <strong>[150]</strong> Mobiliario: "📦 Transporte incluido en 50km..."
                                        </small>
                                    </div>
                                    <div class="alert alert-success mt-2 mb-0 py-2">
                                        <small><i class="bi bi-lightbulb me-1"></i><strong>Consejo:</strong> Use números bajos (1-50) para información crítica que debe leerse primero, y números altos (100+) para información complementaria</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sección: Ejemplos de Familias -->
                <div class="help-section mb-4">
                    <h6 class="text-primary d-flex align-items-center">
                        <i class="bi bi-lightbulb-fill me-2"></i>
                        Ejemplos de Familias de Equipos Audiovisuales
                    </h6>
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-secondary">Equipos de Audio:</h6>
                            <ul class="list-unstyled text-muted">
                                <li>• Altavoces y Monitores (pz)</li>
                                <li>• Microfonía y Sistemas Inalámbricos (pz)</li>
                                <li>• Mesas de Mezclas y Procesadores (pz)</li>
                                <li>• Amplificadores y Crossovers (pz)</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-secondary">Equipos de Video:</h6>
                            <ul class="list-unstyled text-muted">
                                <li>• Proyectores y Pantallas (pz)</li>
                                <li>• Cámaras y Video-grabación (pz)</li>
                                <li>• Monitores y Displays LED (pz)</li>
                                <li>• Matrices y Escaladores (pz)</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-secondary">Iluminación:</h6>
                            <ul class="list-unstyled text-muted">
                                <li>• Focos y Proyectores LED (pz)</li>
                                <li>• Máquinas de Humo y Efectos (pz)</li>
                                <li>• Mesas de Iluminación (pz)</li>
                                <li>• Estructuras y Rigging (pz)</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-secondary">Accesorios y Servicios:</h6>
                            <ul class="list-unstyled text-muted">
                                <li>• Cableado y Conectores (pz/m)</li>
                                <li>• Estructuras y Trussing (pz/m)</li>
                                <li>• Instalación y Montaje (h)</li>
                                <li>• Operación Técnica (h)</li>
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
                            <li>Use códigos cortos y descriptivos para facilitar la identificación rápida</li>
                            <li>Seleccione la unidad de medida más común para cada tipo de equipo</li>
                            <li>Use imágenes representativas para mejorar la identificación en presupuestos</li>
                            <li>Mantenga el catálogo organizado por tipo de tecnología (Audio, Video, Iluminación)</li>
                            <li>Use descripciones claras para establecer criterios de clasificación técnica</li>
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
                                Use el campo de búsqueda superior para encontrar familias por cualquier dato.
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
                                    <td>Familia Activa</td>
                                    <td>La familia está disponible para asignar a productos</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <i class="bi bi-x-circle text-danger fa-2x"></i>
                                    </td>
                                    <td>Familia Inactiva</td>
                                    <td>La familia no está disponible para nuevos productos</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <img src="..." class="img-thumbnail" style="width: 32px; height: 32px;">
                                    </td>
                                    <td>Imagen de Familia</td>
                                    <td>Muestra la imagen representativa de la familia</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <button class="btn btn-info btn-sm">
                                            <i class="fa-solid fa-edit"></i>
                                        </button>
                                    </td>
                                    <td>Botón Editar</td>
                                    <td>Permite modificar los datos de la familia</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <button class="btn btn-danger btn-sm">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </td>
                                    <td>Botón Desactivar</td>
                                    <td>Desactiva la familia (solo si está activa)</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <button class="btn btn-success btn-sm">
                                            <i class="bi bi-hand-thumbs-up-fill"></i>
                                        </button>
                                    </td>
                                    <td>Botón Activar</td>
                                    <td>Activa la familia (solo si está inactiva)</td>
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