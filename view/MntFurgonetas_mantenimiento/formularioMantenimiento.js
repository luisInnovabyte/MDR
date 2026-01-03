$(document).ready(function () {
    // Obtener parámetros de la URL
    const urlParams = new URLSearchParams(window.location.search);
    const modo = urlParams.get('modo');
    const idMantenimiento = urlParams.get('id');
    const idFurgoneta = urlParams.get('id_furgoneta');

    // Configurar la página según el modo
    if (modo === 'editar' && idMantenimiento) {
        configurarModoEdicion(idMantenimiento);
    } else {
        configurarModoNuevo();
    }

    // Cargar información de la furgoneta
    if (idFurgoneta) {
        cargarDatosFurgoneta(idFurgoneta);
    }

    // Configurar validaciones del formulario
    configurarValidaciones();

    // Configurar el botón de guardar
    $('#btnGuardarMantenimiento').on('click', function() {
        guardarMantenimiento();
    });

    // Mostrar/ocultar sección ITV según el tipo de mantenimiento
    $('#tipo_mantenimiento').on('change', function() {
        toggleSeccionITV();
    });

    /////////////////////////////////////
    //   FUNCIONES DE CONFIGURACIÓN    //
    ///////////////////////////////////

    function configurarModoNuevo() {
        $('#page-title').text('Nuevo Mantenimiento');
        $('#breadcrumb-title').text('Nuevo Mantenimiento');
        $('#btnGuardarMantenimiento').html('<i class="fas fa-save me-2"></i>Guardar Mantenimiento');
        $('#estado_section').hide();
        
        // Establecer fecha actual
        const hoy = new Date().toISOString().split('T')[0];
        $('#fecha_mantenimiento').val(hoy);
    }

    function configurarModoEdicion(id) {
        $('#page-title').text('Editar Mantenimiento');
        $('#breadcrumb-title').text('Editar Mantenimiento');
        $('#btnGuardarMantenimiento').html('<i class="fas fa-save me-2"></i>Actualizar Mantenimiento');
        $('#estado_section').show();
        
        // Cargar los datos del mantenimiento
        cargarDatosMantenimiento(id);
    }

    function cargarDatosFurgoneta(id_furgoneta) {
        $.post("../../controller/furgoneta.php?op=mostrar", { id_furgoneta: id_furgoneta }, function (data) {
            if (data) {
                $('#matricula-furgoneta').text(data.matricula_furgoneta || 'Sin matrícula');
                $('#marca-furgoneta').text(data.marca_furgoneta || '');
                $('#modelo-furgoneta').text(data.modelo_furgoneta || '');
                $('#id-furgoneta').text(id_furgoneta);
            }
        }, 'json').fail(function() {
            $('#matricula-furgoneta').text('Error al cargar furgoneta');
        });
    }

    function cargarDatosMantenimiento(id) {
        $.post("../../controller/furgoneta_mantenimiento.php?op=mostrar", { id_mantenimiento: id }, function (data) {
            if (data) {
                $('#id_mantenimiento').val(data.id_mantenimiento);
                $('#id_furgoneta').val(data.id_furgoneta);
                $('#fecha_mantenimiento').val(data.fecha_mantenimiento);
                $('#tipo_mantenimiento').val(data.tipo_mantenimiento);
                $('#descripcion_mantenimiento').val(data.descripcion_mantenimiento);
                $('#kilometraje_mantenimiento').val(data.kilometraje_mantenimiento);
                $('#costo_mantenimiento').val(data.costo_mantenimiento);
                $('#numero_factura_mantenimiento').val(data.numero_factura_mantenimiento);
                $('#taller_mantenimiento').val(data.taller_mantenimiento);
                $('#telefono_taller_mantenimiento').val(data.telefono_taller_mantenimiento);
                $('#direccion_taller_mantenimiento').val(data.direccion_taller_mantenimiento);
                
                // Campos ITV
                if (data.resultado_itv) {
                    $('#resultado_itv').val(data.resultado_itv);
                }
                if (data.fecha_proxima_itv) {
                    $('#fecha_proxima_itv').val(data.fecha_proxima_itv);
                }
                
                $('#garantia_hasta_mantenimiento').val(data.garantia_hasta_mantenimiento);
                $('#observaciones_mantenimiento').val(data.observaciones_mantenimiento);
                
                // Mostrar/ocultar sección ITV
                toggleSeccionITV();
                
                // Configurar estado visual
                const esActivo = data.activo_mantenimiento == 1;
                $('#activo_mantenimiento_display').prop('checked', esActivo);
                $('#estado_texto').text(esActivo ? 'Mantenimiento Activo' : 'Mantenimiento Inactivo');
                $('#estado_descripcion').text(esActivo ? 'El registro está activo y visible.' : 'El registro está inactivo.');
            }
        }, 'json').fail(function() {
            Swal.fire('Error', 'No se pudieron cargar los datos del mantenimiento', 'error');
        });
    }

    function toggleSeccionITV() {
        const tipoMantenimiento = $('#tipo_mantenimiento').val();
        if (tipoMantenimiento === 'itv') {
            $('#seccion_itv').show();
            // Hacer campos ITV obligatorios
            $('#resultado_itv').prop('required', true);
            $('#fecha_proxima_itv').prop('required', true);
        } else {
            $('#seccion_itv').hide();
            // Hacer campos ITV opcionales
            $('#resultado_itv').prop('required', false);
            $('#fecha_proxima_itv').prop('required', false);
            // Limpiar valores si no es ITV
            $('#resultado_itv').val('');
            $('#fecha_proxima_itv').val('');
        }
    }

    function configurarValidaciones() {
        // Validación en tiempo real para fecha
        $('#fecha_mantenimiento').on('input blur', function() {
            validarCampo($(this), validarFecha);
        });

        // Validación en tiempo real para tipo
        $('#tipo_mantenimiento').on('change blur', function() {
            validarCampoRequerido($(this));
        });

        // Validación en tiempo real para descripción
        $('#descripcion_mantenimiento').on('input blur', function() {
            validarCampo($(this), validarDescripcion);
        });

        // Validación en tiempo real para kilometraje
        $('#kilometraje_mantenimiento').on('input blur', function() {
            validarCampo($(this), validarKilometraje, false); // false = no obligatorio
        });

        // Validación en tiempo real para costo
        $('#costo_mantenimiento').on('input blur', function() {
            validarCampo($(this), validarCosto, false);
        });

        // Validación para campos ITV cuando es tipo ITV
        $('#resultado_itv, #fecha_proxima_itv').on('input blur change', function() {
            if ($('#tipo_mantenimiento').val() === 'itv') {
                validarCampoRequerido($(this));
            }
        });

        // Validación de fecha de garantía
        $('#garantia_hasta_mantenimiento').on('input blur', function() {
            validarCampo($(this), validarFechaGarantia, false);
        });
    }

    /////////////////////////////////////
    //   FUNCIONES DE VALIDACIÓN       //
    ///////////////////////////////////

    function validarCampo($campo, funcionValidacion, obligatorio = true) {
        const valor = $campo.val().trim();
        
        if (obligatorio && !valor) {
            mostrarError($campo, 'Este campo es obligatorio');
            return false;
        }
        
        if (valor && !funcionValidacion(valor)) {
            return false;
        }
        
        mostrarExito($campo);
        return true;
    }

    function validarCampoRequerido($campo) {
        const valor = $campo.val();
        
        if (!valor || valor === '') {
            mostrarError($campo, 'Este campo es obligatorio');
            return false;
        }
        
        mostrarExito($campo);
        return true;
    }

    function validarFecha(fecha) {
        if (!fecha) {
            mostrarError($('#fecha_mantenimiento'), 'La fecha es obligatoria');
            return false;
        }
        
        const fechaMantenimiento = new Date(fecha);
        const hoy = new Date();
        hoy.setHours(0, 0, 0, 0);
        
        if (fechaMantenimiento > hoy) {
            mostrarError($('#fecha_mantenimiento'), 'La fecha no puede ser futura');
            return false;
        }
        
        return true;
    }

    function validarDescripcion(descripcion) {
        if (descripcion.length < 5 || descripcion.length > 1000) {
            mostrarError($('#descripcion_mantenimiento'), 'La descripción debe tener entre 5 y 1000 caracteres');
            return false;
        }
        return true;
    }

    function validarKilometraje(kilometraje) {
        const km = parseFloat(kilometraje);
        
        if (isNaN(km) || km < 0) {
            mostrarError($('#kilometraje_mantenimiento'), 'Ingrese un kilometraje válido (número positivo)');
            return false;
        }
        
        if (km > 9999999) {
            mostrarError($('#kilometraje_mantenimiento'), 'El kilometraje es demasiado alto');
            return false;
        }
        
        return true;
    }

    function validarCosto(costo) {
        const costoNum = parseFloat(costo);
        
        if (isNaN(costoNum) || costoNum < 0) {
            mostrarError($('#costo_mantenimiento'), 'Ingrese un costo válido (número positivo)');
            return false;
        }
        
        if (costoNum > 999999.99) {
            mostrarError($('#costo_mantenimiento'), 'El costo es demasiado alto');
            return false;
        }
        
        return true;
    }

    function validarFechaGarantia(fechaGarantia) {
        const fechaMant = new Date($('#fecha_mantenimiento').val());
        const fechaGar = new Date(fechaGarantia);
        
        if (fechaGar <= fechaMant) {
            mostrarError($('#garantia_hasta_mantenimiento'), 'La fecha de garantía debe ser posterior a la fecha del mantenimiento');
            return false;
        }
        
        return true;
    }

    function mostrarError($campo, mensaje) {
        $campo.addClass('is-invalid').removeClass('is-valid');
        $campo.siblings('.invalid-feedback').text(mensaje);
    }

    function mostrarExito($campo) {
        $campo.addClass('is-valid').removeClass('is-invalid');
    }

    /////////////////////////////////////
    //   FUNCIÓN GUARDAR MANTENIMIENTO //
    ///////////////////////////////////

    function guardarMantenimiento() {
        // Validar todos los campos obligatorios
        let esValido = true;
        
        esValido &= validarCampo($('#fecha_mantenimiento'), validarFecha);
        esValido &= validarCampoRequerido($('#tipo_mantenimiento'));
        esValido &= validarCampo($('#descripcion_mantenimiento'), validarDescripcion);
        
        // Validar campos opcionales solo si tienen valor
        const kilometraje = $('#kilometraje_mantenimiento').val().trim();
        if (kilometraje) {
            esValido &= validarCampo($('#kilometraje_mantenimiento'), validarKilometraje, false);
        }

        const costo = $('#costo_mantenimiento').val().trim();
        if (costo) {
            esValido &= validarCampo($('#costo_mantenimiento'), validarCosto, false);
        }

        const garantia = $('#garantia_hasta_mantenimiento').val().trim();
        if (garantia) {
            esValido &= validarCampo($('#garantia_hasta_mantenimiento'), validarFechaGarantia, false);
        }

        // Validar campos ITV si el tipo es ITV
        if ($('#tipo_mantenimiento').val() === 'itv') {
            esValido &= validarCampoRequerido($('#resultado_itv'));
            esValido &= validarCampoRequerido($('#fecha_proxima_itv'));
        }

        if (!esValido) {
            Swal.fire('Error de validación', 'Por favor, corrija los errores en el formulario', 'warning');
            return;
        }

        // Recoger datos del formulario
        const formData = new FormData($('#formMantenimiento')[0]);

        // Mostrar loading
        Swal.fire({
            title: 'Guardando...',
            html: 'Procesando la información del mantenimiento',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Enviar datos
        $.ajax({
            url: '../../controller/furgoneta_mantenimiento.php?op=guardaryeditar',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                Swal.close();
                
                if (response.status === 'success' || response.success) {
                    Swal.fire({
                        title: 'Éxito',
                        text: response.message || 'Mantenimiento guardado correctamente',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        // Redirigir al listado
                        window.location.href = `index.php?id_furgoneta=${idFurgoneta}`;
                    });
                } else {
                    Swal.fire('Error', response.message || 'Error al guardar el mantenimiento', 'error');
                }
            },
            error: function(xhr, status, error) {
                Swal.close();
                console.error('Error:', xhr.responseText);
                Swal.fire('Error', 'Hubo un problema al guardar el mantenimiento', 'error');
            }
        });
    }

    /////////////////////////////////////
    //   FORMATEO DE CAMPOS            //
    ///////////////////////////////////

    // Formatear kilometraje (solo números)
    $('#kilometraje_mantenimiento').on('input', function() {
        let valor = $(this).val();
        valor = valor.replace(/[^\d]/g, '');
        $(this).val(valor);
    });

    // Formatear costo (solo números y punto decimal)
    $('#costo_mantenimiento').on('input', function() {
        let valor = $(this).val();
        // Permitir números y un solo punto decimal
        valor = valor.replace(/[^\d.]/g, '');
        // Evitar múltiples puntos decimales
        const partes = valor.split('.');
        if (partes.length > 2) {
            valor = partes[0] + '.' + partes.slice(1).join('');
        }
        $(this).val(valor);
    });

    // Convertir texto a formato título (taller, descripción)
    $('#taller_mantenimiento').on('input', function() {
        let valor = $(this).val();
        valor = valor.replace(/\b\w/g, function(l) { return l.toUpperCase(); });
        $(this).val(valor);
    });

    // Formatear teléfono taller
    $('#telefono_taller_mantenimiento').on('input', function() {
        let valor = $(this).val();
        // Permitir solo números, espacios, guiones, paréntesis y símbolo +
        valor = valor.replace(/[^\d\s\-\+\(\)]/g, '');
        $(this).val(valor);
    });

    // Calcular fecha de garantía sugerida (3 meses desde el mantenimiento)
    $('#fecha_mantenimiento').on('change', function() {
        const fechaMant = new Date($(this).val());
        if (!isNaN(fechaMant.getTime())) {
            const fechaGarantia = new Date(fechaMant);
            fechaGarantia.setMonth(fechaGarantia.getMonth() + 3);
            
            // Solo sugerir si el campo está vacío
            if (!$('#garantia_hasta_mantenimiento').val()) {
                const fechaFormatada = fechaGarantia.toISOString().split('T')[0];
                $('#garantia_hasta_mantenimiento').val(fechaFormatada);
            }
        }
    });

    // Calcular fecha próxima ITV sugerida (2 años desde el mantenimiento si es ITV favorable)
    $('#resultado_itv').on('change', function() {
        if ($(this).val() === 'favorable') {
            const fechaMant = new Date($('#fecha_mantenimiento').val());
            if (!isNaN(fechaMant.getTime())) {
                const fechaProxITV = new Date(fechaMant);
                fechaProxITV.setFullYear(fechaProxITV.getFullYear() + 2);
                
                // Solo sugerir si el campo está vacío
                if (!$('#fecha_proxima_itv').val()) {
                    const fechaFormatada = fechaProxITV.toISOString().split('T')[0];
                    $('#fecha_proxima_itv').val(fechaFormatada);
                }
            }
        }
    });

    // Inicializar la visibilidad de la sección ITV
    toggleSeccionITV();

});
