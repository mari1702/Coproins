<?php
require '../bd/db_conexion.php';

if (isset($_GET['id_encargado'])) {
    $idEncargado = $_GET['id_encargado'];

    $stmt = $pdo->prepare("DELETE FROM encargado WHERE id_encargado = :idEncargado");
    if ($stmt->execute([':idEncargado' => $idEncargado])) {
        echo "<script>alert('Responsable eliminado correctamente'); window.location.href='../vistas/proyecto.php';</script>";
    } else {
        echo "<script>alert('Error al eliminar el responsable'); window.location.href='../vistas/proyecto.php';</script>";
    }
}
?>
