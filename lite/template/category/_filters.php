<?

    use \Model\Product\Filter\Option\Entity as Option;

    /**
     * @var $productFilter  \Model\Product\Filter
     * @var $baseUrl        string
     * @var $openFilter     bool
     * @var $promoStyle     []
     */

    $helper = \App::helper();
    $categories = [];
    $visibleBrandsCount = 12;

    /** @var \Model\Product\Filter\Entity[] $alwaysShowFilters */
    $alwaysShowFilters = [];
    /** @var \Model\Product\Filter\Entity $priceFilter */
    $priceFilter = null;
    /** @var \Model\Product\Filter\Entity $labelFilter */
    $labelFilter = null;
    /** @var \Model\Product\Filter\Entity $widthFilter */
    $brandFilter1 = null;
    /** @var \Model\Product\Filter\Entity $brandFilter2 */
    $brandFilter2 = null;
    /** @var \Model\Product\Filter\Entity[] $tyreFilters */
    $tyreFilters = [];

    $hasSelectedOtherBrands = false;

    $countInListFilters = 0;

    // Ювелирка
    foreach ($productFilter->getFilterCollection() as $filter) {

        foreach ($productFilter->getValues() as $valKey => $value) {
            if ($valKey == $filter->getId()) {
                $filter->isOpenByDefault = true;
            }
        }

        if (!$filter->getIsInList()) {
            continue;
        } else if ($filter->isPrice()) {
//            $priceFilter = $filter;
//            $priceFilter->setStepType('price');
        } else if ($filter->getIsAlwaysShow() && !$filter->isBrand()) {
            $alwaysShowFilters[] = $filter;
        } else {
//            $otherFilters[] = $filter;
//            $i++;
        }

        if ($filter->getIsInList()){
//            $countInListFilters++;
        }
    }

    foreach ($productFilter->getUngroupedPropertiesV2() as $key => $property) {
        if (!$property->getIsInList()) {
            continue;
        } else if ($property->isPrice()) {
            $priceFilter = $property;
            $priceFilter->setStepType('price');
        } else if ($property->isLabel()) {
            $labelFilter = $property;
        } else if ($property->isBrand() && $property->getIsAlwaysShow()) {
            $brandFilter1 = clone $property;

            $brandFilter2 = clone $property;
            $brandFilter2->deleteAllOptions();

            if (count($brandFilter1->getOption()) >= $visibleBrandsCount) {
                $values = $productFilter->getValue($property);
                while (count($brandFilter1->getOption()) >= $visibleBrandsCount - 1 ) {
                    $option = $brandFilter1->deleteLastOption();
                    if (in_array($option->getId(), $values)) {
                        $hasSelectedOtherBrands = true;
                    }

                    $brandFilter2->unshiftOption($option);
                }
            }
        } else {
            if ($property->isBrand()) { // Сортировка брендов по алфавиту
                $option = $property->getOption();
                usort($option, function(Option $a, Option $b){ return $a->getName() > $b->getName(); });
                $property->setOption($option);
            }
            $tyreFilters[$key] = $property;
        }

        $countInListFilters++;
    }

    if (0 == $countInListFilters) {
        return;
    }

?>



