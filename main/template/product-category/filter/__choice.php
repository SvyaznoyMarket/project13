<?php

return function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Filter $productFilter,
    \Model\Product\Filter\Entity $filter
) {

    $values = $productFilter->getValue($filter);
    $disabledFilters = $helper->getParam('disabledFilters');
    $disabledFilters = isset($disabledFilters['choice']) ? $disabledFilters['choice'] : null;

    $changedFilters = $helper->getParam('changedFilters');
    $changedFilters = isset($changedFilters['choice']) ? $changedFilters['choice'] : null;

    $showFacets = \App::config()->sphinx['showFacets'];
    $quantities = $filter->getQuantity();
?>


    <? foreach ([1 => 'да', 0 => 'нет'] as $value => $name): ?>
    <?
        $viewId = \View\Id::productCategoryFilter($filter->getId()) . '-option-' . $value;
        $inputName = \View\Name::productCategoryFilter($filter, $value);

        $hideOption = false;
        $quantity = is_array($quantities) && isset($quantities[$value]) ? $quantities[$value] : null;
        if ($showFacets) {
            if ($disabledFilters && in_array($inputName, array_keys($disabledFilters))) {
                $hideOption = true;
            }

            // обновление значений
            if ((bool)$changedFilters) {
                if (in_array($inputName, array_keys($changedFilters))) {
                    $quantity = $changedFilters[$inputName];
                }
            }
        }
    ?>
        <div class="bFilterValuesCol jsFilterChoice"<? if ($hideOption): ?> style="display:none;"<? endif ?>>
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
                <? if ($showFacets && $quantity): ?> <span class='facet'>(<?= $quantity ?>)</span><? endif ?>
            </label>
        </div>
    <? endforeach ?>


<? };