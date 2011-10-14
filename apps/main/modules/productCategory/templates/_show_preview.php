<div class="goodsbox">

  <?php include_component('product', 'show', array('view' => 'category', 'category' => $productCategory, 'product' => $item['product'])) ?>

  <h2><a href="<?php echo $item['url'] ?>" class="underline"><?php echo $item['name'] ?></a></h2>
  <ul>
	<?php foreach ($item['links'] as $link): ?>
    <li><a href="<?php echo $link['url'] ?>"><?php echo $link['name'] ?></a></li>
	<?php endforeach ?>
  </ul>
  <div class="ar gray"><a href="<?php echo $item['url'] ?>" class="underline">Товаров</a> (<?php echo $item['product_quantity'] ?>)</div>

  <!-- Hover -->
  <div class="boxhover">
    <b class="rt"></b><b class="lb"></b>
    <div class="rb">
      <div class="lt">

<!--        <a href="" class="fastview">Быстрый просмотр</a>-->
        <?php include_component('product', 'show', array('view' => 'category', 'category' => $productCategory, 'product' => $item['product'])) ?>

        <h2><a href="<?php echo $item['url'] ?>" class="underline"><?php echo $item['name'] ?></a></h2>
        <ul>
          <?php foreach ($item['links'] as $link): ?>
		  <li><a href="<?php echo $link['url'] ?>"><?php echo $link['name'] ?></a></li>
		  <?php endforeach ?>
        </ul>
        <div class="ar gray"><a href="<?php echo $item['url'] ?>" class="underline">Товаров</a> (<?php echo $item['product_quantity'] ?>)</div>
  
      </div>
    </div>
  </div>
  <!-- /Hover -->
</div>
