/* =========================================
   Formulario de Elementos - JavaScript
   ========================================= */

// Variables globales
let modo = 'nuevo'; // nuevo | editar
let idElemento = null;
let idArticulo = null;

/* =========================================
   1. INICIALIZACIÓN
   ========================================= */

$(document).ready(function() {
    console.log('=== Iniciando formularioElemento.js ===');
    console.log('jQuery disponible:', typeof jQuery !== 'undefined');
    
    // Obtener parámetros de URL
    const urlParams = new URLSearchParams(window.location.search);
    modo = urlParams.get('modo') || 'nuevo';
    idElemento = urlParams.get('id');
    idArticulo = urlParams.get('id_articulo');
    
    console.log('Modo:', modo);
    console.log('ID Elemento:', idElemento);
    console.log('ID Artículo:', idArticulo);

    // Validar que id_articulo esté presente
    if (!idArticulo) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se ha especificado el artículo. Será redirigido al listado de artículos.',
            allowOutsideClick: false
        }).then(() => {
            window.location.href = '../MntArticulos/index.php';
        });
        return;
    }

    // Establecer id_articulo en campo oculto
    $('#id_articulo_elemento').val(idArticulo);

    // Cargar información del artículo
    cargarInfoArticulo(idArticulo);

    // Cargar catálogos
    cargarEstadosElemento();
    cargarProveedores();
    cargarFormasPago();

    // Configurar eventos del tipo de propiedad
    console.log('Configurando eventos de tipo de propiedad...');
    configurarEventosTipoPropiedad();

    // Configurar datepickers
    configurarDatepickers();

    // Configurar según modo
    if (modo === 'editar' && idElemento) {
        configurarModoEdicion(idElemento);
        // Cargar marcas primero, luego cargar datos del elemento
        cargarMarcas(function() {
            cargarDatosElemento(idElemento);
        });
    } else {
        configurarModoNuevo();
        // En modo nuevo, solo cargar las marcas sin callback
        cargarMarcas();
    }

    // Configurar validaciones en tiempo real
    configurarValidaciones();

    // Evento del botón guardar
    $('#btnSalvarElemento').on('click', function(e) {
        e.preventDefault();
        guardarElemento();
    });

    // Formatear campos en tiempo real
    configurarFormateosCampos();
});

/* =========================================
   2. CONFIGURACIÓN DE MODO
   ========================================= */

function configurarModoNuevo() {
    $('#page-title').text('Nuevo Elemento del Artículo');
    $('#breadcrumb-title').text('Nuevo Elemento');
    $('#estado_section').hide();
    $('#codigo_elemento_container').hide();
}

function configurarModoEdicion(id) {
    $('#page-title').text('Editar Elemento del Artículo');
    $('#breadcrumb-title').text('Editar Elemento');
    $('#id_elemento').val(id);
    $('#estado_section').show();
    $('#codigo_elemento_container').show();
    
    // Esperar a que se carguen las marcas antes de cargar los datos del elemento
    // Esto asegura que el select de marcas tenga opciones antes de establecer el valor
    // La función cargarDatosElemento se ejecutará después de que cargarMarcas termine
}

/* =========================================
   3. CARGA DE DATOS
   ========================================= */

/**
 * Carga la información del artículo
 */
function cargarInfoArticulo(id) {
    console.log('🔄 Cargando información del artículo ID:', id);
    $.ajax({
        url: '../../controller/articulo.php?op=mostrar',
        method: 'POST',
        data: { id_articulo: id },
        dataType: 'json',
        success: function(response) {
            console.log('✅ Respuesta del artículo:', response);
            if (response && response.nombre_articulo) {
                $('#nombre-articulo').text(response.nombre_articulo);
                $('#codigo-articulo').text(response.codigo_articulo || '--');
                $('#id-articulo').text(id);
                console.log('✅ Información del artículo cargada correctamente');
            } else {
                console.warn('⚠️ Respuesta del artículo sin nombre_articulo:', response);
                $('#nombre-articulo').text('No disponible');
            }
        },
        error: function(xhr, status, error) {
            console.error('❌ Error al cargar info del artículo:', error);
            console.error('❌ Status:', status);
            console.error('❌ Response:', xhr.responseText);
            $('#nombre-articulo').text('Error al cargar');
        }
    });
}

/**
 * Carga las marcas en el select
 */
