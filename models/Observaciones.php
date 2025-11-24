<?php

require_once '../config/conexion.php'; // ✅ Se incluye correctamente el archivo de conexión
require_once '../config/funciones.php'; // ✅ Se incluye correctamente el archivo de conexión


//CREATE TABLE observacion_general (
//    id_obs_general INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
//    codigo_obs_general VARCHAR(20) NOT NULL UNIQUE,
//    titulo_obs_general VARCHAR(100) NOT NULL,
//    title_obs_general VARCHAR(100) NOT NULL DEFAULT '' COMMENT 'Título en inglés',
//    texto_obs_general TEXT NOT NULL,
//    text_obs_general TEXT NOT NULL COMMENT 'Texto en inglés',
//    orden_obs_general INT DEFAULT 0,
//    tipo_obs_general ENUM('condiciones', 'tecnicas', 'legales', 'comerciales', 'otras') DEFAULT 'otras',
//    obligatoria_obs_general BOOLEAN DEFAULT TRUE COMMENT 'Si TRUE, siempre aparece en presupuestos',
//    activo_obs_general BOOLEAN DEFAULT TRUE,
//    created_at_obs_general TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
//    updated_at_obs_general TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
//    INDEX idx_orden_obs_general (orden_obs_general),
//    INDEX idx_obligatoria_obs_general (obligatoria_obs_general)
//) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



