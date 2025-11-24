<?php

require_once '../config/conexion.php'; // ✅ Se incluye correctamente el archivo de conexión
require_once '../config/funciones.php'; // ✅ Se incluye correctamente el archivo de conexión


//CREATE TABLE grupo_articulo (
//    id_grupo INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
//    codigo_grupo VARCHAR(20) NOT NULL UNIQUE,
//    nombre_grupo VARCHAR(100) NOT NULL,
//    descripcion_grupo VARCHAR(255),
//    observaciones_grupo TEXT,
//    activo_grupo BOOLEAN DEFAULT TRUE,
//    created_at_grupo TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
//    updated_at_grupo TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
//) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



class Grupo_articulo
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
                'Grupo_articulo',
                '__construct',
                "No se pudo establecer zona horaria Madrid: " . $e->getMessage(),
                'warning'
            );
        }
    }

    public function get_grupo_articulo()
    {
        try {
            $sql = "SELECT * FROM grupo_articulo";
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->execute();

            return $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC); // Devuelve todos los grupos de artículos
        } catch (PDOException $e) {
            // Esto para desarrollo
            //die("Error al mostrar los grupos de artículos: " . $e->getMessage());

            $this->registro->registrarActividad(
                'admin',
                'Grupo_articulo',
                'get_grupo_articulo',
                "Error al listar los grupos de artículos: " . $e->getMessage(),
                "error"
            );


            //En producción, se recomienda registrar el error en un archivo de logs y devolver false
            /*error_log("Error al obtener grupos de artículos: " . $e->getMessage()); // Registrar error
            return false; // No detener el script, manejar el error en la llamada*/
            // El error se almacena en los logs de PHP o Apache (/var/log/apache2/error.log).
        }
    }

    public function get_grupo_articulo_disponible()
    {
        try {
            $sql = "SELECT * FROM grupo_articulo WHERE activo_grupo = 1";  
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->execute();

            return $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC); // Devuelve todos los grupos de artículos activos
        } catch (PDOException $e) {
            // Esto para desarrollo
            //die("Error al mostrar los grupos de artículos disponibles: " . $e->getMessage());

            $this->registro->registrarActividad(
                'admin',
                'Grupo_articulo',
                'get_grupo_articulo_disponible',
                "Error al listar los grupos de artículos disponibles: " . $e->getMessage(),
                "error"
            );


            //En producción, se recomienda registrar el error en un archivo de logs y devolver false
            /*error_log("Error al obtener grupos de artículos disponibles: " . $e->getMessage()); // Registrar error
            return false; // No detener el script, manejar el error en la llamada*/
            // El error se almacena en los logs de PHP o Apache (/var/log/apache2/error.log).
        }
    }

    public function get_grupo_articuloxid($id_grupo)
    {
        try {
            $sql = "SELECT * FROM grupo_articulo where id_grupo=?";
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->bindValue(1, $id_grupo, PDO::PARAM_INT);
            $stmt->execute();
            //return $resultado = $stmt->fetch(PDO::FETCH_ASSOC); // Solo devuelve un registro;
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC); // Solo devuelve un registro;
                        
            return $resultado; // Devuelve el resultado de un solo grupo de artículo (fetch)
        } catch (PDOException $e) {
            // Esto para desarrollo
            //die("Error al mostrar el grupo de artículo {$id_grupo}:" . $e->getMessage());

            // Esto para producción
            $this->registro->registrarActividad(
                'admin',
                'Grupo_articulo',
                'get_grupo_articuloxid',
                "Error al mostrar el grupo de artículo {$id_grupo}: " . $e->getMessage(),
                "error"
            );

            return false;
            //En producción, se recomienda registrar el error en un archivo de logs y devolver false
            /*error_log("Error al obtener grupo de artículo: " . $e->getMessage()); // Registrar error
            return false; // No detener el script, manejar el error en la llamada*/
            // El error se almacena en los logs de PHP o Apache (/var/log/apache2/error.log).
        }
    }

    public function delete_grupo_articuloxid($id_grupo)
    {
        try {
            $sql = "UPDATE grupo_articulo set activo_grupo=0 where id_grupo=?";
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->bindValue(1, $id_grupo, PDO::PARAM_INT);
            $stmt->execute();


            // Para los logs
            $this->registro->registrarActividad(
                'admin',
                'Grupo_articulo',
                'Desactivar',
                "Se desactivó el grupo de artículo con ID: $id_grupo",
                'info'
            );
            return $stmt->rowCount() > 0; // Retorna true si se eliminó al menos un registro, false si no existía el ID.
        } catch (PDOException $e) {
            // Esto para desarrollo
            //die("Error al desactivar el grupo de artículo {$id_grupo}:" . $e->getMessage());

            // Esto para producción
            $this->registro->registrarActividad(
                'admin',
                'Grupo_articulo',
                'delete_grupo_articuloxid',
                "Error al desactivar el grupo de artículo {$id_grupo}: " . $e->getMessage(),
                'error'
            );


            return false;

            //En producción, se recomienda registrar el error en un archivo de logs y devolver false
            /*error_log("Error al desactivar grupo de artículo: " . $e->getMessage()); // Registrar error
            return false; // No detener el script, manejar el error en la llamada*/
            // El error se almacena en los logs de PHP o Apache (/var/log/apache2/error.log).
        }
    }

    public function activar_grupo_articuloxid($id_grupo)
    {
        try {
            $sql = "UPDATE grupo_articulo set activo_grupo=1 where id_grupo=?";
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->bindValue(1, $id_grupo, PDO::PARAM_INT);
            $stmt->execute();

            // Para generar los logs
            $this->registro->registrarActividad(
                'admin',
                'Grupo_articulo',
                'Activar',
                "Se activó el grupo de artículo con ID: $id_grupo",
                'info'
            );
            // return $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC); // Devuelve el resultado de un solo usuario (fetch)
            return $stmt->rowCount() > 0; // Retorna true si se eliminó al menos un registro, false si no existía el ID.
        } catch (PDOException $e) {
            // Esto para desarrollo
            //die("Error al activar el grupo de artículo {$id_grupo}:" . $e->getMessage());

            // Esto para producción
            $this->registro->registrarActividad(
                'admin',
                'Grupo_articulo',
                'activar_grupo_articuloxid',
                "Error al activar el grupo de artículo {$id_grupo}: " . $e->getMessage(),
                "error"
            );

            return false;

            //En producción, se recomienda registrar el error en un archivo de logs y devolver false
            /*error_log("Error al activar grupo de artículo: " . $e->getMessage()); // Registrar error
            return false; // No detener el script, manejar el error en la llamada*/
            // El error se almacena en los logs de PHP o Apache (/var/log/apache2/error.log).
        }
    }


    //CREATE TABLE grupo_articulo (