function cargarMarcas(callback) {
    console.log('🔄 Cargando marcas...');
    $.ajax({
        url: '../../controller/marca.php?op=listar',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            console.log('✅ Respuesta marcas:', response);
            const $select = $('#id_marca_elemento');
            $select.empty();
            $select.append('<option value="">Seleccione una marca</option>');
            
            // El controlador devuelve {data: [...]}
            if (response && response.data && Array.isArray(response.data)) {
                console.log('📦 Total marcas recibidas:', response.data.length);
                let marcasActivas = 0;
                response.data.forEach(function(marca) {
                    // Comparación flexible: acepta '1', 1, true
                    if (marca.activo_marca == 1 || marca.activo_marca === '1' || marca.activo_marca === true) {
                        $select.append(`<option value="${marca.id_marca}">${marca.nombre_marca}</option>`);
                        marcasActivas++;
                    }
                });
                console.log('✅ Marcas activas cargadas:', marcasActivas);
                
                // Ejecutar callback si se proporcionó
                if (typeof callback === 'function') {
                    callback();
                }
            } else {
                console.warn('⚠️ Estructura de respuesta inesperada:', response);
            }
        },
        error: function(xhr, status, error) {
            console.error('❌ Error al cargar marcas:', error);
            console.error('Respuesta:', xhr.responseText);
            console.error('Status:', status);
        }
    });
}

/**
 * Carga los estados de elementos en el select
 */
function cargarEstadosElemento() {
    console.log('🔄 Cargando estados de elementos...');
    $.ajax({
        url: '../../controller/estado_elemento.php?op=listar',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            console.log('✅ Respuesta estados:', response);
            const $select = $('#id_estado_elemento');
            $select.empty();
            
            // El controlador devuelve {data: [...]}
            if (response && response.data && Array.isArray(response.data)) {
                console.log('📦 Total estados recibidos:', response.data.length);
                let estadosActivos = 0;
                response.data.forEach(function(estado) {
                    // Comparación flexible: acepta '1', 1, true
                    if (estado.activo_estado_elemento == 1 || estado.activo_estado_elemento === '1' || estado.activo_estado_elemento === true) {
                        // Seleccionar "Disponible" por defecto (id_estado_elemento = 1)
                        const selected = (estado.id_estado_elemento == 1 || estado.id_estado_elemento === '1') ? 'selected' : '';
                        
                        // Crear option con color de fondo
                        const color = estado.color_estado_elemento || '#CCCCCC';
                        const $option = $(`<option value="${estado.id_estado_elemento}" ${selected} style="background-color: ${color}; color: white; font-weight: bold;">${estado.descripcion_estado_elemento}</option>`);
                        $option.data('color', color); // Guardar color en data attribute
                        $select.append($option);
                        estadosActivos++;
                    }
                });
                console.log('✅ Estados activos cargados:', estadosActivos);
                
                // Actualizar color del select según la opción seleccionada
                actualizarColorSelect($select);
                
                // Evento change para actualizar color cuando cambia la selección
                $select.off('change.colorEstado').on('change.colorEstado', function() {
                    actualizarColorSelect($(this));
                });
            } else {
                console.warn('⚠️ Estructura de respuesta inesperada:', response);
            }
        },
        error: function(xhr, status, error) {
            console.error('❌ Error al cargar estados:', error);
            console.error('Respuesta:', xhr.responseText);
            console.error('Status:', status);
        }
    });
}

/**
 * Carga los proveedores activos en ambos selects (compra y alquiler)
 */
function cargarProveedores() {
    console.log('🔄 Cargando proveedores...');
    $.ajax({
        url: '../../controller/proveedor.php?op=listarDisponibles',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            console.log('✅ Respuesta proveedores:', response);
            
            // Llenar select de proveedor de compra (id_proveedor)
            const $selectCompra = $('#id_proveedor_compra_elemento');
            $selectCompra.empty();
            $selectCompra.append('<option value="">Seleccione un proveedor</option>');
            
            // Llenar select de proveedor de alquiler (id_proveedor)
            const $selectAlquiler = $('#id_proveedor_alquiler_elemento');
            $selectAlquiler.empty();
            $selectAlquiler.append('<option value="">Seleccione un proveedor</option>');
            
            if (response && response.data && Array.isArray(response.data)) {
                console.log('📦 Total proveedores recibidos:', response.data.length);
                response.data.forEach(function(proveedor) {
                    // Usar id_proveedor como value, no nombre_proveedor
                    $selectCompra.append(`<option value="${proveedor.id_proveedor}">${proveedor.nombre_proveedor}</option>`);
                    $selectAlquiler.append(`<option value="${proveedor.id_proveedor}">${proveedor.nombre_proveedor}</option>`);
                });
                console.log('✅ Proveedores cargados en ambos selects:', response.data.length);
            } else {
                console.warn('⚠️ Estructura de respuesta inesperada:', response);
            }
        },
        error: function(xhr, status, error) {
            console.error('❌ Error al cargar proveedores:', error);
            console.error('Respuesta:', xhr.responseText);
            console.error('Status:', status);
        }
    });
}

