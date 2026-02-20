<?php

require_once '../config/conexion.php'; // ✅ Se incluye correctamente el archivo de conexión
require_once '../config/funciones.php'; // ✅ Se incluye correctamente el archivo de conexión


//  id_estado_ppto INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
//     codigo_estado_ppto VARCHAR(20) NOT NULL UNIQUE,
//     nombre_estado_ppto VARCHAR(100) NOT NULL,
//     color_estado_ppto VARCHAR(7),
//     orden_estado_ppto INT DEFAULT 0,
//     observaciones_estado_ppto TEXT,
//     activo_estado_ppto BOOLEAN DEFAULT TRUE,
//     created_at_estado_ppto TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
//     updated_at_estado_ppto TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
// ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



class Estado_presupuesto
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
                'Estado_presupuesto',
                '__construct',
                "No se pudo establecer zona horaria Madrid: " . $e->getMessage(),
                'warning'
            );
        }
    }

    public function get_estado_presupuesto()
    {
        try {
            $sql = "SELECT * FROM estado_presupuesto ORDER BY orden_estado_ppto ASC, nombre_estado_ppto ASC";
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->execute();

            return $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC); // Devuelve todos los estados de presupuesto
        } catch (PDOException $e) {
            // Esto para desarrollo
            //die("Error al mostrar los estados de presupuesto: " . $e->getMessage());

            $this->registro->registrarActividad(
                'admin',
                'Estado_presupuesto',
                'get_estado_presupuesto',
                "Error al listar los estados de presupuesto: " . $e->getMessage(),
                "error"
            );


            //En producción, se recomienda registrar el error en un archivo de logs y devolver false
            /*error_log("Error al obtener estados de presupuesto: " . $e->getMessage()); // Registrar error
            return false; // No detener el script, manejar el error en la llamada*/
            // El error se almacena en los logs de PHP o Apache (/var/log/apache2/error.log).
        }
    }

    public function get_estado_presupuesto_disponible()
    {
        try {
            $sql = "SELECT * FROM estado_presupuesto WHERE activo_estado_ppto = 1 ORDER BY orden_estado_ppto ASC, nombre_estado_ppto ASC";  
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->execute();

            return $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC); // Devuelve todos los estados de presupuesto disponibles
        } catch (PDOException $e) {
            // Esto para desarrollo
            //die("Error al mostrar los estados de presupuesto: " . $e->getMessage());

            $this->registro->registrarActividad(
                'admin',
                'Estado_presupuesto',
                'get_estado_presupuesto_disponible',
                "Error al listar los estados de presupuesto disponibles: " . $e->getMessage(),
                "error"
            );


            //En producción, se recomienda registrar el error en un archivo de logs y devolver false
            /*error_log("Error al obtener estados de presupuesto: " . $e->getMessage()); // Registrar error
            return false; // No detener el script, manejar el error en la llamada*/
            // El error se almacena en los logs de PHP o Apache (/var/log/apache2/error.log).
        }
    }

    public function get_estado_presupuestoxid($id_estado_ppto)
    {
        try {
            $sql = "SELECT * FROM estado_presupuesto where id_estado_ppto=?";
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->bindValue(1, $id_estado_ppto, PDO::PARAM_INT);
            $stmt->execute();
            //return $resultado = $stmt->fetch(PDO::FETCH_ASSOC); // Solo devuelve un registro;
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC); // Solo devuelve un registro;
                        
            return $resultado; // Devuelve el resultado de un estado de presupuesto (fetch)
        } catch (PDOException $e) {
            // Esto para desarrollo
            //die("Error al mostrar el estado de presupuesto {$id_estado_ppto}:" . $e->getMessage());

            // Esto para producción
            $this->registro->registrarActividad(
                'admin',
                'Estado_presupuesto',
                'get_estado_presupuestoxid',
                "Error al mostrar el estado de presupuesto {$id_estado_ppto}: " . $e->getMessage(),
                "error"
            );

            return false;
            //En producción, se recomienda registrar el error en un archivo de logs y devolver false
            /*error_log("Error al obtener estado de presupuesto: " . $e->getMessage()); // Registrar error
            return false; // No detener el script, manejar el error en la llamada*/
            // El error se almacena en los logs de PHP o Apache (/var/log/apache2/error.log).
        }
    }

    /**
     * Comprueba si un estado está marcado como gestionado por el sistema.
     * @return bool  true = sistema (protegido), false = usuario
     */
    private function _esSistema($id_estado_ppto)
    {
        try {
            $sql = "SELECT es_sistema_estado_ppto FROM estado_presupuesto WHERE id_estado_ppto = ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_estado_ppto, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return ($row && $row['es_sistema_estado_ppto'] == 1);
        } catch (PDOException $e) {
            return false; // Si no existe la columna aún, no bloquear
        }
    }

    public function delete_estado_presupuestoxid($id_estado_ppto)
    {
        try {
            // Protección: no se puede desactivar un estado del sistema
            if ($this->_esSistema($id_estado_ppto)) {
                $this->registro->registrarActividad(
                    'admin',
                    'Estado_presupuesto',
                    'delete_estado_presupuestoxid',
                    "Intento de desactivar estado del sistema ID: $id_estado_ppto - BLOQUEADO",
                    'warning'
                );
                return 'sistema';
            }

            $sql = "UPDATE estado_presupuesto set activo_estado_ppto=0 where id_estado_ppto=?";
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->bindValue(1, $id_estado_ppto, PDO::PARAM_INT);
            $stmt->execute();


            // Para los logs
            $this->registro->registrarActividad(
                'admin',
                'Estado_presupuesto',
                'Desactivar',
                "Se desactivó el estado de presupuesto con ID: $id_estado_ppto",
                'info'
            );
            return $stmt->rowCount() > 0; // Retorna true si se eliminó al menos un registro, false si no existía el ID.
        } catch (PDOException $e) {
            // Esto para desarrollo
            //die("Error al mostrar el estado de presupuesto {$id_estado_ppto}:" . $e->getMessage());

            // Esto para producción
            $this->registro->registrarActividad(
                'admin',
                'Estado_presupuesto',
                'delete_estado_presupuestoxid',
                "Error al desactivar el estado de presupuesto {$id_estado_ppto}: " . $e->getMessage(),
                'error'
            );


            return false;

            //En producción, se recomienda registrar el error en un archivo de logs y devolver false
            /*error_log("Error al obtener estado de presupuesto: " . $e->getMessage()); // Registrar error
            return false; // No detener el script, manejar el error en la llamada*/
            // El error se almacena en los logs de PHP o Apache (/var/log/apache2/error.log).
        }
    }

    public function activar_estado_presupuestoxid($id_estado_ppto)
    {
        try {
            // Protección: no se puede reactivar manualmente un estado del sistema
            if ($this->_esSistema($id_estado_ppto)) {
                $this->registro->registrarActividad(
                    'admin',
                    'Estado_presupuesto',
                    'activar_estado_presupuestoxid',
                    "Intento de activar estado del sistema ID: $id_estado_ppto - BLOQUEADO",
                    'warning'
                );
                return 'sistema';
            }

            $sql = "UPDATE estado_presupuesto set activo_estado_ppto=1 where id_estado_ppto=?";
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->bindValue(1, $id_estado_ppto, PDO::PARAM_INT);
            $stmt->execute();

            // Para generar los logs
            $this->registro->registrarActividad(
                'admin',
                'Estado_presupuesto',
                'Activar',
                "Se activo el estado de presupuesto con ID: $id_estado_ppto",
                'info'
            );
            // return $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC); // Devuelve el resultado de un solo usuario (fetch)
            return $stmt->rowCount() > 0; // Retorna true si se eliminó al menos un registro, false si no existía el ID.
        } catch (PDOException $e) {
            // Esto para desarrollo
            //die("Error al activar el estado de presupuesto {$id_estado_ppto}:" . $e->getMessage());

            // Esto para producción
            $this->registro->registrarActividad(
                'admin',
                'Estado_presupuesto',
                'activar_estado_presupuestoxid',
                "Error al activar el estado de presupuesto {$id_estado_ppto}: " . $e->getMessage(),
                "error"
            );

            return false;

            //En producción, se recomienda registrar el error en un archivo de logs y devolver false
            /*error_log("Error al obtener estado de presupuesto: " . $e->getMessage()); // Registrar error
            return false; // No detener el script, manejar el error en la llamada*/
            // El error se almacena en los logs de PHP o Apache (/var/log/apache2/error.log).
        }
    }




