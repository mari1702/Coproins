<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}
require '../bd/db_conexion.php';

$idCliente = $_GET['id_cliente'] ?? null;
if (!$idCliente) {
    echo "<script>
            alert('Cliente no encontrado. id_cliente no está presente.');
            window.location.href = 'proyectos_habilitados.php';
          </script>";
    exit();
}

$idAdmin = $_SESSION['id_usuario']; // Asumiendo que el ID del admin está guardado en 'id_usuario'

// Verificar que el admin esté logueado
if (!isset($idAdmin)) {
    echo "Error: El administrador no está logueado.";
    exit();
}
$clienteQuery = $pdo->prepare("SELECT cliente, estado_cliente FROM cliente WHERE id_cliente = :idCliente");
$clienteQuery->execute([':idCliente' => $idCliente]);
$cliente = $clienteQuery->fetch(PDO::FETCH_ASSOC);

$sucursales = $pdo->prepare("
    SELECT np.id_nuevo_proyecto, np.estado, np.localidad, e.nombre_completo AS encargado, 
           np.costo_inicial, np.fecha, np.id_cliente, np.estado_proyecto, 
           (SELECT SUM(monto) 
            FROM gasto 
            WHERE id_nuevo_proyecto = np.id_nuevo_proyecto AND tipo_gasto = 'ingreso') AS total_ingresos,
           (SELECT SUM(monto) 
            FROM gasto 
            WHERE id_nuevo_proyecto = np.id_nuevo_proyecto AND tipo_gasto = 'egreso') AS total_gastos,
           (SELECT GROUP_CONCAT(u.usuario SEPARATOR ', ') 
            FROM proyectos_compartidos pc
            JOIN usuario u ON pc.id_admin = u.id_usuario
            WHERE pc.id_proyecto = np.id_nuevo_proyecto AND u.rol = 'admin') AS administradores_compartidos
    FROM nuevo_proyecto np
    JOIN encargado e ON np.id_encargado = e.id_encargado
    WHERE np.id_cliente = :idCliente
    AND (
        np.id_admin_creador = :id_admin 
        OR np.id_nuevo_proyecto IN (
            SELECT id_proyecto 
            FROM proyectos_compartidos 
            WHERE id_admin = :id_admin1
        )
    )
    ORDER BY np.id_nuevo_proyecto DESC
");

$sucursales->execute([
    ':idCliente' => $idCliente,
    ':id_admin' => $idAdmin,
    ':id_admin1' => $idAdmin // Asumiendo que $idAdmin es el ID del admin logueado
]);

$sucursales = $sucursales->fetchAll(PDO::FETCH_ASSOC);

?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sucursales de <?= htmlspecialchars($cliente['cliente']) ?></title>
    <link rel="stylesheet" href="../css/proyecto_habilitado.css">
    <script defer src="../js/proyecto.js"></script>
    <script src="../js/sucursal.js"></script>
</head>

<body>
<?php include 'navbar.php'; ?>
<div class="table-container">
<button onclick="goBack();" class="back-button">🔙 Regresar</button>

<script>
    function goBack() {
        // Verifica si el documento tiene un historial anterior
        if (document.referrer && document.referrer !== window.location.href) {
            // Si hay una página anterior diferente a la actual, regresa a esa página
            window.location.href = document.referrer;
        } else {
            // Si no hay una página anterior, redirige a una página predeterminada (por ejemplo, la página de inicio)
            window.location.href = 'proyectos_habilitados.php'; // Cambia 'index.php' por la página que prefieras
        }
    }
</script>


    <h2>Sucursales de <?= htmlspecialchars($cliente['cliente']) ?></h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Estado</th>
                <th>Sucursal (Localidad)</th>
                <th>Encargado</th>
                <th>Costo Inicial</th>
                <th>Ingresos</th>
                <th>Total Gastos</th>
                <th>Detalles</th>
                <th>Estado</th>
                <th>Deshacer Compartición </th>
                <th>Generar Reporte</th>
            </tr>
        </thead>
        <tbody>
    <?php foreach ($sucursales as $sucursal): 
        // Obtener el id_cliente del proyecto desde la tabla nuevo_proyecto
        $idClienteProyecto = $sucursal['id_cliente'];

        // Consulta para verificar si el proyecto está compartido con el administrador logueado
        $stmtProyectoCompartido = $pdo->prepare("SELECT COUNT(*) AS shared_count
            FROM proyectos_compartidos
            WHERE id_proyecto = :id_proyecto AND id_admin = :id_admin");
        
        $stmtProyectoCompartido->bindParam(':id_proyecto', $sucursal['id_nuevo_proyecto'], PDO::PARAM_INT);
        $stmtProyectoCompartido->bindParam(':id_admin', $idAdmin, PDO::PARAM_INT); // Verificar si el administrador logueado compartió el proyecto
        $stmtProyectoCompartido->execute();
        $proyectoCompartido = $stmtProyectoCompartido->fetch(PDO::FETCH_ASSOC);

        $proyectoCompartidoCount = $proyectoCompartido['shared_count']; 
        $administradoresCompartidos = $sucursal['administradores_compartidos']; 
        
        ?>
        
        
        <tr>
            <td><?= $sucursal['id_nuevo_proyecto'] ?></td>
            <td><?= htmlspecialchars($sucursal['estado']) ?></td>
            <td><?= htmlspecialchars($sucursal['localidad']) ?></td>
            <td><?= htmlspecialchars($sucursal['encargado']) ?></td>
            <td>$<?= number_format($sucursal['costo_inicial'] ?? 0, 2) ?></td>
            <td>$<?= number_format($sucursal['total_ingresos'] ?? 0, 2) ?></td>
            <td>$<?= number_format($sucursal['total_gastos'] ?? 0, 2) ?></td>
            <td>
                <form method="POST" action="sucursales.php?id_cliente=<?= $idCliente ?>">
                    <input type="hidden" name="detalle_sucursal_id" value="<?= $sucursal['id_nuevo_proyecto'] ?>">
                    <button type="submit" name="ver_detalles" class="openModal">
                        <i class="fas fa-eye"></i>
                    </button>
                </form>
            </td>
            <td>
            <?php if ($sucursal['estado_proyecto'] == 'Activo'): ?>
                <!-- Botón para finalizar el proyecto -->
                <form method="POST" action="../php/finalizar_activar_proyecto.php" id="formFinalizar_<?= $sucursal['id_nuevo_proyecto'] ?>" onsubmit="return confirmarFinalizar(<?= $sucursal['id_nuevo_proyecto'] ?>)">
                    <input type="hidden" name="id_proyecto" value="<?= $sucursal['id_nuevo_proyecto'] ?>">
                    <input type="hidden" name="id_cliente" value="<?= $idCliente ?>"> <!-- Aquí pasas el id_cliente -->
                    <button type="submit" name="finalizar_proyecto" class="finalizar-button">🚀 Finalizar</button>
                </form>
            <?php elseif ($sucursal['estado_proyecto'] == 'Finalizado'): ?>
                <!-- Botón para activar el proyecto -->
                <form method="POST" action="../php/finalizar_activar_proyecto.php" id="formActivar_<?= $sucursal['id_nuevo_proyecto'] ?>" onsubmit="return confirmarActivar(<?= $sucursal['id_nuevo_proyecto'] ?>)">
                    <input type="hidden" name="id_proyecto" value="<?= $sucursal['id_nuevo_proyecto'] ?>">
                    <input type="hidden" name="id_cliente" value="<?= $idCliente ?>"> <!-- Aquí pasas el id_cliente -->
                    <button type="submit" name="activar_proyecto" class="activar-button">✅Finalizado</button>
                </form>
            <?php endif; ?>



        </td>
            <td>
                <?php if ($proyectoCompartidoCount > 0): ?>
                    <!-- Deshabilitar el botón si el proyecto no fue compartido por este administrador -->
                    <!-- <button type="button" class="deshacer-comparticion-button" disabled style="opacity: 0.5; cursor: not-allowed;">
                        🛑 Deshacer Compartición
                    </button> -->
                    
                <?php else: ?>
                    <!-- Mostrar el botón solo si el proyecto fue compartido por este administrador con este cliente -->
                    <button type="button" class="deshacer-comparticion-button" onclick="deshacerCompartirProyecto(<?= $sucursal['id_nuevo_proyecto'] ?>)">
                        🛑 (Compartido con: <?= htmlspecialchars($administradoresCompartidos) ?>)
                    </button>
                <?php endif; ?>
            </td>
            <td>
                <a href="../php/reporte_sucursal.php?id_sucursal=<?= $sucursal['id_nuevo_proyecto'] ?>" target="_blank">
                    <i class="fas fa-file-pdf"></i> PDF
                </a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
    </table>
</div>

<?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ver_detalles'])): 
    $idSucursal = $_POST['detalle_sucursal_id'];

    $stmt = $pdo->prepare("SELECT np.id_nuevo_proyecto, np.localidad, np.estado, np.estado_proyecto, e.nombre_completo AS encargado, 
                           np.costo_inicial, np.fecha,
                           (SELECT SUM(monto) FROM gasto WHERE id_nuevo_proyecto = np.id_nuevo_proyecto AND tipo_gasto = 'ingreso') AS total_ingresos,
                           (SELECT SUM(monto) FROM gasto WHERE id_nuevo_proyecto = np.id_nuevo_proyecto AND tipo_gasto = 'egreso') AS total_gastos
                           FROM nuevo_proyecto np
                           JOIN encargado e ON np.id_encargado = e.id_encargado
                           WHERE np.id_nuevo_proyecto = :idSucursal");

    $stmt->bindParam(':idSucursal', $idSucursal, PDO::PARAM_INT);
    $stmt->execute();
    $detalle = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmtGastos = $pdo->prepare("SELECT gasto, monto FROM gasto WHERE id_nuevo_proyecto = :idSucursal");
    $stmtGastos->bindParam(':idSucursal', $idSucursal, PDO::PARAM_INT);
    $stmtGastos->execute();
    $gastos = $stmtGastos->fetchAll(PDO::FETCH_ASSOC);
?>

<div id="projectModal" class="modal show">
    <div class="modal-content">
        <span class="close-button" onclick="closeModal()">&times;</span>
        <h2>Detalles de la Sucursal</h2>
        <form id="sucursalForm">
            <input type="hidden" id="sucursalId" value="<?= $detalle['id_nuevo_proyecto'] ?>">

            <label>Sucursal (Localidad):</label>
            <input type="text" id="localidad" value="<?= htmlspecialchars($detalle['localidad']) ?>" readonly>

            <label>Estado (Ubicación):</label>
            <input type="text" id="estado" value="<?= htmlspecialchars($detalle['estado']) ?>" readonly>

            <label>Encargado:</label>
            <input type="text" id="encargado" value="<?= htmlspecialchars($detalle['encargado']) ?>" readonly>

            <label>Costo Inicial:</label>
            <input type="text" id="costoInicial" value="<?= number_format($detalle['costo_inicial'] ?? 0, 2) ?>" readonly>


            <label>Ingresos:</label>
            <input type="text" value="$<?= number_format($detalle['total_ingresos'] ?? 0, 2) ?>" readonly>

            <label>Total Gastos:</label>
            <input type="text" value="$<?= number_format($detalle['total_gastos'] ?? 0, 2) ?>" readonly>

            <label>Fecha de Registro:</label>
            <input type="date" id="fecha" value="<?= htmlspecialchars($detalle['fecha']) ?>" readonly>

            <div class="edit-container">

            <?php if ($detalle['estado_proyecto'] == 'Finalizado'):  ?>
                <button type="button" class="edit-button" disabled style="opacity: 0.5; cursor: not-allowed;">✏️ Editar</button>
            <?php else: ?>
                <button type="button" class="edit-button" onclick="habilitarEdicion()">✏️ Editar</button>
            <?php endif; ?>

                            <button type="button" class="save-button" onclick="guardarEdicion()" style="display: none;">💾 Guardar</button>
                            <!-- Botón para eliminar -->

                            <div class="edit-container">
                        <?php if ($detalle['estado_proyecto'] == 'Finalizado'):  ?>
                <button type="button" class="delete-button" disabled style="opacity: 0.5; cursor: not-allowed;">🗑️ Eliminar</button>

            <?php else: ?>
                <button type="button" class="delete-button" onclick="eliminarSucursal()">🗑️ Eliminar</button>

            <?php endif; ?>

            </div>
        </form>
    </div>
</div>
<?php endif; ?>
<script>
 function deshacerCompartirProyecto(idProyecto) {
    if (!confirm("⚠️ ¿Estás seguro de deshacer la compartición de este proyecto con otros administradores?")) {
        return; // Si el usuario no confirma, no se realiza la acción
    }

    fetch("../php/deshacer_compartir_proyecto.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: `id_proyecto=${encodeURIComponent(idProyecto)}`
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        if (data.success) {
            location.reload(); // Recargar la página si la operación fue exitosa
        }
    })
    .catch(error => {
        console.error("❌ Error al deshacer la compartición del proyecto:", error);
    });
}


// Función para confirmar la finalización del proyecto
function confirmarFinalizar(idProyecto) {
    if (confirm("¿Deseas finalizar este proyecto?")) {
        // Si se confirma, mostrar una alerta de éxito después de la acción
        setTimeout(function() {
            alert("Proyecto finalizado correctamente.");
        }, 500); // Alerta aparece 500ms después de la acción
        return true; // Permite enviar el formulario
    } else {
        return false; // Cancela el envío del formulario
    }
}

// Función para confirmar la activación del proyecto
function confirmarActivar(idProyecto) {
    if (confirm("¿Deseas activar este proyecto?")) {
        // Si se confirma, mostrar una alerta de éxito después de la acción
        setTimeout(function() {
            alert("Proyecto activado correctamente.");
        }, 500); // Alerta aparece 500ms después de la acción
        return true; // Permite enviar el formulario
    } else {
        return false; // Cancela el envío del formulario
    }
}

</script>
<script src="../js/proyecto.js"></script>
</body>
</html>
