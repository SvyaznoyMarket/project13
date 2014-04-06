<?php

return function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Filter $productFilter,
    $baseUrl,
    $countUrl,
    $hotlinks,
    $openFilter,
    array $promoStyle = [],
    array $categories = [],
    \Model\Product\Category\Entity $selectedCategory = null
) {
    /**
     * @var $filters    \Model\Product\Filter\Entity[]
     * @var $categories \Model\Product\Category\Entity[]
     */

    $filters = [];
    $priceFilter = null;

    $insertCustomFilters = function() use (&$categories, &$filters) {
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

            $filters[] = $categoryFilter;
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
            if ($filter->isPrice()) {
                $priceFilter = $filter;
                $priceFilter->setStepType('price');
            } else {
                $filters[] = $filter;
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

    $showParamsButton = (bool) ($countInListFilters > 1 || !$priceFilter);

?>
    <form id="productCatalog-filter-form" class="bFilter clearfix" action="<?= $baseUrl ?>" data-count-url="<?= $countUrl ?>" method="GET">
        <div class="bFilterHead"<? if(!empty($promoStyle['bFilterHead'])): ?> style="<?= $promoStyle['bFilterHead'] ?>"<? endif ?>>
            <? if ($showParamsButton): ?>
                <a class="bFilterToggle btnGrey <?= ($openFilter) ? 'mOpen' : 'mClose'?>" href="#"><span class="bToggleText">Бренды и параметры</span></a>
            <? endif ?>

            <? if ($priceFilter && $productFilter) {
                /**@var     $productFilter      \Model\Product\Filter
                 **@var     $priceFilter        \Model\Product\Filter\Entity **/
                echo $helper->render('product-category/filter/__slider', ['productFilter' => $productFilter, 'filter' => $priceFilter, 'promoStyle' => $promoStyle]);
            } ?>

            <div class="bBtnPick clearfix">
                <button type="submit" class="bBtnPick__eLink mBtnGrey">Подобрать</button>
            </div>

            <!-- SEO теги -->
            <? if(!empty($hotlinks)): ?>
                <?
                $hotlinksGroups = [];
                foreach ($hotlinks as $hotlink) {
                    $hotlinksGroups[$hotlink['group_name']][] = $hotlink;
                } ?>

                <? foreach($hotlinksGroups as $groupName => $group): ?>
                    <?= !empty($groupName) ? "<br><b>$groupName:</b> " : '' ?>
                    <ul class="bPopularSection"<? if(!empty($promoStyle['bPopularSection'])): ?> style="<?= $promoStyle['bPopularSection'] ?>"<? endif ?>>
                        <? foreach ($group as $hotlink): ?>
                            <li class="bPopularSection__eItem"><a class="bPopularSection__eText" href="<?= $hotlink['url'] ?>"><?= $hotlink['title'] ?></a></li>
                        <? endforeach ?>
                    </ul>
                <? endforeach ?>
            <? endif ?>
            <!-- SEO теги -->
        </div>

        <!-- Фильтр по выбранным параметрам -->
        <div class="bFilterCont clearfix" <? if (!$openFilter): ?>style="display: none"<? endif ?>>
            <!-- Список названий параметров -->
            <ul class="bFilterParams">
            <? $i = 0; foreach ($filters as $filter): ?>
            <?
                if (!$filter->getIsInList()) continue;
                $viewId = \View\Id::productCategoryFilter($filter->getTypeId() . '-' . $filter->getId());

                $hideFilter = false;
                $disabledFilters = $helper->getParam('disabledFilters');
                if (\App::config()->sphinx['showFacets']) {
                    switch ($filter->getTypeId()) {
                        case \Model\Product\Filter\Entity::TYPE_NUMBER:
                        case \Model\Product\Filter\Entity::TYPE_SLIDER:
//                            $disabledFilters = isset($disabledFilters['slider']) ? $disabledFilters['slider'] : null;
//                            if ($disabledFilters) {
//                                $hideFilter = true;
//                                $nameFrom = \View\Name::productCategoryFilter($filter, 'from');
//                                $nameTo = \View\Name::productCategoryFilter($filter, 'to');
//                                if (!in_array($nameFrom, array_keys($disabledFilters)) && !in_array($nameTo, array_keys($disabledFilters))) {
//                                    $hideFilter = false;
//                                }
//                            }
                            break;
                        case \Model\Product\Filter\Entity::TYPE_LIST:
                            $disabledFilters = isset($disabledFilters['list']) ? $disabledFilters['list'] : null;
                            if ($disabledFilters) {
                                $hideFilter = true;
                                foreach ($filter->getOption() as $option) {
                                    $name = \View\Name::productCategoryFilter($filter, $option);
                                    if (
                                        'shop' === $filter->getId() && isset($disabledFilters[$filter->getId()]) &&
                                        !in_array($option->getId(), $disabledFilters[$filter->getId()])
                                    ) {
                                        $hideFilter = false;
                                    } elseif (!in_array($name, array_keys($disabledFilters))) {
                                        $hideFilter = false;
                                    }
                                }
                            }
                            break;
                        case \Model\Product\Filter\Entity::TYPE_BOOLEAN:
                            $disabledFilters = isset($disabledFilters['choice']) ? $disabledFilters['choice'] : null;
                            if ($disabledFilters) {
                                $hideFilter = true;
                                foreach ([1 => 'да', 0 => 'нет'] as $value => $name) {
                                    $inputName = \View\Name::productCategoryFilter($filter, $value);
                                    if (!in_array($inputName, array_keys($disabledFilters))) {
                                        $hideFilter = false;
                                    }
                                }
                            }
                            break;
                    }
                }
            ?>
                <li class="bFilterParams__eItem<? if (0 == $i): ?> mActive<? endif ?>" data-ref="<?= $viewId ?>"<? if ($hideFilter): ?> style="display:none;"<? endif ?>>
                    <span class="bParamName"><?= $filter->getName() ?></span>
                </li>
            <? $i++; endforeach ?>
            </ul>
            <!-- /Список названий параметров -->

            <!-- Список значений параметров -->
            <div class="bFilterValues">
                <? $i = 0; foreach ($filters as $filter): ?>
                <?
                    if (!$filter->getIsInList()) continue;
                    $viewId = \View\Id::productCategoryFilter($filter->getTypeId() . '-' . $filter->getId());
                ?>
                    <div class="bFilterValuesItem clearfix<? if ($i > 0): ?> hf<? endif ?><? if (in_array($filter->getId(), ['shop', 'category'])): ?> mLineItem<? endif ?>" id="<?= $viewId ?>">

                    <? switch ($filter->getTypeId()) {
                        case \Model\Product\Filter\Entity::TYPE_NUMBER:
                        case \Model\Product\Filter\Entity::TYPE_SLIDER:
                            echo $helper->render('product-category/filter/__slider', ['productFilter' => $productFilter, 'filter' => $filter, 'promoStyle' => $promoStyle]);
                            break;
                        case \Model\Product\Filter\Entity::TYPE_LIST:
                            echo $helper->render('product-category/filter/__list', ['productFilter' => $productFilter, 'filter' => $filter]);
                            break;
                        case \Model\Product\Filter\Entity::TYPE_BOOLEAN:
                            echo $helper->render('product-category/filter/__choice', ['productFilter' => $productFilter, 'filter' => $filter]);
                            break;
                    } ?>

                    </div>
                <? $i++; endforeach ?>
            </div>
            <!-- /Список значений параметров -->
        </div>
        <!-- /Фильтр по выбранным параметрам -->

        <?= $helper->render('product-category/__selectedFilter', ['productFilter' => $productFilter, 'baseUrl' => $baseUrl]) ?>
    </form>

<? };