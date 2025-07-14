<?php
function modalShowObjects($table, $objects) {
    $modalId = "show_" . $table;
    $modalLabelId = "modal_show_" . $table;
    $tableId = $table . "_table";
    $formAction = "../actions/" . $table . "_borrar.php";
    ?>

    <div class="modal fade" id="<?= $modalId ?>" tabindex="-1" aria-labelledby="<?= $modalLabelId ?>" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="<?= $modalLabelId ?>">Departamentos</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="table" id="<?= $tableId ?>">
                        <thead class="table-primary">
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Nombre</th>
                                <th scope="col" class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($objects as $object): ?>
                                <tr>
                                    <th scope="row"><?= $object->getId(); ?></th>
                                    <td>
                                        <span id="label-name-<?= $object->getId(); ?>"><?= $object->getNombre(); ?></span>
                                        <input type="text" class="form-control d-none"
                                               id="input-name-<?= $object->getId(); ?>" name="name"
                                               value="<?= $object->getNombre(); ?>" required>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center gap-2">
                                            <!-- Editar -->
                                            <button class="btn btn-primary btn-sm"
                                                    id="<?= $object->getId(); ?>"
                                                    type="button"
                                                    onclick="toggleEdit(this)">
                                                <i class="fas fa-edit"></i>
                                            </button>

                                            <!-- Guardar -->
                                            <button class="btn btn-success btn-sm d-none"
                                                    id="<?= $object->getId(); ?>"
                                                    data-table="<?= $table ?>"
                                                    type="button"
                                                    onclick="saveEdit(this)">
                                                <i class="fas fa-save"></i>
                                            </button>

                                            <!-- Eliminar -->
                                            <form method="POST" action="<?= $formAction ?>" class="d-inline">
                                                <input type="hidden" name="id" value="<?= $object->getId(); ?>">
                                                <button type="button" class="btn btn-danger btn-sm"
                                                        title="Eliminar" aria-label="Eliminar"
                                                        onclick="confirmarEliminacion(this)"
                                                    <?= $object->isDeleteable() ? 'disabled' : '' ?>>
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <?php
}
