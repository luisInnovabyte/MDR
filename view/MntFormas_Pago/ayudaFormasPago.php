<!-- Modal de Ayuda para Formas de Pago -->
<div class="modal fade" id="modalAyudaFormasPago" tabindex="-1" aria-labelledby="modalAyudaFormasPagoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <!-- Encabezado del Modal -->
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title d-flex align-items-center" id="modalAyudaFormasPagoLabel">
                    <i class="bi bi-question-circle-fill me-2 fs-4"></i>
                    Ayuda - Gestión de Formas de Pago
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <!-- Cuerpo del Modal -->
            <div class="modal-body">
                
                <!-- Sección: ¿Qué son las Formas de Pago? -->
                <div class="help-section mb-4">
                    <h6 class="text-primary d-flex align-items-center">
                        <i class="bi bi-wallet2 me-2"></i>
                        ¿Qué son las Formas de Pago en el Sistema?
                    </h6>
                    <p class="text-muted">
                        Las formas de pago definen las condiciones y plazos de pago para los clientes. Mientras que los 
                        <strong>métodos de pago</strong> indican el medio (transferencia, efectivo, tarjeta), las <strong>formas 
                        de pago</strong> establecen cómo se fracciona el pago (anticipo, pago final), los plazos en días, 
                        y descuentos aplicables. Cada forma de pago está asociada a un método de pago específico.
                    </p>
                    <div class="alert alert-info py-2">
                        <small>
                            <i class="bi bi-info-circle me-1"></i>
                            <strong>Ejemplo:</strong> "Fraccionado 40-60" es una <strong>forma de pago</strong> que usa el 
                            <strong>método</strong> "Transferencia bancaria" y define: 40% anticipo en 7 días, 60% final 7 días 
                            antes del evento.
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
                                    <i class="bi bi-upc-scan me-2"></i>
                                    Código de Forma de Pago *
                                </button>
                            </h2>
                            <div id="collapseCodigo" class="accordion-collapse collapse" aria-labelledby="headingCodigo" data-bs-parent="#accordionCampos">
                                <div class="accordion-body">
                                    <strong>Campo obligatorio.</strong> Código único identificativo de la forma de pago.
                                    <br><strong>Ejemplos:</strong> CONT_TRANS, FRAC40_60, FRAC50_50, PAGO_30D
                                    <br><strong>Validaciones:</strong> Máximo 20 caracteres, debe ser único en el sistema
                                    <br><strong>Formato recomendado:</strong> Mayúsculas con guiones bajos
                                </div>
                            </div>
                        </div>

                        <!-- Campo Nombre -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingNombre">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseNombre" aria-expanded="false" aria-controls="collapseNombre">
                                    <i class="bi bi-tag me-2"></i>
                                    Nombre de la Forma de Pago *
                                </button>
                            </h2>
                            <div id="collapseNombre" class="accordion-collapse collapse" aria-labelledby="headingNombre" data-bs-parent="#accordionCampos">
                                <div class="accordion-body">
                                    <strong>Campo obligatorio.</strong> Nombre descriptivo de la forma de pago.
                                    <br><strong>Ejemplos:</strong> "Contado transferencia", "Fraccionado 40-60", "Pago a 30 días", "Anticipo 50% evento día 0"
                                    <br><strong>Validaciones:</strong> Máximo 100 caracteres, debe ser único
                                </div>
                            </div>
                        </div>

                        <!-- Campo Método de Pago -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingMetodo">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseMetodo" aria-expanded="false" aria-controls="collapseMetodo">
                                    <i class="bi bi-credit-card me-2"></i>
                                    Método de Pago *
                                </button>
                            </h2>
                            <div id="collapseMetodo" class="accordion-collapse collapse" aria-labelledby="headingMetodo" data-bs-parent="#accordionCampos">
                                <div class="accordion-body">
                                    <strong>Campo obligatorio.</strong> Método mediante el cual se realizará el pago.
                                    <br><strong>Función:</strong> Selector desplegable que conecta con la tabla de métodos de pago
                                    <br><strong>Opciones:</strong> Transferencia, Efectivo, Tarjeta, Bizum, etc.
                                    <br><strong>Nota:</strong> Solo aparecen métodos activos en el sistema
                                </div>
                            </div>
                        </div>

                        <!-- Campo Porcentaje Anticipo -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingAnticipo">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseAnticipo" aria-expanded="false" aria-controls="collapseAnticipo">
                                    <i class="bi bi-percent me-2"></i>
                                    Porcentaje Anticipo *
                                </button>
                            </h2>
                            <div id="collapseAnticipo" class="accordion-collapse collapse" aria-labelledby="headingAnticipo" data-bs-parent="#accordionCampos">
                                <div class="accordion-body">
                                    <strong>Campo obligatorio.</strong> Porcentaje del total a pagar como anticipo.
                                    <br><strong>Rango:</strong> 0.00% a 100.00% (con 2 decimales)
                                    <br><strong>Regla:</strong> Anticipo + Pago Final = 100%
                                    <br><strong>Ejemplos:</strong>
                                    <ul class="mt-2 mb-0">
                                        <li><strong>100.00%</strong> = Pago único completo</li>
                                        <li><strong>40.00%</strong> = Señal del 40% (resto 60%)</li>
                                        <li><strong>50.00%</strong> = Mitad del importe (resto 50%)</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Campo Días Anticipo -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingDiasAnticipo">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDiasAnticipo" aria-expanded="false" aria-controls="collapseDiasAnticipo">
                                    <i class="bi bi-calendar-event me-2"></i>
                                    Días para Anticipo *
                                </button>
                            </h2>
                            <div id="collapseDiasAnticipo" class="accordion-collapse collapse" aria-labelledby="headingDiasAnticipo" data-bs-parent="#accordionCampos">
                                <div class="accordion-body">
                                    <strong>Campo obligatorio.</strong> Días desde la fecha del presupuesto para pagar el anticipo.
                                    <br><strong>Rango:</strong> 0 o positivo
                                    <br><strong>Ejemplos:</strong>
                                    <ul class="mt-2 mb-0">
                                        <li><strong>0</strong> = Anticipo inmediato (al aceptar presupuesto)</li>
                                        <li><strong>7</strong> = Anticipo a 7 días desde la fecha del presupuesto</li>
                                        <li><strong>15</strong> = Anticipo a 15 días desde la fecha del presupuesto</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Campo Porcentaje Final -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingFinal">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFinal" aria-expanded="false" aria-controls="collapseFinal">
                                    <i class="bi bi-percent me-2"></i>
                                    Porcentaje Pago Final *
                                </button>
                            </h2>
                            <div id="collapseFinal" class="accordion-collapse collapse" aria-labelledby="headingFinal" data-bs-parent="#accordionCampos">
                                <div class="accordion-body">
                                    <strong>Campo obligatorio.</strong> Porcentaje restante a pagar como pago final.
                                    <br><strong>Rango:</strong> 0.00% a 100.00% (con 2 decimales)
                                    <br><strong>Regla:</strong> Anticipo + Pago Final = 100%
                                    <br><strong>Ejemplos:</strong>
                                    <ul class="mt-2 mb-0">
                                        <li><strong>0.00%</strong> = Sin pago final (todo se pagó en anticipo)</li>
                                        <li><strong>60.00%</strong> = 60% restante después del anticipo</li>
                                        <li><strong>50.00%</strong> = Segunda mitad del importe</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Campo Días Final -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingDiasFinal">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDiasFinal" aria-expanded="false" aria-controls="collapseDiasFinal">
                                    <i class="bi bi-calendar-check me-2"></i>
                                    Días para Pago Final *
                                </button>
                            </h2>
                            <div id="collapseDiasFinal" class="accordion-collapse collapse" aria-labelledby="headingDiasFinal" data-bs-parent="#accordionCampos">
                                <div class="accordion-body">
                                    <strong>Campo obligatorio.</strong> Días respecto al evento para realizar el pago final.
                                    <br><strong>Valores:</strong> Negativos, cero o positivos
                                    <br><strong>Comportamiento:</strong>
                                    <ul class="mt-2">
                                        <li><i class="bi bi-arrow-down-short text-info"></i><strong>Negativo:</strong> Días ANTES del evento
                                            <ul>
                                                <li>Ejemplo: <code>-7</code> = 7 días antes del evento</li>
                                                <li>Ejemplo: <code>-15</code> = 15 días antes del evento</li>
                                            </ul>
                                        </li>
                                        <li><i class="bi bi-circle text-warning"></i><strong>Cero (0):</strong> El mismo día del evento</li>
                                        <li><i class="bi bi-arrow-up-short text-success"></i><strong>Positivo:</strong> Días DESPUÉS del evento
                                            <ul>
                                                <li>Ejemplo: <code>7</code> = 7 días después del evento</li>
                                                <li>Ejemplo: <code>30</code> = pago a 30 días del evento</li>
                                            </ul>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Campo Descuento -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingDescuento">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDescuento" aria-expanded="false" aria-controls="collapseDescuento">
                                    <i class="bi bi-tag-fill me-2"></i>
                                    Descuento (%)
                                </button>
                            </h2>
                            <div id="collapseDescuento" class="accordion-collapse collapse" aria-labelledby="headingDescuento" data-bs-parent="#accordionCampos">
                                <div class="accordion-body">
                                    <strong>Campo opcional.</strong> Descuento aplicable sobre el total del presupuesto.
                                    <br><strong>Rango:</strong> 0.00% a 100.00% (con 2 decimales)
                                    <br><strong>⚠️ IMPORTANTE:</strong> Solo se aplica cuando Porcentaje Anticipo = 100%
                                    <br><strong>Ejemplos:</strong>
                                    <ul class="mt-2 mb-0">
                                        <li><strong>5.00%</strong> = 5% de descuento por pago al contado</li>
                                        <li><strong>10.00%</strong> = 10% de descuento por pago inmediato</li>
                                        <li><strong>0.00%</strong> = Sin descuento (pagos fraccionados)</li>
                                    </ul>
                                    <div class="alert alert-warning mt-2 mb-0 py-2">
                                        <small><i class="bi bi-exclamation-triangle me-1"></i>El descuento NO se aplica en pagos fraccionados</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Campo Estado -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingEstado">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseEstado" aria-expanded="false" aria-controls="collapseEstado">
                                    <i class="bi bi-toggle-on me-2"></i>
                                    Estado de la Forma de Pago
                                </button>
                            </h2>
                            <div id="collapseEstado" class="accordion-collapse collapse" aria-labelledby="headingEstado" data-bs-parent="#accordionCampos">
                                <div class="accordion-body">
                                    <strong>Campo obligatorio.</strong> Determina si la forma de pago está disponible.
                                    <br><strong>Activa:</strong> La forma de pago aparece en presupuestos y puede ser seleccionada
                                    <br><strong>Inactiva:</strong> La forma de pago no está disponible para nuevas operaciones
                                    <br><strong>Nota:</strong> Puede desactivar formas de pago sin eliminarlas
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
                                    <strong>Campo opcional.</strong> Notas o información adicional sobre esta forma de pago.
                                    <br><strong>Uso:</strong> Condiciones especiales, restricciones, aclaraciones internas
                                    <br><strong>Ejemplo:</strong> "Solo para clientes VIP", "Requiere aprobación gerencia", "Aplicar en eventos de más de 5000€"
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sección: Ejemplos de Formas de Pago -->
                <div class="help-section mb-4">
                    <h6 class="text-primary d-flex align-items-center">
                        <i class="bi bi-lightbulb-fill me-2"></i>
                        Ejemplos de Formas de Pago
                    </h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Código</th>
                                    <th>Nombre</th>
                                    <th>Anticipo</th>
                                    <th>Días Ant.</th>
                                    <th>Final</th>
                                    <th>Días Final</th>
                                    <th>Desc.</th>
                                    <th>Tipo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="table-success">
                                    <td><code>CONT_TRANS</code></td>
                                    <td>Contado transferencia</td>
                                    <td>100%</td>
                                    <td>0</td>
                                    <td>0%</td>
                                    <td>0</td>
                                    <td>5%</td>
                                    <td><span class="badge bg-success">Pago único</span></td>
                                </tr>
                                <tr class="table-info">
                                    <td><code>FRAC40_60</code></td>
                                    <td>Fraccionado 40-60</td>
                                    <td>40%</td>
                                    <td>7</td>
                                    <td>60%</td>
                                    <td>-7</td>
                                    <td>0%</td>
                                    <td><span class="badge bg-info">Fraccionado</span></td>
                                </tr>
                                <tr class="table-info">
                                    <td><code>FRAC50_50</code></td>
                                    <td>Fraccionado 50-50</td>
                                    <td>50%</td>
                                    <td>0</td>
                                    <td>50%</td>
                                    <td>0</td>
                                    <td>0%</td>
                                    <td><span class="badge bg-info">Fraccionado</span></td>
                                </tr>
                                <tr class="table-info">
                                    <td><code>FRAC30_70</code></td>
                                    <td>Fraccionado 30-70</td>
                                    <td>30%</td>
                                    <td>0</td>
                                    <td>70%</td>
                                    <td>-15</td>
                                    <td>0%</td>
                                    <td><span class="badge bg-info">Fraccionado</span></td>
                                </tr>
                                <tr class="table-success">
                                    <td><code>PAGO_30D</code></td>
                                    <td>Pago a 30 días</td>
                                    <td>100%</td>
                                    <td>30</td>
                                    <td>0%</td>
                                    <td>0</td>
                                    <td>0%</td>
                                    <td><span class="badge bg-success">Pago único</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="alert alert-info mt-2 py-2">
                        <small>
                            <i class="bi bi-info-circle me-1"></i>
                            <strong>Nota:</strong> Los ejemplos con badge verde son pagos únicos (100% anticipo), 
                            los azules son pagos fraccionados (anticipo + pago final = 100%).
                        </small>
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
                            <li>Use códigos descriptivos que indiquen el tipo de pago (CONT, FRAC, etc.)</li>
                            <li>Nombres claros que expliquen las condiciones (ej: "Fraccionado 40-60")</li>
                            <li>Verifique siempre que anticipo + final = 100% (el sistema valida automáticamente)</li>
                            <li>Use días negativos para pagos antes del evento, positivos para después</li>
                            <li>El descuento solo aplica en pagos únicos (100% anticipo)</li>
                            <li>Asocie cada forma de pago al método correcto (transferencia, tarjeta, etc.)</li>
                            <li>No elimine formas de pago en uso, mejor desactívelas</li>
                            <li>Documente condiciones especiales en el campo observaciones</li>
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
                                Use el campo de búsqueda superior para encontrar formas de pago por cualquier dato.
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
                                Use los botones superiores para filtrar por estado: Todos, Activado, Desactivado.
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-secondary">Filtro de Tipo:</h6>
                            <p class="text-muted small">
                                Filtre por tipo: Pago único (100% anticipo) o Pago fraccionado.
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
                                    <td>Forma de Pago Activa</td>
                                    <td>La forma de pago está disponible para usar en presupuestos</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <i class="bi bi-x-circle text-danger fa-2x"></i>
                                    </td>
                                    <td>Forma de Pago Inactiva</td>
                                    <td>La forma de pago no está disponible para nuevas operaciones</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <span class="badge bg-success">Pago único</span>
                                    </td>
                                    <td>Tipo: Pago Único</td>
                                    <td>100% anticipo, 0% pago final, puede tener descuento</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <span class="badge bg-info">Pago fraccionado</span>
                                    </td>
                                    <td>Tipo: Pago Fraccionado</td>
                                    <td>Se divide en anticipo + pago final (suma = 100%), sin descuento</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <button class="btn btn-info btn-sm">
                                            <i class="fa-solid fa-edit"></i>
                                        </button>
                                    </td>
                                    <td>Botón Editar</td>
                                    <td>Permite modificar los datos de la forma de pago</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <button class="btn btn-danger btn-sm">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </td>
                                    <td>Botón Desactivar</td>
                                    <td>Desactiva la forma de pago (solo si está activa)</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <button class="btn btn-success btn-sm">
                                            <i class="bi bi-hand-thumbs-up-fill"></i>
                                        </button>
                                    </td>
                                    <td>Botón Activar</td>
                                    <td>Activa la forma de pago (solo si está inactiva)</td>
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
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
                    <i class="bi bi-check-lg mr-2"></i>Entendido
                </button>
            </div>
        </div>
    </div>
</div>
