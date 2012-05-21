<div class="goodsbox goodsline bNewGoodsBox">
  <div class="goodsboxlink" <?php if ($item['is_insale']): ?> ref="<?php echo $item['token'] ?>"
       data-cid="<?php echo $item['core_id'] ?>" <?php endif ?>>
    <div class="photo"><img height="160" width="160" title="<?php echo $item['name'] ?>"
                            alt="<?php echo $item['name'] ?>" src="<?php echo $item['photo'] ?>"></div>
    <div class="info">
      <h3><a href="<?php echo $item['url'] ?>"><?php echo $item['name'] ?></a></h3>
      <span class="gray bNGB__eArt mInlineBlock">
        Артикул #<?php echo $item['article'] ?>
        <?php
        echo str_repeat('<span class="ratingview" style="width:13px;vertical-align:middle;display:inline-block;"></span>', round($item['rating']));
        echo str_repeat('<span class="ratingview" style="width:13px;vertical-align:middle;display:inline-block;background-position:-51px 0;"></span>', 5 - round($item['rating']));
        ?>
        <span class="bNGB__eDrop"><a href="<?php echo $item['url'] ?>" style="display: none"></a></span>
      </span>

      <div class="pb5 bNGB__eDesc">
        <?php include_component('productSoa', 'property', array('product' => $product)) ?>
      </div>

    </div>
    <div class="extrainfo">
      <span class="db font18 pb10"><b><span class="price"><?php echo $item['price'] ?></span> <span
        class="rubl">p</span></b></span>

      <div class="goodsbar mSmallBtns">
        <?php include_component('cart', 'buy_button', array('product' => $product, 'quantity' => 1, 'soa' => 1)) ?>
      </div>
      <?php if ($item['is_insale']): ?>
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



<?php if (false): ?>
<div class="goodsbox goodsline height170">
  <div class="goodsboxlink">
    <div class="photo"><!--<i title="Новинка" class="new"></i>--><img src="<?php echo $item['photo'] ?>" alt="" title=""
                                                                      width="160" height="160"/></div>
    <div class="info">

      <?php
      echo str_repeat('<span class="ratingview" style="width:13px;vertical-align:middle;display:inline-block;"></span>', round($item['rating']));
      echo str_repeat('<span class="ratingview" style="width:13px;vertical-align:middle;display:inline-block;background-position:-51px 0;"></span>', 5 - round($item['rating']));
      ?>
      <h3 class="bolder"><a href="<?php echo $item['url'] ?>"><?php echo $item['name'] ?></a></h3>

      <?php include_component('productSoa', 'property', array('product' => $product)) ?>

      <span class="gray">Артикул #<?php echo $item['article'] ?></span>
    </div>
    <div class="extrainfo">
      <span class="db font18 pb10"><span class="price"><?php echo $item['price'] ?></span> <span
        class="rubl">p</span></span>

      <?php if ($item['is_insale']): ?>
      <noindex>
        <ul>
          <li><strong class="orange">Есть в наличии</strong></li>
        </ul>
      </noindex>
      <?php endif ?>

    </div>
  </div>

  <!-- Hover -->
  <div class="boxhover"<?php if ($item['is_insale']): ?> ref="<?php echo $item['token'] ?>"
       data-cid="<?php echo $item['core_id'] ?>" <?php endif ?>>
    <!--a href="" class="fastview">Быстрый просмотр</a-->
    <div class="goodsbar">
      <?php include_component('cart', 'buy_button', array('product' => $product, 'quantity' => 1, 'soa' => 1)) ?>
      <?php include_component('userProductCompare', 'button', array('product' => $product)) ?>
    </div>
    <b class="rt"></b><b class="lb"></b>

    <div class="rb">
      <div class="lt" data-url="<?php echo $item['url'] ?>">
        <div class="goodsboxlink"><!-- onclick="window.open('http://')"-->
          <div class="photo"><!--<i title="Новинка" class="new"></i>-->
            <a style="display:inline;" href="<?php echo $item['url'] ?>"><img src="<?php echo $item['photo'] ?>" alt=""
                                                                              title="" width="160" height="160"/></div>
          </a>
          <div class="info">
            <?php echo str_repeat('<span class="ratingview" style="width:13px;vertical-align:middle;display:inline-block;"></span>', round($item['rating'])) ?>
            <?php echo str_repeat('<span class="ratingview" style="width:13px;vertical-align:middle;display:inline-block;background-position:-51px 0;"></span>', 5 - round($item['rating'])) ?>
            <h3 class="bolder"><a href="<?php echo $item['url'] ?>"><?php echo $item['name'] ?></a></h3>

            <?php include_component('productSoa', 'property', array('product' => $product)) ?>

            <span class="gray">Артикул #<?php echo $item['article'] ?></span>
          </div>
          <div class="extrainfo">
            <span class="db font18 pb10"><span class="price"><?php echo $item['price'] ?></span> <span
              class="rubl">p</span></span>
            <?php if ($item['is_insale']): ?>
            <noindex>
              <ul>
                <li><strong class="orange">Есть в наличии</strong></li>
              </ul>
            </noindex>
            <?php endif ?>
          </div>
        </div>
        <div class="clear"></div>
      </div>
    </div>
  </div>
  <!-- /Hover -->

</div>

<?php endif ?>