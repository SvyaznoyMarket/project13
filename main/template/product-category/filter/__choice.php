<?php

return function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Filter $productFilter,
    \Model\Product\Filter\Entity $filter
) {

    $values = $productFilter->getValue($filter);
?>


    <? foreach ([1 => 'да', 0 => 'нет'] as $id => $name): ?>
    <?
        $viewId = \View\Id::productCategoryFilter($filter->getId()) . '-option-' . $id;
    ?>
        <div class="bFilterValuesCol">
            <input
                class="bInputHidden bCustomInput jsCustomRadio"
                type="checkbox"
                id="<?= $viewId ?>"
                name="<?= \View\Name::productCategoryFilter($filter) ?>"
                value="<?= $id ?>"
                hidden
                <? if (in_array($id, $values)) { ?> checked="checked"<? } ?>
                />
            <label class="bFilterCheckbox" for="<?= $viewId ?>">
                <?= $name ?>
            </label>
        </div>
    <? endforeach ?>


<? };