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
  // INICIO DE LA TABLA DE DOCUMENTOS //
  //         DATATABLES             //
  ///////////////////////////////////
  var datatable_documentosConfig = {
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
      },
      {
        name: "id_documento",
        data: "id_documento",
        visible: false,
        className: "text-center",
      },
      {
        name: "titulo_documento",
        data: "titulo_documento",
        className: "text-center",
      },
      {
        name: "nombre_tipo_documento",
        data: "nombre_tipo_documento",
        visible: false,
        className: "text-center",
      },
      {
        name: "fecha_publicacion_documento",
        data: "fecha_publicacion_documento",
        className: "text-center",
      },
      {
        name: "ruta_documento",
        data: null,
        className: "text-center",
      }
    ],
    columnDefs: [
      {
        targets: "control:name",
        width: "5%",
        searchable: false,
        orderable: false,
        className: "text-center",
      },
      {
        targets: "id_documento:name",
        width: "5%",
        searchable: false,
        orderable: false,
        className: "text-center",
      },
      {
        targets: "titulo_documento:name",
        width: "35%",
        searchable: true,
        orderable: true,
        className: "text-center",
      },
      {
        targets: "nombre_tipo_documento:name",
        width: "15%",
        searchable: true,
        orderable: true,
        className: "text-center",
      },
      {
        targets: "fecha_publicacion_documento:name",
        width: "15%",
        searchable: true,
        orderable: true,
        className: "text-center",
        render: function (data, type, row) {
          if (type === "display" && row.fecha_publicacion_documento) {
            return formatoFechaEuropeo(row.fecha_publicacion_documento);
          }
          return row.fecha_publicacion_documento || '<span class="text-muted fst-italic">Sin fecha</span>';
        },
      },
      {
        targets: "ruta_documento:name",
        width: "25%",
        orderable: false,
        searchable: false,
        className: "text-center",
        render: function (data, type, row) {
          if (type === "display" && row.ruta_documento) {
            const rutaArchivo = "../../public/img/documentos/" + row.ruta_documento;
            return `
              <div class="d-flex justify-content-center align-items-center gap-2">
                <a href="${rutaArchivo}" target="_blank" class="btn btn-sm btn-outline-primary" title="Ver documento">
                  <i class="bi bi-file-earmark-text"></i>
                </a>
                <button class="btn btn-sm btn-outline-success descargarDocumento" 
                        data-archivo="${row.ruta_documento}" 
                        title="Descargar documento">
                  <i class="bi bi-download"></i>
                </button>
                <span class="small text-muted">${row.ruta_documento}</span>
              </div>
            `;
          }
          return '<span class="text-muted fst-italic">Sin archivo</span>';
        },
      }
    ],
    order: [[3, 'asc']], // Ordenar por tipo de documento
    ajax: {
      url: "../../controller/documento.php?op=listar",
      type: "GET",
      dataSrc: function (json) {
        console.log("JSON recibido:", json);
        // Filtrar solo documentos activos para técnicos
        if (json.data && Array.isArray(json.data)) {
          return json.data.filter(function(doc) {
            return doc.activo_documento == 1;
          });
        }
        return json.data || json;
      },
    },
    drawCallback: function (settings) {
      var api = this.api();
      var rows = api.rows({ page: 'current' }).nodes();
      var last = null;

      api.column(3, { page: 'current' })
        .data()
        .each(function (group, i) {
          if (last !== group) {
            $(rows)
              .eq(i)
              .before(
                '<tr class="group"><td colspan="6" class="text-start fw-bold bg-primary text-white py-2 px-3"><i class="bi bi-folder-fill me-2"></i>' +
                  group +
                  '</td></tr>'
              );
            last = group;
          }
        });
    },
  };

  /************************************/
  //     ZONA DE DEFINICIONES        //
  /**********************************/
  var $table = $("#documentos_data");
  var $tableConfig = datatable_documentosConfig;
  var $tableBody = $("#documentos_data tbody");
  var $columnFilterInputs = $("#documentos_data tfoot input, #documentos_data tfoot select");

  var table_e = $table.DataTable($tableConfig);

  /************************************/
  //   FIN ZONA DE DEFINICIONES      //
  /**********************************/

  // Función para mostrar detalles expandibles del documento
  function format(d) {
    console.log(d);

    return `
            <div class="card border-primary mb-3" style="overflow: visible;">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-file-earmark-text-fill fs-3 me-2"></i>
                        <h5 class="card-title mb-0">Detalles del Documento</h5>
                    </div>
                </div>
                <div class="card-body p-0" style="overflow: visible;">
                    <table class="table table-borderless table-striped table-hover mb-0">
                        <tbody>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-chat-left-text me-2"></i>Descripción
                                </th>
                                <td class="pe-4" style="max-width: 400px; word-wrap: break-word; white-space: pre-wrap; overflow-wrap: break-word;">
                                    ${
                                      d.descripcion_documento
                                        ? `<div class="alert alert-info py-2 mb-0">
                                            <i class="bi bi-info-circle me-2"></i>${d.descripcion_documento}
                                           </div>`
                                        : '<span class="text-muted fst-italic">Sin descripción</span>'
                                    }
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-calendar-plus me-2"></i>Documento creado el:
                                </th>
                                <td class="pe-4">
                                    ${
                                      d.fecha_creacion_documento
                                        ? formatoFechaEuropeo(d.fecha_creacion_documento)
                                        : '<span class="text-muted fst-italic">Sin fecha</span>'
                                    }
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-calendar-check me-2"></i>Documento actualizado el:
                                </th>
                                <td class="pe-4">
                                  ${
                                    d.fecha_modificacion_documento
                                      ? formatoFechaEuropeo(d.fecha_modificacion_documento)
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

  ///////////////////////////////////////
  //   INICIO ZONA DESCARGAR DOCUMENTO  //
  /////////////////////////////////////
  $(document).on("click", ".descargarDocumento", function (event) {
    event.preventDefault();
    let archivo = $(this).data("archivo");
    
    if (archivo) {
      const rutaCompleta = "../../public/img/documentos/" + archivo;
      const link = document.createElement("a");
      link.href = rutaCompleta;
      link.download = archivo;
      link.target = "_blank";
      
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
      
      toastr.success("Descarga iniciada", "Documento descargado", {
        timeOut: 2000,
        positionClass: "toast-bottom-right",
      });
    }
  });

  ////////////////////////////////////////////
  //  INICIO ZONA FILTROS PIES y SEARCH     //
  //    NO ES NECESARIO TOCAR              //
  ///////////////////////////////////////////

  $columnFilterInputs.on("keyup change", function () {
    var columnIndex = table_e.column($(this).closest("th")).index();
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
      $("#filter-alert").show();
    } else {
      $("#filter-alert").hide();
    }
  }

  table_e.on("search.dt", function () {
    updateFilterMessage();
  });

  $("#clear-filter").on("click", function () {
    table_e.destroy();

    $columnFilterInputs.each(function () {
      $(this).val("");
    });

    table_e = $table.DataTable($tableConfig);

    $("#filter-alert").hide();
  });

  ////////////////////////////////////////////
  //      FIN ZONA FILTROS                 //
  ///////////////////////////////////////////

  ////////////////////////////////////////////////
  //           FUNCIONES DE APOYO              //
  //////////////////////////////////////////////

  function formatoFechaEuropeo(fecha) {
    if (!fecha) return "";

    const date = new Date(fecha);

    if (isNaN(date.getTime())) return fecha;

    const day = String(date.getDate()).padStart(2, "0");
    const month = String(date.getMonth() + 1).padStart(2, "0");
    const year = date.getFullYear();

    return `${day}/${month}/${year}`;
  }
});
