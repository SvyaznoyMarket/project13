<?php

return function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Filter $productFilter,
    \Model\Product\Filter\Entity $filter
) {
    $values = $productFilter->getValue($filter);
    ?>

    <? foreach ([1 => 'да', 0 => 'нет'] as $value => $name): ?>
        <? $viewId = \View\Id::productCategoryFilter($filter->getId()) . '-option-' . $value; ?>

        <div class="filter-values__cell">
            <input
                class="custom-input filter-check jsCustomRadio js-customInput"
                type="checkbox"
                id="<?= $viewId ?>"
                name="<?= \View\Name::productCategoryFilter($filter, $value) ?>"
                value="<?= $value ?>"
                <? if (in_array($value, $values)) { ?> checked="checked"<? } ?>
                />
            <label class="custom-label" for="<?= $viewId ?>">
                <?= $name ?>
            </label>
        </div>
    <? endforeach ?>
<? };