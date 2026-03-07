<?php

require_once '../config/conexion.php';
require_once '../config/funciones.php';

/**
 * Modelo DocumentoPresupuesto
 *
 * Gestiona los documentos generados a partir de un presupuesto:
 *  - Presupuesto (PDF)
 *  - Parte de trabajo
 *  - Factura proforma
 *  - Factura anticipo
 *  - Factura final
 *  - Factura rectificativa (abono)
 *
 * Reglas de negocio clave:
 *  - Las facturas (anticipo/final/rectificativa) NUNCA se emiten con empresa ficticia.
 *  - Una vez emitida la primera factura (cualquier tipo) para un presupuesto, la empresa
 *    emisora queda BLOQUEADA: todas las facturas siguientes usan la misma empresa.
 *  - Una factura proforma de anticipo NO se abona; se repite (activo_doc=0 + nueva doc).
 *  - Solo se puede abonar una factura_anticipo o factura_final activa.
 *  - Los números de documento se generan con sp_obtener_siguiente_numero y se confirman
 *    inmediatamente con sp_actualizar_contador_empresa (dentro de la misma transacción).
 */
class DocumentoPresupuesto
{
    private $conexion;
    private $registro;

    /**
     * Mapa: tipo_documento_ppto → tipo para las SPs de numeración
     * Los tipos 'presupuesto' y 'parte_trabajo' no consumen serie de factura.
     */
    private const SP_TIPO_MAP = [
        'presupuesto'           => 'presupuesto',
        'parte_trabajo'         => null,            // sin numeración correlativa de empresa
        'factura_proforma'      => 'factura_proforma',
        'factura_anticipo'      => 'factura',
        'factura_final'         => 'factura',
        'factura_rectificativa' => 'abono',
    ];

