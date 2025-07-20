<?php
require '../bd/db_conexion.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idContacto = $_POST['id_contacto'] ?? null;
    $nombre = $_POST['nombre_contacto'] ?? null;
    $telefono = $_POST['telefono_contacto'] ?? null;

    // Verificar que los datos estén llenos
    if (empty($idContacto) || empty($nombre)) {
        echo "<script>alert('Todos los campos son obligatorios.'); window.history.back();</script>";
        exit();
    }

    try {
        $stmt = $pdo->prepare("UPDATE contacto 
                               SET nombre = :nombre, telefono = :telefono
                               WHERE id_contacto = :idContacto");
        $stmt->execute([
            ':nombre' => $nombre,
            ':telefono' => $telefono,
            ':idContacto' => $idContacto
        ]);

        echo "<script>alert('Contacto actualizado correctamente.'); window.location.href='../vistas/ver_contactos_responsables.php';</script>";
        exit();
    } catch (PDOException $e) {
        echo "<script>alert('Error al actualizar contacto: " . $e->getMessage() . "'); window.history.back();</script>";
        exit();
    }
}
?>
