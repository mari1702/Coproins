<?php
function productDetails($producto)
{
?>
    <div class="row">

        <?php if ($producto->getImgRuta()): ?>
            <div class="col-4 mb-3">
                <img src="../<?= $producto->getImgRuta(); ?>"
                    class="img-fluid rounded" alt="imagen">
            </div>
        <?php endif ?>

        <div class="<?= ($producto->getImgRuta()) ? 'col-8' : 'col-12' ?>">
            <h4 class="mb-3"><?= $producto->getDescripcion(); ?></h4>

            <ul class="list-group list-group-flush mb-4">
                <li class="list-group-item">
                    <strong>Precio:</strong> 
                    <span class="price-format">
                        <?= $producto->getPrecio(); ?>
                    </span>
                </li>
            </ul>

            <span class="badge bg-primary" id="id"><strong><?= $producto->getId() ?></strong></span>
            <span class="badge bg-secondary" id="department-name"><?= $producto->getCategoria()->getNombre() ?></span>
            <span class="badge bg-success" id="brand-name"><?= $producto->getUnidadMedida()->getNombre() ?></span>
        </div>
    </div>
<?php
}
