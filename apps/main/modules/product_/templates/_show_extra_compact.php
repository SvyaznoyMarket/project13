<?php
/**
 * @var $item ProductEntity
 * @var $ii
 * @var $maxPerPage
 * @var $marker string
 */

$title = $item->getName();
if($main = $item->getMainCategory())
  $title .= ' - '.$main->getName();
$marker = isset($marker) ? $marker : null;
?>
<div class="goodsbox<?php if (isset($fixHeight)) echo ' height220' ?>"<?php echo (isset($ii) && $ii > $maxPerPage) ? ' style="display:none;"' : '' ?>>

  <div class="photo">
    <a href="<?php echo $item->getLink().(!empty($marker) ? $marker : '') ?>">
      <img src="<?php echo $item->getMediaImageUrl() ?>" alt="<?php echo $title ?>"
           title="<?php echo $title ?>" width="119" height="120"/>
    </a>
  </div>

  <?php
  echo str_repeat('<span class="ratingview" style="width:13px;vertical-align:middle;display:inline-block;"></span>', round($item->getRating()));
  echo str_repeat('<span class="ratingview" style="width:13px;vertical-align:middle;display:inline-block;background-position:-51px 0;"></span>', 5 - round($item->getRating()));
  ?>

  <h3><a href="<?php echo $item->getLink().(!empty($marker) ? $marker : '') ?>"><?php echo $item->getName() ?></a></h3>

  <div class="font18 pb10 mSmallBtns"><span class="price"><?php echo formatPrice($item->getPrice()) ?></span> <span class="rubl">p</span></div>
  <!-- Hover -->
  <div class="boxhover"<?php if ($item->getState()->getIsBuyable()): ?> ref="<?php echo $item->getToken() ?>"<?php endif ?>>
    <b class="rt"></b><b class="lb"></b>

    <div class="rb">
      <div class="lt" data-url="<?php echo $item->getLink().(!empty($marker) ? $marker : '') ?>">
        <div class="photo">
          <a href="<?php echo $item->getLink().(!empty($marker) ? $marker : '') ?>">
            <img src="<?php echo $item->getMediaImageUrl() ?>" alt="<?php echo $title ?>"
                 title="<?php echo $title ?>" width="119" height="120"/>
          </a>
        </div>
        <?php
        echo str_repeat('<span class="ratingview" style="width:13px;vertical-align:middle;display:inline-block;"></span>', round($item->getRating()));
        echo str_repeat('<span class="ratingview" style="width:13px;vertical-align:middle;display:inline-block;background-position:-51px 0;"></span>', 5 - round($item->getRating()));
        ?>
        <h3><a href="<?php echo $item->getLink().(!empty($marker) ? $marker : '') ?>"><?php echo $item->getName() ?></a></h3>

        <div class="goodsbar mSmallBtns mR">
          <?php render_partial('cart_/templates/_buy_button.php', array('item' => $item)) ?>
        </div>
        <div class="font18 pb10 mSmallBtns"><span class="price"><?php echo formatPrice($item->getPrice()) ?></span> <span class="rubl">p</span></div>
      </div>
    </div>
  </div>
  <!-- /Hover -->
</div>
