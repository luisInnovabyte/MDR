<!-- Modal de Ayuda para Métodos de Pago -->
<div class="modal fade" id="modalAyudaMetodosPago" tabindex="-1" aria-labelledby="modalAyudaMetodosPagoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <!-- Encabezado del Modal -->
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title d-flex align-items-center" id="modalAyudaMetodosPagoLabel">
                    <i class="bi bi-question-circle-fill me-2 fs-4"></i>
                    Ayuda - Gestión de Métodos de Pago
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <!-- Cuerpo del Modal -->
            <div class="modal-body">
                
                <!-- Sección: ¿Qué son los Métodos de Pago? -->
                <div class="help-section mb-4">
                    <h6 class="text-primary d-flex align-items-center">
                        <i class="bi bi-credit-card me-2"></i>
                        ¿Qué son los Métodos de Pago en el Sistema?
                    </h6>
                    <p class="text-muted">
                        Los métodos de pago son las diferentes formas en que los clientes pueden realizar el pago de productos o servicios. 
                        El sistema permite gestionar distintos métodos de pago que se utilizarán en las operaciones comerciales, como 
                        transferencias bancarias, tarjetas, efectivo, cheques, entre otros.
                    </p>
                </div>

            
                <!-- Sección: Campos del Formulario -->
                <div class="help-section mb-4">
                    <h6 class="text-primary d-flex align-items-center">
                        <i class="bi bi-clipboard-data me-2"></i>
                        Campos del Formulario
                    </h6>
                    
                    <div class="accordion" id="accordionCampos">
                        <!-- Campo Código de Método -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingCodigo">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCodigo" aria-expanded="false" aria-controls="collapseCodigo">
                                    <i class="bi bi-upc-scan me-2"></i>
                                    Código del Método *
                                </button>
                            </h2>
                            <div id="collapseCodigo" class="accordion-collapse collapse" aria-labelledby="headingCodigo" data-bs-parent="#accordionCampos">
                                <div class="accordion-body">
                                    <strong>Campo obligatorio.</strong> Ingrese el código único del método de pago.
                                    <br><strong>Ejemplos:</strong> TRANS (Transferencia), TARJ (Tarjeta), EFEC (Efectivo), CHEQ (Cheque), BIZUM
                                    <br><strong>Validaciones:</strong> Mínimo 2 caracteres, máximo 20 caracteres, debe ser único, se convierte automáticamente a mayúsculas
                                </div>
                            </div>
                        </div>

                        <!-- Campo Nombre de Método -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingNombre">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseNombre" aria-expanded="false" aria-controls="collapseNombre">
                                    <i class="bi bi-tag me-2"></i>
                                    Nombre del Método *
                                </button>
                            </h2>
                            <div id="collapseNombre" class="accordion-collapse collapse" aria-labelledby="headingNombre" data-bs-parent="#accordionCampos">
                                <div class="accordion-body">
                                    <strong>Campo obligatorio.</strong> Ingrese el nombre descriptivo del método de pago.
                                    <br><strong>Ejemplos:</strong> Transferencia bancaria, Tarjeta de crédito/débito, Efectivo, Cheque, Bizum, PayPal
                                    <br><strong>Validaciones:</strong> Mínimo 3 caracteres, máximo 100 caracteres, debe ser único
                                </div>
                            </div>
                        </div>

                        <!-- Campo Estado -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingEstado">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseEstado" aria-expanded="false" aria-controls="collapseEstado">
                                    <i class="bi bi-toggle-on me-2"></i>
                                    Estado del Método
                                </button>
                            </h2>
                            <div id="collapseEstado" class="accordion-collapse collapse" aria-labelledby="headingEstado" data-bs-parent="#accordionCampos">
                                <div class="accordion-body">
                                    <strong>Campo obligatorio.</strong> Determina si el método de pago está disponible para usar.
                                    <br><strong>Activo:</strong> El método de pago aparecerá en formularios y opciones de pago
                                    <br><strong>Inactivo:</strong> El método de pago no estará disponible para nuevas operaciones
                                    <br><strong>Nota:</strong> Puede desactivar métodos que ya no se utilicen sin eliminarlos
                                </div>
                            </div>
                        </div>

                        <!-- Campo Observaciones -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingObservaciones">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseObservaciones" aria-expanded="false" aria-controls="collapseObservaciones">
                                    <i class="bi bi-chat-left-text me-2"></i>
                                    Observaciones del Método
                                </button>
                            </h2>
                            <div id="collapseObservaciones" class="accordion-collapse collapse" aria-labelledby="headingObservaciones" data-bs-parent="#accordionCampos">
                                <div class="accordion-body">
                                    <strong>Campo opcional.</strong> Notas adicionales sobre el método de pago.
                                    <br><strong>Uso:</strong> Para aclarar condiciones especiales, requisitos, comisiones aplicables
                                    <br><strong>Ejemplo:</strong> "Requiere IBAN del cliente", "Comisión del 2% aplicable", "Solo para importes menores a 500€"
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sección: Ejemplos de Métodos de Pago -->
                <div class="help-section mb-4">
                    <h6 class="text-primary d-flex align-items-center">
                        <i class="bi bi-lightbulb-fill me-2"></i>
                        Ejemplos de Métodos de Pago Comunes
                    </h6>
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-secondary">Métodos Tradicionales:</h6>
                            <ul class="list-unstyled text-muted">
                                <li>• TRANS - Transferencia bancaria</li>
                                <li>• TARJ - Tarjeta de crédito/débito</li>
                                <li>• EFEC - Efectivo</li>
                                <li>• CHEQ - Cheque</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-secondary">Métodos Digitales:</h6>
                            <ul class="list-unstyled text-muted">
                                <li>• BIZUM - Bizum</li>
                                <li>• PAYPAL - PayPal</li>
                                <li>• DOMICIL - Domiciliación bancaria</li>
                                <li>• CRIPTOMONEDA - Criptomonedas</li>
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
                            <li>Use códigos claros y cortos para identificar rápidamente los métodos</li>
                            <li>Mantenga nombres descriptivos que los usuarios puedan entender fácilmente</li>
                            <li>No elimine métodos que ya estén en uso, mejor desactívelos</li>
                            <li>Use el campo observaciones para documentar condiciones especiales</li>
                            <li>Revise periódicamente los métodos activos según las necesidades del negocio</li>
                            <li>Configure primero los métodos de pago antes de crear formas de pago</li>
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
                                Use el campo de búsqueda superior para encontrar métodos de pago por cualquier dato.
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
                                    <td>Método Activo</td>
                                    <td>El método de pago está disponible para usar</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <i class="bi bi-x-circle text-danger fa-2x"></i>
                                    </td>
                                    <td>Método Inactivo</td>
                                    <td>El método de pago no está disponible para nuevas operaciones</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <button class="btn btn-info btn-sm">
                                            <i class="fa-solid fa-edit"></i>
                                        </button>
                                    </td>
                                    <td>Botón Editar</td>
                                    <td>Permite modificar los datos del método de pago</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <button class="btn btn-danger btn-sm">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </td>
                                    <td>Botón Desactivar</td>
                                    <td>Desactiva el método de pago (solo si está activo)</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <button class="btn btn-success btn-sm">
                                            <i class="bi bi-hand-thumbs-up-fill"></i>
                                        </button>
                                    </td>
                                    <td>Botón Activar</td>
                                    <td>Activa el método de pago (solo si está inactivo)</td>
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
