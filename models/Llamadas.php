<?php

require_once '../config/conexion.php'; // ✅ Se incluye correctamente el archivo de conexión
require_once "../config/funciones.php";

class Llamadas
{

    private $conexion;
    private $registro; // ✅ Se declara la instancia para el objeto RegistroActividad 

    public function __construct()
    {
        $this->conexion = (new Conexion())->getConexion(); // ✅ Ahora obtiene correctamente la conexión
        $this->registro = new RegistroActividad(); // ✅ Se asigna el objeto RegistroActividad a la propiedad de la clase para los logs.
    }

    public function get_llamada()
    {
        try {

            /*
            $sql = "SELECT * FROM llamadas_con_comerciales_y_metodos";  // Es una vista que contiene el nombre de las llamadas
            */

            $sql = "SELECT 
                    l.id_llamada,
                    l.id_metodo,
                    l.nombre_comunicante,
                    l.domicilio_instalacion,
                    l.telefono_fijo,
                    l.telefono_movil,
                    l.email_contacto,
                    l.fecha_hora_preferida,
                    l.observaciones,
                    l.id_comercial_asignado,
                    l.estado,
                    l.fecha_recepcion,
                    l.activo_llamada,
                    c.nombre AS nombre_comercial,
                    m.nombre AS nombre_metodo,
                    m.imagen_metodo,
                    e.desc_estado AS descripcion_estado,
                    IFNULL(
                        (SELECT GROUP_CONCAT(a.nombre_archivo SEPARATOR ',')
                        FROM adjunto_llamada a
                        WHERE a.id_llamada = l.id_llamada AND a.estado = 1), 'Sin archivos') AS archivos_adjuntos,
                    (SELECT COUNT(0) > 0 FROM contactos c2 WHERE c2.id_llamada = l.id_llamada) AS tiene_contactos,
                    (l.estado = 3) AS estado_es_3,
                    (SELECT COUNT(0) > 0 FROM adjunto_llamada a2 WHERE a2.id_llamada = l.id_llamada AND a2.estado = 1) AS tiene_adjuntos,
                    (SELECT c3.fecha_hora_contacto FROM contactos c3 WHERE c3.id_llamada = l.id_llamada ORDER BY c3.fecha_hora_contacto LIMIT 1) AS fecha_primer_contacto
                FROM 
                    llamadas l
                LEFT JOIN comerciales c ON c.id_comercial = l.id_comercial_asignado
                LEFT JOIN metodos_contacto m ON m.id_metodo = l.id_metodo
                LEFT JOIN estados_llamada e ON e.id_estado = l.estado";
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->execute();

            // 🔹 Registrar consulta de las llamadas
             $this->registro->registrarActividad(
            'admin',
            'Llamadas',
            'Consulta',
            "Se consultó el listado de llamadas", 
            'info'
        );
        
            // Devuelvo los resultados de la consulta
            return $stmt->fetchAll(PDO::FETCH_ASSOC); // Devuelve todos los productos
        
        } catch (PDOException $e) {
       
             // Registrar el error en el log
             $this->registro->registrarActividad(
                'admin',
                'Llamadas',
                'Error',
                "Error al obtener llamadas: " . $e->getMessage(),
                'error'
            );
        
            // Esto para desarrollo (puedes eliminarlo para producción)
            die("Error al mostrar las llamadas: " . $e->getMessage());
        
            // En producción, se recomienda registrar el error en un archivo de logs y devolver false
            /*error_log("Error al obtener productos: " . $e->getMessage()); // Registrar error
            return false; // No detener el script, manejar el error en la llamada*/
            // El error se almacena en los logs de PHP o Apache (/var/log/apache2/error.log).
        }
    }

