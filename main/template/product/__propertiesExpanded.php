<?php

/**
 * Выводим все характеристики productExpanded
 * где в $productExpanded храняться главные характеристики.
 * Но если вместо $productExpanded передать $product, то выведуться все.
 * В принципе можно использовать не только для для \Model\Product\ExpandedEntity, но и для \Model\Product\Entity
 * Важно знать, что есть также сгруппированные характеристики и характеристики, не имеющие группы.
 */
return function (
    \Helper\TemplateHelper $helper,
    $productExpanded,
    $showLinkToProperties = true
) {

    if ( $productExpanded instanceof \Model\Product\ExpandedEntity ) {
        $properties = $productExpanded->getProperty();
    }else{
        return '';
    }


    ?>
    <div class="bSpecifications mSimpleProperty">

        <? if (count($properties) > 0) { ?>
            <dl class="bSpecificationsList clearfix">
                <? foreach ($properties as $property): ?>
                    <? // @var $property \Model\Product\Property\Entity
                    if ( !$property->getValue() ) continue;
                    ?>
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

        <? if ($showLinkToProperties): ?>
            <div class="bTextMore">
                <a class="jsGoToId" data-goto="productspecification" href="">Все характеристики</a>
            </div>
        <? endif; ?>

    </div>
<?
};