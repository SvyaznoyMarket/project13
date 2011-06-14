<h1><?php echo $newsCategory ?></h1>

<div class="block">
  <?php //include_component('productCatalog', 'navigation', array('productCategory' => $productCategory)) ?>
</div>

<div class="block">
  <?php //include_component('productCatalog', 'filter', array('productCategory' => $productCategory)) ?>
</div>

<div class="block">
  <?php include_component('news', 'pagination', array('newsPager' => $newsPager)) ?>
</div>
<div class="block">
  <?php include_component('news', 'pager', array('newsPager' => $newsPager)) ?>
</div>
<div class="block">
  <?php include_component('news', 'pagination', array('newsPager' => $newsPager)) ?>
</div>
