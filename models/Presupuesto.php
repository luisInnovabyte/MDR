<?php

require_once '../config/conexion.php';
require_once '../config/funciones.php';


//CREATE TABLE presupuesto (
//    id_presupuesto INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
//    numero_presupuesto VARCHAR(50) NOT NULL UNIQUE,
//    id_cliente INT UNSIGNED NOT NULL,
//    id_contacto_cliente INT UNSIGNED,
//    id_estado_ppto INT UNSIGNED NOT NULL,
//    id_forma_pago INT UNSIGNED,
//    id_metodo INT UNSIGNED,
//    
//    -- =====================================================
//    -- FECHAS
//    -- =====================================================
//    fecha_presupuesto DATE NOT NULL 
//        COMMENT 'Fecha de emision del presupuesto',
//    
//    fecha_validez_presupuesto DATE 
//        COMMENT 'Fecha hasta la que es valido el presupuesto',
//    
//    fecha_inicio_evento_presupuesto DATE 
//        COMMENT 'Fecha de inicio del evento/servicio',
//    
//    fecha_fin_evento_presupuesto DATE 
//        COMMENT 'Fecha de finalizacion del evento/servicio',
//    
//    -- =====================================================
//    -- DATOS DEL EVENTO/PROYECTO
//    -- =====================================================
//    numero_pedido_cliente_presupuesto VARCHAR(80) 
//        COMMENT 'Numero de pedido del cliente (si lo proporciona)',
//    
//    nombre_evento_presupuesto VARCHAR(255) 
//        COMMENT 'Nombre del evento o proyecto',
//    
//    -- Ubicacion del evento (4 campos separados)
//    direccion_evento_presupuesto VARCHAR(100) 
//        COMMENT 'Direccion del evento',
//    
//    poblacion_evento_presupuesto VARCHAR(80) 
//        COMMENT 'Poblacion/Ciudad del evento',
//    
//    cp_evento_presupuesto VARCHAR(10) 
//        COMMENT 'Codigo postal del evento',
//    
//    provincia_evento_presupuesto VARCHAR(80) 
//        COMMENT 'Provincia del evento',
//    
//    -- =====================================================
//    -- OBSERVACIONES ESPECIFICAS DEL PRESUPUESTO
//    -- =====================================================
//    observaciones_cabecera_presupuesto TEXT 
//        COMMENT 'Observaciones iniciales del presupuesto',
//    
//    observaciones_cabecera_ingles_presupuesto TEXT 
//        COMMENT 'Observaciones iniciales del presupuesto en inglés',
//    
//    observaciones_pie_presupuesto TEXT 
//        COMMENT 'Observaciones especificas adicionales al pie',
//    
//    observaciones_pie_ingles_presupuesto TEXT 
//        COMMENT 'Observaciones específicas adicionales al pie en inglés',
//    
//    mostrar_obs_familias_presupuesto BOOLEAN DEFAULT TRUE 
//        COMMENT 'Si TRUE, muestra observaciones de las familias usadas',
//    
//    mostrar_obs_articulos_presupuesto BOOLEAN DEFAULT TRUE 
//        COMMENT 'Si TRUE, muestra observaciones de los articulos usados',
//    
//    observaciones_internas_presupuesto TEXT 
//        COMMENT 'Notas internas, no se imprimen en el PDF',
//    
//    -- =====================================================
//    -- CONTROL
//    -- =====================================================
//    activo_presupuesto BOOLEAN DEFAULT TRUE,
//    created_at_presupuesto TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
//    updated_at_presupuesto TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
//    
//    -- =====================================================
//    -- CLAVES FORANEAS
//    -- =====================================================
//    CONSTRAINT fk_presupuesto_cliente FOREIGN KEY (id_cliente) 
//        REFERENCES cliente(id_cliente) 
//        ON DELETE RESTRICT 
//        ON UPDATE CASCADE,
//    
//    CONSTRAINT fk_presupuesto_contacto FOREIGN KEY (id_contacto_cliente) 
//        REFERENCES contacto_cliente(id_contacto_cliente) 
//        ON DELETE SET NULL 
//        ON UPDATE CASCADE,
//    
//    CONSTRAINT fk_presupuesto_estado FOREIGN KEY (id_estado_ppto) 
//        REFERENCES estado_presupuesto(id_estado_ppto) 
//        ON DELETE RESTRICT 
//        ON UPDATE CASCADE,
//    
//    CONSTRAINT fk_presupuesto_forma_pago FOREIGN KEY (id_forma_pago) 
//        REFERENCES forma_pago(id_pago) 
//        ON DELETE SET NULL 
//        ON UPDATE CASCADE,
//    
//    CONSTRAINT fk_presupuesto_metodo_contacto FOREIGN KEY (id_metodo) 
//        REFERENCES metodos_contacto(id_metodo_contacto) 
//        ON DELETE SET NULL 
//        ON UPDATE CASCADE,
//    
//    -- =====================================================
//    -- INDICES DE OPTIMIZACION
//    -- =====================================================
//    INDEX idx_numero_presupuesto (numero_presupuesto),
//    INDEX idx_id_cliente_presupuesto (id_cliente),
//    INDEX idx_id_estado_presupuesto (id_estado_ppto),
//    INDEX idx_fecha_presupuesto (fecha_presupuesto),
//    INDEX idx_fecha_inicio_evento (fecha_inicio_evento_presupuesto),
//    INDEX idx_fecha_fin_evento (fecha_fin_evento_presupuesto),
//    INDEX idx_numero_pedido_cliente (numero_pedido_cliente_presupuesto),
//    INDEX idx_poblacion_evento (poblacion_evento_presupuesto),
//    INDEX idx_provincia_evento (provincia_evento_presupuesto)
//    
//) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 
//COMMENT='Cabecera de presupuestos para alquiler de equipos';


