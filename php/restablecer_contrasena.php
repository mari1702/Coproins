<?php
require '../bd/db_conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_usuario'])) {
    $id_usuario = $_POST['id_usuario'];

    // Obtener el usuario
    $stmt = $pdo->prepare("SELECT usuario FROM usuario WHERE id_usuario = :id_usuario");
    $stmt->execute([':id_usuario' => $id_usuario]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        echo "<script>
                alert('Usuario no encontrado.');
                window.location.href='../vistas/nuevo_usuario.php';
              </script>";
        exit();
    }
}

// Si el formulario de restablecimiento se envió
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_password'])) {
    $id_usuario = $_POST['id_usuario'];
    $new_password = password_hash($_POST['new_password'], PASSWORD_BCRYPT);

    $stmtUpdate = $pdo->prepare("UPDATE usuario SET contrasena = :new_password WHERE id_usuario = :id_usuario");
    $stmtUpdate->execute([':new_password' => $new_password, ':id_usuario' => $id_usuario]);

    echo "<script>
            alert('Contraseña actualizada correctamente.');
            window.location.href='../vistas/nuevo_usuario.php';
          </script>";
    exit();
}
?>
