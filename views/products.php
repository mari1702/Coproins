<?php
require_once "../components/templates/template.php";
require_once "../components/organisms/productForm.php";
require_once "../components/organisms/productDetails.php";
require_once "../components/organisms/modalNewObject.php";
require_once "../components/organisms/modalShowObjects.php";
require_once "../components/molecules/modal.php";

include "../components/organisms/navbar.php";
include "../components/organisms/alerts.php";


require_once "../controllers/CategoriaController.php";
require_once "../controllers/UnidadMedidaController.php";
require_once "../controllers/ProductoController.php";




$categorias = CategoriaController::listar();
$unidades = UnidadMedidaController::listar();
$productos = ProductoController::listar();

startTemplate("Productos");
?>

<header>
    <!-- Navbar -->
    <?php
    navBar('productos');
    ?>
</header>


<section class="container-fluid">

    <?php
    alerts(); 
    startModal("NewProduct","Registrar Producto");
    productForm("producto_crear.php",$unidades,$categorias);
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
                            data-bs-target="#NewProduct">
                                <i class="fas fa-plus"> </i>
                                Nuevo
                            </button>
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
                                                <option value='" . htmlspecialchars($categoria->getId()) . "'>" . htmlspecialchars($categoria->getNombre())  . "</option>
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


                        <?php foreach ($productos as $producto): ?>

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
                                                        <?php
                                                        productDetails($producto);
                                                        ?>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">Cerrar</button>
                                                        <a href="edit-product.php?id=<?= $producto->getId(); ?>" class="btn btn-primary">Editar
                                                            Producto
                                                        </a>
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
    <?php
    modalNewObject('unidad');
    modalNewObject('categoria');
    ?>
</section>

<section>
    <?php
    modalShowObjects('unidad', $unidades);
    modalShowObjects('categoria', $categorias);
    ?>
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


    document.addEventListener('DOMContentLoaded', function() {
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

        clearSearch.addEventListener('click', function() {
            searchInput.value = '';
            filterProducts();
            searchInput.focus();
        });

        formatPrices();
    });
</script>



<script src="../js/confirmations.js"></script>
<script src="../js/toggleEdit.js"></script>

<?php
endTemplate();
