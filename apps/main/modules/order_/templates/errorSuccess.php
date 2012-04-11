<?php include_partial('order_/header', array('title' => 'Ошибка')) ?>

<p><?php echo $message ?></p>

<?php include_partial('order_/footer') ?>

<?php if (false): ?>
<?php slot('seo_counters_advance') ?>
<?php include_component('order', 'seo_counters_advance', array('step' => 2)) ?>
<?php end_slot() ?>
<?php endif ?>