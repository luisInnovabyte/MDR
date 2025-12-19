<!-- Modal Técnico para Elementos del Artículo -->
<div class="modal fade" id="modalTecnicoElementos" tabindex="-1" aria-labelledby="modalTecnicoElementosLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" style="max-width: 80%; width: 1600px;">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="modalTecnicoElementosLabel">
                    <i class="fas fa-code me-2"></i>Información Técnica - Triggers de la Tabla Elementos
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                
                <!-- Introducción -->
                <div class="alert alert-info">
                    <h6 class="alert-heading">
                        <i class="fas fa-info-circle me-2"></i>Disparadores Automáticos (Triggers)
                    </h6>
                    <p class="mb-0">
                        La tabla <code>elemento</code> cuenta con <strong>3 triggers</strong> que automatizan procesos críticos 
                        relacionados con la generación de códigos y la limpieza de campos según el tipo de propiedad del elemento 
                        (propio o alquilado). Estos triggers garantizan la integridad de los datos y evitan inconsistencias.
                    </p>
                </div>

                <!-- Trigger 1: Generación de Código y Estado Default -->
                <div class="card border-primary mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-hashtag me-2"></i>1. Trigger: trg_elemento_before_insert
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-primary">
                                    <i class="fas fa-info-circle me-2"></i>Descripción
                                </h6>
                                <p>
                                    Este trigger se ejecuta <strong>ANTES de insertar</strong> un nuevo elemento en la base de datos 
                                    y realiza dos funciones principales:
                                </p>
                                <ol>
                                    <li><strong>Genera automáticamente el código del elemento</strong> basándose en el código del artículo 
                                    más un número correlativo (formato: <code>CODIGO_ARTICULO-NNN</code>)</li>
                                    <li><strong>Asigna un estado por defecto</strong> si no se proporciona uno (id_estado_elemento = 1)</li>
                                </ol>

                                <h6 class="text-success mt-3">
                                    <i class="fas fa-check-circle me-2"></i>Ventajas
                                </h6>
                                <ul>
                                    <li>✅ Códigos únicos y secuenciales automáticos</li>
                                    <li>✅ No requiere intervención manual del usuario</li>
                                    <li>✅ Evita duplicados y errores de codificación</li>
                                    <li>✅ Formato estandarizado: CAM001-001, CAM001-002, etc.</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-secondary">
                                    <i class="fas fa-code me-2"></i>Código SQL del Trigger
                                </h6>
                                <div class="bg-light p-3 rounded" style="font-family: 'Courier New', monospace; font-size: 0.85rem; overflow-x: auto;">
<pre style="margin: 0;">DELIMITER $$

DROP TRIGGER IF EXISTS trg_elemento_before_insert$$

CREATE TRIGGER trg_elemento_before_insert
BEFORE INSERT ON elemento
FOR EACH ROW
BEGIN
    DECLARE codigo_art VARCHAR(50);
    DECLARE max_correlativo INT;
    
    -- Obtener código del artículo
    SELECT codigo_articulo 
    INTO codigo_art
    FROM articulo
    WHERE id_articulo = NEW.id_articulo_elemento;
    
    -- Asignar estado por defecto si es NULL
    IF NEW.id_estado_elemento IS NULL THEN
        SET NEW.id_estado_elemento = 1;
    END IF;
    
    -- Obtener el siguiente número correlativo
    SELECT COALESCE(
        MAX(CAST(
            SUBSTRING_INDEX(codigo_elemento, '-', -1) 
        AS UNSIGNED)), 0) + 1 
    INTO max_correlativo
    FROM elemento
    WHERE id_articulo_elemento = NEW.id_articulo_elemento;
    
    -- Generar código: ARTICULO-001
    SET NEW.codigo_elemento = CONCAT(
        codigo_art, 
        '-', 
        LPAD(max_correlativo, 3, '0')
    );
END$$

