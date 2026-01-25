<!-- Modal para Formulario de Línea -->
<div class="modal fade" id="modalFormularioLinea" tabindex="-1" aria-labelledby="modalFormularioLineaLabel" aria-hidden="true">
    <div class="modal-dialog" style="max-width: 80%; width: 80%; margin: 1.75rem auto;">
        <div class="modal-content" style="max-height: 90vh;">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalFormularioLineaLabel">
                    <i class="bi bi-pencil-square me-2"></i>Nueva Línea de Presupuesto
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form id="formLinea" name="formLinea" method="post">
                <div class="modal-body" style="max-height: calc(90vh - 150px); overflow-y: auto;">
                    <!-- Campos ocultos -->
                    <input type="hidden" id="id_linea_ppto" name="id_linea_ppto">
                    <input type="hidden" id="id_version_presupuesto_hidden" name="id_version_presupuesto">
                    <input type="hidden" name="numero_linea_ppto" value="1">
                    <input type="hidden" name="tipo_linea_ppto" value="articulo">
                    <input type="hidden" name="nivel_jerarquia" value="0">
                    <input type="hidden" name="orden_linea_ppto" value="0">
                    <input type="hidden" name="mostrar_obs_articulo_linea_ppto" value="1">
                    <input type="hidden" name="mostrar_en_presupuesto" value="1">
                    <input type="hidden" name="es_opcional" value="0">
                    <input type="hidden" name="activo_linea_ppto" value="1">
                    
                    <!-- SECCIÓN 1: ARTÍCULO -->
                    <div class="card mb-3 border-primary border-2 shadow-sm">
                        <div class="card-header bg-gradient text-white py-2" style="background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);">
                            <h6 class="mb-0">
                                <i class="bi bi-box-seam-fill me-2"></i>
                                <strong>1. Selección de Artículo</strong>
                                <span class="badge bg-light text-primary ms-2">REQUERIDO</span>
                            </h6>
                        </div>
                        <div class="card-body bg-light" style="background-color: #f8f9fc !important;">
                            <div class="row">
                                <!-- Artículo -->
                                <div class="col-md-8 mb-3">
                                    <label for="id_articulo" class="form-label fw-bold fs-6">
                                        <i class="bi bi-search me-1 text-primary"></i>Buscar Artículo *
                                    </label>
                                    <select class="form-select select2 form-select-lg" id="id_articulo" name="id_articulo" required style="border: 2px solid #0d6efd; font-size: 1rem;">
                                        <option value="">🔍 Escriba para buscar el artículo a alquilar...</option>
                                        <!-- Se carga dinámicamente -->
                                    </select>
                                </div>

                                <!-- Descripción (readonly, desde artículo) -->
                                <div class="col-md-4 mb-3">
                                    <label for="descripcion_linea_ppto" class="form-label fw-bold">
                                        <i class="bi bi-file-text me-1 text-secondary"></i>Descripción
                                    </label>
                                    <input type="text" class="form-control bg-white" id="descripcion_linea_ppto" 
                                           name="descripcion_linea_ppto" readonly placeholder="Descripción automática" style="border: 1px solid #dee2e6;">
                                </div>
                                
                                <!-- Campos ocultos adicionales del artículo -->
                                <input type="hidden" id="codigo_linea_ppto" name="codigo_linea_ppto">
                                <input type="hidden" id="id_impuesto" name="id_impuesto">
                                <input type="hidden" id="valor_coeficiente_linea_ppto" name="valor_coeficiente_linea_ppto" value="">
                            </div>
                            
                            <!-- Mensaje de ayuda al pie de la sección -->
                            <div class="row">
                                <div class="col-12">
                                    <small class="text-muted d-block">
                                        <i class="bi bi-lightbulb me-1"></i>Escriba el nombre, código o descripción del artículo
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECCIÓN 2: FECHAS -->
                    <div class="card mb-3">
                        <div class="card-header bg-info text-white py-2">
                            <h6 class="mb-0"><i class="bi bi-calendar-event-fill me-2"></i>2. Fechas de Planificación y Evento</h6>
                        </div>
                        <div class="card-body">
                            <div class="row mb-2">
                                <div class="col-md-12">
                                    <div class="alert alert-info py-2 mb-3">
                                        <small><i class="bi bi-info-circle me-1"></i>
                                        Las fechas se cargan automáticamente desde la cabecera del presupuesto pero pueden modificarse.</small>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Fechas de Planificación -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="fecha_montaje_linea_ppto" class="form-label fw-bold">
                                        <i class="bi bi-arrow-up-circle me-1 text-success"></i>Fecha Montaje (Planificación)
                                    </label>
                                    <input type="date" class="form-control" id="fecha_montaje_linea_ppto" 
                                           name="fecha_montaje_linea_ppto">
                                    <small class="text-muted">Solo informativa para planning</small>
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="fecha_desmontaje_linea_ppto" class="form-label fw-bold">
                                        <i class="bi bi-arrow-down-circle me-1 text-danger"></i>Fecha Desmontaje (Planificación)
                                        <span id="dias_planificacion" class="badge bg-info ms-2" style="display: none;">
                                            <i class="bi bi-calendar-range me-1"></i><span id="dias_planificacion_texto">0 días</span>
                                        </span>
                                    </label>
                                    <input type="date" class="form-control" id="fecha_desmontaje_linea_ppto" 
                                           name="fecha_desmontaje_linea_ppto">
                                    <small class="text-muted">Solo informativa para planning</small>
                                </div>
                            </div>

                            <!-- Fechas del Evento (para cobro) -->
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="fecha_inicio_linea_ppto" class="form-label fw-bold">
                                        <i class="bi bi-calendar-check me-1 text-primary"></i>Fecha Inicio Evento *
                                    </label>
                                    <input type="date" class="form-control" id="fecha_inicio_linea_ppto" 
                                           name="fecha_inicio_linea_ppto" required>
                                    <small class="text-muted">Para cálculo de cobro y coeficientes</small>
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="fecha_fin_linea_ppto" class="form-label fw-bold">
                                        <i class="bi bi-calendar-x me-1 text-primary"></i>Fecha Fin Evento *
                                        <span id="dias_evento" class="badge bg-primary ms-2" style="display: none;">
                                            <i class="bi bi-calendar-range me-1"></i><span id="dias_evento_texto">0 días</span>
                                        </span>
                                    </label>
                                    <input type="date" class="form-control" id="fecha_fin_linea_ppto" 
                                           name="fecha_fin_linea_ppto" required>
                                    <small class="text-muted">Para cálculo de cobro y coeficientes</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECCIÓN 3: PRECIOS Y CANTIDADES -->
                    <div class="card mb-3">
                        <div class="card-header bg-success text-white py-2">
                            <h6 class="mb-0"><i class="bi bi-currency-euro me-2"></i>3. Cantidad, Precio y Descuento</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <!-- Cantidad -->
                                <div class="col-md-2">
                                    <label for="cantidad_linea_ppto" class="form-label fw-bold">
                                        <i class="bi bi-123 me-1 text-success"></i>Cantidad *
                                    </label>
                                    <input type="number" class="form-control text-center" id="cantidad_linea_ppto" 
                                           name="cantidad_linea_ppto" min="0.01" step="0.01" value="1" required 
                                           onfocus="this.select()">
                                </div>
                                
                                <!-- Precio Unitario (desde artículo) -->
                                <div class="col-md-3">
                                    <label for="precio_unitario_linea_ppto" class="form-label fw-bold">
                                        <i class="bi bi-tag me-1 text-success"></i>Precio Unitario (sin IVA) *
                                    </label>
                                    <div class="input-group">
                                        <input type="number" class="form-control text-end bg-light" id="precio_unitario_linea_ppto" 
                                               name="precio_unitario_linea_ppto" min="0" step="0.01" value="0" readonly>
                                        <span class="input-group-text">€</span>
                                    </div>
                                    <small class="text-muted">Desde precio alquiler artículo</small>
                                </div>
                                
                                <!-- Descuento -->
                                <div class="col-md-2">
                                    <label for="descuento_linea_ppto" class="form-label fw-bold">
                                        <i class="bi bi-percent me-1 text-warning"></i>Descuento %
                                    </label>
                                    <div class="input-group">
                                        <input type="number" class="form-control text-center" id="descuento_linea_ppto" 
                                               name="descuento_linea_ppto" min="0" max="100" step="0.1" value="0" 
                                               onfocus="this.select()">
                                        <span class="input-group-text">%</span>
                                    </div>
                                    <!-- Avisos de artículo -->
                                    <div id="avisos_descuento" class="mt-2"></div>
                                </div>
                                
                                <!-- IVA (calculado desde artículo) -->
                                <div class="col-md-2">
                                    <label for="porcentaje_iva_linea_ppto" class="form-label fw-bold">
                                        <i class="bi bi-receipt me-1 text-info"></i>IVA
                                    </label>
                                    <div class="input-group">
                                        <input type="number" class="form-control text-center bg-light" 
                                               id="porcentaje_iva_linea_ppto" name="porcentaje_iva_linea_ppto" 
                                               value="21" readonly>
                                        <span class="input-group-text">%</span>
                                    </div>
                                    <small class="text-muted">Desde artículo</small>
                                </div>

                                <!-- Preview Total -->
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">
                                        <i class="bi bi-calculator-fill me-1 text-success"></i>Total Línea
                                    </label>
                                    <div class="alert alert-success mb-0 py-2 text-center">
                                        <strong class="fs-5" id="preview_total">0,00 €</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECCIÓN 4: COEFICIENTE REDUCTOR (OPCIONAL) -->
                    <div class="card mb-3 border-warning">
                        <div class="card-header bg-warning bg-opacity-10 py-2">
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input" type="checkbox" id="aplicar_coeficiente_linea_ppto" 
                                       name="aplicar_coeficiente_linea_ppto" value="1">
                                <label class="form-check-label fw-bold" for="aplicar_coeficiente_linea_ppto">
                                    <i class="bi bi-calculator me-1 text-warning"></i>
                                    4. Aplicar Coeficiente Reductor por Jornadas
                                </label>
                            </div>
                            <small class="text-muted d-block mt-1">Se calculará automáticamente según las fechas de inicio y fin del evento</small>
                            
                            <!-- Indicador de estado de coeficiente del artículo -->
                            <div id="info_estado_coeficiente" class="alert mt-2 mb-0 py-2 d-none">
                                <i class="fas fa-info-circle me-2"></i>
                                <span id="texto_estado_coeficiente"></span>
                            </div>
                        </div>
                        <div class="card-body d-none" id="campos_coeficiente">
                            <div class="row align-items-center justify-content-center">
                                <!-- Campo Jornadas Calculadas - OCULTO (se mantiene para envío de datos) -->
                                <input type="hidden" id="jornadas_linea_ppto" name="jornadas_linea_ppto" value="1">
                                
                                <!-- Campo Coeficiente Aplicado - OCULTO (se mantiene para envío de datos) -->
                                <input type="hidden" id="id_coeficiente" name="id_coeficiente" value="">
                                <input type="hidden" id="valor_coeficiente_linea_ppto" name="valor_coeficiente_linea_ppto" value="">
                                
                                <!-- Solo mostrar Factor y Precio con Coeficiente -->
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">
                                        <i class="bi bi-calculator me-1"></i>Factor Aplicado
                                    </label>
                                    <div class="alert alert-warning mb-0 py-2 text-center">
                                        <strong class="fs-5" id="vista_coeficiente">1.00x</strong>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">
                                        <i class="bi bi-currency-euro me-1"></i>Precio con Coeficiente
                                    </label>
                                    <div class="alert alert-info mb-0 py-2 text-center">
                                        <strong class="fs-5" id="preview_precio_coef">0,00 €</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECCIÓN 5: UBICACIÓN Y CONFIGURACIÓN -->
                    <div class="card mb-3">
                        <div class="card-header bg-secondary text-white py-2">
                            <h6 class="mb-0"><i class="bi bi-geo-alt-fill me-2"></i>5. Ubicación y Configuración</h6>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <!-- Ubicación del cliente -->
                                <div class="col-md-12">
                                    <label for="id_ubicacion" class="form-label fw-bold">
                                        <i class="bi bi-pin-map me-1 text-secondary"></i>Lugar de Montaje
                                    </label>
                                    <select class="form-select" id="id_ubicacion" name="id_ubicacion">
                                        <option value="">Sin ubicación específica</option>
                                        <!-- Se carga dinámicamente desde cliente_ubicacion -->
                                    </select>
                                    <small class="text-muted">Ubicación específica del cliente donde se realizará el montaje</small>
                                </div>
                            </div>

                            <!-- Contenedor de opciones KIT (solo visible si es KIT) -->
                            <div class="row" id="contenedor_opciones_kit" style="display: none;">
                                <!-- Ocultar detalle KIT -->
                                <div class="col-md-12">
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" id="ocultar_detalle_kit_linea_ppto" 
                                               name="ocultar_detalle_kit_linea_ppto" value="1">
                                        <label class="form-check-label fw-bold" for="ocultar_detalle_kit_linea_ppto">
                                            <i class="bi bi-eye-slash me-1 text-warning"></i>
                                            Ocultar Detalles del KIT en Impresión
                                        </label>
                                    </div>
                                    <small class="text-muted d-block mb-3">Si está marcado, no se mostrarán los componentes del KIT en el presupuesto impreso</small>
                                    
                                    <!-- Componentes del KIT -->
                                    <div class="alert alert-info mb-0">
                                        <h6 class="alert-heading mb-2">
                                            <i class="bi bi-box-seam me-1"></i>Componentes del KIT:
                                        </h6>
                                        <div id="lista_componentes_kit" class="small">
                                            <span class="text-muted">Seleccione un KIT para ver sus componentes</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECCIÓN 6: OBSERVACIONES -->
                    <div class="card mb-3">
                        <div class="card-header bg-dark text-white py-2">
                            <h6 class="mb-0"><i class="bi bi-chat-square-text-fill me-2"></i>6. Observaciones</h6>
                        </div>
                        <div class="card-body">
                            <textarea class="form-control" id="observaciones_linea_ppto" name="observaciones_linea_ppto" 
                                      rows="3" maxlength="500" placeholder="Observaciones o notas específicas de esta línea..."></textarea>
                            <small class="text-muted">Máximo 500 caracteres</small>
                        </div>
                    </div>

                </div>
                
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary" id="btnGuardarLinea">
                        <i class="bi bi-save me-1"></i>Guardar Línea
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Script específico del formulario -->
<script>
$(document).ready(function() {
    // Al cambiar el artículo, cargar datos automáticos
    $('#id_articulo').on('change', function() {
        const idArticulo = $(this).val();
        if (idArticulo) {
            cargarDatosArticulo(idArticulo);
        } else {
            limpiarDatosArticulo();
        }
    });

    // Toggle campos de coeficiente
    $('#aplicar_coeficiente_linea_ppto').on('change', function() {
        if ($(this).is(':checked')) {
            $('#campos_coeficiente').removeClass('d-none');
            calcularJornadas();
        } else {
            $('#campos_coeficiente').addClass('d-none');
            // Ocultar mensaje de estado de coeficiente al desactivar
            $('#info_estado_coeficiente').addClass('d-none');
        }
        calcularPreview();
    });

    // Calcular jornadas y precio cuando cambien las fechas del evento
    $('#fecha_inicio_linea_ppto, #fecha_fin_linea_ppto').on('change', function() {
        // Recalcular preview con las nuevas jornadas
        calcularPreview();
        
        // Si el coeficiente está activado, recalcular también el coeficiente
        if ($('#aplicar_coeficiente_linea_ppto').is(':checked')) {
            calcularJornadas();
        }
    });

    // Calcular preview en tiempo real cuando cambien cantidad, precio, descuento o IVA
    $('#cantidad_linea_ppto, #precio_unitario_linea_ppto, #descuento_linea_ppto, #porcentaje_iva_linea_ppto').on('input change', function() {
        calcularPreview();
        
        // Si el coeficiente está activado, recalcular jornadas
        if ($('#aplicar_coeficiente_linea_ppto').is(':checked')) {
            calcularJornadas();
        }
    });

    // Submit del formulario
    $('#formLinea').on('submit', function(e) {
        e.preventDefault();
        guardarLinea();
    });

    // Inicializar Select2 si está disponible
    if ($.fn.select2) {
        $('#id_articulo').select2({
            theme: 'bootstrap-5',
            dropdownParent: $('#modalFormularioLinea'),
            placeholder: 'Buscar artículo...',
            allowClear: true,
            width: '100%'
        });
    }
});

