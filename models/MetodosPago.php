<?php

require_once '../config/conexion.php'; // ✅ Se incluye correctamente el archivo de conexión
require_once '../config/funciones.php'; // ✅ Se incluye correctamente el archivo de conexión


//CREATE TABLE metodo_pago (
//    id_metodo_pago INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
//    codigo_metodo_pago VARCHAR(20) NOT NULL UNIQUE,
//    nombre_metodo_pago VARCHAR(100) NOT NULL,
//    observaciones_metodo_pago TEXT,
//    activo_metodo_pago BOOLEAN DEFAULT TRUE,
//    created_at_metodo_pago TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
//    updated_at_metodo_pago TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
//    INDEX idx_codigo_metodo_pago (codigo_metodo_pago)
//) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



class MetodosPago
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
                'MetodosPago',
                '__construct',
                "No se pudo establecer zona horaria Madrid: " . $e->getMessage(),
                'warning'
            );
        }
    }

    public function get_metodo_pago()
    {
        try {
            $sql = "SELECT * FROM metodo_pago";
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->execute();

            return $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC); // Devuelve todos los métodos de pago
        } catch (PDOException $e) {
            // Esto para desarrollo
            //die("Error al mostrar los métodos de pago: " . $e->getMessage());

            $this->registro->registrarActividad(
                'admin',
                'MetodosPago',
                'get_metodo_pago',
                "Error al listar los métodos de pago: " . $e->getMessage(),
                "error"
            );


            //En producción, se recomienda registrar el error en un archivo de logs y devolver false
            /*error_log("Error al obtener métodos de pago: " . $e->getMessage()); // Registrar error
            return false; // No detener el script, manejar el error en la llamada*/
            // El error se almacena en los logs de PHP o Apache (/var/log/apache2/error.log).
        }
    }

    public function get_metodo_pago_disponible()
    {
        try {
            $sql = "SELECT * FROM metodo_pago WHERE activo_metodo_pago = 1";  
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->execute();

            return $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC); // Devuelve todos los métodos de pago activos
        } catch (PDOException $e) {
            // Esto para desarrollo
            //die("Error al mostrar los métodos de pago: " . $e->getMessage());

            $this->registro->registrarActividad(
                'admin',
                'MetodosPago',
                'get_metodo_pago_disponible',
                "Error al listar los métodos de pago disponibles: " . $e->getMessage(),
                "error"
            );


            //En producción, se recomienda registrar el error en un archivo de logs y devolver false
            /*error_log("Error al obtener métodos de pago: " . $e->getMessage()); // Registrar error
            return false; // No detener el script, manejar el error en la llamada*/
            // El error se almacena en los logs de PHP o Apache (/var/log/apache2/error.log).
        }
    }

    public function get_metodo_pagoxid($id_metodo_pago)
    {
        try {
            $sql = "SELECT * FROM metodo_pago where id_metodo_pago=?";
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->bindValue(1, $id_metodo_pago, PDO::PARAM_INT);
            $stmt->execute();
            //return $resultado = $stmt->fetch(PDO::FETCH_ASSOC); // Solo devuelve un registro;
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC); // Solo devuelve un registro;
                        
            return $resultado; // Devuelve el resultado de un solo método de pago (fetch)
        } catch (PDOException $e) {
            // Esto para desarrollo
            //die("Error al mostrar el método de pago {$id_metodo_pago}:" . $e->getMessage());

            // Esto para producción
            $this->registro->registrarActividad(
                'admin',
                'MetodosPago',
                'get_metodo_pagoxid',
                "Error al mostrar el método de pago {$id_metodo_pago}: " . $e->getMessage(),
                "error"
            );

            return false;
            //En producción, se recomienda registrar el error en un archivo de logs y devolver false
            /*error_log("Error al obtener método de pago: " . $e->getMessage()); // Registrar error
            return false; // No detener el script, manejar el error en la llamada*/
            // El error se almacena en los logs de PHP o Apache (/var/log/apache2/error.log).
        }
    }

    public function delete_metodo_pagoxid($id_metodo_pago)
    {
        try {
            $sql = "UPDATE metodo_pago set activo_metodo_pago=0 where id_metodo_pago=?";
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->bindValue(1, $id_metodo_pago, PDO::PARAM_INT);
            $stmt->execute();


            // Para los logs
            $this->registro->registrarActividad(
                'admin',
                'MetodosPago',
                'Desactivar',
                "Se desactivó el método de pago con ID: $id_metodo_pago",
                'info'
            );
            return $stmt->rowCount() > 0; // Retorna true si se desactivó al menos un registro, false si no existía el ID.
        } catch (PDOException $e) {
            // Esto para desarrollo
            //die("Error al desactivar el método de pago {$id_metodo_pago}:" . $e->getMessage());

            // Esto para producción
            $this->registro->registrarActividad(
                'admin',
                'MetodosPago',
                'delete_metodo_pagoxid',
                "Error al desactivar el método de pago {$id_metodo_pago}: " . $e->getMessage(),
                'error'
            );


            return false;

            //En producción, se recomienda registrar el error en un archivo de logs y devolver false
            /*error_log("Error al desactivar método de pago: " . $e->getMessage()); // Registrar error
            return false; // No detener el script, manejar el error en la llamada*/
            // El error se almacena en los logs de PHP o Apache (/var/log/apache2/error.log).
        }
    }

    public function activar_metodo_pagoxid($id_metodo_pago)
    {
        try {
            $sql = "UPDATE metodo_pago set activo_metodo_pago=1 where id_metodo_pago=?";
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->bindValue(1, $id_metodo_pago, PDO::PARAM_INT);
            $stmt->execute();

            // Para generar los logs
            $this->registro->registrarActividad(
                'admin',
                'MetodosPago',
                'Activar',
                "Se activó el método de pago con ID: $id_metodo_pago",
                'info'
            );
            // return $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC); // Devuelve el resultado de un solo registro (fetch)
            return $stmt->rowCount() > 0; // Retorna true si se activó al menos un registro, false si no existía el ID.
        } catch (PDOException $e) {
            // Esto para desarrollo
            //die("Error al activar el método de pago {$id_metodo_pago}:" . $e->getMessage());

            // Esto para producción
            $this->registro->registrarActividad(
                'admin',
                'MetodosPago',
                'activar_metodo_pagoxid',
                "Error al activar el método de pago {$id_metodo_pago}: " . $e->getMessage(),
                "error"
            );

            return false;

            //En producción, se recomienda registrar el error en un archivo de logs y devolver false
            /*error_log("Error al activar método de pago: " . $e->getMessage()); // Registrar error
            return false; // No detener el script, manejar el error en la llamada*/
            // El error se almacena en los logs de PHP o Apache (/var/log/apache2/error.log).
        }
    }


    //CREATE TABLE metodo_pago (
