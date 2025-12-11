<?php

require_once '../config/conexion.php'; // ✅ Se incluye correctamente el archivo de conexión
require_once '../config/funciones.php'; // ✅ Se incluye correctamente el archivo de conexión


//  id_cliente INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
//     codigo_cliente VARCHAR(20) NOT NULL UNIQUE,
//     nombre_cliente VARCHAR(255) NOT NULL,
//     direccion_cliente VARCHAR(255),
//     cp_cliente VARCHAR(10),
//     poblacion_cliente VARCHAR(100),
//     provincia_cliente VARCHAR(100),
//     nif_cliente VARCHAR(20),
//     telefono_cliente VARCHAR(255),
//     fax_cliente VARCHAR(50),
//     web_cliente VARCHAR(255),
//     email_cliente VARCHAR(255),
//     nombre_facturacion_cliente VARCHAR(255),
//     direccion_facturacion_cliente VARCHAR(255),
//     cp_facturacion_cliente VARCHAR(10),
//     poblacion_facturacion_cliente VARCHAR(100),
//     provincia_facturacion_cliente VARCHAR(100),
//     id_forma_pago_habitual INT UNSIGNED,
//     observaciones_cliente TEXT,
//     activo_cliente BOOLEAN DEFAULT TRUE,
//     created_at_cliente TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
//     updated_at_cliente TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP


