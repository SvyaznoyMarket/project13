<?
return function(\Model\Product\Filter $productFilter, $openFilter, $baseUrl) {
    $helper = \App::helper();
    $visibleBrandsCount = 12;

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
                usort($option, function(\Model\Product\Filter\Option\Entity $a, \Model\Product\Filter\Option\Entity $b){ return $a->getName() > $b->getName(); });
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
            <? if ($brandFilter1): ?>
                <!-- бренды -->
                <div class="fltrBtn_kit fltrBtn_kit-brands fltrBtn_kit--mark js-category-filter-otherBrands <?= $hasSelectedOtherBrands ? 'open' : '' ?>">
                    <div class="fltrBtn_tggl fltrBtn_kit_l js-category-filter-otherBrandsOpener <?= $brandFilter2->getOption() ? 'icon-corner' : 'without-opener' ?>">
                        <span class="dotted"><?= $brandFilter1->getName() ?></span>
                    </div>

                    <!-- список брендов -->
                    <div class="fltrBtn_kit_r">

                        <?= $helper->render('category/filters/v2/elements/_brand', ['productFilter' => $productFilter, 'filter' => $brandFilter1]) ?>

                        <? if ($brandFilter2 && count($brandFilter2->getOption())): ?>
                            <a href="#" class="fltrBtn_btn fltrBtn_btn-btn fltrBtn_kit-brands__btn-more js-category-filter-otherBrandsOpener">
                                <span class="dotted">Ещё <?= count($brandFilter2->getOption()) ?></span>
                            </a>
                        <? endif ?>

                        <? if ($brandFilter2): ?>
                            <!-- больше брендов -->
                            <span class="fltrBtn_kit-brands__more js-category-filter-otherBrands">
                        <?= $helper->render('category/filters/v2/elements/_brand', ['productFilter' => $productFilter, 'filter' => $brandFilter2]) ?>
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
                        <?= $helper->render('category/filters/priceDropBox', ['productFilter' => $productFilter, 'filter' => $priceFilter]) ?>

                        <? if ($labelFilter && $labelFilter->getOption()): ?>
                            <div class="fltrBtnBox fltrBtnBox-mark fl-r js-category-filter-dropBox">
                                <div class="fltrBtnBox_tggl icon-corder js-category-filter-dropBox-opener">
                                    <span class="dotted"><?= $labelFilter->getName() ?></span>
                                </div>

                                <div class="fltrBtnBox_dd fltrBtnBox_dd-r js-category-filter-dropBox-content">
                                    <div class="fltrBtnBox_dd_inn">
                                        <?= $helper->render('category/filters/v2/elements/_list', ['productFilter' => $productFilter, 'filter' => $labelFilter]) ?>
                                    </div>
                                </div>
                            </div>
                        <? endif ?>

                        <div class="fltrBtn_range"><?= $helper->render('category/filters/v2/elements/_slider', ['productFilter' => $productFilter, 'filter' => $priceFilter]) ?></div>
                    <? endif ?>
                </div>
            <? endif ?>

            <? if ($productFilter->hasInListGroupedProperties()): ?>
                <div class="fltrBtn_kit fltrBtn_kit-box js-category-filter-otherGroups">
                    <? foreach ($productFilter->getGroupedPropertiesV2() as $group): ?>
                        <? if ($group->hasInListProperties()): ?>
                            <div class="fltrBtnBox <? if ($group->hasSelectedProperties): ?>actv<? endif ?> js-category-filter-dropBox">
                                <div class="fltrBtnBox_tggl icon-corder js-category-filter-dropBox-opener">
                                    <span class="dotted"><?= $group->name ?></span>
                                </div>

                                <div class="fltrBtnBox_dd js-category-filter-dropBox-content">
                                    <div class="fltrBtnBox_dd_inn">
                                        <? foreach ($group->properties as $property): ?>
                                            <? if ($property->getIsInList()): ?>
                                                <div class="fltrBtn_param"> <!--fltrBtn_param-2col-->
                                                    <? if (count($group->properties) > 1 || $property->getName() !== $group->name): ?>
                                                        <div class="fltrBtn_param_n"><?= $property->getName() ?></div>
                                                    <? endif ?>

                                                    <?= $helper->render('category/filters/v2/_element', ['productFilter' => $productFilter, 'filter' => $property]) ?>
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

            <?= $helper->render('category/filters/v2/selected.filters', ['productFilter' => $productFilter, 'baseUrl' => $baseUrl]) ?>
        </form>
    </div>
    <!-- фильтр "Бренды и параметры" -->

<?php }; ?>