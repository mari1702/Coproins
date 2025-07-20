<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header('Location: ../index.php');
    exit();
}
require '../bd/db_conexion.php';

$idUsuario = $_SESSION['id_usuario'];


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $clienteId = isset($_POST['cliente']) ? (int)$_POST['cliente'] : null;
    $sucursalId = !empty($_POST['sucursal']) ? (int)$_POST['sucursal'] : null;

    $monto = isset($_POST['monto']) ? (float)$_POST['monto'] : null;

    $nombreGasto = trim($_POST['nombre_gasto']);
    $tipoGasto = $_POST['tipo'];
    $fecha = $_POST['fecha'];

    $idUsuario = $_SESSION['id_usuario'];

    if (empty($clienteId) || empty($nombreGasto) || empty($tipoGasto) || empty($monto) || empty($fecha) || empty($idUsuario)) {
        echo "<script>alert('❌ Todos los campos son obligatorios.'); window.history.back();</script>";
        exit();
    }

    try {
        // 🔥 1️⃣ Obtener el ID del admin propietario del usuario logueado
    if ($_SESSION['rol'] === 'encargado') {
        // Si es encargado, obtenemos su admin creador
        $stmtAdmin = $pdo->prepare("SELECT id_admin_creador FROM usuario WHERE id_usuario = :id");
        $stmtAdmin->bindParam(':id', $idUsuario, PDO::PARAM_INT);
        $stmtAdmin->execute();
        $idAdmin = $stmtAdmin->fetchColumn(); // ID del admin creador del encargado
    } else {
        $idAdmin = $idUsuario; // Si es admin, él mismo es su admin
    }

    // 🔥 2️⃣ Verificar si el proyecto pertenece al admin o es compartido
    if (!empty($sucursalId)) {
        $stmtVerificar = $pdo->prepare("
            SELECT np.id_nuevo_proyecto
            FROM nuevo_proyecto np
            LEFT JOIN proyectos_compartidos pc 
            ON np.id_nuevo_proyecto = pc.id_proyecto
            WHERE np.id_nuevo_proyecto = :id_proyecto
            AND (
                np.id_admin_creador = :id_admin
                OR pc.id_admin = :id_admin1
            )
        ");
        $stmtVerificar->bindParam(':id_proyecto', $sucursalId, PDO::PARAM_INT);
        $stmtVerificar->bindParam(':id_admin', $idAdmin, PDO::PARAM_INT);
        $stmtVerificar->bindParam(':id_admin1', $idAdmin, PDO::PARAM_INT);

        $stmtVerificar->execute();

        $proyectoExiste = $stmtVerificar->fetch();

        if (!$proyectoExiste) {
            echo "<script>alert('❌ No tienes permiso para registrar gastos en este proyecto.'); window.history.back();</script>";
            exit();
        }
    } else {
        // Si no tiene sucursal, permitimos que sea directo al cliente
        $sucursalId = null;
    }

        // 🔥 3️⃣ Insertar el gasto en la base de datos
        $sql = "INSERT INTO gasto (id_nuevo_proyecto, id_usuario, id_cliente, gasto, tipo_gasto, monto, fecha) 
        VALUES (:id_nuevo_proyecto, :id_usuario, :id_cliente, :gasto, :tipo_gasto, :monto, :fecha)";

        $stmt = $pdo->prepare($sql);

        // 🔥 Manejar NULL o INT para `id_nuevo_proyecto` (Sucursal)
        if ($sucursalId === null) {
            $stmt->bindValue(':id_nuevo_proyecto', null, PDO::PARAM_NULL);
        } else {
            $stmt->bindParam(':id_nuevo_proyecto', $sucursalId, PDO::PARAM_INT);
        }

        // ✅ Enlazar los otros valores correctamente
        $stmt->bindParam(':id_usuario', $_SESSION['id_usuario'], PDO::PARAM_INT);
        $stmt->bindParam(':id_cliente', $_POST['cliente'], PDO::PARAM_INT);
        $stmt->bindParam(':gasto', $_POST['nombre_gasto'], PDO::PARAM_STR);
        $stmt->bindParam(':tipo_gasto', $_POST['tipo'], PDO::PARAM_STR);
        $stmt->bindParam(':monto', $_POST['monto'], PDO::PARAM_STR);
        $stmt->bindParam(':fecha', $_POST['fecha'], PDO::PARAM_STR);

        $stmt->execute();
        $_SESSION['mensaje'] = '✅ Gasto registrado correctamente.';
        header('Location: ../vistas/gastos.php');
        exit();

    } catch (PDOException $e) {
        die("❌ Error al registrar gasto: " . $e->getMessage());
    }
}
?>
