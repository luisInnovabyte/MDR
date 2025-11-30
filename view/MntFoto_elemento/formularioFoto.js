$(document).ready(function () {

    /////////////////////////////////////
    //     FORMATEO DE CAMPOS          //
    ///////////////////////////////////

    var formValidator = new FormValidator('formFoto', {
        id_elemento: {
            required: true
        },
        descripcion_foto_elemento: {
            required: true
        }
        // archivo_foto se valida manualmente porque es un input file
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
    const idFoto = getUrlParameter('id');
    const idElementoPreseleccionado = getUrlParameter('id_elemento');
    
    console.log('=== Inicio de formularioFoto.js ===');
    console.log('Modo:', modo);
    console.log('ID Foto:', idFoto);
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

    // Función para cargar datos de foto para edición
    function cargarDatosFoto(idFoto) {
        console.log('Cargando datos de foto ID:', idFoto);
        
        $.ajax({
            url: "../../controller/foto_elemento.php?op=mostrar",
            type: "POST",
            data: { id_foto_elemento: idFoto },
            dataType: "json",
            success: function(data) {
                try {
                    if (!data || typeof data !== 'object') {
                        throw new Error('Respuesta del servidor no válida');
                    }

                    console.log('Datos recibidos:', data);

                    // Llenar los campos del formulario
                    $('#id_foto_elemento').val(data.id_foto_elemento);
                    $('#id_elemento').val(data.id_elemento);
                    
                    // Cargar información del elemento para mostrar en el banner
                    if (data.id_elemento) {
                        cargarInfoElemento(data.id_elemento);
                    }
                    
                    $('#descripcion_foto_elemento').val(data.descripcion_foto_elemento);
                    $('#observaciones_foto').val(data.observaciones_foto || '');
                    
                    // Configurar archivo actual
                    if (data.archivo_foto) {
                        $('#archivo_actual').val(data.archivo_foto);
                        // Mostrar archivo existente
                        showExistingFile(data.archivo_foto);
                        // En modo edición, el archivo no es obligatorio
                        $('#archivo_foto').removeAttr('required');
                    }
                    
                    // Configurar el switch de estado (mantener estado actual en edición)
                    $('#activo_foto_elemento').prop('checked', data.activo_foto == 1);
                    
                    // Configurar el checkbox de privacidad
                    $('#privado_foto').prop('checked', data.privado_foto == 1);
                    
                    // Actualizar texto del estado según el valor actual
                    if (data.activo_foto == 1) {
                        $('#estado-text').text('Foto Activa').removeClass('text-danger').addClass('text-success');
                    } else {
                        $('#estado-text').text('Foto Inactiva').removeClass('text-success').addClass('text-danger');
                    }
                    
                    // Actualizar texto de privacidad según el valor actual
                    if (data.privado_foto == 1) {
                        $('#privado-text').text('Foto Privada').removeClass('text-success').addClass('text-danger');
                    } else {
                        $('#privado-text').text('Foto Pública').removeClass('text-danger').addClass('text-success');
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
                toastr.error('Error al obtener datos de la foto');
                // Redirigir al listado si hay error
                setTimeout(() => {
                    window.location.href = 'index.php';
                }, 2000);
            }
        });
    }

    // Verificar si estamos en modo edición (las variables ya están declaradas arriba)
    if (modo === 'editar' && idFoto) {
        cargarDatosFoto(idFoto);
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
    //   CAPTURAR EL CLICK EN EL BOTÓN DE SALVAR FOTO
    //*****************************************************/

    $(document).on('click', '#btnSalvarFoto', function (event) {
        console.log('=== CLICK EN BOTÓN GUARDAR ===');
        event.preventDefault();

        // Obtener valores del formulario
        var id_foto_elementoR = ($('#id_foto_elemento').val() || '').trim();
        var id_elementoR = $('#id_elemento').val();
        var descripcion_foto_elementoR = ($('#descripcion_foto_elemento').val() || '').trim();
        var observaciones_fotoR = ($('#observaciones_foto').val() || '').trim();
        
        // El estado siempre será activo para nuevas fotos, o mantener el actual para edición
        var activo_foto_elementoR;
        if (id_foto_elementoR) {
            // En edición: mantener el estado actual
            activo_foto_elementoR = $('#activo_foto_elemento').is(':checked') ? 1 : 0;
        } else {
            // Nueva foto: siempre activa
            activo_foto_elementoR = 1;
        }
        
        // El privado se toma del checkbox
        var privado_fotoR = $('#privado_foto').is(':checked') ? 1 : 0;

        console.log('Valores capturados:');
        console.log('- id_elemento:', id_elementoR);
        console.log('- descripcion:', descripcion_foto_elementoR);
        
        // Validar que existe un elemento asignado
        if (!id_elementoR) {
            console.error('Falta id_elemento');
            toastr.error('No se ha asignado un elemento. Debe acceder desde el listado de elementos.', 'Error de Validación');
            setTimeout(function() {
                window.location.href = 'index.php';
            }, 2000);
            return;
        }

        // Validar archivo (solo obligatorio en modo nuevo)
        const archivoInput = $('#archivo_foto')[0];
        const archivoFile = archivoInput.files && archivoInput.files.length > 0 ? archivoInput.files[0] : null;
        console.log('=== VALIDACIÓN DE ARCHIVO ===');
        console.log('Input archivo existe?:', archivoInput !== null);
        console.log('Tiene files?:', archivoInput.files);
        console.log('Cantidad de files:', archivoInput.files ? archivoInput.files.length : 0);
        console.log('Archivo seleccionado:', archivoFile);
        console.log('Es nueva foto?:', !id_foto_elementoR);
        console.log('===========================');
        
        if (!id_foto_elementoR && !archivoFile) {
            console.error('Falta archivo en modo nuevo');
            toastr.error('Debe seleccionar una imagen para la foto', 'Error de Validación');
            $('#archivo_foto').addClass('is-invalid');
            return;
        }
        
        // Validar tamaño del archivo
        if (archivoFile && archivoFile.size > 5 * 1024 * 1024) {
            console.error('Archivo demasiado grande:', archivoFile.size);
            toastr.error('La imagen es demasiado grande. Máximo 5MB.', 'Error de Validación');
            $('#archivo_foto').addClass('is-invalid');
            return;
        }
        
        // Validar tipo de archivo
        if (archivoFile) {
            const tiposPermitidos = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
            if (!tiposPermitidos.includes(archivoFile.type)) {
                console.error('Tipo de archivo no permitido:', archivoFile.type);
                toastr.error('Formato de imagen no válido. Use JPG, JPEG, PNG, GIF o WEBP.', 'Error de Validación');
                $('#archivo_foto').addClass('is-invalid');
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
        
        // Guardar directamente
        guardarFoto(id_foto_elementoR, id_elementoR, descripcion_foto_elementoR, privado_fotoR, observaciones_fotoR, activo_foto_elementoR);
    });

    function guardarFoto(id_foto_elemento, id_elemento, descripcion_foto_elemento, privado_foto, observaciones_foto, activo_foto_elemento) {
        console.log('=== FUNCIÓN GUARDAR FOTO ===');
        
        // Mostrar indicador de carga
        $('#btnSalvarFoto').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Guardando...');
        
        // Crear FormData para manejar archivos
        var formData = new FormData();
        formData.append('id_elemento', id_elemento);
        formData.append('descripcion_foto_elemento', descripcion_foto_elemento);
        formData.append('privado_foto', privado_foto);
        formData.append('observaciones_foto', observaciones_foto || '');
        formData.append('activo_foto_elemento', activo_foto_elemento);
        
        console.log('Valores en FormData:');
        console.log('- id_elemento:', id_elemento);
        console.log('- descripcion:', descripcion_foto_elemento);
        
        if (id_foto_elemento) {
            formData.append('id_foto_elemento', id_foto_elemento);
            // Agregar archivo actual para mantenerlo si no se cambia
            const archivoActual = $('#archivo_actual').val();
            formData.append('archivo_actual', archivoActual);
            console.log('Modo EDICIÓN - archivo_actual:', archivoActual);
        }
        
        // Agregar archivo si se seleccionó uno
        const archivoInput = $('#archivo_foto')[0];
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
            formData.append('archivo_foto', archivoFile, archivoFile.name);
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
            url: "../../controller/foto_elemento.php?op=guardaryeditar",
            type: "POST",
            data: formData,
            processData: false, // No procesar los datos
            contentType: false, // No establecer contentType
            dataType: "json",
            success: function(res) {
                if (res.success) {
                    // Marcar como guardado para evitar la alerta de salida
                    formSaved = true;
                    
                    toastr.success(res.message || "Foto guardada correctamente");
                    
                    // Redirigir al listado después de un breve delay, manteniendo el filtro por elemento
                    setTimeout(() => {
                        if (id_elemento) {
                            window.location.href = 'index.php?id_elemento=' + id_elemento;
                        } else {
                            window.location.href = 'index.php';
                        }
                    }, 1500);
                } else {
                    toastr.error(res.message || "Error al guardar la foto");
                    // Restaurar botón
                    $('#btnSalvarFoto').prop('disabled', false).html('<i class="fas fa-save me-2"></i>Guardar Foto');
                }
            },
            error: function(xhr, status, error) {
                console.error("Error en guardado:", error);
                Swal.fire('Error', 'No se pudo guardar la foto. Error: ' + error, 'error');
                // Restaurar botón
                $('#btnSalvarFoto').prop('disabled', false).html('<i class="fas fa-save me-2"></i>Guardar Foto');
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
            descripcion_foto_elemento: $('#descripcion_foto_elemento').val(),
            archivo_actual: $('#archivo_actual').val(),
            privado_foto: $('#privado_foto').is(':checked'),
            observaciones_foto: $('#observaciones_foto').val()
        };
    }
    
    // Verificar si el formulario ha cambiado
    function hasFormChanged() {
        // Verificar si se ha seleccionado una nueva imagen
        const hasNewFile = $('#archivo_foto')[0].files && $('#archivo_foto')[0].files.length > 0;
        
        return (
            $('#id_elemento').val() !== formOriginalValues.id_elemento ||
            $('#descripcion_foto_elemento').val() !== formOriginalValues.descripcion_foto_elemento ||
            $('#privado_foto').is(':checked') !== formOriginalValues.privado_foto ||
            $('#observaciones_foto').val() !== formOriginalValues.observaciones_foto ||
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
    
    // La inicialización ya se realiza arriba con las variables modo, idFoto e idElementoPreseleccionado
    // No es necesario duplicar la lógica aquí

}); // de document.ready

// Función global para cargar datos (llamada desde el HTML)
function cargarDatosFoto(idFoto) {
    console.log('Función global - Cargando datos de foto ID:', idFoto);
}

// Función para mostrar vista previa por defecto
function showDefaultFilePreview() {
    const filePreview = document.getElementById('file-preview');
    if (filePreview) {
        filePreview.innerHTML = `
            <i class="fas fa-image text-muted" style="font-size: 3rem;"></i>
            <p class="text-muted mt-2 mb-0">Sin imagen</p>
        `;
    }
}

// Función para mostrar imagen existente
function showExistingFile(filePath) {
    const filePreview = document.getElementById('file-preview');
    if (filePreview && filePath) {
        const fileName = filePath.split('/').pop();
        filePreview.innerHTML = `
            <img src="../../public/img/fotos_elementos/${filePath}" class="img-fluid rounded" style="max-height: 180px; max-width: 100%;" alt="Foto actual">
            <p class="text-muted small mt-2 mb-0">Imagen actual:</p>
            <p class="text-muted small mb-0">${fileName}</p>
            <a href="../../public/img/fotos_elementos/${filePath}" target="_blank" class="btn btn-sm btn-outline-primary mt-2">
                <i class="bi bi-eye me-1"></i>Ver en tamaño completo
            </a>
        `;
    }
}