//    id_grupo INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
//    codigo_grupo VARCHAR(20) NOT NULL UNIQUE,
//    nombre_grupo VARCHAR(100) NOT NULL,
//    descripcion_grupo VARCHAR(255),
//    observaciones_grupo TEXT,
//    activo_grupo BOOLEAN DEFAULT TRUE,
//    created_at_grupo TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
//    updated_at_grupo TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
//) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


    public function insert_grupo_articulo($codigo_grupo, $nombre_grupo, $descripcion_grupo, $observaciones_grupo)
    {
        try {
            // Establecer zona horaria Madrid antes de la consulta
            $this->conexion->exec("SET time_zone = 'Europe/Madrid'");

            $sql = "INSERT INTO grupo_articulo (codigo_grupo, nombre_grupo, descripcion_grupo, observaciones_grupo, activo_grupo, created_at_grupo, updated_at_grupo) 
                                 VALUES (?, ?, ?, ?, 1, NOW(), NOW())";
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->bindValue(1, $codigo_grupo, PDO::PARAM_STR); // Se enlaza el valor del codigo
            $stmt->bindValue(2, $nombre_grupo, PDO::PARAM_STR); // Se enlaza el valor del nombre
            $stmt->bindValue(3, $descripcion_grupo, PDO::PARAM_STR); // Se enlaza el valor de la descripción
            $stmt->bindValue(4, $observaciones_grupo, PDO::PARAM_STR); // Se enlaza el valor de las observaciones
            $stmt->execute();
            $idInsert = $this->conexion->lastInsertId(); // Se obtiene el ID del ultimo insertado

            // Para generar los logs
            $this->registro->registrarActividad(
                'admin',
                'Grupo_articulo',
                'Insertar',
                "Se insertó el grupo de artículo con ID: $idInsert",
                'info'
            );

            //return true; // Devuelve true si la inserción fue exitosa
            return $idInsert; // Devuelve el ID del grupo de artículo insertado
        } catch (PDOException $e) {
            // Esto para desarrollo
            //die("Error al insertar el grupo de artículo: " . $e->getMessage());

            // Esto para producción
            $this->registro->registrarActividad(
                'admin',
                'Grupo_articulo',
                'insert_grupo_articulo',
                "Error al insertar el grupo de artículo: " . $e->getMessage(),
                'error'
            );

            return false;
        }
    }



    //CREATE TABLE grupo_articulo (
