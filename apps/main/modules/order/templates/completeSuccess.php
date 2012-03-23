<?php use_helper('Date') ?>

<?php slot('complete_order_id', array_map(function($i) { return $i['number']; }, $orders)) ?>
<?php slot('complete_order_sum', array_map(function($i) { return $i['sum']; }, $orders)) ?>

<h1>Ваш заказ принят, спасибо за покупку!</h1>
