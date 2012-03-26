<div class="carouseltitle carbig">
  <div class="rubrictitle"><h3>С этим товаром также покупают</h3></div>

  <div class="scroll" data-quantity="<?php echo $item['related_pager']->getNbResults() ?>">
    (страница <span><?php echo $item['related_pager']->getPage() ?></span> из
    <span><?php echo $item['related_pager']->getLastPage() ?></span>)
    <a title="Предыдущие 3" class="srcoll_link_button back disabled"
       data-url="<?php echo url_for('product_related', $sf_data->getRaw('product')) ?>" href="javascript:void(0)"></a>
    <a title="Следующие 3" class="srcoll_link_button forvard"
       data-url="<?php echo url_for('product_related', $sf_data->getRaw('product')) ?>" href="javascript:void(0)"></a>
  </div>
</div>

<div class="line pb10"></div>

<div class="clear"></div>

<div class="bigcarousel">
  <?php include_partial('product/product_related_list', $sf_data) ?>
</div>

<div class="clear"></div>
