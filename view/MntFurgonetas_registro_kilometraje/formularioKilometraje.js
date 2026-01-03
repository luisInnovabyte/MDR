// =====================================================
// FORMULARIO - Registro de Kilometraje
// =====================================================

$(document).ready(function () {
    // Obtener id_furgoneta de la URL
    const urlParams = new URLSearchParams(window.location.search);
    const id_furgoneta = urlParams.get('id_furgoneta');

    if (!id_furgoneta) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se especificó la furgoneta',
            confirmButtonText: 'Volver',
        }).then(() => {
            window.location.href = '../MntFurgonetas/index.php';
        });
        return;
    }

    // Establecer id_furgoneta en el formulario
    $('#id_furgoneta').val(id_furgoneta);

    // Cargar información de la furgoneta
    cargarDatosFurgoneta(id_furgoneta);

    // Establecer fecha actual por defecto
    const hoy = new Date().toISOString().split('T')[0];
    $('#fecha_registro_km').val(hoy);
    $('#fecha_registro_km').attr('max', hoy);

    // Evento submit del formulario
    $('#formKilometraje').on('submit', function (e) {
        e.preventDefault();
        guardarKilometraje();
    });

    // Validación en tiempo real del kilometraje
    $('#kilometraje_registrado_km').on('blur', function () {
        validarKilometraje();
    });

    // Validación en tiempo real de la fecha
    $('#fecha_registro_km').on('change', function () {
        validarFecha();
    });
});

// =====================================================
// CARGAR DATOS DE LA FURGONETA Y ÚLTIMO REGISTRO
// =====================================================
function cargarDatosFurgoneta(id_furgoneta) {
    // Cargar datos básicos de la furgoneta
    $.ajax({
        url: '../../controller/furgoneta.php?op=mostrar',
        type: 'POST',
        data: { id_furgoneta: id_furgoneta },
        dataType: 'json',
        success: function (data) {
            if (data) {
                $('#matricula-furgoneta').text(data.matricula_furgoneta || '-');
                $('#marca-furgoneta').text(data.marca_furgoneta || '');
                $('#modelo-furgoneta').text(data.modelo_furgoneta || '');
                $('#id-furgoneta').text(id_furgoneta);
            }
        },
        error: function () {
            console.error('Error al cargar datos de la furgoneta');
        }
    });

    // Cargar último registro de kilometraje
    $.ajax({
        url: '../../controller/furgoneta_registro_kilometraje.php?op=ultimo_registro',
        type: 'POST',
        data: { id_furgoneta: id_furgoneta },
        dataType: 'json',
        success: function (response) {
            if (response.success && response.data) {
                const data = response.data;
                const km = Number(data.kilometraje_registrado_km || 0);
                
                $('#km-actual').text(km.toLocaleString('es-ES'));
                $('#validacion-km-minimo').text(km.toLocaleString('es-ES') + ' km');
                
                // Establecer el mínimo para el nuevo registro
                $('#kilometraje_registrado_km').attr('min', km);
                $('#kilometraje_registrado_km').attr('placeholder', 'Mínimo: ' + km.toLocaleString('es-ES') + ' km');

                if (data.fecha_registro_km) {
                    // Crear fecha del último registro sin problemas de timezone
                    const fechaParts = data.fecha_registro_km.split('-');
                    const fecha = new Date(fechaParts[0], fechaParts[1] - 1, fechaParts[2]);
                    
                    $('#fecha-ultimo-registro').text(fecha.toLocaleDateString('es-ES'));
                    $('#validacion-fecha-minima').text(fecha.toLocaleDateString('es-ES'));
                    
                    // Establecer la fecha mínima (mismo formato que el input date)
                    const year = fecha.getFullYear();
                    const month = String(fecha.getMonth() + 1).padStart(2, '0');
                    const day = String(fecha.getDate()).padStart(2, '0');
                    const fechaMinima = `${year}-${month}-${day}`;
                    
                    $('#fecha_registro_km').attr('min', fechaMinima);
                }
            } else {
                // No hay registros previos
                $('#km-actual').text('Sin registros previos');
                $('#fecha-ultimo-registro').text('Sin registros previos');
                $('#validacion-km-minimo').text('0 km (primer registro)');
                $('#validacion-fecha-minima').text('Sin restricción');
                $('#kilometraje_registrado_km').attr('min', 0);
            }
        },
        error: function () {
            console.error('Error al cargar último registro');
            $('#km-actual').text('Error al cargar');
            $('#fecha-ultimo-registro').text('Error al cargar');
        }
    });
}

// =====================================================
// GUARDAR REGISTRO DE KILOMETRAJE
// =====================================================
function guardarKilometraje() {
    // Validar formulario
    if (!validarFormulario()) {
        return;
    }

    // Mostrar loading
    Swal.fire({
        title: 'Guardando...',
        text: 'Por favor espere',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // Obtener datos del formulario
    const formData = $('#formKilometraje').serialize();

    // Enviar datos al servidor
    $.ajax({
        url: '../../controller/furgoneta_registro_kilometraje.php?op=guardaryeditar',
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function (response) {
            console.log('Respuesta del servidor:', response);
            Swal.close();
            
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: response.message || 'Registro guardado correctamente',
                    confirmButtonText: 'Aceptar'
                }).then(() => {
                    // Redirigir al listado
                    const id_furgoneta = $('#id_furgoneta').val();
                    window.location.href = 'index.php?id_furgoneta=' + id_furgoneta;
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message || 'No se pudo guardar el registro',
                    confirmButtonText: 'Aceptar'
                });
            }
        },
        error: function (xhr, status, error) {
            Swal.close();
            console.error('Error AJAX:', error);
            console.error('Status:', status);
            console.error('Response Text:', xhr.responseText);
            console.error('Response Status:', xhr.status);
            
            Swal.fire({
                icon: 'error',
                title: 'Error de comunicación',
                text: 'No se pudo conectar con el servidor. Revisa la consola para más detalles.',
                confirmButtonText: 'Aceptar'
            });
        }
    });
}

