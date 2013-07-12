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

if (!isset($categoryClass)) $categoryClass = null;

$hasFurnitureConstructor = \App::config()->product['furnitureConstructor'] && $product->getLine() && (256 == $product->getLine()->getId()); // Серия Байкал

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

$showAveragePrice = \App::config()->product['showAveragePrice'] && !$product->getPriceOld() && $product->getPriceAverage();

$reviewsPresent = !(empty($reviewsData['review_list']) && empty($reviewsDataPro['review_list']));
?>

<? if ($hasFurnitureConstructor): ?>
    <? require __DIR__ . '/show/_furniture-new.php' ?>
<? elseif ($categoryClass && file_exists(__DIR__ . '/show/_' . $categoryClass . '.php')): ?>
    <? require __DIR__ . '/show/_' . $categoryClass . '.php' ?>
<? else: ?>
    <? require __DIR__ . '/show/_default-new.php' ?>
<? endif ?>

<? if ($product->getIsBuyable()): ?>
    <?= $page->render('order/form-oneClick') ?>
<? endif ?>

<? if (\App::config()->analytics['enabled']): ?>
    <?= $page->tryRender('product/partner-counter/_cityads', ['product' => $product]) ?>
    <?= $page->tryRender('product/partner-counter/_recreative', ['product' => $product]) ?>
<? endif ?>