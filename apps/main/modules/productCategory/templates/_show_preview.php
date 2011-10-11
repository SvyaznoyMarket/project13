<div class="goodsbox">

  <?php include_component('product', 'show', array('view' => 'category', 'product' => $item['product'])) ?>

  <h2><a href="<?php echo $item['url'] ?>" class="underline"><?php echo $item['name'] ?></a></h2>
  <ul>
    <li><a href="">В стиле прованс – гостиные для романтиков</a></li>
    <li><a href="">Стеллажи для коллекционеров</a></li>

    <li><a href="">Стенки и горки по цене от 2999 рублей!</a></li>
  </ul>
  <div class="ar gray"><a href="<?php echo $item['url'] ?>" class="underline">Товаров</a> (<?php echo $item['product_quantity'] ?>)</div>

  <!-- Hover -->
  <div class="boxhover">
    <b class="rt"></b><b class="lb"></b>
    <div class="rb">
      <div class="lt">

        <a href="" class="fastview">Быстрый просмотр</a>
        <?php include_component('product', 'show', array('view' => 'category', 'product' => $item['product'])) ?>

        <h2><a href="<?php echo $item['url'] ?>" class="underline"><?php echo $item['name'] ?></a></h2>
        <ul>
          <li><a href="">В стиле прованс – гостиные для романтиков</a></li>
          <li><a href="">Стеллажи для коллекционеров</a></li>

          <li><a href="">Стенки и горки по цене от 2999 рублей!</a></li>
        </ul>
        <div class="ar gray"><a href="<?php echo $item['url'] ?>" class="underline">Товаров</a> (<?php echo $item['product_quantity'] ?>)</div>
  
      </div>
    </div>
  </div>
  <!-- /Hover -->
</div>
