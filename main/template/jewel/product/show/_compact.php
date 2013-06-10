<?php
/**
 * @var $page          \View\Layout
 * @var $product       \Model\Product\CompactEntity
 **/
?>

<?
$disabled = !$product->getIsBuyable();
$gaEvent = !empty($gaEvent) ? $gaEvent : null;
$gaTitle = !empty($gaTitle) ? $gaTitle : null;
if ($disabled) {
    $url = '#';
} else {
    $url = $page->url('old.cart.product.add', array('productId' => $product->getId()));
}
?>

<li class="item">
  <div ref="<?= $product->getToken(); ?>" style="min-height:218px;height:218px;"><? //для корректной работы js ?>
      <div class="item-name"><a href="<?= $product->getLink() ?>"><?= $product->getName() ?></a></div>
      <div class="item-img"><a href="<?= $product->getLink() ?>"><img class="mainImg" src="<?= $product->getImageUrl(2) ?>" alt="<?= $page->escape($product->getNameWithCategory()) ?>" title="<?= $page->escape($product->getNameWithCategory()) ?>" /></a></div>
      <div class="item-price"><span><?= $page->helper->formatPrice($product->getPrice(), 2) ?> RUB</span></div>

      <? //для корректной работы js ?>
      <div class="photo" style="visibility:hidden;"><img class="mainImg" src="<?= $product->getImageUrl(2) ?>" alt="<?= $page->escape($product->getNameWithCategory()) ?>" title="<?= $page->escape($product->getNameWithCategory()) ?>" width="160" height="160"/></div>
      <h3 class="hf"><a href="<?= $product->getLink() ?>"><?= $product->getName() ?></a></h3>
      <span class="hf price"><?= $page->helper->formatPrice($product->getPrice()) ?></span>

      <div class="goodsbar"><? //для корректной работы js ?>
        <a href="<?= $url ?>"<?php echo (!empty($gaEvent) ? (' data-event="'.$gaEvent.'"') : '').(!empty($gaTitle) ? (' data-title="'.$gaTitle.'"') : '') ?> data-product="<?= $product->getId() ?>" data-category="<?= $product->getMainCategory() ? $product->getMainCategory()->getId() : 0 ?>" class="link1 event-click item-buy cart cart-add<?php if ($disabled): ?> disabled<? endif ?><?php if ($gaEvent): ?> gaEvent<? endif ?>"></a>
      </div>
  </div>
</li>
