<?php
session_start();
$_SESSION['id_usuario'] = $idUsuario; // Asegura que el ID del usuario se guarda

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require '../bd/db_conexion.php'; 


try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $userUser = $_POST['userUser'];
        $userPassword = $_POST['userPassword'];

        // Verificar si el usuario existe
        $stmt = $pdo->prepare("SELECT * FROM usuario WHERE usuario = :username");
        $stmt->bindParam(':username', $userUser);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Si la contrase�0�9a est�� encriptada, verificar con password_verify()
            if ($user && password_verify($userPassword, $user['contrasena'])) {

                $_SESSION['id_usuario'] = $user['id_usuario'];
                $_SESSION['usuario'] = $user['usuario'];
                $_SESSION['rol'] = trim($user['rol']); // Asegurar que no tenga espacios
                
                // Redirigir según el rol del usuario
                if ($_SESSION['rol'] === 'admin') {
                    header("Location: ../vistas/ingresos_grafica.php");
                } elseif ($_SESSION['rol'] === 'encargado') {
                    header("Location: ../vistas/gastos.php");
                } else {
                    // Si el rol no está definido correctamente
                    header("Location: ../index.php?error=" . urlencode("Rol no reconocido"));
                }
                exit();
                
            } else {
                throw new Exception("Contraseña incorrecta.");
            }
        } else {
            throw new Exception("Usuario no encontrado.");
        }
    }
} catch (Exception $e) {
    // Capturar errores y redirigir al login con un mensaje
    error_log("Error en login.php: " . $e->getMessage()); // Guardar en logs
    header("Location: ../index.php?error=" . urlencode($e->getMessage()));
    exit();
}
?>
