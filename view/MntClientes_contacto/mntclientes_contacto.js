$(document).ready(function () {
  // Agregar estilos CSS para mejorar la visualización
  if (!document.getElementById("contactos-styles")) {
    const style = document.createElement("style");
    style.id = "contactos-styles";
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
    // INICIO DE LA TABLA DE CONTACTOS //
    //         DATATABLES             //
    ///////////////////////////////////
    var datatable_contactosConfig = {
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
            emptyTable: "No hay contactos registrados para este cliente",
            info: "Mostrando _START_ a _END_ de _TOTAL_ contactos",
            infoEmpty: "Mostrando 0 a 0 de 0 contactos",
            infoFiltered: "(filtrado de _MAX_ contactos totales)",
            lengthMenu: "Mostrar _MENU_ contactos por página",
            loadingRecords: "Cargando...",
            processing: "Procesando...",
            search: "Buscar:",
            zeroRecords: "No se encontraron contactos que coincidan con la búsqueda",
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
            { name: 'id_contacto_cliente', data: 'id_contacto_cliente', visible: false, className: "text-center" }, // Columna 1: ID
            { name: 'nombre_contacto_cliente', data: 'nombre_contacto_cliente', className: "text-center" }, // Columna 2: NOMBRE
            { name: 'apellidos_contacto_cliente', data: 'apellidos_contacto_cliente', className: "text-center" }, // Columna 3: APELLIDOS
            { name: 'cargo_contacto_cliente', data: 'cargo_contacto_cliente', className: "text-center" }, // Columna 4: CARGO
            { name: 'departamento_contacto_cliente', data: 'departamento_contacto_cliente', className: "text-center" }, // Columna 5: DEPARTAMENTO
            { name: 'telefono_contacto_cliente', data: 'telefono_contacto_cliente', className: "text-center" }, // Columna 6: TELEFONO
            { name: 'movil_contacto_cliente', data: 'movil_contacto_cliente', className: "text-center" }, // Columna 7: MOVIL
            { name: 'email_contacto_cliente', data: 'email_contacto_cliente', className: "text-center" }, // Columna 8: EMAIL
            { name: 'principal_contacto_cliente', data: 'principal_contacto_cliente', className: "text-center" }, // Columna 9: PRINCIPAL
            { name: 'activo_contacto_cliente', data: 'activo_contacto_cliente', className: "text-center" }, // Columna 10: ESTADO
            { name: 'activar', data: null, className: "text-center" }, // Columna 11: ACTIVAR/DESACTIVAR
            { name: 'editar', data: null, defaultContent: '', className: "text-center" }  // Columna 12: EDITAR
        ],
        columnDefs: [
            // Columna 0: BOTÓN MÁS 
            { targets: "control:name", width: '5%', searchable: false, orderable: false, className: "text-center"},
            // Columna 1: id_contacto_cliente 
            { targets: "id_contacto_cliente:name", width: '5%', searchable: false, orderable: false, className: "text-center" },
            // Columna 2: nombre_contacto_cliente
            { targets: "nombre_contacto_cliente:name", width: '12%', searchable: true, orderable: true, className: "text-center" },
            // Columna 3: apellidos_contacto_cliente
            { targets: "apellidos_contacto_cliente:name", width: '12%', searchable: true, orderable: true, className: "text-center" },
            // Columna 4: cargo_contacto_cliente
            { targets: "cargo_contacto_cliente:name", width: '10%', searchable: true, orderable: true, className: "text-center" },
            // Columna 5: departamento_contacto_cliente
            { targets: "departamento_contacto_cliente:name", width: '10%', searchable: true, orderable: true, className: "text-center" },
            // Columna 6: telefono_contacto_cliente
            { targets: "telefono_contacto_cliente:name", width: '10%', searchable: true, orderable: true, className: "text-center" },
            // Columna 7: movil_contacto_cliente
            { targets: "movil_contacto_cliente:name", width: '10%', searchable: true, orderable: true, className: "text-center" },
            // Columna 8: email_contacto_cliente
            { targets: "email_contacto_cliente:name", width: '12%', searchable: true, orderable: true, className: "text-center" },
            // Columna 9: principal_contacto_cliente
            {
                targets: "principal_contacto_cliente:name", width: '8%', orderable: true, searchable: true, className: "text-center",
                render: function (data, type, row) {
                    if (type === "display") {
                        return row.principal_contacto_cliente == 1 ? 
                            '<i class="bi bi-star-fill text-warning fa-2x" title="Contacto Principal"></i>' : 
                            '<i class="bi bi-star text-muted fa-2x" title="Contacto Secundario"></i>';
                    }
                    return row.principal_contacto_cliente;
                }
            },
            // Columna 10: activo_contacto_cliente
            {
                targets: "activo_contacto_cliente:name", width: '8%', orderable: true, searchable: true, className: "text-center",
                render: function (data, type, row) {
                    if (type === "display") {
                        return row.activo_contacto_cliente == 1 ? 
                            '<i class="bi bi-check-circle text-success fa-2x"></i>' : 
                            '<i class="bi bi-x-circle text-danger fa-2x"></i>';
                    }
                    return row.activo_contacto_cliente;
                }
            },
            // Columna 11: BOTON PARA ACTIVAR/DESACTIVAR ESTADO
            {   
                targets: "activar:name", width: '8%', searchable: false, orderable: false, class: "text-center",
                render: function (data, type, row) {
                    if (row.activo_contacto_cliente == 1) {
                        return `<button type="button" class="btn btn-danger btn-sm desacContacto" data-bs-toggle="tooltip-primary" data-placement="top" title="Desactivar" data-original-title="Tooltip on top" 
                             data-id_contacto_cliente="${row.id_contacto_cliente}"> 
                             <i class="fa-solid fa-trash"></i>
                             </button>`}
                    else {
                        return `<button class="btn btn-success btn-sm activarContacto" data-bs-toggle="tooltip-primary" data-placement="top" title="Activar" data-original-title="Tooltip on top" 
                             data-id_contacto_cliente="${row.id_contacto_cliente}"> 
                             <i class="bi bi-hand-thumbs-up-fill"></i>
                            </button>`}
                }
            },
            // Columna 12: BOTON PARA EDITAR CONTACTO
            {   
                targets: "editar:name", width: '8%', searchable: false, orderable: false, class: "text-center",
                render: function (data, type, row) {
                    return `<button type="button" class="btn btn-info btn-sm editarContacto" data-toggle="tooltip-primary" data-placement="top" title="Editar"  
                             data-id_contacto_cliente="${row.id_contacto_cliente}"> 
                             <i class="fa-solid fa-edit"></i>
                             </button>`
                }
            }
        ],
        ajax: {
            url: '../../controller/clientes_contacto.php?op=listar',
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
    var $table = $('#contactos_data');
    var $tableConfig = datatable_contactosConfig;
    var $tableBody = $('#contactos_data tbody');
    var $columnFilterInputs = $('#contactos_data tfoot input, #contactos_data tfoot select');

    var table_e = $table.DataTable($tableConfig);

    function format(d) {
        console.log(d);

        return `
            <div class="card border-primary mb-3" style="overflow: visible;">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-person-fill fs-3 me-2"></i>
                        <h5 class="card-title mb-0">Detalles del Contacto</h5>
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
                                            <i class="bi bi-hash me-2"></i>ID Contacto
                                        </th>
                                        <td class="pe-4">
                                            ${d.id_contacto_cliente || '<span class="text-muted fst-italic">Sin ID</span>'}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="ps-4 w-40 align-top">
                                            <i class="bi bi-person me-2"></i>Nombre completo
                                        </th>
                                        <td class="pe-4">
                                            ${(d.nombre_contacto_cliente + ' ' + (d.apellidos_contacto_cliente || '')).trim() || '<span class="text-muted fst-italic">Sin nombre</span>'}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="ps-4 w-40 align-top">
                                            <i class="bi bi-briefcase me-2"></i>Cargo
                                        </th>
                                        <td class="pe-4">
                                            ${d.cargo_contacto_cliente || '<span class="text-muted fst-italic">Sin cargo</span>'}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="ps-4 w-40 align-top">
                                            <i class="bi bi-building me-2"></i>Departamento
                                        </th>
                                        <td class="pe-4">
                                            ${d.departamento_contacto_cliente || '<span class="text-muted fst-italic">Sin departamento</span>'}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="ps-4 w-40 align-top">
                                            <i class="bi bi-telephone me-2"></i>Teléfono
                                        </th>
                                        <td class="pe-4">
                                            ${d.telefono_contacto_cliente || '<span class="text-muted fst-italic">Sin teléfono</span>'}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="ps-4 w-40 align-top">
                                            <i class="bi bi-phone me-2"></i>Móvil
                                        </th>
                                        <td class="pe-4">
                                            ${d.movil_contacto_cliente || '<span class="text-muted fst-italic">Sin móvil</span>'}
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
                                            <i class="bi bi-envelope me-2"></i>Email
                                        </th>
                                        <td class="pe-4">
                                            ${d.email_contacto_cliente ? `<a href="mailto:${d.email_contacto_cliente}" target="_blank">${d.email_contacto_cliente}</a>` : '<span class="text-muted fst-italic">Sin email</span>'}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="ps-4 w-40 align-top">
                                            <i class="bi bi-telephone-plus me-2"></i>Extensión
                                        </th>
                                        <td class="pe-4">
                                            ${d.extension_contacto_cliente || '<span class="text-muted fst-italic">Sin extensión</span>'}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="ps-4 w-40 align-top">
                                            <i class="bi bi-star me-2"></i>Principal
                                        </th>
                                        <td class="pe-4">
                                            ${d.principal_contacto_cliente == 1 ? '<span class="badge bg-warning text-dark">Contacto Principal</span>' : '<span class="badge bg-secondary">Contacto Secundario</span>'}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="ps-4 w-40 align-top">
                                            <i class="bi bi-chat-text me-2"></i>Observaciones
                                        </th>
                                        <td class="pe-4" style="max-width: 300px; word-wrap: break-word; white-space: pre-wrap; overflow-wrap: break-word;">
                                            ${d.observaciones_contacto_cliente || '<span class="text-muted fst-italic">Sin observaciones</span>'}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="ps-4 w-40 align-top">
                                            <i class="bi bi-calendar-plus me-2"></i>Creado el:
                                        </th>
                                        <td class="pe-4">
                                            ${d.created_at_contacto_cliente ? formatoFechaEuropeo(d.created_at_contacto_cliente) : '<span class="text-muted fst-italic">Sin fecha</span>'}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-top-0 text-end">
                    <small class="text-muted">Actualizado: ${d.updated_at_contacto_cliente ? formatoFechaEuropeo(d.updated_at_contacto_cliente) : 'Sin fecha de actualización'}</small>
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
    //   INICIO ZONA DELETE CONTACTO   //
    ///////////////////////////////////
    function desacContacto(id) {
        Swal.fire({
            title: 'Desactivar',
            html: `¿Desea desactivar el contacto con ID ${id}?<br><br><small class="text-warning"><i class="bi bi-exclamation-triangle me-1"></i>El contacto quedará marcado como inactivo</small>`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Si',
            cancelButtonText: 'No',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                $.post("../../controller/clientes_contacto.php?op=eliminar", { id_contacto_cliente: id }, function (data) {
                    $table.DataTable().ajax.reload();
                    Swal.fire(
                        'Desactivado',
                        'El contacto ha sido desactivado',
                        'success'
                    )
                });
            }
        })
    }

    // CAPTURAR EL CLICK EN EL BOTÓN DE BORRAR
    $(document).on('click', '.desacContacto', function (event) {
        event.preventDefault();
        let id = $(this).data('id_contacto_cliente');
        desacContacto(id);
    });

    ///////////////////////////////////////
    //   INICIO ZONA ACTIVAR CONTACTO    //
    /////////////////////////////////////
    function activarContacto(id) {
        Swal.fire({
            title: 'Activar',
            text: `¿Desea activar el contacto con ID ${id}?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Si',
            cancelButtonText: 'No',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                $.post("../../controller/clientes_contacto.php?op=activar", { id_contacto_cliente: id }, function (data) {
                    $table.DataTable().ajax.reload();
                    Swal.fire(
                        'Activado',
                        'El contacto ha sido activado',
                        'success'
                    )
                });
            }
        })
    }

    // CAPTURAR EL CLICK EN EL BOTÓN DE ACTIVAR
    $(document).on('click', '.activarContacto', function (event) {
        event.preventDefault();
        let id = $(this).data('id_contacto_cliente');
        console.log("id contacto:",id);
        activarContacto(id);
    });

    ///////////////////////////////////////
    //      INICIO ZONA EDITAR           //
    //        BOTON DE EDITAR           //
    /////////////////////////////////////
    // CAPTURAR EL CLICK EN EL BOTÓN DE EDITAR
    $(document).on('click', '.editarContacto', function (event) {
        event.preventDefault();

        let id = $(this).data('id_contacto_cliente');
        console.log("id contacto:", id);

        // Redirigir al formulario independiente en modo edición
        window.location.href = `formularioContacto.php?modo=editar&id=${id}&id_cliente=${idCliente}`;
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
