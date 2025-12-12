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

    $('#dateCreateFilter').inputmask('99-99-9999');
    // NO FUNCIONA - Muestra la máscara pero no permite escribir.
    //$('#prod_telefono').inputmask('(+99) 999-999-999');


    // Configura el datepicker en español
    $.datepicker.setDefaults($.datepicker.regional['es']);

    $('#dateCreateFilter').datepicker({
        showAnim: "slideDown",
        dateFormat: 'dd-mm-yy',
        showOtherMonths: true,
        selectOtherMonths: true,
        numberOfMonths: 1
    });

    /////////////////////////////////////////
    //     FIN FORMATEO DE CAMPOS          //
    ////////////////////////////////////////


    /////////////////////////////////////
    // INICIO DE LA TABLA DE PRODUCTOS //
    //         DATATABLES             //
    ///////////////////////////////////
    
    var datatable_logConfig = {
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

            { name: 'usuario', data: 'usuario' },
            { name: 'pantalla', data: 'pantalla' },
            { name: 'actividad', data: 'actividad' },
            { name: 'mensaje', data: 'mensaje' },
            { name: 'tipo', data: 'tipo' }, // para mostrar imagen
            { name: 'fecha_hora', data: 'fecha_hora' },
            { name: 'tipo', data: 'tipo' } // para el filtro
        ], // de las columnas
        columnDefs: [
            // Cuidado que el ordrData puede interferir con el ordenamiento de la tabla    
            // usuario
            { targets: 0, width: '10%', searchable: true, orderable: true, className: "text-center align-middle" },
            //pantalla
            { targets: 1, width: '10%', searchable: true, orderable: true, className: "text-center align-middle" },
            //actividad
            { targets: 2, width: '20%', searchable: true, orderable: true, className: "text-center align-middle" },
            //mensaje
            { targets: 3, width: '40%', searchable: true, orderable: true, className: "text-center align-middle" },
            //tipo
            {
                targets: 4, width: '10%', searchable: true, orderable: true, class: "text-center align-middle",
                render: function (data, type, row) {
                    // El nombre que de la variable que se pasa por data-xxx debe ser el mismo que el nombre de la columna en la base de datos
                    //<i class="bi bi-check-circle text-success"></i>
                    switch (row.tipo) {
                        case 'info':
                            return `<i class="fa-solid fa-circle-info text-info tx-24"></i>`
                            break;
                        case 'error':
                            return `<i class="fa-solid fa-circle-exclamation text-danger tx-24"></i>`
                            break;
                        case 'warning':
                            return `<i class="fa-solid fa-triangle-exclamation text-warning tx-24"></i>`
                            break;
                        default:
                            // success
                            return `<i class="fa-solid fa-circle-check text-success tx-24"></i>`
                    }
                }// de la columDef 8
            },
            //fecha_hora
            {
                targets: 5, width: '10%', orderable: true, className: "text-center align-middle",
                render: function (data, type, row) {
                    if (type === "display" || type === "filter") {
                        return formatoFechaEuropeo(data);
                    } // de la function
                    return data; // para la ordenación y el procesamiento utiliza la original  
                } // de la function
            },
            //tipo literal
            { targets: 6, searchable: true, orderable: false, visible: false },
        ], // de la columnDefs
        ajax: {
            url: '',
            dataSrc: ''
            /*****************************************************************************************/
            /************  ESTO LO UTILIZAREMOS PARA SABER LO QUE NOS TRAE el AJAX ******************/
            /***************************************************************************************/
            //dataSrc:
            //    function (json) {
            //        console.log("JSON recibido:", json); // 📌 Esto mostrará la respuesta en la consola
            //        return json.data; // Asegúrate de que tu JSON tiene la clave "data"
            //    }
            /*********************************************************************************************/
            /************  FIN ESTO LO UTILIZAREMOS PARA SABER LO QUE NOS TRAE el AJAX ******************/
            /*******************************************************************************************/
        }, // del ajax
    }; // de la variable datatable_companiesConfig
    ////////////////////////////
    // FIN DE LA TABLA DE    //
    ///////////////////////////


    /************************************/
    //     ZONA DE DEFINICIONES        //
    /**********************************/
    // Definición inicial de la tabla de empleados
    var $table = $('#logs_data');  /*<--- Es el nombre que le hemos dado a la tabla en HTML */
    var $tableConfig = datatable_logConfig; /*<--- Es el nombre que le hemos dado a la declaración de la definicion de la tabla */
    //var $columSearch = 3; /* <-- Es la columna en la cual al hacer click el valor se colocará en la zona de search y se buscará */
    var $tableBody = $('#logs_data tbody'); /*<--- Es el nombre que le hemos dado al cuerpo de la tabla en HTML */
    /* en el tableBody solo cambiar el nombre de la tabla que encontraremos en HTML*/
    var $columnFilterInputs = $('#logs_data tfoot input'); /*<--- Es el nombre que le hemos dado a los inputs de los pies de la tabla en HTML */
    /* en el $columnFilterInputs solo cambiar el nombre de la tabla que encontraremos en HTML*/

    //ejemplo -- var table_e = $('#employees-table').DataTable(datatable_employeeConfig);
    var table_e = $table.DataTable($tableConfig);

    /************************************/
    //   FIN ZONA DE DEFINICIONES      //
    /**********************************/

    ////////////////////////////////////////////
    //   INICIO ZONA FUNCIONES DE APOYO      //
    //////////////////////////////////////////


    ////////////////////////////////////////////////////////
    //        ZONA FILTROS RADIOBUTTON CABECERA           //
    ///////////////////////////////////////////////////////
    // Escuchar cambios en los radio buttons
    // Si es necesario filtrar por texto en lugar de valores numéricos, hay que asegurarse que los valores de los radio buttons coincidan con los valores de la columna.
    $('input[name="filterStatus"]').on('change', function () {
        var value = $(this).val(); // Obtener el valor seleccionado

        switch (value) {
            case 'info':
                table_e.column(6).search("info").draw();
                break;
            case 'error':
                table_e.column(6).search("error").draw();
                break;
            case 'warning':
                table_e.column(6).search("warning").draw();
                break;
            case 'success':
                table_e.column(6).search("success").draw(); // Código a ejecutar si variableAEvaluar === valorN
                break;
            default: // sera all
                table_e.column(6).search("").draw();
        } // del switch
    }); // del on change
    ////////////////////////////////////////////////////////////
    //        FIN ZONA FILTROS RADIOBUTTON CABECERA          //
    //////////////////////////////////////////////////////////

    $('#dateCreateFilter').on('change', function () {
    var selectedDate = $(this).val(); // Obtener el valor seleccionado
    var formattedDate = moment(selectedDate, 'DD-MM-YYYY').format('YYYY-MM-DD');
    var urlAjax = '../../public/logs/' + formattedDate + '.json';

    console.log("Verificando existencia de archivo:", urlAjax);

    // Comprobar si el archivo existe antes de hacer el AJAX
    $.ajax({
        url: urlAjax,
        type: 'HEAD', // Verificación rápida sin descargar contenido
        success: function () {
            console.log("Archivo encontrado, cargando DataTable...");
            table_e.ajax.url(urlAjax).load();
        },
        error: function () {
            console.warn(`No hay errores registrados para el día ${selectedDate}.`);

            // Limpiar la tabla y mostrar mensaje en lugar de hacer AJAX
            table_e.clear().draw();
            table_e.rows.add([{ usuario: '', pantalla: '', actividad: '', mensaje: `No hay errores registrados para el día ${selectedDate}.`, tipo: '', fecha_hora: '' }]).draw();
        }
    });
});


    // borrar la fecha
    $('#borrarFechaFiltro').on('click', function () {
        $('#dateCreateFilter').val('');
        $('#dateCreateFilter').trigger('change');
    });

    // cambiar el cursor
    $('#borrarFechaFiltro').on('mouseenter', function () {
        $(this).css('cursor', 'pointer');
    }).on('mouseleave', function () {
        $(this).css('cursor', 'default');
    });

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

    // Filtro de cada columna en el pie de la tabla (tfoot)
    // ejemplo - $('#employees-table tfoot input').on('keyup', function () {
    $columnFilterInputs.on('keyup', function () {
        var columnIndex = $(this).closest('th').index(); // Obtener el índice de la columna del encabezado correspondiente
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