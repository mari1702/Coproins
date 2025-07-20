<?php
session_start();
require '../bd/db_conexion.php';

// Verificar acceso
if (
    !isset($_SESSION['rol']) || ($_SESSION['rol'] !== 'encargado' 
    &&
     $_SESSION['rol'] !== 'admin')) {
    header("Location: ../index.php");
    exit();
}

// Obtener el ID del administrador que creó al usuario si es un encargado
$idUsuario = $_SESSION['id_usuario'];
$idAdmin = $idUsuario;

if ($_SESSION['rol'] === 'encargado') {
    $stmtAdmin = $pdo->prepare("SELECT id_admin_creador FROM usuario WHERE id_usuario = :id_usuario");
    $stmtAdmin->bindParam(':id_usuario', $idUsuario, PDO::PARAM_INT);
    $stmtAdmin->execute();
    $idAdmin = $stmtAdmin->fetchColumn(); // Ahora tenemos el ID del admin creador
}
// 🔥 Nueva consulta: SOLO MUESTRA GASTOS DEL ADMIN ACTUAL O COMPARTIDOS
$queryGastos = "
    SELECT g.id_gasto, g.id_nuevo_proyecto, g.tipo_gasto, g.monto, g.gasto, g.fecha, 
           u.usuario AS registrado_por, 
           COALESCE(np.localidad, '') AS localidad, 
           c.id_cliente, c.cliente, c.estado_cliente,
           np.estado_proyecto  
    FROM gasto g
    INNER JOIN usuario u ON g.id_usuario = u.id_usuario
    INNER JOIN cliente c ON g.id_cliente = c.id_cliente
    LEFT JOIN nuevo_proyecto np ON g.id_nuevo_proyecto = np.id_nuevo_proyecto
    LEFT JOIN proyectos_compartidos pc ON np.id_nuevo_proyecto = pc.id_proyecto
    WHERE (
        np.id_admin_creador = :id_admin1
        OR pc.id_admin = :id_admin2
        OR (g.id_nuevo_proyecto IS NULL AND c.id_cliente IN (
            SELECT DISTINCT id_cliente FROM nuevo_proyecto 
            WHERE id_admin_creador = :id_admin3
               OR id_nuevo_proyecto IN (SELECT id_proyecto FROM proyectos_compartidos WHERE id_admin = :id_admin4)
        ))
    )
    ORDER BY g.id_gasto DESC";



$stmtGastos = $pdo->prepare($queryGastos);
$stmtGastos->bindParam(':id_admin1', $idAdmin, PDO::PARAM_INT);
$stmtGastos->bindParam(':id_admin2', $idAdmin, PDO::PARAM_INT);
$stmtGastos->bindParam(':id_admin3', $idAdmin, PDO::PARAM_INT);
$stmtGastos->bindParam(':id_admin4', $idAdmin, PDO::PARAM_INT);
$stmtGastos->execute();
$gastos = $stmtGastos->fetchAll(PDO::FETCH_ASSOC);





// Obtener lista de clientes del admin y sus proyectos compartidos
$queryClientes = "
    SELECT DISTINCT c.id_cliente, c.cliente
    FROM cliente c
    INNER JOIN nuevo_proyecto np ON c.id_cliente = np.id_cliente
    LEFT JOIN proyectos_compartidos pc ON np.id_nuevo_proyecto = pc.id_proyecto
    WHERE (np.id_admin_creador = :id_admin
    AND np.estado_proyecto != 'Finalizado') 
    OR pc.id_admin = :id_admin1
    AND c.estado_cliente != 'Finalizado'
    ORDER BY c.cliente ASC
";


$stmtClientes = $pdo->prepare($queryClientes);
$stmtClientes->bindParam(':id_admin', $idAdmin, PDO::PARAM_INT);
$stmtClientes->bindParam(':id_admin1', $idAdmin, PDO::PARAM_INT);

$stmtClientes->execute();
$clientes = $stmtClientes->fetchAll(PDO::FETCH_ASSOC);


