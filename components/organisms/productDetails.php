<?php
function productdetails($producto)
{
?>
    <div class="row">

        <?php if ($producto->getImgRuta()): ?>
            <div class="col-md-6 col-sm-12 mb-3">
                <img src="../<?= $producto->getImgRuta(); ?>"
                    class="img-fluid rounded" alt="Producto 1">
            </div>
        <?php endif ?>

        <div class="<?= ($producto->getImgRuta()) ? 'col-md-6 col-sm-12' : 'col-12' ?>">
            <h4 class="mb-3"><?= $producto->getDescripcion(); ?></h4>

            <ul class="list-group list-group-flush mb-4">
                <li class="list-group-item">
                    <strong>Unidad de medida:</strong> <?= $producto->getUnidadMedida()->getNombre(); ?>
                </li>
                <li class="list-group-item">
                    <strong>Categoría:</strong> <?= $producto->getCategoria()->getNombre(); ?>
                </li>
                <li class="list-group-item">
                    <strong>Precio:</strong> <span
                        class="price-format"><?= $producto->getPrecio(); ?></span>
                </li>
                <li class="list-group-item">
                    <strong>ID:</strong> <?= $producto->getId(); ?>
                </li>
            </ul>
        </div>
    </div>
<?php
}