    public function get_llamada_por_comercial($id_comercial)
    {
        try {
            $sql = "SELECT 
                        l.id_llamada,
                        l.id_metodo,
                        l.nombre_comunicante,
                        l.domicilio_instalacion,
                        l.telefono_fijo,
                        l.telefono_movil,
                        l.email_contacto,
                        l.fecha_hora_preferida,
                        l.observaciones,
                        l.id_comercial_asignado,
                        l.estado,
                        l.fecha_recepcion,
                        l.activo_llamada,
                        c.nombre AS nombre_comercial,
                        m.nombre AS nombre_metodo,
                        m.imagen_metodo,
                        e.desc_estado AS descripcion_estado,
                        IFNULL(
                            (SELECT GROUP_CONCAT(a.nombre_archivo SEPARATOR ',')
                            FROM adjunto_llamada a
                            WHERE a.id_llamada = l.id_llamada AND a.estado = 1), 'Sin archivos') AS archivos_adjuntos,
                        (SELECT COUNT(0) > 0 FROM contactos c2 WHERE c2.id_llamada = l.id_llamada) AS tiene_contactos,
                        (l.estado = 3) AS estado_es_3,
                        (SELECT COUNT(0) > 0 FROM adjunto_llamada a2 WHERE a2.id_llamada = l.id_llamada AND a2.estado = 1) AS tiene_adjuntos,
                        (SELECT c3.fecha_hora_contacto FROM contactos c3 WHERE c3.id_llamada = l.id_llamada ORDER BY c3.fecha_hora_contacto LIMIT 1) AS fecha_primer_contacto
                    FROM 
                        llamadas l
                    LEFT JOIN comerciales c ON c.id_comercial = l.id_comercial_asignado
                    LEFT JOIN metodos_contacto m ON m.id_metodo = l.id_metodo
                    LEFT JOIN estados_llamada e ON e.id_estado = l.estado
                    WHERE l.id_comercial_asignado = $id_comercial";

            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();

            // Registrar actividad si tienes esa función
            $this->registro->registrarActividad(
                'admin',
                'Llamadas',
                'Consulta',
                "Se consultó el listado de llamadas para comercial id: $id_comercial",
                'info'
            );

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Llamadas',
                'Error',
                "Error al obtener llamadas por comercial: " . $e->getMessage(),
                'error'
            );
            die("Error al mostrar las llamadas: " . $e->getMessage());
        }
    }


    public function get_llamadaxid($idllamada)
    {
        try {
            
            // Consulta SQL que reemplaza la vista llamadas_con_comerciales_y_metodos

            /*
            $sql = "SELECT * FROM llamadas_con_comerciales_y_metodos where id_llamada=?";
            */

            $sql = "SELECT 
                        l.id_llamada,
                        l.id_metodo,
                        l.nombre_comunicante,
                        l.domicilio_instalacion,
                        l.telefono_fijo,
                        l.telefono_movil,
                        l.email_contacto,
                        l.fecha_hora_preferida,
                        l.observaciones,
                        l.id_comercial_asignado,
                        l.estado,
                        l.fecha_recepcion,
                        l.activo_llamada,
                        c.nombre AS nombre_comercial,
                        m.nombre AS nombre_metodo,
                        m.imagen_metodo,
                        e.desc_estado AS descripcion_estado,
                        IFNULL(
                            (SELECT GROUP_CONCAT(a.nombre_archivo SEPARATOR ',')
                            FROM adjunto_llamada a
                            WHERE a.id_llamada = l.id_llamada AND a.estado = 1),
                        'Sin archivos') AS archivos_adjuntos,
                        (SELECT COUNT(0) > 0 FROM contactos c2 WHERE c2.id_llamada = l.id_llamada) AS tiene_contactos,
                        (l.estado = 3) AS estado_es_3,
                        (SELECT COUNT(0) > 0 FROM adjunto_llamada a2 WHERE a2.id_llamada = l.id_llamada AND a2.estado = 1) AS tiene_adjuntos,
                        (SELECT c3.fecha_hora_contacto
                        FROM contactos c3
                        WHERE c3.id_llamada = l.id_llamada
                        ORDER BY c3.fecha_hora_contacto
                        LIMIT 1) AS fecha_primer_contacto
                    FROM llamadas l
                    LEFT JOIN comerciales c ON c.id_comercial = l.id_comercial_asignado
                    LEFT JOIN metodos_contacto m ON m.id_metodo = l.id_metodo
                    LEFT JOIN estados_llamada e ON e.id_estado = l.estado
                    WHERE l.id_llamada = ?";

            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->bindValue(1, $idllamada, PDO::PARAM_INT);
            $stmt->execute();

            // 🔹 Registrar consulta de la llamada
            $this->registro->registrarActividad(
                'admin',
                'Llamadas',
                'Consulta por id',
                "Se consultó el id de llamada " . $idllamada, 
                'info'
            );

            return $stmt->fetch(PDO::FETCH_ASSOC); // Devuelve un solo registro;
        } catch (PDOException $e) {

            // Registrar el error en el log
            $this->registro->registrarActividad(
                'admin',
                'Llamadas',
                'Error',
                "Error al obtener la llamada con id " . $idllamada . " | " . $e->getMessage(),
                'error'
            );

            // Esto para desarrollo
            die("Error al mostrar la llamada {$idllamada}: " . $e->getMessage());

            // En producción, se recomienda registrar el error en un archivo de logs y devolver false
            /*error_log("Error al obtener la llamada: " . $e->getMessage()); 
            return false;*/
        }
    }


    public function delete_llamadaxid($idllamada)
    {
        try {
            $sql = "UPDATE llamadas set activo_llamada=0 where id_llamada=?";
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->bindValue(1, $idllamada, PDO::PARAM_INT);
            $stmt->execute();

              // 🔹 Registrar consulta de las vacaciones
            $this->registro->registrarActividad(
            'admin',
            'Llamadas',
            'Desactivada la llamada',
            "Se desactivó la llamada con el id de llamadas " . $idllamada, 
            'info'
        );

            return $stmt->rowCount() > 0; // Retorna true si se eliminó al menos un usuario, false si no existía el ID.
        } catch (PDOException $e) {

            // ACCIÓN GUARDAR PARA EL ARCHIVO DE LOGS
            // Registrar el error en el log
            $this->registro->registrarActividad(
                'admin',
                'Llamadas',
                'Error',
                "Error al desactivar el id de llamadas " . $idllamada . " | " . $e->getMessage(),
                'error'
            );
            // Esto para desarrollo
            die("Error al eliminar la llamada {$idllamada}:" . $e->getMessage());
            return false;

            //En producción, se recomienda registrar el error en un archivo de logs y devolver false
            /*error_log("Error al obtener usuarios: " . $e->getMessage()); // Registrar error
            return false; // No detener el script, manejar el error en la llamada*/
            // El error se almacena en los logs de PHP o Apache (/var/log/apache2/error.log).
        }
    }

    public function activar_llamadaxid($idllamada)
    {
        try {
            $sql = "UPDATE llamadas set activo_llamada=1 where id_llamada=?";
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->bindValue(1, $idllamada, PDO::PARAM_INT);
            $stmt->execute();

            // 🔹 Registrar consulta de las vacaciones
            $this->registro->registrarActividad(
            'admin',
            'Llamadas',
            'Activada la llamada',
            "Se activó la llamada con el id de llamadas " . $idllamada, 
            'info'
        );

            // return $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC); // Devuelve el resultado de un solo usuario (fetch)
            return $stmt->rowCount() > 0; // Retorna true si se eliminó al menos un usuario, false si no existía el ID.
        } catch (PDOException $e) {

             // Registrar el error en el log
             $this->registro->registrarActividad(
                'admin',
                'Llamadas',
                'Error',
                "Error al activar el id de llamadas " . $idllamada . " | " . $e->getMessage(),
                'error'
            );

            // Esto para desarrollo
            die("Error al activar la llamada con id {$idllamada}:" . $e->getMessage());
            return false;

            //En producción, se recomienda registrar el error en un archivo de logs y devolver false
            /*error_log("Error al obtener usuarios: " . $e->getMessage()); // Registrar error
            return false; // No detener el script, manejar el error en la llamada*/
            // El error se almacena en los logs de PHP o Apache (/var/log/apache2/error.log).
        }
    }

    public function insert_llamada($idmetodo, $nombre_comunicante, $domicilio_instalacion, $telefono_fijo = null, $telefono_movil = null, $email_contacto = null, $fecha_hora_preferida = null, $observaciones = null, $id_comercial_asignado, $estado, $fecha_recepcion)
{
    try {
        $sql = "INSERT INTO llamadas 
                (id_metodo, nombre_comunicante, domicilio_instalacion, 
                 telefono_fijo, telefono_movil, email_contacto, 
                 fecha_hora_preferida, observaciones, 
                 id_comercial_asignado, estado, fecha_recepcion, activo_llamada) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)";
        
        $stmt = $this->conexion->prepare($sql);
        
        // Campos obligatorios
        $stmt->bindValue(1, $idmetodo, PDO::PARAM_INT);
        $stmt->bindValue(2, $nombre_comunicante, PDO::PARAM_STR);
        $stmt->bindValue(3, $domicilio_instalacion, PDO::PARAM_STR);
        
        // Campos nullables - manejo especial
        $stmt->bindValue(4, $telefono_fijo, $telefono_fijo ? PDO::PARAM_INT : PDO::PARAM_NULL);
        $stmt->bindValue(5, $telefono_movil, $telefono_movil ? PDO::PARAM_INT : PDO::PARAM_NULL);
        $stmt->bindValue(6, $email_contacto, $email_contacto ? PDO::PARAM_STR : PDO::PARAM_NULL);
        $stmt->bindValue(7, $fecha_hora_preferida, $fecha_hora_preferida ? PDO::PARAM_STR : PDO::PARAM_NULL);
        $stmt->bindValue(8, $observaciones, $observaciones ? PDO::PARAM_STR : PDO::PARAM_NULL);
        
        // Campos obligatorios restantes
        $stmt->bindValue(9, $id_comercial_asignado, PDO::PARAM_INT);
        $stmt->bindValue(10, $estado, PDO::PARAM_INT);
        $stmt->bindValue(11, $fecha_recepcion, PDO::PARAM_STR);
        
        $stmt->execute();
        $idInsert = $this->conexion->lastInsertId();

        $this->registro->registrarActividad(
            'admin',
            'Llamadas',
            'Insertada la llamada',
            "Se insertó la llamada con el id de llamadas " . $idInsert, 
            'info'
        );
        
        return true;
    } catch (PDOException $e) {
        $this->registro->registrarActividad(
            'admin',
            'Llamadas',
            'Error',
            "Error al insertar la llamada: ". $e->getMessage(),
            'error'
        );
        throw new Exception("Error al insertar la llamada: " . $e->getMessage());
    }
}

