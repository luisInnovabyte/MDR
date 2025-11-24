<?php

require_once '../config/conexion.php'; // ✅ Se incluye correctamente el archivo de conexión
require_once "../config/funciones.php";

class Vacaciones
{
    private $conexion;
    private $registro; // ✅ Se declara la instancia para el objeto RegistroActividad 

    public function __construct()
    {
        $this->conexion = (new Conexion())->getConexion(); // ✅ Ahora obtiene correctamente la conexión
        $this->registro = new RegistroActividad(); // ✅ Se asigna el objeto RegistroActividad a la propiedad de la clase para los logs.
    }

    public function get_vacacion()
{
    try {
        // Agregar la cláusula ORDER BY para ordenar por la fecha en orden descendente (más reciente primero)

        /* 
           $sql = "SELECT * FROM vacaciones_con_nombre ORDER BY fecha_inicio DESC"; // Ordenar de más reciente a más antiguo
        */

         $sql = "SELECT 
                    com_vacaciones.id_vacacion,
                    com_vacaciones.id_comercial,
                    com_vacaciones.fecha_inicio,
                    com_vacaciones.fecha_fin,
                    com_vacaciones.descripcion,
                    com_vacaciones.activo_vacacion,
                    (
                        SELECT CONCAT(comerciales.nombre, ' ', comerciales.apellidos)
                        FROM comerciales
                        WHERE comerciales.id_comercial = com_vacaciones.id_comercial
                    ) AS nombre_comercial
                FROM com_vacaciones
                ORDER BY fecha_inicio DESC";// Ordenar de más reciente a más antiguo
        $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
        $stmt->execute();

        // 🔹 Registrar consulta de las vacaciones
        $this->registro->registrarActividad(
            'admin',
            'Vacaciones',
            'Consulta',
            "Se consultó el listado de vacaciones", 
            'info'
        );
        
        // Devuelvo los resultados de la consulta
        return $stmt->fetchAll(PDO::FETCH_ASSOC); // Devuelve todas las vacaciones ordenadas
        
    } catch (PDOException $e) {
   
        // Registrar el error en el log
        $this->registro->registrarActividad(
            'admin',
            'Vacaciones',
            'Error',
            "Error al obtener vacaciones: " . $e->getMessage(),
            'error'
        );
    
        // Esto para desarrollo (puedes eliminarlo para producción)
        die("Error al mostrar las vacaciones: " . $e->getMessage());
    
        // En producción, se recomienda registrar el error en un archivo de logs y devolver false
        /*error_log("Error al obtener productos: " . $e->getMessage()); // Registrar error
        return false; // No detener el script, manejar el error en la llamada*/
        // El error se almacena en los logs de PHP o Apache (/var/log/apache2/error.log).
    }
}

    public function get_vacacionxid($idvacacion)
    {
        try {

            /* 
            $sql = "SELECT * FROM vacaciones_con_nombre where id_vacacion=?";
            */
            
            $sql = "SELECT 
                    com_vacaciones.id_vacacion,
                    com_vacaciones.id_comercial,
                    com_vacaciones.fecha_inicio,
                    com_vacaciones.fecha_fin,
                    com_vacaciones.descripcion,
                    com_vacaciones.activo_vacacion,
                    (
                        SELECT CONCAT(comerciales.nombre, ' ', comerciales.apellidos)
                        FROM comerciales
                        WHERE comerciales.id_comercial = com_vacaciones.id_comercial
                    ) AS nombre_comercial
                FROM com_vacaciones
                WHERE com_vacaciones.id_vacacion = ?";

            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->bindValue(1, $idvacacion, PDO::PARAM_INT);
            $stmt->execute();

                // 🔹 Registrar consulta de las vacaciones
        $this->registro->registrarActividad(
            'admin',
            'Vacaciones',
            'Consulta por id',
            "Se consultó el id de vacaciones " . $idvacacion, 
            'info'
        );

            return $resultado = $stmt->fetch(PDO::FETCH_ASSOC); // Solo devuelve un registro;
        } catch (PDOException $e) {

            // Registrar el error en el log
            $this->registro->registrarActividad(
                'admin',
                'Vacaciones',
                'Error',
                "Error al obtener el id de vacaciones " . $idvacacion . " | " . $e->getMessage(),
                'error'
            );

            // Esto para desarrollo
            die("Error al mostrar las vacaciones con id {$idvacacion}:" . $e->getMessage());

            //En producción, se recomienda registrar el error en un archivo de logs y devolver false
            /*error_log("Error al obtener usuarios: " . $e->getMessage()); // Registrar error
            return false; // No detener el script, manejar el error en la llamada*/
            // El error se almacena en los logs de PHP o Apache (/var/log/apache2/error.log).
        }
    }

