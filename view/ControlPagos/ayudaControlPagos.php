<!-- Modal de Ayuda para Control de Pagos -->
<div class="modal fade" id="modalAyudaControlPagos" tabindex="-1" aria-labelledby="modalAyudaControlPagosLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">

            <!-- Encabezado -->
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title d-flex align-items-center" id="modalAyudaControlPagosLabel">
                    <i class="fas fa-question-circle me-2 fs-5"></i>
                    Ayuda — Control de Pagos
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <!-- Cuerpo -->
            <div class="modal-body">

                <!-- ¿Qué es esta pantalla? -->
                <div class="mb-4">
                    <h6 class="text-info d-flex align-items-center">
                        <i class="fas fa-eye me-2"></i>¿Qué es el Control de Pagos?
                    </h6>
                    <p class="text-muted small">
                        Resumen financiero de todos los <strong>presupuestos aprobados</strong>. Permite saber, de un vistazo,
                        cuánto se ha facturado, cuánto se ha cobrado realmente y qué queda pendiente de facturar en cada uno.
                    </p>
                    <div class="alert alert-warning py-2 mb-0">
                        <small>
                            <i class="fas fa-lock me-1"></i>
                            Esta pantalla es de <strong>consulta</strong>. Para registrar o modificar pagos accede al
                            formulario del presupuesto correspondiente (botón <em>"Ir al presupuesto"</em> en el desglose).
                        </small>
                    </div>
                </div>

                <hr>

                <!-- KPI Cards -->
                <div class="mb-4">
                    <h6 class="text-info d-flex align-items-center">
                        <i class="fas fa-tachometer-alt me-2"></i>Indicadores (KPI)
                    </h6>
                    <p class="text-muted small mb-2">Los cinco bloques de la parte superior muestran totales globales de todos los presupuestos visibles:</p>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:35%">Indicador</th>
                                    <th>Qué mide</th>
                                </tr>
                            </thead>
                            <tbody class="small text-muted">
                                <tr>
                                    <td><span class="badge bg-secondary">Total Aprobado <sup>*</sup></span></td>
                                    <td>Suma de los importes de todos los presupuestos aprobados, <strong>con IVA incluido</strong>. Debajo indica cuántos presupuestos están en este estado.</td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-success">Total Facturado <sup>*</sup></span></td>
                                    <td>Suma de todos los pagos registrados que <strong>no están anulados</strong> (facturas emitidas, anticipos, abonos con signo positivo), <strong>con IVA incluido</strong>. Es lo que el cliente tiene comprometido documentalmente.</td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-info text-dark">Total Pagado <sup>*</sup></span></td>
                                    <td>Importe de pagos marcados como <strong>conciliados</strong>, es decir, cobro efectivamente confirmado en el banco, <strong>con IVA incluido</strong>. Puede ser menor que el Facturado si hay facturas pendientes de cobrar.</td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-danger">Pdte. de Facturar <sup>*</sup></span></td>
                                    <td><strong>Aprobado − Facturado</strong>, <strong>con IVA incluido</strong>. Lo que aún falta por emitir al cliente. Debajo indica cuántos presupuestos tienen pago parcial y cuántos no tienen ningún pago.</td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-primary">% Global Cobrado</span></td>
                                    <td>Ratio <em>Pagado / Facturado × 100</em>. Indica el porcentaje del total facturado que ya está cobrado y conciliado.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="alert alert-info py-2 mb-0">
                        <small>
                            <i class="fas fa-info-circle me-1"></i>
                            Los KPI se recalculan automáticamente al aplicar o limpiar filtros, reflejando
                            siempre los datos de las filas visibles en ese momento.<br>
                            <sup>*</sup> Todos los importes incluyen IVA (21%).
                        </small>
                    </div>
                </div>

                <hr>

                <!-- Filtros -->
                <div class="mb-4">
                    <h6 class="text-info d-flex align-items-center">
                        <i class="fas fa-filter me-2"></i>Filtros disponibles
                    </h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:35%">Filtro</th>
                                    <th>Efecto</th>
                                </tr>
                            </thead>
                            <tbody class="small text-muted">
                                <tr>
                                    <td><i class="fas fa-toggle-on me-1 text-primary"></i>Pdte. facturar</td>
                                    <td>Muestra únicamente los presupuestos con importe pendiente de facturar &gt; 0 (Aprobado &gt; Facturado). Útil para detectar presupuestos parcialmente facturados.</td>
                                </tr>
                                <tr>
                                    <td><i class="fas fa-toggle-on me-1 text-primary"></i>Sin facturas</td>
                                    <td>Muestra únicamente los presupuestos que <strong>no tienen ninguna factura emitida</strong> (Facturado = 0,00 €). Útil para detectar presupuestos aprobados a los que aún no se ha realizado ninguna facturación.</td>
                                </tr>
                                <tr>
                                    <td><i class="fas fa-calendar me-1"></i>Evento desde / hasta</td>
                                    <td>Filtra por la <strong>fecha del evento</strong> del presupuesto, no por la fecha de creación. Útil para cerrar periodos o revisar una temporada. Los campos de fecha muestran el aviso <em>"Pulsa «Filtrar» para aplicar"</em>.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <p class="text-muted small mt-2 mb-0">
                        Los switches (<em>Pdte. facturar</em> y <em>Sin facturas</em>) se aplican inmediatamente al activarlos.
                        El rango de fechas requiere pulsar el botón <strong>Filtrar</strong> para que surtan efecto.
                        Usa <strong>Limpiar</strong> para restablecer todos los filtros y ver todos los presupuestos.
                    </p>
                </div>

                <hr>

                <!-- Columnas del DataTable -->
                <div class="mb-4">
                    <h6 class="text-info d-flex align-items-center">
                        <i class="fas fa-table me-2"></i>Columnas de la tabla
                    </h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:30%">Columna</th>
                                    <th>Significado</th>
                                </tr>
                            </thead>
                            <tbody class="small text-muted">
                                <tr>
                                    <td><strong>Nº Presupuesto</strong></td>
                                    <td>Código único del presupuesto (ej. <em>PPTO-2025-0042</em>). Sirve de referencia para cualquier comunicación con el cliente.</td>
                                </tr>
                                <tr>
                                    <td><strong>Cliente</strong></td>
                                    <td>Nombre completo del cliente asociado al presupuesto.</td>
                                </tr>
                                <tr>
                                    <td><strong>Evento</strong></td>
                                    <td>Nombre del evento o servicio contratado y su fecha. Ordena la tabla por este campo por defecto (ascendente).</td>
                                </tr>
                                <tr>
                                    <td><strong>Aprobado <sup>*</sup></strong></td>
                                    <td>Importe total del presupuesto en estado <em>Aprobado</em>, <strong>con IVA incluido</strong>. Calculado desde las líneas del presupuesto. Es la cifra de referencia máxima a cobrar.</td>
                                </tr>
                                <tr>
                                    <td><strong>Facturado <sup>*</sup></strong></td>
                                    <td>Suma de todos los pagos emitidos al cliente (facturas, anticipos…) que <strong>no están anulados</strong>, <strong>con IVA incluido</strong>. Puede ser menor que Aprobado si hay partes sin facturar aún. Un valor <strong>0,00 €</strong> indica que aún no se ha emitido ninguna factura.</td>
                                </tr>
                                <tr>
                                    <td><strong>Pagado <sup>*</sup></strong></td>
                                    <td>Importe de pagos <strong>conciliados</strong>, <strong>con IVA incluido</strong>; cobro confirmado y verificado en cuenta. Puede ser menor que Facturado si hay facturas emitidas pero pendientes de cobrar.</td>
                                </tr>
                                <tr>
                                    <td><strong>Pdte. Facturar <sup>*</sup></strong></td>
                                    <td><em>Aprobado − Facturado</em>, <strong>con IVA incluido</strong>. Lo que queda por emitir al cliente. Un valor <strong>0,00 €</strong> indica que toda la facturación está completa. Valores negativos indican que se ha facturado más de lo aprobado (ej. ajustes o revisiones).</td>
                                </tr>
                                <tr>
                                    <td><strong>Documentos</strong></td>
                                    <td>Tipos de documentos de pago generados para ese presupuesto (Factura, Anticipo, Abono, etc.), mostrados como etiquetas.</td>
                                </tr>
                                <tr>
                                    <td><strong>Última Factura</strong></td>
                                    <td>Fecha del documento de pago más reciente emitido. Ayuda a detectar presupuestos con facturación antigua o pendiente de actualizar.</td>
                                </tr>
                                <tr>
                                    <td><strong>Opciones</strong></td>
                                    <td>Botón <i class="fas fa-eye text-primary"></i> que abre el <strong>desglose completo de pagos</strong> de ese presupuesto (ver sección siguiente).</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <hr>

                <!-- Modal desglose de pagos -->
                <div class="mb-4">
                    <h6 class="text-info d-flex align-items-center">
                        <i class="fas fa-list-ul me-2"></i>Desglose de pagos (ventana emergente)
                    </h6>
                    <p class="text-muted small">
                        Al pulsar el icono <i class="fas fa-eye text-primary"></i> de cualquier fila se abre una ventana con el detalle
                        de todos los pagos registrados para ese presupuesto. Incluye:
                    </p>
                    <ul class="text-muted small">
                        <li><strong>Resumen financiero:</strong> total del presupuesto, total facturado y pendiente de facturar.</li>
                        <li><strong>Tabla de pagos:</strong> una fila por cada pago, con los siguientes datos:</li>
                    </ul>
                    <div class="table-responsive ms-3">
                        <table class="table table-sm table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Campo</th>
                                    <th>Descripción</th>
                                </tr>
                            </thead>
                            <tbody class="small text-muted">
                                <tr>
                                    <td><strong>Fecha</strong></td>
                                    <td>Fecha de emisión del pago o documento.</td>
                                </tr>
                                <tr>
                                    <td><strong>Tipo</strong></td>
                                    <td>Tipología del documento: <em>Factura</em>, <em>Anticipo</em>, <em>Abono</em>, etc.</td>
                                </tr>
                                <tr>
                                    <td><strong>Importe</strong></td>
                                    <td>Valor económico del pago. Los abonos aparecen con importe negativo.</td>
                                </tr>
                                <tr>
                                    <td><strong>Método</strong></td>
                                    <td>Forma de pago utilizada: transferencia, efectivo, TPV, etc.</td>
                                </tr>
                                <tr>
                                    <td><strong>Documento</strong></td>
                                    <td>Número o referencia del documento (nº de factura, nº de anticipo…).</td>
                                </tr>
                                <tr>
                                    <td><strong>Estado</strong></td>
                                    <td>Estado del cobro: <em>Pendiente</em> (emitido pero no cobrado), <em>Conciliado</em> (cobro confirmado), <em>Anulado</em> (cancelado, no computa).</td>
                                </tr>
                                <tr>
                                    <td><strong>Referencia</strong></td>
                                    <td>Código o nota interna de referencia adicional (nº transferencia, comentario, etc.).</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <p class="text-muted small mt-2 mb-0">
                        Desde esta ventana también puedes acceder directamente al formulario del presupuesto
                        usando el botón <strong>"Ir al presupuesto"</strong>, que abre la pestaña de pagos.
                    </p>
                </div>

                <hr>

                <!-- Flujo de trabajo -->
                <div class="mb-2">
                    <h6 class="text-info d-flex align-items-center">
                        <i class="fas fa-book me-2"></i>Flujo de trabajo recomendado
                    </h6>
                    <ol class="text-muted small mb-0">
                        <li>Activa <strong>"Pdte. facturar"</strong> para ver qué presupuestos tienen importe pendiente de facturar (Aprobado &gt; Facturado).</li>
                        <li>Activa <strong>"Sin facturas"</strong> para detectar presupuestos aprobados a los que todavía no se les ha emitido ninguna factura (Facturado = 0,00 €).</li>
                        <li>Usa el <strong>rango de fechas de evento</strong> para revisar un periodo concreto (cierre mensual, trimestral…).</li>
                        <li>Pulsa el icono <i class="fas fa-eye text-primary"></i> en cualquier fila para ver el detalle completo de sus pagos.</li>
                        <li>Desde el desglose, usa <strong>"Ir al presupuesto"</strong> si necesitas registrar un nuevo pago o modificar uno existente.</li>
                    </ol>
                </div>

            </div><!-- /modal-body -->

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cerrar</button>
            </div>

        </div>
    </div>
</div>
<!-- FIN MODAL AYUDA CONTROL PAGOS -->
