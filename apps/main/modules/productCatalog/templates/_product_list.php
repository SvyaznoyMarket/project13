<?php $empty = 0 == $productPager->getNbResults() ?>

<?php include_component('product', 'pagination', array('pager' => $productPager)) ?>

<?php if (!$empty): ?>
  <?php include_component('product', 'list_view') ?>
<?php endif ?>

<?php if (!$empty): ?>
  <div class="line"></div>
<?php endif ?>

<?php include_component('product', 'pager', array('pager' => $productPager)) ?>

<?php if (!$empty): ?>
  <div class="line pb10"></div>
<?php endif ?>

<?php include_component('product', 'pagination', array('pager' => $productPager)) ?>



<?php if (false): ?>
  <div class="block">
    <ul class="inline">
      <li>
        <?php include_component('product', 'sorting', array('productSorting' => $productSorting)) ?>
      </li>
      <li>
        <?php include_component('userProductCompare', 'button', array('productCategory' => $productCategory)) ?>
      </li>
    </ul>

    <div class="left">всего: <?php echo $productPager->getNbResults() ?></div>
    <?php include_component('product', 'pagination', array('pager' => $productPager)) ?>
    <?php include_component('product', 'list_view') ?>
    <br class="clear" />
  </div>

  <div class="block">
    <?php include_component('product', 'pager', array('pager' => $productPager)) ?>
  </div>

  <div class="block">
    <div class="left">всего: <?php echo $productPager->getNbResults() ?></div>
    <?php include_component('product', 'pagination', array('pager' => $productPager)) ?>
    <?php include_component('product', 'list_view') ?>
    <br class="clear" />
  </div>
<?php endif ?>