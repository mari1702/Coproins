<?php
require_once "../controllers/CategoriaController.php";
require_once "../controllers/UnidadMedidaController.php";
require_once "../controllers/ProductoController.php";




$categorias = CategoriaController::listar();
$unidades = UnidadMedidaController::listar();
$productos = ProductoController::listar();

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

    <title>Productos</title>
</head>

<body>

    <header>
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg shadow p-3 mb-3 " style="background-color: #55BCD1;">
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
                            <a class="nav-link active" href="products.php">Productos</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="prices.php">Cotizaciones</a>
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

        <?php if (isset($_GET['status']) && $_GET['status'] === 'error'): ?>
            <div class="alert alert-danger" role="alert">
                <?= htmlspecialchars($_GET['message']) ?>
            </div>
        <?php endif; ?>

        <div class=" new row justify-content-center  d-none">
            <div class="card col-sm-11 col-md-7 col-lg-6 shadow p-3 mb-5 bg-white border-0 rounded">
                <div class="card-body">
                    
                    <h2 class="text-center">Registrar producto</h2>
                    <form method="POST" action="../actions/producto_crear.php" enctype="multipart/form-data">

                        <div class="form-group mb-3">
                            <label for="category"><b>Categoría</b></label>

                            <div class="row">
                                <div class="col-8 col-lg-10">
                                    <select id="category" name="category" class="form-control" required>
                                        <option disabled selected="">Seleccione categoria</option>
                                        <?php
                                            foreach ($categorias as $categoria) {
                                                echo "
                                                    <option value='". htmlspecialchars($categoria->getId()) ."'>". htmlspecialchars($categoria->getNombre())  ."</option>
                                                ";  
                                            }
                                        ?>
                                    </select>
                                </div>

                                <div class="col-4 col-lg-2 align-content-center">
                                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#NewCategory">
                                        <i class="fa fa-plus"></i>
                                    </button>

                                    <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#ShowCategories">
                                        <i class="fa fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="measurement"><b>Unidad de medida</b></label>
                            <div class="row">
                                <div class="col-8 col-lg-10">
                                    <select id="measurement" name="measurement" class="form-control" required>
                                        <option disabled selected="">Seleccione unidad de medida</option>
                                        <?php
                                            foreach ($unidades as $unidad) {
                                                echo "
                                                    <option value='". htmlspecialchars($unidad->getId()) ."'>". htmlspecialchars($unidad->getNombre())  ."</option>
                                                ";  
                                            }
                                        ?>
                                    </select>
                                </div>

                                <div class="col-4 col-lg-2 align-content-center">
                                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#NewMeasurement">
                                        <i class="fa fa-plus"></i>
                                    </button>

                                    <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#ShowMeasurements">
                                        <i class="fa fa-eye"></i>
                                    </button>
                                </div>     
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="description"><b>Descripción</b></label>
                            <textarea class="form-control" id="description" name="description" rows="3"
                                placeholder="Ingrese la descripción del producto" required></textarea>
                        </div>
                        <div class="form-group mb-3">
                            <label for="price"><b>Precio</b></label>
                            <input type="number" class="form-control" id="price" name="price"
                                placeholder="Ingrese el precio del producto" min="0" step="0.01" required>
                        </div>


                        <div class="form-group mb-3">
                            <label for="image"><b>Imagen</b></label>
                            <input type="file" class="form-control-file" id="image" name="image" accept="image/*">
                        </div>
                        <button type="submit" class="btn btn-primary col-12">Guardar</button>
                    </form>
                </div>
            </div>
        </div>

    </section>


    <section class="container-fluid">
        <div class="row mb-4">
            <div class="card col-12 shadow mb-5 rounded border-0">
                <div class="card-body">

                    <!-- Filtros y búsqueda -->
                    <div class="row mb-4">

                        <div class="col-lg-2 col-sm-3 mb-3">
                            <div class="input-group">
                                <button class="btn btn-primary" id="showForm"><i class="fas fa-plus"> </i>
                                    Nuevo</button>
                            </div>
                        </div>


                        <div class="col-lg-4 col-sm-9 mb-3">
                            <div class="input-group">
                                <span class="input-group-text bg-primary text-white">
                                    <i class="fas fa-filter"></i>
                                </span>
                                <select class="form-select" id="categoryFilter">
                                    <option value="">Todas las categorías</option>
                                        <?php
                                            foreach ($categorias as $categoria) {
                                                echo "
                                                    <option value='". htmlspecialchars($categoria->getId()) ."'>". htmlspecialchars($categoria->getNombre())  ."</option>
                                                ";  
                                            }
                                        ?>
                                   
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-6 col-sm-12 mb-3">
                            <div class="input-group">
                                <span class="input-group-text bg-primary text-white">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text" id="searchInput" class="form-control"
                                    placeholder="Buscar productos...">
                                <button class="btn btn-outline-secondary" type="button" id="clearSearch">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <table class="table" id="productsTable">
                        <thead class="table-primary">
                            <tr>
                                <th scope="col">ID</th>
                                <th scope='col'>Descripción</th>
                                <th scope="col">Precio</th>
                                <th scope="col" class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>


                            <?php foreach($productos as $producto): ?>

                            <tr data-category="<?= $producto->getCategoria()->getId(); ?>">
                                <th scope="row"><?= $producto->getId(); ?></th>
                                <td><?= $producto->getDescripcion(); ?></td>
                                <td class="price-format"><?= $producto->getPrecio(); ?></td>
                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        <!-- Ver -->
                                        <button class="btn btn-info btn-sm" type="button" data-bs-toggle="modal"
                                            data-bs-target="#<?= $producto->getId(); ?>">
                                            <i class="fas fa-eye"></i>
                                        </button>

                                        <div class="modal fade" id="<?= $producto->getId(); ?>" tabindex="-1"
                                            aria-labelledby="modal<?= $producto->getId(); ?>Label" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-primary text-white">
                                                        <h5 class="modal-title" id="modal<?= $producto->getId(); ?>Label">Detalles del
                                                            Producto</h5>
                                                        <button type="button" class="btn-close btn-close-white"
                                                            data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row">

                                                            <?php if ($producto->getImgRuta()): ?>
                                                            <div class="col-md-6 col-sm-12 mb-3">
                                                                <img src="../<?= $producto->getImgRuta(); ?>"
                                                                    class="img-fluid rounded" alt="Producto 1">
                                                            </div>
                                                            <?php endif?>

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
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">Cerrar</button>
                                                        <a href="edit-product.php?id=<?= $producto->getId(); ?>" class="btn btn-primary">Editar
                                                            Producto</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                        <!-- Editar -->
                                        <a href="edit-product.php?id=<?= $producto->getId(); ?>" class="btn btn-primary btn-sm" title="Editar"
                                            aria-label="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <!-- Eliminar -->
                                        <form method="POST" action="../actions/producto_borrar.php" class="d-inline">
                                            <input type="hidden" name="id" value="<?= $producto->getId(); ?>">
                                            <button type="button" class="btn btn-danger btn-sm" title="Eliminar"
                                                aria-label="Eliminar" onclick="confirmarEliminacion(this)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach ?>

                        </tbody>
                        <div id="noResults" class="text-center py-4 d-none">
                            <i class="fas fa-search fa-3x mb-3 text-muted"></i>
                            <h5 class="text-muted">No se encontraron productos</h5>
                            <p class="text-muted">Intenta con otros términos de búsqueda</p>
                        </div>
                    </table>
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

    <section>
        <div class="modal fade" id="ShowMeasurements" tabindex="-1" aria-labelledby="modalShowMeasurements"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="modalShowMeasurements">Unidades de medida</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <table class="table" id="categoriesTable">
                            <thead class="table-primary">
                                <tr>
                                    <th scope="col">ID</th>
                                    <th scope='col'>Nombre</th>
                                    <th scope="col" class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>


                                <?php foreach($unidades as $unidad): ?>

                                <tr>
                                    <th scope="row"><?= $unidad->getId(); ?></th>
                                    <td>
                                        <span id="label-name-<?= $unidad->getId(); ?>"> <?= $unidad->getNombre(); ?></span>
                                        <input type="text" class="form-control d-none" id="input-name-<?= $unidad->getId(); ?>" name="name"
                                            value="<?= $unidad->getNombre(); ?>" required>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center gap-2">

                                            <!-- Editar -->
                                            <button class="btn btn-primary btn-sm" id="<?= $unidad->getId(); ?>" type="button" onclick="toggleEdit(this)">
                                                <i class="fas fa-edit"></i>
                                            </button>

                                            <!-- Guardar -->

                                            <button class="btn btn-success btn-sm d-none" id="<?= $unidad->getId(); ?>"  data-table="unidad" type="button" onclick="saveEdit(this)">
                                                <i class="fas fa-save"></i>
                                            </button>

                                            <!-- Eliminar -->
                                            <form method="POST" action="../actions/unidad_borrar.php" class="d-inline">
                                                <input type="hidden" name="id" value="<?= $unidad->getId(); ?>">



                                                <button type="button" class="btn btn-danger btn-sm" title="Eliminar"
                                                    aria-label="Eliminar" onclick="confirmarEliminacion(this)"
                                                    <?= ($unidad->getProductos()) ?  "disabled" : "" ?>>
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach ?>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="ShowCategories" tabindex="-1" aria-labelledby="modalShowCategories"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="modalShowCategories">Categorías</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                    <table class="table" id="categoriesTable">
                            <thead class="table-primary">
                                <tr>
                                    <th scope="col">ID</th>
                                    <th scope='col'>Nombre</th>
                                    <th scope="col" class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>


                                <?php foreach($categorias as $categoria): ?>

                                <tr>
                                    <th scope="row"><?= $categoria->getId(); ?></th>
                                    <td>
                                        <span id="label-name-<?= $categoria->getId(); ?>"> <?= $categoria->getNombre(); ?></span>
                                        <input type="text" class="form-control d-none" id="input-name-<?= $categoria->getId(); ?>" name="name"
                                            value="<?= $categoria->getNombre(); ?>" required>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center gap-2">
                                            <!-- Editar -->
                                            <button class="btn btn-primary btn-sm" id="<?= $categoria->getId(); ?>" type="button" onclick="toggleEdit(this)">
                                                <i class="fas fa-edit"></i>
                                            </button>

                                            <!-- Guardar -->

                                            <button class="btn btn-success btn-sm d-none" id="<?= $categoria->getId(); ?>"  data-table="categoria" type="button" onclick="saveEdit(this)">
                                                <i class="fas fa-save"></i>
                                            </button>

                                            

                                            <!-- Eliminar -->
                                            <form method="POST" action="../actions/categoria_borrar.php" class="d-inline">
                                                <input type="hidden" name="id" value="<?= $categoria->getId(); ?>">
                                                <button type="button" class="btn btn-danger btn-sm" title="Eliminar"
                                                    aria-label="Eliminar" onclick="confirmarEliminacion(this)"
                                                    <?= ($categoria->getProductos()) ?  "disabled" : "" ?>>
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach ?>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script>
        function formatPrices() {
            document.querySelectorAll('.price-format').forEach(element => {
                const price = parseInt(element.textContent);
                element.textContent = new Intl.NumberFormat('es-MX', {
                    style: 'currency',
                    currency: 'MXN',
                    minimumFractionDigits: 2
                }).format(price / 1);
            });
        }


        document.addEventListener('DOMContentLoaded', function () {
            // Elementos del DOM
            const searchInput = document.getElementById('searchInput');
            const categoryFilter = document.getElementById('categoryFilter');
            const clearSearch = document.getElementById('clearSearch');
            const productsTable = document.getElementById('productsTable');
            const noResults = document.getElementById('noResults');
            const rows = productsTable.querySelectorAll('tbody tr');

            // Función para filtrar
            function filterProducts() {
                const searchTerm = searchInput.value.toLowerCase();
                const selectedCategory = categoryFilter.value;
                let visibleCount = 0;

                rows.forEach(row => {
                    const name = row.cells[1].textContent.toLowerCase();
                    const category = row.getAttribute('data-category');
                    const matchesSearch = name.includes(searchTerm);
                    const matchesCategory = !selectedCategory || category === selectedCategory;

                    if (matchesSearch && matchesCategory) {
                        row.classList.remove('d-none');
                        visibleCount++;
                    } else {
                        row.classList.add('d-none');
                    }
                });

                noResults.classList.toggle('d-none', visibleCount > 0);
            }

            searchInput.addEventListener('input', filterProducts);
            categoryFilter.addEventListener('change', filterProducts);

            clearSearch.addEventListener('click', function () {
                searchInput.value = '';
                filterProducts();
                searchInput.focus();
            });

            formatPrices();
        });
    </script>



    <script src="../js/toggleForm.js"></script>
    <script src="../js/confirmations.js"></script>
    <script src="../js/toggleEdit.js"></script>
    <!--   Bootstrap JS   -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq"
        crossorigin="anonymous"></script>
    <!-- Form Validation JS -->
    <script src="../js/form-validation.js"></script>
</body>

</html>