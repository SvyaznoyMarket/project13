<?php
$request = sfContext::getInstance()->getRequest();
$page = $request->getParameter('page');
$view = $request->getParameter('view', isset($view) ? $view : null);
?>
<?php $empty = 0 == $productPager->getNbResults() ?>
<?php if (false): ?>
<?php if( count($productPager->getLinks()) - 1 ): ?>
<div data-url="<?php echo url_for('productCatalog_categoryAjax',array('productCategory' => $productCategory->token )); ?>"
	 data-page="<?php  echo $page; ?>"
	 data-mode="<?php  echo $view; ?>"
	 data-lastpage="<?php echo count($productPager->getLinks()); ?>"
	 style="padding-bottom: 9px; cursor:pointer;" class="fr allpager"> все</div>
<?php endif ?>
<?php endif ?>
<?php include_component('product', 'pagination', array('pager' => $productPager)) ?>

<?php if (!$empty && !(isset($list_view) && false === $list_view)): ?>
  <?php include_component('product', 'list_view') ?>
<?php endif ?>

<?php if (!$empty): ?>
  <div class="line"></div>
<?php endif ?>
  
<?php include_component('product', 'pager', array('pager' => $productPager, 'ajax_flag' => false, 'view' => $view, )) ?>

<?php if (false): ?>
  <div class="line pb10"></div>
<?php endif ?>
<?php if (false): ?>
<?php if( count($productPager->getLinks()) - 1 ): ?>
<div data-url="<?php echo url_for('productCatalog_categoryAjax',array('productCategory' => $productCategory->token )); ?>"
	 data-page="<?php  echo $page; ?>"
	 data-mode="<?php  echo $view; ?>"
	 data-lastpage="<?php echo count($productPager->getLinks()); ?>"
	 style="padding-bottom: 9px; cursor:pointer;" class="fr allpager"> все</div>
<?php endif ?>
<?php endif ?>
<?php include_component('product', 'pagination', array('pager' => $productPager)) ?>
