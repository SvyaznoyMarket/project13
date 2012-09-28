<?php
/**
 * @var $ii
 * @var $maxPerPage
 * @var ProductEntity $item
 * @var ProductKitEntity $kit
 */

$show_model = (isset($show_model)?$show_model:true) && $item->getModel() && count($item->getModel()->getPropertyList());
?>
<div class="goodsbox"<?php echo (isset($ii, $maxPerPage) && ($ii > $maxPerPage)) ? ' style="display:none;"' : '' ?>>

  <div class="photo">
    <a href="<?php echo $item->getLink() ?>">
      <?php if (!empty($kit) && $kit->getQuantity()) { ?>
        <div class="bLabelsQuantity" src="/images/quantity_shild.png"><?php echo $kit->getQuantity(); ?> шт.</div>
      <?php } ?>
      <?php if ($label = $item->getMainLabel()): ?>
      <img class="bLabels" src="<?php echo $label->getImageUrl() ?>" alt="<?php echo $label->getName() ?>"/>
      <?php endif; ?>
      <?php $title = $item->getName(); // @todo create getTitle method, or check is category isset ?>
      <?php if($item->getMainCategory()) $title .= ' - ' . $item->getMainCategory()->getName();?>
      <img class="mainImg" src="<?php echo $item->getMediaImageUrl(2) ?>"
           alt="<?php echo $title ?>"
           title="<?php echo $title ?>"
           width="160" height="160"/>
    </a>
  </div>

  <?php
  echo str_repeat('<span class="ratingview" style="width:13px;vertical-align:middle;display:inline-block;"></span>', round($item->getRating()));
  echo str_repeat('<span class="ratingview" style="width:13px;vertical-align:middle;display:inline-block;background-position:-51px 0;"></span>', 5 - round($item->getRating()));
  ?>

  <h3><a href="<?php echo $item->getLink() ?>"><?php echo $item->getName() ?></a></h3>
 
  <div class="font18 pb10 mSmallBtns"><span class="price"><?php echo formatPrice($item->getPrice()) ?></span> <span
    class="rubl">p</span></div>
  <?php if ($show_model): ?>
  <a href="<?php echo $item->getLink() ?>">
    <div class="bListVariants">
      Доступно в разных вариантах<br>
      (<?php echo $item->getModel()->getVariations() ?>)
    </div>
  </a>
  <?php endif ?>
  <!-- Hover -->
  <div class="boxhover"<?php if ($item->getIsBuyable()): ?> ref="<?php echo $item->getToken() ?>"<?php endif ?>>
    <b class="rt"></b><b class="lb"></b>

    <div class="rb">
      <div class="lt" data-url="<?php echo $item->getLink() ?>">
        <!--<a href="" class="fastview">Быстрый просмотр</a>-->

       <div class="photo"> 
          <a href="<?php echo $item->getLink() ?>">
            <?php if (!empty($kit) && $kit->getQuantity()) { ?>
              <!--<div class="bLabelsQuantity" src="/images/quantity_shild.png"><?php echo $kit->getQuantity(); ?> шт.</div>-->
            <?php } ?>
            <?php if ($label = $item->getMainLabel()): ?>
            <!--<img class="bLabels" src="<?php echo $label->getImageUrl() ?>" alt="<?php echo $label->getName() ?>"/>-->
            <?php endif; ?>
            <img class="mainImg" src="<?php echo $item->getMediaImageUrl(2) ?>" alt="<?php echo $title ?>" title="<?php echo $title ?>"
                 width="160" height="160"/>
          </a>
       </div>
        <?php
        echo str_repeat('<span class="ratingview" style="width:13px;vertical-align:middle;display:inline-block;"></span>', round($item->getRating()));
        echo str_repeat('<span class="ratingview" style="width:13px;vertical-align:middle;display:inline-block;background-position:-51px 0;"></span>', 5 - round($item->getRating()));
        ?>
        <h3><a href="<?php echo $item->getLink() ?>"><?php echo $item->getName() ?></a></h3>

        <div class="goodsbar mSmallBtns mR">
          <?php render_partial('cart_/templates/_buy_button.php', array('item' => $item)) ?>
        </div>
        <div class="font18 pb10 mSmallBtns"><span class="price"><?php echo formatPrice($item->getPrice()) ?></span> <span
          class="rubl">p</span></div>
        <?php if ($show_model): ?>
        <a href="<?php echo $item->getLink() ?>">
          <div class="bListVariants">
            Доступно в разных вариантах<br>
            (<?php echo $item->getModel()->getVariations() ?>)
          </div>
        </a>
        <?php endif ?>
      </div>
    </div>

  </div>
  <!-- /Hover -->
</div>