DELIMITER ;</pre>
                                </div>

                                <div class="alert alert-warning mt-3">
                                    <strong><i class="fas fa-exclamation-triangle me-2"></i>Importante:</strong><br>
                                    El código se genera <strong>en el momento de la inserción</strong>. 
                                    No es necesario proporcionarlo manualmente en el formulario.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Trigger 2: Limpieza de Campos en INSERT -->
                <div class="card border-success mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-broom me-2"></i>2. Trigger: trg_elemento_limpiar_campos_insert
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-success">
                                    <i class="fas fa-info-circle me-2"></i>Descripción
                                </h6>
                                <p>
                                    Este trigger se ejecuta <strong>ANTES de insertar</strong> un nuevo elemento y 
                                    <strong>limpia automáticamente los campos que no corresponden</strong> según el tipo de propiedad:
                                </p>

                                <div class="card mb-3 border-warning">
                                    <div class="card-header bg-warning text-dark">
                                        <strong>Elemento ALQUILADO (es_propio_elemento = FALSE)</strong>
                                    </div>
                                    <div class="card-body">
                                        <p class="mb-2"><strong>Campos que se vacían:</strong></p>
                                        <ul class="mb-0">
                                            <li><code>fecha_compra_elemento</code></li>
                                            <li><code>precio_compra_elemento</code></li>
                                            <li><code>fecha_alta_elemento</code></li>
                                            <li><code>id_proveedor_compra_elemento</code></li>
                                            <li><code>fecha_fin_garantia_elemento</code></li>
                                            <li><code>proximo_mantenimiento_elemento</code></li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="card border-info">
                                    <div class="card-header bg-info text-white">
                                        <strong>Elemento PROPIO (es_propio_elemento = TRUE)</strong>
                                    </div>
                                    <div class="card-body">
                                        <p class="mb-2"><strong>Campos que se vacían:</strong></p>
                                        <ul class="mb-0">
                                            <li><code>id_proveedor_alquiler_elemento</code></li>
                                            <li><code>precio_dia_alquiler_elemento</code></li>
                                            <li><code>id_forma_pago_alquiler_elemento</code></li>
                                            <li><code>observaciones_alquiler_elemento</code></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-secondary">
                                    <i class="fas fa-code me-2"></i>Código SQL del Trigger
                                </h6>
                                <div class="bg-light p-3 rounded" style="font-family: 'Courier New', monospace; font-size: 0.85rem; overflow-x: auto;">
<pre style="margin: 0;">DELIMITER $$

DROP TRIGGER IF EXISTS trg_elemento_limpiar_campos_insert$$

CREATE TRIGGER trg_elemento_limpiar_campos_insert
BEFORE INSERT ON elemento
FOR EACH ROW
BEGIN
    -- Si el elemento es ALQUILADO
    -- Vaciar campos de COMPRA
    IF NEW.es_propio_elemento = FALSE THEN
        SET NEW.fecha_compra_elemento = NULL;
        SET NEW.precio_compra_elemento = NULL;
        SET NEW.fecha_alta_elemento = NULL;
        SET NEW.id_proveedor_compra_elemento = NULL;
        SET NEW.fecha_fin_garantia_elemento = NULL;
        SET NEW.proximo_mantenimiento_elemento = NULL;
    END IF;
    
    -- Si el elemento es PROPIO
    -- Vaciar campos de ALQUILER
    IF NEW.es_propio_elemento = TRUE THEN
        SET NEW.id_proveedor_alquiler_elemento = NULL;
        SET NEW.precio_dia_alquiler_elemento = NULL;
        SET NEW.id_forma_pago_alquiler_elemento = NULL;
        SET NEW.observaciones_alquiler_elemento = NULL;
    END IF;
END$$