/**
 * Carga datos automáticos del artículo seleccionado
 * @param {number} idArticulo - ID del artículo a cargar
 * @param {boolean} esEdicion - Si es true, no sobrescribe precio ni otros datos ya cargados
 */
function cargarDatosArticulo(idArticulo, esEdicion = false) {
    $.ajax({
        url: '../../controller/articulo.php?op=mostrar',
        type: 'POST',
        data: { id_articulo: idArticulo },
        dataType: 'json',
        success: function(data) {
            if (data) {
                // Cargar descripción (readonly) - solo si está vacía o no es edición
                if (!esEdicion || !$('#descripcion_linea_ppto').val()) {
                    $('#descripcion_linea_ppto').val(data.nombre_articulo || '');
                }
                
                // Cargar código del artículo - solo si está vacío o no es edición
                if (!esEdicion || !$('#codigo_linea_ppto').val()) {
                    $('#codigo_linea_ppto').val(data.codigo_articulo || '');
                }
                
                // Cargar ID del impuesto - solo si está vacío o no es edición
                if (!esEdicion || !$('#id_impuesto').val()) {
                    $('#id_impuesto').val(data.id_impuesto || '');
                }
                
                // Cargar precio de alquiler - SOLO en creación, NO en edición
                if (!esEdicion) {
                    $('#precio_unitario_linea_ppto').val(parseFloat(data.precio_alquiler_articulo || 0).toFixed(2));
                }
                
                // Cargar IVA del artículo desde tasa_impuesto (SIEMPRE, en creación y edición)
                // Este campo es de solo lectura y proviene de la configuración del artículo
                const tasaIva = data.tasa_impuesto || 21;
                $('#porcentaje_iva_linea_ppto').val(tasaIva);
                
                // Establecer descuento por defecto y mostrar avisos - SOLO en creación
                if (!esEdicion) {
                    const noFacturar = data.no_facturar_articulo == 1 || data.no_facturar_articulo == '1';
                    const permitirDescuentos = data.permitir_descuentos_articulo == 1 || data.permitir_descuentos_articulo == '1';
                    
                    // Establecer valor por defecto del descuento
                    if (noFacturar) {
                        $('#descuento_linea_ppto').val(100);
                    } else if (!permitirDescuentos) {
                        $('#descuento_linea_ppto').val(0);
                    }
                }
                
                // Mostrar avisos (siempre, en creación y edición)
                mostrarAvisosDescuento(data);
                
                // Verificar si es un KIT
                const esKit = data.es_kit_articulo == 1 || data.es_kit_articulo == '1' || data.es_kit_articulo === true;
                
                console.log('Artículo cargado:', data.nombre_articulo, 'Es KIT:', esKit, 'Valor es_kit_articulo:', data.es_kit_articulo);
                
                if (esKit) {
                    // Mostrar sección de KIT
                    $('#contenedor_opciones_kit').show();
                    // Cargar componentes del KIT
                    cargarComponentesKit(idArticulo);
                } else {
                    // Ocultar sección de KIT para artículos normales
                    $('#contenedor_opciones_kit').hide();
                    $('#ocultar_detalle_kit_linea_ppto').prop('checked', false);
                }
                
                // Recalcular preview
                calcularPreview();
            }
        },
        error: function() {
            Swal.fire({
                icon: 'warning',
                title: 'Aviso',
                text: 'No se pudieron cargar los datos del artículo'
            });
        }
    });
}

