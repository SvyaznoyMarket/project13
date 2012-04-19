<div class="carouseltitle carbig">
    <div class="rubrictitle"><h3>Аксессуары</h3></div>
    <?php if ($accessoryPagesNum > 1) { ?>
    <div class="scroll" data-quantity="<?php //echo $item['accessory_pager']->getNbResults() ?>">
        (страница <span>1<?php //echo $item['accessory_pager']->getPage() ?></span> из <span><?php echo $accessoryPagesNum; //echo $item['accessory_pager']->getLastPage() ?></span>)
        <a title="Предыдущие 5" class="srcoll_link_button back disabled" data-url="<?php echo url_for('product_accessory', array('product' => $sf_data->getRaw('product')->id)) ?>" href="javascript:void(0)"></a>
        <a title="Следующие 5" class="srcoll_link_button forvard" data-url="<?php echo url_for('product_accessory', array('product' => $sf_data->getRaw('product')->id)) ?>" href="javascript:void(0)"></a>
    </div>
    <?php } ?>
</div>

<div class="line pb10"></div>

<div class="clear"></div>

<div class="bigcarousel">
    <?php include_partial('productSoa/product_accessory_list', $sf_data) ?>
</div>

<div class="clear"></div>