<?php

return function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Filter $productFilter,
    \Model\Product\Filter\Entity $filter
) { 
    $data = [
        'min' => $helper->clearZeroValue($filter->getMin()),
        'max' => $helper->clearZeroValue($filter->getMax()),
        'step' => 0.1
    ];
?>


    <div class="bRangeSlider">
        <span class="bRangeSlider__eTitle"><?= $filter->getName() ?></span>
        <input class="bRangeSlider__eInput mFromRange" name="" value="<?= $helper->clearZeroValue($filter->getMin()) ?>" type="text" />

        <div class="bFilterSlider" data-config="<?= $helper->json($data) ?>">
            <div class="ui-slider-range ui-widget-header ui-corner-all" style="left: 0%; width: 50%;"></div>
            <a class="ui-slider-handle ui-state-default ui-corner-all" href="#" style="left: -14px;"></a>
            <a class="ui-slider-handle ui-state-default ui-corner-all" href="#" style="left: 50%;"></a>
        </div>

        <input class="bRangeSlider__eInput mLast mToRange" name="" value="<?= $helper->clearZeroValue($filter->getMax()) ?>" type="text" />

        <span class="bRangeSlider__eRub rubl">p</span>
    </div>


<? };