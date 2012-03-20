<div class="goodsbox goodsline bNewGoodsBox">
  <div class="goodsboxlink" <?php if ($item['is_insale']): ?> ref="<?php echo $item['token'] ?>" data-cid="<?php echo $item['core_id'] ?>" <?php endif ?>>
    <div class="photo">
      <?php if ($item['label']): ?>
        <img class="bLabels" src="<?php echo $item['label']->getImageUrl() ?>" alt="<?php echo $item['label']->getName() ?>" />
      <?php endif ?>
      <img height="160" width="160" title="<?php echo $item['name'] ?>" alt="<?php echo $item['name'] ?>" src="<?php echo $item['photo'] ?>">
    </div>
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
        <?php include_component('product', 'property', array('product' => $product)) ?>
      </div>

      <?php if ($item['variation']): ?>
      <div class="bListVariantsOutsideWrap">
        <div class="bListVariantsInsideWrap">
          <a href="<?php echo $item['url'] ?>">
            <div class="bListVariants">
              <span>
                Доступно в разных вариантах<br>
                (<?php echo $item['variation'] ?>)
                <span></span>
              </span>
            </div>
          </a>
        </div>
      </div>
      <?php endif ?>

    </div>
    <div class="extrainfo">
      <span class="db font18 pb10"><b><span class="price"><?php echo $item['price'] ?></span> <span class="rubl">p</span></b></span>
      <div class="goodsbar mSmallBtns">
        <?php include_component('cart', 'buy_button', array('product' => $product, 'quantity' => 1)) ?>
      </div>
        <?php if ($item['is_insale']): ?>
          <noindex><ul class="bNGB__eUl"><li><strong class="orange">Есть в наличии</strong></li></ul></noindex>
        <?php endif ?>
      </ul>
    </div>
  </div>
</div>