/**
 * Carga las formas de pago activas en el select
 */
function cargarFormasPago() {
    console.log('🔄 Cargando formas de pago...');
    $.ajax({
        url: '../../controller/formaspago.php?op=listar',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            console.log('✅ Respuesta formas de pago:', response);
            const $select = $('#id_forma_pago_alquiler_elemento');
            $select.empty();
            $select.append('<option value="">Seleccione una forma de pago</option>');
            
            if (response && response.data && Array.isArray(response.data)) {
                console.log('📦 Total formas de pago recibidas:', response.data.length);
                response.data.forEach(function(formaPago) {
                    if (formaPago.activo_pago == 1 || formaPago.activo_pago === '1' || formaPago.activo_pago === true) {
                        $select.append(`<option value="${formaPago.id_pago}">${formaPago.nombre_pago}</option>`);
                    }
                });
                console.log('✅ Formas de pago cargadas');
            } else {
                console.warn('⚠️ Estructura de respuesta inesperada:', response);
            }
        },
        error: function(xhr, status, error) {
            console.error('❌ Error al cargar formas de pago:', error);
            console.error('Respuesta:', xhr.responseText);
            console.error('Status:', status);
        }
    });
}

/**
 * Configura los eventos para el tipo de propiedad
 */
function configurarEventosTipoPropiedad() {
    console.log('=== Configurando eventos de tipo de propiedad ===');
    
    // Verificar que los elementos existan
    const radioButtons = $('input[name="es_propio_elemento"]');
    console.log('Radio buttons encontrados:', radioButtons.length);
    
    // Evento al cambiar el tipo de propiedad
    radioButtons.on('change', function() {
        const esPropio = $(this).val() === '1';
        console.log('Radio button cambiado - Es Propio:', esPropio);
        mostrarSeccionesSegunTipo(esPropio);
    });
    
    // Verificar el valor inicial
    const valorInicial = $('input[name="es_propio_elemento"]:checked').val();
    console.log('Valor inicial del radio:', valorInicial);
    
    // Inicializar con el valor por defecto (Equipo Propio)
    mostrarSeccionesSegunTipo(true);
}

/**
 * Muestra u oculta secciones según el tipo de propiedad
 */
function mostrarSeccionesSegunTipo(esPropio) {
    console.log('=== mostrarSeccionesSegunTipo - Es Propio:', esPropio, '===');
    
    if (esPropio) {
        console.log('Mostrando secciones de EQUIPO PROPIO');
        
        // Mostrar secciones de EQUIPO PROPIO
        $('#seccion_equipo_propio').slideDown(300);
        $('#seccion_garantia_mantenimiento').slideDown(300);
        
        // Ocultar sección de EQUIPO ALQUILADO
        $('#seccion_equipo_alquilado').slideUp(300);
        
        // Hacer campos obligatorios de equipo propio
        $('#id_proveedor_compra_elemento').prop('required', false); // Opcional
        
        // Quitar obligatoriedad de campos de alquiler
        $('#id_proveedor_alquiler_elemento').prop('required', false);
        $('#precio_dia_alquiler_elemento').prop('required', false);
        
        // Limpiar campos de alquiler
        $('#id_proveedor_alquiler_elemento').val('');
        $('#precio_dia_alquiler_elemento').val('');
        $('#id_forma_pago_alquiler_elemento').val('');
        $('#observaciones_alquiler_elemento').val('');
    } else {
        console.log('Mostrando secciones de EQUIPO ALQUILADO');
        
        // Ocultar secciones de EQUIPO PROPIO
        $('#seccion_equipo_propio').slideUp(300);
        $('#seccion_garantia_mantenimiento').slideUp(300);
        
        // Mostrar sección de EQUIPO ALQUILADO
        $('#seccion_equipo_alquilado').slideDown(300);
        
        // Hacer campos obligatorios de equipo alquilado
        $('#id_proveedor_alquiler_elemento').prop('required', true);
        $('#precio_dia_alquiler_elemento').prop('required', true);
        
        // Quitar obligatoriedad de campos de equipo propio
        $('#id_proveedor_compra_elemento').prop('required', false);
        
        // Limpiar campos de equipo propio
        $('#id_proveedor_compra_elemento').val('');
        $('#fecha_compra_elemento').val('');
        $('#precio_compra_elemento').val('');
        $('#fecha_alta_elemento').val('');
        $('#fecha_fin_garantia_elemento').val('');
        $('#proximo_mantenimiento_elemento').val('');
    }
}

