<?
return function(\Model\Product\Filter $productFilter, $openFilter, $baseUrl, $categories = []) {
    $helper = \App::helper();
    /** @var \Model\Product\Filter\Entity[] $otherFilters */
    $otherFilters = [];
    /** @var \Model\Product\Filter\Entity $priceFilter */
    $priceFilter = null;

    $insertCustomFilters = function() use (&$categories, &$otherFilters) {
        // фильтр "Товары по категориям"
        /** @var \Model\Product\Category\Entity[] $categories */
        if ($categories) {
            $categoryFilter = new \Model\Product\Filter\Entity();
            $categoryFilter->setId('category');
            $categoryFilter->setTypeId(\Model\Product\Filter\Entity::TYPE_LIST);
            $categoryFilter->setName('Товары по категориям');

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

    if (0 == $countInListFilters) return;

    $showParamsButton = (bool) ($countInListFilters > 1 || !$priceFilter);
?>

    <div class="filter filter-options fltr filter-components js-category-filter-wrapper">
        <form id="productCatalog-filter-form" class="bFilter clearfix js-category-filter" action="<?= $baseUrl ?>" method="GET">
            <div class="bFilterHead">
                <? if ($showParamsButton): ?>
                    <a class="bFilterToggle btnGrey <?= $openFilter ? 'fltrSet_tggl-dn' : '' ?> js-category-filter-otherParamsToggleButton js-category-filter-otherParamsToggleButton" href="#">
                        <!--noindex--><span class="bToggleText">Бренды и параметры</span><!--/noindex-->
                    </a>
                <? endif ?>

                <? if ($priceFilter && $productFilter): ?>
                    <?= $helper->render('category/filters/__element', ['productFilter' => $productFilter, 'filter' => $priceFilter]) ?>
                <? endif ?>
            </div>

            <div class="fltrSet js-category-filter-otherParams" style="padding-top: 0;">
                <div class="bFilterCont clearfix js-category-filter-otherParamsContent" <? if (!$openFilter): ?>style="display: none"<? endif ?>>
                    <ul class="bFilterParams">
                        <? foreach ($otherFilters as $i => $filter): ?>
                            <li class="bFilterParams__eItem <? if (0 == $i): ?>mActive<? endif ?> js-category-filter-param">
                                <span class="bParamName"><?= $filter->getName() ?></span>
                            </li>
                        <? endforeach ?>
                    </ul>

                    <div class="bFilterValues clearfix">
                        <? foreach ($otherFilters as $i => $filter): ?>
                            <div class="bFilterValuesItem clearfix <? if ($i > 0): ?>hf<? endif ?> <? if (in_array($filter->getId(), ['shop', 'category'])): ?>mLineItem<? endif ?>">
                                <?= $helper->render('category/filters/__element', ['productFilter' => $productFilter, 'filter' => $filter]) ?>
                            </div>
                        <? endforeach ?>
                    </div>
                </div>

                <div class="fltrBtn_kit fltrBtn_kit-nborder">
                    <div class="js-category-filter-selected">
                        <?= $helper->render('category/filters/selected.filters', ['productFilter' => $productFilter, 'baseUrl' => $baseUrl]) ?>
                    </div>
                </div>
            </div>
        </form>
    </div>

<?php }; ?>