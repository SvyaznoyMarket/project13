<?php if (!isset($list[0])): ?>
  <p>нет товаров</p>

<?php else: ?>
  <?php include_partial('product/list_'.$view, $sf_data) ?>
<?php endif ?>