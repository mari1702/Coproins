<?php
require '../bd/db_conexion.php';

$idCliente = $_GET['id_cliente'] ?? null;

if ($idCliente) {
    // Modificar la consulta para filtrar por estado_proyecto
    $query = "
        SELECT id_nuevo_proyecto, localidad, estado_proyecto
        FROM nuevo_proyecto
        WHERE id_cliente = :id_cliente
        AND estado_proyecto != 'Finalizado'"; // Filtrar las sucursales con estado 'Finalizado'

    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':id_cliente', $idCliente, PDO::PARAM_INT);
    $stmt->execute();

    // Obtener los resultados y devolverlos como JSON
    $sucursales = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($sucursales);
}
?>
