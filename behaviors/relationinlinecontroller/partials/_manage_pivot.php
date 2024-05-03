<div id="relationManagePivotPopup" data-request-data="_relation_inline_field: '<?= $relationField ?>'">
    <?= Form::open() ?>
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="popup">&times;</button>
            <h4 class="modal-title"><?= e(trans($relationManageTitle, ['name'=>trans($relationLabel)])) ?></h4>
        </div>
        <?php if (!$relationSearchWidget): ?>
            <div class="modal-body">
                <p><?= e(trans('backend::lang.relation.help')) ?></p>
            </div>
        <?php endif ?>
        <div class="list-flush">
            <?php if ($relationSearchWidget): ?>
                <?= $relationSearchWidget->render() ?>
            <?php endif ?>
            <?php if ($relationManageFilterWidget): ?>
                <?= $relationManageFilterWidget->render() ?>
            <?php endif ?>
            <?= $relationManageWidget->render() ?>
        </div>

        <div class="modal-footer">
            <?= $this->relationInlineMakePartial('manage_pivot_footer') ?>
        </div>
    <?= Form::close() ?>
</div>
<script>
    setTimeout(
            function(){ $('#relationInlineManagePivotPopup input.form-control:first').focus() },
            310
    )
</script>
