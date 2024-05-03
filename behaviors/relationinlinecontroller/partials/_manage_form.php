<div id="<?= $relationManageWidget->getId('managePopup') ?>">
    <?php if ($relationManageId): ?>

        <?= Form::ajax('onRelationInlineManageUpdate', [
            'data-popup-load-indicator' => true,
            'sessionKey' => $newSessionKey,
            'data-request-success' => "$.wn.relationInlineBehavior.changed('" . e($relationField) . "', 'updated')",
        ]) ?>

            <!-- Passable fields -->
            <input type="hidden" name="manage_id" value="<?= $relationManageId ?>" />
            <input type="hidden" name="_relation_inline_field" value="<?= $relationField ?>" />
            <input type="hidden" name="_relation_inline_mode" value="form" />
            <input type="hidden" name="_relation_session_key" value="<?= $relationSessionKey ?>" />

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="popup">&times;</button>
                <h4 class="modal-title">
                    <?= e(trans($relationManageTitle, ['name' => trans($relationLabel)])) ?>
                </h4>
            </div>

            <div class="modal-body">
                <?= $relationManageWidget->render(['preview' => $this->readOnly]) ?>
            </div>

            <div class="modal-footer">
                <?= $this->relationInlineMakePartial('manage_form_footer_update') ?>
            </div>

        <?= Form::close() ?>

    <?php else: ?>

        <?= Form::ajax('onRelationInlineManageCreate', [
            'data-popup-load-indicator' => true,
            'data-request-success' => "$.wn.relationInlineBehavior.changed('" . e($relationField) . "', 'created')",
            'sessionKey' => $newSessionKey
        ]) ?>

            <!-- Passable fields -->
            <input type="hidden" name="_relation_inline_field" value="<?= $relationField ?>" />
            <input type="hidden" name="_relation_inline_mode" value="form" />
            <input type="hidden" name="_relation_session_key" value="<?= $relationSessionKey ?>" />

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="popup">&times;</button>
                <h4 class="modal-title">
                    <?= e(trans($relationManageTitle, ['name' => trans($relationLabel)])) ?>
                </h4>
            </div>
            <div class="modal-body">

                <?= $relationManageWidget->render() ?>

            </div>
            <div class="modal-footer">
                <?= $this->relationInlineMakePartial('manage_form_footer_create') ?>
            </div>
        <?= Form::close() ?>

    <?php endif ?>

</div>

<script>
    $.wn.relationInlineBehavior.bindToPopups('#<?= $relationManageWidget->getId("managePopup") ?>', {
        _relation_inline_field: '<?= $relationField ?>',
        _relation_inline_mode: 'form'
    })
</script>
