<?php
$f = function(
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
                        <dd class="props-list__val"><?= $property->getStringValue() ?>

                        <? if ($property->getHint()) : ?>

                            <div class="props-list__hint">
                                <a class="i-product i-product--hint" href="" onclick="$('.info-popup--open').removeClass('info-popup--open');$(this).next().addClass('info-popup--open'); return false;"></a>
                                <!-- попап с подсказкой, чтобы показать/скрыть окно необходимо добавить/удалить класс info-popup--open -->
                                <div class="prop-hint info-popup">
                                    <i class="closer" onclick="$(this).parent().removeClass('info-popup--open')">×</i>
                                    <div class="info-popup__inn"><?= $property->getHint() ?></div>
                                </div>
                                <!--/ попап с подсказкой -->
                            </div>
                        <? endif ?>

                        </dd>

                    <? endforeach ?>

                </dl>

            <? endif ?>

        <? endforeach ?>

    </div>

<?}; return $f;