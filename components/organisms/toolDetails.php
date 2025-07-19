<?php
function toolDetails($herramienta)
{
?>
    <div class="row">

        <?php if ($herramienta->getImgRuta()): ?>
            <div class="col-4 mb-3">
                <img src="../<?= $herramienta->getImgRuta(); ?>"
                    class="img-fluid rounded" alt="Producto 1">
            </div>
        <?php endif ?>

        <div class="<?= ($herramienta->getImgRuta()) ? 'col-8' : 'col-12' ?>">
            <h4 class="mb-3"><?= $herramienta->getDescripcion(); ?></h4>

            <span class="badge bg-primary" id="id"><strong><?= $herramienta->getId() ?></strong></span>
            <span class="badge bg-secondary" id="department-name"><?= $herramienta->getDepartamento()->getNombre() ?></span>
            <span class="badge bg-success" id="brand-name"><?= $herramienta->getMarca()->getNombre() ?></span>
        </div>
    </div>

<?php
}
