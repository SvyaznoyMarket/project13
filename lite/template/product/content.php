<?
/**
 * @var $page                   \View\Product\IndexPage
 * @var $product                \Model\Product\Entity
 * @var $lifeGiftProduct        \Model\Product\Entity|null
 * @var $user                   \Session\User
 * @var $accessories            \Model\Product\Entity[]
 * @var $accessoryCategory      \Model\Product\Category\Entity[]
 * @var $kit                    \Model\Product\Entity[]
 * @var $relatedKits            array
 * @var $additionalData         array
 * @var $shopStates             \Model\Product\ShopState\Entity[]
 * @var $creditData             array
 * @var $favoriteProductsByUi   \Model\Favorite\Product\Entity[]
 * @var $isTchibo               boolean
 * @var $addToCartJS     string
 * @var $isUserSubscribedToEmailActions boolean
 * @var $actionChannelName string
 * @var $kitProducts            array   Продукты кита
 * @var $useLens                bool    Показывать лупу
 * @var $reviewsData            array   Данные отзывов
 * @var $breadcrumbs            array   Хлебные крошки
 * @var $trustfactors           array   Трастфакторы
 * @var $reviewsDataSummary     array   Данные отзывов
 * @var $videoHtml              string|null
 * @var $properties3D           []
 * @var $isKit                  bool
 * @var $similarProducts        \Model\Product\Entity[]
 */

$helper = \App::helper();

$isProductAvailable = $product->isAvailable();
if (\App::config()->preview) $isProductAvailable = true;

$showAccessories = $accessories && \App::config()->product['showAccessories'];

/* Показывать блок "Подробнее" если есть описание или инструкции или характеристики */
$hasMedia = array_filter($trustfactors, function(\Model\Product\Trustfactor $t) { return $t->media && $t->media->isFile(); });
$showDescription = $product->getDescription()
    || $hasMedia
    || $product->getSecondaryGroupedProperties();

$buySender = ($request->get('sender') ? (array)$request->get('sender') : \Session\ProductPageSenders::get($product->getUi())) + ['name' => null, 'method' => null, 'position' => null];
$buySender2 = $request->get('sender2');

$reviewsData = null;

?>

<div id="product-info"
     data-ui="<?= $product->getUi() ?>"
    ></div>

