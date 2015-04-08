<?php
return function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Filter $productFilter,
    $baseUrl,
    $countUrl
) {

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

            if (count($brandFilter1->getOption()) >= 10) {
                $values = $productFilter->getValue($property);
                while (count($brandFilter1->getOption()) >= 9) {
                    $option = $brandFilter1->deleteLastOption();
                    if (in_array($option->getId(), $values)) {
                        $hasSelectedOtherBrands = true;
                    }

                    $brandFilter2->unshiftOption($option);
                }
            }
        } else {
            $tyreFilters[$key] = $property;
        }

        $countInListFilters++;
    }

    if (0 == $countInListFilters) {
        return;
    }
    ?>

    <div class="fltrBtn fltrBtn-bt">
        <form id="productCatalog-filter-form" class="js-category-filter" action="<?= $baseUrl ?>" data-count-url="<?= $countUrl ?>" method="GET">
            <? // Для IE9 (чтобы он отправлял форму при нажатии на клавишу enter в текстовом поле ввода) ?>
            <div style="overflow: hidden; position: absolute; top: 0; left: 0; width: 0; height: 0;"><input type="submit" /></div>

            <? if ($tyreFilters): ?>
                <div class="fltrBtn_kit fltrBtn_kit--titled clearfix">

                    <div class="fltrBtn_tggl fltrBtn_kit_l fltrBtn_tggl-ncorner">
                        <span class="fltrBtn_tggl_tx">Параметры шин</span>
                    </div>
                    
                    <ul class="fltrBtn_lst">
                    <? foreach ($tyreFilters as $property): ?>
                        <li class="fltrBtn_lst-i <? if ($property->getName() === 'Ширина'): ?>slash-after<? endif ?>">
                            <div class="fltrBtn_lst-i-name"><?= $property->getName() ?></div>
                            <?= $helper->render('product-category/v2/filter/element/__dropBox2', ['productFilter' => $productFilter, 'property' => $property]) ?>
                        </li>
                    <? endforeach ?>
                    </ul>
                </div>
            <? endif ?>

            <? if ($brandFilter1): ?>
                <div class="fltrBtn_kit clearfix">
                    <div class="fltrBtn_tggl fltrBtn_kit_l <? if (!$brandFilter2 || !count($brandFilter2->getOption())): ?>fltrBtn_tggl-ncorner<? endif ?> <? if ($hasSelectedOtherBrands): ?>opn<? endif ?> <? if ($brandFilter2 && count($brandFilter2->getOption())): ?>js-category-v2-filter-brandTitle<? endif ?>">
                        <span class="fltrBtn_tggl_tx"><?= $brandFilter1->getName() ?></span>
                    </div>

                    <div class="fltrBtn_kit_r clearfix">
                        <?= $helper->render('product-category/v2/filter/element/__brand', ['productFilter' => $productFilter, 'filter' => $brandFilter1]) ?>
                        <? if ($brandFilter2 && count($brandFilter2->getOption())): ?>
                            <a href="#" class="fltrBtn_btn fltrBtn_btn-mini fltrBtn_btn-btn js-category-v2-filter-otherBrandsOpener" <? if ($hasSelectedOtherBrands): ?>style="display: none;"<? endif ?>><span class="fltrBtn_btn_tx">Ещё <?= count($brandFilter2->getOption()) ?></span></a>
                        <? endif ?>

                        <? if ($brandFilter2): ?>
                            <span class="js-category-v2-filter-otherBrands" <? if (!$hasSelectedOtherBrands): ?>style="display: none;"<? endif ?>>
                                <?= $helper->render('product-category/v2/filter/element/__brand', ['productFilter' => $productFilter, 'filter' => $brandFilter2]) ?>
                            </span>
                        <? endif ?>
                    </div>
                </div>
            <? endif ?>

            <? if ($priceFilter || ($labelFilter && $labelFilter->getOption())): ?>
                <div class="fltrBtn_kit fltrBtn_kit-box clearfix">
                    <? if ($priceFilter): ?>
                        <div class="fltrBtnBox fl-l js-category-v2-filter-dropBox js-category-v2-filter-dropBox-price">
                            <div class="fltrBtnBox_tggl js-category-v2-filter-dropBox-opener">
                                <span class="fltrBtnBox_tggl_tx"><?= $priceFilter->getName() ?></span>
                                <i class="fltrBtnBox_tggl_corner"></i>
                            </div>
                            <div class="fltrBtnBox_dd fltrBtnBox_dd-l">
                                <ul class="fltrBtnBox_dd_inn lstdotted js-category-v2-filter-dropBox-content">
                                    <? foreach ($priceFilter->getPriceRanges() as $range): ?>
                                        <li class="lstdotted_i">
                                            <a class="lstdotted_lk js-category-v2-filter-price-link" href="<?= $helper->escape($range['url']) ?>">
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
                                <div class="fltrBtnBox_tggl js-category-v2-filter-dropBox-opener">
                                    <span class="fltrBtnBox_tggl_tx"><?= $labelFilter->getName() ?></span>
                                    <i class="fltrBtnBox_tggl_corner"></i>
                                </div>

                                <div class="fltrBtnBox_dd fltrBtnBox_dd-r js-category-v2-filter-dropBox-content">
                                    <div class="fltrBtnBox_dd_inn">
                                        <?= $helper->render('product-category/v2/filter/element/__list', ['productFilter' => $productFilter, 'filter' => $labelFilter]) ?>
                                    </div>
                                </div>
                            </div>
                        <? endif ?>

                        <div class="fltrBtn_range"><?= $helper->render('product-category/v2/filter/element/__slider', ['productFilter' => $productFilter, 'filter' => $priceFilter]) ?></div>
                    <? endif ?>
                </div>
            <? endif ?>

            <? if ($productFilter->hasInListGroupedProperties()): ?>
                <div class="fltrBtn_kit fltrBtn_kit-box js-category-v2-filter-otherGroups">
                    <? foreach ($productFilter->getGroupedPropertiesV2() as $group): ?>
                        <? if ($group->hasInListProperties()): ?>
                            <div class="fltrBtnBox <? if ($group->hasSelectedProperties): ?>actv<? endif ?> js-category-v2-filter-dropBox">
                                <div class="fltrBtnBox_tggl js-category-v2-filter-dropBox-opener">
                                    <span class="fltrBtnBox_tggl_tx"><?= $group->name ?></span>
                                    <i class="fltrBtnBox_tggl_corner"></i>
                                </div>

                                <div class="fltrBtnBox_dd js-category-v2-filter-dropBox-content">
                                    <div class="fltrBtnBox_dd_inn">
                                        <? foreach ($group->properties as $property): ?>
                                            <? if ($property->getIsInList()): ?>
                                                <div class="fltrBtn_param"> <!--fltrBtn_param-2col-->
                                                    <? if (count($group->properties) > 1 || $property->getName() !== $group->name): ?>
                                                        <div class="fltrBtn_param_n"><?= $property->getName() ?></div>
                                                    <? endif ?>

                                                    <?= $helper->render('product-category/v2/filter/__element', ['productFilter' => $productFilter, 'filter' => $property]) ?>
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
                <?= $helper->render('product-category/v2/__selectedFilter', ['productFilter' => $productFilter, 'baseUrl' => $baseUrl]) ?>
            </div>
        </form>
    </div>

<? };