$(document).ready(function () {

    /////////////////////////////////////
    //            TIPS                //
    ///////////////////////////////////
    // Ocultar dinámicamente la columna con índice 2 (tercera columna)
    // ----> $('#miTabla').DataTable().column(2).visible(false);

    /////////////////////////////////////
    //          FIN DE TIPS           //
    ///////////////////////////////////

    // Obtener el ID de la furgoneta desde la URL
    const urlParams = new URLSearchParams(window.location.search);
    const idFurgoneta = urlParams.get('id_furgoneta');

    if (!idFurgoneta) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se especificó la furgoneta',
            confirmButtonText: 'Volver',
        }).then(() => {
            window.location.href = '../MntFurgonetas/index.php';
        });
        return;
    }

    // Cargar información de la furgoneta
    cargarInfoFurgoneta(idFurgoneta);

    /////////////////////////////////////
    //     FORMATEO DE CAMPOS          //
    ///////////////////////////////////
    // FormValidator removido - ahora se maneja en formularioKilometraje.js
    /////////////////////////////////////////
    //     FIN FORMATEO DE CAMPOS          //
    ////////////////////////////////////////


    /////////////////////////////////////
    // INICIO DE LA TABLA DE KILOMETRAJE //
    //         DATATABLES             //
    ///////////////////////////////////
    var datatable_kilometrajeConfig = {
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
            { name: 'id_registro_km', data: 'id_registro_km', visible: false, className: "text-center" }, // Columna 1: ID_REGISTRO_KM
            { name: 'fecha_registro_km', data: 'fecha_registro_km', className: "text-center align-middle" }, // Columna 2: FECHA
            { name: 'kilometraje_registrado_km', data: 'kilometraje_registrado_km', className: "text-center align-middle" }, // Columna 3: KILOMETRAJE
            { name: 'km_recorridos', data: 'km_recorridos', className: "text-center align-middle" }, // Columna 4: KM RECORRIDOS
            { name: 'dias_transcurridos', data: 'dias_transcurridos', className: "text-center align-middle" }, // Columna 5: DÍAS
            { name: 'km_promedio_diario', data: 'km_promedio_diario', className: "text-center align-middle" }, // Columna 6: KM/DÍA
            { name: 'tipo_registro_km', data: 'tipo_registro_km', className: "text-center align-middle" }, // Columna 7: TIPO
            { name: 'observaciones_registro_km', data: 'observaciones_registro_km', className: "text-left align-middle" }, // Columna 8: OBSERVACIONES
            { name: 'created_at_registro_km', data: 'created_at_registro_km', className: "text-center align-middle" }, // Columna 9: FECHA REGISTRO
            { name: 'editar', data: null, defaultContent: '', className: "text-center align-middle" },  // Columna 10: EDITAR
            
        ], // de las columnas
        columnDefs: [
            // Cuidado que el ordrData puede interferir con el ordenamiento de la tabla    
           
            // Columna 0: BOTÓN MÁS 
            { targets: "control:name", width: '5%', searchable: false, orderable: false, className: "text-center"},
            // Columna 1: id_registro_km 
            { targets: "id_registro_km:name", width: '5%', searchable: false, orderable: false, className: "text-center" },
            // Columna 2: fecha_registro_km
            { 
                targets: "fecha_registro_km:name", 
                width: '10%', 
                searchable: true, 
                orderable: true, 
                className: "text-center",
                render: function (data, type, row) {
                    if (type === "display" && data) {
                        if (!data) return '-';
                        const fecha = new Date(data);
                        return fecha.toLocaleDateString('es-ES', {
                            year: 'numeric',
                            month: '2-digit',
                            day: '2-digit'
                        });
                    }
                    return data;
                }
            },
            // Columna 3: kilometraje_registrado_km
            { 
                targets: "kilometraje_registrado_km:name", 
                width: '12%', 
                searchable: true, 
                orderable: true, 
                className: "text-center",
                render: function (data, type, row) {
                    if (type === "display") {
                        return data ? Number(data).toLocaleString('es-ES') + ' km' : '0 km';
                    }
                    return data;
                }
            },
            // Columna 4: km_recorridos
            { 
                targets: "km_recorridos:name", 
                width: '10%', 
                searchable: false, 
                orderable: true, 
                className: "text-center",
                render: function (data, type, row) {
                    if (type === "display") {
                        console.log("km_recorridos data:", data, "tipo:", typeof data);
                        const km = parseFloat(data);
                        if (!km || km === 0) {
                            return '<span class="text-muted fst-italic" title="Primer registro">-</span>';
                        }
                        const badge = km > 0 ? 'bg-success' : 'bg-secondary';
                        return '<span class="badge ' + badge + '">' + km.toLocaleString('es-ES') + ' km</span>';
                    }
                    return data;
                }
            },
            // Columna 5: dias_transcurridos
            { 
                targets: "dias_transcurridos:name", 
                width: '8%', 
                searchable: false, 
                orderable: true, 
                className: "text-center",
                render: function (data, type, row) {
                    if (type === "display") {
                        console.log("dias_transcurridos data:", data, "tipo:", typeof data);
                        const dias = parseInt(data);
                        if (!dias || dias === 0) {
                            return '<span class="text-muted fst-italic" title="Primer registro o mismo día">-</span>';
                        }
                        return dias + ' días';
                    }
                    return data;
                }
            },
            // Columna 6: km_promedio_diario
            { 
                targets: "km_promedio_diario:name", 
                width: '10%', 
                searchable: false, 
                orderable: true, 
                className: "text-center",
                render: function (data, type, row) {
                    if (type === "display") {
                        console.log("km_promedio_diario data:", data, "tipo:", typeof data);
                        const promedio = parseFloat(data);
                        if (!promedio || promedio === 0) {
                            return '<span class="text-muted fst-italic" title="No hay suficiente información para calcular">-</span>';
                        }
                        return promedio.toFixed(1) + ' km/día';
                    }
                    return data;
                }
            },
            // Columna 7: tipo_registro_km
            { 
                targets: "tipo_registro_km:name", 
                width: '10%', 
                searchable: true, 
                orderable: true, 
                className: "text-center",
                render: function (data, type, row) {
                    if (type === "display") {
                        const badges = {
                            'manual': '<span class="badge bg-primary">Manual</span>',
                            'revision': '<span class="badge bg-warning">Revisión</span>',
                            'itv': '<span class="badge bg-info">ITV</span>',
                            'evento': '<span class="badge bg-danger">Evento</span>'
                        };
                        return badges[data] || '<span class="badge bg-secondary">' + data + '</span>';
                    }
                    return data;
                }
            },
            // Columna 8: observaciones_registro_km
            { 
                targets: "observaciones_registro_km:name", 
                width: '20%', 
                searchable: true, 
                orderable: false, 
                className: "text-left",
                render: function (data, type, row) {
                    if (type === "display") {
                        if (!data) return '-';
                        return data.length > 50 ? data.substring(0, 50) + '...' : data;
                    }
                    return data;
                }
            },
            // Columna 9: created_at_registro_km
            { 
                targets: "created_at_registro_km:name", 
                width: '12%', 
                searchable: false, 
                orderable: true, 
                className: "text-center",
                render: function (data, type, row) {
                    if (type === "display" && data) {
                        if (!data) return '-';
                        const fecha = new Date(data);
                        return fecha.toLocaleDateString('es-ES', {
                            year: 'numeric',
                            month: '2-digit',
                            day: '2-digit',
                            hour: '2-digit',
                            minute: '2-digit'
                        });
                    }
                    return data;
                }
            },
            // Columna 10: BOTON PARA EDITAR KILOMETRAJE
            {   
                targets: "editar:name", width: '7%', searchable: false, orderable: false, class: "text-center",
                render: function (data, type, row) {
                    // El nombre que de la variable que se pasa por data-xxx debe ser el mismo que el nombre de la columna en la base de datos
                    // botón editar el kilometraje
                    return `<button type="button" class="btn btn-info btn-sm editarKilometraje" data-toggle="tooltip-primary" data-placement="top" title="Editar"  
                             data-id_registro_km="${row.id_registro_km}"> 
                             <i class="fa-solid fa-edit"></i>
                             </button>`
                } // de la function
            }
             // De la columna 10
        ], // de la columnDefs
        ajax: {
            url: '../../controller/furgoneta_registro_kilometraje.php?op=listar_por_furgoneta',
            type: 'GET',
            data: function() {
                return {
                    id_furgoneta: idFurgoneta
                };
            },
            dataSrc: function (json) {
                console.log("JSON recibido:", json); // 📌 Ver qué estructura tiene
                return json.data || json; // Ajusta en función de lo recibido
            }
        } // del ajax
    }; // de la variable datatable_kilometrajeConfig
    ////////////////////////////
    // FIN DE LA TABLA DE KILOMETRAJE //
    ///////////////////////////


    /************************************/
    //     ZONA DE DEFINICIONES        //
    /**********************************/
    // Definición inicial de la tabla de kilometrajes
    var $table = $('#kilometrajes_data');  /*<--- Es el nombre que le hemos dado a la tabla en HTML */
    var $tableConfig = datatable_kilometrajeConfig; /*<--- Es el nombre que le hemos dado a la declaración de la definicion de la tabla */
    var $tableBody = $('#kilometrajes_data tbody'); /*<--- Es el nombre que le hemos dado al cuerpo de la tabla en HTML */
    /* en el tableBody solo cambiar el nombre de la tabla que encontraremos en HTML*/
    var $columnFilterInputs = $('#kilometrajes_data tfoot input, #kilometrajes_data tfoot select'); /*<--- Selecciona tanto inputs como selects de los pies de la tabla en HTML */
    /* en el $columnFilterInputs solo cambiar el nombre de la tabla que encontraremos en HTML*/

    //ejemplo -- var table_e = $('#kilometrajes-table').DataTable(datatable_kilometrajeConfig);
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
                        <i class="bi bi-speedometer2 fs-3 me-2"></i>
                        <h5 class="card-title mb-0">Detalles del Registro de Kilometraje</h5>
                    </div>
                </div>
                <div class="card-body p-0" style="overflow: visible;">
                    <table class="table table-borderless table-striped table-hover mb-0">
                        <tbody>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-hash me-2"></i>Id Registro
                                </th>
                                <td class="pe-4">
                                    ${d.id_registro_km || '<span class="text-muted fst-italic">No tiene id registro</span>'}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-calendar me-2"></i>Fecha Registro
                                </th>
                                <td class="pe-4">
                                    ${d.fecha_registro_km ? formatoFechaEuropeo(d.fecha_registro_km) : '<span class="text-muted fst-italic">No tiene fecha</span>'}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-speedometer me-2"></i>Kilometraje Registrado
                                </th>
                                <td class="pe-4">
                                    ${d.kilometraje_registrado_km ? Number(d.kilometraje_registrado_km).toLocaleString('es-ES') + ' km' : '<span class="text-muted fst-italic">No tiene kilometraje</span>'}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-arrow-up-right me-2"></i>KM Recorridos
                                </th>
                                <td class="pe-4">
                                    ${d.km_recorridos && d.km_recorridos != 0 ? Number(d.km_recorridos).toLocaleString('es-ES') + ' km' : '<span class="text-muted fst-italic">Primer registro</span>'}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-clock me-2"></i>Días Transcurridos
                                </th>
                                <td class="pe-4">
                                    ${d.dias_transcurridos && d.dias_transcurridos != 0 ? d.dias_transcurridos + ' días' : '<span class="text-muted fst-italic">Primer registro o mismo día</span>'}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-graph-up me-2"></i>Promedio Diario
                                </th>
                                <td class="pe-4">
                                    ${d.km_promedio_diario && d.km_promedio_diario != 0 ? Number(d.km_promedio_diario).toFixed(1) + ' km/día' : '<span class="text-muted fst-italic">No calculable</span>'}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-tag me-2"></i>Tipo de Registro
                                </th>
                                <td class="pe-4">
                                    ${d.tipo_registro_km || '<span class="text-muted fst-italic">No tiene tipo</span>'}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-chat-left-text me-2"></i>Observaciones
                                </th>
                                <td class="pe-4" style="max-width: 300px; word-wrap: break-word; white-space: pre-wrap; overflow-wrap: break-word;">
                                    ${d.observaciones_registro_km || '<span class="text-muted fst-italic">No tiene observaciones</span>'}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-calendar-plus me-2"></i>Registro creado el:
                                </th>
                                <td class="pe-4">
                                    ${d.created_at_registro_km ? formatoFechaEuropeo(d.created_at_registro_km) : '<span class="text-muted fst-italic">Sin fecha</span>'}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-calendar-check me-2"></i>Registro actualizado el:
                                </th>
                                <td class="pe-4">
                                  ${d.updated_at_registro_km ? formatoFechaEuropeo(d.updated_at_registro_km) : '<span class="text-muted fst-italic">Sin fecha</span>'}
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


    ///////////////////////////////////////
    //      INICIO ZONA EDITAR           //
    //        BOTON DE EDITAR           //
    /////////////////////////////////////
    // CAPTURAR EL CLICK EN EL BOTÓN DE EDITAR
    $(document).on('click', '.editarKilometraje', function (event) {
        event.preventDefault();
        
        let id = $(this).data('id_registro_km');
        console.log("id registro km:", id);
        
        // Redirigir al formulario independiente en modo edición
        window.location.href = `formularioKilometraje.php?modo=editar&id_registro_km=${id}&id_furgoneta=${idFurgoneta}`;
    });
    ///////////////////////////////////////
    //        FIN ZONA EDITAR           //
    /////////////////////////////////////


    ////////////////////////////////////////////////////////
    //        ZONA FILTROS RADIOBUTTON CABECERA           //
    ///////////////////////////////////////////////////////
    // Escuchar cambios en los radio buttons
    // Si es necesario filtrar por texto en lugar de valores numéricos, hay que asegurarse que los valores de los radio buttons coincidan con los valores de la columna.
    $('input[name="filterTipo"]').on('change', function () {
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

    ////////////////////////////////////////////////
    //     FIN ZONA FILTRO DE LA FECHA           //
    ///////////////////////////////////////////////


    /*********************************************************** */
    /********************************************************** */
    /* A PARTIR DE AQUI NO TOCAR  SE ACTUALIZA AUTOMATICAMENTE */
    /******************************************************** */
    /******************************************************* */

    //ejemplo -- var table_e = $('#employees-table').DataTable(datatable_employeeConfig);

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
    // Manejo para elementos input (keyup) y select (change)
    $columnFilterInputs.on('keyup change', function () {
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

// Función para cargar información de la furgoneta
function cargarInfoFurgoneta(idFurgoneta) {
    $.ajax({
        url: '../../controller/furgoneta.php?op=mostrar',
        type: 'POST',
        data: { id_furgoneta: idFurgoneta },
        dataType: 'json',
        success: function (data) {
            if (data) {
                const nombreFurgoneta = data.matricula_furgoneta + ' - ' + data.marca_furgoneta + ' ' + data.modelo_furgoneta;
                $('#nombre-furgoneta').text(nombreFurgoneta);
                $('#id-furgoneta').text(data.id_furgoneta);
                document.title = 'Kilometraje - ' + nombreFurgoneta;
            }
        },
        error: function () {
            $('#nombre-furgoneta').text('Error al cargar información');
            $('#id-furgoneta').text('--');
        }
    });
}
