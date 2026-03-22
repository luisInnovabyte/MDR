<!-- Modal de Ayuda - Facturas Agrupadas -->
<div class="modal fade" id="modalAyudaFacturasAgrupadas"
     tabindex="-1"
     aria-labelledby="modalAyudaFacturasAgrupadasLabel"
     aria-hidden="true">

    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">

            <!-- Encabezado -->
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title d-flex align-items-center" id="modalAyudaFacturasAgrupadasLabel">
                    <i class="bi bi-question-circle-fill me-2 fs-4"></i>
                    Ayuda — Facturas Agrupadas
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <!-- Cuerpo -->
            <div class="modal-body">

                <!-- ¿Qué es? -->
                <div class="help-section mb-4">
                    <h6 class="text-primary d-flex align-items-center">
                        <i class="bi bi-info-circle-fill me-2"></i>
                        ¿Qué es una Factura Agrupada?
                    </h6>
                    <p class="text-muted">
                        Una <strong>Factura Agrupada</strong> consolida <strong>varios presupuestos aprobados de un mismo cliente</strong>
                        en un único documento de facturación. En lugar de emitir una factura por cada presupuesto,
                        se genera una sola factura que los engloba todos, con los importes sumados y los anticipos
                        ya descontados de forma automática.
                    </p>
                    <div class="alert alert-info py-2 mb-0">
                        <small>
                            <i class="bi bi-lightbulb me-1"></i>
                            <strong>Para qué sirve:</strong> simplificar la facturación a clientes con varios eventos o servicios
                            contratados simultáneamente, emitiendo un único documento en lugar de múltiples facturas individuales.
                        </small>
                    </div>
                </div>

                <!-- Requisitos -->
                <div class="help-section mb-4">
                    <h6 class="text-primary d-flex align-items-center">
                        <i class="bi bi-check2-square me-2"></i>
                        Requisitos para crear una Factura Agrupada
                    </h6>
                    <p class="text-muted small mb-2">
                        Un presupuesto solo aparece disponible para agrupar si cumple <strong>todos</strong> estos criterios:
                    </p>
                    <ul class="text-muted small mb-2">
                        <li>Estado <strong>Aprobado</strong> en el sistema.</li>
                        <li>Todavía <strong>no incluido</strong> en ninguna otra Factura Agrupada activa.</li>
                        <li>Sin <strong>factura final</strong> activa emitida individualmente.</li>
                        <li>Todos los presupuestos seleccionados pertenecen al <strong>mismo cliente</strong>.</li>
                    </ul>
                    <div class="alert alert-warning py-2 mb-0">
                        <small>
                            <i class="bi bi-exclamation-triangle me-1"></i>
                            <strong>Importante:</strong> si un presupuesto ya fue incluido en una Factura Agrupada anterior
                            o ya tiene factura final, no aparecerá en la lista del paso 2 del asistente.
                        </small>
                    </div>
                </div>

                <!-- KPIs -->
                <div class="help-section mb-4">
                    <h6 class="text-primary d-flex align-items-center">
                        <i class="bi bi-card-checklist me-2"></i>
                        Indicadores del panel superior (KPIs)
                    </h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="border rounded p-3 h-100">
                                <p class="fw-semibold mb-1 text-secondary">
                                    <i class="fas fa-file-invoice me-1"></i> Total Facturas
                                </p>
                                <p class="text-muted small mb-0">
                                    Número de Facturas Agrupadas activas emitidas hasta la fecha,
                                    sin contar las rectificativas (abonos).
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded p-3 h-100">
                                <p class="fw-semibold mb-1 text-secondary">
                                    <i class="fas fa-euro-sign me-1"></i> Importe Total
                                </p>
                                <p class="text-muted small mb-0">
                                    Suma del campo <em>Total A Cobrar</em> de todas las Facturas Agrupadas activas.
                                    Equivale al importe pendiente de cobro consolidado.
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded p-3 h-100">
                                <p class="fw-semibold mb-1 text-secondary">
                                    <i class="fas fa-list-check me-1"></i> Presupuestos Agrupados
                                </p>
                                <p class="text-muted small mb-0">
                                    Total de presupuestos que están incluidos en alguna Factura Agrupada activa.
                                    Indica cuántos presupuestos han sido ya consolidados en este tipo de facturación.
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded p-3 h-100">
                                <p class="fw-semibold mb-1 text-secondary">
                                    <i class="fas fa-undo me-1"></i> Facturas Rectificativas
                                </p>
                                <p class="text-muted small mb-0">
                                    Número de facturas rectificativas (abonos) emitidas sobre Facturas Agrupadas.
                                    Una rectificativa anula una factura agrupada emitiendo importes negativos.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Columnas de la tabla -->
                <div class="help-section mb-4">
                    <h6 class="text-primary d-flex align-items-center">
                        <i class="bi bi-table me-2"></i>
                        Columnas de la tabla principal
                    </h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered table-hover mb-0">
                            <thead class="table-primary">
                                <tr>
                                    <th style="width:12%">Columna</th>
                                    <th>Descripción</th>
                                    <th style="width:30%">Cómo interpretarlo</th>
                                </tr>
                            </thead>
                            <tbody class="small">
                                <tr>
                                    <td class="fw-semibold">Número</td>
                                    <td>Número de la factura agrupada generado automáticamente (serie <em>F</em>) o, si es rectificativa, serie <em>A</em>.</td>
                                    <td>Identifica de forma única el documento. Las rectificativas se distinguen visualmente con un badge rojo.</td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">Fecha</td>
                                    <td>Fecha de emisión de la factura agrupada en formato <strong>DD/MM/AAAA</strong>.</td>
                                    <td>Fecha que aparece en el PDF y que determina el período contable.</td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">Cliente</td>
                                    <td>Nombre del cliente al que pertenecen todos los presupuestos incluidos.</td>
                                    <td>Todos los presupuestos de una misma FA deben ser del mismo cliente.</td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">Empresa</td>
                                    <td>Empresa emisora de la factura (empresa MDR desde la que se factura).</td>
                                    <td>Determina el membrete, número de serie y datos fiscales del PDF.</td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">Pres.</td>
                                    <td>Número de presupuestos incluidos en esta factura agrupada.</td>
                                    <td>Al pulsar el icono de ojo se abre el detalle con el desglose de cada presupuesto.</td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">Total Bruto</td>
                                    <td>Suma de los totales con IVA de todos los presupuestos incluidos, antes de descontar anticipos.</td>
                                    <td>Importe bruto total de la agrupación. Negativo si es rectificativa.</td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">A Cobrar</td>
                                    <td>Importe efectivo a cobrar al cliente: Total Bruto menos los anticipos ya facturados y cobrados.</td>
                                    <td>Es el importe que debe aparecer en la remesa o en el recibo bancario.</td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">Tipo</td>
                                    <td>
                                        Indica si la fila es una <span class="badge" style="background:#d35400;color:#fff;">Factura</span>
                                        normal o una <span class="badge bg-danger">Rectificativa</span>.
                                    </td>
                                    <td>Las rectificativas tienen importes negativos y anulan la factura original a la que hacen referencia.</td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">PDF</td>
                                    <td>Botón para descargar o generar el PDF de la factura agrupada.</td>
                                    <td>Si el PDF aún no está generado, al pulsarlo se crea en ese momento.</td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">Opciones</td>
                                    <td>Acciones disponibles: ver detalle de presupuestos y generar rectificativa.</td>
                                    <td>La opción de rectificativa solo aparece en facturas normales (no en rectificativas).</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Asistente -->
                <div class="help-section mb-4">
                    <h6 class="text-primary d-flex align-items-center">
                        <i class="bi bi-magic me-2"></i>
                        Asistente de creación — los 3 pasos
                    </h6>
                    <div class="accordion" id="accordionWizard">

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#wizPaso1">
                                    <i class="fas fa-user me-2 text-primary"></i> Paso 1 — Seleccionar cliente
                                </button>
                            </h2>
                            <div id="wizPaso1" class="accordion-collapse collapse" data-bs-parent="#accordionWizard">
                                <div class="accordion-body text-muted small">
                                    Elige el cliente para el que quieres emitir la Factura Agrupada.
                                    Solo aparecen clientes que tienen <strong>al menos un presupuesto aprobado disponible</strong>
                                    (no incluido en otra FA ni con factura final). El desplegable muestra cuántos
                                    presupuestos elegibles tiene cada cliente.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#wizPaso2">
                                    <i class="fas fa-list-check me-2 text-primary"></i> Paso 2 — Seleccionar presupuestos
                                </button>
                            </h2>
                            <div id="wizPaso2" class="accordion-collapse collapse" data-bs-parent="#accordionWizard">
                                <div class="accordion-body text-muted small">
                                    Se muestran todos los presupuestos aprobados disponibles del cliente elegido.
                                    Marca los que quieres incluir (mínimo 2). Puedes usar los botones
                                    <strong>Todos / Ninguno</strong> para selección rápida. Para cada presupuesto
                                    se muestra su número, nombre del evento y total estimado a cobrar (bruto menos anticipos).
                                    <br><br>
                                    Si algún presupuesto no puede incluirse (ya tiene FA o factura final), no aparece en esta lista.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#wizPaso3">
                                    <i class="fas fa-check-circle me-2 text-primary"></i> Paso 3 — Confirmar y crear
                                </button>
                            </h2>
                            <div id="wizPaso3" class="accordion-collapse collapse" data-bs-parent="#accordionWizard">
                                <div class="accordion-body text-muted small">
                                    Revisa el resumen con los presupuestos seleccionados y los totales calculados
                                    (base imponible, IVA, anticipos descontados y total a cobrar).
                                    Puedes ajustar la <strong>fecha de la factura</strong> y añadir <strong>observaciones</strong>
                                    opcionales que aparecerán en el PDF. Al pulsar
                                    <em>Crear Factura</em> se genera el documento y se registra el pago pendiente
                                    en el sistema.
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Rectificativas -->
                <div class="help-section mb-4">
                    <h6 class="text-primary d-flex align-items-center">
                        <i class="bi bi-arrow-counterclockwise me-2"></i>
                        Facturas Rectificativas (Abonos)
                    </h6>
                    <p class="text-muted small mb-2">
                        Una <strong>factura rectificativa</strong> anula completamente una Factura Agrupada previamente emitida.
                        El sistema genera automáticamente una nueva factura con los mismos datos pero con
                        <strong>importes negativos</strong>, siguiendo la normativa de facturación rectificativa.
                    </p>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered mb-0">
                            <thead class="table-light">
                                <tr><th>Qué ocurre al generar una rectificativa</th></tr>
                            </thead>
                            <tbody class="small text-muted">
                                <tr><td>Se crea una nueva FA con serie <strong>A</strong> e importes negativos.</td></tr>
                                <tr><td>La FA original queda <strong>desactivada</strong> (deja de aparecer en el listado activo).</td></tr>
                                <tr><td>Los pagos pendientes asociados a la FA original se <strong>anulan</strong> automáticamente.</td></tr>
                                <tr><td>Los presupuestos incluidos vuelven a quedar <strong>disponibles</strong> para una nueva FA o factura individual.</td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="alert alert-warning mt-2 py-2 mb-0">
                        <small>
                            <i class="bi bi-exclamation-triangle me-1"></i>
                            <strong>Atención:</strong> esta acción no se puede deshacer desde la interfaz.
                            Antes de rectificar, asegúrate de que es realmente necesario.
                        </small>
                    </div>
                </div>

                <!-- FAQ -->
                <div class="help-section mb-2">
                    <h6 class="text-primary d-flex align-items-center">
                        <i class="bi bi-chat-left-text-fill me-2"></i>
                        Preguntas frecuentes
                    </h6>
                    <div class="accordion" id="accordionFAQ">

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed py-2" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#faqFA1">
                                    ¿Por qué no aparece un presupuesto en el paso 2?
                                </button>
                            </h2>
                            <div id="faqFA1" class="accordion-collapse collapse" data-bs-parent="#accordionFAQ">
                                <div class="accordion-body text-muted small">
                                    El presupuesto no aparece porque no cumple alguno de los requisitos:
                                    su estado no es <em>Aprobado</em>, ya está incluido en otra Factura Agrupada activa,
                                    o ya tiene una factura final emitida de forma individual.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed py-2" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#faqFA2">
                                    ¿Puedo incluir presupuestos de dos clientes distintos?
                                </button>
                            </h2>
                            <div id="faqFA2" class="accordion-collapse collapse" data-bs-parent="#accordionFAQ">
                                <div class="accordion-body text-muted small">
                                    No. Una Factura Agrupada agrupa presupuestos de <strong>un único cliente</strong>.
                                    El asistente te obliga a seleccionar primero el cliente y solo muestra
                                    los presupuestos que le pertenecen.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed py-2" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#faqFA3">
                                    ¿Los anticipos se descuentan automáticamente?
                                </button>
                            </h2>
                            <div id="faqFA3" class="accordion-collapse collapse" data-bs-parent="#accordionFAQ">
                                <div class="accordion-body text-muted small">
                                    Sí. El campo <strong>A Cobrar</strong> se calcula en el paso 3 restando
                                    los anticipos reales ya facturados a cada presupuesto. El PDF refleja
                                    este desglose de forma transparente para el cliente.
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div><!-- /modal-body -->

        </div>
    </div>
</div>
