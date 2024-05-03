<?php if ($relationViewMode == 'single'): ?>
    <button
        class="btn btn-sm btn-secondary wn-icon-trash-o"
        data-request="onRelationInlineButtonDelete"
        data-request-confirm="<?= e(trans('backend::lang.relation.delete_confirm')) ?>"
        data-request-success="$.wn.relationInlineBehavior.changed('<?= e($relationField) ?>', 'deleted')"
        data-stripe-load-indicator>
        <?= e(trans($text)) ?>
    </button>
<?php else: ?>
    <button
        class="btn btn-sm btn-secondary wn-icon-trash-o"
        onclick="$(this).data('request-data', {
            checked: $('#<?= $this->relationInlineGetId('view') ?> .control-list').listWidget('getChecked')
        })"
        disabled="disabled"
        data-request="onRelationInlineButtonDelete"
        data-request-confirm="<?= e(trans('backend::lang.relation.delete_confirm')) ?>"
        data-request-success="$.wn.relationInlineBehavior.changed('<?= e($relationField) ?>', 'deleted')"
        data-trigger-action="enable"
        data-trigger="#<?= $this->relationInlineGetId('view') ?> .control-list input[type=checkbox]"
        data-trigger-condition="checked"
        data-stripe-load-indicator>
        <?= e(trans($text)) ?>
    </button>
<?php endif ?>
