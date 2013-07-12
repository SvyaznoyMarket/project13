<?php

return function(
    \Model\Product\Entity $product
) {
    if (!((bool)$product->getModel() && (bool)$product->getModel()->getProperty())) return '';
?>

<div class="bProductDesc__eStore-select">
    <? foreach ($product->getModel()->getProperty() as $property): ?>
        <? if (false && $property->getIsImage()): ?>
        <? else: ?>
        <?
            /** @var $productAttribute Model\Product\Property\Entity */
            $productAttribute = $product->getPropertyById($property->getId());
            if (!$productAttribute) break;
        ?>

        <? endif ?>
        <div class="bDescSelectItem clearfix">
            <strong class="bDescSelectItem__eName"><?= $property->getName() ?></strong>

            <span class="bDescSelectItem__eValue"><?= $productAttribute->getStringValue() ?></span>

            <select class="bDescSelectItem__eSelect">
                <? foreach ($property->getOption() as $option): ?>
                    <option class="bDescSelectItem__eOption" value="<?= $option->getHumanizedName() ?>" data-url="<?= $option->getProduct()->getLink() ?>"<? if ($option->getValue() == $productAttribute->getValue()): ?> selected="selected"<? endif ?>><?= $option->getHumanizedName() ?></option>
                <? endforeach ?>
            </select>
        </div>
    <? endforeach ?>

</div><!--/additional product options -->

<? };