<?php
require_once "../components/templates/template.php";
include "../components/navbar.php";
include "../components/modalNewObject.php";


require_once '../models/Herramienta.php';
require_once "../controllers/DepartamentoController.php";
require_once "../controllers/MarcaController.php";




$departamentos = DepartamentoController::listar();
$brands = MarcaController::listar();

$herramienta = new Herramienta()->getById($_GET['id']);

startTemplate("Editar Herramienta");
?>

<header>
    <!-- Navbar -->
    <?php
    navBar('herramientas');
    ?>
</header>

<section class="container-fluid">


    <div class=" new row justify-content-center">
        <div class=" mb-3">
            <div class="input-group">
                <a class="btn btn-primary" href="tools.php" id="showForm"><i class="fas fa-arrow-left"> </i> Regresar</a>
            </div>
        </div>
        <div class="card col-sm-11 col-md-7 col-lg-4 shadow p-3 mb-5 bg-white rounded">
            <div class="card-body">

                <h2 class="text-center">Editar herramienta</h2>
                <form method="POST" action="../actions/herramienta_editar.php" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?= $herramienta->getId(); ?>">

                    <div class="form-group mb-3">
                        <label for="description"><b>Descripción</b></label>
                        <textarea class="form-control" id="description" name="description" rows="3"
                            placeholder="Ingrese la descripción del producto" required><?= $herramienta->getDescripcion(); ?></textarea>
                    </div>
                    <div class="form-group mb-3">
                        <label for="brand"><b>Marca</b></label>
                        <div class="row">
                            <div class="col-10">
                                <select id="brand" name="brand" class="form-control" required>
                                    <?php foreach ($brands as $brand): ?>
                                        <option <?= ($brand->getId() === $herramienta->getMarca()->getId()) ? 'selected' : ''; ?>
                                            value="<?= $brand->getId(); ?>">
                                            <?= $brand->getNombre(); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-2">
                                <button type="button" class="btn btn-primary " data-bs-toggle="modal"
                                    data-bs-target="#new_marca">
                                    <i class="fa fa-plus"></i>
                                </button>
                            </div>
                        </div>

                    </div>
                    <div class="form-group mb-3">
                        <label for="department"><b>Departamento</b></label>

                        <div class="row">
                            <div class="col-10">
                                <select id="department" name="department" class="form-control" required>
                                    <?php foreach ($departamentos as $departamento): ?>
                                        <option <?= ($departamento->getId() === $herramienta->getDepartamento()->getId()) ? 'selected' : ''; ?>
                                            value="<?= $departamento->getId(); ?>">
                                            <?= $departamento->getNombre(); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-2">
                                <button type="button" class="btn btn-primary " data-bs-toggle="modal"
                                    data-bs-target="#new_departamento">
                                    <i class="fa fa-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <label for="image"><b>Imagen</b></label>
                        <input type="file" class="form-control-file" id="image" name="image" accept="image/*" value="../<?= $herramienta->getImgRuta(); ?>">
                    </div>
                    <button type="submit" class="btn btn-primary col-12">Guardar</button>
                </form>
            </div>
        </div>
    </div>
</section>

<section>
    <?php
    modalNewObject('departamento');
    modalNewObject('marca');
    ?>
</section>

<?php
endTemplate();
