<div class="goodsbox goodsline height170">
  <div class="goodsboxlink">
    <div class="photo"><!--<i title="Новинка" class="new"></i>--><img src="<?php echo $item['photo'] ?>" alt="" title="" width="160" height="160" /></div>
    <div class="info">

        <?php
		echo str_repeat('<span class="ratingview" style="width:13px;vertical-align:middle;display:inline-block;"></span>', round($item['rating']));
		echo str_repeat('<span class="ratingview" style="width:13px;vertical-align:middle;display:inline-block;background-position:-51px 0;"></span>', 5 - round($item['rating']));
		?>
      <h3 class="bolder"><a href="<?php echo $item['url'] ?>"><?php echo $item['name'] ?></a></h3>

      <?php include_component('product', 'property', array('product' => $item['product'])) ?>

      <span class="gray">Артикул #<?php echo $item['article'] ?></span>
    </div>
    <div class="extrainfo">
      <span class="db font18 pb10"><span class="price"><?php echo $item['price'] ?></span> <span class="rubl">p</span></span>

      <ul>
        <?php if ($item['is_insale']):  ?>
          <noindex><li><strong class="orange">Есть в наличии</strong></li></noindex>
          <li>Доставим в течение 24 часов</li>
        <?php endif ?>
      </ul>

    </div>
  </div>

  <!-- Hover -->
  <div class="boxhover"<?php if ($item['is_insale']): ?> ref="<?php echo $item['token'] ?>"<?php endif ?>>
    <!--a href="" class="fastview">Быстрый просмотр</a-->
    <div class="goodsbar">
      <?php include_component('cart', 'buy_button', array('product' => $item['product'], 'quantity' => 1)) ?>
      <?php include_component('userDelayedProduct', 'add_button', array('product' => $item['product'])) ?>
      <?php include_component('userProductCompare', 'button', array('product' => $item['product'])) ?>
    </div>
    <b class="rt"></b><b class="lb"></b>

    <div class="rb">
      <div class="lt" data-url="<?php echo $item['url'] ?>">
        <div class="goodsboxlink"><!-- onclick="window.open('http://')"-->
          <div class="photo"><!--<i title="Новинка" class="new"></i>-->
           <a style="display:inline;" href="<?php echo $item['url'] ?>"><img src="<?php echo $item['photo'] ?>" alt="" title="" width="160" height="160" /></div></a>
          <div class="info">
            <?php	echo str_repeat('<span class="ratingview" style="width:13px;vertical-align:middle;display:inline-block;"></span>', round($item['rating'])) ?>
            <?php	echo str_repeat('<span class="ratingview" style="width:13px;vertical-align:middle;display:inline-block;background-position:-51px 0;"></span>', 5 - round($item['rating'])) ?>
            <h3 class="bolder"><a href="<?php echo $item['url'] ?>"><?php echo $item['name'] ?></a></h3>

            <?php include_component('product', 'property', array('product' => $item['product'])) ?>

            <span class="gray">Артикул #<?php echo $item['article'] ?></span>
          </div>
          <div class="extrainfo">
            <span class="db font18 pb10"><span class="price"><?php echo $item['price'] ?></span> <span class="rubl">p</span></span>
            <ul>
              <noindex><li><strong class="orange">Есть в наличии</strong></li></noindex>
              <li>Доставим в течение 24 часов</li>
            </ul>
          </div>
        </div>
        <div class="clear"></div>
      </div>
    </div>
  </div>
  <!-- /Hover -->

</div>