/**
 * Muestra avisos sobre descuentos según las propiedades del artículo
 */
function mostrarAvisosDescuento(data) {
    const contenedor = $('#avisos_descuento');
    contenedor.empty();
    
    const noFacturar = data.no_facturar_articulo == 1 || data.no_facturar_articulo == '1';
    const permitirDescuentos = data.permitir_descuentos_articulo == 1 || data.permitir_descuentos_articulo == '1';
    
    let avisos = [];
    
    if (noFacturar) {
        avisos.push('<small class="badge bg-danger"><i class="bi bi-exclamation-triangle me-1"></i>Marcado como no facturable</small>');
    }
    
    if (!permitirDescuentos) {
        avisos.push('<small class="badge bg-warning text-dark"><i class="bi bi-slash-circle me-1"></i>Marcado como no permitir descuentos</small>');
    }
    
    if (avisos.length > 0) {
        contenedor.html(avisos.join('<br>'));
    }
}

/**
 * Carga los componentes de un KIT
 */
function cargarComponentesKit(idArticulo) {
    console.log('Cargando componentes del KIT:', idArticulo);
    
    $.ajax({
        url: '../../controller/articulo.php?op=obtener_componentes_kit',
        type: 'POST',
        data: { id_articulo: idArticulo },
        dataType: 'json',
        success: function(response) {
            console.log('Respuesta componentes KIT:', response);
            
            const contenedor = $('#lista_componentes_kit');
            
            if (response.success && response.data && response.data.length > 0) {
                let html = '<ul class="mb-0">';
                response.data.forEach(function(componente) {
                    const cantidad = componente.cantidad_kit || 1;
                    const codigo = componente.codigo_articulo || '';
                    const nombre = componente.nombre_articulo || '';
                    
                    html += `<li><strong>${cantidad}x</strong> ${codigo} - ${nombre}</li>`;
                });
                html += '</ul>';
                contenedor.html(html);
            } else {
                contenedor.html('<span class="text-muted">Este KIT no tiene componentes definidos</span>');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error al cargar componentes:', error);
            console.error('Respuesta:', xhr.responseText);
            $('#lista_componentes_kit').html('<span class="text-danger">Error al cargar componentes</span>');
        }
    });
}

/**
 * Limpia los datos del artículo
 */
function limpiarDatosArticulo() {
    $('#descripcion_linea_ppto').val('');
    $('#precio_unitario_linea_ppto').val('0.00');
    $('#porcentaje_iva_linea_ppto').val('21');
    $('#contenedor_opciones_kit').hide();
    $('#lista_componentes_kit').html('<span class="text-muted">Seleccione un KIT para ver sus componentes</span>');
    calcularPreview();
}

/**
 * Calcula el número de jornadas entre fechas inicio y fin
 */
function calcularJornadas() {
    const fechaInicio = $('#fecha_inicio_linea_ppto').val();
    const fechaFin = $('#fecha_fin_linea_ppto').val();
    
    if (fechaInicio && fechaFin) {
        const inicio = new Date(fechaInicio);
        const fin = new Date(fechaFin);
        const diffTime = Math.abs(fin - inicio);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1; // +1 para incluir ambos días
        
        $('#jornadas_linea_ppto').val(diffDays);
        
        // Cargar coeficiente según jornadas
        if (diffDays > 0) {
            cargarCoeficiente(diffDays);
        }
    }
}

/**
 * Carga el coeficiente correspondiente según las jornadas
 * ESTRATEGIA DE FALLBACK:
 * 1. Buscar coeficiente exacto
 * 2. Si no existe, usar superior más cercano
 * 3. Si no existe, usar inferior más cercano
 * 4. Si no existe ninguno, usar 1.00 (sin descuento)
 */
function cargarCoeficiente(jornadas) {
    $.ajax({
        url: '../../controller/coeficiente.php?op=obtener_por_jornadas',
        type: 'POST',
        data: { jornadas: jornadas },
        dataType: 'json',
        success: function(response) {
            if (response.success && response.data) {
                const data = response.data;
                
                // Actualizar campos ocultos
                $('#id_coeficiente').val(data.id_coeficiente || '');
                $('#valor_coeficiente_linea_ppto').val(data.factor_coeficiente);
                
                // Actualizar visualización del factor
                $('#vista_coeficiente').text(parseFloat(data.factor_coeficiente).toFixed(2) + 'x');
                
                // Mostrar aviso si no es coeficiente exacto
                const infoDiv = $('#info_estado_coeficiente');
                const textoDiv = $('#texto_estado_coeficiente');
                
                if (!data.es_exacto) {
                    // Coeficiente aproximado - mostrar aviso en amarillo
                    infoDiv.removeClass('alert-success alert-danger d-none')
                           .addClass('alert-warning');
                    textoDiv.html('<strong>⚠️ Coeficiente Aproximado:</strong> ' + data.mensaje);
                } else {
                    // Coeficiente exacto - mostrar confirmación en verde
                    infoDiv.removeClass('alert-warning alert-danger d-none')
                           .addClass('alert-success');
                    textoDiv.html('<strong>✓ Coeficiente Exacto:</strong> ' + data.mensaje);
                }
                
                // Recalcular precios
                calcularPreview();
            } else {
                // Error en la respuesta - usar fallback 1.00
                console.error('Error al cargar coeficiente:', response);
                aplicarCoeficientePorDefecto(jornadas, response.message || 'Error desconocido');
            }
        },
        error: function(xhr, status, error) {
            // Error de red - usar fallback 1.00
            console.error('Error AJAX al cargar coeficiente:', error);
            aplicarCoeficientePorDefecto(jornadas, 'Error de conexión con el servidor');
        }
    });
}

/**
 * Aplica coeficiente por defecto (1.00) cuando hay error
 */
function aplicarCoeficientePorDefecto(jornadas, motivoError) {
    // Aplicar valores por defecto
    $('#id_coeficiente').val('');
    $('#valor_coeficiente_linea_ppto').val('1.00');
    $('#vista_coeficiente').text('1.00x');
    
    // Mostrar aviso de error
    const infoDiv = $('#info_estado_coeficiente');
    const textoDiv = $('#texto_estado_coeficiente');
    
    infoDiv.removeClass('alert-success alert-warning d-none')
           .addClass('alert-danger');
    textoDiv.html('<strong>❌ Error:</strong> ' + motivoError + '. Aplicando precio base (1.00x)');
    
    // Mostrar alerta temporal al usuario
    Swal.fire({
        icon: 'warning',
        title: 'Aviso',
        text: 'No se pudo cargar el coeficiente. Se aplicará el precio base sin descuento.',
        timer: 3000,
        showConfirmButton: false,
        toast: true,
        position: 'top-end'
    });
    
    // Recalcular precios
    calcularPreview();
}

/**
 * Calcula preview de totales en tiempo real
 * Fórmula paso a paso según especificación:
 * 1. Aplicar coeficiente si está activado: precio = precio × coeficiente
 * 2. base = días × cantidad × precio (sin IVA)
 * 3. con_descuento = base - (base × descuento/100)
 * 4. total = con_descuento + (con_descuento × iva/100)
 */
function calcularPreview() {
    const cantidad = parseFloat($('#cantidad_linea_ppto').val()) || 0;
    let precioUnitario = parseFloat($('#precio_unitario_linea_ppto').val()) || 0;
    const descuento = parseFloat($('#descuento_linea_ppto').val()) || 0;
    const iva = parseFloat($('#porcentaje_iva_linea_ppto').val()) || 0;
    
    // Obtener jornadas del evento (días de alquiler)
    const fechaInicio = $('#fecha_inicio_linea_ppto').val();
    const fechaFin = $('#fecha_fin_linea_ppto').val();
    let dias = 1;
    
    if (fechaInicio && fechaFin) {
        const inicio = new Date(fechaInicio);
        const fin = new Date(fechaFin);
        const diferencia = Math.floor((fin - inicio) / (1000 * 60 * 60 * 24));
        dias = diferencia + 1; // Jornadas inclusivas
    }

    // Verificar si se aplica coeficiente
    const aplicarCoeficiente = $('#aplicar_coeficiente_linea_ppto').is(':checked');
    
    if (aplicarCoeficiente) {
        const coeficiente = parseFloat($('#valor_coeficiente_linea_ppto').val()) || 1.0;
        // Aplicar coeficiente al precio unitario
        precioUnitario = precioUnitario * coeficiente;
        $('#preview_precio_coef').text(formatearMoneda(precioUnitario));
    }

    // PASO 1: Base = días × cantidad × precio_unitario (con coeficiente si aplica)
    const base = dias * cantidad * precioUnitario;

    // PASO 2: Aplicar descuento
    const importeDescuento = (base * descuento) / 100;
    const conDescuento = base - importeDescuento;

    // PASO 3: Calcular total con IVA
    const importeIva = (conDescuento * iva) / 100;
    const total = conDescuento + importeIva;
    
    // Mostrar el total en la interfaz
    $('#preview_total').text(formatearMoneda(total));
}

/**
 * Formatea un número como moneda (español)
 */
function formatearMoneda(valor) {
    return new Intl.NumberFormat('es-ES', {
        style: 'currency',
        currency: 'EUR',
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(valor || 0);
}

/**
 * Guarda la línea (INSERT o UPDATE)
 */
function guardarLinea() {
    // Serializar formulario
    let formData = $('#formLinea').serializeArray();
    
    // IMPORTANTE: Agregar los checkboxes explícitamente
    // Los checkboxes desmarcados no se envían con serialize()
    const aplicarCoeficiente = $('#aplicar_coeficiente_linea_ppto').is(':checked') ? 1 : 0;
    formData.push({
        name: 'aplicar_coeficiente_linea_ppto',
        value: aplicarCoeficiente
    });
    
    // Agregar checkbox ocultar_detalle_kit_linea_ppto explícitamente
    const ocultarDetalleKit = $('#ocultar_detalle_kit_linea_ppto').is(':checked') ? 1 : 0;
    formData.push({
        name: 'ocultar_detalle_kit_linea_ppto',
        value: ocultarDetalleKit
    });
    
    // Si NO se aplica coeficiente, asegurar que jornadas, id_coeficiente y valor_coeficiente sean NULL/vacío
    if (!aplicarCoeficiente) {
        // Buscar y actualizar o agregar estos campos
        let jornadasField = formData.find(f => f.name === 'jornadas_linea_ppto');
        let coefField = formData.find(f => f.name === 'id_coeficiente');
        let valorCoefField = formData.find(f => f.name === 'valor_coeficiente_linea_ppto');
        
        if (jornadasField) {
            jornadasField.value = '';
        } else {
            formData.push({ name: 'jornadas_linea_ppto', value: '' });
        }
        
        if (coefField) {
            coefField.value = '';
        } else {
            formData.push({ name: 'id_coeficiente', value: '' });
        }
        
        if (valorCoefField) {
            valorCoefField.value = '';
        } else {
            formData.push({ name: 'valor_coeficiente_linea_ppto', value: '' });
        }
    } else {
        // Si SÍ se aplica coeficiente, asegurar que id_coeficiente y valor_coeficiente se envíen
        let coefField = formData.find(f => f.name === 'id_coeficiente');
        let valorCoefField = formData.find(f => f.name === 'valor_coeficiente_linea_ppto');
        
        // Si no están en formData, agregarlos desde los campos hidden
        if (!coefField) {
            let idCoef = $('#id_coeficiente').val();
            if (idCoef && idCoef !== '' && idCoef !== 'null') {
                formData.push({ name: 'id_coeficiente', value: idCoef });
            }
        }
        
        if (!valorCoefField) {
            let valorCoef = $('#valor_coeficiente_linea_ppto').val();
            if (valorCoef && valorCoef !== '' && valorCoef !== 'null') {
                formData.push({ name: 'valor_coeficiente_linea_ppto', value: valorCoef });
            }
        }
    }
    
    const operacion = $('#id_linea_ppto').val() ? 'guardaryeditar' : 'guardaryeditar';

    $.ajax({
        url: `../../controller/lineapresupuesto.php?op=${operacion}`,
        type: 'POST',
        data: $.param(formData),
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Éxito',
                    text: response.message,
                    timer: 2000,
                    showConfirmButton: false
                });
                $('#modalFormularioLinea').modal('hide');
                tabla.ajax.reload();
                cargarTotales();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message
                });
            }
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se pudo guardar la línea'
            });
        }
    });
}
</script>
