<? if(!empty($paymentUrl)) { ?>
    <div class="pb15">
      <form class="form paymentUrl" data-clear-payment-url="<?= $page->url('order.clearPaymentUrl') ?>" method="get" action="<?= $paymentUrl ?>">
        <a class="bOrangeButton paymentUrl" href="<?= $paymentUrl ?>">Оплатить заказ</a>
        <a href="<?= $page->url('content', ['token' => 'how_pay']) ?>">Инструкция для оплаты через QIWI или WebMoney</a>
      </form>
    </div>
<? } ?>
