<?php

require_once '../config/conexion.php';
require_once '../config/funciones.php';

/**
 * Clase ImpresionPresupuesto
 * 
 * Modelo para gestionar la impresión de presupuestos
 * Obtiene datos de cabecera y pie para generar documentos imprimibles
 * 
 * @author Luis - Innovabyte
 * @version 1.0
 * @date 2026-02-05
 */
class ImpresionPresupuesto
{
    /** @var PDO Conexión a la base de datos */
    private $conexion;
    
    /** @var RegistroActividad Sistema de logging */
    private $registro;

    /**
     * Constructor - Inicializa conexión PDO y sistema de logging
     */
    public function __construct()
    {
        // 1. Inicializar conexión PDO
        /** @var Conexion $conexionObj */
        $conexionObj = new Conexion();
        $this->conexion = $conexionObj->getConexion();
        
        // 2. Inicializar registro de actividad
        $this->registro = new RegistroActividad();
        
        // 3. Configurar zona horaria Europe/Madrid
        try {
            $this->conexion->exec("SET time_zone = 'Europe/Madrid'");
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'system',
                'ImpresionPresupuesto',
                '__construct',
                "Error al configurar zona horaria: " . $e->getMessage(),
                'warning'
            );
        }
    }

    /**
     * Obtener datos de cabecera del presupuesto
     * 
     * Obtiene los datos de la versión ACTUAL del presupuesto especificado
     * usando la vista v_presupuesto_totales que incluye:
     * - Datos del presupuesto (número, fechas, evento)
     * - Datos del cliente (nombre, NIF, contacto)
     * - Totales calculados
     * 
     * @param int $id_presupuesto ID del presupuesto
     * @return array|false Datos del presupuesto o false si hay error
     */
    public function get_datos_cabecera($id_presupuesto)
    {
        try {
            // Obtener datos del presupuesto con los datos del cliente
            // Similar a vista_presupuesto_completa pero solo para un presupuesto específico
            $sql = "SELECT 
                        p.id_presupuesto,
                        p.numero_presupuesto,
                        p.numero_pedido_cliente_presupuesto,
                        p.fecha_presupuesto,
                        p.fecha_validez_presupuesto,
                        p.nombre_evento_presupuesto,
                        p.fecha_inicio_evento_presupuesto,
                        p.fecha_fin_evento_presupuesto,
                        p.direccion_evento_presupuesto,
                        p.poblacion_evento_presupuesto,
                        p.cp_evento_presupuesto,
                        p.provincia_evento_presupuesto,
                        p.observaciones_cabecera_presupuesto,
                        p.observaciones_pie_presupuesto,
                        p.version_actual_presupuesto,
                        DATEDIFF(p.fecha_fin_evento_presupuesto, p.fecha_inicio_evento_presupuesto) + 1 AS duracion_evento_dias,
                        
                        -- Datos del cliente
                        c.id_cliente,
                        c.nombre_cliente,
                        c.nif_cliente,
                        c.email_cliente,
                        c.telefono_cliente,
                        c.direccion_cliente,
                        c.cp_cliente,
                        c.poblacion_cliente,
                        c.provincia_cliente,
                        
                        -- Datos del contacto del cliente
                        cc.id_contacto_cliente,
                        cc.nombre_contacto_cliente,
                        cc.apellidos_contacto_cliente,
                        cc.telefono_contacto_cliente,
                        cc.email_contacto_cliente,
                        
                        -- Datos de la versión actual
                        pv.id_version_presupuesto,
                        pv.numero_version_presupuesto,
                        pv.estado_version_presupuesto,
                        pv.fecha_creacion_version,
                        pv.fecha_envio_version,
                        pv.fecha_aprobacion_version,
                        
                        -- Datos de la forma de pago
                        fp.id_pago,
                        fp.codigo_pago,
                        fp.nombre_pago,
                        fp.porcentaje_anticipo_pago,
                        fp.dias_anticipo_pago,
                        fp.porcentaje_final_pago,
                        fp.dias_final_pago,
                        fp.descuento_pago,
                        
                        -- Datos del método de pago
                        mp.id_metodo_pago,
                        mp.codigo_metodo_pago,
                        mp.nombre_metodo_pago
                        
                    FROM presupuesto p
                    INNER JOIN cliente c 
                        ON p.id_cliente = c.id_cliente
                    LEFT JOIN contacto_cliente cc
                        ON p.id_contacto_cliente = cc.id_contacto_cliente
                    LEFT JOIN presupuesto_version pv
                        ON p.id_presupuesto = pv.id_presupuesto
                        AND pv.numero_version_presupuesto = p.version_actual_presupuesto
                    LEFT JOIN forma_pago fp
                        ON p.id_forma_pago = fp.id_pago
                    LEFT JOIN metodo_pago mp
                        ON fp.id_metodo_pago = mp.id_metodo_pago
                    WHERE p.id_presupuesto = ?
                    AND p.activo_presupuesto = 1
                    LIMIT 1";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_presupuesto, PDO::PARAM_INT);
            $stmt->execute();
            
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($resultado) {
                $this->registro->registrarActividad(
                    'admin',
                    'ImpresionPresupuesto',
                    'get_datos_cabecera',
                    "Datos obtenidos para presupuesto ID: $id_presupuesto, Versión: " . ($resultado['numero_version_presupuesto'] ?? 'N/A'),
                    'info'
                );
            } else {
                $this->registro->registrarActividad(
                    'admin',
                    'ImpresionPresupuesto',
                    'get_datos_cabecera',
                    "No se encontraron datos para presupuesto ID: $id_presupuesto",
                    'warning'
                );
            }
            
            return $resultado;
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'ImpresionPresupuesto',
                'get_datos_cabecera',
                "Error al obtener datos de cabecera: " . $e->getMessage(),
                'error'
            );
            return false;
        }
    }

    /**
     * Obtener datos completos de la empresa para impresión
     * 
     * Obtiene todos los datos necesarios de la empresa ficticia principal
     * que se utiliza para facturación/presupuestos, incluyendo:
     * - Datos fiscales (nombre, NIF, dirección)
     * - Contacto (teléfono, email)
     * - Logo y configuración de presupuestos
     * - Textos legales para pie de documento
     * 
     * @return array|false Datos completos de la empresa o false si hay error
     */
    public function get_empresa_datos()
    {
        try {
            $sql = "SELECT 
                        id_empresa,
                        codigo_empresa,
                        nombre_empresa,
                        nombre_comercial_empresa,
                        nif_empresa,
                        direccion_fiscal_empresa,
                        cp_fiscal_empresa,
                        poblacion_fiscal_empresa,
                        provincia_fiscal_empresa,
                        pais_fiscal_empresa,
                        telefono_empresa,
                        movil_empresa,
                        email_empresa,
                        web_empresa,
                        logotipo_empresa,
                        logotipo_pie_empresa,
                        serie_presupuesto_empresa,
                        numero_actual_presupuesto_empresa,
                        dias_validez_presupuesto_empresa,
                        texto_pie_presupuesto_empresa
                    FROM empresa 
                    WHERE empresa_ficticia_principal = 1 
                    AND activo_empresa = 1
                    LIMIT 1";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();
            
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($resultado) {
                $this->registro->registrarActividad(
                    'admin',
                    'ImpresionPresupuesto',
                    'get_empresa_datos',
                    "Datos de empresa obtenidos: " . $resultado['nombre_comercial_empresa'],
                    'info'
                );
            } else {
                $this->registro->registrarActividad(
                    'admin',
                    'ImpresionPresupuesto',
                    'get_empresa_datos',
                    "No se encontró empresa ficticia principal activa",
                    'warning'
                );
            }
            
            return $resultado;
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'ImpresionPresupuesto',
                'get_empresa_datos',
                "Error al obtener datos de empresa: " . $e->getMessage(),
                'error'
            );
            return false;
        }
    }

    /**
     * Verificar si existe físicamente el archivo de logo
     * 
     * Valida que el archivo de logo especificado en la BD existe
     * físicamente en el servidor
     * 
     * @param string $ruta_logo Ruta relativa del logo desde public/img/
     * @return bool true si existe, false si no existe
     */
    public function validar_logo($ruta_logo)
    {
        if (empty($ruta_logo)) {
            return false;
        }
        
        // Limpiar la ruta eliminando prefijos redundantes
        $ruta_limpia = $this->limpiar_ruta_logo($ruta_logo);
        
        // Construir ruta absoluta desde el directorio del script
        $ruta_absoluta = __DIR__ . '/../public/img/' . $ruta_limpia;
        
        $existe = file_exists($ruta_absoluta);
        
        if (!$existe) {
            $this->registro->registrarActividad(
                'admin',
                'ImpresionPresupuesto',
                'validar_logo',
                "Logo no encontrado: $ruta_logo (Ruta absoluta: $ruta_absoluta)",
                'warning'
            );
        }
        
        return $existe;
    }

    /**
     * Obtener URL relativa del logo para usar en HTML
     * 
     * Convierte la ruta del logo de la BD en una URL relativa
     * válida para usar en etiquetas <img> desde el controller
     * 
     * @param string $ruta_logo Ruta del logo desde la BD
     * @return string URL relativa del logo
     */
    public function get_url_logo($ruta_logo)
    {
        if (empty($ruta_logo)) {
            return '';
        }
        
        // Limpiar la ruta eliminando prefijos redundantes
        $ruta_limpia = $this->limpiar_ruta_logo($ruta_logo);
        
        // Retornar ruta relativa desde el controller (un nivel arriba)
        return '../public/img/' . $ruta_limpia;
    }
    
    /**
     * Limpia la ruta del logo eliminando prefijos redundantes
     * 
     * Ejemplos:
     * - "/public/img/logo/Logo2.png" -> "logo/Logo2.png"
     * - "public/img/logo/Logo2.png" -> "logo/Logo2.png"
     * - "/images/logos/mdr_group_logo.png" -> "logos/mdr_group_logo.png"
     * - "logo/Logo2.png" -> "logo/Logo2.png"
     * 
     * @param string $ruta Ruta del logo desde la BD
     * @return string Ruta limpia sin prefijos redundantes
     */
    private function limpiar_ruta_logo($ruta)
    {
        // Guardar ruta original para logging
        $ruta_original = $ruta;
        
        // Convertir barras invertidas a barras normales
        $ruta = str_replace('\\', '/', $ruta);
        
        // Eliminar prefijos comunes que podrían venir de la BD
        $prefijos = [
            '/public/img/',
            'public/img/',
            '/public/',
            'public/',
            '../public/img/',
            '../../public/img/',
            '/images/',
            'images/',
            '/img/',
            'img/'
        ];
        
        foreach ($prefijos as $prefijo) {
            if (stripos($ruta, $prefijo) === 0) {
                $ruta = substr($ruta, strlen($prefijo));
                break;
            }
        }
        
        // Eliminar barra inicial si existe
        $ruta = ltrim($ruta, '/');
        
        $this->registro->registrarActividad(
            'admin',
            'ImpresionPresupuesto',
            'limpiar_ruta_logo',
            "Ruta limpiada: original=\"$ruta_original\" limpia=\"$ruta\"",
            'info'
        );
        
        return $ruta;
    }

    /**
     * Obtener líneas del presupuesto para impresión
     * 
     * Obtiene todas las líneas de la versión actual del presupuesto
     * desde la vista v_linea_presupuesto_calculada, ordenadas por:
     * 1. fecha_inicio_linea_ppto
     * 2. id_ubicacion
     * 3. nombre_articulo
     * 
     * @param int $id_presupuesto ID del presupuesto
     * @return array|false Array de líneas o false si hay error
     */
    public function get_lineas_impresion($id_presupuesto)
    {
        try {
            $sql = "SELECT 
                        vlpc.id_linea_ppto,
                        vlpc.fecha_inicio_linea_ppto,
                        vlpc.fecha_fin_linea_ppto,
                        vlpc.dias_linea,
                        vlpc.id_ubicacion,
                        vlpc.nombre_ubicacion,
                        vlpc.ubicacion_completa_agrupacion,
                        vlpc.nombre_articulo,
                        vlpc.codigo_articulo,
                        vlpc.id_articulo,
                        vlpc.es_kit_articulo,
                        vlpc.ocultar_detalle_kit_linea_ppto,
                        vlpc.cantidad_linea_ppto,
                        vlpc.precio_unitario_linea_ppto,
                        vlpc.descuento_linea_ppto,
                        vlpc.porcentaje_iva_linea_ppto,
                        vlpc.valor_coeficiente_linea_ppto,
                        vlpc.base_imponible,
                        vlpc.importe_iva,
                        vlpc.total_linea,
                        vlpc.tipo_linea_ppto,
                        vlpc.nivel_jerarquia,
                        vlpc.descripcion_linea_ppto
                    FROM v_linea_presupuesto_calculada vlpc
                    WHERE vlpc.id_presupuesto = ?
                    AND vlpc.numero_version_presupuesto = (
                        SELECT version_actual_presupuesto 
                        FROM presupuesto 
                        WHERE id_presupuesto = ?
                    )
                    AND vlpc.activo_linea_ppto = 1
                    AND vlpc.mostrar_en_presupuesto = 1
                    ORDER BY 
                        vlpc.fecha_inicio_linea_ppto ASC,
                        vlpc.id_ubicacion ASC,
                        vlpc.nombre_articulo ASC";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_presupuesto, PDO::PARAM_INT);
            $stmt->bindValue(2, $id_presupuesto, PDO::PARAM_INT);
            $stmt->execute();
            
            $lineas = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $this->registro->registrarActividad(
                'admin',
                'ImpresionPresupuesto',
                'get_lineas_impresion',
                "Líneas obtenidas para presupuesto ID: $id_presupuesto - Total: " . count($lineas),
                'info'
            );
            
            return $lineas;
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'ImpresionPresupuesto',
                'get_lineas_impresion',
                "Error al obtener líneas: " . $e->getMessage(),
                'error'
            );
            return false;
        }
    }

    /**
     * Obtener observaciones de familias y artículos del presupuesto
     * 
     * Consulta la vista v_observaciones_presupuesto que consolida
     * las observaciones de familias y artículos asociados al presupuesto,
     * respetando los flags de visibilidad configurados.
     * 
     * @param int $id_presupuesto ID del presupuesto
     * @param string $idioma Idioma de las observaciones ('es' o 'en')
     * @return array Array de observaciones ordenadas (familias primero, luego artículos)
     */
    public function get_observaciones_presupuesto($id_presupuesto, $idioma = 'es')
    {
        try {
            // NO usar v_observaciones_presupuesto porque depende de vista_presupuesto_completa
            // que puede filtrar presupuestos. Usar consulta directa que sabemos que funciona.
            $sql = "
            -- Observaciones de FAMILIAS
            SELECT 
                'familia' AS tipo_observacion,
                f.nombre_familia,
                NULL AS nombre_articulo,
                f.observaciones_presupuesto_familia AS observacion_es,
                f.orden_obs_familia AS orden_observacion
            FROM presupuesto p
            JOIN presupuesto_version pv ON p.id_presupuesto = pv.id_presupuesto 
                AND pv.numero_version_presupuesto = p.version_actual_presupuesto
            JOIN linea_presupuesto lp ON pv.id_version_presupuesto = lp.id_version_presupuesto
                AND lp.activo_linea_ppto = 1
            JOIN articulo a ON lp.id_articulo = a.id_articulo
                AND a.activo_articulo = 1
            JOIN familia f ON a.id_familia = f.id_familia
                AND f.activo_familia = 1
            WHERE p.id_presupuesto = ?
              AND p.activo_presupuesto = 1
              AND p.mostrar_obs_familias_presupuesto = 1
              AND f.observaciones_presupuesto_familia IS NOT NULL
              AND TRIM(f.observaciones_presupuesto_familia) != ''
            GROUP BY f.id_familia, f.nombre_familia, f.observaciones_presupuesto_familia, f.orden_obs_familia
            
            UNION ALL
            
            -- Observaciones de ARTÍCULOS
            SELECT 
                'articulo' AS tipo_observacion,
                NULL AS nombre_familia,
                a.nombre_articulo,
                a.notas_presupuesto_articulo AS observacion_es,
                a.orden_obs_articulo AS orden_observacion
            FROM presupuesto p
            JOIN presupuesto_version pv ON p.id_presupuesto = pv.id_presupuesto 
                AND pv.numero_version_presupuesto = p.version_actual_presupuesto
            JOIN linea_presupuesto lp ON pv.id_version_presupuesto = lp.id_version_presupuesto
                AND lp.activo_linea_ppto = 1
            JOIN articulo a ON lp.id_articulo = a.id_articulo
                AND a.activo_articulo = 1
            WHERE p.id_presupuesto = ?
              AND p.activo_presupuesto = 1
              AND p.mostrar_obs_articulos_presupuesto = 1
              AND a.notas_presupuesto_articulo IS NOT NULL
              AND TRIM(a.notas_presupuesto_articulo) != ''
              AND lp.mostrar_obs_articulo_linea_ppto = 1
            GROUP BY a.id_articulo, a.nombre_articulo, a.notas_presupuesto_articulo, a.orden_obs_articulo
            
            ORDER BY orden_observacion, tipo_observacion";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_presupuesto, PDO::PARAM_INT);
            $stmt->bindValue(2, $id_presupuesto, PDO::PARAM_INT);
            $stmt->execute();
            
            $observaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $this->registro->registrarActividad(
                'admin',
                'ImpresionPresupuesto',
                'get_observaciones_presupuesto',
                "Observaciones obtenidas (consulta directa) para presupuesto ID: $id_presupuesto - Total: " . count($observaciones),
                'info'
            );
            
            return $observaciones;
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'ImpresionPresupuesto',
                'get_observaciones_presupuesto',
                "Error al obtener observaciones: " . $e->getMessage(),
                'error'
            );
            return [];
        }
    }
}
?>
