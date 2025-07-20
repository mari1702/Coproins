<?php
require '../bd/db_conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_usuario'])) {
    $id_usuario = $_POST['id_usuario'];

    // Obtener datos actuales del usuario
    $stmt = $pdo->prepare("SELECT usuario, rol FROM usuario WHERE id_usuario = :id_usuario");
    $stmt->execute([':id_usuario' => $id_usuario]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Si se envió el formulario de actualización
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_user'])) {
    $id_usuario = $_POST['id_usuario'];
    $userUser = trim($_POST['userUser']);
    $rol = $_POST['rol'];

    $stmtUpdate = $pdo->prepare("UPDATE usuario SET usuario = :userUser, rol = :rol WHERE id_usuario = :id_usuario");
    $stmtUpdate->execute([':userUser' => $userUser, ':rol' => $rol, ':id_usuario' => $id_usuario]);

    echo "<script>
            alert('Usuario actualizado correctamente.');
            window.location.href='../vistas/nuevo_usuario.php';
          </script>";
    exit();
}
?>