DELIMITER ;</pre>
                                </div>

                                <div class="alert alert-success mt-3">
                                    <strong><i class="fas fa-shield-alt me-2"></i>Garantiza Integridad:</strong><br>
                                    Este trigger evita que se almacenen datos contradictorios. Por ejemplo, 
                                    un elemento alquilado no puede tener fecha de compra.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Trigger 3: Limpieza de Campos en UPDATE -->
                <div class="card border-warning mb-4">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0">
                            <i class="fas fa-sync-alt me-2"></i>3. Trigger: trg_elemento_limpiar_campos_update
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-warning">
                                    <i class="fas fa-info-circle me-2"></i>Descripción
                                </h6>
                                <p>
                                    Este trigger se ejecuta <strong>ANTES de actualizar</strong> un elemento existente y 
                                    realiza la <strong>misma limpieza de campos</strong> que el trigger de INSERT, pero durante modificaciones.
                                </p>

                                <div class="alert alert-info">
                                    <h6 class="alert-heading">
                                        <i class="fas fa-lightbulb me-2"></i>¿Por qué es necesario?
                                    </h6>
                                    <p class="mb-0">
                                        Permite <strong>cambiar el tipo de propiedad</strong> de un elemento existente 
                                        (de propio a alquilado o viceversa) manteniendo la integridad de los datos. 
                                        Al cambiar el tipo, los campos innecesarios se limpian automáticamente.
                                    </p>
                                </div>

                                <h6 class="text-primary mt-3">
                                    <i class="fas fa-exchange-alt me-2"></i>Ejemplo de Uso
                                </h6>
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <p class="mb-2"><strong>Escenario:</strong></p>
                                        <ol class="mb-0">
                                            <li>Tienes una cámara <strong>PROPIA</strong> con datos de compra</li>
                                            <li>Decides cambiarla a <strong>ALQUILADA</strong></li>
                                            <li>El trigger automáticamente limpia todos los datos de compra</li>
                                            <li>Ahora puedes completar los datos de alquiler</li>
                                        </ol>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-secondary">
                                    <i class="fas fa-code me-2"></i>Código SQL del Trigger
                                </h6>
                                <div class="bg-light p-3 rounded" style="font-family: 'Courier New', monospace; font-size: 0.85rem; overflow-x: auto;">
<pre style="margin: 0;">DELIMITER $$

DROP TRIGGER IF EXISTS trg_elemento_limpiar_campos_update$$

CREATE TRIGGER trg_elemento_limpiar_campos_update
BEFORE UPDATE ON elemento
FOR EACH ROW
BEGIN
    -- Si el elemento es ALQUILADO
    -- Vaciar campos de COMPRA
    IF NEW.es_propio_elemento = FALSE THEN
        SET NEW.fecha_compra_elemento = NULL;
        SET NEW.precio_compra_elemento = NULL;
        SET NEW.fecha_alta_elemento = NULL;
        SET NEW.id_proveedor_compra_elemento = NULL;
        SET NEW.fecha_fin_garantia_elemento = NULL;
        SET NEW.proximo_mantenimiento_elemento = NULL;
    END IF;
    
    -- Si el elemento es PROPIO
    -- Vaciar campos de ALQUILER
    IF NEW.es_propio_elemento = TRUE THEN
        SET NEW.id_proveedor_alquiler_elemento = NULL;
        SET NEW.precio_dia_alquiler_elemento = NULL;
        SET NEW.id_forma_pago_alquiler_elemento = NULL;
        SET NEW.observaciones_alquiler_elemento = NULL;
    END IF;
END$$