//    id_metodo_pago INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
//    codigo_metodo_pago VARCHAR(20) NOT NULL UNIQUE,
//    nombre_metodo_pago VARCHAR(100) NOT NULL,
//    observaciones_metodo_pago TEXT,
//    activo_metodo_pago BOOLEAN DEFAULT TRUE,
//    created_at_metodo_pago TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
//    updated_at_metodo_pago TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
//    INDEX idx_codigo_metodo_pago (codigo_metodo_pago)
//) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


    public function insert_metodo_pago($codigo_metodo_pago, $nombre_metodo_pago, $observaciones_metodo_pago)
    {
        try {
            // Establecer zona horaria Madrid antes de la consulta
            $this->conexion->exec("SET time_zone = 'Europe/Madrid'");

            $sql = "INSERT INTO metodo_pago (codigo_metodo_pago, nombre_metodo_pago, observaciones_metodo_pago, activo_metodo_pago, created_at_metodo_pago, updated_at_metodo_pago) 
                                 VALUES (?, ?, ?, 1, NOW(), NOW())";
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->bindValue(1, $codigo_metodo_pago, PDO::PARAM_STR); // Se enlaza el valor del código
            $stmt->bindValue(2, $nombre_metodo_pago, PDO::PARAM_STR); // Se enlaza el valor del nombre
            $stmt->bindValue(3, $observaciones_metodo_pago, PDO::PARAM_STR); // Se enlaza el valor de las observaciones
            $stmt->execute();
            $idInsert = $this->conexion->lastInsertId(); // Se obtiene el ID del ultimo insertado

            // Para generar los logs
            $this->registro->registrarActividad(
                'admin',
                'MetodosPago',
                'Insertar',
                "Se insertó el método de pago con ID: $idInsert",
                'info'
            );

            //return true; // Devuelve true si la inserción fue exitosa
            return $idInsert; // Devuelve el ID del método de pago insertado
        } catch (PDOException $e) {
            // Esto para desarrollo
            //die("Error al insertar el método de pago: " . $e->getMessage());

            // Esto para producción
            $this->registro->registrarActividad(
                'admin',
                'MetodosPago',
                'insert_metodo_pago',
                "Error al insertar el método de pago: " . $e->getMessage(),
                'error'
            );

            return false;
        }
    }



    //CREATE TABLE metodo_pago (
