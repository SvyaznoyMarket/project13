<div class="carouseltitle carbig">
  <div class="rubrictitle"><h3>Аксессуары</h3></div>

  <div class="scroll" data-quantity="<?php echo $item['accessory_pager']->getNbResults() ?>">
    (страница <span><?php echo $item['accessory_pager']->getPage() ?></span> из
    <span><?php echo $item['accessory_pager']->getLastPage() ?></span>)
    <a title="Предыдущие 3" class="srcoll_link_button back disabled"
       data-url="<?php echo url_for('product_accessory', $sf_data->getRaw('product')) ?>" href="javascript:void(0)"></a>
    <a title="Следующие 3" class="srcoll_link_button forvard"
       data-url="<?php echo url_for('product_accessory', $sf_data->getRaw('product')) ?>" href="javascript:void(0)"></a>
  </div>
</div>

<div class="line pb10"></div>

<div class="clear"></div>

<div class="bigcarousel">
  <?php include_partial('product/product_accessory_list', $sf_data) ?>
</div>

<div class="clear"></div>