class Observaciones
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
                'Observaciones',
                '__construct',
                "No se pudo establecer zona horaria Madrid: " . $e->getMessage(),
                'warning'
            );
        }
    }

    public function get_observaciones()
    {
        try {
            $sql = "SELECT * FROM observacion_general ORDER BY orden_obs_general";
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->execute();

            return $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC); // Devuelve todas las observaciones
        } catch (PDOException $e) {
            // Esto para desarrollo
            //die("Error al mostrar las observaciones: " . $e->getMessage());

            $this->registro->registrarActividad(
                'admin',
                'Observaciones',
                'get_observaciones',
                "Error al listar las observaciones: " . $e->getMessage(),
                "error"
            );


            //En producción, se recomienda registrar el error en un archivo de logs y devolver false
            /*error_log("Error al obtener usuarios: " . $e->getMessage()); // Registrar error
            return false; // No detener el script, manejar el error en la llamada*/
            // El error se almacena en los logs de PHP o Apache (/var/log/apache2/error.log).
        }
    }

    public function get_observaciones_disponible()
    {
        try {
            $sql = "SELECT * FROM observacion_general WHERE activo_obs_general = 1 ORDER BY orden_obs_general";  
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->execute();

            return $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC); // Devuelve todas las observaciones activas
        } catch (PDOException $e) {
            // Esto para desarrollo
            //die("Error al mostrar las observaciones: " . $e->getMessage());

            $this->registro->registrarActividad(
                'admin',
                'Observaciones',
                'get_observaciones_disponible',
                "Error al listar las observaciones disponibles: " . $e->getMessage(),
                "error"
            );


            //En producción, se recomienda registrar el error en un archivo de logs y devolver false
            /*error_log("Error al obtener familias: " . $e->getMessage()); // Registrar error
            return false; // No detener el script, manejar el error en la llamada*/
            // El error se almacena en los logs de PHP o Apache (/var/log/apache2/error.log).
        }
    }

    public function get_observacionesxid($id_obs_general)
    {
        try {
            $sql = "SELECT * FROM observacion_general where id_obs_general=?";
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->bindValue(1, $id_obs_general, PDO::PARAM_INT);
            $stmt->execute();
            //return $resultado = $stmt->fetch(PDO::FETCH_ASSOC); // Solo devuelve un registro;
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC); // Solo devuelve un registro;
                        
            return $resultado; // Devuelve el resultado de una sola observación (fetch)
        } catch (PDOException $e) {
            // Esto para desarrollo
            //die("Error al mostrar el producto {$prod_id}:" . $e->getMessage());

            // Esto para producción
            $this->registro->registrarActividad(
                'admin',
                'Observaciones',
                'get_observacionesxid',
                "Error al mostrar la observación {$id_obs_general}: " . $e->getMessage(),
                "error"
            );

            return false;
            //En producción, se recomienda registrar el error en un archivo de logs y devolver false
            /*error_log("Error al obtener usuarios: " . $e->getMessage()); // Registrar error
            return false; // No detener el script, manejar el error en la llamada*/
            // El error se almacena en los logs de PHP o Apache (/var/log/apache2/error.log).
        }
    }

    public function delete_observacionesxid($id_obs_general)
    {
        try {
            $sql = "UPDATE observacion_general set activo_obs_general=0 where id_obs_general=?";
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->bindValue(1, $id_obs_general, PDO::PARAM_INT);
            $stmt->execute();


            // Para los logs
            $this->registro->registrarActividad(
                'admin',
                'Observaciones',
                'Desactivar',
                "Se desactivó la observación con ID: $id_obs_general",
                'info'
            );
            return $stmt->rowCount() > 0; // Retorna true si se eliminó al menos un usuario, false si no existía el ID.
        } catch (PDOException $e) {
            // Esto para desarrollo
            //die("Error al mostrar el producto {$prod_id}:" . $e->getMessage());

            // Esto para producción
            $this->registro->registrarActividad(
                'admin',
                'Observaciones',
                'delete_observacionesxid',
                "Error al desactivar la observación {$id_obs_general}: " . $e->getMessage(),
                'error'
            );


            return false;

            //En producción, se recomienda registrar el error en un archivo de logs y devolver false
            /*error_log("Error al obtener usuarios: " . $e->getMessage()); // Registrar error
            return false; // No detener el script, manejar el error en la llamada*/
            // El error se almacena en los logs de PHP o Apache (/var/log/apache2/error.log).
        }
    }

    public function activar_observacionesxid($id_obs_general)
    {
        try {
            $sql = "UPDATE observacion_general set activo_obs_general=1 where id_obs_general=?";
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->bindValue(1, $id_obs_general, PDO::PARAM_INT);
            $stmt->execute();

            // Para generar los logs
            $this->registro->registrarActividad(
                'admin',
                'Observaciones',
                'Activar',
                "Se activo la observación con ID: $id_obs_general",
                'info'
            );
            // return $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC); // Devuelve el resultado de un solo usuario (fetch)
            return $stmt->rowCount() > 0; // Retorna true si se eliminó al menos un usuario, false si no existía el ID.
        } catch (PDOException $e) {
            // Esto para desarrollo
            //die("Error al activar el producto {$prod_id}:" . $e->getMessage());

            // Esto para producción
            $this->registro->registrarActividad(
                'admin',
                'Observaciones',
                'activar_observacionesxid',
                "Error al activar la observación {$id_obs_general}: " . $e->getMessage(),
                "error"
            );

            return false;

            //En producción, se recomienda registrar el error en un archivo de logs y devolver false
            /*error_log("Error al obtener usuarios: " . $e->getMessage()); // Registrar error
            return false; // No detener el script, manejar el error en la llamada*/
            // El error se almacena en los logs de PHP o Apache (/var/log/apache2/error.log).
        }
    }


    //CREATE TABLE observacion_general (
    //    id_obs_general INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    //    codigo_obs_general VARCHAR(20) NOT NULL UNIQUE,
    //    titulo_obs_general VARCHAR(100) NOT NULL,
    //    title_obs_general VARCHAR(100) NOT NULL DEFAULT '' COMMENT 'Título en inglés',
    //    texto_obs_general TEXT NOT NULL,
    //    text_obs_general TEXT NOT NULL COMMENT 'Texto en inglés',
    //    orden_obs_general INT DEFAULT 0,
    //    tipo_obs_general ENUM('condiciones', 'tecnicas', 'legales', 'comerciales', 'otras') DEFAULT 'otras',
    //    obligatoria_obs_general BOOLEAN DEFAULT TRUE COMMENT 'Si TRUE, siempre aparece en presupuestos',
    //    activo_obs_general BOOLEAN DEFAULT TRUE,
    //    created_at_obs_general TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    //    updated_at_obs_general TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    //    INDEX idx_orden_obs_general (orden_obs_general),
    //    INDEX idx_obligatoria_obs_general (obligatoria_obs_general)
    //) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


    public function insert_observaciones($codigo_obs_general, $titulo_obs_general, $title_obs_general, $texto_obs_general, $text_obs_general, $orden_obs_general, $tipo_obs_general, $obligatoria_obs_general)
    {
        try {
            // Establecer zona horaria Madrid antes de la consulta
            $this->conexion->exec("SET time_zone = 'Europe/Madrid'");

            $sql = "INSERT INTO observacion_general (codigo_obs_general, titulo_obs_general, title_obs_general, texto_obs_general, text_obs_general, orden_obs_general, tipo_obs_general, obligatoria_obs_general, activo_obs_general, created_at_obs_general, updated_at_obs_general) 
                                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1, NOW(), NOW())";
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->bindValue(1, $codigo_obs_general, PDO::PARAM_STR); // Se enlaza el valor del codigo
            $stmt->bindValue(2, $titulo_obs_general, PDO::PARAM_STR); // Se enlaza el valor del titulo
            $stmt->bindValue(3, $title_obs_general, PDO::PARAM_STR); // Se enlaza el valor del título en inglés
            $stmt->bindValue(4, $texto_obs_general, PDO::PARAM_STR); // Se enlaza el valor del texto
            $stmt->bindValue(5, $text_obs_general, PDO::PARAM_STR); // Se enlaza el valor del texto en inglés
            $stmt->bindValue(6, $orden_obs_general, PDO::PARAM_INT); // Se enlaza el valor del orden
            $stmt->bindValue(7, $tipo_obs_general, PDO::PARAM_STR); // Se enlaza el valor del tipo
            $stmt->bindValue(8, $obligatoria_obs_general, PDO::PARAM_INT); // Se enlaza el valor de obligatoria
            $stmt->execute();
            $idInsert = $this->conexion->lastInsertId(); // Se obtiene el ID del ultimo insertado

            // Para generar los logs
            $this->registro->registrarActividad(
                'admin',
                'Observaciones',
                'Insertar',
                "Se inserto la observación con ID: $idInsert",
                'info'
            );

            //return true; // Devuelve true si la inserción fue exitosa
            return $idInsert; // Devuelve el ID del usuario insertado
        } catch (PDOException $e) {
            // Esto para desarrollo
            //die("Error al insertar el producto: " . $e->getMessage());

            // Esto para producción
            $this->registro->registrarActividad(
                'admin',
                'Observaciones',
                'insert_observaciones',
                "Error al insertar la observación: " . $e->getMessage(),
                'error'
            );

            return false;
        }
    }



    //CREATE TABLE observacion_general (
    //    id_obs_general INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    //    codigo_obs_general VARCHAR(20) NOT NULL UNIQUE,
    //    titulo_obs_general VARCHAR(100) NOT NULL,
    //    title_obs_general VARCHAR(100) NOT NULL DEFAULT '' COMMENT 'Título en inglés',
    //    texto_obs_general TEXT NOT NULL,
    //    text_obs_general TEXT NOT NULL COMMENT 'Texto en inglés',
    //    orden_obs_general INT DEFAULT 0,
    //    tipo_obs_general ENUM('condiciones', 'tecnicas', 'legales', 'comerciales', 'otras') DEFAULT 'otras',
    //    obligatoria_obs_general BOOLEAN DEFAULT TRUE COMMENT 'Si TRUE, siempre aparece en presupuestos',
    //    activo_obs_general BOOLEAN DEFAULT TRUE,
    //    created_at_obs_general TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    //    updated_at_obs_general TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    //    INDEX idx_orden_obs_general (orden_obs_general),
    //    INDEX idx_obligatoria_obs_general (obligatoria_obs_general)
    //) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    
    public function update_observaciones($id_obs_general, $codigo_obs_general, $titulo_obs_general, $title_obs_general, $texto_obs_general, $text_obs_general, $orden_obs_general, $tipo_obs_general, $obligatoria_obs_general){
        try {
            // Establecer zona horaria Madrid antes de la consulta
            $this->conexion->exec("SET time_zone = 'Europe/Madrid'");
            
            $sql = "UPDATE observacion_general SET codigo_obs_general = ?, titulo_obs_general = ?, title_obs_general = ?, texto_obs_general = ?, text_obs_general = ?, orden_obs_general = ?, tipo_obs_general = ?, obligatoria_obs_general = ?, updated_at_obs_general = NOW() WHERE id_obs_general = ?";
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->bindValue(1, $codigo_obs_general, PDO::PARAM_STR);
            $stmt->bindValue(2, $titulo_obs_general, PDO::PARAM_STR);
            $stmt->bindValue(3, $title_obs_general, PDO::PARAM_STR);
            $stmt->bindValue(4, $texto_obs_general, PDO::PARAM_STR);
            $stmt->bindValue(5, $text_obs_general, PDO::PARAM_STR);
            $stmt->bindValue(6, $orden_obs_general, PDO::PARAM_INT);
            $stmt->bindValue(7, $tipo_obs_general, PDO::PARAM_STR);
            $stmt->bindValue(8, $obligatoria_obs_general, PDO::PARAM_INT);
            $stmt->bindValue(9, $id_obs_general, PDO::PARAM_INT);
          
            $stmt->execute();

            // Para generar los logs
            $this->registro->registrarActividad(
                'admin',
                'Observaciones',
                'Actualizar',
                "Se actualizó la observación con ID: $id_obs_general",
                'info'
            );

            return true; // Devuelve true si el update fue exitoso

        } catch (PDOException $e) {
            // Esto para desarrollo
            //die("Error al hacer update al producto: " . $e->getMessage());

            // Esto para producción
            $this->registro->registrarActividad(
                'admin',
                'Observaciones',
                'update_observaciones',
                "Error al actualizar la observación:" . $e->getMessage(),
                'error'
            );
        }
    }

    public function verificarObservaciones($codigo_obs_general, $id_obs_general = null)
    {
        try {
            // Consulta SQL base - verificamos por codigo
            $sql = "SELECT COUNT(*) AS total FROM observacion_general WHERE LOWER(codigo_obs_general) = LOWER(?)";
            $params = [$codigo_obs_general];
    
            // Si es edición, excluimos el ID actual
            if (!empty($id_obs_general)) {
                $sql .= " AND id_obs_general != ?";
                $params[] = $id_obs_general;
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
                    'Observaciones',
                    'verificarObservaciones',
                    "Error al verificar existencia de la observación: " . $e->getMessage(),
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
