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
    <?= $page->blockViewed() ?>
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

<!-- попап - узнатьо снижении цены -->
<div class="popup-best-price popup popup_360">
    <div class="popup__close js-popup-close">×</div>

    <div class="popup__title">Узнать о снижении цены</div>

    <div class="popup-best-price__desc">Вы получите письмо, когда цена станет ниже 12 333 <span class="rubl">p</span></div>

    <div class="form__field">
        <input type="text" class="form__it it js-lowPriceNotifier-popup-email" name="" value="">
        <label class="form__placeholder placeholder js-auth-username-label">Ваш email</label>
    </div>

    <input type="checkbox" name="subscribe" id="subscribe" value="1" autocomplete="off" class="custom-input custom-input_check js-lowPriceNotifier-popup-subscribe" checked="">
    <label class="popup-best-price__check custom-label" for="subscribe">Подписаться на рассылку и получить купон со скидкой 300 рублей на следующую покупку</label>

    <div class="popup-best-price__btn">
        <a href="#" class="btn-primary btn-primary_bigger js-lowPriceNotifier-popup-submit">Сохранить</a>
    </div>
</div>
<!--/ попап - узнатьо снижении цены -->


<div class="delivery-points popup">
    <div class="popup__close js-popup-close">×</div>
    <div class="popup__title">Выберите точку самовывоза</div>

    <!-- Новая верстка -->
    <div class="delivery-points__left">
        <div class="point-search">
            <i class="point-search__icon i-controls i-controls--search"></i>
            <input class="point-search__it it" type="text" placeholder="Искать по улице, метро">
            <div class="point-search" style="display: none;">×</div>

            <div class="pick-point-suggest" style="display: none">
                <ul class="pick-point-suggest__list"></ul>
            </div>
        </div>

        <div class="drop-filter-kit drop-filter-kit-box">
            <!-- Точки самовывоза - для поселекченного фильтра добавляем класс active-->
            <div class="drop-filter-box open">
                <div class="drop-filter-box__tggl">
                    <span class="drop-filter-box__tggl-tx">Все точки</span>
                </div>

                <div class="drop-filter-box__dd">
                    <div class="drop-filter-box__dd-inn">
                        <div class="drop-filter-box__dd-line">
                            <input class="custom-input custom-input_check-fill" type="checkbox" id="shops" name="" value="">
                            <label class="custom-label" for="shops">Магазины Enter</label>
                            <!-- попап-подсказка с описание пункта самовывоза -->
                            <div class="delivery-points-info delivery-points-info_inline">
                                <a class="delivery-points-info__icon"></a>
                                <div class="delivery-points-info__popup delivery-points-info__popup_top info-popup">
                                    <a class="delivery-points-info__link" href="" title="Как пользоваться постаматом">Как пользоваться постаматом</a>
                                </div>
                            </div>
                            <!--/ попап-подсказка с описание пункта самовывоза -->
                        </div>

                        <div class="drop-filter-box__dd-line">
                            <input class="custom-input custom-input_check-fill" type="checkbox" id="pick" name="" value="">
                            <label class="custom-label" for="pick">Постаматы PickPoint</label>
                            <!-- попап-подсказка с описание пункта самовывоза -->
                            <div class="delivery-points-info delivery-points-info_inline">
                                <a class="delivery-points-info__icon"></a>
                                <div class="delivery-points-info__popup delivery-points-info__popup_top info-popup">
                                    <a class="delivery-points-info__link" href="" title="Как пользоваться постаматом">Как пользоваться постаматом</a>
                                </div>
                            </div>
                            <!--/ попап-подсказка с описание пункта самовывоза -->
                        </div>

                        <div class="drop-filter-box__dd-line">
                            <input class="custom-input custom-input_check-fill" type="checkbox" id="shops1" name="" value="">
                            <label class="custom-label" for="shops1">Магазины Enter</label>
                        </div>
                    </div>
                </div>
            </div>
            <!--/ Точки самовывоза -->

            <!-- Cтоимость -->
            <div class="drop-filter-box">
                <div class="drop-filter-box__tggl">
                    <span class="drop-filter-box__tggl-tx">Стоимость</span>
                </div>

                <div class="drop-filter-box__dd">
                    <div class="drop-filter-box__dd-inn">
                        <div class="fltrBtn_param">
                            <div class="fltrBtn_ln ">
                                <input class="custom-input custom-input_check" type="checkbox" id="" name="" value="0" data-bind="checked: $root.choosenCosts">
                                <label class="custom-label" for="">
                                    <span class="customLabel_btx">Бесплатно</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Cтоимость -->

            <!-- Дата самовывоза -->
            <div class="drop-filter-box">
                <div class="drop-filter-box__tggl">
                    <span class="drop-filter-box__tggl-tx">Дата</span>
                </div>

                <div class="drop-filter-box__dd">
                    <div class="drop-filter-box__dd-inn">
                        <div class="fltrBtn_param">
                            <div class="fltrBtn_ln ">
                                <input class="custom-input custom-input_check" type="checkbox" id="" name="" value="2015-08-11">
                                <label class="custom-label" for="">
                                    <span class="customLabel_btx">11.08.2015</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--/ Дата самовывоза -->
        </div>

        <span class="delivery-points-nomatch" style="display: none;">Поиск не дал результатов</span>

        <div class="delivery-points-lwrap">
            <div class="delivery-points-lwrap__inn">
                <div class="delivery-points-list table">
                    <div class="delivery-points-list__row table-row">
                        <div class="delivery-points-list__logo table-cell">
                            <img src="/images/deliv-logo/enter.png" class="delivery-points-list__img">

                            <!-- попап-подсказка с описание пункта самовывоза -->
                            <div class="delivery-points-info delivery-points-info_absolute">
                                <a class="delivery-points-info__icon"></a>
                                <div class="delivery-points-info__popup delivery-points-info__popup_left info-popup">
                                    <a class="delivery-points-info__link" href="" title="Как пользоваться постаматом">Как пользоваться постаматом</a>
                                </div>
                            </div>
                            <!--/ попап-подсказка с описание пункта самовывоза -->

                            <span class="delivery-points-list__name">Магазин Enter</span>
                        </div>

                        <div class="delivery-points-list__address table-cell">
                            <div class="delivery-points-list__address-name">г. Электросталь, пр-кт Ленина, д. 30/13</div>
                            <div class="delivery-points-list__address-time">с 10.00 до 21.00</div>
                        </div>

                        <div class="delivery-points-list__info table-cell">
                            <div class="delivery-points-list__info-hidden">
                                <div class="delivery-points-list__info-date">11.08.2015</div>
                                <div class="delivery-points-list__info-price"><span>Бесплатно</span></div>
                            </div>

                            <div class="delivery-points-list__info-btn">
                                <a href="" class="btn-primary btn-primary_middle">Купить</a>
                            </div>
                        </div>
                    </div>

                    <div class="delivery-points-list__row table-row">
                        <div class="delivery-points-list__logo table-cell">
                            <img src="/images/deliv-logo/enter.png" class="delivery-points-list__img">
                            <span class="delivery-points-list__name">Магазин Enter</span>
                        </div>

                        <div class="delivery-points-list__address table-cell">
                            <div class="delivery-points-list__address-name">г. Электросталь, пр-кт Ленина, д. 30/13</div>
                            <div class="delivery-points-list__address-time">с 10.00 до 21.00</div>
                        </div>

                        <div class="delivery-points-list__info table-cell">
                            <div class="delivery-points-list__info-hidden">
                                <div class="delivery-points-list__info-date">11.08.2015</div>
                                <div class="delivery-points-list__info-price"><span>Бесплатно</span></div>
                            </div>

                            <div class="delivery-points-list__info-btn">
                                <a href="" class="btn-primary btn-primary_middle">Купить</a>
                            </div>
                        </div>
                    </div>

                    <!-- точка доставки в которой товар есть на витрине - добавляем класс no-hidden -->
                    <div class="delivery-points-list__row no-hidden table-row">
                        <div class="delivery-points-list__logo table-cell">
                            <img src="/images/deliv-logo/enter.png" class="delivery-points-list__img">
                            <span class="delivery-points-list__name">Магазин Enter</span>
                        </div>

                        <div class="delivery-points-list__address table-cell">
                            <div class="delivery-points-list__address-name">г. Электросталь, пр-кт Ленина, д. 30/13</div>
                            <div class="delivery-points-list__address-time">с 10.00 до 21.00</div>
                        </div>

                        <div class="delivery-points-list__info table-cell">
                            <span class="delivery-points-list__info-price">На витрине</span>
                            <!-- попап-подсказка с описание пункта самовывоза -->
                            <div class="delivery-points-info delivery-points-info_inline">
                                <i class="i-product i-product--info-normal i-info__icon"></i>

                                <div class="delivery-points-info__popup delivery-points-info__popup_right info-popup">
                                    Чтобы купить товар с витрины,<br>нужно приехать в магазин и обратиться к продавцу.
                                </div>
                            </div>
                            <!--/ попап-подсказка с описание пункта самовывоза -->
                        </div>
                    </div>

                    <div class="delivery-points-list__row table-row">
                        <div class="delivery-points-list__logo table-cell">
                            <img src="/images/deliv-logo/enter.png" class="delivery-points-list__img">
                            <span class="delivery-points-list__name">Магазин Enter</span>
                        </div>

                        <div class="delivery-points-list__address table-cell">
                            <div class="delivery-points-list__address-name">г. Электросталь, пр-кт Ленина, д. 30/13</div>
                            <div class="delivery-points-list__address-time">с 10.00 до 21.00</div>
                        </div>

                        <div class="delivery-points-list__info table-cell">
                            <div class="delivery-points-list__info-hidden">
                                <div class="delivery-points-list__info-date">11.08.2015</div>
                                <div class="delivery-points-list__info-price"><span>Бесплатно</span></div>
                            </div>

                            <div class="delivery-points-list__info-btn">
                                <a href="" class="btn-primary btn-primary_middle">Купить</a>
                            </div>
                        </div>
                    </div>

                    <div class="delivery-points-list__row table-row">
                        <div class="delivery-points-list__logo table-cell">
                            <img src="/images/deliv-logo/enter.png" class="delivery-points-list__img">
                            <span class="delivery-points-list__name">Магазин Enter</span>
                        </div>

                        <div class="delivery-points-list__address table-cell">
                            <div class="delivery-points-list__address-metro" style="background-color: red">
                               <div class="delivery-points-list__address-metro__inn">Ленинский проспект</div>
                            </div>

                            <div class="delivery-points-list__address-name">г. Электросталь, пр-кт Ленина, д. 30/13</div>
                            <div class="delivery-points-list__address-time">с 10.00 до 21.00</div>
                        </div>

                        <div class="delivery-points-list__info table-cell">
                            <div class="delivery-points-list__info-hidden">
                                <div class="delivery-points-list__info-date">11.08.2015</div>
                                <div class="delivery-points-list__info-price"><span>Бесплатно</span></div>
                            </div>

                            <div class="delivery-points-list__info-btn">
                                <a href="" class="btn-primary btn-primary_middle">Купить</a>
                            </div>
                        </div>
                    </div>

                    <div class="delivery-points-list__row table-row">
                        <div class="delivery-points-list__logo table-cell">
                            <img src="/images/deliv-logo/enter.png" class="delivery-points-list__img">
                            <span class="delivery-points-list__name">Магазин Enter</span>
                        </div>

                        <div class="delivery-points-list__address table-cell">
                            <div class="delivery-points-list__address-name">г. Электросталь, пр-кт Ленина, д. 30/13</div>
                            <div class="delivery-points-list__address-time">с 10.00 до 21.00</div>
                        </div>

                        <div class="delivery-points-list__info table-cell">
                            <div class="delivery-points-list__info-hidden">
                                <div class="delivery-points-list__info-date">11.08.2015</div>
                                <div class="delivery-points-list__info-price"><span>Бесплатно</span></div>
                            </div>

                            <div class="delivery-points-list__info-btn">
                                <a href="" class="btn-primary btn-primary_middle">Купить</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="delivery-points__right"></div>
</div>