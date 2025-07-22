<?php
require_once "../components/templates/template.php";
require_once "../models/Cotizacion.php";

$cotizacion = Cotizacion::getById($_GET['id']);
$productos = $cotizacion->getProductos();

session_start();
RoleHandler::checkSession();


startTemplate("Cotizacion");

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
<section class="container" id="cotizacion">
    <div class="row m-4">
        <div class="row">
            <div class="col-11">
                <p class="text-center bg-secondary-subtle">IMPORTACIONES ALSO</p>
            </div>
            <div class="col-1 text-end">
                <img src="../images/logo-also.png" alt="logo" height="25" style="max-height: 25px;">
            </div>
        </div>

        <p class="text-end">Folio: <?= $cotizacion->getId() ?></p>
        <p class="text-end">Fecha: <?= $cotizacion->getFecha() ?></p>

        <p>Atención: <?= $_SESSION['usuario']?></p> <!-- Atención: Aqui se inserta el nombre de quien genero la cotizacion, obtener su nombre de la session-->
        <p>Cliente: <?= $cotizacion->getCliente()->getCliente() ?></p> <!-- Aqui se inserta el nombre del cliente, obtener su nombre de la base de datos-->
        <p>Proyecto: <?= $cotizacion->getNombreProyecto() ?></p>
        <table class="table border border-dark" id="pricesTable">
            <thead>
                <tr>
                    <td scope="col" class="border border-dark">Descripción</td>
                    <td scope="col" class="border border-dark">Unidad</td>
                    <td scope="col" class="border border-dark">Cantidad</td>
                    <td scope="col" class="border border-dark">Precio</td>
                    <td scope="col" class="border border-dark">Total</td>
                </tr>
            </thead>
            <tbody>
                <?php
                $productosPorCategoria = [];
                foreach ($productos as $producto) {
                    $categoria = $producto['producto']->getCategoria()->getNombre();
                    if (!isset($productosPorCategoria[$categoria])) {
                        $productosPorCategoria[$categoria] = [];
                    }
                    $productosPorCategoria[$categoria][] = $producto;
                }

                foreach ($productosPorCategoria as $categoria => $productosCategoria):
                    $totalCategoria = 0;
                    foreach ($productosCategoria as $producto) {
                        $totalCategoria += $producto['producto']->getPrecio() * $producto['cantidad'];
                    }
                ?>
                    <tr>
                        <td colspan="4" class="table-primary border border-dark text-uppercase">
                            <?= $categoria ?>
                        </td>
                        <td class="table-primary border border-dark price-format">
                            <?= $totalCategoria ?>
                        </td>
                    </tr>
                    <?php foreach ($productosCategoria as $producto): ?>
                        <tr class="border border-dark">
                            <td class="border border-dark"><?= $producto['producto']->getDescripcion() ?></td>
                            <td class="border border-dark"><?= $producto['producto']->getUnidadMedida()->getNombre() ?></td>
                            <td class="border border-dark"><?= $producto['cantidad'] ?></td>
                            <td class="border border-dark price-format"><?= $producto['producto']->getPrecio() ?></td>
                            <td class="border border-dark price-format"><?= $producto['producto']->getPrecio() * $producto['cantidad'] ?></td>
                        </tr>
                <?php endforeach;
                endforeach; ?>
                <tr>
                    <td colspan="3" class="border-0"></td>
                    <td class="border border-dark ">Subtotal</td>
                    <td class="border border-dark price-format"><?= $cotizacion->getTotal() ?></td>
                </tr>
                <tr>
                    <td colspan="3" class="border-0"></td>
                    <td class="border border-dark">IVA</td>
                    <td class="border border-dark price-format"><?= $cotizacion->getTotal() * 0.16 ?></td>
                </tr>
                <tr>
                    <td colspan="3" class="border-0"></td>
                    <td class="border border-dark">Total</td>
                    <td class="border border-dark price-format"><?= $cotizacion->getTotal() + $cotizacion->getTotal() * 0.16 ?></td>
                </tr>
            </tbody>

        </table>

        <div class="text-justify mb-3">
            <!-- Añadir un for each en caso de tener mas notas  -->
            <?php foreach ($cotizacion->getNotas() as $nota): ?>
                <p><?= $nota->getNota() ?></p>
            <?php endforeach; ?>
            <p>En caso de aceptar las condiciones tecnico economicas, favor de regresar autorizada esta cotizacion acompañada de su orden de compra y comprobante de pago de anticipo por email.</p>
            <p>Sin mas por el momento, quedo a sus ordenes para cualquier explicación o aclaración en espera de colaborar con el presente proyecto.</p>
        </div>
        <div class="mb-5">
            <p class="text-uppercase text-center">Atentamente:</p>
        </div>
        <p class="text-uppercase text-center">____________________________________________</p>
        <p class="text-center"> <b>Ing. Servando Flores Martinez</b> </p>
        <p class="text-center">Coordinador de Inst. Electromecanicas Especiales</p>


    </div>

</section>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>


