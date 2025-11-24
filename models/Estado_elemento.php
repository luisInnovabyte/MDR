<?php

require_once '../config/conexion.php'; // ✅ Se incluye correctamente el archivo de conexión
require_once '../config/funciones.php'; // ✅ Se incluye correctamente el archivo de conexión


//  id_estado_elemento INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
//     codigo_estado_elemento VARCHAR(20) NOT NULL UNIQUE,
//     descripcion_estado_elemento VARCHAR(50) NOT NULL,
//     color_estado_elemento VARCHAR(7) COMMENT 'Color hexadecimal para visualización',
//     permite_alquiler_estado_elemento BOOLEAN DEFAULT TRUE COMMENT 'Si TRUE, el elemento puede ser alquilado en este estado',
//     observaciones_estado_elemento TEXT,
//     activo_estado_elemento BOOLEAN DEFAULT TRUE,
//     created_at_estado_elemento TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
//     updated_at_estado_elemento TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP



class Estado_elemento
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
                'Estado_elemento',
                '__construct',
                "No se pudo establecer zona horaria Madrid: " . $e->getMessage(),
                'warning'
            );
        }
    }

    public function get_estado_elemento()
    {
        try {
            $sql = "SELECT * FROM estado_elemento ORDER BY descripcion_estado_elemento ASC";
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->execute();

            return $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC); // Devuelve todos los estados de elemento
        } catch (PDOException $e) {
            // Esto para desarrollo
            //die("Error al mostrar los estados de elemento: " . $e->getMessage());

            $this->registro->registrarActividad(
                'admin',
                'Estado_elemento',
                'get_estado_elemento',
                "Error al listar los estados de elemento: " . $e->getMessage(),
                "error"
            );


            //En producción, se recomienda registrar el error en un archivo de logs y devolver false
            /*error_log("Error al obtener estados de elemento: " . $e->getMessage()); // Registrar error
            return false; // No detener el script, manejar el error en la llamada*/
            // El error se almacena en los logs de PHP o Apache (/var/log/apache2/error.log).
        }
    }

    public function get_estado_elemento_disponible()
    {
        try {
            $sql = "SELECT * FROM estado_elemento WHERE activo_estado_elemento = 1 ORDER BY descripcion_estado_elemento ASC";  
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->execute();

            return $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC); // Devuelve todos los estados de elemento disponibles
        } catch (PDOException $e) {
            // Esto para desarrollo
            //die("Error al mostrar los estados de elemento: " . $e->getMessage());

            $this->registro->registrarActividad(
                'admin',
                'Estado_elemento',
                'get_estado_elemento_disponible',
                "Error al listar los estados de elemento disponibles: " . $e->getMessage(),
                "error"
            );


            //En producción, se recomienda registrar el error en un archivo de logs y devolver false
            /*error_log("Error al obtener estados de elemento: " . $e->getMessage()); // Registrar error
            return false; // No detener el script, manejar el error en la llamada*/
            // El error se almacena en los logs de PHP o Apache (/var/log/apache2/error.log).
        }
    }

    public function get_estado_elementoxid($id_estado_elemento)
    {
        try {
            $sql = "SELECT * FROM estado_elemento where id_estado_elemento=?";
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->bindValue(1, $id_estado_elemento, PDO::PARAM_INT);
            $stmt->execute();
            //return $resultado = $stmt->fetch(PDO::FETCH_ASSOC); // Solo devuelve un registro;
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC); // Solo devuelve un registro;
                        
            return $resultado; // Devuelve el resultado de un estado de elemento (fetch)
        } catch (PDOException $e) {
            // Esto para desarrollo
            //die("Error al mostrar el estado de elemento {$id_estado_elemento}:" . $e->getMessage());

            // Esto para producción
            $this->registro->registrarActividad(
                'admin',
                'Estado_elemento',
                'get_estado_elementoxid',
                "Error al mostrar el estado de elemento {$id_estado_elemento}: " . $e->getMessage(),
                "error"
            );

            return false;
            //En producción, se recomienda registrar el error en un archivo de logs y devolver false
            /*error_log("Error al obtener estado de elemento: " . $e->getMessage()); // Registrar error
            return false; // No detener el script, manejar el error en la llamada*/
            // El error se almacena en los logs de PHP o Apache (/var/log/apache2/error.log).
        }
    }

    public function delete_estado_elementoxid($id_estado_elemento)
    {
        try {
            $sql = "UPDATE estado_elemento set activo_estado_elemento=0 where id_estado_elemento=?";
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->bindValue(1, $id_estado_elemento, PDO::PARAM_INT);
            $stmt->execute();


            // Para los logs
            $this->registro->registrarActividad(
                'admin',
                'Estado_elemento',
                'Desactivar',
                "Se desactivó el estado de elemento con ID: $id_estado_elemento",
                'info'
            );
            return $stmt->rowCount() > 0; // Retorna true si se eliminó al menos un registro, false si no existía el ID.
        } catch (PDOException $e) {
            // Esto para desarrollo
            //die("Error al mostrar el estado de elemento {$id_estado_elemento}:" . $e->getMessage());

            // Esto para producción
            $this->registro->registrarActividad(
                'admin',
                'Estado_elemento',
                'delete_estado_elementoxid',
                "Error al desactivar el estado de elemento {$id_estado_elemento}: " . $e->getMessage(),
                'error'
            );


            return false;

            //En producción, se recomienda registrar el error en un archivo de logs y devolver false
            /*error_log("Error al obtener estado de elemento: " . $e->getMessage()); // Registrar error
            return false; // No detener el script, manejar el error en la llamada*/
            // El error se almacena en los logs de PHP o Apache (/var/log/apache2/error.log).
        }
    }

    public function activar_estado_elementoxid($id_estado_elemento)
    {
        try {
            $sql = "UPDATE estado_elemento set activo_estado_elemento=1 where id_estado_elemento=?";
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->bindValue(1, $id_estado_elemento, PDO::PARAM_INT);
            $stmt->execute();

            // Para generar los logs
            $this->registro->registrarActividad(
                'admin',
                'Estado_elemento',
                'Activar',
                "Se activo el estado de elemento con ID: $id_estado_elemento",
                'info'
            );
            // return $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC); // Devuelve el resultado de un solo usuario (fetch)
            return $stmt->rowCount() > 0; // Retorna true si se eliminó al menos un registro, false si no existía el ID.
        } catch (PDOException $e) {
            // Esto para desarrollo
            //die("Error al activar el estado de elemento {$id_estado_elemento}:" . $e->getMessage());

            // Esto para producción
            $this->registro->registrarActividad(
                'admin',
                'Estado_elemento',
                'activar_estado_elementoxid',
                "Error al activar el estado de elemento {$id_estado_elemento}: " . $e->getMessage(),
                "error"
            );

            return false;

            //En producción, se recomienda registrar el error en un archivo de logs y devolver false
            /*error_log("Error al obtener estado de elemento: " . $e->getMessage()); // Registrar error
            return false; // No detener el script, manejar el error en la llamada*/
            // El error se almacena en los logs de PHP o Apache (/var/log/apache2/error.log).
        }
    }




