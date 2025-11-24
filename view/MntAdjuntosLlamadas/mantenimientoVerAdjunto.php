<!-- Modal para visualizar documentos -->
<div class="modal fade" id="modalVerAdjunto" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" style="max-width: 90%; max-height: 90vh;">
        <div class="modal-content h-100">
            <div class="modal-header bg-primary text-white py-2">
                <h5 class="modal-title text-truncate pe-4 m-0">Documento: <span id="nombre-archivo-titulo"></span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0 d-flex flex-column">
                <div id="contenedor-adjunto" class="flex-grow-1 d-flex justify-content-center align-items-center bg-light">
                    <!-- Contenido se insertará aquí -->
                </div>
            </div>
            <div class="modal-footer py-2">
                <a id="descargar-adjunto" href="#" class="btn btn-success btn-sm" download>
                    <i class="fas fa-download me-1"></i> Descargar
                </a>
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Cerrar
                </button>
            </div>
        </div>
    </div>
</div>