<div class="product-card js-module-require" data-module="enter.product.viewed">
    <?= !empty($breadcrumbs) ? $helper->renderWithMustache('product/blocks/breadcrumbs.mustache', ['breadcrumbs' => $breadcrumbs]) : '' ?>

    <script type="application/json" class="js-product-json"><?= json_encode([
            'id'    => $product->getId(),
            'name'  => $product->getName(),
            'url'   => $product->getLink(),
            'image120' => $product->getMainImageUrl('product_120')
        ])?></script>

	<h1 class="product-name"><?= $product->getName() ?></h1>

	<? if ($product->isOnlyFromPartner() && $product->getPartnerName()) : ?>
        <!-- Информация о партнере -->
        <div class="vendor-offer">
            <a href="<?= $product->getPartnerOfferLink() ?>" class="vendor-offer__lk i-info jsProductPartnerOffer" target="_blank">
                <span class="i-info__tx">Продавец: <?= $product->getPartnerName() ?></span> <i class="i-info__icon i-product i-product--info-normal "></i>
            </a>
        </div>
        <!-- /Информация о партнере -->
    <? endif ?>

	<!-- карточка товара -->
    <?= $helper->render( $product->isAvailable() ? 'product/blocks/product' : 'product/blocks/product.not_available', [
        'product' => $product,
        'trustfactors' => $trustfactors,
        'videoHtml' => $videoHtml,
        'properties3D' => $properties3D,
        'reviewsData' => $reviewsData,
        'creditData' => $creditData,
        'isKit' => $isKit,
        'buySender' => $buySender,
        'buySender2' => $buySender2,
        'request' => \App::request(),
        'favoriteProductsByUi' => $favoriteProductsByUi
    ]) ?>
	<!--/ карточка товара -->

	<!-- с этим товаром покупают -->
	<div class="product-section section_border">
        <? if (\App::config()->product['pullRecommendation']): ?>
            <?= $helper->render('product/blocks/slider', [
                'type'           => 'alsoBought',
                'title'          => 'С этим товаром покупают',
                'products'       => [],
                'count'          => null,
                'limit'          => \App::config()->product['itemsInSlider'],
                'page'           => 1,
//                'additionalData' => $additionalData,
                'url'            => $page->url('product.recommended', ['productId' => $product->getId()]),
                'sender'         => [
                    'name'     => 'retailrocket',
                    'position' => $isProductAvailable ? 'ProductAccessories' : 'ProductMissing', // все правильно - так и надо!
                ],
                'sender2' => $buySender2,
            ]) ?>
        <? endif ?>
    </div>
    <!--/ с этим товаром покупают -->

    <? /* Трастфакторы партнеров */ ?>
    <?/* = $helper->render('product/blocks/trustfactors.partner', ['trustfactors' => $trustfactors]) */?>

    <!-- навигация по странице -->
    <ul class="nav product-tabs">
        <? if ($product->getKit()) : ?><li class="product-tabs__i"><a class="product-tabs__lk jsScrollSpyKitLink" href="#kit" title="">Состав</a></li><? endif ?>
        <? if ($showDescription) : ?><li class="product-tabs__i"><a class="product-tabs__lk jsScrollSpyMoreLink" href="#more" title="">Подробности</a></li><? endif ?>
        <? if ($showAccessories) : ?><li class="product-tabs__i"><a class="product-tabs__lk jsScrollSpyAccessorizeLink" href="#accessorize" title="">Аксессуары</a></li><? endif ?>
        <? if (false) : ?><li class="product-tabs__i"><a class="product-tabs__lk jsScrollSpyReviewsLink" href="#reviews" title="">Отзывы</a></li><? endif ?>
        <? if ($product->isAvailable()) : ?><li class="product-tabs__i jsSimilarTab" style="display: none"><a class="product-tabs__lk jsScrollSpySimilarLink" href="#similar" title="">Похожие товары</a></li><? endif ?>
    </ul>
    <!--/ навигация по странице -->

    <? if ($isKit) : ?>
        <?= $helper->render('product/blocks/kit', ['product' => $product, 'products' => $kitProducts, 'sender' => $buySender, 'sender2' => $buySender2]) ?>
    <? endif ?>

    <? if ($showDescription) : ?>

        <!-- характеристики/описание товара -->
        <div class="product-section grid-2col" id="more">

            <?= $helper->render('product/blocks/properties', ['product' => $product]) ?>

            <? if ($hasMedia || $product->getDescription()) : ?>

                <div class="grid-2col__item">
                    <div class="product-section__desc">
                        <div class="product-section__tl">Описание</div>
                        <?= $helper->render('product/blocks/guides', ['trustfactors' => $trustfactors]) ?>
                        <div class="product-section__content"><?= $product->getDescription() ?></div>
                    </div>
                </div>

            <? endif ?>
        </div>
        <!--/ характеристики/описание товара -->

    <? endif ?>

    <? if ($showAccessories): ?>
    <!-- аксессуары -->
	<div class="product-section">
		<div class="product-section__tl" id="accessorize">Аксессуары</div>

            <?= $helper->render('product/blocks/slider', [
                'type'           => 'accessorize',
                'title'          => null,
                'products'       => array_values($accessories),
                'categories'     => $accessoryCategory,
                'count'          => count($product->getAccessoryId()),
                'limit'          => (bool)$accessoryCategory ? \App::config()->product['itemsInAccessorySlider'] : \App::config()->product['itemsInSlider'],
                'page'           => 1,
                //'url'            => $page->url('product.accessory', ['productToken' => $product->getToken()]),
                'gaEvent'        => 'Accessorize',
                'additionalData' => $additionalData,
                'class'          => (bool)$accessoryCategory ? 'slideItem-3item' : 'slideItem-5item',
                'sender'         => [
                    'name'     => 'enter',
                    'position' => $isProductAvailable ? 'ProductAccessoriesManual' : 'ProductMissing',
                ],
                'sender2' => $buySender2,
            ]) ?>
	</div>
	<!--/ аксессуары -->
    <? endif ?>

	<? if ($reviewsData) : ?>
        <!-- отзывы -->
        <div class="product-section product-section--reviews" id="reviews">
            <div class="product-section__tl">Отзывы</div>

            <?= $helper->render('product/blocks/reviews', ['reviewsData' => $reviewsData, 'product' => $product ]) ?>
        </div>
        <!--/ отзывы -->
    <? endif ?>

	<!-- похожие товары -->
	<div class="product-section product-section--inn" id="similar">
        <? if ($isProductAvailable && \App::config()->product['pullRecommendation']): ?>
            <?= $helper->render('product/blocks/slider', [
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
                'sender2' => $buySender2,
            ]) ?>
        <? endif ?>
	</div>
	<!--/ похожие товары -->

	<!-- вы смотрели -->
    <?= $page->blockViewed() ?>
	<!--/ вы смотрели -->

    <div class="breadcrumbs-bottom">
        <?= !empty($breadcrumbs) ? $helper->renderWithMustache('product/blocks/breadcrumbs.mustache', ['breadcrumbs' => $breadcrumbs]) : '' ?>
    </div>

    <!-- seo информация -->
    <div class="bottom-content">
        <?= $page->tryRender('product/_tag', ['product' => $product, 'newVersion' => true]) ?>
        <?= $page->tryRender('product/_similarProducts', ['products' => $similarProducts, 'newVersion' => true]) ?>
        <?php /*<p class="bottom-content__p bottom-content__text">

        </p>*/ ?>
    </div>
    <!--/ seo информация -->

