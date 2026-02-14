<?php

require_once '../config/conexion.php'; // ✅ Se incluye correctamente el archivo de conexión
require_once '../config/funciones.php'; // ✅ Se incluye correctamente el archivo de conexión

class Login
{

    private $conexion;
    private $registro; // ✅ Se declara la instancia para el objeto RegistroActividad 

    public function __construct()
    {
        $this->conexion = (new Conexion())->getConexion(); // ✅ Ahora obtiene correctamente la conexión
        $this->registro = new RegistroActividad(); // ✅ Se asigna el objeto RegistroActividad a la propiedad de la clase para los logs.
    }

    public function get_usuario()
    {
        try {
            $sql = "SELECT u.*, r.nombre_rol FROM usuarios u LEFT JOIN roles r ON u.id_rol = r.id_rol";
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->execute();

            return $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC); // Devuelve todos los usuarios
        } catch (PDOException $e) {
            // Esto para desarrollo
            //die("Error al mostrar los productos: " . $e->getMessage());

            $this->registro->registrarActividad(
                'admin',
                'Login',
                'get_usuario',
                "Error al listar los usuarios: " . $e->getMessage(),
                "error"
            );


            //En producción, se recomienda registrar el error en un archivo de logs y devolver false
            /*error_log("Error al obtener usuarios: " . $e->getMessage()); // Registrar error
            return false; // No detener el script, manejar el error en la llamada*/
            // El error se almacena en los logs de PHP o Apache (/var/log/apache2/error.log).
        }
    }

     public function get_usuario_comercial_disponible()
    {
        try {
            // Mostrar TODOS los usuarios activos que NO tienen empleado/comercial asignado
            // Sin restricción de rol - cualquier usuario puede ser empleado
            $sql = "SELECT u.*, r.nombre_rol
                    FROM usuarios u
                    JOIN roles r ON u.id_rol = r.id_rol
                    WHERE u.est = 1
                    AND NOT EXISTS (
                        SELECT 1 FROM comerciales c 
                        WHERE c.id_usuario = u.id_usuario 
                        AND c.activo = 1
                    )
                    ORDER BY u.nombre ASC;";
            
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->execute();

            return $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC); // Devuelve todos los usuarios
        } catch (PDOException $e) {
            // Esto para desarrollo
            //die("Error al mostrar los productos: " . $e->getMessage());

            $this->registro->registrarActividad(
                'admin',
                'Login',
                'get_usuario',
                "Error al listar los usuarios: " . $e->getMessage(),
                "error"
            );


            //En producción, se recomienda registrar el error en un archivo de logs y devolver false
            /*error_log("Error al obtener usuarios: " . $e->getMessage()); // Registrar error
            return false; // No detener el script, manejar el error en la llamada*/
            // El error se almacena en los logs de PHP o Apache (/var/log/apache2/error.log).
        }
    }

    public function get_usuario_comercial_por_id($id_usuario)
    {
        try {
            $sql = "SELECT u.*, r.nombre_rol
                    FROM usuarios u
                    JOIN roles r ON u.id_rol = r.id_rol
                    WHERE u.id_usuario = ?"; 

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_usuario, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Login',
                'get_usuario_por_id',
                "Error al obtener usuario por ID: " . $e->getMessage(),
                "error"
            );
            return false;
        }
    }


    public function get_usuarioxid($usu_id)
    {
        try {
            $sql = "SELECT u.*, r.nombre_rol 
                    FROM usuarios u 
                    LEFT JOIN roles r ON u.id_rol = r.id_rol
                    WHERE u.id_usuario = ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $usu_id, PDO::PARAM_INT);
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado;
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Usuarios',
                'get_usuarioxid',
                "Error al mostrar el usuario {$usu_id}: " . $e->getMessage(),
                "error"
            );
            return false;
        }
    }

    public function delete_usuarioxid($usu_id)
    {
        try {
            $sql = "UPDATE usuarios set est=0 where id_usuario=?";
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->bindValue(1, $usu_id, PDO::PARAM_INT);
            $stmt->execute();


            // Para los logs
            $this->registro->registrarActividad(
                'admin',
                'Usuarios',
                'Desactivar',
                "Se desactivó el usuario con ID: $usu_id",
                'info'
            );
            return $stmt->rowCount() > 0; // Retorna true si se eliminó al menos un usuario, false si no existía el ID.
        } catch (PDOException $e) {
            // Esto para desarrollo
            //die("Error al mostrar el producto {$prod_id}:" . $e->getMessage());

            // Esto para producción
            $this->registro->registrarActividad(
                'admin',
                'Usuarios',
                'delete_usuarioxid',
                "Error al desactivar el usuario {$usu_id}: " . $e->getMessage(),
                'error'
            );


            return false;

            //En producción, se recomienda registrar el error en un archivo de logs y devolver false
            /*error_log("Error al obtener usuarios: " . $e->getMessage()); // Registrar error
            return false; // No detener el script, manejar el error en la llamada*/
            // El error se almacena en los logs de PHP o Apache (/var/log/apache2/error.log).
        }
    }

    public function activar_usuarioxid($usu_id)
    {
        try {
            $sql = "UPDATE usuarios set est=1 where id_usuario=?";
            $stmt = $this->conexion->prepare($sql); // Se accede a la conexión correcta
            $stmt->bindValue(1, $usu_id, PDO::PARAM_INT);
            $stmt->execute();

            // Para generar los logs
            $this->registro->registrarActividad(
                'admin',
                'Usuarios',
                'Activar',
                "Se activo el usuario con ID: $usu_id",
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
                'Usuarios',
                'activar_usuarioxid',
                "Error al activar el usuario {$usu_id}: " . $e->getMessage(),
                "error"
            );

            return false;

            //En producción, se recomienda registrar el error en un archivo de logs y devolver false
            /*error_log("Error al obtener usuarios: " . $e->getMessage()); // Registrar error
            return false; // No detener el script, manejar el error en la llamada*/
            // El error se almacena en los logs de PHP o Apache (/var/log/apache2/error.log).
        }
    }

    public function insert_usuario($email, $contrasena, $nombre, $id_rol, $tokenUsu)
    {
        try {
            $sql = "INSERT INTO usuarios (email, contrasena, nombre, est, fecha_crea, id_rol, tokenUsu) 
                    VALUES (?, ?, ?, 1, NOW(), ?, ?)";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $email, PDO::PARAM_STR);
            $stmt->bindValue(2, $contrasena, PDO::PARAM_STR);
            $stmt->bindValue(3, $nombre, PDO::PARAM_STR);
            $stmt->bindValue(4, $id_rol, PDO::PARAM_INT);
            $stmt->bindValue(5, $tokenUsu, PDO::PARAM_STR);
            $stmt->execute();
            $idInsert = $this->conexion->lastInsertId();

            // Para generar los logs
            $this->registro->registrarActividad(
                'admin',
                'Usuarios',
                'Insertar',
                "Se inserto el usuario con ID: $idInsert y token: $tokenUsu",
                'info'
            );

            return $idInsert;
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Usuarios',
                'insert_usuario',
                "Error al insertar el usuario: " . $e->getMessage(),
                'error'
            );
            return false;
        }
    }


    
    public function update_usuario($usu_id, $email, $contrasena, $nombre, $id_rol) {
        try {
            // Construir la consulta SQL condicional
            if (empty($contrasena)) {
                $sql = "UPDATE usuarios SET email = ?, nombre = ?, id_rol = ? WHERE id_usuario = ?";
                $stmt = $this->conexion->prepare($sql);
                $stmt->bindValue(1, $email, PDO::PARAM_STR);
                $stmt->bindValue(2, $nombre, PDO::PARAM_STR);
                $stmt->bindValue(3, $id_rol, PDO::PARAM_INT);
                $stmt->bindValue(4, $usu_id, PDO::PARAM_INT);
            } else {
                $sql = "UPDATE usuarios SET email = ?, contrasena = ?, nombre = ?, id_rol = ? WHERE id_usuario = ?";
                $stmt = $this->conexion->prepare($sql);
                $stmt->bindValue(1, $email, PDO::PARAM_STR);
                $stmt->bindValue(2, $contrasena, PDO::PARAM_STR);
                $stmt->bindValue(3, $nombre, PDO::PARAM_STR);
                $stmt->bindValue(4, $id_rol, PDO::PARAM_INT);
                $stmt->bindValue(5, $usu_id, PDO::PARAM_INT);
            }
    
            $stmt->execute();
    
            // Registro de actividad
            $this->registro->registrarActividad(
                'admin',
                'Usuarios',
                'Actualizar',
                "Se actualizó el usuario con ID: $usu_id" . 
                (empty($contrasena) ? " (sin cambiar contraseña)" : " (con cambio de contraseña)"),
                'info'
            );
    
            return true;
    
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Usuarios',
                'update_usuario',
                "Error al actualizar el usuario:" . $e->getMessage(),
                'error'
            );
            return false;
        }
    }
    
