<?php
return function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Filter $productFilter,
    \Model\Product\Filter\Entity $property
) {
    $values = $productFilter->getValue($property);
?>

    <? foreach ($property->getOption() as $option): ?>
    <div class="fltrBtn_ln fltrBtn_ln-col">
        <input
            class="customInput customInput-defcheck3 js-customInput"
            type="checkbox"
            id="<?= $option->getId() ?>"
            name="<?= \View\Name::productCategoryFilter($property, $option, true) ?>"
            value="<?= $option->getId() ?>"
            <? if (in_array($option->getId(), $values)): ?>
                checked="checked"
            <? endif ?>
        />

        <label class="customLabel" for="<?= $option->getId() ?>">
            <?= $option->getName() ?>
        </label>
    </div>
    <? endforeach ?>

<? };