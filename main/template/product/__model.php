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

        <?
        $attrOptionValue = null;
        if ($productAttribute) {
            $attrOptionValue = $productAttribute->getValue();

            $attrOption = $productAttribute->getOption();
            $attrOption = is_array($attrOption) ? reset($attrOption) : null;
            if ($attrOption instanceof \Model\Product\Property\Option\Entity) {
                $attrOptionValue = $attrOption->getValue();
            }
        }
        ?>

        <div class="bDescSelectItem clearfix">
            <strong class="bDescSelectItem__eName"><?= $property->getName() ?></strong>

            <span class="bDescSelectItem__eValue"><?= $productAttribute->getStringValue() ?></span>

            <select class="bDescSelectItem__eSelect">
                <? foreach ($property->getOption() as $option): ?>
                    <option class="bDescSelectItem__eOption"
                            value="<?= $option->getHumanizedName() ?>"
                            data-url="<?= $option->getProduct()->getLink() ?>"<?
                            if ($attrOptionValue && $option->getValue() == $attrOptionValue):
                                ?> selected="selected"<? endif
                            ?>>
                        <?= $option->getHumanizedName() ?>
                        <? if (!$option->getProduct()->isAvailable()): ?>
                            (распродано)
                        <? endif ?>
                    </option>
                <? endforeach ?>
            </select>
        </div>
    <? endforeach ?>

</div><!--/additional product options -->

<? };