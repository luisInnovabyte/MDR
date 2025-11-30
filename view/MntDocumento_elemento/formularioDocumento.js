$(document).ready(function () {

    /////////////////////////////////////
    //     FORMATEO DE CAMPOS          //
    ///////////////////////////////////

    var formValidator = new FormValidator('formDocumento', {
        id_elemento: {
            required: true
        },
        descripcion_documento_elemento: {
            required: true
        },
        tipo_documento_elemento: {
            required: true
        }
        // archivo_documento se valida manualmente porque es un input file
    });

    /////////////////////////////////////////
    //     FIN FORMATEO DE CAMPOS          //
    ////////////////////////////////////////

    /////////////////////////////////////////
    //   FUNCIONES DE INICIALIZACIÓN      //
    ///////////////////////////////////////

    // Cargar información del elemento seleccionado
    function cargarInfoElemento(idElemento) {
        if (!idElemento) {
            console.error('No se proporcionó ID de elemento');
            toastr.error('Debe seleccionar un elemento desde el listado', 'Error', {
                timeOut: 5000,
                positionClass: 'toast-top-right'
            });
            // Redirigir al index después de 2 segundos
            setTimeout(function() {
                window.location.href = 'index.php';
            }, 2000);
            return;
        }

        $.ajax({
            url: "../../controller/elemento.php?op=mostrar",
            type: "POST",
            data: { id_elemento: idElemento },
            dataType: "json",
            success: function(data) {
                if (data && data.id_elemento) {
                    console.log('Información del elemento cargada:', data);
                    
                    // Establecer el id_elemento en el campo oculto
                    $('#id_elemento').val(data.id_elemento);
                    
                    // Actualizar el banner con la información del elemento
                    $('#elemento-nombre').text(data.nombre_elemento || data.descripcion_elemento || 'Sin nombre');
                    $('#elemento-codigo').text(data.codigo_elemento || '--');
                    $('#elemento-id').text('ID: ' + data.id_elemento);
                    
                    // Mostrar mensaje de éxito
                    toastr.success('Elemento cargado: ' + (data.nombre_elemento || data.descripcion_elemento), 'Información', {
                        timeOut: 3000,
                        positionClass: 'toast-top-right'
                    });
                } else {
                    console.error('Elemento no encontrado o datos inválidos');
                    toastr.error('No se pudo cargar la información del elemento', 'Error');
                    setTimeout(function() {
                        window.location.href = 'index.php';
                    }, 2000);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar elemento:', error);
                toastr.error('Error al cargar la información del elemento', 'Error');
                setTimeout(function() {
                    window.location.href = 'index.php';
                }, 2000);
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
    const idElementoPreseleccionado = getUrlParameter('id_elemento');
    
    console.log('=== Inicio de formularioDocumento.js ===');
    console.log('Modo:', modo);
    console.log('ID Documento:', idDocumento);
    console.log('ID elemento de la URL:', idElementoPreseleccionado);
    console.log('======================================');
    
    if (idElementoPreseleccionado && modo === 'nuevo') {
        // Cargar información del elemento
        cargarInfoElemento(idElementoPreseleccionado);
    } else if (modo === 'nuevo') {
        // Si no hay id_elemento, mostrar error y redirigir
        toastr.warning('Debe seleccionar un elemento desde el listado', 'Advertencia', {
            timeOut: 3000,
            positionClass: 'toast-top-right'
        });
        setTimeout(function() {
            window.location.href = 'index.php';
        }, 2000);
    }

    // Función para cargar datos de documento para edición
    function cargarDatosDocumento(idDocumento) {
        console.log('Cargando datos de documento ID:', idDocumento);
        
        $.ajax({
            url: "../../controller/documento_elemento.php?op=mostrar",
            type: "POST",
            data: { id_documento_elemento: idDocumento },
            dataType: "json",
            success: function(data) {
                try {
                    if (!data || typeof data !== 'object') {
                        throw new Error('Respuesta del servidor no válida');
                    }

                    console.log('Datos recibidos:', data);

                    // Llenar los campos del formulario
                    $('#id_documento_elemento').val(data.id_documento_elemento);
                    $('#id_elemento').val(data.id_elemento);
                    
                    // Cargar información del elemento para mostrar en el banner
                    if (data.id_elemento) {
                        cargarInfoElemento(data.id_elemento);
                    }
                    
                    $('#descripcion_documento_elemento').val(data.descripcion_documento_elemento);
                    $('#tipo_documento_elemento').val(data.tipo_documento_elemento);
                    $('#observaciones_documento').val(data.observaciones_documento || '');
                    
                    // Configurar archivo actual
                    if (data.archivo_documento) {
                        $('#archivo_actual').val(data.archivo_documento);
                        // Mostrar archivo existente
                        showExistingFile(data.archivo_documento);
                        // En modo edición, el archivo no es obligatorio
                        $('#archivo_documento').removeAttr('required');
                    }
                    
                    // Configurar el switch de estado (mantener estado actual en edición)
                    $('#activo_documento_elemento').prop('checked', data.activo_documento_elemento == 1);
                    
                    // Configurar el checkbox de privacidad
                    $('#privado_documento').prop('checked', data.privado_documento == 1);
                    
                    // Actualizar texto del estado según el valor actual
                    if (data.activo_documento_elemento == 1) {
                        $('#estado-text').text('Documento Activo').removeClass('text-danger').addClass('text-success');
                    } else {
                        $('#estado-text').text('Documento Inactivo').removeClass('text-success').addClass('text-danger');
                    }
                    
                    // Actualizar texto de privacidad según el valor actual
                    if (data.privado_documento == 1) {
                        $('#privado-text').text('Documento Privado').removeClass('text-success').addClass('text-danger');
                    } else {
                        $('#privado-text').text('Documento Público').removeClass('text-danger').addClass('text-success');
                    }
                    
                    // Capturar valores originales después de cargar datos
                    setTimeout(function() {
                        captureOriginalValues();
                    }, 100);
                    
                    // Enfocar el primer campo
                    $('#id_elemento').focus();
                    
                } catch (e) {
                    console.error('Error al procesar datos:', e);
                    toastr.error('Error al cargar datos para edición');
                    // Redirigir al listado si hay error
                    setTimeout(() => {
                        window.location.href = 'index.php';
                    }, 2000);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error en la solicitud AJAX:', status, error);
                console.error('Respuesta del servidor:', xhr.responseText);
                toastr.error('Error al obtener datos del documento');
                // Redirigir al listado si hay error
                setTimeout(() => {
                    window.location.href = 'index.php';
                }, 2000);
            }
        });
    }

    // Verificar si estamos en modo edición (las variables ya están declaradas arriba)
    if (modo === 'editar' && idDocumento) {
        cargarDatosDocumento(idDocumento);
    } else {
        // En modo nuevo, enfocar el primer campo y capturar valores iniciales
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
        var id_documento_elementoR = ($('#id_documento_elemento').val() || '').trim();
        var id_elementoR = $('#id_elemento').val();
        var descripcion_documento_elementoR = ($('#descripcion_documento_elemento').val() || '').trim();
        var tipo_documento_elementoR = $('#tipo_documento_elemento').val();
        var observaciones_documentoR = ($('#observaciones_documento').val() || '').trim();
        
        // El estado siempre será activo para nuevos documentos, o mantener el actual para edición
        var activo_documento_elementoR;
        if (id_documento_elementoR) {
            // En edición: mantener el estado actual
            activo_documento_elementoR = $('#activo_documento_elemento').is(':checked') ? 1 : 0;
        } else {
            // Nuevo documento: siempre activo
            activo_documento_elementoR = 1;
        }
        
        // El privado se toma del checkbox
        var privado_documentoR = $('#privado_documento').is(':checked') ? 1 : 0;

        console.log('Valores capturados:');
        console.log('- id_elemento:', id_elementoR);
        console.log('- tipo_documento:', tipo_documento_elementoR);
        console.log('- descripcion:', descripcion_documento_elementoR);
        
        // Validar que existe un elemento asignado
        if (!id_elementoR) {
            console.error('Falta id_elemento');
            toastr.error('No se ha asignado un elemento. Debe acceder desde el listado de elementos.', 'Error de Validación');
            setTimeout(function() {
                window.location.href = 'index.php';
            }, 2000);
            return;
        }

        // Validar que se seleccionó un tipo
        if (!tipo_documento_elementoR) {
            console.error('Falta tipo de documento');
            toastr.error('Debe seleccionar un tipo de documento', 'Error de Validación');
            $('#tipo_documento_elemento').addClass('is-invalid');
            return;
        }

        // Validar archivo (solo obligatorio en modo nuevo)
        const archivoInput = $('#archivo_documento')[0];
        const archivoFile = archivoInput.files && archivoInput.files.length > 0 ? archivoInput.files[0] : null;
        console.log('=== VALIDACIÓN DE ARCHIVO ===');
        console.log('Input archivo existe?:', archivoInput !== null);
        console.log('Tiene files?:', archivoInput.files);
        console.log('Cantidad de files:', archivoInput.files ? archivoInput.files.length : 0);
        console.log('Archivo seleccionado:', archivoFile);
        console.log('Es nuevo documento?:', !id_documento_elementoR);
        console.log('===========================');
        
        if (!id_documento_elementoR && !archivoFile) {
            console.error('Falta archivo en modo nuevo');
            toastr.error('Debe seleccionar un archivo para el documento', 'Error de Validación');
            $('#archivo_documento').addClass('is-invalid');
            return;
        }
        
        // Validar tamaño del archivo
        if (archivoFile && archivoFile.size > 10 * 1024 * 1024) {
            console.error('Archivo demasiado grande:', archivoFile.size);
            toastr.error('El archivo es demasiado grande. Máximo 10MB.', 'Error de Validación');
            $('#archivo_documento').addClass('is-invalid');
            return;
        }
        
        // Validar tipo de archivo
        if (archivoFile) {
            const tiposPermitidos = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
            if (!tiposPermitidos.includes(archivoFile.type)) {
                console.error('Tipo de archivo no permitido:', archivoFile.type);
                toastr.error('Formato de archivo no válido. Use PDF, DOC o DOCX.', 'Error de Validación');
                $('#archivo_documento').addClass('is-invalid');
                return;
            }
        }

        // Validar el formulario
        console.log('Validando formulario...');
        var isValid = formValidator.validateForm(event);
        console.log('Resultado de validación:', isValid);
        
        if (!isValid) {
            console.error('Formulario no válido');
            toastr.error('Por favor, corrija los errores en el formulario.', 'Error de Validación');
            return;
        }
        
        console.log('Formulario válido, continuando...');
        
        // Guardar directamente (sin verificación de código)
        guardarDocumento(id_documento_elementoR, id_elementoR, descripcion_documento_elementoR, tipo_documento_elementoR, privado_documentoR, observaciones_documentoR, activo_documento_elementoR);
    });

    function guardarDocumento(id_documento_elemento, id_elemento, descripcion_documento_elemento, tipo_documento_elemento, privado_documento, observaciones_documento, activo_documento_elemento) {
        console.log('=== FUNCIÓN GUARDAR DOCUMENTO ===');
        
        // Mostrar indicador de carga
        $('#btnSalvarDocumento').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Guardando...');
        
        // Crear FormData para manejar archivos
        var formData = new FormData();
        formData.append('id_elemento', id_elemento);
        formData.append('descripcion_documento_elemento', descripcion_documento_elemento);
        formData.append('tipo_documento_elemento', tipo_documento_elemento);
        formData.append('privado_documento', privado_documento);
        formData.append('observaciones_documento', observaciones_documento || '');
        formData.append('activo_documento_elemento', activo_documento_elemento);
        
        console.log('Valores en FormData:');
        console.log('- id_elemento:', id_elemento);
        console.log('- tipo_documento:', tipo_documento_elemento);
        console.log('- descripcion:', descripcion_documento_elemento);
        
        if (id_documento_elemento) {
            formData.append('id_documento_elemento', id_documento_elemento);
            // Agregar archivo actual para mantenerlo si no se cambia
            const archivoActual = $('#archivo_actual').val();
            formData.append('archivo_actual', archivoActual);
            console.log('Modo EDICIÓN - archivo_actual:', archivoActual);
        }
        
        // Agregar archivo si se seleccionó uno
        const archivoInput = $('#archivo_documento')[0];
        const archivoFile = archivoInput && archivoInput.files && archivoInput.files.length > 0 ? archivoInput.files[0] : null;
        
        console.log('=== INFORMACIÓN DEL ARCHIVO ===');
        console.log('Input existe?:', archivoInput !== null);
        console.log('Tiene files?:', archivoInput && archivoInput.files);
        console.log('Archivo seleccionado?:', archivoFile !== null);
        
        if (archivoFile) {
            console.log('Detalles del archivo:');
            console.log('- name:', archivoFile.name);
            console.log('- size:', archivoFile.size);
            console.log('- type:', archivoFile.type);
            formData.append('archivo_documento', archivoFile, archivoFile.name);
            console.log('✓ Archivo agregado al FormData');
        } else {
            console.log('✗ No hay archivo seleccionado');
        }
        
        console.log('===============================');
        
        // Debug: Mostrar contenido de FormData
        console.log('=== CONTENIDO DE FORMDATA ===');
        for (let pair of formData.entries()) {
            if (pair[1] instanceof File) {
                console.log(pair[0] + ':', 'File(' + pair[1].name + ', ' + pair[1].size + ' bytes, ' + pair[1].type + ')');
            } else {
                console.log(pair[0] + ':', pair[1]);
            }
        }
        console.log('============================');

        $.ajax({
            url: "../../controller/documento_elemento.php?op=guardaryeditar",
            type: "POST",
            data: formData,
            processData: false, // No procesar los datos
            contentType: false, // No establecer contentType
            dataType: "json",
            success: function(res) {
                if (res.success) {
                    // Marcar como guardado para evitar la alerta de salida
                    formSaved = true;
                    
                    toastr.success(res.message || "Documento guardado correctamente");
                    
                    // Redirigir al listado después de un breve delay, manteniendo el filtro por elemento
                    setTimeout(() => {
                        if (id_elemento) {
                            window.location.href = 'index.php?id_elemento=' + id_elemento;
                        } else {
                            window.location.href = 'index.php';
                        }
                    }, 1500);
                } else {
                    toastr.error(res.message || "Error al guardar el documento");
                    // Restaurar botón
                    $('#btnSalvarDocumento').prop('disabled', false).html('<i class="fas fa-save me-2"></i>Guardar Documento');
                }
            },
            error: function(xhr, status, error) {
                console.error("Error en guardado:", error);
                Swal.fire('Error', 'No se pudo guardar el documento. Error: ' + error, 'error');
                // Restaurar botón
                $('#btnSalvarDocumento').prop('disabled', false).html('<i class="fas fa-save me-2"></i>Guardar Documento');
            }
        });
    }

    /////////////////////////////////////////
    //   FUNCIONES DE UTILIDAD            //
    ///////////////////////////////////////

    // Variables para controlar si el formulario ha sido modificado
    var formOriginalValues = {};
    var formSaved = false;
    
    // Capturar valores originales después de cargar datos
    function captureOriginalValues() {
        formOriginalValues = {
            id_elemento: $('#id_elemento').val(),
            descripcion_documento_elemento: $('#descripcion_documento_elemento').val(),
            tipo_documento_elemento: $('#tipo_documento_elemento').val(),
            archivo_actual: $('#archivo_actual').val(),
            privado_documento: $('#privado_documento').is(':checked'),
            observaciones_documento: $('#observaciones_documento').val()
        };
    }
    
    // Verificar si el formulario ha cambiado
    function hasFormChanged() {
        // Verificar si se ha seleccionado un nuevo archivo
        const hasNewFile = $('#archivo_documento')[0].files && $('#archivo_documento')[0].files.length > 0;
        
        return (
            $('#id_elemento').val() !== formOriginalValues.id_elemento ||
            $('#descripcion_documento_elemento').val() !== formOriginalValues.descripcion_documento_elemento ||
            $('#tipo_documento_elemento').val() !== formOriginalValues.tipo_documento_elemento ||
            $('#privado_documento').is(':checked') !== formOriginalValues.privado_documento ||
            $('#observaciones_documento').val() !== formOriginalValues.observaciones_documento ||
            hasNewFile
        );
    }
    
    // Función para mostrar mensajes de confirmación antes de salir
    window.addEventListener('beforeunload', function (e) {
        // Solo mostrar si hay cambios reales en el formulario y no se ha guardado
        if (!formSaved && hasFormChanged()) {
            e.preventDefault();
            e.returnValue = '';
        }
    });

    // Función para marcar el formulario como guardado
    function markFormAsSaved() {
        formSaved = true;
    }

    /////////////////////////////////////////
    //   FIN FUNCIONES DE UTILIDAD        //
    ///////////////////////////////////////

    /////////////////////////////////////////
    //   INICIALIZACIÓN DEL FORMULARIO    //
    ///////////////////////////////////////
    
    // La inicialización ya se realiza arriba con las variables modo, idDocumento e idElementoPreseleccionado
    // No es necesario duplicar la lógica aquí

}); // de document.ready

// Función global para cargar datos (llamada desde el HTML)
function cargarDatosDocumento(idDocumento) {
    console.log('Función global - Cargando datos de documento ID:', idDocumento);
}

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
            <a href="../../public/img/docs_elementos/${filePath}" target="_blank" class="btn btn-sm btn-outline-primary mt-2">
                <i class="bi bi-eye me-1"></i>Ver documento
            </a>
        `;
    }
}
