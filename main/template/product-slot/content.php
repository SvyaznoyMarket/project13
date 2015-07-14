<?php
/**
 * @var $page                   \View\Product\IndexPage
 * @var $product                \Model\Product\Entity
 * @var $lifeGiftProduct        \Model\Product\Entity|null
 * @var $user                   \Session\User
 * @var $accessories            \Model\Product\Entity[]
 * @var $accessoryCategory      \Model\Product\Category\Entity[]
 * @var $kit                    \Model\Product\Entity[]
 * @var $shopStates             \Model\Product\ShopState\Entity[]
 * @var $creditData             array
 * @var $deliveryData           array
 * @var $isTchibo               boolean
 * @var $addToCartJS     string
 * @var $actionChannelName string
 * @var $kitProducts            array   Продукты кита
 * @var $reviewsData            array   Данные отзывов
 * @var $breadcrumbs            array   Хлебные крошки
 * @var $trustfactors           array   Трастфакторы
 * @var $reviewsDataSummary     array   Данные отзывов
 * @var $videoHtml              string|null
 * @var $properties3D           []
 */
$helper = new \Helper\TemplateHelper();

if (!isset($categoryClass)) $categoryClass = null;


$region = \App::user()->getRegion();
if (!$lifeGiftProduct) $lifeGiftProduct = null;
$isKitPage = (bool)$product->getKit();

$isProductAvailable = $product->isAvailable();

$secondaryGroupedProperties = $product->getSecondaryGroupedProperties(['Комплектация']);
$equipment = $product->getEquipmentProperty() ? preg_split('/(\r?\n)+/', trim($product->getEquipmentProperty()->getStringValue())) : null;
foreach ($equipment as $key => $value) {
    $equipment[$key] = preg_replace('/\s*<br \/>$/', '', trim(mb_strtoupper(mb_substr($value, 0, 1)) . mb_substr($value, 1)));
}

$buySender = $request->get('sender');
$buySender2 = $request->get('sender2');
?>

<div class="product-container product-container--kitchen clearfix">
    <? if (\App::abTest()->isNewProductPage()): ?>
        <?= !empty($breadcrumbs) ? $helper->renderWithMustache('product-page/blocks/breadcrumbs.mustache', ['breadcrumbs' => $breadcrumbs]) : '' ?>
    <? else: ?>
        <?= $page->render('_breadcrumbs', array('breadcrumbs' => $breadcrumbs, 'class' => 'bBreadcrumbs clearfix bBreadcrumbs--light')) ?>
    <? endif ?>

    <?= $helper->render('product/__data', ['product' => $product]) ?>

    <div class="product-card__section-left bProductSectionLeftCol">
        <div class="product-card__head">
            <h1 class="product-card__head__title clearfix" itemprop="name">
                    <? if ($product->getPrefix()): ?>
                        <?= $product->getPrefix() ?>
                    <? endif ?>
                    <?= $product->getWebName() ?>
            </h1>
        </div>

        <?= $helper->render('product/__photo', ['product' => $product, 'useLens' => $useLens, 'videoHtml' => $videoHtml, 'properties3D' => $properties3D]) ?>
    </div>

    <div class="product-card__section-right">
        <div class="product-card__vendor">Продавец-партнёр: <nobr><?= $helper->escape($product->getSlotPartnerOffer()['name']) ?></nobr></div>

        <?= $helper->render('product-slot/__price', ['product' => $product]) // Цена ?>

        <span class="product-card__info--price">Цена базового комплекта</span>
        <span class="product-card__info--deliv-period">Срок доставки базового комплекта 3 дня</span>
        <div class="product-card__info--recall">
            <span>Вам перезвонит специалист и поможет выбрать:</span>
            <ul class="product-card__info--recall__list">
                <li>состав комплекта и его изменения;</li>
                <li>условия доставки и сборки.</li>
            </ul>
            <?= $helper->render('cart/__button-product', [
                'product'  => $product,
                'sender'   => $buySender,
                'sender2'  => $buySender2,
                'location' => 'product-card',
            ]) ?>
        <div class="product-card__payment-types">Доступные способы оплаты:<br/>Наличные, банковский перевод</div>
        </div>

        <div class="product-card__specify" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
            <?= $helper->render('product/__mainProperties', ['product' => $product]) ?>
        </div>

        <div class="clear"></div>

        <div class="js-showTopBar"></div>

        <div class="bWidgetBuy mWidget compare--slot js-WidgetBuy">
            <?= $page->render('compare/_button-product-compare', ['product' => $product]) ?>
        </div>
    </div>

    <div class="clear"></div>

    <? /*
    <div class="product-card__bordered">
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
                'sender2'  => 'slot',
            ]) ?>
        <? endif ?>
    </div>
    */ ?>

    <div class="product-card__bordered">
        <div class="product-card__desc">
            <?= $product->getDescription() ?>
        </div>
        <? if ($equipment): ?>
            <div class="product-card__base-set">
                <h2 class="product-card__base-set-header">Базовый комплект</h2>
                <ul class="product-card__base-set-list">
                    <? foreach ($equipment as $equipmentItem): ?>
                        <li class="product-card__base-set-item"><?= $equipmentItem ?>.</li>
                    <? endforeach ?>
                </ul>
            </div>
        <? endif ?>
        <div class="product-card__props">
            <? if ($secondaryGroupedProperties): // показываем все характеристики (сгруппированые), если ранее они не были показаны ?>
                <?= $helper->render('product/__groupedProperty', ['groupedProperties' => $secondaryGroupedProperties]) // Характеристики ?>
            <? endif ?>
        </div>


        <div class="clear"></div>
        <? /* if (\App::config()->product['pullRecommendation']): ?>
            <?= $helper->render('product/__slider', [
                'type'           => 'alsoBought',
                'title'          => 'С этим товаром покупают',
                'products'       => [],
                'count'          => null,
                'limit'          => \App::config()->product['itemsInSlider'],
                'page'           => 1,
                'url'            => $page->url('product.recommended', ['productId' => $product->getId()]),
                'sender'         => [
                    'name'     => 'retailrocket',
                    'position' => 'ProductAccessories', // все правильно - так и надо!
                ],
                'sender2'  => 'slot',
            ]) ?>
        <? endif */ ?>

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
                    'position' => 'Viewed',
                    'from'     => 'productPage'
                ],
                'sender2'  => 'slot',
            ]) ?>
        <? endif ?>

        <div class="product-containter__brcr-bottom">
            <? if (\App::abTest()->isNewProductPage()): ?>
                <?= !empty($breadcrumbs) ? $helper->renderWithMustache('product-page/blocks/breadcrumbs.mustache', ['breadcrumbs' => $breadcrumbs]) : '' ?>
            <? else: ?>
                <?= $page->render('_breadcrumbs', ['breadcrumbs' => $breadcrumbs, 'class' => 'breadcrumbs-footer']) ?>
            <? endif ?>
        </div>

        <? if (\App::config()->analytics['enabled']): ?>
            <?= $page->tryRender('product/partner-counter/_cityads', ['product' => $product]) ?>
            <?//= $page->tryRender('product/partner-counter/_recreative', ['product' => $product]) ?>
        <? endif ?>

        <?= $page->tryRender('product/_tag', ['product' => $product]) ?>

        <?= $helper->render('product/__event', ['product' => $product]) ?>
    </div>
</div>