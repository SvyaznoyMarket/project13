<div class="goodsbox"<?php echo (isset($ii) && ($ii > $maxPerPage)) ? ' style="display:none;"' : '' ?>>

  <div class="photo">
    <a href="<?php echo $order['url'] ?>">
      <?php if ($order['label']): ?>
      <img class="bLabels" src="<?php echo $order['label']->getImageUrl() ?>"
           alt="<?php echo $order['label']->getName() ?>"/>
      <?php endif ?>
      <img src="<?php echo $order['photo'] ?>" alt="<?php echo $order['name'] ?> - <?php echo $order['root_name'] ?>"
           title="<?php echo $order['name'] ?> - <?php echo $order['root_name'] ?>" width="160" height="160"/>
    </a>
  </div>

  <?php
  echo str_repeat('<span class="ratingview" style="width:13px;vertical-align:middle;display:inline-block;"></span>', round($order['rating']));
  echo str_repeat('<span class="ratingview" style="width:13px;vertical-align:middle;display:inline-block;background-position:-51px 0;"></span>', 5 - round($order['rating']));
  ?>

  <h3><a href="<?php echo $order['url'] ?>"><?php echo $order['name'] ?></a></h3>

  <div class="font18 pb10 mSmallBtns"><span class="price"><?php echo $order['price'] ?></span> <span
    class="rubl">p</span></div>
  <?php if ($order['variation']): ?>
  <div class='bListVariants mShort'>Доступно в разных вариантах</div>
  <?php endif ?>
  <!-- Hover -->
  <div class="boxhover"<?php if ($order['is_insale']): ?> ref="<?php echo $order['token'] ?>"<?php endif ?>>
    <b class="rt"></b><b class="lb"></b>

    <div class="rb">
      <div class="lt" data-url="<?php echo $order['url'] ?>">
        <!--<a href="" class="fastview">Быстрый просмотр</a>-->
        <div class="photo">
          <a href="<?php echo $order['url'] ?>">
            <?php if ($order['label']): ?>
            <img class="bLabels" src="<?php echo $order['label']->getImageUrl() ?>"
                 alt="<?php echo $order['label']->getName() ?>"/>
            <?php endif ?>
            <img src="<?php echo $order['photo'] ?>" alt="<?php echo $order['name'] ?> - <?php echo $order['root_name'] ?>"
                 title="<?php echo $order['name'] ?> - <?php echo $order['root_name'] ?>" width="160" height="160"/>
          </a>
        </div>
        <?php
        echo str_repeat('<span class="ratingview" style="width:13px;vertical-align:middle;display:inline-block;"></span>', round($order['rating']));
        echo str_repeat('<span class="ratingview" style="width:13px;vertical-align:middle;display:inline-block;background-position:-51px 0;"></span>', 5 - round($order['rating']));
        ?>
        <h3><a href="<?php echo $order['url'] ?>"><?php echo $order['name'] ?></a></h3>

        <div class="goodsbar mSmallBtns mR">
          <?php include_component('cart', 'buy_button', array('product' => $item, 'quantity' => 1)) ?>
        </div>
        <div class="font18 pb10 mSmallBtns"><span class="price"><?php echo $order['price'] ?></span> <span
          class="rubl">p</span></div>
        <?php if ($order['variation']): ?>
        <div class='bListVariants mShort'>Доступно в разных вариантах</div>
        <?php endif ?>
      </div>
    </div>
  </div>
  <!-- /Hover -->
</div>
