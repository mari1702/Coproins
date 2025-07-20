<?php
require '../bd/db_conexion.php';

if (isset($_GET['id_empresa'])) {
    $idEmpresa = $_GET['id_empresa'];

    try {
        $stmt = $pdo->prepare("SELECT id_contacto, nombre FROM contacto WHERE id_cliente = :idEmpresa");
        $stmt->bindParam(':idEmpresa', $idEmpresa, PDO::PARAM_INT);
        $stmt->execute();
        $contactos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($contactos);
    } catch (PDOException $e) {
        echo json_encode(["error" => "Error en la base de datos: " . $e->getMessage()]);
    }
}
?>
