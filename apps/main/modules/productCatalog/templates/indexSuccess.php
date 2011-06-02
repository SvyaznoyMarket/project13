<h1>Каталог товаров</h1>

<div class="block">
  <?php include_component('productCatalog', 'navigation') ?>
</div>

<div class="block">
  <?php include_component('productCatalog', 'category_list', array('productCategoryList' => $productCategoryList)) ?>
</div>