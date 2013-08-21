<?php


return function (
    \Helper\TemplateHelper $helper,
    $product,
    $showLinkToProperties = true
) {

    if (
        $product instanceof \Model\Product\ExpandedEntity ||
        $product instanceof \Model\Product\Entity
    ) {

        $properties = $product->getProperty();

    }else{

        return '';

    }


    ?>
    <div class="jewel mb15">
        <div class="bSpecifications">

            <? if (count($properties) > 0) { ?>
                <dl class="bSpecificationsList clearfix">
                    <? foreach ($properties as $property): ?>
                        <dd class="bSpecificationsList__eName">
                            <span class="bName"><?= $property->getName() ?></span>
                        </dd>
                        <dt class="bSpecificationsList__eValue">
                            <span><?= $property->getStringValue() ?></span>
                        </dt>
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