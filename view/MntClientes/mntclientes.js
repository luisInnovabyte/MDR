$(document).ready(function () {
  // Agregar estilos CSS para mejorar la visualización
  if (!document.getElementById("cliente-styles")) {
    const style = document.createElement("style");
    style.id = "cliente-styles";
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
    // FormValidator removido - ahora se maneja en formularioCliente.js
    /////////////////////////////////////////
    //     FIN FORMATEO DE CAMPOS          //
    ////////////////////////////////////////


    /////////////////////////////////////
    // INICIO DE LA TABLA DE CLIENTES //
    //         DATATABLES             //
    ///////////////////////////////////
    var datatable_clientesConfig = {
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
            // Mensajes en español para DataTables
            emptyTable: "No hay clientes registrados",
            info: "Mostrando _START_ a _END_ de _TOTAL_ clientes",
            infoEmpty: "Mostrando 0 a 0 de 0 clientes",
            infoFiltered: "(filtrado de _MAX_ clientes totales)",
            lengthMenu: "Mostrar _MENU_ clientes por página",
            loadingRecords: "Cargando...",
            processing: "Procesando...",
            search: "Buscar:",
            zeroRecords: "No se encontraron clientes que coincidan con la búsqueda",
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
            { name: 'id_cliente', data: 'id_cliente', visible: false, className: "text-center" }, // Columna 1: ID_CLIENTE
            { name: 'codigo_cliente', data: 'codigo_cliente' , className: "text-center align-middle" }, // Columna 2: CODIGO_CLIENTE
            { name: 'nombre_cliente', data: 'nombre_cliente', className: "text-center align-middle"  }, // Columna 3: NOMBRE_CLIENTE
            { name: 'nif_cliente', data: 'nif_cliente', className: "text-center align-middle"  }, // Columna 4: NIF_CLIENTE
            { name: 'telefono_cliente', data: 'telefono_cliente', className: "text-center align-middle"  }, // Columna 5: TELEFONO_CLIENTE
            { name: 'porcentaje_descuento_cliente', data: 'porcentaje_descuento_cliente', className: "text-center align-middle"  }, // Columna 6: DESCUENTO
            { name: 'cantidad_contactos', data: 'cantidad_contactos', className: "text-center align-middle"  }, // Columna 8: CANTIDAD_CONTACTOS
            { name: 'activo_cliente', data: 'activo_cliente', className: "text-center align-middle"  }, // Columna 9: ESTADO
            { name: 'acciones', data: null, defaultContent: '', className: "text-center align-middle" }, // Columna 10: ACCIONES
        ], // de las columnas
        columnDefs: [
            // Cuidado que el ordrData puede interferir con el ordenamiento de la tabla    
           
            // Columna 0: BOTÓN MÁS 
            { targets: "control:name", width: '5%', searchable: false, orderable: false, className: "text-center"},
            // Columna 1: id_cliente 
            { targets: "id_cliente:name", width: '5%', searchable: false, orderable: false, className: "text-center" },
            // Columna 2: codigo_cliente
            { targets: "codigo_cliente:name", width: '12%', searchable: true, orderable: true, className: "text-center" },
            // Columna 3: nombre_cliente
            { targets: "nombre_cliente:name", width: '20%', searchable: true, orderable: true, className: "text-center" },
            // Columna 4: nif_cliente
            { targets: "nif_cliente:name", width: '10%', searchable: true, orderable: true, className: "text-center" },
            // Columna 5: telefono_cliente
            { targets: "telefono_cliente:name", width: '12%', searchable: true, orderable: true, className: "text-center" },
            // Columna 6: porcentaje_descuento_cliente
            {
                targets: "porcentaje_descuento_cliente:name", width: '8%', orderable: true, searchable: false, className: "text-center",
                render: function (data, type, row) {
                    if (type === "display") {
                        var descuento = parseFloat(data) || 0;
                        var badgeClass = 'bg-secondary';
                        var icon = 'bi-dash-circle';
                        
                        if (descuento > 0) {
                            if (descuento <= 5) {
                                badgeClass = 'bg-info';
                                icon = 'bi-percent';
                            } else if (descuento <= 15) {
                                badgeClass = 'bg-warning';
                                icon = 'bi-percent';
                            } else {
                                badgeClass = 'bg-success';
                                icon = 'bi-percent';
                            }
                        }
                        
                        return `<span class="badge ${badgeClass} fs-6">
                                    <i class="bi ${icon} me-1"></i>${descuento.toFixed(2)}%
                                </span>`;
                    }
                    return parseFloat(data) || 0;
                }
            },
             // Columna 8: cantidad_contactos
            {
                targets: "cantidad_contactos:name", width: '8%', orderable: true, searchable: false, className: "text-center",
                render: function (data, type, row) {
                    if (type === "display") {
                        var cantidad = parseInt(data) || 0;
                        var badgeClass = cantidad > 0 ? 'bg-success' : 'bg-secondary';
                        return `<span class="badge ${badgeClass} fs-6">
                                    <i class="bi bi-people-fill me-1"></i>${cantidad}
                                </span>`;
                    }
                    return parseInt(data) || 0;
                }
            },
            // Columna 9: activo_cliente
            {
                targets: "activo_cliente:name", width: '8%', orderable: true, searchable: true, className: "text-center",
                render: function (data, type, row) {
                    if (type === "display") {
                        return row.activo_cliente == 1 ? '<i class="bi bi-check-circle text-success fa-2x"></i>' : '<i class="bi bi-x-circle text-danger fa-2x"></i>';
                    }
                    return row.activo_cliente;
                }
            },
            // Columna 10: DROPDOWN DE ACCIONES
            {
                targets: "acciones:name", width: '8%', searchable: false, orderable: false, className: "text-center",
                render: function (data, type, row) {
                    // Label dinámico Activar / Desactivar
                    let itemActivarDesactivar = '';
                    if (row.activo_cliente == 1) {
                        itemActivarDesactivar = `
                            <li>
                                <a class="dropdown-item text-danger desacCliente" href="#"
                                   data-id_cliente="${row.id_cliente}">
                                    <i class="fa-solid fa-ban me-2"></i>Desactivar
                                </a>
                            </li>`;
                    } else {
                        itemActivarDesactivar = `
                            <li>
                                <a class="dropdown-item text-success activarCliente" href="#"
                                   data-id_cliente="${row.id_cliente}">
                                    <i class="bi bi-hand-thumbs-up-fill me-2"></i>Activar
                                </a>
                            </li>`;
                    }

                    return `
                        <div class="dropdown">
                            <button class="btn btn-sm btn-secondary dropdown-toggle"
                                    type="button"
                                    data-bs-toggle="dropdown"
                                    aria-expanded="false">
                                <i class="fa-solid fa-ellipsis-vertical"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow">
                                <!-- Editar -->
                                <li>
                                    <a class="dropdown-item editarCliente" href="#"
                                       data-id_cliente="${row.id_cliente}">
                                        <i class="fa-solid fa-pen-to-square me-2"></i>Editar
                                    </a>
                                </li>
                                <!-- Perfil completo 360° -->
                                <li>
                                    <a class="dropdown-item"
                                       href="../ClientePanel/index.php?id_cliente=${row.id_cliente}">
                                        <i class="bi bi-person-lines-fill me-2"></i>Perfil completo
                                    </a>
                                </li>
                                <!-- Contactos -->
                                <li>
                                    <a class="dropdown-item formularioCliente" href="#"
                                       data-id_cliente="${row.id_cliente}">
                                        <i class="fas fa-users me-2"></i>Contactos
                                    </a>
                                </li>
                                <!-- Ubicaciones -->
                                <li>
                                    <a class="dropdown-item"
                                       href="../MntClientes_ubicaciones/index.php?id_cliente=${row.id_cliente}">
                                        <i class="bi bi-geo-alt-fill me-2"></i>Ubicaciones
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <!-- Activar / Desactivar (dinámico) -->
                                ${itemActivarDesactivar}
                            </ul>
                        </div>`;
                } // de la function
            }
            // De la columna 10 (acciones)
        ], // de la columnDefs
        ajax: {
            url: '../../controller/cliente.php?op=listar',
            type: 'GET',
            dataSrc: function (json) {
                console.log("JSON recibido desde servidor:", json); // 📌 Ver qué estructura tiene
                console.log("Número de registros:", json.data ? json.data.length : json.length);
                
                // Si no hay datos, devolver array vacío
                if (!json || (!json.data && !Array.isArray(json))) {
                    console.warn("No se recibieron datos válidos del servidor");
                    return [];
                }
                
                return json.data || json; // Ajusta en función de lo recibido
            },
            error: function(xhr, status, error) {
                console.error("Error al cargar datos:", error);
                console.error("Status:", status);
                console.error("Response:", xhr.responseText);
            }
        }, // del ajax
        // Configuraciones adicionales para debug
        deferRender: true, // Renderizar solo filas visibles para mejor rendimiento
        pageLength: 25, // Número de filas por página
        lengthMenu: [10, 25, 50, 100], // Opciones de paginación
        order: [[2, 'asc']], // Ordenar por código de cliente por defecto
    }; // de la variable datatable_clientesConfig
    ////////////////////////////
    // FIN DE LA TABLA DE CLIENTES //
    ///////////////////////////


    /************************************/
    //     ZONA DE DEFINICIONES        //
    /**********************************/
    // Definición inicial de la tabla de clientes
    var $table = $('#clientes_data');  /*<--- Es el nombre que le hemos dado a la tabla en HTML */
    var $tableConfig = datatable_clientesConfig; /*<--- Es el nombre que le hemos dado a la declaración de la definicion de la tabla */
    //var $columSearch = 3; /* <-- Es la columna en la cual al hacer click el valor se colocará en la zona de search y se buscará */
    var $tableBody = $('#clientes_data tbody'); /*<--- Es el nombre que le hemos dado al cuerpo de la tabla en HTML */
    /* en el tableBody solo cambiar el nombre de la tabla que encontraremos en HTML*/
    var $columnFilterInputs = $('#clientes_data tfoot input, #clientes_data tfoot select'); /*<--- Selecciona tanto inputs como selects de los pies de la tabla en HTML */
    /* en el $columnFilterInputs solo cambiar el nombre de la tabla que encontraremos en HTML*/

    //ejemplo -- var table_e = $('#clientes-table').DataTable(datatable_clientesConfig);
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
                        <i class="bi bi-person-fill fs-3 me-2"></i>
                        <h5 class="card-title mb-0">Detalles del Cliente</h5>
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
                                            <i class="bi bi-hash me-2"></i>ID Cliente
                                        </th>
                                        <td class="pe-4">
                                            ${d.id_cliente || '<span class="text-muted fst-italic">Sin ID</span>'}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="ps-4 w-40 align-top">
                                            <i class="bi bi-upc me-2"></i>Código
                                        </th>
                                        <td class="pe-4">
                                            ${d.codigo_cliente || '<span class="text-muted fst-italic">Sin código</span>'}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="ps-4 w-40 align-top">
                                            <i class="bi bi-person me-2"></i>Nombre
                                        </th>
                                        <td class="pe-4">
                                            ${d.nombre_cliente || '<span class="text-muted fst-italic">Sin nombre</span>'}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="ps-4 w-40 align-top">
                                            <i class="bi bi-card-text me-2"></i>NIF/CIF
                                        </th>
                                        <td class="pe-4">
                                            ${d.nif_cliente || '<span class="text-muted fst-italic">Sin NIF/CIF</span>'}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="ps-4 w-40 align-top">
                                            <i class="bi bi-geo-alt me-2"></i>Dirección
                                        </th>
                                        <td class="pe-4">
                                            ${d.direccion_cliente || '<span class="text-muted fst-italic">Sin dirección</span>'}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="ps-4 w-40 align-top">
                                            <i class="bi bi-mailbox me-2"></i>CP/Población
                                        </th>
                                        <td class="pe-4">
                                            ${(d.cp_cliente && d.poblacion_cliente) ? `${d.cp_cliente} - ${d.poblacion_cliente}` : '<span class="text-muted fst-italic">Sin CP/Población</span>'}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="ps-4 w-40 align-top">
                                            <i class="bi bi-map me-2"></i>Provincia
                                        </th>
                                        <td class="pe-4">
                                            ${d.provincia_cliente || '<span class="text-muted fst-italic">Sin provincia</span>'}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="ps-4 w-40 align-top">
                                            <i class="bi bi-telephone me-2"></i>Teléfono
                                        </th>
                                        <td class="pe-4">
                                            ${d.telefono_cliente || '<span class="text-muted fst-italic">Sin teléfono</span>'}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="ps-4 w-40 align-top">
                                            <i class="bi bi-envelope me-2"></i>Email
                                        </th>
                                        <td class="pe-4">
                                            ${d.email_cliente ? `<a href="mailto:${d.email_cliente}" target="_blank">${d.email_cliente}</a>` : '<span class="text-muted fst-italic">Sin email</span>'}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="ps-4 w-40 align-top">
                                            <i class="bi bi-globe me-2"></i>Web
                                        </th>
                                        <td class="pe-4">
                                            ${d.web_cliente ? `<a href="${d.web_cliente}" target="_blank" rel="noopener">${d.web_cliente}</a>` : '<span class="text-muted fst-italic">Sin web</span>'}
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
                                            <i class="bi bi-receipt me-2"></i>Facturación
                                        </th>
                                        <td class="pe-4">
                                            ${d.nombre_facturacion_cliente || '<span class="text-muted fst-italic">Sin nombre facturación</span>'}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="ps-4 w-40 align-top">
                                            <i class="bi bi-geo-alt-fill me-2"></i>Dir. Facturación
                                        </th>
                                        <td class="pe-4">
                                            ${d.direccion_facturacion_cliente || '<span class="text-muted fst-italic">Sin dirección facturación</span>'}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="ps-4 w-40 align-top">
                                            <i class="bi bi-mailbox2 me-2"></i>CP/Pobl. Facturación
                                        </th>
                                        <td class="pe-4">
                                            ${(d.cp_facturacion_cliente && d.poblacion_facturacion_cliente) ? `${d.cp_facturacion_cliente} - ${d.poblacion_facturacion_cliente}` : '<span class="text-muted fst-italic">Sin CP/Población facturación</span>'}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="ps-4 w-40 align-top">
                                            <i class="bi bi-map-fill me-2"></i>Prov. Facturación
                                        </th>
                                        <td class="pe-4">
                                            ${d.provincia_facturacion_cliente || '<span class="text-muted fst-italic">Sin provincia facturación</span>'}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="ps-4 w-40 align-top">
                                            <i class="bi bi-printer me-2"></i>Fax
                                        </th>
                                        <td class="pe-4">
                                            ${d.fax_cliente || '<span class="text-muted fst-italic">Sin fax</span>'}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="ps-4 w-40 align-top">
                                            <i class="bi bi-chat-text me-2"></i>Observaciones
                                        </th>
                                        <td class="pe-4" style="max-width: 300px; word-wrap: break-word; white-space: pre-wrap; overflow-wrap: break-word;">
                                            ${d.observaciones_cliente || '<span class="text-muted fst-italic">Sin observaciones</span>'}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="ps-4 w-40 align-top">
                                            <i class="bi bi-calendar-plus me-2"></i>Creado el:
                                        </th>
                                        <td class="pe-4">
                                            ${d.created_at_cliente ? formatoFechaEuropeo(d.created_at_cliente) : '<span class="text-muted fst-italic">Sin fecha</span>'}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="ps-4 w-40 align-top">
                                            <i class="bi bi-credit-card me-2"></i>Forma de Pago
                                        </th>
                                        <td class="pe-4">
                                            ${d.descripcion_forma_pago_cliente || '<span class="text-muted fst-italic">Sin forma de pago</span>'}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="ps-4 w-40 align-top">
                                            <i class="bi bi-tag me-2"></i>Tipo de Pago
                                        </th>
                                        <td class="pe-4">
                                            ${d.tipo_pago_cliente ? `<span class="badge ${d.tipo_pago_cliente === 'Pago único' ? 'bg-info' : d.tipo_pago_cliente === 'Pago fraccionado' ? 'bg-warning' : 'bg-secondary'}">${d.tipo_pago_cliente}</span>` : '<span class="text-muted fst-italic">Sin información</span>'}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="ps-4 w-40 align-top">
                                            <i class="bi bi-percent me-2"></i>Descuento
                                        </th>
                                        <td class="pe-4">
                                            ${d.descuento_pago ? `${d.descuento_pago}%` : '<span class="text-muted fst-italic">Sin descuento</span>'}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="ps-4 w-40 align-top">
                                            <i class="bi bi-check-circle me-2"></i>Estado Forma Pago
                                        </th>
                                        <td class="pe-4">
                                            ${d.estado_forma_pago_cliente ? `<span class="badge ${d.estado_forma_pago_cliente === 'Configurado' ? 'bg-success' : d.estado_forma_pago_cliente === 'Sin configurar' ? 'bg-secondary' : 'bg-danger'}">${d.estado_forma_pago_cliente}</span>` : '<span class="text-muted fst-italic">Sin estado</span>'}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-top-0 text-end">
                    <small class="text-muted">Actualizado: ${d.updated_at_cliente ? formatoFechaEuropeo(d.updated_at_cliente) : 'Sin fecha de actualización'}</small>
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
    //   INICIO ZONA DELETE CLIENTE  //
    ///////////////////////////////////
    function desacCliente(id) {
        Swal.fire({
            title: 'Desactivar',
            html: `¿Desea desactivar el cliente con ID ${id}?<br><br><small class="text-warning"><i class="bi bi-exclamation-triangle me-1"></i>El cliente quedará marcado como inactivo</small>`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Si',
            cancelButtonText: 'No',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                $.post("../../controller/cliente.php?op=eliminar", { id_cliente: id }, function (data) {

                    $table.DataTable().ajax.reload();

                    Swal.fire(
                        'Desactivado',
                        'El cliente ha sido desactivado',
                        'success'
                    )
                });
            }
        })
    }


    // CAPTURAR EL CLICK EN EL BOTÓN DE BORRAR
    $(document).on('click', '.desacCliente', function (event) {
        event.preventDefault();
        let id = $(this).data('id_cliente');
        desacCliente(id);
    });
    ////////////////////////////////////
    //   FIN ZONA DELETE CLIENTE    //
    //////////////////////////////////

    ///////////////////////////////////////
    //   INICIO ZONA ACTIVAR CLIENTE  //
    /////////////////////////////////////
    function activarCliente(id) {
        Swal.fire({
            title: 'Activar',
            text: `¿Desea activar el cliente con ID ${id}?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Si',
            cancelButtonText: 'No',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                $.post("../../controller/cliente.php?op=activar", { id_cliente: id }, function (data) {

                    $table.DataTable().ajax.reload();

                    Swal.fire(
                        'Activado',
                        'El cliente ha sido activado',
                        'success'
                    )
                });
            }
        })
    }


    // CAPTURAR EL CLICK EN EL BOTÓN DE ACTIVAR
    $(document).on('click', '.activarCliente', function (event) {
        event.preventDefault();
        let id = $(this).data('id_cliente');
        console.log("id cliente:",id);
        
        activarCliente(id);
    });
    ////////////////////////////////////
    //   FIN ZONA ACTIVAR CLIENTE    //
    //////////////////////////////////

    ///////////////////////////////////////
    //      INICIO ZONA NUEVO           //
    //        BOTON DE NUEVO           // 
    /////////////////////////////////////
    // BOTÓN NUEVO AHORA ES UN ENLACE DIRECTO AL FORMULARIO INDEPENDIENTE
    // La funcionalidad del modal ha sido removida

    // *****************************************************/
    // FUNCIONES DE GUARDAR CLIENTE REMOVIDAS
    // AHORA SE MANEJAN EN EL FORMULARIO INDEPENDIENTE
    // *****************************************************/

    ///////////////////////////////////////
    //      FIN ZONA NUEVO               //
    // (Ahora manejado por formulario    //
    //       independiente)              //
    /////////////////////////////////////

    ///////////////////////////////////////
    //      INICIO ZONA EDITAR           //
    //        BOTON DE EDITAR           //
    /////////////////////////////////////
    // CAPTURAR EL CLICK EN EL BOTÓN DE EDITAR
    $(document).on('click', '.editarCliente', function (event) {
        event.preventDefault();

        let id = $(this).data('id_cliente');
        console.log("id cliente:", id);

        // Redirigir al formulario independiente en modo edición
        window.location.href = `formularioCliente.php?modo=editar&id=${id}`;
    });
    ///////////////////////////////////////
    //        FIN ZONA EDITAR           //
    /////////////////////////////////////

    ///////////////////////////////////////
    //      INICIO ZONA FORMULARIO       //
    //        BOTON DE FORMULARIO       //
    /////////////////////////////////////
    // CAPTURAR EL CLICK EN EL BOTÓN DE FORMULARIO (CONTACTOS)
    $(document).on('click', '.formularioCliente', function (event) {
        event.preventDefault();

        let id = $(this).data('id_cliente');
        console.log("id cliente para contactos:", id);

        // Redirigir al módulo de contactos del cliente
        window.location.href = `../MntClientes_contacto/index.php?id_cliente=${id}`;
    });
    ///////////////////////////////////////
    //        FIN ZONA FORMULARIO       //
    /////////////////////////////////////


    /*********************************************************** */
    /********************************************************** */
    /* A PARTIR DE AQUI NO TOCAR  SE ACTUALIZA AUTOMATICAMENTE */
    /******************************************************** */
    /******************************************************* */

    //ejemplo -- var table_e = $('#clientes-table').DataTable(datatable_clientesConfig);

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

    // Filtro de cada columna en el pie de la tabla de clientes (tfoot)
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
        const horas = fecha.getHours().toString().padStart(2, '0');
        const minutos = fecha.getMinutes().toString().padStart(2, '0');
        
        return `${dia}/${mes}/${año} ${horas}:${minutos}`;
    } catch (error) {
        console.error('Error al formatear fecha:', error);
        return 'Error en fecha';
    }
}