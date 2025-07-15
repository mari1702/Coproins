<?php
require_once "../components/templates/template.php";
require_once "../components/organisms/productForm.php";

include "../components/navbar.php";
include "../components/modalNewObject.php";


require_once '../models/Producto.php';
require_once "../controllers/CategoriaController.php";
require_once "../controllers/UnidadMedidaController.php";




$categorias = CategoriaController::listar();
$unidades = UnidadMedidaController::listar();

$producto = new Producto()->getById($_GET['id']);

startTemplate("Editar Producto");
?>

<header>
    <!-- Navbar -->
    <?php
    navBar('productos');
    ?>
</header>

<section class="container-fluid">

    <div class=" new row justify-content-center">
        <div class=" mb-3">
            <div class="input-group">
                <a class="btn btn-primary" href="products.php" id="showForm"><i class="fas fa-arrow-left"> </i> Regresar</a>
            </div>
        </div>
        <div class="card col-sm-11 col-md-7 col-lg-4 shadow p-3 mb-5 bg-white rounded">
            <div class="card-body">

                <h2 class="text-center">Editar producto</h2>
                <?php
                productForm("producto_editar.php",$unidades,$categorias,$producto);
                ?>
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

<script src="../js/product-form-validation.js"></script>
<?php
endTemplate();
