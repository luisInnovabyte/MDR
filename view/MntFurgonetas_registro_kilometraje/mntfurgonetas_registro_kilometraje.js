// =====================================================
// DATATABLES - Registro de Kilometraje
// =====================================================

$(document).ready(function () {
    // Agregar estilos CSS para mejorar la visualización
    if (!document.getElementById("kilometraje-styles")) {
        const style = document.createElement("style");
        style.id = "kilometraje-styles";
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
    // INICIO DE LA TABLA DE KILOMETRAJE //
    //         DATATABLES             //
    ///////////////////////////////////
    var datatable_kilometrajeConfig = {
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
            emptyTable: "No hay registros de kilometraje para esta furgoneta",
            info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
            infoEmpty: "Mostrando 0 a 0 de 0 registros",
            infoFiltered: "(filtrado de _MAX_ registros totales)",
            lengthMenu: "Mostrar _MENU_ registros por página",
            loadingRecords: "Cargando...",
            processing: "Procesando...",
            search: "Buscar:",
            zeroRecords: "No se encontraron registros que coincidan con la búsqueda",
            paginate: {
                first: '<i class="bi bi-chevron-double-left"></i>',
                last: '<i class="bi bi-chevron-double-right"></i>',
                previous: '<i class="bi bi-chevron-compact-left"></i>',
                next: '<i class="bi bi-chevron-compact-right"></i>'
            }
        },
        columns: [
            { name: 'control', data: null, defaultContent: '', className: 'details-control sorting_1 text-center' },
            { name: 'id_registro_km', data: 'id_registro_km', visible: false, className: "text-center" },
            { name: 'fecha_registro_km', data: 'fecha_registro_km', className: "text-center align-middle" },
            { name: 'kilometraje_registrado_km', data: 'kilometraje_registrado_km', className: "text-center align-middle" },
            { name: 'km_recorridos', data: 'km_recorridos', className: "text-center align-middle" },
            { name: 'dias_transcurridos', data: 'dias_transcurridos', className: "text-center align-middle" },
            { name: 'km_promedio_diario', data: 'km_promedio_diario', className: "text-center align-middle" },
            { name: 'tipo_registro_km', data: 'tipo_registro_km', className: "text-center align-middle" },
            { name: 'observaciones_registro_km', data: 'observaciones_registro_km', className: "text-left align-middle" },
            { name: 'created_at_registro_km', data: 'created_at_registro_km', className: "text-center align-middle" }
        ],
        columnDefs: [
            { targets: "control:name", width: '5%', searchable: false, orderable: false, className: "text-center"},
            { targets: "id_registro_km:name", width: '5%', searchable: false, orderable: false, className: "text-center" },
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
            { 
                targets: "km_recorridos:name", 
                width: '10%', 
                searchable: false, 
                orderable: true, 
                className: "text-center",
                render: function (data, type, row) {
                    if (type === "display") {
                        if (!data || data == 0) return '-';
                        const km = Number(data);
                        const badge = km > 0 ? 'bg-success' : 'bg-secondary';
                        return '<span class="badge ' + badge + '">' + km.toLocaleString('es-ES') + ' km</span>';
                    }
                    return data;
                }
            },
            { 
                targets: "dias_transcurridos:name", 
                width: '8%', 
                searchable: false, 
                orderable: true, 
                className: "text-center",
                render: function (data, type, row) {
                    if (type === "display") {
                        if (!data || data == 0) return '-';
                        return Number(data) + ' días';
                    }
                    return data;
                }
            },
            { 
                targets: "km_promedio_diario:name", 
                width: '10%', 
                searchable: false, 
                orderable: true, 
                className: "text-center",
                render: function (data, type, row) {
                    if (type === "display") {
                        if (!data || data == 0) return '-';
                        return Number(data).toFixed(1) + ' km/día';
                    }
                    return data;
                }
            },
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
            }
        ],
        ajax: {
            url: '../../controller/furgoneta_registro_kilometraje.php?op=listar_por_furgoneta',
            type: 'GET',
            data: function() {
                console.log('Enviando id_furgoneta:', idFurgoneta);
                return {
                    id_furgoneta: idFurgoneta
                };
            },
            dataSrc: function(json) {
                console.log('Datos recibidos del servidor:', json);
                console.log('Número de registros:', json.data ? json.data.length : 0);
                return json.data;
            },
            error: function (xhr, error, thrown) {
                console.error('Error en DataTable:', error);
                console.error('XHR:', xhr);
                console.error('Thrown:', thrown);
                console.error('Response Text:', xhr.responseText);
                Swal.fire({
                    icon: 'error',
                    title: 'Error al cargar datos',
                    text: 'No se pudieron cargar los registros de kilometraje',
                });
            }
        },
        order: [[2, 'desc']], // Ordenar por fecha descendente (columna 2 ahora, porque 0 es control y 1 es ID)
        responsive: true,
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Todos"]],
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
             '<"row"<"col-sm-12"tr>>' +
             '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>'
    };

    // INICIALIZAR DATATABLE
    var datatableKilometraje = $("#kilometrajes_data").DataTable(datatable_kilometrajeConfig);

    // Botón para nuevo registro
    $('#btnNuevoRegistro').click(function () {
        window.location.href = 'formularioKilometraje.php?id_furgoneta=' + idFurgoneta;
    });
});

// =====================================================
// FUNCIONES AUXILIARES
// =====================================================

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
                $('#id-furgoneta').text(data.id_furgoneta || idFurgoneta);
                document.title = 'Kilometraje - ' + nombreFurgoneta;
            }
        },
        error: function () {
            $('#nombre-furgoneta').text('Error al cargar información');
            $('#id-furgoneta').text(idFurgoneta);
        }
    });
}
