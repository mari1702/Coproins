<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}
require '../bd/db_conexion.php';
// Obtener el id del admin actual
$idAdminActual = $_SESSION['id_usuario'];

// Obtener lista de administradores excluyendo el actual
$queryAdmins = "SELECT id_usuario, usuario FROM usuario WHERE rol = 'admin' AND id_usuario != :id_admin";
$stmtAdmins = $pdo->prepare($queryAdmins);
$stmtAdmins->bindParam(':id_admin', $idAdminActual, PDO::PARAM_INT);
$stmtAdmins->execute();
$admins = $stmtAdmins->fetchAll(PDO::FETCH_ASSOC);


$idAdmin = $_SESSION['id_usuario'];

/* $idAdmin = $_SESSION['id_usuario'];

$idAdmin = $_SESSION['id_usuario']; */

$queryClientes = "
    SELECT c.id_cliente, c.cliente
    FROM cliente c
    WHERE c.id_admin_creador = :id_admin
      AND c.estado_cliente != 'Finalizado'
    ORDER BY c.cliente ASC
";

$stmtClientes = $pdo->prepare($queryClientes);
$stmtClientes->bindParam(':id_admin', $idAdmin, PDO::PARAM_INT);
$stmtClientes->execute();
$clientes = $stmtClientes->fetchAll(PDO::FETCH_ASSOC);





?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Proyecto</title>
    <link rel="stylesheet" href="../css/nuevo_proyecto.css">
   
</head>

<body>
<?php include 'navbar.php'; ?>

<div class="form-container">
<button onclick="window.history.back();" class="back-button">🔙 Regresar</button>

        <h2>Nuevo Proyecto</h2>
<!-- Botón para ver Contactos y Responsables -->

<div class="form-group">
    <a href="ver_contactos_responsables.php" class="submit-btn">
        Ver Contactos y Responsables
    </a>
    
