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

    $valueFrom = $helper->clearZeroValue($productFilter->getValueMin($filter));
    $valueTo = $helper->clearZeroValue($productFilter->getValueMax($filter));

    $disabledFilters = $helper->getParam('disabledFilters');
    $disabledFilters = isset($disabledFilters['slider']) ? $disabledFilters['slider'] : null;

    $changedFilters = $helper->getParam('changedFilters');
    $changedFilters = isset($changedFilters['slider']) ? $changedFilters['slider'] : null;
    $hideOption = false;
    if (\App::config()->sphinx['showFacets']) {
        if ((bool)$disabledFilters && (in_array($nameFrom, array_keys($disabledFilters)) || in_array($nameTo, array_keys($disabledFilters)))) {
            $hideOption = true;
        }

        // обновление значений
//        if ((bool)$changedFilters) {
//            if (in_array($nameFrom, array_keys($changedFilters))) {
//                $valueFrom = $changedFilters[$nameFrom];
//            }
//            if (in_array($nameTo, array_keys($changedFilters))) {
//                $valueTo = $changedFilters[$nameTo];
//            }
//        }
    }
?>


    <div class="bRangeSlider jsFilterSlider"<? if(!empty($promoStyle['bRangeSlider'])): ?> style="<?= $promoStyle['bRangeSlider'] ?><? if ($hideOption): ?> display:none;<? endif ?>"<? endif ?>>
        <? if ($filter->isPrice()): ?>
            <span class="bRangeSlider__eTitle"><?= $filter->getName() ?></span>
        <? endif ?>
        <input class="bRangeSlider__eInput mFromRange" name="<?= $nameFrom ?>" value="<?= $valueFrom ?>" type="text" />

        <div class="bFilterSlider" data-config="<?= $helper->json($data) ?>">
            <a class="ui-slider-handle ui-state-default ui-corner-all" href="#"></a>
            <a class="ui-slider-handle ui-state-default ui-corner-all" href="#"></a>
        </div>

        <input class="bRangeSlider__eInput mLast mToRange" name="<?= $nameTo ?>" value="<?= $valueTo ?>" type="text" />

        <? if ($filter->isPrice()): ?>
            <span class="bRangeSlider__eRub rubl">p</span>
        <? else: ?>
            <span class="bRangeSlider__eRub"><?= $filter->getUnit() ?></span>
        <? endif ?>
    </div>


<? };