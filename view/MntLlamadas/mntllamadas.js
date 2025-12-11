$(document).ready(function () {

    /////////////////////////////////////
    //            TIPS                //
    ///////////////////////////////////
    // Ocultar dinámicamente la columna con índice 2 (tercera columna)
    // ----> $('#miTabla').DataTable().column(2).visible(false);

    // FormValidator --> 96
    // Definicion del datatables --> 204
    // ColumDefs --> 254
    // Zona de definiciones --> 588
    // Las busquedas de los pies --> 1924


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

    $('#filtroFechaHoraPreferida').inputmask('99-99-9999');
    // NO FUNCIONA - Muestra la máscara pero no permite escribir.
    //$('#prod_telefono').inputmask('(+99) 999-999-999');


    // Configura el datepicker en español
    $.datepicker.setDefaults($.datepicker.regional['es']);

    $('#filtroFechaHoraPreferida').datepicker({
        showAnim: "slideDown",
        dateFormat: 'dd-mm-yy',
        showOtherMonths: true,
        selectOtherMonths: true,
        numberOfMonths: 1
    });

    // FILTRO FECHA FIN DECLARACIÓN

    $('#filtroFechaRecepcion').inputmask('99-99-9999');
    // NO FUNCIONA - Muestra la máscara pero no permite escribir.
    //$('#prod_telefono').inputmask('(+99) 999-999-999');


    // Configura el datepicker en español
    $.datepicker.setDefaults($.datepicker.regional['es']);

    $('#filtroFechaRecepcion').datepicker({
        showAnim: "slideDown",
        dateFormat: 'dd-mm-yy',
        showOtherMonths: true,
        selectOtherMonths: true,
        numberOfMonths: 1
    });



    // MODAL FECHA HORA CONTACTO DATETIME DECLARACIÓN

    // Inicializar Flatpickr dentro del modal (equivalente a dropdownParent)
    flatpickr("#fecha_hora_preferida", {
        enableTime: true,
        dateFormat: "d-m-Y H:i", // Formato con hora y minutos
        time_24hr: true,
        locale: flatpickr.l10ns.es, // Asegura que se use el idioma en español
        defaultDate: moment().toDate(), // Valor por defecto (fecha actual)
        positionElement: document.getElementById("fecha_hora_preferida"), // Forzar que se posicione debajo del input
        static: false, // Coloca el calendario en el flujo del DOM en vez de un contenedor separado
        allowInput: true, // Permite que el usuario escriba manualmente
        appendTo: document.body, // Asegura que el calendario no se quede dentro del modal (para evitar problemas de corte)
    });

    // OBSERVACIONES CON LIBRERÍA
    // Inicializar Summernote
    $('#observaciones').summernote({
        height: 200,
        lang: 'es-ES',
        disableDragAndDrop: true,  // Deshabilitar arrastre de imágenes
        toolbar: [
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['font', ['strikethrough', 'superscript', 'subscript']],
            ['fontsize', ['fontsize']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['table', ['table']]
        ]
    });

    var formValidator = new FormValidator('formLlamada', {
        id_metodo: {
            required: true
        },
        nombre_comunicante: {
            required: true
        },
        domicilio_instalacion: {
            required: true
        },
        id_comercial_asignado: {
            required: true
        },
        estado: {
            required: true
        },
        fecha_recepcion: {
            required: true
        },
        email_contacto: {
            required: false,
            pattern: '^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\\.[a-zA-Z]{2,}$'
        },
        telefono_fijo: {
            required: false,
            pattern: '^\\d{9}$'
        },
        telefono_movil: {
            required: false,
            pattern: '^\\d{9}$'
        }

    });

    var formValidator2 = new FormValidator('formAdjuntos', {
        id_llamada_adjunto: {
            required: true
        },
        archivo_adjunto: {
            required: true,
            fileType: ['jpg', 'png', 'pdf'], // Tipos de archivo permitidos
            maxSize: 2048 // Tamaño máximo en KB (2MB)
        },
    });

    /////////////////////////////////////////
    //     FIN FORMATEO DE CAMPOS          //
    ////////////////////////////////////////

    // 1. Al abrir: Cambia a azul claro (bg-info)
    $('#collapseOne').on('show.bs.collapse', function () {
        $('#accordion-toggle')
            .removeClass('bg-primary')  // Quita el azul original
            .addClass('bg-info')       // Añade azul claro
            .css('color', 'white');    // Asegura texto blanco
    });

    // 2. Al cerrar: Restaura el azul original (bg-primary)
    $('#collapseOne').on('hide.bs.collapse', function () {
        $('#accordion-toggle')
            .removeClass('bg-info')    // Quita azul claro
            .addClass('bg-primary')    // Restaura azul original
            .css('color', '#e6f0fa');  // Color texto original
    });

    // 3. Efecto hover (opcional)
    $('#accordion-toggle').hover(
        function () { // Mouse entra
            $(this).css('opacity', '0.9');
        },
        function () { // Mouse sale
            $(this).css('opacity', '1');
        }
    );

    // AJAX QUE SE VA A HACER DEPENDIENDO DE SI SE ES UN COMERCIAL O NO
    // console.log("id comercial:", idComercial);

    const ajaxConfig = idComercial
        ? {
            url: '../../controller/llamadas.php?op=listarPorComercial',
            type: 'GET',
            data: { id_comercial: idComercial },
            dataType: 'json',
            dataSrc: function (json) {
                console.log("JSON recibido (por comercial):", json);
                console.log("entra a listar por comercial");

                return json.data || json;
            }
        }
        : {
            url: '../../controller/llamadas.php?op=listar',
            type: 'GET',
            dataType: 'json',
            dataSrc: function (json) {
                console.log("JSON recibido:", json);
                console.log("entra a listar normal");
                return json.data || json;
            }
        };

    /////////////////////////////////////
    // INICIO DE LA TABLA DE LLAMADAS  //
    //         DATATABLES             //
    ///////////////////////////////////

    var datatable_llamadasConfig = {
        //serverSide: true, // procesamiento del lado del servidor
        autoWidth: false,
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
            { name: 'id_llamada', data: 'id_llamada', visible: false }, // Columna 1: ID_LLAMADA
            { name: 'id_metodo', data: 'id_metodo', className: 'text-center' }, // Columna 2: ID_METODO
            { name: 'nombre_comunicante', data: 'nombre_comunicante', className: 'text-center align-middle' }, // Columna 3: NOMBRE_COMUNICANTE
            { name: 'domicilio_instalacion', data: 'domicilio_instalacion', visible: false }, // Columna 4: DOMICILIO_INSTALACION
            { name: 'telefono_fijo', data: 'telefono_fijo', visible: false }, // Columna 5: TELEFONO FIJO
            { name: 'telefono_movil', data: 'telefono_movil', visible: false }, // Columna 6: TELEFONO MOVIL
            { name: 'email_contacto', data: 'email_contacto', visible: false }, // Columna 7: EMAIL CONTACTO
            { name: 'fecha_hora_preferida', data: 'fecha_hora_preferida', visible: false }, // Columna 8: FECHA HORA PREFERIDA
            { name: 'observaciones', data: 'observaciones', visible: false }, // Columna 9: OBSERVACIONES
            { name: 'id_comercial_asignado', data: 'id_comercial_asignado', className: 'text-center align-middle' }, // Columna 10: ID COMERCIAL ASIGNADO
            { name: 'estado', data: 'estado', className: 'text-center align-middle' }, // Columna 11: ESTADO
            { name: 'fecha_recepcion', data: 'fecha_recepcion', className: 'text-center align-middle' }, // Columna 12: FECHA_RECEPCION
            { name: 'desfase', data: null }, // Columna 13: DESFASE
            { name: 'activo_llamada', data: 'activo_llamada', className: 'text-center align-middle' }, // Columna 14: ACTIVA_LLAMADA
            { name: 'activar', data: null, defaultContent: '' }, // Columna 15: ACTIVAR/DESACTIVAR/
            { name: 'contactos', data: null, defaultContent: '' }, // Columna 15: CONTACTOS/
            { name: 'adjuntar', data: null, className: 'text-center align-middle' }, // Columna 16: ADJUNTAR
            { name: 'editar', data: null, defaultContent: '', className: 'text-center align-middle' },  // Columna 17: EDITAR
            { name: 'archivos_adjuntos', data: 'archivos_adjuntos', className: 'text-center align-middle', visible: false },  // Columna 18: ARCHIVOS ADJUNTOS
            { name: 'imagen_metodo', data: 'imagen_metodo', className: 'text-center align-middle', visible: false },  // Columna 18: IMAGEN MÉTODO

        ], // de las columnas
        columnDefs: [
            // Cuidado que el ordrData puede interferir con el ordenamiento de la tabla    
            {
                targets: "control:name", width: '5%', searchable: false, orderable: false, className: "text-center align-middle"
            },
            // 1 - id_llamada
            { targets: "id_llamada:name", searchable: false, orderable: false },
            // 2 - id_metodo MOSTRAR NOMBRE METODO MAS ADELANTE!!!
            {
                targets: "id_metodo:name",
                width: '8%',
                searchable: true,
                orderable: true,
                className: "text-center",
                render: function (data, type, row) {
                    if (type === "display") {
                        const rutaBase = '../../public/img/metodos/';
                        const rutaImagen = row.imagen_metodo ? rutaBase + row.imagen_metodo : null;

                        // Contenedor principal
                        let html = `<div style="
                            display: flex;
                            justify-content: center;
                            align-items: center;
                            height: 100%;
                            padding: 5px;
                        ">`;

                        if (rutaImagen) {
                            html += `
                            <img src="${rutaImagen}" 
                                 alt="${row.nombre_metodo || 'Ícono método'}"
                                 style="
                                     width: 28px;
                                     height: 28px;
                                     object-fit: contain;
                                     transition: all 0.2s ease;
                                     filter: drop-shadow(0 2px 2px rgba(0,0,0,0.1));
                                 "
                                 onmouseover="this.style.transform='scale(1.2)'; this.style.filter='drop-shadow(0 3px 3px rgba(0,0,0,0.2))'"
                                 onmouseout="this.style.transform='scale(1)'; this.style.filter='drop-shadow(0 2px 2px rgba(0,0,0,0.1))'"
                                 onerror="
                                     this.onerror=null;
                                     this.src='${rutaBase}default-icon.png';
                                     this.style.width='28px';
                                     this.style.height='28px'
                                 "
                                 title="${row.nombre_metodo || 'Método de contacto'}">`;
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
                    if (type === "filter") {
                        return row.nombre_metodo || '';
                    }
                    return data;
                }
            },
            // 3 - nombre_comunicante                       
            { targets: "nombre_comunicante:name", width: '15%', searchable: true, orderable: true },
            // 4 - domicilio_instalacion
            { targets: "domicilio_instalacion", searchable: false, orderable: false, visible: false },
            // 5 - telefono_fijo
            { targets: "telefono_fijo", searchable: false, orderable: false, visible: false },
            // 6 - telefono_movil
            { targets: "telefono_movil", searchable: false, orderable: false, visible: false },
            // 7 - email_contacto
            { targets: "email_contacto", searchable: false, orderable: false, visible: false },
            // 8 - fecha_hora_preferida
            {
                // Columna de Fecha de hora preferida (solo visualización)
                targets: "fecha_hora_preferida",
                searchable: true,
                orderable: true,
                visible: false,
                className: "text-center",
                render: function (data, type, row) {
                    if (type === "display" || type === "filter") {
                        return formatoFechaEuropeo(data); // Muestra "DD-MM-YYYY"
                    }
                    return data; // Ordenamiento/filtro usa "YYYY-MM-DD" (original)
                }
            },
            // 9 - observaciones
            { targets: "observaciones:name", searchable: false, orderable: false, visible: false },
            // 10 - id_comercial_asignado MOSTRAR NOMBRE DE COMERCIAL
            {
                targets: "id_comercial_asignado:name",
                width: '15%',
                searchable: true,
                orderable: true,
                className: "text-center",
                render: function (data, type, row) {
                    if (type === "display" || type === "filter") {
                        return row.nombre_comercial;
                    }
                    return data; // Ordenamiento/filtro usa "YYYY-MM-DD" (original)
                }
            },
            // 11 - estado MOSTRAR DESCRIPCION ESTADO
            {
                targets: "estado:name",
                width: '10%',
                searchable: true,
                orderable: true,
                className: "text-center",
                render: function (data, type, row) {
                    if (type === "display" || type === "filter") {
                        return row.descripcion_estado;
                    }
                    return row.estado; // Ordenamiento/filtro usa "YYYY-MM-DD" (original)
                }
            },
            // 12 - fecha_recepcion
            {
                // Columna de Fecha de Recepción (solo visualización)
                targets: "fecha_recepcion:name",
                width: '10%',
                searchable: true,
                orderable: true,
                className: "text-center",
                render: function (data, type, row) {
                    if (type === "display" || type === "filter") {
                        return formatoFechaEuropeo(data); // Muestra "DD-MM-YYYY"
                    }
                    return data; // Ordenamiento/filtro usa "YYYY-MM-DD" (original)
                }
            },
            // 13 - desfase
            {
                targets: 'desfase:name', width: '5%', orderable: true, className: "text-center",
                render: function (data, type, row) {
                    // Solo modificar la presentación en los modos display y filter
                    if (type === "display" || type === "filter") {

                        // Si existe fecha_primer_contacto la usamos, si no, usamos fecha_recepcion
                        let fechaBase = row.fecha_primer_contacto ? row.fecha_primer_contacto : row.fecha_recepcion;

                        // Calculamos los días de desfase a partir de la fechaBase seleccionada
                        let diasDesfase = calcularDiasDesdeFecha(fechaBase);
                        console.log("dias desfase: ", diasDesfase);

                        // Inicializamos variable para el icono semáforo (color según el desfase)
                        let semaforo = '';

                        // Definimos colores según los días de desfase
                        if (diasDesfase <= 1) {
                            semaforo = '<i class="bi bi-circle-fill tx-24 text-success"></i>'; // Verde para desfase pequeño
                        } else if (diasDesfase >= 2 && diasDesfase < 3) {
                            semaforo = '<i class="bi bi-circle-fill tx-24 text-warning"></i>'; // Amarillo para desfase medio
                        } else {
                            semaforo = '<i class="bi bi-circle-fill tx-24 text-danger"></i>'; // Rojo para desfase grande
                        }

                        // Texto adicional que muestra hace cuánto tiempo fue esa fecha (ejemplo: "hace 3 días")
                        let informacion = `<span class="tx-10 text-dark"> - ${calcularDesdeFecha(fechaBase)} </span>`;

                        // Retornamos el icono y el texto concatenados dentro de un span
                        return `<span>${semaforo}</span>`;
                    }

                    // Para otros tipos de renderizado (ordenación, etc.) devolvemos el dato original sin cambios
                    return data;
                }
            },
            // 14 - ACTIVO_LLAMADA
            {
                targets: 'activo_llamada:name',
                width: '5%',
                orderable: true,
                searchable: true,
                className: "text-center",
                render: function (data, type, row) {
                    if (type === "display") {
                        return data == 1 ?
                            '<i class="bi bi-check-circle text-success fa-2x"></i>' : '<i class="bi bi-x-circle text-danger fa-2x"></i>';
                    }
                    // Para filtrado y ordenamiento, devolver el valor numérico crudo
                    return data;
                }
            },
            // 15 - ACTIVAR/DESACTIVAR
            {
                targets: 'activar:name', width: '5%', searchable: false, orderable: false, class: "text-center",
                render: function (data, type, row) {
                    // El nombre que de la variable que se pasa por data-xxx debe ser el mismo que el nombre de la columna en la base de datos
                    if (row.activo_llamada == 1) {
                        // permito desactivar el estado de la llamada
                        return `<button type="button" class="btn btn-danger btn-sm desacLlamada" data-bs-toggle="tooltip-primary" data-placement="top" title="Desactivar" data-original-title="Tooltip on top" 
                             data-id_llamada="${row.id_llamada}"> 
                             <i class="fa-solid fa-trash"></i>
                             </button>`}
                    else if (row.activo_llamada == 0) {
                        // debo permitir activar de nuevo el estado de la llamada
                        return `<button class="btn btn-success btn-sm activarLlamada" data-bs-toggle="tooltip-primary" data-placement="top" title="Activar" data-original-title="Tooltip on top" 
                             data-id_llamada="${row.id_llamada}"> <!-- Cambiado de data-id a data-prod_id -->
                             <i class="bi bi-hand-thumbs-up-fill"></i>
                            </button>`}
                } // de la function
            },
            // 16 - CONTACTOS
            {
                targets: 'contactos:name',
                width: '5%',
                searchable: false,
                orderable: false,
                className: "text-center",
                render: function (data, type, row) {
                    let btnClass = 'btn-primary'; // Por defecto
                    console.log(row);

                    if (row.estado_es_3) {
                        btnClass = 'btn-success'; // Verde si estado = 3
                    } else if (row.tiene_contactos) {
                        btnClass = 'btn-warning'; // Amarillo si tiene contactos
                    }

                    return `
                        <button class="btn ${btnClass} btn-sm ver-contactos"
                                data-id_llamada="${row.id_llamada}">
                            <i class="fa fa-eye"></i> Contactos
                        </button>
                    `;
                }
            },
            // 17 - ADJUNTAR
            {
                targets: 'adjuntar:name',
                width: '5%',
                searchable: false,
                orderable: false,
                class: "text-center",
                render: function (data, type, row) {
                    // Determina si el botón debe estar deshabilitado
                    const isDisabled = row.activo_llamada === 0 || row.activo_llamada === "0";
                    const disabledAttr = isDisabled ? 'disabled' : '';

                    // Usa directamente el flag tiene_adjuntos para el color
                    const btnClass = row.tiene_adjuntos ? 'btn-warning' : 'btn-primary';

                    // Añade la clase 'btn-disabled' si está deshabilitado para mejorar el estilo visual
                    const disabledClass = isDisabled ? 'btn-disabled' : '';

                    return `<button type="button" class="btn ${btnClass} ${disabledClass} btn-sm nuevoAdjuntar" 
                                    ${disabledAttr}
                                    data-bs-toggle="tooltip-primary" 
                                    data-placement="top" 
                                    title="${isDisabled ? 'Llamada cerrada - No se pueden adjuntar archivos' : 'Adjuntar archivo'}" 
                                    data-id_llamada="${row.id_llamada}"> 
                                <i class="fa-solid fa-paperclip"></i>
                            </button>`;
                }
            },
            // 18 - EDITAR
            {
                targets: 'editar:name',
                width: '5%',
                searchable: false,
                orderable: false,
                class: "text-center",
                render: function (data, type, row) {
                    return `<button type="button" 
                                    class="btn btn-info btn-sm editarLlamada" 
                                    data-toggle="tooltip-primary" 
                                    data-placement="top" 
                                    title="Editar"  
                                    data-id_llamada="${row.id_llamada}"
                                    data-activo_llamada="${row.activo_llamada}">
                                <i class="fa-solid fa-edit"></i>
                            </button>`;
                }
            },
            // 19 - ARCHIVOS_ADJUNTOS
            {
                targets: "archivos_adjuntos:name",
                searchable: false,
                orderable: false,
                visible: false,
                render: function (data, type, row) {
                    return data || 'Sin archivos';
                }
            },
            // 20 - IMAGEN_METODO
            {
                targets: "imagen_metodo:name",
                searchable: false,
                orderable: false,
                visible: false,
                render: function (data, type, row) {
                    return data || 'Sin archivos';
                }
            }
            // De la columna 14
        ], // de la columnDefs
        ajax: ajaxConfig, // del ajax
        order: [[12, 'asc']], // ordenar por la columna 3 - fech_crea 
        rowGroup: {
            dataSrc: function (row) {
                return formatoFechaEuropeoSoloFecha(row.fecha_recepcion);
            },
            startRender: function (rows, group) {
                let $row = $('<tr/>').append('<td colspan="12" class="group-header">' + group + ' / ' + rows.count() + ' llamadas' + '</td>');
                console.log("Fila creada:", $row[0].outerHTML); // Imprime el HTML de la fila);
                return $row;
            } // de la function startRender
        }, // de la rowGroup
        initComplete: function () {
            const api = this.api();
            // APLICAR EL FILTRO PARA QUE NADA MÁS INICIAR LA TABLA, MUESTRE SOLO REGISTROS FUTUROS DE EVENTOS
            aplicarFiltro(api);
        }

    }; // de la variable datatable_companiesConfig
    ////////////////////////////
    // FIN DE LA TABLA DE    //
    ///////////////////////////

    /************************************/
    //     ZONA DE DEFINICIONES        //
    /**********************************/
    // Definición inicial de la tabla de empleados
    var $table = $('#llamadas_data');  /*<--- Es el nombre que le hemos dado a la tabla en HTML */
    var $tableConfig = datatable_llamadasConfig; /*<--- Es el nombre que le hemos dado a la declaración de la definicion de la tabla */
    //var $columSearch = 3; /* <-- Es la columna en la cual al hacer click el valor se colocará en la zona de search y se buscará */
    var $tableBody = $('#llamadas_data tbody'); /*<--- Es el nombre que le hemos dado al cuerpo de la tabla en HTML */
    /* en el tableBody solo cambiar el nombre de la tabla que encontraremos en HTML*/
    var $columnFilterInputs = $('#llamadas_data tfoot input'); /*<--- Es el nombre que le hemos dado a los inputs de los pies de la tabla en HTML */
    /* en el $columnFilterInputs solo cambiar el nombre de la tabla que encontraremos en HTML*/

    //ejemplo -- var table_e = $('#employees-table').DataTable(datatable_employeeConfig);
    var table_e = $table.DataTable($tableConfig);

    /************************************/
    //   FIN ZONA DE DEFINICIONES      //
    /**********************************/

    /***************************************************/
    //   CAMPOS SOLO NUMÉRICOS MODAL LLAMADAS         //
    /**************************************************/

    $('#telefono_fijo').on('keypress', function (event) {
        //        // Obtener el código ASCII de la tecla presionada
        var charCode = (event.which) ? event.which : event.keyCode;
        //        // Permitir solo caracteres numéricos (códigos ASCII 48-57)
        if (charCode > 31 && (charCode < 48 || charCode > 57)) {
            event.preventDefault(); // Impide la entrada de otros caracteres
        }
    });

    $('#telefono_movil').on('keypress', function (event) {
        //        // Obtener el código ASCII de la tecla presionada
        var charCode = (event.which) ? event.which : event.keyCode;
        //        // Permitir solo caracteres numéricos (códigos ASCII 48-57)
        if (charCode > 31 && (charCode < 48 || charCode > 57)) {
            event.preventDefault(); // Impide la entrada de otros caracteres
        }
    });


    /***************************************************/
    //   CAMPOS SOLO NUMÉRICOS PIES FILTROS            //
    /**************************************************/

    $('#fechaRecepcionFiltroPie').on('keypress', function (event) {
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

    $('#fechaRecepcionFiltroPie').on('input', function () {
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

    $(document).on('click', '.ver-contactos', function () {
        const $btn = $(this);
        const id_llamada = $btn.data('id_llamada');

        // Redirigir a la vista de contactos pasando el id_llamada como parámetro
        window.location.href = `../MntContactos/index.php?id_llamada=${id_llamada}`;
    });


    function format(d) {
        console.log(d);

        /////////////////////////////////////////////////////////////////
        // Usamos fecha_primer_contacto si existe, si no, fecha_recepcion
        let fechaBase = d.fecha_primer_contacto ? d.fecha_primer_contacto : d.fecha_recepcion;

        // Calculamos los días de desfase a partir de la fecha base
        let diasDesfa = calcularDiasDesdeFecha(fechaBase);

        let semafo;
        if (diasDesfa <= 1) {
            semafo = '<i class="bi bi-circle-fill tx-24 text-success"></i>'; // Verde
        } else if (diasDesfa >= 2 && diasDesfa < 3) {
            semafo = '<i class="bi bi-circle-fill tx-24 text-warning"></i>'; // Amarillo
        } else {
            semafo = '<i class="bi bi-circle-fill tx-24 text-danger"></i>'; // Rojo
        }

        // Texto con cuánto tiempo hace que ocurrió esa fecha base
        let informacion = `<span class="text-dark">  (${calcularDesdeFecha(fechaBase)}) </span>`;
        let desfa = `${semafo} ${informacion}`;

        const generarAdjuntosHTML = (adjuntos) => {
            // Manejo cuando no hay archivos
            if (!adjuntos || adjuntos === 'Sin archivos' || adjuntos.trim() === '') {
                return `
            <div class="text-center py-3 bg-light rounded">
                <i class="fas fa-times-circle text-muted fa-2x mb-2"></i>
                <p class="text-muted mb-0">No existen archivos adjuntos</p>
            </div>`;
            }

            const archivos = adjuntos.split(',');
            return archivos.map(archivo => {
                const extension = archivo.trim().split('.').pop().toLowerCase();
                const rutaBase = '../../public/documentos/adjuntos/';
                const nombreArchivo = archivo.trim();

                // Contenedor común para todos los archivos
                return `
            <div class="d-inline-block me-3 mb-3 text-center" style="width: 160px;">
                <div class="border rounded p-2 bg-white h-100 d-flex flex-column">
                    ${extension === 'pdf' ? `
                    <a href="${rutaBase}${nombreArchivo}" target="_blank" class="d-block mb-1 py-2 flex-grow-1 d-flex align-items-center justify-content-center">
                        <div>
                            <i class="fas fa-file-pdf fa-3x text-danger"></i>
                            <div class="small text-muted mt-1">PDF</div>
                        </div>
                    </a>
                    ` : `
                    <a href="${rutaBase}${nombreArchivo}" target="_blank" class="d-block mb-1 flex-grow-1">
                        <img src="${rutaBase}${nombreArchivo}" 
                             alt="Adjunto" 
                             class="img-fluid rounded" 
                             style="max-height: 100px; object-fit: contain;">
                    </a>
                    `}
                    <div class="small text-truncate mt-auto pt-1">${nombreArchivo}</div>
                </div>
            </div>`;
            }).join('');
        };

        return `
        <div class="card border-primary mb-3" style="overflow: visible;">
            <div class="card-header bg-primary text-white">
                <div class="d-flex align-items-center">
                    <i class="bi bi-info-circle fs-3 me-2"></i>
                    <h5 class="card-title mb-0">Detalles de la Recepción</h5>
                </div>
            </div>
            <div class="card-body p-0" style="overflow: visible;">
                <table class="table table-borderless table-striped table-hover mb-0">
                    <tbody>
                        <tr>
                            <th scope="row" class="ps-4 w-25 align-top"><i class="bi bi-house-door-fill me-2"></i>Domicilio</th>
                            <td class="pe-4" style="white-space: pre-wrap; word-wrap: break-word;">${d.domicilio_instalacion || '<span class="text-muted fst-italic">No especificado</span>'}</td>
                        </tr>
                        <tr>
                            <th scope="row" class="ps-4 align-top"><i class="bi bi-phone-fill me-2"></i>Teléfono fijo</th>
                            <td class="pe-4" style="white-space: pre-wrap; word-wrap: break-word;">${d.telefono_fijo || '<span class="text-muted fst-italic">No especificado</span>'}</td>
                        </tr>
                        <tr>
                            <th scope="row" class="ps-4 align-top"><i class="bi bi-phone me-2"></i>Teléfono móvil</th>
                            <td class="pe-4" style="white-space: pre-wrap; word-wrap: break-word;">${d.telefono_movil || '<span class="text-muted fst-italic">No especificado</span>'}</td>
                        </tr>
                        <tr>
                            <th scope="row" class="ps-4 align-top"><i class="bi bi-envelope-fill me-2"></i>Email contacto</th>
                            <td class="pe-4" style="white-space: pre-wrap; word-wrap: break-word;">${d.email_contacto || '<span class="text-muted fst-italic">No especificado</span>'}</td>
                        </tr>
                        <tr>
                            <th scope="row" class="ps-4 align-top"><i class="bi bi-calendar-check-fill me-2"></i>Fecha hora preferida</th>
                            <td class="pe-4" style="white-space: pre-wrap; word-wrap: break-word;">${formatoFechaEuropeo(d.fecha_hora_preferida) || '<span class="text-muted fst-italic">No especificada</span>'}</td>
                        </tr>
                        <tr>
                            <th scope="row" class="ps-4 align-top"><i class="bi bi-clipboard-check-fill me-2"></i>Observaciones</th>
                            <td class="pe-4" style="white-space: pre-wrap; word-wrap: break-word;">${d.observaciones || '<span class="text-muted fst-italic">No hay observaciones</span>'}</td>
                        </tr>
                        <tr>
                            <th scope="row" class="ps-4 align-top"><i class="bi bi-exclamation-triangle-fill me-2"></i>Semáforo</th>
                            <td class="pe-4" style="white-space: pre-wrap; word-wrap: break-word;">${desfa || '<span class="text-muted fst-italic">No hay desfase</span>'}</td>
                        </tr>
                        <tr>
                            <th scope="row" class="ps-4 align-top"><i class="bi bi-paperclip me-2"></i>Archivos adjuntos</th>
                            <td class="pe-4">
                                <div class="mt-2 d-flex flex-wrap">
                                    ${generarAdjuntosHTML(d.archivos_adjuntos)}
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-transparent border-top-0 text-end">
                <small class="text-muted">Actualizado: ${new Date().toLocaleDateString()}</small>
            </div>
        </div>`;
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

    // CADA VEZ QUE CAMBIA EL FILTRO DE FILTRAR POR FECHA (PASADA, FUTURA, ETC) SE LLAMA A APLICAR FILTRO
    $('input[name="filterDates"]').on('change', function () {
        aplicarFiltro(table_e);  // Aplicar filtro cuando se cambia
    });

    function aplicarFiltro(api) {
        var filtro = $('input[name="filterDates"]:checked').val();
        var ahora = new Date(); // Fecha y hora exactas actuales

        // 2. Limpiamos filtros previos 
        $.fn.dataTable.ext.search = [];

        if (filtro !== "all") {
            $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
                // Verificamos que el filtro solo se aplica a tabla de eventos
                if (settings.nTable.id !== 'llamadas_data') {
                    return true; // Ignoramos completamente otras tablas
                }

                var fila = api.row(dataIndex).data();

                // Protección contra datos incompletos
                if (!fila || !fila.fecha_recepcion) return false;

                var fechaRecepcion = new Date(fila.fecha_recepcion);
                var esHoy = fechaRecepcion.toDateString() === ahora.toDateString();

                switch (filtro) {
                    case "past":
                        return fechaRecepcion < ahora;
                    case "current":
                        return esHoy && fechaRecepcion > ahora;
                    case "future":
                        return fechaRecepcion > ahora;
                    default:
                        return true;
                }
            });
        }

        // 3. Se aplica el filtro y se redibuja SOLO la tabla de eventos
        api.draw();

        // Mensaje si no hay resultados
        if (api.rows({ filter: 'applied' }).count() === 0) {
            var mensaje = {
                "past": "No hay llamadas pasadas",
                "current": "No hay llamadas futuras hoy",
                "future": "No hay llamadas futuras",
                "all": "No hay llamadas registradas"
            }[filtro];

            $(api.table().body()).html(
                `<tr><td colspan="${api.columns().count()}" class="text-center">${mensaje}</td></tr>`
            );
        }
    }

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

    // Inicializar Select2 para los métodos
    $('#id_metodo').select2({
        templateResult: formatOptionWithImage,
        templateSelection: formatOptionWithImage,
        width: '100%',
        dropdownParent: $('#modalLlamada') // ¡Esto es clave!
    });

    // Inicialización única (deja solo esto en tu document.ready)
    $(document).ready(function () {
        $('#id_metodo').select2({
            templateResult: formatOptionWithImage,
            templateSelection: formatOptionWithImage,
            width: '100%',
            dropdownParent: $('#modalLlamada')
        });
    });

    // Función modificada para cargar datos
    function cargarMetodosEnSelect(selectId, idMetodoSeleccionado) {
        $.post("../../controller/metodos.php?op=listar", function (data) {
            const select = $(selectId);

            // Limpiar select completamente (incluyendo la primera opción)
            select.empty().append('<option value="">Seleccione un método...</option>');

            try {
                const jsondata = typeof data === 'string' ? JSON.parse(data) : data;

                // Añadir opciones
                $.each(jsondata.data || [], function (index, metodo) {
                    const imgUrl = metodo.imagen_metodo
                        ? '../../public/img/metodos/' + metodo.imagen_metodo
                        : '../../public/img/default-method.png';

                    const option = new Option(metodo.nombre, metodo.id_metodo);
                    $(option).data('img', imgUrl);
                    select.append(option);
                });

                // Establecer selección y actualizar
                if (idMetodoSeleccionado) {
                    select.val(idMetodoSeleccionado).trigger('change');
                } else {
                    select.val('').trigger('change'); // Resetear si no hay selección
                }

            } catch (e) {
                console.error('Error:', e);
                toastr.error('Error al cargar métodos');
            }
        }, "json");
    }

    // Función para mostrar imágenes (debe estar en el ámbito global)
    function formatOptionWithImage(option) {
        if (!option.id) return option.text;
        const imgUrl = $(option.element).data('img');
        return $(`<span><img src="${imgUrl}" style="height:20px; margin-right:10px;">${option.text}</span>`);
    }

    // Función para mostrar imágenes (debe estar en el ámbito global)
    function formatOptionWithImage(option) {
        if (!option.id) return option.text;

        const imgUrl = $(option.element).data('img') || '../../public/img/default-method.png';
        return $(`
            <span style="display: flex; align-items: center;">
                <img src="${imgUrl}" style="width:20px; height:20px; margin-right:8px; object-fit:contain;">
                ${option.text}
            </span>
        `);
    }

    // Función para mostrar imágenes
    function formatOptionWithImage(option) {
        if (!option.id) return option.text;

        const imgSrc = $(option.element).data('img') || '../../public/img/default-method.png';
        return $(`
        <span>
            <img src="${imgSrc}" style="height:20px; width:20px; object-fit:contain; margin-right:10px;">
            ${option.text}
        </span>
    `);
    }

    function configurarSelect2Comerciales(selector = '#id_comercial_asignado') {
        $(selector).select2({
            width: '100%',
            dropdownParent: $('#modalLlamada .modal-content'),
            dropdownPosition: 'below',
            dropdownAutoWidth: true,
            placeholder: 'Seleccione un comercial',
            allowClear: true,
            language: {
                noResults: function () {
                    return "No hay comerciales disponibles";
                }
            }
        });
    }

    function cargarComercialesEnSelect(selectId, idComercialSeleccionado) {
        $.post("../../controller/comerciales.php?op=listar", function (data) {
            const jsondata = data;
            var select = $(selectId);
            // Limpiar las opciones existentes
            select.empty();
            // Agregar la opción por defecto
            select.append($('<option>', { value: '', text: 'Seleccione un comercial...' }));

            if (data) {
                if (typeof data === 'string') {
                    try {
                        data = JSON.parse(data);
                    } catch (e) {
                        console.error('Error al parsear JSON:', e);
                    }
                }
            }

            $.each(jsondata.data, function (index, comercial) {
                let selected = (idComercialSeleccionado !== undefined && idComercialSeleccionado !== null && idComercialSeleccionado !== '' && comercial.id_comercial == idComercialSeleccionado) ? 'selected' : '';
                var optionHtml = '<option value="' + comercial.id_comercial + '" ' + selected + '>' + comercial.nombre + " " + comercial.apellidos + '</option>';

                select.append(optionHtml);
            });
        }, "json").fail(function (xhr, status, error) {
            console.error("Error al cargar los comerciales:", error);
        });
    }

    function cargarEstadosEnSelect(selectId, idEstadoSeleccionado) {
        $.post("../../controller/estados.php?op=listar", function (data) {
            const jsondata = data;
            var select = $(selectId);
            // Limpiar las opciones existentes
            select.empty();
            // Agregar la opción por defecto
            select.append($('<option>', { value: '', text: 'Seleccione un estado...' }));

            if (data) {
                if (typeof data === 'string') {
                    try {
                        data = JSON.parse(data);
                    } catch (e) {
                        console.error('Error al parsear JSON:', e);
                    }
                }
            }

            $.each(jsondata.data, function (index, estado) {
                let selected = (idEstadoSeleccionado !== undefined && idEstadoSeleccionado !== null && idEstadoSeleccionado !== '' && estado.id_estado == idEstadoSeleccionado) ? 'selected' : '';
                var optionHtml = '<option value="' + estado.id_estado + '" ' + selected + '>' + estado.desc_estado + '</option>';

                select.append(optionHtml);
            });
        }, "json").fail(function (xhr, status, error) {
            console.error("Error al cargar los estados:", error);
        });
    }

    /////////////////////////////////////
    //   INICIO ZONA DELETE LLAMADAS  //
    ///////////////////////////////////
    function desactivarLlamada(id) {
        swal.fire({
            title: 'Desactivar',
            text: `¿Desea desactivar la llamada con ID ${id}?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Si',
            cancelButtonText: 'No',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                $.post("../../controller/llamadas.php?op=eliminar", { id_llamada: id }, function (data) { // Cambiado a prod_id

                    $table.DataTable().ajax.reload();

                    swal.fire(
                        'Desactivado',
                        'La llamada ha sido desactivada',
                        'success'
                    )
                });
            }
        })
    }


    // CAPTURAR EL CLICK EN EL BOTÓN DE BORRAR
    $(document).on('click', '.desacLlamada', function (event) {
        event.preventDefault();
        let id = $(this).data('id_llamada'); // Cambiado de data('id') a data('prod_id')
        desactivarLlamada(id);
    });
    ////////////////////////////////////
    //   FIN ZONA DELETE LLAMADA    //
    //////////////////////////////////

    ///////////////////////////////////////
    //   INICIO ZONA ACTIVAR LLAMADA  //
    /////////////////////////////////////
    function activarLlamada(id) {
        swal.fire({
            title: 'Activar',
            text: `¿Desea activar la llamada con ID ${id}?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Si',
            cancelButtonText: 'No',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                $.post("../../controller/llamadas.php?op=activar", { id_llamada: id }, function (data) {

                    $table.DataTable().ajax.reload();

                    swal.fire(
                        'Activado',
                        'La llamada ha sido activada',
                        'success'
                    )
                });
            }
        })
    }


    // CAPTURAR EL CLICK EN EL BOTÓN DE ACTIVAR
    $(document).on('click', '.activarLlamada', function (event) { // Sin acento
        event.preventDefault();
        let id = $(this).data('id_llamada');
        activarLlamada(id);
    });
    ////////////////////////////////////
    //   FIN ZONA ACTIVAR VACACIÓN    //
    //////////////////////////////////
    /*
    // Función para limpiar el adjunto
    $("#btnLimpiarAdjunto").click(function() {
        $("#archivo_adjunto").val('');
        $("#previewAdjuntos").empty();
    });
    */

    // ============ FUNCIÓN PARA MOSTRAR ADJUNTOS EXISTENTES ============
    function mostrarAdjuntosExistentes(adjuntos, nuevosArchivos = []) {
        // Limpiar el contenedor de vista previa y aplicar clases de estilo
        $("#previewAdjuntos").empty().addClass('d-flex flex-wrap gap-2');

        // Mostrar adjuntos existentes (que ya tienen un ID, es decir, que ya están almacenados)
        if (adjuntos && adjuntos.length > 0) {
            adjuntos.forEach(adjunto => {
                // Construye la ruta del archivo existente
                const rutaCompleta = `../../public/documentos/adjuntos/${adjunto.nombre_archivo}`;
                // Crea el elemento visual (preview) del archivo
                const previewItem = crearPreviewItem(adjunto, rutaCompleta, true);
                // Lo agrega al contenedor de vista previa
                $("#previewAdjuntos").append(previewItem);
            });
        }

        // Mostrar archivos nuevos que el usuario acaba de seleccionar (no tienen ID aún)
        if (nuevosArchivos && nuevosArchivos.length > 0) {
            nuevosArchivos.forEach((file, index) => {
                // Crear un ID temporal/falso para el archivo nuevo
                const fakeId = 'new-' + index;
                // Crear un objeto con la estructura similar al de los adjuntos existentes
                const adjuntoFake = {
                    id_adjunto: fakeId,
                    nombre_archivo: file.name,
                    tipo: file.type,
                    fecha_subida: new Date().toISOString()
                };

                // Usar FileReader para leer el archivo y mostrarlo como preview
                const reader = new FileReader();
                reader.onload = function (e) {
                    // Crear el preview y agregarlo al contenedor
                    const previewItem = crearPreviewItem(adjuntoFake, e.target.result, false);
                    $("#previewAdjuntos").append(previewItem);
                };
                // Leer el archivo como una URL de datos (base64)
                reader.readAsDataURL(file);
            });
        }

        // Si no hay ni archivos existentes ni nuevos, mostrar un mensaje de vacío
        if ((!adjuntos || adjuntos.length === 0) && (!nuevosArchivos || nuevosArchivos.length === 0)) {
            $("#previewAdjuntos").html('<div class="text-center text-muted w-100 py-3">Esta llamada no tiene archivos adjuntos actualmente</div>');
        }
    }

    function crearPreviewItem(adjunto, ruta, esExistente) {
        // Crear el contenedor principal del preview con estilos CSS aplicados
        const previewItem = $('<div>').addClass('preview-item position-relative')
            .css({
                'width': '120px',
                'height': '120px',
                'border': '1px solid #ddd',
                'border-radius': '4px',
                'overflow': 'hidden',
                'cursor': 'pointer'
            });

        // Verificar si el archivo es una imagen
        if (adjunto.tipo.startsWith('image/')) {
            // Si es imagen, insertar una etiqueta <img> con la ruta correspondiente y estilos
            previewItem.append(
                $('<img>').attr({
                    'src': ruta,
                    'alt': adjunto.nombre_archivo,
                    'title': 'Click para ver'
                }).css({
                    'width': '100%',
                    'height': '100%',
                    'object-fit': 'cover'
                })
            );
        } else {
            // Si no es imagen, usar íconos según el tipo de archivo
            const icono = adjunto.tipo === 'application/pdf' ?
                'fa-file-pdf text-danger' : 'fa-file text-primary';

            // Insertar un contenedor con el ícono y el nombre del archivo
            previewItem.append(
                $('<div>').addClass('h-100 d-flex flex-column align-items-center justify-content-center bg-light p-2')
                    .html(`
                    <i class="fas ${icono} mb-1" style="font-size: 1.5rem;"></i>
                    <small class="text-center text-truncate w-100" title="${adjunto.nombre_archivo}">
                        ${adjunto.nombre_archivo}
                    </small>
                `)
            );
        }

        // Crear el botón de eliminar en la esquina superior derecha del preview
        const deleteBtn = $(` 
        <button type="button" 
                class="btn btn-danger btn-xs remove-adjunto-btn" 
                data-id="${adjunto.id_adjunto}"
                data-nombre="${adjunto.nombre_archivo}"
                data-existente="${esExistente}"
                style="position: absolute; top: 2px; right: 2px; padding: 0.15rem 0.3rem;">
            <i class="fas fa-times" style="font-size: 0.7rem;"></i>
        </button>
    `);

        // Asociar evento al botón para ejecutar la función eliminarAdjunto cuando se hace clic
        deleteBtn.on('click', function (e) {
            e.stopPropagation(); // Evita que el clic también dispare el evento del contenedor
            eliminarAdjunto($(this).data()); // Pasa los datos del botón a la función
        });

        // Agregar el botón de eliminar al preview
        previewItem.append(deleteBtn);

        // Formatear la fecha de subida del archivo
        const fecha = new Date(adjunto.fecha_subida);
        const fechaFormateada = fecha.toLocaleDateString('es-ES');

        // Agregar pie de información con la fecha y si el archivo es nuevo o existente
        previewItem.append(
            $('<div>').addClass('position-absolute bottom-0 start-0 end-0 bg-dark bg-opacity-75 text-white small px-2 py-1 text-center')
                .html(`
                ${fechaFormateada}<br>
                <small>${esExistente ? 'Guardado' : 'Nuevo'}</small>
            `)
        );

        // Asignar evento de clic al contenedor para abrir el archivo en una nueva pestaña
        previewItem.on('click', function () {
            if (esExistente) {
                // Si ya existe, se abre directamente desde su ruta
                window.open(ruta, '_blank');
            } else {
                // Si es nuevo (aún no subido), se crea una vista previa básica
                const win = window.open('', '_blank');
                if (adjunto.tipo.startsWith('image/')) {
                    win.document.write(`<img src="${ruta}" style="max-width:100%">`);
                } else {
                    win.document.write('<p>Vista previa no disponible para este tipo de archivo</p>');
                }
            }
        });

        // Retornar el elemento de vista previa creado
        return previewItem;
    }

    // ============ FUNCIÓN PARA ELIMINAR ADJUNTO ============
    function eliminarAdjunto({ id, nombre, existente }) {
        // Mostrar un cuadro de confirmación al usuario usando SweetAlert2
        Swal.fire({
            title: 'Eliminar archivo',
            text: `¿Estás seguro de eliminar "${nombre}"?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            reverseButtons: true
        }).then((result) => {
            // Si el usuario confirma la eliminación
            if (result.isConfirmed) {
                if (existente) {
                    // Si el archivo ya está guardado en el servidor

                    // Obtener los archivos nuevos que aún no se han subido
                    const nuevosArchivos = $("#archivo_adjunto")[0].files;

                    // Enviar una petición AJAX para eliminar el adjunto del servidor
                    $.ajax({
                        url: '../../controller/adjuntos.php?op=eliminar_adjunto',
                        method: 'POST',
                        data: { id_adjunto: id },
                        success: function () {
                            // Una vez eliminado, volver a obtener los adjuntos actualizados

                            const idLlamada = $('#id_llamada_adjunto').val(); // Obtener ID de la llamada actual

                            $.ajax({
                                url: '../../controller/adjuntos.php?op=obtener_adjuntos_por_llamada',
                                method: 'POST',
                                data: { id_llamada: idLlamada },
                                dataType: 'json',
                                success: function (adjuntos) {
                                    // Volver a mostrar los adjuntos actualizados + archivos nuevos que aún están cargados
                                    mostrarAdjuntosExistentes(adjuntos || [], Array.from(nuevosArchivos || []));
                                    $table.DataTable().ajax.reload();
                                    // Mostrar mensaje de éxito
                                    Swal.fire('Eliminado', 'El archivo ha sido eliminado', 'success');
                                }
                            });
                        }
                    });
                } else {
                    // Si el archivo no está guardado aún (es nuevo y no ha sido subido)

                    // Obtener el ID de la llamada actual
                    const idLlamada = $('#id_llamada_adjunto').val();

                    // Obtener los adjuntos existentes desde el servidor
                    $.ajax({
                        url: '../../controller/adjuntos.php?op=obtener_adjuntos_por_llamada',
                        method: 'POST',
                        data: { id_llamada: idLlamada },
                        dataType: 'json',
                        success: function (adjuntos) {
                            // Filtrar el archivo que se desea eliminar de la lista de archivos seleccionados
                            const input = document.getElementById('archivo_adjunto');
                            const files = Array.from(input.files); // Convertir FileList a Array
                            const updatedFiles = files.filter(file => file.name !== nombre); // Excluir el que se va a eliminar

                            // Crear un nuevo DataTransfer con los archivos filtrados
                            const dataTransfer = new DataTransfer();
                            updatedFiles.forEach(file => dataTransfer.items.add(file));
                            input.files = dataTransfer.files; // Asignar los nuevos archivos al input file

                            // Mostrar nuevamente la vista de los archivos: adjuntos del servidor + nuevos (actualizados)
                            mostrarAdjuntosExistentes(adjuntos || [], updatedFiles);

                            // Mostrar mensaje de eliminación exitosa
                            Swal.fire('Eliminado', 'El archivo no se subirá', 'success');
                        }
                    });
                }
            }
        });
    }

    // ============ BOTÓN NUEVO ADJUNTO ============
    $(document).on('click', '.nuevoAdjuntar', function (event) {
        event.preventDefault(); // Evita el comportamiento por defecto del enlace o botón

        // Limpieza inicial del formulario de adjuntos
        //$("#btnLimpiarAdjunto").trigger("click"); // Simula clic en botón para limpiar adjuntos previos
        $("#formAdjuntos")[0].reset();            // Reinicia todos los campos del formulario
        formValidator2.clearValidation();         // Limpia mensajes de validación del formulario

        // Capturar el ID de la llamada desde el botón clicado
        const idLlamada = $(this).data('id_llamada');
        $('#id_llamada_adjunto').val(idLlamada); // Asigna ese ID al campo oculto del formulario

        // Hacer una solicitud AJAX para obtener los adjuntos existentes de la llamada
        $.ajax({
            url: '../../controller/adjuntos.php?op=obtener_adjuntos_por_llamada', // Ruta al backend
            method: 'POST',
            data: { id_llamada: idLlamada }, // Enviar el ID de la llamada como parámetro
            dataType: 'json',
            success: function (adjuntos) {
                // Obtener los archivos nuevos seleccionados (si los hay)
                const nuevosArchivos = $("#archivo_adjunto")[0].files;

                // Mostrar en la interfaz los adjuntos ya existentes + nuevos (si hay)
                mostrarAdjuntosExistentes(adjuntos || [], Array.from(nuevosArchivos || []));
            },
            error: function () {
                // Si falla la petición, simplemente no se muestran adjuntos existentes
                mostrarAdjuntosExistentes([], []);
            }
        });

        // Mostrar el modal que contiene el formulario para adjuntar archivos
        $('#modalAdjuntos').modal('show');
    });

    // ============ ACTUALIZAR PREVIEW AL SELECCIONAR ARCHIVOS ============
    $("#archivo_adjunto").change(function () {
        // Obtener el ID de la llamada desde el input oculto
        const idLlamada = $('#id_llamada_adjunto').val();

        // Obtener los nuevos archivos seleccionados por el usuario
        const nuevosArchivos = this.files;

        // Hacer una petición AJAX para obtener los adjuntos existentes asociados a la llamada
        $.ajax({
            url: '../../controller/adjuntos.php?op=obtener_adjuntos_por_llamada', // URL del backend que retorna los adjuntos
            method: 'POST',
            data: { id_llamada: idLlamada }, // Enviar el ID de la llamada como parámetro
            dataType: 'json',
            success: function (adjuntos) {
                // Si la respuesta es exitosa, mostrar tanto los adjuntos existentes como los nuevos
                mostrarAdjuntosExistentes(adjuntos || [], Array.from(nuevosArchivos || []));
            },
            error: function () {
                // Si hay un error al obtener los adjuntos existentes, mostrar solo los nuevos seleccionados
                mostrarAdjuntosExistentes([], Array.from(nuevosArchivos || []));
            }
        });
    });


    $(document).on('click', '#btnGuardarAdjunto', async function (event) {
        event.preventDefault();
        event.stopImmediatePropagation();

        console.log('Botón clickeado'); // Para depuración

        // 1. Validar el formulario usando FormValidator
        const isValid = formValidator2.validateForm(event);
        if (!isValid) {
            toastr.error('Por favor, corrija los errores en el formulario', 'Error de Validación');
            return;
        }

        // 2. Obtener datos del formulario
        const idLlamada = $('#formAdjuntos').find('input[name="id_llamada_adjunto"]').val().trim();
        const archivos = $('#archivo_adjunto')[0].files;

        console.log('Datos obtenidos:', { idLlamada, archivos }); // Para depuración

        // 3. Preparar FormData
        const formData = new FormData();
        formData.append('id_llamada', idLlamada);

        // Agregar cada archivo al FormData
        for (let i = 0; i < archivos.length; i++) {
            formData.append('archivo_adjunto[]', archivos[i]);
        }

        console.log(Array.from(formData.entries())); // Mostrar contenido del FormData

        // 4. Configurar AJAX
        const $btn = $(this);
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Guardando...');

        try {
            const response = await $.ajax({
                url: '../../controller/llamadas.php?op=guardar_adjunto',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json'
            });

            console.log('Respuesta recibida:', response);

            if (response.success) {
                $('#modalAdjuntos').modal('hide');
                $table.DataTable().ajax.reload();
                toastr.success(response.mensaje || 'Archivos adjuntados correctamente');
                $('#formAdjuntos')[0].reset();
            } else {
                toastr.error(response.error || 'Error al guardar los adjuntos');
            }
        } catch (error) {
            console.error('Error completo:', error);
            const errorMsg = error.responseJSON?.error || error.statusText || 'Error de conexión';
            toastr.error('Error al adjuntar archivos: ' + errorMsg);
        } finally {
            $btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i> Guardar');
        }
    });

    ///////////////////////////////////////
    //      INICIO ZONA NUEVO           //
    //        BOTON DE NUEVO           // 
    /////////////////////////////////////
    // CAPTURAR EL CLICK EN EL BOTÓN DE NUEVO
    $(document).on('click', '#btnnuevo', function (event) {
        event.preventDefault();
        $('#mdltitulo').text('Nuevo registro de llamada');

        // Obtener fecha y hora actual en formato dd-mm-yyyy hh:mm
        const now = new Date();
        const day = String(now.getDate()).padStart(2, '0');
        const month = String(now.getMonth() + 1).padStart(2, '0'); // Los meses van de 0 a 11
        const year = now.getFullYear();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');

        const fechaHoraActual = `${day}-${month}-${year} ${hours}:${minutes}`;

        $('#modalLlamada').modal('show');

        // Limpiar el formulario
        $("#formLlamada")[0].reset();

        // RESETEAR ID SEGURO
        $('#formLlamada').find('input[name="id_llamada"]').val("");

        // Establecer la fecha y hora actual en el campo fecha_recepcion
        $('#fecha_recepcion').val(fechaHoraActual);

        $('#observaciones').summernote('reset'); // Resetea Summernote al crear un nuevo registro

        // Aquí deshabilitamos el campo estado
        $('#formLlamada select[name="estado"]').prop('disabled', true);

        // Limpiar las validaciones
        formValidator.clearValidation(); // Llama al método clearValidation

        cargarComercialesEnSelect('#id_comercial_asignado');
        configurarSelect2Comerciales();
        cargarMetodosEnSelect("#id_metodo", 2);
        cargarEstadosEnSelect("#estado", 1);

        // Mostrar el mantenimiento(modal) con el foco en el primer campo
        $('#modalLlamada').on('shown.bs.modal', function () {
            $('#modalLlamada .modal-body #id_metodo').focus();
        });

        //console.log('Modal mostrado');
    });


    // CAPTURAR EL CLICK EN EL BOTÓN DE SALVAR
    $(document).on('click', '#btnsalvar', async function (event) {
        event.preventDefault();
        var form = $('#formLlamada');

        // 1. Validación del formulario (delegada completamente al formValidator)
        const isValid = formValidator.validateForm(event);
        if (!isValid) {
            toastr.error('Por favor, complete correctamente los campos obligatorios', 'Error de Validación');
            return;
        }

        // 2. Recoger valores del formulario
        var idL = form.find('input[name="id_llamada"]').val().trim();

        // Campos obligatorios
        var idMetodoL = form.find('select[name="id_metodo"]').val();
        var nombreComunicanteL = form.find('input[name="nombre_comunicante"]').val().trim();
        var domicilioInstalacionL = form.find('input[name="domicilio_instalacion"]').val().trim();
        var idComercialAsignadoL = form.find('select[name="id_comercial_asignado"]').val();
        var estadoL = form.find('select[name="estado"]').val();
        var fechaRecepcionL = form.find('input[name="fecha_recepcion"]').val().trim();

        // Campos opcionales (manejo de valores nulos)
        var telefonoFijoL = form.find('input[name="telefono_fijo"]').val().trim() || null;
        var telefonoMovilL = form.find('input[name="telefono_movil"]').val().trim() || null;
        var emailContactoL = form.find('input[name="email_contacto"]').val().trim() || null;
        var fechaHoraPreferidaL = form.find('input[name="fecha_hora_preferida"]').val().trim() || null;
        var observacionesL = $('#formLlamada').find('#observaciones').summernote('code').trim() || null;


        // 3. Preparación de datos con manejo de nulos
        var datosFormulario = {
            id_metodo: idMetodoL,
            nombre_comunicante: nombreComunicanteL,
            domicilio_instalacion: domicilioInstalacionL,
            telefono_fijo: telefonoFijoL,
            telefono_movil: telefonoMovilL,
            email_contacto: emailContactoL,
            fecha_hora_preferida: fechaHoraPreferidaL ? convertirFechaAFormatoISO(fechaHoraPreferidaL) : null,
            observaciones: observacionesL,
            id_comercial_asignado: idComercialAsignadoL,
            estado: estadoL,
            fecha_recepcion: convertirFechaAFormatoISO(fechaRecepcionL),
        };

        // Solo para actualización
        if (idL) datosFormulario.id_llamada = idL;

        // 4. Envío con FormData (manejo óptimo de nulos)
        var formData = new FormData();
        for (var key in datosFormulario) {
            formData.append(key, datosFormulario[key] === null ? '' : datosFormulario[key]);
        }

        console.log(idMetodoL);
        console.log(nombreComunicanteL);
        console.log(domicilioInstalacionL);
        console.log(telefonoFijoL);
        console.log(telefonoMovilL);
        console.log(emailContactoL);
        console.log(fechaHoraPreferidaL);
        console.log(observacionesL);
        console.log(idComercialAsignadoL);
        console.log(estadoL);
        console.log(fechaRecepcionL);


        // 5. Envío AJAX
        try {
            const response = await $.ajax({
                url: "../../controller/llamadas.php?op=guardaryeditar",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json'
            });

            if (response.success) {
                $('#modalLlamada').modal('hide');
                $table.DataTable().ajax.reload();
                form[0].reset();
                toastr.success(response.message || "Registro guardado exitosamente");
            } else {
                throw new Error(response.message || "Error en el servidor");
            }
        } catch (error) {
            console.error("Error:", error);
            toastr.error(error.message || "Error al guardar los datos");
        }
    });

    // Función para convertir la fecha a formato 'yyyy-mm-dd h:m:s'
    function convertirFechaAFormatoISO(fecha) {
        return moment(fecha, "D-M-YYYY H:m").format("YYYY-MM-DD H:m:s");
    }

    ///////////////////////////////////////
    //      FIN ZONA NUEVO           //
    /////////////////////////////////////


    ///////////////////////////////////////
    //      INICIO ZONA EDITAR           //
    //        BOTON DE EDITAR           //
    /////////////////////////////////////
    // CAPTURAR EL CLICK EN EL BOTÓN DE EDITAR
    $(document).on('click', '.editarLlamada', function (event) {
        event.preventDefault();
        formValidator.clearValidation();

        let id = $(this).data('id_llamada');
        let estaActiva = $(this).data('activo_llamada');

        $.ajax({
            url: "../../controller/llamadas.php?op=mostrar",
            type: "POST",
            data: { id_llamada: id },
            dataType: "json",
            success: function (data) {
                try {
                    if (!data || typeof data !== 'object') {
                        throw new Error('Respuesta del servidor no válida');
                    }

                    // Configurar el modal
                    $('#mdltitulo').text('Edición registro llamada');

                    console.log("estado id asignado:", data.estado);

                    // Cargar selects primero
                    cargarComercialesEnSelect('#id_comercial_asignado', data.id_comercial_asignado);
                    configurarSelect2Comerciales();
                    cargarMetodosEnSelect("#id_metodo", data.id_metodo);
                    cargarEstadosEnSelect("#estado", data.estado);

                    // Llenar campos del formulario
                    $('#formLlamada input[name="id_llamada"]').val(data.id_llamada);
                    $('#formLlamada input[name="nombre_comunicante"]').val(data.nombre_comunicante);
                    $('#formLlamada input[name="domicilio_instalacion"]').val(data.domicilio_instalacion);
                    $('#formLlamada input[name="telefono_fijo"]').val(data.telefono_fijo);
                    $('#formLlamada input[name="telefono_movil"]').val(data.telefono_movil);
                    $('#formLlamada input[name="email_contacto"]').val(data.email_contacto);

                    // Manejar fechas con formato correcto
                    $('#formLlamada input[name="fecha_hora_preferida"]').val(
                        data.fecha_hora_preferida ? formatoFechaEuropeo(data.fecha_hora_preferida) : ''
                    );

                    $('#formLlamada input[name="fecha_recepcion"]').val(
                        formatoFechaEuropeo(data.fecha_recepcion)
                    );

                    // Establecer el contenido en Summernote
                    $('#modalLlamada .modal-body #observaciones').summernote('code', data.observaciones);

                    // Deshabilitar el input de estado si activo_llamada es 0
                    if (estaActiva === 0) {
                        $('#formLlamada select[name="estado"]').prop('disabled', true);
                    } else if (estaActiva === 1) {
                        $('#formLlamada select[name="estado"]').prop('disabled', false);
                    }

                    // Mostrar el modal
                    $('#modalLlamada').modal('show');

                } catch (e) {
                    console.error('Error al procesar datos:', e);
                    toastr.error('Error al cargar datos para edición');
                }
            },
            error: function (xhr, status, error) {
                console.error('Error en la solicitud AJAX:', status, error);
                console.error('Respuesta del servidor:', xhr.responseText);
                toastr.error('Error al obtener datos de la llamada');
            }
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
            table_e.column(14).search("").draw(); // Cambiar numero por el índice de la columna a filtrar
        } else {
            // Filtrar la columna por el valor seleccionado
            table_e.column(14).search(value).draw(); // Cambia numero por el índice de la columna a filtrar

        }
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

    $('#filtroFechaHoraPreferida').on('change', function () {
        let value = $(this).val().trim();

        if (!value) {
            table_e.column('fecha_hora_preferida:name').search("").draw();
            return;
        }

        // Convertir DD-MM-YYYY a YYYY-MM-DD
        const [day, month, year] = value.split("-");
        value = `${year}-${month}-${day}`;

        table_e.column('fecha_hora_preferida:name').search(value).draw();
    });


    // borrar la fecha
    $('#borrarFechaHoraPreferidaFiltro').on('click', function () {
        $('#filtroFechaHoraPreferida').val('');
        $('#filtroFechaHoraPreferida').trigger('change');
    });

    // cambiar el cursor
    $('#borrarFechaHoraPreferidaFiltro').on('mouseenter', function () {
        $(this).css('cursor', 'pointer');
    }).on('mouseleave', function () {
        $(this).css('cursor', 'default');
    });

    ////////////////////////////////////////////////
    //        FECHA DE FIN FILTRO           //
    ///////////////////////////////////////////////

    $('#filtroFechaRecepcion').on('change', function () {
        var value = $(this).val(); // Obtener el valor seleccionado
        console.log("Fecha seleccionada (original):", value);
        table_e.column(12).search(value).draw();
    });


    // borrar la fecha
    $('#borrarFechaRecepcionFiltro').on('click', function () {
        $('#filtroFechaRecepcion').val('');
        $('#filtroFechaRecepcion').trigger('change');
    });

    // cambiar el cursor
    $('#borrarFechaRecepcionFiltro').on('mouseenter', function () {
        $(this).css('cursor', 'pointer');
    }).on('mouseleave', function () {
        $(this).css('cursor', 'default');
    });

    // borrar la fecha de inicio del modal
    $('#borrarFechaHoraPreferida').on('click', function () {
        $('#fecha_hora_preferida').val('');
        $('#fecha_hora_preferida').trigger('change');
    });

    // cambiar el cursor
    $('#borrarFechaHoraPreferida').on('mouseenter', function () {
        $(this).css('cursor', 'pointer');
    }).on('mouseleave', function () {
        $(this).css('cursor', 'default');
    });

    ////////////////////////////////////////////////
    //     FIN ZONA FILTRO DE LA FECHA           //
    ///////////////////////////////////////////////


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




    // Filtro select para la primera columna del pie
    $('#filtroMetodoPie').on('change', function () {
        var valor = $(this).val();
        // Cambia el índice '2' por el índice real de la columna 'id_metodo' si es diferente
        table_e.column('id_metodo:name').search(valor).draw();
        updateFilterMessage();
    });



    // Filtro select para la primera columna del pie
    $('#filtroActivoPie').on('change', function () {
        var valor = $(this).val();
        table_e.column('activo_llamada:name').search(valor).draw();
        updateFilterMessage();
    });


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

        // Revisar selects del footer
        if ($('#filtroMetodoPie').val() !== "" || $('#filtroActivoPie').val() !== "") {
            activeFilters = true;
        }


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
    // Botón para limpiar los filtros y ocultar el mensaje 
    // ////////////////////////////////////////////
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

        
        // Limpiar los selects del footer
        $('#filtroMetodoPie').val('');
        $('#filtroActivoPie').val('');

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