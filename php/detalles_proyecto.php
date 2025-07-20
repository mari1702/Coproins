<?php
require '../bd/db_conexion.php';

if (isset($_GET['id_cliente'])) {
    $idCliente = $_GET['id_cliente'];

    // Obtener los datos del cliente incluyendo su número de teléfono
    $queryCliente = "SELECT cliente, telefono_cliente FROM cliente WHERE id_cliente = :idCliente";
    $stmtCliente = $pdo->prepare($queryCliente);
    $stmtCliente->bindParam(':idCliente', $idCliente, PDO::PARAM_INT);
    $stmtCliente->execute();
    $cliente = $stmtCliente->fetch(PDO::FETCH_ASSOC);

    // Obtener los datos de los proyectos y asegurarse de traer el costo inicial correcto
    $queryProyecto = "SELECT 
                        SUM(np.costo_inicial) AS costo_inicial_total,
                        (SELECT COALESCE(SUM(monto), 0) FROM gasto WHERE id_nuevo_proyecto = np.id_nuevo_proyecto AND tipo_gasto = 'ingreso') AS total_ingresos,
                        (SELECT COALESCE(SUM(monto), 0) FROM gasto WHERE id_nuevo_proyecto = np.id_nuevo_proyecto AND tipo_gasto = 'egreso') AS egresos_sucursal,
                        (SELECT COALESCE(SUM(monto), 0) FROM gasto WHERE id_cliente = np.id_cliente AND id_nuevo_proyecto IS NULL AND tipo_gasto = 'egreso') AS egresos_cliente
                    FROM nuevo_proyecto np
                    WHERE np.id_cliente = :idCliente";

    $stmtProyecto = $pdo->prepare($queryProyecto);
    $stmtProyecto->bindParam(':idCliente', $idCliente, PDO::PARAM_INT);
    $stmtProyecto->execute();
    $proyecto = $stmtProyecto->fetch(PDO::FETCH_ASSOC);
?>

<div id="projectModal" class="modal show">
    <div class="modal-content">
        <span class="close-button" onclick="closeModal()">&times;</span>
        <h2>Detalles del Cliente</h2>
        <form>
            <label>Cliente:</label>
            <input type="text" value="<?= htmlspecialchars($cliente['cliente']) ?>" readonly>
            <label>Teléfono:</label>
            <input type="text" value="<?= htmlspecialchars($cliente['telefono_cliente']) ?>" readonly>
            <label>Costo Inicial:</label>
            <input type="text" value="$<?= number_format($proyecto['costo_inicial_total'] ?? 0, 2) ?>" readonly>
            <label>Ingreso Total:</label>
            <input type="text" value="$<?= number_format($proyecto['total_ingresos'] ?? 0, 2) ?>" readonly>
            <label>Egresos por Sucursal:</label>
            <input type="text" value="$<?= number_format($proyecto['egresos_sucursal'] ?? 0, 2) ?>" readonly>
            <label>Egresos por Cliente:</label>
            <input type="text" value="$<?= number_format($proyecto['egresos_cliente'] ?? 0, 2) ?>" readonly>
        </form>
    </div>
</div>

<?php 
} else {
    echo "<p>Error: No se encontraron detalles del cliente.</p>";
}
?>