<?php

require_once '../config/conexion.php';
require_once '../config/funciones.php';


class Empresas
{
    private $conexion;
    private $registro;

    public function __construct()
    {
        $this->conexion = (new Conexion())->getConexion();
        $this->registro = new RegistroActividad();
        
        // Configurar zona horaria Madrid para todas las operaciones
        try {
            $this->conexion->exec("SET time_zone = 'Europe/Madrid'");
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'system',
                'Empresas',
                '__construct',
                "No se pudo establecer zona horaria Madrid: " . $e->getMessage(),
                'warning'
            );
        }
    }

    public function get_empresa()
    {
        try {
            $sql = "SELECT * FROM empresa";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();

            return $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Empresas',
                'get_empresa',
                "Error al listar las empresas: " . $e->getMessage(),
                "error"
            );
        }
    }

    public function get_empresa_disponible()
    {
        try {
            $sql = "SELECT * FROM empresa WHERE activo_empresa = 1";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();

            return $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Empresas',
                'get_empresa_disponible',
                "Error al listar las empresas disponibles: " . $e->getMessage(),
                "error"
            );
        }
    }

    public function get_empresaActiva()
    {
        try {
            $sql = "SELECT 
                        id_empresa,
                        codigo_empresa,
                        nombre_empresa,
                        nombre_comercial_empresa,
                        serie_presupuesto_empresa,
                        numero_actual_presupuesto_empresa,
                        dias_validez_presupuesto_empresa,
                        logotipo_empresa
                    FROM empresa 
                    WHERE empresa_ficticia_principal = TRUE 
                    AND activo_empresa = TRUE";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();

            return $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Empresas',
                'get_empresaActiva',
                "Error al obtener la empresa activa principal: " . $e->getMessage(),
                "error"
            );

            return false;
        }
    }

    public function validar_empresaFicticia()
    {
        try {
            $sql = "SELECT 
                        COUNT(*) as total,
                        CASE 
                            WHEN COUNT(*) = 1 THEN '✓ CORRECTO'
                            WHEN COUNT(*) = 0 THEN '⚠ ADVERTENCIA: No hay empresa ficticia principal'
                            ELSE '✗ ERROR: Hay más de una empresa ficticia principal'
                        END as validacion
                    FROM empresa
                    WHERE empresa_ficticia_principal = TRUE
                    AND activo_empresa = TRUE";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();

            return $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Empresas',
                'validar_empresaFicticia',
                "Error al validar la empresa ficticia principal: " . $e->getMessage(),
                "error"
            );

            return false;
        }
    }

    public function get_empresaxid($id_empresa)
    {
        try {
            $sql = "SELECT * FROM empresa WHERE id_empresa = ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_empresa, PDO::PARAM_INT);
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
                        
            return $resultado;
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Empresas',
                'get_empresaxid',
                "Error al mostrar la empresa {$id_empresa}: " . $e->getMessage(),
                "error"
            );

            return false;
        }
    }

    public function delete_empresaxid($id_empresa)
    {
        try {
            $sql = "UPDATE empresa SET activo_empresa = 0 WHERE id_empresa = ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_empresa, PDO::PARAM_INT);
            $stmt->execute();

            $this->registro->registrarActividad(
                'admin',
                'Empresas',
                'Desactivar',
                "Se desactivó la empresa con ID: $id_empresa",
                'info'
            );
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Empresas',
                'delete_empresaxid',
                "Error al desactivar la empresa {$id_empresa}: " . $e->getMessage(),
                'error'
            );

            return false;
        }
    }

    public function activar_empresaxid($id_empresa)
    {
        try {
            $sql = "UPDATE empresa SET activo_empresa = 1 WHERE id_empresa = ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_empresa, PDO::PARAM_INT);
            $stmt->execute();

            $this->registro->registrarActividad(
                'admin',
                'Empresas',
                'Activar',
                "Se activó la empresa con ID: $id_empresa",
                'info'
            );
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Empresas',
                'activar_empresaxid',
                "Error al activar la empresa {$id_empresa}: " . $e->getMessage(),
                "error"
            );

            return false;
        }
    }

    public function insert_empresa(
        $codigo_empresa,
        $nombre_empresa,
        $nombre_comercial_empresa,
        $ficticia_empresa,
        $empresa_ficticia_principal,
        $nif_empresa,
        $direccion_fiscal_empresa,
        $cp_fiscal_empresa,
        $poblacion_fiscal_empresa,
        $provincia_fiscal_empresa,
        $pais_fiscal_empresa,
        $telefono_empresa,
        $movil_empresa,
        $email_empresa,
        $email_facturacion_empresa,
        $web_empresa,
        $iban_empresa,
        $swift_empresa,
        $banco_empresa,
        $serie_presupuesto_empresa,
        $numero_actual_presupuesto_empresa,
        $dias_validez_presupuesto_empresa,
        $serie_factura_empresa,
        $numero_actual_factura_empresa,
        $serie_abono_empresa,
        $numero_actual_abono_empresa,
        $verifactu_activo_empresa,
        $verifactu_software_empresa,
        $verifactu_version_empresa,
        $verifactu_nif_desarrollador_empresa,
        $verifactu_nombre_desarrollador_empresa,
        $verifactu_sistema_empresa,
        $verifactu_url_empresa,
        $verifactu_certificado_empresa,
        $logotipo_empresa,
        $logotipo_pie_empresa,
        $texto_legal_factura_empresa,
        $texto_pie_presupuesto_empresa,
        $texto_pie_factura_empresa,
        $observaciones_empresa,
        $modelo_impresion_empresa,
        $configuracion_pdf_presupuesto_empresa,
        $observaciones_cabecera_presupuesto_empresa,
        $observaciones_cabecera_ingles_presupuesto_empresa,
        $mostrar_subtotales_fecha_presupuesto_empresa,
        $cabecera_firma_presupuesto_empresa = 'Departamento comercial',
        $mostrar_cuenta_bancaria_pdf_presupuesto_empresa = 1,
        $mostrar_kits_albaran_empresa = 1,
        $mostrar_obs_familias_articulos_albaran_empresa = 1,
        $mostrar_obs_pie_albaran_empresa = 1,
        $obs_linea_alineadas_descripcion_empresa = 0
    ) {
        try {
            $this->conexion->exec("SET time_zone = 'Europe/Madrid'");

            // Asegurar que los valores booleanos nunca sean NULL
            $mostrar_subtotales_fecha_presupuesto_empresa = intval($mostrar_subtotales_fecha_presupuesto_empresa) ? 1 : 0;
            $mostrar_cuenta_bancaria_pdf_presupuesto_empresa = intval($mostrar_cuenta_bancaria_pdf_presupuesto_empresa) ? 1 : 0;
            $mostrar_kits_albaran_empresa = intval($mostrar_kits_albaran_empresa) ? 1 : 0;
            $mostrar_obs_familias_articulos_albaran_empresa = intval($mostrar_obs_familias_articulos_albaran_empresa) ? 1 : 0;
            $mostrar_obs_pie_albaran_empresa = intval($mostrar_obs_pie_albaran_empresa) ? 1 : 0;
            $obs_linea_alineadas_descripcion_empresa = intval($obs_linea_alineadas_descripcion_empresa) ? 1 : 0;

            $sql = "INSERT INTO empresa (
                codigo_empresa, nombre_empresa, nombre_comercial_empresa, ficticia_empresa, empresa_ficticia_principal,
                nif_empresa, direccion_fiscal_empresa, cp_fiscal_empresa, poblacion_fiscal_empresa, provincia_fiscal_empresa, pais_fiscal_empresa,
                telefono_empresa, movil_empresa, email_empresa, email_facturacion_empresa, web_empresa,
                iban_empresa, swift_empresa, banco_empresa,
                serie_presupuesto_empresa, numero_actual_presupuesto_empresa, dias_validez_presupuesto_empresa,
                serie_factura_empresa, numero_actual_factura_empresa,
                serie_abono_empresa, numero_actual_abono_empresa,
                verifactu_activo_empresa, verifactu_software_empresa, verifactu_version_empresa,
                verifactu_nif_desarrollador_empresa, verifactu_nombre_desarrollador_empresa,
                verifactu_sistema_empresa, verifactu_url_empresa, verifactu_certificado_empresa,
                logotipo_empresa, logotipo_pie_empresa,
                texto_legal_factura_empresa, texto_pie_presupuesto_empresa, texto_pie_factura_empresa,
                observaciones_empresa, modelo_impresion_empresa, configuracion_pdf_presupuesto_empresa,
                observaciones_cabecera_presupuesto_empresa, observaciones_cabecera_ingles_presupuesto_empresa,
                mostrar_subtotales_fecha_presupuesto_empresa,
                cabecera_firma_presupuesto_empresa,
                mostrar_cuenta_bancaria_pdf_presupuesto_empresa,
                mostrar_kits_albaran_empresa,
                mostrar_obs_familias_articulos_albaran_empresa,
                mostrar_obs_pie_albaran_empresa,
                obs_linea_alineadas_descripcion_empresa,
                activo_empresa, created_at_empresa, updated_at_empresa
            ) VALUES (
                ?, ?, ?, ?, ?,
                ?, ?, ?, ?, ?, ?,
                ?, ?, ?, ?, ?,
                ?, ?, ?,
                ?, ?, ?,
                ?, ?,
                ?, ?,
                ?, ?, ?,
                ?, ?,
                ?, ?, ?,
                ?, ?,
                ?, ?, ?,
                ?, ?, ?,
                ?, ?,
                ?,
                ?,
                ?,
                ?, ?, ?,
                ?,
                1, NOW(), NOW()
            )";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $codigo_empresa, PDO::PARAM_STR);
            $stmt->bindValue(2, $nombre_empresa, PDO::PARAM_STR);
            $stmt->bindValue(3, $nombre_comercial_empresa, PDO::PARAM_STR);
            $stmt->bindValue(4, $ficticia_empresa, PDO::PARAM_BOOL);
            $stmt->bindValue(5, $empresa_ficticia_principal, PDO::PARAM_BOOL);
            $stmt->bindValue(6, $nif_empresa, PDO::PARAM_STR);
            $stmt->bindValue(7, $direccion_fiscal_empresa, PDO::PARAM_STR);
            $stmt->bindValue(8, $cp_fiscal_empresa, PDO::PARAM_STR);
            $stmt->bindValue(9, $poblacion_fiscal_empresa, PDO::PARAM_STR);
            $stmt->bindValue(10, $provincia_fiscal_empresa, PDO::PARAM_STR);
            $stmt->bindValue(11, $pais_fiscal_empresa, PDO::PARAM_STR);
            $stmt->bindValue(12, $telefono_empresa, PDO::PARAM_STR);
            $stmt->bindValue(13, $movil_empresa, PDO::PARAM_STR);
            $stmt->bindValue(14, $email_empresa, PDO::PARAM_STR);
            $stmt->bindValue(15, $email_facturacion_empresa, PDO::PARAM_STR);
            $stmt->bindValue(16, $web_empresa, PDO::PARAM_STR);
            $stmt->bindValue(17, $iban_empresa, PDO::PARAM_STR);
            $stmt->bindValue(18, $swift_empresa, PDO::PARAM_STR);
            $stmt->bindValue(19, $banco_empresa, PDO::PARAM_STR);
            $stmt->bindValue(20, $serie_presupuesto_empresa, PDO::PARAM_STR);
            $stmt->bindValue(21, $numero_actual_presupuesto_empresa, PDO::PARAM_INT);
            $stmt->bindValue(22, $dias_validez_presupuesto_empresa, PDO::PARAM_INT);
            $stmt->bindValue(23, $serie_factura_empresa, PDO::PARAM_STR);
            $stmt->bindValue(24, $numero_actual_factura_empresa, PDO::PARAM_INT);
            $stmt->bindValue(25, $serie_abono_empresa, PDO::PARAM_STR);
            $stmt->bindValue(26, $numero_actual_abono_empresa, PDO::PARAM_INT);
            $stmt->bindValue(27, $verifactu_activo_empresa, PDO::PARAM_BOOL);
            $stmt->bindValue(28, $verifactu_software_empresa, PDO::PARAM_STR);
            $stmt->bindValue(29, $verifactu_version_empresa, PDO::PARAM_STR);
            $stmt->bindValue(30, $verifactu_nif_desarrollador_empresa, PDO::PARAM_STR);
            $stmt->bindValue(31, $verifactu_nombre_desarrollador_empresa, PDO::PARAM_STR);
            $stmt->bindValue(32, $verifactu_sistema_empresa, PDO::PARAM_STR);
            $stmt->bindValue(33, $verifactu_url_empresa, PDO::PARAM_STR);
            $stmt->bindValue(34, $verifactu_certificado_empresa, PDO::PARAM_STR);
            $stmt->bindValue(35, $logotipo_empresa, PDO::PARAM_STR);
            $stmt->bindValue(36, $logotipo_pie_empresa, PDO::PARAM_STR);
            $stmt->bindValue(37, $texto_legal_factura_empresa, PDO::PARAM_STR);
            $stmt->bindValue(38, $texto_pie_presupuesto_empresa, PDO::PARAM_STR);
            $stmt->bindValue(39, $texto_pie_factura_empresa, PDO::PARAM_STR);
            $stmt->bindValue(40, $observaciones_empresa, PDO::PARAM_STR);
            $stmt->bindValue(41, $modelo_impresion_empresa, PDO::PARAM_STR);
            $stmt->bindValue(42, $configuracion_pdf_presupuesto_empresa, PDO::PARAM_STR);
            $stmt->bindValue(43, $observaciones_cabecera_presupuesto_empresa, $observaciones_cabecera_presupuesto_empresa === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindValue(44, $observaciones_cabecera_ingles_presupuesto_empresa, $observaciones_cabecera_ingles_presupuesto_empresa === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindValue(45, $mostrar_subtotales_fecha_presupuesto_empresa, PDO::PARAM_INT);
            $stmt->bindValue(46, $cabecera_firma_presupuesto_empresa, PDO::PARAM_STR);
            $stmt->bindValue(47, $mostrar_cuenta_bancaria_pdf_presupuesto_empresa, PDO::PARAM_INT);
            $stmt->bindValue(48, $mostrar_kits_albaran_empresa, PDO::PARAM_INT);
            $stmt->bindValue(49, $mostrar_obs_familias_articulos_albaran_empresa, PDO::PARAM_INT);
            $stmt->bindValue(50, $mostrar_obs_pie_albaran_empresa, PDO::PARAM_INT);
            $stmt->bindValue(51, $obs_linea_alineadas_descripcion_empresa, PDO::PARAM_INT);

            $stmt->execute();
            $idInsert = $this->conexion->lastInsertId();

            $this->registro->registrarActividad(
                'admin',
                'Empresas',
                'Insertar',
                "Se insertó la empresa con ID: $idInsert",
                'info'
            );

            return $idInsert;
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Empresas',
                'insert_empresa',
                "Error al insertar la empresa: " . $e->getMessage(),
                'error'
            );

            return false;
        }
    }

    public function update_empresa(
        $id_empresa,
        $codigo_empresa,
        $nombre_empresa,
        $nombre_comercial_empresa,
        $ficticia_empresa,
        $empresa_ficticia_principal,
        $nif_empresa,
        $direccion_fiscal_empresa,
        $cp_fiscal_empresa,
        $poblacion_fiscal_empresa,
        $provincia_fiscal_empresa,
        $pais_fiscal_empresa,
        $telefono_empresa,
        $movil_empresa,
        $email_empresa,
        $email_facturacion_empresa,
        $web_empresa,
        $iban_empresa,
        $swift_empresa,
        $banco_empresa,
        $serie_presupuesto_empresa,
        $numero_actual_presupuesto_empresa,
        $dias_validez_presupuesto_empresa,
        $serie_factura_empresa,
        $numero_actual_factura_empresa,
        $serie_abono_empresa,
        $numero_actual_abono_empresa,
        $verifactu_activo_empresa,
        $verifactu_software_empresa,
        $verifactu_version_empresa,
        $verifactu_nif_desarrollador_empresa,
        $verifactu_nombre_desarrollador_empresa,
        $verifactu_sistema_empresa,
        $verifactu_url_empresa,
        $verifactu_certificado_empresa,
        $logotipo_empresa,
        $logotipo_pie_empresa,
        $texto_legal_factura_empresa,
        $texto_pie_presupuesto_empresa,
        $texto_pie_factura_empresa,
        $observaciones_empresa,
        $modelo_impresion_empresa,
        $configuracion_pdf_presupuesto_empresa,
        $observaciones_cabecera_presupuesto_empresa,
        $observaciones_cabecera_ingles_presupuesto_empresa,
        $mostrar_subtotales_fecha_presupuesto_empresa,
        $cabecera_firma_presupuesto_empresa = 'Departamento comercial',
        $mostrar_cuenta_bancaria_pdf_presupuesto_empresa = 1,
        $mostrar_kits_albaran_empresa = 1,
        $mostrar_obs_familias_articulos_albaran_empresa = 1,
        $mostrar_obs_pie_albaran_empresa = 1,
        $obs_linea_alineadas_descripcion_empresa = 0
    ) {
        try {
            $this->conexion->exec("SET time_zone = 'Europe/Madrid'");

            // Asegurar que los valores booleanos nunca sean NULL
            $mostrar_subtotales_fecha_presupuesto_empresa = intval($mostrar_subtotales_fecha_presupuesto_empresa) ? 1 : 0;
            $mostrar_cuenta_bancaria_pdf_presupuesto_empresa = intval($mostrar_cuenta_bancaria_pdf_presupuesto_empresa) ? 1 : 0;
            $mostrar_kits_albaran_empresa = intval($mostrar_kits_albaran_empresa) ? 1 : 0;
            $mostrar_obs_familias_articulos_albaran_empresa = intval($mostrar_obs_familias_articulos_albaran_empresa) ? 1 : 0;
            $mostrar_obs_pie_albaran_empresa = intval($mostrar_obs_pie_albaran_empresa) ? 1 : 0;
            $obs_linea_alineadas_descripcion_empresa = intval($obs_linea_alineadas_descripcion_empresa) ? 1 : 0;

            $sql = "UPDATE empresa SET 
                codigo_empresa = ?, nombre_empresa = ?, nombre_comercial_empresa = ?, ficticia_empresa = ?, empresa_ficticia_principal = ?,
                nif_empresa = ?, direccion_fiscal_empresa = ?, cp_fiscal_empresa = ?, poblacion_fiscal_empresa = ?, provincia_fiscal_empresa = ?, pais_fiscal_empresa = ?,
                telefono_empresa = ?, movil_empresa = ?, email_empresa = ?, email_facturacion_empresa = ?, web_empresa = ?,
                iban_empresa = ?, swift_empresa = ?, banco_empresa = ?,
                serie_presupuesto_empresa = ?, numero_actual_presupuesto_empresa = ?, dias_validez_presupuesto_empresa = ?,
                serie_factura_empresa = ?, numero_actual_factura_empresa = ?,
                serie_abono_empresa = ?, numero_actual_abono_empresa = ?,
                verifactu_activo_empresa = ?, verifactu_software_empresa = ?, verifactu_version_empresa = ?,
                verifactu_nif_desarrollador_empresa = ?, verifactu_nombre_desarrollador_empresa = ?,
                verifactu_sistema_empresa = ?, verifactu_url_empresa = ?, verifactu_certificado_empresa = ?,
                logotipo_empresa = ?, logotipo_pie_empresa = ?,
                texto_legal_factura_empresa = ?, texto_pie_presupuesto_empresa = ?, texto_pie_factura_empresa = ?,
                observaciones_empresa = ?, modelo_impresion_empresa = ?, configuracion_pdf_presupuesto_empresa = ?,
                observaciones_cabecera_presupuesto_empresa = ?, observaciones_cabecera_ingles_presupuesto_empresa = ?,
                mostrar_subtotales_fecha_presupuesto_empresa = ?,
                cabecera_firma_presupuesto_empresa = ?,
                mostrar_cuenta_bancaria_pdf_presupuesto_empresa = ?,
                mostrar_kits_albaran_empresa = ?,
                mostrar_obs_familias_articulos_albaran_empresa = ?,
                mostrar_obs_pie_albaran_empresa = ?,
                obs_linea_alineadas_descripcion_empresa = ?,
                updated_at_empresa = NOW()
                WHERE id_empresa = ?";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $codigo_empresa, PDO::PARAM_STR);
            $stmt->bindValue(2, $nombre_empresa, PDO::PARAM_STR);
            $stmt->bindValue(3, $nombre_comercial_empresa, PDO::PARAM_STR);
            $stmt->bindValue(4, $ficticia_empresa, PDO::PARAM_BOOL);
            $stmt->bindValue(5, $empresa_ficticia_principal, PDO::PARAM_BOOL);
            $stmt->bindValue(6, $nif_empresa, PDO::PARAM_STR);
            $stmt->bindValue(7, $direccion_fiscal_empresa, PDO::PARAM_STR);
            $stmt->bindValue(8, $cp_fiscal_empresa, PDO::PARAM_STR);
            $stmt->bindValue(9, $poblacion_fiscal_empresa, PDO::PARAM_STR);
            $stmt->bindValue(10, $provincia_fiscal_empresa, PDO::PARAM_STR);
            $stmt->bindValue(11, $pais_fiscal_empresa, PDO::PARAM_STR);
            $stmt->bindValue(12, $telefono_empresa, PDO::PARAM_STR);
            $stmt->bindValue(13, $movil_empresa, PDO::PARAM_STR);
            $stmt->bindValue(14, $email_empresa, PDO::PARAM_STR);
            $stmt->bindValue(15, $email_facturacion_empresa, PDO::PARAM_STR);
            $stmt->bindValue(16, $web_empresa, PDO::PARAM_STR);
            $stmt->bindValue(17, $iban_empresa, PDO::PARAM_STR);
            $stmt->bindValue(18, $swift_empresa, PDO::PARAM_STR);
            $stmt->bindValue(19, $banco_empresa, PDO::PARAM_STR);
            $stmt->bindValue(20, $serie_presupuesto_empresa, PDO::PARAM_STR);
            $stmt->bindValue(21, $numero_actual_presupuesto_empresa, PDO::PARAM_INT);
            $stmt->bindValue(22, $dias_validez_presupuesto_empresa, PDO::PARAM_INT);
            $stmt->bindValue(23, $serie_factura_empresa, PDO::PARAM_STR);
            $stmt->bindValue(24, $numero_actual_factura_empresa, PDO::PARAM_INT);
            $stmt->bindValue(25, $serie_abono_empresa, PDO::PARAM_STR);
            $stmt->bindValue(26, $numero_actual_abono_empresa, PDO::PARAM_INT);
            $stmt->bindValue(27, $verifactu_activo_empresa, PDO::PARAM_BOOL);
            $stmt->bindValue(28, $verifactu_software_empresa, PDO::PARAM_STR);
            $stmt->bindValue(29, $verifactu_version_empresa, PDO::PARAM_STR);
            $stmt->bindValue(30, $verifactu_nif_desarrollador_empresa, PDO::PARAM_STR);
            $stmt->bindValue(31, $verifactu_nombre_desarrollador_empresa, PDO::PARAM_STR);
            $stmt->bindValue(32, $verifactu_sistema_empresa, PDO::PARAM_STR);
            $stmt->bindValue(33, $verifactu_url_empresa, PDO::PARAM_STR);
            $stmt->bindValue(34, $verifactu_certificado_empresa, PDO::PARAM_STR);
            $stmt->bindValue(35, $logotipo_empresa, PDO::PARAM_STR);
            $stmt->bindValue(36, $logotipo_pie_empresa, PDO::PARAM_STR);
            $stmt->bindValue(37, $texto_legal_factura_empresa, PDO::PARAM_STR);
            $stmt->bindValue(38, $texto_pie_presupuesto_empresa, PDO::PARAM_STR);
            $stmt->bindValue(39, $texto_pie_factura_empresa, PDO::PARAM_STR);
            $stmt->bindValue(40, $observaciones_empresa, PDO::PARAM_STR);
            $stmt->bindValue(41, $modelo_impresion_empresa, PDO::PARAM_STR);
            $stmt->bindValue(42, $configuracion_pdf_presupuesto_empresa, PDO::PARAM_STR);
            $stmt->bindValue(43, $observaciones_cabecera_presupuesto_empresa, $observaciones_cabecera_presupuesto_empresa === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindValue(44, $observaciones_cabecera_ingles_presupuesto_empresa, $observaciones_cabecera_ingles_presupuesto_empresa === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindValue(45, $mostrar_subtotales_fecha_presupuesto_empresa, PDO::PARAM_INT);
            $stmt->bindValue(46, $cabecera_firma_presupuesto_empresa, PDO::PARAM_STR);
            $stmt->bindValue(47, $mostrar_cuenta_bancaria_pdf_presupuesto_empresa, PDO::PARAM_INT);
            $stmt->bindValue(48, $mostrar_kits_albaran_empresa, PDO::PARAM_INT);
            $stmt->bindValue(49, $mostrar_obs_familias_articulos_albaran_empresa, PDO::PARAM_INT);
            $stmt->bindValue(50, $mostrar_obs_pie_albaran_empresa, PDO::PARAM_INT);
            $stmt->bindValue(51, $obs_linea_alineadas_descripcion_empresa, PDO::PARAM_INT);
            $stmt->bindValue(52, $id_empresa, PDO::PARAM_INT);

            $stmt->execute();

            $this->registro->registrarActividad(
                'admin',
                'Empresas',
                'Actualizar',
                "Se actualizó la empresa con ID: $id_empresa",
                'info'
            );

            return true;

        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Empresas',
                'update_empresa',
                "Error al actualizar la empresa: " . $e->getMessage(),
                'error'
            );

            // Lanzar excepción con mensaje detallado para debug
            throw new Exception("Error al actualizar empresa: " . $e->getMessage() . " | SQL State: " . $e->getCode());
        }
    }

    public function verificarEmpresa($codigo_empresa, $nif_empresa, $id_empresa = null)
    {
        try {
            $sql = "SELECT COUNT(*) AS total FROM empresa WHERE (LOWER(codigo_empresa) = LOWER(?) OR LOWER(nif_empresa) = LOWER(?))";
            $params = [$codigo_empresa, $nif_empresa];
    
            if (!empty($id_empresa)) {
                $sql .= " AND id_empresa != ?";
                $params[] = $id_empresa;
            }
    
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute($params);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
    
            return [
                'existe' => ($resultado['total'] > 0)
            ];
    
        } catch (PDOException $e) {
            if (isset($this->registro)) {
                $this->registro->registrarActividad(
                    null,
                    'Empresas',
                    'verificarEmpresa',
                    "Error al verificar existencia de la empresa: " . $e->getMessage(),
                    'error'
                );
            }
    
            return [
                'existe' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Obtiene las observaciones de cabecera por defecto de la empresa activa
     * para pre-cargarlas en nuevos presupuestos
     *
     * @return array ['observaciones_esp' => '...', 'observaciones_eng' => '...']
     */
    public function get_observaciones_por_defecto()
    {
        try {
            // Obtener empresa ficticia principal (la que se usa por defecto)
            $sql = "SELECT
                        observaciones_cabecera_presupuesto_empresa,
                        observaciones_cabecera_ingles_presupuesto_empresa
                    FROM empresa
                    WHERE empresa_ficticia_principal = 1
                      AND activo_empresa = 1
                    LIMIT 1";

            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($resultado) {
                return [
                    'observaciones_esp' => $resultado['observaciones_cabecera_presupuesto_empresa'] ?? '',
                    'observaciones_eng' => $resultado['observaciones_cabecera_ingles_presupuesto_empresa'] ?? ''
                ];
            }

            // Si no hay empresa ficticia principal, devolver vacío
            return [
                'observaciones_esp' => '',
                'observaciones_eng' => ''
            ];

        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'system',
                'Empresas',
                'get_observaciones_por_defecto',
                "Error al obtener observaciones por defecto: " . $e->getMessage(),
                'error'
            );

            return [
                'observaciones_esp' => '',
                'observaciones_eng' => ''
            ];
        }
    }
}
