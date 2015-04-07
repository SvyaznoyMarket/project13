<?php

return function(
    \Helper\TemplateHelper $helper,
    $groupedProperties
) {
?>

<h3 id="productspecification" class="bHeadSection">Характеристики</h3>
<div class="bSpecifications">
    <? foreach ($groupedProperties as $group): ?>
    <?
        if (!(bool)$group['properties']) continue;

        uasort($group['properties'], function(\Model\Product\Property\Entity $a, \Model\Product\Property\Entity $b) {
            return $a->getGroupPosition() - $b->getGroupPosition();
        });
    ?>

        <div class="bSpecifications__eHead"><?= $group['group']->getName() ?></div>
        <dl class="bSpecificationsList clearfix">
            <? foreach ($group['properties'] as $property): ?>
                <? /** @var $property \Model\Product\Property\Entity  */?>
                <dd class="bSpecificationsList__eName">
                <span class="bName"><?= $property->getName() ?>
                    <? if ($property->getHint()): ?>
                        <?= $helper->render('__hint', ['name' => $property->getName(), 'value' => $property->getHint()]) ?>
                    <? endif ?>
                </span>
                </dd>
                <dt class="bSpecificationsList__eValue">
                    <?= $property->getStringValue() ?>
                    <? if ($property->getValueHint()): ?>
                        <?= $helper->render('__hint', ['name' => $property->getStringValue(), 'value' => $property->getValueHint()]) ?>
                    <? endif ?>
                </dt>
            <? endforeach ?>
        </dl>
    <? endforeach ?>
</div><!--/product specifications section -->

<? };