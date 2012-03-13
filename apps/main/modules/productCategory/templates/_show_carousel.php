<?php $view = $item['has_line'] ? 'line' : 'compact' ?>
<!-- Carousel -->
<div class="carouseltitle">
  <div class="rubrictitle"><h2><a href="<?php echo $item['url'] ?>" class="underline"><?php echo $item['name']?></a></h2></div>

  <?php if ($item['product_quantity'] > 3): ?>
  <div class="scroll">
    <span><a href='<?php echo $item['url'] ?>' class='srcoll_link'>посмотреть все</a></span><span class="jshm">( <?php echo $item['product_quantity']?> )</span>
    <a href="javascript:void(0)" data-url="<?php echo $item['carousel_data_url'] ?>" class="srcoll_link_button back disabled" title="Предыдущие 3"></a>
    <a href="javascript:void(0)" data-url="<?php echo $item['carousel_data_url'] ?>" class="srcoll_link_button forvard" title="Следующие 3"></a>
  </div>
  <?php endif ?>
</div>

<div class="line pb10"></div>

<div class="clear"></div>

<div class="carousel">
  <?php $i = 0; foreach ($item['product_list'] as $product): $i++; ?>
    <?php include_component('product', 'show', array('view' => $view, 'ii' => $i, 'product' => $product)) ?>
  <?php endforeach ?>
</div>
<!-- Carousel -->