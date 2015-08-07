<?php

/**
 * Важно знать, что есть также сгруппированные характеристики и характеристики, не имеющие группы.
 */
return function (
    \Helper\TemplateHelper $helper,
    \Model\Product\Entity $product
) {

    $properties = $product->getMainProperties();

    uasort($properties, function(\Model\Product\Property\Entity $a, \Model\Product\Property\Entity $b) {
        return $a->getPosition() - $b->getPosition();
    });
?>

    <div class="bSpecifications mSimpleProperty">

        <? if (count($properties) > 0) { ?>
            <dl class="bSpecificationsList clearfix">
                <? foreach ($properties as $property): ?>
                    <? // @var $property \Model\Product\Property\Entity ?>
                    <dd class="bSpecificationsList__eName">
                        <span class="bName">
                            <?= $property->getName() ?>
                            <?= $helper->render('product-page/blocks/hint', ['name' => $property->getName(), 'value' => $property->getHint()]) ?>
                        </span>
                    </dd>
                    <dt class="bSpecificationsList__eValue">
                        <span>
                            <?= $property->getStringValue() ?>
                            <?= $helper->render('product-page/blocks/hint', ['name' => $property->getStringValue(), 'value' => $property->getValueHint()]) ?>
                        </span>
                    </dt>
                <? endforeach; ?>
            </dl>
        <? } ?>

        <? if ($product->getSecondaryGroupedProperties()): ?>
            <div class="bTextMore">
                <a class="jsGoToId" data-goto="productspecification" href="">Все характеристики</a>
            </div>
        <? endif; ?>

    </div>
<?
};