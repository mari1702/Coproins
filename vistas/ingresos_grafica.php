<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

require '../bd/db_conexion.php';

// Obtener ingresos por mes
$stmtMes = $pdo->prepare("
    SELECT DATE_FORMAT(g.fecha, '%Y-%m') AS mes, SUM(g.monto) AS total_ingresos 
    FROM gasto g
    INNER JOIN nuevo_proyecto np ON g.id_nuevo_proyecto = np.id_nuevo_proyecto
    LEFT JOIN proyectos_compartidos pc ON np.id_nuevo_proyecto = pc.id_proyecto
    WHERE g.tipo_gasto = 'ingreso' 
      AND (np.id_admin_creador = :id_admin OR pc.id_admin = :id_admin1)
    GROUP BY mes
    ORDER BY mes
");
$stmtMes->bindParam(':id_admin', $_SESSION['id_usuario'], PDO::PARAM_INT);
$stmtMes->bindParam(':id_admin1', $_SESSION['id_usuario'], PDO::PARAM_INT);

$stmtMes->execute();
$ingresosMes = $stmtMes->fetchAll(PDO::FETCH_ASSOC);



$meses = [];
$totalesMes = [];
foreach ($ingresosMes as $ingreso) {
    $meses[] = $ingreso['mes'];
    $totalesMes[] = $ingreso['total_ingresos'];
}

// Obtener ingresos por año
$stmtAnio = $pdo->prepare("
    SELECT YEAR(g.fecha) AS anio, SUM(g.monto) AS total_ingresos 
    FROM gasto g
    INNER JOIN nuevo_proyecto np ON g.id_nuevo_proyecto = np.id_nuevo_proyecto
    LEFT JOIN proyectos_compartidos pc ON np.id_nuevo_proyecto = pc.id_proyecto
    WHERE g.tipo_gasto = 'ingreso' 
      AND (np.id_admin_creador = :id_admin OR pc.id_admin = :id_admin1)
    GROUP BY anio
    ORDER BY anio
");
$stmtAnio->bindParam(':id_admin', $_SESSION['id_usuario'], PDO::PARAM_INT);
$stmtAnio->bindParam(':id_admin1', $_SESSION['id_usuario'], PDO::PARAM_INT);

$stmtAnio->execute();
$ingresosAnio = $stmtAnio->fetchAll(PDO::FETCH_ASSOC);



$anios = [];
$totalesAnio = [];
foreach ($ingresosAnio as $ingreso) {
    $anios[] = $ingreso['anio'];
    $totalesAnio[] = $ingreso['total_ingresos'];
}

// Obtener clientes y su monto inicial del proyecto
$stmtClientes = $pdo->prepare("
    SELECT c.cliente, SUM(np.costo_inicial) AS total_proyectos
    FROM nuevo_proyecto np
    JOIN cliente c ON np.id_cliente = c.id_cliente
    LEFT JOIN proyectos_compartidos pc ON np.id_nuevo_proyecto = pc.id_proyecto
    WHERE np.id_admin_creador = :id_admin OR pc.id_admin = :id_admin1
    GROUP BY c.cliente
    ORDER BY total_proyectos DESC
");
$stmtClientes->bindParam(':id_admin', $_SESSION['id_usuario'], PDO::PARAM_INT);
$stmtClientes->bindParam(':id_admin1', $_SESSION['id_usuario'], PDO::PARAM_INT);

$stmtClientes->execute();
$clientesProyectos = $stmtClientes->fetchAll(PDO::FETCH_ASSOC);


$clientes = [];
$montosClientes = [];
foreach ($clientesProyectos as $cliente) {
    $clientes[] = $cliente['cliente'];
    $montosClientes[] = $cliente['total_proyectos'];
}

// Convertir datos a JSON
$mesesJson = json_encode($meses);
$totalesMesJson = json_encode($totalesMes);
$aniosJson = json_encode($anios);
$totalesAnioJson = json_encode($totalesAnio);
$clientesJson = json_encode($clientes);
$montosClientesJson = json_encode($montosClientes);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resumen de Ingresos</title>
    <link rel="stylesheet" href="../css/ingresos_grafica.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<style>
    /* Estilos Generales */
    body {
        font-family: Arial, sans-serif;
        background-color: #f9f9f9;
        margin: 0;
        padding: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    /* Contenedores */
    .container {
        width: 90%;
        max-width: 1200px;
        background: #ffffff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        margin: 20px auto;
    }

    /* Gráfica */
    .chart-container {
        width: 100%;
        max-width: 800px;
        margin: 20px auto;
    }

    canvas {
        max-width: 100%;
    }

    /* Responsividad */
    @media (max-width: 768px) {
        .container {
            width: 100%;
            padding: 15px;
        }
    }
</style>
<body>

<?php include 'navbar.php'; ?>

<div class="container">
    
    <h2>Ingresos por Mes</h2>
    <div class="chart-container">
        <canvas id="ingresosMesChart"></canvas>
    </div>
</div>

<div class="container">
    <h2>Ingresos por Año</h2>
    <div class="chart-container">
        <canvas id="ingresosAnioChart"></canvas>
    </div>
</div>

<div class="container">
    <h2>Clientes con Mayor Monto de Proyectos</h2>
    <div class="chart-container">
        <canvas id="clientesChart"></canvas>
    </div>
</div>
<script>
    const meses = <?= $mesesJson ?>;
    const ingresosMes = <?= $totalesMesJson ?>;
    const anios = <?= $aniosJson ?>;
    const ingresosAnio = <?= $totalesAnioJson ?>;
    const clientes = <?= $clientesJson ?>;
    const montosClientes = <?= $montosClientesJson ?>;

    // 🎨 Colores reutilizables
    const colores = [
        'rgba(255, 99, 132, 0.6)',
        'rgba(54, 162, 235, 0.6)',
        'rgba(255, 206, 86, 0.6)',
        'rgba(75, 192, 192, 0.6)',
        'rgba(153, 102, 255, 0.6)',
        'rgba(255, 159, 64, 0.6)',
        'rgba(199, 199, 199, 0.6)',
        'rgba(83, 102, 255, 0.6)',
        'rgba(255, 99, 71, 0.6)',
        'rgba(60, 179, 113, 0.6)',
        'rgba(100, 149, 237, 0.6)',
        'rgba(255, 140, 0, 0.6)'
    ];

    // 📊 Gráfico de Ingresos por Mes (cada barra como dataset)
    const ctxMes = document.getElementById('ingresosMesChart').getContext('2d');
    new Chart(ctxMes, {
        type: 'bar',
        data: {
            labels: meses,
            datasets: meses.map((mes, i) => ({
                label: mes,
                data: [ingresosMes[i]],
                backgroundColor: colores[i % colores.length],
                borderColor: colores[i % colores.length].replace('0.6', '1'),
                borderWidth: 1
            }))
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom'
                }
            },
            scales: {
                x: { display: false }, // Oculta eje x para evitar duplicado
                y: { beginAtZero: true }
            }
        }
    });

    // 📊 Gráfico de Ingresos por Año (cada barra como dataset)
    const ctxAnio = document.getElementById('ingresosAnioChart').getContext('2d');
    new Chart(ctxAnio, {
        type: 'bar',
        data: {
            labels: anios,
            datasets: anios.map((anio, i) => ({
                label: anio,
                data: [ingresosAnio[i]],
                backgroundColor: colores[i % colores.length],
                borderColor: colores[i % colores.length].replace('0.6', '1'),
                borderWidth: 1
            }))
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom'
                }
            },
            scales: {
                x: { display: false },
                y: { beginAtZero: true }
            }
        }
    });

    // 📈 Gráfico de Clientes (pastel, sin cambios)
    const ctxClientes = document.getElementById('clientesChart').getContext('2d');
    new Chart(ctxClientes, {
        type: 'pie',
        data: {
            labels: clientes,
            datasets: [{
                label: 'Monto Total de Proyectos',
                data: montosClientes,
                backgroundColor: colores,
                borderColor: colores.map(c => c.replace('0.6', '1')),
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
</script>


</body>
</html>
