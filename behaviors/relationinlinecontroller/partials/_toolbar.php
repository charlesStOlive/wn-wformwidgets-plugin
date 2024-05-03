<div data-control="toolbar">

    <?php foreach ($relationToolbarButtons as $type => $text): ?>

        <?php if ($type === 'update'): ?>
            <?= $this->relationInlineMakePartial('button_update', [
                'relationManageId' => $relationViewModel->getKey(),
                'text' => $text
            ]) ?>
        <?php else: ?>
            <?= $this->relationInlineMakePartial('button_' . $type, [
                'text' => $text
            ]) ?>
        <?php endif ?>

    <?php endforeach ?>

</div>
