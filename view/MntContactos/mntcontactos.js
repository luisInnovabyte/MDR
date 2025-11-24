
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

    $('#filtroFechaHoraContacto').inputmask('99-99-9999');
    // NO FUNCIONA - Muestra la máscara pero no permite escribir.
    //$('#prod_telefono').inputmask('(+99) 999-999-999');


    // Configura el datepicker en español
    $.datepicker.setDefaults($.datepicker.regional['es']);

    $('#filtroFechaHoraContacto').datepicker({
        showAnim: "slideDown",
        dateFormat: 'dd-mm-yy',
        showOtherMonths: true,
        selectOtherMonths: true,
        numberOfMonths: 1
    });

    // FILTRO FECHA FIN DECLARACIÓN

    $('#filtroFechaVisitaCerrada').inputmask('99-99-9999');
    // NO FUNCIONA - Muestra la máscara pero no permite escribir.
    //$('#prod_telefono').inputmask('(+99) 999-999-999');


    // Configura el datepicker en español
    $.datepicker.setDefaults($.datepicker.regional['es']);

    $('#filtroFechaVisitaCerrada').datepicker({
        showAnim: "slideDown",
        dateFormat: 'dd-mm-yy',
        showOtherMonths: true,
        selectOtherMonths: true,
        numberOfMonths: 1
    });

    $.datepicker.setDefaults($.datepicker.regional['es']);

    // MODAL FECHA HORA CONTACTO DATETIME DECLARACIÓN
       
    // Inicializar Flatpickr dentro del modal (equivalente a dropdownParent)
    flatpickr("#fecha_hora_contacto", {
        enableTime: true,
        dateFormat: "d-m-Y H:i", // Formato con hora y minutos
        time_24hr: true,
        locale: flatpickr.l10ns.es, // Asegura que se use el idioma en español
        defaultDate: moment().toDate(), // Valor por defecto (fecha actual)
        positionElement: document.getElementById("fecha_hora_contacto"), // Forzar que se posicione debajo del input
        static: false, // Coloca el calendario en el flujo del DOM en vez de un contenedor separado
        allowInput: true, // Permite que el usuario escriba manualmente
        appendTo: document.body, // Asegura que el calendario no se quede dentro del modal (para evitar problemas de corte)
      });

    // MODAL FECHA VISITA CERRADA DATETIME DECLARACIÓN

      // Inicializar Flatpickr dentro del modal (equivalente a dropdownParent)
    flatpickr("#fecha_visita_cerrada", {
        enableTime: true,
        dateFormat: "d-m-Y H:i", // Formato con hora y minutos
        time_24hr: true,
        locale: flatpickr.l10ns.es, // Asegura que se use el idioma en español
        defaultDate: moment().toDate(), // Valor por defecto (fecha actual)
        positionElement: document.getElementById("fecha_visita_cerrada"), // Forzar que se posicione debajo del input
        static: false, // Coloca el calendario en el flujo del DOM en vez de un contenedor separado
        allowInput: true, // Permite que el usuario escriba manualmente
        appendTo: document.body, // Asegura que el calendario no se quede dentro del modal (para evitar problemas de corte)
      });

    var formValidator = new FormValidator('formContacto', {
        id_llamada: {
            required: true
        },
        fecha_hora_contacto: {
            required: true
        },
        id_metodo: {
            required: true
        },
    });

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

        function configurarSelect2Llamada(selector = '#id_llamada') {
            $(selector).select2({
                width: '100%',
                dropdownParent: $('#modalContacto .modal-content'),
                dropdownPosition: 'below',
                dropdownAutoWidth: true,
                placeholder: 'Seleccione una llamada',
                allowClear: true,
                language: {
                    noResults: function () {
                        return "No hay llamadas disponibles";
                    }
                }
            });
        }

           // Inicializar Select2 para los métodos
    $('#id_metodo').select2({
        templateResult: formatOptionWithImage,
        templateSelection: formatOptionWithImage,
        width: '100%',
        dropdownParent: $('#modalContacto') // ¡Esto es clave!
    });

    // Inicialización única (deja solo esto en tu document.ready)
$(document).ready(function() {
    $('#id_metodo').select2({
        templateResult: formatOptionWithImage,
        templateSelection: formatOptionWithImage,
        width: '100%',
        dropdownParent: $('#modalContacto')
    });
});
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

    /////////////////////////////////////////
    //     FIN FORMATEO DE CAMPOS          //
    ////////////////////////////////////////

// PARA VERIFICAR EL ESTADO DE LA LLAMADA EN CASO DE BUSCAR POR ID LLAMADA

