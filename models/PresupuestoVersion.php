<?php

require_once '../config/conexion.php';
require_once '../config/funciones.php';

class PresupuestoVersion
{
    private $conexion;
    private $registro;

    public function __construct()
    {
        $this->conexion = (new Conexion())->getConexion();
        $this->registro = new RegistroActividad();

        try {
            $this->conexion->exec("SET time_zone = 'Europe/Madrid'");
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'system',
                'PresupuestoVersion',
                '__construct',
                "Error zona horaria: " . $e->getMessage(),
                'warning'
            );
        }
    }

    // ============================================
    // MÉTODOS DE LECTURA
    // ============================================

    /**
     * Obtener todas las versiones de un presupuesto
     * @param int $id_presupuesto
     * @return array Lista de versiones con metadatos
     */
    public function get_versiones($id_presupuesto)
    {
        try {
            $sql = "SELECT 
                        pv.id_version_presupuesto,
                        pv.numero_version_presupuesto,
                        pv.version_padre_presupuesto,
                        pv.estado_version_presupuesto,
                        pv.motivo_modificacion_version,
                        pv.fecha_creacion_version,
                        pv.fecha_envio_version,
                        pv.fecha_aprobacion_version,
                        pv.fecha_rechazo_version,
                        pv.motivo_rechazo_version,
                        pv.ruta_pdf_version,
                        pv.creado_por_version,
                        pv.enviado_por_version,
                        (SELECT COUNT(*) FROM linea_presupuesto 
                         WHERE id_version_presupuesto = pv.id_version_presupuesto
                         AND activo_linea_ppto = 1) as total_lineas
                    FROM presupuesto_version pv
                    WHERE pv.id_presupuesto = ?
                    AND pv.activo_version = 1
                    ORDER BY pv.numero_version_presupuesto DESC";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_presupuesto, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'PresupuestoVersion',
                'get_versiones',
                "Error: " . $e->getMessage(),
                'error'
            );
            return [];
        }
    }

    /**
     * Obtener detalle completo de una versión
     * @param int $id_version
     * @return array|false
     */
    public function get_version_detalle($id_version)
    {
        try {
            $sql = "SELECT 
                        pv.*,
                        p.numero_presupuesto,
                        p.nombre_evento_presupuesto,
                        p.version_actual_presupuesto,
                        c.nombre_cliente,
                        c.email_cliente
                    FROM presupuesto_version pv
                    INNER JOIN presupuesto p ON pv.id_presupuesto = p.id_presupuesto
                    INNER JOIN cliente c ON p.id_cliente = c.id_cliente
                    WHERE pv.id_version_presupuesto = ?
                    AND pv.activo_version = 1";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_version, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'PresupuestoVersion',
                'get_version_detalle',
                "Error: " . $e->getMessage(),
                'error'
            );
            return false;
        }
    }

    /**
     * Obtener versión activa de un presupuesto
     * @param int $id_presupuesto
     * @return array|false
     */
    public function get_version_activa($id_presupuesto)
    {
        try {
            $sql = "SELECT pv.*
                    FROM presupuesto_version pv
                    INNER JOIN presupuesto p ON pv.id_presupuesto = p.id_presupuesto
                    WHERE p.id_presupuesto = ?
                    AND pv.numero_version_presupuesto = p.version_actual_presupuesto
                    AND pv.activo_version = 1";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_presupuesto, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'PresupuestoVersion',
                'get_version_activa',
                "Error: " . $e->getMessage(),
                'error'
            );
            return false;
        }
    }

    // ============================================
    // MÉTODOS DE ESCRITURA
    // ============================================

    /**
     * Crear nueva versión vacía
     * El trigger trg_presupuesto_version_before_insert_numero auto-calcula el número de versión.
     * El trigger trg_presupuesto_version_before_insert_validar valida reglas de negocio.
     * @param int $id_presupuesto
     * @param string $motivo
     * @param int $id_usuario
     * @return int|false ID de nueva versión o false en caso de error
     */
    public function crear_version($id_presupuesto, $motivo, $id_usuario)
    {
        try {
            // Obtener id_version_actual para establecer version_padre
            $sql_padre = "SELECT id_version_presupuesto
                          FROM presupuesto_version
                          WHERE id_presupuesto = ?
                          AND numero_version_presupuesto = (
                              SELECT version_actual_presupuesto FROM presupuesto WHERE id_presupuesto = ?
                          )";

            $stmt_padre = $this->conexion->prepare($sql_padre);
            $stmt_padre->execute([$id_presupuesto, $id_presupuesto]);
            $padre = $stmt_padre->fetch(PDO::FETCH_ASSOC);

            $id_version_padre = $padre ? $padre['id_version_presupuesto'] : null;

            $sql = "INSERT INTO presupuesto_version (
                        id_presupuesto,
                        version_padre_presupuesto,
                        estado_version_presupuesto,
                        motivo_modificacion_version,
                        creado_por_version
                    ) VALUES (?, ?, 'borrador', ?, ?)";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_presupuesto, PDO::PARAM_INT);
            if ($id_version_padre !== null) {
                $stmt->bindValue(2, $id_version_padre, PDO::PARAM_INT);
            } else {
                $stmt->bindValue(2, null, PDO::PARAM_NULL);
            }
            $stmt->bindValue(3, $motivo, PDO::PARAM_STR);
            $stmt->bindValue(4, $id_usuario, PDO::PARAM_INT);
            $stmt->execute();

            $id_version_nueva = $this->conexion->lastInsertId();

            $this->registro->registrarActividad(
                'admin',
                'PresupuestoVersion',
                'crear_version',
                "Versión creada: ID=$id_version_nueva, Presupuesto=$id_presupuesto",
                'info'
            );

            return $id_version_nueva;

        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'PresupuestoVersion',
                'crear_version',
                "Error: " . $e->getMessage(),
                'error'
            );
            return false;
        }
    }

    /**
     * Duplicar líneas de una versión a otra
     * @param int $id_version_origen
     * @param int $id_version_destino
     * @return int Cantidad de líneas duplicadas
     */
    public function duplicar_lineas($id_version_origen, $id_version_destino)
    {
        try {
            // Duplicar todas las columnas de la tabla linea_presupuesto
            $sql = "INSERT INTO linea_presupuesto (
                        id_version_presupuesto,
                        id_articulo,
                        id_linea_padre,
                        id_ubicacion,
                        id_coeficiente,
                        id_impuesto,
                        numero_linea_ppto,
                        tipo_linea_ppto,
                        nivel_jerarquia,
                        orden_linea_ppto,
                        codigo_linea_ppto,
                        descripcion_linea_ppto,
                        fecha_montaje_linea_ppto,
                        fecha_desmontaje_linea_ppto,
                        fecha_inicio_linea_ppto,
                        fecha_fin_linea_ppto,
                        cantidad_linea_ppto,
                        precio_unitario_linea_ppto,
                        descuento_linea_ppto,
                        aplicar_coeficiente_linea_ppto,
                        valor_coeficiente_linea_ppto,
                        jornadas_linea_ppto,
                        porcentaje_iva_linea_ppto,
                        observaciones_linea_ppto,
                        mostrar_obs_articulo_linea_ppto,
                        ocultar_detalle_kit_linea_ppto,
                        mostrar_en_presupuesto,
                        es_opcional
                    )
                    SELECT
                        ? AS id_version_presupuesto,
                        id_articulo,
                        id_linea_padre,
                        id_ubicacion,
                        id_coeficiente,
                        id_impuesto,
                        numero_linea_ppto,
                        tipo_linea_ppto,
                        nivel_jerarquia,
                        orden_linea_ppto,
                        codigo_linea_ppto,
                        descripcion_linea_ppto,
                        fecha_montaje_linea_ppto,
                        fecha_desmontaje_linea_ppto,
                        fecha_inicio_linea_ppto,
                        fecha_fin_linea_ppto,
                        cantidad_linea_ppto,
                        precio_unitario_linea_ppto,
                        descuento_linea_ppto,
                        aplicar_coeficiente_linea_ppto,
                        valor_coeficiente_linea_ppto,
                        jornadas_linea_ppto,
                        porcentaje_iva_linea_ppto,
                        observaciones_linea_ppto,
                        mostrar_obs_articulo_linea_ppto,
                        ocultar_detalle_kit_linea_ppto,
                        mostrar_en_presupuesto,
                        es_opcional
                    FROM linea_presupuesto
                    WHERE id_version_presupuesto = ?
                    AND activo_linea_ppto = 1
                    ORDER BY orden_linea_ppto, numero_linea_ppto";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_version_destino, PDO::PARAM_INT);
            $stmt->bindValue(2, $id_version_origen, PDO::PARAM_INT);
            $stmt->execute();

            $lineas_duplicadas = $stmt->rowCount();

            $this->registro->registrarActividad(
                'admin',
                'PresupuestoVersion',
                'duplicar_lineas',
                "Duplicadas $lineas_duplicadas líneas: $id_version_origen → $id_version_destino",
                'info'
            );

            return $lineas_duplicadas;

        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'PresupuestoVersion',
                'duplicar_lineas',
                "Error: " . $e->getMessage(),
                'error'
            );
            return 0;
        }
    }

    /**
     * Cambiar estado de una versión
     * Los triggers trg_version_auto_fechas y trg_version_sync_estado_cabecera se encargan
     * de asignar fechas automáticas y sincronizar el estado con la cabecera del presupuesto.
     * @param int $id_version
     * @param string $nuevo_estado  borrador|enviado|aprobado|rechazado|cancelado
     * @param array $datos_extra    ['motivo_rechazo' => string, 'enviado_por' => int]
     * @return bool
     */
    public function cambiar_estado($id_version, $nuevo_estado, $datos_extra = [])
    {
        $estados_validos = ['borrador', 'enviado', 'aprobado', 'rechazado', 'cancelado'];
        if (!in_array($nuevo_estado, $estados_validos)) {
            $this->registro->registrarActividad(
                'admin',
                'PresupuestoVersion',
                'cambiar_estado',
                "Estado inválido: $nuevo_estado para versión $id_version",
                'warning'
            );
            return false;
        }

        try {
            $sql = "UPDATE presupuesto_version SET 
                        estado_version_presupuesto = ?,
                        enviado_por_version = ?,
                        motivo_rechazo_version = ?
                    WHERE id_version_presupuesto = ?
                    AND activo_version = 1";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $nuevo_estado, PDO::PARAM_STR);

            $enviado_por = $datos_extra['enviado_por'] ?? null;
            if ($enviado_por !== null) {
                $stmt->bindValue(2, $enviado_por, PDO::PARAM_INT);
            } else {
                $stmt->bindValue(2, null, PDO::PARAM_NULL);
            }

            $motivo_rechazo = $datos_extra['motivo_rechazo'] ?? null;
            if ($motivo_rechazo !== null) {
                $stmt->bindValue(3, $motivo_rechazo, PDO::PARAM_STR);
            } else {
                $stmt->bindValue(3, null, PDO::PARAM_NULL);
            }

            $stmt->bindValue(4, $id_version, PDO::PARAM_INT);
            $stmt->execute();

            // ----------------------------------------------------------------
            // RESPALDO PHP: Sincronizar id_estado_ppto en la cabecera
            // El trigger trg_version_sync_estado_cabecera ya lo hace, pero
            // este bloque actúa como seguro adicional en caso de que el trigger
            // no se ejecute por alguna razón (p.ej. cambio directo de BD).
            // ----------------------------------------------------------------
            $mapeo_estados = [
                'borrador'  => 'BORRADOR',
                'enviado'   => 'ESPE-RESP',
                'aprobado'  => 'APROB',
                'rechazado' => 'RECH',
                'cancelado' => 'CANC',
            ];

            if (isset($mapeo_estados[$nuevo_estado])) {
                $codigo_estado = $mapeo_estados[$nuevo_estado];

                $sqlSync = "UPDATE presupuesto p
                    INNER JOIN presupuesto_version pv
                        ON  pv.id_version_presupuesto = ?
                        AND pv.id_presupuesto = p.id_presupuesto
                        AND pv.numero_version_presupuesto = p.version_actual_presupuesto
                    INNER JOIN estado_presupuesto ep
                        ON  ep.codigo_estado_ppto = ?
                    SET p.id_estado_ppto             = ep.id_estado_ppto,
                        p.estado_general_presupuesto  = ?
                    WHERE p.activo_presupuesto = 1";

                $stmtSync = $this->conexion->prepare($sqlSync);
                $stmtSync->bindValue(1, $id_version,    PDO::PARAM_INT);
                $stmtSync->bindValue(2, $codigo_estado, PDO::PARAM_STR);
                $stmtSync->bindValue(3, $nuevo_estado,  PDO::PARAM_STR);
                $stmtSync->execute();
            }
            // ----------------------------------------------------------------

            $this->registro->registrarActividad(
                'admin',
                'PresupuestoVersion',
                'cambiar_estado',
                "Versión $id_version → $nuevo_estado",
                'info'
            );

            return $stmt->rowCount() > 0;

        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'PresupuestoVersion',
                'cambiar_estado',
                "Error: " . $e->getMessage(),
                'error'
            );
            return false;
        }
    }

    // ============================================
    // MÉTODOS DE COMPARACIÓN
    // ============================================

    /**
     * Comparar dos versiones de un presupuesto
     * @param int $id_version_a  Versión base (antigua)
     * @param int $id_version_b  Versión nueva
     * @return array ['anadidas', 'eliminadas', 'modificadas', 'resumen']
     */
    public function comparar_versiones($id_version_a, $id_version_b)
    {
        try {
            // Líneas añadidas en B (presentes en B pero no en A)
            // total_linea_ppto no existe como columna, se calcula al vuelo
            $sql_anadidas = "SELECT lb.*,
                                   ROUND(lb.cantidad_linea_ppto * lb.precio_unitario_linea_ppto * (1 - lb.descuento_linea_ppto/100), 2) AS total_linea_ppto,
                                   'AÑADIDO' as accion
                            FROM linea_presupuesto lb
                            LEFT JOIN linea_presupuesto la
                                ON la.id_articulo = lb.id_articulo
                                AND la.id_version_presupuesto = ?
                                AND la.activo_linea_ppto = 1
                            WHERE lb.id_version_presupuesto = ?
                            AND lb.activo_linea_ppto = 1
                            AND la.id_linea_ppto IS NULL";

            $stmt = $this->conexion->prepare($sql_anadidas);
            $stmt->execute([$id_version_a, $id_version_b]);
            $anadidas = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Líneas eliminadas en B (presentes en A pero no en B)
            $sql_eliminadas = "SELECT la.*,
                                     ROUND(la.cantidad_linea_ppto * la.precio_unitario_linea_ppto * (1 - la.descuento_linea_ppto/100), 2) AS total_linea_ppto,
                                     'ELIMINADO' as accion
                              FROM linea_presupuesto la
                              LEFT JOIN linea_presupuesto lb
                                  ON lb.id_articulo = la.id_articulo
                                  AND lb.id_version_presupuesto = ?
                                  AND lb.activo_linea_ppto = 1
                              WHERE la.id_version_presupuesto = ?
                              AND la.activo_linea_ppto = 1
                              AND lb.id_linea_ppto IS NULL";

            $stmt = $this->conexion->prepare($sql_eliminadas);
            $stmt->execute([$id_version_b, $id_version_a]);
            $eliminadas = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Líneas modificadas (presentes en ambas, con diferencias)
            // total_linea_ppto no existe como columna, se calcula al vuelo
            $sql_modificadas = "SELECT lb.*,
                                       ROUND(lb.cantidad_linea_ppto * lb.precio_unitario_linea_ppto * (1 - lb.descuento_linea_ppto/100), 2) AS total_linea_ppto,
                                       'MODIFICADO' as accion,
                                       la.cantidad_linea_ppto as cantidad_antigua,
                                       la.precio_unitario_linea_ppto as precio_antiguo,
                                       la.descuento_linea_ppto as descuento_antiguo,
                                       ROUND(la.cantidad_linea_ppto * la.precio_unitario_linea_ppto * (1 - la.descuento_linea_ppto/100), 2) as total_antiguo
                               FROM linea_presupuesto la
                               INNER JOIN linea_presupuesto lb
                                   ON lb.id_articulo = la.id_articulo
                                   AND lb.id_version_presupuesto = ?
                                   AND lb.activo_linea_ppto = 1
                               WHERE la.id_version_presupuesto = ?
                               AND la.activo_linea_ppto = 1
                               AND (
                                   la.cantidad_linea_ppto != lb.cantidad_linea_ppto OR
                                   la.precio_unitario_linea_ppto != lb.precio_unitario_linea_ppto OR
                                   la.descuento_linea_ppto != lb.descuento_linea_ppto
                               )";

            $stmt = $this->conexion->prepare($sql_modificadas);
            $stmt->execute([$id_version_b, $id_version_a]);
            $modificadas = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return [
                'anadidas'   => $anadidas,
                'eliminadas' => $eliminadas,
                'modificadas' => $modificadas,
                'resumen'    => [
                    'total_anadidas'   => count($anadidas),
                    'total_eliminadas' => count($eliminadas),
                    'total_modificadas' => count($modificadas)
                ]
            ];

        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'PresupuestoVersion',
                'comparar_versiones',
                "Error: " . $e->getMessage(),
                'error'
            );
            return [
                'anadidas'   => [],
                'eliminadas' => [],
                'modificadas' => [],
                'resumen'    => ['error' => $e->getMessage()]
            ];
        }
    }
}
?>
