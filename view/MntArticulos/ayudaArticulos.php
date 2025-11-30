<!-- Modal de Ayuda para Artículos -->
<div class="modal fade" id="modalAyudaArticulos" tabindex="-1" aria-labelledby="modalAyudaArticulosLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <!-- Encabezado del Modal -->
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title d-flex align-items-center" id="modalAyudaArticulosLabel">
                    <i class="bi bi-question-circle-fill me-2 fs-4"></i>
                    Ayuda - Gestión de Artículos
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <!-- Cuerpo del Modal -->
            <div class="modal-body">
                
                <!-- Sección: ¿Qué son los Artículos? -->
                <div class="help-section mb-4">
                    <h6 class="text-primary d-flex align-items-center">
                        <i class="bi bi-box-seam me-2"></i>
                        ¿Qué son los Artículos?
                    </h6>
                    <p class="text-muted">
                        Los artículos son los productos individuales que se alquilan a los clientes. Cada artículo pertenece 
                        a una familia y puede tener configuraciones específicas de precio, coeficientes de descuento, y notas 
                        para presupuestos. Los artículos son la unidad básica de gestión en el sistema de alquiler.
                    </p>
                    <div class="alert alert-info py-2">
                        <small>
                            <strong>Jerarquía del sistema:</strong>
                            <br><strong>GRUPO</strong> (macro categoría) → <strong>FAMILIA</strong> (categoría) → <strong>ARTÍCULO</strong> (producto) → <strong>ELEMENTO</strong> (unidad física)
                        </small>
                    </div>
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
                                    Código de Artículo *
                                </button>
                            </h2>
                            <div id="collapseCodigo" class="accordion-collapse collapse" aria-labelledby="headingCodigo" data-bs-parent="#accordionCampos">
                                <div class="accordion-body">
                                    <strong>Campo obligatorio.</strong> Código único identificativo del artículo.
                                    <br><strong>Ejemplos:</strong> MIC-SM58, SPKR-QSC-K12, LED-PAR64, CAM-SONY-A7
                                    <br><strong>Validaciones:</strong> Máximo 50 caracteres, debe ser único
                                    <br><strong>Formato recomendado:</strong> CATEGORIA-MARCA-MODELO o código personalizado
                                </div>
                            </div>
                        </div>

                        <!-- Campo Nombre -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingNombre">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseNombre" aria-expanded="false" aria-controls="collapseNombre">
                                    <i class="bi bi-tags me-2"></i>
                                    Nombres del Artículo *
                                </button>
                            </h2>
                            <div id="collapseNombre" class="accordion-collapse collapse" aria-labelledby="headingNombre" data-bs-parent="#accordionCampos">
                                <div class="accordion-body">
                                    <strong>Campos obligatorios.</strong> Nombres descriptivos del artículo en español e inglés.
                                    <br><strong>Ejemplos español:</strong> Micrófono inalámbrico Shure SM58, Altavoz QSC K12.2 2000W
                                    <br><strong>Ejemplos inglés:</strong> Wireless microphone Shure SM58, QSC K12.2 2000W Speaker
                                    <br><strong>Validaciones:</strong> Mínimo 3 caracteres, máximo 255 caracteres cada uno
                                </div>
                            </div>
                        </div>

                        <!-- Campo Familia -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingFamilia">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFamilia" aria-expanded="false" aria-controls="collapseFamilia">
                                    <i class="bi bi-folder-fill me-2"></i>
                                    Familia del Artículo *
                                </button>
                            </h2>
                            <div id="collapseFamilia" class="accordion-collapse collapse" aria-labelledby="headingFamilia" data-bs-parent="#accordionCampos">
                                <div class="accordion-body">
                                    <strong>Campo obligatorio.</strong> Familia a la que pertenece el artículo.
                                    <br><strong>Función:</strong> Selector desplegable que conecta con la tabla de familias
                                    <br><strong>Uso:</strong> Define la categorización del artículo y hereda propiedades como imagen y coeficiente
                                    <br><strong>Ejemplos:</strong> Microfonía, Altavoces, Iluminación LED, Cámaras
                                </div>
                            </div>
                        </div>

                        <!-- Campo Unidad -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingUnidad">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseUnidad" aria-expanded="false" aria-controls="collapseUnidad">
                                    <i class="bi bi-rulers me-2"></i>
                                    Unidad de Medida
                                </button>
                            </h2>
                            <div id="collapseUnidad" class="accordion-collapse collapse" aria-labelledby="headingUnidad" data-bs-parent="#accordionCampos">
                                <div class="accordion-body">
                                    <strong>Campo opcional.</strong> Unidad de medida específica para este artículo.
                                    <br><strong>Comportamiento:</strong> Si está vacío, hereda la unidad de la familia
                                    <br><strong>Ejemplos:</strong> Pieza (pz), Día, Metro (m), Hora (h)
                                    <br><strong>Uso:</strong> Define cómo se factura y mide el artículo
                                </div>
                            </div>
                        </div>

                        <!-- Campo Precio -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingPrecio">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePrecio" aria-expanded="false" aria-controls="collapsePrecio">
                                    <i class="bi bi-currency-euro me-2"></i>
                                    Precio de Alquiler
                                </button>
                            </h2>
                            <div id="collapsePrecio" class="accordion-collapse collapse" aria-labelledby="headingPrecio" data-bs-parent="#accordionCampos">
                                <div class="accordion-body">
                                    <strong>Campo numérico.</strong> Precio base de alquiler del artículo.
                                    <br><strong>Formato:</strong> DECIMAL(10,2) - hasta 99999999.99 €
                                    <br><strong>Ejemplos:</strong> 25.00, 150.50, 1200.00
                                    <br><strong>Uso:</strong> Precio que aparecerá en presupuestos antes de aplicar coeficientes
                                </div>
                            </div>
                        </div>

                        <!-- Campo Imagen -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingImagen">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseImagen" aria-expanded="false" aria-controls="collapseImagen">
                                    <i class="bi bi-image me-2"></i>
                                    Imagen del Artículo
                                </button>
                            </h2>
                            <div id="collapseImagen" class="accordion-collapse collapse" aria-labelledby="headingImagen" data-bs-parent="#accordionCampos">
                                <div class="accordion-body">
                                    <strong>Campo opcional.</strong> Imagen específica del artículo.
                                    <br><strong>Comportamiento:</strong> Si no hay imagen, se muestra la imagen de la familia
                                    <br><strong>Formatos soportados:</strong> JPG, PNG, GIF
                                    <br><strong>Tamaño máximo:</strong> 2MB
                                    <br><strong>Uso:</strong> Facilita la identificación visual en catálogos y presupuestos
                                </div>
                            </div>
                        </div>

                        <!-- Campo Estado -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingEstado">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseEstado" aria-expanded="false" aria-controls="collapseEstado">
                                    <i class="bi bi-toggle-on me-2"></i>
                                    Estado del Artículo
                                </button>
                            </h2>
                            <div id="collapseEstado" class="accordion-collapse collapse" aria-labelledby="headingEstado" data-bs-parent="#accordionCampos">
                                <div class="accordion-body">
                                    <strong>Campo obligatorio.</strong> Determina si el artículo está disponible.
                                    <br><strong>Activo:</strong> El artículo aparece en formularios y puede ser alquilado
                                    <br><strong>Inactivo:</strong> El artículo no está disponible para nuevos presupuestos
                                    <br><strong>Nota:</strong> Nuevos artículos se crean siempre como activos
                                </div>
                            </div>
                        </div>

                        <!-- Campo Coeficientes -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingCoeficientes">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCoeficientes" aria-expanded="false" aria-controls="collapseCoeficientes">
                                    <i class="bi bi-percent me-2"></i>
                                    Coeficientes de Descuento
                                </button>
                            </h2>
                            <div id="collapseCoeficientes" class="accordion-collapse collapse" aria-labelledby="headingCoeficientes" data-bs-parent="#accordionCampos">
                                <div class="accordion-body">
                                    <strong>Campo de configuración.</strong> Determina si el artículo permite coeficientes reductores.
                                    <br><strong>Opciones:</strong>
                                    <ul class="mt-2">
                                        <li><strong>Heredar de familia:</strong> Usa la configuración de la familia (valor NULL)</li>
                                        <li><strong>Sí, permitir:</strong> Permite coeficientes independiente de la familia (valor 1)</li>
                                        <li><strong>No, sin coeficientes:</strong> No permite coeficientes (valor 0)</li>
                                    </ul>
                                    <strong>Jerarquía de prevalencia:</strong>
                                    <ol class="mt-2 mb-0">
                                        <li><strong>Presupuesto:</strong> Configuración específica del presupuesto (máxima prioridad)</li>
                                        <li><strong>Artículo:</strong> Configuración individual del artículo (este campo)</li>
                                        <li><strong>Familia:</strong> Configuración heredada de la familia (mínima prioridad)</li>
                                    </ol>
                                </div>
                            </div>
                        </div>

                        <!-- Campo Es Kit -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingKit">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseKit" aria-expanded="false" aria-controls="collapseKit">
                                    <i class="bi bi-box-seam me-2"></i>
                                    Es un Kit
                                </button>
                            </h2>
                            <div id="collapseKit" class="accordion-collapse collapse" aria-labelledby="headingKit" data-bs-parent="#accordionCampos">
                                <div class="accordion-body">
                                    <strong>Campo checkbox.</strong> Indica si el artículo es un conjunto de productos.
                                    <br><strong>Activado (1):</strong> El artículo es un kit que incluye varios equipos
                                    <br><strong>Desactivado (0):</strong> El artículo es una unidad individual
                                    <br><strong>Ejemplos de kits:</strong> Kit de iluminación completa, Pack de microfonía, Sistema de sonido completo
                                    <br><strong>Uso:</strong> Permite identificar artículos compuestos para mejor gestión de inventario
                                </div>
                            </div>
                        </div>

                        <!-- Campo Control Total -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingControl">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseControl" aria-expanded="false" aria-controls="collapseControl">
                                    <i class="bi bi-shield-check me-2"></i>
                                    Control Total
                                </button>
                            </h2>
                            <div id="collapseControl" class="accordion-collapse collapse" aria-labelledby="headingControl" data-bs-parent="#accordionCampos">
                                <div class="accordion-body">
                                    <strong>Campo checkbox.</strong> Marca artículos que requieren control especial.
                                    <br><strong>Activado (1):</strong> El artículo requiere seguimiento exhaustivo
                                    <br><strong>Desactivado (0):</strong> Control estándar
                                    <br><strong>Uso:</strong> Para equipos de alto valor, frágiles o que requieren documentación especial
                                </div>
                            </div>
                        </div>

                        <!-- Campo No Facturar -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingNoFacturar">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseNoFacturar" aria-expanded="false" aria-controls="collapseNoFacturar">
                                    <i class="bi bi-receipt me-2"></i>
                                    No Facturar
                                </button>
                            </h2>
                            <div id="collapseNoFacturar" class="accordion-collapse collapse" aria-labelledby="headingNoFacturar" data-bs-parent="#accordionCampos">
                                <div class="accordion-body">
                                    <strong>Campo checkbox.</strong> Indica si el artículo no debe facturarse.
                                    <br><strong>Activado (1):</strong> El artículo NO aparecerá en facturas
                                    <br><strong>Desactivado (0):</strong> Facturación normal
                                    <br><strong>Ejemplos:</strong> Cortesías, artículos promocionales, servicios sin cargo
                                    <br><strong>Uso:</strong> Para incluir artículos en presupuestos sin cobro
                                </div>
                            </div>
                        </div>

                        <!-- Campo Notas Presupuesto -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingNotasPresupuesto">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseNotasPresupuesto" aria-expanded="false" aria-controls="collapseNotasPresupuesto">
                                    <i class="bi bi-file-text me-2"></i>
                                    Notas para Presupuestos
                                </button>
                            </h2>
                            <div id="collapseNotasPresupuesto" class="accordion-collapse collapse" aria-labelledby="headingNotasPresupuesto" data-bs-parent="#accordionCampos">
                                <div class="accordion-body">
                                    <strong>Campos opcionales (bilingües).</strong> Texto que aparecerá en presupuestos cuando se incluya este artículo.
                                    <br><strong>Campos:</strong>
                                    <ul class="mt-2">
                                        <li><strong>Español:</strong> Notas para presupuestos (notas_presupuesto_articulo)</li>
                                        <li><strong>Inglés:</strong> Budget notes (notes_budget_articulo)</li>
                                    </ul>
                                    <strong>Ejemplos de uso:</strong>
                                    <ul class="mt-2 mb-0">
                                        <li>Especificaciones técnicas importantes</li>
                                        <li>Requisitos de instalación</li>
                                        <li>Accesorios incluidos</li>
                                        <li>Condiciones especiales de uso</li>
                                    </ul>
                                    <div class="alert alert-info mt-2 mb-0 py-2">
                                        <small><i class="bi bi-info-circle me-1"></i><strong>Nota:</strong> Estas notas se ordenan según el campo "Orden de observaciones"</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Campo Orden Observaciones -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingOrden">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOrden" aria-expanded="false" aria-controls="collapseOrden">
                                    <i class="bi bi-sort-numeric-down me-2"></i>
                                    Orden de Observaciones
                                </button>
                            </h2>
                            <div id="collapseOrden" class="accordion-collapse collapse" aria-labelledby="headingOrden" data-bs-parent="#accordionCampos">
                                <div class="accordion-body">
                                    <strong>Campo numérico.</strong> Define el orden de aparición de las notas en presupuestos.
                                    <br><strong>Valor por defecto:</strong> 200
                                    <br><strong>Rango permitido:</strong> 1 a 999
                                    <br><strong>Regla:</strong> Menor número = aparece primero
                                    <br><strong>Estrategia recomendada:</strong>
                                    <ul class="mt-2 mb-0">
                                        <li>1-50: Notas críticas (seguridad, legal)</li>
                                        <li>51-100: Notas importantes</li>
                                        <li>101-200: Notas informativas (valor por defecto para artículos)</li>
                                        <li>201-999: Notas secundarias</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Campo Observaciones Internas -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingObservaciones">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseObservaciones" aria-expanded="false" aria-controls="collapseObservaciones">
                                    <i class="bi bi-chat-left-dots me-2"></i>
                                    Observaciones Internas
                                </button>
                            </h2>
                            <div id="collapseObservaciones" class="accordion-collapse collapse" aria-labelledby="headingObservaciones" data-bs-parent="#accordionCampos">
                                <div class="accordion-body">
                                    <strong>Campo opcional.</strong> Notas internas que NO aparecen en presupuestos.
                                    <br><strong>Uso:</strong> Información para el equipo interno
                                    <br><strong>Ejemplos:</strong>
                                    <ul class="mt-2 mb-0">
                                        <li>Estado del equipo</li>
                                        <li>Historial de reparaciones</li>
                                        <li>Ubicación en almacén</li>
                                        <li>Notas técnicas de mantenimiento</li>
                                        <li>Compatibilidades con otros equipos</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sección: Ejemplos de Artículos -->
                <div class="help-section mb-4">
                    <h6 class="text-primary d-flex align-items-center">
                        <i class="bi bi-lightbulb-fill me-2"></i>
                        Ejemplos de Artículos por Categoría
                    </h6>
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-secondary">Audio:</h6>
                            <ul class="list-unstyled text-muted">
                                <li>• Micrófono inalámbrico Shure SM58</li>
                                <li>• Altavoz activo QSC K12.2 2000W</li>
                                <li>• Mesa de mezclas Yamaha MG16XU</li>
                                <li>• Amplificador QSC GX7 2x700W</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-secondary">Iluminación:</h6>
                            <ul class="list-unstyled text-muted">
                                <li>• Foco LED PAR64 RGBW 54x3W</li>
                                <li>• Cabeza móvil Beam 230W 7R</li>
                                <li>• Barra LED 8x10W RGBW DMX</li>
                                <li>• Controlador DMX Avolites Titan</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-secondary">Video:</h6>
                            <ul class="list-unstyled text-muted">
                                <li>• Proyector Epson EB-L1505U 12000lm</li>
                                <li>• Pantalla LED P3.9 500x500mm</li>
                                <li>• Cámara Sony PXW-Z190 4K</li>
                                <li>• Switcher video ATEM Mini Pro</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-secondary">Estructuras:</h6>
                            <ul class="list-unstyled text-muted">
                                <li>• Truss cuadrado 290mm x 2m aluminio</li>
                                <li>• Torre elevación VMB TE-034 6m</li>
                                <li>• Tarima escenario 2x1m antideslizante</li>
                                <li>• Base para truss con lastre</li>
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
                            <li>Use códigos descriptivos que incluyan marca y modelo para fácil identificación</li>
                            <li>Complete los nombres en ambos idiomas para soporte internacional</li>
                            <li>Asigne la familia correcta para heredar propiedades apropiadas</li>
                            <li>Configure coeficientes según necesidad de descuentos por artículo</li>
                            <li>Use imágenes específicas para artículos destacados o de alto valor</li>
                            <li>Marque como "Es un Kit" los conjuntos para mejor gestión</li>
                            <li>Active "Control Total" para equipos que requieren seguimiento exhaustivo</li>
                            <li>Use "No Facturar" para cortesías o servicios promocionales</li>
                            <li>Complete notas de presupuesto con información técnica relevante</li>
                            <li>Use observaciones internas para gestión de inventario y mantenimiento</li>
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
                                    <th>Significado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-center">
                                        <i class="bi bi-check-circle text-success fa-2x"></i>
                                    </td>
                                    <td>Artículo Activo</td>
                                    <td>El artículo está disponible para alquiler</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <i class="bi bi-x-circle text-danger fa-2x"></i>
                                    </td>
                                    <td>Artículo Inactivo</td>
                                    <td>El artículo no está disponible</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <i class="bi bi-box-seam text-primary fa-2x"></i>
                                    </td>
                                    <td>Es un Kit</td>
                                    <td>El artículo es un conjunto de equipos</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <i class="bi bi-box text-muted fa-2x"></i>
                                    </td>
                                    <td>Artículo Individual</td>
                                    <td>El artículo es una unidad individual</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <i class="bi bi-percent text-success"></i>
                                    </td>
                                    <td>Permite Coeficientes</td>
                                    <td>El artículo puede tener descuentos</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <i class="bi bi-slash-circle text-danger"></i>
                                    </td>
                                    <td>Sin Coeficientes</td>
                                    <td>El artículo no permite descuentos</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <button class="btn btn-info btn-sm">
                                            <i class="fa-solid fa-edit"></i>
                                        </button>
                                    </td>
                                    <td>Botón Editar</td>
                                    <td>Permite modificar los datos del artículo</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <button class="btn btn-danger btn-sm">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </td>
                                    <td>Botón Desactivar</td>
                                    <td>Desactiva el artículo (solo si está activo)</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <button class="btn btn-success btn-sm">
                                            <i class="bi bi-hand-thumbs-up-fill"></i>
                                        </button>
                                    </td>
                                    <td>Botón Activar</td>
                                    <td>Activa el artículo (solo si está inactivo)</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <span class="badge bg-success fs-6">5</span>
                                        <button class="btn btn-warning btn-sm">
                                            <i class="bi bi-list-ul"></i>
                                        </button>
                                    </td>
                                    <td>Contador de Elementos</td>
                                    <td>El número indica la cantidad de elementos <strong>activos</strong> asociados a este artículo. El botón permite ver el listado completo de elementos</td>
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
                        Sistema MDR v1.1 - Última actualización: <?php echo date('d-m-Y'); ?>
                    </small>
                </div>
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
                    <i class="bi bi-check-lg me-2"></i>Entendido
                </button>
            </div>
        </div>
    </div>
</div>
