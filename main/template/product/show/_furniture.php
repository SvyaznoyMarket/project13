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
 * @var $isUserSubscribedToEmailActions boolean
 * @var $actionChannelName string
 */

$isKitPage = (bool)$product->getKit();
?>

<?= $helper->render('product/__data', ['product' => $product]) ?>

<div class="bProductSectionLeftCol">
    <div id="planner3D" class="bPlanner3D fl" data-cart-sum-url="<?= $page->url('cart.sum') ?>" data-product="<?= $page->json(['id' => $product->getId()]) ?>"></div>

    <?= $helper->render('product/__likeButtons', [] ); // Insert LikeButtons (www.addthis.com) ?>

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

    <?= $helper->render('product/__trustfactors', ['trustfactors' => $trustfactors, 'type' => 'content']) ?>

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

    <?= $helper->render('product/__groupedProperty', ['groupedProperties' => $product->getGroupedProperties()]) // Характеристики ?>

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
    <div class="bWidgetBuy mWidget js-WidgetBuy">
        <div class="bStoreDesc">
            <?= $helper->render('product/__state', ['product' => $product]) // Есть в наличии ?>

            <?= $helper->render('product/__price', ['product' => $product]) // Цена ?>

            <?= $helper->render('product/__notification-lowerPrice', ['product' => $product, 'isUserSubscribedToEmailActions' => $isUserSubscribedToEmailActions, 'actionChannelName' => $actionChannelName]) // Узнать о снижении цены ?>

            <?//= $helper->render('product/__credit', ['product' => $product, 'creditData' => $creditData]) // Купи в кредит ?>
        </div>

        <?= $helper->render('cart/__button-product', [
            'product' => $product,
            'onClick' => isset($addToCartJS) ? $addToCartJS : null,
        ]) // Кнопка купить ?>

        <div class="js-showTopBar"></div>

        <div id="coupeError" class="red" style="display:none"></div>

        <? if (!$isKitPage || $product->getIsKitLocked()): ?>
            <?= $helper->render('cart/__button-product-oneClick', ['product' => $product]) // Покупка в один клик ?>
        <? endif ?>

        <? if (5 !== $product->getStatusId()): // SITE-3109 ?>
            <?= $helper->render('product/__delivery', ['product' => $product, 'deliveryData' => $deliveryData, 'shopStates' => $shopStates]) // Доставка ?>
        <? endif ?>
    </div><!--/widget delivery -->

    <?= $helper->render('product/__adfox', ['product' => $product]) // Баннер Adfox ?>

    <?= $helper->render('product/__trustfactors', ['trustfactors' => $trustfactors, 'type' => 'right']) ?>
</div><!--/right section -->

<div class="bBreadCrumbsBottom"><?= $page->render('_breadcrumbs', ['breadcrumbs' => $breadcrumbs, 'class' => 'breadcrumbs-footer']) ?></div>

</div>
