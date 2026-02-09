-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Servidor: mysql:3306
-- Tiempo de generación: 08-02-2026 a las 16:17:15
-- Versión del servidor: 9.3.0
-- Versión de PHP: 8.2.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `toldos_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `adjunto_llamada`
--

CREATE TABLE `adjunto_llamada` (
  `id_adjunto` int NOT NULL,
  `id_llamada` int NOT NULL,
  `nombre_archivo` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `tipo` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `fecha_subida` datetime DEFAULT CURRENT_TIMESTAMP,
  `estado` tinyint DEFAULT '1' COMMENT '0=Inactivo, 1=Activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `articulo`
--

CREATE TABLE `articulo` (
  `id_articulo` int UNSIGNED NOT NULL,
  `id_familia` int UNSIGNED NOT NULL,
  `id_unidad` int UNSIGNED DEFAULT NULL,
  `codigo_articulo` varchar(50) NOT NULL,
  `nombre_articulo` varchar(255) NOT NULL,
  `name_articulo` varchar(255) NOT NULL,
  `imagen_articulo` varchar(255) DEFAULT NULL,
  `precio_alquiler_articulo` decimal(10,2) DEFAULT '0.00',
  `coeficiente_articulo` tinyint(1) DEFAULT NULL,
  `es_kit_articulo` tinyint(1) DEFAULT '0',
  `control_total_articulo` tinyint(1) DEFAULT '0',
  `no_facturar_articulo` tinyint(1) DEFAULT '0',
  `notas_presupuesto_articulo` text,
  `notes_budget_articulo` text,
  `orden_obs_articulo` int DEFAULT '200',
  `observaciones_articulo` text,
  `activo_articulo` tinyint(1) DEFAULT '1',
  `permitir_descuentos_articulo` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Indica si el artículo acepta descuentos (1=Sí permite, 0=No permite)',
  `id_impuesto` int DEFAULT NULL COMMENT 'Impuesto aplicable al artículo',
  `created_at_articulo` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at_articulo` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id` int NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `fecha` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Disparadores `categorias`
--
DELIMITER $$
CREATE TRIGGER `set_default_fecha` BEFORE INSERT ON `categorias` FOR EACH ROW BEGIN
  IF NEW.fecha IS NULL THEN
    SET NEW.fecha = CURDATE();
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cliente`
--

