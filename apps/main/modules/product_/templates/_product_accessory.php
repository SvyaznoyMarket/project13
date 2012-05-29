<?php
/**
 * @var $product ProductEntity
 * @var $accessoryPagesNum int
 */
?>
<div class="carouseltitle carbig">
    <div class="rubrictitle"><h3>Аксессуары</h3></div>
    <?php if ($accessoryPagesNum > 1) { ?>
    <div class="scroll" data-quantity="<?php echo count($product->getAccessoryList()) ?>">
        (страница <span>1</span> из <span><?php echo $accessoryPagesNum ?></span>)
        <a title="Предыдущие 5" class="srcoll_link_button back disabled" data-url="<?php echo url_for('product_accessory', array('product' => $product->getToken())) ?>" href="javascript:void(0)"></a>
        <a title="Следующие 5" class="srcoll_link_button forvard" data-url="<?php echo url_for('product_accessory', array('product' => $product->getToken())) ?>" href="javascript:void(0)"></a>
    </div>
    <?php } ?>
</div>

<div class="line pb10"></div>

<div class="clear"></div>

<div class="bigcarousel">
  <?php foreach ($product->getAccessoryList() as $i => $accessory)
    render_partial('product_/templates/_show_.php', array(
      'view' => 'extra_compact',
      'item' => $accessory,
      'maxPerPage' => 5,
      'ii' => $i + 1
  )); ?>
</div>

<div class="clear"></div>