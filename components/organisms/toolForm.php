<?php
function toolForm($action, $brands, $departamentos, $herramienta = null)
{
    $id = $herramienta ? $herramienta->getId() : '';
    $descripcion = $herramienta ? $herramienta->getDescripcion() : '';
    $imgRuta = $herramienta ? $herramienta->getImgRuta() : '';
    $marcaId = $herramienta && $herramienta->getMarca() ? $herramienta->getMarca()->getId() : null;
    $departamentoId = $herramienta && $herramienta->getDepartamento() ? $herramienta->getDepartamento()->getId() : null;
?>
    <form method="POST" action="../actions/<?= $action ?>" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">

        <div class="form-group mb-3">
            <label for="description"><b>Descripción</b></label>
            <textarea class="form-control" id="description" name="description" rows="3"
                placeholder="Ingrese la descripción del producto" required><?= htmlspecialchars($descripcion) ?></textarea>
        </div>

        <div class="form-group mb-3">
            <label for="brand"><b>Marca</b></label>
            <div class="row">
                <div class="col-8">
                    <select id="brand" name="brand" class="form-control" required>
                        <option disabled selected value="">Seleccione una marca</option>
                        <?php foreach ($brands as $brand): ?>
                            <option value="<?= $brand->getId(); ?>"
                                <?= ($brand->getId() === $marcaId) ? 'selected' : ''; ?>>
                                <?= $brand->getNombre(); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-4 col-lg-2 align-content-center">
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                        data-bs-target="#new_marca">
                        <i class="fa fa-plus"></i>
                    </button>

                    <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal"
                        data-bs-target="#show_marca">
                        <i class="fa fa-eye"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="form-group mb-3">
            <label for="department"><b>Departamento</b></label>
            <div class="row">
                <div class="col-8">
                    <select id="department" name="department" class="form-control" required>
                        <option disabled selected value="">Seleccione un departamento</option>
                        <?php foreach ($departamentos as $departamento): ?>
                            <option value="<?= $departamento->getId(); ?>"
                                <?= ($departamento->getId() === $departamentoId) ? 'selected' : ''; ?>>
                                <?= $departamento->getNombre(); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-4 col-lg-2 align-content-center">
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                        data-bs-target="#new_departamento">
                        <i class="fa fa-plus"></i>
                    </button>

                    <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal"
                        data-bs-target="#show_departamento">
                        <i class="fa fa-eye"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="form-group mb-3">
            <label for="image"><b>Imagen</b></label>
            <input type="file" class="form-control-file" id="image" name="image" accept="image/*">
            <?php if ($imgRuta): ?>
                <div class="mt-2">
                    <img src="../<?= htmlspecialchars($imgRuta) ?>" alt="Imagen actual" style="max-height: 150px;">
                </div>
            <?php endif; ?>
        </div>

        <button type="submit" class="btn btn-primary col-12">Guardar</button>
    </form>
<?php
}
