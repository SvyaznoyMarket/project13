<?php
/**
 * @param \Model\Product\Category\Entity[] $categories
 */
return function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Filter $productFilter,
    $baseUrl
) {

    /** @var \Model\Product\Filter\Entity $priceProperty */
    $priceProperty = null;
    /** @var \Model\Product\Filter\Entity $categoryProperty */
    $categoryProperty = null;
    /** @var \Model\Product\Filter\Entity[] $tagProperties */
    $tagProperties = [];

    foreach ($productFilter->getFilterCollection() as $property) {
        if (!$property->getIsInList()) {
            continue;
        } else if ($property->isPrice()) {
            $priceProperty = $property;
            $priceProperty->setStepType('price');
        } else if ('category' === $property->getId()) {
            $categoryProperty = $property;
        } else if ('tag' === $property->getId()) {
            $tagProperties[] = $property;
        }
    }
    ?>

    <div class="fltrBtn fltrBtn-bt">
        <form id="productCatalog-filter-form" class="js-category-filter" action="<?= $baseUrl ?>" method="GET">

            <? if ($priceProperty): ?>
                <?= $helper->render('gift/category/filter/property/__slider', ['productFilter' => $productFilter, 'property' => $priceProperty]) ?>
            <? endif ?>

            <? if ($categoryProperty): ?>
                <?= $helper->render('gift/category/filter/property/__list', ['productFilter' => $productFilter, 'property' => $categoryProperty]) ?>
            <? endif ?>

            <? foreach ($tagProperties as $property): ?>
                <?= $helper->render('gift/category/filter/property/__dropBox', ['productFilter' => $productFilter, 'property' => $property]) ?>
            <? endforeach ?>
        </form>
    </div>

<? };