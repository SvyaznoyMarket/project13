<?php
/**
 * @var $page                   \View\ProductCategory\LeafPage
 * @var $category               \Model\Product\Category\Entity
 * @var $brand                  \Model\Brand\Entity|null
 * @var $productFilter          \Model\Product\Filter
 * @var $productPager           \Iterator\EntityPager
 * @var $productSorting         \Model\Product\Sorting
 * @var $productView            string
 * @var $productVideosByProduct array
 * @var $hotlinks               array
 * @var $seoContent             string
 * @var $relatedCategories      array
 * @var $categoryConfigById     array
 */
?>

<?
    $helper = new \Helper\TemplateHelper();
    if ($productFilter->getShop()) $page->setGlobalParam('shop', $productFilter->getShop());

    // получаем стиль листинга
    $listingStyle = !empty($catalogJson['listing_style']) ? $catalogJson['listing_style'] : null;

    // получаем promo стили
    $promoStyle = 'jewel' === $listingStyle && isset($catalogJson['promo_style']) ? $catalogJson['promo_style'] : [];
?>

<div class="bCatalog<?= 'jewel' === $listingStyle ? ' mCustomCss' : '' ?>" id="bCatalog" data-lastpage="<?= $productPager->getLastPage() ?>">

    <?= $helper->render('product-category/__breadcrumbs', ['category' => $category, 'isBrand' => isset($brand)]) // хлебные крошки ?>

    <div class="bCustomFilter"<? if(!empty($promoStyle['promo_image'])): ?> style="<?= $promoStyle['promo_image'] ?>"<? endif ?>>
        <h1 class="bTitlePage"<? if(!empty($promoStyle['title'])): ?> style="<?= $promoStyle['title'] ?>"<? endif ?>><?= $title ?></h1>

        <? if (\App::config()->adFox['enabled']): ?>
        <!-- Баннер --><div id="adfox683sub" class="adfoxWrapper bBannerBox"></div><!--/ Баннер -->
        <? endif ?>

        <? if (!empty($promoContent)): ?>
            <?= $promoContent ?>
        <? elseif ($productPager->getLastPage() > 1): ?>
            <?= $helper->render('product-category/__children',
                [
                    'category'           => $category,
                    'promoStyle'         => $promoStyle,
                    'relatedCategories'  => $relatedCategories,
                    'categoryConfigById' => $categoryConfigById,
                ]
            ) // дочерние категории and relatedCategories ?>
        <? endif ?>

        <?= $helper->render('product-category/__filter', [
            'baseUrl'       => $helper->url('product.category', ['categoryPath' => $category->getPath()]),
            'countUrl'      => $helper->url('product.category.count', ['categoryPath' => $category->getPath()]),
            'productFilter' => $productFilter,
            'hotlinks'      => $hotlinks,
            'openFilter'    => false,
            'promoStyle'    => $promoStyle,
        ]) // фильтры ?>
        
        <!-- Спец-предложения -->
        <div class="specialPrice">
            <div class="specialPriceItem mFirst">
                <div class="specialPriceItemTitle">Хит продаж</div><!--/ шапка -->

                <div class="specialPriceItemCont">
                    <a class="specialPriceItemCont_imgLink" href="">
                        <img class="specialPriceItemCont_img" src="http://fs09.enter.ru/1/1/500/1c/30878.jpg" alt="" />
                    </a><!--/ картинка продукта -->

                    <a class="specialPriceItemCont_name" href="">
                        <span class="catName">Стиральная машина</span>
                        <strong class="prodName">Samsung WF8590NMW9</strong>
                    </a><!--/ название продукта -->

                    <p class="specialPriceItemCont_desc">
                        Стиральная машина с максимальной загрузкой белья 6 кг и скоростью вращения 1000 об/мин имеет 5 программ стирки и керамическим нагревателем против накипи.
                    </p><!--/ описание продукта -->

                    <div class="specialPriceItemCont_buy">
                        <div class="priceBox">
                            <span class="priceBox_new mStatic"><strong>6 990</strong> <span class="rubl">p</span></span><!-- если нет скидки то добавляем класс модификатор mStatic -->
                        </div>

                        <div class="btnBuy">
                            <a class="btnBuy_link jsBuyButton" href="">Купить</a>
                        </div>
                    </div><!--/ купить продукт -->
                </div><!--/ продукт -->

                <div class="specialPriceItemFoot">
                    <a class="specialPriceItemFoot_link" href="">
                        <span class="specialPriceItemFoot_text">Похожие товары</span>
                    </a>
                </div><!--/ кнопка - похожие товары -->
            </div><!--/ блок спец-предложения -->

            <div class="specialPriceItem">
                <div class="specialPriceItemTitle mProfit">Выгодное предложение</div><!--/ шапка, модификатор mProfit -->

                <div class="specialPriceItemCont">
                    <a class="specialPriceItemCont_imgLink" href="">
                        <img class="specialPriceItemCont_img" src="http://fs09.enter.ru/1/1/500/1c/30878.jpg" alt="" />
                    </a><!--/ картинка продукта -->

                    <a class="specialPriceItemCont_name" href="">
                        <span class="catName">Стиральная машина</span>
                        <strong class="prodName">Samsung WF8590NMW9</strong>
                    </a><!--/ название продукта -->

                    <p class="specialPriceItemCont_desc">
                        Стиральная машина с максимальной загрузкой белья 6 кг и скоростью вращения 1000 об/мин имеет 5 программ стирки и керамическим нагревателем против накипи.
                    </p><!--/ описание продукта -->

                    <div class="specialPriceItemCont_buy">
                        <div class="priceBox">
                            <span class="priceBox_old"> 
                                <span class="price">36 490</span> <span class="rubl">p</span> 
                                <span class="persent">5%</span>
                            </span>

                            <span class="priceBox_new"><strong>6 990</strong> <span class="rubl">p</span></span>
                        </div>

                        <div class="btnBuy">
                            <a class="btnBuy_link jsBuyButton" href="">Купить</a>
                        </div>
                    </div><!--/ купить продукт -->
                </div><!--/ продукт -->

                <div class="specialPriceItemFoot">
                    <a class="specialPriceItemFoot_link" href="">
                        <span class="specialPriceItemFoot_text">Похожие товары</span>
                    </a>
                </div><!--/ кнопка - похожие товары -->
            </div><!--/ блок спец-предложения -->

            <div class="specialPriceItem mLast">
                <div class="specialPriceItemTitle mSpec">Самым разборчивым</div><!--/ шапка, модификатор mSpec -->

                <div class="specialPriceItemCont">
                    <a class="specialPriceItemCont_imgLink" href="">
                        <img class="specialPriceItemCont_img" src="http://fs09.enter.ru/1/1/500/1c/30878.jpg" alt="" />
                    </a><!--/ картинка продукта -->

                    <a class="specialPriceItemCont_name" href="">
                        <span class="catName">Стиральная машина</span>
                        <strong class="prodName">Samsung WF8590NMW9</strong>
                    </a><!--/ название продукта -->

                    <p class="specialPriceItemCont_desc">
                        Стиральная машина с максимальной загрузкой белья 6 кг и скоростью вращения 1000 об/мин имеет 5 программ стирки и керамическим нагревателем против накипи.
                    </p><!--/ описание продукта -->

                    <div class="specialPriceItemCont_buy">
                        <div class="priceBox">
                            <span class="priceBox_old"> 
                                <span class="price">36 490</span> <span class="rubl">p</span> 
                                <span class="persent">5%</span>
                            </span>

                            <span class="priceBox_new"><strong>6 990</strong> <span class="rubl">p</span></span>
                        </div>

                        <div class="btnBuy">
                            <a class="btnBuy_link jsBuyButton" href="">Купить</a>
                        </div>
                    </div><!--/ купить продукт -->
                </div><!--/ продукт -->

                <div class="specialPriceItemFoot">
                    <a class="specialPriceItemFoot_link" href="">
                        <span class="specialPriceItemFoot_text">Похожие товары</span>
                    </a>
                </div><!--/ кнопка - похожие товары -->
            </div><!--/ блок спец-предложения -->
        </div><!--/ Спец-предложения -->

        <div class="specialBorderBox">
            <!-- Сюда нужно вывести реальный слайдер и передать мне на доработку -->
            <div class="bSlider">
                <div class="bSlider__eInner">
                    <ul class="bSlider__eList clearfix" style="width: 1200px; left: 0px;">
                            <li data-product="{&quot;article&quot;:&quot;457-1460&quot;,&quot;name&quot;:&quot;\u0421\u0443\u0448\u0438\u043b\u044c\u043d\u0430\u044f \u043c\u0430\u0448\u0438\u043d\u0430 Siemens WT46S515OE&quot;,&quot;isUpsale&quot;:false}" data-category="slider-53426f386d066-category-3789" class="bSlider__eItem jsSliderItem" style="display: list-item;">
                            <div class="product__inner">
                                                        <a href="/product/appliances/sushilnaya-mashina-siemens-wt46s515oe-2020201002755?sender=enter|40489" class="productImg"><img alt="Сушильная машина Siemens WT46S515OE" src="http://fs10.enter.ru/1/1/120/d3/87292.jpg"></a>
                                <div class="productName"><a href="/product/appliances/sushilnaya-mashina-siemens-wt46s515oe-2020201002755?sender=enter|40489">Сушильная машина Siemens WT46S515OE</a></div>
                                <div class="productPrice"><span class="price">29 090 <span class="rubl">p</span></span></div>

                                    <div class="bWidgetBuy__eBuy btnBuy">
                <a data-upsale="{&quot;url&quot;:&quot;\/ajax\/upsale\/40489&quot;,&quot;fromUpsale&quot;:false}" data-group="40489" class="id-cartButton-product-40489 btnBuy__eLink jsBuyButton" href="/cart/add-product/40489">Купить</a>
            </div>

                            </div>
                        </li>
                                                <li data-product="{&quot;article&quot;:&quot;458-0496&quot;,&quot;name&quot;:&quot;\u0421\u0443\u0448\u0438\u043b\u044c\u043d\u0430\u044f \u043c\u0430\u0448\u0438\u043d\u0430 Bosch WTC 84102&quot;,&quot;isUpsale&quot;:false}" data-category="slider-53426f386d066-category-3789" class="bSlider__eItem jsSliderItem" style="display: list-item;">
                            <div class="product__inner">
                                                        <a href="/product/appliances/sushilnaya-mashina-bosch-wtc-84102-2020201003530?sender=enter|46159" class="productImg"><img alt="Сушильная машина Bosch WTC 84102" src="http://fs10.enter.ru/1/1/120/65/108864.jpg"></a>
                                <div class="productName"><a href="/product/appliances/sushilnaya-mashina-bosch-wtc-84102-2020201003530?sender=enter|46159">Сушильная машина Bosch WTC 84102</a></div>
                                <div class="productPrice"><span class="price">16 190 <span class="rubl">p</span></span></div>

                                    <div class="bWidgetBuy__eBuy btnBuy">
                <a data-upsale="{&quot;url&quot;:&quot;\/ajax\/upsale\/46159&quot;,&quot;fromUpsale&quot;:false}" data-group="46159" class="id-cartButton-product-46159 btnBuy__eLink jsBuyButton" href="/cart/add-product/46159">Купить</a>
            </div>

                            </div>
                        </li>
                                                <li data-product="{&quot;article&quot;:&quot;460-7521&quot;,&quot;name&quot;:&quot;\u0427\u0438\u0441\u0442\u044f\u0449\u0435\u0435 \u0441\u0440\u0435\u0434\u0441\u0442\u0432\u043e \u043e\u0442 \u043d\u0430\u043a\u0438\u043f\u0438 \u0438 \u0438\u0437\u0432\u0435\u0441\u0442\u0438 \u0434\u043b\u044f \u0441\u0442\u0438\u0440\u0430\u043b\u044c\u043d\u044b\u0445 \u0438 \u043f\u043e\u0441\u0443\u0434\u043e\u043c\u043e\u0435\u0447\u043d\u044b\u0445 \u043c\u0430\u0448\u0438\u043d Heitmann, 175 \u043c\u043b&quot;,&quot;isUpsale&quot;:false}" data-category="slider-53426f386d066-category-3833" class="bSlider__eItem jsSliderItem" style="display: list-item;">
                            <div class="product__inner">
                                                        <a href="/product/household/chistyashchee-sredstvo-ot-nakipi-i-izvesti-dlya-stiralnih-i-posudomoechnih-mashin-heitmann-175-ml-2040201009226?sender=enter|69236" class="productImg"><img alt="Чистящее средство от накипи и извести для стиральных и посудомоечных машин Heitmann, 175 мл" src="http://fs07.enter.ru/1/1/120/ad/137169.jpg"></a>
                                <div class="productName"><a href="/product/household/chistyashchee-sredstvo-ot-nakipi-i-izvesti-dlya-stiralnih-i-posudomoechnih-mashin-heitmann-175-ml-2040201009226?sender=enter|69236">Чистящее средство от накипи и извести для стиральных и посудомоечных машин Heitmann, 175 мл</a></div>
                                <div class="productPrice"><span class="price">205 <span class="rubl">p</span></span></div>

                                    <div class="bWidgetBuy__eBuy btnBuy">
                <a data-upsale="{&quot;url&quot;:&quot;\/ajax\/upsale\/69236&quot;,&quot;fromUpsale&quot;:false}" data-group="69236" class="id-cartButton-product-69236 btnBuy__eLink jsBuyButton" href="/cart/add-product/69236">Купить</a>
            </div>

                            </div>
                        </li>
                                                <li data-product="{&quot;article&quot;:&quot;461-0341&quot;,&quot;name&quot;:&quot;\u041a\u043e\u043d\u0446\u0435\u043d\u0442\u0440\u0438\u0440\u043e\u0432\u0430\u043d\u043d\u044b\u0439 \u0441\u0442\u0438\u0440\u0430\u043b\u044c\u043d\u044b\u0439 \u043f\u043e\u0440\u043e\u0448\u043e\u043a \u0441 \u043e\u0442\u0431\u0435\u043b\u0438\u0432\u0430\u0442\u0435\u043b\u0435\u043c Frosch \u0022\u0426\u0438\u0442\u0440\u0443\u0441\u0022, 1,35 \u043a\u0433&quot;,&quot;isUpsale&quot;:false}" data-category="slider-53426f386d066-category-3831" class="bSlider__eItem jsSliderItem" style="display: list-item;">
                            <div class="product__inner">
                                                        <a href="/product/household/kontsentrirovanniy-stiralniy-poroshok-s-otbelivatelem-frosch-tsitrus-135-kg-2040201010161?sender=enter|71651" class="productImg"><img alt="Концентрированный стиральный порошок с отбеливателем Frosch &quot;Цитрус&quot;, 1,35 кг" src="http://fs02.enter.ru/1/1/120/9c/136617.jpg"></a>
                                <div class="productName"><a href="/product/household/kontsentrirovanniy-stiralniy-poroshok-s-otbelivatelem-frosch-tsitrus-135-kg-2040201010161?sender=enter|71651">Концентрированный стиральный порошок с отбеливателем Frosch "Цитрус", 1,35 кг</a></div>
                                <div class="productPrice"><span class="price">448 <span class="rubl">p</span></span></div>

                                    <div class="bWidgetBuy__eBuy btnBuy">
                <a data-upsale="{&quot;url&quot;:&quot;\/ajax\/upsale\/71651&quot;,&quot;fromUpsale&quot;:false}" data-group="71651" class="id-cartButton-product-71651 btnBuy__eLink jsBuyButton" href="/cart/add-product/71651">Купить</a>
            </div>

                            </div>
                        </li>
                                                <li data-product="{&quot;article&quot;:&quot;461-0342&quot;,&quot;name&quot;:&quot;\u0421\u0442\u0438\u0440\u0430\u043b\u044c\u043d\u044b\u0439 \u043f\u043e\u0440\u043e\u0448\u043e\u043a \u0434\u043b\u044f \u0446\u0432\u0435\u0442\u043d\u043e\u0433\u043e \u0431\u0435\u043b\u044c\u044f Frosch, 1,35 \u043a\u0433&quot;,&quot;isUpsale&quot;:false}" data-category="slider-53426f386d066-category-3831" class="bSlider__eItem jsSliderItem" style="display: list-item;">
                            <div class="product__inner">
                                                        <a href="/product/household/stiralniy-poroshok-dlya-tsvetnogo-belya-frosch-135-kg-2040201010178?sender=enter|71655" class="productImg"><img alt="Стиральный порошок для цветного белья Frosch, 1,35 кг" src="http://fs06.enter.ru/1/1/120/33/136628.jpg"></a>
                                <div class="productName"><a href="/product/household/stiralniy-poroshok-dlya-tsvetnogo-belya-frosch-135-kg-2040201010178?sender=enter|71655">Стиральный порошок для цветного белья Frosch, 1,35 кг</a></div>
                                <div class="productPrice"><span class="price">379 <span class="rubl">p</span></span></div>

                                    <div class="bWidgetBuy__eBuy btnBuy">
                <a data-upsale="{&quot;url&quot;:&quot;\/ajax\/upsale\/71655&quot;,&quot;fromUpsale&quot;:false}" data-group="71655" class="id-cartButton-product-71655 btnBuy__eLink jsBuyButton" href="/cart/add-product/71655">Купить</a>
            </div>

                            </div>
                        </li>
                                                <li data-product="{&quot;article&quot;:&quot;461-0344&quot;,&quot;name&quot;:&quot;\u0416\u0438\u0434\u043a\u043e\u0435 \u0441\u0440\u0435\u0434\u0441\u0442\u0432\u043e \u0434\u043b\u044f \u0441\u0442\u0438\u0440\u043a\u0438 \u0441 \u0430\u0440\u043e\u043c\u0430\u0442\u043e\u043c \u043b\u0438\u043c\u043e\u043d\u0430 Frosch, 2 \u043b&quot;,&quot;isUpsale&quot;:false}" data-category="slider-53426f386d066-category-3831" class="bSlider__eItem jsSliderItem" style="display: list-item;">
                            <div class="product__inner">
                                                        <a href="/product/household/gidkoe-sredstvo-dlya-stirki-s-aromatom-limona-frosch-2-l-2040201010192?sender=enter|71659" class="productImg"><img alt="Жидкое средство для стирки с ароматом лимона Frosch, 2 л" src="http://fs10.enter.ru/1/1/120/31/136632.jpg"></a>
                                <div class="productName"><a href="/product/household/gidkoe-sredstvo-dlya-stirki-s-aromatom-limona-frosch-2-l-2040201010192?sender=enter|71659">Жидкое средство для стирки с ароматом лимона Frosch, 2 л</a></div>
                                <div class="productPrice"><span class="price">500 <span class="rubl">p</span></span></div>

                                    <div class="bWidgetBuy__eBuy btnBuy">
                <a data-upsale="{&quot;url&quot;:&quot;\/ajax\/upsale\/71659&quot;,&quot;fromUpsale&quot;:false}" data-group="71659" class="id-cartButton-product-71659 btnBuy__eLink jsBuyButton" href="/cart/add-product/71659">Купить</a>
            </div>

                            </div>
                        </li>
                                                <li data-product="{&quot;article&quot;:&quot;461-0346&quot;,&quot;name&quot;:&quot;\u0411\u0430\u043b\u044c\u0437\u0430\u043c \u0434\u043b\u044f \u0441\u0442\u0438\u0440\u043a\u0438 \u0448\u0435\u0440\u0441\u0442\u044f\u043d\u044b\u0445 \u0438 \u043d\u0435\u0436\u043d\u044b\u0445 \u0442\u043a\u0430\u043d\u0435\u0439 Frosch, 2 \u043b&quot;,&quot;isUpsale&quot;:false}" data-category="slider-53426f386d066-category-3831" class="bSlider__eItem jsSliderItem" style="display: list-item;">
                            <div class="product__inner">
                                                        <a href="/product/household/balzam-dlya-stirki-sherstyanih-i-negnih-tkaney-frosch-2-l-2040201010215?sender=enter|71663" class="productImg"><img alt="Бальзам для стирки шерстяных и нежных тканей Frosch, 2 л" src="http://fs04.enter.ru/1/1/120/91/136646.jpg"></a>
                                <div class="productName"><a href="/product/household/balzam-dlya-stirki-sherstyanih-i-negnih-tkaney-frosch-2-l-2040201010215?sender=enter|71663">Бальзам для стирки шерстяных и нежных тканей Frosch, 2 л</a></div>
                                <div class="productPrice"><span class="price">500 <span class="rubl">p</span></span></div>

                                    <div class="bWidgetBuy__eBuy btnBuy">
                <a data-upsale="{&quot;url&quot;:&quot;\/ajax\/upsale\/71663&quot;,&quot;fromUpsale&quot;:false}" data-group="71663" class="id-cartButton-product-71663 btnBuy__eLink jsBuyButton" href="/cart/add-product/71663">Купить</a>
            </div>

                            </div>
                        </li>
                                                <li data-product="{&quot;article&quot;:&quot;461-0347&quot;,&quot;name&quot;:&quot;\u0423\u043d\u0438\u0432\u0435\u0440\u0441\u0430\u043b\u044c\u043d\u043e\u0435 \u043a\u043e\u043d\u0446\u0435\u043d\u0442\u0440\u0438\u0440\u043e\u0432\u0430\u043d\u043d\u043e\u0435 \u0441\u0440\u0435\u0434\u0441\u0442\u0432\u043e \u0434\u043b\u044f \u0441\u0442\u0438\u0440\u043a\u0438 Frosch, 1,5 \u043b&quot;,&quot;isUpsale&quot;:false}" data-category="slider-53426f386d066-category-3831" class="bSlider__eItem jsSliderItem" style="display: list-item;">
                            <div class="product__inner">
                                                        <a href="/product/household/universalnoe-kontsentrirovannoe-sredstvo-dlya-stirki-frosch-15-l-2040201010222?sender=enter|71671" class="productImg"><img alt="Универсальное концентрированное средство для стирки Frosch, 1,5 л" src="http://fs02.enter.ru/1/1/120/4c/136723.jpg"></a>
                                <div class="productName"><a href="/product/household/universalnoe-kontsentrirovannoe-sredstvo-dlya-stirki-frosch-15-l-2040201010222?sender=enter|71671">Универсальное концентрированное средство для стирки Frosch, 1,5 л</a></div>
                                <div class="productPrice"><span class="price">457 <span class="rubl">p</span></span></div>

                                    <div class="bWidgetBuy__eBuy btnBuy">
                <a data-upsale="{&quot;url&quot;:&quot;\/ajax\/upsale\/71671&quot;,&quot;fromUpsale&quot;:false}" data-group="71671" class="id-cartButton-product-71671 btnBuy__eLink jsBuyButton" href="/cart/add-product/71671">Купить</a>
            </div>

                            </div>
                        </li>
                                                <li data-product="{&quot;article&quot;:&quot;461-0348&quot;,&quot;name&quot;:&quot;\u041a\u043e\u043d\u0446\u0435\u043d\u0442\u0440\u0438\u0440\u043e\u0432\u0430\u043d\u043d\u043e\u0435 \u0441\u0440\u0435\u0434\u0441\u0442\u0432\u043e \u0434\u043b\u044f \u0441\u0442\u0438\u0440\u043a\u0438 Frosch \u0022\u0410\u043b\u043e\u0435 \u0432\u0435\u0440\u0430\u0022, 1,5 \u043b&quot;,&quot;isUpsale&quot;:false}" data-category="slider-53426f386d066-category-3831" class="bSlider__eItem jsSliderItem" style="display: list-item;">
                            <div class="product__inner">
                                                        <a href="/product/household/kontsentrirovannoe-sredstvo-dlya-stirki-frosch-aloe-vera-15-l-2040201010239?sender=enter|71674" class="productImg"><img alt="Концентрированное средство для стирки Frosch &quot;Алое вера&quot;, 1,5 л" src="http://fs05.enter.ru/1/1/120/84/136729.jpg"></a>
                                <div class="productName"><a href="/product/household/kontsentrirovannoe-sredstvo-dlya-stirki-frosch-aloe-vera-15-l-2040201010239?sender=enter|71674">Концентрированное средство для стирки Frosch "Алое вера", 1,5 л</a></div>
                                <div class="productPrice"><span class="price">462 <span class="rubl">p</span></span></div>

                                    <div class="bWidgetBuy__eBuy btnBuy">
                <a data-upsale="{&quot;url&quot;:&quot;\/ajax\/upsale\/71674&quot;,&quot;fromUpsale&quot;:false}" data-group="71674" class="id-cartButton-product-71674 btnBuy__eLink jsBuyButton" href="/cart/add-product/71674">Купить</a>
            </div>

                            </div>
                        </li>
                                                <li data-product="{&quot;article&quot;:&quot;461-0349&quot;,&quot;name&quot;:&quot;\u0416\u0438\u0434\u043a\u043e\u0435 \u0441\u0440\u0435\u0434\u0441\u0442\u0432\u043e \u0434\u043b\u044f \u0441\u0442\u0438\u0440\u043a\u0438 Frosch \u0022\u0410\u043b\u043e\u0435 \u0432\u0435\u0440\u0430\u0022, 2 \u043b&quot;,&quot;isUpsale&quot;:false}" data-category="slider-53426f386d066-category-3831" class="bSlider__eItem jsSliderItem" style="display: list-item;">
                            <div class="product__inner">
                                                        <a href="/product/household/gidkoe-sredstvo-dlya-stirki-frosch-aloe-vera-2-l-2040201010246?sender=enter|71675" class="productImg"><img alt="Жидкое средство для стирки Frosch &quot;Алое вера&quot;, 2 л" src="http://fs06.enter.ru/1/1/120/56/136730.jpg"></a>
                                <div class="productName"><a href="/product/household/gidkoe-sredstvo-dlya-stirki-frosch-aloe-vera-2-l-2040201010246?sender=enter|71675">Жидкое средство для стирки Frosch "Алое вера", 2 л</a></div>
                                <div class="productPrice"><span class="price">500 <span class="rubl">p</span></span></div>

                                    <div class="bWidgetBuy__eBuy btnBuy">
                <a data-upsale="{&quot;url&quot;:&quot;\/ajax\/upsale\/71675&quot;,&quot;fromUpsale&quot;:false}" data-group="71675" class="id-cartButton-product-71675 btnBuy__eLink jsBuyButton" href="/cart/add-product/71675">Купить</a>
            </div>

                            </div>
                        </li>
                    </ul>
                </div>

                <div class="bSlider__eBtn mPrev mDisabled"><span></span></div>
                <div class="bSlider__eBtn mNext"><span></span></div>
            </div>
        </div>

        <?= $helper->render('product/__listAction', [
            'pager'          => $productPager,
            'productSorting' => $productSorting,
        ]) // сортировка, режим просмотра, режим листания ?>
    </div>

    <?= $helper->render('product/__list', [
        'pager'                  => $productPager,
        'view'                   => $productView,
        'productVideosByProduct' => $productVideosByProduct,
        'bannerPlaceholder'      => !empty($catalogJson['bannerPlaceholder']) && 'jewel' !== $listingStyle ? $catalogJson['bannerPlaceholder'] : [],
        'listingStyle'           => $listingStyle,
    ]) // листинг ?>

    <div class="bSortingLine mPagerBottom clearfix">
        <?= $helper->render('product/__pagination', ['pager' => $productPager]) // листалка ?>
    </div>

    <? if(!empty($seoContent)): ?>
        <div class="bSeoText">
            <?= $seoContent ?>
        </div>
    <? endif ?>
</div>