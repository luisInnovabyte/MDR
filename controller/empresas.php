<?php
require_once "../config/conexion.php";
require_once "../models/Empresas.php";
require_once '../config/funciones.php';

$registro = new RegistroActividad();
$empresa = new Empresas();


// Función para escribir en el log - Desarrollo
function writeToLog($logData)
{
    $logFile = "../public/logs/log_" . date("Ymd") . ".txt";
    $logMessage = "[" . date("Y-m-d H:i:s") . "] " . json_encode($logData, JSON_UNESCAPED_UNICODE) . "\n";
    file_put_contents($logFile, $logMessage, FILE_APPEND);
}


switch ($_GET["op"]) {

    case "listar":
        $datos = $empresa->get_empresa();
        $data = array();
        foreach ($datos as $row) {
            $data[] = array(
                "id_empresa" => $row["id_empresa"],
                "codigo_empresa" => $row["codigo_empresa"],
                "nombre_empresa" => $row["nombre_empresa"],
                "nombre_comercial_empresa" => $row["nombre_comercial_empresa"],
                "ficticia_empresa" => $row["ficticia_empresa"],
                "empresa_ficticia_principal" => $row["empresa_ficticia_principal"],
                "nif_empresa" => $row["nif_empresa"],
                "direccion_fiscal_empresa" => $row["direccion_fiscal_empresa"],
                "cp_fiscal_empresa" => $row["cp_fiscal_empresa"],
                "poblacion_fiscal_empresa" => $row["poblacion_fiscal_empresa"],
                "provincia_fiscal_empresa" => $row["provincia_fiscal_empresa"],
                "pais_fiscal_empresa" => $row["pais_fiscal_empresa"],
                "telefono_empresa" => $row["telefono_empresa"],
                "movil_empresa" => $row["movil_empresa"],
                "email_empresa" => $row["email_empresa"],
                "email_facturacion_empresa" => $row["email_facturacion_empresa"],
                "web_empresa" => $row["web_empresa"],
                "iban_empresa" => $row["iban_empresa"],
                "swift_empresa" => $row["swift_empresa"],
                "banco_empresa" => $row["banco_empresa"],
                "serie_presupuesto_empresa" => $row["serie_presupuesto_empresa"],
                "numero_actual_presupuesto_empresa" => $row["numero_actual_presupuesto_empresa"],
                "serie_factura_empresa" => $row["serie_factura_empresa"],
                "numero_actual_factura_empresa" => $row["numero_actual_factura_empresa"],
                "serie_abono_empresa" => $row["serie_abono_empresa"],
                "numero_actual_abono_empresa" => $row["numero_actual_abono_empresa"],
                "verifactu_activo_empresa" => $row["verifactu_activo_empresa"],
                "verifactu_software_empresa" => $row["verifactu_software_empresa"],
                "verifactu_version_empresa" => $row["verifactu_version_empresa"],
                "verifactu_nif_desarrollador_empresa" => $row["verifactu_nif_desarrollador_empresa"],
                "verifactu_nombre_desarrollador_empresa" => $row["verifactu_nombre_desarrollador_empresa"],
                "verifactu_sistema_empresa" => $row["verifactu_sistema_empresa"],
                "verifactu_url_empresa" => $row["verifactu_url_empresa"],
                "verifactu_certificado_empresa" => $row["verifactu_certificado_empresa"],
                "logotipo_empresa" => $row["logotipo_empresa"],
                "logotipo_pie_empresa" => $row["logotipo_pie_empresa"],
                "texto_legal_factura_empresa" => $row["texto_legal_factura_empresa"],
                "texto_pie_presupuesto_empresa" => $row["texto_pie_presupuesto_empresa"],
                "texto_pie_factura_empresa" => $row["texto_pie_factura_empresa"],
                "observaciones_empresa" => $row["observaciones_empresa"],
                "activo_empresa" => $row["activo_empresa"],
                "created_at_empresa" => $row["created_at_empresa"],
                "updated_at_empresa" => $row["updated_at_empresa"]
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

    case "listarDisponibles":
        $datos = $empresa->get_empresa_disponible();
        $data = array();
        foreach ($datos as $row) {
            $data[] = array(
                "id_empresa" => $row["id_empresa"],
                "codigo_empresa" => $row["codigo_empresa"],
                "nombre_empresa" => $row["nombre_empresa"],
                "nombre_comercial_empresa" => $row["nombre_comercial_empresa"]
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

    case "guardaryeditar":
        try {
            if (empty($_POST["id_empresa"])) {
                $resultado = $empresa->insert_empresa(
                    $_POST["codigo_empresa"],
                    $_POST["nombre_empresa"],
                    $_POST["nombre_comercial_empresa"],
                    isset($_POST["ficticia_empresa"]) ? 1 : 0,
                    isset($_POST["empresa_ficticia_principal"]) ? 1 : 0,
                    $_POST["nif_empresa"],
                    $_POST["direccion_fiscal_empresa"],
                    $_POST["cp_fiscal_empresa"],
                    $_POST["poblacion_fiscal_empresa"],
                    $_POST["provincia_fiscal_empresa"],
                    $_POST["pais_fiscal_empresa"],
                    $_POST["telefono_empresa"],
                    $_POST["movil_empresa"],
                    $_POST["email_empresa"],
                    $_POST["email_facturacion_empresa"],
                    $_POST["web_empresa"],
                    $_POST["iban_empresa"],
                    $_POST["swift_empresa"],
                    $_POST["banco_empresa"],
                    $_POST["serie_presupuesto_empresa"],
                    $_POST["numero_actual_presupuesto_empresa"],
                    $_POST["serie_factura_empresa"],
                    $_POST["numero_actual_factura_empresa"],
                    $_POST["serie_abono_empresa"],
                    $_POST["numero_actual_abono_empresa"],
                    isset($_POST["verifactu_activo_empresa"]) ? 1 : 0,
                    $_POST["verifactu_software_empresa"],
                    $_POST["verifactu_version_empresa"],
                    $_POST["verifactu_nif_desarrollador_empresa"],
                    $_POST["verifactu_nombre_desarrollador_empresa"],
                    $_POST["verifactu_sistema_empresa"],
                    $_POST["verifactu_url_empresa"],
                    $_POST["verifactu_certificado_empresa"],
                    $_POST["logotipo_empresa"],
                    $_POST["logotipo_pie_empresa"],
                    $_POST["texto_legal_factura_empresa"],
                    $_POST["texto_pie_presupuesto_empresa"],
                    $_POST["texto_pie_factura_empresa"],
                    $_POST["observaciones_empresa"]
                );
                
                if ($resultado !== false && $resultado > 0) {
                    $registro->registrarActividad(
                        'admin',
                        'empresas.php',
                        'Guardar la empresa',
                        "Empresa guardada exitosamente con ID: $resultado",
                        "info"
                    );
                    
                    header('Content-Type: application/json');
                    echo json_encode([
                        'status' => 'success',
                        'message' => 'Empresa guardada exitosamente',
                        'id_empresa' => $resultado
                    ], JSON_UNESCAPED_UNICODE);
                } else {
                    header('Content-Type: application/json');
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'Error al insertar la empresa en la base de datos'
                    ], JSON_UNESCAPED_UNICODE);
                }
                
            } else {
                $resultado = $empresa->update_empresa(
                    $_POST["id_empresa"],
                    $_POST["codigo_empresa"],
                    $_POST["nombre_empresa"],
                    $_POST["nombre_comercial_empresa"],
                    isset($_POST["ficticia_empresa"]) ? 1 : 0,
                    isset($_POST["empresa_ficticia_principal"]) ? 1 : 0,
                    $_POST["nif_empresa"],
                    $_POST["direccion_fiscal_empresa"],
                    $_POST["cp_fiscal_empresa"],
                    $_POST["poblacion_fiscal_empresa"],
                    $_POST["provincia_fiscal_empresa"],
                    $_POST["pais_fiscal_empresa"],
                    $_POST["telefono_empresa"],
                    $_POST["movil_empresa"],
                    $_POST["email_empresa"],
                    $_POST["email_facturacion_empresa"],
                    $_POST["web_empresa"],
                    $_POST["iban_empresa"],
                    $_POST["swift_empresa"],
                    $_POST["banco_empresa"],
                    $_POST["serie_presupuesto_empresa"],
                    $_POST["numero_actual_presupuesto_empresa"],
                    $_POST["serie_factura_empresa"],
                    $_POST["numero_actual_factura_empresa"],
                    $_POST["serie_abono_empresa"],
                    $_POST["numero_actual_abono_empresa"],
                    isset($_POST["verifactu_activo_empresa"]) ? 1 : 0,
                    $_POST["verifactu_software_empresa"],
                    $_POST["verifactu_version_empresa"],
                    $_POST["verifactu_nif_desarrollador_empresa"],
                    $_POST["verifactu_nombre_desarrollador_empresa"],
                    $_POST["verifactu_sistema_empresa"],
                    $_POST["verifactu_url_empresa"],
                    $_POST["verifactu_certificado_empresa"],
                    $_POST["logotipo_empresa"],
                    $_POST["logotipo_pie_empresa"],
                    $_POST["texto_legal_factura_empresa"],
                    $_POST["texto_pie_presupuesto_empresa"],
                    $_POST["texto_pie_factura_empresa"],
                    $_POST["observaciones_empresa"]
                );
                
                if ($resultado !== false) {
                    $registro->registrarActividad(
                        'admin',
                        'empresas.php',
                        'Actualizar la empresa',
                        "Empresa actualizada exitosamente ID: " . $_POST["id_empresa"],
                        "info"
                    );
                    
                    header('Content-Type: application/json');
                    echo json_encode([
                        'status' => 'success',
                        'message' => 'Empresa actualizada exitosamente'
                    ], JSON_UNESCAPED_UNICODE);
                } else {
                    header('Content-Type: application/json');
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'Error al actualizar la empresa en la base de datos'
                    ], JSON_UNESCAPED_UNICODE);
                }
            }
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'status' => 'error',
                'message' => 'Error detallado: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
        break;

    case "mostrar":
        $datos = $empresa->get_empresaxid($_POST["id_empresa"]);

        $registro->registrarActividad(
            'admin',
            'empresas.php',
            'Obtener empresa seleccionada',
            "Empresa obtenida exitosamente",
            "info"
        );

        header('Content-Type: application/json');
        echo json_encode($datos, JSON_UNESCAPED_UNICODE);
        break;

    case "eliminar":
        $empresa->delete_empresaxid($_POST["id_empresa"]);

        $registro->registrarActividad(
            'admin',
            'empresas.php',
            'Eliminar empresa seleccionada',
            "Empresa eliminada exitosamente",
            "info"
        );

        break;

    case "activar":
        $empresa->activar_empresaxid($_POST["id_empresa"]);

        $registro->registrarActividad(
            'admin',
            'empresas.php',
            'Activar empresa seleccionada',
            "Empresa activada exitosamente",
            "info"
        );

        break;

    case "verificar":
        $resultado = $empresa->verificarEmpresa(
            $_POST["codigo_empresa"],
            $_POST["nif_empresa"] ?? null,
            $_POST["id_empresa"] ?? null
        );
        header('Content-Type: application/json');
        echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
        break;

    case "obtenerEmpresaActiva":
        $datos = $empresa->get_empresaActiva();
        
        $registro->registrarActividad(
            'admin',
            'empresas.php',
            'Obtener empresa ficticia principal',
            "Empresa ficticia principal obtenida",
            "info"
        );

        header('Content-Type: application/json');
        echo json_encode($datos, JSON_UNESCAPED_UNICODE);
        break;

    case "validarEmpresaFicticia":
        $datos = $empresa->validar_empresaFicticia();
        
        $registro->registrarActividad(
            'admin',
            'empresas.php',
            'Validar empresa ficticia principal',
            "Validación de empresa ficticia principal ejecutada",
            "info"
        );

        header('Content-Type: application/json');
        echo json_encode($datos, JSON_UNESCAPED_UNICODE);
        break;
}
?>
