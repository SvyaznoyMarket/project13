<?php
/**
 * @var $page              \View\Product\IndexPage
 * @var $product           \Model\Product\Entity
 * @var $productVideos     \Model\Product\Video\Entity[]
 * @var $user              \Session\User
 * @var $accessories       \Model\Product\Entity[]
 * @var $accessoryCategory \Model\Product\Category\Entity[]
 * @var $kit               \Model\Product\Entity[]
 * @var $additionalData    array
 * @var $shopStates        \Model\Product\ShopState\Entity[]
 * @var $creditData        array
 * @var $deliveryData      array
 * @var $isTchibo          boolean
 * @var $addToCartJS string
 */
?>

<?= $helper->render('product/__data', ['product' => $product]) ?>

<div class="bProductSectionLeftCol">
    <?= $helper->render('product/__photo', ['product' => $product, 'productVideos' => $productVideos, 'useLens' => $useLens]) ?>

    <div class="bProductDesc<? if (!$creditData['creditIsAllowed'] || $user->getRegion()->getHasTransportCompany()): ?> mNoCredit<? endif ?>">
        <?= $helper->render('product/__state', ['product' => $product]) // Есть в наличии ?>

        <?= $helper->render('product/__price', ['product' => $product]) // Цена ?>

        <?= $helper->render('product/__notification-lowerPrice', ['product' => $product, 'isUserSubscribedToEmailActions' => $isUserSubscribedToEmailActions]) // Узнать о снижении цены ?>

        <?= $helper->render('product/__credit', ['product' => $product, 'creditData' => $creditData]) // Купи в кредит ?>

        <? /* // Old Card Properties
        <div class="bProductDescText">
            <?= $product->getTagline() ?>

            <?= $helper->render('product/__propertiesSimple', ['product' => $product, 'showLinkToProperties' => false]) // Характеристики ?>
        </div>
        */ ?>

        <?
        // new Card Properties Begin {
        if ($product->getTagline()) {
        ?>
            <div itemprop="description" class="bProductDescText">
                <?= $product->getTagline() ?>
                <? /* <div class="bTextMore"><a class="jsGoToId" data-goto="productspecification" href="">Характеристики</a></div> */ ?>
            </div>
        <?
        }

        echo $helper->render('product/__reviewCount', ['product' => $product, 'reviewsData' => $reviewsData]);
        echo $helper->render('product/__mainProperties', ['product' => $product]);
        // } /end of new Card Properties
        ?>

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
            'count'          => count($product->getAccessoryId()),
            'limit'          => (bool)$accessoryCategory ? \App::config()->product['itemsInAccessorySlider'] : \App::config()->product['itemsInSlider'],
            'page'           => 1,
            //'url'            => $page->url('product.accessory', ['productToken' => $product->getToken()]),
            'gaEvent'        => 'Accessorize',
            'additionalData' => $additionalData,
        ]) ?>
    <? endif ?>

    <div class="bDescriptionProduct">
        <?= $product->getDescription() ?>
    </div>

    <?= $helper->render('product/__trustfactorContent', ['trustfactorContent' => $trustfactorContent]) ?>

    <? if (\App::config()->product['showRelated'] && !$isTchibo): ?>
        <?= $helper->render('product/__slider', [
            'type'           => 'alsoBought',
            'title'          => 'С этим товаром также покупают',
            'products'       => [],
            'count'          => null,
            'limit'          => \App::config()->product['itemsInSlider'],
            'page'           => 1,
            'url'            => $page->url('product.recommended', ['productId' => $product->getId()]),
            'additionalData' => $additionalData,
        ]) ?>
    <? endif ?>

    <? if (\App::config()->product['pullRecommendation'] && !$isTchibo): ?>
        <?= $helper->render('product/__slider', [
            'type'     => 'similar',
            'title'    => 'Похожие товары',
            'products' => [],
            'count'    => null,
            'limit'    => \App::config()->product['itemsInSlider'],
            'page'     => 1,
            'url'      => $page->url('product.recommended', ['productId' => $product->getId()]),
        ]) ?>
    <? endif ?>

    <?
    if ($product->getSecondaryGroupedProperties()) {
        // показываем все характеристики (сгруппированые), если ранее они не были показаны
        echo $helper->render('product/__groupedProperty', ['groupedProperties' => $product->getSecondaryGroupedProperties()]); // Характеристики
    }
    ?>

    <?= $page->render('product/_reviews', ['product' => $product, 'reviewsData' => $reviewsData, 'reviewsDataSummary' => $reviewsDataSummary, 'reviewsPresent' => $reviewsPresent, 'sprosikupiReviews' => $sprosikupiReviews, 'shoppilotReviews' => $shoppilotReviews]) ?>

    <? if (\App::config()->product['pullRecommendation'] && !$isTchibo): ?>
        <?= $helper->render('product/__slider', [
            'type'     => 'alsoViewed',
            'title'    => 'С этим товаром также смотрят',
            'products' => [],
            'count'    => null,
            'limit'    => \App::config()->product['itemsInSlider'],
            'page'     => 1,
            'url'      => $page->url('product.recommended', ['productId' => $product->getId()]),
        ]) ?>
    <? endif ?>
</div><!--/left section -->

<div class="bProductSectionRightCol">

    <? if (5 !== $product->getStatusId() && (bool)$shopStates): // SITE-3109 ?>
        <div class="bWidgetBuy bWidgetBuy-shops mWidget js-WidgetBuy">
            <?= $helper->render('product/__shops', ['shopStates' => $shopStates, 'product' => $product]) // Доставка ?>
        </div>
    <? endif ?>

    <? if ( $product->isInShopStockOnly() || !$product->getIsBuyable() ) : else : ?>
    <div class="bWidgetBuy mWidget js-WidgetBuy">
        <? if ($product->getIsBuyable() && !$product->isInShopStockOnly() && (5 !== $product->getStatusId())): ?>
            <?= $helper->render('__spinner', ['id' => \View\Id::cartButtonForProduct($product->getId()), 'productId' => $product->getId()]) ?>
        <? endif ?>

        <?= $helper->render('cart/__button-product', [
            'product' => $product,
            'url' => $hasFurnitureConstructor ? $page->url('cart.product.setList') : null,
            'onClick' => isset($addToCartJS) ? $addToCartJS : null,
        ]) // Кнопка купить ?>

        <?= $helper->render('cart/__button-product-oneClick', ['product' => $product]) // Покупка в один клик ?>

        <? if (5 !== $product->getStatusId()): // SITE-3109 ?>
            <?= $helper->render('product/__delivery', ['product' => $product, 'deliveryData' => $deliveryData, 'shopStates' => $shopStates]) // Доставка ?>
        <? endif ?>

        <?= $helper->render('cart/__button-product-paypal', ['product' => $product]) // Кнопка купить через paypal ?>

        <?= $helper->render('product/__trustfactorMain', ['trustfactorMain' => $trustfactorMain]) ?>
    </div><!--/widget delivery -->
    <? endif; ?>

    <?= $helper->render('product/__adfox', ['product' => $product]) // Баннер Adfox ?>

    <?//= $helper->render('product/__warranty', ['product' => $product]) ?>

    <?//= $helper->render('product/__service', ['product' => $product]) ?>

    <?= $helper->render('product/__trustfactorRight', ['trustfactorRight' => $trustfactorRight]) ?>
</div><!--/right section -->

<div class="clear"></div>

<div class="bBreadCrumbsBottom"><?= $page->render('_breadcrumbs', ['breadcrumbs' => $breadcrumbs, 'class' => 'breadcrumbs-footer']) ?></div>

</div>
