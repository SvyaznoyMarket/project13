<?php
/**
 * @var $page              \View\Product\IndexPage
 * @var $product           \Model\Product\Entity
 * @var $user              \Session\User
 * @var $accessories       \Model\Product\Entity[]
 * @var $accessoryCategory \Model\Product\Category\Entity[]
 * @var $kit               \Model\Product\Entity[]
 * @var $shopStates        \Model\Product\ShopState\Entity[]
 * @var $creditData        array
 * @var $deliveryData      array
 * @var $isTchibo          boolean
 * @var $addToCartJS string
 * @var $actionChannelName string
 * @var $videoHtml              string|null
 * @var $properties3D           []
 */

$showSimilarOnTop = !$product->isAvailable();
$isKit = (bool)$product->getKit();
$buySender = $request->get('sender');
$buySender2 = $request->get('sender2');
$recommendationSender2 = $product->isOnlyFromPartner() && !$product->getSlotPartnerOffer() ? 'marketplace' : '';
?>

<?= $helper->render('product/__data', ['product' => $product]) ?>

<div class="bProductSectionLeftCol">
    <?= $helper->render('product/__photo', ['product' => $product, 'videoHtml' => $videoHtml, 'properties3D' => $properties3D]) ?>

    <div class="bProductDesc<? if (!$creditData['creditIsAllowed'] || $user->getRegion()->getHasTransportCompany()): ?> mNoCredit<? endif ?>" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
        <?= $helper->render('product/__state', ['product' => $product]) // Есть в наличии ?>

        <?= $helper->render('product/__price', ['product' => $product]) // Цена ?>

        <?= $helper->render('product/__lowPriceNotifier', ['product' => $product, 'actionChannelName' => $actionChannelName]) // Узнать о снижении цены ?>

        <?= $helper->render('product/__credit', ['product' => $product, 'creditData' => $creditData]) // Купи в кредит ?>

        <? /* // Old Card Properties
        <div class="bProductDescText">
            <?= $product->getTagline() ?>

            <?= $helper->render('product/__propertiesSimple', ['product' => $product, 'showLinkToProperties' => false]) // Характеристики ?>
        </div>
        */ ?>

        <?
        // new Card Properties Begin {
        if ($product->getTagline()): ?>
            <div itemprop="description" class="bProductDescText">
                <?= $product->getTagline() ?>
                <? /* <div class="bTextMore"><a class="jsGoToId" data-goto="productspecification" href="">Характеристики</a></div> */ ?>
            </div>
        <? endif ?>

        <? if ($showSimilarOnTop): ?>
            <? if (\App::config()->product['pullRecommendation']): ?>
                <?= $helper->render('product/__slider', [
                    'type'     => 'similar',
                    'title'    => 'Похожие товары',
                    'products' => [],
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

        <?= $helper->render('product/__reviewCount', ['product' => $product, 'reviewsData' => $reviewsData]) ?>
        <?= $helper->render('product/__mainProperties', ['product' => $product]) ?>

        <?= $helper->render('product/__model', ['product' => $product]) // Модели ?>
    </div><!--/product shop description section -->

    <div class="clear"></div>

    <?= $helper->render('product/__likeButtons', [] ); // Insert LikeButtons (www.addthis.com) ?>

    <div class="clear"></div>

    <? if ((bool)$accessories && \App::config()->product['showAccessories']): ?>
        <?= $helper->render('product/__slider', [
            'type'           => 'accessorize',
            'title'          => 'Аксессуары',
            'products'       => array_values($accessories),
            'categories'     => $accessoryCategory,
            'limit'          => (bool)$accessoryCategory ? \App::config()->product['itemsInAccessorySlider'] : \App::config()->product['itemsInSlider'],
            'page'           => 1,
            'gaEvent'        => 'Accessorize',
            'sender'         => [
                'position' => 'ProductAccessoriesManual',
            ],
            'sender2' => $recommendationSender2,
        ]) ?>
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
            'sender2' => $recommendationSender2,
        ]) ?>
    <? endif ?>

    <div class="bDescriptionProduct">
        <?= $product->getDescription() ?>
    </div>

    <?= $helper->render('product/__trustfactors', ['trustfactors' => $trustfactors, 'type' => 'content']) ?>

    <? if ($product->getSecondaryGroupedProperties()): // показываем все характеристики (сгруппированые), если ранее они не были показаны ?>
        <?= $helper->render('product/__groupedProperty', ['groupedProperties' => $product->getSecondaryGroupedProperties()]); // Характеристики ?>
    <? endif ?>

    <?= $page->render('product/_reviews', ['product' => $product, 'reviewsData' => $reviewsData, 'reviewsDataSummary' => $reviewsDataSummary, 'reviewsPresent' => $reviewsPresent]) ?>

    <? if (!$showSimilarOnTop): ?>
        <? if (\App::config()->product['pullRecommendation']): ?>
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
                'sender2' => $recommendationSender2,
            ]) ?>
        <? endif ?>
    <? endif ?>