DELIMITER ;</pre>
                                </div>

                                <div class="alert alert-warning mt-3">
                                    <strong><i class="fas fa-exclamation-triangle me-2"></i>Atención:</strong><br>
                                    Al cambiar el tipo de propiedad, los datos del tipo anterior 
                                    <strong>se perderán automáticamente</strong>. Este proceso es irreversible.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Resumen Final -->
                <div class="card border-dark">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-list-check me-2"></i>Resumen de Automatizaciones
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="text-center p-3 bg-primary text-white rounded">
                                    <i class="fas fa-hashtag fa-3x mb-3"></i>
                                    <h6>Generación de Códigos</h6>
                                    <p class="small mb-0">Códigos automáticos únicos y secuenciales</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center p-3 bg-success text-white rounded">
                                    <i class="fas fa-shield-alt fa-3x mb-3"></i>
                                    <h6>Integridad de Datos</h6>
                                    <p class="small mb-0">Limpieza automática de campos inconsistentes</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center p-3 bg-warning text-dark rounded">
                                    <i class="fas fa-magic fa-3x mb-3"></i>
                                    <h6>Automatización Total</h6>
                                    <p class="small mb-0">Sin intervención manual del usuario</p>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="alert alert-info mb-0">
                            <h6 class="alert-heading">
                                <i class="fas fa-database me-2"></i>Impacto en la Base de Datos
                            </h6>
                            <p class="mb-0">
                                Estos triggers se ejecutan <strong>a nivel de base de datos</strong>, lo que significa que 
                                funcionan independientemente de la aplicación (PHP, controllers, models). Cualquier inserción 
                                o actualización directa en la tabla <code>elemento</code>, ya sea desde la aplicación, 
                                SQL directo o herramientas de administración, activará estos triggers automáticamente.
                            </p>
                        </div>
                    </div>
                </div>

                <hr class="my-5">

                <!-- Sección de Triggers de Sincronización de Estado -->
                <h3 class="text-center mb-4">
                    <i class="fas fa-sync-alt me-2 text-primary"></i>Triggers de Sincronización de Estado
                </h3>

                <div class="alert alert-warning">
                    <h6 class="alert-heading">
                        <i class="fas fa-link me-2"></i>Sincronización Bidireccional
                    </h6>
                    <p class="mb-0">
                        Los siguientes <strong>4 triggers</strong> mantienen sincronizados los campos 
                        <code>activo_elemento</code> y <code>id_estado_elemento</code>. Cuando uno cambia, 
                        el otro se actualiza automáticamente para mantener la coherencia de los datos.
                    </p>
                </div>

                <!-- Trigger 4: Sync Estado INSERT -->
                <div class="card border-primary mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-arrow-right me-2"></i>4. Trigger: trg_elemento_sync_estado_insert
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-primary">
                                    <i class="fas fa-info-circle me-2"></i>Descripción
                                </h6>
                                <p>
                                    Se ejecuta <strong>ANTES de insertar</strong> un elemento y sincroniza 
                                    <code>activo_elemento</code> según el <code>id_estado_elemento</code>:
                                </p>

                                <div class="card mb-3 border-danger">
                                    <div class="card-body">
                                        <h6 class="text-danger mb-2">
                                            <i class="fas fa-times-circle me-2"></i>Si id_estado_elemento = 4
                                        </h6>
                                        <p class="mb-0">
                                            → <code>activo_elemento = FALSE</code><br>
                                            <small class="text-muted">(Estado "Dado de baja")</small>
                                        </p>
                                    </div>
                                </div>

                                <div class="card border-success">
                                    <div class="card-body">
                                        <h6 class="text-success mb-2">
                                            <i class="fas fa-check-circle me-2"></i>Si id_estado_elemento ≠ 4
                                        </h6>
                                        <p class="mb-0">
                                            → <code>activo_elemento = TRUE</code><br>
                                            <small class="text-muted">(Cualquier otro estado)</small>
                                        </p>
                                    </div>
                                </div>

                                <div class="alert alert-info mt-3">
                                    <strong><i class="fas fa-lightbulb me-2"></i>Lógica:</strong><br>
                                    Garantiza que un elemento con estado "Dado de baja" 
                                    siempre esté inactivo automáticamente.
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-secondary">
                                    <i class="fas fa-code me-2"></i>Código SQL del Trigger
                                </h6>
                                <div class="bg-light p-3 rounded" style="font-family: 'Courier New', monospace; font-size: 0.85rem; overflow-x: auto;">
<pre style="margin: 0;">DELIMITER $$

DROP TRIGGER IF EXISTS trg_elemento_sync_estado_insert$$

CREATE TRIGGER trg_elemento_sync_estado_insert
BEFORE INSERT ON elemento
FOR EACH ROW
BEGIN
    -- Si el estado es 4 (Dado de baja)
    IF NEW.id_estado_elemento = 4 THEN
        SET NEW.activo_elemento = FALSE;
    ELSE
        -- Cualquier otro estado
        SET NEW.activo_elemento = TRUE;
    END IF;
END$$

DELIMITER ;</pre>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Trigger 5: Sync Estado UPDATE -->
                <div class="card border-success mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-arrow-right me-2"></i>5. Trigger: trg_elemento_sync_estado_update
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-success">
                                    <i class="fas fa-info-circle me-2"></i>Descripción
                                </h6>
                                <p>
                                    Se ejecuta <strong>ANTES de actualizar</strong> un elemento y sincroniza 
                                    <code>activo_elemento</code> <strong>solo si cambió</strong> el <code>id_estado_elemento</code>:
                                </p>

                                <div class="card mb-3 bg-light">
                                    <div class="card-body">
                                        <h6 class="text-dark mb-2">
                                            <i class="fas fa-not-equal me-2"></i>Condición de Activación
                                        </h6>
                                        <code>NEW.id_estado_elemento ≠ OLD.id_estado_elemento</code>
                                        <p class="mt-2 mb-0 small text-muted">
                                            Solo se ejecuta si el estado cambió respecto al valor anterior
                                        </p>
                                    </div>
                                </div>

                                <div class="card mb-3 border-danger">
                                    <div class="card-body">
                                        <h6 class="text-danger mb-2">
                                            <i class="fas fa-times-circle me-2"></i>Nuevo estado = 4
                                        </h6>
                                        <p class="mb-0">
                                            → <code>activo_elemento = FALSE</code>
                                        </p>
                                    </div>
                                </div>

                                <div class="card border-success">
                                    <div class="card-body">
                                        <h6 class="text-success mb-2">
                                            <i class="fas fa-check-circle me-2"></i>Nuevo estado ≠ 4
                                        </h6>
                                        <p class="mb-0">
                                            → <code>activo_elemento = TRUE</code>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-secondary">
                                    <i class="fas fa-code me-2"></i>Código SQL del Trigger
                                </h6>
                                <div class="bg-light p-3 rounded" style="font-family: 'Courier New', monospace; font-size: 0.85rem; overflow-x: auto;">
