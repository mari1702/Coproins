<?php
require '../bd/db_conexion.php';

if (!isset($_GET['id_cliente'])) {
    echo json_encode(["error" => "No se proporcionó el ID del cliente."]);
    exit();
}

$id_cliente = $_GET['id_cliente'];

$sql = "
    SELECT 
        c.id_cliente, 
        c.cliente, 
        c.telefono_cliente,

        -- Costo Inicial
        COALESCE((
            SELECT SUM(h.costo)
            FROM historial_costo_proyecto h
            INNER JOIN (
                SELECT MIN(h2.fecha_modificacion) AS primera_fecha, h2.id_nuevo_proyecto
                FROM historial_costo_proyecto h2
                INNER JOIN nuevo_proyecto np2 ON np2.id_nuevo_proyecto = h2.id_nuevo_proyecto
                WHERE np2.id_cliente = c.id_cliente
                GROUP BY h2.id_nuevo_proyecto
            ) primera_costos 
            ON h.id_nuevo_proyecto = primera_costos.id_nuevo_proyecto AND h.fecha_modificacion = primera_costos.primera_fecha
        ), 0) AS costo_inicial_total,

        -- Costo Final
        COALESCE((
            SELECT SUM(h.costo)
            FROM historial_costo_proyecto h
            INNER JOIN (
                SELECT MAX(h2.fecha_modificacion) AS ultima_fecha, h2.id_nuevo_proyecto
                FROM historial_costo_proyecto h2
                INNER JOIN nuevo_proyecto np2 ON np2.id_nuevo_proyecto = h2.id_nuevo_proyecto
                WHERE np2.id_cliente = c.id_cliente
                GROUP BY h2.id_nuevo_proyecto
            ) ultima_costos 
            ON h.id_nuevo_proyecto = ultima_costos.id_nuevo_proyecto AND h.fecha_modificacion = ultima_costos.ultima_fecha
        ), 0) AS costo_final_total,

        COALESCE(SUM(CASE WHEN g.tipo_gasto = 'ingreso' THEN g.monto ELSE 0 END), 0) AS total_ingresos,
        COALESCE(SUM(CASE WHEN g.tipo_gasto = 'egreso' THEN g.monto ELSE 0 END), 0) AS total_egresos

    FROM cliente c
    LEFT JOIN gasto g ON g.id_cliente = c.id_cliente
    WHERE c.id_cliente = :id_cliente
    GROUP BY c.id_cliente, c.cliente, c.telefono_cliente
";

$stmt = $pdo->prepare($sql);
$stmt->execute(['id_cliente' => $id_cliente]);
$cliente = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$cliente) {
    echo json_encode(["error" => "No se encontraron datos para el cliente con ID: $id_cliente"]);
    exit();
}


echo json_encode($cliente);
?>
