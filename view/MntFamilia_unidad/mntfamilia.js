$(document).ready(function () {
  // Agregar estilos CSS para el modal de imagen
  if (!document.getElementById("imagen-modal-styles")) {
    const style = document.createElement("style");
    style.id = "imagen-modal-styles";
    style.textContent = `
            .swal-wide {
                max-width: 90% !important;
                width: auto !important;
            }
            .swal2-html-container img {
                box-shadow: 0 4px 15px rgba(0,0,0,0.3);
                transition: transform 0.3s ease;
            }
            .img-thumbnail:hover {
                transform: scale(1.05);
                transition: transform 0.3s ease;
            }
        `;
    document.head.appendChild(style);
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
  //     FORMATEO DE CAMPOS          //
  ///////////////////////////////////
  // FormValidator removido - ahora se maneja en formularioFamilia.js
  /////////////////////////////////////////
  //     FIN FORMATEO DE CAMPOS          //
  ////////////////////////////////////////

  /////////////////////////////////////
  // INICIO DE LA TABLA DE FAMILIAS //
  //         DATATABLES             //
  ///////////////////////////////////
  var datatable_familiasConfig = {
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
        name: "id_familia",
        data: "id_familia",
        visible: false,
        className: "text-center",
      }, // Columna 1: ID_FAMILIA
      {
        name: "codigo_familia",
        data: "codigo_familia",
        className: "text-center",
      }, // Columna 2: CODIGO_FAMILIA
      {
        name: "nombre_familia",
        data: "nombre_familia",
        className: "text-center",
      }, // Columna 3: NOMBRE_FAMILIA
      { name: "unidad_medida", data: null, className: "text-center" }, // Columna 4: UNIDAD DE MEDIDA (simbolo + nombre)
      {
        name: "coeficiente_familia",
        data: "coeficiente_familia",
        className: "text-center",
      }, // Columna 5: COEFICIENTES
      {
        name: "activo_familia",
        data: "activo_familia",
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
      // Columna 1: id_familia
      {
        targets: "id_familia:name",
        width: "5%",
        searchable: false,
        orderable: false,
        className: "text-center",
      },
      // Columna 2: codigo_familia
      {
        targets: "codigo_familia:name",
        width: "15%",
        searchable: true,
        orderable: true,
        className: "text-center",
      },
      // Columna 3: nombre_familia
      {
        targets: "nombre_familia:name",
        width: "20%",
        searchable: true,
        orderable: true,
        className: "text-center",
      },
      // Columna 4: unidad_medida (simbolo + nombre)
      {
        targets: "unidad_medida:name",
        width: "20%",
        searchable: true,
        orderable: true,
        className: "text-center",
        render: function (data, type, row) {
          if (type === "display" || type === "type") {
            // Concatenar símbolo y nombre de unidad
            const simbolo = row.simbolo_unidad || "";
            const nombre = row.nombre_unidad || "";
           

            if (simbolo && nombre) {
              return `<span class="badge bg-primary">${simbolo}</span> <span class="text-muted">${nombre}</span>`;
            } else if (nombre) {
              return `<span class="text-muted">${nombre}</span>`;
            } else if (simbolo) {
              return `<span class="badge bg-primary">${simbolo}</span>`;
            } else {
              return '<span class="text-muted fst-italic">Sin unidad</span>';
            }
          }
          // Para búsqueda y ordenamiento, usar texto plano
          return (row.simbolo_unidad || "") + " " + (row.nombre_unidad || "");
        },
      },
      // Columna 5: coeficiente_familia
      {
        targets: "coeficiente_familia:name",
        width: "10%",
        orderable: true,
        searchable: true,
        className: "text-center",
        render: function (data, type, row) {
          if (type === "display") {
            return row.coeficiente_familia == 1
              ? '<i class="bi bi-percent text-success fa-2x" title="Permite coeficientes"></i>'
              : '<i class="bi bi-slash-circle text-danger fa-2x" title="No permite coeficientes"></i>';
          }
          return row.coeficiente_familia;
        },
      },
      // Columna 4: descr_familia
      { targets: 4, className: "text-start" },
      // Columna 6: activo_familia (Estado)
      {
        targets: "activo_familia:name",
        width: "10%",
        orderable: true,
        searchable: true,
        className: "text-center",
        render: function (data, type, row) {
          if (type === "display") {
            return row.activo_familia == 1
              ? '<i class="bi bi-check-circle text-success fa-2x"></i>'
              : '<i class="bi bi-x-circle text-danger fa-2x"></i>';
          }
          return row.activo_familia;
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
          if (row.activo_familia == 1) {
            // permito desactivar la familia
            return `<button type="button" class="btn btn-danger btn-sm desacFamilia" data-bs-toggle="tooltip-primary" data-placement="top" title="Desactivar" data-original-title="Tooltip on top" 
                             data-id_familia="${row.id_familia}"> 
                             <i class="fa-solid fa-trash"></i>
                             </button>`;
          } else {
            // debo permitir activar de nuevo la familia
            return `<button class="btn btn-success btn-sm activarFamilia" data-bs-toggle="tooltip-primary" data-placement="top" title="Activar" data-original-title="Tooltip on top" 
                             data-id_familia="${row.id_familia}">
                             <i class="bi bi-hand-thumbs-up-fill"></i>
                            </button>`;
          }
        }, // de la function
      }, //
      // Columna 8: BOTON PARA EDITAR FAMILIA
      {
        targets: "editar:name",
        width: "10%",
        searchable: false,
        orderable: false,
        class: "text-center",
        render: function (data, type, row) {
          // El nombre que de la variable que se pasa por data-xxx debe ser el mismo que el nombre de la columna en la base de datos
          // botón editar la familia
          return `<button type="button" class="btn btn-info btn-sm editarFamilia" data-toggle="tooltip-primary" data-placement="top" title="Editar"  
                             data-id_familia="${row.id_familia}"> 
                             <i class="fa-solid fa-edit"></i>
                             </button>`;
        }, // de la function
      },
      // De la columna 8
    ], // de la columnDefs
    ajax: {
      url: "../../controller/familia_unidad.php?op=listar",
      type: "GET",
      dataSrc: function (json) {
        console.log("JSON recibido:", json); // 📌 Ver qué estructura tiene
        return json.data || json; // Ajusta en función de lo recibido
      },
    }, // del ajax
  }; // de la variable datatable_familiasConfig
  ////////////////////////////
  // FIN DE LA TABLA DE FAMILIAS //
  ///////////////////////////

  /************************************/
  //     ZONA DE DEFINICIONES        //
  /**********************************/
  // Definición inicial de la tabla de familias
  var $table =
    $(
      "#familias_data"
    ); /*<--- Es el nombre que le hemos dado a la tabla en HTML */
  var $tableConfig =
    datatable_familiasConfig; /*<--- Es el nombre que le hemos dado a la declaración de la definicion de la tabla */
  //var $columSearch = 3; /* <-- Es la columna en la cual al hacer click el valor se colocará en la zona de search y se buscará */
  var $tableBody = $(
    "#familias_data tbody"
  ); /*<--- Es el nombre que le hemos dado al cuerpo de la tabla en HTML */
  /* en el tableBody solo cambiar el nombre de la tabla que encontraremos en HTML*/
  var $columnFilterInputs = $(
    "#familias_data tfoot input, #familias_data tfoot select"
  ); /*<--- Selecciona tanto inputs como selects de los pies de la tabla en HTML */
  /* en el $columnFilterInputs solo cambiar el nombre de la tabla que encontraremos en HTML*/

  //ejemplo -- var table_e = $('#familias-table').DataTable(datatable_familiasConfig);
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
                        <h5 class="card-title mb-0">Detalles de la Familia</h5>
                    </div>
                </div>
                <div class="card-body p-0" style="overflow: visible;">
                    <table class="table table-borderless table-striped table-hover mb-0">
                        <tbody>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-hash me-2"></i>Id Familia
                                </th>
                                <td class="pe-4">
                                    ${
                                      d.id_familia ||
                                      '<span class="text-muted fst-italic">No tiene id familia</span>'
                                    }
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-tags me-2"></i>Nombre Familia
                                </th>
                                <td class="pe-4">
                                    ${
                                      d.nombre_familia ||
                                      '<span class="text-muted fst-italic">No tiene nombre familia</span>'
                                    }
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-tags me-2"></i>Nombre Familia (en)
                                </th>
                                <td class="pe-4">
                                    ${
                                      d.name_familia ||
                                      '<span class="text-muted fst-italic">No tiene traducción (en)</span>'
                                    }
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-folder-fill me-2"></i>Grupo de Artículo
                                </th>
                                <td class="pe-4">
                                    ${
                                      d.nombre_grupo
                                        ? `<div>
                                            <span class="badge bg-info">${d.codigo_grupo || 'N/A'}</span> 
                                            <strong>${d.nombre_grupo}</strong>
                                            ${d.descripcion_grupo ? `<br><small class="text-muted">${d.descripcion_grupo}</small>` : ''}
                                           </div>`
                                        : '<span class="text-muted fst-italic">No asignado a ningún grupo</span>'
                                    }
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-chat-left-text me-2"></i>Observaciones Familia
                                </th>
                                <td class="pe-4" style="max-width: 300px; word-wrap: break-word; white-space: pre-wrap; overflow-wrap: break-word;">
                                    ${
                                      d.descr_familia ||
                                      '<span class="text-muted fst-italic">No tiene observaciones</span>'
                                    }
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-image me-2"></i>Imagen Familia
                                </th>
                                <td class="pe-4">
                                    ${
                                      d.imagen_familia
                                        ? `<div class="text-center">
                                            <img src="../../public/img/familia/${d.imagen_familia}" 
                                                 alt="Imagen de ${d.nombre_familia}" 
                                                 class="img-thumbnail rounded shadow-sm" 
                                                 style="max-width: 150px; max-height: 150px; cursor: pointer;"
                                                 onclick="mostrarImagenCompleta('../../public/img/familia/${d.imagen_familia}', '${d.nombre_familia}')">
                                            <br>
                                            <small class="text-muted mt-1 d-block">${d.imagen_familia}</small>
                                            <div class="mt-2">
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-primary me-2" 
                                                        onclick="mostrarImagenCompleta('../../public/img/familia/${d.imagen_familia}', '${d.nombre_familia}')"
                                                        title="Ver imagen completa">
                                                    <i class="bi bi-eye me-1"></i>Ver
                                                </button>
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-success" 
                                                        onclick="descargarImagen('../../public/img/familia/${d.imagen_familia}', '${d.nombre_familia}_${d.imagen_familia}')"
                                                        title="Descargar imagen">
                                                    <i class="bi bi-download me-1"></i>Descargar
                                                </button>
                                            </div>
                                        </div>`
                                        : '<span class="text-muted fst-italic">No tiene imagen</span>'
                                    }
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-file-text me-2"></i>Observaciones Presupuesto
                                </th>
                                <td class="pe-4" style="max-width: 400px; word-wrap: break-word; white-space: pre-wrap; overflow-wrap: break-word;">
                                    ${
                                      d.observaciones_presupuesto_familia
                                        ? `<div class="alert alert-info py-2 mb-0">
                                            <i class="bi bi-info-circle me-2"></i>${d.observaciones_presupuesto_familia}
                                           </div>`
                                        : '<span class="text-muted fst-italic">Sin observaciones para presupuestos</span>'
                                    }
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-sort-numeric-down me-2"></i>Orden Observaciones
                                </th>
                                <td class="pe-4">
                                    ${
                                      d.orden_obs_familia
                                        ? `<span class="badge bg-secondary fs-6">${d.orden_obs_familia}</span>
                                           <br><small class="text-muted mt-1">Orden de aparición en presupuestos</small>`
                                        : '<span class="badge bg-secondary fs-6">100</span><br><small class="text-muted mt-1">Orden por defecto</small>'
                                    }
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-file-text me-2"></i>Observaciones Presupuesto (en)
                                </th>
                                <td class="pe-4" style="max-width: 400px; word-wrap: break-word; white-space: pre-wrap; overflow-wrap: break-word;">
                                    ${
                                      d.observations_budget_familia
                                        ? `<div class="alert alert-info py-2 mb-0">
                                            <i class="bi bi-info-circle me-2"></i>${d.observations_budget_familia}
                                           </div>`
                                        : '<span class="text-muted fst-italic">Sin observaciones en inglés para presupuestos</span>'
                                    }
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-percent me-2"></i>Coeficientes de Descuento
                                </th>
                                <td class="pe-4">
                                    ${
                                      d.coeficiente_familia == 1
                                        ? '<span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Permite coeficientes</span>'
                                        : '<span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>No permite coeficientes</span>'
                                    }
                                    <br>
                                    <small class="text-muted mt-1">
                                        ${d.coeficiente_familia == 1 
                                            ? 'Esta familia permite aplicar coeficientes de descuento en artículos y presupuestos' 
                                            : 'Esta familia no permite aplicar coeficientes de descuento'
                                        }
                                    </small>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-calendar-plus me-2"></i>Familia creada el:
                                </th>
                                <td class="pe-4">
                                    ${
                                      d.created_at_familia
                                        ? formatoFechaEuropeo(
                                            d.created_at_familia
                                          )
                                        : '<span class="text-muted fst-italic">Sin fecha</span>'
                                    }
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-calendar-plus me-2"></i>Familia actualizada el:
                                </th>
                                <td class="pe-4">
                                  ${
                                    d.updated_at_familia
                                      ? formatoFechaEuropeo(
                                          d.updated_at_familia
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
  //   INICIO ZONA DELETE FAMILIAS  //
  ///////////////////////////////////
  function desacFamilia(id) {
    Swal.fire({
      title: "Desactivar",
      html: `¿Desea desactivar la familia con ID ${id}?<br><br><small class="text-warning"><i class="bi bi-exclamation-triangle me-1"></i>Esto desactivará todos los artículos que tengan esta familia</small>`,
      icon: "question",
      showCancelButton: true,
      confirmButtonText: "Si",
      cancelButtonText: "No",
      reverseButtons: true,
    }).then((result) => {
      if (result.isConfirmed) {
        $.post(
          "../../controller/familia_unidad.php?op=eliminar",
          { id_familia: id },
          function (data) {
            $table.DataTable().ajax.reload();

            Swal.fire(
              "Desactivado",
              "La familia ha sido desactivada",
              "success"
            );
          }
        );
      }
    });
  }

  // CAPTURAR EL CLICK EN EL BOTÓN DE BORRAR
  $(document).on("click", ".desacFamilia", function (event) {
    event.preventDefault();
    let id = $(this).data("id_familia");
    desacFamilia(id);
  });
  ////////////////////////////////////
  //   FIN ZONA DELETE FAMILIA    //
  //////////////////////////////////

  ///////////////////////////////////////
  //   INICIO ZONA ACTIVAR FAMILIA  //
  /////////////////////////////////////
  function activarFamilia(id) {
    Swal.fire({
      title: "Activar",
      text: `¿Desea activar la familia con ID ${id}?`,
      icon: "question",
      showCancelButton: true,
      confirmButtonText: "Si",
      cancelButtonText: "No",
      reverseButtons: true,
    }).then((result) => {
      if (result.isConfirmed) {
        $.post(
          "../../controller/familia_unidad.php?op=activar",
          { id_familia: id },
          function (data) {
            $table.DataTable().ajax.reload();

            Swal.fire("Activado", "La familia ha sido activada", "success");
          }
        );
      }
    });
  }

  // CAPTURAR EL CLICK EN EL BOTÓN DE ACTIVAR
  $(document).on("click", ".activarFamilia", function (event) {
    event.preventDefault();
    let id = $(this).data("id_familia");
    console.log("id familia:", id);

    activarFamilia(id);
  });
  ////////////////////////////////////
  //   FIN ZONA ACTIVAR FAMILIA    //
  //////////////////////////////////

  ///////////////////////////////////////
  //      INICIO ZONA NUEVO           //
  //        BOTON DE NUEVO           //
  /////////////////////////////////////
  // BOTÓN NUEVO AHORA ES UN ENLACE DIRECTO AL FORMULARIO INDEPENDIENTE
  // La funcionalidad del modal ha sido removida

  // *****************************************************/
  // FUNCIONES DE GUARDAR FAMILIA REMOVIDAS
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
  $(document).on("click", ".editarFamilia", function (event) {
    event.preventDefault();

    let id = $(this).data("id_familia");
    console.log("id familia:", id);

    // Redirigir al formulario independiente en modo edición
    window.location.href = `formularioFamilia.php?modo=editar&id=${id}`;
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

// Función global para mostrar imagen completa en modal
function mostrarImagenCompleta(rutaImagen, nombreFamilia) {
  Swal.fire({
    title: `Imagen de: ${nombreFamilia}`,
    html: `<img src="${rutaImagen}" alt="${nombreFamilia}" style="max-width: 100%; max-height: 80vh; border-radius: 8px;">`,
    showCloseButton: true,
    showConfirmButton: false,
    customClass: {
      popup: "swal-wide",
    },
    background: "#fff",
    backdrop: "rgba(0,0,0,0.8)",
  });
}

// Función global para descargar imagen
function descargarImagen(rutaImagen, nombreArchivo) {
  // Crear un elemento <a> temporal para forzar la descarga
  const link = document.createElement("a");
  link.href = rutaImagen;
  link.download = nombreArchivo;
  link.target = "_blank";

  // Agregar al DOM temporalmente y hacer clic
  document.body.appendChild(link);
  link.click();

  // Remover el elemento temporal
  document.body.removeChild(link);

  // Mostrar mensaje de confirmación
  toastr.success("Descarga iniciada", "Imagen descargada", {
    timeOut: 2000,
    positionClass: "toast-bottom-right",
  });
}
