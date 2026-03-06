<?php
/**
 * Modal: Registrar / Editar Pago de Presupuesto
 * Incluido desde formularioPresupuesto.php
 */
?>

<!-- ======================================================= -->
<!--  MODAL: REGISTRAR PAGO                                  -->
<!-- ======================================================= -->
<div class="modal fade" id="modalRegistrarPago" tabindex="-1" aria-labelledby="modalRegistrarPagoLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="modalRegistrarPagoLabel">
                    <i class="fas fa-money-bill-wave me-2"></i>
                    <span id="tituloModalPago">Registrar Pago</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <form id="frmRegistrarPago" novalidate>
                <div class="modal-body">

                    <!-- Campos ocultos -->
                    <input type="hidden" id="pago_id_pago_ppto"       name="id_pago_ppto">
                    <input type="hidden" id="pago_id_presupuesto"      name="id_presupuesto">

                    <div class="row g-3">

                        <!-- Tipo de pago -->
                        <div class="col-12 col-md-6">
                            <label for="pago_tipo_pago_ppto" class="form-label fw-bold">
                                Tipo de pago <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" id="pago_tipo_pago_ppto" name="tipo_pago_ppto" required>
                                <option value="">— Seleccionar —</option>
                                <option value="anticipo">Anticipo</option>
                                <option value="total">Pago total</option>
                                <option value="resto">Resto / Liquidación</option>
                            </select>
                            <div class="invalid-feedback">Seleccione un tipo de pago.</div>
                        </div>

                        <!-- Fecha de pago -->
                        <div class="col-12 col-md-6">
                            <label for="pago_fecha_pago_ppto" class="form-label fw-bold">
                                Fecha de pago <span class="text-danger">*</span>
                            </label>
                            <input type="date" class="form-control" id="pago_fecha_pago_ppto" name="fecha_pago_ppto" required>
                            <div class="invalid-feedback">Indique la fecha del pago.</div>
                        </div>

                        <!-- Importe -->
                        <div class="col-12 col-md-6">
                            <label for="pago_importe_pago_ppto" class="form-label fw-bold">
                                Importe <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="pago_importe_pago_ppto"
                                       name="importe_pago_ppto" min="0.01" step="0.01" placeholder="0.00" required>
                                <span class="input-group-text">€</span>
                            </div>
                            <div class="invalid-feedback">Indique un importe mayor que 0.</div>
                            <small class="text-muted" id="pago_info_porcentaje"></small>
                        </div>

                        <!-- Método de pago -->
                        <div class="col-12 col-md-6">
                            <label for="pago_id_metodo_pago" class="form-label fw-bold">
                                Método de pago <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" id="pago_id_metodo_pago" name="id_metodo_pago" required>
                                <option value="">— Cargando... —</option>
                            </select>
                            <div class="invalid-feedback">Seleccione un método de pago.</div>
                        </div>

                        <!-- Referencia (opcional) -->
                        <div class="col-12 col-md-6">
                            <label for="pago_referencia_pago_ppto" class="form-label">
                                Referencia / Nº transferencia
                            </label>
                            <input type="text" class="form-control" id="pago_referencia_pago_ppto"
                                   name="referencia_pago_ppto" maxlength="100" placeholder="Opcional">
                        </div>

                        <!-- Observaciones (opcional) -->
                        <div class="col-12 col-md-6">
                            <label for="pago_observaciones_pago_ppto" class="form-label">
                                Observaciones internas
                            </label>
                            <textarea class="form-control" id="pago_observaciones_pago_ppto"
                                      name="observaciones_pago_ppto" rows="2" maxlength="500" placeholder="Opcional"></textarea>
                        </div>

                    </div><!-- /.row -->

                    <hr class="my-3">

                    <!-- SECCIÓN: Generar factura (siempre visible en nuevos pagos) -->
                    <input type="checkbox" id="chkGenerarFactura" name="generar_factura" value="1" checked class="d-none">

                    <div id="seccionGenerarFactura">

                        <!-- Sub-sección: Empresa facturadora -->
                        <div id="seccionEmpresaFactura" class="border-start border-4 border-success rounded-end p-3 bg-success bg-opacity-10 mb-3">
                            <label for="pago_id_empresa_factura" class="form-label fw-bold text-dark">
                                <i class="fas fa-building me-2 text-success"></i>
                                Empresa emisora de la factura <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" id="pago_id_empresa_factura" name="id_empresa_factura">
                                <option value="">— Cargando empresas... —</option>
                            </select>
                            <div id="alertaEmpresaBloqueada" class="alert alert-warning mt-2 d-none py-2">
                                <i class="fas fa-lock me-2"></i>
                                <strong>Empresa bloqueada:</strong> esta empresa ya tiene una factura activa para este presupuesto.
                            </div>
                            <div id="alertaSinEmpresasDisponibles" class="alert alert-danger mt-2 d-none py-2">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                No hay empresas de facturación disponibles.
                            </div>
                        </div>

                        <!-- Sub-sección: Tipo de documento (solo tipo=anticipo) -->
                        <div id="seccionTipoDocumento" class="border-start border-4 border-info rounded-end p-3 bg-info bg-opacity-10 d-none">
                            <label class="form-label fw-bold mb-3 text-dark">
                                <i class="fas fa-file-alt me-2 text-info"></i>
                                Tipo de documento a generar
                            </label>
                            <div class="row g-2 mt-1 ps-1">
                                <div class="col-12 col-md-6">
                                    <label class="d-block cursor-pointer" for="rdoFacturaAnticipo">
                                        <div class="card border-2 p-4 h-100 tipo-doc-card" id="card-rdoFacturaAnticipo" style="border-color:#0d6efd !important; background:#f0f6ff;">
                                            <div class="d-flex align-items-start gap-3">
                                                <input class="form-check-input mt-1 flex-shrink-0" type="radio" name="tipo_documento_generar"
                                                       id="rdoFacturaAnticipo" value="factura_anticipo" checked>
                                                <div>
                                                    <div class="fw-bold"><i class="fas fa-file-invoice-dollar text-primary me-1"></i>Factura de anticipo</div>
                                                    <span class="text-dark small">Documento fiscal oficial de anticipo.</span>
                                                </div>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="d-block cursor-pointer" for="rdoFacturaProforma">
                                        <div class="card border-2 p-4 h-100 tipo-doc-card" id="card-rdoFacturaProforma" style="border-color:#dee2e6 !important;">
                                            <div class="d-flex align-items-start gap-3">
                                                <input class="form-check-input mt-1 flex-shrink-0" type="radio" name="tipo_documento_generar"
                                                       id="rdoFacturaProforma" value="factura_proforma">
                                                <div>
                                                    <div class="fw-bold"><i class="fas fa-file-alt text-secondary me-1"></i>Factura proforma</div>
                                                    <span class="text-dark small">Documento previo, no fiscal.</span>
                                                </div>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                            <div id="alertaProformaInfo" class="alert alert-info mt-3 mb-0 py-2 d-none">
                                <i class="fas fa-info-circle me-2"></i>
                                La factura proforma <strong>no es un documento fiscal</strong>. En caso de error se vuelve a emitir sin necesidad de abono.
                            </div>

                            <!-- IVA del anticipo -->
                            <div class="mt-3 border-top pt-3">
                                <label class="form-label fw-bold mb-2 text-dark" for="pago_porcentaje_iva">
                                    <i class="fas fa-percent me-2 text-info"></i>IVA del anticipo
                                </label>
                                <select class="form-select form-select-sm w-auto" id="pago_porcentaje_iva" name="porcentaje_iva">
                                    <option value="4">4%</option>
                                    <option value="10">10%</option>
                                    <option value="21" selected>21% (por defecto)</option>
                                </select>
                            </div>

                            <!-- tipo_cliente fijo: anticipo siempre sin descuento agencia/hotel -->
                            <input type="hidden" name="tipo_cliente" value="cliente_final">

                        </div><!-- /#seccionTipoDocumento -->

                        <!-- ── Idioma del documento (siempre visible al generar) ── -->
                        <div id="seccionIdiomaFactura" class="border-start border-4 border-secondary rounded-end p-3 mt-3" style="background-color: rgba(108,117,125,0.05);">
                            <label class="form-label fw-bold mb-2 text-dark">
                                <i class="fas fa-language me-2 text-secondary"></i>Idioma del documento
                            </label>
                            <div class="d-flex flex-wrap gap-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="idioma_factura"
                                           id="idioma_factura_es" value="es" checked>
                                    <label class="form-check-label text-dark" for="idioma_factura_es">Español</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="idioma_factura"
                                           id="idioma_factura_en" value="en">
                                    <label class="form-check-label text-dark" for="idioma_factura_en">English</label>
                                </div>
                            </div>
                        </div>

                    </div><!-- /#seccionGenerarFactura -->

                    <!-- Alerta: modo edición (no se re-genera factura) -->
                    <div id="alertaModoEdicion" class="alert alert-info d-none py-2">
                        <i class="fas fa-info-circle me-2"></i>
                        Estás editando un pago existente. La factura vinculada <strong>no se regenerará</strong>.
                    </div>

                </div><!-- /.modal-body -->

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-success" id="btnGuardarPago">
                        <i class="fas fa-save me-1"></i>Guardar Pago
                    </button>
                </div>

            </form><!-- /#frmRegistrarPago -->

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /#modalRegistrarPago -->
