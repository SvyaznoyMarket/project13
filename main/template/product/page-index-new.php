<?php
/**
 * @var $page               \View\Product\IndexPage
 * @var $product            \Model\Product\Entity
 * @var $productVideos      \Model\Product\Video\Entity[]
 * @var $user               \Session\User
 * @var $accessories        \Model\Product\Entity[]
 * @var $accessoryCategory  array
 * @var $related            \Model\Product\Entity[]
 * @var $kit                \Model\Product\Entity[]
 * @var $additionalData     array
 * @var $showAccessoryUpper bool
 * @var $showRelatedUpper   bool
 * @var $shopStates         \Model\Product\ShopState\Entity[]
 */
?>

<?

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
            'swf'=>$model3dExternalUrl,
            'container'=>'maybe3dModel',
            'width'=>'700px',
            'height'=>'500px',
            'version'=>'10.0.0',
            'install'=>'js/expressInstall.swf',
        ],
        'params' => [
            'menu'=> "false",
            'scale'=> "noScale",
            'allowFullscreen'=> "true",
            'allowScriptAccess'=> "always",
            'wmode'=> "direct"
        ],
        'attributes' => [
            'id'=> $model3dName,
        ],
        'flashvars'=> [
            'language'=> "auto",
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



<section class="bProductSection__eLeft">
    <div class="bProductDesc clearfix">
        <div class="bProductDesc__ePhoto">
            <figure class="bProductDesc__ePhoto-bigImg">
                <a class="bigImgLink" href="<?= $product->getImageUrl(4) ?>"><img src="<?= $product->getImageUrl(3) ?>" alt="<?= $page->escape($product->getName()) ?>" /></a>
            </figure><!--/product big image section -->

            <div class="bPhotoAction">
                <ul class="bPhotoAction__eOtherAction">
                    <li class="bPhotoAction__eOtherAction-video"><a href=""></a></li>
                    <li class="bPhotoAction__eOtherAction-grad360"><a href=""></a></li>
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
            <div class="priceOld"><span>7 320</span>p</div>
            <div class="price"><strong>6 890</strong>р</div>
            <div class="priceSale"><span class="dotted">Узнать о снижении цены</span></div>

            <div class="creditbox" style="display: block;">
                <label class="bigcheck" for="creditinput"><b></b>
                    <span class="dotted">Беру в кредит</span>
                    <input id="creditinput" type="checkbox" name="creditinput" autocomplete="off">
                </label>

                <div class="creditbox__sum">от <strong>518</strong>p в месяц</div>
            </div><!--/credit box -->

            <div class="bProductDesc__eStore-text">
                <?= $product->getTagline() ?>
                <div class="text__eAll"><a href="">Характеристики</a></div>
            </div>

            <div class="reviewSection reviewSection100 clearfix">
                <div class="reviewSection__link">
                    <div class="reviewSection__star reviewSection100__star">
                        <img src="/images/reviews_star.png">
                        <img src="/images/reviews_star.png">
                        <img src="/images/reviews_star.png">
                        <img src="/images/reviews_star.png">
                        <img src="/images/reviews_star_empty.png">
                    </div>

                    <span class="border" onclick="scrollToId('reviewsSectionHeader')">13 отзывов</span>
                    <span class="reviewSection__link__write newReviewPopupLink" data-pid="productid">Оставить отзыв</span>
                    <div class="hf" id="reviewsProductName">Смартфон HTC One S черный</div>
                </div>
            </div><!--/review section -->

            <div class="bProductDesc__eStore-select">
                <div class="descSelectItem clearfix">
                    <strong class="descSelectItem__eName">Цвет</strong>
                    <span class="descSelectItem__eValue">16 GB</span>

                    <div class="descSelectItem__eDdm" style="display: none;">
                        <ul>
                            <li>1111</li>
                            <li>2222</li>
                            <li>333</li>
                            <li>444</li>
                        </ul>
                    </div>
                </div>

                <div class="descSelectItem clearfix">
                    <strong class="descSelectItem__eName">Цвет</strong>
                    <span class="descSelectItem__eValue">16 GB</span>

                    <div class="descSelectItem__eDdm" style="display: none;">
                        <ul>
                            <li>1111</li>
                            <li>2222</li>
                            <li>333</li>
                            <li>444</li>
                        </ul>
                    </div>
                </div>

                <div class="descSelectItem clearfix">
                    <strong class="descSelectItem__eName">Цвет</strong>
                    <span class="descSelectItem__eValue">16 GB</span>

                    <div class="descSelectItem__eDdm" style="display: none;">
                        <ul>
                            <li>1111</li>
                            <li>2222</li>
                            <li>333</li>
                            <li>444</li>
                        </ul>
                    </div>
                </div>
            </div><!--/additional product options -->
        </div><!--/product shop description box -->
    </div><!--/product shop description section -->

    <div class="bDescriptionProduct">
        <?= $product->getDescription() ?>
    </div>

    <h3 class="bHeadSection">Аксессуары</h3>

    <div class="bAccessory clearfix">

        <div class="bAccessory__eCat">
            <ul>
                <li class="active"><span>Сумки и чехлы для планшетов</span></li>
                <li><span>Сумки и чехлы для планшетов</span></li>
                <li><span>Стилусы и защитные пленки для планшетов</span></li>
                <li><span>Сумки и чехлы для планшетов</span></li>
                <li><span>Стилусы и защитные пленки для планшетов</span></li>
                <li><span>Сумки и чехлы для планшетов</span></li>
                <li><span>Сумки и чехлы для планшетов</span></li>
                <li><span>Стилусы и защитные пленки для планшетов</span></li>
                <li><span>Сумки и чехлы для планшетов</span></li>
                <li><span>Стилусы и защитные пленки для планшетов</span></li>
            </ul>
        </div>

        <div class="bSliderAction">
            <ul class="bSliderAction__elist clearfix">
                <li>
                    <div class="product__inner">
                        <a class="productImg" href=""><img src="http://fs01.enter.ru/1/1/500/77/142788.jpg" alt="" /></a>
                        <div class="reviewSection__star clearfix reviewSection100__star">
                            <img src="/images/reviews_star.png">
                            <img src="/images/reviews_star.png">
                            <img src="/images/reviews_star.png">
                            <img src="/images/reviews_star.png">
                            <img src="/images/reviews_star_empty.png">
                        </div>
                        <div class="productName"><a href="">Сетевое зарядное устройство Prolife (miniUSB)</a></div>
                        <div class="productPrice"><span class="price">180p</span></div>
                        <div class="btnBuy"><a class="btnBuy__eLink" href="">В корзину</a></div>
                    </div>
                </li>
                <li>
                    <div class="product__inner">
                        <a class="productImg" href=""><img src="http://fs01.enter.ru/1/1/500/77/142788.jpg" alt="" /></a>
                        <div class="reviewSection__star clearfix reviewSection100__star">
                            <img src="/images/reviews_star.png">
                            <img src="/images/reviews_star.png">
                            <img src="/images/reviews_star.png">
                            <img src="/images/reviews_star.png">
                            <img src="/images/reviews_star_empty.png">
                        </div>
                        <div class="productName"><a href="">Сетевое зарядное устройство Prolife (miniUSB)</a></div>
                        <div class="productPrice"><span class="price">180p</span></div>
                        <div class="btnBuy"><a class="btnBuy__eLink" href="">В корзину</a></div>
                    </div>
                </li>
                <li>
                    <div class="product__inner">
                        <a class="productImg" href=""><img src="http://fs01.enter.ru/1/1/500/77/142788.jpg" alt="" /></a>
                        <div class="reviewSection__star clearfix reviewSection100__star">
                            <img src="/images/reviews_star.png">
                            <img src="/images/reviews_star.png">
                            <img src="/images/reviews_star.png">
                            <img src="/images/reviews_star.png">
                            <img src="/images/reviews_star_empty.png">
                        </div>
                        <div class="productName"><a href="">Сетевое зарядное устройство Prolife (miniUSB)</a></div>
                        <div class="productPrice"><span class="price">180p</span></div>
                        <div class="btnBuy"><a class="btnBuy__eLink" href="">В корзину</a></div>
                    </div>
                </li>
            </ul>
            <div class="bSliderAction__eBtn bSliderAction__eDisable bSliderAction__mPrev"><span></span></div>
            <div class="bSliderAction__eBtn bSliderAction__mNext"><span></span></div>
        </div>
    </div><!--/product accessory section -->

    <h3 class="bHeadSection">С этим товаром также смотрят</h3>

    <div class="bSliderAction">
        <ul class="bSliderAction__elist clearfix">
            <li>
                <div class="product__inner">
                    <a class="productImg" href=""><img src="http://fs01.enter.ru/1/1/500/77/142788.jpg" alt="" /></a>
                    <div class="reviewSection__star clearfix reviewSection100__star">
                        <img src="/images/reviews_star.png">
                        <img src="/images/reviews_star.png">
                        <img src="/images/reviews_star.png">
                        <img src="/images/reviews_star.png">
                        <img src="/images/reviews_star_empty.png">
                    </div>
                    <div class="productName"><a href="">Сетевое зарядное устройство Prolife (miniUSB)</a></div>
                    <div class="productPrice"><span class="price">180p</span></div>
                    <div class="btnBuy"><a class="btnBuy__eLink" href="">В корзину</a></div>
                </div>
            </li>

            <li>
                <div class="product__inner">
                    <a class="productImg" href=""><img src="http://fs01.enter.ru/1/1/500/77/142788.jpg" alt="" /></a>
                    <div class="reviewSection__star clearfix reviewSection100__star">
                        <img src="/images/reviews_star.png">
                        <img src="/images/reviews_star.png">
                        <img src="/images/reviews_star.png">
                        <img src="/images/reviews_star.png">
                        <img src="/images/reviews_star_empty.png">
                    </div>
                    <div class="productName"><a href="">Сетевое зарядное устройство Prolife (miniUSB)</a></div>
                    <div class="productPrice"><span class="price">180p</span></div>
                    <div class="btnBuy"><a class="btnBuy__eLink" href="">В корзину</a></div>
                </div>
            </li>

            <li>
                <div class="product__inner">
                    <a class="productImg" href=""><img src="http://fs01.enter.ru/1/1/500/77/142788.jpg" alt="" /></a>
                    <div class="reviewSection__star clearfix reviewSection100__star">
                        <img src="/images/reviews_star.png">
                        <img src="/images/reviews_star.png">
                        <img src="/images/reviews_star.png">
                        <img src="/images/reviews_star.png">
                        <img src="/images/reviews_star_empty.png">
                    </div>
                    <div class="productName"><a href="">Сетевое зарядное устройство Prolife (miniUSB)</a></div>
                    <div class="productPrice"><span class="price">180p</span></div>
                    <div class="btnBuy"><a class="btnBuy__eLink" href="">В корзину</a></div>
                </div>
            </li>

            <li>
                <div class="product__inner">
                    <a class="productImg" href=""><img src="http://fs01.enter.ru/1/1/500/77/142788.jpg" alt="" /></a>
                    <div class="reviewSection__star clearfix reviewSection100__star">
                        <img src="/images/reviews_star.png">
                        <img src="/images/reviews_star.png">
                        <img src="/images/reviews_star.png">
                        <img src="/images/reviews_star.png">
                        <img src="/images/reviews_star_empty.png">
                    </div>
                    <div class="productName"><a href="">Сетевое зарядное устройство Prolife (miniUSB)</a></div>
                    <div class="productPrice"><span class="price">180p</span></div>
                    <div class="btnBuy"><a class="btnBuy__eLink" href="">В корзину</a></div>
                </div>
            <li>
                <div class="product__inner">
                    <a class="productImg" href=""><img src="http://fs01.enter.ru/1/1/500/77/142788.jpg" alt="" /></a>
                    <div class="reviewSection__star clearfix reviewSection100__star">
                        <img src="/images/reviews_star.png">
                        <img src="/images/reviews_star.png">
                        <img src="/images/reviews_star.png">
                        <img src="/images/reviews_star.png">
                        <img src="/images/reviews_star_empty.png">
                    </div>
                    <div class="productName"><a href="">Сетевое зарядное устройство Prolife (miniUSB)</a></div>
                    <div class="productPrice"><span class="price">180p</span></div>
                    <div class="btnBuy"><a class="btnBuy__eLink" href="">В корзину</a></div>
                </div>
            </li>
        </ul>
        <div class="bSliderAction__eBtn bSliderAction__eDisable bSliderAction__mPrev"><span></span></div>
        <div class="bSliderAction__eBtn bSliderAction__mNext"><span></span></div>
    </div><!--/product more section -->

    <h3 class="bHeadSection">Характеристики</h3>

    <div class="bSpecifications">
        <div class="bSpecifications__eHead">Общие</div>
        <dl class="bSpecifications__eList clearfix">
            <dd><span>Тип</span></dd>
            <dt>GSM (800/900/1800/1900 МГц), HSPA+ (850/900/1700/1900/2100 МГц)</dt>
            <dd>
                <span>Двухъядерный процессор
                <div class="bHint">
                  <a class="bHint_eLink">Разрешение дисплея</a>
                  <div class="bHint_ePopup popup">
                    <div class="close"></div>
                    <div class="bHint-text">
                        <p>Разрешение дисплея – это количество мельчайших точек, из которых складывается общая картинка. Каждая точка называется пикселем. Так как этих точек в современных экранах очень много, разрешение записывается двумя числами: первое отражает количество пикселей по горизонтали, второе по вертикали. От разрешения дисплея зависит многое. Как будут выглядеть фотографии и сайты, нужно ли вам будет конвертировать видео, запустится ли игра. И это тот случай, когда чем больше – тем лучше.</p>
                    </div>
                  </div>
                </div>
                </span>
            </dd>
            <dt>GSM (800/900/1800/1900 МГц), HSPA+ (850/900/1700/1900/2100 МГц)</dt>
            <dd><span>Тип</span></dd>
            <dt>GSM (800/900/1800/1900 МГц), HSPA+ (850/900/1700/1900/2100 МГц)</dt>
            <dd><span>Разрешение видео</span></dd>
            <dt>GSM (800/900/1800/1900 МГц), HSPA+ </dt>
            <dd><span>Тип</span></dd>
            <dt>GSM (800/900/1800/1900 МГц), HSPA+ (850/900/1700/1900/2100 МГц)</dt>
            <dd><span>Тип</span></dd>
            <dt>GSM (800/900/1800/1900 МГц), HSPA+ (850/900/1700/1900/2100 МГц)</dt>
        </dl>

        <div class="bSpecifications__eHead">Общие</div>
        <dl class="bSpecifications__eList clearfix">
            <dd><span>Тип</span></dd>
            <dt>GSM (800/900/1800/1900 МГц), HSPA+ (850/900/1700/1900/2100 МГц)</dt>
            <dd><span>Двухъядерный процессор</span></dd>
            <dt>GSM (800/900/1800/1900 МГц), HSPA+ (850/9100 МГц)</dt>
            <dd><span>Тип</span></dd>
            <dt>GSM (800/900/1800/1900 МГц), HSPA+ (850/900/1700/1900/2100 МГц)</dt>
            <dd><span>Разрешение видео </dd>
            <dt>GSM (800/900/1800/1900 МГц), HSPA+ </dt>
            <dd><span>Тип</span></dd>
            <dt>GSM (800/900/1800/1900 МГц), HSPA+ (850/900/1700/1900/2100 МГц)</dt>
            <dd><span>Тип</span></dd>
            <dt>GSM (800/900/1800/1900 МГц), HSPA+ (850/900/1700/1900/2100 МГц)</dt>
        </dl>

        <div class="bSpecifications__eHead">Общие</div>
        <dl class="bSpecifications__eList clearfix">
            <dd><span>Тип</span></dd>
            <dt>GSM (800/900/1800/1900 МГц), HSPA+ (850/900/1700/1900/)</dt>
            <dd><span>Двухъядерный процессор</span></dd>
            <dt>GSM (800/900/1800/1900 МГц), HSPA+ (850/900/12100 МГц)</dt>
            <dd><span>Тип</span></dd>
            <dt>GSM (800/900/1800/1900 МГц), HSPA+ (850/900/1700/1900/2100 МГц)</dt>
            <dd><span>Разрешение видео</span></dd>
            <dt>GSM (800/900/1800/1900 МГц), HSPA+ (850/9/2100 МГц)</dt>
            <dd><span>Тип</span></dd>
            <dt>GSM (800/900/1800/1900 МГц), HSPA+ (850/900/1700/1900/2100 МГц)</dt>
            <dd><span>Тип</span></dd>
            <dt>GSM (800/900/1800/1900 МГц), HSPA+ (800 МГц)</dt>
        </dl>
    </div><!--/product specifications section -->

    <h3 class="bHeadSection">Похожие товары</h3>

    <div class="bSliderAction mNoSliderAction">
        <ul class="bSliderAction__elist clearfix">
            <li>
                <div class="product__inner">
                    <a class="productImg" href=""><img src="http://fs01.enter.ru/1/1/500/77/142788.jpg" alt="" /></a>
                    <div class="reviewSection__star clearfix reviewSection100__star">
                        <img src="/images/reviews_star.png">
                        <img src="/images/reviews_star.png">
                        <img src="/images/reviews_star.png">
                        <img src="/images/reviews_star.png">
                        <img src="/images/reviews_star_empty.png">
                    </div>
                    <div class="productName"><a href="">Сетевое зарядное устройство Prolife (miniUSB)</a></div>
                    <div class="productPrice"><span class="price">180p</span></div>
                    <div class="btnBuy"><a class="btnBuy__eLink" href="">В корзину</a></div>
                </div>
            </li>

            <li>
                <div class="product__inner">
                    <a class="productImg" href=""><img src="http://fs01.enter.ru/1/1/500/77/142788.jpg" alt="" /></a>
                    <div class="reviewSection__star clearfix reviewSection100__star">
                        <img src="/images/reviews_star.png">
                        <img src="/images/reviews_star.png">
                        <img src="/images/reviews_star.png">
                        <img src="/images/reviews_star.png">
                        <img src="/images/reviews_star_empty.png">
                    </div>
                    <div class="productName"><a href="">Сетевое зарядное устройство Prolife (miniUSB)</a></div>
                    <div class="productPrice"><span class="price">180p</span></div>
                    <div class="btnBuy"><a class="btnBuy__eLink" href="">В корзину</a></div>
                </div>
            </li>

            <li>
                <div class="product__inner">
                    <a class="productImg" href=""><img src="http://fs01.enter.ru/1/1/500/77/142788.jpg" alt="" /></a>
                    <div class="reviewSection__star clearfix reviewSection100__star">
                        <img src="/images/reviews_star.png">
                        <img src="/images/reviews_star.png">
                        <img src="/images/reviews_star.png">
                        <img src="/images/reviews_star.png">
                        <img src="/images/reviews_star_empty.png">
                    </div>
                    <div class="productName"><a href="">Сетевое зарядное устройство Prolife (miniUSB)</a></div>
                    <div class="productPrice"><span class="price">180p</span></div>
                    <div class="btnBuy"><a class="btnBuy__eLink" href="">В корзину</a></div>
                </div>
            </li>

            <li>
                <div class="product__inner">
                    <a class="productImg" href=""><img src="http://fs01.enter.ru/1/1/500/77/142788.jpg" alt="" /></a>
                    <div class="reviewSection__star clearfix reviewSection100__star">
                        <img src="/images/reviews_star.png">
                        <img src="/images/reviews_star.png">
                        <img src="/images/reviews_star.png">
                        <img src="/images/reviews_star.png">
                        <img src="/images/reviews_star_empty.png">
                    </div>
                    <div class="productName"><a href="">Сетевое зарядное устройство Prolife (miniUSB)</a></div>
                    <div class="productPrice"><span class="price">180p</span></div>
                    <div class="btnBuy"><a class="btnBuy__eLink" href="">В корзину</a></div>
                </div>
            <li>
                <div class="product__inner">
                    <a class="productImg" href=""><img src="http://fs01.enter.ru/1/1/500/77/142788.jpg" alt="" /></a>
                    <div class="reviewSection__star clearfix reviewSection100__star">
                        <img src="/images/reviews_star.png">
                        <img src="/images/reviews_star.png">
                        <img src="/images/reviews_star.png">
                        <img src="/images/reviews_star.png">
                        <img src="/images/reviews_star_empty.png">
                    </div>
                    <div class="productName"><a href="">Сетевое зарядное устройство Prolife (miniUSB)</a></div>
                    <div class="productPrice"><span class="price">180p</span></div>
                    <div class="btnBuy"><a class="btnBuy__eLink" href="">В корзину</a></div>
                </div>
            </li>
        </ul>
    </div><!--/product more section -->
</section><!--/left section -->

<aside class="bProductSection__eRight">
    <div class="bWidgetBuy mWidget">
        <div class="bCountSection clearfix">
            <button class="bCountSection__eM">-</button>
            <input class="bCountSection__eNum" type="text" value="1" />
            <button class="bCountSection__eP">+</button>
            <span>шт.</span>
        </div><!--/counter -->

        <div class="bWidgetBuy__eBuy btnBuy"><a class="btnBuy__eLink" href="">В корзину</a></div><!--/button buy -->

        <div class="bWidgetBuy__eClick"><a href="">Купить быстро в 1 клик</a></div>

        <ul class="bWidgetBuy__eDelivery">
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
                <label for="name1" class="customInput radio">
                    <input name="name1" type="radio" />

                    <b></b>

                    <div class="labelText">
                        <div>
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
                        </div>
                        <strong>1 490</strong>p
                        <div style="display: none;" class="deSelect"><a class="">Отменить</a></div>
                    </div>
                </label>
            </li>

            <li>
                <label for="name2" class="customInput radio checked">
                    <input name="name1" type="radio" />

                    <b></b>

                    <div class="labelText">
                        <div>
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
                            </div>
                        <strong>1 490</strong>p
                        <div style="display: block;" class="deSelect"><a class="">Отменить</a></div>
                    </div>
                </label>
            </li>

            <li>
                <label for="name3" class="customInput radio">
                    <input name="name1" type="radio" />

                    <b></b>

                    <div class="labelText">
                        <div>
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
                        </div>
                        <strong>1 490</strong>p
                        <div style="display: none;" class="deSelect"><a class="">Отменить</a></div>
                    </div>
                </label>
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
                <label for="name4" class="customInput checkbox">
                    <input name="name4" type="checkbox" />

                    <b></b>

                    <div class="labelText">
                        <div>
                            <span class="dotted">Подключение<br/>электричества</span>
                            <div class="bHint">
                                <a class="bHint_eLink">Разрешение дисплея</a>
                                <div class="bHint_ePopup popup">
                                    <div class="close"></div>
                                    <div class="bHint-text">
                                        <p>конвертировать видео, запустится ли игра. И это тот случай, когда чем больше – тем лучше.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <strong>1 490</strong>p
                        <div style="display: none;" class="deSelect"><a class="">Отменить</a></div>
                    </div>
                </label>
            </li>
        </ul>

        <div class="bWidgetService__eAll"><span class="dotted">Ещё 87 услуг</span><br/>доступны в магазине</div>
    </div><!--/widget services -->
</aside><!--/right section -->
