<?php
/** @deprecated */
?>

<form class="form dengiOnline" action="http://www.onlinedengi.ru/wmpaycheck.php" method="post">
  <input type="hidden" name="project" value="3321">
  <input type="hidden" name="source" value="3321">
  <input type="hidden" name="order_id" value="<?= $order->getId() ?>">
  <input type="hidden" name="nickname" value="<?= $order->getNumber() ?>">
  <input type="hidden" name="amount" value="<?= $order->getPaySum() ?>">
  <input type="hidden" name="mode_type" value="<?= $modeType ?>">
  <a class="bOrangeButton" href="#" onclick="$('form.dengiOnline').submit();return false;">Оплатить заказ</a>

  <?
  if($paymentMethod->isWebmoney()) {
    $linkText = 'Инструкция для оплаты через WebMoney';
  } else {
    $linkText = 'Инструкция для оплаты';
  }
  ?>
  <a href="<?= $page->url('content', ['token' => 'how_pay']) ?>"><?= $linkText ?></a>
</form>

