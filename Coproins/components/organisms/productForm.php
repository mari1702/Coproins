<?php
function productForm($action, $unidades, $categorias, $producto = null)
{
    $id = $producto ? $producto->getId() : '';
    $descripcion = $producto ? $producto->getDescripcion() : '';
    $precio = $producto ? $producto->getPrecio() : '';
    $imgRuta = $producto ? $producto->getImgRuta() : '';
    $categoriaId = $producto && $producto->getCategoria() ? $producto->getCategoria()->getId() : null;
    $unidadId = $producto && $producto->getUnidadMedida() ? $producto->getUnidadMedida()->getId() : null;
?>
    <form method="POST" action="../actions/<?= htmlspecialchars($action) ?>.php" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">

        <div class="form-group mb-3">
            <label for="description"><b>Descripción</b></label>
            <textarea class="form-control" id="description" name="description" rows="3"
                placeholder="Ingrese la descripción del producto" required><?= htmlspecialchars($descripcion) ?></textarea>
        </div>

        <div class="form-group mb-3">
            <label for="price"><b>Precio</b></label>
            <input type="number" class="form-control" id="price" name="price"
                placeholder="Ingrese el precio del producto" min="1" step="0.01"
                value="<?= htmlspecialchars($precio) ?>" required>
        </div>

        <div class="form-group mb-3">
            <label for="measurement"><b>Unidad de medida</b></label>
            <div class="row">

                <div class="<?= ($action == "producto_crear") ?  "col-8 col-lg-10" : "col-12" ?>">
                    <select id="measurement" name="measurement" class="form-control" required>
                        <option disabled <?= is_null($unidadId) ? 'selected' : '' ?>>Seleccione una unidad</option>
                        <?php foreach ($unidades as $unidad): ?>
                            <option value="<?= $unidad->getId(); ?>"
                                <?= ($unidad->getId() === $unidadId) ? 'selected' : ''; ?>>
                                <?= $unidad->getNombre(); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php if($action == "producto_crear"): ?>
                <div class="col-4 col-lg-2 align-content-center">
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                        data-bs-target="#new_unidad">
                        <i class="fa fa-plus"></i>
                    </button>

                    <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal"
                        data-bs-target="#show_unidad">
                        <i class="fa fa-eye"></i>
                    </button>
                </div>
                <?php endif ?>
            </div>
        </div>

        <div class="form-group mb-3">
            <label for="category"><b>Categoría</b></label>
            <div class="row">
                <div class="<?= ($action == "producto_crear") ?  "col-8 col-lg-10" : "col-12" ?>">
                    <select id="category" name="category" class="form-control" required>
                        <option disabled <?= is_null($categoriaId) ? 'selected' : '' ?>>Seleccione una categoría</option>
                        <?php foreach ($categorias as $categoria): ?>
                            <option value="<?= $categoria->getId(); ?>"
                                <?= ($categoria->getId() === $categoriaId) ? 'selected' : ''; ?>>
                                <?= $categoria->getNombre(); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php if($action == "producto_crear"): ?>
                <div class="col-4 col-lg-2 align-content-center">
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                        data-bs-target="#new_categoria">
                        <i class="fa fa-plus"></i>
                    </button>

                    <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal"
                        data-bs-target="#show_categoria">
                        <i class="fa fa-eye"></i>
                    </button>
                </div>
                <?php endif ?>
            </div>
        </div>

        <div class="form-group mb-3">
            <label for="image"><b>Imagen</b></label>
            <div class="row">
                <?php if ($imgRuta): ?>
                    <div class="col-2">
                        <div style="position: relative; width: 100%; padding-bottom: 100%;">
                            <img src="../<?= htmlspecialchars($imgRuta) ?>" class="img-fluid rounded position-absolute top-0 start-0 w-100 h-100" style="object-fit: cover;" alt="imagen">
                        </div>
                    </div>
                <?php endif; ?>
                <div class="col-10">
                    <input type="file" class="form-control-file" id="image" name="image" accept="image/*">
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary col-12">Guardar</button>
    </form>


<?php
}
