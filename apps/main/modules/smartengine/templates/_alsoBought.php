<?php
/**
 * @var $product ProductEntity
 * @var $accessoryPagesNum int
 */
?>
<div class="carouseltitle carbig">
  <div class="rubrictitle"><h3>Also bought</h3></div>
</div>

<div class="line pb10"></div>

<div class="clear"></div>

<div class="bigcarousel">
  <?php $i = 0; foreach ($products as $product): ?>
    <?php render_partial('product_/templates/_show_.php', array(
      'view'       => 'extra_compact',
      'item'       => $product,
      'maxPerPage' => 20,
      'fixHeight'  => true,
    )) ?>
  <?php endforeach ?>
</div>

<div class="clear"></div>