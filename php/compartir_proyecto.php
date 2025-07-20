<?php
session_start();
require '../bd/db_conexion.php';

$idProyecto = $_POST['id_proyecto'];
$idAdminCompartir = $_POST['id_admin'];  // Admin con el que se comparte

$stmt = $pdo->prepare("
    INSERT INTO proyectos_compartidos (id_proyecto, id_admin)
    VALUES (:id_proyecto, :id_admin)
");

$stmt->execute([
    ':id_proyecto' => $idProyecto,
    ':id_admin' => $idAdminCompartir
]);

echo "Proyecto compartido exitosamente.";


?>