<?php
return function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Filter $productFilter,
    \Model\Product\Filter\Entity $property
) {
    $values = $productFilter->getValue($property);
?>

    <? foreach ($property->getOption() as $option): ?>
        <? $id = \View\Id::productCategoryFilter($property->getId()) . '-option-' . $option->getId(); ?>
        <div class="fltrBtn_ln fltrBtn_ln-col">
            <input
                class="customInput customInput-defcheck3 js-customInput"
                type="checkbox"
                id="<?= $helper->escape($id) ?>"
                name="<?= \View\Name::productCategoryFilter($property, $option) ?>"
                value="<?= $option->getId() ?>"
                <? if (in_array($option->getId(), $values)): ?>
                    checked="checked"
                <? endif ?>
                data-title="<?= $helper->escape($option->getName()) ?>"
            />

            <label class="customLabel customLabel-defcheck3" for="<?= $helper->escape($id) ?>">
                <?= $option->getName() ?>
            </label>
        </div>
    <? endforeach ?>

<? };