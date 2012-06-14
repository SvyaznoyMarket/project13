<?php
/**
 * @var $product ProductEntity
 * @var $item ProductEntity
 */
?>
<?php $empty = 0; // $empty = 0 == $productPager->getNbResults() ?>

<?php if (count($product->getKitList())): ?>
<h2 class="bold fl"><?php echo $product->getName() ?> включает в себя:</h2>

<div class="line"></div>
<div style="width: 940px; float: none; margin: 0;" class="goodslist">
  <?php $i = 0; foreach ($product->getKitList() as $kit): $i++ ?>
    <?php render_partial('product_/templates/_show_.php', array(
      'view' => 'compact',
      'show_model' => true,
      'item' => $kit->getProduct(),
      'kit' => $kit,
    )) ?>
    <?php if (0 == ($i % 4)): ?><br class="clear" /><?php endif ?>
  <?php endforeach ?>

</div>

<?php endif ?>