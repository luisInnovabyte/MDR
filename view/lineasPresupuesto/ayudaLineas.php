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
                            <i class="bi bi-calculator-fill me-2 text-success"></i>Columna TOTAL del DataTable
                        </h6>
                    </div>
                    <div class="card-body">
                        <p>La columna <strong>TOTAL</strong> del listado de líneas muestra el cálculo final de cada línea según esta fórmula:</p>
                        
                        <div class="bg-light p-3 rounded mb-3">
                            <code>
                                <strong>TOTAL = (Cantidad × Precio Unitario) - Descuento + IVA</strong>
                            </code>
                        </div>

                        <h6 class="mt-3 mb-2">Consideraciones sobre los Coeficientes:</h6>
                        <ul>
                            <li class="mb-2">
                                <strong>Si el coeficiente está activado:</strong> Se aplicará el coeficiente correspondiente 
                                según las jornadas de alquiler, tanto al cliente final como al hotel.
                            </li>
                            <li class="mb-2">
                                <strong>Si el coeficiente NO está activado:</strong> Se mostrarán los <strong>días efectivos</strong> 
                                que se han contratado el equipamiento.
                            </li>
                            <li class="mb-3">
                                <strong>Modificación del comportamiento:</strong> La aplicación del coeficiente reductor viene 
                                desde la ficha del artículo, pero se puede modificar directamente desde la línea de presupuesto.
                            </li>
                        </ul>

                        <h6 class="mt-3 mb-2">Gestión de Descuentos:</h6>
                        <div class="alert alert-warning mb-3">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>Artículos que NO admiten descuento:</strong><br>
                            Aunque en la ficha del artículo se muestre como "no admite descuentos", el sistema 
                            <strong>informará de tal circunstancia</strong> debajo del campo de descuento, pero 
                            <strong>permitirá introducir un % de descuento para esta línea</strong>. Sin embargo, 
                            <strong>NO se aplicarán los % de descuento del hotel</strong>.
                        </div>

                        <div class="alert alert-danger mb-3">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Artículos NO facturables:</strong><br>
                            Si el artículo se señala como "no facturable" en su ficha, el sistema:
                            <ul class="mb-0 mt-2">
                                <li>Advertirá de tal circunstancia en la línea de presupuesto</li>
                                <li>Aplicará automáticamente el <strong>100% de descuento</strong></li>
                                <li>Permitirá <strong>modificar a la baja el % de descuento</strong> para esa línea</li>
                                <li>En ese caso, pasará a cobrarse al cliente final y aplicando lo correspondiente al hotel</li>
                            </ul>
                        </div>

                        <h6 class="mt-3 mb-2">Detalle de Cálculos:</h6>
                        <div class="alert alert-info mb-0">
                            <i class="bi bi-plus-circle me-2"></i>
                            Al hacer clic en el botón <strong>+</strong> (expansión de columna) de cada línea, 
                            se mostrará el <strong>detalle completo de todos los cálculos finales</strong> aplicados 
                            a esa línea específica.
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
                                <strong>Orden del DataTable:</strong> Las líneas se muestran ordenadas primero por 
                                <strong>fecha de inicio del alquiler</strong> y dentro de la misma fecha se ordenan 
                                por <strong>ubicación</strong>.
                            </li>
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
