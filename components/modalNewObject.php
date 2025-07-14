<?php
function modalNewObject($table) {
    $modalId = "new_" . $table;
    $modalLabelId = "modal_new_" . $table;
    $formAction = "../actions/" . $table . "_crear.php";
    $placeHolder = "Ingrese el nombre de la ".$table;
    ?>
        <div class="modal fade" id="<?= $modalId ?>" tabindex="-1" aria-labelledby="<?= $modalLabelId ?>"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="<?= $modalLabelId ?>">Registrar <?= $table ?></h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">

                        <form method="POST" action="<?= $formAction ?>">
                            <div class="form-group mb-3">
                                <label for="name"><b>Nombre</b></label>
                                <input type="text" class="form-control" id="name" name="name"
                                    placeholder="<?= $placeHolder ?>" required>
                            </div>

                            <button type="submit" class="btn btn-primary col-12">Guardar</button>
                        </form>


                    </div>

                </div>
            </div>
        </div>
    <?php
}
