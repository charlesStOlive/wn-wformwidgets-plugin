<div
    id="<?= $this->relationInlineGetId() ?>"
    data-request-data="_relation_inline_field: '<?= $relationField ?>', _relation_inline_extra_config: '<?= e(base64_encode(json_encode($relationExtraConfig))) ?>'"
    class="relation-behavior relation-view-<?= $relationViewMode ?>">

    <?php if ($toolbar = $this->relationInlineRenderToolbar()): ?>
        <!-- Relation Toolbar -->
        <div id="<?= $this->relationInlineGetId('toolbar') ?>" class="relation-toolbar">
            <?= $toolbar ?>
        </div>
    <?php endif ?>

    <!-- Relation View -->
    <div id="<?= $this->relationInlineGetId('view') ?>" class="relation-manager">
        <?= $this->relationInlineRenderView() ?>
    </div>

</div>
