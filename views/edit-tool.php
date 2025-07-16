<?php
require_once "../components/templates/template.php";
require_once "../components/organisms/toolForm.php";
require_once "../components/organisms/modalNewObject.php";
require_once "../components/organisms/modalShowObjects.php";

include "../components/organisms/navbar.php";

require_once '../models/Herramienta.php';
require_once "../controllers/DepartamentoController.php";
require_once "../controllers/MarcaController.php";




$departamentos = DepartamentoController::listar();
$marcas = MarcaController::listar();

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
                <?php
                toolForm("herramienta_editar.php", $marcas, $departamentos, $herramienta);
                ?>
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

<script src="../js/tool-form-validation.js"></script>
<?php
endTemplate();
