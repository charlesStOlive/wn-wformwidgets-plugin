<?php if ($this->previewMode && !isset($selectedValues)): ?>

    <span class="form-control" disabled="disabled"><?= e(trans('waka.wformwidgets::lang.form.preview_no_record_message')) ?></span>

<?php else: ?>

    <div class="recordfinder-widget" id="<?= $this->getId('container') ?>">
        <?= $this->makePartial('recordsfinder') ?>
    </div>

<?php endif ?>