    public function delete_vacacionxid($idvacacion)
    {
        try {
            $sql = "UPDATE com_vacaciones set activo_vacacion=0 where id_vacacion=?";
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->bindValue(1, $idvacacion, PDO::PARAM_INT);
            $stmt->execute();

        // 🔹 Registrar consulta de las vacaciones
        $this->registro->registrarActividad(
            'admin',
            'Vacaciones',
            'Desactivada la vacacion',
            "Se desactivó la vacación con el id de vacaciones " . $idvacacion, 
            'info'
        );

            return $stmt->rowCount() > 0; // Retorna true si se eliminó al menos un usuario, false si no existía el ID.
        } catch (PDOException $e) {

            // ACCIÓN GUARDAR PARA EL ARCHIVO DE LOGS
            // Registrar el error en el log
            $this->registro->registrarActividad(
                'admin',
                'Vacaciones',
                'Error',
                "Error al desactivar el id de vacaciones " . $idvacacion . " | " . $e->getMessage(),
                'error'
            );

            // Esto para desarrollo
            die("Error al eliminar la vacacion con id {$idvacacion}:" . $e->getMessage());
            return false;

            //En producción, se recomienda registrar el error en un archivo de logs y devolver false
            /*error_log("Error al obtener usuarios: " . $e->getMessage()); // Registrar error
            return false; // No detener el script, manejar el error en la llamada*/
            // El error se almacena en los logs de PHP o Apache (/var/log/apache2/error.log).
        }
    }

    public function activar_vacacionxid($idvacacion)
    {
        try {
            $sql = "UPDATE com_vacaciones set activo_vacacion=1 where id_vacacion=?";
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->bindValue(1, $idvacacion, PDO::PARAM_INT);
            $stmt->execute();

               // 🔹 Registrar consulta de las vacaciones
        $this->registro->registrarActividad(
            'admin',
            'Vacaciones',
            'Activada la vacacion',
            "Se activó la vacación con el id de vacaciones " . $idvacacion, 
            'info'
        );

            // return $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC); // Devuelve el resultado de un solo usuario (fetch)
            return $stmt->rowCount() > 0; // Retorna true si se eliminó al menos un usuario, false si no existía el ID.
        } catch (PDOException $e) {

             // Registrar el error en el log
             $this->registro->registrarActividad(
                'admin',
                'Vacaciones',
                'Error',
                "Error al activar el id de vacaciones " . $idvacacion . " | " . $e->getMessage(),
                'error'
            );

            // Esto para desarrollo
            die("Error al activar el id de vacaciones {$idvacacion}:" . $e->getMessage());
            return false;

            //En producción, se recomienda registrar el error en un archivo de logs y devolver false
            /*error_log("Error al obtener usuarios: " . $e->getMessage()); // Registrar error
            return false; // No detener el script, manejar el error en la llamada*/
            // El error se almacena en los logs de PHP o Apache (/var/log/apache2/error.log).
        }
    }

    public function insert_vacacion($idcomercial, $fechainicio, $fechafin, $descripcion)
    {
        try {
            $sql = "INSERT INTO com_vacaciones (id_comercial, fecha_inicio, fecha_fin, descripcion, activo_vacacion) VALUES (?, ?, ?, ?, 1)";
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->bindValue(1, $idcomercial, PDO::PARAM_INT); // Se enlaza el valor del nombre
            $stmt->bindValue(2, $fechainicio, PDO::PARAM_STR); // Se enlaza el valor de apellidos
            $stmt->bindValue(3, $fechafin, PDO::PARAM_STR); // Se enlaza el valor del movil
            $stmt->bindValue(4, $descripcion, PDO::PARAM_STR); // Se enlaza el valor del telefono
            $stmt->execute();
            $idInsert = $this->conexion->lastInsertId(); // Se obtiene el ID del ultimo insertado

                   // 🔹 Registrar consulta de las vacaciones
            $this->registro->registrarActividad(
                'admin',
                'Vacaciones',
                'Insertada la vacacion',
                "Se insertó la vacación con el id de vacaciones " . $idInsert, 
                'info'
            );

            return true; // Devuelve true si la inserción fue exitosa
            //return $idInsert; // Devuelve el ID del usuario insertado
        } catch (PDOException $e) {

            // Registrar el error en el log
            $this->registro->registrarActividad(
                'admin',
                'Vacaciones',
                'Error',
                "Error al insertar la vacacion: ". $e->getMessage(),
                'error'
            );

            die("Error al insertar la vacacion: " . $e->getMessage());
        }
    }

