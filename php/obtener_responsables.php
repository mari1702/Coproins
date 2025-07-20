<?php
require '../bd/db_conexion.php';

header('Content-Type: application/json');

if (isset($_GET['id_empresa'])) {
    $idEmpresa = $_GET['id_empresa'];

    $stmt = $pdo->prepare("SELECT id_encargado, nombre_completo, telefono_encargado 
                           FROM encargado 
                           WHERE id_cliente = :idEmpresa");
    $stmt->execute([':idEmpresa' => $idEmpresa]);
    $responsables = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($responsables);
} else {
    echo json_encode([]);
}
?>