// 🔥 Obtener sucursales SOLO del administrador actual o compartidas
$querySucursales = "
    SELECT np.id_nuevo_proyecto, np.localidad, np.id_cliente
    FROM nuevo_proyecto np
    LEFT JOIN proyectos_compartidos pc ON np.id_nuevo_proyecto = pc.id_proyecto
    WHERE np.id_admin_creador = :id_admin
       OR pc.id_admin = :id_admin1
    ORDER BY np.localidad ASC
";

$stmtSucursales = $pdo->prepare($querySucursales);
$stmtSucursales->bindParam(':id_admin', $idAdmin, PDO::PARAM_INT);
$stmtSucursales->bindParam(':id_admin1', $idAdmin, PDO::PARAM_INT);

$stmtSucursales->execute();
$sucursales = $stmtSucursales->fetchAll(PDO::FETCH_ASSOC);




// Obtener lista de encargados para filtrar gastos

// 🔥 Nueva consulta: SOLO OBTENER ENCARGADOS DEL ADMIN ACTUAL O DE PROYECTOS COMPARTIDOS
$encargadosQuery = "
    SELECT DISTINCT u.id_usuario, u.usuario 
    FROM usuario u
    INNER JOIN gasto g ON u.id_usuario = g.id_usuario
    INNER JOIN nuevo_proyecto np ON g.id_nuevo_proyecto = np.id_nuevo_proyecto
    LEFT JOIN proyectos_compartidos pc ON np.id_nuevo_proyecto = pc.id_proyecto
    WHERE 
        np.id_admin_creador = :id_admin 
        OR pc.id_admin = :id_admin1
";

$encargadosStmt = $pdo->prepare($encargadosQuery);
$encargadosStmt->bindParam(':id_admin', $idAdmin, PDO::PARAM_INT);
$encargadosStmt->bindParam(':id_admin1', $idAdmin, PDO::PARAM_INT);
$encargadosStmt->execute();
$encargados = $encargadosStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gastos</title>
    <link rel="stylesheet" href="../css/gasto.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<style>
    
</style>
<body>

<?php include 'navbar.php'; ?>

<main>
    <?php
if (isset($_SESSION['mensaje'])) {
    echo '<script>alert("' . $_SESSION['mensaje'] . '");</script>';
    unset($_SESSION['mensaje']); // Limpiar el mensaje para que no se muestre de nuevo
}
?>

