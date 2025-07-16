<?php
function startModal(string $modalId, string $title): void
{
    ?>
    <div class="modal fade" id="<?= htmlspecialchars($modalId) ?>" tabindex="-1"
         aria-labelledby="modal<?= htmlspecialchars($modalId) ?>Label" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modal<?= htmlspecialchars($modalId) ?>Label">
                        <?= htmlspecialchars($title) ?>
                    </h5>
                    <button type="button" class="btn-close btn-close-white"
                            data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
    <?php
    ob_start();
}

function endModal(): void
{
    echo ob_get_clean();
    ?>
                </div>
            </div>
        </div>
    </div>
    <?php
}
?>
