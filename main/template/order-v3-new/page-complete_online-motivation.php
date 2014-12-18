<?php

return function(
    \Helper\TemplateHelper $helper,
    $orders,
    $ordersPayment,
    $products,
    $userEntity,
    $sessionIsReaded,
    $banks,
    $creditData
) {
    /** @var $products \Model\Product\Entity[] */
    $page = new \View\OrderV3\CompletePage();
    /** @var $order \Model\Order\Entity */
    $order = reset($orders);
    ?>

    <?= $helper->render('order-v3-new/__head', ['step' => 3]) ?>

    <section class="orderCnt">

        <!-- Заверстать все блоки тут -->
        <!-- Блок оплата (оплата онлайн невозможна) -->
        <div class="orderPayment">
            <!-- Заголовок -->
            <div class="orderPayment_head">
                    Оформлен заказ № <a href="#" class="orderPayment_num">COXD-305127</a>
            </div>
            <!-- Блок в обводке -->
            <div class="orderPayment_block orderPayment_noOnline">

                <div class="orderPayment_msg orderPayment_noOnline_msg">
                    <div class="orderPayment_msg_head">
                        Ждем вас 27.12.2014 в магазине
                    </div>
                    <div class="orderPayment_msg_shop">
                        <span class="orderPayment_msg_shop_metro">м. Петровско-Разумовская</span>
                        <span class="orderPayment_msg_shop_addr">ул. Линии Октябрьской Железной Дороги, д. 1, стр. 2</span>
                        <a href="#" class="orderPayment_msg_addr_link">
                            Как добраться
                        </a>
                    </div>
                    <div class="orderPayment_msg_info">
                        Вы можете оплатить заказ при получении.
                    </div>
                </div>
            </div>
        </div>







        <div class="orderCompl orderCompl_final clearfix">
            <a class="orderCompl_continue_link" href="<?= $helper->url('homepage') ?>">Вернуться на главную</a>
        </div>
    </section>

    <? if (!$sessionIsReaded) {
        // Если сесиия уже была прочитана, значит юзер обновляет страницу, не трекаем партнёров вторично
        echo $page->render('order/_analytics', array(
            'orders'       => $orders,
            'productsById' => $products,
        ));

        echo $page->render('order/partner-counter/_complete', [
            'orders'       => $orders,
            'productsById' => $products,
        ]);

        echo $helper->render('order/__analyticsData', ['orders' => $orders, 'productsById' => $products]);

        // Flocktory popup
        echo $helper->render('order-v3/partner-counter/_flocktory-complete',[
            'orders'    => $orders,
            'products'  => $products,
        ]);
    } ?>

<? };



