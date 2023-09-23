<?= Form::open(['id' => 'addRuleRuleForm']) ?>


<div class="modal-header">
    <button type="button" class="close" data-dismiss="popup">&times;</button>
    <h4 class="modal-title">Copier ou modifier le code ci dessous</h4>
</div>
<div class="modal-body">

    <?php if ($this->fatalError): ?>
    <p class="flash-message static error"><?= $fatalError ?></p>
    <?php else: ?>
    <input type="hidden" name="_repeaterblock_index" value="<?= $_repeaterblock_index ?>" />
    <input type="hidden" name="_previous_data" value='<?=$_previous_data?>' />
    <textarea id="values" name="values_copied" rows="20" cols="80"><?= $copied_data ?></textarea>

    <?php endif ?>

</div>

<div class="modal-footer">
    <button
                type="submit"
                class="btn btn-primary"
                data-request="<?= $this->getEventHandler('onSaveCopy') ?>"
                data-popup-load-indicator>
                <?= e(trans('backend::lang.form.save')) ?>
            </button>
    <button
            type="button"
            class="btn btn-default"
            data-dismiss="popup">
        <?= e(trans('backend::lang.form.cancel')) ?>
    </button>
</div>
<?= Form::close() ?>
