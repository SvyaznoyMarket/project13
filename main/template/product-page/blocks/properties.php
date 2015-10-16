<?php
$f = function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Entity $product
){

    if (!$product->getSecondaryGroupedProperties()) return '';

    ?>

    <div class="product-section__props">

        <div class="product-section__tl">Характеристики</div>

        <? foreach ($product->getSecondaryGroupedProperties() as $propertyGroup) : ?>

            <? if ($propertyGroup['properties']) : // бывает, что в группе нет свойств ?>

                <div class="product-section__sbtl"><?= $propertyGroup['group']->getName() ?></div>

                <dl class="props-list clearfix">

                    <? foreach ($propertyGroup['properties'] as $property) : /** @var $property \Model\Product\Property\Entity */ ?>

                        <dt class="props-list__name--tl"><span class="props-list__name-i"><?= $property->getName() ?></span></dt>
                        <dd class="props-list__val">
                            <?= $property->getStringValue() ?>
                            <?= $helper->render('product-page/blocks/hint', ['name' => '', 'value' => $property->getHint()]) ?>
                        </dd>

                    <? endforeach ?>

                </dl>

            <? endif ?>

        <? endforeach ?>

    </div>

<?}; return $f;