//    id_metodo_pago INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
//    codigo_metodo_pago VARCHAR(20) NOT NULL UNIQUE,
//    nombre_metodo_pago VARCHAR(100) NOT NULL,
//    observaciones_metodo_pago TEXT,
//    activo_metodo_pago BOOLEAN DEFAULT TRUE,
//    created_at_metodo_pago TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
//    updated_at_metodo_pago TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
//    INDEX idx_codigo_metodo_pago (codigo_metodo_pago)
//) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    
    public function update_metodo_pago($id_metodo_pago, $codigo_metodo_pago, $nombre_metodo_pago, $observaciones_metodo_pago){
        try {
            // Establecer zona horaria Madrid antes de la consulta
            $this->conexion->exec("SET time_zone = 'Europe/Madrid'");
            
            $sql = "UPDATE metodo_pago SET codigo_metodo_pago = ?, nombre_metodo_pago = ?, observaciones_metodo_pago = ?, updated_at_metodo_pago = NOW() WHERE id_metodo_pago = ?";
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->bindValue(1, $codigo_metodo_pago, PDO::PARAM_STR);
            $stmt->bindValue(2, $nombre_metodo_pago, PDO::PARAM_STR);
            $stmt->bindValue(3, $observaciones_metodo_pago, PDO::PARAM_STR);
            $stmt->bindValue(4, $id_metodo_pago, PDO::PARAM_INT);
          
            $stmt->execute();

            // Para generar los logs
            $this->registro->registrarActividad(
                'admin',
                'MetodosPago',
                'Actualizar',
                "Se actualizó el método de pago con ID: $id_metodo_pago",
                'info'
            );

            return true; // Devuelve true si el update fue exitoso

        } catch (PDOException $e) {
            // Esto para desarrollo
            //die("Error al hacer update al método de pago: " . $e->getMessage());

            // Esto para producción
            $this->registro->registrarActividad(
                'admin',
                'MetodosPago',
                'update_metodo_pago',
                "Error al actualizar el método de pago:" . $e->getMessage(),
                'error'
            );
        }
    }

    public function verificarMetodoPago($codigo_metodo_pago, $nombre_metodo_pago = null, $id_metodo_pago = null)
    {
        try {
            // Consulta SQL base - verificamos por código o nombre
            $sql = "SELECT COUNT(*) AS total FROM metodo_pago WHERE (LOWER(codigo_metodo_pago) = LOWER(?) OR LOWER(nombre_metodo_pago) = LOWER(?))";
            $params = [$codigo_metodo_pago, $nombre_metodo_pago];
    
            // Si es edición, excluimos el ID actual
            if (!empty($id_metodo_pago)) {
                $sql .= " AND id_metodo_pago != ?";
                $params[] = $id_metodo_pago;
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
                    'MetodosPago',
                    'verificarMetodoPago',
                    "Error al verificar existencia del método de pago: " . $e->getMessage(),
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
