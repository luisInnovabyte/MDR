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

    // CARGAR SELECT CON COMERCIALES PRIMERO

    cargarComercialesEnSelectFiltro('#filtroComercial');

    // FILTRO FECHA INICIO DECLARACIÓN

    $('#filtroFechaInicio').inputmask('99-99-9999');
    // NO FUNCIONA - Muestra la máscara pero no permite escribir.
    //$('#prod_telefono').inputmask('(+99) 999-999-999');


    // Configura el datepicker en español
    $.datepicker.setDefaults($.datepicker.regional['es']);

    $('#filtroFechaInicio').datepicker({
        showAnim: "slideDown",
        dateFormat: 'dd-mm-yy',
        showOtherMonths: true,
        selectOtherMonths: true,
        numberOfMonths: 1
    });

    // FILTRO FECHA FIN DECLARACIÓN

    $('#filtroFechaFin').inputmask('99-99-9999');
    // NO FUNCIONA - Muestra la máscara pero no permite escribir.
    //$('#prod_telefono').inputmask('(+99) 999-999-999');


    // Configura el datepicker en español
    $.datepicker.setDefaults($.datepicker.regional['es']);

    $('#filtroFechaFin').datepicker({
        showAnim: "slideDown",
        dateFormat: 'dd-mm-yy',
        showOtherMonths: true,
        selectOtherMonths: true,
        numberOfMonths: 1
    });

       // MODAL FECHA INICIO DECLARACIÓN

       $('#fecha_inicio').datepicker({
        dropdownParent: $("modalMantenimiento"),
        showAnim: "slideDown",
        dateFormat: 'dd-mm-yy',
        showOtherMonths: true,
        selectOtherMonths: true,
        numberOfMonths: 1
    });

       // Configura el datepicker en español
       $.datepicker.setDefaults($.datepicker.regional['es']);
   
       // MODAL FECHA FIN DECLARACIÓN
   
       $('#fecha_fin').inputmask('99-99-9999');
       // NO FUNCIONA - Muestra la máscara pero no permite escribir.
       //$('#prod_telefono').inputmask('(+99) 999-999-999');
   
   
       // Configura el datepicker en español
       $.datepicker.setDefaults($.datepicker.regional['es']);
   
       $('#fecha_fin').datepicker({
           showAnim: "slideDown",
           dateFormat: 'dd-mm-yy',
           showOtherMonths: true,
           selectOtherMonths: true,
           numberOfMonths: 1
       });


    var formValidator = new FormValidator('formVacacion', {
        id_comercial: {
            required: true
        },
        fecha_inicio: {
            pattern: '^\\d{2}-\\d{2}-\\d{4}$',
            required: true
        },
        fecha_fin: {
            pattern: '^\\d{2}-\\d{2}-\\d{4}$',
            required: true
        },
        descripcion: {
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

    var modoBusqueda = 'listar'; // 'listar' o 'filtrarPorFecha'
    var datatable_vacacionesConfig = {
        //serverSide: true, // procesamiento del lado del servidor
        processing: true, // mostrar el procesamiento de la tabla
        serverside: true,
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
        order: [[2, 'desc']], // Orden inicial por fecha_inicio en orden descendente
        columns: [

            // Son los botones para más
            // No tocar
            { name: 'control', data: null, defaultContent: '', className: 'details-control sorting_1 text-center' }, // Columna 0: Mostrar más
            { name: 'id_vacacion', data: 'id_vacacion', visible: false }, // Columna 1: ID_VACACION
            { name: 'id_comercial', data: 'id_comercial' }, // Columna 2: ID_COMERCIAL
            { name: 'fecha_inicio', data: 'fecha_inicio' }, // Columna 3: FECHA_INICIO
            { name: 'fecha_fin', data: 'fecha_fin' }, // Columna 4: FECHA_FIN
            { name: 'descripcion', data: 'descripcion', visible: false }, // Columna 5: DESCRIPCION
            { name: 'activo_vacacion', data: 'activo_vacacion'}, // Columna 6: ACTIVO_VACACION
            { name: 'activar', data: null, defaultContent: '', className: 'text-center' },  // Columna //  7: ACTIVAR/DESACTIVAR
            { name: 'editar', data: null, defaultContent: '', className: 'text-center' },  // Columna 8: EDITAR
            
        ], // de las columnas
        columnDefs: [
            // Cuidado que el ordrData puede interferir con el ordenamiento de la tabla    
            // Columna 0: BOTÓN MÁS 
            { 
              targets: 'control:name', width: '5%', searchable: false, orderable: false, className: "text-center" 
            },    
            // id_vacacion
            { targets: 'id_vacacion:name', width: '6%', searchable: false, orderable: false },
            // Columna 2: id_comercial MOSTRAR NOMBRE COMERCIAL
            {
                targets: 'id_comercial:name',
                width: '10%',
                searchable: true,
                orderable: true,
                className: "text-center",
                render: function(data, type, row) {
                    if (type === "display") {
                        return row.nombre_comercial; // Solo para visualización
                    }
                    // Para filtrado, ordenamiento y procesamiento interno usa el ID + nombre
                    return row.id_comercial + "|" + row.nombre_comercial;
                }
            },
            // Columna 3: fecha_inicio
            {
                // Columna de Fecha de inicio (solo visualización)
                targets: 'fecha_inicio:name',
                width: '10%',
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
            // Columna 4: fecha_fin
            {
                // Columna de Fecha de fin (solo visualización)
                targets: 'fecha_fin:name',
                width: '10%',
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
            // Columna 5: descripcion
            { targets: 'descripcion:name', width: '20%', searchable: true, orderable: false,  className: "text-center" },
            // Columna 6: activo_vacacion
            {
                targets: 'activo_vacacion:name', width: '10%', orderable: true, searchable: true, className: "text-center",
                render: function (data, type, row) {
                    if (type === "display") {
                        return data == 1 ? '<i class="bi bi-check-circle text-success fa-2x"></i>' : '<i class="bi bi-x-circle text-danger fa-2x"></i>';
                    }
                    return data;
                }
            },
            {   // Columna 7: BOTON PARA ACTIVAR/DESACTIVAR VACACION
                targets: 'activar:name', width: '10%', searchable: false, orderable: false, class: "text-center",
                render: function (data, type, row) {
                    // El nombre que de la variable que se pasa por data-xxx debe ser el mismo que el nombre de la columna en la base de datos
                    if (row.activo_vacacion == 1) {
                        // permito desactivar el estado
                        return `<button type="button" class="btn btn-danger btn-sm desacVacacion" data-bs-toggle="tooltip-primary" data-placement="top" title="Desactivar" data-original-title="Tooltip on top" 
                             data-id_vacacion="${row.id_vacacion}"> 
                              <i class="fa-solid fa-trash"></i>
                             </button>`}
                    else {
                        // debo permitir activar de nuevo el estado
                        return `<button class="btn btn-success btn-sm activarVacacion" data-bs-toggle="tooltip-primary" data-placement="top" title="Activar" data-original-title="Tooltip on top" 
                             data-id_vacacion="${row.id_vacacion}"> <!-- Cambiado de data-id a data-prod_id -->
                             <i class="bi bi-hand-thumbs-up-fill"></i>
                            </button>`}
                } // de la function
            },// 
            {   // Columna 8: BOTON PARA EDITAR VACACION
                targets: 'editar:name', width: '10%', searchable: false, orderable: false, class: "text-center",
                render: function (data, type, row) {
                    // El nombre que de la variable que se pasa por data-xxx debe ser el mismo que el nombre de la columna en la base de datos
                    // botón editar el producto
                    return `<button type="button" class="btn btn-info btn-sm editarVacacion" data-toggle="tooltip-primary" data-placement="top" title="Editar"  
                             data-id_vacacion="${row.id_vacacion}"> 
                             <i class="fa-solid fa-edit"></i>
                             </button>`
                } // de la function
            },
             // De la columna 9
        ], // de la columnDefs
        ajax: getAjaxConfig(),
        order: [[3, 'asc']], // ordenar por la columna 3
        rowGroup: {
            dataSrc: function (row) {
                return row.nombre_comercial;
            },
            startRender: function (rows, group) {
                let $row = $('<tr/>').append('<td colspan="7" class="group-header">' + group + ' / ' + rows.count() + ' vacaciones' + '</td>');
                return $row;
            } // de la function startRender
        } // de la rowGroup
    };
    ////////////////////////////
    // FIN DE LA TABLA DE    //
    ///////////////////////////

    // 2. Función para obtener configuración AJAX dinámica
    function getAjaxConfig() {
        console.log('Evaluando modo:', modoBusqueda); // Log importante
        
        switch(modoBusqueda) {
            case "listar":
                console.log('Configurando para LISTAR');
                return {
                    url: '../../controller/vacaciones.php?op=listar',
                    type: 'GET',
                    dataSrc: function(json) {
                        console.log("Datos completos recibidos LISTAR CASE:", json);
                        return json.data || json;
                    }
                };
                
                case "filtrarPorFecha":
                // Función para formatear fecha a YYYY-MM-DD
                const formatDate = (dateStr) => {
                    const [day, month, year] = dateStr.split('-');
                    return `${year}-${month.padStart(2,'0')}-${day.padStart(2,'0')}`;
                };

                const fechaInicio = $('#filtroFechaInicio').val();
                const fechaFin = $('#filtroFechaFin').val();
                
                if (!fechaInicio || !fechaFin) {
                    Swal.fire('Error', 'Selecciona ambas fechas', 'warning');
                    throw new Error("Fechas no seleccionadas");
                }

                return {
                    url: `../../controller/vacaciones.php?op=filtrarPorFecha&fecha_inicio=${formatDate(fechaInicio)}&fecha_fin=${formatDate(fechaFin)}`,
                    type: 'GET', // Cambiado a GET para coincidir con tu controlador
                    dataSrc: function(json) {
                        if (json.error) {
                            Swal.fire('Error', json.error, 'error');
                            return [];
                        }
                        return json.data || json;
                    }
                };
                
            default:
                console.warn('Modo desconocido, usando LISTAR por defecto');
                return {
                    url: '../../controller/vacaciones.php?op=listar',
                    type: 'GET',
                    dataSrc: function(json) {
                        console.log("Datos por defecto recibidos LISTAR:", json);
                        return json.data || json;
                    }
                };
        }
    }

    /************************************/
    //     ZONA DE DEFINICIONES        //
    /**********************************/
    // Definición inicial de la tabla de empleados
    var $table = $('#vacaciones_data');  /*<--- Es el nombre que le hemos dado a la tabla en HTML */
    var $tableConfig = datatable_vacacionesConfig; /*<--- Es el nombre que le hemos dado a la declaración de la definicion de la tabla */
    //var $columSearch = 3; /* <-- Es la columna en la cual al hacer click el valor se colocará en la zona de search y se buscará */
    var $tableBody = $('#vacaciones_data tbody'); /*<--- Es el nombre que le hemos dado al cuerpo de la tabla en HTML */
    /* en el tableBody solo cambiar el nombre de la tabla que encontraremos en HTML*/
    var $columnFilterInputs = $('#vacaciones_data tfoot input'); /*<--- Es el nombre que le hemos dado a los inputs de los pies de la tabla en HTML */
    /* en el $columnFilterInputs solo cambiar el nombre de la tabla que encontraremos en HTML*/

    //ejemplo -- var table_e = $('#employees-table').DataTable(datatable_employeeConfig);
    // Versión alternativa con más control
var table_e = $table.DataTable($tableConfig);

    /***************************************************/
    //   CAMPOS SOLO NUMÉRICOS PIES FILTROS            //
    /**************************************************/

    $('#fechaInicioFiltroPies').on('keypress', function (event) {
        var charCode = (event.which) ? event.which : event.keyCode;
        var currentValue = $(this).val();
        var cursorPos = this.selectionStart;

        // Si es guion (-)
        if (charCode === 45) {
            // Bloquear si el carácter anterior es también un guion
            if (cursorPos > 0 && currentValue.charAt(cursorPos - 1) === '-') {
                event.preventDefault();
                return;
            }
        }
        // Si no es número (48-57), bloquear
        else if (charCode < 48 || charCode > 57) {
            event.preventDefault();
        }
    });

    $('#fechaInicioFiltroPies').on('input', function () {
        // Eliminar caracteres que no sean número o guion
        this.value = this.value.replace(/[^0-9\-]/g, '');

        // También eliminar guiones dobles si se pegan
        this.value = this.value.replace(/--+/g, '-');
    });

    $('#fechaFinFiltroPies').on('keypress', function (event) {
        var charCode = (event.which) ? event.which : event.keyCode;
        var currentValue = $(this).val();
        var cursorPos = this.selectionStart;

        // Si es guion (-)
        if (charCode === 45) {
            // Bloquear si el carácter anterior es también un guion
            if (cursorPos > 0 && currentValue.charAt(cursorPos - 1) === '-') {
                event.preventDefault();
                return;
            }
        }
        // Si no es número (48-57), bloquear
        else if (charCode < 48 || charCode > 57) {
            event.preventDefault();
        }
    });

    $('#fechaFinFiltroPies').on('input', function () {
        // Eliminar caracteres que no sean número o guion
        this.value = this.value.replace(/[^0-9\-]/g, '');

        // También eliminar guiones dobles si se pegan
        this.value = this.value.replace(/--+/g, '-');
    });


    /*********************************************************/
    //     APLICAR FILTRO ACTIVO AL INICIAR LA TABLA        //
    /*******************************************************/

// Esperar a que los datos estén listos
table_e.on('init.dt', function() {
    var initialValue = $('input[name="filterStatus"]:checked').val();
    
    // Capturar el orden actual antes de aplicar el filtro
    var currentOrder = table_e.order(); 
    
    // Aplicar el filtro
    table_e.column(6).search(initialValue === "all" ? "" : initialValue).draw();
    
    // Restaurar el orden después de aplicar el filtro
    table_e.order(currentOrder).draw();
});

    /*******************************************************************************/
    //     APLICAR FILTRO PARA RANGO DE FECHAS, TAMBIEN PARA QUITAR FILTRO        //
    /******************************************************************************/

// Función optimizada para recargar
function recargarTabla() {
    console.log('Recargando tabla en modo:', modoBusqueda);
    var config = getAjaxConfig();
    
    // Forma correcta de recargar con nuevos parámetros
    table_e.ajax.url(config.url).load(null, function(json) {
        console.log('Datos recargados:', json);
    });
}
/* ========== CONTROLADORES DE EVENTOS ========== */
$('#btnFiltrarFecha').click(function() {
    const fechaInicio = $('#filtroFechaInicio').val();
    const fechaFin = $('#filtroFechaFin').val();
    
    // Validaciones
    if (!fechaInicio || !fechaFin) {
        Swal.fire('Error', 'Selecciona ambas fechas', 'warning');
        return;
    }
    
    const fechaInicioObj = new Date(fechaInicio.split('-').reverse().join('-'));
    const fechaFinObj = new Date(fechaFin.split('-').reverse().join('-'));
    
    if (fechaInicioObj > fechaFinObj) {
        Swal.fire('Error', 'La fecha inicio no puede ser mayor', 'error');
        return;
    }
    
    modoBusqueda = 'filtrarPorFecha';
    recargarTabla();
    
    Swal.fire({
        title: 'Filtrando...',
        html: `Mostrando de <b>${fechaInicio}</b> a <b>${fechaFin}</b>`,
        timer: 1000,
        showConfirmButton: false
    });
});

$('#btnQuitarFiltro').click(function() {
    $('#filtroFechaInicio').val('');
    $('#filtroFechaFin').val('');
    modoBusqueda = 'listar';
    recargarTabla();
    
    Swal.fire({
        icon: 'success',
        title: 'Filtro eliminado',
        text: 'Mostrando todos los registros',
        timer: 1500,
        showConfirmButton: false
    });
});


$('#borrarFechaInicioFiltro').click(function() {
    $('#filtroFechaInicio').val('');
});

$('#borrarFechaFinFiltro').click(function() {
    $('#filtroFechaFin').val('');
});

function format(d) {
    return `
        <div class="card border-primary mb-3" style="overflow: visible;">
            <div class="card-header bg-primary text-white">
                <div class="d-flex align-items-center">
                    <i class="bi bi-calendar-event fs-3 me-2"></i>
                    <h5 class="card-title mb-0">Detalles de la Vacación</h5>
                </div>
            </div>
            <div class="card-body p-0" style="overflow: visible;">
                <table class="table table-borderless table-striped table-hover mb-0">
                    <tbody>
                        <tr>
                            <th scope="row" class="ps-4 w-25 align-top"><i class="bi bi-calendar-check-fill me-2"></i>Id Vacación</th>
                            <td class="pe-4" style="white-space: pre-wrap; word-wrap: break-word;">${d.id_vacacion || '<span class="text-muted fst-italic">No tiene id vacación</span>'}</td>
                        </tr>
                        <tr>
                            <th scope="row" class="ps-4 align-top"><i class="bi bi-file-earmark-text-fill me-2"></i>Descripción</th>
                            <td class="pe-4" style="white-space: pre-wrap; word-wrap: break-word;">${d.descripcion || '<span class="text-muted fst-italic">No tiene descripción</span>'}</td>
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


    /************************************/
    //   FIN ZONA DE DEFINICIONES      //
    /**********************************/

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

    // Función para convertir la fecha a formato YYYY-MM-DD
    function convertirFechaAFormatoISO(fecha) {
        // Separar la fecha en partes: [DD, MM, YYYY]
        var partes = fecha.split("-"); // La fecha se divide en tres partes por el guion (-)
        
        // Retornar la fecha en el formato YYYY-MM-DD
        return partes[2] + "-" + partes[1] + "-" + partes[0]; // Concatenar el año, mes y día con guiones (-)
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

    function configurarSelect2Comerciales(selector = '#id_comercial') {
    $(selector).select2({
        width: '100%',
        dropdownParent: $('#modalMantenimiento .modal-content'),
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

    function cargarComercialesEnSelectFiltro(selectId) {
        $.post("../../controller/comerciales.php?op=listar", function(data) {
            try {
                const select = $(selectId);
                
                // Limpiar y agregar opción "Todos" como base
                select.empty().append(
                    '<option value="" selected>Todos los comerciales</option>'
                );
                
                
                if (data && data.data) {
                    // Añadir comerciales directamente
                    data.data.forEach(comercial => {
                        
                        select.append(
                            `<option value="${comercial.id_comercial}">${comercial.nombre + " " + comercial.apellidos}</option>`
                        );
                    });
                }
                
                // Inicializar Select2 mínimo
                 select.select2({
                    width: '100%',
                    minimumResultsForSearch: 3 // Opcional: solo muestra search si hay +3 opciones
                });
    
            } catch (e) {
                console.error('Error:', e);
                $(selectId).html('<option value="">Error al cargar</option>');
            }
        }, "json").fail(function() {
            $(selectId).html('<option value="">Error al conectar</option>');
        });
    }

    $('#filtroComercial').on('change', function() {
        var idComercial = $(this).val();
        
        if (idComercial === "") {
            // Si no se seleccionó nada, limpia la búsqueda
            table_e.column(2).search("").draw();
        } else {
            // Busca el ID exacto antes del | (solo el valor numérico de id_comercial)
            table_e.column(2).search('^' + idComercial + '\\|', true, false).draw();
        }
    });
    
    /////////////////////////////////////
    //   INICIO ZONA DELETE VACACIONES  //
    ///////////////////////////////////
    function desacVacacion(id) {
        swal.fire({
            title: 'Desactivar',
            text: `¿Desea desactivar la vacación con ID ${id}?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Si',
            cancelButtonText: 'No',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                $.post("../../controller/vacaciones.php?op=eliminar", { id_vacacion: id }, function (data) { // Cambiado a prod_id

                    $table.DataTable().ajax.reload();

                    swal.fire(
                        'Desactivado',
                        'La vacación ha sido desactivada',
                        'success'
                    )
                });
            }
        })
    }


    // CAPTURAR EL CLICK EN EL BOTÓN DE BORRAR
    $(document).on('click', '.desacVacacion', function (event) {
        event.preventDefault();
        let id = $(this).data('id_vacacion'); // Cambiado de data('id') a data('prod_id')
        desacVacacion(id);
    });
    ////////////////////////////////////
    //   FIN ZONA DELETE VACACION    //
    //////////////////////////////////

    ///////////////////////////////////////
    //   INICIO ZONA ACTIVAR VACACION  //
    /////////////////////////////////////
    function activarVacacion(id) {
        swal.fire({
            title: 'Activar',
            text: `¿Desea activar la vacación con ID ${id}?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Si',
            cancelButtonText: 'No',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                $.post("../../controller/vacaciones.php?op=activar", { id_vacacion: id }, function (data) {

                    $table.DataTable().ajax.reload();

                    swal.fire(
                        'Activado',
                        'La vacación ha sido activada',
                        'success'
                    )
                });
            }
        })
    }


    // CAPTURAR EL CLICK EN EL BOTÓN DE ACTIVAR
    $(document).on('click', '.activarVacacion', function (event) { // Sin acento
        event.preventDefault();
        let id = $(this).data('id_vacacion');
        activarVacacion(id);
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
        $('#mdltitulo').text('Nuevo registro de vacación');

        $('#modalMantenimiento').modal('show');

        // Limpiar el formulario
        $("#formVacacion")[0].reset();

        // RESETEAR ID SEGURO
        $('#formVacacion').find('input[name="id_vacacion"]').val("");

        // Limpiar las validaciones
        formValidator.clearValidation(); // Llama al método clearValidation

        cargarComercialesEnSelect('#id_comercial');
        configurarSelect2Comerciales();

        // Mostrar el mantenimiento(modal) con el foco en el primer campo
        $('#modalMantenimiento').on('shown.bs.modal', function () {
            $('#modalMantenimiento .modal-body #id_comercial').focus();
        });

        //console.log('Modal mostrado');
    });

    // CAPTURAR EL CLICK EN EL BOTÓN DE SALVAR
    $(document).on('click', '#btnsalvar', function (event) {
        event.preventDefault();
        
        // Validación de fechas
        var fechaInicioV = $('#formVacacion').find('input[name="fecha_inicio"]').val().trim();
        var fechaFinV = $('#formVacacion').find('input[name="fecha_fin"]').val().trim();
        
        if (!validarRangoFechas(fechaInicioV, fechaFinV)) {
            return;
        }
    
        // Obtener datos del formulario
        var idV = $('#formVacacion').find('input[name="id_vacacion"]').val().trim();
        var idComercialV = $('#formVacacion').find('select[name="id_comercial"]').val().trim();
        var descripcionV = $('#formVacacion').find('input[name="descripcion"]').val().trim();
        
        // Validación del formulario
        const isValid = formValidator.validateForm(event);
        if (!isValid) {
            toastr.error(`Por favor, corrija los errores en el formulario.`, 'Error de Validación');
            return;
        }
        
        // Convertir fechas a formato ISO (YYYY-MM-DD)
        var fechaInicioV_ISO = convertirFechaAFormatoISO(fechaInicioV);
        var fechaFinV_ISO = convertirFechaAFormatoISO(fechaFinV);
        
        console.log("Enviando para verificación:", {
            id_comercial: idComercialV,
            fecha_inicio: fechaInicioV_ISO,
            fecha_fin: fechaFinV_ISO,
            id_vacacion: idV || null
        });
    
        // Verificar solapamiento
        $.ajax({
            url: "../../controller/vacaciones.php?op=verificarSolapamiento",
            type: "POST",
            data: {
                id_comercial: idComercialV,
                fecha_inicio: fechaInicioV_ISO,
                fecha_fin: fechaFinV_ISO,
                id_vacacion: idV || ''
            },
            dataType: "json",
            success: function(response) {
                console.log("Respuesta de verificación:", response);
                if (response.solapamiento) {
                    swal.fire({
                        title: 'Conflicto de fechas',
                        text: 'El comercial ya tiene vacaciones programadas en este período',
                        icon: 'warning',
                        confirmButtonText: 'Entendido'
                    });
                    return;
                }
    
                // Continuar con el guardado...
                var formData = new FormData();
                formData.append('id_comercial', idComercialV);
                formData.append('fecha_inicio', fechaInicioV_ISO);
                formData.append('fecha_fin', fechaFinV_ISO);
                formData.append('descripcion', descripcionV);
                if (idV) formData.append('id_vacacion', idV);
    
                $.ajax({
                    url: "../../controller/vacaciones.php?op=guardaryeditar",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        $('#modalMantenimiento').modal('hide');
                        $table.DataTable().ajax.reload();
                        $("#formVacacion")[0].reset();
                        toastr.success("La vacación ha sido guardada", "Guardado");
                    },
                    error: function(xhr) {
                        console.error("Error al guardar:", xhr.responseText);
                        swal.fire(
                            'Error',
                            'No se pudo guardar la vacación',
                            'error'
                        );
                    }
                });
            },
            error: function(xhr) {
                console.error("Error en verificación:", xhr.responseText);
                swal.fire(
                    'Error',
                    'Error al verificar disponibilidad',
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
    $(document).on('click', '.editarVacacion', function (event) {
        event.preventDefault();

        // Limpiar las validaciones
        formValidator.clearValidation(); // Llama al método clearValidation

        let id = $(this).data('id_vacacion');
        //        console.log('Antes del click', id);
        $.post("../../controller/vacaciones.php?op=mostrar", { id_vacacion: id }, function (data) {
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

                cargarComercialesEnSelect('#id_comercial', data.id_comercial);
                configurarSelect2Comerciales();

                $('#mdltitulo').text('Edición registro vacacion');
                $('#modalMantenimiento').modal('show');

                $('#modalMantenimiento .modal-body #id_vacacion').val(data.id_vacacion);

                // AQUI HAY QUE HACER UN AJAX PARA RECORRER EL SELECT Y PONER EL QUE COINCIDA
                // CON EL ID COMERCIAL

                $('#modalMantenimiento .modal-body #fecha_inicio').val(formatoFechaEuropeo(data.fecha_inicio));
                $('#modalMantenimiento .modal-body #fecha_fin').val(formatoFechaEuropeo(data.fecha_fin));
                $('#modalMantenimiento .modal-body #descripcion').val(data.descripcion);
                
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
    ////////////////////////////////////////////////////////////
    //        FIN ZONA FILTROS RADIOBUTTON CABECERA          //
    //////////////////////////////////////////////////////////

// 5. Efectos hover mejorados
$('#borrarFechaInicioFiltro, #borrarFechaFinFiltro')
    .on('mouseenter', function() {
        $(this).css({
            'cursor': 'pointer',
            'opacity': '0.8',
            'text-decoration': 'none' // ← Elimina el subrayado
        });
    })
    .on('mouseleave', function() {
        $(this).css({
            'cursor': 'default',
            'opacity': '1',
            'text-decoration': 'none' // ← Mantiene sin subrayado al salir
        });
    });

     // borrar la fecha de inicio del modal
     $('#borrarFechaInicioCarrera').on('click', function () {
        $('#fecha_inicio').val('');
        $('#fecha_inicio').trigger('change');
    });

    // cambiar el cursor
    $('#borrarFechaInicioCarrera').on('mouseenter', function () {
        $(this).css('cursor', 'pointer');
    }).on('mouseleave', function () {
        $(this).css('cursor', 'default');
    });

    // borrar la fecha de fin del modal
    $('#borrarFechaFinCarrera').on('click', function () {
        $('#fecha_fin').val('');
        $('#fecha_fin').trigger('change');
    });

    // cambiar el cursor
    $('#borrarFechaFinCarrera').on('mouseenter', function () {
        $(this).css('cursor', 'pointer');
    }).on('mouseleave', function () {
        $(this).css('cursor', 'default');
    });

    
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