<pre style="margin: 0;">DELIMITER $$

DROP TRIGGER IF EXISTS trg_elemento_sync_estado_update$$

CREATE TRIGGER trg_elemento_sync_estado_update
BEFORE UPDATE ON elemento
FOR EACH ROW
BEGIN
    -- Solo actuar si cambió el id_estado_elemento
    IF NEW.id_estado_elemento != OLD.id_estado_elemento THEN
        -- Si el nuevo estado es 4 (Dado de baja)
        IF NEW.id_estado_elemento = 4 THEN
            SET NEW.activo_elemento = FALSE;
        ELSE
            -- Cualquier otro estado
            SET NEW.activo_elemento = TRUE;
        END IF;
    END IF;
END$$

DELIMITER ;</pre>
                                </div>

                                <div class="alert alert-success mt-3">
                                    <strong><i class="fas fa-shield-alt me-2"></i>Optimización:</strong><br>
                                    El trigger solo se ejecuta cuando realmente cambia el estado, 
                                    evitando procesamiento innecesario.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Trigger 6: Sync Activo INSERT -->
                <div class="card border-info mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-arrow-left me-2"></i>6. Trigger: trg_elemento_sync_activo_insert
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-info">
                                    <i class="fas fa-info-circle me-2"></i>Descripción
                                </h6>
                                <p>
                                    Se ejecuta <strong>ANTES de insertar</strong> un elemento y sincroniza 
                                    <code>id_estado_elemento</code> según el valor de <code>activo_elemento</code>:
                                </p>

                                <div class="card mb-3 border-danger">
                                    <div class="card-body">
                                        <h6 class="text-danger mb-2">
                                            <i class="fas fa-toggle-off me-2"></i>Si activo_elemento = FALSE
                                        </h6>
                                        <p class="mb-0">
                                            → <code>id_estado_elemento = 4</code><br>
                                            <small class="text-muted">(Dado de baja)</small>
                                        </p>
                                    </div>
                                </div>

                                <div class="card border-success">
                                    <div class="card-body">
                                        <h6 class="text-success mb-2">
                                            <i class="fas fa-toggle-on me-2"></i>Si activo_elemento = TRUE
                                        </h6>
                                        <p class="mb-2">
                                            Y el estado es NULL o 4:<br>
                                            → <code>id_estado_elemento = 1</code><br>
                                            <small class="text-muted">(Disponible)</small>
                                        </p>
                                        <p class="mb-0 small text-muted">
                                            Si ya tiene otro estado válido, se respeta.
                                        </p>
                                    </div>
                                </div>

                                <div class="alert alert-info mt-3">
                                    <strong><i class="fas fa-lightbulb me-2"></i>Lógica:</strong><br>
                                    Garantiza que un elemento inactivo siempre tenga estado "Dado de baja", 
                                    y que un elemento activo tenga un estado coherente.
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-secondary">
                                    <i class="fas fa-code me-2"></i>Código SQL del Trigger
                                </h6>
                                <div class="bg-light p-3 rounded" style="font-family: 'Courier New', monospace; font-size: 0.85rem; overflow-x: auto;">
<pre style="margin: 0;">DELIMITER $$

DROP TRIGGER IF EXISTS trg_elemento_sync_activo_insert$$

