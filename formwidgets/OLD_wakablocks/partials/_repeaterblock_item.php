<?php
    $groupCode = $useGroups ? $this->getGroupCodeFromIndex($indexValue) : '';
    $itemTitle = $useGroups ? $this->getGroupTitle($groupCode) : null;
?>
<li
    <?= $itemTitle ? 'data-collapse-title="' . e(trans($itemTitle)) . '"' : '' ?>
    class="field-repeaterblock-item">

    <?php if (!$this->previewMode) : ?>
        <?php if ($sortable) : ?>
            <div class="repeaterblock-item-handle <?= $this->getId('items') ?>-handle">
                <i class="icon-bars"></i>
            </div>
        <?php endif; ?>

        <div class="repeaterblock-item-remove">
            <button
                type="button"
                class="close"
                aria-label="Remove"
                data-repeaterblock-remove
                data-request="<?= $this->getEventHandler('onRemoveItem') ?>"
                data-request-data="'_repeaterblock_index': '<?= $indexValue ?>', '_repeaterblock_group': '<?= $groupCode ?>'"
                data-request-confirm="<?= e(trans('backend::lang.form.action_confirm')) ?>">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif ?>

    <div class="repeaterblock-item-copy">
        <a href="javascript:;" 
            class="repeaterblock-item-copy"
            data-control="popup"
            data-request-data="'_repeaterblock_index': '<?= $indexValue ?>', '_repeaterblock_group': '<?= $groupCode ?>'"
            data-handler="<?= $this->getEventHandler('onLoadCopy') ?>">
            <i class="fas icon-clipboard"></i>
        </a>
    </div>

    <div class="repeaterblock-item-collapse">
        <a href="javascript:;" class="repeaterblock-item-collapse-one">
            <i class="icon-chevron-up"></i>
        </a>
    </div>

    <a class="repeaterblock-item-collapsed-title" href="javascript:;">
        <?= $this->renderPreview($indexValue, $groupCode) ?>
    </a>

    <div class="field-repeaterblock-form"
         data-control="formwidget"
         data-refresh-handler="<?= $this->getEventHandler('onRefresh') ?>"
         data-refresh-data="'_repeaterblock_index': '<?= $indexValue ?>', '_repeaterblock_group': '<?= $groupCode ?>'">
        <?php foreach ($widget->getFields() as $field) : ?>
            <?= $widget->renderField($field) ?>
        <?php endforeach ?>
        <?php if ($useGroups) : ?>
            <input type="hidden" name="<?= $widget->arrayName ?>[_group]" value="<?= $groupCode ?>" />
        <?php endif ?>
    </div>

</li>
