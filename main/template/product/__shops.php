<?php

return function (
    \Helper\TemplateHelper $helper,
    array $shopStates = [],
    \Model\Product\Entity $product
) {
    /** @var $shopStates \Model\Product\ShopState\Entity[]  */

    ?>

    <? if (count($shopStates) == 1) : $shop = $shopStates[0]->getShop(); ?>
        <div class="shopsVar">

            <span class="shopsVar_title"><?= $shopStates[0]->getQuantity() ? 'Есть в магазине' : 'Сегодня есть на витрине магазина' ?></span>

            <div class="markerList markerList-left">
                <? if ((bool)$shop->getSubway()) : ?>
                    <!--  Метро  -->
                    <i class="markColor" style="background-color: <?= $shop->getSubway()[0]->getLine()->getColor() ?>"></i>
                    <span class="markDesc">м. <?= $shop->getSubway()[0]->getName(); ?></span>
                <? endif; ?>

                <!--  Адрес  -->
                <a class="markerList_light td-underl" target="_blank" href="<?= $helper->url('shop.show', ['regionToken' => \App::user()->getRegion()->getToken(), 'shopToken' => $shop->getToken()]) ?>"><?= $shop->getAddress() ?></a>
                
                <!--  Время работы  -->
                <div class="ta-c mb5">с <?= $shop->getWorkingTimeToday()['start_time'] ?> до <?= $shop->getWorkingTimeToday()['end_time'] ?></div>

                <? if ($shopStates[0]->getQuantity()) : ?>
                    <!--  Кнопка Резерв -->
                    <?= $helper->render('cart/__button-product-oneClick',['product' => $product, 'url' => $helper->url('cart.oneClick.product.set', ['productId' => $product->getId(), 'shopId' => $shop->getId()]), 'class' => 'btnBuy__eLink mShopsOnly', 'value' => 'Резерв']) ?>
                <? endif; ?>
            </div>
        </div>
    <? endif; ?>

    <? if (count($shopStates) > 1) : ?>
        <div class="shopsVar shopsVar-center">
            <span class="shopsVar_title">Есть в <?= count($shopStates) ?> магазинах</span>
            <input type="button" class="button whitebutton js-show-shops" id="whitebutton" value="Забрать сегодня" />
        </div>

        <div style="display: none" class="popup shopsPopup">
            <i title="Закрыть" class="close">Закрыть</i>
            <div class="bPopupTitle">Забрать сегодня</div>

            <!--  Магазины  -->
            <ul class="markerList markerList-table">
            <? foreach ($shopStates as $shopState) : $shop = $shopState->getShop(); ?>

                <!--  Магазин -->
                <li class="markerList_row">
                    <span class="markerList_col markerList_col-mark">
                        <? if ($shop->getSubway()) : ?><i class="markColor" style="background-color: <?= ($shop->getSubway()[0] ? $shop->getSubway()[0]->getLine()->getColor() : '') ?>"></i><? endif; ?>
                    </span>

                    <!--  Адрес  -->
                    <span class="markerList_col markerList_col-left">
                        <? if ((bool)$shop->getSubway()) : ?>
                            <!--  Метро  -->
                            м. <?= $shop->getSubway()[0]->getName(); ?>
                        <? endif; ?>
                        <a class="markerList_light" href="<?= $helper->url('shop.show', ['regionToken' => \App::user()->getRegion()->getToken(), 'shopToken' => $shop->getToken()]) ?>"><?= $shop->getAddress() ?></a>
                    </span>

                    <!--  Время работы  -->
                    <span class="markerList_col markerList_col-center">
                        с <?= $shop->getWorkingTimeToday()['start_time'] ?> до <?= $shop->getWorkingTimeToday()['end_time'] ?>
                    </span>
                    
                    <!--  Кнопка "Резерв" или "На витрине"  -->
                    <span class="markerList_col markerList_col-right">
                        <? if ( $shopState->getQuantity() > 0 ) : ?>
                            <?= $helper->render('cart/__button-product-oneClick',['product' => $product, 'url' => $helper->url('cart.oneClick.product.set', ['productId' => $product->getId(), 'shopId' => $shop->getId()]), 'class' => 'btnBuy__eLink mShopsOnly', 'value' => 'Резерв']); ?>
                        <? else : ?>
                            <span class="btnText">На витрине</span>
                        <? endif; ?>
                    </span>
                </li>
            <? endforeach; ?>
            </ul>
        </div>
    <? endif; ?>
<?};