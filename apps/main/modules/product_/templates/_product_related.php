<?php
/**
 * @var $item ProductEntity
 * @var $relatedPagesNum int
 */
?>
<?php if (sfConfig::get('app_related_product_additional')): ?>

<div class="carouseltitle carbig">
  <div class="rubrictitle"><h3>С этим товаром также покупают</h3></div>
  <?php if ($relatedPagesNum > 1) { ?>
  <div class="scroll" data-quantity="<?php echo count($item->getRelatedList())?>">
    (страница <span>1</span> из
    <span><?php echo $relatedPagesNum ?></span>)
    <a title="Предыдущие 5" class="srcoll_link_button back disabled"
       data-url="<?php echo url_for('product_related', array('product' => $item->getToken())) ?>"
       href="javascript:void(0)"></a>
    <a title="Следующие 5" class="srcoll_link_button forvard"
       data-url="<?php echo url_for('product_related', array('product' => $item->getToken())) ?>"
       href="javascript:void(0)"></a>
  </div>
  <?php } ?>

</div>

<div class="line pb10"></div>

<div class="clear"></div>

<div class="bigcarousel">
  <?php foreach ($item->getRelatedList() as $i => $related)
    render_partial('product_/templates/_show_.php', array(
      'view' => 'extra_compact',
      'item' => $related,
      'maxPerPage' => 5,
      'ii' => $i + 1
    ));
  ?>
</div>

<div class="clear"></div>

<?php endif ?>