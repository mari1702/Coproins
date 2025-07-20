<?php
require '../bd/db_conexion.php';

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $idCliente = $_POST["id_cliente"] ?? null;
    $nuevoNombre = $_POST["cliente"] ?? null;
    $nuevoTelefono = $_POST["telefono"] ?? null;

    if (!$idCliente || !$nuevoNombre || !$nuevoTelefono) {
        echo json_encode(["success" => false, "error" => "Datos incompletos"]);
        exit;
    }

    try {
        $stmt = $pdo->prepare("UPDATE cliente SET cliente = :cliente, telefono_cliente = :telefono WHERE id_cliente = :idCliente");
        $stmt->execute([
            ":cliente" => $nuevoNombre,
            ":telefono" => $nuevoTelefono,
            ":idCliente" => $idCliente
        ]);

        echo json_encode(["success" => true]);
    } catch (Exception $e) {
        echo json_encode(["success" => false, "error" => $e->getMessage()]);
    }
}
?>
