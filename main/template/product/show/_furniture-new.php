<?php
/**
 * @var $page              \View\Product\IndexPage
 * @var $product           \Model\Product\Entity
 * @var $productVideos     \Model\Product\Video\Entity[]
 * @var $user              \Session\User
 * @var $accessories       \Model\Product\Entity[]
 * @var $accessoryCategory \Model\Product\Category\Entity[]
 * @var $related           \Model\Product\Entity[]
 * @var $kit               \Model\Product\Entity[]
 * @var $additionalData    array
 * @var $shopStates        \Model\Product\ShopState\Entity[]
 * @var $creditData        array
 */
?>

<div id="jsProductCard" data-value="<?= $page->json($productData) ?>"></div>

<div class="bProductSectionLeftCol">
        <div id="planner3D" class="bPlanner3D fl" data-cart-sum-url="<?= $page->url('cart.sum') ?>" data-product="<?= $page->json(['id' => $product->getId()]) ?>"></div>

        <?= $helper->render('product/__likeButtons', [] ); // Insert LikeButtons (www.addthis.com) ?>

        <div class="bDescriptionProduct">
            <?= $product->getDescription() ?>
        </div>

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

        <? if (\App::config()->smartengine['pull']): ?>
            <?= $helper->render('product/__slider', [
                'type'     => 'also_viewed',
                'title'    => 'С этим товаром также смотрят',
                'products' => [],
                'count'    => null,
                'limit'    => \App::config()->product['itemsInSlider'],
                'page'     => 1,
                'url'      => $page->url('product.alsoViewed', ['productId' => $product->getId()]),
            ]) ?>
        <? endif ?>

        <? if ((bool)$related && \App::config()->product['showRelated']): ?>
            <?= $helper->render('product/__slider', [
                'type'           => 'also_bought',
                'title'          => 'С этим товаром также покупают',
                'products'       => array_values($related),
                'count'          => count($product->getRelatedId()),
                'limit'          => \App::config()->product['itemsInSlider'],
                'page'           => 1,
                //'url'            => $page->url('product.related', ['productToken' => $product->getToken()]),
                'additionalData' => $additionalData,
            ]) ?>
        <? endif ?>

        <?= $helper->render('product/__groupedProperty', ['product' => $product]) // Характеристики ?>

        <div class="bReviews">
            <? if (\App::config()->product['reviewEnabled'] && $reviewsPresent): ?>
            <h3 class="bHeadSection" id="bHeadSectionReviews">Обзоры и отзывы</h3>

            <div class="bReviewsSummary clearfix">
                <?= $page->render('product/_reviewsSummary', ['reviewsData' => $reviewsData, 'reviewsDataPro' => $reviewsDataPro, 'reviewsDataSummary' => $reviewsDataSummary]) ?>
            </div>

        <? if (!empty($reviewsData['review_list'])) { ?>
            <div class="bReviewsWrapper" data-product-id="<?= $product->getId() ?>" data-page-count="<?= $reviewsData['page_count'] ?>" data-container="reviewsUser" data-reviews-type="user">
                <? } elseif(!empty($reviewsDataPro['review_list'])) { ?>
                <div class="bReviewsWrapper" data-product-id="<?= $product->getId() ?>" data-page-count="<?= $reviewsDataPro['page_count'] ?>" data-container="reviewsPro" data-reviews-type="pro">
                    <? } ?>
                    <?= $page->render('product/_reviews', ['product' => $product, 'reviewsData' => $reviewsData, 'reviewsDataPro' => $reviewsDataPro]) ?>
                </div>
                <? endif ?>
            </div>

            <? if (\App::config()->smartengine['pull']): ?>
                <?= $helper->render('product/__slider', [
                    'type'     => 'similar',
                    'title'    => 'Похожие товары',
                    'products' => [],
                    'count'    => null,
                    'limit'    => \App::config()->product['itemsInSlider'],
                    'page'     => 1,
                    'url'      => $page->url('product.similar', ['productId' => $product->getId()]),
                ]) ?>
            <? endif ?>
</div><!--/left section -->

<div class="bProductSectionRightCol">
    <div class="bWidgetBuy mWidget">
        <div class="bStoreDesc">
            <?= $helper->render('product/__state', ['product' => $product]) // Есть в наличии ?>

            <?= $helper->render('product/__price', ['product' => $product]) // Цена ?>

            <?= $helper->render('product/__notification-lowerPrice', ['product' => $product]) // Узнать о снижении цены ?>

            <?//= $helper->render('product/__credit', ['product' => $product, 'creditData' => $creditData]) // Беру в кредит ?>
        </div>

        <?= $helper->render('cart/__button-product', ['product' => $product, 'class' => 'btnBuy__eLink', 'value' => 'Купить', 'url' => $hasFurnitureConstructor ? $page->url('cart.product.setList') : null]) // Кнопка купить ?>

        <div id="coupeError" class="red" style="display:none"></div>

        <?= $helper->render('product/__oneClick', ['product' => $product]) // Покупка в один клик ?>

        <?= $helper->render('product/__delivery', ['product' => $product, 'shopStates' => $shopStates]) // Доставка ?>
    </div><!--/widget delivery -->

    <?= $helper->render('product/__adfox', ['product' => $product]) // Баннер Adfox ?>

    <?= $helper->render('product/__trustfactorRight', ['trustfactorRight' => $trustfactorRight]) ?>
</div><!--/right section -->

<div class="bBottomBuy clearfix">
    <div class="bBottomBuy__eHead">
        <h1 class="bBottomBuy__eTitle"><?= $title ?></h1>
    </div>

    <?= $page->render('cart/_button', ['product' => $product, 'class' => 'btnBuy__eLink', 'value' => 'Купить', 'url' => $hasFurnitureConstructor ? $page->url('cart.product.setList') : null]) // Кнопка купить ?>

    <div class="bPrice"><strong class="jsPrice"><?= $page->helper->formatPrice($product->getPrice()) ?></strong> <span class="rubl">p</span></div>
</div>

<div class="bBreadCrumbsBottom"><?= $page->render('_breadcrumbs', ['breadcrumbs' => $breadcrumbs, 'class' => 'breadcrumbs-footer']) ?></div>
