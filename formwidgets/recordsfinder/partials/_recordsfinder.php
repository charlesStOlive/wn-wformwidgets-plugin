<?php
$previewMode = false;
if ($this->previewMode || $field->readOnly) {
    $previewMode = true;
}
?>
<div
    id="<?= $this->getId() ?>"
    class="field-recordsfinder loading-indicator-container size-input-text"
    data-control="recordsfinder"
    data-refresh-handler="<?= $this->getEventHandler('onRefresh') ?>"
    data-data-locker="#<?= $field->getId() ?>">
    <span class="form-control" <?= $previewMode ? 'disabled="disabled"' : '' ?>>
        <?php if ($selectedValues): ?>
            <?php foreach($selectedValues as $key=>$value) : ?>
                <?php if($previewMode) : ?>
                    <div class="selected-value button-no-border">
                        <?= $value; ?>
                    </div>
                <?php else : ?>
                    <button class="selected-value button-no-border"  data-request="<?= $this->getEventHandler('onClearOneRecord') ?>"
                        data-request-data="clearRecordId:'<?=$key?>'">
                        <?= $value; ?>
                    <i class="icon-times"></i>
                    </button>
                <?php endif  ?>
            <?php endforeach ?>
        <?php else: ?>
            <span class="text-muted"><?= $prompt ?></span>
        <?php endif ?>
    </span>

    <?php if (!$previewMode): ?>
        <?php if ($selectedValues): ?>
            <button
                type="button"
                class="btn btn-default clear-record"
                data-request="<?= $this->getEventHandler('onClearRecord') ?>"
                data-request-confirm="<?= e(trans('waka.wformwidgets::lang.form.action_confirm')) ?>"
                data-request-success="var $locker = $('#<?= $field->getId() ?>'); $locker.val(''); $locker.trigger('change')"
                aria-label="Remove">
                <i class="icon-times"></i>
            </button>
        <?php endif ?>
        <button
            id="<?= $this->getId('popupTrigger') ?>"
            class="btn btn-default find-record"
            data-control="popup"
            data-size="huge"
            data-handler="<?= $this->getEventHandler('onFindRecord') ?>"
            data-request-data="recordsfinder_flag: 1"
            type="button">
            <i class="icon-th-list"></i>
        </button>
    <?php endif ?>

    <input
        type="hidden"
        name="<?= $field->getName() ?>"
        id="<?= $field->getId() ?>"
        value="<?= e(json_encode(array_keys($selectedValues) , true)) ?>"
        />
</div>
