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

    <div class="fltrBtn fltrBtn-gift" style="background-image: url('/styles/catalog/img/bg-ny-gift.jpg')">
        <form id="productCatalog-filter-form" class="js-category-filter" action="<?= $baseUrl ?>" method="GET">
            <div class="fltrBtnLn">
                <? foreach ($tagProperties as $property): ?>
                    <?= $helper->render('gift/category/filter/property/__dropBox', ['productFilter' => $productFilter, 'property' => $property]) ?>
                <? endforeach ?>
            </div>

            <div class="fltrBtnLn fltrBtnLn-bg">
                <? if ($priceProperty): ?>
                    <?= $helper->render('gift/category/filter/property/__slider', ['productFilter' => $productFilter, 'property' => $priceProperty]) ?>
                <? endif ?>
            </div>

            <div class="fltrBtnLn fltrBtnLn-npb fltrBtnLn-lst fltrBtnLn-bg">
                <? if ($categoryProperty): ?>
                    <?= $helper->render('gift/category/filter/property/__list', ['productFilter' => $productFilter, 'property' => $categoryProperty]) ?>
                <? endif ?>
            </div>
        </form>
    </div>

<? };