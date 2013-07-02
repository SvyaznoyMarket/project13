<?php
/**
 * @var $renderer           \Templating\PhpClosureEngine
 * @var $page               \View\Product\IndexPage
 * @var $product            \Model\Product\Entity
 * @var $productVideos      \Model\Product\Video\Entity[]
 * @var $user               \Session\User
 * @var $accessories        \Model\Product\Entity[]
 * @var $accessoryCategory  array
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
        <div class="bProductDesc clearfix">

            <div class="bProductDesc__ePhoto">
                <figure class="bProductDesc__ePhoto-bigImg">
                    <img class="bZoomedImg" src="<?= $product->getImageUrl(3) ?>" data-zoom-image="<?= $product->getImageUrl(4) ?>" alt="<?= $page->escape($product->getName()) ?>" />
                </figure><!--/product big image section -->

                <div class="bPhotoAction">
                    <ul class="bPhotoAction__eOtherAction">
                        <? if ($productVideo && $productVideo->getContent()): ?>
                            <li class="bPhotoAction__eOtherAction-video"><a href=""></a></li>
                        <? endif ?>
                        <? if (count($photoList) || $model3dExternalUrl || $model3dImg):  ?>
                            <li class="bPhotoAction__eOtherAction-grad360 <?=$model3dExternalUrl?'maybe3d':''?><?=$model3dImg?'3dimg':''?>"><a href=""></a></li>
                        <? endif ?>
                    </ul><!--/view product section -->

                    <div class="bPhotoAction__eOtherPhoto mSliderActionMiniPhoto">
                        <ul>
                            <? foreach ($photoList as $photo): ?>
                            <li>
                                <a href="">
                                    <figure><img src="<?= $photo->getUrl(3) ?>" alt="" /></figure>
                                </a>
                            </li>
                            <? endforeach ?>
                        </ul>

                        <div class="mSliderActionMiniPhoto__eBtn mSliderActionMiniPhoto__eDisable mSliderActionMiniPhoto__mPrev"><span>&#9668;</span></div>
                        <div class="mSliderActionMiniPhoto__eBtn mSliderActionMiniPhoto__mNext"><span>&#9658;</span></div>
                    </div><!--/slider mini product images -->
                </div>
            </div><!--/product images section -->

            <div class="bProductDesc__eStore">
                <? if ($product->getIsBuyable()): ?>
                    <link itemprop="availability" href="http://schema.org/InStock" />
                    <div class="inStock">Есть в наличии</div>
                <? elseif (!$product->getIsBuyable() && $product->getState()->getIsShop()): ?>
                    <link itemprop="availability" href="http://schema.org/InStoreOnly" />
                <? else: ?>
                    <link itemprop="availability" href="http://schema.org/OutOfStock" />
                <? endif ?>

                <? if($product->getPriceOld() && !$user->getRegion()->getHasTransportCompany()): ?>
                    <div class="priceOld"><span><?= $page->helper->formatPrice($product->getPriceOld()) ?></span>p</div>
                <? endif ?>
                <div class="price"><strong><?= $page->helper->formatPrice($product->getPrice()) ?></strong>р</div>

                <? if ($hasLowerPriceNotification): ?>
                <?
                    $lowerPrice =
                        ($product->getMainCategory() && $product->getMainCategory()->getPriceChangePercentTrigger())
                            ? round($product->getPrice() * $product->getMainCategory()->getPriceChangePercentTrigger())
                            : 0;
                ?>
                <div class="priceSale">
                    <span class="dotted jsLowPriceNotifer">Узнать о снижении цены</span>
                    <div class="bLowPriceNotiferPopup popup">
                        <i class="close"></i>
                        <h2 class="bLowPriceNotiferPopup__eTitle">
                            Вы получите письмо,<br/>когда цена станет ниже
                            <? if ($lowerPrice && ($lowerPrice < $product->getPrice())): ?>
                                <strong class="price"><?= $page->helper->formatPrice($lowerPrice) ?></strong> <span class="rubl">p</span>
                            <? endif ?>
                        </h2>
                        <input class="bLowPriceNotiferPopup__eInputEmail" placeholder="Ваш email" value="<?= $user->getEntity() ? $user->getEntity()->getEmail() : '' ?>" />
                        <p class="bLowPriceNotiferPopup__eError red"></p>
                        <a href="#" class="bLowPriceNotiferPopup__eSubmitEmail button bigbuttonlink mDisabled" data-url="<?= $page->url('product.notification.lowerPrice', ['productId' => $product->getId()]) ?>">Сохранить</a>
                    </div>
                </div>
                <? endif ?>

                <? if ($creditData['creditIsAllowed'] && !$user->getRegion()->getHasTransportCompany()) : ?>
                <div class="creditbox">
                    <label class="bigcheck" for="creditinput"><b></b>
                        <span class="dotted">Беру в кредит</span>
                        <input id="creditinput" type="checkbox" name="creditinput" autocomplete="off">
                    </label>

                    <div class="creditbox__sum">от <strong></strong>p в месяц</div>
                    <input data-model="<?= $page->escape($creditData['creditData']) ?>" id="dc_buy_on_credit_<?= $product->getArticle(); ?>" name="dc_buy_on_credit" type="hidden" />
                </div><!--/credit box -->
                <? endif ?>

                <div class="bProductDesc__eStore-text">
                    <?= $product->getTagline() ?>
                    <div class="text__eAll"><a href="">Характеристики</a></div>
                </div>

                <div class="bReviewSection clearfix">
                    <div class="bReviewSection__eStar">
                        <? $avgStarScore = empty($reviewsData['avg_star_score']) ? 0 : $reviewsData['avg_star_score'] ?>
                        <?= empty($avgStarScore) ? '' : $page->render('product/_starsFive', ['score' => $avgStarScore]) ?>
                    </div>
                    <? if (!empty($avgStarScore)) { ?>
                        <span class="border" onclick="scrollToId('bHeadSectionReviews')"><?= $reviewsData['num_reviews'] ?> <?= $page->helper->numberChoice($reviewsData['num_reviews'], array('отзыв', 'отзыва', 'отзывов')) ?></span>
                    <? } else { ?>
                        <span>Отзывов нет</span>
                    <? } ?>
                    <span class="bReviewSection__eWrite jsLeaveReview" data-pid="productid">Оставить отзыв</span>
                </div><!--/review section -->

                <? if ((bool)$product->getModel() && (bool)$product->getModel()->getProperty()): //модели ?>
                <div class="bProductDesc__eStore-select">
                <? foreach ($product->getModel()->getProperty() as $property): ?>
                    <? if ($property->getIsImage()): ?>
                    <? else: ?>
                    <?
                        $productAttribute = $product->getPropertyById($property->getId());
                        if (!$productAttribute) break;
                    ?>

                    <? endif ?>
                    <div class="descSelectItem clearfix">
                        <strong class="descSelectItem__eName"><?= $property->getName() ?></strong>
                        <span class="descSelectItem__eValue"><?= $productAttribute->getStringValue() ?></span>

                        <div class="descSelectItem__eDdm" style="display: none;">
                            <ul>
                            <? foreach ($property->getOption() as $option): ?>
                            <? if ($option->getValue() == $productAttribute->getValue()) continue ?>
                                <li>
                                    <a href="<?= $option->getProduct()->getLink() ?>"><?= $option->getHumanizedName() ?></a>
                                </li>
                            <? endforeach ?>
                            </ul>
                        </div>
                    </div>
                <? endforeach ?>

                </div><!--/additional product options -->
                <? endif ?>

            </div><!--/product shop description box -->
        </div><!--/product shop description section -->

        <div class="bDescriptionProduct">
            <?= $product->getDescription() ?>
        </div>

        <? if ((bool)$accessories && \App::config()->product['showAccessories']): ?>
        <h3 class="bHeadSection">Аксессуары</h3>
        <div class="bAccessory clearfix">

            <? if ((bool)$accessoryCategory): ?>
            <div class="bAccessory__eCat">
                <ul>
                <? $i = 0; foreach ($accessoryCategory as $iCategory): ?>
                    <li<? if (0 == $i): ?> class="active"<? endif ?>><span><?= $iCategory->getName() ?></span></li>
                <? $i++; endforeach ?>
                </ul>
            </div>
            <? endif ?>

            <?= $renderer->render('product/__slider', [
                'products'       => array_values($accessories),
                'count'          => count($product->getAccessoryId()),
                'limit'          => $accessoryCategory ? \App::config()->product['itemsInAccessorySlider'] : \App::config()->product['itemsInSlider'],
                'page'           => 1,
                'url'            => $page->url('product.accessory', ['productToken' => $product->getToken()]),
                'gaEvent'        => 'Accessorize',
                'additionalData' => $additionalData,
            ]) ?>
        </div><!--/product accessory section -->
        <? endif ?>

        <? if ((bool)$related && \App::config()->product['showRelated']): ?>
            <h3 class="bHeadSection">С этим товаром также покупают</h3>
            <?= $renderer->render('product/__slider', [
                'products'       => array_values($related),
                'count'          => count($product->getRelatedId()),
                'limit'          => \App::config()->product['itemsInSlider'],
                'page'           => 1,
                'url'            => $page->url('product.related', ['productToken' => $product->getToken()]),
                'additionalData' => $additionalData,
            ]) ?>
        <? endif ?>

        <h3 class="bHeadSection">Характеристики</h3>
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
                        <?= $renderer->render('product/__propertyHint', ['name' => $property->getName(), 'value' => $property->getHint()]) ?>
                    <? endif ?>
                    </span>
                </dd>
                <dt>
                    <?= $property->getStringValue() ?>
                    <? if ($property->getValueHint()): ?>
                        <?= $renderer->render('product/__propertyHint', ['name' => $property->getStringValue(), 'value' => $property->getValueHint()]) ?>
                    <? endif ?>
                </dt>
            <? endforeach ?>
            </dl>
        <? endforeach ?>
        </div><!--/product specifications section -->

        <h3 class="bHeadSection" id="bHeadSectionReviews">Обзоры и отзывы</h3>

        <div class="bReviews">
            <? if (\App::config()->product['reviewEnabled'] && $reviewsPresent): ?>
                <div class="bReviewsSummary clearfix">
                    <?= $page->render('product/_reviewsSummary', ['reviewsData' => $reviewsData, 'reviewsDataPro' => $reviewsDataPro, 'reviewsDataSummary' => $reviewsDataSummary]) ?>
                </div>

                <? if (!empty($reviewsData['review_list'])) { ?>
                    <div class="bReviewsWrapper" data-product-id="<?= $product->getId() ?>" data-page-count="<?= $reviewsData['page_count'] ?>" data-container="reviewsUser" data-reviews-type="user">
                <? } elseif(!empty($reviewsDataPro['review_list'])) { ?>
                <div class="bReviewsWrapper" data-product-id="<?= $product->getId() ?>" data-page-count="<?= $reviewsDataPro['page_count'] ?>" data-container="reviewsPro" data-reviews-type="pro">
                    <? } ?>
                <?= $page->render('product/_reviews', ['reviewsData' => $reviewsData, 'reviewsDataPro' => $reviewsDataPro]) ?>
                </div>
            <? endif ?>
            </div>
        </div>


        <? if (!$product->getIsBuyable() && $product->getState()->getIsShop()  && \App::config()->smartengine['pull']): ?>
            <h3 class="bHeadSection">Похожие товары</h3>
            <?= $renderer->render('product/__slider', [
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
            <div class="bCountSection clearfix" data-spinner="<?= $page->json(['button' => sprintf('cartButton-product-%s', $product->getId())]) ?>">
                <button class="bCountSection__eM">-</button>
                <input class="bCountSection__eNum" type="text" value="1" />
                <button class="bCountSection__eP">+</button>
                <span>шт.</span>
            </div><!--/counter -->

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

            <ul class="bWidgetBuy__eDelivery" data-value="<?= $page->json(['url' => $page->url('product.delivery')]) ?>">
                <li class="bWidgetBuy__eDelivery-item bWidgetBuy__eDelivery-price">
                    <span>Доставка <strong>290</strong>p</span>
                    <div>Завтра, 16.05.2013</div>
                </li>
                <li class="bWidgetBuy__eDelivery-item bWidgetBuy__eDelivery-free">
                    <span>Самовывоз <strong>бесплатно</strong></span>
                    <div>Завтра, 16.05.2013</div>
                </li>

                <li class="bWidgetBuy__eDelivery-item bWidgetBuy__eDelivery-now click">
                    <span class="dotted">Есть в магазинах</span>
                    <div>Купить сегодня без предзаказа</div>
                </li>

                <ul style="display: block;" class="bDeliveryFreeAddress">
                    <li>
                        м. Белорусская,<br/>
                        ул. Грузинский вал, д. 31
                    </li>
                    <li>
                        м. Ленинский проспект, <br/>
                        ул. Орджоникидзе, д. 11, стр. 10
                    </li>
                    <li>
                        м. Белорусская, <br/>
                        ул. Грузинский вал, д. 31
                    </li>
                    <li>
                        м. Ленинский проспект, <br/>
                        ул. Орджоникидзе, д. 11, стр. 10
                    </li>
                    <li>
                        м. Белорусская, <br/>
                        ул. Грузинский вал, д. 31
                    </li>
                    <li>
                        м. Ленинский проспект, <br/>
                        ул. Орджоникидзе, д. 11, стр. 10
                    </li>
                    <li>
                        м. Белорусская, <br/>
                        ул. Грузинский вал, д. 31
                    </li>
                    <li>
                        м. Ленинский проспект, <br/>
                        ул. Орджоникидзе, д. 11, стр. 10
                    </li>
                </ul><!--/выпадающий список при клике по - Есть в магазинах -->
            </ul>

            <div class="bAwardSection"><figure><img src="/css/newProductCard/img/award.jpg" alt="" /></figure></div>
        </div><!--/widget delivery -->

        <div class="bWidgetService mWidget">
            <div class="bWidgetService__eHead">
                <strong>Под защитой F1</strong>
                Расширенная гарантия
            </div>

            <ul class="bWidgetService__eInputList">
                <li>
                    <input id="id4" name="name1" type="radio" hidden />
                    <label class="bCustomInput" for="id4">
                        <div class="bCustomInput__eText">
                            <span class="dotted">Black: 2 годa</span>

                            <div class="bHint">
                              <a class="bHint_eLink">Разрешение дисплея</a>
                              <div class="bHint_ePopup popup">
                                <div class="close"></div>
                                <div class="bHint-text">
                                    <p>конвертировать видео, запустится ли игра. И это тот случай, когда чем больше – тем лучше.</p>
                                </div>
                              </div>
                            </div>

                            <div class="bCustomInput__ePrice"><strong>1 490</strong>p</div>
                        </div>
                    </label>
                    <div style="display: block;" class="bDeSelect"><a href="">Отменить</a></div>
                </li>

                <li>
                    <input id="id3" name="name1" type="radio" hidden />
                    <label class="bCustomInput" for="id3">
                        <div class="bCustomInput__eText">
                            <span class="dotted">Gold: 2,5 годa</span>

                            <div class="bHint">
                              <a class="bHint_eLink">Разрешение дисплея</a>
                              <div class="bHint_ePopup popup">
                                <div class="close"></div>
                                <div class="bHint-text">
                                    <p>конвертировать видео, запустится ли игра. И это тот случай, когда чем больше – тем лучше.</p>
                                </div>
                              </div>
                            </div>

                            <div class="bCustomInput__ePrice"><strong>1 490</strong>p</div>
                        </div>
                    </label>
                    <div style="display: none;" class="bDeSelect"><a href="">Отменить</a></div>
                </li>

                <li>
                    <input id="id2" name="name1" type="radio" hidden />
                    <label class="bCustomInput" for="id2">
                        <div class="bCustomInput__eText">
                            <span class="dotted">Platinum: 3 годa</span>

                            <div class="bHint">
                                <a class="bHint_eLink">Разрешение дисплея</a>
                                <div class="bHint_ePopup popup">
                                    <div class="close"></div>
                                    <div class="bHint-text">
                                        <p>конвертировать видео, запустится ли игра. И это тот случай, когда чем больше – тем лучше.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="bCustomInput__ePrice"><strong>1 490</strong>p</div>
                        </div>
                    </label>
                    <div style="display: none;" class="bDeSelect"><a href="">Отменить</a></div>
                </li>
            </ul>
        </div><!--/widget services -->

        <div class="bWidgetService mWidget">
            <div class="bWidgetService__eHead">
                <strong>F1 сервис</strong>
                Установка и настройка
            </div>

            <ul class="bWidgetService__eInputList">
                <li>
                    <input id="id1" name="name4" type="checkbox" hidden />
                    <label class="bCustomInput" for="id1">
                        <div class="bCustomInput__eText">
                            <span class="dotted">Подключение электричества</span>

                            <div class="bHint">
                                <a class="bHint_eLink">Разрешение дисплея</a>
                                <div class="bHint_ePopup popup">
                                    <div class="close"></div>
                                    <div class="bHint-text">
                                        <p>конвертировать видео, запустится ли игра. И это тот случай, когда чем больше – тем лучше.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="bCustomInput__ePrice"><strong>1 490</strong>p</div>
                        </div>
                    </label>
                </li>
            </ul>
            <div class="bWidgetService__eAll"><span class="dotted">Ещё 87 услуг</span><br/>доступны в магазине</div>
        </div><!--/widget services -->
    </aside>
</div><!--/right section -->

<?= $helper->render('product/__delivery') ?>

<? if ($product->getIsBuyable()): ?>
    <?= $page->render('order/form-oneClick') ?>
<? endif ?>

<? if (\App::config()->analytics['enabled']): ?>
    <?= $page->tryRender('product/partner-counter/_cityads', ['product' => $product]) ?>
    <?= $page->tryRender('product/partner-counter/_recreative', ['product' => $product]) ?>
<? endif ?>