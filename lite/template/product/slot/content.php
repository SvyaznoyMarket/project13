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


$secondaryGroupedProperties = $product->getSecondaryGroupedProperties(['Комплектация']);
$equipment = $product->getEquipmentProperty() ? preg_split('/(\r?\n)+/', trim($product->getEquipmentProperty()->getStringValue())) : null;
foreach ($equipment as $key => $value) {
    $equipment[$key] = preg_replace('/\s*<br \/>$/', '', trim(mb_strtoupper(mb_substr($value, 0, 1)) . mb_substr($value, 1)));
}

/* Главные характеристики */
$mainProperties = $product->getMainProperties();
uasort($mainProperties, function(\Model\Product\Property\Entity $a, \Model\Product\Property\Entity $b) {
    return $a->getPosition() - $b->getPosition();
});
?>

<div class="product-card">
    <?= !empty($breadcrumbs) ? $helper->renderWithMustache('product/blocks/breadcrumbs.mustache', ['breadcrumbs' => $breadcrumbs]) : '' ?>

    <section>
        <h1 class="product-name"><?= $helper->escape($product->getName()) ?></h1>

        <div class="product-card-set">
            <div class="product-card-set-info product-card-set__right js-module-require"
                 data-module="enter.product"
                 data-product='<?= json_encode([
                     'id'   => $product->getId(),
                     'ui'   => $product->getUi()
                 ], JSON_HEX_APOS) ?>'>

                <div class="product-card-set-buying">
                    <? if ($product->isOnlyFromPartner() && $product->getPartnerName()) : ?>
                        <!-- Информация о партнере -->
                        <div class="vendor-offer">
                            <p class="vendor-offer__lk i-info" target="_blank">
                                <span class="i-info__tx">Продавец: <?= $product->getPartnerName() ?></span>
                            </p>
                        </div>
                        <!-- /Информация о партнере -->
                    <? endif ?>

                    <? if ($product->getPriceOld()) : ?>
                    <div class="product-card-set-buying__old-price">
                        <span class="line-through"><?= $helper->formatPrice($product->getPriceOld()) ?></span>&thinsp;<span class="rubl">C</span>
                    </div>
                    <? endif ?>

                    <div class="product-card-set-buying__price">
                        <?= $helper->formatPrice($product->getPrice()) ?>&thinsp;<span class="rubl">C</span>
                    </div>

                    <div class="product-card-set-buying__kit">Цена базового комплекта</div>
                    <div class="product-card-set-buying__delivery-time">Срок доставки базового комплекта 3 дня</div>
                </div>

                <div class="product-card-set-recall">
                    <div class="product-card-set-recall__title">Вам перезвонит специалист<br> и поможет выбрать:</div class="product-card-set-info-title">

                    <ul class="product-card-set-recall-list">
                        <li class="product-card-set-recall-list__item">состав комплекта и его изменения;</li>
                        <li class="product-card-set-recall-list__item">условия доставки и сборки.</li>
                    </ul>

                    <?= $helper->render('product/_button.buy', [
                        'product'   => $product,
                        'class'     => 'product-card-set__btn-app btn-primary_bigger btn-primary_centred'
                    ]) ?>

                    <div class="product-card-set-recall__payment-types">Доступные способы оплаты:<br>Наличные, банковский перевод</div>
                </div>

                <dl class="set-specify-list">

                    <? foreach ($mainProperties as $prop) : ?>
                        <dd class="set-specify-list__name"><?= $prop->getName() ?></dd>
                        <dt class="set-specify-list__value">
                            <?= $prop->getStringValue() ?>
                            <? if ($prop->getValueHint()) : ?>
                            <div class="props-list__hint">
                                <a class="i-product i-product--hint" href="" onclick="$('.show').removeClass('show'); $(this).next().addClass('show'); return false;">></a>
                                <!-- попап с подсказкой, чтобы показать/скрыть окно необходимо добавить/удалить класс info-popup--open -->
                                <div class="prop-hint info-popup">
                                    <i class="closer" onclick="$(this).parent().removeClass('show')">×</i>
                                    <div class="info-popup__inn"><?= $prop->getValueHint() ?></div>
                                </div>
                                <!--/ попап с подсказкой -->
                            </div>
                            <? endif ?>
                        </dt>
                    <? endforeach ?>

                    <dd class="set-specify-list__name">
                        <a class="dotted js-go-to" href="#more" title="">Все характеристики</a>
                    </dd>
                    <dt class="set-specify-list__value">
                    </dt>
                </dl>

                <ul class="product-card-tools">
                    <li class="product-card-tools__i product-card-tools__i--compare js-compareProduct" data-bind="" data-id="" data-type-id="">
                        <a href="<?= $page->url('compare.add', ['productId' => $product->getId()]) ?>"
                           class="product-card-tools__lk js-compare-button"
                           data-id="<?= $product->getId()?>">
                            <i class="product-card-tools__icon i-tools-icon i-tools-icon--product-compare"></i>
                            <span class="product-card-tools__tx js-compare-button-status">Сравнить</span>
                        </a>
                    </li>
                </ul>
            </div>

            <div class="product-gallery product-card-set__left js-module-require" data-module="enter.product.photoSlider">
                <div class="product-gallery-image js-photo-container">
                    <img class="image js-photo-zoomedImg" src="<?= $product->getMainImageUrl('product_550') ?>">
                </div>

                <div class="product-gallery-thumbs">
                    <div class="product-gallery-thumbs__wrap">
                        <ul class="product-gallery-thumbs__list">
                            <? foreach ($product->getMedias('image') as $key => $photo) : ?>
                                <li class="product-gallery-thumbs__item jsProductPhotoThumb <?= $key == 0 ? 'active' : '' ?>"
                                    data-middle-img="<?= $photo->getSource('product_500')->url ?>"
                                    data-big-img="<?= $photo->getSource('product_1500')->url ?>"
                                >
                                    <a class="product-gallery-thumbs__link" href="#">
                                        <img class="product-gallery-thumbs__img"
                                            src="<?= $photo->getSource('product_60')->url ?>" alt="">
                                    </a>
                                </li>
                             <? endforeach ?>

                        </ul>
                    </div>

                    <div class="product-gallery-thumbs__btn product-gallery-thumbs__btn_prev"></div>
                    <div class="product-gallery-thumbs__btn product-gallery-thumbs__btn_next"></div>
                </div><!--/slider mini product images -->
            </div><!--/product images section -->
        </div>

        <div id="product-info" data-ui="<?= $product->getUi() ?>"></div>

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

        <!-- навигация по странице -->
        <ul class="nav product-tabs">
            <? if ($product->getKit()) : ?><li class="product-tabs__i"><a class="product-tabs__lk jsScrollSpyKitLink" href="#kit" title="">Состав</a></li><? endif ?>
            <? if ($showDescription) : ?><li class="product-tabs__i"><a class="product-tabs__lk jsScrollSpyMoreLink" href="#more" title="">Подробности</a></li><? endif ?>
            <? if ($showAccessories) : ?><li class="product-tabs__i"><a class="product-tabs__lk jsScrollSpyAccessorizeLink" href="#accessorize" title="">Аксессуары</a></li><? endif ?>
            <? /* <li class="product-tabs__i"><a class="product-tabs__lk jsScrollSpyReviewsLink" href="#reviews" title="">Отзывы</a></li> */ ?>
            <? if ($product->isAvailable()) : ?><li class="product-tabs__i jsSimilarTab" style="display: none"><a class="product-tabs__lk jsScrollSpySimilarLink" href="#similar" title="">Похожие товары</a></li><? endif ?>
        </ul>
        <!--/ навигация по странице -->

        <? if ($isKit) : ?>
            <?= $helper->render('product/blocks/kit', ['product' => $product, 'products' => $kitProducts, 'sender' => $buySender, 'sender2' => $buySender2]) ?>
        <? endif ?>

        <? if ($equipment || $showDescription) : ?>

            <!-- характеристики/описание товара -->
            <div class="product-section" id="more">

                <?= $helper->render('product/slot/properties', ['groupedProperties' => $secondaryGroupedProperties]) ?>

                <? if ($hasMedia || $product->getDescription() || $equipment) : ?>
                    <div class="grid-2col__item">
                        <div class="product-section__desc">
                            <div class="product-section__tl">Базовый комплект</div>

                            <div class="product-section__content"><?= $product->getDescription() ?></div>

                            <? if ($equipment): ?>
                                <div class="product-card__base-set">
                                    <ul class="product-card__base-set-list">
                                        <? foreach ($equipment as $equipmentItem): ?>
                                            <li class="product-card__base-set-item"><?= $equipmentItem ?>.</li>
                                        <? endforeach ?>
                                    </ul>
                                </div>
                            <? endif ?>

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

        <? /* if ($reviewsData) : ?>
            <!-- отзывы -->
            <div class="product-section product-section--reviews" id="reviews">
                <div class="product-section__tl">Отзывы</div>

                <?= $helper->render('product/blocks/reviews', ['reviewsData' => $reviewsData, 'product' => $product ]) ?>


            </div>
            <!--/ отзывы -->
        <? endif */ ?>

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

        <?= $page->blockViewed() ?>

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
    </section>
</div>
<!--/ карточка товара -->