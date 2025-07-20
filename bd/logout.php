<?php
session_start(); // Inicia la sesión
session_destroy(); // Destruye la sesión
header("Location: ../index.php"); // Redirige al inicio de sesión
exit();
?>
