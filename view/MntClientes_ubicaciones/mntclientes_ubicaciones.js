$(document).ready(function () {
  // Agregar estilos CSS para mejorar la visualización
  if (!document.getElementById("ubicaciones-styles")) {
    const style = document.createElement("style");
    style.id = "ubicaciones-styles";
    style.textContent = `
            .swal-wide {
                max-width: 90% !important;
                width: auto !important;
            }
            .table-hover tbody tr:hover {
                background-color: #f8f9fa;
            }
        `;
    document.head.appendChild(style);
  }

    // Obtener el ID del cliente desde la URL
    const urlParams = new URLSearchParams(window.location.search);
    const idCliente = urlParams.get('id_cliente');

    // Cargar información del cliente
    if (idCliente) {
        cargarInfoCliente(idCliente);
    }

    /////////////////////////////////////
    // INICIO DE LA TABLA DE UBICACIONES //
    //         DATATABLES             //
    ///////////////////////////////////
    var datatable_ubicacionesConfig = {
        processing: true, // mostrar el procesamiento de la tabla
        layout: {
            bottomEnd: { // que elementos de la paginación queremos que aparezcan
                paging: {
                    firstLast: true,
                    numbers: false,
                    previousNext: true
                }
            }
        },
        language: {
            // Mensajes en español para DataTables
            emptyTable: "No hay ubicaciones registradas para este cliente",
            info: "Mostrando _START_ a _END_ de _TOTAL_ ubicaciones",
            infoEmpty: "Mostrando 0 a 0 de 0 ubicaciones",
            infoFiltered: "(filtrado de _MAX_ ubicaciones totales)",
            lengthMenu: "Mostrar _MENU_ ubicaciones por página",
            loadingRecords: "Cargando...",
            processing: "Procesando...",
            search: "Buscar:",
            zeroRecords: "No se encontraron ubicaciones que coincidan con la búsqueda",
            // Se hace para cambiar la paginación por flechas
            paginate: {
                first: '<i class="bi bi-chevron-double-left"></i>',
                last: '<i class="bi bi-chevron-double-right"></i>',
                previous: '<i class="bi bi-chevron-compact-left"></i>',
                next: '<i class="bi bi-chevron-compact-right"></i>'
            }
        },
        columns: [
            // Son los botones para más
            { name: 'control', data: null, defaultContent: '', className: 'details-control sorting_1 text-center' }, // Columna 0: Mostrar más
            { name: 'id_ubicacion', data: 'id_ubicacion', visible: false, className: "text-center" }, // Columna 1: ID
            { name: 'nombre_ubicacion', data: 'nombre_ubicacion', className: "text-center align-middle" }, // Columna 2: NOMBRE
            { name: 'direccion_ubicacion', data: 'direccion_ubicacion', className: "text-center align-middle" }, // Columna 3: DIRECCION
            { name: 'poblacion_ubicacion', data: 'poblacion_ubicacion', className: "text-center align-middle" }, // Columna 4: POBLACION
            { name: 'provincia_ubicacion', data: 'provincia_ubicacion', className: "text-center align-middle" }, // Columna 5: PROVINCIA
            { name: 'pais_ubicacion', data: 'pais_ubicacion', className: "text-center align-middle" }, // Columna 6: PAIS
            { name: 'persona_contacto_ubicacion', data: 'persona_contacto_ubicacion', className: "text-center align-middle" }, // Columna 7: CONTACTO
            { name: 'telefono_contacto_ubicacion', data: 'telefono_contacto_ubicacion', className: "text-center align-middle" }, // Columna 8: TELEFONO
            { name: 'es_principal_ubicacion', data: 'es_principal_ubicacion', className: "text-center align-middle" }, // Columna 9: PRINCIPAL
            { name: 'activo_ubicacion', data: 'activo_ubicacion', className: "text-center align-middle" }, // Columna 10: ESTADO
            { name: 'activar', data: null, className: "text-center align-middle" }, // Columna 11: ACTIVAR/DESACTIVAR
            { name: 'editar', data: null, defaultContent: '', className: "text-center align-middle" }  // Columna 12: EDITAR
        ],
        columnDefs: [
            // Columna 0: BOTÓN MÁS 
            { targets: "control:name", width: '5%', searchable: false, orderable: false, className: "text-center"},
            // Columna 1: id_ubicacion 
            { targets: "id_ubicacion:name", width: '5%', searchable: false, orderable: false, className: "text-center" },
            // Columna 2: nombre_ubicacion
            { targets: "nombre_ubicacion:name", width: '12%', searchable: true, orderable: true, className: "text-center" },
            // Columna 3: direccion_ubicacion
            { targets: "direccion_ubicacion:name", width: '15%', searchable: true, orderable: true, className: "text-center" },
            // Columna 4: poblacion_ubicacion
            { targets: "poblacion_ubicacion:name", width: '10%', searchable: true, orderable: true, className: "text-center" },
            // Columna 5: provincia_ubicacion
            { targets: "provincia_ubicacion:name", width: '10%', searchable: true, orderable: true, className: "text-center" },
            // Columna 6: pais_ubicacion
            { targets: "pais_ubicacion:name", width: '8%', searchable: true, orderable: true, className: "text-center" },
            // Columna 7: persona_contacto_ubicacion
            { targets: "persona_contacto_ubicacion:name", width: '12%', searchable: true, orderable: true, className: "text-center" },
            // Columna 8: telefono_contacto_ubicacion
            { targets: "telefono_contacto_ubicacion:name", width: '10%', searchable: true, orderable: true, className: "text-center" },
            // Columna 9: es_principal_ubicacion
            {
                targets: "es_principal_ubicacion:name", width: '8%', orderable: true, searchable: true, className: "text-center",
                render: function (data, type, row) {
                    if (type === "display") {
                        return row.es_principal_ubicacion == 1 ? 
                            '<i class="bi bi-star-fill text-warning fa-2x" title="Ubicación Principal"></i>' : 
                            '<i class="bi bi-star text-muted fa-2x" title="Ubicación Secundaria"></i>';
                    }
                    return row.es_principal_ubicacion;
                }
            },
            // Columna 10: activo_ubicacion
            {
                targets: "activo_ubicacion:name", width: '8%', orderable: true, searchable: true, className: "text-center",
                render: function (data, type, row) {
                    if (type === "display") {
                        return row.activo_ubicacion == 1 ? 
                            '<i class="bi bi-check-circle text-success fa-2x"></i>' : 
                            '<i class="bi bi-x-circle text-danger fa-2x"></i>';
                    }
                    return row.activo_ubicacion;
                }
            },
            // Columna 11: BOTON PARA ACTIVAR/DESACTIVAR ESTADO
            {   
                targets: "activar:name", width: '8%', searchable: false, orderable: false, class: "text-center",
                render: function (data, type, row) {
                    if (row.activo_ubicacion == 1) {
                        return `<button type="button" class="btn btn-danger btn-sm desacUbicacion" data-bs-toggle="tooltip-primary" data-placement="top" title="Desactivar" data-original-title="Tooltip on top" 
                             data-id_ubicacion="${row.id_ubicacion}"> 
                             <i class="fa-solid fa-trash"></i>
                             </button>`}
                    else {
                        return `<button class="btn btn-success btn-sm activarUbicacion" data-bs-toggle="tooltip-primary" data-placement="top" title="Activar" data-original-title="Tooltip on top" 
                             data-id_ubicacion="${row.id_ubicacion}"> 
                             <i class="bi bi-hand-thumbs-up-fill"></i>
                            </button>`}
                }
            },
            // Columna 12: BOTON PARA EDITAR UBICACION
            {   
                targets: "editar:name", width: '8%', searchable: false, orderable: false, class: "text-center",
                render: function (data, type, row) {
                    return `<button type="button" class="btn btn-info btn-sm editarUbicacion" data-toggle="tooltip-primary" data-placement="top" title="Editar"  
                             data-id_ubicacion="${row.id_ubicacion}"> 
                             <i class="fa-solid fa-edit"></i>
                             </button>`
                }
            }
        ],
        ajax: {
            url: '../../controller/ubicaciones.php?op=listar',
            type: 'GET',
            data: function() {
                return {
                    id_cliente: idCliente
                };
            },
            dataSrc: function (json) {
                console.log("JSON recibido desde servidor:", json);
                console.log("Número de registros:", json.data ? json.data.length : json.length);
                
                if (!json || (!json.data && !Array.isArray(json))) {
                    console.warn("No se recibieron datos válidos del servidor");
                    return [];
                }
                
                return json.data || json;
            },
            error: function(xhr, status, error) {
                console.error("Error al cargar datos:", error);
                console.error("Status:", status);
                console.error("Response:", xhr.responseText);
            }
        },
        deferRender: true,
        pageLength: 25,
        lengthMenu: [10, 25, 50, 100],
        order: [[2, 'asc']] // Ordenar por nombre por defecto
    };

    /************************************/
    //     ZONA DE DEFINICIONES        //
    /**********************************/
    var $table = $('#ubicaciones_data');
    var $tableConfig = datatable_ubicacionesConfig;
    var $tableBody = $('#ubicaciones_data tbody');
    var $columnFilterInputs = $('#ubicaciones_data tfoot input, #ubicaciones_data tfoot select');

    var table_e = $table.DataTable($tableConfig);

    function format(d) {
        console.log(d);

        return `
            <div class="card border-primary mb-3" style="overflow: visible;">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-geo-alt-fill fs-3 me-2"></i>
                        <h5 class="card-title mb-0">Detalles de la Ubicación</h5>
                    </div>
                </div>
                <div class="card-body p-0" style="overflow: visible;">
                    <div class="row">
                        <!-- Columna izquierda -->
                        <div class="col-md-6">
                            <table class="table table-borderless table-striped table-hover mb-0">
                                <tbody>
                                    <tr>
                                        <th scope="row" class="ps-4 w-40 align-top">
                                            <i class="bi bi-hash me-2"></i>ID Ubicación
                                        </th>
                                        <td class="pe-4">
                                            ${d.id_ubicacion || '<span class="text-muted fst-italic">Sin ID</span>'}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="ps-4 w-40 align-top">
                                            <i class="bi bi-pin-map me-2"></i>Nombre
                                        </th>
                                        <td class="pe-4">
                                            ${d.nombre_ubicacion || '<span class="text-muted fst-italic">Sin nombre</span>'}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="ps-4 w-40 align-top">
                                            <i class="bi bi-house me-2"></i>Dirección
                                        </th>
                                        <td class="pe-4">
                                            ${d.direccion_ubicacion || '<span class="text-muted fst-italic">Sin dirección</span>'}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="ps-4 w-40 align-top">
                                            <i class="bi bi-mailbox me-2"></i>Código Postal
                                        </th>
                                        <td class="pe-4">
                                            ${d.codigo_postal_ubicacion || '<span class="text-muted fst-italic">Sin C.P.</span>'}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="ps-4 w-40 align-top">
                                            <i class="bi bi-building me-2"></i>Población
                                        </th>
                                        <td class="pe-4">
                                            ${d.poblacion_ubicacion || '<span class="text-muted fst-italic">Sin población</span>'}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="ps-4 w-40 align-top">
                                            <i class="bi bi-map me-2"></i>Provincia
                                        </th>
                                        <td class="pe-4">
                                            ${d.provincia_ubicacion || '<span class="text-muted fst-italic">Sin provincia</span>'}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Columna derecha -->
                        <div class="col-md-6">
                            <table class="table table-borderless table-striped table-hover mb-0">
                                <tbody>
                                    <tr>
                                        <th scope="row" class="ps-4 w-40 align-top">
                                            <i class="bi bi-globe me-2"></i>País
                                        </th>
                                        <td class="pe-4">
                                            ${d.pais_ubicacion || '<span class="text-muted fst-italic">Sin país</span>'}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="ps-4 w-40 align-top">
                                            <i class="bi bi-person me-2"></i>Persona Contacto
                                        </th>
                                        <td class="pe-4">
                                            ${d.persona_contacto_ubicacion || '<span class="text-muted fst-italic">Sin contacto</span>'}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="ps-4 w-40 align-top">
                                            <i class="bi bi-telephone me-2"></i>Teléfono
                                        </th>
                                        <td class="pe-4">
                                            ${d.telefono_contacto_ubicacion || '<span class="text-muted fst-italic">Sin teléfono</span>'}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="ps-4 w-40 align-top">
                                            <i class="bi bi-envelope me-2"></i>Email
                                        </th>
                                        <td class="pe-4">
                                            ${d.email_contacto_ubicacion ? `<a href="mailto:${d.email_contacto_ubicacion}" target="_blank">${d.email_contacto_ubicacion}</a>` : '<span class="text-muted fst-italic">Sin email</span>'}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="ps-4 w-40 align-top">
                                            <i class="bi bi-star me-2"></i>Principal
                                        </th>
                                        <td class="pe-4">
                                            ${d.es_principal_ubicacion == 1 ? '<span class="badge bg-warning text-dark">Ubicación Principal</span>' : '<span class="badge bg-secondary">Ubicación Secundaria</span>'}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="ps-4 w-40 align-top">
                                            <i class="bi bi-chat-text me-2"></i>Observaciones
                                        </th>
                                        <td class="pe-4" style="max-width: 300px; word-wrap: break-word; white-space: pre-wrap; overflow-wrap: break-word;">
                                            ${d.observaciones_ubicacion || '<span class="text-muted fst-italic">Sin observaciones</span>'}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-top-0 text-end">
                    <small class="text-muted">Creado: ${d.created_at_ubicacion ? formatoFechaEuropeo(d.created_at_ubicacion) : 'Sin fecha'} | Actualizado: ${d.updated_at_ubicacion ? formatoFechaEuropeo(d.updated_at_ubicacion) : 'Sin fecha'}</small>
                </div>
            </div>
        `;
    }
    
    // Control de expansión de filas
    $tableBody.on('click', 'td.details-control', function () {
        var tr = $(this).closest('tr');
        var row = table_e.row(tr);

        if (row.child.isShown()) {
            row.child.hide();
            tr.removeClass('shown');
        } else {
            row.child(format(row.data())).show();
            tr.addClass('shown');
        }
    });

    ////////////////////////////////////////////
    //   INICIO ZONA FUNCIONES DE APOYO      //
    //////////////////////////////////////////

    // Función para cargar información del cliente
    function cargarInfoCliente(id_cliente) {
        $.post("../../controller/cliente.php?op=mostrar", { id_cliente: id_cliente }, function (data) {
            if (data) {
                $('#nombre-cliente').text(data.nombre_cliente || 'Sin nombre');
                $('#id-cliente').text(id_cliente);
            }
        }, 'json').fail(function() {
            $('#nombre-cliente').text('Error al cargar');
        });
    }

    /////////////////////////////////////
    //   INICIO ZONA DELETE UBICACION   //
    ///////////////////////////////////
    function desacUbicacion(id) {
        Swal.fire({
            title: 'Desactivar',
            html: `¿Desea desactivar la ubicación con ID ${id}?<br><br><small class="text-warning"><i class="bi bi-exclamation-triangle me-1"></i>La ubicación quedará marcada como inactiva</small>`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Si',
            cancelButtonText: 'No',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                $.post("../../controller/ubicaciones.php?op=eliminar", { id_ubicacion: id }, function (data) {
                    $table.DataTable().ajax.reload();
                    Swal.fire(
                        'Desactivado',
                        'La ubicación ha sido desactivada',
                        'success'
                    )
                });
            }
        })
    }

    // CAPTURAR EL CLICK EN EL BOTÓN DE BORRAR
    $(document).on('click', '.desacUbicacion', function (event) {
        event.preventDefault();
        let id = $(this).data('id_ubicacion');
        desacUbicacion(id);
    });

    ///////////////////////////////////////
    //   INICIO ZONA ACTIVAR UBICACION    //
    /////////////////////////////////////
    function activarUbicacion(id) {
        Swal.fire({
            title: 'Activar',
            text: `¿Desea activar la ubicación con ID ${id}?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Si',
            cancelButtonText: 'No',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                $.post("../../controller/ubicaciones.php?op=activar", { id_ubicacion: id }, function (data) {
                    $table.DataTable().ajax.reload();
                    Swal.fire(
                        'Activado',
                        'La ubicación ha sido activada',
                        'success'
                    )
                });
            }
        })
    }

    // CAPTURAR EL CLICK EN EL BOTÓN DE ACTIVAR
    $(document).on('click', '.activarUbicacion', function (event) {
        event.preventDefault();
        let id = $(this).data('id_ubicacion');
        console.log("id ubicacion:",id);
        activarUbicacion(id);
    });

    ///////////////////////////////////////
    //      INICIO ZONA EDITAR           //
    //        BOTON DE EDITAR           //
    /////////////////////////////////////
    // CAPTURAR EL CLICK EN EL BOTÓN DE EDITAR
    $(document).on('click', '.editarUbicacion', function (event) {
        event.preventDefault();

        let id = $(this).data('id_ubicacion');
        console.log("id ubicacion:", id);

        // Redirigir al formulario independiente en modo edición
        window.location.href = `formularioUbicacion.php?modo=editar&id=${id}&id_cliente=${idCliente}`;
    });

    /*********************************************************** */
    /********************************************************** */
    /* A PARTIR DE AQUI NO TOCAR  SE ACTUALIZA AUTOMATICAMENTE */
    /******************************************************** */
    /******************************************************* */

    /////////////////////////////////////
    //  INICIO ZONA FILTROS PIES y SEARCH     //
    //    NO ES NECESARIO TOCAR              //
    //     FUNCIONES NO TOCAR               // 
    ///////////////////////////////////////////

    // Filtro de cada columna en el pie de la tabla (tfoot)
    $columnFilterInputs.on('keyup change', function () {
        var columnIndex = table_e.column($(this).closest('th')).index();
        var searchValue = $(this).val();

        table_e.column(columnIndex).search(searchValue).draw();
        updateFilterMessage();
    });

    // Función para actualizar el mensaje de filtro activo
    function updateFilterMessage() {
        var activeFilters = false;

        $columnFilterInputs.each(function () {
            if ($(this).val() !== "") {
                activeFilters = true;
                return false;
            }
        });

        if (table_e.search() !== "") {
            activeFilters = true;
        }

        if (activeFilters) {
            $('#filter-alert').show();
        } else {
            $('#filter-alert').hide();
        }
    }

    table_e.on('search.dt', function () {
        updateFilterMessage();
    });

    // Botón para limpiar los filtros
    $('#clear-filter').on('click', function () {
        table_e.destroy();

        $columnFilterInputs.each(function () {
            $(this).val('');
        });

        table_e = $table.DataTable($tableConfig);
        $('#filter-alert').hide();
    });

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
        const horas = fecha.getHours().toString().padStart(2, '0');
        const minutos = fecha.getMinutes().toString().padStart(2, '0');
        
        return `${dia}/${mes}/${año} ${horas}:${minutos}`;
    } catch (error) {
        console.error('Error al formatear fecha:', error);
        return 'Error en fecha';
    }
}
