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
  // FormValidator removido - ahora se maneja en formularioEstadoPresupuesto.js
  /////////////////////////////////////////
  //     FIN FORMATEO DE CAMPOS          //
  ////////////////////////////////////////

  /////////////////////////////////////
  // INICIO DE LA TABLA DE ESTADOS DE PRESUPUESTO //
  //         DATATABLES             //
  ///////////////////////////////////
  var datatable_estadosPresupuestoConfig = {
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
        name: "id_estado_ppto",
        data: "id_estado_ppto",
        visible: false,
        className: "text-center",
      }, // Columna 1: ID_ESTADO_PPTO
      {
        name: "codigo_estado_ppto",
        data: "codigo_estado_ppto",
        className: "text-center",
      }, // Columna 2: CODIGO_ESTADO_PPTO
      {
        name: "nombre_estado_ppto",
        data: "nombre_estado_ppto",
        className: "text-center",
      }, // Columna 3: NOMBRE_ESTADO_PPTO
      {
        name: "color_estado_ppto",
        data: "color_estado_ppto",
        className: "text-center",
      }, // Columna 4: COLOR_ESTADO_PPTO
      {
        name: "orden_estado_ppto",
        data: "orden_estado_ppto",
        className: "text-center",
      }, // Columna 5: ORDEN_ESTADO_PPTO
      {
        name: "activo_estado_ppto",
        data: "activo_estado_ppto",
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
      // Cuidado que el ordrData puede interferir con el ordenamiento de la tabla

      // Columna 0: BOTÓN MÁS
      {
        targets: "control:name",
        width: "5%",
        searchable: false,
        orderable: false,
        className: "text-center",
      },
      // Columna 1: id_estado_ppto
      {
        targets: "id_estado_ppto:name",
        width: "5%",
        searchable: false,
        orderable: false,
        className: "text-center",
      },
      // Columna 2: codigo_estado_ppto
      {
        targets: "codigo_estado_ppto:name",
        width: "15%",
        searchable: true,
        orderable: true,
        className: "text-center",
      },
      // Columna 3: nombre_estado_ppto
      {
        targets: "nombre_estado_ppto:name",
        width: "25%",
        searchable: true,
        orderable: true,
        className: "text-center",
      },
      // Columna 4: color_estado_ppto
      {
        targets: "color_estado_ppto:name",
        width: "15%",
        searchable: true,
        orderable: true,
        className: "text-center",
        render: function (data, type, row) {
          if (type === "display") {
            return row.color_estado_ppto
              ? `<div class="d-flex align-items-center justify-content-center">
                   <span style="display: inline-block; background-color: ${row.color_estado_ppto}; width: 20px; height: 20px; border-radius: 50%; margin-right: 8px; border: 1px solid #ccc;"></span>
                   <span>${row.color_estado_ppto}</span>
                 </div>`
              : '<span class="text-muted">Sin color</span>';
          }
          return row.color_estado_ppto;
        },
      },
      // Columna 5: orden_estado_ppto
      {
        targets: "orden_estado_ppto:name",
        width: "10%",
        searchable: true,
        orderable: true,
        className: "text-center",
        render: function (data, type, row) {
          if (type === "display") {
            return row.orden_estado_ppto !== null && row.orden_estado_ppto !== ""
              ? `<span class="badge bg-info">${row.orden_estado_ppto}</span>`
              : '<span class="text-muted">Sin orden</span>';
          }
          return row.orden_estado_ppto;
        },
      },
      // Columna 6: activo_estado_ppto (Estado)
      {
        targets: "activo_estado_ppto:name",
        width: "10%",
        orderable: true,
        searchable: true,
        className: "text-center",
        render: function (data, type, row) {
          if (type === "display") {
            return row.activo_estado_ppto == 1
              ? '<i class="bi bi-check-circle text-success fa-2x"></i>'
              : '<i class="bi bi-x-circle text-danger fa-2x"></i>';
          }
          return row.activo_estado_ppto;
        },
      },
      // Columna 7: BOTON PARA ACTIVAR/DESACTIVAR ESTADO
      {
        targets: "activar:name",
        width: "10%",
        searchable: false,
        orderable: false,
        class: "text-center",
        render: function (data, type, row) {
          // El nombre que de la variable que se pasa por data-xxx debe ser el mismo que el nombre de la columna en la base de datos
          if (row.activo_estado_ppto == 1) {
            // permito desactivar el estado de presupuesto
            return `<button type="button" class="btn btn-danger btn-sm desacEstadoPresupuesto" data-bs-toggle="tooltip-primary" data-placement="top" title="Desactivar" data-original-title="Tooltip on top" 
                             data-id_estado_ppto="${row.id_estado_ppto}"> 
                             <i class="fa-solid fa-trash"></i>
                             </button>`;
          } else {
            // debo permitir activar de nuevo el estado de presupuesto
            return `<button class="btn btn-success btn-sm activarEstadoPresupuesto" data-bs-toggle="tooltip-primary" data-placement="top" title="Activar" data-original-title="Tooltip on top" 
                             data-id_estado_ppto="${row.id_estado_ppto}">
                             <i class="bi bi-hand-thumbs-up-fill"></i>
                            </button>`;
          }
        }, // de la function
      }, //
      // Columna 8: BOTON PARA EDITAR ESTADO DE PRESUPUESTO
      {
        targets: "editar:name",
        width: "10%",
        searchable: false,
        orderable: false,
        class: "text-center",
        render: function (data, type, row) {
          // El nombre que de la variable que se pasa por data-xxx debe ser el mismo que el nombre de la columna en la base de datos
          // botón editar el estado de presupuesto
          return `<button type="button" class="btn btn-info btn-sm editarEstadoPresupuesto" data-toggle="tooltip-primary" data-placement="top" title="Editar"  
                             data-id_estado_ppto="${row.id_estado_ppto}"> 
                             <i class="fa-solid fa-edit"></i>
                             </button>`;
        }, // de la function
      },
      // De la columna 8
    ], // de la columnDefs
    ajax: {
      url: "../../controller/estado_presupuesto.php?op=listar",
      type: "GET",
      dataSrc: function (json) {
        console.log("JSON recibido:", json); // 📌 Ver qué estructura tiene
        return json.data || json; // Ajusta en función de lo recibido
      },
    }, // del ajax
  }; // de la variable datatable_estadosPresupuestoConfig
  ////////////////////////////
  // FIN DE LA TABLA DE ESTADOS DE PRESUPUESTO //
  ///////////////////////////

  /************************************/
  //     ZONA DE DEFINICIONES        //
  /**********************************/
  // Definición inicial de la tabla de estados de presupuesto
  var $table =
    $(
      "#estados_presupuesto_data"
    ); /*<--- Es el nombre que le hemos dado a la tabla en HTML */
  var $tableConfig =
    datatable_estadosPresupuestoConfig; /*<--- Es el nombre que le hemos dado a la declaración de la definicion de la tabla */
  //var $columSearch = 3; /* <-- Es la columna en la cual al hacer click el valor se colocará en la zona de search y se buscará */
  var $tableBody = $(
    "#estados_presupuesto_data tbody"
  ); /*<--- Es el nombre que le hemos dado al cuerpo de la tabla en HTML */
  /* en el tableBody solo cambiar el nombre de la tabla que encontraremos en HTML*/
  var $columnFilterInputs = $(
    "#estados_presupuesto_data tfoot input, #estados_presupuesto_data tfoot select"
  ); /*<--- Selecciona tanto inputs como selects de los pies de la tabla en HTML */
  /* en el $columnFilterInputs solo cambiar el nombre de la tabla que encontraremos en HTML*/

  //ejemplo -- var table_e = $('#estados_presupuesto-table').DataTable(datatable_estadosPresupuestoConfig);
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
                        <i class="bi bi-flag fs-3 me-2"></i>
                        <h5 class="card-title mb-0">Detalles del Estado de Presupuesto</h5>
                    </div>
                </div>
                <div class="card-body p-0" style="overflow: visible;">
                    <table class="table table-borderless table-striped table-hover mb-0">
                        <tbody>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-hash me-2"></i>Id Estado
                                </th>
                                <td class="pe-4">
                                    ${
                                      d.id_estado_ppto ||
                                      '<span class="text-muted fst-italic">No tiene id</span>'
                                    }
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-tag me-2"></i>Código
                                </th>
                                <td class="pe-4">
                                    <span class="badge bg-primary">${
                                      d.codigo_estado_ppto ||
                                      'Sin código'
                                    }</span>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-flag me-2"></i>Nombre Estado
                                </th>
                                <td class="pe-4">
                                    ${
                                      d.nombre_estado_ppto ||
                                      '<span class="text-muted fst-italic">No tiene nombre</span>'
                                    }
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-palette me-2"></i>Color
                                </th>
                                <td class="pe-4">
                                    ${
                                      d.color_estado_ppto
                                        ? `<div class="d-flex align-items-center">
                                             <span class="badge" style="background-color: ${d.color_estado_ppto}; color: white; width: 30px; height: 30px; border-radius: 50%; margin-right: 10px;"></span>
                                             <span class="fw-bold">${d.color_estado_ppto}</span>
                                           </div>`
                                        : '<span class="text-muted fst-italic">Sin color asignado</span>'
                                    }
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-sort-numeric-up me-2"></i>Orden
                                </th>
                                <td class="pe-4">
                                    ${
                                      d.orden_estado_ppto !== null && d.orden_estado_ppto !== ""
                                        ? `<span class="badge bg-info fs-6">Posición ${d.orden_estado_ppto}</span>`
                                        : '<span class="badge bg-secondary fs-6">Sin orden definido</span>'
                                    }
                                    <br>
                                    <small class="text-muted mt-1">
                                        ${d.orden_estado_ppto !== null && d.orden_estado_ppto !== ""
                                            ? `Este estado aparece en la posición ${d.orden_estado_ppto} en las listas ordenadas` 
                                            : 'No tiene una posición específica en el orden de estados'
                                        }
                                    </small>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-chat-left-text me-2"></i>Observaciones
                                </th>
                                <td class="pe-4" style="max-width: 300px; word-wrap: break-word; white-space: pre-wrap; overflow-wrap: break-word;">
                                    ${
                                      d.observaciones_estado_ppto ||
                                      '<span class="text-muted fst-italic">Sin observaciones</span>'
                                    }
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-calendar-plus me-2"></i>Creado el:
                                </th>
                                <td class="pe-4">
                                    ${
                                      d.created_at_estado_ppto
                                        ? formatoFechaEuropeo(
                                            d.created_at_estado_ppto
                                          )
                                        : '<span class="text-muted fst-italic">Sin fecha</span>'
                                    }
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-calendar-check me-2"></i>Actualizado el:
                                </th>
                                <td class="pe-4">
                                  ${
                                    d.updated_at_estado_ppto
                                      ? formatoFechaEuropeo(
                                          d.updated_at_estado_ppto
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
  //   INICIO ZONA DELETE ESTADO PRESUPUESTO  //
  ///////////////////////////////////
  function desacEstadoPresupuesto(id) {
    Swal.fire({
      title: "Desactivar",
      html: `¿Desea desactivar el estado de presupuesto con ID ${id}?<br><br><small class="text-warning"><i class="bi bi-exclamation-triangle me-1"></i>Este estado no estará disponible para nuevos presupuestos</small>`,
      icon: "question",
      showCancelButton: true,
      confirmButtonText: "Si",
      cancelButtonText: "No",
      reverseButtons: true,
    }).then((result) => {
      if (result.isConfirmed) {
        $.post(
          "../../controller/estado_presupuesto.php?op=eliminar",
          { id_estado_ppto: id },
          function (data) {
            $table.DataTable().ajax.reload();

            Swal.fire(
              "Desactivado",
              "El estado de presupuesto ha sido desactivado",
              "success"
            );
          }
        );
      }
    });
  }

  // CAPTURAR EL CLICK EN EL BOTÓN DE BORRAR
  $(document).on("click", ".desacEstadoPresupuesto", function (event) {
    event.preventDefault();
    let id = $(this).data("id_estado_ppto");
    desacEstadoPresupuesto(id);
  });
  ////////////////////////////////////
  //   FIN ZONA DELETE ESTADO PRESUPUESTO    //
  //////////////////////////////////

  ///////////////////////////////////////
  //   INICIO ZONA ACTIVAR ESTADO PRESUPUESTO  //
  /////////////////////////////////////
  function activarEstadoPresupuesto(id) {
    Swal.fire({
      title: "Activar",
      text: `¿Desea activar el estado de presupuesto con ID ${id}?`,
      icon: "question",
      showCancelButton: true,
      confirmButtonText: "Si",
      cancelButtonText: "No",
      reverseButtons: true,
    }).then((result) => {
      if (result.isConfirmed) {
        $.post(
          "../../controller/estado_presupuesto.php?op=activar",
          { id_estado_ppto: id },
          function (data) {
            $table.DataTable().ajax.reload();

            Swal.fire("Activado", "El estado de presupuesto ha sido activado", "success");
          }
        );
      }
    });
  }

  // CAPTURAR EL CLICK EN EL BOTÓN DE ACTIVAR
  $(document).on("click", ".activarEstadoPresupuesto", function (event) {
    event.preventDefault();
    let id = $(this).data("id_estado_ppto");
    console.log("id estado presupuesto:", id);

    activarEstadoPresupuesto(id);
  });
  ////////////////////////////////////
  //   FIN ZONA ACTIVAR ESTADO PRESUPUESTO    //
  //////////////////////////////////

  ///////////////////////////////////////
  //      INICIO ZONA NUEVO           //
  //        BOTON DE NUEVO           //
  /////////////////////////////////////
  // BOTÓN NUEVO AHORA ES UN ENLACE DIRECTO AL FORMULARIO INDEPENDIENTE
  // La funcionalidad del modal ha sido removida

  // *****************************************************/
  // FUNCIONES DE GUARDAR ESTADO PRESUPUESTO REMOVIDAS
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
  $(document).on("click", ".editarEstadoPresupuesto", function (event) {
    event.preventDefault();

    let id = $(this).data("id_estado_ppto");
    console.log("id estado presupuesto:", id);

    // Redirigir al formulario independiente en modo edición
    window.location.href = `formularioEstadoPresupuesto.php?modo=editar&id=${id}`;
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

  //ejemplo -- var table_e = $('#employees-table').DataTable(datatable_employeeConfig);

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

  // Filtro de cada columna en el pie de la tabla de empleados (tfoot)
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
    //console.log('Limpiando filtros...');
    table_e.destroy(); // Destruir la tabla para limpiar los filtros

    // Limpiar los campos de búsqueda del pie de la tabla
    // ejemplo - $('#employees-table tfoot input').each(function () {
    $columnFilterInputs.each(function () {
      //console.log('Campo:', $(this).attr('placeholder'), 'Valor antes:', $(this).val());
      $(this).val(""); // Limpiar cada campo input del pie y disparar el evento input
      //console.log('Valor después:', $(this).val());
    });

    table_e = $table.DataTable($tableConfig);

    // Ocultar el mensaje de "Hay un filtro activo"
    $("#filter-alert").hide();
  });
  ////////////////////////////////////////////
  //  FIN ZONA FILTROS PIES y SEARCH     //
  ///////////////////////////////////////////
}); // de document.ready