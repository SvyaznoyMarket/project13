<?php

return function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Filter $productFilter,
    \Model\Product\Filter\Entity $filter
) { ?>


    <div class="bFilterPrice">
        <span class="bFilterPrice__eTitle"><?= $filter->getName() ?></span>
        <input class="bFilterPrice__eInput" name="" value="<?= $helper->clearZeroValue($filter->getMin()) ?>" type="text" />

        <div class="bFilterSlider">
            <div class="ui-slider-range ui-widget-header ui-corner-all" style="left: 0%; width: 50%;"></div>
            <a class="ui-slider-handle ui-state-default ui-corner-all" href="#" style="left: -14px;"></a>
            <a class="ui-slider-handle ui-state-default ui-corner-all" href="#" style="left: 50%;"></a>
        </div>

        <input class="bFilterPrice__eInput mLast" name="" value="<?= $helper->clearZeroValue($filter->getMax()) ?>" type="text" />

        <span class="bFilterPrice__eRub rubl">p</span>
    </div>


<? };