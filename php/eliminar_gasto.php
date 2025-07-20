<?php
require '../bd/db_conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_gasto'])) {
    $id_gasto = $_POST['id_gasto'];

    try {
        $stmt = $pdo->prepare("DELETE FROM gasto WHERE id_gasto = :id_gasto");
        $stmt->bindParam(':id_gasto', $id_gasto, PDO::PARAM_INT);
        $stmt->execute();

        echo "<script>
                alert('Gasto eliminado correctamente.');
                window.location.href = '../vistas/gastos.php';
              </script>";
    } catch (PDOException $e) {
        echo "<script>
                alert('Error al eliminar el gasto: " . $e->getMessage() . "');
                window.history.back();
              </script>";
    }
}
?>
