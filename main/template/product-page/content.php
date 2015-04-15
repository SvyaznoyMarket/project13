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
 * @var $line                   \Model\Line\Entity
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
$buySender2 = \Session\ProductPageSendersForMarketplace::get($product->getUi());

?>

<?= !empty($breadcrumbs) ? $helper->renderWithMustache('product-page/blocks/breadcrumbs.mustache', ['breadcrumbs' => $breadcrumbs]) : '' ?>

<section>

	<h1 class="product-name"><?= $product->getName() ?></h1>

	<? if ($product->isOnlyFromPartner() && $product->getPartnerName()) : ?>
        <!-- Информация о партнере -->
        <div class="vandor-offer">
            <a href="" class="vandor-offer__lk i-info">
                <span class="i-info__tx">Продавец: <?= $product->getPartnerName() ?></span> <i class="i-info__icon i-product i-product--info-normal"></i>
            </a>
        </div>
        <!-- /Информация о партнере -->
    <? endif ?>

	<!-- карточка товара -->
	<div class="product-card clearfix">

        <!-- блок с фото -->
        <?= $helper->render('product-page/blocks/photo', ['product' => $product, 'videoHtml' => $videoHtml, 'properties3D' => $properties3D ]) ?>
        <!--/ блок с фото -->

		<!-- краткое описание товара -->
		<div class="product-card__c">

            <?= $helper->render('product-page/blocks/reviews.short', ['reviewsData' => $reviewsData]) ?>

			<?= $helper->render('product-page/blocks/variants', ['product' => $product]) ?>

			<p class="product-card-desc"><?= $product->getAnnounce() ?></p>

            <dl class="product-card-prop">
                <? foreach ($product->getMainProperties() as $property) : ?>
				<dt class="product-card-prop__i product-card-prop__i--name"><?= $property->getName() ?></dt>
				<dd class="product-card-prop__i product-card-prop__i--val"><?= $property->getStringValue() ?></dd>
                <? endforeach ?>
			</dl>

			<ul class="product-card-assure">
				<li class="product-card-assure__i">
					<div class="product-card-assure__l">
						<img class="product-card-assure__img" src="/styles/product/img/pandora.png">
					</div>

					<span class="product-card-assure__r">Гарантия подлинности и качества</span>
				</li>

				<li class="product-card-assure__i">
					<div class="product-card-assure__l">
						<img class="product-card-assure__img" src="/styles/product/img/jewelery-guar.png">
					</div>

					<span class="product-card-assure__r">Обмен в течение 30 дней<br/>Возврат по гарантии в течение 1 года</span>
				</li>
			</ul>

			<ul class="product-card-sharing-list">
				<li class="product-card-sharing-list__i">
					<i class="product-card-sharing-list__icon i-sharing-icon i-sharing-icon--fb"></i>
				</li>
				<li class="product-card-sharing-list__i">
					<i class="product-card-sharing-list__icon i-sharing-icon i-sharing-icon--vk"></i>
				</li>
				<li class="product-card-sharing-list__i">
					<i class="product-card-sharing-list__icon i-sharing-icon i-sharing-icon--tw"></i>
				</li>
				<li class="product-card-sharing-list__i">
					<i class="product-card-sharing-list__icon i-sharing-icon i-sharing-icon--pi"></i>
				</li>
				<li class="product-card-sharing-list__i">
					<i class="product-card-sharing-list__icon i-sharing-icon i-sharing-icon--gp"></i>
				</li>
			</ul>
			<ul class="pay-system-list">
                <li class="pay-system-list__i"><i class="pay-system-list__icon i-paysystem-icon i-paysystem-icon--visa"></i></li>
                <li class="pay-system-list__i"><i class="pay-system-list__icon i-paysystem-icon i-paysystem-icon--mastercard"></i></li>
                <li class="pay-system-list__i"><i class="pay-system-list__icon i-paysystem-icon i-paysystem-icon--psb"></i></li>
            </ul>
		</div>
		<!--/ краткое описание товара -->

		<!-- купить -->
		<div class="product-card__r">
			<div class="product-card-action i-info" style="display: none">
				<span class="product-card-action__tx i-info__tx">Акция действует<br>ещё 1 день 22:11:07</span>
				<i class="product-card-action__icon i-product i-product--info-warn i-info__icon"></i>

				<!-- попап - подробности акции, чтобы показать/скрыть окно необходимо добавить/удалить класс info-popup--open -->
				<div class="info-popup info-popup--action info-popup--open" style="display: none">
					<i class="closer">×</i>
					<div class="info-popup__inn">
						<a href="" title=""><img src="/styles/product/img/trust-sale.png" alt=""></a>
					</div>
				</div>
				<!--/ попап - подробности акции -->

				<!-- попап - подробности акции, чтобы показать/скрыть окно необходимо добавить/удалить класс info-popup--open -->
				<div class="action-hint info-popup info-popup--action info-popup--open" style="display: none">
					<i class="closer">×</i>
					<div class="info-popup__inn">
						<div class="action-hint__desc">
							<img class="action-hint__img" src="/styles/product/img/shild-124x38.png">

							<a class="action-hint__lk">Черная пятница в Enter</a>
						</div>
					</div>
				</div>
				<!--/ попап - подробности акции -->
			</div>

            <? if ($product->getPriceOld()) : ?>
            <div class="product-card-old-price">
				<span class="product-card-old-price__inn"><?= $helper->formatPrice($product->getPriceOld()) ?></span> <span class="rubl">p</span>
			</div>
            <? endif ?>

			<!-- цена товара -->
			<div class="product-card-price i-info">
				<span class="product-card-price__val i-info__tx"><?= $helper->formatPrice($product->getPrice()) ?><span class="rubl">p</span></span>
				<i class="i-product i-product--info-normal i-info__icon"></i>

				<!-- попап - узнатьо снижении цены, чтобы показать/скрыть окно необходимо добавить/удалить класс info-popup--open -->
				<div class="best-price-popup info-popup info-popup--best-price info-popup--open" style="display: none">
					<i class="closer">×</i>

					<strong class="best-price-popup__tl">Узнать о снижении цены</strong>

					<p class="best-price-popup__desc">Вы получите письмо,<br>когда цена станет ниже 9 000 <span class="rubl">p</span></p>

					<input class="best-price-popup__it textfield" placeholder="Ваш email" value="">

					<input type="checkbox" name="subscribe" id="subscribe" value="1" autocomplete="off" class="customInput customInput-defcheck jsCustomRadio js-customInput jsSubscribe" checked="">
					<label class="best-price-popup__check customLabel customLabel-defcheck mChecked" for="subscribe">Подписаться на рассылку и получить купон со скидкой 300 рублей на следующую покупку</label>

					<div style="text-align: center">
						<a href="#" class="best-price-popup__btn btn-type btn-type--buy">Сохранить</a>
					</div>
				</div>
				<!--/ попап - узнатьо снижении цены -->
			</div>
			<!--/ цена товара -->

			<!-- применить скидку -->
			<div class="product-card-discount-switch" style="display: none">
				<div class="product-card-discount-switch__i discount-switch">
					<input class="discount-switch__it" type="checkbox" name="" id="discount-switch">
					<label class="discount-switch__lbl" for="discount-switch"></label>
				</div>

				<span class="product-card-discount-switch__i product-card-discount-switch__i--tx">Скидка 10%</span>
				<img class="product-card-discount-switch__i product-card-discount-switch__img" src="/styles/product/img/i-fishka.png">
			</div>
			<!--/ применить скидку -->

            <?= $helper->render('cart/__button-product', [
                'product'  => $product,
                'onClick'  => isset($addToCartJS) ? $addToCartJS : null,
                'sender'   => $buySender + [
                        'from' => preg_filter('/\?+?.*$/', '', $request->server->get('HTTP_REFERER')) == null ? $request->server->get('HTTP_REFERER') : preg_filter('/\?+?.*$/', '', $request->server->get('HTTP_REFERER')) // удаляем из REFERER параметры
                    ],
                'sender2' => $buySender2,
                'location' => 'product-card',
            ]) // Кнопка купить ?>

			<? if ($product->getPrice() >= \App::config()->product['minCreditPrice']) : ?>
                <!-- купить в кредит -->
                <a class="buy-on-credit btn-type btn-type--normal btn-type--longer jsProductCreditButton" href="" style="display: none"
                   data-credit='<?= $creditData['creditData'] ?>'>
                    <span class="buy-on-credit__tl">Купить в кредит</span>
                    <span class="buy-on-credit__tx">от <mark class="buy-on-credit__mark jsProductCreditPrice">0</mark>&nbsp;&nbsp;<span class="rubl">p</span> в месяц</span>
                </a>
                <!--/ купить в кредит -->
            <? endif ?>

            <div class="js-showTopBar"></div>

			<!-- сравнить, добавить в виш лист -->
			<ul class="product-card-tools">
				<li class="product-card-tools__i product-card-tools__i--onclick">

                    <? if (!$hasFurnitureConstructor && !count($product->getPartnersOffer()) && (!$isKit || $product->getIsKitLocked())): ?>
                        <?= $helper->render('cart/__button-product-oneClick', [
                            'product' => $product,
                            'sender'  => $buySender,
                            'sender2' => $buySender2,
                            'value' => 'Купить в 1 клик'
                        ]) // Покупка в один клик ?>
                    <? endif ?>


				</li>

                <!-- TODO функционал удаления из сравнения -->
                <li class="product-card-tools__i product-card-tools__i--compare"
                    data-bind="compareButtonBinding: compare"
                    data-id="<?= $product->getId() ?>"
                    data-type-id="<?= $product->getType() ? $product->getType()->getId() : null ?>">
                    <a id="<?= 'compareButton-' . $product->getId() ?>"
                       href="<?= \App::router()->generate('compare.add', ['productId' => $product->getId(), 'location' => 'product']) ?>"
                       class="product-card-tools__lk jsCompareLink"
                       data-is-slot="<?= (bool)$product->getSlotPartnerOffer() ?>"
                       data-is-only-from-partner="<?= $product->isOnlyFromPartner() ?>"
                        >
                        <i class="product-card-tools__icon i-tools-icon i-tools-icon--product-compare"></i>
                        <span class="product-card-tools__tx">Сравнить</span>
                    </a>
                </li>

                <? if (false) : // Функционал избранного оставляем на потом ?>
                    <li class="product-card-tools__i product-card-tools__i--wish">
                        <a href="" class="product-card-tools__lk">
                            <i class="product-card-tools__icon i-tools-icon i-tools-icon--wish"></i>
                            <span class="product-card-tools__tx">В избранное</span>
                        </a>
                    </li>
                <? endif ?>
			</ul>
			<!--/ сравнить, добавить в виш лист -->

			<?= $helper->render('product-page/blocks/delivery', ['product' => $product]) ?>

		</div>
		<!--/ купить -->
	</div>
	<!--/ карточка товара -->

	<!-- с этим товаром покупают -->
	<div class="product-section product-section--inn product-section--border-top">
