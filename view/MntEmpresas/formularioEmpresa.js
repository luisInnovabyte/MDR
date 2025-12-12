$(document).ready(function () {

    /////////////////////////////////////
    //     FORMATEO DE CAMPOS          //
    ///////////////////////////////////

    var formValidator = new FormValidator('formEmpresa', {
        codigo_empresa: {
            required: true
        },
        nombre_empresa: {
            required: true
        },
        nif_empresa: {
            required: true
        }
    });

    /////////////////////////////////////////
    //     FIN FORMATEO DE CAMPOS          //
    ////////////////////////////////////////

    /////////////////////////////////////////
    //   FUNCIONES DE INICIALIZACIÓN      //
    ///////////////////////////////////////

    // Función para obtener parámetros de la URL
    function getUrlParameter(name) {
        name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
        var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
        var results = regex.exec(location.search);
        return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
    }

    // Función para cargar datos de empresa para edición
    function cargarDatosEmpresa(idEmpresa) {
        console.log('Cargando datos de empresa ID:', idEmpresa);
        
        $.ajax({
            url: "../../controller/empresas.php?op=mostrar",
            type: "POST",
            data: { id_empresa: idEmpresa },
            dataType: "json",
            success: function(data) {
                try {
                    if (!data || typeof data !== 'object') {
                        throw new Error('Respuesta del servidor no válida');
                    }

                    console.log('Datos recibidos:', data);

                    // Identificación básica
                    $('#id_empresa').val(data.id_empresa);
                    $('#codigo_empresa').val(data.codigo_empresa);
                    $('#nombre_empresa').val(data.nombre_empresa);
                    $('#nombre_comercial_empresa').val(data.nombre_comercial_empresa);

                    // Tipo de empresa
                    $('#ficticia_empresa').prop('checked', data.ficticia_empresa == 1);
                    $('#empresa_ficticia_principal').prop('checked', data.empresa_ficticia_principal == 1);

                    // Datos fiscales
                    $('#nif_empresa').val(data.nif_empresa);
                    $('#direccion_fiscal_empresa').val(data.direccion_fiscal_empresa);
                    $('#cp_fiscal_empresa').val(data.cp_fiscal_empresa);
                    $('#poblacion_fiscal_empresa').val(data.poblacion_fiscal_empresa);
                    $('#provincia_fiscal_empresa').val(data.provincia_fiscal_empresa);
                    $('#pais_fiscal_empresa').val(data.pais_fiscal_empresa);

                    // Contacto
                    $('#telefono_empresa').val(data.telefono_empresa);
                    $('#movil_empresa').val(data.movil_empresa);
                    $('#email_empresa').val(data.email_empresa);
                    $('#email_facturacion_empresa').val(data.email_facturacion_empresa);
                    $('#web_empresa').val(data.web_empresa);

                    // Bancarios
                    $('#iban_empresa').val(data.iban_empresa);
                    $('#swift_empresa').val(data.swift_empresa);
                    $('#banco_empresa').val(data.banco_empresa);

                    // Series
                    $('#serie_presupuesto_empresa').val(data.serie_presupuesto_empresa);
                    $('#numero_actual_presupuesto_empresa').val(data.numero_actual_presupuesto_empresa);
                    $('#dias_validez_presupuesto_empresa').val(data.dias_validez_presupuesto_empresa || 30);
                    $('#serie_factura_empresa').val(data.serie_factura_empresa);
                    $('#numero_actual_factura_empresa').val(data.numero_actual_factura_empresa);
                    $('#serie_abono_empresa').val(data.serie_abono_empresa);
                    $('#numero_actual_abono_empresa').val(data.numero_actual_abono_empresa);

                    // VeriFactu
                    $('#verifactu_activo_empresa').prop('checked', data.verifactu_activo_empresa == 1);
                    $('#verifactu_sistema_empresa').val(data.verifactu_sistema_empresa || 'online');
                    $('#verifactu_nif_empresa').val(data.verifactu_nif_empresa);
                    $('#verifactu_nombre_empresa').val(data.verifactu_nombre_empresa);
                    $('#verifactu_nombre_comercial_empresa').val(data.verifactu_nombre_comercial_empresa);
                    $('#verifactu_id_software_empresa').val(data.verifactu_id_software_empresa);
                    $('#verifactu_nombre_software_empresa').val(data.verifactu_nombre_software_empresa);
                    $('#verifactu_version_software_empresa').val(data.verifactu_version_software_empresa);
                    $('#verifactu_numero_instalacion_empresa').val(data.verifactu_numero_instalacion_empresa);

                    // Logotipos y textos
                    $('#logotipo_empresa').val(data.logotipo_empresa);
                    $('#logotipo2_empresa').val(data.logotipo2_empresa);
                    $('#texto_legal_empresa').val(data.texto_legal_empresa);
                    $('#texto_pie_empresa').val(data.texto_pie_empresa);

                    // Estado activo
                    $('#activo_empresa_hidden').val(data.activo_empresa);
                    $('#activo_empresa_display').prop('checked', data.activo_empresa == 1);
                    
                    if (data.activo_empresa == 1) {
                        $('#estado_texto').text('Empresa Activa').removeClass('text-danger').addClass('text-success');
                        $('#estado_descripcion').text('Esta empresa está activa y aparecerá en las selecciones.');
                    } else {
                        $('#estado_texto').text('Empresa Inactiva').removeClass('text-success').addClass('text-danger');
                        $('#estado_descripcion').text('Esta empresa está inactiva. Para activarla use la opción desde la lista.');
                    }
                    
                    // Capturar valores originales después de cargar datos
                    setTimeout(function() {
                        captureOriginalValues();
                    }, 100);
                    
                    $('#codigo_empresa').focus();
                    toastr.success('Datos cargados correctamente', 'Éxito');
                    
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
                toastr.error('Error al obtener datos de la empresa');
                setTimeout(() => {
                    window.location.href = 'index.php';
                }, 2000);
            }
        });
    }

    // Verificar si estamos en modo edición
    const idEmpresa = getUrlParameter('id');
    const modo = getUrlParameter('modo') || 'nuevo';
    
    if (modo === 'editar' && idEmpresa) {
        cargarDatosEmpresa(idEmpresa);
    } else {
        $('#codigo_empresa').focus();
        setTimeout(function() {
            captureOriginalValues();
        }, 100);
    }

    /////////////////////////////////////////
    //   FIN FUNCIONES DE INICIALIZACIÓN  //
    ///////////////////////////////////////

    //*****************************************************/
    //   CAPTURAR EL CLICK EN EL BOTÓN DE SALVAR EMPRESA
    //*****************************************************/

    $(document).on('click', '#btnSalvarEmpresa', function (event) {
        event.preventDefault();

        // Validar el formulario
        if (!formValidator.validateForm(event)) {
            toastr.error('Por favor, corrija los errores en el formulario.', 'Error de Validación');
            return;
        }

        // Obtener valores del formulario
        var id_empresa = $('#id_empresa').val().trim();
        var codigo_empresa = $('#codigo_empresa').val().trim().toUpperCase();
        var nombre_empresa = $('#nombre_empresa').val().trim();
        var nombre_comercial_empresa = $('#nombre_comercial_empresa').val().trim();
        
        // Tipo de empresa
        var ficticia_empresa = $('#ficticia_empresa').is(':checked') ? '1' : '0';
        var empresa_ficticia_principal = $('#empresa_ficticia_principal').is(':checked') ? '1' : '0';
        
        // Datos fiscales
        var nif_empresa = $('#nif_empresa').val().trim().toUpperCase();
        var direccion_fiscal_empresa = $('#direccion_fiscal_empresa').val().trim();
        var cp_fiscal_empresa = $('#cp_fiscal_empresa').val().trim();
        var poblacion_fiscal_empresa = $('#poblacion_fiscal_empresa').val().trim();
        var provincia_fiscal_empresa = $('#provincia_fiscal_empresa').val().trim();
        var pais_fiscal_empresa = $('#pais_fiscal_empresa').val().trim();
        
        // Contacto
        var telefono_empresa = $('#telefono_empresa').val().trim();
        var movil_empresa = $('#movil_empresa').val().trim();
        var email_empresa = $('#email_empresa').val().trim();
        var email_facturacion_empresa = $('#email_facturacion_empresa').val().trim();
        var web_empresa = $('#web_empresa').val().trim();
        
        // Bancarios
        var iban_empresa = $('#iban_empresa').val().trim();
        var swift_empresa = $('#swift_empresa').val().trim();
        var banco_empresa = $('#banco_empresa').val().trim();
        
        // Series
        var serie_presupuesto_empresa = $('#serie_presupuesto_empresa').val().trim();
        var numero_actual_presupuesto_empresa = $('#numero_actual_presupuesto_empresa').val().trim();
        var dias_validez_presupuesto_empresa = $('#dias_validez_presupuesto_empresa').val().trim() || '30';
        var serie_factura_empresa = $('#serie_factura_empresa').val().trim();
        var numero_actual_factura_empresa = $('#numero_actual_factura_empresa').val().trim();
        var serie_abono_empresa = $('#serie_abono_empresa').val().trim();
        var numero_actual_abono_empresa = $('#numero_actual_abono_empresa').val().trim();
        
        // VeriFactu
        var verifactu_activo_empresa = $('#verifactu_activo_empresa').is(':checked') ? '1' : '0';
        var verifactu_sistema_empresa = $('#verifactu_sistema_empresa').val();
        var verifactu_nif_empresa = $('#verifactu_nif_empresa').val().trim();
        var verifactu_nombre_empresa = $('#verifactu_nombre_empresa').val().trim();
        var verifactu_nombre_comercial_empresa = $('#verifactu_nombre_comercial_empresa').val().trim();
        var verifactu_id_software_empresa = $('#verifactu_id_software_empresa').val().trim();
        var verifactu_nombre_software_empresa = $('#verifactu_nombre_software_empresa').val().trim();
        var verifactu_version_software_empresa = $('#verifactu_version_software_empresa').val().trim();
        var verifactu_numero_instalacion_empresa = $('#verifactu_numero_instalacion_empresa').val().trim();
        
        // Logotipos y textos
        var logotipo_empresa = $('#logotipo_empresa').val().trim();
        var logotipo2_empresa = $('#logotipo2_empresa').val().trim();
        var texto_legal_empresa = $('#texto_legal_empresa').val().trim();
        var texto_pie_empresa = $('#texto_pie_empresa').val().trim();
        
        // Estado
        var activo_empresa;
        if (id_empresa) {
            activo_empresa = $('#activo_empresa_hidden').val();
        } else {
            activo_empresa = 1;
        }
        
        // Verificar empresa primero
        verificarEmpresaExistente(
            id_empresa, codigo_empresa, nombre_empresa, nombre_comercial_empresa,
            ficticia_empresa, empresa_ficticia_principal,
            nif_empresa, direccion_fiscal_empresa, cp_fiscal_empresa, poblacion_fiscal_empresa, provincia_fiscal_empresa, pais_fiscal_empresa,
            telefono_empresa, movil_empresa, email_empresa, email_facturacion_empresa, web_empresa,
            iban_empresa, swift_empresa, banco_empresa,
            serie_presupuesto_empresa, numero_actual_presupuesto_empresa, dias_validez_presupuesto_empresa,
            serie_factura_empresa, numero_actual_factura_empresa,
            serie_abono_empresa, numero_actual_abono_empresa,
            verifactu_activo_empresa, verifactu_sistema_empresa, verifactu_nif_empresa,
            verifactu_nombre_empresa, verifactu_nombre_comercial_empresa,
            verifactu_id_software_empresa, verifactu_nombre_software_empresa,
            verifactu_version_software_empresa, verifactu_numero_instalacion_empresa,
            logotipo_empresa, logotipo2_empresa, texto_legal_empresa, texto_pie_empresa,
            activo_empresa
        );
    });

    function verificarEmpresaExistente(...params) {
        var id_empresa = params[0];
        var codigo_empresa = params[1];
        var nif_empresa = params[6];
        
        console.log('🔍 Verificando empresa:', { codigo: codigo_empresa, nif: nif_empresa, id: id_empresa });
        
        $.ajax({
            url: "../../controller/empresas.php?op=verificar",
            type: "POST",
            data: { 
                codigo_empresa: codigo_empresa,
                nif_empresa: nif_empresa,
                id_empresa: id_empresa || ''
            },
            dataType: "json",
            success: function(response) {
                console.log('📋 Respuesta verificación:', response);
                
                if (response.existe === false) {
                    console.log('✅ Empresa no existe, verificando empresa principal...');
                    
                    // Si marca empresa_ficticia_principal, validar que sea única
                    if (params[5] == '1') { // empresa_ficticia_principal
                        validarEmpresaPrincipalUnica(id_empresa, ...params);
                    } else {
                        guardarEmpresa(...params);
                    }
                } else {
                    console.log('❌ Empresa ya existe');
                    mostrarErrorEmpresaExistente("Ya existe una empresa con el código '" + codigo_empresa + "' o NIF '" + nif_empresa + "'");
                }
            },
            error: function(xhr, status, error) {
                console.error('Error en verificación:', error);
                console.error('Respuesta del servidor:', xhr.responseText);
                toastr.error('Error al verificar la empresa. Intente nuevamente.', 'Error');
            }
        });
    }

    function validarEmpresaPrincipalUnica(id_empresa, ...params) {
        $.ajax({
            url: "../../controller/empresas.php?op=validarEmpresaFicticia",
            type: "POST",
            data: { id_empresa: id_empresa || '' },
            dataType: "json",
            success: function(response) {
                console.log('Validación empresa principal:', response);

                if (response.existe_otra) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Empresa Principal Existente',
                        html: `Ya existe una empresa ficticia principal: <br><strong>${response.empresa_existente}</strong>`,
                        showCancelButton: true,
                        confirmButtonText: 'Continuar de todos modos',
                        cancelButtonText: 'Cancelar',
                        confirmButtonColor: '#d33',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            guardarEmpresa(...params);
                        }
                    });
                } else {
                    guardarEmpresa(...params);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error en validación:', error);
                toastr.error('Error al validar empresa principal', 'Error');
            }
        });
    }

    function mostrarErrorEmpresaExistente(mensaje) {
        console.log("Empresa duplicada detectada:", mensaje);
        Swal.fire({
            title: 'Empresa duplicada',
            text: mensaje,
            icon: 'warning',
            confirmButtonText: 'Entendido'
        });
    }

    function guardarEmpresa(...params) {
        // Mostrar indicador de carga
        $('#btnSalvarEmpresa').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Guardando...');
        
        // Preparar los datos para enviar
        const datosEnvio = {
            id_empresa: params[0],
            codigo_empresa: params[1],
            nombre_empresa: params[2],
            nombre_comercial_empresa: params[3],
            ficticia_empresa: params[4],
            empresa_ficticia_principal: params[5],
            nif_empresa: params[6],
            direccion_fiscal_empresa: params[7],
            cp_fiscal_empresa: params[8],
            poblacion_fiscal_empresa: params[9],
            provincia_fiscal_empresa: params[10],
            pais_fiscal_empresa: params[11],
            telefono_empresa: params[12],
            movil_empresa: params[13],
            email_empresa: params[14],
            email_facturacion_empresa: params[15],
            web_empresa: params[16],
            iban_empresa: params[17],
            swift_empresa: params[18],
            banco_empresa: params[19],
            serie_presupuesto_empresa: params[20],
            numero_actual_presupuesto_empresa: params[21],
            dias_validez_presupuesto_empresa: params[22],
            serie_factura_empresa: params[23],
            numero_actual_factura_empresa: params[24],
            serie_abono_empresa: params[25],
            numero_actual_abono_empresa: params[26],
            verifactu_activo_empresa: params[27],
            verifactu_sistema_empresa: params[28],
            verifactu_nif_empresa: params[29],
            verifactu_nombre_empresa: params[30],
            verifactu_nombre_comercial_empresa: params[31],
            verifactu_id_software_empresa: params[32],
            verifactu_nombre_software_empresa: params[33],
            verifactu_version_software_empresa: params[34],
            verifactu_numero_instalacion_empresa: params[35],
            logotipo_empresa: params[36],
            logotipo2_empresa: params[37],
            texto_legal_empresa: params[38],
            texto_pie_empresa: params[39],
            activo_empresa: params[40]
        };
        
        console.log('💾 Datos a guardar:', datosEnvio);
        
        $.ajax({
            url: "../../controller/empresas.php?op=guardaryeditar",
            type: "POST",
            data: datosEnvio,
            dataType: "json",
            success: function(res) {
                console.log('📋 Respuesta del guardado:', res);
                
                if (res.status === 'success' || res.success) {
                    formSaved = true;
                    toastr.success(res.message || "Empresa guardada correctamente");
                    setTimeout(() => {
                        window.location.href = 'index.php';
                    }, 1500);
                } else {
                    toastr.error(res.message || "Error al guardar la empresa");
                    $('#btnSalvarEmpresa').prop('disabled', false).html('<i class="fas fa-save me-2"></i>Guardar Empresa');
                }
            },
            error: function(xhr, status, error) {
                console.error("Error en guardado:", error);
                console.error("Estado:", status);
                console.error("Respuesta del servidor:", xhr.responseText);
                
                let errorMsg = 'No se pudo guardar la empresa.';
                try {
                    const response = JSON.parse(xhr.responseText);
                    errorMsg = response.message || errorMsg;
                } catch (e) {
                    errorMsg += ' Error: ' + error;
                }
                
                Swal.fire('Error', errorMsg, 'error');
                $('#btnSalvarEmpresa').prop('disabled', false).html('<i class="fas fa-save me-2"></i>Guardar Empresa');
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
            codigo_empresa: $('#codigo_empresa').val(),
            nombre_empresa: $('#nombre_empresa').val(),
            nif_empresa: $('#nif_empresa').val()
            // ... otros campos si es necesario
        };
    }
    
    function hasFormChanged() {
        return (
            $('#codigo_empresa').val() !== formOriginalValues.codigo_empresa ||
            $('#nombre_empresa').val() !== formOriginalValues.nombre_empresa ||
            $('#nif_empresa').val() !== formOriginalValues.nif_empresa
        );
    }
    
    window.addEventListener('beforeunload', function (e) {
        if (!formSaved && hasFormChanged()) {
            e.preventDefault();
            e.returnValue = '';
        }
    });

    // Auto-formato para algunos campos
    $('#codigo_empresa').on('input', function() {
        this.value = this.value.toUpperCase().replace(/\s+/g, '').slice(0, 20);
    });

    $('#nif_empresa, #verifactu_nif_empresa').on('input', function() {
        this.value = this.value.toUpperCase().replace(/\s+/g, '').slice(0, 20);
    });

    $('#cp_fiscal_empresa').on('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);
    });

    // Lógica de checkboxes relacionados
    $('#ficticia_empresa').on('change', function () {
        if (!$(this).is(':checked')) {
            $('#empresa_ficticia_principal').prop('checked', false);
            toastr.info('Una empresa real no puede ser ficticia principal', 'Información');
        }
    });

    $('#empresa_ficticia_principal').on('change', function () {
        if ($(this).is(':checked')) {
            $('#ficticia_empresa').prop('checked', true);
            toastr.info('Empresa ficticia principal activada', 'Información');
        }
    });

    // Validación VeriFactu
    $('#verifactu_activo_empresa').on('change', function () {
        if ($(this).is(':checked') && $('#ficticia_empresa').is(':checked')) {
            Swal.fire({
                icon: 'warning',
                title: 'Advertencia',
                text: 'VeriFactu está diseñado SOLO para empresas REALES que facturen',
                confirmButtonText: 'Entendido'
            });
        }
    });

    /////////////////////////////////////////
    //   FIN FUNCIONES DE UTILIDAD        //
    ///////////////////////////////////////

    /////////////////////////////////////////
    //   INICIALIZACIÓN DEL FORMULARIO    //
    ///////////////////////////////////////

    const urlParamsInit = new URLSearchParams(window.location.search);
    const idEmpresaInit = urlParamsInit.get('id');
    const modoInit = urlParamsInit.get('modo') || 'nuevo';
    
    if (modoInit === 'editar' && idEmpresaInit) {
        document.getElementById('page-title').textContent = 'Editar Empresa';
        document.getElementById('breadcrumb-title').textContent = 'Editar Empresa';
        document.getElementById('btnSalvarEmpresa').innerHTML = '<i class="fas fa-save me-2"></i>Actualizar Empresa';
        document.getElementById('id_empresa').value = idEmpresaInit;
        
        setTimeout(function() {
            cargarDatosEmpresa(idEmpresaInit);
        }, 500);
    } else {
        setTimeout(function() {
            captureOriginalValues();
        }, 100);
    }

}); // de document.ready
