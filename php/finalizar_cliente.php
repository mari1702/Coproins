<?php
require '../bd/db_conexion.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_cliente'])) {
    $idCliente = $_POST['id_cliente'];

    try {
        $pdo->beginTransaction(); // 🔥 Iniciar transacción

        // 1️⃣ Actualizar el estado del cliente a "Finalizado"
        $stmtCliente = $pdo->prepare("UPDATE cliente SET estado_cliente = 'Finalizado' WHERE id_cliente = :id_cliente");
        $stmtCliente->bindParam(':id_cliente', $idCliente, PDO::PARAM_INT);
        $stmtCliente->execute();

        // 2️⃣ Actualizar el estado de todos los proyectos del cliente a "Finalizado"
        $stmtProyectos = $pdo->prepare("UPDATE nuevo_proyecto SET estado_proyecto = 'Finalizado' WHERE id_cliente = :id_cliente");
        $stmtProyectos->bindParam(':id_cliente', $idCliente, PDO::PARAM_INT);
        $stmtProyectos->execute();

        $pdo->commit(); // ✅ Confirmar la transacción

        if ($stmtCliente->rowCount() > 0 || $stmtProyectos->rowCount() > 0) {
            echo json_encode(["success" => true, "message" => "✅ Cliente y proyectos finalizados correctamente."]);
        } else {
            echo json_encode(["success" => false, "message" => "⚠️ No se encontró el cliente o ya estaba finalizado."]);
        }
    } catch (PDOException $e) {
        $pdo->rollBack(); // ❌ Revertir cambios si hay error
        echo json_encode(["success" => false, "message" => "❌ Error al finalizar: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["success" => false, "message" => "❌ Solicitud inválida."]);
}
?>
