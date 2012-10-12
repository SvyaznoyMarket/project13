<?php
/**
 * @var $page \View\DefaultLayout
 * @var $product \Model\Product\CompactEntity
 * @var $isHidden bool
 * @var $maxHeight bool
 * */
?>

<?php
$isHidden = isset($isHidden) && $isHidden;
$maxHeight = isset($maxHeight) && $maxHeight;
?>

<div class="goodsbox<? if ($maxHeight): ?> height220<? endif ?>"<? if ($isHidden): ?> style="display:none;"<? endif ?>>

  <div class="photo">
    <a href="<?php echo $product->getLink() ?>">
      <img src="<?php echo $product->getImageUrl() ?>" alt="<?php echo $product->getNameWithCategory() ?>"
           title="<?php echo $product->getNameWithCategory() ?>" width="119" height="120"/>
    </a>
  </div>

  <?php
  echo str_repeat('<span class="ratingview" style="width:13px;vertical-align:middle;display:inline-block;"></span>', round($product->getRating()));
  echo str_repeat('<span class="ratingview" style="width:13px;vertical-align:middle;display:inline-block;background-position:-51px 0;"></span>', 5 - round($product->getRating()));
  ?>

  <h3><a href="<?php echo $product->getLink() ?>"><?php echo $product->getName() ?></a></h3>

  <div class="font18 pb10 mSmallBtns"><span class="price"><?php echo $page->helper->formatPrice($product->getPrice()) ?></span> <span class="rubl">p</span></div>
  <!-- Hover -->
  <div class="boxhover"<?php if ($product->getState()->getIsBuyable()): ?> ref="<?php echo $product->getToken() ?>"<?php endif ?>>
    <b class="rt"></b><b class="lb"></b>

    <div class="rb">
      <div class="lt" data-url="<?php echo $product->getLink() ?>">
        <div class="photo">
          <a href="<?php echo $product->getLink() ?>">
            <img src="<?php echo $product->getImageUrl() ?>" alt="<?php echo $product->getNameWithCategory() ?>"
                 title="<?php echo $product->getNameWithCategory() ?>" width="119" height="120"/>
          </a>
        </div>
        <?php
        echo str_repeat('<span class="ratingview" style="width:13px;vertical-align:middle;display:inline-block;"></span>', round($product->getRating()));
        echo str_repeat('<span class="ratingview" style="width:13px;vertical-align:middle;display:inline-block;background-position:-51px 0;"></span>', 5 - round($product->getRating()));
        ?>
        <h3><a href="<?php echo $product->getLink() ?>"><?php echo $product->getName() ?></a></h3>

        <div class="goodsbar mSmallBtns mR">
          <?php echo $page->render('cart/_button', array('product' => $product)) ?>
        </div>
        <div class="font18 pb10 mSmallBtns"><span class="price"><?php echo $page->helper->formatPrice($product->getPrice()) ?></span> <span class="rubl">p</span></div>
      </div>
    </div>
  </div>
  <!-- /Hover -->
</div>
