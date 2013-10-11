<?php

return function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Filter $productFilter,
    $baseUrl,
    $countUrl,
    $hotlinks
) {
    /** @var $filters \Model\Product\Filter\Entity[] */
    $openFilter = false;
    $filters = [];
    $priceFilter = null;

    $i = 1;
    foreach ($productFilter->getFilterCollection() as $filter) {
        if ($filter->isPrice()) {
            $priceFilter = $filter;
            $priceFilter->setStepType('price');
        } else {
            $filters[] = $filter;
            $i++;
        }

        // фильтр "Наличие в магазинах"
        if (3 == $i) {
            /** @var $shops \Model\Shop\Entity[] */
            $shops = $helper->getParam('shops');

            $shopFilter = new \Model\Product\Filter\Entity();
            $shopFilter->setId('shop');
            $shopFilter->setTypeId(\Model\Product\Filter\Entity::TYPE_LIST);
            $shopFilter->setName('Наличие в магазинах');
            $shopFilter->getIsInList(true);
            $shopFilter->setIsMultiple(false);

            foreach ($shops as $shop) {
                $option = new \Model\Product\Filter\Option\Entity();
                $option->setId($shop->getId());
                $option->setName($shop->getName());
                $shopFilter->addOption($option);
            }
            $filters[] = $shopFilter;
            $i++;
        }
    }

?>

    <form class="bFilter clearfix" action="<?= $baseUrl ?>" data-count-url="<?= $countUrl ?>" method="GET">
        <div class="bFilterHead">
            <a class="bFilterToggle <?= ($openFilter) ? 'mOpen' : 'mClose'?>" href="#"><span class="bToggleText">Бренды и параметры</span></a>

            <? if ( $priceFilter && $productFilter ) {
                /**@var     $productFilter      \Model\Product\Filter
                 **@var     $priceFilter        \Model\Product\Filter\Entity **/
                echo $helper->render('product-category/filter/__slider', ['productFilter' => $productFilter, 'filter' => $priceFilter]);
            } ?>

            <div class="bBtnPick clearfix">
                <button type="submit" class="bBtnPick__eLink mBtnGrey">Подобрать</button>
            </div>

            <!-- SEO теги -->
            <? if(!empty($hotlinks)): ?>
                <ul class="bPopularSection">
                    <? foreach ($hotlinks as $hotlink): ?>
                        <li class="bPopularSection__eItem"><a class="bPopularSection__eText" href="<?= $hotlink['url'] ?>"><?= $hotlink['title'] ?></a></li>
                    <? endforeach ?>
                </ul>
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
                    if (!$filter->getIsInList()) continue;
                    $viewId = \View\Id::productCategoryFilter($filter->getTypeId() . '-' . $filter->getId());
                ?>
                    <div class="bFilterValuesItem clearfix<? if ($i > 0): ?> hf<? endif ?><? if ('shop' == $filter->getId()): ?> mLineItem<? endif ?>" id="<?= $viewId ?>">

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
            </div>
            <!-- /Список значений параметров -->
        </div>
        <!-- /Фильтр по выбранным параметрам -->

        <?= $helper->render('product-category/__selectedFilter', ['productFilter' => $productFilter, 'baseUrl' => $baseUrl]) ?>
    </form>

<? };