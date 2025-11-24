<?php

require_once '../config/conexion.php';
require_once '../config/funciones.php';

class Clientes_contacto
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
                'Clientes_contacto',
                '__construct',
                "No se pudo establecer zona horaria Madrid: " . $e->getMessage(),
                'warning'
            );
        }
    }

    public function get_contactos_cliente()
    {
        try {
            $sql = "SELECT cc.*, c.nombre_cliente 
                    FROM contacto_cliente cc
                    INNER JOIN cliente c ON cc.id_cliente = c.id_cliente
                    ORDER BY c.nombre_cliente ASC, cc.principal_contacto_cliente DESC, cc.nombre_contacto_cliente ASC";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Clientes_contacto',
                'get_contactos_cliente',
                "Error al listar contactos de clientes: " . $e->getMessage(),
                "error"
            );
            return [];
        }
    }

    public function get_contactos_by_cliente($id_cliente)
    {
        try {
            $sql = "SELECT cc.*, c.nombre_cliente 
                    FROM contacto_cliente cc
                    INNER JOIN cliente c ON cc.id_cliente = c.id_cliente
                    WHERE cc.id_cliente = ? 
                    ORDER BY cc.principal_contacto_cliente DESC, cc.nombre_contacto_cliente ASC";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_cliente, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Clientes_contacto',
                'get_contactos_by_cliente',
                "Error al listar contactos del cliente {$id_cliente}: " . $e->getMessage(),
                "error"
            );
            return [];
        }
    }

    public function get_contacto_clientexid($id_contacto_cliente)
    {
        try {
            $sql = "SELECT cc.*, c.nombre_cliente 
                    FROM contacto_cliente cc
                    INNER JOIN cliente c ON cc.id_cliente = c.id_cliente
                    WHERE cc.id_contacto_cliente = ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_contacto_cliente, PDO::PARAM_INT);
            $stmt->execute();
            
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado;
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Clientes_contacto',
                'get_contacto_clientexid',
                "Error al mostrar el contacto cliente {$id_contacto_cliente}: " . $e->getMessage(),
                "error"
            );
            return false;
        }
    }

    public function activar_contacto_clientexid($id_contacto_cliente)
    {
        try {
            $sql = "UPDATE contacto_cliente SET activo_contacto_cliente = 1 WHERE id_contacto_cliente = ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_contacto_cliente, PDO::PARAM_INT);
            $stmt->execute();

            $this->registro->registrarActividad(
                'admin',
                'Clientes_contacto',
                'Activar',
                "Se activó el contacto cliente con ID: $id_contacto_cliente",
                'info'
            );
            
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Clientes_contacto',
                'activar_contacto_clientexid',
                "Error al activar el contacto cliente {$id_contacto_cliente}: " . $e->getMessage(),
                "error"
            );
            return false;
        }
    }

    public function desactivar_contacto_clientexid($id_contacto_cliente)
    {
        try {
            $sql = "UPDATE contacto_cliente SET activo_contacto_cliente = 0 WHERE id_contacto_cliente = ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_contacto_cliente, PDO::PARAM_INT);
            $stmt->execute();

            $this->registro->registrarActividad(
                'admin',
                'Clientes_contacto',
                'Desactivar',
                "Se desactivó el contacto cliente con ID: $id_contacto_cliente",
                'info'
            );
            
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Clientes_contacto',
                'desactivar_contacto_clientexid',
                "Error al desactivar el contacto cliente {$id_contacto_cliente}: " . $e->getMessage(),
                "error"
            );
            return false;
        }
    }

    public function insert_contacto_cliente($id_cliente, $nombre_contacto_cliente, $apellidos_contacto_cliente, $cargo_contacto_cliente, $departamento_contacto_cliente, $telefono_contacto_cliente, $movil_contacto_cliente, $email_contacto_cliente, $extension_contacto_cliente, $principal_contacto_cliente, $observaciones_contacto_cliente)
    {
        try {
            // Configurar zona horaria para esta operación
            $this->conexion->exec("SET time_zone = 'Europe/Madrid'");
            
            $sql = "INSERT INTO contacto_cliente (id_cliente, nombre_contacto_cliente, apellidos_contacto_cliente, cargo_contacto_cliente, departamento_contacto_cliente, 
            telefono_contacto_cliente, movil_contacto_cliente, email_contacto_cliente, extension_contacto_cliente, principal_contacto_cliente, 
            observaciones_contacto_cliente, activo_contacto_cliente, created_at_contacto_cliente, updated_at_contacto_cliente) 
                                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, NOW(), NOW())";
            
            $stmt = $this->conexion->prepare($sql);
            
            if (!$stmt) {
                throw new Exception("Error al preparar la consulta SQL");
            }
                              
            $stmt->bindValue(1, $id_cliente, PDO::PARAM_INT); 
            $stmt->bindValue(2, $nombre_contacto_cliente, PDO::PARAM_STR); 
            $stmt->bindValue(3, $apellidos_contacto_cliente, PDO::PARAM_STR); 
            $stmt->bindValue(4, $cargo_contacto_cliente, PDO::PARAM_STR); 
            $stmt->bindValue(5, $departamento_contacto_cliente, PDO::PARAM_STR); 
            $stmt->bindValue(6, $telefono_contacto_cliente, PDO::PARAM_STR); 
            $stmt->bindValue(7, $movil_contacto_cliente, PDO::PARAM_STR); 
            $stmt->bindValue(8, $email_contacto_cliente, PDO::PARAM_STR); 
            $stmt->bindValue(9, $extension_contacto_cliente, PDO::PARAM_STR); 
            $stmt->bindValue(10, $principal_contacto_cliente, PDO::PARAM_BOOL); 
            $stmt->bindValue(11, $observaciones_contacto_cliente, PDO::PARAM_STR); 
                              
            $resultado = $stmt->execute();
            
            if (!$resultado) {
                $errorInfo = $stmt->errorInfo();
                throw new Exception("Error al ejecutar la consulta: " . $errorInfo[2]);
            }
                              
            $idInsert = $this->conexion->lastInsertId();

            $this->registro->registrarActividad(
                'admin',
                'Clientes_contacto',
                'Insertar',
                "Se insertó el contacto cliente con ID: $idInsert",
                'info'
            );

            return $idInsert;
            
        } catch (PDOException $e) {
            throw new Exception("Error SQL en insert_contacto_cliente: " . $e->getMessage());
        } catch (Exception $e) {
            throw new Exception("Error general en insert_contacto_cliente: " . $e->getMessage());
        }
    }

    public function update_contacto_cliente($id_contacto_cliente, $id_cliente, $nombre_contacto_cliente, $apellidos_contacto_cliente, $cargo_contacto_cliente, $departamento_contacto_cliente, $telefono_contacto_cliente, $movil_contacto_cliente, $email_contacto_cliente, $extension_contacto_cliente, $principal_contacto_cliente, $observaciones_contacto_cliente)
    {
        try {
            // Configurar zona horaria para esta operación
            $this->conexion->exec("SET time_zone = 'Europe/Madrid'");
            
            $sql = "UPDATE contacto_cliente SET id_cliente = ?, nombre_contacto_cliente = ?, apellidos_contacto_cliente = ?, cargo_contacto_cliente = ?, departamento_contacto_cliente = ?, telefono_contacto_cliente = ?, movil_contacto_cliente = ?, email_contacto_cliente = ?, extension_contacto_cliente = ?, principal_contacto_cliente = ?, observaciones_contacto_cliente = ?, updated_at_contacto_cliente = NOW() WHERE id_contacto_cliente = ?";
            
            $stmt = $this->conexion->prepare($sql);
            
            if (!$stmt) {
                throw new Exception("Error al preparar la consulta de actualización");
            }
            
            $stmt->bindValue(1, $id_cliente, PDO::PARAM_INT);
            $stmt->bindValue(2, $nombre_contacto_cliente, PDO::PARAM_STR);
            $stmt->bindValue(3, $apellidos_contacto_cliente, PDO::PARAM_STR);
            $stmt->bindValue(4, $cargo_contacto_cliente, PDO::PARAM_STR);
            $stmt->bindValue(5, $departamento_contacto_cliente, PDO::PARAM_STR);
            $stmt->bindValue(6, $telefono_contacto_cliente, PDO::PARAM_STR);
            $stmt->bindValue(7, $movil_contacto_cliente, PDO::PARAM_STR);
            $stmt->bindValue(8, $email_contacto_cliente, PDO::PARAM_STR);
            $stmt->bindValue(9, $extension_contacto_cliente, PDO::PARAM_STR);
            $stmt->bindValue(10, $principal_contacto_cliente, PDO::PARAM_BOOL);
            $stmt->bindValue(11, $observaciones_contacto_cliente, PDO::PARAM_STR);
            $stmt->bindValue(12, $id_contacto_cliente, PDO::PARAM_INT); 

            $resultado = $stmt->execute();
            
            if (!$resultado) {
                $errorInfo = $stmt->errorInfo();
                throw new Exception("Error al ejecutar la actualización: " . $errorInfo[2]);
            }

            $this->registro->registrarActividad(
                'admin',
                'Clientes_contacto',
                'Actualizar',
                "Se actualizó el contacto cliente con ID: $id_contacto_cliente",
                'info'
            );

            return $stmt->rowCount();

        } catch (PDOException $e) {
            throw new Exception("Error SQL en update_contacto_cliente: " . $e->getMessage());
        } catch (Exception $e) {
            throw new Exception("Error general en update_contacto_cliente: " . $e->getMessage());
        }
    }

    public function verificarContactoCliente($nombre_contacto_cliente, $id_cliente, $id_contacto_cliente = null)
    {
        try {
            $sql = "SELECT COUNT(*) AS total FROM contacto_cliente WHERE LOWER(nombre_contacto_cliente) = LOWER(?) AND id_cliente = ?";
            $params = [trim($nombre_contacto_cliente), $id_cliente];
    
            if (!empty($id_contacto_cliente)) {
                $sql .= " AND id_contacto_cliente != ?";
                $params[] = $id_contacto_cliente;
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
                    'Clientes_contacto',
                    'verificarContactoCliente',
                    "Error al verificar existencia del contacto cliente: " . $e->getMessage(),
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