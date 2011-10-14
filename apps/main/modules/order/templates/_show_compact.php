<?php //include_partial('default/sum', array('sum' => $item['sum'])) ?>

<?
#print_r($item);
sfProjectConfiguration::getActive()->loadHelpers('Date');
?>
    <div class="font16 orange pb10"><strong>Заказ № <?=$item['token']?> от <?=format_date($item['created_at'],'MM.dd.yyyy')?></strong> на сумму&nbsp;<?=$item['sum']?> Р</div>      
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
        <? foreach($item['products'] as $product): ?>
        <tr>
           <th><a href="<?=$product['url']?>"><?=$product['name']?> (<?=$product['quantity']?> шт.)</a><div class="font11 pt5">Артикул #<?=$product['article']?> (сформирован на складе)</div></th>
           <td><strong class="font14"><?=$product['price']?>&nbsp;<span class="rubl">p</span></strong></td>
       </tr>
       <? endforeach; ?>
       <tr>
           <th>
               <div class="font12 pb5">Дата доставки: <?=format_date($item['delivered_at'][0],'dd MMMM yyyy','ru')?>г. (<?=$item['delivered_period']?>)</div>
               <div class="font12">Способ оплаты: <?=$item['payment_method_name']?></div>
           </th>
           <td>Итого к оплате:<br><strong class="font18"><?=$item['sum']?>&nbsp;<span class="rubl">p</span></strong></td>
       </tr>
       <tr>
           <th><input type="button" value="Отменить заказ" class="button whitebutton"></th>
           <td></td>
       </tr>    
    </table>    

