<div class="fl"><strong class="font16">Текущие заказы (<?php echo count($list); ?>)</strong></div>
<div class="clear pb20"></div>
<?php
if (count($list)<1) echo '<div>У Вас пока нет ни одного заказа.</div>';
?>

<?php foreach ($list as $item): ?>
    <?php include_component('order', 'show', array('view' => 'compact', 'order' => $item, 'statusList' => $statusList)) ?>
<?php endforeach ?>