CREATE TRIGGER trg_elemento_sync_activo_insert
BEFORE INSERT ON elemento
FOR EACH ROW
BEGIN
    -- Si se está insertando como inactivo
    IF NEW.activo_elemento = FALSE THEN
        SET NEW.id_estado_elemento = 4; -- Dado de baja
    ELSE
        -- Si viene activo y el estado no está 
        -- definido o es inconsistente
        IF NEW.id_estado_elemento IS NULL 
           OR NEW.id_estado_elemento = 4 THEN
            SET NEW.id_estado_elemento = 1; -- Disponible
        END IF;
    END IF;
END$$

DELIMITER ;</pre>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Trigger 7: Sync Activo UPDATE -->
                <div class="card border-warning mb-4">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0">
                            <i class="fas fa-arrow-left me-2"></i>7. Trigger: trg_elemento_sync_activo_update
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-warning">
                                    <i class="fas fa-info-circle me-2"></i>Descripción
                                </h6>
                                <p>
                                    Se ejecuta <strong>ANTES de actualizar</strong> un elemento y sincroniza 
                                    <code>id_estado_elemento</code> <strong>solo si cambió</strong> el <code>activo_elemento</code>:
                                </p>

                                <div class="card mb-3 bg-light">
                                    <div class="card-body">
                                        <h6 class="text-dark mb-2">
                                            <i class="fas fa-not-equal me-2"></i>Condición de Activación
                                        </h6>
                                        <code>NEW.activo_elemento ≠ OLD.activo_elemento</code>
                                        <p class="mt-2 mb-0 small text-muted">
                                            Solo se ejecuta si el campo activo cambió
                                        </p>
                                    </div>
                                </div>

                                <div class="card mb-3 border-danger">
                                    <div class="card-body">
                                        <h6 class="text-danger mb-2">
                                            <i class="fas fa-toggle-off me-2"></i>Se desactiva (FALSE)
                                        </h6>
                                        <p class="mb-0">
                                            → <code>id_estado_elemento = 4</code><br>
                                            <small class="text-muted">(Dado de baja)</small>
                                        </p>
                                    </div>
                                </div>

                                <div class="card border-success">
                                    <div class="card-body">
                                        <h6 class="text-success mb-2">
                                            <i class="fas fa-toggle-on me-2"></i>Se reactiva (TRUE)
                                        </h6>
                                        <p class="mb-0">
                                            → <code>id_estado_elemento = 1</code><br>
                                            <small class="text-muted">(Disponible)</small>
                                        </p>
                                    </div>
                                </div>

                                <div class="alert alert-warning mt-3">
                                    <strong><i class="fas fa-exclamation-triangle me-2"></i>Importante:</strong><br>
                                    Al reactivar un elemento, siempre vuelve al estado "Disponible" (1). 
                                    Si necesita otro estado, debe cambiarse manualmente después.
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-secondary">
                                    <i class="fas fa-code me-2"></i>Código SQL del Trigger
                                </h6>
                                <div class="bg-light p-3 rounded" style="font-family: 'Courier New', monospace; font-size: 0.85rem; overflow-x: auto;">
<pre style="margin: 0;">DELIMITER $$

DROP TRIGGER IF EXISTS trg_elemento_sync_activo_update$$

CREATE TRIGGER trg_elemento_sync_activo_update
BEFORE UPDATE ON elemento
FOR EACH ROW
BEGIN
    -- Solo actuar si cambió el activo_elemento
    IF NEW.activo_elemento != OLD.activo_elemento THEN
        -- Si se está desactivando
        IF NEW.activo_elemento = FALSE THEN
            SET NEW.id_estado_elemento = 4; -- Dado de baja
        ELSE
            -- Si se está reactivando
            SET NEW.id_estado_elemento = 1; -- Disponible
        END IF;
    END IF;
END$$

