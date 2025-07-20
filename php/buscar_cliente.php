<?php
// Conexión a la base de datos (asegúrate de incluir tu configuración de conexión)
require '../bd/db_conexion.php';


if (isset($_GET['cliente'])) {
    $cliente = $_GET['cliente'];

    // Consulta SQL para buscar clientes que coincidan con lo que se ha escrito
    $query = "SELECT id_cliente, cliente FROM cliente WHERE cliente LIKE :cliente LIMIT 10";
    $stmt = $pdo->prepare($query);
    $stmt->bindValue(':cliente', '%' . $cliente . '%');
    $stmt->execute();
    $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($clientes) {
        foreach ($clientes as $cliente) {
            echo "<div class='cliente-item' onclick='seleccionarCliente(\"" . $cliente['cliente'] . "\")'>" . htmlspecialchars($cliente['cliente']) . "</div>";
        }
    } else {
        echo "No se encontraron clientes.";
    }
}
?>
