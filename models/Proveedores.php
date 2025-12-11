<?php

require_once '../config/conexion.php'; // ✅ Se incluye correctamente el archivo de conexión
require_once '../config/funciones.php'; // ✅ Se incluye correctamente el archivo de conexión


//  id_proveedor INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
//     codigo_proveedor VARCHAR(20) NOT NULL UNIQUE,
//     nombre_proveedor VARCHAR(255) NOT NULL,
//     direccion_proveedor VARCHAR(255),
//     cp_proveedor VARCHAR(10),
//     poblacion_proveedor VARCHAR(100),
//     provincia_proveedor VARCHAR(100),
//     nif_proveedor VARCHAR(20),
//     telefono_proveedor VARCHAR(255),
//     fax_proveedor VARCHAR(50),
//     web_proveedor VARCHAR(255),
//     email_proveedor VARCHAR(255),
//     persona_contacto_proveedor VARCHAR(255),
//     direccion_sat_proveedor VARCHAR(255),
//     cp_sat_proveedor VARCHAR(10),
//     poblacion_sat_proveedor VARCHAR(100),
//     provincia_sat_proveedor VARCHAR(100),
//     telefono_sat_proveedor VARCHAR(255),
//     fax_sat_proveedor VARCHAR(50),
//     email_sat_proveedor VARCHAR(255),
//        -- =====================================================
//        -- FORMA DE PAGO HABITUAL DEL PROVEEDOR
//        -- =====================================================
//        id_forma_pago_habitual INT UNSIGNED 
//        COMMENT 'Forma de pago habitual del proveedor. Se usará por defecto en nuevas órdenes de compra',
//     observaciones_proveedor TEXT,
//     activo_proveedor BOOLEAN DEFAULT TRUE,
//     created_at_proveedor TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
//     updated_at_proveedor TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP


