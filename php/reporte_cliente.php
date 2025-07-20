<?php
require '../bd/db_conexion.php';
require '../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

if (!isset($_GET['id_cliente'])) {
    die("Cliente no especificado.");
}
$id_cliente = intval($_GET['id_cliente']);

$stmt = $pdo->prepare("SELECT * FROM cliente WHERE id_cliente = ?");
$stmt->execute([$id_cliente]);
$cliente = $stmt->fetch();

if (!$cliente) {
    die("Cliente no encontrado.");
}

$proyectos = $pdo->prepare("SELECT np.*, c.cliente,
           (SELECT costo FROM historial_costo_proyecto WHERE id_nuevo_proyecto = np.id_nuevo_proyecto ORDER BY fecha_modificacion ASC LIMIT 1) AS primer_costo
    FROM nuevo_proyecto np
    INNER JOIN cliente c ON np.id_cliente = c.id_cliente
    WHERE np.id_cliente = ?");
$proyectos->execute([$id_cliente]);
$proyectos = $proyectos->fetchAll();

ob_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte Cliente <?= htmlspecialchars($cliente['cliente']) ?></title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table, th, td { border: 1px solid #656C6EFF; padding: 5px; }
        th { background-color: #55BCD1; color: white; }
        h1, h2, h3 { text-align: center; color: #0A0A0AFF; }
        .right { text-align: right; }
        .final{ text-align: right; color: red; }
        .highlight { font-weight: bold; background-color: #f9f9f9; }
        .ingreso { color: green; font-weight: bold; }
        .egreso { color: red; font-weight: bold; }
        .logo { width: 120px; }
    </style>
</head>
<body>

<img src="http://gastos.conproins.com/assets/logo.jpg" class="logo">

<h1>Reporte Financiero - <?= htmlspecialchars($cliente['cliente']) ?></h1>
<p><strong>Teléfono Cliente:</strong> <?= htmlspecialchars($cliente['telefono_cliente']) ?></p>
<p><strong>Fecha del Reporte:</strong> <?= date('Y-m-d H:i:s') ?></p>

<h2>Sucursales / Proyectos</h2>                                                                                                                                                                                 
<table>
    <thead>
        <tr>
            <th>Proyecto</th>
            <th>Localidad</th>
            <th>Fecha</th>
            <th>Costo Inicial</th>
            <th>Costo Actual</th>
            <th>Diferencia</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($proyectos as $proyecto):
            $costo_actual = $proyecto['costo_inicial'];
            $primer_costo = $proyecto['primer_costo'];
            $diferencia = $costo_actual - $primer_costo;
        ?>
        <tr>
            <td><?= $proyecto['cliente'] ?></td>
            <td><?= htmlspecialchars($proyecto['localidad']) ?></td>
            <td><?= $proyecto['fecha'] ?></td>
            <td class="right">$<?= number_format($primer_costo, 2) ?></td>
            <td class="right">$<?= number_format($costo_actual, 2) ?></td>
            <td class="right">$<?= number_format($diferencia, 2) ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php
$total_general_ingresos = 0;
$total_general_egresos = 0;
?>

<?php foreach ($proyectos as $proyecto): ?>
<h3>Historial de Modificaciones - <?= htmlspecialchars($proyecto['localidad']) ?></h3>
<table>
    <thead>
        <tr>
            <th>Fecha Modificación</th>
            <th>Nuevo Costo</th>
            <th>Diferencia</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $stmtHistorial = $pdo->prepare("SELECT * FROM historial_costo_proyecto WHERE id_nuevo_proyecto = :id ORDER BY fecha_modificacion ASC");
        $stmtHistorial->execute([':id' => $proyecto['id_nuevo_proyecto']]);
        $historial = $stmtHistorial->fetchAll();
        ?>
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

<h3>Gastos e Ingresos - <?= htmlspecialchars($proyecto['localidad']) ?></h3>
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
        <?php
        $stmtGastos = $pdo->prepare("SELECT * FROM gasto WHERE id_nuevo_proyecto = :id ORDER BY fecha ASC");
        $stmtGastos->execute([':id' => $proyecto['id_nuevo_proyecto']]);
        $gastos = $stmtGastos->fetchAll();

        $ingresos = 0;
        $egresos = 0;

        foreach ($gastos as $gasto):
            if ($gasto['tipo_gasto'] === 'ingreso') {
                $ingresos += $gasto['monto'];
            } else {
                $egresos += $gasto['monto'];
            }
        ?>
        <tr>
            <td><?= $gasto['fecha'] ?></td>
            <td><?= ucfirst($gasto['tipo_gasto']) ?></td>
            <td><?= htmlspecialchars($gasto['gasto']) ?></td>
            <td class="right">$<?= number_format($gasto['monto'], 2) ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<p><strong>Total Ingresos:</strong> <span class="ingreso">$<?= number_format($ingresos, 2) ?></span></p>
<p><strong>Total Egresos:</strong> <span class="egreso">$<?= number_format($egresos, 2) ?></span></p>
<hr>
<?php
$total_general_ingresos += $ingresos;
$total_general_egresos += $egresos;
endforeach; ?>

<h2>Resumen Financiero General</h2>
<table>
    <tr>
        <td class="highlight">Total Ingresos</td>
        <td class="right ingreso">$<?= number_format($total_general_ingresos, 2) ?></td>
    </tr>
    <tr>
        <td class="highlight">Total Egresos</td>
        <td class="right egreso">$<?= number_format($total_general_egresos, 2) ?></td>
    </tr>
    <tr>
        <td class="highlight">Balance Final</td>
        <td class="final">$<?= number_format($total_general_ingresos - $total_general_egresos, 2) ?></td>
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
$dompdf->stream("Reporte_Cliente_{$cliente['cliente']}.pdf", ["Attachment" => false]);
?>
