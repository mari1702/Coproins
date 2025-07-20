<?php
require '../bd/db_conexion.php';
session_start();

header('Content-Type: application/json'); // 👈 Asegura que devuelve JSON

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_cliente'])) {
    $idCliente = $_POST['id_cliente'];

    try {
        $pdo->beginTransaction();

        // 1️⃣ Eliminar los gastos relacionados con el cliente
        $stmt = $pdo->prepare("DELETE FROM gasto WHERE id_cliente = :id_cliente");
        $stmt->execute([':id_cliente' => $idCliente]);

        // 2️⃣ Eliminar proyectos compartidos relacionados con el cliente
        $stmt = $pdo->prepare("DELETE FROM proyectos_compartidos WHERE id_proyecto IN 
                              (SELECT id_nuevo_proyecto FROM nuevo_proyecto WHERE id_cliente = :id_cliente)");
        $stmt->execute([':id_cliente' => $idCliente]);

        // 3️⃣ Eliminar los proyectos del cliente
        $stmt = $pdo->prepare("DELETE FROM nuevo_proyecto WHERE id_cliente = :id_cliente");
        $stmt->execute([':id_cliente' => $idCliente]);

        // 4️⃣ Finalmente, eliminar el cliente
        $stmt = $pdo->prepare("DELETE FROM cliente WHERE id_cliente = :id_cliente");
        $stmt->execute([':id_cliente' => $idCliente]);

        $pdo->commit(); // ✅ Confirmar la eliminación

        echo json_encode(["success" => true, "message" => "✅ Cliente eliminado correctamente."]);
        exit();
    } catch (PDOException $e) {
        $pdo->rollBack(); // ❌ Revertir cambios si hay error
        echo json_encode(["success" => false, "error" => $e->getMessage()]);
        exit();
    }
}

?>
