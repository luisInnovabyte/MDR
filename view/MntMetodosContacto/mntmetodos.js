$(document).ready(function () {

    /////////////////////////////////////
    //            TIPS                //
    ///////////////////////////////////
    // Ocultar dinámicamente la columna con índice 2 (tercera columna)
    // ----> $('#miTabla').DataTable().column(2).visible(false);

    /////////////////////////////////////
    //          FIN DE TIPS           //
    ///////////////////////////////////


    /////////////////////////////////////
    //     FORMATEO DE CAMPOS          //
    ///////////////////////////////////

    var formValidator = new FormValidator('formMetodo', {
        nombre: {
            pattern: "^[A-Za-zÁÉÍÓÚáéíóúÑñ\\s\\-’']+$",
            required: true
        }
    });
    

    /////////////////////////////////////////
    //     FIN FORMATEO DE CAMPOS          //
    ////////////////////////////////////////

     // 1. Al abrir: Cambia a azul claro (bg-info)
     $('#collapseOne').on('show.bs.collapse', function() {
        $('#accordion-toggle')
            .removeClass('bg-primary')  // Quita el azul original
            .addClass('bg-info')       // Añade azul claro
            .css('color', 'white');    // Asegura texto blanco
    });

    // 2. Al cerrar: Restaura el azul original (bg-primary)
    $('#collapseOne').on('hide.bs.collapse', function() {
        $('#accordion-toggle')
            .removeClass('bg-info')    // Quita azul claro
            .addClass('bg-primary')    // Restaura azul original
            .css('color', '#e6f0fa');  // Color texto original
    });

    // 3. Efecto hover (opcional)
    $('#accordion-toggle').hover(
        function() { // Mouse entra
            $(this).css('opacity', '0.9');
        },
        function() { // Mouse sale
            $(this).css('opacity', '1');
        }
    );

    /////////////////////////////////////
    // INICIO DE LA TABLA DE METODOS //
    //         DATATABLES             //
    ///////////////////////////////////
    var datatable_metodosConfig = {
        //serverSide: true, // procesamiento del lado del servidor
        processing: true, // mostrar el procesamiento de la tabla
        layout: {
            bottomEnd: { // que elementos de la paginación queremos que aparezcan
                paging: {
                    firstLast: true,
                    numbers: false,
                    previousNext: true
                }
            }
        }, //
        language: {
            // Se hace para cambiar la paginación por flechas
            paginate: {
                first: '<i class="bi bi-chevron-double-left"></i>', // Ícono de FontAwesome
                last: '<i class="bi bi-chevron-double-right"></i>', // Ícono de FontAwesome
                previous: '<i class="bi bi-chevron-compact-left"></i>', // Ícono de FontAwesome
                next: '<i class="bi bi-chevron-compact-right"></i>'  // Ícono de FontAwesome
            }
        }, // de la language
        columns: [
            { name: 'control', data: null, defaultContent: '', className: 'details-control sorting_1 text-center' }, // Columna 0: Mostrar más      
            { name: 'id_metodo', data: 'id_metodo', visible: false, className: "text-center"  }, // Columna 0: ID_METODO
            { name: 'nombre', data: 'nombre', className: "text-center"  }, // Columna 1: NOMBRE
            { name: 'estado', data: 'estado', className: "text-center"  }, // Columna 2: ESTADO
            { name: 'imagen_metodo', data: 'imagen_metodo', className: "text-center"  }, // Columna 3: IMAGEN_METODO
            { name: 'activar', data: null, defaultContent: '', className: "text-center"  },  // Columna //  4: ACTIVAR/DESACTIVAR ADJUNTO
            { name: 'editar', data: null, defaultContent: '', className: "text-center"  },  // Columna //  5: EDITAR METODO
        ], // de las columnas
        columnDefs: [
            // 0 - CONTROL
            { 
              targets: 'control:name', width: '10%', searchable: false, orderable: false, className: "text-center" 
            }, 
            // 1 - ID_METODO
            { targets: 'id_metodo:name', width: '10%', searchable: false, orderable: true, className: "text-center"  },
            // 2 - NOMBRE
            {
                targets: 'nombre:name',
                width: '40%',
                searchable: true,
                orderable: true,
                className: "text-center",
                render: function(data, type, row) {
                    if (type === "display" || type === "filter") {
                        return row.nombre;
                    }
                    return data;
                }
            },
            // 3 - ESTADO
            {
                targets: 'estado:name', width: '20%', orderable: true, searchable: true, className: "text-center",
                render: function (data, type, row) {
                    if (type === "display") {
                        return data == 1 ? '<i class="bi bi-check-circle text-success fa-2x"></i>' : '<i class="bi bi-x-circle text-danger fa-2x"></i>';
                    }
                    return data;
                }
            },
            // Columna 4: Imagen
            // 2 - id_metodo MOSTRAR NOMBRE METODO MAS ADELANTE!!!
            {
                targets: "imagen_metodo:name",
                width: '15%',
                searchable: true,
                orderable: true,
                className: "text-center",
                render: function(data, type, row) {
                    if (type === "display") {
                        const rutaBase = '../../public/img/';  // Ruta base general para las imágenes
                        let rutaImagen;

                        // Verificar si la imagen es la predeterminada
                        if (row.imagen_metodo === 'default_method.png' || !row.imagen_metodo) {
                            // Si es la predeterminada, usar la ruta en img
                            rutaImagen = rutaBase + 'default_method.png';
                        } else {
                            // Si es una imagen personalizada, usar la ruta en metodos
                            rutaImagen = rutaBase + 'metodos/' + row.imagen_metodo;
                        }
                        
                        // Contenedor principal
                        let html = `<div style="display: flex; justify-content: center; align-items: center; height: 100%; padding: 5px;">`;

                        if (rutaImagen) {
                            html += `
                            <img src="${rutaImagen}" 
                                alt="${row.imagen_metodo || 'Ícono método'}"
                                style="width: 28px; height: 28px; object-fit: contain; transition: all 0.2s ease; filter: drop-shadow(0 2px 2px rgba(0,0,0,0.1));"
                                onmouseover="this.style.transform='scale(1.2)'; this.style.filter='drop-shadow(0 3px 3px rgba(0,0,0,0.2))'"
                                onmouseout="this.style.transform='scale(1)'; this.style.filter='drop-shadow(0 2px 2px rgba(0,0,0,0.1))'"
                                onerror="this.onerror=null; this.src='${rutaBase}default_method.png'; this.style.width='28px'; this.style.height='28px';"
                                title="${row.imagen_metodo || 'Método de contacto'}">
                            `;
                        } else {
                            html += `
                            <div style="
                                width: 28px;
                                height: 28px;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                background: #f8f9fa;
                                border-radius: 50%;
                                color: #6c757d;
                            ">
                                <i class="fas fa-question-circle" style="font-size: 14px;"></i>
                            </div>`;
                        }

                        html += `</div>`;
                        return html;
                    }
                    return data;
                }
            },
            {// 5 - BOTON PARA ACTIVAR/DESACTIVAR ESTADO
                   
                targets: 'activar:name', width: '10%', searchable: false, orderable: false, class: "text-center",
                render: function (data, type, row) {
                    if (row.id_metodo == 2) {
                        // Si es método 2 (llamada), botón deshabilitado
                        if (row.estado == 1) {
                            return `<button type="button" class="btn btn-danger btn-sm" disabled
                                        data-bs-toggle="tooltip-primary" data-placement="top" title="Desactivar"
                                        data-original-title="Tooltip on top" data-id_metodo="${row.id_metodo}"> 
                                        <i class="fa-solid fa-trash"></i>
                                    </button>`;
                        } else {
                            return `<button class="btn btn-success btn-sm" disabled
                                        data-bs-toggle="tooltip-primary" data-placement="top" title="Activar"
                                        data-original-title="Tooltip on top" data-id_metodo="${row.id_metodo}">
                                        <i class="bi bi-hand-thumbs-up-fill"></i>
                                    </button>`;
                        }
                    } else {
                        // Para otros métodos, el botón funciona normalmente
                        if (row.estado == 1) {
                            return `<button type="button" class="btn btn-danger btn-sm desacEstado"
                                        data-bs-toggle="tooltip-primary" data-placement="top" title="Desactivar"
                                        data-original-title="Tooltip on top" data-id_metodo="${row.id_metodo}"> 
                                        <i class="fa-solid fa-trash"></i>
                                    </button>`;
                        } else {
                            return `<button class="btn btn-success btn-sm activarEstado"
                                        data-bs-toggle="tooltip-primary" data-placement="top" title="Activar"
                                        data-original-title="Tooltip on top" data-id_metodo="${row.id_metodo}">
                                        <i class="bi bi-hand-thumbs-up-fill"></i>
                                    </button>`;
                        }
                    }
                }
            },//
            // 6 - BOTON PARA EDITAR METODO
            {   
                targets: 'editar:name', width: '10%', searchable: false, orderable: false, class: "text-center",
                render: function (data, type, row) {
                    return `<button type="button" class="btn btn-info btn-sm editarMetodo" data-toggle="tooltip-primary" data-placement="top" title="Editar"  
                             data-id_metodo="${row.id_metodo}"> 
                             <i class="fa-solid fa-edit"></i>
                             </button>`
                } // de la function
            }
             // De la columna 9
        ], // de la columnDefs
        ajax: {
            url: '../../controller/metodos.php?op=listar',
            type: 'GET',
            dataSrc: function (json) {
                console.log("JSON recibido:", json); // 📌 Ver qué estructura tiene
                return json.data || json; // Ajusta en función de lo recibido
            }
        } // del ajax
    }; // de la variable datatable_companiesConfig
    ////////////////////////////
    // FIN DE LA TABLA DE    //
    ///////////////////////////


    /************************************/
    //     ZONA DE DEFINICIONES        //
    /**********************************/
    // Definición inicial de la tabla de empleados
    var $table = $('#metodos_data');  /*<--- Es el nombre que le hemos dado a la tabla en HTML */
    var $tableConfig = datatable_metodosConfig; /*<--- Es el nombre que le hemos dado a la declaración de la definicion de la tabla */
    //var $columSearch = 3; /* <-- Es la columna en la cual al hacer click el valor se colocará en la zona de search y se buscará */
    var $tableBody = $('#metodos_data tbody'); /*<--- Es el nombre que le hemos dado al cuerpo de la tabla en HTML */
    /* en el tableBody solo cambiar el nombre de la tabla que encontraremos en HTML*/
    var $columnFilterInputs = $('#metodos_data tfoot input'); /*<--- Es el nombre que le hemos dado a los inputs de los pies de la tabla en HTML */
    /* en el $columnFilterInputs solo cambiar el nombre de la tabla que encontraremos en HTML*/

    //ejemplo -- var table_e = $('#employees-table').DataTable(datatable_employeeConfig);
    var table_e = $table.DataTable($tableConfig);

    /************************************/
    //   FIN ZONA DE DEFINICIONES      //
    /**********************************/

    function format(d) {
        console.log(d);
    
        return `
            <div class="card border-primary mb-3" style="overflow: visible;">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-gear-fill fs-3 me-2"></i>
                        <h5 class="card-title mb-0">Detalles del Método</h5>
                    </div>
                </div>
                <div class="card-body p-0" style="overflow: visible;">
                    <table class="table table-borderless table-striped table-hover mb-0">
                        <tbody>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-hash me-2"></i>Id Método
                                </th>
                                <td class="pe-4">
                                    ${d.id_metodo || '<span class="text-muted fst-italic">No tiene id método</span>'}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer bg-transparent border-top-0 text-end">
                    <small class="text-muted">Actualizado: ${new Date().toLocaleDateString()}</small>
                </div>
            </div>
        `;
    }
    
        // NO TOCAR, se configura en la parte superior --> funcion format(d)
        $tableBody.on('click', 'td.details-control', function () {
            var tr = $(this).closest('tr');
            var row = table_e.row(tr);
    
            if (row.child.isShown()) {
                // Esta fila ya está abierta, la cerramos
                row.child.hide();
                tr.removeClass('shown');
            } else {
                // Abrir esta fila
                row.child(format(row.data())).show();
                tr.addClass('shown');
            }
        });


    ////////////////////////////////////////////
    //   INICIO ZONA FUNCIONES DE APOYO      //
    //////////////////////////////////////////

    /////////////////////////////////////
    //   INICIO ZONA DELETE PERMITIR ADJUNTOS  //
    ///////////////////////////////////
    function desacEstado(id) {
        swal.fire({
            title: 'Desactivar',
            text: `¿Desea desactivar el estado del método con ID ${id}?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Si',
            cancelButtonText: 'No',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                $.post("../../controller/metodos.php?op=eliminar", { id_metodo: id }, function (data) { // Cambiado a prod_id

                    $table.DataTable().ajax.reload();

                    swal.fire(
                        'Desactivado',
                        'El método ha sido desactivado',
                        'success'
                    )
                });
            }
        })
    }

    // CAPTURAR EL CLICK EN EL BOTÓN DE BORRAR
    $(document).on('click', '.desacEstado', function (event) {
        event.preventDefault();
        let id = $(this).data('id_metodo'); // Cambiado de data('id') a data('prod_id')
        desacEstado(id);
    });
    ////////////////////////////////////
    //   FIN ZONA DELETE METODO    //
    //////////////////////////////////

    ///////////////////////////////////////
    //   INICIO ZONA ACTIVAR METODO  //
    /////////////////////////////////////
    function activarMetodo(id) {
        swal.fire({
            title: 'Activar',
            text: `¿Desea activar el estado del método con ID ${id}?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Si',
            cancelButtonText: 'No',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                $.post("../../controller/metodos.php?op=activar", { id_metodo: id }, function (data) {

                    $table.DataTable().ajax.reload();

                    swal.fire(
                        'Activado',
                        'El método ha sido activado',
                        'success'
                    )
                });
            }
        })
    }


    // CAPTURAR EL CLICK EN EL BOTÓN DE ACTIVAR
    $(document).on('click', '.activarEstado', function (event) { // Sin acento
        event.preventDefault();
        let id = $(this).data('id_metodo');
        activarMetodo(id);
    });
    ////////////////////////////////////
    //   FIN ZONA ACTIVAR METODO    //
    //////////////////////////////////

       ////////////////////////////////////
    //   TRATAMIENTO DE LA IMAGEN    //
    //////////////////////////////////

    // Función para limpiar la imagen seleccionada
    /*
    $("#btnLimpiarImagen").click(function () {
        limpiarInputImagen();
    });
    */
/**
 * Crea un elemento de vista previa para una imagen con opciones de eliminación
 * ruta - Ruta de la imagen a mostrar
 * esPredeterminada - Indica si es la imagen por defecto
 * imageName - Nombre del archivo de imagen
 * boolean esTemporal - Indica si la imagen es temporal (no guardada en servidor)
 * returns jQuery Elemento jQuery con la vista previa
 */
function crearPreviewItem(ruta, esPredeterminada = false, imageName = '', esTemporal = false) {
    // Crear contenedor principal para la vista previa
    const previewItem = $('<div>').addClass('preview-item position-relative').css({
        width: '150px', height: '150px', border: '1px solid #ddd',
        borderRadius: '4px', overflow: 'hidden', cursor: 'pointer'
    });

    // Añadir elemento img con la imagen
    previewItem.append(
        $('<img>').attr({ src: ruta, alt: 'Vista previa', title: 'Clic para ver' })
        .css({ width: '100%', height: '100%', objectFit: 'cover' })
    );

    // Si no es imagen predeterminada, añadir botón de eliminar
    if (!esPredeterminada) {
        const deleteBtn = $(`
            <button type="button" 
                    class="remove-image-btn" 
                    data-image-name="${imageName}" 
                    data-temporal="${esTemporal}" 
                    title="Eliminar imagen"
                    style="position: absolute; top: 5px; right: 5px; background: #dc3545; border: none; color: white; width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 16px; padding: 0; cursor: pointer;">
                <i class="fa fa-times"></i>
            </button>
        `);
        previewItem.append(deleteBtn);
    }

    // Manejar clic en botón de eliminar
    previewItem.on('click', '.remove-image-btn', function (event) {
        event.preventDefault();
        const isTemporal = $(this).data('temporal');
        const imageName = $(this).data('image-name');
        const id_metodo = $('#modalMetodo .modal-body #id_metodo').val();
        $('#formMetodo').find('input[name="imagen_metodo"]').removeClass('is-invalid is-valid');

        if (isTemporal) {
            // Eliminar imagen temporal: limpiar input y mostrar imagen predeterminada
            limpiarInputImagen();
            cargarImagenInicial('../../public/img/default_method.png', true, 'default_method.png');
        } else {
            // Eliminar imagen guardada en servidor
            borrarImagen(id_metodo, imageName);
        }
    });

    return previewItem;
}

    // Función para limpiar el input de imagen
    function limpiarInputImagen() {
        // Limpiar el valor del input file
        $('#formMetodo').find('input[name="imagen_metodo"]').val("");
        // Limpiar la previsualización
        $("#previewImagen").empty();
    }

    // Función para tener en cuenta que estoy nuevo registro
    function HabilitoInputImagenNuevo() {
        // Vamos a "quitar" el campo input file - Solo nuevo
        //$('#btnLimpiarImagen').removeClass('d-none');

        // Quitar el campo de  (files)
        $('#imagen_metodo').removeClass('d-none');
    }
    ////////////////////////////////////
    //               FIN             //
    //   TRATAMIENTO DE LA IMAGEN    //
    //////////////////////////////////


    ///////////////////////////////////////
    //      INICIO ZONA NUEVO           //
    //        BOTON DE NUEVO           // 
    /////////////////////////////////////
    // Mostrar imagen predeterminada al crear nuevo

    
/**
 * Maneja el clic en el botón "Nuevo" para preparar el formulario
 */
$(document).on('click', '#btnnuevo', function (event) {
    event.preventDefault();
    $('#mdltitulo').text('Nuevo registro de método');
    $('#modalMetodo').modal('show');

    // Resetear formulario
    $("#formMetodo")[0].reset();
    $('#formMetodo').find('input[name="id_metodo"]').val("");
    formValidator.clearValidation();
    limpiarInputImagen();
    HabilitoInputImagenNuevo();
    $('#formMetodo').find('input[name="imagen_metodo"]').removeClass('is-invalid is-valid');

    // Mostrar imagen predeterminada
    cargarImagenInicial('../../public/img/default_method.png', true, 'default_method.png');

    // Enfocar primer campo al mostrar el modal
    $('#modalMetodo').on('shown.bs.modal', function () {
        $('#modalMetodo .modal-body #nombre').focus();
    });
});


/**
 * Maneja el clic en el botón "Guardar" para validar y enviar el formulario
 */
$(document).on('click', '#btnsalvar', async function (event) {
    event.preventDefault();
    
    // Obtener valores del formulario
    var idM = $('#formMetodo').find('input[name="id_metodo"]').val().trim();
    var nombreM = $('#formMetodo').find('input[name="nombre"]').val().trim();
    var permiteAdjuntosM = $('#formMetodo').find('input[name="permite_adjuntos"]').is(':checked') ? 1 : 0;
    var inputImagen = $('#formMetodo').find('input[name="imagen_metodo"]')[0];
    var imagenMetodo = inputImagen.files[0];
    
    // Validación para nuevo registro (requiere imagen)
    if (!idM) {
        if (!imagenMetodo) {
            toastr.error('Debe seleccionar una imagen para el nuevo método.');
            $(inputImagen).addClass('is-invalid');
            return;
        }
    } else {
        // Validación para edición (verificar imagen existente)
        try {
            const response = await $.ajax({
                url: "../../controller/metodos.php?op=obtenerImagenMetodo",
                type: "POST",
                data: { id_metodo: idM },
                dataType: "json"
            });
            
            const imagenExistente = response;
            console.log(imagenExistente);
            
            const esImagenPredeterminada = imagenExistente === 'default_method.png' || imagenExistente === null;
            console.log(esImagenPredeterminada);

            // Requerir nueva imagen si la existente es predeterminada
            if (!imagenMetodo && esImagenPredeterminada) {
                toastr.error('Debe seleccionar una imagen distinta a la predeterminada para este método.');
                $(inputImagen).addClass('is-invalid');
                return;
            }
            
            // Validar nueva imagen si se seleccionó
            if (imagenMetodo) {
                const validation = validarImagen(imagenMetodo);
                if (!validation.isValid) {
                    toastr.error(validation.message);
                    $(inputImagen).removeClass('is-valid').addClass('is-invalid');
                    return;
                }
            }
            
        } catch (error) {
            console.error("Error al verificar imagen existente:", error);
            toastr.error('No se pudo verificar la imagen existente del método.');
            return;
        }
    }
    
    // Validar imagen seleccionada
    if (imagenMetodo) {
        const validation = validarImagen(imagenMetodo);
        if (!validation.isValid) {
            toastr.error(validation.message);
            $(inputImagen).removeClass('is-valid').addClass('is-invalid');
            return;
        }
        $(inputImagen).removeClass('is-invalid').addClass('is-valid');
    }
    
    // Validar formulario completo
    const isValid = formValidator.validateForm(event);
    if (!isValid) {
        toastr.error('Por favor, corrija los errores en el formulario.', 'Error de Validación');
        return;
    }
    
    // Preparar datos para enviar
    var datosFormulario = {
        nombre: nombreM,
        permite_adjuntos: permiteAdjuntosM
    };
    
    if (idM) {
        datosFormulario.id_metodo = idM;
    }
    
    var formData = new FormData();
    for (var key in datosFormulario) {
        formData.append(key, datosFormulario[key]);
    }
    
    if (imagenMetodo) {
        formData.append('imagen_metodo', imagenMetodo);
    }
    
    // Enviar datos al servidor
    $.ajax({
        url: "../../controller/metodos.php?op=guardaryeditar",
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        success: function (data) {
            $('#modalMetodo').modal('hide');
            $table.DataTable().ajax.reload();
            $("#formMetodo")[0].reset();
            toastr["success"]("El método ha sido guardado", "Guardado");
        },
        error: function (xhr, status, error) {
            swal.fire(
                'Error',
                'No se pudo guardar el método. ' + (xhr.responseText || error),
                'error'
            );
        }
    });
});
    
    ///////////////////////////////////////
    //      FIN ZONA NUEVO           //
    /////////////////////////////////////


    ///////////////////////////////////////
    //      INICIO ZONA EDITAR           //
    //        BOTON DE EDITAR           //
    /////////////////////////////////////

/**
 * Maneja el clic en botón "Editar" para cargar datos existentes
 */
$(document).on('click', '.editarMetodo', function (event) {
    event.preventDefault();

    $('#formMetodo').find('input[name="id_metodo"]').val("");
    formValidator.clearValidation();

    let id = $(this).data('id_metodo');

    // Obtener datos del método a editar
    $.post("../../controller/metodos.php?op=mostrar", { id_metodo: id }, function (data) {
        if (data) {
            // Parsear datos si vienen como string
            if (typeof data === 'string') {
                try {
                    data = JSON.parse(data);
                } catch (e) {
                    console.error('Error al parsear JSON:', e);
                }
            }

            $('#mdltitulo').text('Edición registro metodo');
            $('#modalMetodo').modal('show');

            // Rellenar campos del formulario
            $('#modalMetodo .modal-body #id_metodo').val(data.id_metodo);
            $('#modalMetodo .modal-body #nombre').val(data.nombre);
            $('#modalMetodo .modal-body input[name="permite_adjuntos"]').prop('checked', data.permite_adjuntos == 1);
            $('#formMetodo').find('input[name="imagen_metodo"]').removeClass('is-invalid is-valid');

            // Cargar imagen (predeterminada o específica)
            let rutaBase = '../../public/img/';
            let rutaImagen = (data.imagen_metodo && data.imagen_metodo !== 'default_method.png') 
                             ? '../../public/img/metodos/' + data.imagen_metodo 
                             : rutaBase + 'default_method.png';
            let esDefault = (data.imagen_metodo === 'default_method.png');

            cargarImagenInicial(rutaImagen, esDefault, data.imagen_metodo);
        } else {
            console.error('Error: Datos no encontrados');
        }
    }).fail(function (xhr, status, error) {
        console.error('Error en la solicitud AJAX:', status, error);
        console.error('Respuesta del servidor:', xhr.responseText);
    });
});

/**
 * Carga una imagen en el área de vista previa
 * url - Ruta de la imagen
 * esPredeterminada - Indica si es imagen predeterminada
 * imageName - Nombre del archivo
 * esTemporal - Indica si es temporal
 */
function cargarImagenInicial(url, esPredeterminada = false, imageName = '', esTemporal = false) {
    const preview = crearPreviewItem(url, esPredeterminada, imageName, esTemporal);
    $('#previewImagen').html(preview);

    if (esPredeterminada) {
        $('#imagen_metodo').val('');
    }
}

/**
 * Valida un archivo de imagen
 * file - Archivo de imagen a validar
 * returns Object Objeto con isValid y message
 */
function validarImagen(file) {
    // Validar existencia de archivo
    if (!file) {
        return {
            isValid: false,
            message: "No se ha seleccionado ninguna imagen."
        };
    }

    // Tipos MIME permitidos
    var allowedMimes = ["image/jpg", "image/jpeg", "image/png"];

    // Validar tipo de archivo
    if (allowedMimes.indexOf(file.type) === -1) {
        return {
            isValid: false,
            message: "El archivo seleccionado no es un tipo de imagen permitido."
        };
    }

    // Validar tamaño máximo (2MB)
    if (file.size > 2 * 1024 * 1024) {
        return {
            isValid: false,
            message: "El tamaño de la imagen supera el límite permitido (2 MB)."
        };
    }

    // Validar que no sea la imagen predeterminada
    if (file.name.toLowerCase() === 'default_method.png') {
        return {
            isValid: false,
            message: "No se permite usar la imagen predeterminada como imagen de método."
        };
    }

    return { isValid: true };
}


/**
 * Maneja el cambio en el input de imagen
 */
$('#imagen_metodo').on('change', function () {
    var file = this.files[0];
    
    // Validar imagen seleccionada
    const validation = validarImagen(file);
    
    if (!validation.isValid) {
        Swal.fire({
            title: "Error",
            text: validation.message,
            icon: "error"
        });
        $(this).val('');
        $(this).removeClass('is-valid').addClass('is-invalid');
        return;
    }

    // Mostrar vista previa si la imagen es válida
    var reader = new FileReader();
    reader.onload = function (e) {
        cargarImagenInicial(e.target.result, false, file.name, true);
    };
    reader.readAsDataURL(file);

    $(this).removeClass('is-invalid').addClass('is-valid');
});


/**
 * Elimina una imagen del servidor
 * id_metodo - ID del método
 * imageName - Nombre de la imagen a borrar
 */
function borrarImagen(id_metodo, imageName) {
    swal.fire({
        title: 'Borrar',
        text: `¿Desea BORRAR la imagen ${imageName}?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Si',
        cancelButtonText: 'No',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            $.post("../../controller/metodos.php?op=borrarImagen", { 
                id_metodo: id_metodo, 
                imageName: imageName 
            })
            .done(function(data) {
                // Actualizar tabla y mostrar notificación
                $table.DataTable().ajax.reload();
                swal.fire('Borrada', 'La imagen ha sido borrada', 'success');

                // Recargar datos y vista previa
                $.post("../../controller/metodos.php?op=mostrar", { id_metodo: id_metodo }, function(newData) {
                    try {
                        if (typeof newData === 'string') {
                            newData = JSON.parse(newData);
                        }

                        let ruta = newData.imagen_metodo && newData.imagen_metodo !== 'default_method.png'
                                   ? '../../public/img/metodos/' + newData.imagen_metodo
                                   : '../../public/img/default_method.png';
                        let esDefault = (newData.imagen_metodo === 'default_method.png');

                        cargarImagenInicial(ruta, esDefault, newData.imagen_metodo);

                    } catch (e) {
                        console.error('Error al procesar respuesta:', e);
                        $("#previewImagen").html('<p class="text-danger">Error al cargar imagen</p>');
                    }
                });
            })
            .fail(function(xhr, status, error) {
                swal.fire(
                    'Error',
                    'No se pudo borrar la imagen. ' + error + '. Respuesta del servidor: ' + xhr.responseText,
                    'error'
                );
            });
        }
    });
}

    
/**
 * Maneja el clic en botón de eliminar imagen
 */
$("#previewImagen").on('click', '.remove-image-btn', function (event) {
    event.preventDefault();
    let imageName = $(this).data('image-name');
    let id_metodo = $('#modalMetodo .modal-body #id_metodo').val();
    
    borrarImagen(id_metodo, imageName);
});

    ///////////////////////////////////////
    //        FIN ZONA EDITAR           //
    /////////////////////////////////////


    ////////////////////////////////////////////////////////
    //        ZONA FILTROS RADIOBUTTON CABECERA           //
    ///////////////////////////////////////////////////////
    // Escuchar cambios en los radio buttons
    // Si es necesario filtrar por texto en lugar de valores numéricos, hay que asegurarse que los valores de los radio buttons coincidan con los valores de la columna.
    $('input[name="filterStatus"]').on('change', function () {
        var value = $(this).val(); // Obtener el valor seleccionado

        if (value === "all") {
            // Si se selecciona "Todos", limpiar el filtro
            table_e.column(3).search("").draw(); // Cambiar numero por el índice de la columna a filtrar
        } else {
            // Filtrar la columna por el valor seleccionado
            table_e.column(3).search(value).draw(); // Cambia numero por el índice de la columna a filtrar

        }
    });
    ////////////////////////////////////////////////////////////
    //        FIN ZONA FILTROS RADIOBUTTON CABECERA          //
    //////////////////////////////////////////////////////////

    /*********************************************************** */
    /********************************************************** */
    /* A PARTIR DE AQUI NO TOCAR  SE ACTUALIZA AUTOMATICAMENTE */
    /******************************************************** */
    /******************************************************* */

    //ejemplo -- var table_e = $('#employees-table').DataTable(datatable_employeeConfig);
    //var table_e = $table.DataTable($tableConfig);

    /////////////////////////////////////
    //  INICIO ZONA CLICS COLUMNA     //
    //    NO ES NECESARIO TOCAR      // 
    //////////////////////////////////
    //Código para capturar clics solo en la tercera columna (edad) y filtrar DataTables
    // El resto no responden al clic
    //ejemplo - $('#employees-table tbody').on('click', 'td', function () {

    // En caso de no querer que se filtre por columna se puede comentar o eliminar

    /*  En este caso no deseamos buscar por ninguna columna al hacer clic
        $tableBody.on('click', 'td', function () {
            var cellIndex = table_e.cell(this).index().column; // Índice real de la columna en DataTables
     
            // ejemplo - if (cellIndex === 3) { // Asegúrarse de que es la columna 'edad' 
            if (cellIndex === $columSearch) { // Asegúrarse de que es la columna 'edad' 
                var cellValue = $(this).text().trim();
                table_e.search(cellValue).draw();
                updateFilterMessage(); // Actualizar el mensaje cuando se aplique el filtro
            }
        });
    */
    /////////////////////////////////////
    //  FIN ZONA CLICS COLUMNA     //
    ///////////////////////////////////

   ////////////////////////////////////////////
    //  INICIO ZONA FILTROS PIES y SEARCH     //
    //    NO ES NECESARIO TOCAR              //
    //     FUNCIONES NO TOCAR               // 
    ///////////////////////////////////////////

    /* IMPORTANTE ---- IMPORTANTE ---- IMPORTANTE ---- IMPORTANTE */
    /* Si algún campo no quiere que se habilite en el footer la busqueda, 
    bastará con poner en el columnDefs -- > searchable: false */

    // Filtro de cada columna en el pie de la tabla de empleados (tfoot)
    // ejemplo - $('#employees-table tfoot input').on('keyup', function () {
        $columnFilterInputs.on('keyup', function () {
            var columnIndex = table_e.column($(this).closest('th')).index(); // Obtener el índice de la columna del encabezado correspondiente
            var searchValue = $(this).val(); // Obtener el valor del campo de búsqueda
    
            // Aplicar el filtro a la columna correspondiente
            table_e.column(columnIndex).search(searchValue).draw();
    
            // Actualizar el mensaje de filtro
            updateFilterMessage();
        });
    
        // Función para actualizar el mensaje de filtro activo
        function updateFilterMessage() {
            var activeFilters = false;
    
            // Revisamos si hay algún filtro activo en cualquier columna
            $columnFilterInputs.each(function () {
                if ($(this).val() !== "") {
                    activeFilters = true;
                    return false; // Si encontramos un filtro activo, salimos del loop
                }
            });
    
            // Revisamos si hay un filtro activo en la búsqueda global
            if (table_e.search() !== "") {
                activeFilters = true;
            }
    
            // Muestra u oculta el mensaje "Hay un filtro activo"
            if (activeFilters) {
                $('#filter-alert').show();
            } else {
                $('#filter-alert').hide();
            }
        }
    
        // Esto es solo valido para la busqueda superior //
        table_e.on('search.dt', function () {
            updateFilterMessage(); // Actualizar mensaje de filtro
        });
        ////////////////////////////////////////////////////////
    
        // Botón para limpiar los filtros y ocultar el mensaje ////////////////////////////////////////////
        $('#clear-filter').on('click', function () {
            //console.log('Limpiando filtros...');
            table_e.destroy();  // Destruir la tabla para limpiar los filtros
    
            // Limpiar los campos de búsqueda del pie de la tabla
            // ejemplo - $('#employees-table tfoot input').each(function () {
            $columnFilterInputs.each(function () {
                //console.log('Campo:', $(this).attr('placeholder'), 'Valor antes:', $(this).val());
                $(this).val('');  // Limpiar cada campo input del pie y disparar el evento input
                //console.log('Valor después:', $(this).val());
            });
    
            table_e = $table.DataTable($tableConfig);
    
            // Ocultar el mensaje de "Hay un filtro activo"
            $('#filter-alert').hide();
        });
        ////////////////////////////////////////////
        //  FIN ZONA FILTROS PIES y SEARCH     //
        ///////////////////////////////////////////
    }); // de document.ready


    // Función global para formatear fecha al formato europeo
function formatoFechaEuropeo(fechaString) {
    if (!fechaString) return 'Sin fecha';
    
    try {
        const fecha = new Date(fechaString);
        if (isNaN(fecha.getTime())) return 'Fecha inválida';
        
        const dia = fecha.getDate().toString().padStart(2, '0');
        const mes = (fecha.getMonth() + 1).toString().padStart(2, '0');
        const año = fecha.getFullYear();
        
        return `${dia}/${mes}/${año}`;
    } catch (error) {
        console.error('Error al formatear fecha:', error);
        return 'Error en fecha';
    }
}