<?php

return function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Filter $productFilter,
    \Model\Product\Filter\Entity $filter
) {
    $values = $productFilter->getValue($filter);
    $category = $helper->getParam('selectedCategory');
    $categoryId = $category ? $category->getId() : null;
    ?>


    <? $i = 0; foreach ($filter->getOption() as $option): ?>
        <?
        $optionId = $option->getId();
        $viewId = \View\Id::productCategoryFilter($filter->getId()) . '-option-' . $optionId;
        ?>

        <div class="fltrBtn_i <? if ($option->getImageUrl()): ?>bFilterValuesCol-gbox<? endif ?>">
            <input
                class="customInput customInput-btn jsCustomRadio js-customInput <?= $filter->isBrand() ? 'js-category-filter-brand js-category-v2-filter-brand' : '' ?>"
                type="<?= $filter->getIsMultiple() ? 'checkbox' : 'radio' ?>"
                id="<?= $viewId ?>"
                name="<?= \View\Name::productCategoryFilter($filter, $option) ?>"
                value="<?= $optionId ?>"
                <? if ($filter->isBrand()) { echo 'data-name="',$option->getName(),'"'; } ?>
                <? if (in_array($optionId, $values) || $optionId === $categoryId) { ?> checked="checked"<? } ?>
                />
            <label class="fltrBtn_btn icon-clear <? if (!$filter->getIsMultiple()) { ?> mCustomLabelRadio<? } ?>" for="<?= $viewId ?>">
                <? if ($option->getImageUrl()): ?>
                    <img class="fltrBtn_btn_img" src="<?= $helper->escape($option->getImageUrl()) ?>">
                <? else: ?>
                    <span class="fltrBtn_btn_tx"><?= $option->getName() ?></span>
                <? endif ?>
            </label>
        </div>
        <? $i++; endforeach ?>
<? };