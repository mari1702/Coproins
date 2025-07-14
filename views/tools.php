<?php
include "../components/navbar.php";

require_once "../controllers/DepartamentoController.php";
require_once "../controllers/MarcaController.php";
require_once "../controllers/HerramientaController.php";




$departamentos = DepartamentoController::listar();
$marcas = MarcaController::listar();
$herramientas = HerramientaController::listar();

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

    <title>Herramientas</title>
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

        <?php if (isset($_GET['status']) && $_GET['status'] === 'error'): ?>
            <div class="alert alert-danger" role="alert">
                <?= htmlspecialchars($_GET['message']) ?>
            </div>
        <?php endif; ?>

        <div class=" new row justify-content-center  d-none">
            <div class="card col-sm-11 col-md-7 col-lg-6 shadow p-3 mb-5 bg-white border-0 rounded">
                <div class="card-body">
                    
                    <h2 class="text-center">Registrar herramienta</h2>
                    <form method="POST" action="../actions/herramienta_crear.php" enctype="multipart/form-data">

                        <div class="form-group mb-3">
                            <label for="department"><b>Departamento</b></label>

                            <div class="row">
                                <div class="col-8 col-lg-10">
                                    <select id="department" name="department" class="form-control" required>
                                        <option disabled selected="">Seleccione departamento</option>
                                        <?php
                                            foreach ($departamentos as $departamento) {
                                                echo "
                                                    <option value='". htmlspecialchars($departamento->getId()) ."'>". htmlspecialchars($departamento->getNombre())  ."</option>
                                                ";  
                                            }
                                        ?>
                                    </select>
                                </div>

                                <div class="col-4 col-lg-2 align-content-center">
                                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#NewDepartment">
                                        <i class="fa fa-plus"></i>
                                    </button>

                                    <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#ShowDepartments">
                                        <i class="fa fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        

                        <div class="form-group mb-3">
                            <label for="brand"><b>Marca</b></label>
                            <div class="row">
                                <div class="col-8 col-lg-10">
                                    <select id="brand" name="brand" class="form-control" required>
                                        <option disabled selected="">Seleccione marca</option>
                                        <?php
                                            foreach ($marcas as $marca) {
                                                echo "
                                                    <option value='". htmlspecialchars($marca->getId()) ."'>". htmlspecialchars($marca->getNombre())  ."</option>
                                                ";  
                                            }
                                        ?>
                                    </select>
                                </div>

                                <div class="col-4 col-lg-2 align-content-center">
                                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#NewBrand">
                                        <i class="fa fa-plus"></i>
                                    </button>

                                    <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#ShowBrands">
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
                                <select class="form-select" id="departmentFilter">
                                    <option value="">Todos los departamentos</option>
                                        <?php
                                            foreach ($departamentos as $departamento) {
                                                echo "
                                                    <option value='". htmlspecialchars($departamento->getId()) ."'>". htmlspecialchars($departamento->getNombre())  ."</option>
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
                                    placeholder="Buscar herramientas...">
                                <button class="btn btn-outline-secondary" type="button" id="clearSearch">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <table class="table" id="toolsTable">
                        <thead class="table-primary">
                            <tr>
                                <th scope="col">ID</th>
                                <th scope='col'>Descripción</th>
                                <th scope="col">Marca</th>
                                <th scope="col" class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>


                            <?php foreach($herramientas as $herramienta): ?>

                            <tr data-department="<?= $herramienta->getDepartamento()->getId(); ?>">
                                <th scope="row"><?= $herramienta->getId(); ?></th>
                                <td><?= $herramienta->getDescripcion(); ?></td>
                                <td><?= $herramienta->getMarca()->getNombre(); ?></td>
                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        <!-- Ver -->
                                        <button class="btn btn-info btn-sm" type="button" data-bs-toggle="modal"
                                            data-bs-target="#<?= $herramienta->getId(); ?>">
                                            <i class="fas fa-eye"></i>
                                        </button>

                                        <div class="modal fade" id="<?= $herramienta->getId(); ?>" tabindex="-1"
                                            aria-labelledby="modal<?= $herramienta->getId(); ?>Label" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-primary text-white">
                                                        <h5 class="modal-title" id="modal<?= $herramienta->getId(); ?>Label">
                                                            Detalles de la herramienta
                                                        </h5>
                                                        <button type="button" class="btn-close btn-close-white"
                                                            data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row">

                                                            <?php if ($herramienta->getImgRuta()): ?>
                                                            <div class="col-md-6 col-sm-12 mb-3">
                                                                <img src="../<?= $herramienta->getImgRuta(); ?>"
                                                                    class="img-fluid rounded" alt="Producto 1">
                                                            </div>
                                                            <?php endif?>

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
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">Cerrar</button>
                                                        <a href="edit-tool.php?id=<?= $herramienta->getId(); ?>" class="btn btn-primary">Editar
                                                            Herramienta</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                        <!-- Editar -->
                                        <a href="edit-tool.php?id=<?= $herramienta->getId(); ?>" class="btn btn-primary btn-sm" title="Editar"
                                            aria-label="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <!-- Eliminar -->
                                        <form method="POST" action="../actions/herramienta_borrar.php" class="d-inline">
                                            <input type="hidden" name="id" value="<?= $herramienta->getId(); ?>">
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
                            <h5 class="text-muted">No se encontraron herramientas</h5>
                            <p class="text-muted">Intenta con otros términos de búsqueda</p>
                        </div>
                    </table>
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

    <section>

        <div class="modal fade" id="ShowDepartments" tabindex="-1" aria-labelledby="modalShowDepartments"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="modalShowDepartments">Departamentos</h5>
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


                                <?php foreach($departamentos as $departamento): ?>

                                <tr>
                                    <th scope="row"><?= $departamento->getId(); ?></th>
                                    <td>
                                        <span id="label-name-<?= $departamento->getId(); ?>"> <?= $departamento->getNombre(); ?></span>
                                        <input type="text" class="form-control d-none" id="input-name-<?= $departamento->getId(); ?>" name="name"
                                            value="<?= $departamento->getNombre(); ?>" required>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center gap-2">
                                            <!-- Editar -->
                                            <button class="btn btn-primary btn-sm" id="<?= $departamento->getId(); ?>" type="button" onclick="toggleEdit(this)">
                                                <i class="fas fa-edit"></i>
                                            </button>

                                            <!-- Guardar -->

                                            <button class="btn btn-success btn-sm d-none" id="<?= $departamento->getId(); ?>"  data-table="departamento" type="button" onclick="saveEdit(this)">
                                                <i class="fas fa-save"></i>
                                            </button>

                                            

                                            <!-- Eliminar -->
                                            <form method="POST" action="../actions/departamento_borrar.php" class="d-inline">
                                                <input type="hidden" name="id" value="<?= $departamento->getId(); ?>">
                                                <button type="button" class="btn btn-danger btn-sm" title="Eliminar"
                                                    aria-label="Eliminar" onclick="confirmarEliminacion(this)"
                                                    <?= ($departamento->getHerramientas()) ?  "disabled" : "" ?>>
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

        <div class="modal fade" id="ShowBrands" tabindex="-1" aria-labelledby="modalShowBrands"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="modalShowBrands">Departamentos</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                    <table class="table" id="brandsTable">
                            <thead class="table-primary">
                                <tr>
                                    <th scope="col">ID</th>
                                    <th scope='col'>Nombre</th>
                                    <th scope="col" class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>


                                <?php foreach($marcas as $marca): ?>

                                <tr>
                                    <th scope="row"><?= $marca->getId(); ?></th>
                                    <td>
                                        <span id="label-name-<?= $marca->getId(); ?>"> <?= $marca->getNombre(); ?></span>
                                        <input type="text" class="form-control d-none" id="input-name-<?= $marca->getId(); ?>" name="name"
                                            value="<?= $marca->getNombre(); ?>" required>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center gap-2">
                                            <!-- Editar -->
                                            <button class="btn btn-primary btn-sm" id="<?= $marca->getId(); ?>" type="button" onclick="toggleEdit(this)">
                                                <i class="fas fa-edit"></i>
                                            </button>

                                            <!-- Guardar -->

                                            <button class="btn btn-success btn-sm d-none" id="<?= $marca->getId(); ?>"  data-table="marca" type="button" onclick="saveEdit(this)">
                                                <i class="fas fa-save"></i>
                                            </button>

                                            

                                            <!-- Eliminar -->
                                            <form method="POST" action="../actions/marca_borrar.php" class="d-inline">
                                                <input type="hidden" name="id" value="<?= $marca->getId(); ?>">
                                                <button type="button" class="btn btn-danger btn-sm" title="Eliminar"
                                                    aria-label="Eliminar" onclick="confirmarEliminacion(this)"
                                                    <?= ($marca->getHerramientas()) ?  "disabled" : "" ?>>
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


        document.addEventListener('DOMContentLoaded', function () {
            // Elementos del DOM
            const searchInput = document.getElementById('searchInput');
            const departmentFilter = document.getElementById('departmentFilter');
            const clearSearch = document.getElementById('clearSearch');
            const toolsTable = document.getElementById('toolsTable');
            const noResults = document.getElementById('noResults');
            const rows = toolsTable.querySelectorAll('tbody tr');

            // Función para filtrar
            function filterProducts() {
                const searchTerm = searchInput.value.toLowerCase();
                const selectedDepartment = departmentFilter.value;
                let visibleCount = 0;

                rows.forEach(row => {
                    const name = row.cells[1].textContent.toLowerCase();
                    const department = row.getAttribute('data-department');
                    const matchesSearch = name.includes(searchTerm);
                    const matchesDepartment = !selectedDepartment || department === selectedDepartment;

                    if (matchesSearch && matchesDepartment) {
                        row.classList.remove('d-none');
                        visibleCount++;
                    } else {
                        row.classList.add('d-none');
                    }
                });

                noResults.classList.toggle('d-none', visibleCount > 0);
            }

            searchInput.addEventListener('input', filterProducts);
            departmentFilter.addEventListener('change', filterProducts);

            clearSearch.addEventListener('click', function () {
                searchInput.value = '';
                filterProducts();
                searchInput.focus();
            });

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
    <script src="../js/tool-form-validation.js"></script>
</body>

</html>