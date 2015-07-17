<?
/**
 * @var $page                   \View\ProductCategory\LeafPage
 * @var $category               \Model\Product\Category\Entity
 * @var $brand                  \Model\Brand\Entity|null
 * @var $productFilter          \Model\Product\Filter
 * @var $productPager           \Iterator\EntityPager
 * @var $productSorting         \Model\Product\Sorting
 * @var $productView            string
 * @var $hotlinks               array
 * @var $seoContent             string
 * @var $relatedCategories      array
 * @var $categoryConfigById     array
 * @var $slideData              array
 */
?>
<!-- для внутренних страниц добавляется класс middle_transform -->
<div class="middle middle_transform">
    <div class="container">
        <main class="content">
            <div class="content__inner">
                <!-- баннер -->
                <div class="banner-section">
                    <img src="http://content.adfox.ru/150713/adfox/176461/1346077.jpg" width="940" height="240" alt="" border="0">
                </div>
                <!--/ баннер -->

                <div class="section">
                    <ul class="bread-crumbs">
                        <li class="bread-crumbs__item"><a href="" class="bread-crumbs__link underline">Мебель</a></li>
                        <li class="bread-crumbs__item"><a href="" class="bread-crumbs__link underline">Мягкая мебель</a></li>
                        <li class="bread-crumbs__item">Кресла</li>
                    </ul>

                    <ul class="categories-grid grid-3col">

                        <? foreach ($category->getChild() as $childCategory) : ?>

                            <li class="categories-grid__item grid-3col__item">
                                <a href="<?= $childCategory->getLink() ?>" class="categories-grid__link">
                                    <span class="categories-grid__img">
                                        <img src="<?= $childCategory->getImageUrl() ?>" alt="" class="image">
                                    </span>

                                    <span class="categories-grid__text"><?= $childCategory->getName() ?></span>
                                </a>
                            </li>

                        <? endforeach ?>

                    </ul>
                </div>

                <hr class="hr-orange">

                <div class="filter filter-options fltr">
                    <form id="productCatalog-filter-form" class="fltrSet js-category-filter" action="/catalog/children/disney-baby-5854" method="GET">
                        <div class="fltrSet_tggl js-category-filter-otherParamsToggleButton">
                            <span class="fltrSet_tggl_tx">Бренды и параметры</span>
                        </div>

                        <div class="fltrSet_cnt js-category-v1-filter-otherParams">
                            <!-- Фильтр по выбранным параметрам -->
                            <div class="filter-content js-category-filter-otherParamsContent">
                                <!-- Список названий параметров -->
                                <ul class="filter-params">
                                    <li class="filter-params__item js-category-filter-param" data-ref="">
                                        <span class="filter-params__text">WOW-товары</span>
                                    </li>
                                    <li class="filter-params__item js-category-filter-param mActive" data-ref="">
                                        <span class="filter-params__text">Бренд</span>
                                    </li>
                                    <li class="filter-params__item js-category-filter-param" data-ref="">
                                        <span class="filter-params__text">Наличие в магазинах</span>
                                    </li>
                                </ul>
                                <!-- /Список названий параметров -->

                                <!-- Список значений параметров -->
                                <div class="filter-values">
                                    <div class="filter-values__inner">
                                        <div class="filter-values__item js-category-filter-element" id="id-productCategory-filter-5-label" style="display: none;">
                                            <div class="filter-values__cell">
                                                <input class="custom-input filter-check jsCustomRadio js-customInput " type="checkbox" id="id-productCategory-filter-label-option-36" name="f-label-sale" value="36">
                                                <label class="custom-label" for="id-productCategory-filter-label-option-36">
                                                    Sale
                                                </label>
                                            </div>
                                        </div>

                                        <div class="filter-values__item hf js-category-filter-element" id="id-productCategory-filter-5-brand" style="display: block;">
                                            <div class="filter-values__cell">
                                                <input class="custom-input filter-check jsCustomRadio js-customInput js-category-filter-brand" type="checkbox" id="id-productCategory-filter-brand-option-106" name="f-brand-disney" value="106" data-name="Disney">
                                                <label class="custom-label" for="id-productCategory-filter-brand-option-106">
                                                    Disney
                                                </label>
                                            </div>

                                            <div class="filter-values__cell">
                                                <input class="custom-input filter-check jsCustomRadio js-customInput js-category-filter-brand" type="checkbox" id="id-productCategory-filter-brand-option-179" name="f-brand-huggies" value="179" data-name="Huggies">
                                                <label class="custom-label" for="id-productCategory-filter-brand-option-179">
                                                    Huggies
                                                </label>
                                            </div>

                                            <div class="filter-values__cell">
                                                <input class="custom-input filter-check jsCustomRadio js-customInput js-category-filter-brand" type="checkbox" id="id-productCategory-filter-brand-option-278" name="f-brand-nuk" value="278" data-name="Nuk">
                                                <label class="custom-label" for="id-productCategory-filter-brand-option-278">
                                                    Nuk
                                                </label>
                                            </div>
                                        </div>

                                        <div class="filter-values__item hf mLineItem js-category-filter-element" id="id-productCategory-filter-5-shop" style="display: none;">
                                            <div class="filter-values__cell">
                                                <input class="custom-input filter-radio jsCustomRadio js-customInput " type="radio" id="id-productCategory-filter-shop-option-8" name="shop" value="8">
                                                <label class="custom-label mCustomLabelRadio" for="id-productCategory-filter-shop-option-8">
                                                    г. Сергиев Посад, ул. 1-я Рыбная, д. 19/22
                                                </label>
                                            </div>

                                            <div class="filter-values__cell">
                                                <input class="custom-input filter-radio jsCustomRadio js-customInput " type="radio" id="id-productCategory-filter-shop-option-23" name="shop" value="23">
                                                <label class="custom-label mCustomLabelRadio" for="id-productCategory-filter-shop-option-23">
                                                    г. Щелково, ул. Талсинская, д. 2
                                                </label>
                                            </div>

                                            <div class="filter-values__cell">
                                                <input class="custom-input filter-radio jsCustomRadio js-customInput " type="radio" id="id-productCategory-filter-shop-option-40" name="shop" value="40">
                                                <label class="custom-label mCustomLabelRadio" for="id-productCategory-filter-shop-option-40">
                                                    г. Серпухов, ул. Ворошилова, д. 113
                                                </label>
                                            </div>

                                            <div class="filter-values__cell">
                                                <input class="custom-input filter-radio jsCustomRadio js-customInput " type="radio" id="id-productCategory-filter-shop-option-83" name="shop" value="83">
                                                <label class="custom-label mCustomLabelRadio" for="id-productCategory-filter-shop-option-83">
                                                    г. Подольск, ул. Б. Серпуховская, д. 5, ТЦ "Центральный"
                                                </label>
                                            </div>

                                            <div class="filter-values__cell">
                                                <input class="custom-input filter-radio jsCustomRadio js-customInput " type="radio" id="id-productCategory-filter-shop-option-86" name="shop" value="86">
                                                <label class="custom-label mCustomLabelRadio" for="id-productCategory-filter-shop-option-86">
                                                    г. Электросталь, пр-кт Ленина, д. 30/13
                                                </label>
                                            </div>

                                            <div class="filter-values__cell">
                                                <input class="custom-input filter-radio jsCustomRadio js-customInput " type="radio" id="id-productCategory-filter-shop-option-128" name="shop" value="128">
                                                <label class="custom-label mCustomLabelRadio" for="id-productCategory-filter-shop-option-128">
                                                    г. Коломна, ул. Октябрьской революции, д. 368
                                                </label>
                                            </div>

                                            <div class="filter-values__cell">
                                                <input class="custom-input filter-radio jsCustomRadio js-customInput " type="radio" id="id-productCategory-filter-shop-option-149" name="shop" value="149">
                                                <label class="custom-label mCustomLabelRadio" for="id-productCategory-filter-shop-option-149">
                                                    г. Орехово-Зуево, ул. Ленина, д. 49
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="filter filter-components fltr">
                    <form id="productCatalog-filter-form" class="bFilter js-category-filter js-category-filter-v3" action="/catalog/jewel/zolotie-ukrasheniya-3299" method="GET">

                        <div class="fltrSet js-category-filter-toggle-container fltrSet-metall">
                            <div class="fltrSet_tggl js-category-filter-toggle-button">
                                <span class="fltrSet_tggl_tx">Металл</span>
                            </div>

                            <div class="fltrSet_cnt js-category-filter-toggle-content" style="display: none;">
                                <div class="fltrSet_inn">
                                    <div class="bFilterValuesCol bFilterValuesCol-gbox">
                                        <input class="customInput jsCustomRadio js-customInput " type="checkbox" id="id-productCategory-filter-prop2194-option-29502" name="f-prop2194-beloe_zoloto_585" value="29502">
                                        <label class="bFilterCheckbox" for="id-productCategory-filter-prop2194-option-29502">
                                            <span class="customLabel_wimg"></span>
                                            <img class="customLabel_bimg" src="http://0.imgenter.ru/uploads/media/fb/ed/c8/8750e48fe26f4e02ea5776914561ea5228f5490e.png">

                                            <span class="customLabel_btx">белое золото 585</span>
                                        </label>
                                    </div>

                                    <div class="bFilterValuesCol bFilterValuesCol-gbox">
                                        <input class="customInput jsCustomRadio js-customInput " type="checkbox" id="id-productCategory-filter-prop2194-option-29506" name="f-prop2194-geltoe_zoloto_585" value="29506">
                                        <label class="bFilterCheckbox" for="id-productCategory-filter-prop2194-option-29506">
                                            <span class="customLabel_wimg"></span>
                                            <img class="customLabel_bimg" src="http://f.imgenter.ru/uploads/media/f1/1e/59/38c72c637f05867d9d5649e96179e8f60fa52342.png">

                                            <span class="customLabel_btx">желтое золото 585</span>
                                        </label>
                                    </div>

                                    <div class="bFilterValuesCol bFilterValuesCol-gbox">
                                        <input class="customInput jsCustomRadio js-customInput " type="checkbox" id="id-productCategory-filter-prop2194-option-29503" name="f-prop2194-krasnoe_zoloto_375" value="29503">
                                        <label class="bFilterCheckbox" for="id-productCategory-filter-prop2194-option-29503">
                                            <span class="customLabel_wimg"></span>
                                            <img class="customLabel_bimg" src="http://8.imgenter.ru/uploads/media/ef/dd/f3/b1bd6e14dae63f5c47b60968666fc138bef759df.png">
                                            <span class="customLabel_btx">красное золото 375</span>
                                        </label>
                                    </div>

                                    <div class="bFilterValuesCol bFilterValuesCol-gbox">
                                        <input class="customInput jsCustomRadio js-customInput " type="checkbox" id="id-productCategory-filter-prop2194-option-29500" name="f-prop2194-krasnoe_zoloto_585" value="29500">
                                        <label class="bFilterCheckbox" for="id-productCategory-filter-prop2194-option-29500">
                                            <span class="customLabel_wimg"></span>
                                            <img class="customLabel_bimg" src="http://9.imgenter.ru/uploads/media/8c/df/8f/927818bd5e15c8ddcd9f17af8f7cd3b15c63635d.png">
                                            <span class="customLabel_btx">красное золото 585</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="fltrSet js-category-filter-toggle-container fltrSet-insertion fltrSet-close">
                            <div class="fltrSet_tggl js-category-filter-toggle-button">
                                <span class="fltrSet_tggl_tx">Вставка</span>
                            </div>

                            <div class="fltrSet_cnt js-category-filter-toggle-content" style="display: none;">
                                <div class="fltrSet_inn">

                                    <div class="bFilterValuesCol bFilterValuesCol-gbox">
                                        <input class="customInput jsCustomRadio js-customInput " type="checkbox" id="id-productCategory-filter-prop7451-option-14381" name="f-prop7451-agat" value="14381">
                                        <label class="bFilterCheckbox" for="id-productCategory-filter-prop7451-option-14381">
                                            <span class="customLabel_wimg"></span>
                                            <img class="customLabel_bimg" src="http://2.imgenter.ru/uploads/media/be/4a/e9/b76078ce6a186de0b8e6bdd1c582f846e1831e6b.png">

                                            <span class="customLabel_btx">агат</span>
                                        </label>
                                    </div>

                                    <div class="bFilterValuesCol bFilterValuesCol-gbox">
                                        <input class="customInput jsCustomRadio js-customInput " type="checkbox" id="id-productCategory-filter-prop7451-option-14382" name="f-prop7451-ametist" value="14382">
                                        <label class="bFilterCheckbox" for="id-productCategory-filter-prop7451-option-14382">
                                            <span class="customLabel_wimg"></span>
                                            <img class="customLabel_bimg" src="http://c.imgenter.ru/uploads/media/ae/b3/2b/2cb15804f1ac6129aace4e69892f0040de7e5a9c.png">

                                            <span class="customLabel_btx">аметист</span>
                                        </label>
                                    </div>

                                    <div class="bFilterValuesCol bFilterValuesCol-gbox">
                                        <input class="customInput jsCustomRadio js-customInput " type="checkbox" id="id-productCategory-filter-prop7451-option-14383" name="f-prop7451-biryuza" value="14383">
                                        <label class="bFilterCheckbox" for="id-productCategory-filter-prop7451-option-14383">
                                            <span class="customLabel_wimg"></span>
                                            <img class="customLabel_bimg" src="http://e.imgenter.ru/uploads/media/35/6b/8e/f4a4af695b4e373c5ada1683bb9846912274206c.png">

                                            <span class="customLabel_btx">бирюза</span>
                                        </label>
                                    </div>

                                    <div class="bFilterValuesCol bFilterValuesCol-gbox">
                                        <input class="customInput jsCustomRadio js-customInput " type="checkbox" id="id-productCategory-filter-prop7451-option-16618" name="f-prop7451-brilliant" value="16618">
                                        <label class="bFilterCheckbox" for="id-productCategory-filter-prop7451-option-16618">
                                            <span class="customLabel_wimg"></span>
                                            <img class="customLabel_bimg" src="http://8.imgenter.ru/uploads/media/70/e0/5e/03062588d11501d8242bc4f7784d70b042ca9312.png">

                                            <span class="customLabel_btx">бриллиант</span>
                                        </label>
                                    </div>

                                    <div class="bFilterValuesCol bFilterValuesCol-gbox">
                                        <input class="customInput jsCustomRadio js-customInput " type="checkbox" id="id-productCategory-filter-prop7451-option-14384" name="f-prop7451-granat" value="14384">
                                        <label class="bFilterCheckbox" for="id-productCategory-filter-prop7451-option-14384">
                                            <span class="customLabel_wimg"></span>
                                            <img class="customLabel_bimg" src="http://9.imgenter.ru/uploads/media/16/1f/25/94a46fb68a2aaaec3f2e52b27896b53cecb16762.png">

                                            <span class="customLabel_btx">гранат</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="fltrSet">
                            <div class="fltrSet_tggl js-category-filter-otherParamsToggleButton">
                                <span class="fltrSet_tggl_tx">Ещё параметры</span>
                            </div>

                            <div class="fltrSet_cnt js-category-v1-filter-otherParams">
                                <!-- Фильтр по выбранным параметрам -->
                                <div class="filter-content js-category-filter-otherParamsContent">
                                    <!-- Список названий параметров -->
                                    <ul class="filter-params">
                                        <li class="filter-params__item js-category-filter-param" data-ref="">
                                            <span class="filter-params__text">WOW-товары</span>
                                        </li>
                                        <li class="filter-params__item js-category-filter-param mActive" data-ref="">
                                            <span class="filter-params__text">Бренд</span>
                                        </li>
                                        <li class="filter-params__item js-category-filter-param" data-ref="">
                                            <span class="filter-params__text">Наличие в магазинах</span>
                                        </li>
                                        <li class="filter-params__item js-category-filter-param" data-ref="">
                                            <span class="filter-params__text">WOW-товары</span>
                                        </li>
                                        <li class="filter-params__item js-category-filter-param" data-ref="">
                                            <span class="filter-params__text">Бренд</span>
                                        </li>
                                        <li class="filter-params__item js-category-filter-param" data-ref="">
                                            <span class="filter-params__text">Наличие в магазинах</span>
                                        </li>
                                    </ul>
                                    <!-- /Список названий параметров -->

                                    <!-- Список значений параметров -->
                                    <div class="filter-values">
                                        <div class="filter-values__inner">
                                            <div class="filter-values__item js-category-filter-element" id="id-productCategory-filter-5-label" style="display: none;">
                                                <div class="filter-values__cell">
                                                    <input class="custom-input filter-check jsCustomRadio js-customInput " type="checkbox" id="id-productCategory-filter-label-option-36" name="f-label-sale" value="36">
                                                    <label class="custom-label" for="id-productCategory-filter-label-option-36">
                                                        Sale
                                                    </label>
                                                </div>
                                            </div>

                                            <div class="filter-values__item hf js-category-filter-element" id="id-productCategory-filter-5-brand" style="display: block;">
                                                <div class="filter-values__cell">
                                                    <input class="custom-input filter-check jsCustomRadio js-customInput js-category-filter-brand" type="checkbox" id="id-productCategory-filter-brand-option-106" name="f-brand-disney" value="106" data-name="Disney">
                                                    <label class="custom-label" for="id-productCategory-filter-brand-option-106">
                                                        Disney
                                                    </label>
                                                </div>

                                                <div class="filter-values__cell">
                                                    <input class="custom-input filter-check jsCustomRadio js-customInput js-category-filter-brand" type="checkbox" id="id-productCategory-filter-brand-option-179" name="f-brand-huggies" value="179" data-name="Huggies">
                                                    <label class="custom-label" for="id-productCategory-filter-brand-option-179">
                                                        Huggies
                                                    </label>
                                                </div>

                                                <div class="filter-values__cell">
                                                    <input class="custom-input filter-check jsCustomRadio js-customInput js-category-filter-brand" type="checkbox" id="id-productCategory-filter-brand-option-278" name="f-brand-nuk" value="278" data-name="Nuk">
                                                    <label class="custom-label" for="id-productCategory-filter-brand-option-278">
                                                        Nuk
                                                    </label>
                                                </div>
                                            </div>

                                            <div class="filter-values__item hf mLineItem js-category-filter-element" id="id-productCategory-filter-5-shop" style="display: none;">
                                                <div class="filter-values__cell">
                                                    <input class="custom-input filter-radio jsCustomRadio js-customInput " type="radio" id="id-productCategory-filter-shop-option-8" name="shop" value="8">
                                                    <label class="custom-label mCustomLabelRadio" for="id-productCategory-filter-shop-option-8">
                                                        г. Сергиев Посад, ул. 1-я Рыбная, д. 19/22
                                                    </label>
                                                </div>

                                                <div class="filter-values__cell">
                                                    <input class="custom-input filter-radio jsCustomRadio js-customInput " type="radio" id="id-productCategory-filter-shop-option-23" name="shop" value="23">
                                                    <label class="custom-label mCustomLabelRadio" for="id-productCategory-filter-shop-option-23">
                                                        г. Щелково, ул. Талсинская, д. 2
                                                    </label>
                                                </div>

                                                <div class="filter-values__cell">
                                                    <input class="custom-input filter-radio jsCustomRadio js-customInput " type="radio" id="id-productCategory-filter-shop-option-40" name="shop" value="40">
                                                    <label class="custom-label mCustomLabelRadio" for="id-productCategory-filter-shop-option-40">
                                                        г. Серпухов, ул. Ворошилова, д. 113
                                                    </label>
                                                </div>

                                                <div class="filter-values__cell">
                                                    <input class="custom-input filter-radio jsCustomRadio js-customInput " type="radio" id="id-productCategory-filter-shop-option-83" name="shop" value="83">
                                                    <label class="custom-label mCustomLabelRadio" for="id-productCategory-filter-shop-option-83">
                                                        г. Подольск, ул. Б. Серпуховская, д. 5, ТЦ "Центральный"
                                                    </label>
                                                </div>

                                                <div class="filter-values__cell">
                                                    <input class="custom-input filter-radio jsCustomRadio js-customInput " type="radio" id="id-productCategory-filter-shop-option-86" name="shop" value="86">
                                                    <label class="custom-label mCustomLabelRadio" for="id-productCategory-filter-shop-option-86">
                                                        г. Электросталь, пр-кт Ленина, д. 30/13
                                                    </label>
                                                </div>

                                                <div class="filter-values__cell">
                                                    <input class="custom-input filter-radio jsCustomRadio js-customInput " type="radio" id="id-productCategory-filter-shop-option-128" name="shop" value="128">
                                                    <label class="custom-label mCustomLabelRadio" for="id-productCategory-filter-shop-option-128">
                                                        г. Коломна, ул. Октябрьской революции, д. 368
                                                    </label>
                                                </div>

                                                <div class="filter-values__cell">
                                                    <input class="custom-input filter-radio jsCustomRadio js-customInput " type="radio" id="id-productCategory-filter-shop-option-149" name="shop" value="149">
                                                    <label class="custom-label mCustomLabelRadio" for="id-productCategory-filter-shop-option-149">
                                                        г. Орехово-Зуево, ул. Ленина, д. 49
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="filter filter-brands fltrBtn fltrBtn-bt">
                    <form id="productCatalog-filter-form" class="js-category-filter" action="/catalog/electronics/telefoni-897" data-count-url="" method="GET">
                        <div class="fltrBtn_kit fltrBtn_kit--mark">
                            <div class="fltrBtn_tggl fltrBtn_kit_l">
                                <i class="fltrBtn_tggl_corner icon-corner"></i>
                                <span class="dotted">Бренд</span>
                            </div>

                            <div class="fltrBtn_kit_r ">
                                <div class="fltrBtn_i bFilterValuesCol-gbox">
                                    <input class="custom-input customInput-btn jsCustomRadio js-customInput js-category-filter-brand js-category-v2-filter-brand" type="checkbox" id="id-productCategory-filter-brand-option-566" name="f-brand-panasonic" value="566" data-name="Panasonic">
                                    <label class="fltrBtn_btn" for="id-productCategory-filter-brand-option-566">
                                        <img class="fltrBtn_btn_img" src="http://8.imgenter.ru/uploads/media/ae/63/2d/393d44343d06f3ab2cd8564ca76d598c067e0a8f.png">
                                        <i class="fltrBtn_btn_clsr btn-closer1"></i>
                                    </label>
                                </div>

                                <div class="fltrBtn_i bFilterValuesCol-gbox">
                                    <input class="custom-input customInput-btn jsCustomRadio js-customInput js-category-filter-brand js-category-v2-filter-brand" type="checkbox" id="id-productCategory-filter-brand-option-566" name="f-brand-panasonic" value="566" data-name="Panasonic">
                                    <label class="fltrBtn_btn" for="id-productCategory-filter-brand-option-566">
                                        <img class="fltrBtn_btn_img" src="http://a.imgenter.ru/uploads/media/b4/fc/a6/63bd2f7a1be1eae1c0e67343ccc063dc6572efb1.png">
                                        <i class="fltrBtn_btn_clsr btn-closer1"></i>
                                    </label>
                                </div>

                                <div class="fltrBtn_i bFilterValuesCol-gbox">
                                    <input class="custom-input customInput-btn jsCustomRadio js-customInput js-category-filter-brand js-category-v2-filter-brand" type="checkbox" id="id-productCategory-filter-brand-option-566" name="f-brand-panasonic" value="566" data-name="Panasonic">
                                    <label class="fltrBtn_btn" for="id-productCategory-filter-brand-option-566">
                                        <img class="fltrBtn_btn_img" src="http://8.imgenter.ru/uploads/media/ae/63/2d/393d44343d06f3ab2cd8564ca76d598c067e0a8f.png">
                                        <i class="fltrBtn_btn_clsr btn-closer1"></i>
                                    </label>
                                </div>

                                <div class="fltrBtn_i bFilterValuesCol-gbox">
                                    <input class="custom-input customInput-btn jsCustomRadio js-customInput js-category-filter-brand js-category-v2-filter-brand" type="checkbox" id="id-productCategory-filter-brand-option-566" name="f-brand-panasonic" value="566" data-name="Panasonic">
                                    <label class="fltrBtn_btn" for="id-productCategory-filter-brand-option-566">
                                        <img class="fltrBtn_btn_img" src="http://a.imgenter.ru/uploads/media/b4/fc/a6/63bd2f7a1be1eae1c0e67343ccc063dc6572efb1.png">
                                        <i class="fltrBtn_btn_clsr btn-closer1"></i>
                                    </label>
                                </div>

                                <div class="fltrBtn_i bFilterValuesCol-gbox">
                                    <input class="custom-input customInput-btn jsCustomRadio js-customInput js-category-filter-brand js-category-v2-filter-brand" type="checkbox" id="id-productCategory-filter-brand-option-566" name="f-brand-panasonic" value="566" data-name="Panasonic">
                                    <label class="fltrBtn_btn" for="id-productCategory-filter-brand-option-566">
                                        <img class="fltrBtn_btn_img" src="http://8.imgenter.ru/uploads/media/ae/63/2d/393d44343d06f3ab2cd8564ca76d598c067e0a8f.png">
                                        <i class="fltrBtn_btn_clsr btn-closer1"></i>
                                    </label>
                                </div>

                                <div class="fltrBtn_i bFilterValuesCol-gbox">
                                    <input class="custom-input customInput-btn jsCustomRadio js-customInput js-category-filter-brand js-category-v2-filter-brand" type="checkbox" id="id-productCategory-filter-brand-option-566" name="f-brand-panasonic" value="566" data-name="Panasonic">
                                    <label class="fltrBtn_btn" for="id-productCategory-filter-brand-option-566">
                                        <img class="fltrBtn_btn_img" src="http://a.imgenter.ru/uploads/media/b4/fc/a6/63bd2f7a1be1eae1c0e67343ccc063dc6572efb1.png">
                                        <i class="fltrBtn_btn_clsr btn-closer1"></i>
                                    </label>
                                </div>

                                <a href="#" class="fltrBtn_btn fltrBtn_btn-btn js-category-v2-filter-otherBrandsOpener"><span class="dotted">Ещё 26</span></a>

                                    <span class="js-category-v2-filter-otherBrands" style="display: inline;">
                                        <div class="fltrBtn_i ">
                                            <input class="custom-input customInput-btn jsCustomRadio js-customInput js-category-filter-brand js-category-v2-filter-brand" type="checkbox" id="id-productCategory-filter-brand-option-4298" name="f-brand-jinga" value="4298" data-name="Jinga">
                                            <label class="fltrBtn_btn" for="id-productCategory-filter-brand-option-4298">
                                                <span class="fltrBtn_btn_tx">Jinga</span>
                                                <i class="fltrBtn_btn_clsr btn-closer1"></i>
                                            </label>
                                        </div>
                                    </span>
                            </div>
                        </div>

                        <div class="fltrBtn_kit fltrBtn_kit-box ">
                            <div class="fltrBtnBox fl-l js-category-v2-filter-dropBox js-category-v2-filter-dropBox-price">
                                <div class="fltrBtnBox_tggl js-category-v2-filter-dropBox-opener">
                                    <span class="fltrBtnBox_tggl_tx dotted">Цена</span>
                                    <i class="fltrBtnBox_tggl_corner icon-corder"></i>
                                </div>

                                <div class="fltrBtnBox_dd fltrBtnBox_dd-l">
                                    <ul class="fltrBtnBox_dd_inn lstdotted js-category-v2-filter-dropBox-content">
                                        <li class="lstdotted_i">
                                            <a class="dotted js-category-v2-filter-price-link" href="/catalog/electronics/telefoni-897?f-price-to=12800">
                                                <span class="txmark1">до</span> 12&thinsp;800
                                            </a>
                                        </li>

                                        <li class="lstdotted_i">
                                            <a class="dotted js-category-v2-filter-price-link" href="/catalog/electronics/telefoni-897?f-price-from=12801&amp;f-price-to=25200">
                                                <span class="txmark1">от</span> 12&thinsp;801
                                                <span class="txmark1">до</span> 25&thinsp;200
                                            </a>
                                        </li>

                                        <li class="lstdotted_i">
                                            <a class="dotted js-category-v2-filter-price-link" href="/catalog/electronics/telefoni-897?f-price-from=50001">
                                                <span class="txmark1">от</span> 50&thinsp;001
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="fltrBtn_kit fltrBtn_kit-box js-category-v2-filter-otherGroups">
                            <div class="fltrBtnBox  js-category-v2-filter-dropBox">
                                <div class="fltrBtnBox_tggl js-category-v2-filter-dropBox-opener">
                                    <span class="fltrBtnBox_tggl_tx dotted">В магазине</span>
                                    <i class="fltrBtnBox_tggl_corner icon-corder"></i>
                                </div>

                                <div class="fltrBtnBox_dd js-category-v2-filter-dropBox-content">
                                    <div class="fltrBtnBox_dd_inn">
                                        <div class="fltrBtn_param">
                                            <div class="fltrBtn_ln ">
                                                <input class="custom-input custom-input_radio js-category-v2-filter-element-list-radio jsCustomRadio js-customInput  js-category-v2-filter-element-shop-input" type="radio" id="id-productCategory-filter-shop-option-2" name="shop" value="2">
                                                <label class="custom-label" for="id-productCategory-filter-shop-option-2">
                                                    ул. Орджоникидзе, д. 11, стр. 10
                                                </label>
                                            </div>

                                            <div class="fltrBtn_ln ">
                                                <input class="custom-input custom-input_radio js-category-v2-filter-element-list-radio jsCustomRadio js-customInput  js-category-v2-filter-element-shop-input" type="radio" id="id-productCategory-filter-shop-option-13" name="shop" value="13">
                                                <label class="custom-label" for="id-productCategory-filter-shop-option-13">
                                                    Волгоградский пр-т, д. 119а.
                                                </label>
                                            </div>

                                            <div class="fltrBtn_ln ">
                                                <input class="custom-input custom-input_radio js-category-v2-filter-element-list-radio jsCustomRadio js-customInput  js-category-v2-filter-element-shop-input" type="radio" id="id-productCategory-filter-shop-option-68" name="shop" value="68">
                                                <label class="custom-label" for="id-productCategory-filter-shop-option-68">
                                                    Свободный пр-кт, д. 33
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="fltrBtnBox  js-category-v2-filter-dropBox">
                                <div class="fltrBtnBox_tggl js-category-v2-filter-dropBox-opener">
                                    <span class="fltrBtnBox_tggl_tx dotted">Платформа</span>
                                    <i class="fltrBtnBox_tggl_corner icon-corder"></i>
                                </div>

                                <div class="fltrBtnBox_dd js-category-v2-filter-dropBox-content">
                                    <div class="fltrBtnBox_dd_inn">
                                        <div class="fltrBtn_param">
                                            <div class="fltrBtn_ln ">
                                                <input class="custom-input custom-input_check2 js-category-v2-filter-element-list-checkbox jsCustomRadio js-customInput  " type="checkbox" id="id-productCategory-filter-prop3826-option-5337" name="f-prop3826-android" value="5337">
                                                <label class="custom-label" for="id-productCategory-filter-prop3826-option-5337">
                                                    Android
                                                </label>
                                            </div>

                                            <div class="fltrBtn_ln ">
                                                <input class="custom-input custom-input_check2 js-category-v2-filter-element-list-checkbox jsCustomRadio js-customInput  " type="checkbox" id="id-productCategory-filter-prop3826-option-29929" name="f-prop3826-android_4_0_ics" value="29929">
                                                <label class="custom-label" for="id-productCategory-filter-prop3826-option-29929">
                                                    Android 4.0 ICS
                                                </label>
                                            </div>

                                            <div class="fltrBtn_ln ">
                                                <input class="custom-input custom-input_check2 js-category-v2-filter-element-list-checkbox jsCustomRadio js-customInput  " type="checkbox" id="id-productCategory-filter-prop3826-option-29928" name="f-prop3826-android_4_1_jelly_bean" value="29928">
                                                <label class="custom-label" for="id-productCategory-filter-prop3826-option-29928">
                                                    Android 4.1 Jelly Bean
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="fltrBtnBox  js-category-v2-filter-dropBox">
                                <div class="fltrBtnBox_tggl js-category-v2-filter-dropBox-opener">
                                    <span class="fltrBtnBox_tggl_tx dotted">Дисплей</span>
                                    <i class="fltrBtnBox_tggl_corner icon-corder"></i>
                                </div>

                                <div class="fltrBtnBox_dd js-category-v2-filter-dropBox-content">
                                    <div class="fltrBtnBox_dd_inn">
                                        <div class="fltrBtn_param">
                                            <div class="fltrBtn_param_n">Диагональ экрана</div>

                                            <div class="fltrBtn_ln js-category-v2-filter-element-number">
                                                <span class="fltrBtn_param_lbl txmark1">от</span> <input class="fltrBtn_param_it js-category-v2-filter-element-number-from" name="" value="" placeholder="1.4" type="text">
                                                &ensp;<span class="fltrBtn_param_lbl txmark1">до</span> <input class="fltrBtn_param_it js-category-v2-filter-element-number-to" name="" value="" placeholder="6" type="text">
                                                <span class="fltrBtn_param_lbl txmark1">"</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="fltrBtn_kit fltrBtn_kit-nborder">
                            <div class="js-category-filter-selected">
                                <ul class="fltr_slctd">
                                    <li class="fltr_slctd_i fltr_slctd_i-n">В магазине:</li>

                                    <li class="fltr_slctd_i">
                                        <span>ул. Орджоникидзе, д. 11, стр. 10</span>
                                        <a class="btn-closer2 jsHistoryLink" href=""></a>
                                    </li>
                                </ul>

                                <ul class="fltr_slctd">
                                    <li class="fltr_slctd_i fltr_slctd_i-n">Память:</li>

                                    <li class="fltr_slctd_i">
                                        Встроенная память

                                        <span>от 5 ГБ</span>
                                        <a class="btn-closer2 jsHistoryLink" href=""></a>
                                    </li>
                                </ul>

                                <ul class="fltr_slctd">
                                    <li class="fltr_slctd_i fltr_slctd_i-n">Платформа:</li>

                                    <li class="fltr_slctd_i">
                                        <span>Android 4.1 Jelly Bean</span>
                                        <a class="btn-closer2 jsHistoryLink" href=""></a>
                                    </li>
                                </ul>

                                <a class="fltr_clsr jsHistoryLink" href="">
                                    <span class="btn-closer3"></span>
                                    <span class="dotted">Очистить все</span>
                                </a>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="sorting sorting-top js-category-sortingAndPagination">
                    <!-- Сортировка товаров по параметрам -->
                    <ul class="sorting_lst fl-l js-category-sorting">
                        <li class="sorting_i sorting_i-tl">Сортировать</li>

                        <li class="sorting_i act js-category-sorting-activeItem js-category-sorting-defaultItem js-category-sorting-item">
                            <a class="sorting_lk jsSorting" data-sort="default-desc" href="/catalog/electronics/kompyuteri-i-plansheti-plansheti-817?shop=87&amp;f-prop9396-from=5&amp;f-prop3826-android_4_1_jelly_bean=29928">Автоматически</a>
                        </li>
                        <li class="sorting_i js-category-sorting-item">
                            <a class="sorting_lk jsSorting" data-sort="hits-desc" href="/catalog/electronics/kompyuteri-i-plansheti-plansheti-817?shop=87&amp;f-prop9396-from=5&amp;f-prop3826-android_4_1_jelly_bean=29928&amp;sort=hits-desc">Хиты продаж</a>
                        </li>
                        <li class="sorting_i js-category-sorting-item">
                            <a class="sorting_lk jsSorting" data-sort="price-asc" href="/catalog/electronics/kompyuteri-i-plansheti-plansheti-817?shop=87&amp;f-prop9396-from=5&amp;f-prop3826-android_4_1_jelly_bean=29928&amp;sort=price-asc">По цене ▲</a>
                        </li>
                        <li class="sorting_i js-category-sorting-item">
                            <a class="sorting_lk jsSorting" data-sort="price-desc" href="/catalog/electronics/kompyuteri-i-plansheti-plansheti-817?shop=87&amp;f-prop9396-from=5&amp;f-prop3826-android_4_1_jelly_bean=29928&amp;sort=price-desc">По цене ▼</a>
                        </li>
                    </ul>

                    <ul class="sorting_lst fl-r js-category-pagination">
                        <li class="sorting_i sorting_i-tl">Страницы</li>

                        <li class="sorting_i act js-category-pagination-activePage js-category-pagination-page">
                            <a class="sorting_lk sorting_lk-page jsPagination" href="#">1</a>
                        </li>

                        <li class="sorting_i hidden js-category-pagination-paging"><a class="sorting_lk sorting_lk-page jsPaginationEnable" href="#">123</a></li>

                        <li class="sorting_i js-category-pagination-infinity"><a class="sorting_lk sorting_lk-page jsInfinityEnable" href="#">∞</a></li>
                    </ul>
                </div>

                <div class="section">
                    <div class="goods goods_grid goods_listing grid-4col">
                        <?= $page->render('category/list/pager', ['productPager'=> $productPager]) ?>
                    </div>
                </div>

                <div class="sorting sorting_bottom js-category-sortingAndPagination">
                    <ul class="sorting_lst fl-r js-category-pagination">
                        <li class="sorting_i sorting_i-tl">Страницы</li>

                        <li class="sorting_i act js-category-pagination-activePage js-category-pagination-page">
                            <a class="sorting_lk sorting_lk-page jsPagination" href="#">1</a>
                        </li>

                        <li class="sorting_i hidden js-category-pagination-paging"><a class="sorting_lk sorting_lk-page jsPaginationEnable" href="#">123</a></li>

                        <li class="sorting_i js-category-pagination-infinity"><a class="sorting_lk sorting_lk-page jsInfinityEnable" href="#">∞</a></li>
                    </ul>
                </div>

                <?= $page->blockViewed() ?>

                <!-- SEO информация -->
                <div class="section section_bordered section_seo">
                    <p>Тут какой-то SEO-текст</p>
                </div>
                <!--/ SEO информация -->
            </div>
        </main>
    </div>

    <aside class="left-bar">
        <?= $page->blockNavigation() ?>
    </aside>
</div>
