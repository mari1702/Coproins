<?php
session_start();

// Verificar que el administrador esté logueado
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'No tienes permisos para realizar esta acción.']);
    exit();
}

require '../bd/db_conexion.php';

// Obtener el ID del cliente y la acción a realizar
$idCliente = $_GET['id_cliente'] ?? null;
$accion = $_GET['accion'] ?? null;

if (!$idCliente || !$accion) {
    echo json_encode(['success' => false, 'message' => 'Faltan parámetros.']);
    exit();
}

if ($accion == 'finalizar') {
    // Actualizar el estado del cliente a 'Finalizado'
    $stmt = $pdo->prepare("UPDATE cliente SET estado_cliente = 'Finalizado' WHERE id_cliente = :idCliente");
} elseif ($accion == 'desfinalizar') {
    // Actualizar el estado del cliente a 'Activo'
    $stmt = $pdo->prepare("UPDATE cliente SET estado_cliente = 'Activo' WHERE id_cliente = :idCliente");
} else {
    echo json_encode(['success' => false, 'message' => 'Acción no válida.']);
    exit();
}

// Ejecutar la consulta de actualización
$stmt->execute([':idCliente' => $idCliente]);

// Verificar si la actualización fue exitosa
if ($stmt->rowCount() > 0) {
    echo json_encode(['success' => true, 'message' => 'Operación realizada correctamente.']);
} else {
    echo json_encode(['success' => false, 'message' => 'No se pudo realizar la operación.']);
}
?>
