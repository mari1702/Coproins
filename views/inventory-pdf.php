<?php
require_once "../components/templates/template.php";
require_once "../models/Inventario.php";

$inventario = Inventario::getById($_GET['id']);
$herramientas = $inventario->getHerramientas();

startTemplate("Inventario");
?>

<section class="container-fluid">
    <div class="row m-4">
        <div class="d-flex justify-content-end mb-3">
            <button class="btn btn-primary" onclick="downloadPDF()">
                <i class="fas fa-download"></i> Descargar PDF
            </button>
        </div>
    </div>

</section>
<section class="container" id="inventario">
    <div class="row m-4">
        <div class="row">
            <div class="col-11">
                <p class="text-center bg-secondary-subtle">IMPORTACIONES ALSO</p>
            </div>
            <div class="col-1 text-end">
                <img src="../images/logo-also.png" alt="logo" height="25" style="max-height: 25px;">
            </div>
        </div>

        <p class="text-end">Folio: <?= $inventario->getId() ?></p>
        <p class="text-end">Fecha: <?= $inventario->getFecha() ?></p>

        <p>Ubicación: <?= $inventario->getUbicacion() ?></p>
        <table class="table border border-dark" id="pricesTable">
            <thead>
                <tr>
                    <td scope="col" class="border border-dark">Descripción</td>
                    <td scope="col" class="border border-dark">Marca</td>
                    <td scope="col" class="border border-dark">Cantidad</td>
                </tr>
            </thead>
            <tbody>
                <?php
                $herramientasPorDepartamento = [];
                foreach ($herramientas as $herramienta) {
                    $departamento = $herramienta['herramienta']->getDepartamento()->getNombre();
                    if (!isset($herramientasPorDepartamento[$departamento])) {
                        $herramientasPorDepartamento[$departamento] = [];
                    }
                    $herramientasPorDepartamento[$departamento][] = $herramienta;
                }

                foreach ($herramientasPorDepartamento as $departamento => $herramientasDepartamento):
                ?>
                    <tr>
                        <td colspan="4" class="table-primary border border-dark text-uppercase">
                            <?= $departamento ?>
                        </td>
                    </tr>
                    <?php foreach ($herramientasDepartamento as $herramienta): ?>
                        <tr class="border border-dark">
                            <td class="border border-dark"><?= $herramienta['herramienta']->getDescripcion() ?></td>
                            <td class="border border-dark"><?= $herramienta['herramienta']->getMarca()->getNombre() ?></td>
                            <td class="border border-dark"><?= $herramienta['cantidad'] ?></td>
                        </tr>
                <?php endforeach;
                endforeach; ?>
            </tbody>

        </table>


        <div class="mb-5">
            <p class="text-uppercase text-center">Atentamente:</p>
        </div>
        <p class="text-uppercase text-center">____________________________________________</p>
        <p class="text-center"> <b>Ing. Gerardo Moreno Borja</b> </p>


    </div>

</section>




<script>
    function downloadPDF() {
        const {
            jsPDF
        } = window.jspdf;
        const doc = new jsPDF();

        // Configuración inicial
        doc.setFont("helvetica");
        doc.setFontSize(12);

        // Agregar logo
        const img = new Image();
        img.src = '../images/logo-also.png';

        // Esperar a que la imagen se cargue
        img.onload = function() {

            // Información de la cotización
            doc.setFontSize(10);
            doc.text(`Folio: ${<?= $inventario->getId() ?>}`, 195, 25, {
                align: "right"
            });
            doc.text(`Fecha: ${<?= json_encode($inventario->getFecha()) ?>}`, 195, 30, {
                align: "right"
            });

            // Datos de la ubicacion
            doc.text(`Ubicación: ${<?= json_encode($inventario->getUbicacion()) ?>}`, 15, 35);

            // Tabla de herramientas
            const tableColumn = ["Descripción", "Marca", "Cantidad"];
            const tableRows = [];

            <?php foreach ($herramientasPorDepartamento as $departamento => $herramientasDepartamento): ?>
                tableRows.push([{
                    content: "<?= $departamento ?>",
                    colSpan: 4,
                    styles: {
                        fillColor: [200, 200, 200]
                    }
                }]);

                <?php foreach ($herramientasDepartamento as $herramienta): ?>
                    tableRows.push([
                        <?= json_encode($herramienta['herramienta']->getDescripcion()) ?>,
                        <?= json_encode($herramienta['herramienta']->getMarca()->getNombre()) ?>,
                        <?= json_encode($herramienta['cantidad']) ?>,
                    ]);
                <?php endforeach; ?>
            <?php endforeach; ?>


            doc.autoTable({
                head: [tableColumn],
                body: tableRows,
                startY: 40,
                theme: 'grid',
                styles: {
                    fontSize: 10,
                    cellPadding: 3
                },
                headStyles: {
                    fillColor: [85, 188, 209],
                    textColor: 255
                },
                pageBreak: 'auto',
                margin: {
                    top: 60
                },
                didDrawPage: function(data) {
                    // Agregar encabezado y logo en cada página
                    doc.setFontSize(16);
                    doc.text("IMPORTACIONES ALSO", 105, 15, {
                        align: "center"
                    });
                    doc.addImage(img, 'PNG', 160, 10, 40, 10);

                    // Agregar pie de página
                    doc.setFontSize(10);
                    doc.text("IMPORTACIONES ALSO", 10, 285);
                    doc.text("Cel: 55 45 57 88 96", 150, 285);
                    doc.text("Mail: proyectos@imp-also.com", 150, 290);
                }
            });

            // firma
            const finalY = doc.lastAutoTable.finalY + 10;

            // Verificar si hay espacio suficiente para las notas y firma
            if (finalY > 250) {
                doc.addPage();
                yPosition = 20;
            }


            var yPosition = finalY; // Posición inicial después de la tabla

            // Firma
            doc.text("ATENTAMENTE:", 105, yPosition + 10, {
                align: "center"
            });
            doc.text("____________________________________________", 105, yPosition + 30, {
                align: "center"
            });
            doc.text("Ing. Gerardo Moreno Borja", 105, yPosition + 35, {
                align: "center"
            });

            // Guardar el PDF
            doc.save(`inventario_${<?= $inventario->getId() ?>}.pdf`);
        };
    }

    document.addEventListener('DOMContentLoaded', function() {});
</script>

<?php
endTemplate();
