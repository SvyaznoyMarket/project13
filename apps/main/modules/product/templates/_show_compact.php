<div class="goodsbox">
  <div class="photo"><!--<i class="new" title="Новинка"></i>--><img class="noneleminate" src="<?php echo $item['photo'] ?>" alt="" title="" width="160" height="160" /></div>
  <span class="ratingview"></span>
  <h3><a href="<?php echo $item['url'] ?>"><?php echo $item['name'] ?></a></h3>

  <div class="font18 pb10"><?php echo $item['price'] ?> <span class="rubl">p</span></div>

  <!-- Hover -->
  <div class="boxhover" ref="<?php echo $item['product']->token ?>">
    <b class="rt"></b><b class="lb"></b>
    <div class="rb">
      <div class="lt"  onclick="window.location.href='<?php echo $item['url'] ?>'">
        <!--a href="" class="fastview">Быстрый просмотр</a-->

        <div class="photo"><!--<i class="new" title="Новинка"></i>--><img class="noneleminate" src="<?php echo $item['photo'] ?>" alt="" title="" width="160" height="160" /></div>
        <span class="ratingview"></span>
        <h3><a href="<?php echo $item['url'] ?>"><?php echo $item['name'] ?></a></h3>
        <div class="font18 pb10"><span class="price"><?php echo $item['price'] ?></span> <span class="rubl">p</span></div>
        <div class="goodsbar">
          <?php include_component('cart', 'buy_button', array('product' => $item['product'], 'quantity' => 1)) ?>
          <?php include_component('userDelayedProduct', 'add_button', array('product' => $product)) ?>
          <?php include_component('userProductCompare', 'button', array('product' => $product)) ?>
        </div>
      </div>
    </div>
  </div>
  <!-- /Hover -->

</div>