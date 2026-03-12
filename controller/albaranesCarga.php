<?php

require_once "../config/conexion.php";
require_once "../config/funciones.php";

$registro  = new RegistroActividad();
$conexion  = (new Conexion())->getConexion();

switch ($_GET["op"] ?? '') {

    // ------------------------------------------------------------------
    // LISTAR: presupuestos aprobados con evento futuro (o sin fecha)
    // ------------------------------------------------------------------
    case "listar":

        try {
            $sql = "
                SELECT
                    p.id_presupuesto,
                    p.numero_presupuesto,
                    IFNULL(p.nombre_evento_presupuesto, '(Sin nombre)') AS nombre_evento_presupuesto,
                    p.fecha_inicio_evento_presupuesto,
                    p.fecha_fin_evento_presupuesto,
                    c.nombre_cliente,
                    MIN(vlp.fecha_montaje_linea_ppto)      AS fecha_montaje_min,
                    MAX(vlp.fecha_desmontaje_linea_ppto)   AS fecha_desmontaje_max
                FROM presupuesto p
                INNER JOIN cliente c
                    ON p.id_cliente = c.id_cliente
                INNER JOIN estado_presupuesto ep
                    ON p.id_estado_ppto = ep.id_estado_ppto
                INNER JOIN presupuesto_version pv
                    ON pv.id_presupuesto = p.id_presupuesto
                   AND pv.numero_version_presupuesto = p.version_actual_presupuesto
                LEFT JOIN v_linea_presupuesto_calculada vlp
                    ON vlp.id_version_presupuesto = pv.id_version_presupuesto
                WHERE ep.codigo_estado_ppto = 'APROB'
                  AND p.activo_presupuesto = 1
                  AND (
                      p.fecha_inicio_evento_presupuesto IS NULL
                      OR p.fecha_inicio_evento_presupuesto >= CURDATE()
                  )
                GROUP BY
                    p.id_presupuesto,
                    p.numero_presupuesto,
                    p.nombre_evento_presupuesto,
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
            foreach ($datos as $row) {

                // Fechas evento
                $f_ini = $row['fecha_inicio_evento_presupuesto']
                    ? date('d/m/Y', strtotime($row['fecha_inicio_evento_presupuesto'])) : '—';
                $f_fin = $row['fecha_fin_evento_presupuesto']
                    ? date('d/m/Y', strtotime($row['fecha_fin_evento_presupuesto'])) : '';
                $fecha_evento = $f_ini . ($f_fin ? ' → ' . $f_fin : '');

                // Fechas montaje / desmontaje
                $fecha_montaje     = $row['fecha_montaje_min']
                    ? date('d/m/Y', strtotime($row['fecha_montaje_min'])) : '—';
                $fecha_desmontaje  = $row['fecha_desmontaje_max']
                    ? date('d/m/Y', strtotime($row['fecha_desmontaje_max'])) : '—';

                $id = (int)$row['id_presupuesto'];

                $opciones =
                    '<div class="d-flex gap-1 justify-content-center">' .
                    '<button class="btn btn-warning btn-sm" ' .
                        'onclick="abrirAlbaran(' . $id . ',\'albaran_carga_m2\')" ' .
                        'title="Albarán de carga completo">' .
                        '<i class="fas fa-clipboard-list"></i>' .
                    '</button>' .
                    '<button class="btn btn-info btn-sm" ' .
                        'onclick="abrirAlbaran(' . $id . ',\'albaran_carga_resumido\')" ' .
                        'title="Albarán de carga resumido (por artículo)">' .
                        '<i class="fas fa-boxes"></i>' .
                    '</button>' .
                    '</div>';

                $data[] = [
                    'id_presupuesto'             => $id,
                    'numero_presupuesto'         => htmlspecialchars($row['numero_presupuesto'], ENT_QUOTES, 'UTF-8'),
                    'nombre_evento_presupuesto'  => htmlspecialchars($row['nombre_evento_presupuesto'], ENT_QUOTES, 'UTF-8'),
                    'nombre_cliente'             => htmlspecialchars($row['nombre_cliente'], ENT_QUOTES, 'UTF-8'),
                    'fecha_evento'               => $fecha_evento,
                    'fecha_montaje'              => $fecha_montaje,
                    'fecha_desmontaje'           => $fecha_desmontaje,
                    'opciones'                   => $opciones,
                ];
            }

            $results = [
                'draw'            => intval($_POST['draw'] ?? 1),
                'recordsTotal'    => count($data),
                'recordsFiltered' => count($data),
                'data'            => $data,
            ];

            header('Content-Type: application/json');
            echo json_encode($results, JSON_UNESCAPED_UNICODE);

        } catch (PDOException $e) {
            $registro->registrarActividad(
                'admin', 'albaranesCarga.php', 'listar',
                'Error: ' . $e->getMessage(), 'error'
            );
            header('Content-Type: application/json');
            echo json_encode([
                'draw' => 1, 'recordsTotal' => 0, 'recordsFiltered' => 0, 'data' => [],
                'error' => 'Error al cargar los datos',
            ], JSON_UNESCAPED_UNICODE);
        }
        break;

    default:
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Operación no reconocida'], JSON_UNESCAPED_UNICODE);
        break;
}
?>
