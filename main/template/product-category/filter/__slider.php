<?php

return function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Filter $productFilter,
    \Model\Product\Filter\Entity $filter,
    array $promoStyle = []
) { 
    $data = [
        'min'  => $helper->clearZeroValue($filter->getMin()),
        'max'  => $helper->clearZeroValue($filter->getMax()),
        'step' => $filter->isPrice() ? 10 : $filter->getStepByFilter()
    ];

    $nameFrom = \View\Name::productCategoryFilter($filter, 'from');
    $nameTo = \View\Name::productCategoryFilter($filter, 'to');

    $disabledFilters = $helper->getParam('disabledFilters');
    $hideOption = false;
    if (
        \App::config()->sphinx['showFacets'] && $disabledFilters &&
        (in_array($nameFrom, array_keys($disabledFilters)) || in_array($nameTo, array_keys($disabledFilters)))
    ) {
        $hideOption = true;
    }
?>


    <div class="bRangeSlider"<? if(!empty($promoStyle['bRangeSlider'])): ?> style="<?= $promoStyle['bRangeSlider'] ?><? if ($hideOption): ?> display:none;<? endif ?>"<? endif ?>>
        <? if ($filter->isPrice()): ?>
            <span class="bRangeSlider__eTitle"><?= $filter->getName() ?></span>
        <? endif ?>
        <input class="bRangeSlider__eInput mFromRange" name="<?= $nameFrom ?>" value="<?= $helper->clearZeroValue($productFilter->getValueMin($filter)) ?>" type="text" />

        <div class="bFilterSlider" data-config="<?= $helper->json($data) ?>">
            <a class="ui-slider-handle ui-state-default ui-corner-all" href="#"></a>
            <a class="ui-slider-handle ui-state-default ui-corner-all" href="#"></a>
        </div>

        <input class="bRangeSlider__eInput mLast mToRange" name="<?= $nameTo ?>" value="<?= $helper->clearZeroValue($productFilter->getValueMax($filter)) ?>" type="text" />

        <? if ($filter->isPrice()): ?>
            <span class="bRangeSlider__eRub rubl">p</span>
        <? else: ?>
            <span class="bRangeSlider__eRub"><?= $filter->getUnit() ?></span>
        <? endif ?>
    </div>


<? };