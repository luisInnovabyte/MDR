$(document).ready(function () {
    console.log("Formulario de Estado de Presupuesto cargado");

    // ===============================================
    // CONFIGURACIÓN INICIAL Y VARIABLES GLOBALES
    // ===============================================
    let isEditMode = false;
    let currentEstadoPresupuestoId = null;

    // ===============================================
    // DETECTAR MODO DE EDICIÓN
    // ===============================================
    function detectarModoEdicion() {
        const urlParams = new URLSearchParams(window.location.search);
        const id = urlParams.get('id');
        const modo = urlParams.get('modo');
        
        if (modo === 'editar' && id) {
            isEditMode = true;
            currentEstadoPresupuestoId = id;
            document.getElementById('id_estado_ppto').value = id;
            cargarDatosEstadoPresupuesto(id);
        }
    }

    // ===============================================
    // CARGAR DATOS PARA EDICIÓN
    // ===============================================
    function cargarDatosEstadoPresupuesto(idEstado) {
        console.log('Cargando datos de estado de presupuesto:', idEstado);
        
        $.ajax({
            url: "../../controller/estado_presupuesto.php?op=mostrar",
            type: "POST",
            data: { id_estado_ppto: idEstado },
            dataType: "json",
            success: function(data) {
                console.log('Datos recibidos:', data);
                
                if (data && !data.error) {
                    // Cargar datos en el formulario
                    $('#codigo_estado_ppto').val(data.codigo_estado_ppto);
                    $('#nombre_estado_ppto').val(data.nombre_estado_ppto);
                    $('#color_estado_ppto').val(data.color_estado_ppto || '#007bff');
                    $('#color_estado_ppto_text').val(data.color_estado_ppto || '#007bff');
                    $('#orden_estado_ppto').val(data.orden_estado_ppto || '');
                    $('#observaciones_estado_ppto').val(data.observaciones_estado_ppto);
                    
                    // Configurar estado
                    const estadoCheckbox = document.getElementById('activo_estado_ppto');
                    const estadoText = document.getElementById('estado-text');
                    if (data.activo_estado_ppto == 1) {
                        estadoCheckbox.checked = true;
                        estadoText.textContent = 'Estado de Presupuesto Activo';
                    } else {
                        estadoCheckbox.checked = false;
                        estadoText.textContent = 'Estado de Presupuesto Inactivo';
                    }
                    
                    // Actualizar contador de caracteres
                    actualizarContadorCaracteres();
                    
                    // Actualizar resumen
                    actualizarResumenConfiguracion();
                    
                    // Validar campos cargados
                    validarCamposFormulario();
                    
                    toastr.success('Datos de estado de presupuesto cargados correctamente');
                } else {
                    toastr.error(data.error || 'Error al cargar los datos del estado de presupuesto');
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
    
    // Validación del código del estado
    $('#codigo_estado_ppto').on('input', function() {
        let valor = $(this).val().toUpperCase().replace(/\s+/g, '');
        $(this).val(valor);
        
        if (valor.length >= 2 && valor.length <= 20) {
            $(this).removeClass('is-invalid').addClass('is-valid');
        } else {
            $(this).removeClass('is-valid').addClass('is-invalid');
        }
        
        // Verificar duplicados
        if (valor.length >= 2) {
            verificarCodigoDuplicado(valor);
        }
    });
    
    // Validación del nombre del estado
    $('#nombre_estado_ppto').on('input', function() {
        const valor = $(this).val();
        
        if (valor.length >= 3 && valor.length <= 100) {
            $(this).removeClass('is-invalid').addClass('is-valid');
        } else {
            $(this).removeClass('is-valid').addClass('is-invalid');
        }
        
        actualizarResumenConfiguracion();
    });
    
    // Sincronización del color
    $('#color_estado_ppto').on('input', function() {
        const color = $(this).val();
        $('#color_estado_ppto_text').val(color);
        actualizarResumenConfiguracion();
    });
    
    // Validación del orden
    $('#orden_estado_ppto').on('input', function() {
        const valor = parseInt($(this).val());
        
        if ($(this).val() === '' || (valor >= 1 && valor <= 999)) {
            $(this).removeClass('is-invalid');
            if ($(this).val() !== '') {
                $(this).addClass('is-valid');
            }
        } else {
            $(this).removeClass('is-valid').addClass('is-invalid');
        }
        
        actualizarResumenConfiguracion();
    });
    
    // Validación de observaciones
    $('#observaciones_estado_ppto').on('input', function() {
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
    // VERIFICAR CÓDIGO DUPLICADO
    // ===============================================
    function verificarCodigoDuplicado(codigo) {
        const idActual = currentEstadoPresupuestoId || '';
        
        $.ajax({
            url: "../../controller/estado_presupuesto.php?op=verificarEstadoPresupuesto",
            type: "POST",
            data: { 
                codigo_estado_ppto: codigo,
                id_estado_ppto: idActual
            },
            dataType: "json",
            success: function(response) {
                const codigoInput = $('#codigo_estado_ppto');
                
                if (response.existe) {
                    codigoInput.removeClass('is-valid').addClass('is-invalid');
                    codigoInput.next('.invalid-feedback').text('Este código ya existe. Debe ser único.');
                } else {
                    if (codigo.length >= 2) {
                        codigoInput.removeClass('is-invalid').addClass('is-valid');
                    }
                }
            },
            error: function() {
                console.error('Error al verificar código duplicado');
            }
        });
    }

    // ===============================================
    // ACTUALIZAR CONTADOR DE CARACTERES
    // ===============================================
    function actualizarContadorCaracteres() {
        const observaciones = $('#observaciones_estado_ppto').val();
        const contador = $('#char-count');
        const longitud = observaciones.length;
        
        contador.text(longitud);
        
        if (longitud > 400) {
            contador.css('color', '#dc3545'); // Rojo
        } else if (longitud > 300) {
            contador.css('color', '#ffc107'); // Amarillo
        } else {
            contador.css('color', '#6c757d'); // Gris
        }
    }

    // ===============================================
    // ACTUALIZAR RESUMEN DE CONFIGURACIÓN
    // ===============================================
    function actualizarResumenConfiguracion() {
        const color = $('#color_estado_ppto').val();
        const orden = parseInt($('#orden_estado_ppto').val());
        
        // Actualizar visualización del color
        const resumenColor = $('#resumen-color');
        if (resumenColor.length) {
            resumenColor.html(`
                <div class="d-flex align-items-center">
                    <span class="badge me-3" style="background-color: ${color}; color: white; width: 30px; height: 30px; border-radius: 50%;"></span>
                    <span class="fw-bold">Color: ${color}</span>
                </div>
            `);
        }
        
        // Actualizar orden
        const resumenOrden = $('#resumen-orden');
        if (resumenOrden.length) {
            if (!isNaN(orden) && orden > 0) {
                resumenOrden.html(`<span class="badge bg-info fs-6">Posición ${orden} en las listas</span>`);
            } else {
                resumenOrden.html('<span class="badge bg-secondary fs-6">Sin orden específico</span>');
            }
        }
    }

    // ===============================================
    // VALIDAR TODOS LOS CAMPOS DEL FORMULARIO
    // ===============================================
    function validarCamposFormulario() {
        $('#codigo_estado_ppto').trigger('input');
        $('#nombre_estado_ppto').trigger('input');
        $('#color_estado_ppto').trigger('input');
        $('#orden_estado_ppto').trigger('input');
        $('#observaciones_estado_ppto').trigger('input');
    }

    // ===============================================
    // VALIDACIÓN ANTES DE ENVIAR
    // ===============================================
    function validarFormulario() {
        let errores = [];
        
        // Validar código
        const codigo = $('#codigo_estado_ppto').val().trim();
        if (codigo.length < 2 || codigo.length > 20) {
            errores.push('El código debe tener entre 2 y 20 caracteres');
        }
        
        // Validar nombre
        const nombre = $('#nombre_estado_ppto').val().trim();
        if (nombre.length < 3 || nombre.length > 100) {
            errores.push('El nombre debe tener entre 3 y 100 caracteres');
        }
        
        // Validar orden (opcional)
        const ordenVal = $('#orden_estado_ppto').val();
        if (ordenVal !== '') {
            const orden = parseInt(ordenVal);
            if (isNaN(orden) || orden < 1 || orden > 999) {
                errores.push('El orden debe ser un número entre 1 y 999');
            }
        }
        
        // Validar observaciones
        const observaciones = $('#observaciones_estado_ppto').val();
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
        formData.append('codigo_estado_ppto', $('#codigo_estado_ppto').val().trim());
        formData.append('nombre_estado_ppto', $('#nombre_estado_ppto').val().trim());
        
        // Campos opcionales
        const color = $('#color_estado_ppto').val();
        formData.append('color_estado_ppto', color || '#007bff');
        
        const orden = $('#orden_estado_ppto').val();
        if (orden && !isNaN(parseInt(orden))) {
            formData.append('orden_estado_ppto', parseInt(orden));
        } else {
            formData.append('orden_estado_ppto', '0');
        }
        
        const observaciones = $('#observaciones_estado_ppto').val().trim();
        formData.append('observaciones_estado_ppto', observaciones);
        
        // El estado siempre es activo (1) para nuevos estados de presupuesto
        formData.append('activo_estado_ppto', '1');
        
        // ID para edición
        if (isEditMode && currentEstadoPresupuestoId) {
            formData.append('id_estado_ppto', currentEstadoPresupuestoId);
        }
        
        return formData;
    }

    // ===============================================
    // GUARDAR ESTADO DE PRESUPUESTO
    // ===============================================
    function guardarEstadoPresupuesto() {
        console.log('Iniciando guardado de estado de presupuesto...');
        
        // Validar formulario
        const errores = validarFormulario();
        if (errores.length > 0) {
            toastr.error('Por favor, corrija los siguientes errores:\n• ' + errores.join('\n• '));
            return;
        }
        
        // Recopilar datos
        const formData = recopilarDatosFormulario();
        
        // Mostrar loading
        const btnGuardar = $('#btnSalvarEstadoPresupuesto');
        const textoOriginal = btnGuardar.html();
        btnGuardar.prop('disabled', true)
                  .html('<i class="fas fa-spinner fa-spin me-2"></i>Guardando...');
        
        // Determinar la operación
        const operacion = isEditMode ? 'guardaryeditar' : 'guardaryeditar';
        
        $.ajax({
            url: `../../controller/estado_presupuesto.php?op=${operacion}`,
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            dataType: "json",
            success: function(data) {
                console.log('Respuesta del servidor:', data);
                
                if (data.success || data.status === 'success') {
                    const mensaje = isEditMode ? 
                        'Estado de presupuesto actualizado correctamente' : 
                        'Estado de presupuesto creado correctamente';
                        
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
    $('#btnSalvarEstadoPresupuesto').on('click', function(e) {
        e.preventDefault();
        
        Swal.fire({
            title: isEditMode ? '¿Actualizar estado de presupuesto?' : '¿Guardar estado de presupuesto?',
            text: isEditMode ? 
                'Se actualizarán los datos de este estado de presupuesto' : 
                'Se creará un nuevo estado de presupuesto con los datos ingresados',
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
                guardarEstadoPresupuesto();
            }
        });
    });
    
    // Tecla Enter en campos del formulario
    $('#formEstadoPresupuesto input, #formEstadoPresupuesto textarea').on('keypress', function(e) {
        if (e.which === 13) { // Enter
            e.preventDefault();
            $('#btnSalvarEstadoPresupuesto').click();
        }
    });

    // ===============================================
    // INICIALIZACIÓN
    // ===============================================
    detectarModoEdicion();
    actualizarContadorCaracteres();
    actualizarResumenConfiguracion();
    
    console.log('Formulario de estado de presupuesto inicializado correctamente');
});

// ===============================================
// FUNCIONES AUXILIARES GLOBALES
// ===============================================

// Función global para limpiar formulario
function limpiarFormulario() {
    $('#formEstadoPresupuesto')[0].reset();
    $('#formEstadoPresupuesto .is-valid, #formEstadoPresupuesto .is-invalid').removeClass('is-valid is-invalid');
    $('#id_estado_ppto').val('');
    $('#char-count').text('0').css('color', '#6c757d');
    $('#color_estado_ppto').val('#007bff');
    $('#color_estado_ppto_text').val('#007bff');
    actualizarResumenConfiguracion();
}

// Función para mostrar vista previa por defecto (no aplica para estados de presupuesto)
function showDefaultImagePreview() {
    // No aplica para estados de presupuesto
}

// Función para mostrar imagen existente (no aplica para estados de presupuesto)
function showExistingImage() {
    // No aplica para estados de presupuesto
}