/**
 * Actualiza el color de fondo del select según la opción seleccionada
 */
function actualizarColorSelect($select) {
    const $selectedOption = $select.find('option:selected');
    const color = $selectedOption.data('color') || $selectedOption.css('background-color');
    
    if (color) {
        $select.css({
            'background-color': color,
            'color': 'white',
            'font-weight': 'bold'
        });
    }
}

/**
 * Carga los datos del elemento para edición
 */
function cargarDatosElemento(id) {
    $.ajax({
        url: '../../controller/elemento.php?op=mostrar',
        method: 'POST',
        data: { id_elemento: id },
        dataType: 'json',
        success: function(data) {
            if (data) {
                // Información básica
                $('#descripcion_elemento').val(data.descripcion_elemento || '');
                $('#codigo_elemento_display').val(data.codigo_elemento || '');
                $('#codigo_barras_elemento').val(data.codigo_barras_elemento || '');
                $('#numero_serie_elemento').val(data.numero_serie_elemento || '');
                
                // Identificación
                // La vista devuelve 'id_marca' no 'id_marca_elemento'
                $('#id_marca_elemento').val(data.id_marca || '');
                
                $('#modelo_elemento').val(data.modelo_elemento || '');
                $('#id_estado_elemento').val(data.id_estado_elemento || '1');
                $('#nave_elemento').val(data.nave_elemento || '');
                $('#pasillo_columna_elemento').val(data.pasillo_columna_elemento || '');
                $('#altura_elemento').val(data.altura_elemento || '');
                
                // Tipo de propiedad
                const esPropio = data.es_propio_elemento == 1 || data.es_propio_elemento === '1' || data.es_propio_elemento === true;
                $(`input[name="es_propio_elemento"][value="${esPropio ? '1' : '0'}"]`).prop('checked', true);
                mostrarSeccionesSegunTipo(esPropio);
                
                // Datos de adquisición (equipo propio)
                if (data.fecha_compra_elemento) {
                    $('#fecha_compra_elemento').val(formatoFechaEuropeo(data.fecha_compra_elemento));
                }
                $('#precio_compra_elemento').val(data.precio_compra_elemento || '');
                $('#id_proveedor_compra_elemento').val(data.id_proveedor_compra_elemento || '');
                if (data.fecha_alta_elemento) {
                    $('#fecha_alta_elemento').val(formatoFechaEuropeo(data.fecha_alta_elemento));
                }
                
                // Datos de alquiler (equipo alquilado)
                $('#id_proveedor_alquiler_elemento').val(data.id_proveedor_alquiler_elemento || '');
                $('#precio_dia_alquiler_elemento').val(data.precio_dia_alquiler_elemento || '');
                $('#id_forma_pago_alquiler_elemento').val(data.id_forma_pago_alquiler_elemento || '');
                $('#observaciones_alquiler_elemento').val(data.observaciones_alquiler_elemento || '');
                
                // Garantía y mantenimiento
                if (data.fecha_fin_garantia_elemento) {
                    $('#fecha_fin_garantia_elemento').val(formatoFechaEuropeo(data.fecha_fin_garantia_elemento));
                }
                if (data.proximo_mantenimiento_elemento) {
                    $('#proximo_mantenimiento_elemento').val(formatoFechaEuropeo(data.proximo_mantenimiento_elemento));
                }
                
                // Observaciones
                $('#observaciones_elemento').val(data.observaciones_elemento || '');
                
                // Estado del elemento (activo/inactivo)
                // Comparación flexible: acepta '1', 1, true, '0', 0, false
                const activo = (data.activo_elemento == 1 || data.activo_elemento === '1' || data.activo_elemento === true);
                $('#activo_elemento_display').prop('checked', activo);
                $('#estado_texto').text(activo ? 'Elemento Activo' : 'Elemento Inactivo');
                $('#estado_texto').removeClass('text-success text-danger').addClass(activo ? 'text-success' : 'text-danger');
                $('#estado_descripcion').text(activo 
                    ? 'Este elemento está activo y visible en el sistema.' 
                    : 'Este elemento está inactivo y no aparece en las listas principales.');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error al cargar datos del elemento:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se pudo cargar la información del elemento.'
            });
        }
    });
}

