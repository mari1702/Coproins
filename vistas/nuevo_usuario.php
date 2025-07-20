<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}
require '../bd/db_conexion.php';
$idAdmin = $_SESSION['id_usuario'];

$stmt = $pdo->prepare("SELECT id_usuario, usuario, rol FROM usuario WHERE id_admin_creador = :id_admin");
$stmt->bindParam(':id_admin', $idAdmin, PDO::PARAM_INT);
$stmt->execute();
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Usuario</title>
    <link rel="stylesheet" href="../css/nuevo_usuario.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<style>
    /* Ocultar modales por defecto */
.modal {
    display: none; /* Oculto inicialmente */
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    justify-content: center;
    align-items: center;
}

/* Mostrar el modal cuando se active */
.modal.show {
    display: flex;
}

/* Contenido del modal */
.modal-content {
    background-color: white;
    padding: 20px;
    border-radius: 10px;
    width: 400px;
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
    position: relative;
}

/* Botón de cerrar */
.close-button {
    font-size: 1.5rem;
    color: #333;
    float: right;
    cursor: pointer;
}
label {
    display: block;
    font-size: 1rem;
    font-weight: bold;
    margin-bottom: 5px;
}
select, input {
    width: 100%;
    padding: 10px;
    font-size: 1rem;
    border: 1px solid #ccc;
    border-radius: 5px;
}
</style>
<body>
<?php include 'navbar.php'; ?>
    <main>
        <div class="form-container">
            <h2>Registrar Usuario</h2>
            <form action="../php/procesar_usuario.php" method="POST">
                <div class="form-group">
                    <label for="userUser">Usuario</label>
                    <input type="text" id="userUser" name="userUser" required>
                </div>
                <div class="form-group">
                    <label for="userPassword">Contraseña</label>
                    <input type="password" id="userPassword" name="userPassword" required>
                </div>
                <div class="form-group">
                    <label for="rol">Rol</label>
                    <select id="rol" name="rol" required>
                        <option value="admin">Administrador</option>
                        <option value="encargado">Encargado</option>
                    </select>
                </div>
                <button type="submit"><i class="fas fa-user-plus"></i> Registrar Usuario</button>
            </form>
        </div>
        
        <div class="table-container">
            <h2>Usuarios Registrados</h2>
            <table class="styled-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Usuario</th>
                        <th>Rol</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
    <?php foreach ($usuarios as $usuario): ?>
        <tr>
            <td><?= $usuario['id_usuario'] ?></td>
            <td><?= htmlspecialchars($usuario['usuario']) ?></td>
            <td><?= htmlspecialchars($usuario['rol']) ?></td>
            <td>
                <button onclick="openEditModal(<?= $usuario['id_usuario'] ?>, '<?= htmlspecialchars($usuario['usuario']) ?>', '<?= htmlspecialchars($usuario['rol']) ?>')">
                    <i class="fas fa-edit"></i>
                </button>
                <button onclick="openResetModal(<?= $usuario['id_usuario'] ?>)">
                    <i class="fas fa-key"></i>
                </button>
                <form action="../php/eliminar_usuario.php" method="POST" style="display:inline;" onsubmit="return confirm('¿Seguro que deseas eliminar este usuario?');">
                    <input type="hidden" name="id_usuario" value="<?= $usuario['id_usuario'] ?>">
                    <button type="submit" class="delete-btn"><i class="fas fa-trash-alt"></i></button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
</tbody>

            </table>
        </div>
    </main>
    
    <!-- Modal para editar usuario -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close-button" onclick="closeEditModal()">&times;</span>
            <h2>Editar Usuario</h2>
            <form action="../php/editar_usuario.php" method="POST">
                <input type="hidden" id="editUserId" name="id_usuario">
                <label for="editUser">Usuario</label>
                <input type="text" id="editUser" name="userUser" required>
                <label for="editRol">Rol</label>
                <select id="editRol" name="rol" required>
                    <option value="admin">Administrador</option>
                    <option value="encargado">Encargado</option>
                </select>
                <button type="submit" name="update_user">Actualizar</button>
            </form>
        </div>
    </div>

    <!-- Modal para restablecer contraseña -->
    <div id="resetModal" class="modal">
        <div class="modal-content">
            <span class="close-button" onclick="closeResetModal()">&times;</span>
            <h2>Restablecer Contraseña</h2>
            <form action="../php/restablecer_contrasena.php" method="POST">
                <input type="hidden" id="resetUserId" name="id_usuario">
                <label for="newPassword">Nueva Contraseña</label>
                <input type="password" id="newPassword" name="new_password" required>
                <button type="submit">Restablecer</button>
            </form>
        </div>
    </div>

    <script>
    function openEditModal(id, user, rol) {
        document.getElementById("editUserId").value = id;
        document.getElementById("editUser").value = user;
        document.getElementById("editRol").value = rol;
        document.getElementById("editModal").classList.add("show");
    }
    function closeEditModal() {
        document.getElementById("editModal").classList.remove("show");
    }
    function openResetModal(id) {
        document.getElementById("resetUserId").value = id;
        document.getElementById("resetModal").classList.add("show");
    }
    function closeResetModal() {
        document.getElementById("resetModal").classList.remove("show");
    }
     // Cerrar el modal si el usuario hace clic fuera de él
     window.onclick = function (event) {
        if (event.target.classList.contains("modal")) {
            event.target.classList.remove("show");
        }
    };
    </script>
</body>
</html>
