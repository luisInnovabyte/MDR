$(document).ready(function () {

  // Obtener el ID del elemento desde la URL (si existe)
  const urlParams = new URLSearchParams(window.location.search);
  const idElemento = urlParams.get('id_elemento');
  
  // Si hay un id_elemento, mostrar info y ajustar el título
  if (idElemento) {
    cargarInfoElemento(idElemento);
    $('.br-pagetitle h4').text('Fotos del Elemento');
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
  // INICIO DE LA TABLA DE FOTOS //
  //         DATATABLES             //
  ///////////////////////////////////
  var datatable_fotosConfig = {
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
        name: "id_foto_elemento",
        data: "id_foto_elemento",
        visible: false,
        className: "text-center",
      }, // Columna 1: ID_FOTO_ELEMENTO
      {
        name: "descripcion_foto_elemento",
        data: "descripcion_foto_elemento",
        className: "text-center",
      }, // Columna 2: DESCRIPCION_FOTO_ELEMENTO
      {
        name: "privado_foto",
        data: "privado_foto",
        className: "text-center",
      }, // Columna 3: PRIVADO
      {
        name: "archivo_foto",
        data: null,
        className: "text-center",
      }, // Columna 4: VISTA PREVIA
      {
        name: "activo_foto",
        data: "activo_foto",
        className: "text-center",
      }, // Columna 5: ESTADO
      { name: "activar", data: null, className: "text-center" }, // Columna 6: ACTIVAR/DESACTIVAR
      {
        name: "editar",
        data: null,
        defaultContent: "",
        className: "text-center",
      }, // Columna 7: EDITAR
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
      // Columna 1: id_foto_elemento
      {
        targets: "id_foto_elemento:name",
        width: "5%",
        searchable: false,
        orderable: false,
        className: "text-center",
      },
      // Columna 2: descripcion_foto_elemento
      {
        targets: "descripcion_foto_elemento:name",
        width: "30%",
        searchable: true,
        orderable: true,
        className: "text-center",
      },
      // Columna 3: privado_foto
      {
        targets: "privado_foto:name",
        width: "8%",
        orderable: true,
        searchable: true,
        className: "text-center",
        render: function (data, type, row) {
          if (type === "display") {
            return row.privado_foto == 1
              ? '<i class="bi bi-lock-fill text-danger fa-2x" title="Foto privada"></i>'
              : '<i class="bi bi-unlock-fill text-success fa-2x" title="Foto pública"></i>';
          }
          return row.privado_foto;
        },
      },
      // Columna 4: archivo_foto (vista previa)
      {
        targets: "archivo_foto:name",
        width: "20%",
        orderable: false,
        searchable: false,
        className: "text-center",
        render: function (data, type, row) {
          if (type === "display" && row.archivo_foto) {
            const rutaArchivo = "../../public/img/fotos_elementos/" + row.archivo_foto;
            return `
              <div class="d-flex justify-content-center align-items-center gap-2">
                <a href="${rutaArchivo}" target="_blank" title="Ver imagen completa">
                  <img src="${rutaArchivo}" class="img-thumbnail" style="max-height: 60px; max-width: 80px; object-fit: cover; cursor: pointer;" alt="Vista previa">
                </a>
                <button class="btn btn-sm btn-outline-success descargarFoto" 
                        data-archivo="${row.archivo_foto}" 
                        title="Descargar foto">
                  <i class="bi bi-download"></i>
                </button>
              </div>
            `;
          }
          return '<span class="text-muted fst-italic">Sin imagen</span>';
        },
      },
      // Columna 5: activo_foto (Estado)
      {
        targets: "activo_foto:name",
        width: "8%",
        orderable: true,
        searchable: true,
        className: "text-center",
        render: function (data, type, row) {
          if (type === "display") {
            return row.activo_foto == 1
              ? '<i class="bi bi-check-circle text-success fa-2x"></i>'
              : '<i class="bi bi-x-circle text-danger fa-2x"></i>';
          }
          return row.activo_foto;
        },
      },
      // Columna 6: BOTON PARA ACTIVAR/DESACTIVAR ESTADO
      {
        targets: "activar:name",
        width: "8%",
        searchable: false,
        orderable: false,
        class: "text-center",
        render: function (data, type, row) {
          if (row.activo_foto == 1) {
            // permito desactivar la foto
            return `<button type="button" class="btn btn-danger btn-sm desacFoto" data-bs-toggle="tooltip-primary" data-placement="top" title="Desactivar" data-original-title="Tooltip on top" 
                             data-id_foto_elemento="${row.id_foto_elemento}"> 
                             <i class="fa-solid fa-trash"></i>
                             </button>`;
          } else {
            // debo permitir activar de nuevo la foto
            return `<button class="btn btn-success btn-sm activarFoto" data-bs-toggle="tooltip-primary" data-placement="top" title="Activar" data-original-title="Tooltip on top" 
                             data-id_foto_elemento="${row.id_foto_elemento}">
                             <i class="bi bi-hand-thumbs-up-fill"></i>
                            </button>`;
          }
        }, // de la function
      }, //
      // Columna 7: BOTON PARA EDITAR FOTO
      {
        targets: "editar:name",
        width: "8%",
        searchable: false,
        orderable: false,
        class: "text-center",
        render: function (data, type, row) {
          // botón editar la foto
          return `<button type="button" class="btn btn-info btn-sm editarFoto" data-toggle="tooltip-primary" data-placement="top" title="Editar"  
                             data-id_foto_elemento="${row.id_foto_elemento}"> 
                             <i class="fa-solid fa-edit"></i>
                             </button>`;
        }, // de la function
      },
    ], // de la columnDefs
    ajax: {
      url: "../../controller/foto_elemento.php?op=listar",
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
  }; // de la variable datatable_fotosConfig
  ////////////////////////////
  // FIN DE LA TABLA DE FOTOS //
  ///////////////////////////

  /************************************/
  //     ZONA DE DEFINICIONES        //
  /**********************************/
  var $table = $("#fotos_data");
  var $tableConfig = datatable_fotosConfig;
  var $tableBody = $("#fotos_data tbody");
  var $columnFilterInputs = $("#fotos_data tfoot input, #fotos_data tfoot select");

  var table_e = $table.DataTable($tableConfig);

  /************************************/
  //   FIN ZONA DE DEFINICIONES      //
  /**********************************/

  // Función para mostrar detalles expandibles de la foto
  function format(d) {
    console.log(d);

    return `
            <div class="card border-primary mb-3" style="overflow: visible;">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-image-fill fs-3 me-2"></i>
                        <h5 class="card-title mb-0">Detalles de la Foto</h5>
                    </div>
                </div>
                <div class="card-body p-0" style="overflow: visible;">
                    <table class="table table-borderless table-striped table-hover mb-0">
                        <tbody>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-image me-2"></i>Imagen completa
                                </th>
                                <td class="pe-4">
                                    ${
                                      d.archivo_foto
                                        ? `<div class="text-center">
                                            <a href="../../public/img/fotos_elementos/${d.archivo_foto}" target="_blank">
                                              <img src="../../public/img/fotos_elementos/${d.archivo_foto}" class="img-fluid rounded" style="max-height: 300px;" alt="${d.descripcion_foto_elemento}">
                                            </a>
                                           </div>`
                                        : '<span class="text-muted fst-italic">Sin imagen</span>'
                                    }
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-chat-left-text me-2"></i>Observaciones
                                </th>
                                <td class="pe-4" style="max-width: 400px; word-wrap: break-word; white-space: pre-wrap; overflow-wrap: break-word;">
                                    ${
                                      d.observaciones_foto
                                        ? `<div class="alert alert-info py-2 mb-0">
                                            <i class="bi bi-info-circle me-2"></i>${d.observaciones_foto}
                                           </div>`
                                        : '<span class="text-muted fst-italic">Sin observaciones</span>'
                                    }
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-calendar-plus me-2"></i>Foto creada el:
                                </th>
                                <td class="pe-4">
                                    ${
                                      d.created_at_foto
                                        ? formatoFechaEuropeo(
                                            d.created_at_foto
                                          )
                                        : '<span class="text-muted fst-italic">Sin fecha</span>'
                                    }
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-calendar-check me-2"></i>Foto actualizada el:
                                </th>
                                <td class="pe-4">
                                  ${
                                    d.updated_at_foto
                                      ? formatoFechaEuropeo(
                                          d.updated_at_foto
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
  //   INICIO ZONA DELETE FOTOS  //
  ///////////////////////////////////
  function desacFoto(id) {
    Swal.fire({
      title: "Desactivar",
      html: `¿Desea desactivar la foto con ID ${id}?`,
      icon: "question",
      showCancelButton: true,
      confirmButtonText: "Si",
      cancelButtonText: "No",
      reverseButtons: true,
    }).then((result) => {
      if (result.isConfirmed) {
        $.post(
          "../../controller/foto_elemento.php?op=eliminar",
          { id_foto_elemento: id },
          function (data) {
            $table.DataTable().ajax.reload();

            Swal.fire(
              "Desactivado",
              "La foto ha sido desactivada",
              "success"
            );
          }
        );
      }
    });
  }

  // CAPTURAR EL CLICK EN EL BOTÓN DE BORRAR
  $(document).on("click", ".desacFoto", function (event) {
    event.preventDefault();
    let id = $(this).data("id_foto_elemento");
    desacFoto(id);
  });
  ////////////////////////////////////
  //   FIN ZONA DELETE FOTO    //
  //////////////////////////////////

  ///////////////////////////////////////
  //   INICIO ZONA ACTIVAR FOTO  //
  /////////////////////////////////////
  function activarFoto(id) {
    Swal.fire({
      title: "Activar",
      text: `¿Desea activar la foto con ID ${id}?`,
      icon: "question",
      showCancelButton: true,
      confirmButtonText: "Si",
      cancelButtonText: "No",
      reverseButtons: true,
    }).then((result) => {
      if (result.isConfirmed) {
        $.post(
          "../../controller/foto_elemento.php?op=activar",
          { id_foto_elemento: id },
          function (data) {
            $table.DataTable().ajax.reload();

            Swal.fire("Activado", "La foto ha sido activada", "success");
          }
        );
      }
    });
  }

  // CAPTURAR EL CLICK EN EL BOTÓN DE ACTIVAR
  $(document).on("click", ".activarFoto", function (event) {
    event.preventDefault();
    let id = $(this).data("id_foto_elemento");
    console.log("id foto:", id);

    activarFoto(id);
  });
  ////////////////////////////////////
  //   FIN ZONA ACTIVAR FOTO    //
  //////////////////////////////////

  ///////////////////////////////////////
  //   INICIO ZONA DESCARGAR FOTO  //
  /////////////////////////////////////
  $(document).on("click", ".descargarFoto", function (event) {
    event.preventDefault();
    let archivo = $(this).data("archivo");
    
    if (archivo) {
      const rutaCompleta = "../../public/img/fotos_elementos/" + archivo;
      const link = document.createElement("a");
      link.href = rutaCompleta;
      link.download = archivo;
      link.target = "_blank";
      
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
      
      toastr.success("Descarga iniciada", "Foto descargada", {
        timeOut: 2000,
        positionClass: "toast-bottom-right",
      });
    }
  });
  ////////////////////////////////////
  //   FIN ZONA DESCARGAR FOTO    //
  //////////////////////////////////

  /////////////////////////////////////
  //      INICIO ZONA EDITAR           //
  //        BOTON DE EDITAR           //
  /////////////////////////////////////
  $(document).on("click", ".editarFoto", function (event) {
    event.preventDefault();

    let id = $(this).data("id_foto_elemento");
    console.log("id foto:", id);

    // Redirigir al formulario independiente en modo edición
    let url = `formularioFoto.php?modo=editar&id=${id}`;
    
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
        // Crear card de información del elemento
        const infoHtml = `
          <div class="alert alert-info alert-dismissible fade show mb-3" role="alert">
            <div class="d-flex align-items-center">
              <div class="me-3">
                <i class="bi bi-box-seam" style="font-size: 2rem;"></i>
              </div>
              <div class="flex-grow-1">
                <h6 class="alert-heading mb-1">Filtrando por elemento:</h6>
                <p class="mb-0">
                  <strong>${data.descripcion_elemento || 'Sin descripción'}</strong> 
                  <span class="badge bg-info ms-2">${data.codigo_elemento || '--'}</span>
                </p>
                <small class="text-muted">ID Elemento: ${id_elemento}</small>
              </div>
            </div>
          </div>
        `;
        
        // Insertar antes de la tabla
        $('.br-section-wrapper').prepend(infoHtml);
        
        // Ajustar el botón de nueva foto para incluir el id_elemento
        const btnNuevo = $('#btnNuevaFoto');
        if (btnNuevo.length) {
          const href = btnNuevo.attr('href');
          if (href && href.indexOf('id_elemento=') === -1) {
            btnNuevo.attr('href', `${href}&id_elemento=${id_elemento}`);
          }
        }
        
        // Ajustar el botón de volver para incluir el id_articulo del elemento
        if (data.id_articulo) {
          const btnVolver = $('#btnVolverElementos');
          if (btnVolver.length) {
            const hrefVolver = btnVolver.attr('href');
            if (hrefVolver && hrefVolver.indexOf('id_articulo=') === -1) {
              btnVolver.attr('href', `${hrefVolver}?id_articulo=${data.id_articulo}`);
            }
          }
        }
      }
    }, 'json').fail(function() {
      console.error('Error al cargar información del elemento');
    });
  }

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