class Clientes
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
                'Clientes',
                '__construct',
                "No se pudo establecer zona horaria Madrid: " . $e->getMessage(),
                'warning'
            );
        }
    }

    public function get_clientes()
    {
        try {
            $sql = "SELECT * FROM contacto_cantidad_cliente ORDER BY nombre_cliente ASC";
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->execute();

            return $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC); // Devuelve todos los clientes
        } catch (PDOException $e) {
            // Esto para desarrollo
            //die("Error al mostrar los clientes: " . $e->getMessage());

            $this->registro->registrarActividad(
                'admin',
                'Clientes',
                'get_clientes',
                "Error al listar los clientes: " . $e->getMessage(),
                "error"
            );


            //En producción, se recomienda registrar el error en un archivo de logs y devolver false
            /*error_log("Error al obtener clientes: " . $e->getMessage()); // Registrar error
            return false; // No detener el script, manejar el error en la llamada*/
            // El error se almacena en los logs de PHP o Apache (/var/log/apache2/error.log).
        }
    }

    public function get_clientes_disponibles()
    {
        try {
            $sql = "SELECT * FROM contacto_cantidad_cliente WHERE activo_cliente = 1 ORDER BY nombre_cliente ASC";  
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->execute();

            return $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC); // Devuelve todos los clientes disponibles
        } catch (PDOException $e) {
            // Esto para desarrollo
            //die("Error al mostrar los clientes: " . $e->getMessage());

            $this->registro->registrarActividad(
                'admin',
                'Clientes',
                'get_clientes_disponibles',
                "Error al listar los clientes disponibles: " . $e->getMessage(),
                "error"
            );


            //En producción, se recomienda registrar el error en un archivo de logs y devolver false
            /*error_log("Error al obtener clientes: " . $e->getMessage()); // Registrar error
            return false; // No detener el script, manejar el error en la llamada*/
            // El error se almacena en los logs de PHP o Apache (/var/log/apache2/error.log).
        }
    }

    public function get_clientexid($id_cliente)
    {
        try {
            $sql = "SELECT * FROM contacto_cantidad_cliente where id_cliente=?";
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->bindValue(1, $id_cliente, PDO::PARAM_INT);
            $stmt->execute();
            //return $resultado = $stmt->fetch(PDO::FETCH_ASSOC); // Solo devuelve un registro;
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC); // Solo devuelve un registro;
                        
            return $resultado; // Devuelve el resultado de un cliente (fetch)
        } catch (PDOException $e) {
            // Esto para desarrollo
            //die("Error al mostrar el cliente {$id_cliente}:" . $e->getMessage());

            // Esto para producción
            $this->registro->registrarActividad(
                'admin',
                'Clientes',
                'get_clientexid',
                "Error al mostrar el cliente {$id_cliente}: " . $e->getMessage(),
                "error"
            );

            return false;
            //En producción, se recomienda registrar el error en un archivo de logs y devolver false
            /*error_log("Error al obtener cliente: " . $e->getMessage()); // Registrar error
            return false; // No detener el script, manejar el error en la llamada*/
            // El error se almacena en los logs de PHP o Apache (/var/log/apache2/error.log).
        }
    }

    public function delete_clientexid($id_cliente)
    {
        try {
            $sql = "UPDATE cliente set activo_cliente=0 where id_cliente=?";
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->bindValue(1, $id_cliente, PDO::PARAM_INT);
            $stmt->execute();


            // Para los logs
            $this->registro->registrarActividad(
                'admin',
                'Clientes',
                'Desactivar',
                "Se desactivó el cliente con ID: $id_cliente",
                'info'
            );
            return $stmt->rowCount() > 0; // Retorna true si se eliminó al menos un registro, false si no existía el ID.
        } catch (PDOException $e) {
            // Esto para desarrollo
            //die("Error al mostrar el cliente {$id_cliente}:" . $e->getMessage());

            // Esto para producción
            $this->registro->registrarActividad(
                'admin',
                'Clientes',
                'delete_clientexid',
                "Error al desactivar el cliente {$id_cliente}: " . $e->getMessage(),
                'error'
            );


            return false;

            //En producción, se recomienda registrar el error en un archivo de logs y devolver false
            /*error_log("Error al obtener cliente: " . $e->getMessage()); // Registrar error
            return false; // No detener el script, manejar el error en la llamada*/
            // El error se almacena en los logs de PHP o Apache (/var/log/apache2/error.log).
        }
    }

    public function activar_clientexid($id_cliente)
    {
        try {
            $sql = "UPDATE cliente set activo_cliente=1 where id_cliente=?";
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->bindValue(1, $id_cliente, PDO::PARAM_INT);
            $stmt->execute();

            // Para generar los logs
            $this->registro->registrarActividad(
                'admin',
                'Clientes',
                'Activar',
                "Se activo el cliente con ID: $id_cliente",
                'info'
            );
            // return $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC); // Devuelve el resultado de un solo usuario (fetch)
            return $stmt->rowCount() > 0; // Retorna true si se eliminó al menos un registro, false si no existía el ID.
        } catch (PDOException $e) {
            // Esto para desarrollo
            //die("Error al activar el cliente {$id_cliente}:" . $e->getMessage());

            // Esto para producción
            $this->registro->registrarActividad(
                'admin',
                'Clientes',
                'activar_clientexid',
                "Error al activar el cliente {$id_cliente}: " . $e->getMessage(),
                "error"
            );

            return false;

            //En producción, se recomienda registrar el error en un archivo de logs y devolver false
            /*error_log("Error al obtener cliente: " . $e->getMessage()); // Registrar error
            return false; // No detener el script, manejar el error en la llamada*/
            // El error se almacena en los logs de PHP o Apache (/var/log/apache2/error.log).
        }
    }


    public function insert_cliente($codigo_cliente, $nombre_cliente, $direccion_cliente, $cp_cliente, $poblacion_cliente, $provincia_cliente, $nif_cliente, $telefono_cliente, $fax_cliente, $web_cliente, $email_cliente, $nombre_facturacion_cliente, $direccion_facturacion_cliente, $cp_facturacion_cliente, $poblacion_facturacion_cliente, $provincia_facturacion_cliente, $id_forma_pago_habitual, $observaciones_cliente)
    {
        try {
            // Establecer zona horaria Madrid antes de la consulta
            $this->conexion->exec("SET time_zone = 'Europe/Madrid'");
            
            $sql = "INSERT INTO cliente (codigo_cliente, nombre_cliente, direccion_cliente, cp_cliente, poblacion_cliente, provincia_cliente, 
            nif_cliente, telefono_cliente, fax_cliente, web_cliente, email_cliente, nombre_facturacion_cliente, direccion_facturacion_cliente, 
            cp_facturacion_cliente, poblacion_facturacion_cliente, provincia_facturacion_cliente, id_forma_pago_habitual,
            observaciones_cliente, activo_cliente, created_at_cliente, updated_at_cliente) 
                                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, NOW(), NOW())";
            
            $stmt = $this->conexion->prepare($sql);
            
            if (!$stmt) {
                throw new Exception("Error al preparar la consulta SQL");
            }
                              
            $stmt->bindValue(1, $codigo_cliente, PDO::PARAM_STR); 
            $stmt->bindValue(2, $nombre_cliente, PDO::PARAM_STR); 
            $stmt->bindValue(3, $direccion_cliente, PDO::PARAM_STR); 
            $stmt->bindValue(4, $cp_cliente, PDO::PARAM_STR); 
            $stmt->bindValue(5, $poblacion_cliente, PDO::PARAM_STR); 
            $stmt->bindValue(6, $provincia_cliente, PDO::PARAM_STR); 
            $stmt->bindValue(7, $nif_cliente, PDO::PARAM_STR); 
            $stmt->bindValue(8, $telefono_cliente, PDO::PARAM_STR); 
            $stmt->bindValue(9, $fax_cliente, PDO::PARAM_STR); 
            $stmt->bindValue(10, $web_cliente, PDO::PARAM_STR); 
            $stmt->bindValue(11, $email_cliente, PDO::PARAM_STR); 
            $stmt->bindValue(12, $nombre_facturacion_cliente, PDO::PARAM_STR); 
            $stmt->bindValue(13, $direccion_facturacion_cliente, PDO::PARAM_STR); 
            $stmt->bindValue(14, $cp_facturacion_cliente, PDO::PARAM_STR); 
            $stmt->bindValue(15, $poblacion_facturacion_cliente, PDO::PARAM_STR); 
            $stmt->bindValue(16, $provincia_facturacion_cliente, PDO::PARAM_STR); 
            
            // Manejar id_forma_pago_habitual que puede ser NULL
            if (!empty($id_forma_pago_habitual)) {
                $stmt->bindValue(17, $id_forma_pago_habitual, PDO::PARAM_INT);
            } else {
                $stmt->bindValue(17, null, PDO::PARAM_NULL);
            }
            
            $stmt->bindValue(18, $observaciones_cliente, PDO::PARAM_STR); 
                              
            $resultado = $stmt->execute();
            
            if (!$resultado) {
                $errorInfo = $stmt->errorInfo();
                throw new Exception("Error al ejecutar la consulta: " . $errorInfo[2]);
            }
                              
            $idInsert = $this->conexion->lastInsertId();

            // Para generar los logs
            $this->registro->registrarActividad(
                'admin',
                'Clientes',
                'Insertar',
                "Se insertó el cliente con ID: $idInsert",
                'info'
            );

            return $idInsert; // Devuelve el ID del registro insertado
            
        } catch (PDOException $e) {
            // Para desarrollo - devolver el error detallado
            throw new Exception("Error SQL en insert_cliente: " . $e->getMessage());
            
        } catch (Exception $e) {
            throw new Exception("Error general en insert_cliente: " . $e->getMessage());
        }
    }


    public function update_cliente($id_cliente, $codigo_cliente, $nombre_cliente, $direccion_cliente, $cp_cliente, $poblacion_cliente, $provincia_cliente, $nif_cliente, $telefono_cliente, $fax_cliente, $web_cliente, $email_cliente, $nombre_facturacion_cliente, $direccion_facturacion_cliente, $cp_facturacion_cliente, $poblacion_facturacion_cliente, $provincia_facturacion_cliente, $id_forma_pago_habitual, $observaciones_cliente){
        try {
            // Establecer zona horaria Madrid antes de la consulta
            $this->conexion->exec("SET time_zone = 'Europe/Madrid'");
            
            $sql = "UPDATE cliente SET codigo_cliente = ?, nombre_cliente = ?, direccion_cliente = ?, cp_cliente = ?, poblacion_cliente = ?, provincia_cliente = ?, nif_cliente = ?, telefono_cliente = ?, fax_cliente = ?, web_cliente = ?, email_cliente = ?, nombre_facturacion_cliente = ?, direccion_facturacion_cliente = ?, cp_facturacion_cliente = ?, poblacion_facturacion_cliente = ?, provincia_facturacion_cliente = ?, id_forma_pago_habitual = ?, observaciones_cliente = ?, updated_at_cliente = NOW() WHERE id_cliente = ?";
            
            $stmt = $this->conexion->prepare($sql);
            
            if (!$stmt) {
                throw new Exception("Error al preparar la consulta de actualización");
            }
            
            $stmt->bindValue(1, $codigo_cliente, PDO::PARAM_STR);
            $stmt->bindValue(2, $nombre_cliente, PDO::PARAM_STR);
            $stmt->bindValue(3, $direccion_cliente, PDO::PARAM_STR);
            $stmt->bindValue(4, $cp_cliente, PDO::PARAM_STR);
            $stmt->bindValue(5, $poblacion_cliente, PDO::PARAM_STR);
            $stmt->bindValue(6, $provincia_cliente, PDO::PARAM_STR);
            $stmt->bindValue(7, $nif_cliente, PDO::PARAM_STR);
            $stmt->bindValue(8, $telefono_cliente, PDO::PARAM_STR);
            $stmt->bindValue(9, $fax_cliente, PDO::PARAM_STR);
            $stmt->bindValue(10, $web_cliente, PDO::PARAM_STR);
            $stmt->bindValue(11, $email_cliente, PDO::PARAM_STR);
            $stmt->bindValue(12, $nombre_facturacion_cliente, PDO::PARAM_STR);
            $stmt->bindValue(13, $direccion_facturacion_cliente, PDO::PARAM_STR);
            $stmt->bindValue(14, $cp_facturacion_cliente, PDO::PARAM_STR);
            $stmt->bindValue(15, $poblacion_facturacion_cliente, PDO::PARAM_STR);
            $stmt->bindValue(16, $provincia_facturacion_cliente, PDO::PARAM_STR);
            
            // Manejar id_forma_pago_habitual que puede ser NULL
            if (!empty($id_forma_pago_habitual)) {
                $stmt->bindValue(17, $id_forma_pago_habitual, PDO::PARAM_INT);
            } else {
                $stmt->bindValue(17, null, PDO::PARAM_NULL);
            }
            
            $stmt->bindValue(18, $observaciones_cliente, PDO::PARAM_STR);
            $stmt->bindValue(19, $id_cliente, PDO::PARAM_INT); 

            $resultado = $stmt->execute();
            
            if (!$resultado) {
                $errorInfo = $stmt->errorInfo();
                throw new Exception("Error al ejecutar la actualización: " . $errorInfo[2]);
            }

            // Para generar los logs
            $this->registro->registrarActividad(
                'admin',
                'Clientes',
                'Actualizar',
                "Se actualizó el cliente con ID: $id_cliente",
                'info'
            );

            return $stmt->rowCount(); // Devuelve el número de filas afectadas

        } catch (PDOException $e) {
            throw new Exception("Error SQL en update_cliente: " . $e->getMessage());
        } catch (Exception $e) {
            throw new Exception("Error general en update_cliente: " . $e->getMessage());
        }
    }

    public function verificarCliente($codigo_cliente, $nombre_cliente = null, $id_cliente = null)
    {
        try {
            // Consulta SQL base - verificamos por código y nombre
            $sql = "SELECT COUNT(*) AS total FROM cliente WHERE (LOWER(codigo_cliente) = LOWER(?) OR LOWER(nombre_cliente) = LOWER(?))";
            $params = [trim($codigo_cliente), trim($nombre_cliente)];
    
            // Si es edición, excluimos el ID actual
            if (!empty($id_cliente)) {
                $sql .= " AND id_cliente != ?";
                $params[] = $id_cliente;
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
                    'Clientes',
                    'verificarCliente',
                    "Error al verificar existencia del cliente: " . $e->getMessage(),
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