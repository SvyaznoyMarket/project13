<?
return function(\Model\Product\Filter $productFilter, $openFilter, $baseUrl) {
    $helper = \App::helper();
    /** @var \Model\Product\Filter\Entity[] $alwaysShowFilters */
    $alwaysShowFilters = [];
    /** @var \Model\Product\Filter\Entity[] $otherFilters */
    $otherFilters = [];
    /** @var \Model\Product\Filter\Entity $priceFilter */
    $priceFilter = null;

    $countFilters = count($productFilter->getFilterCollection());
    $countInListFilters = null;
    if (0 != $countFilters) {
        $countInListFilters = 0;
        foreach ($productFilter->getFilterCollection() as $filter) {
            if (!$filter->getIsInList()) {
                continue;
            } else if ($filter->isPrice()) {
                $priceFilter = $filter;
                $priceFilter->setStepType('price');
            } else if ($filter->getIsAlwaysShow()) {
                $alwaysShowFilters[] = $filter;
            } else {
                $otherFilters[] = $filter;
            }

            if ($filter->getIsInList()){
                $countInListFilters++;
            }
        }
    }

    if (0 == $countInListFilters) return;
?>


    <!-- фильтр "Ювелирный" -->
    <div class="filter fltr js-category-filter-wrapper">
        <form id="productCatalog-filter-form" class="js-category-filter" action="<?= $baseUrl ?>" method="GET">
            <? foreach ($alwaysShowFilters as $filter): ?>
                <!-- фильтр по компонентам -->
                <div class="
                    fltrSet filter-components js-category-filter-toggle-container
                    <?= $filter->isOpenByDefault ? 'open' : '' ?>
                    <? if ($filter->isMetall()): ?>fltrSet-metall<? endif ?>
                    <? if ($filter->isInsertion()): ?>fltrSet-insertion<? endif ?>
                ">

                    <div class="fltrSet_tggl js-category-filter-toggle-button">
                        <span class="fltrSet_tggl_tx"><?= $helper->escape($filter->getName()) ?></span>
                    </div>

                    <div class="fltrSet_cnt">
                        <div class="fltrSet_inn">
                            <?= $helper->render('category/filters/v3/elements/_images', ['productFilter' => $productFilter, 'filter' => $filter]) ?>
                        </div>
                    </div>
                </div>
                <!--/ фильтр по компонентам -->
            <? endforeach ?>

            <? if ($priceFilter): ?>
                <div class="fltrBtn_kit fltrBtn_kit-box">
                    <div class="filter-price">
                        <?= $helper->render('category/filters/priceDropBox', ['productFilter' => $productFilter, 'filter' => $priceFilter]) ?>
                        <div class="fltrBtn_range"><?= $helper->render('category/filters/v3/elements/_slider', ['productFilter' => $productFilter, 'filter' => $priceFilter]) ?></div>
                    </div>
                </div>
            <? endif ?>

            <? if ($otherFilters): ?>
                <div class="fltrSet js-category-filter-toggle-container">
                    <div class="fltrSet_tggl <?= $openFilter ? 'fltrSet_tggl-dn' : '' ?> js-category-filter-toggle-button">
                        <span class="fltrSet_tggl_tx">Ещё параметры</span>
                    </div>

                    <div class="fltrSet_cnt">
                        <div class="filter-content">
                            <ul class="filter-params">
                                <? foreach ($otherFilters as $i => $filter): ?>
                                    <li class="filter-params__item <? if (0 == $i): ?>mActive<? endif ?> js-category-filter-param">
                                        <span class="filter-params__text"><?= $filter->getName() ?></span>
                                    </li>
                                <? endforeach ?>
                            </ul>

                            <div class="filter-values">
                                <div class="filter-values__inner">
                                    <? foreach ($otherFilters as $i => $filter): ?>
                                        <div class="filter-values__item <? if ($i > 0): ?>hf<? endif ?> <? if (in_array($filter->getId(), ['shop', 'category'])): ?>mLineItem<? endif ?> js-category-filter-group">
                                            <?= $helper->render('category/filters/v3/_element', ['productFilter' => $productFilter, 'filter' => $filter]) ?>
                                        </div>
                                    <? endforeach ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <? endif ?>

            <?= $helper->render('category/filters/v3/selected.filters', ['productFilter' => $productFilter, 'baseUrl' => $baseUrl]) ?>
        </form>
    </div>
    <!-- фильтр "Ювелирный" -->

<?php }; ?>