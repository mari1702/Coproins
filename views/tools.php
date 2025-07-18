<?php
require_once "../components/templates/template.php";
require_once "../components/organisms/toolForm.php";
require_once "../components/organisms/modalNewObject.php";
require_once "../components/organisms/modalShowObjects.php";
require_once "../components/molecules/modal.php";

include "../components/organisms/navbar.php";
include "../components/organisms/alerts.php";

require_once "../controllers/DepartamentoController.php";
require_once "../controllers/MarcaController.php";
require_once "../controllers/HerramientaController.php";




$departamentos = DepartamentoController::listar();
$marcas = MarcaController::listar();
$herramientas = HerramientaController::listar();

startTemplate("Herramientas")

?>


<header>
    <?php
    navBar('herramientas');
    ?>
</header>


<section class="container-fluid">
    <?php
    alerts(); 
    startModal("NewTool","Registrar herramienta");
    toolForm("herramienta_crear.php",$marcas,$departamentos);
    endModal();
    ?>
</section>


<section class="container-fluid">
    <div class="row mb-4">
        <div class="card col-12 shadow mb-5 rounded border-0">
            <div class="card-body">

                <!-- Filtros y búsqueda -->
                <div class="row mb-4">

                    <div class="col-lg-2 col-sm-3 mb-3">
                        <div class="input-group">
                            <button class="btn btn-primary" 
                            type="button" 
                            data-bs-toggle="modal"       
                            data-bs-target="#NewTool">
                            <i class="fas fa-plus"> </i>
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
                                                    <option value='" . htmlspecialchars($departamento->getId()) . "'>" . htmlspecialchars($departamento->getNombre())  . "</option>
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


                        <?php foreach ($herramientas as $herramienta): ?>

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
    <?php
    modalNewObject('departamento');
    modalNewObject('marca');
    ?>
</section>

<section>
    <?php
    modalShowObjects('departamento', $departamentos);
    modalShowObjects('marca', $marcas);
    ?>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
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

        clearSearch.addEventListener('click', function() {
            searchInput.value = '';
            filterProducts();
            searchInput.focus();
        });

    });
</script>



<script src="../js/confirmations.js"></script>
<script src="../js/toggleEdit.js"></script>
<script src="../js/tool-form-validation.js"></script>

<?php
endTemplate();