public function verificarUsuario($email, $contrasena)
{
    try {
        $sql = "SELECT usuarios.*, roles.nombre_rol, comerciales.id_comercial 
                FROM usuarios 
                INNER JOIN roles ON usuarios.id_rol = roles.id_rol
                LEFT JOIN comerciales ON comerciales.id_usuario = usuarios.id_usuario
                WHERE usuarios.email = ? AND usuarios.contrasena = ?";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(1, $email, PDO::PARAM_STR);
        $stmt->bindValue(2, $contrasena, PDO::PARAM_STR);
        $stmt->execute();
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario) {
            $this->registro->registrarActividad(
                $usuario['email'],
                'Login.php',
                'verificarUsuario',
                "Inicio de sesión exitoso",
                'info'
            );
        }

        return $usuario;
    } catch (PDOException $e) {
        $this->registro->registrarActividad(
            $email,
            'Login.php',
            'verificarUsuario',
            "Error al verificar credenciales: " . $e->getMessage(),
            'error'
        );
        return false;
    }
}


public function existeCorreoUsuario($email)
{
    try {
        $sql = "SELECT COUNT(*) as total FROM usuarios WHERE email = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(1, $email, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result['total'] > 0;
    } catch (PDOException $e) {
        $this->registro->registrarActividad(
            $email,
            'Login.php',
            'existeCorreoUsuario',
            "Error al comprobar existencia del correo del usuario: " . $e->getMessage(),
            'error'
        );
        return false;
    }
}

