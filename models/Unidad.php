<?php

require_once '../config/conexion.php'; // ✅ Se incluye correctamente el archivo de conexión
require_once '../config/funciones.php'; // ✅ Se incluye correctamente el archivo de conexión


//CREATE TABLE unidad_medida (
//    id_unidad INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
//    nombre_unidad VARCHAR(50) NOT NULL,
//    name_unidad VARCHAR(50) NOT NULL COMMENT 'Nombre en inglés',
//    descr_unidad VARCHAR(255),
//    simbolo_unidad VARCHAR(10),
//    activo_unidad boolean default true, 
//    created_at_unidad TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
//    updated_at_unidad TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
//);



class Unidad
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
                'Unidad',
                '__construct',
                "No se pudo establecer zona horaria Madrid: " . $e->getMessage(),
                'warning'
            );
        }
    }

    public function get_unidad()
    {
        try {
            $sql = "SELECT * FROM unidad_medida";
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->execute();

            return $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC); // Devuelve todas las unidades
        } catch (PDOException $e) {
            // Esto para desarrollo
            //die("Error al mostrar las unidades: " . $e->getMessage());

            $this->registro->registrarActividad(
                'admin',
                'Unidad',
                'get_unidad',
                "Error al listar las unidades: " . $e->getMessage(),
                "error"
            );


            //En producción, se recomienda registrar el error en un archivo de logs y devolver false
            /*error_log("Error al obtener unidades: " . $e->getMessage()); // Registrar error
            return false; // No detener el script, manejar el error en la llamada*/
            // El error se almacena en los logs de PHP o Apache (/var/log/apache2/error.log).
        }
    }

    public function get_unidad_disponible()
    {
        try {
            $sql = "SELECT * FROM unidad_medida WHERE activo_unidad = 1";  
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->execute();

            return $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC); // Devuelve todas las unidades disponibles
        } catch (PDOException $e) {
            // Esto para desarrollo
            //die("Error al mostrar las unidades disponibles: " . $e->getMessage());

            $this->registro->registrarActividad(
                'admin',
                'Unidad',
                'get_unidad_disponible',
                "Error al listar las unidades disponibles: " . $e->getMessage(),
                "error"
            );


            //En producción, se recomienda registrar el error en un archivo de logs y devolver false
            /*error_log("Error al obtener unidades: " . $e->getMessage()); // Registrar error
            return false; // No detener el script, manejar el error en la llamada*/
            // El error se almacena en los logs de PHP o Apache (/var/log/apache2/error.log).
        }
    }

    public function get_unidadxid($id_unidad)
    {
        try {
            $sql = "SELECT * FROM unidad_medida where id_unidad=?";
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->bindValue(1, $id_unidad, PDO::PARAM_INT);
            $stmt->execute();
            //return $resultado = $stmt->fetch(PDO::FETCH_ASSOC); // Solo devuelve un registro;
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC); // Solo devuelve un registro;
                        
            return $resultado; // Devuelve el resultado de una sola unidad (fetch)
        } catch (PDOException $e) {
            // Esto para desarrollo
            //die("Error al mostrar la unidad {$id_unidad}:" . $e->getMessage());

            // Esto para producción
            $this->registro->registrarActividad(
                'admin',
                'Unidad',
                'get_unidadxid',
                "Error al mostrar la unidad {$id_unidad}: " . $e->getMessage(),
                "error"
            );

            return false;
            //En producción, se recomienda registrar el error en un archivo de logs y devolver false
            /*error_log("Error al obtener unidad: " . $e->getMessage()); // Registrar error
            return false; // No detener el script, manejar el error en la llamada*/
            // El error se almacena en los logs de PHP o Apache (/var/log/apache2/error.log).
        }
    }

    public function delete_unidadxid($id_unidad)
    {
        try {
            $sql = "UPDATE unidad_medida set activo_unidad=0 where id_unidad=?";
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->bindValue(1, $id_unidad, PDO::PARAM_INT);
            $stmt->execute();


            // Para los logs
            $this->registro->registrarActividad(
                'admin',
                'Unidad',
                'Desactivar',
                "Se desactivó la unidad con ID: $id_unidad",
                'info'
            );
            return $stmt->rowCount() > 0; // Retorna true si se desactivó al menos una unidad, false si no existía el ID.
        } catch (PDOException $e) {
            // Esto para desarrollo
            //die("Error al desactivar la unidad {$id_unidad}:" . $e->getMessage());

            // Esto para producción
            $this->registro->registrarActividad(
                'admin',
                'Unidad',
                'delete_unidadxid',
                "Error al desactivar la unidad {$id_unidad}: " . $e->getMessage(),
                'error'
            );


            return false;

            //En producción, se recomienda registrar el error en un archivo de logs y devolver false
            /*error_log("Error al desactivar unidad: " . $e->getMessage()); // Registrar error
            return false; // No detener el script, manejar el error en la llamada*/
            // El error se almacena en los logs de PHP o Apache (/var/log/apache2/error.log).
        }
    }

    public function activar_unidadxid($id_unidad)
    {
        try {
            $sql = "UPDATE unidad_medida set activo_unidad=1 where id_unidad=?";
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->bindValue(1, $id_unidad, PDO::PARAM_INT);
            $stmt->execute();

            // Para generar los logs
            $this->registro->registrarActividad(
                'admin',
                'Unidad',
                'Activar',
                "Se activó la unidad con ID: $id_unidad",
                'info'
            );
            // return $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC); // Devuelve el resultado de una sola unidad (fetch)
            return $stmt->rowCount() > 0; // Retorna true si se activó al menos una unidad, false si no existía el ID.
        } catch (PDOException $e) {
            // Esto para desarrollo
            //die("Error al activar la unidad {$id_unidad}:" . $e->getMessage());

            // Esto para producción
            $this->registro->registrarActividad(
                'admin',
                'Unidad',
                'activar_unidadxid',
                "Error al activar la unidad {$id_unidad}: " . $e->getMessage(),
                "error"
            );

            return false;

            //En producción, se recomienda registrar el error en un archivo de logs y devolver false
            /*error_log("Error al activar unidad: " . $e->getMessage()); // Registrar error
            return false; // No detener el script, manejar el error en la llamada*/
            // El error se almacena en los logs de PHP o Apache (/var/log/apache2/error.log).
        }
    }


    //CREATE TABLE unidad_medida (
    //    id_unidad INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    //    nombre_unidad VARCHAR(50) NOT NULL,
    //    name_unidad VARCHAR(50) NOT NULL COMMENT 'Nombre en inglés',
    //    descr_unidad VARCHAR(255),
    //    simbolo_unidad VARCHAR(10),
    //    activo_unidad boolean default true, 
    //    created_at_unidad TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    //    updated_at_unidad TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    //);


    public function insert_unidad($nombre_unidad, $name_unidad, $descr_unidad, $simbolo_unidad)
    {
        try {
            // Configurar zona horaria para esta operación
            $this->conexion->exec("SET time_zone = 'Europe/Madrid'");

            $sql = "INSERT INTO unidad_medida (nombre_unidad, name_unidad, descr_unidad, simbolo_unidad, activo_unidad, created_at_unidad, updated_at_unidad) 
                                 VALUES (?, ?, ?, ?, 1, NOW(), NOW())";
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->bindValue(1, $nombre_unidad, PDO::PARAM_STR); // Se enlaza el valor del nombre
            $stmt->bindValue(2, $name_unidad, PDO::PARAM_STR); // Se enlaza el valor del nombre en inglés
            $stmt->bindValue(3, $descr_unidad, PDO::PARAM_STR); // Se enlaza el valor de la descripción
            $stmt->bindValue(4, $simbolo_unidad, PDO::PARAM_STR); // Se enlaza el valor del símbolo
            $stmt->execute();
            $idInsert = $this->conexion->lastInsertId(); // Se obtiene el ID del ultimo insertado

            // Para generar los logs
            $this->registro->registrarActividad(
                'admin',
                'Unidad',
                'Insertar',
                "Se insertó la unidad con ID: $idInsert",
                'info'
            );

            //return true; // Devuelve true si la inserción fue exitosa
            return $idInsert; // Devuelve el ID de la unidad insertada
        } catch (PDOException $e) {
            // Esto para desarrollo
            //die("Error al insertar la unidad: " . $e->getMessage());

            // Esto para producción
            $this->registro->registrarActividad(
                'admin',
                'Unidad',
                'insert_unidad',
                "Error al insertar la unidad: " . $e->getMessage(),
                'error'
            );

            return false;
        }
    }



    //CREATE TABLE unidad_medida (
    //    id_unidad INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    //    nombre_unidad VARCHAR(50) NOT NULL,
    //    name_unidad VARCHAR(50) NOT NULL COMMENT 'Nombre en inglés',
    //    descr_unidad VARCHAR(255),
    //    simbolo_unidad VARCHAR(10),
    //    activo_unidad boolean default true, 
    //    created_at_unidad TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    //    updated_at_unidad TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    //);

    
    public function update_unidad($id_unidad, $nombre_unidad, $name_unidad, $descr_unidad, $simbolo_unidad){
        try {
            // Configurar zona horaria para esta operación
            $this->conexion->exec("SET time_zone = 'Europe/Madrid'");
            
            $sql = "UPDATE unidad_medida SET nombre_unidad = ?, name_unidad = ?, descr_unidad = ?, simbolo_unidad = ?, updated_at_unidad = NOW() WHERE id_unidad = ?";
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->bindValue(1, $nombre_unidad, PDO::PARAM_STR);
            $stmt->bindValue(2, $name_unidad, PDO::PARAM_STR);
            $stmt->bindValue(3, $descr_unidad, PDO::PARAM_STR);
            $stmt->bindValue(4, $simbolo_unidad, PDO::PARAM_STR);
            $stmt->bindValue(5, $id_unidad, PDO::PARAM_INT);
          
            $stmt->execute();

            // Para generar los logs
            $this->registro->registrarActividad(
                'admin',
                'Unidad',
                'Actualizar',
                "Se actualizó la unidad con ID: $id_unidad",
                'info'
            );

            return true; // Devuelve true si el update fue exitoso

        } catch (PDOException $e) {
            // Esto para desarrollo
            //die("Error al hacer update a la unidad: " . $e->getMessage());

            // Esto para producción
            $this->registro->registrarActividad(
                'admin',
                'Unidad',
                'update_unidad',
                "Error al actualizar la unidad:" . $e->getMessage(),
                'error'
            );
        }
    }

    public function verificarUnidad($nombre_unidad, $name_unidad = null, $simbolo_unidad = null, $id_unidad = null)
    {
        try {
            // Construir la consulta dinámicamente según los parámetros que no estén vacíos
            $condiciones = [];
            $params = [];
            
            // Verificar nombre en español
            if (!empty($nombre_unidad)) {
                $condiciones[] = "LOWER(nombre_unidad) = LOWER(?)";
                $params[] = trim($nombre_unidad);
            }
            
            // Verificar nombre en inglés
            if (!empty($name_unidad)) {
                $condiciones[] = "LOWER(name_unidad) = LOWER(?)";
                $params[] = trim($name_unidad);
            }
            
            // Verificar símbolo (solo si no está vacío)
            if (!empty($simbolo_unidad)) {
                $condiciones[] = "LOWER(simbolo_unidad) = LOWER(?)";
                $params[] = trim($simbolo_unidad);
            }
            
            // Si no hay condiciones, no hay duplicados
            if (empty($condiciones)) {
                return ['existe' => false];
            }
            
            // Construir la consulta SQL
            $sql = "SELECT COUNT(*) AS total FROM unidad_medida WHERE (" . implode(' OR ', $condiciones) . ")";
            
            // Si es edición, excluimos el ID actual
            if (!empty($id_unidad)) {
                $sql .= " AND id_unidad != ?";
                $params[] = $id_unidad;
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
                    'Unidad',
                    'verificarUnidad',
                    "Error al verificar existencia de la unidad: " . $e->getMessage(),
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