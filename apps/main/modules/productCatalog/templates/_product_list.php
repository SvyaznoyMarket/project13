<?php 
$request = sfContext::getInstance()->getRequest();
$page =$request->getParameter('page');
$view =$request->getParameter('view');
?>
<?php $empty = 0 == $productPager->getNbResults() ?>
<div data-url="<?php echo url_for('productCatalog_categoryAjax',array('productCategory' => 'divani')), $view; ?>" data-page="<?php  echo $page;  ?>"
	style="padding-bottom: 9px; cursor:pointer;" class="fr allpager"> все</div>

<?php include_component('product', 'pagination', array('pager' => $productPager)) ?>

<?php if (!$empty): ?>
  <?php include_component('product', 'list_view') ?>
<?php endif ?>

<?php if (!$empty): ?>
  <div class="line"></div>
<?php endif ?>

<?php include_component('product', 'pager', array('pager' => $productPager, 'ajax_flag' => false)) ?>

<?php if (false): ?>
  <div class="line pb10"></div>
<?php endif ?>
<div style="padding-bottom: 9px; cursor:pointer;" class="fr allpager"> все</div>
<?php include_component('product', 'pagination', array('pager' => $productPager)) ?> 
