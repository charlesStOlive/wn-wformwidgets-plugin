<?php if ($this->previewMode) : ?>

    <div class="form-control">
        <?= $value ?>
    </div>

<?php else : ?>

    <div id="<?= $this->getId() ?>" class="star-rating" data-control="starRating">
        <?php for ($i = 1; $i <= 5; $i++) : ?>
            <i class="star-rating-icon <?= $i <= $value ? 'icon-star' : 'icon-star-o' ?>" style="cursor:pointer"></i>
        <?php endfor; ?>
        <input type="hidden" name="<?= $field->getName() ?>" id="<?= $field->getId() ?>" value="<?= empty($value) ? null : $value ?>" />
    </div>

<?php endif ?>