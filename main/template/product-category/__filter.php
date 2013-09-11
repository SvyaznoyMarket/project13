<?php

return function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Category\Entity $category,
    \Model\Product\Filter $productFilter
) {
    /** @var $filters \Model\Product\Filter\Entity[] */
    $filters = [];
    $priceFilter = null;
    foreach ($productFilter->getFilterCollection() as $filter) {
        if ($filter->isPrice()) {
            $priceFilter = $filter;
        } else {
            $filters[] = $filter;
        }
    }

?>

    <div class="bFilter clearfix">
        <div class="bFilterHead">
            <a class="bFilterToggle mOpen" href=""><span class="bToggleText">Бренды и параметры</span></a>

            <?= $helper->render('product-category/filter/__price', ['productFilter' => $productFilter, 'filter' => $priceFilter]) ?>
        </div>

        <!-- Фильтр по выбранным параметрам -->
        <div class="bFilterCont clearfix">
            <!-- Список названий параметров -->
            <ul class="bFilterParams">
            <? $i = 0; foreach ($filters as $filter): ?>
            <?
                $viewId = \View\Id::productCategoryFilter($filter->getTypeId() . '-' .$filter->getId());
            ?>
                <li class="bFilterParams__eItem<? if (0 == $i): ?> mActive<? endif ?>" data-ref="<?= $viewId ?>">
                    <span class="bParamName"><?= $filter->getName() ?></span>
                </li>
            <? $i++; endforeach ?>
            </ul>
            <!-- /Список названий параметров -->

            <!-- Список значений параметров -->
            <div class="bFilterValues">
                <? $i = 0; foreach ($filters as $filter): ?>
                <?
                    $viewId = \View\Id::productCategoryFilter($filter->getTypeId() . '-' .$filter->getId());
                ?>
                    <div class="bFilterValuesItem clearfix" id="<?= $viewId ?>">

                    <? switch ($filter->getTypeId()) {
                        case \Model\Product\Filter\Entity::TYPE_NUMBER:
                        case \Model\Product\Filter\Entity::TYPE_SLIDER:
                            echo $helper->render('product-category/filter/__slider', ['productFilter' => $productFilter, 'filter' => $filter]);
                            break;
                        case \Model\Product\Filter\Entity::TYPE_LIST:
                            echo $helper->render('product-category/filter/__list', ['productFilter' => $productFilter, 'filter' => $filter]);
                            break;
                        case \Model\Product\Filter\Entity::TYPE_BOOLEAN:
                            //echo $helper->render('product-category/filter/__choice', ['productFilter' => $productFilter, 'filter' => $filter]);
                            break;
                    } ?>

                    </div>
                <? $i++; endforeach ?>

                <div class="bBtnPick clearfix"><a class="bBtnPick__eLink mBtnGrey" href="">Подобрать</a></div>
            </div>
            <!-- /Список значений параметров -->
        </div>
        <!-- /Фильтр по выбранным параметрам -->

        <!-- Списоки выбранных параметров -->
        <div class="bFilterFoot">
            <ul class="bFilterCheckedParams clearfix">
                <li class="bFilterCheckedParams__eItem mTitle">Цена</li>

                <li class="bFilterCheckedParams__eItem mParams"><a class="bDelete" href=""></a><span class="bParamsName">от 2 000p</span></li>

                <li class="bFilterCheckedParams__eItem mParams"><a class="bDelete" href=""></a><span class="bParamsName">до 1 000 000p</span></li>
            </ul>

            <ul class="bFilterCheckedParams clearfix mLast">
                <li class="bFilterCheckedParams__eItem mTitle">Бренд</li>

                <li class="bFilterCheckedParams__eItem mParams"><a class="bDelete" href=""></a><span class="bParamsName">Ahava</span></li>

                <li class="bFilterCheckedParams__eItem mParams"><a class="bDelete" href=""></a><span class="bParamsName">Bubchen</span></li>

                <li class="bFilterCheckedParams__eItem mParams"><a class="bDelete" href=""></a><span class="bParamsName">Агентство старинных развлечений "Работорцы"</span></li>

                <li class="bFilterCheckedParams__eItem mParams mClearAll"><a class="bDelete" href=""><strong class="bParamsName">Очистить все</strong></a></li> <!-- Добаялется только в списке идущем по очереди последним -->
            </ul>
        </div>
        <!-- /Списоки выбранных параметров -->
    </div>



    <? if (false): ?>
        <!-- Фильтр товаров -->
        <div class="bFilter clearfix">
            <div class="bFilterHead">
                <a class="bFilterToggle mClose" href=""><span class="bToggleText">Бренды и параметры</span></a>

                <!-- Фильтр по цене -->
                <div class="bFilterPrice">
                    <span class="bFilterPrice__eTitle">Цена</span>
                    <input class="bFilterPrice__eInput" name="" value="1 000" type="text"  />

                    <div class="bFilterSlider">
                        <div class="ui-slider-range ui-widget-header ui-corner-all" style="left: 0%; width: 50%;"></div>
                        <a class="ui-slider-handle ui-state-default ui-corner-all" href="#" style="left: -14px;"></a>
                        <a class="ui-slider-handle ui-state-default ui-corner-all" href="#" style="left: 50%;"></a>
                    </div>

                    <input class="bFilterPrice__eInput mLast" name="" value="10 000" type="text"  />

                    <span class="bFilterPrice__eRub rubl">p</span>
                </div>
                <!-- /Фильтр по цене -->

                <!-- Фильтр по популярным позициям -->
                <ul class="bPopularSection">
                    <li class="bPopularSection__eItem mTitle">Популярные бренды</li>
                    <li class="bPopularSection__eItem"><strong class="bPopularSection__eText">Samsung</strong></li>
                    <li class="bPopularSection__eItem"><strong class="bPopularSection__eText">Nokia</strong></li>
                    <li class="bPopularSection__eItem"><strong class="bPopularSection__eText">Roga und Koppentenganger</strong></li>
                    <li class="bPopularSection__eItem"><strong class="bPopularSection__eText">Dr. Buchman</strong></li>
                    <li class="bPopularSection__eItem"><strong class="bPopularSection__eText"></strong></li>
                </ul>
                <!-- /Фильтр по популярным позициям -->
            </div>
        </div>
        <!-- Фильтр товаров -->
    <? endif ?>

<? };