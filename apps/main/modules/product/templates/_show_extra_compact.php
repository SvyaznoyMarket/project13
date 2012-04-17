<div class="goodsbox"<?php echo (isset($ii) && $ii > $maxPerPage) ? ' style="display:none;"' : '' ?>>

  <div class="photo">
    <a href="<?php echo $item['url'] ?>">
      <img src="<?php echo $item['photo'] ?>" alt="<?php echo $item['name'] ?> - <?php echo $item['root_name'] ?>"
           title="<?php echo $item['name'] ?> - <?php echo $item['root_name'] ?>" width="119" height="120"/>
    </a>
  </div>

  <?php
  echo str_repeat('<span class="ratingview" style="width:13px;vertical-align:middle;display:inline-block;"></span>', round($item['rating']));
  echo str_repeat('<span class="ratingview" style="width:13px;vertical-align:middle;display:inline-block;background-position:-51px 0;"></span>', 5 - round($item['rating']));
  ?>

  <h3><a href="<?php echo $item['url'] ?>"><?php echo $item['name'] ?></a></h3>

  <div class="font18 pb10 mSmallBtns"><span class="price"><?php echo $item['price'] ?></span> <span
    class="rubl">p</span></div>
  <!-- Hover -->
  <div class="boxhover"<?php if ($item['is_insale']): ?> ref="<?php echo $item['token'] ?>"<?php endif ?>>
    <b class="rt"></b><b class="lb"></b>

    <div class="rb">
      <div class="lt" data-url="<?php echo $item['url'] ?>">
        <!--<a href="" class="fastview">Быстрый просмотр</a>-->
        <div class="photo">
          <a href="<?php echo $item['url'] ?>">
            <img src="<?php echo $item['photo'] ?>" alt="<?php echo $item['name'] ?> - <?php echo $item['root_name'] ?>"
                 title="<?php echo $item['name'] ?> - <?php echo $item['root_name'] ?>" width="119" height="120"/>
          </a>
        </div>
        <?php
        echo str_repeat('<span class="ratingview" style="width:13px;vertical-align:middle;display:inline-block;"></span>', round($item['rating']));
        echo str_repeat('<span class="ratingview" style="width:13px;vertical-align:middle;display:inline-block;background-position:-51px 0;"></span>', 5 - round($item['rating']));
        ?>
        <h3><a href="<?php echo $item['url'] ?>"><?php echo $item['name'] ?></a></h3>

        <div class="goodsbar mSmallBtns mR">
          <?php include_component('cart', 'buy_button', array('product' => $product->core_id, 'quantity' => 1)) ?>
        </div>
        <div class="font18 pb10 mSmallBtns"><span class="price"><?php echo $item['price'] ?></span> <span
          class="rubl">p</span></div>
      </div>
    </div>
  </div>
  <!-- /Hover -->
</div>
