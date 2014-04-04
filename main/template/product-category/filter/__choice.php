<?php

return function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Filter $productFilter,
    \Model\Product\Filter\Entity $filter
) {

    $values = $productFilter->getValue($filter);
    $disabledFilters = $helper->getParam('disabledFilters');
?>


    <? foreach ([1 => 'да', 0 => 'нет'] as $value => $name): ?>
    <?
        $viewId = \View\Id::productCategoryFilter($filter->getId()) . '-option-' . $value;
        $inputName = \View\Name::productCategoryFilter($filter, $value);

        $hideOption = false;
        if (
            \App::config()->sphinx['showFacets'] && $disabledFilters &&
            in_array($inputName, array_keys($disabledFilters))
        ) {
            $hideOption = true;
        }
    ?>
        <div class="bFilterValuesCol"<? if ($hideOption): ?> style="display:none;"<? endif ?>>
            <input
                class="bInputHidden bCustomInput jsCustomRadio"
                type="checkbox"
                id="<?= $viewId ?>"
                name="<?= $inputName ?>"
                value="<?= $value ?>"
                <? if (in_array($value, $values)) { ?> checked="checked"<? } ?>
                />
            <label class="bFilterCheckbox" for="<?= $viewId ?>">
                <?= $name ?>
            </label>
        </div>
    <? endforeach ?>


<? };