<?php

require_once '../config/conexion.php'; // ✅ Se incluye correctamente el archivo de conexión
require_once '../config/funciones.php'; // ✅ Se incluye correctamente el archivo de conexión


//  id_contacto_proveedor INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
//     id_proveedor INT UNSIGNED NOT NULL,
//     nombre_contacto_proveedor VARCHAR(100) NOT NULL,
//     apellidos_contacto_proveedor VARCHAR(150),
//     cargo_contacto_proveedor VARCHAR(100),
//     departamento_contacto_proveedor VARCHAR(100),
//     telefono_contacto_proveedor VARCHAR(50),
//     movil_contacto_proveedor VARCHAR(50),
//     email_contacto_proveedor VARCHAR(255),
//     extension_contacto_proveedor VARCHAR(10),
//     principal_contacto_proveedor BOOLEAN DEFAULT FALSE,
//     observaciones_contacto_proveedor TEXT,
//     activo_contacto_proveedor BOOLEAN DEFAULT TRUE,
//     created_at_contacto_proveedor TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
//     updated_at_contacto_proveedor TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP


class Proveedores_contacto
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
                'Proveedores_contacto',
                '__construct',
                "No se pudo establecer zona horaria Madrid: " . $e->getMessage(),
                'warning'
            );
        }
    }

    public function get_contactos_proveedor()
    {
        try {
            $sql = "SELECT * FROM contacto_proveedor ORDER BY nombre_contacto_proveedor ASC";
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->execute();

            return $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC); // Devuelve todos los contactos de proveedores
        } catch (PDOException $e) {
            // Esto para desarrollo
            //die("Error al mostrar los contactos de proveedores: " . $e->getMessage());

            $this->registro->registrarActividad(
                'admin',
                'Proveedores_contacto',
                'get_contactos_proveedor',
                "Error al listar los contactos de proveedores: " . $e->getMessage(),
                "error"
            );


            //En producción, se recomienda registrar el error en un archivo de logs y devolver false
            /*error_log("Error al obtener contactos de proveedores: " . $e->getMessage()); // Registrar error
            return false; // No detener el script, manejar el error en la llamada*/
            // El error se almacena en los logs de PHP o Apache (/var/log/apache2/error.log).
        }
    }

    public function get_contactos_proveedor_disponibles()
    {
        try {
            $sql = "SELECT * FROM contacto_proveedor WHERE activo_contacto_proveedor = 1 ORDER BY nombre_contacto_proveedor ASC";  
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->execute();

            return $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC); // Devuelve todos los contactos de proveedores disponibles
        } catch (PDOException $e) {
            // Esto para desarrollo
            //die("Error al mostrar los contactos de proveedores: " . $e->getMessage());

            $this->registro->registrarActividad(
                'admin',
                'Proveedores_contacto',
                'get_contactos_proveedor_disponibles',
                "Error al listar los contactos de proveedores disponibles: " . $e->getMessage(),
                "error"
            );


            //En producción, se recomienda registrar el error en un archivo de logs y devolver false
            /*error_log("Error al obtener contactos de proveedores: " . $e->getMessage()); // Registrar error
            return false; // No detener el script, manejar el error en la llamada*/
            // El error se almacena en los logs de PHP o Apache (/var/log/apache2/error.log).
        }
    }

    public function get_contacto_proveedorxid($id_contacto_proveedor)
    {
        try {
            $sql = "SELECT * FROM contacto_proveedor where id_contacto_proveedor=?";
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->bindValue(1, $id_contacto_proveedor, PDO::PARAM_INT);
            $stmt->execute();
            //return $resultado = $stmt->fetch(PDO::FETCH_ASSOC); // Solo devuelve un registro;
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC); // Solo devuelve un registro;
                        
            return $resultado; // Devuelve el resultado de un contacto proveedor (fetch)
        } catch (PDOException $e) {
            // Esto para desarrollo
            //die("Error al mostrar el contacto proveedor {$id_contacto_proveedor}:" . $e->getMessage());

            // Esto para producción
            $this->registro->registrarActividad(
                'admin',
                'Proveedores_contacto',
                'get_contacto_proveedorxid',
                "Error al mostrar el contacto proveedor {$id_contacto_proveedor}: " . $e->getMessage(),
                "error"
            );

            return false;
            //En producción, se recomienda registrar el error en un archivo de logs y devolver false
            /*error_log("Error al obtener contacto proveedor: " . $e->getMessage()); // Registrar error
            return false; // No detener el script, manejar el error en la llamada*/
            // El error se almacena en los logs de PHP o Apache (/var/log/apache2/error.log).
        }
    }

    public function get_contactos_by_proveedor($id_proveedor)
    {
        try {
            $sql = "SELECT * FROM contacto_proveedor WHERE id_proveedor = ? ORDER BY principal_contacto_proveedor DESC, nombre_contacto_proveedor ASC";
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->bindValue(1, $id_proveedor, PDO::PARAM_INT);
            $stmt->execute();

            return $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC); // Devuelve todos los contactos del proveedor
        } catch (PDOException $e) {
            // Esto para desarrollo
            //die("Error al mostrar los contactos del proveedor {$id_proveedor}:" . $e->getMessage());

            // Esto para producción
            $this->registro->registrarActividad(
                'admin',
                'Proveedores_contacto',
                'get_contactos_by_proveedor',
                "Error al mostrar los contactos del proveedor {$id_proveedor}: " . $e->getMessage(),
                "error"
            );

            return false;
            //En producción, se recomienda registrar el error en un archivo de logs y devolver false
            /*error_log("Error al obtener contactos por proveedor: " . $e->getMessage()); // Registrar error
            return false; // No detener el script, manejar el error en la llamada*/
            // El error se almacena en los logs de PHP o Apache (/var/log/apache2/error.log).
        }
    }

    public function delete_contacto_proveedorxid($id_contacto_proveedor)
    {
        try {
            $sql = "UPDATE contacto_proveedor set activo_contacto_proveedor=0 where id_contacto_proveedor=?";
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->bindValue(1, $id_contacto_proveedor, PDO::PARAM_INT);
            $stmt->execute();


            // Para los logs
            $this->registro->registrarActividad(
                'admin',
                'Proveedores_contacto',
                'Desactivar',
                "Se desactivó el contacto proveedor con ID: $id_contacto_proveedor",
                'info'
            );
            return $stmt->rowCount() > 0; // Retorna true si se eliminó al menos un registro, false si no existía el ID.
        } catch (PDOException $e) {
            // Esto para desarrollo
            //die("Error al mostrar el contacto proveedor {$id_contacto_proveedor}:" . $e->getMessage());

            // Esto para producción
            $this->registro->registrarActividad(
                'admin',
                'Proveedores_contacto',
                'delete_contacto_proveedorxid',
                "Error al desactivar el contacto proveedor {$id_contacto_proveedor}: " . $e->getMessage(),
                'error'
            );


            return false;

            //En producción, se recomienda registrar el error en un archivo de logs y devolver false
            /*error_log("Error al obtener contacto proveedor: " . $e->getMessage()); // Registrar error
            return false; // No detener el script, manejar el error en la llamada*/
            // El error se almacena en los logs de PHP o Apache (/var/log/apache2/error.log).
        }
    }

    public function activar_contacto_proveedorxid($id_contacto_proveedor)
    {
        try {
            $sql = "UPDATE contacto_proveedor set activo_contacto_proveedor=1 where id_contacto_proveedor=?";
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->bindValue(1, $id_contacto_proveedor, PDO::PARAM_INT);
            $stmt->execute();

            // Para generar los logs
            $this->registro->registrarActividad(
                'admin',
                'Proveedores_contacto',
                'Activar',
                "Se activo el contacto proveedor con ID: $id_contacto_proveedor",
                'info'
            );
            // return $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC); // Devuelve el resultado de un solo usuario (fetch)
            return $stmt->rowCount() > 0; // Retorna true si se eliminó al menos un registro, false si no existía el ID.
        } catch (PDOException $e) {
            // Esto para desarrollo
            //die("Error al activar el contacto proveedor {$id_contacto_proveedor}:" . $e->getMessage());

            // Esto para producción
            $this->registro->registrarActividad(
                'admin',
                'Proveedores_contacto',
                'activar_contacto_proveedorxid',
                "Error al activar el contacto proveedor {$id_contacto_proveedor}: " . $e->getMessage(),
                "error"
            );

            return false;

            //En producción, se recomienda registrar el error en un archivo de logs y devolver false
            /*error_log("Error al obtener contacto proveedor: " . $e->getMessage()); // Registrar error
            return false; // No detener el script, manejar el error en la llamada*/
            // El error se almacena en los logs de PHP o Apache (/var/log/apache2/error.log).
        }
    }


    public function insert_contacto_proveedor($id_proveedor, $nombre_contacto_proveedor, $apellidos_contacto_proveedor, $cargo_contacto_proveedor, $departamento_contacto_proveedor, $telefono_contacto_proveedor, $movil_contacto_proveedor, $email_contacto_proveedor, $extension_contacto_proveedor, $principal_contacto_proveedor, $observaciones_contacto_proveedor)
    {
        try {
            // Configurar zona horaria para esta operación
            $this->conexion->exec("SET time_zone = 'Europe/Madrid'");
            
            $sql = "INSERT INTO contacto_proveedor (id_proveedor, nombre_contacto_proveedor, apellidos_contacto_proveedor, cargo_contacto_proveedor, departamento_contacto_proveedor, 
            telefono_contacto_proveedor, movil_contacto_proveedor, email_contacto_proveedor, extension_contacto_proveedor, principal_contacto_proveedor, 
            observaciones_contacto_proveedor, activo_contacto_proveedor, created_at_contacto_proveedor, updated_at_contacto_proveedor) 
                                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, NOW(), NOW())";
            
            $stmt = $this->conexion->prepare($sql);
            
            if (!$stmt) {
                throw new Exception("Error al preparar la consulta SQL");
            }
                              
            $stmt->bindValue(1, $id_proveedor, PDO::PARAM_INT); 
            $stmt->bindValue(2, $nombre_contacto_proveedor, PDO::PARAM_STR); 
            $stmt->bindValue(3, $apellidos_contacto_proveedor, PDO::PARAM_STR); 
            $stmt->bindValue(4, $cargo_contacto_proveedor, PDO::PARAM_STR); 
            $stmt->bindValue(5, $departamento_contacto_proveedor, PDO::PARAM_STR); 
            $stmt->bindValue(6, $telefono_contacto_proveedor, PDO::PARAM_STR); 
            $stmt->bindValue(7, $movil_contacto_proveedor, PDO::PARAM_STR); 
            $stmt->bindValue(8, $email_contacto_proveedor, PDO::PARAM_STR); 
            $stmt->bindValue(9, $extension_contacto_proveedor, PDO::PARAM_STR); 
            $stmt->bindValue(10, $principal_contacto_proveedor, PDO::PARAM_BOOL); 
            $stmt->bindValue(11, $observaciones_contacto_proveedor, PDO::PARAM_STR); 
                              
            $resultado = $stmt->execute();
            
            if (!$resultado) {
                $errorInfo = $stmt->errorInfo();
                throw new Exception("Error al ejecutar la consulta: " . $errorInfo[2]);
            }
                              
            $idInsert = $this->conexion->lastInsertId();

            // Para generar los logs
            $this->registro->registrarActividad(
                'admin',
                'Proveedores_contacto',
                'Insertar',
                "Se insertó el contacto proveedor con ID: $idInsert",
                'info'
            );

            return $idInsert; // Devuelve el ID del registro insertado
            
        } catch (PDOException $e) {
            // Para desarrollo - devolver el error detallado
            throw new Exception("Error SQL en insert_contacto_proveedor: " . $e->getMessage());
            
        } catch (Exception $e) {
            throw new Exception("Error general en insert_contacto_proveedor: " . $e->getMessage());
        }
    }


    public function update_contacto_proveedor($id_contacto_proveedor, $id_proveedor, $nombre_contacto_proveedor, $apellidos_contacto_proveedor, $cargo_contacto_proveedor, $departamento_contacto_proveedor, $telefono_contacto_proveedor, $movil_contacto_proveedor, $email_contacto_proveedor, $extension_contacto_proveedor, $principal_contacto_proveedor, $observaciones_contacto_proveedor){
        try {
            // Configurar zona horaria para esta operación
            $this->conexion->exec("SET time_zone = 'Europe/Madrid'");
            
            $sql = "UPDATE contacto_proveedor SET id_proveedor = ?, nombre_contacto_proveedor = ?, apellidos_contacto_proveedor = ?, cargo_contacto_proveedor = ?, departamento_contacto_proveedor = ?, telefono_contacto_proveedor = ?, movil_contacto_proveedor = ?, email_contacto_proveedor = ?, extension_contacto_proveedor = ?, principal_contacto_proveedor = ?, observaciones_contacto_proveedor = ?, updated_at_contacto_proveedor = NOW() WHERE id_contacto_proveedor = ?";
            
            $stmt = $this->conexion->prepare($sql);
            
            if (!$stmt) {
                throw new Exception("Error al preparar la consulta de actualización");
            }
            
            $stmt->bindValue(1, $id_proveedor, PDO::PARAM_INT);
            $stmt->bindValue(2, $nombre_contacto_proveedor, PDO::PARAM_STR);
            $stmt->bindValue(3, $apellidos_contacto_proveedor, PDO::PARAM_STR);
            $stmt->bindValue(4, $cargo_contacto_proveedor, PDO::PARAM_STR);
            $stmt->bindValue(5, $departamento_contacto_proveedor, PDO::PARAM_STR);
            $stmt->bindValue(6, $telefono_contacto_proveedor, PDO::PARAM_STR);
            $stmt->bindValue(7, $movil_contacto_proveedor, PDO::PARAM_STR);
            $stmt->bindValue(8, $email_contacto_proveedor, PDO::PARAM_STR);
            $stmt->bindValue(9, $extension_contacto_proveedor, PDO::PARAM_STR);
            $stmt->bindValue(10, $principal_contacto_proveedor, PDO::PARAM_BOOL);
            $stmt->bindValue(11, $observaciones_contacto_proveedor, PDO::PARAM_STR);
            $stmt->bindValue(12, $id_contacto_proveedor, PDO::PARAM_INT); 

            $resultado = $stmt->execute();
            
            if (!$resultado) {
                $errorInfo = $stmt->errorInfo();
                throw new Exception("Error al ejecutar la actualización: " . $errorInfo[2]);
            }

            // Para generar los logs
            $this->registro->registrarActividad(
                'admin',
                'Proveedores_contacto',
                'Actualizar',
                "Se actualizó el contacto proveedor con ID: $id_contacto_proveedor",
                'info'
            );

            return $stmt->rowCount(); // Devuelve el número de filas afectadas

        } catch (PDOException $e) {
            throw new Exception("Error SQL en update_contacto_proveedor: " . $e->getMessage());
        } catch (Exception $e) {
            throw new Exception("Error general en update_contacto_proveedor: " . $e->getMessage());
        }
    }

    public function verificarContactoProveedor($nombre_contacto_proveedor, $id_proveedor, $id_contacto_proveedor = null)
    {
        try {
            // Consulta SQL base - verificamos por nombre del contacto y proveedor
            $sql = "SELECT COUNT(*) AS total FROM contacto_proveedor WHERE LOWER(nombre_contacto_proveedor) = LOWER(?) AND id_proveedor = ?";
            $params = [trim($nombre_contacto_proveedor), $id_proveedor];
    
            // Si es edición, excluimos el ID actual
            if (!empty($id_contacto_proveedor)) {
                $sql .= " AND id_contacto_proveedor != ?";
                $params[] = $id_contacto_proveedor;
            }
    
            // Ejecución de la consulta
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute($params);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
    
            return [
                'existe' => ($resultado['total'] > 0)
            ];
    
        } catch (PDOException $e) {
            // Registro de error - Versión exacta que solicitaste mantener
            if (isset($this->registro)) {
                $this->registro->registrarActividad(
                    null,
                    'Proveedores_contacto',
                    'verificarContactoProveedor',
                    "Error al verificar existencia del contacto proveedor: " . $e->getMessage(),
                    'error'
                );
            }
    
            return [
                'existe' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public function verificarContactoPrincipal($id_proveedor, $id_contacto_proveedor = null)
    {
        try {
            // Consulta SQL base - verificamos si ya existe un contacto principal para el proveedor
            $sql = "SELECT COUNT(*) AS total FROM contacto_proveedor WHERE id_proveedor = ? AND principal_contacto_proveedor = 1";
            $params = [$id_proveedor];
    
            // Si es edición, excluimos el ID actual
            if (!empty($id_contacto_proveedor)) {
                $sql .= " AND id_contacto_proveedor != ?";
                $params[] = $id_contacto_proveedor;
            }
    
            // Ejecución de la consulta
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute($params);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
    
            return [
                'existe_principal' => ($resultado['total'] > 0)
            ];
    
        } catch (PDOException $e) {
            // Registro de error
            if (isset($this->registro)) {
                $this->registro->registrarActividad(
                    null,
                    'Proveedores_contacto',
                    'verificarContactoPrincipal',
                    "Error al verificar contacto principal: " . $e->getMessage(),
                    'error'
                );
            }
    
            return [
                'existe_principal' => false,
                'error' => $e->getMessage()
            ];
        }
    }

}
?>