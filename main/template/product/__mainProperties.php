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
        return ($b->getPosition() ?: 1000) - ($a->getPosition() ?: 1000);
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
                            <? if ($property->getHint()): ?>
                                <?= $helper->render('__hint', ['name' => $property->getName(), 'value' => $property->getHint()]) ?>
                            <? endif ?>
                        </span>
                    </dd>
                    <dt class="bSpecificationsList__eValue">
                        <span>
                            <?= $property->getStringValue() ?>
                            <? if ($property->getValueHint()): ?>
                                <?= $helper->render('__hint', ['name' => $property->getStringValue(), 'value' => $property->getValueHint()]) ?>
                            <? endif ?>
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