//  id_estado_elemento INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
//     codigo_estado_elemento VARCHAR(20) NOT NULL UNIQUE,
//     descripcion_estado_elemento VARCHAR(50) NOT NULL,
//     color_estado_elemento VARCHAR(7) COMMENT 'Color hexadecimal para visualización',
//     permite_alquiler_estado_elemento BOOLEAN DEFAULT TRUE COMMENT 'Si TRUE, el elemento puede ser alquilado en este estado',
//     observaciones_estado_elemento TEXT,
//     activo_estado_elemento BOOLEAN DEFAULT TRUE,
//     created_at_estado_elemento TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
//     updated_at_estado_elemento TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP


    public function insert_estado_elemento($codigo_estado_elemento, $descripcion_estado_elemento, $color_estado_elemento, $permite_alquiler_estado_elemento, $observaciones_estado_elemento)
    {
        try {
            // Establecer zona horaria Madrid antes de la consulta
            $this->conexion->exec("SET time_zone = 'Europe/Madrid'");
            
            $sql = "INSERT INTO estado_elemento (codigo_estado_elemento, descripcion_estado_elemento, color_estado_elemento, permite_alquiler_estado_elemento, observaciones_estado_elemento, activo_estado_elemento, created_at_estado_elemento, updated_at_estado_elemento) 
                                 VALUES (?, ?, ?, ?, ?, 1, NOW(), NOW())";
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
                              
            $stmt->bindValue(1, $codigo_estado_elemento, PDO::PARAM_STR); // Se enlaza el valor del codigo
            $stmt->bindValue(2, $descripcion_estado_elemento, PDO::PARAM_STR); // Se enlaza el valor de la descripcion
            $stmt->bindValue(3, $color_estado_elemento, PDO::PARAM_STR); // Se enlaza el valor del color
            $stmt->bindValue(4, $permite_alquiler_estado_elemento, PDO::PARAM_INT); // Se enlaza el valor del permite_alquiler
            $stmt->bindValue(5, $observaciones_estado_elemento, PDO::PARAM_STR); // Se enlaza el valor de las observaciones
                              
            $stmt->execute();
                              
            $idInsert = $this->conexion->lastInsertId(); // Se obtiene el ID del ultimo insertado

            // Para generar los logs
            $this->registro->registrarActividad(
                'admin',
                'Estado_elemento',
                'Insertar',
                "Se inserto el estado de elemento con ID: $idInsert",
                'info'
            );

            //return true; // Devuelve true si la inserción fue exitosa
            return $idInsert; // Devuelve el ID del registro insertado
        } catch (PDOException $e) {
            // Esto para desarrollo
            //die("Error al insertar el estado de elemento: " . $e->getMessage());

            // Esto para producción
            $this->registro->registrarActividad(
                'admin',
                'Estado_elemento',
                'insert_estado_elemento',
                "Error al insertar el estado de elemento: " . $e->getMessage(),
                'error'
            );

            return false;
        }
    }


