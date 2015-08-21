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

        <!-- секция -->
        <div class="filter-values__cell">
            <input class="custom-input <?= $filter->getIsMultiple()
                ? 'custom-input_check js-category-v2-filter-element-list-checkbox'
                : 'custom-input_radio js-category-v2-filter-element-list-radio' ?>"
                   type="<?= $filter->getIsMultiple() ? 'checkbox' : 'radio' ?>"
                   id="<?= $viewId ?>"
                   name="<?= \View\Name::productCategoryFilter($filter, $option) ?>"
                   value="<?= $optionId ?>"
                >
            <label class="custom-label filter-img-box"
                   for="<?= $viewId ?>">
                <span class="customLabel_wimg"></span>
                <? if ($option->getImageUrl()): ?>
                    <img class="customLabel_bimg" src="<?= $helper->escape($option->getImageUrl()) ?>">
                <? endif ?>

                <span class="customLabel_btx"><?= $option->getName() ?></span>
            </label>
        </div>
        <!--/ секция -->
        <? $i++; endforeach ?>
<? };