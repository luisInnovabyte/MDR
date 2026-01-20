<!-- Modal de Ayuda -->
<div class="modal fade" id="modalAyudaLineas" tabindex="-1" aria-labelledby="modalAyudaLineasLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="modalAyudaLineasLabel">
                    <i class="bi bi-question-circle-fill me-2"></i>Ayuda - Líneas de Presupuesto
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                
                <!-- Introducción -->
                <div class="alert alert-info">
                    <h6 class="alert-heading">
                        <i class="bi bi-info-circle me-2"></i>¿Qué son las líneas de presupuesto?
                    </h6>
                    <p class="mb-0">
                        Las líneas de presupuesto representan cada elemento (artículo, kit, texto o sección) 
                        que forma parte de un presupuesto. Cada línea tiene su propia cantidad, precio, 
                        descuentos y coeficientes que se calculan automáticamente para obtener el total.
                    </p>
                </div>

                <!-- Sistema de Versiones -->
                <div class="card mb-3 border-warning">
                    <div class="card-header bg-warning bg-opacity-10">
                        <h6 class="mb-0">
                            <i class="bi bi-lock me-2 text-warning"></i>Sistema de Versiones y Bloqueo
                        </h6>
                    </div>
                    <div class="card-body">
                        <p><strong>IMPORTANTE:</strong> Solo se pueden editar las líneas de versiones en estado <span class="badge bg-warning text-dark">BORRADOR</span>.</p>
                        
                        <h6 class="mt-3 mb-2">Estados de Versión:</h6>
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <span class="badge bg-warning text-dark me-2">BORRADOR</span>
                                <strong>Editable</strong> - Se pueden crear, modificar y eliminar líneas libremente.
                            </li>
                            <li class="mb-2">
                                <span class="badge bg-info me-2">ENVIADO</span>
                                <strong>Bloqueado</strong> - La versión ha sido enviada al cliente y no se puede modificar.
                            </li>
                            <li class="mb-2">
                                <span class="badge bg-success me-2">ACEPTADO</span>
                                <strong>Bloqueado</strong> - Versión aceptada por el cliente. Inmutable.
                            </li>
                            <li class="mb-2">
                                <span class="badge bg-danger me-2">RECHAZADO</span>
                                <strong>Bloqueado</strong> - Versión rechazada. Se debe crear una nueva versión.
                            </li>
                            <li class="mb-2">
                                <span class="badge bg-secondary me-2">CADUCADO</span>
                                <strong>Bloqueado</strong> - La validez del presupuesto ha expirado.
                            </li>
                        </ul>

                        <div class="alert alert-warning mb-0 mt-3">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>Regla de Oro:</strong> Una vez que una versión sale del estado "borrador", 
                            <strong>NUNCA</strong> se puede volver a editar. Para hacer cambios, debe crear una nueva versión 
                            desde el listado de presupuestos.
                        </div>
                    </div>
                </div>

                <!-- Tipos de Línea -->
                <div class="card mb-3">
                    <div class="card-header bg-primary bg-opacity-10">
                        <h6 class="mb-0">
                            <i class="bi bi-tag me-2 text-primary"></i>Tipos de Línea
                        </h6>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0">
                            <li class="mb-3">
                                <span class="badge bg-primary me-2">Artículo</span>
                                <strong>Elemento individual</strong><br>
                                <small class="text-muted">
                                    Representa un artículo específico del catálogo (toldo, estructura, accesorio, etc.). 
                                    Se puede aplicar coeficiente por jornadas de alquiler.
                                </small>
                            </li>
                            <li class="mb-3">
                                <span class="badge bg-info me-2">Kit</span>
                                <strong>Conjunto de artículos</strong><br>
                                <small class="text-muted">
                                    Agrupa varios artículos relacionados con un precio conjunto. 
                                    Ideal para paquetes predefinidos.
                                </small>
                            </li>
                            <li class="mb-3">
                                <span class="badge bg-secondary me-2">Sección</span>
                                <strong>Separador visual</strong><br>
                                <small class="text-muted">
                                    Título para agrupar líneas relacionadas en el presupuesto. 
                                    No tiene precio asociado.
                                </small>
                            </li>
                            <li class="mb-0">
                                <span class="badge bg-light text-dark me-2">Texto</span>
                                <strong>Texto libre</strong><br>
                                <small class="text-muted">
                                    Permite añadir notas o descripciones sin artículo asociado. 
                                    Puede tener precio si es necesario.
                                </small>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Sistema de Coeficientes -->
                <div class="card mb-3 border-warning">
                    <div class="card-header bg-warning bg-opacity-10">
                        <h6 class="mb-0">
                            <i class="bi bi-calculator me-2 text-warning"></i>Sistema de Coeficientes
                        </h6>
                    </div>
                    <div class="card-body">
                        <p>
                            <strong>¿Qué es un coeficiente?</strong><br>
                            Es un multiplicador que se aplica al precio según el número de jornadas (días) de alquiler.
                            A más jornadas, mayor es el coeficiente, pero con tarifas degresivas.
                        </p>

                        <h6 class="mt-3 mb-2">Ejemplo de Cálculo:</h6>
                        <div class="bg-light p-3 rounded">
                            <code>
                                Precio unitario: 100 €<br>
                                Cantidad: 2 unidades<br>
                                Jornadas: 3 días → Coeficiente: 2.0x<br>
                                <strong>Base = (100 € × 2) × 2.0 = 400 €</strong>
                            </code>
                        </div>

                        <div class="alert alert-info mt-3 mb-0">
                            <i class="bi bi-lightbulb me-2"></i>
                            <strong>Nota:</strong> Los coeficientes se gestionan en el módulo de Coeficientes y se 
                            aplican automáticamente según las jornadas especificadas.
                        </div>
                    </div>
                </div>

                <!-- Cálculos Automáticos -->
                <div class="card mb-3">
                    <div class="card-header bg-success bg-opacity-10">
                        <h6 class="mb-0">
                            <i class="bi bi-calculator-fill me-2 text-success"></i>Cálculos Automáticos
                        </h6>
                    </div>
                    <div class="card-body">
                        <p>Todos los cálculos se realizan automáticamente siguiendo esta fórmula:</p>
                        
                        <ol class="mb-3">
                            <li><strong>Subtotal sin descuento:</strong> Cantidad × Precio Unitario</li>
                            <li><strong>Aplicar descuento:</strong> Subtotal - (Subtotal × Descuento%)</li>
                            <li><strong>Aplicar coeficiente:</strong> Resultado × Coeficiente (si aplica)</li>
                            <li><strong>Base Imponible:</strong> Resultado del paso 3</li>
                            <li><strong>IVA:</strong> Base Imponible × IVA%</li>
                            <li><strong>Total Línea:</strong> Base Imponible + IVA</li>
                        </ol>

                        <div class="alert alert-warning mb-0">
                            <i class="bi bi-info-circle me-2"></i>
                            Los totales del presupuesto (pie) se actualizan automáticamente al guardar o eliminar líneas.
                        </div>
                    </div>
                </div>

                <!-- Consejos y Buenas Prácticas -->
                <div class="card mb-0 border-primary">
                    <div class="card-header bg-primary bg-opacity-10">
                        <h6 class="mb-0">
                            <i class="bi bi-lightbulb me-2 text-primary"></i>Consejos y Buenas Prácticas
                        </h6>
                    </div>
                    <div class="card-body">
                        <ul class="mb-0">
                            <li class="mb-2">
                                <strong>Orden de las líneas:</strong> Se asigna automáticamente al crear la línea. 
                                Puedes reorganizarlas desde la vista de edición.
                            </li>
                            <li class="mb-2">
                                <strong>Descripciones claras:</strong> Usa descripciones detalladas para que el cliente 
                                entienda exactamente qué está contratando.
                            </li>
                            <li class="mb-2">
                                <strong>Uso de secciones:</strong> Agrupa líneas relacionadas usando líneas de tipo "Sección" 
                                para mejorar la legibilidad del presupuesto.
                            </li>
                            <li class="mb-2">
                                <strong>Revisión antes de enviar:</strong> Verifica todos los cálculos y cantidades 
                                antes de cambiar el estado de la versión a "enviado".
                            </li>
                            <li class="mb-2">
                                <strong>Ubicaciones:</strong> Si el artículo se entrega en una ubicación específica del cliente, 
                                asegúrate de especificarla en la línea.
                            </li>
                            <li class="mb-0">
                                <strong>Observaciones:</strong> Usa el campo de observaciones para notas internas que 
                                ayuden al equipo técnico en la preparación del pedido.
                            </li>
                        </ul>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>Cerrar
                </button>
            </div>
        </div>
    </div>
</div>