</div><!--/left section -->

<div class="bProductSectionRightCol">

    <? if (5 !== $product->getStatusId() && (bool)$shopStates): // SITE-3109 ?>
        <div class="bWidgetBuy bWidgetBuy-shops mWidget js-WidgetBuy">
            <?= $helper->render('product/__shops', ['shopStates' => $shopStates, 'product' => $product, 'sender'  => $buySender, 'sender2'  => $buySender2, 'location'  => 'product-card']) // Доставка ?>
        </div>
    <? endif ?>

    <? if (!$product->isInShopStockOnly() && $product->getIsBuyable()): ?>
        <div class="bWidgetBuy mWidget js-WidgetBuy">
            <? if ($product->getIsBuyable() && !$product->isInShopStockOnly() && (5 !== $product->getStatusId())): ?>
                <?= $helper->render('__spinner', [
                    'id'        => \View\Id::cartButtonForProduct($product->getId()),
                    'productId' => $product->getId(),
                    'location'  => 'product-card',
                ]) ?>
            <? endif ?>

            <?= $helper->render('cart/__button-product', [
                'product'  => $product,
                'onClick'  => isset($addToCartJS) ? $addToCartJS : null,
                'sender'   => $buySender,
                'sender2'  => $buySender2,
                'location' => 'product-card',
            ]) // Кнопка купить ?>

            <div class="js-showTopBar"></div>

            <?= $helper->render('cart/__button-product-oneClick', ['product' => $product, 'sender'  => $buySender, 'sender2' => $buySender2, 'location'  => 'product-card']) ?>

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

    <? if ($lifeGiftProduct): ?>
        <?= $helper->render('cart/__button-product-lifeGift', ['product' => $lifeGiftProduct]) // Кнопка "Подари жизнь" ?>
    <? endif ?>

    <?= $helper->render('product/__adfox', ['product' => $product]) // Баннер Adfox ?>

    <? if ($product->isAvailable()): // SITE-4709 ?>
        <?= $helper->render('product/__trustfactors', ['trustfactors' => $trustfactors, 'type' => 'right']) ?>
    <? endif ?>
</div><!--/right section -->

<div class="clear"></div>

<? if (false && \App::config()->product['pullRecommendation']): ?>
    <?= $helper->render('product/__slider', [
        'type'     => 'alsoViewed',
        'title'    => 'С этим товаром также смотрят',
        'products' => [],
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
        'limit'     => \App::config()->product['itemsInSlider'],
        'page'      => 1,
        'url'       => $page->url('product.recommended', ['productId' => $product->getId()]),
        'sender'    => [
            'name'     => 'enter',
            'position' => 'Viewed',
            'from'     => 'productPage',
        ],
        'sender2' => $recommendationSender2,
    ]) ?>
<? endif ?>

<div class="bBreadCrumbsBottom"><?= $page->render('_breadcrumbs', ['breadcrumbs' => $breadcrumbs, 'class' => 'breadcrumbs-footer']) ?></div>

</div>
