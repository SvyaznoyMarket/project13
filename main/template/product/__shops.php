<?php

return function (
    \Helper\TemplateHelper $helper,
    array $shopStates = [],
    \Model\Product\Entity $product
) {
    /** @var $shopStates \Model\Product\ShopState\Entity[]  */

    ?>

    <? if (count($shopStates) == 1) : $shop = $shopStates[0]->getShop(); ?>
        Есть в магазине <br />

        <? if ((bool)$shop->getSubway()) : ?>
            <!--  Метро  -->
            <div>
                <div style="display: inline-block; border-radius: 50%; width: 10px; height: 10px; background: <?= $shop->getSubway()[0]->getLine()->getColor() ?>"></div>
                м. <?= $shop->getSubway()[0]->getName(); ?>
            </div>
        <? endif; ?>

            <!--  Адрес  -->
            <div>
                <a href="<?= $helper->url('shop.show', ['regionToken' => \App::user()->getRegion()->getToken(), 'shopToken' => $shop->getToken()]) ?>"><?= $shop->getAddress() ?></a>
            </div>
            <!--  Время работы  -->
            <div>
                <?= $shop->getRegime() ?>
            </div>
            <!--  Кнопка Резерв           -->
            <div>
                <?= $helper->render('cart/__button-product',['product' => $product, 'url' => null, 'class' => null, 'value' => 'Резерв']) ?>
            </div>

    <? endif; ?>

    <? if (count($shopStates) > 1) : ?>

        Есть в <?= count($shopStates) ?> магазинах
        <input type="button" class="btnMore button whitebutton bDeliveryNowClick" id="whitebutton" value="Забрать сегодня">

        <div style="display: none" class="popup shopsPopup">
            <i title="Закрыть" class="close">Закрыть</i>
            <div class="bPopupTitle">Забрать сегодня</div>
            <!--  Магазины  -->
            <ul>
            <? foreach ($shopStates as $shopState) : $shop = $shopState->getShop(); ?>
                <!--  Магазин -->
                <li>

                    <!--  Адрес  -->
                    <div style="display: inline-block">
                        <? if ((bool)$shop->getSubway()) : ?>
                            <!--  Метро  -->
                            <div>
                                <div style="display: inline-block; border-radius: 50%; width: 10px; height: 10px; background: <?= $shop->getSubway()[0]->getLine()->getColor() ?>"></div>
                                м. <?= $shop->getSubway()[0]->getName(); ?>
                            </div>
                        <? endif; ?>
                        <a href="<?= $helper->url('shop.show', ['regionToken' => \App::user()->getRegion()->getToken(), 'shopToken' => $shop->getToken()]) ?>"><?= $shop->getAddress() ?></a>
                    </div>

                    <!--  Время работы  -->
                    <div style="display: inline-block">
                        <?= $shop->getRegime() ?>
                    </div>
                    <!--  Кнопка "Резерв" или "На витрине"  -->
                    <div style="display: inline-block">
                        <? if ( $shopState->getQuantity() > 0 ) : ?>
                            <?= $helper->render('cart/__button-product-oneClick',['product' => $product, 'url' => null, 'class' => null, 'value' => 'Резерв']); ?>
                        <? else : ?>
                            На витрине
                        <? endif; ?>
                    </div>

                </li>
            <? endforeach; ?>
            </ul>
        </div>

    <? endif; ?>

<?};