<!-- фильтр "Бренды и параметры" -->
<div class="filter filter-options fltr filter-components js-category-filter-wrapper" style="display: block">

    <form id="productCatalog-filter-form" class="js-category-filter" action="<?= $baseUrl ?>" method="GET">

        <? foreach ($alwaysShowFilters as $filter): ?>

            <!-- фильтр по компонентам -->
            <div class="fltrSet js-category-filter-toggle-container <?= $filter->isOpenByDefault ? 'open' : '' ?>">

                <div class="fltrSet_tggl js-category-filter-toggle-button">
                    <span class="fltrSet_tggl_tx"><?= $helper->escape($filter->getName()) ?></span>
                </div>

                <div class="fltrSet_cnt js-category-filter-toggle-content">
                    <div class="fltrSet_inn">
                        <?= $helper->render('category/filters/_images', ['productFilter' => $productFilter, 'filter' => $filter, 'promoStyle' => $promoStyle]) ?>
                    </div>
                </div>
            </div>
            <!--/ фильтр по компонентам -->
        <? endforeach ?>

        <? if ($brandFilter1): ?>
            <!-- бренды -->
            <div class="fltrBtn_kit fltrBtn_kit-brands fltrBtn_kit--mark js-category-v2-filter-otherBrands <?= $hasSelectedOtherBrands ? 'open' : '' ?>">
                <div class="fltrBtn_tggl fltrBtn_kit_l js-category-v2-filter-otherBrandsOpener <?= $brandFilter2->getOption() ? 'icon-corner' : 'without-opener' ?>">
                    <span class="dotted"><?= $brandFilter1->getName() ?></span>
                </div>

                <!-- список брендов -->
                <div class="fltrBtn_kit_r">

                    <?= $helper->render('category/filters/_brand', ['productFilter' => $productFilter, 'filter' => $brandFilter1]) ?>

                    <? if ($brandFilter2 && count($brandFilter2->getOption())): ?>
                        <a href="#" class="fltrBtn_btn fltrBtn_btn-btn fltrBtn_kit-brands__btn-more js-category-v2-filter-otherBrandsOpener">
                            <span class="dotted">Ещё <?= count($brandFilter2->getOption()) ?></span>
                        </a>
                    <? endif ?>

                    <? if ($brandFilter2): ?>
                    <!-- больше брендов -->
                    <span class="fltrBtn_kit-brands__more js-category-v2-filter-otherBrands">
                        <?= $helper->render('category/filters/_brand', ['productFilter' => $productFilter, 'filter' => $brandFilter2]) ?>
                    </span>
                    <!--/ больше брендов -->
                    <? endif ?>
                </div>
                <!--/ список брендов -->
            </div>
            <!--/ бренды -->
        <? endif ?>

        <? if ($priceFilter || ($labelFilter && $labelFilter->getOption())): ?>
            <div class="fltrBtn_kit fltrBtn_kit-box clearfix">
                <? if ($priceFilter): ?>
                    <div class="fltrBtnBox fl-l js-category-v2-filter-dropBox js-category-v2-filter-dropBox-price">
                        <div class="fltrBtnBox_tggl icon-corder js-category-v2-filter-dropBox-opener">
                            <span class="dotted"><?= $priceFilter->getName() ?></span>
                        </div>

                        <div class="fltrBtnBox_dd fltrBtnBox_dd-l">
                            <ul class="fltrBtnBox_dd_inn js-category-v2-filter-dropBox-content">
                                <? foreach ($priceFilter->getPriceRanges() as $range): ?>
                                    <li class="lstdotted_i">
                                        <a class="lstdotted_lk dotted js-category-v2-filter-price-link" href="<?= $helper->escape($range['url']) ?>">
                                            <? if (isset($range['from'])): ?>
                                                <span class="txmark1">от</span> <?= $helper->formatPrice($range['from']) ?>
                                            <? endif ?>

                                            <? if (isset($range['to'])): ?>
                                                <span class="txmark1">до</span> <?= $helper->formatPrice($range['to']) ?>
                                            <? endif ?>
                                        </a>
                                    </li>
                                <? endforeach ?>
                            </ul>
                        </div>
                    </div>

                    <? if ($labelFilter && $labelFilter->getOption()): ?>
                        <div class="fltrBtnBox fltrBtnBox-mark fl-r js-category-v2-filter-dropBox js-category-v2-filter-dropBox-labels">
                            <div class="fltrBtnBox_tggl icon-corder js-category-v2-filter-dropBox-opener">
                                <span class="dotted"><?= $labelFilter->getName() ?></span>
                            </div>

                            <div class="fltrBtnBox_dd fltrBtnBox_dd-r js-category-v2-filter-dropBox-content">
                                <div class="fltrBtnBox_dd_inn">
                                    <?= $helper->render('category/filters/_list', ['productFilter' => $productFilter, 'filter' => $labelFilter]) ?>
                                </div>
                            </div>
                        </div>
                    <? endif ?>

                    <div class="fltrBtn_range"><?= $helper->render('category/filters/_slider', ['productFilter' => $productFilter, 'filter' => $priceFilter]) ?></div>
                <? endif ?>
            </div>
        <? endif ?>

        <? if ($productFilter->hasInListGroupedProperties()): ?>
            <div class="fltrBtn_kit fltrBtn_kit-box js-category-v2-filter-otherGroups">
                <? foreach ($productFilter->getGroupedPropertiesV2() as $group): ?>
                    <? if ($group->hasInListProperties()): ?>
                        <div class="fltrBtnBox <? if ($group->hasSelectedProperties): ?>actv<? endif ?> js-category-v2-filter-dropBox">
                            <div class="fltrBtnBox_tggl icon-corder js-category-v2-filter-dropBox-opener">
                                <span class="dotted"><?= $group->name ?></span>
                            </div>

                            <div class="fltrBtnBox_dd js-category-v2-filter-dropBox-content">
                                <div class="fltrBtnBox_dd_inn">
                                    <? foreach ($group->properties as $property): ?>
                                        <? if ($property->getIsInList()): ?>
                                            <div class="fltrBtn_param"> <!--fltrBtn_param-2col-->
                                                <? if (count($group->properties) > 1 || $property->getName() !== $group->name): ?>
                                                    <div class="fltrBtn_param_n"><?= $property->getName() ?></div>
                                                <? endif ?>

                                                <?= $helper->render('category/filters/__element', ['productFilter' => $productFilter, 'filter' => $property]) ?>
                                            </div>
                                        <? endif ?>
                                    <? endforeach ?>
                                </div>
                            </div>
                        </div>
                    <? endif ?>
                <? endforeach ?>
            </div>
        <? endif ?>

        <div class="fltrBtn_kit fltrBtn_kit-nborder">
            <div class="js-category-filter-selected">
                <?= $helper->render('category/filters/selected.filters', ['productFilter' => $productFilter, 'baseUrl' => $baseUrl]) ?>
            </div>
        </div>

    </form>
</div>
<!-- фильтр "Бренды и параметры" -->

