<?php

require_once '../config/conexion.php'; // ✅ Se incluye correctamente el archivo de conexión
require_once '../config/funciones.php'; // ✅ Se incluye correctamente el archivo de conexión


//CREATE TABLE forma_pago (
//    id_pago INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
//    codigo_pago VARCHAR(20) NOT NULL UNIQUE COMMENT 'Código único identificador (ej: CONT_TRANS, FRAC40_60)',
//    nombre_pago VARCHAR(100) NOT NULL COMMENT 'Nombre descriptivo de la forma de pago',
//    id_metodo_pago INT UNSIGNED NOT NULL COMMENT 'Método de pago a utilizar (transferencia, tarjeta, efectivo...)',
//    descuento_pago DECIMAL(5,2) DEFAULT 0.00 COMMENT 'Descuento por pronto pago en porcentaje (ej: 2.00 = 2%). Solo aplica si porcentaje_anticipo_pago = 100',
//    porcentaje_anticipo_pago DECIMAL(5,2) DEFAULT 100.00 COMMENT 'Porcentaje del total a pagar como anticipo (ej: 40.00 = 40%). Si es 100.00 = pago único',
//    dias_anticipo_pago INT DEFAULT 0 COMMENT 'Días para pagar el anticipo desde la firma del presupuesto. 0=al firmar, 7=a los 7 días, 30=a los 30 días',
//    porcentaje_final_pago DECIMAL(5,2) DEFAULT 0.00 COMMENT 'Porcentaje restante del total (ej: 60.00 = 60%). Debe sumar 100% con el anticipo. Si es 0 = pago único',
//    dias_final_pago INT DEFAULT 0 COMMENT 'Días para el pago final. Positivo=días desde firma (30=a 30 días), Negativo=días antes del evento (-7=7 días antes), 0=al finalizar evento',
//    observaciones_pago TEXT COMMENT 'Observaciones internas sobre esta forma de pago',
//    activo_pago BOOLEAN DEFAULT TRUE COMMENT 'Si FALSE, la forma de pago no estará disponible para nuevos presupuestos',
//    created_at_pago TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
//    updated_at_pago TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
//    CONSTRAINT fk_forma_pago_metodo FOREIGN KEY (id_metodo_pago) REFERENCES metodo_pago(id_metodo_pago) ON DELETE RESTRICT ON UPDATE CASCADE,
//    INDEX idx_id_metodo_pago (id_metodo_pago),
//    INDEX idx_activo_pago (activo_pago)
//) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



class FormasPago
{

    private $conexion;
    private $registro; // ✅ Se declara la instancia para el objeto RegistroActividad 

    public function __construct()
    {
        $this->conexion = (new Conexion())->getConexion(); // ✅ Ahora obtiene correctamente la conexión
        $this->registro = new RegistroActividad(); // ✅ Se asigna el objeto RegistroActividad a la propiedad de la clase para los logs.
        
        // Configurar zona horaria Madrid para todas las operaciones
        try {
            $this->conexion->exec("SET time_zone = 'Europe/Madrid'");
        } catch (PDOException $e) {
            // Si no se puede establecer la zona horaria, registrar error pero continuar
            $this->registro->registrarActividad(
                'system',
                'FormasPago',
                '__construct',
                "No se pudo establecer zona horaria Madrid: " . $e->getMessage(),
                'warning'
            );
        }
    }