DELIMITER ;</pre>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Diagrama de Sincronización -->
                <div class="card border-dark mb-4">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-project-diagram me-2"></i>Diagrama de Sincronización Bidireccional
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-5">
                                <div class="p-4 bg-primary text-white rounded">
                                    <i class="fas fa-toggle-on fa-3x mb-3"></i>
                                    <h5>activo_elemento</h5>
                                    <p class="mb-0 small">Campo Booleano</p>
                                    <p class="mb-0"><code>TRUE / FALSE</code></p>
                                </div>
                            </div>
                            <div class="col-md-2 d-flex align-items-center justify-content-center">
                                <div>
                                    <i class="fas fa-arrows-alt-h fa-3x text-success"></i>
                                    <p class="mt-2 mb-0 small text-muted">Sincronización<br>Automática</p>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="p-4 bg-info text-white rounded">
                                    <i class="fas fa-list-ol fa-3x mb-3"></i>
                                    <h5>id_estado_elemento</h5>
                                    <p class="mb-0 small">ID del Estado</p>
                                    <p class="mb-0"><code>1, 2, 3, 4, ...</code></p>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-primary">
                                    <i class="fas fa-arrow-circle-right me-2"></i>Estado → Activo
                                </h6>
                                <ul class="list-unstyled">
                                    <li class="mb-2">
                                        <span class="badge bg-danger me-2">Estado 4</span>
                                        <i class="fas fa-long-arrow-alt-right mx-2"></i>
                                        <code>activo = FALSE</code>
                                    </li>
                                    <li>
                                        <span class="badge bg-success me-2">Otro Estado</span>
                                        <i class="fas fa-long-arrow-alt-right mx-2"></i>
                                        <code>activo = TRUE</code>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-info">
                                    <i class="fas fa-arrow-circle-left me-2"></i>Activo → Estado
                                </h6>
                                <ul class="list-unstyled">
                                    <li class="mb-2">
                                        <code>activo = FALSE</code>
                                        <i class="fas fa-long-arrow-alt-right mx-2"></i>
                                        <span class="badge bg-danger me-2">Estado 4</span>
                                    </li>
                                    <li>
                                        <code>activo = TRUE</code>
                                        <i class="fas fa-long-arrow-alt-right mx-2"></i>
                                        <span class="badge bg-success me-2">Estado 1</span>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class="alert alert-success mt-3 mb-0">
                            <h6 class="alert-heading">
                                <i class="fas fa-check-double me-2"></i>Coherencia Garantizada
                            </h6>
                            <p class="mb-0">
                                Estos 4 triggers trabajan en conjunto para garantizar que <strong>nunca existan inconsistencias</strong> 
                                entre el estado de un elemento y su campo activo. Independientemente de cuál se modifique, 
                                el otro se actualizará automáticamente para mantener la coherencia.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Tabla Resumen Estados -->
                <div class="card border-secondary mb-4">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-table me-2"></i>Tabla de Referencia: Estados del Elemento
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID Estado</th>
                                        <th>Nombre del Estado</th>
                                        <th>activo_elemento</th>
                                        <th>Descripción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="table-success">
                                        <td class="text-center"><strong>1</strong></td>
                                        <td><span class="badge bg-success">Disponible</span></td>
                                        <td class="text-center"><code>TRUE</code></td>
                                        <td>Elemento listo para alquilar</td>
                                    </tr>
                                    <tr class="table-info">
                                        <td class="text-center"><strong>2</strong></td>
                                        <td><span class="badge bg-info">Alquilado</span></td>
                                        <td class="text-center"><code>TRUE</code></td>
                                        <td>Elemento actualmente en alquiler</td>
                                    </tr>
                                    <tr class="table-warning">
                                        <td class="text-center"><strong>3</strong></td>
                                        <td><span class="badge bg-warning text-dark">En reparación</span></td>
                                        <td class="text-center"><code>TRUE</code></td>
                                        <td>Elemento en proceso de reparación</td>
                                    </tr>
                                    <tr class="table-danger">
                                        <td class="text-center"><strong>4</strong></td>
                                        <td><span class="badge bg-danger">Dado de baja</span></td>
                                        <td class="text-center"><code>FALSE</code></td>
                                        <td>Elemento fuera de servicio permanentemente</td>
                                    </tr>
                                    <tr>
                                        <td class="text-center"><strong>Otros</strong></td>
                                        <td><span class="badge bg-secondary">Otros estados</span></td>
                                        <td class="text-center"><code>TRUE</code></td>
                                        <td>Cualquier otro estado personalizado</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="alert alert-info mt-3 mb-0">
                            <strong><i class="fas fa-info-circle me-2"></i>Nota Importante:</strong><br>
                            Solo el estado <strong>ID 4 (Dado de baja)</strong> marca el elemento como inactivo. 
                            Todos los demás estados (1, 2, 3, 5, 6, etc.) mantienen el elemento activo.
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Cerrar
                </button>
            </div>
        </div>
    </div>
</div>
