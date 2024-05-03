<a
    href="javascript:;"
    class="btn btn-sm btn-secondary wn-icon-unlink"
    data-request="onRelationInlineButtonUnlink"
    data-request-success="$.wn.relationInlineBehavior.changed('<?= e($relationField) ?>', 'removed')"
    data-request-confirm="<?= e(trans('backend::lang.relation.unlink_confirm')) ?>"
    data-stripe-load-indicator>
    <?= e(trans($text)) ?>
</a>
