<?php
require '../bd/db_conexion.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idProyecto = $_POST['id_proyecto'];

    if (empty($idProyecto)) {
        echo json_encode(["success" => false, "message" => "❌ ID del proyecto no válido."]);
        exit();
    }

    try {
        // Eliminar la relación de compartición en la tabla 'proyectos_compartidos'
        $stmt = $pdo->prepare("DELETE FROM proyectos_compartidos WHERE id_proyecto = :id_proyecto");
        $stmt->bindParam(':id_proyecto', $idProyecto, PDO::PARAM_INT);
        $stmt->execute();

        echo json_encode(["success" => true, "message" => "✅ Proyecto compartido deshecho correctamente."]);
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => "❌ Error al deshacer la compartición: " . $e->getMessage()]);
    }
}
?>
