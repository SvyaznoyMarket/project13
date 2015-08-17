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
?>

<?
if (!isset($useLens)) {
    $useLens = false;
}

$region = \App::user()->getRegion();
if (!$lifeGiftProduct) $lifeGiftProduct = null;

$isProductAvailable = $product->isAvailable();
if (\App::config()->preview) {
    $isProductAvailable = true;
}

$buySender = $request->get('sender');
$buySender2 = $request->get('sender2');
$recommendationSender2 = $product->isOnlyFromPartner() && !$product->getSlotPartnerOffer() ? 'marketplace' : '';
?>

<?= $helper->render('product/__data', ['product' => $product]) ?>

<div class="bProductSectionLeftCol">
    <?= $helper->render('product/__photo', ['product' => $product, 'useLens' => $useLens, 'videoHtml' => $videoHtml, 'properties3D' => $properties3D]) ?>

    <div class="bProductDesc<? if (!$creditData['creditIsAllowed'] || $user->getRegion()->getHasTransportCompany()): ?> mNoCredit<? endif ?>" itemprop="offers" itemscope itemtype="http://schema.org/Offer">

        <? if ($isProductAvailable): ?>

            <?= $helper->render('product/__state', ['product' => $product]) // Есть в наличии ?>

            <?= $helper->render('product/__price', ['product' => $product]) // Цена ?>

            <?= $helper->render('product/__lowPriceNotifier', ['product' => $product, 'actionChannelName' => $actionChannelName]) // Узнать о снижении цены ?>

            <? if (count($product->getPartnersOffer()) == 0) : ?>
                <?= $helper->render('product/__credit', ['product' => $product, 'creditData' => $creditData]) // Купи в кредит ?>
            <? endif ?>

            <? if ($product->getTagline()): // new Card Properties Begin { ?>
                <div itemprop="description" class="bProductDescText">
                    <?= $product->getTagline() ?>
                    <? /* <div class="bTextMore"><a class="jsGoToId" data-goto="productspecification" href="">Характеристики</a></div> */ ?>
                </div>
            <? endif // } /end of new Card Properties ?>

        <? endif ?>

        <? if (!$isProductAvailable): ?>
            <? if (\App::config()->product['pullRecommendation']): ?>
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
                        'position' => 'ProductMissing',
                    ],
                    'sender2' => $recommendationSender2,
                ]) ?>
            <? endif ?>
        <? endif ?>

        <? if ($isProductAvailable): ?>

            <?= $helper->render('product/__reviewCount', ['product' => $product, 'reviewsData' => $reviewsData]) ?>
            <?= $helper->render('product/__mainProperties', ['product' => $product]) ?>
            <?= $helper->render('product/__model', ['product' => $product]) // Модели ?>

        <? endif; ?>

    </div><!--/product shop description section -->

    <div class="clear"></div>

    <?= $helper->render('product/__likeButtons', [] ); // Insert LikeButtons (www.addthis.com) ?>

    <div class="clear"></div>

    <? if ($isKit): ?>
        <?= $helper->render('product/__baseKit', [
            'products' => $kitProducts,
            'product' => $product,
            'sender'  => $buySender,
            'sender2' => $buySender2,
        ]) ?>
    <? endif ?>

    <? if ((bool)$accessories && \App::config()->product['showAccessories']): ?>
        <?= $helper->render('product/__slider', [
            'type'           => 'accessorize',
            'title'          => 'Аксессуары',
            'products'       => array_values($accessories),
            'categories'     => $accessoryCategory,
            'count'          => count($product->getAccessoryId()),
            'limit'          => (bool)$accessoryCategory ? \App::config()->product['itemsInAccessorySlider'] : \App::config()->product['itemsInSlider'],
            'page'           => 1,
            'gaEvent'        => 'Accessorize',
            'class'          => (bool)$accessoryCategory ? 'slideItem-3item' : 'slideItem-5item',
            'sender'         => [
                'name'     => 'enter',
                'position' => $isProductAvailable ? 'ProductAccessoriesManual' : 'ProductMissing',
            ],
            'sender2' => $recommendationSender2,
        ]) ?>
    <? endif ?>

    <? if (\App::config()->product['pullRecommendation']): ?>
        <?= $helper->render('product/__slider', [
            'type'           => 'alsoBought',
            'title'          => 'С этим товаром покупают',
            'products'       => [],
            'count'          => null,
            'limit'          => \App::config()->product['itemsInSlider'],
            'page'           => 1,
            'url'            => $page->url('product.recommended', ['productId' => $product->getId()]),
            'sender'         => [
                'name'     => 'retailrocket',
                'position' => $isProductAvailable ? 'ProductAccessories' : 'ProductMissing', // все правильно - так и надо!
            ],
            'sender2' => $recommendationSender2,
        ]) ?>
    <? endif ?>

    <div class="bDescriptionProduct">
        <?= $product->getDescription() ?>
    </div>

    <?= $helper->render('product/__trustfactors', ['trustfactors' => $trustfactors, 'type' => 'content']) ?>

    <? if ($product->getSecondaryGroupedProperties()): // показываем все характеристики (сгруппированые), если ранее они не были показаны ?>
        <?= $helper->render('product/__groupedProperty', ['groupedProperties' => $product->getSecondaryGroupedProperties()]) // Характеристики ?>
    <? endif ?>

    <?= $page->render('product/_reviews', ['product' => $product, 'reviewsData' => $reviewsData, 'reviewsDataSummary' => $reviewsDataSummary, 'reviewsPresent' => $reviewsPresent]) ?>

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
            'sender2' => $recommendationSender2,
        ]) ?>
    <? endif ?>

