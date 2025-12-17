$(document).ready(function () {

    /////////////////////////////////////
    //     FORMATEO DE CAMPOS          //
    ///////////////////////////////////

    var formValidator = new FormValidator('formDocumento', {
        titulo_documento: {
            required: true
        },
        id_tipo_documento_documento: {
            required: true
        }
    });

    /////////////////////////////////////////
    //   FUNCIONES DE INICIALIZACIÓN      //
    ///////////////////////////////////////

    // Cargar tipos de documento
    function cargarTiposDocumento() {
        $.ajax({
            url: "../../controller/tipodocumento.php?op=listar_disponibles",
            type: "GET",
            dataType: "json",
            success: function(data) {
                let options = '<option value="">Seleccionar tipo...</option>';
                if (data.data && Array.isArray(data.data)) {
                    data.data.forEach(function(tipo) {
                        options += `<option value="${tipo.id_tipo_documento}">${tipo.nombre_tipo_documento}</option>`;
                    });
                }
                $('#id_tipo_documento_documento').html(options);
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar tipos de documento:', error);
                toastr.error('Error al cargar tipos de documento');
            }
        });
    }

    // Función para obtener parámetros de la URL
    function getUrlParameter(name) {
        name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
        var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
        var results = regex.exec(location.search);
        return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
    }

    // Obtener parámetros de la URL
    const modo = getUrlParameter('modo') || 'nuevo';
    const idDocumento = getUrlParameter('id');
    
    console.log('=== Inicio de formularioDocumento.js ===');
    console.log('Modo:', modo);
    console.log('ID Documento:', idDocumento);
    console.log('======================================');

    // Cargar tipos de documento
    cargarTiposDocumento();

    // Función para cargar datos de documento para edición
    function cargarDatosDocumento(idDocumento) {
        console.log('Cargando datos de documento ID:', idDocumento);
        
        $.ajax({
            url: "../../controller/documento.php?op=mostrar",
            type: "POST",
            data: { id_documento: idDocumento },
            dataType: "json",
            success: function(data) {
                try {
                    if (!data || typeof data !== 'object') {
                        throw new Error('Respuesta del servidor no válida');
                    }

                    console.log('Datos recibidos:', data);

                    // Llenar los campos del formulario
                    $('#id_documento').val(data.id_documento);
                    $('#titulo_documento').val(data.titulo_documento);
                    $('#descripcion_documento').val(data.descripcion_documento || '');
                    $('#fecha_publicacion_documento').val(data.fecha_publicacion_documento || '');
                    
                    // Esperar a que se carguen los tipos de documento y luego seleccionar el correcto
                    setTimeout(function() {
                        $('#id_tipo_documento_documento').val(data.id_tipo_documento_documento);
                    }, 500);
                    
                    // Configurar archivo actual
                    if (data.ruta_documento) {
                        $('#ruta_actual').val(data.ruta_documento);
                        showExistingFile(data.ruta_documento);
                        $('#ruta_documento').removeAttr('required');
                    }
                    
                    // Configurar el switch de estado
                    $('#activo_documento').prop('checked', data.activo_documento == 1);
                    
                    // Actualizar texto del estado según el valor actual
                    if (data.activo_documento == 1) {
                        $('#estado-text').text('Documento Activo').removeClass('text-danger').addClass('text-success');
                    } else {
                        $('#estado-text').text('Documento Inactivo').removeClass('text-success').addClass('text-danger');
                    }
                    
                    // Capturar valores originales después de cargar datos
                    setTimeout(function() {
                        captureOriginalValues();
                    }, 100);
                    
                    // Enfocar el primer campo
                    $('#titulo_documento').focus();
                    
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
                toastr.error('Error al obtener datos del documento');
                setTimeout(() => {
                    window.location.href = 'index.php';
                }, 2000);
            }
        });
    }

    // Verificar si estamos en modo edición
    if (modo === 'editar' && idDocumento) {
        cargarDatosDocumento(idDocumento);
    } else {
        setTimeout(function() {
            captureOriginalValues();
        }, 100);
    }

    /////////////////////////////////////////
    //   FIN FUNCIONES DE INICIALIZACIÓN  //
    ///////////////////////////////////////

    //*****************************************************/
    //   CAPTURAR EL CLICK EN EL BOTÓN DE SALVAR DOCUMENTO
    //*****************************************************/

    $(document).on('click', '#btnSalvarDocumento', function (event) {
        console.log('=== CLICK EN BOTÓN GUARDAR ===');
        event.preventDefault();

        // Obtener valores del formulario
        var id_documentoR = ($('#id_documento').val() || '').trim();
        var titulo_documentoR = ($('#titulo_documento').val() || '').trim();
        var descripcion_documentoR = ($('#descripcion_documento').val() || '').trim();
        var id_tipo_documento_documentoR = $('#id_tipo_documento_documento').val();
        var fecha_publicacion_documentoR = $('#fecha_publicacion_documento').val() || null;
        
        // El estado siempre será activo para nuevos documentos
        var activo_documentoR;
        if (id_documentoR) {
            activo_documentoR = $('#activo_documento').is(':checked') ? 1 : 0;
        } else {
            activo_documentoR = 1;
        }

        console.log('Valores capturados:');
        console.log('- titulo:', titulo_documentoR);
        console.log('- tipo_documento:', id_tipo_documento_documentoR);
        
        // Validar campos obligatorios
        if (!titulo_documentoR) {
            toastr.error('El título es obligatorio', 'Error de Validación');
            $('#titulo_documento').addClass('is-invalid');
            return;
        }

        if (!id_tipo_documento_documentoR) {
            toastr.error('Debe seleccionar un tipo de documento', 'Error de Validación');
            $('#id_tipo_documento_documento').addClass('is-invalid');
            return;
        }

        // Validar archivo (solo obligatorio en modo nuevo)
        const archivoInput = $('#ruta_documento')[0];
        const archivoFile = archivoInput.files && archivoInput.files.length > 0 ? archivoInput.files[0] : null;
        
        if (!id_documentoR && !archivoFile) {
            toastr.error('Debe seleccionar un archivo para el documento', 'Error de Validación');
            $('#ruta_documento').addClass('is-invalid');
            return;
        }
        
        // Validar tamaño del archivo
        if (archivoFile && archivoFile.size > 10 * 1024 * 1024) {
            toastr.error('El archivo es demasiado grande. Máximo 10MB.', 'Error de Validación');
            $('#ruta_documento').addClass('is-invalid');
            return;
        }
        
        // Validar tipo de archivo
        if (archivoFile) {
            const tiposPermitidos = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
            if (!tiposPermitidos.includes(archivoFile.type)) {
                toastr.error('Formato de archivo no válido. Use PDF, DOC o DOCX.', 'Error de Validación');
                $('#ruta_documento').addClass('is-invalid');
                return;
            }
        }

        // Validar el formulario
        var isValid = formValidator.validateForm(event);
        
        if (!isValid) {
            toastr.error('Por favor, corrija los errores en el formulario.', 'Error de Validación');
            return;
        }
        
        // Guardar documento
        guardarDocumento(id_documentoR, titulo_documentoR, descripcion_documentoR, id_tipo_documento_documentoR, fecha_publicacion_documentoR, activo_documentoR);
    });

    function guardarDocumento(id_documento, titulo_documento, descripcion_documento, id_tipo_documento_documento, fecha_publicacion_documento, activo_documento) {
        console.log('=== FUNCIÓN GUARDAR DOCUMENTO ===');
        
        // Mostrar indicador de carga
        $('#btnSalvarDocumento').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Guardando...');
        
        // Crear FormData para manejar archivos
        var formData = new FormData();
        formData.append('titulo_documento', titulo_documento);
        formData.append('descripcion_documento', descripcion_documento);
        formData.append('id_tipo_documento_documento', id_tipo_documento_documento);
        formData.append('fecha_publicacion_documento', fecha_publicacion_documento);
        formData.append('activo_documento', activo_documento);
        
        if (id_documento) {
            formData.append('id_documento', id_documento);
            const rutaActual = $('#ruta_actual').val();
            formData.append('ruta_actual', rutaActual);
        }
        
        // Agregar archivo si se seleccionó uno
        const archivoInput = $('#ruta_documento')[0];
        const archivoFile = archivoInput && archivoInput.files && archivoInput.files.length > 0 ? archivoInput.files[0] : null;
        
        if (archivoFile) {
            formData.append('ruta_documento', archivoFile, archivoFile.name);
        }

        $.ajax({
            url: "../../controller/documento.php?op=guardaryeditar",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            dataType: "json",
            success: function(res) {
                if (res.success) {
                    formSaved = true;
                    toastr.success(res.message || "Documento guardado correctamente");
                    setTimeout(() => {
                        window.location.href = 'index.php';
                    }, 1500);
                } else {
                    toastr.error(res.message || "Error al guardar el documento");
                    $('#btnSalvarDocumento').prop('disabled', false).html('<i class="fas fa-save me-2"></i>Guardar Documento');
                }
            },
            error: function(xhr, status, error) {
                console.error("Error en guardado:", error);
                Swal.fire('Error', 'No se pudo guardar el documento. Error: ' + error, 'error');
                $('#btnSalvarDocumento').prop('disabled', false).html('<i class="fas fa-save me-2"></i>Guardar Documento');
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
            titulo_documento: $('#titulo_documento').val(),
            descripcion_documento: $('#descripcion_documento').val(),
            id_tipo_documento_documento: $('#id_tipo_documento_documento').val(),
            fecha_publicacion_documento: $('#fecha_publicacion_documento').val(),
            ruta_actual: $('#ruta_actual').val()
        };
    }
    
    function hasFormChanged() {
        const hasNewFile = $('#ruta_documento')[0].files && $('#ruta_documento')[0].files.length > 0;
        
        return (
            $('#titulo_documento').val() !== formOriginalValues.titulo_documento ||
            $('#descripcion_documento').val() !== formOriginalValues.descripcion_documento ||
            $('#id_tipo_documento_documento').val() !== formOriginalValues.id_tipo_documento_documento ||
            $('#fecha_publicacion_documento').val() !== formOriginalValues.fecha_publicacion_documento ||
            hasNewFile
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

}); // de document.ready

// Función para mostrar vista previa por defecto
function showDefaultFilePreview() {
    const filePreview = document.getElementById('file-preview');
    if (filePreview) {
        filePreview.innerHTML = `
            <i class="fas fa-file text-muted" style="font-size: 3rem;"></i>
            <p class="text-muted mt-2 mb-0">Sin archivo</p>
        `;
    }
}

// Función para mostrar archivo existente
function showExistingFile(filePath) {
    const filePreview = document.getElementById('file-preview');
    if (filePreview && filePath) {
        const fileName = filePath.split('/').pop();
        const fileExt = fileName.split('.').pop().toUpperCase();
        filePreview.innerHTML = `
            <i class="fas fa-file-${fileExt.toLowerCase() === 'pdf' ? 'pdf' : 'word'} text-primary" style="font-size: 3rem;"></i>
            <p class="text-muted mt-2 mb-0">Archivo actual:</p>
            <p class="text-muted small mb-0">${fileName}</p>
            <a href="../../public/img/documentos/${filePath}" target="_blank" class="btn btn-sm btn-outline-primary mt-2">
                <i class="bi bi-eye me-1"></i>Ver documento
            </a>
        `;
    }
}
