<div class="goodsbox"<?php echo (isset($ii) && $ii > 3) ? ' style="display:none;"' : '' ?>>

  <div class="photo">
    <img src="<?php echo $item['photo'] ?>" alt="<?php echo $item['name'] ?> - <?php echo $item['root_name'] ?>" title="<?php echo $item['name'] ?> - <?php echo $item['root_name'] ?>" width="160" height="160" />
  </div>

  <span class="ratingview" data-rating="<?php echo round($item['rating']) ?>"></span>

  <h3><a href="<?php echo $item['url'] ?>"><?php echo $item['name'] ?></a></h3>
  <div class="font18 pb10 mSmallBtns"><span class="price"><?php echo $item['price'] ?></span> <span class="rubl">p</span></div>
  <!-- Hover -->
  <div class="boxhover"<?php if ($item['is_insale']): ?> ref="<?php echo $item['token'] ?>"<?php endif ?>>
    <b class="rt"></b><b class="lb"></b>

    <div class="rb">
      <div class="lt" data-url="<?php echo $item['url'] ?>">
        <!--<a href="" class="fastview">Быстрый просмотр</a>-->
        <div class="photo">
          <img src="<?php echo $item['photo'] ?>" alt="<?php echo $item['name'] ?> - <?php echo $item['root_name'] ?>" title="<?php echo $item['name'] ?> - <?php echo $item['root_name'] ?>" width="160" height="160" />
        </div>
        <span class="ratingview" data-rating="<?php echo round($item['rating']) ?>"></span>
        <h3><a href="<?php echo $item['url'] ?>"><?php echo $item['name'] ?></a></h3>
        <div class="goodsbar mSmallBtns mR">
          <?php include_component('cart', 'buy_button', array('product' => $product, 'quantity' => 1)) ?>
        </div>
        <div class="font18 pb10 mSmallBtns"><span class="price"><?php echo $item['price'] ?></span> <span class="rubl">p</span></div>
      </div>
    </div>
  </div>
  <!-- /Hover -->
</div>