</div><!--/left section -->

<div class="bProductSectionRightCol">

    <? if ($isProductAvailable): ?>

    <? if (5 !== $product->getStatusId() && (bool)$shopStates): // SITE-3109 ?>
        <div class="bWidgetBuy bWidgetBuy-shops mWidget js-WidgetBuy">
            <?= $helper->render('product/__shops', ['shopStates' => $shopStates, 'product' => $product, 'sender'  => $buySender, 'sender2'  => $buySender2, 'location'  => 'product-card']) // Доставка ?>
        </div>
    <? endif ?>

    <? if (!$product->isInShopStockOnly() && $product->getIsBuyable() && 5 != $product->getStatusId()): ?>
        <div class="bWidgetBuy mWidget js-WidgetBuy">
            <? if ($product->getIsBuyable() && !$product->isInShopStockOnly() && (5 !== $product->getStatusId()) && 0 == count($kitProducts)): ?>
                <?= $helper->render('__spinner', [
                    'id'        => \View\Id::cartButtonForProduct($product->getId()),
                    'productId' => $product->getId(),
                    'location'  => 'product-card',
                ]) ?>
            <? endif ?>

            <?= $helper->render('cart/__button-product', [
                'product'  => $product,
                'onClick'  => isset($addToCartJS) ? $addToCartJS : null,
                'sender'   => (is_array($buySender) ? $buySender : []) + [
                    'from' => preg_filter('/\?+?.*$/', '', $request->server->get('HTTP_REFERER')) == null ? $request->server->get('HTTP_REFERER') : preg_filter('/\?+?.*$/', '', $request->server->get('HTTP_REFERER')) // удаляем из REFERER параметры
                ],
                'sender2' => $buySender2,
                'location' => 'product-card',
            ]) // Кнопка купить ?>

            <div class="js-showTopBar"></div>

            <? if (!count($product->getPartnersOffer()) && (!$isKit || $product->getIsKitLocked())): ?>
                <?= $helper->render('cart/__button-product-oneClick', ['product' => $product, 'sender'  => $buySender, 'sender2' => $buySender2, 'location'  => 'product-card']) // Покупка в один клик ?>
            <? endif ?>

            <? if (!$isKit || $product->getIsKitLocked()) : ?>
                <?= $page->render('compare/_button-product-compare', ['product' => $product]) ?>
            <? endif ?>

            <? if (5 !== $product->getStatusId()): // SITE-3109 ?>
                <?= $helper->render('product/__delivery', ['product' => $product, 'deliveryData' => $deliveryData, 'shopStates' => $shopStates]) // Доставка ?>
            <? endif ?>

            <?= $helper->render('product/__trustfactors', ['trustfactors' => $trustfactors, 'type' => 'main']) ?>
        </div>
    <? elseif (!$isKit || $product->getIsKitLocked()): ?>
        <div class="bWidgetBuy mWidget js-WidgetBuy">
            <div class="js-showTopBar"></div>
            <?= $page->render('compare/_button-product-compare', ['product' => $product]) ?>
        </div>
    <? else: ?>
        <div class="js-showTopBar"></div>
    <? endif ?>

    <? else: ?>
    <div class="js-showTopBar"></div>
    <? endif ?>

    <?/*= $helper->render('cart/__form-oneClick', [
        'product' => $product,
        'region'  => $region,
        'sender'  => $buySender,
        'sender2' => $buySender2,
    ])*/ // Форма покупки в один клик ?>

    <? if ($lifeGiftProduct): ?>
        <?= $helper->render('cart/__button-product-lifeGift', ['product' => $lifeGiftProduct]) // Кнопка "Подари жизнь" ?>
    <? endif ?>

    <?= $helper->render('product/__adfox', ['product' => $product]) // Баннер Adfox ?>

    <? if ($isProductAvailable): // SITE-4709 ?>
        <?= $helper->render('product/__trustfactors', ['trustfactors' => $trustfactors, 'type' => 'right']) ?>
    <? endif ?>
</div><!--/right section -->

<div class="clear"></div>

<? if (false && \App::config()->product['pullRecommendation']): ?>
    <?= $helper->render('product/__slider', [
        'type'     => 'alsoViewed',
        'title'    => 'С этим товаром также смотрят',
        'products' => [],
        'count'    => null,
        'limit'    => \App::config()->product['itemsInSlider'],
        'page'     => 1,
        'url'      => $page->url('product.recommended', ['productId' => $product->getId()]),
        'sender'   => [
            'name'     => 'retailrocket',
            'position' => 'ProductUpSale',
        ],
        'sender2' => $recommendationSender2,
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
            'from'     => 'productPage',
            'position' => $isProductAvailable ? 'Viewed' : 'ProductMissing',
        ],
        'sender2' => $recommendationSender2,
    ]) ?>
<? endif ?>

<div class="bBreadCrumbsBottom"><?= $page->render('_breadcrumbs', ['breadcrumbs' => $breadcrumbs, 'class' => 'breadcrumbs-footer']) ?></div>

</div>
