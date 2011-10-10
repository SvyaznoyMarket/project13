<!-- Carousel -->
<div class="carouseltitle">
  <div class="rubrictitle"><h2><a href="<?php echo $item['url'] ?>" class="underline"><?php echo $item['name'] ?></a></h2> <strong class="orange font09">(<?php echo $item['product_quantity'] ?>)</strong></div>

  <div class="scroll">
    (страница <span>1</span> из <span>10</span>)
    <a href="" class="back disabled" title="Предыдущие 3"></a>
    <a href="" class="forvard" title="Следующие 3"></a>
  </div>
</div>

<div class="line pb10"></div>

<div class="clear"></div>

<div class="carousel">
<?php $i = 0; foreach ($item['product_list'] as $product): $i++; ?>
  <?php include_component('product', 'show', array('view' => 'compact', 'product' => $product)) ?>
<?php endforeach ?>

</div>
<!-- Carousel -->