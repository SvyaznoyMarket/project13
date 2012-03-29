<?php
/**
 * @var $list
 * @var $view
 */
?>
<?php $i = 0;
foreach ($list as $item): $i++; ?>
<?php include_partial('show_', array('view' => $view, 'item' => $item)) ?>
<?php if (!($i % 3)): ?>
  <div class="line"></div>
  <?php endif ?>
<?php endforeach ?>