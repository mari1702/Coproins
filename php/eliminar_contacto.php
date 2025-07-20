<?php
require '../bd/db_conexion.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_contacto'])) {
    $idContacto = $_POST['id_contacto'];

    try {
        $stmt = $pdo->prepare("DELETE FROM contacto WHERE id_contacto = :idContacto");
        $stmt->execute([':idContacto' => $idContacto]);

        echo "<script>alert('Contacto eliminado correctamente.'); window.location.href='../vistas/ver_contactos_responsables.php';</script>";
        exit();
    } catch (PDOException $e) {
        echo "<script>alert('Error al eliminar contacto: " . $e->getMessage() . "'); window.history.back();</script>";
        exit();
    }
}
?>
