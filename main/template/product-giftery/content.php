<?php
/**
 * @var $page                   \View\Product\IndexPage
 * @var $product                \Model\Product\Entity
 * @var $lifeGiftProduct        \Model\Product\Entity|null
 * @var $user                   \Session\User
 * @var $accessories            \Model\Product\Entity[]
 * @var $accessoryCategory      \Model\Product\Category\Entity[]
 * @var $kit                    \Model\Product\Entity[]
 * @var $shopStates             \Model\Product\ShopState\Entity[]
 * @var $creditData             array
 * @var $deliveryData           array
 * @var $isTchibo               boolean
 * @var $addToCartJS     string
 * @var $actionChannelName string
 * @var $kitProducts            array   Продукты кита
 * @var $reviewsData            array   Данные отзывов
 * @var $breadcrumbs            array   Хлебные крошки
 * @var $trustfactors           array   Трастфакторы
 * @var $reviewsDataSummary     array   Данные отзывов
 * @var $videoHtml              string|null
 * @var $properties3D           []
 */
$helper = new \Helper\TemplateHelper();

if (!isset($categoryClass)) $categoryClass = null;


$region = \App::user()->getRegion();
if (!$lifeGiftProduct) $lifeGiftProduct = null;
$isKitPage = (bool)$product->getKit();

$isProductAvailable = $product->isAvailable();

$buySender = $request->get('sender');
$buySender2 = $request->get('sender2');
?>

<?= $helper->render('product/__data', ['product' => $product]) ?>
<div class="product-wrapper">
    <div class="product-card__section-left">
        <div class="product-card__head">
            <h1 class="product-card__head__title clearfix" itemprop="name">
                    <? if ($product->getPrefix()): ?>
                        <?= $helper->escape($product->getPrefix()) ?>
                    <? endif ?>
                    <?= $helper->escape($product->getWebName()) ?>
            </h1>
            <span class="product-card__head__article">Артикул: <?= $product->getArticle() ?></span>
        </div>

        <?= $helper->render('product/__photo', ['product' => $product]) ?>
    </div>

    <div class="product-card__section-right">

        <?= $helper->render('product-giftery/__price', ['product' => $product]) // Цена ?>

        <div class="product-card__info">
            <span>Электронный подарочный сертификат на покупки в Enter.</span>
            <?= $helper->render('cart/__button-product', [
                'product'  => $product,
                'sender'   => $buySender,
                'sender2'  => $buySender2,
                'location' => 'product-card',
            ]) ?>
        </div>

        <div class="product-card__specify" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
            <?= $helper->render('product/__mainProperties', ['product' => $product]) ?>
        </div>

        <div class="clear"></div>

        <div class="js-showTopBar"></div>

    </div>
</div>

<div class="clear"></div>

<div class="product-card__bordered">
    <? if ($isProductAvailable && \App::config()->product['pullRecommendation']): ?>
        <?= $helper->render('product/__slider', [
            'type'     => 'similar',
            'title'    => 'Похожие товары',
            'products' => [],
            'limit'    => \App::config()->product['itemsInSlider'],
            'page'     => 1,
            'url'      => $page->url('product.recommended', ['productId' => $product->getId()]),
            'sender'   => [
                'name'     => 'retailrocket',
                'position' => 'ProductSimilar',
            ],
            'sender2'  => 'slot',
        ]) ?>
    <? endif ?>
</div>

<div class="product-card__bordered">
    
    <? if ($product->getDescription()):?>
        <div class="product-card__desc">
            <h3 class="bHeadSection">Описание</h3>
            <?= $product->getDescription() ?>
        </div>
    <? endif ?>

    
    <? if ($product->getSecondaryGroupedProperties()): // показываем все характеристики (сгруппированые), если ранее они не были показаны ?>
        <div class="product-card__props">
            <?= $helper->render('product/__groupedProperty', ['groupedProperties' => $product->getSecondaryGroupedProperties()]) // Характеристики ?>
        </div>
    <? endif ?>
    

    <? if (\App::config()->product['pullRecommendation']): ?>
        <?= $helper->render('product/__slider', [
            'type'           => 'alsoBought',
            'title'          => 'С этим товаром покупают',
            'products'       => [],
            'limit'          => \App::config()->product['itemsInSlider'],
            'page'           => 1,
            'url'            => $page->url('product.recommended', ['productId' => $product->getId()]),
            'sender'         => [
                'name'     => 'retailrocket',
                'position' => 'ProductAccessories', // все правильно - так и надо!
            ],
            'sender2'  => 'slot',
        ]) ?>
    <? endif ?>

    <? if (\App::config()->product['pullRecommendation'] && \App::config()->product['viewedEnabled']): ?>
        <?= $helper->render('product/__slider', [
            'type'      => 'viewed',
            'title'     => 'Вы смотрели',
            'products'  => [],
            'limit'     => \App::config()->product['itemsInSlider'],
            'page'      => 1,
            'url'       => $page->url('product.recommended', ['productId' => $product->getId()]),
            'sender'    => [
                'name'     => 'enter',
                'position' => 'Viewed',
                'from'     => 'productPage'
            ],
        ]) ?>
    <? endif ?>

    <div class="product-containter__brcr-bottom"><?= $page->render('_breadcrumbs', ['breadcrumbs' => $breadcrumbs, 'class' => 'breadcrumbs-footer']) ?></div>

    <? if (\App::config()->analytics['enabled']): ?>
        <?= $page->tryRender('product/partner-counter/_cityads', ['product' => $product]) ?>
    <? endif ?>

    <?= $page->tryRender('product/_tag', ['product' => $product]) ?>
    </div>
</div>