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
?>

<div id="product-info"
     data-ui="<?= $product->getUi() ?>"
    ></div>

<section class="product-card js-module-require" data-module="enter.product.viewed">
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
	<div class="product-section section-border">
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
    <?= $helper->render('product/blocks/trustfactors.partner', ['trustfactors' => $trustfactors]) ?>

    <div style="height: 50px">
        <!-- навигация по странице -->
        <div id="jsScrollSpy" class="product-tabs-scroll jsProductTabs">
            <ul class="nav product-tabs">
                <? if ($product->getKit()) : ?><li class="product-tabs__i"><a class="product-tabs__lk jsScrollSpyKitLink" href="#kit" title="">Состав</a></li><? endif ?>
                <? if ($showDescription) : ?><li class="product-tabs__i"><a class="product-tabs__lk jsScrollSpyMoreLink" href="#more" title="">Подробности</a></li><? endif ?>
                <? if ($showAccessories) : ?><li class="product-tabs__i"><a class="product-tabs__lk jsScrollSpyAccessorizeLink" href="#accessorize" title="">Аксессуары</a></li><? endif ?>
                <li class="product-tabs__i"><a class="product-tabs__lk jsScrollSpyReviewsLink" href="#reviews" title="">Отзывы</a></li>
                <? if ($product->isAvailable()) : ?><li class="product-tabs__i jsSimilarTab" style="display: none"><a class="product-tabs__lk jsScrollSpySimilarLink" href="#similar" title="">Похожие товары</a></li><? endif ?>
            </ul>
        </div>
	    <!--/ навигация по странице -->
    </div>

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
	<div class="product-section product-section--inn" style="margin-top: 40px;">
        <? if (\App::config()->product['pullRecommendation'] && \App::config()->product['viewedEnabled']): ?>
            <?/*= $helper->render('product/__slider', [
                'type'      => 'viewed',
                'title'     => 'Вы смотрели',
                'products'  => [],
                'count'     => null,
                'limit'     => \App::config()->product['itemsInSlider'],
                'page'      => 1,
                'url'       => $page->url('product.recommended', ['productId' => $product->getId()]),
                'sender'    => [
                    'name'     => 'enter',
                    'from'     => 'productPage',
                    'position' => $isProductAvailable ? 'Viewed' : 'ProductMissing',
                ],
                'sender2' => $buySender2,
            ]) */?>
        <? endif ?>
	</div>
	<!--/ вы смотрели -->

    <?= !empty($breadcrumbs) ? $helper->renderWithMustache('product/blocks/breadcrumbs.mustache', ['breadcrumbs' => $breadcrumbs]) : '' ?>

    <!-- seo информация -->
    <div class="bottom-content">
        <?= $page->tryRender('product/_tag', ['product' => $product, 'newVersion' => true]) ?>
        <?= $page->tryRender('product/_similarProducts', ['products' => $similarProducts, 'newVersion' => true]) ?>
        <?php /*<p class="bottom-content__p bottom-content__text">

        </p>*/ ?>
    </div>
    <!--/ seo информация -->

</section>
<!--/ карточка товара -->

