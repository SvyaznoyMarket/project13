<?php
$request = sfContext::getInstance()->getRequest();
$page = $request->getParameter('page');
$view = $request->getParameter('view', isset($view) ? $view : null);
?>
<?php $empty = 0 == $productPager->getNbResults() ?>

<?php if (!$empty): ?>
<?php include_component('product', 'pagination', array('pager' => $productPager)) ?>
<h2 class="bold fl">Еще другие модели в серии <?php echo $line->name ?></h2>
<?php if (!$empty && !(isset($list_view) && false === $list_view)): ?>
  <?php include_component('product', 'list_view') ?>
<?php endif ?>

<?php if (!$empty): ?>
  <div class="line"></div>
<?php endif ?>

<?php include_component('line', 'pager', array('pager' => $productPager, 'ajax_flag' => false, 'view' => $view, )) ?>

<?php include_component('product', 'pagination', array('pager' => $productPager)) ?>
<?php endif ?>