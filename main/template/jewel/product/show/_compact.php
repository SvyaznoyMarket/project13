<?php
/**
 * @var $page          \View\Layout
 * @var $product       \Model\Product\CompactEntity
 * @var $isHidden      bool
 * @var $kit           \Model\Product\Kit\Entity
 * @var $productVideos \Model\Product\Video\Entity[]
 * @var $addInfo       array
 **/
?>

<li class="item">
  <div class="item-name"><a href="<?= $product->getLink() ?>"><?= $product->getName() ?></a></div>
  <div class="item-img"><a href="<?= $product->getLink() ?>"><img class="mainImg" src="<?= $product->getImageUrl(2) ?>" alt="<?= $page->escape($product->getNameWithCategory()) ?>" title="<?= $page->escape($product->getNameWithCategory()) ?>" /></a></div>
  <div class="item-price"><?= $page->helper->formatPrice($product->getPrice()) ?> RUR</div>
  <div class="item-buy"><a href="">Купить</a></div>
</li>