<div class="form-container">
        <h2>Registrar Gasto</h2>
        <div id="alert" class="alert"></div>

        <form id="gastoForm" class="gasto-form" action="../php/registrar_gastos.php" method="POST" onsubmit="redirectAfterSubmit(event)"> 
            
            <!-- Selección de Cliente -->
            <div class="form-group">
            <label for="cliente">Cliente</label>
            <select id="cliente" name="cliente" required onchange="cargarSucursales()">
                <option value="" disabled selected>Seleccione un cliente</option>
                <?php foreach ($clientes as $cliente): ?>
                    <option value="<?= $cliente['id_cliente'] ?>"><?= htmlspecialchars($cliente['cliente']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="sucursal">Sucursal</label>
            <select id="sucursal" name="sucursal" required>
                <option value="" disabled selected>Seleccione una sucursal</option>
            </select>
        </div>

            <!-- Campo Nombre del Gasto -->
            <div class="form-group">
                <label for="nombre_gasto">Nombre del Gasto</label>
                <input type="text" id="nombre_gasto" name="nombre_gasto" placeholder="Ingrese el nombre del gasto" required>
            </div>

            <!-- Tipo de Gasto -->
            <div class="form-group">
                <label for="tipo">Tipo de gasto</label>
                <select id="tipo" name="tipo" required>
                    <option value="" disabled selected>Seleccione el tipo de gasto</option>
    <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin'): ?>

                    <option value="ingreso">Ingreso</option>
                    <?php endif; ?>

                    <option value="egreso">Egreso</option>


                </select>
            </div>

            <!-- Campo Fecha -->
            <div class="form-group">
                <label for="fecha">Fecha del Gasto</label>
                <input type="date" id="fecha" name="fecha" required>
            </div>
            
            <!-- Campo Monto -->
            <div class="form-group">
                <label for="monto">Monto</label>
                <input type="number" id="monto" name="monto" step="0.01" placeholder="$" required>
            </div>

            <button type="submit" class="button1">Agregar</button>
        </form>
    </div>


    <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin'): ?>
       
        <div class="table-container">
    <h2>Lista de Gastos</h2>

    <!-- 🔍 FILTRO POR ENCARGADO -->
    <label for="filtroEncargado">Filtrar por Encargado:</label>
    <select id="filtroEncargado" onchange="filtrarTabla()">
        <option value="">Todos</option>
        <?php foreach ($encargados as $encargado): ?>
            <option value="<?= htmlspecialchars($encargado['usuario']) ?>"><?= htmlspecialchars($encargado['usuario']) ?></option>
        <?php endforeach; ?>
    </select>

    <br>

    <!-- 🔍 FILTRO POR CLIENTE -->
    <label for="filtroCliente">Buscar por Cliente:</label>
    <input type="text" id="filtroCliente" onkeyup="filtrarTabla()" placeholder="Escribe el nombre del cliente...">

    <div id="resultadoClientes"></div>

    <table class="styled-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Cliente</th>
                <th>Sucursal</th>
                <th>Nombre del Gasto</th>
                <th>Tipo</th>
                <th>Monto</th>
                <th>Fecha</th>
                <th>Registrado por</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody id="tablaGastos">
    <?php foreach ($gastos as $gasto): ?>
    <tr class="gasto-row" 
        data-cliente="<?= strtolower(htmlspecialchars($gasto['cliente'])) ?>" 
        data-encargado="<?= strtolower(htmlspecialchars($gasto['registrado_por'])) ?>"
        data-admin="<?= $idAdmin ?>">

        <td><?= $gasto['id_gasto'] ?></td>
        <td><?= htmlspecialchars($gasto['cliente']) ?></td>
        <td><?= htmlspecialchars($gasto['localidad'] ?? '') ?></td>
        <td><?= htmlspecialchars($gasto['gasto']) ?></td>
        <td><?= htmlspecialchars($gasto['tipo_gasto']) ?></td>
        <td>$<?= number_format($gasto['monto'], 2) ?></td>
        <td><?= $gasto['fecha'] ?></td>
        <td><?= htmlspecialchars($gasto['registrado_por']) ?></td>
        <td>
        <?php if (strtolower($gasto['estado_cliente']) !== 'finalizado' && strtolower($gasto['estado_proyecto']) === 'activo'): ?>

    <button class="button1" onclick="openEditModal(
        '<?= $gasto['id_gasto'] ?>',
        '<?= $gasto['id_cliente'] ?>',
        '<?= $gasto['id_nuevo_proyecto'] ?>', 
        '<?= htmlspecialchars($gasto['gasto']) ?>',
        '<?= $gasto['tipo_gasto'] ?>',
        '<?= $gasto['monto'] ?>',
        '<?= $gasto['fecha'] ?>'
    )">
        <i class="fas fa-edit"></i>
    </button>

    <form method="POST" action="../php/eliminar_gasto.php" onsubmit="return confirm('¿Seguro que deseas eliminar este gasto?');">
        <input type="hidden" name="id_gasto" value="<?= $gasto['id_gasto'] ?>">
        <button class="delete-btn" type="submit"><i class="fas fa-trash-alt"></i></button>
    </form>
<?php else: ?>
    <button class="button1" disabled style="opacity: 0.5; cursor: not-allowed;" title="Cliente finalizado">
        <i class="fas fa-edit"></i>
    </button>
    <button class="delete-btn" disabled style="opacity: 0.5; cursor: not-allowed;" title="Cliente finalizado">
        <i class="fas fa-trash-alt"></i>
    </button>
<?php endif; ?>
</td>

    </tr>
    <?php endforeach; ?>
</tbody>

    </table>
</div>


    <?php endif; ?>