// =====================================================
// VALIDAR FORMULARIO COMPLETO
// =====================================================
function validarFormulario() {
    let valido = true;
    let mensajes = [];

    // Validar campos obligatorios
    if (!$('#fecha_registro_km').val()) {
        mensajes.push('La fecha del registro es obligatoria');
        $('#fecha_registro_km').addClass('is-invalid');
        valido = false;
    } else {
        $('#fecha_registro_km').removeClass('is-invalid');
    }

    if (!$('#kilometraje_registrado_km').val()) {
        mensajes.push('El kilometraje es obligatorio');
        $('#kilometraje_registrado_km').addClass('is-invalid');
        valido = false;
    } else {
        $('#kilometraje_registrado_km').removeClass('is-invalid');
    }

    if (!$('#tipo_registro_km').val()) {
        mensajes.push('El tipo de registro es obligatorio');
        $('#tipo_registro_km').addClass('is-invalid');
        valido = false;
    } else {
        $('#tipo_registro_km').removeClass('is-invalid');
    }

    // Validar fecha
    if (!validarFecha()) {
        valido = false;
    }

    // Validar kilometraje
    if (!validarKilometraje()) {
        valido = false;
    }

    // Mostrar mensajes si hay errores
    if (!valido && mensajes.length > 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Campos incompletos',
            html: mensajes.join('<br>'),
            confirmButtonText: 'Aceptar'
        });
    }

    return valido;
}

// =====================================================
// VALIDAR FECHA
// =====================================================
function validarFecha() {
    const fecha = $('#fecha_registro_km').val();
    
    if (!fecha) {
        return false;
    }

    // Crear fecha sin problemas de timezone (usando solo año, mes, día)
    const [year, month, day] = fecha.split('-');
    const fechaRegistro = new Date(year, month - 1, day);
    
    const hoy = new Date();
    hoy.setHours(0, 0, 0, 0);

    // Validar que no sea fecha futura (permitir el día de hoy)
    if (fechaRegistro > hoy) {
        $('#fecha_registro_km').addClass('is-invalid');
        Swal.fire({
            icon: 'warning',
            title: 'Fecha inválida',
            text: 'La fecha no puede ser futura',
            confirmButtonText: 'Aceptar'
        });
        return false;
    }

    // Validar que no sea anterior al último registro
    const fechaMinima = $('#fecha_registro_km').attr('min');
    if (fechaMinima) {
        const [minYear, minMonth, minDay] = fechaMinima.split('-');
        const fechaMin = new Date(minYear, minMonth - 1, minDay);
        fechaMin.setHours(0, 0, 0, 0);
        
        if (fechaRegistro < fechaMin) {
            $('#fecha_registro_km').addClass('is-invalid');
            Swal.fire({
                icon: 'warning',
                title: 'Fecha inválida',
                text: 'La fecha no puede ser anterior al último registro (' + fechaMin.toLocaleDateString('es-ES') + ')',
                confirmButtonText: 'Aceptar'
            });
            return false;
        }
    }

    $('#fecha_registro_km').removeClass('is-invalid');
    return true;
}

// =====================================================
// VALIDAR KILOMETRAJE
// =====================================================
function validarKilometraje() {
    const km = $('#kilometraje_registrado_km').val();
    
    if (!km) {
        return false;
    }

    const kilometraje = Number(km);

    // Validar que sea un número positivo
    if (kilometraje < 0) {
        $('#kilometraje_registrado_km').addClass('is-invalid');
        Swal.fire({
            icon: 'warning',
            title: 'Kilometraje inválido',
            text: 'El kilometraje debe ser un número positivo',
            confirmButtonText: 'Aceptar'
        });
        return false;
    }

    // Validar que sea mayor o igual al último registro
    const kmMinimo = $('#kilometraje_registrado_km').attr('min');
    if (kmMinimo) {
        const minimo = Number(kmMinimo);
        if (kilometraje < minimo) {
            $('#kilometraje_registrado_km').addClass('is-invalid');
            Swal.fire({
                icon: 'warning',
                title: 'Kilometraje inválido',
                text: 'El kilometraje debe ser mayor o igual a ' + minimo.toLocaleString('es-ES') + ' km (último registro)',
                confirmButtonText: 'Aceptar'
            });
            return false;
        }
    }

    // Validar incremento razonable (alerta si es más de 100,000 km de diferencia)
    if (kmMinimo) {
        const incremento = kilometraje - Number(kmMinimo);
        if (incremento > 100000) {
            Swal.fire({
                icon: 'warning',
                title: 'Incremento inusual',
                text: 'El incremento de kilometraje es muy alto (' + incremento.toLocaleString('es-ES') + ' km). ¿Está seguro?',
                showCancelButton: true,
                confirmButtonText: 'Sí, continuar',
                cancelButtonText: 'Corregir'
            }).then((result) => {
                if (!result.isConfirmed) {
                    $('#kilometraje_registrado_km').focus();
                }
            });
        }
    }

    $('#kilometraje_registrado_km').removeClass('is-invalid');
    return true;
}
