<?php

//print_r($productExpanded);

return function (
    \Helper\TemplateHelper $helper,
    \Model\Product\ExpandedEntity $productExpanded,
    $showLinkToProperties = true
) {

    $properties = $productExpanded->getProperty();

    ?>
    <div class="jewel mb15">
        <div class="bSpecifications">

            <? if (count($properties) > 0) { ?>
                <dl class="bSpecifications__eList clearfix">
                    <? foreach ($properties as $property): ?>
                        <dd><span><?= $property->getName() ?></span></dd>
                        <dt><span><?= $property->getStringValue() ?></span></dt>
                    <? endforeach; ?>
                </dl>
            <? } ?>

            <? if ($showLinkToProperties): ?>
                <div class="bTextMore">
                    <a class="jsGoToId" data-goto="productspecification" href="">Все характеристики</a>
                </div>
            <? endif; ?>

        </div>
    </div>
<?
}; //end function