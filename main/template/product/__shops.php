<?php

return function (
    \Helper\TemplateHelper $helper,
    array $shopStates = [],
    \Model\Product\Entity $product
) {
    /** @var $shopStates \Model\Product\ShopState\Entity[]  */

    ?>

    <div class="dlvr-self dlvr-self__center">
        <button class="dlvr-self_btn btn6 js-show-shops">Забрать сегодня</button>
        <span class="dlvr-self_t">
            Бесплатный самовывоз<br/>
            из <?= count($shopStates) ?> <?= $helper->numberChoice(count($shopStates), ['магазина', 'магазинов', 'магазинов'])?>
        </span>
    </div>

    <div style="display: none" class="popup shopsPopup">
        <i title="Закрыть" class="close">Закрыть</i>
        <div class="popup_hd">
            <div class="popup_hd_tl">Забрать сегодня</div>
            <? if ($product->isInShopShowroom()): ?>
            <p class="popup_hd_tx">Чтобы купить товар с витрины, нужно приехать в магазин и обратиться к продавцу.</p>
            <? endif ?>
        </div>

        <!--  Магазины  -->
        <div class="markerList">
            <ul class="markerList_lst">
            <? foreach ($shopStates as $shopState) : $shop = $shopState->getShop() ?>

                <!--  Магазин -->
                <li class="markerList_row">
                    <span class="markerList_col markerList_col-mark">
                        <? if ($shop->getSubway()) : ?><i class="markColor" style="background-color: <?= ($shop->getSubway()[0] ? $shop->getSubway()[0]->getLine()->getColor() : '') ?>"></i><? endif ?>
                    </span>

                    <!--  Адрес  -->
                    <span class="markerList_col markerList_col-left">
                        <? if ((bool)$shop->getSubway()) : ?>
                            <!--  Метро  -->
                            м. <?= $shop->getSubway()[0]->getName() ?>
                        <? endif ?>
                        <div><a class="markerList_light" href="<?= $helper->url('shop.show', ['regionToken' => \App::user()->getRegion()->getToken(), 'shopToken' => $shop->getToken()]) ?>"><?= $shop->getAddress() ?></a></div>
                    </span>

                    <!--  Время работы  -->
                    <span class="markerList_col markerList_col-center">
                        <span class="markerList_light">с <?= $shop->getWorkingTimeToday()['start_time'] ?> до <?= $shop->getWorkingTimeToday()['end_time'] ?></span>
                    </span>

                    <!--  Кнопка "Резерв" или "На витрине"  -->
                    <span class="markerList_col markerList_col-right">
                        <? if ( $shopState->getQuantity() > 0 ) : ?>
                            <?= $helper->render('cart/__button-product-oneClick', [
                                'product' => $product,
                                'shop'    => $shop,
                                'url'     => $helper->url('cart.oneClick.product.set', ['productId' => $product->getId(), 'shopId' => $shop->getId()]),
                                //'class'   => 'btnBuy__eLink mShopsOnly',
                                'class'   => 'btnBuy__eLink',
                                'value'   => 'Купить'
                            ]) ?>
                        <? else: ?>
                            <span class="btnText">На витрине</span>
                        <? endif ?>
                    </span>
                </li>
            <? endforeach ?>
            </ul>
        </div>
    </div>
<? };