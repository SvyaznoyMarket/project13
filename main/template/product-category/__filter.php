<?php

return function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Filter $productFilter,
    \Model\Product\Category\Entity $category
) {
    /** @var $filters \Model\Product\Filter\Entity[] */
    $filters = [];
    $priceFilter = null;
    foreach ($productFilter->getFilterCollection() as $filter) {
        if ($filter->isPrice()) {
            $priceFilter = $filter;
            $priceFilter->setStepType('price');
        } else {
            $filters[] = $filter;
        }
    }

?>

    <form class="bFilter clearfix" action="<?= $helper->url('product.category', ['categoryPath' => $category->getPath()]) ?>" method="GET">
        <div class="bFilterHead">
            <a class="bFilterToggle mOpen" href="#"><span class="bToggleText">Бренды и параметры</span></a>

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
                    <div class="bFilterValuesItem clearfix<? if ($i > 0): ?> hf<? endif ?>" id="<?= $viewId ?>">

                    <? switch ($filter->getTypeId()) {
                        case \Model\Product\Filter\Entity::TYPE_NUMBER:
                        case \Model\Product\Filter\Entity::TYPE_SLIDER:
                            echo $helper->render('product-category/filter/__slider', ['productFilter' => $productFilter, 'filter' => $filter]);
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

                <div class="bBtnPick clearfix">
                    <button type="submit" class="bBtnPick__eLink mBtnGrey">Подобрать</button>
                </div>
            </div>
            <!-- /Список значений параметров -->
        </div>
        <!-- /Фильтр по выбранным параметрам -->

        <?= $helper->render('product-category/__selectedFilter', ['productFilter' => $productFilter, 'baseUrl' => $helper->url('product.category', ['categoryPath' => $category->getPath()])]) ?>
    </form>



    <? if (false): ?>
        <!-- Фильтр товаров -->
        <div class="bFilter clearfix">
            <div class="bFilterHead">
                <a class="bFilterToggle mClose" href=""><span class="bToggleText">Бренды и параметры</span></a>

                <!-- Фильтр по цене -->
                <div class="bRangeSlider">
                    <span class="bRangeSlider__eTitle">Цена</span>
                    <input class="bRangeSlider__eInput mFromRange" name="" value="1 000" type="text"  />

                    <div class="bFilterSlider" data-config="{&quot;min&quot;:2990,&quot;max&quot;:113990,&quot;step&quot;:0.1}">
                        <a class="ui-slider-handle ui-state-default ui-corner-all" href="#"></a>
                        <a class="ui-slider-handle ui-state-default ui-corner-all" href="#"></a>
                    </div>

                    <input class="bRangeSlider__eInput mLast mToRange" name="" value="10 000" type="text"  />

                    <span class="bRangeSlider__eRub rubl">p</span>
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