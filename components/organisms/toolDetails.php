<?php
function toolDetails($herramienta)
{
?>
    <div class="row">

        <?php if ($herramienta->getImgRuta()): ?>
            <div class="col-md-6 col-sm-12 mb-3">
                <img src="../<?= $herramienta->getImgRuta(); ?>"
                    class="img-fluid rounded" alt="Producto 1">
            </div>
        <?php endif ?>

        <div class="<?= ($herramienta->getImgRuta()) ? 'col-md-6 col-sm-12' : 'col-12' ?>">
            <h4 class="mb-3"><?= $herramienta->getDescripcion(); ?></h4>

            <ul class="list-group list-group-flush mb-4">
                <li class="list-group-item">
                    <strong>Departamento:</strong> <?= $herramienta->getDepartamento()->getNombre(); ?>
                </li>
                <li class="list-group-item">
                    <strong>Precio:</strong> <span
                        class="price-format"><?= $herramienta->getMarca()->getNombre(); ?></span>
                </li>
                <li class="list-group-item">
                    <strong>ID:</strong> <?= $herramienta->getId(); ?>
                </li>
            </ul>
        </div>
    </div>

<?php
}