</div>


        <?php if (isset($_GET['message'])): ?>
            <div class="alert">
                <?= htmlspecialchars($_GET['message']) ?>
            </div>
        <?php endif; ?>

        <form id="projectForm" action="../php/procesar.php" method="POST">
            <div class="form-row">
            <div class="form-group">
                    <label for="cliente">Empresa</label>
                    <div class="input-group">
                    <select id="cliente" name="cliente" required onchange="actualizarEmpresaSeleccionada(); cargarContactos(); cargarResponsables();">

                            <option value="" disabled selected>Seleccione</option>
                            <?php
                            require '../bd/db_conexion.php';
                            foreach ($clientes as $cliente): ?>
                                <option value="<?= $cliente['id_cliente'] ?>"><?= $cliente['cliente'] ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button type="button" onclick="openClientModal()">+</button>
                    </div>
                </div>

                <!-- Contacto -->
                <div class="form-group">
                    <label for="contacto">Contacto</label>
                    <div class="input-group">
                    <select id="contacto" name="contacto" required>
                        <option value="" disabled selected>Seleccione un contacto</option>
                    </select>

                    <button type="button" onclick="actualizarEmpresaSeleccionada(); openContactoModal()">+</button>

                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="trabajador">Responsable</label>
                <div class="input-group">
                    <select id="trabajador" name="trabajador" required>
                        <option value="" disabled selected>Seleccione un responsable</option>
                    </select>
                    <button type="button" onclick="actualizarEmpresaSeleccionada(); openEncargadoModal()">+</button>

                </div>
            </div>



            <div class="form-group">
                <label for="estado">Estado</label>
                <select id="estado" name="estado" required>
                    <option value="" disabled selected>Seleccione un estado</option>
                    <?php
                    $estados = ["Aguascalientes", "Baja California", "Baja California Sur", "Campeche", "Chiapas",
                                "Chihuahua", "Coahuila", "Colima", "Ciudad de Mexico", "Durango", "Estado de México", "Guanajuato",
                                "Guerrero", "Hidalgo", "Jalisco", "Michoacán", "Morelos", "Nayarit", "Nuevo León",
                                "Oaxaca", "Puebla", "Querétaro", "Quintana Roo", "San Luis Potosí", "Sinaloa",
                                "Sonora", "Tabasco", "Tamaulipas", "Tlaxcala", "Veracruz", "Yucatán", "Zacatecas"];
                    foreach ($estados as $estado) {
                        echo "<option value='$estado'>$estado</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label for="localidad">Localidad</label>
                <input type="text" id="localidad" name="localidad" required>
            </div>

            <div class="form-group">
                <label for="costo">Costo Inicial</label>
                <input type="number" step="0.01" id="costo" name="costo">
            </div>
                     <!-- 🏆 Selección de Administrador con el que se compartirá -->

            <?php if (isset($_SESSION['usuario']) && $_SESSION['usuario'] === 'gmoreno'): ?>
                <label>Compartir con:</label>
                <select name="id_admin_compartido">
                    <option value="">Ninguno</option>
                    <?php foreach ($admins as $admin): ?>
                        <option value="<?= $admin['id_usuario']; ?>"><?= htmlspecialchars($admin['usuario']); ?></option>
                    <?php endforeach; ?>
                </select>
            <?php endif; ?>
            <button type="submit" class="submit-btn">Agregar</button>
        </form>
    </div>


<!-- Modals -->

<!-- Modal Empresa -->
<div id="clientModal" class="modal">
    <div class="modal-content">
        <span class="close-button" onclick="closeClientModal()">&times;</span>
        <h3>Agregar Empresa</h3>
        <form action="../php/procesar.php" method="POST">
            <div class="form-group">
                <label for="clientName">Nombre de la empresa</label>
                <input type="text" id="clientName" name="clientName" required>
            </div>
            <div class="form-group">
                <label for="empresaTelefono">Teléfono de la Empresa</label>
                <input type="text" id="empresaTelefono" name="empresaTelefono" required>
            </div>
            <button type="submit">Registrar Empresa</button>
        </form>
    </div>
</div>

<div id="contactoModal" class="modal">
    <div class="modal-content">
        <span class="close-button" onclick="closeContactoModal()">&times;</span>
        <h3>Agregar Contacto</h3>
        <form action="../php/procesar.php" method="POST">
            <input type="hidden" id="id_cliente" name="id_cliente">
            <div class="form-group">
                <label for="contactoNombre">Nombre Completo</label>
                <input type="text" id="contactoNombre" name="contactoNombre" required>
            </div>
            <div class="form-group">
                <label for="contactoTelefono">Teléfono</label>
                <input type="text" id="contactoTelefono" name="contactoTelefono">
            </div>
            <button type="submit">Agregar</button>
        </form>
    </div>
</div>

<div id="encargadoModal" class="modal">
    <div class="modal-content">
        <span class="close-button" onclick="closeEncargadoModal()">&times;</span>
        <h3>Agregar Responsable</h3>
        <form action="../php/procesar.php" method="POST" onsubmit="return prepararEnvioResponsable(event)">

        <input type="hidden" id="id_cliente_responsable" name="id_cliente">



            <div class="form-group">
                <label for="encargadoNombre">Nombre Completo</label>
                <input type="text" id="encargadoNombre" name="encargadoNombre" required>
            </div>
            <div class="form-group">
                <label for="encargadoTelefono">Teléfono</label>
                <input type="text" id="encargadoTelefono" name="encargadoTelefono">
            </div>
            <button type="submit">Agregar</button>
        </form>
    </div>
</div>

<script>
    
        // Función para actualizar el ID de la empresa seleccionada en los formularios de contacto y encargado
        function actualizarEmpresaSeleccionada() {
    const empresaSeleccionada = document.getElementById("cliente").value;
    document.getElementById("id_cliente").value = empresaSeleccionada;
    console.log("Empresa seleccionada:", empresaSeleccionada);
}


        function cargarContactos() {
    let idEmpresa = document.getElementById("cliente").value;
    
    fetch(`../php/obtener_contactos.php?id_empresa=${idEmpresa}`)
        .then(response => response.json())
        .then(data => {
            let selectContacto = document.getElementById("contacto");
            selectContacto.innerHTML = '<option value="" disabled selected>Seleccione un contacto</option>';
            
            data.forEach(contacto => {
                let option = document.createElement("option");
                option.value = contacto.id_contacto;
                option.textContent = contacto.nombre;
                selectContacto.appendChild(option);
            });
        })
        .catch(error => console.error("Error cargando contactos:", error));
}

function cargarResponsables() {
    const clienteId = document.getElementById("cliente").value;
    const responsableSelect = document.getElementById("trabajador");

    // Limpiar completamente
    while (responsableSelect.firstChild) {
        responsableSelect.removeChild(responsableSelect.firstChild);
    }

    const defaultOption = document.createElement("option");
    defaultOption.disabled = true;
    defaultOption.selected = true;
    defaultOption.textContent = "Seleccione un responsable";
    responsableSelect.appendChild(defaultOption);

    if (clienteId) {
        fetch(`../php/obtener_responsables.php?id_empresa=${clienteId}`)
            .then(response => response.json())
            .then(data => {
                const idsUnicos = new Set();

                data.forEach(encargado => {
                    if (!idsUnicos.has(encargado.id_encargado)) {
                        idsUnicos.add(encargado.id_encargado);

                        const option = document.createElement("option");
                        option.value = encargado.id_encargado;
                        option.textContent = encargado.nombre_completo;
                        responsableSelect.appendChild(option);
                    }
                });

                if (idsUnicos.size === 0) {
                    const noOption = document.createElement("option");
                    noOption.textContent = "No hay responsables registrados";
                    noOption.disabled = true;
                    responsableSelect.appendChild(noOption);
                }
            })
            .catch(error => {
                console.error("Error al obtener encargados:", error);
            });
    }
}


// Eventos al cambiar cliente
document.getElementById("cliente").addEventListener("change", cargarContactos);
function prepararEnvioResponsable(event) {
    const empresaSeleccionada = document.getElementById("cliente").value;

    if (!empresaSeleccionada) {
        alert("Por favor, seleccione una empresa antes de agregar un responsable.");
        event.preventDefault();
        return false;
    }

    document.getElementById("id_cliente_responsable").value = empresaSeleccionada;
    return true;
}

    </script>                                                               
<script src="../js/proyecto.js"></script>

</body>
</html>