</div>
<!--/ карточка товара -->

<!-- просмотр большого изображения товара -->
<div class="popup popup-photo" style="display: none;">
    <div class="popup__close">×</div>

    <div class="product-card-photo">
        <img src="http://c.imgenter.ru/uploads/media/7b/a4/d5/thumb_be1c_product_1500.jpeg" class="product-card-photo__img fixed" style="max-height: 100%; max-width: 100%; top: 0px; left: 0px;">
    </div>

    <div class="product-card-photo__ctrl product-card-photo__ctrl--prev"><span class="symb"></span></div>
    <div class="product-card-photo__ctrl product-card-photo__ctrl--next"><span class="symb"></span></div>

    <div class="product-card-photo-zoom">
        <div class="product-card-photo-zoom__ctrl product-card-photo-zoom__ctrl--out disabled">–</div>
        <div class="product-card-photo-zoom__ctrl product-card-photo-zoom__ctrl--in">+</div>
    </div>

    <div class="product-card-photo-thumbs">
        <ul class="product-card-photo-thumbs-list">
            <li class="product-card-photo-thumbs__i active"><img src="http://3.imgenter.ru/uploads/media/7b/a4/d5/thumb_be1c_product_120.jpeg" class="product-card-photo-thumbs__img"></li>
            <li class="product-card-photo-thumbs__i"><img src="http://7.imgenter.ru/uploads/media/f5/5a/b1/thumb_b437_product_120.jpeg" class="product-card-photo-thumbs__img"></li>
            <li class="product-card-photo-thumbs__i"><img src="http://0.imgenter.ru/uploads/media/4a/1a/33/thumb_59d6_product_120.jpeg" class="product-card-photo-thumbs__img"></li>
            <li class="product-card-photo-thumbs__i"><img src="http://b.imgenter.ru/uploads/media/8a/98/27/thumb_4f65_product_120.jpeg" class="product-card-photo-thumbs__img"></li>
            <li class="product-card-photo-thumbs__i"><img src="http://4.imgenter.ru/uploads/media/04/7e/39/thumb_c035_product_120.jpeg" class="product-card-photo-thumbs__img"></li>
            <li class="product-card-photo-thumbs__i"><img src="http://4.imgenter.ru/uploads/media/a5/cb/16/thumb_77f4_product_120.jpeg" class="product-card-photo-thumbs__img"></li>
            <li class="product-card-photo-thumbs__i"><img src="http://9.imgenter.ru/uploads/media/af/0e/83/thumb_1512_product_120.jpeg" class="product-card-photo-thumbs__img"></li>
            <li class="product-card-photo-thumbs__i"><img src="http://b.imgenter.ru/uploads/media/c8/f7/e4/thumb_c0e4_product_120.jpeg" class="product-card-photo-thumbs__img"></li>
        </ul>

        <div class="product-card-photo-thumbs__btn product-card-photo-thumbs__btn--l product-card-photo-thumbs__btn--disabled"></div>
        <div class="product-card-photo-thumbs__btn product-card-photo-thumbs__btn--r"></div>
    </div>

    <div class="popup-photo__price">121 649&thinsp;<span class="rubl">C</span></div>
    <a class="btn-primary btn-primary_normal" href="">Купить</a>
</div>
<!--/ просмотр большого изображения товара -->

<!-- просмотр видео и 3D -->
<div class="popup popup_710 popup_nopadding" style="display: none;">
    <div class="popup__close">×</div>
</div>
<!--/ просмотр видео и 3D -->