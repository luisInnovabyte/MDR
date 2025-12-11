<!-- Modal de Ayuda de Grupos de Artículos -->
<div class="modal fade" id="modalAyudaGrupos" tabindex="-1" aria-labelledby="modalAyudaGruposLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalAyudaGruposLabel">
                    <i class="fas fa-question-circle me-2"></i>Ayuda - Mantenimiento de Grupos de Artículos
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12 mb-4">
                        <h6 class="text-primary"><i class="fas fa-info-circle me-2"></i>¿Qué son los Grupos de Artículos?</h6>
                        <p>
                            Los <strong>Grupos de Artículos</strong> son categorías de primer nivel que permiten organizar 
                            tus artículos en grandes grupos o categorías principales. Son el nivel más alto de 
                            clasificación en la jerarquía de productos.
                        </p>
                        <div class="alert alert-info">
                            <strong>Jerarquía:</strong> GRUPO → FAMILIA → ARTÍCULO → ELEMENTO
                        </div>
                    </div>
                    
                    <div class="col-12 mb-4">
                        <h6 class="text-primary"><i class="fas fa-list me-2"></i>Funcionalidades del Módulo</h6>
                        <ul class="list-unstyled ms-3">
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                <strong>Crear grupos:</strong> Define nuevas categorías principales de artículos
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                <strong>Editar grupos:</strong> Modifica la información de grupos existentes
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                <strong>Activar/Desactivar:</strong> Controla la disponibilidad de los grupos sin eliminarlos
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                <strong>Filtrar y buscar:</strong> Encuentra grupos rápidamente usando los filtros
                            </li>
                        </ul>
                    </div>
                    
                    <div class="col-12 mb-4">
                        <h6 class="text-primary"><i class="fas fa-table me-2"></i>Campos de la Tabla</h6>
                        <table class="table table-sm table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Campo</th>
                                    <th>Descripción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>Código grupo</strong></td>
                                    <td>Identificador corto único del grupo (ej: AUD, ILU, VID)</td>
                                </tr>
                                <tr>
                                    <td><strong>Nombre grupo</strong></td>
                                    <td>Nombre descriptivo del grupo (ej: Audio, Iluminación, Vídeo)</td>
                                </tr>
                                <tr>
                                    <td><strong>Descripción</strong></td>
                                    <td>Descripción detallada del grupo de artículos</td>
                                </tr>
                                <tr>
                                    <td><strong>Estado</strong></td>
                                    <td>Indica si el grupo está activo o inactivo</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="col-12 mb-4">
                        <h6 class="text-primary"><i class="fas fa-lightbulb me-2"></i>Ejemplos de Uso</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card border-info mb-2">
                                    <div class="card-body p-2">
                                        <h6 class="card-title text-info mb-1">Grupo: AUDIO</h6>
                                        <ul class="small mb-0">
                                            <li>Micrófonos</li>
                                            <li>Altavoces</li>
                                            <li>Consolas de mezcla</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card border-warning mb-2">
                                    <div class="card-body p-2">
                                        <h6 class="card-title text-warning mb-1">Grupo: ILUMINACIÓN</h6>
                                        <ul class="small mb-0">
                                            <li>Iluminación móvil</li>
                                            <li>Iluminación estática</li>
                                            <li>Accesorios de luz</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-12">
                        <h6 class="text-primary"><i class="fas fa-exclamation-triangle me-2"></i>Consideraciones Importantes</h6>
                        <div class="alert alert-warning mb-0">
                            <ul class="mb-0">
                                <li>Los códigos de grupo deben ser únicos en el sistema</li>
                                <li>Al desactivar un grupo, las familias vinculadas pueden verse afectadas</li>
                                <li>Los grupos ayudan a organizar reportes y estadísticas</li>
                                <li>Use nombres descriptivos y códigos cortos para facilitar la búsqueda</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
                <!-- <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
                    <i class="bi bi-check-lg mr-2"></i>Entendido
                </button> -->


            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="bi bi-check-lg mr-2"></i>Entendido</button>
            </div>
        </div>
    </div>
</div>
