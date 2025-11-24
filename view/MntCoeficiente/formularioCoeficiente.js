$(document).ready(function () {
    console.log("Formulario de Coeficiente Reductor cargado");

    // ===============================================
    // CONFIGURACIÓN INICIAL Y VARIABLES GLOBALES
    // ===============================================
    let isEditMode = false;
    let currentCoeficienteId = null;

    // ===============================================
    // DETECTAR MODO DE EDICIÓN
    // ===============================================
    function detectarModoEdicion() {
        const urlParams = new URLSearchParams(window.location.search);
        const id = urlParams.get('id');
        const modo = urlParams.get('modo');
        
        if (modo === 'editar' && id) {
            isEditMode = true;
            currentCoeficienteId = id;
            document.getElementById('id_coeficiente').value = id;
            document.getElementById('page-title').textContent = 'Editar Coeficiente Reductor';
            document.getElementById('breadcrumb-title').textContent = 'Editar Coeficiente Reductor';
            cargarDatosCoeficiente(id);
        }
    }

    // ===============================================
    // CARGAR DATOS PARA EDICIÓN
    // ===============================================
    function cargarDatosCoeficiente(idCoeficiente) {
        console.log('Cargando datos de coeficiente:', idCoeficiente);
        
        $.ajax({
            url: "../../controller/coeficiente.php?op=mostrar",
            type: "POST",
            data: { id_coeficiente: idCoeficiente },
            dataType: "json",
            success: function(data) {
                console.log('Datos recibidos:', data);
                
                if (data && !data.error) {
                    // Cargar datos en el formulario
                    $('#jornadas_coeficiente').val(data.jornadas_coeficiente);
                    $('#valor_coeficiente').val(data.valor_coeficiente);
                    $('#observaciones_coeficiente').val(data.observaciones_coeficiente);
                    
                    // Configurar estado
                    const estadoCheckbox = document.getElementById('activo_coeficiente');
                    const estadoText = document.getElementById('estado-text');
                    if (data.activo_coeficiente == 1) {
                        estadoCheckbox.checked = true;
                        estadoText.textContent = 'Coeficiente Activo';
                    } else {
                        estadoCheckbox.checked = false;
                        estadoText.textContent = 'Coeficiente Inactivo';
                    }
                    
                    // Actualizar contador de caracteres
                    actualizarContadorCaracteres();
                    
                    // Actualizar vista previa y resumen
                    actualizarVistaPrevia();
                    actualizarResumenConfiguracion();
                    
                    // Validar campos cargados
                    validarCamposFormulario();
                    
                    toastr.success('Datos de coeficiente cargados correctamente');
                } else {
                    toastr.error(data.error || 'Error al cargar los datos del coeficiente');
                    setTimeout(() => {
                        window.location.href = 'index.php';
                    }, 2000);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error AJAX:', error);
                toastr.error('Error de conexión al cargar los datos');
                setTimeout(() => {
                    window.location.href = 'index.php';
                }, 2000);
            }
        });
    }

    // ===============================================
    // VALIDACIONES EN TIEMPO REAL
    // ===============================================
    
    // Validación del número de jornadas
    $('#jornadas_coeficiente').on('input', function() {
        const valor = parseInt($(this).val());
        
        if ($(this).val() !== '' && valor >= 1 && valor <= 9999) {
            $(this).removeClass('is-invalid').addClass('is-valid');
        } else {
            $(this).removeClass('is-valid').addClass('is-invalid');
        }
        
        // Verificar duplicados
        if ($(this).val() !== '' && valor >= 1) {
            verificarJornadasDuplicadas(valor);
        }
        
        // Re-validar días facturados cuando cambien las jornadas
        $('#valor_coeficiente').trigger('input');
        
        actualizarVistaPrevia();
        actualizarResumenConfiguracion();
    });
    
    // Validación del valor del coeficiente (días a facturar)
    $('#valor_coeficiente').on('input', function() {
        const valor = parseFloat($(this).val());
        const jornadas = parseFloat($('#jornadas_coeficiente').val());
        
        if ($(this).val() !== '' && valor >= 0 && valor <= 9999.99) {
            // Validación adicional: normalmente los días facturados deben ser <= días alquilados
            if (jornadas && valor > jornadas) {
                $(this).removeClass('is-valid').addClass('is-invalid');
                $(this).next('.invalid-feedback').text('Los días a facturar no deberían exceder los días alquilados');
            } else {
                $(this).removeClass('is-invalid').addClass('is-valid');
            }
        } else {
            $(this).removeClass('is-valid').addClass('is-invalid');
        }
        
        actualizarVistaPrevia();
        actualizarResumenConfiguracion();
    });
    
    // Validación de observaciones
    $('#observaciones_coeficiente').on('input', function() {
        const valor = $(this).val();
        
        if (valor.length <= 500) {
            $(this).removeClass('is-invalid');
            if (valor.length > 0) {
                $(this).addClass('is-valid');
            }
        } else {
            $(this).removeClass('is-valid').addClass('is-invalid');
        }
        
        actualizarContadorCaracteres();
    });

    // ===============================================
    // VERIFICAR JORNADAS DUPLICADAS
    // ===============================================
    function verificarJornadasDuplicadas(jornadas) {
        const idActual = currentCoeficienteId || '';
        
        $.ajax({
            url: "../../controller/coeficiente.php?op=verificarJornadas",
            type: "POST",
            data: { 
                jornadas_coeficiente: jornadas,
                id_coeficiente: idActual
            },
            dataType: "json",
            success: function(response) {
                const jornadasInput = $('#jornadas_coeficiente');
                
                if (response.existe) {
                    jornadasInput.removeClass('is-valid').addClass('is-invalid');
                    jornadasInput.next('.invalid-feedback').text('Ya existe un coeficiente para estas jornadas. Debe ser único.');
                } else {
                    if (jornadas >= 1) {
                        jornadasInput.removeClass('is-invalid').addClass('is-valid');
                    }
                }
            },
            error: function() {
                console.error('Error al verificar jornadas duplicadas');
            }
        });
    }

    // ===============================================
    // ACTUALIZAR VISTA PREVIA
    // ===============================================
    function actualizarVistaPrevia() {
        const jornadas = $('#jornadas_coeficiente').val();
        const valor = $('#valor_coeficiente').val();
        const preview = $('#preview-coeficiente');
        
        if (jornadas && valor) {
            const jornadasBadge = `<span class="badge bg-primary fs-6 me-2">${jornadas} días alquilados</span>`;
            const valorBadge = `<span class="badge bg-success fs-6">${parseFloat(valor).toFixed(2)} días facturados</span>`;
            const descripcion = `<br><small class="text-muted">Descuento de ${(parseFloat(jornadas) - parseFloat(valor)).toFixed(2)} días</small>`;
            
            preview.html(jornadasBadge + valorBadge + descripcion);
        } else {
            preview.html(`
                <span class="badge bg-primary fs-6 me-2">-</span>
                <span class="badge bg-success fs-6">-</span>
                <br><small class="text-muted">Configurar jornadas y días a facturar</small>
            `);
        }
    }

    // ===============================================
    // ACTUALIZAR CONTADOR DE CARACTERES
    // ===============================================
    function actualizarContadorCaracteres() {
        const observaciones = $('#observaciones_coeficiente').val();
        const contador = $('#char-count');
        const longitud = observaciones.length;
        
        contador.text(longitud);
        
        if (longitud > 450) {
            contador.addClass('text-warning');
        } else {
            contador.removeClass('text-warning text-danger');
        }
        
        if (longitud > 490) {
            contador.removeClass('text-warning').addClass('text-danger');
        }
    }

    // ===============================================
    // ACTUALIZAR RESUMEN DE CONFIGURACIÓN
    // ===============================================
    function actualizarResumenConfiguracion() {
        const jornadas = $('#jornadas_coeficiente').val();
        const valor = $('#valor_coeficiente').val();
        
        // Actualizar aplicación del descuento
        const resumenAplicacion = $('#resumen-aplicacion');
        if (jornadas && valor) {
            const descuento = (parseFloat(jornadas) - parseFloat(valor)).toFixed(2);
            const porcentajeDescuento = ((parseFloat(descuento) / parseFloat(jornadas)) * 100).toFixed(1);
            const color = parseFloat(descuento) > 0 ? 'text-success' : 'text-warning';
            
            resumenAplicacion.html(`
                <p class="fw-bold mb-1">${jornadas} días alquilados → ${parseFloat(valor).toFixed(2)} días facturados</p>
                <small class="${color}">
                    Descuento: ${descuento} días (${porcentajeDescuento}% menos)
                </small>
            `);
        } else {
            resumenAplicacion.html(`
                <p class="fw-bold mb-1">Configuración pendiente</p>
                <small class="text-muted">Complete los campos para ver el resumen</small>
            `);
        }
        
        // Actualizar ejemplo de facturación
        const resumenCalculo = $('#resumen-calculo');
        if (valor) {
            const totalFacturado = (parseFloat(valor) * 100).toFixed(2);
            const totalOriginal = jornadas ? (parseFloat(jornadas) * 100).toFixed(2) : '-';
            const ahorro = jornadas ? ((parseFloat(jornadas) - parseFloat(valor)) * 100).toFixed(2) : '-';
            
            resumenCalculo.html(`
                <p class="fw-bold mb-1">Días facturados × Precio = Total</p>
                <small class="text-muted">${parseFloat(valor).toFixed(2)} días × 100€ = ${totalFacturado}€</small>
                ${jornadas ? `<br><small class="text-success">Ahorro: ${ahorro}€ (vs ${totalOriginal}€ original)</small>` : ''}
            `);
        } else {
            resumenCalculo.html(`
                <p class="fw-bold mb-1">Días facturados × Precio = Total</p>
                <small class="text-muted">- días × 100€ = -€</small>
            `);
        }
    }

    // ===============================================
    // VALIDAR CAMPOS DEL FORMULARIO
    // ===============================================
    function validarCamposFormulario() {
        const jornadas = $('#jornadas_coeficiente').val();
        const valor = $('#valor_coeficiente').val();
        const botonGuardar = $('#btnSalvarCoeficiente');
        
        // Por defecto, el botón está habilitado
        let esValido = true;
        
        // Solo deshabilitar si hay campos con valores inválidos
        if (jornadas && (parseInt(jornadas) < 1 || parseInt(jornadas) > 9999)) {
            esValido = false;
        }
        
        if (valor && (parseFloat(valor) < 0 || parseFloat(valor) > 9999.99)) {
            esValido = false;
        }
        
        // Validación adicional: días facturados normalmente deben ser <= días alquilados
        if (jornadas && valor && parseFloat(valor) > parseFloat(jornadas)) {
            // Permitimos pero marcamos como advertencia
            console.log('Advertencia: Días facturados exceden días alquilados');
        }
        
        // Habilitar/deshabilitar botón de guardar
        if (esValido) {
            botonGuardar.prop('disabled', false).removeClass('btn-secondary').addClass('btn-primary');
        } else {
            botonGuardar.prop('disabled', true).removeClass('btn-primary').addClass('btn-secondary');
        }
        
        return esValido;
    }

    // ===============================================
    // VALIDACIÓN ANTES DE ENVIAR
    // ===============================================
    function validarFormulario() {
        let errores = [];
        
        // Validar jornadas
        const jornadas = $('#jornadas_coeficiente').val();
        if (!jornadas || parseInt(jornadas) < 1 || parseInt(jornadas) > 9999) {
            errores.push('Las jornadas deben ser un número entre 1 y 9999');
        }
        
        // Validar valor
        const valor = $('#valor_coeficiente').val();
        if (!valor || parseFloat(valor) < 0 || parseFloat(valor) > 9999.99) {
            errores.push('Los días a facturar deben ser un número entre 0 y 9999.99');
        }
        
        // Validar observaciones
        const observaciones = $('#observaciones_coeficiente').val();
        if (observaciones.length > 500) {
            errores.push('Las observaciones no pueden exceder 500 caracteres');
        }
        
        return errores;
    }

    // ===============================================
    // RECOPILAR DATOS DEL FORMULARIO
    // ===============================================
    function recopilarDatosFormulario() {
        const formData = new FormData();
        
        // Campos obligatorios
        formData.append('jornadas_coeficiente', $('#jornadas_coeficiente').val());
        formData.append('valor_coeficiente', $('#valor_coeficiente').val());
        
        // Campo opcional
        const observaciones = $('#observaciones_coeficiente').val().trim();
        formData.append('observaciones_coeficiente', observaciones);
        
        // El estado siempre es activo (1) para nuevos coeficientes
        formData.append('activo_coeficiente', '1');
        
        // ID para edición
        if (isEditMode && currentCoeficienteId) {
            formData.append('id_coeficiente', currentCoeficienteId);
        }
        
        return formData;
    }

    // ===============================================
    // GUARDAR COEFICIENTE
    // ===============================================
    function guardarCoeficiente() {
        console.log('Iniciando guardado de coeficiente...');
        
        // Validar formulario
        const errores = validarFormulario();
        if (errores.length > 0) {
            toastr.error('Por favor, corrija los siguientes errores:\n• ' + errores.join('\n• '));
            return;
        }
        
        // Recopilar datos
        const formData = recopilarDatosFormulario();
        
        // Mostrar loading
        const btnGuardar = $('#btnSalvarCoeficiente');
        const textoOriginal = btnGuardar.html();
        btnGuardar.prop('disabled', true)
                  .html('<i class="fas fa-spinner fa-spin me-2"></i>Guardando...');
        
        // Determinar la operación
        const operacion = isEditMode ? 'guardaryeditar' : 'guardaryeditar';
        
        $.ajax({
            url: `../../controller/coeficiente.php?op=${operacion}`,
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            dataType: "json",
            success: function(data) {
                console.log('Respuesta del servidor:', data);
                
                if (data.success || data.status === 'success') {
                    const mensaje = isEditMode ? 
                        'Coeficiente actualizado correctamente' : 
                        'Coeficiente creado correctamente';
                        
                    toastr.success(mensaje);
                    
                    // Redirigir después de un breve delay
                    setTimeout(() => {
                        window.location.href = 'index.php';
                    }, 1500);
                } else {
                    // Mostrar error específico
                    const mensajeError = data.message || data.error || 'Error al procesar la solicitud';
                    toastr.error(mensajeError);
                    
                    // Restaurar botón
                    btnGuardar.prop('disabled', false).html(textoOriginal);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error AJAX:', {xhr, status, error});
                toastr.error('Error de conexión. Intente nuevamente.');
                
                // Restaurar botón
                btnGuardar.prop('disabled', false).html(textoOriginal);
            }
        });
    }

    // ===============================================
    // EVENTOS
    // ===============================================
    
    // Botón guardar
    $('#btnSalvarCoeficiente').on('click', function(e) {
        e.preventDefault();
        
        Swal.fire({
            title: isEditMode ? '¿Actualizar coeficiente?' : '¿Guardar coeficiente?',
            text: isEditMode ? 
                'Se actualizarán los datos de este coeficiente' : 
                'Se creará un nuevo coeficiente con los datos ingresados',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: isEditMode ? 'Sí, actualizar' : 'Sí, guardar',
            cancelButtonText: 'Cancelar',
            reverseButtons: true,
            customClass: {
                confirmButton: 'btn btn-primary me-3',
                cancelButton: 'btn btn-secondary'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                guardarCoeficiente();
            }
        });
    });
    
    // Tecla Enter en campos del formulario
    $('#formCoeficiente input, #formCoeficiente textarea').on('keypress', function(e) {
        if (e.which === 13) { // Enter
            e.preventDefault();
            $('#btnSalvarCoeficiente').click();
        }
    });

    // ===============================================
    // INICIALIZACIÓN
    // ===============================================
    function inicializar() {
        // Detectar modo de edición
        detectarModoEdicion();
        
        // Configurar validaciones iniciales
        actualizarContadorCaracteres();
        actualizarVistaPrevia();
        actualizarResumenConfiguracion();
        validarCamposFormulario();
        
        // Configurar título según el modo
        if (!isEditMode) {
            document.getElementById('page-title').textContent = 'Nuevo Coeficiente Reductor';
            document.getElementById('breadcrumb-title').textContent = 'Nuevo Coeficiente Reductor';
        }
        
        console.log('Formulario inicializado - Modo:', isEditMode ? 'Edición' : 'Nuevo');
    }

    // ===============================================
    // EJECUTAR INICIALIZACIÓN
    // ===============================================
    inicializar();

}); // Final de document.ready