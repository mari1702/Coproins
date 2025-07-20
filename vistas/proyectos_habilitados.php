<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

require '../bd/db_conexion.php';

// Obtener el id_admin basado en el tipo de usuario logueado
$idAdmin = $_SESSION['id_usuario'];

if ($_SESSION['rol'] === 'usuario') {
    // Si es usuario, obtener el id_admin_creador (quién lo creó)
    $stmt = $pdo->prepare("SELECT id_admin_creador FROM usuario WHERE id_usuario = :id");
    $stmt->bindParam(':id', $idAdmin);
    $stmt->execute();
    $idAdmin = $stmt->fetchColumn();
}

// 🔥 Verificar que el usuario realmente tiene clientes asignados
$queryClientes = "
    WITH primeros_costos AS (
        SELECT h.id_nuevo_proyecto, h.costo
        FROM historial_costo_proyecto h
        JOIN (
            SELECT id_nuevo_proyecto, MIN(fecha_modificacion) AS primera_fecha
            FROM historial_costo_proyecto
            GROUP BY id_nuevo_proyecto
        ) primeras ON h.id_nuevo_proyecto = primeras.id_nuevo_proyecto AND h.fecha_modificacion = primeras.primera_fecha
    ),
    ultimos_costos AS (
        SELECT h.id_nuevo_proyecto, h.costo
        FROM historial_costo_proyecto h
        JOIN (
            SELECT id_nuevo_proyecto, MAX(fecha_modificacion) AS ultima_fecha
            FROM historial_costo_proyecto
            GROUP BY id_nuevo_proyecto
        ) ultimas ON h.id_nuevo_proyecto = ultimas.id_nuevo_proyecto AND h.fecha_modificacion = ultimas.ultima_fecha
    )
    SELECT 
        c.id_cliente, 
        c.cliente, 
        c.telefono_cliente,
        c.estado_cliente,

        COALESCE((
            SELECT MIN(h1.costo)
            FROM historial_costo_proyecto h1
            JOIN nuevo_proyecto np ON np.id_nuevo_proyecto = h1.id_nuevo_proyecto
            WHERE np.id_cliente = c.id_cliente
            AND (
                np.id_admin_creador = :id_admin 
                OR np.id_nuevo_proyecto IN (
                    SELECT id_proyecto FROM proyectos_compartidos WHERE id_admin = :id_admin1
                )
            )
        ), 0) AS costo_inicial,

        COALESCE((
            SELECT SUM(uc.costo)
            FROM nuevo_proyecto np
            JOIN ultimos_costos uc ON np.id_nuevo_proyecto = uc.id_nuevo_proyecto
            WHERE np.id_cliente = c.id_cliente
            AND (
                np.id_admin_creador = :id_admin2 
                OR np.id_nuevo_proyecto IN (
                    SELECT id_proyecto FROM proyectos_compartidos WHERE id_admin = :id_admin3
                )
            )
        ), 0) AS costo_final,

        COALESCE(SUM(CASE WHEN g.tipo_gasto = 'ingreso' THEN g.monto ELSE 0 END), 0) AS total_ingresos,

        COALESCE(SUM(CASE WHEN g.tipo_gasto = 'egreso' AND g.id_nuevo_proyecto IS NOT NULL THEN g.monto ELSE 0 END), 0) AS egresos_sucursal,

        (
            SELECT estado_proyecto 
            FROM nuevo_proyecto np 
            WHERE np.id_cliente = c.id_cliente 
            AND (
                np.id_admin_creador = :id_admin4 
                OR np.id_nuevo_proyecto IN (
                    SELECT id_proyecto FROM proyectos_compartidos WHERE id_admin = :id_admin5
                )
            )
            ORDER BY np.id_nuevo_proyecto DESC LIMIT 1
        ) AS estado_proyecto,

        (
            SELECT id_nuevo_proyecto 
            FROM nuevo_proyecto np 
            WHERE np.id_cliente = c.id_cliente 
            AND (
                np.id_admin_creador = :id_admin6 
                OR np.id_nuevo_proyecto IN (
                    SELECT id_proyecto FROM proyectos_compartidos WHERE id_admin = :id_admin7
                )
            )
            ORDER BY np.id_nuevo_proyecto DESC LIMIT 1
        ) AS id_nuevo_proyecto

    FROM cliente c
    LEFT JOIN gasto g ON g.id_cliente = c.id_cliente

    WHERE EXISTS (
        SELECT 1 FROM nuevo_proyecto np
        WHERE np.id_cliente = c.id_cliente
        AND (
            np.id_admin_creador = :id_admin8 
            OR np.id_nuevo_proyecto IN (
                SELECT id_proyecto FROM proyectos_compartidos WHERE id_admin = :id_admin9
            )
        )
    )

    GROUP BY c.id_cliente, c.cliente, c.telefono_cliente, c.estado_cliente
    ORDER BY c.id_cliente DESC;
