<?php
return function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Filter $productFilter,
    \Model\Product\Filter\Entity $property
) {
    $values = $productFilter->getValue($property);
?>

    <? foreach ($property->getOption() as $option): ?>
        <label class=" <? if (!$property->getIsMultiple()): ?>mCustomLabelRadio<? endif ?>">
            <input
                class="customInput js-customInput"
                type="checkbox"
                name="<?= \View\Name::productCategoryFilter($property, $option, true) ?>"
                value="<?= $option->getId() ?>"
                <? if (in_array($option->getId(), $values)): ?>
                    checked="checked"
                <? endif ?>
            />

            <span class="customLabel_btx"><?= $option->getName() ?></span>
        </label>
    <? endforeach ?>

<? };