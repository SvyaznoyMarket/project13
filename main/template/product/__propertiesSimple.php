<?php

/**
 * Выводим все сгруппированные характеристики.
 * Функция не сработает для \Model\Product\ExpandedEntity
 * Важно знать, что есть также характеристики не имеющие группы.
 */
return function (
    \Helper\TemplateHelper $helper,
    $product,
    $showLinkToProperties = true
) {

    if ( $product instanceof \Model\Product\Entity ) {
        $groupedProperties = $product->getGroupedProperties();
    }else{
        return '';
    }

    ?>
    <div class="bSpecifications mSimpleProperty">

        <? if (count($groupedProperties) > 0) { ?>
            <dl class="bSpecificationsList clearfix">
                <? foreach ($groupedProperties as $group) { ?>
                    <? if (!(bool)$group['properties']) continue ?>

                    <? foreach ($group['properties'] as $property) { ?>
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
                    <? } //endforeach $group ?>
                <? } //endforeach $groupedProperties ?>
            </dl>
        <? } ?>

        <? if ($showLinkToProperties): ?>
            <div class="bTextMore">
                <a class="jsGoToId" data-goto="productspecification" href="">Все характеристики</a>
            </div>
        <? endif; ?>

    </div>
<?
}; //end function