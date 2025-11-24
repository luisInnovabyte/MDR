<?php
require_once "../config/conexion.php";
// require_once "../config/funciones.php";
require_once "../models/Contactos.php";
require_once "../models/Llamadas.php";

require_once '../config/funciones.php'; // ✅ Se incluye correctamente el archivo de conexión

$registro = new RegistroActividad(); // ✅ Se crea una instancia de la clase RegistroActividad
$contacto = new Contactos();
$llamada = new Llamadas();


// Función para escribir en el log - Desarrollo
function writeToLog($logData)
{
    $logFile = "../public/logs/log_" . date("Ymd") . ".txt"; // Nombre del archivo de log
    $logMessage = "[" . date("Y-m-d H:i:s") . "] " . json_encode($logData, JSON_UNESCAPED_UNICODE) . "\n";
    file_put_contents($logFile, $logMessage, FILE_APPEND);
}


switch ($_GET["op"]) {

    case "listar":
    try {
        $datos = $contacto->get_contacto();
        $data = array();

        foreach ($datos as $row) {
            $data[] = array(
                "id_contacto" => $row["id_contacto"],
                "id_llamada" => $row["id_llamada"],
                "fecha_hora_contacto" => $row["fecha_hora_contacto"],
                "tipo_contacto" => $row["tipo_contacto"],
                "observaciones" => $row["observaciones"],
                "id_visita_cerrada" => $row["id_visita_cerrada"],
                "fecha_visita_cerrada" => $row["fecha_visita_cerrada"],
                "nombre_comunicante" => $row["nombre_comunicante"],
                "estado" => $row["estado"],
                "domicilio_instalacion" => $row["domicilio_instalacion"],
                "telefono_fijo" => $row["telefono_fijo"],
                "telefono_movil" => $row["telefono_movil"],
                "email_contacto" => $row["email_contacto"],
                "fecha_hora_preferida" => $row["fecha_hora_preferida"],
                "fecha_recepcion" => $row["fecha_recepcion"],
                "id_metodo" => $row["id_metodo"],
                "id_comercial_asignado" => $row["id_comercial_asignado"],
                "estado_llamada" => $row["estado_llamada"],
                "activo_llamada" => $row["activo_llamada"],
                "nombre_metodo" => $row["nombre_metodo"],
                "imagen_metodo" => $row["imagen_metodo"],
                "descripcion_estado_llamada" => $row["descripcion_estado_llamada"],
                "nombre_comercial" => $row["nombre_comercial"],
                "archivos_adjuntos" => $row["archivos_adjuntos"],
                "tiene_contactos" => $row["tiene_contactos"],
                "estado_es_3" => $row["estado_es_3"],
                "tiene_adjuntos" => $row["tiene_adjuntos"]
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
    } catch (Exception $e) {
        // Crear archivo .json con el error
        $error_array = array(
            "error" => "Error al listar contactos",
            "mensaje" => $e->getMessage()
        );
        $json_string = json_encode($error_array, JSON_UNESCAPED_UNICODE);
        $file = 'error_log.json'; // Puedes poner otro nombre
        file_put_contents($file, $json_string);

        // Devolver error como JSON al cliente
        header('Content-Type: application/json');
        http_response_code(500);
        echo $json_string;
    }
    break;

    case "listarPorComercial":
    try {
        $idComercial = isset($_POST['id_comercial']) ? $_POST['id_comercial'] : null;
        if (!$idComercial) {
            throw new Exception("Falta id_comercial");
        }
        $datos = $contacto->get_contacto_por_comercial($idComercial);
        $data = array();

        foreach ($datos as $row) {
            $data[] = array(
                "id_contacto" => $row["id_contacto"],
                "id_llamada" => $row["id_llamada"],
                "fecha_hora_contacto" => $row["fecha_hora_contacto"],
                "tipo_contacto" => $row["tipo_contacto"],
                "observaciones" => $row["observaciones"],
                "id_visita_cerrada" => $row["id_visita_cerrada"],
                "fecha_visita_cerrada" => $row["fecha_visita_cerrada"],
                "nombre_comunicante" => $row["nombre_comunicante"],
                "estado" => $row["estado"],
                "domicilio_instalacion" => $row["domicilio_instalacion"],
                "telefono_fijo" => $row["telefono_fijo"],
                "telefono_movil" => $row["telefono_movil"],
                "email_contacto" => $row["email_contacto"],
                "fecha_hora_preferida" => $row["fecha_hora_preferida"],
                "fecha_recepcion" => $row["fecha_recepcion"],
                "id_metodo" => $row["id_metodo"],
                "id_comercial_asignado" => $row["id_comercial_asignado"],
                "estado_llamada" => $row["estado_llamada"],
                "activo_llamada" => $row["activo_llamada"],
                "nombre_metodo" => $row["nombre_metodo"],
                "imagen_metodo" => $row["imagen_metodo"],
                "descripcion_estado_llamada" => $row["descripcion_estado_llamada"],
                "nombre_comercial" => $row["nombre_comercial"],
                "archivos_adjuntos" => $row["archivos_adjuntos"],
                "tiene_contactos" => $row["tiene_contactos"],
                "estado_es_3" => $row["estado_es_3"],
                "tiene_adjuntos" => $row["tiene_adjuntos"]
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
    } catch (Exception $e) {
        $error_array = array(
            "error" => "Error al listar contactos por comercial",
            "mensaje" => $e->getMessage()
        );
        header('Content-Type: application/json');
        http_response_code(500);
        echo json_encode($error_array, JSON_UNESCAPED_UNICODE);
    }
    break;


        
  case "guardaryeditar":
    header('Content-Type: application/json');

    try {
        $observaciones = empty($_POST["observaciones"]) ? null : $_POST["observaciones"];
        $fecha_visita = empty($_POST["fecha_visita_cerrada"]) ? null : $_POST["fecha_visita_cerrada"];
        $id_llamada = $_POST["id_llamada"];

        if (empty($_POST["id_contacto"])) {
            // Insertar contacto
            $id_contacto = $contacto->insert_contacto(
                $id_llamada,
                $_POST["fecha_hora_contacto"],
                $_POST["id_metodo"],
                $observaciones,
                null
            );

            if (!empty($fecha_visita)) {
                $id_visita_cerrada = $contacto->guardar_o_actualizar_visita_cerrada(
                    $id_contacto,
                    $id_llamada,
                    $fecha_visita
                );

                $contacto->update_contacto(
                    $id_contacto,
                    $id_llamada,
                    $_POST["fecha_hora_contacto"],
                    $_POST["id_metodo"],
                    $observaciones,
                    $id_visita_cerrada
                );
            }

            $registro->registrarActividad(
                'admin',
                'contactos.php',
                'Guardar contacto',
                "Contacto guardado: ID $id_contacto",
                "info"
            );

        } else {
            // Edición
            $id_contacto = $_POST["id_contacto"];
            $tiene_visita = $contacto->tieneVisitaCerrada($id_contacto);

            if ($tiene_visita && empty($fecha_visita)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'No se puede eliminar la visita cerrada si ya existe una asociada.'
                ]);
                exit;
            }

            $id_visita_cerrada = null;
            if (!empty($fecha_visita)) {
                $id_visita_cerrada = $contacto->guardar_o_actualizar_visita_cerrada(
                    $id_contacto,
                    $id_llamada,
                    $fecha_visita
                );
            }

            $contacto->update_contacto(
                $id_contacto,
                $id_llamada,
                $_POST["fecha_hora_contacto"],
                $_POST["id_metodo"],
                $observaciones,
                $id_visita_cerrada
            );

            $registro->registrarActividad(
                'admin',
                'contactos.php',
                'Actualizar contacto',
                "Contacto actualizado: ID $id_contacto",
                "info"
            );
        }

        // ¡AQUÍ ACTUALIZAS EL ESTADO DE LA LLAMADA!
        $contacto->actualizarEstadoLlamada($id_llamada);

        echo json_encode(['success' => true]);

    } catch (Exception $e) {
        $registro->registrarActividad(
            'admin',
            'contactos.php',
            'Error',
            "Error: " . $e->getMessage(),
            "error"
        );

        echo json_encode([
            'success' => false,
            'error' => 'Ocurrió un error al procesar la solicitud'
        ]);
    }
break;

    case "listarPorLlamada":
        $datos = $contacto->get_contactosxidllamada($_POST["id_llamada"]);
        
        $registro->registrarActividad(
            'admin',
            'contactos.php',
            'Obtener contactos por id llamada seleccionado',
            "Contactos obtenidos exitosamente ",
            "info"
            );
        
            header('Content-Type: application/json');
            echo json_encode($datos, JSON_UNESCAPED_UNICODE);
            break;        

    case "mostrar":
        $datos = $contacto->get_contactoxid($_POST["id_contacto"]);
        // if (is_array($datos) == true and count($datos) > 0) {
        //     foreach ($datos as $row) {
        //         $output["prod_id"] = $row["prod_id"];
        //         $output["prod_nom"] = $row["prod_nom"];
        //     }
        // }
        //echo json_encode($output);

        $registro->registrarActividad(
            'admin',
            'contactos.php',
            'Obtener contacto seleccionado',
            "Contacto obtenido exitosamente ",
            "info"
        );

        header('Content-Type: application/json');
        echo json_encode($datos, JSON_UNESCAPED_UNICODE);
        break;

    case "eliminar":
        $contacto->delete_contactoxid($_POST["id_contacto"]);

        $registro->registrarActividad(
            'admin',
            'contactos.php',
            'Eliminar contacto seleccionado',
            "Contacto eliminado exitosamente ",
            "info"
        );

        break;

    case "activar":
        $contacto->activar_contactoxid($_POST["id_contacto"]);

        $registro->registrarActividad(
            'admin',
            'contactos.php',
            'Obtener contactos seleccionado',
            "Contacto activado exitosamente ",
            "info"
        );

        break;
    // SE USA CUANDO EN TABLA CONTACTOS SE PASA UN ID_LLAMADA, PARA COMPROBAR
    // EL ESTADO DE ESA LLAMADA Y SEGÚN LO QUE TENGA, PONER CIERTOS DATOS DE UNA
    // U OTRA FORMA (BLOQUEAR BOTÓN NUEVO EN CONTACTOS)
        case "verificarLlamadaCerrada":
    $datos = $llamada->verificar_estado_activo_llamada($_POST["id_llamada"]);
    
    $registro->registrarActividad(
        'admin',
        'contactos.php',
        'Verificar estado llamada',
        "Se verificó el estado de la llamada con ID: " . $_POST["id_llamada"],
        "info"
    );

    header('Content-Type: application/json');
    echo json_encode($datos, JSON_UNESCAPED_UNICODE);
    break;

     // Verifica si ya existe una visita cerrada para una llamada específica,
    // excluyendo opcionalmente el contacto actual (útil al editar).
    // Esto previene que múltiples contactos tengan visitas cerradas duplicadas
    // asociadas a la misma llamada.
    case "verificarFechaVisitaCerradaPorLlamada":

    $id_llamada = isset($_POST["id_llamada"]) ? $_POST["id_llamada"] : null;
    $id_contacto = isset($_POST["id_contacto"]) ? $_POST["id_contacto"] : null;

    $datos = $contacto->verificar_fecha_visita_cerrada($id_llamada, $id_contacto);

    $registro->registrarActividad(
        'admin',
        'contactos.php',
        'Verificar fecha visita cerrada',
        "Se verificó si existe una fecha de visita cerrada para la llamada ID: $id_llamada",
        "info"
    );

    header('Content-Type: application/json');
    echo json_encode($datos, JSON_UNESCAPED_UNICODE);
    break;

    // Verifica si un contacto ya tiene una visita cerrada asociada (id_visita_cerrada).
    // Es útil para decidir si se puede editar o eliminar esa relación.
    case "verificarVisitaCerradaPorContacto":
    $id_contacto = $_POST["id_contacto"] ?? null;

    $existe = $contacto->tieneVisitaCerrada($id_contacto);

    $registro->registrarActividad(
        'admin',
        'contactos.php',
        'Verificar fecha visita cerrada por contacto',
        "Se verificó si existe una fecha de visita cerrada por contacto para la llamada ID: $id_llamada",
        "info"
    );

    header('Content-Type: application/json');
    echo json_encode(['existe' => $existe]);
    break;


    
}
