<?php
/**
 * @param \Model\Product\Category\Entity[] $categories
 */
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
    /** @var \Model\Product\Filter\Entity $shopFilter */
    $shopFilter = null;
    /** @var \Model\Product\Filter\Entity $widthFilter */
    $brandFilter1 = null;
    /** @var \Model\Product\Filter\Entity $brandFilter2 */
    $brandFilter2 = null;
    /** @var array $groups */
    $groups = [];
    $additionalGroup = null;

    $countInListFilters = 0;
    foreach ($productFilter->getFilterCollection() as $filter) {
        if (!$filter->getIsInList()) {
            continue;
        } else if ($filter->isPrice()) {
            $priceFilter = $filter;
            $priceFilter->setStepType('price');
        } else if ($filter->isLabel()) {
            $labelFilter = $filter;
        } else if ($filter->isShop()) {
            $shopFilter = $filter;
        } else if ($filter->isBrand()) {
            $brandFilter1 = clone $filter;

            $brandFilter2 = clone $filter;
            $brandFilter2->deleteAllOptions();

            while (true) {
                if (count($brandFilter1->getOption()) < 9) {
                    break;
                }

                $brandFilter2->unshiftOption($brandFilter1->deleteLastOption());
            }
        } else if ($filter->groupUi) {
            if (!isset($groups[$filter->groupUi])) {
                $groups[$filter->groupUi] = ['name' => $filter->groupName, 'properties' => [], 'hasSelectedProperties' => false];

                if ('Дополнительно' === $filter->groupName) {
                    $additionalGroup = &$groups[$filter->groupUi];
                }
            }

            $groups[$filter->groupUi]['properties'][] = $filter;
            if ($productFilter->getValue($filter)) {
                $groups[$filter->groupUi]['hasSelectedProperties'] = true;
            }
        }

        $countInListFilters++;
    }

    if ($shopFilter) {
        array_unshift($groups, ['name' => $shopFilter->getName(), 'properties' => [$shopFilter], 'hasSelectedProperties' => (bool)$productFilter->getValue($shopFilter)]);
    }

    if ($labelFilter) {
        foreach ($labelFilter->getOption() as $key => $option) {
            if ('instore' === $option->getToken()) {
                $property = new \Model\Product\Filter\Entity();
                $property->setId($option->getToken());
                $property->setName($option->getName());
                $property->setTypeId(\Model\Product\Filter\Entity::TYPE_BOOLEAN);

                array_unshift($additionalGroup['properties'], $property);
                $labelFilter->deleteOption($key);

                break;
            }
        }
    }

    if (0 == $countInListFilters) {
        return;
    }
    ?>

    <div class="fltrBtn fltrBtn-bt">
        <form id="productCatalog-filter-form" class="js-category-filter" action="<?= $baseUrl ?>" data-count-url="<?= $countUrl ?>" method="GET">
            <? if ($brandFilter1): ?>
                <div class="fltrBtn_kit clearfix">
                    <div class="fltrBtn_tggl fltrBtn_kit_l <? if ($brandFilter2 && count($brandFilter2->getOption())): ?>js-category-v2-filter-brandTitle<? endif ?>">
                        <span class="fltrBtn_tggl_tx"><?= $brandFilter1->getName() ?></span>
                    </div>

                    <div class="fltrBtn_kit_r">
                        <?= $helper->render('product-category/v2/filter/element/__brand', ['productFilter' => $productFilter, 'filter' => $brandFilter1]) ?>
                        <? if ($brandFilter2 && count($brandFilter2->getOption())): ?>
                            <a href="#" class="fltrBtn_btn fltrBtn_btn-mini fltrBtn_btn-btn js-category-v2-filter-otherBrandsOpener"><span class="fltrBtn_btn_tx">Ещё <?= count($brandFilter2->getOption()) ?></span></a>
                        <? endif ?>

                        <? if ($brandFilter2): ?>
                            <span class="js-category-v2-filter-otherBrands" style="display: none;">
                            <?= $helper->render('product-category/v2/filter/element/__brand', ['productFilter' => $productFilter, 'filter' => $brandFilter2]) ?>
                        </span>
                        <? endif ?>
                    </div>
                </div>
            <? endif ?>

            <? if ($priceFilter || ($labelFilter && $labelFilter->getOption())): ?>
                <div class="fltrBtn_kit fltrBtn_kit-box clearfix">
                    <? if ($priceFilter): ?>
                        <div class="fltrBtnBox fl-l js-category-v2-filter-dropBox">
                            <div class="fltrBtnBox_tggl js-category-v2-filter-dropBox-opener">
                                <span class="fltrBtnBox_tggl_tx"><?= $priceFilter->getName() ?></span>
                            </div>
                            <div class="fltrBtnBox_dd fltrBtnBox_dd-l ">
                                <ul class="fltrBtnBox_dd_inn lstdotted js-category-v2-filter-dropBox-content">
                                    <? foreach ($priceFilter->getPriceRanges() as $range): ?>
                                        <li class="lstdotted_i">
                                            <a class="lstdotted_lk js-category-v2-filter-price-link" href="<?= $helper->escape($range['url']) ?>">
                                                <? if (isset($range['from'])): ?>
                                                    <span class="txmark1">от</span> <?= $helper->escape($range['from']) ?>
                                                <? endif ?>

                                                <? if (isset($range['to'])): ?>
                                                    <span class="txmark1">до</span> <?= $helper->escape($range['to']) ?>
                                                <? endif ?>
                                            </a>
                                        </li>
                                    <? endforeach ?>
                                </ul>
                            </div>
                        </div>

                        <div class="fltrBtn_range fl-l"><?= $helper->render('product-category/v2/filter/element/__slider', ['productFilter' => $productFilter, 'filter' => $priceFilter]) ?></div>
                    <? endif ?>

                    <? if ($labelFilter && $labelFilter->getOption()): ?>
                        <div class="fltrBtnBox fltrBtnBox-mark fl-r js-category-v2-filter-dropBox">
                            <div class="fltrBtnBox_tggl js-category-v2-filter-dropBox-opener">
                                <span class="fltrBtnBox_tggl_tx"><?= $labelFilter->getName() ?></span>
                            </div>

                            <div class="fltrBtnBox_dd fltrBtnBox_dd-r js-category-v2-filter-dropBox-content">
                                <div class="fltrBtnBox_dd_inn">
                                    <?= $helper->render('product-category/v2/filter/element/__list', ['productFilter' => $productFilter, 'filter' => $labelFilter]) ?>
                                </div>
                            </div>
                        </div>
                    <? endif ?>
                </div>
            <? endif ?>

            <? if (count($groups)): ?>
                <div class="fltrBtn_kit fltrBtn_kit-box">
                    <? foreach ($groups as $group): ?>
                        <div class="fltrBtnBox <? if ($group['hasSelectedProperties']): ?>actv<? endif ?> js-category-v2-filter-dropBox">
                            <div class="fltrBtnBox_tggl js-category-v2-filter-dropBox-opener">
                                <span class="fltrBtnBox_tggl_tx"><?= $group['name'] ?></span>
                            </div>

                            <div class="fltrBtnBox_dd js-category-v2-filter-dropBox-content">
                                <div class="fltrBtnBox_dd_inn">
                                    <? foreach ($group['properties'] as $property): ?>
                                        <? /** @var \Model\Product\Filter\Entity $property */?>
                                        <div class="fltrBtn_param"> <!--fltrBtn_param-2col-->
                                            <? if ('shop' !== $property->getId()): ?>
                                                <div class="fltrBtn_param_n"><?= $property->getName() ?></div>
                                            <? endif ?>

                                            <?= $helper->render('product-category/v2/filter/__element', ['productFilter' => $productFilter, 'filter' => $property]) ?>
                                        </div>
                                    <? endforeach ?>
                                </div>
                            </div>
                        </div>
                    <? endforeach ?>
                </div>
            <? endif ?>

            <div class="fltrBtn_kit fltrBtn_kit-nborder">
                <?= $helper->render('product-category/v2/__selectedFilter', ['productFilter' => $productFilter, 'baseUrl' => $baseUrl]) ?>
            </div>
        </form>
    </div>

<? };