</main>
<!-- 🔹 Modal para Editar Gasto -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close-button" onclick="closeEditModal()">&times;</span>
        <h2>Editar Gasto</h2>
        <form id="editGastoForm" action="../php/editar_gasto.php" method="POST">
            <input type="hidden" id="edit_id_gasto" name="id_gasto">
            
            <!-- Cliente -->
            <label>Cliente:</label>
            <select id="edit_cliente" name="cliente" required onchange="actualizarSucursales()">
                <option value="" disabled selected>Seleccione un cliente</option>
                <?php foreach ($clientes as $cliente): ?>
                    <option value="<?= $cliente['id_cliente']; ?>"><?= $cliente['cliente']; ?></option>
                <?php endforeach; ?>
            </select>

            <!-- Sucursal -->
            <label>Sucursal:</label>
            <select id="edit_sucursal" name="sucursal" >
                <option value="" disabled selected>Seleccione una sucursal</option>
                <option value="">Cliente (Sin sucursal)</option>

            </select>

            <!-- Nombre del Gasto -->
            <label>Nombre del Gasto:</label>
            <input type="text" id="edit_nombre_gasto" name="nombre_gasto" required>

            <!-- Tipo de Gasto -->
            <label>Tipo de Gasto:</label>
            <select id="edit_tipo" name="tipo" required>
                <option value="ingreso">Ingreso</option>
                <option value="egreso">Egreso</option>
            </select>

            <!-- Monto -->
            <label>Monto:</label>
            <input type="number" id="edit_monto" name="monto" step="0.01" required>

            <!-- Fecha -->
            <label>Fecha:</label>
            <input type="date" id="edit_fecha" name="fecha" required>

            <button type="submit"><i class="fas fa-save"></i> Guardar Cambios</button>
        </form>
    </div>
</div>



<script>
    
     function redirectAfterSubmit(event) {
        setTimeout(() => {
            window.location.href = "proyectos_habilitados.php";
        }, 1000); // Redirige después de 1 segundo
    }

    function cargarSucursales() {
        const clienteId = document.getElementById('cliente').value;
        const sucursalSelect = document.getElementById('sucursal');

        // Limpiar opciones previas
        sucursalSelect.innerHTML = '<option value="" disabled selected>Seleccione una sucursal</option>';

        if (clienteId) {
            fetch(`../php/obtener_sucursales.php?id_cliente=${clienteId}`)
                .then(response => response.json())
                .then(data => {
                    data.forEach(sucursal => {
                        const option = document.createElement('option');
                        option.value = sucursal.id_nuevo_proyecto;
                        option.textContent = sucursal.localidad;
                        sucursalSelect.appendChild(option);
                    });
                })
                .catch(error => console.error('Error obteniendo sucursales:', error));
        }
    }
    // 🔥 Lista de sucursales por cliente (para filtrarlas correctamente)
const sucursalesPorCliente = <?= json_encode($sucursales); ?>;

// 📌 Función para actualizar las sucursales en el modal
function actualizarSucursales() {
    const clienteSeleccionado = document.getElementById('edit_cliente').value;
    const selectSucursal = document.getElementById('edit_sucursal');

    // Limpiar opciones previas
    selectSucursal.innerHTML = '<option value="" disabled selected>Seleccione una sucursal</option>';

    // Filtrar sucursales por el cliente seleccionado
    const sucursalesFiltradas = sucursalesPorCliente.filter(sucursal => sucursal.id_cliente == clienteSeleccionado);

    // Crear opciones en el select
    sucursalesFiltradas.forEach(sucursal => {
        const option = document.createElement('option');
        option.value = sucursal.id_nuevo_proyecto;
        option.textContent = sucursal.localidad;
        selectSucursal.appendChild(option);
    });

    if (sucursalesFiltradas.length === 0) {
        const option = document.createElement('option');
        option.textContent = 'No hay sucursales disponibles';
        selectSucursal.appendChild(option);
    }
}

