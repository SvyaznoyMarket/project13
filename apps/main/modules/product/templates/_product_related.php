<div class="carouseltitle carbig">
  <div class="rubrictitle"><h3>С этим товаров также покупают</h3></div>
  <div class="scroll" data-quantity="<?php echo $item['related_pager']->getNbResults() ?>">
    (страница <span><?php echo $item['related_pager']->getPage() ?></span> из <span><?php echo $item['related_pager']->getLastPage() ?></span>)
    <a title="Предыдущие 3" class="back disabled" data-url="<?php echo url_for('product_related', $sf_data->getRaw('product')) ?>" href="javascript:void(0)"></a>
    <a title="Следующие 3" class="forvard" data-url="<?php echo url_for('product_related', $sf_data->getRaw('product')) ?>" href="javascript:void(0)"></a>
  </div>
</div>

<div class="line pb10"></div>

<div class="clear"></div>

<div class="bigcarousel">
  <?php foreach ($item['related'] as $i => $related): ?>
    <?php include_component('product', 'show', array('view' => 'extra_compact', 'product' => $related, 'ii' => $i * $item['related_pager']->getPage())) ?>
  <?php endforeach ?>
</div>

<div class="clear"></div>