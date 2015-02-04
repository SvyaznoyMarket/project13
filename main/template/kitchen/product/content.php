<?php
/**
 * @var $page                   \View\Product\IndexPage
 * @var $product                \Model\Product\Entity
 * @var $lifeGiftProduct        \Model\Product\Entity|null
 * @var $user                   \Session\User
 * @var $accessories            \Model\Product\Entity[]
 * @var $accessoryCategory      \Model\Product\Category\Entity[]
 * @var $kit                    \Model\Product\Entity[]
 * @var $relatedKits            array
 * @var $additionalData         array
 * @var $shopStates             \Model\Product\ShopState\Entity[]
 * @var $creditData             array
 * @var $line                   \Model\Line\Entity
 * @var $deliveryData           array
 * @var $isTchibo               boolean
 * @var $addToCartJS     string
 * @var $isUserSubscribedToEmailActions boolean
 * @var $actionChannelName string
 * @var $kitProducts            array   Продукты кита
 * @var $useLens                bool    Показывать лупу
 * @var $reviewsData            array   Данные отзывов
 * @var $breadcrumbs            array   Хлебные крошки
 * @var $trustfactors           array   Трастфакторы
 * @var $reviewsDataSummary     array   Данные отзывов
 * @var $sprosikupiReviews      array   Данные отзывов
 * @var $shoppilotReviews       array   Данные отзывов
 */
$helper = new \Helper\TemplateHelper();

if (!isset($categoryClass)) $categoryClass = null;


$region = \App::user()->getRegion();
if (!$lifeGiftProduct) $lifeGiftProduct = null;
$isKitPage = (bool)$product->getKit();

$isProductAvailable = $product->isAvailable();

$buySender = ($request->get('sender') ? (array)$request->get('sender') : \Session\ProductPageSenders::get($product->getUi())) + ['name' => null, 'method' => null, 'position' => null];
$postBuyOffer = $product->getPostBuyOffer();
?>

<?= $helper->render('product/__data', ['product' => $product]) ?>

<div class="bProductSectionLeftCol">
    <div class="bPageHead">
        <? if ($product->getPrefix()): ?>
            <div class="bPageHead__eSubtitle"><?= $product->getPrefix() ?></div>
        <? endif ?>
        <div class="bPageHead__eTitle clearfix">
            <h1 itemprop="name"><?= $product->getWebName() ?></h1>
        </div>
        <span class="bPageHead__eArticle">Артикул: <?= $product->getArticle() ?></span>
    </div>

    <?= $helper->render('kitchen/product/__photo', ['product' => $product, 'useLens' => $useLens]) ?>
</div>

<div class="bProductSectionRightCol">
    <? if ($isProductAvailable): ?>
        <p>Продавец-партнёр: <?= $helper->escape($postBuyOffer['name']) ?></p>
        <?= $helper->render('kitchen/product/__price', ['product' => $product]) // Цена ?>
        <p>Цена базового комплекта</p>
        <p>Срок доставки базового комплекта 3 дня</p>
        <p>Закажите обратный звонок и уточните</p>
        <ul>
            <li>Состав мебели и техники</li>
            <li>Условия доставки, сборки и оплаты</li>
        </ul>

        <?= $helper->render('cart/__button-product', [
            'product'  => $product,
            'sender'   => $buySender,
            'location' => 'product-card',
        ]) ?>

        <div class="bProductDesc" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
            <?= $helper->render('product/__mainProperties', ['product' => $product]) ?>
        </div>

        <div class="clear"></div>

        <div class="js-showTopBar"></div>

        <div class="bWidgetBuy mWidget js-WidgetBuy">
            <?= $page->render('compare/_button-product-compare', ['id' => $product->getId(), 'typeId' => $product->getType() ? $product->getType()->getId() : null]) ?>
        </div>

    <? else: ?>
        <div class="js-showTopBar"></div>
    <? endif ?>
</div>

<div class="clear"></div>

<? if ($isProductAvailable && \App::config()->product['pullRecommendation']): ?>
    <?= $helper->render('product/__slider', [
        'type'     => 'similar',
        'title'    => 'Похожие товары',
        'products' => [],
        'count'    => null,
        'limit'    => \App::config()->product['itemsInSlider'],
        'page'     => 1,
        'url'      => $page->url('product.recommended', ['productId' => $product->getId()]),
        'sender'   => [
            'name'     => 'retailrocket',
            'position' => 'ProductSimilar',
        ],
    ]) ?>
<? endif ?>

<div class="bDescriptionProduct">
    <?= $product->getDescription() ?>
</div>

<? if ($product->getSecondaryGroupedProperties()): // показываем все характеристики (сгруппированые), если ранее они не были показаны ?>
    <?= $helper->render('product/__groupedProperty', ['groupedProperties' => $product->getSecondaryGroupedProperties()]) // Характеристики ?>
<? endif ?>

<? if (\App::config()->product['pullRecommendation']): ?>
    <?= $helper->render('product/__slider', [
        'type'           => 'alsoBought',
        'title'          => 'С этим товаром покупают',
        'products'       => [],
        'count'          => null,
        'limit'          => \App::config()->product['itemsInSlider'],
        'page'           => 1,
        'additionalData' => $additionalData,
        'url'            => $page->url('product.recommended', ['productId' => $product->getId()]),
        'sender'         => [
            'name'     => 'retailrocket',
            'position' => 'ProductAccessories', // все правильно - так и надо!
        ],
    ]) ?>
<? endif ?>

<? if (\App::config()->product['pullRecommendation'] && \App::config()->product['viewedEnabled']): ?>
    <?= $helper->render('product/__slider', [
        'type'      => 'viewed',
        'title'     => 'Вы смотрели',
        'products'  => [],
        'count'     => null,
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

<div class="bBreadCrumbsBottom"><?= $page->render('_breadcrumbs', ['breadcrumbs' => $breadcrumbs, 'class' => 'breadcrumbs-footer']) ?></div>

<? if (\App::config()->analytics['enabled']): ?>
    <?= $page->tryRender('product/partner-counter/_cityads', ['product' => $product]) ?>
    <?//= $page->tryRender('product/partner-counter/_recreative', ['product' => $product]) ?>
<? endif ?>

<?= $page->tryRender('product/_tag', ['product' => $product]) ?>

<?= $helper->render('product/__event', ['product' => $product]) ?>