class Presupuesto
{
    private $conexion;
    private $registro;

    public function __construct()
    {
        $this->conexion = (new Conexion())->getConexion();
        $this->registro = new RegistroActividad();
        
        // Configurar zona horaria Madrid para todas las operaciones
        try {
            $this->conexion->exec("SET time_zone = 'Europe/Madrid'");
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'system',
                'Presupuesto',
                '__construct',
                "No se pudo establecer zona horaria Madrid: " . $e->getMessage(),
                'warning'
            );
        }
    }

    public function get_presupuestos()
    {
        try {
            $sql = "SELECT * FROM vista_presupuesto_completa ORDER BY fecha_presupuesto DESC";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Presupuesto',
                'get_presupuestos',
                "Error al listar los presupuestos: " . $e->getMessage(),
                "error"
            );
        }
    }

    public function get_presupuestos_disponibles()
    {
        try {
            $sql = "SELECT * FROM vista_presupuesto_completa WHERE activo_presupuesto = 1 ORDER BY fecha_presupuesto DESC";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Presupuesto',
                'get_presupuestos_disponibles',
                "Error al listar los presupuestos disponibles: " . $e->getMessage(),
                "error"
            );
        }
    }

    public function get_presupuestoxid($id_presupuesto)
    {
        try {
            $sql = "SELECT * FROM vista_presupuesto_completa WHERE id_presupuesto = ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_presupuesto, PDO::PARAM_INT);
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $resultado;
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Presupuesto',
                'get_presupuestoxid',
                "Error al mostrar el presupuesto {$id_presupuesto}: " . $e->getMessage(),
                "error"
            );

            return false;
        }
    }

    public function delete_presupuestoxid($id_presupuesto)
    {
        try {
            $sql = "UPDATE presupuesto SET activo_presupuesto = 0 WHERE id_presupuesto = ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_presupuesto, PDO::PARAM_INT);
            $stmt->execute();

            $this->registro->registrarActividad(
                'admin',
                'Presupuesto',
                'Desactivar',
                "Se desactivó el presupuesto con ID: $id_presupuesto",
                'info'
            );
            
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Presupuesto',
                'delete_presupuestoxid',
                "Error al desactivar el presupuesto {$id_presupuesto}: " . $e->getMessage(),
                'error'
            );

            return false;
        }
    }

    public function activar_presupuestoxid($id_presupuesto)
    {
        try {
            $sql = "UPDATE presupuesto SET activo_presupuesto = 1 WHERE id_presupuesto = ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_presupuesto, PDO::PARAM_INT);
            $stmt->execute();

            $this->registro->registrarActividad(
                'admin',
                'Presupuesto',
                'Activar',
                "Se activó el presupuesto con ID: $id_presupuesto",
                'info'
            );
            
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Presupuesto',
                'activar_presupuestoxid',
                "Error al activar el presupuesto {$id_presupuesto}: " . $e->getMessage(),
                "error"
            );

            return false;
        }
    }

    public function desactivar_presupuestoxid($id_presupuesto)
    {
        try {
            $sql = "UPDATE presupuesto SET activo_presupuesto = 0 WHERE id_presupuesto = ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_presupuesto, PDO::PARAM_INT);
            $stmt->execute();

            $this->registro->registrarActividad(
                'admin',
                'Presupuesto',
                'Desactivar',
                "Se desactivó el presupuesto con ID: $id_presupuesto",
                'info'
            );
            
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Presupuesto',
                'desactivar_presupuestoxid',
                "Error al desactivar el presupuesto {$id_presupuesto}: " . $e->getMessage(),
                "error"
            );

            return false;
        }
    }

    public function insert_presupuesto($numero_presupuesto, $id_cliente, $id_contacto_cliente, $id_estado_ppto, $id_forma_pago, $id_metodo, $fecha_presupuesto, $fecha_validez_presupuesto, $fecha_inicio_evento_presupuesto, $fecha_fin_evento_presupuesto, $numero_pedido_cliente_presupuesto, $aplicar_coeficientes_presupuesto, $nombre_evento_presupuesto, $direccion_evento_presupuesto, $poblacion_evento_presupuesto, $cp_evento_presupuesto, $provincia_evento_presupuesto, $observaciones_cabecera_presupuesto, $observaciones_cabecera_ingles_presupuesto, $observaciones_pie_presupuesto, $observaciones_pie_ingles_presupuesto, $mostrar_obs_familias_presupuesto, $mostrar_obs_articulos_presupuesto, $observaciones_internas_presupuesto)
    {
        try {
            $this->conexion->exec("SET time_zone = 'Europe/Madrid'");
            
            $sql = "INSERT INTO presupuesto (numero_presupuesto, id_cliente, id_contacto_cliente, id_estado_ppto, id_forma_pago, id_metodo, 
                    fecha_presupuesto, fecha_validez_presupuesto, fecha_inicio_evento_presupuesto, fecha_fin_evento_presupuesto, 
                    numero_pedido_cliente_presupuesto, aplicar_coeficientes_presupuesto, nombre_evento_presupuesto, direccion_evento_presupuesto, 
                    poblacion_evento_presupuesto, cp_evento_presupuesto, provincia_evento_presupuesto, 
                    observaciones_cabecera_presupuesto, observaciones_cabecera_ingles_presupuesto, observaciones_pie_presupuesto, 
                    observaciones_pie_ingles_presupuesto, mostrar_obs_familias_presupuesto, mostrar_obs_articulos_presupuesto, 
                    observaciones_internas_presupuesto, activo_presupuesto, created_at_presupuesto, updated_at_presupuesto) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, NOW(), NOW())";
            
            $stmt = $this->conexion->prepare($sql);
            
            if (!$stmt) {
                throw new Exception("Error al preparar la consulta SQL");
            }
            
            $stmt->bindValue(1, $numero_presupuesto, PDO::PARAM_STR);
            $stmt->bindValue(2, $id_cliente, PDO::PARAM_INT);
            
            if (!empty($id_contacto_cliente)) {
                $stmt->bindValue(3, $id_contacto_cliente, PDO::PARAM_INT);
            } else {
                $stmt->bindValue(3, null, PDO::PARAM_NULL);
            }
            
            $stmt->bindValue(4, $id_estado_ppto, PDO::PARAM_INT);
            
            if (!empty($id_forma_pago)) {
                $stmt->bindValue(5, $id_forma_pago, PDO::PARAM_INT);
            } else {
                $stmt->bindValue(5, null, PDO::PARAM_NULL);
            }
            
            if (!empty($id_metodo)) {
                $stmt->bindValue(6, $id_metodo, PDO::PARAM_INT);
            } else {
                $stmt->bindValue(6, null, PDO::PARAM_NULL);
            }
            
            $stmt->bindValue(7, $fecha_presupuesto, PDO::PARAM_STR);
            
            if (!empty($fecha_validez_presupuesto)) {
                $stmt->bindValue(8, $fecha_validez_presupuesto, PDO::PARAM_STR);
            } else {
                $stmt->bindValue(8, null, PDO::PARAM_NULL);
            }
            
            if (!empty($fecha_inicio_evento_presupuesto)) {
                $stmt->bindValue(9, $fecha_inicio_evento_presupuesto, PDO::PARAM_STR);
            } else {
                $stmt->bindValue(9, null, PDO::PARAM_NULL);
            }
            
            if (!empty($fecha_fin_evento_presupuesto)) {
                $stmt->bindValue(10, $fecha_fin_evento_presupuesto, PDO::PARAM_STR);
            } else {
                $stmt->bindValue(10, null, PDO::PARAM_NULL);
            }
            
            $stmt->bindValue(11, $numero_pedido_cliente_presupuesto, PDO::PARAM_STR);
            $stmt->bindValue(12, $aplicar_coeficientes_presupuesto, PDO::PARAM_BOOL);
            $stmt->bindValue(13, $nombre_evento_presupuesto, PDO::PARAM_STR);
            $stmt->bindValue(14, $direccion_evento_presupuesto, PDO::PARAM_STR);
            $stmt->bindValue(15, $poblacion_evento_presupuesto, PDO::PARAM_STR);
            $stmt->bindValue(16, $cp_evento_presupuesto, PDO::PARAM_STR);
            $stmt->bindValue(17, $provincia_evento_presupuesto, PDO::PARAM_STR);
            $stmt->bindValue(18, $observaciones_cabecera_presupuesto, PDO::PARAM_STR);
            $stmt->bindValue(19, $observaciones_cabecera_ingles_presupuesto, PDO::PARAM_STR);
            $stmt->bindValue(20, $observaciones_pie_presupuesto, PDO::PARAM_STR);
            $stmt->bindValue(21, $observaciones_pie_ingles_presupuesto, PDO::PARAM_STR);
            $stmt->bindValue(22, $mostrar_obs_familias_presupuesto, PDO::PARAM_BOOL);
            $stmt->bindValue(23, $mostrar_obs_articulos_presupuesto, PDO::PARAM_BOOL);
            $stmt->bindValue(24, $observaciones_internas_presupuesto, PDO::PARAM_STR);
            
            $resultado = $stmt->execute();
            
            if (!$resultado) {
                $errorInfo = $stmt->errorInfo();
                throw new Exception("Error al ejecutar la consulta: " . $errorInfo[2]);
            }
            
            $idInsert = $this->conexion->lastInsertId();

            $this->registro->registrarActividad(
                'admin',
                'Presupuesto',
                'Insertar',
                "Se insertó el presupuesto con ID: $idInsert",
                'info'
            );

            return $idInsert;
            
        } catch (PDOException $e) {
            throw new Exception("Error SQL en insert_presupuesto: " . $e->getMessage());
        } catch (Exception $e) {
            throw new Exception("Error general en insert_presupuesto: " . $e->getMessage());
        }
    }

    public function update_presupuesto($id_presupuesto, $numero_presupuesto, $id_cliente, $id_contacto_cliente, $id_estado_ppto, $id_forma_pago, $id_metodo, $fecha_presupuesto, $fecha_validez_presupuesto, $fecha_inicio_evento_presupuesto, $fecha_fin_evento_presupuesto, $numero_pedido_cliente_presupuesto, $aplicar_coeficientes_presupuesto, $nombre_evento_presupuesto, $direccion_evento_presupuesto, $poblacion_evento_presupuesto, $cp_evento_presupuesto, $provincia_evento_presupuesto, $observaciones_cabecera_presupuesto, $observaciones_cabecera_ingles_presupuesto, $observaciones_pie_presupuesto, $observaciones_pie_ingles_presupuesto, $mostrar_obs_familias_presupuesto, $mostrar_obs_articulos_presupuesto, $observaciones_internas_presupuesto)
    {
        try {
            $this->conexion->exec("SET time_zone = 'Europe/Madrid'");
            
            $sql = "UPDATE presupuesto SET numero_presupuesto = ?, id_cliente = ?, id_contacto_cliente = ?, id_estado_ppto = ?, 
                    id_forma_pago = ?, id_metodo = ?, fecha_presupuesto = ?, fecha_validez_presupuesto = ?, 
                    fecha_inicio_evento_presupuesto = ?, fecha_fin_evento_presupuesto = ?, numero_pedido_cliente_presupuesto = ?, 
                    aplicar_coeficientes_presupuesto = ?, nombre_evento_presupuesto = ?, direccion_evento_presupuesto = ?, poblacion_evento_presupuesto = ?, 
                    cp_evento_presupuesto = ?, provincia_evento_presupuesto = ?, observaciones_cabecera_presupuesto = ?, 
                    observaciones_cabecera_ingles_presupuesto = ?, observaciones_pie_presupuesto = ?, 
                    observaciones_pie_ingles_presupuesto = ?, mostrar_obs_familias_presupuesto = ?, 
                    mostrar_obs_articulos_presupuesto = ?, observaciones_internas_presupuesto = ?, 
                    updated_at_presupuesto = NOW() WHERE id_presupuesto = ?";
            
            $stmt = $this->conexion->prepare($sql);
            
            if (!$stmt) {
                throw new Exception("Error al preparar la consulta de actualización");
            }
            
            $stmt->bindValue(1, $numero_presupuesto, PDO::PARAM_STR);
            $stmt->bindValue(2, $id_cliente, PDO::PARAM_INT);
            
            if (!empty($id_contacto_cliente)) {
                $stmt->bindValue(3, $id_contacto_cliente, PDO::PARAM_INT);
            } else {
                $stmt->bindValue(3, null, PDO::PARAM_NULL);
            }
            
            $stmt->bindValue(4, $id_estado_ppto, PDO::PARAM_INT);
            
            if (!empty($id_forma_pago)) {
                $stmt->bindValue(5, $id_forma_pago, PDO::PARAM_INT);
            } else {
                $stmt->bindValue(5, null, PDO::PARAM_NULL);
            }
            
            if (!empty($id_metodo)) {
                $stmt->bindValue(6, $id_metodo, PDO::PARAM_INT);
            } else {
                $stmt->bindValue(6, null, PDO::PARAM_NULL);
            }
            
            $stmt->bindValue(7, $fecha_presupuesto, PDO::PARAM_STR);
            
            if (!empty($fecha_validez_presupuesto)) {
                $stmt->bindValue(8, $fecha_validez_presupuesto, PDO::PARAM_STR);
            } else {
                $stmt->bindValue(8, null, PDO::PARAM_NULL);
            }
            
            if (!empty($fecha_inicio_evento_presupuesto)) {
                $stmt->bindValue(9, $fecha_inicio_evento_presupuesto, PDO::PARAM_STR);
            } else {
                $stmt->bindValue(9, null, PDO::PARAM_NULL);
            }
            
            if (!empty($fecha_fin_evento_presupuesto)) {
                $stmt->bindValue(10, $fecha_fin_evento_presupuesto, PDO::PARAM_STR);
            } else {
                $stmt->bindValue(10, null, PDO::PARAM_NULL);
            }
            
            $stmt->bindValue(11, $numero_pedido_cliente_presupuesto, PDO::PARAM_STR);
            $stmt->bindValue(12, $aplicar_coeficientes_presupuesto, PDO::PARAM_BOOL);
            $stmt->bindValue(13, $nombre_evento_presupuesto, PDO::PARAM_STR);
            $stmt->bindValue(14, $direccion_evento_presupuesto, PDO::PARAM_STR);
            $stmt->bindValue(15, $poblacion_evento_presupuesto, PDO::PARAM_STR);
            $stmt->bindValue(16, $cp_evento_presupuesto, PDO::PARAM_STR);
            $stmt->bindValue(17, $provincia_evento_presupuesto, PDO::PARAM_STR);
            $stmt->bindValue(18, $observaciones_cabecera_presupuesto, PDO::PARAM_STR);
            $stmt->bindValue(19, $observaciones_cabecera_ingles_presupuesto, PDO::PARAM_STR);
            $stmt->bindValue(20, $observaciones_pie_presupuesto, PDO::PARAM_STR);
            $stmt->bindValue(21, $observaciones_pie_ingles_presupuesto, PDO::PARAM_STR);
            $stmt->bindValue(22, $mostrar_obs_familias_presupuesto, PDO::PARAM_BOOL);
            $stmt->bindValue(23, $mostrar_obs_articulos_presupuesto, PDO::PARAM_BOOL);
            $stmt->bindValue(24, $observaciones_internas_presupuesto, PDO::PARAM_STR);
            $stmt->bindValue(25, $id_presupuesto, PDO::PARAM_INT);

            $resultado = $stmt->execute();
            
            if (!$resultado) {
                $errorInfo = $stmt->errorInfo();
                throw new Exception("Error al ejecutar la actualización: " . $errorInfo[2]);
            }

            $this->registro->registrarActividad(
                'admin',
                'Presupuesto',
                'Actualizar',
                "Se actualizó el presupuesto con ID: $id_presupuesto",
                'info'
            );

            return $stmt->rowCount();

        } catch (PDOException $e) {
            throw new Exception("Error SQL en update_presupuesto: " . $e->getMessage());
        } catch (Exception $e) {
            throw new Exception("Error general en update_presupuesto: " . $e->getMessage());
        }
    }

    public function verificarPresupuesto($numero_presupuesto, $id_presupuesto = null)
    {
        try {
            $sql = "SELECT COUNT(*) AS total FROM presupuesto WHERE LOWER(numero_presupuesto) = LOWER(?)";
            $params = [trim($numero_presupuesto)];
    
            if (!empty($id_presupuesto)) {
                $sql .= " AND id_presupuesto != ?";
                $params[] = $id_presupuesto;
            }
    
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute($params);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
    
            return [
                'existe' => ($resultado['total'] > 0)
            ];
    
        } catch (PDOException $e) {
            if (isset($this->registro)) {
                $this->registro->registrarActividad(
                    null,
                    'Presupuesto',
                    'verificarPresupuesto',
                    "Error al verificar existencia del presupuesto: " . $e->getMessage(),
                    'error'
                );
            }
    
            return [
                'existe' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Obtener estadísticas completas de presupuestos
     * Incluye: totales por estado, alertas de caducidad, estadísticas mensuales
     */
    public function obtenerEstadisticas()
    {
        try {
            $estadisticas = [];
            
            // ===================================================================
            // ESTADÍSTICAS GENERALES - Total de presupuestos activos
            // ===================================================================
            $sql = "SELECT COUNT(*) as total FROM presupuesto WHERE activo_presupuesto = 1";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $estadisticas['total_activos'] = (int)$result['total'];
            
            // ===================================================================
            // CONTEO POR ESTADO (usando códigos del sistema)
            // ===================================================================
            // En proceso (PROC)
            $sql = "SELECT COUNT(*) as total 
                    FROM presupuesto p
                    INNER JOIN estado_presupuesto ep ON p.id_estado_ppto = ep.id_estado_ppto
                    WHERE p.activo_presupuesto = 1 AND ep.codigo_estado_ppto = 'PROC'";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $estadisticas['en_proceso'] = (int)$result['total'];
            
            // Pendiente de revisión (PEND)
            $sql = "SELECT COUNT(*) as total 
                    FROM presupuesto p
                    INNER JOIN estado_presupuesto ep ON p.id_estado_ppto = ep.id_estado_ppto
                    WHERE p.activo_presupuesto = 1 AND ep.codigo_estado_ppto = 'PEND'";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $estadisticas['pendiente_revision'] = (int)$result['total'];
            
            // Esperando respuesta (ESPE-RESP)
            $sql = "SELECT COUNT(*) as total 
                    FROM presupuesto p
                    INNER JOIN estado_presupuesto ep ON p.id_estado_ppto = ep.id_estado_ppto
                    WHERE p.activo_presupuesto = 1 AND ep.codigo_estado_ppto = 'ESPE-RESP'";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $estadisticas['esperando_respuesta'] = (int)$result['total'];
            
            // Aprobados (APROB)
            $sql = "SELECT COUNT(*) as total 
                    FROM presupuesto p
                    INNER JOIN estado_presupuesto ep ON p.id_estado_ppto = ep.id_estado_ppto
                    WHERE p.activo_presupuesto = 1 AND ep.codigo_estado_ppto = 'APROB'";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $estadisticas['aprobados'] = (int)$result['total'];
            
            // Rechazados (RECH)
            $sql = "SELECT COUNT(*) as total 
                    FROM presupuesto p
                    INNER JOIN estado_presupuesto ep ON p.id_estado_ppto = ep.id_estado_ppto
                    WHERE p.activo_presupuesto = 1 AND ep.codigo_estado_ppto = 'RECH'";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $estadisticas['rechazados'] = (int)$result['total'];
            
            // Cancelados (CANC)
            $sql = "SELECT COUNT(*) as total 
                    FROM presupuesto p
                    INNER JOIN estado_presupuesto ep ON p.id_estado_ppto = ep.id_estado_ppto
                    WHERE p.activo_presupuesto = 1 AND ep.codigo_estado_ppto = 'CANC'";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $estadisticas['cancelados'] = (int)$result['total'];
            
            // ===================================================================
            // TASA DE CONVERSIÓN (Aprobados / Total en estados activos)
            // ===================================================================
            $total_evaluables = $estadisticas['aprobados'] + $estadisticas['rechazados'];
            if ($total_evaluables > 0) {
                $estadisticas['tasa_conversion'] = round(($estadisticas['aprobados'] / $total_evaluables) * 100, 2);
            } else {
                $estadisticas['tasa_conversion'] = 0;
            }
            
            // ===================================================================
            // ALERTAS DE VALIDEZ
            // ===================================================================
            // Presupuestos que caducan HOY
            $sql = "SELECT COUNT(*) as total 
                    FROM presupuesto p
                    INNER JOIN estado_presupuesto ep ON p.id_estado_ppto = ep.id_estado_ppto
                    WHERE p.activo_presupuesto = 1 
                    AND p.fecha_validez_presupuesto = CURDATE()
                    AND ep.codigo_estado_ppto IN ('PROC', 'PEND', 'ESPE-RESP')";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $estadisticas['caduca_hoy'] = (int)$result['total'];
            
            // Presupuestos ya CADUCADOS (fecha_validez < HOY)
            $sql = "SELECT COUNT(*) as total 
                    FROM presupuesto p
                    INNER JOIN estado_presupuesto ep ON p.id_estado_ppto = ep.id_estado_ppto
                    WHERE p.activo_presupuesto = 1 
                    AND p.fecha_validez_presupuesto < CURDATE()
                    AND ep.codigo_estado_ppto IN ('PROC', 'PEND', 'ESPE-RESP')";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $estadisticas['caducados'] = (int)$result['total'];
            
            // Presupuestos que caducan en los próximos 7 días
            $sql = "SELECT COUNT(*) as total 
                    FROM presupuesto p
                    INNER JOIN estado_presupuesto ep ON p.id_estado_ppto = ep.id_estado_ppto
                    WHERE p.activo_presupuesto = 1 
                    AND p.fecha_validez_presupuesto BETWEEN CURDATE() + INTERVAL 1 DAY AND CURDATE() + INTERVAL 7 DAY
                    AND ep.codigo_estado_ppto IN ('PROC', 'PEND', 'ESPE-RESP')";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $estadisticas['por_caducar_7dias'] = (int)$result['total'];
            
            // Presupuestos VIGENTES (fecha_validez >= HOY)
            $sql = "SELECT COUNT(*) as total 
                    FROM presupuesto p
                    INNER JOIN estado_presupuesto ep ON p.id_estado_ppto = ep.id_estado_ppto
                    WHERE p.activo_presupuesto = 1 
                    AND p.fecha_validez_presupuesto >= CURDATE()
                    AND ep.codigo_estado_ppto IN ('PROC', 'PEND', 'ESPE-RESP')";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $estadisticas['vigentes'] = (int)$result['total'];
            
            // ===================================================================
            // ESTADÍSTICAS DEL MES ACTUAL
            // ===================================================================
            $sql = "SELECT COUNT(*) as total 
                    FROM presupuesto 
                    WHERE activo_presupuesto = 1 
                    AND MONTH(fecha_presupuesto) = MONTH(CURDATE())
                    AND YEAR(fecha_presupuesto) = YEAR(CURDATE())";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $estadisticas['mes_total'] = (int)$result['total'];
            
            // Aprobados del mes
            $sql = "SELECT COUNT(*) as total 
                    FROM presupuesto p
                    INNER JOIN estado_presupuesto ep ON p.id_estado_ppto = ep.id_estado_ppto
                    WHERE p.activo_presupuesto = 1 
                    AND ep.codigo_estado_ppto = 'APROB'
                    AND MONTH(p.fecha_presupuesto) = MONTH(CURDATE())
                    AND YEAR(p.fecha_presupuesto) = YEAR(CURDATE())";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $estadisticas['mes_aprobados'] = (int)$result['total'];
            
            // Pendientes del mes (PEND + ESPE-RESP + PROC)
            $sql = "SELECT COUNT(*) as total 
                    FROM presupuesto p
                    INNER JOIN estado_presupuesto ep ON p.id_estado_ppto = ep.id_estado_ppto
                    WHERE p.activo_presupuesto = 1 
                    AND ep.codigo_estado_ppto IN ('PEND', 'ESPE-RESP', 'PROC')
                    AND MONTH(p.fecha_presupuesto) = MONTH(CURDATE())
                    AND YEAR(p.fecha_presupuesto) = YEAR(CURDATE())";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $estadisticas['mes_pendientes'] = (int)$result['total'];
            
            // Rechazados del mes
            $sql = "SELECT COUNT(*) as total 
                    FROM presupuesto p
                    INNER JOIN estado_presupuesto ep ON p.id_estado_ppto = ep.id_estado_ppto
                    WHERE p.activo_presupuesto = 1 
                    AND ep.codigo_estado_ppto = 'RECH'
                    AND MONTH(p.fecha_presupuesto) = MONTH(CURDATE())
                    AND YEAR(p.fecha_presupuesto) = YEAR(CURDATE())";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $estadisticas['mes_rechazados'] = (int)$result['total'];
            
            // ===================================================================
            // EVENTOS PRÓXIMOS (en los próximos 7 días)
            // ===================================================================
            $sql = "SELECT COUNT(*) as total 
                    FROM presupuesto p
                    INNER JOIN estado_presupuesto ep ON p.id_estado_ppto = ep.id_estado_ppto
                    WHERE p.activo_presupuesto = 1 
                    AND p.fecha_inicio_evento_presupuesto BETWEEN CURDATE() AND CURDATE() + INTERVAL 7 DAY
                    AND ep.codigo_estado_ppto = 'APROB'";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $estadisticas['eventos_proximos_7dias'] = (int)$result['total'];
            
            return $estadisticas;
            
        } catch (PDOException $e) {
            if (isset($this->registro)) {
                $this->registro->registrarActividad(
                    null,
                    'Presupuesto',
                    'obtenerEstadisticas',
                    "Error al obtener estadísticas: " . $e->getMessage(),
                    'error'
                );
            }
            
            return [
                'error' => true,
                'mensaje' => $e->getMessage()
            ];
        }
    }
    public function get_presupuestos_por_mes($month, $year)
{
    try {
        $sql = "
            SELECT 
                numero_presupuesto,
                nombre_cliente,
                nombre_evento_presupuesto,
                nombre_estado_ppto,
                fecha_inicio_evento_presupuesto,
                fecha_fin_evento_presupuesto,
                color_estado_ppto,
                total_presupuesto
            FROM vista_presupuesto_completa
            WHERE fecha_inicio_evento_presupuesto IS NOT NULL
              AND MONTH(fecha_inicio_evento_presupuesto) = ?
              AND YEAR(fecha_inicio_evento_presupuesto) = ?
        ";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([$month, $year]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        $this->registro->registrarActividad(
            'admin',
            'Presupuesto',
            'get_presupuestos_por_mes',
            'Error calendario: ' . $e->getMessage(),
            'error'
        );

        return [];
    }
}

}


?>