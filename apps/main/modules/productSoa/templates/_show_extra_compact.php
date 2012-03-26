<div class="goodsbox"<?php echo (isset($ii) && $ii > $maxPerPage) ? ' style="display:none;"' : '' ?>>

  <div class="photo">
    <a href="<?php echo url_for('productCardSoa', array('product' => $product)) ?>">
        <?php if ($product->label): ?>
        <?php foreach ($product->label as $label): ?>
            <img class="bLabels" src="<?php echo $product->getLabelUrl($label['media_image']) ?>" alt="<?php echo $label['name'] ?>">
            <?php endforeach ?>
        <?php endif ?>

    <img src="<?php echo $product->getMainPhotoUrl(2) ?>" alt="<?php echo $product->name ?> - <?php //echo $product->root_name ?>" title="<?php echo $product->name ?> - <?php //echo $product->root_name ?>" width="119" height="120" />
    </a>
  </div>

  <?php
    echo str_repeat('<span class="ratingview" style="width:13px;vertical-align:middle;display:inline-block;"></span>', round($product->rating));
    echo str_repeat('<span class="ratingview" style="width:13px;vertical-align:middle;display:inline-block;background-position:-51px 0;"></span>', 5 - round($product->rating));
  ?>

  <h3><a href="<?php echo url_for('productCardSoa', array('product' => $product->path)) ?>"><?php echo $product->name ?></a></h3>
  <div class="font18 pb10 mSmallBtns"><span class="price"><?php echo $product->price ?></span> <span class="rubl">p</span></div>
  <!-- Hover -->
  <div class="boxhover"<?php if ($product->is_insale): ?> ref="<?php echo $product->token ?>"<?php endif ?>>
    <b class="rt"></b><b class="lb"></b>

    <div class="rb">
      <div class="lt" data-url="<?php echo url_for('productCardSoa', array('product' => $product->path)) ?>">
        <!--<a href="" class="fastview">Быстрый просмотр</a>-->
        <div class="photo">
          <a href="<?php echo url_for('productCardSoa', array('product' => $product->path)) ?>">
          <img src="<?php echo $product->getMainPhotoUrl(2) ?>" alt="<?php echo $product->name ?> - <?php //echo $product->root_name ?>" title="<?php echo $product->name ?> - <?php //echo $product->root_name ?>" width="119" height="120" />
          </a>
        </div>
        <?php
          echo str_repeat('<span class="ratingview" style="width:13px;vertical-align:middle;display:inline-block;"></span>', round($product->rating));
          echo str_repeat('<span class="ratingview" style="width:13px;vertical-align:middle;display:inline-block;background-position:-51px 0;"></span>', 5 - round($product->rating));
        ?>
        <h3><a href="<?php echo url_for('productCardSoa', array('product' => $product->path)) ?>"><?php echo $product->name ?></a></h3>
        <div class="goodsbar mSmallBtns mR">
          <?php include_component('cart', 'buy_button', array('product' => $product, 'quantity' => 1, 'soa' => 1)) ?>
        </div>
        <div class="font18 pb10 mSmallBtns"><span class="price"><?php echo $product->price ?></span> <span class="rubl">p</span></div>
      </div>
    </div>
  </div>
  <!-- /Hover -->
</div>
