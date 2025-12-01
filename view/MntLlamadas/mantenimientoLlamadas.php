<!-- Modal con animación fade -->
<div class="modal fade" id="modalLlamada" tabindex="-1" aria-labelledby="modalMostrarVacacionesLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header pd-y-20 pd-x-25">
                <h6 class="tx-14 mg-b-0 tx-uppercase tx-inverse tx-bold">Llamadas</h6>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body pd-25">
                <h4 class="lh-3 mg-b-20" id="mdltitulo"><a href="" class="tx-inverse hover-primary">Mantenimiento de llamadas</a></h4>
                <form id="formLlamada">
                    <!-- ID LLAMADA -->
                    <input type="hidden" name="id_llamada" id="id_llamada">
                    
                    <!-- Contenedor principal con espaciado -->
                    <div class="form-container" style="margin-left: 15px; margin-right: 15px;">
                        
                        <!-- BLOQUE 1: INFORMACIÓN BÁSICA -->
                        <div class="card mb-4 border-primary">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0 tx-bold">Información Básica</h5>
                            </div>
                            <div class="card-body">
                                <!-- ID METODO -->
                                <div class="form-group row mb-3">
                                    <div class="col-12 col-lg-3">
                                        <label for="metodo" class="form-label">Método:<span class="tx-danger">*</span></label>
                                    </div>
                                    <div class="col-7 col-sm-9">
                                        <select id="id_metodo" name="id_metodo" class="form-control wd-300" required>
                                            <!-- Vacío, se llenará vía AJAX -->
                                        </select>
                                        <div class="invalid-feedback small-invalid-feedback">
                                            Por favor seleccione un método.
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- NOMBRE COMUNICANTE -->
                                <div class="form-group row mb-3">
                                    <div class="col-12 col-lg-3">
                                        <label for="nombre_comunicante" class="form-label">Nombre Comunicante:<span class="tx-danger">*</span></label>
                                    </div>
                                    <div class="col-7 col-sm-9">
                                        <input type="text" class="form-control wd-500" name="nombre_comunicante" id="nombre_comunicante" maxlength="100" placeholder="Nombre comunicante..." required>
                                        <div class="invalid-feedback small-invalid-feedback">Escribe el nombre del comunicante</div>
                                    </div>
                                </div>
                                
                                <!-- DOMICILIO INSTALACIÓN -->
                                <div class="form-group row mb-3">
                                    <div class="col-12 col-lg-3">
                                        <label for="domicilio_instalacion" class="form-label">Domicilio Instalación:<span class="tx-danger">*</span></label>
                                    </div>
                                    <div class="col-7 col-sm-9">
                                        <input type="text" class="form-control wd-500" name="domicilio_instalacion" id="domicilio_instalacion" maxlength="100" placeholder="Domicilio de instalación..." required>
                                        <div class="invalid-feedback small-invalid-feedback">Escribe el nombre del domicilio de instalación</div>
                                    </div>
                                </div>

                                 <!-- FECHA RECEPCION -->
                                 <div class="form-group row mb-3">
                                    <div class="col-12 col-lg-3">
                                        <label for="fecha_recepcion" class="form-label">Fecha de Recepción:<span class="tx-danger">*</span></label>
                                    </div>
                                    <div class="col-7 col-sm-9">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <i class="fa fa-calendar tx-16 lh-0 op-6"></i>
                                                </div>
                                            </div>
                                            <input id="fecha_recepcion" name="fecha_recepcion" type="text" class="form-control fc-datepicker" placeholder="dd-mm-aaaa HH:mm:ss" readonly required tabindex="-1">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- BLOQUE 2: DATOS DE CONTACTO -->
                        <div class="card mb-4 border-info">
                            <div class="card-header bg-info text-white">
                                <h5 class="mb-0 tx-bold">Datos de Contacto</h5>
                            </div>
                            <div class="card-body">
                                <!-- TELEFONO FIJO -->
                                <div class="form-group row mb-3">
                                    <div class="col-12 col-lg-3">
                                        <label for="telefono_fijo" class="form-label">Teléfono Fijo:</label>
                                    </div>
                                    <div class="col-7 col-sm-9">
                                        <input type="text" class="form-control wd-300" name="telefono_fijo" id="telefono_fijo" maxlength="14" placeholder="Teléfono fijo (xxx-xxx-xxx)...">
                                        <div class="text-muted small">Opcional</div>
                                    </div>
                                </div>
                                
                                <!-- TELEFONO MÓVIL -->
                                <div class="form-group row mb-3">
                                    <div class="col-12 col-lg-3">
                                        <label for="telefono_movil" class="form-label">Teléfono Móvil:</label>
                                    </div>
                                    <div class="col-7 col-sm-9">
                                        <input type="text" class="form-control wd-300" name="telefono_movil" id="telefono_movil" maxlength="14" placeholder="Teléfono móvil (xxx-xxx-xxx)...">
                                        <div class="text-muted small">Opcional</div>
                                    </div>
                                </div>
                                
                                <!-- EMAIL CONTACTO -->
                                <div class="form-group row mb-3">
                                    <div class="col-12 col-lg-3">
                                        <label for="email_contacto" class="form-label">Email Contacto:</label>
                                    </div>
                                    <div class="col-7 col-sm-9">
                                        <input type="text" class="form-control wd-300" name="email_contacto" id="email_contacto" maxlength="50" placeholder="ejemplo@gmail.com...">
                                        <div class="text-muted small">Opcional</div>
                                    </div>
                                </div>
                                
                                <!-- FECHA HORA PREFERIDA -->
                                <div class="form-group row mb-3">
                                    <div class="col-12 col-lg-3">
                                        <label for="fecha_hora_preferida" class="form-label">Fecha de Hora Preferida:</label>
                                    </div>
                                    <div class="col-7 col-sm-9">
                                        <div class="input-group" id="fecha_hora_wrapper">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <i class="fa fa-calendar tx-16 lh-0 op-6"></i>
                                                </div>
                                            </div>
                                            <input id="fecha_hora_preferida" name="fecha_hora_preferida" type="text" class="form-control" placeholder="dd-mm-aaaa HH:mm:ss" readonly>
                                        </div>
                                        <div class="tx-8 tx-info" id="borrarFechaHoraPreferida">Borrar fecha de hora preferida</div>
                                        <div class="text-muted small">Opcional</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- BLOQUE 3: GESTIÓN DE LA LLAMADA -->
                        <div class="card mb-4 border-success">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0 tx-bold">Gestión de la Llamada</h5>
                            </div>
                            <div class="card-body">
                                <!-- RESPONSABLE ASIGNADO -->
                                <div class="form-group row mb-3">
                                    <div class="col-12 col-lg-3">
                                        <label for="id_comercial_asignado" class="form-label">Responsable Asignado:<span class="tx-danger">*</span></label>
                                    </div>
                                    <div class="col-7 col-sm-9">
                                        <select id="id_comercial_asignado" name="id_comercial_asignado" class="form-control wd-300" required>
                                            <!-- Vacío, se llenará vía AJAX -->
                                        </select>
                                        <div class="invalid-feedback small-invalid-feedback">
                                            Por favor seleccione un Responsable de atender la llamada.
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- ESTADO -->
                                <div class="form-group row mb-3">
                                    <div class="col-12 col-lg-3">
                                        <label for="estado" class="form-label">Estado:<span class="tx-danger">*</span></label>
                                    </div>
                                    <div class="col-7 col-sm-9">
                                        <select id="estado" name="estado" class="form-control wd-300" required>
                                            <!-- Vacío, se llenará vía AJAX -->
                                        </select>
                                        <div class="invalid-feedback small-invalid-feedback">
                                            Por favor seleccione un estado.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- BLOQUE 4: OBSERVACIONES -->
                        <div class="card mb-3 border-warning">
                            <div class="card-header bg-warning text-dark">
                                <h5 class="mb-0 tx-bold">Observaciones</h5>
                            </div>
                            <div class="card-body">
                                <!-- OBSERVACIONES -->
                                <div class="form-group row mb-0">
                                    <div class="col-12 col-lg-3">
                                        <label for="observaciones" class="form-label">Observaciones:</label>
                                    </div>
                                    <div class="col-7 col-sm-9">
                                        <!-- Convertimos el textarea en Summernote -->
                                        <div id="observaciones" name="observaciones"></div>
                                        <div class="text-muted small">Opcional</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" name="action" id="btnsalvar" class="btn btn-primary tx-11 tx-uppercase pd-y-12 pd-x-25 tx-mont tx-medium">Salvar</button>
                <button type="button" class="btn btn-secondary tx-11 tx-uppercase pd-y-12 pd-x-25 tx-mont tx-medium" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>