public function existeCorreoUsuarioEditando($email, $id_usuario)
{
    try {
        $sql = "SELECT COUNT(*) as total FROM usuarios WHERE email = ? AND id_usuario != ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(1, $email, PDO::PARAM_STR);
        $stmt->bindValue(2, $id_usuario, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result['total'] > 0;
    } catch (PDOException $e) {
        $this->registro->registrarActividad(
            'sistema',
            'Login.php',
            'existeCorreoUsuarioEditando',
            "Error al comprobar correo en edición del usuario ID {$id_usuario}: " . $e->getMessage(),
            'error'
        );
        return false;
    }
}

  // Se utiliza para comprobar si se puede dar de alta.
    public function get_usuario_x_usu($correoUsu)
{
    try {
        $sql = "SELECT * FROM usuarios WHERE TRIM(email) = TRIM(?)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(1, $correoUsu, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        $this->registro->registrarActividad(
            'sistema',
            'Login.php',
            'get_usuario_x_usu',
            "Error al obtener usuario por correo: " . $e->getMessage(),
            'error'
        );
        return false;
    }
}


    
    public function get_token_x_correo($correoUsu)
{
    try {
        $sql = "SELECT tokenUsu FROM usuarios WHERE email = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(1, $correoUsu, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        $this->registro->registrarActividad(
            'sistema',
            'Login.php',
            'get_token_x_correo',
            "Error al obtener token por correo: " . $e->getMessage(),
            'error'
        );
        return false;
    }
}


    public function update_password($id, $password)
{
    try {
        $sql = "UPDATE usuarios SET contrasena = MD5(?) WHERE tokenUsu = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(1, $password, PDO::PARAM_STR);
        $stmt->bindValue(2, $id, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->rowCount(); // Devuelve cuántas filas se actualizaron
    } catch (PDOException $e) {
        $this->registro->registrarActividad(
            'sistema',
            'Login.php',
            'update_password',
            "Error al actualizar la contraseña del token $id: " . $e->getMessage(),
            'error'
        );
        return false;
    }
}



}
