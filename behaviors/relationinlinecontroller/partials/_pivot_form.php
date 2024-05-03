<?php if ($relationManageId): ?>

    <?= Form::ajax('onRelationInlineManagePivotUpdate', [
        'data' => ['_relation_inline_field' => $relationField, 'manage_id' => $relationManageId],
        'data-request-success' => "$.wn.relationInlineBehavior.changed('" . e($relationField) . "', 'updated')",
        'data-popup-load-indicator' => true
    ]) ?>

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="popup">&times;</button>
            <h4 class="modal-title"><?= e(trans('backend::lang.relation.related_data', ['name'=>trans($relationLabel)])) ?></h4>
        </div>
        <div class="modal-body">
            <?= $relationPivotWidget->render(['preview' => $this->readOnly]) ?>
        </div>
        <div class="modal-footer">
            <?= $this->relationInlineMakePartial('pivot_form_footer') ?>
        </div>

    <?= Form::close() ?>

<?php else: ?>

    <?= Form::ajax('onRelationInlineManagePivotCreate', [
        'data' => ['_relation_inline_field' => $relationField, 'foreign_id' => $foreignId],
        'data-request-success' => "$.wn.relationInlineBehavior.changed('" . e($relationField) . "', 'created')",
        'data-popup-load-indicator' => true
    ]) ?>

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="popup">&times;</button>
            <h4 class="modal-title"><?= e(trans('backend::lang.relation.related_data', ['name'=>trans($relationLabel)])) ?></h4>
        </div>
        <div class="modal-body">
            <?= $relationPivotWidget->render() ?>
        </div>
        <div class="modal-footer">
            <button
                type="submit"
                class="btn btn-primary">
                <?= e(trans('backend::lang.relation.add')) ?>
            </button>
            <button
                type="button"
                class="btn btn-default"
                data-dismiss="popup">
                <?= e(trans('backend::lang.relation.cancel')) ?>
            </button>
        </div>

    <?= Form::close() ?>

<?php endif ?>
