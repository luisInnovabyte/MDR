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
  // FormValidator removido - ahora se maneja en formularioCoeficiente.js
  /////////////////////////////////////////
  //     FIN FORMATEO DE CAMPOS          //
  ////////////////////////////////////////

  /////////////////////////////////////
  // INICIO DE LA TABLA DE COEFICIENTES //
  //         DATATABLES             //
  ///////////////////////////////////
  var datatable_coeficientesConfig = {
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
        name: "id_coeficiente",
        data: "id_coeficiente",
        visible: false,
        className: "text-center",
      }, // Columna 1: ID_COEFICIENTE
      {
        name: "jornadas_coeficiente",
        data: "jornadas_coeficiente",
        className: "text-center ",
      }, // Columna 2: JORNADAS_COEFICIENTE
      {
        name: "valor_coeficiente",
        data: "valor_coeficiente",
        className: "text-center align-middle",
      }, // Columna 3: VALOR_COEFICIENTE
      {
        name: "observaciones_coeficiente",
        data: "observaciones_coeficiente",
        className: "text-center align-middle",
      }, // Columna 4: OBSERVACIONES_COEFICIENTE
      {
        name: "activo_coeficiente",
        data: "activo_coeficiente",
        className: "text-center align-middle",
      }, // Columna 5: ESTADO
      { name: "activar", data: null, className: "text-center align-middle" }, // Columna 6: ACTIVAR/DESACTIVAR
      {
        name: "editar",
        data: null,
        defaultContent: "",
        className: "text-center align-middle",
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
      // Columna 1: id_coeficiente
      {
        targets: "id_coeficiente:name",
        width: "5%",
        searchable: false,
        orderable: false,
        className: "text-center",
      },
      // Columna 2: jornadas_coeficiente
      {
        targets: "jornadas_coeficiente:name",
        width: "15%",
        searchable: true,
        orderable: true,
        className: "text-center",
        render: function (data, type, row) {
          if (type === "display") {
            return row.jornadas_coeficiente !== null && row.jornadas_coeficiente !== ""
              ? `<span class="badge bg-primary fs-6">${row.jornadas_coeficiente} días alquilados</span>`
              : '<span class="text-muted">Sin jornadas</span>';
          }
          return row.jornadas_coeficiente;
        },
      },
      // Columna 3: valor_coeficiente
      {
        targets: "valor_coeficiente:name",
        width: "15%",
        searchable: true,
        orderable: true,
        className: "text-center",
        render: function (data, type, row) {
          if (type === "display") {
            if (row.valor_coeficiente !== null && row.valor_coeficiente !== "") {
              const descuento = row.jornadas_coeficiente ? (parseFloat(row.jornadas_coeficiente) - parseFloat(row.valor_coeficiente)).toFixed(2) : '0';
              return `<span class="badge bg-success fs-6">${parseFloat(row.valor_coeficiente).toFixed(2)} días facturados</span>
                      <br><small class="text-muted">Descuento: ${descuento} días</small>`;
            } else {
              return '<span class="text-muted">Sin valor</span>';
            }
          }
          return row.valor_coeficiente;
        },
      },
      // Columna 4: observaciones_coeficiente
      {
        targets: "observaciones_coeficiente:name",
        width: "25%",
        searchable: true,
        orderable: true,
        className: "text-center",
        render: function (data, type, row) {
          if (type === "display") {
            const observaciones = row.observaciones_coeficiente || '';
            if (observaciones.length > 50) {
              return `<span title="${observaciones}">${observaciones.substring(0, 47)}...</span>`;
            }
            return observaciones || '<span class="text-muted fst-italic">Sin observaciones</span>';
          }
          return row.observaciones_coeficiente;
        },
      },
      // Columna 5: activo_coeficiente (Estado)
      {
        targets: "activo_coeficiente:name",
        width: "10%",
        orderable: true,
        searchable: true,
        className: "text-center",
        render: function (data, type, row) {
          if (type === "display") {
            return row.activo_coeficiente == 1
              ? '<i class="bi bi-check-circle text-success fa-2x"></i>'
              : '<i class="bi bi-x-circle text-danger fa-2x"></i>';
          }
          return row.activo_coeficiente;
        },
      },
      // Columna 6: BOTON PARA ACTIVAR/DESACTIVAR COEFICIENTE
      {
        targets: "activar:name",
        width: "10%",
        searchable: false,
        orderable: false,
        class: "text-center",
        render: function (data, type, row) {
          // El nombre que de la variable que se pasa por data-xxx debe ser el mismo que el nombre de la columna en la base de datos
          if (row.activo_coeficiente == 1) {
            // permito desactivar el coeficiente
            return `<button type="button" class="btn btn-danger btn-sm desacCoeficiente" data-bs-toggle="tooltip-primary" data-placement="top" title="Desactivar" data-original-title="Tooltip on top" 
                             data-id_coeficiente="${row.id_coeficiente}"> 
                             <i class="fa-solid fa-trash"></i>
                             </button>`;
          } else {
            // debo permitir activar de nuevo el coeficiente
            return `<button class="btn btn-success btn-sm activarCoeficiente" data-bs-toggle="tooltip-primary" data-placement="top" title="Activar" data-original-title="Tooltip on top" 
                             data-id_coeficiente="${row.id_coeficiente}">
                             <i class="bi bi-hand-thumbs-up-fill"></i>
                            </button>`;
          }
        }, // de la function
      }, //
      // Columna 7: BOTON PARA EDITAR COEFICIENTE
      {
        targets: "editar:name",
        width: "10%",
        searchable: false,
        orderable: false,
        class: "text-center",
        render: function (data, type, row) {
          // El nombre que de la variable que se pasa por data-xxx debe ser el mismo que el nombre de la columna en la base de datos
          // botón editar el coeficiente
          return `<button type="button" class="btn btn-info btn-sm editarCoeficiente" data-toggle="tooltip-primary" data-placement="top" title="Editar"  
                             data-id_coeficiente="${row.id_coeficiente}"> 
                             <i class="fa-solid fa-edit"></i>
                             </button>`;
        }, // de la function
      },
      // De la columna 7
    ], // de la columnDefs
    ajax: {
      url: "../../controller/coeficiente.php?op=listar",
      type: "GET",
      dataSrc: function (json) {
        console.log("JSON recibido:", json); // 📌 Ver qué estructura tiene
        return json.data || json; // Ajusta en función de lo recibido
      },
    }, // del ajax
  }; // de la variable datatable_coeficientesConfig
  ////////////////////////////
  // FIN DE LA TABLA DE COEFICIENTES //
  ///////////////////////////

  /************************************/
  //     ZONA DE DEFINICIONES        //
  /**********************************/
  // Definición inicial de la tabla de coeficientes
  var $table =
    $(
      "#coeficientes_data"
    ); /*<--- Es el nombre que le hemos dado a la tabla en HTML */
  var $tableConfig =
    datatable_coeficientesConfig; /*<--- Es el nombre que le hemos dado a la declaración de la definicion de la tabla */
  //var $columSearch = 3; /* <-- Es la columna en la cual al hacer click el valor se colocará en la zona de search y se buscará */
  var $tableBody = $(
    "#coeficientes_data tbody"
  ); /*<--- Es el nombre que le hemos dado al cuerpo de la tabla en HTML */
  /* en el tableBody solo cambiar el nombre de la tabla que encontraremos en HTML*/
  var $columnFilterInputs = $(
    "#coeficientes_data tfoot input, #coeficientes_data tfoot select"
  ); /*<--- Selecciona tanto inputs como selects de los pies de la tabla en HTML */
  /* en el $columnFilterInputs solo cambiar el nombre de la tabla que encontraremos en HTML*/

  //ejemplo -- var table_e = $('#coeficientes-table').DataTable(datatable_coeficientesConfig);
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
                        <i class="bi bi-calculator fs-3 me-2"></i>
                        <h5 class="card-title mb-0">Detalles del Coeficiente Reductor</h5>
                    </div>
                </div>
                <div class="card-body p-0" style="overflow: visible;">
                    <table class="table table-borderless table-striped table-hover mb-0">
                        <tbody>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-hash me-2"></i>Id Coeficiente
                                </th>
                                <td class="pe-4">
                                    ${
                                      d.id_coeficiente ||
                                      '<span class="text-muted fst-italic">No tiene id</span>'
                                    }
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-calendar-week me-2"></i>Días alquilados
                                </th>
                                <td class="pe-4">
                                    <span class="badge bg-primary fs-6">${
                                      d.jornadas_coeficiente ||
                                      'Sin jornadas'
                                    } días</span>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-receipt me-2"></i>Días facturados
                                </th>
                                <td class="pe-4">
                                    <span class="badge bg-success fs-6">${
                                      d.valor_coeficiente 
                                        ? parseFloat(d.valor_coeficiente).toFixed(2) + ' días'
                                        : 'Sin valor'
                                    }</span>
                                    ${d.jornadas_coeficiente && d.valor_coeficiente 
                                        ? `<br><small class="text-info mt-1">Descuento: ${(parseFloat(d.jornadas_coeficiente) - parseFloat(d.valor_coeficiente)).toFixed(2)} días</small>`
                                        : ''
                                    }
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-chat-left-text me-2"></i>Observaciones
                                </th>
                                <td class="pe-4" style="max-width: 300px; word-wrap: break-word; white-space: pre-wrap; overflow-wrap: break-word;">
                                    ${
                                      d.observaciones_coeficiente ||
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
                                      d.created_at_coeficiente
                                        ? formatoFechaEuropeo(
                                            d.created_at_coeficiente
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
                                    d.updated_at_coeficiente
                                      ? formatoFechaEuropeo(
                                          d.updated_at_coeficiente
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
  //   INICIO ZONA DELETE COEFICIENTE  //
  ///////////////////////////////////
  function desacCoeficiente(id) {
    Swal.fire({
      title: "Desactivar",
      html: `¿Desea desactivar el coeficiente con ID ${id}?<br><br><small class="text-warning"><i class="bi bi-exclamation-triangle me-1"></i>Este coeficiente no estará disponible para cálculos</small>`,
      icon: "question",
      showCancelButton: true,
      confirmButtonText: "Si",
      cancelButtonText: "No",
      reverseButtons: true,
    }).then((result) => {
      if (result.isConfirmed) {
        $.post(
          "../../controller/coeficiente.php?op=eliminar",
          { id_coeficiente: id },
          function (data) {
            $table.DataTable().ajax.reload();

            Swal.fire(
              "Desactivado",
              "El coeficiente ha sido desactivado",
              "success"
            );
          }
        );
      }
    });
  }

  // CAPTURAR EL CLICK EN EL BOTÓN DE BORRAR
  $(document).on("click", ".desacCoeficiente", function (event) {
    event.preventDefault();
    let id = $(this).data("id_coeficiente");
    desacCoeficiente(id);
  });
  ////////////////////////////////////
  //   FIN ZONA DELETE COEFICIENTE    //
  //////////////////////////////////

  ///////////////////////////////////////
  //   INICIO ZONA ACTIVAR COEFICIENTE  //
  /////////////////////////////////////
  function activarCoeficiente(id) {
    Swal.fire({
      title: "Activar",
      text: `¿Desea activar el coeficiente con ID ${id}?`,
      icon: "question",
      showCancelButton: true,
      confirmButtonText: "Si",
      cancelButtonText: "No",
      reverseButtons: true,
    }).then((result) => {
      if (result.isConfirmed) {
        $.post(
          "../../controller/coeficiente.php?op=activar",
          { id_coeficiente: id },
          function (data) {
            $table.DataTable().ajax.reload();

            Swal.fire("Activado", "El coeficiente ha sido activado", "success");
          }
        );
      }
    });
  }

  // CAPTURAR EL CLICK EN EL BOTÓN DE ACTIVAR
  $(document).on("click", ".activarCoeficiente", function (event) {
    event.preventDefault();
    let id = $(this).data("id_coeficiente");
    console.log("id coeficiente:", id);

    activarCoeficiente(id);
  });
  ////////////////////////////////////
  //   FIN ZONA ACTIVAR COEFICIENTE    //
  //////////////////////////////////

  ///////////////////////////////////////
  //      INICIO ZONA NUEVO           //
  //        BOTON DE NUEVO           //
  /////////////////////////////////////
  // BOTÓN NUEVO AHORA ES UN ENLACE DIRECTO AL FORMULARIO INDEPENDIENTE
  // La funcionalidad del modal ha sido removida

  // *****************************************************/
  // FUNCIONES DE GUARDAR COEFICIENTE REMOVIDAS
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
  $(document).on("click", ".editarCoeficiente", function (event) {
    event.preventDefault();

    let id = $(this).data("id_coeficiente");
    console.log("id coeficiente:", id);

    // Redirigir al formulario independiente en modo edición
    window.location.href = `formularioCoeficiente.php?modo=editar&id=${id}`;
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

  //ejemplo -- var table_e = $('#coeficientes-table').DataTable(datatable_coeficientesConfig);

  /////////////////////////////////////
  //  INICIO ZONA CLICS COLUMNA     //
  //    NO ES NECESARIO TOCAR      //
  //////////////////////////////////
  //Código para capturar clics solo en la tercera columna (edad) y filtrar DataTables
  // El resto no responden al clic
  //ejemplo - $('#coeficientes-table tbody').on('click', 'td', function () {

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

  // Filtro de cada columna en el pie de la tabla de coeficientes (tfoot)
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
    // ejemplo - $('#coeficientes-table tfoot input').each(function () {
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