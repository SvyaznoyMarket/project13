<?php slot('navigation') ?>
  <?php include_component('productCard_', 'navigation', array('product' => $productEntity, 'isComment' => true)) ?>
<?php end_slot() ?>

<?php slot('title', $item->getName().': отзывы покупателей') ?>

<?php include_partial('product', array('product' => $item, 'hasProductStockLink' => $hasProductStockLink)) ?>

<p>Если вы затрудняетесь с выбором товара, мы рекомендуем вам узнать мнение и отзывы покупателей о товаре <?php echo $item->getName() ?>, представленные на этой странице. Вы также можете выразить свое мнение о товаре <?php echo $item->getName() ?>, оставив свой отзыв.</p>

<?php include_component('productComment', 'list', array('product' => $item, 'page' => $page, 'sort' => $sort, 'showSort' => true, 'showPage' => true)) ?>
