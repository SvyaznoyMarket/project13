<div class="block">
  <ul class="inline">
    <li><?php include_component('product', 'sorting', array('productSorting' => $productSorting)) ?></li>
    <li><?php include_component('userProductCompare', 'button', array('productCategory' => $productCategory)) ?></li>
  </ul>
  <?php include_component('product', 'pagination', array('productPager' => $productPager)) ?>
</div>
<div class="block">
  <?php include_component('product', 'pager', array('productPager' => $productPager)) ?>
</div>
<div class="block">
  <?php include_component('product', 'pagination', array('productPager' => $productPager)) ?>
</div>