//  id_estado_elemento INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
//     codigo_estado_elemento VARCHAR(20) NOT NULL UNIQUE,
//     descripcion_estado_elemento VARCHAR(50) NOT NULL,
//     color_estado_elemento VARCHAR(7) COMMENT 'Color hexadecimal para visualización',
//     permite_alquiler_estado_elemento BOOLEAN DEFAULT TRUE COMMENT 'Si TRUE, el elemento puede ser alquilado en este estado',
//     observaciones_estado_elemento TEXT,
//     activo_estado_elemento BOOLEAN DEFAULT TRUE,
//     created_at_estado_elemento TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
//     updated_at_estado_elemento TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP


    public function update_estado_elemento($id_estado_elemento, $codigo_estado_elemento, $descripcion_estado_elemento, $color_estado_elemento, $permite_alquiler_estado_elemento, $observaciones_estado_elemento){
        try {
            // Establecer zona horaria Madrid antes de la consulta
            $this->conexion->exec("SET time_zone = 'Europe/Madrid'");
            
            $sql = "UPDATE estado_elemento SET codigo_estado_elemento = ?, descripcion_estado_elemento = ?, color_estado_elemento = ?, permite_alquiler_estado_elemento = ?, observaciones_estado_elemento = ?, 
            updated_at_estado_elemento = NOW() WHERE id_estado_elemento = ?";
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->bindValue(1, $codigo_estado_elemento, PDO::PARAM_STR);
            $stmt->bindValue(2, $descripcion_estado_elemento, PDO::PARAM_STR);
            $stmt->bindValue(3, $color_estado_elemento, PDO::PARAM_STR);
            $stmt->bindValue(4, $permite_alquiler_estado_elemento, PDO::PARAM_INT);
            $stmt->bindValue(5, $observaciones_estado_elemento, PDO::PARAM_STR);
            $stmt->bindValue(6, $id_estado_elemento, PDO::PARAM_INT); // ID de estado de elemento

            $stmt->execute();

            // Para generar los logs
            $this->registro->registrarActividad(
                'admin',
                'Estado_elemento',
                'Actualizar',
                "Se actualizó el estado de elemento con ID: $id_estado_elemento",
                'info'
            );

            return true; // Devuelve true si el update fue exitoso

        } catch (PDOException $e) {
            // Esto para desarrollo
            //die("Error al hacer update al estado de elemento: " . $e->getMessage());

            // Esto para producción
            $this->registro->registrarActividad(
                'admin',
                'Estado_elemento',
                'update_estado_elemento',
                "Error al actualizar el estado de elemento:" . $e->getMessage(),
                'error'
            );
        }
    }

    public function verificarEstadoElemento($codigo_estado_elemento, $descripcion_estado_elemento = null, $id_estado_elemento = null)
    {
        try {
            // Consulta SQL base - verificamos por código y descripcion
            $sql = "SELECT COUNT(*) AS total FROM estado_elemento WHERE (LOWER(codigo_estado_elemento) = LOWER(?) OR LOWER(descripcion_estado_elemento) = LOWER(?))";
            $params = [trim($codigo_estado_elemento), trim($descripcion_estado_elemento)];
    
            // Si es edición, excluimos el ID actual
            if (!empty($id_estado_elemento)) {
                $sql .= " AND id_estado_elemento != ?";
                $params[] = $id_estado_elemento;
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
                    'Estado_elemento',
                    'verificarEstadoElemento',
                    "Error al verificar existencia del estado de elemento: " . $e->getMessage(),
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
