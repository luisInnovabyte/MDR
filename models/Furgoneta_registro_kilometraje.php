<?php

require_once '../config/conexion.php';
require_once '../config/funciones.php';

class Furgoneta_registro_kilometraje
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
                'Furgoneta_registro_kilometraje',
                '__construct',
                "Error zona horaria: " . $e->getMessage(),
                'warning'
            );
        }
    }

    // =====================================================
    // MÉTODO 1: Listar todos los registros
    // =====================================================
    public function get_registros_km()
    {
        try {
            $sql = "SELECT 
                        rk.*,
                        f.matricula_furgoneta,
                        f.marca_furgoneta,
                        f.modelo_furgoneta
                    FROM furgoneta_registro_kilometraje rk
                    INNER JOIN furgoneta f ON rk.id_furgoneta = f.id_furgoneta
                    ORDER BY rk.fecha_registro_km DESC";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Furgoneta_registro_kilometraje',
                'get_registros_km',
                "Error al listar registros: " . $e->getMessage(),
                'error'
            );
            return [];
        }
    }

    // =====================================================
    // MÉTODO 2: Obtener registro por ID
    // =====================================================
    public function get_registro_kmxid($id_registro_km)
    {
        try {
            $sql = "SELECT 
                        rk.*,
                        f.matricula_furgoneta,
                        f.marca_furgoneta,
                        f.modelo_furgoneta
                    FROM furgoneta_registro_kilometraje rk
                    INNER JOIN furgoneta f ON rk.id_furgoneta = f.id_furgoneta
                    WHERE rk.id_registro_km = ?";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_registro_km, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Furgoneta_registro_kilometraje',
                'get_registro_kmxid',
                "Error al obtener registro ID {$id_registro_km}: " . $e->getMessage(),
                'error'
            );
            return false;
        }
    }

    // =====================================================
    // MÉTODO 3: Obtener registros por furgoneta
    // =====================================================
    public function get_registros_por_furgoneta($id_furgoneta)
    {
        try {
            $sql = "SELECT 
                        rk.*,
                        f.matricula_furgoneta,
                        f.marca_furgoneta,
                        f.modelo_furgoneta
                    FROM furgoneta_registro_kilometraje rk
                    INNER JOIN furgoneta f ON rk.id_furgoneta = f.id_furgoneta
                    WHERE rk.id_furgoneta = ?
                    ORDER BY rk.fecha_registro_km DESC";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_furgoneta, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Furgoneta_registro_kilometraje',
                'get_registros_por_furgoneta',
                "Error: " . $e->getMessage(),
                'error'
            );
            return [];
        }
    }

    // =====================================================
    // MÉTODO 4: Obtener último registro de una furgoneta
    // =====================================================
    public function get_ultimo_registro($id_furgoneta)
    {
        try {
            $sql = "SELECT 
                        rk.*,
                        f.matricula_furgoneta,
                        f.marca_furgoneta,
                        f.modelo_furgoneta
                    FROM furgoneta_registro_kilometraje rk
                    INNER JOIN furgoneta f ON rk.id_furgoneta = f.id_furgoneta
                    WHERE rk.id_furgoneta = ?
                    ORDER BY rk.fecha_registro_km DESC, rk.kilometraje_registrado_km DESC
                    LIMIT 1";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_furgoneta, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Furgoneta_registro_kilometraje',
                'get_ultimo_registro',
                "Error: " . $e->getMessage(),
                'error'
            );
            return false;
        }
    }

    // =====================================================
    // MÉTODO 5: Obtener kilometraje actual de una furgoneta
    // =====================================================
    public function get_kilometraje_actual($id_furgoneta)
    {
        try {
            $sql = "SELECT MAX(kilometraje_registrado_km) AS kilometraje_actual
                    FROM furgoneta_registro_kilometraje
                    WHERE id_furgoneta = ?";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_furgoneta, PDO::PARAM_INT);
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $resultado ? intval($resultado['kilometraje_actual']) : 0;
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Furgoneta_registro_kilometraje',
                'get_kilometraje_actual',
                "Error: " . $e->getMessage(),
                'error'
            );
            return 0;
        }
    }

    // =====================================================
    // MÉTODO 6: Insertar nuevo registro
    // =====================================================
    public function insert_registro_km(
        $id_furgoneta,
        $fecha_registro,
        $kilometraje_registrado,
        $tipo_registro = 'manual',
        $observaciones = null
    ) {
        try {
            // Validar tipo de registro
            $tipos_validos = ['manual', 'revision', 'itv', 'evento'];
            if (!in_array($tipo_registro, $tipos_validos)) {
                $tipo_registro = 'manual';
            }

            // Validar que el kilometraje no sea menor al último registrado
            $ultimo_km = $this->get_kilometraje_actual($id_furgoneta);
            if ($kilometraje_registrado < $ultimo_km) {
                $this->registro->registrarActividad(
                    'admin',
                    'Furgoneta_registro_kilometraje',
                    'insert_registro_km',
                    "Intento de registrar kilometraje inferior al actual. Actual: $ultimo_km, Nuevo: $kilometraje_registrado",
                    'warning'
                );
                return ['error' => 'El kilometraje registrado no puede ser menor al actual', 'kilometraje_actual' => $ultimo_km];
            }

            $sql = "INSERT INTO furgoneta_registro_kilometraje (
                        id_furgoneta,
                        fecha_registro_km,
                        kilometraje_registrado_km,
                        tipo_registro_km,
                        observaciones_registro_km,
                        created_at_registro_km
                    ) VALUES (?, ?, ?, ?, ?, NOW())";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_furgoneta, PDO::PARAM_INT);
            $stmt->bindValue(2, $fecha_registro, PDO::PARAM_STR);
            $stmt->bindValue(3, $kilometraje_registrado, PDO::PARAM_INT);
            $stmt->bindValue(4, $tipo_registro, PDO::PARAM_STR);
            $stmt->bindValue(5, !empty($observaciones) ? $observaciones : null, !empty($observaciones) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            
            $stmt->execute();
            
            $id = $this->conexion->lastInsertId();
            
            $this->registro->registrarActividad(
                'admin',
                'Furgoneta_registro_kilometraje',
                'insert_registro_km',
                "Registro de kilometraje creado con ID: $id - Furgoneta: $id_furgoneta - KM: $kilometraje_registrado",
                'info'
            );
            
            return $id;
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Furgoneta_registro_kilometraje',
                'insert_registro_km',
                "Error: " . $e->getMessage(),
                'error'
            );
            return false;
        }
    }

    // =====================================================
    // MÉTODO 7: Actualizar registro
    // =====================================================
    public function update_registro_km(
        $id_registro_km,
        $id_furgoneta,
        $fecha_registro,
        $kilometraje_registrado,
        $tipo_registro = 'manual',
        $observaciones = null
    ) {
        try {
            // Validar tipo de registro
            $tipos_validos = ['manual', 'revision', 'itv', 'evento'];
            if (!in_array($tipo_registro, $tipos_validos)) {
                $tipo_registro = 'manual';
            }

            $sql = "UPDATE furgoneta_registro_kilometraje SET 
                        id_furgoneta = ?,
                        fecha_registro_km = ?,
                        kilometraje_registrado_km = ?,
                        tipo_registro_km = ?,
                        observaciones_registro_km = ?
                    WHERE id_registro_km = ?";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_furgoneta, PDO::PARAM_INT);
            $stmt->bindValue(2, $fecha_registro, PDO::PARAM_STR);
            $stmt->bindValue(3, $kilometraje_registrado, PDO::PARAM_INT);
            $stmt->bindValue(4, $tipo_registro, PDO::PARAM_STR);
            $stmt->bindValue(5, !empty($observaciones) ? $observaciones : null, !empty($observaciones) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(6, $id_registro_km, PDO::PARAM_INT);
            
            $stmt->execute();
            
            $this->registro->registrarActividad(
                'admin',
                'Furgoneta_registro_kilometraje',
                'update_registro_km',
                "Registro actualizado ID: $id_registro_km - KM: $kilometraje_registrado",
                'info'
            );
            
            return $stmt->rowCount();
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Furgoneta_registro_kilometraje',
                'update_registro_km',
                "Error: " . $e->getMessage(),
                'error'
            );
            return false;
        }
    }

    // =====================================================
    // MÉTODO 8: Eliminar registro
    // =====================================================
    public function delete_registro_kmxid($id_registro_km)
    {
        try {
            $sql = "DELETE FROM furgoneta_registro_kilometraje 
                    WHERE id_registro_km = ?";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_registro_km, PDO::PARAM_INT);
            $stmt->execute();
            
            $this->registro->registrarActividad(
                'admin',
                'Furgoneta_registro_kilometraje',
                'delete_registro_kmxid',
                "Registro eliminado ID: $id_registro_km",
                'info'
            );
            
            return $stmt->rowCount() > 0;
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Furgoneta_registro_kilometraje',
                'delete_registro_kmxid',
                "Error: " . $e->getMessage(),
                'error'
            );
            return false;
        }
    }

    // =====================================================
    // MÉTODO 9: Obtener registros por tipo
    // =====================================================
    public function get_registros_por_tipo($id_furgoneta, $tipo_registro)
    {
        try {
            $sql = "SELECT 
                        rk.*,
                        f.matricula_furgoneta,
                        f.marca_furgoneta,
                        f.modelo_furgoneta
                    FROM furgoneta_registro_kilometraje rk
                    INNER JOIN furgoneta f ON rk.id_furgoneta = f.id_furgoneta
                    WHERE rk.id_furgoneta = ?
                    AND rk.tipo_registro_km = ?
                    ORDER BY rk.fecha_registro_km DESC";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_furgoneta, PDO::PARAM_INT);
            $stmt->bindValue(2, $tipo_registro, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Furgoneta_registro_kilometraje',
                'get_registros_por_tipo',
                "Error: " . $e->getMessage(),
                'error'
            );
            return [];
        }
    }

    // =====================================================
    // MÉTODO 10: Obtener registros por rango de fechas
    // =====================================================
    public function get_registros_por_fecha($id_furgoneta, $fecha_inicio, $fecha_fin)
    {
        try {
            $sql = "SELECT 
                        rk.*,
                        f.matricula_furgoneta,
                        f.marca_furgoneta,
                        f.modelo_furgoneta
                    FROM furgoneta_registro_kilometraje rk
                    INNER JOIN furgoneta f ON rk.id_furgoneta = f.id_furgoneta
                    WHERE rk.id_furgoneta = ?
                    AND rk.fecha_registro_km BETWEEN ? AND ?
                    ORDER BY rk.fecha_registro_km DESC";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_furgoneta, PDO::PARAM_INT);
            $stmt->bindValue(2, $fecha_inicio, PDO::PARAM_STR);
            $stmt->bindValue(3, $fecha_fin, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Furgoneta_registro_kilometraje',
                'get_registros_por_fecha',
                "Error: " . $e->getMessage(),
                'error'
            );
            return [];
        }
    }

    // =====================================================
    // MÉTODO 11: Calcular kilometraje recorrido en período
    // =====================================================
    public function calcular_km_periodo($id_furgoneta, $fecha_inicio, $fecha_fin)
    {
        try {
            $sql = "SELECT 
                        MAX(kilometraje_registrado_km) AS km_final,
                        MIN(kilometraje_registrado_km) AS km_inicial
                    FROM furgoneta_registro_kilometraje
                    WHERE id_furgoneta = ?
                    AND fecha_registro_km BETWEEN ? AND ?";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_furgoneta, PDO::PARAM_INT);
            $stmt->bindValue(2, $fecha_inicio, PDO::PARAM_STR);
            $stmt->bindValue(3, $fecha_fin, PDO::PARAM_STR);
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($resultado && $resultado['km_final'] && $resultado['km_inicial']) {
                return [
                    'km_inicial' => intval($resultado['km_inicial']),
                    'km_final' => intval($resultado['km_final']),
                    'km_recorridos' => intval($resultado['km_final']) - intval($resultado['km_inicial'])
                ];
            }
            
            return [
                'km_inicial' => 0,
                'km_final' => 0,
                'km_recorridos' => 0
            ];
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Furgoneta_registro_kilometraje',
                'calcular_km_periodo',
                "Error: " . $e->getMessage(),
                'error'
            );
            return [
                'km_inicial' => 0,
                'km_final' => 0,
                'km_recorridos' => 0
            ];
        }
    }

    // =====================================================
    // MÉTODO 12: Calcular kilómetros desde última revisión
    // =====================================================
    public function calcular_km_desde_ultima_revision($id_furgoneta)
    {
        try {
            // Obtener kilometraje de la última revisión
            $sql_revision = "SELECT kilometraje_registrado_km 
                            FROM furgoneta_registro_kilometraje
                            WHERE id_furgoneta = ?
                            AND tipo_registro_km = 'revision'
                            ORDER BY fecha_registro_km DESC
                            LIMIT 1";
            
            $stmt = $this->conexion->prepare($sql_revision);
            $stmt->bindValue(1, $id_furgoneta, PDO::PARAM_INT);
            $stmt->execute();
            $ultima_revision = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Obtener kilometraje actual
            $km_actual = $this->get_kilometraje_actual($id_furgoneta);
            
            if ($ultima_revision) {
                $km_revision = intval($ultima_revision['kilometraje_registrado_km']);
                return [
                    'km_ultima_revision' => $km_revision,
                    'km_actual' => $km_actual,
                    'km_desde_revision' => $km_actual - $km_revision
                ];
            }
            
            return [
                'km_ultima_revision' => 0,
                'km_actual' => $km_actual,
                'km_desde_revision' => $km_actual
            ];
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Furgoneta_registro_kilometraje',
                'calcular_km_desde_ultima_revision',
                "Error: " . $e->getMessage(),
                'error'
            );
            return [
                'km_ultima_revision' => 0,
                'km_actual' => 0,
                'km_desde_revision' => 0
            ];
        }
    }

    // =====================================================
    // MÉTODO 13: Verificar si necesita revisión
    // =====================================================
    public function verificar_necesita_revision($id_furgoneta)
    {
        try {
            // Obtener configuración de km entre revisiones
            $sql_config = "SELECT kilometros_entre_revisiones_furgoneta 
                          FROM furgoneta 
                          WHERE id_furgoneta = ?";
            
            $stmt = $this->conexion->prepare($sql_config);
            $stmt->bindValue(1, $id_furgoneta, PDO::PARAM_INT);
            $stmt->execute();
            $config = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$config) {
                return false;
            }
            
            $km_entre_revisiones = intval($config['kilometros_entre_revisiones_furgoneta']);
            $km_info = $this->calcular_km_desde_ultima_revision($id_furgoneta);
            
            return [
                'necesita_revision' => ($km_info['km_desde_revision'] >= $km_entre_revisiones),
                'km_desde_revision' => $km_info['km_desde_revision'],
                'km_para_revision' => max(0, $km_entre_revisiones - $km_info['km_desde_revision']),
                'km_configurados' => $km_entre_revisiones
            ];
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Furgoneta_registro_kilometraje',
                'verificar_necesita_revision',
                "Error: " . $e->getMessage(),
                'error'
            );
            return false;
        }
    }

    // =====================================================
    // MÉTODO 14: Obtener estadísticas de kilometraje
    // =====================================================
    public function obtener_estadisticas($id_furgoneta)
    {
        try {
            $sql = "SELECT 
                        COUNT(*) AS total_registros,
                        MIN(kilometraje_registrado_km) AS km_minimo,
                        MAX(kilometraje_registrado_km) AS km_maximo,
                        AVG(kilometraje_registrado_km) AS km_promedio,
                        MIN(fecha_registro_km) AS fecha_primer_registro,
                        MAX(fecha_registro_km) AS fecha_ultimo_registro,
                        SUM(CASE WHEN tipo_registro_km = 'manual' THEN 1 ELSE 0 END) AS registros_manuales,
                        SUM(CASE WHEN tipo_registro_km = 'revision' THEN 1 ELSE 0 END) AS registros_revision,
                        SUM(CASE WHEN tipo_registro_km = 'itv' THEN 1 ELSE 0 END) AS registros_itv,
                        SUM(CASE WHEN tipo_registro_km = 'evento' THEN 1 ELSE 0 END) AS registros_evento
                    FROM furgoneta_registro_kilometraje
                    WHERE id_furgoneta = ?";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_furgoneta, PDO::PARAM_INT);
            $stmt->execute();
            $estadisticas = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($estadisticas && $estadisticas['total_registros'] > 0) {
                // Calcular km total recorridos
                $estadisticas['km_totales_recorridos'] = intval($estadisticas['km_maximo']) - intval($estadisticas['km_minimo']);
                
                // Calcular días transcurridos
                if ($estadisticas['fecha_primer_registro'] && $estadisticas['fecha_ultimo_registro']) {
                    $fecha_inicio = new DateTime($estadisticas['fecha_primer_registro']);
                    $fecha_fin = new DateTime($estadisticas['fecha_ultimo_registro']);
                    $dias = $fecha_inicio->diff($fecha_fin)->days;
                    $estadisticas['dias_transcurridos'] = $dias;
                    
                    // Calcular km promedio por día
                    if ($dias > 0) {
                        $estadisticas['km_promedio_por_dia'] = round($estadisticas['km_totales_recorridos'] / $dias, 2);
                    } else {
                        $estadisticas['km_promedio_por_dia'] = 0;
                    }
                }
                
                return $estadisticas;
            }
            
            return [];
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Furgoneta_registro_kilometraje',
                'obtener_estadisticas',
                "Error: " . $e->getMessage(),
                'error'
            );
            return [];
        }
    }

    // =====================================================
    // MÉTODO 15: Obtener histórico completo con cálculos
    // =====================================================
    public function get_historico_completo($id_furgoneta)
    {
        try {
            $sql = "SELECT 
                        rk.*,
                        f.matricula_furgoneta,
                        f.marca_furgoneta,
                        f.modelo_furgoneta,
                        LAG(rk.kilometraje_registrado_km) OVER (ORDER BY rk.fecha_registro_km) AS km_anterior,
                        rk.kilometraje_registrado_km - LAG(rk.kilometraje_registrado_km) OVER (ORDER BY rk.fecha_registro_km) AS km_recorridos_desde_anterior,
                        DATEDIFF(rk.fecha_registro_km, LAG(rk.fecha_registro_km) OVER (ORDER BY rk.fecha_registro_km)) AS dias_desde_anterior
                    FROM furgoneta_registro_kilometraje rk
                    INNER JOIN furgoneta f ON rk.id_furgoneta = f.id_furgoneta
                    WHERE rk.id_furgoneta = ?
                    ORDER BY rk.fecha_registro_km DESC";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_furgoneta, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Furgoneta_registro_kilometraje',
                'get_historico_completo',
                "Error: " . $e->getMessage(),
                'error'
            );
            return [];
        }
    }
}
?>
