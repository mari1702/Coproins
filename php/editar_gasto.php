<?php
session_start();
require '../bd/db_conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idGasto = $_POST['id_gasto'] ?? null;
    $clienteId = $_POST['cliente'] ?? null;
    $sucursalId = $_POST['sucursal'] ?? null;
    $nombreGasto = trim($_POST['nombre_gasto']);
    $tipoGasto = $_POST['tipo'];
    $monto = $_POST['monto'];
    $fecha = $_POST['fecha'];
    $idUsuario = $_SESSION['id_usuario']; // ID del usuario que realiza la edición

    // Verificar que todos los campos requeridos están llenos
    if (empty($idGasto) || empty($clienteId) || empty($nombreGasto) || empty($tipoGasto) || empty($monto) || empty($fecha) || empty($idUsuario)) {
        echo "<script>alert('Todos los campos deben ser llenados.'); window.history.back();</script>";
        exit();
    }

    try {
        // Determinar si es un gasto de CLIENTE o de SUCURSAL
        if ($sucursalId === 'cliente' || empty($sucursalId)) {
            $sucursalId = null; // Si es un gasto de cliente, `id_nuevo_proyecto` debe ser NULL
        } else {
            $sucursalId = (int)$sucursalId; // Si es una sucursal, aseguramos que sea un número entero válido
        }

        // Actualizar gasto en la base de datos
        $sql = "UPDATE gasto 
                SET id_cliente = :id_cliente, 
                    id_nuevo_proyecto = :id_nuevo_proyecto, 
                    gasto = :gasto, 
                    tipo_gasto = :tipo_gasto, 
                    monto = :monto, 
                    fecha = :fecha 
                WHERE id_gasto = :id_gasto";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id_cliente', $clienteId, PDO::PARAM_INT);
        
        if ($sucursalId === null) {
            $stmt->bindValue(':id_nuevo_proyecto', null, PDO::PARAM_NULL);
        } else {
            $stmt->bindValue(':id_nuevo_proyecto', $sucursalId, PDO::PARAM_INT);
        }
        $stmt->bindParam(':gasto', $nombreGasto, PDO::PARAM_STR);
        $stmt->bindParam(':tipo_gasto', $tipoGasto, PDO::PARAM_STR);
        $stmt->bindParam(':monto', $monto, PDO::PARAM_STR);
        $stmt->bindParam(':fecha', $fecha, PDO::PARAM_STR);
        $stmt->bindParam(':id_gasto', $idGasto, PDO::PARAM_INT);
        $stmt->execute();

        echo "<script>alert('Gasto actualizado correctamente.'); window.location.href='../vistas/gastos.php';</script>";
        exit();
    } catch (PDOException $e) {
        die("Error al actualizar gasto: " . $e->getMessage());
    }
}
?>
