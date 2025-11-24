$(document).ready(function () {

    /////////////////////////////////////
    //     FORMATEO DE CAMPOS          //
    ///////////////////////////////////

    var formValidator = new FormValidator('formFamilia', {
        codigo_familia: {
            required: true
        },
        nombre_familia: {
            required: true
        },
        name_familia: {
            required: true
        }
    });

    /////////////////////////////////////////
    //     FIN FORMATEO DE CAMPOS          //
    ////////////////////////////////////////

    /////////////////////////////////////////
    //   FUNCIONES DE INICIALIZACIÓN      //
    ///////////////////////////////////////

    // Variable para almacenar la instancia de DataTable
    var tablaUnidades;

    // Inicializar modal de búsqueda de unidades
    function inicializarModalUnidades() {
        // Inicializar DataTable cuando se abre el modal
        $('#modalBuscarUnidad').on('shown.bs.modal', function () {
            if (!$.fn.DataTable.isDataTable('#tablaUnidadesBusqueda')) {
                tablaUnidades = $('#tablaUnidadesBusqueda').DataTable({
                    "processing": true,
                    "serverSide": false,
                    "ajax": {
                        "url": "../../controller/unidad_medida.php?op=listarTodasParaModal",
                        "type": "GET",
                        "dataSrc": ""
                    },
                    "columns": [
                        { "data": "id_unidad" },
                        { "data": "nombre_unidad" },
                        { 
                            "data": "simbolo_unidad",
                            "defaultContent": "-"
                        },
                        { 
                            "data": "descr_unidad",
                            "defaultContent": "-"
                        },
                        { 
                            "data": "activo_unidad",
                            "render": function(data, type, row) {
                                if (data == 1) {
                                    return '<span class="badge bg-success">Activo</span>';
                                } else {
                                    return '<span class="badge bg-danger">Inactivo</span>';
                                }
                            }
                        },
                        {
                            "data": null,
                            "defaultContent": "",
                            "orderable": false,
                            "render": function(data, type, row) {
                                return '<button type="button" class="btn btn-sm btn-primary seleccionar-unidad" ' +
                                       'data-id="' + row.id_unidad + '" ' +
                                       'data-nombre="' + row.nombre_unidad + '" ' +
                                       'data-simbolo="' + (row.simbolo_unidad || '') + '" ' +
                                       'data-descripcion="' + (row.descr_unidad || '') + '">' +
                                       '<i class="fas fa-check me-1"></i>Seleccionar</button>';
                            }
                        }
                    ],
                    "language": {
                        "lengthMenu": "Mostrar _MENU_ registros por página",
                        "zeroRecords": "No se encontraron unidades de medida",
                        "info": "Página _PAGE_ de _PAGES_ (_TOTAL_ unidades totales)",
                        "infoEmpty": "No hay unidades disponibles",
                        "infoFiltered": "(filtrado de _MAX_ unidades totales)",
                        "search": "Buscar:",
                         // Se hace para cambiar la paginación por flechas
                        "paginate": {
                            "first": '<i class="bi bi-chevron-double-left"></i>', // Ícono de FontAwesome
                            "last": '<i class="bi bi-chevron-double-right"></i>', // Ícono de FontAwesome
                            "previous": '<i class="bi bi-chevron-compact-left"></i>', // Ícono de FontAwesome
                            "next": '<i class="bi bi-chevron-compact-right"></i>'  // Ícono de FontAwesome
                        },
                        "processing": "Procesando..."
                    },
                    "pageLength": 10,
                    "responsive": true,
                    "order": [[1, 'asc']], // Ordenar por nombre
                    "columnDefs": [
                        {
                            "targets": [0], // ID
                            "width": "8%"
                        },
                        {
                            "targets": [1], // Nombre
                            "width": "25%"
                        },
                        {
                            "targets": [2], // Símbolo
                            "width": "12%"
                        },
                        {
                            "targets": [3], // Descripción
                            "width": "35%"
                        },
                        {
                            "targets": [4], // Estado
                            "width": "10%"
                        },
                        {
                            "targets": [5], // Acción
                            "width": "10%"
                        }
                    ]
                });
            }
        });

        // Limpiar DataTable cuando se cierra el modal
        $('#modalBuscarUnidad').on('hidden.bs.modal', function () {
            if (tablaUnidades) {
                tablaUnidades.destroy();
                tablaUnidades = null;
            }
        });
    }

    // Manejar selección de unidad
    $(document).on('click', '.seleccionar-unidad', function() {
        var id = $(this).data('id');
        var nombre = $(this).data('nombre');
        var simbolo = $(this).data('simbolo');
        var descripcion = $(this).data('descripcion');
        
        // Establecer valores
        $('#id_unidad_familia').val(id);
        
        // Mostrar texto en el campo display
        var displayText = nombre;
        if (simbolo && simbolo.trim() !== '') {
            displayText += ' (' + simbolo + ')';
        }
        $('#unidad_medida_display').val(displayText);
        
        // Mostrar descripción si existe
        if (descripcion && descripcion.trim() !== '') {
            $('#unidad-descr-text').text(descripcion);
            $('#unidad-descripcion').show();
        } else {
            $('#unidad-descripcion').hide();
        }
        
        // Cerrar modal
        $('#modalBuscarUnidad').modal('hide');
        
        // Mostrar confirmación
        toastr.success('Unidad de medida seleccionada: ' + nombre);
    });

    // Manejar botón limpiar
    $('#limpiarUnidad').on('click', function() {
        $('#id_unidad_familia').val('');
        $('#unidad_medida_display').val('');
        $('#unidad-descripcion').hide();
        toastr.info('Selección de unidad de medida eliminada');
    });

    // Cargar unidades de medida disponibles (mantenido para compatibilidad, pero ya no se usa)
    function cargarUnidadesMedida() {
        // Esta función se mantiene por compatibilidad pero ya no es necesaria
        console.log('Función cargarUnidadesMedida() - Modal implementado');
    }

    // Función para obtener parámetros de la URL
    function getUrlParameter(name) {
        name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
        var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
        var results = regex.exec(location.search);
        return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
    }

    // Función para cargar datos de familia para edición
    function cargarDatosFamilia(idFamilia) {
        console.log('Cargando datos de familia ID:', idFamilia);
        
        $.ajax({
            url: "../../controller/familia_unidad.php?op=mostrar",
            type: "POST",
            data: { id_familia: idFamilia },
            dataType: "json",
            success: function(data) {
                try {
                    if (!data || typeof data !== 'object') {
                        throw new Error('Respuesta del servidor no válida');
                    }

                    console.log('Datos recibidos:', data);

                    // Llenar los campos del formulario
                    $('#id_familia').val(data.id_familia);
                    $('#codigo_familia').val(data.codigo_familia);
                    $('#nombre_familia').val(data.nombre_familia);
                    $('#name_familia').val(data.name_familia);
                    $('#descr_familia').val(data.descr_familia);
                    
                    // Configurar unidad de familia
                    if (data.id_unidad_familia) {
                        $('#id_unidad_familia').val(data.id_unidad_familia);
                        
                        // Buscar información de la unidad para mostrar en el campo display
                        $.ajax({
                            url: "../../controller/unidad_medida.php?op=obtenerUnidadPorId",
                            type: "GET",
                            data: { id_unidad: data.id_unidad_familia },
                            dataType: "json",
                            success: function(unidadData) {
                                if (unidadData && unidadData.nombre_unidad) {
                                    var displayText = unidadData.nombre_unidad;
                                    if (unidadData.simbolo_unidad) {
                                        displayText += ' (' + unidadData.simbolo_unidad + ')';
                                    }
                                    $('#unidad_medida_display').val(displayText);
                                    
                                    // Mostrar descripción si existe
                                    if (unidadData.descr_unidad && unidadData.descr_unidad.trim() !== '') {
                                        $('#unidad-descr-text').text(unidadData.descr_unidad);
                                        $('#unidad-descripcion').show();
                                    }
                                }
                            },
                            error: function() {
                                console.log('No se pudo cargar información de la unidad');
                            }
                        });
                    }
                    
                    // Configurar imagen actual
                    if (data.imagen_familia) {
                        $('#imagen_actual').val(data.imagen_familia);
                        // Mostrar imagen existente
                        showExistingImage(data.imagen_familia);
                    }
                    
                    // Configurar el switch de estado (mantener estado actual en edición)
                    $('#activo_familia').prop('checked', data.activo_familia == 1);
                    
                    // Actualizar texto del estado según el valor actual
                    if (data.activo_familia == 1) {
                        $('#estado-text').text('Familia Activa').removeClass('text-danger').addClass('text-success');
                    } else {
                        $('#estado-text').text('Familia Inactiva').removeClass('text-success').addClass('text-danger');
                    }
                    
                    // Actualizar contador de caracteres si existe descripción
                    if (data.descr_familia) {
                        $('#char-count').text(data.descr_familia.length);
                    }
                    
                    // Capturar valores originales después de cargar datos
                    setTimeout(function() {
                        captureOriginalValues();
                    }, 100);
                    
                    // Enfocar el primer campo
                    $('#codigo_familia').focus();
                    
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
                toastr.error('Error al obtener datos de la familia');
                // Redirigir al listado si hay error
                setTimeout(() => {
                    window.location.href = 'index.php';
                }, 2000);
            }
        });
    }

    // Verificar si estamos en modo edición
    const idFamilia = getUrlParameter('id');
    const modo = getUrlParameter('modo') || 'nuevo';
    
    if (modo === 'editar' && idFamilia) {
        cargarDatosFamilia(idFamilia);
    } else {
        // En modo nuevo, enfocar el primer campo y capturar valores iniciales
        $('#codigo_familia').focus();
        setTimeout(function() {
            captureOriginalValues();
        }, 100);
    }

    /////////////////////////////////////////
    //   FIN FUNCIONES DE INICIALIZACIÓN  //
    ///////////////////////////////////////

    //*****************************************************/
    //   CAPTURAR EL CLICK EN EL BOTÓN DE SALVAR FAMILIA
    //*****************************************************/

    $(document).on('click', '#btnSalvarFamilia', function (event) {
        event.preventDefault();

        // Obtener valores del formulario
        var id_familiaR = $('#id_familia').val().trim();
        var codigo_familiaR = $('#codigo_familia').val().trim();
        var nombre_familiaR = $('#nombre_familia').val().trim();
        var name_familiaR = $('#name_familia').val().trim();
        var descr_familiaR = $('#descr_familia').val().trim();
        var id_unidad_familiaR = $('#id_unidad_familia').val() || null;
        
        // El estado siempre será activo para nuevas familias, o mantener el actual para edición
        var activo_familiaR;
        if (id_familiaR) {
            // En edición: mantener el estado actual (el que está en el checkbox)
            activo_familiaR = $('#activo_familia').is(':checked') ? 1 : 0;
        } else {
            // Nueva familia: siempre activo
            activo_familiaR = 1;
        }

        // Validar el formulario
        if (!formValidator.validateForm(event)) {
            toastr.error('Por favor, corrija los errores en el formulario.', 'Error de Validación');
            return;
        }
        
        // Verificar familia primero
        verificarFamiliaExistente(id_familiaR, codigo_familiaR, name_familiaR, nombre_familiaR, descr_familiaR, activo_familiaR, id_unidad_familiaR);
    });

    function verificarFamiliaExistente(id_familia, codigo_familia, name_familia, nombre_familia, descr_familia, activo_familia, id_unidad_familia) {
        $.ajax({
            url: "../../controller/familia_unidad.php?op=verificarFamilia",
            type: "GET",
            data: { 
                nombre_familia: nombre_familia,
                name_familia: name_familia, 
                codigo_familia: codigo_familia,
                id_familia: id_familia 
            },
            dataType: "json",
            success: function(response) {
                if (!response.success) {
                    toastr.warning(response.message || "Error al verificar la familia.");
                    return;
                }

                if (response.existe) {
                    mostrarErrorFamiliaExistente(nombre_familia);
                } else {
                    guardarFamilia(id_familia, codigo_familia, name_familia, nombre_familia, descr_familia, activo_familia, id_unidad_familia);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error en verificación:', error);
                toastr.error('Error al verificar la familia. Intente nuevamente.', 'Error');
            }
        });
    }

    function mostrarErrorFamiliaExistente(nombre_familia) {
        console.log("Familia duplicada detectada:", nombre_familia);
        Swal.fire({
            title: 'Nombre de familia duplicado',
            text: 'La familia "' + nombre_familia + '" ya existe. Por favor, elija otro nombre.',
            icon: 'warning',
            confirmButtonText: 'Entendido'
        });
    }

    function guardarFamilia(id_familia, codigo_familia, name_familia, nombre_familia, descr_familia, activo_familia, id_unidad_familia) {
        // Mostrar indicador de carga
        $('#btnSalvarFamilia').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Guardando...');
        
        // Crear FormData para manejar archivos
        var formData = new FormData();
        formData.append('codigo_familia', codigo_familia);
        formData.append('nombre_familia', nombre_familia);
        formData.append('name_familia', name_familia);
        formData.append('descr_familia', descr_familia);
        formData.append('activo_familia', activo_familia);
        formData.append('id_unidad_familia', id_unidad_familia || '');
        
        if (id_familia) {
            formData.append('id_familia', id_familia);
            // Agregar imagen actual para mantenerla si no se cambia
            formData.append('imagen_actual', $('#imagen_actual').val());
        }
        
        // Agregar archivo de imagen si se seleccionó uno
        const imagenFile = $('#imagen_familia')[0].files[0];
        if (imagenFile) {
            formData.append('imagen_familia', imagenFile);
        }

        $.ajax({
            url: "../../controller/familia_unidad.php?op=guardaryeditar",
            type: "POST",
            data: formData,
            processData: false, // No procesar los datos
            contentType: false, // No establecer contentType
            dataType: "json",
            success: function(res) {
                if (res.success) {
                    // Marcar como guardado para evitar la alerta de salida
                    formSaved = true;
                    
                    toastr.success(res.message || "Familia guardada correctamente");
                    
                    // Redirigir al listado después de un breve delay
                    setTimeout(() => {
                        window.location.href = 'index.php';
                    }, 1500);
                } else {
                    toastr.error(res.message || "Error al guardar la familia");
                    // Restaurar botón
                    $('#btnSalvarFamilia').prop('disabled', false).html('<i class="fas fa-save me-2"></i>Guardar Familia');
                }
            },
            error: function(xhr, status, error) {
                console.error("Error en guardado:", error);
                Swal.fire('Error', 'No se pudo guardar la familia. Error: ' + error, 'error');
                // Restaurar botón
                $('#btnSalvarFamilia').prop('disabled', false).html('<i class="fas fa-save me-2"></i>Guardar Familia');
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
            codigo_familia: $('#codigo_familia').val(),
            nombre_familia: $('#nombre_familia').val(),
            name_familia: $('#name_familia').val(),
            descr_familia: $('#descr_familia').val(),
            imagen_actual: $('#imagen_actual').val(),
            id_unidad_familia: $('#id_unidad_familia').val()
        };
    }
    
    // Verificar si el formulario ha cambiado
    function hasFormChanged() {
        // Verificar si se ha seleccionado una nueva imagen
        const hasNewImage = $('#imagen_familia')[0].files && $('#imagen_familia')[0].files.length > 0;
        
        return (
            $('#codigo_familia').val() !== formOriginalValues.codigo_familia ||
            $('#nombre_familia').val() !== formOriginalValues.nombre_familia ||
            $('#name_familia').val() !== formOriginalValues.name_familia ||
            $('#descr_familia').val() !== formOriginalValues.descr_familia ||
            $('#id_unidad_familia').val() !== formOriginalValues.id_unidad_familia ||
            hasNewImage
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
    
    // Inicializar modal de unidades de medida
    inicializarModalUnidades();
    
    // Cargar unidades de medida al cargar la página (ahora solo para compatibilidad)
    cargarUnidadesMedida();

    // Inicialización según el modo (nuevo/editar)
    const urlParamsInit = new URLSearchParams(window.location.search);
    const idFamiliaInit = urlParamsInit.get('id');
    const modoInit = urlParamsInit.get('modo') || 'nuevo';
    
    if (modoInit === 'editar' && idFamiliaInit) {
        // Cargar datos después de cargar las unidades de medida
        setTimeout(function() {
            cargarDatosFamilia(idFamiliaInit);
        }, 500);
    } else {
        // Capturar valores originales para formulario nuevo
        setTimeout(function() {
            captureOriginalValues();
        }, 100);
    }

}); // de document.ready

// Función global para cargar datos (llamada desde el HTML)
function cargarDatosFamilia(idFamilia) {
    console.log('Función global - Cargando datos de familia ID:', idFamilia);
    // Esta función ya está implementada dentro del document.ready
}

// Función para mostrar vista previa por defecto
function showDefaultImagePreview() {
    const imagePreview = document.getElementById('image-preview');
    if (imagePreview) {
        imagePreview.innerHTML = `
            <i class="fas fa-image text-muted" style="font-size: 3rem;"></i>
            <p class="text-muted mt-2 mb-0">Sin imagen</p>
        `;
    }
}

// Función para mostrar imagen existente
function showExistingImage(imagePath) {
    const imagePreview = document.getElementById('image-preview');
    if (imagePreview && imagePath) {
        const fullPath = '../../public/img/familia/' + imagePath;
        imagePreview.innerHTML = `
            <img src="${fullPath}" alt="Imagen actual" style="max-width: 100%; max-height: 100px; border-radius: 4px;" 
                 onerror="this.onerror=null; showDefaultImagePreview();">
            <p class="text-muted mt-1 mb-0 small">Imagen actual: ${imagePath}</p>
        `;
    }
}