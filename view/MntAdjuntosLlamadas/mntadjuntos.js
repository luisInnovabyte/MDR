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

    /* DATEPICKER DE FILTRO DATATABLES */
    // Aplicar la máscara para fecha dd/mm/yyyy
    //9 : numeric
    //a: alphabetical
    // * : alphanumeric

    // FILTRO FECHA INICIO DECLARACIÓN

    $('#filtroFechaSubida').inputmask('99-99-9999');
    // NO FUNCIONA - Muestra la máscara pero no permite escribir.
    //$('#prod_telefono').inputmask('(+99) 999-999-999');


    // Configura el datepicker en español
    $.datepicker.setDefaults($.datepicker.regional['es']);

    $('#filtroFechaSubida').datepicker({
        showAnim: "slideDown",
        dateFormat: 'dd-mm-yy',
        showOtherMonths: true,
        selectOtherMonths: true,
        numberOfMonths: 1
    });

// 1. Primero configura el regional en español
$.timepicker.regional['es'] = {
    timeOnlyTitle: 'Seleccionar hora',
    timeText: 'Hora',
    hourText: 'Horas',
    minuteText: 'Minutos',
    secondText: 'Segundos',
    millisecText: 'Milisegundos',
    currentText: 'Ahora',
    closeText: 'Cerrar',
    timeFormat: 'HH:mm:ss',
    amNames: ['a.m.', 'AM', 'A'],
    pmNames: ['p.m.', 'PM', 'P'],
    ampm: false,
    monthNames: ['Enero','Febrero','Marzo','Abril','Mayo','Junio',
                'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
    monthNamesShort: ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'],
    dayNames: ['Domingo','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado'],
    dayNamesShort: ['Dom','Lun','Mar','Mié','Jue','Vie','Sáb'],
    dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','Sá'],
    firstDay: 1
};

// 2. Aplica la configuración regional ANTES de inicializar el datetimepicker
$.timepicker.setDefaults($.timepicker.regional['es']);

// 3. Configuración del datetimepicker
$('#fecha_subida').datetimepicker({
    dropdownParent: $("#modalAdjuntos"),
    showAnim: "slideDown",
    dateFormat: 'dd-mm-yy',
    timeFormat: 'HH:mm:ss',
    controlType: 'select',
    showSecond: true,
    stepSecond: 1,
    showOtherMonths: true,
    selectOtherMonths: true,
    numberOfMonths: 1,
    hourMin: 0,
    hourMax: 23,
    // Asegúrate de incluir estas configuraciones adicionales para español
    monthNames: $.timepicker.regional['es'].monthNames,
    monthNamesShort: $.timepicker.regional['es'].monthNamesShort,
    dayNames: $.timepicker.regional['es'].dayNames,
    dayNamesShort: $.timepicker.regional['es'].dayNamesShort,
    dayNamesMin: $.timepicker.regional['es'].dayNamesMin,
    firstDay: $.timepicker.regional['es'].firstDay,
    prevText: '&#x3C;Ant',
    nextText: 'Sig&#x3E;',
    currentText: 'Hoy',
    closeText: 'Cerrar'
});


    var formValidator = new FormValidator('formAdjunto', {
        id_llamada: {
            required: true
        },
        nombre_archivo: {
            required: true
        },
        tipo: {
            required: true
        },
        fecha_subida: {
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
    // INICIO DE LA TABLA DE VACACIONES //
    //         DATATABLES             //
    ///////////////////////////////////
    var datatable_adjuntosConfig = {
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

            // Son los botones para más
            // No tocar
            { name: 'control', data: null, defaultContent: '', className: 'details-control sorting_1 text-center' }, // Columna 0: Mostrar más
            { name: 'id_adjunto', data: 'id_adjunto', visible: false, className: 'text-center'}, // Columna 0: ID_ADJUNTO
            { name: 'id_llamada', data: 'id_llamada', className: 'text-center' }, // Columna 1: ID_LLAMADA
            { name: 'nombre_archivo', data: 'nombre_archivo', className: 'text-center' }, // Columna 2: NOMBRE_ARCHIVO
            { name: 'tipo', data: 'tipo', className: 'text-center' }, // Columna 3: TIPO
            { name: 'fecha_subida', data: 'fecha_subida', className: 'text-center' }, // Columna 4: FECHA_SUBIDA
            { name: 'estado', data: 'estado', className: 'text-center'}, // Columna 5: ESTADO
            { name: 'ver', data: null, defaultContent: '', className: 'text-center' },  // Columna 6: VER DOCUMENTO/IMAGEN
            { name: 'activar', data: null, defaultContent: '', className: 'text-center' },  // Columna //  7: ACTIVAR
            //{ name: 'editar', data: null, defaultContent: '', className: 'text-center' },  // Columna 8: EDITAR
            
        ], // de las columnas
        columnDefs: [
            // Cuidado que el ordrData puede interferir con el ordenamiento de la tabla    
            // BOTON MAS
            { targets: 'control:name', width: '5%', searchable: false, orderable: false, className: "text-center" },
            // id_adjunto   
            { targets: "id_adjunto:name", width: '20%', searchable: true, orderable: true },
            // id_llamada MOSTRAR NOMBRE COMUNICANTE MAS ADELANTE
            {
                targets: "id_llamada:name",
                width: '10%',
                searchable: true,
                orderable: true,
                className: "text-center",
                render: function(data, type, row) {
                    if (type === "display" || type === "filter") {
                        return row.nombre_comunicante;
                    }
                    return data; 
                }
            },
            // nombre_archivo
            { targets: "nombre_archivo:name", width: '15%', searchable: true, orderable: true },
            // tipo
            { targets: "tipo:name", width: '15%', searchable: true, orderable: true },
            // fecha_subida
            {
                targets: "fecha_subida:name",
                width: '15%',
                searchable: true,
                orderable: true,
                className: "text-center",
                render: function(data, type, row) {
                    if (type === "display" || type === "filter") {
                        return formatoFechaEuropeo(data); // Muestra "DD-MM-YYYY"
                    }
                    return data; 
                }
            },
            // estado
            {
                targets: "estado:name", width: '10%', orderable: true, searchable: true, className: "text-center",
                render: function (data, type, row) {
                    if (type === "display") {
                        return data == 1 ? '<i class="bi bi-check-circle text-success fa-2x"></i>' : '<i class="bi bi-x-circle text-danger fa-2x"></i>';
                    }
                    return data;
                }
            },
            {   // BOTON PARA VER DOCUMENTO
                targets: "ver:name", 
                width: '5%', 
                searchable: false, 
                orderable: false, 
                className: "text-center",
                render: function (data, type, row) {
                    return `<button type="button" class="btn btn-primary btn-sm verAdjunto" 
                            data-bs-toggle="tooltip" 
                            data-placement="top" 
                            title="Ver documento"
                            data-id_adjunto="${row.id_adjunto}"
                            data-nombre-archivo="${row.nombre_archivo}"
                            data-tipo="${row.tipo}">
                            <i class="fa-solid fa-eye"></i>
                            </button>`;
                }
            },
            {   // BOTON PARA ACTIVAR/DESACTIVAR ADJUNTO
                targets: "activar:name", width: '5%', searchable: false, orderable: false, class: "text-center",
                render: function (data, type, row) {
                    // El nombre que de la variable que se pasa por data-xxx debe ser el mismo que el nombre de la columna en la base de datos
                    if (row.estado == 1) {
                        // permito desactivar el estado
                        return `<button type="button" class="btn btn-danger btn-sm desacAdjunto" data-bs-toggle="tooltip-primary" data-placement="top" title="Desactivar" data-original-title="Tooltip on top" 
                             data-id_adjunto="${row.id_adjunto}"> 
                             <i class="fa-solid fa-trash"></i>
                             </button>`}
                    else {
                        // debo permitir activar de nuevo el estado
                        return `<button class="btn btn-success btn-sm activarAdjunto" data-bs-toggle="tooltip-primary" data-placement="top" title="Activar" data-original-title="Tooltip on top" 
                             data-id_adjunto="${row.id_adjunto}"> <!-- Cambiado de data-id a data-prod_id -->
                             <i class="bi bi-hand-thumbs-up-fill"></i>
                            </button>`}
                } // de la function
            },// 
            /*{   // BOTON PARA EDITAR ADJUNTO
                targets: "editar:name", width: '5%', searchable: false, orderable: false, class: "text-center",
                render: function (data, type, row) {
                    // El nombre que de la variable que se pasa por data-xxx debe ser el mismo que el nombre de la columna en la base de datos
                    // botón editar el producto
                    return `<button type="button" class="btn btn-info btn-sm editarAdjunto" data-toggle="tooltip-primary" data-placement="top" title="Editar"  
                             data-id_adjunto="${row.id_adjunto}"> 
                             <i class="fa-solid fa-edit"></i>
                             </button>`
                } // de la function
            },*/
             // De la columna 9
        ], // de la columnDefs
        ajax: {
            url: '../../controller/adjuntos.php?op=listar',
            type: 'GET',
            dataSrc: function (json) {
                console.log("JSON recibido:", json); // 📌 Ver qué estructura tiene
                return json.data || json; // Ajusta en función de lo recibido
            },
            
        }, // del ajax
        order: [["id_llamada:name", 'asc']], // ordenar por la columna 3
        rowGroup: {
            dataSrc: function (row) {
                return row.id_llamada + " | " + row.nombre_comunicante;
            },
            startRender: function (rows, group) {
                let $row = $('<tr/>').append('<td colspan="9" class="group-header">' + group + ' / ' + rows.count() + ' adjuntos' + '</td>');
                console.log("Fila creada:", $row[0].outerHTML); // Imprime el HTML de la fila);
                return $row;
            } // de la function startRender
        } // de la rowGroup
    }; // de la variable datatable_companiesConfig
    ////////////////////////////
    // FIN DE LA TABLA DE    //
    ///////////////////////////


    /************************************/
    //     ZONA DE DEFINICIONES        //
    /**********************************/
    // Definición inicial de la tabla de empleados
    var $table = $('#adjuntos_data');  /*<--- Es el nombre que le hemos dado a la tabla en HTML */
    var $tableConfig = datatable_adjuntosConfig; /*<--- Es el nombre que le hemos dado a la declaración de la definicion de la tabla */
    //var $columSearch = 3; /* <-- Es la columna en la cual al hacer click el valor se colocará en la zona de search y se buscará */
    var $tableBody = $('#adjuntos_data tbody'); /*<--- Es el nombre que le hemos dado al cuerpo de la tabla en HTML */
    /* en el tableBody solo cambiar el nombre de la tabla que encontraremos en HTML*/
    var $columnFilterInputs = $('#adjuntos_data tfoot input'); /*<--- Es el nombre que le hemos dado a los inputs de los pies de la tabla en HTML */
    /* en el $columnFilterInputs solo cambiar el nombre de la tabla que encontraremos en HTML*/

    //ejemplo -- var table_e = $('#employees-table').DataTable(datatable_employeeConfig);
    var table_e = $table.DataTable($tableConfig);

    /************************************/
    //   FIN ZONA DE DEFINICIONES      //
    /**********************************/

    $('#fechaSubidaFiltroPie').on('keypress', function (event) {
        var charCode = event.which || event.keyCode;
        var char = String.fromCharCode(charCode);
        var currentValue = $(this).val();
        var cursorPos = this.selectionStart;

        // Permitir números (0-9)
        if (charCode >= 48 && charCode <= 57) return;

        // Permitir guion (-) sólo en la parte de fecha y si no hay guion repetido
        if (char === '-') {
            // No permitir guion después de otro guion
            if (cursorPos > 0 && currentValue.charAt(cursorPos - 1) === '-') {
                event.preventDefault();
            }
            // No permitir guion después del espacio (no debe ir en hora)
            if (currentValue.includes(' ') && cursorPos > currentValue.indexOf(' ')) {
                event.preventDefault();
            }
            return;
        }

        // Permitir espacio sólo 1 vez para separar fecha y hora
        if (char === ' ') {
            if (currentValue.includes(' ')) {
                event.preventDefault();
            }
            return;
        }

        // Permitir dos puntos (:) sólo en la parte de hora (después del espacio)
        if (char === ':') {
            var spaceIndex = currentValue.indexOf(' ');
            // No permitir si no hay espacio o si el cursor está antes del espacio
            if (spaceIndex === -1 || cursorPos <= spaceIndex) {
                event.preventDefault();
            }
            // No permitir dos puntos repetidos seguidos
            if (cursorPos > 0 && currentValue.charAt(cursorPos - 1) === ':') {
                event.preventDefault();
            }
            return;
        }

        // Bloquear todo lo demás
        event.preventDefault();
    });

    $('#fechaSubidaFiltroPie').on('input', function () {
        // Eliminar caracteres que no sean números, guion, espacio o dos puntos
        this.value = this.value.replace(/[^0-9\- :]/g, '');

        // Evitar guiones o dos puntos repetidos
        this.value = this.value
            .replace(/--+/g, '-')
            .replace(/::+/g, ':')
            .replace(/  +/g, ' ');

        // Validar formato hora (hh:mm:ss) si hay parte de hora
        var parts = this.value.split(' ');
        if (parts.length === 2) {
            var time = parts[1];
            // Limitar a máximo 8 caracteres (hh:mm:ss)
            if (time.length > 8) {
                parts[1] = time.substring(0, 8);
                this.value = parts.join(' ');
            }
            // Validar formato básico con regex
            var timeRegex = /^(\d{0,2})(:?)(\d{0,2})(:?)(\d{0,2})$/;
            if (!timeRegex.test(parts[1])) {
                parts[1] = parts[1].replace(/[^0-9:]/g, ''); // eliminar lo que no sea número o :
                this.value = parts.join(' ');
            }
        }
    });

    function format(d) {
        console.log(d);
    
        const rutaBase = '../../public/documentos/adjuntos/';
        const extension = d.nombre_archivo?.split('.').pop().toLowerCase();
        const nombreArchivo = d.nombre_archivo?.trim();
        const tipoAdjunto = extension === 'pdf' ? 'pdf' : 'imagen';
    
        const vistaArchivo = tipoAdjunto === 'pdf'
            ? `<a href="${rutaBase}${nombreArchivo}" target="_blank" class="btn btn-outline-danger btn-sm">
                    <i class="bi bi-file-earmark-pdf-fill me-2"></i> Ver PDF
               </a>`
            : `<img src="${rutaBase}${nombreArchivo}" 
                     alt="Vista previa" 
                     class="img-fluid rounded border" 
                     style="max-height: 200px; object-fit: contain;">`;
    
        return `
            <div class="card border-primary mb-3" style="overflow: visible;">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-paperclip fs-3 me-2"></i>
                        <h5 class="card-title mb-0">Detalles del Adjunto</h5>
                    </div>
                </div>
                <div class="card-body p-0" style="overflow: visible;">
                    <table class="table table-borderless table-striped table-hover mb-0">
                        <tbody>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-hash me-2"></i>Id Adjunto
                                </th>
                                <td class="pe-4">
                                    ${d.id_adjunto || '<span class="text-muted fst-italic">No tiene id adjunto</span>'}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 align-top">
                                    <i class="bi bi-image-fill me-2"></i>Archivo
                                </th>
                                <td class="pe-4">
                                    ${d.nombre_archivo ? vistaArchivo : '<span class="text-muted fst-italic">No hay archivo disponible</span>'}
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

    // Función para convertir el formato incorrecto al correcto
    function convertirFechaAFormatoISO(fechaStr) {
        if (!fechaStr) return null;
        
        // 1. Intentar con formato dd-mm-yyyy HH:mm:ss
        let match = fechaStr.match(/^(\d{2})-(\d{2})-(\d{4}) (\d{2}:\d{2}:\d{2})$/);
        if (match) {
            return `${match[3]}-${match[2]}-${match[1]} ${match[4]}`;
        }
        
        // 2. Intentar con formato dd-mm-yy HH:mm:ss
         match = fechaStr.match(/^(\d{2})-(\d{2})-(\d{2}) (\d{2}:\d{2}:\d{2})$/);
         if (match) {
            const year = match[3].length === 2 ? '20' + match[3] : match[3];
            return `${year}-${match[2]}-${match[1]} ${match[4]}`;
        }
        
        // 3. Si ya está en formato ISO
        if (/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/.test(fechaStr)) {
            return fechaStr;
        }
        
        console.error('Formato de fecha no reconocido:', fechaStr);
        return null;
    }


    function cargarLlamadasEnSelect(selectId, idLlamadaSeleccionada) {
        $.post("../../controller/llamadas.php?op=listar", function (data) {
            const jsondata = data;
            var select = $(selectId);
            // Limpiar las opciones existentes
            select.empty();
            // Agregar la opción por defecto
            select.append($('<option>', { value: '', text: 'Seleccione una llamada...' }));

            if (data) {
                if (typeof data === 'string') {
                    try {
                        data = JSON.parse(data);
                    } catch (e) {
                        console.error('Error al parsear JSON:', e);
                    }
                }
            }

            $.each(jsondata.data, function (index, llamada) {
                let selected = (idLlamadaSeleccionada !== undefined && idLlamadaSeleccionada !== null && idLlamadaSeleccionada !== '' && llamada.id_llamada == idLlamadaSeleccionada) ? 'selected' : '';
                var optionHtml = '<option value="' + llamada.id_llamada + '" ' + selected + '>' + llamada.id_llamada + " | " + llamada.nombre_comunicante + '</option>';

                select.append(optionHtml);
            });
        }, "json").fail(function (xhr, status, error) {
            console.error("Error al cargar los comerciales:", error);
        });
    }
 
    /////////////////////////////////////
    //   INICIO ZONA DELETE ADJUNTOS  //
    ///////////////////////////////////
    function desacAdjunto(id) {
        swal.fire({
            title: 'Desactivar',
            text: `¿Desea desactivar el adjunto con ID ${id}?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Si',
            cancelButtonText: 'No',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                $.post("../../controller/adjuntos.php?op=eliminar", { id_adjunto: id }, function (data) { // Cambiado a prod_id

                    $table.DataTable().ajax.reload();

                    swal.fire(
                        'Desactivado',
                        'El adjunto ha sido desactivada',
                        'success'
                    )
                });
            }
        })
    }

    // VER ADJUNTO BOTÓN ACCIÓN AL HACER CLICK

    $(document).on('click', '.verAdjunto', function() {
        const nombreArchivo = $(this).data('nombre-archivo');
        const tipo = $(this).data('tipo');
        const rutaBase = '../../public/documentos/adjuntos/';
        const rutaCompleta = rutaBase + encodeURIComponent(nombreArchivo);
        
        $('#nombre-archivo-titulo').text(nombreArchivo);
        $('#descargar-adjunto').attr('href', rutaCompleta);
        
        const $contenedor = $('#contenedor-adjunto');
        $contenedor.empty().removeClass('p-3 p-1');
        
        // Añadir spinner mientras carga
        $contenedor.html('<div class="spinner-border text-primary" role="status"></div>');
        
        if (tipo.includes('image')) {
            const img = new Image();
            img.onload = function() {
                const esVertical = this.height > this.width;
                $contenedor.html(`
                    <div class="${esVertical ? 'h-100' : 'w-100'} p-2">
                        <img src="${rutaCompleta}" class="img-fluid ${esVertical ? 'h-100' : 'w-100'}" 
                             style="object-fit: contain; ${esVertical ? 'width: auto;' : 'height: auto;'}">
                    </div>
                `);
            };
            img.src = rutaCompleta;
            
        } else if (tipo.includes('pdf')) {
            $contenedor.html(`
                <div class="w-100 h-100">
                    <embed src="${rutaCompleta}#toolbar=1&navpanes=1&scrollbar=1" 
                           type="application/pdf" 
                           class="w-100 h-100">
                </div>
            `);
        } else {
            $contenedor.html(`
                <div class="alert alert-danger m-3">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    Tipo de archivo no soportado para visualización
                </div>
            `);
        }
        
        $('#modalVerAdjunto').modal('show');
    });

    // CAPTURAR EL CLICK EN EL BOTÓN DE BORRAR
    $(document).on('click', '.desacAdjunto', function (event) {
        event.preventDefault();
        let id = $(this).data('id_adjunto'); // Cambiado de data('id') a data('prod_id')
        desacAdjunto(id);
    });
    ////////////////////////////////////
    //   FIN ZONA DELETE ADJUNTO    //
    //////////////////////////////////

    ///////////////////////////////////////
    //   INICIO ZONA ACTIVAR ADJUNTO  //
    /////////////////////////////////////
    function activarAdjunto(id) {
        swal.fire({
            title: 'Activar',
            text: `¿Desea activar el adjunto con ID ${id}?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Si',
            cancelButtonText: 'No',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                $.post("../../controller/adjuntos.php?op=activar", { id_adjunto: id }, function (data) {

                    $table.DataTable().ajax.reload();

                    swal.fire(
                        'Activado',
                        'El adjunto ha sido activado',
                        'success'
                    )
                });
            }
        })
    }


    // CAPTURAR EL CLICK EN EL BOTÓN DE ACTIVAR
    $(document).on('click', '.activarAdjunto', function (event) { // Sin acento
        event.preventDefault();
        let id = $(this).data('id_adjunto');
        activarAdjunto(id);
    });
    ////////////////////////////////////
    //   FIN ZONA ACTIVAR ADJUNTO    //
    //////////////////////////////////
    

    ///////////////////////////////////////
    //      INICIO ZONA NUEVO           //
    //        BOTON DE NUEVO           // 
    /////////////////////////////////////
    // CAPTURAR EL CLICK EN EL BOTÓN DE NUEVO
    $(document).on('click', '#btnnuevo', function (event) {
        event.preventDefault();
        $('#mdltitulo').text('Nuevo registro de adjunto');

        $('#modalAdjuntos').modal('show');

        // Limpiar el formulario
        $("#formAdjunto")[0].reset();

        // RESETEAR ID SEGURO
        $('#formAdjunto').find('input[name="id_adjunto"]').val("");

        // Limpiar las validaciones
        formValidator.clearValidation(); // Llama al método clearValidation

        cargarLlamadasEnSelect('#id_llamada');

        // Mostrar el mantenimiento(modal) con el foco en el primer campo
        $('#modalAdjuntos').on('shown.bs.modal', function () {
            $('#modalAdjuntos .modal-body #id_adjunto').focus();
        });

        //console.log('Modal mostrado');
    });

    // CAPTURAR EL CLICK EN EL BOTÓN DE SALVAR
$(document).on('click', '#btnsalvar', async function (event) {
    event.preventDefault();
    
     // 1. Obtener valores con comprobación de nulidad
     var idAdjunto = $('#formAdjunto').find('input[name="id_adjunto"]').val() || '';
     var idLlamada = $('#formAdjunto').find('select[name="id_llamada"]').val() || '';
     var nombreArchivo = $('#formAdjunto').find('input[name="nombre_archivo"]').val() || '';
     var tipoA = $('#formAdjunto').find('input[name="tipo"]').val() || '';
     var fechaSubida = $('#formAdjunto').find('input[name="fecha_subida"]').val() || '';
     var estadoA = $('#formAdjunto').find('input[name="estado"]').val() || '1'; // Valor por defecto
     
     // 2. Aplicar trim() solo si el valor existe
     idAdjunto = idAdjunto.toString().trim();
     idLlamada = idLlamada.toString().trim();
     nombreArchivo = nombreArchivo.toString().trim();
     tipoA = tipoA.toString().trim();
     fechaSubida = fechaSubida.toString().trim();
     estadoA = estadoA.toString().trim();
    
    // Validar el formulario usando FormValidator
    const isValid = formValidator.validateForm(event);
    
    // Si la validación falla, no enviar el formulario
    if (!isValid) {
        toastr.error(`Por favor, corrija los errores en el formulario.`, 'Error de Validación');
        return;
    }
    
    // Convertir las fechas a formato YYYY-MM-DD
    var fechaSubida_ISO = convertirFechaAFormatoISO(fechaSubida);
    
    // Datos del formulario
    var datosFormulario = {
        id_adjunto: idAdjunto,
        id_llamada: idLlamada,
        nombre_archivo: nombreArchivo,
        tipo: tipoA,
        fecha_subida: fechaSubida_ISO,
        estado: estadoA
    };
    
    // Solo agregar `id_vacacion` si tiene un valor
    if (idAdjunto.trim() != "") {
        datosFormulario.id_adjunto = idAdjunto;
    }
    
    var formData = new FormData();
    
    // Agregar los datos al objeto FormData
    for (var key in datosFormulario) {
        formData.append(key, datosFormulario[key]);
    }
    
    // Mostrar el FormData antes de enviarlo
    console.log('formData:', formData);
    
    for (var pair of formData.entries()) {
        console.log(pair[0] + ": " + pair[1]);
    }
    
    // Enviar los datos mediante AJAX
    $.ajax({
        url: "../../controller/adjuntos.php?op=guardaryeditar",
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        success: function (data) {
            $('#modalAdjuntos').modal('hide');
            $table.DataTable().ajax.reload();
            $("#formAdjunto")[0].reset();
            toastr["success"]("El adjunto ha sido guardado", "Guardado");
        },
        error: function (xhr, status, error) {
            swal.fire(
                'Error',
                'No se pudo guardar el adjunto',
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
    // CAPTURAR EL CLICK EN EL BOTÓN DE EDITAR
    $(document).on('click', '.editarAdjunto', function (event) {
        event.preventDefault();

        // Limpiar las validaciones
        formValidator.clearValidation(); // Llama al método clearValidation

        let id = $(this).data('id_adjunto');
        //        console.log('Antes del click', id);
        $.post("../../controller/adjuntos.php?op=mostrar", { id_adjunto: id }, function (data) {
            //console.log('Datos recibidos del servidor:', data);

            if (data) {
                // Podría ser que los datos estén llegando como una cadena JSON
                // Intentemos parsear si es necesario
                if (typeof data === 'string') {
                    try {
                        data = JSON.parse(data);
                    } catch (e) {
                        console.error('Error al parsear JSON:', e);
                    }
                }

                //console.log('Datos parseados:', data);

                $('#mdltitulo').text('Edición registro adjunto');
                $('#modalAdjuntos').modal('show');

                $('#modalAdjuntos .modal-body #id_adjunto').val(data.id_adjunto);

                // AQUI HAY QUE HACER UN AJAX PARA RECORRER EL SELECT Y PONER EL QUE COINCIDA
                // CON EL ID LLAMADA

                cargarLlamadasEnSelect('#id_llamada', data.id_llamada);

                $('#modalAdjuntos .modal-body #nombre_archivo').val(data.nombre_archivo);
                $('#modalAdjuntos .modal-body #tipo').val(data.tipo);
                $('#modalAdjuntos .modal-body #fecha_subida').val(data.fecha_subida);
                $('#modalAdjuntos .modal-body #estado').val(data.estado);
                
            } else {
                console.error('Error: Datos no encontrados');
            }
        }).fail(function (xhr, status, error) {
            console.error('Error en la solicitud AJAX:', status, error);
            console.error('Respuesta del servidor:', xhr.responseText);
        });
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
            table_e.column(6).search("").draw(); // Cambiar numero por el índice de la columna a filtrar
        } else {
            // Filtrar la columna por el valor seleccionado
            table_e.column(6).search(value).draw(); // Cambia numero por el índice de la columna a filtrar

        }
    });

    $('#filtroTipoArchivo').on('change', function() {
        var value = $(this).val();
        var searchPattern = '';
        
        switch(value) {
            case "1": // PDF
                searchPattern = '^application\\/(pdf|x-pdf)$'; // Versión optimizada del regex
                break;
            case "2": // Imagen
                searchPattern = '^image\\/'; // Captura cualquier tipo de imagen (jpeg, png, etc.)
                break;
            default: // Todos o "Seleccione un Tipo"
                searchPattern = '';
        }
        
        // Aplicar filtro con parámetros: searchTerm, regex, smartSearch, caseInsensitive
        table_e.column(4).search(searchPattern, true, false, true).draw();
    });

    ////////////////////////////////////////////////////////////
    //        FIN ZONA FILTROS RADIOBUTTON CABECERA          //
    //////////////////////////////////////////////////////////
    ////////////////////////////////////////////////
    //        ZONA FILTRO DE LA FECHA            //
    ///////////////////////////////////////////////

    ////////////////////////////////////////////////
    //        FECHA DE INICIO FILTRO           //
    ///////////////////////////////////////////////

    $('#filtroFechaSubida').on('change', function () {
        var value = $(this).val(); // Obtener el valor seleccionado
        console.log(value);
        table_e.column(5).search(value).draw();
    });

    // borrar la fecha
    $('#borrarFechaSubidaFiltro').on('click', function () {
        $('#filtroFechaSubida').val('');
        $('#filtroFechaSubida').trigger('change');
    });

    // cambiar el cursor
    $('#borrarFechaSubidaFiltro').on('mouseenter', function () {
        $(this).css('cursor', 'pointer');
    }).on('mouseleave', function () {
        $(this).css('cursor', 'default');
    });

     ////////////////////////////////////////////////
    //        FECHA DE FIN FILTRO           //
    ///////////////////////////////////////////////

    // borrar la fecha
    $('#borrarFechaSubida').on('click', function () {
        $('#fecha_subida').val('');
        $('#fecha_subida').trigger('change');
    });

    // cambiar el cursor
    $('#borrarFechaSubida').on('mouseenter', function () {
        $(this).css('cursor', 'pointer');
    }).on('mouseleave', function () {
        $(this).css('cursor', 'default');
    });

    ////////////////////////////////////////////////
    //     FIN ZONA FILTRO DE LA FECHA           //
    ///////////////////////////////////////////////

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