public function update_llamada($idllamada, $idmetodo, $nombre_comunicante, $domicilio_instalacion, $telefono_fijo = null, $telefono_movil = null, $email_contacto = null, $fecha_hora_preferida = null, $observaciones = null, $id_comercial_asignado, $estado, $fecha_recepcion)
{
    try {
        $sql = "UPDATE llamadas SET 
                id_metodo = ?, 
                nombre_comunicante = ?, 
                domicilio_instalacion = ?, 
                telefono_fijo = ?, 
                telefono_movil = ?, 
                email_contacto = ?, 
                fecha_hora_preferida = ?, 
                observaciones = ?, 
                id_comercial_asignado = ?, 
                estado = ?, 
                fecha_recepcion = ?
                WHERE id_llamada = ?";
        
        $stmt = $this->conexion->prepare($sql);
        
        // Campos obligatorios
        $stmt->bindValue(1, $idmetodo, PDO::PARAM_INT);
        $stmt->bindValue(2, $nombre_comunicante, PDO::PARAM_STR);
        $stmt->bindValue(3, $domicilio_instalacion, PDO::PARAM_STR);
        
        // Campos nullables - mismo manejo que en insert
        $stmt->bindValue(4, $telefono_fijo, $telefono_fijo ? PDO::PARAM_INT : PDO::PARAM_NULL);
        $stmt->bindValue(5, $telefono_movil, $telefono_movil ? PDO::PARAM_INT : PDO::PARAM_NULL);
        $stmt->bindValue(6, $email_contacto, $email_contacto ? PDO::PARAM_STR : PDO::PARAM_NULL);
        $stmt->bindValue(7, $fecha_hora_preferida, $fecha_hora_preferida ? PDO::PARAM_STR : PDO::PARAM_NULL);
        $stmt->bindValue(8, $observaciones, $observaciones ? PDO::PARAM_STR : PDO::PARAM_NULL);
        
        // Campos obligatorios restantes
        $stmt->bindValue(9, $id_comercial_asignado, PDO::PARAM_INT);
        $stmt->bindValue(10, $estado, PDO::PARAM_INT);
        $stmt->bindValue(11, $fecha_recepcion, PDO::PARAM_STR);
        $stmt->bindValue(12, $idllamada, PDO::PARAM_INT);
        
        $stmt->execute();
        
        $this->registro->registrarActividad(
            'admin',
            'Llamadas',
            'Actualizada la llamada',
            "Se actualizó la llamada con ID: " . $idllamada, 
            'info'
        );
        
        return true;
    } catch (PDOException $e) {
        $this->registro->registrarActividad(
            'admin',
            'Llamadas',
            'Error',
            "Error al actualizar la llamada: ". $e->getMessage(),
            'error'
        );
        throw new Exception("Error al actualizar la llamada: " . $e->getMessage());
    }
}

public function verificar_estado_activo_llamada($id_llamada)
{
    try {
        $sql = "SELECT activo_llamada FROM llamadas WHERE id_llamada = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(1, $id_llamada, PDO::PARAM_INT);
        $stmt->execute();
        
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Registrar actividad
        $this->registro->registrarActividad(
            'admin',
            'Llamadas',
            'Verificación estado activo llamada',
            "Se verificó activo_llamada para la llamada con ID: " . $id_llamada,
            'info'
        );
        
        return $resultado;
        
    } catch (PDOException $e) {
        // Registrar el error
        $this->registro->registrarActividad(
            'admin',
            'Llamadas',
            'Error verificación estado',
            "Error al verificar activo_llamada para llamada ID: " . $id_llamada . " | " . $e->getMessage(),
            'error'
        );
        
        // Para desarrollo
        die("Error al verificar estado activo de la llamada: " . $e->getMessage());
        
        // En producción:
        // error_log("Error al verificar estado activo llamada: " . $e->getMessage());
        // return ['activo_llamada' => null]; // O return false según tu lógica
    }
}


}
