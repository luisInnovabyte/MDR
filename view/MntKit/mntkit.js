$(document).ready(function () {
    // Obtener el ID del artículo KIT desde la URL
    const urlParams = new URLSearchParams(window.location.search);
    const idArticuloMaestro = urlParams.get('id_articulo');

    // Validar que se haya proporcionado un ID de artículo
    if (!idArticuloMaestro) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se ha proporcionado un ID de artículo KIT',
            confirmButtonText: 'Volver a Artículos'
        }).then(() => {
            window.location.href = '../MntArticulos/index.php';
        });
        return;
    }

    // Cargar información del artículo KIT
    cargarInfoArticuloKit(idArticuloMaestro);

    /////////////////////////////////////
    // INICIO DE LA TABLA DE KIT       //
    //         DATATABLES              //
    /////////////////////////////////////
    var datatable_kitConfig = {
        processing: true,
        layout: {
            bottomEnd: {
                paging: {
                    firstLast: true,
                    numbers: false,
                    previousNext: true
                }
            }
        },
        language: {
            emptyTable: "No hay componentes registrados en este KIT",
            info: "Mostrando _START_ a _END_ de _TOTAL_ componentes",
            infoEmpty: "Mostrando 0 a 0 de 0 componentes",
            infoFiltered: "(filtrado de _MAX_ componentes totales)",
            lengthMenu: "Mostrar _MENU_ componentes por página",
            loadingRecords: "Cargando...",
            processing: "Procesando...",
            search: "Buscar:",
            zeroRecords: "No se encontraron componentes que coincidan con la búsqueda",
            paginate: {
                first: '<i class="bi bi-chevron-double-left"></i>',
                last: '<i class="bi bi-chevron-double-right"></i>',
                previous: '<i class="bi bi-chevron-compact-left"></i>',
                next: '<i class="bi bi-chevron-compact-right"></i>'
            }
        },
        columns: [
            { name: 'control', data: null, defaultContent: '', orderable: false, className: "details-control sorting_1 text-center" }, // Columna 0: Mostrar más
            { name: 'id_kit', data: 'id_kit', visible: false, className: "text-center" }, // Columna 1: ID
            { name: 'codigo_articulo_componente', data: 'codigo_articulo_componente', className: "text-center" }, // Columna 2: CODIGO
            { name: 'nombre_articulo_componente', data: 'nombre_articulo_componente', className: "text-center" }, // Columna 3: NOMBRE
            { name: 'cantidad_kit', data: 'cantidad_kit', className: "text-center" }, // Columna 4: CANTIDAD
            { name: 'precio_articulo_componente', data: 'precio_articulo_componente', className: "text-center" }, // Columna 5: PRECIO UNITARIO
            { name: 'subtotal_componente', data: 'subtotal_componente', className: "text-center" }, // Columna 6: SUBTOTAL
            { name: 'activo_kit', data: 'activo_kit', className: "text-center" }, // Columna 7: ACTIVO
            { name: 'eliminar', data: null, className: "text-center" }, // Columna 8: ELIMINAR
            { name: 'editar', data: null, defaultContent: '', className: "text-center" }  // Columna 9: EDITAR
        ],
        columnDefs: [
            // Columna 0: Control (botón de expandir)
            { targets: "control:name", width: '5%', searchable: false, orderable: false, className: "text-center" },
            // Columna 1: id_kit 
            { targets: "id_kit:name", width: '5%', searchable: false, orderable: false, className: "text-center" },
            // Columna 2: codigo_articulo_componente
            { targets: "codigo_articulo_componente:name", width: '15%', searchable: true, orderable: true, className: "text-center" },
            // Columna 3: nombre_articulo_componente
            { targets: "nombre_articulo_componente:name", width: '23%', searchable: true, orderable: true, className: "text-center" },
            // Columna 4: cantidad_kit
            {
                targets: "cantidad_kit:name", width: '10%', orderable: true, searchable: false, className: "text-center",
                render: function (data, type, row) {
                    if (type === "display") {
                        return `<span class="badge bg-primary">${data}</span>`;
                    }
                    return data;
                }
            },
            // Columna 5: precio_articulo_componente
            {
                targets: "precio_articulo_componente:name", width: '11%', orderable: true, searchable: false, className: "text-center",
                render: function (data, type, row) {
                    if (type === "display") {
                        const precio = parseFloat(data).toFixed(2);
                        return `${precio} €`;
                    }
                    return data;
                }
            },
            // Columna 6: subtotal_componente
            {
                targets: "subtotal_componente:name", width: '11%', orderable: true, searchable: false, className: "text-center",
                render: function (data, type, row) {
                    if (type === "display") {
                        const subtotal = parseFloat(data).toFixed(2);
                        return `<strong>${subtotal} €</strong>`;
                    }
                    return data;
                }
            },
            // Columna 7: activo_kit
            {
                targets: "activo_kit:name", width: '8%', orderable: true, searchable: true, className: "text-center",
                render: function (data, type, row) {
                    if (type === "display") {
                        return data == 1 ? 
                            '<i class="bi bi-check-circle text-success fa-2x"></i>' : 
                            '<i class="bi bi-x-circle text-danger fa-2x"></i>';
                    }
                    return data;
                }
            },
            // Columna 8: BOTON PARA ELIMINAR COMPONENTE
            {
                targets: "eliminar:name", width: '8%', searchable: false, orderable: false, class: "text-center",
                render: function (data, type, row) {
                    return `<button type="button" class="btn btn-danger btn-sm eliminarComponente" data-bs-toggle="tooltip" data-placement="top" title="Eliminar componente" 
                             data-id_kit="${row.id_kit}" data-nombre="${row.nombre_articulo_componente}"> 
                             <i class="fa-solid fa-trash"></i>
                             </button>`;
                }
            },
            // Columna 9: BOTON PARA EDITAR COMPONENTE
            {
                targets: "editar:name", width: '8%', searchable: false, orderable: false, class: "text-center",
                render: function (data, type, row) {
                    return `<button type="button" class="btn btn-info btn-sm editarComponente" data-toggle="tooltip" data-placement="top" title="Editar"  
                             data-id_kit="${row.id_kit}"> 
                             <i class="fa-solid fa-edit"></i>
                             </button>`;
                }
            }
        ],
        ajax: {
            url: '../../controller/kit.php?op=listar',
            type: 'GET',
            data: function() {
                return {
                    id_articulo_maestro: idArticuloMaestro
                };
            },
            dataSrc: 'data',
            error: function (xhr, error, code) {
                console.error('Error al cargar componentes del kit:', error);
                console.error('Código:', code);
                console.error('Respuesta:', xhr.responseText);
                Swal.fire({
                    icon: 'error',
                    title: 'Error al cargar datos',
                    text: 'No se pudieron cargar los componentes del kit. Revise la consola para más detalles.',
                    confirmButtonText: 'OK'
                });
            }
        },
        deferRender: true,
        pageLength: 25,
        lengthMenu: [10, 25, 50, 100],
        order: [[3, "asc"]] // Ordenar por nombre del componente (columna 3 ahora)
    };

    /************************************/
    //     ZONA DE DEFINICIONES        //
    /**********************************/
    var $table = $('#kit_data');
    var $tableConfig = datatable_kitConfig;
    var $tableBody = $('#kit_data tbody');
    var $columnFilterInputs = $('#kit_data tfoot input, #kit_data tfoot select');

    // Inicializar DataTable
    const table = $('#kit_data').DataTable(datatable_kitConfig);

    ///////////////////////////////////////////
    //    FILTROS DEL PIE DE TABLA          //
    ///////////////////////////////////////////

    // Filtro de cada columna en el pie de la tabla (tfoot)
    $columnFilterInputs.on('keyup change', function () {
        var columnIndex = table.column($(this).closest('th')).index();
        var searchValue = $(this).val();

        table.column(columnIndex).search(searchValue).draw();
    });

    ///////////////////////////////////////////
    //    FUNCIÓN DE DETALLES EXPANDIDOS     //
    ///////////////////////////////////////////
    function format(d) {
        return `
            <div class="card border-primary mb-3">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-box-seam fs-3 me-2"></i>
                        <h5 class="card-title mb-0">Detalles del Componente</h5>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless table-sm">
                                <tbody>
                                    <tr>
                                        <th scope="row" class="w-40">
                                            <i class="bi bi-hash me-2"></i>ID Componente
                                        </th>
                                        <td><strong>${d.id_kit || '--'}</strong></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">
                                            <i class="bi bi-upc-scan me-2"></i>Código Artículo
                                        </th>
                                        <td><strong>${d.codigo_articulo_componente || '--'}</strong></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">
                                            <i class="bi bi-box me-2"></i>Nombre Artículo
                                        </th>
                                        <td>${d.nombre_articulo_componente || '--'}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">
                                            <i class="bi bi-sort-numeric-up me-2"></i>Cantidad
                                        </th>
                                        <td><span class="badge bg-primary">${d.cantidad_kit || 0}</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless table-sm">
                                <tbody>
                                    <tr>
                                        <th scope="row" class="w-40">
                                            <i class="bi bi-currency-euro me-2"></i>Precio Unitario
                                        </th>
                                        <td><strong>${parseFloat(d.precio_articulo_componente || 0).toFixed(2)} €</strong></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">
                                            <i class="bi bi-calculator me-2"></i>Subtotal
                                        </th>
                                        <td><strong class="text-success">${parseFloat(d.subtotal_componente || 0).toFixed(2)} €</strong></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">
                                            <i class="bi bi-toggle-on me-2"></i>Estado
                                        </th>
                                        <td>
                                            ${d.activo_kit == 1 ? 
                                                '<span class="badge bg-success">Activo</span>' : 
                                                '<span class="badge bg-danger">Inactivo</span>'}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row">
                                            <i class="bi bi-clock-history me-2"></i>Fecha Creación
                                        </th>
                                        <td><small class="text-muted">${d.created_at_kit || '--'}</small></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-top-0 text-end">
                    <small class="text-muted">Última actualización: ${d.updated_at_kit || '--'}</small>
                </div>
            </div>
        `;
    }

    // Evento click para expandir/contraer detalles
    $tableBody.on('click', 'td.details-control', function () {
        var tr = $(this).closest('tr');
        var row = table.row(tr);

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

    /////////////////////////////////////
    //   FUNCIONES DE GESTIÓN          //
    /////////////////////////////////////

    // Función para cargar información del artículo KIT
    function cargarInfoArticuloKit(idArticulo) {
        $.ajax({
            url: '../../controller/kit.php?op=obtenerArticuloMaestro',
            type: 'POST',
            data: { id_articulo: idArticulo },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success' && response.data) {
                    const articulo = response.data;
                    $('#nombre-articulo-kit').text(articulo.nombre_articulo || 'Sin nombre');
                    $('#codigo-articulo-kit').text(articulo.codigo_articulo || '--');
                    $('#id-articulo-kit').text(articulo.id_articulo || '--');
                    
                    // Verificar que sea un KIT
                    if (articulo.es_kit_articulo != 1) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Advertencia',
                            text: 'Este artículo no está marcado como KIT',
                            confirmButtonText: 'OK'
                        });
                    }
                    
                    // Actualizar totales
                    actualizarTotalesKit(idArticulo);
                } else {
                    console.error('Error al cargar información del artículo KIT:', response);
                    $('#nombre-articulo-kit').text('Error al cargar');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error AJAX al cargar artículo KIT:', error);
                $('#nombre-articulo-kit').text('Error al cargar');
            }
        });
    }

    // Función para actualizar totales del kit
    function actualizarTotalesKit(idArticulo) {
        // Obtener total de componentes
        $.ajax({
            url: '../../controller/kit.php?op=obtenerTotalComponentes',
            type: 'POST',
            data: { id_articulo_maestro: idArticulo },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    $('#total-componentes-kit').text(response.total_componentes || 0);
                }
            }
        });

        // Obtener precio total
        $.ajax({
            url: '../../controller/kit.php?op=obtenerPrecioTotal',
            type: 'POST',
            data: { id_articulo_maestro: idArticulo },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    const precioTotal = parseFloat(response.precio_total || 0).toFixed(2);
                    $('#precio-total-kit').text(`${precioTotal} €`);
                }
            }
        });
    }

    // Evento: Agregar nuevo componente
    $('#modalFormularioKit').on('show.bs.modal', function (e) {
        // Solo limpiar formulario si no hay id_kit (modo agregar)
        // En modo editar, el id_kit ya estará seteado
        if (!$('#id_kit').val()) {
            $('#frmKit')[0].reset();
            $('#id_kit').val('');
            $('#id_articulo_maestro').val(idArticuloMaestro);
            $('#modalFormularioKitLabel').text('Agregar Componente al KIT');
            
            // Cargar artículos disponibles solo en modo agregar
            cargarArticulosDisponibles(idArticuloMaestro);
        }
    });

    // Función para cargar artículos disponibles
    function cargarArticulosDisponibles(idArticuloMaestro) {
        $.ajax({
            url: '../../controller/kit.php?op=listarArticulosDisponibles',
            type: 'POST',
            data: { id_articulo_maestro: idArticuloMaestro },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success' && response.data) {
                    const select = $('#id_articulo_componente');
                    select.empty();
                    select.append('<option value="">Seleccione un artículo...</option>');
                    
                    response.data.forEach(function(articulo) {
                        const precio = parseFloat(articulo.precio_alquiler_articulo || 0).toFixed(2);
                        select.append(`<option value="${articulo.id_articulo}" data-precio="${precio}">
                            ${articulo.codigo_articulo} - ${articulo.nombre_articulo} (${precio} €)
                        </option>`);
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar artículos disponibles:', error);
            }
        });
    }

    // Evento: Submit del formulario
    $('#frmKit').on('submit', function(e) {
        e.preventDefault();
        
        const formData = $(this).serialize();
        
        $.ajax({
            url: '../../controller/kit.php?op=guardaryeditar',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Éxito',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                    $('#modalFormularioKit').modal('hide');
                    table.ajax.reload();
                    actualizarTotalesKit(idArticuloMaestro);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Error al guardar el componente'
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al guardar:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error de comunicación con el servidor'
                });
            }
        });
    });

    // Evento: Editar componente
    $(document).on('click', '.editarComponente', function() {
        const idKit = $(this).data('id_kit');
        
        $.ajax({
            url: '../../controller/kit.php?op=mostrar',
            type: 'POST',
            data: { id_kit: idKit },
            dataType: 'json',
            success: function(data) {
                console.log('Datos recibidos del servidor:', data);
                
                $('#modalFormularioKitLabel').text('Editar Componente del KIT');
                
                // Cargar artículos disponibles y seleccionar el actual
                cargarArticulosDisponibles(data.id_articulo_maestro);
                
                setTimeout(function() {
                    // Establecer valores de campos ocultos
                    $('#id_kit').val(data.id_kit);
                    $('#id_articulo_maestro').val(data.id_articulo_maestro);
                    
                    // Establecer la cantidad PRIMERO
                    $('#cantidad_kit').val(data.cantidad_kit);
                    console.log('Cantidad seteada:', data.cantidad_kit);
                    
                    // Seleccionar el artículo componente
                    $('#id_articulo_componente').val(data.id_articulo_componente);
                    
                    // Actualizar el precio desde los datos recibidos
                    const precio = parseFloat(data.precio_articulo_componente || 0);
                    $('#precio_unitario_display').val(precio.toFixed(2));
                    
                    // Calcular y mostrar el subtotal
                    const cantidad = parseFloat(data.cantidad_kit || 0);
                    const subtotal = precio * cantidad;
                    $('#subtotal_display').val(subtotal.toFixed(2));
                    
                    console.log('Valores finales - Cantidad:', $('#cantidad_kit').val(), 'Precio:', precio, 'Subtotal:', subtotal);
                    
                    // Disparar el evento change para asegurar sincronización
                    $('#id_articulo_componente').trigger('change');
                }, 500);
                
                $('#modalFormularioKit').modal('show');
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar componente:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudo cargar el componente'
                });
            }
        });
    });

    // Evento: Eliminar componente
    $(document).on('click', '.eliminarComponente', function() {
        const idKit = $(this).data('id_kit');
        const nombreComponente = $(this).data('nombre');
        
        Swal.fire({
            title: '¿Estás seguro?',
            text: `¿Deseas eliminar el componente "${nombreComponente}" del KIT?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '../../controller/kit.php?op=eliminar',
                    type: 'POST',
                    data: { id_kit: idKit },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Eliminado',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false
                            });
                            table.ajax.reload();
                            actualizarTotalesKit(idArticuloMaestro);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error al eliminar:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Error de comunicación con el servidor'
                        });
                    }
                });
            }
        });
    });

    // Inicializar tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();
});
