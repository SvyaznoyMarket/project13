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

$reviewsPresent = !(empty($reviewsData['review_list']) && empty($reviewsDataPro['review_list']));
?>

<? if ($hasFurnitureConstructor): ?>
    <? require __DIR__ . '/show/_furniture.php' ?>
<? elseif ($categoryClass && file_exists(__DIR__ . '/show/_' . $categoryClass . '.php')): ?>
    <? require __DIR__ . '/show/_' . $categoryClass . '.php' ?>
<? else: ?>
    <? require __DIR__ . '/show/_default.php' ?>
<? endif ?>

<? if ($product->getIsBuyable()): ?>
    <?= $page->render('order/form-oneClick') ?>
<? endif ?>

<? if (\App::config()->analytics['enabled']): ?>
    <?= $page->tryRender('product/partner-counter/_cityads', ['product' => $product]) ?>
    <?= $page->tryRender('product/partner-counter/_recreative', ['product' => $product]) ?>
<? endif ?>

<? if (false && \App::config()->product['pushRecommendation']): //TODO: почистить ?>
    <div id="jsAlsoViewedProduct" data-url="<?= $page->url('smartengine.push.product_view', ['productId' => $product->getId()]) ?>"></div>
<? endif ?>

<?= $page->tryRender('product/_tag', ['product' => $product]) ?>