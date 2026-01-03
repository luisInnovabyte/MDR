$(document).ready(function () {
  // Agregar estilos CSS para el modal de imagen y grupos
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
            .group-row {
                background-color: #f8f9fa !important;
                font-weight: bold;
                font-size: 1.1em;
                cursor: pointer;
            }
            .group-row:hover {
                background-color: #e9ecef !important;
            }
            .group-row td {
                padding: 12px 8px !important;
                border-bottom: 2px solid #0d6efd !important;
            }
        `;
    document.head.appendChild(style);
  }

  // Función para recargar estadísticas
  function recargarEstadisticas() {
    $.ajax({
      url: "../../controller/articulo.php?op=estadisticas",
      type: "GET",
      dataType: "json",
      success: function(response) {
        if (response.success) {
          // Actualizar los valores en las tarjetas
          $(".card.border-primary h2").text(response.data.total);
          $(".card.border-success h2").text(response.data.activos);
          $(".card.border-info h2").text(response.data.kits);
          $(".card.border-warning h2").text(response.data.coeficientes);
        }
      },
      error: function(xhr, status, error) {
        console.error("Error al recargar estadísticas:", error);
      }
    });
  }

  /////////////////////////////////////
  // INICIO DE LA TABLA DE ARTÍCULOS //
  //         DATATABLES             //
  ///////////////////////////////////
  var datatable_articulosConfig = {
    processing: true,
    scrollX: true, // Habilita el desplazamiento horizontal
    scrollCollapse: true, // Permite que la tabla se ajuste al contenedor y que el scroll se oculte cuando no es necesario
    fixedColumns: {
      left: 2  // Fija columnas: control (0), id_articulo oculto (1) 
    },
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
        name: "id_articulo",
        data: "id_articulo",
        visible: false,
        className: "text-center",
      }, // Columna 1: ID_ARTICULO
      {
        name: "codigo_articulo",
        data: "codigo_articulo",
        className: "text-center align-middle",
      }, // Columna 2: CODIGO_ARTICULO
      {
        name: "nombre_articulo",
        data: "nombre_articulo",
        className: "text-center align-middle",
      }, // Columna 3: NOMBRE_ARTICULO
      { name: "familia", data: null, className: "text-center align-middle" }, // Columna 4: FAMILIA
      {
        name: "precio_alquiler_articulo",
        data: "precio_alquiler_articulo",
        className: "text-center align-middle",
      }, // Columna 5: PRECIO
      {
        name: "es_kit_articulo",
        data: "es_kit_articulo",
        className: "text-center align-middle",
      }, // Columna 6: ES_KIT
      {
        name: "coeficiente_efectivo",
        data: "coeficiente_efectivo",
        className: "text-center align-middle",
      }, // Columna 7: COEFICIENTES
      {
        name: "activo_articulo",
        data: "activo_articulo",
        className: "text-center align-middle",
      }, // Columna 8: ESTADO
      { name: "activar", data: null, className: "text-center align-middle" }, // Columna 9: ACTIVAR/DESACTIVAR
      {
        name: "editar",
        data: null,
        defaultContent: "",
        className: "text-center align-middle",
      }, // Columna 10: EDITAR
      {
        name: "elementos",
        data: null,
        defaultContent: "",
        className: "text-center align-middle",
      }, // Columna 11: ELEMENTOS
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
      // Columna 1: id_articulo
      {
        targets: "id_articulo:name",
        width: "5%",
        searchable: false,
        orderable: false,
        className: "text-center",
      },
      // Columna 2: codigo_articulo
      {
        targets: "codigo_articulo:name",
        width: "10%",
        searchable: true,
        orderable: true,
        className: "text-center",
      },
      // Columna 3: nombre_articulo
      {
        targets: "nombre_articulo:name",
        width: "20%",
        searchable: true,
        orderable: true,
        className: "text-center",
      },
      // Columna 4: familia (nombre_familia)
      {
        targets: "familia:name",
        width: "15%",
        searchable: true,
        orderable: true,
        className: "text-center",
        render: function (data, type, row) {
          if (type === "display" || type === "type") {
            const nombreFamilia = row.nombre_familia || "";
            const codigoFamilia = row.codigo_familia || "";
            
            if (nombreFamilia) {
              return `<span class="badge bg-info">${codigoFamilia}</span> <span class="text-muted">${nombreFamilia}</span>`;
            } else {
              return '<span class="text-muted fst-italic">Sin familia</span>';
            }
          }
          return row.nombre_familia || "";
        },
      },
      // Columna 5: precio_alquiler_articulo
      {
        targets: "precio_alquiler_articulo:name",
        width: "10%",
        orderable: true,
        searchable: true,
        className: "text-center",
        render: function (data, type, row) {
          if (type === "display") {
            return row.precio_alquiler_articulo > 0
              ? '<span class="badge bg-success">' + parseFloat(row.precio_alquiler_articulo).toFixed(2) + ' €</span>'
              : '<span class="badge bg-secondary">0.00 €</span>';
          }
          return row.precio_alquiler_articulo;
        },
      },
      // Columna 6: es_kit_articulo
      {
        targets: "es_kit_articulo:name",
        width: "8%",
        orderable: true,
        searchable: true,
        className: "text-center",
        render: function (data, type, row) {
          if (type === "display") {
            return row.es_kit_articulo == 1
              ? `<button type="button" class="btn btn-link p-0 verKit" data-toggle="tooltip" data-placement="top" title="Gestionar composición del KIT" data-id_articulo="${row.id_articulo}">
                   <i class="bi bi-box-seam text-primary fa-2x"></i>
                 </button>`
              : '<i class="bi bi-box text-muted fa-2x" title="Artículo individual"></i>';
          }
          return row.es_kit_articulo;
        },
      },
      // Columna 7: coeficiente_efectivo
      {
        targets: "coeficiente_efectivo:name",
        width: "8%",
        orderable: true,
        searchable: true,
        className: "text-center",
        render: function (data, type, row) {
          if (type === "display") {
            return row.coeficiente_efectivo == 1
              ? '<i class="bi bi-percent text-success fa-2x" title="Permite coeficientes"></i>'
              : '<i class="bi bi-slash-circle text-danger fa-2x" title="No permite coeficientes"></i>';
          }
          return row.coeficiente_efectivo;
        },
      },
      // Columna 8: activo_articulo (Estado)
      {
        targets: "activo_articulo:name",
        width: "8%",
        orderable: true,
        searchable: true,
        className: "text-center",
        render: function (data, type, row) {
          if (type === "display") {
            return row.activo_articulo == 1
              ? '<i class="bi bi-check-circle text-success fa-2x"></i>'
              : '<i class="bi bi-x-circle text-danger fa-2x"></i>';
          }
          return row.activo_articulo;
        },
      },
      // Columna 9: BOTON PARA ACTIVAR/DESACTIVAR ESTADO
      {
        targets: "activar:name",
        width: "8%",
        searchable: false,
        orderable: false,
        class: "text-center",
        render: function (data, type, row) {
          if (row.activo_articulo == 1) {
            return `<button type="button" class="btn btn-danger btn-sm desacArticulo" data-bs-toggle="tooltip-primary" data-placement="top" title="Desactivar" data-original-title="Tooltip on top" 
                             data-id_articulo="${row.id_articulo}"> 
                             <i class="fa-solid fa-trash"></i>
                             </button>`;
          } else {
            return `<button class="btn btn-success btn-sm activarArticulo" data-bs-toggle="tooltip-primary" data-placement="top" title="Activar" data-original-title="Tooltip on top" 
                             data-id_articulo="${row.id_articulo}">
                             <i class="bi bi-hand-thumbs-up-fill"></i>
                            </button>`;
          }
        },
      },
      // Columna 10: BOTON PARA EDITAR ARTICULO
      {
        targets: "editar:name",
        width: "8%",
        searchable: false,
        orderable: false,
        class: "text-center",
        render: function (data, type, row) {
          return `<button type="button" class="btn btn-info btn-sm editarArticulo" data-toggle="tooltip-primary" data-placement="top" title="Editar"  
                             data-id_articulo="${row.id_articulo}"> 
                             <i class="fa-solid fa-edit"></i>
                             </button>`;
        },
      },
      // Columna 11: CONTEO Y BOTON PARA VER ELEMENTOS
      {
        targets: "elementos:name",
        width: "8%",
        searchable: false,
        orderable: true,
        class: "text-center",
        render: function (data, type, row) {
          if (type === "display") {
            const totalElementos = row.total_elementos || 0;
            const badgeClass = totalElementos > 0 ? 'bg-success' : 'bg-secondary';
            return `<div class="d-flex align-items-center justify-content-center gap-2">
                      <span class="badge ${badgeClass} fs-6">${totalElementos}</span>
                      <button type="button" class="btn btn-warning btn-sm verElementos" data-toggle="tooltip-primary" data-placement="top" title="Ver Elementos"  
                                 data-id_articulo="${row.id_articulo}"> 
                                 <i class="bi bi-list-ul"></i>
                      </button>
                    </div>`;
          }
          return row.total_elementos || 0;
        },
      },
    ],
    ajax: {
      url: "../../controller/articulo.php?op=listar",
      type: "GET",
      dataSrc: function (json) {
        console.log("JSON recibido:", json);
        return json.data || json;
      },
    },
    order: [[4, 'asc']], // Ordenar por columna de familia (índice 4)
    rowGroup: {
      dataSrc: 'nombre_familia',
      startRender: function (rows, group) {
        // Obtener información del grupo
        var familiaData = rows.data()[0];
        var codigoFamilia = familiaData.codigo_familia || 'Sin código';
        var nombreFamilia = group || 'Sin familia';
        var count = rows.count();
        
        return $('<tr/>')
          .addClass('group-row bg-light')
          .append('<td colspan="12" class="text-start fw-bold text-primary">' +
            '<i class="bi bi-folder-fill me-2"></i>' +
            '<span class="badge bg-primary me-2">' + codigoFamilia + '</span>' +
            nombreFamilia + 
            ' <span class="badge bg-secondary ms-2">' + count + ' artículo(s)</span>' +
            '</td>');
      }
    }
  };
  ////////////////////////////
  // FIN DE LA TABLA DE ARTÍCULOS //
  ///////////////////////////

  /************************************/
  //     ZONA DE DEFINICIONES        //
  /**********************************/
  var $table = $("#articulos_data");
  var $tableConfig = datatable_articulosConfig;
  var $tableBody = $("#articulos_data tbody");
  var $columnFilterInputs = $(
    "#articulos_data tfoot input, #articulos_data tfoot select"
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
                        <i class="bi bi-box-seam fs-3 me-2"></i>
                        <h5 class="card-title mb-0">Detalles del Artículo</h5>
                    </div>
                </div>
                <div class="card-body p-0" style="overflow: visible;">
                    <table class="table table-borderless table-striped table-hover mb-0">
                        <tbody>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-hash me-2"></i>Id Artículo
                                </th>
                                <td class="pe-4">
                                    ${d.id_articulo || '<span class="text-muted fst-italic">No tiene id artículo</span>'}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-tags me-2"></i>Nombre Artículo
                                </th>
                                <td class="pe-4">
                                    ${d.nombre_articulo || '<span class="text-muted fst-italic">No tiene nombre artículo</span>'}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-tags me-2"></i>Nombre Artículo (en)
                                </th>
                                <td class="pe-4">
                                    ${d.name_articulo || '<span class="text-muted fst-italic">No tiene traducción (en)</span>'}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-folder-fill me-2"></i>Familia
                                </th>
                                <td class="pe-4">
                                    ${d.nombre_familia
                                        ? `<div>
                                            <span class="badge bg-info">${d.codigo_familia || 'N/A'}</span> 
                                            <strong>${d.nombre_familia}</strong>
                                            ${d.nombre_grupo ? `<br><small class="text-muted">Grupo: ${d.nombre_grupo}</small>` : ''}
                                           </div>`
                                        : '<span class="text-muted fst-italic">Sin familia asignada</span>'
                                    }
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-rulers me-2"></i>Unidad de Medida
                                </th>
                                <td class="pe-4">
                                    ${d.nombre_unidad
                                        ? `<span class="badge bg-secondary">${d.simbolo_unidad || ''}</span> ${d.nombre_unidad}`
                                        : '<span class="text-muted fst-italic">Sin unidad asignada</span>'
                                    }
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-cash-coin me-2"></i>Precio de Alquiler
                                </th>
                                <td class="pe-4">
                                    <span class="badge bg-success fs-5">${parseFloat(d.precio_alquiler_articulo || 0).toFixed(2)} €</span>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-box-seam me-2"></i>Tipo de Artículo
                                </th>
                                <td class="pe-4">
                                    ${d.es_kit_articulo == 1
                                        ? '<span class="badge bg-primary"><i class="bi bi-box-seam me-1"></i>Es un Kit</span>'
                                        : '<span class="badge bg-secondary"><i class="bi bi-box me-1"></i>Artículo Individual</span>'
                                    }
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-toggles me-2"></i>Controles Especiales
                                </th>
                                <td class="pe-4">
                                    ${d.control_total_articulo == 1 
                                        ? '<span class="badge bg-warning text-dark me-1"><i class="bi bi-shield-check me-1"></i>Control Total</span>' 
                                        : ''}
                                    ${d.no_facturar_articulo == 1 
                                        ? '<span class="badge bg-danger me-1"><i class="bi bi-x-circle me-1"></i>No Facturar</span>' 
                                        : ''}
                                    ${d.control_total_articulo == 0 && d.no_facturar_articulo == 0
                                        ? '<span class="text-muted fst-italic">Sin controles especiales</span>'
                                        : ''}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-percent me-2"></i>Coeficientes de Descuento
                                </th>
                                <td class="pe-4">
                                    ${d.coeficiente_efectivo == 1
                                        ? '<span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Permite coeficientes</span>'
                                        : '<span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>No permite coeficientes</span>'
                                    }
                                    <br>
                                    <small class="text-muted mt-1">
                                        ${d.coeficiente_articulo === null 
                                            ? `Heredado de familia (${d.coeficiente_familia == 1 ? 'Sí permite' : 'No permite'})`
                                            : d.coeficiente_efectivo == 1
                                                ? 'Configuración propia: permite descuentos'
                                                : 'Configuración propia: no permite descuentos'
                                        }
                                    </small>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-image me-2"></i>Imagen Artículo
                                </th>
                                <td class="pe-4">
                                    ${d.imagen_articulo
                                        ? `<div class="text-center">
                                            <img src="../../public/img/articulo/${d.imagen_articulo}" 
                                                 alt="Imagen de ${d.nombre_articulo}" 
                                                 class="img-thumbnail rounded shadow-sm" 
                                                 style="max-width: 150px; max-height: 150px; cursor: pointer;"
                                                 onclick="mostrarImagenCompleta('../../public/img/articulo/${d.imagen_articulo}', '${d.nombre_articulo}')">
                                            <br>
                                            <small class="text-muted mt-1 d-block">${d.imagen_articulo}</small>
                                            <div class="mt-2">
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-primary me-2" 
                                                        onclick="mostrarImagenCompleta('../../public/img/articulo/${d.imagen_articulo}', '${d.nombre_articulo}')"
                                                        title="Ver imagen completa">
                                                    <i class="bi bi-eye me-1"></i>Ver
                                                </button>
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-success" 
                                                        onclick="descargarImagen('../../public/img/articulo/${d.imagen_articulo}', '${d.nombre_articulo}_${d.imagen_articulo}')"
                                                        title="Descargar imagen">
                                                    <i class="bi bi-download me-1"></i>Descargar
                                                </button>
                                            </div>
                                        </div>`
                                        : d.imagen_efectiva 
                                            ? `<div class="text-center">
                                                <img src="../../public/img/familia/${d.imagen_efectiva}" 
                                                     alt="Imagen heredada de familia" 
                                                     class="img-thumbnail rounded shadow-sm" 
                                                     style="max-width: 150px; max-height: 150px; opacity: 0.7;">
                                                <br>
                                                <small class="text-muted mt-1 d-block"><i class="bi bi-arrow-down-circle me-1"></i>Heredada de familia</small>
                                               </div>`
                                            : '<span class="text-muted fst-italic">Sin imagen</span>'
                                    }
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-file-text me-2"></i>Notas Presupuesto (ES)
                                </th>
                                <td class="pe-4" style="max-width: 400px; word-wrap: break-word; white-space: pre-wrap; overflow-wrap: break-word;">
                                    ${d.notas_presupuesto_articulo
                                        ? `<div class="alert alert-info py-2 mb-0">
                                            <i class="bi bi-info-circle me-2"></i>${d.notas_presupuesto_articulo}
                                           </div>`
                                        : '<span class="text-muted fst-italic">Sin notas para presupuestos</span>'
                                    }
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-file-text me-2"></i>Budget Notes (EN)
                                </th>
                                <td class="pe-4" style="max-width: 400px; word-wrap: break-word; white-space: pre-wrap; overflow-wrap: break-word;">
                                    ${d.notes_budget_articulo
                                        ? `<div class="alert alert-info py-2 mb-0">
                                            <i class="bi bi-info-circle me-2"></i>${d.notes_budget_articulo}
                                           </div>`
                                        : '<span class="text-muted fst-italic">No budget notes</span>'
                                    }
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-sort-numeric-down me-2"></i>Orden Observaciones
                                </th>
                                <td class="pe-4">
                                    <span class="badge bg-secondary fs-6">${d.orden_obs_articulo || 200}</span>
                                    <br><small class="text-muted mt-1">Orden de aparición en presupuestos</small>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-chat-left-text me-2"></i>Observaciones Internas
                                </th>
                                <td class="pe-4" style="max-width: 300px; word-wrap: break-word; white-space: pre-wrap; overflow-wrap: break-word;">
                                    ${d.observaciones_articulo || '<span class="text-muted fst-italic">Sin observaciones</span>'}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-diagram-3 me-2"></i>Jerarquía Completa
                                </th>
                                <td class="pe-4">
                                    <span class="text-primary">${d.jerarquia_completa || 'No disponible'}</span>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-calendar-plus me-2"></i>Artículo creado el:
                                </th>
                                <td class="pe-4">
                                    ${d.created_at_articulo
                                        ? formatoFechaEuropeo(d.created_at_articulo)
                                        : '<span class="text-muted fst-italic">Sin fecha</span>'
                                    }
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-calendar-check me-2"></i>Artículo actualizado el:
                                </th>
                                <td class="pe-4">
                                  ${d.updated_at_articulo
                                      ? formatoFechaEuropeo(d.updated_at_articulo)
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
  //   INICIO ZONA DELETE ARTÍCULOS  //
  ///////////////////////////////////
  function desacArticulo(id) {
    Swal.fire({
      title: "Desactivar",
      text: `¿Desea desactivar el artículo con ID ${id}?`,
      icon: "question",
      showCancelButton: true,
      confirmButtonText: "Si",
      cancelButtonText: "No",
      reverseButtons: true,
    }).then((result) => {
      if (result.isConfirmed) {
        $.post(
          "../../controller/articulo.php?op=eliminar",
          { id_articulo: id },
          function (data) {
            $table.DataTable().ajax.reload();
            recargarEstadisticas();

            Swal.fire(
              "Desactivado",
              "El artículo ha sido desactivado",
              "success"
            );
          }
        );
      }
    });
  }

  // CAPTURAR EL CLICK EN EL BOTÓN DE BORRAR
  $(document).on("click", ".desacArticulo", function (event) {
    event.preventDefault();
    let id = $(this).data("id_articulo");
    desacArticulo(id);
  });
  ////////////////////////////////////
  //   FIN ZONA DELETE ARTÍCULO    //
  //////////////////////////////////

  ///////////////////////////////////////
  //   INICIO ZONA ACTIVAR ARTÍCULO  //
  /////////////////////////////////////
  function activarArticulo(id) {
    Swal.fire({
      title: "Activar",
      text: `¿Desea activar el artículo con ID ${id}?`,
      icon: "question",
      showCancelButton: true,
      confirmButtonText: "Si",
      cancelButtonText: "No",
      reverseButtons: true,
    }).then((result) => {
      if (result.isConfirmed) {
        $.post(
          "../../controller/articulo.php?op=activar",
          { id_articulo: id },
          function (data) {
            $table.DataTable().ajax.reload();
            recargarEstadisticas();

            Swal.fire("Activado", "El artículo ha sido activado", "success");
          }
        );
      }
    });
  }

  // CAPTURAR EL CLICK EN EL BOTÓN DE ACTIVAR
  $(document).on("click", ".activarArticulo", function (event) {
    event.preventDefault();
    let id = $(this).data("id_articulo");
    console.log("id articulo:", id);

    activarArticulo(id);
  });
  ////////////////////////////////////
  //   FIN ZONA ACTIVAR ARTÍCULO    //
  //////////////////////////////////

  ///////////////////////////////////////
  //      INICIO ZONA EDITAR           //
  //        BOTON DE EDITAR           //
  /////////////////////////////////////
  // CAPTURAR EL CLICK EN EL BOTÓN DE EDITAR
  $(document).on("click", ".editarArticulo", function (event) {
    event.preventDefault();

    let id = $(this).data("id_articulo");
    console.log("id articulo:", id);

    // Redirigir al formulario independiente en modo edición
    window.location.href = `formularioArticulo.php?modo=editar&id=${id}`;
  });
  ///////////////////////////////////////
  //        FIN ZONA EDITAR           //
  /////////////////////////////////////

  ///////////////////////////////////////
  //   INICIO ZONA VER ELEMENTOS       //
  /////////////////////////////////////
  // CAPTURAR EL CLICK EN EL BOTÓN DE VER ELEMENTOS
  $(document).on("click", ".verElementos", function (event) {
    event.preventDefault();

    let id_articulo = $(this).data("id_articulo");
    console.log("Ver elementos del artículo:", id_articulo);

    // Redirigir a la tabla de elementos filtrada por artículo
    window.location.href = `../MntElementos/index.php?id_articulo=${id_articulo}`;
  });
  ///////////////////////////////////////
  //     FIN ZONA VER ELEMENTOS        //
  /////////////////////////////////////

  ///////////////////////////////////////
  //   INICIO ZONA VER KIT             //
  /////////////////////////////////////
  // CAPTURAR EL CLICK EN EL BOTÓN DE VER KIT
  $(document).on("click", ".verKit", function (event) {
    event.preventDefault();

    let id_articulo = $(this).data("id_articulo");
    console.log("Ver composición del KIT:", id_articulo);

    // Redirigir a la gestión de composición del KIT
    window.location.href = `../MntKit/index.php?id_articulo=${id_articulo}`;
  });
  ///////////////////////////////////////
  //     FIN ZONA VER KIT              //
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

// Función global para mostrar imagen completa en modal
function mostrarImagenCompleta(rutaImagen, nombreArticulo) {
  Swal.fire({
    title: `Imagen de: ${nombreArticulo}`,
    html: `<img src="${rutaImagen}" alt="${nombreArticulo}" style="max-width: 100%; max-height: 80vh; border-radius: 8px;">`,
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
  const link = document.createElement("a");
  link.href = rutaImagen;
  link.download = nombreArchivo;
  link.target = "_blank";

  document.body.appendChild(link);
  link.click();

  document.body.removeChild(link);

  toastr.success("Descarga iniciada", "Imagen descargada", {
    timeOut: 2000,
    positionClass: "toast-bottom-right",
  });
}

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