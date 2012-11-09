<?php
/**
 * @var $product ProductEntity
 * @var $pagesNum int
 * @var $marker string
 */
?>

<?php
$pagesNum = ceil(count($products) / 5);
$marker = isset($marker) ? $marker : null;
?>

<div class="carouseltitle carbig">
  <div class="rubrictitle"><h3><?php echo $title ?></h3></div>
  <?php if ($pagesNum > 1) { ?>
  <div class="scroll" data-quantity="<?php echo count($products) ?>">
    (страница <span>1</span> из <span><?php echo $pagesNum ?></span>)
    <a title="Предыдущие 5" class="srcoll_link_button back disabled" data-url="" href="javascript:void(0)"></a>
    <a title="Следующие 5" class="srcoll_link_button forvard" data-url="" href="javascript:void(0)"></a>
  </div>
  <?php } ?>
</div>

<div class="line pb10"></div>

<div class="clear"></div>

<div class="bigcarousel">
  <?php $i = 1; foreach ($products as $product): ?>
    <?php render_partial('product_/templates/_show_.php', array(
      'view'       => 'extra_compact',
      'item'       => $product,
      'maxPerPage' => 5,
      'fixHeight'  => true,
      'ii'         => $i++,
      'marker'     => $marker,
      'gaEvent'    => 'SmartEngine',
    )) ?>
  <?php endforeach ?>
</div>

<div class="clear"></div>