//  id_estado_ppto INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
//     codigo_estado_ppto VARCHAR(20) NOT NULL UNIQUE,
//     nombre_estado_ppto VARCHAR(100) NOT NULL,
//     color_estado_ppto VARCHAR(7),
//     orden_estado_ppto INT DEFAULT 0,
//     observaciones_estado_ppto TEXT,
//     activo_estado_ppto BOOLEAN DEFAULT TRUE,
//     created_at_estado_ppto TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
//     updated_at_estado_ppto TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP


    public function insert_estado_presupuesto($codigo_estado_ppto, $nombre_estado_ppto, $color_estado_ppto, $orden_estado_ppto, $observaciones_estado_ppto)
    {
        try {
            // Establecer zona horaria Madrid antes de la consulta
            $this->conexion->exec("SET time_zone = 'Europe/Madrid'");
            
            $sql = "INSERT INTO estado_presupuesto (codigo_estado_ppto, nombre_estado_ppto, color_estado_ppto, orden_estado_ppto, observaciones_estado_ppto, activo_estado_ppto, created_at_estado_ppto, updated_at_estado_ppto) 
                                 VALUES (?, ?, ?, ?, ?, 1, NOW(), NOW())";
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
                              
            $stmt->bindValue(1, $codigo_estado_ppto, PDO::PARAM_STR); // Se enlaza el valor del codigo
            $stmt->bindValue(2, $nombre_estado_ppto, PDO::PARAM_STR); // Se enlaza el valor del nombre
            $stmt->bindValue(3, $color_estado_ppto, PDO::PARAM_STR); // Se enlaza el valor del color
            $stmt->bindValue(4, $orden_estado_ppto, PDO::PARAM_INT); // Se enlaza el valor del orden
            $stmt->bindValue(5, $observaciones_estado_ppto, PDO::PARAM_STR); // Se enlaza el valor de las observaciones
                              
            $stmt->execute();
                              
            $idInsert = $this->conexion->lastInsertId(); // Se obtiene el ID del ultimo insertado

            // Para generar los logs
            $this->registro->registrarActividad(
                'admin',
                'Estado_presupuesto',
                'Insertar',
                "Se inserto el estado de presupuesto con ID: $idInsert",
                'info'
            );

            //return true; // Devuelve true si la inserción fue exitosa
            return $idInsert; // Devuelve el ID del registro insertado
        } catch (PDOException $e) {
            // Esto para desarrollo
            //die("Error al insertar el estado de presupuesto: " . $e->getMessage());

            // Esto para producción
            $this->registro->registrarActividad(
                'admin',
                'Estado_presupuesto',
                'insert_estado_presupuesto',
                "Error al insertar el estado de presupuesto: " . $e->getMessage(),
                'error'
            );

            return false;
        }
    }


