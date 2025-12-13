$(document).ready(function () {
    console.log('formularioPresupuesto.js cargado');
    console.log('Botón Nuevo existe:', $('#btnNuevoContacto').length);
    console.log('Botón Editar existe:', $('#btnEditarContacto').length);

    /////////////////////////////////////
    //     FORMATEO DE CAMPOS          //
    ///////////////////////////////////

    var formValidator = new FormValidator('formPresupuesto', {
        fecha_presupuesto: {
            required: true
        },
        id_cliente: {
            required: true
        },
        id_estado_ppto: {
            required: true
        }
    });

    /////////////////////////////////////////
    //   VALIDACIONES DE FECHAS           //
    ///////////////////////////////////////

    // Validar fecha de validez >= fecha presupuesto
    function validarFechaValidez() {
        var fechaPresupuesto = $('#fecha_presupuesto').val();
        var fechaValidez = $('#fecha_validez_presupuesto').val();
        
        if (fechaPresupuesto && fechaValidez) {
            if (fechaValidez < fechaPresupuesto) {
                $('#fecha_validez_presupuesto').addClass('is-invalid');
                toastr.warning('La fecha de validez debe ser mayor o igual a la fecha del presupuesto');
                return false;
            } else {
                $('#fecha_validez_presupuesto').removeClass('is-invalid');
                return true;
            }
        }
        $('#fecha_validez_presupuesto').removeClass('is-invalid');
        return true;
    }

    // Validar fecha fin evento >= fecha inicio evento
    function validarFechasEvento() {
        var fechaInicio = $('#fecha_inicio_evento_presupuesto').val();
        var fechaFin = $('#fecha_fin_evento_presupuesto').val();
        
        if (fechaInicio && fechaFin) {
            if (fechaFin < fechaInicio) {
                $('#fecha_fin_evento_presupuesto').addClass('is-invalid');
                toastr.warning('La fecha de fin del evento debe ser mayor o igual a la fecha de inicio');
                return false;
            } else {
                $('#fecha_fin_evento_presupuesto').removeClass('is-invalid');
                return true;
            }
        }
        $('#fecha_fin_evento_presupuesto').removeClass('is-invalid');
        return true;
    }

    // Listeners para validación en tiempo real
    $('#fecha_presupuesto, #fecha_validez_presupuesto').on('change', function() {
        validarFechaValidez();
    });

    $('#fecha_inicio_evento_presupuesto, #fecha_fin_evento_presupuesto').on('change', function() {
        validarFechasEvento();
    });

    // Listener para recalcular fecha de validez al cambiar fecha de presupuesto
    $('#fecha_presupuesto').on('change', function() {
        var fechaPresupuesto = $(this).val();
        if (fechaPresupuesto) {
            var fechaValidezActual = $('#fecha_validez_presupuesto').val();
            
            // Si no hay fecha de validez previa, calcularla automáticamente
            if (!fechaValidezActual) {
                // Asegurar que los días de validez estén cargados
                if (diasValidezPresupuesto === 30) {
                    // Cargar días si aún no se han cargado
                    cargarDiasValidezEmpresa().then(function() {
                        var nuevaFechaValidez = calcularFechaValidez(fechaPresupuesto);
                        $('#fecha_validez_presupuesto').val(nuevaFechaValidez);
                        console.log('✓ Fecha de validez calculada:', nuevaFechaValidez, '(+' + diasValidezPresupuesto + ' días)');
                    });
                } else {
                    // Ya están cargados, calcular directamente
                    var nuevaFechaValidez = calcularFechaValidez(fechaPresupuesto);
                    $('#fecha_validez_presupuesto').val(nuevaFechaValidez);
                    console.log('✓ Fecha de validez calculada:', nuevaFechaValidez, '(+' + diasValidezPresupuesto + ' días)');
                }
            } else {
                // Si ya existe, solo mostrar sugerencia en consola
                var fechaSugerida = calcularFechaValidez(fechaPresupuesto);
                console.log('ℹ Fecha de validez sugerida:', fechaSugerida, '(actual: ' + fechaValidezActual + ')');
            }
        }
    });

    /////////////////////////////////////////
    //   FIN VALIDACIONES DE FECHAS       //
    ///////////////////////////////////////

    /////////////////////////////////////////
    //     FIN FORMATEO DE CAMPOS          //
    ////////////////////////////////////////

    /////////////////////////////////////////
    //   FUNCIONES DE INICIALIZACIÓN      //
    ///////////////////////////////////////

    // Variable global para almacenar los días de validez
    var diasValidezPresupuesto = 30; // Valor por defecto

    // Cargar opciones de selects
    cargarClientes();
    cargarEstadosPresupuesto();
    cargarFormasPago();
    cargarMetodosContacto();
    
    // Cargar información de la empresa (nombre y días de validez) al iniciar
    cargarDiasValidezEmpresa();

    // Función para obtener parámetros de la URL
    function getUrlParameter(name) {
        name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
        var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
        var results = regex.exec(location.search);
        return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
    }

    // Función para cargar días de validez de la empresa ficticia principal (retorna Promise)
    function cargarDiasValidezEmpresa() {
        return $.ajax({
            url: "../../controller/empresas.php?op=obtenerEmpresaActiva",
            type: "GET",
            dataType: "json"
        }).then(function(data) {
            if (data && data.dias_validez_presupuesto_empresa) {
                diasValidezPresupuesto = parseInt(data.dias_validez_presupuesto_empresa);
                console.log('✓ Días de validez cargados desde empresa:', diasValidezPresupuesto);
                
                // Actualizar campos informativos
                if (data.nombre_empresa) {
                    $('#nombre_empresa_info').val(data.nombre_empresa);
                }
                $('#dias_validez_info').val(diasValidezPresupuesto);
            } else {
                console.warn('⚠ No se pudieron cargar los días de validez, usando valor por defecto: 30');
                diasValidezPresupuesto = 30;
                $('#nombre_empresa_info').val('No disponible');
                $('#dias_validez_info').val('30');
            }
            return diasValidezPresupuesto;
        }).fail(function(xhr, status, error) {
            console.error('✗ Error al cargar días de validez de empresa:', error);
            diasValidezPresupuesto = 30; // Valor por defecto en caso de error
            $('#nombre_empresa_info').val('Error al cargar');
            $('#dias_validez_info').val('30');
            return diasValidezPresupuesto;
        });
    }

    // Función para calcular fecha de validez basada en fecha de presupuesto
    function calcularFechaValidez(fechaPresupuesto) {
        if (!fechaPresupuesto) {
            return '';
        }
        
        // Convertir la fecha string a objeto Date
        var fecha = new Date(fechaPresupuesto + 'T00:00:00');
        
        // Sumar los días de validez
        fecha.setDate(fecha.getDate() + diasValidezPresupuesto);
        
        // Convertir de vuelta a formato YYYY-MM-DD
        var year = fecha.getFullYear();
        var month = String(fecha.getMonth() + 1).padStart(2, '0');
        var day = String(fecha.getDate()).padStart(2, '0');
        
        return year + '-' + month + '-' + day;
    }

    // Función para cargar clientes
    function cargarClientes() {
        $.ajax({
            url: "../../controller/cliente.php?op=listar",
            type: "GET",
            dataType: "json",
            success: function(data) {
                var select = $('#id_cliente');
                select.empty();
                select.append('<option value="">Seleccione un cliente</option>');
                
                if (data.data && Array.isArray(data.data)) {
                    $.each(data.data, function(index, cliente) {
                        if (cliente.activo_cliente == 1) {
                            select.append('<option value="' + cliente.id_cliente + '">' + cliente.nombre_cliente + '</option>');
                        }
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar clientes:', error);
                toastr.error('Error al cargar la lista de clientes');
            }
        });
    }

    // Función para cargar estados de presupuesto
    function cargarEstadosPresupuesto() {
        $.ajax({
            url: "../../controller/estado_presupuesto.php?op=listar",
            type: "GET",
            dataType: "json",
            success: function(data) {
                var select = $('#id_estado_ppto');
                select.empty();
                select.append('<option value="">Seleccione un estado</option>');
                
                if (data.data && Array.isArray(data.data)) {
                    $.each(data.data, function(index, estado) {
                        if (estado.activo_estado_ppto == 1) {
                            select.append('<option value="' + estado.id_estado_ppto + '">' + estado.nombre_estado_ppto + '</option>');
                        }
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar estados:', error);
                toastr.error('Error al cargar la lista de estados');
            }
        });
    }

    // Función para cargar formas de pago
    function cargarFormasPago() {
        $.ajax({
            url: "../../controller/formaspago.php?op=listar",
            type: "GET",
            dataType: "json",
            success: function(data) {
                var select = $('#id_forma_pago');
                select.empty();
                select.append('<option value="">Sin forma de pago</option>');
                
                if (data.data && Array.isArray(data.data)) {
                    $.each(data.data, function(index, formaPago) {
                        if (formaPago.activo_pago == 1) {
                            var descripcion = formaPago.nombre_pago;
                            // Añadir información adicional si está disponible
                            if (formaPago.nombre_metodo_pago) {
                                descripcion += ' (' + formaPago.nombre_metodo_pago + ')';
                            }
                            select.append('<option value="' + formaPago.id_pago + '">' + descripcion + '</option>');
                        }
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar formas de pago:', error);
                toastr.error('Error al cargar la lista de formas de pago');
            }
        });
    }

    // Función para cargar métodos de contacto
    function cargarMetodosContacto() {
        $.ajax({
            url: "../../controller/metodos.php?op=listar",
            type: "GET",
            dataType: "json",
            success: function(data) {
                var select = $('#id_metodo');
                select.empty();
                select.append('<option value="">Sin método específico</option>');
                
                if (data.data && Array.isArray(data.data)) {
                    $.each(data.data, function(index, metodo) {
                        if (metodo.estado == 1) {
                            select.append('<option value="' + metodo.id_metodo + '">' + metodo.nombre + '</option>');
                        }
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar métodos de contacto:', error);
                toastr.error('Error al cargar la lista de métodos de contacto');
            }
        });
    }

    // Función para cargar contactos del cliente seleccionado
    function cargarContactosCliente(idCliente, idContactoSeleccionado = null) {
        var select = $('#id_contacto_cliente');
        select.empty();
        
        if (!idCliente) {
            select.append('<option value="">Seleccione primero un cliente</option>');
            select.prop('disabled', true);
            return;
        }
        
        select.prop('disabled', true);
        select.append('<option value="">Cargando contactos...</option>');
        
        $.ajax({
            url: "../../controller/clientes_contacto.php?op=selectByCliente",
            type: "POST",
            data: { id_cliente: idCliente },
            dataType: "json",
            success: function(data) {
                select.empty();
                select.append('<option value="">Sin contacto específico</option>');
                
                if (data.data && Array.isArray(data.data) && data.data.length > 0) {
                    data.data.forEach(function(contacto) {
                        var nombreCompleto = contacto.nombre_contacto_cliente;
                        if (contacto.apellidos_contacto_cliente) {
                            nombreCompleto += ' ' + contacto.apellidos_contacto_cliente;
                        }
                        if (contacto.cargo_contacto_cliente) {
                            nombreCompleto += ' (' + contacto.cargo_contacto_cliente + ')';
                        }
                        
                        var option = $('<option></option>')
                            .val(contacto.id_contacto_cliente)
                            .text(nombreCompleto);
                        
                        // Marcar como seleccionado si es el contacto principal o el especificado
                        if (idContactoSeleccionado && contacto.id_contacto_cliente == idContactoSeleccionado) {
                            option.prop('selected', true);
                        } else if (!idContactoSeleccionado && contacto.principal_contacto_cliente == 1) {
                            option.prop('selected', true);
                        }
                        
                        select.append(option);
                    });
                    select.prop('disabled', false);
                    
                    // Disparar evento change para actualizar la información del contacto
                    // Dispara siempre que haya un contacto seleccionado (principal o específico)
                    var contactoSeleccionado = select.val();
                    if (contactoSeleccionado) {
                        select.trigger('change');
                    }
                } else {
                    select.append('<option value="">No hay contactos disponibles</option>');
                    select.prop('disabled', true);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar contactos:', error);
                select.empty();
                select.append('<option value="">Error al cargar contactos</option>');
                select.prop('disabled', true);
                toastr.warning('No se pudieron cargar los contactos del cliente');
            }
        });
    }

    // Función para cargar y mostrar información del cliente
    function cargarInfoCliente(idCliente) {
        if (!idCliente) {
            // Ocultar información si no hay cliente seleccionado
            $('#info-direccion-cliente').hide();
            return;
        }
        
        $.ajax({
            url: "../../controller/cliente.php?op=mostrar",
            type: "POST",
            data: { id_cliente: idCliente },
            dataType: "json",
            success: function(data) {
                if (data) {
                    // Mostrar los datos del cliente
                    $('#nif_cliente_info').text(data.nif_cliente || '-');
                    $('#direccion_cliente_info').text(data.direccion_cliente || '-');
                    $('#cp_cliente_info').text(data.cp_cliente || '-');
                    $('#poblacion_cliente_info').text(data.poblacion_cliente || '-');
                    $('#provincia_cliente_info').text(data.provincia_cliente || '-');
                    
                    // Mostrar el panel de información
                    $('#info-direccion-cliente').slideDown();
                } else {
                    $('#info-direccion-cliente').hide();
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar información del cliente:', error);
                $('#info-direccion-cliente').hide();
            }
        });
    }

    // Función para cargar la forma de pago habitual del cliente
    function cargarFormaPagoHabitualCliente(idCliente) {
        $.ajax({
            url: "../../controller/cliente.php?op=mostrar",
            type: "POST",
            data: { id_cliente: idCliente },
            dataType: "json",
            success: function(data) {
                if (data && data.id_forma_pago_habitual) {
                    $('#id_forma_pago').val(data.id_forma_pago_habitual);
                    console.log('✓ Forma de pago habitual cargada:', data.id_forma_pago_habitual);
                } else {
                    console.log('ℹ Cliente sin forma de pago habitual configurada');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar forma de pago habitual:', error);
            }
        });
    }

    // Función para cargar y mostrar información del contacto
    function cargarInfoContacto(idContacto) {
        if (!idContacto) {
            // Ocultar información si no hay contacto seleccionado
            $('#info-contacto-cliente').hide();
            return;
        }
        
        $.ajax({
            url: "../../controller/clientes_contacto.php?op=mostrar",
            type: "POST",
            data: { id_contacto_cliente: idContacto },
            dataType: "json",
            success: function(data) {
                if (data) {
                    // Mostrar los datos del contacto
                    $('#cargo_contacto_info').text(data.cargo_contacto_cliente || '-');
                    $('#departamento_contacto_info').text(data.departamento_contacto_cliente || '-');
                    $('#telefono_contacto_info').text(data.telefono_contacto_cliente || '-');
                    $('#movil_contacto_info').text(data.movil_contacto_cliente || '-');
                    $('#email_contacto_info').text(data.email_contacto_cliente || '-');
                    
                    // Mostrar el panel de información
                    $('#info-contacto-cliente').slideDown();
                } else {
                    $('#info-contacto-cliente').hide();
                }
            },
            error: function(error) {
                console.error('Error al cargar información del contacto:', error);
                $('#info-contacto-cliente').hide();
            }
        });
    }

    // Event listener para cambio de cliente
    $('#id_cliente').on('change', function() {
        var idCliente = $(this).val();
        cargarContactosCliente(idCliente);
        cargarInfoCliente(idCliente);
        // Limpiar información del contacto al cambiar de cliente
        $('#info-contacto-cliente').hide();
        
        // Cargar forma de pago habitual del cliente
        if (idCliente) {
            cargarFormaPagoHabitualCliente(idCliente);
            $('#btnNuevoContacto').removeClass('btn-disabled').attr('data-enabled', 'true');
        } else {
            $('#id_forma_pago').val('');
            $('#btnNuevoContacto, #btnEditarContacto').addClass('btn-disabled').attr('data-enabled', 'false');
        }
    });
    
    // Event listener para cambio de contacto
    $('#id_contacto_cliente').on('change', function() {
        var idContacto = $(this).val();
        cargarInfoContacto(idContacto);
        
        // Habilitar/deshabilitar botón de editar contacto
        if (idContacto) {
            $('#btnEditarContacto').removeClass('btn-disabled').attr('data-enabled', 'true');
        } else {
            $('#btnEditarContacto').addClass('btn-disabled').attr('data-enabled', 'false');
        }
    });

    // Función para cargar datos de presupuesto para edición
    function cargarDatosPresupuesto(idPresupuesto) {
        console.log('Cargando datos de presupuesto ID:', idPresupuesto);
        
        $.ajax({
            url: "../../controller/presupuesto.php?op=mostrar",
            type: "POST",
            data: { id_presupuesto: idPresupuesto },
            dataType: "json",
            success: function(data) {
                try {
                    if (!data || typeof data !== 'object') {
                        throw new Error('Respuesta del servidor no válida');
                    }

                    console.log('Datos recibidos:', data);

                    // Llenar los campos del formulario
                    $('#id_presupuesto').val(data.id_presupuesto);
                    $('#numero_presupuesto').val(data.numero_presupuesto);
                    $('#fecha_presupuesto').val(data.fecha_presupuesto);
                    $('#fecha_validez_presupuesto').val(data.fecha_validez_presupuesto);
                    $('#fecha_inicio_evento_presupuesto').val(data.fecha_inicio_evento_presupuesto);
                    $('#fecha_fin_evento_presupuesto').val(data.fecha_fin_evento_presupuesto);
                    $('#numero_pedido_cliente_presupuesto').val(data.numero_pedido_cliente_presupuesto);
                    $('#nombre_evento_presupuesto').val(data.nombre_evento_presupuesto);
                    $('#direccion_evento_presupuesto').val(data.direccion_evento_presupuesto);
                    $('#poblacion_evento_presupuesto').val(data.poblacion_evento_presupuesto);
                    $('#cp_evento_presupuesto').val(data.cp_evento_presupuesto);
                    $('#provincia_evento_presupuesto').val(data.provincia_evento_presupuesto);
                    $('#observaciones_cabecera_presupuesto').val(data.observaciones_cabecera_presupuesto);
                    $('#observaciones_cabecera_ingles_presupuesto').val(data.observaciones_cabecera_ingles_presupuesto);
                    $('#observaciones_pie_presupuesto').val(data.observaciones_pie_presupuesto);
                    $('#observaciones_pie_ingles_presupuesto').val(data.observaciones_pie_ingles_presupuesto);
                    $('#observaciones_internas_presupuesto').val(data.observaciones_internas_presupuesto);
                    
                    // Checkboxes
                    $('#mostrar_obs_familias_presupuesto').prop('checked', data.mostrar_obs_familias_presupuesto == 1);
                    $('#mostrar_obs_articulos_presupuesto').prop('checked', data.mostrar_obs_articulos_presupuesto == 1);
                    $('#activo_presupuesto').prop('checked', data.activo_presupuesto == 1);
                    
                    // Selects - esperar a que se carguen primero
                    setTimeout(function() {
                        $('#id_cliente').val(data.id_cliente);
                        $('#id_estado_ppto').val(data.id_estado_ppto);
                        $('#id_forma_pago').val(data.id_forma_pago);
                        $('#id_metodo').val(data.id_metodo);
                        
                        // Cargar contactos del cliente y seleccionar el contacto si existe
                        if (data.id_cliente) {
                            cargarContactosCliente(data.id_cliente, data.id_contacto_cliente);
                            cargarInfoCliente(data.id_cliente);
                            // Cargar información del contacto si existe
                            if (data.id_contacto_cliente) {
                                setTimeout(function() {
                                    cargarInfoContacto(data.id_contacto_cliente);
                                }, 800);
                            }
                        }
                    }, 500);
                    
                    // Actualizar texto del estado
                    if (data.activo_presupuesto == 1) {
                        $('#estado-text').text('Presupuesto Activo').removeClass('text-danger').addClass('text-success');
                    } else {
                        $('#estado-text').text('Presupuesto Inactivo').removeClass('text-success').addClass('text-danger');
                    }
                    
                    // Capturar valores originales después de cargar datos
                    setTimeout(function() {
                        captureOriginalValues();
                    }, 600);
                    
                    // Enfocar el primer campo
                    $('#numero_presupuesto').focus();
                    
                } catch (e) {
                    console.error('Error al procesar datos:', e);
                    toastr.error('Error al cargar datos para edición');
                    setTimeout(() => {
                        window.location.href = 'index.php';
                    }, 2000);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error en la solicitud AJAX:', status, error);
                console.error('Respuesta del servidor:', xhr.responseText);
                toastr.error('Error al obtener datos del presupuesto');
                setTimeout(() => {
                    window.location.href = 'index.php';
                }, 2000);
            }
        });
    }

    // Verificar si estamos en modo edición
    const idPresupuesto = getUrlParameter('id');
    const modo = getUrlParameter('modo') || 'nuevo';
    
    if (modo === 'editar' && idPresupuesto) {
        // Actualizar títulos
        $('#page-title').text('Editar Presupuesto');
        $('#breadcrumb-title').text('Editar Presupuesto');
        $('#btnSalvarPresupuesto').html('<i class="fas fa-save me-2"></i>Actualizar Presupuesto');
        
        cargarDatosPresupuesto(idPresupuesto);
    } else {
        // En modo nuevo, establecer fecha actual
        var hoy = new Date().toISOString().split('T')[0];
        $('#fecha_presupuesto').val(hoy);
        
        // Calcular y establecer fecha de validez automáticamente DESPUÉS de cargar los días
        cargarDiasValidezEmpresa().then(function() {
            var fechaValidez = calcularFechaValidez(hoy);
            $('#fecha_validez_presupuesto').val(fechaValidez);
            console.log('✓ Fecha de validez calculada automáticamente:', fechaValidez, '(+' + diasValidezPresupuesto + ' días)');
        });
        
        $('#numero_presupuesto').focus();
        setTimeout(function() {
            captureOriginalValues();
        }, 100);
    }

    /////////////////////////////////////////
    //   FIN FUNCIONES DE INICIALIZACIÓN  //
    ///////////////////////////////////////

    //*****************************************************/
    //   CAPTURAR EL CLICK EN EL BOTÓN DE SALVAR PRESUPUESTO
    //*****************************************************/

    $(document).on('click', '#btnSalvarPresupuesto', function (event) {
        event.preventDefault();

        // Obtener valores del formulario
        var id_presupuestoR = $('#id_presupuesto').val().trim();
        var numero_presupuestoR = $('#numero_presupuesto').val().trim();
        var id_clienteR = $('#id_cliente').val();
        var id_contacto_clienteR = $('#id_contacto_cliente').val() || null;
        var id_estado_pptoR = $('#id_estado_ppto').val();
        var id_forma_pagoR = $('#id_forma_pago').val() || null;
        var id_metodoR = $('#id_metodo').val() || null;
        var fecha_presupuestoR = $('#fecha_presupuesto').val();
        var fecha_validez_presupuestoR = $('#fecha_validez_presupuesto').val() || null;
        var fecha_inicio_evento_presupuestoR = $('#fecha_inicio_evento_presupuesto').val() || null;
        var fecha_fin_evento_presupuestoR = $('#fecha_fin_evento_presupuesto').val() || null;
        var numero_pedido_cliente_presupuestoR = $('#numero_pedido_cliente_presupuesto').val() || '';
        var nombre_evento_presupuestoR = $('#nombre_evento_presupuesto').val() || '';
        var direccion_evento_presupuestoR = $('#direccion_evento_presupuesto').val() || '';
        var poblacion_evento_presupuestoR = $('#poblacion_evento_presupuesto').val() || '';
        var cp_evento_presupuestoR = $('#cp_evento_presupuesto').val() || '';
        var provincia_evento_presupuestoR = $('#provincia_evento_presupuesto').val() || '';
        var observaciones_cabecera_presupuestoR = $('#observaciones_cabecera_presupuesto').val() || '';
        var observaciones_cabecera_ingles_presupuestoR = $('#observaciones_cabecera_ingles_presupuesto').val() || '';
        var observaciones_pie_presupuestoR = $('#observaciones_pie_presupuesto').val() || '';
        var observaciones_pie_ingles_presupuestoR = $('#observaciones_pie_ingles_presupuesto').val() || '';
        var mostrar_obs_familias_presupuestoR = $('#mostrar_obs_familias_presupuesto').is(':checked') ? 1 : 0;
        var mostrar_obs_articulos_presupuestoR = $('#mostrar_obs_articulos_presupuesto').is(':checked') ? 1 : 0;
        var observaciones_internas_presupuestoR = $('#observaciones_internas_presupuesto').val() || '';
        
        var activo_presupuestoR;
        if (id_presupuestoR) {
            activo_presupuestoR = $('#activo_presupuesto').is(':checked') ? 1 : 0;
        } else {
            activo_presupuestoR = 1;
        }

        // Validar el formulario
        if (!formValidator.validateForm(event)) {
            toastr.error('Por favor, corrija los errores en el formulario.', 'Error de Validación');
            return;
        }

        // Validar fechas
        if (!validarFechaValidez()) {
            return;
        }
        
        if (!validarFechasEvento()) {
            return;
        }
        
        // Verificar presupuesto primero
        verificarPresupuestoExistente(
            id_presupuestoR,
            numero_presupuestoR,
            id_clienteR,
            id_contacto_clienteR,
            id_estado_pptoR,
            id_forma_pagoR,
            id_metodoR,
            fecha_presupuestoR,
            fecha_validez_presupuestoR,
            fecha_inicio_evento_presupuestoR,
            fecha_fin_evento_presupuestoR,
            numero_pedido_cliente_presupuestoR,
            nombre_evento_presupuestoR,
            direccion_evento_presupuestoR,
            poblacion_evento_presupuestoR,
            cp_evento_presupuestoR,
            provincia_evento_presupuestoR,
            observaciones_cabecera_presupuestoR,
            observaciones_cabecera_ingles_presupuestoR,
            observaciones_pie_presupuestoR,
            observaciones_pie_ingles_presupuestoR,
            mostrar_obs_familias_presupuestoR,
            mostrar_obs_articulos_presupuestoR,
            observaciones_internas_presupuestoR,
            activo_presupuestoR
        );
    });

    function verificarPresupuestoExistente(
        id_presupuesto,
        numero_presupuesto,
        id_cliente,
        id_contacto_cliente,
        id_estado_ppto,
        id_forma_pago,
        id_metodo,
        fecha_presupuesto,
        fecha_validez_presupuesto,
        fecha_inicio_evento_presupuesto,
        fecha_fin_evento_presupuesto,
        numero_pedido_cliente_presupuesto,
        nombre_evento_presupuesto,
        direccion_evento_presupuesto,
        poblacion_evento_presupuesto,
        cp_evento_presupuesto,
        provincia_evento_presupuesto,
        observaciones_cabecera_presupuesto,
        observaciones_cabecera_ingles_presupuesto,
        observaciones_pie_presupuesto,
        observaciones_pie_ingles_presupuesto,
        mostrar_obs_familias_presupuesto,
        mostrar_obs_articulos_presupuesto,
        observaciones_internas_presupuesto,
        activo_presupuesto
    ) {
        $.ajax({
            url: "../../controller/presupuesto.php?op=verificar",
            type: "POST",
            data: { 
                numero_presupuesto: numero_presupuesto,
                id_presupuesto: id_presupuesto 
            },
            dataType: "json",
            success: function(response) {
                console.log('Respuesta verificación:', response);
                
                if (response.existe) {
                    mostrarErrorPresupuestoExistente(numero_presupuesto);
                } else {
                    guardarPresupuesto(
                        id_presupuesto,
                        numero_presupuesto,
                        id_cliente,
                        id_contacto_cliente,
                        id_estado_ppto,
                        id_forma_pago,
                        id_metodo,
                        fecha_presupuesto,
                        fecha_validez_presupuesto,
                        fecha_inicio_evento_presupuesto,
                        fecha_fin_evento_presupuesto,
                        numero_pedido_cliente_presupuesto,
                        nombre_evento_presupuesto,
                        direccion_evento_presupuesto,
                        poblacion_evento_presupuesto,
                        cp_evento_presupuesto,
                        provincia_evento_presupuesto,
                        observaciones_cabecera_presupuesto,
                        observaciones_cabecera_ingles_presupuesto,
                        observaciones_pie_presupuesto,
                        observaciones_pie_ingles_presupuesto,
                        mostrar_obs_familias_presupuesto,
                        mostrar_obs_articulos_presupuesto,
                        observaciones_internas_presupuesto,
                        activo_presupuesto
                    );
                }
            },
            error: function(xhr, status, error) {
                console.error('Error en verificación:', error);
                toastr.error('Error al verificar el presupuesto. Intente nuevamente.', 'Error');
            }
        });
    }

    function mostrarErrorPresupuestoExistente(numero_presupuesto) {
        console.log("Presupuesto duplicado detectado:", numero_presupuesto);
        Swal.fire({
            title: 'Número de presupuesto duplicado',
            text: 'El presupuesto "' + numero_presupuesto + '" ya existe. Por favor, elija otro número.',
            icon: 'warning',
            confirmButtonText: 'Entendido'
        });
    }

    function guardarPresupuesto(
        id_presupuesto,
        numero_presupuesto,
        id_cliente,
        id_contacto_cliente,
        id_estado_ppto,
        id_forma_pago,
        id_metodo,
        fecha_presupuesto,
        fecha_validez_presupuesto,
        fecha_inicio_evento_presupuesto,
        fecha_fin_evento_presupuesto,
        numero_pedido_cliente_presupuesto,
        nombre_evento_presupuesto,
        direccion_evento_presupuesto,
        poblacion_evento_presupuesto,
        cp_evento_presupuesto,
        provincia_evento_presupuesto,
        observaciones_cabecera_presupuesto,
        observaciones_cabecera_ingles_presupuesto,
        observaciones_pie_presupuesto,
        observaciones_pie_ingles_presupuesto,
        mostrar_obs_familias_presupuesto,
        mostrar_obs_articulos_presupuesto,
        observaciones_internas_presupuesto,
        activo_presupuesto
    ) {
        // Mostrar indicador de carga
        $('#btnSalvarPresupuesto').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Guardando...');
        
        // Crear datos para enviar
        var formData = {
            'numero_presupuesto': numero_presupuesto,
            'id_cliente': id_cliente,
            'id_contacto_cliente': id_contacto_cliente,
            'id_estado_ppto': id_estado_ppto,
            'id_forma_pago': id_forma_pago,
            'id_metodo': id_metodo,
            'fecha_presupuesto': fecha_presupuesto,
            'fecha_validez_presupuesto': fecha_validez_presupuesto,
            'fecha_inicio_evento_presupuesto': fecha_inicio_evento_presupuesto,
            'fecha_fin_evento_presupuesto': fecha_fin_evento_presupuesto,
            'numero_pedido_cliente_presupuesto': numero_pedido_cliente_presupuesto,
            'nombre_evento_presupuesto': nombre_evento_presupuesto,
            'direccion_evento_presupuesto': direccion_evento_presupuesto,
            'poblacion_evento_presupuesto': poblacion_evento_presupuesto,
            'cp_evento_presupuesto': cp_evento_presupuesto,
            'provincia_evento_presupuesto': provincia_evento_presupuesto,
            'observaciones_cabecera_presupuesto': observaciones_cabecera_presupuesto,
            'observaciones_cabecera_ingles_presupuesto': observaciones_cabecera_ingles_presupuesto,
            'observaciones_pie_presupuesto': observaciones_pie_presupuesto,
            'observaciones_pie_ingles_presupuesto': observaciones_pie_ingles_presupuesto,
            'mostrar_obs_familias_presupuesto': mostrar_obs_familias_presupuesto,
            'mostrar_obs_articulos_presupuesto': mostrar_obs_articulos_presupuesto,
            'observaciones_internas_presupuesto': observaciones_internas_presupuesto,
            'activo_presupuesto': activo_presupuesto
        };
        
        if (id_presupuesto) {
            formData['id_presupuesto'] = id_presupuesto;
        }

        $.ajax({
            url: "../../controller/presupuesto.php?op=guardaryeditar",
            type: "POST",
            data: formData,
            dataType: "json",
            success: function(res) {
                if (res.success) {
                    formSaved = true;
                    
                    toastr.success(res.message || "Presupuesto guardado correctamente");
                    
                    setTimeout(() => {
                        window.location.href = 'index.php';
                    }, 1500);
                } else {
                    toastr.error(res.message || "Error al guardar el presupuesto");
                    $('#btnSalvarPresupuesto').prop('disabled', false).html('<i class="fas fa-save me-2"></i>Guardar Presupuesto');
                }
            },
            error: function(xhr, status, error) {
                console.error("Error en guardado:", error);
                Swal.fire('Error', 'No se pudo guardar el presupuesto. Error: ' + error, 'error');
                $('#btnSalvarPresupuesto').prop('disabled', false).html('<i class="fas fa-save me-2"></i>Guardar Presupuesto');
            }
        });
    }

    /////////////////////////////////////////
    //   FUNCIONES DE UTILIDAD            //
    ///////////////////////////////////////

    var formOriginalValues = {};
    var formSaved = false;
    
    function captureOriginalValues() {
        formOriginalValues = {
            numero_presupuesto: $('#numero_presupuesto').val(),
            id_cliente: $('#id_cliente').val(),
            fecha_presupuesto: $('#fecha_presupuesto').val(),
            nombre_evento_presupuesto: $('#nombre_evento_presupuesto').val()
        };
    }
    
    function hasFormChanged() {
        return (
            $('#numero_presupuesto').val() !== formOriginalValues.numero_presupuesto ||
            $('#id_cliente').val() !== formOriginalValues.id_cliente ||
            $('#fecha_presupuesto').val() !== formOriginalValues.fecha_presupuesto ||
            $('#nombre_evento_presupuesto').val() !== formOriginalValues.nombre_evento_presupuesto
        );
    }
    
    window.addEventListener('beforeunload', function (e) {
        if (!formSaved && hasFormChanged()) {
            e.preventDefault();
            e.returnValue = '';
        }
    });

    function markFormAsSaved() {
        formSaved = true;
    }

    /////////////////////////////////////////
    //   GESTIÓN DE CONTACTOS RÁPIDOS     //
    ///////////////////////////////////////

    console.log('Registrando event handlers de botones...');
    console.log('btnNuevoContacto encontrado:', $('#btnNuevoContacto').length);
    console.log('btnEditarContacto encontrado:', $('#btnEditarContacto').length);
    console.log('btnNuevoContacto disabled:', $('#btnNuevoContacto').prop('disabled'));
    console.log('btnEditarContacto disabled:', $('#btnEditarContacto').prop('disabled'));

    // Abrir modal para nuevo contacto
    $('#btnNuevoContacto').on('click', function(e) {
        e.preventDefault();
        console.log('Click en btnNuevoContacto');
        
        var idCliente = $('#id_cliente').val();
        console.log('ID Cliente:', idCliente);
        
        if (!idCliente) {
            toastr.warning('Debe seleccionar un cliente primero');
            return;
        }
        
        $('#tituloModalContacto').text('Nuevo Contacto');
        $('#formContactoRapido')[0].reset();
        $('#id_contacto_cliente_modal').val('');
        $('#id_cliente_modal').val(idCliente);
        $('#principal_contacto_cliente_modal').prop('checked', false);
        $('#modalContactoRapido').modal('show');
    });

    // Abrir modal para editar contacto
    $('#btnEditarContacto').on('click', function(e) {
        e.preventDefault();
        console.log('Click en btnEditarContacto');
        
        var idContacto = $('#id_contacto_cliente').val();
        console.log('ID Contacto:', idContacto);
        
        if (!idContacto) {
            toastr.warning('Debe seleccionar un contacto primero');
            return;
        }
        
        $('#tituloModalContacto').text('Editar Contacto');
        
        // Cargar datos del contacto
        $.ajax({
            url: '../../controller/clientes_contacto.php?op=mostrar',
            type: 'POST',
            data: { id_contacto_cliente: idContacto },
            dataType: 'json',
            success: function(data) {
                if (data) {
                    $('#id_contacto_cliente_modal').val(data.id_contacto_cliente);
                    $('#id_cliente_modal').val(data.id_cliente);
                    $('#nombre_contacto_cliente_modal').val(data.nombre_contacto_cliente || '');
                    $('#apellidos_contacto_cliente_modal').val(data.apellidos_contacto_cliente || '');
                    $('#cargo_contacto_cliente_modal').val(data.cargo_contacto_cliente || '');
                    $('#departamento_contacto_cliente_modal').val(data.departamento_contacto_cliente || '');
                    $('#telefono_contacto_cliente_modal').val(data.telefono_contacto_cliente || '');
                    $('#movil_contacto_cliente_modal').val(data.movil_contacto_cliente || '');
                    $('#email_contacto_cliente_modal').val(data.email_contacto_cliente || '');
                    $('#principal_contacto_cliente_modal').prop('checked', data.principal_contacto_cliente == 1);
                    $('#modalContactoRapido').modal('show');
                } else {
                    toastr.error('No se pudo cargar la información del contacto');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar contacto:', error);
                toastr.error('Error de comunicación con el servidor');
            }
        });
    });
    
    console.log('Event handlers de botones registrados correctamente');
    
    // ALTERNATIVA: Event delegation para asegurar que funcione
    $(document).on('click', '#btnNuevoContacto', function(e) {
        e.preventDefault();
        e.stopPropagation();
        console.log('Click en btnNuevoContacto (delegación)');
        
        // Verificar si el botón está habilitado
        if ($(this).attr('data-enabled') !== 'true') {
            console.log('Botón deshabilitado, ignorando click');
            return false;
        }
        
        var idCliente = $('#id_cliente').val();
        console.log('ID Cliente (delegación):', idCliente);
        
        if (!idCliente) {
            toastr.warning('Debe seleccionar un cliente primero');
            return false;
        }
        
        $('#tituloModalContacto').text('Nuevo Contacto');
        $('#formContactoRapido')[0].reset();
        $('#id_contacto_cliente_modal').val('');
        $('#id_cliente_modal').val(idCliente);
        $('#principal_contacto_cliente_modal').prop('checked', false);
        $('#modalContactoRapido').modal('show');
        
        return false;
    });
    
    $(document).on('click', '#btnEditarContacto', function(e) {
        e.preventDefault();
        e.stopPropagation();
        console.log('Click en btnEditarContacto (delegación)');
        
        // Verificar si el botón está habilitado
        if ($(this).attr('data-enabled') !== 'true') {
            console.log('Botón deshabilitado, ignorando click');
            return false;
        }
        
        var idContacto = $('#id_contacto_cliente').val();
        console.log('ID Contacto (delegación):', idContacto);
        
        if (!idContacto) {
            toastr.warning('Debe seleccionar un contacto primero');
            return false;
        }
        
        $('#tituloModalContacto').text('Editar Contacto');
        
        // Cargar datos del contacto
        $.ajax({
            url: '../../controller/clientes_contacto.php?op=mostrar',
            type: 'POST',
            data: { id_contacto_cliente: idContacto },
            dataType: 'json',
            success: function(data) {
                if (data) {
                    $('#id_contacto_cliente_modal').val(data.id_contacto_cliente);
                    $('#id_cliente_modal').val(data.id_cliente);
                    $('#nombre_contacto_cliente_modal').val(data.nombre_contacto_cliente || '');
                    $('#apellidos_contacto_cliente_modal').val(data.apellidos_contacto_cliente || '');
                    $('#cargo_contacto_cliente_modal').val(data.cargo_contacto_cliente || '');
                    $('#departamento_contacto_cliente_modal').val(data.departamento_contacto_cliente || '');
                    $('#telefono_contacto_cliente_modal').val(data.telefono_contacto_cliente || '');
                    $('#movil_contacto_cliente_modal').val(data.movil_contacto_cliente || '');
                    $('#email_contacto_cliente_modal').val(data.email_contacto_cliente || '');
                    $('#principal_contacto_cliente_modal').prop('checked', data.principal_contacto_cliente == 1);
                    $('#modalContactoRapido').modal('show');
                } else {
                    toastr.error('No se pudo cargar la información del contacto');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar contacto:', error);
                toastr.error('Error de comunicación con el servidor');
            }
        });
        
        return false;
    });

    // Guardar contacto desde el modal
    $('#btnGuardarContactoRapido').on('click', function(e) {
        e.preventDefault();
        console.log('Click en btnGuardarContactoRapido');
        
        var form = $('#formContactoRapido')[0];
        
        // Validar formulario
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }
        
        var idContacto = $('#id_contacto_cliente_modal').val();
        console.log('ID Contacto a guardar:', idContacto);
        
        // Preparar datos del formulario
        var formData = new FormData(form);
        formData.append('activo_contacto_cliente', '1');
        
        // Si no está marcado como principal, enviar 0
        if (!$('#principal_contacto_cliente_modal').prop('checked')) {
            formData.set('principal_contacto_cliente', '0');
        }
        
        // Deshabilitar botón mientras se guarda
        var btnGuardar = $('#btnGuardarContactoRapido');
        var textoOriginal = btnGuardar.html();
        btnGuardar.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Guardando...');
        
        console.log('Enviando petición AJAX a guardaryeditar');
        
        $.ajax({
            url: '../../controller/clientes_contacto.php?op=guardaryeditar',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                btnGuardar.prop('disabled', false).html(textoOriginal);
                
                if (response.success || response.status === 'success') {
                    var mensaje = idContacto ? 'Contacto actualizado correctamente' : 'Contacto creado correctamente';
                    toastr.success(mensaje);
                    $('#modalContactoRapido').modal('hide');
                    
                    // Obtener el ID del contacto guardado
                    var idContactoGuardado = response.id_contacto_cliente || response.id || idContacto;
                    
                    console.log('ID del contacto guardado:', idContactoGuardado);
                    
                    // Recargar lista de contactos del cliente (el trigger change cargará la info)
                    var idCliente = $('#id_cliente').val();
                    cargarContactosCliente(idCliente, idContactoGuardado);
                } else {
                    toastr.error(response.message || 'Error al guardar el contacto');
                }
            },
            error: function(xhr, status, error) {
                btnGuardar.prop('disabled', false).html(textoOriginal);
                console.error('Error al guardar contacto:', error);
                console.error('Status:', status);
                console.error('XHR Status:', xhr.status);
                console.error('Response Text:', xhr.responseText);
                console.error('FormData entries:');
                for (var pair of formData.entries()) {
                    console.log(pair[0] + ': ' + pair[1]);
                }
                toastr.error('Error de comunicación con el servidor. Ver consola para detalles.');
            }
        });
    });

    /////////////////////////////////////////
    //   FIN FUNCIONES DE UTILIDAD        //
    ///////////////////////////////////////

}); // de document.ready
