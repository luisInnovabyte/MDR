<?php

require_once '../config/conexion.php'; // ✅ Se incluye correctamente el archivo de conexión
require_once "../config/funciones.php";

class Contactos
{
    private $conexion;
    private $registro; // ✅ Se declara la instancia para el objeto RegistroActividad 

    public function __construct()
    {
        $this->conexion = (new Conexion())->getConexion(); // ✅ Ahora obtiene correctamente la conexión
        $this->registro = new RegistroActividad(); // ✅ Se asigna el objeto RegistroActividad a la propiedad de la clase para los logs.
    }

    public function get_contacto()
    {
        try {

            /* 
            $sql = "
                SELECT c.*, v.fecha_visita_cerrada
                FROM     c
                LEFT JOIN visitas_cerradas v ON c.id_contacto = v.id_contacto
            ";
            */

            $sql = "
                SELECT c.*, v.fecha_visita_cerrada
                FROM
                (
                    SELECT 
                        c.id_contacto,
                        c.id_llamada,
                        c.id_metodo,
                        c.fecha_hora_contacto,
                        c.observaciones,
                        c.id_visita_cerrada,
                        (SELECT vc.fecha_visita_cerrada FROM visitas_cerradas vc WHERE vc.id_visita_cerrada = c.id_visita_cerrada) AS fecha_visita_cerrada,
                        c.estado,
                        (SELECT l.nombre_comunicante FROM llamadas l WHERE l.id_llamada = c.id_llamada) AS nombre_comunicante,
                        (SELECT l.domicilio_instalacion FROM llamadas l WHERE l.id_llamada = c.id_llamada) AS domicilio_instalacion,
                        (SELECT l.telefono_fijo FROM llamadas l WHERE l.id_llamada = c.id_llamada) AS telefono_fijo,
                        (SELECT l.telefono_movil FROM llamadas l WHERE l.id_llamada = c.id_llamada) AS telefono_movil,
                        (SELECT l.email_contacto FROM llamadas l WHERE l.id_llamada = c.id_llamada) AS email_contacto,
                        (SELECT l.fecha_hora_preferida FROM llamadas l WHERE l.id_llamada = c.id_llamada) AS fecha_hora_preferida,
                        (SELECT l.fecha_recepcion FROM llamadas l WHERE l.id_llamada = c.id_llamada) AS fecha_recepcion,
                        (SELECT l.id_comercial_asignado FROM llamadas l WHERE l.id_llamada = c.id_llamada) AS id_comercial_asignado,
                        (SELECT l.estado FROM llamadas l WHERE l.id_llamada = c.id_llamada) AS estado_llamada,
                        (SELECT l.activo_llamada FROM llamadas l WHERE l.id_llamada = c.id_llamada) AS activo_llamada,
                        (SELECT m.nombre FROM metodos_contacto m WHERE m.id_metodo = c.id_metodo) AS nombre_metodo,
                        (SELECT m.imagen_metodo FROM metodos_contacto m WHERE m.id_metodo = c.id_metodo) AS imagen_metodo,
                        (SELECT e.desc_estado FROM estados_llamada e WHERE e.id_estado = (SELECT l.estado FROM llamadas l WHERE l.id_llamada = c.id_llamada)) AS descripcion_estado_llamada,
                        (SELECT com.nombre FROM comerciales com WHERE com.id_comercial = (SELECT l.id_comercial_asignado FROM llamadas l WHERE l.id_llamada = c.id_llamada)) AS nombre_comercial,
                        IFNULL((SELECT GROUP_CONCAT(a.nombre_archivo SEPARATOR ',') FROM adjunto_llamada a WHERE a.id_llamada = c.id_llamada AND a.estado = 1), 'Sin archivos') AS archivos_adjuntos,
                        (SELECT (COUNT(0) > 0) FROM contactos cont WHERE cont.id_llamada = c.id_llamada) AS tiene_contactos,
                        ((SELECT l.estado FROM llamadas l WHERE l.id_llamada = c.id_llamada) = 3) AS estado_es_3,
                        (SELECT (COUNT(0) > 0) FROM adjunto_llamada a WHERE a.id_llamada = c.id_llamada AND a.estado = 1) AS tiene_adjuntos
                    FROM contactos c
                ) AS c
                LEFT JOIN visitas_cerradas v ON c.id_contacto = v.id_contacto
            ";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();

            $this->registro->registrarActividad(
                'admin',
                'Contactos',
                'Consulta',
                "Se consultó el listado de contactos con fecha de visita cerrada",
                'info'
            );

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Contactos',
                'Error',
                "Error al obtener contactos: " . $e->getMessage(),
                'error'
            );
            die("Error al mostrar los contactos: " . $e->getMessage());
        }
    }

    public function get_contacto_por_comercial($idComercial)
      {
        try {
            
            $sql = "
                SELECT c.*, v.fecha_visita_cerrada
                FROM
                (
                    SELECT 
                        c.id_contacto,
                        c.id_llamada,
                        c.id_metodo,
                        c.fecha_hora_contacto,
                        c.observaciones,
                        c.id_visita_cerrada,
                        (SELECT vc.fecha_visita_cerrada FROM visitas_cerradas vc WHERE vc.id_visita_cerrada = c.id_visita_cerrada) AS fecha_visita_cerrada,
                        c.estado,
                        (SELECT l.nombre_comunicante FROM llamadas l WHERE l.id_llamada = c.id_llamada) AS nombre_comunicante,
                        (SELECT l.domicilio_instalacion FROM llamadas l WHERE l.id_llamada = c.id_llamada) AS domicilio_instalacion,
                        (SELECT l.telefono_fijo FROM llamadas l WHERE l.id_llamada = c.id_llamada) AS telefono_fijo,
                        (SELECT l.telefono_movil FROM llamadas l WHERE l.id_llamada = c.id_llamada) AS telefono_movil,
                        (SELECT l.email_contacto FROM llamadas l WHERE l.id_llamada = c.id_llamada) AS email_contacto,
                        (SELECT l.fecha_hora_preferida FROM llamadas l WHERE l.id_llamada = c.id_llamada) AS fecha_hora_preferida,
                        (SELECT l.fecha_recepcion FROM llamadas l WHERE l.id_llamada = c.id_llamada) AS fecha_recepcion,
                        (SELECT l.id_comercial_asignado FROM llamadas l WHERE l.id_llamada = c.id_llamada) AS id_comercial_asignado,
                        (SELECT l.estado FROM llamadas l WHERE l.id_llamada = c.id_llamada) AS estado_llamada,
                        (SELECT l.activo_llamada FROM llamadas l WHERE l.id_llamada = c.id_llamada) AS activo_llamada,
                        (SELECT m.nombre FROM metodos_contacto m WHERE m.id_metodo = c.id_metodo) AS nombre_metodo,
                        (SELECT m.imagen_metodo FROM metodos_contacto m WHERE m.id_metodo = c.id_metodo) AS imagen_metodo,
                        (SELECT e.desc_estado FROM estados_llamada e WHERE e.id_estado = (SELECT l.estado FROM llamadas l WHERE l.id_llamada = c.id_llamada)) AS descripcion_estado_llamada,
                        (SELECT com.nombre FROM comerciales com WHERE com.id_comercial = (SELECT l.id_comercial_asignado FROM llamadas l WHERE l.id_llamada = c.id_llamada)) AS nombre_comercial,
                        IFNULL((SELECT GROUP_CONCAT(a.nombre_archivo SEPARATOR ',') FROM adjunto_llamada a WHERE a.id_llamada = c.id_llamada AND a.estado = 1), 'Sin archivos') AS archivos_adjuntos,
                        (SELECT (COUNT(0) > 0) FROM contactos cont WHERE cont.id_llamada = c.id_llamada) AS tiene_contactos,
                        ((SELECT l.estado FROM llamadas l WHERE l.id_llamada = c.id_llamada) = 3) AS estado_es_3,
                        (SELECT (COUNT(0) > 0) FROM adjunto_llamada a WHERE a.id_llamada = c.id_llamada AND a.estado = 1) AS tiene_adjuntos
                    FROM contactos c
                    WHERE c.id_llamada IN (
                        SELECT l.id_llamada FROM llamadas l WHERE l.id_comercial_asignado = $idComercial
                    )
                ) AS c
                LEFT JOIN visitas_cerradas v ON c.id_contacto = v.id_contacto";

            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();

            $this->registro->registrarActividad(
                'admin',
                'Contactos',
                'Consulta',
                "Se consultó el listado de contactos filtrado por comercial: $idComercial",
                'info'
            );

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Contactos',
                'Error',
                "Error al obtener contactos por comercial: " . $e->getMessage(),
                'error'
            );
            die("Error al mostrar los contactos por comercial: " . $e->getMessage());
        }
      }

    public function get_contactoxid($idcontacto)
    {
        try {
            /*
            $sql = "
                SELECT c.*, v.fecha_visita_cerrada
                FROM contactos_con_nombre_comunicante c
                LEFT JOIN visitas_cerradas v ON c.id_contacto = v.id_contacto
                WHERE c.id_contacto = ?
            ";
            */

            $sql = "SELECT c.*, v.fecha_visita_cerrada
                    FROM
                    (
                    SELECT 
                        c.id_contacto,
                        c.id_llamada,
                        c.id_metodo,
                        c.fecha_hora_contacto,
                        c.observaciones,
                        c.id_visita_cerrada,
                        (SELECT vc.fecha_visita_cerrada FROM visitas_cerradas vc WHERE vc.id_visita_cerrada = c.id_visita_cerrada) AS fecha_visita_cerrada,
                        c.estado,
                        (SELECT l.nombre_comunicante FROM llamadas l WHERE l.id_llamada = c.id_llamada) AS nombre_comunicante,
                        (SELECT l.domicilio_instalacion FROM llamadas l WHERE l.id_llamada = c.id_llamada) AS domicilio_instalacion,
                        (SELECT l.telefono_fijo FROM llamadas l WHERE l.id_llamada = c.id_llamada) AS telefono_fijo,
                        (SELECT l.telefono_movil FROM llamadas l WHERE l.id_llamada = c.id_llamada) AS telefono_movil,
                        (SELECT l.email_contacto FROM llamadas l WHERE l.id_llamada = c.id_llamada) AS email_contacto,
                        (SELECT l.fecha_hora_preferida FROM llamadas l WHERE l.id_llamada = c.id_llamada) AS fecha_hora_preferida,
                        (SELECT l.fecha_recepcion FROM llamadas l WHERE l.id_llamada = c.id_llamada) AS fecha_recepcion,
                        (SELECT l.id_comercial_asignado FROM llamadas l WHERE l.id_llamada = c.id_llamada) AS id_comercial_asignado,
                        (SELECT l.estado FROM llamadas l WHERE l.id_llamada = c.id_llamada) AS estado_llamada,
                        (SELECT l.activo_llamada FROM llamadas l WHERE l.id_llamada = c.id_llamada) AS activo_llamada,
                        (SELECT m.nombre FROM metodos_contacto m WHERE m.id_metodo = c.id_metodo) AS nombre_metodo,
                        (SELECT m.imagen_metodo FROM metodos_contacto m WHERE m.id_metodo = c.id_metodo) AS imagen_metodo,
                        (SELECT e.desc_estado FROM estados_llamada e WHERE e.id_estado = (SELECT l.estado FROM llamadas l WHERE l.id_llamada = c.id_llamada)) AS descripcion_estado_llamada,
                        (SELECT com.nombre FROM comerciales com WHERE com.id_comercial = (SELECT l.id_comercial_asignado FROM llamadas l WHERE l.id_llamada = c.id_llamada)) AS nombre_comercial,
                        IFNULL((SELECT GROUP_CONCAT(a.nombre_archivo SEPARATOR ',') FROM adjunto_llamada a WHERE a.id_llamada = c.id_llamada AND a.estado = 1), 'Sin archivos') AS archivos_adjuntos,
                        (SELECT (COUNT(0) > 0) FROM contactos cont WHERE cont.id_llamada = c.id_llamada) AS tiene_contactos,
                        ((SELECT l.estado FROM llamadas l WHERE l.id_llamada = c.id_llamada) = 3) AS estado_es_3,
                        (SELECT (COUNT(0) > 0) FROM adjunto_llamada a WHERE a.id_llamada = c.id_llamada AND a.estado = 1) AS tiene_adjuntos
                    FROM contactos c
                    ) AS c
                    LEFT JOIN visitas_cerradas v ON c.id_contacto = v.id_contacto
                    WHERE c.id_contacto = $idcontacto
                    ";

            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();

            $this->registro->registrarActividad(
                'admin',
                'Contactos',
                'Consulta por id',
                "Se consultó el id de contactos " . $idcontacto, 
                'info'
            );

            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Contactos',
                'Error',
                "Error al obtener el id de contactos " . $idcontacto . " | " . $e->getMessage(),
                'error'
            );

            die("Error al mostrar los contactos con id {$idcontacto}:" . $e->getMessage());
        }
    }

    public function get_contactosxidllamada($id_llamada)
    {
        try {
            /*  $sql = "SELECT * FROM contactos_con_nombre_comunicante WHERE id_llamada = ?"; */

            $sql = "
                SELECT 
                    c.id_contacto,
                    c.id_llamada,
                    c.id_metodo,
                    c.fecha_hora_contacto,
                    c.observaciones,
                    c.id_visita_cerrada,
                    (SELECT vc.fecha_visita_cerrada FROM visitas_cerradas vc WHERE vc.id_visita_cerrada = c.id_visita_cerrada) AS fecha_visita_cerrada,
                    c.estado,
                    (SELECT l.nombre_comunicante FROM llamadas l WHERE l.id_llamada = c.id_llamada) AS nombre_comunicante,
                    (SELECT l.domicilio_instalacion FROM llamadas l WHERE l.id_llamada = c.id_llamada) AS domicilio_instalacion,
                    (SELECT l.telefono_fijo FROM llamadas l WHERE l.id_llamada = c.id_llamada) AS telefono_fijo,
                    (SELECT l.telefono_movil FROM llamadas l WHERE l.id_llamada = c.id_llamada) AS telefono_movil,
                    (SELECT l.email_contacto FROM llamadas l WHERE l.id_llamada = c.id_llamada) AS email_contacto,
                    (SELECT l.fecha_hora_preferida FROM llamadas l WHERE l.id_llamada = c.id_llamada) AS fecha_hora_preferida,
                    (SELECT l.fecha_recepcion FROM llamadas l WHERE l.id_llamada = c.id_llamada) AS fecha_recepcion,
                    (SELECT l.id_comercial_asignado FROM llamadas l WHERE l.id_llamada = c.id_llamada) AS id_comercial_asignado,
                    (SELECT l.estado FROM llamadas l WHERE l.id_llamada = c.id_llamada) AS estado_llamada,
                    (SELECT l.activo_llamada FROM llamadas l WHERE l.id_llamada = c.id_llamada) AS activo_llamada,
                    (SELECT m.nombre FROM metodos_contacto m WHERE m.id_metodo = c.id_metodo) AS nombre_metodo,
                    (SELECT m.imagen_metodo FROM metodos_contacto m WHERE m.id_metodo = c.id_metodo) AS imagen_metodo,
                    (SELECT e.desc_estado FROM estados_llamada e WHERE e.id_estado = (SELECT l.estado FROM llamadas l WHERE l.id_llamada = c.id_llamada)) AS descripcion_estado_llamada,
                    (SELECT com.nombre FROM comerciales com WHERE com.id_comercial = (SELECT l.id_comercial_asignado FROM llamadas l WHERE l.id_llamada = c.id_llamada)) AS nombre_comercial,
                    IFNULL((SELECT GROUP_CONCAT(a.nombre_archivo SEPARATOR ',') FROM adjunto_llamada a WHERE a.id_llamada = c.id_llamada AND a.estado = 1), 'Sin archivos') AS archivos_adjuntos,
                    (SELECT (COUNT(0) > 0) FROM contactos cont WHERE cont.id_llamada = c.id_llamada) AS tiene_contactos,
                    ((SELECT l.estado FROM llamadas l WHERE l.id_llamada = c.id_llamada) = 3) AS estado_es_3,
                    (SELECT (COUNT(0) > 0) FROM adjunto_llamada a WHERE a.id_llamada = c.id_llamada AND a.estado = 1) AS tiene_adjuntos
                FROM contactos c
                WHERE c.id_llamada = $id_llamada
            ";

            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();

            // 🔹 Registrar consulta por ID de llamada
            $this->registro->registrarActividad(
                'admin',
                'Contactos',
                'Consulta por id_llamada',
                "Se consultaron contactos con id_llamada " . $id_llamada,
                'info'
            );

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {

            // Registrar el error
            $this->registro->registrarActividad(
                'admin',
                'Contactos',
                'Error',
                "Error al consultar contactos por id_llamada " . $id_llamada . " | " . $e->getMessage(),
                'error'
            );

            die("Error al consultar contactos con id_llamada {$id_llamada}: " . $e->getMessage());

            // En producción: log y return false;
            /* error_log("Error al obtener contactos por id_llamada: " . $e->getMessage());
            return false; */
        }
    }

    // NO HE MODIFICADO ESTA PARTE PORQUE CONTACTOS NO TIENE ESTADO
    public function delete_contactoxid($idcontacto)
    {
        try {
            $sql = "UPDATE contactos set estado=0 where id_contacto=?";
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->bindValue(1, $idcontacto, PDO::PARAM_INT);
            $stmt->execute();

        // 🔹 Registrar consulta de las vacaciones
        $this->registro->registrarActividad(
            'admin',
            'Contactos',
            'Desactivado el contacto',
            "Se desactivó el contacto con el id de contactos " . $idcontacto, 
            'info'
        );

            return $stmt->rowCount() > 0; // Retorna true si se eliminó al menos un usuario, false si no existía el ID.
        } catch (PDOException $e) {

            // ACCIÓN GUARDAR PARA EL ARCHIVO DE LOGS
            // Registrar el error en el log
            $this->registro->registrarActividad(
                'admin',
                'Contactos',
                'Error',
                "Error al desactivar el id de contactos " . $idcontacto . " | " . $e->getMessage(),
                'error'
            );

            // Esto para desarrollo
            die("Error al eliminar el contacto con id {$idcontacto}:" . $e->getMessage());
            return false;

            //En producción, se recomienda registrar el error en un archivo de logs y devolver false
            /*error_log("Error al obtener usuarios: " . $e->getMessage()); // Registrar error
            return false; // No detener el script, manejar el error en la llamada*/
            // El error se almacena en los logs de PHP o Apache (/var/log/apache2/error.log).
        }
    }

    // NO HE MODIFICADO ESTA PARTE PORQUE CONTACTOS NO TIENE ESTADO
    public function activar_contactoxid($idcontacto)
    {
        try {
            $sql = "UPDATE contactos set estado=1 where id_contacto=?";
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->bindValue(1, $idcontacto, PDO::PARAM_INT);
            $stmt->execute();

               // 🔹 Registrar consulta de las vacaciones
        $this->registro->registrarActividad(
            'admin',
            'Contactos',
            'Activada el contacto',
            "Se activó el contacto con el id de contactos " . $idcontacto, 
            'info'
        );

            // return $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC); // Devuelve el resultado de un solo usuario (fetch)
            return $stmt->rowCount() > 0; // Retorna true si se eliminó al menos un usuario, false si no existía el ID.
        } catch (PDOException $e) {

             // Registrar el error en el log
             $this->registro->registrarActividad(
                'admin',
                'Contactos',
                'Error',
                "Error al activar el id de contactos " . $idcontacto . " | " . $e->getMessage(),
                'error'
            );

            // Esto para desarrollo
            die("Error al activar el id de contacto {$idcontacto}:" . $e->getMessage());
            return false;

            //En producción, se recomienda registrar el error en un archivo de logs y devolver false
            /*error_log("Error al obtener usuarios: " . $e->getMessage()); // Registrar error
            return false; // No detener el script, manejar el error en la llamada*/
            // El error se almacena en los logs de PHP o Apache (/var/log/apache2/error.log).
        }
    }

   public function insert_contacto($id_llamada, $fechahoracontacto, $id_metodo, $observaciones, $id_visita_cerrada)
{
    try {
        $sql = "INSERT INTO contactos (id_llamada, fecha_hora_contacto, id_metodo, observaciones, id_visita_cerrada) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(1, $id_llamada, PDO::PARAM_INT);
        $stmt->bindValue(2, $fechahoracontacto, PDO::PARAM_STR);
        $stmt->bindValue(3, $id_metodo, PDO::PARAM_INT);
        $stmt->bindValue(4, $observaciones, PDO::PARAM_STR);
        
        if ($id_visita_cerrada === null) {
            $stmt->bindValue(5, null, PDO::PARAM_NULL);
        } else {
            $stmt->bindValue(5, $id_visita_cerrada, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        $idInsert = $this->conexion->lastInsertId();

        $this->registro->registrarActividad(
            'admin',
            'Contactos',
            'Insertado el contacto',
            "Se insertó el contacto con el id de contactos " . $idInsert,
            'info'
        );

        return $idInsert;
    } catch (PDOException $e) {
        $this->registro->registrarActividad(
            'admin',
            'Contactos',
            'Error',
            "Error al insertar el contacto: ". $e->getMessage(),
            'error'
        );
        die("Error al insertar el contacto: " . $e->getMessage());
    }
}

public function update_contacto($id_contacto, $id_llamada, $fechahoracontacto, $id_metodo, $observaciones, $id_visita_cerrada)
{
    try {
        $sql = "UPDATE contactos SET id_llamada = ?, fecha_hora_contacto = ?, id_metodo = ?, observaciones = ?, id_visita_cerrada = ? WHERE id_contacto = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(1, $id_llamada, PDO::PARAM_INT);
        $stmt->bindValue(2, $fechahoracontacto, PDO::PARAM_STR);
        $stmt->bindValue(3, $id_metodo, PDO::PARAM_INT);
        $stmt->bindValue(4, $observaciones, PDO::PARAM_STR);
        
        if ($id_visita_cerrada === null) {
            $stmt->bindValue(5, null, PDO::PARAM_NULL);
        } else {
            $stmt->bindValue(5, $id_visita_cerrada, PDO::PARAM_INT);
        }

        $stmt->bindValue(6, $id_contacto, PDO::PARAM_INT);
        $stmt->execute();

        $this->registro->registrarActividad(
            'admin',
            'Contactos',
            'Actualizado el contacto',
            "Se actualizó el contacto con el id de contactos " . $id_contacto,
            'info'
        );

        return true;
    } catch (PDOException $e) {
        $this->registro->registrarActividad(
            'admin',
            'Contactos',
            'Error',
            "Error al actualizar el contacto: ". $e->getMessage(),
            'error'
        );
        die("Error al hacer update al contacto: " . $e->getMessage());
    }
}

// AQUÍ COMPRUEBO LA FECHA VISITA CERRADA SI EXISTE EN EL CONTACTO, Y SI 
// SE ESTA EDITANDO, NO TENER EN CUENTA EL CONTACTO ACTUAL
    public function verificar_fecha_visita_cerrada($id_llamada, $id_contacto = null)
{
    try {
        // Construir la consulta base
        $sql = "SELECT * FROM visitas_cerradas WHERE id_llamada = ?";
        $params = [$id_llamada];

        // Si se está editando un contacto, excluir su propio ID
        if (!empty($id_contacto)) {
            $sql .= " AND id_contacto != ?";
            $params[] = $id_contacto;
        }

        $stmt = $this->conexion->prepare($sql); // Conexión segura
        $stmt->execute($params);

        $existe = $stmt->fetch(PDO::FETCH_ASSOC);

        // Registrar actividad
        $this->registro->registrarActividad(
            'admin',
            'Contactos',
            'Verificación',
            "Se verificó si la llamada con ID $id_llamada tiene una visita cerrada (excluyendo contacto ID $id_contacto)",
            'info'
        );

        return ['existe' => $existe ? true : false];

    } catch (PDOException $e) {
        $this->registro->registrarActividad(
            'admin',
            'Contactos',
            'Error',
            "Error al verificar visita cerrada para llamada ID $id_llamada: " . $e->getMessage(),
            'error'
        );
        die("Error al verificar la visita cerrada: " . $e->getMessage());
    }
}

// DESDE AQUÍ HAGO LA GESTIÓN DE EL GUARDAR O ACTUALIZAR LA FECHA VISITA CERRADA
public function guardar_o_actualizar_visita_cerrada($id_contacto, $id_llamada, $fecha_visita)
{
    try {
        // Buscar visita existente para ese contacto y esa llamada
        $sql = "SELECT id_visita_cerrada FROM visitas_cerradas WHERE id_contacto = ? AND id_llamada = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([$id_contacto, $id_llamada]);
        $visitaExistente = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($visitaExistente) {
            // Actualizar si ya existe
            $sqlUpdate = "UPDATE visitas_cerradas SET fecha_visita_cerrada = ? WHERE id_visita_cerrada = ?";
            $stmtUpdate = $this->conexion->prepare($sqlUpdate);
            $stmtUpdate->execute([$fecha_visita, $visitaExistente['id_visita_cerrada']]);

            return $visitaExistente['id_visita_cerrada'];
        } else {
            // Insertar si no existe
            $sqlInsert = "INSERT INTO visitas_cerradas (id_contacto, id_llamada, fecha_visita_cerrada) VALUES (?, ?, ?)";
            $stmtInsert = $this->conexion->prepare($sqlInsert);
            $stmtInsert->execute([$id_contacto, $id_llamada, $fecha_visita]);

            return $this->conexion->lastInsertId();
        }

    } catch (PDOException $e) {
        $this->registro->registrarActividad(
            'admin',
            'Visitas Cerradas',
            'Error',
            "Error al guardar o actualizar visita cerrada: " . $e->getMessage(),
            'error'
        );
        throw $e;
    }
}

// SIMPLEMENTE RETORNA SI TIENE LA FECHA DE VISITA CERRADA
public function tieneVisitaCerrada($id_contacto)
{
    try {
        $sql = "SELECT id_visita_cerrada FROM contactos WHERE id_contacto = ? AND id_visita_cerrada IS NOT NULL";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([$id_contacto]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result !== false;
    } catch (PDOException $e) {
        $this->registro->registrarActividad(
            'admin',
            'Contactos',
            'Error',
            "Error en tieneVisitaCerrada: " . $e->getMessage(),
            'error'
        );
        return false; // En caso de error, no bloquea el proceso (pero podrías hacer distinto)
    }
}

// COMPROBAR SI LA LLAMADA TIENE ALGÚN CONTACTO
public function tieneContactos($id_llamada)
{
    try {
        $sql = "SELECT 1 FROM contactos WHERE id_llamada = ? LIMIT 1";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([$id_llamada]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result !== false;
    } catch (PDOException $e) {
        $this->registro->registrarActividad(
            'admin',
            'Contactos',
            'Error',
            "Error en tieneContactos: " . $e->getMessage(),
            'error'
        );
        return false;
    }
}

// SE UTILIZA PARA DAR UN ESTADO O OTRO A LA LLAMADA, SE USA EN EL MÉTODO DE OP DE GUARDAR
// EN CONTACTOS, CADA VEZ QUE SE MODIFICA UN ESTADO LA LLAMADA VA A TENER QUE CAMBIAR
// DE UN ESTADO A OTRO
public function actualizarEstadoLlamada($id_llamada)
{
    try {
        if ($this->verificar_fecha_visita_cerrada($id_llamada)['existe']) {
            $estado = 3;  // ID para "Cita Cerrada"
        } elseif ($this->tieneContactos($id_llamada)) {
            $estado = 2;  // ID para "Con contacto"
        } else {
            $estado = 1;  // ID para "Recibida sin atención" o estado por defecto
        }

        $sql = "UPDATE llamadas SET estado = ? WHERE id_llamada = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([$estado, $id_llamada]);

        $this->registro->registrarActividad(
            'admin',
            'Llamadas',
            'Actualización',
            "Estado de llamada ID $id_llamada actualizado a ID estado $estado",
            'info'
        );

    } catch (PDOException $e) {
        $this->registro->registrarActividad(
            'admin',
            'Llamadas',
            'Error',
            "Error actualizando estado llamada ID $id_llamada: " . $e->getMessage(),
            'error'
        );
        throw $e;
    }
}


}
