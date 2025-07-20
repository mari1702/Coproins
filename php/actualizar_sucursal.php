<?php
require '../bd/db_conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['idSucursal'], $_POST['localidad'], $_POST['estado'], $_POST['encargado'], $_POST['costoInicial'], $_POST['fecha'])) {
        echo "Error: Datos incompletos.";
        exit;
    }

    $idSucursal = $_POST['idSucursal'];
    $localidad = $_POST['localidad'];
    $estado = $_POST['estado'];
    $encargado = $_POST['encargado'];
    $costoInicial = str_replace(',', '', $_POST['costoInicial']);
    $costoInicial = floatval($costoInicial);
    $fecha = $_POST['fecha'];

    try {
        // Buscar el ID del encargado
        $idEncargado = null;
        if (!empty($encargado)) {
            $stmtEncargado = $pdo->prepare("SELECT id_encargado FROM encargado WHERE nombre_completo = :encargado");
            $stmtEncargado->execute([':encargado' => $encargado]);
            $idEncargado = $stmtEncargado->fetchColumn();
            
            if (!$idEncargado) {
                echo "Error: Encargado no encontrado en la base de datos.";
                exit;
            }
        }

        // Obtener el costo inicial actual
        $stmtCosto = $pdo->prepare("SELECT costo_inicial FROM nuevo_proyecto WHERE id_nuevo_proyecto = :idSucursal");
        $stmtCosto->execute([':idSucursal' => $idSucursal]);
        $costoInicialActual = floatval($stmtCosto->fetchColumn());

        // Preparar la consulta de actualización
        $sql = "UPDATE nuevo_proyecto SET localidad = :localidad, estado = :estado, fecha = :fecha";
        $params = [
            ':localidad' => $localidad,
            ':estado' => $estado,
            ':fecha' => $fecha,
            ':idSucursal' => $idSucursal
        ];

        // Solo actualizar el costo inicial y registrar en historial si cambia
        if ($costoInicial != $costoInicialActual) {

            // Buscar el primer costo registrado
            $stmtPrimerCosto = $pdo->prepare("
                SELECT costo 
                FROM historial_costo_proyecto 
                WHERE id_nuevo_proyecto = :id_nuevo_proyecto 
                ORDER BY fecha_modificacion ASC 
                LIMIT 1
            ");
            $stmtPrimerCosto->execute([':id_nuevo_proyecto' => $idSucursal]);
            $primerCosto = floatval($stmtPrimerCosto->fetchColumn());

            if (!$primerCosto) {
                $primerCosto = $costoInicialActual; // Usa el costo actual si no hay historial
            }

            // Calcular diferencia contra el primer costo
            $diferencia = $costoInicial - $primerCosto;

            $sql .= ", costo_inicial = :costoInicial";
            $params[':costoInicial'] = $costoInicial;

            // Registrar en historial_costo_proyecto con la diferencia
            $stmtHistorial = $pdo->prepare("
                INSERT INTO historial_costo_proyecto (id_nuevo_proyecto, costo, diferencia) 
                VALUES (:id_nuevo_proyecto, :costo, :diferencia)
            ");
            $stmtHistorial->execute([
                ':id_nuevo_proyecto' => $idSucursal,
                ':costo' => $costoInicial,
                ':diferencia' => $diferencia
            ]);
        }

        // Si se cambió el encargado, actualizarlo también
        if ($idEncargado) {
            $sql .= ", id_encargado = :idEncargado";
            $params[':idEncargado'] = $idEncargado;
        }

        $sql .= " WHERE id_nuevo_proyecto = :idSucursal";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        echo "Sucursal actualizada correctamente.";
    } catch (PDOException $e) {
        echo "Error al actualizar: " . $e->getMessage();
    }
}

?>
