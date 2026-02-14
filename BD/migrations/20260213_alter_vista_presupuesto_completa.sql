-- ====================================================================
-- MIGRACIÓN: Punto 17 - Actualizar vista_presupuesto_completa
-- Fecha: 13 de febrero de 2026
-- Autor: Luis - Innovabyte
-- Descripción: Añade campos exento_iva_cliente y justificacion_exencion_iva_cliente a la vista
-- ====================================================================

USE toldos_db;

-- Nota: Esta vista se debe actualizar DESPUÉS de ejecutar 20260213_alter_cliente_exento_iva.sql

DROP VIEW IF EXISTS vista_presupuesto_completa;

CREATE OR REPLACE VIEW vista_presupuesto_completa AS
SELECT 
    -- Campos de presupuesto
    p.id_presupuesto,
    p.numero_presupuesto,
    p.version_actual_presupuesto,
    p.fecha_presupuesto,
    p.fecha_validez_presupuesto,
    p.fecha_inicio_evento_presupuesto,
    p.fecha_fin_evento_presupuesto,
    p.numero_pedido_cliente_presupuesto,
    p.aplicar_coeficientes_presupuesto,
    p.descuento_presupuesto,
    p.nombre_evento_presupuesto,
    p.direccion_evento_presupuesto,
    p.poblacion_evento_presupuesto,
    p.cp_evento_presupuesto,
    p.provincia_evento_presupuesto,
    p.observaciones_cabecera_presupuesto,
    p.observaciones_pie_presupuesto,
    p.observaciones_cabecera_ingles_presupuesto,
    p.observaciones_pie_ingles_presupuesto,
    p.mostrar_obs_familias_presupuesto,
    p.mostrar_obs_articulos_presupuesto,
    p.observaciones_internas_presupuesto,
    p.activo_presupuesto,
    p.created_at_presupuesto,
    p.updated_at_presupuesto,
    
    -- Campos de cliente
    c.id_cliente,
    c.codigo_cliente,
    c.nombre_cliente,
    c.nif_cliente,
    c.direccion_cliente,
    c.cp_cliente,
    c.poblacion_cliente,
    c.provincia_cliente,
    c.telefono_cliente,
    c.email_cliente,
    c.porcentaje_descuento_cliente,
    c.nombre_facturacion_cliente,
    c.direccion_facturacion_cliente,
    c.cp_facturacion_cliente,
    c.poblacion_facturacion_cliente,
    c.provincia_facturacion_cliente,
    
    -- *** NUEVOS CAMPOS - PUNTO 17 ***
    c.exento_iva_cliente,
    c.justificacion_exencion_iva_cliente,
    
    -- Datos del contacto
    cc.id_contacto_cliente,
    cc.nombre_contacto_cliente,
    cc.apellidos_contacto_cliente,
    cc.telefono_contacto_cliente,
    cc.email_contacto_cliente,
    
    -- Estado del presupuesto
    ep.id_estado_ppto,
    ep.codigo_estado_ppto,
    ep.nombre_estado_ppto,
    ep.color_estado_ppto,
    ep.orden_estado_ppto,
    
    -- Forma de pago
    fp.id_pago AS id_forma_pago,
    fp.codigo_pago,
    fp.nombre_pago,
    fp.porcentaje_anticipo_pago,
    fp.dias_anticipo_pago,
    fp.porcentaje_final_pago,
    fp.dias_final_pago,
    fp.descuento_pago,
    
    -- Método de pago
    mp.id_metodo_pago,
    mp.codigo_metodo_pago,
    mp.nombre_metodo_pago,
    
    -- Método de contacto
    mc.id_metodo AS id_metodo_contacto,
    mc.nombre AS nombre_metodo_contacto,
    
    -- Forma de pago habitual del cliente
    c.id_forma_pago_habitual,
    fph.nombre_pago AS nombre_forma_pago_habitual_cliente,
    
    -- Direcciones concatenadas
    CONCAT_WS(', ', 
        p.direccion_evento_presupuesto, 
        CONCAT(p.cp_evento_presupuesto, ' ', p.poblacion_evento_presupuesto), 
        p.provincia_evento_presupuesto
    ) AS direccion_completa_evento_presupuesto,
    
    CONCAT_WS(', ', 
        c.direccion_cliente, 
        CONCAT(c.cp_cliente, ' ', c.poblacion_cliente), 
        c.provincia_cliente
    ) AS direccion_completa_cliente,
    
    CONCAT_WS(', ', 
        c.direccion_facturacion_cliente, 
        CONCAT(c.cp_facturacion_cliente, ' ', c.poblacion_facturacion_cliente), 
        c.provincia_facturacion_cliente
    ) AS direccion_facturacion_completa_cliente,
    
    -- Nombre completo del contacto
    CONCAT_WS(' ', cc.nombre_contacto_cliente, cc.apellidos_contacto_cliente) AS nombre_completo_contacto,
    
    -- Validez del presupuesto
    (TO_DAYS(p.fecha_validez_presupuesto) - TO_DAYS(CURDATE())) AS dias_validez_restantes,
    
    CASE 
        WHEN p.fecha_validez_presupuesto IS NULL THEN 'Sin fecha de validez'
        WHEN p.fecha_validez_presupuesto < CURDATE() THEN 'Caducado'
        WHEN p.fecha_validez_presupuesto = CURDATE() THEN 'Caduca hoy'
        WHEN (TO_DAYS(p.fecha_validez_presupuesto) - TO_DAYS(CURDATE())) <= 7 THEN 'Próximo a caducar'
        ELSE 'Vigente'
    END AS estado_validez_presupuesto,
    
    -- Duración del evento
    ((TO_DAYS(p.fecha_fin_evento_presupuesto) - TO_DAYS(p.fecha_inicio_evento_presupuesto)) + 1) AS duracion_evento_dias,
    
    (TO_DAYS(p.fecha_inicio_evento_presupuesto) - TO_DAYS(CURDATE())) AS dias_hasta_inicio_evento,
    (TO_DAYS(p.fecha_fin_evento_presupuesto) - TO_DAYS(CURDATE())) AS dias_hasta_fin_evento,
    
    -- Estado del evento
    CASE 
        WHEN p.fecha_inicio_evento_presupuesto IS NULL THEN 'Sin fecha de evento'
        WHEN (p.fecha_inicio_evento_presupuesto < CURDATE() AND p.fecha_fin_evento_presupuesto < CURDATE()) THEN 'Evento finalizado'
        WHEN (p.fecha_inicio_evento_presupuesto <= CURDATE() AND p.fecha_fin_evento_presupuesto >= CURDATE()) THEN 'Evento en curso'
        WHEN p.fecha_inicio_evento_presupuesto = CURDATE() THEN 'Evento HOY'
        WHEN (TO_DAYS(p.fecha_inicio_evento_presupuesto) - TO_DAYS(CURDATE())) = 1 THEN 'Evento MAÑANA'
        WHEN (TO_DAYS(p.fecha_inicio_evento_presupuesto) - TO_DAYS(CURDATE())) <= 7 THEN 'Evento esta semana'
        WHEN (TO_DAYS(p.fecha_inicio_evento_presupuesto) - TO_DAYS(CURDATE())) <= 30 THEN 'Evento este mes'
        ELSE 'Evento futuro'
    END AS estado_evento_presupuesto,
    
    -- Prioridad del presupuesto
    CASE 
        WHEN p.fecha_inicio_evento_presupuesto IS NULL THEN 'Sin prioridad'
        WHEN p.fecha_inicio_evento_presupuesto = CURDATE() THEN 'HOY'
        WHEN (TO_DAYS(p.fecha_inicio_evento_presupuesto) - TO_DAYS(CURDATE())) = 1 THEN 'MAÑANA'
        WHEN (TO_DAYS(p.fecha_inicio_evento_presupuesto) - TO_DAYS(CURDATE())) <= 7 THEN 'Esta semana'
        WHEN (TO_DAYS(p.fecha_inicio_evento_presupuesto) - TO_DAYS(CURDATE())) <= 15 THEN 'Próximo'
        WHEN (TO_DAYS(p.fecha_inicio_evento_presupuesto) - TO_DAYS(CURDATE())) <= 30 THEN 'Este mes'
        ELSE 'Futuro'
    END AS prioridad_presupuesto,
    
    -- Tipo de pago
    CASE 
        WHEN fp.id_pago IS NULL THEN 'Sin forma de pago'
        WHEN fp.porcentaje_anticipo_pago = 100.00 THEN 'Pago único'
        WHEN fp.porcentaje_anticipo_pago < 100.00 THEN 'Pago fraccionado'
        ELSE 'Sin forma de pago'
    END AS tipo_pago_presupuesto,
    
    -- Descripción completa forma de pago
    CASE 
        WHEN fp.id_pago IS NULL THEN 'Sin forma de pago asignada'
        WHEN fp.porcentaje_anticipo_pago = 100.00 THEN CONCAT(
            mp.nombre_metodo_pago, ' - ', fp.nombre_pago,
            CASE WHEN fp.descuento_pago > 0 THEN CONCAT(' (Dto: ', fp.descuento_pago, '%)') ELSE '' END
        )
        ELSE CONCAT(mp.nombre_metodo_pago, ' - ', fp.porcentaje_anticipo_pago, '% + ', fp.porcentaje_final_pago, '%')
    END AS descripcion_completa_forma_pago,
    
    -- Fechas de vencimiento
    CASE 
        WHEN fp.dias_anticipo_pago = 0 THEN p.fecha_presupuesto
        ELSE (p.fecha_presupuesto + INTERVAL fp.dias_anticipo_pago DAY)
    END AS fecha_vencimiento_anticipo,
    
    CASE 
        WHEN (fp.dias_final_pago = 0 AND p.fecha_fin_evento_presupuesto IS NOT NULL) THEN p.fecha_fin_evento_presupuesto
        WHEN fp.dias_final_pago > 0 THEN (p.fecha_presupuesto + INTERVAL fp.dias_final_pago DAY)
        WHEN (fp.dias_final_pago < 0 AND p.fecha_inicio_evento_presupuesto IS NOT NULL) THEN (p.fecha_inicio_evento_presupuesto + INTERVAL fp.dias_final_pago DAY)
        ELSE NULL
    END AS fecha_vencimiento_final,
    
    -- Comparación de descuento
    CASE 
        WHEN p.descuento_presupuesto = c.porcentaje_descuento_cliente THEN 'Igual al habitual'
        WHEN p.descuento_presupuesto > c.porcentaje_descuento_cliente THEN 'Mayor al habitual'
        WHEN p.descuento_presupuesto < c.porcentaje_descuento_cliente THEN 'Menor al habitual'
        ELSE 'Sin comparar'
    END AS comparacion_descuento,
    
    -- Estado del descuento
    CASE 
        WHEN p.descuento_presupuesto = 0.00 THEN 'Sin descuento'
        WHEN (p.descuento_presupuesto > 0.00 AND p.descuento_presupuesto <= 5.00) THEN CONCAT('Descuento bajo: ', p.descuento_presupuesto, '%')
        WHEN (p.descuento_presupuesto > 5.00 AND p.descuento_presupuesto <= 15.00) THEN CONCAT('Descuento medio: ', p.descuento_presupuesto, '%')
        WHEN p.descuento_presupuesto > 15.00 THEN CONCAT('Descuento alto: ', p.descuento_presupuesto, '%')
        ELSE 'Sin descuento'
    END AS estado_descuento_presupuesto,
    
    -- Aplica descuento
    CASE WHEN p.descuento_presupuesto > 0.00 THEN TRUE ELSE FALSE END AS aplica_descuento_presupuesto,
    
    -- Diferencia de descuento
    (p.descuento_presupuesto - c.porcentaje_descuento_cliente) AS diferencia_descuento,
    
    -- Tiene dirección de facturación diferente
    CASE WHEN c.direccion_facturacion_cliente IS NOT NULL THEN TRUE ELSE FALSE END AS tiene_direccion_facturacion_diferente,
    
    -- Días desde emisión
    (TO_DAYS(CURDATE()) - TO_DAYS(p.fecha_presupuesto)) AS dias_desde_emision,
    
    -- Versión actual
    pv.id_version_presupuesto AS id_version_actual,
    pv.numero_version_presupuesto AS numero_version_actual,
    pv.estado_version_presupuesto AS estado_version_actual,
    pv.fecha_creacion_version AS fecha_creacion_version_actual,
    
    -- Estado general del presupuesto
    CASE 
        WHEN ep.codigo_estado_ppto = 'CANC' THEN 'Cancelado'
        WHEN ep.codigo_estado_ppto = 'FACT' THEN 'Facturado'
        WHEN (p.fecha_validez_presupuesto < CURDATE() AND ep.codigo_estado_ppto NOT IN ('ACEP', 'RECH', 'CANC', 'FACT')) THEN 'Caducado'
        WHEN (p.fecha_inicio_evento_presupuesto < CURDATE() AND p.fecha_fin_evento_presupuesto < CURDATE()) THEN 'Evento finalizado'
        WHEN (p.fecha_inicio_evento_presupuesto <= CURDATE() AND p.fecha_fin_evento_presupuesto >= CURDATE()) THEN 'Evento en curso'
        WHEN ep.codigo_estado_ppto = 'ACEP' THEN 'Aceptado - Pendiente evento'
        ELSE ep.nombre_estado_ppto
    END AS estado_general_presupuesto

FROM presupuesto p
INNER JOIN cliente c ON p.id_cliente = c.id_cliente
LEFT JOIN contacto_cliente cc ON p.id_contacto_cliente = cc.id_contacto_cliente
INNER JOIN estado_presupuesto ep ON p.id_estado_ppto = ep.id_estado_ppto
LEFT JOIN forma_pago fp ON p.id_forma_pago = fp.id_pago
LEFT JOIN metodo_pago mp ON fp.id_metodo_pago = mp.id_metodo_pago
LEFT JOIN metodos_contacto mc ON p.id_metodo = mc.id_metodo
LEFT JOIN forma_pago fph ON c.id_forma_pago_habitual = fph.id_pago
LEFT JOIN presupuesto_version pv ON (p.id_presupuesto = pv.id_presupuesto AND pv.numero_version_presupuesto = p.version_actual_presupuesto);

-- ====================================================================
-- VERIFICACIÓN DE LA VISTA
-- ====================================================================
-- SELECT exento_iva_cliente, justificacion_exencion_iva_cliente
-- FROM vista_presupuesto_completa
-- LIMIT 5;
-- ====================================================================
