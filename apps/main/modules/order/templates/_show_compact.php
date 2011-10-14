<?php //include_partial('default/sum', array('sum' => $item['sum'])) ?>
<?php use_helper('Date') ?>

  <div class="font16 orange pb10"><strong>Заказ № <?php echo $item['token']?> от <?php echo format_date($item['created_at'],'MM.dd.yyyy')?></strong> на сумму&nbsp;<?php echo $item['sum']?> <span class="rubl">p</span></div>
    <table cellspacing="0" class="status">
               <tr>
                   <td class="first active">Обработка заказа</td>
                   <td class="active">Резерв товара</td>
                   <td>Доставка товара</td>
                   <td>Заказ выполнен</td>
                   <td class="next">Заказ выполнен</td>
               </tr>
    </table>
    <table class="order mb15">
        <?php foreach($item['products'] as $product): ?>
        <tr>
           <th><a href="<?php echo $product['url'] ?>"><?php echo $product['name'] ?> (<?php echo $product['quantity'] ?> шт.)</a><div class="font11 pt5">Артикул #<?php echo $product['article'] ?> (сформирован на складе)</div></th>
           <td><strong class="font14"><?php echo $product['price'] ?>&nbsp;<span class="rubl">p</span></strong></td>
       </tr>
       <?php endforeach; ?>
       <tr>
           <th>
               <div class="font12 pb5">Дата доставки: <?php echo format_date($item['delivered_at'][0],'dd MMMM yyyy','ru')?>г. (<?php echo $item['delivered_period']?>)</div>
               <div class="font12">Способ оплаты: <?php echo $item['payment_method_name']?></div>
           </th>
           <td>Итого к оплате:<br><strong class="font18"><?php echo $item['sum'] ?>&nbsp;<span class="rubl">p</span></strong></td>
       </tr>
       <tr>
           <th><input type="button" value="Отменить заказ" class="button whitebutton"></th>
           <td></td>
       </tr>
    </table>