<div class="popup popup_set js-productkit-popup" style="display: block;">
    <div class="popup__close js-popup-close">×</div>

    <div class="popup__title">Набор мебели для гостиной «Ксено»</div>

    <div class="set-section-main-img"><img src="http://8.imgenter.ru/uploads/media/07/e3/fa/thumb_9c7d_product_500.jpeg"></div>

    <div class="set-section__r">
        <div class="set-list__tl clearfix">
            Уточните комплектацию
        </div>

        <div class="set-section__inn">
            <ul class="set-list">
                <li class="set-list__item">
                    <a class="set-list__left set-list__img" href=""><img src="http://5.imgenter.ru/uploads/media/61/9a/94/thumb_c95d_product_120.jpeg"></a>

                    <div class="set-list__right">
                        <div class="set-list__name"><a class="" href="">Навесная полка «Ксено СТЛ.078.05»</a></div>

                        <div class="set-list__desc table">

                            <div class="table-cell">
                                <div class="set-list__nominal"><span>990</span>&nbsp;<span class="rubl">p</span></div>

                                <div class="set-list__dimention">
                                    <span class="set-list__dimention-name">Высота</span>
                                    <span class="set-list__dimention-val">20</span>
                                </div>

                                <div class="set-list__dimention">
                                    <span class="set-list__dimention-name">&nbsp;</span>
                                    <span class="set-list__dimention-val set-list__dimention-val_separation">x</span>
                                </div>

                                <div class="set-list__dimention">
                                    <span class="set-list__dimention-name">Ширина</span>
                                    <span class="set-list__dimention-val">104</span>
                                </div>

                                <div class="set-list__dimention">
                                    <span class="set-list__dimention-name">&nbsp;</span>
                                    <span class="set-list__dimention-val set-list__dimention-val_separation">x</span>
                                </div>

                                <div class="set-list__dimention">
                                    <span class="set-list__dimention-name">Глубина</span>
                                    <span class="set-list__dimention-val">25</span>
                                </div>

                                <div class="set-list__dimention">
                                    <span class="set-list__dimention-name">&nbsp;</span>
                                    <span class="set-list__dimention-val">см</span>
                                </div>
                            </div>

                            <div class="set-list__counter table-cell">
                                <div class="counter counter_mini js-counter">
                                    <button class="counter__btn counter__btn_minus disabled js-counter-minus"></button>
                                    <input type="text" class="counter__it js-counter-value" value="1">
                                    <button class="counter__btn counter__btn_plus js-counter-plus"></button>
                                    <span class="counter__num">шт.</span>
                                </div>
                            </div>

                            <div class="set-list__price table-cell">
                                <span>990</span>&nbsp;<span class="rubl">p</span>
                            </div>
                        </div>
                    </div>
                </li>

                <li class="set-list__item">
                    <a class="set-list__left set-list__img" href=""><img src="http://5.imgenter.ru/uploads/media/61/9a/94/thumb_c95d_product_120.jpeg"></a>

                    <div class="set-list__right">
                        <div class="set-list__name"><a class="" href="">Навесная полка «Ксено СТЛ.078.05»</a></div>

                        <div class="set-list__desc table">

                            <div class="table-cell">
                                <div class="set-list__nominal"><span>990</span>&nbsp;<span class="rubl">p</span></div>

                                <div class="set-list__dimention">
                                    <span class="set-list__dimention-name">Высота</span>
                                    <span class="set-list__dimention-val">20</span>
                                </div>

                                <div class="set-list__dimention">
                                    <span class="set-list__dimention-name">&nbsp;</span>
                                    <span class="set-list__dimention-val set-list__dimention-val_separation">x</span>
                                </div>

                                <div class="set-list__dimention">
                                    <span class="set-list__dimention-name">Ширина</span>
                                    <span class="set-list__dimention-val">104</span>
                                </div>

                                <div class="set-list__dimention">
                                    <span class="set-list__dimention-name">&nbsp;</span>
                                    <span class="set-list__dimention-val set-list__dimention-val_separation">x</span>
                                </div>

                                <div class="set-list__dimention">
                                    <span class="set-list__dimention-name">Глубина</span>
                                    <span class="set-list__dimention-val">25</span>
                                </div>

                                <div class="set-list__dimention">
                                    <span class="set-list__dimention-name">&nbsp;</span>
                                    <span class="set-list__dimention-val">см</span>
                                </div>
                            </div>

                            <div class="set-list__counter table-cell">
                                <div class="counter counter_mini js-counter">
                                    <button class="counter__btn counter__btn_minus disabled js-counter-minus"></button>
                                    <input type="text" class="counter__it js-counter-value" value="1">
                                    <button class="counter__btn counter__btn_plus js-counter-plus"></button>
                                    <span class="counter__num">шт.</span>
                                </div>
                            </div>

                            <div class="set-list__price table-cell">
                                <span>990</span>&nbsp;<span class="rubl">p</span>
                            </div>
                        </div>
                    </div>
                </li>

                <li class="set-list__item">
                    <a class="set-list__left set-list__img" href=""><img src="http://5.imgenter.ru/uploads/media/61/9a/94/thumb_c95d_product_120.jpeg"></a>

                    <div class="set-list__right">
                        <div class="set-list__name"><a class="" href="">Навесная полка «Ксено СТЛ.078.05»</a></div>

                        <div class="set-list__desc table">

                            <div class="table-cell">
                                <div class="set-list__nominal"><span>990</span>&nbsp;<span class="rubl">p</span></div>

                                <div class="set-list__dimention">
                                    <span class="set-list__dimention-name">Высота</span>
                                    <span class="set-list__dimention-val">20</span>
                                </div>

                                <div class="set-list__dimention">
                                    <span class="set-list__dimention-name">&nbsp;</span>
                                    <span class="set-list__dimention-val set-list__dimention-val_separation">x</span>
                                </div>

                                <div class="set-list__dimention">
                                    <span class="set-list__dimention-name">Ширина</span>
                                    <span class="set-list__dimention-val">104</span>
                                </div>

                                <div class="set-list__dimention">
                                    <span class="set-list__dimention-name">&nbsp;</span>
                                    <span class="set-list__dimention-val set-list__dimention-val_separation">x</span>
                                </div>

                                <div class="set-list__dimention">
                                    <span class="set-list__dimention-name">Глубина</span>
                                    <span class="set-list__dimention-val">25</span>
                                </div>

                                <div class="set-list__dimention">
                                    <span class="set-list__dimention-name">&nbsp;</span>
                                    <span class="set-list__dimention-val">см</span>
                                </div>
                            </div>

                            <div class="set-list__counter table-cell">
                                <div class="counter counter_mini js-counter">
                                    <button class="counter__btn counter__btn_minus disabled js-counter-minus"></button>
                                    <input type="text" class="counter__it js-counter-value" value="1">
                                    <button class="counter__btn counter__btn_plus js-counter-plus"></button>
                                    <span class="counter__num">шт.</span>
                                </div>
                            </div>

                            <div class="set-list__price table-cell">
                                <span>990</span>&nbsp;<span class="rubl">p</span>
                            </div>
                        </div>
                    </div>
                </li>

                <li class="set-list__item">
                    <a class="set-list__left set-list__img" href=""><img src="http://5.imgenter.ru/uploads/media/61/9a/94/thumb_c95d_product_120.jpeg"></a>

                    <div class="set-list__right">
                        <div class="set-list__name"><a class="" href="">Навесная полка «Ксено СТЛ.078.05»</a></div>

                        <div class="set-list__desc table">

                            <div class="table-cell">
                                <div class="set-list__nominal"><span>990</span>&nbsp;<span class="rubl">p</span></div>

                                <div class="set-list__dimention">
                                    <span class="set-list__dimention-name">Высота</span>
                                    <span class="set-list__dimention-val">20</span>
                                </div>

                                <div class="set-list__dimention">
                                    <span class="set-list__dimention-name">&nbsp;</span>
                                    <span class="set-list__dimention-val set-list__dimention-val_separation">x</span>
                                </div>

                                <div class="set-list__dimention">
                                    <span class="set-list__dimention-name">Ширина</span>
                                    <span class="set-list__dimention-val">104</span>
                                </div>

                                <div class="set-list__dimention">
                                    <span class="set-list__dimention-name">&nbsp;</span>
                                    <span class="set-list__dimention-val set-list__dimention-val_separation">x</span>
                                </div>

                                <div class="set-list__dimention">
                                    <span class="set-list__dimention-name">Глубина</span>
                                    <span class="set-list__dimention-val">25</span>
                                </div>

                                <div class="set-list__dimention">
                                    <span class="set-list__dimention-name">&nbsp;</span>
                                    <span class="set-list__dimention-val">см</span>
                                </div>
                            </div>

                            <div class="set-list__counter table-cell">
                                <div class="counter counter_mini js-counter">
                                    <button class="counter__btn counter__btn_minus disabled js-counter-minus"></button>
                                    <input type="text" class="counter__it js-counter-value" value="1">
                                    <button class="counter__btn counter__btn_plus js-counter-plus"></button>
                                    <span class="counter__num">шт.</span>
                                </div>
                            </div>

                            <div class="set-list__price table-cell">
                                <span>990</span>&nbsp;<span class="rubl">p</span>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
        </div>

        <div class="set-section__footer">
            <div class="set-section__default packageSetDefault">
                <input type="checkbox" id="defaultSet" class="custom-input custom-input_check">
                <label for="defaultSet" class="custom-label">Базовый комплект</label>
            </div>

            <div class="set-section__price">Итого за <span>5</span> предметов: <strong data-bind="html: totalPrice">17&thinsp;900</strong> <span class="rubl">p</span></div>

            <div class="set-section__buy">
                <a class="btn-primary btn-primary_bigger" href="">Купить</a>
            </div>
        </div>
    </div>
</div>