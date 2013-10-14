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
 * @var $parts             \Model\Product\CompactEntity[]
 * @var $mainProduct       \Model\Product\Entity
 * @var $line              \Model\Line\Entity
 */

$showLinkToProperties = true;
$countModels = count($product->getModel());

//$countProperties = count($product->getProperty());
//$countProperties = count($product->getGroupedProperties());
$countProperties = 0;

//foreach ($product->getProperty() as $property) if ( $property->getValue() ) $countProperties++;
foreach ($product->getGroupedProperties() as $group) {
    if (!(bool)$group['properties']) continue;
    foreach ($group['properties'] as $property) {
        $countProperties++;
    }
}

$is_showed = [];

?>

<?= $helper->render('product/__data', ['product' => $product]) ?>

<div class="bProductSectionLeftCol">
    <?= $helper->render('product/__photo', ['product' => $product, 'productVideos' => $productVideos, 'useLens' => $useLens]) ?>

    <div class="bProductDesc">
        <?= $helper->render('product/__state', ['product' => $product]) // Есть в наличии ?>

        <?= $helper->render('product/__price', ['product' => $product]) // Цена ?>

        <?= $helper->render('product/__notification-lowerPrice', ['product' => $product]) // Узнать о снижении цены ?>

        <?= $helper->render('product/__credit', ['product' => $product, 'creditData' => $creditData]) // Купи в кредит ?>

            <?
            // new Card Properties Begin {
            if ( $product->getTagline() ) {
                ?>
                <div class="bProductDescText">
                    <?= $product->getTagline() ?>
                    <? /* <div class="bTextMore"><a class="jsGoToId" data-goto="productspecification" href="">Характеристики</a></div> */ ?>
                </div>
                <?= $helper->render('product/__reviewCount', ['product' => $product, 'reviewsData' => $reviewsData]) ?>
            <?
            } elseif (
                (!$countModels) &&
                ( !isset($product->getDescription) || (isset($product->getDescription) && !$product->getDescription) ) &&
                ($countProperties < 16)
            ) {
                echo $helper->render('product/__reviewCount', ['product' => $product, 'reviewsData' => $reviewsData]);

                // Выводим все характеристики товара в центральном блоке первого экрана карточки
                $showLinkToProperties = false;
                echo $helper->render('product/__propertiesSimple', ['product' => $product, 'showLinkToProperties' => $showLinkToProperties]);
                $is_showed[] = 'all_properties';
            }

            if ( $countProperties < 8 and empty($is_showed) ) {
                // выводим все характеристики в первом экране, сразу под отзывами.
                $showLinkToProperties = false;
                echo $helper->render('product/__propertiesSimple', ['product' => $product, 'showLinkToProperties' => $showLinkToProperties]);
                $is_showed[] = 'all_properties';

            }

            if (!in_array('all_properties', $is_showed)) { // Если ранее не были показаны характеристики все,
                // (во всех остальных случаях) выводим главные характеристики (productExpanded)
                echo $helper->render('product/__propertiesExpanded', ['productExpanded' => $productExpanded, 'showLinkToProperties' => $showLinkToProperties]);
                $is_showed[] = 'main_properties';
            }
            // } /end of new Card Properties
            ?>

            <?= $helper->render('product/__model', ['product' => $product]) // Модели ?>
    </div><!--/product shop description section -->

    <div class="clear"></div>

    <?= $helper->render('product/__likeButtons', [] ); // Insert LikeButtons (www.addthis.com) ?>

    <div class="clear"></div>

    <? if ( $mainProduct && count($mainProduct->getKit()) ): ?>
        <?= $helper->render('product/__slider', [
            'title'     => 'Состав набора &laquo;' . $line->getName() . '&raquo;',
            'products'  => $parts,
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
            //'url'            => $page->url('product.accessory', ['productToken' => $product->getToken()]),
            'gaEvent'        => 'Accessorize',
            'additionalData' => $additionalData,
        ]) ?>
    <? endif ?>

    <div class="bDescriptionProduct">
        <?= $product->getDescription() ?>
    </div>

    <? if (\App::config()->product['pullRecommendation']): ?>
        <?= $helper->render('product/__slider', [
            'type'     => 'alsoViewed',
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
            'type'           => 'alsoBought',
            'title'          => 'С этим товаром также покупают',
            'products'       => array_values($related),
            'count'          => count($product->getRelatedId()),
            'limit'          => \App::config()->product['itemsInSlider'],
            'page'           => 1,
            //'url'            => $page->url('product.related', ['productToken' => $product->getToken()]),
            'additionalData' => $additionalData,
        ]) ?>
    <? endif ?>

    <?
    if (!in_array('all_properties', $is_showed)) {
        // показываем все характеристики (сгруппированые), если ранее они не были показаны
        echo $helper->render('product/__groupedProperty', ['product' => $product]); // Характеристики
    }
    ?>

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

        <? if (\App::config()->product['pullRecommendation']): ?>
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
        <? if (!$product->isInShopStockOnly()): ?>
            <?= $helper->render('__spinner', ['id' => \View\Id::cartButtonForProduct($product->getId()), 'disabled' => !$product->getIsBuyable()]) ?>
        <? endif ?>

        <?= $helper->render('cart/__button-product', ['product' => $product, 'class' => 'btnBuy__eLink', 'value' => 'Купить', 'url' => $hasFurnitureConstructor ? $page->url('cart.product.setList') : null]) // Кнопка купить ?>

        <?= $helper->render('product/__oneClick', ['product' => $product]) // Покупка в один клик ?>

        <?= $helper->render('product/__delivery', ['product' => $product, 'shopStates' => $shopStates]) // Доставка ?>

        <?= $helper->render('product/__trustfactorMain', ['trustfactorMain' => $trustfactorMain]) ?>

        <?= $helper->render('cart/__button-product-paypal', ['product' => $product]) // Кнопка купить через paypal ?>
    </div><!--/widget delivery -->

    <?= $helper->render('product/__adfox', ['product' => $product]) // Баннер Adfox ?>

    <?//= $helper->render('product/__warranty', ['product' => $product]) ?>
    <?//= $helper->render('product/__service', ['product' => $product]) ?>

    <?= $helper->render('product/__trustfactorRight', ['trustfactorRight' => $trustfactorRight]) ?>
</div><!--/right section -->

<div class="clear"></div>

<div class="bBottomBuy clearfix">
    <div class="bBottomBuy__eHead">
        <div class="bBottomBuy__eTitle"><?= $title ?></div>
    </div>

    <?= $helper->render('cart/__button-product', ['product' => $product, 'class' => 'btnBuy__eLink', 'value' => 'Купить', 'url' => $hasFurnitureConstructor ? $page->url('cart.product.setList') : null]) // Кнопка купить ?>

    <? if (!$product->isInShopStockOnly()): ?>
        <?= $helper->render('__spinner', ['id' => \View\Id::cartButtonForProduct($product->getId()), 'disabled' => !$product->getIsBuyable()]) ?>
    <? endif ?>

    <div class="bPrice"><strong class="jsPrice"><?= $page->helper->formatPrice($product->getPrice()) ?></strong> <span class="rubl">p</span></div>
</div>

<div class="bBreadCrumbsBottom"><?= $page->render('_breadcrumbs', ['breadcrumbs' => $breadcrumbs, 'class' => 'breadcrumbs-footer']) ?></div>
