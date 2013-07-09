<?php
/**
 * @var $page               \View\Product\IndexPage
 * @var $product            \Model\Product\Entity
 * @var $productVideos      \Model\Product\Video\Entity[]
 * @var $user               \Session\User
 * @var $accessories        \Model\Product\Entity[]
 * @var $accessoryCategory  \Model\Product\Category\Entity[]
 * @var $related            \Model\Product\Entity[]
 * @var $kit                \Model\Product\Entity[]
 * @var $additionalData     array
 * @var $shopStates         \Model\Product\ShopState\Entity[]
 * @var $creditData         array
 */
?>

<?

$helper = new \Helper\TemplateHelper();

$hasLowerPriceNotification =
    \App::config()->product['lowerPriceNotification']
    && $product->getMainCategory() && $product->getMainCategory()->getPriceChangeTriggerEnabled();

$hasFurnitureConstructor = \App::config()->product['furnitureConstructor'] && $product->getLine() && (256 == $product->getLine()->getId()); // Серия Байкал

/** @var  $productVideo \Model\Product\Video\Entity|null */
$productVideo = reset($productVideos);

$productData = [
    'id'      => $product->getId(),
    'token'   => $product->getToken(),
    'article' => $product->getArticle(),
    'name'    => $product->getName(),
    'price'   => $product->getPrice(),
    'image'   => [
        'default' => $product->getImageUrl(3),
        'big'     => $product->getImageUrl(2),
    ],
    'isSupplied'  => $product->getState() ? $product->getState()->getIsSupplier() : false,
    'stockState'  =>
    $product->getIsBuyable()
        ? 'in stock'
        : (
    ($product->getState() && $product->getState()->getIsShop())
        ? 'at shop'
        : 'out of stock'
    ),
];

$shopData = [];
foreach ($shopStates as $shopState) {
    $shop = $shopState->getShop();
    if (!$shop instanceof \Model\Shop\Entity) continue;

    $shopData[] = [
        'id'        => $shop->getId(),
        'name'      => $shop->getName(),
        'address'   => $shop->getAddress(),
        'regtime'   => $shop->getRegime(),
        'longitude' => $shop->getLongitude(),
        'latitude'  => $shop->getLatitude(),
        'url'       => $page->url('shop.show', ['shopToken' => $shop->getToken(), 'regionToken' => $user->getRegion()->getToken()]),
    ];
}


$photoList = $product->getPhoto();

/** @var string $model3dExternalUrl */
$model3dExternalUrl = ($productVideo instanceof \Model\Product\Video\Entity) ? $productVideo->getMaybe3d() : false;
/** @var string $model3dImg */
$model3dImg = ($productVideo instanceof \Model\Product\Video\Entity) ? $productVideo->getImg3d() : false;
/** @var array $photo3dList */
$photo3dList = [];
/** @var array $p3d_res_small */
$p3d_res_small = [];
/** @var array $p3d_res_big */
$p3d_res_big = [];

if (!$model3dExternalUrl && !$model3dImg) {
    $photo3dList = $product->getPhoto3d();
    foreach ($photo3dList as $photo3d) {
        $p3d_res_small[] = $photo3d->getUrl(0);
        $p3d_res_big[] = $photo3d->getUrl(1);
    }
} elseif ($model3dExternalUrl) {
    $model3dName = preg_replace('/\.swf|\.swf$/iu', '', basename($model3dExternalUrl));
    if (!strlen($model3dName)) $model3dExternalUrl = false;
}

$showAveragePrice = \App::config()->product['showAveragePrice'] && !$product->getPriceOld() && $product->getPriceAverage();

$adfox_id_by_label = 'adfox400';
if ($product->getLabel()) {
    switch ($product->getLabel()->getId()) {
        case \Model\Product\Label\Entity::LABEL_PROMO:
            $adfox_id_by_label = 'adfox400counter';
            break;
        case \Model\Product\Label\Entity::LABEL_CREDIT:
            $adfox_id_by_label = 'adfoxWowCredit';
            break;
        case \Model\Product\Label\Entity::LABEL_GIFT:
            $adfox_id_by_label = 'adfoxGift';
            break;
    }
}

$reviewsPresent = !(empty($reviewsData['review_list']) && empty($reviewsDataPro['review_list']));
?>


