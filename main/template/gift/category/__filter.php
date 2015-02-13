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
    /** @var \Model\Product\Filter\Entity $holidayProperty */
    $holidayProperty = null;
    /** @var \Model\Product\Filter\Entity $sexProperty */
    $sexProperty = null;
    /** @var \Model\Product\Filter\Entity $statusProperty */
    $statusProperty = null;
    /** @var \Model\Product\Filter\Entity $ageProperty */
    $ageProperty = null;

    foreach ($productFilter->getFilterCollection() as $property) {
        if (!$property->getIsInList()) {
            continue;
        } else if ($property->isPrice()) {
            $priceProperty = $property;
            $priceProperty->setStepType('price');
        } else if ('category' === $property->getId()) {
            $categoryProperty = $property;
        } else if ('holiday' === $property->getId()) {
            $holidayProperty = $property;
        } else if ('sex' === $property->getId()) {
            $sexProperty = $property;
        } else if ('status' === $property->getId()) {
            $statusProperty = $property;
        } else if ('age' === $property->getId()) {
            $ageProperty = $property;
        }
    }
    ?>

    <div class="fltrBtn fltrBtn-gift" style="background-image: url('/styles/catalog/img/bg-ny-gift.jpg?2')">
        <a class="fltrBtn-action-item" href="/gift_holiday" target="_blank"><img src="/styles/catalog/img/gift-sale-1000.png" alt=""></a>
        <form id="productCatalog-filter-form" class="fltrBtnPosBottom js-category-filter" action="<?= $baseUrl ?>" method="GET">
            <div class="fltrBtnLn">
                <? if ($holidayProperty): ?>
                    <?= $helper->render('gift/category/filter/property/__dropBox', ['productFilter' => $productFilter, 'property' => $holidayProperty]) ?>
                <? endif ?>

                <? if ($sexProperty): ?>
                    <?= $helper->render('gift/category/filter/property/__dropBox', ['productFilter' => $productFilter, 'property' => $sexProperty]) ?>
                <? endif ?>

                <? if ($statusProperty): ?>
                    <?= $helper->render('gift/category/filter/property/__dropBox', ['productFilter' => $productFilter, 'property' => $statusProperty, 'sexProperty' => $sexProperty]) ?>
                <? endif ?>

                <? if ($ageProperty): ?>
                    <?= $helper->render('gift/category/filter/property/__dropBox', ['productFilter' => $productFilter, 'property' => $ageProperty, 'initialValue' => 'Возраст']) ?>
                <? endif ?>
            </div>

            <div class="fltrBtnLn fltrBtnLn-bg">
                <? if ($priceProperty): ?>
                    <?= $helper->render('gift/category/filter/property/__slider', ['productFilter' => $productFilter, 'property' => $priceProperty]) ?>
                <? endif ?>
            </div>

            <div class="fltrBtnLn fltrBtnLn-lst fltrBtnLn-bg clearfix js-gift-category-filter-category">
                <? if ($categoryProperty): ?>
                    <?= $helper->render('gift/category/filter/property/__list', ['productFilter' => $productFilter, 'property' => $categoryProperty]) ?>
                <? endif ?>
            </div>
        </form>

        <a href="<?= $helper->url('product.category', ['categoryPath' => 'gifthobby/podarochnie-sertifikati-enter-2287']) ?>" class="fltrBtn_anim-item" target="_blank"><img class="fltrBtn_anim-item_img" src="/styles/catalog/img/gift-card.png" alt=""></a>
    </div>

<? };