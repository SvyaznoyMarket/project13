<?php
/**
 * @var $item ProductEntity|sfOutputEscaperObjectDecorator
 */
?>
<div class="goodsbox goodsline bNewGoodsBox">
  <div class="goodsboxlink" <?php if ($item->getIsBuyable()): ?> ref="<?php echo $item->getToken() ?>"
       data-cid="<?php echo $item->getId() ?>" <?php endif ?>>
    <div class="photo">
      <a href="<?php echo $item->getLink() ?>">
      <?php if ($label = $item->getMainLabel()): ?>
      <img class="bLabels" src="<?php echo $label->getImageUrl() ?>" alt="<?php echo $label->getName() ?>"/>
      <?php endif; ?>
      <img height="160" width="160" title="<?php echo $item->getName() ?>" alt="<?php echo $item->getName() ?>"
           src="<?php echo $item->getMediaImageUrl() ?>"
           class="mainImg"/>
      </a>
    </div>
    <div class="info">
      <h3><a href="<?php echo $item->getLink() ?>"><?php echo $item->getName() ?></a></h3>
      <span class="gray bNGB__eArt mInlineBlock">
        Артикул #<?php echo $item->getArticle() ?>
        <?php
        echo str_repeat('<span class="ratingview" style="width:13px;vertical-align:middle;display:inline-block;"></span>', round($item->getRating()));
        echo str_repeat('<span class="ratingview" style="width:13px;vertical-align:middle;display:inline-block;background-position:-51px 0;"></span>', 5 - round($item->getRating()));
        ?>
        <span class="bNGB__eDrop"><a href="<?php echo $item->getLink() ?>" style="display: none"></a></span>
      </span>

      <div class="pb5 bNGB__eDesc">
        <?php foreach ($item->getAttributeListForListing() as $attr): ?>
        <?php echo $attr->getName() ?>: <?php echo $attr->getStringValue() ?><br/>
        <?php endforeach ?>
      </div>

      <?php if ($item->getModel() && $item->getModel()->getPropertyList()): ?>
      <a href="<?php echo $item->getLink() ?>">
        <div class="bListVariants">
          Доступно в разных вариантах<br>
          (<?php echo $item->getModel()->getVariations() ?>)
        </div>
      </a>
      <?php endif ?>

    </div>
    <div class="extrainfo">
      <span class="db font18 pb10"><b><span class="price"><?php echo formatPrice($item->getPrice()) ?></span> <span class="rubl">p</span></b></span>

        <div class="goodsbar mSmallBtns">
          <?php render_partial('cart_/templates/_buy_button.php', array('item' => $item)) ?>
        </div>

      <?php if ($item->getIsBuyable()): ?>
      <noindex>
        <ul class="bNGB__eUl">
          <li><strong class="orange">Есть в наличии</strong></li>
        </ul>
      </noindex>
      <?php endif ?>
    </div>
  </div>
</div>