/* =========================================
   4. CONFIGURACIÓN DE DATEPICKERS
   ========================================= */

function configurarDatepickers() {
    // Destruir datepickers existentes si los hay
    $('.datepicker').datepicker('destroy');
    
    // Configurar datepickers con formato europeo
    $('.datepicker').each(function() {
        $(this).datepicker({
            dateFormat: 'dd/mm/yy',
            changeMonth: true,
            changeYear: true,
            yearRange: '1990:+10',
            showButtonPanel: true,
            closeText: 'Cerrar',
            currentText: 'Hoy',
            monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 
                         'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
            monthNamesShort: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 
                              'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
            dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
            dayNamesShort: ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'],
            dayNamesMin: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sá'],
            firstDay: 1,
            regional: 'es',
            // Forzar formato al seleccionar
            onSelect: function(dateText, inst) {
                // Asegurarse de que la fecha esté en formato dd/mm/yyyy
                var parts = dateText.split('/');
                if (parts.length === 3) {
                    // Si está en formato mm/dd/yyyy, convertir a dd/mm/yyyy
                    if (parts[0].length === 2 && parts[1].length === 2 && parts[2].length === 4) {
                        // Verificar si el primer valor es mayor que 12 (indicaría que es día)
                        if (parseInt(parts[0]) > 12) {
                            // Ya está en formato dd/mm/yyyy
                            $(this).val(dateText);
                        } else {
                            // Podría estar en formato mm/dd/yyyy, intercambiar
                            $(this).val(parts[1] + '/' + parts[0] + '/' + parts[2]);
                        }
                    } else {
                        $(this).val(dateText);
                    }
                } else {
                    $(this).val(dateText);
                }
            }
        });
    });
}

/* =========================================
   5. VALIDACIONES
   ========================================= */

/**
 * Configura las validaciones en tiempo real
 */
function configurarValidaciones() {
    // Descripción (obligatorio)
    $('#descripcion_elemento').on('blur', function() {
        validarCampo($(this), validarDescripcion, true);
    });

    // Código de barras (único si se introduce)
    $('#codigo_barras_elemento').on('blur', function() {
        const valor = $(this).val().trim();
        if (valor) {
            validarCodigoBarras($(this));
        } else {
            mostrarExito($(this));
        }
    });

    // Número de serie (único si se introduce)
    $('#numero_serie_elemento').on('blur', function() {
        const valor = $(this).val().trim();
        if (valor) {
            validarNumeroSerie($(this));
        } else {
            mostrarExito($(this));
        }
    });

    // Precio (numérico si se introduce)
    $('#precio_compra_elemento').on('blur', function() {
        const valor = $(this).val().trim();
        if (valor) {
            validarCampo($(this), validarPrecio, false);
            // Formatear a 2 decimales
            const precio = parseFloat(valor);
            if (!isNaN(precio)) {
                $(this).val(precio.toFixed(2));
            }
        }
    });
}

/**
 * Valida un campo genérico
 */
function validarCampo($campo, funcionValidacion, obligatorio) {
    const valor = $campo.val().trim();
    
    if (obligatorio && !valor) {
        mostrarError($campo, 'Este campo es obligatorio');
        return false;
    }
    
    if (valor && funcionValidacion) {
        const resultado = funcionValidacion(valor);
        if (resultado !== true) {
            mostrarError($campo, resultado);
            return false;
        }
    }
    
    mostrarExito($campo);
    return true;
}

/**
 * Valida la descripción
 */
