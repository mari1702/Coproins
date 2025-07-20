<?php
require '../bd/db_conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['idSucursal'])) {
        echo "Error: Datos incompletos.";
        exit;
    }

    $idSucursal = $_POST['idSucursal'];

    try {
        // Eliminar todos los gastos asociados a la sucursal
        $stmtGastos = $pdo->prepare("DELETE FROM gasto WHERE id_nuevo_proyecto = :idSucursal");
        $stmtGastos->execute([':idSucursal' => $idSucursal]);

        // Eliminar la sucursal en sí
        $stmtSucursal = $pdo->prepare("DELETE FROM nuevo_proyecto WHERE id_nuevo_proyecto = :idSucursal");
        $stmtSucursal->execute([':idSucursal' => $idSucursal]);

        // Redirigir a la página de sucursales sin reenviar el formulario
        echo "Sucursal eliminada correctamente.";
    } catch (PDOException $e) {
        echo "Error al eliminar la sucursal: " . $e->getMessage();
    }
}
?>