// Variables inyectadas desde PHP en el <head>:
//   const idLlamada = ...;
//   const idComercial = ...;

const ajaxConfig = idLlamada
  ? {
      // Si hay idLlamada, listamos los contactos filtrados por llamada
      url: '../../controller/contactos.php?op=listarPorLlamada',
      type: 'POST',
      data: { id_llamada: idLlamada },
      dataType: 'json',
      dataSrc: function (json) {
        console.log("JSON recibido (por llamada específica):", json);
        const data = json.data || json;

        // Verificar si la llamada está cerrada para activar/desactivar botón
        $.ajax({
          url: '../../controller/contactos.php?op=verificarLlamadaCerrada',
          type: 'POST',
          data: { id_llamada: idLlamada },
          dataType: 'json'
        }).done(function(estadoLlamada) {
          console.log("Estado de la llamada:", estadoLlamada);

          if (estadoLlamada.activo_llamada === 0 || estadoLlamada.activo_llamada === "0") {
            $('#btnnuevo').prop('disabled', true);
          } else {
            const tieneContactoInactivo = data.some(contacto =>
              contacto.activo_llamada === 0 || contacto.activo_llamada === "0"
            );
            $('#btnnuevo').prop('disabled', tieneContactoInactivo);
          }
        }).fail(function(error) {
          console.error("Error al verificar estado de llamada:", error);
          $('#btnnuevo').prop('disabled', false);
        });

        return data;
      }
    }
  : (
    idComercial
      ? {
          // Si NO hay idLlamada PERO sí idComercial, listamos contactos filtrados por comercial
          url: '../../controller/contactos.php?op=listarPorComercial',
          type: 'POST',
          data: { id_comercial: idComercial },
          dataType: 'json',
          dataSrc: function(json) {
            console.log("JSON recibido (por comercial):", json);
            $('#btnnuevo').prop('disabled', false);
            return json.data || json;
          }
        }
      : {
          // Si NO hay ni idLlamada ni idComercial, listamos todos los contactos (listar normal)
          url: '../../controller/contactos.php?op=listar',
          type: 'GET',
          dataType: 'json',
          dataSrc: function(json) {
            console.log("JSON recibido (listado completo):", json);
            $('#btnnuevo').prop('disabled', false);
            return json.data || json;
          }
        }
  );


    /////////////////////////////////////
    // INICIO DE LA TABLA DE VACACIONES //
    //         DATATABLES             //
    ///////////////////////////////////
    var datatable_contactosConfig = {
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
            { name: 'id_contacto', data: 'id_contacto', visible: false, className: "text-center"  }, // Columna 0: ID_CONTACTO
            { name: 'id_llamada', data: 'id_llamada', className: "text-center"  }, // Columna 1: ID_LLAMADA
            { name: 'fecha_hora_contacto', data: 'fecha_hora_contacto', className: "text-center"  }, // Columna 2: FECHA_HORA_CONTACTO
            { name: 'id_metodo', data: 'id_metodo', className: "text-center"  }, // Columna 3: ID_METODO
            { name: 'observaciones', data: 'observaciones', visible: false, className: "text-center"  }, // Columna 4: OBSERVACIONES
            { name: 'fecha_visita_cerrada', data: 'fecha_visita_cerrada', visible: false}, // Columna 5: FECHA_VISITA_CERRADA
            { name: 'estado', data: 'estado', className: "text-center" }, // Columna 6: ESTADO
            { name: 'activar', data: null, defaultContent: '', className: "text-center"  },  // Columna //  7: ACT/DES ESTADO
            { name: 'editar', data: null, defaultContent: '', className: "text-center"  },  // Columna //  8: EDITAR
            { name: 'nombre_metodo', data: 'nombre_metodo', className: "text-center", visible: false}, 
            { name: 'imagen_metodo', data: 'imagen_metodo', className: "text-center",  visible: false}, 
        ], // de las columnas
        columnDefs: [
            // Columna 0: BOTÓN MÁS 
            { 
              targets: 'control:name', width: '5%', searchable: false, orderable: false, className: "text-center" 
            },    
            // 1 - ID_CONTACTO
            { targets: 'id_contacto:name', width: '5%', searchable: false, orderable: false },
            // 2 - ID_LLAMADA MOSTRAR NOMBRE COMUNICANTE
            {
                targets: 'id_llamada:name',
                width: '20%',
                searchable: true,
                orderable: true,
                className: "text-center",
                render: function(data, type, row) {
                    if (type === "display" || type === "filter") {
                        return row.nombre_comunicante;
                    }
                    return row.nombre_comunicante; 
                }
            },
            // 3 - FECHA_HORA_CONTACTO
            {
                // Columna de Fecha de inicio (solo visualización)
                targets: 'fecha_hora_contacto:name',
                width: '15%',
                searchable: true,
                orderable: true,
                className: "text-center",
                render: function(data, type, row) {
                    if (type === "display" || type === "filter") {
                        return formatoFechaEuropeo(data); // Muestra "DD-MM-YYYY"
                    }
                    return data; // Ordenamiento/filtro usa "YYYY-MM-DD" (original)
                }
            },
            // 4 - ID_METODO
            {
                targets: "id_metodo:name",
                width: '15%',
                searchable: true,
                orderable: true,
                className: "text-center",
                render: function(data, type, row) {
                    if (type === "display") {
                        const rutaBase = '../../public/img/metodos/';
                        const rutaImagen = row.imagen_metodo ? rutaBase + row.imagen_metodo : null;
                        console.log(row);
                        
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
            // 5 - OBSERVACIONES
            { targets: 'observaciones:name', searchable: true, orderable: false,  className: "text-center" },
            // 6 - FECHA_VISITA_CERRADA
            {
                targets: "fecha_visita_cerrada:name",
                width: '10%',
                searchable: true,
                orderable: true,
                className: "text-center",
                render: function(data, type, row) {
                    if (type === "display" || type === "filter") {
                        if (!data) {
                            return '<span class="text-muted fst-italic">Sin visita</span>';
                        }
                        return formatoFechaEuropeo(data);
                    }
                    return data;
                }
            },
             // 7 - ESTADO
             {
                targets: "estado:name", width: '15%', orderable: true, searchable: true, className: "text-center",
                render: function (data, type, row) {
                    if (type === "display") {
                        return data == 1 ? '<i class="bi bi-check-circle text-success fa-2x"></i>' : '<i class="bi bi-x-circle text-danger fa-2x"></i>';
                    }
                    return data;
                }
            },
            {// 8 - BOTON PARA ACTIVAR/DESACTIVAR ESTADO
                   
                targets: "activar:name", width: '10%', searchable: false, orderable: false, class: "text-center",
                render: function (data, type, row) {
                    if (row.estado == 1) {
                        return `<button type="button" class="btn btn-danger btn-sm desacEstado" data-bs-toggle="tooltip-primary" data-placement="top" title="Desactivar" data-original-title="Tooltip on top" 
                             data-id_contacto="${row.id_contacto}"> 
                             <i class="fa-solid fa-trash"></i>
                             </button>`} 
                    else if (row.estado == 0){
                        // debo permitir activar de nuevo el estado
                        return `<button class="btn btn-success btn-sm activarEstado" data-bs-toggle="tooltip-primary" data-placement="top" title="Activar" data-original-title="Tooltip on top" 
                             data-id_contacto="${row.id_contacto}"> <!-- Cambiado de data-id a data-prod_id -->
                             <i class="bi bi-hand-thumbs-up-fill"></i>
                            </button>`}
                } // de la function
            },//
            {   // 9 BOTON PARA EDITAR CONTACTOS
                targets: "editar:name", width: '5%', searchable: false, orderable: false, class: "text-center",
                render: function (data, type, row) {
                    // El nombre que de la variable que se pasa por data-xxx debe ser el mismo que el nombre de la columna en la base de datos
                    // botón editar el producto
                    return `<button type="button" class="btn btn-info btn-sm editarContacto" data-toggle="tooltip-primary" data-placement="top" title="Editar"  
                             data-id_contacto="${row.id_contacto}"> 
                             <i class="fa-solid fa-edit"></i>
                             </button>`
                } // de la function
            },
               // 10 - ARCHIVOS_ADJUNTOS
               {
                targets: "nombre_metodo:name",
                searchable: false,
                orderable: false,
                visible: false,
                render: function(data, type, row) {
                    return data || 'Sin archivos';
                }
            },
              // 11 - IMAGEN_METODO
            {
                targets: "imagen_metodo:name",
                searchable: false,
                orderable: false,
                visible: false,
                render: function(data, type, row) {
                    return data || 'Sin archivos';
                }
            }
             // De la columna 9
        ], // de la columnDefs
        ajax: ajaxConfig
    }; // de la variable datatable_companiesConfig
    ////////////////////////////
    // FIN DE LA TABLA DE    //
    ///////////////////////////




    /************************************/
    //     ZONA DE DEFINICIONES        //
    /**********************************/
    // Definición inicial de la tabla de empleados
    var $table = $('#contactos_data');  /*<--- Es el nombre que le hemos dado a la tabla en HTML */
    var $tableConfig = datatable_contactosConfig; /*<--- Es el nombre que le hemos dado a la declaración de la definicion de la tabla */
    //var $columSearch = 3; /* <-- Es la columna en la cual al hacer click el valor se colocará en la zona de search y se buscará */
    var $tableBody = $('#contactos_data tbody'); /*<--- Es el nombre que le hemos dado al cuerpo de la tabla en HTML */
    /* en el tableBody solo cambiar el nombre de la tabla que encontraremos en HTML*/
    var $columnFilterInputs = $('#contactos_data tfoot input'); /*<--- Es el nombre que le hemos dado a los inputs de los pies de la tabla en HTML */
    /* en el $columnFilterInputs solo cambiar el nombre de la tabla que encontraremos en HTML*/

    //ejemplo -- var table_e = $('#employees-table').DataTable(datatable_employeeConfig);
    var table_e = $table.DataTable($tableConfig);

    /************************************/
    //   FIN ZONA DE DEFINICIONES      //
    /**********************************/

   $('#fechaHoraContactoFiltroPies').on('keypress', function (event) {
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

    $('#fechaHoraContactoFiltroPies').on('input', function () {
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

        const mostrarFecha = (fecha, textoAlternativo) => 
            fecha ? formatoFechaEuropeo(fecha) : `<span class="text-muted fst-italic">${textoAlternativo}</span>`;

        const mostrarTexto = (texto, fallback) => texto || `<span class="text-muted fst-italic">${fallback}</span>`;

        const mostrarSiNo = (valor) => valor ? 'Sí' : 'No';

        return `
            <div class="card border-primary mb-3" style="overflow: visible;">
                <!-- Detalles del Contacto -->
                <div class="card-header bg-primary text-white">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-person-circle fs-3 me-2"></i>
                        <h5 class="card-title mb-0">Detalles del Contacto</h5>
                    </div>
                </div>

                <div class="card-body p-0" style="overflow: visible;">
                    <table class="table table-borderless table-striped table-hover mb-0" style="width: 100%; min-width: 600px;">
                        <tbody>
                            <tr>
                                <th scope="row" class="ps-4 align-top" style="vertical-align: top; width: 30%;">
                                    <i class="bi bi-person-fill me-2"></i>Id Contacto
                                </th>
                                <td class="pe-4 align-top" style="white-space: normal; word-break: break-word;">
                                    ${mostrarTexto(d.id_contacto, 'No tiene id contacto')}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 align-top" style="vertical-align: top; width: 30%;">
                                    <i class="bi bi-file-earmark-text-fill me-2"></i>Observaciones
                                </th>
                                <td class="pe-4 align-top" style="white-space: normal; word-break: break-word;">
                                    ${mostrarTexto(d.observaciones, 'No tiene observaciones')}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 align-top" style="vertical-align: top; width: 30%;">
                                    <i class="bi bi-calendar-check-fill me-2"></i>Fecha Visita Cerrada
                                </th>
                                <td class="pe-4 align-top" style="white-space: normal; word-break: break-word;">
                                    ${mostrarFecha(d.fecha_visita_cerrada, 'No tiene fecha de visita cerrada')}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Detalles de la Llamada -->
                <div class="card-header bg-primary text-white mt-4">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-telephone-fill fs-3 me-2"></i>
                        <h5 class="card-title mb-0">Detalles de la Llamada</h5>
                    </div>
                </div>

                <div class="card-body p-0" style="overflow: visible;">
                    <table class="table table-borderless table-striped table-hover mb-0" style="width: 100%; min-width: 600px;">
                        <tbody>
                            <tr>
                                <th scope="row" class="ps-4 align-top" style="vertical-align: top; width: 30%;">
                                    <i class="bi bi-house-door-fill me-2"></i>Domicilio Instalación
                                </th>
                                <td class="pe-4 align-top" style="white-space: normal; word-break: break-word;">
                                    ${mostrarTexto(d.domicilio_instalacion, 'No tiene domicilio de instalación')}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 align-top" style="vertical-align: top; width: 30%;">
                                    <i class="bi bi-telephone-fill me-2"></i>Teléfono Fijo
                                </th>
                                <td class="pe-4 align-top" style="white-space: normal; word-break: break-word;">
                                    ${mostrarTexto(d.telefono_fijo, 'No tiene teléfono fijo')}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 align-top" style="vertical-align: top; width: 30%;">
                                    <i class="bi bi-phone-fill me-2"></i>Teléfono Móvil
                                </th>
                                <td class="pe-4 align-top" style="white-space: normal; word-break: break-word;">
                                    ${mostrarTexto(d.telefono_movil, 'No tiene teléfono móvil')}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 align-top" style="vertical-align: top; width: 30%;">
                                    <i class="bi bi-envelope-fill me-2"></i>Email de Contacto
                                </th>
                                <td class="pe-4 align-top" style="white-space: normal; word-break: break-word;">
                                    ${mostrarTexto(d.email_contacto, 'No tiene email de contacto')}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 align-top" style="vertical-align: top; width: 30%;">
                                    <i class="bi bi-clock-fill me-2"></i>Fecha/Hora Preferida
                                </th>
                                <td class="pe-4 align-top" style="white-space: normal; word-break: break-word;">
                                    ${mostrarFecha(d.fecha_hora_preferida, 'No tiene fecha/hora preferida')}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 align-top" style="vertical-align: top; width: 30%;">
                                    <i class="bi bi-calendar-check-fill me-2"></i>Fecha Recepción
                                </th>
                                <td class="pe-4 align-top" style="white-space: normal; word-break: break-word;">
                                    ${mostrarFecha(d.fecha_recepcion, 'No tiene fecha de recepción')}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 align-top" style="vertical-align: top; width: 30%;">
                                    <i class="bi bi-person-circle me-2"></i>Comercial Asignado
                                </th>
                                <td class="pe-4 align-top" style="white-space: normal; word-break: break-word;">
                                    ${mostrarTexto(d.nombre_comercial, 'No tiene comercial asignado')}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 align-top" style="vertical-align: top; width: 30%;">
                                    <i class="bi bi-people-fill me-2"></i>Tiene Contactos
                                </th>
                                <td class="pe-4 align-top" style="white-space: normal; word-break: break-word;">
                                    ${mostrarSiNo(d.tiene_contactos)}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 align-top" style="vertical-align: top; width: 30%;">
                                    <i class="bi bi-check-circle-fill me-2"></i>¿Tiene la cita cerrada?
                                </th>
                                <td class="pe-4 align-top" style="white-space: normal; word-break: break-word;">
                                    ${mostrarSiNo(d.estado_es_3)}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 align-top" style="vertical-align: top; width: 30%;">
                                    <i class="bi bi-paperclip me-2"></i>Tiene Adjuntos
                                </th>
                                <td class="pe-4 align-top" style="white-space: normal; word-break: break-word;">
                                    ${mostrarSiNo(d.tiene_adjuntos)}
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

    function validarRangoFechas(fechaInicio, fechaFin) {
        // Verificar que ambas fechas existen
        if (!fechaInicio || !fechaFin) {
            toastr.error('Debe completar ambas fechas', 'Datos incompletos');
            return false;
        }
        
        // Comparación directa (formato DD-MM-YYYY)
        if (fechaInicio > fechaFin) {
            toastr.error(
                'La fecha de inicio debe ser anterior a la fecha de fin', 
                'Rango inválido'
            );
            return false;
        }
        
        return true;
    }

// Función para convertir la fecha a formato 'yyyy-mm-dd h:m:s'
function convertirFechaAFormatoISO(fecha) {
    return moment(fecha, "D-M-YYYY H:m").format("YYYY-MM-DD H:m:s");
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
                    jsondata = data; // Actualizamos jsondata con el JSON parseado
                    console.log("JSON parseado:", jsondata);
                    } catch (e) {
                        console.error('Error al parsear JSON:', e);
                        select.append($('<option>', { 
                            value: '', 
                            text: 'Error en datos recibidos',
                            disabled: true
                        }));
                        return;
                    }
                }
    
                console.log("data", jsondata);
                console.log("data2", jsondata.data);
    
                // Verificar si hay datos en jsondata.data
                if (!jsondata.data || jsondata.data.length === 0) {
                    select.empty(); // Limpiar el select
                    select.append($('<option>', { 
                        value: '', 
                        text: 'No hay llamadas disponibles',
                        disabled: true,
                        selected: true
                    }));
                    return;
                }
    
                // Si hay datos, procesarlos
                $.each(jsondata.data, function (index, llamada) {
                    let selected = (idLlamadaSeleccionada !== undefined && 
                                  idLlamadaSeleccionada !== null && 
                                  idLlamadaSeleccionada !== '' && 
                                  llamada.id_llamada == idLlamadaSeleccionada) ? 'selected' : '';
                    var optionHtml = '<option value="' + llamada.id_llamada + '" ' + selected + '>' + llamada.nombre_comunicante + '</option>';
                    select.append(optionHtml);
                });
            } else {
                // Si data es null o undefined
                select.empty();
                select.append($('<option>', { 
                    value: '', 
                    text: 'No hay llamadas disponibles',
                    disabled: true,
                    selected: true
                }));
            }
        }, "json").fail(function (xhr, status, error) {
            console.error("Error al cargar las llamadas:", error);
            $(selectId).empty().append($('<option>', { 
                value: '', 
                text: 'Error al cargar llamadas',
                disabled: true
            }));
        });
    }

// Función modificada para cargar datos
function cargarMetodosEnSelect(selectId, idMetodoSeleccionado) {
    $.post("../../controller/metodos.php?op=listar", function(data) {
        const select = $(selectId);
        
        // Limpiar select completamente (incluyendo la primera opción)
        select.empty().append('<option value="">Seleccione un método...</option>');

        try {
            const jsondata = typeof data === 'string' ? JSON.parse(data) : data;
            
            // Añadir opciones
            $.each(jsondata.data || [], function(index, metodo) {
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
 
    /////////////////////////////////////
    //   INICIO ZONA DELETE CONTACTOS  //
    ///////////////////////////////////
    // NO ESTA HECHO PORQUE NO HAY ESTADO DE DESACTIVAR CONTACTOS
    function desacContacto(id) {
        swal.fire({
            title: 'Desactivar',
            text: `¿Desea desactivar el contacto con ID ${id}?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Si',
            cancelButtonText: 'No',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                $.post("../../controller/contactos.php?op=eliminar", { id_contacto: id }, function (data) { // Cambiado a prod_id

                    $table.DataTable().ajax.reload();

                    swal.fire(
                        'Desactivado',
                        'El contacto ha sido desactivado',
                        'success'
                    )
                });
            }
        })
    }


    // NO ESTA HECHO PORQUE NO HAY ESTADO DE DESACTIVAR CONTACTOS
    // CAPTURAR EL CLICK EN EL BOTÓN DE BORRAR
    $(document).on('click', '.desacEstado', function (event) {
        event.preventDefault();
        let id = $(this).data('id_contacto'); // Cambiado de data('id') a data('prod_id')
        desacContacto(id);
    });
    ////////////////////////////////////
    //   FIN ZONA DELETE CONTACTO   //
    //////////////////////////////////

    ///////////////////////////////////////
    //   INICIO ZONA ACTIVAR CONTACTO  //
    /////////////////////////////////////
    // NO ESTA HECHO PORQUE NO HAY ESTADO DE ACTIVAR CONTACTOS
    function activarContacto(id) {
        swal.fire({
            title: 'Activar',
            text: `¿Desea activar el contacto con ID ${id}?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Si',
            cancelButtonText: 'No',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                $.post("../../controller/contactos.php?op=activar", { id_contacto: id }, function (data) {

                    $table.DataTable().ajax.reload();

                    swal.fire(
                        'Activado',
                        'El contacto ha sido activada',
                        'success'
                    )
                });
            }
        })
    }


    // NO ESTA HECHO PORQUE NO HAY ESTADO DE ACTIVAR CONTACTOS
    // CAPTURAR EL CLICK EN EL BOTÓN DE ACTIVAR
    $(document).on('click', '.activarEstado', function (event) { // Sin acento
        event.preventDefault();
        let id = $(this).data('id_contacto');
        activarContacto(id);
    });
    ////////////////////////////////////
    //   FIN ZONA ACTIVAR VACACIÓN    //
    //////////////////////////////////


    /// ME HE QUEDADO POR AQUIIIIIIIIIIIIIIIIIII
    // DLKJFWDOPIGHRGHJFDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDD

    ///////////////////////////////////////
    //      INICIO ZONA NUEVO           //
    //        BOTON DE NUEVO           // 
    /////////////////////////////////////
    // CAPTURAR EL CLICK EN EL BOTÓN DE NUEVO
    $(document).on('click', '#btnnuevo', function (event) {
    event.preventDefault();
    $('#mdltitulo').text('Nuevo registro de contacto');

    const now = new Date();
    const day = String(now.getDate()).padStart(2, '0');
    const month = String(now.getMonth() + 1).padStart(2, '0');
    const year = now.getFullYear();
    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');
    const fechaHoraActual = `${day}-${month}-${year} ${hours}:${minutes}`;

    $('#modalContacto').modal('show');
    $("#formContacto")[0].reset();
    $('#formContacto').find('input[name="id_contacto"]').val("");
    $('#fecha_hora_contacto').val(fechaHoraActual);

    formValidator.clearValidation();
    $('#observaciones').summernote('reset');

    cargarLlamadasEnSelect("#id_llamada", idLlamada);
    configurarSelect2Llamada();
    cargarMetodosEnSelect("#id_metodo");

    // 🔹 Aquí desactivamos el campo si hay id_llamada en la URL
    if (idLlamada) {
        $('#id_llamada').prop('disabled', true);
    } else {
        $('#id_llamada').prop('disabled', false);
    }

    $('#modalContacto').on('shown.bs.modal', function () {
        $('#modalContacto .modal-body #id_llamada').focus();
    });
});

    // CAPTURAR EL CLICK EN EL BOTÓN DE SALVAR
$(document).on('click', '#btnsalvar', async function (event) {
    event.preventDefault();
    
    // Obtener valores
    var fechaHoraContactoC = $('#formContacto').find('input[name="fecha_hora_contacto"]').val().trim();
    var fechaVisitaCerradaC = $('#formContacto').find('input[name="fecha_visita_cerrada"]').val().trim();
    
    // Convertir fechas (manejar caso de campo vacío)
    var fechaHoraContactoC_ISO = fechaHoraContactoC ? convertirFechaAFormatoISO(fechaHoraContactoC) : null;
    var fechaVisitaCerradaC_ISO = fechaVisitaCerradaC ? convertirFechaAFormatoISO(fechaVisitaCerradaC) : null;
    
    // Resto de datos
    var idC = $('#formContacto').find('input[name="id_contacto"]').val();
    var idLlamadaC = $('#formContacto').find('select[name="id_llamada"]').val();
    var idMetodoC = $('#formContacto').find('select[name="id_metodo"]').val().trim(); // Actualización aquí
    var observacionesC = $('#formContacto').find('#observaciones').summernote('code').trim();
    
    // Validación usando el validador personalizado
    const isValid = formValidator.validateForm(event);
    if (!isValid) {
        toastr.error('Por favor, complete correctamente los campos obligatorios', 'Error de Validación');
        return;
    }

    // 🔍 Validar si ya existe una visita cerrada para esta llamada (antes de guardar)
    try {
        const validacion = await $.ajax({
            url: "../../controller/contactos.php?op=verificarFechaVisitaCerradaPorLlamada",
            type: "POST",
            dataType: "json",
            data: {
                id_llamada: idLlamadaC,
                id_contacto: idC || null
            }
        });

        if (validacion.existe && fechaVisitaCerradaC_ISO !== null) {
            toastr.error("Esta llamada ya tiene una fecha de visita cerrada asociada. No se puede registrar otra.");
            return; // detener guardado
        }
    } catch (error) {
        console.error("Error al verificar fecha visita cerrada:", error);
        toastr.error("Ocurrió un error al verificar la fecha de visita cerrada.");
        return;
    }

    // Preparar datos - manejo explícito de null
    var datosFormulario = {
        id_llamada: idLlamadaC,
        fecha_hora_contacto: fechaHoraContactoC_ISO,
        id_metodo: idMetodoC, // Actualización aquí
        observaciones: observacionesC === '' ? null : observacionesC,
        fecha_visita_cerrada: fechaVisitaCerradaC === '' ? null : fechaVisitaCerradaC_ISO
    };
    
    if (idC && idC.trim() !== "") {
        datosFormulario.id_contacto = idC;
    }
    
    // Debug: Ver datos que se enviarán
    console.log("Datos a enviar:", JSON.stringify(datosFormulario, null, 2));
    
    // Enviar datos
    $.ajax({
        url: "../../controller/contactos.php?op=guardaryeditar",
        type: "POST",
        data: datosFormulario,
        dataType: 'json',
        success: function (response) {
            if (response.success) {
                $('#modalContacto').modal('hide');
                $table.DataTable().ajax.reload();
                $("#formContacto")[0].reset();
                toastr.success(response.message || "Contacto guardado correctamente");
            } else {
                toastr.error(response.message || "Error al guardar el contacto");
                console.error("Error del servidor:", response.error);
            }
        },
        error: function (xhr, status, error) {
            console.error("Error en la petición AJAX:", status, error);
            swal.fire('Error', 'No se pudo guardar el contacto: ' + error, 'error');
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
    $(document).on('click', '.editarContacto', function (event) {
    event.preventDefault();

    // Limpiar las validaciones
    formValidator.clearValidation(); // Llama al método clearValidation

    let id = $(this).data('id_contacto');
    console.log('Antes del click', id);
    $.post("../../controller/contactos.php?op=mostrar", { id_contacto: id }, function (data) {
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

            $('#mdltitulo').text('Edición registro contacto');
            $('#modalContacto').modal('show');

            console.log(data.observaciones);

            $('#modalContacto .modal-body #id_contacto').val(data.id_contacto);
            $('#modalContacto .modal-body #observaciones').val(data.observaciones);

            // Cargar opciones y seleccionar el correcto
            cargarLlamadasEnSelect("#id_llamada", data.id_llamada);
            configurarSelect2Llamada();

            // Desactivar #id_llamada si viene id_llamada en la URL
            if (idLlamada) {
                $('#id_llamada').prop('disabled', true);
            } else {
                $('#id_llamada').prop('disabled', false);
            }

            cargarMetodosEnSelect("#id_metodo", data.id_metodo);

            $('#modalContacto .modal-body #fecha_hora_contacto').val(formatoFechaEuropeo(data.fecha_hora_contacto));
            $('#modalContacto .modal-body #tipo_contacto').val(data.tipo_contacto);

            // Establecer el contenido en Summernote
            $('#modalContacto .modal-body #observaciones').summernote('code', data.observaciones);

            $('#modalContacto .modal-body #fecha_visita_cerrada').val(formatoFechaEuropeo(data.fecha_visita_cerrada));

        } else {
            console.error('Error: Datos no encontrados');
        }
    }).fail(function (xhr, status, error) {
        console.error('Error en la solicitud AJAX:', status, error);
        console.error('Respuesta del servidor:', xhr.responseText);
    });
});

// Opcional: habilitar #id_llamada al cerrar modal para evitar que quede deshabilitado en otras aperturas
$('#modalContacto').on('hidden.bs.modal', function () {
    $('#id_llamada').prop('disabled', false);
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
            table_e.column(7).search("").draw(); // Cambiar numero por el índice de la columna a filtrar
        } else {
            // Filtrar la columna por el valor seleccionado
            table_e.column(7).search(value).draw(); // Cambia numero por el índice de la columna a filtrar

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

    $('#filtroFechaHoraContacto').on('change', function () {
        var value = $(this).val(); // Obtener el valor seleccionado
        console.log(value);
        table_e.column(3).search(value).draw();
    });


    // borrar la fecha
    $('#borrarFechaHoraContactoFiltro').on('click', function () {
        $('#filtroFechaHoraContacto').val('');
        $('#filtroFechaHoraContacto').trigger('change');
    });

    // cambiar el cursor
    $('#borrarFechaHoraContactoFiltro').on('mouseenter', function () {
        $(this).css('cursor', 'pointer');
    }).on('mouseleave', function () {
        $(this).css('cursor', 'default');
    });

     ////////////////////////////////////////////////
    //        FECHA DE FIN FILTRO           //
    ///////////////////////////////////////////////

    $('#filtroFechaVisitaCerrada').on('change', function () {
        var value = $(this).val(); // Obtener el valor seleccionado
        console.log(value);
        table_e.column(6).search(value).draw();
    });

    // borrar la fecha
    $('#borrarFechaVisitaCerradaFiltro').on('click', function () {
        $('#filtroFechaVisitaCerrada').val('');
        $('#filtroFechaVisitaCerrada').trigger('change');
    });

    // cambiar el cursor
    $('#borrarFechaVisitaCerradaFiltro').on('mouseenter', function () {
        $(this).css('cursor', 'pointer');
    }).on('mouseleave', function () {
        $(this).css('cursor', 'default');
    });

     // borrar la fecha de hora de contacto del modal
     $('#borrarFechaHoraContacto').on('click', function () {
        $('#fecha_hora_contacto').val('');
        $('#fecha_hora_contacto').trigger('change');
    });

    // cambiar el cursor
    $('#borrarFechaHoraContacto').on('mouseenter', function () {
        $(this).css('cursor', 'pointer');
    }).on('mouseleave', function () {
        $(this).css('cursor', 'default');
    });

    // borrar la fecha de fin del modal
    $('#borrarFechaVisitaCerrada').on('click', function () {
        $('#fecha_visita_cerrada').val('');
        $('#fecha_visita_cerrada').trigger('change');
    });

    // cambiar el cursor
    $('#borrarFechaVisitaCerrada').on('mouseenter', function () {
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