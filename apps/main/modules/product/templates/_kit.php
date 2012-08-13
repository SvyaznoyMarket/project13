<?php
$request = sfContext::getInstance()->getRequest();
$page = $request->getParameter('page');
$view = $request->getParameter('view', isset($view) ? $view : null);
?>
<?php $empty = 0 == $productPager->getNbResults() ?>

<?php if (!$empty): ?>
<?php include_component('product', 'pagination', array('pager' => $productPager)) ?>
<h2 class="bold fl"><?php echo $item->name ?> включает в себя:</h2>

<?php if (!$empty && !(isset($list_view) && false === $list_view)): ?>
  <?php include_component('product', 'list_view') ?>
<?php endif ?>

  <div class="line"></div>

<?php include_component('product', 'pager', array('pager' => $productPager, 'ajax_flag' => false, 'view' => $view, 'in_row' => 4, 'last_line' => false, )) ?>

<?php include_component('product', 'pagination', array('pager' => $productPager)) ?>
<?php endif ?>