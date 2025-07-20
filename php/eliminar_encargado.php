<?php
require '../bd/db_conexion.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_encargado'])) {
    $idEncargado = $_POST['id_encargado'];

    try {
        $stmt = $pdo->prepare("DELETE FROM encargado WHERE id_encargado = :idEncargado");
        $stmt->execute([':idEncargado' => $idEncargado]);

        echo "<script>alert('Responsable eliminado correctamente.'); window.location.href='../vistas/ver_contactos_responsables.php';</script>";
        exit();
    } catch (PDOException $e) {
        echo "<script>alert('Error al eliminar responsable: " . $e->getMessage() . "'); window.history.back();</script>";
        exit();
    }
}
?>
