<?php $empty = 0 == $productPager->getNbResults() ?>

<?php if (!$empty): ?>
  <div class="line"></div>
<?php endif ?>

<?php include_component('product', 'pager', array('pager' => $productPager)) ?>

<?php if (false): ?>
  <div class="line pb10"></div>
<?php endif ?>