function validarDescripcion(descripcion) {
    if (descripcion.length < 3) {
        return 'La descripción debe tener al menos 3 caracteres';
    }
    if (descripcion.length > 255) {
        return 'La descripción no puede exceder 255 caracteres';
    }
    return true;
}

/**
 * Valida el precio
 */
function validarPrecio(precio) {
    const valor = parseFloat(precio);
    if (isNaN(valor)) {
        return 'Ingrese un precio válido';
    }
    if (valor < 0) {
        return 'El precio no puede ser negativo';
    }
    return true;
}

/**
 * Valida que el código de barras sea único
 */
function validarCodigoBarras($campo) {
    const codigoBarras = $campo.val().trim();
    const idElementoActual = $('#id_elemento').val();
    
    $.ajax({
        url: '../../controller/elemento.php?op=verificarCodigoBarras',
        method: 'POST',
        data: { 
            codigo_barras: codigoBarras,
            id_elemento: idElementoActual || null
        },
        dataType: 'json',
        success: function(response) {
            if (response.existe) {
                mostrarError($campo, 'Este código de barras ya está registrado en otro elemento');
            } else {
                mostrarExito($campo);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error al verificar código de barras:', error);
        }
    });
}

/**
 * Valida que el número de serie sea único
 */
function validarNumeroSerie($campo) {
    const numeroSerie = $campo.val().trim();
    const idElementoActual = $('#id_elemento').val();
    
    $.ajax({
        url: '../../controller/elemento.php?op=verificarNumeroSerie',
        method: 'POST',
        data: { 
            numero_serie: numeroSerie,
            id_elemento: idElementoActual || null
        },
        dataType: 'json',
        success: function(response) {
            if (response.existe) {
                mostrarError($campo, 'Este número de serie ya está registrado en otro elemento');
            } else {
                mostrarExito($campo);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error al verificar número de serie:', error);
        }
    });
}

/**
 * Muestra mensaje de error en un campo
 */
function mostrarError($campo, mensaje) {
    $campo.removeClass('is-valid').addClass('is-invalid');
    $campo.siblings('.invalid-feedback').text(mensaje);
}

/**
 * Muestra que el campo es válido
 */
function mostrarExito($campo) {
    $campo.removeClass('is-invalid').addClass('is-valid');
}

/**
 * Limpia las validaciones visuales
 */
function limpiarValidaciones() {
    $('.is-invalid, .is-valid').removeClass('is-invalid is-valid');
}

/* =========================================
   6. FORMATEO DE CAMPOS
   ========================================= */

function configurarFormateosCampos() {
    // Capitalizar descripción
    $('#descripcion_elemento').on('input', function() {
        const valor = $(this).val();
        $(this).val(valor.charAt(0).toUpperCase() + valor.slice(1));
    });

    // Uppercase para código de barras
    $('#codigo_barras_elemento').on('input', function() {
        $(this).val($(this).val().toUpperCase());
    });

    // Uppercase para número de serie
    $('#numero_serie_elemento').on('input', function() {
        $(this).val($(this).val().toUpperCase());
    });
}

/* =========================================
   7. GUARDAR ELEMENTO
   ========================================= */

function guardarElemento() {
    // Validar campos obligatorios
    let esValido = true;
    
    // Validar descripción
    esValido &= validarCampo($('#descripcion_elemento'), validarDescripcion, true);
    
    // Validar según tipo de propiedad
    let esPropio = $('input[name="es_propio_elemento"]:checked').val();
    
    if (esPropio === '1') {
        // Validar precio de compra si se ingresó
        const precio = $('#precio_compra_elemento').val().trim();
        if (precio) {
            esValido &= validarCampo($('#precio_compra_elemento'), validarPrecio, false);
        }
    } else {
        // Validar campos obligatorios de alquiler
        const proveedorAlquiler = $('#id_proveedor_alquiler_elemento').val();
        const precioAlquiler = $('#precio_dia_alquiler_elemento').val();
        
        if (!proveedorAlquiler) {
            mostrarError($('#id_proveedor_alquiler_elemento'), 'Debe seleccionar un proveedor de alquiler');
            esValido = false;
        }
        
        if (!precioAlquiler || parseFloat(precioAlquiler) <= 0) {
            mostrarError($('#precio_dia_alquiler_elemento'), 'El precio por día debe ser mayor a 0');
            esValido = false;
        }
    }
    
    // Validar código de barras (único) - esto se verifica en blur, pero comprobamos que no tenga error
    if ($('#codigo_barras_elemento').hasClass('is-invalid')) {
        esValido = false;
    }
    
    // Validar número de serie (único) - esto se verifica en blur, pero comprobamos que no tenga error
    if ($('#numero_serie_elemento').hasClass('is-invalid')) {
        esValido = false;
    }
    
    if (!esValido) {
        Swal.fire({
            icon: 'warning',
            title: 'Datos incompletos',
            text: 'Por favor, complete correctamente todos los campos obligatorios.'
        });
        return;
    }
    
    // Crear FormData
    const formData = new FormData($('#formElemento')[0]);
    
    // IMPORTANTE: Asegurarse de incluir el campo es_propio_elemento
    // (algunos navegadores no incluyen radio buttons si no están marcados correctamente)
    esPropio = $('input[name="es_propio_elemento"]:checked').val();
    formData.set('es_propio_elemento', esPropio || '1');
    
    // Si NO es propio (esPropio === '0'), forzar la inclusión de los campos de alquiler
    if (esPropio === '0') {
        // Asegurarse de que los campos de alquiler se incluyan explícitamente
        const idProveedorAlquiler = $('#id_proveedor_alquiler_elemento').val();
        const precioAlquiler = $('#precio_dia_alquiler_elemento').val();
        const idFormaPagoAlquiler = $('#id_forma_pago_alquiler_elemento').val();
        const obsAlquiler = $('#observaciones_alquiler_elemento').val();
        
        if (idProveedorAlquiler) formData.set('id_proveedor_alquiler_elemento', idProveedorAlquiler);
        if (precioAlquiler) formData.set('precio_dia_alquiler_elemento', precioAlquiler);
        if (idFormaPagoAlquiler) formData.set('id_forma_pago_alquiler_elemento', idFormaPagoAlquiler);
        if (obsAlquiler) formData.set('observaciones_alquiler_elemento', obsAlquiler);
    }
    
    // Convertir fechas de formato europeo (dd/mm/yyyy) a formato MySQL (yyyy-mm-dd)
    ['fecha_compra_elemento', 'fecha_alta_elemento', 'fecha_fin_garantia_elemento', 'proximo_mantenimiento_elemento'].forEach(function(campo) {
        const valor = $(`#${campo}`).val();
        if (valor) {
            const fechaMySQL = convertirFechaAMySQL(valor);
            formData.set(campo, fechaMySQL);
        }
    });
    
    // Mostrar spinner de carga
    Swal.fire({
        title: 'Guardando...',
        text: 'Por favor espere',
        allowOutsideClick: false,
        allowEscapeKey: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Enviar por AJAX
    $.ajax({
        url: '../../controller/elemento.php?op=guardaryeditar',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: response.message || 'Elemento guardado correctamente',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    window.location.href = `index.php?id_articulo=${idArticulo}`;
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message || 'No se pudo guardar el elemento'
                });
            }
        },
        error: function(xhr, status, error) {
            console.error('Error al guardar elemento:', error);
            console.error('Respuesta del servidor:', xhr.responseText);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Ocurrió un error al guardar el elemento. Por favor, inténtelo de nuevo.'
            });
        }
    });
}

/* =========================================
   8. UTILIDADES
   ========================================= */

/**
 * Convierte fecha de formato europeo a MySQL
 */
function convertirFechaAMySQL(fecha) {
    // Entrada: dd/mm/yyyy
    // Salida: yyyy-mm-dd
    const partes = fecha.split('/');
    if (partes.length === 3) {
        return `${partes[2]}-${partes[1]}-${partes[0]}`;
    }
    return fecha;
}

/**
 * Convierte fecha de MySQL a formato europeo
 */
function formatoFechaEuropeo(fechaString) {
    if (!fechaString) return '';
    
    // Si ya está en formato dd/mm/yyyy, devolver tal cual
    if (fechaString.includes('/')) {
        return fechaString;
    }
    
    // Convertir de yyyy-mm-dd a dd/mm/yyyy
    const fecha = new Date(fechaString);
    if (isNaN(fecha.getTime())) return fechaString;
    
    const dia = String(fecha.getDate()).padStart(2, '0');
    const mes = String(fecha.getMonth() + 1).padStart(2, '0');
    const anio = fecha.getFullYear();
    
    return `${dia}/${mes}/${anio}`;
}
