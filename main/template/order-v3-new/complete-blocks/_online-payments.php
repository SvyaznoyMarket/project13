<?php

$f = function(
    \Helper\TemplateHelper $helper,
    $topMessage = '',
    $bottomMessage = 'Вы будете перенаправлены на сайт платежной системы',
    \Model\Order\Entity $order,
    $orderPayment,
    $blockVisible = false
) { ?>

    <!-- Блок оплата платежные системы -->
    <div class="orderPayment orderPaymentWeb jsOnlinePaymentBlock <?= $blockVisible ? 'jsOnlinePaymentBlockVisible' : '' ?>" style="display: <?= $blockVisible ? 'block' : 'none' ?>">
        <!-- Заголовок-->
        <!-- Блок в обводке -->
        <div class="orderPayment_block orderPayment_noOnline">

            <div class="orderPayment_msg orderPayment_noOnline_msg">
                <div class="orderPayment_msg_head">
                    <? if ($topMessage) : ?>
                        <?= $topMessage ?>
                    <? else : ?>
                        К оплате: <?= $helper->formatPrice($order->getSum()) ?> <span class="rubl">p</span>
                    <? endif ?>
                </div>
                <ul class="orderPaymentWeb_lst clearfix">
                    <?= $helper->render('order-v3-new/complete-blocks/__payments-li', ['orderPayment' => $orderPayment]) ?>
                </ul>
                <div class="orderPayment_msg_info">
                    <?= $bottomMessage ?>
                </div>
            </div>
        </div>
    </div>

<? }; return $f;