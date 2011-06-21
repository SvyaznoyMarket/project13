<div class="block">
  <?php include_component('user', 'menu') ?>
</div>

<h1>Сравнение товаров</h1>

<div class="block">

  <?php if (count($productCategoryList) > 0): ?>
    <ul>
    <?php foreach ($productCategoryList as $productCategory): ?>
      <li><a href="<?php echo url_for('userProductCompare_show', $productCategory) ?>"><?php echo $productCategory ?></a></li>
    <?php endforeach ?>
    </ul>

  <?php else: ?>
    <p>нет товаров для сравнения</p>

  <?php endif ?>

</div>
