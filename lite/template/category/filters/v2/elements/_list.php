<?php

return function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Filter $productFilter,
    \Model\Product\Filter\Entity $filter
) {
    $values = $productFilter->getValue($filter);
    $category = $helper->getParam('selectedCategory');
    $categoryId = $category ? $category->getId() : null;

    $showFasets = \App::config()->sphinx['showFacets'];
    ?>


    <? $i = 0; foreach ($filter->getOption() as $option): ?>
        <?
        $optionId = $option->getId();
        $viewId = \View\Id::productCategoryFilter($filter->getId()) . '-option-' . $optionId;
        ?>
        <div class="fltrBtn_ln <? if ($option->getImageUrl()): ?>bFilterValuesCol-gbox<? endif ?>">
            <input
                class="custom-input <?= $filter->getIsMultiple() ? 'custom-input_check' : 'custom-input_radio' ?>"
                type="<?= $filter->getIsMultiple() ? 'checkbox' : 'radio' ?>"
                id="<?= $viewId ?>"
                name="<?= \View\Name::productCategoryFilter($filter, $option) ?>"
                value="<?= $optionId ?>"
                <? if ($filter->isBrand()) { echo 'data-name="',$option->getName(),'"'; } ?>
                <? if (in_array($optionId, $values) || $optionId === $categoryId) { ?> checked="checked"<? } ?>
                />
            <label class="custom-label" for="<?= $viewId ?>">
                <? if ($option->getImageUrl()): ?>
                    <span class="customLabel_wimg"></span>
                    <img class="customLabel_bimg" src="<?= $helper->escape($option->getImageUrl()) ?>">
                <? endif ?>

                <span class="customLabel_btx"><?= $option->getName() ?><?= ($showFasets && $option->getQuantity()) ? " ({$option->getQuantity()})" : '' ?></span>
            </label>
        </div>
        <? $i++; endforeach ?>
<? };