    public function update_vacacion($idvacacion, $idcomercial, $fechainicio, $fechafin, $descripcion)
    {
        try {
            $sql = "UPDATE com_vacaciones SET id_comercial = ?, fecha_inicio = ?, fecha_fin = ?, descripcion = ?  WHERE id_vacacion = ?";
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->bindValue(1, $idcomercial, PDO::PARAM_INT);
            $stmt->bindValue(2, $fechainicio, PDO::PARAM_STR);
            $stmt->bindValue(3, $fechafin, PDO::PARAM_STR);
            $stmt->bindValue(4, $descripcion, PDO::PARAM_STR);
            $stmt->bindValue(5, $idvacacion, PDO::PARAM_INT);
            $stmt->execute();

                // 🔹 Registrar consulta de las vacaciones
                $this->registro->registrarActividad(
                    'admin',
                    'Vacaciones',
                    'Actualizada la vacacion',
                    "Se actualizó la vacación con el id de vacaciones " . $idInsert, 
                    'info'
                );
        

            return true; // Devuelve true si el update fue exitoso

        } catch (PDOException $e) {

             // Registrar el error en el log
             $this->registro->registrarActividad(
                'admin',
                'Vacaciones',
                'Error',
                "Error al actualizar la vacacion: ". $e->getMessage(),
                'error'
            );

            die("Error al hacer update al comercial: " . $e->getMessage());
        }
    }

    public function verificarSolapamiento($id_comercial, $fecha_inicio, $fecha_fin, $id_vacacion = null) {
        try {
            // Validación básica de fechas
            if (strtotime($fecha_inicio) === false || strtotime($fecha_fin) === false) {
                throw new Exception("Formato de fecha inválido");
            }
    
            // Consulta SQL optimizada que cubre todos los casos de solapamiento
            $sql = "SELECT COUNT(*) as solapadas 
                    FROM com_vacaciones 
                    WHERE id_comercial = ? 
                    AND activo_vacacion = 1 
                    AND (
                        (fecha_inicio <= ? AND fecha_fin >= ?) OR  -- Rango nuevo dentro de existente
                        (fecha_inicio >= ? AND fecha_fin <= ?) OR  -- Rango existente dentro del nuevo
                        (fecha_inicio <= ? AND fecha_fin >= ?)     -- Cualquier solapamiento
                    )";
            
            if ($id_vacacion !== null) {
                $sql .= " AND id_vacacion != ?";
            }
    
            $stmt = $this->conexion->prepare($sql);
            
            // Parámetros para la consulta (cada fecha aparece múltiples veces)
            $params = [
                $id_comercial,
                $fecha_fin, $fecha_inicio,    // Primera condición
                $fecha_inicio, $fecha_fin,    // Segunda condición
                $fecha_inicio, $fecha_fin     // Tercera condición
            ];
            
            if ($id_vacacion !== null) {
                $params[] = $id_vacacion;
            }
    
            // Log para depuración
            error_log("Verificando solapamiento para comercial $id_comercial: $fecha_inicio a $fecha_fin");
            error_log("SQL: $sql");
            error_log("Params: " . print_r($params, true));
    
            $stmt->execute($params);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
    
            $solapadas = $resultado['solapadas'] > 0;
            error_log("Resultado: " . ($solapadas ? 'SOLAPAMIENTO DETECTADO' : 'Sin solapamiento'));
    
            return $solapadas;
    
        } catch (PDOException $e) {
            error_log("Error en verificarSolapamiento: " . $e->getMessage());
            $this->registro->registrarActividad(
                'admin',
                'Vacaciones',
                'Error en verificación',
                "Error: " . $e->getMessage(),
                'error'
            );
            throw new Exception("Error al verificar solapamiento: " . $e->getMessage());
        }
    }

    public function obtenerPorRangoFechas($fechaInicio, $fechaFin)
    {
        try {
            // Consulta que filtra SOLO vacaciones que caen COMPLETAMENTE dentro del rango

            /* 
             $sql = "SELECT * FROM vacaciones_con_nombre 
                WHERE fecha_inicio >= ? 
                AND fecha_fin <= ?
                ORDER BY fecha_inicio DESC";
            */
            
            $sql = "SELECT 
                        com_vacaciones.id_vacacion,
                        com_vacaciones.id_comercial,
                        com_vacaciones.fecha_inicio,
                        com_vacaciones.fecha_fin,
                        com_vacaciones.descripcion,
                        com_vacaciones.activo_vacacion,
                        (
                            SELECT CONCAT(comerciales.nombre, ' ', comerciales.apellidos)
                            FROM comerciales
                            WHERE comerciales.id_comercial = com_vacaciones.id_comercial
                        ) AS nombre_comercial
                    FROM com_vacaciones
                    WHERE com_vacaciones.fecha_inicio >= ? 
                    AND com_vacaciones.fecha_fin <= ?
                    ORDER BY com_vacaciones.fecha_inicio DESC";

            $stmt = $this->conexion->prepare($sql);
            
            // Bind de parámetros
            $stmt->bindValue(1, $fechaInicio, PDO::PARAM_STR);
            $stmt->bindValue(2, $fechaFin, PDO::PARAM_STR);
            
            $stmt->execute();
            
            // Registro de actividad
            $this->registro->registrarActividad(
                'admin',
                'Vacaciones',
                'Consulta filtrada',
                "Se consultó vacaciones entre $fechaInicio y $fechaFin", 
                'info'
            );
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Vacaciones',
                'Error',
                "Error al obtener vacaciones filtradas: " . $e->getMessage(),
                'error'
            );
            
            die("Error al filtrar vacaciones: " . $e->getMessage());
        }
    }


}