class Proveedores
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
                'Proveedores',
                '__construct',
                "No se pudo establecer zona horaria Madrid: " . $e->getMessage(),
                'warning'
            );
        }
    }

    public function get_proveedores()
    {
        try {
            
            // $sql = "SELECT * FROM proveedor ORDER BY nombre_proveedor ASC";
            $sql = "SELECT * FROM contacto_cantidad_proveedor ORDER BY nombre_proveedor ASC";
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->execute();

            return $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC); // Devuelve todos los proveedores
        } catch (PDOException $e) {
            // Esto para desarrollo
            //die("Error al mostrar los proveedores: " . $e->getMessage());

            $this->registro->registrarActividad(
                'admin',
                'Proveedores',
                'get_proveedores',
                "Error al listar los proveedores: " . $e->getMessage(),
                "error"
            );


            //En producción, se recomienda registrar el error en un archivo de logs y devolver false
            /*error_log("Error al obtener proveedores: " . $e->getMessage()); // Registrar error
            return false; // No detener el script, manejar el error en la llamada*/
            // El error se almacena en los logs de PHP o Apache (/var/log/apache2/error.log).
        }
    }

    public function get_proveedores_disponibles()
    {
        try {
            $sql = "SELECT * FROM contacto_cantidad_proveedor WHERE activo_proveedor = 1 ORDER BY nombre_proveedor ASC";  
            // $sql = "SELECT * FROM proveedor WHERE activo_proveedor = 1 ORDER BY nombre_proveedor ASC";  
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->execute();

            return $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC); // Devuelve todos los proveedores disponibles
        } catch (PDOException $e) {
            // Esto para desarrollo
            //die("Error al mostrar los proveedores: " . $e->getMessage());

            $this->registro->registrarActividad(
                'admin',
                'Proveedores',
                'get_proveedores_disponibles',
                "Error al listar los proveedores disponibles: " . $e->getMessage(),
                "error"
            );


            //En producción, se recomienda registrar el error en un archivo de logs y devolver false
            /*error_log("Error al obtener proveedores: " . $e->getMessage()); // Registrar error
            return false; // No detener el script, manejar el error en la llamada*/
            // El error se almacena en los logs de PHP o Apache (/var/log/apache2/error.log).
        }
    }

    public function get_proveedorxid($id_proveedor)
    {
        try {
            $sql = "SELECT * FROM contacto_cantidad_proveedor where id_proveedor=?";
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->bindValue(1, $id_proveedor, PDO::PARAM_INT);
            $stmt->execute();
            //return $resultado = $stmt->fetch(PDO::FETCH_ASSOC); // Solo devuelve un registro;
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC); // Solo devuelve un registro;
                        
            return $resultado; // Devuelve el resultado de un proveedor (fetch)
        } catch (PDOException $e) {
            // Esto para desarrollo
            //die("Error al mostrar el proveedor {$id_proveedor}:" . $e->getMessage());

            // Esto para producción
            $this->registro->registrarActividad(
                'admin',
                'Proveedores',
                'get_proveedorxid',
                "Error al mostrar el proveedor {$id_proveedor}: " . $e->getMessage(),
                "error"
            );

            return false;
            //En producción, se recomienda registrar el error en un archivo de logs y devolver false
            /*error_log("Error al obtener proveedor: " . $e->getMessage()); // Registrar error
            return false; // No detener el script, manejar el error en la llamada*/
            // El error se almacena en los logs de PHP o Apache (/var/log/apache2/error.log).
        }
    }

    public function delete_proveedorxid($id_proveedor)
    {
        try {
            $sql = "UPDATE proveedor set activo_proveedor=0 where id_proveedor=?";
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->bindValue(1, $id_proveedor, PDO::PARAM_INT);
            $stmt->execute();


            // Para los logs
            $this->registro->registrarActividad(
                'admin',
                'Proveedores',
                'Desactivar',
                "Se desactivó el proveedor con ID: $id_proveedor",
                'info'
            );
            return $stmt->rowCount() > 0; // Retorna true si se eliminó al menos un registro, false si no existía el ID.
        } catch (PDOException $e) {
            // Esto para desarrollo
            //die("Error al mostrar el proveedor {$id_proveedor}:" . $e->getMessage());

            // Esto para producción
            $this->registro->registrarActividad(
                'admin',
                'Proveedores',
                'delete_proveedorxid',
                "Error al desactivar el proveedor {$id_proveedor}: " . $e->getMessage(),
                'error'
            );


            return false;

            //En producción, se recomienda registrar el error en un archivo de logs y devolver false
            /*error_log("Error al obtener proveedor: " . $e->getMessage()); // Registrar error
            return false; // No detener el script, manejar el error en la llamada*/
            // El error se almacena en los logs de PHP o Apache (/var/log/apache2/error.log).
        }
    }

    public function activar_proveedorxid($id_proveedor)
    {
        try {
            $sql = "UPDATE proveedor set activo_proveedor=1 where id_proveedor=?";
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->bindValue(1, $id_proveedor, PDO::PARAM_INT);
            $stmt->execute();

            // Para generar los logs
            $this->registro->registrarActividad(
                'admin',
                'Proveedores',
                'Activar',
                "Se activo el proveedor con ID: $id_proveedor",
                'info'
            );
            // return $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC); // Devuelve el resultado de un solo usuario (fetch)
            return $stmt->rowCount() > 0; // Retorna true si se eliminó al menos un registro, false si no existía el ID.
        } catch (PDOException $e) {
            // Esto para desarrollo
            //die("Error al activar el proveedor {$id_proveedor}:" . $e->getMessage());

            // Esto para producción
            $this->registro->registrarActividad(
                'admin',
                'Proveedores',
                'activar_proveedorxid',
                "Error al activar el proveedor {$id_proveedor}: " . $e->getMessage(),
                "error"
            );

            return false;

            //En producción, se recomienda registrar el error en un archivo de logs y devolver false
            /*error_log("Error al obtener proveedor: " . $e->getMessage()); // Registrar error
            return false; // No detener el script, manejar el error en la llamada*/
            // El error se almacena en los logs de PHP o Apache (/var/log/apache2/error.log).
        }
    }


    public function insert_proveedor($codigo_proveedor, $nombre_proveedor, $direccion_proveedor, $cp_proveedor, $poblacion_proveedor, $provincia_proveedor, $nif_proveedor, $telefono_proveedor, $fax_proveedor, $web_proveedor, $email_proveedor, $persona_contacto_proveedor, $direccion_sat_proveedor, $cp_sat_proveedor, $poblacion_sat_proveedor, $provincia_sat_proveedor, $telefono_sat_proveedor, $fax_sat_proveedor, $email_sat_proveedor, $id_forma_pago_habitual, $observaciones_proveedor)
    {
        try {
            // Configurar zona horaria para esta operación
            $this->conexion->exec("SET time_zone = 'Europe/Madrid'");
            
            $sql = "INSERT INTO proveedor (codigo_proveedor, nombre_proveedor, direccion_proveedor, cp_proveedor, poblacion_proveedor, provincia_proveedor, 
            nif_proveedor, telefono_proveedor, fax_proveedor, web_proveedor, email_proveedor, persona_contacto_proveedor, direccion_sat_proveedor, 
            cp_sat_proveedor, poblacion_sat_proveedor, provincia_sat_proveedor, telefono_sat_proveedor, fax_sat_proveedor, email_sat_proveedor, 
            id_forma_pago_habitual, observaciones_proveedor, activo_proveedor, created_at_proveedor, updated_at_proveedor) 
                                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, NOW(), NOW())";
            
            $stmt = $this->conexion->prepare($sql);
            
            if (!$stmt) {
                throw new Exception("Error al preparar la consulta SQL");
            }
                              
            $stmt->bindValue(1, $codigo_proveedor, PDO::PARAM_STR); 
            $stmt->bindValue(2, $nombre_proveedor, PDO::PARAM_STR); 
            $stmt->bindValue(3, $direccion_proveedor, PDO::PARAM_STR); 
            $stmt->bindValue(4, $cp_proveedor, PDO::PARAM_STR); 
            $stmt->bindValue(5, $poblacion_proveedor, PDO::PARAM_STR); 
            $stmt->bindValue(6, $provincia_proveedor, PDO::PARAM_STR); 
            $stmt->bindValue(7, $nif_proveedor, PDO::PARAM_STR); 
            $stmt->bindValue(8, $telefono_proveedor, PDO::PARAM_STR); 
            $stmt->bindValue(9, $fax_proveedor, PDO::PARAM_STR); 
            $stmt->bindValue(10, $web_proveedor, PDO::PARAM_STR); 
            $stmt->bindValue(11, $email_proveedor, PDO::PARAM_STR); 
            $stmt->bindValue(12, $persona_contacto_proveedor, PDO::PARAM_STR); 
            $stmt->bindValue(13, $direccion_sat_proveedor, PDO::PARAM_STR); 
            $stmt->bindValue(14, $cp_sat_proveedor, PDO::PARAM_STR); 
            $stmt->bindValue(15, $poblacion_sat_proveedor, PDO::PARAM_STR); 
            $stmt->bindValue(16, $provincia_sat_proveedor, PDO::PARAM_STR); 
            $stmt->bindValue(17, $telefono_sat_proveedor, PDO::PARAM_STR); 
            $stmt->bindValue(18, $fax_sat_proveedor, PDO::PARAM_STR); 
            $stmt->bindValue(19, $email_sat_proveedor, PDO::PARAM_STR); 
            $stmt->bindValue(20, $id_forma_pago_habitual, PDO::PARAM_INT); 
            $stmt->bindValue(21, $observaciones_proveedor, PDO::PARAM_STR); 
                              
            $resultado = $stmt->execute();
            
            if (!$resultado) {
                $errorInfo = $stmt->errorInfo();
                throw new Exception("Error al ejecutar la consulta: " . $errorInfo[2]);
            }
                              
            $idInsert = $this->conexion->lastInsertId();

            // Para generar los logs
            $this->registro->registrarActividad(
                'admin',
                'Proveedores',
                'Insertar',
                "Se insertó el proveedor con ID: $idInsert",
                'info'
            );

            return $idInsert; // Devuelve el ID del registro insertado
            
        } catch (PDOException $e) {
            // Para desarrollo - devolver el error detallado
            throw new Exception("Error SQL en insert_proveedor: " . $e->getMessage());
            
        } catch (Exception $e) {
            throw new Exception("Error general en insert_proveedor: " . $e->getMessage());
        }
    }


    public function update_proveedor($id_proveedor, $codigo_proveedor, $nombre_proveedor, $direccion_proveedor, $cp_proveedor, $poblacion_proveedor, $provincia_proveedor, $nif_proveedor, $telefono_proveedor, $fax_proveedor, $web_proveedor, $email_proveedor, $persona_contacto_proveedor, $direccion_sat_proveedor, $cp_sat_proveedor, $poblacion_sat_proveedor, $provincia_sat_proveedor, $telefono_sat_proveedor, $fax_sat_proveedor, $email_sat_proveedor, $id_forma_pago_habitual, $observaciones_proveedor){
        try {
            // Configurar zona horaria para esta operación
            $this->conexion->exec("SET time_zone = 'Europe/Madrid'");
            
            $sql = "UPDATE proveedor SET codigo_proveedor = ?, nombre_proveedor = ?, direccion_proveedor = ?, cp_proveedor = ?, poblacion_proveedor = ?, provincia_proveedor = ?, nif_proveedor = ?, telefono_proveedor = ?, fax_proveedor = ?, web_proveedor = ?, email_proveedor = ?, persona_contacto_proveedor = ?, direccion_sat_proveedor = ?, cp_sat_proveedor = ?, poblacion_sat_proveedor = ?, provincia_sat_proveedor = ?, telefono_sat_proveedor = ?, fax_sat_proveedor = ?, email_sat_proveedor = ?, id_forma_pago_habitual = ?, observaciones_proveedor = ?, updated_at_proveedor = NOW() WHERE id_proveedor = ?";
            
            $stmt = $this->conexion->prepare($sql);
            
            if (!$stmt) {
                throw new Exception("Error al preparar la consulta de actualización");
            }
            
            $stmt->bindValue(1, $codigo_proveedor, PDO::PARAM_STR);
            $stmt->bindValue(2, $nombre_proveedor, PDO::PARAM_STR);
            $stmt->bindValue(3, $direccion_proveedor, PDO::PARAM_STR);
            $stmt->bindValue(4, $cp_proveedor, PDO::PARAM_STR);
            $stmt->bindValue(5, $poblacion_proveedor, PDO::PARAM_STR);
            $stmt->bindValue(6, $provincia_proveedor, PDO::PARAM_STR);
            $stmt->bindValue(7, $nif_proveedor, PDO::PARAM_STR);
            $stmt->bindValue(8, $telefono_proveedor, PDO::PARAM_STR);
            $stmt->bindValue(9, $fax_proveedor, PDO::PARAM_STR);
            $stmt->bindValue(10, $web_proveedor, PDO::PARAM_STR);
            $stmt->bindValue(11, $email_proveedor, PDO::PARAM_STR);
            $stmt->bindValue(12, $persona_contacto_proveedor, PDO::PARAM_STR);
            $stmt->bindValue(13, $direccion_sat_proveedor, PDO::PARAM_STR);
            $stmt->bindValue(14, $cp_sat_proveedor, PDO::PARAM_STR);
            $stmt->bindValue(15, $poblacion_sat_proveedor, PDO::PARAM_STR);
            $stmt->bindValue(16, $provincia_sat_proveedor, PDO::PARAM_STR);
            $stmt->bindValue(17, $telefono_sat_proveedor, PDO::PARAM_STR);
            $stmt->bindValue(18, $fax_sat_proveedor, PDO::PARAM_STR);
            $stmt->bindValue(19, $email_sat_proveedor, PDO::PARAM_STR);
            $stmt->bindValue(20, $id_forma_pago_habitual, PDO::PARAM_INT);
            $stmt->bindValue(21, $observaciones_proveedor, PDO::PARAM_STR);
            $stmt->bindValue(22, $id_proveedor, PDO::PARAM_INT); 

            $resultado = $stmt->execute();
            
            if (!$resultado) {
                $errorInfo = $stmt->errorInfo();
                throw new Exception("Error al ejecutar la actualización: " . $errorInfo[2]);
            }

            // Para generar los logs
            $this->registro->registrarActividad(
                'admin',
                'Proveedores',
                'Actualizar',
                "Se actualizó el proveedor con ID: $id_proveedor",
                'info'
            );

            return $stmt->rowCount(); // Devuelve el número de filas afectadas

        } catch (PDOException $e) {
            throw new Exception("Error SQL en update_proveedor: " . $e->getMessage());
        } catch (Exception $e) {
            throw new Exception("Error general en update_proveedor: " . $e->getMessage());
        }
    }

    public function verificarProveedor($codigo_proveedor, $nombre_proveedor = null, $id_proveedor = null)
    {
        try {
            // Consulta SQL base - verificamos por código y nombre
            $sql = "SELECT COUNT(*) AS total FROM proveedor WHERE (LOWER(codigo_proveedor) = LOWER(?) OR LOWER(nombre_proveedor) = LOWER(?))";
            $params = [trim($codigo_proveedor), trim($nombre_proveedor)];
    
            // Si es edición, excluimos el ID actual
            if (!empty($id_proveedor)) {
                $sql .= " AND id_proveedor != ?";
                $params[] = $id_proveedor;
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
                    'Proveedores',
                    'verificarProveedor',
                    "Error al verificar existencia del proveedor: " . $e->getMessage(),
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