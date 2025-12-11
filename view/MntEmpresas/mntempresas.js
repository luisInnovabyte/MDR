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
    // FormValidator removido - ahora se maneja en formularioEmpresa.js
    /////////////////////////////////////////
    //     FIN FORMATEO DE CAMPOS          //
    ////////////////////////////////////////


    /////////////////////////////////////
    // INICIO DE LA TABLA DE EMPRESAS //
    //         DATATABLES             //
    ///////////////////////////////////
    var datatable_empresasConfig = {
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
            paginate: {
                first: '<i class="bi bi-chevron-double-left"></i>',
                last: '<i class="bi bi-chevron-double-right"></i>',
                previous: '<i class="bi bi-chevron-compact-left"></i>',
                next: '<i class="bi bi-chevron-compact-right"></i>'
            }
        },
        columns: [
            { name: 'control', data: null, defaultContent: '', className: 'details-control sorting_1 text-center' },
            { name: 'id_empresa', data: 'id_empresa', visible: false, className: "text-center" },
            { name: 'codigo_empresa', data: 'codigo_empresa', className: "text-center align-middle" },
            { name: 'nombre_empresa', data: 'nombre_empresa', className: "text-center align-middle" },
            { name: 'nif_empresa', data: 'nif_empresa', className: "text-center align-middle" },
            { name: 'tipo_empresa', data: null, className: "text-center align-middle" },
            { name: 'activo_empresa', data: 'activo_empresa', className: "text-center align-middle" },
            { name: 'activar', data: null, className: "text-center align-middle" },
            { name: 'editar', data: null, defaultContent: '', className: "text-center align-middle" },
        ],
        columnDefs: [
            { targets: "control:name", width: '5%', searchable: false, orderable: false, className: "text-center"},
            { targets: "id_empresa:name", width: '5%', searchable: false, orderable: false, className: "text-center" },
            { targets: "codigo_empresa:name", width: '10%', searchable: true, orderable: true, className: "text-center" },
            { targets: "nombre_empresa:name", width: '25%', searchable: true, orderable: true, className: "text-center" },
            { targets: "nif_empresa:name", width: '15%', searchable: true, orderable: true, className: "text-center" },
            {
                targets: "tipo_empresa:name", width: '10%', orderable: true, searchable: true, className: "text-center",
                render: function (data, type, row) {
                    if (type === "display") {
                        if (row.ficticia_empresa == 1) {
                            return '<span class="badge bg-warning text-dark">FICTICIA</span>';
                        } else {
                            return '<span class="badge bg-success">REAL</span>';
                        }
                    }
                    return row.ficticia_empresa == 1 ? 'FICTICIA' : 'REAL';
                }
            },
            {
                targets: "activo_empresa:name", width: '10%', orderable: true, searchable: true, className: "text-center",
                render: function (data, type, row) {
                    if (type === "display") {
                        return row.activo_empresa == 1 ? '<i class="bi bi-check-circle text-success fa-2x"></i>' : '<i class="bi bi-x-circle text-danger fa-2x"></i>';
                    }
                    return row.activo_empresa;
                }
            },
            {   
                targets: "activar:name", width: '10%', searchable: false, orderable: false, class: "text-center",
                render: function (data, type, row) {
                    if (row.activo_empresa == 1) {
                        return `<button type="button" class="btn btn-danger btn-sm desacEmpresa" data-bs-toggle="tooltip-primary" data-placement="top" title="Desactivar" data-original-title="Tooltip on top" 
                             data-id_empresa="${row.id_empresa}"> 
                             <i class="fa-solid fa-trash"></i>
                             </button>`;
                    } else {
                        return `<button class="btn btn-success btn-sm activarEmpresa" data-bs-toggle="tooltip-primary" data-placement="top" title="Activar" data-original-title="Tooltip on top" 
                             data-id_empresa="${row.id_empresa}">
                             <i class="bi bi-hand-thumbs-up-fill"></i>
                            </button>`;
                    }
                }
            },
            {   
                targets: "editar:name", width: '10%', searchable: false, orderable: false, class: "text-center",
                render: function (data, type, row) {
                    return `<button type="button" class="btn btn-info btn-sm editarEmpresa" data-toggle="tooltip-primary" data-placement="top" title="Editar"  
                             data-id_empresa="${row.id_empresa}"> 
                             <i class="fa-solid fa-edit"></i>
                             </button>`
                }
            }
        ],
        ajax: {
            url: '../../controller/empresas.php?op=listar',
            type: 'GET',
            dataSrc: function (json) {
                console.log("JSON recibido:", json);
                return json.data || json;
            }
        }
    };
    ////////////////////////////
    // FIN DE LA TABLA DE EMPRESAS //
    ///////////////////////////


    /************************************/
    //     ZONA DE DEFINICIONES        //
    /**********************************/
    var $table = $('#empresas_data');
    var $tableConfig = datatable_empresasConfig;
    var $tableBody = $('#empresas_data tbody');
    var $columnFilterInputs = $('#empresas_data tfoot input, #empresas_data tfoot select');

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
                        <i class="bi bi-building fs-3 me-2"></i>
                        <h5 class="card-title mb-0">Detalles de la Empresa</h5>
                    </div>
                </div>
                <div class="card-body p-0" style="overflow: visible;">
                    <table class="table table-borderless table-striped table-hover mb-0">
                        <tbody>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-hash me-2"></i>Id Empresa
                                </th>
                                <td class="pe-4">
                                    ${d.id_empresa || '<span class="text-muted fst-italic">No tiene id empresa</span>'}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-building me-2"></i>Nombre Comercial
                                </th>
                                <td class="pe-4">
                                    ${d.nombre_comercial_empresa || '<span class="text-muted fst-italic">No tiene nombre comercial</span>'}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-geo-alt me-2"></i>Dirección Fiscal
                                </th>
                                <td class="pe-4">
                                    ${d.direccion_fiscal_empresa || '<span class="text-muted fst-italic">No tiene dirección</span>'}
                                    <br>
                                    ${d.cp_fiscal_empresa} - ${d.poblacion_fiscal_empresa}, ${d.provincia_fiscal_empresa}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-telephone me-2"></i>Contacto
                                </th>
                                <td class="pe-4">
                                    Tel: ${d.telefono_empresa || 'N/A'}
                                    <br>
                                    Email: ${d.email_empresa || 'N/A'}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-file-earmark-text me-2"></i>Series
                                </th>
                                <td class="pe-4">
                                    Presupuesto: ${d.serie_presupuesto_empresa} (${d.numero_actual_presupuesto_empresa})
                                    <br>
                                    Factura: ${d.serie_factura_empresa} (${d.numero_actual_factura_empresa})
                                    <br>
                                    Abono: ${d.serie_abono_empresa} (${d.numero_actual_abono_empresa})
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-shield-check me-2"></i>VeriFact
                                </th>
                                <td class="pe-4">
                                    ${d.verifactu_activo_empresa == 1 ? '<span class="badge bg-success">ACTIVO</span>' : '<span class="badge bg-secondary">INACTIVO</span>'}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-calendar-plus me-2"></i>Empresa creada el:
                                </th>
                                <td class="pe-4">
                                    ${d.created_at_empresa ? formatoFechaEuropeo(d.created_at_empresa) : '<span class="text-muted fst-italic">Sin fecha</span>'}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-calendar-plus me-2"></i>Empresa actualizada el:
                                </th>
                                <td class="pe-4">
                                  ${d.updated_at_empresa ? formatoFechaEuropeo(d.updated_at_empresa) : '<span class="text-muted fst-italic">Sin fecha</span>'}
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

    /////////////////////////////////////
    //   INICIO ZONA DELETE EMPRESAS  //
    ///////////////////////////////////
    function desacEmpresa(id) {
        Swal.fire({
            title: 'Desactivar',
            html: `¿Desea desactivar la empresa con ID ${id}?<br><br><small class="text-warning"><i class="bi bi-exclamation-triangle me-1"></i>Esto desactivará esta empresa en el sistema</small>`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Si',
            cancelButtonText: 'No',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                $.post("../../controller/empresas.php?op=eliminar", { id_empresa: id }, function (data) {

                    $table.DataTable().ajax.reload();

                    Swal.fire(
                        'Desactivado',
                        'La empresa ha sido desactivada',
                        'success'
                    )
                });
            }
        })
    }


    $(document).on('click', '.desacEmpresa', function (event) {
        event.preventDefault();
        let id = $(this).data('id_empresa');
        desacEmpresa(id);
    });
    ////////////////////////////////////
    //   FIN ZONA DELETE EMPRESA    //
    //////////////////////////////////

    ///////////////////////////////////////
    //   INICIO ZONA ACTIVAR EMPRESA  //
    /////////////////////////////////////
    function activarEmpresa(id) {
        Swal.fire({
            title: 'Activar',
            text: `¿Desea activar la empresa con ID ${id}?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Si',
            cancelButtonText: 'No',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                $.post("../../controller/empresas.php?op=activar", { id_empresa: id }, function (data) {

                    $table.DataTable().ajax.reload();

                    Swal.fire(
                        'Activado',
                        'La empresa ha sido activada',
                        'success'
                    )
                });
            }
        })
    }


    $(document).on('click', '.activarEmpresa', function (event) {
        event.preventDefault();
        let id = $(this).data('id_empresa');
        console.log("id empresa:",id);
        
        activarEmpresa(id);
    });
    ////////////////////////////////////
    //   FIN ZONA ACTIVAR EMPRESA    //
    //////////////////////////////////

    ///////////////////////////////////////
    //      INICIO ZONA EDITAR           //
    //        BOTON DE EDITAR           //
    /////////////////////////////////////
    $(document).on('click', '.editarEmpresa', function (event) {
        event.preventDefault();
        
        let id = $(this).data('id_empresa');
        console.log("id empresa:", id);
        
        window.location.href = `formularioEmpresa.php?modo=editar&id=${id}`;
    });
    ///////////////////////////////////////
    //        FIN ZONA EDITAR           //
    /////////////////////////////////////


    ////////////////////////////////////////////////////////
    //        ZONA FILTROS RADIOBUTTON CABECERA           //
    ///////////////////////////////////////////////////////
    $('input[name="filterStatus"]').on('change', function () {
        var value = $(this).val();

        if (value === "all") {
            table_e.column(6).search("").draw();
        } else {
            table_e.column(6).search(value).draw();
        }
    });
    ////////////////////////////////////////////////////////////
    //        FIN ZONA FILTROS RADIOBUTTON CABECERA          //
    //////////////////////////////////////////////////////////

    /************************************************************/
    /* A PARTIR DE AQUI NO TOCAR  SE ACTUALIZA AUTOMATICAMENTE */
    /*************************************************************/

    $columnFilterInputs.on('keyup change', function () {
        var columnIndex = table_e.column($(this).closest('th')).index();
        var searchValue = $(this).val();

        table_e.column(columnIndex).search(searchValue).draw();

        updateFilterMessage();
    });

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

    $('#clear-filter').on('click', function () {
        table_e.destroy();

        $columnFilterInputs.each(function () {
            $(this).val('');
        });

        table_e = $table.DataTable($tableConfig);

        $('#filter-alert').hide();
    });
    ////////////////////////////////////////////
    //  FIN ZONA FILTROS PIES y SEARCH     //
    ///////////////////////////////////////////
});

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
