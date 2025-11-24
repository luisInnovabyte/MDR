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
  // INICIO DE LA TABLA DE GRUPOS  //
  //         DATATABLES             //
  ///////////////////////////////////
  var datatable_gruposConfig = {
    //serverSide: true, // procesamiento del lado del servidor
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
      // Son los botones para más
      // No tocar
      {
        name: "control",
        data: null,
        defaultContent: "",
        className: "details-control sorting_1 text-center",
      }, // Columna 0: Mostrar más
      {
        name: "id_grupo",
        data: "id_grupo",
        visible: false,
        className: "text-center",
      }, // Columna 1: ID_GRUPO
      {
        name: "codigo_grupo",
        data: "codigo_grupo",
        className: "text-center",
      }, // Columna 2: CODIGO_GRUPO
      {
        name: "nombre_grupo",
        data: "nombre_grupo",
        className: "text-center",
      }, // Columna 3: NOMBRE_GRUPO
      {
        name: "descripcion_grupo",
        data: "descripcion_grupo",
        className: "text-center",
      }, // Columna 4: DESCRIPCION_GRUPO
      {
        name: "activo_grupo",
        data: "activo_grupo",
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
      // Cuidado que el ordrData puede interferir con el ordenamiento de la tabla

      // Columna 0: BOTÓN MÁS
      {
        targets: "control:name",
        width: "5%",
        searchable: false,
        orderable: false,
        className: "text-center",
      },
      // Columna 1: id_grupo
      {
        targets: "id_grupo:name",
        width: "5%",
        searchable: false,
        orderable: false,
        className: "text-center",
      },
      // Columna 2: codigo_grupo
      {
        targets: "codigo_grupo:name",
        width: "15%",
        searchable: true,
        orderable: true,
        className: "text-center",
      },
      // Columna 3: nombre_grupo
      {
        targets: "nombre_grupo:name",
        width: "20%",
        searchable: true,
        orderable: true,
        className: "text-center",
      },
      // Columna 4: descripcion_grupo
      {
        targets: "descripcion_grupo:name",
        width: "25%",
        searchable: true,
        orderable: true,
        className: "text-start",
      },
      // Columna 5: activo_grupo (Estado)
      {
        targets: "activo_grupo:name",
        width: "10%",
        orderable: true,
        searchable: true,
        className: "text-center",
        render: function (data, type, row) {
          if (type === "display") {
            return row.activo_grupo == 1
              ? '<i class="bi bi-check-circle text-success fa-2x"></i>'
              : '<i class="bi bi-x-circle text-danger fa-2x"></i>';
          }
          return row.activo_grupo;
        },
      },
      // Columna 6: BOTON PARA ACTIVAR/DESACTIVAR ESTADO
      {
        targets: "activar:name",
        width: "10%",
        searchable: false,
        orderable: false,
        class: "text-center",
        render: function (data, type, row) {
          // El nombre que de la variable que se pasa por data-xxx debe ser el mismo que el nombre de la columna en la base de datos
          if (row.activo_grupo == 1) {
            // permito desactivar el grupo
            return `<button type="button" class="btn btn-danger btn-sm desacGrupo" data-bs-toggle="tooltip-primary" data-placement="top" title="Desactivar" data-original-title="Tooltip on top" 
                             data-id_grupo="${row.id_grupo}"> 
                             <i class="fa-solid fa-trash"></i>
                             </button>`;
          } else {
            // debo permitir activar de nuevo el grupo
            return `<button class="btn btn-success btn-sm activarGrupo" data-bs-toggle="tooltip-primary" data-placement="top" title="Activar" data-original-title="Tooltip on top" 
                             data-id_grupo="${row.id_grupo}">
                             <i class="bi bi-hand-thumbs-up-fill"></i>
                            </button>`;
          }
        }, // de la function
      }, //
      // Columna 7: BOTON PARA EDITAR GRUPO
      {
        targets: "editar:name",
        width: "10%",
        searchable: false,
        orderable: false,
        class: "text-center",
        render: function (data, type, row) {
          // El nombre que de la variable que se pasa por data-xxx debe ser el mismo que el nombre de la columna en la base de datos
          // botón editar el grupo
          return `<button type="button" class="btn btn-info btn-sm editarGrupo" data-toggle="tooltip-primary" data-placement="top" title="Editar"  
                             data-id_grupo="${row.id_grupo}"> 
                             <i class="fa-solid fa-edit"></i>
                             </button>`;
        }, // de la function
      },
      // De la columna 7
    ], // de la columnDefs
    ajax: {
      url: "../../controller/grupo_articulo.php?op=listar",
      type: "GET",
      dataSrc: function (json) {
        console.log("JSON recibido:", json); // 📌 Ver qué estructura tiene
        return json.data || json; // Ajusta en función de lo recibido
      },
    }, // del ajax
  }; // de la variable datatable_gruposConfig
  ////////////////////////////
  // FIN DE LA TABLA DE GRUPOS //
  ///////////////////////////

  /************************************/
  //     ZONA DE DEFINICIONES        //
  /**********************************/
  // Definición inicial de la tabla de grupos
  var $table =
    $(
      "#grupos_data"
    ); /*<--- Es el nombre que le hemos dado a la tabla en HTML */
  var $tableConfig =
    datatable_gruposConfig; /*<--- Es el nombre que le hemos dado a la declaración de la definicion de la tabla */
  var $tableBody = $(
    "#grupos_data tbody"
  ); /*<--- Es el nombre que le hemos dado al cuerpo de la tabla en HTML */
  /* en el tableBody solo cambiar el nombre de la tabla que encontraremos en HTML*/
  var $columnFilterInputs = $(
    "#grupos_data tfoot input, #grupos_data tfoot select"
  ); /*<--- Selecciona tanto inputs como selects de los pies de la tabla en HTML */
  /* en el $columnFilterInputs solo cambiar el nombre de la tabla que encontraremos en HTML*/

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
                        <i class="bi bi-gear-fill fs-3 me-2"></i>
                        <h5 class="card-title mb-0">Detalles del Grupo de Artículo</h5>
                    </div>
                </div>
                <div class="card-body p-0" style="overflow: visible;">
                    <table class="table table-borderless table-striped table-hover mb-0">
                        <tbody>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-hash me-2"></i>Id Grupo
                                </th>
                                <td class="pe-4">
                                    ${
                                      d.id_grupo ||
                                      '<span class="text-muted fst-italic">No tiene id grupo</span>'
                                    }
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-tags me-2"></i>Nombre Grupo
                                </th>
                                <td class="pe-4">
                                    ${
                                      d.nombre_grupo ||
                                      '<span class="text-muted fst-italic">No tiene nombre grupo</span>'
                                    }
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-chat-left-text me-2"></i>Descripción Grupo
                                </th>
                                <td class="pe-4" style="max-width: 300px; word-wrap: break-word; white-space: pre-wrap; overflow-wrap: break-word;">
                                    ${
                                      d.descripcion_grupo ||
                                      '<span class="text-muted fst-italic">No tiene descripción</span>'
                                    }
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-file-text me-2"></i>Observaciones
                                </th>
                                <td class="pe-4" style="max-width: 300px; word-wrap: break-word; white-space: pre-wrap; overflow-wrap: break-word;">
                                    ${
                                      d.observaciones_grupo ||
                                      '<span class="text-muted fst-italic">No tiene observaciones</span>'
                                    }
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-calendar-plus me-2"></i>Grupo creado el:
                                </th>
                                <td class="pe-4">
                                    ${
                                      d.created_at_grupo
                                        ? formatoFechaEuropeo(
                                            d.created_at_grupo
                                          )
                                        : '<span class="text-muted fst-italic">Sin fecha</span>'
                                    }
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-calendar-plus me-2"></i>Grupo actualizado el:
                                </th>
                                <td class="pe-4">
                                  ${
                                    d.updated_at_grupo
                                      ? formatoFechaEuropeo(
                                          d.updated_at_grupo
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
  //   INICIO ZONA DELETE GRUPOS    //
  ///////////////////////////////////
  function desacGrupo(id) {
    Swal.fire({
      title: "Desactivar",
      html: `¿Desea desactivar el grupo con ID ${id}?<br><br><small class="text-warning"><i class="bi bi-exclamation-triangle me-1"></i>Esto puede afectar las familias vinculadas a este grupo</small>`,
      icon: "question",
      showCancelButton: true,
      confirmButtonText: "Si",
      cancelButtonText: "No",
      reverseButtons: true,
    }).then((result) => {
      if (result.isConfirmed) {
        $.post(
          "../../controller/grupo_articulo.php?op=eliminar",
          { id_grupo: id },
          function (data) {
            $table.DataTable().ajax.reload();

            Swal.fire(
              "Desactivado",
              "El grupo ha sido desactivado",
              "success"
            );
          }
        );
      }
    });
  }

  // CAPTURAR EL CLICK EN EL BOTÓN DE BORRAR
  $(document).on("click", ".desacGrupo", function (event) {
    event.preventDefault();
    let id = $(this).data("id_grupo");
    desacGrupo(id);
  });
  ////////////////////////////////////
  //   FIN ZONA DELETE GRUPO      //
  //////////////////////////////////

  ///////////////////////////////////////
  //   INICIO ZONA ACTIVAR GRUPO    //
  /////////////////////////////////////
  function activarGrupo(id) {
    Swal.fire({
      title: "Activar",
      text: `¿Desea activar el grupo con ID ${id}?`,
      icon: "question",
      showCancelButton: true,
      confirmButtonText: "Si",
      cancelButtonText: "No",
      reverseButtons: true,
    }).then((result) => {
      if (result.isConfirmed) {
        $.post(
          "../../controller/grupo_articulo.php?op=activar",
          { id_grupo: id },
          function (data) {
            $table.DataTable().ajax.reload();

            Swal.fire("Activado", "El grupo ha sido activado", "success");
          }
        );
      }
    });
  }

  // CAPTURAR EL CLICK EN EL BOTÓN DE ACTIVAR
  $(document).on("click", ".activarGrupo", function (event) {
    event.preventDefault();
    let id = $(this).data("id_grupo");
    console.log("id grupo:", id);

    activarGrupo(id);
  });
  ////////////////////////////////////
  //   FIN ZONA ACTIVAR GRUPO      //
  //////////////////////////////////

  ///////////////////////////////////////
  //      INICIO ZONA EDITAR           //
  //        BOTON DE EDITAR           //
  /////////////////////////////////////
  // CAPTURAR EL CLICK EN EL BOTÓN DE EDITAR
  $(document).on("click", ".editarGrupo", function (event) {
    event.preventDefault();

    let id = $(this).data("id_grupo");
    console.log("id grupo:", id);

    // Redirigir al formulario independiente en modo edición
    window.location.href = `formularioGrupo.php?modo=editar&id=${id}`;
  });
  ///////////////////////////////////////
  //        FIN ZONA EDITAR           //
  /////////////////////////////////////

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

  // Filtro de cada columna en el pie de la tabla (tfoot)
  // Manejo para elementos input (keyup) y select (change)
  $columnFilterInputs.on("keyup change", function () {
    var columnIndex = table_e.column($(this).closest("th")).index(); // Obtener el índice de la columna del encabezado correspondiente
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
      $("#filter-alert").show();
    } else {
      $("#filter-alert").hide();
    }
  }

  // Esto es solo valido para la busqueda superior //
  table_e.on("search.dt", function () {
    updateFilterMessage(); // Actualizar mensaje de filtro
  });
  ////////////////////////////////////////////////////////

  // Botón para limpiar los filtros y ocultar el mensaje ////////////////////////////////////////////
  $("#clear-filter").on("click", function () {
    table_e.destroy(); // Destruir la tabla para limpiar los filtros

    // Limpiar los campos de búsqueda del pie de la tabla
    $columnFilterInputs.each(function () {
      $(this).val(""); // Limpiar cada campo input del pie y disparar el evento input
    });

    table_e = $table.DataTable($tableConfig);

    // Ocultar el mensaje de "Hay un filtro activo"
    $("#filter-alert").hide();
  });
  ////////////////////////////////////////////
  //  FIN ZONA FILTROS PIES y SEARCH     //
  ///////////////////////////////////////////
}); // de document.ready
