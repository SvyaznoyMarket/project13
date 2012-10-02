<?php
/**
 * @var $productLine ProductLineEntity
 */

$product = $productLine->getMainProduct();

#JSON data
$json = array(
  'jsref' => $product->getToken(),
  'jsimg' => $product->getMediaImageUrl(3),
  'jstitle' => $product->getName(),
  'jsprice' => formatPrice($product->getPrice())
)
?>
<h2 class="mbSet"><strong><?php echo $product->getName() ?></strong></h2>
<div class="line pb15"></div>

<div class='bSet' data-value='<?php echo json_encode($json) ?>'>
  <div class='bSet__eImage'>
    <a href="<?php echo $product->getLink() ?>" title="<?php echo $product->getName() ?>">
      <?php foreach ($product->getLabelList() as $label):?>
        <img class="bLabels" src="<?php echo $label->getImageUrl(1) ?>" alt="<?php echo $label->getName() ?>" />
      <?php endforeach ?>
      <img src="<?php echo $product->getMediaImageUrl(3) ?>" alt="<?php echo $product->getName() ?>" width="500" height="500" title=""/>
    </a>
  </div>
  <div class='bSet__eInfo'>
    <div class='bSet__eArticul'>Артикул #<?php echo $product->getArticle() ?></div>
    <p class='bSet__eDescription'><?php echo $product->getDescription() ?></p>

    <div class='bSet__ePrice'>
      <?php include_partial('product/price', array('price' => formatPrice($product->getPrice()))) ?>

      <?php render_partial('cart_/templates/_buy_button.php', array(
        'item' => $product,
        'text' => 'Купить ' . (count($product->getKitList()) ? ' набор' : ''),
      )) ?>

      <?php if ($product->getIsBuyable()): ?>
      <div class="pb5"><strong class="orange">Есть в наличии</strong></div>
      <?php endif ?>

      <?php if (false && $product->getIsBuyable()): ?>
      <div class="pb5"><strong><a onClick="_gaq.push(['_trackEvent', 'QuickOrder', 'Open']);"
                                  href="<?php echo url_for('order_1click', array('product' => $product->getToken())) ?>"
                                  class="red underline order1click-link">Купить быстро в 1 клик</a></strong></div>
      <?php endif ?>
    </div>
    <div class='bSet__eIconsWrap'>
      <?php if (count($product->getKitList())): ?>
      <h3 class='bSet__eG'>Состав набора:</h3>
      <div class='bSet__eIcons'>
        <ul class="previewlist">
          <?php foreach ($product->getKitList() as $kit): ?>
          <li><b><a href="<?php echo $kit->getProduct()->getLink() ?>" title="<?php echo $kit->getProduct()->getName() ?>"></a></b>
            <img src="<?php echo $kit->getProduct()->getMediaImageUrl(1) ?>" alt="<?php echo $kit->getProduct()->getName() ?>" width="48" height="48">
          </li>
          <?php endforeach ?>
        </ul>
      </div>
      <?php endif ?>
      <div class='bSet__eTWrap'>
        <a class='bSet__eMoreInfo' href="<?php echo $product->getLink() ?>">
          Подробнее о <?php echo count($product->getKitList())  ? 'наборе' : 'товаре' ?>
        </a>
      </div>
    </div>
  </div>
</div>