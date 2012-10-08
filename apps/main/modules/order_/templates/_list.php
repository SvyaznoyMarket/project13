<?php
/**
 * @var $list OrderEntity[]
 * @var $statusList array
 * @var $productList ProductEntity[]
 * @var $serviceList ServiceEntity[]
 * @var $deliveryTypeList DeliveryTypeEntity[]
 * @var $paymentMethodList PaymentMethodEntity[]
 *
 */
?>

<?php
$isCorporative = $sf_user->getGuardUser() ? $sf_user->getGuardUser()->getIsCorporative() : false;
?>

<div class="fl"><strong class="font16">Текущие заказы (<?php echo count($list); ?>)</strong></div>
<div class="clear pb20"></div>
<?php
use_helper('Date');

if (count($list)<1) echo '<div>У Вас пока нет ни одного заказа.</div>';
?>

<?php foreach ($list as $item): /** @var $item OrderEntity  */?>

  <?php if($item->getStatusId() == Order::STATUS_READY){ ?>
  <div class="fr font16 orange pb10">Заказ выполнен</div>
  <?php }elseif ($item->getStatusId() == Order::STATUS_CANCELLED){ ?>
  <div class="fr font16 orange pb10">Заказ отменен</div>
  <?php  }?>
  <div class="font16 orange pb10"><strong>Заказ № <?php echo $item->getNumber() ?> от <?php echo format_date($item->getCreatedAt(),'dd.MM.yyyy')?></strong> на сумму&nbsp;<?php echo $item->getSum()?> <span class="rubl">p</span></div>

  <?php if ($item->getStatusId()!=Order::STATUS_READY && $item->getStatusId()!=Order::STATUS_CANCELLED && false){ ?>
  <table cellspacing="0" class="status">
    <tr>
      <?php
      $num = 0;
      foreach($statusList as $status) {
        if ($status['token']=='cancelled') continue;
        $num++;
        ?>
        <td class="
                       <?php
          if ($num==1) echo 'first ';
          elseif ($num==count($statusList)-1) echo 'next ';
          if ($item->getStatusId()>=$status['id']) echo "active ";
          ?>
                       ">
          <?php echo $status['name']?>
        </td>
        <?php } ?>
    </tr>
  </table>
  <?php } ?>

  <table class="order mb15">

    <?php
    $items = $item->getItem();
    if(!$items){ $items = array();}
    foreach($items as $product):
      /** @var $product OrderItemEntity */
      if ($product->getServiceId()) { //услуга :) ?>
      <tr>
        <th>
          <a href="<?php echo url_for('service_show', array('service' => $serviceList[$product->getServiceId()]->getToken()))  ?>">
            <?php echo $serviceList[$product->getServiceId()]->getName() ?> (<?php echo $product->getQuantity() ?> шт.)
          </a>
        </th>
        <td>
          <strong class="font14"><?php echo $product->getPrice() ?>&nbsp;<span class="rubl">p</span></strong>
        </td>
      </tr>
      <?php } else { ?>
      <tr>
        <th>
          <a href="<?php echo $productList[$product->getProductId()]->getLink() ?>">
            <?php echo $productList[$product->getProductId()]->getName() ?> (<?php echo $product->getQuantity() ?> шт.)
          </a>
        </th>
        <td>
          <strong class="font14"><?php echo $product->getPrice() ?>&nbsp;<span class="rubl">p</span></strong>
        </td>
      </tr>
      <?php }

    endforeach; ?>

    <?php if ($item->getDeliveryType() && (int)$item->getDeliveryPrice()>0 ) { ?>
    <tr>
      <th>
        <?php echo (array_key_exists($item->getDeliveryType(), $deliveryTypeList))? $deliveryTypeList[$item->getDeliveryType()] : ''; ?>
      </th>
      <td>
        <strong class="font14"><?php echo $item->getDeliveryPrice() ?>&nbsp;<span class="rubl">p</span></strong>
      </td>
    </tr>

    <?php } ?>


    <tr>
      <th>
        <?php if ($item->getDeliveryDate()){ ?>
        <div class="font12 pb5">
          <?php //echo (array_key_exists($item->getDeliveryType(), $deliveryTypeList))? $deliveryTypeList[$item->getDeliveryType()]->getName() : '' ."."; ?>
          <?php if ('0000-00-00' !== $item->getDeliveryDate()): ?>
          <?php echo format_date($item->getDeliveryDate(),'dd MMMM yyyy','ru')?>г.
          <?php endif ?>
        </div>
        <?php } ?>
        <div class="font12">Способ оплаты: <?php echo  (array_key_exists($item->getPaymentId(), $paymentMethodList))? $paymentMethodList[$item->getPaymentId()]->getName() : ''?></div>

        <?php if ($isCorporative) { ?>
        <div class="font12">Счет: <?php echo $item->getIsBill() ? link_to('выставлен', 'user_order_bill', array('order' => $item->getNumber())) : 'выставляется' ?></div>
        <?php } ?>
      </th>
      <td>Итого к оплате:<br><strong class="font18"><?php echo $item->getSum() ?>&nbsp;<span class="rubl">p</span></strong></td>
    </tr>
    <?php if (false): ?>
    <tr>
      <?php if ($item->getStatusId()==Order::STATUS_READY || $item->getStatusId()==Order::STATUS_CANCELLED){ ?>
      <!--<th><input type="button" value="Повторить заказ" class="button whitebutton"></th>-->
      <?php }else{ ?>
      <form method="post" action="<?php echo url_for('order_cancel')?>/<?php echo $item->getId() ?>" >
        <th><input type="submit" value="Отменить заказ" name="cancel" class="button whitebutton"></th>
      </form>
      <?php } ?>
      <td></td>
    </tr>
    <?php endif ?>
  </table>
<?php endforeach ?>