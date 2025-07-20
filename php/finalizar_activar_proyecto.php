<?php
// Inicia la sesión para verificar si el administrador está logueado
session_start();

// Verifica que el usuario esté logueado y sea administrador
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

require '../bd/db_conexion.php';

// Verificar si se recibe un ID de proyecto y un ID de cliente
$idProyecto = $_POST['id_proyecto'] ?? null;
$idCliente = $_POST['id_cliente'] ?? null;


if (!$idProyecto || !$idCliente) {
    echo "Error: id_proyecto o id_cliente no están presentes.";
    exit();
}

// Aquí, tu código para cambiar el estado del proyecto (activar o finalizar)
if (isset($_POST['finalizar_proyecto'])) {
    // Actualizar el estado del proyecto a 'Finalizado'
    $stmt = $pdo->prepare("UPDATE nuevo_proyecto SET estado_proyecto = 'Finalizado' WHERE id_nuevo_proyecto = :idProyecto");
    $stmt->execute([':idProyecto' => $idProyecto]);
    
    // Redirigir después de la actualización
    header("Location: ../vistas/sucursales.php?id_cliente=" . $idCliente); // Asegúrate de pasar id_cliente en la redirección
    exit();
} elseif (isset($_POST['activar_proyecto'])) {
    // Actualizar el estado del proyecto a 'Activo'
    $stmt = $pdo->prepare("UPDATE nuevo_proyecto SET estado_proyecto = 'Activo' WHERE id_nuevo_proyecto = :idProyecto");
    $stmt->execute([':idProyecto' => $idProyecto]);
    
    // Redirigir después de la actualización
    header("Location: ../vistas/sucursales.php?id_cliente=" . $idCliente); // Asegúrate de pasar id_cliente en la redirección
    exit();
}
?>
