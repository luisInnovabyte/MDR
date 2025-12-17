$(document).ready(function () {

  // Obtener el ID del elemento desde la URL (si existe)
  const urlParams = new URLSearchParams(window.location.search);
  const idElemento = urlParams.get('id_elemento');
  
  // Si hay un id_elemento, mostrar info y ajustar el título
  if (idElemento) {
    cargarInfoElemento(idElemento);
    $('.br-pagetitle h4').text('Documentos del Elemento');
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
  // INICIO DE LA TABLA DE DOCUMENTOS //
  //         DATATABLES             //
  ///////////////////////////////////
  var datatable_documentosConfig = {
    processing: true, // mostrar el procesamiento de la tabla
    layout: {
      bottomEnd: {
        // que elementos de la paginación queremos que aparezcan
        paging: {
          firstLast: true,
          numbers: false,
          previousNext: true,
        },
      },
    }, //
    language: {
      // Se hace para cambiar la paginación por flechas
      paginate: {
        first: '<i class="bi bi-chevron-double-left"></i>', // Ícono de FontAwesome
        last: '<i class="bi bi-chevron-double-right"></i>', // Ícono de FontAwesome
        previous: '<i class="bi bi-chevron-compact-left"></i>', // Ícono de FontAwesome
        next: '<i class="bi bi-chevron-compact-right"></i>', // Ícono de FontAwesome
      },
    }, // de la language
    columns: [
      // Botón para expandir detalles
      {
        name: "control",
        data: null,
        defaultContent: "",
        className: "details-control sorting_1 text-center",
      }, // Columna 0: Mostrar más
      {
        name: "id_documento_elemento",
        data: "id_documento_elemento",
        visible: false,
        className: "text-center",
      }, // Columna 1: ID_DOCUMENTO_ELEMENTO
      {
        name: "descripcion_documento_elemento",
        data: "descripcion_documento_elemento",
        className: "text-center",
      }, // Columna 2: DESCRIPCION_DOCUMENTO_ELEMENTO
      {
        name: "tipo_documento_elemento",
        data: "tipo_documento_elemento",
        className: "text-center",
      }, // Columna 3: TIPO_DOCUMENTO_ELEMENTO
      {
        name: "privado_documento",
        data: "privado_documento",
        className: "text-center",
      }, // Columna 4: PRIVADO
      {
        name: "archivo_documento",
        data: null,
        className: "text-center",
      }, // Columna 5: ARCHIVO
      {
        name: "activo_documento",
        data: "activo_documento",
        className: "text-center",
      }, // Columna 6: ESTADO
      { name: "activar", data: null, className: "text-center" }, // Columna 7: ACTIVAR/DESACTIVAR
      {
        name: "editar",
        data: null,
        defaultContent: "",
        className: "text-center",
      }, // Columna 8: EDITAR
    ], // de las columnas
    columnDefs: [
      // Columna 0: BOTÓN MÁS (expandir)
      {
        targets: "control:name",
        width: "5%",
        searchable: false,
        orderable: false,
        className: "text-center",
      },
      // Columna 1: id_documento_elemento
      {
        targets: "id_documento_elemento:name",
        width: "5%",
        searchable: false,
        orderable: false,
        className: "text-center",
      },
      // Columna 2: descripcion_documento_elemento
      {
        targets: "descripcion_documento_elemento:name",
        width: "20%",
        searchable: true,
        orderable: true,
        className: "text-center",
      },
      // Columna 3: tipo_documento_elemento
      {
        targets: "tipo_documento_elemento:name",
        width: "10%",
        searchable: true,
        orderable: true,
        className: "text-center",
      },
      // Columna 4: privado_documento
      {
        targets: "privado_documento:name",
        width: "8%",
        orderable: true,
        searchable: true,
        className: "text-center",
        render: function (data, type, row) {
          if (type === "display") {
            return row.privado_documento == 1
              ? '<i class="bi bi-lock-fill text-danger fa-2x" title="Documento privado"></i>'
              : '<i class="bi bi-unlock-fill text-success fa-2x" title="Documento público"></i>';
          }
          return row.privado_documento;
        },
      },
      // Columna 5: archivo_documento
      {
        targets: "archivo_documento:name",
        width: "15%",
        orderable: false,
        searchable: false,
        className: "text-center",
        render: function (data, type, row) {
          if (type === "display" && row.archivo_documento) {
            const rutaArchivo = "../../public/img/docs_elementos/" + row.archivo_documento;
            return `
              <div class="d-flex justify-content-center align-items-center gap-2">
                <a href="${rutaArchivo}" target="_blank" class="btn btn-sm btn-outline-primary" title="Ver documento">
                  <i class="bi bi-file-earmark-text"></i>
                </a>
                <button class="btn btn-sm btn-outline-success descargarDocumento" 
                        data-archivo="${row.archivo_documento}" 
                        title="Descargar documento">
                  <i class="bi bi-download"></i>
                </button>
                <span class="small text-muted">${row.archivo_documento}</span>
              </div>
            `;
          }
          return '<span class="text-muted fst-italic">Sin archivo</span>';
        },
      },
      // Columna 6: activo_documento (Estado)
      {
        targets: "activo_documento:name",
        width: "8%",
        orderable: true,
        searchable: true,
        className: "text-center",
        render: function (data, type, row) {
          if (type === "display") {
            return row.activo_documento == 1
              ? '<i class="bi bi-check-circle text-success fa-2x"></i>'
              : '<i class="bi bi-x-circle text-danger fa-2x"></i>';
          }
          return row.activo_documento;
        },
      },
      // Columna 7: BOTON PARA ACTIVAR/DESACTIVAR ESTADO
      {
        targets: "activar:name",
        width: "8%",
        searchable: false,
        orderable: false,
        class: "text-center",
        render: function (data, type, row) {
          if (row.activo_documento == 1) {
            // permito desactivar el documento
            return `<button type="button" class="btn btn-danger btn-sm desacDocumento" data-bs-toggle="tooltip-primary" data-placement="top" title="Desactivar" data-original-title="Tooltip on top" 
                             data-id_documento_elemento="${row.id_documento_elemento}"> 
                             <i class="fa-solid fa-trash"></i>
                             </button>`;
          } else {
            // debo permitir activar de nuevo el documento
            return `<button class="btn btn-success btn-sm activarDocumento" data-bs-toggle="tooltip-primary" data-placement="top" title="Activar" data-original-title="Tooltip on top" 
                             data-id_documento_elemento="${row.id_documento_elemento}">
                             <i class="bi bi-hand-thumbs-up-fill"></i>
                            </button>`;
          }
        }, // de la function
      }, //
      // Columna 8: BOTON PARA EDITAR DOCUMENTO
      {
        targets: "editar:name",
        width: "8%",
        searchable: false,
        orderable: false,
        class: "text-center",
        render: function (data, type, row) {
          // botón editar el documento
          return `<button type="button" class="btn btn-info btn-sm editarDocumento" data-toggle="tooltip-primary" data-placement="top" title="Editar"  
                             data-id_documento_elemento="${row.id_documento_elemento}"> 
                             <i class="fa-solid fa-edit"></i>
                             </button>`;
        }, // de la function
      },
    ], // de la columnDefs
    ajax: {
      url: "../../controller/documento_elemento.php?op=listar",
      type: "GET",
      data: function() {
        // Si hay id_elemento en la URL, enviarlo para filtrar
        if (idElemento) {
          return {
            id_elemento: idElemento
          };
        }
        return {};
      },
      dataSrc: function (json) {
        console.log("JSON recibido:", json); // 📌 Ver qué estructura tiene
        return json.data || json; // Ajusta en función de lo recibido
      },
    }, // del ajax
  }; // de la variable datatable_documentosConfig
  ////////////////////////////
  // FIN DE LA TABLA DE DOCUMENTOS //
  ///////////////////////////

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
                                    <i class="bi bi-chat-left-text me-2"></i>Observaciones
                                </th>
                                <td class="pe-4" style="max-width: 400px; word-wrap: break-word; white-space: pre-wrap; overflow-wrap: break-word;">
                                    ${
                                      d.observaciones_documento
                                        ? `<div class="alert alert-info py-2 mb-0">
                                            <i class="bi bi-info-circle me-2"></i>${d.observaciones_documento}
                                           </div>`
                                        : '<span class="text-muted fst-italic">Sin observaciones</span>'
                                    }
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-calendar-plus me-2"></i>Documento creado el:
                                </th>
                                <td class="pe-4">
                                    ${
                                      d.created_at_documento
                                        ? formatoFechaEuropeo(
                                            d.created_at_documento
                                          )
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
                                    d.updated_at_documento
                                      ? formatoFechaEuropeo(
                                          d.updated_at_documento
                                        )
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
      // Esta fila ya está abierta, la cerramos
      row.child.hide();
      tr.removeClass("shown");
    } else {
      // Abrir esta fila
      row.child(format(row.data())).show();
      tr.addClass("shown");
    }
  });

  ////////////////////////////////////////////
  //   INICIO ZONA FUNCIONES DE APOYO      //
  //////////////////////////////////////////

  /////////////////////////////////////
  //   INICIO ZONA DELETE DOCUMENTOS  //
  ///////////////////////////////////
  function desacDocumento(id) {
    Swal.fire({
      title: "Desactivar",
      html: `¿Desea desactivar el documento con ID ${id}?`,
      icon: "question",
      showCancelButton: true,
      confirmButtonText: "Si",
      cancelButtonText: "No",
      reverseButtons: true,
    }).then((result) => {
      if (result.isConfirmed) {
        $.post(
          "../../controller/documento_elemento.php?op=eliminar",
          { id_documento_elemento: id },
          function (data) {
            $table.DataTable().ajax.reload();

            Swal.fire(
              "Desactivado",
              "El documento ha sido desactivado",
              "success"
            );
          }
        );
      }
    });
  }

  // CAPTURAR EL CLICK EN EL BOTÓN DE BORRAR
  $(document).on("click", ".desacDocumento", function (event) {
    event.preventDefault();
    let id = $(this).data("id_documento_elemento");
    desacDocumento(id);
  });
  ////////////////////////////////////
  //   FIN ZONA DELETE DOCUMENTO    //
  //////////////////////////////////

  ///////////////////////////////////////
  //   INICIO ZONA ACTIVAR DOCUMENTO  //
  /////////////////////////////////////
  function activarDocumento(id) {
    Swal.fire({
      title: "Activar",
      text: `¿Desea activar el documento con ID ${id}?`,
      icon: "question",
      showCancelButton: true,
      confirmButtonText: "Si",
      cancelButtonText: "No",
      reverseButtons: true,
    }).then((result) => {
      if (result.isConfirmed) {
        $.post(
          "../../controller/documento_elemento.php?op=activar",
          { id_documento_elemento: id },
          function (data) {
            $table.DataTable().ajax.reload();

            Swal.fire("Activado", "El documento ha sido activado", "success");
          }
        );
      }
    });
  }

  // CAPTURAR EL CLICK EN EL BOTÓN DE ACTIVAR
  $(document).on("click", ".activarDocumento", function (event) {
    event.preventDefault();
    let id = $(this).data("id_documento_elemento");
    console.log("id documento:", id);

    activarDocumento(id);
  });
  ////////////////////////////////////
  //   FIN ZONA ACTIVAR DOCUMENTO    //
  //////////////////////////////////

  ///////////////////////////////////////
  //   INICIO ZONA DESCARGAR DOCUMENTO  //
  /////////////////////////////////////
  $(document).on("click", ".descargarDocumento", function (event) {
    event.preventDefault();
    let archivo = $(this).data("archivo");
    
    if (archivo) {
      const rutaCompleta = "../../public/img/docs_elementos/" + archivo;
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
  ////////////////////////////////////
  //   FIN ZONA DESCARGAR DOCUMENTO    //
  //////////////////////////////////

  /////////////////////////////////////
  //      INICIO ZONA EDITAR           //
  //        BOTON DE EDITAR           //
  /////////////////////////////////////
  $(document).on("click", ".editarDocumento", function (event) {
    event.preventDefault();

    let id = $(this).data("id_documento_elemento");
    console.log("id documento:", id);

    // Redirigir al formulario independiente en modo edición
    let url = `formularioDocumento.php?modo=editar&id=${id}`;
    
    // Si hay un id_elemento en la URL actual, incluirlo
    if (idElemento) {
      url += `&id_elemento=${idElemento}`;
    }
    
    window.location.href = url;
  });
  ///////////////////////////////////////
  //        FIN ZONA EDITAR           //
  /////////////////////////////////////

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

  ////////////////////////////////////////////
  //  INICIO ZONA FILTROS PIES y SEARCH     //
  //    NO ES NECESARIO TOCAR              //
  //     FUNCIONES NO TOCAR               //
  ///////////////////////////////////////////

  /* IMPORTANTE ---- IMPORTANTE ---- IMPORTANTE ---- IMPORTANTE */
  /* Si algún campo no quiere que se habilite en el footer la busqueda, 
    bastará con poner en el columnDefs -- > searchable: false */

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

  ////////////////////////////////////////////
  //   FUNCIONES DE APOYO ADICIONALES      //
  //////////////////////////////////////////

  // Función para cargar información del elemento
function cargarInfoElemento(id_elemento) {
    $.post("../../controller/elemento.php?op=mostrar", { id_elemento: id_elemento }, function (data) {
        if (data) {
            $('#descripcion-elemento').text(data.descripcion_elemento || 'Sin descripción');
            $('#codigo-elemento').text(data.codigo_elemento || '--');
            $('#id-elemento').text(id_elemento);
            $('#info-elemento').show();

           
        }
    }, 'json').fail(function() {
        console.error('Error al cargar información del elemento');
        $('#info-elemento').hide();
    });
}

$('#btnVolverElementos').on('click', function(e) {
    e.preventDefault();
    const urlParams = new URLSearchParams(window.location.search);
    const idArticulo = urlParams.get('id_articulo');
    const origen = urlParams.get('origen');

    if (origen === 'consulta') {
        window.location.href = '../MntElementos_consulta/index.php';
    } else if (idArticulo) {
        // Redirigir al listado de elementos filtrado por el artículo
        window.location.href = `../MntElementos/index.php?id_articulo=${idArticulo}`;
    } else {
        // Fallback si no hay id_articulo
        window.location.href = '../MntElementos/index.php';
    }
});

$('#btnVolverElementosFiltrados').on('click', function(e) {
    e.preventDefault();
    const urlParams = new URLSearchParams(window.location.search);
    const idArticulo = urlParams.get('id_articulo');

    if (idArticulo) {
        // Redirigir al listado de elementos filtrado por el artículo
        window.location.href = `../MntElementos/index.php?id_articulo=${idArticulo}`;
    } else {
        // Fallback si no hay id_articulo
        window.location.href = '../MntElementos/index.php';
    }
});


  ////////////////////////////////////////////
  //   FIN FUNCIONES DE APOYO ADICIONALES  //
  //////////////////////////////////////////
});






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