    public function get_formas_pago()
    {
        try {
            $sql = "SELECT 
                        fp.id_pago,
                        fp.codigo_pago,
                        fp.nombre_pago,
                        fp.id_metodo_pago,
                        mp.nombre_metodo_pago,
                        fp.porcentaje_anticipo_pago,
                        fp.dias_anticipo_pago,
                        fp.porcentaje_final_pago,
                        fp.dias_final_pago,
                        fp.descuento_pago,
                        CASE 
                            WHEN fp.porcentaje_anticipo_pago = 100.00 THEN 'Pago único'
                            ELSE 'Pago fraccionado'
                        END as tipo_pago,
                        fp.observaciones_pago,
                        fp.activo_pago,
                        fp.created_at_pago,
                        fp.updated_at_pago
                    FROM forma_pago fp
                    INNER JOIN metodo_pago mp ON fp.id_metodo_pago = mp.id_metodo_pago
                    ORDER BY 
                        CASE WHEN fp.porcentaje_anticipo_pago = 100.00 THEN 1 ELSE 2 END,
                        fp.nombre_pago";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();

            return $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'FormasPago',
                'get_formas_pago',
                "Error al listar las formas de pago: " . $e->getMessage(),
                "error"
            );
        }
    }

    public function get_formas_pago_disponibles()
    {
        try {
            $sql = "SELECT 
                        fp.id_pago,
                        fp.codigo_pago,
                        fp.nombre_pago,
                        fp.id_metodo_pago,
                        mp.nombre_metodo_pago,
                        fp.porcentaje_anticipo_pago,
                        fp.dias_anticipo_pago,
                        fp.porcentaje_final_pago,
                        fp.dias_final_pago,
                        fp.descuento_pago,
                        CASE 
                            WHEN fp.porcentaje_anticipo_pago = 100.00 THEN 'Pago único'
                            ELSE 'Pago fraccionado'
                        END as tipo_pago,
                        fp.observaciones_pago,
                        fp.activo_pago,
                        fp.created_at_pago,
                        fp.updated_at_pago
                    FROM forma_pago fp
                    INNER JOIN metodo_pago mp ON fp.id_metodo_pago = mp.id_metodo_pago
                    WHERE fp.activo_pago = 1
                    ORDER BY 
                        CASE WHEN fp.porcentaje_anticipo_pago = 100.00 THEN 1 ELSE 2 END,
                        fp.nombre_pago";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();

            return $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'FormasPago',
                'get_formas_pago_disponibles',
                "Error al listar las formas de pago disponibles: " . $e->getMessage(),
                "error"
            );
        }
    }

    public function get_forma_pagoxid($id_pago)
    {
        try {
            $sql = "SELECT 
                        fp.id_pago,
                        fp.codigo_pago,
                        fp.nombre_pago,
                        fp.id_metodo_pago,
                        mp.nombre_metodo_pago,
                        fp.porcentaje_anticipo_pago,
                        fp.dias_anticipo_pago,
                        fp.porcentaje_final_pago,
                        fp.dias_final_pago,
                        fp.descuento_pago,
                        CASE 
                            WHEN fp.porcentaje_anticipo_pago = 100.00 THEN 'Pago único'
                            ELSE 'Pago fraccionado'
                        END as tipo_pago,
                        fp.observaciones_pago,
                        fp.activo_pago,
                        fp.created_at_pago,
                        fp.updated_at_pago
                    FROM forma_pago fp
                    INNER JOIN metodo_pago mp ON fp.id_metodo_pago = mp.id_metodo_pago
                    WHERE fp.id_pago = ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_pago, PDO::PARAM_INT);
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
                        
            return $resultado;
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'FormasPago',
                'get_forma_pagoxid',
                "Error al mostrar la forma de pago {$id_pago}: " . $e->getMessage(),
                "error"
            );

            return false;
        }
    }

    public function delete_forma_pagoxid($id_pago)
    {
        try {
            $sql = "UPDATE forma_pago SET activo_pago = 0 WHERE id_pago = ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_pago, PDO::PARAM_INT);
            $stmt->execute();

            $this->registro->registrarActividad(
                'admin',
                'FormasPago',
                'Desactivar',
                "Se desactivó la forma de pago con ID: $id_pago",
                'info'
            );
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'FormasPago',
                'delete_forma_pagoxid',
                "Error al desactivar la forma de pago {$id_pago}: " . $e->getMessage(),
                'error'
            );

            return false;
        }
    }

    public function activar_forma_pagoxid($id_pago)
    {
        try {
            $sql = "UPDATE forma_pago SET activo_pago = 1 WHERE id_pago = ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_pago, PDO::PARAM_INT);
            $stmt->execute();

            $this->registro->registrarActividad(
                'admin',
                'FormasPago',
                'Activar',
                "Se activó la forma de pago con ID: $id_pago",
                'info'
            );
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'FormasPago',
                'activar_forma_pagoxid',
                "Error al activar la forma de pago {$id_pago}: " . $e->getMessage(),
                "error"
            );

            return false;
        }
    }

    public function insert_forma_pago($codigo_pago, $nombre_pago, $id_metodo_pago, $descuento_pago, $porcentaje_anticipo_pago, $dias_anticipo_pago, $porcentaje_final_pago, $dias_final_pago, $observaciones_pago)
    {
        try {
            // Establecer zona horaria Madrid antes de la consulta
            $this->conexion->exec("SET time_zone = 'Europe/Madrid'");

            $sql = "INSERT INTO forma_pago (codigo_pago, nombre_pago, id_metodo_pago, descuento_pago, porcentaje_anticipo_pago, dias_anticipo_pago, porcentaje_final_pago, dias_final_pago, observaciones_pago, activo_pago, created_at_pago, updated_at_pago) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1, NOW(), NOW())";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $codigo_pago, PDO::PARAM_STR);
            $stmt->bindValue(2, $nombre_pago, PDO::PARAM_STR);
            $stmt->bindValue(3, $id_metodo_pago, PDO::PARAM_INT);
            $stmt->bindValue(4, $descuento_pago, PDO::PARAM_STR);
            $stmt->bindValue(5, $porcentaje_anticipo_pago, PDO::PARAM_STR);
            $stmt->bindValue(6, $dias_anticipo_pago, PDO::PARAM_INT);
            $stmt->bindValue(7, $porcentaje_final_pago, PDO::PARAM_STR);
            $stmt->bindValue(8, $dias_final_pago, PDO::PARAM_INT);
            $stmt->bindValue(9, $observaciones_pago, PDO::PARAM_STR);
            $stmt->execute();
            $idInsert = $this->conexion->lastInsertId();

            $this->registro->registrarActividad(
                'admin',
                'FormasPago',
                'Insertar',
                "Se insertó la forma de pago con ID: $idInsert",
                'info'
            );

            return $idInsert;
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'FormasPago',
                'insert_forma_pago',
                "Error al insertar la forma de pago: " . $e->getMessage(),
                'error'
            );

            return false;
        }
    }

    public function update_forma_pago($id_pago, $codigo_pago, $nombre_pago, $id_metodo_pago, $descuento_pago, $porcentaje_anticipo_pago, $dias_anticipo_pago, $porcentaje_final_pago, $dias_final_pago, $observaciones_pago)
    {
        try {
            // Establecer zona horaria Madrid antes de la consulta
            $this->conexion->exec("SET time_zone = 'Europe/Madrid'");
            
            $sql = "UPDATE forma_pago SET codigo_pago = ?, nombre_pago = ?, id_metodo_pago = ?, descuento_pago = ?, porcentaje_anticipo_pago = ?, dias_anticipo_pago = ?, porcentaje_final_pago = ?, dias_final_pago = ?, observaciones_pago = ?, updated_at_pago = NOW() WHERE id_pago = ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $codigo_pago, PDO::PARAM_STR);
            $stmt->bindValue(2, $nombre_pago, PDO::PARAM_STR);
            $stmt->bindValue(3, $id_metodo_pago, PDO::PARAM_INT);
            $stmt->bindValue(4, $descuento_pago, PDO::PARAM_STR);
            $stmt->bindValue(5, $porcentaje_anticipo_pago, PDO::PARAM_STR);
            $stmt->bindValue(6, $dias_anticipo_pago, PDO::PARAM_INT);
            $stmt->bindValue(7, $porcentaje_final_pago, PDO::PARAM_STR);
            $stmt->bindValue(8, $dias_final_pago, PDO::PARAM_INT);
            $stmt->bindValue(9, $observaciones_pago, PDO::PARAM_STR);
            $stmt->bindValue(10, $id_pago, PDO::PARAM_INT);
          
            $stmt->execute();

            $this->registro->registrarActividad(
                'admin',
                'FormasPago',
                'Actualizar',
                "Se actualizó la forma de pago con ID: $id_pago",
                'info'
            );

            return true;

        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'FormasPago',
                'update_forma_pago',
                "Error al actualizar la forma de pago: " . $e->getMessage(),
                'error'
            );
        }
    }

    public function verificarFormaPago($codigo_pago, $nombre_pago = null, $id_pago = null)
    {
        try {
            // Consulta SQL base - verificamos por código o nombre
            $sql = "SELECT COUNT(*) AS total FROM forma_pago WHERE (LOWER(codigo_pago) = LOWER(?) OR LOWER(nombre_pago) = LOWER(?))";
            $params = [$codigo_pago, $nombre_pago];
    
            // Si es edición, excluimos el ID actual
            if (!empty($id_pago)) {
                $sql .= " AND id_pago != ?";
                $params[] = $id_pago;
            }
    
            // Ejecución de la consulta
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute($params);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
    
            return [
                'existe' => ($resultado['total'] > 0)
            ];
    
        } catch (PDOException $e) {
            // Registro de error
            if (isset($this->registro)) {
                $this->registro->registrarActividad(
                    null,
                    'FormasPago',
                    'verificarFormaPago',
                    "Error al verificar existencia de la forma de pago: " . $e->getMessage(),
                    'error'
                );
            }
    
            return [
                'existe' => false,
                'error' => $e->getMessage()
            ];
        }
    }

}
