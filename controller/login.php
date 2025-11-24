<?php
require_once "../config/conexion.php";
// require_once "../config/funciones.php";
require_once "../models/Login.php";
require_once '../config/funciones.php'; // ✅ Se incluye correctamente el archivo de conexión

require '../public/lib/PHPMailer/src/Exception.php';
require '../public/lib/PHPMailer/src/PHPMailer.php';
require '../public/lib/PHPMailer/src/SMTP.php';

//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
//Create an instance; passing `true` enables exceptions
$mail = new PHPMailer(true);

// Función para escribir en el log - Desarrollo
function writeToLog($logData)
{
    $logFile = "../public/logs/log_" . date("Ymd") . ".txt"; // Nombre del archivo de log
    $logMessage = "[" . date("Y-m-d H:i:s") . "] " . json_encode($logData, JSON_UNESCAPED_UNICODE) . "\n";
    file_put_contents($logFile, $logMessage, FILE_APPEND);
}

$registro = new RegistroActividad(); // ✅ Se crea una instancia de la clase RegistroActividad
$login = new Login();


switch ($_GET["op"]) {

        case "listar":
            $datos = $login->get_usuario();
            $data = array();
            foreach ($datos as $row) {
                $data[] = array(
                    "id_usuario" => $row["id_usuario"],
                    "email" => $row["email"],
                    "contrasena" => $row["contrasena"],
                    "nombre" => $row["nombre"],
                    "fecha_crea" => $row["fecha_crea"],
                    "est" => $row["est"],
                    "id_rol" => $row["id_rol"],
                    "tokenUsu" => $row["tokenUsu"],          // Añadido tokenUsu aquí
                    "nombre_rol" => $row["nombre_rol"],
                );
            }

            $results = array(
                "draw" => 1,
                "recordsTotal" => count($data),
                "recordsFiltered" => count($data),
                "data" => $data
            );

            header('Content-Type: application/json');
            echo json_encode($results, JSON_UNESCAPED_UNICODE);
            break;

            case "listarComercialesDisponibles":
            $datos = $login->get_usuario_comercial_disponible();
            $data = array();
            foreach ($datos as $row) {
                $data[] = array(
                    "id_usuario" => $row["id_usuario"],
                    "email" => $row["email"],
                    "contrasena" => $row["contrasena"],
                    "nombre" => $row["nombre"],
                    "fecha_crea" => $row["fecha_crea"],
                    "est" => $row["est"],
                    "id_rol" => $row["id_rol"],
                    "tokenUsu" => $row["tokenUsu"],          // Añadido tokenUsu aquí
                    "nombre_rol" => $row["nombre_rol"],
                );
            }

            $results = array(
                "draw" => 1,
                "recordsTotal" => count($data),
                "recordsFiltered" => count($data),
                "data" => $data
            );

            header('Content-Type: application/json');
            echo json_encode($results, JSON_UNESCAPED_UNICODE);
            break;

            case "listarUsuariosConSeleccionado":
                $id_usuario = isset($_GET['id_usuario']) ? intval($_GET['id_usuario']) : 0;
                $datos = $login->get_usuario_comercial_por_id($id_usuario);
                $data = array();
                if ($datos) {
                    foreach ($datos as $row) {
                        $data[] = array(
                            "id_usuario" => $row["id_usuario"],
                            "email" => $row["email"],
                            "contrasena" => $row["contrasena"],
                            "nombre" => $row["nombre"],
                            "fecha_crea" => $row["fecha_crea"],
                            "est" => $row["est"],
                            "id_rol" => $row["id_rol"],
                            "tokenUsu" => $row["tokenUsu"],
                            "nombre_rol" => $row["nombre_rol"],
                        );
                    }
                }
                $results = array(
                    "draw" => 1,
                    "recordsTotal" => count($data),
                    "recordsFiltered" => count($data),
                    "data" => $data
                );
                header('Content-Type: application/json');
                echo json_encode($results, JSON_UNESCAPED_UNICODE);
            break;


       case "guardaryeditar":
    session_start();

    // Recoger datos
    $id_usuario = $_POST["id_usuario"] ?? null;
    $email = $_POST["email"] ?? '';
    $contrasena = $_POST["contrasena"] ?? '';
    $nombre = $_POST["nombre"] ?? '';
    $id_rol = $_POST["id_rol"] ?? '';

    // Validación de campos requeridos (excepto contraseña en edición)
    $campos = ['email' => $email, 'nombre' => $nombre, 'id_rol' => $id_rol];
    $errores = [];

    foreach ($campos as $nombreCampo => $valorCampo) {
        if (empty($valorCampo)) {
            $errores[] = "El campo {$nombreCampo} es obligatorio.";
        }
    }

    // Solo validar contraseña si es nuevo usuario o se está cambiando
    if (empty($id_usuario)) {
        if (empty($contrasena)) {
            $errores[] = "El campo contrasena es obligatorio para nuevos usuarios.";
        }
    }

    if (!empty($errores)) {
        $registro->registrarActividad(
            'sistema',
            'login.php',
            'guardaryeditar',
            'Errores de validación: ' . implode(', ', $errores),
            'error'
        );
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => implode(', ', $errores)
        ]);
        exit;
    }

    if (empty($id_usuario)) {
        // --- NUEVO REGISTRO ---
        if ($login->existeCorreoUsuario($email)) {
            $registro->registrarActividad(
                'sistema',
                'login.php',
                'guardaryeditar',
                "Correo duplicado al registrar: $email",
                'error'
            );
            http_response_code(409);
            echo json_encode([
                'success' => false,
                'message' => 'El correo ya está registrado.'
            ]);
            exit;
        }

        // Generar token de 30 caracteres
        $token = $registro->generarToken(30);

        // Insertar nuevo usuario con token
        $resultado = $login->insert_usuario($email, md5($contrasena), $nombre, $id_rol, $token);

        if ($resultado !== false) {
            $registro->registrarActividad(
                'sistema',
                'login.php',
                'guardaryeditar',
                "Usuario creado: $email con token $token",
                'info'
            );
            echo json_encode([
                'success' => true,
                'message' => 'Usuario registrado correctamente.',
                'userId' => $resultado,
                'token' => $token
            ]);
        } else {
            $registro->registrarActividad(
                'sistema',
                'login.php',
                'guardaryeditar',
                "Error al registrar el usuario: $email",
                'error'
            );
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'No se pudo registrar el usuario.'
            ]);
        }

    } else {
        // --- EDICIÓN DE USUARIO EXISTENTE ---
        if ($login->existeCorreoUsuarioEditando($email, $id_usuario)) {
            $registro->registrarActividad(
                'sistema',
                'login.php',
                'guardaryeditar',
                "Correo duplicado al editar: $email",
                'error'
            );
            http_response_code(409);
            echo json_encode([
                'success' => false,
                'message' => 'El correo ya está en uso por otro usuario.'
            ]);
            exit;
        }

        // Actualizar usuario (con o sin contraseña)
        if (empty($contrasena)) {
            // Actualizar sin cambiar la contraseña
            $resultado = $login->update_usuario($id_usuario, $email, null, $nombre, $id_rol);
        } else {
            // Actualizar con nueva contraseña
            $resultado = $login->update_usuario($id_usuario, $email, md5($contrasena), $nombre, $id_rol);
        }

        if ($resultado) {
            $registro->registrarActividad(
                'sistema',
                'login.php',
                'guardaryeditar',
                "Usuario actualizado: $id_usuario",
                'info'
            );
            echo json_encode([
                'success' => true,
                'message' => 'Usuario actualizado correctamente.'
            ]);
        } else {
            $registro->registrarActividad(
                'sistema',
                'login.php',
                'guardaryeditar',
                "Error al actualizar el usuario: $id_usuario",
                'error'
            );
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'No se pudo actualizar el usuario.'
            ]);
        }
    }

    break;

        

    case "mostrar":
        $datos = $login->get_usuarioxid($_POST["id_usuario"]);
        header('Content-Type: application/json');
        echo json_encode($datos, JSON_UNESCAPED_UNICODE);
        break;

    case "eliminar":
        $login->delete_usuarioxid($_POST["id_usuario"]);
        break;

    case "activar":
        $login->activar_usuarioxid($_POST["id_usuario"]);
        break;

