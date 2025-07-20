<?php
session_start();
require '../bd/db_conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['userUser']);
    $contrasena = password_hash($_POST['userPassword'], PASSWORD_BCRYPT);
    $rol = $_POST['rol'];
    $idAdminCreador = $_SESSION['id_usuario']; // Admin que crea el usuario

    try {
        // 🔥 VERIFICAR QUE NO EXISTA EL USUARIO ANTES DE INSERTARLO
        $stmtVerificar = $pdo->prepare("SELECT COUNT(*) FROM usuario WHERE usuario = :usuario");
        $stmtVerificar->bindParam(':usuario', $usuario, PDO::PARAM_STR);
        $stmtVerificar->execute();
        $existeUsuario = $stmtVerificar->fetchColumn();

        if ($existeUsuario > 0) {
            echo "<script>alert('❌ Error: El usuario ya existe.'); window.history.back();</script>";
            exit();
        }

        // ✅ INSERTAR NUEVO USUARIO
        $sql = "INSERT INTO usuario (usuario, contrasena, rol, id_admin_creador) 
                VALUES (:usuario, :contrasena, :rol, :id_admin_creador)";
                
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':usuario', $usuario, PDO::PARAM_STR);
        $stmt->bindParam(':contrasena', $contrasena, PDO::PARAM_STR);
        $stmt->bindParam(':rol', $rol, PDO::PARAM_STR);
        $stmt->bindParam(':id_admin_creador', $idAdminCreador, PDO::PARAM_INT);
        $stmt->execute();

        echo "<script>alert('✅ Usuario registrado correctamente.'); window.location.href = '../vistas/nuevo_usuario.php';</script>";
        exit();
    } catch (PDOException $e) {
        die("❌ Error al registrar usuario: " . $e->getMessage());
    }
}
?>
