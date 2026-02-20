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

    /**
     * Obtiene información de una versión específica de presupuesto
     * Incluye datos del presupuesto cabecera, versión y cliente
     * 
     * @param int $id_version_presupuesto ID de la versión
     * @return array|false Datos de la versión o false en caso de error
     */
    public function get_info_version($id_version_presupuesto)
    {
        try {
            $sql = "SELECT 
                        -- Datos de la versión
                        pv.id_version_presupuesto,
                        pv.numero_version_presupuesto,
                        pv.estado_version_presupuesto,
                        pv.motivo_modificacion_version,
                        pv.fecha_creacion_version,
                        pv.creado_por_version,
                        
                        -- Datos del presupuesto cabecera
                        p.id_presupuesto,
                        p.numero_presupuesto,
                        p.nombre_evento_presupuesto,
                        p.fecha_presupuesto,
                        p.fecha_validez_presupuesto,
                        p.fecha_inicio_evento_presupuesto,
                        p.fecha_fin_evento_presupuesto,
                        
                        -- Datos del cliente
                        c.id_cliente,
                        c.nombre_cliente,
                        c.email_cliente,
                        c.telefono_cliente,
                        c.exento_iva_cliente
                        
                    FROM presupuesto_version pv
                    INNER JOIN presupuesto p ON pv.id_presupuesto = p.id_presupuesto
                    INNER JOIN cliente c ON p.id_cliente = c.id_cliente
                    WHERE pv.id_version_presupuesto = ?
                    AND pv.activo_version = 1";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_version_presupuesto, PDO::PARAM_INT);
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($resultado) {
                $this->registro->registrarActividad(
                    'admin',
                    'Presupuesto',
                    'get_info_version',
                    "Info versión obtenida: {$id_version_presupuesto}",
                    'info'
                );
                return $resultado;
            } else {
                $this->registro->registrarActividad(
                    'admin',
                    'Presupuesto',
                    'get_info_version',
                    "No se encontró la versión {$id_version_presupuesto}",
                    'warning'
                );
                return false;
            }
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Presupuesto',
                'get_info_version',
                "Error SQL: " . $e->getMessage() . " | Query para versión: {$id_version_presupuesto}",
                'error'
            );

            // Retornar el error para debugging
            return ['error' => $e->getMessage()];
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

    public function insert_presupuesto($numero_presupuesto, $id_cliente, $id_contacto_cliente, $id_estado_ppto, $id_forma_pago, $id_metodo, $fecha_presupuesto, $fecha_validez_presupuesto, $fecha_inicio_evento_presupuesto, $fecha_fin_evento_presupuesto, $numero_pedido_cliente_presupuesto, $aplicar_coeficientes_presupuesto, $descuento_presupuesto, $nombre_evento_presupuesto, $direccion_evento_presupuesto, $poblacion_evento_presupuesto, $cp_evento_presupuesto, $provincia_evento_presupuesto, $observaciones_cabecera_presupuesto, $observaciones_cabecera_ingles_presupuesto, $observaciones_pie_presupuesto, $observaciones_pie_ingles_presupuesto, $destacar_observaciones_pie_presupuesto, $mostrar_obs_familias_presupuesto, $mostrar_obs_articulos_presupuesto, $observaciones_internas_presupuesto)
    {
        try {
            $this->conexion->exec("SET time_zone = 'Europe/Madrid'");
            
            $sql = "INSERT INTO presupuesto (numero_presupuesto, id_cliente, id_contacto_cliente, id_estado_ppto, id_forma_pago, id_metodo, 
                    fecha_presupuesto, fecha_validez_presupuesto, fecha_inicio_evento_presupuesto, fecha_fin_evento_presupuesto, 
                    numero_pedido_cliente_presupuesto, aplicar_coeficientes_presupuesto, descuento_presupuesto, 
                    nombre_evento_presupuesto, direccion_evento_presupuesto, poblacion_evento_presupuesto, cp_evento_presupuesto, 
                    provincia_evento_presupuesto, observaciones_cabecera_presupuesto, observaciones_cabecera_ingles_presupuesto, 
                    observaciones_pie_presupuesto, observaciones_pie_ingles_presupuesto, destacar_observaciones_pie_presupuesto, 
                    mostrar_obs_familias_presupuesto, mostrar_obs_articulos_presupuesto, observaciones_internas_presupuesto, 
                    activo_presupuesto, created_at_presupuesto, updated_at_presupuesto) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, NOW(), NOW())";
            
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
            $stmt->bindValue(13, $descuento_presupuesto, PDO::PARAM_STR);
            $stmt->bindValue(14, $nombre_evento_presupuesto, PDO::PARAM_STR);
            $stmt->bindValue(15, $direccion_evento_presupuesto, PDO::PARAM_STR);
            $stmt->bindValue(16, $poblacion_evento_presupuesto, PDO::PARAM_STR);
            $stmt->bindValue(17, $cp_evento_presupuesto, PDO::PARAM_STR);
            $stmt->bindValue(18, $provincia_evento_presupuesto, PDO::PARAM_STR);
            $stmt->bindValue(19, $observaciones_cabecera_presupuesto, PDO::PARAM_STR);
            $stmt->bindValue(20, $observaciones_cabecera_ingles_presupuesto, PDO::PARAM_STR);
            $stmt->bindValue(21, $observaciones_pie_presupuesto, PDO::PARAM_STR);
            $stmt->bindValue(22, $observaciones_pie_ingles_presupuesto, PDO::PARAM_STR);
            $stmt->bindValue(23, $destacar_observaciones_pie_presupuesto, PDO::PARAM_BOOL);
            $stmt->bindValue(24, $mostrar_obs_familias_presupuesto, PDO::PARAM_BOOL);
            $stmt->bindValue(25, $mostrar_obs_articulos_presupuesto, PDO::PARAM_BOOL);
            $stmt->bindValue(26, $observaciones_internas_presupuesto, PDO::PARAM_STR);
            
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

    public function update_presupuesto($id_presupuesto, $numero_presupuesto, $id_cliente, $id_contacto_cliente, $id_estado_ppto, $id_forma_pago, $id_metodo, $fecha_presupuesto, $fecha_validez_presupuesto, $fecha_inicio_evento_presupuesto, $fecha_fin_evento_presupuesto, $numero_pedido_cliente_presupuesto, $aplicar_coeficientes_presupuesto, $descuento_presupuesto, $nombre_evento_presupuesto, $direccion_evento_presupuesto, $poblacion_evento_presupuesto, $cp_evento_presupuesto, $provincia_evento_presupuesto, $observaciones_cabecera_presupuesto, $observaciones_cabecera_ingles_presupuesto, $observaciones_pie_presupuesto, $observaciones_pie_ingles_presupuesto, $destacar_observaciones_pie_presupuesto, $mostrar_obs_familias_presupuesto, $mostrar_obs_articulos_presupuesto, $observaciones_internas_presupuesto)
    {
        try {
            $this->conexion->exec("SET time_zone = 'Europe/Madrid'");
            
            $sql = "UPDATE presupuesto SET numero_presupuesto = ?, id_cliente = ?, id_contacto_cliente = ?, id_estado_ppto = ?, 
                    id_forma_pago = ?, id_metodo = ?, fecha_presupuesto = ?, fecha_validez_presupuesto = ?, 
                    fecha_inicio_evento_presupuesto = ?, fecha_fin_evento_presupuesto = ?, numero_pedido_cliente_presupuesto = ?, 
                    aplicar_coeficientes_presupuesto = ?, descuento_presupuesto = ?, nombre_evento_presupuesto = ?, 
                    direccion_evento_presupuesto = ?, poblacion_evento_presupuesto = ?, cp_evento_presupuesto = ?, 
                    provincia_evento_presupuesto = ?, observaciones_cabecera_presupuesto = ?, 
                    observaciones_cabecera_ingles_presupuesto = ?, observaciones_pie_presupuesto = ?, 
                    observaciones_pie_ingles_presupuesto = ?, destacar_observaciones_pie_presupuesto = ?, 
                    mostrar_obs_familias_presupuesto = ?, mostrar_obs_articulos_presupuesto = ?, 
                    observaciones_internas_presupuesto = ?, updated_at_presupuesto = NOW() WHERE id_presupuesto = ?";
            
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
            $stmt->bindValue(13, $descuento_presupuesto, PDO::PARAM_STR);
            $stmt->bindValue(14, $nombre_evento_presupuesto, PDO::PARAM_STR);
            $stmt->bindValue(15, $direccion_evento_presupuesto, PDO::PARAM_STR);
            $stmt->bindValue(16, $poblacion_evento_presupuesto, PDO::PARAM_STR);
            $stmt->bindValue(17, $cp_evento_presupuesto, PDO::PARAM_STR);
            $stmt->bindValue(18, $provincia_evento_presupuesto, PDO::PARAM_STR);
            $stmt->bindValue(19, $observaciones_cabecera_presupuesto, PDO::PARAM_STR);
            $stmt->bindValue(20, $observaciones_cabecera_ingles_presupuesto, PDO::PARAM_STR);
            $stmt->bindValue(21, $observaciones_pie_presupuesto, PDO::PARAM_STR);
            $stmt->bindValue(22, $observaciones_pie_ingles_presupuesto, PDO::PARAM_STR);
            $stmt->bindValue(23, $destacar_observaciones_pie_presupuesto, PDO::PARAM_BOOL);
            $stmt->bindValue(24, $mostrar_obs_familias_presupuesto, PDO::PARAM_BOOL);
            $stmt->bindValue(25, $mostrar_obs_articulos_presupuesto, PDO::PARAM_BOOL);
            $stmt->bindValue(26, $observaciones_internas_presupuesto, PDO::PARAM_STR);
            $stmt->bindValue(27, $id_presupuesto, PDO::PARAM_INT);

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
     * Obtener todas las versiones de un presupuesto para selector de impresión
     *
     * @param int $id_presupuesto ID del presupuesto
     * @return array Array de versiones ordenadas ASC
     */
    public function get_versiones_presupuesto($id_presupuesto)
    {
        try {
            $sql = "SELECT
                        pv.id_version_presupuesto,
                        pv.numero_version_presupuesto,
                        pv.estado_version_presupuesto,
                        pv.fecha_creacion_version,
                        p.version_actual_presupuesto,
                        (pv.numero_version_presupuesto = p.version_actual_presupuesto) AS es_actual
                    FROM presupuesto_version pv
                    INNER JOIN presupuesto p ON pv.id_presupuesto = p.id_presupuesto
                    WHERE pv.id_presupuesto = ?
                    AND pv.activo_version = 1
                    ORDER BY pv.numero_version_presupuesto ASC";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_presupuesto, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            if (isset($this->registro)) {
                $this->registro->registrarActividad(
                    'admin',
                    'Presupuesto',
                    'get_versiones_presupuesto',
                    "Error: " . $e->getMessage(),
                    'error'
                );
            }
            return [];
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

    /**
     * Obtiene presupuestos con eventos en un mes/año específico
     * @param int $month Número del mes (1-12)
     * @param int $year Año (YYYY)
     * @return array Lista de presupuestos con eventos en ese mes
     */
    // ============================================
    // MÉTODOS DE GESTIÓN DE VERSIONES
    // ============================================

    /**
     * Crear nueva versión duplicando líneas de la versión actual
     * Usa transacción para garantizar consistencia.
     * @param int $id_presupuesto
     * @param string|null $motivo
     * @param int $id_usuario
     * @return array ['success' => bool, 'id_version' => int, 'numero_version' => int, 'lineas_duplicadas' => int]
     */
    public function crear_nueva_version($id_presupuesto, $motivo = null, $id_usuario = 1)
    {
        try {
            $this->conexion->beginTransaction();

            // Obtener versión actual
            $sql_actual = "SELECT pv.id_version_presupuesto,
                                  pv.numero_version_presupuesto,
                                  pv.estado_version_presupuesto
                           FROM presupuesto_version pv
                           INNER JOIN presupuesto p ON pv.id_presupuesto = p.id_presupuesto
                           WHERE pv.id_presupuesto = ?
                           AND pv.numero_version_presupuesto = p.version_actual_presupuesto
                           AND pv.activo_version = 1";

            $stmt = $this->conexion->prepare($sql_actual);
            $stmt->execute([$id_presupuesto]);
            $version_actual = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$version_actual) {
                throw new Exception("No se encontró la versión actual del presupuesto");
            }

            $estado = $version_actual['estado_version_presupuesto'];
            if ($estado === 'aprobado') {
                throw new Exception("No se puede crear una nueva versión de un presupuesto aprobado");
            }
            if ($estado === 'cancelado') {
                throw new Exception("No se puede crear una nueva versión de un presupuesto cancelado");
            }

            // Crear nueva versión vacía (los triggers calculan numero_version y validan)
            require_once 'PresupuestoVersion.php';
            $modeloVersion = new PresupuestoVersion();

            $id_version_nueva = $modeloVersion->crear_version(
                $id_presupuesto,
                $motivo ?? 'Nueva versión solicitada',
                $id_usuario
            );

            if (!$id_version_nueva) {
                throw new Exception("Error al insertar la nueva versión en la base de datos");
            }

            // Duplicar líneas de la versión actual a la nueva
            $lineas_duplicadas = $modeloVersion->duplicar_lineas(
                $version_actual['id_version_presupuesto'],
                $id_version_nueva
            );

            // Obtener número de la nueva versión
            $sql_num = "SELECT numero_version_presupuesto FROM presupuesto_version WHERE id_version_presupuesto = ?";
            $stmt_num = $this->conexion->prepare($sql_num);
            $stmt_num->execute([$id_version_nueva]);
            $row_num = $stmt_num->fetch(PDO::FETCH_ASSOC);
            $numero_nueva_version = $row_num ? (int)$row_num['numero_version_presupuesto'] : ($version_actual['numero_version_presupuesto'] + 1);

            // Actualizar versión actual en la cabecera del presupuesto
            $sql_update = "UPDATE presupuesto SET 
                               version_actual_presupuesto = ?
                           WHERE id_presupuesto = ?";
            $stmt_update = $this->conexion->prepare($sql_update);
            $stmt_update->execute([$numero_nueva_version, $id_presupuesto]);

            $this->conexion->commit();

            $this->registro->registrarActividad(
                'admin',
                'Presupuesto',
                'crear_nueva_version',
                "Presupuesto $id_presupuesto: versión $numero_nueva_version creada (ID=$id_version_nueva) con $lineas_duplicadas líneas",
                'info'
            );

            return [
                'success'          => true,
                'id_version'       => (int)$id_version_nueva,
                'numero_version'   => $numero_nueva_version,
                'lineas_duplicadas' => $lineas_duplicadas
            ];

        } catch (Exception $e) {
            if ($this->conexion->inTransaction()) {
                $this->conexion->rollBack();
            }

            $this->registro->registrarActividad(
                'admin',
                'Presupuesto',
                'crear_nueva_version',
                "Error: " . $e->getMessage(),
                'error'
            );

            return [
                'success' => false,
                'error'   => $e->getMessage()
            ];
        }
    }

    /**
     * Cambiar la versión activa de un presupuesto (solo borradores)
     * @param int $id_presupuesto
     * @param int $numero_version
     * @return bool
     */
    public function activar_version($id_presupuesto, $numero_version)
    {
        try {
            // Verificar que la versión existe y está en borrador
            $sql_ver = "SELECT id_version_presupuesto, estado_version_presupuesto
                        FROM presupuesto_version
                        WHERE id_presupuesto = ?
                        AND numero_version_presupuesto = ?
                        AND activo_version = 1";

            $stmt = $this->conexion->prepare($sql_ver);
            $stmt->execute([$id_presupuesto, $numero_version]);
            $version = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$version) {
                throw new Exception("Versión no encontrada");
            }

            if ($version['estado_version_presupuesto'] !== 'borrador') {
                throw new Exception("Solo se pueden activar versiones en estado borrador");
            }

            $sql_update = "UPDATE presupuesto SET version_actual_presupuesto = ? WHERE id_presupuesto = ?";
            $stmt_update = $this->conexion->prepare($sql_update);
            $stmt_update->execute([$numero_version, $id_presupuesto]);

            $this->registro->registrarActividad(
                'admin',
                'Presupuesto',
                'activar_version',
                "Presupuesto $id_presupuesto: versión $numero_version activada",
                'info'
            );

            return true;

        } catch (Exception $e) {
            $this->registro->registrarActividad(
                'admin',
                'Presupuesto',
                'activar_version',
                "Error: " . $e->getMessage(),
                'error'
            );
            return false;
        }
    }

    /**
     * Obtener estadísticas de versiones de un presupuesto
     * @param int $id_presupuesto
     * @return array
     */
    public function get_estadisticas_versiones($id_presupuesto)
    {
        try {
            $sql = "SELECT 
                        COUNT(*) as total_versiones,
                        MAX(numero_version_presupuesto) as ultima_version,
                        SUM(CASE WHEN estado_version_presupuesto = 'borrador'  THEN 1 ELSE 0 END) as borradores,
                        SUM(CASE WHEN estado_version_presupuesto = 'enviado'   THEN 1 ELSE 0 END) as enviadas,
                        SUM(CASE WHEN estado_version_presupuesto = 'aprobado'  THEN 1 ELSE 0 END) as aprobadas,
                        SUM(CASE WHEN estado_version_presupuesto = 'rechazado' THEN 1 ELSE 0 END) as rechazadas,
                        MAX(updated_at_version) as ultima_modificacion
                    FROM presupuesto_version
                    WHERE id_presupuesto = ?
                    AND activo_version = 1";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_presupuesto, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Presupuesto',
                'get_estadisticas_versiones',
                "Error: " . $e->getMessage(),
                'error'
            );
            return [];
        }
    }

    // ============================================

    public function get_presupuestos_por_mes($month, $year)
    {
        try {
            // Usar la vista completa para tener todos los campos necesarios para el calendario
            $sql = "SELECT * 
                    FROM vista_presupuesto_completa
                    WHERE activo_presupuesto = 1
                    AND (
                        (MONTH(fecha_inicio_evento_presupuesto) = ? AND YEAR(fecha_inicio_evento_presupuesto) = ?)
                        OR 
                        (MONTH(fecha_fin_evento_presupuesto) = ? AND YEAR(fecha_fin_evento_presupuesto) = ?)
                        OR
                        (fecha_inicio_evento_presupuesto <= LAST_DAY(CONCAT(?, '-', ?, '-01'))
                         AND fecha_fin_evento_presupuesto >= CONCAT(?, '-', LPAD(?, 2, '0'), '-01'))
                    )
                    ORDER BY fecha_inicio_evento_presupuesto ASC";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $month, PDO::PARAM_INT);
            $stmt->bindValue(2, $year, PDO::PARAM_INT);
            $stmt->bindValue(3, $month, PDO::PARAM_INT);
            $stmt->bindValue(4, $year, PDO::PARAM_INT);
            $stmt->bindValue(5, $year, PDO::PARAM_INT);
            $stmt->bindValue(6, $month, PDO::PARAM_INT);
            $stmt->bindValue(7, $year, PDO::PARAM_INT);
            $stmt->bindValue(8, $month, PDO::PARAM_INT);
            $stmt->execute();
            
            $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $this->registro->registrarActividad(
                'system',
                'Presupuesto',
                'get_presupuestos_por_mes',
                "Presupuestos obtenidos para mes $month/$year: " . count($resultado) . " registros",
                'info'
            );
            
            return $resultado;
            
        } catch (PDOException $e) {
            if (isset($this->registro)) {
                $this->registro->registrarActividad(
                    'admin',
                    'Presupuesto',
                    'get_presupuestos_por_mes',
                    "Error: " . $e->getMessage(),
                    'error'
                );
            }
            return [];
        }
    }
    
}


?>