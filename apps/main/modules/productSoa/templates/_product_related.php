<div class="carouseltitle carbig">
  <div class="rubrictitle"><h3>С этим товаром также покупают</h3></div>
  <?php $product = $sf_data->getRaw('product'); ?>
  <?php if ($relatedPagesNum > 1) { ?>
        <div class="scroll" data-quantity="<?php //echo $item['related_pager']->getNbResults() ?>">
        (страница <span>1<?php //echo $item['related_pager']->getPage() ?></span> из <span><?php echo $relatedPagesNum; //echo $item['related_pager']->getLastPage() ?></span>)
        <a title="Предыдущие 5" class="srcoll_link_button back disabled" data-url="<?php echo url_for('product_related', array('product' => $product->id)) ?>" href="javascript:void(0)"></a>
        <a title="Следующие 5" class="srcoll_link_button forvard" data-url="<?php echo url_for('product_related', array('product' => $product->id)) ?>" href="javascript:void(0)"></a>
        </div>
  <?php } ?>

</div>

<div class="line pb10"></div>

<div class="clear"></div>

<div class="bigcarousel">
  <?php include_partial('productSoa/product_related_list', $sf_data) ?>
</div>

<div class="clear"></div>