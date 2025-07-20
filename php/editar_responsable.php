<?php
require '../bd/db_conexion.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idEncargado = $_POST['id_encargado'] ?? null;
    $nombre = $_POST['nombre_encargado'] ?? null;
    $telefono = $_POST['telefono_encargado'] ?? null;

    // Verificar que los datos estén llenos
    if (empty($idEncargado) || empty($nombre)) {
        echo "<script>alert('Todos los campos son obligatorios.'); window.history.back();</script>";
        exit();
    }

    try {
        $stmt = $pdo->prepare("UPDATE encargado 
                               SET nombre_completo = :nombre, telefono_encargado = :telefono
                               WHERE id_encargado = :idEncargado");
        $stmt->execute([
            ':nombre' => $nombre,
            ':telefono' => $telefono,
            ':idEncargado' => $idEncargado
        ]);

        echo "<script>alert('Responsable actualizado correctamente.'); window.location.href='../vistas/ver_contactos_responsables.php';</script>";
        exit();
    } catch (PDOException $e) {
        echo "<script>alert('Error al actualizar responsable: " . $e->getMessage() . "'); window.history.back();</script>";
        exit();
    }
}
?>