case "iniciarSesion":
    session_start();

    // Verificar si los campos de email y contraseña están presentes
    if (empty($_POST['email']) || empty($_POST['contrasena'])) {
        $registro->registrarActividad(
            'sistema',
            'login.php',
            'iniciarSesion',
            'Faltan campos: email o contraseña',
            'error'
        );
        http_response_code(400); // Bad Request
        echo json_encode([
            'success' => false,
            'message' => 'Faltan email o contraseña'
        ]);
        exit;
    }

    $email = $_POST['email'];
    $password = md5($_POST['contrasena']); // ⚠️ Considera cambiar a password_hash a futuro

    // Verificar el usuario en la base de datos
    $user = $login->verificarUsuario($email, $password);

    if ($user) {
        // Verificar si la cuenta está activa
        if ($user['est'] == 0) {
            $registro->registrarActividad(
                'sistema',
                'login.php',
                'iniciarSesion',
                'Cuenta inhabilitada',
                'error'
            );
            http_response_code(403); // Forbidden
            echo json_encode([
                'success' => false,
                'message' => 'Cuenta inhabilitada'
            ]);
            exit;
        }

        // Guardar datos en la sesión, incluyendo id_comercial
        $_SESSION = [
            'id_usuario' => $user['id_usuario'],
            'email' => $user['email'],
            'nombre' => $user['nombre'],
            'fecha_crea' => $user['fecha_crea'],
            'est' => $user['est'],
            'id_rol' => $user['id_rol'],
            'tokenUsu' => $user['tokenUsu'],
            'nombre_rol' => $user['nombre_rol'],
            'id_comercial' => $user['id_comercial'],  // Aquí el id_comercial
            'sesion_iniciada' => true
        ];

        // Registrar actividad exitosa
        $registro->registrarActividad(
            $user['email'],
            'login.php',
            'iniciarSesion',
            'Inicio de sesión exitoso',
            'info'
        );

        echo json_encode([
            'success' => true,
            'message' => 'Inicio de sesión correcto.',
            'redireccion' => 'Inicio.php',
            'datos_usuario' => $_SESSION
        ]);
    } else {
        // Verificar si el usuario existe
        $usuarioExiste = $login->existeCorreoUsuario($email);

        if (!$usuarioExiste) {
            $registro->registrarActividad(
                'sistema',
                'login.php',
                'iniciarSesion',
                'Usuario no encontrado',
                'error'
            );
            http_response_code(404); // Not Found
            echo json_encode([
                'success' => false,
                'message' => 'Usuario no encontrado'
            ]);
        } else {
            $registro->registrarActividad(
                $email,
                'login.php',
                'iniciarSesion',
                'Contraseña incorrecta',
                'error'
            );
            http_response_code(401); // Unauthorized
            echo json_encode([
                'success' => false,
                'message' => 'Contraseña incorrecta'
            ]);
        }

        exit;
    }
