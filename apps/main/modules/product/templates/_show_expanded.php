<div class="goodsbox goodsline bNewGoodsBox">
  <div class="goodsboxlink" <?php if ($order['is_insale']): ?> ref="<?php echo $order['token'] ?>"
       data-cid="<?php echo $order['core_id'] ?>" <?php endif ?>>
    <div class="photo">
      <?php if ($order['label']): ?>
      <img class="bLabels" src="<?php echo $order['label']->getImageUrl() ?>"
           alt="<?php echo $order['label']->getName() ?>"/>
      <?php endif ?>
      <img height="160" width="160" title="<?php echo $order['name'] ?>" alt="<?php echo $order['name'] ?>"
           src="<?php echo $order['photo'] ?>">
    </div>
    <div class="info">
      <h3><a href="<?php echo $order['url'] ?>"><?php echo $order['name'] ?></a></h3>
      <span class="gray bNGB__eArt mInlineBlock">
        Артикул #<?php echo $order['article'] ?>
        <?php
        echo str_repeat('<span class="ratingview" style="width:13px;vertical-align:middle;display:inline-block;"></span>', round($order['rating']));
        echo str_repeat('<span class="ratingview" style="width:13px;vertical-align:middle;display:inline-block;background-position:-51px 0;"></span>', 5 - round($order['rating']));
        ?>
        <span class="bNGB__eDrop"><a href="<?php echo $order['url'] ?>" style="display: none"></a></span>
      </span>

      <div class="pb5 bNGB__eDesc">
        <?php include_component('product', 'property', array('product' => $item)) ?>
      </div>

      <?php if ($order['variation']): ?>

      <a href="<?php echo $order['url'] ?>">
        <div class="bListVariants">
          Доступно в разных вариантах<br>
          (<?php echo $order['variation'] ?>)
        </div>
      </a>
      <?php endif ?>

    </div>
    <div class="extrainfo">
      <span class="db font18 pb10"><b><span class="price"><?php echo $order['price'] ?></span> <span
        class="rubl">p</span></b></span>

      <div class="goodsbar mSmallBtns">
        <?php include_component('cart', 'buy_button', array('product' => $item, 'quantity' => 1)) ?>
      </div>
      <?php if ($order['is_insale']): ?>
      <noindex>
        <ul class="bNGB__eUl">
          <li><strong class="orange">Есть в наличии</strong></li>
        </ul>
      </noindex>
      <?php endif ?>
      </ul>
    </div>
  </div>
</div>
