<!-- Goods info -->
<div class="goodsphotosmall"><div class="photo"><img src="<?php echo $item->getMainPhotoUrl(2) ?>" alt="" width="163" height="163" title="" /></div></div>
<div class="fr width219">
  <div class="font11 gray pb10">Перейти в:</div>
  <div class="articlemenu">
    <ul>
      <li><a href="<?php echo url_for('productCard', $sf_data->getRaw('product')) ?>">Карточка товара</a></li>
      <li><a href="<?php echo url_for('productComment', $sf_data->getRaw('product')) ?>" class="current">Отзывы пользователей</a></li>
      <?php if ($hasProductStockLink): ?>
        <li class="next"><a href="<?php echo url_for('productStock', $sf_data->getRaw('product')) ?>">Где купить в магазинах</a></li>
      <?php endif ?>
    </ul>
  </div>
</div>

<?php include_component('product', 'show', array('view' => 'description', 'product' => $item)) ?>
<!-- /Goods info -->

<div class="clear pb20"></div>