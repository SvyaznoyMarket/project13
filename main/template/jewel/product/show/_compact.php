<?php
/**
 * @var $page          \View\Layout
 * @var $product       \Model\Product\CompactEntity
 * @var $addInfo       array
 * @var $itemsPerRow   int
 **/
?>

<?
$helper = new \Helper\TemplateHelper();
$disabled = !$product->getIsBuyable();
$gaEvent = !empty($gaEvent) ? $gaEvent : null;
$gaTitle = !empty($gaTitle) ? $gaTitle : null;
if ($disabled) {
    $url = '#';
} else {
    $url = $page->url('cart.product.set', array('productId' => $product->getId()));
}

$imgSize = isset($itemsPerRow) && 3 == $itemsPerRow ? 6 : 2;
?>

<li class="bBrandGoodsList__eItem js-jewelListing">
  <div class="goodsbox" ref="<?= $product->getToken(); ?>"><? //для корректной работы js ?>
    <div class="goodsbox__inner" data-url="<?= $product->getLink() ?>" <?php if (isset($additionalData)) echo 'data-product="' . $page->json($additionalData) . '"' ?> <?= (count($addInfo)) ? 'data-add="'.$page->json($addInfo).'"' :''; ?>>
      <div class="bItemName"><a href="<?= $product->getLink() ?>"><?= $product->getName() ?></a></div>
      <div class="bItemImg"><a href="<?= $product->getLink() ?>"><img class="mainImg" src="<?= $product->getImageUrl($imgSize) ?>" alt="<?= $page->escape($product->getNameWithCategory()) ?>" /></a></div>

        <? if ($product->getPriceOld()): ?>
            <div class="bPriceLine clearfix">
                <span class="bPriceOld">
                    <strong class="bDecor"><?= $helper->formatPrice($product->getPriceOld()) ?></strong> <span class="rubl">p</span>
                </span>
            </div>
        <? endif ?>

      <div class="bItemPrice"><span><?= $page->helper->formatPrice($product->getPrice()) ?> <span class="rubl">p</span></span></div>

      <? if ($product->getIsBuyable()): ?>
        <?= $helper->render('cart/__button-product', ['product' => $product]) // Кнопка купить ?>
      <? endif ?>
    </div>

      <?= $page->render('product/show/__corner_features', ['product' => $product]) ?>
  </div>
</li>
