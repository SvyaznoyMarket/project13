<div class="carouseltitle">
  <div class="rubrictitle"><h3>С этим товаров часто покупают</h3></div>
  <div class="scroll">
    (страница <span>1</span> из <span><?php echo ceil($item['related_quantity'] / 3) ?></span>)
    <a title="Предыдущие 3" class="back disabled" href=""></a>
    <a title="Следующие 3" class="forvard" href=""></a>
  </div>
</div>

<div class="line pb10"></div>

<div class="clear"></div>

<div class="bigcarousel">
  <?php foreach ($item['related'] as $related): ?>
    <?php include_component('product', 'show', array('view' => 'extra_compact', 'product' => $related)) ?>
  <?php endforeach ?>
</div>

<div class="clear"></div>