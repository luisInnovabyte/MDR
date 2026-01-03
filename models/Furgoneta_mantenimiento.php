<?php

require_once '../config/conexion.php';
require_once '../config/funciones.php';

class Furgoneta_mantenimiento
{
    private $conexion;
    private $registro;

    public function __construct()
    {
        // Inicializar conexión PDO
        $this->conexion = (new Conexion())->getConexion();
        
        // Inicializar registro de actividad
        $this->registro = new RegistroActividad();
        
        // Configurar zona horaria
        try {
            $this->conexion->exec("SET time_zone = 'Europe/Madrid'");
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'system',
                'Furgoneta_mantenimiento',
                '__construct',
                "Error zona horaria: " . $e->getMessage(),
                'warning'
            );
        }
    }

    // =====================================================
    // MÉTODO 1: Listar todos los mantenimientos
    // =====================================================
    public function get_mantenimientos()
    {
        try {
            $sql = "SELECT * FROM vista_mantenimiento_completo
                    ORDER BY fecha_mantenimiento DESC";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Furgoneta_mantenimiento',
                'get_mantenimientos',
                "Error al listar mantenimientos: " . $e->getMessage(),
                'error'
            );
            return [];
        }
    }

    // =====================================================
    // MÉTODO 2: Listar todos (incluyendo inactivos)
    // =====================================================
    public function get_todos_mantenimientos()
    {
        try {
            // Nota: La vista solo muestra activos, aquí usamos tabla directa
            $sql = "SELECT * FROM vista_mantenimiento_completo
                    ORDER BY fecha_mantenimiento DESC";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Furgoneta_mantenimiento',
                'get_todos_mantenimientos',
                "Error: " . $e->getMessage(),
                'error'
            );
            return [];
        }
    }

    // =====================================================
    // MÉTODO 3: Obtener mantenimiento por ID
    // =====================================================
    public function get_mantenimientoxid($id_mantenimiento)
    {
        try {
            $sql = "SELECT * FROM vista_mantenimiento_completo
                    WHERE id_mantenimiento = ?";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_mantenimiento, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Furgoneta_mantenimiento',
                'get_mantenimientoxid',
                "Error al obtener mantenimiento ID {$id_mantenimiento}: " . $e->getMessage(),
                'error'
            );
            return false;
        }
    }

    // =====================================================
    // MÉTODO 4: Obtener mantenimientos por furgoneta
    // =====================================================
    public function get_mantenimientos_por_furgoneta($id_furgoneta)
    {
        try {
            $sql = "SELECT * FROM vista_mantenimiento_completo
                    WHERE id_furgoneta = ?
                    ORDER BY fecha_mantenimiento DESC";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_furgoneta, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Furgoneta_mantenimiento',
                'get_mantenimientos_por_furgoneta',
                "Error: " . $e->getMessage(),
                'error'
            );
            return [];
        }
    }

    // =====================================================
    // MÉTODO 5: Obtener último mantenimiento de una furgoneta
    // =====================================================
    public function get_ultimo_mantenimiento($id_furgoneta)
    {
        try {
            $sql = "SELECT * FROM vista_mantenimiento_completo
                    WHERE id_furgoneta = ?
                    ORDER BY fecha_mantenimiento DESC
                    LIMIT 1";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_furgoneta, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Furgoneta_mantenimiento',
                'get_ultimo_mantenimiento',
                "Error: " . $e->getMessage(),
                'error'
            );
            return false;
        }
    }

    // =====================================================
    // MÉTODO 6: Insertar nuevo mantenimiento
    // =====================================================
    public function insert_mantenimiento(
        $id_furgoneta,
        $fecha_mantenimiento,
        $tipo_mantenimiento,
        $descripcion_mantenimiento,
        $kilometraje_mantenimiento = null,
        $costo_mantenimiento = 0.00,
        $numero_factura = null,
        $taller = null,
        $telefono_taller = null,
        $direccion_taller = null,
        $resultado_itv = null,
        $fecha_proxima_itv = null,
        $garantia_hasta = null,
        $observaciones = null
    ) {
        try {
            // Validar tipo de mantenimiento
            $tipos_validos = ['revision', 'reparacion', 'itv', 'neumaticos', 'otros'];
            if (!in_array($tipo_mantenimiento, $tipos_validos)) {
                return ['error' => 'Tipo de mantenimiento no válido'];
            }

            // Si es ITV y tiene resultado favorable, actualizar fecha en tabla furgoneta
            if ($tipo_mantenimiento == 'itv' && $resultado_itv == 'favorable' && !empty($fecha_proxima_itv)) {
                $sql_update_furgoneta = "UPDATE furgoneta SET 
                                         fecha_proxima_itv_furgoneta = ?,
                                         updated_at_furgoneta = NOW()
                                         WHERE id_furgoneta = ?";
                $stmt_update = $this->conexion->prepare($sql_update_furgoneta);
                $stmt_update->bindValue(1, $fecha_proxima_itv, PDO::PARAM_STR);
                $stmt_update->bindValue(2, $id_furgoneta, PDO::PARAM_INT);
                $stmt_update->execute();
            }

            $sql = "INSERT INTO furgoneta_mantenimiento (
                        id_furgoneta,
                        fecha_mantenimiento,
                        tipo_mantenimiento,
                        descripcion_mantenimiento,
                        kilometraje_mantenimiento,
                        costo_mantenimiento,
                        numero_factura_mantenimiento,
                        taller_mantenimiento,
                        telefono_taller_mantenimiento,
                        direccion_taller_mantenimiento,
                        resultado_itv,
                        fecha_proxima_itv,
                        garantia_hasta_mantenimiento,
                        observaciones_mantenimiento,
                        created_at_mantenimiento
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_furgoneta, PDO::PARAM_INT);
            $stmt->bindValue(2, $fecha_mantenimiento, PDO::PARAM_STR);
            $stmt->bindValue(3, $tipo_mantenimiento, PDO::PARAM_STR);
            $stmt->bindValue(4, $descripcion_mantenimiento, PDO::PARAM_STR);
            $stmt->bindValue(5, !empty($kilometraje_mantenimiento) ? $kilometraje_mantenimiento : null, !empty($kilometraje_mantenimiento) ? PDO::PARAM_INT : PDO::PARAM_NULL);
            $stmt->bindValue(6, $costo_mantenimiento, PDO::PARAM_STR);
            $stmt->bindValue(7, !empty($numero_factura) ? $numero_factura : null, !empty($numero_factura) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(8, !empty($taller) ? $taller : null, !empty($taller) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(9, !empty($telefono_taller) ? $telefono_taller : null, !empty($telefono_taller) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(10, !empty($direccion_taller) ? $direccion_taller : null, !empty($direccion_taller) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(11, !empty($resultado_itv) ? $resultado_itv : null, !empty($resultado_itv) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(12, !empty($fecha_proxima_itv) ? $fecha_proxima_itv : null, !empty($fecha_proxima_itv) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(13, !empty($garantia_hasta) ? $garantia_hasta : null, !empty($garantia_hasta) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(14, !empty($observaciones) ? $observaciones : null, !empty($observaciones) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            
            $stmt->execute();
            
            $id = $this->conexion->lastInsertId();
            
            $this->registro->registrarActividad(
                'admin',
                'Furgoneta_mantenimiento',
                'insert_mantenimiento',
                "Mantenimiento creado con ID: $id - Furgoneta: $id_furgoneta - Tipo: $tipo_mantenimiento",
                'info'
            );
            
            return $id;
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Furgoneta_mantenimiento',
                'insert_mantenimiento',
                "Error: " . $e->getMessage(),
                'error'
            );
            return false;
        }
    }

    // =====================================================
    // MÉTODO 7: Actualizar mantenimiento
    // =====================================================
    public function update_mantenimiento(
        $id_mantenimiento,
        $id_furgoneta,
        $fecha_mantenimiento,
        $tipo_mantenimiento,
        $descripcion_mantenimiento,
        $kilometraje_mantenimiento = null,
        $costo_mantenimiento = 0.00,
        $numero_factura = null,
        $taller = null,
        $telefono_taller = null,
        $direccion_taller = null,
        $resultado_itv = null,
        $fecha_proxima_itv = null,
        $garantia_hasta = null,
        $observaciones = null
    ) {
        try {
            // Validar tipo de mantenimiento
            $tipos_validos = ['revision', 'reparacion', 'itv', 'neumaticos', 'otros'];
            if (!in_array($tipo_mantenimiento, $tipos_validos)) {
                return ['error' => 'Tipo de mantenimiento no válido'];
            }

            // Si es ITV y tiene resultado favorable, actualizar fecha en tabla furgoneta
            if ($tipo_mantenimiento == 'itv' && $resultado_itv == 'favorable' && !empty($fecha_proxima_itv)) {
                $sql_update_furgoneta = "UPDATE furgoneta SET 
                                         fecha_proxima_itv_furgoneta = ?,
                                         updated_at_furgoneta = NOW()
                                         WHERE id_furgoneta = ?";
                $stmt_update = $this->conexion->prepare($sql_update_furgoneta);
                $stmt_update->bindValue(1, $fecha_proxima_itv, PDO::PARAM_STR);
                $stmt_update->bindValue(2, $id_furgoneta, PDO::PARAM_INT);
                $stmt_update->execute();
            }

            $sql = "UPDATE furgoneta_mantenimiento SET 
                        id_furgoneta = ?,
                        fecha_mantenimiento = ?,
                        tipo_mantenimiento = ?,
                        descripcion_mantenimiento = ?,
                        kilometraje_mantenimiento = ?,
                        costo_mantenimiento = ?,
                        numero_factura_mantenimiento = ?,
                        taller_mantenimiento = ?,
                        telefono_taller_mantenimiento = ?,
                        direccion_taller_mantenimiento = ?,
                        resultado_itv = ?,
                        fecha_proxima_itv = ?,
                        garantia_hasta_mantenimiento = ?,
                        observaciones_mantenimiento = ?,
                        updated_at_mantenimiento = NOW()
                    WHERE id_mantenimiento = ?";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_furgoneta, PDO::PARAM_INT);
            $stmt->bindValue(2, $fecha_mantenimiento, PDO::PARAM_STR);
            $stmt->bindValue(3, $tipo_mantenimiento, PDO::PARAM_STR);
            $stmt->bindValue(4, $descripcion_mantenimiento, PDO::PARAM_STR);
            $stmt->bindValue(5, !empty($kilometraje_mantenimiento) ? $kilometraje_mantenimiento : null, !empty($kilometraje_mantenimiento) ? PDO::PARAM_INT : PDO::PARAM_NULL);
            $stmt->bindValue(6, $costo_mantenimiento, PDO::PARAM_STR);
            $stmt->bindValue(7, !empty($numero_factura) ? $numero_factura : null, !empty($numero_factura) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(8, !empty($taller) ? $taller : null, !empty($taller) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(9, !empty($telefono_taller) ? $telefono_taller : null, !empty($telefono_taller) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(10, !empty($direccion_taller) ? $direccion_taller : null, !empty($direccion_taller) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(11, !empty($resultado_itv) ? $resultado_itv : null, !empty($resultado_itv) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(12, !empty($fecha_proxima_itv) ? $fecha_proxima_itv : null, !empty($fecha_proxima_itv) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(13, !empty($garantia_hasta) ? $garantia_hasta : null, !empty($garantia_hasta) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(14, !empty($observaciones) ? $observaciones : null, !empty($observaciones) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(15, $id_mantenimiento, PDO::PARAM_INT);
            
            $stmt->execute();
            
            $this->registro->registrarActividad(
                'admin',
                'Furgoneta_mantenimiento',
                'update_mantenimiento',
                "Mantenimiento actualizado ID: $id_mantenimiento - Tipo: $tipo_mantenimiento",
                'info'
            );
            
            return $stmt->rowCount();
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Furgoneta_mantenimiento',
                'update_mantenimiento',
                "Error: " . $e->getMessage(),
                'error'
            );
            return false;
        }
    }

    // =====================================================
    // MÉTODO 8: Eliminar (SOFT DELETE)
    // =====================================================
    public function delete_mantenimientoxid($id_mantenimiento)
    {
        try {
            $sql = "UPDATE furgoneta_mantenimiento SET 
                        activo_mantenimiento = 0,
                        updated_at_mantenimiento = NOW()
                    WHERE id_mantenimiento = ?";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_mantenimiento, PDO::PARAM_INT);
            $stmt->execute();
            
            $this->registro->registrarActividad(
                'admin',
                'Furgoneta_mantenimiento',
                'delete_mantenimientoxid',
                "Mantenimiento desactivado ID: $id_mantenimiento",
                'info'
            );
            
            return $stmt->rowCount() > 0;
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Furgoneta_mantenimiento',
                'delete_mantenimientoxid',
                "Error: " . $e->getMessage(),
                'error'
            );
            return false;
        }
    }

    // =====================================================
    // MÉTODO 9: Activar mantenimiento
    // =====================================================
    public function activar_mantenimientoxid($id_mantenimiento)
    {
        try {
            $sql = "UPDATE furgoneta_mantenimiento SET 
                        activo_mantenimiento = 1,
                        updated_at_mantenimiento = NOW()
                    WHERE id_mantenimiento = ?";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_mantenimiento, PDO::PARAM_INT);
            $stmt->execute();
            
            $this->registro->registrarActividad(
                'admin',
                'Furgoneta_mantenimiento',
                'activar_mantenimientoxid',
                "Mantenimiento activado ID: $id_mantenimiento",
                'info'
            );
            
            return $stmt->rowCount() > 0;
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Furgoneta_mantenimiento',
                'activar_mantenimientoxid',
                "Error: " . $e->getMessage(),
                'error'
            );
            return false;
        }
    }

    // =====================================================
    // MÉTODO 10: Obtener mantenimientos por tipo
    // =====================================================
    public function get_mantenimientos_por_tipo($id_furgoneta, $tipo_mantenimiento)
    {
        try {
            $sql = "SELECT * FROM vista_mantenimiento_completo
                    WHERE id_furgoneta = ?
                    AND tipo_mantenimiento = ?
                    ORDER BY fecha_mantenimiento DESC";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_furgoneta, PDO::PARAM_INT);
            $stmt->bindValue(2, $tipo_mantenimiento, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Furgoneta_mantenimiento',
                'get_mantenimientos_por_tipo',
                "Error: " . $e->getMessage(),
                'error'
            );
            return [];
        }
    }

    // =====================================================
    // MÉTODO 11: Obtener todas las ITV de una furgoneta
    // =====================================================
    public function get_historial_itv($id_furgoneta)
    {
        try {
            $sql = "SELECT * FROM vista_mantenimiento_completo
                    WHERE id_furgoneta = ?
                    AND tipo_mantenimiento = 'itv'
                    ORDER BY fecha_mantenimiento DESC";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_furgoneta, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Furgoneta_mantenimiento',
                'get_historial_itv',
                "Error: " . $e->getMessage(),
                'error'
            );
            return [];
        }
    }

    // =====================================================
    // MÉTODO 12: Obtener última ITV de una furgoneta
    // =====================================================
    public function get_ultima_itv($id_furgoneta)
    {
        try {
            $sql = "SELECT * FROM vista_mantenimiento_completo
                    WHERE id_furgoneta = ?
                    AND tipo_mantenimiento = 'itv'
                    ORDER BY fecha_mantenimiento DESC
                    LIMIT 1";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_furgoneta, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Furgoneta_mantenimiento',
                'get_ultima_itv',
                "Error: " . $e->getMessage(),
                'error'
            );
            return false;
        }
    }

    // =====================================================
    // MÉTODO 13: Obtener mantenimientos por rango de fechas
    // =====================================================
    public function get_mantenimientos_por_fecha($id_furgoneta, $fecha_inicio, $fecha_fin)
    {
        try {
            $sql = "SELECT * FROM vista_mantenimiento_completo
                    WHERE id_furgoneta = ?
                    AND fecha_mantenimiento BETWEEN ? AND ?
                    ORDER BY fecha_mantenimiento DESC";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_furgoneta, PDO::PARAM_INT);
            $stmt->bindValue(2, $fecha_inicio, PDO::PARAM_STR);
            $stmt->bindValue(3, $fecha_fin, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Furgoneta_mantenimiento',
                'get_mantenimientos_por_fecha',
                "Error: " . $e->getMessage(),
                'error'
            );
            return [];
        }
    }

    // =====================================================
    // MÉTODO 14: Calcular costo total de mantenimientos
    // =====================================================
    public function calcular_costo_total($id_furgoneta, $fecha_inicio = null, $fecha_fin = null)
    {
        try {
            $sql = "SELECT 
                        SUM(costo_mantenimiento) AS costo_total,
                        COUNT(*) AS total_mantenimientos
                    FROM furgoneta_mantenimiento
                    WHERE id_furgoneta = ?
                    AND activo_mantenimiento = 1";
            
            $params = [$id_furgoneta];
            
            if (!empty($fecha_inicio) && !empty($fecha_fin)) {
                $sql .= " AND fecha_mantenimiento BETWEEN ? AND ?";
                $params[] = $fecha_inicio;
                $params[] = $fecha_fin;
            }
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute($params);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return [
                'costo_total' => floatval($resultado['costo_total'] ?? 0),
                'total_mantenimientos' => intval($resultado['total_mantenimientos'] ?? 0)
            ];
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Furgoneta_mantenimiento',
                'calcular_costo_total',
                "Error: " . $e->getMessage(),
                'error'
            );
            return [
                'costo_total' => 0,
                'total_mantenimientos' => 0
            ];
        }
    }

    // =====================================================
    // MÉTODO 15: Obtener mantenimientos con garantía vigente
    // =====================================================
    public function get_mantenimientos_con_garantia($id_furgoneta)
    {
        try {
            $sql = "SELECT *,
                        DATEDIFF(garantia_hasta_mantenimiento, CURDATE()) AS dias_garantia_restantes
                    FROM vista_mantenimiento_completo
                    WHERE id_furgoneta = ?
                    AND garantia_hasta_mantenimiento IS NOT NULL
                    AND garantia_hasta_mantenimiento >= CURDATE()
                    ORDER BY garantia_hasta_mantenimiento ASC";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_furgoneta, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Furgoneta_mantenimiento',
                'get_mantenimientos_con_garantia',
                "Error: " . $e->getMessage(),
                'error'
            );
            return [];
        }
    }

    // =====================================================
    // MÉTODO 16: Obtener estadísticas de mantenimientos
    // =====================================================
    public function obtener_estadisticas($id_furgoneta)
    {
        try {
            $sql = "SELECT 
                        COUNT(*) AS total_mantenimientos,
                        SUM(costo_mantenimiento) AS costo_total,
                        AVG(costo_mantenimiento) AS costo_promedio,
                        MIN(fecha_mantenimiento) AS fecha_primer_mantenimiento,
                        MAX(fecha_mantenimiento) AS fecha_ultimo_mantenimiento,
                        SUM(CASE WHEN tipo_mantenimiento = 'revision' THEN 1 ELSE 0 END) AS total_revisiones,
                        SUM(CASE WHEN tipo_mantenimiento = 'reparacion' THEN 1 ELSE 0 END) AS total_reparaciones,
                        SUM(CASE WHEN tipo_mantenimiento = 'itv' THEN 1 ELSE 0 END) AS total_itv,
                        SUM(CASE WHEN tipo_mantenimiento = 'neumaticos' THEN 1 ELSE 0 END) AS total_neumaticos,
                        SUM(CASE WHEN tipo_mantenimiento = 'otros' THEN 1 ELSE 0 END) AS total_otros,
                        SUM(CASE WHEN tipo_mantenimiento = 'revision' THEN costo_mantenimiento ELSE 0 END) AS costo_revisiones,
                        SUM(CASE WHEN tipo_mantenimiento = 'reparacion' THEN costo_mantenimiento ELSE 0 END) AS costo_reparaciones,
                        SUM(CASE WHEN tipo_mantenimiento = 'itv' THEN costo_mantenimiento ELSE 0 END) AS costo_itv,
                        SUM(CASE WHEN tipo_mantenimiento = 'neumaticos' THEN costo_mantenimiento ELSE 0 END) AS costo_neumaticos
                    FROM furgoneta_mantenimiento
                    WHERE id_furgoneta = ?
                    AND activo_mantenimiento = 1";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_furgoneta, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Furgoneta_mantenimiento',
                'obtener_estadisticas',
                "Error: " . $e->getMessage(),
                'error'
            );
            return [];
        }
    }

    // =====================================================
    // MÉTODO 17: Obtener mantenimientos próximos (por garantía)
    // =====================================================
    public function get_garantias_proximas_vencer($dias = 30)
    {
        try {
            $sql = "SELECT *,
                        DATEDIFF(garantia_hasta_mantenimiento, CURDATE()) AS dias_garantia_restantes
                    FROM vista_mantenimiento_completo
                    WHERE garantia_hasta_mantenimiento IS NOT NULL
                    AND garantia_hasta_mantenimiento >= CURDATE()
                    AND garantia_hasta_mantenimiento <= DATE_ADD(CURDATE(), INTERVAL ? DAY)
                    ORDER BY garantia_hasta_mantenimiento ASC";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $dias, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Furgoneta_mantenimiento',
                'get_garantias_proximas_vencer',
                "Error: " . $e->getMessage(),
                'error'
            );
            return [];
        }
    }

    // =====================================================
    // MÉTODO 18: Obtener resumen de ITV de todas las furgonetas
    // =====================================================
    public function get_resumen_itv_todas()
    {
        try {
            $sql = "SELECT 
                        f.id_furgoneta,
                        f.matricula_furgoneta,
                        f.marca_furgoneta,
                        f.modelo_furgoneta,
                        m.fecha_mantenimiento AS fecha_ultima_itv,
                        m.resultado_itv,
                        m.fecha_proxima_itv,
                        DATEDIFF(m.fecha_proxima_itv, CURDATE()) AS dias_para_proxima_itv
                    FROM furgoneta f
                    LEFT JOIN (
                        SELECT 
                            id_furgoneta,
                            fecha_mantenimiento,
                            resultado_itv,
                            fecha_proxima_itv,
                            ROW_NUMBER() OVER (PARTITION BY id_furgoneta ORDER BY fecha_mantenimiento DESC) AS rn
                        FROM furgoneta_mantenimiento
                        WHERE tipo_mantenimiento = 'itv'
                        AND activo_mantenimiento = 1
                    ) m ON f.id_furgoneta = m.id_furgoneta AND m.rn = 1
                    WHERE f.activo_furgoneta = 1
                    ORDER BY dias_para_proxima_itv ASC";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Furgoneta_mantenimiento',
                'get_resumen_itv_todas',
                "Error: " . $e->getMessage(),
                'error'
            );
            return [];
        }
    }

    // =====================================================
    // MÉTODO 19: Obtener costo promedio por tipo de mantenimiento
    // =====================================================
    public function get_costo_promedio_por_tipo()
    {
        try {
            $sql = "SELECT 
                        tipo_mantenimiento,
                        COUNT(*) AS total,
                        SUM(costo_mantenimiento) AS costo_total,
                        AVG(costo_mantenimiento) AS costo_promedio,
                        MIN(costo_mantenimiento) AS costo_minimo,
                        MAX(costo_mantenimiento) AS costo_maximo
                    FROM furgoneta_mantenimiento
                    WHERE activo_mantenimiento = 1
                    GROUP BY tipo_mantenimiento
                    ORDER BY costo_total DESC";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Furgoneta_mantenimiento',
                'get_costo_promedio_por_tipo',
                "Error: " . $e->getMessage(),
                'error'
            );
            return [];
        }
    }

    // =====================================================
    // MÉTODO 20: Obtener talleres más utilizados
    // =====================================================
    public function get_talleres_mas_utilizados($limite = 10)
    {
        try {
            $sql = "SELECT 
                        taller_mantenimiento,
                        telefono_taller_mantenimiento,
                        COUNT(*) AS total_servicios,
                        SUM(costo_mantenimiento) AS costo_total,
                        AVG(costo_mantenimiento) AS costo_promedio,
                        MAX(fecha_mantenimiento) AS fecha_ultimo_servicio
                    FROM furgoneta_mantenimiento
                    WHERE taller_mantenimiento IS NOT NULL
                    AND activo_mantenimiento = 1
                    GROUP BY taller_mantenimiento, telefono_taller_mantenimiento
                    ORDER BY total_servicios DESC
                    LIMIT ?";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $limite, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Furgoneta_mantenimiento',
                'get_talleres_mas_utilizados',
                "Error: " . $e->getMessage(),
                'error'
            );
            return [];
        }
    }
}
?>
