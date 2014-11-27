<?php
/**
 * @param \Model\Product\Category\Entity[] $categories
 */
return function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Filter $productFilter,
    $baseUrl,
    $countUrl,
    $openFilter,
    array $promoStyle = [],
    array $categories = []
) {

    /** @var \Model\Product\Filter\Entity[] $alwaysShowFilters */
    $alwaysShowFilters = [];
    /** @var \Model\Product\Filter\Entity[] $otherFilters */
    $otherFilters = [];
    /** @var \Model\Product\Filter\Entity $priceFilter */
    $priceFilter = null;
    /** @var \Model\Product\Filter\Entity $saleFilter */
    $saleFilter = null;
    /** @var \Model\Product\Filter\Entity $widthFilter */
    $widthFilter = null;
    /** @var \Model\Product\Filter\Entity $brandFilter */
    $brandFilter = null;
    /** @var \Model\Product\Filter\Entity $classFilter */
    $classFilter = null;

    $insertCustomFilters = function() use (&$categories, &$otherFilters) {
        // фильтр "Товары по категориям"
        if ((bool)$categories) {
            $categoryFilter = new \Model\Product\Filter\Entity();
            $categoryFilter->setId('category');
            $categoryFilter->setTypeId(\Model\Product\Filter\Entity::TYPE_LIST);
            $categoryFilter->setName('Товары по категориям');
            $categoryFilter->getIsInList(true);

            foreach ($categories as $category) {
                $option = new \Model\Product\Filter\Option\Entity();
                $option->setId($category->getId());
                $option->setName($category->getName());
                $categoryFilter->addOption($option);
            }

            $otherFilters[] = $categoryFilter;
        }

    };

    $countFilters = count($productFilter->getFilterCollection());
    $countInListFilters = null;
    if (0 == $countFilters) {
        $insertCustomFilters();
    } else {
        $insertIndex = $countFilters > 3 ? 3 : $countFilters;
        $i = 1;
        $countInListFilters = 0;
        foreach ($productFilter->getFilterCollection() as $filter) {
            if (!$filter->getIsInList()) {
                continue;
            } else if ($filter->isPrice()) {
                $priceFilter = $filter;
                $priceFilter->setStepType('price');
            } else if ('Бренд' === $filter->getName()) {
                $brandFilter = $filter;
            } else if ('Сушка' === $filter->getName()) {
                $classFilter = $filter;
            } else if ('Ширина' === $filter->getName()) {
                $widthFilter = $filter;
            } else if ($filter->isSale()) {
                $saleFilter = $filter;
            } else if ($filter->getIsAlwaysShow()) {
                $alwaysShowFilters[] = $filter;
            } else {
                $otherFilters[] = $filter;
                $i++;
            }

            if ($insertIndex == $i) {
                $insertCustomFilters();
                $i++;
            }

            if ($filter->getIsInList()){
                $countInListFilters++;
            }
        }
    }

    if (0 === $countInListFilters) return;
?>

<div class="fltr">
    <form id="productCatalog-filter-form" class="bFilter clearfix js-filter" action="<?= $baseUrl ?>" data-count-url="<?= $countUrl ?>" method="GET">
        <? foreach ($alwaysShowFilters as $filter): ?>
            <div class="fltrSet js-filter-toggle-container <? if ('Металл' === $filter->getName()): ?>fltrSet-metall<? endif ?> <? if ('Вставка' === $filter->getName()): ?>fltrSet-insertion<? endif ?>">
                <div class="fltrSet_tggl fltrSet_tggl-dn js-filter-toggle-button">
                    <span class="fltrSet_tggl_tx"><?= $helper->escape($filter->getName()) ?></span>
                </div>

                <div class="fltrSet_cnt js-filter-toggle-content">
                    <div class="fltrSet_inn clearfix">
                        <?= $helper->render('product-category/filter2/__element', ['productFilter' => $productFilter, 'filter' => $filter, 'promoStyle' => $promoStyle]) ?>
                    </div>
                </div>
            </div>
        <? endforeach ?>

        <div class="flrtBox">
            <? if ($priceFilter): ?>
                <div class="js-productCategory-filter2-dropBox">
                    <a href="#" class="js-productCategory-filter2-dropBox-open">
                        <?= $priceFilter->getName() ?>
                    </a>

                    <ul style="display: none;" class="js-productCategory-filter2-dropBox-content">
                        <? foreach ($priceFilter->getPriceRanges() as $range): ?>
                            <li>
                                <a href="<?= $helper->escape($range['url']) ?>">
                                    <? if (isset($range['from'])): ?>
                                        от <?= $helper->escape($range['from']) ?>
                                    <? endif ?>

                                    <? if (isset($range['to'])): ?>
                                        до <?= $helper->escape($range['to']) ?>
                                    <? endif ?>
                                </a>
                            </li>
                        <? endforeach ?>
                    </ul>
                </div>

                <?= $helper->render('product-category/filter2/element/__slider', ['productFilter' => $productFilter, 'filter' => $priceFilter, 'promoStyle' => $promoStyle]) ?>
            <? endif ?>

            <? if ($saleFilter): ?>
                <div class="js-productCategory-filter2-dropBox">
                    <a href="#" class="js-productCategory-filter2-dropBox-open">
                        <?= $saleFilter->getName() ?>
                    </a>

                    <div style="display: none;" class="js-productCategory-filter2-dropBox-content">
                        <?= $helper->render('product-category/filter2/element/__list', ['productFilter' => $productFilter, 'filter' => $saleFilter, 'promoStyle' => $promoStyle]) ?>
                    </div>
                </div>
            <? endif ?>
        </div>

        <div class="flrtBox">
            <div class="js-productCategory-filter2-dropBox">
                <a href="#" class="js-productCategory-filter2-dropBox-open">
                    Габариты
                </a>

                <div style="display: none;" class="js-productCategory-filter2-dropBox-content">
                    <div>
                        Ширина
                        <?= $helper->render('product-category/filter2/element/__number', ['productFilter' => $productFilter, 'filter' => $widthFilter]) ?>
                    </div>
                    <div>
                        Сушка
                        <?= $helper->render('product-category/filter2/element/__choice', ['productFilter' => $productFilter, 'filter' => $classFilter]) ?>
                    </div>
                    <div>
                        Класс стирки
                        <?= $helper->render('product-category/filter2/element/__list', ['productFilter' => $productFilter, 'filter' => $brandFilter]) ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="fltrSet" style="padding-top: 0;">
            <?= $helper->render('product-category/__selectedFilter', ['productFilter' => $productFilter, 'baseUrl' => $baseUrl]) ?>
        </div>
    </form>
</div>    

<? };