<?php
require '../bd/db_conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_proyecto'])) {
    $id_proyecto = $_POST['id_proyecto'];
    $id_cliente = $_POST['id_cliente'];
    $id_encargado = $_POST['id_encargado'];
    $localidad = $_POST['localidad'];
    $costo_inicial = $_POST['costo_inicial'];
    $fecha = $_POST['fecha'];

    try {
        // Verificar que los IDs existen antes de actualizar
        $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM cliente WHERE id_cliente = :id_cliente");
        $stmtCheck->execute([':id_cliente' => $id_cliente]);
        if ($stmtCheck->fetchColumn() == 0) {
            throw new Exception("El cliente seleccionado no existe.");
        }

        $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM encargado WHERE id_encargado = :id_encargado");
        $stmtCheck->execute([':id_encargado' => $id_encargado]);
        if ($stmtCheck->fetchColumn() == 0) {
            throw new Exception("El encargado seleccionado no existe.");
        }

        // Actualizar el proyecto con los IDs correctos
        $stmt = $pdo->prepare("UPDATE nuevo_proyecto 
            SET id_cliente = :id_cliente, id_encargado = :id_encargado, 
                localidad = :localidad, costo_inicial = :costo_inicial, fecha = :fecha
            WHERE id_nuevo_proyecto = :id_proyecto");

        $stmt->execute([
            ':id_cliente' => $id_cliente,
            ':id_encargado' => $id_encargado,
            ':localidad' => $localidad,
            ':costo_inicial' => $costo_inicial,
            ':fecha' => $fecha,
            ':id_proyecto' => $id_proyecto
        ]);

        echo "<script>
                alert('Proyecto actualizado correctamente.');
                window.location.href='../vistas/proyectos_habilitados.php';
              </script>";
    } catch (PDOException $e) {
        echo "Error en la base de datos: " . $e->getMessage();
    } catch (Exception $e) {
        echo "<script>alert('" . $e->getMessage() . "');</script>";
    }
}
?>
