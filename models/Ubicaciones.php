<?php

require_once '../config/conexion.php';
require_once '../config/funciones.php';

class Ubicaciones
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
            // Si no se puede establecer la zona horaria, registrar error pero continuar
            $this->registro->registrarActividad(
                'system',
                'Ubicaciones',
                '__construct',
                "No se pudo establecer zona horaria Madrid: " . $e->getMessage(),
                'warning'
            );
        }
    }

    public function get_ubicaciones_cliente()
    {
        try {
            $sql = "SELECT * FROM vista_cliente_ubicaciones 
                    ORDER BY nombre_cliente ASC, es_principal_ubicacion DESC, nombre_ubicacion ASC";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Ubicaciones',
                'get_ubicaciones_cliente',
                "Error al listar ubicaciones de clientes: " . $e->getMessage(),
                "error"
            );
            return [];
        }
    }

    public function get_ubicaciones_by_cliente($id_cliente)
    {
        try {
            $sql = "SELECT u.*, c.nombre_cliente 
                    FROM cliente_ubicacion u
                    INNER JOIN cliente c ON u.id_cliente = c.id_cliente
                    WHERE u.id_cliente = ? 
                    ORDER BY u.es_principal_ubicacion DESC, u.nombre_ubicacion ASC";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_cliente, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Ubicaciones',
                'get_ubicaciones_by_cliente',
                "Error al listar ubicaciones del cliente {$id_cliente}: " . $e->getMessage(),
                "error"
            );
            return [];
        }
    }

    public function get_ubicacionxid($id_ubicacion)
    {
        try {
            $sql = "SELECT u.*, c.nombre_cliente 
                    FROM cliente_ubicacion u
                    INNER JOIN cliente c ON u.id_cliente = c.id_cliente
                    WHERE u.id_ubicacion = ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_ubicacion, PDO::PARAM_INT);
            $stmt->execute();
            
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado;
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Ubicaciones',
                'get_ubicacionxid',
                "Error al mostrar la ubicación {$id_ubicacion}: " . $e->getMessage(),
                "error"
            );
            return false;
        }
    }

    public function activar_ubicacionxid($id_ubicacion)
    {
        try {
            $sql = "UPDATE cliente_ubicacion SET activo_ubicacion = 1 WHERE id_ubicacion = ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_ubicacion, PDO::PARAM_INT);
            $stmt->execute();

            $this->registro->registrarActividad(
                'admin',
                'Ubicaciones',
                'Activar',
                "Se activó la ubicación con ID: $id_ubicacion",
                'info'
            );
            
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Ubicaciones',
                'activar_ubicacionxid',
                "Error al activar la ubicación {$id_ubicacion}: " . $e->getMessage(),
                "error"
            );
            return false;
        }
    }

    public function desactivar_ubicacionxid($id_ubicacion)
    {
        try {
            $sql = "UPDATE cliente_ubicacion SET activo_ubicacion = 0 WHERE id_ubicacion = ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_ubicacion, PDO::PARAM_INT);
            $stmt->execute();

            $this->registro->registrarActividad(
                'admin',
                'Ubicaciones',
                'Desactivar',
                "Se desactivó la ubicación con ID: $id_ubicacion",
                'info'
            );
            
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Ubicaciones',
                'desactivar_ubicacionxid',
                "Error al desactivar la ubicación {$id_ubicacion}: " . $e->getMessage(),
                "error"
            );
            return false;
        }
    }

    public function insert_ubicacion($id_cliente, $nombre_ubicacion, $direccion_ubicacion, $codigo_postal_ubicacion, $poblacion_ubicacion, $provincia_ubicacion, $pais_ubicacion, $persona_contacto_ubicacion, $telefono_contacto_ubicacion, $email_contacto_ubicacion, $observaciones_ubicacion, $es_principal_ubicacion)
    {
        try {
            // Configurar zona horaria para esta operación
            $this->conexion->exec("SET time_zone = 'Europe/Madrid'");
            
            $sql = "INSERT INTO cliente_ubicacion (id_cliente, nombre_ubicacion, direccion_ubicacion, codigo_postal_ubicacion, poblacion_ubicacion, 
            provincia_ubicacion, pais_ubicacion, persona_contacto_ubicacion, telefono_contacto_ubicacion, email_contacto_ubicacion, 
            observaciones_ubicacion, es_principal_ubicacion, activo_ubicacion, created_at_ubicacion, updated_at_ubicacion) 
                                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, NOW(), NOW())";
            
            $stmt = $this->conexion->prepare($sql);
            
            if (!$stmt) {
                throw new Exception("Error al preparar la consulta SQL");
            }
                              
            $stmt->bindValue(1, $id_cliente, PDO::PARAM_INT); 
            $stmt->bindValue(2, $nombre_ubicacion, PDO::PARAM_STR); 
            $stmt->bindValue(3, $direccion_ubicacion, PDO::PARAM_STR); 
            $stmt->bindValue(4, $codigo_postal_ubicacion, PDO::PARAM_STR); 
            $stmt->bindValue(5, $poblacion_ubicacion, PDO::PARAM_STR); 
            $stmt->bindValue(6, $provincia_ubicacion, PDO::PARAM_STR); 
            $stmt->bindValue(7, $pais_ubicacion, PDO::PARAM_STR); 
            $stmt->bindValue(8, $persona_contacto_ubicacion, PDO::PARAM_STR); 
            $stmt->bindValue(9, $telefono_contacto_ubicacion, PDO::PARAM_STR); 
            $stmt->bindValue(10, $email_contacto_ubicacion, PDO::PARAM_STR); 
            $stmt->bindValue(11, $observaciones_ubicacion, PDO::PARAM_STR); 
            $stmt->bindValue(12, $es_principal_ubicacion, PDO::PARAM_BOOL); 
                              
            $resultado = $stmt->execute();
            
            if (!$resultado) {
                $errorInfo = $stmt->errorInfo();
                throw new Exception("Error al ejecutar la consulta: " . $errorInfo[2]);
            }
                              
            $idInsert = $this->conexion->lastInsertId();

            $this->registro->registrarActividad(
                'admin',
                'Ubicaciones',
                'Insertar',
                "Se insertó la ubicación con ID: $idInsert",
                'info'
            );

            return $idInsert;
            
        } catch (PDOException $e) {
            throw new Exception("Error SQL en insert_ubicacion: " . $e->getMessage());
        } catch (Exception $e) {
            throw new Exception("Error general en insert_ubicacion: " . $e->getMessage());
        }
    }

    public function update_ubicacion($id_ubicacion, $id_cliente, $nombre_ubicacion, $direccion_ubicacion, $codigo_postal_ubicacion, $poblacion_ubicacion, $provincia_ubicacion, $pais_ubicacion, $persona_contacto_ubicacion, $telefono_contacto_ubicacion, $email_contacto_ubicacion, $observaciones_ubicacion, $es_principal_ubicacion)
    {
        try {
            // Configurar zona horaria para esta operación
            $this->conexion->exec("SET time_zone = 'Europe/Madrid'");
            
            $sql = "UPDATE cliente_ubicacion SET id_cliente = ?, nombre_ubicacion = ?, direccion_ubicacion = ?, codigo_postal_ubicacion = ?, poblacion_ubicacion = ?, provincia_ubicacion = ?, pais_ubicacion = ?, persona_contacto_ubicacion = ?, telefono_contacto_ubicacion = ?, email_contacto_ubicacion = ?, observaciones_ubicacion = ?, es_principal_ubicacion = ?, updated_at_ubicacion = NOW() WHERE id_ubicacion = ?";
            
            $stmt = $this->conexion->prepare($sql);
            
            if (!$stmt) {
                throw new Exception("Error al preparar la consulta de actualización");
            }
            
            $stmt->bindValue(1, $id_cliente, PDO::PARAM_INT);
            $stmt->bindValue(2, $nombre_ubicacion, PDO::PARAM_STR);
            $stmt->bindValue(3, $direccion_ubicacion, PDO::PARAM_STR);
            $stmt->bindValue(4, $codigo_postal_ubicacion, PDO::PARAM_STR);
            $stmt->bindValue(5, $poblacion_ubicacion, PDO::PARAM_STR);
            $stmt->bindValue(6, $provincia_ubicacion, PDO::PARAM_STR);
            $stmt->bindValue(7, $pais_ubicacion, PDO::PARAM_STR);
            $stmt->bindValue(8, $persona_contacto_ubicacion, PDO::PARAM_STR);
            $stmt->bindValue(9, $telefono_contacto_ubicacion, PDO::PARAM_STR);
            $stmt->bindValue(10, $email_contacto_ubicacion, PDO::PARAM_STR);
            $stmt->bindValue(11, $observaciones_ubicacion, PDO::PARAM_STR);
            $stmt->bindValue(12, $es_principal_ubicacion, PDO::PARAM_BOOL);
            $stmt->bindValue(13, $id_ubicacion, PDO::PARAM_INT); 

            $resultado = $stmt->execute();
            
            if (!$resultado) {
                $errorInfo = $stmt->errorInfo();
                throw new Exception("Error al ejecutar la actualización: " . $errorInfo[2]);
            }

            $this->registro->registrarActividad(
                'admin',
                'Ubicaciones',
                'Actualizar',
                "Se actualizó la ubicación con ID: $id_ubicacion",
                'info'
            );

            return $stmt->rowCount();

        } catch (PDOException $e) {
            throw new Exception("Error SQL en update_ubicacion: " . $e->getMessage());
        } catch (Exception $e) {
            throw new Exception("Error general en update_ubicacion: " . $e->getMessage());
        }
    }

    public function verificarUbicacion($nombre_ubicacion, $id_cliente, $id_ubicacion = null)
    {
        try {
            $sql = "SELECT COUNT(*) AS total FROM cliente_ubicacion WHERE LOWER(nombre_ubicacion) = LOWER(?) AND id_cliente = ?";
            $params = [trim($nombre_ubicacion), $id_cliente];
    
            if (!empty($id_ubicacion)) {
                $sql .= " AND id_ubicacion != ?";
                $params[] = $id_ubicacion;
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
                    'Ubicaciones',
                    'verificarUbicacion',
                    "Error al verificar existencia de la ubicación: " . $e->getMessage(),
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
?>
