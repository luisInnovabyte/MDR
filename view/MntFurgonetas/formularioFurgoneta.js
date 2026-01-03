// ========================================
// FORMULARIO FURGONETA - JAVASCRIPT
// ========================================

$(document).ready(function () {
    // =====================
    //   Variables globales
    // =====================
    let formOriginalValues = {}; // Valores originales del formulario
    let formSaved = false; // Indica si el formulario se guardó

    // =====================
    //   Inicializar FormValidator
    // =====================
    let formValidator = null;
    if (typeof FormValidator !== 'undefined') {
        formValidator = new FormValidator('formFurgoneta', {
            matricula_furgoneta: {
                required: true
            }
        });
    }

    // =====================
    //   Detectar modo del formulario
    // =====================
    const modo = getUrlParameter('modo');
    const idFurgoneta = getUrlParameter('id');

    if (modo === 'editar' && idFurgoneta) {
        cargarDatosFurgoneta(idFurgoneta);
    } else if (modo === 'nuevo') {
        // Estado por defecto en modo nuevo
        $('#estado_furgoneta').val('operativa');
        // Capturar valores originales
        setTimeout(function () {
            captureOriginalValues();
        }, 500);
    }

    // =====================
    //   Validación de matrícula única
    // =====================
    $('#matricula_furgoneta').on('blur', function () {
        const matricula = $(this).val().trim();
        const id = $('#id_furgoneta').val();

        if (matricula !== '') {
            verificarMatriculaExistente(matricula, id);
        }
    });

    // =====================
    //   Cambio en estado del switch (solo en modo editar)
    // =====================
    $('#activo_furgoneta').on('change', function () {
        if ($(this).is(':checked')) {
            $('#estado-text').text('Furgoneta Activa');
        } else {
            $('#estado-text').text('Furgoneta Inactiva');
        }
    });

    //*****************************************************/
    //   CAPTURAR EL CLICK EN EL BOTÓN DE SALVAR FURGONETA
    //*****************************************************/

    $(document).on('click', '#btnSalvarFurgoneta', function (event) {
        event.preventDefault();

        // Obtener valores del formulario
        var id_furgoneta = $('#id_furgoneta').val().trim();
        var matricula_furgoneta = $('#matricula_furgoneta').val().trim().toUpperCase();
        var marca_furgoneta = $('#marca_furgoneta').val().trim();
        var modelo_furgoneta = $('#modelo_furgoneta').val().trim();
        var anio_furgoneta = $('#anio_furgoneta').val().trim();
        var numero_bastidor_furgoneta = $('#numero_bastidor_furgoneta').val().trim();
        var kilometros_entre_revisiones_furgoneta = $('#kilometros_entre_revisiones_furgoneta').val().trim();
        var fecha_proxima_itv_furgoneta = $('#fecha_proxima_itv_furgoneta').val().trim();
        var fecha_vencimiento_seguro_furgoneta = $('#fecha_vencimiento_seguro_furgoneta').val().trim();
        var compania_seguro_furgoneta = $('#compania_seguro_furgoneta').val().trim();
        var numero_poliza_seguro_furgoneta = $('#numero_poliza_seguro_furgoneta').val().trim();
        var capacidad_carga_kg_furgoneta = $('#capacidad_carga_kg_furgoneta').val().trim();
        var capacidad_carga_m3_furgoneta = $('#capacidad_carga_m3_furgoneta').val().trim();
        var tipo_combustible_furgoneta = $('#tipo_combustible_furgoneta').val();
        var consumo_medio_furgoneta = $('#consumo_medio_furgoneta').val().trim();
        var taller_habitual_furgoneta = $('#taller_habitual_furgoneta').val().trim();
        var telefono_taller_furgoneta = $('#telefono_taller_furgoneta').val().trim();
        var estado_furgoneta = $('#estado_furgoneta').val();
        var observaciones_furgoneta = $('#observaciones_furgoneta').val().trim();

        // Obtener el estado activo
        var activo_furgoneta;
        if (id_furgoneta) {
            // En edición: usar el valor del switch
            activo_furgoneta = $('#activo_furgoneta').is(':checked') ? '1' : '0';
        } else {
            // Nuevo: siempre activo
            activo_furgoneta = '1';
        }

        // Validar el formulario
        if (formValidator && !formValidator.validateForm(event)) {
            toastr.error('Por favor, corrija los errores en el formulario.', 'Error de Validación');
            return;
        }

        // Validación básica de matrícula
        if (!matricula_furgoneta) {
            toastr.error('La matrícula es obligatoria', 'Error de Validación');
            $('#matricula_furgoneta').focus();
            return;
        }

        // Verificar furgoneta primero
        verificarFurgonetaExistente(
            id_furgoneta,
            matricula_furgoneta,
            marca_furgoneta,
            modelo_furgoneta,
            anio_furgoneta,
            numero_bastidor_furgoneta,
            kilometros_entre_revisiones_furgoneta,
            fecha_proxima_itv_furgoneta,
            fecha_vencimiento_seguro_furgoneta,
            compania_seguro_furgoneta,
            numero_poliza_seguro_furgoneta,
            capacidad_carga_kg_furgoneta,
            capacidad_carga_m3_furgoneta,
            tipo_combustible_furgoneta,
            consumo_medio_furgoneta,
            taller_habitual_furgoneta,
            telefono_taller_furgoneta,
            estado_furgoneta,
            observaciones_furgoneta,
            activo_furgoneta
        );
    });

    function verificarFurgonetaExistente(
        id_furgoneta,
        matricula_furgoneta,
        marca_furgoneta,
        modelo_furgoneta,
        anio_furgoneta,
        numero_bastidor_furgoneta,
        kilometros_entre_revisiones_furgoneta,
        fecha_proxima_itv_furgoneta,
        fecha_vencimiento_seguro_furgoneta,
        compania_seguro_furgoneta,
        numero_poliza_seguro_furgoneta,
        capacidad_carga_kg_furgoneta,
        capacidad_carga_m3_furgoneta,
        tipo_combustible_furgoneta,
        consumo_medio_furgoneta,
        taller_habitual_furgoneta,
        telefono_taller_furgoneta,
        estado_furgoneta,
        observaciones_furgoneta,
        activo_furgoneta
    ) {
        console.log('🔍 Verificando furgoneta:', { matricula: matricula_furgoneta, id: id_furgoneta });

        $.ajax({
            url: "../../controller/furgoneta.php?op=verificar",
            type: "POST",
            data: {
                matricula_furgoneta: matricula_furgoneta,
                id_furgoneta: id_furgoneta || ''
            },
            dataType: "json",
            success: function (response) {
                console.log('📋 Respuesta verificación:', response);

                // La respuesta del modelo es { existe: true/false }
                if (response.existe === false) {
                    // No existe, podemos guardar
                    console.log('✅ Furgoneta no existe, procediendo a guardar');
                    guardarFurgoneta(
                        id_furgoneta,
                        matricula_furgoneta,
                        marca_furgoneta,
                        modelo_furgoneta,
                        anio_furgoneta,
                        numero_bastidor_furgoneta,
                        kilometros_entre_revisiones_furgoneta,
                        fecha_proxima_itv_furgoneta,
                        fecha_vencimiento_seguro_furgoneta,
                        compania_seguro_furgoneta,
                        numero_poliza_seguro_furgoneta,
                        capacidad_carga_kg_furgoneta,
                        capacidad_carga_m3_furgoneta,
                        tipo_combustible_furgoneta,
                        consumo_medio_furgoneta,
                        taller_habitual_furgoneta,
                        telefono_taller_furgoneta,
                        estado_furgoneta,
                        observaciones_furgoneta,
                        activo_furgoneta
                    );
                } else {
                    // Ya existe
                    console.log('❌ Furgoneta ya existe');
                    mostrarErrorFurgonetaExistente("Ya existe una furgoneta con la matrícula '" + matricula_furgoneta + "'");
                }
            },
            error: function (xhr, status, error) {
                console.error('Error en verificación:', error);
                console.error('Respuesta del servidor:', xhr.responseText);
                toastr.error('Error al verificar la furgoneta. Intente nuevamente.', 'Error');
            }
        });
    }

    function mostrarErrorFurgonetaExistente(mensaje) {
        console.log("Furgoneta duplicada detectada:", mensaje);
        Swal.fire({
            title: 'Furgoneta duplicada',
            text: mensaje,
            icon: 'warning',
            confirmButtonText: 'Entendido'
        });
    }

    function guardarFurgoneta(
        id_furgoneta,
        matricula_furgoneta,
        marca_furgoneta,
        modelo_furgoneta,
        anio_furgoneta,
        numero_bastidor_furgoneta,
        kilometros_entre_revisiones_furgoneta,
        fecha_proxima_itv_furgoneta,
        fecha_vencimiento_seguro_furgoneta,
        compania_seguro_furgoneta,
        numero_poliza_seguro_furgoneta,
        capacidad_carga_kg_furgoneta,
        capacidad_carga_m3_furgoneta,
        tipo_combustible_furgoneta,
        consumo_medio_furgoneta,
        taller_habitual_furgoneta,
        telefono_taller_furgoneta,
        estado_furgoneta,
        observaciones_furgoneta,
        activo_furgoneta
    ) {
        // Mostrar indicador de carga
        $('#btnSalvarFurgoneta').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Guardando...');

        // Preparar los datos para enviar - IMPORTANTE: Usar FormData para enviar todos los campos
        const formData = new FormData();
        formData.append('id_furgoneta', id_furgoneta);
        formData.append('matricula_furgoneta', matricula_furgoneta);
        formData.append('marca_furgoneta', marca_furgoneta);
        formData.append('modelo_furgoneta', modelo_furgoneta);
        formData.append('anio_furgoneta', anio_furgoneta);
        formData.append('numero_bastidor_furgoneta', numero_bastidor_furgoneta);
        formData.append('kilometros_entre_revisiones_furgoneta', kilometros_entre_revisiones_furgoneta);
        formData.append('fecha_proxima_itv_furgoneta', fecha_proxima_itv_furgoneta);
        formData.append('fecha_vencimiento_seguro_furgoneta', fecha_vencimiento_seguro_furgoneta);
        formData.append('compania_seguro_furgoneta', compania_seguro_furgoneta);
        formData.append('numero_poliza_seguro_furgoneta', numero_poliza_seguro_furgoneta);
        formData.append('capacidad_carga_kg_furgoneta', capacidad_carga_kg_furgoneta);
        formData.append('capacidad_carga_m3_furgoneta', capacidad_carga_m3_furgoneta);
        formData.append('tipo_combustible_furgoneta', tipo_combustible_furgoneta || '');
        formData.append('consumo_medio_furgoneta', consumo_medio_furgoneta);
        formData.append('taller_habitual_furgoneta', taller_habitual_furgoneta);
        formData.append('telefono_taller_furgoneta', telefono_taller_furgoneta);
        formData.append('estado_furgoneta', estado_furgoneta);
        formData.append('observaciones_furgoneta', observaciones_furgoneta);
        formData.append('activo_furgoneta', activo_furgoneta);

        console.log('💾 Enviando con FormData');

        $.ajax({
            url: "../../controller/furgoneta.php?op=guardaryeditar",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            dataType: "json",
            success: function (res) {
                console.log('📋 Respuesta del guardado:', res);

                if (res.success) {
                    // Marcar como guardado para evitar la alerta de salida
                    formSaved = true;

                    toastr.success(res.message || "Furgoneta guardada correctamente");

                    // Redirigir al listado después de un breve delay
                    setTimeout(() => {
                        window.location.href = 'index.php';
                    }, 1500);
                } else {
                    toastr.error(res.message || "Error al guardar la furgoneta");
                    // Restaurar botón
                    $('#btnSalvarFurgoneta').prop('disabled', false).html('<i class="fas fa-save me-2"></i>Guardar Furgoneta');
                }
            },
            error: function (xhr, status, error) {
                console.error("Error en guardado:", error);
                console.error("Estado:", status);
                console.error("Respuesta del servidor:", xhr.responseText);

                let errorMsg = 'No se pudo guardar la furgoneta.';
                try {
                    // Intentar parsear la respuesta como JSON
                    const response = JSON.parse(xhr.responseText);
                    errorMsg = response.message || errorMsg;
                } catch (e) {
                    // Si no es JSON válido, usar el error original
                    errorMsg += ' Error: ' + error;
                    if (xhr.responseText) {
                        errorMsg += ' Respuesta del servidor: ' + xhr.responseText.substring(0, 200);
                    }
                }

                Swal.fire('Error', errorMsg, 'error');
                // Restaurar botón
                $('#btnSalvarFurgoneta').prop('disabled', false).html('<i class="fas fa-save me-2"></i>Guardar Furgoneta');
            }
        });
    }

    // =====================
    //   Advertencia al salir sin guardar
    // =====================
    window.addEventListener('beforeunload', function (e) {
        if (!formSaved && hasFormChanged()) {
            e.preventDefault();
            e.returnValue = '';
            return '';
        }
    });

    // =====================
    //   FUNCIONES
    // =====================

    /**
     * Obtener parámetro de URL
     */
    function getUrlParameter(name) {
        name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
        const regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
        const results = regex.exec(location.search);
        return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
    }

    /**
     * Cargar datos de furgoneta para edición
     */
    function cargarDatosFurgoneta(id) {
        $.ajax({
            url: '../../controller/furgoneta.php?op=mostrar',
            type: 'POST',
            data: { id_furgoneta: id },
            dataType: 'json',
            success: function (data) {
                if (data && data.id_furgoneta) {
                    // Llenar campos
                    $('#id_furgoneta').val(data.id_furgoneta);
                    $('#matricula_furgoneta').val(data.matricula_furgoneta);
                    $('#marca_furgoneta').val(data.marca_furgoneta);
                    $('#modelo_furgoneta').val(data.modelo_furgoneta);
                    $('#anio_furgoneta').val(data.anio_furgoneta);
                    $('#numero_bastidor_furgoneta').val(data.numero_bastidor_furgoneta);
                    $('#kilometros_entre_revisiones_furgoneta').val(data.kilometros_entre_revisiones_furgoneta);
                    $('#fecha_proxima_itv_furgoneta').val(data.fecha_proxima_itv_furgoneta);
                    $('#fecha_vencimiento_seguro_furgoneta').val(data.fecha_vencimiento_seguro_furgoneta);
                    $('#compania_seguro_furgoneta').val(data.compania_seguro_furgoneta);
                    $('#numero_poliza_seguro_furgoneta').val(data.numero_poliza_seguro_furgoneta);
                    $('#capacidad_carga_kg_furgoneta').val(data.capacidad_carga_kg_furgoneta);
                    $('#capacidad_carga_m3_furgoneta').val(data.capacidad_carga_m3_furgoneta);
                    $('#tipo_combustible_furgoneta').val(data.tipo_combustible_furgoneta);
                    $('#consumo_medio_furgoneta').val(data.consumo_medio_furgoneta);
                    $('#taller_habitual_furgoneta').val(data.taller_habitual_furgoneta);
                    $('#telefono_taller_furgoneta').val(data.telefono_taller_furgoneta);
                    $('#estado_furgoneta').val(data.estado_furgoneta);
                    $('#observaciones_furgoneta').val(data.observaciones_furgoneta);

                    // Estado activo (switch)
                    if (data.activo_furgoneta == 1) {
                        $('#activo_furgoneta').prop('checked', true);
                        $('#estado-text').text('Furgoneta Activa');
                    } else {
                        $('#activo_furgoneta').prop('checked', false);
                        $('#estado-text').text('Furgoneta Inactiva');
                    }

                    // Disparar evento change en campos de select
                    $('#tipo_combustible_furgoneta').trigger('change');
                    $('#estado_furgoneta').trigger('change');

                    // Capturar valores originales después de cargar
                    setTimeout(function () {
                        captureOriginalValues();
                    }, 500);
                } else {
                    toastr.error('No se pudieron cargar los datos de la furgoneta', 'Error');
                    setTimeout(function () {
                        window.location.href = 'index.php';
                    }, 2000);
                }
            },
            error: function (xhr, status, error) {
                console.error('Error:', error);
                toastr.error('Error al cargar los datos de la furgoneta', 'Error');
                setTimeout(function () {
                    window.location.href = 'index.php';
                }, 2000);
            }
        });
    }

    /**
     * Verificar si la matrícula existe (onBlur validation)
     */
    function verificarMatriculaExistente(matricula, id) {
        $.ajax({
            url: '../../controller/furgoneta.php?op=verificar',
            type: 'POST',
            data: {
                matricula_furgoneta: matricula,
                id_furgoneta: id || ''
            },
            dataType: 'json',
            success: function (response) {
                if (response.existe) {
                    $('#matricula_furgoneta').addClass('is-invalid');
                    $('#matricula_furgoneta').after('<div class="invalid-feedback d-block">La matrícula ya existe</div>');
                    toastr.warning('La matrícula ya existe en el sistema', 'Advertencia');
                } else {
                    $('#matricula_furgoneta').removeClass('is-invalid');
                    $('#matricula_furgoneta').siblings('.invalid-feedback').remove();
                }
            },
            error: function () {
                console.error('Error al verificar la matrícula');
            }
        });
    }

    /**
     * Capturar valores originales del formulario
     */
    function captureOriginalValues() {
        formOriginalValues = {
            matricula_furgoneta: $('#matricula_furgoneta').val(),
            marca_furgoneta: $('#marca_furgoneta').val(),
            modelo_furgoneta: $('#modelo_furgoneta').val(),
            anio_furgoneta: $('#anio_furgoneta').val(),
            numero_bastidor_furgoneta: $('#numero_bastidor_furgoneta').val(),
            kilometros_entre_revisiones_furgoneta: $('#kilometros_entre_revisiones_furgoneta').val(),
            fecha_proxima_itv_furgoneta: $('#fecha_proxima_itv_furgoneta').val(),
            fecha_vencimiento_seguro_furgoneta: $('#fecha_vencimiento_seguro_furgoneta').val(),
            compania_seguro_furgoneta: $('#compania_seguro_furgoneta').val(),
            numero_poliza_seguro_furgoneta: $('#numero_poliza_seguro_furgoneta').val(),
            capacidad_carga_kg_furgoneta: $('#capacidad_carga_kg_furgoneta').val(),
            capacidad_carga_m3_furgoneta: $('#capacidad_carga_m3_furgoneta').val(),
            tipo_combustible_furgoneta: $('#tipo_combustible_furgoneta').val(),
            consumo_medio_furgoneta: $('#consumo_medio_furgoneta').val(),
            taller_habitual_furgoneta: $('#taller_habitual_furgoneta').val(),
            telefono_taller_furgoneta: $('#telefono_taller_furgoneta').val(),
            estado_furgoneta: $('#estado_furgoneta').val(),
            observaciones_furgoneta: $('#observaciones_furgoneta').val(),
            activo_furgoneta: $('#activo_furgoneta').is(':checked') ? '1' : '0'
        };
    }

    /**
     * Verificar si hubo cambios en el formulario
     */
    function hasFormChanged() {
        const currentValues = {
            matricula_furgoneta: $('#matricula_furgoneta').val(),
            marca_furgoneta: $('#marca_furgoneta').val(),
            modelo_furgoneta: $('#modelo_furgoneta').val(),
            anio_furgoneta: $('#anio_furgoneta').val(),
            numero_bastidor_furgoneta: $('#numero_bastidor_furgoneta').val(),
            kilometros_entre_revisiones_furgoneta: $('#kilometros_entre_revisiones_furgoneta').val(),
            fecha_proxima_itv_furgoneta: $('#fecha_proxima_itv_furgoneta').val(),
            fecha_vencimiento_seguro_furgoneta: $('#fecha_vencimiento_seguro_furgoneta').val(),
            compania_seguro_furgoneta: $('#compania_seguro_furgoneta').val(),
            numero_poliza_seguro_furgoneta: $('#numero_poliza_seguro_furgoneta').val(),
            capacidad_carga_kg_furgoneta: $('#capacidad_carga_kg_furgoneta').val(),
            capacidad_carga_m3_furgoneta: $('#capacidad_carga_m3_furgoneta').val(),
            tipo_combustible_furgoneta: $('#tipo_combustible_furgoneta').val(),
            consumo_medio_furgoneta: $('#consumo_medio_furgoneta').val(),
            taller_habitual_furgoneta: $('#taller_habitual_furgoneta').val(),
            telefono_taller_furgoneta: $('#telefono_taller_furgoneta').val(),
            estado_furgoneta: $('#estado_furgoneta').val(),
            observaciones_furgoneta: $('#observaciones_furgoneta').val(),
            activo_furgoneta: $('#activo_furgoneta').is(':checked') ? '1' : '0'
        };

        // Comparar con valores originales
        for (let key in formOriginalValues) {
            if (formOriginalValues[key] !== currentValues[key]) {
                return true;
            }
        }

        return false;
    }

}); // Fin $(document).ready