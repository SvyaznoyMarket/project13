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
    $url = $page->url('cart.product.set', array('productId' => $product->getId()));
}
?>

<li class="item">
  <div class="goodsbox" ref="<?= $product->getToken(); ?>"><? //для корректной работы js ?>
    <div class="goodsbox__inner" data-url="<?= $product->getLink() ?>" <?php if (isset($additionalData)) echo 'data-product="' . $page->json($additionalData) . '"' ?>>
      <div class="item-name"><a href="<?= $product->getLink() ?>"><?= $product->getName() ?></a></div>
      <div class="item-img"><a href="<?= $product->getLink() ?>"><img class="mainImg" src="<?= $product->getImageUrl(2) ?>" alt="<?= $page->escape($product->getNameWithCategory()) ?>" /></a></div>
      <div class="item-price"><span><?= $page->helper->formatPrice($product->getPrice()) ?> <span class="rubl">p</span></span></div>

      <? //для корректной работы js ?>
      <div class="photo" style="visibility:hidden;"><img class="mainImg" src="<?= $product->getImageUrl(2) ?>" alt="<?= $page->escape($product->getNameWithCategory()) ?>" width="160" height="160"/></div>
      <h3 class="hf"><a href="<?= $product->getLink() ?>"><?= $product->getName() ?></a></h3>
      <span class="hf price"><?= $page->helper->formatPrice($product->getPrice()) ?></span>

      <? if ($product->getIsBuyable()): ?>
        <div class="goodsbar"><? //для корректной работы js ?>
          <? $btnText = \App::user()->getCart()->hasProduct($product->getId()) ? 'В корзине' : 'Купить' ?>
          <a href="<?= $url ?>"<?php echo (!empty($gaEvent) ? (' data-event="'.$gaEvent.'"') : '').(!empty($gaTitle) ? (' data-title="'.$gaTitle.'"') : '') ?> data-product="<?= $product->getId() ?>" data-category="<?= $product->getMainCategory() ? $product->getMainCategory()->getId() : 0 ?>" class="link1 event-click item-buy cart cart-add<?php if ($disabled): ?> disabled<? endif ?><?php if ($gaEvent): ?> gaEvent<? endif ?><?php if (\App::user()->getCart()->hasProduct($product->getId())): ?> link1active<? endif ?>"><?= $btnText ?></a>
        </div>
      <? endif ?>
    </div>

    <? if (!$product->getIsBuyable() && $product->getState()->getIsShop()): ?>
      <div class="notBuying font12" style="bottom:0;left:0;">
          <div class="corner"><div></div></div>
          Только в магазинах
      </div>
    <? endif ?>
  </div>
</li>
