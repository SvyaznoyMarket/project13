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
?>

<?
$region = \App::user()->getRegion();
if (!$lifeGiftProduct) $lifeGiftProduct = null;
$isKitPage = (bool)$product->getKit();

$isProductAvailable = $product->isAvailable();
if (\App::config()->preview) {
    $isProductAvailable = true;
}

$buySender = ($request->get('sender') ? (array)$request->get('sender') : \Session\ProductPageSenders::get($product->getUi())) + ['name' => null, 'method' => null, 'position' => null];
?>

<?= $helper->render('product/__data', ['product' => $product]) ?>

<div class="bProductSectionLeftCol">
    <?= $helper->render('product/__photo', ['product' => $product, 'useLens' => $useLens]) ?>

    <div class="bProductDesc<? if (!$creditData['creditIsAllowed'] || $user->getRegion()->getHasTransportCompany()): ?> mNoCredit<? endif ?>" itemprop="offers" itemscope itemtype="http://schema.org/Offer">

        <? if ($isProductAvailable): ?>

            <?= $helper->render('product/__state', ['product' => $product]) // Есть в наличии ?>

            <?= $helper->render('product/__price', ['product' => $product]) // Цена ?>

            <?= $helper->render('product/__notification-lowerPrice', ['product' => $product, 'isUserSubscribedToEmailActions' => $isUserSubscribedToEmailActions, 'actionChannelName' => $actionChannelName]) // Узнать о снижении цены ?>

            <? if (count($product->getPartnersOffer()) == 0) : ?>
                <?= $helper->render('product/__credit', ['product' => $product, 'creditData' => $creditData]) // Купи в кредит ?>
            <? endif; ?>

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

    <? if ( $isKitPage ): // если это набор пакет ?>
        <?= $helper->render('product/__baseKit', [
            'products' => $kitProducts,
            'product' => $product,
            'sender'  => $buySender,
        ]) ?>
    <? endif ?>

    <? if ( (bool)$relatedKits ) : // если есть родительские пакеты ?>
        <?= $helper->render('product/__relatedKits',['kits' => $relatedKits, 'product' => $product]) ?>
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
            //'url'            => $page->url('product.accessory', ['productToken' => $product->getToken()]),
            'gaEvent'        => 'Accessorize',
            'additionalData' => $additionalData,
            'class'          => (bool)$accessoryCategory ? 'slideItem-3item' : 'slideItem-5item',
            'sender'         => [
                'name'     => 'enter',
                'position' => $isProductAvailable ? 'ProductAccessoriesManual' : 'ProductMissing',
            ],
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
            'additionalData' => $additionalData,
            'url'            => $page->url('product.recommended', ['productId' => $product->getId()]),
            'sender'         => [
                'name'     => 'retailrocket',
                'position' => $isProductAvailable ? 'ProductAccessories' : 'ProductMissing', // все правильно - так и надо!
            ],
        ]) ?>
    <? endif ?>

    <div class="bDescriptionProduct">
        <?= $product->getDescription() ?>
    </div>

    <?= $helper->render('product/__trustfactors', ['trustfactors' => $trustfactors, 'type' => 'content']) ?>

    <? if ($product->getSecondaryGroupedProperties()): // показываем все характеристики (сгруппированые), если ранее они не были показаны ?>
        <?= $helper->render('product/__groupedProperty', ['groupedProperties' => $product->getSecondaryGroupedProperties()]) // Характеристики ?>
    <? endif ?>

    <?= $page->render('product/_reviews', ['product' => $product, 'reviewsData' => $reviewsData, 'reviewsDataSummary' => $reviewsDataSummary, 'reviewsPresent' => $reviewsPresent, 'sprosikupiReviews' => $sprosikupiReviews, 'shoppilotReviews' => $shoppilotReviews]) ?>

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

</div><!--/left section -->

<div class="bProductSectionRightCol">

    <? if ($isProductAvailable): ?>

    <? if (5 !== $product->getStatusId() && (bool)$shopStates): // SITE-3109 ?>
        <div class="bWidgetBuy bWidgetBuy-shops mWidget js-WidgetBuy">
            <?= $helper->render('product/__shops', ['shopStates' => $shopStates, 'product' => $product, 'sender'  => $buySender]) // Доставка ?>
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

            <? if ($isKitPage && !$product->getIsKitLocked()): ?>
                <?= $helper->render('cart/__button-product-kit', [
                    'product'  => $product,
                    'sender'  => $buySender,
                ]) // Кнопка купить для набора продуктов ?>
            <? else: ?>
                <?= $helper->render('cart/__button-product', [
                    'product'  => $product,
                    'onClick'  => isset($addToCartJS) ? $addToCartJS : null,
                    'sender'   => $buySender + [
                        'from' => preg_filter('/\?+?.*$/', '', $request->server->get('HTTP_REFERER')) == null ? $request->server->get('HTTP_REFERER') : preg_filter('/\?+?.*$/', '', $request->server->get('HTTP_REFERER')) // удаляем из REFERER параметры
                    ],
                    'location' => 'product-card',
                ]) // Кнопка купить ?>
            <? endif ?>

            <div class="js-showTopBar"></div>

            <? if (!$hasFurnitureConstructor && !count($product->getPartnersOffer()) && (!$isKitPage || $product->getIsKitLocked())): ?>
                <?= $helper->render('cart/__button-product-oneClick', ['product' => $product, 'sender'  => $buySender]) // Покупка в один клик ?>
            <? endif ?>

            <? if (!$isKitPage || $product->getIsKitLocked()) : ?>
                <?= $page->render('compare/_button-product-compare', ['product' => $product]) ?>
            <? endif ?>

            <? if (5 !== $product->getStatusId()): // SITE-3109 ?>
                <?= $helper->render('product/__delivery', ['product' => $product, 'deliveryData' => $deliveryData, 'shopStates' => $shopStates]) // Доставка ?>
            <? endif ?>

            <?= $helper->render('cart/__button-product-paypal', ['product' => $product]) // Кнопка купить через paypal ?>

            <?= $helper->render('product/__trustfactors', ['trustfactors' => $trustfactors, 'type' => 'main']) ?>
        </div>
    <? elseif (!$isKitPage || $product->getIsKitLocked()): ?>
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
    ]) ?>
<? endif ?>

<div class="bBreadCrumbsBottom"><?= $page->render('_breadcrumbs', ['breadcrumbs' => $breadcrumbs, 'class' => 'breadcrumbs-footer']) ?></div>

</div>
