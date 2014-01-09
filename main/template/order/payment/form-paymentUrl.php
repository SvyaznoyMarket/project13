<? if (!empty($paymentUrl)): ?>
    <div class="pb15">
        <form class="form paymentUrl" data-clear-payment-url="<?= $page->url('order.clearPaymentUrl') ?>" method="get" action="<?= $paymentUrl ?>">
            <a class="bOrangeButton paymentUrl" href="<?= $paymentUrl ?>">Оплатить заказ</a>

            <? $linkText = $paymentMethod->isQiwi() ? 'Инструкция для оплаты через QIWI' : 'Инструкция для оплаты' ?>
            <a href="<?= $page->url('content', ['token' => 'how_pay']) ?>"><?= $linkText ?></a>
        </form>
    </div>
<? endif ?>
