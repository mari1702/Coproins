<?php
require '../bd/db_conexion.php';
require '../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

if (!isset($_GET['id_sucursal'])) {
    die("Sucursal no especificada.");
}
$id_sucursal = intval($_GET['id_sucursal']);

// Obtener datos de la sucursal
$stmt = $pdo->prepare("SELECT np.*, c.cliente, e.nombre_completo AS encargado
                       FROM nuevo_proyecto np
                       JOIN cliente c ON np.id_cliente = c.id_cliente
                       JOIN encargado e ON np.id_encargado = e.id_encargado
                       WHERE np.id_nuevo_proyecto = ?");
$stmt->execute([$id_sucursal]);
$sucursal = $stmt->fetch();

if (!$sucursal) {
    die("Sucursal no encontrada.");
}

// Historial de costos
$historialStmt = $pdo->prepare("SELECT * FROM historial_costo_proyecto WHERE id_nuevo_proyecto = ? ORDER BY fecha_modificacion ASC");
$historialStmt->execute([$id_sucursal]);
$historial = $historialStmt->fetchAll();

// Gastos e ingresos
$gastoStmt = $pdo->prepare("SELECT * FROM gasto WHERE id_nuevo_proyecto = ? ORDER BY fecha ASC");
$gastoStmt->execute([$id_sucursal]);
$gastos = $gastoStmt->fetchAll();

$ingresos = 0;
$egresos = 0;
foreach ($gastos as $gasto) {
    if ($gasto['tipo_gasto'] === 'ingreso') {
        $ingresos += $gasto['monto'];
    } else {
        $egresos += $gasto['monto'];
    }
}

ob_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte Sucursal - <?= htmlspecialchars($sucursal['localidad']) ?></title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table, th, td { border: 1px solid #656C6EFF; padding: 5px; }
        th { background-color: #55BCD1; color: white; }
        h1, h2, h3 { text-align: center; color: #0A0A0AFF; }
        .right { text-align: right; }
        .highlight { font-weight: bold; background-color: #f9f9f9; }
        .ingreso { color: green; font-weight: bold; }
        .egreso { color: red; font-weight: bold; }
        .logo { width: 120px; }
    </style>
</head>
<body>

<img src="http://gastos.conproins.com/assets/logo.jpg" class="logo">

<h1>Reporte de Sucursal - <?= htmlspecialchars($sucursal['localidad']) ?></h1>
<p><strong>Cliente:</strong> <?= htmlspecialchars($sucursal['cliente']) ?></p>
<p><strong>Encargado:</strong> <?= htmlspecialchars($sucursal['encargado']) ?></p>
<p><strong>Estado:</strong> <?= htmlspecialchars($sucursal['estado']) ?></p>
<p><strong>Fecha de Registro:</strong> <?= htmlspecialchars($sucursal['fecha']) ?></p>
<p><strong>Costo Actual:</strong> $<?= number_format($sucursal['costo_inicial'], 2) ?></p>
<p><strong>Fecha del Reporte:</strong> <?= date('Y-m-d H:i:s') ?></p>

<h2>Historial de Modificaciones</h2>
<table>
    <thead>
        <tr>
            <th>Fecha</th>
            <th>Nuevo Costo</th>
            <th>Diferencia</th>
        </tr>
    </thead>
    <tbody>
        <?php if (count($historial) > 0): ?>
            <?php foreach ($historial as $mod): ?>
            <tr>
                <td><?= $mod['fecha_modificacion'] ?></td>
                <td class="right">$<?= number_format($mod['costo'], 2) ?></td>
                <td class="right">$<?= number_format($mod['diferencia'], 2) ?></td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="3">Sin modificaciones registradas.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<h2>Gastos e Ingresos</h2>
<table>
    <thead>
        <tr>
            <th>Fecha</th>
            <th>Tipo</th>
            <th>Concepto</th>
            <th>Monto</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($gastos as $gasto): ?>
        <tr>
            <td><?= $gasto['fecha'] ?></td>
            <td><?= ucfirst($gasto['tipo_gasto']) ?></td>
            <td><?= htmlspecialchars($gasto['gasto']) ?></td>
            <td class="right">$<?= number_format($gasto['monto'], 2) ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<h2>Resumen Financiero</h2>
<table>
<tr>
    <td class="highlight">Total Ingresos</td>
    <td class="right ingreso">$<?= number_format($ingresos, 2) ?></td>
</tr>
<tr>
    <td class="highlight">Total Egresos</td>
    <td class="right egreso">$<?= number_format($egresos, 2) ?></td>
</tr>
<tr>
    <td class="highlight">Balance Final</td>
    <td class="right">$<?= number_format($ingresos - $egresos, 2) ?></td>
</tr>
</table>

</body>
</html>
<?php
$html = ob_get_clean();
$options = new Options();
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("Reporte_Sucursal_{$sucursal['localidad']}.pdf", ["Attachment" => false]);
?>
