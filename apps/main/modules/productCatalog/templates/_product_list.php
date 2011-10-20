<?php $empty = 0 == $productPager->getNbResults() ?>

<?php include_component('product', 'pagination', array('pager' => $productPager)) ?>

<?php if (!$empty): ?>
  <?php include_component('product', 'list_view') ?>
<?php endif ?>

<?php if (!$empty): ?>
  <div class="line"></div>
<?php endif ?>

<?php include_component('product', 'pager', array('pager' => $productPager)) ?>

<?php if (false): ?>
  <div class="line pb10"></div>
<?php endif ?>

<?php include_component('product', 'pagination', array('pager' => $productPager)) ?>
