<?php
/**
 * @var $page           \View\Product\IndexPage
 * @var $line           \Model\Line\Entity
 * @var $mainProduct    \Model\Product\Entity
 * @var $parts          \Model\Product\Entity[]
 * @var $request        \Http\Request
 * @var $productPager   \Iterator\EntityPager|NULL
 * @var $productView    string
 */

#JSON data
$json = array(
'jsref' =>      $mainProduct->getToken(),
'jsimg' =>      $mainProduct->getImageUrl(3),
'jstitle' =>    $page->escape($mainProduct->getName()),
'jsprice' =>    $mainProduct->getPrice(),// formatPrice($product->getPrice())
)
?>
<h2 class="mbSet"><strong><?php echo $mainProduct->getName() ?></strong></h2>
<div class="line pb15"></div>

<div class='bSet' data-value='<?php echo json_encode($json) ?>'>
    <div class='bSet__eImage'>
        <a href="<?php echo $mainProduct->getLink() ?>" title="<?php echo $mainProduct->getName() ?>">
            <?php if ((bool)$mainProduct->getLabel()): ?>
            <img class="bLabels" src="<?php echo $mainProduct->getLabel()->getImageUrl(1) ?>" alt="<?php echo $mainProduct->getLabel()->getName() ?>" />
            <?php endif ?>
            <img src="<?php echo $mainProduct->getImageUrl(3) ?>" alt="<?php echo $mainProduct->getName() ?>" width="500" height="500" title=""/>
        </a>
    </div>
    <div class='bSet__eInfo'>
        <div class='bSet__eArticul'>Артикул #<?php echo $mainProduct->getArticle() ?></div>
        <p class='bSet__eDescription'><?php echo $mainProduct->getDescription() ?></p>

        <div class='bSet__ePrice'>
            <strong class="font34"><span class="price<?php if ($mainProduct->hasSaleLabel()) echo ' red'; ?>"><?php echo $page->helper->formatPrice($mainProduct->getPrice()) ?></span> <span class="rubl">p</span></strong>
            <?php echo $page->render('cart/_button', array('product' => $mainProduct, 'disabled' => !$mainProduct->getIsBuyable(), 'value' => 'Купить ' . (count($mainProduct->getKit()) ? ' набор' : ''))) ?>
            <?php if ($mainProduct->getIsBuyable()): ?>
            <div class="pb5"><strong class="orange">Есть в наличии</strong></div>
            <?php endif ?>
            <?php if (false && $mainProduct->getIsBuyable()): ?>
            <div class="pb5"><strong><a onClick="_gaq.push(['_trackEvent', 'QuickOrder', 'Open']);"
                                        href="<?php echo $page->url('order.1click', array('product' => $mainProduct->getToken())) ?>"
                                        class="red underline order1click-link">Купить быстро в 1 клик</a></strong></div>
            <?php endif ?>
        </div>
        <div class='bSet__eIconsWrap'>
            <?php if (count($mainProduct->getKit())): ?>
            <h3 class='bSet__eG'>Состав набора:</h3>
            <div class='bSet__eIcons'>
                <ul class="previewlist">
                    <?php foreach ($parts as $part): ?>
                    <li>
                    	<a href="<?php echo $part->getLink() ?>" title="<?php echo $part->getName() ?>">
                        	<img src="<?php echo $part->getImageUrl(1) ?>" alt="<?php echo $part->getName() ?>" width="48" height="48">
                        </a>
                    </li>
                    <?php endforeach ?>
                </ul>
            </div>
            <?php endif ?>
            <div class='bSet__eTWrap'>
                <a class='bSet__eMoreInfo' href="<?php echo $mainProduct->getLink() ?>">
                    Подробнее о <?php echo count($mainProduct->getKit())  ? 'наборе' : 'товаре' ?>
                </a>
            </div>
        </div>
    </div>
</div>

<?php if ((bool)$productPager): ?>

<h2 class="bold fl">Еще другие модели в серии <?php echo $line->getName() ?></h2>

<?php echo $page->render('product/_listView', array('view' => $productView, 'request' => $request, 'category' => null)) ?>

<div class="line"></div>

<?php echo $page->render('product/_list', array('pager' => $productPager, 'view' => $productView, 'itemsPerRow' => 4)) ?>

<?php endif ?>