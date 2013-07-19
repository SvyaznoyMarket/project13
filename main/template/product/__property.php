<?php

return function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Entity $product
) { ?>

<? $groupedProperties = $product->getGroupedProperties() ?>
<div class="bSpecifications">
    <dl class="bSpecifications__eList clearfix">
    <? foreach ($groupedProperties as $group): ?>
        <? if (!(bool)$group['properties']) continue ?>

        <? foreach ($group['properties'] as $property): ?>
            <? /** @var $property \Model\Product\Property\Entity  */?>
            <dd>
            <span><?= $property->getName() ?>
                <? if ($property->getHint()): ?>
                    <?= $helper->render('__hint', ['name' => $property->getName(), 'value' => $property->getHint()]) ?>
                <? endif ?>
            </span>
            </dd>
            <dt>
                <?= $property->getStringValue() ?>
                <? if ($property->getValueHint()): ?>
                    <?= $helper->render('__hint', ['name' => $property->getStringValue(), 'value' => $property->getValueHint()]) ?>
                <? endif ?>
            </dt>
        <? endforeach ?>
    <? endforeach ?>
    </dl>
</div><!--/product specifications section -->

<? };