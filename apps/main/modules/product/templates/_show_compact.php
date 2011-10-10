<div class="goodsbox">
  <a href="" class="fastview">Быстрый просмотр</a>
  
  <a href="<?php echo url_for('productCard', $item['product'], array('absolute' => true,)) ?>" class="goodsboxlink">
    <span class="photo"><!--i title="Бестселлер" class="bestseller"></i--><img src="http://core.ent3.ru/upload/pic/2/200/<?php echo $item['product']['Photo'][0]['resource'] ?>" alt="" title="" width="160" height="160" /></span>
    <span class="ratingview"></span>
    <span class="link"><?php echo $item['name'] ?></span>
    <span class="db font18 pb10"><?php echo $item['price'] ?> <span class="rubl">&#8399;</span></span>
  </a>

  <div class="extrabox">
    <div class="doodsbar">
      <?php include_component('cart', 'buy_button', array('product' => $item['product'], 'quantity' => 1)) ?>
      <?php include_component('userDelayedProduct', 'add_button', array('product' => $product)) ?>
      <?php include_component('userProductCompare', 'button', array('product' => $product)) ?>
    </div>
  </div>

</div>
