<?php //include_partial('default/sum', array('sum' => $item['sum'])) ?>
<?php
$info = $order->getData();
use_helper('Date'); 
//print_r($statusList);
?>
   <?php if ($info['status_id']==Order::STATUS_READY){ ?>
      <div class="fr font16 orange pb10">Заказ выполнен</div>  
   <?php }elseif ($info['status_id']==Order::STATUS_CANCELLED){ ?>
      <div class="fr font16 orange pb10">Заказ отменен</div>
  <?php } ?>    
  <div class="font16 orange pb10"><strong>Заказ № <?php echo $item['token']?> от <?php echo format_date($item['created_at'],'MM.dd.yyyy')?></strong> на сумму&nbsp;<?php echo $item['sum']?> <span class="rubl">p</span></div>
  
   <?php if ($info['status_id']!=Order::STATUS_READY && $info['status_id']!=Order::STATUS_CANCELLED){ ?>
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
        <tr>
           <th><a href="<?php echo $product['url'] ?>"><?php echo $product['name'] ?> (<?php echo $product['quantity'] ?> шт.)</a><div class="font11 pt5">Артикул #<?php echo $product['article'] ?> (сформирован на складе)</div></th>
           <td><strong class="font14"><?php echo $product['price'] ?>&nbsp;<span class="rubl">p</span></strong></td>
       </tr>
       <?php endforeach; ?>
       <tr>
           <th>
               <?php if (isset($item['delivered_at'])){ ?><div class="font12 pb5"> Дата доставки: <?php $date = explode(" ",$item['delivered_at']);  echo format_date($date[0],'dd MMMM yyyy','ru')?>г. (<?php echo $item['delivered_period']?>)</div><?php } ?>
               <div class="font12">Способ оплаты: <?php echo $item['payment_method_name']?></div>
           </th>
           <td>Итого к оплате:<br><strong class="font18"><?php echo $item['sum'] ?>&nbsp;<span class="rubl">p</span></strong></td>
       </tr>
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
    </table>