CREATE TABLE `cliente` (
  `id_cliente` int UNSIGNED NOT NULL,
  `codigo_cliente` varchar(20) NOT NULL,
  `nombre_cliente` varchar(255) NOT NULL,
  `direccion_cliente` varchar(255) DEFAULT NULL,
  `cp_cliente` varchar(10) DEFAULT NULL,
  `poblacion_cliente` varchar(100) DEFAULT NULL,
  `provincia_cliente` varchar(100) DEFAULT NULL,
  `nif_cliente` varchar(20) DEFAULT NULL,
  `telefono_cliente` varchar(255) DEFAULT NULL,
  `fax_cliente` varchar(50) DEFAULT NULL,
  `web_cliente` varchar(255) DEFAULT NULL,
  `email_cliente` varchar(255) DEFAULT NULL,
  `nombre_facturacion_cliente` varchar(255) DEFAULT NULL,
  `direccion_facturacion_cliente` varchar(255) DEFAULT NULL,
  `cp_facturacion_cliente` varchar(10) DEFAULT NULL,
  `poblacion_facturacion_cliente` varchar(100) DEFAULT NULL,
  `provincia_facturacion_cliente` varchar(100) DEFAULT NULL,
  `id_forma_pago_habitual` int UNSIGNED DEFAULT NULL COMMENT 'Forma de pago habitual del cliente. Se usará por defecto en nuevos presupuestos',
  `porcentaje_descuento_cliente` decimal(5,2) NOT NULL DEFAULT '0.00' COMMENT 'Porcentaje de descuento habitual acordado con el cliente (0.00 a 100.00). Ejemplo: 10.00 = 10% de descuento',
  `observaciones_cliente` text,
  `activo_cliente` tinyint(1) DEFAULT '1',
  `created_at_cliente` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at_cliente` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cliente_ubicacion`
--

CREATE TABLE `cliente_ubicacion` (
  `id_ubicacion` int UNSIGNED NOT NULL,
  `id_cliente` int UNSIGNED NOT NULL COMMENT 'Cliente propietario de esta ubicación',
  `nombre_ubicacion` varchar(255) NOT NULL COMMENT 'Nombre identificativo: "Teatro Municipal", "Auditorio Central", etc.',
  `direccion_ubicacion` varchar(255) DEFAULT NULL COMMENT 'Calle, número, piso, etc.',
  `codigo_postal_ubicacion` varchar(10) DEFAULT NULL,
  `poblacion_ubicacion` varchar(100) DEFAULT NULL,
  `provincia_ubicacion` varchar(100) DEFAULT NULL,
  `pais_ubicacion` varchar(100) DEFAULT 'España' COMMENT 'País de la ubicación',
  `persona_contacto_ubicacion` varchar(255) DEFAULT NULL COMMENT 'Persona de contacto en esta ubicación específica',
  `telefono_contacto_ubicacion` varchar(50) DEFAULT NULL COMMENT 'Teléfono de contacto en la ubicación',
  `email_contacto_ubicacion` varchar(255) DEFAULT NULL COMMENT 'Email de contacto en la ubicación',
  `observaciones_ubicacion` text COMMENT 'Notas operativas: "Acceso calle trasera", "Ascensor limitado", "Horario carga 8-10h", etc.',
  `es_principal_ubicacion` tinyint(1) DEFAULT '0' COMMENT 'TRUE: Ubicación por defecto del cliente | FALSE: Ubicación secundaria',
  `activo_ubicacion` tinyint(1) DEFAULT '1' COMMENT 'TRUE: Ubicación activa | FALSE: Ubicación desactivada',
  `created_at_ubicacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at_ubicacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Ubicaciones habituales de eventos para cada cliente';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `coeficiente`
--

CREATE TABLE `coeficiente` (
  `id_coeficiente` int UNSIGNED NOT NULL,
  `jornadas_coeficiente` int NOT NULL,
  `valor_coeficiente` decimal(10,2) NOT NULL,
  `observaciones_coeficiente` text,
  `activo_coeficiente` tinyint(1) DEFAULT '1',
  `created_at_coeficiente` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at_coeficiente` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comerciales`
--

CREATE TABLE `comerciales` (
  `id_comercial` int NOT NULL,
  `nombre` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `apellidos` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `movil` varchar(15) COLLATE utf8mb4_general_ci NOT NULL,
  `telefono` varchar(15) COLLATE utf8mb4_general_ci NOT NULL,
  `activo` tinyint DEFAULT '1',
  `id_usuario` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `com_vacaciones`
--

CREATE TABLE `com_vacaciones` (
  `id_vacacion` int NOT NULL,
  `id_comercial` int NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `descripcion` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `activo_vacacion` tinyint DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contactos`
--

CREATE TABLE `contactos` (
  `id_contacto` int NOT NULL,
  `id_llamada` int NOT NULL,
  `fecha_hora_contacto` datetime DEFAULT CURRENT_TIMESTAMP,
  `observaciones` text COLLATE utf8mb4_general_ci,
  `estado` tinyint NOT NULL DEFAULT '1' COMMENT '0=Inactivo, 1=Activo',
  `id_metodo` int NOT NULL,
  `id_visita_cerrada` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `contactos_con_nombre_comunicante`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `contactos_con_nombre_comunicante` (
`id_contacto` int
,`id_llamada` int
,`id_metodo` int
,`fecha_hora_contacto` datetime
,`observaciones` text
,`id_visita_cerrada` int
,`fecha_visita_cerrada` datetime
,`estado` tinyint
,`nombre_comunicante` varchar(100)
,`domicilio_instalacion` varchar(200)
,`telefono_fijo` varchar(15)
,`telefono_movil` varchar(15)
,`email_contacto` varchar(50)
,`fecha_hora_preferida` datetime
,`fecha_recepcion` datetime
,`id_comercial_asignado` bigint
,`estado_llamada` bigint
,`activo_llamada` int
,`nombre_metodo` varchar(50)
,`imagen_metodo` varchar(255)
,`descripcion_estado_llamada` varchar(100)
,`nombre_comercial` varchar(50)
,`archivos_adjuntos` text
,`tiene_contactos` int
,`estado_es_3` int
,`tiene_adjuntos` int
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `contacto_cantidad_cliente`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `contacto_cantidad_cliente` (
`id_cliente` int unsigned
,`codigo_cliente` varchar(20)
,`nombre_cliente` varchar(255)
,`direccion_cliente` varchar(255)
,`cp_cliente` varchar(10)
,`poblacion_cliente` varchar(100)
,`provincia_cliente` varchar(100)
,`nif_cliente` varchar(20)
,`telefono_cliente` varchar(255)
,`fax_cliente` varchar(50)
,`web_cliente` varchar(255)
,`email_cliente` varchar(255)
,`nombre_facturacion_cliente` varchar(255)
,`direccion_facturacion_cliente` varchar(255)
,`cp_facturacion_cliente` varchar(10)
,`poblacion_facturacion_cliente` varchar(100)
,`provincia_facturacion_cliente` varchar(100)
,`observaciones_cliente` text
,`activo_cliente` tinyint(1)
,`created_at_cliente` timestamp
,`updated_at_cliente` timestamp
,`porcentaje_descuento_cliente` decimal(5,2)
,`id_forma_pago_habitual` int unsigned
,`codigo_pago` varchar(20)
,`nombre_pago` varchar(100)
,`descuento_pago` decimal(5,2)
,`porcentaje_anticipo_pago` decimal(5,2)
,`dias_anticipo_pago` int
,`porcentaje_final_pago` decimal(5,2)
,`dias_final_pago` int
,`observaciones_pago` text
,`activo_pago` tinyint(1)
,`id_metodo_pago` int unsigned
,`codigo_metodo_pago` varchar(20)
,`nombre_metodo_pago` varchar(100)
,`observaciones_metodo_pago` text
,`activo_metodo_pago` tinyint(1)
,`cantidad_contactos_cliente` bigint
,`tipo_pago_cliente` varchar(17)
,`descripcion_forma_pago_cliente` varchar(219)
,`direccion_completa_cliente` varchar(470)
,`direccion_facturacion_completa_cliente` varchar(470)
,`tiene_direccion_facturacion_diferente` int
,`estado_forma_pago_cliente` varchar(23)
,`categoria_descuento_cliente` varchar(15)
,`tiene_descuento_cliente` int
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `contacto_cantidad_proveedor`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `contacto_cantidad_proveedor` (
`id_proveedor` int unsigned
,`codigo_proveedor` varchar(20)
,`nombre_proveedor` varchar(255)
,`direccion_proveedor` varchar(255)
,`cp_proveedor` varchar(10)
,`poblacion_proveedor` varchar(100)
,`provincia_proveedor` varchar(100)
,`nif_proveedor` varchar(20)
,`telefono_proveedor` varchar(255)
,`fax_proveedor` varchar(50)
,`web_proveedor` varchar(255)
,`email_proveedor` varchar(255)
,`persona_contacto_proveedor` varchar(255)
,`direccion_sat_proveedor` varchar(255)
,`cp_sat_proveedor` varchar(10)
,`poblacion_sat_proveedor` varchar(100)
,`provincia_sat_proveedor` varchar(100)
,`telefono_sat_proveedor` varchar(255)
,`fax_sat_proveedor` varchar(50)
,`email_sat_proveedor` varchar(255)
,`observaciones_proveedor` text
,`activo_proveedor` tinyint(1)
,`created_at_proveedor` timestamp
,`updated_at_proveedor` timestamp
,`id_forma_pago_habitual` int unsigned
,`codigo_pago` varchar(20)
,`nombre_pago` varchar(100)
,`descuento_pago` decimal(5,2)
,`porcentaje_anticipo_pago` decimal(5,2)
,`dias_anticipo_pago` int
,`porcentaje_final_pago` decimal(5,2)
,`dias_final_pago` int
,`observaciones_pago` text
,`activo_pago` tinyint(1)
,`id_metodo_pago` int unsigned
,`codigo_metodo_pago` varchar(20)
,`nombre_metodo_pago` varchar(100)
,`observaciones_metodo_pago` text
,`activo_metodo_pago` tinyint(1)
,`cantidad_contacto_proveedor` bigint
,`tipo_pago_proveedor` varchar(17)
,`descripcion_forma_pago_proveedor` varchar(219)
,`direccion_completa_proveedor` varchar(470)
,`direccion_sat_completa_proveedor` varchar(470)
,`tiene_direccion_sat` int
,`estado_forma_pago_proveedor` varchar(23)
);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contacto_cliente`
--

CREATE TABLE `contacto_cliente` (
  `id_contacto_cliente` int UNSIGNED NOT NULL,
  `id_cliente` int UNSIGNED NOT NULL,
  `nombre_contacto_cliente` varchar(100) NOT NULL,
  `apellidos_contacto_cliente` varchar(150) DEFAULT NULL,
  `cargo_contacto_cliente` varchar(100) DEFAULT NULL,
  `departamento_contacto_cliente` varchar(100) DEFAULT NULL,
  `telefono_contacto_cliente` varchar(50) DEFAULT NULL,
  `movil_contacto_cliente` varchar(50) DEFAULT NULL,
  `email_contacto_cliente` varchar(255) DEFAULT NULL,
  `extension_contacto_cliente` varchar(10) DEFAULT NULL,
  `principal_contacto_cliente` tinyint(1) DEFAULT '0',
  `observaciones_contacto_cliente` text,
  `activo_contacto_cliente` tinyint(1) DEFAULT '1',
  `created_at_contacto_cliente` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at_contacto_cliente` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contacto_proveedor`
--

CREATE TABLE `contacto_proveedor` (
  `id_contacto_proveedor` int UNSIGNED NOT NULL,
  `id_proveedor` int UNSIGNED NOT NULL,
  `nombre_contacto_proveedor` varchar(100) NOT NULL,
  `apellidos_contacto_proveedor` varchar(150) DEFAULT NULL,
  `cargo_contacto_proveedor` varchar(100) DEFAULT NULL,
  `departamento_contacto_proveedor` varchar(100) DEFAULT NULL,
  `telefono_contacto_proveedor` varchar(50) DEFAULT NULL,
  `movil_contacto_proveedor` varchar(50) DEFAULT NULL,
  `email_contacto_proveedor` varchar(255) DEFAULT NULL,
  `extension_contacto_proveedor` varchar(10) DEFAULT NULL,
  `principal_contacto_proveedor` tinyint(1) DEFAULT '0',
  `observaciones_contacto_proveedor` text,
  `activo_contacto_proveedor` tinyint(1) DEFAULT '1',
  `created_at_contacto_proveedor` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at_contacto_proveedor` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `documento`
--

CREATE TABLE `documento` (
  `id_documento` int UNSIGNED NOT NULL,
  `titulo_documento` varchar(255) COLLATE utf8mb4_spanish_ci NOT NULL,
  `descripcion_documento` text COLLATE utf8mb4_spanish_ci,
  `ruta_documento` varchar(500) COLLATE utf8mb4_spanish_ci NOT NULL COMMENT 'Ruta relativa del archivo PDF',
  `id_tipo_documento_documento` int UNSIGNED NOT NULL,
  `fecha_publicacion_documento` date DEFAULT NULL,
  `activo_documento` tinyint(1) DEFAULT '1',
  `fecha_creacion_documento` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_modificacion_documento` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `documento_elemento`
--

CREATE TABLE `documento_elemento` (
  `id_documento_elemento` int UNSIGNED NOT NULL,
  `id_elemento` int UNSIGNED NOT NULL,
  `descripcion_documento_elemento` text,
  `tipo_documento_elemento` varchar(100) DEFAULT NULL COMMENT 'Tipo: Manual, Garantía, Factura, Certificado, etc.',
  `archivo_documento` varchar(500) NOT NULL COMMENT 'Ruta completa con nombre del archivo',
  `privado_documento` tinyint(1) DEFAULT '0' COMMENT 'Si TRUE, solo visible para administración',
  `observaciones_documento` text,
  `activo_documento` tinyint(1) DEFAULT '1',
  `created_at_documento` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at_documento` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `elemento`
--

CREATE TABLE `elemento` (
  `id_elemento` int UNSIGNED NOT NULL,
  `id_articulo_elemento` int UNSIGNED NOT NULL,
  `id_marca_elemento` int UNSIGNED DEFAULT NULL,
  `modelo_elemento` varchar(100) DEFAULT NULL,
  `codigo_elemento` varchar(50) NOT NULL COMMENT 'Formato: codigo_articulo-correlativo',
  `codigo_barras_elemento` varchar(100) DEFAULT NULL,
  `descripcion_elemento` varchar(255) NOT NULL,
  `numero_serie_elemento` varchar(100) DEFAULT NULL,
  `id_estado_elemento` int UNSIGNED NOT NULL DEFAULT '1' COMMENT 'Estado actual del elemento',
  `nave_elemento` varchar(50) DEFAULT NULL COMMENT 'Nave o almacén donde se encuentra (ej: "Nave 1", "Nave Principal")',
  `pasillo_columna_elemento` varchar(50) DEFAULT NULL COMMENT 'Pasillo y columna (ej: "A-5", "B-12", "C-3")',
  `altura_elemento` varchar(50) DEFAULT NULL COMMENT 'Altura o nivel (ej: "Planta baja", "Nivel 2", "Altura 3m")',
  `fecha_compra_elemento` date DEFAULT NULL COMMENT 'Fecha de compra del elemento',
  `precio_compra_elemento` decimal(10,2) DEFAULT '0.00' COMMENT 'Precio de compra',
  `fecha_alta_elemento` date DEFAULT NULL COMMENT 'Fecha de puesta en servicio',
  `fecha_fin_garantia_elemento` date DEFAULT NULL,
  `proximo_mantenimiento_elemento` date DEFAULT NULL,
  `observaciones_elemento` text,
  `activo_elemento` tinyint(1) DEFAULT '1',
  `es_propio_elemento` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'TRUE: Equipo propio de la empresa | FALSE: Equipo alquilado a proveedor',
  `id_proveedor_compra_elemento` int UNSIGNED DEFAULT NULL COMMENT 'Proveedor que vendió el elemento (solo si es_propio = TRUE)',
  `id_proveedor_alquiler_elemento` int UNSIGNED DEFAULT NULL COMMENT 'Proveedor al que se alquila el elemento (solo si es_propio = FALSE)',
  `precio_dia_alquiler_elemento` decimal(10,2) DEFAULT NULL COMMENT 'Precio por día que pagamos al proveedor por alquilar este elemento',
  `id_forma_pago_alquiler_elemento` int UNSIGNED DEFAULT NULL COMMENT 'Forma de pago acordada con el proveedor para el alquiler (solo si es_propio = FALSE)',
  `observaciones_alquiler_elemento` text COMMENT 'Condiciones especiales de alquiler: mínimo de días, restricciones, contacto, etc.',
  `created_at_elemento` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at_elemento` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Disparadores `elemento`
--
DELIMITER $$
CREATE TRIGGER `trg_elemento_before_insert` BEFORE INSERT ON `elemento` FOR EACH ROW BEGIN
    DECLARE codigo_art VARCHAR(50);
    DECLARE max_correlativo INT;
    
    -- Obtener código del artículo
    SELECT codigo_articulo 
    INTO codigo_art
    FROM articulo
    WHERE id_articulo = NEW.id_articulo_elemento;
    
    -- Si no se especifica estado, asignar "Disponible"
    IF NEW.id_estado_elemento IS NULL THEN
        SET NEW.id_estado_elemento = 1;
    END IF;
    
    -- Calcular siguiente correlativo extrayéndolo de códigos existentes
    SELECT COALESCE(MAX(
        CAST(SUBSTRING_INDEX(codigo_elemento, '-', -1) AS UNSIGNED)
    ), 0) + 1 INTO max_correlativo
    FROM elemento
    WHERE id_articulo_elemento = NEW.id_articulo_elemento;
    
    -- Generar código completo
    SET NEW.codigo_elemento = CONCAT(
        codigo_art,
        '-',
        LPAD(max_correlativo, 3, '0')
    );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_elemento_limpiar_campos_insert` BEFORE INSERT ON `elemento` FOR EACH ROW BEGIN
    -- Si el elemento es ALQUILADO (es_propio_elemento = FALSE)
    -- Vaciar campos de COMPRA
    IF NEW.es_propio_elemento = FALSE THEN
        SET NEW.fecha_compra_elemento = NULL;
        SET NEW.precio_compra_elemento = NULL;
        SET NEW.fecha_alta_elemento = NULL;
        SET NEW.id_proveedor_compra_elemento = NULL;
        SET NEW.fecha_fin_garantia_elemento = NULL;
        SET NEW.proximo_mantenimiento_elemento = NULL;
    END IF;
    
    -- Si el elemento es PROPIO (es_propio_elemento = TRUE)
    -- Vaciar campos de ALQUILER
    IF NEW.es_propio_elemento = TRUE THEN
        SET NEW.id_proveedor_alquiler_elemento = NULL;
        SET NEW.precio_dia_alquiler_elemento = NULL;
        SET NEW.id_forma_pago_alquiler_elemento = NULL;
        SET NEW.observaciones_alquiler_elemento = NULL;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_elemento_limpiar_campos_update` BEFORE UPDATE ON `elemento` FOR EACH ROW BEGIN
    -- Si el elemento es ALQUILADO (es_propio_elemento = FALSE)
    -- Vaciar campos de COMPRA
    IF NEW.es_propio_elemento = FALSE THEN
        SET NEW.fecha_compra_elemento = NULL;
        SET NEW.precio_compra_elemento = NULL;
        SET NEW.fecha_alta_elemento = NULL;
        SET NEW.id_proveedor_compra_elemento = NULL;
        SET NEW.fecha_fin_garantia_elemento = NULL;
        SET NEW.proximo_mantenimiento_elemento = NULL;
    END IF;
    
    -- Si el elemento es PROPIO (es_propio_elemento = TRUE)
    -- Vaciar campos de ALQUILER
    IF NEW.es_propio_elemento = TRUE THEN
        SET NEW.id_proveedor_alquiler_elemento = NULL;
        SET NEW.precio_dia_alquiler_elemento = NULL;
        SET NEW.id_forma_pago_alquiler_elemento = NULL;
        SET NEW.observaciones_alquiler_elemento = NULL;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_elemento_sync_activo_insert` BEFORE INSERT ON `elemento` FOR EACH ROW BEGIN
    -- Si se está insertando como inactivo
    IF NEW.activo_elemento = FALSE THEN
        SET NEW.id_estado_elemento = 4; -- Dado de baja
    ELSE
        -- Si viene activo y el estado no está definido o es inconsistente
        IF NEW.id_estado_elemento IS NULL OR NEW.id_estado_elemento = 4 THEN
            SET NEW.id_estado_elemento = 1; -- Disponible
        END IF;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_elemento_sync_activo_update` BEFORE UPDATE ON `elemento` FOR EACH ROW BEGIN
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
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_elemento_sync_estado_insert` BEFORE INSERT ON `elemento` FOR EACH ROW BEGIN
    -- Si el estado es 4 (Dado de baja)
    IF NEW.id_estado_elemento = 4 THEN
        SET NEW.activo_elemento = FALSE;
    ELSE
        -- Cualquier otro estado
        SET NEW.activo_elemento = TRUE;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_elemento_sync_estado_update` BEFORE UPDATE ON `elemento` FOR EACH ROW BEGIN
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
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empresa`
--

CREATE TABLE `empresa` (
  `id_empresa` int UNSIGNED NOT NULL,
  `codigo_empresa` varchar(20) NOT NULL COMMENT 'Código único identificador (ej: MDR01, MDR02, FICTICIA)',
  `nombre_empresa` varchar(255) NOT NULL COMMENT 'Razón social completa',
  `nombre_comercial_empresa` varchar(255) DEFAULT NULL COMMENT 'Nombre comercial si difiere de la razón social',
  `ficticia_empresa` tinyint(1) DEFAULT '0' COMMENT 'Si TRUE, empresa ficticia solo para presupuestos. Si FALSE, empresa real que factura',
  `empresa_ficticia_principal` tinyint(1) DEFAULT '0' COMMENT 'Si TRUE, esta es la empresa ficticia por defecto para presupuestos',
  `nif_empresa` varchar(20) NOT NULL COMMENT 'NIF/CIF de la empresa',
  `direccion_fiscal_empresa` varchar(255) NOT NULL COMMENT 'Dirección completa del domicilio fiscal',
  `cp_fiscal_empresa` varchar(10) NOT NULL COMMENT 'Código postal',
  `poblacion_fiscal_empresa` varchar(100) NOT NULL COMMENT 'Población/Ciudad',
  `provincia_fiscal_empresa` varchar(100) NOT NULL COMMENT 'Provincia',
  `pais_fiscal_empresa` varchar(100) DEFAULT 'España' COMMENT 'País',
  `telefono_empresa` varchar(50) DEFAULT NULL COMMENT 'Teléfono principal',
  `movil_empresa` varchar(50) DEFAULT NULL COMMENT 'Teléfono móvil',
  `email_empresa` varchar(255) DEFAULT NULL COMMENT 'Email general',
  `email_facturacion_empresa` varchar(255) DEFAULT NULL COMMENT 'Email específico para facturación',
  `web_empresa` varchar(255) DEFAULT NULL COMMENT 'Sitio web',
  `iban_empresa` varchar(34) DEFAULT NULL COMMENT 'IBAN para domiciliaciones y transferencias',
  `swift_empresa` varchar(11) DEFAULT NULL COMMENT 'Código SWIFT/BIC',
  `banco_empresa` varchar(100) DEFAULT NULL COMMENT 'Nombre del banco',
  `serie_presupuesto_empresa` varchar(10) DEFAULT 'P' COMMENT 'Serie para presupuestos (ej: P, PPTO, MDR-P)',
  `numero_actual_presupuesto_empresa` int UNSIGNED DEFAULT '0' COMMENT 'Último número de presupuesto emitido',
  `dias_validez_presupuesto_empresa` int UNSIGNED NOT NULL DEFAULT '30' COMMENT 'Días de validez por defecto para los presupuestos emitidos por esta empresa',
  `serie_factura_empresa` varchar(10) DEFAULT 'F' COMMENT 'Serie para facturas (ej: F, FAC, A)',
  `numero_actual_factura_empresa` int UNSIGNED DEFAULT '0' COMMENT 'Último número de factura emitido',
  `serie_abono_empresa` varchar(10) DEFAULT 'R' COMMENT 'Serie para facturas rectificativas/abonos (ej: R, AB, REC)',
  `numero_actual_abono_empresa` int UNSIGNED DEFAULT '0' COMMENT 'Último número de abono emitido',
  `verifactu_activo_empresa` tinyint(1) DEFAULT '1' COMMENT 'Si TRUE, esta empresa debe cumplir con VeriFact',
  `verifactu_software_empresa` varchar(100) DEFAULT NULL COMMENT 'Nombre del software de facturación',
  `verifactu_version_empresa` varchar(50) DEFAULT NULL COMMENT 'Versión del software',
  `verifactu_nif_desarrollador_empresa` varchar(20) DEFAULT NULL COMMENT 'NIF del desarrollador del software',
  `verifactu_nombre_desarrollador_empresa` varchar(255) DEFAULT NULL COMMENT 'Nombre del desarrollador',
  `verifactu_sistema_empresa` enum('online','offline') DEFAULT 'online' COMMENT 'online=envío inmediato | offline=envío diferido',
  `verifactu_url_empresa` varchar(255) DEFAULT NULL COMMENT 'URL del endpoint de VeriFact',
  `verifactu_certificado_empresa` text COMMENT 'Ruta o datos del certificado digital',
  `logotipo_empresa` varchar(255) DEFAULT NULL COMMENT 'Ruta al archivo del logotipo (para facturas y presupuestos)',
  `logotipo_pie_empresa` varchar(255) DEFAULT NULL COMMENT 'Logotipo secundario para pie de página',
  `texto_legal_factura_empresa` text COMMENT 'Texto legal que aparece en facturas (registro mercantil, etc.)',
  `texto_pie_presupuesto_empresa` text COMMENT 'Texto que aparece en el pie de los presupuestos',
  `texto_pie_factura_empresa` text COMMENT 'Texto que aparece en el pie de las facturas',
  `observaciones_empresa` text COMMENT 'Observaciones internas sobre la empresa',
  `activo_empresa` tinyint(1) DEFAULT '1' COMMENT 'Si FALSE, la empresa no estará disponible para nuevos documentos',
  `created_at_empresa` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at_empresa` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Gestión de empresas del grupo para facturación y presupuestos';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estados_llamada`
--

CREATE TABLE `estados_llamada` (
  `id_estado` int NOT NULL,
  `desc_estado` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `defecto_estado` tinyint DEFAULT NULL,
  `activo_estado` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `peso_estado` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estado_elemento`
--

CREATE TABLE `estado_elemento` (
  `id_estado_elemento` int UNSIGNED NOT NULL,
  `codigo_estado_elemento` varchar(20) NOT NULL,
  `descripcion_estado_elemento` varchar(50) NOT NULL,
  `color_estado_elemento` varchar(7) DEFAULT NULL COMMENT 'Color hexadecimal para visualización',
  `permite_alquiler_estado_elemento` tinyint(1) DEFAULT '1' COMMENT 'Si TRUE, el elemento puede ser alquilado en este estado',
  `observaciones_estado_elemento` text,
  `activo_estado_elemento` tinyint(1) DEFAULT '1',
  `created_at_estado_elemento` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at_estado_elemento` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estado_presupuesto`
--

CREATE TABLE `estado_presupuesto` (
  `id_estado_ppto` int UNSIGNED NOT NULL,
  `codigo_estado_ppto` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre_estado_ppto` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `color_estado_ppto` varchar(7) COLLATE utf8mb4_unicode_ci DEFAULT '#007bff',
  `orden_estado_ppto` int DEFAULT '0',
  `observaciones_estado_ppto` text COLLATE utf8mb4_unicode_ci,
  `activo_estado_ppto` tinyint(1) DEFAULT '1',
  `created_at_estado_ppto` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at_estado_ppto` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `familia`
--

CREATE TABLE `familia` (
  `id_familia` int UNSIGNED NOT NULL,
  `id_grupo` int UNSIGNED DEFAULT NULL,
  `codigo_familia` varchar(20) NOT NULL,
  `nombre_familia` varchar(100) NOT NULL,
  `name_familia` varchar(100) NOT NULL COMMENT 'Nombre en inglés',
  `descr_familia` varchar(255) DEFAULT NULL,
  `activo_familia` tinyint(1) DEFAULT '1',
  `permite_descuento_familia` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'TRUE: Los artículos de esta familia pueden tener descuento | FALSE: Familia sin descuentos (consumibles, servicios especiales, etc.)',
  `coeficiente_familia` tinyint DEFAULT NULL,
  `id_unidad_familia` int DEFAULT NULL COMMENT 'el Id (id_unidad) de la tabla unidad_medida',
  `imagen_familia` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'Es la foto representativa de la familia',
  `observaciones_presupuesto_familia` text,
  `observations_budget_familia` text,
  `orden_obs_familia` int DEFAULT '100',
  `created_at_familia` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at_familia` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `familia_unidad_media`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `familia_unidad_media` (
`id_familia` int unsigned
,`id_grupo` int unsigned
,`codigo_familia` varchar(20)
,`nombre_familia` varchar(100)
,`name_familia` varchar(100)
,`descr_familia` varchar(255)
,`imagen_familia` varchar(150)
,`activo_familia` tinyint(1)
,`coeficiente_familia` tinyint
,`created_at_familia` timestamp
,`updated_at_familia` timestamp
,`id_unidad_familia` int
,`observaciones_presupuesto_familia` text
,`orden_obs_familia` int
,`nombre_unidad` varchar(50)
,`descr_unidad` varchar(255)
,`simbolo_unidad` varchar(10)
,`activo_unidad` tinyint(1)
,`codigo_grupo` varchar(20)
,`nombre_grupo` varchar(100)
,`descripcion_grupo` varchar(255)
);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `forma_pago`
--

CREATE TABLE `forma_pago` (
  `id_pago` int UNSIGNED NOT NULL,
  `codigo_pago` varchar(20) NOT NULL COMMENT 'Código único identificador (ej: CONT_TRANS, FRAC40_60)',
  `nombre_pago` varchar(100) NOT NULL COMMENT 'Nombre descriptivo de la forma de pago',
  `id_metodo_pago` int UNSIGNED NOT NULL COMMENT 'Método de pago a utilizar (transferencia, tarjeta, efectivo...)',
  `descuento_pago` decimal(5,2) DEFAULT '0.00' COMMENT 'Descuento por pronto pago en porcentaje (ej: 2.00 = 2%). Solo aplica si porcentaje_anticipo_pago = 100',
  `porcentaje_anticipo_pago` decimal(5,2) DEFAULT '100.00' COMMENT 'Porcentaje del total a pagar como anticipo (ej: 40.00 = 40%). Si es 100.00 = pago único',
  `dias_anticipo_pago` int DEFAULT '0' COMMENT 'Días para pagar el anticipo desde la firma del presupuesto. 0=al firmar, 7=a los 7 días, 30=a los 30 días',
  `porcentaje_final_pago` decimal(5,2) DEFAULT '0.00' COMMENT 'Porcentaje restante del total (ej: 60.00 = 60%). Debe sumar 100% con el anticipo. Si es 0 = pago único',
  `dias_final_pago` int DEFAULT '0' COMMENT 'Días para el pago final. Positivo=días desde firma (30=a 30 días), Negativo=días antes del evento (-7=7 días antes), 0=al finalizar evento',
  `observaciones_pago` text COMMENT 'Observaciones internas sobre esta forma de pago',
  `activo_pago` tinyint(1) DEFAULT '1' COMMENT 'Si FALSE, la forma de pago no estará disponible para nuevos presupuestos',
  `created_at_pago` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at_pago` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `foto_elemento`
--

CREATE TABLE `foto_elemento` (
  `id_foto_elemento` int UNSIGNED NOT NULL,
  `id_elemento` int UNSIGNED NOT NULL,
  `descripcion_foto_elemento` text,
  `archivo_foto` varchar(500) NOT NULL COMMENT 'Ruta completa con nombre del archivo de imagen',
  `privado_foto` tinyint(1) DEFAULT '0' COMMENT 'Si TRUE, solo visible para administración',
  `observaciones_foto` text,
  `activo_foto` tinyint(1) DEFAULT '1',
  `created_at_foto` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at_foto` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `furgoneta`
--

CREATE TABLE `furgoneta` (
  `id_furgoneta` int UNSIGNED NOT NULL,
  `matricula_furgoneta` varchar(20) NOT NULL COMMENT 'Matrícula del vehículo',
  `marca_furgoneta` varchar(100) DEFAULT NULL COMMENT 'Marca del vehículo (Renault, Mercedes, Ford, etc.)',
  `modelo_furgoneta` varchar(100) DEFAULT NULL COMMENT 'Modelo del vehículo (Master, Sprinter, Transit, etc.)',
  `anio_furgoneta` int DEFAULT NULL COMMENT 'Año de fabricación',
  `numero_bastidor_furgoneta` varchar(50) DEFAULT NULL COMMENT 'Número de bastidor/chasis (VIN)',
  `kilometros_entre_revisiones_furgoneta` int UNSIGNED DEFAULT '10000' COMMENT 'Kilómetros entre revisiones preventivas (ej: 10000 km)',
  `fecha_proxima_itv_furgoneta` date DEFAULT NULL COMMENT 'Fecha de vencimiento de la ITV',
  `fecha_vencimiento_seguro_furgoneta` date DEFAULT NULL COMMENT 'Fecha de vencimiento del seguro',
  `compania_seguro_furgoneta` varchar(255) DEFAULT NULL COMMENT 'Compañía aseguradora',
  `numero_poliza_seguro_furgoneta` varchar(100) DEFAULT NULL COMMENT 'Número de póliza del seguro',
  `capacidad_carga_kg_furgoneta` decimal(10,2) DEFAULT NULL COMMENT 'Capacidad de carga en kilogramos',
  `capacidad_carga_m3_furgoneta` decimal(10,2) DEFAULT NULL COMMENT 'Capacidad de carga en metros cúbicos',
  `tipo_combustible_furgoneta` varchar(50) DEFAULT NULL COMMENT 'Tipo de combustible (Diesel, Gasolina, Eléctrico, Híbrido)',
  `consumo_medio_furgoneta` decimal(5,2) DEFAULT NULL COMMENT 'Consumo medio en L/100km',
  `taller_habitual_furgoneta` varchar(255) DEFAULT NULL COMMENT 'Taller donde se realizan los mantenimientos habitualmente',
  `telefono_taller_furgoneta` varchar(50) DEFAULT NULL COMMENT 'Teléfono del taller habitual',
  `estado_furgoneta` enum('operativa','taller','baja') DEFAULT 'operativa' COMMENT 'Estado actual del vehículo',
  `observaciones_furgoneta` text COMMENT 'Observaciones generales sobre el vehículo',
  `activo_furgoneta` tinyint(1) DEFAULT '1' COMMENT 'TRUE: Vehículo activo | FALSE: Vehículo dado de baja',
  `created_at_furgoneta` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at_furgoneta` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Vehículos de la empresa (furgonetas de transporte)';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `furgoneta_mantenimiento`
--

CREATE TABLE `furgoneta_mantenimiento` (
  `id_mantenimiento` int UNSIGNED NOT NULL,
  `id_furgoneta` int UNSIGNED NOT NULL COMMENT 'Furgoneta a la que pertenece este mantenimiento',
  `fecha_mantenimiento` date NOT NULL COMMENT 'Fecha en que se realizó el mantenimiento',
  `tipo_mantenimiento` enum('revision','reparacion','itv','neumaticos','otros') NOT NULL COMMENT 'Tipo de mantenimiento realizado',
  `descripcion_mantenimiento` text NOT NULL COMMENT 'Descripción detallada del trabajo realizado',
  `kilometraje_mantenimiento` int UNSIGNED DEFAULT NULL COMMENT 'Kilometraje del vehículo en el momento del mantenimiento',
  `costo_mantenimiento` decimal(10,2) DEFAULT '0.00' COMMENT 'Coste total del mantenimiento/reparación',
  `numero_factura_mantenimiento` varchar(100) DEFAULT NULL COMMENT 'Número de factura del taller',
  `taller_mantenimiento` varchar(255) DEFAULT NULL COMMENT 'Nombre del taller que realizó el trabajo',
  `telefono_taller_mantenimiento` varchar(50) DEFAULT NULL COMMENT 'Teléfono del taller',
  `direccion_taller_mantenimiento` varchar(255) DEFAULT NULL COMMENT 'Dirección del taller',
  `resultado_itv` enum('favorable','desfavorable','negativa') DEFAULT NULL COMMENT 'Resultado de la ITV (solo si tipo_mantenimiento = itv)',
  `fecha_proxima_itv` date DEFAULT NULL COMMENT 'Nueva fecha de ITV (si aplica)',
  `garantia_hasta_mantenimiento` date DEFAULT NULL COMMENT 'Fecha hasta la que cubre la garantía del trabajo',
  `observaciones_mantenimiento` text COMMENT 'Observaciones adicionales sobre el mantenimiento',
  `activo_mantenimiento` tinyint(1) DEFAULT '1' COMMENT 'TRUE: Registro activo | FALSE: Registro anulado',
  `created_at_mantenimiento` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at_mantenimiento` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Historial de mantenimientos y reparaciones de furgonetas';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `furgoneta_registro_kilometraje`
--

CREATE TABLE `furgoneta_registro_kilometraje` (
  `id_registro_km` int UNSIGNED NOT NULL,
  `id_furgoneta` int UNSIGNED NOT NULL COMMENT 'Furgoneta a la que pertenece este registro',
  `fecha_registro_km` date NOT NULL COMMENT 'Fecha en que se realizó la lectura',
  `kilometraje_registrado_km` int UNSIGNED NOT NULL COMMENT 'Kilometraje leído en el cuentakilómetros',
  `tipo_registro_km` enum('manual','revision','itv','evento') DEFAULT 'manual' COMMENT 'Origen del registro: manual, revisión, ITV, evento',
  `observaciones_registro_km` text COMMENT 'Observaciones sobre este registro específico',
  `created_at_registro_km` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at_registro_km` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Registro histórico de kilometraje de furgonetas';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `grupo_articulo`
--

CREATE TABLE `grupo_articulo` (
  `id_grupo` int UNSIGNED NOT NULL,
  `codigo_grupo` varchar(20) NOT NULL,
  `nombre_grupo` varchar(100) NOT NULL,
  `descripcion_grupo` varchar(255) DEFAULT NULL,
  `observaciones_grupo` text,
  `activo_grupo` tinyint(1) DEFAULT '1',
  `created_at_grupo` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at_grupo` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `impuesto`
--

CREATE TABLE `impuesto` (
  `id_impuesto` int NOT NULL,
  `tipo_impuesto` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `tasa_impuesto` decimal(5,2) NOT NULL,
  `descr_impuesto` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `activo_impuesto` tinyint(1) DEFAULT '1',
  `coeficiente_familia` tinyint DEFAULT NULL COMMENT '1 = SI APLICA, 0 = NO APLICA COEFICIENTE DE DESCUANTO',
  `created_at_impuesto` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at_impuesto` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `kit`
--

CREATE TABLE `kit` (
  `id_kit` int UNSIGNED NOT NULL,
  `cantidad_kit` int UNSIGNED NOT NULL DEFAULT '1' COMMENT 'Cantidad del artículo componente en el kit',
  `id_articulo_maestro` int UNSIGNED NOT NULL COMMENT 'Artículo principal (el KIT)',
  `id_articulo_componente` int UNSIGNED NOT NULL COMMENT 'Artículo que forma parte del kit',
  `activo_kit` tinyint(1) NOT NULL DEFAULT '1',
  `created_at_kit` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at_kit` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci COMMENT='Composición de artículos tipo KIT';

--
-- Disparadores `kit`
--
DELIMITER $$
CREATE TRIGGER `trg_kit_before_insert` BEFORE INSERT ON `kit` FOR EACH ROW BEGIN
    DECLARE v_es_kit_maestro TINYINT(1);
    DECLARE v_es_kit_componente TINYINT(1);
    
    -- ----------------------------------------
    -- VALIDACIÓN 1: Auto-referencia
    -- ----------------------------------------
    IF NEW.id_articulo_maestro = NEW.id_articulo_componente THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'ERROR: Un artículo no puede ser componente de sí mismo';
    END IF;
    
    -- ----------------------------------------
    -- VALIDACIÓN 2: El maestro debe tener es_kit_articulo = 1
    -- ----------------------------------------
    SELECT es_kit_articulo INTO v_es_kit_maestro
    FROM articulo 
    WHERE id_articulo = NEW.id_articulo_maestro;
    
    IF v_es_kit_maestro = 0 OR v_es_kit_maestro IS NULL THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'ERROR: El artículo maestro debe tener es_kit_articulo = 1';
    END IF;
    
    -- ----------------------------------------
    -- VALIDACIÓN 3: El componente NO puede ser un KIT (evitar recursividad)
    -- ----------------------------------------
    SELECT es_kit_articulo INTO v_es_kit_componente
    FROM articulo 
    WHERE id_articulo = NEW.id_articulo_componente;
    
    IF v_es_kit_componente = 1 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'ERROR: Un componente no puede ser a su vez un KIT (evitar recursividad)';
    END IF;
    
    -- ----------------------------------------
    -- VALIDACIÓN 4: Cantidad debe ser positiva
    -- ----------------------------------------
    IF NEW.cantidad_kit <= 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'ERROR: La cantidad debe ser mayor a 0';
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_kit_before_update` BEFORE UPDATE ON `kit` FOR EACH ROW BEGIN
    DECLARE v_es_kit_maestro TINYINT(1);
    DECLARE v_es_kit_componente TINYINT(1);
    
    -- ----------------------------------------
    -- VALIDACIÓN 1: Auto-referencia
    -- ----------------------------------------
    IF NEW.id_articulo_maestro = NEW.id_articulo_componente THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'ERROR: Un artículo no puede ser componente de sí mismo';
    END IF;
    
    -- ----------------------------------------
    -- VALIDACIÓN 2: El maestro debe tener es_kit_articulo = 1
    -- ----------------------------------------
    SELECT es_kit_articulo INTO v_es_kit_maestro
    FROM articulo 
    WHERE id_articulo = NEW.id_articulo_maestro;
    
    IF v_es_kit_maestro = 0 OR v_es_kit_maestro IS NULL THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'ERROR: El artículo maestro debe tener es_kit_articulo = 1';
    END IF;
    
    -- ----------------------------------------
    -- VALIDACIÓN 3: El componente NO puede ser un KIT
    -- ----------------------------------------
    SELECT es_kit_articulo INTO v_es_kit_componente
    FROM articulo 
    WHERE id_articulo = NEW.id_articulo_componente;
    
    IF v_es_kit_componente = 1 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'ERROR: Un componente no puede ser a su vez un KIT (evitar recursividad)';
    END IF;
    
    -- ----------------------------------------
    -- VALIDACIÓN 4: Cantidad debe ser positiva
    -- ----------------------------------------
    IF NEW.cantidad_kit <= 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'ERROR: La cantidad debe ser mayor a 0';
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `linea_presupuesto`
--

CREATE TABLE `linea_presupuesto` (
  `id_linea_ppto` int UNSIGNED NOT NULL,
  `id_version_presupuesto` int UNSIGNED NOT NULL COMMENT 'FK: Versión del presupuesto a la que pertenece esta línea',
  `id_articulo` int UNSIGNED DEFAULT NULL COMMENT 'FK: Artículo original (NULL para líneas tipo texto/sección)',
  `id_linea_padre` int UNSIGNED DEFAULT NULL COMMENT 'FK: Línea padre para componentes de KIT (NULL si es línea principal)',
  `id_ubicacion` int UNSIGNED DEFAULT NULL COMMENT 'FK: Ubicación específica de montaje',
  `id_coeficiente` int UNSIGNED DEFAULT NULL COMMENT 'FK: Coeficiente reductor aplicado',
  `id_impuesto` int DEFAULT NULL COMMENT 'FK: Tipo de impuesto/IVA aplicado (INT sin UNSIGNED por compatibilidad con tabla impuesto)',
  `numero_linea_ppto` int NOT NULL COMMENT 'Número de línea visual en el presupuesto',
  `tipo_linea_ppto` enum('articulo','kit','componente_kit','seccion','texto','subtotal') COLLATE utf8mb4_spanish2_ci DEFAULT 'articulo' COMMENT 'Tipo de línea',
  `nivel_jerarquia` tinyint DEFAULT '0' COMMENT 'Nivel de anidamiento: 0=principal, 1=componente KIT, 2=sub-componente',
  `orden_linea_ppto` int DEFAULT '0' COMMENT 'Orden de visualización',
  `codigo_linea_ppto` varchar(50) COLLATE utf8mb4_spanish2_ci DEFAULT NULL COMMENT 'Código del artículo',
  `descripcion_linea_ppto` text COLLATE utf8mb4_spanish2_ci NOT NULL COMMENT 'Descripción de la línea',
  `fecha_montaje_linea_ppto` date DEFAULT NULL COMMENT 'Fecha orientativa de montaje (informativa para planning)',
  `fecha_desmontaje_linea_ppto` date DEFAULT NULL COMMENT 'Fecha orientativa de desmontaje (informativa para planning)',
  `fecha_inicio_linea_ppto` date DEFAULT NULL COMMENT 'Fecha REAL de inicio para el cobro (heredada pero modificable)',
  `fecha_fin_linea_ppto` date DEFAULT NULL COMMENT 'Fecha REAL de fin para el cobro (heredada pero modificable)',
  `cantidad_linea_ppto` decimal(10,2) DEFAULT '1.00' COMMENT 'Cantidad de unidades',
  `precio_unitario_linea_ppto` decimal(10,2) DEFAULT '0.00' COMMENT 'Precio unitario base (heredado del artículo pero modificable)',
  `descuento_linea_ppto` decimal(5,2) DEFAULT '0.00' COMMENT 'Descuento porcentual específico de la línea (%)',
  `aplicar_coeficiente_linea_ppto` tinyint(1) DEFAULT '0' COMMENT 'Si se aplica coeficiente reductor (Sí/No)',
  `valor_coeficiente_linea_ppto` decimal(10,2) DEFAULT NULL COMMENT 'Valor del coeficiente aplicado',
  `jornadas_linea_ppto` int DEFAULT NULL COMMENT 'Número de jornadas para cálculo del coeficiente',
  `porcentaje_iva_linea_ppto` decimal(5,2) DEFAULT '21.00' COMMENT 'Porcentaje de IVA aplicado',
  `observaciones_linea_ppto` text COLLATE utf8mb4_spanish2_ci COMMENT 'Observaciones específicas de esta línea',
  `mostrar_obs_articulo_linea_ppto` tinyint(1) DEFAULT '1' COMMENT 'Si mostrar las observaciones del artículo original',
  `ocultar_detalle_kit_linea_ppto` tinyint(1) DEFAULT '0' COMMENT 'TRUE: no mostrar desglose del KIT | FALSE: mostrar componentes',
  `mostrar_en_presupuesto` tinyint(1) DEFAULT '1' COMMENT 'Si se muestra al cliente en el presupuesto',
  `es_opcional` tinyint(1) DEFAULT '0' COMMENT 'Si es una línea opcional',
  `activo_linea_ppto` tinyint(1) DEFAULT '1' COMMENT 'Estado activo/inactivo',
  `created_at_linea_ppto` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha de creación',
  `updated_at_linea_ppto` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Fecha de última actualización'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci COMMENT='Líneas de detalle de versiones de presupuesto con soporte para KITs jerárquicos';

--
-- Disparadores `linea_presupuesto`
--
DELIMITER $$
CREATE TRIGGER `trg_linea_presupuesto_before_delete` BEFORE DELETE ON `linea_presupuesto` FOR EACH ROW BEGIN
    DECLARE estado_version VARCHAR(20);
    
    -- Obtener estado de la versión
    SELECT estado_version_presupuesto INTO estado_version
    FROM presupuesto_version
    WHERE id_version_presupuesto = OLD.id_version_presupuesto;
    
    -- Bloquear si no es borrador
    IF estado_version != 'borrador' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'ERROR: No se pueden eliminar líneas de versiones que no están en borrador. El histórico debe permanecer inmutable.';
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_linea_presupuesto_before_update` BEFORE UPDATE ON `linea_presupuesto` FOR EACH ROW BEGIN
    DECLARE estado_version VARCHAR(20);
    
    -- Obtener estado de la versión a la que pertenece esta línea
    SELECT estado_version_presupuesto INTO estado_version
    FROM presupuesto_version
    WHERE id_version_presupuesto = NEW.id_version_presupuesto;
    
    -- Bloquear si no es borrador
    IF estado_version != 'borrador' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'ERROR: No se pueden modificar líneas de versiones que no están en borrador. Para hacer cambios, cree una nueva versión.';
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llamadas`
--

CREATE TABLE `llamadas` (
  `id_llamada` int NOT NULL,
  `id_metodo` int NOT NULL,
  `nombre_comunicante` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `domicilio_instalacion` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `telefono_fijo` varchar(15) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `telefono_movil` varchar(15) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email_contacto` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `fecha_hora_preferida` datetime DEFAULT NULL,
  `observaciones` text COLLATE utf8mb4_general_ci,
  `id_comercial_asignado` int NOT NULL,
  `estado` int NOT NULL,
  `fecha_recepcion` datetime DEFAULT CURRENT_TIMESTAMP,
  `activo_llamada` tinyint DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `llamadas_con_comerciales_y_metodos`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `llamadas_con_comerciales_y_metodos` (
`id_llamada` int
,`id_metodo` int
,`nombre_comunicante` varchar(100)
,`domicilio_instalacion` varchar(200)
,`telefono_fijo` varchar(15)
,`telefono_movil` varchar(15)
,`email_contacto` varchar(50)
,`fecha_hora_preferida` datetime
,`observaciones` text
,`id_comercial_asignado` int
,`estado` int
,`fecha_recepcion` datetime
,`activo_llamada` tinyint
,`nombre_comercial` varchar(50)
,`nombre_metodo` varchar(50)
,`imagen_metodo` varchar(255)
,`descripcion_estado` varchar(100)
,`archivos_adjuntos` text
,`tiene_contactos` int
,`estado_es_3` int
,`tiene_adjuntos` int
,`fecha_primer_contacto` datetime
);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `marca`
--

CREATE TABLE `marca` (
  `id_marca` int UNSIGNED NOT NULL,
  `codigo_marca` varchar(20) NOT NULL,
  `nombre_marca` varchar(100) NOT NULL,
  `name_marca` varchar(100) NOT NULL COMMENT 'nombre en inglés',
  `descr_marca` varchar(255) DEFAULT NULL,
  `activo_marca` tinyint(1) DEFAULT '1',
  `created_at_marca` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at_marca` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `metodos_contacto`
--

CREATE TABLE `metodos_contacto` (
  `id_metodo` int NOT NULL,
  `nombre` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `permite_adjuntos` tinyint DEFAULT '0',
  `estado` tinyint DEFAULT '1',
  `imagen_metodo` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Ruta o nombre de la imagen del método de contacto'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `metodo_forma_pago`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `metodo_forma_pago` (
`codigo_pago` varchar(20)
,`nombre_pago` varchar(100)
,`nombre_metodo_pago` varchar(100)
,`porcentaje_anticipo_pago` decimal(5,2)
,`dias_anticipo_pago` int
,`porcentaje_final_pago` decimal(5,2)
,`dias_final_pago` int
,`descuento_pago` decimal(5,2)
,`tipo_pago` varchar(16)
);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `metodo_pago`
--

CREATE TABLE `metodo_pago` (
  `id_metodo_pago` int UNSIGNED NOT NULL,
  `codigo_metodo_pago` varchar(20) NOT NULL,
  `nombre_metodo_pago` varchar(100) NOT NULL,
  `observaciones_metodo_pago` text,
  `activo_metodo_pago` tinyint(1) DEFAULT '1',
  `created_at_metodo_pago` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at_metodo_pago` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `observacion_general`
--

CREATE TABLE `observacion_general` (
  `id_obs_general` int UNSIGNED NOT NULL,
  `codigo_obs_general` varchar(20) NOT NULL,
  `titulo_obs_general` varchar(100) NOT NULL,
  `title_obs_general` varchar(100) NOT NULL DEFAULT '' COMMENT 'Título en inglés',
  `texto_obs_general` text NOT NULL,
  `text_obs_general` text NOT NULL COMMENT 'Texto en inglés',
  `orden_obs_general` int DEFAULT '0',
  `tipo_obs_general` enum('condiciones','tecnicas','legales','comerciales','otras') DEFAULT 'otras',
  `obligatoria_obs_general` tinyint(1) DEFAULT '1' COMMENT 'Si TRUE, siempre aparece en presupuestos',
  `activo_obs_general` tinyint(1) DEFAULT '1',
  `created_at_obs_general` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at_obs_general` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `presupuesto`
--

CREATE TABLE `presupuesto` (
  `id_presupuesto` int UNSIGNED NOT NULL,
  `id_empresa` int UNSIGNED DEFAULT NULL COMMENT 'Empresa que emite el presupuesto (ficticia o real)',
  `numero_presupuesto` varchar(50) NOT NULL,
  `id_cliente` int UNSIGNED NOT NULL,
  `id_contacto_cliente` int UNSIGNED DEFAULT NULL,
  `id_estado_ppto` int UNSIGNED NOT NULL,
  `version_actual_presupuesto` int UNSIGNED DEFAULT '1' COMMENT 'Número de versión activa actual (la que se muestra/edita)',
  `estado_general_presupuesto` enum('borrador','enviado','aprobado','rechazado','cancelado') DEFAULT 'borrador' COMMENT 'Estado general del presupuesto (sincronizado con versión actual)',
  `id_forma_pago` int UNSIGNED DEFAULT NULL,
  `id_metodo` int DEFAULT NULL,
  `fecha_presupuesto` date NOT NULL COMMENT 'Fecha de emisión del presupuesto',
  `fecha_validez_presupuesto` date DEFAULT NULL COMMENT 'Fecha hasta la que es válido el presupuesto',
  `fecha_inicio_evento_presupuesto` date DEFAULT NULL COMMENT 'Fecha de inicio del evento/servicio',
  `fecha_fin_evento_presupuesto` date DEFAULT NULL COMMENT 'Fecha de finalización del evento/servicio',
  `numero_pedido_cliente_presupuesto` varchar(80) DEFAULT NULL COMMENT 'Número de pedido del cliente (si lo proporciona)',
  `aplicar_coeficientes_presupuesto` tinyint(1) DEFAULT '1' COMMENT 'TRUE: aplicar coeficientes reductores por días. FALSE: usar precio base sin reducción',
  `descuento_presupuesto` decimal(5,2) NOT NULL DEFAULT '0.00' COMMENT 'Porcentaje de descuento aplicado en este presupuesto (0.00 a 100.00). Se hereda de porcentaje_descuento_cliente pero puede modificarse',
  `nombre_evento_presupuesto` varchar(255) DEFAULT NULL COMMENT 'Nombre del evento o proyecto',
  `direccion_evento_presupuesto` varchar(100) DEFAULT NULL COMMENT 'Dirección del evento',
  `poblacion_evento_presupuesto` varchar(80) DEFAULT NULL COMMENT 'Población/Ciudad del evento',
  `cp_evento_presupuesto` varchar(10) DEFAULT NULL COMMENT 'Código postal del evento',
  `provincia_evento_presupuesto` varchar(80) DEFAULT NULL COMMENT 'Provincia del evento',
  `observaciones_cabecera_presupuesto` text COMMENT 'Observaciones iniciales del presupuesto',
  `observaciones_cabecera_ingles_presupuesto` text COMMENT 'Observaciones iniciales del presupuesto en inglés',
  `observaciones_pie_presupuesto` text COMMENT 'Observaciones específicas adicionales al pie',
  `observaciones_pie_ingles_presupuesto` text COMMENT 'Observaciones específicas adicionales al pie en inglés',
  `mostrar_obs_familias_presupuesto` tinyint(1) DEFAULT '1' COMMENT 'Si TRUE, muestra observaciones de las familias usadas',
  `mostrar_obs_articulos_presupuesto` tinyint(1) DEFAULT '1' COMMENT 'Si TRUE, muestra observaciones de los artículos usados',
  `observaciones_internas_presupuesto` text COMMENT 'Notas internas, no se imprimen en el PDF',
  `activo_presupuesto` tinyint(1) DEFAULT '1',
  `created_at_presupuesto` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at_presupuesto` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ;

--
-- Disparadores `presupuesto`
--
DELIMITER $$
CREATE TRIGGER `trg_presupuesto_after_insert` AFTER INSERT ON `presupuesto` FOR EACH ROW BEGIN
    -- Crear automáticamente la versión 1
    -- El presupuesto ya tiene version_actual_presupuesto = 1 por DEFAULT
    -- No es necesario hacer UPDATE
    
    INSERT INTO presupuesto_version (
        id_presupuesto,
        numero_version_presupuesto,
        version_padre_presupuesto,
        estado_version_presupuesto,
        creado_por_version,
        motivo_modificacion_version,
        fecha_creacion_version
    ) VALUES (
        NEW.id_presupuesto,
        1,                              -- Siempre es versión 1
        NULL,                           -- No tiene padre
        'borrador',                     -- Empieza como borrador
        1,                              -- Usuario por defecto (TODO: cambiar cuando exista tabla usuario)
        'Versión inicial',              -- Motivo por defecto
        NOW()                           -- Fecha actual
    );
    
    -- ✅ CORRECCIÓN: Ya NO hacemos UPDATE aquí
    -- Los valores se establecen por DEFAULT en la tabla:
    --   - version_actual_presupuesto = 1 (por DEFAULT)
    --   - estado_general_presupuesto = 'borrador' (por DEFAULT)
    
    -- NOTA: Si en el INSERT se especificaron valores diferentes,
    -- esos valores se respetan y NO se sobrescriben
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_presupuesto_before_desactivar` BEFORE UPDATE ON `presupuesto` FOR EACH ROW BEGIN
    -- Si se está desactivando (1 → 0)
    IF OLD.activo_presupuesto = 1 AND NEW.activo_presupuesto = 0 THEN
        
        -- Cambiar a CANCELADO
        SET NEW.id_estado_ppto = (
            SELECT id_estado_ppto 
            FROM estado_presupuesto 
            WHERE codigo_estado_ppto = 'CANC' 
            LIMIT 1
        );
        
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_presupuesto_before_insert` BEFORE INSERT ON `presupuesto` FOR EACH ROW BEGIN
    DECLARE v_serie VARCHAR(10);
    DECLARE v_numero_actual INT;
    DECLARE v_anio VARCHAR(4);
    DECLARE v_id_empresa INT UNSIGNED;
    
    -- Obtener el año actual
    SET v_anio = YEAR(CURDATE());
    
    -- Obtener la empresa ficticia principal (para presupuestos)
    SELECT 
        id_empresa,
        serie_presupuesto_empresa,
        numero_actual_presupuesto_empresa + 1
    INTO 
        v_id_empresa,
        v_serie,
        v_numero_actual
    FROM empresa
    WHERE empresa_ficticia_principal = TRUE
    AND activo_empresa = TRUE
    LIMIT 1;
    
    -- Generar el número de presupuesto
    -- Formato: SERIE-NUMERO/AÑO (Ejemplo: P-00001/2025)
    SET NEW.numero_presupuesto = CONCAT(
        v_serie,
        '-',
        LPAD(v_numero_actual, 5, '0'),
        '/',
        v_anio
    );
    
    -- Actualizar el contador en la tabla empresa
    UPDATE empresa 
    SET numero_actual_presupuesto_empresa = v_numero_actual
    WHERE id_empresa = v_id_empresa;
    
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_presupuesto_before_reactivar` BEFORE UPDATE ON `presupuesto` FOR EACH ROW BEGIN
    -- Si se está reactivando (0 → 1)
    IF OLD.activo_presupuesto = 0 AND NEW.activo_presupuesto = 1 THEN
        
        -- Cambiar a EN PROCESO
        SET NEW.id_estado_ppto = (
            SELECT id_estado_ppto 
            FROM estado_presupuesto 
            WHERE codigo_estado_ppto = 'PROC' 
            LIMIT 1
        );
        
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_presupuesto_estado_cancelado` BEFORE UPDATE ON `presupuesto` FOR EACH ROW BEGIN
    DECLARE v_codigo_cancelado VARCHAR(20);
    
    -- Obtener el código de estado CANCELADO
    SELECT codigo_estado_ppto 
    INTO v_codigo_cancelado
    FROM estado_presupuesto 
    WHERE id_estado_ppto = NEW.id_estado_ppto;
    
    -- Si el nuevo estado es CANCELADO (código 'CANC')
    IF v_codigo_cancelado = 'CANC' THEN
        -- Desactivar el presupuesto automáticamente
        SET NEW.activo_presupuesto = 0;
    END IF;
    
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_presupuesto_estado_no_cancelado` BEFORE UPDATE ON `presupuesto` FOR EACH ROW BEGIN
    DECLARE v_codigo_viejo VARCHAR(20);
    DECLARE v_codigo_nuevo VARCHAR(20);
    
    -- Obtener el código del estado antiguo
    SELECT codigo_estado_ppto 
    INTO v_codigo_viejo
    FROM estado_presupuesto 
    WHERE id_estado_ppto = OLD.id_estado_ppto;
    
    -- Obtener el código del estado nuevo
    SELECT codigo_estado_ppto 
    INTO v_codigo_nuevo
    FROM estado_presupuesto 
    WHERE id_estado_ppto = NEW.id_estado_ppto;
    
    -- Si el estado anterior era CANCELADO y el nuevo NO es CANCELADO
    IF v_codigo_viejo = 'CANC' AND v_codigo_nuevo != 'CANC' THEN
        -- Reactivar el presupuesto automáticamente
        SET NEW.activo_presupuesto = 1;
    END IF;
    
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `presupuesto_version`
--

CREATE TABLE `presupuesto_version` (
  `id_version_presupuesto` int UNSIGNED NOT NULL COMMENT '? ID único de TABLA (AUTO_INCREMENT). NO confundir con numero_version_presupuesto. \r\n            Ejemplo: Si tienes 3 presupuestos con 2 versiones cada uno, tendrás IDs 1-6,\r\n            pero cada presupuesto tendrá sus propias versiones 1 y 2',
  `id_presupuesto` int UNSIGNED NOT NULL COMMENT '? FK a tabla presupuesto (cabecera). \r\n            Indica a qué presupuesto pertenece esta versión.\r\n            Múltiples versiones pueden apuntar al mismo id_presupuesto',
  `numero_version_presupuesto` int UNSIGNED NOT NULL COMMENT '? Número LÓGICO de versión dentro de este presupuesto (1, 2, 3...).\r\n            Es secuencial DENTRO de cada presupuesto.\r\n            Presupuesto A: versiones 1, 2, 3\r\n            Presupuesto B: versiones 1, 2 (independiente de A)\r\n            ⚠️ NO confundir con id_version_presupuesto',
  `version_padre_presupuesto` int UNSIGNED DEFAULT NULL COMMENT '?‍? ID de la versión anterior (genealogía).\r\n            NULL = Versión original (primera)\r\n            Si tiene valor = ID de la versión desde la cual se creó esta\r\n            Ejemplo: Versión 3 creada desde versión 2 → version_padre = id de versión 2\r\n            Permite rastrear el árbol de cambios',
  `estado_version_presupuesto` enum('borrador','enviado','aprobado','rechazado','cancelado') CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci NOT NULL DEFAULT 'borrador' COMMENT '? Estado específico de ESTA versión (workflow).\r\n            - borrador: En edición, se pueden modificar líneas\r\n            - enviado: Enviado al cliente, bloqueado para edición\r\n            - aprobado: Cliente aceptó, bloqueado permanentemente\r\n            - rechazado: Cliente rechazó, se puede crear nueva versión\r\n            - cancelado: Versión cancelada, no se usa\r\n            ⚠️ Solo "borrador" permite editar líneas',
  `motivo_modificacion_version` text CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci COMMENT '? Razón por la que se creó esta versión.\r\n            - Versión 1: "Versión inicial"\r\n            - Versión 2: "Cliente solicitó cambio de precios"\r\n            - Versión 3: "Reducción de equipos por presupuesto"\r\n            Ayuda a entender el historial de cambios',
  `fecha_creacion_version` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '? Fecha y hora de creación de esta versión.\r\n            Se establece automáticamente al crear el registro.\r\n            Útil para auditoría y timeline del presupuesto',
  `creado_por_version` int UNSIGNED NOT NULL COMMENT '? ID del usuario que creó esta versión.\r\n            Permite rastrear responsabilidades.\r\n            TODO: Vincular con tabla usuarios cuando exista\r\n            Actualmente = 1 por defecto',
  `fecha_envio_version` datetime DEFAULT NULL COMMENT '? Fecha y hora de envío al cliente.\r\n            NULL = Aún no enviado\r\n            Se establece automáticamente al cambiar estado a "enviado"\r\n            Marca el momento en que el cliente recibió esta versión',
  `enviado_por_version` int UNSIGNED DEFAULT NULL COMMENT '? ID del usuario que envió esta versión al cliente.\r\n            NULL = No enviado aún\r\n            Permite rastrear quién realizó el envío',
  `fecha_aprobacion_version` datetime DEFAULT NULL COMMENT '✅ Fecha y hora de aprobación del cliente.\r\n            NULL = No aprobado\r\n            Se establece al cambiar estado a "aprobado"\r\n            Importante para facturación y producción',
  `fecha_rechazo_version` datetime DEFAULT NULL COMMENT '❌ Fecha y hora de rechazo del cliente.\r\n            NULL = No rechazado\r\n            Se establece al cambiar estado a "rechazado"\r\n            Indica que se necesita nueva versión',
  `motivo_rechazo_version` text CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci COMMENT '? Razón por la que el cliente rechazó esta versión.\r\n            NULL = No rechazado o no especificado\r\n            Ejemplo: "Precio muy alto", "Faltan equipos"\r\n            Ayuda a crear la siguiente versión correctamente',
  `ruta_pdf_version` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci DEFAULT NULL COMMENT '? Ruta del archivo PDF generado para esta versión.\r\n            NULL = PDF no generado\r\n            Formato: /documentos/presupuestos/P-00001-2026_v1.pdf\r\n            Se genera automáticamente al enviar.\r\n            Mantiene histórico de PDFs enviados',
  `activo_version` tinyint(1) DEFAULT '1' COMMENT '?️ Soft delete: 1=activo, 0=eliminado lógicamente.\r\n            NO usar DELETE físico, cambiar a 0 para "eliminar"\r\n            Mantiene histórico completo en BD',
  `created_at_version` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '⏱️ Timestamp de creación del registro en BD.\r\n            Auditoría técnica del sistema',
  `updated_at_version` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '⏱️ Timestamp de última actualización del registro.\r\n            Se actualiza automáticamente en cada UPDATE.\r\n            Auditoría técnica del sistema'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci COMMENT='Tabla de control de versiones de presupuestos. Cada registro representa una versión específica con su historial completo de cambios y estados';

--
-- Disparadores `presupuesto_version`
--
DELIMITER $$
CREATE TRIGGER `trg_presupuesto_version_before_delete` BEFORE DELETE ON `presupuesto_version` FOR EACH ROW BEGIN
    DECLARE num_lineas INT;
    DECLARE tiene_hijos INT;
    
    -- Contar líneas asociadas a esta versión
    SELECT COUNT(*) INTO num_lineas
    FROM linea_presupuesto
    WHERE id_version_presupuesto = OLD.id_version_presupuesto;
    
    -- Bloquear si tiene líneas
    IF num_lineas > 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'ERROR: No se puede eliminar una versión que tiene líneas asociadas. Elimine primero las líneas.';
    END IF;
    
    -- Contar versiones hijas (que tienen esta como padre)
    SELECT COUNT(*) INTO tiene_hijos
    FROM presupuesto_version
    WHERE version_padre_presupuesto = OLD.id_version_presupuesto;
    
    -- Bloquear si tiene versiones hijas
    IF tiene_hijos > 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'ERROR: No se puede eliminar una versión que tiene versiones hijas. Esto rompería la cadena genealógica.';
    END IF;
    
    -- Bloquear si no está en borrador (versiones enviadas/aprobadas/rechazadas deben permanecer)
    IF OLD.estado_version_presupuesto != 'borrador' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'ERROR: No se pueden eliminar versiones que no están en borrador. El histórico debe ser inmutable.';
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_presupuesto_version_before_insert_numero` BEFORE INSERT ON `presupuesto_version` FOR EACH ROW BEGIN
    DECLARE max_version INT;
    
    -- Si no se especificó número de versión, calcularlo automáticamente
    IF NEW.numero_version_presupuesto IS NULL OR NEW.numero_version_presupuesto = 0 THEN
        
        -- Obtener el número de versión más alto actual para este presupuesto
        SELECT COALESCE(MAX(numero_version_presupuesto), 0) INTO max_version
        FROM presupuesto_version
        WHERE id_presupuesto = NEW.id_presupuesto;
        
        -- Asignar el siguiente número de versión
        SET NEW.numero_version_presupuesto = max_version + 1;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_presupuesto_version_before_insert_validar` BEFORE INSERT ON `presupuesto_version` FOR EACH ROW BEGIN
    DECLARE estado_actual VARCHAR(20);
    DECLARE version_actual INT;
    
    -- Obtener estado y número de la versión actual
    SELECT 
        pv.estado_version_presupuesto,
        p.version_actual_presupuesto
    INTO 
        estado_actual,
        version_actual
    FROM presupuesto p
    LEFT JOIN presupuesto_version pv 
        ON pv.id_presupuesto = p.id_presupuesto 
        AND pv.numero_version_presupuesto = p.version_actual_presupuesto
    WHERE p.id_presupuesto = NEW.id_presupuesto;
    
    -- REGLA 1: No crear versiones si está aprobada o cancelada
    IF estado_actual IN ('aprobado', 'cancelado') THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'ERROR: No se pueden crear nuevas versiones de presupuestos aprobados o cancelados. El presupuesto está cerrado.';
    END IF;
    
    -- REGLA 2: No crear nueva versión si existe una en borrador
    IF estado_actual = 'borrador' AND NEW.numero_version_presupuesto > 1 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'ERROR: No se puede crear una nueva versión mientras existe una versión en borrador. Complete o envíe la versión actual primero.';
    END IF;
    
    -- REGLA 3: Solo se pueden crear versiones desde estados 'enviado' o 'rechazado'
    IF NEW.numero_version_presupuesto > 1 
       AND estado_actual NOT IN ('enviado', 'rechazado') THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'ERROR: Solo se pueden crear nuevas versiones desde estados enviado o rechazado.';
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_version_auto_fechas` BEFORE UPDATE ON `presupuesto_version` FOR EACH ROW BEGIN
    -- Cuando se marca como 'enviado'
    IF NEW.estado_version_presupuesto = 'enviado' 
       AND OLD.estado_version_presupuesto != 'enviado' 
       AND NEW.fecha_envio_version IS NULL THEN
        SET NEW.fecha_envio_version = NOW();
    END IF;
    
    -- Cuando se marca como 'aprobado'
    IF NEW.estado_version_presupuesto = 'aprobado' 
       AND OLD.estado_version_presupuesto != 'aprobado' 
       AND NEW.fecha_aprobacion_version IS NULL THEN
        SET NEW.fecha_aprobacion_version = NOW();
    END IF;
    
    -- Cuando se marca como 'rechazado'
    IF NEW.estado_version_presupuesto = 'rechazado' 
       AND OLD.estado_version_presupuesto != 'rechazado' 
       AND NEW.fecha_rechazo_version IS NULL THEN
        SET NEW.fecha_rechazo_version = NOW();
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_version_auto_ruta_pdf` BEFORE UPDATE ON `presupuesto_version` FOR EACH ROW BEGIN
    DECLARE numero_ppto VARCHAR(50);
    
    -- Solo generar ruta cuando se envía y no existe ruta
    IF NEW.estado_version_presupuesto = 'enviado' 
       AND OLD.estado_version_presupuesto != 'enviado'
       AND (NEW.ruta_pdf_version IS NULL OR NEW.ruta_pdf_version = '') THEN
        
        -- Obtener número de presupuesto
        SELECT numero_presupuesto INTO numero_ppto
        FROM presupuesto
        WHERE id_presupuesto = NEW.id_presupuesto;
        
        -- Generar ruta: /documentos/presupuestos/PPTO-2025-001_v2.pdf
        SET NEW.ruta_pdf_version = CONCAT(
            '/documentos/presupuestos/',
            numero_ppto,
            '_v',
            NEW.numero_version_presupuesto,
            '.pdf'
        );
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_version_sync_estado_cabecera` AFTER UPDATE ON `presupuesto_version` FOR EACH ROW BEGIN
    DECLARE version_actual INT;
    
    -- Obtener la versión actual del presupuesto
    SELECT version_actual_presupuesto INTO version_actual
    FROM presupuesto
    WHERE id_presupuesto = NEW.id_presupuesto;
    
    -- Si esta es la versión actual, sincronizar estado en la cabecera
    IF NEW.numero_version_presupuesto = version_actual THEN
        UPDATE presupuesto
        SET estado_general_presupuesto = NEW.estado_version_presupuesto
        WHERE id_presupuesto = NEW.id_presupuesto;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_version_validar_transicion_estado` BEFORE UPDATE ON `presupuesto_version` FOR EACH ROW BEGIN
    -- Solo validar si cambió el estado
    IF OLD.estado_version_presupuesto != NEW.estado_version_presupuesto THEN
        
        -- DESDE BORRADOR: solo puede ir a 'enviado' o 'cancelado'
        IF OLD.estado_version_presupuesto = 'borrador' 
           AND NEW.estado_version_presupuesto NOT IN ('enviado', 'cancelado') THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'ERROR: Desde borrador solo se puede pasar a enviado o cancelado. Workflow inválido.';
        END IF;
        
        -- DESDE ENVIADO: solo puede ir a 'aprobado', 'rechazado' o 'cancelado'
        IF OLD.estado_version_presupuesto = 'enviado' 
           AND NEW.estado_version_presupuesto NOT IN ('aprobado', 'rechazado', 'cancelado') THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'ERROR: Desde enviado solo se puede pasar a aprobado, rechazado o cancelado. Workflow inválido.';
        END IF;
        
        -- ESTADOS FINALES: 'aprobado' y 'cancelado' no pueden cambiar
        IF OLD.estado_version_presupuesto IN ('aprobado', 'cancelado') THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'ERROR: No se puede cambiar el estado de versiones aprobadas o canceladas. Son estados finales e inmutables.';
        END IF;
        
        -- DESDE RECHAZADO: solo puede ir a 'cancelado'
        IF OLD.estado_version_presupuesto = 'rechazado' 
           AND NEW.estado_version_presupuesto != 'cancelado' THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'ERROR: Una versión rechazada solo puede cancelarse. Para nuevos intentos, cree una nueva versión.';
        END IF;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedor`
--

CREATE TABLE `proveedor` (
  `id_proveedor` int UNSIGNED NOT NULL,
  `codigo_proveedor` varchar(20) NOT NULL,
  `nombre_proveedor` varchar(255) NOT NULL,
  `direccion_proveedor` varchar(255) DEFAULT NULL,
  `cp_proveedor` varchar(10) DEFAULT NULL,
  `poblacion_proveedor` varchar(100) DEFAULT NULL,
  `provincia_proveedor` varchar(100) DEFAULT NULL,
  `nif_proveedor` varchar(20) DEFAULT NULL,
  `telefono_proveedor` varchar(255) DEFAULT NULL,
  `fax_proveedor` varchar(50) DEFAULT NULL,
  `web_proveedor` varchar(255) DEFAULT NULL,
  `email_proveedor` varchar(255) DEFAULT NULL,
  `persona_contacto_proveedor` varchar(255) DEFAULT NULL,
  `direccion_sat_proveedor` varchar(255) DEFAULT NULL,
  `cp_sat_proveedor` varchar(10) DEFAULT NULL,
  `poblacion_sat_proveedor` varchar(100) DEFAULT NULL,
  `provincia_sat_proveedor` varchar(100) DEFAULT NULL,
  `telefono_sat_proveedor` varchar(255) DEFAULT NULL,
  `fax_sat_proveedor` varchar(50) DEFAULT NULL,
  `email_sat_proveedor` varchar(255) DEFAULT NULL,
  `id_forma_pago_habitual` int UNSIGNED DEFAULT NULL COMMENT 'Forma de pago habitual del proveedor. Se usará por defecto en nuevas órdenes de compra',
  `observaciones_proveedor` text,
  `activo_proveedor` tinyint(1) DEFAULT '1',
  `created_at_proveedor` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at_proveedor` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id_rol` int NOT NULL,
  `nombre_rol` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish2_ci NOT NULL,
  `est` tinyint DEFAULT '1' COMMENT 'est = 0 --> Inactivo, est = 1 --> Activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish2_ci COMMENT='Tabla que contiene los distintos roles de usuario';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_documento`
--

CREATE TABLE `tipo_documento` (
  `id_tipo_documento` int UNSIGNED NOT NULL,
  `codigo_tipo_documento` varchar(20) COLLATE utf8mb4_spanish_ci NOT NULL COMMENT 'Código alfanumérico único del tipo (ej: SEG, MAN, PROC)',
  `nombre_tipo_documento` varchar(100) COLLATE utf8mb4_spanish_ci NOT NULL COMMENT 'Nombre descriptivo del tipo de documento',
  `descripcion_tipo_documento` text COLLATE utf8mb4_spanish_ci COMMENT 'Descripción detallada del tipo de documento',
  `activo_tipo_documento` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Estado del registro: 1=activo, 0=inactivo',
  `created_at_tipo_documento` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha y hora de creación del registro',
  `updated_at_tipo_documento` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Fecha y hora de última modificación'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci COMMENT='Catálogo de tipos de documentos para el gestor documental de técnicos';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `unidad_medida`
--

CREATE TABLE `unidad_medida` (
  `id_unidad` int UNSIGNED NOT NULL,
  `nombre_unidad` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `name_unidad` varchar(50) COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Nombre en ingles.',
  `descr_unidad` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `simbolo_unidad` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `activo_unidad` tinyint(1) DEFAULT '1',
  `created_at_unidad` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at_unidad` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int NOT NULL,
  `email` varchar(60) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish2_ci DEFAULT NULL,
  `contrasena` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish2_ci DEFAULT NULL,
  `nombre` varchar(60) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish2_ci DEFAULT NULL,
  `fecha_crea` datetime DEFAULT CURRENT_TIMESTAMP,
  `est` tinyint DEFAULT NULL COMMENT 'est = 0 --> Inactivo, est =1 --> activo',
  `id_rol` int NOT NULL,
  `tokenUsu` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish2_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish2_ci COMMENT='Tabla de usuario con contraseñas y roles asociados';

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `usuarios_con_rol`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `usuarios_con_rol` (
`id_usuario` int
,`nombre` varchar(60)
,`email` varchar(60)
,`contrasena` varchar(255)
,`fecha_crea` datetime
,`est` tinyint
,`id_rol` int
,`nombre_rol` varchar(50)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vacaciones_con_nombre`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vacaciones_con_nombre` (
`id_vacacion` int
,`id_comercial` int
,`fecha_inicio` date
,`fecha_fin` date
,`descripcion` varchar(50)
,`activo_vacacion` tinyint
,`nombre_comercial` varchar(101)
);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `visitas_cerradas`
--

CREATE TABLE `visitas_cerradas` (
  `id_visita_cerrada` int NOT NULL,
  `fecha_visita_cerrada` datetime NOT NULL,
  `id_contacto` int NOT NULL,
  `id_llamada` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_adjuntos_con_comunicante`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_adjuntos_con_comunicante` (
`id_adjunto` int
,`id_llamada` int
,`nombre_archivo` varchar(255)
,`tipo` varchar(20)
,`fecha_subida` datetime
,`estado` tinyint
,`nombre_comunicante` varchar(100)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_articulo_completa`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_articulo_completa` (
`id_articulo` int unsigned
,`id_familia` int unsigned
,`id_unidad` int unsigned
,`id_impuesto` int
,`codigo_articulo` varchar(50)
,`nombre_articulo` varchar(255)
,`name_articulo` varchar(255)
,`imagen_articulo` varchar(255)
,`precio_alquiler_articulo` decimal(10,2)
,`coeficiente_articulo` tinyint(1)
,`es_kit_articulo` tinyint(1)
,`control_total_articulo` tinyint(1)
,`no_facturar_articulo` tinyint(1)
,`notas_presupuesto_articulo` text
,`notes_budget_articulo` text
,`orden_obs_articulo` int
,`observaciones_articulo` text
,`permitir_descuentos_articulo` tinyint(1)
,`activo_articulo` tinyint(1)
,`created_at_articulo` timestamp
,`updated_at_articulo` timestamp
,`id_grupo` int unsigned
,`codigo_familia` varchar(20)
,`nombre_familia` varchar(100)
,`name_familia` varchar(100)
,`descr_familia` varchar(255)
,`imagen_familia` varchar(150)
,`coeficiente_familia` tinyint
,`observaciones_presupuesto_familia` text
,`observations_budget_familia` text
,`orden_obs_familia` int
,`permite_descuento_familia` tinyint(1)
,`activo_familia_relacionada` tinyint(1)
,`tipo_impuesto` varchar(20)
,`tasa_impuesto` decimal(5,2)
,`descr_impuesto` varchar(255)
,`activo_impuesto_relacionado` tinyint(1)
,`nombre_unidad` varchar(50)
,`name_unidad` varchar(50)
,`descr_unidad` varchar(255)
,`simbolo_unidad` varchar(10)
,`activo_unidad_relacionada` tinyint(1)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_cliente_completa`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_cliente_completa` (
`id_cliente` int unsigned
,`codigo_cliente` varchar(20)
,`nombre_cliente` varchar(255)
,`direccion_cliente` varchar(255)
,`cp_cliente` varchar(10)
,`poblacion_cliente` varchar(100)
,`provincia_cliente` varchar(100)
,`nif_cliente` varchar(20)
,`telefono_cliente` varchar(255)
,`fax_cliente` varchar(50)
,`web_cliente` varchar(255)
,`email_cliente` varchar(255)
,`nombre_facturacion_cliente` varchar(255)
,`direccion_facturacion_cliente` varchar(255)
,`cp_facturacion_cliente` varchar(10)
,`poblacion_facturacion_cliente` varchar(100)
,`provincia_facturacion_cliente` varchar(100)
,`observaciones_cliente` text
,`activo_cliente` tinyint(1)
,`created_at_cliente` timestamp
,`updated_at_cliente` timestamp
,`id_forma_pago_habitual` int unsigned
,`codigo_pago` varchar(20)
,`nombre_pago` varchar(100)
,`descuento_pago` decimal(5,2)
,`porcentaje_anticipo_pago` decimal(5,2)
,`dias_anticipo_pago` int
,`porcentaje_final_pago` decimal(5,2)
,`dias_final_pago` int
,`observaciones_pago` text
,`activo_pago` tinyint(1)
,`id_metodo_pago` int unsigned
,`codigo_metodo_pago` varchar(20)
,`nombre_metodo_pago` varchar(100)
,`observaciones_metodo_pago` text
,`activo_metodo_pago` tinyint(1)
,`tipo_pago_cliente` varchar(17)
,`descripcion_forma_pago_cliente` varchar(219)
,`direccion_completa_cliente` varchar(470)
,`direccion_facturacion_completa_cliente` varchar(470)
,`tiene_direccion_facturacion_diferente` int
,`estado_forma_pago_cliente` varchar(23)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_cliente_ubicaciones`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_cliente_ubicaciones` (
`id_cliente` int unsigned
,`codigo_cliente` varchar(20)
,`nombre_cliente` varchar(255)
,`nif_cliente` varchar(20)
,`telefono_cliente` varchar(255)
,`email_cliente` varchar(255)
,`activo_cliente` tinyint(1)
,`id_ubicacion` int unsigned
,`nombre_ubicacion` varchar(255)
,`direccion_ubicacion` varchar(255)
,`codigo_postal_ubicacion` varchar(10)
,`poblacion_ubicacion` varchar(100)
,`provincia_ubicacion` varchar(100)
,`pais_ubicacion` varchar(100)
,`persona_contacto_ubicacion` varchar(255)
,`telefono_contacto_ubicacion` varchar(50)
,`email_contacto_ubicacion` varchar(255)
,`observaciones_ubicacion` text
,`es_principal_ubicacion` tinyint(1)
,`activo_ubicacion` tinyint(1)
,`direccion_completa_cliente` varchar(470)
,`direccion_completa_ubicacion` text
,`tipo_ubicacion` varchar(10)
,`estado_completo` varchar(18)
,`tiene_contacto_propio` int
,`total_ubicaciones_cliente` bigint
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_costos_furgoneta`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_costos_furgoneta` (
`id_furgoneta` int unsigned
,`matricula_furgoneta` varchar(20)
,`marca_furgoneta` varchar(100)
,`modelo_furgoneta` varchar(100)
,`anio_furgoneta` int
,`costo_total` decimal(32,2)
,`costo_anio_actual` decimal(32,2)
,`costo_revisiones` decimal(32,2)
,`costo_reparaciones` decimal(32,2)
,`costo_itv` decimal(32,2)
,`costo_neumaticos` decimal(32,2)
,`total_mantenimientos` bigint
,`costo_promedio` decimal(14,6)
,`fecha_ultimo_mantenimiento` date
,`kilometraje_actual` bigint unsigned
,`costo_por_km` decimal(36,6)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_elementos_completa`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_elementos_completa` (
`id_elemento` int unsigned
,`codigo_elemento` varchar(50)
,`codigo_barras_elemento` varchar(100)
,`descripcion_elemento` varchar(255)
,`numero_serie_elemento` varchar(100)
,`modelo_elemento` varchar(100)
,`nave_elemento` varchar(50)
,`pasillo_columna_elemento` varchar(50)
,`altura_elemento` varchar(50)
,`ubicacion_completa_elemento` varchar(156)
,`fecha_compra_elemento` date
,`precio_compra_elemento` decimal(10,2)
,`fecha_alta_elemento` date
,`fecha_fin_garantia_elemento` date
,`proximo_mantenimiento_elemento` date
,`observaciones_elemento` text
,`es_propio_elemento` tinyint(1)
,`id_proveedor_compra_elemento` int unsigned
,`id_proveedor_alquiler_elemento` int unsigned
,`precio_dia_alquiler_elemento` decimal(10,2)
,`id_forma_pago_alquiler_elemento` int unsigned
,`observaciones_alquiler_elemento` text
,`codigo_proveedor_compra` varchar(20)
,`nombre_proveedor_compra` varchar(255)
,`telefono_proveedor_compra` varchar(255)
,`email_proveedor_compra` varchar(255)
,`nif_proveedor_compra` varchar(20)
,`codigo_proveedor_alquiler` varchar(20)
,`nombre_proveedor_alquiler` varchar(255)
,`telefono_proveedor_alquiler` varchar(255)
,`email_proveedor_alquiler` varchar(255)
,`nif_proveedor_alquiler` varchar(20)
,`persona_contacto_proveedor_alquiler` varchar(255)
,`codigo_forma_pago_alquiler` varchar(20)
,`nombre_forma_pago_alquiler` varchar(100)
,`porcentaje_anticipo_alquiler` decimal(5,2)
,`dias_anticipo_alquiler` int
,`porcentaje_final_alquiler` decimal(5,2)
,`dias_final_alquiler` int
,`descuento_forma_pago_alquiler` decimal(5,2)
,`codigo_metodo_pago_alquiler` varchar(20)
,`nombre_metodo_pago_alquiler` varchar(100)
,`id_articulo` int unsigned
,`codigo_articulo` varchar(50)
,`nombre_articulo` varchar(255)
,`name_articulo` varchar(255)
,`precio_alquiler_articulo` decimal(10,2)
,`id_familia` int unsigned
,`codigo_familia` varchar(20)
,`nombre_familia` varchar(100)
,`name_familia` varchar(100)
,`id_grupo` int unsigned
,`codigo_grupo` varchar(20)
,`nombre_grupo` varchar(100)
,`id_marca` int unsigned
,`codigo_marca` varchar(20)
,`nombre_marca` varchar(100)
,`id_estado_elemento` int unsigned
,`codigo_estado_elemento` varchar(20)
,`descripcion_estado_elemento` varchar(50)
,`color_estado_elemento` varchar(7)
,`permite_alquiler_estado_elemento` tinyint(1)
,`activo_elemento` tinyint(1)
,`created_at_elemento` timestamp
,`updated_at_elemento` timestamp
,`jerarquia_completa_elemento` text
,`tipo_propiedad_elemento` varchar(21)
,`proveedor_principal_elemento` varchar(255)
,`estado_configuracion_alquiler` varchar(43)
,`descripcion_forma_pago_alquiler` varchar(219)
,`costo_mensual_estimado_alquiler` decimal(12,2)
,`dias_en_servicio_elemento` int
,`anios_en_servicio_elemento` decimal(13,2)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_furgoneta_completa`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_furgoneta_completa` (
`id_furgoneta` int unsigned
,`matricula_furgoneta` varchar(20)
,`marca_furgoneta` varchar(100)
,`modelo_furgoneta` varchar(100)
,`anio_furgoneta` int
,`numero_bastidor_furgoneta` varchar(50)
,`kilometros_entre_revisiones_furgoneta` int unsigned
,`fecha_proxima_itv_furgoneta` date
,`fecha_vencimiento_seguro_furgoneta` date
,`compania_seguro_furgoneta` varchar(255)
,`numero_poliza_seguro_furgoneta` varchar(100)
,`capacidad_carga_kg_furgoneta` decimal(10,2)
,`capacidad_carga_m3_furgoneta` decimal(10,2)
,`tipo_combustible_furgoneta` varchar(50)
,`consumo_medio_furgoneta` decimal(5,2)
,`taller_habitual_furgoneta` varchar(255)
,`telefono_taller_furgoneta` varchar(50)
,`estado_furgoneta` enum('operativa','taller','baja')
,`observaciones_furgoneta` text
,`activo_furgoneta` tinyint(1)
,`created_at_furgoneta` timestamp
,`updated_at_furgoneta` timestamp
,`kilometraje_actual` bigint unsigned
,`fecha_ultimo_registro_km` date
,`total_mantenimientos` bigint
,`costo_total_mantenimientos` decimal(32,2)
,`fecha_ultimo_mantenimiento` date
,`estado_itv` varchar(11)
,`estado_seguro` varchar(14)
,`km_desde_ultima_revision` bigint unsigned
,`necesita_revision` int
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_kilometraje_completo`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_kilometraje_completo` (
`id_registro_km` int unsigned
,`id_furgoneta` int unsigned
,`fecha_registro_km` date
,`kilometraje_registrado_km` int unsigned
,`tipo_registro_km` enum('manual','revision','itv','evento')
,`observaciones_registro_km` text
,`created_at_registro_km` timestamp
,`updated_at_registro_km` timestamp
,`matricula_furgoneta` varchar(20)
,`marca_furgoneta` varchar(100)
,`modelo_furgoneta` varchar(100)
,`estado_furgoneta` enum('operativa','taller','baja')
,`km_recorridos` decimal(11,0)
,`dias_transcurridos` int
,`km_promedio_diario` decimal(14,2)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_kit_completa`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_kit_completa` (
`id_kit` int unsigned
,`cantidad_kit` int unsigned
,`activo_kit` tinyint(1)
,`created_at_kit` timestamp
,`updated_at_kit` timestamp
,`id_articulo_maestro` int unsigned
,`codigo_articulo_maestro` varchar(50)
,`nombre_articulo_maestro` varchar(255)
,`name_articulo_maestro` varchar(255)
,`precio_articulo_maestro` decimal(10,2)
,`es_kit_articulo_maestro` tinyint(1)
,`activo_articulo_maestro` tinyint(1)
,`id_articulo_componente` int unsigned
,`codigo_articulo_componente` varchar(50)
,`nombre_articulo_componente` varchar(255)
,`name_articulo_componente` varchar(255)
,`precio_articulo_componente` decimal(10,2)
,`es_kit_articulo_componente` tinyint(1)
,`activo_articulo_componente` tinyint(1)
,`subtotal_componente` decimal(20,2)
,`total_componentes_kit` bigint
,`precio_total_kit` decimal(42,2)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_mantenimiento_completo`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_mantenimiento_completo` (
`id_mantenimiento` int unsigned
,`id_furgoneta` int unsigned
,`fecha_mantenimiento` date
,`tipo_mantenimiento` enum('revision','reparacion','itv','neumaticos','otros')
,`descripcion_mantenimiento` text
,`kilometraje_mantenimiento` int unsigned
,`costo_mantenimiento` decimal(10,2)
,`numero_factura_mantenimiento` varchar(100)
,`taller_mantenimiento` varchar(255)
,`telefono_taller_mantenimiento` varchar(50)
,`direccion_taller_mantenimiento` varchar(255)
,`resultado_itv` enum('favorable','desfavorable','negativa')
,`fecha_proxima_itv` date
,`garantia_hasta_mantenimiento` date
,`observaciones_mantenimiento` text
,`activo_mantenimiento` tinyint(1)
,`created_at_mantenimiento` timestamp
,`updated_at_mantenimiento` timestamp
,`matricula_furgoneta` varchar(20)
,`marca_furgoneta` varchar(100)
,`modelo_furgoneta` varchar(100)
,`anio_furgoneta` int
,`estado_furgoneta` enum('operativa','taller','baja')
,`estado_garantia` varchar(16)
,`dias_desde_mantenimiento` int
,`km_desde_mantenimiento` bigint unsigned
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_presupuesto_completa`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_presupuesto_completa` (
`id_presupuesto` int unsigned
,`numero_presupuesto` varchar(50)
,`version_actual_presupuesto` int unsigned
,`fecha_presupuesto` date
,`fecha_validez_presupuesto` date
,`fecha_inicio_evento_presupuesto` date
,`fecha_fin_evento_presupuesto` date
,`numero_pedido_cliente_presupuesto` varchar(80)
,`aplicar_coeficientes_presupuesto` tinyint(1)
,`descuento_presupuesto` decimal(5,2)
,`nombre_evento_presupuesto` varchar(255)
,`direccion_evento_presupuesto` varchar(100)
,`poblacion_evento_presupuesto` varchar(80)
,`cp_evento_presupuesto` varchar(10)
,`provincia_evento_presupuesto` varchar(80)
,`observaciones_cabecera_presupuesto` text
,`observaciones_pie_presupuesto` text
,`observaciones_cabecera_ingles_presupuesto` text
,`observaciones_pie_ingles_presupuesto` text
,`mostrar_obs_familias_presupuesto` tinyint(1)
,`mostrar_obs_articulos_presupuesto` tinyint(1)
,`observaciones_internas_presupuesto` text
,`activo_presupuesto` tinyint(1)
,`created_at_presupuesto` timestamp
,`updated_at_presupuesto` timestamp
,`id_cliente` int unsigned
,`codigo_cliente` varchar(20)
,`nombre_cliente` varchar(255)
,`nif_cliente` varchar(20)
,`direccion_cliente` varchar(255)
,`cp_cliente` varchar(10)
,`poblacion_cliente` varchar(100)
,`provincia_cliente` varchar(100)
,`telefono_cliente` varchar(255)
,`email_cliente` varchar(255)
,`porcentaje_descuento_cliente` decimal(5,2)
,`nombre_facturacion_cliente` varchar(255)
,`direccion_facturacion_cliente` varchar(255)
,`cp_facturacion_cliente` varchar(10)
,`poblacion_facturacion_cliente` varchar(100)
,`provincia_facturacion_cliente` varchar(100)
,`id_contacto_cliente` int unsigned
,`nombre_contacto_cliente` varchar(100)
,`apellidos_contacto_cliente` varchar(150)
,`telefono_contacto_cliente` varchar(50)
,`email_contacto_cliente` varchar(255)
,`id_estado_ppto` int unsigned
,`codigo_estado_ppto` varchar(20)
,`nombre_estado_ppto` varchar(100)
,`color_estado_ppto` varchar(7)
,`orden_estado_ppto` int
,`id_forma_pago` int unsigned
,`codigo_pago` varchar(20)
,`nombre_pago` varchar(100)
,`porcentaje_anticipo_pago` decimal(5,2)
,`dias_anticipo_pago` int
,`porcentaje_final_pago` decimal(5,2)
,`dias_final_pago` int
,`descuento_pago` decimal(5,2)
,`id_metodo_pago` int unsigned
,`codigo_metodo_pago` varchar(20)
,`nombre_metodo_pago` varchar(100)
,`id_metodo_contacto` int
,`nombre_metodo_contacto` varchar(50)
,`id_forma_pago_habitual` int unsigned
,`nombre_forma_pago_habitual_cliente` varchar(100)
,`direccion_completa_evento_presupuesto` varchar(275)
,`direccion_completa_cliente` varchar(470)
,`direccion_facturacion_completa_cliente` varchar(470)
,`nombre_completo_contacto` varchar(251)
,`dias_validez_restantes` int
,`estado_validez_presupuesto` varchar(20)
,`duracion_evento_dias` bigint
,`dias_hasta_inicio_evento` int
,`dias_hasta_fin_evento` int
,`estado_evento_presupuesto` varchar(19)
,`prioridad_presupuesto` varchar(13)
,`tipo_pago_presupuesto` varchar(17)
,`descripcion_completa_forma_pago` varchar(219)
,`fecha_vencimiento_anticipo` date
,`fecha_vencimiento_final` date
,`comparacion_descuento` varchar(17)
,`estado_descuento_presupuesto` varchar(25)
,`aplica_descuento_presupuesto` int
,`diferencia_descuento` decimal(6,2)
,`tiene_direccion_facturacion_diferente` int
,`dias_desde_emision` int
,`id_version_actual` int unsigned
,`numero_version_actual` int unsigned
,`estado_version_actual` enum('borrador','enviado','aprobado','rechazado','cancelado')
,`fecha_creacion_version_actual` timestamp
,`estado_general_presupuesto` varchar(100)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_registro_kilometraje`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_registro_kilometraje` (
`id_registro_km` int unsigned
,`id_furgoneta` int unsigned
,`fecha_registro_km` date
,`kilometraje_registrado_km` int unsigned
,`tipo_registro_km` enum('manual','revision','itv','evento')
,`observaciones_registro_km` text
,`created_at_registro_km` timestamp
,`matricula_furgoneta` varchar(20)
,`marca_furgoneta` varchar(100)
,`modelo_furgoneta` varchar(100)
,`estado_furgoneta` enum('operativa','taller','baja')
,`km_recorridos` bigint unsigned
,`dias_transcurridos` int
,`km_promedio_diario` decimal(15,4)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `v_linea_presupuesto_calculada`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `v_linea_presupuesto_calculada` (
`id_linea_ppto` int unsigned
,`id_version_presupuesto` int unsigned
,`id_articulo` int unsigned
,`id_linea_padre` int unsigned
,`id_ubicacion` int unsigned
,`numero_linea_ppto` int
,`tipo_linea_ppto` enum('articulo','kit','componente_kit','seccion','texto','subtotal')
,`nivel_jerarquia` tinyint
,`codigo_linea_ppto` varchar(50)
,`descripcion_linea_ppto` text
,`orden_linea_ppto` int
,`observaciones_linea_ppto` text
,`mostrar_obs_articulo_linea_ppto` tinyint(1)
,`ocultar_detalle_kit_linea_ppto` tinyint(1)
,`mostrar_en_presupuesto` tinyint(1)
,`es_opcional` tinyint(1)
,`activo_linea_ppto` tinyint(1)
,`fecha_montaje_linea_ppto` date
,`fecha_desmontaje_linea_ppto` date
,`fecha_inicio_linea_ppto` date
,`fecha_fin_linea_ppto` date
,`cantidad_linea_ppto` decimal(10,2)
,`precio_unitario_linea_ppto` decimal(10,2)
,`descuento_linea_ppto` decimal(5,2)
,`porcentaje_iva_linea_ppto` decimal(5,2)
,`jornadas_linea_ppto` int
,`id_coeficiente` int unsigned
,`aplicar_coeficiente_linea_ppto` tinyint(1)
,`valor_coeficiente_linea_ppto` decimal(10,2)
,`jornadas_coeficiente` int
,`valor_coeficiente` decimal(10,2)
,`observaciones_coeficiente` text
,`activo_coeficiente` tinyint(1)
,`dias_linea` bigint
,`subtotal_sin_coeficiente` decimal(39,10)
,`base_imponible` decimal(41,12)
,`importe_iva` decimal(50,18)
,`total_linea` decimal(51,18)
,`precio_unitario_linea_ppto_hotel` decimal(20,8)
,`base_imponible_hotel` decimal(41,12)
,`importe_descuento_linea_ppto_hotel` decimal(50,18)
,`TotalImporte_descuento_linea_ppto_hotel` decimal(51,18)
,`importe_iva_linea_ppto_hotel` decimal(60,24)
,`TotalImporte_iva_linea_ppto_hotel` decimal(61,24)
,`codigo_articulo` varchar(50)
,`nombre_articulo` varchar(255)
,`name_articulo` varchar(255)
,`imagen_articulo` varchar(255)
,`precio_alquiler_articulo` decimal(10,2)
,`coeficiente_articulo` tinyint(1)
,`es_kit_articulo` tinyint(1)
,`control_total_articulo` tinyint(1)
,`no_facturar_articulo` tinyint(1)
,`notas_presupuesto_articulo` text
,`notes_budget_articulo` text
,`orden_obs_articulo` int
,`observaciones_articulo` text
,`activo_articulo` tinyint(1)
,`permitir_descuentos_articulo` tinyint(1)
,`id_familia` int unsigned
,`created_at_articulo` timestamp
,`updated_at_articulo` timestamp
,`id_impuesto_articulo` int
,`tipo_impuesto_articulo` varchar(20)
,`tasa_impuesto_articulo` decimal(5,2)
,`descr_impuesto_articulo` varchar(255)
,`activo_impuesto_articulo` tinyint(1)
,`id_unidad` int unsigned
,`nombre_unidad` varchar(50)
,`name_unidad` varchar(50)
,`descr_unidad` varchar(255)
,`simbolo_unidad` varchar(10)
,`activo_unidad` tinyint(1)
,`id_grupo` int unsigned
,`codigo_familia` varchar(20)
,`nombre_familia` varchar(100)
,`name_familia` varchar(100)
,`descr_familia` varchar(255)
,`imagen_familia` varchar(150)
,`coeficiente_familia` tinyint
,`observaciones_presupuesto_familia` text
,`observations_budget_familia` text
,`orden_obs_familia` int
,`permite_descuento_familia` tinyint(1)
,`activo_familia_relacionada` tinyint(1)
,`id_impuesto` int
,`tipo_impuesto` varchar(20)
,`tasa_impuesto` decimal(5,2)
,`descr_impuesto` varchar(255)
,`activo_impuesto` tinyint(1)
,`id_presupuesto` int unsigned
,`numero_version_presupuesto` int unsigned
,`estado_version_presupuesto` enum('borrador','enviado','aprobado','rechazado','cancelado')
,`fecha_creacion_version` timestamp
,`fecha_envio_version` datetime
,`fecha_aprobacion_version` datetime
,`numero_presupuesto` varchar(50)
,`fecha_presupuesto` date
,`fecha_validez_presupuesto` date
,`nombre_evento_presupuesto` varchar(255)
,`fecha_inicio_evento_presupuesto` date
,`fecha_fin_evento_presupuesto` date
,`id_cliente` int unsigned
,`id_estado_ppto` int unsigned
,`activo_presupuesto` tinyint(1)
,`nombre_cliente` varchar(255)
,`nif_cliente` varchar(20)
,`email_cliente` varchar(255)
,`telefono_cliente` varchar(255)
,`direccion_cliente` varchar(255)
,`cp_cliente` varchar(10)
,`poblacion_cliente` varchar(100)
,`provincia_cliente` varchar(100)
,`porcentaje_descuento_cliente` decimal(5,2)
,`duracion_evento_dias` bigint
,`dias_hasta_inicio_evento` int
,`dias_hasta_fin_evento` int
,`estado_evento_presupuesto` varchar(19)
,`prioridad_presupuesto` varchar(13)
,`tipo_pago_presupuesto` varchar(17)
,`descripcion_completa_forma_pago` varchar(219)
,`fecha_vencimiento_anticipo` date
,`fecha_vencimiento_final` date
,`comparacion_descuento` varchar(17)
,`estado_descuento_presupuesto` varchar(25)
,`aplica_descuento_presupuesto` int
,`diferencia_descuento` decimal(6,2)
,`tiene_direccion_facturacion_diferente` int
,`dias_desde_emision` int
,`id_version_actual` int unsigned
,`numero_version_actual` int unsigned
,`estado_version_actual` enum('borrador','enviado','aprobado','rechazado','cancelado')
,`fecha_creacion_version_actual` timestamp
,`estado_general_presupuesto` varchar(100)
,`mostrar_obs_familias_presupuesto` tinyint(1)
,`mostrar_obs_articulos_presupuesto` tinyint(1)
,`nombre_ubicacion` varchar(255)
,`direccion_ubicacion` varchar(255)
,`codigo_postal_ubicacion` varchar(10)
,`poblacion_ubicacion` varchar(100)
,`provincia_ubicacion` varchar(100)
,`pais_ubicacion` varchar(100)
,`persona_contacto_ubicacion` varchar(255)
,`telefono_contacto_ubicacion` varchar(50)
,`email_contacto_ubicacion` varchar(255)
,`observaciones_ubicacion` text
,`es_principal_ubicacion` tinyint(1)
,`activo_ubicacion` tinyint(1)
,`ubicacion_agrupacion` varchar(255)
,`ubicacion_completa_agrupacion` text
,`created_at_linea_ppto` timestamp
,`updated_at_linea_ppto` timestamp
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `v_observaciones_presupuesto`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `v_observaciones_presupuesto` (
`id_presupuesto` int unsigned
,`id_familia` int unsigned
,`id_articulo` int unsigned
,`tipo_observacion` varchar(8)
,`codigo_familia` varchar(50)
,`nombre_familia` varchar(255)
,`name_familia` varchar(255)
,`observacion_es` mediumtext
,`observacion_en` mediumtext
,`orden_observacion` int
,`mostrar_observacion` tinyint
,`numero_presupuesto` varchar(50)
,`nombre_evento_presupuesto` varchar(255)
,`id_cliente` int unsigned
,`nombre_cliente` varchar(255)
,`activo_origen` tinyint
,`activo_presupuesto` tinyint
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `v_presupuesto_totales`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `v_presupuesto_totales` (
`id_version_presupuesto` int unsigned
,`numero_version_presupuesto` int unsigned
,`estado_version_presupuesto` enum('borrador','enviado','aprobado','rechazado','cancelado')
,`fecha_creacion_version` timestamp
,`fecha_envio_version` datetime
,`fecha_aprobacion_version` datetime
,`id_presupuesto` int unsigned
,`numero_presupuesto` varchar(50)
,`fecha_presupuesto` date
,`fecha_validez_presupuesto` date
,`nombre_evento_presupuesto` varchar(255)
,`fecha_inicio_evento_presupuesto` date
,`fecha_fin_evento_presupuesto` date
,`id_cliente` int unsigned
,`nombre_cliente` varchar(255)
,`nif_cliente` varchar(20)
,`email_cliente` varchar(255)
,`telefono_cliente` varchar(255)
,`duracion_evento_dias` bigint
,`total_base_imponible` decimal(63,12)
,`total_iva` decimal(65,18)
,`total_con_iva` decimal(65,18)
,`total_base_imponible_hotel` decimal(63,12)
,`total_importe_descuento_linea_hotel` decimal(65,18)
,`total_despues_descuento_linea_hotel` decimal(65,18)
,`total_iva_hotel` decimal(65,24)
,`total_con_iva_hotel` decimal(65,24)
,`cantidad_lineas_total` bigint
,`cantidad_lineas_con_coeficiente` bigint
,`base_iva_21` decimal(63,12)
,`importe_iva_21` decimal(65,18)
,`total_iva_21` decimal(65,18)
,`base_iva_10` decimal(63,12)
,`importe_iva_10` decimal(65,18)
,`total_iva_10` decimal(65,18)
,`base_iva_4` decimal(63,12)
,`importe_iva_4` decimal(65,18)
,`total_iva_4` decimal(65,18)
,`base_iva_0` decimal(63,12)
,`importe_iva_0` decimal(65,18)
,`total_iva_0` decimal(65,18)
,`base_iva_otros` decimal(63,12)
,`importe_iva_otros` decimal(65,18)
,`total_iva_otros` decimal(65,18)
,`base_iva_21_hotel` decimal(63,12)
,`importe_iva_21_hotel` decimal(65,24)
,`total_iva_21_hotel` decimal(65,24)
,`base_iva_10_hotel` decimal(63,12)
,`importe_iva_10_hotel` decimal(65,24)
,`total_iva_10_hotel` decimal(65,24)
,`base_iva_4_hotel` decimal(63,12)
,`importe_iva_4_hotel` decimal(65,24)
,`total_iva_4_hotel` decimal(65,24)
,`base_iva_0_hotel` decimal(63,12)
,`importe_iva_0_hotel` decimal(65,24)
,`total_iva_0_hotel` decimal(65,24)
,`base_iva_otros_hotel` decimal(63,12)
,`importe_iva_otros_hotel` decimal(65,24)
,`total_iva_otros_hotel` decimal(65,24)
,`ahorro_total_coeficientes` decimal(64,12)
,`diferencia_base_imponible_cliente` decimal(64,12)
,`diferencia_total_con_iva_cliente` decimal(65,24)
,`fecha_primera_linea_creada` timestamp
,`fecha_ultima_modificacion_linea` timestamp
);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `adjunto_llamada`
--
ALTER TABLE `adjunto_llamada`
  ADD PRIMARY KEY (`id_adjunto`),
  ADD KEY `fk_id_llamada` (`id_llamada`);

--
-- Indices de la tabla `articulo`
--
ALTER TABLE `articulo`
  ADD PRIMARY KEY (`id_articulo`),
  ADD UNIQUE KEY `codigo_articulo` (`codigo_articulo`),
  ADD KEY `fk_articulo_unidad` (`id_unidad`),
  ADD KEY `idx_codigo_articulo` (`codigo_articulo`),
  ADD KEY `idx_id_familia_articulo` (`id_familia`),
  ADD KEY `idx_nombre_articulo` (`nombre_articulo`),
  ADD KEY `idx_es_kit_articulo` (`es_kit_articulo`),
  ADD KEY `idx_id_impuesto` (`id_impuesto`);

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `cliente`
--
ALTER TABLE `cliente`
  ADD PRIMARY KEY (`id_cliente`),
  ADD UNIQUE KEY `codigo_cliente` (`codigo_cliente`),
  ADD KEY `idx_codigo_cliente` (`codigo_cliente`),
  ADD KEY `idx_nombre_cliente` (`nombre_cliente`),
  ADD KEY `idx_nif_cliente` (`nif_cliente`),
  ADD KEY `idx_id_forma_pago_habitual` (`id_forma_pago_habitual`),
  ADD KEY `idx_porcentaje_descuento_cliente` (`porcentaje_descuento_cliente`);

--
-- Indices de la tabla `cliente_ubicacion`
--
ALTER TABLE `cliente_ubicacion`
  ADD PRIMARY KEY (`id_ubicacion`),
  ADD KEY `idx_id_cliente_ubicacion` (`id_cliente`),
  ADD KEY `idx_nombre_ubicacion` (`nombre_ubicacion`),
  ADD KEY `idx_poblacion_ubicacion` (`poblacion_ubicacion`),
  ADD KEY `idx_provincia_ubicacion` (`provincia_ubicacion`),
  ADD KEY `idx_es_principal_ubicacion` (`es_principal_ubicacion`),
  ADD KEY `idx_activo_ubicacion` (`activo_ubicacion`);

--
-- Indices de la tabla `coeficiente`
--
ALTER TABLE `coeficiente`
  ADD PRIMARY KEY (`id_coeficiente`),
  ADD UNIQUE KEY `jornadas_coeficiente` (`jornadas_coeficiente`);

--
-- Indices de la tabla `comerciales`
--
ALTER TABLE `comerciales`
  ADD PRIMARY KEY (`id_comercial`),
  ADD KEY `fk_comerciales_usuarios` (`id_usuario`);

--
-- Indices de la tabla `com_vacaciones`
--
ALTER TABLE `com_vacaciones`
  ADD PRIMARY KEY (`id_vacacion`),
  ADD KEY `fk_vacaciones_comercial` (`id_comercial`);

--
-- Indices de la tabla `contactos`
--
ALTER TABLE `contactos`
  ADD PRIMARY KEY (`id_contacto`),
  ADD KEY `id_llamada` (`id_llamada`),
  ADD KEY `fk_id_metodo` (`id_metodo`),
  ADD KEY `fk_contactos_visitas_cerradas` (`id_visita_cerrada`);

--
-- Indices de la tabla `contacto_cliente`
--
ALTER TABLE `contacto_cliente`
  ADD PRIMARY KEY (`id_contacto_cliente`),
  ADD KEY `idx_id_cliente_contacto` (`id_cliente`),
  ADD KEY `idx_nombre_contacto_cliente` (`nombre_contacto_cliente`);

--
-- Indices de la tabla `contacto_proveedor`
--
ALTER TABLE `contacto_proveedor`
  ADD PRIMARY KEY (`id_contacto_proveedor`),
  ADD KEY `idx_id_proveedor_contacto` (`id_proveedor`),
  ADD KEY `idx_nombre_contacto_proveedor` (`nombre_contacto_proveedor`);

--
-- Indices de la tabla `documento`
--
ALTER TABLE `documento`
  ADD PRIMARY KEY (`id_documento`),
  ADD KEY `idx_tipo_documento` (`id_tipo_documento_documento`),
  ADD KEY `idx_activo_documento` (`activo_documento`);

--
-- Indices de la tabla `documento_elemento`
--
ALTER TABLE `documento_elemento`
  ADD PRIMARY KEY (`id_documento_elemento`),
  ADD KEY `idx_id_elemento_documento` (`id_elemento`),
  ADD KEY `idx_tipo_documento` (`tipo_documento_elemento`),
  ADD KEY `idx_privado_documento` (`privado_documento`);

--
-- Indices de la tabla `elemento`
--
ALTER TABLE `elemento`
  ADD PRIMARY KEY (`id_elemento`),
  ADD UNIQUE KEY `codigo_elemento` (`codigo_elemento`),
  ADD UNIQUE KEY `codigo_barras_elemento` (`codigo_barras_elemento`),
  ADD UNIQUE KEY `numero_serie_elemento` (`numero_serie_elemento`),
  ADD KEY `idx_codigo_elemento` (`codigo_elemento`),
  ADD KEY `idx_codigo_barras_elemento` (`codigo_barras_elemento`),
  ADD KEY `idx_numero_serie_elemento` (`numero_serie_elemento`),
  ADD KEY `idx_id_articulo_elemento` (`id_articulo_elemento`),
  ADD KEY `idx_id_marca_elemento` (`id_marca_elemento`),
  ADD KEY `idx_id_estado_elemento` (`id_estado_elemento`),
  ADD KEY `idx_nave_elemento` (`nave_elemento`),
  ADD KEY `idx_pasillo_columna_elemento` (`pasillo_columna_elemento`),
  ADD KEY `idx_fecha_compra_elemento` (`fecha_compra_elemento`),
  ADD KEY `idx_es_propio_elemento` (`es_propio_elemento`),
  ADD KEY `idx_id_proveedor_compra_elemento` (`id_proveedor_compra_elemento`),
  ADD KEY `idx_id_proveedor_alquiler_elemento` (`id_proveedor_alquiler_elemento`),
  ADD KEY `idx_id_forma_pago_alquiler_elemento` (`id_forma_pago_alquiler_elemento`);

--
-- Indices de la tabla `empresa`
--
ALTER TABLE `empresa`
  ADD PRIMARY KEY (`id_empresa`),
  ADD UNIQUE KEY `codigo_empresa` (`codigo_empresa`),
  ADD UNIQUE KEY `nif_empresa` (`nif_empresa`),
  ADD KEY `idx_codigo_empresa` (`codigo_empresa`),
  ADD KEY `idx_nif_empresa` (`nif_empresa`),
  ADD KEY `idx_ficticia_empresa` (`ficticia_empresa`),
  ADD KEY `idx_empresa_ficticia_principal` (`empresa_ficticia_principal`),
  ADD KEY `idx_activo_empresa` (`activo_empresa`);

--
-- Indices de la tabla `estados_llamada`
--
ALTER TABLE `estados_llamada`
  ADD PRIMARY KEY (`id_estado`);

--
-- Indices de la tabla `estado_elemento`
--
ALTER TABLE `estado_elemento`
  ADD PRIMARY KEY (`id_estado_elemento`),
  ADD UNIQUE KEY `codigo_estado_elemento` (`codigo_estado_elemento`),
  ADD KEY `idx_codigo_estado_elemento` (`codigo_estado_elemento`);

--
-- Indices de la tabla `estado_presupuesto`
--
ALTER TABLE `estado_presupuesto`
  ADD PRIMARY KEY (`id_estado_ppto`),
  ADD UNIQUE KEY `codigo_estado_ppto` (`codigo_estado_ppto`),
  ADD KEY `idx_estado_presupuesto_activo` (`activo_estado_ppto`),
  ADD KEY `idx_estado_presupuesto_codigo` (`codigo_estado_ppto`),
  ADD KEY `idx_estado_presupuesto_orden` (`orden_estado_ppto`);

--
-- Indices de la tabla `familia`
--
ALTER TABLE `familia`
  ADD PRIMARY KEY (`id_familia`),
  ADD UNIQUE KEY `codigo_familia` (`codigo_familia`),
  ADD KEY `idx_id_grupo_familia` (`id_grupo`),
  ADD KEY `idx_permite_descuento_familia` (`permite_descuento_familia`);

--
-- Indices de la tabla `forma_pago`
--
ALTER TABLE `forma_pago`
  ADD PRIMARY KEY (`id_pago`),
  ADD UNIQUE KEY `codigo_pago` (`codigo_pago`),
  ADD KEY `idx_id_metodo_pago` (`id_metodo_pago`),
  ADD KEY `idx_activo_pago` (`activo_pago`);

--
-- Indices de la tabla `foto_elemento`
--
ALTER TABLE `foto_elemento`
  ADD PRIMARY KEY (`id_foto_elemento`),
  ADD KEY `idx_id_elemento_foto` (`id_elemento`),
  ADD KEY `idx_privado_foto` (`privado_foto`);

--
-- Indices de la tabla `furgoneta`
--
ALTER TABLE `furgoneta`
  ADD PRIMARY KEY (`id_furgoneta`),
  ADD UNIQUE KEY `matricula_furgoneta` (`matricula_furgoneta`),
  ADD KEY `idx_matricula_furgoneta` (`matricula_furgoneta`),
  ADD KEY `idx_estado_furgoneta` (`estado_furgoneta`),
  ADD KEY `idx_activo_furgoneta` (`activo_furgoneta`),
  ADD KEY `idx_fecha_proxima_itv` (`fecha_proxima_itv_furgoneta`);

--
-- Indices de la tabla `furgoneta_mantenimiento`
--
ALTER TABLE `furgoneta_mantenimiento`
  ADD PRIMARY KEY (`id_mantenimiento`),
  ADD KEY `idx_id_furgoneta_mantenimiento` (`id_furgoneta`),
  ADD KEY `idx_fecha_mantenimiento` (`fecha_mantenimiento`),
  ADD KEY `idx_tipo_mantenimiento` (`tipo_mantenimiento`),
  ADD KEY `idx_activo_mantenimiento` (`activo_mantenimiento`);

--
-- Indices de la tabla `furgoneta_registro_kilometraje`
--
ALTER TABLE `furgoneta_registro_kilometraje`
  ADD PRIMARY KEY (`id_registro_km`),
  ADD KEY `idx_id_furgoneta_registro` (`id_furgoneta`),
  ADD KEY `idx_fecha_registro_km` (`fecha_registro_km`),
  ADD KEY `idx_tipo_registro_km` (`tipo_registro_km`);

--
-- Indices de la tabla `grupo_articulo`
--
ALTER TABLE `grupo_articulo`
  ADD PRIMARY KEY (`id_grupo`),
  ADD UNIQUE KEY `codigo_grupo` (`codigo_grupo`);

--
-- Indices de la tabla `impuesto`
--
ALTER TABLE `impuesto`
  ADD PRIMARY KEY (`id_impuesto`);

--
-- Indices de la tabla `kit`
--
ALTER TABLE `kit`
  ADD PRIMARY KEY (`id_kit`),
  ADD UNIQUE KEY `uk_kit_componente` (`id_articulo_maestro`,`id_articulo_componente`),
  ADD KEY `idx_id_articulo_maestro` (`id_articulo_maestro`),
  ADD KEY `idx_id_articulo_componente` (`id_articulo_componente`);

--
-- Indices de la tabla `linea_presupuesto`
--
ALTER TABLE `linea_presupuesto`
  ADD PRIMARY KEY (`id_linea_ppto`),
  ADD KEY `fk_linea_ppto_coeficiente` (`id_coeficiente`),
  ADD KEY `idx_id_version_presupuesto_linea` (`id_version_presupuesto`),
  ADD KEY `idx_id_articulo_linea` (`id_articulo`),
  ADD KEY `idx_orden_linea_ppto` (`orden_linea_ppto`),
  ADD KEY `idx_tipo_linea` (`tipo_linea_ppto`),
  ADD KEY `idx_linea_padre` (`id_linea_padre`),
  ADD KEY `idx_fecha_montaje` (`fecha_montaje_linea_ppto`),
  ADD KEY `idx_fecha_inicio` (`fecha_inicio_linea_ppto`),
  ADD KEY `idx_ubicacion` (`id_ubicacion`),
  ADD KEY `idx_impuesto` (`id_impuesto`),
  ADD KEY `idx_activo` (`activo_linea_ppto`);

--
-- Indices de la tabla `llamadas`
--
ALTER TABLE `llamadas`
  ADD PRIMARY KEY (`id_llamada`);

--
-- Indices de la tabla `marca`
--
ALTER TABLE `marca`
  ADD PRIMARY KEY (`id_marca`),
  ADD UNIQUE KEY `codigo_marca` (`codigo_marca`);

--
-- Indices de la tabla `metodos_contacto`
--
ALTER TABLE `metodos_contacto`
  ADD PRIMARY KEY (`id_metodo`),
  ADD UNIQUE KEY `nombre_UNIQUE` (`nombre`);

--
-- Indices de la tabla `metodo_pago`
--
ALTER TABLE `metodo_pago`
  ADD PRIMARY KEY (`id_metodo_pago`),
  ADD UNIQUE KEY `codigo_metodo_pago` (`codigo_metodo_pago`),
  ADD KEY `idx_codigo_metodo_pago` (`codigo_metodo_pago`);

--
-- Indices de la tabla `observacion_general`
--
ALTER TABLE `observacion_general`
  ADD PRIMARY KEY (`id_obs_general`),
  ADD UNIQUE KEY `codigo_obs_general` (`codigo_obs_general`),
  ADD KEY `idx_orden_obs_general` (`orden_obs_general`),
  ADD KEY `idx_obligatoria_obs_general` (`obligatoria_obs_general`);

--
-- Indices de la tabla `presupuesto`
--
ALTER TABLE `presupuesto`
  ADD PRIMARY KEY (`id_presupuesto`),
  ADD UNIQUE KEY `numero_presupuesto` (`numero_presupuesto`),
  ADD KEY `fk_presupuesto_contacto` (`id_contacto_cliente`),
  ADD KEY `fk_presupuesto_forma_pago` (`id_forma_pago`),
  ADD KEY `fk_presupuesto_metodo_contacto` (`id_metodo`),
  ADD KEY `idx_numero_presupuesto` (`numero_presupuesto`),
  ADD KEY `idx_id_cliente_presupuesto` (`id_cliente`),
  ADD KEY `idx_id_estado_presupuesto` (`id_estado_ppto`),
  ADD KEY `idx_fecha_presupuesto` (`fecha_presupuesto`),
  ADD KEY `idx_fecha_inicio_evento` (`fecha_inicio_evento_presupuesto`),
  ADD KEY `idx_fecha_fin_evento` (`fecha_fin_evento_presupuesto`),
  ADD KEY `idx_numero_pedido_cliente` (`numero_pedido_cliente_presupuesto`),
  ADD KEY `idx_poblacion_evento` (`poblacion_evento_presupuesto`),
  ADD KEY `idx_provincia_evento` (`provincia_evento_presupuesto`),
  ADD KEY `idx_aplicar_coeficientes_presupuesto` (`aplicar_coeficientes_presupuesto`),
  ADD KEY `idx_descuento_presupuesto` (`descuento_presupuesto`),
  ADD KEY `idx_version_actual_presupuesto` (`version_actual_presupuesto`),
  ADD KEY `idx_estado_general_presupuesto` (`estado_general_presupuesto`),
  ADD KEY `idx_id_empresa_presupuesto` (`id_empresa`);

--
-- Indices de la tabla `presupuesto_version`
--
ALTER TABLE `presupuesto_version`
  ADD PRIMARY KEY (`id_version_presupuesto`),
  ADD KEY `idx_id_presupuesto_version` (`id_presupuesto`),
  ADD KEY `idx_numero_version` (`numero_version_presupuesto`),
  ADD KEY `idx_version_padre` (`version_padre_presupuesto`),
  ADD KEY `idx_estado_version` (`estado_version_presupuesto`),
  ADD KEY `idx_fecha_creacion_version` (`fecha_creacion_version`),
  ADD KEY `idx_fecha_envio_version` (`fecha_envio_version`),
  ADD KEY `idx_presupuesto_numero_version` (`id_presupuesto`,`numero_version_presupuesto`),
  ADD KEY `idx_creado_por` (`creado_por_version`),
  ADD KEY `idx_enviado_por` (`enviado_por_version`);

--
-- Indices de la tabla `proveedor`
--
ALTER TABLE `proveedor`
  ADD PRIMARY KEY (`id_proveedor`),
  ADD UNIQUE KEY `codigo_proveedor` (`codigo_proveedor`),
  ADD KEY `idx_codigo_proveedor` (`codigo_proveedor`),
  ADD KEY `idx_nombre_proveedor` (`nombre_proveedor`),
  ADD KEY `idx_nif_proveedor` (`nif_proveedor`),
  ADD KEY `idx_id_forma_pago_habitual` (`id_forma_pago_habitual`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id_rol`);

--
-- Indices de la tabla `tipo_documento`
--
ALTER TABLE `tipo_documento`
  ADD PRIMARY KEY (`id_tipo_documento`),
  ADD UNIQUE KEY `uk_codigo_tipo_documento` (`codigo_tipo_documento`),
  ADD KEY `idx_nombre_tipo_documento` (`nombre_tipo_documento`);

--
-- Indices de la tabla `unidad_medida`
--
ALTER TABLE `unidad_medida`
  ADD PRIMARY KEY (`id_unidad`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD KEY `id_rol` (`id_rol`);

--
-- Indices de la tabla `visitas_cerradas`
--
ALTER TABLE `visitas_cerradas`
  ADD PRIMARY KEY (`id_visita_cerrada`),
  ADD KEY `id_contacto` (`id_contacto`),
  ADD KEY `fk_visitas_llamadas` (`id_llamada`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `adjunto_llamada`
--
ALTER TABLE `adjunto_llamada`
  MODIFY `id_adjunto` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `articulo`
--
ALTER TABLE `articulo`
  MODIFY `id_articulo` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `cliente`
--
ALTER TABLE `cliente`
  MODIFY `id_cliente` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `cliente_ubicacion`
--
ALTER TABLE `cliente_ubicacion`
  MODIFY `id_ubicacion` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `coeficiente`
--
ALTER TABLE `coeficiente`
  MODIFY `id_coeficiente` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `comerciales`
--
ALTER TABLE `comerciales`
  MODIFY `id_comercial` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `com_vacaciones`
--
ALTER TABLE `com_vacaciones`
  MODIFY `id_vacacion` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `contactos`
--
ALTER TABLE `contactos`
  MODIFY `id_contacto` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `contacto_cliente`
--
ALTER TABLE `contacto_cliente`
  MODIFY `id_contacto_cliente` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `contacto_proveedor`
--
ALTER TABLE `contacto_proveedor`
  MODIFY `id_contacto_proveedor` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `documento`
--
ALTER TABLE `documento`
  MODIFY `id_documento` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `documento_elemento`
--
ALTER TABLE `documento_elemento`
  MODIFY `id_documento_elemento` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `elemento`
--
ALTER TABLE `elemento`
  MODIFY `id_elemento` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `empresa`
--
ALTER TABLE `empresa`
  MODIFY `id_empresa` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `estados_llamada`
--
ALTER TABLE `estados_llamada`
  MODIFY `id_estado` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `estado_elemento`
--
ALTER TABLE `estado_elemento`
  MODIFY `id_estado_elemento` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `estado_presupuesto`
--
ALTER TABLE `estado_presupuesto`
  MODIFY `id_estado_ppto` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `familia`
--
ALTER TABLE `familia`
  MODIFY `id_familia` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `forma_pago`
--
ALTER TABLE `forma_pago`
  MODIFY `id_pago` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `foto_elemento`
--
ALTER TABLE `foto_elemento`
  MODIFY `id_foto_elemento` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `furgoneta`
--
ALTER TABLE `furgoneta`
  MODIFY `id_furgoneta` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `furgoneta_mantenimiento`
--
ALTER TABLE `furgoneta_mantenimiento`
  MODIFY `id_mantenimiento` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `furgoneta_registro_kilometraje`
--
ALTER TABLE `furgoneta_registro_kilometraje`
  MODIFY `id_registro_km` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `grupo_articulo`
--
ALTER TABLE `grupo_articulo`
  MODIFY `id_grupo` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `impuesto`
--
ALTER TABLE `impuesto`
  MODIFY `id_impuesto` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `kit`
--
ALTER TABLE `kit`
  MODIFY `id_kit` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `linea_presupuesto`
--
ALTER TABLE `linea_presupuesto`
  MODIFY `id_linea_ppto` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `llamadas`
--
ALTER TABLE `llamadas`
  MODIFY `id_llamada` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `marca`
--
ALTER TABLE `marca`
  MODIFY `id_marca` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `metodos_contacto`
--
ALTER TABLE `metodos_contacto`
  MODIFY `id_metodo` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `metodo_pago`
--
ALTER TABLE `metodo_pago`
  MODIFY `id_metodo_pago` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `observacion_general`
--
ALTER TABLE `observacion_general`
  MODIFY `id_obs_general` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `presupuesto`
--
ALTER TABLE `presupuesto`
  MODIFY `id_presupuesto` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `presupuesto_version`
--
ALTER TABLE `presupuesto_version`
  MODIFY `id_version_presupuesto` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '? ID único de TABLA (AUTO_INCREMENT). NO confundir con numero_version_presupuesto. \r\n            Ejemplo: Si tienes 3 presupuestos con 2 versiones cada uno, tendrás IDs 1-6,\r\n            pero cada presupuesto tendrá sus propias versiones 1 y 2';

--
-- AUTO_INCREMENT de la tabla `proveedor`
--
ALTER TABLE `proveedor`
  MODIFY `id_proveedor` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id_rol` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tipo_documento`
--
ALTER TABLE `tipo_documento`
  MODIFY `id_tipo_documento` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `unidad_medida`
--
ALTER TABLE `unidad_medida`
  MODIFY `id_unidad` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `visitas_cerradas`
--
ALTER TABLE `visitas_cerradas`
  MODIFY `id_visita_cerrada` int NOT NULL AUTO_INCREMENT;

-- --------------------------------------------------------

--
-- Estructura para la vista `contactos_con_nombre_comunicante`
--
DROP TABLE IF EXISTS `contactos_con_nombre_comunicante`;

CREATE ALGORITHM=UNDEFINED DEFINER=`administrator`@`%` SQL SECURITY DEFINER VIEW `contactos_con_nombre_comunicante`  AS SELECT `c`.`id_contacto` AS `id_contacto`, `c`.`id_llamada` AS `id_llamada`, `c`.`id_metodo` AS `id_metodo`, `c`.`fecha_hora_contacto` AS `fecha_hora_contacto`, `c`.`observaciones` AS `observaciones`, `c`.`id_visita_cerrada` AS `id_visita_cerrada`, (select `vc`.`fecha_visita_cerrada` from `visitas_cerradas` `vc` where (`vc`.`id_visita_cerrada` = `c`.`id_visita_cerrada`)) AS `fecha_visita_cerrada`, `c`.`estado` AS `estado`, (select `l`.`nombre_comunicante` from `llamadas` `l` where (`l`.`id_llamada` = `c`.`id_llamada`)) AS `nombre_comunicante`, (select `l`.`domicilio_instalacion` from `llamadas` `l` where (`l`.`id_llamada` = `c`.`id_llamada`)) AS `domicilio_instalacion`, (select `l`.`telefono_fijo` from `llamadas` `l` where (`l`.`id_llamada` = `c`.`id_llamada`)) AS `telefono_fijo`, (select `l`.`telefono_movil` from `llamadas` `l` where (`l`.`id_llamada` = `c`.`id_llamada`)) AS `telefono_movil`, (select `l`.`email_contacto` from `llamadas` `l` where (`l`.`id_llamada` = `c`.`id_llamada`)) AS `email_contacto`, (select `l`.`fecha_hora_preferida` from `llamadas` `l` where (`l`.`id_llamada` = `c`.`id_llamada`)) AS `fecha_hora_preferida`, (select `l`.`fecha_recepcion` from `llamadas` `l` where (`l`.`id_llamada` = `c`.`id_llamada`)) AS `fecha_recepcion`, (select `l`.`id_comercial_asignado` from `llamadas` `l` where (`l`.`id_llamada` = `c`.`id_llamada`)) AS `id_comercial_asignado`, (select `l`.`estado` from `llamadas` `l` where (`l`.`id_llamada` = `c`.`id_llamada`)) AS `estado_llamada`, (select `l`.`activo_llamada` from `llamadas` `l` where (`l`.`id_llamada` = `c`.`id_llamada`)) AS `activo_llamada`, (select `m`.`nombre` from `metodos_contacto` `m` where (`m`.`id_metodo` = `c`.`id_metodo`)) AS `nombre_metodo`, (select `m`.`imagen_metodo` from `metodos_contacto` `m` where (`m`.`id_metodo` = `c`.`id_metodo`)) AS `imagen_metodo`, (select `e`.`desc_estado` from `estados_llamada` `e` where (`e`.`id_estado` = (select `l`.`estado` from `llamadas` `l` where (`l`.`id_llamada` = `c`.`id_llamada`)))) AS `descripcion_estado_llamada`, (select `com`.`nombre` from `comerciales` `com` where (`com`.`id_comercial` = (select `l`.`id_comercial_asignado` from `llamadas` `l` where (`l`.`id_llamada` = `c`.`id_llamada`)))) AS `nombre_comercial`, ifnull((select group_concat(`a`.`nombre_archivo` separator ',') from `adjunto_llamada` `a` where ((`a`.`id_llamada` = `c`.`id_llamada`) and (`a`.`estado` = 1))),'Sin archivos') AS `archivos_adjuntos`, (select (count(0) > 0) from `contactos` `cont` where (`cont`.`id_llamada` = `c`.`id_llamada`)) AS `tiene_contactos`, ((select `l`.`estado` from `llamadas` `l` where (`l`.`id_llamada` = `c`.`id_llamada`)) = 3) AS `estado_es_3`, (select (count(0) > 0) from `adjunto_llamada` `a` where ((`a`.`id_llamada` = `c`.`id_llamada`) and (`a`.`estado` = 1))) AS `tiene_adjuntos` FROM `contactos` AS `c` ;

-- --------------------------------------------------------

--
-- Estructura para la vista `contacto_cantidad_cliente`
--
DROP TABLE IF EXISTS `contacto_cantidad_cliente`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `contacto_cantidad_cliente`  AS SELECT `c`.`id_cliente` AS `id_cliente`, `c`.`codigo_cliente` AS `codigo_cliente`, `c`.`nombre_cliente` AS `nombre_cliente`, `c`.`direccion_cliente` AS `direccion_cliente`, `c`.`cp_cliente` AS `cp_cliente`, `c`.`poblacion_cliente` AS `poblacion_cliente`, `c`.`provincia_cliente` AS `provincia_cliente`, `c`.`nif_cliente` AS `nif_cliente`, `c`.`telefono_cliente` AS `telefono_cliente`, `c`.`fax_cliente` AS `fax_cliente`, `c`.`web_cliente` AS `web_cliente`, `c`.`email_cliente` AS `email_cliente`, `c`.`nombre_facturacion_cliente` AS `nombre_facturacion_cliente`, `c`.`direccion_facturacion_cliente` AS `direccion_facturacion_cliente`, `c`.`cp_facturacion_cliente` AS `cp_facturacion_cliente`, `c`.`poblacion_facturacion_cliente` AS `poblacion_facturacion_cliente`, `c`.`provincia_facturacion_cliente` AS `provincia_facturacion_cliente`, `c`.`observaciones_cliente` AS `observaciones_cliente`, `c`.`activo_cliente` AS `activo_cliente`, `c`.`created_at_cliente` AS `created_at_cliente`, `c`.`updated_at_cliente` AS `updated_at_cliente`, `c`.`porcentaje_descuento_cliente` AS `porcentaje_descuento_cliente`, `c`.`id_forma_pago_habitual` AS `id_forma_pago_habitual`, `fp`.`codigo_pago` AS `codigo_pago`, `fp`.`nombre_pago` AS `nombre_pago`, `fp`.`descuento_pago` AS `descuento_pago`, `fp`.`porcentaje_anticipo_pago` AS `porcentaje_anticipo_pago`, `fp`.`dias_anticipo_pago` AS `dias_anticipo_pago`, `fp`.`porcentaje_final_pago` AS `porcentaje_final_pago`, `fp`.`dias_final_pago` AS `dias_final_pago`, `fp`.`observaciones_pago` AS `observaciones_pago`, `fp`.`activo_pago` AS `activo_pago`, `mp`.`id_metodo_pago` AS `id_metodo_pago`, `mp`.`codigo_metodo_pago` AS `codigo_metodo_pago`, `mp`.`nombre_metodo_pago` AS `nombre_metodo_pago`, `mp`.`observaciones_metodo_pago` AS `observaciones_metodo_pago`, `mp`.`activo_metodo_pago` AS `activo_metodo_pago`, (select count(`cc`.`id_contacto_cliente`) from `contacto_cliente` `cc` where (`cc`.`id_cliente` = `c`.`id_cliente`)) AS `cantidad_contactos_cliente`, (case when (`fp`.`porcentaje_anticipo_pago` = 100.00) then 'Pago único' when (`fp`.`porcentaje_anticipo_pago` < 100.00) then 'Pago fraccionado' else 'Sin forma de pago' end) AS `tipo_pago_cliente`, (case when (`fp`.`id_pago` is null) then 'Sin forma de pago asignada' when (`fp`.`porcentaje_anticipo_pago` = 100.00) then concat(`mp`.`nombre_metodo_pago`,' - ',`fp`.`nombre_pago`,(case when (`fp`.`descuento_pago` > 0) then concat(' (Dto: ',`fp`.`descuento_pago`,'%)') else '' end)) else concat(`mp`.`nombre_metodo_pago`,' - ',`fp`.`porcentaje_anticipo_pago`,'% + ',`fp`.`porcentaje_final_pago`,'%') end) AS `descripcion_forma_pago_cliente`, concat_ws(', ',`c`.`direccion_cliente`,concat(`c`.`cp_cliente`,' ',`c`.`poblacion_cliente`),`c`.`provincia_cliente`) AS `direccion_completa_cliente`, (case when (`c`.`direccion_facturacion_cliente` is not null) then concat_ws(', ',`c`.`direccion_facturacion_cliente`,concat(`c`.`cp_facturacion_cliente`,' ',`c`.`poblacion_facturacion_cliente`),`c`.`provincia_facturacion_cliente`) else NULL end) AS `direccion_facturacion_completa_cliente`, (case when (`c`.`direccion_facturacion_cliente` is not null) then true else false end) AS `tiene_direccion_facturacion_diferente`, (case when (`c`.`id_forma_pago_habitual` is null) then 'Sin configurar' when (`fp`.`activo_pago` = false) then 'Forma de pago inactiva' when (`mp`.`activo_metodo_pago` = false) then 'Método de pago inactivo' else 'Configurado' end) AS `estado_forma_pago_cliente`, (case when (`c`.`porcentaje_descuento_cliente` = 0.00) then 'Sin descuento' when ((`c`.`porcentaje_descuento_cliente` > 0.00) and (`c`.`porcentaje_descuento_cliente` <= 5.00)) then 'Descuento bajo' when ((`c`.`porcentaje_descuento_cliente` > 5.00) and (`c`.`porcentaje_descuento_cliente` <= 15.00)) then 'Descuento medio' when (`c`.`porcentaje_descuento_cliente` > 15.00) then 'Descuento alto' else 'Sin descuento' end) AS `categoria_descuento_cliente`, (case when (`c`.`porcentaje_descuento_cliente` > 0.00) then true else false end) AS `tiene_descuento_cliente` FROM ((`cliente` `c` left join `forma_pago` `fp` on((`c`.`id_forma_pago_habitual` = `fp`.`id_pago`))) left join `metodo_pago` `mp` on((`fp`.`id_metodo_pago` = `mp`.`id_metodo_pago`))) ;

-- --------------------------------------------------------

--
-- Estructura para la vista `contacto_cantidad_proveedor`
--
DROP TABLE IF EXISTS `contacto_cantidad_proveedor`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `contacto_cantidad_proveedor`  AS SELECT `p`.`id_proveedor` AS `id_proveedor`, `p`.`codigo_proveedor` AS `codigo_proveedor`, `p`.`nombre_proveedor` AS `nombre_proveedor`, `p`.`direccion_proveedor` AS `direccion_proveedor`, `p`.`cp_proveedor` AS `cp_proveedor`, `p`.`poblacion_proveedor` AS `poblacion_proveedor`, `p`.`provincia_proveedor` AS `provincia_proveedor`, `p`.`nif_proveedor` AS `nif_proveedor`, `p`.`telefono_proveedor` AS `telefono_proveedor`, `p`.`fax_proveedor` AS `fax_proveedor`, `p`.`web_proveedor` AS `web_proveedor`, `p`.`email_proveedor` AS `email_proveedor`, `p`.`persona_contacto_proveedor` AS `persona_contacto_proveedor`, `p`.`direccion_sat_proveedor` AS `direccion_sat_proveedor`, `p`.`cp_sat_proveedor` AS `cp_sat_proveedor`, `p`.`poblacion_sat_proveedor` AS `poblacion_sat_proveedor`, `p`.`provincia_sat_proveedor` AS `provincia_sat_proveedor`, `p`.`telefono_sat_proveedor` AS `telefono_sat_proveedor`, `p`.`fax_sat_proveedor` AS `fax_sat_proveedor`, `p`.`email_sat_proveedor` AS `email_sat_proveedor`, `p`.`observaciones_proveedor` AS `observaciones_proveedor`, `p`.`activo_proveedor` AS `activo_proveedor`, `p`.`created_at_proveedor` AS `created_at_proveedor`, `p`.`updated_at_proveedor` AS `updated_at_proveedor`, `p`.`id_forma_pago_habitual` AS `id_forma_pago_habitual`, `fp`.`codigo_pago` AS `codigo_pago`, `fp`.`nombre_pago` AS `nombre_pago`, `fp`.`descuento_pago` AS `descuento_pago`, `fp`.`porcentaje_anticipo_pago` AS `porcentaje_anticipo_pago`, `fp`.`dias_anticipo_pago` AS `dias_anticipo_pago`, `fp`.`porcentaje_final_pago` AS `porcentaje_final_pago`, `fp`.`dias_final_pago` AS `dias_final_pago`, `fp`.`observaciones_pago` AS `observaciones_pago`, `fp`.`activo_pago` AS `activo_pago`, `mp`.`id_metodo_pago` AS `id_metodo_pago`, `mp`.`codigo_metodo_pago` AS `codigo_metodo_pago`, `mp`.`nombre_metodo_pago` AS `nombre_metodo_pago`, `mp`.`observaciones_metodo_pago` AS `observaciones_metodo_pago`, `mp`.`activo_metodo_pago` AS `activo_metodo_pago`, (select count(`cp`.`id_contacto_proveedor`) from `contacto_proveedor` `cp` where (`cp`.`id_proveedor` = `p`.`id_proveedor`)) AS `cantidad_contacto_proveedor`, (case when (`fp`.`porcentaje_anticipo_pago` = 100.00) then 'Pago único' when (`fp`.`porcentaje_anticipo_pago` < 100.00) then 'Pago fraccionado' else 'Sin forma de pago' end) AS `tipo_pago_proveedor`, (case when (`fp`.`id_pago` is null) then 'Sin forma de pago asignada' when (`fp`.`porcentaje_anticipo_pago` = 100.00) then concat(`mp`.`nombre_metodo_pago`,' - ',`fp`.`nombre_pago`,(case when (`fp`.`descuento_pago` > 0) then concat(' (Dto: ',`fp`.`descuento_pago`,'%)') else '' end)) else concat(`mp`.`nombre_metodo_pago`,' - ',`fp`.`porcentaje_anticipo_pago`,'% + ',`fp`.`porcentaje_final_pago`,'%') end) AS `descripcion_forma_pago_proveedor`, concat_ws(', ',`p`.`direccion_proveedor`,concat(`p`.`cp_proveedor`,' ',`p`.`poblacion_proveedor`),`p`.`provincia_proveedor`) AS `direccion_completa_proveedor`, (case when (`p`.`direccion_sat_proveedor` is not null) then concat_ws(', ',`p`.`direccion_sat_proveedor`,concat(`p`.`cp_sat_proveedor`,' ',`p`.`poblacion_sat_proveedor`),`p`.`provincia_sat_proveedor`) else NULL end) AS `direccion_sat_completa_proveedor`, (case when (`p`.`direccion_sat_proveedor` is not null) then true else false end) AS `tiene_direccion_sat`, (case when (`p`.`id_forma_pago_habitual` is null) then 'Sin configurar' when (`fp`.`activo_pago` = false) then 'Forma de pago inactiva' when (`mp`.`activo_metodo_pago` = false) then 'Método de pago inactivo' else 'Configurado' end) AS `estado_forma_pago_proveedor` FROM ((`proveedor` `p` left join `forma_pago` `fp` on((`p`.`id_forma_pago_habitual` = `fp`.`id_pago`))) left join `metodo_pago` `mp` on((`fp`.`id_metodo_pago` = `mp`.`id_metodo_pago`))) ;

-- --------------------------------------------------------

--
-- Estructura para la vista `familia_unidad_media`
--
DROP TABLE IF EXISTS `familia_unidad_media`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `familia_unidad_media`  AS SELECT `f`.`id_familia` AS `id_familia`, `f`.`id_grupo` AS `id_grupo`, `f`.`codigo_familia` AS `codigo_familia`, `f`.`nombre_familia` AS `nombre_familia`, `f`.`name_familia` AS `name_familia`, `f`.`descr_familia` AS `descr_familia`, `f`.`imagen_familia` AS `imagen_familia`, `f`.`activo_familia` AS `activo_familia`, `f`.`coeficiente_familia` AS `coeficiente_familia`, `f`.`created_at_familia` AS `created_at_familia`, `f`.`updated_at_familia` AS `updated_at_familia`, `f`.`id_unidad_familia` AS `id_unidad_familia`, `f`.`observaciones_presupuesto_familia` AS `observaciones_presupuesto_familia`, `f`.`orden_obs_familia` AS `orden_obs_familia`, `u`.`nombre_unidad` AS `nombre_unidad`, `u`.`descr_unidad` AS `descr_unidad`, `u`.`simbolo_unidad` AS `simbolo_unidad`, `u`.`activo_unidad` AS `activo_unidad`, `g`.`codigo_grupo` AS `codigo_grupo`, `g`.`nombre_grupo` AS `nombre_grupo`, `g`.`descripcion_grupo` AS `descripcion_grupo` FROM ((`familia` `f` left join `unidad_medida` `u` on((`f`.`id_unidad_familia` = `u`.`id_unidad`))) left join `grupo_articulo` `g` on((`f`.`id_grupo` = `g`.`id_grupo`))) ;

-- --------------------------------------------------------

--
-- Estructura para la vista `llamadas_con_comerciales_y_metodos`
--
DROP TABLE IF EXISTS `llamadas_con_comerciales_y_metodos`;

CREATE ALGORITHM=UNDEFINED DEFINER=`administrator`@`%` SQL SECURITY DEFINER VIEW `llamadas_con_comerciales_y_metodos`  AS SELECT `l`.`id_llamada` AS `id_llamada`, `l`.`id_metodo` AS `id_metodo`, `l`.`nombre_comunicante` AS `nombre_comunicante`, `l`.`domicilio_instalacion` AS `domicilio_instalacion`, `l`.`telefono_fijo` AS `telefono_fijo`, `l`.`telefono_movil` AS `telefono_movil`, `l`.`email_contacto` AS `email_contacto`, `l`.`fecha_hora_preferida` AS `fecha_hora_preferida`, `l`.`observaciones` AS `observaciones`, `l`.`id_comercial_asignado` AS `id_comercial_asignado`, `l`.`estado` AS `estado`, `l`.`fecha_recepcion` AS `fecha_recepcion`, `l`.`activo_llamada` AS `activo_llamada`, (select `c`.`nombre` from `comerciales` `c` where (`c`.`id_comercial` = `l`.`id_comercial_asignado`)) AS `nombre_comercial`, (select `m`.`nombre` from `metodos_contacto` `m` where (`m`.`id_metodo` = `l`.`id_metodo`)) AS `nombre_metodo`, (select `m`.`imagen_metodo` from `metodos_contacto` `m` where (`m`.`id_metodo` = `l`.`id_metodo`)) AS `imagen_metodo`, (select `e`.`desc_estado` from `estados_llamada` `e` where (`e`.`id_estado` = `l`.`estado`)) AS `descripcion_estado`, ifnull((select group_concat(`a`.`nombre_archivo` separator ',') from `adjunto_llamada` `a` where ((`a`.`id_llamada` = `l`.`id_llamada`) and (`a`.`estado` = 1))),'Sin archivos') AS `archivos_adjuntos`, (select (count(0) > 0) from `contactos` `c` where (`c`.`id_llamada` = `l`.`id_llamada`)) AS `tiene_contactos`, (`l`.`estado` = 3) AS `estado_es_3`, (select (count(0) > 0) from `adjunto_llamada` `a` where ((`a`.`id_llamada` = `l`.`id_llamada`) and (`a`.`estado` = 1))) AS `tiene_adjuntos`, (select `c`.`fecha_hora_contacto` from `contactos` `c` where (`c`.`id_llamada` = `l`.`id_llamada`) order by `c`.`fecha_hora_contacto` limit 1) AS `fecha_primer_contacto` FROM `llamadas` AS `l` ;

-- --------------------------------------------------------

--
-- Estructura para la vista `metodo_forma_pago`
--
DROP TABLE IF EXISTS `metodo_forma_pago`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `metodo_forma_pago`  AS SELECT `fp`.`codigo_pago` AS `codigo_pago`, `fp`.`nombre_pago` AS `nombre_pago`, `mp`.`nombre_metodo_pago` AS `nombre_metodo_pago`, `fp`.`porcentaje_anticipo_pago` AS `porcentaje_anticipo_pago`, `fp`.`dias_anticipo_pago` AS `dias_anticipo_pago`, `fp`.`porcentaje_final_pago` AS `porcentaje_final_pago`, `fp`.`dias_final_pago` AS `dias_final_pago`, `fp`.`descuento_pago` AS `descuento_pago`, (case when (`fp`.`porcentaje_anticipo_pago` = 100.00) then 'Pago único' else 'Pago fraccionado' end) AS `tipo_pago` FROM (`forma_pago` `fp` join `metodo_pago` `mp` on((`fp`.`id_metodo_pago` = `mp`.`id_metodo_pago`))) WHERE (`fp`.`activo_pago` = true) ORDER BY (case when (`fp`.`porcentaje_anticipo_pago` = 100.00) then 1 else 2 end) ASC, `fp`.`nombre_pago` ASC ;

-- --------------------------------------------------------

--
-- Estructura para la vista `usuarios_con_rol`
--
DROP TABLE IF EXISTS `usuarios_con_rol`;

CREATE ALGORITHM=UNDEFINED DEFINER=`administrator`@`%` SQL SECURITY DEFINER VIEW `usuarios_con_rol`  AS SELECT `usuarios`.`id_usuario` AS `id_usuario`, `usuarios`.`nombre` AS `nombre`, `usuarios`.`email` AS `email`, `usuarios`.`contrasena` AS `contrasena`, `usuarios`.`fecha_crea` AS `fecha_crea`, `usuarios`.`est` AS `est`, `usuarios`.`id_rol` AS `id_rol`, (select `roles`.`nombre_rol` from `roles` where (`roles`.`id_rol` = `usuarios`.`id_rol`)) AS `nombre_rol` FROM `usuarios` ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vacaciones_con_nombre`
--
DROP TABLE IF EXISTS `vacaciones_con_nombre`;

CREATE ALGORITHM=UNDEFINED DEFINER=`administrator`@`%` SQL SECURITY DEFINER VIEW `vacaciones_con_nombre`  AS SELECT `com_vacaciones`.`id_vacacion` AS `id_vacacion`, `com_vacaciones`.`id_comercial` AS `id_comercial`, `com_vacaciones`.`fecha_inicio` AS `fecha_inicio`, `com_vacaciones`.`fecha_fin` AS `fecha_fin`, `com_vacaciones`.`descripcion` AS `descripcion`, `com_vacaciones`.`activo_vacacion` AS `activo_vacacion`, (select concat(`comerciales`.`nombre`,' ',`comerciales`.`apellidos`) from `comerciales` where (`comerciales`.`id_comercial` = `com_vacaciones`.`id_comercial`)) AS `nombre_comercial` FROM `com_vacaciones` ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_adjuntos_con_comunicante`
--
DROP TABLE IF EXISTS `vista_adjuntos_con_comunicante`;

CREATE ALGORITHM=UNDEFINED DEFINER=`administrator`@`%` SQL SECURITY DEFINER VIEW `vista_adjuntos_con_comunicante`  AS SELECT `adjunto_llamada`.`id_adjunto` AS `id_adjunto`, `adjunto_llamada`.`id_llamada` AS `id_llamada`, `adjunto_llamada`.`nombre_archivo` AS `nombre_archivo`, `adjunto_llamada`.`tipo` AS `tipo`, `adjunto_llamada`.`fecha_subida` AS `fecha_subida`, `adjunto_llamada`.`estado` AS `estado`, (select `llamadas`.`nombre_comunicante` from `llamadas` where (`llamadas`.`id_llamada` = `adjunto_llamada`.`id_llamada`)) AS `nombre_comunicante` FROM `adjunto_llamada` ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_articulo_completa`
--
DROP TABLE IF EXISTS `vista_articulo_completa`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `vista_articulo_completa`  AS SELECT `a`.`id_articulo` AS `id_articulo`, `a`.`id_familia` AS `id_familia`, `a`.`id_unidad` AS `id_unidad`, `a`.`id_impuesto` AS `id_impuesto`, `a`.`codigo_articulo` AS `codigo_articulo`, `a`.`nombre_articulo` AS `nombre_articulo`, `a`.`name_articulo` AS `name_articulo`, `a`.`imagen_articulo` AS `imagen_articulo`, `a`.`precio_alquiler_articulo` AS `precio_alquiler_articulo`, `a`.`coeficiente_articulo` AS `coeficiente_articulo`, `a`.`es_kit_articulo` AS `es_kit_articulo`, `a`.`control_total_articulo` AS `control_total_articulo`, `a`.`no_facturar_articulo` AS `no_facturar_articulo`, `a`.`notas_presupuesto_articulo` AS `notas_presupuesto_articulo`, `a`.`notes_budget_articulo` AS `notes_budget_articulo`, `a`.`orden_obs_articulo` AS `orden_obs_articulo`, `a`.`observaciones_articulo` AS `observaciones_articulo`, `a`.`permitir_descuentos_articulo` AS `permitir_descuentos_articulo`, `a`.`activo_articulo` AS `activo_articulo`, `a`.`created_at_articulo` AS `created_at_articulo`, `a`.`updated_at_articulo` AS `updated_at_articulo`, `f`.`id_grupo` AS `id_grupo`, `f`.`codigo_familia` AS `codigo_familia`, `f`.`nombre_familia` AS `nombre_familia`, `f`.`name_familia` AS `name_familia`, `f`.`descr_familia` AS `descr_familia`, `f`.`imagen_familia` AS `imagen_familia`, `f`.`coeficiente_familia` AS `coeficiente_familia`, `f`.`observaciones_presupuesto_familia` AS `observaciones_presupuesto_familia`, `f`.`observations_budget_familia` AS `observations_budget_familia`, `f`.`orden_obs_familia` AS `orden_obs_familia`, `f`.`permite_descuento_familia` AS `permite_descuento_familia`, `f`.`activo_familia` AS `activo_familia_relacionada`, `imp`.`tipo_impuesto` AS `tipo_impuesto`, `imp`.`tasa_impuesto` AS `tasa_impuesto`, `imp`.`descr_impuesto` AS `descr_impuesto`, `imp`.`activo_impuesto` AS `activo_impuesto_relacionado`, `u`.`nombre_unidad` AS `nombre_unidad`, `u`.`name_unidad` AS `name_unidad`, `u`.`descr_unidad` AS `descr_unidad`, `u`.`simbolo_unidad` AS `simbolo_unidad`, `u`.`activo_unidad` AS `activo_unidad_relacionada` FROM (((`articulo` `a` join `familia` `f` on((`a`.`id_familia` = `f`.`id_familia`))) left join `impuesto` `imp` on((`a`.`id_impuesto` = `imp`.`id_impuesto`))) left join `unidad_medida` `u` on((`a`.`id_unidad` = `u`.`id_unidad`))) ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_cliente_completa`
--
DROP TABLE IF EXISTS `vista_cliente_completa`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `vista_cliente_completa`  AS SELECT `c`.`id_cliente` AS `id_cliente`, `c`.`codigo_cliente` AS `codigo_cliente`, `c`.`nombre_cliente` AS `nombre_cliente`, `c`.`direccion_cliente` AS `direccion_cliente`, `c`.`cp_cliente` AS `cp_cliente`, `c`.`poblacion_cliente` AS `poblacion_cliente`, `c`.`provincia_cliente` AS `provincia_cliente`, `c`.`nif_cliente` AS `nif_cliente`, `c`.`telefono_cliente` AS `telefono_cliente`, `c`.`fax_cliente` AS `fax_cliente`, `c`.`web_cliente` AS `web_cliente`, `c`.`email_cliente` AS `email_cliente`, `c`.`nombre_facturacion_cliente` AS `nombre_facturacion_cliente`, `c`.`direccion_facturacion_cliente` AS `direccion_facturacion_cliente`, `c`.`cp_facturacion_cliente` AS `cp_facturacion_cliente`, `c`.`poblacion_facturacion_cliente` AS `poblacion_facturacion_cliente`, `c`.`provincia_facturacion_cliente` AS `provincia_facturacion_cliente`, `c`.`observaciones_cliente` AS `observaciones_cliente`, `c`.`activo_cliente` AS `activo_cliente`, `c`.`created_at_cliente` AS `created_at_cliente`, `c`.`updated_at_cliente` AS `updated_at_cliente`, `c`.`id_forma_pago_habitual` AS `id_forma_pago_habitual`, `fp`.`codigo_pago` AS `codigo_pago`, `fp`.`nombre_pago` AS `nombre_pago`, `fp`.`descuento_pago` AS `descuento_pago`, `fp`.`porcentaje_anticipo_pago` AS `porcentaje_anticipo_pago`, `fp`.`dias_anticipo_pago` AS `dias_anticipo_pago`, `fp`.`porcentaje_final_pago` AS `porcentaje_final_pago`, `fp`.`dias_final_pago` AS `dias_final_pago`, `fp`.`observaciones_pago` AS `observaciones_pago`, `fp`.`activo_pago` AS `activo_pago`, `mp`.`id_metodo_pago` AS `id_metodo_pago`, `mp`.`codigo_metodo_pago` AS `codigo_metodo_pago`, `mp`.`nombre_metodo_pago` AS `nombre_metodo_pago`, `mp`.`observaciones_metodo_pago` AS `observaciones_metodo_pago`, `mp`.`activo_metodo_pago` AS `activo_metodo_pago`, (case when (`fp`.`porcentaje_anticipo_pago` = 100.00) then 'Pago único' when (`fp`.`porcentaje_anticipo_pago` < 100.00) then 'Pago fraccionado' else 'Sin forma de pago' end) AS `tipo_pago_cliente`, (case when (`fp`.`id_pago` is null) then 'Sin forma de pago asignada' when (`fp`.`porcentaje_anticipo_pago` = 100.00) then concat(`mp`.`nombre_metodo_pago`,' - ',`fp`.`nombre_pago`,(case when (`fp`.`descuento_pago` > 0) then concat(' (Dto: ',`fp`.`descuento_pago`,'%)') else '' end)) else concat(`mp`.`nombre_metodo_pago`,' - ',`fp`.`porcentaje_anticipo_pago`,'% + ',`fp`.`porcentaje_final_pago`,'%') end) AS `descripcion_forma_pago_cliente`, concat_ws(', ',`c`.`direccion_cliente`,concat(`c`.`cp_cliente`,' ',`c`.`poblacion_cliente`),`c`.`provincia_cliente`) AS `direccion_completa_cliente`, (case when (`c`.`direccion_facturacion_cliente` is not null) then concat_ws(', ',`c`.`direccion_facturacion_cliente`,concat(`c`.`cp_facturacion_cliente`,' ',`c`.`poblacion_facturacion_cliente`),`c`.`provincia_facturacion_cliente`) else NULL end) AS `direccion_facturacion_completa_cliente`, (case when (`c`.`direccion_facturacion_cliente` is not null) then true else false end) AS `tiene_direccion_facturacion_diferente`, (case when (`c`.`id_forma_pago_habitual` is null) then 'Sin configurar' when (`fp`.`activo_pago` = false) then 'Forma de pago inactiva' when (`mp`.`activo_metodo_pago` = false) then 'Método de pago inactivo' else 'Configurado' end) AS `estado_forma_pago_cliente` FROM ((`cliente` `c` left join `forma_pago` `fp` on((`c`.`id_forma_pago_habitual` = `fp`.`id_pago`))) left join `metodo_pago` `mp` on((`fp`.`id_metodo_pago` = `mp`.`id_metodo_pago`))) ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_cliente_ubicaciones`
--
DROP TABLE IF EXISTS `vista_cliente_ubicaciones`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `vista_cliente_ubicaciones`  AS SELECT `c`.`id_cliente` AS `id_cliente`, `c`.`codigo_cliente` AS `codigo_cliente`, `c`.`nombre_cliente` AS `nombre_cliente`, `c`.`nif_cliente` AS `nif_cliente`, `c`.`telefono_cliente` AS `telefono_cliente`, `c`.`email_cliente` AS `email_cliente`, `c`.`activo_cliente` AS `activo_cliente`, `u`.`id_ubicacion` AS `id_ubicacion`, `u`.`nombre_ubicacion` AS `nombre_ubicacion`, `u`.`direccion_ubicacion` AS `direccion_ubicacion`, `u`.`codigo_postal_ubicacion` AS `codigo_postal_ubicacion`, `u`.`poblacion_ubicacion` AS `poblacion_ubicacion`, `u`.`provincia_ubicacion` AS `provincia_ubicacion`, `u`.`pais_ubicacion` AS `pais_ubicacion`, `u`.`persona_contacto_ubicacion` AS `persona_contacto_ubicacion`, `u`.`telefono_contacto_ubicacion` AS `telefono_contacto_ubicacion`, `u`.`email_contacto_ubicacion` AS `email_contacto_ubicacion`, `u`.`observaciones_ubicacion` AS `observaciones_ubicacion`, `u`.`es_principal_ubicacion` AS `es_principal_ubicacion`, `u`.`activo_ubicacion` AS `activo_ubicacion`, concat_ws(', ',`c`.`direccion_cliente`,concat(`c`.`cp_cliente`,' ',`c`.`poblacion_cliente`),`c`.`provincia_cliente`) AS `direccion_completa_cliente`, concat_ws(', ',`u`.`direccion_ubicacion`,concat(`u`.`codigo_postal_ubicacion`,' ',`u`.`poblacion_ubicacion`),`u`.`provincia_ubicacion`,(case when (`u`.`pais_ubicacion` <> 'España') then `u`.`pais_ubicacion` else NULL end)) AS `direccion_completa_ubicacion`, (case when (`u`.`es_principal_ubicacion` = true) then 'Principal' else 'Secundaria' end) AS `tipo_ubicacion`, (case when (`c`.`activo_cliente` = false) then 'Cliente inactivo' when (`u`.`activo_ubicacion` = false) then 'Ubicación inactiva' else 'Activa' end) AS `estado_completo`, (case when (`u`.`persona_contacto_ubicacion` is not null) then true else false end) AS `tiene_contacto_propio`, (select count(0) from `cliente_ubicacion` `cu` where ((`cu`.`id_cliente` = `c`.`id_cliente`) and (`cu`.`activo_ubicacion` = true))) AS `total_ubicaciones_cliente` FROM (`cliente` `c` join `cliente_ubicacion` `u` on((`c`.`id_cliente` = `u`.`id_cliente`))) ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_costos_furgoneta`
--
DROP TABLE IF EXISTS `vista_costos_furgoneta`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `vista_costos_furgoneta`  AS SELECT `f`.`id_furgoneta` AS `id_furgoneta`, `f`.`matricula_furgoneta` AS `matricula_furgoneta`, `f`.`marca_furgoneta` AS `marca_furgoneta`, `f`.`modelo_furgoneta` AS `modelo_furgoneta`, `f`.`anio_furgoneta` AS `anio_furgoneta`, coalesce(sum(`m`.`costo_mantenimiento`),0) AS `costo_total`, coalesce(sum((case when (year(`m`.`fecha_mantenimiento`) = year(curdate())) then `m`.`costo_mantenimiento` else 0 end)),0) AS `costo_anio_actual`, coalesce(sum((case when (`m`.`tipo_mantenimiento` = 'revision') then `m`.`costo_mantenimiento` else 0 end)),0) AS `costo_revisiones`, coalesce(sum((case when (`m`.`tipo_mantenimiento` = 'reparacion') then `m`.`costo_mantenimiento` else 0 end)),0) AS `costo_reparaciones`, coalesce(sum((case when (`m`.`tipo_mantenimiento` = 'itv') then `m`.`costo_mantenimiento` else 0 end)),0) AS `costo_itv`, coalesce(sum((case when (`m`.`tipo_mantenimiento` = 'neumaticos') then `m`.`costo_mantenimiento` else 0 end)),0) AS `costo_neumaticos`, count(`m`.`id_mantenimiento`) AS `total_mantenimientos`, coalesce(avg(`m`.`costo_mantenimiento`),0) AS `costo_promedio`, max(`m`.`fecha_mantenimiento`) AS `fecha_ultimo_mantenimiento`, (select max(`furgoneta_registro_kilometraje`.`kilometraje_registrado_km`) from `furgoneta_registro_kilometraje` where (`furgoneta_registro_kilometraje`.`id_furgoneta` = `f`.`id_furgoneta`)) AS `kilometraje_actual`, (case when ((select max(`furgoneta_registro_kilometraje`.`kilometraje_registrado_km`) from `furgoneta_registro_kilometraje` where (`furgoneta_registro_kilometraje`.`id_furgoneta` = `f`.`id_furgoneta`)) > 0) then (coalesce(sum(`m`.`costo_mantenimiento`),0) / (select max(`furgoneta_registro_kilometraje`.`kilometraje_registrado_km`) from `furgoneta_registro_kilometraje` where (`furgoneta_registro_kilometraje`.`id_furgoneta` = `f`.`id_furgoneta`))) else NULL end) AS `costo_por_km` FROM (`furgoneta` `f` left join `furgoneta_mantenimiento` `m` on(((`f`.`id_furgoneta` = `m`.`id_furgoneta`) and (`m`.`activo_mantenimiento` = 1)))) GROUP BY `f`.`id_furgoneta`, `f`.`matricula_furgoneta`, `f`.`marca_furgoneta`, `f`.`modelo_furgoneta`, `f`.`anio_furgoneta` ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_elementos_completa`
--
DROP TABLE IF EXISTS `vista_elementos_completa`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `vista_elementos_completa`  AS SELECT `e`.`id_elemento` AS `id_elemento`, `e`.`codigo_elemento` AS `codigo_elemento`, `e`.`codigo_barras_elemento` AS `codigo_barras_elemento`, `e`.`descripcion_elemento` AS `descripcion_elemento`, `e`.`numero_serie_elemento` AS `numero_serie_elemento`, `e`.`modelo_elemento` AS `modelo_elemento`, `e`.`nave_elemento` AS `nave_elemento`, `e`.`pasillo_columna_elemento` AS `pasillo_columna_elemento`, `e`.`altura_elemento` AS `altura_elemento`, concat_ws(' | ',coalesce(`e`.`nave_elemento`,''),coalesce(`e`.`pasillo_columna_elemento`,''),coalesce(`e`.`altura_elemento`,'')) AS `ubicacion_completa_elemento`, `e`.`fecha_compra_elemento` AS `fecha_compra_elemento`, `e`.`precio_compra_elemento` AS `precio_compra_elemento`, `e`.`fecha_alta_elemento` AS `fecha_alta_elemento`, `e`.`fecha_fin_garantia_elemento` AS `fecha_fin_garantia_elemento`, `e`.`proximo_mantenimiento_elemento` AS `proximo_mantenimiento_elemento`, `e`.`observaciones_elemento` AS `observaciones_elemento`, `e`.`es_propio_elemento` AS `es_propio_elemento`, `e`.`id_proveedor_compra_elemento` AS `id_proveedor_compra_elemento`, `e`.`id_proveedor_alquiler_elemento` AS `id_proveedor_alquiler_elemento`, `e`.`precio_dia_alquiler_elemento` AS `precio_dia_alquiler_elemento`, `e`.`id_forma_pago_alquiler_elemento` AS `id_forma_pago_alquiler_elemento`, `e`.`observaciones_alquiler_elemento` AS `observaciones_alquiler_elemento`, `prov_compra`.`codigo_proveedor` AS `codigo_proveedor_compra`, `prov_compra`.`nombre_proveedor` AS `nombre_proveedor_compra`, `prov_compra`.`telefono_proveedor` AS `telefono_proveedor_compra`, `prov_compra`.`email_proveedor` AS `email_proveedor_compra`, `prov_compra`.`nif_proveedor` AS `nif_proveedor_compra`, `prov_alquiler`.`codigo_proveedor` AS `codigo_proveedor_alquiler`, `prov_alquiler`.`nombre_proveedor` AS `nombre_proveedor_alquiler`, `prov_alquiler`.`telefono_proveedor` AS `telefono_proveedor_alquiler`, `prov_alquiler`.`email_proveedor` AS `email_proveedor_alquiler`, `prov_alquiler`.`nif_proveedor` AS `nif_proveedor_alquiler`, `prov_alquiler`.`persona_contacto_proveedor` AS `persona_contacto_proveedor_alquiler`, `fp_alquiler`.`codigo_pago` AS `codigo_forma_pago_alquiler`, `fp_alquiler`.`nombre_pago` AS `nombre_forma_pago_alquiler`, `fp_alquiler`.`porcentaje_anticipo_pago` AS `porcentaje_anticipo_alquiler`, `fp_alquiler`.`dias_anticipo_pago` AS `dias_anticipo_alquiler`, `fp_alquiler`.`porcentaje_final_pago` AS `porcentaje_final_alquiler`, `fp_alquiler`.`dias_final_pago` AS `dias_final_alquiler`, `fp_alquiler`.`descuento_pago` AS `descuento_forma_pago_alquiler`, `mp_alquiler`.`codigo_metodo_pago` AS `codigo_metodo_pago_alquiler`, `mp_alquiler`.`nombre_metodo_pago` AS `nombre_metodo_pago_alquiler`, `a`.`id_articulo` AS `id_articulo`, `a`.`codigo_articulo` AS `codigo_articulo`, `a`.`nombre_articulo` AS `nombre_articulo`, `a`.`name_articulo` AS `name_articulo`, `a`.`precio_alquiler_articulo` AS `precio_alquiler_articulo`, `f`.`id_familia` AS `id_familia`, `f`.`codigo_familia` AS `codigo_familia`, `f`.`nombre_familia` AS `nombre_familia`, `f`.`name_familia` AS `name_familia`, `g`.`id_grupo` AS `id_grupo`, `g`.`codigo_grupo` AS `codigo_grupo`, `g`.`nombre_grupo` AS `nombre_grupo`, `m`.`id_marca` AS `id_marca`, `m`.`codigo_marca` AS `codigo_marca`, `m`.`nombre_marca` AS `nombre_marca`, `est`.`id_estado_elemento` AS `id_estado_elemento`, `est`.`codigo_estado_elemento` AS `codigo_estado_elemento`, `est`.`descripcion_estado_elemento` AS `descripcion_estado_elemento`, `est`.`color_estado_elemento` AS `color_estado_elemento`, `est`.`permite_alquiler_estado_elemento` AS `permite_alquiler_estado_elemento`, `e`.`activo_elemento` AS `activo_elemento`, `e`.`created_at_elemento` AS `created_at_elemento`, `e`.`updated_at_elemento` AS `updated_at_elemento`, concat_ws(' > ',coalesce(`g`.`nombre_grupo`,'Sin grupo'),`f`.`nombre_familia`,`a`.`nombre_articulo`,`e`.`descripcion_elemento`) AS `jerarquia_completa_elemento`, (case when (`e`.`es_propio_elemento` = true) then 'PROPIO' else 'ALQUILADO A PROVEEDOR' end) AS `tipo_propiedad_elemento`, (case when (`e`.`es_propio_elemento` = true) then `prov_compra`.`nombre_proveedor` else `prov_alquiler`.`nombre_proveedor` end) AS `proveedor_principal_elemento`, (case when (`e`.`es_propio_elemento` = true) then 'N/A - Equipo propio' when (`e`.`id_proveedor_alquiler_elemento` is null) then 'Sin proveedor asignado' when ((`e`.`precio_dia_alquiler_elemento` is null) or (`e`.`precio_dia_alquiler_elemento` = 0)) then 'Proveedor asignado - Falta precio' when (`e`.`id_forma_pago_alquiler_elemento` is null) then 'Proveedor y precio OK - Falta forma de pago' else 'Completamente configurado' end) AS `estado_configuracion_alquiler`, (case when ((`e`.`es_propio_elemento` = false) and (`fp_alquiler`.`id_pago` is not null)) then (case when (`fp_alquiler`.`porcentaje_anticipo_pago` = 100.00) then concat(`mp_alquiler`.`nombre_metodo_pago`,' - ',`fp_alquiler`.`nombre_pago`,(case when (`fp_alquiler`.`descuento_pago` > 0) then concat(' (Dto: ',`fp_alquiler`.`descuento_pago`,'%)') else '' end)) else concat(`mp_alquiler`.`nombre_metodo_pago`,' - ',`fp_alquiler`.`porcentaje_anticipo_pago`,'% + ',`fp_alquiler`.`porcentaje_final_pago`,'%') end) else NULL end) AS `descripcion_forma_pago_alquiler`, (case when ((`e`.`es_propio_elemento` = false) and (`e`.`precio_dia_alquiler_elemento` is not null)) then round((`e`.`precio_dia_alquiler_elemento` * 30),2) else NULL end) AS `costo_mensual_estimado_alquiler`, (to_days(curdate()) - to_days(`e`.`fecha_alta_elemento`)) AS `dias_en_servicio_elemento`, round(((to_days(curdate()) - to_days(`e`.`fecha_alta_elemento`)) / 365.25),2) AS `anios_en_servicio_elemento` FROM (((((((((`elemento` `e` join `articulo` `a` on((`e`.`id_articulo_elemento` = `a`.`id_articulo`))) join `familia` `f` on((`a`.`id_familia` = `f`.`id_familia`))) left join `grupo_articulo` `g` on((`f`.`id_grupo` = `g`.`id_grupo`))) left join `marca` `m` on((`e`.`id_marca_elemento` = `m`.`id_marca`))) join `estado_elemento` `est` on((`e`.`id_estado_elemento` = `est`.`id_estado_elemento`))) left join `proveedor` `prov_compra` on((`e`.`id_proveedor_compra_elemento` = `prov_compra`.`id_proveedor`))) left join `proveedor` `prov_alquiler` on((`e`.`id_proveedor_alquiler_elemento` = `prov_alquiler`.`id_proveedor`))) left join `forma_pago` `fp_alquiler` on((`e`.`id_forma_pago_alquiler_elemento` = `fp_alquiler`.`id_pago`))) left join `metodo_pago` `mp_alquiler` on((`fp_alquiler`.`id_metodo_pago` = `mp_alquiler`.`id_metodo_pago`))) ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_furgoneta_completa`
--
DROP TABLE IF EXISTS `vista_furgoneta_completa`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `vista_furgoneta_completa`  AS SELECT `f`.`id_furgoneta` AS `id_furgoneta`, `f`.`matricula_furgoneta` AS `matricula_furgoneta`, `f`.`marca_furgoneta` AS `marca_furgoneta`, `f`.`modelo_furgoneta` AS `modelo_furgoneta`, `f`.`anio_furgoneta` AS `anio_furgoneta`, `f`.`numero_bastidor_furgoneta` AS `numero_bastidor_furgoneta`, `f`.`kilometros_entre_revisiones_furgoneta` AS `kilometros_entre_revisiones_furgoneta`, `f`.`fecha_proxima_itv_furgoneta` AS `fecha_proxima_itv_furgoneta`, `f`.`fecha_vencimiento_seguro_furgoneta` AS `fecha_vencimiento_seguro_furgoneta`, `f`.`compania_seguro_furgoneta` AS `compania_seguro_furgoneta`, `f`.`numero_poliza_seguro_furgoneta` AS `numero_poliza_seguro_furgoneta`, `f`.`capacidad_carga_kg_furgoneta` AS `capacidad_carga_kg_furgoneta`, `f`.`capacidad_carga_m3_furgoneta` AS `capacidad_carga_m3_furgoneta`, `f`.`tipo_combustible_furgoneta` AS `tipo_combustible_furgoneta`, `f`.`consumo_medio_furgoneta` AS `consumo_medio_furgoneta`, `f`.`taller_habitual_furgoneta` AS `taller_habitual_furgoneta`, `f`.`telefono_taller_furgoneta` AS `telefono_taller_furgoneta`, `f`.`estado_furgoneta` AS `estado_furgoneta`, `f`.`observaciones_furgoneta` AS `observaciones_furgoneta`, `f`.`activo_furgoneta` AS `activo_furgoneta`, `f`.`created_at_furgoneta` AS `created_at_furgoneta`, `f`.`updated_at_furgoneta` AS `updated_at_furgoneta`, (select max(`furgoneta_registro_kilometraje`.`kilometraje_registrado_km`) from `furgoneta_registro_kilometraje` where (`furgoneta_registro_kilometraje`.`id_furgoneta` = `f`.`id_furgoneta`)) AS `kilometraje_actual`, (select `furgoneta_registro_kilometraje`.`fecha_registro_km` from `furgoneta_registro_kilometraje` where (`furgoneta_registro_kilometraje`.`id_furgoneta` = `f`.`id_furgoneta`) order by `furgoneta_registro_kilometraje`.`fecha_registro_km` desc limit 1) AS `fecha_ultimo_registro_km`, (select count(0) from `furgoneta_mantenimiento` where ((`furgoneta_mantenimiento`.`id_furgoneta` = `f`.`id_furgoneta`) and (`furgoneta_mantenimiento`.`activo_mantenimiento` = 1))) AS `total_mantenimientos`, (select sum(`furgoneta_mantenimiento`.`costo_mantenimiento`) from `furgoneta_mantenimiento` where ((`furgoneta_mantenimiento`.`id_furgoneta` = `f`.`id_furgoneta`) and (`furgoneta_mantenimiento`.`activo_mantenimiento` = 1))) AS `costo_total_mantenimientos`, (select `furgoneta_mantenimiento`.`fecha_mantenimiento` from `furgoneta_mantenimiento` where ((`furgoneta_mantenimiento`.`id_furgoneta` = `f`.`id_furgoneta`) and (`furgoneta_mantenimiento`.`activo_mantenimiento` = 1)) order by `furgoneta_mantenimiento`.`fecha_mantenimiento` desc limit 1) AS `fecha_ultimo_mantenimiento`, (case when ((to_days(`f`.`fecha_proxima_itv_furgoneta`) - to_days(curdate())) < 0) then 'ITV_VENCIDA' when ((to_days(`f`.`fecha_proxima_itv_furgoneta`) - to_days(curdate())) <= 30) then 'ITV_PROXIMA' else 'ITV_OK' end) AS `estado_itv`, (case when ((to_days(`f`.`fecha_vencimiento_seguro_furgoneta`) - to_days(curdate())) < 0) then 'SEGURO_VENCIDO' when ((to_days(`f`.`fecha_vencimiento_seguro_furgoneta`) - to_days(curdate())) <= 30) then 'SEGURO_PROXIMO' else 'SEGURO_OK' end) AS `estado_seguro`, (case when ((select max(`furgoneta_registro_kilometraje`.`kilometraje_registrado_km`) from `furgoneta_registro_kilometraje` where (`furgoneta_registro_kilometraje`.`id_furgoneta` = `f`.`id_furgoneta`)) is not null) then ((select max(`furgoneta_registro_kilometraje`.`kilometraje_registrado_km`) from `furgoneta_registro_kilometraje` where (`furgoneta_registro_kilometraje`.`id_furgoneta` = `f`.`id_furgoneta`)) - coalesce((select `furgoneta_mantenimiento`.`kilometraje_mantenimiento` from `furgoneta_mantenimiento` where ((`furgoneta_mantenimiento`.`id_furgoneta` = `f`.`id_furgoneta`) and (`furgoneta_mantenimiento`.`tipo_mantenimiento` = 'revision') and (`furgoneta_mantenimiento`.`activo_mantenimiento` = 1)) order by `furgoneta_mantenimiento`.`fecha_mantenimiento` desc limit 1),0)) else NULL end) AS `km_desde_ultima_revision`, (case when (((select max(`furgoneta_registro_kilometraje`.`kilometraje_registrado_km`) from `furgoneta_registro_kilometraje` where (`furgoneta_registro_kilometraje`.`id_furgoneta` = `f`.`id_furgoneta`)) is not null) and (((select max(`furgoneta_registro_kilometraje`.`kilometraje_registrado_km`) from `furgoneta_registro_kilometraje` where (`furgoneta_registro_kilometraje`.`id_furgoneta` = `f`.`id_furgoneta`)) - coalesce((select `furgoneta_mantenimiento`.`kilometraje_mantenimiento` from `furgoneta_mantenimiento` where ((`furgoneta_mantenimiento`.`id_furgoneta` = `f`.`id_furgoneta`) and (`furgoneta_mantenimiento`.`tipo_mantenimiento` = 'revision') and (`furgoneta_mantenimiento`.`activo_mantenimiento` = 1)) order by `furgoneta_mantenimiento`.`fecha_mantenimiento` desc limit 1),0)) >= `f`.`kilometros_entre_revisiones_furgoneta`)) then true else false end) AS `necesita_revision` FROM `furgoneta` AS `f` ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_kilometraje_completo`
--
DROP TABLE IF EXISTS `vista_kilometraje_completo`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `vista_kilometraje_completo`  AS SELECT `rk`.`id_registro_km` AS `id_registro_km`, `rk`.`id_furgoneta` AS `id_furgoneta`, `rk`.`fecha_registro_km` AS `fecha_registro_km`, `rk`.`kilometraje_registrado_km` AS `kilometraje_registrado_km`, `rk`.`tipo_registro_km` AS `tipo_registro_km`, `rk`.`observaciones_registro_km` AS `observaciones_registro_km`, `rk`.`created_at_registro_km` AS `created_at_registro_km`, `rk`.`updated_at_registro_km` AS `updated_at_registro_km`, `f`.`matricula_furgoneta` AS `matricula_furgoneta`, `f`.`marca_furgoneta` AS `marca_furgoneta`, `f`.`modelo_furgoneta` AS `modelo_furgoneta`, `f`.`estado_furgoneta` AS `estado_furgoneta`, coalesce((`rk`.`kilometraje_registrado_km` - lag(`rk`.`kilometraje_registrado_km`) OVER (PARTITION BY `rk`.`id_furgoneta` ORDER BY `rk`.`fecha_registro_km`,`rk`.`kilometraje_registrado_km` ) ),0) AS `km_recorridos`, coalesce((to_days(`rk`.`fecha_registro_km`) - to_days(lag(`rk`.`fecha_registro_km`) OVER (PARTITION BY `rk`.`id_furgoneta` ORDER BY `rk`.`fecha_registro_km`,`rk`.`kilometraje_registrado_km` ) )),0) AS `dias_transcurridos`, (case when (coalesce((to_days(`rk`.`fecha_registro_km`) - to_days(lag(`rk`.`fecha_registro_km`) OVER (PARTITION BY `rk`.`id_furgoneta` ORDER BY `rk`.`fecha_registro_km`,`rk`.`kilometraje_registrado_km` ) )),0) > 0) then round((coalesce((`rk`.`kilometraje_registrado_km` - lag(`rk`.`kilometraje_registrado_km`) OVER (PARTITION BY `rk`.`id_furgoneta` ORDER BY `rk`.`fecha_registro_km`,`rk`.`kilometraje_registrado_km` ) ),0) / coalesce((to_days(`rk`.`fecha_registro_km`) - to_days(lag(`rk`.`fecha_registro_km`) OVER (PARTITION BY `rk`.`id_furgoneta` ORDER BY `rk`.`fecha_registro_km`,`rk`.`kilometraje_registrado_km` ) )),1)),2) else 0 end) AS `km_promedio_diario` FROM (`furgoneta_registro_kilometraje` `rk` join `furgoneta` `f` on((`rk`.`id_furgoneta` = `f`.`id_furgoneta`))) ORDER BY `rk`.`id_furgoneta` ASC, `rk`.`fecha_registro_km` DESC, `rk`.`kilometraje_registrado_km` DESC ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_kit_completa`
--
DROP TABLE IF EXISTS `vista_kit_completa`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `vista_kit_completa`  AS SELECT `k`.`id_kit` AS `id_kit`, `k`.`cantidad_kit` AS `cantidad_kit`, `k`.`activo_kit` AS `activo_kit`, `k`.`created_at_kit` AS `created_at_kit`, `k`.`updated_at_kit` AS `updated_at_kit`, `k`.`id_articulo_maestro` AS `id_articulo_maestro`, `am`.`codigo_articulo` AS `codigo_articulo_maestro`, `am`.`nombre_articulo` AS `nombre_articulo_maestro`, `am`.`name_articulo` AS `name_articulo_maestro`, `am`.`precio_alquiler_articulo` AS `precio_articulo_maestro`, `am`.`es_kit_articulo` AS `es_kit_articulo_maestro`, `am`.`activo_articulo` AS `activo_articulo_maestro`, `k`.`id_articulo_componente` AS `id_articulo_componente`, `ac`.`codigo_articulo` AS `codigo_articulo_componente`, `ac`.`nombre_articulo` AS `nombre_articulo_componente`, `ac`.`name_articulo` AS `name_articulo_componente`, `ac`.`precio_alquiler_articulo` AS `precio_articulo_componente`, `ac`.`es_kit_articulo` AS `es_kit_articulo_componente`, `ac`.`activo_articulo` AS `activo_articulo_componente`, (`k`.`cantidad_kit` * `ac`.`precio_alquiler_articulo`) AS `subtotal_componente`, (select count(0) from `kit` `k2` where ((`k2`.`id_articulo_maestro` = `k`.`id_articulo_maestro`) and (`k2`.`activo_kit` = 1))) AS `total_componentes_kit`, (select sum((`k2`.`cantidad_kit` * `a2`.`precio_alquiler_articulo`)) from (`kit` `k2` join `articulo` `a2` on((`k2`.`id_articulo_componente` = `a2`.`id_articulo`))) where ((`k2`.`id_articulo_maestro` = `k`.`id_articulo_maestro`) and (`k2`.`activo_kit` = 1) and (`a2`.`activo_articulo` = 1))) AS `precio_total_kit` FROM ((`kit` `k` join `articulo` `am` on((`k`.`id_articulo_maestro` = `am`.`id_articulo`))) join `articulo` `ac` on((`k`.`id_articulo_componente` = `ac`.`id_articulo`))) ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_mantenimiento_completo`
--
DROP TABLE IF EXISTS `vista_mantenimiento_completo`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `vista_mantenimiento_completo`  AS SELECT `m`.`id_mantenimiento` AS `id_mantenimiento`, `m`.`id_furgoneta` AS `id_furgoneta`, `m`.`fecha_mantenimiento` AS `fecha_mantenimiento`, `m`.`tipo_mantenimiento` AS `tipo_mantenimiento`, `m`.`descripcion_mantenimiento` AS `descripcion_mantenimiento`, `m`.`kilometraje_mantenimiento` AS `kilometraje_mantenimiento`, `m`.`costo_mantenimiento` AS `costo_mantenimiento`, `m`.`numero_factura_mantenimiento` AS `numero_factura_mantenimiento`, `m`.`taller_mantenimiento` AS `taller_mantenimiento`, `m`.`telefono_taller_mantenimiento` AS `telefono_taller_mantenimiento`, `m`.`direccion_taller_mantenimiento` AS `direccion_taller_mantenimiento`, `m`.`resultado_itv` AS `resultado_itv`, `m`.`fecha_proxima_itv` AS `fecha_proxima_itv`, `m`.`garantia_hasta_mantenimiento` AS `garantia_hasta_mantenimiento`, `m`.`observaciones_mantenimiento` AS `observaciones_mantenimiento`, `m`.`activo_mantenimiento` AS `activo_mantenimiento`, `m`.`created_at_mantenimiento` AS `created_at_mantenimiento`, `m`.`updated_at_mantenimiento` AS `updated_at_mantenimiento`, `f`.`matricula_furgoneta` AS `matricula_furgoneta`, `f`.`marca_furgoneta` AS `marca_furgoneta`, `f`.`modelo_furgoneta` AS `modelo_furgoneta`, `f`.`anio_furgoneta` AS `anio_furgoneta`, `f`.`estado_furgoneta` AS `estado_furgoneta`, (case when (`m`.`garantia_hasta_mantenimiento` is null) then 'SIN_GARANTIA' when (`m`.`garantia_hasta_mantenimiento` < curdate()) then 'GARANTIA_VENCIDA' when ((to_days(`m`.`garantia_hasta_mantenimiento`) - to_days(curdate())) <= 30) then 'GARANTIA_PROXIMA' else 'GARANTIA_VIGENTE' end) AS `estado_garantia`, (to_days(curdate()) - to_days(`m`.`fecha_mantenimiento`)) AS `dias_desde_mantenimiento`, (case when (((select max(`furgoneta_registro_kilometraje`.`kilometraje_registrado_km`) from `furgoneta_registro_kilometraje` where (`furgoneta_registro_kilometraje`.`id_furgoneta` = `m`.`id_furgoneta`)) is not null) and (`m`.`kilometraje_mantenimiento` is not null)) then ((select max(`furgoneta_registro_kilometraje`.`kilometraje_registrado_km`) from `furgoneta_registro_kilometraje` where (`furgoneta_registro_kilometraje`.`id_furgoneta` = `m`.`id_furgoneta`)) - `m`.`kilometraje_mantenimiento`) else NULL end) AS `km_desde_mantenimiento` FROM (`furgoneta_mantenimiento` `m` join `furgoneta` `f` on((`m`.`id_furgoneta` = `f`.`id_furgoneta`))) WHERE (`m`.`activo_mantenimiento` = 1) ORDER BY `m`.`fecha_mantenimiento` DESC ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_presupuesto_completa`
--
DROP TABLE IF EXISTS `vista_presupuesto_completa`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `vista_presupuesto_completa`  AS SELECT `p`.`id_presupuesto` AS `id_presupuesto`, `p`.`numero_presupuesto` AS `numero_presupuesto`, `p`.`version_actual_presupuesto` AS `version_actual_presupuesto`, `p`.`fecha_presupuesto` AS `fecha_presupuesto`, `p`.`fecha_validez_presupuesto` AS `fecha_validez_presupuesto`, `p`.`fecha_inicio_evento_presupuesto` AS `fecha_inicio_evento_presupuesto`, `p`.`fecha_fin_evento_presupuesto` AS `fecha_fin_evento_presupuesto`, `p`.`numero_pedido_cliente_presupuesto` AS `numero_pedido_cliente_presupuesto`, `p`.`aplicar_coeficientes_presupuesto` AS `aplicar_coeficientes_presupuesto`, `p`.`descuento_presupuesto` AS `descuento_presupuesto`, `p`.`nombre_evento_presupuesto` AS `nombre_evento_presupuesto`, `p`.`direccion_evento_presupuesto` AS `direccion_evento_presupuesto`, `p`.`poblacion_evento_presupuesto` AS `poblacion_evento_presupuesto`, `p`.`cp_evento_presupuesto` AS `cp_evento_presupuesto`, `p`.`provincia_evento_presupuesto` AS `provincia_evento_presupuesto`, `p`.`observaciones_cabecera_presupuesto` AS `observaciones_cabecera_presupuesto`, `p`.`observaciones_pie_presupuesto` AS `observaciones_pie_presupuesto`, `p`.`observaciones_cabecera_ingles_presupuesto` AS `observaciones_cabecera_ingles_presupuesto`, `p`.`observaciones_pie_ingles_presupuesto` AS `observaciones_pie_ingles_presupuesto`, `p`.`mostrar_obs_familias_presupuesto` AS `mostrar_obs_familias_presupuesto`, `p`.`mostrar_obs_articulos_presupuesto` AS `mostrar_obs_articulos_presupuesto`, `p`.`observaciones_internas_presupuesto` AS `observaciones_internas_presupuesto`, `p`.`activo_presupuesto` AS `activo_presupuesto`, `p`.`created_at_presupuesto` AS `created_at_presupuesto`, `p`.`updated_at_presupuesto` AS `updated_at_presupuesto`, `c`.`id_cliente` AS `id_cliente`, `c`.`codigo_cliente` AS `codigo_cliente`, `c`.`nombre_cliente` AS `nombre_cliente`, `c`.`nif_cliente` AS `nif_cliente`, `c`.`direccion_cliente` AS `direccion_cliente`, `c`.`cp_cliente` AS `cp_cliente`, `c`.`poblacion_cliente` AS `poblacion_cliente`, `c`.`provincia_cliente` AS `provincia_cliente`, `c`.`telefono_cliente` AS `telefono_cliente`, `c`.`email_cliente` AS `email_cliente`, `c`.`porcentaje_descuento_cliente` AS `porcentaje_descuento_cliente`, `c`.`nombre_facturacion_cliente` AS `nombre_facturacion_cliente`, `c`.`direccion_facturacion_cliente` AS `direccion_facturacion_cliente`, `c`.`cp_facturacion_cliente` AS `cp_facturacion_cliente`, `c`.`poblacion_facturacion_cliente` AS `poblacion_facturacion_cliente`, `c`.`provincia_facturacion_cliente` AS `provincia_facturacion_cliente`, `cc`.`id_contacto_cliente` AS `id_contacto_cliente`, `cc`.`nombre_contacto_cliente` AS `nombre_contacto_cliente`, `cc`.`apellidos_contacto_cliente` AS `apellidos_contacto_cliente`, `cc`.`telefono_contacto_cliente` AS `telefono_contacto_cliente`, `cc`.`email_contacto_cliente` AS `email_contacto_cliente`, `ep`.`id_estado_ppto` AS `id_estado_ppto`, `ep`.`codigo_estado_ppto` AS `codigo_estado_ppto`, `ep`.`nombre_estado_ppto` AS `nombre_estado_ppto`, `ep`.`color_estado_ppto` AS `color_estado_ppto`, `ep`.`orden_estado_ppto` AS `orden_estado_ppto`, `fp`.`id_pago` AS `id_forma_pago`, `fp`.`codigo_pago` AS `codigo_pago`, `fp`.`nombre_pago` AS `nombre_pago`, `fp`.`porcentaje_anticipo_pago` AS `porcentaje_anticipo_pago`, `fp`.`dias_anticipo_pago` AS `dias_anticipo_pago`, `fp`.`porcentaje_final_pago` AS `porcentaje_final_pago`, `fp`.`dias_final_pago` AS `dias_final_pago`, `fp`.`descuento_pago` AS `descuento_pago`, `mp`.`id_metodo_pago` AS `id_metodo_pago`, `mp`.`codigo_metodo_pago` AS `codigo_metodo_pago`, `mp`.`nombre_metodo_pago` AS `nombre_metodo_pago`, `mc`.`id_metodo` AS `id_metodo_contacto`, `mc`.`nombre` AS `nombre_metodo_contacto`, `c`.`id_forma_pago_habitual` AS `id_forma_pago_habitual`, `fph`.`nombre_pago` AS `nombre_forma_pago_habitual_cliente`, concat_ws(', ',`p`.`direccion_evento_presupuesto`,concat(`p`.`cp_evento_presupuesto`,' ',`p`.`poblacion_evento_presupuesto`),`p`.`provincia_evento_presupuesto`) AS `direccion_completa_evento_presupuesto`, concat_ws(', ',`c`.`direccion_cliente`,concat(`c`.`cp_cliente`,' ',`c`.`poblacion_cliente`),`c`.`provincia_cliente`) AS `direccion_completa_cliente`, concat_ws(', ',`c`.`direccion_facturacion_cliente`,concat(`c`.`cp_facturacion_cliente`,' ',`c`.`poblacion_facturacion_cliente`),`c`.`provincia_facturacion_cliente`) AS `direccion_facturacion_completa_cliente`, concat_ws(' ',`cc`.`nombre_contacto_cliente`,`cc`.`apellidos_contacto_cliente`) AS `nombre_completo_contacto`, (to_days(`p`.`fecha_validez_presupuesto`) - to_days(curdate())) AS `dias_validez_restantes`, (case when (`p`.`fecha_validez_presupuesto` is null) then 'Sin fecha de validez' when (`p`.`fecha_validez_presupuesto` < curdate()) then 'Caducado' when (`p`.`fecha_validez_presupuesto` = curdate()) then 'Caduca hoy' when ((to_days(`p`.`fecha_validez_presupuesto`) - to_days(curdate())) <= 7) then 'Próximo a caducar' else 'Vigente' end) AS `estado_validez_presupuesto`, ((to_days(`p`.`fecha_fin_evento_presupuesto`) - to_days(`p`.`fecha_inicio_evento_presupuesto`)) + 1) AS `duracion_evento_dias`, (to_days(`p`.`fecha_inicio_evento_presupuesto`) - to_days(curdate())) AS `dias_hasta_inicio_evento`, (to_days(`p`.`fecha_fin_evento_presupuesto`) - to_days(curdate())) AS `dias_hasta_fin_evento`, (case when (`p`.`fecha_inicio_evento_presupuesto` is null) then 'Sin fecha de evento' when ((`p`.`fecha_inicio_evento_presupuesto` < curdate()) and (`p`.`fecha_fin_evento_presupuesto` < curdate())) then 'Evento finalizado' when ((`p`.`fecha_inicio_evento_presupuesto` <= curdate()) and (`p`.`fecha_fin_evento_presupuesto` >= curdate())) then 'Evento en curso' when (`p`.`fecha_inicio_evento_presupuesto` = curdate()) then 'Evento HOY' when ((to_days(`p`.`fecha_inicio_evento_presupuesto`) - to_days(curdate())) = 1) then 'Evento MAÑANA' when ((to_days(`p`.`fecha_inicio_evento_presupuesto`) - to_days(curdate())) <= 7) then 'Evento esta semana' when ((to_days(`p`.`fecha_inicio_evento_presupuesto`) - to_days(curdate())) <= 30) then 'Evento este mes' else 'Evento futuro' end) AS `estado_evento_presupuesto`, (case when (`p`.`fecha_inicio_evento_presupuesto` is null) then 'Sin prioridad' when (`p`.`fecha_inicio_evento_presupuesto` = curdate()) then 'HOY' when ((to_days(`p`.`fecha_inicio_evento_presupuesto`) - to_days(curdate())) = 1) then 'MAÑANA' when ((to_days(`p`.`fecha_inicio_evento_presupuesto`) - to_days(curdate())) <= 7) then 'Esta semana' when ((to_days(`p`.`fecha_inicio_evento_presupuesto`) - to_days(curdate())) <= 15) then 'Próximo' when ((to_days(`p`.`fecha_inicio_evento_presupuesto`) - to_days(curdate())) <= 30) then 'Este mes' else 'Futuro' end) AS `prioridad_presupuesto`, (case when (`fp`.`id_pago` is null) then 'Sin forma de pago' when (`fp`.`porcentaje_anticipo_pago` = 100.00) then 'Pago único' when (`fp`.`porcentaje_anticipo_pago` < 100.00) then 'Pago fraccionado' else 'Sin forma de pago' end) AS `tipo_pago_presupuesto`, (case when (`fp`.`id_pago` is null) then 'Sin forma de pago asignada' when (`fp`.`porcentaje_anticipo_pago` = 100.00) then concat(`mp`.`nombre_metodo_pago`,' - ',`fp`.`nombre_pago`,(case when (`fp`.`descuento_pago` > 0) then concat(' (Dto: ',`fp`.`descuento_pago`,'%)') else '' end)) else concat(`mp`.`nombre_metodo_pago`,' - ',`fp`.`porcentaje_anticipo_pago`,'% + ',`fp`.`porcentaje_final_pago`,'%') end) AS `descripcion_completa_forma_pago`, (case when (`fp`.`dias_anticipo_pago` = 0) then `p`.`fecha_presupuesto` else (`p`.`fecha_presupuesto` + interval `fp`.`dias_anticipo_pago` day) end) AS `fecha_vencimiento_anticipo`, (case when ((`fp`.`dias_final_pago` = 0) and (`p`.`fecha_fin_evento_presupuesto` is not null)) then `p`.`fecha_fin_evento_presupuesto` when (`fp`.`dias_final_pago` > 0) then (`p`.`fecha_presupuesto` + interval `fp`.`dias_final_pago` day) when ((`fp`.`dias_final_pago` < 0) and (`p`.`fecha_inicio_evento_presupuesto` is not null)) then (`p`.`fecha_inicio_evento_presupuesto` + interval `fp`.`dias_final_pago` day) else NULL end) AS `fecha_vencimiento_final`, (case when (`p`.`descuento_presupuesto` = `c`.`porcentaje_descuento_cliente`) then 'Igual al habitual' when (`p`.`descuento_presupuesto` > `c`.`porcentaje_descuento_cliente`) then 'Mayor al habitual' when (`p`.`descuento_presupuesto` < `c`.`porcentaje_descuento_cliente`) then 'Menor al habitual' else 'Sin comparar' end) AS `comparacion_descuento`, (case when (`p`.`descuento_presupuesto` = 0.00) then 'Sin descuento' when ((`p`.`descuento_presupuesto` > 0.00) and (`p`.`descuento_presupuesto` <= 5.00)) then concat('Descuento bajo: ',`p`.`descuento_presupuesto`,'%') when ((`p`.`descuento_presupuesto` > 5.00) and (`p`.`descuento_presupuesto` <= 15.00)) then concat('Descuento medio: ',`p`.`descuento_presupuesto`,'%') when (`p`.`descuento_presupuesto` > 15.00) then concat('Descuento alto: ',`p`.`descuento_presupuesto`,'%') else 'Sin descuento' end) AS `estado_descuento_presupuesto`, (case when (`p`.`descuento_presupuesto` > 0.00) then true else false end) AS `aplica_descuento_presupuesto`, (`p`.`descuento_presupuesto` - `c`.`porcentaje_descuento_cliente`) AS `diferencia_descuento`, (case when (`c`.`direccion_facturacion_cliente` is not null) then true else false end) AS `tiene_direccion_facturacion_diferente`, (to_days(curdate()) - to_days(`p`.`fecha_presupuesto`)) AS `dias_desde_emision`, `pv`.`id_version_presupuesto` AS `id_version_actual`, `pv`.`numero_version_presupuesto` AS `numero_version_actual`, `pv`.`estado_version_presupuesto` AS `estado_version_actual`, `pv`.`fecha_creacion_version` AS `fecha_creacion_version_actual`, (case when (`ep`.`codigo_estado_ppto` = 'CANC') then 'Cancelado' when (`ep`.`codigo_estado_ppto` = 'FACT') then 'Facturado' when ((`p`.`fecha_validez_presupuesto` < curdate()) and (`ep`.`codigo_estado_ppto` not in ('ACEP','RECH','CANC','FACT'))) then 'Caducado' when ((`p`.`fecha_inicio_evento_presupuesto` < curdate()) and (`p`.`fecha_fin_evento_presupuesto` < curdate())) then 'Evento finalizado' when ((`p`.`fecha_inicio_evento_presupuesto` <= curdate()) and (`p`.`fecha_fin_evento_presupuesto` >= curdate())) then 'Evento en curso' when (`ep`.`codigo_estado_ppto` = 'ACEP') then 'Aceptado - Pendiente evento' else `ep`.`nombre_estado_ppto` end) AS `estado_general_presupuesto` FROM ((((((((`presupuesto` `p` join `cliente` `c` on((`p`.`id_cliente` = `c`.`id_cliente`))) left join `contacto_cliente` `cc` on((`p`.`id_contacto_cliente` = `cc`.`id_contacto_cliente`))) join `estado_presupuesto` `ep` on((`p`.`id_estado_ppto` = `ep`.`id_estado_ppto`))) left join `forma_pago` `fp` on((`p`.`id_forma_pago` = `fp`.`id_pago`))) left join `metodo_pago` `mp` on((`fp`.`id_metodo_pago` = `mp`.`id_metodo_pago`))) left join `metodos_contacto` `mc` on((`p`.`id_metodo` = `mc`.`id_metodo`))) left join `forma_pago` `fph` on((`c`.`id_forma_pago_habitual` = `fph`.`id_pago`))) left join `presupuesto_version` `pv` on(((`p`.`id_presupuesto` = `pv`.`id_presupuesto`) and (`pv`.`numero_version_presupuesto` = `p`.`version_actual_presupuesto`)))) ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_registro_kilometraje`
--
DROP TABLE IF EXISTS `vista_registro_kilometraje`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `vista_registro_kilometraje`  AS SELECT `rk`.`id_registro_km` AS `id_registro_km`, `rk`.`id_furgoneta` AS `id_furgoneta`, `rk`.`fecha_registro_km` AS `fecha_registro_km`, `rk`.`kilometraje_registrado_km` AS `kilometraje_registrado_km`, `rk`.`tipo_registro_km` AS `tipo_registro_km`, `rk`.`observaciones_registro_km` AS `observaciones_registro_km`, `rk`.`created_at_registro_km` AS `created_at_registro_km`, `f`.`matricula_furgoneta` AS `matricula_furgoneta`, `f`.`marca_furgoneta` AS `marca_furgoneta`, `f`.`modelo_furgoneta` AS `modelo_furgoneta`, `f`.`estado_furgoneta` AS `estado_furgoneta`, (`rk`.`kilometraje_registrado_km` - coalesce((select `furgoneta_registro_kilometraje`.`kilometraje_registrado_km` from `furgoneta_registro_kilometraje` where ((`furgoneta_registro_kilometraje`.`id_furgoneta` = `rk`.`id_furgoneta`) and (`furgoneta_registro_kilometraje`.`fecha_registro_km` < `rk`.`fecha_registro_km`)) order by `furgoneta_registro_kilometraje`.`fecha_registro_km` desc limit 1),0)) AS `km_recorridos`, (to_days(`rk`.`fecha_registro_km`) - to_days(coalesce((select `furgoneta_registro_kilometraje`.`fecha_registro_km` from `furgoneta_registro_kilometraje` where ((`furgoneta_registro_kilometraje`.`id_furgoneta` = `rk`.`id_furgoneta`) and (`furgoneta_registro_kilometraje`.`fecha_registro_km` < `rk`.`fecha_registro_km`)) order by `furgoneta_registro_kilometraje`.`fecha_registro_km` desc limit 1),`rk`.`fecha_registro_km`))) AS `dias_transcurridos`, (case when ((to_days(`rk`.`fecha_registro_km`) - to_days(coalesce((select `furgoneta_registro_kilometraje`.`fecha_registro_km` from `furgoneta_registro_kilometraje` where ((`furgoneta_registro_kilometraje`.`id_furgoneta` = `rk`.`id_furgoneta`) and (`furgoneta_registro_kilometraje`.`fecha_registro_km` < `rk`.`fecha_registro_km`)) order by `furgoneta_registro_kilometraje`.`fecha_registro_km` desc limit 1),`rk`.`fecha_registro_km`))) > 0) then ((`rk`.`kilometraje_registrado_km` - coalesce((select `furgoneta_registro_kilometraje`.`kilometraje_registrado_km` from `furgoneta_registro_kilometraje` where ((`furgoneta_registro_kilometraje`.`id_furgoneta` = `rk`.`id_furgoneta`) and (`furgoneta_registro_kilometraje`.`fecha_registro_km` < `rk`.`fecha_registro_km`)) order by `furgoneta_registro_kilometraje`.`fecha_registro_km` desc limit 1),0)) / (to_days(`rk`.`fecha_registro_km`) - to_days(coalesce((select `furgoneta_registro_kilometraje`.`fecha_registro_km` from `furgoneta_registro_kilometraje` where ((`furgoneta_registro_kilometraje`.`id_furgoneta` = `rk`.`id_furgoneta`) and (`furgoneta_registro_kilometraje`.`fecha_registro_km` < `rk`.`fecha_registro_km`)) order by `furgoneta_registro_kilometraje`.`fecha_registro_km` desc limit 1),`rk`.`fecha_registro_km`)))) else 0 end) AS `km_promedio_diario` FROM (`furgoneta_registro_kilometraje` `rk` join `furgoneta` `f` on((`rk`.`id_furgoneta` = `f`.`id_furgoneta`))) ORDER BY `rk`.`fecha_registro_km` DESC ;

-- --------------------------------------------------------

--
-- Estructura para la vista `v_linea_presupuesto_calculada`
--
DROP TABLE IF EXISTS `v_linea_presupuesto_calculada`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `v_linea_presupuesto_calculada`  AS SELECT `lp`.`id_linea_ppto` AS `id_linea_ppto`, `lp`.`id_version_presupuesto` AS `id_version_presupuesto`, `lp`.`id_articulo` AS `id_articulo`, `lp`.`id_linea_padre` AS `id_linea_padre`, `lp`.`id_ubicacion` AS `id_ubicacion`, `lp`.`numero_linea_ppto` AS `numero_linea_ppto`, `lp`.`tipo_linea_ppto` AS `tipo_linea_ppto`, `lp`.`nivel_jerarquia` AS `nivel_jerarquia`, `lp`.`codigo_linea_ppto` AS `codigo_linea_ppto`, `lp`.`descripcion_linea_ppto` AS `descripcion_linea_ppto`, `lp`.`orden_linea_ppto` AS `orden_linea_ppto`, `lp`.`observaciones_linea_ppto` AS `observaciones_linea_ppto`, `lp`.`mostrar_obs_articulo_linea_ppto` AS `mostrar_obs_articulo_linea_ppto`, `lp`.`ocultar_detalle_kit_linea_ppto` AS `ocultar_detalle_kit_linea_ppto`, `lp`.`mostrar_en_presupuesto` AS `mostrar_en_presupuesto`, `lp`.`es_opcional` AS `es_opcional`, `lp`.`activo_linea_ppto` AS `activo_linea_ppto`, `lp`.`fecha_montaje_linea_ppto` AS `fecha_montaje_linea_ppto`, `lp`.`fecha_desmontaje_linea_ppto` AS `fecha_desmontaje_linea_ppto`, `lp`.`fecha_inicio_linea_ppto` AS `fecha_inicio_linea_ppto`, `lp`.`fecha_fin_linea_ppto` AS `fecha_fin_linea_ppto`, `lp`.`cantidad_linea_ppto` AS `cantidad_linea_ppto`, `lp`.`precio_unitario_linea_ppto` AS `precio_unitario_linea_ppto`, `lp`.`descuento_linea_ppto` AS `descuento_linea_ppto`, `lp`.`porcentaje_iva_linea_ppto` AS `porcentaje_iva_linea_ppto`, `lp`.`jornadas_linea_ppto` AS `jornadas_linea_ppto`, `lp`.`id_coeficiente` AS `id_coeficiente`, `lp`.`aplicar_coeficiente_linea_ppto` AS `aplicar_coeficiente_linea_ppto`, `lp`.`valor_coeficiente_linea_ppto` AS `valor_coeficiente_linea_ppto`, `c`.`jornadas_coeficiente` AS `jornadas_coeficiente`, `c`.`valor_coeficiente` AS `valor_coeficiente`, `c`.`observaciones_coeficiente` AS `observaciones_coeficiente`, `c`.`activo_coeficiente` AS `activo_coeficiente`, (case when ((`lp`.`fecha_inicio_linea_ppto` is not null) and (`lp`.`fecha_fin_linea_ppto` is not null)) then ((to_days(`lp`.`fecha_fin_linea_ppto`) - to_days(`lp`.`fecha_inicio_linea_ppto`)) + 1) else 1 end) AS `dias_linea`, ((((case when ((`lp`.`fecha_inicio_linea_ppto` is not null) and (`lp`.`fecha_fin_linea_ppto` is not null)) then ((to_days(`lp`.`fecha_fin_linea_ppto`) - to_days(`lp`.`fecha_inicio_linea_ppto`)) + 1) else 1 end) * `lp`.`cantidad_linea_ppto`) * `lp`.`precio_unitario_linea_ppto`) * (1 - (`lp`.`descuento_linea_ppto` / 100))) AS `subtotal_sin_coeficiente`, (case when ((`lp`.`aplicar_coeficiente_linea_ppto` = 1) and (`lp`.`valor_coeficiente_linea_ppto` is not null) and (`lp`.`valor_coeficiente_linea_ppto` > 0)) then (((`lp`.`cantidad_linea_ppto` * `lp`.`precio_unitario_linea_ppto`) * (1 - (`lp`.`descuento_linea_ppto` / 100))) * `lp`.`valor_coeficiente_linea_ppto`) else ((((case when ((`lp`.`fecha_inicio_linea_ppto` is not null) and (`lp`.`fecha_fin_linea_ppto` is not null)) then ((to_days(`lp`.`fecha_fin_linea_ppto`) - to_days(`lp`.`fecha_inicio_linea_ppto`)) + 1) else 1 end) * `lp`.`cantidad_linea_ppto`) * `lp`.`precio_unitario_linea_ppto`) * (1 - (`lp`.`descuento_linea_ppto` / 100))) end) AS `base_imponible`, (case when ((`lp`.`aplicar_coeficiente_linea_ppto` = 1) and (`lp`.`valor_coeficiente_linea_ppto` is not null) and (`lp`.`valor_coeficiente_linea_ppto` > 0)) then ((((`lp`.`cantidad_linea_ppto` * `lp`.`precio_unitario_linea_ppto`) * (1 - (`lp`.`descuento_linea_ppto` / 100))) * `lp`.`valor_coeficiente_linea_ppto`) * (`lp`.`porcentaje_iva_linea_ppto` / 100)) else (((((case when ((`lp`.`fecha_inicio_linea_ppto` is not null) and (`lp`.`fecha_fin_linea_ppto` is not null)) then ((to_days(`lp`.`fecha_fin_linea_ppto`) - to_days(`lp`.`fecha_inicio_linea_ppto`)) + 1) else 1 end) * `lp`.`cantidad_linea_ppto`) * `lp`.`precio_unitario_linea_ppto`) * (1 - (`lp`.`descuento_linea_ppto` / 100))) * (`lp`.`porcentaje_iva_linea_ppto` / 100)) end) AS `importe_iva`, (case when ((`lp`.`aplicar_coeficiente_linea_ppto` = 1) and (`lp`.`valor_coeficiente_linea_ppto` is not null) and (`lp`.`valor_coeficiente_linea_ppto` > 0)) then ((((`lp`.`cantidad_linea_ppto` * `lp`.`precio_unitario_linea_ppto`) * (1 - (`lp`.`descuento_linea_ppto` / 100))) * `lp`.`valor_coeficiente_linea_ppto`) * (1 + (`lp`.`porcentaje_iva_linea_ppto` / 100))) else (((((case when ((`lp`.`fecha_inicio_linea_ppto` is not null) and (`lp`.`fecha_fin_linea_ppto` is not null)) then ((to_days(`lp`.`fecha_fin_linea_ppto`) - to_days(`lp`.`fecha_inicio_linea_ppto`)) + 1) else 1 end) * `lp`.`cantidad_linea_ppto`) * `lp`.`precio_unitario_linea_ppto`) * (1 - (`lp`.`descuento_linea_ppto` / 100))) * (1 + (`lp`.`porcentaje_iva_linea_ppto` / 100))) end) AS `total_linea`, (case when (`a`.`permitir_descuentos_articulo` = 1) then (`lp`.`precio_unitario_linea_ppto` - ((`lp`.`precio_unitario_linea_ppto` * `p`.`porcentaje_descuento_cliente`) / 100)) else `lp`.`precio_unitario_linea_ppto` end) AS `precio_unitario_linea_ppto_hotel`, (case when ((`lp`.`aplicar_coeficiente_linea_ppto` = 1) and (`lp`.`valor_coeficiente_linea_ppto` is not null) and (`lp`.`valor_coeficiente_linea_ppto` > 0)) then (case when (`a`.`permitir_descuentos_articulo` = 1) then (((`lp`.`precio_unitario_linea_ppto` - ((`lp`.`precio_unitario_linea_ppto` * `p`.`porcentaje_descuento_cliente`) / 100)) * `lp`.`valor_coeficiente_linea_ppto`) * `lp`.`cantidad_linea_ppto`) else ((`lp`.`precio_unitario_linea_ppto` * `lp`.`valor_coeficiente_linea_ppto`) * `lp`.`cantidad_linea_ppto`) end) else (case when (`a`.`permitir_descuentos_articulo` = 1) then (((case when ((`lp`.`fecha_inicio_linea_ppto` is not null) and (`lp`.`fecha_fin_linea_ppto` is not null)) then ((to_days(`lp`.`fecha_fin_linea_ppto`) - to_days(`lp`.`fecha_inicio_linea_ppto`)) + 1) else 1 end) * `lp`.`cantidad_linea_ppto`) * (`lp`.`precio_unitario_linea_ppto` - ((`lp`.`precio_unitario_linea_ppto` * `p`.`porcentaje_descuento_cliente`) / 100))) else (((case when ((`lp`.`fecha_inicio_linea_ppto` is not null) and (`lp`.`fecha_fin_linea_ppto` is not null)) then ((to_days(`lp`.`fecha_fin_linea_ppto`) - to_days(`lp`.`fecha_inicio_linea_ppto`)) + 1) else 1 end) * `lp`.`cantidad_linea_ppto`) * `lp`.`precio_unitario_linea_ppto`) end) end) AS `base_imponible_hotel`, (((case when ((`lp`.`aplicar_coeficiente_linea_ppto` = 1) and (`lp`.`valor_coeficiente_linea_ppto` is not null) and (`lp`.`valor_coeficiente_linea_ppto` > 0)) then (case when (`a`.`permitir_descuentos_articulo` = 1) then (((`lp`.`precio_unitario_linea_ppto` - ((`lp`.`precio_unitario_linea_ppto` * `p`.`porcentaje_descuento_cliente`) / 100)) * `lp`.`valor_coeficiente_linea_ppto`) * `lp`.`cantidad_linea_ppto`) else ((`lp`.`precio_unitario_linea_ppto` * `lp`.`valor_coeficiente_linea_ppto`) * `lp`.`cantidad_linea_ppto`) end) else (case when (`a`.`permitir_descuentos_articulo` = 1) then (((case when ((`lp`.`fecha_inicio_linea_ppto` is not null) and (`lp`.`fecha_fin_linea_ppto` is not null)) then ((to_days(`lp`.`fecha_fin_linea_ppto`) - to_days(`lp`.`fecha_inicio_linea_ppto`)) + 1) else 1 end) * `lp`.`cantidad_linea_ppto`) * (`lp`.`precio_unitario_linea_ppto` - ((`lp`.`precio_unitario_linea_ppto` * `p`.`porcentaje_descuento_cliente`) / 100))) else (((case when ((`lp`.`fecha_inicio_linea_ppto` is not null) and (`lp`.`fecha_fin_linea_ppto` is not null)) then ((to_days(`lp`.`fecha_fin_linea_ppto`) - to_days(`lp`.`fecha_inicio_linea_ppto`)) + 1) else 1 end) * `lp`.`cantidad_linea_ppto`) * `lp`.`precio_unitario_linea_ppto`) end) end) * `lp`.`descuento_linea_ppto`) / 100) AS `importe_descuento_linea_ppto_hotel`, (case when ((`lp`.`aplicar_coeficiente_linea_ppto` = 1) and (`lp`.`valor_coeficiente_linea_ppto` is not null) and (`lp`.`valor_coeficiente_linea_ppto` > 0)) then (case when (`a`.`permitir_descuentos_articulo` = 1) then ((((`lp`.`precio_unitario_linea_ppto` - ((`lp`.`precio_unitario_linea_ppto` * `p`.`porcentaje_descuento_cliente`) / 100)) * `lp`.`valor_coeficiente_linea_ppto`) * `lp`.`cantidad_linea_ppto`) * (1 - (`lp`.`descuento_linea_ppto` / 100))) else (((`lp`.`precio_unitario_linea_ppto` * `lp`.`valor_coeficiente_linea_ppto`) * `lp`.`cantidad_linea_ppto`) * (1 - (`lp`.`descuento_linea_ppto` / 100))) end) else (case when (`a`.`permitir_descuentos_articulo` = 1) then ((((case when ((`lp`.`fecha_inicio_linea_ppto` is not null) and (`lp`.`fecha_fin_linea_ppto` is not null)) then ((to_days(`lp`.`fecha_fin_linea_ppto`) - to_days(`lp`.`fecha_inicio_linea_ppto`)) + 1) else 1 end) * `lp`.`cantidad_linea_ppto`) * (`lp`.`precio_unitario_linea_ppto` - ((`lp`.`precio_unitario_linea_ppto` * `p`.`porcentaje_descuento_cliente`) / 100))) * (1 - (`lp`.`descuento_linea_ppto` / 100))) else ((((case when ((`lp`.`fecha_inicio_linea_ppto` is not null) and (`lp`.`fecha_fin_linea_ppto` is not null)) then ((to_days(`lp`.`fecha_fin_linea_ppto`) - to_days(`lp`.`fecha_inicio_linea_ppto`)) + 1) else 1 end) * `lp`.`cantidad_linea_ppto`) * `lp`.`precio_unitario_linea_ppto`) * (1 - (`lp`.`descuento_linea_ppto` / 100))) end) end) AS `TotalImporte_descuento_linea_ppto_hotel`, (((case when ((`lp`.`aplicar_coeficiente_linea_ppto` = 1) and (`lp`.`valor_coeficiente_linea_ppto` is not null) and (`lp`.`valor_coeficiente_linea_ppto` > 0)) then (case when (`a`.`permitir_descuentos_articulo` = 1) then ((((`lp`.`precio_unitario_linea_ppto` - ((`lp`.`precio_unitario_linea_ppto` * `p`.`porcentaje_descuento_cliente`) / 100)) * `lp`.`valor_coeficiente_linea_ppto`) * `lp`.`cantidad_linea_ppto`) * (1 - (`lp`.`descuento_linea_ppto` / 100))) else (((`lp`.`precio_unitario_linea_ppto` * `lp`.`valor_coeficiente_linea_ppto`) * `lp`.`cantidad_linea_ppto`) * (1 - (`lp`.`descuento_linea_ppto` / 100))) end) else (case when (`a`.`permitir_descuentos_articulo` = 1) then ((((case when ((`lp`.`fecha_inicio_linea_ppto` is not null) and (`lp`.`fecha_fin_linea_ppto` is not null)) then ((to_days(`lp`.`fecha_fin_linea_ppto`) - to_days(`lp`.`fecha_inicio_linea_ppto`)) + 1) else 1 end) * `lp`.`cantidad_linea_ppto`) * (`lp`.`precio_unitario_linea_ppto` - ((`lp`.`precio_unitario_linea_ppto` * `p`.`porcentaje_descuento_cliente`) / 100))) * (1 - (`lp`.`descuento_linea_ppto` / 100))) else ((((case when ((`lp`.`fecha_inicio_linea_ppto` is not null) and (`lp`.`fecha_fin_linea_ppto` is not null)) then ((to_days(`lp`.`fecha_fin_linea_ppto`) - to_days(`lp`.`fecha_inicio_linea_ppto`)) + 1) else 1 end) * `lp`.`cantidad_linea_ppto`) * `lp`.`precio_unitario_linea_ppto`) * (1 - (`lp`.`descuento_linea_ppto` / 100))) end) end) * `lp`.`porcentaje_iva_linea_ppto`) / 100) AS `importe_iva_linea_ppto_hotel`, (case when ((`lp`.`aplicar_coeficiente_linea_ppto` = 1) and (`lp`.`valor_coeficiente_linea_ppto` is not null) and (`lp`.`valor_coeficiente_linea_ppto` > 0)) then (case when (`a`.`permitir_descuentos_articulo` = 1) then (((((`lp`.`precio_unitario_linea_ppto` - ((`lp`.`precio_unitario_linea_ppto` * `p`.`porcentaje_descuento_cliente`) / 100)) * `lp`.`valor_coeficiente_linea_ppto`) * `lp`.`cantidad_linea_ppto`) * (1 - (`lp`.`descuento_linea_ppto` / 100))) * (1 + (`lp`.`porcentaje_iva_linea_ppto` / 100))) else ((((`lp`.`precio_unitario_linea_ppto` * `lp`.`valor_coeficiente_linea_ppto`) * `lp`.`cantidad_linea_ppto`) * (1 - (`lp`.`descuento_linea_ppto` / 100))) * (1 + (`lp`.`porcentaje_iva_linea_ppto` / 100))) end) else (case when (`a`.`permitir_descuentos_articulo` = 1) then (((((case when ((`lp`.`fecha_inicio_linea_ppto` is not null) and (`lp`.`fecha_fin_linea_ppto` is not null)) then ((to_days(`lp`.`fecha_fin_linea_ppto`) - to_days(`lp`.`fecha_inicio_linea_ppto`)) + 1) else 1 end) * `lp`.`cantidad_linea_ppto`) * (`lp`.`precio_unitario_linea_ppto` - ((`lp`.`precio_unitario_linea_ppto` * `p`.`porcentaje_descuento_cliente`) / 100))) * (1 - (`lp`.`descuento_linea_ppto` / 100))) * (1 + (`lp`.`porcentaje_iva_linea_ppto` / 100))) else (((((case when ((`lp`.`fecha_inicio_linea_ppto` is not null) and (`lp`.`fecha_fin_linea_ppto` is not null)) then ((to_days(`lp`.`fecha_fin_linea_ppto`) - to_days(`lp`.`fecha_inicio_linea_ppto`)) + 1) else 1 end) * `lp`.`cantidad_linea_ppto`) * `lp`.`precio_unitario_linea_ppto`) * (1 - (`lp`.`descuento_linea_ppto` / 100))) * (1 + (`lp`.`porcentaje_iva_linea_ppto` / 100))) end) end) AS `TotalImporte_iva_linea_ppto_hotel`, `a`.`codigo_articulo` AS `codigo_articulo`, `a`.`nombre_articulo` AS `nombre_articulo`, `a`.`name_articulo` AS `name_articulo`, `a`.`imagen_articulo` AS `imagen_articulo`, `a`.`precio_alquiler_articulo` AS `precio_alquiler_articulo`, `a`.`coeficiente_articulo` AS `coeficiente_articulo`, `a`.`es_kit_articulo` AS `es_kit_articulo`, `a`.`control_total_articulo` AS `control_total_articulo`, `a`.`no_facturar_articulo` AS `no_facturar_articulo`, `a`.`notas_presupuesto_articulo` AS `notas_presupuesto_articulo`, `a`.`notes_budget_articulo` AS `notes_budget_articulo`, `a`.`orden_obs_articulo` AS `orden_obs_articulo`, `a`.`observaciones_articulo` AS `observaciones_articulo`, `a`.`activo_articulo` AS `activo_articulo`, `a`.`permitir_descuentos_articulo` AS `permitir_descuentos_articulo`, `a`.`id_familia` AS `id_familia`, `a`.`created_at_articulo` AS `created_at_articulo`, `a`.`updated_at_articulo` AS `updated_at_articulo`, `a`.`id_impuesto` AS `id_impuesto_articulo`, `a`.`tipo_impuesto` AS `tipo_impuesto_articulo`, `a`.`tasa_impuesto` AS `tasa_impuesto_articulo`, `a`.`descr_impuesto` AS `descr_impuesto_articulo`, `a`.`activo_impuesto_relacionado` AS `activo_impuesto_articulo`, `a`.`id_unidad` AS `id_unidad`, `a`.`nombre_unidad` AS `nombre_unidad`, `a`.`name_unidad` AS `name_unidad`, `a`.`descr_unidad` AS `descr_unidad`, `a`.`simbolo_unidad` AS `simbolo_unidad`, `a`.`activo_unidad_relacionada` AS `activo_unidad`, `a`.`id_grupo` AS `id_grupo`, `a`.`codigo_familia` AS `codigo_familia`, `a`.`nombre_familia` AS `nombre_familia`, `a`.`name_familia` AS `name_familia`, `a`.`descr_familia` AS `descr_familia`, `a`.`imagen_familia` AS `imagen_familia`, `a`.`coeficiente_familia` AS `coeficiente_familia`, `a`.`observaciones_presupuesto_familia` AS `observaciones_presupuesto_familia`, `a`.`observations_budget_familia` AS `observations_budget_familia`, `a`.`orden_obs_familia` AS `orden_obs_familia`, `a`.`permite_descuento_familia` AS `permite_descuento_familia`, `a`.`activo_familia_relacionada` AS `activo_familia_relacionada`, `lp`.`id_impuesto` AS `id_impuesto`, `imp`.`tipo_impuesto` AS `tipo_impuesto`, `imp`.`tasa_impuesto` AS `tasa_impuesto`, `imp`.`descr_impuesto` AS `descr_impuesto`, `imp`.`activo_impuesto` AS `activo_impuesto`, `pv`.`id_presupuesto` AS `id_presupuesto`, `pv`.`numero_version_presupuesto` AS `numero_version_presupuesto`, `pv`.`estado_version_presupuesto` AS `estado_version_presupuesto`, `pv`.`fecha_creacion_version` AS `fecha_creacion_version`, `pv`.`fecha_envio_version` AS `fecha_envio_version`, `pv`.`fecha_aprobacion_version` AS `fecha_aprobacion_version`, `p`.`numero_presupuesto` AS `numero_presupuesto`, `p`.`fecha_presupuesto` AS `fecha_presupuesto`, `p`.`fecha_validez_presupuesto` AS `fecha_validez_presupuesto`, `p`.`nombre_evento_presupuesto` AS `nombre_evento_presupuesto`, `p`.`fecha_inicio_evento_presupuesto` AS `fecha_inicio_evento_presupuesto`, `p`.`fecha_fin_evento_presupuesto` AS `fecha_fin_evento_presupuesto`, `p`.`id_cliente` AS `id_cliente`, `p`.`id_estado_ppto` AS `id_estado_ppto`, `p`.`activo_presupuesto` AS `activo_presupuesto`, `p`.`nombre_cliente` AS `nombre_cliente`, `p`.`nif_cliente` AS `nif_cliente`, `p`.`email_cliente` AS `email_cliente`, `p`.`telefono_cliente` AS `telefono_cliente`, `p`.`direccion_cliente` AS `direccion_cliente`, `p`.`cp_cliente` AS `cp_cliente`, `p`.`poblacion_cliente` AS `poblacion_cliente`, `p`.`provincia_cliente` AS `provincia_cliente`, `p`.`porcentaje_descuento_cliente` AS `porcentaje_descuento_cliente`, `p`.`duracion_evento_dias` AS `duracion_evento_dias`, `p`.`dias_hasta_inicio_evento` AS `dias_hasta_inicio_evento`, `p`.`dias_hasta_fin_evento` AS `dias_hasta_fin_evento`, `p`.`estado_evento_presupuesto` AS `estado_evento_presupuesto`, `p`.`prioridad_presupuesto` AS `prioridad_presupuesto`, `p`.`tipo_pago_presupuesto` AS `tipo_pago_presupuesto`, `p`.`descripcion_completa_forma_pago` AS `descripcion_completa_forma_pago`, `p`.`fecha_vencimiento_anticipo` AS `fecha_vencimiento_anticipo`, `p`.`fecha_vencimiento_final` AS `fecha_vencimiento_final`, `p`.`comparacion_descuento` AS `comparacion_descuento`, `p`.`estado_descuento_presupuesto` AS `estado_descuento_presupuesto`, `p`.`aplica_descuento_presupuesto` AS `aplica_descuento_presupuesto`, `p`.`diferencia_descuento` AS `diferencia_descuento`, `p`.`tiene_direccion_facturacion_diferente` AS `tiene_direccion_facturacion_diferente`, `p`.`dias_desde_emision` AS `dias_desde_emision`, `p`.`id_version_actual` AS `id_version_actual`, `p`.`numero_version_actual` AS `numero_version_actual`, `p`.`estado_version_actual` AS `estado_version_actual`, `p`.`fecha_creacion_version_actual` AS `fecha_creacion_version_actual`, `p`.`estado_general_presupuesto` AS `estado_general_presupuesto`, `p`.`mostrar_obs_familias_presupuesto` AS `mostrar_obs_familias_presupuesto`, `p`.`mostrar_obs_articulos_presupuesto` AS `mostrar_obs_articulos_presupuesto`, `cu`.`nombre_ubicacion` AS `nombre_ubicacion`, `cu`.`direccion_ubicacion` AS `direccion_ubicacion`, `cu`.`codigo_postal_ubicacion` AS `codigo_postal_ubicacion`, `cu`.`poblacion_ubicacion` AS `poblacion_ubicacion`, `cu`.`provincia_ubicacion` AS `provincia_ubicacion`, `cu`.`pais_ubicacion` AS `pais_ubicacion`, `cu`.`persona_contacto_ubicacion` AS `persona_contacto_ubicacion`, `cu`.`telefono_contacto_ubicacion` AS `telefono_contacto_ubicacion`, `cu`.`email_contacto_ubicacion` AS `email_contacto_ubicacion`, `cu`.`observaciones_ubicacion` AS `observaciones_ubicacion`, `cu`.`es_principal_ubicacion` AS `es_principal_ubicacion`, `cu`.`activo_ubicacion` AS `activo_ubicacion`, coalesce(`cu`.`nombre_ubicacion`,`p`.`nombre_evento_presupuesto`,'Sin ubicación') AS `ubicacion_agrupacion`, coalesce(concat_ws(', ',`cu`.`nombre_ubicacion`,`cu`.`direccion_ubicacion`,concat(`cu`.`codigo_postal_ubicacion`,' ',`cu`.`poblacion_ubicacion`),`cu`.`provincia_ubicacion`),`p`.`direccion_completa_evento_presupuesto`,'Sin ubicación') AS `ubicacion_completa_agrupacion`, `lp`.`created_at_linea_ppto` AS `created_at_linea_ppto`, `lp`.`updated_at_linea_ppto` AS `updated_at_linea_ppto` FROM ((((((`linea_presupuesto` `lp` join `presupuesto_version` `pv` on((`lp`.`id_version_presupuesto` = `pv`.`id_version_presupuesto`))) join `vista_presupuesto_completa` `p` on((`pv`.`id_presupuesto` = `p`.`id_presupuesto`))) left join `vista_articulo_completa` `a` on((`lp`.`id_articulo` = `a`.`id_articulo`))) left join `coeficiente` `c` on((`lp`.`id_coeficiente` = `c`.`id_coeficiente`))) left join `impuesto` `imp` on((`lp`.`id_impuesto` = `imp`.`id_impuesto`))) left join `cliente_ubicacion` `cu` on((`lp`.`id_ubicacion` = `cu`.`id_ubicacion`))) WHERE (`p`.`activo_presupuesto` = true) ;

-- --------------------------------------------------------

--
-- Estructura para la vista `v_observaciones_presupuesto`
--
DROP TABLE IF EXISTS `v_observaciones_presupuesto`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `v_observaciones_presupuesto`  AS SELECT `p`.`id_presupuesto` AS `id_presupuesto`, `f`.`id_familia` AS `id_familia`, NULL AS `id_articulo`, 'familia' AS `tipo_observacion`, `f`.`codigo_familia` AS `codigo_familia`, `f`.`nombre_familia` AS `nombre_familia`, `f`.`name_familia` AS `name_familia`, `f`.`observaciones_presupuesto_familia` AS `observacion_es`, `f`.`observations_budget_familia` AS `observacion_en`, `f`.`orden_obs_familia` AS `orden_observacion`, `p`.`mostrar_obs_familias_presupuesto` AS `mostrar_observacion`, `p`.`numero_presupuesto` AS `numero_presupuesto`, `p`.`nombre_evento_presupuesto` AS `nombre_evento_presupuesto`, `p`.`id_cliente` AS `id_cliente`, `vp`.`nombre_cliente` AS `nombre_cliente`, `f`.`activo_familia` AS `activo_origen`, `p`.`activo_presupuesto` AS `activo_presupuesto` FROM (((((`presupuesto` `p` join `vista_presupuesto_completa` `vp` on((`p`.`id_presupuesto` = `vp`.`id_presupuesto`))) join `presupuesto_version` `pv` on(((`p`.`id_presupuesto` = `pv`.`id_presupuesto`) and (`pv`.`numero_version_presupuesto` = `p`.`version_actual_presupuesto`)))) join `linea_presupuesto` `lp` on(((`pv`.`id_version_presupuesto` = `lp`.`id_version_presupuesto`) and (`lp`.`activo_linea_ppto` = 1)))) join `articulo` `a` on(((`lp`.`id_articulo` = `a`.`id_articulo`) and (`a`.`activo_articulo` = 1)))) join `familia` `f` on(((`a`.`id_familia` = `f`.`id_familia`) and (`f`.`activo_familia` = 1)))) WHERE ((`p`.`activo_presupuesto` = 1) AND (`p`.`mostrar_obs_familias_presupuesto` = 1) AND ((`f`.`observaciones_presupuesto_familia` is not null) OR (`f`.`observations_budget_familia` is not null))) GROUP BY `p`.`id_presupuesto`, `f`.`id_familia`, `p`.`numero_presupuesto`, `p`.`nombre_evento_presupuesto`, `p`.`id_cliente`, `vp`.`nombre_cliente`, `p`.`mostrar_obs_familias_presupuesto`, `p`.`activo_presupuesto`, `f`.`codigo_familia`, `f`.`nombre_familia`, `f`.`name_familia`, `f`.`observaciones_presupuesto_familia`, `f`.`observations_budget_familia`, `f`.`orden_obs_familia`, `f`.`activo_familia`union all select `p`.`id_presupuesto` AS `id_presupuesto`,NULL AS `id_familia`,`a`.`id_articulo` AS `id_articulo`,'articulo' AS `tipo_observacion`,`a`.`codigo_articulo` AS `codigo_articulo`,`a`.`nombre_articulo` AS `nombre_articulo`,`a`.`name_articulo` AS `name_articulo`,`a`.`notas_presupuesto_articulo` AS `observacion_es`,`a`.`notes_budget_articulo` AS `observacion_en`,`a`.`orden_obs_articulo` AS `orden_observacion`,`p`.`mostrar_obs_articulos_presupuesto` AS `mostrar_observacion`,`p`.`numero_presupuesto` AS `numero_presupuesto`,`p`.`nombre_evento_presupuesto` AS `nombre_evento_presupuesto`,`p`.`id_cliente` AS `id_cliente`,`vp`.`nombre_cliente` AS `nombre_cliente`,`a`.`activo_articulo` AS `activo_origen`,`p`.`activo_presupuesto` AS `activo_presupuesto` from ((((`presupuesto` `p` join `vista_presupuesto_completa` `vp` on((`p`.`id_presupuesto` = `vp`.`id_presupuesto`))) join `presupuesto_version` `pv` on(((`p`.`id_presupuesto` = `pv`.`id_presupuesto`) and (`pv`.`numero_version_presupuesto` = `p`.`version_actual_presupuesto`)))) join `linea_presupuesto` `lp` on(((`pv`.`id_version_presupuesto` = `lp`.`id_version_presupuesto`) and (`lp`.`activo_linea_ppto` = 1)))) join `articulo` `a` on(((`lp`.`id_articulo` = `a`.`id_articulo`) and (`a`.`activo_articulo` = 1)))) where ((`p`.`activo_presupuesto` = 1) and (`p`.`mostrar_obs_articulos_presupuesto` = 1) and ((`a`.`notas_presupuesto_articulo` is not null) or (`a`.`notes_budget_articulo` is not null)) and (`lp`.`mostrar_obs_articulo_linea_ppto` = 1)) group by `p`.`id_presupuesto`,`a`.`id_articulo`,`p`.`numero_presupuesto`,`p`.`nombre_evento_presupuesto`,`p`.`id_cliente`,`vp`.`nombre_cliente`,`p`.`mostrar_obs_articulos_presupuesto`,`p`.`activo_presupuesto`,`a`.`codigo_articulo`,`a`.`nombre_articulo`,`a`.`name_articulo`,`a`.`notas_presupuesto_articulo`,`a`.`notes_budget_articulo`,`a`.`orden_obs_articulo`,`a`.`activo_articulo` order by `id_presupuesto`,`orden_observacion`,`tipo_observacion`  ;

-- --------------------------------------------------------

--
-- Estructura para la vista `v_presupuesto_totales`
--
DROP TABLE IF EXISTS `v_presupuesto_totales`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `v_presupuesto_totales`  AS SELECT `vlpc`.`id_version_presupuesto` AS `id_version_presupuesto`, min(`vlpc`.`numero_version_presupuesto`) AS `numero_version_presupuesto`, min(`vlpc`.`estado_version_presupuesto`) AS `estado_version_presupuesto`, min(`vlpc`.`fecha_creacion_version`) AS `fecha_creacion_version`, min(`vlpc`.`fecha_envio_version`) AS `fecha_envio_version`, min(`vlpc`.`fecha_aprobacion_version`) AS `fecha_aprobacion_version`, min(`vlpc`.`id_presupuesto`) AS `id_presupuesto`, min(`vlpc`.`numero_presupuesto`) AS `numero_presupuesto`, min(`vlpc`.`fecha_presupuesto`) AS `fecha_presupuesto`, min(`vlpc`.`fecha_validez_presupuesto`) AS `fecha_validez_presupuesto`, min(`vlpc`.`nombre_evento_presupuesto`) AS `nombre_evento_presupuesto`, min(`vlpc`.`fecha_inicio_evento_presupuesto`) AS `fecha_inicio_evento_presupuesto`, min(`vlpc`.`fecha_fin_evento_presupuesto`) AS `fecha_fin_evento_presupuesto`, min(`vlpc`.`id_cliente`) AS `id_cliente`, min(`vlpc`.`nombre_cliente`) AS `nombre_cliente`, min(`vlpc`.`nif_cliente`) AS `nif_cliente`, min(`vlpc`.`email_cliente`) AS `email_cliente`, min(`vlpc`.`telefono_cliente`) AS `telefono_cliente`, max(`vlpc`.`duracion_evento_dias`) AS `duracion_evento_dias`, sum(`vlpc`.`base_imponible`) AS `total_base_imponible`, sum(`vlpc`.`importe_iva`) AS `total_iva`, sum(`vlpc`.`total_linea`) AS `total_con_iva`, sum(`vlpc`.`base_imponible_hotel`) AS `total_base_imponible_hotel`, sum(`vlpc`.`importe_descuento_linea_ppto_hotel`) AS `total_importe_descuento_linea_hotel`, sum(`vlpc`.`TotalImporte_descuento_linea_ppto_hotel`) AS `total_despues_descuento_linea_hotel`, sum(`vlpc`.`importe_iva_linea_ppto_hotel`) AS `total_iva_hotel`, sum(`vlpc`.`TotalImporte_iva_linea_ppto_hotel`) AS `total_con_iva_hotel`, count(0) AS `cantidad_lineas_total`, count((case when (`vlpc`.`valor_coeficiente_linea_ppto` is not null) then 1 end)) AS `cantidad_lineas_con_coeficiente`, sum((case when (`vlpc`.`porcentaje_iva_linea_ppto` = 21.00) then `vlpc`.`base_imponible` else 0 end)) AS `base_iva_21`, sum((case when (`vlpc`.`porcentaje_iva_linea_ppto` = 21.00) then `vlpc`.`importe_iva` else 0 end)) AS `importe_iva_21`, sum((case when (`vlpc`.`porcentaje_iva_linea_ppto` = 21.00) then `vlpc`.`total_linea` else 0 end)) AS `total_iva_21`, sum((case when (`vlpc`.`porcentaje_iva_linea_ppto` = 10.00) then `vlpc`.`base_imponible` else 0 end)) AS `base_iva_10`, sum((case when (`vlpc`.`porcentaje_iva_linea_ppto` = 10.00) then `vlpc`.`importe_iva` else 0 end)) AS `importe_iva_10`, sum((case when (`vlpc`.`porcentaje_iva_linea_ppto` = 10.00) then `vlpc`.`total_linea` else 0 end)) AS `total_iva_10`, sum((case when (`vlpc`.`porcentaje_iva_linea_ppto` = 4.00) then `vlpc`.`base_imponible` else 0 end)) AS `base_iva_4`, sum((case when (`vlpc`.`porcentaje_iva_linea_ppto` = 4.00) then `vlpc`.`importe_iva` else 0 end)) AS `importe_iva_4`, sum((case when (`vlpc`.`porcentaje_iva_linea_ppto` = 4.00) then `vlpc`.`total_linea` else 0 end)) AS `total_iva_4`, sum((case when (`vlpc`.`porcentaje_iva_linea_ppto` = 0.00) then `vlpc`.`base_imponible` else 0 end)) AS `base_iva_0`, sum((case when (`vlpc`.`porcentaje_iva_linea_ppto` = 0.00) then `vlpc`.`importe_iva` else 0 end)) AS `importe_iva_0`, sum((case when (`vlpc`.`porcentaje_iva_linea_ppto` = 0.00) then `vlpc`.`total_linea` else 0 end)) AS `total_iva_0`, sum((case when (`vlpc`.`porcentaje_iva_linea_ppto` not in (21.00,10.00,4.00,0.00)) then `vlpc`.`base_imponible` else 0 end)) AS `base_iva_otros`, sum((case when (`vlpc`.`porcentaje_iva_linea_ppto` not in (21.00,10.00,4.00,0.00)) then `vlpc`.`importe_iva` else 0 end)) AS `importe_iva_otros`, sum((case when (`vlpc`.`porcentaje_iva_linea_ppto` not in (21.00,10.00,4.00,0.00)) then `vlpc`.`total_linea` else 0 end)) AS `total_iva_otros`, sum((case when (`vlpc`.`porcentaje_iva_linea_ppto` = 21.00) then `vlpc`.`base_imponible_hotel` else 0 end)) AS `base_iva_21_hotel`, sum((case when (`vlpc`.`porcentaje_iva_linea_ppto` = 21.00) then `vlpc`.`importe_iva_linea_ppto_hotel` else 0 end)) AS `importe_iva_21_hotel`, sum((case when (`vlpc`.`porcentaje_iva_linea_ppto` = 21.00) then `vlpc`.`TotalImporte_iva_linea_ppto_hotel` else 0 end)) AS `total_iva_21_hotel`, sum((case when (`vlpc`.`porcentaje_iva_linea_ppto` = 10.00) then `vlpc`.`base_imponible_hotel` else 0 end)) AS `base_iva_10_hotel`, sum((case when (`vlpc`.`porcentaje_iva_linea_ppto` = 10.00) then `vlpc`.`importe_iva_linea_ppto_hotel` else 0 end)) AS `importe_iva_10_hotel`, sum((case when (`vlpc`.`porcentaje_iva_linea_ppto` = 10.00) then `vlpc`.`TotalImporte_iva_linea_ppto_hotel` else 0 end)) AS `total_iva_10_hotel`, sum((case when (`vlpc`.`porcentaje_iva_linea_ppto` = 4.00) then `vlpc`.`base_imponible_hotel` else 0 end)) AS `base_iva_4_hotel`, sum((case when (`vlpc`.`porcentaje_iva_linea_ppto` = 4.00) then `vlpc`.`importe_iva_linea_ppto_hotel` else 0 end)) AS `importe_iva_4_hotel`, sum((case when (`vlpc`.`porcentaje_iva_linea_ppto` = 4.00) then `vlpc`.`TotalImporte_iva_linea_ppto_hotel` else 0 end)) AS `total_iva_4_hotel`, sum((case when (`vlpc`.`porcentaje_iva_linea_ppto` = 0.00) then `vlpc`.`base_imponible_hotel` else 0 end)) AS `base_iva_0_hotel`, sum((case when (`vlpc`.`porcentaje_iva_linea_ppto` = 0.00) then `vlpc`.`importe_iva_linea_ppto_hotel` else 0 end)) AS `importe_iva_0_hotel`, sum((case when (`vlpc`.`porcentaje_iva_linea_ppto` = 0.00) then `vlpc`.`TotalImporte_iva_linea_ppto_hotel` else 0 end)) AS `total_iva_0_hotel`, sum((case when (`vlpc`.`porcentaje_iva_linea_ppto` not in (21.00,10.00,4.00,0.00)) then `vlpc`.`base_imponible_hotel` else 0 end)) AS `base_iva_otros_hotel`, sum((case when (`vlpc`.`porcentaje_iva_linea_ppto` not in (21.00,10.00,4.00,0.00)) then `vlpc`.`importe_iva_linea_ppto_hotel` else 0 end)) AS `importe_iva_otros_hotel`, sum((case when (`vlpc`.`porcentaje_iva_linea_ppto` not in (21.00,10.00,4.00,0.00)) then `vlpc`.`TotalImporte_iva_linea_ppto_hotel` else 0 end)) AS `total_iva_otros_hotel`, sum((`vlpc`.`subtotal_sin_coeficiente` - `vlpc`.`base_imponible`)) AS `ahorro_total_coeficientes`, (sum(`vlpc`.`base_imponible`) - sum(`vlpc`.`base_imponible_hotel`)) AS `diferencia_base_imponible_cliente`, (sum(`vlpc`.`total_linea`) - sum(`vlpc`.`TotalImporte_iva_linea_ppto_hotel`)) AS `diferencia_total_con_iva_cliente`, min(`vlpc`.`created_at_linea_ppto`) AS `fecha_primera_linea_creada`, max(`vlpc`.`updated_at_linea_ppto`) AS `fecha_ultima_modificacion_linea` FROM `v_linea_presupuesto_calculada` AS `vlpc` GROUP BY `vlpc`.`id_version_presupuesto` ;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `adjunto_llamada`
--
ALTER TABLE `adjunto_llamada`
  ADD CONSTRAINT `fk_id_llamada` FOREIGN KEY (`id_llamada`) REFERENCES `llamadas` (`id_llamada`);

--
-- Filtros para la tabla `articulo`
--
ALTER TABLE `articulo`
  ADD CONSTRAINT `fk_articulo_familia` FOREIGN KEY (`id_familia`) REFERENCES `familia` (`id_familia`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_articulo_impuesto` FOREIGN KEY (`id_impuesto`) REFERENCES `impuesto` (`id_impuesto`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_articulo_unidad` FOREIGN KEY (`id_unidad`) REFERENCES `unidad_medida` (`id_unidad`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `cliente`
--
ALTER TABLE `cliente`
  ADD CONSTRAINT `fk_cliente_forma_pago_habitual` FOREIGN KEY (`id_forma_pago_habitual`) REFERENCES `forma_pago` (`id_pago`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `cliente_ubicacion`
--
ALTER TABLE `cliente_ubicacion`
  ADD CONSTRAINT `fk_ubicacion_cliente` FOREIGN KEY (`id_cliente`) REFERENCES `cliente` (`id_cliente`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `comerciales`
--
ALTER TABLE `comerciales`
  ADD CONSTRAINT `fk_comerciales_usuarios` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `com_vacaciones`
--
ALTER TABLE `com_vacaciones`
  ADD CONSTRAINT `fk_vacaciones_comercial` FOREIGN KEY (`id_comercial`) REFERENCES `comerciales` (`id_comercial`) ON DELETE CASCADE;

--
-- Filtros para la tabla `contactos`
--
ALTER TABLE `contactos`
  ADD CONSTRAINT `contactos_ibfk_1` FOREIGN KEY (`id_llamada`) REFERENCES `llamadas` (`id_llamada`),
  ADD CONSTRAINT `fk_contactos_visitas_cerradas` FOREIGN KEY (`id_visita_cerrada`) REFERENCES `visitas_cerradas` (`id_visita_cerrada`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_id_metodo` FOREIGN KEY (`id_metodo`) REFERENCES `metodos_contacto` (`id_metodo`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Filtros para la tabla `contacto_cliente`
--
ALTER TABLE `contacto_cliente`
  ADD CONSTRAINT `fk_contacto_cliente` FOREIGN KEY (`id_cliente`) REFERENCES `cliente` (`id_cliente`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `contacto_proveedor`
--
ALTER TABLE `contacto_proveedor`
  ADD CONSTRAINT `fk_contacto_proveedor` FOREIGN KEY (`id_proveedor`) REFERENCES `proveedor` (`id_proveedor`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `documento`
--
ALTER TABLE `documento`
  ADD CONSTRAINT `documento_ibfk_1` FOREIGN KEY (`id_tipo_documento_documento`) REFERENCES `tipo_documento` (`id_tipo_documento`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Filtros para la tabla `documento_elemento`
--
ALTER TABLE `documento_elemento`
  ADD CONSTRAINT `fk_documento_elemento` FOREIGN KEY (`id_elemento`) REFERENCES `elemento` (`id_elemento`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `elemento`
--
ALTER TABLE `elemento`
  ADD CONSTRAINT `fk_elemento_articulo` FOREIGN KEY (`id_articulo_elemento`) REFERENCES `articulo` (`id_articulo`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_elemento_estado` FOREIGN KEY (`id_estado_elemento`) REFERENCES `estado_elemento` (`id_estado_elemento`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_elemento_forma_pago_alquiler` FOREIGN KEY (`id_forma_pago_alquiler_elemento`) REFERENCES `forma_pago` (`id_pago`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_elemento_marca` FOREIGN KEY (`id_marca_elemento`) REFERENCES `marca` (`id_marca`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_elemento_proveedor_alquiler` FOREIGN KEY (`id_proveedor_alquiler_elemento`) REFERENCES `proveedor` (`id_proveedor`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_elemento_proveedor_compra` FOREIGN KEY (`id_proveedor_compra_elemento`) REFERENCES `proveedor` (`id_proveedor`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `familia`
--
ALTER TABLE `familia`
  ADD CONSTRAINT `fk_familia_grupo` FOREIGN KEY (`id_grupo`) REFERENCES `grupo_articulo` (`id_grupo`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `forma_pago`
--
ALTER TABLE `forma_pago`
  ADD CONSTRAINT `fk_forma_pago_metodo` FOREIGN KEY (`id_metodo_pago`) REFERENCES `metodo_pago` (`id_metodo_pago`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Filtros para la tabla `foto_elemento`
--
ALTER TABLE `foto_elemento`
  ADD CONSTRAINT `fk_foto_elemento` FOREIGN KEY (`id_elemento`) REFERENCES `elemento` (`id_elemento`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `furgoneta_mantenimiento`
--
ALTER TABLE `furgoneta_mantenimiento`
  ADD CONSTRAINT `fk_mantenimiento_furgoneta` FOREIGN KEY (`id_furgoneta`) REFERENCES `furgoneta` (`id_furgoneta`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `furgoneta_registro_kilometraje`
--
ALTER TABLE `furgoneta_registro_kilometraje`
  ADD CONSTRAINT `fk_registro_km_furgoneta` FOREIGN KEY (`id_furgoneta`) REFERENCES `furgoneta` (`id_furgoneta`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `kit`
--
ALTER TABLE `kit`
  ADD CONSTRAINT `fk_kit_articulo_componente` FOREIGN KEY (`id_articulo_componente`) REFERENCES `articulo` (`id_articulo`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_kit_articulo_maestro` FOREIGN KEY (`id_articulo_maestro`) REFERENCES `articulo` (`id_articulo`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `linea_presupuesto`
--
ALTER TABLE `linea_presupuesto`
  ADD CONSTRAINT `fk_linea_ppto_articulo` FOREIGN KEY (`id_articulo`) REFERENCES `articulo` (`id_articulo`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_linea_ppto_coeficiente` FOREIGN KEY (`id_coeficiente`) REFERENCES `coeficiente` (`id_coeficiente`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_linea_ppto_impuesto` FOREIGN KEY (`id_impuesto`) REFERENCES `impuesto` (`id_impuesto`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_linea_ppto_linea_padre` FOREIGN KEY (`id_linea_padre`) REFERENCES `linea_presupuesto` (`id_linea_ppto`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_linea_ppto_ubicacion` FOREIGN KEY (`id_ubicacion`) REFERENCES `cliente_ubicacion` (`id_ubicacion`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_linea_ppto_version` FOREIGN KEY (`id_version_presupuesto`) REFERENCES `presupuesto_version` (`id_version_presupuesto`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `presupuesto`
--
ALTER TABLE `presupuesto`
  ADD CONSTRAINT `fk_presupuesto_cliente` FOREIGN KEY (`id_cliente`) REFERENCES `cliente` (`id_cliente`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_presupuesto_contacto` FOREIGN KEY (`id_contacto_cliente`) REFERENCES `contacto_cliente` (`id_contacto_cliente`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_presupuesto_empresa` FOREIGN KEY (`id_empresa`) REFERENCES `empresa` (`id_empresa`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_presupuesto_estado` FOREIGN KEY (`id_estado_ppto`) REFERENCES `estado_presupuesto` (`id_estado_ppto`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_presupuesto_forma_pago` FOREIGN KEY (`id_forma_pago`) REFERENCES `forma_pago` (`id_pago`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_presupuesto_metodo_contacto` FOREIGN KEY (`id_metodo`) REFERENCES `metodos_contacto` (`id_metodo`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `presupuesto_version`
--
ALTER TABLE `presupuesto_version`
  ADD CONSTRAINT `fk_version_padre` FOREIGN KEY (`version_padre_presupuesto`) REFERENCES `presupuesto_version` (`id_version_presupuesto`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_version_presupuesto` FOREIGN KEY (`id_presupuesto`) REFERENCES `presupuesto` (`id_presupuesto`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `proveedor`
--
ALTER TABLE `proveedor`
  ADD CONSTRAINT `fk_proveedor_forma_pago_habitual` FOREIGN KEY (`id_forma_pago_habitual`) REFERENCES `forma_pago` (`id_pago`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id_rol`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Filtros para la tabla `visitas_cerradas`
--
ALTER TABLE `visitas_cerradas`
  ADD CONSTRAINT `fk_visitas_llamadas` FOREIGN KEY (`id_llamada`) REFERENCES `llamadas` (`id_llamada`) ON UPDATE CASCADE,
  ADD CONSTRAINT `visitas_cerradas_ibfk_1` FOREIGN KEY (`id_contacto`) REFERENCES `contactos` (`id_contacto`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