    public function __construct()
    {
        $this->conexion = (new Conexion())->getConexion();
        $this->registro = new RegistroActividad();

        try {
            $this->conexion->exec("SET time_zone = 'Europe/Madrid'");
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'system',
                'DocumentoPresupuesto',
                '__construct',
                "Error zona horaria: " . $e->getMessage(),
                'warning'
            );
        }
    }

    // ══════════════════════════════════════════════════════════
    //  LECTURA
    // ══════════════════════════════════════════════════════════

    /**
     * Obtiene todos los documentos activos de un presupuesto (vía vista).
     *
     * @param int $id_presupuesto
     * @return array
     */
    public function get_documentos_presupuesto(int $id_presupuesto): array
    {
        try {
            $sql = "SELECT *
                    FROM   v_documentos_presupuesto
                    WHERE  id_presupuesto = ?
                      AND  activo_documento_ppto = 1
                    ORDER  BY fecha_generacion_documento_ppto ASC";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_presupuesto, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin', 'DocumentoPresupuesto', 'get_documentos_presupuesto',
                "Error id_presupuesto=$id_presupuesto: " . $e->getMessage(), 'error'
            );
            return [];
        }
    }

    /**
     * Obtiene todos los documentos de un presupuesto (incluidos inactivos).
     *
     * @param int $id_presupuesto
     * @return array
     */
    public function get_documentos_presupuesto_todos(int $id_presupuesto): array
    {
        try {
            $sql = "SELECT *
                    FROM   v_documentos_presupuesto
                    WHERE  id_presupuesto = ?
                    ORDER  BY fecha_generacion_documento_ppto ASC";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_presupuesto, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin', 'DocumentoPresupuesto', 'get_documentos_presupuesto_todos',
                "Error id_presupuesto=$id_presupuesto: " . $e->getMessage(), 'error'
            );
            return [];
        }
    }

    /**
     * Obtiene un documento por su ID.
     *
     * @param int $id_documento_ppto
     * @return array|false
     */
    public function get_documentoxid(int $id_documento_ppto)
    {
        try {
            $sql = "SELECT *
                    FROM   v_documentos_presupuesto
                    WHERE  id_documento_ppto = ?";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_documento_ppto, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin', 'DocumentoPresupuesto', 'get_documentoxid',
                "Error id=$id_documento_ppto: " . $e->getMessage(), 'error'
            );
            return false;
        }
    }

    /**
     * Obtiene los documentos activos de un presupuesto filtrados por tipo.
     *
     * @param int    $id_presupuesto
     * @param string $tipo  Valor del ENUM tipo_documento_ppto
     * @return array
     */
    public function get_documentos_por_tipo(int $id_presupuesto, string $tipo): array
    {
        try {
            $sql = "SELECT *
                    FROM   v_documentos_presupuesto
                    WHERE  id_presupuesto     = ?
                      AND  tipo_documento_ppto = ?
                      AND  activo_documento_ppto = 1
                    ORDER  BY fecha_generacion_documento_ppto ASC";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_presupuesto, PDO::PARAM_INT);
            $stmt->bindValue(2, $tipo,           PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin', 'DocumentoPresupuesto', 'get_documentos_por_tipo',
                "Error id_presupuesto=$id_presupuesto tipo=$tipo: " . $e->getMessage(), 'error'
            );
            return [];
        }
    }

    /**
     * Obtiene el último documento activo de un tipo para un presupuesto.
     *
     * @param int    $id_presupuesto
     * @param string $tipo
     * @return array|false
     */
    public function get_ultimo_documento_tipo(int $id_presupuesto, string $tipo)
    {
        try {
            $sql = "SELECT *
                    FROM   v_documentos_presupuesto
                    WHERE  id_presupuesto      = ?
                      AND  tipo_documento_ppto  = ?
                      AND  activo_documento_ppto = 1
                    ORDER  BY fecha_generacion_documento_ppto DESC
                    LIMIT  1";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_presupuesto, PDO::PARAM_INT);
            $stmt->bindValue(2, $tipo,           PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin', 'DocumentoPresupuesto', 'get_ultimo_documento_tipo',
                "Error: " . $e->getMessage(), 'error'
            );
            return false;
        }
    }

    /**
     * Obtiene el documento origen de una factura rectificativa (abono).
     *
     * @param int $id_documento_ppto  ID del abono
     * @return array|false
     */
    public function get_factura_origen(int $id_documento_ppto)
    {
        try {
            $sql = "SELECT dp_orig.*
                    FROM   documento_presupuesto dp_abono
                    JOIN   documento_presupuesto dp_orig
                           ON dp_abono.id_documento_origen = dp_orig.id_documento_ppto
                    WHERE  dp_abono.id_documento_ppto = ?
                      AND  dp_abono.activo_documento_ppto = 1";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_documento_ppto, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin', 'DocumentoPresupuesto', 'get_factura_origen',
                "Error id=$id_documento_ppto: " . $e->getMessage(), 'error'
            );
            return false;
        }
    }

    // ══════════════════════════════════════════════════════════
    //  VALIDACIONES
    // ══════════════════════════════════════════════════════════

    /**
     * Comprueba si ya existe un documento activo del tipo indicado para el presupuesto.
     *
     * @param int    $id_presupuesto
     * @param string $tipo
     * @return bool
     */
    public function verificar_existe_tipo(int $id_presupuesto, string $tipo): bool
    {
        try {
            $sql = "SELECT COUNT(*) AS total
                    FROM   documento_presupuesto
                    WHERE  id_presupuesto      = ?
                      AND  tipo_documento_ppto  = ?
                      AND  activo_documento_ppto = 1";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_presupuesto, PDO::PARAM_INT);
            $stmt->bindValue(2, $tipo,           PDO::PARAM_STR);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)$row['total'] > 0;

        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin', 'DocumentoPresupuesto', 'verificar_existe_tipo',
                "Error: " . $e->getMessage(), 'error'
            );
            return false;
        }
    }

    /**
     * Verifica si un documento puede ser abonado.
     * Requisitos:
     *  - Tipo: factura_anticipo o factura_final
     *  - Activo
     *  - No tiene ya una factura_rectificativa activa asociada
     *
     * @param int $id_documento_ppto
     * @return array ['puede' => bool, 'motivo' => string]
     */
    public function verificar_puede_abonar(int $id_documento_ppto): array
    {
        try {
            // Obtener el documento
            $sql = "SELECT tipo_documento_ppto, activo_documento_ppto
                    FROM   documento_presupuesto
                    WHERE  id_documento_ppto = ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_documento_ppto, PDO::PARAM_INT);
            $stmt->execute();
            $doc = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$doc) {
                return ['puede' => false, 'motivo' => 'Documento no encontrado'];
            }

            if (!$doc['activo_documento_ppto']) {
                return ['puede' => false, 'motivo' => 'El documento está inactivo'];
            }

            if (!in_array($doc['tipo_documento_ppto'], ['factura_anticipo', 'factura_final', 'factura_proforma'])) {
                return [
                    'puede'  => false,
                    'motivo' => 'Solo se pueden abonar facturas de anticipo, finales o proforma'
                ];
            }

            // Comprobar si ya tiene una rectificativa activa
            $sql2 = "SELECT COUNT(*) AS total
                     FROM   documento_presupuesto
                     WHERE  id_documento_origen     = ?
                       AND  tipo_documento_ppto       = 'factura_rectificativa'
                       AND  activo_documento_ppto     = 1";
            $stmt2 = $this->conexion->prepare($sql2);
            $stmt2->bindValue(1, $id_documento_ppto, PDO::PARAM_INT);
            $stmt2->execute();
            $row2 = $stmt2->fetch(PDO::FETCH_ASSOC);

            if ((int)$row2['total'] > 0) {
                return ['puede' => false, 'motivo' => 'Este documento ya tiene una factura de abono activa'];
            }

            return ['puede' => true, 'motivo' => ''];

        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin', 'DocumentoPresupuesto', 'verificar_puede_abonar',
                "Error id=$id_documento_ppto: " . $e->getMessage(), 'error'
            );
            return ['puede' => false, 'motivo' => 'Error interno al validar'];
        }
    }

    /**
     * Verifica que una empresa NO es ficticia (requerimiento para emitir facturas).
     *
     * @param int $id_empresa
     * @return bool  true = es real, false = es ficticia o no existe
     */
    public function verificar_empresa_real(int $id_empresa): bool
    {
        try {
            $sql = "SELECT ficticia_empresa
                    FROM   empresa
                    WHERE  id_empresa    = ?
                      AND  activo_empresa = 1";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_empresa, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$row) {
                return false;
            }
            return !(bool)$row['ficticia_empresa'];

        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin', 'DocumentoPresupuesto', 'verificar_empresa_real',
                "Error id_empresa=$id_empresa: " . $e->getMessage(), 'error'
            );
            return false;
        }
    }

    /**
     * Obtiene la empresa que ya ha emitido la primera factura para este presupuesto.
     * Una vez existe cualquier factura (anticipo/final/rectificativa) con empresa real,
     * esa empresa queda bloqueada para todas las facturas del presupuesto.
     *
     * @param int $id_presupuesto
     * @return array|false  ['id_empresa', 'codigo_empresa', 'nombre_empresa', ...] o false
     */
    public function obtener_empresa_bloqueada_presupuesto(int $id_presupuesto)
    {
        try {
            $sql = "SELECT e.*
                    FROM   documento_presupuesto dp
                    JOIN   empresa e ON dp.id_empresa = e.id_empresa
                    WHERE  dp.id_presupuesto       = ?
                      AND  dp.tipo_documento_ppto    IN ('factura_proforma','factura_anticipo','factura_final')
                      AND  dp.activo_documento_ppto  = 1
                      AND  e.ficticia_empresa        = 0
                    ORDER  BY dp.fecha_generacion_documento_ppto ASC
                    LIMIT  1";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_presupuesto, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin', 'DocumentoPresupuesto', 'obtener_empresa_bloqueada_presupuesto',
                "Error id_presupuesto=$id_presupuesto: " . $e->getMessage(), 'error'
            );
            return false;
        }
    }

    // ══════════════════════════════════════════════════════════
    //  NUMERACIÓN (usa SPs)
    // ══════════════════════════════════════════════════════════

    /**
     * Obtiene el siguiente número de documento SIN actualizar el contador de empresa.
     * Llama a sp_obtener_siguiente_numero.
     *
     * @param string $codigo_empresa
     * @param string $tipo_sp  Uno de: presupuesto | factura | factura_proforma | abono
     * @return string|false  El número generado (ej. "FP2025/0001") o false en error
     */
    public function obtener_siguiente_numero(string $codigo_empresa, string $tipo_sp)
    {
        try {
            $this->conexion->exec("CALL sp_obtener_siguiente_numero(
                '$codigo_empresa', '$tipo_sp', @numero_completo
            )");
            $row = $this->conexion->query("SELECT @numero_completo AS numero")->fetch(PDO::FETCH_ASSOC);
            return $row['numero'] ?? false;

        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin', 'DocumentoPresupuesto', 'obtener_siguiente_numero',
                "Error empresa=$codigo_empresa tipo=$tipo_sp: " . $e->getMessage(), 'error'
            );
            return false;
        }
    }

    // ══════════════════════════════════════════════════════════
    //  ESCRITURA
    // ══════════════════════════════════════════════════════════

    /**
     * Inserta un nuevo documento de presupuesto.
     * Si el tipo requiere numeración correlativa, llama a las SPs dentro de una transacción.
     *
     * @param array $datos {
     *   id_presupuesto          int       (obligatorio)
     *   id_version_presupuesto  int       (obligatorio)
     *   id_empresa              int       (obligatorio)
     *   tipo_documento_ppto     string    (obligatorio)
     *   fecha_emision           string    YYYY-MM-DD (obligatorio)
     *   subtotal_documento_ppto float     (opcional, default null)
     *   total_iva_documento_ppto float    (opcional, default null)
     *   total_documento_ppto    float     (opcional, default null)
     *   id_documento_origen     int       (opcional, para rectificativas)
     *   motivo_abono_documento_ppto string (opcional)
     *   observaciones_documento_ppto string (opcional)
     *   seleccion_manual_empresa_documento_ppto bool (opcional, default 0)
     * }
     * @return int|false  ID del nuevo documento o false en error
     */
    public function insert_documento(array $datos)
    {
        $tipo = $datos['tipo_documento_ppto'];
        $tipo_sp = self::SP_TIPO_MAP[$tipo] ?? null;

        // Si es una rectificativa de una factura proforma → usar contador independiente
        if ($tipo === 'factura_rectificativa' && !empty($datos['id_documento_origen'])) {
            try {
                $stmt_orig = $this->conexion->prepare(
                    "SELECT tipo_documento_ppto FROM documento_presupuesto WHERE id_documento_ppto = ?"
                );
                $stmt_orig->bindValue(1, $datos['id_documento_origen'], PDO::PARAM_INT);
                $stmt_orig->execute();
                $orig = $stmt_orig->fetch(PDO::FETCH_ASSOC);
                if ($orig && $orig['tipo_documento_ppto'] === 'factura_proforma') {
                    $tipo_sp = 'abono_proforma';
                }
            } catch (PDOException $e) {
                // Si falla la consulta, usar el tipo por defecto ('abono')
                $this->registro->registrarActividad(
                    'admin', 'DocumentoPresupuesto', 'insert_documento',
                    "No se pudo determinar tipo doc origen: " . $e->getMessage(), 'warning'
                );
            }
        }

        try {
            $this->conexion->beginTransaction();

            $numero_documento = null;
            $serie_documento  = null;

            // Si tiene numeración correlativa, obtener y reservar el número
            if ($tipo_sp !== null) {
                // Obtener codigo_empresa
                $stmt_cod = $this->conexion->prepare(
                    "SELECT codigo_empresa FROM empresa WHERE id_empresa = ? AND activo_empresa = 1"
                );
                $stmt_cod->bindValue(1, $datos['id_empresa'], PDO::PARAM_INT);
                $stmt_cod->execute();
                $emp = $stmt_cod->fetch(PDO::FETCH_ASSOC);

                if (!$emp) {
                    $this->conexion->rollBack();
                    return false;
                }

                $codigo_empresa = $emp['codigo_empresa'];

                // Llamar SP de numeración
                $this->conexion->exec(
                    "CALL sp_obtener_siguiente_numero(
                        " . $this->conexion->quote($codigo_empresa) . ",
                        " . $this->conexion->quote($tipo_sp) . ",
                        @v_numero
                    )"
                );
                $row_num = $this->conexion->query("SELECT @v_numero AS numero")->fetch(PDO::FETCH_ASSOC);
                $numero_documento = $row_num['numero'] ?? null;

                if (!$numero_documento) {
                    $this->conexion->rollBack();
                    $this->registro->registrarActividad(
                        'admin', 'DocumentoPresupuesto', 'insert_documento',
                        "SP no devolvió número para empresa=$codigo_empresa tipo=$tipo_sp", 'error'
                    );
                    return false;
                }

                // Actualizar contador (confirmar reserva)
                $this->conexion->exec(
                    "CALL sp_actualizar_contador_empresa({$datos['id_empresa']}, " .
                    $this->conexion->quote($tipo_sp) . ")"
                );
            }

            // Insertar registro
            $fecha_emision = $datos['fecha_emision'] ?? date('Y-m-d');
            $total_doc     = $datos['total_documento_ppto'] ?? $datos['importe_documento_ppto'] ?? null;

            $sql = "INSERT INTO documento_presupuesto (
                        id_presupuesto,
                        id_version_presupuesto,
                        id_empresa,
                        seleccion_manual_empresa_documento_ppto,
                        tipo_documento_ppto,
                        numero_documento_ppto,
                        id_documento_origen,
                        motivo_abono_documento_ppto,
                        subtotal_documento_ppto,
                        total_iva_documento_ppto,
                        total_documento_ppto,
                        fecha_emision_documento_ppto,
                        fecha_generacion_documento_ppto,
                        observaciones_documento_ppto
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?)";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1,  $datos['id_presupuesto'],         PDO::PARAM_INT);
            $stmt->bindValue(2,  $datos['id_version_presupuesto'] ?? null, PDO::PARAM_INT);
            $stmt->bindValue(3,  $datos['id_empresa'],             PDO::PARAM_INT);
            $stmt->bindValue(4,  (int)($datos['seleccion_manual_empresa_documento_ppto'] ?? 0), PDO::PARAM_INT);
            $stmt->bindValue(5,  $tipo,                            PDO::PARAM_STR);
            $stmt->bindValue(6,  $numero_documento,                !empty($numero_documento) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(7,  $datos['id_documento_origen']     ?? null, !empty($datos['id_documento_origen'])     ? PDO::PARAM_INT : PDO::PARAM_NULL);
            $stmt->bindValue(8,  $datos['motivo_abono_documento_ppto'] ?? null, !empty($datos['motivo_abono_documento_ppto']) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(9,  $datos['subtotal_documento_ppto'] ?? null, isset($datos['subtotal_documento_ppto'])  ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(10, $datos['total_iva_documento_ppto'] ?? null, isset($datos['total_iva_documento_ppto']) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(11, $total_doc,                       isset($total_doc) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(12, $fecha_emision,                   PDO::PARAM_STR);
            $stmt->bindValue(13, $datos['observaciones_documento_ppto'] ?? null, !empty($datos['observaciones_documento_ppto']) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->execute();

            $id = (int)$this->conexion->lastInsertId();
            $this->conexion->commit();

            $this->registro->registrarActividad(
                'admin', 'DocumentoPresupuesto', 'insert_documento',
                "Documento creado ID=$id tipo=$tipo numero=" . ($numero_documento ?? 'N/A') .
                " presupuesto={$datos['id_presupuesto']}", 'info'
            );

            return $id;

        } catch (PDOException $e) {
            if ($this->conexion->inTransaction()) {
                $this->conexion->rollBack();
            }
            $this->registro->registrarActividad(
                'admin', 'DocumentoPresupuesto', 'insert_documento',
                "Error tipo=$tipo presupuesto=" . ($datos['id_presupuesto'] ?? '?') . ": " . $e->getMessage(), 'error'
            );
            return false;
        }
    }

    /**
     * Actualiza la ruta del PDF generado y su tamaño en bytes.
     *
     * @param int    $id_documento_ppto
     * @param string $ruta_pdf   Ruta relativa al documento generado
     * @param int    $tamano_pdf Tamaño en bytes
     * @return bool
     */
    public function actualizar_ruta_pdf(int $id_documento_ppto, string $ruta_pdf, int $tamano_pdf = 0): bool
    {
        try {
            $sql = "UPDATE documento_presupuesto
                    SET    ruta_pdf_documento_ppto    = ?,
                           tamano_pdf_documento_ppto  = ?,
                           updated_at_documento_ppto  = NOW()
                    WHERE  id_documento_ppto = ?";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $ruta_pdf,           PDO::PARAM_STR);
            $stmt->bindValue(2, $tamano_pdf,          PDO::PARAM_INT);
            $stmt->bindValue(3, $id_documento_ppto,   PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->rowCount() > 0;

        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin', 'DocumentoPresupuesto', 'actualizar_ruta_pdf',
                "Error id=$id_documento_ppto: " . $e->getMessage(), 'error'
            );
            return false;
        }
    }

    /**
     * Actualiza los importes de un documento (útil si se calcula tras la inserción).
     *
     * @param int   $id_documento_ppto
     * @param float $subtotal
     * @param float $total_iva
     * @param float $total
     * @return bool
     */
    public function actualizar_importes(int $id_documento_ppto, float $subtotal, float $total_iva, float $total): bool
    {
        try {
            $sql = "UPDATE documento_presupuesto
                    SET    subtotal_documento_ppto     = ?,
                           total_iva_documento_ppto    = ?,
                           total_documento_ppto        = ?,
                           updated_at_documento_ppto   = NOW()
                    WHERE  id_documento_ppto = ?";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $subtotal,          PDO::PARAM_STR);
            $stmt->bindValue(2, $total_iva,         PDO::PARAM_STR);
            $stmt->bindValue(3, $total,             PDO::PARAM_STR);
            $stmt->bindValue(4, $id_documento_ppto, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->rowCount() > 0;

        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin', 'DocumentoPresupuesto', 'actualizar_importes',
                "Error id=$id_documento_ppto: " . $e->getMessage(), 'error'
            );
            return false;
        }
    }

    /**
     * Soft delete de un documento (activo_documento_ppto = 0).
     *
     * @param int $id_documento_ppto
     * @return bool
     */
    public function delete_documentoxid(int $id_documento_ppto): bool
    {
        try {
            $sql = "UPDATE documento_presupuesto
                    SET    activo_documento_ppto     = 0,
                           updated_at_documento_ppto = NOW()
                    WHERE  id_documento_ppto = ?";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_documento_ppto, PDO::PARAM_INT);
            $stmt->execute();

            $ok = $stmt->rowCount() > 0;

            if ($ok) {
                $this->registro->registrarActividad(
                    'admin', 'DocumentoPresupuesto', 'delete_documentoxid',
                    "Documento desactivado ID=$id_documento_ppto", 'info'
                );
            }

            return $ok;

        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin', 'DocumentoPresupuesto', 'delete_documentoxid',
                "Error id=$id_documento_ppto: " . $e->getMessage(), 'error'
            );
            return false;
        }
    }

    /**
     * Reactiva un documento (activo_documento_ppto = 1).
     *
     * @param int $id_documento_ppto
     * @return bool
     */
    public function activar_documentoxid(int $id_documento_ppto): bool
    {
        try {
            $sql = "UPDATE documento_presupuesto
                    SET    activo_documento_ppto     = 1,
                           updated_at_documento_ppto = NOW()
                    WHERE  id_documento_ppto = ?";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_documento_ppto, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->rowCount() > 0;

        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin', 'DocumentoPresupuesto', 'activar_documentoxid',
                "Error id=$id_documento_ppto: " . $e->getMessage(), 'error'
            );
            return false;
        }
    }

    /**
     * Repite una factura proforma de anticipo:
     *  1. Desactiva la proforma anterior (activo = 0).
     *  2. Crea una nueva proforma con los mismos datos base.
     *
     * NOTA: Las proformas de anticipos NO se abonan, se repiten.
     *
     * @param int   $id_documento_origen  ID de la proforma a sustituir
     * @param array $datos_nuevo          Datos para la nueva proforma (misma estructura que insert_documento)
     * @return int|false  ID de la nueva proforma o false en error
     */
    public function repetir_proforma_anticipo(int $id_documento_origen, array $datos_nuevo)
    {
        try {
            // Verificar que el origen es una proforma
            $orig = $this->get_documentoxid($id_documento_origen);
            if (!$orig || $orig['tipo_documento_ppto'] !== 'factura_proforma') {
                $this->registro->registrarActividad(
                    'admin', 'DocumentoPresupuesto', 'repetir_proforma_anticipo',
                    "El doc ID=$id_documento_origen no es una factura_proforma activa", 'error'
                );
                return false;
            }

            $this->conexion->beginTransaction();

            // Desactivar la proforma anterior
            $sql_off = "UPDATE documento_presupuesto
                        SET    activo_documento_ppto     = 0,
                               updated_at_documento_ppto = NOW()
                        WHERE  id_documento_ppto = ?";
            $stmt_off = $this->conexion->prepare($sql_off);
            $stmt_off->bindValue(1, $id_documento_origen, PDO::PARAM_INT);
            $stmt_off->execute();

            $this->conexion->commit();

            // Insertar nueva proforma (insert_documento gestiona su propia transacción)
            $datos_nuevo['tipo_documento_ppto'] = 'factura_proforma';
            $id_nuevo = $this->insert_documento($datos_nuevo);

            if (!$id_nuevo) {
                // Si falla la inserción, reactivar la anterior
                $sql_on = "UPDATE documento_presupuesto
                           SET    activo_documento_ppto     = 1,
                                  updated_at_documento_ppto = NOW()
                           WHERE  id_documento_ppto = ?";
                $stmt_on = $this->conexion->prepare($sql_on);
                $stmt_on->bindValue(1, $id_documento_origen, PDO::PARAM_INT);
                $stmt_on->execute();
                return false;
            }

            $this->registro->registrarActividad(
                'admin', 'DocumentoPresupuesto', 'repetir_proforma_anticipo',
                "Proforma repetida: antigua ID=$id_documento_origen → nueva ID=$id_nuevo", 'info'
            );

            return $id_nuevo;

        } catch (PDOException $e) {
            if ($this->conexion->inTransaction()) {
                $this->conexion->rollBack();
            }
            $this->registro->registrarActividad(
                'admin', 'DocumentoPresupuesto', 'repetir_proforma_anticipo',
                "Error id_origen=$id_documento_origen: " . $e->getMessage(), 'error'
            );
            return false;
        }
    }
}