<!--		<div class="product-section__h3">С этим товаром покупают</div>-->
        <? if (\App::config()->product['pullRecommendation']): ?>
            <?= $helper->render('product/__slider', [
                'type'           => 'alsoBought',
                'title'          => 'С этим товаром покупают',
                'products'       => [],
                'count'          => null,
                'limit'          => \App::config()->product['itemsInSlider'],
                'page'           => 1,
                'additionalData' => $additionalData,
                'url'            => $page->url('product.recommended', ['productId' => $product->getId()]),
                'sender'         => [
                    'name'     => 'retailrocket',
                    'position' => $isProductAvailable ? 'ProductAccessories' : 'ProductMissing', // все правильно - так и надо!
                ],
                'sender2' => $sender2,
            ]) ?>
        <? endif ?>

        <!-- слайдер товаров для слайдера с аксессуарами применяем модификатор goods-slider--5items -->
        <div class="goods-slider">
            <div class="goods-slider__inn">
                <ul class="goods-slider-list clearfix">
                    <li class="goods-slider-list__i" data-category="" data-product="">
                        <img class="slideItem_stick" src="http://fs07.enter.ru/7/1/66x23/fe/375422.png" alt="Sale">

                        <a id="" class="goods-slider-list__link" href="" target="_blank">
                            <span class="goods-slider-list__action"><img class="goods-slider-list__img" src="http://fs03.enter.ru/1/1/120/ed/247782.jpg" alt="Защитная пленка для iPhone 5C  XtremeMac IPP-TSMLC-03 матовая"></span>

                            <span class="goods-slider-list__name">Защитная пленка для iPhone 5C  XtremeMac IPP-TSMLC-03 матовая</span>
                        </a>

                        <div class="goods-slider-list__price-old"><span class="line-through">195</span> <span class="rubl">p</span></div>
                        <div class="goods-slider-list__price-now">195 <span class="rubl">p</span></div>

                        <a href="" class="btn-type btn-type--buy btn-type--light">Купить</a>
                    </li>

                    <li class="goods-slider-list__i" data-category="" data-product="">
                        <img class="slideItem_stick" src="http://fs07.enter.ru/7/1/66x23/fe/375422.png" alt="Sale">

                        <a id="" class="goods-slider-list__link" href="" target="_blank">
                            <span class="goods-slider-list__action"><img class="goods-slider-list__img" src="http://fs03.enter.ru/1/1/120/ed/247782.jpg" alt="Защитная пленка для iPhone 5C  XtremeMac IPP-TSMLC-03 матовая"></span>

                            <span class="goods-slider-list__name">Защитная пленка для iPhone 5C  XtremeMac IPP-TSMLC-03 матовая</span>
                        </a>

                        <div class="goods-slider-list__price-old"></div>
                        <div class="goods-slider-list__price-now">195 <span class="rubl">p</span></div>

                        <a href="" class="btn-type btn-type--buy btn-type--light">Купить</a>
                    </li>

                    <li class="goods-slider-list__i" data-category="" data-product="">
                        <img class="slideItem_stick" src="http://fs07.enter.ru/7/1/66x23/fe/375422.png" alt="Sale">

                        <a id="" class="goods-slider-list__link" href="" target="_blank">
                            <span class="goods-slider-list__action"><img class="goods-slider-list__img" src="http://fs03.enter.ru/1/1/120/ed/247782.jpg" alt="Защитная пленка для iPhone 5C  XtremeMac IPP-TSMLC-03 матовая"></span>

                            <span class="goods-slider-list__name">Защитная пленка для</span>
                        </a>

                        <div class="goods-slider-list__price-old"><span class="line-through">195</span> <span class="rubl">p</span></div>
                        <div class="goods-slider-list__price-now">195 <span class="rubl">p</span></div>

                        <a href="" class="btn-type btn-type--buy btn-type--light">Купить</a>
                    </li>

                    <li class="goods-slider-list__i" data-category="" data-product="">
                        <img class="slideItem_stick" src="http://fs07.enter.ru/7/1/66x23/fe/375422.png" alt="Sale">

                        <a id="" class="goods-slider-list__link" href="" target="_blank">
                            <span class="goods-slider-list__action"><img class="goods-slider-list__img" src="http://fs03.enter.ru/1/1/120/ed/247782.jpg" alt="Защитная пленка для iPhone 5C  XtremeMac IPP-TSMLC-03 матовая"></span>

                            <span class="goods-slider-list__name">Защитная пленка для iPhone 5C  XtremeMac IPP-TSMLC-03 матовая</span>
                        </a>

                        <div class="goods-slider-list__price-old"><span class="line-through">195</span> <span class="rubl">p</span></div>
                        <div class="goods-slider-list__price-now">195 <span class="rubl">p</span></div>

                        <a href="" class="btn-type btn-type--buy btn-type--light">Купить</a>
                    </li>

                    <li class="goods-slider-list__i" data-category="" data-product="">
                        <img class="slideItem_stick" src="http://fs07.enter.ru/7/1/66x23/fe/375422.png" alt="Sale">

                        <a id="" class="goods-slider-list__link" href="" target="_blank">
                            <span class="goods-slider-list__action"><img class="goods-slider-list__img" src="http://fs03.enter.ru/1/1/120/ed/247782.jpg" alt="Защитная пленка для iPhone 5C  XtremeMac IPP-TSMLC-03 матовая"></span>

                            <span class="goods-slider-list__name">Защитная пленка для iPhone 5C  XtremeMac IPP-TSMLC-03 матовая</span>
                        </a>

                        <div class="goods-slider-list__price-old"><span class="line-through">195</span> <span class="rubl">p</span></div>
                        <div class="goods-slider-list__price-now">195 <span class="rubl">p</span></div>

                        <a href="" class="btn-type btn-type--buy btn-type--light">Купить</a>
                    </li>

                    <li class="goods-slider-list__i" data-category="" data-product="">
                        <img class="slideItem_stick" src="http://fs07.enter.ru/7/1/66x23/fe/375422.png" alt="Sale">

                        <a id="" class="goods-slider-list__link" href="" target="_blank">
                            <span class="goods-slider-list__action"><img class="goods-slider-list__img" src="http://fs03.enter.ru/1/1/120/ed/247782.jpg" alt="Защитная пленка для iPhone 5C  XtremeMac IPP-TSMLC-03 матовая"></span>

                            <span class="goods-slider-list__name">Защитная пленка для iPhone 5C  XtremeMac IPP-TSMLC-03 матовая</span>
                        </a>

                        <div class="goods-slider-list__price-old"><span class="line-through">195</span> <span class="rubl">p</span></div>
                        <div class="goods-slider-list__price-now">195 <span class="rubl">p</span></div>

                        <a href="" class="btn-type btn-type--buy btn-type--light">Купить</a>
                    </li>
                </ul>
            </div>

            <div class="goods-slider__btn goods-slider__btn--prev disabled"></div>
            <div class="goods-slider__btn goods-slider__btn--next"></div>
        </div>
    </div>
    <!--/ слайдер товаров -->
    <!--/ с этим товаром покупают -->

    <!-- ссылки связной, сбер и многору -->
    <div class="product-discounts">
        <ul class="product-discounts-list">
            <li class="product-discounts-list__i"><a class="product-discounts-list__lk" href="/mnogo-ru"><img src="/styles/product/img/mnogoru.png"></a></li>
            <li class="product-discounts-list__i"><a class="product-discounts-list__lk" href="/sberbank_spasibo"><img src="/styles/product/img/sberbank.png"></a></li>
        </ul>
    </div>
    <!--/ ссылки связной, сбер и многору -->

    <!-- навигация по странице -->
    <div id="jsScrollSpy" class="product-tabs-scroll jsProductTabs">
        <ul class="nav product-tabs">
            <? if ($showDescription) : ?><li class="product-tabs__i"><a class="product-tabs__lk" href="#more" title="">Подробности</a></li><? endif ?>
            <? if ($showAccessories) : ?><li class="product-tabs__i"><a class="product-tabs__lk" href="#accessorize" title="">Аксессуары</a></li><? endif ?>
            <li class="product-tabs__i"><a class="product-tabs__lk" href="#reviews" title="">Отзывы</a></li>
            <li class="product-tabs__i jsSimilarTab" style="display: none"><a class="product-tabs__lk" href="#similar" title="">Похожие товары</a></li>

            <li class="product-tabs__right product-ep">
                <div class="product-ep-fishka">%</div>
                <div class="product-ep-desc">Фишка со скидкой 20% на этот товар</div>
            </li>
        </ul>
    </div>
	<!--/ навигация по странице -->

    <? if ($showDescription) : ?>

        <!-- характеристики/описание товара -->
        <div class="product-section clearfix">

            <?= $helper->render('product-page/blocks/properties', ['product' => $product]) ?>

            <? if ($hasMedia || $product->getDescription()) : ?>

                <div class="product-section__desc">
                    <div class="product-section__tl" id="more">Описание</div>
                    <?= $helper->render('product-page/blocks/guides', ['trustfactors' => $trustfactors]) ?>
                    <div class="product-section__content"><?= $product->getDescription() ?></div>
                </div>

            <? endif ?>
        </div>
        <!--/ характеристики/описание товара -->

    <? endif ?>

    <? if ($showAccessories): ?>
    <!-- аксессуары -->
	<div class="product-section">
		<div class="product-section__tl" id="accessorize">Аксессуары</div>

            <?= $helper->render('product/__slider', [
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
                'sender2' => $sender2,
            ]) ?>
	</div>
	<!--/ аксессуары -->
    <? endif ?>

	<? if ($reviewsData) : ?>
        <!-- отзывы -->
        <div class="product-section" id="reviews">
            <div class="product-section__tl">Отзывы</div>

            <?= $helper->render('product-page/blocks/reviews', ['reviewsData' => $reviewsData, 'product' => $product ]) ?>


        </div>
        <!--/ отзывы -->
    <? endif ?>

	<!-- похожие товары -->
	<div class="product-section product-section--inn" id="similar">
<!--		<div class="product-section__h3">Похожие товары</div>-->
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
                'sender2' => $sender2,
            ]) ?>
        <? endif ?>
	</div>
	<!--/ похожие товары -->

	<!-- вы смотрели -->
	<div class="product-section product-section--inn">
<!--		<div class="product-section__h3">Вы смотрели</div>-->
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
                    'from'     => 'productPage',
                    'position' => $isProductAvailable ? 'Viewed' : 'ProductMissing',
                ],
                'sender2' => $sender2,
            ]) ?>
        <? endif ?>
	</div>
	<!--/ вы смотрели -->

    <?= !empty($breadcrumbs) ? $helper->renderWithMustache('product-page/blocks/breadcrumbs.mustache', ['breadcrumbs' => $breadcrumbs]) : '' ?>

	<!-- seo информация -->
	<!--<div class="bottom-content">
		<p class="bottom-content__p">
			<span class="bottom-content__tl">Теги: </span>
		</p>
		<p class="bottom-content__p">
			<span class="bottom-content__tl">Похожие товары: </span>
		</p>
		<p class="bottom-content__p bottom-content__text">

		</p>
	</div>-->
	<!--/ seo информация -->
</section>


<!--/ карточка товара -->