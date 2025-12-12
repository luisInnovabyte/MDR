$(document).ready(function () {
  // Agregar estilos CSS para mejorar la visualización
  if (!document.getElementById("proveedor-styles")) {
    const style = document.createElement("style");
    style.id = "proveedor-styles";
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
    // FormValidator removido - ahora se maneja en formularioProveedor.js
    /////////////////////////////////////////
    //     FIN FORMATEO DE CAMPOS          //
    ////////////////////////////////////////


    /////////////////////////////////////
    // INICIO DE LA TABLA DE PROVEEDORES //
    //         DATATABLES             //
    ///////////////////////////////////
    var datatable_proveedoresConfig = {
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
            emptyTable: "No hay proveedores registrados",
            info: "Mostrando _START_ a _END_ de _TOTAL_ proveedores",
            infoEmpty: "Mostrando 0 a 0 de 0 proveedores",
            infoFiltered: "(filtrado de _MAX_ proveedores totales)",
            lengthMenu: "Mostrar _MENU_ proveedores por página",
            loadingRecords: "Cargando...",
            processing: "Procesando...",
            search: "Buscar:",
            zeroRecords: "No se encontraron proveedores que coincidan con la búsqueda",
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
            { name: 'id_proveedor', data: 'id_proveedor', visible: false, className: "text-center" }, // Columna 1: ID_PROVEEDOR
            { name: 'codigo_proveedor', data: 'codigo_proveedor' , className: "text-center align-middle" }, // Columna 2: CODIGO_PROVEEDOR
            { name: 'nombre_proveedor', data: 'nombre_proveedor', className: "text-center align-middle"  }, // Columna 3: NOMBRE_PROVEEDOR
            { name: 'nif_proveedor', data: 'nif_proveedor', className: "text-center align-middle"  }, // Columna 4: NIF_PROVEEDOR
            { name: 'telefono_proveedor', data: 'telefono_proveedor', className: "text-center align-middle"  }, // Columna 5: TELEFONO_PROVEEDOR
            { name: 'email_proveedor', data: 'email_proveedor', className: "text-center align-middle"  }, // Columna 6: EMAIL_PROVEEDOR
            { name: 'cantidad_contactos', data: 'cantidad_contactos', className: "text-center align-middle"  }, // Columna 7: CANTIDAD_CONTACTOS
            { name: 'activo_proveedor', data: 'activo_proveedor', className: "text-center align-middle"  }, // Columna 8: ESTADO
            { name: 'activar', data: null, className: "text-center align-middle" }, // Columna 9: ACTIVAR/DESACTIVAR
            { name: 'editar', data: null, defaultContent: '', className: "text-center align-middle"  },  // Columna 10: EDITAR
            { name: 'formulario', data: null, defaultContent: '', className: "text-center align-middle"  },  // Columna 11: FORMULARIO
            
        ], // de las columnas
        columnDefs: [
            // Cuidado que el ordrData puede interferir con el ordenamiento de la tabla    
           
            // Columna 0: BOTÓN MÁS 
            { targets: "control:name", width: '5%', searchable: false, orderable: false, className: "text-center"},
            // Columna 1: id_proveedor 
            { targets: "id_proveedor:name", width: '5%', searchable: false, orderable: false, className: "text-center" },
            // Columna 2: codigo_proveedor
            { targets: "codigo_proveedor:name", width: '12%', searchable: true, orderable: true, className: "text-center" },
            // Columna 3: nombre_proveedor
            { targets: "nombre_proveedor:name", width: '20%', searchable: true, orderable: true, className: "text-center" },
            // Columna 4: nif_proveedor
            { targets: "nif_proveedor:name", width: '10%', searchable: true, orderable: true, className: "text-center" },
            // Columna 5: telefono_proveedor
            { targets: "telefono_proveedor:name", width: '12%', searchable: true, orderable: true, className: "text-center" },
            // Columna 6: email_proveedor
            { targets: "email_proveedor:name", width: '15%', searchable: true, orderable: true, className: "text-center" },
            // Columna 7: cantidad_contactos
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
            // Columna 8: activo_proveedor
            {
                targets: "activo_proveedor:name", width: '8%', orderable: true, searchable: true, className: "text-center",
                render: function (data, type, row) {
                    if (type === "display") {
                        return row.activo_proveedor == 1 ? '<i class="bi bi-check-circle text-success fa-2x"></i>' : '<i class="bi bi-x-circle text-danger fa-2x"></i>';
                    }
                    return row.activo_proveedor;
                }
            },
            // Columna 9: BOTON PARA ACTIVAR/DESACTIVAR ESTADO
            {   
                targets: "activar:name", width: '8%', searchable: false, orderable: false, class: "text-center",
                render: function (data, type, row) {
                    // El nombre que de la variable que se pasa por data-xxx debe ser el mismo que el nombre de la columna en la base de datos
                    if (row.activo_proveedor == 1) {
                        // permito desactivar el proveedor
                        return `<button type="button" class="btn btn-danger btn-sm desacProveedor" data-bs-toggle="tooltip-primary" data-placement="top" title="Desactivar" data-original-title="Tooltip on top" 
                             data-id_proveedor="${row.id_proveedor}"> 
                             <i class="fa-solid fa-trash"></i>
                             </button>`}
                    else {
                        // debo permitir activar de nuevo el proveedor
                        return `<button class="btn btn-success btn-sm activarProveedor" data-bs-toggle="tooltip-primary" data-placement="top" title="Activar" data-original-title="Tooltip on top" 
                             data-id_proveedor="${row.id_proveedor}"> 
                             <i class="bi bi-hand-thumbs-up-fill"></i>
                            </button>`}
                } // de la function
            },// 
            // Columna 10: BOTON PARA EDITAR PROVEEDOR
            {   
                targets: "editar:name", width: '7%', searchable: false, orderable: false, class: "text-center",
                render: function (data, type, row) {
                    // El nombre que de la variable que se pasa por data-xxx debe ser el mismo que el nombre de la columna en la base de datos
                    // botón editar el proveedor
                    return `<button type="button" class="btn btn-info btn-sm editarProveedor" data-toggle="tooltip-primary" data-placement="top" title="Editar"  
                             data-id_proveedor="${row.id_proveedor}"> 
                             <i class="fa-solid fa-edit"></i>
                             </button>`
                } // de la function
            },
            // Columna 11: BOTON PARA FORMULARIO PROVEEDOR (Contactos)
            {   
                targets: "formulario:name", width: '7%', searchable: false, orderable: false, class: "text-center",
                render: function (data, type, row) {
                    // El nombre que de la variable que se pasa por data-xxx debe ser el mismo que el nombre de la columna en la base de datos
                    // botón para ir al formulario del proveedor
                    return `<button type="button" class="btn btn-primary btn-sm formularioProveedor" data-toggle="tooltip-primary" data-placement="top" title="Formulario"  
                             data-id_proveedor="${row.id_proveedor}"> 
                             <i class="fa-solid fa-file-alt"></i>
                             </button>`
                } // de la function
            }
             // De la columna 11
        ], // de la columnDefs
        ajax: {
            url: '../../controller/proveedor.php?op=listar',
            type: 'GET',
            dataSrc: function (json) {
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
        order: [[2, 'asc']], // Ordenar por código de proveedor por defecto
    }; // de la variable datatable_proveedoresConfig
    ////////////////////////////
    // FIN DE LA TABLA DE PROVEEDORES //
    ///////////////////////////


    /************************************/
    //     ZONA DE DEFINICIONES        //
    /**********************************/
    // Definición inicial de la tabla de proveedores
    var $table = $('#proveedores_data');  /*<--- Es el nombre que le hemos dado a la tabla en HTML */
    var $tableConfig = datatable_proveedoresConfig; /*<--- Es el nombre que le hemos dado a la declaración de la definicion de la tabla */
    //var $columSearch = 3; /* <-- Es la columna en la cual al hacer click el valor se colocará en la zona de search y se buscará */
    var $tableBody = $('#proveedores_data tbody'); /*<--- Es el nombre que le hemos dado al cuerpo de la tabla en HTML */
    /* en el tableBody solo cambiar el nombre de la tabla que encontraremos en HTML*/
    var $columnFilterInputs = $('#proveedores_data tfoot input, #proveedores_data tfoot select'); /*<--- Selecciona tanto inputs como selects de los pies de la tabla en HTML */
    /* en el $columnFilterInputs solo cambiar el nombre de la tabla que encontraremos en HTML*/

    //ejemplo -- var table_e = $('#proveedores-table').DataTable(datatable_proveedoresConfig);
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
                        <i class="bi bi-building-fill fs-3 me-2"></i>
                        <h5 class="card-title mb-0">Detalles del Proveedor</h5>
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
                                            <i class="bi bi-hash me-2"></i>ID Proveedor
                                        </th>
                                        <td class="pe-4">
                                            ${d.id_proveedor || '<span class="text-muted fst-italic">Sin ID</span>'}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="ps-4 w-40 align-top">
                                            <i class="bi bi-upc me-2"></i>Código
                                        </th>
                                        <td class="pe-4">
                                            ${d.codigo_proveedor || '<span class="text-muted fst-italic">Sin código</span>'}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="ps-4 w-40 align-top">
                                            <i class="bi bi-building me-2"></i>Nombre
                                        </th>
                                        <td class="pe-4">
                                            ${d.nombre_proveedor || '<span class="text-muted fst-italic">Sin nombre</span>'}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="ps-4 w-40 align-top">
                                            <i class="bi bi-card-text me-2"></i>NIF/CIF
                                        </th>
                                        <td class="pe-4">
                                            ${d.nif_proveedor || '<span class="text-muted fst-italic">Sin NIF/CIF</span>'}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="ps-4 w-40 align-top">
                                            <i class="bi bi-geo-alt me-2"></i>Dirección
                                        </th>
                                        <td class="pe-4">
                                            ${d.direccion_proveedor || '<span class="text-muted fst-italic">Sin dirección</span>'}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="ps-4 w-40 align-top">
                                            <i class="bi bi-mailbox me-2"></i>CP/Población
                                        </th>
                                        <td class="pe-4">
                                            ${(d.cp_proveedor && d.poblacion_proveedor) ? `${d.cp_proveedor} - ${d.poblacion_proveedor}` : '<span class="text-muted fst-italic">Sin CP/Población</span>'}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="ps-4 w-40 align-top">
                                            <i class="bi bi-map me-2"></i>Provincia
                                        </th>
                                        <td class="pe-4">
                                            ${d.provincia_proveedor || '<span class="text-muted fst-italic">Sin provincia</span>'}
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
                                            <i class="bi bi-person me-2"></i>Contacto
                                        </th>
                                        <td class="pe-4">
                                            ${d.persona_contacto_proveedor || '<span class="text-muted fst-italic">Sin contacto</span>'}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="ps-4 w-40 align-top">
                                            <i class="bi bi-telephone me-2"></i>Teléfono
                                        </th>
                                        <td class="pe-4">
                                            ${d.telefono_proveedor || '<span class="text-muted fst-italic">Sin teléfono</span>'}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="ps-4 w-40 align-top">
                                            <i class="bi bi-envelope me-2"></i>Email
                                        </th>
                                        <td class="pe-4">
                                            ${d.email_proveedor ? `<a href="mailto:${d.email_proveedor}" target="_blank">${d.email_proveedor}</a>` : '<span class="text-muted fst-italic">Sin email</span>'}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="ps-4 w-40 align-top">
                                            <i class="bi bi-globe me-2"></i>Web
                                        </th>
                                        <td class="pe-4">
                                            ${d.web_proveedor ? `<a href="${d.web_proveedor}" target="_blank" rel="noopener">${d.web_proveedor}</a>` : '<span class="text-muted fst-italic">Sin web</span>'}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="ps-4 w-40 align-top">
                                            <i class="bi bi-people-fill me-2"></i>Contactos Registrados
                                        </th>
                                        <td class="pe-4">
                                            ${(() => {
                                                const cantidad = parseInt(d.cantidad_contactos) || 0;
                                                const badgeClass = cantidad > 0 ? 'bg-success' : 'bg-secondary';
                                                const texto = cantidad === 1 ? 'contacto' : 'contactos';
                                                return `<span class="badge ${badgeClass} fs-6">
                                                            <i class="bi bi-people-fill me-1"></i>${cantidad} ${texto}
                                                        </span>`;
                                            })()}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="ps-4 w-40 align-top">
                                            <i class="bi bi-tools me-2"></i>SAT
                                        </th>
                                        <td class="pe-4">
                                            ${(d.direccion_sat_proveedor || d.telefono_sat_proveedor || d.email_sat_proveedor) 
                                                ? `<div>
                                                    ${d.direccion_sat_proveedor ? `<div><small>Dir: ${d.direccion_sat_proveedor}</small></div>` : ''}
                                                    ${d.telefono_sat_proveedor ? `<div><small>Tel: ${d.telefono_sat_proveedor}</small></div>` : ''}
                                                    ${d.email_sat_proveedor ? `<div><small>Email: ${d.email_sat_proveedor}</small></div>` : ''}
                                                   </div>`
                                                : '<span class="text-muted fst-italic">Sin SAT</span>'
                                            }
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="ps-4 w-40 align-top">
                                            <i class="bi bi-chat-text me-2"></i>Observaciones
                                        </th>
                                        <td class="pe-4" style="max-width: 300px; word-wrap: break-word; white-space: pre-wrap; overflow-wrap: break-word;">
                                            ${d.observaciones_proveedor || '<span class="text-muted fst-italic">Sin observaciones</span>'}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="ps-4 w-40 align-top">
                                            <i class="bi bi-calendar-plus me-2"></i>Creado el:
                                        </th>
                                        <td class="pe-4">
                                            ${d.created_at_proveedor ? formatoFechaEuropeo(d.created_at_proveedor) : '<span class="text-muted fst-italic">Sin fecha</span>'}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="ps-4 w-40 align-top">
                                            <i class="bi bi-credit-card me-2"></i>Forma de Pago
                                        </th>
                                        <td class="pe-4">
                                            ${d.descripcion_forma_pago_proveedor || '<span class="text-muted fst-italic">Sin forma de pago</span>'}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="ps-4 w-40 align-top">
                                            <i class="bi bi-tag me-2"></i>Tipo de Pago
                                        </th>
                                        <td class="pe-4">
                                            ${d.tipo_pago_proveedor ? `<span class="badge ${d.tipo_pago_proveedor === 'Pago único' ? 'bg-info' : d.tipo_pago_proveedor === 'Pago fraccionado' ? 'bg-warning' : 'bg-secondary'}">${d.tipo_pago_proveedor}</span>` : '<span class="text-muted fst-italic">Sin información</span>'}
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
                                            ${d.estado_forma_pago_proveedor ? `<span class="badge ${d.estado_forma_pago_proveedor === 'Configurado' ? 'bg-success' : d.estado_forma_pago_proveedor === 'Sin configurar' ? 'bg-secondary' : 'bg-danger'}">${d.estado_forma_pago_proveedor}</span>` : '<span class="text-muted fst-italic">Sin estado</span>'}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-top-0 text-end">
                    <small class="text-muted">Actualizado: ${d.updated_at_proveedor ? formatoFechaEuropeo(d.updated_at_proveedor) : 'Sin fecha de actualización'}</small>
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
    //   INICIO ZONA DELETE PROVEEDOR  //
    ///////////////////////////////////
    function desacProveedor(id) {
        Swal.fire({
            title: 'Desactivar',
            html: `¿Desea desactivar el proveedor con ID ${id}?<br><br><small class="text-warning"><i class="bi bi-exclamation-triangle me-1"></i>El proveedor quedará marcado como inactivo</small>`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Si',
            cancelButtonText: 'No',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                $.post("../../controller/proveedor.php?op=eliminar", { id_proveedor: id }, function (data) {

                    $table.DataTable().ajax.reload();

                    Swal.fire(
                        'Desactivado',
                        'El proveedor ha sido desactivado',
                        'success'
                    )
                });
            }
        })
    }


    // CAPTURAR EL CLICK EN EL BOTÓN DE BORRAR
    $(document).on('click', '.desacProveedor', function (event) {
        event.preventDefault();
        let id = $(this).data('id_proveedor');
        desacProveedor(id);
    });
    ////////////////////////////////////
    //   FIN ZONA DELETE PROVEEDOR    //
    //////////////////////////////////

    ///////////////////////////////////////
    //   INICIO ZONA ACTIVAR PROVEEDOR  //
    /////////////////////////////////////
    function activarProveedor(id) {
        Swal.fire({
            title: 'Activar',
            text: `¿Desea activar el proveedor con ID ${id}?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Si',
            cancelButtonText: 'No',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                $.post("../../controller/proveedor.php?op=activar", { id_proveedor: id }, function (data) {

                    $table.DataTable().ajax.reload();

                    Swal.fire(
                        'Activado',
                        'El proveedor ha sido activado',
                        'success'
                    )
                });
            }
        })
    }


    // CAPTURAR EL CLICK EN EL BOTÓN DE ACTIVAR
    $(document).on('click', '.activarProveedor', function (event) {
        event.preventDefault();
        let id = $(this).data('id_proveedor');
        console.log("id proveedor:",id);
        
        activarProveedor(id);
    });
    ////////////////////////////////////
    //   FIN ZONA ACTIVAR PROVEEDOR    //
    //////////////////////////////////

    ///////////////////////////////////////
    //      INICIO ZONA NUEVO           //
    //        BOTON DE NUEVO           // 
    /////////////////////////////////////
    // BOTÓN NUEVO AHORA ES UN ENLACE DIRECTO AL FORMULARIO INDEPENDIENTE
    // La funcionalidad del modal ha sido removida

    // *****************************************************/
    // FUNCIONES DE GUARDAR PROVEEDOR REMOVIDAS
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
    $(document).on('click', '.editarProveedor', function (event) {
        event.preventDefault();

        let id = $(this).data('id_proveedor');
        console.log("id proveedor:", id);

        // Redirigir al formulario independiente en modo edición
        window.location.href = `formularioProveedor.php?modo=editar&id=${id}`;
    });
    ///////////////////////////////////////
    //        FIN ZONA EDITAR           //
    /////////////////////////////////////

    ///////////////////////////////////////
    //      INICIO ZONA FORMULARIO       //
    //        BOTON DE FORMULARIO       //
    /////////////////////////////////////
    // CAPTURAR EL CLICK EN EL BOTÓN DE FORMULARIO
    $(document).on('click', '.formularioProveedor', function (event) {
        event.preventDefault();

        let id = $(this).data('id_proveedor');
        console.log("id proveedor para formulario:", id);

        // Redirigir al módulo de contactos del proveedor
        window.location.href = `../MntProveedores_contacto/index.php?id_proveedor=${id}`;
    });
    ///////////////////////////////////////
    //        FIN ZONA FORMULARIO       //
    /////////////////////////////////////


    /*********************************************************** */
    /********************************************************** */
    /* A PARTIR DE AQUI NO TOCAR  SE ACTUALIZA AUTOMATICAMENTE */
    /******************************************************** */
    /******************************************************* */

    //ejemplo -- var table_e = $('#proveedores-table').DataTable(datatable_proveedoresConfig);

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

    // Filtro de cada columna en el pie de la tabla de proveedores (tfoot)
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