<?php

require_once "../config/conexion.php";
require_once "../config/funciones.php";

$registro = new RegistroActividad();
$conexion = (new Conexion())->getConexion();

switch ($_GET["op"] ?? '') {

    // ------------------------------------------------------------------
    // LISTAR: presupuestos aprobados en rango de -3 días hasta +14 días
    // Clasifica cada evento en columna: montaje | en_curso | desmontaje
    // ------------------------------------------------------------------
    case "listar":

        try {
            $sql = "
                SELECT
                    p.id_presupuesto,
                    p.numero_presupuesto,
                    IFNULL(p.nombre_evento_presupuesto, '(Sin nombre)')  AS nombre_evento_presupuesto,
                    IFNULL(p.direccion_evento_presupuesto, '')           AS direccion_evento_presupuesto,
                    IFNULL(p.poblacion_evento_presupuesto, '')           AS poblacion_evento_presupuesto,
                    IFNULL(p.provincia_evento_presupuesto, '')           AS provincia_evento_presupuesto,
                    p.fecha_inicio_evento_presupuesto,
                    p.fecha_fin_evento_presupuesto,
                    c.nombre_cliente,
                    MIN(vlp.fecha_montaje_linea_ppto)    AS fecha_montaje_min,
                    MAX(vlp.fecha_desmontaje_linea_ppto) AS fecha_desmontaje_max
                FROM presupuesto p
                INNER JOIN cliente c
                    ON p.id_cliente = c.id_cliente
                INNER JOIN estado_presupuesto ep
                    ON p.id_estado_ppto = ep.id_estado_ppto
                INNER JOIN presupuesto_version pv
                    ON pv.id_presupuesto    = p.id_presupuesto
                   AND pv.numero_version_presupuesto = p.version_actual_presupuesto
                LEFT JOIN v_linea_presupuesto_calculada vlp
                    ON vlp.id_version_presupuesto = pv.id_version_presupuesto
                WHERE ep.codigo_estado_ppto = 'APROB'
                  AND p.activo_presupuesto  = 1
                  AND (
                      -- Sin fecha asignada: siempre mostrar
                      p.fecha_inicio_evento_presupuesto IS NULL
                      OR (
                          -- Evento que empieza dentro de los próximos 14 días
                          p.fecha_inicio_evento_presupuesto <= DATE_ADD(CURDATE(), INTERVAL 14 DAY)
                          -- Evento que no terminó hace más de 3 días
                          AND IFNULL(p.fecha_fin_evento_presupuesto, p.fecha_inicio_evento_presupuesto) >= DATE_SUB(CURDATE(), INTERVAL 3 DAY)
                      )
                  )
                GROUP BY
                    p.id_presupuesto,
                    p.numero_presupuesto,
                    p.nombre_evento_presupuesto,
                    p.direccion_evento_presupuesto,
                    p.poblacion_evento_presupuesto,
                    p.provincia_evento_presupuesto,
                    p.fecha_inicio_evento_presupuesto,
                    p.fecha_fin_evento_presupuesto,
                    c.nombre_cliente
                ORDER BY
                    (p.fecha_inicio_evento_presupuesto IS NULL) ASC,
                    p.fecha_inicio_evento_presupuesto ASC
            ";

            $stmt = $conexion->prepare($sql);
            $stmt->execute();
            $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $data = [];
            $hoy  = new DateTime('today');

            foreach ($datos as $row) {

                // ---- Calcular columna Kanban ----
                $fecha_montaje_dt    = $row['fecha_montaje_min']    ? new DateTime($row['fecha_montaje_min'])    : null;
                $fecha_desmontaje_dt = $row['fecha_desmontaje_max'] ? new DateTime($row['fecha_desmontaje_max']) : null;
                $fecha_inicio_dt     = $row['fecha_inicio_evento_presupuesto'] ? new DateTime($row['fecha_inicio_evento_presupuesto']) : null;
                $fecha_fin_dt        = $row['fecha_fin_evento_presupuesto']    ? new DateTime($row['fecha_fin_evento_presupuesto'])    : null;

                // Usamos fechas de montaje si existen, si no, fechas del evento
                $inicio_ref = $fecha_montaje_dt    ?? $fecha_inicio_dt;
                $fin_ref    = $fecha_desmontaje_dt ?? $fecha_fin_dt ?? $inicio_ref;

                if ($inicio_ref === null) {
                    $columna = 'montaje'; // Sin fechas: colocar en montaje
                } elseif ($inicio_ref > $hoy) {
                    $columna = 'montaje';     // Todavía no ha empezado el montaje
                } elseif ($fin_ref !== null && $fin_ref >= $hoy) {
                    $columna = 'en_curso';    // Montaje ya empezó y desmontaje aún no ha terminado
                } else {
                    $columna = 'desmontaje';  // El servicio ya terminó (desmontaje reciente)
                }

                // ---- Formatear fechas para mostrar ----
                $f_inicio = $row['fecha_inicio_evento_presupuesto']
                    ? date('d/m/Y', strtotime($row['fecha_inicio_evento_presupuesto'])) : null;
                $f_fin = $row['fecha_fin_evento_presupuesto']
                    ? date('d/m/Y', strtotime($row['fecha_fin_evento_presupuesto'])) : null;

                $fecha_montaje_str    = $row['fecha_montaje_min']    ? date('d/m/Y', strtotime($row['fecha_montaje_min']))    : null;
                $fecha_desmontaje_str = $row['fecha_desmontaje_max'] ? date('d/m/Y', strtotime($row['fecha_desmontaje_max'])) : null;

                // Ubicación del evento
                $ubicacion_parts = array_filter([
                    htmlspecialchars($row['direccion_evento_presupuesto'], ENT_QUOTES, 'UTF-8'),
                    htmlspecialchars($row['poblacion_evento_presupuesto'], ENT_QUOTES, 'UTF-8'),
                    htmlspecialchars($row['provincia_evento_presupuesto'], ENT_QUOTES, 'UTF-8'),
                ]);
                $ubicacion = implode(', ', $ubicacion_parts);

                $data[] = [
                    'id_presupuesto'            => (int)$row['id_presupuesto'],
                    'numero_presupuesto'        => htmlspecialchars($row['numero_presupuesto'], ENT_QUOTES, 'UTF-8'),
                    'nombre_evento'             => htmlspecialchars($row['nombre_evento_presupuesto'], ENT_QUOTES, 'UTF-8'),
                    'nombre_cliente'            => htmlspecialchars($row['nombre_cliente'], ENT_QUOTES, 'UTF-8'),
                    'ubicacion'                 => $ubicacion,
                    'fecha_inicio'              => $f_inicio,
                    'fecha_fin'                 => $f_fin,
                    'fecha_montaje'             => $fecha_montaje_str,
                    'fecha_desmontaje'          => $fecha_desmontaje_str,
                    'columna'                   => $columna,
                ];
            }

            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'data' => $data, 'total' => count($data)], JSON_UNESCAPED_UNICODE);

        } catch (PDOException $e) {
            $registro->registrarActividad('admin', 'calendarioTecnicos.php', 'listar', "Error: " . $e->getMessage(), 'error');
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Error al cargar los datos.'], JSON_UNESCAPED_UNICODE);
        }
        break;

    default:
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Operación no válida'], JSON_UNESCAPED_UNICODE);
        exit;
}
?>
