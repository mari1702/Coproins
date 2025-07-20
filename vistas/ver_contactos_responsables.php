<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}
require '../bd/db_conexion.php';
$idAdmin = $_SESSION['id_usuario'];
// Obtener Contactos
$queryContactos = "
    SELECT DISTINCT c.id_contacto, c.nombre, c.telefono, cl.cliente 
    FROM contacto c
    INNER JOIN cliente cl ON c.id_cliente = cl.id_cliente
    INNER JOIN nuevo_proyecto np ON np.id_cliente = cl.id_cliente
    LEFT JOIN proyectos_compartidos pc ON np.id_nuevo_proyecto = pc.id_proyecto
    WHERE np.id_admin_creador = :id_admin
       OR (pc.id_admin = :id_admin1 AND pc.id_proyecto IS NOT NULL)
    ORDER BY cl.cliente ASC
";
$stmtContactos = $pdo->prepare($queryContactos);
$stmtContactos->bindParam(':id_admin', $idAdmin, PDO::PARAM_INT);
$stmtContactos->bindParam(':id_admin1', $idAdmin, PDO::PARAM_INT);
$stmtContactos->execute();
$contactos = $stmtContactos->fetchAll(PDO::FETCH_ASSOC);


$queryResponsables = "
    SELECT e.id_encargado, e.nombre_completo, e.telefono_encargado, c.cliente
    FROM encargado e
    INNER JOIN cliente c ON e.id_cliente = c.id_cliente
    WHERE c.id_admin_creador = :id_admin
    ORDER BY c.cliente ASC, e.nombre_completo ASC
";


$stmtResponsables = $pdo->prepare($queryResponsables);
$stmtResponsables->bindParam(':id_admin', $idAdmin, PDO::PARAM_INT);
$stmtResponsables->execute();
$responsables = $stmtResponsables->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Contactos y Responsables</title>
    <link rel="stylesheet" href="../css/ver_contacto.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
<?php include 'navbar.php'; ?>
<button onclick="window.history.back();" class="back-button">🔙 Regresar</button>

<div class="container">
    
    <h2>Contactos</h2>
    <div class="table-container">
        <table class="styled-table">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Teléfono</th>
                    <th>Empresa</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($contactos as $contacto): ?>
                <tr>
                    <td><?= htmlspecialchars($contacto['nombre']) ?></td>
                    <td><?= htmlspecialchars($contacto['telefono']) ?></td>
                    <td><?= htmlspecialchars($contacto['cliente']) ?></td>
                    <td>
                        <!-- Botón Editar Contacto -->
                        <button onclick="openEditContactoModal(
                            <?= $contacto['id_contacto'] ?>, 
                            '<?= htmlspecialchars($contacto['nombre']) ?>', 
                            '<?= htmlspecialchars($contacto['telefono']) ?>'
                        )">
                            <i class="fas fa-edit"></i>
                        </button>

                        <!-- Botón Eliminar Contacto -->
                        <form action="../php/eliminar_contacto.php" method="POST" style="display:inline;">
                            <input type="hidden" name="id_contacto" value="<?= $contacto['id_contacto'] ?>">
                            <button type="submit" class="delete-btn" onclick="return confirm('¿Seguro que deseas eliminar este contacto?');">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <h2>Responsables</h2>
<div class="table-container">
    <table class="styled-table">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Teléfono</th>
                <th>Empresa</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($responsables as $responsable): ?>
            <tr>
                <td><?= htmlspecialchars($responsable['nombre_completo']) ?></td>
                <td><?= htmlspecialchars($responsable['telefono_encargado']) ?></td>
                <td><?= htmlspecialchars($responsable['cliente']) ?></td>
                <td>
                    <button onclick="openEditEncargadoModal(
                        <?= $responsable['id_encargado'] ?>, 
                        '<?= htmlspecialchars($responsable['nombre_completo']) ?>', 
                        '<?= htmlspecialchars($responsable['telefono_encargado']) ?>'
                    )">
                        <i class="fas fa-edit"></i>
                    </button>

                    <form action="../php/eliminar_encargado.php" method="POST" style="display:inline;">
                        <input type="hidden" name="id_encargado" value="<?= $responsable['id_encargado'] ?>">
                        <button type="submit" class="delete-btn" onclick="return confirm('¿Seguro que deseas eliminar este responsable?');">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>


<!-- Modal para Editar Contacto -->
<div id="editContactoModal" class="modal">
    <div class="modal-content">
        <span class="close-button" onclick="closeEditContactoModal()">&times;</span>
        <h3>Editar Contacto</h3>
        <form action="../php/editar_contacto.php" method="POST">
            <input type="hidden" id="edit_id_contacto" name="id_contacto">

            <label>Nombre:</label>
            <input type="text" id="edit_nombre_contacto" name="nombre_contacto" required>

            <label>Teléfono:</label>
            <input type="text" id="edit_telefono_contacto" name="telefono_contacto">

            <button class="btn" type="submit">Guardar Cambios</button>
        </form>
    </div>
</div>

<!-- Modal para Editar Responsable -->
<div id="editEncargadoModal" class="modal">
    <div class="modal-content">
        <span class="close-button" onclick="closeEditEncargadoModal()">&times;</span>
        <h3>Editar Responsable</h3>
        <form action="../php/editar_responsable.php" method="POST">
            <input type="hidden" id="edit_id_encargado" name="id_encargado">

            <label>Nombre:</label>
            <input type="text" id="edit_nombre_encargado" name="nombre_encargado" required>

            <label>Teléfono:</label>
            <input type="text" id="edit_telefono_encargado" name="telefono_encargado">

            <button class="btn" type="submit">Guardar Cambios</button>
        </form>
    </div>
</div>

<script>
// Funciones para editar Contacto
function openEditContactoModal(id, nombre, telefono) {
    document.getElementById("edit_id_contacto").value = id;
    document.getElementById("edit_nombre_contacto").value = nombre;
    document.getElementById("edit_telefono_contacto").value = telefono;
    document.getElementById("editContactoModal").classList.add("show");
}
function closeEditContactoModal() {
    document.getElementById("editContactoModal").classList.remove("show");
}

// Funciones para editar Encargado
function openEditEncargadoModal(id, nombre, telefono) {
    document.getElementById("edit_id_encargado").value = id;
    document.getElementById("edit_nombre_encargado").value = nombre;
    document.getElementById("edit_telefono_encargado").value = telefono;
    document.getElementById("editEncargadoModal").classList.add("show");
}
function closeEditEncargadoModal() {
    document.getElementById("editEncargadoModal").classList.remove("show");
}

// Cerrar modal al hacer clic fuera
window.onclick = function(event) {
    if (event.target === document.getElementById("editContactoModal")) {
        closeEditContactoModal();
    }
    if (event.target === document.getElementById("editEncargadoModal")) {
        closeEditEncargadoModal();
    }
};
</script>

</body>
</html>
