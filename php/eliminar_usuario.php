<?php
require '../bd/db_conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_usuario'])) {
    $id_usuario = $_POST['id_usuario'];

    // Eliminar el usuario
    $stmt = $pdo->prepare("DELETE FROM usuario WHERE id_usuario = :id_usuario");
    $stmt->execute([':id_usuario' => $id_usuario]);

    echo "<script>
            alert('Usuario eliminado correctamente.');
            window.location.href='../vistas/nuevo_usuario.php';
          </script>";
    exit();
}
?>
