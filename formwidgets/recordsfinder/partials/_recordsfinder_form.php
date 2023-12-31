<div id="<?= $this->getId('popup') ?>" class="recordsfinder-popup">
    <?= Form::open(['data-request-parent' => "#{$parentElementId}"]) ?>
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="popup">&times;</button>
            <h4 class="modal-title"><?= e(trans($title)) ?></h4>
        </div>

        <div class="recordfinder-list list-flush" data-request-data="recordsfinder_flag: 1">
            <?= $searchWidget->render() ?>
            <?= $listWidget->render() ?>
        </div>

        <div class="modal-footer">
            <button
                id="#validateSelection"
                type="submit"
                class="btn btn-primary"
                data-dismiss="popup"
                data-request="<?= $this->getEventHandler('onValidateSelection') ?>">
                <?= e(trans('waka.wformwidgets::lang.recordsfinder.validate')) ?>
            </button>
            <button
                type="button"
                class="btn btn-default"
                data-dismiss="popup">
                <?= e(trans('waka.wformwidgets::lang.recordsfinder.cancel')) ?>
            </button>
        </div>
    <?= Form::close() ?>
</div>

<script>
    setTimeout(
        function(){ $('#<?= $this->getId('popup') ?> input.form-control:first').focus() },
        310
    )
</script>