";

$stmtClientes = $pdo->prepare($queryClientes);

$stmtClientes->bindParam(':id_admin', $idAdmin, PDO::PARAM_INT);
$stmtClientes->bindParam(':id_admin1', $idAdmin, PDO::PARAM_INT);
$stmtClientes->bindParam(':id_admin2', $idAdmin, PDO::PARAM_INT);
$stmtClientes->bindParam(':id_admin3', $idAdmin, PDO::PARAM_INT);
$stmtClientes->bindParam(':id_admin4', $idAdmin, PDO::PARAM_INT);
$stmtClientes->bindParam(':id_admin5', $idAdmin, PDO::PARAM_INT);
$stmtClientes->bindParam(':id_admin6', $idAdmin, PDO::PARAM_INT);
$stmtClientes->bindParam(':id_admin7', $idAdmin, PDO::PARAM_INT);
$stmtClientes->bindParam(':id_admin8', $idAdmin, PDO::PARAM_INT);
$stmtClientes->bindParam(':id_admin9', $idAdmin, PDO::PARAM_INT);

$stmtClientes->execute();
$clientes = $stmtClientes->fetchAll(PDO::FETCH_ASSOC);
?>



<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clientes</title>
    <link rel="stylesheet" href="../css/proyecto_habilitado.css">
    <script defer src="../js/proyecto.js"></script>
</head>

<body>
    
<?php include 'navbar.php'; ?>

<div class="table-container">
    <h2>Lista de Clientes</h2>


    <button><a href="proyecto.php">Nuevo Proyecto</a></button>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Cliente</th>
                <th>Costo Inicial</th>
                <th>Costo Final</th>
                <th>Ingreso Total</th>
                <th>Egresos</th>
                <th>Saldo Pendiente</th>
                <th>Estatus</th>
                <th>Detalles</th> 
                <th>Estado</th>
                <th>Generar Reporte</th>
            </tr>
        </thead>
        <tbody>
<?php foreach ($clientes as $cliente): 
    $costoInicial = $cliente['costo_inicial']; // desde historial
    $costoFinal = $cliente['costo_final'];     // desde historial
    $ingresosTotales = $cliente['total_ingresos'];
    $egresosSucursal = $cliente['egresos_sucursal'];

    $estadoCliente = $cliente['estado_cliente'];

    $deuda = $costoFinal - $ingresosTotales;
    $ganancia = $ingresosTotales - $egresosSucursal;
    $tooltip = "";
    $estatus = "";
    $estatusClass = "";

    if ($estadoCliente === 'Finalizado') {
        $estatus = "Finalizado";
        $estatusClass = "verde";
        $tooltip = "Proyecto cerrado. Ganancia final: $" . number_format($ganancia, 2);
    } elseif ($ingresosTotales < $egresosSucursal) {
        $estatus = "Pendiente ";
        $estatusClass = "rojo";
        $tooltip = "Los egresos superan a los ingresos. Se está perdiendo dinero.";
    } elseif ($ingresosTotales == $egresosSucursal) {
        $estatus = "Pendiente ";
        $estatusClass = "amarillo";
        $tooltip = "Ingresos y egresos se equilibran. No hay pérdida ni ganancia.";
    } elseif ($ingresosTotales > $egresosSucursal) {
        $estatus = "Pendiente ";
        $estatusClass = "verde";
        $tooltip = "Los ingresos superan a los egresos. Hay margen de ganancia.";
    }
