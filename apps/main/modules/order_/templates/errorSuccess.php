<?php include_partial('order_/header', array('title' => 'Финальный шаг :)')) ?>

<?php include_component('order_', 'errors', array('errors' => $errors)) ?>

<div id="order-loader" class='bOrderPreloader'>
  <span>Загрузка...</span><img src='/images/bPreloader.gif'>
</div>