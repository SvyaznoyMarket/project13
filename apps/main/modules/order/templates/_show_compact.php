<?php
/** @var $order OrderEntity */
/** @var $item OrderItemEntity */
?>

<?php
$order = $sf_data->getRaw('order');
use_helper('Date');
?>
<?php if ($order->getStatus() == OrderEntity::STATUS_READY): ?>
<div class="fr font16 orange pb10">Заказ выполнен</div>
<?php elseif ($order->getStatus() == Order::STATUS_CANCELLED): ?>
<div class="fr font16 orange pb10">Заказ отменен</div>
<?php endif ?>

<div class="font16 orange pb10"><strong>Заказ № <?php echo $order->getNumber() ?>
  от <?php echo format_date($order->getCreatedAt(), 'dd.MM.yyyy')?></strong> на
  сумму&nbsp;<?php echo $order->getSum() ?> <span class="rubl">p</span></div>

<?php if ($order->getStatus() != Order::STATUS_READY && $order->getStatus() != Order::STATUS_CANCELLED && false) { ?>
<table cellspacing="0" class="status">
  <tr>
    <?php
    $num = 0;
    foreach ($statusList as $status) {
      if ($status['token'] == 'cancelled') continue;
      $num++;
      ?>
      <td class="
                       <?php
        if ($num == 1) echo 'first ';
        elseif ($num == count($statusList) - 1) echo 'next ';
        if ($order->getStatus() >= $status['id']) echo "active ";
        ?>
                       ">
        <?php echo $status['name']?>
      </td>
      <?php } ?>
  </tr>
</table>
<?php } ?>

<table class="order mb15">

  <?php foreach ($order->getItem() as $item): ?>
  <?php if (OrderItemEntity::TYPE_PRODUCT == $item->getType()) { ?>
    <tr>
      <th>
        <a href="<?php echo $item->getProduct()->getLink() ?>">
          <?php echo $item->getProduct()->getName() ?> (<?php echo $item->getQuantity() ?> шт.)
        </a>
      </th>
      <td>
        <strong class="font14"><?php echo $item->getPrice() ?>&nbsp;<span class="rubl">p</span></strong>
      </td>
    </tr>
    <?php } else { ?>
    <tr>
      <th>
        <a href="#">
          <?php echo $item->getService()->getName() ?> (<?php echo $item->getQuantity() ?> шт.)
        </a>
      </th>
      <td>
        <strong class="font14"><?php echo $item->getPrice() ?>&nbsp;<span class="rubl">p</span></strong>
      </td>
    </tr>
    <?php } ?>
  <?php endforeach; ?>

  <?php if ($order->getDeliveryPrice() && $order->getDeliveryType() && (int)$order->getDeliveryPrice() > 0) { ?>
  <tr>
    <th>
      <?php echo $order->getDeliveryType()->getName() ?>
    </th>
    <td>
      <strong class="font14"><?php echo $order->getDeliveryPrice() ?>&nbsp;<span class="rubl">p</span></strong>
    </td>
  </tr>

  <?php } ?>


  <tr>
    <th>
      <?php if (false && !empty($order['delivered_at']) && ('0000-00-00' != $order['delivered_at'])) { ?>
      <div class="font12 pb5">
        <?php echo $order['delivery_type'] . "."; ?>
        <?php if ('0000-00-00 00:00:00' !== $order['delivered_at']): ?>
        <?php $date = explode(" ", $order['delivered_at']);
        echo format_date($date[0], 'dd MMMM yyyy', 'ru') ?>г.
        <?php endif ?>
        <?php if (isset($order['delivered_period'])) echo '(' . $order['delivered_period'] . ')'; ?>
      </div>
      <?php } ?>

      <?php if (false): ?>
      <div class="font12">Способ оплаты: <?php echo $order->getPaymentMethod()->getName() ?></div>
      <?php endif ?>
    </th>
    <td>Итого к оплате:<br><strong class="font18"><?php echo $order['sum'] ?>&nbsp;<span class="rubl">p</span></strong>
    </td>
  </tr>

  <?php if (false): ?>
  <tr>
    <?php if ($order['status_id'] == Order::STATUS_READY || $order['status_id'] == Order::STATUS_CANCELLED) { ?>
    <!--<th><input type="button" value="Повторить заказ" class="button whitebutton"></th>-->
    <?php } else { ?>
    <form method="post" action="<?php echo url_for('order_cancel')?>/<?php echo $order['token'] ?>">
      <th><input type="submit" value="Отменить заказ" name="cancel" class="button whitebutton"></th>
    </form>
    <?php } ?>
    <td></td>
  </tr>
  <?php endif ?>

</table>

