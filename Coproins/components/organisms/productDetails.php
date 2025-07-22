<?php
function productDetails($producto)
{
?>
    <div class="row">

        <?php if ($producto->getImgRuta()): ?>
            <div class="col-4 mb-3" >
                <div style="position: relative; width: 100%; padding-bottom: 100%;">
                    <img src="../<?= $producto->getImgRuta(); ?>" class="img-fluid rounded position-absolute top-0 start-0 w-100 h-100" style="object-fit: cover;" alt="imagen">
                </div>
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