// 📌 Función para abrir el modal y cargar datos correctamente
window.openEditModal = function (id, cliente, sucursal, nombreGasto, tipo, monto, fecha) {
    console.log("🟢 Abriendo modal para ID:", id, "Cliente:", cliente, "Sucursal:", sucursal, "Gasto:", nombreGasto);

    // Obtener los elementos del formulario
    const clienteSelect = document.getElementById("edit_cliente");
    const sucursalSelect = document.getElementById("edit_sucursal");
    const nombreGastoInput = document.getElementById("edit_nombre_gasto");
    const tipoGastoSelect = document.getElementById("edit_tipo");
    const montoInput = document.getElementById("edit_monto");
    const fechaInput = document.getElementById("edit_fecha");

    if (!clienteSelect || !sucursalSelect || !nombreGastoInput || !tipoGastoSelect || !montoInput || !fechaInput) {
        console.error("❌ Error: No se encontraron todos los elementos del modal.");
        return;
    }

    // Asignar valores a los inputs del modal
    document.getElementById("edit_id_gasto").value = id;
    clienteSelect.value = cliente;
    nombreGastoInput.value = nombreGasto;
    tipoGastoSelect.value = tipo;
    montoInput.value = monto;
    fechaInput.value = fecha;

    // Cargar las sucursales disponibles para el cliente seleccionado
    actualizarSucursales();

    // Seleccionar la sucursal del gasto si existe
    setTimeout(() => {
        sucursalSelect.value = sucursal;
    }, 100);

    // Mostrar el modal
    document.getElementById("editModal").classList.add("show");
};


window.closeEditModal = function () {
    editModal.classList.remove("show");
    location.reload(); // Recargar la página al cerrar el modal
};

window.onclick = function (event) {
    if (event.target === editModal) {
        closeEditModal();
    }
};

function filtrarTabla() {
    var filtroEncargado = document.getElementById('filtroEncargado').value.toLowerCase();
    var filtroCliente = document.getElementById('filtroCliente').value.toLowerCase();
    var rows = document.querySelectorAll("#tablaGastos .gasto-row");

    rows.forEach(row => {
        var cliente = row.getAttribute("data-cliente").toLowerCase();
        var encargado = row.getAttribute("data-encargado").toLowerCase();

        var mostrar = true;

        if (filtroCliente && !cliente.includes(filtroCliente)) {
            mostrar = false;
        }
        if (filtroEncargado && encargado !== filtroEncargado) {
            mostrar = false;
        }

        // ✅ Asegurar que solo se muestren los gastos del admin actual
        if (!row.hasAttribute("data-admin") || row.getAttribute("data-admin") !== "<?= $idAdmin ?>") {
            mostrar = false;
        }

        row.style.display = mostrar ? "" : "none";
    });
}





function filtrarEncargado() {
    let filtro = document.getElementById("filtroEncargado").value.toLowerCase();
    let filas = document.querySelectorAll(".gasto-row");

    filas.forEach(fila => {
        let encargado = fila.getAttribute("data-encargado").toLowerCase();

        if (filtro === "" || encargado === filtro) {
            fila.style.display = "";
        } else {
            fila.style.display = "none";
        }
    });
}

function filtrarTabla() {
    var filtroEncargado = document.getElementById('filtroEncargado').value.toLowerCase();  // Obtener el valor del filtro de encargado
    var filtroCliente = document.getElementById('filtroCliente').value.toLowerCase();  // Obtener el valor del filtro de cliente
    var table = document.getElementById('tablaGastos');  // Obtener la tabla
    var rows = table.getElementsByTagName('tr');  // Obtener todas las filas de la tabla

    // Recorrer todas las filas de la tabla
    for (var i = 0; i < rows.length; i++) {
        var row = rows[i];
        var clienteCell = row.querySelector("td:nth-child(2)");  // Obtener la celda de la columna "Cliente" (segunda columna)
        var encargadoCell = row.querySelector("td:nth-child(8)");  // Obtener la celda de la columna "Registrado por" (octava columna)

        if (clienteCell && encargadoCell) {
            var clienteText = clienteCell.textContent || clienteCell.innerText;  // Obtener el texto del cliente
            var encargadoText = encargadoCell.textContent || encargadoCell.innerText;  // Obtener el texto del encargado

            // Verificar si la fila cumple con ambos filtros
            if (
                (clienteText.toLowerCase().indexOf(filtroCliente) > -1) && 
                (encargadoText.toLowerCase().indexOf(filtroEncargado) > -1)
            ) {
                row.style.display = "";  // Mostrar la fila si ambos filtros coinciden
            } else {
                row.style.display = "none";  // Ocultar la fila si no coinciden con ambos filtros
            }
        }
    }
}


</script>

</body>
</html>