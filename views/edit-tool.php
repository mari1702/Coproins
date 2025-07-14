<?php
include "../components/navbar.php";

require_once '../models/Herramienta.php';
require_once "../controllers/DepartamentoController.php";
require_once "../controllers/MarcaController.php";




$departamentos = DepartamentoController::listar();
$brands = MarcaController::listar();

$herramienta = new Herramienta()->getById($_GET['id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!--  Bootstrap CSS    -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <!--  Navbar CSS    -->
    <link rel="stylesheet" href="../css/navbar.css">
    <!-- Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">


    <title>Editar Herramienta</title>

</head>
<body>

    <header>
        <!-- Navbar -->
        <?php
        navBar('herramientas');
        ?>
    </header>

    <section class="container-fluid">
        <?php if (isset($_GET['status']) && $_GET['status'] === 'success'): ?>
            <div class="alert alert-success" role="alert"> Cambios realizados exitosamente!</div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger" role="alert">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

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
                        <input type="hidden" name="id" value="<?= $herramienta->getId();?>">
                        
                        <div class="form-group mb-3">
                            <label for="description"><b>Descripción</b></label>
                            <textarea class="form-control" id="description" name="description" rows="3"
                                placeholder="Ingrese la descripción del producto"  required><?= $herramienta->getDescripcion(); ?></textarea>
                        </div>
                        <div class="form-group mb-3">
                            <label for="brand"><b>Marca</b></label>
                            <div class="row">
                                <div class="col-10">
                                    <select id="brand" name="brand" class="form-control" required>
                                        <?php foreach($brands as $brand): ?>
                                        <option <?=($brand->getId() === $herramienta->getMarca()->getId() ) ? 'selected' : ''; ?>
                                            value="<?= $brand->getId();?>">
                                            <?= $brand->getNombre();?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-2">
                                    <button type="button" class="btn btn-primary " data-bs-toggle="modal"
                                        data-bs-target="#NewBrand">
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
                                        <?php foreach($departamentos as $departamento): ?>
                                        <option <?=($departamento->getId() === $herramienta->getDepartamento()->getId() ) ? 'selected' : ''; ?>
                                            value="<?= $departamento->getId(); ?>">
                                            <?= $departamento->getNombre();?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>    
                                </div>
                                <div class="col-2">
                                    <button type="button" class="btn btn-primary " data-bs-toggle="modal"
                                        data-bs-target="#NewDepartment">
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
        <div class="modal fade" id="NewDepartment" tabindex="-1" aria-labelledby="modalNewDepartmentForm"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="modalNewDepartmentForm">Registrar departamento</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">

                        <form method="POST" action="../actions/departamento_crear.php">
                            <div class="form-group mb-3">
                                <label for="name"><b>Nombre</b></label>
                                <input type="text" class="form-control" id="name" name="name"
                                    placeholder="Ingrese el nombre de la categoria" required>
                            </div>

                            <button type="submit" class="btn btn-primary col-12">Guardar</button>
                        </form>


                    </div>

                </div>
            </div>
        </div>

        
        <div class="modal fade" id="NewBrand" tabindex="-1" aria-labelledby="modalNewBrandForm"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="modalNewBrandForm">Registrar marca</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" action="../actions/marca_crear.php">
                            <div class="form-group mb-3">
                                <label for="name"><b>Nombre</b></label>
                                <input type="text" class="form-control" id="name" name="name"
                                    placeholder="Ingrese el nombre de la unidad de medida" required>
                            </div>

                            <button type="submit" class="btn btn-primary col-12">Guardar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!--   Bootstrap JS   -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq"
        crossorigin="anonymous"></script>
</body>
</html>