?>
<tr>
    <td><?= htmlspecialchars($cliente['id_cliente']) ?></td>
    <td>
        <a href="sucursales.php?id_cliente=<?= htmlspecialchars($cliente['id_cliente']) ?>" class="btn-detalles">
            <?= htmlspecialchars($cliente['cliente']) ?>
        </a>
    </td>
    <td>$<?= number_format($costoInicial, 2) ?></td>
    <td>$<?= number_format($costoFinal, 2) ?></td>

    <td>$<?= number_format($ingresosTotales, 2) ?></td>
    <td>$<?= number_format($egresosSucursal, 2) ?></td>
    <td>$<?= number_format($deuda, 2) ?></td>


    <td class="<?= $estatusClass ?>" title="<?= $tooltip ?>">
        <?= $estatus ?><br>
        <small><?= $estadoCliente === 'Finalizado' ? 'Ganancia: $' . number_format($ganancia, 2) : '' ?></small>
    </td>

    <td>
        <button type="button" class="openModalDetalles"
            data-id="<?= htmlspecialchars($cliente['id_cliente']) ?>" 
            data-cliente="<?= htmlspecialchars($cliente['cliente']) ?>" 
            data-telefono="<?= htmlspecialchars($cliente['telefono_cliente']) ?>"
            data-costoinicial="<?= $costoInicial ?>"
            data-costofinal="<?= $costoFinal ?>"
            data-ingresos="<?= $ingresosTotales ?>"
            data-egresossucursal="<?= $egresosSucursal ?>"
            
            data-estado="<?= htmlspecialchars($estadoCliente) ?>">
            <i class="fas fa-eye"></i>
        </button>
    </td>

    <td>
    <?php if ($estadoCliente === 'Finalizado'): ?>
        <!-- Botón de Desfinalizar, solo si el cliente está Finalizado -->
        <button onclick="desfinalizarCliente(<?= $cliente['id_cliente'] ?>)" class="activar-button">
        ✅ Finalizado
        </button>
    <?php else: ?>
        <!-- Botón de Finalizar, solo si el cliente está Activo -->
        <button onclick="finalizarCliente(<?= $cliente['id_cliente'] ?>)" class="finalizar-button">
            🚀 Finalizar
        </button>
    <?php endif; ?>
</td>


    <td>
        <a href="../php/reporte_cliente.php?id_cliente=<?= $cliente['id_cliente'] ?>" target="_blank">
            <i class="fas fa-file-pdf"></i> PDF
        </a>
    </td>
</tr>
<?php endforeach; ?>
</tbody>

    </table>
</div>

   <!-- MODAL DE DETALLES -->
<div id="projectModal" class="modal">
    <div class="modal-content">
        <span class="close-button">&times;</span>
        <h2>Detalles del Cliente</h2>
        <form id="clienteForm">
            <input type="hidden" id="clienteId">
            
            <label>Cliente:</label>
            <input type="text" id="modalCliente" readonly>

            <label>Teléfono:</label>
            <input type="text" id="modalTelefono" readonly>

            <label>Costo Inicial:</label>
            <input type="text" id="modalCostoInicial" readonly>

            <label>Costo Final:</label>
            <input type="text" id="modalCostoFinal" readonly>

            <label>Ingreso Total:</label>
            <input type="text" id="modalIngresos" readonly>

            <label>Egresos por Sucursal:</label>
            <input type="text" id="modalEgresosSucursal" readonly>

            <div style="display: none;">
  <label>Egresos por Cliente:</label>
  <input type="text" id="modalEgresosCliente" readonly>
</div>

                        <!-- T de Cuentas de Resultados -->
                        <div class="t-resultados">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th colspan="2">CUENTAS DE RESULTADOS</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><strong>Debe (Egresos)</strong></td>
                                        <td><strong>Haber (Ingresos)</strong></td>
                                    </tr>
                                
                                    <tr>
                                        <td id="gastos">---</td>

                                        <td id="ingresos">---</td>
                                    </tr>
                            
                                    <tr>
                                    <td id="compras">---</td>

                                    <td>---</td>
                                    </tr>
                                    <tr>
                                        <td id="perdidas" style="color: red; font-weight: bold;">---</td>
                                        <td id="ganancias" style="color: green; font-weight: bold;">---</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <button type="button"  id="editButton"
        class="editButton"
        data-id="<?= htmlspecialchars($cliente['id_cliente']) ?>" 
        data-estado="<?= htmlspecialchars(trim($cliente['estado_cliente'])) ?>">
    ✏ Editar
</button>


            <button type="button" id="saveButton" style="display: none;">💾 Guardar</button>
            <button type="button" class="deleteProyecto">🗑 Eliminar</button>
        </form>
    </div>
</div>
<style>