<script>
    function formatPrices() {
        document.querySelectorAll('.price-format').forEach(element => {
            const price = parseInt(element.textContent);
            element.textContent = new Intl.NumberFormat('es-MX', {
                style: 'currency',
                currency: 'MXN',
                minimumFractionDigits: 2
            }).format(price / 1);
        });
    }

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
            doc.text(`Folio: ${<?= $cotizacion->getId() ?>}`, 195, 25, {
                align: "right"
            });
            doc.text(`Fecha: ${<?= json_encode($cotizacion->getFecha()) ?>}`, 195, 30, {
                align: "right"
            });

            // Datos del cliente
            doc.text("Atención:", 15, 35);
            doc.text("Cliente: <?= $cotizacion->getCliente()->getCliente() ?>", 15, 45);
            doc.text(`Proyecto: ${<?= json_encode($cotizacion->getNombreProyecto()) ?>}`, 15, 55);

            // Tabla de productos
            const tableColumn = ["Descripción", "Unidad", "Cantidad", "Precio", "Total"];
            const tableRows = [];

            <?php foreach ($productosPorCategoria as $categoria => $productosCategoria):
                $totalCategoria = 0;
                foreach ($productosCategoria as $producto) {
                    $totalCategoria += $producto['producto']->getPrecio() * $producto['cantidad'];
                }
            ?>
                tableRows.push([{
                    content: "<?= $categoria ?>",
                    colSpan: 4,
                    styles: {
                        fillColor: [200, 200, 200]
                    }
                }, {
                    content: "$<?= number_format($totalCategoria, 2) ?>",
                    styles: {
                        halign: 'right'
                    }
                }]);

                <?php foreach ($productosCategoria as $producto): ?>
                    tableRows.push([
                        <?= json_encode($producto['producto']->getDescripcion()) ?>,
                        <?= json_encode($producto['producto']->getUnidadMedida()->getNombre()) ?>,
                        <?= json_encode($producto['cantidad']) ?>,
                        "$" + <?= json_encode(number_format($producto['producto']->getPrecio(), 2)) ?>,
                        "$" + <?= json_encode(number_format($producto['producto']->getPrecio() * $producto['cantidad'], 2)) ?>
                    ]);
                <?php endforeach; ?>
            <?php endforeach; ?>

            // Totales
            const subtotal = <?= $cotizacion->getTotal() ?>;
            const iva = subtotal * 0.16;
            const total = subtotal + iva;

            tableRows.push(
                [{
                    content: "",
                    border: false
                }, {
                    content: "",
                    border: false
                }, {
                    content: "",
                    border: false
                }, {
                    content: "IVA",
                }, {
                    content: "$" + iva.toFixed(2),
                    styles: {
                        halign: 'right'
                    }
                }],
                [{
                    content: "",
                    border: false
                }, {
                    content: "",
                    border: false
                }, {
                    content: "",
                    border: false
                }, {
                    content: "Total",
                }, {
                    content: "$" + total.toFixed(2),
                    styles: {
                        halign: 'right'
                    }
                }],
                [{
                    content: "",
                    border: false
                }, {
                    content: "",
                    border: false
                }, {
                    content: "",
                    border: false
                }, {
                    content: "Subtotal",
                }, {
                    content: "$" + subtotal.toFixed(2),
                    styles: {
                        halign: 'right'
                    }
                }]
            );

            doc.autoTable({
                head: [tableColumn],
                body: tableRows,
                startY: 60,
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

            // Notas y firma
            const finalY = doc.lastAutoTable.finalY + 10;

            // Verificar si hay espacio suficiente para las notas y firma
            if (finalY > 250) {
                doc.addPage();
                doc.setFontSize(10);
            } else {
                doc.setFontSize(10);

            }


            var yPosition = finalY; // Posición inicial después de la tabla

            const notas = <?= json_encode(array_map(function ($nota) {
                                return $nota->getNota();
                            }, $cotizacion->getNotas())) ?>;

            notas.forEach(nota => {
                doc.text(nota, 15, yPosition, {
                    maxWidth: 175
                });
                yPosition = yPosition + 10; // Incrementar la posición para el siguiente párrafo
                console.log(yPosition);
            });

            doc.text("En caso de aceptar las condiciones tecnico economicas, favor de regresar autorizada esta cotizacion acompañada de su orden de compra y comprobante de pago de anticipo por email.", 15, yPosition, {
                maxWidth: 175
            });

            doc.text("Sin mas por el momento, quedo a sus ordenes para cualquier explicación o aclaración en espera de colaborar con el presente proyecto.", 15, yPosition + 10, {
                maxWidth: 175
            });

            // Firma
            doc.text("ATENTAMENTE:", 105, yPosition + 30, {
                align: "center"
            });
            doc.text("____________________________________________", 105, yPosition + 50, {
                align: "center"
            });
            doc.text("Ing. Gerardo Moreno Borja", 105, yPosition + 55, {
                align: "center"
            });

            // Guardar el PDF
            doc.save(`cotizacion_${<?= $cotizacion->getId() ?>}.pdf`);
        };
    }

    document.addEventListener('DOMContentLoaded', function() {
        formatPrices();
    });
</script>


<?php
endTemplate();
