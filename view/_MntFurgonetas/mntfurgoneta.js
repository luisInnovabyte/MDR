$(document).ready(function () {
  // Agregar estilos CSS para el modal y el detalle
  if (!document.getElementById("furgoneta-modal-styles")) {
    const style = document.createElement("style");
    style.id = "furgoneta-modal-styles";
    style.textContent = `
            .swal-wide {
                max-width: 90% !important;
                width: auto !important;
            }
            .details-row {
                background-color: #f8f9fa !important;
            }
            .details-row td {
                padding: 20px !important;
            }
        `;
    document.head.appendChild(style);
  }

  // Función para recargar estadísticas
  function recargarEstadisticas() {
    $.ajax({
      url: "../../controller/furgoneta.php?op=estadisticas",
      type: "GET",
      dataType: "json",
      success: function(response) {
        if (response.success) {
          // Actualizar los valores en las tarjetas
          $(".card.border-primary h2").text(response.data.total);
          $(".card.border-success h2").text(response.data.activas);
          $(".card.border-info h2").text(response.data.operativas);
          $(".card.border-warning h2").text(response.data.taller);
        }
      },
      error: function(xhr, status, error) {
        console.error("Error al recargar estadísticas:", error);
      }
    });
  }

  /////////////////////////////////////
  // INICIO DE LA TABLA DE FURGONETAS //
  //         DATATABLES             //
  ///////////////////////////////////
  var datatable_furgonetasConfig = {
    processing: true,
    layout: {
      bottomEnd: {
        paging: {
          firstLast: true,
          numbers: false,
          previousNext: true,
        },
      },
    },
    language: {
      paginate: {
        first: '<i class="bi bi-chevron-double-left"></i>',
        last: '<i class="bi bi-chevron-double-right"></i>',
        previous: '<i class="bi bi-chevron-compact-left"></i>',
        next: '<i class="bi bi-chevron-compact-right"></i>',
      },
    },
    columns: [
      {
        name: "control",
        data: null,
        defaultContent: "",
        className: "details-control sorting_1 text-center",
      }, // Columna 0: Mostrar más
      {
        name: "id_furgoneta",
        data: "id_furgoneta",
        visible: false,
        className: "text-center",
      }, // Columna 1: ID_FURGONETA
      {
        name: "matricula_furgoneta",
        data: "matricula_furgoneta",
        className: "text-center align-middle",
      }, // Columna 2: MATRICULA
      {
        name: "marca_furgoneta",
        data: "marca_furgoneta",
        className: "text-center align-middle",
      }, // Columna 3: MARCA
      {
        name: "modelo_furgoneta",
        data: "modelo_furgoneta",
        className: "text-center align-middle",
      }, // Columna 4: MODELO
      {
        name: "anio_furgoneta",
        data: "anio_furgoneta",
        className: "text-center align-middle",
      }, // Columna 5: AÑO
      {
        name: "estado_furgoneta",
        data: "estado_furgoneta",
        className: "text-center align-middle",
      }, // Columna 6: ESTADO
      {
        name: "activo_furgoneta",
        data: "activo_furgoneta",
        className: "text-center align-middle",
      }, // Columna 7: ACTIVO
      { name: "activar", data: null, className: "text-center align-middle" }, // Columna 8: ACTIVAR/DESACTIVAR
      {
        name: "editar",
        data: null,
        defaultContent: "",
        className: "text-center align-middle",
      }, // Columna 9: EDITAR
      {
        name: "kilometraje",
        data: null,
        defaultContent: "",
        className: "text-center align-middle",
      }, // Columna 10: KILOMETRAJE
      {
        name: "mantenimiento",
        data: null,
        defaultContent: "",
        className: "text-center align-middle",
      }, // Columna 11: MANTENIMIENTO
    ],
    columnDefs: [
      // Columna 0: BOTÓN MÁS
      {
        targets: "control:name",
        width: "3%",
        searchable: false,
        orderable: false,
        className: "text-center",
      },
      // Columna 1: id_furgoneta
      {
        targets: "id_furgoneta:name",
        width: "5%",
        searchable: false,
        orderable: false,
        className: "text-center",
      },
      // Columna 2: matricula_furgoneta
      {
        targets: "matricula_furgoneta:name",
        width: "10%",
        searchable: true,
        orderable: true,
        className: "text-center",
        render: function (data, type, row) {
          if (type === "display") {
            return `<strong>${data}</strong>`;
          }
          return data;
        },
      },
      // Columna 3: marca_furgoneta
      {
        targets: "marca_furgoneta:name",
        width: "12%",
        searchable: true,
        orderable: true,
        className: "text-center",
      },
      // Columna 4: modelo_furgoneta
      {
        targets: "modelo_furgoneta:name",
        width: "15%",
        searchable: true,
        orderable: true,
        className: "text-center",
      },
      // Columna 5: anio_furgoneta
      {
        targets: "anio_furgoneta:name",
        width: "8%",
        orderable: true,
        searchable: true,
        className: "text-center",
        render: function (data, type, row) {
          if (type === "display") {
            return data ? `<span class="badge bg-secondary">${data}</span>` : '<span class="text-muted">-</span>';
          }
          return data;
        },
      },
      // Columna 6: estado_furgoneta
      {
        targets: "estado_furgoneta:name",
        width: "10%",
        orderable: true,
        searchable: true,
        className: "text-center",
        render: function (data, type, row) {
          if (type === "display") {
            switch(data) {
              case 'operativa':
                return '<span class="badge bg-success"><i class="fa fa-check-circle me-1"></i>Operativa</span>';
              case 'taller':
                return '<span class="badge bg-warning text-dark"><i class="fa fa-wrench me-1"></i>Taller</span>';
              case 'averiada':
                return '<span class="badge bg-danger"><i class="fa fa-exclamation-triangle me-1"></i>Averiada</span>';
              case 'baja':
                return '<span class="badge bg-secondary"><i class="fa fa-ban me-1"></i>Baja</span>';
              default:
                return '<span class="text-muted">-</span>';
            }
          }
          return data;
        },
      },
      // Columna 7: activo_furgoneta (Estado)
      {
        targets: "activo_furgoneta:name",
        width: "8%",
        orderable: true,
        searchable: true,
        className: "text-center",
        render: function (data, type, row) {
          if (type === "display") {
            return row.activo_furgoneta == 1
              ? '<i class="bi bi-check-circle text-success fa-2x"></i>'
              : '<i class="bi bi-x-circle text-danger fa-2x"></i>';
          }
          return row.activo_furgoneta;
        },
      },
      // Columna 8: BOTON PARA ACTIVAR/DESACTIVAR ESTADO
      {
        targets: "activar:name",
        width: "8%",
        searchable: false,
        orderable: false,
        class: "text-center",
        render: function (data, type, row) {
          if (row.activo_furgoneta == 1) {
            return `<button type="button" class="btn btn-danger btn-sm desacFurgoneta" data-bs-toggle="tooltip-primary" data-placement="top" title="Desactivar" data-original-title="Tooltip on top" 
                             data-id_furgoneta="${row.id_furgoneta}"> 
                             <i class="fa-solid fa-trash"></i>
                             </button>`;
          } else {
            return `<button class="btn btn-success btn-sm activarFurgoneta" data-bs-toggle="tooltip-primary" data-placement="top" title="Activar" data-original-title="Tooltip on top" 
                             data-id_furgoneta="${row.id_furgoneta}">
                             <i class="bi bi-hand-thumbs-up-fill"></i>
                            </button>`;
          }
        },
      },
      // Columna 9: BOTON PARA EDITAR FURGONETA
      {
        targets: "editar:name",
        width: "8%",
        searchable: false,
        orderable: false,
        class: "text-center",
        render: function (data, type, row) {
          return `<button type="button" class="btn btn-info btn-sm editarFurgoneta" data-toggle="tooltip-primary" data-placement="top" title="Editar"  
                             data-id_furgoneta="${row.id_furgoneta}"> 
                             <i class="fa-solid fa-edit"></i>
                             </button>`;
        },
      },
      // Columna 10: BOTON PARA VER KILOMETRAJE
      {
        targets: "kilometraje:name",
        width: "8%",
        searchable: false,
        orderable: false,
        class: "text-center",
        render: function (data, type, row) {
          return `<button type="button" class="btn btn-primary btn-sm verKilometraje" data-toggle="tooltip-primary" data-placement="top" title="Ver Kilometraje"  
                             data-id_furgoneta="${row.id_furgoneta}"> 
                             <i class="fa fa-tachometer-alt"></i>
                             </button>`;
        },
      },
      // Columna 11: BOTON PARA VER MANTENIMIENTO
      {
        targets: "mantenimiento:name",
        width: "8%",
        searchable: false,
        orderable: false,
        class: "text-center",
        render: function (data, type, row) {
          return `<button type="button" class="btn btn-secondary btn-sm verMantenimiento" data-toggle="tooltip-primary" data-placement="top" title="Ver Mantenimiento"  
                             data-id_furgoneta="${row.id_furgoneta}"> 
                             <i class="fa fa-wrench"></i>
                             </button>`;
        },
      },
    ],
    ajax: {
      url: "../../controller/furgoneta.php?op=listar",
      type: "GET",
      dataSrc: function (json) {
        console.log("JSON recibido:", json);
        return json.data || json;
      },
    },
    order: [[2, 'asc']], // Ordenar por columna de matrícula (índice 2)
  };
  ////////////////////////////
  // FIN DE LA TABLA DE FURGONETAS //
  ///////////////////////////

  /************************************/
  //     ZONA DE DEFINICIONES        //
  /**********************************/
  var $table = $("#furgonetas_data");
  var $tableConfig = datatable_furgonetasConfig;
  var $tableBody = $("#furgonetas_data tbody");
  var $columnFilterInputs = $(
    "#furgonetas_data tfoot input, #furgonetas_data tfoot select"
  );

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
                        <i class="fa fa-truck fs-3 me-2"></i>
                        <h5 class="card-title mb-0">Detalles de la Furgoneta</h5>
                    </div>
                </div>
                <div class="card-body p-0" style="overflow: visible;">
                    <table class="table table-borderless table-striped table-hover mb-0">
                        <tbody>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="fa fa-hashtag me-2"></i>ID Furgoneta
                                </th>
                                <td class="pe-4">
                                    ${d.id_furgoneta || '<span class="text-muted fst-italic">No tiene ID</span>'}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="fa fa-id-card me-2"></i>Matrícula
                                </th>
                                <td class="pe-4">
                                    <strong class="text-primary fs-5">${d.matricula_furgoneta || '<span class="text-muted fst-italic">Sin matrícula</span>'}</strong>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="fa fa-truck me-2"></i>Marca y Modelo
                                </th>
                                <td class="pe-4">
                                    ${d.marca_furgoneta || 'Sin marca'} - ${d.modelo_furgoneta || 'Sin modelo'}
                                    ${d.anio_furgoneta ? `<span class="badge bg-secondary ms-2">${d.anio_furgoneta}</span>` : ''}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="fa fa-barcode me-2"></i>Número de Bastidor
                                </th>
                                <td class="pe-4">
                                    ${d.numero_bastidor_furgoneta || '<span class="text-muted fst-italic">Sin bastidor</span>'}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="fa fa-clipboard-check me-2"></i>Fecha Próxima ITV
                                </th>
                                <td class="pe-4">
                                    ${d.fecha_proxima_itv_furgoneta 
                                        ? formatoFechaEuropeo(d.fecha_proxima_itv_furgoneta) 
                                        : '<span class="text-muted fst-italic">Sin fecha ITV</span>'}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="fa fa-shield-alt me-2"></i>Seguro
                                </th>
                                <td class="pe-4">
                                    ${d.compania_seguro_furgoneta 
                                        ? `<div>
                                            <strong>${d.compania_seguro_furgoneta}</strong><br>
                                            <small>Póliza: ${d.numero_poliza_seguro_furgoneta || 'N/A'}</small><br>
                                            <small>Vencimiento: ${d.fecha_vencimiento_seguro_furgoneta ? formatoFechaEuropeo(d.fecha_vencimiento_seguro_furgoneta) : 'N/A'}</small>
                                           </div>`
                                        : '<span class="text-muted fst-italic">Sin datos de seguro</span>'}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="fa fa-weight-hanging me-2"></i>Capacidad de Carga
                                </th>
                                <td class="pe-4">
                                    <span class="badge bg-info me-2">${d.capacidad_carga_kg_furgoneta || '0'} kg</span>
                                    <span class="badge bg-info">${d.capacidad_carga_m3_furgoneta || '0'} m³</span>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="fa fa-gas-pump me-2"></i>Combustible
                                </th>
                                <td class="pe-4">
                                    ${d.tipo_combustible_furgoneta || '<span class="text-muted">Sin tipo</span>'}
                                    ${d.consumo_medio_furgoneta ? `<span class="badge bg-warning ms-2">${d.consumo_medio_furgoneta} L/100km</span>` : ''}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="fa fa-tools me-2"></i>Taller Habitual
                                </th>
                                <td class="pe-4">
                                    ${d.taller_habitual_furgoneta 
                                        ? `<div>
                                            <strong>${d.taller_habitual_furgoneta}</strong><br>
                                            ${d.telefono_taller_furgoneta ? `<small><i class="fa fa-phone me-1"></i>${d.telefono_taller_furgoneta}</small>` : ''}
                                           </div>`
                                        : '<span class="text-muted fst-italic">Sin taller asignado</span>'}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="fa fa-wrench me-2"></i>Km entre Revisiones
                                </th>
                                <td class="pe-4">
                                    ${d.kilometros_entre_revisiones_furgoneta 
                                        ? `<span class="badge bg-secondary">${d.kilometros_entre_revisiones_furgoneta} km</span>` 
                                        : '<span class="text-muted fst-italic">No configurado</span>'}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="fa fa-info-circle me-2"></i>Estado
                                </th>
                                <td class="pe-4">
                                    ${d.estado_furgoneta === 'operativa' 
                                        ? '<span class="badge bg-success"><i class="fa fa-check-circle me-1"></i>Operativa</span>'
                                        : d.estado_furgoneta === 'taller'
                                            ? '<span class="badge bg-warning text-dark"><i class="fa fa-wrench me-1"></i>En Taller</span>'
                                            : d.estado_furgoneta === 'averiada'
                                                ? '<span class="badge bg-danger"><i class="fa fa-exclamation-triangle me-1"></i>Averiada</span>'
                                                : '<span class="badge bg-secondary"><i class="fa fa-ban me-1"></i>Baja</span>'
                                    }
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="fa fa-comment-alt me-2"></i>Observaciones
                                </th>
                                <td class="pe-4" style="max-width: 400px; word-wrap: break-word; white-space: pre-wrap; overflow-wrap: break-word;">
                                    ${d.observaciones_furgoneta || '<span class="text-muted fst-italic">Sin observaciones</span>'}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="fa fa-calendar-plus me-2"></i>Creada el:
                                </th>
                                <td class="pe-4">
                                    ${d.created_at_furgoneta
                                        ? formatoFechaEuropeo(d.created_at_furgoneta)
                                        : '<span class="text-muted fst-italic">Sin fecha</span>'
                                    }
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="fa fa-calendar-check me-2"></i>Actualizada el:
                                </th>
                                <td class="pe-4">
                                  ${d.updated_at_furgoneta
                                      ? formatoFechaEuropeo(d.updated_at_furgoneta)
                                      : '<span class="text-muted fst-italic">Sin fecha</span>'
                                  }
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
  $tableBody.on("click", "td.details-control", function () {
    var tr = $(this).closest("tr");
    var row = table_e.row(tr);

    if (row.child.isShown()) {
      row.child.hide();
      tr.removeClass("shown");
    } else {
      row.child(format(row.data())).show();
      tr.addClass("shown");
    }
  });

  ////////////////////////////////////////////
  //   INICIO ZONA FUNCIONES DE APOYO      //
  //////////////////////////////////////////

  /////////////////////////////////////
  //   INICIO ZONA DELETE FURGONETAS  //
  ///////////////////////////////////
  function desacFurgoneta(id) {
    Swal.fire({
      title: "Desactivar",
      text: `¿Desea desactivar la furgoneta con ID ${id}?`,
      icon: "question",
      showCancelButton: true,
      confirmButtonText: "Sí",
      cancelButtonText: "No",
      reverseButtons: true,
    }).then((result) => {
      if (result.isConfirmed) {
        $.post(
          "../../controller/furgoneta.php?op=eliminar",
          { id_furgoneta: id },
          function (data) {
            $table.DataTable().ajax.reload();
            recargarEstadisticas();

            Swal.fire(
              "Desactivada",
              "La furgoneta ha sido desactivada",
              "success"
            );
          }
        );
      }
    });
  }

  // CAPTURAR EL CLICK EN EL BOTÓN DE BORRAR
  $(document).on("click", ".desacFurgoneta", function (event) {
    event.preventDefault();
    let id = $(this).data("id_furgoneta");
    desacFurgoneta(id);
  });
  ////////////////////////////////////
  //   FIN ZONA DELETE FURGONETA    //
  //////////////////////////////////

  ///////////////////////////////////////
  //   INICIO ZONA ACTIVAR FURGONETA  //
  /////////////////////////////////////
  function activarFurgoneta(id) {
    Swal.fire({
      title: "Activar",
      text: `¿Desea activar la furgoneta con ID ${id}?`,
      icon: "question",
      showCancelButton: true,
      confirmButtonText: "Sí",
      cancelButtonText: "No",
      reverseButtons: true,
    }).then((result) => {
      if (result.isConfirmed) {
        $.post(
          "../../controller/furgoneta.php?op=activar",
          { id_furgoneta: id },
          function (data) {
            $table.DataTable().ajax.reload();
            recargarEstadisticas();

            Swal.fire("Activada", "La furgoneta ha sido activada", "success");
          }
        );
      }
    });
  }

  // CAPTURAR EL CLICK EN EL BOTÓN DE ACTIVAR
  $(document).on("click", ".activarFurgoneta", function (event) {
    event.preventDefault();
    let id = $(this).data("id_furgoneta");
    console.log("id furgoneta:", id);

    activarFurgoneta(id);
  });
  ////////////////////////////////////
  //   FIN ZONA ACTIVAR FURGONETA    //
  //////////////////////////////////

  ///////////////////////////////////////
  //      INICIO ZONA EDITAR           //
  //        BOTON DE EDITAR           //
  /////////////////////////////////////
  // CAPTURAR EL CLICK EN EL BOTÓN DE EDITAR
  $(document).on("click", ".editarFurgoneta", function (event) {
    event.preventDefault();

    let id = $(this).data("id_furgoneta");
    console.log("id furgoneta:", id);

    // Redirigir al formulario independiente en modo edición
    window.location.href = `formularioFurgoneta.php?modo=editar&id=${id}`;
  });
  ///////////////////////////////////////
  //        FIN ZONA EDITAR           //
  /////////////////////////////////////

  ///////////////////////////////////////
  //   INICIO ZONA VER KILOMETRAJE     //
  /////////////////////////////////////
  // CAPTURAR EL CLICK EN EL BOTÓN DE VER KILOMETRAJE
  $(document).on("click", ".verKilometraje", function (event) {
    event.preventDefault();

    let id_furgoneta = $(this).data("id_furgoneta");
    console.log("Ver kilometraje de la furgoneta:", id_furgoneta);

    // Redirigir a la tabla de kilometraje filtrada por furgoneta
    window.location.href = `../MntKilometraje/index.php?id_furgoneta=${id_furgoneta}`;
  });
  ///////////////////////////////////////
  //     FIN ZONA VER KILOMETRAJE      //
  /////////////////////////////////////

  ///////////////////////////////////////
  //   INICIO ZONA VER MANTENIMIENTO   //
  /////////////////////////////////////
  // CAPTURAR EL CLICK EN EL BOTÓN DE VER MANTENIMIENTO
  $(document).on("click", ".verMantenimiento", function (event) {
    event.preventDefault();

    let id_furgoneta = $(this).data("id_furgoneta");
    console.log("Ver mantenimiento de la furgoneta:", id_furgoneta);

    // Redirigir a la tabla de mantenimiento filtrada por furgoneta
    window.location.href = `../MntMantenimiento/index.php?id_furgoneta=${id_furgoneta}`;
  });
  ///////////////////////////////////////
  //   FIN ZONA VER MANTENIMIENTO      //
  /////////////////////////////////////

  /******************************************************* */
  /* A PARTIR DE AQUI NO TOCAR  SE ACTUALIZA AUTOMATICAMENTE */
  /******************************************************** */

  /////////////////////////////////////
  //  INICIO ZONA FILTROS PIES y SEARCH     //
  //    NO ES NECESARIO TOCAR              //
  //     FUNCIONES NO TOCAR               //
  ///////////////////////////////////////////

  // Filtro de cada columna en el pie de la tabla
  $columnFilterInputs.on("keyup change", function () {
    var columnIndex = table_e.column($(this).closest("th")).index();
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
      $("#filter-alert").show();
    } else {
      $("#filter-alert").hide();
    }
  }

  table_e.on("search.dt", function () {
    updateFilterMessage();
  });

  // Botón para limpiar los filtros
  $("#clear-filter").on("click", function () {
    table_e.destroy();

    $columnFilterInputs.each(function () {
      $(this).val("");
    });

    table_e = $table.DataTable($tableConfig);

    $("#filter-alert").hide();
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
