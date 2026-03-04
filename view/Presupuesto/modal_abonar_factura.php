<?php
/**
 * Modal: Abonar Factura (Nota de abono)
 * Incluido desde formularioPresupuesto.php
 */
?>

<!-- ======================================================= -->
<!--  MODAL: ABONAR FACTURA                                  -->
<!-- ======================================================= -->
<div class="modal fade" id="modalAbonarFactura" tabindex="-1" aria-labelledby="modalAbonarFacturaLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-md">
        <div class="modal-content">

            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="modalAbonarFacturaLabel">
                    <i class="fas fa-rotate-left me-2"></i>Generar Factura de Abono
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <form id="frmAbonarFactura" novalidate>
                <div class="modal-body">

                    <!-- Campos ocultos -->
                    <input type="hidden" id="abono_id_documento_origen" name="id_documento_origen">
                    <input type="hidden" id="abono_id_presupuesto"      name="id_presupuesto">
                    <input type="hidden" id="abono_id_empresa"          name="id_empresa">

                    <!-- Información del documento que se va a abonar -->
                    <div class="card mb-3 border-warning">
                        <div class="card-header bg-warning bg-opacity-25 fw-bold py-2">
                            <i class="fas fa-file-invoice me-2"></i>Documento a abonar
                        </div>
                        <div class="card-body py-2">
                            <div class="row">
                                <div class="col-6">
                                    <small class="text-muted d-block">Nº Documento</small>
                                    <span class="fw-bold" id="abono_numero_documento">—</span>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Tipo</small>
                                    <span id="abono_badge_tipo">—</span>
                                </div>
                                <div class="col-6 mt-2">
                                    <small class="text-muted d-block">Empresa emisora</small>
                                    <span id="abono_nombre_empresa">—</span>
                                </div>
                                <div class="col-6 mt-2">
                                    <small class="text-muted d-block">Total facturado</small>
                                    <span class="fw-bold text-danger fs-5" id="abono_total_documento">—</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Motivo del abono -->
                    <div class="mb-3">
                        <label for="abono_motivo_abono" class="form-label fw-bold">
                            Motivo del abono <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control" id="abono_motivo_abono" name="motivo_abono"
                                  rows="3" minlength="10" maxlength="500"
                                  placeholder="Describa el motivo por el que se genera la factura de abono (mín. 10 caracteres)..."
                                  required></textarea>
                        <div class="invalid-feedback">El motivo es obligatorio y debe tener al menos 10 caracteres.</div>
                    </div>

                    <!-- Alerta con las consecuencias -->
                    <div class="alert alert-danger py-2 mb-3">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Atención:</strong> esta acción generará una factura de abono y
                        <strong>anulará el pago vinculado</strong> al documento original.
                        Esta operación <strong>no se puede deshacer</strong>.
                    </div>

                    <!-- Confirmación explícita -->
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="chkConfirmarAbono" required>
                        <label class="form-check-label fw-bold text-danger" for="chkConfirmarAbono">
                            Confirmo que quiero generar la factura de abono y anular el pago vinculado.
                        </label>
                        <div class="invalid-feedback">Debes confirmar antes de continuar.</div>
                    </div>

                </div><!-- /.modal-body -->

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-warning fw-bold" id="btnConfirmarAbono">
                        <i class="fas fa-rotate-left me-1"></i>Generar Abono
                    </button>
                </div>

            </form><!-- /#frmAbonarFactura -->

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /#modalAbonarFactura -->
