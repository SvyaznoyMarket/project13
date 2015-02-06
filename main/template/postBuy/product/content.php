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

<div class="product-card__section-left bProductSectionLeftCol">
    <div class="product-card__head">
        <h1 class="product-card__head__title clearfix" itemprop="name">
                <? if ($product->getPrefix()): ?>
                    <div class="product-card__head__subtitle"><?= $product->getPrefix() ?></div>
                <? endif ?>
                <?= $product->getWebName() ?>
        </h1>
        <span class="product-card__head__article">Артикул: <?= $product->getArticle() ?></span>
    </div>

    <?= $helper->render('product/__photo', ['product' => $product, 'useLens' => $useLens]) ?>
</div>

<div class="product-card__section-right">
    <? if ($isProductAvailable): ?>
        <div class="product-card__vendor">Продавец-партнёр: <?= $helper->escape($postBuyOffer['name']) ?></div>

        <?= $helper->render('postBuy/product/__price', ['product' => $product]) // Цена ?>

        <span class="product-card__info--price">Цена базового комплекта</span>
        <span class="product-card__info--deliv-period">Срок доставки базового комплекта 3 дня</span>
        <div class="product-card__info--recall">
            <span>Закажите обратный звонок и уточните</span>
            <ul class="product-card__info--recall__list">
                <li>Состав мебели и техники</li>
                <li>Условия доставки, сборки и оплаты</li>
            </ul>
        <?= $helper->render('cart/__button-product', [
            'product'  => $product,
            'sender'   => $buySender,
            'location' => 'product-card',
        ]) ?>

        </div>

        <div class="product-card__specify" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
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
<div class="product-card__bordered">
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
</div>
<div class="product-card__bordered">
    <div class="product-card__desc">
        <?= $product->getDescription() ?>
    </div>
    <div class="product-card__props">
        <? if ($product->getSecondaryGroupedProperties()): // показываем все характеристики (сгруппированые), если ранее они не были показаны ?>
            <?= $helper->render('product/__groupedProperty', ['groupedProperties' => $product->getSecondaryGroupedProperties()]) // Характеристики ?>
        <? endif ?>
    </div>

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

    <div class="product-containter__brcr-bottom"><?= $page->render('_breadcrumbs', ['breadcrumbs' => $breadcrumbs, 'class' => 'breadcrumbs-footer']) ?></div>

    <? if (\App::config()->analytics['enabled']): ?>
        <?= $page->tryRender('product/partner-counter/_cityads', ['product' => $product]) ?>
        <?//= $page->tryRender('product/partner-counter/_recreative', ['product' => $product]) ?>
    <? endif ?>

    <?= $page->tryRender('product/_tag', ['product' => $product]) ?>

    <?= $helper->render('product/__event', ['product' => $product]) ?>
    </div>
</div>