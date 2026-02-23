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
    public function get_datos_cabecera($id_presupuesto, $numero_version = null)
    {
        try {
            // Obtener datos del presupuesto con los datos del cliente
            // Similar a vista_presupuesto_completa pero solo para un presupuesto específica
            // Condición de versión: si se proporciona, usar esa; si no, la versión actual
            $pv_version_condition = is_null($numero_version) ? "p.version_actual_presupuesto" : "?";
            $sql = "SELECT 
                        p.id_presupuesto,
                        p.id_empresa,
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
                        p.destacar_observaciones_pie_presupuesto,
                        p.version_actual_presupuesto,
                        p.descuento_presupuesto,
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
                        c.exento_iva_cliente,
                        c.justificacion_exencion_iva_cliente,
                        
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
                        AND pv.numero_version_presupuesto = $pv_version_condition
                    LEFT JOIN forma_pago fp
                        ON p.id_forma_pago = fp.id_pago
                    LEFT JOIN metodo_pago mp
                        ON fp.id_metodo_pago = mp.id_metodo_pago
                    WHERE p.id_presupuesto = ?
                    LIMIT 1";
            
            $stmt = $this->conexion->prepare($sql);
            if (!is_null($numero_version)) {
                $stmt->bindValue(1, $numero_version, PDO::PARAM_INT);
                $stmt->bindValue(2, $id_presupuesto, PDO::PARAM_INT);
            } else {
                $stmt->bindValue(1, $id_presupuesto, PDO::PARAM_INT);
            }
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
                        iban_empresa,
                        swift_empresa,
                        banco_empresa,
                        logotipo_empresa,
                        logotipo_pie_empresa,
                        serie_presupuesto_empresa,
                        numero_actual_presupuesto_empresa,
                        dias_validez_presupuesto_empresa,
                        texto_pie_presupuesto_empresa,
                        mostrar_subtotales_fecha_presupuesto_empresa,
                    cabecera_firma_presupuesto_empresa,
                    mostrar_cuenta_bancaria_pdf_presupuesto_empresa,
                    mostrar_kits_albaran_empresa,
                    mostrar_obs_familias_articulos_albaran_empresa,
                    mostrar_obs_pie_albaran_empresa,
                    obs_linea_alineadas_descripcion_empresa,
                    permitir_descuentos_lineas_empresa
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
    public function get_lineas_impresion($id_presupuesto, $numero_version = null)
    {
        try {
            // Condición de versión: si se proporciona, usar esa; si no, subquery a la actual
            $version_sql_condition = is_null($numero_version)
                ? "(SELECT version_actual_presupuesto FROM presupuesto WHERE id_presupuesto = ?)"
                : "?";
            $sql = "SELECT 
                        vlpc.id_linea_ppto,
                        vlpc.fecha_inicio_linea_ppto,
                        vlpc.fecha_fin_linea_ppto,
                        vlpc.fecha_montaje_linea_ppto,
                        vlpc.fecha_desmontaje_linea_ppto,
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
                        vlpc.descripcion_linea_ppto,
                        vlpc.observaciones_linea_ppto
                    FROM v_linea_presupuesto_calculada vlpc
                    WHERE vlpc.id_presupuesto = ?
                    AND vlpc.numero_version_presupuesto = $version_sql_condition
                    AND vlpc.activo_linea_ppto = 1
                    AND vlpc.mostrar_en_presupuesto = 1
                    ORDER BY 
                        vlpc.fecha_inicio_linea_ppto ASC,
                        vlpc.id_ubicacion ASC,
                        vlpc.nombre_articulo ASC";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute(is_null($numero_version)
                ? [$id_presupuesto, $id_presupuesto]
                : [$id_presupuesto, $numero_version]);
            
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
     * Obtener líneas agrupadas del presupuesto con totales calculados
     * 
     * Agrupa las líneas del presupuesto por:
     * 1. Fecha de inicio
     * 2. Ubicación
     * 
     * Calcula subtotales por ubicación, fecha y totales generales
     * Genera desglose de IVA por porcentaje
     * 
     * @param int $id_presupuesto ID del presupuesto
     * @return array|false Array con estructura:
     *   - lineas_agrupadas: Array agrupado por fecha y ubicación
     *   - totales_generales: subtotal, total_iva, total
     *   - desglose_iva: Array con base_imponible e importe_iva por % IVA
     */
    public function get_lineas_agrupadas($id_presupuesto, $numero_version = null)
    {
        try {
            // 1. Obtener líneas del presupuesto
            $lineas = $this->get_lineas_impresion($id_presupuesto, $numero_version);
            
            if ($lineas === false) {
                throw new Exception("Error al obtener las líneas del presupuesto");
            }
            
            // 2. Inicializar estructuras de datos
            $lineas_agrupadas = [];
            $totales_generales = [
                'subtotal' => 0,
                'total_iva' => 0,
                'total' => 0
            ];
            $desglose_iva = [];
            
            // 3. Agrupar líneas por fecha de inicio y ubicación
            foreach ($lineas as $linea) {
                $fecha_inicio = $linea['fecha_inicio_linea_ppto'];
                $id_ubicacion = $linea['id_ubicacion'] ?? 0;
                
                // Inicializar fecha si no existe
                if (!isset($lineas_agrupadas[$fecha_inicio])) {
                    $lineas_agrupadas[$fecha_inicio] = [
                        'ubicaciones' => [],
                        'subtotal_fecha' => 0,
                        'total_iva_fecha' => 0,
                        'total_fecha' => 0
                    ];
                }
                
                // Inicializar ubicación si no existe
                if (!isset($lineas_agrupadas[$fecha_inicio]['ubicaciones'][$id_ubicacion])) {
                    $lineas_agrupadas[$fecha_inicio]['ubicaciones'][$id_ubicacion] = [
                        'nombre_ubicacion' => $linea['nombre_ubicacion'] ?? 'Sin ubicación',
                        'ubicacion_completa' => $linea['ubicacion_completa_agrupacion'] ?? '',
                        'lineas' => [],
                        'subtotal_ubicacion' => 0,
                        'total_iva_ubicacion' => 0,
                        'total_ubicacion' => 0
                    ];
                }
                
                // Añadir línea al grupo
                $lineas_agrupadas[$fecha_inicio]['ubicaciones'][$id_ubicacion]['lineas'][] = $linea;
                
                // Acumular subtotales de ubicación
                $base = floatval($linea['base_imponible'] ?? 0);
                $iva = floatval($linea['importe_iva'] ?? 0);
                $total = floatval($linea['total_linea'] ?? 0);
                
                $lineas_agrupadas[$fecha_inicio]['ubicaciones'][$id_ubicacion]['subtotal_ubicacion'] += $base;
                $lineas_agrupadas[$fecha_inicio]['ubicaciones'][$id_ubicacion]['total_iva_ubicacion'] += $iva;
                $lineas_agrupadas[$fecha_inicio]['ubicaciones'][$id_ubicacion]['total_ubicacion'] += $total;
                
                // Acumular subtotales de fecha
                $lineas_agrupadas[$fecha_inicio]['subtotal_fecha'] += $base;
                $lineas_agrupadas[$fecha_inicio]['total_iva_fecha'] += $iva;
                $lineas_agrupadas[$fecha_inicio]['total_fecha'] += $total;
                
                // Acumular totales generales
                $totales_generales['subtotal'] += $base;
                $totales_generales['total_iva'] += $iva;
                $totales_generales['total'] += $total;
                
                // Agrupar por porcentaje de IVA para desglose
                $porcentaje_iva = floatval($linea['porcentaje_iva_linea_ppto'] ?? 0);
                if (!isset($desglose_iva[$porcentaje_iva])) {
                    $desglose_iva[$porcentaje_iva] = [
                        'base_imponible' => 0,
                        'importe_iva' => 0
                    ];
                }
                $desglose_iva[$porcentaje_iva]['base_imponible'] += $base;
                $desglose_iva[$porcentaje_iva]['importe_iva'] += $iva;
            }
            
            // 4. Ordenar desglose de IVA por porcentaje
            ksort($desglose_iva);
            
            // 5. Registrar actividad
            $this->registro->registrarActividad(
                'admin',
                'ImpresionPresupuesto',
                'get_lineas_agrupadas',
                "Líneas agrupadas para presupuesto ID: $id_presupuesto - Total: " . count($lineas) . " líneas en " . count($lineas_agrupadas) . " fechas",
                'info'
            );
            
            // 6. Retornar estructura completa
            return [
                'lineas_agrupadas' => $lineas_agrupadas,
                'totales_generales' => $totales_generales,
                'desglose_iva' => $desglose_iva
            ];
            
        } catch (Exception $e) {
            $this->registro->registrarActividad(
                'admin',
                'ImpresionPresupuesto',
                'get_lineas_agrupadas',
                "Error: " . $e->getMessage(),
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
    public function get_observaciones_presupuesto($id_presupuesto, $idioma = 'es', $numero_version = null)
    {
        try {
            // NO usar v_observaciones_presupuesto porque depende de vista_presupuesto_completa
            // que puede filtrar presupuestos. Usar consulta directa que sabemos que funciona.
            // Condición de versión
            $pv_version_condition = is_null($numero_version) ? "p.version_actual_presupuesto" : "?";
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
                AND pv.numero_version_presupuesto = $pv_version_condition
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
                AND pv.numero_version_presupuesto = $pv_version_condition
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
            if (!is_null($numero_version)) {
                // Con versión explícita: 4 parámetros (? en cada JOIN × 2 queries UNION)
                $stmt->execute([$numero_version, $id_presupuesto, $numero_version, $id_presupuesto]);
            } else {
                // Versión actual: solo los 2 WHERE
                $stmt->execute([$id_presupuesto, $id_presupuesto]);
            }
            
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

    /**
     * Obtener modelo de impresión configurado para la empresa
     * 
     * Obtiene el nombre del archivo de controller que debe usarse para imprimir
     * presupuestos de una empresa específica. Si no está configurado o hay error,
     * retorna el modelo por defecto (impresionpresupuesto_m1_es.php)
     * 
     * @param int $id_empresa ID de la empresa
     * @return string Nombre del archivo controller (ej: 'impresionpresupuesto_m1_es.php')
     */
    public function get_modelo_impresion($id_empresa)
    {
        try {
            $sql = "SELECT modelo_impresion_empresa 
                    FROM empresa 
                    WHERE id_empresa = ? 
                    AND activo_empresa = 1
                    LIMIT 1";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_empresa, PDO::PARAM_INT);
            $stmt->execute();
            
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Si se encuentra y tiene valor, retornar el modelo configurado
            if ($resultado && !empty($resultado['modelo_impresion_empresa'])) {
                $modelo = $resultado['modelo_impresion_empresa'];
                
                $this->registro->registrarActividad(
                    'admin',
                    'ImpresionPresupuesto',
                    'get_modelo_impresion',
                    "Modelo de impresión obtenido para empresa ID $id_empresa: $modelo",
                    'info'
                );
                
                return $modelo;
            } else {
                // Retornar modelo por defecto
                $modelo_defecto = 'impresionpresupuesto_m1_es.php';
                
                $this->registro->registrarActividad(
                    'admin',
                    'ImpresionPresupuesto',
                    'get_modelo_impresion',
                    "No se encontró modelo configurado para empresa ID $id_empresa, usando modelo por defecto: $modelo_defecto",
                    'info'
                );
                
                return $modelo_defecto;
            }
            
        } catch (PDOException $e) {
            // En caso de error, retornar modelo por defecto
            $modelo_defecto = 'impresionpresupuesto_m1_es.php';
            
            $this->registro->registrarActividad(
                'admin',
                'ImpresionPresupuesto',
                'get_modelo_impresion',
                "Error al obtener modelo de impresión: " . $e->getMessage() . " - Usando modelo por defecto: $modelo_defecto",
                'error'
            );
            
            return $modelo_defecto;
        }
    }

    /**
     * Obtener peso total de un presupuesto
     * 
     * Consulta la vista vista_presupuesto_peso para obtener el peso total calculado
     * de todas las líneas del presupuesto que tengan artículos con peso definido.
     * 
     * Retorna información sobre:
     * - Peso total en kilogramos
     * - Número de líneas con peso calculado
     * - Número total de líneas
     * - Porcentaje de completitud (líneas con peso / total líneas)
     * 
     * @param int $id_version_presupuesto ID de la versión del presupuesto
     * @return array|false Array con datos del peso o false si hay error
     *                     Keys: peso_total_kg, lineas_con_peso, lineas_sin_peso, 
     *                           total_lineas, porcentaje_completitud
     */
    public function get_peso_total_presupuesto($id_version_presupuesto)
    {
        try {
            $sql = "SELECT 
                        id_version_presupuesto,
                        peso_total_kg,
                        lineas_con_peso,
                        lineas_sin_peso,
                        total_lineas,
                        porcentaje_completitud
                    FROM vista_presupuesto_peso
                    WHERE id_version_presupuesto = ?
                    LIMIT 1";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_version_presupuesto, PDO::PARAM_INT);
            $stmt->execute();
            
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($resultado) {
                $this->registro->registrarActividad(
                    'admin',
                    'ImpresionPresupuesto',
                    'get_peso_total_presupuesto',
                    "Peso total obtenido para versión ID $id_version_presupuesto: {$resultado['peso_total_kg']} kg ({$resultado['porcentaje_completitud']}% completitud)",
                    'info'
                );
            } else {
                // Si no hay resultado, retornar estructura con valores null
                $resultado = [
                    'id_version_presupuesto' => $id_version_presupuesto,
                    'peso_total_kg' => null,
                    'lineas_con_peso' => 0,
                    'lineas_sin_peso' => 0,
                    'total_lineas' => 0,
                    'porcentaje_completitud' => 0
                ];
                
                $this->registro->registrarActividad(
                    'admin',
                    'ImpresionPresupuesto',
                    'get_peso_total_presupuesto',
                    "No se encontraron datos de peso para versión ID $id_version_presupuesto",
                    'info'
                );
            }
            
            return $resultado;
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'ImpresionPresupuesto',
                'get_peso_total_presupuesto',
                "Error al obtener peso total: " . $e->getMessage(),
                'error'
            );
            return false;
        }
    }

    /**
     * Obtener líneas del presupuesto con información de peso
     * 
     * Consulta la vista vista_linea_peso para obtener todas las líneas del presupuesto
     * con información del peso calculado de cada línea.
     * 
     * Útil para:
     * - Mostrar desglose de peso por línea
     * - Identificar líneas sin peso definido
     * - Debugging de cálculos de peso
     * 
     * @param int $id_version_presupuesto ID de la versión del presupuesto
     * @return array Array de líneas con información de peso (puede estar vacío)
     *               Cada elemento contiene: id_linea, cantidad, nombre_articulo,
     *                                        peso_unitario_kg, peso_total_linea_kg, 
     *                                        linea_tiene_peso
     */
    public function get_lineas_con_peso($id_version_presupuesto)
    {
        try {
            $sql = "SELECT 
                        vlp.id_linea_presupuesto,
                        vlp.cantidad_linea_presupuesto,
                        a.nombre_articulo,
                        vlp.peso_articulo_kg AS peso_unitario_kg,
                        vlp.peso_total_linea_kg,
                        vlp.linea_tiene_peso,
                        a.es_kit_articulo,
                        vlp.metodo_calculo_peso
                    FROM vista_linea_peso vlp
                    INNER JOIN linea_presupuesto lp 
                        ON vlp.id_linea_presupuesto = lp.id_linea_presupuesto
                    INNER JOIN articulo a 
                        ON lp.id_articulo = a.id_articulo
                    WHERE vlp.id_version_presupuesto = ?
                    ORDER BY lp.orden_linea_presupuesto ASC";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_version_presupuesto, PDO::PARAM_INT);
            $stmt->execute();
            
            $lineas = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $total_lineas = count($lineas);
            $lineas_con_peso = 0;
            foreach ($lineas as $linea) {
                if ($linea['linea_tiene_peso'] == 1) {
                    $lineas_con_peso++;
                }
            }
            
            $this->registro->registrarActividad(
                'admin',
                'ImpresionPresupuesto',
                'get_lineas_con_peso',
                "Líneas con peso obtenidas para versión ID $id_version_presupuesto: $total_lineas líneas ($lineas_con_peso con peso definido)",
                'info'
            );
            
            return $lineas;
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'ImpresionPresupuesto',
                'get_lineas_con_peso',
                "Error al obtener líneas con peso: " . $e->getMessage(),
                'error'
            );
            return [];
        }
    }

    /**
     * Obtener líneas del presupuesto para impresión versión HOTEL
     *
     * Idéntico a get_lineas_impresion() pero añade dos campos extra por línea:
     *   - permitir_descuentos_articulo : 1=sí permite descuento hotel, 0=no
     *   - permite_descuento_familia    : 1=sí permite descuento hotel, 0=no
     * Si el artículo o la familia no existen (línea libre) se devuelve 1 (sí aplica).
     *
     * @param int      $id_presupuesto  ID del presupuesto
     * @param int|null $numero_version  Versión concreta o null para la versión actual
     * @return array|false Array de líneas o false si hay error
     */
    public function get_lineas_impresion_hotel($id_presupuesto, $numero_version = null)
    {
        try {
            $version_sql_condition = is_null($numero_version)
                ? "(SELECT version_actual_presupuesto FROM presupuesto WHERE id_presupuesto = ?)"
                : "?";

            $sql = "SELECT
                        vlpc.id_linea_ppto,
                        vlpc.fecha_inicio_linea_ppto,
                        vlpc.fecha_fin_linea_ppto,
                        vlpc.fecha_montaje_linea_ppto,
                        vlpc.fecha_desmontaje_linea_ppto,
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
                        vlpc.descripcion_linea_ppto,
                        vlpc.observaciones_linea_ppto,
                        COALESCE(a.permitir_descuentos_articulo, 1) AS permitir_descuentos_articulo,
                        COALESCE(f.permite_descuento_familia, 1)    AS permite_descuento_familia
                    FROM v_linea_presupuesto_calculada vlpc
                    LEFT JOIN articulo a ON vlpc.id_articulo = a.id_articulo
                    LEFT JOIN familia  f ON a.id_familia = f.id_familia
                    WHERE vlpc.id_presupuesto = ?
                    AND vlpc.numero_version_presupuesto = $version_sql_condition
                    AND vlpc.activo_linea_ppto = 1
                    AND vlpc.mostrar_en_presupuesto = 1
                    ORDER BY
                        vlpc.fecha_inicio_linea_ppto ASC,
                        vlpc.id_ubicacion ASC,
                        vlpc.nombre_articulo ASC";

            $stmt = $this->conexion->prepare($sql);
            $stmt->execute(is_null($numero_version)
                ? [$id_presupuesto, $id_presupuesto]
                : [$id_presupuesto, $numero_version]);

            $lineas = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $this->registro->registrarActividad(
                'admin',
                'ImpresionPresupuesto',
                'get_lineas_impresion_hotel',
                "Líneas HOTEL obtenidas para presupuesto ID: $id_presupuesto - Total: " . count($lineas),
                'info'
            );

            return $lineas;

        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'ImpresionPresupuesto',
                'get_lineas_impresion_hotel',
                "Error al obtener líneas hotel: " . $e->getMessage(),
                'error'
            );
            return false;
        }
    }

    /**
     * Obtener el número de la versión aprobada de un presupuesto
     *
     * @param int $id_presupuesto
     * @return int|null Número de versión aprobada o null si no existe
     */
    public function get_numero_version_aprobada($id_presupuesto)
    {
        try {
            $sql = "SELECT numero_version_presupuesto 
                    FROM presupuesto_version 
                    WHERE id_presupuesto = ? 
                      AND estado_version_presupuesto = 'aprobado' 
                    ORDER BY numero_version_presupuesto DESC 
                    LIMIT 1";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_presupuesto, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ? (int)$row['numero_version_presupuesto'] : null;
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'ImpresionPresupuesto',
                'get_numero_version_aprobada',
                "Error: " . $e->getMessage(),
                'error'
            );
            return null;
        }
    }
}
?>
