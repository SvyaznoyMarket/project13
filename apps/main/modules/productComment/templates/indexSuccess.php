<?php slot('navigation') ?>
  <?php include_component('productCard_', 'navigation', array('product' => $product)) ?>
<?php end_slot() ?>

<?php slot('title', $product->getName().': отзывы покупателей') ?>

<?php include_partial('product', array('product' => $product)) ?>

<p>Если вы затрудняетесь с выбором товара, мы рекомендуем вам узнать мнение и отзывы покупателей о товаре <strong><?php echo $product->getName() ?></strong>, представленные на этой странице. Вы также можете выразить свое мнение о товаре <strong><?php echo $product->getName() ?></strong>, оставив свой отзыв.</p>

<?php include_component('productComment', 'list', array('product' => $product, 'page' => $page, 'sort' => $sort, 'showSort' => true, 'showPage' => true)) ?>