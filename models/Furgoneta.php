<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../config/funciones.php';

class Furgoneta
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
                'Furgoneta',
                '__construct',
                "Error zona horaria: " . $e->getMessage(),
                'warning'
            );
        }
    }

    // =====================================================
    // MÉTODOS DE ESTADÍSTICAS
    // =====================================================
    
    /**
     * Obtener el total de furgonetas (activas e inactivas)
     */
    public function total_furgonetas()
    {
        try {
            $sql = "SELECT COUNT(*) AS total FROM furgoneta";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $resultado['total'];
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Furgoneta',
                'total_furgonetas',
                "Error al obtener total de furgonetas: " . $e->getMessage(),
                'error'
            );
            return 0;
        }
    }

    /**
     * Obtener el total de furgonetas activas
     */
    public function total_furgonetas_activas()
    {
        try {
            $sql = "SELECT COUNT(*) AS total FROM furgoneta 
                    WHERE activo_furgoneta = 1";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $resultado['total'];
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Furgoneta',
                'total_furgonetas_activas',
                "Error al obtener total de furgonetas activas: " . $e->getMessage(),
                'error'
            );
            return 0;
        }
    }

    /**
     * Obtener el total de furgonetas operativas (activas y en estado operativa)
     */
    public function total_furgonetas_operativas()
    {
        try {
            $sql = "SELECT COUNT(*) AS total FROM furgoneta 
                    WHERE activo_furgoneta = 1 
                    AND estado_furgoneta = 'operativa'";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $resultado['total'];
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Furgoneta',
                'total_furgonetas_operativas',
                "Error al obtener total de furgonetas operativas: " . $e->getMessage(),
                'error'
            );
            return 0;
        }
    }

    /**
     * Obtener el total de furgonetas en taller (activas y en estado taller)
     */
    public function total_furgonetas_taller()
    {
        try {
            $sql = "SELECT COUNT(*) AS total FROM furgoneta 
                    WHERE activo_furgoneta = 1 
                    AND estado_furgoneta = 'taller'";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $resultado['total'];
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Furgoneta',
                'total_furgonetas_taller',
                "Error al obtener total de furgonetas en taller: " . $e->getMessage(),
                'error'
            );
            return 0;
        }
    }

    // =====================================================
    // MÉTODO 1: Listar todas las furgonetas
    // =====================================================
    public function get_furgonetas()
    {
        try {
            $sql = "SELECT * FROM furgoneta 
                    ORDER BY matricula_furgoneta ASC";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Furgoneta',
                'get_furgonetas',
                "Error al listar furgonetas: " . $e->getMessage(),
                'error'
            );
            return [];
        }
    }

    // =====================================================
    // MÉTODO 2: Listar solo furgonetas activas
    // =====================================================
    public function get_furgonetas_disponibles()
    {
        try {
            $sql = "SELECT * FROM furgoneta 
                    WHERE activo_furgoneta = 1 
                    ORDER BY matricula_furgoneta ASC";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Furgoneta',
                'get_furgonetas_disponibles',
                "Error: " . $e->getMessage(),
                'error'
            );
            return [];
        }
    }

    // =====================================================
    // MÉTODO 3: Listar furgonetas operativas
    // =====================================================
    public function get_furgonetas_operativas()
    {
        try {
            $sql = "SELECT * FROM furgoneta 
                    WHERE activo_furgoneta = 1 
                    AND estado_furgoneta = 'operativa'
                    ORDER BY matricula_furgoneta ASC";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Furgoneta',
                'get_furgonetas_operativas',
                "Error: " . $e->getMessage(),
                'error'
            );
            return [];
        }
    }

    // =====================================================
    // MÉTODO 3B: Listar furgonetas en taller
    // =====================================================
    public function get_furgonetas_en_taller()
    {
        try {
            $sql = "SELECT * FROM furgoneta 
                    WHERE activo_furgoneta = 1 
                    AND estado_furgoneta = 'taller'
                    ORDER BY matricula_furgoneta ASC";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Furgoneta',
                'get_furgonetas_en_taller',
                "Error: " . $e->getMessage(),
                'error'
            );
            return [];
        }
    }

    // =====================================================
    // MÉTODO 4: Obtener furgoneta por ID
    // =====================================================
    public function get_furgonetaxid($id_furgoneta)
    {
        try {
            $sql = "SELECT * FROM furgoneta 
                    WHERE id_furgoneta = ?";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_furgoneta, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Furgoneta',
                'get_furgonetaxid',
                "Error al obtener furgoneta ID {$id_furgoneta}: " . $e->getMessage(),
                'error'
            );
            return false;
        }
    }

    // =====================================================
    // MÉTODO 5: Obtener furgoneta por matrícula
    // =====================================================
    public function get_furgonetaxmatricula($matricula)
    {
        try {
            $sql = "SELECT * FROM furgoneta 
                    WHERE matricula_furgoneta = ?";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $matricula, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Furgoneta',
                'get_furgonetaxmatricula',
                "Error al obtener furgoneta matrícula {$matricula}: " . $e->getMessage(),
                'error'
            );
            return false;
        }
    }

    // =====================================================
    // MÉTODO: Verificar si una furgoneta existe por matrícula
    // =====================================================
    public function verificarFurgoneta($matricula, $id_furgoneta = null)
    {
        try {
            $sql = "SELECT COUNT(*) AS total FROM furgoneta 
                    WHERE LOWER(TRIM(matricula_furgoneta)) = LOWER(TRIM(?))";
            $params = [$matricula];
            
            // Excluir el propio registro en edición
            if (!empty($id_furgoneta)) {
                $sql .= " AND id_furgoneta != ?";
                $params[] = $id_furgoneta;
            }
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute($params);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return [
                'existe' => ($resultado['total'] > 0)
            ];
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Furgoneta',
                'verificarFurgoneta',
                "Error al verificar matrícula: " . $e->getMessage(),
                'error'
            );
            return [
                'existe' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    // =====================================================
    // MÉTODO 6: Insertar nueva furgoneta
    // =====================================================
    public function insert_furgoneta(
        $matricula,
        $marca = null,
        $modelo = null,
        $anio = null,
        $numero_bastidor = null,
        $kilometros_entre_revisiones = 10000,
        $fecha_proxima_itv = null,
        $fecha_vencimiento_seguro = null,
        $compania_seguro = null,
        $numero_poliza_seguro = null,
        $capacidad_carga_kg = null,
        $capacidad_carga_m3 = null,
        $tipo_combustible = null,
        $consumo_medio = null,
        $taller_habitual = null,
        $telefono_taller = null,
        $estado = 'operativa',
        $observaciones = null
    ) {
        try {
            $sql = "INSERT INTO furgoneta (
                        matricula_furgoneta,
                        marca_furgoneta,
                        modelo_furgoneta,
                        anio_furgoneta,
                        numero_bastidor_furgoneta,
                        kilometros_entre_revisiones_furgoneta,
                        fecha_proxima_itv_furgoneta,
                        fecha_vencimiento_seguro_furgoneta,
                        compania_seguro_furgoneta,
                        numero_poliza_seguro_furgoneta,
                        capacidad_carga_kg_furgoneta,
                        capacidad_carga_m3_furgoneta,
                        tipo_combustible_furgoneta,
                        consumo_medio_furgoneta,
                        taller_habitual_furgoneta,
                        telefono_taller_furgoneta,
                        estado_furgoneta,
                        observaciones_furgoneta,
                        created_at_furgoneta
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $matricula, PDO::PARAM_STR);
            $stmt->bindValue(2, !empty($marca) ? $marca : null, !empty($marca) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(3, !empty($modelo) ? $modelo : null, !empty($modelo) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(4, !empty($anio) ? $anio : null, !empty($anio) ? PDO::PARAM_INT : PDO::PARAM_NULL);
            $stmt->bindValue(5, !empty($numero_bastidor) ? $numero_bastidor : null, !empty($numero_bastidor) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(6, $kilometros_entre_revisiones, PDO::PARAM_INT);
            $stmt->bindValue(7, !empty($fecha_proxima_itv) ? $fecha_proxima_itv : null, !empty($fecha_proxima_itv) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(8, !empty($fecha_vencimiento_seguro) ? $fecha_vencimiento_seguro : null, !empty($fecha_vencimiento_seguro) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(9, !empty($compania_seguro) ? $compania_seguro : null, !empty($compania_seguro) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(10, !empty($numero_poliza_seguro) ? $numero_poliza_seguro : null, !empty($numero_poliza_seguro) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(11, !empty($capacidad_carga_kg) ? $capacidad_carga_kg : null, !empty($capacidad_carga_kg) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(12, !empty($capacidad_carga_m3) ? $capacidad_carga_m3 : null, !empty($capacidad_carga_m3) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(13, !empty($tipo_combustible) ? $tipo_combustible : null, !empty($tipo_combustible) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(14, !empty($consumo_medio) ? $consumo_medio : null, !empty($consumo_medio) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(15, !empty($taller_habitual) ? $taller_habitual : null, !empty($taller_habitual) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(16, !empty($telefono_taller) ? $telefono_taller : null, !empty($telefono_taller) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(17, $estado, PDO::PARAM_STR);
            $stmt->bindValue(18, !empty($observaciones) ? $observaciones : null, !empty($observaciones) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            
            $stmt->execute();
            
            $id = $this->conexion->lastInsertId();
            
            $this->registro->registrarActividad(
                'admin',
                'Furgoneta',
                'insert_furgoneta',
                "Furgoneta creada con ID: $id - Matrícula: $matricula",
                'info'
            );
            
            return $id;
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Furgoneta',
                'insert_furgoneta',
                "Error: " . $e->getMessage(),
                'error'
            );
            return false;
        }
    }

    // =====================================================
    // MÉTODO 7: Actualizar furgoneta
    // =====================================================
    public function update_furgoneta(
        $id_furgoneta,
        $matricula,
        $marca = null,
        $modelo = null,
        $anio = null,
        $numero_bastidor = null,
        $kilometros_entre_revisiones = 10000,
        $fecha_proxima_itv = null,
        $fecha_vencimiento_seguro = null,
        $compania_seguro = null,
        $numero_poliza_seguro = null,
        $capacidad_carga_kg = null,
        $capacidad_carga_m3 = null,
        $tipo_combustible = null,
        $consumo_medio = null,
        $taller_habitual = null,
        $telefono_taller = null,
        $estado = 'operativa',
        $observaciones = null
    ) {
        try {
            $sql = "UPDATE furgoneta SET 
                        matricula_furgoneta = ?,
                        marca_furgoneta = ?,
                        modelo_furgoneta = ?,
                        anio_furgoneta = ?,
                        numero_bastidor_furgoneta = ?,
                        kilometros_entre_revisiones_furgoneta = ?,
                        fecha_proxima_itv_furgoneta = ?,
                        fecha_vencimiento_seguro_furgoneta = ?,
                        compania_seguro_furgoneta = ?,
                        numero_poliza_seguro_furgoneta = ?,
                        capacidad_carga_kg_furgoneta = ?,
                        capacidad_carga_m3_furgoneta = ?,
                        tipo_combustible_furgoneta = ?,
                        consumo_medio_furgoneta = ?,
                        taller_habitual_furgoneta = ?,
                        telefono_taller_furgoneta = ?,
                        estado_furgoneta = ?,
                        observaciones_furgoneta = ?,
                        updated_at_furgoneta = NOW()
                    WHERE id_furgoneta = ?";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $matricula, PDO::PARAM_STR);
            $stmt->bindValue(2, !empty($marca) ? $marca : null, !empty($marca) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(3, !empty($modelo) ? $modelo : null, !empty($modelo) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(4, !empty($anio) ? $anio : null, !empty($anio) ? PDO::PARAM_INT : PDO::PARAM_NULL);
            $stmt->bindValue(5, !empty($numero_bastidor) ? $numero_bastidor : null, !empty($numero_bastidor) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(6, $kilometros_entre_revisiones, PDO::PARAM_INT);
            $stmt->bindValue(7, !empty($fecha_proxima_itv) ? $fecha_proxima_itv : null, !empty($fecha_proxima_itv) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(8, !empty($fecha_vencimiento_seguro) ? $fecha_vencimiento_seguro : null, !empty($fecha_vencimiento_seguro) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(9, !empty($compania_seguro) ? $compania_seguro : null, !empty($compania_seguro) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(10, !empty($numero_poliza_seguro) ? $numero_poliza_seguro : null, !empty($numero_poliza_seguro) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(11, !empty($capacidad_carga_kg) ? $capacidad_carga_kg : null, !empty($capacidad_carga_kg) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(12, !empty($capacidad_carga_m3) ? $capacidad_carga_m3 : null, !empty($capacidad_carga_m3) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(13, !empty($tipo_combustible) ? $tipo_combustible : null, !empty($tipo_combustible) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(14, !empty($consumo_medio) ? $consumo_medio : null, !empty($consumo_medio) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(15, !empty($taller_habitual) ? $taller_habitual : null, !empty($taller_habitual) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(16, !empty($telefono_taller) ? $telefono_taller : null, !empty($telefono_taller) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(17, $estado, PDO::PARAM_STR);
            $stmt->bindValue(18, !empty($observaciones) ? $observaciones : null, !empty($observaciones) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(19, $id_furgoneta, PDO::PARAM_INT);
            
            $stmt->execute();
            
            $this->registro->registrarActividad(
                'admin',
                'Furgoneta',
                'update_furgoneta',
                "Furgoneta actualizada ID: $id_furgoneta - Matrícula: $matricula",
                'info'
            );
            
            return $stmt->rowCount();
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Furgoneta',
                'update_furgoneta',
                "Error: " . $e->getMessage(),
                'error'
            );
            return false;
        }
    }

    // =====================================================
    // MÉTODO 8: Eliminar (SOFT DELETE)
    // =====================================================
    public function delete_furgonetaxid($id_furgoneta)
    {
        try {
            $sql = "UPDATE furgoneta SET 
                        activo_furgoneta = 0,
                        updated_at_furgoneta = NOW()
                    WHERE id_furgoneta = ?";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_furgoneta, PDO::PARAM_INT);
            $stmt->execute();
            
            $this->registro->registrarActividad(
                'admin',
                'Furgoneta',
                'delete_furgonetaxid',
                "Furgoneta desactivada ID: $id_furgoneta",
                'info'
            );
            
            return $stmt->rowCount() > 0;
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Furgoneta',
                'delete_furgonetaxid',
                "Error: " . $e->getMessage(),
                'error'
            );
            return false;
        }
    }

    // =====================================================
    // MÉTODO 9: Activar furgoneta
    // =====================================================
    public function activar_furgonetaxid($id_furgoneta)
    {
        try {
            $sql = "UPDATE furgoneta SET 
                        activo_furgoneta = 1,
                        updated_at_furgoneta = NOW()
                    WHERE id_furgoneta = ?";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_furgoneta, PDO::PARAM_INT);
            $stmt->execute();
            
            $this->registro->registrarActividad(
                'admin',
                'Furgoneta',
                'activar_furgonetaxid',
                "Furgoneta activada ID: $id_furgoneta",
                'info'
            );
            
            return $stmt->rowCount() > 0;
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Furgoneta',
                'activar_furgonetaxid',
                "Error: " . $e->getMessage(),
                'error'
            );
            return false;
        }
    }

    // =====================================================
    // MÉTODO 10: Verificar existencia de matrícula
    // =====================================================
    public function verificarMatricula($matricula, $id_furgoneta = null)
    {
        try {
            $sql = "SELECT COUNT(*) AS total FROM furgoneta 
                    WHERE LOWER(matricula_furgoneta) = LOWER(?)";
            $params = [trim($matricula)];
            
            // Excluir el propio registro en edición
            if (!empty($id_furgoneta)) {
                $sql .= " AND id_furgoneta != ?";
                $params[] = $id_furgoneta;
            }
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute($params);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return [
                'existe' => ($resultado['total'] > 0)
            ];
            
        } catch (PDOException $e) {
            return [
                'existe' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    // =====================================================
    // MÉTODO 11: Cambiar estado de furgoneta
    // =====================================================
    public function cambiar_estado($id_furgoneta, $nuevo_estado)
    {
        try {
            // Validar que el estado sea válido
            $estados_validos = ['operativa', 'taller', 'baja'];
            if (!in_array($nuevo_estado, $estados_validos)) {
                return false;
            }

            $sql = "UPDATE furgoneta SET 
                        estado_furgoneta = ?,
                        updated_at_furgoneta = NOW()
                    WHERE id_furgoneta = ?";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $nuevo_estado, PDO::PARAM_STR);
            $stmt->bindValue(2, $id_furgoneta, PDO::PARAM_INT);
            $stmt->execute();
            
            $this->registro->registrarActividad(
                'admin',
                'Furgoneta',
                'cambiar_estado',
                "Estado cambiado a '$nuevo_estado' para furgoneta ID: $id_furgoneta",
                'info'
            );
            
            return $stmt->rowCount() > 0;
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Furgoneta',
                'cambiar_estado',
                "Error: " . $e->getMessage(),
                'error'
            );
            return false;
        }
    }

    // =====================================================
    // MÉTODO 12: Obtener furgonetas próximas a ITV
    // =====================================================
    public function get_furgonetas_proximas_itv($dias = 30)
    {
        try {
            $sql = "SELECT * FROM furgoneta 
                    WHERE activo_furgoneta = 1 
                    AND fecha_proxima_itv_furgoneta IS NOT NULL
                    AND fecha_proxima_itv_furgoneta <= DATE_ADD(CURDATE(), INTERVAL ? DAY)
                    AND fecha_proxima_itv_furgoneta >= CURDATE()
                    ORDER BY fecha_proxima_itv_furgoneta ASC";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $dias, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Furgoneta',
                'get_furgonetas_proximas_itv',
                "Error: " . $e->getMessage(),
                'error'
            );
            return [];
        }
    }

    // =====================================================
    // MÉTODO 13: Obtener furgonetas con seguro próximo a vencer
    // =====================================================
    public function get_furgonetas_seguro_proximo($dias = 30)
    {
        try {
            $sql = "SELECT * FROM furgoneta 
                    WHERE activo_furgoneta = 1 
                    AND fecha_vencimiento_seguro_furgoneta IS NOT NULL
                    AND fecha_vencimiento_seguro_furgoneta <= DATE_ADD(CURDATE(), INTERVAL ? DAY)
                    AND fecha_vencimiento_seguro_furgoneta >= CURDATE()
                    ORDER BY fecha_vencimiento_seguro_furgoneta ASC";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $dias, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Furgoneta',
                'get_furgonetas_seguro_proximo',
                "Error: " . $e->getMessage(),
                'error'
            );
            return [];
        }
    }

    // =====================================================
    // MÉTODO 14: Obtener estadísticas de furgonetas
    // =====================================================
    public function obtenerEstadisticas()
    {
        try {
            $sql = "SELECT 
                        COUNT(*) AS total,
                        SUM(CASE WHEN activo_furgoneta = 1 THEN 1 ELSE 0 END) AS activas,
                        SUM(CASE WHEN activo_furgoneta = 1 AND estado_furgoneta = 'operativa' THEN 1 ELSE 0 END) AS operativas,
                        SUM(CASE WHEN activo_furgoneta = 1 AND estado_furgoneta = 'taller' THEN 1 ELSE 0 END) AS taller,
                        SUM(CASE WHEN estado_furgoneta = 'baja' THEN 1 ELSE 0 END) AS baja,
                        AVG(YEAR(CURDATE()) - anio_furgoneta) AS edad_media_anios,
                        SUM(capacidad_carga_kg_furgoneta) AS capacidad_total_kg,
                        SUM(capacidad_carga_m3_furgoneta) AS capacidad_total_m3
                    FROM furgoneta";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Asegurar que los valores numéricos no sean null
            return [
                'total' => (int)($resultado['total'] ?? 0),
                'activas' => (int)($resultado['activas'] ?? 0),
                'operativas' => (int)($resultado['operativas'] ?? 0),
                'taller' => (int)($resultado['taller'] ?? 0),
                'baja' => (int)($resultado['baja'] ?? 0),
                'edad_media_anios' => round($resultado['edad_media_anios'] ?? 0, 1),
                'capacidad_total_kg' => (float)($resultado['capacidad_total_kg'] ?? 0),
                'capacidad_total_m3' => (float)($resultado['capacidad_total_m3'] ?? 0)
            ];
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Furgoneta',
                'obtenerEstadisticas',
                "Error: " . $e->getMessage(),
                'error'
            );
            return [
                'total' => 0,
                'activas' => 0,
                'operativas' => 0,
                'taller' => 0,
                'baja' => 0,
                'edad_media_anios' => 0,
                'capacidad_total_kg' => 0,
                'capacidad_total_m3' => 0
            ];
        }
    }

    // =====================================================
    // MÉTODO 15: Obtener alertas de furgonetas
    // =====================================================
    public function obtenerAlertas()
    {
        try {
            $sql = "SELECT 
                        SUM(CASE 
                            WHEN activo_furgoneta = 1 
                            AND fecha_proxima_itv_furgoneta IS NOT NULL 
                            AND DATEDIFF(fecha_proxima_itv_furgoneta, CURDATE()) <= 30 
                            AND DATEDIFF(fecha_proxima_itv_furgoneta, CURDATE()) >= 0 
                            THEN 1 ELSE 0 
                        END) AS itv_proximas_vencer,
                        
                        SUM(CASE 
                            WHEN activo_furgoneta = 1 
                            AND fecha_vencimiento_seguro_furgoneta IS NOT NULL 
                            AND DATEDIFF(fecha_vencimiento_seguro_furgoneta, CURDATE()) <= 30 
                            AND DATEDIFF(fecha_vencimiento_seguro_furgoneta, CURDATE()) >= 0 
                            THEN 1 ELSE 0 
                        END) AS seguros_proximos_vencer,
                        
                        SUM(CASE 
                            WHEN activo_furgoneta = 1 
                            AND (fecha_vencimiento_seguro_furgoneta IS NULL 
                                 OR compania_seguro_furgoneta IS NULL 
                                 OR numero_poliza_seguro_furgoneta IS NULL) 
                            THEN 1 ELSE 0 
                        END) AS sin_datos_seguro,
                        
                        SUM(CASE 
                            WHEN activo_furgoneta = 1 
                            AND fecha_proxima_itv_furgoneta IS NULL 
                            THEN 1 ELSE 0 
                        END) AS sin_fecha_itv
                    FROM furgoneta";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return [
                'itv_proximas_vencer' => (int)($resultado['itv_proximas_vencer'] ?? 0),
                'seguros_proximos_vencer' => (int)($resultado['seguros_proximos_vencer'] ?? 0),
                'sin_datos_seguro' => (int)($resultado['sin_datos_seguro'] ?? 0),
                'sin_fecha_itv' => (int)($resultado['sin_fecha_itv'] ?? 0)
            ];
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Furgoneta',
                'obtenerAlertas',
                "Error: " . $e->getMessage(),
                'error'
            );
            return [
                'itv_proximas_vencer' => 0,
                'seguros_proximos_vencer' => 0,
                'sin_datos_seguro' => 0,
                'sin_fecha_itv' => 0
            ];
        }
    }
}
?>
