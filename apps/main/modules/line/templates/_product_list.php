<?php
/**
 * @var $productLine ProductLineEntity
 * @var $view
 */


$empty = 0 == $productLine->getTotalCount();
?>

<?php if (!$empty): ?>

  <h2 class="bold fl">Еще другие модели в серии <?php echo $productLine->getName() ?></h2>

  <?php include_component('product', 'list_view') ?>

  <div class="line"></div>

  <?php
    if($view == 'expanded')
      render_partial('product_/templates/_list_expanded_.php', array(
        'list' => $productLine->getFullProductList(),
      ));
    else
      render_partial('product_/templates/_list_compact_.php', array(
        'list' => $productLine->getFullProductList(),
        'in_row' => 4
      ));
  ?>

<?php endif ?>