<? if ($model3dExternalUrl) :

    $arrayToMaybe3D = [
        'init' => [
            'swf'       => $model3dExternalUrl,
            'container' => 'maybe3dModel',
            'width'     => '700px',
            'height'    => '500px',
            'version'   => '10.0.0',
            'install'   => 'js/vendor/expressInstall.swf',
        ],
        'params' => [
            'menu'              => 'false',
            'scale'             => 'noScale',
            'allowFullscreen'   => 'true',
            'allowScriptAccess' => 'always',
            'wmode'             => 'direct',
        ],
        'attributes' => [
            'id' => $model3dName,
        ],
        'flashvars' => [
            'language' => "auto",
        ]

    ];

    ?>

    <div id="maybe3dModelPopup" class="popup" data-value="<?php print $page->json($arrayToMaybe3D); ?>">
        <i class="close" title="Закрыть">Закрыть</i>
        <div id="maybe3dModelPopup_inner" style="position: relative;">
            <div id="maybe3dModel">
                <a href="http://www.adobe.com/go/getflashplayer">
                    <img src="http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif" alt="Get Adobe Flash player" />
                </a>
            </div>
        </div>
    </div>

<? endif ?>

<? if ($model3dImg) : ?>
    <div id="3dModelImg" class="popup" data-value="<?php print $page->json($model3dImg); ?>" data-host="<?= $page->json(['http://'.App::request()->getHost()]) ?>">
        <i class="close" title="Закрыть">Закрыть</i>
    </div>
<? endif ?>

<script type="text/javascript">
    <? if ($model3dExternalUrl) : ?>
    product_3d_url = <?= json_encode($model3dExternalUrl) ?>;
    <? elseif (count($photo3dList) > 0) : ?>
    product_3d_small = <?= json_encode($p3d_res_small) ?>;
    product_3d_big = <?= json_encode($p3d_res_big) ?>;
    <? endif ?>
</script>

<div id="jsProductCard" data-value="<?= $page->json($productData) ?>"></div>

<div class="bProductSection__eLeft">
    <section>
       <? if ($hasFurnitureConstructor): ?>
            <? require __DIR__ . '/show/_furniture-new.php' ?>
        <? else: ?>
            <? require __DIR__ . '/show/_default-new.php' ?>
        <? endif ?>


        <div class="bDescriptionProduct">
            <?= $product->getDescription() ?>
        </div>

        <? if ((bool)$accessories && \App::config()->product['showAccessories']): ?>
            <h3 class="bHeadSection">Аксессуары</h3>
            <?= $helper->render('product/__slider', [
                'products'       => array_values($accessories),
                'categories'     => $accessoryCategory,
                'count'          => count($product->getAccessoryId()),
                'limit'          => (bool)$accessoryCategory ? \App::config()->product['itemsInAccessorySlider'] : \App::config()->product['itemsInSlider'],
                'page'           => 1,
                'url'            => $page->url('product.accessory', ['productToken' => $product->getToken()]),
                'gaEvent'        => 'Accessorize',
                'additionalData' => $additionalData,
            ]) ?>
        <? endif ?>

        <? if ((bool)$related && \App::config()->product['showRelated']): ?>
            <h3 class="bHeadSection">С этим товаром также покупают</h3>
            <?= $helper->render('product/__slider', [
                'products'       => array_values($related),
                'count'          => count($product->getRelatedId()),
                'limit'          => \App::config()->product['itemsInSlider'],
                'page'           => 1,
                'url'            => $page->url('product.related', ['productToken' => $product->getToken()]),
                'additionalData' => $additionalData,
            ]) ?>
        <? endif ?>

        <h3 id="productspecification"  class="bHeadSection">Характеристики</h3>
        <? $groupedProperties = $product->getGroupedProperties() ?>
        <div class="bSpecifications">
        <? foreach ($groupedProperties as $key => $group): ?>
            <? if (!(bool)$group['properties']) continue ?>

            <div class="bSpecifications__eHead"><?= $group['group']->getName() ?></div>
            <dl class="bSpecifications__eList clearfix">
            <? foreach ($group['properties'] as $property): ?>
            <? /** @var $property \Model\Product\Property\Entity  */?>
                <dd>
                    <span><?= $property->getName() ?>
                    <? if ($property->getHint()): ?>
                        <?= $helper->render('__hint', ['name' => $property->getName(), 'value' => $property->getHint()]) ?>
                    <? endif ?>
                    </span>
                </dd>
                <dt>
                    <?= $property->getStringValue() ?>
                    <? if ($property->getValueHint()): ?>
                        <?= $helper->render('__hint', ['name' => $property->getStringValue(), 'value' => $property->getValueHint()]) ?>
                    <? endif ?>
                </dt>
            <? endforeach ?>
            </dl>
        <? endforeach ?>
        </div><!--/product specifications section -->

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


        <? if (!$product->getIsBuyable() && $product->getState()->getIsShop() && \App::config()->smartengine['pull']): ?>
            <h3 class="bHeadSection">Похожие товары</h3>
            <?= $helper->render('product/__slider', [
                'products'       => [],
                'count'          => null,
                'limit'          => \App::config()->product['itemsInSlider'],
                'page'           => 1,
                'url'            => $page->url('smartengine.pull.product_similar', ['productId' => $product->getId()]),
                //'additionalData' => $additionalData,
            ]) ?>
        <? endif ?>


    </section>