</style>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const modalDetalles = document.getElementById("projectModal");
    const closeModal = modalDetalles.querySelector(".close-button");

    document.querySelectorAll(".openModalDetalles").forEach(button => {
        button.addEventListener("click", function () {
            // Obtener datos directamente del botón (ya están en los atributos data-)
            document.getElementById("modalCliente").value = this.getAttribute("data-cliente");
            document.getElementById("modalTelefono").value = this.getAttribute("data-telefono");
            document.getElementById("modalCostoInicial").value = `$${parseFloat(this.getAttribute("data-costoinicial"))}`;
            document.getElementById("modalCostoFinal").value = `$${parseFloat(this.getAttribute("data-costofinal"))}`;
            document.getElementById("modalIngresos").value = `$${parseFloat(this.getAttribute("data-ingresos"))}`;
            document.getElementById("modalEgresosSucursal").value = `$${parseFloat(this.getAttribute("data-egresossucursal"))}`;

            const ingresos = parseFloat(this.getAttribute("data-ingresos"));
            const egresos = parseFloat(this.getAttribute("data-egresossucursal"));
            const saldo = ingresos - egresos;

            document.getElementById("ingresos").textContent = `$${ingresos}`;
            document.getElementById("gastos").textContent = `$${egresos}`;
            document.getElementById("compras").textContent = "---";
            document.getElementById("perdidas").textContent = saldo < 0 ? `$${Math.abs(saldo)}` : "$0.00";
            document.getElementById("ganancias").textContent = saldo > 0 ? `$${saldo}` : "$0.00";

            document.getElementById("perdidas").style.color = saldo < 0 ? "red" : "black";
            document.getElementById("ganancias").style.color = saldo > 0 ? "green" : "black";

            modalDetalles.classList.add("show");
        });
    });

    closeModal.addEventListener("click", function () {
        modalDetalles.classList.remove("show");
    });

    window.addEventListener("click", function (event) {
        if (event.target === modalDetalles) {
            modalDetalles.classList.remove("show");
        }
    });
});

// Función para finalizar el cliente
function finalizarCliente(idCliente) {
    if (confirm("¿Estás seguro de que deseas finalizar este cliente?")) {
        // Enviar la solicitud para cambiar el estado del cliente a 'Finalizado'
        fetch(`../php/finalizar_desfinalizar_cliente.php?id_cliente=${idCliente}&accion=finalizar`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Cliente finalizado correctamente.');
                    location.reload(); // Recargar la página para ver el cambio
                } else {
                    alert('Hubo un error al finalizar el cliente.');
                }
            });
    }
}

// Función para desfinalizar el cliente
function desfinalizarCliente(idCliente) {
    if (confirm("¿Estás seguro de que deseas desfinalizar este cliente?")) {
        // Enviar la solicitud para cambiar el estado del cliente a 'Activo'
        fetch(`../php/finalizar_desfinalizar_cliente.php?id_cliente=${idCliente}&accion=desfinalizar`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Cliente desfinalizado correctamente.');
                    location.reload(); // Recargar la página para ver el cambio
                } else {
                    alert('Hubo un error al desfinalizar el cliente.');
                }
            });
    }
}


document.addEventListener("DOMContentLoaded", function () {
    const modalDetalles = document.getElementById("projectModal");
    const closeModal = modalDetalles.querySelector(".close-button");
    const editButton = document.getElementById("editButton");

    document.querySelectorAll(".openModalDetalles").forEach(button => {
        button.addEventListener("click", function () {
            const estadoCliente = this.getAttribute("data-estado")?.trim().toLowerCase(); // Obtiene el estado del cliente

            console.log(`Estado del Cliente seleccionado: ${estadoCliente}`); // 🔍 Debug en consola

            // ✅ Habilitar o deshabilitar el botón según el estado
            if (estadoCliente === "finalizado") {
                editButton.disabled = true;
                editButton.style.opacity = "0.5";
                editButton.style.cursor = "not-allowed";
            } else {
                editButton.disabled = false;
                editButton.style.opacity = "1";
                editButton.style.cursor = "pointer";
            }

            modalDetalles.classList.add("show"); // Mostrar modal
        });
    });

    closeModal.addEventListener("click", function () {
        modalDetalles.classList.remove("show");
    });

    window.addEventListener("click", function (event) {
        if (event.target === modalDetalles) {
            modalDetalles.classList.remove("show");
        }
    });
});

</script>
</body>
</html>