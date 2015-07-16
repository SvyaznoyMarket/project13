<?
/**
 * @var $page                   \View\ProductCategory\LeafPage
 * @var $productPager           \Iterator\EntityPager
 */
$helper = \App::helper();
?>

<? foreach ($productPager as $product) : ?>

    <? if (!$product instanceof \Model\Product\Entity) continue; ?>

<div class="goods__item grid-3col__item">
    <? if ($product->getLabel()) : ?>
        <div class="sticker-list">
            <div class="sticker sticker_sale"><?= $product->getLabel()->getName() ?></div>
        </div>
    <? endif ?>

    <div class="goods__controls">
        <a class="add-control add-control_wish" href=""></a>
        <a class="add-control add-control_compare" href=""></a>
    </div>

    <? if ($product->getBrand()) : ?>
        <div class="sticker-brand">
            <a href=""><img src="<?= $product->getBrand()->getImage() ?>" alt=""></a>
        </div>
    <? endif ?>

    <a href="<?= $product->getLink() ?>" class="goods__img">
        <img src="<?= $product->getMainImageUrl('product_200') ?>" alt="" class="goods__img-image" style="opacity: 1;">

        <!--<div class="sticker sticker_info">Товар со склада</div>-->
    </a>

    <? if ($product->getRating()) : ?>

    <div class="goods__rating rating">
        <span class="rating-state">
        <? foreach ( range(1,5) as $i) : ?>
            <i class="rating-state__item rating-state__item_1 icon-rating <?= round($product->getRating()) >= $i ? 'rating-state__item_fill' : '' ?>"></i>
        <? endforeach ?>
        </span>
        <span class="rating-count">(<?= $product->getRatingCount() ?>)</span>
    </div>

    <? endif ?>

    <div class="goods__name">
        <div class="goods__name-inn">
            <a href="<?= $product->getLink() ?>"><?= $product->getName() ?></a>
        </div>
    </div>

    <? if ($product->getPriceOld()) : ?>
        <div class="goods__price-old"><span class="line-through"><?= $helper->formatPrice($product->getPriceOld()) ?></span> ₽</div>
    <? endif ?>

    <div class="goods__price-now"><?= $helper->formatPrice($product->getPrice()) ?> ₽</div>

    <?= $helper->render('product/_button.buy', ['product' => $product, 'class' => 'btn-primary_middle' ]) ?>
</div>

<? endforeach ?>