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
        <div class="fltrBtn_ln">
            <input
                class="customInput customInput-defcheck2 js-customInput"
                type="checkbox"
                id="<?= $viewId ?>"
                name="<?= \View\Name::productCategoryFilter($filter, $value) ?>"
                value="<?= $value ?>"
                <? if (in_array($value, $values)) { ?> checked="checked"<? } ?>
                />
            <label class="customLabel" for="<?= $viewId ?>">
                <?= $name ?>
            </label>
        </div>
    <? endforeach ?>


<? };