break;


        case "cerrarSesion":
            session_start();
        
            // Guardamos el email si existiera en sesión para el log
            $email = $_SESSION['email'] ?? 'usuario desconocido';
        
            // Limpiar y destruir la sesión
            session_unset();
            session_destroy();
        
            // Mensaje por defecto de cierre
            $mensaje = 'Sesión cerrada correctamente.';
        
            // Registrar la actividad
            $registro->registrarActividad(
                $email,
                'login.php',
                'cerrarSesion',
                $mensaje,
                'info'
            );
        
            // Devolver respuesta JSON para el frontend
            echo json_encode([
                'success' => true,
                'message' => $mensaje
            ]);
            exit;
        
            break;

            case "comprobarCorreo":
        $correoUsu = strtolower(trim($_POST['usu_correo'])); // Poner en minuscula

        $datosUsu = $login->get_usuario_x_usu(strtolower(trim($correoUsu)));
        if (is_array($datosUsu) == true and count($datosUsu) > 0) {  // si es mayor que cero es que ya existe uno y debo sacar error
            echo 1;
        };
        break;

        case "getTokenByCorreo":
    try {
        $correo = $_POST["correoUsu"];

        // Log: Correo recibido
        $json_string = json_encode(["correo_recibido" => $correo, "timestamp" => date("Y-m-d H:i:s")]);
        $file = 'log_token_correo_recibido.json';
        file_put_contents($file, $json_string);

        // Consulta a base de datos
        $datosUsuario = $login->get_token_x_correo($correo);

        // Log: Resultado de la query
        $json_string = json_encode(["resultado_query" => $datosUsuario, "timestamp" => date("Y-m-d H:i:s")]);
        $file = 'log_token_resultado_query.json';
        file_put_contents($file, $json_string);

        // Mostrar resultado
        echo json_encode($datosUsuario);

    } catch (Exception $e) {
        // Log: Error ocurrido
        $json_string = json_encode(["error" => $e->getMessage(), "timestamp" => date("Y-m-d H:i:s")]);
        $file = 'log_token_error.json';
        file_put_contents($file, $json_string);
    }
    break;


        break;

        case "correoValidarCambioPass":

        $correo = $_POST['usu_correo'];
        $numeroAleatorio = $_POST['numeroAleatorio'];

        $dominio_actual = $_SERVER["SERVER_NAME"];

        // IMAGEN LOGOTIPO CORREO
        $img = 'https://' . $dominio_actual . '/public/img/logo_pequeno.png';

        $nombreSoftware = 'EFEUNO DEV';

        try {

            // Archivo de configuración Mail //
            //include 'configMail.php';
            //Server settings
                  //Server settings
                  $mail->isSMTP();
                  $mail->Host = 'innovabyte.es'; // Servidor SMTP
                  $mail->SMTPAuth = true;
                  $mail->Username = 'luiscarlos@innovabyte.es'; // Usuario SMTP
                  $mail->Password = '27979699$C'; // Contraseña SMTP
                  $mail->SMTPSecure = 'ssl'; // Seguridad SSL
                  $mail->Port = 465; // Puerto SMTP
      
                  
                  //Recipients
                  $mail->setFrom('luiscarlos@innovabyte.es', 'Administracion');

            $mail->addAddress($correo, '');     //Add a recipient

            $mail->CharSet = 'UTF-8'; // Establecer la codificación del correo

            $mail->Subject = 'Validar correo ';

            $cuerpo = file_get_contents("../public/mailTemplate/correoValidarCambioPass.html"); /* Ruta del template en formato HTML */
            /* parametros del template a remplazar */
            $cuerpo = str_replace("00000", $numeroAleatorio, $cuerpo);
            $cuerpo = str_replace("logotipo", $img, $cuerpo);

            $cuerpo = str_replace("NAMESOFTWARE", $nombreSoftware, $cuerpo);


            $mail->Body    = $cuerpo;
            $mail->AltBody = "Validar correo";

            $mail->send();

            echo 1;
        } catch (Exception $e) {


            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }

        break;

        case "cambiarPassword":
        $id = $_POST['idUsu'];
        $password = trim($_POST['password']);

        $datosUsu = $login->update_password($id, $password);

        echo json_encode($datosUsu);
        break;

        case "verificarCorreo":
    try {
        // Obtener los parámetros
        $email = $_POST["email"];
        $id_usuario = isset($_POST["id_usuario"]) ? trim($_POST["id_usuario"]) : null;

        // Logging de entrada
        $log_entrada = [
            "timestamp" => date("Y-m-d H:i:s"),
            "input" => [
                "email" => $email,
                "id_usuario" => $id_usuario
            ]
        ];
        file_put_contents("log_verificar_usuario_input.json", json_encode($log_entrada));

        // Validación mínima
        if (empty($email)) {
            echo json_encode([
                "success" => false,
                "message" => "El correo electrónico es obligatorio."
            ]);
            exit;
        }

        $existe = $id_usuario
            ? $login->existeCorreoUsuarioEditando($email, $id_usuario)
            : $login->existeCorreoUsuario($email);

        $log_resultado = [
            "timestamp" => date("Y-m-d H:i:s"),
            "existe" => $existe
        ];
        file_put_contents("log_verificar_usuario_resultado.json", json_encode($log_resultado));

        echo json_encode([
            "success" => true,
            "existe" => $existe
        ]);

    } catch (Exception $e) {
        $log_error = [
            "timestamp" => date("Y-m-d H:i:s"),
            "error" => $e->getMessage()
        ];
        file_put_contents("log_verificar_usuario_error.json", json_encode($log_error));

        echo json_encode([
            "success" => false,
            "message" => "Ocurrió un error al verificar el correo."
        ]);
    }
    break;



        
}
