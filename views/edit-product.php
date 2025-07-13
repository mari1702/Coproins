<?php
require_once '../models/Producto.php';
require_once "../controllers/CategoriaController.php";
require_once "../controllers/UnidadMedidaController.php";




$categorias = CategoriaController::listar();
$unidades = UnidadMedidaController::listar();

$producto = new Producto()->getById($_GET['id']);
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


    <title>Editar producto</title>

</head>
<body>

    <header>
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg shadow p-3 mb-3" style="background-color: #55BCD1;">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">
                    <img src="../images/logo.png" alt="Logo" width="25" height="25" />
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false"
                    aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse justify-content-center" id="navbarNavDropdown">
                    <ul class="navbar-nav ">
                        <li class="nav-item">
                            <a class="nav-link" aria-current="page" href="#">Inicio</a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link" href="products.php">Productos</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="prices.php">Cotizaciones</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="tools.php">Herramientas</a>
                        </li>
                        <li>
                            <a href="../bd/logout.php" class="nav-link logout-icon">
                                <i class="fas fa-sign-out-alt"></i></a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
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
                    <a class="btn btn-primary" href="products.php" id="showForm"><i class="fas fa-arrow-left"> </i> Regresar</a>
                </div>
            </div>
            <div class="card col-sm-11 col-md-7 col-lg-4 shadow p-3 mb-5 bg-white rounded">
                <div class="card-body">
                    
                    <h2 class="text-center">Editar producto</h2>
                    <form method="POST" action="../actions/producto_editar.php" enctype="multipart/form-data">
                        <input type="hidden" name="id" value="<?= $producto->getId();?>">
                        
                        <div class="form-group mb-3">
                            <label for="description"><b>Descripción</b></label>
                            <textarea class="form-control" id="description" name="description" rows="3"
                                placeholder="Ingrese la descripción del producto"  required><?= $producto->getDescripcion(); ?></textarea>
                        </div>
                        <div class="form-group mb-3">
                            <label for="price"><b>Precio</b></label>
                            <input type="number" class="form-control" id="price" name="price"
                                placeholder="Ingrese el precio del producto" min="1" step="0.01" value="<?= $producto->getPrecio(); ?>" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="measurement"><b>Unidad de medida</b></label>
                            <div class="row">
                                <div class="col-10">
                                    <select id="measurement" name="measurement" class="form-control" required>
                                        <?php foreach($unidades as $unidad): ?>
                                        <option <?=($unidad->getId() === $producto->getUnidadMedida()->getId() ) ? 'selected' : ''; ?>
                                            value="<?= $unidad->getId();?>">
                                            <?= $unidad->getNombre();?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-2">
                                    <button type="button" class="btn btn-primary " data-bs-toggle="modal"
                                        data-bs-target="#NewMeasurement">
                                        <i class="fa fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                            
                        </div>
                        <div class="form-group mb-3">
                            <label for="category"><b>Categoría</b></label>

                            <div class="row">
                                <div class="col-10">
                                    <select id="category" name="category" class="form-control" required>
                                        <?php foreach($categorias as $categoria): ?>
                                        <option <?=($categoria->getId() === $producto->getCategoria()->getId() ) ? 'selected' : ''; ?>
                                            value="<?= $categoria->getId(); ?>">
                                            <?= $categoria->getNombre();?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>    
                                </div>
                                <div class="col-2">
                                    <button type="button" class="btn btn-primary " data-bs-toggle="modal"
                                        data-bs-target="#NewCategory">
                                        <i class="fa fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <label for="image"><b>Imagen</b></label>
                            <input type="file" class="form-control-file" id="image" name="image" accept="image/*" value="../<?= $producto->getImgRuta(); ?>">
                        </div>
                        <button type="submit" class="btn btn-primary col-12">Guardar</button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <section>
        <div class="modal fade" id="NewCategory" tabindex="-1" aria-labelledby="modalNewCategoryForm"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="modalNewCategoryForm">Registrar categoria</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">

                        <form method="POST" action="../actions/categoria_crear.php">
                            <div class="form-group mb-3">
                                <input type="hidden" name="producto_id" value="<?= $producto->getId()?>">
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

        <div class="modal fade" id="NewMeasurement" tabindex="-1" aria-labelledby="modalNewMeasurementForm"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="modalNewMeasurementForm">Registrar unidad de medida</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" action="../actions/um_crear.php">
                            <input type="hidden" name="producto_id" value="<?= $producto->getId()?>">

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