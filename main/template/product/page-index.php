<?php
/**
 * @var $page               \View\Product\IndexPage
 * @var $product            \Model\Product\Entity
 * @var $user               \Session\User
 * @var $accessories        \Model\Product\Entity[]
 * @var $accessoryCategory  \Model\Product\Category\Entity[]
 * @var $kit                \Model\Product\Entity[]
 * @var $shopStates         \Model\Product\ShopState\Entity[]
 * @var $creditData         array
 * @var $deliveryDataResponse   array
 * @var $similarProducts    \Model\Product\Entity[]
 */

$helper = new \Helper\TemplateHelper();

if (!isset($categoryClass)) $categoryClass = null;

$reviewsPresent = !(empty($reviewsData['review_list']));
?>

<? if ($categoryClass && file_exists(__DIR__ . '/show/_' . $categoryClass . '.php')): ?>
    <? require __DIR__ . '/show/_' . $categoryClass . '.php' ?>
<? else: ?>
    <? require __DIR__ . '/show/_default.php' ?>
<? endif ?>

<? if (\App::config()->analytics['enabled']): ?>
    <?= $page->tryRender('product/partner-counter/_cityads', ['product' => $product]) ?>
    <?//= $page->tryRender('product/partner-counter/_recreative', ['product' => $product]) ?>
<? endif ?>

<?= $page->tryRender('product/_tag', ['product' => $product]) ?>

<?= $page->tryRender('product/_similarProducts', ['products' => $similarProducts]) ?>

<?= $helper->render('product/__event', ['product' => $product]) ?>