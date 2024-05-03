<?php if ($relationViewMode == 'single'): ?>
    <button
        class="btn btn-sm btn-secondary wn-icon-minus"
        data-request="onRelationInlineButtonRemove"
        data-request-success="$.wn.relationInlineBehavior.changed('<?= e($relationField) ?>', 'removed')"
        data-stripe-load-indicator>
        <?= e(trans($text)) ?>
    </button>
<?php else: ?>
    <button
        class="btn btn-sm btn-secondary wn-icon-minus"
        onclick="$(this).data('request-data', {
            checked: $('#<?= $this->relationInlineGetId('view') ?> .control-list').listWidget('getChecked')
        })"
        disabled="disabled"
        data-request="onRelationInlineButtonRemove"
        data-request-success="$.wn.relationInlineBehavior.changed('<?= e($relationField) ?>', 'removed')"
        data-trigger-action="enable"
        data-trigger="#<?= $this->relationInlineGetId('view') ?> .control-list input[type=checkbox]"
        data-trigger-condition="checked"
        data-stripe-load-indicator>
        <?= e(trans($text)) ?>
    </button>
<?php endif ?>
