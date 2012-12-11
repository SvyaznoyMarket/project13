<?php include_partial('order_/header', array('title' => 'Финальный шаг :)')) ?>

<?php if (isset($errors)): ?>
<?php include_component('order_', 'errors', array('errors' => $errors)) ?>

<div id="order-loader" class='bOrderPreloader'>
  <span>Загрузка...</span><img src='/images/bPreloader.gif'>
</div>

<?php else: ?>
  <h2><?php echo isset($message) ? $message : 'При формировании заказа произошла ошибка' ?></h2>
  <script type="text/javascript">
  	_gaq.push(['_trackEvent', 'Errors', 'Orders/new error', <?php echo isset($message) ? $message : 'При формировании заказа произошла ошибка' ?>])
  </script>
<?php endif ?>