</div><!--/left section -->

<div class="bProductSection__eRight">
    <aside>
        <div class="bWidgetBuy mWidget">
            <?= $helper->render('__spinner', ['id' => \View\Id::cartButtonForProduct($product->getId())]) ?>

            <div class="bWidgetBuy__eBuy btnBuy">
                <?= $helper->render('cart/__button-product', ['product' => $product, 'class' => 'btnBuy__eLink', 'value' => 'В корзину']) ?>
            </div><!--/button buy -->

            <? if ($product->getIsBuyable()): ?>
                <div class="bWidgetBuy__eClick">
                    <a
                        href="#"
                        class="jsOrder1click"
                        data-model="<?= $page->json([
                            'jsref'        => $product->getToken(),
                            'jstitle'      => $product->getName(),
                            'jsprice'      => $product->getPrice(),
                            'jsimg'        => $product->getImageUrl(3),
                            'jsbimg'       => $product->getImageUrl(2),
                            'jsshortcut'   => $product->getArticle(),
                            'jsitemid'     => $product->getId(),
                            'jsregionid'   => $user->getRegion()->getId(),
                            'jsregionName' => $user->getRegion()->getName(),
                            'jsstock'      => 10,
                        ]) ?>"
                        link-output="<?= $page->url('order.1click', ['product' => $product->getToken()]) ?>"
                        link-input="<?= $page->url('product.delivery_1click') ?>"
                        >Купить быстро в 1 клик</a>
                </div>
                <form id="order1click-form" action="<?= $page->url('order.1click', ['product' => $product->getBarcode()]) ?>" method="post"></form>
            <? endif ?>

            <?= $helper->render('product/__delivery', ['product' => $product]) ?>

            <div class="bAwardSection"><img src="/css/newProductCard/img/award.jpg" alt="" /></div>
        </div><!--/widget delivery -->

        <? if ((bool)$product->getWarranty()): ?>
            <?= $helper->render('product/__warranty', ['product' => $product]) ?>
        <? endif ?>

        <? if ((bool)$product->getService()): ?>
            <?= $helper->render('product/__service', ['product' => $product]) ?>
        <? endif ?>
    </aside>
</div><!--/right section -->

<div class="bBottomBuy clearfix">
    <div class="bBottomBuy__eHead">
        <div class="bBottomBuy__eSubtitle"><?= $product->getType()->getName() ?></div>
        <h1 class="bBottomBuy__eTitle"><?= $title ?></h1>
    </div>

    <div class="bWidgetBuy__eBuy btnBuy">
        <?= $page->render('cart/_button', ['product' => $product, 'class' => 'btnBuy__eLink', 'value' => 'В корзину']) ?>
    </div><!--/button buy -->

    <?= $helper->render('__spinner', ['id' => \View\Id::cartButtonForProduct($product->getId())]) ?>

    <div class="price"><strong><?= $page->helper->formatPrice($product->getPrice()) ?></strong> <span class="rubl">p</span></div>
</div>

<div class="bBreadCrumbsBottom"><?= $page->render('_breadcrumbs', ['breadcrumbs' => $breadcrumbs, 'class' => 'breadcrumbs-footer']) ?></div>

<? if ($product->getIsBuyable()): ?>
    <?= $page->render('order/form-oneClick') ?>
<? endif ?>

<? if (\App::config()->analytics['enabled']): ?>
    <?= $page->tryRender('product/partner-counter/_cityads', ['product' => $product]) ?>
    <?= $page->tryRender('product/partner-counter/_recreative', ['product' => $product]) ?>
<? endif ?>