//  id_estado_ppto INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
//     codigo_estado_ppto VARCHAR(20) NOT NULL UNIQUE,
//     nombre_estado_ppto VARCHAR(100) NOT NULL,
//     color_estado_ppto VARCHAR(7),
//     orden_estado_ppto INT DEFAULT 0,
//     observaciones_estado_ppto TEXT,
//     activo_estado_ppto BOOLEAN DEFAULT TRUE,
//     created_at_estado_ppto TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
//     updated_at_estado_ppto TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP


    public function update_estado_presupuesto($id_estado_ppto, $codigo_estado_ppto, $nombre_estado_ppto, $color_estado_ppto, $orden_estado_ppto, $observaciones_estado_ppto){
        try {
            // Protección: no se puede editar un estado del sistema
            if ($this->_esSistema($id_estado_ppto)) {
                $this->registro->registrarActividad(
                    'admin',
                    'Estado_presupuesto',
                    'update_estado_presupuesto',
                    "Intento de editar estado del sistema ID: $id_estado_ppto - BLOQUEADO",
                    'warning'
                );
                return 'sistema';
            }

            // Establecer zona horaria Madrid antes de la consulta
            $this->conexion->exec("SET time_zone = 'Europe/Madrid'");
            
            $sql = "UPDATE estado_presupuesto SET codigo_estado_ppto = ?, nombre_estado_ppto = ?, color_estado_ppto = ?, orden_estado_ppto = ?, observaciones_estado_ppto = ?, 
            updated_at_estado_ppto = NOW() WHERE id_estado_ppto = ?";
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->bindValue(1, $codigo_estado_ppto, PDO::PARAM_STR);
            $stmt->bindValue(2, $nombre_estado_ppto, PDO::PARAM_STR);
            $stmt->bindValue(3, $color_estado_ppto, PDO::PARAM_STR);
            $stmt->bindValue(4, $orden_estado_ppto, PDO::PARAM_INT);
            $stmt->bindValue(5, $observaciones_estado_ppto, PDO::PARAM_STR);
            $stmt->bindValue(6, $id_estado_ppto, PDO::PARAM_INT); // ID de estado de presupuesto

            $stmt->execute();

            // Para generar los logs
            $this->registro->registrarActividad(
                'admin',
                'Estado_presupuesto',
                'Actualizar',
                "Se actualizó el estado de presupuesto con ID: $id_estado_ppto",
                'info'
            );

            return true; // Devuelve true si el update fue exitoso

        } catch (PDOException $e) {
            // Esto para desarrollo
            //die("Error al hacer update al estado de presupuesto: " . $e->getMessage());

            // Esto para producción
            $this->registro->registrarActividad(
                'admin',
                'Estado_presupuesto',
                'update_estado_presupuesto',
                "Error al actualizar el estado de presupuesto:" . $e->getMessage(),
                'error'
            );
        }
    }

    public function verificarEstadoPresupuesto($codigo_estado_ppto, $nombre_estado_ppto = null, $id_estado_ppto = null)
    {
        try {
            // Consulta SQL base - verificamos por código y nombre
            $sql = "SELECT COUNT(*) AS total FROM estado_presupuesto WHERE (LOWER(codigo_estado_ppto) = LOWER(?) OR LOWER(nombre_estado_ppto) = LOWER(?))";
            $params = [trim($codigo_estado_ppto), trim($nombre_estado_ppto)];
    
            // Si es edición, excluimos el ID actual
            if (!empty($id_estado_ppto)) {
                $sql .= " AND id_estado_ppto != ?";
                $params[] = $id_estado_ppto;
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
                    'Estado_presupuesto',
                    'verificarEstadoPresupuesto',
                    "Error al verificar existencia del estado de presupuesto: " . $e->getMessage(),
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