//    id_grupo INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
//    codigo_grupo VARCHAR(20) NOT NULL UNIQUE,
//    nombre_grupo VARCHAR(100) NOT NULL,
//    descripcion_grupo VARCHAR(255),
//    observaciones_grupo TEXT,
//    activo_grupo BOOLEAN DEFAULT TRUE,
//    created_at_grupo TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
//    updated_at_grupo TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
//) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    
    public function update_grupo_articulo($id_grupo, $codigo_grupo, $nombre_grupo, $descripcion_grupo, $observaciones_grupo){
        try {
            // Establecer zona horaria Madrid antes de la consulta
            $this->conexion->exec("SET time_zone = 'Europe/Madrid'");
            
            $sql = "UPDATE grupo_articulo SET codigo_grupo = ?, nombre_grupo = ?, descripcion_grupo = ?, observaciones_grupo = ?, updated_at_grupo = NOW() WHERE id_grupo = ?";
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->bindValue(1, $codigo_grupo, PDO::PARAM_STR);
            $stmt->bindValue(2, $nombre_grupo, PDO::PARAM_STR);
            $stmt->bindValue(3, $descripcion_grupo, PDO::PARAM_STR);
            $stmt->bindValue(4, $observaciones_grupo, PDO::PARAM_STR);
            $stmt->bindValue(5, $id_grupo, PDO::PARAM_INT);
          
            $stmt->execute();

            // Para generar los logs
            $this->registro->registrarActividad(
                'admin',
                'Grupo_articulo',
                'Actualizar',
                "Se actualizó el grupo de artículo con ID: $id_grupo",
                'info'
            );

            return true; // Devuelve true si el update fue exitoso

        } catch (PDOException $e) {
            // Esto para desarrollo
            //die("Error al hacer update al grupo de artículo: " . $e->getMessage());

            // Esto para producción
            $this->registro->registrarActividad(
                'admin',
                'Grupo_articulo',
                'update_grupo_articulo',
                "Error al actualizar el grupo de artículo:" . $e->getMessage(),
                'error'
            );
        }
    }

    public function verificarGrupoArticulo($codigo_grupo, $nombre_grupo = null, $id_grupo = null)
    {
        try {
            // Consulta SQL base - verificamos por código o nombre del grupo
            $sql = "SELECT COUNT(*) AS total FROM grupo_articulo WHERE (LOWER(codigo_grupo) = LOWER(?) OR LOWER(nombre_grupo) = LOWER(?))";
            $params = [$codigo_grupo, $nombre_grupo];
    
            // Si es edición, excluimos el ID actual
            if (!empty($id_grupo)) {
                $sql .= " AND id_grupo != ?";
                $params[] = $id_grupo;
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
                    'Grupo_articulo',
                    'verificarGrupoArticulo',
                    "Error al verificar existencia del grupo de artículo: " . $e->getMessage(),
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
