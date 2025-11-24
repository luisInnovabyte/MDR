$(document).ready(function () {
  console.log("📄 formularioEstadoElemento.js cargado correctamente");

  /////////////////////////////////////
  //       VARIABLES GLOBALES        //
  /////////////////////////////////////
  let modo = "nuevo"; // Por defecto, modo 'nuevo'
  let id_estado_elemento = null;

  /////////////////////////////////////
  //   DETECCIÓN DE MODO Y CARGA     //
  /////////////////////////////////////
  // Detectar el modo del formulario desde la URL
  const urlParams = new URLSearchParams(window.location.search);
  modo = urlParams.get("modo") || "nuevo";
  id_estado_elemento = urlParams.get("id") || null;

  console.log(`🔍 Modo detectado: ${modo}`);
  console.log(`🆔 ID estado elemento: ${id_estado_elemento}`);

  // Si estamos en modo edición, cargar los datos
  if (modo === "editar" && id_estado_elemento) {
    console.log(`✏️ Cargando datos del estado de elemento ID: ${id_estado_elemento}`);
    cargarDatosEstadoElemento(id_estado_elemento);
  } else {
    console.log("✅ Modo nuevo - Formulario vacío");
  }

  /////////////////////////////////////
  //   FUNCIÓN CARGAR DATOS          //
  /////////////////////////////////////
  function cargarDatosEstadoElemento(id) {
    console.log(`🔄 Solicitando datos del estado de elemento ID: ${id}`);

    $.ajax({
      url: "../../controller/estado_elemento.php",
      method: "POST",
      data: {
        op: "mostrar",
        id_estado_elemento: id,
      },
      dataType: "json",
      success: function (response) {
        console.log("📦 Respuesta del servidor:", response);

        // Verificar si hay error en la respuesta
        if (response.status === "error") {
          console.error("❌ Error en respuesta:", response.message);
          toastr.error(response.message || "Error al cargar datos", "Error");
          return;
        }

        if (response && response.id_estado_elemento) {
          console.log("✅ Datos del estado de elemento recibidos correctamente");

          // Rellenar el formulario con los datos
          $("#id_estado_elemento").val(response.id_estado_elemento);
          $("#codigo_estado_elemento").val(response.codigo_estado_elemento);
          $("#descripcion_estado_elemento").val(response.descripcion_estado_elemento);
          $("#color_estado_elemento").val(response.color_estado_elemento);
          $("#color_estado_elemento_text").val(response.color_estado_elemento);
          
          // Configurar el switch de permite_alquiler
          if (response.permite_alquiler_estado_elemento == 1) {
            $("#permite_alquiler_estado_elemento").prop("checked", true);
            $("#permite_alquiler_label").text("Los elementos en este estado SÍ pueden ser alquilados")
              .removeClass("text-danger")
              .addClass("text-success");
          } else {
            $("#permite_alquiler_estado_elemento").prop("checked", false);
            $("#permite_alquiler_label").text("Los elementos en este estado NO pueden ser alquilados")
              .removeClass("text-success")
              .addClass("text-danger");
          }

          $("#observaciones_estado_elemento").val(response.observaciones_estado_elemento || "");

          // Actualizar contador de caracteres para observaciones
          const obsLength = (response.observaciones_estado_elemento || "").length;
          $("#char-count-observaciones").text(`${obsLength}/500`);
          
          if (obsLength > 400) {
            $("#char-count-observaciones").removeClass("text-muted text-warning").addClass("text-danger");
          } else if (obsLength > 250) {
            $("#char-count-observaciones").removeClass("text-muted text-danger").addClass("text-warning");
          } else {
            $("#char-count-observaciones").removeClass("text-warning text-danger").addClass("text-muted");
          }

          // Si el estado está inactivo, mostrar el switch de activo
          if (response.activo_estado_elemento != undefined) {
            $("#activo_estado_elemento").prop("checked", response.activo_estado_elemento == 1);
          }

          console.log("✅ Formulario cargado con los datos del estado de elemento");
        } else {
          console.error("❌ Error: Respuesta del servidor sin datos válidos");
          toastr.error("No se encontraron datos del estado de elemento", "Error");
        }
      },
      error: function (xhr, status, error) {
        console.error("❌ Error al cargar los datos del estado de elemento:", error);
        console.error("📄 Status HTTP:", xhr.status);
        console.error("📄 Respuesta del servidor:", xhr.responseText);
        console.error("📄 Status text:", xhr.statusText);
        
        let errorMsg = "Error al cargar los datos del estado de elemento";
        
        // Intentar parsear la respuesta para obtener más detalles
        try {
          const errorResponse = JSON.parse(xhr.responseText);
          if (errorResponse.message) {
            errorMsg = errorResponse.message;
          }
        } catch (e) {
          // Si no es JSON, mostrar el texto tal cual (limitado a 200 caracteres)
          if (xhr.responseText && xhr.responseText.length > 0) {
            errorMsg += ": " + xhr.responseText.substring(0, 200);
          }
        }
        
        toastr.error(errorMsg, "Error HTTP " + xhr.status, {
          timeOut: 10000,
          closeButton: true
        });
      },
    });
  }

  /////////////////////////////////////
  //   VALIDACIÓN DEL FORMULARIO     //
  /////////////////////////////////////
  function validarFormulario() {
    console.log("🔍 Validando formulario...");

    let errores = [];

    // Validar código
    const codigo = $("#codigo_estado_elemento").val().trim();
    if (!codigo) {
      errores.push("El código del estado es obligatorio");
    } else if (codigo.length < 2 || codigo.length > 20) {
      errores.push("El código debe tener entre 2 y 20 caracteres");
    }

    // Validar descripción
    const descripcion = $("#descripcion_estado_elemento").val().trim();
    if (!descripcion) {
      errores.push("La descripción del estado es obligatoria");
    } else if (descripcion.length < 3 || descripcion.length > 50) {
      errores.push("La descripción debe tener entre 3 y 50 caracteres");
    }

    // Validar color
    const color = $("#color_estado_elemento").val().trim();
    if (!color) {
      errores.push("El color del estado es obligatorio");
    } else if (!/^#[0-9A-F]{6}$/i.test(color)) {
      errores.push("El color debe ser un código hexadecimal válido (ej: #4CAF50)");
    }

    // Mostrar errores si existen
    if (errores.length > 0) {
      console.log("❌ Errores de validación:", errores);
      toastr.error(errores.join("<br>"), "Errores de validación", {
        timeOut: 5000,
        closeButton: true,
        escapeHtml: false,
      });
      return false;
    }

    console.log("✅ Formulario válido");
    return true;
  }

  /////////////////////////////////////
  //   RECOPILAR DATOS DEL FORMULARIO //
  /////////////////////////////////////
  function recopilarDatosFormulario() {
    console.log("📋 Recopilando datos del formulario...");

    const formData = new FormData();

    // Agregar todos los campos del formulario
    formData.append("op", "guardaryeditar");
    formData.append("id_estado_elemento", $("#id_estado_elemento").val() || "");
    formData.append("codigo_estado_elemento", $("#codigo_estado_elemento").val().trim());
    formData.append("descripcion_estado_elemento", $("#descripcion_estado_elemento").val().trim());
    formData.append("color_estado_elemento", $("#color_estado_elemento").val().trim());
    formData.append("permite_alquiler_estado_elemento", $("#permite_alquiler_estado_elemento").is(":checked") ? "1" : "0");
    formData.append("observaciones_estado_elemento", $("#observaciones_estado_elemento").val().trim() || "");
    formData.append("activo_estado_elemento", $("#activo_estado_elemento").is(":checked") ? "1" : "0");

    // Log de los datos para debug
    console.log("📦 Datos recopilados:");
    for (let [key, value] of formData.entries()) {
      console.log(`  ${key}: ${value}`);
    }

    return formData;
  }

  /////////////////////////////////////
  //   VERIFICAR CÓDIGO DUPLICADO    //
  /////////////////////////////////////
  function verificarCodigoDuplicado(codigo, descripcion, callback) {
    console.log(`🔍 Verificando duplicados - Código: ${codigo}, Descripción: ${descripcion}`);

    $.ajax({
      url: "../../controller/estado_elemento.php",
      method: "POST",
      data: {
        op: "verificarEstadoElemento",
        codigo_estado_elemento: codigo,
        descripcion_estado_elemento: descripcion,
        id_estado_elemento: $("#id_estado_elemento").val() || "",
      },
      dataType: "json",
      success: function (response) {
        console.log("📦 Respuesta verificación duplicados:", response);

        if (response.existe) {
          console.warn("⚠️ Estado de elemento duplicado encontrado");
          Swal.fire({
            icon: "warning",
            title: "Estado de elemento duplicado",
            html: `Ya existe un estado de elemento con:<br><strong>${response.campo}</strong>: <em>${response.valor}</em>`,
            confirmButtonText: "Entendido",
          });
          callback(false); // Indicar que hay duplicado
        } else {
          console.log("✅ No se encontraron duplicados");
          callback(true); // No hay duplicado, continuar
        }
      },
      error: function (xhr, status, error) {
        console.error("❌ Error al verificar duplicados:", error);
        console.error("📄 Respuesta del servidor:", xhr.responseText);
        toastr.error("Error al verificar duplicados", "Error");
        callback(false); // En caso de error, no continuar
      },
    });
  }

  /////////////////////////////////////
  //   GUARDAR ESTADO DE ELEMENTO    //
  /////////////////////////////////////
  function guardarEstadoElemento() {
    console.log("💾 Iniciando guardado del estado de elemento...");

    // Validar el formulario antes de enviar
    if (!validarFormulario()) {
      console.log("❌ Validación fallida, no se enviará el formulario");
      return;
    }

    // Verificar duplicados antes de guardar
    const codigo = $("#codigo_estado_elemento").val().trim();
    const descripcion = $("#descripcion_estado_elemento").val().trim();

    verificarCodigoDuplicado(codigo, descripcion, function (noDuplicado) {
      if (!noDuplicado) {
        console.log("❌ Se encontró un duplicado, no se guardará");
        return;
      }

      // Si no hay duplicado, proceder con el guardado
      console.log("✅ No hay duplicados, procediendo a guardar...");

      const formData = recopilarDatosFormulario();

      // Mostrar loading
      Swal.fire({
        title: "Guardando...",
        text: "Por favor espere",
        allowOutsideClick: false,
        didOpen: () => {
          Swal.showLoading();
        },
      });

      $.ajax({
        url: "../../controller/estado_elemento.php",
        method: "POST",
        data: formData,
        processData: false,
        contentType: false,
        dataType: "json",
        success: function (response) {
          console.log("📦 Respuesta del servidor:", response);

          Swal.close(); // Cerrar el loading

          if (response.status === "success") {
            console.log("✅ Estado de elemento guardado correctamente");

            Swal.fire({
              icon: "success",
              title: "¡Éxito!",
              text: response.message || "Estado de elemento guardado correctamente",
              timer: 2000,
              showConfirmButton: false,
            }).then(() => {
              // Redirigir a la página de listado
              window.location.href = "index.php";
            });
          } else {
            console.error("❌ Error al guardar:", response.message);
            Swal.fire({
              icon: "error",
              title: "Error",
              text: response.message || "Error al guardar el estado de elemento",
            });
          }
        },
        error: function (xhr, status, error) {
          console.error("❌ Error en la petición AJAX:", error);
          console.error("📄 Respuesta del servidor:", xhr.responseText);

          Swal.close();

          Swal.fire({
            icon: "error",
            title: "Error",
            text: "Error al comunicarse con el servidor. Por favor, inténtelo de nuevo.",
          });
        },
      });
    });
  }

  /////////////////////////////////////
  //   EVENTO BOTÓN GUARDAR          //
  /////////////////////////////////////
  $("#btnSalvarEstadoElemento").on("click", function (e) {
    e.preventDefault();
    console.log("🖱️ Click en botón Guardar Estado de Elemento");
    guardarEstadoElemento();
  });

  /////////////////////////////////////
  //   EVENTO ENTER EN FORMULARIO    //
  /////////////////////////////////////
  $("#formEstadoElemento").on("keypress", function (e) {
    if (e.which === 13) {
      // Enter
      e.preventDefault();
      console.log("⌨️ Enter presionado en el formulario");
      guardarEstadoElemento();
    }
  });

  /////////////////////////////////////
  //   FORMATO CÓDIGO (MAYÚSCULAS)   //
  /////////////////////////////////////
  $("#codigo_estado_elemento").on("input", function () {
    let valor = $(this).val();
    // Convertir a mayúsculas y eliminar espacios
    valor = valor.toUpperCase().replace(/\s+/g, "");
    $(this).val(valor);
  });

  /////////////////////////////////////
  //   SINCRONIZACIÓN COLOR          //
  /////////////////////////////////////
  $("#color_estado_elemento").on("input", function () {
    const color = $(this).val();
    $("#color_estado_elemento_text").val(color);
    console.log(`🎨 Color seleccionado: ${color}`);
  });

  $("#color_estado_elemento_text").on("input", function () {
    const color = $(this).val();
    if (/^#[0-9A-F]{6}$/i.test(color)) {
      $("#color_estado_elemento").val(color);
      console.log(`🎨 Color ingresado: ${color}`);
    }
  });

  /////////////////////////////////////
  //   ACTUALIZAR TEXTO SWITCH       //
  //   PERMITE ALQUILER              //
  /////////////////////////////////////
  $("#permite_alquiler_estado_elemento").on("change", function () {
    if ($(this).is(":checked")) {
      $("#permite_alquiler_label").text("Los elementos en este estado SÍ pueden ser alquilados")
        .removeClass("text-danger")
        .addClass("text-success");
      console.log("✅ Permite alquiler: SÍ");
    } else {
      $("#permite_alquiler_label").text("Los elementos en este estado NO pueden ser alquilados")
        .removeClass("text-success")
        .addClass("text-danger");
      console.log("❌ Permite alquiler: NO");
    }
  });

  /////////////////////////////////////
  //   CONTADOR DE CARACTERES        //
  //   OBSERVACIONES                 //
  /////////////////////////////////////
  $("#observaciones_estado_elemento").on("input", function () {
    const maxLength = 500;
    const currentLength = $(this).val().length;
    const $counter = $("#char-count-observaciones");

    $counter.text(`${currentLength}/${maxLength}`);

    // Cambiar color según la longitud
    if (currentLength > 400) {
      $counter.removeClass("text-muted text-warning").addClass("text-danger");
    } else if (currentLength > 250) {
      $counter.removeClass("text-muted text-danger").addClass("text-warning");
    } else {
      $counter.removeClass("text-warning text-danger").addClass("text-muted");
    }
  });

  /////////////////////////////////////
  //   VALIDACIÓN EN TIEMPO REAL     //
  /////////////////////////////////////
  $("#codigo_estado_elemento").on("blur", function () {
    const codigo = $(this).val().trim();
    if (codigo.length > 0 && (codigo.length < 2 || codigo.length > 20)) {
      $(this).addClass("is-invalid");
      toastr.warning("El código debe tener entre 2 y 20 caracteres", "Validación");
    } else {
      $(this).removeClass("is-invalid");
    }
  });

  $("#descripcion_estado_elemento").on("blur", function () {
    const descripcion = $(this).val().trim();
    if (descripcion.length > 0 && (descripcion.length < 3 || descripcion.length > 50)) {
      $(this).addClass("is-invalid");
      toastr.warning("La descripción debe tener entre 3 y 50 caracteres", "Validación");
    } else {
      $(this).removeClass("is-invalid");
    }
  });

  console.log("✅ Eventos del formulario configurados correctamente");
}); // de document.ready
