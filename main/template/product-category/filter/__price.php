<?php

return function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Filter $productFilter,
    \Model\Product\Filter\Entity $filter
) { 
    $data = [
        'min' => $helper->clearZeroValue($filter->getMin()),
        'max' => $helper->clearZeroValue($filter->getMax()),
        'step' => $filter->getStepByFilter()
    ];
?>


    <div class="bRangeSlider">
        <span class="bRangeSlider__eTitle"><?= $filter->getName() ?></span>
        <input class="bRangeSlider__eInput mFromRange" name="f-<?= $filter->getId() . '-from' ?>" value="<?= $helper->clearZeroValue($filter->getMin()) ?>" type="text" />

        <div class="bFilterSlider" data-config="<?= $helper->json($data) ?>">
            <a class="ui-slider-handle ui-state-default ui-corner-all" href="#"></a>
            <a class="ui-slider-handle ui-state-default ui-corner-all" href="#"></a>
        </div>

        <input class="bRangeSlider__eInput mLast mToRange" name="f-<?= $filter->getId() . '-to' ?>" value="<?= $helper->clearZeroValue($filter->getMax()) ?>" type="text" />

        <span class="bRangeSlider__eRub rubl">p</span>
    </div>


<? };