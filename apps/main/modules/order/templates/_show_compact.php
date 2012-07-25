<?php //include_partial('default/sum', array('sum' => $item['sum'])) ?>
<?php
$info = $sf_data->getRaw('order');
use_helper('Date');
//print_r($statusList);
?>
   <?php if ($info['status'] == OrderEntity::STATUS_READY){ ?>
      <div class="fr font16 orange pb10">Заказ выполнен</div>
   <?php }elseif ($info['status'] == Order::STATUS_CANCELLED){ ?>
      <div class="fr font16 orange pb10">Заказ отменен</div>
  <?php } ?>
  <div class="font16 orange pb10"><strong>Заказ № <?php echo $item['number']?> от <?php echo format_date($item['created_at'],'dd.MM.yyyy')?></strong> на сумму&nbsp;<?php echo $item['sum']?> <span class="rubl">p</span></div>

   <?php if ($info['status']!=Order::STATUS_READY && $info['status']!=Order::STATUS_CANCELLED && false){ ?>
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
                       if ($info['status_id']>=$status['id']) echo "active ";
                       ?>
                       ">
                       <?php echo $status['name']?>
                   </td>
                   <?php } ?>
               </tr>
    </table>
  <?php } ?>
  <table class="order mb15">

        <?php foreach($item['products'] as $product): ?>
            <?php if ($product['type'] == 'service') { ?>
                <tr>
                   <th>
                       <a href="<?php echo $product['url'] ?>">
                        <?php echo $product['name'] ?> (<?php echo $product['quantity'] ?> шт.)
                       </a>
                   </th>
                   <td>
                       <strong class="font14"><?php echo $product['price'] ?>&nbsp;<span class="rubl">p</span></strong>
                   </td>
               </tr>
           <?php } else { ?>
                <tr>
                   <th>
                       <a href="<?php echo $product['url'] ?>">
                        <?php echo $product['name'] ?> (<?php echo $product['quantity'] ?> шт.)
                       </a>
                   </th>
                   <td>
                       <strong class="font14"><?php echo $product['price'] ?>&nbsp;<span class="rubl">p</span></strong>
                   </td>
               </tr>
           <?php } ?>
       <?php endforeach; ?>

       <?php if (isset($item['delivery_price']) && isset($item['delivery_type']) && (int)$item['delivery_price']>0 ) { ?>
            <tr>
               <th>
                    <?php echo $item['delivery_type'] ?>
               </th>
               <td>
                   <strong class="font14"><?php echo $item['delivery_price'] ?>&nbsp;<span class="rubl">p</span></strong>
               </td>
           </tr>

       <?php } ?>


       <tr>
           <th>
               <?php if (isset($item['delivered_at'])){ ?>
                    <div class="font12 pb5">
                        <?php echo $item['delivery_type'] ."."; ?>
                        <?php if ('0000-00-00 00:00:00' !== $item['delivered_at']): ?>
                        <?php $date = explode(" ",$item['delivered_at']);  echo format_date($date[0],'dd MMMM yyyy','ru')?>г.
                        <?php endif ?>
                        <?php if (isset($item['delivered_period'])) echo '('.$item['delivered_period'].')'; ?>
                    </div>
               <?php } ?>
               <div class="font12">Способ оплаты: <?php echo $item['payment_method_name'] ?></div>
           </th>
           <td>Итого к оплате:<br><strong class="font18"><?php echo $item['sum'] ?>&nbsp;<span class="rubl">p</span></strong></td>
       </tr>
<?php if (false): ?>
       <tr>
           <?php if ($info['status_id']==Order::STATUS_READY || $info['status_id']==Order::STATUS_CANCELLED){ ?>
               <!--<th><input type="button" value="Повторить заказ" class="button whitebutton"></th>-->
           <?php }else{ ?>
               <form method="post" action="<?php echo url_for('order_cancel')?>/<?php echo $item['token'] ?>" >
                   <th><input type="submit" value="Отменить заказ" name="cancel" class="button whitebutton"></th>
               </form>
          <?php } ?>
           <td></td>
       </tr>
<?php endif ?>
    </table>

